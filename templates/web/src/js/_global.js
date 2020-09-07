// Global
web.global = {
    init: function(){ // Load all global functions here
        web.global.stickyMenu();
        web.global.mobileNav();
        web.global.loadMiscell();
    },

    stickyMenu: function(){
         // menu interactios (hover versus click)
        $('#about-nav .dropdown-menu')
            .bind('mouseover', function(event) {
                $(this).prev('.nav-link').addClass('active');
            })
            .bind('mouseleave', function(event) {
                $(this).prev('.nav-link').removeClass('active');
        });

        $('.lang-region-menu').hide();

        // region / lang switch
        $('.lang-region-toggle').click(function(e){
            e.preventDefault();
            $(this).next('.lang-region-menu').show();
        });

        $('.menu-close.langs').click(function(e){
            e.preventDefault();
            $(this).parent().parent('.lang-region-menu').hide();
        });

        // sticky menu
        var prev            = 0;
        var $window         = $(window);

        var secondaryNav    = $('#navDesktop #secondaryNav');
        var logoNav         = $('#navDesktop #secondaryNav .navbar-brand');
        var subMenuNav      = $('#navDesktop #secondaryNav .dropdown-menu');
        // submenu click
        var aboutNav        = $('#navDesktop #primaryNav #aboutNav .dropdown-menu');
        var userNav         = $('#navDesktop #userNav');
        var mainHeader      = $('body.desktop header');
        var langsNav        = $('#navDesktop .lang-region-menu');

        $window.on('scroll', function(){
            var scrollTop = $window.scrollTop();
            secondaryNav.toggleClass('stickyNav', scrollTop > prev);
            logoNav.toggleClass('stickyNav', scrollTop > prev);
            subMenuNav.removeClass('show', scrollTop > prev);
            // submenu click
            aboutNav.removeClass('show', scrollTop > prev);
            userNav.toggleClass('stickyNav', scrollTop > prev);
            mainHeader.toggleClass('stickyNav', scrollTop > prev);
            langsNav.hide();
            prev = scrollTop;
        });

        $('#hamburgerButton').click(function(e){
            e.preventDefault();
            console.log($('#navDesktop #secondaryNav').attr("class"));
            secondaryNav.toggleClass('stickyNav');
        });
    },

    mobileNav: function(){

        // function getViewports() {
        //     document.getElementById("innerWidth").innerHTML =
        //     window.innerWidth;
        //     document.getElementById("innerHeight").innerHTML =
        //     window.innerHeight;

        //     document.getElementById("clientWidth").innerHTML =
        //     document.documentElement.clientWidth;
        //     document.getElementById("clientHeight").innerHTML =
        //     document.documentElement.clientHeight;

        //     var body = document.body;

        //     if (document.getElementById("clientWidth").innerHTML < 1199) {
        //         body.classList.add('mobile');
        //         body.classList.remove('desktop');
        //     } else if (document.getElementById("clientWidth").innerHTML > 1200) {
        //         body.classList.add('desktop');
        //         body.classList.remove('mobile');
        //     }
        // }

        // window.addEventListener('resize', getViewports);
        // window.addEventListener('orientationchange', getViewports);

        // getViewports();

        // Reposition header
        function checkWindowSize() {
            var body        = $('body');
            if (window.matchMedia('(max-width: 1199px)').matches) {
                body.addClass('mobile');
                body.removeClass('desktop');
            } else {
                body.addClass('desktop');
                body.removeClass('mobile');
            }
        }

        $(function() {
            checkWindowSize();
        });

        $(window).resize(function() {
            checkWindowSize();
        });

        // Mobile Nav - Hide logo
        $('#navMobileWrapper .navbar-toggler.navbar-open').click(function(e){
            e.preventDefault();
            $('#navMobileWrapper .navbar-brand').addClass('off');
        });

        // Mobile Nav - Close all submenus & show logo
        $('#navMobileWrapper .navbar-toggler.navbar-close').click(function(e){
            e.preventDefault();
            $('#navMobileWrapper .navbar-brand').removeClass('off');
            $("#navMobile .collapse").collapse("hide");
        });

    },

    loadMiscell: function(){
        $('.button__bookmark').click(function(e){
            e.preventDefault();
            $(this).toggleClass('button__bookmark--on');
        });

        $('.no-link').click(function(e){
            e.preventDefault();
        });

        // $('.doble__arrow__accordion').click(function(){
        //     $(this).toggleClass('doble__arrow__accordion--on');

        //     if ($(this).hasClass('doble__arrow__accordion--on')) {
        //         $(this).text('Ver menos');
        //     } else {
        //         $(this).text('Ver más');
        //     }

        // });
    },

    // BORRAR (activar en la propia página)
    // showMoreLess: function(){
    //     $('.read-more').each(function() {
    //         if ($(this).children('p').length > 4) {
    //             $(this).children('p:lt(3)').show();
    //             $(this).append('<button type="button" class="doble__arrow__accordion loadMore">Ver más</button>');
    //         }
    //     });
    //     $('.read-more').on("click", '.loadMore', function() {
    //         $(this).parent('.read-more').children('p').slideDown();
    //         $(this).removeClass('loadMore').addClass('loadLess').text('Ver menos');
    //         $('.doble__arrow__accordion').blur();
    //     });
    //     $('.read-more').on("click", '.loadLess', function() {
    //         $(this).parent('.read-more').children('p:gt(2)').slideUp();
    //         $(this).removeClass('loadLess').addClass('loadMore').text('Ver más');
    //         $('.doble__arrow__accordion').blur();
    //     });
    // },


    /* Stand by BORRAR MAYBE */
    /*
    toggleMoreInfo: function(){
        // Show / hide filters

        // <button data-id="add_group"> Add Group </button>

        $('button').on('click', function() {
            $('#' + $(this).data('id')).slideToggle();
        });


        // $('.toggle-filters').click(function(e){
        //     e.preventDefault();
        //     $('.event-filters').slideToggle();
        // });

        // $('.toggle-filters, .button-favorite, .button-calendar').click(function(e){
        //     e.preventDefault();
        //     $(this).toggleClass('on');

        //     var el = $(this);
        //     el.text() == el.data("text-original")
        //         ? el.text(el.data("text-swap"))
        //         : el.text(el.data("text-original"));
        // });
    },

    toggleTextButton: function(){
        // Change text literal buttons
        $('.toggle-filters, .button-favorite, .button-calendar').click(function(e){
            e.preventDefault();
            $(this).toggleClass('on');

            var el = $(this);
            el.text() == el.data("text-original")
                ? el.text(el.data("text-swap"))
                : el.text(el.data("text-original"));
        });
    },
    */

    customSelects: function(){
        /*
        Custom Selects
        Reference: http://jsfiddle.net/BB3JK/47/
        */
        $('.custom__select').each(function(){
            var $this = $(this), numberOfOptions = $(this).children('option').length;

            $this.addClass('select-hidden');
            $this.wrap('<div class="select"></div>');
            $this.after('<div class="select-styled"></div>');

            var $styledSelect = $this.next('div.select-styled');
            // $styledSelect.text($this.children('option').eq(0).text());
            $styledSelect.text($this.children('option:selected').text())

            var $list = $('<ul/>', {
                'class': 'select-options'
            }).insertAfter($styledSelect);

            for (var i = 0; i < numberOfOptions; i++) {
                $('<li/>', {
                    text: $this.children('option').eq(i).text(),
                    rel: $this.children('option').eq(i).val()
                }).appendTo($list);
            }

            var $listItems = $list.children('li');

            $styledSelect.click(function(e) {
                e.stopPropagation();
                $('div.select-styled.active').not(this).each(function(){
                    $(this).removeClass('active').next('ul.select-options').hide();
                });
                $(this).toggleClass('active').next('ul.select-options').toggle();
            });

            $listItems.click(function(e) {
                e.stopPropagation();
                $styledSelect.text($(this).text()).removeClass('active');
                $this.val($(this).attr('rel'));
                $list.hide();
                //console.log($this.val());
            });

            $(document).click(function() {
                $styledSelect.removeClass('active');
                $list.hide();
            });
        });
    },

    testimonials: function(){
        $('.testimonials__item').hover(function(){
            if ($(this).hasClass('selected')) return;
            $('.selected').removeClass('selected');
            $(this).addClass('selected');
        });
    },

    /* BORRAR
    // ACTIVAR EN CADA PAGE >>> sliderGeneral & sliderCases
    // VER PAGE COMPONENTES */
    /*

    sliderHome: function(){
        var swiperHome = new Swiper ('#sliderHome', {
            slidesPerView: 1,
            spaceBetween: 0,
            loop: true,
            // grabCursor: true,
            speed: 800,
            allowSlidePrev: false,
            allowTouchMove: false,
            // autoplay: {
            //     delay: 5000,
            // },
            navigation: {
                nextEl: '.home__preview__button'
            }
        });
    },


    sliderGeneral: function(){
        var swiperGeneral = new Swiper ('.slider__general', {
            slidesPerView: 3,
            spaceBetween: 25,
            // loop: true,
            allowTouchMove: true,
            grabCursor: true,
            scrollbar: {
                el: '.swiper-scrollbar',
                draggable: true
            }
        });
    },

    sliderCases: function(){
        var swiperCases = new Swiper ('#sliderCases', {
            slidesPerView: 'auto',
            spaceBetween: 20,
            loop: true,
            allowTouchMove: true,
            grabCursor: true
        });
    },
    */
    // END BORRAR


    // sliderAwards: function(){
    //     var swiperAwards = new Swiper ('#sliderAwards', {
    //         slidesPerView: 1,
    //         spaceBetween: 0,
    //         centeredSlides: true,
    //         loop: true,
    //         grabCursor: true,
    //         breakpoints: {
    //             1199: {
    //                 slidesPerView: 3,
    //                 spaceBetween: 60,
    //             },
    //         }
    //     });
    // },

}

// Run the global stuff
web.global.init();
