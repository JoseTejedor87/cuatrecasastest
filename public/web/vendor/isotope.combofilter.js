/* isotope.combofilter.js
 * Author: Drew Taylor
 *
 * https://stackoverflow.com/questions/7886882/combining-elements-for-filtering-with-isotope
 * https://web.archive.org/web/20170926022727/http://everyonelovessharing.com/misc/isotopedemo/
 */
    var $container;
    var filters = {};
    $(function(){
        $container = $('#filter-container');
        var $filterDisplay = $('#filter-display');
        $container.imagesLoaded( function(){
            $container.isotope({
                itemSelector : '.item',
                sortBy : 'random'
            });
        });
        var numItemsDisp = $('img.item:not(.isotope-hidden)').length;
        $filterDisplay.append( '<span class="filter-label data-counter pull-right">Displaying all&nbsp;'+numItemsDisp+'&nbsp;artworks</span>' );
        var group = '';
        // filter on click
        $('.filter a').click(function(){
            // exit directly if filter already disabled
            if ($(this).hasClass('disabled') ){
                return false;
            }
            var $this = $(this);
            var $optionSet = $(this).parents('.option-set');
            group = $optionSet.attr('data-filter-group');
            // store filter value in object
            var filterGroup = filters[ group ];
            if ( !filterGroup ) {
                filterGroup = filters[ group ] = [];
            }
            var isAll = $this.hasClass('all');
            // reset filter group
            if ( isAll ) {
                Array.prototype.remove = function(from, to) {
                    var rest = this.slice((to || from) + 1 || this.length);
                    this.length = from < 0 ? this.length + from : from;
                    return this.push.apply(this, rest);
                };
                filters[ group ].remove(0,-1)
            }
            var index = $.inArray($this.attr('data-filter-value'), filterGroup );
            if ( !isAll && index === -1 ) {
                // push filter to group
            filters[ group ].push($this.attr('data-filter-value') );
            }
            else if ( !isAll ) {
                // remove filter from group
                filters[ group ].splice( index, 1 );
            }
            // class toggling
            if ($this.hasClass('active') ) {
                $this.removeClass('active');
            }
            else {
                $this.addClass('active');
            }
            // let's do some filtering :>
            var comboFilter = getComboFilter( filters );
            $container.isotope({ filter: comboFilter });
            // gotta check and set those disabled filters!
            var $that = $(this);
            // artist
            $('a.artist:not(.clone)').each(function() {
                var $this = $(this);
                var getVal = $this.attr('data-filter-value');
                var numItems = $('img'+getVal+':not(.isotope-hidden)').length;
                if(!$(this).hasClass('active') && !$that.hasClass('artist') ){
                    if(numItems === 0){
                        $this.addClass('disabled');
                    }
                    else {
                        $this.removeClass('disabled');
                    }
                }
                else if( $this.hasClass('active') && $this.hasClass('disabled') ){
                    $this.removeClass('disabled');
                }
                else if(!$(this).hasClass('active') ) {
                    if(numItems > 0){
                        $this.removeClass('disabled');
                    }
                }
            });
            // medium
            $('a.medium:not(.clone)').each(function() {
                var $this = $(this);
                var getVal = $this.attr('data-filter-value');
                var numItems = $('img'+getVal+':not(.isotope-hidden)').length;
                if(!$(this).hasClass('active') && !$that.hasClass('medium') ){
                    if(numItems === 0){
                        $this.addClass('disabled');
                    }
                    else {
                        $this.removeClass('disabled');
                    }
                }
                else if( $this.hasClass('active') && $this.hasClass('disabled') ){
                    $this.removeClass('disabled');
                }
                else if(!$(this).hasClass('active') ) {
                    if(numItems > 0){
                        $this.removeClass('disabled');
                    }
                }
            });
            // colour
            $('a.colour:not(.clone)').each(function() {
                var $this = $(this);
                var getVal = $this.attr('data-filter-value');
                var numItems = $('img'+getVal+':not(.isotope-hidden)').length;
                if(!$(this).hasClass('active') && !$that.hasClass('colour') ){
                    if(numItems === 0){
                        $this.addClass('disabled');
                    }
                    else {
                        $this.removeClass('disabled');
                    }
                }
                else if( $this.hasClass('active') && $this.hasClass('disabled') ){
                    $this.removeClass('disabled');
                }
                else if(!$(this).hasClass('active') ) {
                    if(numItems > 0){
                        $this.removeClass('disabled');
                    }
                }
            });
            // size
            $('a.size:not(.clone)').each(function() {
                var $this = $(this);
                var getVal = $this.attr('data-filter-value');
                var numItems = $('img'+getVal+':not(.isotope-hidden)').length;
                if(!$(this).hasClass('active') && !$that.hasClass('size') ){
                    if(numItems === 0){
                        $this.addClass('disabled');
                    }
                    else {
                        $this.removeClass('disabled');
                    }
                }
                else if( $this.hasClass('active') && $this.hasClass('disabled') ){
                    $this.removeClass('disabled');
                }
                else if(!$(this).hasClass('active') ) {
                    if(numItems > 0){
                        $this.removeClass('disabled');
                    }
                }
            });
            // subject
            $('a.subject:not(.clone)').each(function() {
                var $this = $(this);
                var getVal = $this.attr('data-filter-value');
                var numItems = $('img'+getVal+':not(.isotope-hidden)').length;
                if(!$(this).hasClass('active') && !$that.hasClass('subject') ){
                    if(numItems === 0){
                        $this.addClass('disabled');
                    }
                    else {
                        $this.removeClass('disabled');
                    }
                }
                else if( $this.hasClass('active') && $this.hasClass('disabled') ){
                    $this.removeClass('disabled');
                }
                else if(!$(this).hasClass('active') ) {
                    if(numItems > 0){
                        $this.removeClass('disabled');
                    }
                }
            });
            // price
            $('a.price:not(.clone)').each(function() {
                var $this = $(this);
                var getVal = $this.attr('data-filter-value');
                var numItems = $('img'+getVal+':not(.isotope-hidden)').length;
                if(!$(this).hasClass('active') && !$that.hasClass('price') ){
                    if(numItems === 0){
                        $this.addClass('disabled');
                    }
                    else {
                        $this.removeClass('disabled');
                    }
                }
                else if( $this.hasClass('active') && $this.hasClass('disabled') ){
                    $this.removeClass('disabled');
                }
                else if(!$(this).hasClass('active') ) {
                    if(numItems > 0){
                        $this.removeClass('disabled');
                    }
                }
            });
            // sale
            $('a.sale:not(.clone)').each(function() {
                var $this = $(this);
                var getVal = $this.attr('data-filter-value');
                var numItems = $('img'+getVal+':not(.isotope-hidden)').length;
                if(!$(this).hasClass('active') && !$that.hasClass('sale') ){
                    if(numItems === 0){
                        $this.addClass('disabled');
                    }
                    else {
                        $this.removeClass('disabled');
                    }
                }
                else if( $this.hasClass('active') && $this.hasClass('disabled') ){
                    $this.removeClass('disabled');
                }
                else if(!$(this).hasClass('active') ) {
                    if(numItems > 0){
                        $this.removeClass('disabled');
                    }
                }
            });
            // update filter display
            var arrLbl = [];
            arrLbl = comboFilter.split('.');
            // before iterating we empty previous display vals
            $filterDisplay.empty();
            // clone method for filter display
            var clone = 'clone';
            var cloneId = 0; // because cloning an id attr just wrong :>
            $('a.active').each(function() {
                cloneId++;
                $(this).clone().appendTo($filterDisplay).attr('id',clone+cloneId).addClass('clone');
            });
            $('a.clone').on('click', function() {
                var that = $(this);
                var parent = that.attr('data-filter-value');
                $('div.filter').find(parent).each(function() {
                    $(this).trigger('click');
                });
            });
            // resolves any outstanding issues with disableds
            // TODO: Find a way around using indexOf this way. Lots of unneccesary overhead.
            if ( comboFilter.indexOf('medium') == -1
                && comboFilter.indexOf('colour') == -1
                && comboFilter.indexOf('size') == -1
                && comboFilter.indexOf('subject') == -1
                && comboFilter.indexOf('price') == -1
                && comboFilter.indexOf('sale') == -1
                && comboFilter.indexOf('artist') > 0 ){
                    $('a.artist:not(.clone)').removeClass('disabled');
            }
            if ( comboFilter.indexOf('artist') == -1
                && comboFilter.indexOf('colour') == -1
                && comboFilter.indexOf('size') == -1
                && comboFilter.indexOf('subject') == -1
                && comboFilter.indexOf('price') == -1
                && comboFilter.indexOf('sale') == -1
                && comboFilter.indexOf('medium') > 0 ){
                    $('a.medium:not(.clone)').removeClass('disabled');
            }
            if ( comboFilter.indexOf('artist') == -1
                && comboFilter.indexOf('medium') == -1
                && comboFilter.indexOf('size') == -1
                && comboFilter.indexOf('subject') == -1
                && comboFilter.indexOf('price') == -1
                && comboFilter.indexOf('sale') == -1
                && comboFilter.indexOf('colour') > 0 ){
                    $('a.colour:not(.clone)').removeClass('disabled');
            }
            if ( comboFilter.indexOf('artist') == -1
                && comboFilter.indexOf('medium') == -1
                && comboFilter.indexOf('colour') == -1
                && comboFilter.indexOf('subject') == -1
                && comboFilter.indexOf('price') == -1
                && comboFilter.indexOf('sale') == -1
                && comboFilter.indexOf('size') > 0 ){
                    $('a.size:not(.clone)').removeClass('disabled');
            }
            if ( comboFilter.indexOf('artist') == -1
                && comboFilter.indexOf('medium') == -1
                && comboFilter.indexOf('size') == -1
                && comboFilter.indexOf('colour') == -1
                && comboFilter.indexOf('price') == -1
                && comboFilter.indexOf('sale') == -1
                && comboFilter.indexOf('subject') > 0 ){
                    $('a.subject:not(.clone)').removeClass('disabled');
            }
            if ( comboFilter.indexOf('artist') == -1
                && comboFilter.indexOf('medium') == -1
                && comboFilter.indexOf('size') == -1
                && comboFilter.indexOf('colour') == -1
                && comboFilter.indexOf('subject') == -1
                && comboFilter.indexOf('sale') == -1
                && comboFilter.indexOf('price') > 0 ){
                    $('a.price:not(.clone)').removeClass('disabled');
            }
            // filter display count
            var numItemsHidden = $('img.item.isotope-hidden').length;
            var numItemsDisp = $('img.item:not(.isotope-hidden)').length;
            var numItemsVisible = $('img.item:not(.isotope-hidden)').length;
            var activeCheck = $('a.active').size();
            if(numItemsHidden !== 0 && numItemsDisp != 1) {
                // clear filter
                $filterDisplay.append( '<a onclick="clearAll();" id="reset-filters" class="pull-right filter-btn">Clear All</a>' );
                $filterDisplay.append( '<span class="filter-label data-counter pull-right">Displaying:&nbsp;'+numItemsDisp+'&nbsp;works&nbsp;&nbsp;</span>' );
            }
            else if (numItemsHidden !== 0 && numItemsDisp === 1) {
                $filterDisplay.append( '<a onclick="clearAll();" id="reset-filters" class="pull-right filter-btn">Clear All</a>' );
                $filterDisplay.append( '<span class="filter-label data-counter pull-right">Displaying:&nbsp;'+numItemsDisp+' work&nbsp;&nbsp;</span>' );
            }
            else if (numItemsHidden === 0 && activeCheck > 0) {
                $filterDisplay.append( '<a onclick="clearAll();" id="reset-filters" class="pull-right filter-btn">Clear All</a>' );
                $filterDisplay.append( '<span class="filter-label data-counter pull-right">Displaying all artworks ('+numItemsDisp+')&nbsp;&nbsp;</span>' );
            }
            else if (numItemsHidden === 0 && activeCheck === 0) {
                $filterDisplay.append( '<span class="filter-label data-counter pull-right">Displaying all&nbsp;'+numItemsDisp+'&nbsp;artworks</span>' );
            }
            else{ // strictly fall-back - this should never get triggered
                $filterDisplay.append( '<a onclick="clearAll();" id="reset-filters" class="pull-right filter-btn">Clear All</a>' );
                $filterDisplay.append( '<span class="filter-label data-counter pull-right">Displaying:&nbsp;'+numItemsDisp+'&nbsp;works&nbsp;&nbsp;</span>' );
                //console.log("else was triggered"); // <-- uncomment for debugging
            }
        });
    });
    function getComboFilter( filters ) {
        var i = 0;
        var comboFilters = [];
        var message = [];
        for ( var prop in filters ) {
            message.push( filters[ prop ].join(' ') );
            var filterGroup = filters[ prop ];
            // skip to next filter group if it doesn't have any values
            if ( !filterGroup.length ) {
                continue;
            }
            if ( i === 0 ) {
                // copy to new array
                comboFilters = filterGroup.slice(0);
            }
            else {
                var filterSelectors = [];
                // copy to fresh array
                var groupCombo = comboFilters.slice(0); // [ A, B ]
                // merge filter Groups
                for (var k=0, len3 = filterGroup.length; k < len3; k++) {
                    for (var j=0, len2 = groupCombo.length; j < len2; j++) {
                        filterSelectors.push( groupCombo[j] + filterGroup[k] ); // [ 1, 2 ]
                    }
                }
                // apply filter selectors to combo filters for next group
                comboFilters = filterSelectors;
            }
            i++;
        }
        comboFilters.sort();
        var comboFilter = comboFilters.join(', ');
        return comboFilter;
    }
    function clearAll(){
        $('a.active').trigger('click');
        $('#filter-display').empty();
        var numItemsDisp = $('img.item:not(.isotope-hidden)').length;
        $('#filter-display').append( '<span class="filter-label data-counter pull-right">Displaying all&nbsp;'+numItemsDisp+'&nbsp;artworks</span>' );
    }
/*
     FILE ARCHIVED ON 07:35:10 Mar 10, 2016 AND RETRIEVED FROM THE
     INTERNET ARCHIVE ON 16:50:15 May 19, 2020.
     JAVASCRIPT APPENDED BY WAYBACK MACHINE, COPYRIGHT INTERNET ARCHIVE.

     ALL OTHER CONTENT MAY ALSO BE PROTECTED BY COPYRIGHT (17 U.S.C.
     SECTION 108(a)(3)).
*/
/*
playback timings (ms):
  load_resource: 73.18
  esindex: 0.017
  exclusion.robots: 0.213
  CDXLines.iter: 15.566 (3)
  LoadShardBlock: 128.64 (3)
  captures_list: 154.713
  PetaboxLoader3.resolve: 37.057
  PetaboxLoader3.datanode: 128.577 (4)
  RedisCDXSource: 7.297
  exclusion.robots.policy: 0.198
*/
