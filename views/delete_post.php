<?php
global $db;
if (!empty($_SESSION)) {
    if ($_SESSION['user_access_type'] === 1) {
        include 'config/db_config.php';

        if (isset($_POST['post_id']) && isset($_POST['delete'])) {
            $postId = $db->real_escape_string($_POST['post_id']);

            $deleteCommentsSql = "DELETE FROM comments WHERE post_id = $postId";
            $db->query($deleteCommentsSql);

            $selectPhotoSql = "SELECT post_photo FROM posts WHERE post_id = $postId";
            $photoResult = $db->query($selectPhotoSql);
            if ($photoResult && $photoResult->num_rows > 0) {
                $photoRow = $photoResult->fetch_assoc();
                $postPhoto = $photoRow['post_photo'];

                if (!empty($postPhoto) && file_exists($postPhoto)) {
                    unlink($postPhoto);
                }
            }

            $deleteSql = "DELETE FROM posts WHERE post_id = $postId";

            if ($db->query($deleteSql) === TRUE) {
                echo "Пост успішно видалено";
                echo '<script>window.location.href = "index.php?action=main";</script>';
                exit();
            } else {
                include 'templates/page_not_found.php';
            }
        } else {
            include 'templates/page_not_found.php';
        }

        $db->close();
    } else {
        include 'templates/page_not_found.php';
    }
} else {
    include 'templates/page_not_found.php';
}
