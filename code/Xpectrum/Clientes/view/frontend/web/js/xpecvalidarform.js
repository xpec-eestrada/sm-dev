require([
    "jquery","Validador"
], function($){

    $(document).ready(function($) {
        $("#rut").attr('placeholder','RUT Ej: 12345678-9');
        $("#rut").attr('maxlength',10);
        $('body').mouseover(function(){
            $("#rut").attr('placeholder','RUT Ej: 12345678-9');
            $("#rut").attr('maxlength',10);
        });
        $("#rut").blur(function(){
            var rut=$(this).val();
            if(rut.length){
                if( window.Fn.validaRut(rut) ){
                    $("#rut-error").remove();
                    $("#rut").removeClass('mage-error');
                }else{
                    $("#rut-error").remove();
                    $("#rut").addClass('mage-error');
                    $(this).parent('.control').append('<div for="rut" generated="true" class="mage-error" id="rut-error">Rut Invalido.</div>');
                }
            }
        });
        $(document).on("click",'.action.submit.primary',function(e){
            if(!validarForm()){
                e.preventDefault();
            }
        });
        function validarForm(){
            var sw=true;
            if(("#rut").length){
                var rut=$("#rut").val();
                if( window.Fn.validaRut(rut) ){
                    $("#rut-error").remove();
                    $("#rut").removeClass('mage-error');
                }else{
                    sw=false;
                    $("#rut-error").remove();
                    $("#rut").addClass('mage-error');
                    $("#rut").parent('.control').append('<div for="rut" generated="true" class="mage-error" id="rut-error">Rut Invalido.</div>');
                    $("#rut").focus();
                }
            }
            return sw;
        }
    });
});