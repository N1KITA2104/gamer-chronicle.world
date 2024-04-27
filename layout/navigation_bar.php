<nav class="navbar navbar-expand-lg bg-dark navbar-dark fixed-top">
    <div class="container">
        <a class="navbar-brand d-flex" href="index.php?action=main">
            <img class="me-2" src="img/icon_logo.svg" alt="Logo" width="30px" height="30px">
            <span class="logo-font" style="color: #D96C6C">GamerChronicle</span>
        </a>
        <button class="navbar-toggler" type="button" title="Випадне меню" data-bs-toggle="collapse" data-bs-target="#collapsibleNavbar">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse justify-content-end" id="collapsibleNavbar">
            <ul class="navbar-nav d-flex align-items-center">
                <li class="nav-item pe-3">
                    <form class="d-flex" method="POST" id="searchForm">
                        <input class="form-control me-2" id="search" type="search" placeholder="Пошук по сайту..." aria-label="Search" name="search">
                    </form>
                    <script>
                        $(document).ready(function() {
                            $('#search').on('input', function() {
                                var searchData = $(this).val();
                                $.ajax({
                                    type: 'POST',
                                    url: 'views/search.php',
                                    data: { search: searchData },
                                    success: function(response) {
                                        $('#searchResults').html(response); 
                                    }
                                });
                            });
                        });

                    </script>
                </li>
                <li class="nav-item">
                    <a class="nav-link" title="Новини" href="index.php?action=news">Всі новини</a>
                </li>
                <?php
                global $db;
                    if (empty($_SESSION)) {
                        echo '
                        <li class="nav-item">
                            <a class="nav-link" title="Зареєструватися" href="index.php?action=registration"><img src="img/sign_up.svg" height="25px" width="25px" alt="Sign up"></a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" title="Увійти" href="index.php?action=login"><img src="img/log_in.svg" height="25px" width="25px" alt="Log in"></a>
                        </li>';
                    } else {
                        include 'config/db_config.php';
                        $user_id = $_SESSION['user_id'];
                        $query = "SELECT * FROM users WHERE user_id =" . $user_id;
                        $result = mysqli_query($db, $query);
                        $user_data = mysqli_fetch_assoc($result);

                        echo '
                        <li class="nav-item dropdown dropdown-danger">
                            <a class="nav-link dropdown-toggle" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">' . (!empty($user_data['img']) ? '<img class="border-red rounded-circle nav-img" height="30px" width="30px" src="uploads/profiles/' . $user_data['img'] . '" alt="Profile Picture">' : '<img class="rounded-circle" height="30px" width="30px" src="img/user-ico.png" alt="Profile Picture">') . '</a>
                            <ul class="dropdown-menu dropdown-menu-dark rounded-0 mt-2" aria-labelledby="userDropdown">
                                <li><a class="dropdown-item" title="Мій профіль" href="index.php?action=profile&id='.$_SESSION['user_id'].'"><img src="img/profile_logo.svg" height="25px" width="25px" alt="Мій профіль"> Профіль</a></li>
                                <li><a class="dropdown-item" title="Налаштування" href="index.php?action=edit_profile"><img src="img/settings-ico.svg" height="25px" width="25px" alt="Налаштування"> Налаштування</a></li>
                                <li><a class="dropdown-item" title="Додати новину" href="index.php?action=create_post"><img src="img/add.svg" height="20px" width="20px" alt="Додати новину"> Додати статтю</a></li>
                                <li><a class="dropdown-item" title="Вийти" href="views/logout.php"><img src="img/logout_logo.svg" height="20px" width="20px" alt="Logout"> Вийти</a></li>
                            </ul>
                        </li>';
                    }
                ?>
            </ul>
        </div>
    </div>
</nav>
<div id="scroll-progress"></div>
<div id="searchResults"></div>