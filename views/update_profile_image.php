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
        $newImgName = uniqid() . '.' . $extension;
        $uploadPath = $uploadDir . $newImgName;

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

        if (move_uploaded_file($newImg['tmp_name'], $uploadPath)) {
            $updateQuery = "UPDATE users SET img='$newImgName' WHERE user_id=$user_id";
            if (mysqli_query($db, $updateQuery)) {
                echo json_encode(array('status' => 'success', 'message' => 'Фото успішно оновлено.'));
            } else {
                echo json_encode(array('status' => 'error', 'message' => 'Помилка при оновленні фото ' . mysqli_error($db)));
            }
        } else {
            echo json_encode(array('status' => 'error', 'message' => 'Помилка при завантаженні файлу'));
        }
    } else {
        echo json_encode(array('status' => 'error', 'message' => 'Формат зображення, що не підтримується, або розмір файлу перевищує 2 МБ. Формати, що підтримуються: JPG, JPEG, PNG, GIF.'));
    }
}
?>
