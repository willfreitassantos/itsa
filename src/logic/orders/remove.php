<?php
require_once '../../../vendor/autoload.php';

use ITSA\DAO\OrderDAO;

if($_SERVER['REQUEST_METHOD'] == 'GET') :
	if(isset($_GET['order-id'])) :
		$orderId = $_GET['order-id'];
		OrderDAO::removeBy($orderId);
		die();
	else :
		throw new Exception('Bad request!');
	endif;
else :
	throw new Exception('Invalid HTTP method request!');
endif;
