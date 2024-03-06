<?php
session_start();

require_once("vendor/autoload.php");
require_once("layout/header.php");
require_once("layout/navigation_bar.php");

$action = $_GET['action'] ?? 'main';
$viewFile = "views/" . htmlspecialchars($action) . '.php';

if (file_exists($viewFile)) {
    require_once($viewFile);
} else {
    require_once('views/main.php');
}

require_once("templates/stars_animation.php");
require_once("layout/cookie.php");
require_once("layout/footer.php");