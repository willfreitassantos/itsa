<?php
require_once '../user/check-authorization.php';
require_once '../../../vendor/autoload.php';

use ITSA\DAO\StoreDAO;
use ITSA\DAO\CompanyDAO;
use ITSA\DAO\ProductDAO;
use ITSA\Model\Order;

if($_SERVER['REQUEST_METHOD'] == 'POST') {
	if(isset($_POST['company_id']) && isset($_POST['store_id'])) {
		$requestCompanyId = $_POST['company_id'];
		$requestStoreId = $_POST['store_id'];

		$selectedStore = unserialize($_SESSION['selected_store']);

		$sessionCompanyId = $selectedStore->getCompany()->getCompanyId();
		$sessionStoreId = $selectedStore->getStoreId();

		$jsonResponse = array();

		#This case occurs only when...
		if($requestStoreId == null) {
			#the page is loaded or...
			if($requestCompanyId == $sessionCompanyId) {
				$jsonResponse['stores'] = $selectedStore->getCompany()->getStores();
				$jsonResponse['selected_store_id'] = $sessionStoreId;
			} else { #when the company is changed
				$company = CompanyDAO::selectBy($requestCompanyId);
				$jsonResponse['stores'] = $company->getStores();
				$jsonResponse['selected_store_id'] = $company->getStores()[0]->getStoreId();
				$jsonResponse['products'] = ProductDAO::listAllAvailableBy($company);
				$_SESSION['selected_store'] = serialize($company->getStores()[0]);
				
				$loggedUser = unserialize($_SESSION['logged_user']);
				$newOrderInfo = new Order();
				$newOrderInfo->setUser($loggedUser);
				$_SESSION['new_order_info'] = serialize($newOrderInfo);
			}
		} else {
			$store = StoreDAO::selectById($requestStoreId);
			$_SESSION['selected_store'] = serialize($store);
			$jsonResponse['selected_store_id'] = $store->getStoreId();
		}

		echo json_encode($jsonResponse);
		die();
	}
} else {
	http_response_code(405);
	die();
}
http_response_code(400);
die();
