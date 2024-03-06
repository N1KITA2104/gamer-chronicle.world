<?php
if (!empty($_SESSION)) {
    include "templates/create_post_successful.php";
} else {
    include "templates/page_not_found.php";
}