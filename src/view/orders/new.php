<?php
require_once '../../logic/user/check-authorization.php';
require_once '../../../vendor/autoload.php';

use ITSA\Model\Order;
use ITSA\DAO\CompanyDAO;
use ITSA\DAO\StoreDAO;
use ITSA\DAO\ProductDAO;

$loggedUser = unserialize($_SESSION['logged_user']);

if(!isset($_SESSION['new_order_info'])) :
	$newOrderInfo = new Order();

	$newOrderInfo->setUser($loggedUser);

	$_SESSION['new_order_info'] = serialize($newOrderInfo);
endif;

$newOrderInfo = unserialize($_SESSION['new_order_info']);

if($loggedUser->isAdmin()) :
	$companies = CompanyDAO::listAll();
	if(!isset($_SESSION['selected_store'])) :
		$_SESSION['selected_store'] = serialize($companies[0]->getStores()[0]);
	endif;
else:
	$companies = array();
	$selectedStore = StoreDAO::selectById($loggedUser->getStoreId());
	$_SESSION['selected_store'] = serialize($selectedStore);
endif;

$selectedStore = unserialize($_SESSION['selected_store']);
$products_available = ProductDAO::listAllAvailableBy($selectedStore->getCompany());

require_once('../default/header.php');
require_once('../default/modal-order-details.php');
?>
<section class="main-wrapper">
	<!-- NAVIGATION -->
	<div class="container">
		<div class="row">
			<div class="col-md-12">
				<ul class="order-prograss">
					<li><a href="javascript:;" class="procesing complete">Select Product (<span id="product_number" data-product-number="<?=count($newOrderInfo->getOrderItems())?>"></span>)</a></li>
					<li><a href="javascript:;" class="procesing">Order Information</a></li>
					<li><a href="javascript:;" class="procesing">Complete Order</a></li>
				</ul>
			</div>
		</div>
	</div>
	<!-- ALERT PRODUCTS NOT SELECTED -->
	<?php
	if(isset($_SESSION['no_products_selected'])) :
	?>
		<div class="container">
			<div class="row">
				<div class="col-md-12">
					<div id="alert" class="alert alert-danger alert-dismissible fade show text-center alert-products-not-selected" role="alert">
		  				<strong>Oops!</strong> <?=$_SESSION['no_products_selected']?>
		 				<button type="button" class="close" data-dismiss="alert" aria-label="Close">
		    				<span aria-hidden="true">&times;</span>
		  				</button>
					</div>
				</div>
			</div>
		</div>
	<?php
		unset($_SESSION['no_products_selected']);
	endif;
	?>
	<!-- PRODUCT LIST -->
	<div class="container">
		<?php
		if($products_available != null && count($products_available) > 0 && count($companies) > 0) :
		?>
			<div class="row company-store-select">
				<div class="col-xs-12 col-sm-5 form-group">
					<label for="company" class="control-label">Company</label>
					<select id="company" class="form-control">
						<?php
						foreach($companies as $company) :
						?>
							<option value="<?=$company->getCompanyId()?>" <?=$selectedStore->getCompany()->getCompanyId() == $company->getCompanyId() ? 'selected' : ''?>><?=$company->getName()?></option>
						<?php
						endforeach;
						?>
					</select>
				</div>
				<div class="col-xs-12 col-sm-5 form-group">
					<label for="store" class="control-label">Store</label>
					<select id="store" class="form-control">
					</select>
				</div>

				<div class="col-xs-12 col-sm-2 form-group d-flex justify-content-center">
					<!-- Botão para acionar modal -->
					<button type="button" class="btn btn-import-csv" data-toggle="modal" data-target="#modalImportCSV">
						Import CSV
					</button>
				</div>

			</div>
		<?php
		endif;
		?>
		<div class="row">
			<div class="col-md-12">
				<div class="product_container">
					<ul class="row">
					<?php
						if($products_available != null && count($products_available) > 0) :
							foreach ($products_available as $product) :
					?>
								<li class="li col-sm-6 col-md-4 col-lg-3 col-xl-2">
									<div class="product_item <?=array_key_exists($product->getProductId(), $newOrderInfo->getOrderItems()) ? 'selected' : ''?>">
										<input type="checkbox" id="product_item_<?=$product->getProductId()?>" data-product-id="<?=$product->getProductId()?>">
										<label for="product_item_<?=$product->getProductId()?>">
											<img src="../<?=$product->getPhotoPath()?>" alt="bitmap">
											<div class="product-discrip">
												<span><?=$product->getDescription()?></span>
											</div>
										</label>
									</div>
								</li>
					<?php
							endforeach;
						else :
					?>
							<span class="no-products-available">There are no products available!</span>
					<?php
						endif;
					?>
					</ul>
					<a class="btn_2nd pull-right" id="btn-order-information" href="./info">Order Information</a>
				</div>
			</div>
		</div>
	</div>
	<div class="container">
		<div class="row">
			<?php
			require_once('previous-orders.php');
			require_once('today-orders.php');
			?>
		</div>
	</div>
</section>
<script type="text/javascript" src="../resources/plugins/jquery/js/jquery-3.3.1.min.js"></script>
<script type="text/javascript" src="../resources/plugins/bootstrap/js/bootstrap.min.js"></script>
<script type="text/javascript" src="../resources/plugins/jquery-mask-plugin/js/jquery.mask.min.js"></script>
<script type="text/javascript" src="../resources/plugins/jquery-blockUI/js/jquery.blockUI.js"></script>
<script type="text/javascript" src="../resources/plugins/sweet-alert/js/sweet-alert.min.js"></script>
<script type="text/javascript" src="../resources/js/orders/previous-orders.js"></script>
<script type="text/javascript" src="../resources/js/orders/today-orders.js"></script>
<script type="text/javascript" src="../resources/js/orders/order-utils.js"></script>
<script type="text/javascript" src="../resources/js/orders/new.js"></script>
<?php
require_once('../default/footer.php');
?>


<!-- Modal -->
<div class="modal fade" id="modalImportCSV" tabindex="-1" role="dialog" aria-labelledby="modalImportCSV" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
			<div class="modal-header">
        <h4 class="modal-title" id="exampleModalLabel">Import Order in File CSV</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
        	<span aria-hidden="true">&times;</span>
				</button>
      </div>

			<div class="modal-body">
				<form class="form-horizontal" action="../orders/importcsv" method="post" name="upload_excel" enctype="multipart/form-data">
						<fieldset>
							<!-- File Button -->
						  <div class="form-group">
								<div class="row">
							    <div class="col-sm-12">
									<input type="file" name="file[]" id="file" class="input-large" accept=".csv" multiple="" />
							  	</div>
								</div>
							</div>
							<!-- Button -->
						  <div class="form-group">
								<div class="row">
									<div class="col-sm-2 d-flex align-items-center">
										<span class="spanSeparator">Separator</span>
									</div>

									<div class="col-sm-2 d-flex justify-content-left align-items-center">
										<input type="text" name="txSeparator" value="," maxlength="1" size="5" />
									</div>
									<div class="col-sm-8 btn-group btn-group-justified">
										<button type="submit" id="submit" name="Import" class="btn btn-secondary btn-lg btn-block btn-import-csv" data-loading-text="Importing...">Import</button>
									</div>
								</div>
						  </div>
						</fieldset>
				</form>
			</div>
    </div>
  </div>
</div>
