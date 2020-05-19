// Global
web.global = {
    init: function(){ // Load all global functions here
        web.global.loadMiscell();
        // web.global.stickyMenu();
    },

    stickyMenu: function(){

        /* When the user scrolls down, hide the navbar. When the user scrolls up, show the navbar */
        /*
        var prevScrollpos = window.pageYOffset;
        window.onscroll = function() {
            var currentScrollPos = window.pageYOffset;
            if (prevScrollpos > currentScrollPos) {
                document.getElementById("secondaryNav").style.top = "62px";
            } else {
                document.getElementById("secondaryNav").style.top = "0";
            }
            prevScrollpos = currentScrollPos;
        }
        */


        var prev    = 0;
        var $window = $(window);
        var nav     = $('#navDesktop #secondaryNav');
        var logo    = $('#navDesktop #secondaryNav .navbar-brand');
        var header  = $('#navDesktop header');

        $window.on('scroll', function(){
            var scrollTop = $window.scrollTop();
            nav.toggleClass('stickyNav', scrollTop > prev);
            logo.toggleClass('stickyNav', scrollTop > prev);
            header.toggleClass('stickyNav', scrollTop > prev);
            prev = scrollTop;
        });
    },

    stickyMenuOld: function(){
        // Hide Header on scroll down
        var didScroll;
        var lastScrollTop = 0;
        var delta = 5;
        var navbarHeight = $('header').outerHeight();

        $(window).scroll(function(event){
            didScroll = true;
        });

        setInterval(function() {
            if (didScroll) {
                hasScrolled();
                didScroll = false;
            }
        }, 250);

        function hasScrolled() {
            var st = $(this).scrollTop();

            // Make sure they scroll more than delta
            if(Math.abs(lastScrollTop - st) <= delta)
                return;

            // If they scrolled down and are past the navbar, add class .nav-up.
            // This is necessary so you never see what is "behind" the navbar.
            if (st > lastScrollTop && st > navbarHeight){
                // Scroll Down
                $('header').removeClass('nav-down').addClass('nav-up');
            } else {
                // Scroll Up
                if(st + $(window).height() < $(document).height()) {
                    $('header').removeClass('nav-up').addClass('nav-down');
                }
            }

            lastScrollTop = st;
        }
    },

    /*
    stickyMenu: function(){
        const body = document.body;
        const triggerMenu = document.querySelector(".page-header .trigger-menu");
        const nav = document.querySelector(".page-header nav");
        const menu = document.querySelector(".page-header .menu");
        const scrollUp = "scroll-up";
        const scrollDown = "scroll-down";
        let lastScroll = 0;

        triggerMenu.addEventListener("click", () => {
          body.classList.toggle("menu-open");
        });

        window.addEventListener("scroll", () => {
          const currentScroll = window.pageYOffset;
          if (currentScroll == 0) {
            body.classList.remove(scrollUp);
            return;
          }

          if (currentScroll > lastScroll && !body.classList.contains(scrollDown)) {
            // down
            body.classList.remove(scrollUp);
            body.classList.add(scrollDown);
          } else if (currentScroll < lastScroll && body.classList.contains(scrollDown)) {
            // up
            body.classList.remove(scrollDown);
            body.classList.add(scrollUp);
          }
          lastScroll = currentScroll;
        });
    },
    */

    loadMiscell: function(){
        $(function () {
            $('.button__bookmark').click(function(e){
                e.preventDefault();
                $(this).toggleClass('on');
            });

            $('.no-link').click(function(e){
                e.preventDefault();
            });

            // Sticky Menu
            // $("#contact-nav").after('.main');
            // $(window).scroll(function () {
            //     if ($(document).scrollTop() > 0 ) {
            //         $('#contactNav .navbar-brand').hide();

            //         if ($('.main__nav__wrapper .sub-nav').hasClass('show')) {
            //             $('.main__nav__wrapper .sub-nav').removeClass('show');
            //         }

            //     } else {
            //         $('#contactNav .navbar-brand').fadeIn();
            //     }

            //     if ($(document).scrollTop() > 62 ) {
            //         $('#secondaryNav, header').addClass('stickyNav');

            //     } else {
            //         $('#secondaryNav, header').removeClass('stickyNav');
            //     }
            // });
        });
    },

    showMoreLess: function(){
        // https://stackoverflow.com/questions/58455602/debugging-show-more-show-less-button-missing-paragraphs-of-text

        $('.read-more').each(function() {
            if ($(this).children('p').length > 4) {
                $(this).children('p:lt(3)').show();
                $(this).append('<button type="button" class="doble__arrow__button loadMore">Ver más</button>');
            }
        });
        $('.read-more').on("click", '.loadMore', function() {
            $(this).parent('.read-more').children('p').slideDown();
            $(this).removeClass('loadMore').addClass('loadLess').text('Ver menos');
            $('.doble__arrow__button').blur();
        });
        $('.read-more').on("click", '.loadLess', function() {
            $(this).parent('.read-more').children('p:gt(2)').slideUp();
            $(this).removeClass('loadLess').addClass('loadMore').text('Ver más');
            $('.doble__arrow__button').blur();
        });
    },

    /* Stand by */
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

    lawyerResults: function(){
        // Add display: flex instead of display: block to loader
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
    },

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

    sliderHome: function(){
        var swiperCarousel = new Swiper ('#sliderHome', {
            slidesPerView: 1,
            spaceBetween: 0,
            loop: true,
            // grabCursor: true,
            speed: 800,
            allowSlidePrev: false,
            allowTouchMove: false,
            autoplay: {
                delay: 5000,
            },
            navigation: {
                nextEl: '.home__preview__button'
            }
        });
    },

    sliderGeneral: function(){
        var swiperCarousel = new Swiper ('.slider__general', {
            slidesPerView: 3,
            spaceBetween: 25,
            // loop: true,
            // allowTouchMove: false,
            // grabCursor: true,
            scrollbar: {
                el: '.swiper-scrollbar',
                draggable: true
            }
        });
    },

    sliderNews: function(){
        var swiperCarousel = new Swiper ('#sliderNews', {
            slidesPerView: 3,
            spaceBetween: 25,
            // loop: true,
            // allowTouchMove: false,
            // grabCursor: true,
            scrollbar: {
                el: '.swiper-scrollbar',
                draggable: true
            }
        });
    },

    sliderCases: function(){
        var swiperSlider = new Swiper ('#sliderCases', {
            slidesPerView: 'auto',
            spaceBetween: 20,
            // loop: true,
            grabCursor: true
        });
    },

    sliderAwards: function(){
        var swiperCarousel = new Swiper ('#sliderAwards', {
            slidesPerView: 'auto',
            spaceBetween: 60,
            centeredSlides: true,
            loop: true,
            grabCursor: true
        });
    },

    testimonials: function(){
        $('.testimonials__item').hover(function(){
            if ($(this).hasClass('selected')) return;
            $('.selected').removeClass('selected');
            $(this).addClass('selected');
        });
    },

}

// Run the global stuff
web.global.init();
