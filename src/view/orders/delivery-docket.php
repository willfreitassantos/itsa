<?php
require_once '../../logic/user/check-authorization.php';
require_once '../../../vendor/autoload.php';

require_once('../default/header.php');

use ITSA\DAO\CompanyDAO;

$tomorrow = date('d/m/Y', strtotime('+1 day'));
$companies = CompanyDAO::listAll();
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

.delivery-docket-filter-wrapper {
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
</style>
<section class="main-wrapper">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="../home"><i class="ion-android-home"></i> Home</a></li>
                        <li class="breadcrumb-item"><a href="../orders/new"><i class="ion-android-cart"></i> Orders</a></li>
                        <li class="breadcrumb-item active" aria-current="page"><i class="ion-arrow-graph-up-right"></i> Delivery Docket</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="delivery-docket-filter-wrapper">
                    <form id="delivery-docket-filter-form" action="../orders/delivery-docket/filter" method="POST" target="_blank">
                        <div class="row">
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="date" class="control-label">Date</label>
                                    <input type="datetime" name="date" id="date" class="form-control" maxlength="10" placeholder="DD/MM/YYYY" value="<?=$tomorrow?>">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="po_number" class="control-label">P.O. Number</label>
                                    <input type="text" name="po_number" id="po_number" class="form-control" maxlength="100">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="company_id" class="control-label">Company</label>
                                    <select name="company_id" id="company_id" class="form-control">
                                        <option value="all"><-- all --></option>
                                        <?php
                                        foreach($companies as $company) :
                                        ?>
                                            <option value="<?=$company->getCompanyId()?>"><?=$company->getName()?></option>
                                        <?php
                                        endforeach;
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="store_id" class="control-label">Store</label>
                                    <select name="store_id" id="store_id" class="form-control">
                                        <option value="all"><-- all --></option>
                                    </select>
                                </div>
                            </div>
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
<script type="text/javascript">
    $(document).ready(function() {
        $('#date').mask('00/00/0000', {
            clearIfNotMatch: true
        });

        $('#delivery-docket-filter-form').validate({
            rules: {
                'date': {
                    dateITA: true
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
                let $date = $('#date').val().trim(),
                    $po_number = $('#po_number').val().trim(),
                    $company_id = $('#company_id').val(),
                    $store_id = $('#store_id').val();
                if($date != '' || $po_number != '' || $company_id != 'all' || $store_id != 'all') {
                    form.submit();
                } else {
                    window.alert('Please, you must select at least one option!');
                }
            }   
        });
    });

    $(document).on('change', '#company_id', function(e) {
        $('#store_id option').remove();
        let options = '<option value="all"><-- all --></option>';
        if($('#company_id').val() != 'all') {
            $.ajax({
                type: 'POST',
                url: '../orders/select-store',
                data: {
                    company_id: $('#company_id').val(),
                    store_id: null
                },
                dataType: 'json',
                success: function(json) {
                    $(json.stores).each(function() {
                        options += '<option value="' + this.store_id + '">' + this.name + '</option>';
                    });
                    $('#store_id').append(options);
                },
                error: function(json) {
                    window.alert("Something went wrong! Please, try again later... If this problem persists, contact your system administrator!");
                }
            });
        } else {
            $('#store_id').append(options);
        }
    });
</script>
<?php
require_once '../default/footer.php';
?>
