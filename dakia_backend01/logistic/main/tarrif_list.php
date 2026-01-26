<?php
// get settings
require_once("../includes/settings/config.inc.php");
@session_start;

class Page extends BasePage {

    private $country_drop;
    private $serviceOrProduct;
    private $isProduct;

    protected function init() {


        // user must be CLIENT
        $user = SessionManager::getUser();
        if ($user->getUserType() != "finance" && $user->getUserType() != "admin") {
            util_redirect("index.php");
        }

        if ($user == NULL) {
            util_redirect("../main/index.php");
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


            $Service = $_POST['service'];
            $result_currency_id = $_POST['CurrencyId']; // resulting currency id

            if ($_POST['WeightSymbol'] == "lb") {
                $parcelWeightInKg = $_POST['parcelWeight'] / 2.2046;
                $parcelWeightInLb = $_POST['parcelWeight'];
            } else if ($_POST['WeightSymbol'] == "kg") {
                $parcelWeightInLb = $_POST['parcelWeight'] * 2.2046;
                $parcelWeightInKg = $_POST['parcelWeight'];
            }
            $result = Tariffs::CalculateTariff($sourceCountry[0]->getIso(), $destinationCOuntry[0]->getIso(), $Service, $parcelWeightInKg, $Pieces, $result_currency_id);

            //echo $result;

            $arr = explode('||', $result);
            $shippingFee = $arr[0]; //////////// api rate
            $CurSym = $arr[1]; ////////// api currency symbol	
            $api_currency_id = $arr[2]; //////// currency id
            $TansitTime = $arr[3]; //////// Transit Time
            //////////////// USER SELECTED CURRENCY ///////////
            $currency = new CurrencyFilter();
            $currency->addCurrencyIdByFilter($_POST['CurrencyId']);
            $ExChangeRate = $currency->getList();
            $ExchangeRate = $ExChangeRate[0]->getCurrencyexchangerate();

            $res = array();
            $res['parcelWeighrKg'] = number_format($parcelWeightInKg, 2);
            $res['parcelWeighrLb'] = number_format($parcelWeightInLb, 2);
            $res['shippingFee'] = number_format($shippingFee, 2);
            $res['CurSym'] = $CurSym;
            $res['fromCountry'] = $sourceCountry[0]->getIso();
            $res['toCountry'] = $destinationCOuntry[0]->getIso();

            echo json_encode($res);
            exit;
        }




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

        $sessionManager = Sessionmanager::getUser();

        $user_id = $sessionManager->getId();
        $user_name = $sessionManager->getUserName();
        $user_pass = $sessionManager->getUserPass();
        $user_account = $sessionManager->getUseraccount();
        //echo '<pre>';print_r($sessionManager);echo '</pre>';
        $this->isProduct = $sessionManager->getIsProduct();
        if ($sessionManager->getIsProduct() == 'YES') {
            $RoutingUserMappingFilter = new CustomizedUserServicesRoutingFilter();
            $RoutingUserMappingFilter->addUserAccountFilter($user_account);
            $this->serviceOrProduct = $RoutingUserMappingFilter->getColumnList('routing_name');
        } else if ($sessionManager->getIsProduct() == 'NO') {
            $PartnerServicesRoutingFilter = new PartnerServicesRoutingFilter();
            $PartnerServicesRoutingFilter->addUserIdFilter($user_id);
            $this->serviceOrProduct = $PartnerServicesRoutingFilter->getUserAllowServiceList();
        }


        //	t_on(); // turn on trace for this page
    }

