<?php
global $db;
session_start();

require_once("../vendor/autoload.php");
use Carbon\Carbon;

include ("../config/db_config.php");

$post_id = intval($_GET['post_id']);

$comments_query = "SELECT c.*, u.nick_name AS author_name, u.img AS author_img 
                    FROM comments c 
                    LEFT JOIN users u ON c.user_id = u.user_id 
                    WHERE c.post_id = $post_id";
$comments_result = mysqli_query($db, $comments_query);

$html_code = '';
if (!$comments_result || mysqli_num_rows($comments_result) == 0) {
    $html_code .= '<p class="bg-dark p-3 text-light rounded">Поки що нема коментарів.</p>';
} else {
    while ($comment_row = mysqli_fetch_assoc($comments_result)) {
        Carbon::setLocale('uk');
        $comment_time = Carbon::parse($comment_row['posted_date'])->setTimezone('Europe/Kiev');
        $time_ago = $comment_time->diffForHumans();

        $config = HTMLPurifier_Config::createDefault();
        $purifier = new HTMLPurifier($config);
        $comment = $purifier->purify($comment_row["comment"]);

        $html_code .= '<div class="card mb-2">';
        $html_code .= '<div class="card-body">';
        $html_code .= '<div class="row mb-3 align-items-center">';
        $html_code .= '<div class="col-auto">';
        $html_code .= '<a href="index.php?action=profile&id=' . $comment_row['user_id'] . '">';
        $html_code .= '<img src="uploads/profiles/' . $comment_row['author_img'] . '" alt="' . $comment_row['author_name'] . '" height="40px" width="40px" style="object-fit: cover;" class="rounded-circle nav-img">';
        $html_code .= '</a>';
        $html_code .= '</div>';
        $html_code .= '<div class="col">';
        $html_code .= '<a href="index.php?action=profile&id=' . $comment_row['user_id'] . '" class="text-dark">';
        $html_code .= '<h6>' . $comment_row['author_name'] . '</h6>';
        $html_code .= '</a>';
        $html_code .= '</div>';
        $html_code .= '</div>';
        $html_code .= '<p class="card-text text-muted">' . $comment . '</p>';
        $html_code .= '<small><p class="card-text pb-2 text-secondary">Опубліковано: ' . $time_ago . '</p></small>';
        if (!empty($_SESSION)) {
            if ($_SESSION['user_access_type'] == 1) {
                $html_code .= '<button class="btn btn-danger btn-sm" onclick="deleteComment(' . $comment_row['comment_id'] . ');">Видалити</button>';
            }
        }
        $html_code .= '</div>';
        $html_code .= '</div>';
    }
}

echo $html_code;

mysqli_close($db);
