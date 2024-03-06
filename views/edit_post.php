<?php
global $db;
$errors = [];
$post_photo = null;

if (!empty($_SESSION) && $_SESSION['user_access_type'] === 1) {
    include 'config/db_config.php';

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

                        $allowedExtensions = array("jpg", "jpeg", "png");
                        $extension = strtolower(pathinfo($_FILES['post_photo']['name'], PATHINFO_EXTENSION));

                        if (!in_array($extension, $allowedExtensions) || $_FILES['post_photo']['size'] > $maxFileSize) {
                            $errors[] = 'Unsupported image format or file size exceeds 2MB. Supported formats: JPG, JPEG, PNG, GIF.';
                        } elseif (move_uploaded_file($_FILES['post_photo']['tmp_name'], $uploadFile)) {
                            if ($extension === 'png' || $extension === 'jpg' || $extension === 'jpeg') {
                                $sourceFile = $uploadFile;
                                $webpFile = 'uploads/posts/' . pathinfo($uniqueFilename, PATHINFO_FILENAME) . '.webp';

                                try {
                                    $imagick = new Imagick($sourceFile);
                                    $imagick->setImageFormat('webp');
                                    $imagick->setCompressionQuality(50);
                                    $imagick->writeImage($webpFile);
                                    $imagick->clear();
                                    $imagick->destroy();
                                    if (file_exists($webpFile)) {
                                        if (!empty($post['post_photo']) && file_exists($post['post_photo'])) {
                                            unlink($post['post_photo']);
                                        }
                                        unlink($sourceFile);
                                        $post_photo = mysqli_real_escape_string($db, $webpFile);
                                    } else {
                                        $errors[] = 'Помилка конвертації у WebP.';
                                    }
                                } catch (ImagickException $e) {
                                    $errors[] = 'Помилка при обробці зображення: ' . $e->getMessage();
                                }
                            } else {
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
                        echo '<script>window.location.href = "index.php?action=view_post&id=' . $id . '";</script>';
                        exit();
                    } else {
                        $errors[] = 'Error editing post: ' . mysqli_error($db);
                    }
                }
            }
            include 'templates/edit_post_form.php';
        } else {
            include 'templates/page_not_found.php';
        }
    } else {
        include 'templates/page_not_found.php';
    }
} else {
    include 'templates/page_not_found.php';
}
