<?php
require_once '../user/check-authorization.php';
require_once '../../../vendor/autoload.php';

use ITSA\Util\DateTimeUtils;
use ITSA\DAO\OrderDAO;

$orderToUpdate = unserialize($_SESSION['order-to-update']);

if($_SERVER['REQUEST_METHOD'] == 'POST') :
	if(isset($_POST['client_name']) && isset($_POST['po_number']) && isset($_POST['delivery_date']) && isset($_POST['client_comments']) && isset($_POST['order_item_qty'])):
		$client_name = $_POST['client_name'];
		$po_number = $_POST['po_number'];
		$delivery_date = $_POST['delivery_date'];
		$client_comments = $_POST['client_comments'];
		$order_item_qty = $_POST['order_item_qty'];

		$orderToUpdateIssue = '';
		$json_data = array();
		$orderItemQuantityIssue = array();
		
		#quantity validation
		$orderItemsToUpdate = array();
		foreach($orderToUpdate->getOrderItems() as $orderItem) :
			$orderItemsToUpdate[$orderItem->getProduct()->getProductId()] = $orderItem;
		endforeach;
		$quantities = explode(';', $order_item_qty);
		foreach($quantities as $quantity) {
			$productId = explode(',', $quantity)[0];
			$productQty = explode(',', $quantity)[1];

			$orderItem = $orderItemsToUpdate[$productId];
			
			if(empty($productQty)) {
				$orderItemQuantityIssue['empty'] = '<li>There are empty quantity fields</li>';
			} else if(is_nan($productQty)) {
				$orderItemQuantityIssue['nan'] = '<li>There are invalid quantity fields</li>';
			} else {
				$orderItem->setQuantity($productQty);
			}
		}
		$orderToUpdate->setOrderItems(array());
		foreach($orderItemsToUpdate as $orderItem) :
			$orderToUpdate->addWithoutSerialization($orderItem);
		endforeach;

		if(count($orderItemQuantityIssue) > 0) {
			foreach($orderItemQuantityIssue as $quantityIssue) {
				$orderToUpdateIssue .= $quantityIssue;
			}
		}

		#client_name validation
		if(empty($client_name)) {
			$orderToUpdateIssue .= '<li>The field name is empty</li>';
		} else if(strlen($client_name) > 200) {
			$orderToUpdateIssue .= '<li>The field name has more than 200 characters</li>';
		} else {
			$orderToUpdate->setClientName($client_name);
		}

		#po_number validation
		if(empty($po_number)) {
			$orderToUpdateIssue .= '<li>The field PO number is empty</li>';
		} else if(strlen($po_number) > 100) {
			$orderToUpdateIssue .= '<li>The field PO number has more than 100 characters</li>';
		} else {
			$orderToUpdate->setPoNumber($po_number);
		}

		#delivery_date validation
		if(empty($delivery_date)) {
			$orderToUpdateIssue .= '<li>The field delivery date is empty</li>';
		} else if(!DateTimeUtils::isValid($delivery_date)) {
			$orderToUpdateIssue .= '<li>The delivery date is not a valid date</li>';
		} else {
			$ddMMyy = explode('/', $delivery_date);
			$orderToUpdate->setDeliveryDate(date('d/m/y', strtotime($ddMMyy[1] . '/' . $ddMMyy[0] . '/' . $ddMMyy[2])));
		}

		#client_comments validation
		if(!empty($client_comments) && strlen($client_comments) > 200) {
			$orderToUpdateIssue .= '<li>The field comments has more than 200 characters</li>';
		} else {
			$orderToUpdate->setComments($client_comments);
		}


		if(!empty($orderToUpdateIssue)) {
			$_SESSION['order-to-update'] = serialize($orderToUpdate);
			$json_data['hasErrors'] = true;
			$json_data['errors'] = $orderToUpdateIssue;
		} else {
			unset($_SESSION['order-to-update']);
			OrderDAO::update($orderToUpdate);
			$json_data['hasErrors'] = false;
		}

		echo json_encode($json_data);
		die();
	endif;
	throw new Exception('Bad Request!');
	die();
else :
	throw new Exception('Invalid method!');
	die();
endif;
