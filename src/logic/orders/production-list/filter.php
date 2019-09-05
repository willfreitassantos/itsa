<?php
require_once '../../user/check-authorization.php';
require_once '../../../../vendor/autoload.php';

use ITSA\Util\DateTimeUtils;
use ITSA\DAO\ConnectionManager;

$loggedUser = unserialize($_SESSION['logged_user']);

if($loggedUser->isAdmin()) {
	if($_SERVER['REQUEST_METHOD'] == 'POST') {
		if(isset($_POST['production-list-date']) && DateTimeUtils::isValid($_POST['production-list-date'], 'd/m/Y')) {
			$conn = ConnectionManager::getConnection();

			$nextDelivery = date('Y-m-d', strtotime(str_replace('/', '-', $_POST['production-list-date'])));

			$querySelectTotal = "SELECT oi.product_id, p.description, SUM(oi.quantity) AS quantity FROM order_items AS oi INNER JOIN orders AS o ON o.order_id = oi.order_id INNER JOIN products AS p ON oi.product_id = p.product_id WHERE o.delivery_date = :delivery_date GROUP BY oi.product_id ORDER BY p.description";
			$stmt = $conn->prepare($querySelectTotal);
			$stmt->bindValue(':delivery_date', $nextDelivery);
			$stmt->execute();
			$resultsetSelectTotal = $stmt->fetchAll();

			$querySelectByStore = "SELECT c.company_id, c.name AS company, s.store_id, s.name AS store, p.description, SUM(oi.quantity) AS quantity FROM order_items AS oi INNER JOIN products AS p ON oi.product_id = p.product_id INNER JOIN orders AS o ON oi.order_id = o.order_id INNER JOIN stores AS s ON o.store_id = s.store_id INNER JOIN companies AS c ON s.company_id = c.company_id WHERE o.delivery_date = :delivery_date GROUP BY p.product_id, s.store_id ORDER BY c.name, s.name, p.description";
			$stmt = $conn->prepare($querySelectByStore);
			$stmt->bindValue(':delivery_date', $nextDelivery);
			$stmt->execute();
			$resultsetSelectByStore = $stmt->fetchAll();
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
					<link rel="stylesheet" href="../../resources/css/production-list.css">
				</head>
				<body>
					<header>
						<div class="delivery-on">
							<h1>Products to be Delivered on</h1>
							<h2><?=date('d/m/Y', strtotime($nextDelivery))?></h2>
						</div><div class="logo">
							<img src="../../resources/images/logo_itsa.png" alt="ITSA logo">
							<a href="https://www.itsa.ie/">www.itsa.ie</a>
						</div>
					</header>
					<main>
						<?php
						if(count($resultsetSelectTotal) > 0) :
						?>
							<section class="total-ordered-items">
								<span class="content-description">Total Ordered Items</span>
								<table>
									<thead>
										<tr>
											<th>Item Description</th>
											<th>Quantity</th>
										</tr>
									</thead>
									<tbody>
										<?php
										#$generalTotal = 0;
										foreach($resultsetSelectTotal as $total_data) :
											#$generalTotal += $total_data['quantity'];
										?>
											<tr>
												<td><?=$total_data['description']?></td>
												<td><?=$total_data['quantity']?></td>
											</tr>
										<?php
										endforeach;
										?>
										<!--<tr>
											<th>Total</th>
											<td><?=$generalTotal?></td>
										</tr>-->
									</tbody>
								</table>
							</section>
							<section class="ordered-items-by-store">
								<span class="content-description">Ordered Items by Store</span>
								<?php
								$lastCompanyId = 0;
								$lastStoreId = 0;
								#$storeTotal = 0;
								foreach($resultsetSelectByStore as $byStore_data) :
									if($lastCompanyId != $byStore_data['company_id']) :
										if($lastCompanyId != 0) : ?>
											<!--<tr>
												<th>Total</th>
												<td><?=$storeTotal?></td>
											</tr>-->
											</tbody>
											</table>
										<?php
											#$storeTotal = 0;
										endif;
										?>
										<span class="company-name"><?=$byStore_data['company']?></span>
									<?php
									endif;
									?>
									<table>
										<tbody>
									<?php
									if($lastStoreId != $byStore_data['store_id']) :
										#if($lastStoreId != 0 && $storeTotal > 0) :
									?>
											<!--<tr>
												<th>Total</th>
												<td><?=$storeTotal?></td>
											</tr>-->
										<!--<?php #endif; ?>-->
										<tr>
											<th colspan="2" class="store-name"><?=$byStore_data['store']?></th>
										</tr>
										<tr>
											<th>Item Description</th>
											<th>Quantity</th>
										</tr>
									<?php
										#$storeTotal = 0;
									endif;
									?>
									<tr>
										<td><?=$byStore_data['description']?></td>
										<td><?=$byStore_data['quantity']?></td>
									</tr>
									<?php
									$lastCompanyId = $byStore_data['company_id'];
									$lastStoreId = $byStore_data['store_id'];
									#$storeTotal += $byStore_data['quantity'];
								endforeach;
								?>
										<!--<tr>
											<th>Total</th>
											<td><?=$storeTotal?></td>
										</tr>-->
									</tbody>
								</table>
							</section>
						<?php
						else :
						?>
							<span class="content-description">There is no products to be produced!</span>
						<?php
						endif;
						?>
					</main>
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
