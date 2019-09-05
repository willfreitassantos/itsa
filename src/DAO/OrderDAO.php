<?php
namespace ITSA\DAO;

use ITSA\Model\Order;
use ITSA\DAO\ConnectionManager;
use ITSA\DAO\OrderItemsDAO;
use ITSA\Model\User;
use ITSA\Util\Factories\OrderFactory;
use PDO;

class OrderDAO
{

	public static function insert(Order $newOrder)
	{
		$query = "INSERT INTO orders (user_id, store_id, order_status_id, date, client_name, po_number, delivery_date, comments) VALUES (:user_id, :store_id, :order_status_id, :date, :client_name, :po_number, :delivery_date, :comments)";
		$conn = ConnectionManager::getConnection();
		$stmt = $conn->prepare($query);
		$stmt->bindValue(':user_id', $newOrder->getUser()->getId());
		$stmt->bindValue(':store_id', $newOrder->getStore()->getStoreId());
		$stmt->bindValue(':order_status_id', 1); #Not Started
		$stmt->bindValue(':date', date('Y-m-d H:i:s'));
		$stmt->bindValue(':client_name', $newOrder->getClientName());
		$stmt->bindValue(':po_number', $newOrder->getPoNumber());
		$stmt->bindValue(':delivery_date', $newOrder->getDeliveryDateDB());
		$stmt->bindValue(':comments', (empty($newOrder->getComments()) ? null : $newOrder->getComments()));
		$stmt->execute();

		$orderId = $conn->lastInsertId();
		$newOrder->setOrderId($orderId);

		OrderItemsDAO::insertAll($newOrder->getOrderItems(), $orderId);

		return $newOrder;
	}

	public static function update(Order $order)
	{
		$query = "UPDATE orders SET client_name = :client_name, po_number = :po_number, delivery_date = :delivery_date, comments = :comments WHERE order_id = :order_id";
		$conn = ConnectionManager::getConnection();
		$stmt = $conn->prepare($query);
		$stmt->bindValue(':client_name', $order->getClientName());
		$stmt->bindValue(':po_number', $order->getPoNumber());
		$stmt->bindValue(':delivery_date', $order->getDeliveryDateDB());
		$stmt->bindValue(':comments', (empty($order->getComments()) ? null : $order->getComments()));
		$stmt->bindValue(':order_id', $order->getOrderId());
		$stmt->execute();
		OrderItemsDAO::removeBy($order->getOrderId());
		OrderItemsDAO::insertAllUnserialized($order->getOrderItems(), $order->getOrderId());
	}

	public static function selectBy($orderId)
	{
		$query = "SELECT DATE_FORMAT(o.date, '%d/%m/%Y') AS date, o.order_id, o.po_number, s.description AS status, o.client_name, o.comments, DATE_FORMAT(o.delivery_date, '%m/%d/%y') AS delivery_date, u.user_id, u.name AS user_name FROM orders AS o INNER JOIN users AS u ON o.user_id = u.user_id INNER JOIN order_status AS s ON o.order_status_id = s.order_status_id WHERE o.order_id = :order_id";
		try {
			$conn = ConnectionManager::getConnection();
			$stmt = $conn->prepare($query);
			$stmt->bindValue(':order_id', $orderId);
			$stmt->execute();
			$resultSet = $stmt->fetch();
			return OrderFactory::createOrderFrom($resultSet);
		} catch (Exception $ex) {
			return null;
		}
	}

	public static function selectPrevious(User $user, $searchParam = null)
	{
		if($user->isAdmin()) {
			if($searchParam != null) {
				return self::filterAllPreviousOrders($searchParam);
			} else {
				return self::selectAllPreviousOrders();
			}
		} else {
			if($searchParam != null) {
				return self::filterPreviousOrdersBy($user->getStoreId(), $searchParam);
			} else {
				return self::selectPreviousOrdersBy($user->getStoreId());
			}
		}
	}

