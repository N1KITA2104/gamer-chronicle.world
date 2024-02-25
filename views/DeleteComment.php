<?php
session_start();
global $db;

include("../config/db_config.php");

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (!empty($_SESSION['user_id'])) {
        if (isset($_GET['post_id']) && isset($_GET['comment_id'])) {
            $post_id = $_GET['post_id'];
            $comment_id = $_GET['comment_id'];

            // Перевірка, чи користувач має права на видалення коментаря (якщо він адміністратор)
            if ($_SESSION['user_access_type'] == 1) {
                $stmt = $db->prepare("DELETE FROM comments WHERE post_id = ? AND comment_id = ?");
                $stmt->bind_param("ii", $post_id, $comment_id);
                $stmt->execute();
                $stmt->close();

                echo json_encode(array("status" => "success"));
            } else {
                echo json_encode(array("status" => "error", "message" => "У вас недостатньо прав для видалення коментаря"));
            }
        } else {
            echo json_encode(array("status" => "error", "message" => "Відсутні обов'язкові дані"));
        }
    } else {
        echo json_encode(array("status" => "error", "message" => "Користувач не увійшов в систему"));
    }
} else {
    echo json_encode(array("status" => "error", "message" => "Недійсний метод запиту"));
}

mysqli_close($db);
?>