    /**
     * Force page refresh if importing
     */
    /*     * *
     * Content View
     */
    protected function renderBody() {
        ?>





        <br><br>
        <div class="main_formpage">
            <div class="portlet box blue">
                <div class="portlet-title">
                    <div class="caption"> <i class="icon-docs"></i>
                        Tarrif Catalog</div>
                    <div class="tools"> <a href="javascript:;" class="collapse" data-original-title="" title=""> </a> <a href="" class="fullscreen" data-original-title="" title=""> </a> <a href="#portlet-config" data-toggle="modal" class="config" data-original-title="" title=""> </a> </div>
                </div>

                <div class="portlet-body">
                    <?php errorList::getItem()->render(); ?>
                    <div class="row">
                        <div class="form-group col-md-12">
                            <a href="../csv/ECONOMY-TRACKED.xlsx" target="_blank" class="btn btn-primary">Download Tarrif</a>
                            <a href="view_tarrif.php" class="btn btn-primary">View Tarrif</a>                            
                            <a data-toggle="modal" data-target="#calc_modal" class="btn btn-primary">Estimated Shipping Cost Calculator</a>        
                            <a href="tarifflist.php" class="btn btn-primary">Tarrif List</a>        
                        </div>	
                    </div>
                </div>
                <div style="clear:both;"></div>

            </div>
        </div>


        <input type="hidden" name="form_action" id="form_action" value="<?php echo @$form_action; ?>"  />

        <div id="calc_modal" class="modal fade modal-transparent modal-fullscreen in" style="display: none;" aria-hidden="false"><div class="modal-backdrop fade in" style="height: 745px;"></div>
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button aria-label="Close" data-dismiss="modal" class="close" type="button"><span aria-hidden="true">×</span></button>
                        <h3 class="heading-cal"> ESTIMATED SHIPPING CALCULATOR</h3>
                    </div>
                    <div class="modal-body">
                        <div class="modal-box">
                            <div class="cal-header">                                
                                <div class="cal-screen">
                                    <div id="screen_currency" class="col-md-2 col-sm-2 col-xs-2 text-center">GBP</div>
                                    <div id="screen_scale" class="col-md-2 col-sm-2 col-xs-2 text-center">kg</div>
                                    <div id="screen_dim" class="col-md-4 col-sm-4 col-xs-4 text-center">W L H</div>
                                    <div id="screen_from_to" class="col-md-4 col-sm-4 col-xs-4 text-center">GB to US</div>
                                    <div class="col-md-12">
                                        <span class="cal-result">
                                            <h2 id="screen_price"></h2>
                                        </span>
                                    </div>
                                    <div class="clearfix"></div>
                                </div>
                            </div>
                            <div class="cal-body">
                                <div class="form-group">
                                    <div class="col-sm-6">
                                        <label for="textinput" class="control-label">Currency</label>
                                        <select class="form-control calculate_price" id="calc_currency" name="calc_currency"><option data-code="EUR" value="3">Euro (EUR)</option><option class="selected-option" selected="" data-code="GBP" value="2">Pound Sterling (GBP)</option><option data-code="AED" value="4">UAE  Dirham (AED)</option><option data-code="USD" value="1">US Dollar (USD)</option></select>                                    </div>
                                    <div class="col-sm-6">
                                        <label for="calc_chargeable_weight" class="control-label font-green-soft"><strong>Chargeable Weight</strong></label>
                                        <input type="text" readonly="" class="form-control chargeable_weight" placeholder="" name="calc_chargeable_weight" id="calc_chargeable_weight">
                                    </div>
                                    <div class="clearfix"></div>
                                </div>
                                <div class="form-group">
                                    <div class="col-sm-6">
                                        <label for="textinput" class="control-label">Shopping from?</label>
                                        <select class="form-control calculate_price" name="calc_shipping_from" id="calc_shipping_from">
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
                                <div id="calc_total_weight_container" class="form-group has-success">
                                    <div class="col-sm-4">
                                        <label class="control-label" for="calc_weight">Weight</label>
                                    </div>
                                    <div class="col-sm-4">
                                    </div>
                                    <div class="col-sm-4">
                                        <label class="control-label" for="service">Services/Products</label>

                                    </div>
                                    <div class="clearfix"></div>
                                    <div class="col-sm-4">
                                        <input type="number" style="" class="form-control calculate_price_input" placeholder="" name="calc_weight" id="calc_weight">
                                        <div class="help-block with-errors" id="calc_total_weight_error_container" style="display: none;">Please enter valid weight</div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="btn-group" id="radioBtn2">
                                            <a data-title="kg" data-toggle="calc_scale" class="btn btn-warning  btn-md active">KGS</a>
                                            <a data-title="lb" data-toggle="calc_scale" class="btn btn-info  btn-md notActive">LBS</a>
                                        </div>
                                        <input type="hidden" id="calc_scale" value="kg" class="calculate_price" name="calc_scale">
                                    </div>
                                    <div class="col-sm-4">                                        
                                        <select class="form-control calculate_price" name="service" id="service">
                                            <?php
                                            $Service_or_products = '';
                                            foreach ($this->serviceOrProduct as $data) {
                                                $DDL_Value = '';
                                                $DDL_Text = '';
                                                if ($this->isProduct == 'YES') {
                                                    $DDL_Value = $data->getRoutingName();
                                                    $DDL_Text = $data->getRoutingName();
                                                } else if ($this->isProduct == 'NO') {
                                                    $DDL_Value = $data->getCarrier();
                                                    $DDL_Text = $data->getServiceName();
                                                }
                                                ?>
                                                <option value="<?php echo $DDL_Value; ?>"><?php echo $DDL_Text; ?></option>
                                            <?php } ?>
                                        </select>                                        
                                    </div>
                                    <div class="col-sm-2"><i id="calc_loading" class="fa fa-refresh fa-lg fa-spin" style="display: none;"></i></div>
                                    <div class="clearfix"></div>
                                </div>
                                <div class="form-group">

                                    <div class="col-sm-3">
                                        <label for="calc_length" class="control-label">Length</label>
                                        <input type="number" style="" class="form-control calculate_price_input" placeholder="0.0" name="calc_length" id="calc_length">
                                    </div>
                                    <div class="col-sm-3">
                                        <label for="calc_width" class="control-label">Width</label>
                                        <input type="number" style="" class="form-control calculate_price_input" placeholder="0.0" name="calc_width" id="calc_width">
                                    </div>
                                    <div class="col-sm-3">
                                        <label for="calc_height" class="control-label">Height</label>
                                        <input type="number" style="" class="form-control calculate_price_input" placeholder="0.0" name="calc_height" id="calc_height">
                                    </div>
                                    <div class="col-sm-3">
                                        <label class="control-label" for="calc_dim_unit">Unit</label>
                                        <select name="calc_dim_unit" id="calc_dim_unit" class="form-control calculate_price">
                                            <option value="cm">cm</option>
                                            <option value="in">In</option>
                                        </select>
                                    </div>

                                    <div class="clearfix"></div>
                                </div>    
                                <div class="cal-dimesnion">
                                    <span class="dhead"> Dimensions</span>
                                    <span class="dheight">
                                        <span>Height (<span class="calc_dim_label_unit">cm</span>) </span>
                                        <br>
                                        <input type="text" readonly="" class="form-control" placeholder="0.0" name="_calc_height" id="_calc_height">
                                    </span> 
                                    <span class="dlenght">Length (<span class="calc_dim_label_unit">cm</span>) <br>
                                        <input type="text" readonly="" class="form-control" placeholder="0.0" name="_calc_length" id="_calc_length">
                                    </span>
                                    <span class="dwidht">Width (<span class="calc_dim_label_unit">cm</span>) </span>
                                    <input type="text" readonly="" class="form-control dwidhtinput" placeholder="0.0" name="_calc_width" id="_calc_width">
                                </div>
                            </div>
                        </div>
                        <br /><br />
                    </div>
                </div>
            </div>
        </div>



        <?php
        // report any errors
        // has file been chosen yet?		
    }

