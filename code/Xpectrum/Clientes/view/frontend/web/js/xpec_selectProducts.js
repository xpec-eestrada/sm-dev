require([
    "jquery"
    ],function($){
        window.fnload=function(){
            var ids=[];
            var labels=[];
            if($('.xpec-select').length){
                $.each($('.xpec-select'),function(index,element){
                    ids[index]=$(this).val();
                    labels[index]=$(this).attr('data-title');
                });
            }
            
            if(window.swloadselect){
                window.cont++;
            }
            if(window.cont<15){
                $(".content-xpecselect").remove();
                $.each(ids,function(index,value){
                    if($("select[name='super_attribute["+value+"]']").length){
                        window.swloadselect=true;
                        if($("select[name='super_attribute["+value+"]'] option").eq(1).length){
                            $("select[name='super_attribute["+value+"]']").val($("select[name='super_attribute["+value+"]'] option").eq(1).attr('value'));
                            $('<div class="content-xpecselect"><div class="cont-xpec-lab" ><span class="label">'+labels[index]+'</span></div><div class="cont-xpec-val"><span class="value">'+$("select[name='super_attribute["+value+"]'] option").eq(1).text()+'</span></div></div>').insertBefore('.product-info-main .product-add-form');
                        }else{
                            $('<div class="content-xpecselect"><div class="cont-xpec-lab" ><span class="label">'+labels[index]+'</span></div><div class="cont-xpec-val"><span class="value">No tiene '+labels[index]+'</span></div></div>').insertBefore('.product-info-main .product-add-form');
                        }
                        if(window.cont==0){
                            $("select[name='super_attribute["+value+"]']").parents('.field.configurable').addClass('xpec-hidden');;
                        }
                    }
                });
                
            }
        }
        window.swloadselect=false;
        window.cont=0;
        $(document).ready(function() {
            window.fnload();
            $(window).scroll(function(e){
                window.fnload();
                //loadselect();
                
            });
            $('body').mouseover(function(){
                window.fnload();
            });
        });
    });