<?php
global $db;
include "config/db_config.php";

if (!empty($_SESSION)) {
    $user_id = $_SESSION['user_id'];
    $query = "SELECT * FROM users WHERE user_id = $user_id";
    $result = mysqli_query($db, $query);
    $profile_info = mysqli_fetch_assoc($result);
    mysqli_close($db);

    include "templates/edit_profile_form.php";
} else {
    include 'templates/page_not_found.php';
}
?>

<script>
$(document).ready(function() {
    $(document).ready(function() {
        $('#profile_image_form').submit(function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            $.ajax({
                url: 'views/update_profile_image.php',
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                success: function(response) {
                    let jsonResponse = JSON.parse(response);
                    if (jsonResponse.status === 'success') {
                        $('#img_error').html('<div class="alert alert-success" role="alert">' + jsonResponse.message + '</div>');
                    } else {
                        $('#img_error').html('<div class="alert alert-danger" role="alert">' + jsonResponse.message + '</div>');
                    }
                }
            });
        });
    });

    $('#profile_form').submit(function(e) {
        e.preventDefault();
        const formData = $(this).serialize();
        $.ajax({
            url: 'views/update_profile.php',
            type: 'POST',
            data: formData,
            success: function(response) {
                const jsonResponse = JSON.parse(response);
                if (jsonResponse.status === 'success') {
                    $('#profile_error').html('<span class="alert alert-success" role="alert">' + jsonResponse.message + '</span>');
                } else {
                    $('#profile_error').html('<span class="alert alert-danger" role="alert">' + jsonResponse.message + '</span>');
                }
            }
        });
    });


    $('#phone_number_form').submit(function(e) {
        e.preventDefault();
        const formData = $(this).serialize();
        $.ajax({
            url: 'views/update_phone_number.php',
            type: 'POST',
            data: formData,
            success: function(response) {
                let jsonResponse = JSON.parse(response);
                if (jsonResponse.status === 'success') {
                    $('#number_error').html('<span class="alert alert-success" role="alert">' + jsonResponse.message + '</span>');
                } else {
                    $('#number_error').html('<span class="alert alert-danger" role="alert">' + jsonResponse.message + '</span>');
                }
            }
        });
    });

    $('#email_form').submit(function(e) {
        e.preventDefault();
        const formData = $(this).serialize();
        $.ajax({
            url: 'views/update_email.php',
            type: 'POST',
            data: formData,
            success: function(response) {
                let jsonResponse = JSON.parse(response);
                if (jsonResponse.status === 'success') {
                    $('#email_error').html('<span class="alert alert-success" role="alert">' + jsonResponse.message + '</span>');
                } else {
                    $('#email_error').html('<span class="alert alert-danger" role="alert">' + jsonResponse.message + '</span>');
                }
            }
        });
    });

    $('#password_form').submit(function(e) {
        e.preventDefault();
        const formData = $(this).serialize();
        $.ajax({
            url: 'views/update_password.php',
            type: 'POST',
            data: formData,
            success: function(response) {
                let jsonResponse = JSON.parse(response);
                if (jsonResponse.status === 'success') {
                    $('#pass_error').html('<span class="alert alert-success" role="alert">' + jsonResponse.message + '</span>');
                } else {
                    $('#pass_error').html('<span class="alert alert-danger" role="alert">' + jsonResponse.message + '</span>');
                }
            }
        });
    });
});
</script>
