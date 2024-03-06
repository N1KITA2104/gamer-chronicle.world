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
        <div class="row justify-content-center mb-3">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-dark text-light p-4">
                        <h1 class="text-center display-6">Вхід до облікового запису</h1>
                    </div>
                    <form method="post" class="p-5">
                        <div class="form-group mb-3">
                            <label for="login_or_email" class="form-label">Login або email</label>
                            <input type="text" id="login_or_email" name="login_or_email" class="form-control" placeholder="Введіть login або email" required autocomplete>
                        </div>
                        <div class="form-group mb-3">
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
                        <div class="form-group form-check mb-3">
                            <input type="checkbox" class="form-check-input" id="remember" name="remember">
                            <label class="form-check-label" for="remember">Запам'ятай мене!</label>
                        </div>
                        <div class="row">
                            <input class="btn btn-danger" type="submit" value="Увійти">
                        </div>
                        <?php if (!empty($login_errors)) : ?>
                            <span class='text-danger'>Невірний логін або пароль.</span><br>
                        <?php endif; ?>
                    </form>
                    <div class="card-footer row p-4">
                        <a class="created-account" href="index.php?action=registration">У мене немає облікового запису</a>
                        <a class="forgot-password" href="index.php?action=forgot_password">Забули пароль?</a>
                    </div>
                </div>
            </div>
        </div>
    <?php else : ?>
        <?php include 'templates/page_not_found.php'; ?>
    <?php endif; ?>
</main>
<script src="js/passwordToggle.js"></script>
