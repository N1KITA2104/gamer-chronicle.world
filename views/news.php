<?php
// Отримуємо значення параметра "page" з URL-адреси
$page = $_GET['page'] ?? 1;
?>

<main class="container bg-light p-3">
    <header>
        <h1 class="bg-dark text-light display-4 p-4"
            style="border-bottom: 10px solid #D96C6C;
                        border-left: 10px solid transparent">Всі новини GamerChronicle</h1>
    </header>
    <aside class="sidebar m-3 p-3">
        <form id="filter-form">
            <h2>Фільтри</h2>
            <div class="form-group">
                <label for="category">Категорія:</label>
                <select class="form-control" id="category" name="category">
                    <option value="">Виберіть категорію</option>
                </select>
            </div>
            <div class="form-group">
                <label for="search">Пошук за назвою:</label>
                <input type="text" class="form-control" id="search" name="search" placeholder="Введіть щось для пошуку...">
            </div>
            <div class="form-group mb-3">
                <label for="date">Дата:</label>
                <input type="date" class="form-control" id="date" name="date">
            </div>
            <button type="submit" class="btn btn-danger">Застосувати фільтр</button>
            <button type="button" class="btn btn-secondary" onclick="resetFilters()">Скинути фільтри</button>
        </form>
    </aside>
    <article>
        <section class="all-news-container mb-5" id="news-container">
            <!-- News will be loaded here dynamically via AJAX -->
        </section>
        <nav aria-label="Page navigation">
            <ul class="pagination pagination-dark justify-content-center" id="pagination">
                <!-- Pagination links will be loaded here dynamically via AJAX -->
            </ul>
        </nav>
    </article>
</main>

