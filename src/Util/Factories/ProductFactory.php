<?php
namespace ITSA\Util\Factories;

use ITSA\Model\Product;

class ProductFactory
{
	
	public static function createFrom($resultset)
	{
		$product = new Product();
		$product->setProductId($resultset['product_id']);
		$product->setDescription($resultset['description']);
		$product->setPhotoPath($resultset['photo_path']);
		$product->setAvailable($resultset['available']);
		return $product;
	}

	public static function createArrayFrom($resultset)
	{
		$products = array();
		foreach ($resultset as $product_data) {
			$product = new Product();
			$product->setProductId($product_data['product_id']);
			$product->setDescription($product_data['description']);
			$product->setPhotoPath($product_data['photo_path']);
			$product->setAvailable($product_data['available']);
			array_push($products, $product);
		}
		return $products;
	}
}
