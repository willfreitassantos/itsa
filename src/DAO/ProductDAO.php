<?php
namespace ITSA\DAO;

use ITSA\DAO\ConnectionManager;
use ITSA\Model\Company;
use ITSA\Util\Factories\ProductFactory;
use Exception;

class ProductDAO
{

	public static function selectBy($product_id)
	{
		$query = "SELECT product_id, description, photo_path, available FROM products WHERE product_id = :product_id";
		$conn = ConnectionManager::getConnection();
		$stmt = $conn->prepare($query);
		$stmt->bindValue(':product_id', $product_id);
		$stmt->execute();
		$resultset = $stmt->fetch();
		return ProductFactory::createFrom($resultset);
	}

	public static function selectByProductCodes($product_id_external)
	{
		$query = "SELECT P.product_id, P.description, P.photo_path, P.available ";
		$query .= "FROM products P ";
		$query .= "INNER JOIN productcodes PC ON P.product_id = PC.product_id ";
		$query .= "WHERE PC.product_id_external = :product_id_external";
		$conn = ConnectionManager::getConnection();
		$stmt = $conn->prepare($query);
		$stmt->bindValue(':product_id_external', $product_id_external);
		$stmt->execute();
		$resultset = $stmt->fetch();
		$num_rows = $stmt->rowcount();
		if ($num_rows > 0) {
			return ProductFactory::createFrom($resultset);
		}
		else {
			return null;
		}
	}

	public static function listAllAvailableBy(Company $company)
	{
		$query = "SELECT product_id, description, photo_path, available FROM products WHERE available = :available AND company_id = :company_id ORDER BY description";
		try {
			$conn = ConnectionManager::getConnection();
			$stmt = $conn->prepare($query);
			$stmt->bindValue(':available', true);
			$stmt->bindValue(':company_id', $company->getCompanyId());
			$stmt->execute();
			$resultset = $stmt->fetchAll();
			return ProductFactory::createArrayFrom($resultset);
		} catch (Exception $ex) {
			return null;
		}
	}

	public static function listAll()
	{
		$query = "SELECT product_id, description, photo_path, available FROM products ORDER BY description";
		try{
		$conn = ConnectionManager::getConnection();
		$stmt = $conn->prepare($query);
		$stmt->execute();
		$resultset = $stmt->fetchAll();
		return ProductFactory::createArrayFrom($resultset);
		} catch (Exception $ex) {
			echo $ex->getMessage();
			die();
		}
	}
}
