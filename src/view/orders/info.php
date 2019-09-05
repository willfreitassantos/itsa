<?php
require_once '../../logic/user/check-authorization.php';
require_once '../../logic/orders/validate-new-order.php';
require_once '../../../vendor/autoload.php';

$newOrderInfo = unserialize($_SESSION['new_order_info']);

require_once('../default/header.php');
require_once('../default/modal-order-details.php');
?>

<section class="main-wrapper">
	<!-- NAVIGATION -->
	<div class="container">
		<div class="row">
			<div class="col-md-12">
				<ul class="order-prograss">
					<li><a href="javascript:;" class="procesing complete">Select Product (<span id="product_number" data-product-number="<?=count($newOrderInfo->getOrderItems())?>"></span>)</a></li>
					<li><a href="javascript:;" class="procesing complete">Order Information</a></li>
					<li><a href="javascript:;" class="procesing" id="lnk-complete-order">Complete Order</a></li>
				</ul>
			</div>
		</div>
	</div>
	<!-- ALERT PRODUCTS NOT SELECTED -->
	<?php
	if(isset($_SESSION['order_item_issue'])) :
	?>
		<div class="container">
			<div class="row">
				<div class="col-md-12">
					<div id="alert" class="alert alert-danger alert-dismissible fade show text-center alert-products-not-selected" role="alert">
		  				<strong>Oops!</strong> We found some issues... please, correct them and try again.
		 				<button type="button" class="close" data-dismiss="alert" aria-label="Close">
		    				<span aria-hidden="true">&times;</span>
		  				</button>
		  				<ul class="issues-list">
		  					<?=$_SESSION['order_item_issue']?>
		  				</ul>
					</div>
				</div>
			</div>
		</div>
	<?php
		unset($_SESSION['order_item_issue']);
	endif;
	?>
	<!-- NEW ORDER INFO -->
	<div class="container">
		<div class="row">
			<div class="col-md-12">
				<div class="row order_cart_container mr-0 ml-0">
					<div class="col-xl-5 col-lg-12">
						<div id="order_information" class="product_order_cart">
							<table width="100%" border="0" cellspacing="0" cellpadding="0">
								<tbody>
									<tr>
										<th scope="col">item</th>
										<th scope="col">quantity</th>
										<th scope="col">&nbsp;</th>
										<th scope="col">&nbsp;</th>
									</tr>
									<?php
									foreach ($newOrderInfo->getOrderItems() as $orderItem) :
										$orderItem = unserialize($orderItem);
									?>
										<tr class="order-item" data-product-id="<?=$orderItem->getProduct()->getProductId()?>">
											<td data-label="item">
												<div class="product_item_cart">
													<img src="../<?=$orderItem->getProduct()->getPhotoPath()?>" alt="product_icon">
													<span><?=$orderItem->getProduct()->getDescription()?></span>
												</div>
											</td>
											<td data-label="quantity">
												<div class="quantity_numbar">
													<span class="input-number-decrement">–</span>
													<input class="input-number" type="text" value="<?=$orderItem->getQuantity()?>" min="0" max="50">
													<span class="input-number-increment">+</span>
												</div>
											</td>
											<td>&nbsp;</td>
											<td><button class="close_this"><i class="ion-android-close"></i></button></td>
										</tr>
									<?php
									endforeach;
									?>
								</tbody>
							</table>
						</div>
					</div>
					<div class="col-xl-7 col-lg-12">
			  			<div class="product_order_summary">
			  				<div class="row">
			  					<div class="col-md-4">
			  						<input id="client_name" type="text" placeholder="Enter name" value="<?=$newOrderInfo->getClientName()?>" maxlength="200">
			  					</div>
			  					<div class="col-md-4">
			  						<input id="po_number" type="text" placeholder="Enter PO number" value="<?=$newOrderInfo->getPoNumber()?>" maxlength="100">
			  					</div>
			  					<div class="col-md-4">
			  						<input id="delivery_date" type="datetime" placeholder="Delivery date" value="<?=$newOrderInfo->getDeliveryDate()?>" maxlength="8">
			  					</div>
								<div class="col-md-12">
									<textarea id="client_comments" cols="30" rows="10" placeholder="Write Comments" maxlength="200"><?=$newOrderInfo->getComments()?></textarea>
								</div>
								<div class="col-md-12">
									<button class="btn_2nd pull-right" id="btn-view-summary">VIEW SUMMARY</button>
									<a href="./new" class="btn_2nd pull-right" id="btn-select-products">EDIT ORDER</a>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- PREVIOUS ORDERS -->
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
<script type="text/javascript" src="../resources/js/orders/info.js"></script>
<?php
require_once('../default/footer.php');
?>