<?php
namespace ITSA\DAO;

use ITSA\DAO\ConnectionManager;
use ITSA\Model\Order;
use ITSA\Util\Factories\OrderItemsFactory;

class OrderItemsDAO
{

	public static function insertAll($orderItems, $orderId)
	{
		$query = "INSERT INTO order_items (order_id, product_id, quantity) VALUES (:order_id, :product_id, :quantity)";
		$conn = ConnectionManager::getConnection();
		foreach ($orderItems as $orderItem) {
			$orderItem = unserialize($orderItem);
			$stmt = $conn->prepare($query);
			$stmt->bindValue(':order_id', $orderId);
			$stmt->bindValue(':product_id', $orderItem->getProduct()->getProductId());
			$stmt->bindValue(':quantity', $orderItem->getQuantity());
			$stmt->execute();
		}
	}

	public static function insertAllUnserialized($orderItems, $orderId)
	{
		$query = "INSERT INTO order_items (order_id, product_id, quantity) VALUES (:order_id, :product_id, :quantity)";
		$conn = ConnectionManager::getConnection();
		foreach ($orderItems as $orderItem) {
			$stmt = $conn->prepare($query);
			$stmt->bindValue(':order_id', $orderId);
			$stmt->bindValue(':product_id', $orderItem->getProduct()->getProductId());
			$stmt->bindValue(':quantity', $orderItem->getQuantity());
			$stmt->execute();
		}
	}

	public static function selectBy(Order $order)
	{
		$query = "SELECT oi.product_id, p.description, p.photo_path, p.available, oi.quantity FROM order_items AS oi INNER JOIN products AS p ON oi.product_id = p.product_id WHERE oi.order_id = :order_id ORDER BY p.description";
		$conn = ConnectionManager::getConnection();
		$stmt = $conn->prepare($query);
		$stmt->bindValue(':order_id', $order->getOrderId());
		$stmt->execute();
		$resultSet = $stmt->fetchAll();
		return OrderItemsFactory::createOrderItemsFrom($resultSet, $order);
	}

	public static function removeBy($orderId)
	{
		$query = "DELETE FROM order_items WHERE order_id = :order_id";
		$conn = ConnectionManager::getConnection();
		$stmt = $conn->prepare($query);
		$stmt->bindValue(':order_id', $orderId);
		$stmt->execute();
	}
}
