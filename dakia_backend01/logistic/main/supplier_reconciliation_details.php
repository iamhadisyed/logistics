<?php
// get settings
require_once("../includes/settings/config.inc.php");
include_classes([
    'yodel.class'
], 'labels');
include_classes([
    'iaddress.class',
    'consignment.class',
    'consignmentfilter.class',
    'currency.class',
    'currencyfilter.class',
    'consignmentchargestypes.class',
    'consignmentchargestypesfilter.class' ,
    'consignmentcharges.class' ,
    'consignmentchargesfilter.class' ,
    'consignmentchargeslog.class' ,
    'consignmentchargeslogfilter.class' ,
    'services.class' ,
    'servicefilter.class' ,
    'paymentshistory.class' ,
    'paymentshistoryfilter.class' ,
    'csvassignpricetemplate.class' ,
    'csvassignpricetemplatefilter.class' ,
    'carrier.class' ,
    'carrierfilter.class' ,
    'parcel.class' ,
    'parcelfilter.class' ,
    'supplierinvoices.class',
    'supplierinvoicesfilter.class',
    'reconciliationdata.class',
    'reconciliationdatafilter.class',
    'settingsupplieremail.class',
    'settingsupplieremailfilter.class',
    'supplierinvoiceemailsdetails.class',
    'supplierinvoiceemailsdetailsfilter.class',
    'reconciliationbagdata.class',
    'reconciliationbagdatafilter.class'

]);
class Page extends BasePage {
    /*     * *
     * Controller logic
     */

    private $user = "";
    private $encodedInvoiceNumber = "";
    private $invoiceNumber = "";
    private $reconciliationDataObjs = [];
    private $csvColumn = [];
    private $supplierInvoiceId = 0;
    private $supplierInvoicesStatus = '';
    private $bagNumber = '';

