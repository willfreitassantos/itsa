$(function(){
	$('input[type="datetime"]').mask('00/00/00');
	$('.input-number').mask('09');
	$('#order_information table tbody tr:nth-child(2)').find('.input-number').select();
});

$(".product_order_cart tbody").on("click", ".input-number-decrement,.input-number-increment", function() {
    var $qty = $(this).closest(".quantity_numbar").find(".input-number"),
        $currentVal = parseInt($qty.val()),
        $min = $qty.attr("min"),
        $max = $qty.attr("max"),
        $isAdd = $(this).hasClass("input-number-increment");

    if (!isNaN($currentVal)) {
        if ($isAdd) {
            $currentVal++;
            if($currentVal <= $max) {
                $qty.val($currentVal);
            }
        }else{
            $currentVal--;
            if($currentVal >= $min) {
                $qty.val($currentVal);
            }
        }
    }
});

$(".product_order_cart tbody").on("click", ".close_this", function() {
	let $target = $(this),
		$product_id = $target.parent().parent().attr("data-product-id"),
		$action = 'remove';
	$.ajax({
		type: 'POST',
		url: '../orders/select-product',
		data: {
			'action': $action,
			'product_id': $product_id
		},
		dataType: 'json',
		success: function(json) {
			$target.parent().parent().remove();
			$('#product_number').attr('data-product-number', json);
			if(json == 0) {
				window.alert('There is no more items in your cart! Please, select some items and try again...');
				window.location.href = '../orders/new';
			}
		},
		error: function(json) {
			window.alert("Something went wrong! Please, try again later... If this problem persists, contact your system administrator!");
		}
	});
});

$("body").on("click", "#btn-view-summary", function(evt) {
	evt.preventDefault();
	let $clientName = $('#client_name').val().trim(),
		$poNumber = $('#po_number').val().trim().toUpperCase(),
		$deliveryDate = $('#delivery_date').val().trim(),
		$clientComments = $('#client_comments').val().trim(),
		$orderItemQty = '';
	$("tr.order-item").each(function() {
		$orderItemQty += $(this).attr('data-product-id') + ',' + $(this).find('.input-number').val() + ';';
	});
	$orderItemQty = $orderItemQty.substring(0, $orderItemQty.length - 1);
	$html = '<form id="redirect-to-complete-order" action="../orders/info/validate" method="POST" style="display: none;">';
	$html += '<input type="hidden" name="client_name" value="' + $clientName + '">';
	$html += '<input type="hidden" name="po_number" value="' + $poNumber + '">';
	$html += '<input type="hidden" name="delivery_date" value="' + $deliveryDate + '">';
	$html += '<input type="hidden" name="client_comments" value="' + $clientComments + '">';
	$html += '<input type="hidden" name="order_item_qty" value="' + $orderItemQty + '">';
	$html += '</form>';
	$('body').append($html);
	$('#redirect-to-complete-order').submit();
});

$(document).on('keypress', '.input-number', function(e) {
	if(e.which == 13) {
		$nextElement = $(e.target).closest('tr').next();
		if($nextElement.is('tr')) {
			$nextElement.find('.input-number').select();
		}
	}
});