<main class="container bg-light mb-3 p-5">
    <h1 class="bg-dark p-3 display-4 text-light">Галерея GamerChronicle</h1>
    <div id="carouselExampleControls" class="carousel slide" data-bs-ride="carousel">
        <div class="carousel-inner">
            <?php
            global $db;
            $directory = "";

            require_once("config/db_config.php");

            $sql = "SELECT post_photo FROM posts WHERE visible = 1";
            $result = $db->query($sql);

            $active = true;
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    $image = $directory . $row["post_photo"];
                    echo '<div class="carousel-item' . ($active ? ' active' : '') . '">';
                    echo '<img class="d-block w-100 carousel-image" src="' . $image . '" alt="Gallery Image">';
                    echo '</div>';
                    $active = false;
                }
            } else {
                echo "0 results";
            }
            $db->close();
            ?>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleControls"  data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Попередній</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleControls"  data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Наступний</span>
        </button>
    </div>
</main>
