<?php
global $db;
$errors = [];
$post_photo = null;

if (!empty($_SESSION) && $_SESSION['user_access_type'] === 1) {
    include 'config/db_config.php';

    if (!$db) {
        die("Connection error to database: " . mysqli_connect_error());
    }

    if (!empty($_GET['id'])) {
        $id = mysqli_real_escape_string($db, $_GET['id']);

        $select_query = "SELECT * FROM posts WHERE post_id = $id";
        $result = mysqli_query($db, $select_query);

        if (mysqli_num_rows($result) > 0) {
            $post = mysqli_fetch_assoc($result);

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $config_article = HTMLPurifier_Config::createDefault();
                $config_else = HTMLPurifier_Config::createDefault();

                $config_article->set('HTML.ForbiddenElements', ['script', 'iframe', 'object']);
                $config_else->set('HTML.Allowed', 'h2');

                $purifier_article = new HTMLPurifier($config_article);
                $purifier_else = new HTMLPurifier($config_else);

                $post_title = mysqli_real_escape_string($db, $purifier_else->purify($_POST['post_title'] ?? ''));
                $post_description = mysqli_real_escape_string($db, $purifier_else->purify($_POST['post_description'] ?? ''));
                $post_article = mysqli_real_escape_string($db, $purifier_article->purify($_POST['post_article'] ?? ''));

                $visible = isset($_POST['visible']) ? 1 : 0;

                $category_id = mysqli_real_escape_string($db, $_POST['category_id'] ?? '');

if (isset($_FILES['post_photo']) && $_FILES['post_photo']['error'] === 0) {
    if (!empty($_FILES['post_photo']['name'])) {
        $uploadDir = 'uploads/posts/';
        $maxFileSize = 2 * 1024 * 1024;
        $uniqueFilename = uniqid() . '_' . $_FILES['post_photo']['name'];
        $uploadFile = $uploadDir . $uniqueFilename;

        $allowedExtensions = array("jpg", "jpeg", "png", "gif");
        $extension = strtolower(pathinfo($_FILES['post_photo']['name'], PATHINFO_EXTENSION));

        if (!in_array($extension, $allowedExtensions) || $_FILES['post_photo']['size'] > $maxFileSize) {
            $errors[] = 'Unsupported image format or file size exceeds 2MB. Supported formats: JPG, JPEG, PNG, GIF.';
        } elseif (move_uploaded_file($_FILES['post_photo']['tmp_name'], $uploadFile)) {
            if ($extension === 'png' || $extension === 'jpg' || $extension === 'jpeg') {
                // Путь к исходному файлу
                $sourceFile = $uploadFile;
                // Путь для сохранения WebP-файла
                $webpFile = 'uploads/posts/' . pathinfo($uniqueFilename, PATHINFO_FILENAME) . '.webp';

                try {
                    // Создаем новый объект Imagick
                    $imagick = new Imagick($sourceFile);

                    // Устанавливаем формат вывода
                    $imagick->setImageFormat('webp');

                    // Устанавливаем качество (необязательно)
                    $imagick->setCompressionQuality(75);

                    // Сохраняем изображение в формате WebP
                    $imagick->writeImage($webpFile);

                    // Очищаем ресурсы
                    $imagick->clear();
                    $imagick->destroy();

                    // Проверяем, существует ли файл WebP
                    if (file_exists($webpFile)) {
                        // Удаляем старое изображение, если оно существует
                        if (!empty($post['post_photo']) && file_exists($post['post_photo'])) {
                            unlink($post['post_photo']);
                        }
                        // Удаляем исходный файл после конвертации
                        unlink($sourceFile);
                        $post_photo = mysqli_real_escape_string($db, $webpFile);
                    } else {
                        $errors[] = 'Ошибка конвертации в WebP.';
                    }
                } catch (ImagickException $e) {
                    $errors[] = 'Ошибка при обработке изображения: ' . $e->getMessage();
                }
            } else {
                // Если формат не PNG, JPG или JPEG, сохраняем путь к файлу напрямую
                $post_photo = mysqli_real_escape_string($db, $uploadFile);
            }
        } else {
            $errors[] = 'Error uploading the file.';
        }
    } else {
        $post_photo = mysqli_real_escape_string($db, $post['post_photo']);
    }
} else {
    $post_photo = mysqli_real_escape_string($db, $post['post_photo']);
}


                if (empty($errors)) {
                    $update_query = "UPDATE posts SET post_photo = '$post_photo', post_title = '$post_title', post_description = '$post_description', post_article = '$post_article', visible = $visible, category_id = '$category_id' WHERE post_id = $id";

                    if (mysqli_query($db, $update_query)) {
                        echo '<script>window.location.href = "index.php?action=ViewPost&id=' . $id . '";</script>';
                        exit();
                    } else {
                        $errors[] = 'Error editing post: ' . mysqli_error($db);
                    }
                }
            }

            include 'views/EditPostForm.php';
        } else {
            include 'layout/PageNotFound.php';
        }
    } else {
        include 'layout/PageNotFound.php';
    }
} else {
    include 'layout/PageNotFound.php';
}