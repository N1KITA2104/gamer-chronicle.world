<?php
require_once 'config/db_config.php';

use Carbon\Carbon;

global $db;

if (!isset($_GET['id'])) {
    include 'layout/PageNotFound.php';
    exit();
}

$post_id = intval($_GET['id']);

$query = "SELECT p.*, u.nick_name AS author_name, 
    (SELECT IFNULL(SUM(post_rating_value), 0) FROM post_rating WHERE post_id = p.post_id) AS total_rating 
    FROM posts p 
    LEFT JOIN users u ON p.author_id = u.user_id ";

if (empty($_SESSION) || $_SESSION['user_access_type'] !== 1) {
    $query .= "WHERE p.post_id = $post_id AND p.visible = 1";
} else {
    $query .= "WHERE p.post_id = $post_id";
}

$result = mysqli_query($db, $query);

if (!$result || mysqli_num_rows($result) == 0) {
    include 'layout/PageNotFound.php';
    exit();
}

$row = mysqli_fetch_assoc($result);

Carbon::setLocale('uk');
$post_time = Carbon::parse($row['date'])->setTimezone('Europe/Kiev');

if ($post_time->isToday()) {
    $formattedDate = 'сьогодні ' . $post_time->format('H:i');
} elseif ($post_time->isYesterday()) {
    $formattedDate = 'вчора ' . $post_time->format('H:i');
} else {
    $formattedDate = $post_time->isoFormat('D MMMM Y H:mm');
}

?>

<main class="container bg-light p-3 pb-3">
    <article>
        <header class="bg-dark text-light p-3 m-2 me-5"
                style="border-bottom: 10px solid #D96C6C;
                       border-left: 10px solid transparent;">
            <h2 class="display-6"><?php echo $row['post_title']; ?></h2>
        </header>
        <section class="mb-3 mt-3 p-2">
            <div class="text-center p
            me-5">
                <div class="d-flex justify-content-center">
                    <img src="<?php echo $row['post_photo']; ?>" alt="<?php echo $row['post_title']; ?>" class="img-fluid" style="width: 100vw">
                </div>
            </div>
            <section id="post-title" class="bg-dark description text-light p-3 post-details mb-3 me-5">
                <img src="img/author.svg" height="20px" alt="Автор: "><span><a class="footer-link" href="index.php?action=Profile&id=<?php echo $row['author_id']; ?>"><?php echo $row['author_name']; ?></a></span><br>
                <img src="img/clock-icon.svg" height="20px" alt="Дата: "><span> <?php echo $formattedDate ?></span>
            </section>
            <h6 class="mb-3"><?php echo $row['post_description']; ?></h6>
            <hr>
            <div id="post-article post" class="container-fluid"><?php echo $row['post_article']; ?></div>
        </section>
        <article>
        <?php
        include("config/db_config.php");

        if (isset($_SESSION['user_id'])) {
            $user_id = $_SESSION['user_id'];

            $query = "SELECT reaction FROM post_rating WHERE user_id = ? and post_id = ?";
            $statement = $db->prepare($query);
            $statement->bind_param("ii", $user_id, $post_id);
            $statement->execute();
            $result = $statement->get_result();

            if ($result->num_rows > 0) {
                $react = $result->fetch_assoc();
                $reaction = $react['reaction'];
            } else {
                $reaction = null;
            }

            $statement->close();
        }
        ?>
        <section class="mb-3 d-flex flex-column align-items-center section-container p-2 pe-3 mt-3">
            <?php if (!empty($_SESSION) && $_SESSION['user_access_type'] === 1): ?>
                <button style="height:32px;width:32px" class="btn btn-danger mb-2 d-flex align-items-center justify-content-center" title="Редагувати пост" onclick="window.location.href = 'index.php?action=EditPost&id=<?php echo $row['post_id']; ?>';"><i class="fas fa-edit"></i></button>
                <button style="height:32px;width:32px"  class="btn btn-danger mb-2 d-flex align-items-center justify-content-center" id="deleteForm" title="Видалити пост" onclick="window.location.href = 'index.php?action=ConfirmDeletePost&id=<?php echo $row['post_id']; ?>';"><i class="fas fa-trash-alt"></i> </button>
            <?php endif; ?>
            <div class="bg-dark-subtle rounded d-flex flex-column align-items-center">
            <?php if (!empty($_SESSION)) : ?>
                <span title="Вгору" id="upvoteBtn" class="reaction-btn-positive <?php echo ($reaction === 'upvote') ? 'active' : ''; ?>" onclick="vote('upvote')">
                    <img class="positive" src="img/up-chevron.svg" height="20px" alt="Вгору">
                </span>
            <?php endif; ?>
            <span title="Рейтинг посту" class="p-1" id="postRating"><b><?php echo $row['total_rating']; ?></b></span>
            <?php if (!empty($_SESSION)) : ?>
                <span title="Вниз" id="downvoteBtn" class="reaction-btn-negative <?php echo ($reaction === 'downvote') ? 'active' : ''; ?>" onclick="vote('downvote')">
                    <img class="negative" src="img/down-chevron.svg" height="20px" alt="Вниз">
                </span>
            </div>
                <?php if ($reaction !== null) : ?>
                    <button class="btn btn-danger mt-2" id="cancelVoteBtn" title="Відмінити голос" onclick="vote('cancel')"><i class="fas fa-times"></i></button>
                <?php endif; ?>
            <?php endif; ?>
        </section>
    </article>
    </article>
    <?php include("views/CommentsBlock.php"); ?>
</main>

<script>
    function vote(reaction) {
        $.ajax({
            url: 'views/UpdateRating.php',
            type: 'POST',
            data: {
                id: <?php echo $post_id; ?>,
                vote: reaction
            },
            success: function(response) {
                $('#postRating').text(response.rating);
                getRating();
                updateButtons(reaction, 'upvoteBtn', 'downvoteBtn');
            },
            error: function(xhr, status, error) {
                console.error(error);
            }
        });
    }

    function getRating() {
        $.ajax({
            url: 'views/GetRating.php',
            type: 'GET',
            data: {
                id: <?php echo $post_id; ?>
            },
            success: function(response) {
                $('#postRating').text(response.rating);
            },
            error: function(xhr, status, error) {
                console.error(error);
            }
        });
    }

    function updateButtons(reaction, upvoteBtnId, downvoteBtnId) {
        $('.reaction-btn-positive, .reaction-btn-negative').removeClass('active');

        if (reaction === 'upvote') {
            $('#' + upvoteBtnId).addClass('active');
        } else if (reaction === 'downvote') {
            $('#' + downvoteBtnId).addClass('active');
        }
    }

</script>
<?php mysqli_close($db); ?>
