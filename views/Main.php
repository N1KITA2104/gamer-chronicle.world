<main class="container bg-light p-3">
    <article>
        <section class="display-4 bg-dark text-light p-4 mb-3"
                 style="border-bottom: 10px solid #D96C6C;
                        border-left: 10px solid transparent">
        <h1>Останні новини GamerChronicle</h1>
            <?php
            if(!empty($_SESSION)) {
                $user_greeting = ($_SESSION['user_nick'] == 'Noname') ? $_SESSION['user_login'] : $_SESSION['user_nick'];
                echo "<h4>Ласкаво просимо на портал, <span style='color: #D96C6C;'> $user_greeting </span></h4>";
            }
            ?>
        </section>
        <?php include 'PostsMainLatest.php'; ?>
        <h2 class="display-6 bg-dark text-light p-3 mt-3"
            style="border-bottom: 5px solid #D96C6C;
                   border-left: 10px solid transparent">
            Новини зі світу кіно!
        </h2>
        <?php include 'PostsMainKino.php'; ?>
    </article>
</main>