<script>
    $(document).ready(function() {
        loadNews(<?php echo $page; ?>);
        getCategories();

        $('#filter-form').submit(function(event) {
            event.preventDefault();
            let formData = $(this).serialize();
            filterNews(formData);
        });
    });

    function loadNews(page) {
        $.ajax({
            url: 'views/get_posts_news.php?page=' + page, // Додаємо параметр "page" до URL-адреси
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                let posts = response.posts;
                let totalPages = response.totalPages;

                renderPosts(posts); // Відображаємо новини
                renderPagination(page, totalPages); // Відображаємо посилання на сторінки
            },
            error: function(xhr, status, error) {
                console.error(xhr.responseText);
            }
        });
    }

    // Функція для відображення новин
    function renderPosts(posts) {
        let newsContainer = $('#news-container');
        newsContainer.empty();

        posts.forEach(function(post) {
            let postHtml = `
                <div class="news-item text-light description">
                    <div class="news-image-container description">
                        <a href="index.php?action=view_post&id=${post.post_id}">
                            <img class="news-image" src="${post.post_photo}" alt="${post.post_title}" width="300" height="200">
                        </a>
                    </div>
                    ${post.visible !== null && post.visible === 0 ?
                `<div style="position: absolute; top: 10px; right: 10px">
                            <span class="badge bg-success" style="border-radius: 2px; padding: 4px; font-size: 12px">На розгляді</span>
                        </div>
                        <div style="position: absolute; top: 40px; right: 10px">
                            <span class="badge bg-danger" style="border-radius: 2px; padding: 4px; font-size: 12px">${post.category_name}</span>
                        </div>` :
                `<div style="position: absolute; top: 10px; right: 10px">
                            <span class="badge bg-danger" style="border-radius: 2px; padding: 4px; font-size: 12px">${post.category_name}</span>
                        </div>`}
                    <div class="news-caption">
                        <a href="index.php?action=view_post&id=${post.post_id}">${post.post_title}</a>
                    </div>
                    ${post.img !== null ?
                `<div class="service-buttons">
                            <button class="edit-button" title="Редагувати пост" onclick="window.location.href = 'index.php?action=edit_post&id=${post.post_id}'">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="edit-button" title="Видалити пост" onclick="window.location.href = 'index.php?action=confirm_delete_post&id=${post.post_id}'">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </div>` : ``}
                    <div class="post-info-footer description row">
                        <div class="col-sm-10">
                            ${post.img !== null ?
                `<img class="rounded-circle" height="18px" width="18px" src="uploads/profiles/${post.img}" alt="Profile Picture">` :
                `<img class="rounded-circle" height="30px" width="30px" src="img/user-ico.png" alt="Profile Picture">`}
                            <span><a href="index.php?action=profile&id=${post.author_id}" title="Автор посту">${post.author_name}</a></span><br>
                            <img src="img/clock-icon.svg" width="20px" height="20px" alt="Дата: "><span title="Дата та час публікації">${post.formattedDate}</span>
                        </div>
                        <div class="col-sm-2 text-right">
                            <div class="d-flex align-items-center">
                                <img src="img/comments.svg" width="16px" height="16px" title="Коментарі" alt="Коментарі:">
                                <span>${post.comment_count}</span>
                            </div>
                            <div class="d-flex align-items-center">
                                <img src="img/rating.svg" width="16px" height="16px" title="Рейтинг" alt="Рейтинг:">
                                <span>${post.total_rating}</span>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            newsContainer.append(postHtml);
        });
    }

    // Функція для відображення посилань на сторінки
    function renderPagination(currentPage, totalPages) {
        let pagination = $('#pagination');
        pagination.empty();

        // Додаємо кнопку "Перша сторінка"
        let firstPageBtn = $('<li class="page-item"><a class="page-link" href="index.php?action=news&page=1"><i class="fas fa-angle-double-left"></i></a></li>');
        if (currentPage === 1) {
            firstPageBtn.addClass('disabled');
        }
        pagination.append(firstPageBtn);

        // Обмежуємо кількість кнопок пагінації до 10
        let startPage = Math.max(1, currentPage - 4);
        let endPage = Math.min(totalPages, startPage + 9);

        // Додаємо посилання на попередню сторінку
        let previousPageBtn = $('<li class="page-item"><a class="page-link" href="index.php?action=news&page=' + (currentPage - 1) + '"><i class="fas fa-chevron-left"></i></a></li>');
        if (currentPage === 1) {
            previousPageBtn.addClass('disabled');
        }
        pagination.append(previousPageBtn);

        // Додаємо посилання на кожну сторінку
        for (let i = startPage; i <= endPage; i++) {
            let activeClass = i === currentPage ? 'active btn-danger' : '';
            pagination.append('<li class="page-item ' + activeClass + '"><a class="page-link" href="index.php?action=news&page=' + i + '">' + i + '</a></li>');
        }

        // Додаємо посилання на наступну сторінку
        let nextPageBtn = $('<li class="page-item"><a class="page-link" href="index.php?action=news&page=' + (currentPage + 1) + '"><i class="fas fa-chevron-right"></i></a></li>');
        if (currentPage === totalPages) {
            nextPageBtn.addClass('disabled');
        }
        pagination.append(nextPageBtn);

        // Додаємо кнопку "Остання сторінка"
        let lastPageBtn = $('<li class="page-item"><a class="page-link" href="index.php?action=news&page=' + totalPages + '"><i class="fas fa-angle-double-right"></i></a></li>');
        if (currentPage === totalPages) {
            lastPageBtn.addClass('disabled');
        }
        pagination.append(lastPageBtn);
    }

    function getCategories() {
        $.ajax({
            url: 'views/get_categories.php',
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                let categories = response.categories;
                let categorySelect = $('#category');
                categorySelect.empty();
                categorySelect.append('<option value="">Виберіть категорію</option>');
                categories.forEach(function(category) {
                    categorySelect.append('<option value="' + category.category_id + '">' + category.category_name + '</option>');
                });
            },
            error: function(xhr, status, error) {
                console.error(xhr.responseText);
            }
        });
    }

    function resetFilters() {
        $('#filter-form')[0].reset();
        loadNews(1);
    }

    function filterNews(formData) {
        $.ajax({
            url: 'views/filter_posts.php',
            type: 'GET',
            data: formData,
            dataType: 'json',
            success: function(response) {
                let posts = response.posts;
                let totalPages = response.totalPages;
                renderPosts(posts);
                renderPagination(1, totalPages); // Reload pagination with page 1 selected
            },
            error: function(xhr, status, error) {
                console.error(xhr.responseText);
            }
        });
    }
</script>
