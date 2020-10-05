$(function () {
    // var swiperGeneral = new Swiper('.slider__general', {
    //     init: false,
    //     slidesPerView: 1,
    //     spaceBetween: 20,
    //     allowTouchMove: false,
    //     grabCursor: false,
    //     scrollbar: {
    //         el: '.swiper-scrollbar',
    //         draggable: true
    //     },
    //     breakpoints: {
    //         992: {
    //             slidesPerView: 3,
    //             spaceBetween: 25,
    //         },
    //     }
    // });

    var sliderArticles = new Swiper('#sliderArticles', {
        init: false,
        slidesPerView: 1,
        spaceBetween: 20,
        allowTouchMove: true,
        grabCursor: true,
        scrollbar: {
            el: '.swiper-scrollbar',
            draggable: true
        },
        breakpoints: {
            992: {
                slidesPerView: 3,
                spaceBetween: 25,
            },
        }
    });

    if ($("#sliderArticles").length) {
        sliderArticles.init();
        if($("#sliderArticles .swiper-slide").length <= 3) {
            $("#sliderArticles .swiper-slide").addClass('destroyed');
            sliderArticles.destroy(true, true);
        }
    }

    var sliderEvents = new Swiper('#sliderEvents', {
        init: false,
        slidesPerView: 1,
        spaceBetween: 20,
        allowTouchMove: true,
        grabCursor: true,
        scrollbar: {
            el: '.swiper-scrollbar',
            draggable: true
        },
        breakpoints: {
            992: {
                slidesPerView: 3,
                spaceBetween: 25,
            },
        }
    });

    if ($("#sliderEvents").length) {
        sliderEvents.init();
        if($("#sliderEvents .swiper-slide").length <= 3) {
            $("#sliderEvents .swiper-slide").addClass('destroyed');
            sliderEvents.destroy(true, true);
        }
    }

    var swiperCases = new Swiper('#sliderCases', {
        init: false,
        slidesPerView: 'auto',
        spaceBetween: 20,
        loop: true,
        allowTouchMove: true,
        grabCursor: true
    });

    if ($("#sliderCases").length) {
        swiperCases.init();
    }

    var swiperAwards = new Swiper('#sliderAwards', {
        init: false,
        slidesPerView: 1,
        spaceBetween: 0,
        loop: false,
        allowTouchMove: true,
        grabCursor: true,
        scrollbar: {
            el: '.swiper-scrollbar',
            draggable: true
        },
        breakpoints: {
            1199: {
                slidesPerView: 3,
                spaceBetween: 60,
            },
        }
    });

    if ($("#sliderAwards").length) {
        swiperAwards.init();
        if($("#sliderAwards .swiper-slide").length <= 3) {
            $("#sliderAwards .swiper-slide").addClass('destroyed');
            swiperAwards.destroy(true, true);
        }
    }
});
