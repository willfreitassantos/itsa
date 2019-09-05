<?php
require_once '../../user/check-authorization.php';
require_once '../../../../vendor/autoload.php';

use ITSA\Util\DateTimeUtils;
use ITSA\DAO\ConnectionManager;

$loggedUser = unserialize($_SESSION['logged_user']);

if($loggedUser->isAdmin()) {
	if($_SERVER['REQUEST_METHOD'] == 'POST') {
		if(isset($_POST['dateFrom']) && isset($_POST['dateTo']) && (isset($_POST['chkAllProducts']) || (isset($_POST['chkProducts']) && !empty($_POST['chkProducts'])))) {
			$dateFrom = $_POST['dateFrom'];
			$dateTo = $_POST['dateTo'];

			if(($dateFrom != null && $dateFrom != '' && !DateTimeUtils::isValid($dateFrom, 'd/m/Y')) || ($dateTo != null && $dateTo != '' && !DateTimeUtils::isValid($dateTo, 'd/m/Y'))) {
				header('Location: /itsa/orders/new');
				die();
			}

			$productsInQuery = " ";
			if(!isset($_POST['chkAllProducts']) && (isset($_POST['chkProducts']) && !empty($_POST['chkProducts']))) {
				$products = "";
				$copy = $_POST['chkProducts'];
				foreach ($_POST['chkProducts'] as $product_id) {
					$products .= $product_id;
					if(next($copy)) {
						$products .= ",";
					}
				}
				$productsInQuery = " AND oi.product_id IN (" . $products . ") ";
			}

			$conn = ConnectionManager::getConnection();
			
			$queryTotalProductsProduced = "SELECT o.delivery_date, DATE_FORMAT(o.delivery_date, '%d/%m') AS delivery_date_formatted, SUM(oi.quantity) AS total_products_produced FROM orders AS o INNER JOIN order_items AS oi ON oi.order_id = o.order_id WHERE o.delivery_date BETWEEN :dateFrom AND :dateTo" . $productsInQuery . "GROUP BY o.delivery_date";

			$queryMaxAndMinProdution = "SELECT MAX(virtual_table_1.sum1) AS max_and_min_produced_product FROM (SELECT SUM(oi.quantity) AS sum1 FROM order_items AS oi INNER JOIN orders AS o ON oi.order_id = o.order_id WHERE o.delivery_date = :date1" . $productsInQuery . "GROUP BY oi.product_id) AS virtual_table_1 UNION SELECT MIN(virtual_table_2.sum2) AS max_and_min_produced_product FROM (SELECT oi.product_id, SUM(oi.quantity) AS sum2 FROM order_items AS oi INNER JOIN orders AS o ON oi.order_id = o.order_id WHERE o.delivery_date = :date2" . $productsInQuery . "GROUP BY oi.product_id) AS virtual_table_2";

			$queryTotalProductionByProducts = "SELECT p.description, SUM(oi.quantity) AS total FROM order_items AS oi INNER JOIN orders AS o ON oi.order_id = o.order_id INNER JOIN products AS p ON oi.product_id = p.product_id WHERE o.delivery_date BETWEEN :dateFrom AND :dateTo" . $productsInQuery . "GROUP BY oi.product_id ORDER BY total DESC";

			$stmt = $conn->prepare($queryTotalProductsProduced);
			

			$stmt->bindValue(':dateFrom', date('Y-m-d', strtotime(DateTimeUtils::getDateDB($dateFrom) . ' + 1 days')));
			$stmt->bindValue(':dateTo', date('Y-m-d', strtotime(DateTimeUtils::getDateDB($dateTo) . ' + 1 days')));
			$stmt->execute();
			$resultsetTotalProductsProduced = $stmt->fetchAll();

			if(count($resultsetTotalProductsProduced) > 0) {
				$chart_data = "[";
				$i = 0;
				foreach ($resultsetTotalProductsProduced as $totalProductsProducedArray) {
					$i++;
					$chart_data .= "['" . $totalProductsProducedArray['delivery_date_formatted'] . "', " . $totalProductsProducedArray['total_products_produced'] . ", ";
					$stmt2 = $conn->prepare($queryMaxAndMinProdution);
					$stmt2->bindValue(':date1', $totalProductsProducedArray['delivery_date']);
					$stmt2->bindValue(':date2', $totalProductsProducedArray['delivery_date']);
					$stmt2->execute();
					$resultsetMaxAndMinProduction = $stmt2->fetchAll();
					$j = 0;
					foreach ($resultsetMaxAndMinProduction as $maxAndMinProductionArray) {
						if(count($resultsetMaxAndMinProduction) == 1) {
							$chart_data .= $maxAndMinProductionArray['max_and_min_produced_product'] . ", " . $maxAndMinProductionArray['max_and_min_produced_product'];
							break;
						} else {
							$chart_data .= $maxAndMinProductionArray['max_and_min_produced_product'] . ($j == 0 ? ", " : "");
							$j++;
						}
					}
					$chart_data .= "]" . ($i < count($resultsetTotalProductsProduced) ? "," : "");
				}
				$chart_data .= "]";

				$stmt3 = $conn->prepare($queryTotalProductionByProducts);
				$stmt3->bindValue(':dateFrom', date('Y-m-d', strtotime(DateTimeUtils::getDateDB($dateFrom) . ' + 1 days')));
				$stmt3->bindValue(':dateTo', date('Y-m-d', strtotime(DateTimeUtils::getDateDB($dateTo) . ' + 1 days')));
				$stmt3->execute();
				$resultsetTotalProductionByProducts = $stmt3->fetchAll();
			}
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
					<link rel="stylesheet" href="../../resources/css/period-report.css">
					<?php
						if(count($resultsetTotalProductsProduced) > 0) {
					?>
						<script type="text/javascript" src="../../resources/plugins/google-charts/js/loader.js"></script>
						<script type="text/javascript">
							google.charts.load('current', {packages: ['corechart'], language: 'en-US', callback: drawChart});

							function drawChart() {
								let data = new google.visualization.DataTable();

								data.addColumn('string', 'Period');
								data.addColumn('number', 'Total');
								data.addColumn('number', 'Most produced');
								data.addColumn('number', 'Least produced');

								data.addRows(<?=$chart_data?>);

								let view = new google.visualization.DataView(data);

								view.setColumns([0, 1, {calc: "stringify", sourceColumn: 1, type: "string", role: "annotation"}, 2, {calc: "stringify", sourceColumn: 2, type: "string", role: "annotation"}, 3, {calc: "stringify", sourceColumn: 3, type: "string", role: "annotation"}]);

								let options = {
									annotations: {
										alwaysOutside: true,
										textStyle: {
											fontSize: 14
										}
									},
									legend: {
										position: 'bottom'
									},
									height: 400,
									width: 1000
								};

								let chart = new google.visualization.ColumnChart(document.getElementById('chart_div'));
								chart.draw(view, options);
							}
						</script>
					<?php
					}
					?>
				</head>
				<body>
					<header>
						<div class="delivery-on">
							<h1>Production Report</h1>
							<h2><?=$dateFrom?> - <?=$dateTo?></h2>
						</div><div class="logo">
							<img src="../../resources/images/logo_itsa.png" alt="ITSA logo">
							<a href="https://www.itsa.ie/">www.itsa.ie</a>
						</div>
					</header>
					<main>
						<?php
						if(count($resultsetTotalProductsProduced) > 0) :
						?>
							<div id="chart_div" style="width: 1000px; height: 400px;"></div>
							<section style="text-align: center; margin-top: 15px;">
								<?php
								if(count($resultsetTotalProductionByProducts) > 20) :
								?>
									<section class="total-ordered-items-left" style="width: 400px;display: inline-block;vertical-align: top;">
										<table>
											<thead>
												<tr>
													<th>Item Description</th>
													<th>Quantity</th>
												</tr>
											</thead>
											<tbody>
												<?php
												$i = 0;
												for($i; $i < (count($resultsetTotalProductionByProducts) % 2 == 0 ? count($resultsetTotalProductionByProducts) / 2 : (count($resultsetTotalProductionByProducts) + 1) / 2); $i++) :
												?>
													<tr>
														<td><?=$resultsetTotalProductionByProducts[$i]['description']?></td>
														<td><?=$resultsetTotalProductionByProducts[$i]['total']?></td>
													</tr>
												<?php
												endfor;
												?>
											</tbody>
										</table>
									</section>
									<section class="total-ordered-items-right" style="width: 400px;display: inline-block;vertical-align: top;">
										<table>
											<thead>
												<tr>
													<th>Item Description</th>
													<th>Quantity</th>
												</tr>
											</thead>
											<tbody>
												<?php
												for($i; $i < count($resultsetTotalProductionByProducts); $i++) :
												?>
													<tr>
														<td><?=$resultsetTotalProductionByProducts[$i]['description']?></td>
														<td><?=$resultsetTotalProductionByProducts[$i]['total']?></td>
													</tr>
												<?php
												endfor;
												?>
											</tbody>
										</table>
									</section>
								<?php
								else :
								?>
									<section class="total-ordered-items">
										<table>
											<thead>
												<tr>
													<th>Item Description</th>
													<th>Quantity</th>
												</tr>
											</thead>
											<tbody>
												<?php
												$i = 0;
												foreach($resultsetTotalProductionByProducts as $totalProductionByProductArray) :
												?>
													<tr>
														<td><?=$totalProductionByProductArray['description']?></td>
														<td><?=$totalProductionByProductArray['total']?></td>
													</tr>
												<?php
												endforeach;
												?>
											</tbody>
										</table>
									</section>
								<?php
								endif;
								?>
							</section>
						<?php
						else :
						?>
							<span class="content-description">No data could be found for this period!</span>
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
