<main class="container bg-light p-5">
    <div class="stars-in-space"></div>
    <div class="form-page">
        <h1>Редагування посту</h1>
        <form class="create-post" method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="post_photo" class="form-label">Фото:</label>
                <input type="file" class="form-control" name="post_photo" id="post_photo" accept="image/*">
                <div class="invalid-feedback">Unsupported image format or file size exceeds 2MB. Supported formats: JPG, JPEG, PNG, GIF.</div>
            </div>
            <div class="mb-3">
                <label for="post_title" class="form-label">Заголовок:</label>
                <input type="text" class="form-control" id="post_title" name="post_title" placeholder="Введіть заголовок" value="<?= htmlspecialchars($post['post_title']) ?>">
            </div>
            <div class="mb-3">
                <label for="post_description" class="form-label">Опис:</label>
                <textarea class="form-control" id="post_description" name="post_description" placeholder="Введіть опис для посту" rows="4"><?= htmlspecialchars($post['post_description']) ?></textarea>
            </div>
            <div class="mb-3">
                <label for="post_article" class="form-label">Стаття:</label>
                <textarea class="form-control" id="post_article" name="post_article" placeholder="Напишіть статтю для посту" rows="16"><?= $post['post_article'] ?></textarea>
            </div>
            <div class="form-group">
                <label for="category_id">Категорія посту</label>
                <select class="form-control" id="category_id" name="category_id">
                    <?php
                    global$db;
                    $category_query = "SELECT * FROM category";
                    $category_result = mysqli_query($db, $category_query);

                    if (mysqli_num_rows($category_result) > 0) {
                        while ($category_row = mysqli_fetch_assoc($category_result)) {
                            $selected = ($category_row['category_id'] == $post['category_id']) ? "selected" : "";
                            echo '<option value="' . $category_row['category_id'] . '" ' . $selected . '>' . $category_row['category_name'] . '</option>';
                        }
                    }
                    ?>
                </select>
            </div>
            <div class="form-check mb-3">
                <input class="form-check-input" type="checkbox" name="visible" id="visible" <?= $post['visible'] == 1 ? 'checked' : '' ?>>
                <label class="form-check-label text-danger" for="visible">Видимість на сайті!</label>
            </div>
            <button type="submit" class="btn btn-danger">Зберегти</button>
        </form>
    </div>
</main>
