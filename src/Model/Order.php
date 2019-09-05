<?php
namespace ITSA\Model;

use JsonSerializable;

class Order implements JsonSerializable
{

	private $order_id;
	private $user;
	private $store;
	private $order_status;
	private $date;
	private $order_items;
	private $client_name;
	private $po_number;
	private $delivery_date;
	private $comments;

	public function __construct()
	{
		$this->order_items = array();
		$this->delivery_date = date('d/m/y', strtotime('+1 day'));
	}

	public function getOrderId()
	{
		return $this->order_id;
	}

	public function setOrderId($order_id)
	{
		$this->order_id = $order_id;
	}

	public function getUser()
	{
		return $this->user;
	}

	public function setUser(User $user)
	{
		$this->user = $user;
	}

	public function getStore()
	{
		return $this->store;
	}

	public function setStore(Store $store)
	{
		$this->store = $store;
	}

	public function getOrderStatus()
	{
		return $this->order_status;
	}

	public function setOrderStatus($order_status)
	{
		$this->order_status = $order_status;
	}

	public function getDate()
	{
		return $this->date;
	}

	public function setDate($date)
	{
		$this->date = $date;
	}

	public function getOrderItems()
	{
		return $this->order_items;
	}

	public function setOrderItems($order_items)
	{
		$this->order_items = $order_items;
	}

	public function add(OrderItems $order_items)
	{
		$this->order_items[$order_items->getProduct()->getProductId()] = serialize($order_items);
	}

	public function addWithoutSerialization(OrderItems $order_items)
	{
		array_push($this->order_items, $order_items);
	}	

	public function remove(OrderItems $order_items)
	{
		$this->order_items = array_filter($this->order_items, function($key) use ($order_items) {
			return $key != $order_items->getProduct()->getProductId();
		}, ARRAY_FILTER_USE_KEY);
	}

	public function removeWithoutSerialization(OrderItems $order_items)
	{
		$this->order_items = array_filter($this->order_items, function($val) use ($order_items) {
			return $val->getProduct()->getproductId() != $order_items->getProduct()->getProductId();
		});
		$orderItem_aux = array();
		foreach($this->order_items as $orderItem) {
			array_push($orderItem_aux, $orderItem);
		}
		$this->order_items = $orderItem_aux;
	}

	public function getClientName()
	{
		return $this->client_name;
	}

	public function setClientName($client_name)
	{
		$this->client_name = $client_name;
	}

	public function getPoNumber()
	{
		return $this->po_number;
	}

	public function setPoNumber($po_number)
	{
		$this->po_number = $po_number;
	}

	public function getDeliveryDate()
	{
		return $this->delivery_date;
	}

	public function getDeliveryDateDB()
	{
		$day = explode('/', $this->delivery_date)[0];
		$month = explode('/', $this->delivery_date)[1];
		$year = explode('/', $this->delivery_date)[2];
		return '20' . $year . '/' . $month . '/' . $day;
	}

	public function setDeliveryDate($delivery_date)
	{
		$this->delivery_date = $delivery_date;
	}

	public function getComments()
	{
		return $this->comments;
	}

	public function setComments($comments)
	{
		$this->comments = $comments;
	}

	public function jsonSerialize()
	{
        return 
        [
            'order_id'   => $this->order_id,
            'order_status' => $this->order_status,
            'date' => $this->date,
            'po_number' => $this->po_number,
            'delivery_date' => $this->delivery_date,
            'order_items' => $this->order_items,
            'client_name' => $this->client_name,
            'comments' => $this->comments,
            'user' => $this->user
        ];
    }
}
