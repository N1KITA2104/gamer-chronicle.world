$(document).ready(function() {
    let btn = $('#scrollToTopBtn');

    $(window).scroll(function() {
        if ($(window).scrollTop() > 300) {
            btn.addClass('show');
        } else {
            btn.removeClass('show');
        }
    });

    btn.on('click', function(e) {
        e.preventDefault();
        $('html, body').animate({scrollTop: 0}, 50);
    });

    // Add passive event listener for touch and wheel events
    window.addEventListener('touchstart', function() {}, { passive: true });
    window.addEventListener('wheel', function() {}, { passive: true });
});
