<?php
global $db;
session_start();
include '../config/db_config.php';
include '../vendor/autoload.php';
use Carbon\Carbon;

$page = $_GET['page'] ?? 1;
$postsPerPage = 16;
$offset = ($page - 1) * $postsPerPage;

$query = '';
if (!empty($_SESSION)) {
    if ($_SESSION['user_access_type'] == 1) {
        $query = "SELECT posts.*, category.category_name 
                  FROM posts 
                  LEFT JOIN category ON posts.category_id = category.category_id 
                  ORDER BY posts.date DESC";
    } else {
        $query = "SELECT posts.*, category.category_name 
                  FROM posts 
                  LEFT JOIN category ON posts.category_id = category.category_id 
                  WHERE posts.visible = 1 
                  ORDER BY posts.date DESC";
    }
} else {
    $query = "SELECT posts.*, category.category_name 
              FROM posts 
              LEFT JOIN category ON posts.category_id = category.category_id 
              WHERE posts.visible = 1 
              ORDER BY posts.date DESC";
}

$result = mysqli_query($db, $query);
$totalCount = mysqli_num_rows($result);

$totalPages = ceil($totalCount / $postsPerPage);

$query .= " LIMIT ?, ?";
$stmt = mysqli_prepare($db, $query);
mysqli_stmt_bind_param($stmt, 'ii', $offset, $postsPerPage);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$posts = [];
if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $query_author_name = "SELECT nick_name, img FROM users WHERE user_id =" . $row["author_id"];
        $res = mysqli_query($db, $query_author_name);
        $row_author = mysqli_fetch_assoc($res);

        $author_name = $row_author['nick_name'];
        $img = $row_author['img'];

        Carbon::setLocale('uk');
        $date = Carbon::createFromTimestamp(strtotime($row['date']))->setTimezone('Europe/Kiev');
        $formattedDate = $date->diffForHumans();

        if ($date->isToday()) {
            $formattedDate = 'сьогодні ' . $date->format('H:i');
        } elseif ($date->isYesterday()) {
            $formattedDate = 'вчора ' . $date->format('H:i');
        } else {
            $formattedDate = $date->isoFormat('D MMMM Y H:mm');
        }

        // Отримуємо кількість коментарів для даного поста
        $query_comments_count = "SELECT COUNT(*) AS comment_count FROM comments WHERE post_id = " . $row['post_id'];
        $result_comments_count = mysqli_query($db, $query_comments_count);
        $row_comments_count = mysqli_fetch_assoc($result_comments_count);
        $comment_count = $row_comments_count['comment_count'];

        // Отримуємо рейтинг поста
        $query_rating = "SELECT IFNULL(SUM(post_rating_value), 0) AS total_rating FROM post_rating WHERE post_id = " . $row['post_id'];
        $result_rating = mysqli_query($db, $query_rating);
        $row_rating = mysqli_fetch_assoc($result_rating);
        $total_rating = $row_rating['total_rating'];

        $posts[] = [
            'post_id' => $row['post_id'],
            'post_title' => strip_tags($row['post_title']),
            'post_photo' => $row['post_photo'],
            'visible' => $row['visible'] ?? null,
            'category_name' => $row['category_name'],
            'author_name' => $author_name,
            'author_id' => $row['author_id'],
            'img' => $img,
            'formattedDate' => $formattedDate,
            'comment_count' => $comment_count,
            'total_rating' => $total_rating !== null ? $total_rating : 0
        ];
    }
}

echo json_encode(['posts' => $posts, 'totalPages' => $totalPages]);
mysqli_close($db);