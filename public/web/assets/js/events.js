


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
    if($('.flag-show')[0] === undefined){
        $('#checksOutput').fadeOut();
    }

    $('.boxnews.bg__grey__01').fadeOut(); 
    month = $("#month").val();
    year = $("#year").val();
    activity = $("#activity").val();
    office = $("#office").val();
    title = $("#title").val();
    data = { month: month, year: year, title: title, activity: activity, office: office  };
    tags(data);
    $.ajax({
        method: "GET",
        url: getEventUrl,
        data: data,
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


// document.getElementById('#month').addEventListener("change", searchEvents());
/// AQUI 

$(".select-options li").on("click", function() {
    searchEvents();
});


$(document).on("click", ".search_box__button",function (){
    searchEvents();
});


$(document).on("click", ".ion-android-close",function (){
    $(this).each(function(ind,el){
        $(el.parentElement.parentElement).css("display", "none");
        let value  = el.parentElement.dataset.value;
        let select = "select#"+value;
        if (value === 'title'){
            $('#title').val('');

        }else{
         // console.log(select);
        $(select).val($(select+" option:first").val());
        $(select).next(".select-styled").text($("#"+value+" option:selected" ).text());
        }
        $("#checkbox-tag-"+value).removeClass("flag-show");
    });

    searchEvents();
});


function showTag(name,text){
    $("#checkbox-tag-"+name+" span.tag-text").text(text);
    $("#checkbox-tag-"+name).css("display", "flex");
    $("#checkbox-tag-"+name).addClass("flag-show");
}

function tags(data) {
    $("#checksOutput").css("display", "flex");
    
    originalMonth = $("select#month option:first").val();
    originalYear = $("select#year option:first").val();

    // console.log(data);
    
    if (data.month !== originalMonth ){
        text = $('#month option[value="'+data.month+'"]').html();
        showTag('month',text);
    }

    if (data.year !== originalYear){
        text = $('#year option[value="'+data.year+'"]').html();
        showTag('year',text);
    }

    if (data.office !== null ){
        text = $('#office option[value="'+data.office+'"]').html();
        showTag('office',text);
    }

    if (data.activity !== null ){
        text = $('#activity option[value="'+data.activity+'"]').html();
        showTag('activity',text);
    }

    if (data.title !== ''){
        text = $('#title').val();
        showTag('title',text);
    }

}
