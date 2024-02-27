<?php 
$post_id = intval($_GET['id']);
?>

<article>
    <h3 class="bg-dark p-3 text-light"
        style="border-bottom: 5px solid #D96C6C;
                   border-left: 5px solid transparent;">
        Коментарі
    </h3>
    <section id="comments" class="mb-3">
        <div class="comments-outer-block">
            <div class="comments-inner-block" id="comments-container">
                <!-- Comments will be loaded dynamically here -->
            </div>
        </div>
        <?php if (!empty($_SESSION)): ?>
            <form id="commentForm" class="mt-3">
                <input type="hidden" name="<?php echo $post_id; ?>" value="<?php echo $post_id; ?>">
                <div class="mb-3">
                    <label for="comment" class="form-label"><b>Ваш коментар</b></label>
                    <textarea class="form-control" id="comment" name="comment" rows="3" placeholder="Тут може бути ваш коментар..." required></textarea>
                </div>
                <button type="button" class="btn btn-danger" onclick="addComment()">Коментувати</button>
            </form>
        <?php else: ?>
            <p class="bg-dark text-light mt-3 p-3 rounded"><a class="footer-link" href="index.php?action=login"><b>Авторизуйтесь</b></a>, щоб залишити коментар.</p>
        <?php endif; ?>
    </section>
</article>

<script>
    function addComment() {
        let post_id = <?php echo $post_id; ?>;
        let comment = document.getElementById('comment').value.trim(); // Удаляем лишние пробелы в начале и в конце

        // Проверяем, что комментарий не пустой
        if (comment === "") {
            alert("Порожній коментар не може бути доданий.");
            return; // Прерываем функцию, чтобы комментарий не был отправлен
        }

        $.ajax({
            url: 'views/AddComments.php',
            type: 'POST',
            data: {
                post_id: post_id,
                comment: comment
            },
            success: function(response) {
                getComments();
                document.getElementById('commentForm').reset();
            },
            error: function(xhr, status, error) {
                console.error(error);
            }
        });
    }

    function getComments() {
        var post_id = <?php echo $post_id; ?>;

        $.ajax({
            url: 'views/GetComments.php',
            type: 'GET',
            data: {
                post_id: post_id
            },
            success: function(response) {
                document.getElementById('comments-container').innerHTML = response;
            },
            error: function(xhr, status, error) {
                console.error(error);
            }
        });
    }

    function deleteComment(comment_id) {
        if (confirm("Ви впевнені, що хочете видалити цей коментар?")) {
            $.ajax({
                url: 'views/DeleteComment.php',
                type: 'GET',
                data: {
                    post_id: <?php echo $post_id; ?>,
                    comment_id: comment_id
                },
                success: function(response) {
                    getComments();
                },
                error: function(xhr, status, error) {
                    console.error(error);
                }
            });
        }
    }

    window.onload = function() {
        getComments();
    };
</script>
