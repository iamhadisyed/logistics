<?php
// get settings
require_once("../includes/settings/config.inc.php");

include_classes([   
                    'ivisualcomponent','ddl.inc'
                ],'library');
include_classes([
    'tcpdf'
    ], '3rdparty/tcpdf');
include_classes([   
                    'iaddress.class',
                    'consignment.class',
                    'consignmentfilter.class',
                    'consignmentcharges.class',
                    'consignmentchargesfilter.class',
                    'currency.class',
                    'currencyfilter.class',
                    'invoices.class',
                    'invoicesfilter.class',
                    'invoicesmanualdetails.class',
                    'invoicebankdetails.class',
                    'invoicebankdetailsfilter.class',
                    'invoicesmanualdetailsfilter.class']);
class Page extends BasePage {
    /*     * *
     * Controller logic
     */

    private $user = "";
    private $invoiceId = 0;
    private $invoiceObj;
    private $invoiceDetailObj = array();

    protected function init() {
        $this->breadCrumb['data'] = array(
            'index.php' => Translation::GetCaption("HOME"),
            "Manual Invoices Details"
        );
        $this->user = SessionManager::getUser();
        if ((isset($_GET['id'])) && ($_GET['id'] > 0)) {
            $this->invoiceId = $_GET['id'];
            $this->invoiceObj = new Invoices($_GET['id']);
            $invoicesManualDetailsFilter = new InvoicesManualDetailsFilter();
            $invoicesManualDetailsFilter->where(['imd.invoice_id' => $this->invoiceId]);
            $this->invoiceDetailObj = $invoicesManualDetailsFilter->getList();
        } else {
            $this->invoiceObj = new Invoices();
        }
        /*
         * DataTable handlings
         */

        if (isset($this->form_vars['action']) && $this->form_vars['action'] == 'save_manual_invoice') {
            $output = array();
            $customerAccount = $this->form_vars['customer_account'];
            $netAmount = $this->form_vars['net_amount'];
            $vat = $this->form_vars['total_vat'];
            $totalAmount = $this->form_vars['grand_total_amount'];
            $invoice_date = strtotime($this->form_vars['invoice_date']);
            $invoice_heading = $this->form_vars['invoice_heading'];

            $added_by = $this->user->getId();
            $date_added = time();
            $updated_by = $this->user->getId();
            $date_update = time();

            $userAccountObj = new CustomerAccount($customerAccount);
            $currencyFilter = new CurrencyFilter();
            $currencyFilter->addFieldFilter("rightsymbol", $userAccountObj->getBillingCurrency());
            $currencyObj = $currencyFilter->getList();
            /* Default currency is GBP */
            $billingCurrencyId = 2;
            if (count($currencyObj) > 0) {
                $billingCurrencyId = $currencyObj[0]->getId();
            }
            $generateManualInvoice = true;
            $invoicedHawb = [];
            if ($totalAmount > 0) {
                if ($this->form_vars['invoiceId'] > 0) {
                    $invoice = new Invoices($this->form_vars['invoiceId']);
                } else {
                    $hawbs = $this->form_vars['hawb'];
                    $res = $this->checkAlreadyInvoice($hawbs, $customerAccount);
                    if (isset($res['status']) && $res['status'] == "error") {
                        $generateManualInvoice = false;
                        if (isset($res['hawb'])) {
                            $invoicedHawb = $res['hawb'];
                        }
                    }
                    $result = Invoices::generateInvoiceNumber($this->user->getUseraccountId(), 'MNI');
                    $invoice_number = $result['invoice_number'];
                    $invoice = new Invoices();
                    $invoice->setInvoiceNo($invoice_number);
                }
                if ($generateManualInvoice) {
                    $invoice->setInvoiceType("MNI");
                    $invoice->setUserAccountId($customerAccount);
                    $invoice->setNetAmount($netAmount);
                    $invoice->setVat($vat);
                    $invoice->setTotalAmount($totalAmount);
                    $invoice->setCurrencyId($billingCurrencyId);
                    if(!empty($invoice_date))
                        $invoice->setInvoiceDate($invoice_date);
                    else
                        $invoice->setInvoiceDate(time());
                    $invoice->setInvoiceHeading($invoice_heading);
                    $invoice->setInvoiceBy($this->user->getUserAccountId());
                    $invoice->setAddedBy($added_by);
                    $invoice->setDateCreated($date_added);
                    $invoice->setDateUpdated($date_update);
                    $invoice->save();

                   $last_invoice_id = $invoice->getId();
                   
                   
                    if ($last_invoice_id > 0) {
                        $amounts = $this->form_vars['total_amount'];
                       
                        InvoicesManualDetails::deleteManualInvoiceDetailsDetailsByInvoiceId($last_invoice_id);
                        if (count($amounts) > 0) {
                            foreach ($amounts as $key => $value) {
                                $hawb = $this->form_vars['hawb'][$key];
                                if (!empty($this->form_vars['date_booked'][$key])) {
                                    $date_booked = strtotime($this->form_vars['date_booked'][$key]);
                                } else {
                                    $date_booked = '';
                                }
                                $reference = $this->form_vars['reference'][$key];
                                $weight = $this->form_vars['weight'][$key];
                                $description = $this->form_vars['description'][$key];
                                $destination = $this->form_vars['destination'][$key];
                                $vat_amount = $this->form_vars['vat_amount'][$key];
                                $service_id = $this->form_vars['service_id'][$key];
                                if (isset($this->form_vars['is_vat'][$key]) && $this->form_vars['is_vat'][$key] > 0) {
                                    $is_vat = "YES";
                                } else {
                                    $is_vat = "NO";
                                }
                                $invoicesManualDetails = new InvoicesManualDetails();
                                $invoicesManualDetails->setInvoiceId($last_invoice_id);
                                $invoicesManualDetails->setHawb($hawb);
                                $invoicesManualDetails->setServiceId($service_id);
                                $invoicesManualDetails->setDateBooked($date_booked);
                                $invoicesManualDetails->setReference($reference);
                                $invoicesManualDetails->setWeight($weight);
                                $invoicesManualDetails->setAmount((float)$value);
                                $invoicesManualDetails->setDescription($description);
                                $invoicesManualDetails->setDestination($destination);
                                $invoicesManualDetails->setVatAmount((float)$vat_amount);
                                $invoicesManualDetails->setIsVat($is_vat);
                                $invoicesManualDetails->setCreatedBy($added_by);
                                $invoicesManualDetails->setDateCreated($date_added);
                                $invoicesManualDetails->setUpdatedBy($updated_by);
                                $invoicesManualDetails->setDateUpdated($date_update);
                                $invoicesManualDetails->save();
                                 
                                $consignmentId = '';
                                $checkInvoiced = 0;
                                $consignmentFilter = new ConsignmentFilter();
                                $consignmentFilter->addFieldFilter('     c.hawb', $hawb);
                                $consignmentFilterObj = $consignmentFilter->getListNew('     c.id');
                                if (count($consignmentFilterObj) > 0) {
                                    $consignmentId = $consignmentFilterObj[0]->getId();
                                    $checkInvoiced = Consignment::checkConsignmentInvoiced($consignmentId);
                                }
                                if ($checkInvoiced == 0) {
                                    if (isset($res['auto_invoiced'][$hawb]) && $res['auto_invoiced'][$hawb] == 0) {
                                        ConsignmentCharges::updateInvoiceId($customerAccount, $consignmentId, $last_invoice_id);
                                    }
                                }
                            }
                            $output['status'] = "success";
                            $output['message'] = "Manual invoice has been generated successfully";
                            $output['invoice_id'] = $last_invoice_id;
                        } else {
                            $output['status'] = "error";
                            $output['message'] = "Invoice cannot generated, Please contact to itsupport@oneworldexpress.com";
                            $output['invoice_id'] = 0;
                            $output['invoiced_hawb'] = [];
                        }
                    } else {
                        $output['status'] = "error";
                        $output['message'] = "Invoice cannot generated, Please contact to itsupport@oneworldexpress.com";
                        $output['invoice_id'] = 0;
                        $output['invoiced_hawb'] = [];
                    }
                } else {
                    $output['status'] = "error";
                    $output['message'] = "Some Hawb is already invoiced";
                    $output['invoice_id'] = 0;
                    $output['invoiced_hawb'] = $invoicedHawb;
                }
            } else {
                $output['status'] = "error";
                $output['message'] = "Your invoice amount in invalid";
            }
            echo json_encode($output);
            exit;
        }

        if (isset($this->form_vars['action']) && trim($this->form_vars['action']) == 'manual_invoice_pdf') {
            $output = array();
            $invoice_id = $this->form_vars['invoice_id'];
            $pdf = new InvoicePDF();
            $data = $pdf->SaveManualPDFInvoiceFile($invoice_id);
            $invoiceObj = new Invoices($invoice_id);
            $invoiceObj->setPdf($data['FILENAME']);
            $invoiceObj->save();
            $output['path'][] = $data;
            $output['status'] = "success";
            $output['message'] = "Manual invoice has been generated successfully";
            echo json_encode($output);
            die;
        }

        if (isset($this->form_vars['action']) && $this->form_vars['action'] == 'get_user_details') {
            $output = array();
            $customer_account_id = $this->form_vars['customer_account_id'];
            $userAccountObj = new CustomerAccount($customer_account_id);
            if ($userAccountObj->getVatChargable() > 0) {
                $output['vat_value'] = 20;
            } else {
                $output['vat_value'] = 0.00;
            }
            echo json_encode($output);
            die;
        }

        if (isset($this->form_vars['action']) && $this->form_vars['action'] == 'get_hawb_details') {
            $output = array();
            $output['status'] = "not_found";
            $hawb = $this->form_vars['hawb'];
            $account = $this->form_vars['account'];
            $consignmentFilter = new ConsignmentFilter();
            $consignmentFilter->addFieldFilter("c.hawb", $hawb);
            if (!empty($account)) {
                $consignmentFilter->addFieldFilter("u.user_account_id", $account);
            }
            $consignmentFilterObj = $consignmentFilter->getList();
            if (count($consignmentFilterObj) > 0) {
                $output['status'] = "found";
                foreach ($consignmentFilterObj as $consignmentObj) {
                    if ($consignmentObj->getCountryId() > 0) {
                        $countryObj = new Country($consignmentObj->getCountryId());
                        $country = $countryObj->getName();
                    } else {
                        $country = "";
                    }
                    if ($consignmentObj->getDateBooked() > 0) {
                        $date_booked = date('Y-m-d', $consignmentObj->getDateBooked());
                    } else {
                        $date_booked = "";
                    }
                    $output['date_booked'] = $date_booked;
                    $output['reference'] = $consignmentObj->getReference();
                    $output['destination'] = $consignmentObj->getCity() . " " . $country;
                    $output['service_id'] = $consignmentObj->getServiceId();
                }
            }
            echo json_encode($output);
            die;
        }
    }

