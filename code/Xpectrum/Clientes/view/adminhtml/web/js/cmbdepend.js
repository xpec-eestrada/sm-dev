require([
  "jquery"
], function($){
  $(document).ready(function() {
    var $_swactive=false;
    var $_swactive2=false;

    //$("div[data-index='region_id_input']").find('admin__control-text').val();
    
    //region_id_input

    $(document).on("click",".admin__page-nav-item",function() {      
      $(document).off("change","div[data-index='cmbregion']");
      $(document).on("change","div[data-index='cmbregion']",function(event) {
        if($_swactive){
          var nameprov=event.target.name.replace('cmbregion','cmbprovincias');
          var namecom=event.target.name.replace('cmbregion','cmbcomunas');
          $("select[name='"+namecom+"']").attr("disabled","disabled");
          $("select[name='"+nameprov+"']").html("<option value=''>Seleccione</option>");
          var id_region=event.target.value;
          var baseUrl = document.location.origin;
          $("select[name='"+nameprov+"']").attr("disabled","disabled");
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
              $("select[name='"+nameprov+"']").html(items);
              $("select[name='"+nameprov+"']").removeAttr("disabled");
          });
        }else{
          $_swactive=true;
        }
      });
      $(document).off("change","div[data-index='cmbprovincias']");
      $(document).on("change","div[data-index='cmbprovincias']",function(event) {
        if($_swactive2){
          var namecom=event.target.name.replace('cmbprovincias','cmbcomunas');
          var namereg=event.target.name.replace('cmbprovincias','cmbregion');
          var id_region=$("select[name='"+namereg+"']").val();
          var id_provincia=event.target.value;
          $("select[name='"+namecom+"']").html("<option value=''>Seleccione</option>");
          $("select[name='"+namecom+"']").attr("disabled","disabled");
          var baseUrl = document.location.origin;
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
              $("select[name='"+namecom+"']").html(items);
              $("select[name='"+namecom+"']").removeAttr("disabled");
          });
        }else{
          $_swactive2=true;
        }
      });
      setTimeout(function(){ 
        console.log("num: "+$("div[data-index='cmbregion']").length);
        $.each($("div[data-index='cmbregion']"),function(index,element){
          $(this).find('.admin__control-select');
          console.log($(this).find('.admin__control-select').val());
        });
      }, 3000);
      
      


      //console.log( jQuery("div[data-index='cmbprovincias']").html()  );
      
    });
  });
});