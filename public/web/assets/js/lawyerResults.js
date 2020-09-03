$(function () {
    // Add display: flex instead of display: block for loader
    $('.lawyer__search__wrapper__loader').css("display", "flex").hide();

    // Toogle Results View
    $('.list').click(function(e){
        e.preventDefault();

        $('.icon__button.grid').removeClass('active');
        $(this).addClass('active');

        $('.lawyer__search__wrapper__loader').fadeIn('fast');

        $('.lawyer__search__wrapper').addClass('loading');
        $('.lawyer__search__wrapper').removeClass('lawyer__search__wrapper--grid');
        $('.lawyer__search__wrapper').addClass('lawyer__search__wrapper--list');

        setTimeout(
            function(){
                $('.lawyer__search__wrapper__loader').fadeOut();
                $('.lawyer__search__wrapper').removeClass('loading');
            }
        , 600);
    });

    $('.grid').click(function(e){
        e.preventDefault();

        $('.icon__button.list').removeClass('active');
        $(this).addClass('active');

        $('.lawyer__search__wrapper__loader').fadeIn('fast');

        $('.lawyer__search__wrapper').addClass('loading');
        $('.lawyer__search__wrapper').removeClass('lawyer__search__wrapper--list');
        $('.lawyer__search__wrapper').addClass('lawyer__search__wrapper--grid');

        setTimeout(
            function(){
                $('.lawyer__search__wrapper__loader').fadeOut();
                $('.lawyer__search__wrapper').removeClass('loading');
            }
        , 600);
    });
});