	private function filterAllPreviousOrders($searchParam)
	{
		$query = "SELECT DATE_FORMAT(o.date, '%d/%m/%Y') AS date, o.order_id, o.po_number, s.description AS status FROM orders AS o INNER JOIN users AS u ON o.user_id = u.user_id INNER JOIN order_status AS s ON o.order_status_id = s.order_status_id WHERE DATE(o.delivery_date) <> DATE_ADD(CURRENT_DATE(), INTERVAL 1 DAY) AND (o.po_number LIKE :po_number OR DATE_FORMAT(o.delivery_date, '%d/%m/%Y') LIKE :delivery_date) ORDER BY o.date DESC";
		try {
			$conn = ConnectionManager::getConnection();
			$stmt = $conn->prepare($query);
			$stmt->bindValue(':po_number', '%'.$searchParam.'%', PDO::PARAM_STR);
			$stmt->bindValue(':delivery_date', '%'.$searchParam.'%', PDO::PARAM_STR);
			$stmt->execute();
			$resultSet = $stmt->fetchAll();
			return OrderFactory::createPreviousAndTodaysOrdersArrayFrom($resultSet);
		} catch (Exception $ex) {
			return null;
		}
	}

	private function selectAllPreviousOrders()
	{
		$query = "SELECT DATE_FORMAT(o.date, '%d/%m/%Y') AS date, o.order_id, o.po_number, s.description AS status FROM orders AS o INNER JOIN users AS u ON o.user_id = u.user_id INNER JOIN order_status AS s ON o.order_status_id = s.order_status_id WHERE DATE(o.delivery_date) <> DATE_ADD(CURRENT_DATE(), INTERVAL 1 DAY) ORDER BY o.date DESC";
		try {
			$conn = ConnectionManager::getConnection();
			$stmt = $conn->prepare($query);
			$stmt->execute();
			$resultSet = $stmt->fetchAll();
			return OrderFactory::createPreviousAndTodaysOrdersArrayFrom($resultSet);
		} catch (Exception $ex) {
			return null;
		}
	}

	private function filterPreviousOrdersBy($store_id, $searchParam)
	{
		$query = "SELECT DATE_FORMAT(o.date, '%d/%m/%Y') AS date, o.order_id, o.po_number, s.description AS status FROM orders AS o INNER JOIN users AS u ON o.user_id = u.user_id INNER JOIN order_status AS s ON o.order_status_id = s.order_status_id WHERE u.store_id = :store_id AND DATE(o.delivery_date) <> DATE_ADD(CURRENT_DATE(), INTERVAL 1 DAY) AND (o.po_number LIKE :po_number OR DATE_FORMAT(o.delivery_date, '%d/%m/%Y') LIKE :delivery_date) ORDER BY o.date DESC";
		try {
			$conn = ConnectionManager::getConnection();
			$stmt = $conn->prepare($query);
			$stmt->bindValue(':store_id', $store_id);
			$stmt->bindValue(':po_number', '%'.$searchParam.'%', PDO::PARAM_STR);
			$stmt->bindValue(':delivery_date', '%'.$searchParam.'%', PDO::PARAM_STR);
			$stmt->execute();
			$resultSet = $stmt->fetchAll();
			return OrderFactory::createPreviousAndTodaysOrdersArrayFrom($resultSet);
		} catch (Exception $ex) {
			return null;
		}
	}

	private function selectPreviousOrdersBy($store_id)
	{
		$query = "SELECT DATE_FORMAT(o.date, '%d/%m/%Y') AS date, o.order_id, o.po_number, s.description AS status FROM orders AS o INNER JOIN users AS u ON o.user_id = u.user_id INNER JOIN order_status AS s ON o.order_status_id = s.order_status_id WHERE u.store_id = :store_id AND DATE(o.delivery_date) <> DATE_ADD(CURRENT_DATE(), INTERVAL 1 DAY) ORDER BY o.date DESC";
		try {
			$conn = ConnectionManager::getConnection();
			$stmt = $conn->prepare($query);
			$stmt->bindValue(':store_id', $store_id);
			$stmt->execute();
			$resultSet = $stmt->fetchAll();
			return OrderFactory::createPreviousAndTodaysOrdersArrayFrom($resultSet);
		} catch (Exception $ex) {
			return null;
		}
	}

	public static function selectTodays(User $user, $searchParam = null)
	{
		if($user->isAdmin()) {
			if($searchParam != null) {
				return self::filterAllTodayOrders($searchParam);
			} else {
				return self::selectAllTodayOrders();
			}
		} else {
			if($searchParam != null) {
				return self::filterTodayOrdersBy($user->getStoreId(), $searchParam);
			} else {
				return self::selectTodayOrdersBy($user->getStoreId());
			}
		}
	}

