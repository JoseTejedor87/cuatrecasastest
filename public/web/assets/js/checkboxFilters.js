/*
// INCLUSIVE FILTERS
*/

// checkboxes selected tags
var $checksOutput = $('#checksOutput');
$checksOutput.hide();

// filter with selects and checkboxes
var $checkBoxes = $('#filterTabsContent input');

// map input values & text to an array
var inclusivesFilters = [];
var inclusivesText = [];

// checkboxes action
$checkBoxes.change( function() {
    checkboxReset();
});

$($checksOutput).on('click', '.checkbox-tag .close-tag', function(element){
    var value = $(this).data( "value" );
    console.log(value);
    $checkBoxes.each(function(i, elem) {
        if($(elem).attr("data-name") == value){
            $(elem).prop( "checked", false );
            checkboxReset();
        }
    });
});

function checkboxReset() {
    // empty checkboxes array
    inclusivesFilters = [];
    inclusivesText = [];

    // inclusive filters from checkboxes
    $checkBoxes.each(function(i, elem) {
        // if checkbox, use value if checked
        if ( elem.checked ) {
            //console.log($(elem).attr("data-name"));
            // checkbox value
            inclusivesFilters.push( $(elem).attr("data-name") );
            // console.log('checkbox value: '+elem.value);

            // checkbox text
            var labelTxt = $(this).next('label').text();
            inclusivesText.push(labelTxt);

            // var
            // console.log('label txt: '+inclusivesText);
        }
    });

    // chekbox tags array
    var filterTags = '';
    var filterText = '';

    // checkboxex autoclose tags

    inclusivesFilters.forEach(function(value) {
        filterTags = filterTags + '<span class="checkbox-tag"><span>'+value.replace('.', '')+'</span><button type="button" class="close-tag" data-value="'+value+'" aria-label="close"><span aria-hidden="true" class="icon ion-android-close"></span></button></span>';
    });

    // tag cloud elements
    var showText    = filterText;
    var showTags    = filterTags;
    var showFull    = filterText + filterTags;


    var valueTags    = filterTags;
    var cleanTags   = '<a href="#" id="deleteFilters">Limpiar filtros</a>';
    var fullTags    = showTags.concat(cleanTags);

    var $accordionFilters = $('#filterTabsContent .collapse');

    // checkboxes & tags & buttons functionality
    if (inclusivesFilters.length <= 0) {
        $checksOutput.hide();

        $('.order_abc').removeClass('active');
        $('.order_pop').removeClass('active');

    } else if (inclusivesFilters.length == 1) {
        $checksOutput.show();
        $checksOutput.html(valueTags);

    } else if (inclusivesFilters.length > 1) {
        $checksOutput.show();
        $checksOutput.html(fullTags);
    }

    // apply button functionality
    $('.apply_button').on('click', function() {
        if($accordionFilters.hasClass('show')) {
            $accordionFilters.collapse('hide');
        }

        // to do...
    });

    // clear all tags & checkboxes
    $('#deleteFilters').on('click', function(e) {
        e.preventDefault();
        inclusivesFilters.forEach(function(tag, index) {
            var value = tag.replace('.', '');
            $('#'+value).prop( "checked", false );
            checkboxReset();
        });

        if($accordionFilters.hasClass('show')) {
            $accordionFilters.collapse('hide');
        }
    });

    // combine inclusive filters
    var filterValue = inclusivesFilters.length ? inclusivesFilters.join(', ') : '*';
}

$(function () {
    // Order by buttons
    $('.order_abc').click(function (e) {
        e.preventDefault();
        $(this).addClass('active');
        if($(this).hasClass('active')) {
            $('.order_pop').removeClass('active');
        }
    });

    $('.order_pop').click(function (e) {
        e.preventDefault();
        $(this).addClass('active');
        if($(this).hasClass('active')) {
            $('.order_abc').removeClass('active');
        }
    });
});
