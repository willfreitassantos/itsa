<?php
namespace ITSA\DAO;

use ITSA\Model\Company;
use ITSA\DAO\ConnectionManager;
use ITSA\Util\Factories\StoreFactory;

class StoreDAO {

	public static function selectBy(Company $company) {
		$query = "SELECT store_id, name, short_name FROM stores WHERE company_id = :company_id ORDER BY name";
		$conn = ConnectionManager::getConnection();
		$stmt = $conn->prepare($query);
		$stmt->bindValue(':company_id', $company->getCompanyId());
		$stmt->execute();
		$resultset = $stmt->fetchAll();
		return StoreFactory::createArrayFrom($resultset, $company);
	}

	public static function selectById($store_id) {
		$query = "SELECT store_id, company_id, name, short_name FROM stores WHERE store_id = :store_id";
		$conn = ConnectionManager::getConnection();
		$stmt = $conn->prepare($query);
		$stmt->bindValue(':store_id', $store_id);
		$stmt->execute();
		$resultset = $stmt->fetch();
		return StoreFactory::createFrom($resultset);
	}

	public static function selectByStoreCode($storeCode) {
		$query = "SELECT * FROM stores WHERE store_code = :store_code";
		$conn = ConnectionManager::getConnection();
		$stmt = $conn->prepare($query);
		$stmt->bindValue(':store_code', $storeCode);
		$stmt->execute();
		$resultset = $stmt->fetch();
		$num_rows = $stmt->rowcount();
		if ($num_rows > 0) {
			return StoreFactory::createFrom($resultset);
		}
		else {
			return null;
		}
	}

}
