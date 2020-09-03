$(function () {

    // hide search elements
    $('#filterOrderby').hide();
    $('#applyFilters').hide();

    // Order by buttons
    $('#abcOder').click(function (e) {
        e.preventDefault();
        $(this).addClass('active');
        if($(this).hasClass('active')){
            $('#popOder').removeClass('active');
        }
    });

    $('#popOder').click(function (e) {
        e.preventDefault();
        $(this).addClass('active');
        if($(this).hasClass('active')){
            $('#abcOder').removeClass('active');
        }
    });


    // function checkFiltersAttr() {

    //     $filterGroup.each( function( i, elem ) {

    //         var theAttr = $(this).attr('aria-expanded');
    //         tab_attribs.push( theAttr );
    //         console.log('attr: '+theAttr);

    //         var labelTxt = $(this).text();
    //         console.log('txt: '+labelTxt);

    //         if ( theAttr == true ) {
    //             console.log('TRUE');
    //         } else {
    //             console.log('FALSE');
    //         }
    //     });
    // }



    var $filterGroup = $('#filterTabs .nav-link');
    var tab_attribs = [];

    $($filterGroup).click(function (e) {
        e.preventDefault();
        console.log('checkFiltersAttr');
        // checkFiltersAttr();

        var theAttr = $(this).attr('aria-expanded');
        console.log('this tab_attribs: '+theAttr);


        if(theAttr === 'true') {
            $('#filterOrderby').fadeOut();
            $('#applyFilters').fadeOut();
        } else {
            $('#filterOrderby').fadeIn();
            $('#applyFilters').fadeIn();
        }
    });


});


/*
// ISOTOPE INCLUSIVE FILTERS
*/

// init Isotope
var $container = $('.grid-masonry').isotope({
    itemSelector: '.grid-item',
    percentPosition: true,
    masonry: {
        columnWidth: '.grid-sizer'
    }
});

// checkboxes selected tags
var $checksOutputTags = $('#checksOutput');

// filter with selects and checkboxes
var $checkBoxes = $('#filterTabsContent input');

// map input values to an array
var inclusivesFilters = [];

// checkboxes action
$checkBoxes.change( function() {
    checkboxReset();
});

$($checksOutputTags).on('click', '.checkbox-tag .close-tag', function(element){
    var value = $(this).data( "value" ).replace('.', '');
    $('#'+value).prop( "checked", false );

    // var txt = $(this).next('label').text();
    // console.log(txt);

    checkboxReset();
});


function checkboxReset() {
    // empty checkboxes array
    inclusivesFilters = [];

    // inclusive filters from checkboxes
    $checkBoxes.each( function( i, elem ) {
        // if checkbox, use value if checked
        if ( elem.checked ) {
            inclusivesFilters.push( elem.value );
            // console.log('checkbox value: '+elem.value);

            var labelTxt = $(this).next('label').text();
            // console.log('label txt: '+labelTxt);
        }
    });

    // chekbox tags array
    var filterTags = '';

    // checkboxex autoclose tags
    inclusivesFilters.forEach(function(tag, index) {
        filterTags = filterTags + '<span class="checkbox-tag">'+tag.replace('.', '')+'<button type="button" class="close-tag" data-value="'+tag+'" aria-label="close"><span aria-hidden="true" class="icon ion-android-close"></span></button></span>';
    });

    // tag cloud elements
    var showTags    = filterTags;
    var cleanTags   = '<a href="#" id="deleteFilters">Limpiar filtros</a>';
    var fullTags    = showTags.concat(cleanTags);


    // print all tags + clean all + order by + button
    if (inclusivesFilters.length == 0) {
        $checksOutputTags.fadeOut();
        $('#filterOrderby').fadeOut();
        $('#applyFilters').fadeOut();

    } else if (inclusivesFilters.length == 1) {
        $checksOutputTags.fadeIn();
        $('#filterOrderby').fadeIn();
        $('#applyFilters').fadeIn();
        $checksOutputTags.html(showTags);

    } else if (inclusivesFilters.length > 1) {
        $checksOutputTags.fadeIn();
        $('#filterOrderby').fadeIn();
        $('#applyFilters').fadeIn();
        $checksOutputTags.html(fullTags);

    } else {
        $checksOutputTags.fadeOut();
        $('#filterOrderby').fadeIn();
        $('#applyFilters').fadeIn();
        $checksOutputTags.html(fullTags);
    }

    // action buttons
    $('#applyFilters').on('click', function(e){
        e.preventDefault();
    });

    $('#deleteFilters').on('click', function(e){
        e.preventDefault();
        inclusivesFilters.forEach(function(tag, index) {
            var value = tag.replace('.', '');
            $('#'+value).prop( "checked", false );
            checkboxReset();
        });
    });

    // combine inclusive filters
    var filterValue = inclusivesFilters.length ? inclusivesFilters.join(', ') : '*';

    // main isotope funcionallity
    $container.isotope({ filter: filterValue })
}
