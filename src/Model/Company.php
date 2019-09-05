<?php
namespace ITSA\Model;

class Company
{

	private $company_id;
	private $name;
	private $stores;

	public function __construct()
	{
		$stores = array();
	}

	public function getCompanyId()
	{
		return $this->company_id;
	}

	public function setCompanyId($company_id)
	{
		$this->company_id = $company_id;
	}

	public function getName()
	{
		return $this->name;
	}

	public function setName($name)
	{
		$this->name = $name;
	}

	public function getStores()
	{
		return $this->stores;
	}

	public function setStores($stores)
	{
		$this->stores = $stores;
	}

	public function add(Store $store)
	{
		$this->stores[$store->getStoreId()] = $store;
	}

	public function remove(Store $store)
	{
		$this->stores = array_filter($this->stores, function($key) use ($store) {
			return $key != $store->getStoreId();
		}, ARRAY_FILTER_USE_KEY);
	}
}
