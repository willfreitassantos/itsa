<?php
require_once '../../user/check-authorization.php';
require_once '../../../../vendor/autoload.php';

use ITSA\Util\DateTimeUtils;
use ITSA\DAO\ConnectionManager;

function completeWithZeroes($str, $str_length) {
	if(strlen($str) < $str_length) {
		while(strlen($str) < $str_length) {
			$str = '0' . $str;
		}
	}
	return $str;
}

$loggedUser = unserialize($_SESSION['logged_user']);

if($loggedUser->isAdmin()) {
	if($_SERVER['REQUEST_METHOD'] == 'POST') {
		if(isset($_POST['date']) && isset($_POST['po_number']) && isset($_POST['company_id']) && isset($_POST['store_id'])) {
			$date = $_POST['date'];
			$po_number = $_POST['po_number'];
			$company_id = $_POST['company_id'];
			$store_id = $_POST['store_id'];

			if($date != null && $date != '' && !DateTimeUtils::isValid($date, 'd/m/Y')) {
				header('Location: /itsa/orders/new');
				die();
			}

			$whereClause = '';
			#DATE
			if($date != null && $date != '') {
				$whereClause .= ($whereClause != '' ? ' AND ' : ' WHERE ') . 'o.delivery_date = :delivery_date';
			}
			#PO_NUMBER
			if($po_number != null && $po_number != '') {
				$whereClause .= ($whereClause != '' ? ' AND ' : ' WHERE ') . 'o.po_number = :po_number';
			}
			#COMPANY_ID
			if($company_id != null && $company_id != 'all') {
				$whereClause .= ($whereClause != '' ? ' AND ' : ' WHERE ') . 'c.company_id = :company_id';
			}
			#STORE_ID
			if($store_id != null && $store_id != 'all') {
				$whereClause .= ($whereClause != '' ? ' AND ' : ' WHERE ') . 's.store_id = :store_id';
			}

			$conn = ConnectionManager::getConnection();

			$queryOrders = "SELECT DISTINCT s.name AS store, c.name AS company, u.name, o.order_id, o.client_name, DATE_FORMAT(o.delivery_date, '%d/%m/%Y') AS delivery_date, o.po_number, o.comments FROM orders AS o INNER JOIN stores AS s  on o.store_id = s.store_id INNER JOIN companies AS c ON s.company_id = c.company_id INNER JOIN users AS u ON o.user_id = u.user_id INNER JOIN order_items AS oi ON o.order_id = oi.order_id" . $whereClause . " ORDER BY c.name, s.name";
			$stmt = $conn->prepare($queryOrders);
			if($whereClause != '') {
				#DATE
				if($date != null && $date != '') {
					$stmt->bindValue(':delivery_date', date('Y-m-d', strtotime(str_replace('/', '-', $date))));
				}
				#PO_NUMBER
				if($po_number != null && $po_number != '') {
					$stmt->bindValue(':po_number', $po_number);
				}
				#COMPANY_ID
				if($company_id != null && $company_id != 'all') {
					$stmt->bindValue(':company_id', $company_id);
				}
				#STORE_ID
				if($store_id != null && $store_id != 'all') {
					$stmt->bindValue(':store_id', $store_id);
				}
			}
			$stmt->execute();
			$resultsetDelivery = $stmt->fetchAll();
?>
			<!DOCTYPE html>
			<html lang="en-US">
				<head>
					<meta charset="UTF-8"/>
					<meta name="msapplication-tap-highlight" content="no"/>
					<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
					<meta name="description" content="">
					<meta name="author" content="">
					<title>Home || itsa...</title>
					<!-- Favicon -->
					<link href="../../resources/images/favicon.png" rel="shortcut icon" type="image/png">
					<link href="../../resources/images/apple-icon.png" rel="icon" type="image/png">
					<!--=================== style sheet===========-->
					<link rel="stylesheet" href="../../resources/fonts/custom-fonts.css">
					<link rel="stylesheet" href="../../resources/css/delivery-docket.css">
				</head>
				<body>
					<?php
					if(count($resultsetDelivery) > 0) :
						foreach($resultsetDelivery as $delivery_data) :
						?>
							<div><div class="delivery-info"><h1>Delivery Docket</h1>
								<span class="date"><strong>Date:</strong><?=$delivery_data['delivery_date']?></span>
								<span><strong>Company:</strong><?=$delivery_data['company']?></span>
								<span><strong>Store:</strong><?=$delivery_data['store']?></span>
								<span><strong>Order taken by:</strong><?=$delivery_data['name']?></span>
								<span><strong>Customer name:</strong><?=$delivery_data['client_name']?></span>
								<span><strong>P.O. Number:</strong><?=$delivery_data['po_number']?></span>
								<span><strong>Special instructions:</strong><?=$delivery_data['comments']?></span>
							</div><div class="logo">
								<img src="../../resources/images/logo_itsa.png" alt="ITSA logo">
								<a href="https://www.itsa.ie/">www.itsa.ie</a>
								<span class="order-number"># <?=completeWithZeroes($delivery_data['order_id'], 4)?></span>
							</div>
							</div>
							<section class="ordered-items">
								<span class="content-description">Ordered Items</span>
								<table>
									<thead>
										<tr>
											<th>Item Description</th>
											<th>Quantity</th>
										</tr>
									</thead>
									<tbody>
										<?php
										$query = "SELECT p.description, oi.quantity FROM order_items as oi INNER JOIN orders AS o ON o.order_id = oi.order_id INNER JOIN products AS p ON oi.product_id = p.product_id WHERE oi.order_id = :order_id ORDER BY p.description";
										$stmt = $conn->prepare($query);
										$stmt->bindValue(':order_id', $delivery_data['order_id']);
										$stmt->execute();
										$resultset = $stmt->fetchAll();
										$total = 0;
										foreach($resultset as $orderItem_data) :
											$total += $orderItem_data['quantity'];
										?>
											<tr>
												<td><?=$orderItem_data['description']?></td>
												<td><?=$orderItem_data['quantity']?></td>
											</tr>
										<?php
										endforeach;
										?>
										<tr>
											<th>Total</th>
											<td><?=$total?></td>
										</tr>
									</tbody>
								</table>
							</section>
							<section class="signatures">
								<div class="signatures-wrapper">
									<div>
										<span class="signator">Checked by:</span>
										<span class="signature"></span>
									</div>
									<div>
										<span class="signator">Checked by:</span>
										<span class="signature"></span>
									</div>
									<div>
										<span class="signator">Driver:</span>
										<span class="signature"></span>
									</div>
								</div>
							</section>
							<div class="page-breaker"></div>
						<?php
						endforeach;
					else :
					?>
						<span class="content-description">There is no products to be delivered!</span>
					<?php
					endif;
					?>
					<script type="text/javascript">
						(function() {
							window.print();
						})();
					</script>
				</body>
			</html>
		<?php
			die();
		}
	}
}
header('Location: /itsa/orders/new');
die();
?>
