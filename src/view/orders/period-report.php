<?php
require_once '../../logic/user/check-authorization.php';
require_once '../../../vendor/autoload.php';

use ITSA\DAO\ProductDAO;

require_once('../default/header.php');

$currentDate = date('d/m/Y', strtotime('now'));
$products = ProductDAO::listAll();
?>
<style>
ol.breadcrumb {
    margin-top: 40px;
    background-color: #9f0766;
    border: 1px solid #ffffff;
    border-radius: 10px;
    font-family: "Avenir";
    font-size: 17px;
    font-weight: 800;
}

ol.breadcrumb a,
.breadcrumb-item + .breadcrumb-item::before {
    color: #ffffff;
}

.breadcrumb-item.active {
    color: #a6a6a6;
}

.period-report-filter-wrapper {
    margin: 30px 0;
    padding-top: 50px;
    padding-bottom: 20px;
    background: #fff;
    padding-left: 30px;
    padding-right: 30px;
    border-radius: 15px;
    list-style: none;
    display: inline-block;
    width: 100%;
}

.form-group {
    font-family: "Oxygen Regular";
    font-size: 14px;
    font-weight: 600;
}

.btn_2nd {
    margin-top: 15px;
}

.form-check label {
    line-height: 1.7em;
    font-family: "Oxygen Regular";
}
</style>
<section class="main-wrapper">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="../home"><i class="ion-android-home"></i> Home</a></li>
                        <li class="breadcrumb-item"><a href="../orders/new"><i class="ion-android-cart"></i> Orders</a></li>
                        <li class="breadcrumb-item active" aria-current="page"><i class="ion-arrow-graph-up-right"></i> Production Report</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="period-report-filter-wrapper">
                    <form id="period-report-filter-form" action="../orders/period-report/filter" method="POST" target="_blank">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="dateFrom" class="control-label">From:</label>
                                    <input type="datetime" name="dateFrom" id="dateFrom" class="form-control" maxlength="10" placeholder="DD/MM/YYYY" value="<?=$currentDate?>">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="dateTo" class="control-label">To:</label>
                                    <input type="datetime" name="dateTo" id="dateTo" class="form-control" maxlength="10" placeholder="DD/MM/YYYY" value="<?=$currentDate?>">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-check all-products">
                                    <input type="checkbox" id="chkAllProducts" name="chkAllProducts" class="form-check-input" value="true">
                                    <label for="chkAllProducts"><strong>All Products</strong></label>
                                </div>
                            </div>
                            <?php
                            $iterator = 0;
                            foreach ($products as $product) :
                            ?>
                                <div class="col-md-4">
                                    <div class="form-check">
                                        <input type="checkbox" id="chkProduct_<?=$iterator?>" name="chkProducts[]" class="form-check-input form-check-input-product" value="<?=$product->getProductId()?>">
                                        <label for="chkProduct_<?=$iterator?>"><?=$product->getDescription()?></label>
                                    </div>
                                </div>
                            <?php
                                $iterator++;
                            endforeach;
                            ?>
                        </div>
                        <button type="submit" class="btn_2nd">Generate Report</button>
                    </form>
                </div>
            </div>
        </div>
    </div>	
</section>
<script type="text/javascript" src="../resources/plugins/jquery/js/jquery-3.3.1.min.js"></script>
<script type="text/javascript" src="../resources/plugins/bootstrap/js/bootstrap.min.js"></script>
<script type="text/javascript" src="../resources/plugins/jquery-mask-plugin/js/jquery.mask.min.js"></script>
<script type="text/javascript" src="../resources/plugins/jquery-validation/js/jquery.validate.min.js"></script>
<script type="text/javascript" src="../resources/plugins/jquery-validation/js/additional-methods.min.js"></script>
<script type="text/javascript" src="../resources/plugins/momentJS/js/moment.js"></script>
<script type="text/javascript">
    $.validator.addMethod( "dateGreaterOrEqual", function(value) {
        let dateFrom = $('#dateFrom').val();
        let day_dateFrom = dateFrom.substring(0, 2);
        let month_dateFrom = dateFrom.substring(3, 5);
        let year_dateFrom = dateFrom.substring(6, 10);

        let day_dateTo = value.substring(0, 2);
        let month_dateTo = value.substring(3, 5);
        let year_dateTo = value.substring(6, 10);
    
        let initialPeriod = new Date();
        let finalPeriod = new Date();

        initialPeriod.setFullYear(year_dateFrom, month_dateFrom - 1, day_dateFrom);
        finalPeriod.setFullYear(year_dateTo, month_dateTo - 1, day_dateTo);

        return finalPeriod >= initialPeriod;
    }, "This date must be greater or equal to the date entered in the field 'From'.");

    $.validator.addMethod( "maxDaysBetweenDates", function(value) {
        let dateFrom = $('#dateFrom').val();
        let dateTo = value;

        let initialPeriod = moment(dateFrom, 'DD/MM/YYYY');
        let finalPeriod = moment(dateTo, 'DD/MM/YYYY');

        return finalPeriod.diff(initialPeriod, 'days') <= 31;
    }, "The period must not be greater than 31 days.");

    $(document).ready(function() {
        $('#dateFrom,#dateTo').mask('00/00/0000', {
            clearIfNotMatch: true
        });

        $('#period-report-filter-form').validate({
            rules: {
                'dateFrom': {
                    required: true,
                    dateITA: true
                },
                'dateTo': {
                    required: true,
                    dateITA: true,
                    dateGreaterOrEqual: true,
                    maxDaysBetweenDates: true
                }
            },
            highlight: function(element) {
                $(element).addClass('is-invalid');
            },
            unhighlight: function(element) {
                $(element).removeClass('is-invalid');
            },
            errorElement: 'div',
            errorClass: 'invalid-feedback',
            submitHandler: function(form) {
                if($('.form-check-input-product:checked').length > 0 || $('#chkAllProducts').is(":checked")) {
                    form.submit();
                } else {
                    window.alert("There must be at least one product selected!");
                }
            }
        });
    });

    $(document).on('click', '.form-check-input', function(evt) {
        if($(evt.target).attr('id') == "chkAllProducts") {
            if($(evt.target).is(':checked')) {
                $('.form-check-input-product').prop('checked', true);
            } else {
                $('.form-check-input-product').prop('checked', false);
            }
        } else {
            if($('.form-check-input-product:checked').length == $('.form-check-input-product').length) {
                $('#chkAllProducts').prop('checked', true);
            } else {
                $('#chkAllProducts').prop('checked', false);
            }
        }
    });
</script>
<?php
require_once '../default/footer.php';
?>