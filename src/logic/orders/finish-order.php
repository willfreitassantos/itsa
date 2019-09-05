<?php
require_once '../user/check-authorization.php';
require_once '../../../vendor/autoload.php';

use ITSA\DAO\OrderDAO;

$newOrder = unserialize($_SESSION['new_order_info']);

$selectedStore = unserialize($_SESSION['selected_store']);
$newOrder->setStore($selectedStore);

$newOrder = OrderDAO::insert($newOrder);

$orderCompletedInfo = array(
	'order_id'		=> $newOrder->getOrderId(),
	'po_number'		=> $newOrder->getPoNumber()
);

unset($_SESSION['new_order_info']);

echo json_encode($orderCompletedInfo);
