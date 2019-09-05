$(document).on('click', '#btn-edit-order', function() {
	window.location.href = '../orders/info';
});

$(document).on('click', '#btn-finish-order', function() {
	$('#btn-finish-order').text('Processing...').attr('disabled', true);
	$('#btn-edit-order').attr('disabled', true);
	$.ajax({
		type: 'POST',
		url: '../orders/finish',
		dataType: 'json',
		success: function(json) {
			let message = '<img src="./assets/images/success.png" alt="checkmark success">';
			message += '<span class="order-id-success">Order #' + json.order_id + ' Successful</span>';
			message += '<span class="po-number-success">P.O: ' + json.po_number + '</span>';
			message += '<span class="confort-message">Sit back and relax!</span>';
			message += '<span class="confort-message">Your order is being processed and</span>';
			message += '<span class="confort-message">will be delivered shortly</span>';
			message += '<a href="../home" class="btn-home">Home</a>';
			$('.order_cart_container').block({
				message: message,
				css: {
					border: 'none',
    				backgroundColor: 'transparent',
    				cursor: 'default',
    				width: '100%'
				},
				overlayCSS:  {
    				backgroundColor: 'rgb(1, 38, 77)',
    				opacity: 0.83,
    				cursor: 'default'
				}
			});
		},
		error: function() {
			window.alert("Something went wrong! Please, try again later... If this problem persists, contact your system administrator!");
			$('#btn-finish-order').text('Finish Order').attr('disabled', false);
			$('#btn-edit-order').attr('disabled', false);
		}
	});
});