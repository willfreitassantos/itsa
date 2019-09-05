$(document).ready(function() {
	$('div.product_container').on('click', '.product_item', function(evt) {
		evt.preventDefault();
		let $target_element = $(this);
		let $product_id = $target_element.find("input[type='checkbox']").attr('data-product-id');
		let $action = $target_element.hasClass('selected') ? 'remove' : 'add';

		$.ajax({
			type: 'POST',
			url: '../orders/select-product',
			data: {
				'action': $action,
				'product_id': $product_id
			},
			dataType: 'json',
			success: function(json) {
				$target_element.toggleClass('selected');
				$('#product_number').attr('data-product-number', json);
			},
			error: function(json) {
				window.alert("Something went wrong! Please, try again later... If this problem persists, contact your system administrator!");
			}
		});
	});

	$('#company').trigger('change');
});

$(document).on('change', '#company,#store', function(e) {
	if($(e.target).is('#company')) {
		$('#store option').remove();
	}
	$.ajax({
		type: 'POST',
		url: '../orders/select-store',
		data: {
			company_id: $('#company').val(),
			store_id: $('#store').val()
		},
		dataType: 'json',
		success: function(json) {
			let selectedStoreId = json.selected_store_id;
			
			let options = '';
			$(json.stores).each(function() {
				options += '<option value="' + this.store_id + '" ' + (this.store_id == selectedStoreId ? 'selected' : '') + '>' + this.name + '</option>';
 			});
 			$('#store').append(options);
	
 			if(json.hasOwnProperty('products')) {
 				$('div.product_container ul.row li').remove();
				$('div.product_container ul.row span').remove();
				$('#product_number').attr('data-product-number', 0);
 				let products = '';
 				$(json.products).each(function() {
 					products += '<li class="li col-sm-6 col-md-4 col-lg-3 col-xl-2">';
					products += '<div class="product_item">';
					products += '<input type="checkbox" id="product_item_' + this.product_id + '" data-product-id="' + this.product_id + '">';
					products += '<label for="product_item_' + this.product_id + '">';
					products += '<img src="../' + this.photo_path + '" alt="bitmap">';
					products += '<div class="product-discrip">';
					products += '<span>' + this.description + '</span>';
					products += '</div>';
					products += '</label>';
					products += '</div>';
					products += '</li>';
 				});
 				if(products == '') {
 					products = '<span class="no-products-available">There are no products available!</span>';
 				}				
 				$('div.product_container ul.row').append(products);
 			}
		},
		error: function(json) {
			window.alert("Something went wrong! Please, try again later... If this problem persists, contact your system administrator!");
		}
	})
});