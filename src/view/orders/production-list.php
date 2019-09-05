<?php
require_once '../../logic/user/check-authorization.php';
require_once '../../../vendor/autoload.php';

require_once('../default/header.php');

$tomorrow = date('d/m/Y', strtotime('+1 day'));
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

.production-list-filter-wrapper {
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
                        <li class="breadcrumb-item active" aria-current="page"><i class="ion-arrow-graph-up-right"></i> Production List</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="production-list-filter-wrapper">
                    <form id="production-list-filter-form" action="../orders/production-list/filter" method="POST" target="_blank">
                        <div class="form-group">
                            <label for="production-list-date" class="control-label">Production List Date</label>
                            <input type="datetime" name="production-list-date" id="production-list-date" class="form-control" value="<?=$tomorrow?>" maxlength="10" placeholder="DD/MM/YYYY">
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
        $('#production-list-date').mask('00/00/0000', {
            clearIfNotMatch: true
        });

        $('#production-list-filter-form').validate({
            rules: {
                'production-list-date': {
                    required: true,
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
            errorClass: 'invalid-feedback'
        });
    });
</script>
<?php
require_once '../default/footer.php';
?>