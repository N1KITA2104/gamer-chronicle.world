<main class="container d-flex align-items-center">
    <?php
    if(empty($_SESSION)) {
        include "templates/registration_success.php";
    } else {
        include "templates/page_not_found.php";
    }
    ?>
</main>