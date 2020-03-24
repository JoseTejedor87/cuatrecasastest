jQuery(document).ready(function () {
    jQuery('.remove-item').click(function (e) {
        jQuery(this).parents('.ordered-collection-item').addClass('for-deletion');
        jQuery(this).parent().find('.form-group input[type=checkbox]').attr('checked', true);
    });
    jQuery('.undo-remove-item').click(function (e) {
        jQuery(this).parents('.ordered-collection-item').removeClass('for-deletion');
        jQuery(this).parent().find('.form-group input[type=checkbox]').attr('checked', false);
    });
    jQuery('.add-item').click(function (e) {
        var list = jQuery(jQuery(this).attr('data-list-selector'));
        // Try to find the counter of the list or use the length of the list
        var counter = list.data('widget-counter') || list.children().length;

        // grab the prototype template
        var newWidget = list.attr('data-prototype');
        // replace the "__name__" used in the id and name of the prototype
        // with a number that's unique to your items
        newWidget = newWidget.replace(/__name__/g, counter);
        // Increase the counter
        counter++;
        // And store it, the length cannot be used if deleting widgets is allowed
        list.data('widget-counter', counter);

        // create a new list element
        var newElement = jQuery(newWidget);

        // apply select2 behaviour over each dropdown selector
        newElement.find("select.m-select2").select2();
        // and add it to the list.
        newElement.appendTo(list.find(
            '#'+list.data('child-field-id')
        ));
    });
});