<?php
// get settings
require_once("../includes/settings/config.inc.php");
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
    'consignmentrelabel.class' ,
    'consignmentrelabelfilter.class' ,
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
    'tariffs.class',
    'tariffsfilter.class',
    'settingsupplieremail.class',
    'settingsupplieremailfilter.class',
    'supplierinvoiceemailsdetails.class',
    'supplierinvoiceemailsdetailsfilter.class',
    'reconciliationbagdata.class',
    'reconciliationbagdatafilter.class',
    'bagging.class',
    'baggingfilter.class',
    'parcelbaggingmapping.class',
    'parcelbaggingmappingfilter.class'
]);
class Page extends BasePage {
    /*     * *
     * Controller logic
     */
    private $user = "";

    protected function init() {
        $this->breadCrumb['data'] = array(
            'index.php' => Translation::GetCaption("HOME"),
            "Assign price with csv upload"
        );
        $this->user = SessionManager::getUser();
        if (isset($_GET['action']) && $_GET['action'] == 'invoice_emails_to_supplier') {
            $siedFilter = new SupplierInvoiceEmailsDetailsFilter();            
            $siedFilter->addFilter(['sied.supplier_invoice_id' => $this->form_vars['invoice_id']], '=');
            //$sied = $siedFilter->getList();
 /*           if (isset($this->form_vars['action']) && $this->form_vars['action'] == 'filter') {

                $userName = $this->form_vars['user_name'];
                if (!empty($userName)) {
                    $siedFilter->addFilter(" CONCAT(u.first_name, ' ', u.last_name) LIKE '%$userName%'");
                }

                $actionDate = $this->form_vars['action_date'];
                if (!empty($actionDate)) {
                    $actionDate = date('Y-m-d', strtotime(trim($actionDate)));
                    $siedFilter->addFilter("ua.created_at LIKE '%{$actionDate}%'");
                }

                $tableName = $this->form_vars['table_name'];
                if (!empty($tableName)) {
                    $siedFilter->addFilter("ua.table_name LIKE '%" . $userAuditObjNew->userSearchTable[$tableName]['table_name'] . "%'");
                }
            }
*/
            /*
             * Set columns orders for sorting
             */
            if (isset($this->form_vars['order'][0]['column']) && $this->form_vars['order'][0]['column'] != 0) {
                $dataTableColumnId = $this->form_vars['order'][0]['column'];
                $orderBy = $this->form_vars['order'][0]['dir'];
                $orderFalse = 'ASC';
                if ($orderBy == 'true') {
                    $orderFalse = 'DESC';
                }

                $dataTableColumnName = ucfirst($this->form_vars['columns'][$dataTableColumnId]['data']);
                //$functionName = 'AddOrderBy' . $dataTableColumnName;
//                    echo $functionName; die;
                $siedFilter->OrderBy(strtolower("vhl." . $dataTableColumnName), strtoupper($orderBy));
            }
            /*
             * Pagination Logic Implemented
             *
             */
            $siedFilter->orderBy('sied.id', 'DESC');

            $iDisplayLength = intval($_REQUEST['length']);
//            $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
            $iDisplayStart = intval($_REQUEST['start']);
            $sEcho = intval($_REQUEST['draw']);
            $end = $iDisplayStart + $iDisplayLength;
//            $end = $end > $iTotalRecords ? $iTotalRecords : $end;

            $siedFilter->setRowsPerPage($iDisplayLength);
            // the offset of the list, based on current page
            $siedFilter->setOffset($iDisplayStart);
            $siedList = $siedFilter->getList('*');

            $iTotalRecords = $siedFilter->getCount();
            $setDataArr = array();
            
            foreach ($siedList as $siedObj) {
                $currentArr = array();
                $currentArr['date']         = date("d-m-Y h:i:s",$siedObj->getAddedDate());
                $currentArr['email_to']     = $siedObj->getEmailTo();
                $currentArr['email_cc']     = $siedObj->getEmailCc();
                $currentArr['subject']      = $siedObj->getEmailSubject();
                $currentArr['body']         = $siedObj->getEmailBody();
                $currentArr['actions']      .= "<a href='' class='btn btn-xs blue btn-outline' id='driver-detail-btn' data-target='#vehicle-drivers' data-audit_id='" . $siedObj->getId() . "' data-toggle='modal'><span class='fa fa-eye'></span> </a>";
                $setDataArr[]               = $currentArr;
            }
            $setDataArrJson['data'] = $setDataArr;
            $setDataArrJson['draw'] = $sEcho;
            $setDataArrJson['recordsTotal'] = $iTotalRecords;
            $setDataArrJson['recordsFiltered'] = $iTotalRecords;
            echo json_encode($setDataArrJson, JSON_PARTIAL_OUTPUT_ON_ERROR);
            die;
        } else if (isset($_GET['func']) && $_GET['func'] == 'reconciliation_list') {
            $supplierInvoicesFilter = new SupplierInvoicesFilter();
            $supplierInvoicesFilter->where(['account_id' => $this->user->getUserAccountId()]);
            /*
             * Column filter
             * For search
             */
            if (isset($this->form_vars['action']) && $this->form_vars['action'] == 'filter') {
                $carrierId = $this->form_vars['carrier_id'];
                if (!empty($carrierId)) {
                    $supplierInvoicesFilter->where(['carrier_id' => $carrierId]);
                }
                $invoiceNumber = $this->form_vars['invoice_number'];
                if (!empty($invoiceNumber)) {
                    $supplierInvoicesFilter->where(['invoice_number' => $invoiceNumber]);
                }
                $status = $this->form_vars['status'];
                if (!empty($status)) {
                    $supplierInvoicesFilter->where(['status' => $status]);
                }
                $searchDateFrom = $this->form_vars['search_date_from'];
                $searchDateTo = $this->form_vars['search_date_to'];
                if (!empty($searchDateFrom) && !empty($searchDateTo)) {
                    $supplierInvoicesFilter->whereBetween('invoice_date',$searchDateFrom,$searchDateTo);
                }
            }
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
                    $supplierInvoicesFilter->orderBy(strtolower($dataTableColumnName), $orderFalse);
                }
            } else {
                $supplierInvoicesFilter->orderBy(strtolower("si.id"), "DESC");
            }
            /*
             * Pagination Logic Implemented
             *
             */
            $iDisplayLength = intval($_REQUEST['length']);
            $iDisplayLength = $iDisplayLength < 0 ? 20 : $iDisplayLength;
            $iDisplayStart = intval($_REQUEST['start']);
            $sEcho = intval($_REQUEST['draw']);
            $supplierInvoicesFilter->setRowsPerPage($iDisplayLength);
            $supplierInvoicesFilter->setOffset($iDisplayStart);
            $supplierInvoicesFilterObjs = $supplierInvoicesFilter->getList("si.*");
            $iTotalRecords = $supplierInvoicesFilter->getCount();
            $setDataArr = [];
            if(count($supplierInvoicesFilterObjs)) {
                foreach($supplierInvoicesFilterObjs as $supplierInvoicesFilterObj) {
                    $carrierObj = new Carrier($supplierInvoicesFilterObj->getCarrierId());
                    $carrierNameLink = str_replace(" ","_",$carrierObj->getCarrier());
                    $carrierName = $carrierObj->getCarrier();
                    $dateFolder = $supplierInvoicesFilterObj->getDate();
                    $uploadFile = RECONCILIATION_URL.$carrierNameLink.'/'.$dateFolder.'/'.$supplierInvoicesFilterObj->getUploadFile();
                    $invoiceCheckFile = RECONCILIATION_URL.$carrierNameLink.'/'.$dateFolder.'/'.$supplierInvoicesFilterObj->getInvoiceCheckFile();
                    $reconciliationFile = RECONCILIATION_URL.$carrierNameLink.'/'.$dateFolder.'/'.$supplierInvoicesFilterObj->getReconciliationFile();
                    $currentArr['invoice_date'] = $supplierInvoicesFilterObj->getInvoiceDate();
                    $currentArr['invoice_number'] = $supplierInvoicesFilterObj->getInvoiceNumber();
                    $currentArr['carrier_id'] = $carrierName;
                    $currentArr['total_weight'] = $supplierInvoicesFilterObj->getTotalWeight();
                    $currentArr['total_pieces'] = $supplierInvoicesFilterObj->getTotalPieces();
                    $currentArr['total_amount'] = $supplierInvoicesFilterObj->getTotalAmount().' '.$supplierInvoicesFilterObj->getCurrency();
                    $currentArr['reconciliation_total_weight'] = $supplierInvoicesFilterObj->getTotalProcessedWeight();
                    $currentArr['reconciliation_total_pieces'] = $supplierInvoicesFilterObj->getTotalProcessedPieces();
                    $currentArr['reconciliation_total_amount'] = $supplierInvoicesFilterObj->getTotalProcessedAmount().' '.$supplierInvoicesFilterObj->getCurrency();
                    if($supplierInvoicesFilterObj->getStatus() == "pending") {
                        $currentArr['status'] = '<label class="btn btn-xs btn-danger">'.$supplierInvoicesFilterObj->getStatus().'</label>';
                    } else if($supplierInvoicesFilterObj->getStatus() == "processed") {
                        $currentArr['status'] = '<label class="btn btn-xs btn-warning">'.$supplierInvoicesFilterObj->getStatus().'</label>';
                    } else {
                        $currentArr['status'] = '<label class="btn btn-xs btn-success">'.$supplierInvoicesFilterObj->getStatus().'</label>';
                    }
                    $action = '<div class="btn-group" data-container="body" >
                                            <button class="btn btn-xs blue mt-ladda-btn ladda-button btn-outline dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Tools
                                                <i class="fa fa-angle-down"></i>
                                            </button>
                                            <ul class="dropdown-menu" >';
                                    if($carrierName == "Royal Mail"){
                                        $action .= '<li>
                                                    <a title="Bag Details" href="reconciliation_bag_list.php?invoice_number='.base64_encode($supplierInvoicesFilterObj->getInvoiceNumber()).'" target="_blank">
                                                        <span class="glyphicon glyphicon-eye-open"></span> Bag Details
                                                    </a>
                                                </li>';
                                    }
                                    $action .= '<li>
                                                    <a title="Reconciliation Bag" href="supplier_reconciliation_details.php?invoice_number='.base64_encode($supplierInvoicesFilterObj->getInvoiceNumber()).'" target="_blank">
                                                        <span class="glyphicon glyphicon-eye-open"></span> Details
                                                    </a>
                                                </li>';
                                    if($supplierInvoicesFilterObj->getStatus() != "approved") {
                                        $action .= '<li>
                                                    <a title="Re process" href="javascript:;" class="reprocess_file" data-id="' . $supplierInvoicesFilterObj->getId() . '">
                                                        <span class="glyphicon glyphicon-ok"></span> Re Process
                                                    </a>
                                                </li>';
                                    }
                                    $action .= '<li>
                                                    <a title="Uploaded File" href="'.$uploadFile.'" target="_blank">
                                                        <span class="glyphicon glyphicon-download"></span> Uploaded File
                                                    </a>
                                                </li>';
                                    if(!empty($supplierInvoicesFilterObj->getInvoiceCheckFile())) {
                                        $action .= '<li>
                                                    <a title="Invoice Check File" href="' . $invoiceCheckFile . '" target="_blank">
                                                        <span class="glyphicon glyphicon-download"></span> Invoice Check File
                                                    </a>
                                                </li>';
                                    }
                                    if(!empty($supplierInvoicesFilterObj->getReconciliationFile())) {
                                        $action .= '<li>
                                                    <a title="Reconciliation File" href="' . $reconciliationFile . '" target="_blank">
                                                        <span class="glyphicon glyphicon-download"></span> Reconciliation File
                                                    </a>
                                                </li>';
                                    }
                                    $action .= '
                                                <li>
                                                    <a title="Re process" href="javascript:;" class="show_email_supplier_query" 
                                                    data-target="#show_email_supplier_query_modal" 
                                                    data-supplier_type="'.$supplierInvoicesFilterObj->getCarrierType().'"  '
                                                    . 'data-supplier_id="'.$supplierInvoicesFilterObj->getCarrierId().'" '
                                                    . 'data-id="' . $supplierInvoicesFilterObj->getId() . '">
                                                                <span class="glyphicon glyphicon-eye-open"></span> Supplier Emails Details 
                                                    </a>
                                                </li>';
                                $action .= '</ul>';
                    $action .= '</div>';
                    if($supplierInvoicesFilterObj->getStatus() != "approved") {
                        $select = '<div class="action_check_box">';
                        $select .= '<label class="mt-checkbox mt-checkbox-single mt-checkbox-outline"><input type="checkbox" name="multi_select[]"  data-supplier_invoice_id="' . $supplierInvoicesFilterObj->getId() . '" id="multi_select_' . $supplierInvoicesFilterObj->getId() . '"  value="' . $supplierInvoicesFilterObj->getId() . '"  class="group-checkable reconciliationDataIds" /><span></span></label>';
                        $select .= '</div>';
                    }
                    $currentArr['actions'] = $action;
                    $currentArr['select'] = $select;
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

        if (isset($this->form_vars['func']) && $this->form_vars['func'] == 'upload_csv_file') {
            $output = [];
            $noOfFileUploadForReconciliation = $this->form_vars['no_of_file_upload_for_reconciliation'];
            $carrierId = $this->form_vars['carrier_id'];
            $processingOption = $this->form_vars['processing_option'];
            $carrierObj = new Carrier($carrierId);
            $serviceFilter = new ServiceFilter();
            $serviceFilter->addFieldFilter('       carrier_id',$carrierId);
            $serviceFilterObjs = $serviceFilter->getList();
            $carrierName = str_replace(" ","_",$carrierObj->getCarrier());
            $headingArr = [
                'account_number',
                'invoice_number',
                'agent_reference_number',
                'collection_date',
                'delivery_country',
                'mawb',
                'awb',
                'hawb',
                'service_name',
                'service_code',
                'weight',
                'vol_weight',
                'length',
                'width',
                'height',
                'number_of_pieces',
                'basic_charges',
                'fuel_charges',
                'additional_charges',
                'vat',
                'total_amount',
                'notes',
                'currency'
            ];
            /* csv get data and add to table */
            $carrierClass = "";
            if(count($serviceFilterObjs)>0) {
                $carrierClass = $serviceFilterObjs[0]->getLabelClassName();
            }

            if(trim($carrierClass) != "") {
                $file = "../includes/labels/" . strtolower($carrierClass) . ".class.php";
                if (is_file($file)) {
                    include_classes([
                        strtolower($carrierClass).'.class',
                    ], 'labels');
                } else {
                    $outputInvoiceCsv['status'] = "error";
                    $outputInvoiceCsv['message'] = "Sorry class file does not exist";
                }
                if(class_exists($carrierClass)){
                    $carrierClassObj = new $carrierClass;
                    // Move file here
                    @$csv_file = $_FILES['csv_file'];
                    @$csv_file_2 = $_FILES['csv_file_2'];
                    if (!empty($csv_file['name'])) {
                        $file_name = $csv_file['name'];
                        $path_parts = pathinfo($file_name);
                        $ext = strtolower($path_parts['extension']);
                        $basename = $path_parts['basename'];
                        if ($ext == 'csv') {
                            $carrierName = str_replace(" ","_",$carrierObj->getCarrier());
                            $user = SessionManager::getUser();
                            $userId = $user->getId();
                            $batchNumber = $userId . "_" . time();
                            $newFileName = strtolower($carrierName)."_" . $batchNumber . "." . $ext;
                            $dateFolder = date('Y-m-d');
                            $returnArr['date_folder'] = $dateFolder;
                            if (!file_exists(RECONCILIATION_PATH)) {
                                @mkdir(RECONCILIATION_PATH, 0775);
                            }
                            if (!file_exists(RECONCILIATION_PATH.$carrierName.'/')) {
                                @mkdir(RECONCILIATION_PATH.$carrierName.'/', 0775);
                            }
                            if (!file_exists(RECONCILIATION_PATH.$carrierName.'/'.$dateFolder.'/')) {
                                @mkdir(RECONCILIATION_PATH.$carrierName.'/'.$dateFolder.'/', 0775);
                            }
                            $carrierName = str_replace(" ","_",$carrierObj->getCarrier());
                            $filePath = RECONCILIATION_PATH.$carrierName.'/'.$dateFolder;
                            $relPath = RECONCILIATION_PATH.$carrierName.'/'.$dateFolder.'/'.$newFileName;
                            if(isset($_FILES['csv_file_2']) && ($noOfFileUploadForReconciliation > 1)) {
                                $batchNumber2 = $batchNumber."_2";
                                $newFileName2 = strtolower($carrierName)."_" . $batchNumber2 . "." . $ext;
                                $relPath2 = RECONCILIATION_PATH.$carrierName.'/'.$dateFolder.'/'.$newFileName2;
                            }
                            //                /* create new csv file and write data on it*/
                            $new_csv_file_name = strtolower($carrierName)."_new_" . $batchNumber . ".csv";
                            $new_csv_file_created = RECONCILIATION_PATH.$carrierName.'/'.$dateFolder.'/'.$new_csv_file_name;
                            $new_csv_file_save = RECONCILIATION_URL.$carrierName.'/'.$dateFolder.'/'.$new_csv_file_name;
                            if($noOfFileUploadForReconciliation > 1) {
                                $new_csv_file_name_2 = strtolower($carrierName)."_new_2_" . $batchNumber . ".csv";
                                $new_csv_file_created_2 = RECONCILIATION_PATH.$carrierName.'/'.$dateFolder.'/'.$new_csv_file_name_2;
                            }
                            $returnArr['upload_file'] = $newFileName;
                            if (move_uploaded_file($csv_file['tmp_name'], $relPath)) {
                                if(isset($_FILES['csv_file_2'])){
                                    if($noOfFileUploadForReconciliation > 1) {
                                        if (move_uploaded_file($csv_file_2['tmp_name'], $relPath2)) {

                                        } else {
                                            $returnArr['status'] = 'error';
                                            $returnArr['message'] = 'File 2 upload error';
                                        }
                                    }
                                }
                            } else {
                                $returnArr['status'] = 'error';
                                $returnArr['message'] = 'File upload error';
                            }
                        }else {
                            $returnArr['status'] = 'error';
                            $returnArr['message'] = 'Please select valid csv file';
                        }
                        if($returnArr['status'] == 'error'){
                            echo json_encode($returnArr);
                            exit;
                        }
                    }
                    // Now get file summery by calling a function in the carreir class and break the loop when the data is completed
                    $returnSummeryArr = $carrierClassObj->getSummeryData($relPath);
                    // Save data into supplier invoices table
                    // check if invoice is already entred
                    $supplierInvoicesFilter = new SupplierInvoicesFilter();
                    $supplierInvoicesFilter->addFilter(["invoice_number"=>$returnSummeryArr['invoice_number'] ]);
                    $supplierInvoicesObj = $supplierInvoicesFilter->getList("*");
                    $suplierInvoiceId = "";
                    if(count($supplierInvoicesObj) == 0){
                        $date_added = time();
                        $supplierInvoices = new SupplierInvoices();
                        $supplierInvoices->setAccountId($this->user->getUserAccountId());
                        $supplierInvoices->setInvoiceNumber($returnSummeryArr['invoice_number']);
                        $supplierInvoices->setCarrierId($carrierId);
                        $supplierInvoices->setInvoiceDate($returnSummeryArr['collection_date']);
                        $supplierInvoices->setDate($dateFolder);
                        $supplierInvoices->setUploadFile($newFileName);
                        $supplierInvoices->setAddedBy($this->user->getId());
                        $supplierInvoices->setAddedDate($date_added);
                        $supplierInvoices->setTotalWeight("0.00");
                        $supplierInvoices->setTotalPieces("0.00");
                        $supplierInvoices->setTotalAmount("0.00");
                        $supplierInvoices->setTotalProcessedWeight("0.00");
                        $supplierInvoices->setTotalProcessedPieces("0.00");
                        $supplierInvoices->setTotalProcessedAmount("0.00");
                        $supplierInvoices->save();
                        $suplierInvoiceId = $supplierInvoices->getId();
                    }else{
                        $outputInvoiceCsv['status'] = "success";
                        $outputInvoiceCsv['message'] = "Your file is already procesed.";
                        echo json_encode($outputInvoiceCsv);
                        exit;
                    }
                    if($noOfFileUploadForReconciliation > 1) {
                        $returnArr = $carrierClassObj->reconciliation_data($headingArr,$carrierId,$relPath, $relPath2,$new_csv_file_created,$new_csv_file_created_2,$filePath,$batchNumber);
                    } else {
                        $returnArr = $carrierClassObj->reconciliation_data($headingArr,$carrierId,$relPath,$new_csv_file_created,$filePath,$batchNumber);
                    }
                    // remove uplier invoice data from table if no record is inserted
                    if(empty($returnArr)){
                        $supplierInvoices = new SupplierInvoices();
                        $supplierInvoices->deleteSupplierInvoiceBySupplierInvoiceId($suplierInvoiceId);
                        $outputInvoiceCsv['status'] = "error";
                        $outputInvoiceCsv['message'] = "Your file is not processed. Data is not according to the format";
                        echo json_encode($outputInvoiceCsv);
                        exit;
                    }
                    $returnArr['supplier_invoice_id'] = $suplierInvoiceId;
                    $currency = $returnArr['currency'];
                    if($currency != "" && $suplierInvoiceId != "") {
                        $totalWeight = $returnArr['total_weight'];
                        $totalNumberOfPieces = $returnArr['total_pieces'];
                        $totalAmount = $returnArr['total_amount'];
                        $supplierInvoices = new SupplierInvoices($suplierInvoiceId);
                        $supplierInvoices->setCurrency($currency);
                        $supplierInvoices->setTotalWeight($totalWeight);
                        $supplierInvoices->setTotalPieces($totalNumberOfPieces);
                        $supplierInvoices->setTotalAmount($totalAmount);
                        $supplierInvoices->save();
                    }
                    $returnArr['new_invoice_save'] = 1;
                    // Update ReconciliationData table with suplier invoice id column
                    $reconciliationdataFilter = New ReconciliationDataFilter();
                    $reconciliationdataFilter->set(["supplier_invoice_id"=>$suplierInvoiceId]);
                    $reconciliationdataFilter->where(["batch_number"=>$batchNumber]);
                    $reconciliationdataFilter->update();
                    // Now call the method of read data of carrier call
                    // Now check if data is more then 50 records then show error and break the request
                    if(!empty($returnArr['data']) && (count($returnArr['data']) > 50 || $returnArr['data'] == 50)) {
                        $this->supplierUpdateForReconciliation($returnArr, $carrierId);
                        $outputInvoiceCsv['status'] = "success";
                        $outputInvoiceCsv['message'] = "Your file is processing. We will let you know when its done.";
                        echo json_encode($outputInvoiceCsv);
                        exit;
                    }
                    // First save invoice data if error then remove from invoice
                    // make other function in carrier class which can be called at cron job
                    if(trim($processingOption) == 'reconcile' || trim($processingOption) == 'invoice_check'){
                        $outputInvoiceCsv = SupplierInvoices::invoiceCheckCsv($returnArr,$carrierName,"","",$carrierClass);
                    }
                } else {
                    $outputInvoiceCsv['status'] = "error";
                    $outputInvoiceCsv['message'] = "Sorry class does not exist";
                }
                if($outputInvoiceCsv['status'] == 'success' || trim($processingOption) == 'reconcile') {
                    $returnArr['status'] = $outputInvoiceCsv['status'];
                    $returnArr['invoice_check_file'] = $outputInvoiceCsv['file'];
                    if(trim($processingOption) == 'both' || trim($processingOption) == 'reconcile'){
                        $outputReconciliationCsv = SupplierInvoices::reconciliationCsv($returnArr,$carrierName);
                        if($noOfFileUploadForReconciliation > 1) {
                            SupplierInvoices::reconciliationBagCsv($returnArr,$carrierName);
                        }
                    }
                    $returnArr['reconciliation_file'] = $outputReconciliationCsv['file'];
                    $newInvoiceSave = $returnArr['new_invoice_save'];
                    $this->supplierUpdateForReconciliation($returnArr, $carrierId, $outputReconciliationCsv, $outputInvoiceCsv);
                } else {
                    $returnArr['status'] = $outputInvoiceCsv['status'];
                    $returnArr['message'] = $outputInvoiceCsv['message'];
                }
            } else {
                $returnArr['status'] = "error";
                $returnArr['message'] = "Sorry carrier class label not exist";
            }
            echo json_encode($returnArr);
            exit;
        }

        if (isset($this->form_vars['func']) && $this->form_vars['func'] == 'reprocess_file') {
            $id = $this->form_vars['id'];
            $output = SupplierInvoices::reprocessFile($id);
            echo json_encode($output);
            die;
        }
        if (isset($this->form_vars['func']) && $this->form_vars['func'] == 'update_selected_action') {
            $passData = [];
            $output = [];
            $ids = explode(',', $this->form_vars['ids']);
            foreach ($ids as $id) {
                $supplierInvoice = new SupplierInvoices($id);
                $invoiceNumber = $supplierInvoice->getInvoiceNumber();
                $sql = "SELECT * FROM reconciliation_data WHERE supplier_invoice_id='" . $id . "' ";
                $resultSql = DbAccess3::runQuery($sql);
                $res = [];
                while ($obj = mysqli_fetch_object($resultSql)) {
                    $res[] = $obj;
                }
                $dateFolder = $supplierInvoice->getDate();
                $carrierObj = new Carrier($supplierInvoice->getCarrierId());
                $carrierName = str_replace(" ", "_", $carrierObj->getCarrier());
                $filePath = RECONCILIATION_PATH . $carrierName . '/' . $dateFolder;
                $batchNumber = '';
                if (count($res)) {
                    $batchNumber = $res[0]->batch_number;
                }
                $templateCheck = $supplierInvoice->getTemplate();
                $passData['status'] = 'success';
                $passData['file_path'] = $filePath;
                $passData['batch_number'] = $batchNumber;
                $passData['template'] = $templateCheck;
                $passData['data'] = $res;
                $invoice_check_csv_file_name = $supplierInvoice->getInvoiceCheckFile();
                $outputInvoiceCsv = SupplierInvoices::invoiceCheckCsv($passData, $carrierName, $dateFolder, $invoice_check_csv_file_name);
                if ($outputInvoiceCsv['status'] == 'success') {
                    $reconciliation_csv_file_name = $supplierInvoice->getReconciliationFile();
                    $outputReconciliationCsv = SupplierInvoices::reconciliationCsv($passData, $carrierName, $dateFolder, $reconciliation_csv_file_name);
                    $totalProcessedWeight = $outputInvoiceCsv['total_processed_weight'];
                    $totalProcessedPieces = $outputInvoiceCsv['total_processed_pieces'];
                    $totalProcessedAmount = $outputInvoiceCsv['total_processed_amount'];
                    if ($outputInvoiceCsv['all_success'] == 1) {
                        $supplierInvoicesUpdate = new SupplierInvoices($id);
                        $supplierInvoicesUpdate->setStatus('approved');
                        $supplierInvoicesUpdate->setTotalProcessedWeight($totalProcessedWeight);
                        $supplierInvoicesUpdate->setTotalProcessedPieces($totalProcessedPieces);
                        $supplierInvoicesUpdate->setTotalProcessedAmount($totalProcessedAmount);
                        $supplierInvoicesUpdate->save();
                    }
                    $output['status'] = 'success';
                    $output['message'] = 'Re processed file successfully';
                }
            }
            if(!isset($output['status']) || $output['status'] != 'success') {
                $output['status'] = 'error';
                $output['message'] = 'File not reprocessed please contact to support';
            }
            echo json_encode($output);
            die;
        }

    }

    public function supplierUpdateForReconciliation($returnArr, $carrierId, $outputReconciliationCsv = [], $outputInvoiceCsv = []) {
        $invoiceNumber = $returnArr['invoice_number'];
        $uploadFile = $returnArr['upload_file'];
        $dateFolder = $returnArr['date_folder'];
        $invoiceDate = $returnArr['invoice_date'];
        $totalWeight = $returnArr['total_weight'];
        $totalPieces = $returnArr['total_pieces'];
        $totalAmount = $returnArr['total_amount'];
        $template = $returnArr['template'];
        if(!empty($outputInvoiceCsv)) {
            $totalProcessedWeight = $outputInvoiceCsv['total_processed_weight'];
            $totalProcessedPieces = $outputInvoiceCsv['total_processed_pieces'];
            $totalProcessedAmount = $outputInvoiceCsv['total_processed_amount'];
        } else {
            $totalProcessedWeight = 0;
            $totalProcessedPieces = 0;
            $totalProcessedAmount = 0;
        }
        $reconciliationBagDataFilter = New ReconciliationBagDataFilter();
        $reconciliationBagDataFilter->addFieldFilter("    invoice_number",$invoiceNumber);
        $reconciliationBagDataFilter->addGroupBy("status");
        $reconciliationBagData = $reconciliationBagDataFilter->getList("id,status");
        if($returnArr['new_invoice_save'] == 0) {
            $date_added = time();
            $supplierInvoices = new SupplierInvoices($returnArr['supplier_invoice_id']);
            if(!isset($returnArr['supplier_invoice_id'])){
                $supplierInvoices->setAccountId($this->user->getUserAccountId());
                $supplierInvoices->setCarrierId($carrierId);
                $supplierInvoices->setInvoiceNumber($invoiceNumber);
                $supplierInvoices->setInvoiceDate($invoiceDate);
                $supplierInvoices->setDate($dateFolder);
                $supplierInvoices->setAddedBy($this->user->getId());
                $supplierInvoices->setAddedDate($date_added);
            }
            $supplierInvoices->setTemplate($template);
            $supplierInvoices->setTotalWeight($totalWeight);
            $supplierInvoices->setTotalPieces($totalPieces);
            $supplierInvoices->setTotalAmount($totalAmount);
            $supplierInvoices->setTotalProcessedWeight($totalProcessedWeight);
            $supplierInvoices->setTotalProcessedPieces($totalProcessedPieces);
            $supplierInvoices->setTotalProcessedAmount($totalProcessedAmount);
            if(strpos($uploadFile, ".csv") !== false){
                $supplierInvoices->setUploadFile($uploadFile);
            }
            if(empty($outputInvoiceCsv)) {
                $supplierInvoices->setInvoiceCheckFile('');
            } else {
                $supplierInvoices->setInvoiceCheckFile($outputInvoiceCsv['file_name']);
            }
            if(empty($outputReconciliationCsv['file_name'])) {
                $supplierInvoices->setReconciliationFile('');
            } else {
                $supplierInvoices->setReconciliationFile($outputReconciliationCsv['file_name']);
            }
            if(empty($outputInvoiceCsv['all_success'])){
                $supplierInvoices->setStatus('pending');
            } else {
                if ($outputInvoiceCsv['all_success'] == 1) {
                    $supplierInvoices->setStatus('approved');
                } else {
                    $supplierInvoices->setStatus('processed');
                }
            }
            if (count($reconciliationBagData) > 0) {
                $isError = false;
                foreach ($reconciliationBagData as $reconciliationBagDatum) {
                    if($reconciliationBagDatum->getStatus() == "error"){
                        $isError = true;
                    }
                }
                if($isError){
                    $supplierInvoices->setStatus('processed');
                }
            }
            $supplierInvoices->save();
            $supplierInvoicesId = $supplierInvoices->getId();
           $sql = "UPDATE reconciliation_data SET supplier_invoice_id=".$supplierInvoicesId." WHERE invoice_number='".$invoiceNumber . "'";

            DbAccess3::runQuery($sql);
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
            var urlPage = 'supplier_reconciliation.php';
            var grid = null;
            var DataTableFun = function () {
                var handleDataTable = function () {
                    grid = new Datatable();
                    grid.init({
                        src: $("#reconciliation_datatable"),
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
                                "url": urlPage+'?func=reconciliation_list', // ajax source
                                headers: {

                                },
                            },
                            "bStateSave": true,
                            "columns": [
                                {"data": "actions", "bSortable": false},
                                {"data": "status"},
                                {"data": "invoice_date"},
                                {"data": "invoice_number"},
                                {"data": "carrier_id"},
                                {"data": "total_weight"},
                                {"data": "reconciliation_total_weight"},
                                {"data": "total_pieces"},
                                {"data": "reconciliation_total_pieces"},
                                {"data": "total_amount"},
                                {"data": "reconciliation_total_amount"},
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
            $('.select-carrier-dropdown').change(function() {
                var carrier_id_check = $(this).val();
                if(carrier_id_check == 189) {
                    $('.second-file-upload').css('display', 'flex');
                    $('#no_of_file_upload_for_reconciliation').val(2);
                } else {
                    $('.second-file-upload').css('display', 'none');
                    $('#no_of_file_upload_for_reconciliation').val(1);
                }
            });            
            
        var gridSuplierEmailDataTable = null;
    var DataTableFunSuplierEmailDataTable = function () {
        var handleDataTableSuplierEmailDataTable = function () {
            var datatableparcelurl = "supplier_reconciliation.php?action=invoice_emails_to_supplier";
            gridUserAudit = new Datatable();
            gridUserAudit.init({
                src: $("#manage-data-table-show-supplier-email"),
                onSuccess: function (grid, response) {
                    // execute some code after table records loaded
                },

                onError: function (grid) {
                    // execute some code on network or other general error
                },
                dataTable: {// here you can define a typical datatable settings from http://datatables.net/usage/options
                    "lengthMenu": [
                        [10, 20, 50, 100, 150],
                        [10, 20, 50, 100, 150] // change per page values here
                    ],
                    "pageLength": 10, // default record count per page
                    "ajax": {
                        "url": datatableparcelurl, // ajax source
                        headers: {}
                    },
                    "bServerSide": false,
                    "deferLoading": false,
                    "bStateSave": true,
                    "ordering": false,
                    "columns": [
                        {"data": "date", "bSortable": false},
                        {"data": "email_to", "bSortable": false},
                        {"data": "email_cc", "bSortable": false},
                        {"data": "subject", "bSortable": false},
                        {"data": "body",    "bSortable": false},
                    ],
                    rowCallback: function (row, data, index) {

                    }
                }
            });
        };
        return {
            //main function to initiate the module
            init: function () {
                handleDataTableSuplierEmailDataTable();
            }
        };
    }();
    
            
            $(document).ready(function () {
                $(document).ajaxStart($.blockUI).ajaxStop($.unblockUI);
                DataTableFun.init();
                if ($('.date-picker').length > 0) {
                    //init date pickers
                    $('.date-picker').datepicker({
                        autoclose: true
                    });
                }
                $("#btnSubmitImport").click(function () {
                    var carrier_id = $('#carrier_id').val();
                    var processing_option = $('#processing_option').val();
                    $('#console_window').html('');
                    $('#console_window').html("Uploading CSV File....<br />");
                    var file_data = $('#csv_for_pricing').prop('files')[0];
                    var file_data_2 = $('#csv_for_priceing_2').prop('files')[0];
                    var form_data = new FormData();
                    var batch_number = '';
                    var table_name = '';
                    var no_of_file_upload_for_reconciliation = $('#no_of_file_upload_for_reconciliation').val();
                    form_data.append('csv_file', file_data);
                    form_data.append('csv_file_2', file_data_2);
                    form_data.append('carrier_id', carrier_id);
                    form_data.append('processing_option', processing_option);
                    form_data.append('no_of_file_upload_for_reconciliation', no_of_file_upload_for_reconciliation);
                    form_data.append('func', 'upload_csv_file');
                    $.ajax({
                        url: urlPage,
                        dataType: 'json',
                        cache: false,
                        contentType: false,
                        processData: false,
                        data: form_data,
                        type: 'post',
                        success: function (response) {
                            if (response.status == 'success') {
                                $('#console_window').append('Reconciliation successfully<br />');
                                if(response.invoice_check_file) {
                                    $('#console_window').append('<br /> <a href="' + response.invoice_check_file + '" class="btn btn-primary btn-xs">Invoice check csv</a> Click button to download file ');
                                }
                                if(response.reconciliation_file) {
                                    $('#console_window').append('<br /><br /> <a href="' + response.reconciliation_file + '" class="btn btn-primary btn-xs">Reconciliation csv</a> Click button to download file ');
                                }
                                grid.getDataTable().ajax.reload();
                            } else {
                                $('#console_window').append('<span style="color:red;">' + response.message + '</span><br />');
                            }
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
                            form_data.append('func', 'update_selected_action');
                            $.ajax({
                                type: "POST",
                                url: urlPage,
                                data: form_data,
                                dataType: "json",
                                cache: false,
                                contentType: false,
                                processData: false,
                                success: function (data) {
                                    grid.getDataTable().ajax.reload();
                                    swal("Success!", data.message, "success");
                                },
                                error: function () {
                                    //alert('error handing here');
                                }
                            });
                        } else {
                            swal("Sorry!", "Please select action", "error");
                        }
                    } else {
                        swal("Sorry!", "Please check the checkbox for action", "error");
                    }
                });

                $(document).on('click','.reprocess_file',function(){
                    var id = $(this).data('id');
                    var form_data = new FormData();
                    form_data.append('id', id);
                    form_data.append('func', 'reprocess_file');
                    $.ajax({
                        url: urlPage,
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
                
                $('body').on('click', '.show_email_supplier_query', function () {
			$('#spplier_email_detail_view_html').html('<p>Loading Please wait...</p>');
			if(gridUserAudit == null)
                            DataTableFunSuplierEmailDataTable.init();
                        var supplier_id = $(this).data('supplier_id');
                        var supplier_type = $(this).data('supplier_type');
                        var invoice_id = $(this).data('id');
                        if (invoice_id != '' && supplier_type != undefined && supplier_id != '' && supplier_id != undefined) {
                            gridUserAudit.setAjaxParam('supplier_type', supplier_type);
                            gridUserAudit.setAjaxParam('invoice_id', invoice_id);
                            gridUserAudit.setAjaxParam('supplier_id', supplier_id);
                            gridUserAudit.setAjaxParam('onload', 1);
                            gridUserAudit.submitFilter();
                            $('.custom-alerts').hide();
                        } else {
                            $('#spplier_email_detail_view_html').html('<p>No record found.</p>');
                        }
                        $("#show_email_supplier_query_modal").modal("show");
                    });

        
              /*  $(document).on('click','.show_email_supplier_query',function(){
                    var id = $(this).data('id');
                    var supplier_id = $(this).data('supplier_id');
                    var supplier_type = $(this).data('supplier_type');
                    var id = $(this).data('id');
                    var form_data = new FormData();
                    form_data.append('id', id);
                    form_data.append('supplier_id', supplier_id);
                    form_data.append('supplier_type', supplier_type);
                    form_data.append('func', 'show_email_supplier_query');
                    $.ajax({
                        url: urlPage,
                        dataType: 'json',
                        cache: false,
                        contentType: false,
                        processData: false,
                        data: form_data,
                        type: 'post',
                        success: function (response) {
                            if(response.status == "success") {
                              //  grid.getDataTable().ajax.reload();
                                swal("Success!", response.message, "success");
                            } else {
                                swal("Sorry!", response.message, "error");
                            }
                        }
                    });
                });
                */
                $('.change_status').change(function(){
                    $(".fa-search").click();
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
                <div class="caption"> <i class="fa fa-search"></i>
                    CSV Upload Panel
                </div>
                <div class="actions"></div>
            </div>
            <div class="portlet-body">
                <form method="post" action="supplier_reconciliation.php" >
                    <input type="hidden" name="no_of_file_upload_for_reconciliation" id="no_of_file_upload_for_reconciliation" value="1" >
                    <div class="row">
                        <div class="col-sm-3">
                            <?php $selectedCarrier = ''; ?>
                            <div class="form-group">
                                <label>Process Option</label>
                                <div class="input-group">
                                    <span class="input-group-addon"> <i class="fa fa-shopping-cart"></i></span>
                                    <?php 
                                    $arrayProcessingOption = ['invoice_check'=>'Invoice Check','reconcile'=>'Reconciliation'];
                                    echo Ddl::generateArrayDDL('processing_option', $arrayProcessingOption, 'both' , "", ' class="form-control form-filter select2"', "", 'processing_option' );?>
                                    <?php //echo Ddl::generateCarrierDDLWithImage('carrier_id', $selectedCarrier, 'id', ' class="bs-select form-control" data-live-search="true" data-show-subtext="true"','carrier_id','Carrier Id'," is_reconcile = '1'"); ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <?php $selectedCarrier = ''; ?>
                            <div class="form-group">
                                <label>Select Carrier</label>
                                <div class="input-group">
                                    <span class="input-group-addon"> <i class="fa fa-shopping-cart"></i></span>
                                    <?php echo Ddl::generateCarrierDDLWithImage('carrier_id', $selectedCarrier, 'id', ' class="bs-select form-control select-carrier-dropdown" data-live-search="true" data-show-subtext="true"','carrier_id','Carrier Id'," is_reconcile = '1'"); ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 second-file-upload" style="display: none;">
                            <div class="fileinput fileinput-new" data-provides="fileinput">
                                <div class="form-group">
                                    <label> Shipment File</label>
                                    <div class="input-group input-large">
                                        <div class="form-control uneditable-input input-fixed input-medium" data-trigger="fileinput">
                                            <i class="fa fa-file fileinput-exists"></i>&nbsp;
                                            <span class="fileinput-filename"> </span>
                                        </div>
                                        <span class="input-group-addon btn default btn-file">
                                        <span class="fileinput-new"> Select file </span>
                                        <span class="fileinput-exists"> Change </span>
                                        <input type="file" name="csv_for_priceing_2" id="csv_for_priceing_2">
                                    </span>
                                        <a href="javascript:;" class="input-group-addon btn red fileinput-exists" data-dismiss="fileinput"> Remove </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="fileinput fileinput-new" data-provides="fileinput">
                                <div class="form-group">
                                    <label> Shipment Details</label>
                                    <div class="input-group input-large">
                                        <div class="form-control uneditable-input input-fixed input-medium" data-trigger="fileinput">
                                            <i class="fa fa-file fileinput-exists"></i>&nbsp;
                                            <span class="fileinput-filename"> </span>
                                        </div>
                                        <span class="input-group-addon btn default btn-file">
                                        <span class="fileinput-new"> Select file </span>
                                        <span class="fileinput-exists"> Change </span>
                                        <input type="file" name="csv_for_priceing" id="csv_for_pricing">
                                    </span>
                                        <a href="javascript:;" class="input-group-addon btn red fileinput-exists" data-dismiss="fileinput"> Remove </a>
                                        <a href="javascript:;" class="input-group-addon btn blue" id="btnSubmitImport" >Upload</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
                <div class="row">
                    <div class="col-sm-12">
                        <div id="console_window">

                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="portlet light">
            <div class="portlet-title">
                <div class="caption"> <i class="fa fa-list"></i>
                    Reconciliation List
                </div>
                <div class="actions">
                    <div class="table-actions-wrapper">
                        <span> </span>
                        <select class="table-group-action-input form-control input-inline input-small input-sm" id="reconciliation_action" name="reconciliation_action" >
                            <option value="">Select Action</option>
                            <option value="manually_approved">Re Process</option>
                        </select>
                        <button class="btn btn-sm btn-default table-group-action-submit" type="button" id="reconciliation_action_btn" >
                            <i class="fa fa-check"></i> Submit
                        </button>
                    </div>
                </div>
            </div>
            <div class="portlet-body">
                <div class="row">
                    <div class="col-sm-12">
                        <?php
                        $this->flashMsg->display();
                        ?>
                    </div>
                </div>
                <table class="table table-striped table-bordered table-hover table-condensed" id="reconciliation_datatable">
                    <thead>
                    <tr role="row" class="heading">
                        <th>Actions</th>
                        <th>Status</th>
                        <th>Invoice Date</th>
                        <th>Invoice Number</th>
                        <th>Carrier</th>
                        <th>Supplier Total Weight</th>
                        <th>Reconciliation Total Weight</th>
                        <th>Supplier Total Pieces</th>
                        <th>Reconciliation Total Pieces</th>
                        <th>Supplier Total Amount</th>
                        <th>Reconciliation Total Amount</th>
                    </tr>
                    <tr role="row" class="filter">
                        <td>
                            <div class="margin-bottom-5">
                                <button class="btn btn-xs blue filter-submit btn-outline" ><i class="fa fa-search"></i> </button>
                                <button class="btn btn-xs red filter-cancel mt-ladda-btn ladda-button btn-outline"><i class="fa fa-times"></i> </button>
                            </div>
                        </td>
                        <td>
                            <select class="form-control form-filter input-sm change_status" name="status">
                                <option value="">Select</option>
                                <option value="pending">Pending</option>
                                <option value="processed">Processed</option>
                                <option value="approved">Approved</option>
                            </select>
                        </td>
                        <td>
                            <div class="input-group date date-picker margin-bottom-5" data-date-format="dd-mm-yyyy">
                                <input type="text" class="form-control form-filter input-sm" name="search_date_from" placeholder="From" data-date-format="yyyy-mm-dd" readonly>
                                <span class="input-group-btn">
                                    <button class="btn btn-sm" type="button"><i class="fa fa-calendar"></i></button>
                                </span>
                            </div>
                            <div class="input-group date date-picker" data-date-format="dd-mm-yyyy">
                                <input type="text" class="form-control form-filter input-sm" name="search_date_to" placeholder="To" data-date-format="yyyy-mm-dd" readonly>
                                <span class="input-group-btn">
                                    <button class="btn btn-sm" type="button"><i class="fa fa-calendar"></i></button>
                                </span>
                            </div>
                        </td>
                        <td>
                            <input type="text" class="form-control form-filter input-sm " name="invoice_number" id ="invoice_number" />
                        </td>
                        <td>
                            <div >
                                <?php echo Ddl::generateCarrierDDLWithImage('carrier_id', '', 'id', ' class="change_status bs-select form-control form-filter input-sm" data-live-search="true" data-show-subtext="true"'); ?>
                            </div>
                        </td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
        
        <!--modal to view user audit on every page-->
<div class="modal fade" tabindex="-1" role="dialog" id="show_email_supplier_query_modal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Email Send to Supplier</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="user-audit-no-record"></div>
                        <!--                        <table class="table table-bordered table-hover user-audit-table">
                                                    <thead>
                                                    <tr>
                                                        <th>User Name</th>
                                                        <th>Message</th>
                                                        <th>Dated</th>
                                                        <th>Action</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody id="audit_detail_view_html">
                                                    </tbody>
                                                </table>-->
                        <table class="table table-striped table-bordered table-hover user-audit-table table-condensed"
                               id="manage-data-table-show-supplier-email">
                            <thead>
                            <tr role="row" class="heading">
                                <th>Date</th>
                                <th>Email To</th>
                                <th>Email CC</th>
                                <th>Subject</th>
                                <th>Body</th>
                            </tr>
                            </thead>
                            <tbody id="spplier_email_detail_view_html">
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
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

/* ------------------------------------------------------------------------------ */
// create and render page
$page = new Page(CONFIG_TEMPLATE_ADMIN);
$page->show();
?>