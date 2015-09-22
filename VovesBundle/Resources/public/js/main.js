$(function() {
	enviar_form=false;
	
	$('#ahoramadrid_vovesbundle_votante_Enviar').hide();

	$("#formularioInscripcionVoves").submit(function() {
	  return enviar_form;
	});

	$(".checkbox_acepto input").change(function () {
		if( $('.acepto input').is(':checked')){
			$('#send-dummy').hide();
			$('#ahoramadrid_vovesbundle_votante_Enviar').fadeIn();
			enviar_form=true;
		}
		else{
			$('#send-dummy').fadeIn();
			$('#ahoramadrid_vovesbundle_votante_Enviar').hide();
			enviar_form=false;
		}
	});

});
function sizeContent() {
	var newHeight = $(window).height() + 'px';
    $("#page1").css("height", newHeight);
}


var options={
    //Boolean - Whether we should show a stroke on each segment
    segmentShowStroke : true,
    animationEasing: "easeOutQuart",
}