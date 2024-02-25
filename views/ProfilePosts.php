<div id="post-count"></div>
<div id="posts-container"></div>

<script>
    let page = 0;
    let isLoading = false;
    let hasMorePosts = true;

    function loadPosts(id) {
        if (!isLoading && hasMorePosts) {
            isLoading = true;
            $.ajax({
                url: 'views/GetPosts.php',
                type: 'GET',
                data: {
                    id: id,
                    page: page
                },
                success: function (response) {
                    if (response.trim() !== '') {
                        $('#posts-container').append(response);
                        page++;
                    } else {
                        hasMorePosts = false;
                    }
                    isLoading = false;
                },
                error: function (xhr, status, error) {
                    console.error(error);
                    isLoading = false;
                }
            });
        }
    }

    $(document).ready(function () {
        let urlParams = new URLSearchParams(window.location.search);
        let userId = urlParams.get('id');
        if (userId) {
            // Load initial posts
            loadPosts(userId);
            // Load post count
            $.ajax({
                url: 'views/GetPostsCount.php',
                type: 'GET',
                data: {
                    id: userId
                },
                success: function (response) {
                    $('#post-count').html(response);
                },
                error: function (xhr, status, error) {
                    console.error(error);
                }
            });

             $(window).scroll(function () {
                if ($(window).scrollTop() >= ($(document).height() - $(window).height()) * 0.9) {
                    loadPosts(userId);
                }
            });
        }
    });
</script>
