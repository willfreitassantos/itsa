<?php
require_once '../user/check-authorization.php';
require_once '../../../vendor/autoload.php';

use ITSA\DAO\ProductDAO;
use ITSA\Model\OrderItems;

if($_SERVER['REQUEST_METHOD'] == 'POST') :
	if(isset($_POST['action']) && isset($_POST['product_id'])) :
		$action = $_POST['action'];
		$product_id = (int) $_POST['product_id'];

		$newOrderInfo = unserialize($_SESSION['new_order_info']);

		$product = ProductDAO::selectBy($product_id);
		
		$orderItems = new OrderItems();
		$orderItems->setProduct($product);

		if($action == 'add') :
			$newOrderInfo->add($orderItems);
		else :
			$newOrderInfo->remove($orderItems);
		endif;

		$_SESSION['new_order_info'] = serialize($newOrderInfo);

		echo json_encode(count($newOrderInfo->getOrderItems()));
		http_response_code(200);
		die();
	endif;
else :
	http_response_code(405);
	die();
endif;
http_response_code(400);
