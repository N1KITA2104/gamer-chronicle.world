<?php

include 'config/db_config.php';

$errors = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["email"]) && isset($_POST["code"]) && isset($_POST["password"])) {
        $email = $_POST["email"];
        $confirmation_code = $_POST["code"];
        $new_password = $_POST["password"];
        
        // Перевірка чи паролі співпадають
        if ($_POST["password"] !== $_POST["confirm_password"]) {
            $errors[] = "Паролі не співпадають.";
        }
        
        // Перевірка чи код підтвердження вірний
        $check_query = $db->prepare("SELECT code FROM GeneratedCodes WHERE email = ? ORDER BY created_at DESC LIMIT 1");
        $check_query->bind_param("s", $email);
        $check_query->execute();
        $check_result = $check_query->get_result();
        
        if ($check_result->num_rows > 0) {
            $row = $check_result->fetch_assoc();
            $stored_confirmation_code = $row['code'];
            
            if ($confirmation_code != $stored_confirmation_code) {
                $errors[] = "Неправильний код підтвердження.";
            }
        } else {
            $errors[] = "Код підтвердження вже використаний або не існує.";
        }
        
        // Валідація мінімальної довжини паролю та рекомендації Google
        if (strlen($new_password) < 8) {
            $errors[] = "Пароль повинен містити принаймні 8 символів.";
        }
        
        if (!preg_match("#[0-9]+#", $new_password)) {
            $errors[] = "Пароль повинен містити принаймні одну цифру.";
        }
        
        if (!preg_match("#[A-Z]+#", $new_password)) {
            $errors[] = "Пароль повинен містити принаймні одну велику літеру.";
        }
        
        if (!preg_match("#[a-z]+#", $new_password)) {
            $errors[] = "Пароль повинен містити принаймні одну маленьку літеру.";
        }
        
        if (!preg_match("/[!@#$%^&*()\-_=+{};:,<.>]/", $new_password)) {
            $errors[] = "Пароль повинен містити принаймні один спеціальний символ.";
        }
        
        if (empty($errors)) {
            // Якщо немає помилок, оновлюємо пароль
            $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);
            $update_query = $db->prepare("UPDATE users SET password_hash = ? WHERE email = ?");
            $update_query->bind_param("ss", $hashed_password, $email);
            $update_query->execute();
            
            // Видалення кода підтвердження з бази даних
            $delete_old_query = $db->prepare("DELETE FROM GeneratedCodes WHERE email = ?");
            $delete_old_query->bind_param("s", $email);
            $delete_old_query->execute();
            
            echo '<script>window.location.href = "index.php?action=Login"</script>';
        }
    } else {
        $errors[] = "Усі поля форми повинні бути заповнені.";
    }
}

?>

<?php if(empty($_SESSION["user_id"])): ?>
<main class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card mt-5">
                <div class="bg-dark card-header text-center p-4">
                    <h5 class="text-light card-title display-6">Відновлення пароля</h5>
                </div>
                <div class="card-body p-5">
                    <?php if (!empty($errors)): ?>
                        <div class="alert alert-danger" role="alert">
                            <?php foreach ($errors as $error): ?>
                                <?php echo $error; ?><br>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                    <form id="forgotPasswordForm" method="post">
                        <div class="form-group mb-3">
                            <label for="email">Email</label>
                            <input type="email" id="email" name="email" class="form-control" placeholder="Введіть ваш email" required>
                        </div>
                        <div class="form-group mb-3">
                            <label for="code">Код підтвердження</label>
                            <input type="text" id="code" name="code" class="form-control" placeholder="Введіть код підтвердження" required>
                        </div>
                        <div class="form-group mb-3">
                            <label for="password">Новий пароль</label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="password" name="password" placeholder="Введіть пароль" required>
                                <div class="input-group-append">
                                    <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="form-group mb-3">
                            <label for="confirm_password">Підтвердіть пароль</label>
                            <div class="input-group">
                                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="Підтвердіть пароль" required>
                                    <div class="input-group-append">
                                        <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                </div>
                        </div>
                        <button type="button" onclick="sendCode()" class="btn btn-primary">Відправити код</button>
                        <button type="submit" class="btn btn-danger">Підтвердити пароль</button>
                        <p id="timer"></p>
                    </form>
                </div>
            </div>
        </div>
    </div>
</main>
<script src="js/passwordToggle.js"></script>
<?php else: ?>
    <?php include "layout/PageNotFound.php"; ?>
<?php endif; ?>

<script>
    let isEmailSending = false;
    let remainingTime = 60;

    function sendCode() {
        if (isEmailSending) return;

        let email = document.getElementById("email").value;
        let xhr = new XMLHttpRequest();
        xhr.open("POST", "views/change_password_code.php", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        // Обработка ответа от скрипта change_password_code.php
        xhr.onreadystatechange = function() {
            if (xhr.readyState === XMLHttpRequest.DONE) {
                if (xhr.status === 200) {
                    if (xhr.responseText === "user_not_found") {
                        alert("Аккаунту з такою поштою не існує.");
                    } else {
                        alert("Лист успішно відправлений.");
                        isEmailSending = true;
                        startTimer();
                    }
                } else {
                    alert("Помилка при відправці листа: " + xhr.responseText);
                }
            }
        };
        xhr.send("email=" + email);
    }

    function startTimer() {
        let timerElement = document.getElementById("timer");

        let timerInterval = setInterval(function() {
            remainingTime--;
            timerElement.textContent = remainingTime + " с";

            if (remainingTime <= 0) {
                clearInterval(timerInterval);
                timerElement.textContent = "";
                isEmailSending = false;
            }
        }, 1000);
    }
</script>