<?php
require_once '../user/check-authorization.php';
require_once '../../../vendor/autoload.php';

use ITSA\DAO\OrderDAO;

$loggedUser = unserialize($_SESSION['logged_user']);

if($_SERVER['REQUEST_METHOD'] == 'POST') :
	if(isset($_POST['searchParam'])) :
		$previousOrdersSearchParam = $_POST['searchParam'];
		$previousOrders = OrderDAO::selectPrevious($loggedUser, $previousOrdersSearchParam);
		$_SESSION['previousOrdersSearchParam'] = $previousOrdersSearchParam;
		echo json_encode($previousOrders);
		die();
	else :
		throw new Exception('Bad request!');
	endif;
else :
	throw new Exception('Invalid HTTP method request!');
endif;
