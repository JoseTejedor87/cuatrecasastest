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
        if($('.translatable-field-wrapper .form-group input').hasClass( "required" )){
            $('.translatable-field-wrapper .form-group input').hide().prop('required',false);
        }
        // Show fields tagged with the selected locale
        $('.translatable-field-wrapper[data-locale="'+locale+'"]').show();
        var inputs = $('.translatable-field-wrapper[data-locale="'+locale+'"]').find('input');
        // var textareas = $('.translatable-field-wrapper[data-locale="'+locale+'"]').find('textarea');
        inputs.each(function( index ) {
            if($( this ).hasClass( "required" )){
                $( this ).show().prop('required',true);
            }else{
                $( this ).show();
            }
        });
        // textareas.each(function( index ) {
        //     if($( this ).hasClass( "required" )){
        //         $( this ).show().prop('required',true);
        //     }else{
        //         $( this ).show();
        //     }
        // });

        
        this.locale = locale;
    },
    refresh: function() {
        this.changeView(this.locale);
    },
    locale: 'es'
};
jQuery(document).ready(()=> {
    TranslationSelector.init();
    TranslationSelector.changeView('es');
});

