<main class="container bg-light p-5">
    <div class="edit-profile">
        <h1 class="display-4 mb-3 bg-dark text-light p-4">Налаштування профілю</h1>
        <div class="profile-picture text-center mb-3">
            <?php
                global $user_id;
                if (!empty($profile_info['img'])) {
                    echo '<img class="border-red rounded-circle object-fit-cover" height="150px" src="uploads/profiles/' . $profile_info['img'] . '" alt="Profile Picture">';
                } else {
                    echo '<img class="border-red rounded-circle object-fit-cover" height="150px" src="img/user-ico.png" alt="Profile Picture">';
                }
                echo '<div class="p-3"><a href="index.php?action=Profile&id=' . $user_id .'"><span class="text-dark display-6">' . $profile_info['nick_name'] . '</span></a></div>';
            ?>
        </div>

        <ul class="nav nav-tabs" id="myTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link text-dark active" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile" type="button" role="tab" aria-controls="profile" aria-selected="true">Профіль</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link text-dark" id="email-tab" data-bs-toggle="tab" data-bs-target="#email" type="button" role="tab" aria-controls="email" aria-selected="false">Email</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link text-dark" id="password-tab" data-bs-toggle="tab" data-bs-target="#password" type="button" role="tab" aria-controls="password" aria-selected="false">Пароль</button>
            </li>
        </ul>
        <div class="tab-content" id="myTabContent">
            <div class="pt-3 tab-pane fade show active" id="profile" role="tabpanel" aria-labelledby="profile-tab">
                <div class="row mb-3">
                    <form method="POST" id="profile_image_form" enctype="multipart/form-data">
                        <label for="profile_image"><b>Фото профілю:</b></label>
                        <div class="input-group">
                            <div class="custom-file">
                                <input class="form-control" type="file" class="custom-file-input" id="profile_image" name="profile_image">
                            </div>
                        </div>
                        <div class="mt-2 mb-3">
                            <button type="submit" name="save_img" class="btn btn-danger">Зберегти</button>
                        </div>
                    </form>
                    <form method="POST" id="phone_number_form">
                        <label for="phone_number"><b>Номер телефону:</b></label>
                        <input type="text" class="form-control" id="phone_number" placeholder="Введіть номер телефону" name="phone_number" value="<?=$profile_info['phone_number']?>">
                        <div class="mt-2 mb-3">
                            <button type="submit" name="save_phone_number" class="btn btn-danger">Зберегти</button>
                        </div>
                    </form>
                    <form method="POST" id="profile_form">
                        <div class="form-group mb-2">
                            <label for="nick_name"><b>Ім'я користувача:</b></label>
                            <input type="text" class="form-control" id="nick_name" placeholder="Введіть повне ім\'я" name="nick_name" value="<?=$profile_info['nick_name']?>">
                        </div>
                        <div class="form-group mb-2">
                            <label><b>Стать</b></label><br>
                            <div>
                                <input type="radio" id="male" name="sex" value="1" <?=($profile_info['sex'] == '1' ? 'checked' : '')?>>
                                <label for="male">Чоловік</label>
                                <input type="radio" id="female" name="sex" value="2" <?=($profile_info['sex'] == '2' ? 'checked' : '')?>>
                                <label for="female">Жінка</label>
                                <input type="radio" id="not_specified" name="sex" value="3" <?=($profile_info['sex'] == '3' ? 'checked' : '')?>>
                                <label for="not_specified">Не вказано</label>
                            </div>
                        </div>
                        <div class="form-group mb-2">
                            <label for="description"><b>Опис профілю:</b></label>
                            <textarea rows="4" class="form-control" id="description" placeholder="Напишіть про себе" name="description"><?=$profile_info['description']?></textarea>
                        </div>
                        <div class="form-group mb-2">
                            <label for="geo_position"><b>Локація:</b></label>
                            <input type="text" class="form-control" id="geo_position" name="geo_position" value="<?=$profile_info['geo_position']?>" placeholder="Введіть поточну локацію">
                        </div>
                        <div class="mt-2 mb-3">
                            <button type="submit" id="save_profile" name="save_profile" class="btn btn-danger">Зберегти</button>
                        </div>
                    </form>
                </div>
                <div id="img_error"></div>
                <div id="number_error"></div>
                <div id="profile_error"></div>
            </div>
            <div class="pt-3 tab-pane fade" id="email" role="tabpanel" aria-labelledby="email-tab">
                <div>
                    <form method="POST" id="email_form">
                        <label for="new_email"><b>Email:</b></label><br>
                        <input type="email" class="form-control" id="new_email" name="new_email" value="<?=$profile_info['email']?>" placeholder="Введіть email">
                        <input type="password" class="form-control mt-2" name="password" placeholder="Поточний пароль">
                        <div class="mt-2 mb-3">
                            <button type="submit" name="change_email" class="btn btn-danger">Змінити Email</button>
                        </div>
                    </form>
                    <div id="email_error" class="pt-5"></div>
                </div>
            </div>
            <div class="pt-3 tab-pane fade" id="password" role="tabpanel" aria-labelledby="password-tab">
                <div>
                    <form method="POST" id="password_form">
                        <label for="current_password"><b>Пароль:</b></label><br>
                        <input type="password" class="form-control" id="current_password" name="current_password" placeholder="Поточний пароль">
                        <input type="password" class="form-control mt-2" id="new_password" name="new_password" placeholder="Новий пароль">
                        <input type="password" class="form-control mt-2" id="confirm_new_password" name="confirm_new_password" placeholder="Підтвердити новий пароль">
                        <div class="mt-2 mb-3">
                            <button type="submit" name="change_password" class="btn btn-danger">Змінити пароль</button>
                        </div>
                    </form>
                    <div id="pass_error" class="pt-5"></div>
                </div>
            </div>
            <script>
                $(document).ready(function(){
                    // Функция для удаления содержимого через 5 секунд
                    function removeContent() {
                        $("#img_error, #number_error, #profile_error, #email_error, #pass_error").empty();
                    }

                    // Обработчик события клика на кнопки "Зберегти"
                    $("button[name='save_img'], button[name='save_phone_number'], #save_profile, button[name='change_password'], button[name='change_email']").click(function(){
                        $("#img_error, #number_error, #profile_error, #email_error, #pass_error").empty();
                        setTimeout(removeContent, 5000);
                    });
                });
            </script>
        </div>
    </div>
</main>