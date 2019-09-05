<?php
require_once '../../../vendor/autoload.php';

use ITSA\DAO\OrderDAO;

$loggedUser = unserialize($_SESSION['logged_user']);
$previousOrdersSearchParam = isset($_SESSION['previousOrdersSearchParam']) ? $_SESSION['previousOrdersSearchParam'] : null;
$previousOrders = OrderDAO::selectPrevious($loggedUser, $previousOrdersSearchParam);
?>

<div class="col-xl-6 col-lg-12">
	<div class="order-history" id="previous-orders-history">
		<div class="order_hist_header">
			<div class="hist_header_title">
				<h3>PREVIOUS ORDERS</h3>
			</div>
			<form id="formPreviousOrders" class="order_hist_search">
				<button type="submit" class="order_hist_submit"><i class="ion-android-search"></i></button>
				<input type="text" id="previous-orders-search-param" name="previous-orders-search-param" placeholder="Search order" class="search_order_hist" value="<?=$previousOrdersSearchParam != null ? $previousOrdersSearchParam : ''?>">
			</form>
		</div>
		<div class="order_hist_content">
			<div class="order_hist_content_container">
				<table id="list-previous-orders" width="100%" border="0" cellspacing="0" cellpadding="0" class="previous-order">
					<tbody>
						<tr class="table-heading">
							<th scope="col">Date</th>
							<th scope="col">Order Number</th>
							<th scope="col">PO Number</th>
							<th scope="col">Status</th>
							<th scope="col">&nbsp;</th>
						</tr>
						<?php
						if($previousOrders != null && count($previousOrders) > 0) :
							foreach($previousOrders as $order) :
						?>
								<tr id="<?=$order->getOrderId()?>">
								  <td data-label="Date"><?=$order->getDate()?></td>
								  <td data-label="Order Number">#<?=$order->getOrderId()?></td>
								  <td data-label="PO Number"><?=$order->getPoNumber()?></td>
								  <td data-label="Status"><?=$order->getOrderStatus()?></td>
								  <td><a href="javascript:;" class="btn_1st btn-order-detail" data-order-id="<?=$order->getOrderId()?>">view details</a></td>
								</tr>
						<?php
							endforeach;
						else :
						?>
							<tr>
								<td colspan="5" class="text-center">
									No orders could be found...
								</td>
							</tr>
						<?php
						endif;
						?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>
