$(document).on('submit', '#formPreviousOrders', function(evt) {
	evt.preventDefault();
	$.ajax({
		type: 'POST',
		url: '../orders/previous/filter',
		data: {
			'searchParam': $('#previous-orders-search-param').val().trim()
		},
		dataType: 'json',
		success: function(json) {
			var result = '';
			if(json.length > 0) {
				for(var i = 0; i < json.length; i++) {
					result += '<tr id="' + json[i].order_id + '">';
					result += '<td data-label="Date">' + json[i].date + '</td>';
					result += '<td data-label="Order Number">#' + json[i].order_id + '</td>';
					result += '<td data-label="PO Number">' + json[i].po_number + '</td>';
					result += '<td data-label="Status">' + json[i].order_status + '</td>';
					result += '<td><a href="javascript:;" class="btn_1st btn-order-detail" data-order-id="' + json[i].order_id + '">view details</a></td>';
					result += '</tr>';
				}
			} else {
				result += '<tr>';
				result += '<td colspan="5" class="text-center">';
				result += 'No orders could be found...';
				result += '</td>';
				result += '</tr>';
			}
			$('#list-previous-orders tbody tr').not('.table-heading').remove();
			$('#list-previous-orders tbody').append(result);
		},
		error: function(json) {
			window.alert("Something went wrong! Please, try again later... If this problem persists, contact your system administrator!");
		}
	});
});