    /*     * *
     * This page's content
     * @return void
     */

    public function renderHead() {
        ?>
        <script type="text/javascript">
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
            function calculatePrice() {
                var calc_currency = $("#calc_currency").val();
                var calc_scale = $("#calc_scale").val();
                var calc_shipping_from = $("#calc_shipping_from").val();
                var calc_shipping_to = $("#calc_shipping_to").val();
                var calc_weight = $.trim($("#calc_weight").val());
                var calc_width = $("#calc_width").val();
                var calc_length = $("#calc_length").val();
                var calc_height = $("#calc_height").val();
                var service = $('#service').val();
                var calc_dim_unit = $("#calc_dim_unit").val();
                $(".calc_dim_label_unit").html(calc_dim_unit);
                calc_weight = parseFloat(calc_weight);
                var weight = calc_weight;
                if (calc_weight != "" && calc_width != "" && calc_length != "" && calc_height != "") {
                    var volumetric_weight = weight;
                    if(calc_dim_unit == 'cm')
                        volumetric_weight = (calc_width * calc_length * calc_height) / 5000;
                    else if(calc_dim_unit == 'in')
                        volumetric_weight = (calc_width * calc_length * calc_height) / 138.4;
                    
                    volumetric_weight = volumetric_weight.toFixed(2);
                    weight = volumetric_weight > calc_weight ? volumetric_weight : calc_weight;
                }
                if($.trim(weight) == '')
                    weight = 0;
                $("#calc_chargeable_weight").val(weight);
                if (validateWeight(weight, 'calc_')) {
                    $("#calc_loading").show();
                    $.post(
                            "/remote/main/tarrif_list.php",
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
                                service: service                                
                            },
                    function(response) {
                        //console.log(response);
                        if(typeof response =='object'){
                            var CurSym = (response.CurSym != null ? response.CurSym : 'N/A');
                            var shippingFee = (response.shippingFee != null ? response.shippingFee : 'N/A');
                            $("#screen_currency").html(CurSym);
                            $("#screen_scale").html(calc_scale);
                            $("#screen_dim").html('W' + calc_width + ' L' + calc_length + ' H' + calc_height);
                            $("#screen_from_to").html(response.fromCountry + " to " + response.toCountry);
                            $("#screen_price").html(shippingFee);
                            $("#calc_loading").hide();
                        }else{
                            console.log(response);
                        }
                    },"json");
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
            });
        </script>
        <style type="text/css">
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
                background: rgba(0, 0, 0, 0) url("images/dimesion.png") no-repeat scroll 0 0;
                height: 131px;
                margin: 20px auto;
                max-width: 207px;
                position: relative;
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
                color: #ccc;
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
                margin: 20px 0 0;
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
        </style>

        <?php
    }

    public function renderMenu() {
        $menu = new Adminmenu(Adminmenu::COURIERS);
        $menu->render();
    }

}

// class

/* ------------------------------------------------------------------------------ */
// create and render page
$page = new Page(CONFIG_TEMPLATE_ADMIN);
$page->show();
