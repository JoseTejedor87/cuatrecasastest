$(function () {
    $.fn.responsiveTabs = function() {
        this.addClass('responsive-tabs'),
        this.append($('<span class="icon4-fletxa_cuatrecasas"></span>')),
        // this.children().children().attr('data-toggle', 'tab'),
        this.on("click", "li > a.active, span.icon4-fletxa_cuatrecasas", function (){
            this.toggleClass('open');
        }.bind(this)), this.on("click", "li > a:not(.active)", function() {
            this.removeClass("open")
        }.bind(this));
    }
});
