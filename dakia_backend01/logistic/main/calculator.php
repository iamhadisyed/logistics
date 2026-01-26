<?php
//require_once("../includes/settings/common.inc.php");
require_once("../includes/settings/config.inc.php");
include_classes([
    'tcpdf'
], '3rdparty/tcpdf');
include_classes([
        'country.class',
        'countryfilter.class',
        'parcelfilter.class',
        'warehouse.class',
        'consignment.class',
        'parcel.class',
        'trackingdata.class',
        'owereturnlabel.class',
        'consignment.class',
        'consignmentfilter.class',
        'currency.class',
        'currencyfilter.class',
    'carrierfilter.class',
        'carrier.class',
    
        
        ]);
include_classes([
        'yakit.class'        
        ],'labels');
include_classes(['inputfile1.class'],
        'autoload');

/*
 */

// set up local page class
class Page extends BasePage {

    //how many bookings to import/validate at once?
    const IMPORT_BATCH_MAX = 5;
    const IMPORT_FILE_DIR = '../_assets/duty_taxes/';
    // page states
    const STATUS_INITIAL = 0;
    const STATUS_GOT_FILE = 1;
    const STATUS_IMPORTING = 2;
    const STATUS_IMPORTED = 3;
    private $csv ="";
    private $page_count;
    private $country_drop;
    private $serviceOrProduct;
    private $isProduct;
    
    public $clearenceFee = 5.00;
    
    public $category = ["Fashion",
                "Women",
                "Men",
                "Accessories",
                "Shoes",
                "Technology",
                "Computer, Tablets and Accessories",
                "Mobile Phones &amp; Communication",
                "Leisure &amp; Entertainment",
                "Home &amp; Garden",
                "Health &amp; Beauty",
                "Kids",
                "Sound &amp; Vision",
                "Fragrance",
                "Make-Up",
                "Healthcare &amp; Dietary Supplements",
                "Sporting Goods",
                "Toys &amp; Games",
                "Instruments",
                "Lifestyle",
                "Department Store &amp; Platforms",
                "Travel",
                "Skin Care", "Others"];
    /*     * *
     * This page's content
     * @return void
     */

    protected function addPagelavelCss() {
        ?>
        <link rel="stylesheet" type="text/css" href="../assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/select2/css/select2-bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/bootstrap-sweetalert/sweetalert.css" rel="stylesheet" type="text/css" />

        <link href="../assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/bootstrap-select/css/bootstrap-select.css" rel="stylesheet" type="text/css" />        
        <link href="../assets/global/plugins/icheck/skins/all.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css" rel="stylesheet" type="text/css" />
        <link rel="stylesheet" href="../assets/pages/css/flipclock.css">


        
        <style>
            .label-account{
                font-size: 12px;
                font-weight: bold;
            }
            .blockUI {
                z-index: 99999999 !important;
            }
        </style>
        <?php
    }