    protected function init() {
        $this->breadCrumb['data'] = array(
            'index.php' => Translation::GetCaption("HOME"),
            "Assign price with csv upload"
        );

        $this->encodedInvoiceNumber = util_get("invoice_number");
        $this->invoiceNumber = base64_decode(util_get("invoice_number"));
        $this->bagNumber = base64_decode(util_get("bag_number"));
        if(!empty($this->encodedInvoiceNumber)) {
            $supplierInvoices = new SupplierInvoicesFilter();
            $supplierInvoices->where(['invoice_number' => $this->invoiceNumber], '=');
            $supplierInvoices = $supplierInvoices->getList();
            $this->supplierInvoiceId = $supplierInvoices[0]->getId();
            $this->supplierInvoicesStatus = $supplierInvoices[0]->getStatus();
        }

        $reconciliationDataFilter = new ReconciliationDataFilter();
        $reconciliationDataFilter->where(['invoice_number' => $this->invoiceNumber]);
        $this->reconciliationDataObjs = $reconciliationDataFilter->getList();

        $this->user = SessionManager::getUser();
        if (isset($_GET['func']) && $_GET['func'] == 'reconciliation_consignment_list') {
            $bagNumberPosted = util_get("bag_number_posted");
            $reconciliationDataFilter = new ReconciliationDataFilter();
            /*
             * Column filter
             * For search
             */
            if (isset($this->form_vars['action']) && $this->form_vars['action'] == 'filter') {
                $message = $this->form_vars['message'];
                if (!empty($message)) {
                    $reconciliationDataFilter->whereLike(['message' => $message]);
                }
                $awb = $this->form_vars['awb'];
                if (!empty($awb)) {
                    $reconciliationDataFilter->where(['awb' => $awb]);
                }
                $hawb = $this->form_vars['hawb'];
                if (!empty($hawb)) {
                    $reconciliationDataFilter->where(['hawb' => $hawb]);
                }
                $status = $this->form_vars['status'];
                if (!empty($status)) {
                    $reconciliationDataFilter->where(['rd.status' => $status]);
                }
                $bagNumber = $this->form_vars['bag_number'];
                if (!empty($bagNumber)) {
                    $reconciliationDataFilter->where(['bag_number' => $bagNumber]);
                }
                $serviceName = $this->form_vars['service_name'];
                if (!empty($serviceName)) {
                    $reconciliationDataFilter->whereLike(['service_name' => $serviceName]);
                    $reconciliationDataFilter->whereLike(['service_code' => $serviceName]);
                }
                $action = $this->form_vars['re_action'];
                if (!empty($action)) {
                    $reconciliationDataFilter->where(['action' => $action]);
                }

            }
            if(!empty($bagNumberPosted) && empty($bagNumber)){
                $reconciliationDataFilter->where(['bag_number' => $bagNumberPosted]);
            }
            $reconciliationDataFilter->where(['rd.invoice_number' => $this->invoiceNumber]);
            /*
             * Set columns orders for sorting
             */
            if (isset($this->form_vars['order'][0]['column']) && $this->form_vars['order'][0]['column'] != 0) {
                $dataTableColumnId = $this->form_vars['order'][0]['column'];
                $orderBy = $this->form_vars['order'][0]['dir'];
                $orderFalse = "ASC";
                if ($orderBy == 'desc') {
                    $orderFalse = 'DESC';
                }
                $dataTableColumnName = ucfirst($this->form_vars['columns'][$dataTableColumnId]['data']);

                if ($dataTableColumnName != "Active") {
                    $reconciliationDataFilter->orderBy(strtolower($dataTableColumnName), $orderFalse);
                }
            } else {
                $reconciliationDataFilter->orderBy(strtolower("rd.id"), "DESC");
            }
            /*
             * Pagination Logic Implemented
             *
             */
            $iDisplayLength = intval($_REQUEST['length']);
            $iDisplayLength = $iDisplayLength < 0 ? 20 : $iDisplayLength;
            $iDisplayStart = intval($_REQUEST['start']);
            $sEcho = intval($_REQUEST['draw']);
            $reconciliationDataFilter->setRowsPerPage($iDisplayLength);
            $reconciliationDataFilter->setOffset($iDisplayStart);
            $reconciliationDataFilterObjs = $reconciliationDataFilter->getList("rd.*");
            $iTotalRecords = $reconciliationDataFilter->getCount();
            $setDataArr = [];
            if(count($reconciliationDataFilterObjs)) {
                foreach($reconciliationDataFilterObjs as $reconciliationDataFilterObj) {
                    $serviceName = $reconciliationDataFilterObj->getServiceName();
                    $currentArr['message'] = $reconciliationDataFilterObj->getMessage();
                    $currentArr['awb'] = $reconciliationDataFilterObj->getAwb();
                    $currentArr['hawb'] = $reconciliationDataFilterObj->getHawb();
                    $currentArr['service_name'] = $serviceName;
                    $currentArr['weight'] = $reconciliationDataFilterObj->getWeight();
                    $currentArr['bag_number'] = $reconciliationDataFilterObj->getBagNumber();
                    $currentArr['total_amount'] = $reconciliationDataFilterObj->getTotalAmount() .' '.$reconciliationDataFilterObj->getCurrency();
                    $currentArr['dimension'] = $reconciliationDataFilterObj->getLength().'x'.$reconciliationDataFilterObj->getWidth().'x'.$reconciliationDataFilterObj->getHeight();
                    if($reconciliationDataFilterObj->getStatus() == "error") {
                        $currentArr['status'] = '<label class="btn btn-xs btn-danger">' . $reconciliationDataFilterObj->getStatus() . '</label>';
                    } else {
                        $currentArr['status'] = '<label class="btn btn-xs btn-success">' . $reconciliationDataFilterObj->getStatus() . '</label>';
                    }
                    $currentArr['action'] = ucwords(str_replace('_',' ', $reconciliationDataFilterObj->getAction()));
                    $action = '';
                    $action .= '<div class="action_check_box">';
                    $action .= '<label class="mt-checkbox mt-checkbox-single mt-checkbox-outline"><input type="checkbox" name="multi_select[]"  data-reconciliation_data_id="' . $reconciliationDataFilterObj->getId() . '" id="multi_select_' . $reconciliationDataFilterObj->getId() . '"  value="' . $reconciliationDataFilterObj->getId() . '"  class="group-checkable reconciliationDataIds" /><span></span></label>';
                    $action .= '</div>';
                    $currentArr['actions'] = $action;
                    $setDataArr [] = $currentArr;
                }
            }
            $setDataArrJson['data'] = $setDataArr;
            $setDataArrJson['draw'] = $sEcho;
            $setDataArrJson['recordsTotal'] = $iTotalRecords;
            $setDataArrJson['recordsFiltered'] = $iTotalRecords;
            echo json_encode($setDataArrJson);
            die;
        }

        if (isset($this->form_vars['action']) && $this->form_vars['action'] == 'update_selected_action') {
            $ids = explode(',',$this->form_vars['ids']);
            if(count($ids) > 0){
                $consignmentAction = $this->form_vars['consignment_action'];
                $reconciliationDataFilter = new ReconciliationDataFilter();
                $reconciliationDataFilter->set(['action' => $consignmentAction]);
                $reconciliationDataFilter->whereIn('id',$ids);
                $reconciliationDataFilter->update();

                // Check if all parcels are approved then change the bag status
                $reconciliationDataFilterNew = new ReconciliationDataFilter();
                $reconciliationDataFilterNew->whereIn('id',$ids);
                $reconciliationDataFilterData = $reconciliationDataFilterNew->getList("id,invoice_number,bag_number");
                if(count($reconciliationDataFilterData) > 0){
                    foreach ($reconciliationDataFilterData as $reconciliationDataFilterArr) {
                        $reconciliationBagDataFilter = New ReconciliationBagDataFilter();
                        $reconciliationBagDataFilter->addFieldFilter("    invoice_number",$reconciliationDataFilterArr->getInvoiceNumber());
                        $reconciliationBagDataFilter->addFieldFilter("    bag_number",$reconciliationDataFilterArr->getBagNumber());
                        $reconciliationBagDataObj = $reconciliationBagDataFilter->getColumnList("*");
                        if(count($reconciliationBagDataObj) > 0){
                            if($reconciliationBagDataObj[0]->getStatus() != "success"){
                                // First get all parcel
                                $reconciliationDataFilterObj = new ReconciliationDataFilter();
                                $reconciliationDataFilterObj->where(['bag_number' => $reconciliationDataFilterArr->getBagNumber()]);
                                $reconciliationDataFilterObj->where(['status' =>"success"]);
                                $reconciliationDataFilterObj->orWhere(['action' =>"approved"]);
                                $reconciliationDataFilterObj->orWhere(['action' =>"manually_approved"]);
                                $reconciliationDataFilterObj->orWhere(['action' =>"credit_note_received"]);
                                $reconciliationDataFilterObj = $reconciliationDataFilterNew->getList("*");
                                $approvedPieces = count($reconciliationDataFilterObj);
                                if(count($reconciliationDataFilterObj) == $reconciliationBagDataObj[0]->getMatchedPiece()){
                                    $reconciliationBagData = New ReconciliationBagData($reconciliationBagDataObj[0]->getId());
                                    $reconciliationBagData->setParcelStatus("success");
                                    $reconciliationBagData->save();
                                }



//                                // Check if all clear then update Bag status
//                                if($reconciliationBagDataObj[0]->getNumberOfParcelStatus() == "match" && $reconciliationBagDataObj[0]->getWeightStatus() == "match" && $reconciliationBagDataObj[0]->getParcelStatus() == "success"){
//                                    // Update the bag status
//                                    $reconciliationBagData = New ReconciliationBagData($reconciliationBagDataObj[0]->getId());
//                                    $reconciliationBagData->setStatus("success");
//                                    $reconciliationBagData->save();
//                                }
                            }
                        }
                    }
                }
                $output['status'] = 'success';
                $output['message'] = 'Consignment action is updated successfully';
            }else{
                $output['status'] = 'error';
                $output['message'] = 'Consignment update issue, Please check bag status';
            }
            echo json_encode($output);
            die;
        } else if (isset($this->form_vars['action']) && $this->form_vars['action'] == 'get_agent_emails_and_data_details') {
            
            $ids = explode(',',$this->form_vars['ids']);
            $consignmentAction = $this->form_vars['consignment_action'];
            $reconciliationDataFilter = new ReconciliationDataFilter();
            $reconciliationDataFilter->whereNotIn('rd.status',['success','processed']);
            $reconciliationDataFilter->innerJoin("supplier_invoices si", "si.id = rd.supplier_invoice_id");
            $reconciliationDataFilter->whereIn('rd.id',$ids);
            $reconConcileData   =   $reconciliationDataFilter->getList("rd.*,si.carrier_id,si.carrier_type ");
             $iTotalRecords = $reconciliationDataFilter->getCount(true);
            
            if(count($iTotalRecords)>0){
                $dataDetailHtml =   ""
                        . "Dear Sir/Madam
                            
"
                            ."We would like to query about below shipment.Either shipment don't belong to us or it has incorrect charges
                            
"
                            ;
                $dataErrorCode  =   ["not_found"=>"Shipment Not Found:  ",
                                "charges_issue"=>"Shipment has incorrect charges:   ",
                                "service_issue"=>"Shipment service incorrect:   ",
                                "weight_issue"=>"Shipment weight issue: ",
                                "item_issue"=>"Incorrect number of shipment charged: ",
                                "duplicate_shipment"=>"Shipment already invoiced"
                                    ];
                $carrier_type = 'carrier';
                $queriesId = [];
                foreach($reconConcileData as $itemData){
                    $carrier_type = $itemData->getCarrierType();
                    $carrier_id = $itemData->getCarrierId();
                    $queriesId[] = $itemData->getId();
                    $supplier_invoice_id = $itemData->getSupplierInvoiceId();
                    $invoice_number = $itemData->getInvoiceNumber();
                   $dataDetailHtml  .=  
                            $dataErrorCode[$itemData->getMessageCode()]
                            ."Invoice No. ".$itemData->getInvoiceNumber()
                            ." Shipment Tracking No. ".$itemData->getAwb()
                            ." Service Name ".$itemData->getServiceName()
                            ." Service Code ".$itemData->getServiceCode()
                            ." Weight ".$itemData->getWeight()
                            ." Basic Charge ".$itemData->getBasicCharges()
                            ." Fuel Charge ".$itemData->getFuelCharges()
                            ." TAX ".$itemData->getVat()
                            ." Total Charge ".$itemData->getTotalAmount()
                            ."
                            
                            
";
                   
                }
                $dataDetailHtml  .=  
                            "
                            
"
                            ."Thanks
"
                            . " Smart Track ";
                $settingSupplierEmailFilter = new SettingSupplierEmailFilter();
                $settingSupplierEmailFilter->set([
                                'supplier_id'   => $carrier_id,
                                'supplier_type' => $carrier_type,
                                'account_id'    => $this->user->getUserAccountId()
                            ]);
                $settingSupplierEmailData   =   $settingSupplierEmailFilter->getList();
                $financeEmailsTo = '';
                $financeEmailsCc = '';
                if(count($settingSupplierEmailData)>0){
                    $settingSupplier = $settingSupplierEmailData[0];
                    $financeEmailsTo = $settingSupplier->getFinanceEmailsTo();
                    $financeEmailsCc = $settingSupplier->getFinanceEmailsCc();
                    
                }
                $output['status'] = 'success';
                $output['message'] = '';
                $output['data']['htmlstring'] = $dataDetailHtml;
                $output['data']['subject'] = "Query against invoice number: ".$invoice_number;
                $output['data']['emailsto'] = $financeEmailsTo;
                $output['data']['emailscc'] = $financeEmailsCc;
                $output['data']['supplier_invoice_id'] = $supplier_invoice_id;
                $output['data']['email_supplier_invoice_items_id'] = $queriesId;
                $output['data']['email_supplier_id'] = $carrier_id;
                $output['data']['email_supplier_type'] = $carrier_type;
                
            } else {
                $output['status'] = 'error';
                $output['message'] = 'Please check you selection entry, there is something wrong either entries already send to supplier or approved successfully.';
            }
            
            echo json_encode($output);
            die;
        }else if (isset($this->form_vars['action']) && $this->form_vars['action'] == 'make_query_with_supplier'){
            //echo "<pre>";
            //print_r($_POST);
            extract($this->form_vars);
            $errorData  =   '';
            $settingSupplierFilter   =   new SettingSupplierEmailFilter();
            $settingSupplierFilter->where([
                    'supplier_id'=> $email_supplier_id, 
                    'supplier_type'=>$email_supplier_type, 
                    'account_id'=> $this->user->getUserAccountId()]);
            $settingSupplierEmailData   =   $settingSupplierFilter->getList();
            $emailcountData =   $settingSupplierFilter->getCount(true);
            if($emailcountData >0)
                $settingsupplieremail =   $settingSupplierEmailData[0];
            else
                $settingsupplieremail   =   new SettingSupplierEmail();
            $settingsupplieremail->setSupplierId($email_supplier_id);
            $settingsupplieremail->setSupplierType($email_supplier_type);
            $settingsupplieremail->setAccountId($this->user->getUserAccountId());
            $settingsupplieremail->setAddedBy($this->user->getId());
            #$settingsupplieremail->setAddedDate();
            $settingsupplieremail->setFinanceEmailsTo($send_inv_email_to);
            $settingsupplieremail->setfinanceEmailsCc($send_inv_email_cc);
            if($settingsupplieremail->save()){
                $supplierinvoiceemailsdetails   =   new SupplierInvoiceEmailsDetails();
                $supplierinvoiceemailsdetails->setSupplierInvoiceId($email_supplier_invoice_id);
                $supplierinvoiceemailsdetails->setEmailTo($send_inv_email_to);
                $supplierinvoiceemailsdetails->setEmailCc($send_inv_email_cc);
                $supplierinvoiceemailsdetails->setEmailSubject($send_inv_email_subject);
                $supplierinvoiceemailsdetails->setEmailBody($send_inv_email_message);
                $supplierinvoiceemailsdetails->setAddedBy($this->user->getId());
                $supplierinvoiceemailsdetails->setSupplierInvoiceEntityId($email_supplier_invoice_items_id);
                if($supplierinvoiceemailsdetails->save()){
                    $mailObj = new SendEmail();
                    $sendTo = $send_inv_email_tol;
                    $sendSubject = $send_inv_email_subject;
                    $sendMessage = $send_inv_email_message;
                    $sendCc     =   $send_inv_email_cc;
                    $output = [];
                    $email_response = $mailObj->autoInvoiceEmailSend($sendTo, $sendSubject, $sendMessage, $sendCc);
                    //if((isset($email_response['status'])) && ($email_response['status'] == "success")) {
                        $ids = explode(',',$email_supplier_invoice_items_id);
                        $reconciliationDataFilter = new ReconciliationDataFilter();
                        $reconciliationDataFilter->set(['action' => 'query_with_supplier']);
                        $reconciliationDataFilter->whereIn('id',$ids);
                        $reconciliationDataFilter->update();
                        $output['status'] = "success";
                        $output['message'] = "Email successfully send to supplier.";
                        echo json_encode($output);
                        die;
                    //} else {
                      //  $errorData = "Supplier email cannot be send, Please contact to administrator";
                    //}
                } else{
                 // ERROR FOR EMAIL LOGINGN   
                    $errorData = "Supplier email cannot be logged, Please contact to administrator";
                    
        }
            } else {
                //  ERROR FOR SETTING SUPPLIER EMAILS 
                $errorData = "Supplier setting email cannot be logged, Please contact to administrator";
        
            }
            if(trim($errorData)!=''){
                $output['status'] = "error";
                $output['message'] = $errorData;
            }
            //$this->flashMsg->success($output['email_response']['message']);
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
        <link rel="stylesheet" type="text/css" href="../assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/select2/css/select2-bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/bootstrap-sweetalert/sweetalert.css" rel="stylesheet" type="text/css" />

        <link href="../assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/bootstrap-select/css/bootstrap-select.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/jquery-nestable/jquery.nestable.css" rel="stylesheet" type="text/css" />
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
        <script src="../assets/global/plugins/jquery-nestable/jquery.nestable.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/bootstrap-sweetalert/sweetalert.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/icheck/icheck.min.js" type="text/javascript"></script>
        <script src="../assets/pages/scripts/flipclock.min.js"></script>

        <script src="../assets/pages/scripts/components-date-time-pickers.min.js" type="text/javascript"></script>
        <script src="../assets/pages/scripts/ui-confirmations.min.js" type="text/javascript"></script>
        <script type="text/javascript">
            var urlPage = 'supplier_reconciliation_details.php';
            var grid = null;
            var DataTableFun = function () {
                var handleDataTable = function () {
                    grid = new Datatable();
                    grid.init({
                        src: $("#reconciliation_consignment_datatable"),
                        onSuccess: function (grid) {
                            // execute some code after table records loaded
                        },
                        onError: function (grid) {
                            // execute some code on network or other general error
                        },
                        dataTable: {// here you can define a typical datatable settings from http://datatables.net/usage/options
                            "lengthMenu": [
                                [20, 50, 100, 150],
                                [20, 50, 100, 150] // change per page values here
                            ],
                            "pageLength": 20, // default record count per page
                            "ajax": {
                                "url": urlPage+'?func=reconciliation_consignment_list&invoice_number=<?php echo $this->encodedInvoiceNumber ?>&bag_number_posted=<?php echo $this->bagNumber; ?>', // ajax source
                                headers: {

                                },
                            },
                            "bStateSave": true,
                            "columns": [
                                {"data": "actions", "bSortable": false},
                                {"data": "message"},
                                {"data": "hawb"},
                                {"data": "awb"},
                                {"data": "service_name"},
                                {"data": "weight"},
                                {"data": "bag_number"},
                                {"data": "total_amount"},
                                {"data": "dimension","bSortable": false},
                                {"data": "status","bSortable": false},
                                {"data": "action","bSortable": false},
                            ]
                        }
                    });
                }
                return {
                    init: function () {
                        handleDataTable();
                    }
                };
            }();
            $(document).ready(function () {
                $(document).on('click','.reprocess_file',function(){
                    var id = $(this).data('id');
                    var form_data = new FormData();
                    form_data.append('id', id);
                    form_data.append('func', 'reprocess_file');
                    $.ajax({
                        url: 'supplier_reconciliation.php',
                        dataType: 'json',
                        cache: false,
                        contentType: false,
                        processData: false,
                        data: form_data,
                        type: 'post',
                        success: function (response) {
                            if(response.status == "success") {
                                grid.getDataTable().ajax.reload();
                                swal("Success!", response.message, "success");
                            } else {
                                swal("Sorry!", response.message, "error");
                            }
                        }
                    });
                });

                $(document).ajaxStart($.blockUI).ajaxStop($.unblockUI);
                DataTableFun.init();
                if ($('.date-picker').length > 0) {
                    //init date pickers
                    $('.date-picker').datepicker({
                        autoclose: true
                    });
                }
                $(document).on('click','#query_with_supplier_action_btn',function(){
                    var form_data = new FormData($( "#form_send_query_to_supplier" )[0]);
                        form_data.append('action', 'make_query_with_supplier');
                      console.log( form_data );
                    $.ajax({                                    
                                    type: "POST",
                                    url: urlPage,
                                    data: form_data,
                                    dataType: "json",
                                    cache: false,
                                    contentType: false,
                                    processData: false,
                                    success: function (data) {
                                        //grid.getDataTable().ajax.reload();
                                        if(data.status == 'success'){
                                            swal("Success", data.message, "success");
                                            grid.getDataTable().ajax.reload();
                                            $('#send_sale_auto_invoice_email_modal').modal('hide');                               
                                            //$('#send_sale_auto_invoice_email_modal').modal('gide');
                                        } else {
                                            swal("Error!", data.message, "error");
                                        }
                        
                                    },
                                    error: function () {
                                        //alert('error handing here');
                    }
                                });
                                
                });
                $(document).on('click','#reconciliation_action_btn',function(){
                    var ids = [];
                    $('.reconciliationDataIds:checked').map(function () {
                        ids.push(this.value);
                    }).get();
                    if (typeof ids !== 'undefined' && ids.length > 0) {
                        
                        var action = $('#reconciliation_action').val();
                        
                        if(action != "") {
                            var form_data = new FormData();
                            form_data.append('ids', ids);
                            form_data.append('consignment_action', action);
                            
                            if(action =='query_with_supplier'){
                                form_data.append('action', 'get_agent_emails_and_data_details');
                                $.ajax({                                    
                                    type: "POST",
                                    url: urlPage,
                                    data: form_data,
                                    dataType: "json",
                                    cache: false,
                                    contentType: false,
                                    processData: false,
                                    success: function (data) {
                                        //grid.getDataTable().ajax.reload();
                                        if(data.status == 'success'){
                                            $('#send_inv_email_to').val(data.data.emailsto);
                                            $('#send_inv_email_cc').val(data.data.emailscc);
                                          //  $('#send_inv_email_to').val(data.data.emails);
                                            $('#send_inv_email_subject').val(data.data.subject);
                                            $('#send_inv_email_invoice_number').val(data.data.supplier_invoice_id);
                                            $('#email_supplier_id').val(data.data.email_supplier_id);
                                            $('#email_supplier_type').val(data.data.email_supplier_type);
                                            $('#email_supplier_invoice_items_id').val(data.data.email_supplier_invoice_items_id);
                                            $('#email_supplier_invoice_id').val(data.data.supplier_invoice_id);
                                            
                                            $('#send_inv_email_message').val(data.data.htmlstring);
                                            
                                           // $('#send_inv_email_invoice_number').val(invoice_id);

                                            $('#send_sale_auto_invoice_email_modal').modal('show');
                                        } else {
                                            swal("Error!", data.message, "error");
                                        }
                                        
                                    },
                                    error: function () {
                                        //alert('error handing here');
                                    }
                                });
                            } else {
                                form_data.append('action', 'update_selected_action');
                                $.ajax({
                                    type: "POST",
                                    url: urlPage,
                                    data: form_data,
                                    dataType: "json",
                                    cache: false,
                                    contentType: false,
                                    processData: false,
                                    success: function (data) {
                                        if(data.status == "success"){
                                            grid.getDataTable().ajax.reload();
                                            swal("Success!", data.message, "success");
                                        }else{
                                            swal("Error!", data.message, "error");
                                        }
                                    },
                                    error: function () {
                                        //alert('error handing here');
                                    }
                                });
                            }
                        } else {
                            swal("Sorry!", "Please select action", "error");
                        }
                    } else {
                        swal("Sorry!", "Please check the checkbox for action", "error");
                    }
                });
                $(document).on('change','#status',function () {
                    $(".filter-submit").click();
                });
                $(document).on('change','#re_action',function () {
                    $(".filter-submit").click();
                });
                $(document).on('keypress',function(e) {
                    if(e.which == 13) {
                        $(".filter-submit").click();
                    }
                });
            });
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
                    Supplier Reconciliation Details List (<?php echo $this->invoiceNumber ?>)
                </div>
                <div class="actions">
                    <?php if($this->supplierInvoicesStatus != "approved") {?>
                    <a title="Re process" href="javascript:;" class="btn blue margin-bottom-5 reprocess_file" data-id="<?php echo $this->supplierInvoiceId; ?>">
                        <span class="glyphicon glyphicon-ok"></span> Re Process
                    </a>
                </div>
            <?php } ?>
            </div>
            <div class="portlet-body">
                <div class="row">
                    <div class="col-sm-12">
                        <?php
                        $this->flashMsg->display();
                        ?>
                    </div>
                </div>
                <div class="table-container">
                    <div class="table-actions-wrapper">
                        <span> </span>
                        <select class="table-group-action-input form-control input-inline input-small input-sm" id="reconciliation_action" name="reconciliation_action" >
                            <option value="">Select Action</option>
                            <option value="manually_approved">Manually Approved</option>
                            <option value="query_with_supplier">Query With Supplier</option>
                            <option value="credit_note_received">Credit Note Received</option>
                        </select>
                        <button class="btn btn-sm btn-default table-group-action-submit" type="button" id="reconciliation_action_btn" >
                            <i class="fa fa-check"></i> Submit
                        </button>
                    </div>
                    <table class="table table-striped table-bordered table-hover table-condensed" id="reconciliation_consignment_datatable">
                        <thead>
                        <tr role="row" class="heading">
                            <th>Actions</th>
                            <th>Message</th>
                            <th>Hawb</th>
                            <th>Awb</th>
                            <th>Service</th>
                            <th>Weight</th>
                            <th>Bag Number</th>
                            <th>Amount</th>
                            <th>Dimension</th>
                            <th>Status</th>
                            <th>Consignment action</th>
                        </tr>
                        <tr role="row" class="filter">
                            <td>
                                <div class="margin-bottom-5">
                                    <button class="btn btn-xs blue filter-submit btn-outline" ><i class="fa fa-search"></i> </button>
                                    <button class="btn btn-xs red filter-cancel mt-ladda-btn ladda-button btn-outline"><i class="fa fa-times"></i> </button>
                                </div>
                                <label class="mt-checkbox mt-checkbox-single mt-checkbox-outline">
                                    <input type='checkbox' name='checkall' class="group-checkable"/>
                                    <span></span>
                                </label>
                            </td>
                            <td>
                                <input type="text" class="form-control form-filter input-sm " name="message" id ="message" />
                            </td>
                            <td>
                                <input type="text" class="form-control form-filter input-sm " name="hawb" id ="hawb" />
                            </td>
                            <td>
                                <input type="text" class="form-control form-filter input-sm " name="awb" id ="awb" />
                            </td>
                            <td>
                                <input type="text" class="form-control form-filter input-sm " name="service_name" id ="service_name" />
                            </td>
                            <td></td>
                            <td><input type="text" value="<?php if(!empty($_GET['bag_number'])){ echo base64_decode($_GET['bag_number']); } ?>" class="form-control form-filter input-sm " name="bag_number" id ="bag_number" /></td>
                            <td></td>
                            <td></td>
                            <td>
                                <select class="form-control form-filter input-sm" name="status" id ="status">
                                    <option value="">Select</option>
                                    <option value="success">Success</option>
                                    <option value="error">Error</option>
                                </select>
                            </td>
                            <td>
                                <select class="form-control form-filter input-sm" name="re_action" id ="re_action">
                                    <option value="">Select</option>
                                    <option value="approved">Approved</option>
                                    <option value="manually_approved">Manually Approved</option>
                                    <option value="query_with_supplier">Query with Supplier</option>
                                    <option value="credit_note_received">Credit Note Received</option>
                                </select>
                            </td>
                        </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <div class="modal fade" tabindex="-1" role="dialog" id="send_sale_auto_invoice_email_modal" >
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">Send Email</h4>
                    </div>
                    <form method="post" action="invoice_list.php?action=send_email_auto_invoice" id="form_send_query_to_supplier" enctype="multipart/form-data">
                    <div class="modal-body">
                        
                            <input type="hidden" id="send_inv_email_invoice_number" name="invoice_id" />
                            <div class="row">
                                <div class="col-md-6">
                                    <label class="label-account">To</label>
                                    <div class="form-group">
                                        <div class="input-group"> <span class="input-group-addon"> <i class="fa fa-star-o"></i> </span>
                                            <input class="form-control form-filter" id="send_inv_email_to" name="send_inv_email_to" type="text" placeholder="To" value="" rel="tooltip" data-original-title="To">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="label-account">CC</label>
                                    <div class="form-group">
                                        <div class="input-group"> <span class="input-group-addon"> <i class="fa fa-star-o"></i> </span>
                                            <input class="form-control form-filter" id="send_inv_email_cc" name="send_inv_email_cc" type="text" placeholder="CC" value="finance@oneworldexpress.com" rel="tooltip" data-original-title="CC">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <label class="label-account">Subject</label>
                                    <div class="form-group">
                                        <div class="input-group"> <span class="input-group-addon"> <i class="fa fa-star-o"></i> </span>
                                            <input class="form-control form-filter" id="send_inv_email_subject" name="send_inv_email_subject" type="text" placeholder="Subject" value="" rel="tooltip" data-original-title="Subject">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php
                                $texteraData = "Dear ALFONSO, &#13;&#10; &#13;&#10; Please see below links for the invoice number INV-61215 date 31 August 2018 back-up data. &#13;&#10;&#13;&#10; Your Invoice &#13;&#10; &#13;&#10; link is here &#13;&#10; &#13;&#10;  #INVOICEDATALINK# &#13;&#10; &#13;&#10; if you have any queries, please contect us at finace@oneworldexpress.com";
                            ?>
                            <div class="row">
                                <div class="col-md-12">
                                    <label class="label-account">Message</label>
                                    <div class="form-group">
                                        <div class="input-group"> <span class="input-group-addon"> <i class="fa fa-envelope-o"></i> </span>
                                            <textarea class="form-control" rows="12" name="send_inv_email_message" id="send_inv_email_message" ></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                        
                    </div>
                    <div class="modal-footer"> 
                        <input type="hidden" id="email_supplier_id" name="email_supplier_id" value="" >
                        <input type="hidden" id="email_supplier_type" name="email_supplier_type" value="" >
                        <input type="hidden" id="email_supplier_invoice_id" name="email_supplier_invoice_id" value="" >
                        <input type="hidden" id="email_supplier_invoice_items_id" name="email_supplier_invoice_items_id" value="" >
                        <input type="button" id="query_with_supplier_action_btn" value="send" id="query_with_supplier_action_btn" class="btn btn btn-success" >
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                    </form>
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
        $menu = new Adminmenu(Adminmenu::CUSTOMERS);
        $menu->render();
    }

    public function renderHead() {
        ?>
        <style type="text/css">
            .dataTables_extended_wrapper .table.dataTable {
                margin: 0px 0px !important;
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