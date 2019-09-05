<?php
namespace ITSA\Util\Factories;

use ITSA\Model\Company;
use ITSA\DAO\StoreDAO;

class CompanyFactory
{

	public static function createFrom($resultset)
	{
		$company = new Company();
		$company->setCompanyId($resultset['company_id']);
		$company->setName($resultset['name']);
		$company->setStores(StoreDAO::selectBy($company));
		return $company;
	}

	public static function createArrayFrom($resultset)
	{
		$companies = array();
		foreach ($resultset as $company_data) {
			$company = new Company();
			$company->setCompanyId($company_data['company_id']);
			$company->setName($company_data['name']);
			$company->setStores(StoreDAO::selectBy($company));
			array_push($companies, $company);
		}
		return $companies;
	}
}
