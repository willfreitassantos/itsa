<?php
require_once '../../logic/user/check-authorization.php';
require_once '../../logic/orders/validate-new-order.php';
require_once '../../logic/orders/check-new-order-integrity.php';
require_once '../../../vendor/autoload.php';

$newOrderInfo = unserialize($_SESSION['new_order_info']);

require_once('../default/header.php');
require_once('../default/modal-order-details.php');
?>

<section class="main-wrapper">
	<div class="container">
		<div class="row">
			<div class="col-md-12">
				<ul class="order-prograss">
					<li><a href="javascript:;" class="procesing complete">Select Product (<span id="product_number" data-product-number="<?=count($newOrderInfo->getOrderItems())?>"></span>)</a></li>
					<li><a href="javascript:;" class="procesing complete">Order Information</a></li>
					<li><a href="javascript:;" class="procesing complete">Complete Order</a></li>
				</ul>
			</div>
		</div>
	</div>
	
	<div class="container">
		<div class="row">
			<div class="col-md-12">
				<div class="row order_cart_container mr-0 ml-0 justify-content-center">
					<div class="col-xl-8 col-lg-12">
						<div id="complete_order" class="product_order_cart">
							<table width="100%" border="0" cellspacing="0" cellpadding="0">
						  		<tbody>
									<tr>
							  			<th scope="col">item</th>
							  			<th scope="col">quantity</th>
							  			<th scope="col">&nbsp;</th>
									</tr>
									<?php
									foreach ($newOrderInfo->getOrderItems() as $orderItem) :
										$orderItem = unserialize($orderItem);
									?>
										<tr data-product-id="<?=$orderItem->getProduct()->getProductId()?>">
		        							<td data-label="item">
		        								<div class="product_item_cart">
		        									<img src="../<?=$orderItem->getProduct()->getPhotoPath()?>" alt="product_icon">
		        									<span><?=$orderItem->getProduct()->getDescription()?></span>
		        								</div>
		        							</td>
		        							<td data-label="quantity"><span class="confrm-quantity"><?=$orderItem->getQuantity()?></span></td>
		        							<td><span class="no-button"></span></td>
		        						</tr>
		        					<?php
		        					endforeach;
		        					?>
						  		</tbody>
							</table>
						</div>
					</div>
				
					<div class="col-xl-8 col-lg-12">
				  		<div class="product_order_summary">
				  			<div class="row">
				  				<div class="col-md-4">
				  					<input id="client_name" type="text" placeholder="Enter name" value="<?=$newOrderInfo->getClientName()?>" disabled>
				  				</div>
				  				<div class="col-md-4">
				  					<input id="po_number" type="text" placeholder="Enter PO number" value="<?=$newOrderInfo->getPoNumber()?>" disabled>
				  				</div>
				  				<div class="col-md-4">
				  					<input id="delivery_date" type="datetime" value="<?=$newOrderInfo->getDeliveryDate()?>" disabled>
				  				</div>
								<div class="col-md-12">
									<textarea id="client_comments" cols="30" rows="10" placeholder="No Comments" disabled><?=$newOrderInfo->getComments()?></textarea>
								</div>
							</div>
						</div>
					</div>
				
					<div class="col-lg-12 text-center mb-50 mt-15">
						<button id="btn-edit-order" class="btn_2nd mr-3">Edit Order</button>
						<button id="btn-finish-order" class="btn_2nd ml-3">Finish Order</button>				
					</div>
				</div>
			</div>
		</div>
	</div>
	
	<div class="container">
		<div class="row">
			<?php
			require_once('previous-orders.php');
			require_once('today-orders.php');
			?>
		</div>
	</div>
	
</section>
<script type="text/javascript" src="../resources/plugins/jquery/js/jquery-3.3.1.min.js"></script>
<script type="text/javascript" src="../resources/plugins/bootstrap/js/bootstrap.min.js"></script>
<script type="text/javascript" src="../resources/plugins/jquery-mask-plugin/js/jquery.mask.min.js"></script>
<script type="text/javascript" src="../resources/plugins/jquery-blockUI/js/jquery.blockUI.js"></script>
<script type="text/javascript" src="../resources/plugins/sweet-alert/js/sweet-alert.min.js"></script>
<script type="text/javascript" src="../resources/js/orders/previous-orders.js"></script>
<script type="text/javascript" src="../resources/js/orders/today-orders.js"></script>
<script type="text/javascript" src="../resources/js/orders/order-utils.js"></script>
<script type="text/javascript" src="../resources/js/orders/complete.js"></script>
<?php
require_once('../default/footer.php');
?>