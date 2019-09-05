<?php
namespace ITSA\Util\Factories;

use ITSA\Model\Order;
use ITSA\Model\OrderItems;
use ITSA\Util\Factories\ProductFactory;

class OrderItemsFactory
{

	public static function createOrderItemsFrom($resultSet, Order $order)
	{
		$orderItems = array();
		foreach($resultSet as $orderItem_data) {
			$orderItem = new OrderItems();
			$orderItem->setOrder($order);
			$orderItem->setProduct(ProductFactory::createFrom($orderItem_data));
			$orderItem->setQuantity($orderItem_data['quantity']);
			array_push($orderItems, $orderItem);
		}
		return $orderItems;
	}
}
