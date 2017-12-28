require([
    "jquery"
    ],function($){
        $(document).ready(function() {
            $(window).scroll(function(e){
                if( $('.form-wishlist-items button[data-role="all-tocart"]').length ){
                    $('.form-wishlist-items button[data-role="all-tocart"]').remove();
                }
            });
            $('body').mouseover(function(){
                if( $('.form-wishlist-items button[data-role="all-tocart"]').length ){
                    $('.form-wishlist-items button[data-role="all-tocart"]').remove();
                }
            });
        });
    });