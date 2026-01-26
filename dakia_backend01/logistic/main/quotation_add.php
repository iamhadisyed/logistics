<?php
// get settings
require_once("../includes/settings/config.inc.php");
require_once("../Classes/PHPExcel.php");
    include_classes([
        'tcpdf'
        ], '3rdparty/tcpdf');
    include_classes([   
                    'carrier.class',
                    'carrierfilter.class',
                    'services.class' ,
                    'servicefilter.class',
                    'country.class',
                    'countryfilter.class',
                    'quotationdetails.class',
                    'quotationdetailsfilter.class',
                    'currency.class',
                    'currencyfilter.class',
                    'tariffs.class',]);
    
class Page extends BasePage {
    /*     * *
     * Controller logic
     */
    
    private $user = '';

    protected function init() {
        $this->breadCrumb['data'] = array(
            'index.php' => Translation::GetCaption("HOME"),
            "Quotation Add"
        );
        $this->user = SessionManager::getUser();
        
//        echo '<pre>';
//        print_r(Tariffs::getUserQuotationsByAssignedServices(148, 225, 225,"BF12AT", "BF12AT", "BFPO","BFPO",2, 2));
//        echo '</pre>';
//        die;
        
        if (isset($this->form_vars['action']) && $this->form_vars['action'] == 'get_quotaions') {
            $priceType = $this->form_vars['price_type'];
            $userAccountId = 0;
            if($priceType == "user") {
                $userAccountId = $this->form_vars['user_account_id'];
            } else {
                $userAccountId = $this->user->getUserAccountId();
            }
            $fromCountry = $this->form_vars['from_country']; 
            $toCountry = $this->form_vars['to_country']; 
            $fromPostcode = $this->form_vars['from_postcode'];
            $toPostcode = $this->form_vars['to_postcode'];
            $fromCity = $this->form_vars['from_city'];
            $toCity = $this->form_vars['to_city'];
            $calculate = $this->form_vars['calculate'];
            $pieces = 1;
            $vol_weight = 0.00;
            $weight = 0.00;
            if(count($calculate) > 0) {
                $pieces = count($calculate);
                $length = 0;
                $width = 0;
                $height = 0;
                $conversionRate = $this->form_vars['conversion_rate'];
                foreach($calculate as $value) {
                    if(!empty($value['length'])) {
                        $length = $value['length'];
                    }
                    if(!empty($value['width'])) {
                        $width = $value['width'];
                    }
                    if(!empty($value['height'])) {
                        $height = $value['height'];
                    }
                    $vol_weight += (($length * $width * $height) / $conversionRate);
                    $weightType = $value['weight_type'];
                    if($weightType == "lb") {
                        $weight += $value['weight'] * 0.453592;
                    } else {
                        $weight += $value['weight'];
                    }
                }
            }
            if($vol_weight > $weight) {
                $weight = $vol_weight;
            }
            $output = [];
            $html = "";
            if(!empty($userAccountId) && !empty($fromCountry) && !empty($toCountry) && !empty($weight)) {
                $result = Tariffs::getUserQuotationsByAssignedServices($userAccountId, $fromCountry, $toCountry,$fromPostcode,$toPostcode,$fromCity,$toCity,$weight,$pieces);
                if(isset($result['QUOTATIONS']) && (count($result['QUOTATIONS']) > 0)) {
                    foreach($result['QUOTATIONS'] as $key => $quation) {
                        $serviceFilter = new ServiceFilter();
                        $serviceFilter->addFieldFilter("ser.name", $quation['SERVICE_NAME']);
                        $serviceFilter->addFieldFilter("ser.code", $quation['SERVICE_CODE']);
                        $serviceFilterObj = $serviceFilter->getList();
                        if(count($serviceFilterObj) > 0) {
                            foreach($serviceFilterObj as $serviceObj) {
                                $result['QUOTATIONS'][$key]['SERVICE_ID'] = $serviceObj->getId();
                                $result['QUOTATIONS'][$key]['CARRIER_ID'] = $serviceObj->getCarrierId();
                            }
                        }
                        $serviceId = $result['QUOTATIONS'][$key]['SERVICE_ID'];
                        $carrierId = $result['QUOTATIONS'][$key]['CARRIER_ID'];
                        $carrierObj = new Carrier($carrierId);
                        
                        $currenctId = "";
                        $currencyFilter = new CurrencyFilter();
                        $currencyFilter->addFieldFilter("rightsymbol", $quation['CURRENCY_CODE']);
                        $currencyFilterObjs = $currencyFilter->getList();
                        if(count($currencyFilterObjs) > 0) {
                            $currenctId = $currencyFilterObjs[0]->getId();
                        }
                        
                        $html .= "<tr>";
                        $html .= "<td>" . $carrierObj->getCarrier() . "</td>";
                        $html .= "<td>" . $quation['SERVICE_NAME'] . "</td>";
                        $html .= "<td>" . $quation['BASIC_CHARGE'] . "</td>";
                        $html .= "<td>" . $quation['VAT_CHARGE'] . "</td>";
                        $html .= "<td>" . $quation['TOTAL_EXTRAS'] . "</td>";
                        $html .= "<td>" . $quation['SUBTOTAL'] . "</td>";
                        $html .= "<td>" . $quation['DISCOUNT_AMOUNT'] . "</td>";
                        $html .= "<td>" . $quation['TOTAL'] . "</td>";
                        $dt = $quation['DISCOUNT_TYPE'];
                        $action = "set_quotation_value('".$quation['BASIC_CHARGE']."','".$quation['VAT_CHARGE']."','".$quation['TOTAL_EXTRAS']."','".$quation['SUBTOTAL']."','".$quation['DISCOUNT_AMOUNT']."','".$quation['TOTAL']."','".$dt."','".$currenctId."','".$quation['VOLUMETRIC_DENOMINATOR']."','".$carrierId."','".$serviceId."','".$key."')";
                        $html .= '<td><button type="button" class="btn btn-success btn-sm" id="btn_'.$key.'" onclick="'.$action.'" >Select</button></td>';
                        $html .= "</tr>";
                    }
                }
                if(isset($result['STATUS']) && $result['STATUS'] == "ERROR") {
                    $html .= "<tr>";
                    $html .= "<td colspan='9'>" . $result['MESSAGE'] . "</td>";
                    $html .= "</tr>";
                }
            } else {
                $html .= "<tr>";
                $html .= "<td colspan='9'>No recoud found</td>";
                $html .= "</tr>";
            }
            $output['status'] = "success";
            $output['html'] = $html;
            echo json_encode($output);
            die;
        }
        
        if (isset($this->form_vars['action']) && $this->form_vars['action'] == 'save_quotation') {
            $output = [];
            
            $quotationDetailsObj = new QuotationDetails();
            $shipingFrom = $this->form_vars['from_country'];
            $quotationDetailsObj->setShippingFrom($shipingFrom);
            $shipingTo = $this->form_vars['to_country'];
            $quotationDetailsObj->setShippingTo($shipingTo);
            $carrierId = $this->form_vars['carrier_id'];
            $quotationDetailsObj->setCarrierId($carrierId);
            $serviceId = $this->form_vars['service_id'];
            $quotationDetailsObj->setServiceId($serviceId);
            $priceType = $this->form_vars['price_type'];
            $userAccountId = 0;
            if($priceType == "user") {
                $userAccountId = $this->form_vars['user_account_id'];
            } else {
                $userAccountId = $this->user->getUserAccountId();
            }
            $quotationDetailsObj->setAccountId($userAccountId);
            $priceType = $this->form_vars['price_type'];
            $quotationDetailsObj->setPriceType($priceType);
            $pieces = count($this->form_vars['calculate']);
            $quotationDetailsObj->setPieces($pieces);
            $currencyId = $this->form_vars['currency_id'];
            $quotationDetailsObj->setCurrencyId($currencyId);
            $dimensions = serialize($this->form_vars['calculate']);
            $quotationDetailsObj->setDimensions($dimensions);
            $vol_weight = 0;
            $weight = 0.00;
            $conversionRate = $this->form_vars['conversion_rate'];
            $calculate = $this->form_vars['calculate'];
            if(count($calculate) > 0) {
                $pieces = count($calculate);
                $length = 0;
                $width = 0;
                $height = 0;
                foreach($calculate as $value) {
                    if(!empty($value['length'])) {
                        $length = $value['length'];
                    }
                    if(!empty($value['width'])) {
                        $width = $value['width'];
                    }
                    if(!empty($value['height'])) {
                        $height = $value['height'];
                    }
                    $vol_weight += (($length * $width * $height) / $conversionRate);
                    $weightType = $value['weight_type'];
                    if($weightType == "lb") {
                        $weight = $value['weight'] * 0.453592;
                    } else {
                        $weight = $value['weight'];
                    }
                }
            }
            $quotationDetailsObj->setWeight($weight);
            $quotationDetailsObj->setVolumnWeight($vol_weight);
            $basicCharges = $this->form_vars['basic_charges'];
            $quotationDetailsObj->setBasicCharge($basicCharges);
            $vatCharges = $this->form_vars['vat_charges'];
            $quotationDetailsObj->setVatCharge($vatCharges);
            $extraCharges = $this->form_vars['extra_charges'];
            $quotationDetailsObj->setExtraCharge($extraCharges);
            $subTotalCharges = $this->form_vars['subTotalCharges'];
            $quotationDetailsObj->setSubTotal($subTotalCharges);
            $discount = $this->form_vars['discount'];
            if(empty($discount)) {
                $discount = 0.00;
            }
            $quotationDetailsObj->setDiscount($discount);
            $discountType = $this->form_vars['discount_type'];
            $quotationDetailsObj->setDiscountType($discountType);
            $totalCharges = $this->form_vars['total_charges'];
            $quotationDetailsObj->setTotalCharge($totalCharges);
            $email = $this->form_vars['email'];
            $quotationDetailsObj->setUserEmail($email);
            $description = $this->form_vars['description'];
            $quotationDetailsObj->setRemark($description);
            $fromCity = $this->form_vars['from_city'];
            $quotationDetailsObj->setFromCity($fromCity);
            $toCity = $this->form_vars['to_city'];
            $quotationDetailsObj->setToCity($toCity);
            $FromPostcode = $this->form_vars['from_postcode'];
            $quotationDetailsObj->setFromPostcode($FromPostcode);
            $toPostcode = $this->form_vars['to_postcode'];
            $quotationDetailsObj->setToPostcode($toPostcode);

            $quotationDetailsObj->setConversionrate($conversionRate);
            $status = "active";
            $quotationDetailsObj->setStatus($status);
            
            $date_added = time();
            $added_by = $this->user->getUserAccountId();
            $date_update = time();
            $update_by = $this->user->getUserAccountId();

            $quoteData = $this->form_vars['quote_data'];
            if($quoteData != "") {
                $quotationDetailsObj->setQuotationData($quoteData);
            }

            $quotationDetailsObj->setDateCreated($date_added);
            $quotationDetailsObj->setAddedBy($added_by);
            $quotationDetailsObj->setDateUpdated($date_update);
            $quotationDetailsObj->setUpdatedBy($update_by);
            $quotationDetailsObj->save();
            /* print quotation */
            $quotationId = $quotationDetailsObj->getId();
            $quotatonDetail = new QuotationDetails($quotationId);
            $ufilter = new UserAccountFilter();
            $ufilter->addFieldFilter("      ua.id",$userAccountId);
            $uList = $ufilter->getColumnList("user_account,email,alternative_email, return_address, billing_currency,country,  billing_address");
            $dataArray = [];
            if (count($uList) > 0) {
                $dataArray['email'] = $uList[0]->getEmail();
                $dataArray['account'] = $uList[0]->getAccount();
                $dataArray['billing_address'] = $uList[0]->getBillingAddress();
                $dataArray['return_address'] = $uList[0]->getReturnAddress();
                $dataArray['billing_currency'] = $uList[0]->getBillingCurrency();
                $dataArray['country'] = $uList[0]->getCountry();
            }
            $newPDFFileQuotation = new QuotationTemplate();
            $pdfFileName = $newPDFFileQuotation->addHTML($quotatonDetail, $dataArray);
            /* print quotation */
            if(!empty($pdfFileName)) {
                $quotationDetailsUpdateObj = new QuotationDetails($quotationId);
                $quotationDetailsUpdateObj->setPdf($pdfFileName);
                $quotationDetailsUpdateObj->save();
            }
            $output['status'] = "success";
            $output['message'] = "Save quotation successfully";
            $output['id'] = $quotationId;
            $key = md5($quotationId);
            $output['link'] = BASE_URL."quotation_pay.php?key=".$key;
            echo json_encode($output);
            die;
        }
        
    }

