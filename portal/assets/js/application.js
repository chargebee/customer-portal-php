/*
 * Sets the error class and error element for jQuery form validation.
 */
jQuery.validator.setDefaults({
	errorClass: "text-danger",
    errorElement: "span"
});

/* 
 * Ajax call used to update the html content. 
 */
function AjaxCall(url, type, data, selector, spinner) {
    $.ajax({
        type: type,
        url: url,
        data: data,
        contentType: "application/x-www-form-urlencoded;charset=utf-8",
		beforeSend: function() {
			$(".loader").show();
		},
        success: function (result) {
            $(selector).html(result);
        },
		complete: function() {
			$(".loader").hide();
		}
    });
}


/* 
 * Ajax call for adding/updating the portal information. 
 */
function AjaxCallMessage(url, type, dataType, data, page) {
    $.ajax({
        type: type,
        dataType: dataType,
        url: url,
        data: data,
        contentType: "application/x-www-form-urlencoded;charset=utf-8",
		beforeSend: function() {
			$(".cb-alert-flash, .loader").show();
			$('.text-danger').hide();
		},
        success: function(response) {
            redirect_url= response.forward + "?success=true&page="+page;
            window.location.href = redirect_url;
        },
		error: function(response) {
			var msg = ""
			try {
				var error = JSON.parse(response.responseText);
				if('error_param' in error){
					$('span[id="' + error.error_param +'"]').text(error.error_msg);
					$('span[id="' + error.error_param +'"]').show();
					msg = response.error_msg;
				} else {
	            	msg = response.error_msg;
				}
			}catch(e) {
				msg = "Sorry, something went wrong while processing your request.";	
			}
			$("#cb-handle-progress .alert-danger").show();
			$("#cb-handle-progress .alert-danger .message").text(msg);
        	$("#cb-handle-progress .alert-danger").fadeOut(8000);
		},
		complete: function(){
			$(".loader").hide();
		}
    });
}

$(document).ready(function(){
	/**
	 * On clicking the "Next" button to the get the subsequent set of invoices.
	 */
	$('#invoiceTableShow').on('click', '#next', function (e) {
	    e.preventDefault();
	    var offset = $('.inv-next-offset').text();
	    var lastInvoiceNo = $('.inv-end-no').text();
	    var params = {lastInvoiceNo:lastInvoiceNo, offset:offset};
	    AjaxCall('nextInvoiceDetails.php', 'POST', params, '#invoiceTableShow', '#spinner');
	});
})

