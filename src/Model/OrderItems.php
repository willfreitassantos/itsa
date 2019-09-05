<?php
namespace ITSA\Model;

use ITSA\Model\Order;
use ITSA\Model\Product;
use JsonSerializable;

class OrderItems implements JsonSerializable
{

	private $order;
	private $product;
	private $quantity;

	function __construct()
	{
		$this->quantity = 0;
	}

	public function getOrder()
	{
		return $this->order;
	}

	public function setOrder(Order $order)
	{
		$this->order = $order;
	}

	public function getProduct()
	{
		return $this->product;
	}

	public function setProduct(Product $product)
	{
		$this->product = $product;
	}

	public function getQuantity()
	{
		return $this->quantity;
	}

	public function setQuantity($quantity)
	{
		$this->quantity = $quantity;
	}

	public function jsonSerialize()
	{
        return 
        [
            'product'   => $this->product,
            'quantity' => $this->quantity
        ];
    }
}
