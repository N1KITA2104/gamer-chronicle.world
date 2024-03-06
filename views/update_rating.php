<?php
global $db;
session_start();
include '../config/db_config.php';

if (isset($_POST['id']) && isset($_POST['vote'])) {
    $post_id = intval($_POST['id']);
    $vote = $_POST['vote'];

    $user_id = $_SESSION['user_id'] ?? 0;

    if ($vote === 'cancel') {
        $delete_vote_query = "DELETE FROM post_rating WHERE user_id = $user_id AND post_id = $post_id";
        mysqli_query($db, $delete_vote_query);
    } else {
        $value = ($vote === 'upvote') ? 1 : (($vote === 'downvote') ? -1 : 0);

        $delete_previous_vote_query = "DELETE FROM post_rating WHERE user_id = $user_id AND post_id = $post_id";
        mysqli_query($db, $delete_previous_vote_query);

        $insert_vote_query = "INSERT INTO post_rating (user_id, post_id, post_rating_value, reaction) VALUES ($user_id, $post_id, $value, '$vote')";
        mysqli_query($db, $insert_vote_query);
    }

    $rating_query = "SELECT SUM(post_rating_value) AS total_rating FROM post_rating WHERE post_id = $post_id";
    $rating_result = mysqli_query($db, $rating_query);
    $rating_row = mysqli_fetch_assoc($rating_result);
    $total_rating = $rating_row['total_rating'] ?? 0;

    echo json_encode(['rating' => $total_rating]);
}

mysqli_close($db);
