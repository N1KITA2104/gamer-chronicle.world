<?php
global $db;
include '../config/db_config.php';

$post_id = intval($_GET['id']);

$rating_query = "SELECT SUM(post_rating_value) AS total_rating FROM post_rating WHERE post_id = $post_id";
$rating_result = mysqli_query($db, $rating_query);
$rating_row = mysqli_fetch_assoc($rating_result);
$total_rating = $rating_row['total_rating'] ?? 0;

$user_id = $_SESSION['user_id'] ?? 0;

header('Content-Type: application/json');
echo json_encode(['rating' => $total_rating]);

mysqli_close($db);