	private function filterAllTodayOrders($searchParam)
	{
		$query = "SELECT DATE_FORMAT(o.date, '%d/%m/%Y') AS date, o.order_id, o.po_number, s.description AS status FROM orders AS o INNER JOIN users AS u ON o.user_id = u.user_id INNER JOIN order_status AS s ON o.order_status_id = s.order_status_id WHERE DATE(o.delivery_date) = DATE_ADD(CURRENT_DATE(), INTERVAL 1 DAY) AND (o.po_number LIKE :po_number OR DATE_FORMAT(o.delivery_date, '%d/%m/%Y') LIKE :delivery_date) ORDER BY o.date DESC";
		try {			
			$conn = ConnectionManager::getConnection();
			$stmt = $conn->prepare($query);
			$stmt->bindValue(':po_number', '%'.$searchParam.'%', PDO::PARAM_STR);
			$stmt->bindValue(':delivery_date', '%'.$searchParam.'%', PDO::PARAM_STR);
			$stmt->execute();
			$resultSet = $stmt->fetchAll();
			return OrderFactory::createPreviousAndTodaysOrdersArrayFrom($resultSet);
		} catch (Exception $ex) {
			return null;
		}
	}

	private function selectAllTodayOrders()
	{
		$query = "SELECT DATE_FORMAT(o.date, '%d/%m/%Y') AS date, o.order_id, o.po_number, s.description AS status FROM orders AS o INNER JOIN users AS u ON o.user_id = u.user_id INNER JOIN order_status AS s ON o.order_status_id = s.order_status_id WHERE DATE(o.delivery_date) = DATE_ADD(CURRENT_DATE(), INTERVAL 1 DAY) ORDER BY o.date DESC";
		try {			
			$conn = ConnectionManager::getConnection();
			$stmt = $conn->prepare($query);
			$stmt->execute();
			$resultSet = $stmt->fetchAll();
			return OrderFactory::createPreviousAndTodaysOrdersArrayFrom($resultSet);
		} catch (Exception $ex) {
			return null;
		}
	}

	private function filterTodayOrdersBy($store_id, $searchParam)
	{
		$query = "SELECT DATE_FORMAT(o.date, '%d/%m/%Y') AS date, o.order_id, o.po_number, s.description AS status FROM orders AS o INNER JOIN users AS u ON o.user_id = u.user_id INNER JOIN order_status AS s ON o.order_status_id = s.order_status_id WHERE u.store_id = :store_id AND DATE(o.delivery_date) = DATE_ADD(CURRENT_DATE(), INTERVAL 1 DAY) AND (o.po_number LIKE :po_number OR DATE_FORMAT(o.delivery_date, '%d/%m/%Y') LIKE :delivery_date) ORDER BY o.date DESC";
		try {
			$conn = ConnectionManager::getConnection();
			$stmt = $conn->prepare($query);
			$stmt->bindValue(':store_id', $store_id);
			$stmt->bindValue(':po_number', '%'.$searchParam.'%', PDO::PARAM_STR);
			$stmt->bindValue(':delivery_date', '%'.$searchParam.'%', PDO::PARAM_STR);
			$stmt->execute();
			$resultset = $stmt->fetchAll();
			return OrderFactory::createPreviousAndTodaysOrdersArrayFrom($resultset);
		} catch (Exception $ex) {
			return null;
		}
	}

	private function selectTodayOrdersBy($store_id)
	{
		$query = "SELECT DATE_FORMAT(o.date, '%d/%m/%Y') AS date, o.order_id, o.po_number, s.description AS status FROM orders AS o INNER JOIN users AS u ON o.user_id = u.user_id INNER JOIN order_status AS s ON o.order_status_id = s.order_status_id WHERE u.store_id = :store_id AND DATE(o.delivery_date) = DATE_ADD(CURRENT_DATE(), INTERVAL 1 DAY) ORDER BY o.date DESC";
		try {
			$conn = ConnectionManager::getConnection();
			$stmt = $conn->prepare($query);
			$stmt->bindValue(':store_id', $store_id);
			$stmt->execute();
			$resultset = $stmt->fetchAll();
			return OrderFactory::createPreviousAndTodaysOrdersArrayFrom($resultset);
		} catch (Exception $ex) {
			return null;
		}
	}

	public static function removeBy($order_id)
	{
		OrderItemsDAO::removeBy($order_id);
		$query = "DELETE FROM orders WHERE order_id = :order_id";
		$conn = ConnectionManager::getConnection();
		$stmt = $conn->prepare($query);
		$stmt->bindValue(':order_id', $order_id);
		$stmt->execute();
	}
}