    public function checkAlreadyInvoice($hawbs,$userAccountId) {
        $output = [];
        foreach($hawbs as $hawb) {
            $consignmentFilter = new ConsignmentFilter();
            $consignmentFilter->addFieldFilter('     c.hawb', $hawb);
            $consignmentFilterObj = $consignmentFilter->getListNew('     c.id');
            if(count($consignmentFilterObj) > 0) {
                $consignmentId = $consignmentFilterObj[0]->getId();
                $data = Consignment::checkConsignmentInvoiced($consignmentId,true);
                if(isset($data['invoiced']) && $data['invoiced'] == 1) {
                    if($data['invoice_type'] == "MNI") {
                        $output['hawb'][] = $hawb;
                        $output['auto_invoiced'][$hawb] = $data['invoiced'];
                    } else {
                        $output['auto_invoiced'][$hawb] = $data['invoiced'];
                        $invoiceFilter = new InvoiceFilter();
                        $invoiceFilter->join("invoices_manual_details imd", "imd.invoice_id = inv.id");
                        $invoiceFilter->where(["imd.hawb" => $hawb,"inv.user_account_id" => $userAccountId]);
                        $invoiceFilterObjs = $invoiceFilter->getList("inv.invoice_type");
                        if(count($invoiceFilterObjs) > 0) {
                            foreach($invoiceFilterObjs as $invoiceFilterObj) {
                                if($invoiceFilterObj->getInvoiceType() == "MNI") {
                                    $output['hawb'][] = $hawb;
                                    break;
                                }
                            }
                        }
                    }
                } else {
                    $output['auto_invoiced'][$hawb] = 0;
                }
            }
            
        }
        if(isset($output['hawb'])) {
            $output['status'] = "error";
            $output['message'] = "Following consignment is already invoiced";
        } else {
            $output['status'] = "success";
            $output['message'] = "No consignment already invoiced";
        }
        return $output;
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
        <link rel="stylesheet" type="text/css" href="../assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/select2/css/select2-bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/bootstrap-sweetalert/sweetalert.css" rel="stylesheet" type="text/css" />

        <link href="../assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/bootstrap-select/css/bootstrap-select.css" rel="stylesheet" type="text/css" />        
        <link href="../assets/global/plugins/icheck/skins/all.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css" rel="stylesheet" type="text/css" />
        <link rel="stylesheet" href="../assets/pages/css/flipclock.css">                            	
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

        <script src="../assets/pages/scripts/components-date-time-pickers.min.js" type="text/javascript"></script>
        <script src="../assets/pages/scripts/ui-confirmations.min.js" type="text/javascript"></script>
        <script type="text/javascript">
            var urlPage = 'manual_invoices_details.php?id=<?php echo (int)$this->invoiceId ?>';
            $(document).ajaxStart($.blockUI).ajaxStop($.unblockUI);
            var elindex = 0;
            $(document).ready(function () { 
                $(".initial-button").hide();
                if ($('.date-picker').length > 0) {
                    //init date pickers
                    $('.date-picker').datepicker({
                        autoclose: true,
                        formate: 'dd-mm-yyyy'
                    });
                }
                if (jQuery('#elindex-hardcode').length > 0) {
                    elindex = jQuery('#elindex-hardcode').val();
                }
                $(document).on('click', '.add_more_manual_invoice_details', function () {
                    elindex++;
                    var clone = $(this).parent().parent().parent().parent().clone();
                    var hawb = $(clone).find('.hawb').attr('name');
                    var hawbId = $(clone).find('.hawb').attr('id');
                    var service = $(clone).find('.service_id').attr('name');
                    var serviceId = $(clone).find('.service_id').attr('id');
                    var dateBooked = $(clone).find('.date_booked').attr('name');
                    var dateBookedId = $(clone).find('.date_booked').attr('id');
                    var reference = $(clone).find('.reference').attr('name');
                    var referenceId = $(clone).find('.reference').attr('id');
                    var weight = $(clone).find('.weight').attr('name');
                    var weightId = $(clone).find('.weight').attr('id');
                    var totalAmount = $(clone).find('.total_amount').attr('name');
                    var totalAmountId = $(clone).find('.total_amount').attr('id');
                    var description = $(clone).find('.description').attr('name');
                    var descriptionId = $(clone).find('.description').attr('id');
                    var destination = $(clone).find('.destination').attr('name');
                    var destinationId = $(clone).find('.destination').attr('id');
                    var isVat = $(clone).find('.is_vat').attr('name');
                    var isVatId = $(clone).find('.is_vat').attr('id');
                    var vatAmount = $(clone).find('.vat_amount').attr('name');
                    var vatAmountId = $(clone).find('.vat_amount').attr('id');

                    $(this).remove();

                    $(clone).find('input').val('');
                    $(clone).find('.hawb').attr('name', hawb.replace(/\d+/, elindex));
                    $(clone).find('.hawb').attr('id', hawbId.replace(/\d+/, elindex));
                    $(clone).find('.hawb').attr('data-number_of_record', elindex);
                    $(clone).find('.service_id').attr('name', service.replace(/\d+/, elindex));
                    $(clone).find('.service_id').attr('id', serviceId.replace(/\d+/, elindex));
                    $(clone).find('.date_booked').attr('name', dateBooked.replace(/\d+/, elindex));
                    $(clone).find('.date_booked').attr('id', dateBookedId.replace(/\d+/, elindex));
                    $(clone).find('.date_booked').attr('data-date-format', "yyyy-mm-dd");
                    $(clone).find('.date_booked').datepicker({
                        autoclose: true
                    });
                    $(clone).find('.reference').attr('name', reference.replace(/\d+/, elindex));
                    $(clone).find('.reference').attr('id', referenceId.replace(/\d+/, elindex));
                    $(clone).find('.weight').attr('name', weight.replace(/\d+/, elindex));
                    $(clone).find('.weight').attr('id', weightId.replace(/\d+/, elindex));
                    $(clone).find('.total_amount').attr('name', totalAmount.replace(/\d+/, elindex));
                    $(clone).find('.total_amount').attr('id', totalAmountId.replace(/\d+/, elindex));
                    $(clone).find('.description').attr('name', description.replace(/\d+/, elindex));
                    $(clone).find('.description').attr('id', descriptionId.replace(/\d+/, elindex));
                    $(clone).find('.destination').attr('name', destination.replace(/\d+/, elindex));
                    $(clone).find('.destination').attr('id', destinationId.replace(/\d+/, elindex));
                    $(clone).find('.vat_amount').attr('name', vatAmount.replace(/\d+/, elindex));
                    $(clone).find('.vat_amount').attr('id', vatAmountId.replace(/\d+/, elindex));
                    $(clone).find('.vat_amount').attr('value', "0.00");
                    $(clone).find('.is_vat').attr('name', isVat.replace(/\d+/, elindex));
                    $(clone).find('.is_vat').attr('id', isVatId.replace(/\d+/, elindex));
                    $(clone).find('.is_vat').attr('value', "1");
                    $(clone).find('.is_vat').iCheck({checkboxClass: 'icheckbox_flat-green', increaseArea: '20%'});
                    $(clone).find('.is_vat').prop("checked", false).iCheck('update');
                    $(clone).find('button.remove_manual_invoice_details').show();
                    $(clone).find('button.remove_manual_invoice_details').removeClass('initial-button');

                    $(clone).appendTo($('.manual_invoice_container'));
                });
                $(document).on('click', '.remove_manual_invoice_details', function () {
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
                                    if ($('.manual_invoice_container .remove_manual_invoice_details').length > 1) {
                                        $(el).parent().parent().parent().parent().remove();
                                        elindex--;
                                        if ($('.add_more_manual_invoice_details').length == 0) {
                                            var addMore = $(el).parent().find('.add_more_manual_invoice_details').clone();
                                            $('.manual_invoice_container .remove_manual_invoice_details').last().parent().prepend(addMore);
                                        }
                                        calculateFields();
                                    } else {
                                        $(el).parent().find('input').val('');
                                    }
                                }
                            });
                });
                $(document).on('ifChanged', '.is_vat', function(event) {
                    calculateFields();
                });
                get_user_details();
            });
            function calculateFields() {
                var amountCalculationForVat = parseFloat(0);
                var vatRecordid = new Array();
                var values = new Array();
                $("input[name^='total_amount']").each(function(index){
                    values.push($(this).val());
                });
                var checkBoxesValue = new Array();
                $("input[name^='is_vat']").each(function(index){
                    checkBoxesValue.push($(this).is(':checked'));
                });        
                var total = 0;
                var vatPercentage = parseFloat(parseFloat($('#user_vat_percentage').val()) / 100);
                $.each(values, function (index) {
                    if (checkBoxesValue[index] == true) {
                        var checkValue = parseFloat(this).toFixed(2);
                        if(isNaN(checkValue) || checkValue == '') {
                            $('#vat_amount_'+index).val("0.00");
                        } else {
                            amountCalculationForVat = parseFloat((parseFloat(amountCalculationForVat) + parseFloat(checkValue))).toFixed(2);
                        }
                        var vatAmountOnCurrentAmount = parseFloat(parseFloat(values[index]) * vatPercentage).toFixed(2);
                        $('#vat_amount_'+index).val(vatAmountOnCurrentAmount); 
                        vatRecordid.push(index);
                    }
                    if (this != '') {
                        total =  parseFloat(parseFloat(total) + (parseFloat(values[index]))).toFixed(2);
                    }
                });
                var vatAmountOnTotalAmount = parseFloat(amountCalculationForVat * vatPercentage).toFixed(2);
                $('#total_vat').val(vatAmountOnTotalAmount);
                if(isNaN(total)) {
                    $('#net_amount').val('0.00');
                } else {
                    $('#net_amount').val(total);
                }
                var grand_total_amount = parseFloat(parseFloat(total) + parseFloat(vatAmountOnTotalAmount)).toFixed(2);
                if(isNaN(grand_total_amount)) {
                    $('#grand_total_amount').val('0.00');
                } else {
                    $('#grand_total_amount').val(grand_total_amount);
                }
            }
            
            function get_user_details() {
                var form_data = new FormData();
                var account_id = $('#customer_account').val();
                form_data.append('customer_account_id', account_id);
                form_data.append('action', 'get_user_details');
                $.ajax({
                    url: urlPage,
                    cache: false,
                    contentType: false,
                    processData: false,
                    data: form_data,
                    type: 'post',
                    dataType: 'json',
                    success: function (data) {
                        $('#user_vat_percentage').val(data.vat_value);
                        calculateFields();
                        var id = <?php echo (int)$this->invoiceId ?>;
                        if(id == 0) {
                            set_default_consignment_details();
                        }
                    },
                    error: function () {
                        //alert('error handing here');
                    }
                });
            }
            
            function set_default_consignment_details() {
                elindex = 0;
                $('.manual_invoice_container .main_clone_box').not('div:first').remove();
                if ($('.add_more_manual_invoice_details').length == 0) {
                    var addMore = '<button type="button" class="btn btn-success btn-sm add_more_manual_invoice_details">+</button>';
                    $('.manual_invoice_container .remove_manual_invoice_details').last().parent().prepend(addMore);
                }
                $('.add_more_manual_invoice_details').parent().parent().parent().parent().find('input').val('');
                $('.add_more_manual_invoice_details').parent().parent().parent().parent().find('.is_vat').prop("checked", false).iCheck('update');
                calculateFields();
            }
            
            function get_detail(obj) {
                var form_data = new FormData();
                var number_of_record = $(obj).data('number_of_record');
                var hawb = obj.value;
                var customer_account = $('#customer_account').val();
                form_data.append('hawb', hawb);
                form_data.append('account', customer_account);
                form_data.append('action', 'get_hawb_details');
                $.ajax({
                    url: urlPage,
                    cache: false,
                    contentType: false,
                    processData: false,
                    data: form_data,
                    type: 'post',
                    dataType: 'json',
                    success: function (data) {
                        if(data.status == "found") {
                            $('#date_booked_'+number_of_record).val(data.date_booked);
                            $('#reference_'+number_of_record).val(data.reference);
                            $('#destination_'+number_of_record).val(data.destination);
                            $('#service_id_'+number_of_record).val(data.service_id);
                        }
                    },
                    error: function () {
                        //alert('error handing here');
                    }
                });
            }
            
            function save_changes() {
                var form_data = $("#manual_invoice_form").serializeArray();
                $(".icheck:not(:checked)").each(function () {
                    form_data.push({name: this.name, value: '0'});
                });
                form_data.push({name: 'action', value: "save_manual_invoice"});
                $.ajax({
                    url: urlPage,
                    data: form_data,
                    type: 'post',
                    dataType: "json",
                    success: function (response) {
                       if(response.status == "success") {
                            var form_data_pdf = new FormData();
                            var new_invoice_id = response.invoice_id;
                            form_data_pdf.append('invoice_id', new_invoice_id);
                            form_data_pdf.append('action', 'manual_invoice_pdf');
                            $.ajax({
                                url: urlPage,
                                cache: false,
                                contentType: false,
                                processData: false,
                                data: form_data_pdf,
                                type: 'post',
                                dataType: 'json',
                                success: function (result) {
                                    swal({
                                        type: 'success',
                                        html:true,
                                        title: "Success!",
                                        text: response.message
                                    },function(isConfirm){
                                        if (isConfirm) {
                                          window.location.href = "invoice_list.php?itype=mni";
                                        }
                                    });
                                },
                                error: function () {
                                    //alert('error handing here');
                                }
                            });
                        } else {
                            swal({
                                type: 'error',
                                html:true,
                                title: "Sorry!",
                                text: response.message
                            });
                        }
                    }
                });
            }
            
        </script>
        <?php
    }

    /*     * *
     * Content View
     */

    protected function renderBody() {
        ?>
        <form method="post" action="manual_invoices_details.php" id="manual_invoice_form">
            <input type="hidden" name="user_vat_percentage" id="user_vat_percentage" value="0.00" >
            <input type="hidden" name="invoiceId" id="invoiceId" value="<?php echo (int)$this->invoiceId; ?>" >
            <div class="portlet light">
                <div class="portlet-title">
                    <div class="caption"> <i class="fa fa-search"></i>
                        Manual Invoices
                    </div>
                    <div class="actions">
                    </div>
                </div>
                <div class="portlet-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="row">                        
                                <div class="col-md-3">
                                   
                                    <div class="form-group">
                                         <div class="has-float-label input-icon right">
                                             <div class="first_form_col">
                                        <?php
                                        $accountParentId = $this->user->getUserAccountId();
                                        $selectedAccount = $this->invoiceObj->getUserAccountId();
                                        $allowedLevel = 0;
                                        if (Permissions::checkFilePermission('hide_subaccount')) {
                                            $allowedLevel = 1;
                                        }
                                        echo Ddl::showTreeDropdown('customer_account', 'user_account', 'user_account', 'id', $accountParentId, array("active_flag = '1' and parentid = '" . $accountParentId . "'"), $selectedAccount, "Please Select Account", 'class="form-filter bs-select form-control" data-live-search="true" onchange="get_user_details()" onblur="get_user_details()"', "", "", 'logo', '../images/userlogo/thumbnail/', 'owe_16_', false,$allowedLevel);
                                        ?>
                                         <label class="label-account">Select Customer Account</label>
                                    </div>
                                    </div>
                                     </div>
                                </div>
                                <div class="col-md-3">
                                    
                                    <div class="form-group">
                                        <div class="has-float-label input-icon right">
                                        <i class="fa fa-magic"></i>
                                            <input type="text" name="invoice_heading" id="invoice_heading" class="form-control" placeholder="Enter Heading" value="<?php echo $this->invoiceObj->getInvoiceHeading() ?>">
                                            <label for="invoice_heading">Invoice Heading</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
 <div class="form-group">
                                        <div class="has-float-label input-icon right">

                                    
                                    <div class="input-group date date-picker margin-bottom-5" data-date-format="dd-mm-yyyy">
                                        
                                        <?php $incoiceDate = (!empty($this->invoiceObj->getInvoiceDate()) ? date('d-m-Y',$this->invoiceObj->getInvoiceDate()) : ""); ?>
                                        <input type="text" class="form-control date_booked" readonly name="invoice_date" id="invoice_date" placeholder="Invoice Date" value="<?php echo $incoiceDate; ?>" >
                                        <label class="label-account">Invoice Date</label>
                                        <span class="input-group-btn">
                                            <button class="btn default" type="button"><i class="fa fa-calendar"></i></button>
                                        </span>

                                    </div>


                                </div>
                                </div>
                                </div>
                            </div>
                            <div class="manual_invoice_container">
                                <?php if(count($this->invoiceDetailObj) > 0) { ?>
                                <?php foreach($this->invoiceDetailObj as $key => $detailInvoiceObj) { ?>
                                    <div class="main_clone_box">
                                        <div class="row">
                                            <div class="col-md-3">
                                                
                                                <div class="form-group">
                                                    <div class="has-float-label input-icon right">

                                                    <i class="fa fa-pencil-square"></i> 
                                                        <input type="text" placeholder="HAWB" data-number_of_record="<?php echo $key; ?>" id="hawb_<?php echo $key; ?>" name="hawb[<?php echo $key; ?>]" class="form-control hawb" onkeyup="get_detail(this)" value="<?php echo $detailInvoiceObj->getHawb(); ?>">
                                                        <label class="label-account">HAWB</label>
                                                    </div>
                                                </div>
                                                <input type="hidden" class="service_id" name="service_id[<?php echo $key; ?>]" id="service_id_<?php echo $key; ?>" value="<?php echo $detailInvoiceObj->getServiceId(); ?>" >
                                            </div>
                                            <div class="col-md-3">
                                               
                                                <div class="form-group">
                                                    <div class="has-float-label input-icon right">

                                                <div class="input-group date date-picker margin-bottom-5" data-date-format="dd-mm-yyyy">
                                                   
                                                     <?php $dateBooked = (!empty($detailInvoiceObj->getDateBooked()) ? date('Y-m-d',$detailInvoiceObj->getDateBooked()) : ""); ?>
                                                    <input type="text" class="form-control date_booked" readonly name="date_booked[<?php echo $key; ?>]" id="date_booked_<?php echo $key; ?>" placeholder="Date Booked" value="<?php echo $dateBooked; ?>" >
                                                     <label class="label-account">Date Booked</label>
                                                      <span class="input-group-btn">
                                                        <button class="btn default" type="button"><i class="fa fa-calendar"></i></button>
                                                    </span>
                                                </div>
                                            </div>
                                             </div>
                                              </div>
                                            <div class="col-md-3">
                                                
                                                <div class="form-group">
                                                       <div class="has-float-label input-icon right">
                                                <i class="fa fa-ticket "></i>
                                                        <input type="text" placeholder="Reference" id="reference_<?php echo $key; ?>" name="reference[<?php echo $key; ?>]" class="form-control reference" value="<?php echo $detailInvoiceObj->getReference(); ?>" >
                                                        <label for="reference[<?php echo $key; ?>]">Reference</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                
                                                <div class="form-group">
                                                    <div class="has-float-label input-icon right">
                                                  <i class="fa fa-umbrella"></i>
                                                        <input type="text" placeholder="Weight (Kgs)" id="weight_<?php echo $key; ?>" name="weight[<?php echo $key; ?>]" class="form-control weight" value="<?php echo $detailInvoiceObj->getWeight(); ?>">
                                                        <label for="weight[<?php echo $key; ?>]">Weight (Kgs)</label>

                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-3">
                                               
                                                <div class="form-group">
                                                     <div class="has-float-label input-icon right">
                                                     <i class="fa fa-money"></i>
                                                        <input type="text" placeholder="Total Amount" id="total_amount_<?php echo $key; ?>" name="total_amount[<?php echo $key; ?>]" class="form-control total_amount" onkeyup="calculateFields()" value="<?php echo $detailInvoiceObj->getAmount(); ?>" > <label for="total_amount[<?php echo $key; ?>]">Total Amount</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                
                                                <div class="form-group">
                                                     <div class="has-float-label input-icon right"> <i class="fa  fa-search-plus"></i> 
                                                        <input type="text" placeholder="Description" id="description_<?php echo $key; ?>" name="description[<?php echo $key; ?>]" class="form-control description" value="<?php echo $detailInvoiceObj->getDescription(); ?>" />
                                                        <label for="description[<?php echo $key; ?>]">Description</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                
                                                <div class="form-group">
                                                       <div class="has-float-label input-icon right">
                                                    <input type="text" placeholder="Destination" id="destination_<?php echo $key; ?>" name="destination[<?php echo $key; ?>]" class="form-control destination" value="<?php echo $detailInvoiceObj->getDestination(); ?>" />
                                                    <label for="destination[<?php echo $key; ?>]">Destination</label>
                                                </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <input type="hidden" class="vat_amount" name="vat_amount[<?php echo $key; ?>]" id="vat_amount_<?php echo $key; ?>" value="<?php echo $detailInvoiceObj->getVatAmount(); ?>" >
                                                <div class="input-group margin-top-30 float-left">
                                                    <div class="icheck-inline">
                                                        <label class="label-account">
                                                            <input id="is_vat_<?php echo $key; ?>" name="is_vat[<?php echo $key; ?>]" type="checkbox" class="icheck is_vat" data-checkbox="icheckbox_flat-green" value="1" <?php echo ((!empty($detailInvoiceObj->getIsVat()) && $detailInvoiceObj->getIsVat() == "YES") ? "checked='checked'" : ""); ?> /> Vat Applicable
                                                        </label>
                                                    </div>
                                                </div>  
                                                <div class="margin-top-25 float-right">
                                                    <?php $initialbtn = (count($this->invoiceDetailObj) == 1) ? "initial-button" : ""; ?>
                                                    <?php if (count($this->invoiceDetailObj) == ($key + 1)) { ?>
                                                        <button type="button" class="btn btn-success add_more_manual_invoice_details"><i class="fa fa-plus"></i></button>
                                                        <button type="button" class="btn btn-danger remove_manual_invoice_details  <?php echo $initialbtn; ?>"><i class="fa fa-minus"></i></button>
                                                    <?php } else { ?>
                                                        <button type="button" class="btn btn-danger remove_manual_invoice_details"><i class="fa fa-minus"></i></button>
                                                    <?php } ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php } ?>
                                <?php } else { ?>
                                <div class="main_clone_box">
                                    <div class="row">
                                        <div class="col-md-3">
                                            
                                            <div class="form-group">
                                                <div class="has-float-label input-icon right">

                                               <i class="fa fa-pencil-square"></i> 
                                                    <input type="text" placeholder="HAWB" data-number_of_record="0" value="" id="hawb_0" name="hawb[0]" class="form-control hawb" onkeyup="get_detail(this)">
                                                    <label class="label-account">HAWB</label>
                                                </div>
                                            </div>
                                            <input type="hidden" class="service_id" name="service_id[0]" id="service_id_0" value="0" >
                                        </div>
                                        <div class="col-md-3">
                                             <div class="form-group">
                                                <div class="has-float-label input-icon right">

                                         
                                            <div class="input-group date date-picker margin-bottom-5" data-date-format="dd-mm-yyyy">
                                                
                                                <input type="text" class="form-control date_booked" readonly name="date_booked[0]" id="date_booked_0" placeholder="Date Booked" value="" >
<span class="input-group-btn">
                                                    <button class="btn default" type="button"><i class="fa fa-calendar"></i></button>
                                                </span>

                                                   <label class="label-account">Date Booked</label>

                                            </div>
                                        </div>
                                        </div>
                                        </div>
                                        <div class="col-md-3">
                                           
                                            <div class="form-group">
                                                 <div class="has-float-label input-icon right">
                                                <i class="fa fa-ticket "></i> 
                                                    <input type="text" placeholder="Reference" value="" id="reference_0" name="reference[0]" class="form-control reference"> <label for="reference[0]">Reference</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            
                                            <div class="form-group">
                                                <div class="has-float-label input-icon right"><i class="fa fa-umbrella"></i>
                                                    <input type="text" placeholder="Weight (Kgs)" value="" id="weight_0" name="weight[0]" class="form-control weight">
                                                    <label for="weight[0]">Weight (Kgs)</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-3">
                                           
                                            <div class="form-group">
                                                  <div class="has-float-label input-icon right"> <i class="fa fa-money"></i> 
                                                    <input type="text" placeholder="Total Amount" value="" id="total_amount_0" name="total_amount[0]" class="form-control total_amount" onkeyup="calculateFields()">
                                                     <label for="total_amount[0]">Total Amount</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                         
                                            <div class="form-group">
                                              <div class="has-float-label input-icon right">  <i class="fa  fa-search-plus"></i> 
                                                    <input type="text" placeholder="Description" value="" id="description_0" name="description[0]" class="form-control description" />
                                                       <label for="description[0]">Description</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                           
                                            <div class="form-group">
                                                 <div class="has-float-label input-icon right">  
                                                <input type="text" placeholder="Destination" value="" id="destination_0" name="destination[0]" class="form-control destination" /> <label for="destination[0]">Destination</label>
                                            </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <input type="hidden" class="vat_amount" name="vat_amount[0]" id="vat_amount_0" value="0.00" >
                                            <div class="input-group float-left">
                                                <div class="icheck-inline">
                                                    <label class="label-account">
                                                        <input id="is_vat_0" name="is_vat[0]" type="checkbox" class="icheck is_vat" data-checkbox="icheckbox_flat-green" value="1" /> Vat Applicable
                                                    </label>
                                                </div>
                                            </div>  
                                            <div class="margin-top-25 float-right">
                                                <button type="button" class="btn btn-success btn-sm add_more_manual_invoice_details">+</button>
                                                <button type="button" class="btn btn-danger btn-sm remove_manual_invoice_details initial-button">-</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                    <?php if($this->invoiceId > 0) { ?>
                        <input type="hidden" name="elindex-hardcode" id="elindex-hardcode" value="<?php echo (count($this->invoiceDetailObj) - 1); ?>" />
                    <?php } else { ?>
                        <input type="hidden" name="elindex-hardcode" id="elindex-hardcode" value="0" />
                    <?php } ?>
                </div>
            </div>

            <div class="portlet light">
                <div class="portlet-title">
                    <div class="caption"> <i class="fa fa-search"></i>
                        Calculate Manual Invoices
                    </div>
                    <div class="actions">
                    </div>
                </div>
                <div class="portlet-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="row">
                                <div class="col-md-3">
                                    
                                    <div class="form-group">
                                       <div class="has-float-label input-icon right">  
                                        <i class="fa  fa-tag"></i> 
                                            <input class="form-control" name="net_amount" id="net_amount" type="text" placeholder="Net Amount" value=""><label class="label-account">Net Amount</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                  
                                    <div class="form-group">
                                        <div class="has-float-label input-icon right">  <i class="fa  fa-tachometer"></i>
                                            <input class="form-control" name="total_vat" id="total_vat" type="text" placeholder="VAT" value="">
                                              <label class="label-account">VAT</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                  
                                    <div class="form-group">
                                      <div class="has-float-label input-icon right"> <i class="fa fa-stethoscope "></i> 
                                            <input class="form-control" name="grand_total_amount" id="grand_total_amount" type="text" placeholder="Total Amount" value="" />
                                              <label class="label-account">Total Amount</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <input type="button" class="btn btn-success" value="Save Changes" onclick="save_changes()" >
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
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