$(function () {
    var swiperGeneral = new Swiper('.slider__general', {
        slidesPerView: 1,
        spaceBetween: 20,
        allowTouchMove: false,
        grabCursor: false,
        scrollbar: {
            el: '.swiper-scrollbar',
            draggable: true
        },
        breakpoints: {
            992: {
                slidesPerView: 3,
                spaceBetween: 25,
            },
        },
        // on: {
        //     resize: function () {
        //         alert('rezize');
        //         swiperGeneral.init();
        //     },
        // }
    });

    // swiperGeneral.on('resize', function() {
    //     alert('rezize3');
    //     swiperGeneral.init();
    // });

    var swiperCases = new Swiper('#sliderCases', {
        slidesPerView: 'auto',
        spaceBetween: 20,
        loop: true,
        allowTouchMove: true,
        grabCursor: true
    });

    var swiperAwards = new Swiper('#sliderAwards', {
        slidesPerView: 1,
        spaceBetween: 0,
        loop: false,
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

    if($("#sliderAwards .swiper-slide").length <= 3) {
        $("#sliderAwards .swiper-slide").addClass('destroyed');
        swiperAwards.destroy(true, true);
    }
});
