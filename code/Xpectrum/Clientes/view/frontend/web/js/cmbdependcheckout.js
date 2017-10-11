require([
  "jquery"
], function($){
    $(document).ready(function() {
        var $_showcmb=false;
        var $_showcmb2=false;
        
        $(document).off("change","select[name='cmbregion']");
        $(document).on("change","select[name='cmbregion']",function(event) {
            if($_showcmb){
                $("input[name='postcode']").val('001');
                var id_region=event.target.value;
                var baseUrl = document.location.origin;
                $("select[name='cmbprovincias']").attr("disabled","disabled");
                if($('.load-background-xpec').length>=1){
                    $('.load-background-xpec').remove();
                }
                $('.checkout-index-index').append('<div class=\'load-background-xpec\'></div>');
                $.ajax({
                    url: baseUrl+'/direcciones/index/ajaxprovincia/',
                    data: {
                        id_region:id_region
                    },
                    type: "POST",
                    dataType: 'json'
                }).done(function (data) {
                    var items="<option value=''>Seleccione</option>";
                    if(data.result.length){
                        $.each(data.result,function(index,item){
                            items=items+"<option value='"+item.provincia_id+"'>"+item.provincia_nombre+"</option>";
                        });
                    }
                    $("select[name='cmbprovincias']").html(items);
                    $("select[name='cmbprovincias']").removeAttr("disabled");
                    $('.load-background-xpec').remove();
                });
            }
            
            $_showcmb=true;
        });
        $(document).off("change","select[name='cmbprovincias']");
        $(document).on("change","select[name='cmbprovincias']",function(event) {
            if($_showcmb2){
                var id_region=$("select[name='cmbregion']").val();
                var id_provincia=event.target.value;
                var baseUrl = document.location.origin;
                $("select[name='region_id']").attr("disabled","disabled");
                if($('.load-background-xpec').length>=1){
                    $('.load-background-xpec').remove();
                }
                $('.checkout-index-index').append('<div class=\'load-background-xpec\'></div>');
                $.ajax({
                    url: baseUrl+'/direcciones/index/ajaxcomuna/',
                    data: {
                        id_region:id_region,
                        id_provincia:id_provincia
                    },
                    type: "POST",
                    dataType: 'json'
                }).done(function (data) {
                    var items="<option value=''>Seleccione</option>";
                    if(data.result.length){
                        $.each(data.result,function(index,item){
                            items=items+"<option value='"+item.comunas_id+"'>"+item.comunas_nombre+"</option>";
                        });
                    }
                    $("select[name='region_id']").html(items);
                    $("select[name='region_id']").removeAttr("disabled");
                    $('.load-background-xpec').remove();
                    $("input[name='postcode']").val('001');

                    var e = jQuery.Event("keyup");
                    e.which = 77; // # Some key code value
                    $("input[name='postcode']").trigger( e );
                });
                $(document).off("keyup","input[name='postcode']");
                $("input[name='postcode']").keyup(function(e){
                    
                });
            }
            $_showcmb2=true;
        });
        $(document).off("change","select[name='region_id']");
        $(document).on("change","select[name='region_id']",function(event) {
            $("input[name='city']").val($("select[name='region_id'] option:selected").text());
            var e = jQuery.Event("keyup");
            e.which = 77; // # Some key code value
            $("input[name='city']").trigger( e );
        });
        
    });
});