<?php
global $db;
session_start();
include '../config/db_config.php';

$category = $_GET['category'] ?? '';
$search = $_GET['search'] ?? '';
$date = $_GET['date'] ?? '';

$query = "SELECT posts.*, category.category_name 
          FROM posts 
          LEFT JOIN category ON posts.category_id = category.category_id 
          WHERE posts.visible = 1";

if (!empty($category)) {
    $query .= " AND category.category_id = $category";
}
if (!empty($search)) {
    $query .= " AND posts.post_title LIKE '%$search%'";
}
if (!empty($date)) {
    $query .= " AND DATE(posts.date) = '$date'";
}

$query .= " ORDER BY posts.date DESC";

$result = mysqli_query($db, $query);

$posts = [];
if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        // Query to get author's information
        $query_author_name = "SELECT nick_name, img FROM users WHERE user_id =" . $row["author_id"];
        $res = mysqli_query($db, $query_author_name);
        $row_author = mysqli_fetch_assoc($res);

        $author_name = $row_author['nick_name'];
        $img = $row_author['img'];

        // Query to get comment count for the post
        $query_comments_count = "SELECT COUNT(*) AS comment_count FROM comments WHERE post_id = " . $row['post_id'];
        $result_comments_count = mysqli_query($db, $query_comments_count);
        $row_comments_count = mysqli_fetch_assoc($result_comments_count);
        $comment_count = $row_comments_count['comment_count'];

        // Query to get total rating for the post
        $query_rating = "SELECT IFNULL(SUM(post_rating_value), 0) AS total_rating FROM post_rating WHERE post_id = " . $row['post_id'];
        $result_rating = mysqli_query($db, $query_rating);
        $row_rating = mysqli_fetch_assoc($result_rating);
        $total_rating = $row_rating['total_rating'];

        // Format the date
        $formattedDate = date('Y-m-d H:i', strtotime($row['date']));

        $posts[] = [
            'post_id' => $row['post_id'],
            'post_title' => strip_tags($row['post_title']),
            'post_photo' => $row['post_photo'],
            'visible' => $row['visible'] ?? null,
            'category_name' => $row['category_name'],
            'author_name' => $author_name,
            'img' => $img,
            'formattedDate' => $formattedDate, // Include the formatted date
            'comment_count' => $comment_count,
            'total_rating' => $total_rating
        ];
    }
}

echo json_encode(['posts' => $posts]);
mysqli_close($db);
