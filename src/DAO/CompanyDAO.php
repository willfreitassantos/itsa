<?php
namespace ITSA\DAO;

use ITSA\DAO\ConnectionManager;
use ITSA\Util\Factories\CompanyFactory;
use ITSA\Model\Order;

class CompanyDAO
{

	public static function selectBy($company_id)
	{
		$query = "SELECT company_id, name FROM companies WHERE company_id = :company_id";
		$conn = ConnectionManager::getConnection();
		$stmt = $conn->prepare($query);
		$stmt->bindValue(':company_id', $company_id);
		$stmt->execute();
		$resultset = $stmt->fetch();
		return CompanyFactory::createFrom($resultset);
	}

	public static function selectByOrder(Order $order)
	{
		$query = "SELECT c.company_id, c.name FROM orders AS o INNER JOIN stores AS s ON o.store_id = s.store_id INNER JOIN companies AS c ON s.company_id = c.company_id WHERE o.order_id = :order_id";
		$conn = ConnectionManager::getConnection();
		$stmt = $conn->prepare($query);
		$stmt->bindValue(':order_id', $order->getOrderId());
		$stmt->execute();
		$resultset = $stmt->fetch();
		return CompanyFactory::createFrom($resultset);
	}

	public static function listAll()
	{
		$query = "SELECT company_id, name FROM companies ORDER BY name";
		$conn = ConnectionManager::getConnection();
		$stmt = $conn->prepare($query);
		$stmt->execute();
		$resultset = $stmt->fetchAll();
		return CompanyFactory::createArrayFrom($resultset);
	}
}
