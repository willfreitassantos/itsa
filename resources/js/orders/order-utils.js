$(document).on('click', '.btn-order-detail', function() {
	let orderId = $(this).attr('data-order-id'),
		divToBlock = $(this).closest('.order-history'),
		isPreviousHistory = divToBlock.attr('id') == 'previous-orders-history';
	$.ajax({
		type: 'GET',
		url: '../orders/' + orderId,
		dataType: 'json',
		success: function(json) {
			let message = '<div class="container-fluid">';
			message += '<div class="order-details-wrapper">';
			message += '<button type="button" class="btn-dismiss-order-details" data-is-previous="' + isPreviousHistory + '">';
			message += '<span aria-hidden="true">&times;</span>';
			message += '</button>';
			message += '<div class="order-information">';
			message += '<div class="row">';
			message += '<div class="col align-self-center po-number-details">P.O. Number: ' + json.po_number + '</div>';
			message += '</div>';
			message += '<div class="row">';
			message += '<div class="col-sm-8 offset-sm-2 order-description-details">Order #' + json.order_id + ' taken on ' + json.date + ' by ' + json.user.name + ' to be delivered on ' + json.delivery_date + '.</div>';
			message += '</div>';
			message += '</div>';
			message += '<div class="row order-items-wrapper" style="margin-left: 0; margin-right: 0;">';
			for(var i = 0; i < json.order_items.length; i++) {
				message += '<div class="col-sm-6">' + json.order_items[i].product.description + ' (' + json.order_items[i].quantity + ')</div>';
			}
			message += '</div>';
			message += '<div class="row order-details-options" style="margin-left: 0; margin-right: 0;">';
			message += '<div class="col">';
			message += '<button class="btn_2nd btn-edit-order" data-order-id="' + json.order_id + '" data-is-previous="' + isPreviousHistory + '">Edit</button>';
			message += '<button class="btn_2nd btn-remove-order" data-order-id="' + json.order_id + '" data-is-previous="' + isPreviousHistory + '">Remove</button>';
			message += '</div>';
			message += '</div>';
			message += '</div>';
			divToBlock.block({
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
			swal({
				title: 'Oops...',
				text: 'Something went wrong! Please, try again later... If this problem persists, contact your system administrator!',
				icon: 'error'
			});
		}
	});
});

$(document).on('click', '.btn-dismiss-order-details', function() {
	if($(this).attr('data-is-previous') == 'true') {
		$('#previous-orders-history').unblock();
	} else {
		$('#today-orders-history').unblock();
	}
});

$(document).on('click', '.btn-remove-order', function() {
	let order_id = $(this).attr('data-order-id'),
		isPreviousHistory = $(this).attr('data-is-previous');
	
	swal({
  		title: "Are you sure?",
  		text: "Once deleted, you will not be able to recover this order!",
  		icon: "warning",
  		closeOnClickOutside: false,
  		buttons: {
  			cancel: true,
  			confirm: {
    			text: "Yes",
    			value: true,
    			visible: true,
    			className: "",
    			closeModal: false
  			}
  		},
  		dangerMode: true,
	})
	.then((willDelete) => {
  		if (willDelete) {
  			$.ajax({
				type: 'GET',
				url: '../orders/remove/' + order_id,
				success: function() {
					if(isPreviousHistory == 'true') {
						$('#previous-orders-history table#list-previous-orders tbody tr#' + order_id).remove();
						if($('#previous-orders-history table#list-previous-orders tbody tr').not('.table-heading').length == 0) {
							$('#previous-orders-history table#list-previous-orders tbody').append('<tr><td colspan="5" class="text-center">No orders could be found...</td></tr>');
						}
						$('#previous-orders-history').unblock();
					} else {
						$('table#list-today-orders tbody tr#' + order_id).remove();
						if($('#today-orders-history table#list-today-orders tbody tr').not('.table-heading').length == 0) {
							$('#today-orders-history table#list-today-orders tbody').append('<tr><td colspan="5" class="text-center">No orders could be found...</td></tr>');
						}
						$('#today-orders-history').unblock();
					}
					swal("The order has been deleted!", {
      					icon: "success"
    				});
				},
				error: function() {
					swal({
						title: 'Oops...',
						text: 'Something went wrong! Please, try again later... If this problem persists, contact your system administrator!',
						icon: 'error'
					});
				}
			});
    	}
	});
});

$(document).on('click', '.btn-edit-order, #btn-select-products-update', function() {
	let order_id = $(this).attr('data-order-id'),
		isPreviousHistory = $(this).attr('data-is-previous');
	if(isPreviousHistory == 'true') {
		$('#previous-orders-history').unblock();
	} else {
		$('#today-orders-history').unblock();
	}
	$.ajax({
		type: 'GET',
		url: '../orders/form-update/' + order_id,
		dataType: 'json',
		success: function(json) {
			$('#modal-order-details #order-number').html(order_id);

			let modalBody = '';
			/*if(json.loggedUserIsAdmin) {
				modalBody += '<div class="row company-store-select">';
				modalBody += '<div class="col-xs-12 col-sm-6 form-group">';
				modalBody += '<label for="company" class="control-label">Company</label>';
				modalBody += '<select id="company" class="form-control">';
					for(var i = 0; i < json.companies.length) {
						modalBody += '<option value="' + json.companies[i].id + ' ' + (json.companies[i].id == json.order.company_id ? 'selected' : '') + '>' + json.companies[i].name + '</option>';
					}
				modalBody += '</select>';
				modalBody += '</div>';
				modalBody += '<div class="col-xs-12 col-sm-6 form-group">';
				modalBody += '<label for="store" class="control-label">Store</label>';
				modalBody += '<select id="store" class="form-control">';
				modalBody += '</select>';
				modalBody += '</div>';
				modalBody += '</div>';
			}*/
			modalBody += '<div class="container">';
			modalBody += '<div class="row">';
			modalBody += '<div class="col-md-12">';
			modalBody += '<ul class="order-prograss-update">';
			modalBody += '<li><a href="javascript:;" class="procesing complete">Select Product (<span id="product_number_update" data-product-number="' + json.products_selected_already + '"></span>)</a></li>';
			modalBody += '<li><a href="javascript:;" class="procesing">Order Information</a></li>';
			modalBody += '</ul>';
			modalBody += '</div>';
			modalBody += '</div>';
			modalBody += '</div>';
			modalBody += '<div class="product_update_container">';
			modalBody += '<ul class="row">';
			if(json.products_available.length > 0) {
				for(var i = 0; i < json.products_available.length; i++) {
					modalBody += '<li class="li col-sm-6 col-md-4">';
					modalBody += '<div class="product_update_item' + (json.products_ordered_id.includes('(' + json.products_available[i].product_id + ')') ? ' selected' : '') + '">';
					modalBody += '<input type="checkbox" id="product_item_' + json.products_available[i].product_id + '" data-product-id="' + json.products_available[i].product_id + '">';
					modalBody += '<label for="product_item_' + json.products_available[i].product_id + '">';
					modalBody += '<img src="../' + json.products_available[i].photo_path + '" alt="bitmap">';
					modalBody += '<div class="product_update-discrip">';
					modalBody += '<span>' + json.products_available[i].description + '</span>';
					modalBody += '</div>';
					modalBody += '</label>';
					modalBody += '</div>';
					modalBody += '</li>';
				}
			} else {
				modalBody += '<span class="no-products-available">There are no products available!</span>';
			}
			modalBody += '</ul>';
			modalBody += '</div>';
			modalBody += '<div id="error-message"></div>';
			$('#modal-order-details .modal-body').html(modalBody);

			let modalFooter = '';
			modalFooter += '<button id="btn-order-info-update" type="button">Order information</button>';
			$('#modal-order-details .modal-footer').html(modalFooter);
			
			$('#modal-order-details').modal();
		},
		error: function() {
			swal({
				title: 'Oops...',
				text: 'Something went wrong! Please, try again later... If this problem persists, contact your system administrator!',
				icon: 'error'
			});
		}
	});
});

$('#modal-order-details div.modal-body').on('click', '.product_update_item', function(evt) {
	evt.preventDefault();
	let $target_element = $(this);
	let $product_id = $target_element.find("input[type='checkbox']").attr('data-product-id');
	let $action = $target_element.hasClass('selected') ? 'remove' : 'add';

 	$.ajax({
		type: 'POST',
		url: '../orders/select-product-update',
		data: {
			'action': $action,
			'product_id': $product_id
		},
		dataType: 'json',
		success: function(json) {
			$target_element.toggleClass('selected');
			$('#product_number_update').attr('data-product-number', json);
		},
		error: function(json) {
			swal({
				title: 'Oops...',
				text: 'Something went wrong! Please, try again later... If this problem persists, contact your system administrator!',
				icon: 'error'
			});
		}
	});
});

$('#modal-order-details div.modal-footer').on('click', '#btn-order-info-update', function() {
	$.ajax({
		type: 'GET',
		url: '../orders/complete-update',
		dataType: 'json',
		success: function(json) {
			if(!json.hasProductsSelected) {
				let errorMessage = '';
				errorMessage += '<div class="container">';
				errorMessage += '<div class="row">';
				errorMessage += '<div class="col-md-12">';
				errorMessage += '<div id="alert" class="alert alert-danger alert-dismissible fade show text-center alert-products-not-selected" role="alert">';
		  		errorMessage += '<strong>Oops!</strong> There are no products selected...';
		 		errorMessage += '<button type="button" class="close" data-dismiss="alert" aria-label="Close">';
		    	errorMessage += '<span aria-hidden="true">&times;</span>';
		  		errorMessage += '</button>';
				errorMessage += '</div>';
				errorMessage += '</div>';
				errorMessage += '</div>';
				errorMessage += '</div>';
				$('#modal-order-details .modal-body #error-message').html(errorMessage);
			} else {
				let modalBody = '';
				modalBody += '<div class="container">';
				modalBody += '<div class="row">';
				modalBody += '<div class="col-md-12">';
				modalBody += '<ul class="order-prograss-update">';
				modalBody += '<li><a href="javascript:;" class="procesing complete">Select Product (<span id="product_number_update" data-product-number="' + json.orderToUpdate.order_items.length + '"></span>)</a></li>';
				modalBody += '<li><a href="javascript:;" class="procesing complete">Order Information</a></li>';
				modalBody += '</ul>';
				modalBody += '</div>';
				modalBody += '</div>';
				modalBody += '</div>';
				modalBody += '<div class="container">';
				modalBody += '<div class="row order_cart_update_container mr-0 ml-0">';
				modalBody += '<div class="col-sm-12">';
				modalBody += '<div id="order_update_information" class="product_order_update_cart">';
				modalBody += '<table width="100%" border="0" cellspacing="0" cellpadding="0">';
				modalBody += '<tbody>';
				modalBody += '<tr>';
				modalBody += '<th scope="col">item</th>';
				modalBody += '<th scope="col">quantity</th>';
				modalBody += '</tr>';
				for(var i = 0; i < json.orderToUpdate.order_items.length; i++) {
					modalBody += '<tr class="order-item-update" data-product-id="' + json.orderToUpdate.order_items[i].product.product_id + '">';
					modalBody += '<td data-label="item">';
					modalBody += '<div class="product_item_update_cart">';
					modalBody += '<img src="../' + json.orderToUpdate.order_items[i].product.photo_path + '" alt="product_icon">';
					modalBody += '<span>' + json.orderToUpdate.order_items[i].product.description + '</span>';
					modalBody += '</div>';
					modalBody += '</td>';
					modalBody += '<td data-label="quantity">';
					modalBody += '<div class="quantity_update_numbar">';
					modalBody += '<span class="input-number-decrement">–</span>';
					modalBody += '<input class="input-update-number" type="text" value="' + json.orderToUpdate.order_items[i].quantity + '" min="0" max="50">';
					modalBody += '<span class="input-number-increment">+</span>';
					modalBody += '</div>';
					modalBody += '</td>';
					modalBody += '</tr>';
				}
				modalBody += '</tbody>';
				modalBody += '</table>';
				modalBody += '</div>';
				modalBody += '</div>';
				modalBody += '<div class="col-sm-12">';
			  	modalBody += '<div class="product_order_summary_update">';
			  	modalBody += '<div class="row">';
			  	modalBody += '<div class="col-md-4">';
			  	modalBody += '<input id="client_name_update" type="text" placeholder="Enter name" value="' + json.orderToUpdate.client_name + '" maxlength="200">';
			  	modalBody += '</div>';
			  	modalBody += '<div class="col-md-4">';
			  	modalBody += '<input id="po_number_update" type="text" placeholder="Enter PO number" value="' + json.orderToUpdate.po_number+ '" maxlength="100">';
			  	modalBody += '</div>';
			  	modalBody += '<div class="col-md-4">';
			  	modalBody += '<input id="delivery_date_update" type="datetime" placeholder="Delivery date" value="' + json.orderToUpdate.delivery_date + '" maxlength="8">';
			  	modalBody += '</div>';
				modalBody += '<div class="col-md-12">';
				modalBody += '<textarea id="client_comments_update" cols="30" rows="10" placeholder="Write Comments" maxlength="200">' + (json.orderToUpdate.comments != null ? json.orderToUpdate.comments : "") + '</textarea>';
				modalBody += '</div>';
				modalBody += '</div>';
				modalBody += '</div>';
				modalBody += '</div>';
				modalBody += '</div>';
				modalBody += '</div>';
				modalBody += '<div id="error-message"></div>';
				$('#modal-order-details div.modal-body').html(modalBody);

				let modalFooter = '';
				modalFooter += '<button id="btn-select-products-update" type="button" data-order-id="' + json.orderToUpdate.order_id + '">Select Products</button>';
				modalFooter += '<button id="btn-finish-update" type="button">Update Order</button>';
				$('#modal-order-details .modal-footer').html(modalFooter);

				$(".product_order_update_cart tbody").on("click", ".input-number-decrement,.input-number-increment", function() {
				    var $qty = $(this).closest(".quantity_update_numbar").find(".input-update-number"),
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

				$('#modal-order-details .modal-body input[type="datetime"]').mask('00/00/00');
				$('#modal-order-details .modal-body .input-update-number').mask('09');
				$('#order_update_information table tbody tr:nth-child(2)').find('.input-update-number').select();
			}
		},
		error: function(json) {
			swal({
				title: 'Oops...',
				text: 'Something went wrong! Please, try again later... If this problem persists, contact your system administrator!',
				icon: 'error'
			});
		}
	});
});

$(document).on('keypress', '.input-update-number', function(e) {
	if(e.which == 13) {
		$nextElement = $(e.target).closest('tr').next();
		if($nextElement.is('tr')) {
			$nextElement.find('.input-update-number').select();
		}
	}
});

$('#modal-order-details div.modal-footer').on('click', '#btn-finish-update', function() {
	swal({
  		title: "Are you sure?",
  		text: "Once updated, the old data can't be recovered!",
  		icon: "warning",
  		closeOnClickOutside: false,
  		buttons: {
  			cancel: true,
  			confirm: {
    			text: "Update",
    			value: true,
    			visible: true,
    			className: "",
    			closeModal: false
  			}
  		},
  		dangerMode: true,
	})
	.then((willUpdate) => {
  		if (willUpdate) {
  			let $clientName = $('#client_name_update').val().trim(),
				$poNumber = $('#po_number_update').val().trim().toUpperCase(),
				$deliveryDate = $('#delivery_date_update').val().trim(),
				$clientComments = $('#client_comments_update').val().trim(),
				$orderItemQty = '';
			$("tr.order-item-update").each(function() {
				$orderItemQty += $(this).attr('data-product-id') + ',' + $(this).find('.input-update-number').val() + ';';
			});
			$orderItemQty = $orderItemQty.substring(0, $orderItemQty.length - 1);
			$.ajax({
				type: 'POST',
				url: '../orders/update',
				data: {
					'client_name': $clientName,
					'po_number': $poNumber,
					'delivery_date': $deliveryDate,
					'client_comments': $clientComments,
					'order_item_qty': $orderItemQty
				},
				dataType: 'json',
				success: function(json) {
					if(json.hasErrors) {
						let alertErrors = '';
						alertErrors += '<div class="container">';
						alertErrors += '<div class="row">';
						alertErrors += '<div class="col-md-12">';
						alertErrors += '<div id="alert" class="alert alert-danger alert-dismissible fade show text-center alert-products-not-selected" role="alert">';
			  			alertErrors += '<strong>Oops!</strong> We found some issues... please, correct them and try again.';
			 			alertErrors += '<button type="button" class="close" data-dismiss="alert" aria-label="Close">';
			    		alertErrors += '<span aria-hidden="true">&times;</span>';
			  			alertErrors += '</button>';
			  			alertErrors += '<ul class="issues-list">';
			  			alertErrors += json.errors;
			  			alertErrors += '</ul>';
						alertErrors += '</div>';
						alertErrors += '</div>';
						alertErrors += '</div>';
						alertErrors += '</div>';
						$('#modal-order-details div.modal-body #error-message').html(alertErrors);
						swal.close();
					} else {
						swal("The order has been updated!", {
	      					icon: "success"
	    				});
						$('#modal-order-details').modal('hide');
					}
				},
				error: function(json) {
					swal({
						title: 'Oops...',
						text: 'Something went wrong! Please, try again later... If this problem persists, contact your system administrator!',
						icon: 'error'
					});
				}
			});
  		}
  	});
});
