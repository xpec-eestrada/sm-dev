require([
    "jquery"
    ],function($){
        $(document).on('click','.btn-quickview',function(){
            window.swloadselect=false;
            window.cont=0;
            $('.action.primary.tocart').css({'display':'block'});
        });
        $('body, html').on('change','.super-attribute-select',function(){
            window.submitaddcar=true;
            $('.action.primary.tocart').css({'display':'block'});
            validacionPrecio();
        });
        window.fnload=function(){
            if(window.swloadselect){
                window.cont++;
            }
            if(window.cont<2){
                if( $('.field.configurable').length ){
                    window.swloadselect=true;
                    $.each($('.field.configurable'),function(index,element){
                        var $select=$(this).find('select');
                        if( $($select).find('option').eq(1).attr('value') != undefined ){
                            $($select).val($($select).find('option').eq(1).attr('value'));
                            $($select).trigger('change');
                        }else{
                            window.cont=0;
                        }
                    });
                }
                $('.fiedxpec').remove();
                if($('.xpec-select').length){
                    $.each($('.xpec-select'),function(index,element){
                        if($("select[name='super_attribute["+$(this).val()+"]']").length){
                            var valor=$("select[name='super_attribute["+$(this).val()+"]']").val();
                            var texto=$("select[name='super_attribute["+$(this).val()+"]'] option:selected").text();
                            var valor=$("select[name='super_attribute["+$(this).val()+"]']").parents('.field.configurable').css({'display':'none'});
                            $('<div class="fiedxpec content-xpecgenero"><div class="cont-xpec-lab" ><span class="label">'+$(this).attr('data-title')+'</span></div><div class="cont-xpec-val"><span class="value">'  +texto+  '</span></div></div>').insertBefore('.product-info-main .product-add-form');
                        }                        
                    });
                }
            }
        }
        window.submitaddcar=true;
        window.swloadselect=false;
        window.cont=0;
        $(document).ready(function() {
            window.fnload();
            $(window).scroll(function(e){
                validacionPrecio();
                window.fnload();
            });
            $('body').mouseover(function(){
                validacionPrecio();
                window.fnload();
            });
            $('body, html').on('submit','#product_addtocart_form',function(e){
                if(!window.submitaddcar){
                    e.preventDefault();
                }else{
                    window.submitaddcar=true;
                    validacionPrecio();
                    if(!window.submitaddcar){
                        e.preventDefault();
                    }
                }
            });
        });
        function validacionPrecio(){
            if( $('.special-price').length ){
                var tmp = $('.special-price').find('.price-wrapper span').html().split('$');
                var valor=  parseInt(tmp[1].replace('.','')  );
                if(valor<=1990){
                    window.submitaddcar=false;
                    $('.action.primary.tocart').css({'display':'none'});
                }
            }else{
                if($('.price-final_price').length){
                    var valor=  parseInt($('.price-final_price').find('.price-wrapper ').attr('data-price-amount'));
                    if(valor<=1990){
                        window.submitaddcar=false;
                        $('.action.primary.tocart').css({'display':'none'});
                    }
                }
            }
        }
    });