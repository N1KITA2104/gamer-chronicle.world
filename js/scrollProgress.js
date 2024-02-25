$(document).ready(function() {
    function updateScrollProgress() {
        const windowHeight = $(window).height();
        const documentHeight = $(document).height();
        const scrollTop = $(window).scrollTop();
        const progress = (scrollTop / (documentHeight - windowHeight)) * 100;
        $('#scroll-progress').css('width', progress + '%');
    }

    $(window).on('scroll', updateScrollProgress);
    $(window).on('load', updateScrollProgress);
});
