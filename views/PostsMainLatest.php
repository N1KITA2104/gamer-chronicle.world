<div class="news-container">
<?php

global $db;

use Carbon\Carbon;

include 'config/db_config.php';

$query = "SELECT posts.*, IFNULL(SUM(post_rating_value), 0) AS total_rating, category.category_name 
          FROM posts 
          LEFT JOIN post_rating ON posts.post_id = post_rating.post_id 
          LEFT JOIN category ON posts.category_id = category.category_id
          WHERE visible = 1 
          GROUP BY date DESC LIMIT 24";
$result = mysqli_query($db, $query);

for ($i = 0; $row = mysqli_fetch_assoc($result); $i++) {
    $query_author_name = "SELECT nick_name, img FROM users WHERE user_id =" . $row["author_id"];
    $res = mysqli_query($db, $query_author_name);
    $row_author = mysqli_fetch_assoc($res);

    $author_name = $row_author['nick_name'];
    $img = $row_author['img'];

    $j = $i + 1;

    // Получение количества комментариев для данного поста
    $query_comments_count = "SELECT COUNT(*) AS comment_count FROM comments WHERE post_id = " . $row['post_id'];
    $result_comments_count = mysqli_query($db, $query_comments_count);
    $row_comments_count = mysqli_fetch_assoc($result_comments_count);
    $comment_count = $row_comments_count['comment_count'];

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

    // Определение srcset и sizes для адаптивных изображений
    $srcset_mobile = $row['post_photo'] . '?w=300';
    $srcset_desktop = $row['post_photo'] . '?w=800';
    $sizes = '(max-width: 600px) 300px, 800px';

    // Вывод HTML для каждого поста с адаптивным изображением
    echo '<div class="news-item text-light description div' . $j . '">
            <div class="news-image-container">
                <a href="index.php?action=ViewPost&id=' . $row['post_id'] . '">
                    <img class="news-image" src="' . $srcset_mobile . '" srcset="' . $srcset_mobile . ' 300w, ' . $srcset_desktop . ' 800w" sizes="' . $sizes . '" alt="' . strip_tags($row['post_title']) . '">
                </a>
            </div>
            <div style="position: absolute; top: 10px; right: 10px"> 
                <span class="badge bg-danger" style="border-radius: 2px; padding: 4px; font-size: 12px">' . $row['category_name'] . '</span>
            </div>
            <div class="news-caption text-light"><a href="index.php?action=ViewPost&id=' . $row['post_id'] . '">' . strip_tags($row['post_title']) . '</a></div>';

    // Если у пользователя есть сессия и его уровень доступа равен 1, добавляем кнопки для редактирования и удаления поста
    if (!empty($_SESSION) && $_SESSION['user_access_type'] === 1) {
        echo '<div class="service-buttons">
                <button class="edit-button" onclick="window.location.href = \'index.php?action=EditPost&id=' . $row['post_id'] . '\'">
                    <i class="fas fa-edit"></i>
                </button>
                <button class="edit-button" onclick="window.location.href = \'index.php?action=ConfirmDeletePost&id=' . $row['post_id'] . '\'">
                    <i class="fas fa-trash-alt"></i> 
                </button>
            </div>';
    }

    // Вывод информации об авторе, дате и количестве комментариев
    echo '<div class="post-info-footer row">
            <div class="col-sm-10">' . (!empty($img) ? '<img class="rounded-circle" height="18px" width="18px" src="uploads/profiles/' . $img . '" alt="Profile Picture">' : '<img class="rounded-circle" height="30px" width="30px" src="img/user-ico.png" alt="Profile Picture">') . '
                    <span><a href="index.php?action=Profile&id=' . $row['author_id'] . '" title="Автор посту">' . $author_name . '</a></span><br>
                <img src="img/clock-icon.svg" height="20px" width="20px" alt="Дата: "><span title="Дата та час публікації">' . $formattedDate . '</span>
            </div>
            <div class="col-sm-2 text-right">
                <img src="img/comments.svg" title="Коментарі" height="16px" width="16px" alt="Коментарі: "><span>' . $comment_count . '</span><br>
                <img src="img/rating.svg" title="Рейтинг" height="16px" width="16px" alt="Рейтинг: "><span>' . ($row['total_rating'] !== null ? $row['total_rating'] : 0) . '</span>
            </div>
        </div>
    </div>';
}

mysqli_close($db);
?>
</div>
