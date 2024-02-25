<?php
global $db;
if (!empty($_SESSION)) {
    if ($_SESSION['user_access_type'] === 1) {
        include 'config/db_config.php';

        if (isset($_POST['post_id']) && isset($_POST['delete'])) {
            $postId = $db->real_escape_string($_POST['post_id']);

            // Удаление комментариев связанных с постом
            $deleteCommentsSql = "DELETE FROM comments WHERE post_id = $postId";
            $db->query($deleteCommentsSql);

            // Получение пути к файлу поста
            $selectPhotoSql = "SELECT post_photo FROM posts WHERE post_id = $postId";
            $photoResult = $db->query($selectPhotoSql);
            if ($photoResult && $photoResult->num_rows > 0) {
                $photoRow = $photoResult->fetch_assoc();
                $postPhoto = $photoRow['post_photo'];

                // Проверка наличия файла и его удаление
                if (!empty($postPhoto) && file_exists($postPhoto)) {
                    unlink($postPhoto);
                }
            }

            // Удаление поста
            $deleteSql = "DELETE FROM posts WHERE post_id = $postId";

            if ($db->query($deleteSql) === TRUE) {
                echo "Пост успішно видалено";
                echo '<script>window.location.href = "index.php?action=Main";</script>';
                exit();
            } else {
                echo '<main>
                        <div class="stars-in-space"></div>
                        <div class="confirm-page">
                            <h1>Помилка при видаленні поста: ' . $db->error . '</h1>
                        </div>
                    </main>';
            }
        } else {
            echo '<main>
                    <div class="stars-in-space"></div>
                    <div class="confirm-page">
                        <h1>Неправильний запит</h1>
                    </div>
                </main>';
        }

        $db->close();
    } else {
        include 'layout/PageNotFound.php';
    }
} else {
    include 'layout/PageNotFound.php';
}
?>
