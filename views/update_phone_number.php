<?php
global $db;
session_start();
include '../config/db_config.php';

$user_id = $_SESSION['user_id'];

$newPhoneNumber = mysqli_real_escape_string($db, $_POST['phone_number']);

$response = array();

if (preg_match('/^\+\d{12}$/', $newPhoneNumber)) {
    $checkPhoneNumberQuery = "SELECT user_id FROM users WHERE phone_number='$newPhoneNumber'";
    $result = mysqli_query($db, $checkPhoneNumberQuery);

    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        if ($row['user_id'] == $user_id) {
            $response['status'] = 'error';
            $response['message'] = 'Занятий номер телефону належить вам.';
        } else {
            $response['status'] = 'error';
            $response['message'] = 'Номер телефону вже занятий.';
        }
    } else {
        $updateQuery = "UPDATE users SET phone_number='$newPhoneNumber' WHERE user_id=$user_id";
        if (mysqli_query($db, $updateQuery)) {
            $response['status'] = 'success';
            $response['message'] = 'Номер телефону успішно оновлено.';
        } else {
            $response['status'] = 'error';
            $response['message'] = 'Помилка при оновленні номера телефону: ' . mysqli_error($db);
        }
    }
} else {
    $response['status'] = 'error';
    $response['message'] = 'Номер телефону повинен починатися з + і мати 12 цифр.';
}

echo json_encode($response);
