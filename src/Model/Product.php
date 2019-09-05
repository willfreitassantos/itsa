<?php
namespace ITSA\Model;

use JsonSerializable;

class Product implements JsonSerializable
{

	private $product_id;
	private $description;
	private $photo_path;
	private $available;

	public function getProductId()
	{
		return $this->product_id;
	}

	public function setProductId($product_id)
	{
		$this->product_id = $product_id;
	}

	public function getDescription()
	{
		return $this->description;
	}

	public function setDescription($description)
	{
		$this->description = $description;
	}

	public function getPhotoPath()
	{
		return $this->photo_path;
	}

	public function setPhotoPath($photo_path)
	{
		$this->photo_path = $photo_path;
	}

	public function isAvailable()
	{
		return $this->available;
	}

	public function setAvailable($available)
	{
		$this->available = $available;
	}

	public function jsonSerialize()
	{
        return 
        [
            'product_id'   => $this->product_id,
            'description' => $this->description,
            'photo_path' => $this->photo_path
        ];
    }
}
