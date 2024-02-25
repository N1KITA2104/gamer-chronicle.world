<?php
global $db;
session_start();
include '../config/db_config.php';

$user_id = $_SESSION['user_id'];

$currentPassword = mysqli_real_escape_string($db, $_POST['current_password']);
$newPassword = mysqli_real_escape_string($db, $_POST['new_password']);
$confirmNewPassword = mysqli_real_escape_string($db, $_POST['confirm_new_password']);

$response = array();

$uppercase = preg_match('/[A-Z]/', $newPassword);
$digit = preg_match('/\d/', $newPassword);

if (!$uppercase || !$digit || strlen($newPassword) < 8) {
    $response['status'] = 'error';
    $response['message'] = 'Пароль повинен містити мінімум 1 велику літеру, 1 цифру і бути довжиною не менше 8 символів.';
} elseif ($newPassword !== $confirmNewPassword) {
    $response['status'] = 'error';
    $response['message'] = 'Новий пароль не співпадає з підтвердженням';
} else {
    $checkPasswordQuery = "SELECT password_hash FROM users WHERE user_id=$user_id";
    $result = mysqli_query($db, $checkPasswordQuery);
    $row = mysqli_fetch_assoc($result);

    if (password_verify($currentPassword, $row['password_hash'])) {
        $hashedNewPassword = password_hash($newPassword, PASSWORD_BCRYPT);
        $updatePasswordQuery = "UPDATE users SET password_hash='$hashedNewPassword' WHERE user_id=$user_id";
        if (mysqli_query($db, $updatePasswordQuery)) {
            $response['status'] = 'success';
            $response['message'] = 'Пароль успішно оновлено.';
        } else {
            $response['status'] = 'error';
            $response['message'] = 'Помилка при оновленні паролю: ' . mysqli_error($db);
        }
    } else {
        $response['status'] = 'error';
        $response['message'] = 'Неправильний поточний пароль';
    }
}

echo json_encode($response);
