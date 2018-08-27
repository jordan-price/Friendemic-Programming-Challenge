$(function(){
	// Prevent form submission
	$("form").submit(function(e){
		e.preventDefault();
	});
	
	// Client side file type checking
	$("input[name=inviteFile]").change(function(){
		var fileName = $(this).val();
		var extension = fileName.replace(/^.*\./, '');
		$('.error-msg').remove();
		if(extension != "csv"){
			$(this).val('');
			$('button[type=submit]').attr('disabled', true);
			if($.trim(extension) != '')
				$(".input-group").after("<span class='text-danger error-msg'>File rejected, invalid file type.</span>");
		} else {
			$('button[type=submit]').attr('disabled', false);
		}
	});
	
	// Send file to be processed
	$('button[type=submit]').click(function(e){
		var formData = new FormData();
		formData.append('_token', $("input[name=_token]").val());
		formData.append('inviteFile', $("input[name=inviteFile]")[0].files[0]);
		
		// apply "visual" request
		$(".card form").fadeOut(function(){
			$(".card form").remove();
			$(".card").prepend("<img id='loadIndicator' class='mx-auto' src='public/images/loadingIndicator.gif' style='display:none;max-height:200px;'/>");
			$("#loadIndicator").fadeIn(function(){
				$("#loadIndicator").after("<h5 id='loadInfoText' class='text-muted mx-auto' style='display:none;'>Just one moment while we send off your invitations!</h5>");
				$("#loadInfoText").fadeIn();
			});
		});
		
		// send request
		$.ajax({
            url: 'submit-invite',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function( response ) {
				// allow for a smooth transition from "visual" loading
                setTimeout(function(){
					// output data to console
					console.log(response);
					// apply visual changes to HTML supporting output
					$("#loadIndicator,#loadInfoText").fadeOut(function(){
						$("#loadIndicator,#loadInfoText").remove();
						$(".card").html(response.html);
						$('[data-toggle="popover"]').popover({ html:true });
						$("#output").fadeIn();
					});
				}, 1000);
            }
        });
	});
});