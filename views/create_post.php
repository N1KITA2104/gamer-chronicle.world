<?php

global $db;
if (empty($_SESSION) || !isset($_SESSION['user_access_type'])) {
    include 'templates/page_not_found.php';
    exit;
}

include 'config/db_config.php';

$errors = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $config_article = HTMLPurifier_Config::createDefault();
    $config_else = HTMLPurifier_Config::createDefault();

    $config_article->set('HTML.ForbiddenElements', ['script', 'iframe', 'object']);
    $config_else->set('HTML.Allowed', 'h2');

    $purifier_article = new HTMLPurifier($config_article);
    $purifier_else = new HTMLPurifier($config_else);

    $post_title = $purifier_else->purify($_POST['post_title'] ?? '');
    $post_description = $purifier_else->purify($_POST['post_description'] ?? '');
    $post_article = $purifier_article->purify($_POST['post_article'] ?? '');

    if (isset($_FILES['post_photo']) && $_FILES['post_photo']['error'] === 0) {
        $post_photo = null;
        $uploadDir = 'uploads/posts/';
        $maxFileSize = 2 * 1024 * 1024;
        $uniqueFilename = uniqid() . '_' . $_FILES['post_photo']['name'];
        $uploadFile = $uploadDir . $uniqueFilename;

        $allowedExtensions = array("jpg", "jpeg", "png", "gif");
        $extension = strtolower(pathinfo($_FILES['post_photo']['name'], PATHINFO_EXTENSION));

        if (!in_array($extension, $allowedExtensions)) {
            $errors[] = 'Непідтримуваний формат зображення або розмір файлу перевищує 2 МБ. Підтримувані формати: JPG, JPEG, PNG, GIF.';
        } elseif ($_FILES['post_photo']['size'] > $maxFileSize) {
            $errors[] = 'Непідтримуваний формат зображення або розмір файлу перевищує 2 МБ. Підтримувані формати: JPG, JPEG, PNG, GIF.';
        } elseif (move_uploaded_file($_FILES['post_photo']['tmp_name'], $uploadFile)) {
            $webpFile = 'uploads/posts/' . pathinfo($uniqueFilename, PATHINFO_FILENAME) . '.webp';
            $image = imagecreatefromstring(file_get_contents($uploadFile));

            if ($extension === 'jpg' || $extension === 'jpeg') {
                imagewebp($image, $webpFile, 50);
            } elseif ($extension === 'png') {
                $pngFile = $uploadFile;
                $webpFile = 'uploads/posts/' . pathinfo($uniqueFilename, PATHINFO_FILENAME) . '.webp';
                $command = "webp -q 50 $pngFile -o $webpFile";
                exec($command);
                
                if (file_exists($webpFile)) {
                    $post_photo = mysqli_real_escape_string($db, $webpFile);
                } else {
                    $errors[] = 'Помилка конвертації PNG в WebP.';
                }
            } elseif ($extension === 'gif') {
                $image = imagecreatefromstring(file_get_contents($uploadFile));
                imagepng($image, $webpFile);
                imagedestroy($image);
            }

            imagedestroy($image);
            unlink($uploadFile);

            $post_photo = mysqli_real_escape_string($db, $webpFile);
        } else {
            $errors[] = 'Помилка завантаження файлу.';
        }
    }

    $author_id = $_SESSION['user_id'];
    $category_id = $_POST['category_id'];
    $visible = isset($_POST['visible']) ? 1 : 0;

    if (empty($errors)) {
        $sql = "INSERT INTO posts (visible, author_id, post_photo, post_title, post_description, post_article, category_id) 
                    VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $db->prepare($sql);
        $stmt->bind_param("iissssi", $visible, $author_id, $post_photo, $post_title, $post_description, $post_article, $category_id);

        if ($stmt->execute()) {
            echo '<script>window.location.href = "index.php?action=create_post_successful";</script>';
            exit;
        }
    }
}
?>

<main class="container bg-light p-5">
    <div>
        <h2 class="display-4">Створення посту</h2>
        <form method="post" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="post_photo" class="form-label"><b>Фотографія:</b></label>
                <input class="form-control" type="file" name="post_photo" id="post_photo" accept="image/*" required>
            </div>
            <div class="mb-3">
                <label for="post_title" class="form-label"><b>Заголовок:</b></label>
                <textarea class="form-control" placeholder="Введіть заголовок посту" name="post_title" id="post_title" required><?php echo isset($_POST['post_title']) ? htmlspecialchars($_POST['post_title']) : ''; ?></textarea>
            </div>
            <div class="mb-3">
                <label for="post_description" class="form-label"><b>Опис:</b></label>
                <textarea class="form-control" placeholder="Введіть опис для посту (під описом)" name="post_description" id="post_description" rows="4"><?php echo isset($_POST['post_description']) ? htmlspecialchars($_POST['post_description']) : ''; ?></textarea>
            </div>
            <div class="mb-3">
                <label for="post_article" class="form-label"><b>Стаття:</b></label>
                <textarea class="form-control" placeholder="Напишіть статтю до посту" name="post_article" id="post_article" rows="16"><?php echo isset($_POST['post_article']) ? htmlspecialchars($_POST['post_article']) : ''; ?></textarea>
            </div>
            <div class="mb-3">
                <label for="category_id" class="form-label"><b>Категорія:</b></label>
                <select class="form-select" name="category_id" id="category_id" required>
                    <?php
                    $query = "SELECT category_id, category_name FROM category";
                    $result = $db->query($query);
                    while ($row = $result->fetch_assoc()) {
                        echo '<option value="' . $row['category_id'] . '">' . $row['category_name'] . '</option>';
                    }
                    ?>
                </select>
            </div>
            <?php if ($_SESSION['user_access_type'] != 0): ?>
                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" name="visible" id="visible">
                    <label class="form-check-label text-dark" for="visible">Видимість на сайті!</label>
                </div>
            <?php endif; ?>

            <?php if (!empty($errors)) echo '<div class="m-3 alert alert-danger">' . implode("<br>", $errors) . '</div>'; ?>
            <button class="btn btn-danger" type="submit">Додати пост</button>
        </form>
    </div>
</main>

<?php
$db->close();
?>
