$(function () {

    // hide search elements
    $('#filterOrderby').hide();
    $('#applyFilters').hide();

    // ordery by buttons
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

    // AUTO CLOSE ACTIVE TAB CONTENT
    // $('#filter-pills-tab a').click(function (e) {
    $('a[data-toggle="tab"]').click(function (e) {
        e.preventDefault();

        // to do...

        // console.log($(this).hasClass('active'));

        $('#filterOrderby').show();

        if($(this).hasClass('active')){

            console.log('YES active');

            $(this).removeClass('active');
            $(this).addClass('probando123');

            $($(this).attr('aria-selected', 'false'));

            // $($(this).attr('href')).removeClass('active');

        } else {

            // console.log('NO active');
            e.preventDefault();

            // $($(this).attr('aria-selected', 'true'));
            $(this).removeClass('probando123');

            // e.preventDefault();
            // $(this).tab('show');
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

/*
* Getting multiple checked checkbox labels into an array
https://stackoverflow.com/questions/22907422/jquery-checked-checkboxes-sibling-text
https://stackoverflow.com/questions/30182001/getting-multiple-checked-checkbox-labels-into-an-array

$('.submit').on("click", function (e) {
    //got all checked checkboxes into 'children'.
    var array = $("div.category-panel input:checked").next('label').map(function(){
        return $(this).text();
    }).get();

    //Show the array.
    for (var i = 0; i < array.length; i++) {
        console.log(array[i]);
    }
});
 */

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
    var tagCloud    = showTags.concat(cleanTags);


    // print all tags & buttons
    if (inclusivesFilters.length == 0) {
        $checksOutputTags.hide();
        $('#applyFilters').hide();

    } else if (inclusivesFilters.length == 1) {
        $checksOutputTags.show();
        $('#applyFilters').show();
        $checksOutputTags.html(showTags);

    } else if (inclusivesFilters.length > 1) {
        $checksOutputTags.show();
        $('#applyFilters').show();
        $checksOutputTags.html(tagCloud);

    } else {
        $checksOutputTags.hide();
        $('#applyFilters').show();
        $checksOutputTags.html(tagCloud);
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
