$(function () {
    var swiperGeneral = new Swiper ('.slider__general', {
        slidesPerView: 3,
        spaceBetween: 25,
        allowTouchMove: true,
        grabCursor: true,
        scrollbar: {
            el: '.swiper-scrollbar',
            draggable: true
        }
    });
});
