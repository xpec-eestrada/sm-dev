require([
    "jquery"
    ],function($){
        $(document).ready(function() {
            $(window).scroll(function(e){
                var scroll = $(window).scrollTop();
                if(scroll>=55){
                    $('.custom-header .header-bottom-custom').addClass('header-fixed');
                }else{
                    $('.custom-header .header-bottom-custom').removeClass('header-fixed');
                }
                
            });
        });
    });