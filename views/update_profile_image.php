<?php
session_start();
include '../config/db_config.php';

$user_id = $_SESSION['user_id'];

if (isset($_FILES['profile_image'])) {
    $uploadDir = dirname(__FILE__) . '/../uploads/profiles/'; // Используем абсолютный путь
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true); // Создаем директорию, если ее нет
    }
    $newImg = $_FILES['profile_image'];

    $allowedExtensions = array("jpg", "jpeg", "png", "gif");
    $extension = strtolower(pathinfo($newImg['name'], PATHINFO_EXTENSION));
    $maxFileSize = 2 * 1024 * 1024; // 2 МБ в байтах

    if (in_array($extension, $allowedExtensions) && $newImg['size'] <= $maxFileSize) {
        // Преобразование изображения в WebP
        $image = imagecreatefromstring(file_get_contents($newImg['tmp_name']));
        $newImgName = uniqid() . '.webp';
        $uploadPath = $uploadDir . $newImgName;
        imagewebp($image, $uploadPath);

        // Очистка памяти от исходного изображения
        imagedestroy($image);

        // Обновление записи в базе данных
        $query = "SELECT img FROM users WHERE user_id =" . $user_id;
        $result = mysqli_query($db, $query);

        if ($result) {
            $row = mysqli_fetch_assoc($result);
            $oldImgName = $row['img'];

            if ($oldImgName) {
                $oldImgPath = $uploadDir . $oldImgName;
                if (file_exists($oldImgPath)) {
                    unlink($oldImgPath);
                }
            }
        }

        $updateQuery = "UPDATE users SET img='$newImgName' WHERE user_id=$user_id";
        if (mysqli_query($db, $updateQuery)) {
            echo json_encode(array('status' => 'success', 'message' => 'Фото успешно обновлено.'));
        } else {
            echo json_encode(array('status' => 'error', 'message' => 'Ошибка при обновлении фото ' . mysqli_error($db)));
        }
    } else {
        echo json_encode(array('status' => 'error', 'message' => 'Формат изображения не поддерживается или размер файла превышает 2 МБ. Поддерживаемые форматы: JPG, JPEG, PNG, GIF.'));
    }
}
?>
