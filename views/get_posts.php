<?php
global $db;
include('../config/db_config.php');
require_once ('../vendor/autoload.php');
use Carbon\Carbon;

session_start();

if (isset($_GET['id']) && isset($_GET['page'])) {
    $user_id = intval($_GET['id']);
    $page = intval($_GET['page']);

    $postsPerPage = 6;
    $offset = $page * $postsPerPage;

    $query = "SELECT 
                posts.*,
                category.category_name,
                COUNT(comments.comment_id) AS comment_count,
                COALESCE(pr.total_rating, 0) AS total_rating
              FROM 
                posts
              LEFT JOIN 
                category ON posts.category_id = category.category_id
              LEFT JOIN 
                comments ON posts.post_id = comments.post_id
              LEFT JOIN 
                (SELECT post_id, SUM(post_rating_value) AS total_rating FROM post_rating GROUP BY post_id) AS pr 
                ON posts.post_id = pr.post_id
              WHERE 
                posts.author_id = ? AND posts.visible = 1
              GROUP BY 
                posts.post_id
              LIMIT ?, ?";

    $stmt = $db->prepare($query);
    $stmt->bind_param("iii", $user_id, $offset, $postsPerPage);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $formattedDate = Carbon::parse($row['date'])->format('Y-m-d H:i');

            echo '
                    <div class="news-item card mb-3 bg-dark text-light">
                        <div class="row">
                            <div class="col-md-4" style="position: relative;">
                                <a href="index.php?action=view_post&id=' . $row['post_id'] . '">
                                    <img src="' . $row['post_photo'] . '" class="card-img" alt="Post Image">
                                    <div class="card-text m-2" style="position: absolute; top: 10px; right: 10px"> 
                                        <span class="badge" style="background-color: indianred; border-radius: 2px; padding: 4px; font-size: 12px">' . $row['category_name'] . '</span>
                                    </div>
                                </a>
                            </div>
                            <div class="col-md-8">
                                <div class="card-body p-3">
                                <a class="m-2" href="index.php?action=view_post&id=' . $row['post_id'] . '">
                                    <h5 class="card-title footer-link">' . $row['post_title'] . '</h5>
                                    <p class="card-text">' . $row['post_description'] . '</p>
                                </a>
                                    <div class="d-flex flex-column">
                                        <span class="d-flex align-items-center" title="Дата та час публікації">
                                            <img class="me-1" src="img/clock-icon.svg" height="20px" alt="Дата: ">
                                            <span>' . $formattedDate . '</span>
                                        </span>
                                        <div class="d-flex align-items-center">
                                            <img class="me-1" src="img/comments.svg" title="Коментарі" height="16px" alt="Коментарі: ">
                                            <span>' . $row["comment_count"] . '</span>
                                        </div>
                                        <div class="d-flex align-items-center">
                                            <img class="me-1" src="img/rating.svg" title="Рейтинг" height="16px" alt="Рейтинг: ">
                                            <span>' . $row["total_rating"] . '</span>
                                        </div>
                                    </div>';
            if (!empty($_SESSION) && $_SESSION['user_access_type'] === 1) {
                echo '<div class="service-buttons">
                        <button class="edit-button" onclick="window.location.href = \'index.php?action=edit_post&id=' . $row['post_id'] . '\'">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="edit-button" onclick="window.location.href = \'index.php?action=confirm_delete_post&id=' . $row['post_id'] . '\'">
                            <i class="fas fa-trash-alt"></i> 
                        </button>
                    </div>';
            }
            echo '          </div>
                            </div>
                        </div>
                    </div>
                ';
        }
    } else {
        echo '';
    }
    $stmt->close();
} else {
    echo '<div class="alert alert-danger">User ID or page is not provided.</div>';
}

$db->close();