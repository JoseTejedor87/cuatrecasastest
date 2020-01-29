// Global
web.global = {
    init: function(){ // Load all global functions here
        console.log("load global functions");
        web.global.homeSelect();
        web.global.carouselItems();
        // web.global.loadHeader();
        // web.global.carouselAwards();
        // web.global.sliderCases();
    },

    loadHeader: function(){ // Some specific function
        console.log("loadHeader()");
    },


    homeSelect: function(){
        /*
        Reference: http://jsfiddle.net/BB3JK/47/
        */
        $('.custom-home-select').each(function(){
            var $this = $(this), numberOfOptions = $(this).children('option').length;

            $this.addClass('select-hidden');
            $this.wrap('<div class="select"></div>');
            $this.after('<div class="select-styled"></div>');

            var $styledSelect = $this.next('div.select-styled');
            $styledSelect.text($this.children('option').eq(0).text());

            var $list = $('<ul />', {
                'class': 'select-options'
            }).insertAfter($styledSelect);

            for (var i = 0; i < numberOfOptions; i++) {
                $('<li />', {
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

    carouselItems: function(){
        $(document).ready(function() {
            $('#carouselHome').owlCarousel({
                loop: true,
                margin: 30,
                nav: true,
                navText:["<div class='nav-btn prev-slide'><i class='icon ion-ios-arrow-left'></i></div>","<div class='nav-btn next-slide'><i class='icon ion-ios-arrow-right'></i></div>"],
                dots: false
            })
        });
    },

    // carouselAwards: function(){
    //     var swiperCarousel = new Swiper ('#carouselAwards', {
    //         slidesPerView: 4,
    //         spaceBetween: 30,
    //         grabCursor: true,
    //         loop: true,
    //         pagination: {
    //             el: '.swiper-pagination',
    //             type: 'progressbar',
    //         },
    //         navigation: {
    //             nextEl: '.swiper-button-next',
    //             prevEl: '.swiper-button-prev',
    //         },
    //     });
    // },

    // sliderCases: function(){
    //     var swiperSlider = new Swiper ('#sliderCases', {
    //         navigation: {
    //             nextEl: '.swiper-button-next',
    //             prevEl: '.swiper-button-prev',
    //         },
    //     });
    // }
}

// Run the global stuff
web.global.init();