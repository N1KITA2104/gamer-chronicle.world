$(document).ready(function() {
    $('#togglePassword').click(function() {
        let passwordInput = $('#password');
        if (passwordInput.attr('type') === 'password') {
            passwordInput.attr('type', 'text');
            $('#togglePassword').html('<i class="fas fa-eye-slash"></i>');
        } else {
            passwordInput.attr('type', 'password');
            $('#togglePassword').html('<i class="fas fa-eye"></i>');
        }
    });

    $('#toggleConfirmPassword').click(function() {
        let confirmPasswordInput = $('#confirm_password');
        if (confirmPasswordInput.attr('type') === 'password') {
            confirmPasswordInput.attr('type', 'text');
            $('#toggleConfirmPassword').html('<i class="fas fa-eye-slash"></i>');
        } else {
            confirmPasswordInput.attr('type', 'password');
            $('#toggleConfirmPassword').html('<i class="fas fa-eye"></i>');
        }
    });
});