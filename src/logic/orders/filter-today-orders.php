<?php
require_once '../user/check-authorization.php';
require_once '../../../vendor/autoload.php';

use ITSA\DAO\OrderDAO;

$loggedUser = unserialize($_SESSION['logged_user']);

if($_SERVER['REQUEST_METHOD'] == 'POST') :
	if(isset($_POST['searchParam'])) :
		$todayOrdersSearchParam = $_POST['searchParam'];
		$todayOrders = OrderDAO::selectTodays($loggedUser, $todayOrdersSearchParam);
		$_SESSION['todayOrdersSearchParam'] = $todayOrdersSearchParam != '' ? $todayOrdersSearchParam : null;
		echo json_encode($todayOrders);
		die();
	else :
		throw new Exception('Bad request!');
	endif;
else :
	throw new Exception('Invalid HTTP method request!');
endif;
