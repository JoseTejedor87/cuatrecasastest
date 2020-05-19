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
                allDay: true,
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
                allDay: true,
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
                allDay: true,
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
                allDay: true,
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
                allDay: true,
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
                allDay: true,
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
                allDay: true,
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
                allDay: true,
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
                allDay: true,
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
                allDay: true,
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
                allDay: true,
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
                allDay: true,
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
                allDay: true,
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
                allDay: true,
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
                allDay: true,
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
                allDay: true,
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
                allDay: false,
                sector: 'Consumo y retail',
                place: 'Barcelona',
                placeLink: 'https://www.cuatrecasas.com/es/oficina/barcelona.html',
                fullDate: '27 mayo',
                fullTime: '10.30 — 14.30',
                button: 'Inscribirme',
            },
        ],

        viewSkeletonRender: function(info) {
            var headerButtons = calendarEl.querySelectorAll('.fc-button');
            headerButtons.forEach(function(button) {
                if (button.innerText === 'Grid') {
                    button.classList.add('active');
                    button.id = 'gridBot';
                }
                if (button.innerText === 'List') {
                    button.id = 'listBot';
                }
            });
        },


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
                info.el.firstChild.innerHTML = '<div>123</div><div class="event-place"><a href="'+ info.event.extendedProps.placeLink +'">'+ info.event.extendedProps.place +'</a></div><div class="event-date">'+ info.event.extendedProps.fullDate +'</div><div class="event-time">'+ info.event.extendedProps.fullTime +'</div><div class="event-button"><button type="button" class="doble__arrow__button">'+ info.event.extendedProps.button +'</button></div>';

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


            var dayNumber = calendarEl.querySelectorAll('.fc-day');

            var allDay = info.event.allDay;
            // console.log('allDay: '+allDay);

            if(allDay === true) {
                // console.log('allDay true');
            }


            var fc_bg = calendarEl.getElementsByClassName('.fc-bg');
            console.log('fc_bg: '+fc_bg);

            dayNumber.forEach( function(ele, indice, array) {
                // console.log("En el índice " + indice + " hay este valor: " + ele.getAttribute("data-date"));


                // .fc-bg > .fc-day.fc-widget-content

                // calendarEl.getElementsByClassName('.fc-bg').classList.add('probando-day');

                if(allDay === true) {
                    // console.log('allDay true');
                    // ele.classList.add('probando-day');
                    ele.closest(".fc-bg").classList.add('probando-table');
                    ele.classList.add('probando-day');
                }




                // ele.closest('table').classList.add('probando-table');



                console.log('ele.closest: '+ele.closest(".fc-bg"));
                console.log('ele.nodeName: '+ele.nodeName);

                // console.log('ele: '+ele.closest('table'));
                // calendarEl.querySelectorAll('.fc-event-container')[indice].classList.add('probando3');
            });

            // var dayCell = calendarEl.querySelectorAll('.fc-day');

            console.log('+++');

        },

        // EVENT CLICK
        eventClick: function(info) {
            // info.jsEvent.preventDefault();
            calendar.changeView('listDay', eventDate);

            // var gridFocus = document.getElementById('gridFocus');
            // gridFocus.scrollIntoView();

        }

    });

    calendar.render();

});
