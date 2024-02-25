<?php

global $db;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $loginOrEmail = $_POST['login_or_email'] ?? '';
    $password = $_POST['password'] ?? '';

    include 'config/db_config.php';

    $query = "SELECT user_id, login, nick_name, access_type, password_hash FROM users WHERE (login = ? OR email = ?) LIMIT 1";
    $stmt = $db->prepare($query);
    $stmt->bind_param("ss", $loginOrEmail, $loginOrEmail);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user && password_verify($password, $user['password_hash'])) {
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['user_login'] = $user['login'];
        $_SESSION['user_nick'] = $user['nick_name'];
        $_SESSION['user_access_type'] = $user['access_type'];

        echo '<script>window.location.href = "index.php?action=main";</script>';

        exit;
    } else {
        $login_errors[] = "Невірний логін або пароль.";
    }

    $stmt->close();
    $db->close();
}

?>

<main class="container">
    <?php if (empty($_SESSION)) : ?>
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-dark text-light py-4">
                        <h4 class="text-center">Вхід до облікового запису</h4>
                    </div>
                    <form method="post" class="p-4">
                        <div class="form-floating mb-3">
                            <input type="text" id="login_or_email" name="login_or_email" class="form-control" required autocomplete>
                            <label for="login_or_email" class="form-label">Логін або Email</label>
                        </div>
                        <div class="form-group mb-2">
                            <label for="password">Пароль</label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="password" name="password" placeholder="Введіть пароль" required autocomplete>
                                <div class="input-group-append">
                                    <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="form-group form-check mb-1">
                            <input type="checkbox" class="form-check-input" id="remember" name="remember">
                            <label class="form-check-label" for="remember">Запам'ятай мене!</label>
                        </div>
                        <input class="btn btn-danger" type="submit" value="Увійти">
                        <a class="created-account m-4" href="index.php?action=registration">У мене немає аккаунту</a>
                        <?php if (!empty($login_errors)) : ?>
                            <span class='text-danger'>Невірний логін або пароль.</span><br>
                        <?php endif; ?>
                    </form>
                </div>
            </div>
        </div>
    <?php else : ?>
        <?php include 'layout/PageNotFound.php'; ?>
    <?php endif; ?>
</main>
<script src="js/passwordToggle.js"></script>
