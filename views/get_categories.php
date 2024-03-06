<?php
// get_categories.php
global $db;
include '../config/db_config.php';

$query = "SELECT * FROM category";
$result = mysqli_query($db, $query);

$categories = [];
if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $categories[] = [
            'category_id' => $row['category_id'],
            'category_name' => $row['category_name']
        ];
    }
}

echo json_encode(['categories' => $categories]);
mysqli_close($db);
