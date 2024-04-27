<main class="container">
    <?php
    // Включення бібліотеки Carbon та підключення файла автозавантаження Composer
    require '../vendor/autoload.php';
    use Carbon\Carbon;

    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['search'])) {
        $search = $_POST['search'];

        if (!empty($search)) {
            include "../config/db_config.php";

            $sql_posts = "SELECT posts.*, users.nick_name
                          FROM posts
                          LEFT JOIN users ON posts.author_id = users.user_id
                          WHERE post_title LIKE ? OR post_description LIKE ? OR post_article LIKE ?
                          ORDER BY date DESC
                          LIMIT 5";

            $stmt = $db->prepare($sql_posts);

            $param = "%$search%";
            $stmt->bind_param("sss", $param, $param, $param);

            $stmt->execute();

            $result_posts = $stmt->get_result();

            if ($result_posts->num_rows > 0) {
                echo "<div class='list-group mt-3' style='background-color: #fff;'>"; 
                echo "<h2 class='list-group-item list-group-item-action active' style='background-color: #D96C6C; border: none;'>Результати пошуку:</h2>";
                while ($row = $result_posts->fetch_assoc()) {
                    echo "<a href='index.php?action=view_post&id=" . $row["post_id"] . "' class='list-group-item list-group-item-action'>";
                    echo "<h5 class='mb-1'>" . $row["post_title"] . "</h5>";
                    echo "<p class='mb-1'>" . $row["post_description"] . "</p>";
                    // Форматування дати з використанням Carbon
                    $formatted_date = Carbon::parse($row["date"])->format('Y.m.d H:i:s');
                    echo "<small class='text-muted'>" . $formatted_date . "</small><br>";
                    echo "<small title='Автор' class='d-block p-2'><a href='index.php?action=profile&id=" . $row["author_id"] . "'>" . $row["nick_name"] . "</a></small>"; // Добавлені класи Bootstrap для стилізації
                    echo "</a>";
                }
                echo "</div>";
            } else {
                echo "<p class='alert alert-warning mt-3'>По вашому запиту нічого не знайдено.</p>"; // Додамо клас для стилізації Bootstrap
            }


            $stmt->close();
            $db->close();
        }
    }
    ?>
</main>
