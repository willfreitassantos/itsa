<?php
namespace ITSA\Util\Factories;

use ITSA\Model\Order;
use ITSA\Model\User;
use ITSA\DAO\OrderItemsDAO;

class OrderFactory
{

	public static function createPreviousAndTodaysOrdersArrayFrom($resultSet)
	{
		$orders = array();
		foreach($resultSet as $order_data) :
			$order = new Order();
			$order->setOrderId($order_data['order_id']);
			$order->setOrderStatus($order_data['status']);
			$order->setDate($order_data['date']);
			$order->setPoNumber($order_data['po_number']);
			array_push($orders, $order);
		endforeach;
		return $orders;
	}

	public static function createOrderFrom($resultSet)
	{
		$order = new Order();
		$order->setOrderId($resultSet['order_id']);
		$order->setOrderStatus($resultSet['status']);
		$order->setDate($resultSet['date']);
		$order->setPoNumber($resultSet['po_number']);
		$order->setClientName($resultSet['client_name']);
		$order->setComments($resultSet['comments']);
		$order->setDeliveryDate(date('d/m/y', strtotime($resultSet['delivery_date'])));
		$user = new User();
		$user->setId($resultSet['user_id']);
		$user->setName($resultSet['user_name']);
		$order->setUser($user);
		$order->setOrderItems(OrderItemsDAO::selectBy($order));
		return $order;
	}
}
