require([
    "jquery"
], function($){
    $(document).ready(function($) {
        $('body').mouseover(function(){
            if( $("input[name='product[price]']").length ){
                var precio=$("input[name='product[price]']").val();
                $("input[name='product[price]']").val(0);
                precio = precio.replace(".","", "gi");
                precio=parseInt(precio);
                $("input[name='product[price]']").val(precio);
            }
        });
    });
});