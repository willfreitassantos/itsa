<?php
require_once '../user/check-authorization.php';
require_once '../../../vendor/autoload.php';

use ITSA\DAO\OrderDAO;
use ITSA\DAO\CompanyDAO;
use ITSA\DAO\ProductDAO;

if($_SERVER['REQUEST_METHOD'] == 'GET') :
	if(isset($_GET['order-id'])) :
		$orderId = $_GET['order-id'];
		$orderToUpdate = null;
		if(isset($_SESSION['order-to-update'])) :
			$orderAux = unserialize($_SESSION['order-to-update']);
			if($orderAux->getOrderId() == $orderId) :
				$orderToUpdate = $orderAux;
			else :
				$orderToUpdate = OrderDAO::selectBy($orderId);
			endif;
		else :
			$orderToUpdate = OrderDAO::selectBy($orderId);
		endif;
		$_SESSION['order-to-update'] = serialize($orderToUpdate);

		$json_data = array();

		$orderCompany = CompanyDAO::selectByOrder($orderToUpdate);
		$products_available = ProductDAO::listAllAvailableBy($orderCompany);
		$json_data['products_available'] = $products_available;

		$productsOrderedId = '';
		foreach($orderToUpdate->getOrderItems() as $orderItem) :
			$productsOrderedId .= '(' . $orderItem->getProduct()->getProductId() . ')';
		endforeach;
		$json_data['products_ordered_id'] = $productsOrderedId;

		$json_data['products_selected_already'] = count($orderToUpdate->getOrderItems());

		echo json_encode($json_data);
		die();
	else :
		throw new Exception('Bad request!');
	endif;
else :
	throw new Exception('Invalid HTTP method request!');
endif;
