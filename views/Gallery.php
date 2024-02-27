<main class="container bg-light mb-3 p-5">
    <h1 class="bg-dark p-3 display-4 text-light">Галерея GamerChronicle</h1>
    
    <div class="row">
        <?php
        global $db;
        $directory = "";

        require_once("config/db_config.php");

        $category_query = "SELECT DISTINCT category_name FROM category";
        $category_result = $db->query($category_query);

        function displayGalleryByCategory($category_name) {
            global $db, $directory;

            $sql = "SELECT posts.post_photo 
                    FROM posts 
                    INNER JOIN category ON posts.category_id = category.category_id 
                    WHERE category.category_name = '$category_name' 
                    AND posts.visible = 1 
                    ORDER BY posts.date DESC";

            $result = $db->query($sql);

            echo '<div class="col-md-6">';
            echo '<h4 class="bg-dark p-2 text-light mt-3">' . $category_name . '</h4>';
            echo '<div class="bg-light" id="carouselExampleControls_' . $category_name . '" class="carousel slide" data-bs-ride="carousel">';
            echo '<div class="carousel-inner">';

            $active = true;
            if ($result && $result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    $image = $directory . $row["post_photo"];
                    if (file_exists($image)) {
                        echo '<div class="carousel-item' . ($active ? ' active' : '') . '">';
                        echo '<img class="d-block carousel-image img-fluid" src="' . $image . '" alt="Gallery Image">';
                        echo '</div>';
                        $active = false;
                    }
                }
            } else {
                echo '<div class="m-3 alert alert-warning" role="alert">Немає доступних зображень</div>';
            }
            echo '</div>';
            echo '<button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleControls_' . $category_name . '"  data-bs-slide="prev">';
            echo '<span class="carousel-control-prev-icon" aria-hidden="true"></span>';
            echo '<span class="visually-hidden">Попередній</span>';
            echo '</button>';
            echo '<button class="carousel-control-next" type="button" data-bs-target="#carouselExampleControls_' . $category_name . '"  data-bs-slide="next">';
            echo '<span class="carousel-control-next-icon" aria-hidden="true"></span>';
            echo '<span class="visually-hidden">Наступний</span>';
            echo '</button>';
            echo '</div>';
            echo '</div>';
        }

        if ($category_result && $category_result->num_rows > 0) {
            while ($row = $category_result->fetch_assoc()) {
                $category_name = $row['category_name'];
                displayGalleryByCategory($category_name);
            }
        }

        $db->close();
        ?>
    </div>
</main>

<style>
    .carousel-item {
        height: 250px;
    }
    .carousel-item {
        max-width: 100%;
        height: auto;
        margin: 0 auto; 
    }
</style>
