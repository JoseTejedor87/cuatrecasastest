var web={};web.global={init:function(){web.global.loadMiscell()},stickyMenu:function(){var t=0,a=$(window),s=$("#navDesktop #secondaryNav"),l=$("#navDesktop #secondaryNav .navbar-brand"),r=$("#navDesktop header");a.on("scroll",function(){var e=a.scrollTop();s.toggleClass("stickyNav",t<e),l.toggleClass("stickyNav",t<e),r.toggleClass("stickyNav",t<e),t=e})},stickyMenuOld:function(){var t,a=0,s=$("header").outerHeight();$(window).scroll(function(e){t=!0}),setInterval(function(){t&&(function(){var e=$(this).scrollTop();if(Math.abs(a-e)<=5)return;a<e&&s<e?$("header").removeClass("nav-down").addClass("nav-up"):e+$(window).height()<$(document).height()&&$("header").removeClass("nav-up").addClass("nav-down");a=e}(),t=!1)},250)},loadMiscell:function(){$(function(){$(".button__bookmark").click(function(e){e.preventDefault(),$(this).toggleClass("on")}),$(".no-link").click(function(e){e.preventDefault()})})},showMoreLess:function(){$(".read-more").each(function(){4<$(this).children("p").length&&($(this).children("p:lt(3)").show(),$(this).append('<button type="button" class="doble__arrow__button loadMore">Ver más</button>'))}),$(".read-more").on("click",".loadMore",function(){$(this).parent(".read-more").children("p").slideDown(),$(this).removeClass("loadMore").addClass("loadLess").text("Ver menos"),$(".doble__arrow__button").blur()}),$(".read-more").on("click",".loadLess",function(){$(this).parent(".read-more").children("p:gt(2)").slideUp(),$(this).removeClass("loadLess").addClass("loadMore").text("Ver más"),$(".doble__arrow__button").blur()})},toggleMoreInfo:function(){$("button").on("click",function(){$("#"+$(this).data("id")).slideToggle()})},toggleTextButton:function(){$(".toggle-filters, .button-favorite, .button-calendar").click(function(e){e.preventDefault(),$(this).toggleClass("on");var t=$(this);t.text()==t.data("text-original")?t.text(t.data("text-swap")):t.text(t.data("text-original"))})},lawyerResults:function(){$(".lawyer__search__wrapper__loader").css("display","flex").hide(),$(".list").click(function(e){e.preventDefault(),$(".icon__button.grid").removeClass("active"),$(this).addClass("active"),$(".lawyer__search__wrapper__loader").fadeIn("fast"),$(".lawyer__search__wrapper").addClass("loading"),$(".lawyer__search__wrapper").removeClass("lawyer__search__wrapper--grid"),$(".lawyer__search__wrapper").addClass("lawyer__search__wrapper--list"),setTimeout(function(){$(".lawyer__search__wrapper__loader").fadeOut(),$(".lawyer__search__wrapper").removeClass("loading")},600)}),$(".grid").click(function(e){e.preventDefault(),$(".icon__button.list").removeClass("active"),$(this).addClass("active"),$(".lawyer__search__wrapper__loader").fadeIn("fast"),$(".lawyer__search__wrapper").addClass("loading"),$(".lawyer__search__wrapper").removeClass("lawyer__search__wrapper--list"),$(".lawyer__search__wrapper").addClass("lawyer__search__wrapper--grid"),setTimeout(function(){$(".lawyer__search__wrapper__loader").fadeOut(),$(".lawyer__search__wrapper").removeClass("loading")},600)})},customSelects:function(){$(".custom__select").each(function(){var t=$(this),e=$(this).children("option").length;t.addClass("select-hidden"),t.wrap('<div class="select"></div>'),t.after('<div class="select-styled"></div>');var a=t.next("div.select-styled");a.text(t.children("option:selected").text());for(var s=$("<ul/>",{class:"select-options"}).insertAfter(a),l=0;l<e;l++)$("<li/>",{text:t.children("option").eq(l).text(),rel:t.children("option").eq(l).val()}).appendTo(s);var r=s.children("li");a.click(function(e){e.stopPropagation(),$("div.select-styled.active").not(this).each(function(){$(this).removeClass("active").next("ul.select-options").hide()}),$(this).toggleClass("active").next("ul.select-options").toggle()}),r.click(function(e){e.stopPropagation(),a.text($(this).text()).removeClass("active"),t.val($(this).attr("rel")),s.hide()}),$(document).click(function(){a.removeClass("active"),s.hide()})})},sliderHome:function(){new Swiper("#sliderHome",{slidesPerView:1,spaceBetween:0,loop:!0,speed:800,allowSlidePrev:!1,allowTouchMove:!1,autoplay:{delay:5e3},navigation:{nextEl:".home__preview__button"}})},sliderNews:function(){new Swiper("#sliderNews",{slidesPerView:3,spaceBetween:25,scrollbar:{el:".swiper-scrollbar",draggable:!0}})},sliderCases:function(){new Swiper("#sliderCases",{slidesPerView:"auto",spaceBetween:20,grabCursor:!0})},sliderAwards:function(){new Swiper("#sliderAwards",{slidesPerView:"auto",spaceBetween:60,centeredSlides:!0,loop:!0,grabCursor:!0})},testimonials:function(){$(".testimonials__item").hover(function(){$(this).hasClass("selected")||($(".selected").removeClass("selected"),$(this).addClass("selected"))})},loadCalendar:function(){document.addEventListener("DOMContentLoaded",function(){var e=document.getElementById("eventCalendar");new FullCalendar.Calendar(e,{plugins:["dayGrid"],defaultView:"dayGridMonth",locale:"es",weekNumberCalculation:"ISO",columnHeaderFormat:{weekday:"long"},header:{left:"prev",center:"title",right:"next"},height:"auto",events:[{url:"https://www.cuatrecasas.com/",title:'"Quick fixes": análisis detallado de las novedades en el régimen...',start:"2020-02-01",place:"Barcelona"},{url:"https://www.cuatrecasas.com/",title:"Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua",start:"2020-02-04",end:"2020-02-06",place:"Lisboa"},{url:"https://www.cuatrecasas.com/",title:'"Quick fixes": análisis detallado de las novedades en el régimen...',start:"2020-02-07"},{url:"https://www.cuatrecasas.com/",title:"Conference lorem ipsum dolor sit amet, consectetur adipiscing elit",start:"2020-02-12",end:"2020-02-14",place:"Madrid"},{url:"https://www.cuatrecasas.com/",title:"Meeting lorem ipsum dolor sit amet, consectetur adipiscing elit",start:"2020-02-13T10:30:00+00:00",end:"2020-02-13T12:30:00+00:00",place:"Barcelona"},{url:"https://www.cuatrecasas.com/",title:"Meeting",start:"2020-02-17T10:00:00+00:00",end:"2020-02-18T10:00:00+00:00",place:"Barcelona"},{url:"https://www.cuatrecasas.com/",title:"Lunch",start:"2020-02-17T12:00:00+00:00",place:"Barcelona"},{url:"https://www.cuatrecasas.com/",title:"Vestibulum lorem sed risus ultricies tristique nulla aliquet enim pulvinar sapien condimentum lacinia quis",start:"2020-02-26",end:"2020-02-29",place:"Barcelona"},{url:"https://www.cuatrecasas.com/",title:"Meeting lorem ipsum dolor sit amet, consectetur adipiscing elit",start:"2020-03-10T10:30:00+00:00",end:"2020-03-12T12:30:00+00:00",place:"Barcelona"}],eventRender:function(e){e.event.extendedProps.place&&(e.el.firstChild.innerHTML='<div class="fc-title">'+e.event.title+'</div><div class="fc-place">'+e.event.extendedProps.place+"</div>")}}).render()})}},web.global.init();