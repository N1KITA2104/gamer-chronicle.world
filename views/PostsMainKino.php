<?php
global $db;
use Carbon\Carbon;

include 'config/db_config.php';

$query = "SELECT posts.*, IFNULL(SUM(post_rating_value), 0) AS total_rating, category.category_name 
          FROM posts 
          LEFT JOIN post_rating ON posts.post_id = post_rating.post_id 
          LEFT JOIN category ON posts.category_id = category.category_id
          WHERE visible = 1 AND category.category_name = 'Кіно'
          GROUP BY date DESC LIMIT 16";
$result = mysqli_query($db, $query);
?>

<div class="news-slider">
    <?php while ($row = mysqli_fetch_assoc($result)): ?>
        <?php
        $authorData = fetchAuthorData($row["author_id"]);
        $authorName = $authorData['nick_name'];
        $authorImg = $authorData['img'];

        $formattedDate = formatDate($row['date']);
        $commentCount = getCommentCount($row['post_id']);
        ?>

        <div class="news-item text-light">
            <div class="news-image-container">
                <a href="index.php?action=ViewPost&id=<?= $row['post_id'] ?>">
                    <img class="news-image" loading="lazy" src="<?= $row['post_photo'] ?>" alt="<?= strip_tags($row['post_title']) ?>">
                </a>
            </div>
            <div class="news-caption">
                <a href="index.php?action=ViewPost&id=<?= $row['post_id'] ?>"><?= strip_tags($row['post_title']) ?></a>
            </div>

            <?php if (!empty($_SESSION) && $_SESSION['user_access_type'] === 1): ?>
                <div class="service-buttons">
                    <button class="edit-button" onclick="window.location.href = 'index.php?action=EditPost&id=<?= $row['post_id'] ?>'">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="edit-button" onclick="window.location.href = 'index.php?action=ConfirmDeletePost&id=<?= $row['post_id'] ?>'">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </div>
            <?php endif; ?>

            <div class="post-info-footer row">
                <div class="col-sm-10 d-flex flex-column justify-content-center">
                    <span class="d-inline-flex"><?= !empty($authorImg) ? '<img class="rounded-circle" height="16px" width="16px" src="uploads/profiles/' . $authorImg . '" alt="Profile Picture"> ' : '<img class="rounded-circle" height="30px" width="30px" src="img/user-ico.png" alt="Profile Picture"> ' ?><a href="index.php?action=profile&id=<?= $row['author_id'] ?>" title="Автор посту"><?= $authorName ?></a></span>
                    <span class="d-inline-flex" title="Дата та час публікації"><img src="img/clock-icon.svg" height="16px" width="16px" alt="Дата: "><?= $formattedDate ?></span>
                </div>

                <div class="col-sm-2 text-right d-flex flex-column justify-content-center">
                    <span class="d-inline-flex"><img src="img/comments.svg" title="Коментарі" height="16px" width="16px" alt="Коментарі: "><?= $commentCount ?></span>
                    <span class="d-inline-flex"><img src="img/rating.svg" title="Рейтинг" height="16px" width="16px" alt="Рейтинг: "><?= $row['total_rating'] !== null ? $row['total_rating'] : 0 ?></span>
                </div>
            </div>
        </div>
    <?php endwhile; ?>
</div>

<?php
mysqli_close($db);

// Функция для извлечения данных о пользователе
function fetchAuthorData($authorId): false|array|null
{
    global $db;
    $query = "SELECT nick_name, img FROM users WHERE user_id = $authorId";
    $result = mysqli_query($db, $query);
    return mysqli_fetch_assoc($result);
}

// Функция для форматирования даты
function formatDate($date): string
{
    Carbon::setLocale('uk');
    $dateObj = Carbon::createFromTimestamp(strtotime($date))->setTimezone('Europe/Kiev');
    if ($dateObj->isToday()) {
        return 'сьогодні ' . $dateObj->format('H:i');
    } elseif ($dateObj->isYesterday()) {
        return 'вчора ' . $dateObj->format('H:i');
    } else {
        return $dateObj->isoFormat('D MMMM Y');
    }
}

// Функция для получения количества комментариев
function getCommentCount($postId) {
    global $db;
    $query = "SELECT COUNT(*) AS comment_count FROM comments WHERE post_id = $postId";
    $result = mysqli_query($db, $query);
    $row = mysqli_fetch_assoc($result);
    return $row['comment_count'];
}
?>

<!-- Slick JavaScript -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.js"></script>

<script>
    $(document).ready(function(){
        function adjustSlider() {
            const windowWidth = $(window).width();
            let slidesToShow;

            if (windowWidth >= 1200) {
                slidesToShow = 4;
            } else if (windowWidth >= 1000) {
                slidesToShow = 3;
            } else if (windowWidth >= 800) {
                slidesToShow = 2;
            } else {
                slidesToShow = 1;
            }

            $('.news-slider').slick('slickSetOption', 'slidesToShow', slidesToShow, true);
        }

        $('.news-slider').slick({
            slidesToShow: 4,
            slidesToScroll: 1,
            autoplay: true,
            autoplaySpeed: 3000,
            dots: false,
            arrows: false,
            responsive: [
                {
                    breakpoint: 1200,
                    settings: {
                        slidesToShow: 3
                    }
                },
                {
                    breakpoint: 1000,
                    settings: {
                        slidesToShow: 2
                    }
                },
                {
                    breakpoint: 800,
                    settings: {
                        slidesToShow: 1
                    }
                }
            ]
        });

        adjustSlider();

        $(window).resize(function() {
            adjustSlider();
        });
    });

</script>
