<?php

global $db;
if (empty($_SESSION) || !isset($_SESSION['user_access_type']) || $_SESSION['user_access_type'] !== 1) {
    include 'layout/PageNotFound.php';
    exit;
}

include ('config/db_config.php');

if (!isset($_GET['id'])) {
    include 'layout/PageNotFound.php';
    exit;
}

$postId = mysqli_real_escape_string($db, $_GET['id']);
$sql = "SELECT * FROM posts WHERE post_id = $postId";
$result = $db->query($sql);
$db->close();

if (!$result || $result->num_rows === 0) {
    include 'layout/PageNotFound.php';
    exit;
}

echo "
<main class='container bg-light p-5 d-flex align-items-center justify-content-center'>
    <div class='confirm-page'>
        <h1>Ви впевнені, що хочете видалити пост?</h1>
        <form method='post' action='index.php?action=DeletePost'>
                <input type='hidden' name='post_id' value='$postId'>
                <input class='btn btn-danger' type='submit' name='delete' value='Так'>
                <input class='btn btn-secondary' type='button' value='Ні' onclick='history.back()'>
        </form>
    </div>
</main>";
