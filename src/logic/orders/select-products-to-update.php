<?php
require_once '../user/check-authorization.php';
require_once '../../../vendor/autoload.php';

use ITSA\DAO\ProductDAO;
use ITSA\Model\OrderItems;

if($_SERVER['REQUEST_METHOD'] == 'POST') :
	if(isset($_POST['action']) && isset($_POST['product_id'])) :
		$action = $_POST['action'];
		$product_id = (int) $_POST['product_id'];

		$orderToUpdate = unserialize($_SESSION['order-to-update']);

		$product = ProductDAO::selectBy($product_id);
		
		$orderItems = new OrderItems();
		$orderItems->setProduct($product);

		if($action == 'add') :
			$orderToUpdate->addWithoutSerialization($orderItems);
		else :
			$orderToUpdate->removeWithoutSerialization($orderItems);
		endif;

		$_SESSION['order-to-update'] = serialize($orderToUpdate);

		echo json_encode(count($orderToUpdate->getOrderItems()));
		http_response_code(200);
		die();
	endif;
else :
	http_response_code(405);
	die();
endif;
http_response_code(400);
