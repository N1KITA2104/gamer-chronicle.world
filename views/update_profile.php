<?php
global $db;
session_start();
require_once ("../vendor/autoload.php");
require_once ('../config/db_config.php');

$user_id = $_SESSION['user_id'];

$config = HTMLPurifier_Config::createDefault();
$config->set('HTML.ForbiddenElements', ['script', 'iframe', 'object']);
$purifier = new HTMLPurifier($config);

$clean_NickName = $purifier->purify($_POST['nick_name']);
$clean_Description = $purifier->purify($_POST['description']);
$clean_Sex = $purifier->purify($_POST['sex']);
$clean_GeoPosition = $purifier->purify($_POST['geo_position']);

$newNickName = mysqli_real_escape_string($db, $clean_NickName);
$newDescription = mysqli_real_escape_string($db, $clean_Description);
$newSex = mysqli_real_escape_string($db, $clean_Sex);
$newGeoPosition = mysqli_real_escape_string($db, $clean_GeoPosition);

$updateQuery = "UPDATE users 
                SET nick_name='$newNickName', 
                    description='$newDescription', 
                    sex='$newSex', 
                    geo_position='$newGeoPosition' 
                WHERE user_id=$user_id";

if (mysqli_query($db, $updateQuery)) {
    echo json_encode(array('status' => 'success', 'message' => 'Профіль успішно оновлено.'));
    $_SESSION["user_nick"] = $newNickName;
} else {
    echo json_encode(array('status' => 'error', 'message' => 'Помилка при оновленні профілю: ' . mysqli_error($db)));
}
