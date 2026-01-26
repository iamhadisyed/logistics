<?php
////////////////////////////////////////////////////
//
// Controller for Admin - Country details page
//
////////////////////////////////////////////////////
// get settings

require_once("../includes/settings/config.inc.php");
    include_classes([
        'tcpdf'
        ], '3rdparty/tcpdf');
include_classes([   
                    'ivisualcomponent','ddl.inc'
                ],'library');
include_classes([   
                    'iaddress.class',
                    'creditnote.class',
                    'creditnotefilter.class',
                    'creditnotedetails.class',
                    'creditnotedetailsfilter.class',
                    'consignment.class',
                    'consignmentfilter.class',
                    'invoices.class',
                    'invoicesfilter.class',]);
// set up local page class
class Page extends BasePage {

    private $error_msg = "";
    private $user = NULL;
    private $credit_note_id = 0;
    private $creditNoteObj;
    private $creditNoteDetailObj = array();

    public function init() {
        $this->user = SessionManager::getUser();
         $this->breadCrumb['data'] = array('index.php' => Translation::GetCaption("HOME"), 'Credit Note'
        );
        if(isset($_GET['id']) && $_GET['id'] > 0) {
            $this->credit_note_id = $_GET['id'];
            $this->creditNoteObj = new CreditNote($_GET['id']);
            $creditNoteDetailsFilter = new CreditNoteDetailsFilter();
            $creditNoteDetailsFilter->where(['cnd.credit_note_id' => $this->credit_note_id]);
            $this->creditNoteDetailObj = $creditNoteDetailsFilter->getList();
        } else {
            $this->creditNoteObj = new CreditNote();
        }
        
        if (isset($this->form_vars['action']) && $this->form_vars['action'] == 'save_credit_note') {
            $output = array();
            $customerAccount = $this->form_vars['account'];
            $invoiceType = $this->form_vars['invoice_type'];
            $invoiceNumber = $this->form_vars['invoice_number'];
            $creditNoteType = $this->form_vars['credit_note_type'];
            $hawb_number = $this->form_vars['hawb_number'];
            $credit_note_heading = $this->form_vars['credit_note_heading'];
            if($this->form_vars['credit_date'] != "") {
                $credit_date = strtotime($this->form_vars['credit_date']);
            } else {
                $credit_date = "";
            }
            $netAmount = $this->form_vars['net_amount'];
            $totalVat = $this->form_vars['total_vat'];
            $grandTotalAmount = $this->form_vars['grand_total_amount'];
            
            $added_by = $this->user->getId();
            $date_added = time();
            $updated_by = $this->user->getId();
            $date_update = time();
            
            if($this->form_vars['credit_note_id'] > 0) {
                $creditNote = new CreditNote($this->form_vars['credit_note_id']);
            } else {
                $result = Invoices::generateInvoiceNumber($this->user->getUserAccountId(), 'CRD');
                if($result['status'] == "success") {
                    $credit_note_number = $result['invoice_number'];
                }
                $creditNote = new CreditNote();
                $creditNote->setCreditNoteNumber($credit_note_number);
            }
            $creditNote->setUserAccountId($customerAccount);
            $creditNote->setInvoiceType($invoiceType);
            $creditNote->setInvoiceNumber($invoiceNumber);
            $creditNote->setCreditNoteType($creditNoteType);
            $creditNote->setHawb($hawb_number);
            $creditNote->setCreditNoteHeading($credit_note_heading);
            $creditNote->setCreditDate($credit_date);
            $creditNote->setNetAmount($netAmount);
            $creditNote->setVatAmount($totalVat);
            $creditNote->setCreditTotal($grandTotalAmount);
            $creditNote->setCreditNoteBy($this->user->getUserAccountId());
            $creditNote->setAddedBy($added_by);
            $creditNote->setDateCreated($date_added);
            $creditNote->setUpdatedBy($updated_by);
            $creditNote->setDateUpdated($date_update);
            $creditNote->save();
            
            $last_credit_note_id = $creditNote->getId();
            CreditNote::deleteCreditNoteDetailsByCreditNoteId($last_credit_note_id);
            $hawbs = $this->form_vars['hawb'];
            foreach($hawbs as $key => $hawb) {
                if($this->form_vars['date_booked'][$key] != "") {
                    $dateBooked = strtotime($this->form_vars['date_booked'][$key]);
                } else {
                    $dateBooked = "";
                }
                $reference = $this->form_vars['reference'][$key];
                $invoiceAmount = $this->form_vars['charged_amount'][$key];
                $actualAmount = $this->form_vars['actual_amount'][$key];
                $totalAmount = $this->form_vars['total_amount'][$key];
                $description = $this->form_vars['description'][$key];
                $vatAmount = $this->form_vars['vat_amount'][$key];
                
                if(($this->form_vars['is_vat'][$key] > 0) || ($this->form_vars['is_vat'][$key] == "")) {
                    $isVat = "YES";
                } else {
                    $isVat = "NO";
                }
                
                $creditNoteDetails = new CreditNoteDetails();
                $creditNoteDetails->setCreditNoteId($last_credit_note_id);
                $creditNoteDetails->setHawb($hawb);
                $creditNoteDetails->setDateBooked($dateBooked);
                $creditNoteDetails->setReference($reference);
                $creditNoteDetails->setInvoiceAmount((float)$invoiceAmount);
                $creditNoteDetails->setChargeableAmount((float)$actualAmount);
                $creditNoteDetails->setCreditAmount((float)$totalAmount);
                $creditNoteDetails->setDescription($description);
                $creditNoteDetails->setVatAmount((float)$vatAmount);
                $creditNoteDetails->setIsVatable($isVat);
                $creditNoteDetails->setAddedBy($added_by);
                $creditNoteDetails->setDateCreated($date_added);
                $creditNoteDetails->setUpdatedBy($updated_by);
                $creditNoteDetails->setDateUpdated($date_update);
                $creditNoteDetails->save();
            }

            $output['status'] = "success";
            $output['message'] = "Credit note is save successfully";
            $output['credit_note_id'] = $last_credit_note_id;
            echo json_encode($output);
            exit;
        }
        
        if (isset($this->form_vars['action']) && trim($this->form_vars['action']) == 'credit_note_pdf') {
            $output = array();
            $credit_note_id = $this->form_vars['credit_note_id'];
            $pdf = new InvoicePDF();
            $data = $pdf->SaveCreditNotePDF($credit_note_id);
            $CreditNoteObj = new CreditNote($credit_note_id);
            $CreditNoteObj->setPdf($data['FILENAME']);
            $CreditNoteObj->save();
            $output['path'][] = $data;
            $output['status'] = "success";
            $output['message'] = "Credit note Pdf is generated successfully";
            echo json_encode($output);
            die;
        }
         
        if (isset($this->form_vars['action']) && $this->form_vars['action'] == 'get_user_details') {
            $output = array();
            $customer_account_id = $this->form_vars['customer_account_id'];
            $userAccountObj = new CustomerAccount($customer_account_id);
            if($userAccountObj->getVatChargable() > 0) {
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
            $userAccount = $this->form_vars['user_account'];
            $consignmentFilter = new ConsignmentFilter();
            $consignmentFilter->addConsignmentChargesJoin($this->user->getUserAccountId(),$this->user->getUserType());
            $consignmentFilter->addInvoiceJoin();
            $consignmentFilter->addFilter("     cc.cost_type = 'customer'","filter");
            $consignmentFilter->addFieldFilter("    c.hawb", $hawb);
            $consignmentFilter->addFilter("     cc.account_id = '" . DbAccess3::escape($userAccount) . "'","filter");
            $consignmentFilter->addGroupBy('c.id');
            $consignmentFilterObj = $consignmentFilter->getColumnList('c.hawb,c.date_booked,c.reference,inv.net_amount');

            if(count($consignmentFilterObj) > 0) {
                $output['status'] = "found";
                foreach($consignmentFilterObj as $consignmentObj) {
                    if($consignmentObj->getDateBooked() > 0) {
                        $date_booked = date('Y-m-d',$consignmentObj->getDateBooked());
                    } else {
                        $date_booked = "";
                    }
                    $output['date_booked'] = $date_booked; 
                    $output['reference'] = $consignmentObj->getReference(); 
                    $output['invoice_amount'] = $consignmentObj->getNetAmount();
                }
            }
            echo json_encode($output);
            die;
        }
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
            var urlPage = 'credit_note_details.php';
            var elindex = 0;
            $(document).ajaxStart($.blockUI).ajaxStop($.unblockUI);
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
                $(document).on('click', '.add_more_credit_note_details', function () {
                    elindex++;
                    var clone = $(this).parent().parent().parent().clone();
                    var hawb = $(clone).find('.hawb').attr('name');
                    var hawbId = $(clone).find('.hawb').attr('id');
                    var dateBooked = $(clone).find('.date_booked').attr('name');
                    var dateBookedId = $(clone).find('.date_booked').attr('id');
                    var reference = $(clone).find('.reference').attr('name');
                    var referenceId = $(clone).find('.reference').attr('id');
                    var invoiceAmount = $(clone).find('.invoice_amount').attr('name');
                    var invoiceAmountId = $(clone).find('.invoice_amount').attr('id');
                    var chargeableAmount = $(clone).find('.chargeable_amount').attr('name');
                    var chargeableAmountId = $(clone).find('.chargeable_amount').attr('id');
                    var creditAmountAmount = $(clone).find('.credit_amount').attr('name');
                    var creditAmountAmountId = $(clone).find('.credit_amount').attr('id');
                    var description = $(clone).find('.description').attr('name');
                    var descriptionId = $(clone).find('.description').attr('id');
                    var isVat = $(clone).find('.is_vat').attr('name');
                    var isVatId = $(clone).find('.is_vat').attr('id');
                    var vatAmount = $(clone).find('.vat_amount').attr('name');
                    var vatAmountId = $(clone).find('.vat_amount').attr('id');

                    $(this).remove();

                    $(clone).find('input').val('');
                    $(clone).find('.hawb').attr('name', hawb.replace(/\d+/, elindex));
                    $(clone).find('.hawb').attr('id', hawbId.replace(/\d+/, elindex));
                    $(clone).find('.hawb').attr('data-number_of_record', elindex);
                    $(clone).find('.date_booked').attr('name', dateBooked.replace(/\d+/, elindex));
                    $(clone).find('.date_booked').attr('id', dateBookedId.replace(/\d+/, elindex));
                    $(clone).find('.date_booked').attr('data-date-format', "yyyy-mm-dd");
                    $(clone).find('.date_booked').datepicker({
                        autoclose: true
                    });
                    $(clone).find('.reference').attr('name', reference.replace(/\d+/, elindex));
                    $(clone).find('.reference').attr('id', referenceId.replace(/\d+/, elindex));
                    $(clone).find('.invoice_amount').attr('name', invoiceAmount.replace(/\d+/, elindex));
                    $(clone).find('.invoice_amount').attr('id', invoiceAmountId.replace(/\d+/, elindex));
                    $(clone).find('.chargeable_amount').attr('name', chargeableAmount.replace(/\d+/, elindex));
                    $(clone).find('.chargeable_amount').attr('id', chargeableAmountId.replace(/\d+/, elindex));
                    $(clone).find('.credit_amount').attr('name', creditAmountAmount.replace(/\d+/, elindex));
                    $(clone).find('.credit_amount').attr('id', creditAmountAmountId.replace(/\d+/, elindex));
                    $(clone).find('.description').attr('name', description.replace(/\d+/, elindex));
                    $(clone).find('.description').attr('id', descriptionId.replace(/\d+/, elindex));
                    $(clone).find('.vat_amount').attr('name', vatAmount.replace(/\d+/, elindex));
                    $(clone).find('.vat_amount').attr('id', vatAmountId.replace(/\d+/, elindex));
                    $(clone).find('.vat_amount').attr('value', "0.00");
                    $(clone).find('.is_vat').attr('name', isVat.replace(/\d+/, elindex));
                    $(clone).find('.is_vat').attr('id', isVatId.replace(/\d+/, elindex));
                    $(clone).find('.is_vat').attr('value', "1");
                    $(clone).find('.is_vat').iCheck({checkboxClass: 'icheckbox_flat-green', increaseArea: '20%'});
                    $(clone).find('.is_vat').prop("checked", false).iCheck('update');
                    $(clone).find('button.remove_credit_note_details').show();
                    $(clone).find('button.remove_credit_note_details').removeClass('initial-button');
                    $(clone).appendTo($('#consignment-detail-new'));
                });
                $(document).on('click', '.remove_credit_note_details', function () {
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
                            if ($('#consignment-detail-new .remove_credit_note_details').length > 1) {
                                $(el).parent().parent().parent().remove();
                                elindex--;
                                if ($('.add_more_credit_note_details').length == 0) {
                                    var addMore = $(el).parent().find('.add_more_credit_note_details').clone();
                                    $('#consignment-detail-new .remove_credit_note_details').last().parent().prepend(addMore);
                                }
                            calculateFields();
                            } else {
                                $(el).parent().find('input').val('');
                            }
                        }
                    });
                });
                $(document).on('ifChanged', '.is_vat', function (event) {
                    calculateFields();
                });
                <?php if($this->credit_note_id > 0) { ?>
                populateInvoices();
                get_user_details();
                hawbOptions();
                $('#consignment-detail-new').show();
                $('#message_box').hide();
                <?php } ?>
            });
            function getInvoiceData()
            {
                var accountNumber = $("#account").val();
                var invoiceNumber = $("#invoice_number").val();
                var hawbNumber = $("#hawb_number").val();
                var invoiceType = $("#invoice_type").val();
                var incoiveOption = $("#credit_note_type").val();
                var credit_note_id = $("#credit_note_id").val();
                if (incoiveOption == 'FULL' && invoiceNumber == '')
                {
                    swal("Sorry!", "Please select the invoice from dropdown.", "error");
                    return false;
                }
                if (accountNumber == '')
                {
                    swal("Sorry!", "Please select the account number.", "error");
                    return false;
                }
                if (incoiveOption == 'PARTIAL' && hawbNumber == '')
                {
                    swal("Sorry!", "Please enter hawb numbers.", "error");
                    return false;
                }
                set_default_consignment_details();
                if (incoiveOption != 'OTHER') {
                    $.post("credit_note_ajax.php",
                    {
                        action: 'LOAD-INVOICES-DATA',
                        accountNumber: accountNumber,
                        invoiceNumber: invoiceNumber,
                        hawbNumber: hawbNumber,
                        incoiveOption: incoiveOption,
                        invoiceType: invoiceType,
                        creditNoteId: credit_note_id
                    },  function (data) {
                            if (incoiveOption == 'PARTIAL') {
                                $.each(JSON.parse(data), function (index, element)
                                {
                                    if (index > 0) {
                                        $('.add_more_credit_note_details').trigger('click');
                                    }
                                    $('#hawb_' + index).val(element.hawb);
                                    $('#date_booked_' + index).val(element.date_booked);
                                    $('#reference_' + index).val(element.reference);
                                    $('#charged_amount_' + index).val(element.invoice_amount);
                                });
                            } else if (incoiveOption == 'FULL') {
                                $.each(JSON.parse(data), function (index, element)
                                {
                                    if (index > 0) {
                                        $('.add_more_credit_note_details').trigger('click');
                                    }
                                    $('#charged_amount_' + index).val(element.invoice_amount);
                                    $('#description_' + index).val(element.description);
                                });
                            }
                            calculateFields();
                    });
                }
            }
            function populateInvoices()
            {
                var invoiceOption = $('#invoice_type').val();
                var accNumber = $('#account').val();
                var invoice_number = $("#invoice_number").val();
                $.post("credit_note_ajax.php", {
                    action: 'LOAD-INVOICES',
                    account: accNumber,
                    invoiceType: invoiceOption,
                    invoiceNumber: invoice_number
                }, function (data) {
                    $('#invoceData').html(data);
                    $('#invoice_number').val('<?php echo $this->creditNoteObj->getInvoiceNumber(); ?>');
                });
            }
            function hawbOptions()
            {
                if ($("#credit_note_type").val() == 'PARTIAL') {
                    $('#hawb_div').show();
                } else {
                    $('#hawb_div').hide();
                }
            }
            function get_user_details() {
                var form_data = new FormData();
                var account_id = $('#account').val();
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
                         <?php if($this->credit_note_id > 0) { ?>
                                calculateFields();
                         <?php } ?>
                    },
                    error: function () {
                        //alert('error handing here');
                    }
                });
            }
            function calculateFields() {
                calculate_individual_populated();
                var amountCalculationForVat = parseFloat(0);
                var vatRecordid = new Array();
                var values = new Array();
                $("input[name^='total_amount']").each(function (index) {
                    values.push($(this).val());
                });
                var checkBoxesValue = new Array();
                $("input[name^='is_vat']").each(function (index) {
                    checkBoxesValue.push($(this).is(':checked'));
                });
                var total = 0;
                var vatPercentage = parseFloat(parseFloat($('#user_vat_percentage').val()) / 100);
                $.each(values, function (index) {
                    if (checkBoxesValue[index] == true) {
                        var checkValue = parseFloat(this).toFixed(2);
                        if (isNaN(checkValue) || checkValue == '') {
                            $('#vat_amount_' + index).val("0.00");
                        } else {
                            amountCalculationForVat = parseFloat((parseFloat(amountCalculationForVat) + parseFloat(checkValue))).toFixed(2);;
                        }
                        var vatAmountOnCurrentAmount = parseFloat(parseFloat(values[index]) * vatPercentage).toFixed(2);
                        $('#vat_amount_' + index).val(vatAmountOnCurrentAmount);
                        vatRecordid.push(index);
                    }
                    if (this != '') {
                        total =  parseFloat(parseFloat(total) + (parseFloat(values[index]))).toFixed(2);
                    }
                });
                var vatAmountOnTotalAmount = parseFloat(amountCalculationForVat * vatPercentage).toFixed(2);
                $('#total_vat').val(vatAmountOnTotalAmount);
                if (isNaN(total)) {
                    $('#net_amount').val('0.00');
                } else {
                    $('#net_amount').val(total);
                }
                var grand_total_amount = parseFloat(parseFloat(total) + parseFloat(vatAmountOnTotalAmount)).toFixed(2);
                if (isNaN(grand_total_amount)) {
                    $('#grand_total_amount').val('0.00');
                } else {
                    $('#grand_total_amount').val(grand_total_amount);
                }
            }
            function calculate_individual_populated() {
                var charged_amount = new Array();
                $("input[name^='charged_amount']").each(function (index) {
                    charged_amount.push($(this).val());
                });
                var actual_amount = new Array();
                $("input[name^='actual_amount']").each(function (index) {
                    actual_amount.push($(this).val());
                });
                var total_amount = 0;
                $.each(charged_amount, function (index) {
                    var calculate = 0;
                    if(charged_amount[index] != "" && actual_amount[index] != "") {
                        calculate = parseFloat(parseFloat(charged_amount[index]) - parseFloat(actual_amount[index])).toFixed(2);
                    } else if(charged_amount[index] != "") {
                        calculate = parseFloat(charged_amount[index]).toFixed(2);
                    } else if(actual_amount[index] != "") {
                        calculate = parseFloat(actual_amount[index]).toFixed(2);
                    }
                    if (isNaN(calculate) || calculate == '') {
                        $('#total_amount_' + index).val('0.00');
                    } else {
                        $('#total_amount_' + index).val(calculate);
                    }
                    if (this != '') {
                        total_amount += parseFloat(parseFloat(total_amount) + parseFloat(calculate)).toFixed(2);
                    }
                }); 
            }
            function set_default_consignment_details() {
                $('#consignment-detail-new').show();
                $('#message_box').hide();
                elindex = 0;
                $('#consignment-detail-new .row').not('div:first').remove();
                if ($('.add_more_credit_note_details').length == 0) {
                    var addMore = '<button type="button" class="btn btn-success btn-sm add_more_credit_note_details">+</button>';
                    $('#consignment-detail-new .remove_credit_note_details').last().parent().prepend(addMore);
                }
                $('.add_more_credit_note_details').parent().parent().parent().find('input').val('');
                $('.add_more_credit_note_details').parent().parent().parent().find('.is_vat').prop("checked", false).iCheck('update');
                calculateFields();
            }
            function save_changes() {
                var form_data = $("#credit_note_form").serializeArray();
                $(".icheck:checked").each(function () {
                    form_data.push({name: this.name, value: this.value});
                });
                $(".icheck:not(:checked)").each(function () {
                    form_data.push({name: this.name, value: '0'});
                });
                form_data.push({name: 'action', value: "save_credit_note"});
                $.ajax({
                    url: urlPage,
                    data: form_data,
                    type: 'post',
                    dataType: "json",
                    success: function (response) {
                        console.log(response);
                       if(response.status == "success") {
                            var form_data_pdf = new FormData();
                            var new_credit_note_id = response.credit_note_id;
                            form_data_pdf.append('credit_note_id', new_credit_note_id);
                            form_data_pdf.append('action', 'credit_note_pdf');
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
                                          window.location.href = "credit_note.php";
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
                                text: "please contact to admin"
                            });
                        }
                    }
                });
            }
            function get_hawb_details(obj) {
                var credit_note_type = $('#credit_note_type').val();
                if(credit_note_type == "OTHER") {
                    var form_data = new FormData();
                    var number_of_record = $(obj).data('number_of_record');
                    var hawb = obj.value;
                    var user_account = $('#account').val();
                    form_data.append('hawb', hawb);
                    form_data.append('user_account', user_account);
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
                                $('#charged_amount_'+number_of_record).val(data.invoice_amount);
                            } else {
                                $('#date_booked_'+number_of_record).val('');
                                $('#reference_'+number_of_record).val('');
                                $('#charged_amount_'+number_of_record).val('');
                            }
                            calculateFields();
                        },
                        error: function () {
                            //alert('error handing here');
                        }
                    });
                }
            }
        </script>
        <?php
    }

    /*     * *
     * This page's content
     * @return void
     */

    public function renderBody() {
        ?>
        <form method="post" action="credit_note_details.php" id="credit_note_form">
            <input type="hidden" name="credit_note_id" value="<?php echo $this->credit_note_id; ?>" >
            <div class="portlet light" >
                <div class="portlet-title">
                    <div class="caption"> <i class="glyphicon glyphicon-briefcase"></i>Credit Note Details </div>
                </div>
                <div class="portlet-body">
                    <div data-rail-color="blue" data-handle-color="blue">
                        <div class="row">
                            <div class="col-md-12">
                                <?php
                                if ($this->error_msg != "") {
                                    echo $this->error_msg;
                                }
                                ?>
                            </div>
                        </div>
                        <div class="row">
                            <input type="hidden" name="user_vat_percentage" id="user_vat_percentage" value="0.00" >
                            <div class="col-md-2">

                                 <div class="form-group"> 
                                     <div class="has-float-label">

                          
                           
                                <?php
                                $selectedAccount = $this->creditNoteObj->getUserAccountId();
                                $accountParentId = $this->user->getUserAccountId();
                                $allowedLevel = 0;
                                if (Permissions::checkFilePermission('hide_subaccount')) {
                                    $allowedLevel = 1;
                                }
                                echo Ddl::showTreeDropdown('account', 'user_account', 'user_account', 'id', $accountParentId, array("active_flag = '1' and parentid = '" . $accountParentId . "'"), $selectedAccount, "Please Select Account", 'class="form-filter bs-select form-control" data-live-search="true" onchange="populateInvoices(); get_user_details();"', "", "", 'logo', '../images/userlogo/thumbnail/', 'owe_16_', false,$allowedLevel);
                                ?>     <label>Account</label>

                            </div>
                            </div>
                            </div>



                            <div class="col-md-2">

                                 <div class="form-group"> 
                                     <div class="has-float-label">

                             
                              
                                <?php
                                $invoiceSection = array("INV" => "Auto Invoices", "MNI" => "Manual Invoices");
                                $selectedInvoiceType = $this->creditNoteObj->getInvoiceType();
                                echo Ddl::generateArrayDDL('invoice_type', $invoiceSection, $selectedInvoiceType, '', 'onchange="populateInvoices()"  class="form-control select2 select" rel="tooltip" data-original-title="User Type" placeholder="User Type"');
                                ?>  <label>Invoice Type</label>
                            </div>
                              </div>
                                </div>
                            <div class="col-md-2">
                               
                                <div class="form-group" id="invoceData">

                                     <div class="has-float-label">


                                    <?php
                                    $invoiceNumbersSection = array("" => "Please Select Invoices");
                                    $selectedInvoiceNumber = $this->creditNoteObj->getInvoiceNumber();
                                    echo Ddl::generateArrayDDL('invoice_number', $invoiceNumbersSection, $selectedInvoiceNumber, '', ' class="form-control select2 select" rel="tooltip" data-original-title="Invoice Number" placeholder="Invoice Number"');
                                    ?> <label>Invoice Number</label>
                                </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                  <div class="has-float-label">

                                 

                                
                                <?php
                                $creditNoteOption = array("PARTIAL" => "Partial Invoice", "FULL" => "Full Invoice", "OTHER" => "Others");
                                $selectedCreditNoteType = $this->creditNoteObj->getCreditNoteType();
                                echo Ddl::generateArrayDDL('credit_note_type', $creditNoteOption, $selectedCreditNoteType, '', 'onchange="hawbOptions()"  class="form-control select2 select" rel="tooltip" data-original-title="User Type" placeholder="User Type"');
                                ?>
                                <label>Credit Note Type</label>

                            </div>
                              </div>
                               
                            <div class="col-md-3" id="hawb_div">
                          
                                <div class="form-group">
                                     <div class="has-float-label input-icon right">
                                    <textarea  class="form-control" type="text" id="hawb_number" name="hawb_number" placeholder="HAWB" ><?php echo nl2br($this->creditNoteObj->getHawb()) ?></textarea>
                                          <label for="hawb_number">HAWB</label>
                                </div>
                            </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                
                                <div class="form-group">
                                     <div class="has-float-label input-icon right">
                                        <i class="fa fa-user"></i>

                                <input name="credit_note_heading"  class="form-control" id="credit_note_heading" value="<?php echo $this->creditNoteObj->getCreditNoteHeading() ?>" placeholder="Credit note heading section" >
                                <label for="credit_note_heading">Credit Note Heading</label>

                            </div>
                              </div>
                                </div>
                            <div class="col-md-3">
                               
                                <div class="input-group date date-picker margin-bottom-5" data-date-format="dd-mm-yyyy">
                                    <span class="input-group-btn">
                                        <button class="btn default" type="button"><i class="fa fa-calendar"></i></button>
                                    </span>
                                    <?php $creditDate = (!empty($this->creditNoteObj->getCreditDate()) ? formatDate(date("Y-m-d", $this->creditNoteObj->getCreditDate())) : "" ) ?>
                                    <input type="text" class="form-control credit_date" readonly name="credit_date" id="credit_date" placeholder="Credit Dated" value="<?php echo $creditDate; ?>" >
                                </div>
                            </div>
                            <div class="col-md-3">
                                <button type="button" class="btn btn-primary col-md-12" id="btn_save" name="btn_save" value="Show Data" onclick="getInvoiceData();"> Search </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="portlet light" >
                <div class="portlet-title">
                    <div class="caption"> <i class="glyphicon glyphicon-briefcase"></i>Consignment Details </div>
                </div>
                <div class="portlet-body">
                    <div id="consignment-detail-new">
                        <?php if(count($this->creditNoteDetailObj) > 0) { ?>
                        <?php foreach($this->creditNoteDetailObj as $key => $creditDetailObj) { ?>
                        <div class="row">
                            <div class="col-md-2">


                                  <div class="form-group">
                                     <div class="has-float-label input-icon right">
                                        <i class="fa fa-user"></i>

                                   
                                    <input class="form-control hawb" data-number_of_record="<?php echo $key; ?>" name="hawb[<?php echo $key; ?>]" id="hawb_<?php echo $key; ?>" type="text" placeholder="Order Reference" onkeyup="get_hawb_details(this)" value="<?php echo $creditDetailObj->getHawb() ?>" >


                                     <label for="hawb[<?php echo $key; ?>]" id="hawb_<?php echo $key; ?>">Order Reference </label>
                                </div>
                                 </div>
                                  </div>
                        
                            <div class="col-md-2">
                                <label class="label-account">Reference Date </label>
                                <div class="input-group date date-picker margin-bottom-5" data-date-format="dd-mm-yyyy">
                                    <span class="input-group-btn">
                                        <button class="btn default" type="button"><i class="fa fa-calendar"></i></button>
                                    </span>
                                    <?php $referenceDate = (!empty($creditDetailObj->getDateBooked()) ? formatDate(date("Y-m-d", $creditDetailObj->getDateBooked())) : "" ) ?>
                                    <input type="text" class="form-control date_booked" readonly name="date_booked[<?php echo $key; ?>]" id="date_booked_<?php echo $key; ?>" placeholder="Reference Date" value="<?php echo $referenceDate ?>" >
                                </div>
                            </div>
                            <div class="col-md-2">
                           
                                  
                                    <div class="form-group">
                                     <div class="has-float-label input-icon right">
                                        <i class="fa fa-user"></i>
                                    <input class="form-control reference" type="text" id="reference_<?php echo $key; ?>" name="reference[<?php echo $key; ?>]" placeholder="Reference" value="<?php echo $creditDetailObj->getReference() ?>" />
                                      <label for="reference[<?php echo $key; ?>]">Reference </label>
                                </div>
                                    </div>
                                        </div>
                          
                            <div class="col-md-2">
                                <div class="form-group">
                                <div class="has-float-label input-icon right">
                                        <i class="fa fa-user"></i>
                                    
                                    <input class="form-control invoice_amount" id="charged_amount_<?php echo $key; ?>" name="charged_amount[<?php echo $key; ?>]" type="text" placeholder="Invoiced Amount" onkeyup="calculateFields()" value="<?php echo $creditDetailObj->getInvoiceAmount() ?>" > 
                                    <label for="charged_amount[<?php echo $key; ?>]">Invoiced Amount</label>
                                </div>
                                </div>
                                </div>
                         
                            <div class="col-md-2">
                                <div class="form-group">
                                     <div class="has-float-label input-icon right">
                                        <i class="fa fa-user"></i>
                                    
                                    <input class="form-control chargeable_amount" id="actual_amount_<?php echo $key; ?>" name="actual_amount[<?php echo $key; ?>]" type="text" placeholder="Chargeable Amount" onkeyup="calculateFields()" value="<?php echo $creditDetailObj->getChargeableAmount() ?>" >
                                    <label for="actual_amount[<?php echo $key; ?>]">Chargeable Amount</label>
                                </div>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <div class="has-float-label input-icon right">
                                        <i class="fa fa-user"></i>
                                    
                                    <input class="form-control credit_amount" id="total_amount_<?php echo $key; ?>" name="total_amount[<?php echo $key; ?>]" type="text" placeholder="Credit Amount" readonly="readonly" value="<?php echo $creditDetailObj->getCreditAmount() ?>" ><label for="total_amount[<?php echo $key; ?>]">Credit Amount</label>
                                </div>
                                  </div>
                            </div>
                            <div style="clear:both;">
                            </div>
                            <div class="col-md-9">
                                <div class="form-group">
                                    <div class="has-float-label input-icon right">
                                        <i class="fa fa-user"></i>
                                    
                                    <input class="form-control description" name="description[<?php echo $key; ?>]" id="description_<?php echo $key; ?>" type="text" placeholder="Description" value="<?php echo $creditDetailObj->getDescription() ?>" >
                                    <label for="description[<?php echo $key; ?>]">Description</label>
                                </div>
                                 </div>
                            </div>
                            <div class="col-md-3">
                                <input type="hidden" class="vat_amount" name="vat_amount[<?php echo $key; ?>]" id="vat_amount_<?php echo $key; ?>" value="<?php echo $creditDetailObj->getVatAmount() ?>" >
                                <div class="input-group margin-top-30 float-left">
                                    <div class="icheck-inline">
                                        <label class="label-account">
                                            <input class="icheck is_vat" id="is_vat_<?php echo $key; ?>" name="is_vat[<?php echo $key; ?>]" type="checkbox" data-checkbox="icheckbox_flat-green" value="1" <?php echo ((!empty($creditDetailObj->getIsVatable()) && $creditDetailObj->getIsVatable() == "YES") ? "checked='checked'" : ""); ?> /> VAT Applicable
                                        </label>
                                    </div>
                                </div>  
                                <div class="margin-top-25 float-right">
                                    <?php $initialbtn = (count($this->creditNoteDetailObj) == 1) ? "initial-button" : ""; ?>
                                    <?php if (count($this->creditNoteDetailObj) == ($key + 1)) { ?>
                                        <button type="button" class="btn btn-success add_more_credit_note_details"><i class="fa fa-plus"></i></button>
                                        <button type="button" class="btn btn-danger remove_credit_note_details  <?php echo $initialbtn; ?>"><i class="fa fa-minus"></i></button>
                                    <?php } else { ?>
                                        <button type="button" class="btn btn-danger remove_credit_note_details"><i class="fa fa-minus"></i></button>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                        <?php } ?>
                        <?php } else { ?>
                        <div class="row">
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Order Reference </label>
                                    <input class="form-control hawb" data-number_of_record="0" name="hawb[0]" id="hawb_0" type="text" value="" placeholder="Order Reference" onkeyup="get_hawb_details(this)" >
                                </div>
                            </div>
                            <div class="col-md-2">
                                <label class="label-account">Reference Date </label>
                                <div class="input-group date date-picker margin-bottom-5" data-date-format="dd-mm-yyyy">
                                    <span class="input-group-btn">
                                        <button class="btn default" type="button"><i class="fa fa-calendar"></i></button>
                                    </span>
                                    <input type="text" class="form-control date_booked" readonly name="date_booked[0]" id="date_booked_0" placeholder="Reference Date" value="" >
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Reference </label>
                                    <input class="form-control reference" type="text" id="reference_0" name="reference[0]" value="" placeholder="Reference" />
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Invoiced Amount</label>
                                    <input class="form-control invoice_amount" id="charged_amount_0" name="charged_amount[0]" type="text" value="" placeholder="Invoiced Amount" onkeyup="calculateFields()" >
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Chargeable Amount</label>
                                    <input class="form-control chargeable_amount" id="actual_amount_0" name="actual_amount[0]" type="text" value="" placeholder="Chargeable Amount" onkeyup="calculateFields()" >
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Credit Amount</label>
                                    <input class="form-control credit_amount" id="total_amount_0" name="total_amount[0]" type="text" value="" placeholder="Credit Amount" readonly="readonly">
                                </div>
                            </div>
                            <div style="clear:both;">
                            </div>
                            <div class="col-md-9">
                                <div class="form-group">
                                    <label>Description</label>
                                    <input class="form-control description" name="description[0]" id="description_0" type="text" value="" placeholder="Description">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <input type="hidden" class="vat_amount" name="vat_amount[0]" id="vat_amount_0" value="0.00" >
                                <div class="input-group margin-top-30 float-left">
                                    <div class="icheck-inline">
                                        <label class="label-account">
                                            <input class="icheck is_vat" id="is_vat_0" name="is_vat[0]" type="checkbox" data-checkbox="icheckbox_flat-green" value="1" /> VAT Applicable
                                        </label>
                                    </div>
                                </div>  
                                <div class="margin-top-25 float-right">
                                    <button type="button" class="btn btn-success btn-sm add_more_credit_note_details">+</button>
                                    <button type="button" class="btn btn-danger btn-sm remove_credit_note_details initial-button">-</button>
                                </div>
                            </div>
                        </div>
                        <?php } ?>
                    </div>
                    <div id="message_box"> 
                        <div class="row">
                            <div class="col-md-12">
                                <div class="alert alert-danger">
                                    <p>Please search credit note detail first</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="portlet light" >
                <div class="portlet-title">
                    <div class="caption"> <i class="glyphicon glyphicon-briefcase"></i>Summary </div>
                </div>
                <div class="portlet-body">
                    <div>
                        <div class="row">
                            <div class="col-md-3">

                               
                                  <div class="form-group">
                                     <div class="has-float-label input-icon right">
                               <i class="fa  fa-anchor"></i> 
                                        <input class="form-control" name="net_amount" id="net_amount" type="text" value="" placeholder="Net Total" readonly="readonly" >
                                         <label>Net Total</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                               
                                <div class="form-group">
                                     <div class="has-float-label input-icon right">
                                        <i class="fa fa-bolt"></i> 
                                        <input class="form-control" name="total_vat" id="total_vat" type="text" value="" placeholder="VAT" readonly="readonly" >
                                         <label>VAT Total</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                              
                               <div class="form-group">
                                     <div class="has-float-label input-icon right"><i class="fa fa-building-o"></i> 
                                        <input class="form-control" name="grand_total_amount" id="grand_total_amount" type="text" value="" placeholder="Credit Total" readonly="readonly" >
                                          <label>Credit Total</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <input type="button" class="btn btn-success btn-sm" value="Save Changes" onclick="save_changes()" >
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <input type="hidden" name="elindex-hardcode" id="elindex-hardcode" value="<?php echo (count($this->creditNoteDetailObj) - 1); ?>" />
        </form>
        <?php
    }

    /**
     * Override to show the menu
     *
     */
    public function renderMenu() {
        $menu = new Adminmenu(Adminmenu::CURRENCY);
        $menu->render();
    }


    public function renderHead() {
        ?>

        <?php
    }

}

/* ------------------------------------------------------------------------------ */
// create and render page
$PageObj = new Page(CONFIG_TEMPLATE_ADMIN);
$PageObj->show();
?>
