<?php
require_once '../../../vendor/autoload.php';

use ITSA\Util\DateTimeUtils;

$newOrderInfo = unserialize($_SESSION['new_order_info']);

$orderItemIssue = '';
$orderItemQuantityIssue = array();

#order_item_quantity validation
foreach($newOrderInfo->getOrderItems() as $orderItem) {
	$productQty = unserialize($orderItem)->getQuantity();
			
	if(empty($productQty)) {
		$orderItemQuantityIssue['empty'] = '<li>There are empty quantity fields</li>';
	} else if(is_nan($productQty)) {
		$orderItemQuantityIssue['nan'] = '<li>There are invalid quantity fields</li>';
	}
}

if(count($orderItemQuantityIssue) > 0) {
	foreach($orderItemQuantityIssue as $quantityIssue) {
		$orderItemIssue .= $quantityIssue;
	}
}

#client_name validation
if(empty($newOrderInfo->getClientName())) {
	$orderItemIssue .= '<li>The field name is empty</li>';
} else if(strlen($newOrderInfo->getClientName()) > 200) {
	$orderItemIssue .= '<li>The field name has more than 200 characters</li>';
}

#po_number validation
if(empty($newOrderInfo->getPoNumber())) {
	$orderItemIssue .= '<li>The field PO number is empty</li>';
} else if(strlen($newOrderInfo->getPoNumber()) > 100) {
	$orderItemIssue .= '<li>The field PO number has more than 100 characters</li>';
}

#delivery_date validation
if(empty($newOrderInfo->getDeliveryDate())) {
	$orderItemIssue .= '<li>The field delivery date is empty</li>';
} else if(!DateTimeUtils::isValid($newOrderInfo->getDeliveryDate())) {
	$orderItemIssue .= '<li>The delivery date is not a valid date</li>';
} else if(!DateTimeUtils::isGreaterThanToday($newOrderInfo->getDeliveryDate())) {
	$orderItemIssue .= '<li>The delivery date must be later than the current date</li>';
}

#client_comments validation
if(!empty($newOrderInfo->getComments()) && strlen($newOrderInfo->getComments()) > 200) {
	$orderItemIssue .= '<li>The field comments has more than 200 characters</li>';
}

if(!empty($orderItemIssue)) {
	$_SESSION['order_item_issue'] = $orderItemIssue;
	header('Location: ../orders/info');
	die();
}
