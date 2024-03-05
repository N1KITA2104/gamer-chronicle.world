<main class="container bg-light p-3">
    <article>
        <header>
            <h1 class="bg-dark text-light display-4 p-4" 
                style="border-bottom: 10px solid #D96C6C;
                        border-left: 10px solid transparent">Всі новини GamerChronicle</h1>
        </header>
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

                    ?>
                    <div class="news-item text-light description">
                        <div class="news-image-container description">
                            <a href="index.php?action=ViewPost&id=<?= $row['post_id'] ?>">
                                <img class="news-image" src="<?= $row['post_photo'] ?>" alt="<?= strip_tags($row['post_title']) ?>" width="300" height="200">
                            </a>
                        </div>
                        <?php if (!empty($_SESSION) && $_SESSION['user_access_type'] === 1 && $row['visible'] == 0): ?>
                            <div style="position: absolute; top: 10px; right: 10px"> 
                                <span class="badge" style="background-color: #D96C6C; border-radius: 6px; padding: 5px">Не опубліковано</span>
                            </div>
                        <?php endif; ?>
                        <div class="news-caption">
                            <a href="index.php?action=ViewPost&id=<?= $row['post_id'] ?>"><?= strip_tags($row['post_title']) ?></a>
                        </div>
                        <?php if (!empty($_SESSION) && $_SESSION['user_access_type'] === 1): ?>
                            <div class="service-buttons">
                                <button class="edit-button" title="Редагувати пост" onclick="window.location.href = 'index.php?action=EditPost&id=<?= $row['post_id'] ?>'">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="edit-button" title="Видалити пост" onclick="window.location.href = 'index.php?action=ConfirmDeletePost&id=<?= $row['post_id'] ?>'">
                                    <i class="fas fa-trash-alt"></i> 
                                </button>
                            </div>
                        <?php endif; ?>

                        <?php
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
                        ?>

                        <div class="post-info-footer description row">
                            <div class="col-sm-10">
                                <?= (!empty($img) ? '<img class="rounded-circle" height="18px" width="18px" src="uploads/profiles/' . $img . '" alt="Profile Picture">' : '<img class="rounded-circle" height="30px" width="30px" src="img/user-ico.png" alt="Profile Picture">') ?>
                                <span><a href="index.php?action=Profile&id=<?= $row['author_id'] ?>" title="Автор посту"><?= $author_name ?></a></span><br>
                                <img src="img/clock-icon.svg" width="20px" height="20px" alt="Дата: "><span title="Дата та час публікації"><?= $formattedDate ?></span>
                            </div>
                            <div class="col-sm-2 text-right">
                                <img src="img/comments.svg" width="16px" height="16px" title="Коментарі" alt="Коментарі: "><span><?= $comment_count ?></span><br>
                                <img src="img/rating.svg" width="16px" height="16px" title="Рейтинг" alt="Рейтинг: "><span><?= ($total_rating !== null ? $total_rating : 0) ?></span>
                            </div>
                        </div>
                    </div>
                    <?php
                }
            } else {
                echo "<div class='alert alert-danger'>No more posts to show.</div>";
            }
            mysqli_close($db);

            $nextPage = $page + 1;
            $prevPage = $page - 1;
            $visiblePages = 5;
            $halfVisible = ceil($visiblePages / 2);

            ?>
        </section>
        <nav aria-label="Page navigation">
            <ul class="pagination pagination-dark justify-content-center">
                <?php if ($prevPage > 0): ?>
                    <li class="page-item">
                        <a class="page-link" href="index.php?action=News&page=<?= $prevPage ?>" aria-label="Previous">
                            <span aria-hidden="true">&laquo;</span>
                        </a>
                    </li>
                <?php else: ?>
                    <li class="page-item disabled">
                        <span class="page-link" aria-hidden="true">&laquo;</span>
                    </li>
                <?php endif; ?>
        
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <li class="page-item <?= ($i == $page) ? 'active btn-danger' : '' ?>">
                        <a class="page-link" href="index.php?action=News&page=<?= $i ?>"><?= $i ?></a>
                    </li>
                <?php endfor; ?>
        
                <?php if ($nextPage <= $totalPages): ?>
                    <li class="page-item">
                        <a class="page-link" href="index.php?action=News&page=<?= $nextPage ?>" aria-label="Next">
                            <span aria-hidden="true">&raquo;</span>
                        </a>
                    </li>
                <?php else: ?>
                    <li class="page-item disabled">
                        <span class="page-link" aria-hidden="true">&raquo;</span>
                    </li>
                <?php endif; ?>
            </ul>
        </nav>
        </article>
    </main>
