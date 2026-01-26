<?php
// get settings
require_once("../includes/settings/config.inc.php");
include_classes([
                    'reconciliationdata.class',
                    'reconciliationdatafilter.class',
                    'reconciliationbagdata.class',
                    'reconciliationbagdatafilter.class',
                    'settingsupplieremail.class',
                    'settingsupplieremailfilter.class',
                    'supplierinvoiceemailsdetails.class',
                    'supplierinvoiceemailsdetailsfilter.class',
                    'supplierinvoices.class',
                    'supplierinvoicesfilter.class',
                ]);

class Page extends BasePage {
    /* 
     * Controller logic
     */
    
    private $reconciliationbagdata = array();
    private $user = '';
    private $heading = '';
    private $inv = '';

    protected function init() {
        $this->heading = "Reconciliation Bag List";
        $this->breadCrumb['data'] = array(
            'index.php' => Translation::GetCaption("HOME"),
            $this->heading
        );
        $this->user = SessionManager::getUser();
        $this->inv = trim(base64_decode($_GET['invoice_number']));
        if (isset($this->form_vars['action']) && $this->form_vars['action'] == 'update_selected_action') {
            $ids = explode(',',$this->form_vars['ids']);
            $reconciliationBagDataFilter = New ReconciliationBagDataFilter();
            $reconciliationBagDataFilter->addFilterIn("    rbd.id",$ids);
            $reconciliationBagDataObj = $reconciliationBagDataFilter->getColumnList("rbd.bag_number");
            $allClear = false;
            if(count($reconciliationBagDataObj) > 0){
                foreach ($reconciliationBagDataObj as $reconciliationBagDataArr) {
                    $reconciliationDataFilter = new ReconciliationDataFilter();
                    $reconciliationDataFilter->addFilter(['bag_number' => $reconciliationBagDataArr->getBagNumber()]);
                    $reconciliationDataFilter->addFilter(" (`action` = 'query_with_supplier' OR `action` IS NULL )");
                    $reconciliationDataObj = $reconciliationDataFilter->getList("*");
                    if(count($reconciliationDataObj) > 0){
                        unset($ids[array_search($reconciliationBagDataArr->getId(),$ids)]);
                    }
                }
            }
            if(count($ids) > 0){
                $consignmentAction = $this->form_vars['consignment_action'];
                $reconciliationDataFilterNew = new ReconciliationBagDataFilter();
                $reconciliationDataFilterNew->addFilterIn('id',$ids);
                $reconciliationDataFilterNew->update("SET action = '".$consignmentAction."'");
                $output['status'] = 'success';
                $output['message'] = 'Bag action is updated successfully';
            }else{
                $output['status'] = 'error';
                $output['message'] = 'Bag update issue';
            }
            echo json_encode($output);
            die;
        }
        else if (isset($this->form_vars['action']) && $this->form_vars['action'] == 'get_agent_emails_and_data_details') {
            $financeEmailsTo = "";
            $financeEmailsCc = "";
            $carrier_id = "";
            $carrier_type = "";
            $queriesId = [];
            $supplier_invoice_id = 0;
            $bagIdsStr = "";
            $this->user = SessionManager::getUser();
            $invNumber = $this->form_vars['invoice_number'];
            $ids = explode(',',$this->form_vars['ids']);
            $consignmentAction = $this->form_vars['consignment_action'];
            // Get carrier Id and Carrier Type first to fetch the from , to email contacts
            $supplierInvoicesFilter = New SupplierInvoicesFilter();
            $supplierInvoicesFilter->where(['invoice_number'=>$invNumber]);
            $supplierInvoicesObj = $supplierInvoicesFilter->getList("id,carrier_id,carrier_type");
            $settingSupplierEmailObj = NULL;
            if(count($supplierInvoicesObj) > 0){
                $carrier_id = $supplierInvoicesObj[0]->getCarrierId();
                $carrier_type = $supplierInvoicesObj[0]->getCarrierType();
                $settingSupplierEmailFilter = New SettingSupplierEmailFilter();
                $settingSupplierEmailFilter->where(['supplier_id'=>$supplierInvoicesObj[0]->getCarrierId(),'supplier_type'=>$supplierInvoicesObj[0]->getCarrierType()]);
                $settingSupplierEmailObj = $settingSupplierEmailFilter->getList("*");
                if(count($settingSupplierEmailObj) > 0) {
                    $financeEmailsTo = $settingSupplierEmailObj[0]->getFinanceEmailsTo();
                    $financeEmailsCc = $settingSupplierEmailObj[0]->getFinanceEmailsCc();
                }
            }

            $reconciliationBagDataFilter = New ReconciliationBagDataFilter();
            $reconciliationBagDataFilter->addFilterIn("    id",$ids);
            $reconciliationBagDataFilter->addFieldFilter("    status","error");
            $reconciliationBagDataObj = $reconciliationBagDataFilter->getColumnList("*");
            $dataDetailHtml = "";
            if(count($reconciliationBagDataObj) > 0){
                $dataDetailHtml =   ""
                    . "Dear Sir/Madam
                            
"
                    ."We would like to query about below docket/Bag. Either docket/Bag don't belong to us or it has incorrect charges
                            
";
                foreach ($reconciliationBagDataObj as $reconciliationBagDataArr) {
                    $bagIdsStr .= $reconciliationBagDataArr->getId().",";
                    $queriesId[] = $reconciliationBagDataArr->getId();
                    $dataDetailHtml .= "
                    Invoice No. ".$reconciliationBagDataArr->getInvoiceNumber(). " Docket/Bag Number: ".$reconciliationBagDataArr->getBagNumber()." having weight ".$reconciliationBagDataArr->getWeight()." and number of pieces ".$reconciliationBagDataArr->getnumberOfPiece();
                    $bagInvoiceArr[$reconciliationBagDataArr->getInvoiceNumber()][$reconciliationBagDataArr->getId()] = $reconciliationBagDataArr->getBagNumber();
                    $dataDetailHtml .= "
                    ";
                }
                $dataDetailHtml  .=
                    "
                            
"
                    ."Thanks
"
                    . " Smart Track ";
            }else {
                $output['status'] = 'error';
                $output['message'] = 'Please check you selection entry, there is something wrong either entries already send to supplier or approved successfully.';
            }
            $bagSupplierInvIds = [];
            if(count($bagInvoiceArr) > 0){
                foreach ($bagInvoiceArr as $inv => $bagData) {
                    foreach ($bagData as $bagId => $bagNumber) {
                        $reconciliationDataFilter = New ReconciliationDataFilter();
                        $reconciliationDataFilter->where(['invoice_number'=>$inv]);
                        $reconciliationDataObj = $reconciliationDataFilter->getList('supplier_invoice_id');
                        if(count($reconciliationDataObj) > 0){
                            $supplier_invoice_id = $reconciliationDataObj[0]->getSupplierInvoiceId();
                        }
                    }
                }
            }
////            $reconciliation = New
//            $reconciliationBagDataFilter->addJoin("supplier_invoices si","");
//            $reconciliationDataFilter = new ReconciliationDataFilter();
//            $reconciliationDataFilter->whereNotIn('rd.status',['success','processed']);
//            $reconciliationDataFilter->innerJoin("supplier_invoices si", "si.id = rd.supplier_invoice_id");
//            $reconciliationDataFilter->whereIn('rd.id',$ids);
//            $reconConcileData   =   $reconciliationDataFilter->getList("rd.*,si.carrier_id,si.carrier_type ");
//            $iTotalRecords = $reconciliationDataFilter->getCount(true);

//            if(count($iTotalRecords)>0){
//                $dataDetailHtml =   ""
//                    . "Dear Sir/Madam
//
//"
//                    ."We would like to query about below docket/Bag.Either docket/Bag don't belong to us or it has incorrect charges
//
//"
//                ;
//                $dataErrorCode  =   ["not_found"=>"docket/Bag Not Found:  ",
//                    "charges_issue"=>"Shipment has incorrect charges:   ",
//                    "service_issue"=>"Shipment service incorrect:   ",
//                    "weight_issue"=>"Shipment weight issue: ",
//                    "item_issue"=>"Incorrect number of shipment charged: ",
//                    "duplicate_shipment"=>"Shipment already invoiced"
//                ];
//                $carrier_type = 'carrier';
//                $queriesId = [];
//                foreach($reconConcileData as $itemData){
//                    $carrier_type = $itemData->getCarrierType();
//                    $carrier_id = $itemData->getCarrierId();
//                    $queriesId[] = $itemData->getId();
//                    $supplier_invoice_id = $itemData->getSupplierInvoiceId();
//                    $invoice_number = $itemData->getInvoiceNumber();
//                    $dataDetailHtml  .=
//                        $dataErrorCode[$itemData->getMessageCode()]
//                        ."Invoice No. ".$itemData->getInvoiceNumber()
//                        ." Shipment Tracking No. ".$itemData->getAwb()
//                        ." Service Name ".$itemData->getServiceName()
//                        ." Service Code ".$itemData->getServiceCode()
//                        ." Weight ".$itemData->getWeight()
//                        ." Basic Charge ".$itemData->getBasicCharges()
//                        ." Fuel Charge ".$itemData->getFuelCharges()
//                        ." TAX ".$itemData->getVat()
//                        ." Total Charge ".$itemData->getTotalAmount()
//                        ."
//
//
//";
//
//                }
//                $dataDetailHtml  .=
//                    "
//
//"
//                    ."Thanks
//"
//                    . " Smart Track ";
//                $settingSupplierEmailFilter = new SettingSupplierEmailFilter();
//                $settingSupplierEmailFilter->set([
//                    'supplier_id'   => $carrier_id,
//                    'supplier_type' => $carrier_type,
//                    'account_id'    => $this->user->getUserAccountId()
//                ]);
//                $settingSupplierEmailData   =   $settingSupplierEmailFilter->getList();
//                $financeEmailsTo = '';
//                $financeEmailsCc = '';
//                if(count($settingSupplierEmailData)>0){
//                    $settingSupplier = $settingSupplierEmailData[0];
//                    $financeEmailsTo = $settingSupplier->getFinanceEmailsTo();
//                    $financeEmailsCc = $settingSupplier->getFinanceEmailsCc();
//
//                }
                $output['status'] = 'success';
                $output['message'] = '';
                $output['data']['htmlstring'] = $dataDetailHtml;
                $output['data']['subject'] = "Query against invoice number: ".$invNumber;
                $output['data']['emailsto'] = $financeEmailsTo;
                $output['data']['emailscc'] = $financeEmailsCc;
                $output['data']['supplier_invoice_id'] = $supplier_invoice_id;
                $output['data']['email_supplier_invoice_items_id'] = $queriesId;
                $output['data']['email_supplier_id'] = $carrier_id;
                $output['data']['email_supplier_type'] = $carrier_type;
                $output['data']['bag_id'] = $bagIdsStr;
//
//            } else {
//                $output['status'] = 'error';
//                $output['message'] = 'Please check you selection entry, there is something wrong either entries already send to supplier or approved successfully.';
//            }

            echo json_encode($output);
            die;
        }
        else if (isset($this->form_vars['action']) && $this->form_vars['action'] == 'make_query_with_supplier'){
            $this->user = SessionManager::getUser();
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
                $supplierinvoiceemailsdetails->setSupplierBagId($email_supplier_invoice_items_id);
                $supplierinvoiceemailsdetails->setEmailTo($send_inv_email_to);
                $supplierinvoiceemailsdetails->setEmailCc($send_inv_email_cc);
                $supplierinvoiceemailsdetails->setEmailSubject($send_inv_email_subject);
                $supplierinvoiceemailsdetails->setEmailBody($send_inv_email_message);
                $supplierinvoiceemailsdetails->setAddedBy($this->user->getId());
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
                   $reconciliationBagDataFilter = New ReconciliationBagDataFilter();
                   $reconciliationBagDataFilter->addFilterIn("    id",$ids);
                    $reconciliationBagDataFilter->update(" SET   action = 'query_with_supplier' ");
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
        /*
         * DataTable handlings
         */
        
        $this->reconciliationbagdata = new ReconciliationBagDataFilter();
        if (isset($_GET['action']) && $_GET['action'] == "reconciliation_bag_ajax") {
            $invoiceNumber = base64_decode($_GET['invoice']);
            $this->reconciliationbagdata->addFieldFilter('   invoice_number', $invoiceNumber);
            /*
             * Column filter
             * For search
             */
            if (isset($this->form_vars['action']) && $this->form_vars['action'] == 'filter') {
                $this->applyFilter($this->form_vars);
            }

            /*
             * Set columns orders for sorting
             */
            if (isset($this->form_vars['order'][0]['column']) && $this->form_vars['order'][0]['column'] != 0) {
                $dataTableColumnId = $this->form_vars['order'][0]['column'];
                $orderBy = $this->form_vars['order'][0]['dir'];
                $orderFalse = TRUE;
                if ($orderBy == 'desc') {
                    $orderFalse = FALSE;
                }
                $dataTableColumnName = ucfirst($this->form_vars['columns'][$dataTableColumnId]['data']);
                if(trim($this->form_vars['columns'][$dataTableColumnId]['data']) == 'no_of_piece'){
                    $dataTableColumnName = "number_of_piece";
                }
                if(trim($this->form_vars['columns'][$dataTableColumnId]['data']) == 'system_piece'){
                    $dataTableColumnName = "matched_piece";
                }
                if(trim($this->form_vars['columns'][$dataTableColumnId]['data']) == 'system_weight'){
                    $dataTableColumnName = "matched_weight";
                }
                //$functionName = 'AddOrderBy' . $dataTableColumnName;
//                    echo $functionName; die;
                $this->reconciliationbagdata->AddOrderBy(strtolower($dataTableColumnName), $orderFalse);
            } else {
                $this->reconciliationbagdata->AddOrderBy(strtolower('rbd.id'), false);
            }
            /*
             * Pagination Logic Implemented
             * 
             */
            $iTotalRecords = $this->reconciliationbagdata->getPagingCount();
            $iDisplayLength = intval($_REQUEST['length']);
            $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
            $iDisplayStart = intval($_REQUEST['start']);
            $sEcho = intval($_REQUEST['draw']);
            $end = $iDisplayStart + $iDisplayLength;
            $end = $end > $iTotalRecords ? $iTotalRecords : $end;
            $this->reconciliationbagdata->setRowsPerPage($iDisplayLength);
            // the offset of the list, based on current page
            $this->reconciliationbagdata->setOffset($iDisplayStart);
            $reconciliationbagdataObjs = $this->reconciliationbagdata->getPagingList();
            $setDataArr = array();
            foreach ($reconciliationbagdataObjs as $reconciliationbagdataObj) {
                $currentArr = array();
                $bagNumber = $reconciliationbagdataObj->getBagNumber();
                $countryCode = $reconciliationbagdataObj->getCountryCode();
                if($countryCode != "") {
                    $bagNumber = $bagNumber.'_'.$countryCode;
                }
                $currentArr['bag_number'] = $bagNumber;
                $currentArr['weight'] = $reconciliationbagdataObj->getWeight();
                $currentArr['no_of_piece'] = $reconciliationbagdataObj->getNumberOfPiece();
                $currentArr['system_piece'] = $reconciliationbagdataObj->getMatchedPiece();
                $currentArr['system_weight'] = $reconciliationbagdataObj->getMatchedWeight();

                if($reconciliationbagdataObj->getNumberOfParcelStatus() == "less"){
                    $currentArr['number_of_parcel_status'] ='<span class="label label-sm label-danger"> <strong>Less</strong> </span>';
                }else if($reconciliationbagdataObj->getNumberOfParcelStatus() == "more"){
                    $currentArr['number_of_parcel_status'] ='<span class="label label-sm label-warning"> <strong>More</strong> </span>';
                }else{
                    $currentArr['number_of_parcel_status'] ='<span class="label label-sm label-success"> <strong>Match</strong> </span>';
                }

                if($reconciliationbagdataObj->getWeightStatus() == "less"){
                    $currentArr['weight_status'] ='<span class="label label-sm label-danger"> <strong>Less</strong> </span>';
                }else if($reconciliationbagdataObj->getWeightStatus() == "more"){
                    $currentArr['weight_status'] ='<span class="label label-sm label-warning"> <strong>More</strong> </span>';
                }else{
                    $currentArr['weight_status'] ='<span class="label label-sm label-success"> <strong>Match</strong> </span>';
                }

                if($reconciliationbagdataObj->getParcelStatus() == "error"){
                    $currentArr['parcel_status'] ='<span class="label label-sm label-danger"> <strong>Error</strong> </span>';
                }else {
                    $currentArr['parcel_status'] ='<span class="label label-sm label-success"> <strong>Success</strong> </span>';
                }

                if($reconciliationbagdataObj->getStatus() == "error"){
                    $currentArr['status'] ='<span class="label label-sm label-danger"> <strong>Error</strong> </span>';
                }else {
                    $currentArr['status'] ='<span class="label label-sm label-success"> <strong>Success</strong> </span>';
                }
                $currentArr['action'] = $reconciliationbagdataObj->getAction();
                $currentArr['message'] = $reconciliationbagdataObj->getMessage();

                $currentArr['actions'] = '<div class="btn-group" data-container="body">
                                            <button class="btn btn-xs blue mt-ladda-btn ladda-button btn-outline dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Tools
                                                <i class="fa fa-angle-down"></i>
                                            </button>
                                                <ul class="dropdown-menu" >
                                                    <li>
                                                        <a href="supplier_reconciliation_details.php?bag_number='.base64_encode($reconciliationbagdataObj->getBagNumber()).'&invoice_number='.base64_encode($reconciliationbagdataObj->getInvoiceNumber()).'" title="Parcel Details" target="_blank" >
                                                            <i class="fa fa-eye"></i> Parcel Details
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div>
                                            ';
                $currentArr['select'] = '<div class="action_check_box"><label class="mt-checkbox mt-checkbox-single mt-checkbox-outline"><input type="checkbox" name="multi_select[]"  data-reconciliation_data_id="' . $reconciliationbagdataObj->getId() . '" id="multi_select_' . $reconciliationbagdataObj->getId() . '"  value="' . $reconciliationbagdataObj->getId() . '"  class="group-checkable reconciliationDataIds" /><span></span></label></div>';
                $setDataArr [] = $currentArr;
            }
            $setDataArrJson['data'] = $setDataArr;
            $setDataArrJson['draw'] = $sEcho;
            $setDataArrJson['recordsTotal'] = $iTotalRecords;
            $setDataArrJson['recordsFiltered'] = $iTotalRecords;
            echo json_encode($setDataArrJson);
            die;
        }
        if(isset($this->form_vars['action']) && $this->form_vars['action'] == "clear_bag"){
            if(isset($this->form_vars['bag_id']) && $this->form_vars['bag_id'] > 0){
                $reconciliationBagData = New ReconciliationBagData($this->form_vars['bag_id']);
                $reconciliationBagData->setStatus("v");
                $reconciliationBagData->save();
                echo "bag is clear now";
                die;
            }
        }
    }
    
    protected function applyFilter($data) {
        $this->form_vars = $data;
        $bagNumber = $this->form_vars['bag_number'];
        if (!empty($bagNumber)){
                $this->reconciliationbagdata->addFieldFilter('   rbd.bag_number', $bagNumber);
        }
        $weight = $this->form_vars['weight'];
        if (!empty($weight))
            $this->reconciliationbagdata->addFieldFilter('    rbd.weight', $weight);

        $noOfPiece = $this->form_vars['no_of_piece'];
        if (!empty($noOfPiece))
            $this->reconciliationbagdata->addFieldFilter('    rbd.number_of_piece', $noOfPiece);

        $systemPiece = $this->form_vars['system_piece'];
        if (!empty($systemPiece))
            $this->reconciliationbagdata->addFieldFilter('rbd.matched_piece', $systemPiece);

        $systemWeight = $this->form_vars['system_weight'];
        if (!empty($systemWeight))
            $this->reconciliationbagdata->addFieldFilter('rbd.matched_weight', $systemWeight);

        $status = $this->form_vars['status'];
        if (!empty($status))
            $this->reconciliationbagdata->addFieldFilter('rbd.status', $status);

        $numberOfParcelStatus = $this->form_vars['number_of_parcel_status'];
        if (!empty($numberOfParcelStatus))
            $this->reconciliationbagdata->addFieldFilter('rbd.number_of_parcel_status', $numberOfParcelStatus);


        $weightStatus = $this->form_vars['weight_status'];
        if (!empty($weightStatus))
            $this->reconciliationbagdata->addFieldFilter('rbd.weight_status', $weightStatus);

        $parcelStatus = $this->form_vars['parcel_status'];
        if (!empty($parcelStatus))
            $this->reconciliationbagdata->addFieldFilter('rbd.parcel_status', $parcelStatus);


        $action = $this->form_vars['action_frm'];
        if (!empty($action))
            $this->reconciliationbagdata->addFieldFilter('rbd.action', $action);


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
        <script type="text/javascript">
            var grid = null;        
            var DataTableFun = function () {
                var handleDataTable = function () {
                    grid = new Datatable();
                    grid.init({
                        src: $("#manage-data-table"),
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
                                "url": "reconciliation_bag_list.php?action=reconciliation_bag_ajax&invoice=<?php echo $_GET['invoice_number']; ?>", // ajax source
                                headers: {

                                },
                            },
                            "bStateSave": true,
                            "columns": [
                                        {"data": "select", "bSortable": false},
                                        {"data": "actions", "bSortable": false},
                                        {"data": "bag_number"},
                                        {"data": "weight"},
                                        {"data": "system_weight"},
                                        {"data": "no_of_piece"},
                                        {"data": "system_piece"},
                                        {"data": "number_of_parcel_status"},
                                        {"data": "weight_status"},
                                        {"data": "parcel_status"},
                                        {"data": "status"},
                                        {"data": "action"},
                                        {"data": "message", "bSortable": false}
                                    ]
                        }
                    });
                }
                return {
                    //main function to initiate the module
                    init: function () {
                        handleDataTable();
                    }
                };
            }();
            $(document).ready(function () {
                DataTableFun.init();
                <?php if(!empty($_GET['dispatch']) && $_GET['dispatch']=="yes"){ ?>
                    $(".filter-submit").click();
                <?php } ?>
                if ($('.date-picker').length > 0) {
                    //init date pickers
                    $('.date-picker').datepicker({
                        autoclose: true
                    });
                }
                $('.change_status').change(function(){
                    $(".fa-search").click();
                });
            });
            function clearBag(bagId) {
                swal({
                        title: "Are you sure you want to clear the bag",
                        text: "",
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonClass: "btn-danger",
                        confirmButtonText: "Yes",
                        cancelButtonText: "No",
                        closeOnConfirm: true,
                        closeOnCancel: true
                    },
                    function(isConfirm) {
                        if (isConfirm) {
                            $.ajax({
                                type: "POST",
                                url: "reconciliation_bag_list.php",
                                data: {action:'clear_bag',bag_id:bagId},
                                success: function (data) {
                                    grid.getDataTable().ajax.reload();
                                },
                                error: function () {
                                    //alert('error handing here');
                                }
                            });
                        }
                    });
            }
            $(document).on('click','#reconciliation_action_btn',function(){
                urlPage = "reconciliation_bag_list.php";
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
                        form_data.append('invoice_number', <?php echo $this->inv; ?>);

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
            $(document).on('click','#query_with_supplier_action_btn',function(){
                urlPage = "reconciliation_bag_list.php";
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
                   <?= $this->heading;?>
                </div>
                <div class="actions">

                </div>
            </div>
            <div class="portlet-body">
                <div class="row">
                    <?php $this->flashMsg->display(); ?>
                </div>
                <div class="table-actions-wrapper pull-right">
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
                <form method="post" action="" id="operation_manifest_list_form">
                    <input type="hidden" name="action" value="download_operation_manifest_list_csv" />
                    <table class="table table-striped table-bordered table-hover" id="manage-data-table">
                    <thead>
                        <tr role="row" class="heading">
                            <th>Select</th>
                            <th>Actions</th>
                            <th>Bag Number</th>
                            <th>Weight</th>
                            <th>System Weight</th>
                            <th>No. of Pieces</th>
                            <th>System Pieces</th>
                            <th>Pieces Status</th>
                            <th>Weight Status</th>
                            <th>Parcel Status</th>
                            <th>Status</th>
                            <th>Action</th>
                            <th>Message</th>
                        </tr>
                        <tr role="row" class="filter">
                            <td class="user_acccount_correct_button">
                                <div class="margin-bottom-5">
                                    <button class="btn btn-xs blue filter-submit btn-outline margin-left-5" ><i class="fa fa-search"></i> </button>
                                    <button class="btn btn-xs red filter-cancel mt-ladda-btn ladda-button btn-outline"><i class="fa fa-times"></i> </button>
                                </div>
                                <label class="mt-checkbox mt-checkbox-single mt-checkbox-outline">
                                    <input type='checkbox' name='checkall' class="group-checkable"/>
                                    <span></span>
                                </label>
                            </td>
                            <td  class="user_acccount_correct_button">


                            </td>
                            <td class="user_acccount_correct_button">
                                <input type="text" class="form-control form-filter input-xs" name="bag_number" id ="bag_number" />
                            </td>
                            <td class="user_acccount_correct_button">
                                <input type="text" class="form-control form-filter input-xs" name="weight" id ="weight" />
                            </td>
                            <td class="user_acccount_correct_button">
                                <input type="text" class="form-control form-filter input-xs" name="system_weight" id ="system_weight" />
                            </td>
                            <td class="user_acccount_correct_button">
                                <input type="text" class="form-control form-filter input-xs" name="no_of_piece" id ="no_of_piece" />
                            </td>
                            <td class="user_acccount_correct_button">
                                <input type="text" class="form-control form-filter input-xs" name="system_piece" id ="system_piece" />
                            </td>
                            <td class="user_acccount_correct_button">
                                <?php $IsOpen = array(''=>'Select','match' => 'Match', 'less' => 'Less', 'more' => 'More');
                                echo Ddl::generateArrayDDL('number_of_parcel_status', $IsOpen, "", '', ' class="change_status form-control form-filter select2 select" rel="tooltip" data-original-title="Number of Parcel Status" placeholder="Number of Parcel Status"'); ?>
                            </td>
                            <td class="user_acccount_correct_button">
                                <?php $IsOpen = array(''=>'Select','match' => 'Match', 'less' => 'Less', 'more' => 'More');
                                echo Ddl::generateArrayDDL('weight_status', $IsOpen, "", '', ' class="change_status form-control form-filter select2 select" rel="tooltip" data-original-title="Weight Status" placeholder="Weight Status"'); ?>
                            </td>
                            <td class="user_acccount_correct_button">
                                <?php $IsOpen = array(''=>'Select','success' => 'Success', 'error' => 'Error');
                                echo Ddl::generateArrayDDL('parcel_status', $IsOpen, "", '', ' class="change_status form-control form-filter select2 select" rel="tooltip" data-original-title="Parcel Status" placeholder="Parcel Status"'); ?>
                            </td>
                            <td class="user_acccount_correct_button">
                                <?php $IsOpen = array(''=>'Select','success' => 'Success', 'error' => 'Error');
                                echo Ddl::generateArrayDDL('status', $IsOpen, "", '', ' class="change_status form-control form-filter select2 select" rel="tooltip" data-original-title="Status" placeholder="Status"'); ?>
                            </td>
                            <td class="user_acccount_correct_button">
                                <?php $IsOpen = array(''=>'Select','approved' => 'Approved', 'manually_approved' => 'Manually Approved', 'query_with_supplier' => 'Query With Supplier', 'credit_note_received' => 'Credit Note Received');
                                echo Ddl::generateArrayDDL('action_frm', $IsOpen, "", '', ' class="change_status form-control form-filter select2 select" rel="tooltip" data-original-title="Action" placeholder="Action"'); ?>
                            </td>
                            <td class="user_acccount_correct_button">

                            </td>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
                </form>
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

}

// class

/* ------------------------------------------------------------------------------ */
// create and render page
$page = new Page(CONFIG_TEMPLATE_ADMIN);
$page->show();
?>