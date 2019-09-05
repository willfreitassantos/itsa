<?php
require_once '../user/check-authorization.php';
require_once '../../../vendor/autoload.php';

use ITSA\DAO\OrderDAO;
use ITSA\DAO\CompanyDAO;
use ITSA\DAO\ProductDAO;

if($_SERVER['REQUEST_METHOD'] == 'GET') :
	$orderToUpdate = null;
	if(isset($_SESSION['order-to-update'])) {
		$json_data = array();
		$orderToUpdate = unserialize($_SESSION['order-to-update']);
		if(count($orderToUpdate->getOrderItems()) > 0) {
			$json_data['hasProductsSelected'] = true;
			$json_data['orderToUpdate'] = $orderToUpdate;
		} else {
			$json_data['hasProductsSelected'] = false;
		}
		echo json_encode($json_data);
		die();
	} else {
		throw new Exception('The order to update is not set in session!');
		die();
	}
else :
	throw new Exception('Invalid HTTP method request!');
	die();
endif;
