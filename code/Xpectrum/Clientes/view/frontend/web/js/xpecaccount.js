require([
    "jquery"
    ],function($){
        $(document).ready(function($) {
            $('.field.field-rut').insertBefore('.field.field-name-firstname');
            $("#group-fields-customer-attributes").remove();
        });
    });