<?php
session_start();
global $db;

include ("../config/db_config.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!empty($_SESSION['user_id'])) {
        if (isset($_POST['post_id']) && isset($_POST['comment'])) {
            $post_id = $_POST['post_id'];
            $comment = $_POST['comment'];

            $user_id = $_SESSION['user_id'];
            $stmt = $db->prepare("INSERT INTO comments (post_id, user_id, comment) VALUES (?, ?, ?)");
            $stmt->bind_param("iis", $post_id, $user_id, $comment);
            $stmt->execute();
            $stmt->close();

            echo json_encode(array("status" => "success"));
        } else {
            echo json_encode(array("status" => "error", "message" => "Missing required data"));
        }
    } else {
        echo json_encode(array("status" => "error", "message" => "User is not logged in"));
    }
} else {
    echo json_encode(array("status" => "error", "message" => "Invalid request method"));
}

mysqli_close($db);