    /**
     * Page-specific buttons
     */
    protected function renderFooter() {
        ?>
        <?php
    }

    protected function addPagelavelCss() {
        ?>
        <link href="../assets/global/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/select2/css/select2-bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/bootstrap-sweetalert/sweetalert.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css" rel="stylesheet" type="text/css" />
        <style>
            .error-class {
                color:red;  z-index:0; position:relative; display:block; text-align: left;
            }
        </style>
        <?php
    }

    public function addPagelavelJs() {
        ?>
        <script type="text/javascript" src="../assets/global/plugins/select2/js/select2.full.min.js" type="text/javascript"></script>
        <script src="../assets/global/scripts/datatable.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/datatables/datatables.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/bootstrap-sweetalert/sweetalert.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/jquery-validation/js/jquery.validate.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/jquery-validation/js/additional-methods.js" type="text/javascript"></script>
        <script type="text/javascript">
            $(document).ready(function () {
                $(".initial-button").hide();
                if ($('.date-picker').length > 0) {
                    //init date pickers
                    $('.date-picker').datepicker({
                        autoclose: true
                    });
                }
                var elindex = 0;
                if (jQuery('#elindex-hardcode').length > 0) {
                    elindex = jQuery('#elindex-hardcode').val();
                    $('#elindex-hardcode').val(1);
                }
                $(document).on('click', '.add-more-pieces-keys', function () {
                    elindex++;
                    $('#elindex-hardcode').val(elindex +1);
                    var clone = $(this).parent().parent().parent().clone();
                    var length = $(clone).find('.length').attr('name');
                    var lengthId = $(clone).find('.length').attr('id');
                    var width = $(clone).find('.width').attr('name');
                    var widthId = $(clone).find('.width').attr('id');
                    var height = $(clone).find('.height').attr('name');
                    var heightId = $(clone).find('.height').attr('id');
                    var weight = $(clone).find('.weight').attr('name');
                    var weightId = $(clone).find('.weight').attr('id');
                    var weightType = $(clone).find('.weight_type .selectpicker').attr('name');
                    var weightTypeId = $(clone).find('.weight_type .selectpicker').attr('id');
                    var volWeight = $(clone).find('.vol_weight').attr('name');
                    var volWeightId = $(clone).find('.vol_weight').attr('id');

                    $(this).remove();
                    $(clone).find('.length').attr('name', length.replace(/\d+/, elindex));
                    $(clone).find('.length').attr('id', lengthId.replace(/\d+/, elindex));
                    $(clone).find('.length').attr('data-field_id', elindex);
                    $(clone).find('.width').attr('name', width.replace(/\d+/, elindex));
                    $(clone).find('.width').attr('id', widthId.replace(/\d+/, elindex));
                    $(clone).find('.height').attr('name', height.replace(/\d+/, elindex));
                    $(clone).find('.height').attr('id', heightId.replace(/\d+/, elindex));
                    $(clone).find('.weight').attr('name', weight.replace(/\d+/, elindex));
                    $(clone).find('.weight').attr('id', weightId.replace(/\d+/, elindex));
                    $(clone).find('.weight_type .selectpicker').attr('name', weightType.replace(/\d+/, elindex));
                    $(clone).find('.weight_type .selectpicker').attr('id', weightTypeId.replace(/\d+/, elindex));
                    $(clone).find('.vol_weight').attr('name', volWeight.replace(/\d+/, elindex));
                    $(clone).find('.vol_weight').attr('id', volWeightId.replace(/\d+/, elindex));

                    $(clone).find('.bootstrap-select.weight_type').replaceWith(function () {
                        return $('#weight_type_' + elindex, this);
                    });
                    $(clone).find('#weight_type_' + elindex).selectpicker('refresh');

                    $(clone).find('button.remove-pieces-key').show();
                    $(clone).find('button.remove-pieces-key').removeClass('initial-button');;
                    $(clone).appendTo($('.pieces-container'));
                });
                $(document).on('click', '.remove-pieces-key', function () {
                    var el = $(this);
                    swal({
                        title: "<?php echo Translation::GetCaption("ARE_YOU_SURE_YOU_WANT_TO_DELETE_THIS_RECORD") ?>",
                        text: "",
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonClass: "btn-danger",
                        confirmButtonText: "Yes",
                        cancelButtonText: "No",
                        closeOnConfirm: true,
                        closeOnCancel: true
                    },
                            function (isConfirm) {
                                if (isConfirm) {
                                    if ($('.pieces-container .remove-pieces-key').length > 1) {
                                        $(el).parent().parent().remove();
                                        $('#elindex-hardcode').val(elindex);                                        
                                        if ($('.add-more-pieces-keys').length == 0) {
                                            var addMore = $(el).parent().find('.add-more-pieces-keys').clone();
                                            $('.pieces-container .remove-pieces-key').last().parent().prepend(addMore);
                                        }
                                    } else {
                                        $(el).parent().parent().find('input').val('');
                                    }
                                }
                            });
                });
                /* form validation */
                $('#quotation_form').validate ({
                    // validation rules for registration form
                    errorClass: "error-class",
                    validClass: "valid-class",
                    errorElement: 'div',
                    errorPlacement: function(error, element) {
                        if(element.parent('.input-group').length) {
                            error.insertAfter(element.parent());
                        } else {
                            error.insertAfter(element);
                        }
                    },
                    onError : function(){
                        $('.input-group.error-class').find('.help-block.form-error').each(function() {
                            $(this).closest('.form-group').addClass('error-class').append($(this));
                        });
                    },

                    rules: {
                        from_country: {
                            required: true
                        },
                        from_postcode: {
                            required: true
                        },
                        from_city: {
                            required: true
                        },
                        to_country: {
                            required: true
                        },
                        to_postcode: {
                            required: true
                        },
                        to_city: {
                            required: true
                        }
                    },

                    messages: {
                        from_country: {
                            required: "This field is required."
                        },
                        from_postcode: {
                            required: "This field is required."
                        },
                        from_city: {
                            required: "This field is required."
                        },
                        to_country: {
                            required: "This field is required."
                        },
                        to_postcode: {
                            required: "This field is required."
                        },
                        to_city: {
                            required: "This field is required."
                        },

                        highlight: function(element, errorClass) {
                            $(element).removeClass(errorClass);
                        }
                    },

                });
            });
            function get_quotaions() {
                var priceType = $('#price_type').val();
                if(priceType != "manual") {
                    $('.manual_price_fields').attr('readonly','readonly');
                    var form_data = $("#quotation_form").serializeArray();
                    form_data.push({name: 'action', value: "get_quotaions"});
                    $.ajax({
                        url: 'quotation_add.php',
                        dataType: 'json',
                        data: form_data,
                        type: 'post',
                        success: function (response) {
                            $('#charges_list').html(response.html);
                        }
                    });
                } else {
                    $('.manual_price_fields').removeAttr('readonly');
                }
                set_quotation_value('','','','','','','','','','','','');
            }
            function calculateVolWeight() {
                $('.pieces_data').map(function(idx, elem) {
                    var keyNumber = $(elem).data('field_id');
                    var length = $('#length_' + keyNumber).val();
                    var width = $('#width_' + keyNumber).val();
                    var height = $('#height_' + keyNumber).val();
                    var conversion = $('#conversion_rate').val();
                    var volumnWeight = (length * width * height) / conversion;
                    $('#vol_weight_' + keyNumber).val(volumnWeight.toFixed(2));
                }).get();
            }
            function set_quotation_value(basicCharges,vatCharges,extraCharges,subTotalCharges,discount,totalCharges,discountType,currency,conversionRate,carrier_id,service_id,key) {
                $('tr').css('background-color','#fff');
                if(key != '') {
                    $('#btn_'+key).parent().parent().css('background-color','#3cd232');
                }
                $('#carrier_id').val(carrier_id);
                $('#service_id').val(service_id);
                $('#basic_charges').val(parseFloat(basicCharges).toFixed(2));
                $('#vat_charges').val(parseFloat(vatCharges).toFixed(2));
                $('#extra_charges').val(parseFloat(extraCharges).toFixed(2));
                $('#total_charges').val(parseFloat(totalCharges).toFixed(2));
                if(subTotalCharges != "") {
                    $('#subTotalCharges').val(parseFloat(subTotalCharges).toFixed(2));
                }
                if(discount != "") {
                    $('#discount').val(parseFloat(discount).toFixed(2));
                }
                if(discountType != "") {
                    $('#discount_type').val(discountType);
                    $('#discount_type').select2();
                }
                if(currency != "") {
                    $('#currency_id').val(currency);
                    $('#currency_id').select2();
                } else {
                    $('#currency_id').val('');
                    $('#currency_id').select2();
                }
                if(conversionRate != "") {
                    $('#conversion_rate').val(conversionRate);
                    $('#conversion_rate').select2();
                }
            }
            function calculate_change_quotation() {
                var basic_charges = $('#basic_charges').val();
                if(basic_charges == "") {
                    basic_charges = 0;
                }
                var discount = $('#discount').val();
                if(discount == "") {
                    discount = 0;
                }
                var vat_charges = $('#vat_charges').val();
                if(vat_charges == "") {
                    vat_charges = 0;
                }
                var extra_charges = $('#extra_charges').val();
                if(extra_charges == "") {
                    extra_charges = 0;
                }
                
                var subTotal = (parseFloat(basic_charges) + parseFloat(vat_charges) + parseFloat(extra_charges));
                $('#subTotalCharges').val(parseFloat(subTotal));
                
                var discount_type = $('#discount_type').val();
                if(discount_type == "percentage") {
                    var discount_percentage = ((parseFloat(discount) / 100) * parseFloat(basic_charges));
                    if(discount_percentage != "") {
                        basic_charges = (parseFloat(basic_charges) - parseFloat(discount_percentage));
                    }
                } else {
                    basic_charges = (parseFloat(basic_charges) - parseFloat(discount));
                }
                
                var total_charges = (parseFloat(basic_charges) + parseFloat(vat_charges) + parseFloat(extra_charges)); 
                $('#total_charges').val(parseFloat(total_charges));
            }
            function saveQuotation(sendemail=false) {
                var checkValid = $('#quotation_form').valid();
                if(checkValid) {
                    var form_data = $("#quotation_form").serializeArray();
                    if (sendemail) {
                        form_data.push({name: "send_email", value: '1'});
                    } else {
                        form_data.push({name: "send_email", value: '0'});
                    }
                    form_data.push({name: "action", value: 'save_quotation'});
                    $.ajax({
                        url: 'quotation_add.php',
                        data: form_data,
                        type: 'post',
                        dataType: "json",
                        success: function (response) {
                            if (response.status == "success") {
                                swal({
                                    type: 'success',
                                    html: true,
                                    title: "Success!",
                                    text: response.message
                                }, function (isConfirm) {
                                    if (response.id > 0 && sendemail) {
                                        $.ajax({
                                            type: "POST",
                                            url: "quotation_list.php",
                                            data: {'action': "send_email", 'id': response.id},
                                            dataType: "json",
                                            success: function (res) {
                                                if (isConfirm) {
                                                    window.location.href = "quotation_list.php?id=" + response.id;
                                                }
                                            },
                                            error: function () {
                                                //alert('error handing here');
                                            }
                                        });
                                    } else {
                                        window.location.href = "quotation_list.php?id=" + response.id;
                                    }
                                });
                            } else {
                                swal("Sorry!", "Some thing went wrong please contact to support", "error");
                            }
                        }
                    });
                }
            }
        </script>
        <?php
    }

