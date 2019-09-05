<?php
  session_start();
  require_once '../../../vendor/autoload.php';

  use ITSA\DAO\StoreDAO;
  use ITSA\DAO\ProductDAO;
	use ITSA\DAO\OrderDAO;
  use ITSA\Model\Order;
  use ITSA\Model\OrderItems;

  function validateFileExtension($extension) {
    $allowedFiles = array("csv", "CSV");
    if (!in_array($extension, $allowedFiles)) { return false; }
    else { return true; }
  }

  function getStoreByCode($storeCode) {
    return StoreDAO::selectByStoreCode($storeCode);
  }

  function createOrderInfo($store, $fileName) {
    $loggedUser = unserialize($_SESSION['logged_user']);
    $newOrderInfo = new Order();
    $newOrderInfo->setUser($loggedUser);
    $_SESSION['selected_store'] = serialize($store);
    $selectedStore = unserialize($_SESSION['selected_store']);
    $newOrderInfo->setStore($selectedStore);
    $newOrderInfo->setClientName($selectedStore->getName());
    $newOrderInfo->setPoNumber($fileName);
    $newOrderInfo->setComments('');
    $_SESSION['new_order_info'] = serialize($newOrderInfo);
    return $newOrderInfo;
  }

  function processItems($file) {
    while (($getData = fgetcsv($file, 0, ",")) !== FALSE) {
      $product_id = $getData[0];
      $product = ProductDAO::selectByProductCodes($product_id);
      if (is_null($product)) {
        continue;
      }

      $qty =$getData[3];
      if (!is_numeric($qty)) {
        $qty = 1;
      }

      $newOrderInfo = unserialize($_SESSION['new_order_info']);
      $orderItems = new OrderItems();
      $orderItems->setProduct($product);
      $orderItems->setQuantity($qty);
      $newOrderInfo->add($orderItems);
      $_SESSION['new_order_info'] = serialize($newOrderInfo);
    } // end - while
  }

  function insertOrder() {
    $newOrder = unserialize($_SESSION['new_order_info']);

    $selectedStore = unserialize($_SESSION['selected_store']);
    $newOrder->setStore($selectedStore);

    $newOrder = OrderDAO::insert($newOrder);

    unset($_SESSION['new_order_info']);
  }

  function getDirectoryFileCsv() {
    $ini = parse_ini_file("../../../config.ini", true);
    return $ini['csv']['directory_read'];
  }



  // processing
  if (!isset($_POST["Import"])) {
    header('Location: /itsa/orders/info');
    exit;
  }

  $listFiles = array();

  if(count($_FILES['file']['name'])) {
    $i = 0;
    $pathCsv = getDirectoryFileCsv();
    foreach ($_FILES['file']['name'] as $file1) {
      $filename = $_FILES["file"]["tmp_name"][$i];
      $size =  $_FILES["file"]["size"][$i];
			if (!($size > 0 )) {
        continue;
			}

      $file = fopen($filename, "r");
			$fileNameComplete = $_FILES['file']['name'][$i];
			$fileName = pathinfo($fileNameComplete, PATHINFO_FILENAME);

      if (!validateFileExtension(pathinfo($fileNameComplete, PATHINFO_EXTENSION))) {
        echo "Extension not allowed";
        exit;
      }

      $selectedStore = getStoreByCode(substr($fileName, 0, 3));
			if (is_null($selectedStore)) {
        fclose($file);
        $i++;
        continue;
			}

      $newOrderInfo = createOrderInfo($selectedStore, $fileName);
      processItems($file);
      fclose($file);
      insertOrder();
      $listFiles[] = $pathCsv . $fileNameComplete;
			$i++;
		} // end - foreach
	} // end - count

  $_SESSION['files_csv'] = serialize($listFiles);
  header('Location: ../orders/importcsv-end');
 ?>
