// Initialization of every single input SELECT tagged with class m-select
// in order to add the select2 javascript library functionalities
var Select2 = {
    init: function() {
        $("select.m-select2").select2();
    }};
jQuery(document).ready(function(){Select2.init()});