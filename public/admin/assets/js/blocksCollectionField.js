
// data-list-selector="#{{ id }}_wrapper"

var blockCollectionManager = {

    addItemTo: function(itemType, $collection) {

        var list = $collection.find(
            '#' + $collection.data('items-list-selector')
        );

        // Try to find the counter of the list or use the length of the list
        // IMPORTANT:
        // We must use the widget-counter property always if exists.
        // The length of the list is not always a correct measure if the "remove" option is allowed.
        const counter = $collection.data('widget-counter') || list.children().length;

        // grab the prototype template
        var protoItem = $collection.data('prototype');
        // replace the "__name__" used in the id and name of the prototype
        // with a number that's unique to your items
        protoItem = protoItem.replace(/__name__/g, counter);

        // create and initialize a new
        // list item using the protoItem
        var newItem = this.initializeItem(
            jQuery(protoItem),
            itemType
        );

        // Adding the newItem to the list.
        newItem.appendTo(list);

        // Scroll to the position of the newItem
        // to improve the usability
        newItem.get(0).scrollIntoView();

        // Add 1 to the widget-counter attribute.
        // Remember, the length of the list cannot be used if the "remove" option is allowed
        list.data('widget-counter', counter + 1);

        this.refreshPositions($collection);
    },

    initializeItem: function($item, type) {
        if (type) {
            // select the current type from the block type selector
            $item.find(".item-type-selector").val(type);
        }
        else {
            type = $item.find(".item-type-selector").val();
        }
        // Hidden unnecessary fields for the current block type
        $item.find(".form-control[data-item-type]").each((position, field)=> {
            var field = jQuery(field);
            const blockType = field.data('item-type');
            let formGroup = field.parent('.form-group');
            if (blockType == type) {
                formGroup.show();
            } else {
                formGroup.hide();
            }
        });

        // apply select2 behaviour over each dropdown selector
        $item.find("select.m-select2").select2();

        return $item;
    },

    refreshPositions: function($collection) {
        let index = 0;
        $collection.find('[name$="[position]"]').each((position, field)=> {
            jQuery(field).val(index);
            index++;
        });
    },

    init: function() {

        const $collections = jQuery('.blocks-collection-field');

        // Iterate throw all the "block collection fields"
        // in the current page, and do the required initializations
        $collections.each((position, collection)=> {

            var $collection = jQuery(collection);

            // Getting the list of item types
            var itemTypesList = jQuery($collection).find('a[data-item-type]');

            // Attaching click handler to each item type
            // in order to invoque the addItemTo method accordingly
            itemTypesList.each((i, itemType)=> {
                jQuery(itemType).on('click' , (event)=> {
                    event.preventDefault();
                    this.addItemTo(
                        jQuery(event.target).data('item-type'),
                        $collection
                    );
                });
            });

            // Initializing each item in the collection
            $collection.find('#' + $collection.data('items-list-selector'))
                .children().each((position, item)=> {
                    let $item = jQuery(item);
                    this.initializeItem($item);
                });

        });


    },


}
jQuery(document).ready(()=> {
    blockCollectionManager.init();
});