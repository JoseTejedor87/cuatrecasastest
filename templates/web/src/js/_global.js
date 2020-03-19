// Global
web.global = {
    init: function(){ // Load all global functions here
        // web.global.loadMiscell();
        // web.global.homeSelect();
        // web.global.carouselAwards();
        // web.global.sliderCases();
        // web.global.loadCalendar();
        // web.global.loadHeader();
    },

    loadMiscell: function(){
        // Miscell Stuff
        $(document).ready(function () {
            $('.button__bookmark').click(function(e){
                e.preventDefault();
                $(this).toggleClass('on');
            });

            $('.no-link').click(function(e){
                e.preventDefault();
            });
        });
    },

    homeSelect: function(){
        /*
        Reference: http://jsfiddle.net/BB3JK/47/
        */
        $('.custom-select').each(function(){
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
                center: true,
                items: 3,
                loop: false,
                margin: 30,
                nav: true,
                navText:["<div class='nav-btn prev-slide'><i class='icon ion-ios-arrow-left'></i></div>","<div class='nav-btn next-slide'><i class='icon ion-ios-arrow-right'></i></div>"],
                dots: false
            })
        });
    },

    carouselAwards: function(){
        $(document).ready(function () {
            var swiperCarousel = new Swiper ('#carouselAwards', {
                slidesPerView: 4,
                grabCursor: true,
                loop: true,
                scrollbar: {
                    el: '.swiper-scrollbar',
                    draggable: true,
                    dragSize: 200,
                }
            });
        });
    },

    sliderCases: function(){
        var swiperSlider = new Swiper ('#sliderCases', {
            navigation: {
                nextEl: '.swiper-button-next',
                prevEl: '.swiper-button-prev',
            }
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

            plugins: [ 'dayGrid' ],
            defaultView: 'dayGridMonth',

            locale: 'es',
            weekNumberCalculation: 'ISO',
            // firstDay: 1,
            // timeZone: 'UTC',

            columnHeaderFormat: { weekday: 'long' },

            header: {
              left: 'prev',
              center: 'title',
              right: 'next'
            },

            /*
            https://fullcalendar.io/docs/height
            https://fullcalendar.io/docs/full-height-demo
            */
            height: 'auto',


            /*
            eventLimit: true, // for all non-TimeGrid views
            views: {
              timeGrid: {
                eventLimit: 2 // adjust to 6 only for timeGridWeek/timeGridDay
              }
            },
            */

            // events: 'https://fullcalendar.io/demo-events.json',
            /*
            https://fullcalendar.io/docs/event-object
            */
            events: [
              {
                "url":"https:\/\/www.cuatrecasas.com\/",
                // "title":"\"Quick fixes\": análisis detallado de las novedades en el régimen...",
                "title":"\"Quick fixes\": análisis detallado de las novedades en el régimen...",
                "start":"2020-02-01",
                place: 'Barcelona'
              },
              {
                "url":"https:\/\/www.cuatrecasas.com\/",
                "title":"Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua",
                "start":"2020-02-04",
                "end":"2020-02-06",
                place: 'Lisboa'
              },
              {
                "url":"https:\/\/www.cuatrecasas.com\/",
                // "title":"\"Quick fixes\": análisis detallado de las novedades en el régimen...",
                "title":"\"Quick fixes\": análisis detallado de las novedades en el régimen...",
                "start":"2020-02-07"
              },
              {
                "url":"https:\/\/www.cuatrecasas.com\/",
                "title":"Conference lorem ipsum dolor sit amet, consectetur adipiscing elit",
                "start":"2020-02-12",
                "end":"2020-02-14",
                place: 'Madrid'
              },
              {
                "url":"https:\/\/www.cuatrecasas.com\/",
                "title":"Meeting lorem ipsum dolor sit amet, consectetur adipiscing elit",
                "start":"2020-02-13T10:30:00+00:00",
                "end":"2020-02-13T12:30:00+00:00",
                place: 'Barcelona'
              },
              {
                "url":"https:\/\/www.cuatrecasas.com\/",
                "title":"Meeting",
                "start":"2020-02-17T10:00:00+00:00",
                "end":"2020-02-18T10:00:00+00:00",
                place: 'Barcelona'
              },
              {
                "url":"https:\/\/www.cuatrecasas.com\/",
                "title":"Lunch",
                "start":"2020-02-17T12:00:00+00:00",
                place: 'Barcelona'
              },
              {
                "url":"https:\/\/www.cuatrecasas.com\/",
                "title":"Vestibulum lorem sed risus ultricies tristique nulla aliquet enim pulvinar sapien condimentum lacinia quis",
                "start":"2020-02-26",
                "end":"2020-02-29",
                place: 'Barcelona'
              },
              {
                "url":"https:\/\/www.cuatrecasas.com\/",
                "title":"Meeting lorem ipsum dolor sit amet, consectetur adipiscing elit",
                "start":"2020-03-10T10:30:00+00:00",
                "end":"2020-03-12T12:30:00+00:00",
                place: 'Barcelona'
              },
            ],

            eventRender: function (info) {
              /*
              https://stackoverflow.com/questions/56280133/how-to-add-an-image-to-an-event-on-vue-fullcalendar-imageurl-returns-undefined
              https://github.com/fullcalendar/fullcalendar/issues/2919
              */
              if (info.event.extendedProps.place) {
                  info.el.firstChild.innerHTML = "<div class=\"fc-title\">"+ info.event.title +"</div><div class=\"fc-place\">"+ info.event.extendedProps.place +"</div>";
              }
            }

            // eventRender: function(info) {
            //   if (info.event.extendedProps.status === 'done') {

            //     // Change background color of row
            //     info.el.style.backgroundColor = 'red';

            //     // Change color of dot marker
            //     var dotEl = info.el.getElementsByClassName('fc-event-dot')[0];
            //     if (dotEl) {
            //       dotEl.style.backgroundColor = 'white';
            //     }
            //   }
            // }
          });

          calendar.render();
        });
    }
}

// Run the global stuff
web.global.init();
