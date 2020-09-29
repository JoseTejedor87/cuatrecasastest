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
        }
    });

    var swiperCases = new Swiper('#sliderCases', {
        slidesPerView: 'auto',
        spaceBetween: 20,
        loop: true,
        allowTouchMove: true,
        grabCursor: true
    });

    var swiperAwards = new Swiper('#sliderAwards', {
        slidesPerView: 'auto',
        spaceBetween: 0,
        centeredSlides: false,
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
});
