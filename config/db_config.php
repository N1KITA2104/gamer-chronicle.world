<?php

global $db_host, $db_user, $db_password, $db_name;
$db_host = 'localhost';
$db_user = 'root';
$db_pass = '';
$db_name = 'gamer_chron';

$db = mysqli_connect($db_host, $db_user, $db_pass, $db_name);

if (!$db) {
    die("Error connecting to the database: " . mysqli_connect_error());
}

# Конфігураційний файл