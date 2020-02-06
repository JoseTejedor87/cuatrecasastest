jQuery(document).ready(function () {
    jQuery('.delete-attachment-button').click(function (e) {
        jQuery(this).parents('.vich-attachment-item').addClass('for-deletion');
        jQuery(this).parent().find('.form-group input[type=checkbox]').attr('checked', true);
    });
    jQuery('.undo-delete-attachment-button').click(function (e) {
        jQuery(this).parents('.vich-attachment-item').removeClass('for-deletion');
        jQuery(this).parent().find('.form-group input[type=checkbox]').attr('checked', false);
    });
    jQuery('.add-another-collection-widget').click(function (e) {
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

        // create a new list element, attach to it an onChange event
        // and add it to the list.
        var newElem = jQuery(list.attr('data-widget-tags')).html(newWidget);
        newElem.find('.custom-file-input').on("change",function() {
            var t=$(this).val();
            $(this).next(".custom-file-label").addClass("selected").html(t)
        });
        newElem.appendTo(list);
    });
});