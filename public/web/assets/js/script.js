var web={};web.global={init:function(){web.global.stickyMenu(),web.global.mobileNav(),web.global.loadMiscell()},stickyMenu:function(){$("#about-nav .dropdown-menu").bind("mouseover",function(e){$(this).prev(".nav-link").addClass("active")}).bind("mouseleave",function(e){$(this).prev(".nav-link").removeClass("active")}),$(".lang-region-menu").hide(),$(".lang-region-toggle").click(function(e){e.preventDefault(),$(this).next(".lang-region-menu").show()}),$(".menu-close.langs").click(function(e){e.preventDefault(),$(this).parent().parent(".lang-region-menu").hide()});var t=0,n=$(window),a=$("#navDesktop #secondaryNav"),o=$("#navDesktop #secondaryNav .navbar-brand"),l=$("#navDesktop #secondaryNav .dropdown-menu"),s=$("#navDesktop #primaryNav #aboutNav .dropdown-menu"),i=$("#navDesktop #userNav"),c=$("body.desktop header"),r=$("#navDesktop .lang-region-menu");n.on("scroll",function(){var e=n.scrollTop();a.toggleClass("stickyNav",t<e),o.toggleClass("stickyNav",t<e),l.removeClass("show",t<e),s.removeClass("show",t<e),i.toggleClass("stickyNav",t<e),c.toggleClass("stickyNav",t<e),r.hide(),t=e}),$("#hamburgerButton").click(function(e){e.preventDefault(),a.toggleClass("stickyNav")})},mobileNav:function(){function e(){var e=$("body");window.matchMedia("(max-width: 1199px)").matches?(e.addClass("mobile"),e.removeClass("desktop")):(e.addClass("desktop"),e.removeClass("mobile"))}$(function(){e()}),$(window).resize(function(){e()}),$("#navMobileWrapper .navbar-toggler.navbar-open").click(function(e){e.preventDefault(),$("#navMobileWrapper .navbar-brand").addClass("off")}),$("#navMobileWrapper .navbar-toggler.navbar-close").click(function(e){e.preventDefault(),$("#navMobileWrapper .navbar-brand").removeClass("off"),$("#navMobile .collapse").collapse("hide")})},loadMiscell:function(){$(".button__bookmark").click(function(e){e.preventDefault(),$(this).toggleClass("button__bookmark--on")}),$(".no-link").click(function(e){e.preventDefault()})},customSelects:function(){$(".custom__select").each(function(){var t=$(this),e=$(this).children("option").length;t.addClass("select-hidden"),t.wrap('<div class="select"></div>'),t.after('<div class="select-styled"></div>');var n=t.next("div.select-styled");n.text(t.children("option:selected").text());for(var a=$("<ul/>",{class:"select-options"}).insertAfter(n),o=0;o<e;o++)$("<li/>",{text:t.children("option").eq(o).text(),rel:t.children("option").eq(o).val()}).appendTo(a);var l=a.children("li");n.click(function(e){e.stopPropagation(),$("div.select-styled.active").not(this).each(function(){$(this).removeClass("active").next("ul.select-options").hide()}),$(this).toggleClass("active").next("ul.select-options").toggle()}),l.click(function(e){e.stopPropagation(),n.text($(this).text()).removeClass("active"),t.val($(this).attr("rel")),a.hide()}),$(document).click(function(){n.removeClass("active"),a.hide()})})},testimonials:function(){$(".testimonials__item").hover(function(){$(this).hasClass("selected")||($(".selected").removeClass("selected"),$(this).addClass("selected"))})}},web.global.init();