    public function addPagelavelJs() {
        ?>
        <script src="../assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.js" type="text/javascript"></script>
        <script type="text/javascript" src="../assets/global/plugins/select2/js/select2.full.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/bootstrap-select/js/bootstrap-select.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/bootstrap-confirmation/bootstrap-confirmation.min.js" type="text/javascript"></script>

        <script src="../assets/global/scripts/datatable.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/datatables/datatables.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/bootstrap-sweetalert/sweetalert.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/icheck/icheck.min.js" type="text/javascript"></script>  
        <script src="../assets/pages/scripts/flipclock.min.js"></script>
        <!--        <script src="/assets/global/scripts/app.min.js" type="text/javascript"></script>     -->
        <script src="../assets/pages/scripts/components-date-time-pickers.min.js" type="text/javascript"></script>
        <script src="../assets/pages/scripts/ui-confirmations.min.js" type="text/javascript"></script>
        <script type="text/javascript">
            
            
            
            
            function calculatePrice() {
                var calc_currency = $("#calc_currency").val();
                var calc_scale = $("#calc_scale").val();
                var calc_shipping_from = $("#calc_shipping_from").val();
                var calc_shipping_to = $("#calc_shipping_to").val();
                var calc_weight = $.trim($("#calc_weight").val());
                var calc_width = $("#calc_width").val();
                var calc_length = $("#calc_length").val();
                var calc_height = $("#calc_height").val();
                var calc_dim_unit = $("#calc_dim_unit").val();
                var description = $("#description").val();
                var categories = $("#categories").val();
                var calc_value = $("#calc_value").val();
                
                if($.trim(calc_weight) == '')
                    return fasle;
              //  var service = $('#service').val();
                $(".calc_dim_label_unit").html(calc_dim_unit);
                calc_weight = parseFloat(calc_weight);
                var weight = calc_weight;
                if (calc_weight != "" && calc_width != "" && calc_length != "" && calc_height != "") {
                    var volumetric_weight = weight;
                    if (calc_dim_unit == 'cm')
                        volumetric_weight = (calc_width * calc_length * calc_height) / 5000;
                    else if (calc_dim_unit == 'in')
                        volumetric_weight = (calc_width * calc_length * calc_height) / 138.4;

                    volumetric_weight = volumetric_weight.toFixed(2);
                    weight = volumetric_weight > calc_weight ? volumetric_weight : calc_weight;
                }
                if ($.trim(weight) == '')
                {    weight = 0;}
                $("#cal-api-error").html("").addClass("hidden").hide();
                $("#calc_chargeable_weight").val(weight);
                if (validateWeight(weight, 'calc_')) {
                    $("#calc_loading").show();
                $("#screen_description").html(description );
                $("#screen_category").html(categories);
                var currencyDisplay = $("#calc_currency").find(':selected').attr('data-code');
                $("#screen_parcel_value").html( calc_value+" "+currencyDisplay );
                    $.post(
                            "calculator.php",
                            {
                                UpcomingItemCall: 1,
                                SourceCountryId: calc_shipping_from,
                                DestinationCountryId: calc_shipping_to,
                                CurrencyId: calc_currency,
                                 parcelWeight: weight,
                                IsDocument: 0,
                                InsuranceRequired: 0,
                                WeightSymbol: calc_scale,
                                ParcelValue: "",
                                calc_width : calc_width, 
                                calc_length : calc_length,
                                calc_height : calc_height,
                                calc_dim_unit : calc_dim_unit,
                                calc_value : calc_value,                                
                                description:description,
                                categories:categories
                                //service: service
                            },
                            function (response) {
                                //console.log(response);
                                if (typeof response == 'object') {
                                    if(response.status){
                                        var CurSym = (response.CurSym != null ? response.CurSym : 'N/A');
                                        var shippingFee = (response.shippingFee != null ? response.shippingFee : 'N/A');
                                        var dutiesTaxes = (response.dutiesTaxes != null ? response.dutiesTaxes : 'N/A');
                                        var clearenceFee = <?php echo $this->clearenceFee;?>;
                                        
                                        $("#screen_shipping_code").html(shippingFee +" " + CurSym);
                                        $("#screen_scale").html(weight+' '+calc_scale);
                                        $("#screen_dim").html( calc_width + ' x ' + calc_length + ' x ' + calc_height);
                                        $("#screen_from_to").html(response.fromCountry + ' to  ' + response.toCountry);
                                        //$("#screen_price").html(shippingFee);
                                        $("#screen_duty_taxes").html( response.dutiesTaxes+" "+CurSym);
                                        $("#screen_hscode").html( response.hscode );
                                        $("#screen_hscode").html( response.hscode );
                                        $("#screen_clearence_fee").html( clearenceFee.toFixed(2) +" "+CurSym);
                                        var totaltoshow = (shippingFee+dutiesTaxes+clearenceFee).toFixed(2);
                                        $("#screen_total").html(totaltoshow+" " + CurSym);
                                        $("#calc_loading").hide();
                                    } else {
                                        $("#cal-api-error").removeClass("hidden").html(response.message).show();
                                        $("#calc_loading").hide();
                                    }
                                    
                                } else {
                                    console.log(response);
                                }
                            }, "json");
                }
            }
            function validateWeight(TotalWeight, prefix) {
                TotalWeight = parseFloat(TotalWeight);
                if (!isNaN(TotalWeight) && TotalWeight > 0) {
                    if ($("#" + prefix + "total_weight_container").length > 0) {
                        $("#" + prefix + "total_weight_container").removeClass('has-error');
                        $("#" + prefix + "total_weight_container").addClass('has-success');
                        $("#" + prefix + "total_weight_error_container").hide();
                    }
                    return true;
                } else {
                    if ($("#" + prefix + "total_weight_container").length > 0) {
                        $("#" + prefix + "total_weight_container").removeClass('has-success');
                        $("#" + prefix + "total_weight_container").addClass(' has-error');
                        $("#" + prefix + "total_weight_error_container").show();
                        $("#" + prefix + "total_weight_container").find('input').focus();
                        $('html,body').animate({
                            scrollTop: ($("#" + prefix + "total_weight_container").offset().top - 100)
                        }, 'slow');
                    }
                    return false;
                }
            }
            $(document).ready(function () {
                $("#calc_loading").hide();
                $("#calc_total_weight_error_container").hide();
                $('#radioBtn2 a').on('click', function () {
                    var sel = $(this).data('title');
                    var tog = $(this).data('toggle');
                    $('#' + tog).prop('value', sel);
                    $('a[data-toggle="' + tog + '"]').not('[data-title="' + sel + '"]').removeClass('active').addClass('notActive');
                    $('a[data-toggle="' + tog + '"][data-title="' + sel + '"]').removeClass('notActive').addClass('active');
                    calculatePrice();
                });
                $("#calc_weight").keyup(function () {
                    validateWeight($(this).val(), 'calc_');
                });
                $(".calculate_price").change(function () {
                    calculatePrice();
                });

                $(".calculate_price_input").keyup(function () {

                    var id = $(this).attr('id');
                    if ($("#_" + id).length > 0)
                        $("#_" + id).val($(this).val());
                    if (window.myajax) {
                        clearTimeout(window.myajax);
                    }
                    window.myajax = setTimeout(function () {
                        calculatePrice();
                        window.myajax = null;
                    }, 500);
                });
                
                $("#bulk_duty_taxes_checked").click(function () {
                    $("#file_in").val("");
                    $("#csv_upload").modal('show');
                });
                
            
            $("#btnSubmitImport").click(function () {
                $('#console_window').html('').show();
                $('#console_window').html("Uploading CSV File....<br />");
                var file_data = $('#file_in').prop('files')[0];

                var form_data = new FormData();
                form_data.append('csv_file', file_data);
                form_data.append('func', 'upload_csv_file');
                $.ajax({
                    url: 'calculator.php',
                    dataType: 'json',
                    cache: false,
                    contentType: false,
                    processData: false,
                    data: form_data,
                    type: 'post',
                    success: function (response) {
                        if (response.status == 'success') {
                            $('#console_window').append('Start Importing data<br />');
                            // alert(response.file_name);
                            importCSV(0, response.file_name, 0,'');
                        } else {
                            $('#console_window').append('<span style="color:red;">' + response.message + '</span><br />');
                        }
                    }
                });
                return false;
            });
        });
        
        function importCSV(startfrom, filename, next_actual,output_file) {
                $('#console_window').show();
                var batch_limit = '<?php echo Page::IMPORT_BATCH_MAX; ?>'
                batch_limit = parseInt(batch_limit);
                $('#console_window').append('Importing records from ' + (next_actual + 1) + ' to ' + (next_actual + batch_limit) + '...');
                var form_data = new FormData();
                form_data.append('func', 'import_file');
                form_data.append('start', startfrom);
                form_data.append('start_real', next_actual);
                form_data.append('filename', filename);
                form_data.append('output_file', output_file);
                
                $.ajax({
                    url: 'calculator.php',
                    dataType: 'json',
                    cache: false,
                    contentType: false,
                    processData: false,
                    data: form_data,
                    type: 'post',
                    success: function (response) {
                        //console.log(response);

                        if (response.more == 1) {
                            $('#console_window').append(response.message + '<br />');

                            importCSV(response.next, filename, response.next_real,response.output_file);
                        } else {
                            $('#console_window').append(response.message + '<br />All Records imported successfully. <br /> <a href="<?php echo Page::IMPORT_FILE_DIR."log/"?>'+response.output_file+'.csv" target="_blank" class="btn btn-xs btn-primary margin-right-10">Click Here to View Service</a>');
                            
                        }
                    }
                });

            }
            
            
        </script>
        <link href="../assets/pages/css/profile.min.css" rel="stylesheet" type="text/css">
        <style type="text/css">

            @import url('https://fonts.googleapis.com/css?family=Orbitron&display=swap');






            .calc-border {
                background: #fff none repeat scroll 0 0;
                border: 1px solid #ccc;
                border-radius: 5px;
                margin-bottom: 25px;
                padding: 5px;
            } 
            .calc-border { border:1px solid #ccc; padding:5px; border-radius:5px; background:#fff;     margin-bottom: 25px;}
            .calc-border h4 { text-align:center
            }

            .dwidht {
                left: -25px;
                position: absolute;
                top: 103px;
                transform: rotate(39deg);
                writing-mode: horizontal-tb;
            }   
            .dlenght {
                left: 80px;
                margin-top: 15px;
                position: absolute;
                top: 126px;
                width: 92px;
            }   
            .dheight {
                position: absolute;
                right: -85px;
                top: 46px;
                transform: rotate(90deg);
                width: 82px;
            }
            .cal-dimesnion {
                background: rgba(0, 0, 0, 0) url("http://www.yourpersonalshopper.com/assets/site/img/dimesion.png") no-repeat scroll 0 0;
                height: 131px;
                margin: 20px auto;
                max-width: 207px;
                position: relative;
                zoom: .7;
            }
            .cal-body{
                margin-bottom: 50px;
            }            
            .control-label {
                float: left;
            }
            .dhead {
                left: 53px;
                position: absolute;
                top: 10px;
            }
            .page-content{
                min-height: 800px !important;
            }
            .cal-screen {
                background: rgba(0, 0, 0, 0) linear-gradient(to bottom, #ffffff 0%, #e5e5e5 100%) repeat scroll 0 0;
                border: 1px solid #ccc;
                border-radius: 10px !important;
                color: #333;
                font-family: "Orbitron",sans-serif;
                font-size: 20px;
                margin-bottom: 10px;
                padding: 10px 10px 0;
            }
            .cal-result h2 {
                color: #000;
                float: right;
                font-family: "Orbitron",sans-serif;
                font-size: 65px;
                font-weight: 500;
                margin: 0px 0 0;
            }
            .dlenght input {
                background: #ff9999 none repeat scroll 0 0 !important;
                color: #000 !important;
            } 
            .dwidhtinput {
                background: #ffdc73 none repeat scroll 0 0 !important;
                color: #000 !important;
                left: -50px;
                position: absolute;
                top: 118px;
                transform: rotate(40deg);
                width: 92px;
            }
            .dheight input {
                background: #99b3ff none repeat scroll 0 0 !important;
                color: #000 !important;
            }
            .control-label { float:left; }
            #radioBtn .notActive {
                background-color: #fff;
                border: 1px solid #3276b1;
                color: #3276b1;
            }
            #radioBtn2 .notActive{   color: #3276b1;   background-color: #fff;}
            #radioBtn .active {
                background: rgba(0, 0, 0, 0) linear-gradient(to bottom, #5b9ae7 0%, #065dd6 100%) repeat scroll 0 0;
                color: #fff;
            }
            .modal-box {
                margin: 0 auto;
                max-width: 500px;
            }   

            .page-breadcrumb { display: none }      

.profile-sidebar-portlet {
    padding: 5px 0 0!important;
    border: 2px solid #ccc !important;
}

.profile-usermenu {
    margin-top: 5px;
    padding-bottom: 0px;
}


.profile-usertitle {
    text-align: center;
    margin-top: 5px;
}

.profile-usermenu ul li a {
    
    font-size: 14px;
    font-weight: bold;

}

/*.profile-usertitle-name {
transform: rotate(90deg);
transform-origin: left top 0;
}*/
.profile-usertitle-name {
 margin-bottom: 0px !important;
 text-align: center;
}

.nav>li>a {
    padding: 8px 15px !important;
}
            @media (min-width: 992px) {
.page-content-wrapper .page-content {
        padding-top: 0px;
}

}   
        </style>

        <?php
    }

    public function renderBody() {
        ?>                
        <div class="main_formpage">
            <div class="portlet light">
                <div class="portlet-title">
                    <div class="caption"><i class="fa fa-upload"></i>
                        Estimated Shipping Calculator
                    </div>
                    <div class="actions">
                        <a href="javascript:;" class="btn blue" id="bulk_duty_taxes_checked" name="bulk_duty_taxes_checked">
                            <i class="fa fa-upload"></i> Import Shipping Data
                        </a>
                    </div>
                </div>
                <div class="portlet-body ">
                    <div class="row">
                        <div class="col-sm-6 box">
                            <div class="profile-sidebar1">
                                <div class="portlet light profile-sidebar-portlet ">
                            <div class="cal-header">                                
                                
                                <div class="alert alert-danger hidden" id="cal-api-error"></div>
                            </div>
                            <div class="cal-body">
                                <div class="form-group">
                                    <div class="col-sm-6">
                                        <label for="textinput" class="control-label">Shopping from?</label>
                                        <select class="form-control calculate_price input-sm" name="calc_shipping_from" id="calc_shipping_from">
                                            <option value="225">United Kingdom</option>
                                            <option value="226">United States</option>
                                            <option value="150">Netherlands</option>
                                            <option value="44">China</option>
                                        </select>
                                    </div>                                                                
                                    <div class="col-sm-6">
                                        <label for="textinput" class="control-label">Ship to?</label>
        <?php echo $this->country_drop; ?>

                                    </div>
                                    <div class="clearfix"></div>
                                </div>
                                <div class="form-group">
                                    <div class="col-sm-6">
                                        <label for="textinput" class="control-label">Currency</label>
                                        <select class="form-control calculate_price input-sm" id="calc_currency" name="calc_currency">
                                            <option data-code="EUR" value="3">Euro (EUR)</option>
                                            <option class="selected-option" selected="" data-code="GBP" value="2">Pound Sterling (GBP)</option>
                                            <option data-code="USD" value="1">US Dollar (USD)</option>
                                        </select>                                    
                                    </div>
                                    <div class="col-sm-6">
                                        <label for="calc_chargeable_weight" class="control-label font-green-soft"><strong>Chargeable Weight</strong></label>
                                        <input type="text" readonly="" class="form-control chargeable_weight input-sm" placeholder="" name="calc_chargeable_weight" id="calc_chargeable_weight">
                                    </div>
                                    <div class="clearfix"></div>
                                </div>
                                <div class="form-group">
                                    <div class="col-sm-6">
                                        <label for="textinput" class="control-label">Category</label>
                                        <select id="categories" class="form-control  input-sm" name="categories" onchange="calculatePrice();">
                                            <?php 
                                                foreach($this->category as $key=>$catName){
                                                    echo '<option value="'.$catName.'">'.$catName.'</option>';
                                                }?>
                                        </select>                                    
                                    </div>
                                    <div class="col-sm-6">
                                        <label for="description" class="control-label">Product Description</label>
                                        <input type="text"  class="form-control description  input-sm" placeholder="" name="description" id="description" onblur="calculatePrice();">
                                    </div>
                                    <div class="clearfix"></div>
                                </div>
                                
                            
                                <div class="form-group">

                                    <div class="col-sm-3">
                                        <label for="calc_value" class="control-label">Value</label>
                                        <input type="number" style="" class="form-control calc_value input-sm" placeholder="0.0" name="calc_value" id="calc_value">
                                    </div>
                                    <div class="col-sm-3">
                                        <label for="calc_length" class="control-label">Length(cm)</label>
                                        <input type="number" style="" class="form-control calculate_price_input input-sm" placeholder="0.0" name="calc_length" id="calc_length">
                                    </div>
                                    <div class="col-sm-3">
                                        <label for="calc_width" class="control-label">Width(cm)</label>
                                        <input type="number" style="" class="form-control calculate_price_input input-sm" placeholder="0.0" name="calc_width" id="calc_width">
                                    </div>
                                    <div class="col-sm-3">
                                        <label for="calc_height" class="control-label">Height (cm)</label>
                                        <input type="number" style="" class="form-control calculate_price_input input-sm" placeholder="0.0" name="calc_height" id="calc_height">
                                    </div>
                                    <div class="clearfix"></div>
                                </div>   

                                    <div id="calc_total_weight_container" class="form-group has-success">
                                    <div class="col-sm-6">
                                        <label class="control-label" for="calc_weight">Weight</label>
                                    </div>
                                    <div class="col-sm-6">
                                    </div>
                                    
                                    <div class="clearfix"></div>
                                    <div class="col-sm-6">
                                        <input type="number" style="" class="form-control calculate_price_input input-sm"  placeholder="" name="calc_weight" id="calc_weight">
                                        <div class="help-block with-errors" id="calc_total_weight_error_container" style="display: none;">Please enter valid weight</div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="btn-group" id="radioBtn2">
                                            <a data-title="kg" data-toggle="calc_scale" class="btn btn-warning  btn-sm active">KGS</a>
                                            <a data-title="lb" data-toggle="calc_scale" class="btn btn-info  btn-sm notActive">LBS</a>
                                        </div>
                                        <input type="hidden" id="calc_scale" value="kg" class="calculate_price" name="calc_scale">
                                    </div>
                                    
                                    <div class="col-sm-2"><i id="calc_loading" class="fa fa-refresh fa-lg fa-spin" style="display: none;"></i></div>
                                    <div class="clearfix"></div>
                                   
                                   <div class="col-sm-6 text-center">
                                     
   <div class="cal-dimesnion">
                                    <span class="dhead"> Dimensions</span>
                                    <span class="dheight">
                                        <span>Height (cm) </span>
                                        <br>
                                        <input type="text" readonly="" class="form-control" placeholder="0.0" name="_calc_height" id="_calc_height">
                                    </span> 
                                    <span class="dlenght">Length (cm) <br>
                                        <input type="text" readonly="" class="form-control" placeholder="0.0" name="_calc_length" id="_calc_length">
                                    </span>
                                    <span class="dwidht">Width (cm) </span>
                                    <input type="text" readonly="" class="form-control dwidhtinput" placeholder="0.0" name="_calc_width" id="_calc_width">
                                    <br clear="all">
                                </div>
                                    </div>

                                    <div class="col-sm-6 text-center">
                                        <br clear="all">
                                        <button class="btn btn-info btn-block" onclick="calculatePrice();">Calculate</button>
                                    </div>
                                    <div class="clearfix"></div>
                          
                                </div>


<br clear="all">

                               
                                


                            </div>
                        </div>    
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="profile-sidebar1">
                                <div class="portlet light profile-sidebar-portlet ">
                                    <!-- SIDEBAR USERPIC -->
                                    <div class="profile-userpic">
                                    <!-- SIDEBAR USER TITLE -->
                                  
                                    <!-- END SIDEBAR BUTTONS -->
                                    <!-- SIDEBAR MENU -->

                                 
                                    <div class="profile-usermenu">

                                        <div class="profile-usertitle-name"> <i class="fa fa-info"></i> Shipment Data </div>
                                        <ul class="nav">
                                            <li class="active">
                                                <a href="javascript:0;">
                                                <div class="row">    <div class="col-md-6"><i class="fa fa-ship"></i> Weight: </div> <div class="col-md-6"><span id="screen_scale"></span></div> </div> </a> 
                                            </li>
                                            <li>
                                                <a href="javascript:0;">
                                                   <div class="row">    <div class="col-md-6">   <i class="fa fa-database"></i> Category: </div><div class="col-md-6"> <span id="screen_category"></span></div> </div> </a>
                                            </li>
                                            <li class="active">
                                               <a href="javascript:0;">
                                              <div class="row">    <div class="col-md-6">       <i class="fa fa-codepen"></i> Description: </div> <div class="col-md-6"><span id="screen_description"></span> </div> </div></a>
                                            </li>
                                             <li>
                                                <a href="javascript:0;">
                                              <div class="row">    <div class="col-md-6">       <i class="fa fa-circle"></i> Parcel Value:</div> <div class="col-md-6"><span id="screen_parcel_value"></span> </div> </div> </a>
                                            </li>
                                             <li class="active">
                                                <a href="javascript:0;">
                                               <div class="row">    <div class="col-md-6">      <i class="fa fa-cube"></i>Dims (W x L x H) </div> <div class="col-md-6"><span id="screen_dim"></span> </div> </div></a>
                                            </li>
                                             <li>
                                                <a href="javascript:0;">
                                               <div class="row">    <div class="col-md-6">      <i class="fa fa-flag"></i>Country: </div> <div class="col-md-6"><span id="screen_from_to"></span> </div> </div></a>
                                            </li>
                                        </ul>
                                    </div>
                                    <!-- END MENU -->
                                </div>
                                </div>
                            </div>
                            <div class="profile-sidebar1">
                                <div class="portlet light profile-sidebar-portlet ">
                                    <!-- SIDEBAR USERPIC -->
                                    <div class="profile-userpic">
                                    <!-- SIDEBAR USER TITLE -->
                            
                                    <!-- END SIDEBAR BUTTONS -->
                                    <!-- SIDEBAR MENU -->
                                    <div class="profile-usermenu">
                                          <div class="profile-usertitle-name">  <i class="fa fa-eye"></i> Landed Cost</div>
                                        <ul class="nav">
                                            <li class="active">
                                                <a href="javascript:0;">
                                                 <div class="row">    <div class="col-md-6">     <i class="fa fa-ship"></i> Commodity Code:</div> <div class="col-md-6">  <span id="screen_commodity"></span> </div> </div> </a> 
                                            </li> 
                                            <li>
                                                <a href="javascript:0;">
                                                <div class="row">    <div class="col-md-6">      <i class="fa fa-database"></i> Hscode: </div> <div class="col-md-6"><span id="screen_hscode"></span></div> </div>  </a>
                                            </li>
                                            <li class="active">
                                               <a href="javascript:0;">
                                               <div class="row">    <div class="col-md-6">       <i class="fa fa-codepen"></i> Duty & Taxes: </div> <div class="col-md-6"><span id="screen_duty_taxes"></span> </div> </div></a>
                                            </li>
                                             <li>
                                                <a href="javascript:0;">
                                                <div class="row">    <div class="col-md-6">      <i class="fa fa-circle"></i> Clearence Fee:</div> <div class="col-md-6"> <span id="screen_clearence_fee"></span> </div> </div></a>
                                            </li>
                                             <li class="active">
                                                <a href="javascript:0;">
                                                <div class="row">    <div class="col-md-6">      <i class="fa fa-cube"></i>Shipping Cost: </div> <div class="col-md-6"> <span id="screen_shipping_code"></span></div> </div> </a>
                                            </li>
                                             <li>
                                                <a href="javascript:0;">
                                                 <div class="row">    <div class="col-md-6">     <i class="fa fa-flag"></i>Total: </div> <div class="col-md-6"><span id="screen_total"></span> </div> </div></a>
                                            </li>



                                        </ul>
                                    </div>
                                    <!-- END MENU -->
                                </div>
                                </div>
                            </div>
                    </div>
                </div>  
            </div>
        </div>

        
        
        
        <!--Model for CSV Upload-->
    <div class="modal fade" id="csv_upload" tabindex="-1" role="basic" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                    <h4 class="modal-title">Import File For Duty Taxes</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <div class="fileinput fileinput-new" data-provides="fileinput">
                                    <div class="input-group input-sm">
                                        <div class="form-control uneditable-input input-fixed input-sm" data-trigger="fileinput">
                                            <i class="fa fa-file fileinput-exists"></i>&nbsp;
                                            <span class="fileinput-filename"> </span>
                                        </div>
                                        <span class="input-group-addon btn default btn-file">
                                            <span class="fileinput-new"> Select file </span>
                                            <span class="fileinput-exists"> Change </span>
                                            <input type="file" name="file_in" id="file_in">
                                        </span>
                                        <a href="javascript:;" class="input-group-addon btn red fileinput-exists" data-dismiss="fileinput"> Remove </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                            <div id="console_window" style="clear:both; border-style: solid; border-width:1px;  width: 100%; padding: 15px; display: none;">

                        </div>
                        <div style="clear:both;"></div>
                    </div>
                    <div style="clear:both;"></div>
                </div>
                <div class="modal-footer">
                    <a id="download_csv_btn" class="btn btn-sm btn-primary" href="?get_duty_taxes_template=<?php echo md5('download_template'); ?>">  <i class="fa fa-download"></i> Download Template </a>
                   <button type="button" class="btn dark  btn-sm btn-outline" data-dismiss="modal">Close</button>
                   <button type="button" id="btnSubmitImport" class="btn  btn-sm green">Upload</button>
               </div>
           </div>
           <!-- /.modal-content -->
       </div>
       <!-- /.modal-dialog -->
    </div>
        
        
        <?php
    }

    /**
     * Override to show the menu
     *
     */
    public function renderMenu() {
        $menu = new Adminmenu(Adminmenu::DASHBOARD);
        $menu->render();
    }

    /*     * *
     * Controller logic goes here
     */

    public function init() {
        
        SessionManager::checkUserAccess(User::PRIVILEGE_IMPORT);
        $user = SessionManager::getUser();
        
        $this->breadCrumb['data'] = array('index.php' => Translation::GetCaption("HOME"),
            'calculator.php' => 'Calculator'
        );
       
        $country = '';
        $country .= '<select class="form-control calculate_price" id="calc_shipping_to" name="calc_shipping_to">';
        //$country .= '<option>--- Please Select ---</option>';
        $countriesList = new CountryFilter();
        $countryListData = $countriesList->getList();
        foreach ($countryListData as $countryItem) {
            $country .= '<option value="' . $countryItem->getId() . '"' . ($countryItem->getId() == '225' ? ' selected="selected"' : '') . '>' . $countryItem->getName() . '</option>';
        }
        $country .= '</select>';
        $this->country_drop = $country;

        if (isset($_GET["get_duty_taxes_template"]) && $_GET["get_duty_taxes_template"] == md5('download_template')) {
            $array = [];
            $array[] = [  'Origin Country',
                        'Destination Country',
                        'Weight',
                        'Weight Unit',
                        'Value',
                        'Currency',
                        'Quantity',
                        'Dims unit',
                        'Length',
                        'Width',
                        'Height',
                        'Category',
                        'Description'];
            $array[] = [  'CN',
                        'GB',
                        1.20,
                        'KG',
                        '15',
                        'GBP',
                        '1',
                        'cm',
                        '10',
                        '10',
                        '10',
                        'Fashion',
                        'shirts'];
            header('Content-Description: File Transfer');
            header('Content-Type: application/csv');
            header("Content-Disposition: attachment; filename=template-duty-taxes.csv");
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            $handle = fopen('php://output', 'w');
            ob_clean(); // clean slate
            fputcsv($handle, $array[0]);   // direct to buffered output
            fputcsv($handle, $array[1]);   // direct to buffered output
            ob_flush(); // dump buffer
            fclose($handle);
            die;            
        }
        if (isset($_POST['func']) && $_POST['func'] == 'upload_csv_file') {

            $output = array();
            $output['status'] = 'success';
            $output['message'] = 'Uploaded successfully.';
            @$csv_file = $_FILES['csv_file'];
            if (!empty($csv_file['name'])) {
                $file_name1 = $csv_file['name'];
                $path_parts = pathinfo($file_name1);
                $ext = strtolower($path_parts['extension']);
                $basename = $path_parts['basename'];

                if ($ext == 'csv') {
                    $user = SessionManager::getUser();
                    $account = $user->getAccount();
                    $new_file_name = $account . "_" . time() . "_" . $basename;
                    $file_name = Page::IMPORT_FILE_DIR . $new_file_name;
                    if (!file_exists(Page::IMPORT_FILE_DIR)) {
                        mkdir(Page::IMPORT_FILE_DIR, 0777, true);
                    }
                    if (move_uploaded_file($csv_file['tmp_name'], $file_name)) {
                        $output['file_name'] = $new_file_name;
                        $file_object = new InputFile1($file_name);
                        if (!$file_object->exists()) {
                            $output['message'] = "Enable to open file" . $new_file_name;
                            $output['status'] = 'fail';
                            //exit;
                        }
                    } else {
                        $output['message'] = 'File upload fail.';
                        $output['status'] = 'fail';
                    }
                } else {
                    $output['message'] = 'Invalid CSV File.';
                    $output['status'] = 'fail';
                }
            } else {
                $output['message'] = 'No file found to import data.';
                $output['status'] = 'fail';
            }
            echo json_encode($output);
            exit;
        }
        if (isset($_POST['func']) && $_POST['func'] == 'import_file') {

            $limit = Page::IMPORT_BATCH_MAX;
            $start = $_POST['start'];
            $start_real = $_POST['start_real'];
           // $start_real = ($start_real== 0?1:$start_real);
            $file_name = $_POST['filename'];
            $output_file = (trim(@$_POST['output_file']) == ''?"_" . date("YmdHis") . "_duty_taxes":$_POST['output_file']);
            $output = array();

            $output['more'] = 1;
            $output['output_file'] = $output_file;            
            $output['next_real'] = $start_real + $limit;

            //Page::IMPORT_FILE_DIR .$file_name;
            $file_object = new InputFileDutyTaxes(Page::IMPORT_FILE_DIR . $file_name);
            
            $file_object->setPosition($start);

            $output['error_message'] = array();
            if ($file_object == null) {
                $output['error_message'][] = "Cannot open File" . $file_name;
            } else {
                $more_rows = true;
                $successCount = 0;
                $failureCount = 0;
                $file_object->setPosition($start);
                $output['position'] = $start . ' -- ' . print_r($file_object->setPosition($start), true);
                for ($row_idx = 0; $more_rows && ($row_idx < $limit); $row_idx++) {
                    $output['position_next'] = $file_object->getPosition() . ' -- ';
                    
                    if ($file_object->nextRow()) {
                        if($output['position_next'] == 0)
                          continue;
                        $result = $this->checkDutyAndTaxes($file_object);

                        if ($result['status']) {
                            $successCount++;
                        } else {
                            $failureCount++;
                            $output['error_message'][] = $result['error'];
                        }
                        $output['next'] = $file_object->getPosition();
                    } else {
                        $more_rows = false;
                        $output['more'] = 0;
                    }
                }
                if ($this->csv != '') {
                    $folder_path = Page::IMPORT_FILE_DIR."log";
                    if (!file_exists($folder_path)) {
                        mkdir($folder_path, 0777, true);
                    }
                    $uniqueFileName = $output_file;
                    $file_path = $folder_path . "/" . $uniqueFileName . ".csv";
                    $csvHeaderName = new InputFileDutyTaxes();
                    if(!file_exists($file_path))
                        $csvHeader = ucwords( str_replace('_', ' ', implode(",",array_keys($csvHeaderName->csv_header() )) ) ."\r\n") ;
                    else
                        $csvHeader = "";
                    $mode = (!file_exists($file_path)) ? 'w':'a';
                    $file_path = fopen($file_path, $mode);
                    fwrite($file_path, $csvHeader . $this->csv);
                    // close file
                    fclose($file_path);
                }
            }
            $output['message'] = 'Done[' . $successCount . '] Fail[' . $failureCount . ']' . (count($output['error_message']) > 0 ? '<span style="color:red">' . implode("", $output['error_message']) . '</span>' : '');
            echo json_encode($output);
            exit;
        }

        
        if (isset($_POST["UpcomingItemCall"]) && $_POST["UpcomingItemCall"] == 1) {
            $grandTotal = 0;
            $insuranceFee = 0;
            $shippingFee = 0;
            //$fromCurrency= 'GBP';
            $personalShopperFee = 0;
            $ExchangeRate = 1;

            $resultText = '';

            $DestinationCountryId = trim($_POST['DestinationCountryId']);
            $dest_country = new CountryFilter();
            $dest_country->addCountryIdByFilter($DestinationCountryId); // destination country
            $destinationCOuntry = $dest_country->getList();
            $sour_country = new CountryFilter();
            $sour_country->addCountryIdByFilter($_POST['SourceCountryId']); // destination country
            $sourceCountry = $sour_country->getList();


            $sservice = $_POST['service'];
            $result_currency_id = $_POST['CurrencyId']; // resulting currency id

            /*if ($_POST['WeightSymbol'] == "lb") {
                $parcelWeightInKg = $_POST['parcelWeight'] / 2.2046;
                $parcelWeightInLb = $_POST['parcelWeight'];
            } else if ($_POST['WeightSymbol'] == "kg") {
                $parcelWeightInLb = $_POST['parcelWeight'] * 2.2046;
                $parcelWeightInKg = $_POST['parcelWeight'];
            }*/
            $weightSymbol       = $_POST['WeightSymbol'];
            $description        = $_POST['description'];
            $item_value         = $_POST['calc_value'];
            $calc_length        = $_POST['calc_length'];
            $calc_width         = $_POST['calc_width'];
            $calc_height        = $_POST['calc_height'];
            $weight             = $_POST['parcelWeight'];
            $categories         = $_POST['categories'];
            $description        = $_POST['description'];
            $categories        = $_POST['categories'];
            
            $dimsUnit           = 'cm';
            $quantity           = 1;
                    
          /*  $result = Tariffs::CalculateTariff($sourceCountry[0]->getIso(), $destinationCOuntry[0]->getIso(), $Service, $parcelWeightInKg, $Pieces, $result_currency_id);

            //echo $result;

            $arr = explode('||', $result);
            $shippingFee = $arr[0]; //////////// api rate
            $CurSym = $arr[1]; ////////// api currency symbol	
            $api_currency_id = $arr[2]; //////// currency id
            $TansitTime = $arr[3]; //////// Transit Time*/
            //////////////// USER SELECTED CURRENCY ///////////
            $currency = new Currency($_POST['CurrencyId']);
            $ExchangeRate = $currency->getCurrencyexchangerate();
            $currencyRightSymbol = $currency->getRightsymbol();
            $fromCountry = $sourceCountry[0]->getIso();
            $toCountry = $destinationCOuntry[0]->getIso();
            $res = array();
            //$res['parcelWeighrKg'] = number_format($parcelWeightInKg, 2);
            //$res['parcelWeighrLb'] = number_format($parcelWeightInLb, 2);
            //$res['shippingFee'] = number_format($shippingFee, 2);
            //$res['CurSym'] = $CurSym;
            

            $dutyCalculation = Yakit::hsCodeDutyCalculator($fromCountry,$toCountry, $currencyRightSymbol, $categories,  $description, $quantity, $item_value,  $weightSymbol, $weight, $dimsUnit, $calc_length, $calc_width, $calc_height);
            if(!$dutyCalculation['status']){
                $res['status']  =   $dutyCalculation['status'];
                $res['message']  =   $dutyCalculation['message'];
            }else{
                //echo "<pre>";print_r($dutyCalculation);die;
                
                if(isset($dutyCalculation['shipment']->error))
                {
                   
                    $res['status']          =   false;
                    if(is_object($dutyCalculation['shipment']->error))
                        $res['message']         =   $dutyCalculation['shipment']->error->message;
                    else if(is_array($dutyCalculation['shipment']->error))
                        $res['message']         =   $dutyCalculation['shipment']->error[0]->message;
                    else 
                        $res['message']         =  "Unknown error format";
                }else if(isset($dutyCalculation['hscode']->error))
                {
                    $res['status']          =   false;
                    if(is_object($dutyCalculation['hscode']->error))
                        $res['message']         =   $dutyCalculation['hscode']->error->message;
                    else if(is_array($dutyCalculation['hscode']->error))
                        $res['message']         =   $dutyCalculation['hscode']->error[0]->message;
                    else 
                        $res['message']         =  "Unknown error format";
                }
                else{
                    $res['status']          =   true;
                    $res['CurSym']          =   $currencyRightSymbol;
                    $res['shippingFee']     =   $dutyCalculation['shipment']->ttdList[0]->countryList[0]->deliveryCharge;
                    $res['fromCountry']     =   $fromCountry;
                    $res['toCountry']       =   $toCountry;
                    $res['dutiesTaxes']     =   $dutyCalculation['hscode']->dutiesTaxes;
                    $res['unit']            =   $dutyCalculation['hscode']->unit;
                    $res['hscode']          =   $dutyCalculation['hscode']->classifications[0]->hsCode;
                }
            }
            echo json_encode($res);
            exit;
        }


        $sessionManager = Sessionmanager::getUser();

        $userAccountId = $sessionManager->getUserAccountId();
        $user_name = $sessionManager->getUserName();
        $user_pass = $sessionManager->getUserPass();
        $user_account = $sessionManager->getUseraccount();
        //echo '<pre>';print_r($sessionManager);echo '</pre>';
        $this->isProduct = $sessionManager->getIsProduct();
        if ($sessionManager->getIsProduct() == 'YES') {
            $customizedUserServicesRoutingFilter = new customizedUserServicesRoutingFilter();
            $customizedUserServicesRoutingFilter->addUserAccountFilter($user_account);
            $this->serviceOrProduct = $customizedUserServicesRoutingFilter->getColumnList('routing_name');
        } else if ($sessionManager->getIsProduct() == 'NO') {
            $CustomizedServicesRoutingFilter = new CustomizedServicesRoutingFilter();
            $CustomizedServicesRoutingFilter->addUserAccountIdFilter($userAccountId);
            $this->serviceOrProduct = $CustomizedServicesRoutingFilter->getColumnList('service_name');
        }
//        $language = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
//        Translation::PopulateKeywordsArray($language);

        t_on(); // turn on trace for this page
        // Log user out?
//        if (util_request("logout") == "true") {
//            $data = $_SESSION["user_account"];
//            User::logUserOut();
//            //util_redirect("../main/index.php");
//            //	echo "../main/index.php?data=".$data;
//            //	die;
//            util_redirect("../main/index.php?data=" . $data);
//            die;
//        }


        /* ------------------------------------------------------------------------------ */
        // get vars
//        $this->username = (isset($_POST['username']) ? strip_tags($_POST['username']) : '');
//        $this->password = (isset($_POST['password']) ? strip_tags($_POST['password']) : '');
//        $this->user_name = (isset($_POST['user_name']) ? strip_tags($_POST['user_name']) : '');

        /* ------------------------------------------------------------------------------ */
//		print_r($_POST);
        // process form
    }
    /**
     * Import one consignment
     * - set status to INVALID (ignore user feedback) or VALID
     *
     * @param $file_object (with file row already extracted)
     */
    private function checkDutyAndTaxes(InputFileDutyTaxes $file_object) {
        
        $dutiesTaxes = 0.00;
        $clearence = $this->clearenceFee;
        $shippingFee = 0.00;
            
        $csvHeadersData = [];
        $getError = false;
        $error = '';
        $res = array();
        $res['status'] = true;
        $res['error'] = '';
        $error_array = array();
        
        $origin_country_iso = $file_object->getField("origin_country_iso");
        $destination_country_iso = $file_object->getField("destination_country_iso");
        $weight = $file_object->getField("weight");
        $weight_unit = $file_object->getField("weight_unit");
        $item_value = $file_object->getField("item_value");
        $value_currency = $file_object->getField("value_currency");
        $quantity = $file_object->getField("quantity");
        $dims_unit = $file_object->getField("dims_unit");
        $parcel_length = $file_object->getField("parcel_length");
        $parcel_width = $file_object->getField("parcel_width");
        $parcel_height = $file_object->getField("parcel_height");
        $description = $file_object->getField("description");
        $categories = $file_object->getField("categories");
      
        $dutyCalculation = Yakit::hsCodeDutyCalculator(
                                    $origin_country_iso,
                                    $destination_country_iso, 
                                    $value_currency, 
                                    $categories, 
                                    $description, 
                                    $quantity, 
                                    $item_value,  
                                    $weight_unit, 
                                    $weight, 
                                    $dims_unit, 
                                    $parcel_length, 
                                    $parcel_width, 
                                    $parcel_height
                                );

//        /$csvHeaders = InputFileDutyTaxes::csv_header();
        if(!$dutyCalculation['status']){
                $res['status']      =   $dutyCalculation['status'];
                $res['error']     =   $dutyCalculation['message'];
            }else{
                //echo "<pre>";print_r($dutyCalculation);die;
                
                if(isset($dutyCalculation['shipment']->error))
                {
                   
                    $res['status']          =   false;
                    if(is_object($dutyCalculation['shipment']->error))
                        $res['error']         =   $dutyCalculation['shipment']->error->message;
                    else if(is_array($dutyCalculation['shipment']->error))
                        $res['error']         =   $dutyCalculation['shipment']->error[0]->message;
                    else 
                        $res['error']         =  "Unknown error format";
                }else if(isset($dutyCalculation['hscode']->error))
                {
                    $res['status']          =   false;
                    if(is_object($dutyCalculation['hscode']->error))
                        $res['error']         =   $dutyCalculation['hscode']->error->message;
                    else if(is_array($dutyCalculation['hscode']->error))
                        $res['error']         =   $dutyCalculation['hscode']->error[0]->message;
                    else 
                        $res['error']         =  "Unknown error format";
                }
                else{
                    $res['status']          =   true;
                    $res['data']['CurSym']          =   $currencyRightSymbol;
                    $shippingFee = $res['data']['shippingFee']     =   $dutyCalculation['shipment']->ttdList[0]->countryList[0]->deliveryCharge;
                    $res['data']['fromCountry']     =   $fromCountry;
                    $res['data']['toCountry']       =   $toCountry;
                    $dutiesTaxes = $res['data']['dutiesTaxes']     =   $dutyCalculation['hscode']->dutiesTaxes;
                    $res['data']['unit']            =   $dutyCalculation['hscode']->unit;
                    $res['data']['hscode']          =   $dutyCalculation['hscode']->classifications[0]->hsCode;
                }
            }
            
            $csvHeadersData[] = $origin_country_iso;
            $csvHeadersData[] = $destination_country_iso;
            $csvHeadersData[] = $weight;
            $csvHeadersData[] = $weight_unit;
            $csvHeadersData[] = $item_value;
            $csvHeadersData[] = $value_currency;
            $csvHeadersData[] = $quantity;
            $csvHeadersData[] = $dims_unit;
            $csvHeadersData[] = $parcel_length;
            $csvHeadersData[] = $parcel_width;
            $csvHeadersData[] = $parcel_height;
            $csvHeadersData[] = $categories;
            $csvHeadersData[] = $description;
            $csvHeadersData[] = (isset( $res['status'])?'success':'error');
            $csvHeadersData[] = (isset( $res['status'])?'':$res['error'] );
            $csvHeadersData[] = $commodity;
            $csvHeadersData[] =  @$res['data']['hscode'];
            $csvHeadersData[] = $dutiesTaxes;
            $csvHeadersData[] = $clearence;
            $csvHeadersData[] = $shippingFee ;
            $csvHeadersData[] = ( $dutiesTaxes+ $shippingFee + $clearence);
            $csvHeadersData[] = "\r\n";
            
            $this->csv .= implode(",",$csvHeadersData);
            
        return $res;
    }

// end function
// end function

    public function replaceSpecial($str) {
        $chunked = str_split($str, 1);
        $str = "";
        foreach ($chunked as $chunk) {
            $num = ord($chunk);
            // Remove non-ascii & non html characters
            if ($num >= 32 && $num <= 123) {
                $str .= $chunk;
            }
        }
        return mb_convert_encoding($str, 'UTF-8');
    }

    function time_elapsed($last = null) {
        //static $last = null;
        $now = microtime(true);
        if ($last != null) {
            //echo '<!-- ' . ($now - $last) . ' -->';
            return ($now - $last);
        }
        //$last = $now;
    }
    
}

/* ------------------------------------------------------------------------------ */
// create and render page
$PageObj = new Page(CONFIG_TEMPLATE_ADMIN);
$PageObj->show();
?>


