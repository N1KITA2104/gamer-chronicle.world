<main class="container bg-light p-5">
    <article>
        <header>
            <h1 class="display-4">Всі новини GamerChronicle</h1>
        </header>
        <hr>
        <section class="all-news-container mb-5">
            <?php
            global $db;
            use Carbon\Carbon;

            include 'config/db_config.php';
            $page = $_GET['page'] ?? 1;
            $postsPerPage = 16;
            $offset = ($page - 1) * $postsPerPage;

            $query = '';
            if (!empty($_SESSION)) {
                if ($_SESSION['user_access_type'] == 1) {
                    $query = "SELECT * FROM posts ORDER BY date DESC";
                } else {
                    $query = "SELECT * FROM posts WHERE visible = 1 ORDER BY date DESC";
                }
            } else {
                $query = "SELECT * FROM posts WHERE visible = 1 ORDER BY date DESC";
            }

            $result = mysqli_query($db, $query);
            $totalCount = mysqli_num_rows($result);

            $totalPages = ceil($totalCount / $postsPerPage);

            $query .= " LIMIT ?, ?";

            $stmt = mysqli_prepare($db, $query);
            mysqli_stmt_bind_param($stmt, 'ii', $offset, $postsPerPage);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    $query_author_name = "SELECT nick_name, img FROM users WHERE user_id =" . $row["author_id"];
                    $res = mysqli_query($db, $query_author_name);
                    $row_author = mysqli_fetch_assoc($res);

                    $author_name = $row_author['nick_name'];
                    $img = $row_author['img'];

                    echo '<div class="news-item text-light">
            <div class="news-image-container">
            <a href="index.php?action=ViewPost&id=' . $row['post_id'] . '"><img class="news-image" src="' . $row['post_photo'] . '" alt="' . strip_tags($row['post_title']) . '"></a>
            </div>';
                    if (!empty($_SESSION) && $_SESSION['user_access_type'] === 1) {
                        if ($row['visible'] == 0)
                            echo '
                <div style="position: absolute; top: 10px; right: 10px"> 
                    <span class="badge" style="background-color: indianred; border-radius: 6px; padding: 5px">Не опубліковано</span>
                </div>
                ';
                    }
                    echo' <div class="news-caption"><a href="index.php?action=ViewPost&id=' . $row['post_id'] . '">' . strip_tags($row['post_title']) . '</a></div>';
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

                    echo '
                    <div class="post-info-footer row">
                        <div class="col-sm-10">' . (!empty($img) ? '<img class="rounded-circle" height="18px" width="18px" src="uploads/profiles/' . $img . '" alt="Profile Picture">' : '
                            <img class="rounded-circle" height="30px" width="30px" src="img/user-ico.png" alt="Profile Picture">') . '
                                <span><a href="index.php?action=Profile&id=' . $row['author_id'] . '" title="Автор посту">' . $author_name . '</a></span><br>
                            <img src="img/clock-icon.svg" height="20px" alt="Дата: "><span title="Дата та час публікації">' . $formattedDate . '</span>
                        </div>
                        <div class="col-sm-2 text-right">
                            <img src="img/comments.svg" title="Коментарі" height="16px" alt="Коментарі: "><span>' . $comment_count . '</span><br>
                            <img src="img/rating.svg" title="Рейтинг" height="16px" alt="Рейтинг: "><span>' . ($total_rating !== null ? $total_rating : 0) . '</span>
                        </div>
                    </div>
                    </div>';

                }
            } else {
                echo "No more posts to show.";
            }
            echo '</section>
    </article>';
            mysqli_close($db);

            $nextPage = $page + 1;
            $prevPage = $page - 1;
            $visiblePages = 5;
            $halfVisible = ceil($visiblePages / 2);

            echo '<div class="load-buttons">';

            if ($page == 1) {
                echo '<span class="current-page"><i class="fa fa-arrow-left" aria-hidden="true"></i></span>';
            } else {
                echo '<a class="load-more-news" href="index.php?action=News&page=1"><i class="fa fa-arrow-left" aria-hidden="true"></i></a>';
            }

            if ($prevPage > 0) {
                echo '<a class="load-more-news" href="index.php?action=News&page=' . $prevPage . '"><i class="fa fa-chevron-left" aria-hidden="true"></i></a>';
            } else {
                echo '<span class="current-page"><i class="fa fa-chevron-left" aria-hidden="true"></i></span>';
            }

            if ($totalPages > 1) {
                echo "<div class='load-buttons'>";
                $startPage = max(1, $page - $halfVisible + 1);
                $endPage = min($totalPages, $startPage + $visiblePages - 1);

                for ($i = $startPage; $i <= $endPage; $i++) {
                    $class = ($i == $page) ? 'current-page' : 'load-more-news';
                    echo '<a class="' . $class . '" href="index.php?action=News&page=' . $i . '">' . $i . '</a>';
                }

                echo "</div>";
            }

            if ($nextPage <= $totalPages) {
                echo '<a class="load-more-news" href="index.php?action=News&page=' . $nextPage . '"><i class="fa fa-chevron-right" aria-hidden="true"></i></a>';
            } else {
                echo '<span class="current-page"><i class="fa fa-chevron-right" aria-hidden="true"></i></span>';
            }

            if ($page == $totalPages) {
                echo '<span class="current-page"><i class="fa fa-arrow-right" aria-hidden="true"></i></span>';
            } else {
                echo '<a class="load-more-news" href="index.php?action=News&page=' . $totalPages . '"><i class="fa fa-arrow-right" aria-hidden="true"></i></a>';
            }
            ?>
        </section>
</main>
