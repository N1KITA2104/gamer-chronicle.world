<?php

global $db;

use Carbon\Carbon;

include("config/db_config.php");
$user_id = intval($_GET['id']);

$sql_posts = "SELECT COUNT(*) AS post_count FROM posts WHERE author_id = $user_id AND visible = 1";
$result_posts = $db->query($sql_posts);
$post_count = $result_posts->fetch_assoc()['post_count'];

$sql_comments = "SELECT COUNT(*) AS comment_count FROM comments WHERE user_id = $user_id";
$result_comments = $db->query($sql_comments);
$comment_count = $result_comments->fetch_assoc()['comment_count'];

$sql_reactions = "SELECT COUNT(*) AS reaction_count FROM post_rating WHERE user_id = $user_id AND reaction != 'none'";
$result_reactions = $db->query($sql_reactions);
$reaction_count = $result_reactions->fetch_assoc()['reaction_count'];

$sql_recent_activities = "
    (SELECT 'post' AS type, post_id, date AS activity_date, post_title AS activity_title, NULL AS comment, NULL AS reaction
    FROM posts 
    WHERE author_id = $user_id AND visible = 1
    ORDER BY date DESC
    LIMIT 16)
    
    UNION ALL
    
    (SELECT 'comment' AS type, post_id, posted_date AS activity_date, NULL AS activity_title, comment, NULL AS reaction
    FROM comments 
    WHERE user_id = $user_id
    ORDER BY posted_date DESC
    LIMIT 16)
    
    UNION ALL
    
    (SELECT 'reaction' AS type, post_id, date AS activity_date, NULL AS activity_title, NULL AS comment, reaction
    FROM post_rating 
    WHERE user_id = $user_id AND reaction != 'none'
    ORDER BY date DESC
    LIMIT 16)
";

$result_recent_activities = $db->query($sql_recent_activities);

?>

<div class="container">
    <div class="row">
        <div class="col-md-6 p-4 bg-light border border-dark-subtle">
            <h3 class="text-body-secondary">Сумарна активність</h3>
            <ul class="list-group">
                <li class="list-group-item">Кількість постів: <?php echo $post_count; ?></li>
                <li class="list-group-item">Кількість коментарів: <?php echo $comment_count; ?></li>
                <li class="list-group-item">Кількість реакцій на пости: <?php echo $reaction_count; ?></li>
            </ul>
        </div>

        <div class="col-md-6 p-4 bg-light border border-dark-subtle">
            <h3>Остання активність</h3>
            <ul class="list-group">
                <?php
                $all_activities = [];
                while ($activity = $result_recent_activities->fetch_assoc()) {
                    $all_activities[] = $activity;
                }

                usort($all_activities, function ($a, $b) {
                    return strtotime($b['activity_date']) - strtotime($a['activity_date']);
                });

                foreach ($all_activities as $activity) : ?>
                    <li class="list-group-item">
                        <?php
                        $date = Carbon::now();
                        $formatted_date = $date->format('d.m.Y H:i');

                        switch ($activity['type']) {
                            case 'post':
                                echo '<b>' . $formatted_date . '</b> | <a class="profile-last-activity" href="index.php?action=view_post&id=' . $activity['post_id'] . '">Новий пост: ' . $activity['activity_title'] . '</a>';
                                break;
                            case 'comment':
                                echo '<b>' . $formatted_date . '</b> | <a class="profile-last-activity" href="index.php?action=view_post&id=' . $activity['post_id'] . '">Новий коментар: ' . $activity['comment'] . '</a>';
                                break;
                            case 'reaction':
                                $reaction_value = ($activity['reaction'] == 'upvote') ? '1' : '-1';
                                $reaction_class = ($reaction_value == '1') ? 'text-success' : 'text-danger';
                                echo '<b>' . $formatted_date . '</b> | <a class="profile-last-activity ' . $reaction_class . '" href="index.php?action=view_post&id=' . $activity['post_id'] . '">Реакція на пост: ' . $reaction_value . '</a>';
                                break;
                        }
                        ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
</div>
