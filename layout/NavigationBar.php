<nav class="navbar navbar-expand-lg bg-dark navbar-dark fixed-top">
    <div class="container">
        <a class="navbar-brand d-flex" href="index.php?action=Main">
            <img class="me-2" src="img/icon_logo.svg" alt="GamerChronicle" width="30px" height="30px">
            <span class="logo-font" style="color: #D96C6C">GamerChronicle</span>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#collapsibleNavbar">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse justify-content-end" id="collapsibleNavbar">
            <ul class="navbar-nav d-flex align-items-center">
            <!-- Search Form -->
                <li class="nav-item pe-3">
                    <form class="d-flex">
                        <input class="form-control me-2" id="search" type="search" placeholder="Пошук по сайту..." aria-label="Search">
                        <button class="btn btn-outline-danger" type="submit">Пошук</button>
                    </form>
                </li>
                <li class="nav-item">
                    <a class="nav-link" title="Новини" href="index.php?action=News">Новини</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" title="Ігри" href="index.php?action=Games">Ігри</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" title="Гайди" href="index.php?action=Guide">Гайди</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" title="Галерея" href="index.php?action=Gallery">Галерея</a>
                </li>
                <?php
                global $db;
                    if (empty($_SESSION)) {
                        echo '
                        <li class="nav-item">
                            <a class="nav-link" title="Зареєструватися" href="index.php?action=Registration"><img src="img/sign_up.svg" height="25px" width="25px" alt="Sign up"></a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" title="Увійти" href="index.php?action=Login"><img src="img/log_in.svg" height="25px" width="25px" alt="Log in"></a>
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
                                <li><a class="dropdown-item" title="Мій профіль" href="index.php?action=Profile&id='.$_SESSION['user_id'].'"><img src="img/profile_logo.svg" height="25px" width="25px" alt="Мій профіль"> Профіль</a></li>
                                <li><a class="dropdown-item" title="Налаштування" href="index.php?action=EditProfile"><img src="img/settings-ico.svg" height="25px" width="25px" alt="Налаштування"> Налаштування</a></li>
                                <li><a class="dropdown-item" title="Додати новину" href="index.php?action=CreatePost"><img src="img/add.svg" height="20px" width="20px" alt="Додати новину"> Додати статтю</a></li>
                                <li><a class="dropdown-item" title="Вийти" href="views/Logout.php"><img src="img/logout_logo.svg" height="20px" width="20px" alt="Logout"> Вийти</a></li>
                            </ul>
                        </li>';
                    }
                ?>
            </ul>
        </div>
    </div>
</nav>
<div id="scroll-progress"></div>