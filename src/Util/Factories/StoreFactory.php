<?php
namespace ITSA\Util\Factories;

use ITSA\Model\Store;
use ITSA\Model\Company;
use ITSA\DAO\CompanyDAO;

class StoreFactory
{

	public static function createFrom($resultset)
	{
		$store = new Store();
		$store->setStoreId($resultset['store_id']);
		$store->setCompany(CompanyDAO::selectBy($resultset['company_id']));
		$store->setName($resultset['name']);
		$store->setShortName($resultset['short_name']);
		return $store;
	}

	public static function createArrayFrom($resultset, Company $company = null)
	{
		$stores = array();
			foreach ($resultset as $store_data) :
				$store = new Store();
				$store->setStoreId($store_data['store_id']);
				$store->setCompany($company);
				$store->setName($store_data['name']);
				$store->setShortName($store_data['short_name']);
				array_push($stores, $store);
			endforeach;
		return $stores;
	}
}
