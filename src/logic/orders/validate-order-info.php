<?php
require_once '../user/check-authorization.php';
require_once 'validate-new-order.php';
require_once '../../../vendor/autoload.php';

use ITSA\Util\DateTimeUtils;

$newOrderInfo = unserialize($_SESSION['new_order_info']);

if($_SERVER['REQUEST_METHOD'] == 'POST') :
	if(isset($_POST['client_name']) && isset($_POST['po_number']) && isset($_POST['delivery_date']) && isset($_POST['client_comments']) && isset($_POST['order_item_qty'])):
		$client_name = $_POST['client_name'];
		$po_number = $_POST['po_number'];
		$delivery_date = $_POST['delivery_date'];
		$client_comments = $_POST['client_comments'];
		$order_item_qty = $_POST['order_item_qty'];

		$orderItemIssue = '';
		$orderItemQuantityIssue = array();
		
		#quantity validation
		$quantities = explode(';', $order_item_qty);
		foreach($quantities as $quantity) {
			$productId = explode(',', $quantity)[0];
			$productQty = explode(',', $quantity)[1];
			$orderItem = unserialize($newOrderInfo->getOrderItems()[$productId]);
			
			if(empty($productQty)) {
				$orderItemQuantityIssue['empty'] = '<li>There are empty quantity fields</li>';
				$orderItem->setQuantity(0);
			} else if(is_nan($productQty)) {
				$orderItemQuantityIssue['nan'] = '<li>There are invalid quantity fields</li>';
				$orderItem->setQuantity($productQty);
			} else {
				$orderItem->setQuantity($productQty);
			}

			$newOrderInfo->add($orderItem);
		}

		if(count($orderItemQuantityIssue) > 0) {
			foreach($orderItemQuantityIssue as $quantityIssue) {
				$orderItemIssue .= $quantityIssue;
			}
		}

		#client_name validation
		if(empty($client_name)) {
			$orderItemIssue .= '<li>The field name is empty</li>';
			$newOrderInfo->setClientName('');
		} else if(strlen($client_name) > 200) {
				$orderItemIssue .= '<li>The field name has more than 200 characters</li>';
				$newOrderInfo->setClientName($client_name);
		} else {
			$newOrderInfo->setClientName($client_name);
		}

		#po_number validation
		if(empty($po_number)) {
			$orderItemIssue .= '<li>The field PO number is empty</li>';
			$newOrderInfo->setPoNumber('');
		} else if(strlen($po_number) > 100) {
			$orderItemIssue .= '<li>The field PO number has more than 100 characters</li>';
			$newOrderInfo->setPoNumber($po_number);
		} else {
			$newOrderInfo->setPoNumber($po_number);
		}

		#delivery_date validation
		if(empty($delivery_date)) {
			$orderItemIssue .= '<li>The field delivery date is empty</li>';
			$newOrderInfo->setDeliveryDate('');
		} else if(!DateTimeUtils::isValid($delivery_date)) {
			$orderItemIssue .= '<li>The delivery date is not a valid date</li>';
			$newOrderInfo->setDeliveryDate($delivery_date);
		} else if(!DateTimeUtils::isGreaterThanToday($delivery_date)) {
			$orderItemIssue .= '<li>The delivery date must be later than the current date</li>';
			$newOrderInfo->setDeliveryDate($delivery_date);
		} else {
			$newOrderInfo->setDeliveryDate($delivery_date);
		}

		#client_comments validation
		$newOrderInfo->setComments('');
		if(!empty($client_comments) && strlen($client_comments) > 200) {
			$orderItemIssue .= '<li>The field comments has more than 200 characters</li>';
			$newOrderInfo->setComments($client_comments);
		} else {
			$newOrderInfo->setComments($client_comments);
		}

		$_SESSION['new_order_info'] = serialize($newOrderInfo);

		if(!empty($orderItemIssue)) {
			$_SESSION['order_item_issue'] = $orderItemIssue;
			header('Location: ../../orders/info');
			die();
		}

		$_SESSION['new_order_info'] = serialize($newOrderInfo);
		header('Location: ../../orders/complete');
		die();
	endif;
endif;

header('Location: ../../orders/info');
die();
