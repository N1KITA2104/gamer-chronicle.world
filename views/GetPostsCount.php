<?php
global $db;
session_start();
require_once('../config/db_config.php');

if (isset($_GET['id'])) {
    $user_id = intval($_GET['id']);

    // Query to get total count of user's posts
    $count_query = "SELECT COUNT(*) AS post_count FROM posts WHERE author_id = ? AND visible = 1";

    // Prepare statement for count query
    $count_stmt = $db->prepare($count_query);
    $count_stmt->bind_param("i", $user_id);
    $count_stmt->execute();
    $count_result = $count_stmt->get_result();

    // Fetch and display total count of user's posts
    if ($count_result->num_rows > 0) {
        $count_row = $count_result->fetch_assoc();
        echo '<div class="alert alert-info">Всі пости: (' . $count_row['post_count'] . ')</div>';
    } else {
        echo '<div class="alert alert-danger">Failed to fetch post count.</div>';
    }

    // Close statement
    $count_stmt->close();
    $db->close();
} else {
    echo '<div class="alert alert-danger">User ID is not provided.</div>';
}
