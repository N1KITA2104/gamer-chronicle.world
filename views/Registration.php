<?php
global $db;
if (empty($_SESSION['user_id'])) {
    $errors = array();

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $login = $_POST["login"];
        $password = $_POST["password"];
        $confirm_password = $_POST["confirm_password"];
        $email = $_POST["email"];

        if (strlen($login) < 4 || !preg_match('/^[a-zа-яієїґ0-9_\-]+$/ui', $login)) {
            $errors[] = "Логін повинен містити принаймні 4 символи та складатися з латинських, кириличних літер, цифр, _ або -.";
        }

        if (strlen($password) < 7 || !preg_match('/[a-z]/', $password) || !preg_match('/[A-Z]/', $password) || !preg_match('/[0-9]/', $password)) {
            $errors[] = "Пароль повинен містити принаймні 7 символів і включати великі та малі літери та цифри.";
        }

        if ($password != $confirm_password) {
            $errors[] = "Поля 'Пароль' і 'Повторіть пароль' повинні співпадати.";
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Неправильний формат електронної пошти.";
        }

        if (empty($errors)) {
            include 'config/db_config.php';

            $check_query = $db->prepare("SELECT login, email FROM users WHERE login = ? OR email = ?");
            $check_query->bind_param("ss", $login, $email);
            $check_query->execute();
            $check_result = $check_query->get_result();

            if ($check_result->num_rows > 0) {
                $errors[] = "Email або логін вже існують.";
            } else {
                $password_hash = password_hash($password, PASSWORD_BCRYPT);

                $stmt = $db->prepare("INSERT INTO users (login, password_hash, email) VALUES (?, ?, ?)");
                $stmt->bind_param("sss", $login, $password_hash, $email);

                if ($stmt->execute()) {
                    echo '<script>window.location.href = "index.php?action=RegistrationSuccessful"</script>';
                    exit();
                } else {
                    echo "Error: " . $stmt->error;
                }

                $stmt->close();
            }

            $db->close();
        }
    }
    ?>

    <main class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card mb-3">
                    <div class="card-header bg-dark text-light py-4">
                        <h4 class="text-center">Реєстрація облікового запису</h4>
                    </div>
                    <div class="card-body">
                        <form method="post" class="p-3">
                            <?php if (!empty($errors)) : ?>
                                <div class="alert alert-danger" role="alert">
                                    <ul style="list-style-type: none;">
                                        <?php foreach ($errors as $error) : ?>
                                            <li><?php echo $error; ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            <?php endif; ?>
                            <div class="form-floating mb-2">
                                <input type="text" class="form-control" id="login" name="login" value="<?php echo isset($login) ? htmlspecialchars($login) : ''; ?>" required>
                                <label for="login">Логін користувача</label>
                            </div>
                            <div class="form-floating mb-2">
                                <input type="email" class="form-control" id="email" name="email" value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>" required autocomplete="email">
                                <label for="email">Пошта користувача</label>
                            </div>
                            <div class="form-group mb-2">
                                <label for="password">Пароль</label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="password" name="password" placeholder="Введіть пароль" required>
                                    <div class="input-group-append">
                                        <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group mb-4">
                                <label for="confirm_password">Підтвердження паролю</label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="Повторіть пароль" required>
                                    <div class="input-group-append">
                                        <button class="btn btn-outline-secondary" type="button" id="toggleConfirmPassword">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <script src="js/passwordToggle.js"></script>
                            <div class="form-group form-check mb-2">
                                <input type="checkbox" class="form-check-input" id="userAgreement" required>
                                <label class="form-check-label" for="userAgreement">Я погоджуюсь з <a class="btn-link text-dark" target="_blank" href="index.php?action=TermsOfService">Умовами користування</a></label>
                            </div>
                            <div class="form-group form-check mb-4">
                                <input type="checkbox" class="form-check-input" id="privacyPolicy" required>
                                <label class="form-check-label" for="privacyPolicy">Я погоджуюсь з <a class="btn-link text-dark" target="_blank" href="index.php?action=PrivacyPolicy">Політикою конфіденційності</a></label>
                            </div>
                            <button type="submit" class="btn btn-danger mb-3">Зареєструватися</button> <br>
                            <a class="btn-link text-dark" href="index.php?action=Login">У мене вже існує аккаунт</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <?php
} else {
    include('layout/PageNotFound.php');
}
?>
