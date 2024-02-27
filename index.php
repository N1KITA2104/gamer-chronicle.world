<?php
session_start();

require_once("vendor/autoload.php");
require_once("layout/Header.php");
require_once("layout/NavigationBar.php");

$action = $_GET['action'] ?? 'Main';
$viewFile = "views/" . htmlspecialchars($action) . '.php';

if (file_exists($viewFile)) {
    require_once($viewFile);
} else {
    require_once('views/Main.php');
}

require_once("layout/StarsAnimation.php");
require_once("layout/Cookie.php");
require_once("layout/Footer.php");