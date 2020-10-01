// Global
web.global = {
    init: function(){ // Load all global functions here
        web.global.stickyMenu();
        web.global.mobileNav();
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
            // console.log($('#navDesktop #secondaryNav').attr("class"));
            secondaryNav.toggleClass('stickyNav');
        });
    },

    mobileNav: function(){
        // Reposition header
        function checkWindowSize() {
            var body = $('body');
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
        $('.testimonials__wrapper div:nth-child(2)').addClass('selected');
        $('.testimonials__item').hover(function(){
            if ($(this).hasClass('selected')) return;
            $('.selected').removeClass('selected');
            $(this).addClass('selected');
        });
    }
}

// Run the global stuff
web.global.init();
