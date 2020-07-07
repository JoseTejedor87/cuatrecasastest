$(function () {
    $('#readMore').each(function() {
        if ($(this).children('p').length > 4) {
            $(this).children('p:lt(3)').show();
            $(this).append('<button type="button" class="doble__arrow__accordion loadMore">Ver más</button>');
        }else{
            $(this).children('p:lt(3)').show();
            $(this).parent('#readMore').children('p:gt(2)').slideUp();
        }
    });
    $('#readMore').on("click", '.loadMore', function() {
        $(this).parent('#readMore').children('p').slideDown();
        $(this).removeClass('loadMore').addClass('loadLess');
        $('.doble__arrow__accordion').blur();
    });
    $('#readMore').on("click", '.loadLess', function() {
        $('html, body').animate({
            scrollTop: $("#readMore").offset().top
        }, 300);
        $(this).parent('#readMore').children('p:gt(2)').slideUp();
        $(this).removeClass('loadLess').addClass('loadMore');
        //$('.doble__arrow__accordion').blur();
    });
    $('.doble__arrow__accordion').click(function(){
        $(this).toggleClass('doble__arrow__accordion--on');
        if ($(this).hasClass('doble__arrow__accordion--on')) {
            $(this).text('Ver menos');
        } else {
            $(this).text('Ver más');
        }
    });
});
