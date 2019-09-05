<?php
require_once '../../../vendor/autoload.php';

use ITSA\DAO\OrderDAO;

$loggedUser = unserialize($_SESSION['logged_user']);
$todayOrdersSearchParam = isset($_SESSION['todayOrdersSearchParam']) ? $_SESSION['todayOrdersSearchParam'] : null;
$todaysOrders = OrderDAO::selectTodays($loggedUser, $todayOrdersSearchParam);
?>
<div class="col-xl-6 col-lg-12">
	<div class="order-history" id="today-orders-history">
		<div class="order_hist_header">
			<div class="hist_header_title">
				<h3>TODAY  ORDERS</h3>
			</div>
			<form id="formTodayOrders" class="order_hist_search">
				<button type="submit" class="order_hist_submit"><i class="ion-android-search"></i></button>
				<input type="text" id="today-orders-search-param" name="today-orders-search-param" placeholder="Search order" class="search_order_hist" value="<?=$todayOrdersSearchParam != null ? $todayOrdersSearchParam : ''?>">
			</form>
		</div>
		<div class="order_hist_content">
			<div class="order_hist_content_container">
				<table id="list-today-orders" width="100%" border="0" cellspacing="0" cellpadding="0" class="today-order">
					<tbody>
						<tr class="table-heading">
							<th scope="col">Date</th>
							<th scope="col">Order Number</th>
							<th scope="col">PO Number</th>
							<th scope="col">Status</th>
							<th scope="col">&nbsp;</th>
						</tr>
						<?php
						if($todaysOrders != null && count($todaysOrders) > 0) :
							foreach($todaysOrders as $order) :
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
