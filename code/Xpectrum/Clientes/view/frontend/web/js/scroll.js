require([
    "jquery"
    ],function($){
        $(document).ready(function() {
            $(window).scroll(function(e){
                var scroll = $(window).scrollTop();
                if(scroll>=55){
                    $('.custom-header').addClass('header-fixed');
                }else{
                    $('.custom-header').removeClass('header-fixed');
                }
                
            });
        });
    });