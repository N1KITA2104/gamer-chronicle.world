<?php
global $db;
session_start();
include '../config/db_config.php';

$user_id = $_SESSION['user_id'];

$newEmail = mysqli_real_escape_string($db, $_POST['new_email']);
$password = mysqli_real_escape_string($db, $_POST['password']);

$response = array();

if (!filter_var($newEmail, FILTER_VALIDATE_EMAIL)) {
    $response['status'] = 'error';
    $response['message'] = 'Новий email не є дійсною адресою електронної пошти.';
} else {
    $checkEmailQuery = "SELECT user_id FROM users WHERE email = '$newEmail'";
    $result = mysqli_query($db, $checkEmailQuery);
    if (mysqli_num_rows($result) > 0) {
        $response['status'] = 'error';
        $response['message'] = 'Новий email вже використовується іншим користувачем або вами.';
    } else {
        $checkPasswordQuery = "SELECT password_hash FROM users WHERE user_id = $user_id";
        $result = mysqli_query($db, $checkPasswordQuery);
        $row = mysqli_fetch_assoc($result);

        if (password_verify($password, $row['password_hash'])) {
            $updateQuery = "UPDATE users SET email = '$newEmail' WHERE user_id = $user_id";
            if (mysqli_query($db, $updateQuery)) {
                $response['status'] = 'success';
                $response['message'] = 'Email успішно оновлено.';
            } else {
                $response['status'] = 'error';
                $response['message'] = 'Помилка при оновленні Email: ' . mysqli_error($db);
            }
        } else {
            $response['status'] = 'error';
            $response['message'] = 'Неправильний поточний пароль.';
        }
    }
}

echo json_encode($response);
