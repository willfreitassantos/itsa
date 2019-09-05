<?php
require_once '../../../vendor/autoload.php';

if(!isset($_SESSION['new_order_info']) || count(unserialize($_SESSION['new_order_info'])->getOrderItems()) <= 0) :
	$_SESSION['no_products_selected'] = 'There are no products selected...';
	header('Location: ../orders/new');
	die();
endif;