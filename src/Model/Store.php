<?php
namespace ITSA\Model;

use JsonSerializable;

class Store implements JsonSerializable
{

	private $store_id;
	private $company;
	private $name;
	private $short_name;

	public function getStoreId()
	{
		return $this->store_id;
	}

	public function setStoreId($store_id)
	{
		$this->store_id = $store_id;
	}

	public function getCompany()
	{
		return $this->company;
	}

	public function setCompany(Company $company)
	{
		$this->company = $company;
	}

	public function getName()
	{
		return $this->name;
	}

	public function setName($name)
	{
		$this->name = $name;
	}

	public function getShortName()
	{
		return $this->short_name;
	}

	public function setShortName($short_name)
	{
		$this->short_name = $short_name;
	}

	public function jsonSerialize()
	{
        return 
        [
            'store_id'   => $this->store_id,
            'name' => $this->name
        ];
    }
}
