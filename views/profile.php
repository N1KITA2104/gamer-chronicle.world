<?php
global $db;
include 'config/db_config.php';

$user_id = isset($_GET["id"]) ? (int) mysqli_real_escape_string($db, $_GET["id"]) : null;

if ($user_id !== null) {
    $query = "SELECT * FROM users WHERE user_id = $user_id";
    $result = mysqli_query($db, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $profile_info = mysqli_fetch_assoc($result);

        $sex_icons = [
            1 => 'чоловік',
            2 => 'жінка',
            3 => 'не вказано'
        ];
        $sex_icon = $sex_icons[$profile_info['sex']] ?? 'не вказано';

        ?>
        <main class="container p-0">
            <div class="card rounded-0">
                <div class="card-header text-light">
                    <div class="profile-container p-5 d-flex flex-column flex-md-row justify-content-between align-items-center rounded">
                        <div class="d-flex flex-column align-items-center">
                            <img class="border-red rounded-circle object-fit-cover" src="<?= (!empty($profile_info['img']) ? 'uploads/profiles/' . $profile_info['img'] : 'img/user-ico.png') ?>" alt="Profile Picture" style="width: 100px; height: 100px;">
                            <h4 class="card-title mt-3 mb-3"><?= $profile_info['nick_name'] ?></h4>
                        </div>

                        <div class="p-4 rounded-2" style="background-color: rgba(0,0,0,0.25)">
                            <p class="card-text">Локація: <?= $profile_info['geo_position'] ?></p>
                            <p class="card-text">Стать: <?= $sex_icon ?></p>
                            <p class="card-text">Дата реєстрації: <?= date('j F Y', strtotime($profile_info['reg_date'])) ?></p>
                        </div>
                    </div>
                    <div class="m-3">
                    <?php if (!empty($_SESSION) && $user_id == $_SESSION['user_id']): ?>
                        <a href="index.php?action=edit_profile" class="btn btn-danger"><img src="img/settings-ico.svg" alt="Налаштування" height="20px" width="20px"> Змінити профіль</a>
                    <?php endif; ?>
                    </div>
                </div>
                <div class="card-body">
                    <ul class="nav nav-tabs" id="myTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active text-dark font-weight-bold" id="active-tab" data-bs-toggle="tab" data-bs-target="#active" type="button" role="tab" aria-controls="active" aria-selected="true">Активність</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link text-dark font-weight-bold" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile" type="button" role="tab" aria-controls="profile" aria-selected="true">Оголошення</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link text-dark font-weight-bold" id="posts-tab" data-bs-toggle="tab" data-bs-target="#posts" type="button" role="tab" aria-controls="posts" aria-selected="true">Пости</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link text-dark font-weight-bold" id="gallery-tab" data-bs-toggle="tab" data-bs-target="#gallery" type="button" role="tab" aria-controls="gallery" aria-selected="true">Галерея</button>
                        </li>
                    </ul>
                    <div class="tab-content" id="myTabContent">
                        <div class="tab-pane fade show active" id="active" role="tabpanel" aria-labelledby="active-tab">
                            <div class="mb-3 mt-3 p-3">
                                <?php include("views/profile_activity.php"); ?>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
                            <div class="profile-about mb-3 mt-3 p-3 ps-5 bg-light rounded">
                                <p class="card-text">
                                    <?= (!empty($profile_info['description']) ? $profile_info['description'] : 'Опис відсутній') ?>
                                </p>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="posts" role="tabpanel" aria-labelledby="posts-tab">
                            <div class="mb-3 mt-3 p-3">
                                <?php include("views/profile_posts.php"); ?>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="gallery" role="tabpanel" aria-labelledby="gallery-tab">
                            <div class="mb-3 mt-3 p-3">

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <?php
    } else {
        include 'templates/page_not_found.php';
    }
} else {
    include 'templates/page_not_found.php';
}

mysqli_close($db);
?>
