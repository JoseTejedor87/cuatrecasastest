// Initialization of every single input SELECT tagged with class m-select
// in order to add the select2 javascript library functionalities
var TranslationSelector = {
    init: function() {
        $("#translationSelector a").click((e) => {
            e.preventDefault();
            var locale = $(e.target).data('locale');
            this.changeView(locale);
        });
    },
    changeView: function(locale) {
        // Hide all translatable fields
        $('.translatable-field-wrapper').hide();
        // Show fields tagged with the selected locale
        $('.translatable-field-wrapper[data-locale="'+locale+'"]').show();
    }
};
jQuery(document).ready(()=> {
    TranslationSelector.init();
    TranslationSelector.changeView('es');
});