    /*     * *
     * Content View
     */

    protected function renderBody() {
        ?>
         <div class="portlet light">
            <div class="portlet-title">
                <div class="caption"> <i class="fa fa-list"></i>
                    Quotation Form
                </div>
                <div class="actions">
                </div>
            </div>
            <div class="portlet-body">
                <form action="quotation_add.php" id="quotation_form"  >
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group has-float-label">
                                        <div class="input-group"> <span class="input-group-addon"> <i class="fa  fa-key"></i> </span>
                                            <select class="select2" name="price_type" id="price_type" onchange="get_quotaions()">
                                                <option value="agent"> Supplier Price</option>
                                                <option value="user"> Customer Price </option>
                                                <option value="manual"> Manual Price</option>
                                            </select>
                                            <label class="label-account">Price Type</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group has-float-label">
                                        <?php echo Ddl::generateCountryDDL('from_country', '', 'id', 'class="form-filter bs-select form-control" required="" data-live-search="true" data-container="body" data-size="8" onchange="get_quotaions()"'); ?>
                                        <label class="label-account">From Country</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group has-float-label">
                                        <div class="input-group"> <span class="input-group-addon"> <i class="fa  fa-key"></i> </span>
                                            <input class="form-control form-filter" name="from_postcode" placeholder="From Postcode" id="from_postcode" type="text" value="" rel="tooltip" data-original-title="From Postcode" data-placement="top" onchange="get_quotaions()" required="required" >
                                        </div>
                                        <label class="label-account">From Postcode</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group has-float-label">
                                        <div class="input-group"> <span class="input-group-addon"> <i class="fa  fa-key"></i> </span>
                                            <input class="form-control form-filter" name="from_city" placeholder="From City" id="from_city" type="text" value="" rel="tooltip" data-original-title="From City" data-placement="top" onchange="get_quotaions()" required="required" >
                                        </div>
                                        <label class="label-account">From City</label>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group has-float-label">
                                        <div id="user_content">
                                            <?php
                                            $accountParentId = 0;
                                            $includeParent = true;
                                            if ($this->user->getUserType() == User::USER_TYPE_CORPORATE) {
                                                $accountParentId = $this->user->getUserAccountId();
                                                $includeParent = false;
                                            }
                                            $selectedAccount = "";
                                            $allowedLevel = 0;
                                            if (Permissions::checkFilePermission('hide_subaccount')) {
                                                $allowedLevel = 1;
                                            }
                                            ?>
                                            <?php echo Ddl::showTreeDropdown('user_account_id', 'user_account', 'user_account', 'id', $accountParentId, array("active_flag = '1'"), $selectedAccount, "Please Select Account", 'class="form-filter bs-select form-control" onchange="get_quotaions()" data-live-search="true"', "", "", 'logo', '../images/userlogo/thumbnail/', 'owe_16_', $includeParent,$allowedLevel); ?>
                                        </div>
                                        <label class="label-account">Select Account</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group has-float-label">
                                        <?php echo Ddl::generateCountryDDL('to_country', '', 'id', 'class="form-filter bs-select form-control" required="" data-live-search="true" data-container="body" data-size="8" onchange="get_quotaions()"'); ?>
                                        <label class="label-account">To Country</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group has-float-label">
                                        <div class="input-group"> <span class="input-group-addon"> <i class="fa  fa-key"></i> </span>
                                            <input class="form-control form-filter" name="to_postcode" placeholder="To Postcode" id="to_postcode" type="text" value="" rel="tooltip" data-original-title="To Postcode" data-placement="top" onchange="get_quotaions()" required="required" >
                                        </div>
                                        <label class="label-account">To Postcode</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group has-float-label">
                                        <div class="input-group"> <span class="input-group-addon"> <i class="fa  fa-key"></i> </span>
                                            <input class="form-control form-filter" name="to_city" placeholder="To City" id="to_city" type="text" value="" rel="tooltip" data-original-title="To City" data-placement="top" onchange="get_quotaions()" required="required" >
                                        </div>
                                        <label class="label-account">To City</label>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="caption margin-bottom-10 block">
                                        <span class="caption-subject bold uppercase">Pieces</span>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="row">
                                        <div class="col-md-2">
                                            <label>Weight</label>
                                        </div>
                                        <div class="col-md-2">
                                            <label>Length</label>
                                        </div>
                                        <div class="col-md-2">
                                            <label>Width</label>
                                        </div>
                                        <div class="col-md-2">
                                            <label>Height</label>                                  
                                        </div>
                                        <div class="col-md-2">
                                            <label>Vol Weight</label>
                                        </div>
                                        <div class="col-md-2">
                                        </div>
                                    </div>
                                    <div class="pieces-container" id="pieces-container">
                                        <div class="form-group">
                                            <div class="row">
                                                <div class="col-md-2">
                                                    <div class="input-group">
                                                        <input class="form-control form-filter weight" name="calculate[0][weight]" placeholder="Weight" id="weight_0" type="number" value="" rel="tooltip" data-original-title="Weight" data-placement="top" onkeyup="get_quotaions()" >
                                                        <div class="input-group-btn">
                                                            <select class="selectpicker form-control weight_type" name="calculate[0][weight_type]" id="weight_type_0" onchange="get_quotaions()" >
                                                                <option value="kg">KGS</option>
                                                                <option value="lb">LBS</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="input-group"> <span class="input-group-addon"> <i class="fa  fa-key"></i> </span>
                                                        <input class="form-control form-filter length pieces_data" data-field_id="0" name="calculate[0][length]" placeholder="Length" id="length_0" type="number" value="" rel="tooltip" data-original-title="Length" data-placement="top" onkeyup="calculateVolWeight()" >
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="input-group"> <span class="input-group-addon"> <i class="fa  fa-key"></i> </span>
                                                        <input class="form-control form-filter width" name="calculate[0][width]" placeholder="Width" id="width_0" type="number" value="" rel="tooltip" data-original-title="Width" data-placement="top" onkeyup="calculateVolWeight()" >
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="input-group"> <span class="input-group-addon"> <i class="fa  fa-key"></i> </span>
                                                        <input class="form-control form-filter height" name="calculate[0][height]" placeholder="Height" id="height_0" type="number" value="" rel="tooltip" data-original-title="Height" data-placement="top" onkeyup="calculateVolWeight()" >
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="input-group"> <span class="input-group-addon"> <i class="fa  fa-key"></i> </span>
                                                        <input class="form-control form-filter vol_weight" name="calculate[0][vol_weight]" placeholder="Vol Weight" id="vol_weight_0" type="number" value="" rel="tooltip" data-original-title="Vol Weight" data-placement="top" readonly="readonly" >
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <button type="button" class="btn btn-success add-more-pieces-keys"><i class="fa fa-plus"></i></button>
                                                    <button type="button" class="btn btn-danger remove-pieces-key initial-button"><i class="fa fa-minus"></i></button>
                                                </div>
                                            </div>
                                        </div>
                                        <input type="hidden" name="elindex-hardcode" id="elindex-hardcode" value="0" />
                                    </div>
                                </div>    
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="caption margin-bottom-10 block">
                                        Result Tariff
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="table-container">
                                       <table class="table table-striped table-bordered table-hover table-condensed" id="result_charges_table">
                                           <thead>
                                               <tr>
                                                   <th>Carriers</th>
                                                   <th>Services</th>
                                                   <th>Basic Charges</th>
                                                   <th>Vat Charges</th>
                                                   <th>Extra Charges</th>
                                                   <th>Sub Total</th>
                                                   <th>Discount</th>
                                                   <th>Total</th>
                                                   <th>Action</th>
                                               </tr>
                                           </thead>
                                           <tbody id="charges_list">
                                                <tr>
                                                    <td colspan='9'>No recoud found</td>
                                                </tr>
                                           </tbody>
                                       </table>
                                   </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group has-float-label">
                                        <div class="input-group">
                                            <span class="input-group-addon"> <i class="fa fa-shopping-cart"></i></span>
                                            <?php
                                            echo Ddl::generateDDL('currency_id', 'CurrencyFilter', "AND isactive='1' AND ClientDisplay='1'", 'currencyname', 'id', '', ' class="input-sm form-control select2 validate_check manual_price_fields" readonly="readonly" ', 'Select Currency', '');
                                            ?>
                                        </div>
                                        <label>Currency</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group has-float-label">
                                        <select class="select2" name="conversion_rate" id="conversion_rate" onchange="calculateVolWeight()" >
                                            <option value="2000"> 2000</option>
                                            <option value="3000"> 3000 </option>
                                            <option value="4000"> 4000</option>
                                            <option value="5000"> 5000</option>
                                        </select>
                                        <label class="label-account">Conversion Rate</label>
                                    </div>
                                </div>
                                <div id="hidden_inputs">
                                    <input type="hidden" name="subTotalCharges" id="subTotalCharges" value="" >
                                    <input type="hidden" name="carrier_id" id="carrier_id" value="" >
                                    <input type="hidden" name="service_id" id="service_id" value="" >
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group has-float-label">
                                        <div class="input-group"> <span class="input-group-addon"> <i class="fa  fa-key"></i> </span>
                                            <input class="form-control form-filter manual_price_fields" name="discount" placeholder="Discount" id="discount" type="number" value="" rel="tooltip" data-original-title="Discount" data-placement="top" readonly="readonly" onkeyup="calculate_change_quotation()" >
                                        </div>
                                        <label class="label-account">Discount</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group has-float-label">
                                        <div class="input-group"> <span class="input-group-addon"> <i class="fa  fa-key"></i> </span>
                                            <select readonly="readonly" class="select2 manual_price_fields" id="discount_type" name="discount_type" onchange="calculate_change_quotation()" >
                                                <option value="fixed">Fixed</option>
                                                <option value="percentage">Percentage</option>
                                            </select>
                                        </div>
                                        <label class="label-account">Discount Type</label>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group has-float-label">
                                        <div class="input-group"> <span class="input-group-addon"> <i class="fa  fa-key"></i> </span>
                                            <input class="form-control form-filter manual_price_fields" name="basic_charges" placeholder="Basic Charges" id="basic_charges" type="number" value="" rel="tooltip" data-original-title="Basic Charges" data-placement="top" readonly="readonly" onkeyup="calculate_change_quotation()" >
                                        </div>
                                        <label class="label-account">Basic Charges</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group has-float-label">
                                        <div class="input-group"> <span class="input-group-addon"> <i class="fa  fa-key"></i> </span>
                                            <input class="form-control form-filter manual_price_fields" name="vat_charges" placeholder="Vat Charges" id="vat_charges" type="number" value="" rel="tooltip" data-original-title="Vat Charges" data-placement="top" readonly="readonly" onkeyup="calculate_change_quotation()" >
                                        </div>
                                        <label class="label-account">Vat Charges</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group has-float-label">
                                        <div class="input-group"> <span class="input-group-addon"> <i class="fa  fa-key"></i> </span>
                                            <input class="form-control form-filter manual_price_fields" name="extra_charges" placeholder="Extra Charges" id="extra_charges" type="number" value="" rel="tooltip" data-original-title="Extra Charges" data-placement="top" readonly="readonly" onkeyup="calculate_change_quotation()" >
                                        </div>
                                        <label class="label-account">Extra Charges</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group has-float-label">
                                        <div class="input-group"> <span class="input-group-addon"> <i class="fa  fa-key"></i> </span>
                                            <input class="form-control form-filter" name="total_charges" placeholder="Total Charges" id="total_charges" type="number" value="" rel="tooltip" data-original-title="Total Charges" data-placement="top" readonly="readonly" >
                                        </div>
                                        <label class="label-account">Total Charges</label>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group has-float-label">
                                        <div class="input-group"> <span class="input-group-addon"> <i class="fa  fa-key"></i> </span>
                                            <textarea class="form-control" style="min-height: 110px;" name="description" id="description" ></textarea>
                                        </div>
                                        <label class="label-account">Description</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group has-float-label">
                                        <div class="input-group"> <span class="input-group-addon"> <i class="fa  fa-key"></i> </span>
                                            <input class="form-control form-filter" name="email" placeholder="Email" id="email" type="email" value="" rel="tooltip" data-original-title="Email" data-placement="top">
                                        </div>
                                        <label class="label-account">Email</label>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <button type="button" id="save_quotation" class="btn btn-primary" onclick="saveQuotation(true)" >Save Quotation & Send Email</button>
                                    <button type="button" id="save_quotation" class="btn btn-primary" onclick="saveQuotation()" >Save Quotation</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <?php
    }

    /**
     * Override to show the menu
     *
     */
    public function renderMenu() {
        $menu = new Adminmenu(Adminmenu::CUSTOMERS);
        $menu->render();
    }
    
    public function renderHead() {
        ?>
        <style type="text/css">
            #result_charges_table thead tr th {
                font-weight: bold;
            }
            #result_charges_table tbody tr td {
                vertical-align:middle;
            }
            select[readonly].select2 + .select2-container {
                pointer-events: none;
                touch-action: none;

                .select2-selection {
                    background: #eef1f5;
                    box-shadow: none;
                }

                .select2-selection__arrow,
                .select2-selection__clear {
                  display: none;
                }
             }
        </style>
        <?php
    }

}

// class

/* ------------------------------------------------------------------------------ */
// create and render page
$page = new Page(CONFIG_TEMPLATE_ADMIN);
$page->show();
?>
