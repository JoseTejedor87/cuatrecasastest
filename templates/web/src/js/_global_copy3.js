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

    loadCalendar: function(){
        document.addEventListener('DOMContentLoaded', function() {

            var calendarEl = document.getElementById('eventCalendar');

            var calendar = new FullCalendar.Calendar(calendarEl, {

                /*
                https://fullcalendar.io/docs/locale
                https://fullcalendar.io/docs/locale-demo
                https://codepen.io/pen/?&editable=true&editors=001
                */

                plugins: [ 'dayGrid', 'list' ],
                defaultView: 'dayGridMonth',
                themeSystem: 'standard',
                // weekNumberCalculation: 'ISO',
                timeZone: 'UTC',
                locale: 'es',
                firstDay: 1,
                columnHeaderFormat: { weekday: 'long' },
                height: 'auto',

                views: {
                    dayGridMonth: {
                        type: 'dayGrid',
                        // buttonText: 'month grid',
                        eventLimit: 4,
                        eventLimitText: ""
                    },
                    listMonth: {
                        type: 'listGrid',
                        // buttonText: 'list month',
                        listDayAltFormat: false,
                        eventLimit: false
                    },
                    listDay: {
                        type: 'listGrid',
                        // buttonText: 'list day',
                        listDayAltFormat: false,
                        eventLimit: false
                    },
                },

                // CUSTOM BUTTONS
                // https://fullcalendar.io/docs/customButtons
                customButtons: {
                    icon__button__prev: {
                        // text: 'Prev',
                        icon: 'chevron-left',
                        click: function() {
                            calendar.prev();
                        }
                    },
                    icon__button__next: {
                        // text: 'Next',
                        icon: 'chevron-right',
                        click: function() {
                            calendar.next();
                        }
                    },
                    icon__button__list: {
                        text: 'List',
                        click: function() {
                            calendar.changeView('listMonth');

                            var listButton = document.getElementById("listBot");
                            var gridButton = document.getElementById("gridBot");

                            listButton.classList.add("active");
                            gridButton.classList.remove("active");

                        }
                    },
                    icon__button__grid: {
                        text: 'Grid',
                        click: function() {
                            calendar.changeView('dayGridMonth');

                            var listButton = document.getElementById("listBot");
                            var gridButton = document.getElementById("gridBot");

                            gridButton.classList.add("active");
                            listButton.classList.remove("active");
                        }
                    }
                },


                /*
                ADD ACTIVE STATE TO HEADER BUTTONS
                https://github.com/fullcalendar/fullcalendar/issues/5117
                https://codepen.io/acerix/pen/jOOYypP?editors=0110
                https://stackoverflow.com/questions/41588745/how-add-id-element-to-the-specific-class-line
                */


                // https://github.com/fullcalendar/fullcalendar/issues/5117
                // https://codepen.io/acerix/pen/jOOYypP?editors=0110


                header: {
                    // left: 'prev,next',
                    left: 'icon__button__prev,icon__button__next',
                    center: 'title',
                    // right: 'dayGridMonth,listDay,listMonth'
                    right: 'icon__button__list,icon__button__grid'
                },

                events: [
                    {
                        title: 'La CNMC archiva de nuevo un expediente sobre el sistema de doble precio de los laboratorios farmacéuticos.',
                        titleURL: 'http://localhost/cuatrecasas/es/knowledge/eventDetail/efectos-fiscales-de-la-reforma-contable-y-otras-novedades-fiscales-2008',
                        start: '2020-05-05T10:00:00+00:00',
                        end: '2020-05-05T14:00:00+00:00',
                        sector: 'Fusiones y adquisiciones',
                        place: 'Barcelona',
                        placeLink: 'https://www.cuatrecasas.com/es/oficina/barcelona.html',
                        fullDate: '4 mayo',
                        fullTime: '10.00 — 14.00',
                        button: 'Inscribirme',
                        speakersTitle: 'Ponentes',
                        speakers: [
                            {"speaker_name": "Antón Pérez-Iriondo", "speaker_url" : "#"},
                            {"speaker_name": "Elvira Azaola", "speaker_url" : "#"},
                            {"speaker_name": "Ignacio Javier Irigoyen"}
                        ]
                    },
                    {
                        title: 'La Directiva de Intermediarios (DAC 6) Estado de transposición y principales aspectos conflictivos.',
                        titleURL: 'http://localhost/cuatrecasas/es/knowledge/eventDetail/efectos-fiscales-de-la-reforma-contable-y-otras-novedades-fiscales-2008',
                        start: '2020-05-04',
                        sector: 'Fiscal',
                        place: 'Madrid',
                        placeLink: 'https://www.cuatrecasas.com/es/oficina/barcelona.html',
                        fullDate: '04 mayo',
                        fullTime: '10.30 — 14.30',
                        button: 'Inscribirme',
                    },
                    {
                        title: 'Seminario conjunto con la Autoritat Catalana de la Competènce sobre Compentencia en la Contratación Pública.',
                        titleURL: 'http://localhost/cuatrecasas/es/knowledge/eventDetail/efectos-fiscales-de-la-reforma-contable-y-otras-novedades-fiscales-2008',
                        start: '2020-05-05T10:30:00+00:00',
                        end: '2020-05-05T14:30:00+00:00',
                        sector: 'Laboral',
                        place: 'Barcelona',
                        placeLink: 'https://www.cuatrecasas.com/es/oficina/madrid.html',
                        fullDate: '4 mayo',
                        fullTime: '10.30 — 14.30',
                        button: 'Inscribirme',
                        speakersTitle: 'Ponentes',
                        speakers: [
                            {"speaker_name": "Antón Pérez-Iriondo"},
                            {"speaker_name": "Elvira Azaola", "speaker_url" : "#"},
                            {"speaker_name": "Ignacio Javier Irigoyen"}
                        ]
                    },
                    {
                        title: 'Mesa redonda: información privilegiada y otra información relevante.',
                        titleURL: 'http://localhost/cuatrecasas/es/knowledge/eventDetail/efectos-fiscales-de-la-reforma-contable-y-otras-novedades-fiscales-2008',
                        start: '2020-05-05T11:00:00+00:00',
                        end: '2020-05-05T13:00:00+00:00',
                        sector: 'Fiscal',
                        place: 'Madrid',
                        placeLink: 'https://www.cuatrecasas.com/es/oficina/valencia.html',
                        fullDate: '4 mayo',
                        fullTime: '11.00 — 13.00',
                        button: 'Inscribirme',
                        speakersTitle: 'Ponentes',
                        speakers: [
                            {"speaker_name": "Antón Pérez-Iriondo", "speaker_url" : "#"},
                            {"speaker_name": "Elvira Azaola"},
                            {"speaker_name": "Ignacio Javier Irigoyen", "speaker_url" : "#"}
                        ]
                    },
                    {
                        title: 'La Directiva de Intermediarios (DAC 6) Estado de transposición y principales aspectos conflictivos.',
                        titleURL: 'http://localhost/cuatrecasas/es/knowledge/eventDetail/efectos-fiscales-de-la-reforma-contable-y-otras-novedades-fiscales-2008',
                        start: '2020-05-05T10:30:00+00:00',
                        end: '2020-05-05T14:30:00+00:00',
                        sector: 'Logística y Transporte',
                        place: 'Valencia',
                        placeLink: 'https://www.cuatrecasas.com/es/oficina/barcelona.html',
                        fullDate: '4 mayo',
                        fullTime: '10.30 — 14.30',
                        button: 'Inscribirme',
                        speakersTitle: 'Ponentes',
                        speakers: [
                            {"speaker_name": "Antón Pérez-Iriondo", "speaker_url" : "#"},
                            {"speaker_name": "Elvira Azaola", "speaker_url" : "#"},
                            {"speaker_name": "Ignacio Javier Irigoyen", "speaker_url" : "#"}
                        ]
                    },
                    {
                        title: 'Seminario conjunto con la Autoritat Catalana de la Competènce sobre Compentencia en la Contratación Pública.',
                        titleURL: 'http://localhost/cuatrecasas/es/knowledge/eventDetail/efectos-fiscales-de-la-reforma-contable-y-otras-novedades-fiscales-2008',
                        start: '2020-05-05T10:30:00+00:00',
                        end: '2020-05-05T14:30:00+00:00',
                        sector: 'Consumo y Retail',
                        place: 'Barcelona',
                        placeLink: 'https://www.cuatrecasas.com/es/oficina/madrid.html',
                        fullDate: '4 mayo',
                        fullTime: '10.30 — 14.30',
                        button: 'Inscribirme',
                        speakersTitle: 'Ponentes',
                        speakers: [
                            {"speaker_name": "Antón Pérez-Iriondo"},
                            {"speaker_name": "Elvira Azaola", "speaker_url" : "#"},
                            {"speaker_name": "Ignacio Javier Irigoyen"}
                        ]
                    },
                    {
                        title: 'Mesa redonda: información privilegiada y otra información relevante.',
                        titleURL: 'http://localhost/cuatrecasas/es/knowledge/eventDetail/efectos-fiscales-de-la-reforma-contable-y-otras-novedades-fiscales-2008',
                        start: '2020-05-05T10:30:00+00:00',
                        end: '2020-05-05T14:30:00+00:00',
                        sector: 'Laboral',
                        place: 'Madrid',
                        placeLink: 'https://www.cuatrecasas.com/es/oficina/valencia.html',
                        fullDate: '4 mayo',
                        fullTime: '10.30 — 14.30',
                        button: 'Inscribirme',
                        speakersTitle: 'Ponentes',
                        speakers: [
                            {"speaker_name": "Antón Pérez-Iriondo", "speaker_url" : "#"},
                            {"speaker_name": "Elvira Azaola"},
                            {"speaker_name": "Ignacio Javier Irigoyen", "speaker_url" : "#"}
                        ]
                    },

                    {
                        title: 'Programa de Actualización Tributaria | Novedades fiscales y plan de control tributario 2020.',
                        titleURL: 'http://localhost/cuatrecasas/es/knowledge/eventDetail/efectos-fiscales-de-la-reforma-contable-y-otras-novedades-fiscales-2008',
                        start: '2020-05-06',
                        sector: 'Fiscal',
                        place: 'Bilbao',
                        placeLink: 'https://www.cuatrecasas.com/es/oficina/bilbao.html',
                        fullDate: '6 mayo',
                        fullTime: '10.30 — 14.30',
                        button: 'Inscribirme',
                        speakersTitle: 'Ponentes',
                        speakers: [
                            {"speaker_name": "Antón Pérez-Iriondo", "speaker_url" : "#"},
                            {"speaker_name": "Elvira Azaola", "speaker_url" : "#"},
                            {"speaker_name": "Ignacio Javier Irigoyen"}
                        ]
                    },
                    {
                        title: 'La Directiva de Intermediarios (DAC 6) Estado de transposición y principales aspectos conflictivos.',
                        titleURL: 'http://localhost/cuatrecasas/es/knowledge/eventDetail/efectos-fiscales-de-la-reforma-contable-y-otras-novedades-fiscales-2008',
                        start: '2020-05-06',
                        sector: 'Consumo y Retail',
                        place: 'Barcelona',
                        placeLink: 'https://www.cuatrecasas.com/es/oficina/barcelona.html',
                        fullDate: '6 mayo',
                        fullTime: '10.30 — 14.30',
                        button: 'Inscribirme',
                    },
                    {
                        title: 'La Directiva de Intermediarios (DAC 6) Estado de transposición y principales aspectos conflictivos.',
                        titleURL: 'http://localhost/cuatrecasas/es/knowledge/eventDetail/efectos-fiscales-de-la-reforma-contable-y-otras-novedades-fiscales-2008',
                        start: '2020-05-07',
                        sector: 'Mercantil y Societario',
                        place: 'Valencia',
                        placeLink: 'https://www.cuatrecasas.com/es/oficina/barcelona.html',
                        fullDate: '07 mayo',
                        fullTime: '10.30 — 14.30',
                        button: 'Inscribirme',
                    },
                    {
                        title: 'Programa de Actualización Tributaria | Novedades fiscales y plan de control tributario 2020.',
                        titleURL: 'http://localhost/cuatrecasas/es/knowledge/eventDetail/efectos-fiscales-de-la-reforma-contable-y-otras-novedades-fiscales-2008',
                        start: '2020-05-08',
                        sector: 'Consumo y Retail',
                        place: 'Barcelona',
                        placeLink: 'https://www.cuatrecasas.com/es/oficina/bilbao.html',
                        fullDate: '8 mayo',
                        fullTime: '10.30 — 14.30',
                        button: 'Inscribirme',
                        speakersTitle: 'Ponentes',
                        speakers: [
                            {"speaker_name": "Antón Pérez-Iriondo", "speaker_url" : "#"},
                            {"speaker_name": "Elvira Azaola", "speaker_url" : "#"},
                            {"speaker_name": "Ignacio Javier Irigoyen"}
                        ]
                    },
                    {
                        title: 'La Directiva de Intermediarios (DAC 6) Estado de transposición y principales aspectos conflictivos.',
                        titleURL: 'http://localhost/cuatrecasas/es/knowledge/eventDetail/efectos-fiscales-de-la-reforma-contable-y-otras-novedades-fiscales-2008',
                        start: '2020-05-08',
                        sector: 'Logística y Transporte',
                        place: 'Bilbao',
                        placeLink: 'https://www.cuatrecasas.com/es/oficina/barcelona.html',
                        fullDate: '8 mayo',
                        fullTime: '10.30 — 14.30',
                        button: 'Inscribirme',
                    },
                    {
                        title: 'Mesa redonda: información privilegiada y otra información relevante.',
                        titleURL: 'http://localhost/cuatrecasas/es/knowledge/eventDetail/efectos-fiscales-de-la-reforma-contable-y-otras-novedades-fiscales-2008',
                        start: '2020-05-08',
                        sector: 'Fiscal',
                        place: 'Madrid',
                        placeLink: 'https://www.cuatrecasas.com/es/oficina/valencia.html',
                        fullDate: '8 mayo',
                        fullTime: '11.00 — 13.00',
                        button: 'Inscribirme',
                        speakersTitle: 'Ponentes',
                        speakers: [
                            {"speaker_name": "Antón Pérez-Iriondo", "speaker_url" : "#"},
                            {"speaker_name": "Elvira Azaola"},
                            {"speaker_name": "Ignacio Javier Irigoyen", "speaker_url" : "#"}
                        ]
                    },
                    {
                        title: 'La Directiva de Intermediarios (DAC 6) Estado de transposición y principales aspectos conflictivos.',
                        titleURL: 'http://localhost/cuatrecasas/es/knowledge/eventDetail/efectos-fiscales-de-la-reforma-contable-y-otras-novedades-fiscales-2008',
                        start: '2020-05-08',
                        sector: 'Logística y Transporte',
                        place: 'Valencia',
                        placeLink: 'https://www.cuatrecasas.com/es/oficina/barcelona.html',
                        fullDate: '8 mayo',
                        fullTime: '10.30 — 14.30',
                        button: 'Inscribirme',
                        speakersTitle: 'Ponentes',
                        speakers: [
                            {"speaker_name": "Antón Pérez-Iriondo", "speaker_url" : "#"},
                            {"speaker_name": "Elvira Azaola", "speaker_url" : "#"},
                            {"speaker_name": "Ignacio Javier Irigoyen", "speaker_url" : "#"}
                        ]
                    },
                    {
                        title: 'Programa de Actualización Tributaria | Novedades fiscales y plan de control tributario 2020.',
                        titleURL: 'http://localhost/cuatrecasas/es/knowledge/eventDetail/efectos-fiscales-de-la-reforma-contable-y-otras-novedades-fiscales-2008',
                        start: '2020-05-08',
                        sector: 'Mercantil y Societario',
                        place: 'Bilbao',
                        placeLink: 'https://www.cuatrecasas.com/es/oficina/bilbao.html',
                        fullDate: '8 mayo',
                        fullTime: '10.30 — 14.30',
                        button: 'Inscribirme',
                        speakersTitle: 'Ponentes',
                        speakers: [
                            {"speaker_name": "Antón Pérez-Iriondo", "speaker_url" : "#"},
                            {"speaker_name": "Elvira Azaola", "speaker_url" : "#"},
                            {"speaker_name": "Ignacio Javier Irigoyen"}
                        ]
                    },

                    {
                        title: 'La Directiva de Intermediarios (DAC 6) Estado de transposición y principales aspectos conflictivos.',
                        titleURL: 'http://localhost/cuatrecasas/es/knowledge/eventDetail/efectos-fiscales-de-la-reforma-contable-y-otras-novedades-fiscales-2008',
                        start: '2020-05-11',
                        sector: 'Fusiones y Adquisiciones',
                        place: 'Barcelona',
                        placeLink: 'https://www.cuatrecasas.com/es/oficina/barcelona.html',
                        fullDate: '9 mayo',
                        fullTime: '10.30 — 14.30',
                        button: 'Inscribirme',
                    },
                    {
                        title: 'La Directiva de Intermediarios (DAC 6) Estado de transposición y principales aspectos conflictivos.',
                        titleURL: 'http://localhost/cuatrecasas/es/knowledge/eventDetail/efectos-fiscales-de-la-reforma-contable-y-otras-novedades-fiscales-2008',
                        start: '2020-05-13',
                        allDay: true,
                        sector: 'Fiscal',
                        place: 'Barcelona',
                        placeLink: 'https://www.cuatrecasas.com/es/oficina/barcelona.html',
                        fullDate: '13 mayo',
                        fullTime: '10.30 — 14.30',
                        button: 'Inscribirme',
                    },
                    {
                        title: 'Programa de Actualización Tributaria | Novedades fiscales y plan de control tributario 2020.',
                        titleURL: 'http://localhost/cuatrecasas/es/knowledge/eventDetail/efectos-fiscales-de-la-reforma-contable-y-otras-novedades-fiscales-2008',
                        start: '2020-05-22',
                        allDay: true,
                        sector: 'Mercantil y Societario',
                        place: 'Bilbao',
                        placeLink: 'https://www.cuatrecasas.com/es/oficina/bilbao.html',
                        fullDate: '22 mayo',
                        fullTime: '10.30 — 14.30',
                        button: 'Inscribirme',
                        speakersTitle: 'Ponentes',
                        speakers: [
                            {"speaker_name": "Antón Pérez-Iriondo", "speaker_url" : "#"},
                            {"speaker_name": "Elvira Azaola", "speaker_url" : "#"},
                            {"speaker_name": "Ignacio Javier Irigoyen"}
                        ]
                    },
                    {
                        title: 'La Directiva de Intermediarios (DAC 6) Estado de transposición y principales aspectos conflictivos.',
                        titleURL: 'http://localhost/cuatrecasas/es/knowledge/eventDetail/efectos-fiscales-de-la-reforma-contable-y-otras-novedades-fiscales-2008',
                        start: '2020-05-27',
                        allDay: true,
                        sector: 'Consumo y retail',
                        place: 'Barcelona',
                        placeLink: 'https://www.cuatrecasas.com/es/oficina/barcelona.html',
                        fullDate: '27 mayo',
                        fullTime: '10.30 — 14.30',
                        button: 'Inscribirme',
                    },
                ],

                /*
                DAY BACKGROUND COLOR
                https://stackoverrun.com/es/q/2008610
                https://stackoverflow.com/questions/16089123/can-i-style-the-events-container-cell-instead-of-the-event-with-the-fullcalenda

                >>>
                https://www.google.com/search?q=fullcalendar+dayrender+background+with+events&rlz=1C1CHBD_esES892ES892&oq=fullcalendar+dayrender+background+with+events&aqs=chrome..69i57.23519j0j4&sourceid=chrome&ie=UTF-8

                http://jsfiddle.net/kvakulo/CYnJY/4/

                https://stackoverflow.com/questions/17920017/change-the-day-background-color-in-fullcalendar
                https://stackoverflow.com/questions/26784901/fullcalendar-dayrender
                https://stackoverflow.com/questions/55030405/fullcalendar-dayrender-for-specific-events


                >>>
                https://stackoverflow.com/questions/49929524/how-to-change-cell-background-color-in-fullcalendar
                https://github.com/fullcalendar/fullcalendar/issues/4145

                */





                viewSkeletonRender: function(info) {
                    var headerButtons = calendarEl.querySelectorAll('.fc-button');
                    // console.log('headerButtons: '+headerButtons);

                    headerButtons.forEach(function(button) {
                        if (button.innerText === 'Grid') {
                            button.classList.add('active');
                            button.id = 'gridBot';
                        }
                        if (button.innerText === 'List') {
                            button.id = 'listBot';
                        }
                    });



                    // var today = new Date();
                    // if (date.getDate() === today.getDate()) {
                    //     cell.css("background-color", "red");
                    // }

                    /*
                    success: function (data) {
                        $.each(data, function(i) {
                            $('.fc-day[data-date="'+data[i]["date"]+'"]').css('background', Your Color Code);
                        });
                    }
                    */

                    // $("[data-date="+$.fullCalendar.formatDate(new Date(), "yyyy-MM-dd")+"]").css("background-color", "red");


                    // http://jsfiddle.net/marcrazyness/C8jpm/

                    /*
                    var dayCell = calendarEl.querySelectorAll('.fc-day');
                    console.log('dayCell: '+dayCell);

                    // each(data, function(i) {
                    //         $('.fc-day[data-date="'+data[i]["date"]+'"]').css('background', Your Color Code);
                    //     });

                    dayCell.forEach(function(cell) {
                        console.log('cell: '+cell);
                        cell.classList.add('probando');

                        if(cell === "2020-05-10") {
                            cell.classList.add('probando');
                        }
                    });
                    */

                },


                // https://fullcalendar.io/docs/datesRender

                datesRender: function(info) {

                    var listButton = document.getElementById("listBot");
                    var gridButton = document.getElementById("gridBot");

                    if(info.view.type === "dayGridMonth") {
                        console.log('dayGridMonth');
                    }

                    if(info.view.type === "listMonth" || info.view.type === "listDay") {
                        console.log('listMonth');
                    }



                },


                // RENDER VIEWS
                eventRender: function (info) {

                    if(info.view.type === "dayGridMonth") {
                        info.el.firstChild.innerHTML = '<div class="fc-event-place">'+ info.event.extendedProps.place +'</div><div class="fc-event-sector">'+ info.event.extendedProps.sector +'</div>';
                    }

                    if(info.view.type === "listMonth" || info.view.type === "listDay") {
                        // DETAILS
                        info.el.firstChild.innerHTML = '<div class="event-place"><a href="'+ info.event.extendedProps.placeLink +'">'+ info.event.extendedProps.place +'</a></div><div class="event-date">'+ info.event.extendedProps.fullDate +'</div><div class="event-time">'+ info.event.extendedProps.fullTime +'</div><div class="event-button"><button type="button" class="doble__arrow__button">'+ info.event.extendedProps.button +'</button></div>';

                        // TITLE
                        info.el.lastChild.innerHTML = '<div class="event-intro"><a href="'+ info.event.extendedProps.titleURL +'">'+ info.event.title +'</a></div>';

                        // SPEAKERS
                        // console.log(info.event.extendedProps);
                        var speakersInfo = info.event.extendedProps.speakers;

                        if(speakersInfo) {
                            var htmlStr = '<div class="event-speakers"><div class="title">'+ info.event.extendedProps.speakersTitle +'</div><ul class="related__content">';

                            speakersInfo.forEach(function(value, index, array) {
                                if(value['speaker_url']) {
                                    htmlStr += '<li><a href="'+ value['speaker_url'] +'">'+ value['speaker_name'] +'</a></li>';
                                } else {
                                    htmlStr += '<li><span>'+ value['speaker_name'] +'</span></li>';
                                }
                            });

                            htmlStr += '</div></ul>';
                            info.el.lastChild.innerHTML += htmlStr;
                        }
                    }



                    /*
                    console.log('info.event.start: '+info.event.start);
                    if (info.date === '2020-05-22') {
                        // info.el.css("background-color", "red");

                        alert('yes');

                        info.el.classList.add("fc-event-day");
                        console.log('today.getDate');
                    }
                    */

                    // var dayCell = calendarEl.querySelectorAll('.fc-day')[1].getAttribute("data-date");
                    // // var dayCell = calendarEl.querySelectorAll('.fc-day');
                    // console.log('dayCell: '+dayCell);

                    // var dayEvent = calendarEl.querySelectorAll('.fc-event-container');
                    // // console.log('dayEvent: '+dayEvent);


                    // var dayDate = info.event.extendedProps.probando;
                    // console.log('dayDate: '+dayDate);

                    // console.log('+++');


                    // >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>

                    var allDay = info.event.allDay;
                    console.log('allDay: '+allDay);

                    var skeleton = calendarEl.getElementsByClassName('fc-content-skeleton');
                    console.log('skeleton: '+skeleton);

                    var dayNumber = calendarEl.querySelectorAll('.fc-day');

                    dayNumber.forEach( function(ele, indice, array) {
                        // console.log("En el índice " + indice + " hay este valor: " + ele.getAttribute("data-date"));

                        ele.classList.add('probando');
                        // ele.closest('table').classList.add('probando-table');

                        if(allDay === true) {
                            console.log('allDay = true');
                            // ele.classList.add('probando-day');
                            // ele.closest('table').classList.add('probando-table');

                            // skeleton.classList.add('probando-day');

                        } else {
                            console.log('allDay = false');
                        }


                        // ele.closest('table').classList.add('probando-table');

                        // console.log('dayRender: '+info.el);
                        // console.log('ele: '+ele.closest('table'));
                        // calendarEl.querySelectorAll('.fc-event-container')[indice].classList.add('probando3');
                    });

                    // var dayCell = calendarEl.querySelectorAll('.fc-day');

                    // var dayDate = info.event.extendedProps.probando;
                    // console.log('dayDate: '+dayDate);

                    // console.log('+++');



                },


                /*
                DAY RENDER
                // https://fullcalendar.io/docs/upgrading-from-v3#view-api

                // BACKGROUND COLOR
                // https://stackoverflow.com/questions/55323256/how-to-change-background-color-of-selected-date-in-fullcalendar

                https://stackoverflow.com/questions/17613582/fullcalendar-change-the-color-for-specific-days/36927481
                http://jsfiddle.net/kvakulo/CYnJY/4/

                https://stackoverflow.com/questions/55030405/fullcalendar-dayrender-for-specific-events
                */
                // dayRender: function(info) {
                //     console.log(info.date.toUTCString());
                //     console.log(info.el);
                //     console.log(info.view.type);
                // },


                // https://stackoverflow.com/questions/17920017/change-the-day-background-color-in-fullcalendar


                // outerHTML: "<td class="fc-day fc-widget-content fc-sun fc-future" data-date="2020-05-17"></td>"


                // dayRender: function (info) {
                //     // https://github.com/fullcalendar/fullcalendar/issues/4145

                //     var today = new Date();
                //     var end = new Date();
                //     end.setDate(today.getDate()+7);

                //     // console.log(today);
                //     // console.log(end);

                //     console.log(info.date);
                //     console.log(info.el);

                //     if (info.date === '2020-05-08') {
                //         // info.el.css("background-color", "red");

                //         alert('yes');

                //         info.el.classList.add("fc-event-day");
                //         console.log('today.getDate');
                //     }


                //     if (info.date.getDate() === today.getDate()) {
                //         // info.el.css("background-color", "red");

                //         info.el.classList.add("fc-event-day");
                //         console.log('today.getDate');
                //     }

                //     if(info.date > today && info.date <= end) {
                //         // cell.css("background-color", "yellow");
                //         console.log('today.getDate 2');
                //     }


                // },


                dayRender: function (info) {

                    // var dayNumber = calendarEl.querySelectorAll('.fc-bg');

                    // dayNumber.forEach( function(ele, indice, array) {
                    //     console.log("En el índice " + indice + " hay este valor: " + ele.getAttribute("data-date"));
                    //     console.log('ele: '+ele);
                    //     ele.classList.add('probando-day');
                    //     // ele.closest('table').classList.add('probando-table');

                    //     // console.log('dayRender: '+info.el);
                    //     console.log('ele: '+ele.closest('table'));
                    //     // calendarEl.querySelectorAll('.fc-event-container')[indice].classList.add('probando3');
                    // });

                    // // var dayCell = calendarEl.querySelectorAll('.fc-day');
                    // // var dayDate = info.event.extendedProps.probando;
                    // // console.log('dayDate: '+dayDate);

                    // console.log('+++');

                    // console.log('dayRender: '+info.date.toUTCString());
                    // console.log('dayRender: '+info.el);
                    // console.log('dayRender: '+info.view.type);

                    // var allDay = info.event.allDay;
                    // console.log('allDay: '+allDay);


                    // var today = new Date();
                    // if (date.getDate() === today.getDate()) {
                    //     cell.css("background-color", "red");
                    // }

                    /*
                    success: function (data) {
                        $.each(data, function(i) {
                            $('.fc-day[data-date="'+data[i]["date"]+'"]').css('background', Your Color Code);
                        });
                    }
                    */


                    // success: function (data) {
                    //     $.each(data, function(i) {
                    //         $('.fc-day[data-date="'+data[i]["date"]+'"]').css('background', Your Color Code);
                    //     });
                    // }

                },


                // DAY CLICK
                /*
                dateClick: function(info) {
                    // alert('clicked day: ' + info.dateStr);

                    // var clickedDate = date;
                    // if(clickedDate >= event.start && clickedDate <= event.end) {}

                    var eventDay = info.dateStr;
                    console.log('eventDay');
                    console.log(eventDay);

                    // https://stackoverflow.com/questions/20523905/get-events-clicking-a-day-in-fullcalendar


                    // var eventStart = info.start;
                    // console.log('eventStart');
                    // console.log(eventStart);

                    // var eventEnd = info.end;
                    // console.log('eventEnd');
                    // console.log(eventEnd);

                    // var dayEvent = $calendar.fullCalendar('clientEvents', function(event) { return +event.start.startOf('day') == +date.startOf('day'); });


                    // events: dayEvents,
                    // getEvents(date);


                    // function getEvents(date){
                    //     all_events.forEach(function(entry) {
                    //         if (entry['start'] == date.format()){
                    //         alert(entry['title']);}
                    //         else if (entry['start'] <= date.format() && entry['end'] >= date.format()){
                    //         alert(entry['title']);}
                    //     });
                    // }

                },
                */


                /*
                GET DAY
                https://fullcalendar.io/docs/Calendar-getDate
                https://fullcalendar.io/docs/date-object
                https://fullcalendar.io/docs/visibleRange
                */

                // EVENT CLICK
                eventClick: function(info) {
                    // info.jsEvent.preventDefault();

                    // https://stackoverflow.com/questions/41479497/fullcalendar-unable-to-get-date-of-the-clicked-event

                    var eventDate = info.event.start;
                    // var eventDate = moment(info.event.start).format("YYYY-MM-DD")
                    // console.log('eventDate');
                    // console.log(eventDate);
                    //example output: "Wed Oct 02 2019 00:00:00 GMT-0600 (Central Standard Time)"

                    // EVENT DATE
                    // https://stackoverflow.com/questions/26151121/fullcalendar-js-get-events-of-the-day-on-click/26161449
                    // http://jsfiddle.net/syf9ycbc/7/
                    // http://jsfiddle.net/syf9ycbc/8/

                    // var eventDay = info.dateStr;
                    // console.log('eventDay');
                    // console.log(eventDay);

                    // info.dateStr


                    /*
                    CHANGE VIEW
                    https://fullcalendar.io/docs/view-api
                    https://fullcalendar.io/docs/Calendar-changeView
                    */

                    calendar.changeView('listDay', eventDate);

                    var gridFocus = document.getElementById('gridFocus');
                    gridFocus.scrollIntoView();

                    // handleDayView();

                    // TRY
                    // https://stackoverflow.com/questions/45354280/in-jquery-fullcalendar-how-to-display-a-list-of-events-for-the-current-month-vi

                    function handleDayView() {

                        var calendarDayEl = document.getElementById('eventCalendarDay');

                        var dayHtml = '<h4 class="section__title section__title--events">Listado eventos</h4><article class="event__list__item"><div class="event__list__details"><div class="event-place"><a href="'+ info.event.extendedProps.placeLink +'">'+ info.event.extendedProps.place +'</a></div><div class="event-date">'+ info.event.extendedProps.fullDate +'</div><div class="event-time">'+ info.event.extendedProps.fullTime +'</div><div class="event-button"><button type="button" class="doble__arrow__button">'+ info.event.extendedProps.button +'</button></div></div>';

                        dayHtml += '<div class="event__list__info"><div class="event-intro"><a href="'+ info.event.extendedProps.titleURL +'">'+ info.event.title +'</a></div>';

                        var speakersDay = info.event.extendedProps.speakers;
                        if(speakersDay) {
                            dayHtml += '<div class="event-speakers"><div class="title">'+ info.event.extendedProps.speakersTitle +'</div><ul class="related__content">';
                            speakersDay.forEach(function(value, index, array) {
                                if(value['speaker_url']) {
                                    dayHtml += '<li><a href="'+ value['speaker_url'] +'">'+ value['speaker_name'] +'</a></li>';
                                } else {
                                    dayHtml += '<li><span>'+ value['speaker_name'] +'</span></li>';
                                }
                            });
                            dayHtml += '</ul></div>';
                        }

                        dayHtml += '</div></article>';

                        // RENDER VIEW
                        calendarDayEl.innerHTML = dayHtml;

                        // calendarDayEl.scrollIntoView();

                    }
                }

            });


            // https://fullcalendar.io/docs/date-formatting
            /*
            var str = calendar.formatDate('2018-09-01', {
                year: 'numeric',
                month: '2-digit',
                day: '2-digit'
            });

            console.log('formatDate: '+str); // "1 de septiembre de 2018 0:00 UTC"
            */

            calendar.render();



            // var element = document.getElementById("myDIV");
            // element.classList.add("mystyle");

            // var miClass = document.getElementsByClassName("fc-icon__button__grid-button");
            // miClass.classList.add("active");

            // var listButton = document.getElementsByClassName("fc-icon__button__list-button");
            // var gridButton = document.getElementsByClassName("fc-icon__button__grid-button");

            // gridButton.classList.add("active123");
            // console.log(listButton);


        });

        // $(function () {
        //     $('.fc-icon__button__grid-button').click(function(e){

        //         console.log('click icon__button__grid');
        //         e.preventDefault();

        //         $(this).addClass('active');
        //         $('.fc-icon__button__list-button').removeClass('active');
        //         $(this).addClass('active');
        //     });

        //     $('.fc-icon__button__list-button').click(function(e){

        //         console.log('click icon__button__list');
        //         e.preventDefault();

        //         $('.fc-icon__button__grid-button').removeClass('active');
        //         $(this).addClass('active');
        //     });
        // });





    },



    loadCalendarList: function(){
        document.addEventListener('DOMContentLoaded', function() {

            var calendarListEl = document.getElementById('eventCalendarList');
            var calendarList = new FullCalendar.Calendar(calendarListEl, {

                plugins: [ 'list', 'bootstrap' ],
                defaultView: 'listMonth',
                themeSystem: 'bootstrap',
                timeZone: 'UTC',
                locale: 'es',
                height: 'auto',
                header: false,
                displayEventTime: true,
                eventLimit: true, // allow "more" link when too many events
                listDayAltFormat: false,

                /*
                events: [{"title":"Long Event","start":"2020-05-07","end":"2020-05-10"},{"groupId":"999","title":"Repeating Event","start":"2020-05-16T16:00:00+00:00"},{"title":"Conference","start":"2020-05-11","end":"2020-05-13"},{"title":"Meeting","start":"2020-05-12T10:30:00+00:00","end":"2020-05-12T12:30:00+00:00"},{"title":"Lunch","start":"2020-05-12T12:00:00+00:00"},{"title":"Birthday Party","start":"2020-05-13T07:00:00+00:00"}],
                */

                events: [
                    {
                        title: 'La Directiva de Intermediarios (DAC 6) Estado de transposición y principales aspectos conflictivos.',
                        titleURL: 'http://localhost/cuatrecasas/es/knowledge/eventDetail/efectos-fiscales-de-la-reforma-contable-y-otras-novedades-fiscales-2008',
                        start: '2020-05-04T10:30:00+00:00',
                        end: '2020-05-04T14:30:00+00:00',
                        sector: 'Logística y Transporte',
                        place: 'Barcelona',
                        placeLink: 'https://www.cuatrecasas.com/es/oficina/barcelona.html',
                        fullDate: '4 mayo',
                        fullTime: '10.30 — 14.30',
                        button: 'Inscribirme',
                        speakersTitle: 'Ponentes',
                        speakers: [
                            {"speaker_name": "Antón Pérez-Iriondo", "speaker_url" : "#"},
                            {"speaker_name": "Elvira Azaola", "speaker_url" : "#"},
                            {"speaker_name": "Ignacio Javier Irigoyen", "speaker_url" : "#"}
                        ]
                    },
                    {
                        title: 'Seminario conjunto con la Autoritat Catalana de la Competènce sobre Compentencia en la Contratación Pública.',
                        titleURL: 'http://localhost/cuatrecasas/es/knowledge/eventDetail/efectos-fiscales-de-la-reforma-contable-y-otras-novedades-fiscales-2008',
                        start: '2020-05-04T10:30:00+00:00',
                        end: '2020-05-04T14:30:00+00:00',
                        sector: 'Penal',
                        place: 'Madrid',
                        placeLink: 'https://www.cuatrecasas.com/es/oficina/madrid.html',
                        fullDate: '4 mayo',
                        fullTime: '10.30 — 14.30',
                        button: 'Inscribirme',
                        speakersTitle: 'Ponentes',
                        speakers: [
                            {"speaker_name": "Antón Pérez-Iriondo"},
                            {"speaker_name": "Elvira Azaola", "speaker_url" : "#"},
                            {"speaker_name": "Ignacio Javier Irigoyen"}
                        ]
                    },
                    {
                        title: 'Mesa redonda: información privilegiada y otra información relevante.',
                        titleURL: 'http://localhost/cuatrecasas/es/knowledge/eventDetail/efectos-fiscales-de-la-reforma-contable-y-otras-novedades-fiscales-2008',
                        start: '2020-05-04T10:30:00+00:00',
                        end: '2020-05-04T14:30:00+00:00',
                        sector: 'Laboral',
                        place: 'Valencia',
                        placeLink: 'https://www.cuatrecasas.com/es/oficina/valencia.html',
                        fullDate: '4 mayo',
                        fullTime: '10.30 — 14.30',
                        button: 'Inscribirme',
                        speakersTitle: 'Ponentes',
                        speakers: [
                            {"speaker_name": "Antón Pérez-Iriondo", "speaker_url" : "#"},
                            {"speaker_name": "Elvira Azaola"},
                            {"speaker_name": "Ignacio Javier Irigoyen", "speaker_url" : "#"}
                        ]
                    },
                    {
                        title: 'Programa de Actualización Tributaria | Novedades fiscales y plan de control tributario 2020.',
                        titleURL: 'http://localhost/cuatrecasas/es/knowledge/eventDetail/efectos-fiscales-de-la-reforma-contable-y-otras-novedades-fiscales-2008',
                        start: '2020-05-05',
                        sector: 'Mercantil y Societario',
                        place: 'Bilbao',
                        placeLink: 'https://www.cuatrecasas.com/es/oficina/bilbao.html',
                        fullDate: '5 mayo',
                        fullTime: '10.30 — 14.30',
                        button: 'Inscribirme',
                        speakersTitle: 'Ponentes',
                        speakers: [
                            {"speaker_name": "Antón Pérez-Iriondo", "speaker_url" : "#"},
                            {"speaker_name": "Elvira Azaola", "speaker_url" : "#"},
                            {"speaker_name": "Ignacio Javier Irigoyen"}
                        ]
                    },
                    {
                        title: 'La Directiva de Intermediarios (DAC 6) Estado de transposición y principales aspectos conflictivos.',
                        titleURL: 'http://localhost/cuatrecasas/es/knowledge/eventDetail/efectos-fiscales-de-la-reforma-contable-y-otras-novedades-fiscales-2008',
                        start: '2020-05-09',
                        sector: 'Consumo y Retail',
                        place: 'Barcelona',
                        placeLink: 'https://www.cuatrecasas.com/es/oficina/barcelona.html',
                        fullDate: '9 mayo',
                        fullTime: '10.30 — 14.30',
                        button: 'Inscribirme',
                    },
                    {
                        title: 'La Directiva de Intermediarios (DAC 6) Estado de transposición y principales aspectos conflictivos.',
                        titleURL: 'http://localhost/cuatrecasas/es/knowledge/eventDetail/efectos-fiscales-de-la-reforma-contable-y-otras-novedades-fiscales-2008',
                        start: '2020-05-12',
                        sector: 'Fusiones y Adquisiciones',
                        place: 'Barcelona',
                        placeLink: 'https://www.cuatrecasas.com/es/oficina/barcelona.html',
                        fullDate: '9 mayo',
                        fullTime: '10.30 — 14.30',
                        button: 'Inscribirme',
                    }
                ],

                // https://fullcalendar.io/docs/list-view
                eventRender: function (info) {

                    // DETAILS
                    info.el.firstChild.innerHTML = '<div class="event-place"><a href="'+ info.event.extendedProps.placeLink +'">'+ info.event.extendedProps.place +'</a></div><div class="event-date">'+ info.event.extendedProps.fullDate +'</div><div class="event-time">'+ info.event.extendedProps.fullTime +'</div><div class="event-button"><button type="button" class="doble__arrow__button">'+ info.event.extendedProps.button +'</button></div>';

                    // TITLE
                    info.el.lastChild.innerHTML = '<div class="event-intro"><a href="'+ info.event.extendedProps.titleURL +'">'+ info.event.title +'</a></div>';

                    // SPEAKERS
                    // console.log(info.event.extendedProps);
                    var speakersInfo = info.event.extendedProps.speakers;

                    if(speakersInfo) {
                        var htmlStr = '<div class="event-speakers"><div class="title">'+ info.event.extendedProps.speakersTitle +'</div><ul class="related__content">';

                        speakersInfo.forEach(function(value, index, array) {
                            if(value['speaker_url']) {
                                htmlStr += '<li><a href="'+ value['speaker_url'] +'">'+ value['speaker_name'] +'</a></li>';
                            } else {
                                htmlStr += '<li><span>'+ value['speaker_name'] +'</span></li>';
                            }
                        });

                        htmlStr += '</div></ul>';
                        info.el.lastChild.innerHTML += htmlStr;
                    }

                },
            });

            calendarList.render();
        });
    },

}

// Run the global stuff
web.global.init();
