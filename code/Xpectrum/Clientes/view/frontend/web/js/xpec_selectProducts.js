require([
    "jquery"
    ],function($){
        window.fnload=function(){
            
            var ids=[];
            var labels=[];
            // if($('.xpec-select').length){
            //     $.each($('.xpec-select'),function(index,element){
            //         ids[index]=$(this).val();
            //         labels[index]=$(this).attr('data-title');
            //     });
            // }
            if(window.swloadselect){
                window.cont++;
            }
            if(window.cont<10){
                if( $('.field.configurable').length ){
                    window.swloadselect=true;
                    $.each($('.field.configurable'),function(index,element){
                        var $select=$(this).find('select');
                        if( $($select).find('option').eq(1).attr('value') != undefined ){
                            $($select).val($($select).find('option').eq(1).attr('value'));
                            $($select).trigger('change');
                        }
                    });
                }
                $('.fiedxpec').remove();
                if($('.xpec-select').length){
                    $.each($('.xpec-select'),function(index,element){
                        var valor=$("select[name='super_attribute["+$(this).val()+"]']").val();
                        var texto=$("select[name='super_attribute["+$(this).val()+"]'] option:selected").text();
                        var valor=$("select[name='super_attribute["+$(this).val()+"]']").parents('.field.configurable').css({'display':'none'});
                        $('<div class="fiedxpec content-xpecgenero"><div class="cont-xpec-lab" ><span class="label">'+$(this).attr('data-title')+'</span></div><div class="cont-xpec-val"><span class="value">'  +texto+  '</span></div></div>').insertBefore('.product-info-main .product-add-form');
                    });
                }
                
            }
        }
        window.swloadselect=false;
        window.cont=0;
        $(document).ready(function() {
            window.fnload();
            $(window).scroll(function(e){
                window.fnload();
            });
            $('body').mouseover(function(){
                window.fnload();
            });
        });
    });