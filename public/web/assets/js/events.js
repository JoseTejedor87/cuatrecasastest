


function changeMonth(month){
    $.ajax({
        method: "GET",
        url: getEventUrl,
        data: { month: month },
        beforeSend: function( xhr ) {
            $('.search__component__loader').css("display", "flex").fadeIn('fast');
        }
    })
    .done(function( msg ) {
        $('.search__component__loader').fadeOut();
        $("#eventCalendar").empty();
        newCalendar( msg,month,year);

    })
    .fail(function() {
        console.log(data);
    });

}

$(document).on("click", ".fc-icon__button__prev-button .fc-icon-chevron-left", function(){
    month = parseInt(month)-1;
    changeMonth(month);
});

$(document).on("click", ".fc-icon__button__next-button .fc-icon-chevron-right", function(){
    month = parseInt(month)+1;
    changeMonth(month);
});

function searchEvents(){
    month = $("#month").val();
    year = $("#year").val();
    activity = $("#expertise").val();
    office = $("#place").val();
    title = $("#title").val();
    $.ajax({
        method: "GET",
        url: getEventUrl,
        data: { month: month, year: year, title: title, activity: activity, office: office  },
        beforeSend: function( xhr ) {
            $('.search__component__loader').css("display", "flex").fadeIn('fast');
        }
    })
    .done(function( msg ) {
        $('.search__component__loader').fadeOut();
        $("#eventCalendar").empty();
        newCalendar( msg,month,year);

    })
    .fail(function() {
        //console.log(data);
        console.log({ month: month, year: year, title: title, activity: activity, office: office  });

    });
}    


document.getElementById('#month').addEventListener("change", searchEvents());
/// AQUI 

$(document).on("click", ".search_box__button",function (){
    $('.boxnews.bg__grey__01').fadeOut(); 
    searchEvents();
});

function tags(office) {
    $("#checksOutput").css("display", "flex");

    if(office !== '') {
        var officeText = $("#place option:selected" ).text();
        $("#checkbox-tag-place span.tag-text").text(officeText);
        $("#checkbox-tag-place").css("display", "flex");
    }


    $("#checkbox-tag-practice .ion-android-close").click(function(){
        $("#checkbox-tag-practice").css("display", "none");
        $("select#place").val($("select#place option:first").val());
        $("select#place").next(".select-styled").text($("#place option:selected" ).text());
        ajax();
        alert($("#place option:selected" ).val());
    });

}