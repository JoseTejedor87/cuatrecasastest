function newCalendar(eventosjson, month , year) {
    var events = eventosjson;
    var calendarEl = document.getElementById('eventCalendar');
    var date = new Date();
    var day = date.getDate();

    if(month && month < 10){
        month = `0${month}`;
    }
    if(month && year){
        var dayCalendar = year + '-' +  month + '-01';
    }else{
        if(month || year){
            if(year){
                var dayCalendar = year + '-' +  date.getMonth() + 1 + '-01';
            }
            if(month){
                var dayCalendar = date.getFullYear() + '-' +  month + '-01';
                console.log("entro month");
            }

        }else{
            var month = date.getMonth() + 1;
            if(month < 10){
                month = `0${month}`;
            }
            var dayCalendar = date.getFullYear() + '-' +  month + '-01';
        }

        console.log(month);
    }
    var calendar = new FullCalendar.Calendar(calendarEl, {

        /*
        https://fullcalendar.io/docs/locale
        https://fullcalendar.io/docs/locale-demo
        https://codepen.io/pen/?&editable=true&editors=001
        */

        plugins: [ 'interaction', 'dayGrid', 'list' ],
        defaultView: 'listMonth',
        themeSystem: 'standard',
        // weekNumberCalculation: 'ISO',
        defaultDate: dayCalendar,
        timeZone: 'UTC',
        locale: 'es',
        firstDay: 1,
        columnHeaderFormat: { weekday: 'long' },
        height: 'auto',

        views: {
            dayGridMonth: {
                type: 'dayGrid',
                // buttonText: 'month grid',
                eventLimit: 0,
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

        // Header/Title to custom <DIV>?
        // https://stackoverflow.com/questions/40048176/fullcalendar-header-title-to-custom-div

        header: {
            // left: 'prev,next',
            // left: 'icon__button__prev,icon__button__next',
            left: 'icon__button__list,icon__button__grid',
            center: 'icon__button__prev,title,icon__button__next',
            // right: 'dayGridMonth,listDay,listMonth'
            right: ''
        },

        events: events,
        // console.log()
        viewSkeletonRender: function(info) {
            var headerButtons = calendarEl.querySelectorAll('.fc-button');

            headerButtons.forEach(function(button) {
                if (button.innerText === 'Grid') {
                    button.classList.add('active');
                    button.id = 'gridBot';
                }
                if (button.innerText === 'List') {
                    // button.classList.add('active');
                    button.id = 'listBot';
                }
            });

            var listButton = document.getElementById("listBot");
            var gridButton = document.getElementById("gridBot");

            if(info.view.type === "dayGridMonth") {
                // console.log('dayGridMonth');
                gridButton.classList.add("active");
                listButton.classList.remove("active");
            }

            if(info.view.type === "listMonth" || info.view.type === "listDay") {
                // console.log('listMonth / listDay');
                gridButton.classList.remove("active");
                listButton.classList.add("active");
            }
        },


        datesRender: function(info) {
            var listButton = document.getElementById("listBot");
            var gridButton = document.getElementById("gridBot");
            console.log(info['view']);
            /*
            if(info.view.type === "dayGridMonth") {
                // console.log('dayGridMonth');
                gridButton.classList.add("active");
                listButton.classList.remove("active");
            }

            if(info.view.type === "listMonth" || info.view.type === "listDay") {
                // console.log('listMonth / listDay');
                gridButton.classList.remove("active");
                listButton.classList.add("active");
            }
            */
        },


        // RENDER VIEWS
        eventRender: function (info) {

            // GRID VIEW (MONTH)
            if(info.view.type === "dayGridMonth") {
                info.el.firstChild.innerHTML = '<div class="fc-event-place">'+ info.event.extendedProps.place +'</div><div class="fc-event-sector">'+ info.event.extendedProps.sector +'</div>';
            }

            // LIST VIEW (MONTH & DAY)
            if(info.view.type === "listMonth" || info.view.type === "listDay") {
                // DETAILS
                info.el.firstChild.innerHTML = '<div class="event-place"><a href="#">'+ info.event.extendedProps.place +'</a></div><div class="event-date">'+ info.event.extendedProps.fullDate +'</div><div class="event-time">'+ info.event.extendedProps.fullTime +'</div><div class="event-button"><a href="#" class="doble__arrow__link">'+ info.event.extendedProps.button +'</a></div>';

                // TITLE
                info.el.lastChild.innerHTML = '<div class="event-intro"><a href="'+ info.event.extendedProps.titleURL +'">'+ info.event.title +'</a></div>';

                // SPEAKERS
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

            // DAY BACKGROUND COLOR (WITH EVENT)
            var dayCell = calendarEl.querySelectorAll('.fc-day');

            let date = new Date(info.event.start);
            let day = date.getDate();
            if(day < 10){
                day = `0${day}`;
            }
            let month = date.getMonth() + 1;
            if(month < 10){
                month = `0${month}`;
            }
            let year = date.getFullYear();

            var fullDate = `${year}-${month}-${day}`;
            // console.log(info.event.start);
            dayCell.forEach( function(ele, indice, array) {
                if(fullDate == ele.getAttribute("data-date")){
                    ele.classList.add('day-bg');
                }
             });
        },

        // DATE CLICK
        /*
        dateClick: function(info) {
            console.log('dateStr: ' + info.dateStr);
            console.log('allDay: ' + info.allDay);
            console.log('dayEl: ' + info.dayEl);
            console.log('jsEvent: ' + info.jsEvent);
            console.log('---');

            // alert('Clicked on: ' + info.dateStr);
            console.log('Clicked on: ' + info.dateStr);

            // info.jsEvent.preventDefault();
            var eventDate = info.event.start;
            calendar.changeView('listDay', eventDate);

            var gridFocus = document.getElementById('gridFocus');
            gridFocus.scrollIntoView();
        },
        */


        // EVENT CLICK
        eventClick: function(info) {
            // info.jsEvent.preventDefault();
            var eventDate = info.event.start;
            calendar.changeView('listDay', eventDate);

            var gridFocus = document.getElementById('gridFocus');
            gridFocus.scrollIntoView();
        }

    });

    calendar.render();

};
