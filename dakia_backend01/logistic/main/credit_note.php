<?php
// get settings
require_once("../includes/settings/config.inc.php");
require_once("../Classes/PHPExcel.php");

include_classes([   
                    'ivisualcomponent','ddl.inc'
                ],'library');
include_classes([   'creditnote.class',
                    'creditnotefilter.class',
                    'invoices.class',
                    'invoicesfilter.class']);

class Page extends BasePage {
    /*     * *
     * Controller logic
     */

    private $user = "";
    private $creditNoteFilter = "";
    private $msg;

    protected function init() {
        $this->breadCrumb['data'] = array(
            'index.php' => Translation::GetCaption("HOME"),
            "Credit Note List"
        ); 
        $this->user = SessionManager::getUser();
        /*
         * DataTable handlings
         */
        if (isset($_GET['action']) && $_GET['action'] == "credit_note_ajax") {

            $this->applyFilter($this->form_vars);

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
                    $this->creditNoteFilter->orderBy(strtolower($dataTableColumnName), $orderFalse);
                }
            } else {
                $this->creditNoteFilter->orderBy(strtolower("cn.id"), "DESC");
            }
            /*
             * Pagination Logic Implemented
             * 
             */


            $iDisplayLength = intval($_REQUEST['length']);
            $iDisplayLength = $iDisplayLength < 0 ? 20 : $iDisplayLength;
            $iDisplayStart = intval($_REQUEST['start']);
            $sEcho = intval($_REQUEST['draw']);
            $this->creditNoteFilter->setRowsPerPage($iDisplayLength);
            $this->creditNoteFilter->setOffset($iDisplayStart);
            $creditNoteFilterObj = $this->creditNoteFilter->getList("cn.*, ua.user_account, CONCAT(u.first_name,' ',u.last_name) AS user_name");
            $iTotalRecords = $this->creditNoteFilter->getCount();
            $setDataArr = array();
            foreach ($creditNoteFilterObj as $obj) {
                if (!empty($obj->getCreditDate())) {
                    $credit_date = formatDate(date("d-m-Y", $obj->getCreditDate()));
                } else {
                    $credit_date = "";
                }
                if (!empty($obj->getDateCreated())) {
                    $date_created = formatDate(date("d-m-Y", $obj->getDateCreated()));
                } else {
                    $date_created = "";
                }
                $pdfUrl = SETTING_URL . '_assets/credit_note/pdf/';
                if ($obj->getPdf() != "") {
                    $pdf = $pdfUrl . $obj->getPdf();
                } else {
                    $pdf = '';
                }
				$isEmail = $obj->getIsEmail();

                $currentArr = array();
                $currentArr['credit_note_number'] = $obj->getCreditNoteNumber();
                $currentArr['invoice_number'] = $obj->getInvoiceNumber();
                $currentArr['credit_date'] = $credit_date;
                $currentArr['date_created'] = $date_created;
                $currentArr['user_account_id'] = $obj->getUserAccount();
                $currentArr['net_amount'] = $obj->getNetAmount();
                $currentArr['vat_amount'] = $obj->getVatAmount();
                $currentArr['credit_total'] = $obj->getCreditTotal();
                $currentArr['credit_note_type'] = $obj->getCreditNoteType();
                $currentArr['credit_note_by'] = $obj->getUserName();
                $currentArr['active'] = "Yes";
                $account = new CustomerAccount($obj->getUserAccountId());
                $currentArr['actions'] = '';
                $currentArr['actions'] .= '<div class="btn-group" data-container="body" >
                                            <button class="btn btn-xs blue mt-ladda-btn ladda-button btn-outline dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Tools
                                                <i class="fa fa-angle-down"></i>
                                            </button>
                                        <ul class="dropdown-menu" >';
                if ($pdf != "") {
                    $clickFunction = "";
                    if($_GET['type'] == "purchase") {
                        $clickFunction = "updateReadCreditNote(".$obj->getId().")";
                    }
                    $currentArr['actions'] .= '<li>
                                                    <a title="Download Pdf" href="' . $pdf . '" data-pdf_link="' . $pdf . '" target="_blank" onclick="'.$clickFunction.'" >
                                                        <span class="fa fa-download"></span> Download Pdf
                                                    </a>
                                                </li>';
                    if($_GET['type'] == "sale") {
                        if($obj->getIsEmail() == "0") {
                        $currentArr['actions'] .= '<li>
                                                        <form action="javascript:;" id="reset_pdf_' . $obj->getId() . '" method="post">
                                                            <input type="hidden" name="credit_note_id" value="' . $obj->getId() . '" />
                                                            <input type="hidden" name="pdf" value="' . $obj->getPdf() . '" />
                                                            <a title="Reset Pdf" href="javascript:;" onclick="reset_pdf('."'".'reset_pdf_' . $obj->getId() ."'".')" >
                                                                <span class="fa fa-download"></span> Reset Pdf
                                                            </a>
                                                        </form>
                                                    </li>';
                        }
                        $currentArr['actions'] .= '<li>
                                                        <a title="Send Email" href="javascript:;" data-credit_note_id="' . $obj->getId() . '" data-invoice_number="' . $obj->getInvoiceNumber() . '"  data-credit_note_number="' . $obj->getCreditNoteNumber() . '" data-biling_email="' . $account->getBillingEmail() . '" data-company="' . (trim($account->getBillingContact())!='' ? $account->getBillingContact():$account->getCompany()) . '" data-credit_date="' . $credit_date . '"  data-invoice_number="' . $obj->getInvoiceNumber() . '"  data-credit_note_pdf="' . $pdf . '" onclick="send_email_credit_note(this)"  >
                                                            <span class="fa fa-download"></span> Send Email
                                                        </a>
                                                    </li>';
                    }
                } else {
                    $currentArr['actions'] .= '<li>
                                                    <a title="Edit" href="credit_note_details.php?id=' . $obj->getId() . '" data-pdf_link="' . $pdf . '" target="_blank" >
                                                        <span class="fa fa-edit"></span> Edit
                                                    </a>
                                                </li>';
                }
                /*$currentArr['actions'] .= '<li>
                                                <form action="credit_note.php?action=single_credit_note_csv&type='.$_GET['type'].'" id="single_credit_note_' . $obj->getId() . '_csv" method="post">
                                                    <input type="hidden" name="credit_note_id" value="' . $obj->getId() . '" />
                                                    <a title="Download CSV" href="javascript:;" onclick="document.getElementById(' . "'" . 'single_credit_note_' . $obj->getId() . '_csv' . "'" . ').submit()" >
                                                        <span class="fa fa-download"></span> Download CSV
                                                    </a>
                                                </form>
                                            </li>';*/
                 $currentArr['actions'] .= "<li>"
                                            . "<a href='' id='user-audit-detail-view' data-target='#user-audit-view-modal' data-log_key='" . $obj->getId() . "' data-log_name='credit_note' data-toggle='modal'> <i class='fa fa-list'></i> View Audit</a>"
                                            . "</li>";
                $currentArr['actions'] .= '</ul>
                                            </div>';
                $setDataArr [] = $currentArr;
            }
            $setDataArrJson['data'] = $setDataArr;
            $setDataArrJson['draw'] = $sEcho;
            $setDataArrJson['recordsTotal'] = $iTotalRecords;
            $setDataArrJson['recordsFiltered'] = $iTotalRecords;
            echo json_encode($setDataArrJson);
            die;
        }

        if (isset($_GET['action']) && $_GET['action'] == 'credit_note_download_excel') {
            $this->applyFilter($this->form_vars);
            $creditNoteFilterObj = $this->creditNoteFilter->getList("cn.*, ua.user_account, CONCAT(u.first_name,' ',u.last_name) AS user_name");
            //$heading = ["Id", "Account", "Invoice Type", "Invoice Number", "Credit Note Type", "HAWB", "Credit Note Heading", "Credit Date", "Net Amount", "Vat Amount", "Credit Total", "Credit Note By", "Date Created"];
            $heading = ["Type", "Account REF", "Nominal Account REF", "Department ID", "Date", "REF", "Details", "Net Amount", "Tax Code", "Tax Amount"];
            if (!empty($creditNoteFilterObj)) {
                $objPHPExcel = new PHPExcel();
                $rowNum = 1;
                $colNum = 'A';
                foreach ($heading as $h) {
                    $cell_name = $colNum . $rowNum;
                    $objPHPExcel->getActiveSheet()->getStyle($cell_name)->getFont()->setBold(true);
                    $objPHPExcel->getActiveSheet()->SetCellValue($cell_name, $h);
                    $colNum++;
                }
                $rowNum = 2;
                foreach ($creditNoteFilterObj as $creditNoteObj) {
                    $colNum = 'A';
                    $objPHPExcel->getActiveSheet()->SetCellValue($colNum . $rowNum, 'SC');
                    $colNum++;
                    $objPHPExcel->getActiveSheet()->SetCellValue($colNum . $rowNum, $creditNoteObj->getUserAccount());
                    $colNum++;
                    $objPHPExcel->getActiveSheet()->SetCellValue($colNum . $rowNum, "4000");//$creditNoteObj->getInvoiceType()
                    $colNum++;
                    $objPHPExcel->getActiveSheet()->SetCellValue($colNum . $rowNum, "");//$creditNoteObj->getInvoiceNumber()
                    $colNum++;
                    if ($creditNoteObj->getCreditDate() != "") {
                        $creditDate = date("d-m-Y", $creditNoteObj->getCreditDate());
                    } else {
                        $creditDate = "";
                    }
                    $objPHPExcel->getActiveSheet()->SetCellValue($colNum . $rowNum, $creditDate);
                    $colNum++;
                    
                    $objPHPExcel->getActiveSheet()->SetCellValue($colNum . $rowNum, $creditNoteObj->getCreditNoteNumber());
                    $colNum++;
                    $objPHPExcel->getActiveSheet()->SetCellValue($colNum . $rowNum, $creditNoteObj->getCreditNoteHeading());
                    $colNum++;
                    
                    $objPHPExcel->getActiveSheet()->SetCellValue($colNum . $rowNum, $creditNoteObj->getNetAmount());
                    $colNum++;
                    
                    $objPHPExcel->getActiveSheet()->SetCellValue($colNum . $rowNum, "");
                    $colNum++;
                    
                    $objPHPExcel->getActiveSheet()->SetCellValue($colNum . $rowNum, $creditNoteObj->getVatAmount());
                    $colNum++;
                    /*
                    
                    if ($creditNoteObj->getCreditNoteBy() > 0) {
                        $userAccountObj = new CustomerAccount($creditNoteObj->getCreditNoteBy());
                        $noteBy = $userAccountObj->getUserAccount();
                    } else {
                        $noteBy = "";
                    }
                    $objPHPExcel->getActiveSheet()->SetCellValue($colNum . $rowNum, $noteBy);
                    $colNum++;
                    if ($creditNoteObj->getDateCreated() != "") {
                        $dateCreated = date("d F Y", $creditNoteObj->getDateCreated());
                    } else {
                        $dateCreated = "";
                    }
                    $objPHPExcel->getActiveSheet()->SetCellValue($colNum . $rowNum, $dateCreated);*/
                    $rowNum++;
                }
                $fileName = "credit_note_list_" . time();
                header('Content-Type: application/vnd.ms-excel');
                header('Content-Disposition: attachment;filename=' . $fileName . '.xls'); // file name of excel
                header('Cache-Control: max-age=0');
                header('Cache-Control: max-age=1');
                header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
                header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
                header('Cache-Control: cache, must-revalidate');
                header('Pragma: public'); // HTTP/1.0
                $objWorksheet = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
                $objWorksheet->setIncludeCharts(true);
                $objWorksheet->save('php://output');
                echo "excel download successfully";
                die;
            } else {
                $this->msg = 'No record found to download.';
                $this->flashMsg->error($this->msg);
            }
        }

        if (isset($_GET['action']) && $_GET['action'] == 'single_credit_note_csv') {
            $output = array();
            $creditNoteId = $this->form_vars['credit_note_id'];
			if($_GET['type'] == 'purchase') {
                $this->readCreditNoteUpdate($creditNoteId);
            }
			
            $csvData = $this->creditNoteDetailCsv($creditNoteId);
            $output['path'][] = $csvData;
            die;
        }
        
        if (isset($_GET['action']) && $_GET['action'] == 'reset_pdf') {
            $output = [];
            $credit_note_id = $this->form_vars['credit_note_id'];
            $pdf = $this->form_vars['pdf'];
            if (trim($pdf) != '') {
                unlink('../_assets/credit_note/pdf/' . $pdf);
            }
            $creditNoteObj = new CreditNote($credit_note_id);
            $creditNoteObj->setPdf('');
            $creditNoteObj->save();
            $output['status'] = "success";
            $output['message'] = "Credit note is ready for edit.";
            echo json_encode($output);
            die;
        }
		
		if (isset($_GET['action']) && $_GET['action'] == 'send_email_credit_note') {
            $mailObj = new SendEmail();
            $sendTo = explode(",", $this->form_vars['send_credit_note_email_to']);
            $sendCc = explode(",", $this->form_vars['send_credit_note_email_cc']);
            $sendSubject = $this->form_vars['send_credit_note_email_subject'];
            $sendMessage = $this->form_vars['send_credit_note_email_message'];
            $creditNoteId = $this->form_vars['credit_note_id'];
            $creditNote = new CreditNote($creditNoteId);
            $creditNote->setIsEmail(1);
            $creditNote->save();
            
            $output = [];
            $attachment_file = $_FILES['credit_note_csv_file'];
            if (isset($attachment_file) && !empty($attachment_file['name'][0]) && is_array($attachment_file)) {
                foreach($attachment_file['name'] as $key => $fileName) {
                    $file_name = $fileName;
                    $path_parts = pathinfo($file_name);
                    $ext = strtolower($path_parts['extension']);
                    $basename = $path_parts['basename'];
                    if ($ext == 'csv' || $ext == 'xlsx') {
                        $account = $this->user->getAccount();
                        $new_file_name = "email_attachment_" . time() . "_" . $basename;
                        $relPath = '../_assets/InvoicesFiles/email_attachment/' . $new_file_name;
                        if (!file_exists("../_assets/InvoicesFiles/email_attachment/")) {
                            @mkdir("../_assets/InvoicesFiles/email_attachment/", 0775);
                        }
                        if (move_uploaded_file($attachment_file['tmp_name'][$key], $relPath)) {
                            $emailAttachmentFiles[] = $relPath;
                            $output['upload_status'] = "success";
                        } else {
                            $output['upload_status'] = 'fail1';
                        }
                    } else {
                        $output['upload_status'] = 'fail2';
                    }
                }
                if(isset($output['upload_status']) && ($output['upload_status'] == "success")) {
                    $output['email_response'] = $mailObj->autoInvoiceEmailSend($sendTo, $sendSubject, $sendMessage, $sendCc,$emailAttachmentFiles);
                }
            } else {
                $output['email_response'] = $mailObj->autoInvoiceEmailSend($sendTo, $sendSubject, $sendMessage, $sendCc);
            }
            if((isset($output['email_response']['status'])) && ($output['email_response']['status'] == "success")) {
                $this->flashMsg->success($output['email_response']['message']);
            }
        }
        
        if (isset($this->form_vars['action']) && $this->form_vars['action'] == 'update_read_credit_note') {
            $output = [];
            $creditNoteId = $this->form_vars['creditNoteId'];
            $this->readCreditNoteUpdate($creditNoteId);
            $output['status'] = "success";
            $output['message'] = "Credit Note Read Successfully";
            echo json_encode($output);
            die;
        }
		
		
    }

    protected function creditNoteDetailCsv($creditNoteId) {
        $returnData = array();
        $creditNoteDetailFilter = new CreditNoteDetailsFilter();
        $creditNoteDetailFilter->where(["cnd.credit_note_id" => $creditNoteId]);
        $creditNoteDetailFilterObj = $creditNoteDetailFilter->getList();

        $heading = ["id", "Credit Note Id",  "Hawb", "Date Booked", "Reference", "Invoice Amount", "Chargeable Amount", "Credit Amount", "Description", "Is Vatable", "Vat Amount", "Added By", "Date Created"];

        $fileName = "CN_" . $creditNoteId . "_" . time() . ".csv";

        if (count($creditNoteDetailFilterObj) > 0) {
            $returnString = "";
            foreach ($heading as $h) {
                $returnString .= $h . ",";
            }
            foreach ($creditNoteDetailFilterObj as $creditNoteObj) {
                if ($creditNoteObj->getDateBooked() != "") {
                    $dateBooked = date("d-m-Y", $creditNoteObj->getDateBooked());
                } else {
                    $dateBooked = " ";
                }
                if ($creditNoteObj->getDateCreated() != "") {
                    $dateCreated = date("d-m-Y", $creditNoteObj->getDateCreated());
                } else {
                    $dateCreated = " ";
                }
                $userObj = new User($creditNoteObj->getAddedBy());
                $returnString .= "\r\n";
                $returnString .= cleanCsvCall($creditNoteObj->getId()) . ",";
                $returnString .= cleanCsvCall($creditNoteObj->getCreditNoteId()) . ",";
                $returnString .= cleanCsvCall($creditNoteObj->getHawb()) . ",";
                $returnString .= $dateBooked . ",";
                $returnString .= cleanCsvCall($creditNoteObj->getReference()) . ",";
                $returnString .= cleanCsvCall($creditNoteObj->getInvoiceAmount()) . ",";
                $returnString .= cleanCsvCall($creditNoteObj->getChargeableAmount()) . ",";
                $returnString .= cleanCsvCall($creditNoteObj->getCreditAmount()) . ",";
                $returnString .= cleanCsvCall($creditNoteObj->getDescription()) . ",";
                $returnString .= cleanCsvCall($creditNoteObj->getIsVatable()) . ",";
                $returnString .= cleanCsvCall($creditNoteObj->getVatAmount()) . ",";
                $returnString .= cleanCsvCall($userObj->getFirstName()) . " " . cleanCsvCall($userObj->getLastName()) . ",";
                $returnString .= $dateCreated . ",";
            }
        }
        header("Content-type: text/csv");
        header("Content-Disposition: attachment; filename=" . $fileName);
        header("Pragma: no-cache");
        header("Expires: 0");
        echo $returnString;
        $returnData['FILENAME'] = $fileName;
        return $returnData;
        die;
    }

	public function readCreditNoteUpdate($creditNoteId) {
        $creditNote = new CreditNote($creditNoteId);
        $creditNote->setIsRead(1);
        $creditNote->save();
        return;
    }
    protected function applyFilter($form_vars) {
        $this->form_vars = $form_vars;
        $this->creditNoteFilter = new CreditNoteFilter();
        $this->creditNoteFilter->join("user u", ['cn.added_by' => 'u.id']);
        if ($_GET['type'] == "sale") {
            $this->creditNoteFilter->join("customer_account ua", ['cn.user_account_id' => 'ua.id']);
            $this->creditNoteFilter->where(['cn.credit_note_by' => $this->user->getUserAccountId()]);
        } else {
            $this->creditNoteFilter->join("customer_account ua", ['cn.credit_note_by' => 'ua.id']);
            $this->creditNoteFilter->where(['cn.user_account_id' => $this->user->getUserAccountId()]);
			$this->creditNoteFilter->where(['cn.is_email' => 1]);
        }

//        if ($this->user->getUserType() == User::USER_TYPE_ADMIN) {
//            $userAccountArry = CustomerAccount::accountSubAccount($this->user->getUserAccountId(), 0, true);
//            $this->creditNoteFilter->whereIn("cn.user_account_id", $userAccountArry);
//        } else {
//            $userAccountArry = CustomerAccount::accountSubAccount($this->user->getUserAccountId(), 0, false);
//            $this->creditNoteFilter->whereIn("cn.user_account_id", $userAccountArry);
//        }
        /*
         * Column filter
         * For search
         */
        if (isset($this->form_vars['action']) && $this->form_vars['action'] == 'filter') {
            $filterArray = [];

            $credit_note_number = $this->form_vars['credit_note_number'];
            if (!empty($credit_note_number))
                $filterArray['cn.credit_note_number'] = $credit_note_number;
            
            $invoice_number = $this->form_vars['invoice_number'];
            if (!empty($invoice_number))
                $filterArray['cn.invoice_number'] = $invoice_number;

            $account = $this->form_vars['user_account_id'];
            if (!empty($account))
                $filterArray['cn.user_account_id'] = $account;

            $net_amount = $this->form_vars['net_amount'];
            if (!empty($net_amount))
                $filterArray['cn.net_amount'] = $net_amount;

            $vat_amount = $this->form_vars['vat_amount'];
            if (!empty($vat_amount))
                $filterArray['cn.vat_amount'] = $vat_amount;

            $credit_total = $this->form_vars['credit_total'];
            if (!empty($credit_total))
                $filterArray['cn.credit_total'] = $credit_total;

            $credit_note_type = $this->form_vars['credit_note_type'];
            if (!empty($credit_note_type))
                $filterArray['cn.credit_note_type'] = $credit_note_type;

            $this->creditNoteFilter->where($filterArray);

            $credit_date_from = $this->form_vars['credit_date_from'];
            $credit_date_to = $this->form_vars['credit_date_to'];
            if (!empty($credit_date_from) && !empty($credit_date_to))
                $this->creditNoteFilter->whereBetween('cn.credit_date', date('Y-m-d 00:00:00', strtotime($credit_date_from)), date('Y-m-d 23:59:59', strtotime($credit_date_to)));

            $date_created_from = $this->form_vars['date_created_from'];
            $date_created_to = $this->form_vars['date_created_to'];
            if (!empty($date_created_from) && !empty($date_created_to))
                $this->creditNoteFilter->whereBetween('cn.date_created', date('Y-m-d 00:00:00', strtotime($date_created_from)), date('Y-m-d 23:59:59', strtotime($date_created_to)));

            $added_by = $this->form_vars['added_by'];
            if (!empty($added_by))
                $this->creditNoteFilter->where("(u.first_name LIKE '%" . $added_by . "%' OR u.last_name LIKE '%" . $added_by . "%')");
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
        <?php
    }

    public function addPagelavelJs() {
        ?>
        <script src="../assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.js" type="text/javascript"></script>
        <script type="text/javascript" src="../assets/global/plugins/select2/js/select2.full.min.js" type="text/javascript"></script>
        <script src="../assets/global/scripts/app.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/bootstrap-select/js/bootstrap-select.min.js" type="text/javascript"></script>

        <script src="../assets/global/scripts/datatable.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/datatables/datatables.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/bootstrap-sweetalert/sweetalert.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js" type="text/javascript"></script>


        <script type="text/javascript">
            var grid = null;
            var DataTableFun = function () {
                var handleDataTable = function () {
                    var datatableurl = "credit_note.php?action=credit_note_ajax&type=sale";
                    grid = new Datatable();
                    grid.init({
                        src: $("#invoice_send_databale"),
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
                                "url": datatableurl, // ajax source
                                headers: {

                                },
                            },
                            "bStateSave": true,
                            "columns": [
                                {"data": "actions", "bSortable": false},
                                {"data": "credit_note_number"},
                                {"data": "invoice_number"},
                                {"data": "credit_date"},
                                {"data": "date_created"},
                                {"data": "user_account_id"},
                                {"data": "net_amount"},
                                {"data": "vat_amount"},
                                {"data": "credit_total"},
                                {"data": "credit_note_type"},
                                {"data": "credit_note_by"},
                                {"data": "active"}
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
            var CreditNotePurchasegrid = null;
            var InvoiceFromDataTableFun = function () {
                var InvoiceFromhandleDataTable = function () {
                    var datatableurl = "credit_note.php?action=credit_note_ajax&type=purchase";
                    CreditNotePurchasegrid = new Datatable();
                    CreditNotePurchasegrid.init({
                        src: $("#invoice_received_databale"),
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
                                "url": datatableurl, // ajax source
                                headers: {

                                },
                            },
                            "bStateSave": true,
                            "columns": [
                                {"data": "actions", "bSortable": false},
                                {"data": "credit_note_number"},
                                {"data": "invoice_number"},
                                {"data": "credit_date"},
                                {"data": "date_created"},
                                {"data": "user_account_id"},
                                {"data": "net_amount"},
                                {"data": "vat_amount"},
                                {"data": "credit_total"},
                                {"data": "credit_note_type"},
                                {"data": "credit_note_by"},
                                {"data": "active"}
                            ]
                        }
                    });
                }
                return {
                    //main function to initiate the module
                    init: function () {
                        InvoiceFromhandleDataTable();
                    }
                };
            }();
            $(document).ready(function () {
                DataTableFun.init();
                InvoiceFromDataTableFun.init();
                if ($('.date-picker').length > 0) {
                    //init date pickers
                    $('.date-picker').datepicker({
                        autoclose: true
                    });
                }
                
                var elindex = 0;
                var limt = 4;
                $(".initial-button").hide();
                if (jQuery('#elindex-hardcode').length > 0) {
                    elindex = jQuery('#elindex-hardcode').val();
                }
                $(document).on('click', '.add_more_email_attachment_send_inv_invoice', function () {
                    if(elindex < limt) { 
                        elindex++;
                        var clone = $(this).parent().parent().clone();
                        var invCsv = $(clone).find('.credit_note_csv_file').attr('name');
                        var invCsvId = $(clone).find('.credit_note_csv_file').attr('id');

                        $(this).remove();

                        $(clone).find('input').val('');
                        $(clone).find('.credit_note_csv_file').attr('name', invCsv.replace(/\d+/, elindex));
                        $(clone).find('.credit_note_csv_file').attr('id', invCsvId.replace(/\d+/, elindex));
                        $(clone).find('button.remove_email_attachment_send_inv_invoice').show();
                        $(clone).find('button.remove_email_attachment_send_inv_invoice').removeClass('initial-button');

                        $(clone).appendTo($('#send_email_inv_invoice_clone'));
                        if(elindex > (parseInt(limt) - parseInt(1))) {
                            $('.add_more_email_attachment_send_inv_invoice').hide();
                        }
                    } else {
                        swal({
                                type: 'success',
                                html:true,
                                title: "Success!",
                                text: "Sorry only 5 attachment allowed"
                            });
                    }
                });
                $(document).on('click', '.remove_email_attachment_send_inv_invoice', function () {
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
                                    if ($('#send_email_inv_invoice_clone .remove_email_attachment_send_inv_invoice').length > 1) {
                                        $(el).parent().parent().remove();
                                        elindex--;
                                        if ($('.add_more_email_attachment_send_inv_invoice').length == 0) {
                                            var addMore = $(el).parent().find('.add_more_email_attachment_send_inv_invoice').clone();
                                            $('#send_email_inv_invoice_clone .remove_email_attachment_send_inv_invoice').last().parent().prepend(addMore);
                                        }
                                        if(elindex < limt) {
                                            $('.add_more_email_attachment_send_inv_invoice').show();
                                        }
                                    } else {
                                        $(el).parent().find('input').val('');
                                    }
                                }
                            });
                });
            });
            function get_excel_purchase() {
                $('#purchase_credit_note_form').submit();
            }
            function get_excel_sale() {
                $('#sale_credit_note_form').submit();
            }
            function reset_pdf(id) {
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
                },function (isConfirm) {
                    if (isConfirm) {
                        var form_data = $("#"+id).serializeArray();
                        $.ajax({
                            url: "credit_note.php?action=reset_pdf",
                            data: form_data,
                            type: "post",
                            dataType: "json",
                            success: function (response) {
                                if(response.status == "success") {
                                    grid.getDataTable().ajax.reload();
                                    CreditNotePurchasegrid.getDataTable().ajax.reload();
                                }
                            }
                        });
                    }
                });
            }
			
            function send_email_credit_note(obj) {
                var to = $(obj).data('biling_email');
                var credit_note_id = $(obj).data('credit_note_id');
                var credit_note_number = $(obj).data('credit_note_number');
                var pdf = $(obj).data('credit_note_pdf');
                var credit_date = $(obj).data('credit_date');
                var company = $(obj).data('company');
                var mesage = "Dear " + company + ", \n\n Please see below links for the credit note number " + credit_note_number + " date " + credit_date + " back-up data. \n\n Your Credit note \n\n " + pdf + " \n\n  #CREDITNOTELINK# \n\n if you have any queries, please contect us at finace@oneworldexpress.com";
                
                $('#send_credit_note_email_to').val(to);
                $('#send_credit_note_email_subject').val(credit_note_number);
                $('#send_credit_note_email_message').val(mesage);
                $('#send_credit_note_email_credit_note_number').val(credit_note_number);
                $('#send_credit_note_email_credit_note_id').val(credit_note_id);
                
                $('#send_credit_note_email_modal').modal('show');
            }
            function updateReadCreditNote(creditNoteId) {
                var form_data = new FormData();
                form_data.append('creditNoteId', creditNoteId);
                form_data.append('action', 'update_read_credit_note');
                $.ajax({
                        url: "credit_note.php",
                        cache: false,
                        contentType: false,
                        processData: false,
                        data: form_data,
                        type: 'post',
                        dataType: 'json',
                        success: function (data) {
                             console.log(data);
                        },
                        error: function () {
                                //alert('error handing here');
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
        <div class="portlet light">
            <div class="portlet-title">
                <div class="caption"> <i class="fa fa-list"></i>
                    Credit Notes
                </div>
                <div class="actions">
                </div>
            </div>
            <div class="portlet-body">
                <div class="row">
                    <?php
                        $this->flashMsg->display();
                    ?>
               </div>
                <div class="tabbable-line">
                    <ul class="nav nav-tabs ">
                        <li class="active">
                            <a href="#invoices_sale" data-toggle="tab"> Sale Credit Notes </a>
                        </li>
                        <li>
                            <a href="#invoices_purchase" data-toggle="tab"> Purchase Credit Notes </a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane active" id="invoices_sale">
                            <div class="table-container">
                                <div class="table-actions-wrapper">
                                    <button class="btn btn-sm btn-default table-group-action-submit" id="csv_download_send_btn" onclick="get_excel_sale()" data-original-title="Download csv" title="Download csv"><span></span><i class="fa fa-download"></i>&nbsp;Download CSV</button>
                                </div>
                                <form action="credit_note.php?action=credit_note_download_excel&type=sale" method="post" id="sale_credit_note_form" >
                                    <input type="hidden" name="action" value="filter" />
                                    <table class="table table-striped table-bordered table-hover table-condensed" id="invoice_send_databale">
                                        <thead>
                                            <tr role="row" class="heading">
                                                <th width="75">Actions</th>
                                                <th>Credit Note Number</th>
                                                <th>Invoice Number</th>
                                                <th>Credit Date</th>
                                                <th>Created Date</th>
                                                <th>Account</th>
                                                <th>Net Amount</th>
                                                <th>Vat Amount</th>
                                                <th>Credit Amount</th>
                                                <th>Credit Note Type</th>
                                                <th>Generated By</th>
                                                <th>Active</th>
                                            </tr>
                                            <tr role="row" class="filter">
                                                <td>
                                                    <div class="margin-bottom-5">
                                                        <button class="btn btn-xs blue filter-submit btn-outline" ><i class="fa fa-search"></i> </button>
                                                        <button class="btn btn-xs red filter-cancel mt-ladda-btn ladda-button btn-outline"><i class="fa fa-times"></i> </button>
                                                    </div>

                                                </td>
                                                <td>
                                                    <input type="text" class="form-control form-filter input-sm" name="credit_note_number">
                                                </td>
                                                <td>
                                                    <input type="text" class="form-control form-filter input-sm" name="invoice_number">
                                                </td>
                                                <td>
                                                    <div class="input-group date date-picker margin-bottom-5" data-date-format="dd-mm-yyyy">
                                                        <input type="text" class="form-control form-filter input-sm" readonly name="credit_date_from" placeholder="From">
                                                        <span class="input-group-btn">
                                                            <button class="btn btn-sm default" type="button">
                                                                <i class="fa fa-calendar"></i>
                                                            </button>
                                                        </span>
                                                    </div>
                                                    <div class="input-group date date-picker" data-date-format="dd-mm-yyyy">
                                                        <input type="text" class="form-control form-filter input-sm" readonly name="credit_date_to" placeholder="To">
                                                        <span class="input-group-btn">
                                                            <button class="btn btn-sm default" type="button">
                                                                <i class="fa fa-calendar"></i>
                                                            </button>
                                                        </span>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="input-group date date-picker margin-bottom-5" data-date-format="dd-mm-yyyy">
                                                        <input type="text" class="form-control form-filter input-sm" readonly name="date_created_from" placeholder="From">
                                                        <span class="input-group-btn">
                                                            <button class="btn btn-sm default" type="button">
                                                                <i class="fa fa-calendar"></i>
                                                            </button>
                                                        </span>
                                                    </div>
                                                    <div class="input-group date date-picker" data-date-format="dd-mm-yyyy">
                                                        <input type="text" class="form-control form-filter input-sm" readonly name="date_created_to" placeholder="To">
                                                        <span class="input-group-btn">
                                                            <button class="btn btn-sm default" type="button">
                                                                <i class="fa fa-calendar"></i>
                                                            </button>
                                                        </span>
                                                    </div>
                                                </td>
                                                <td class="user_acccount_correct_button">
                                                    <?php
                                                    $accountParentId = 0;
                                                    if ($this->user->getUserType() == User::USER_TYPE_CORPORATE)
                                                        $accountParentId = $this->user->getUserAccountId();

                                                    $selectedAccount = "";
                                                    $allowedLevel = 0;
                                                    if (Permissions::checkFilePermission('hide_subaccount')) {
                                                        $allowedLevel = 1;
                                                    }
                                                    ?>
                                                    <?php echo Ddl::showTreeDropdown('user_account_id', 'user_account', 'user_account', 'id', $accountParentId, array("active_flag = '1'"), $selectedAccount, "Please Select Account", 'class="form-filter bs-select form-control input-sm" data-live-search="true"', "", "", 'logo', '../images/userlogo/thumbnail/', 'owe_16_',true,$allowedLevel); ?>
                                                </td>
                                                <td>
                                                    <input type="number" name="net_amount" class="form-control form-filter input-sm" >
                                                </td>
                                                <td>
                                                    <input type="number" name="vat_amount" class="form-control form-filter input-sm" >
                                                </td>
                                                <td>
                                                    <input type="number" name="credit_total" class="form-control form-filter input-sm" >
                                                </td>
                                                <td>
                                                    <?php
                                                    $note_type_array = [
                                                        '' => 'Select',
                                                        'PARTIAL' => 'PARTIAL',
                                                        'FULL' => 'FULL',
                                                        'OTHER' => 'OTHER'
                                                    ];
                                                    ?>
                                                    <?php echo Ddl::generateArrayDDL('credit_note_type', $note_type_array, "", "", " class='select2 form-filter input-sm' ") ?>
                                                </td>
                                                <td>
                                                    <input type="text" class="form-control form-filter input-sm" name="added_by">
                                                </td>
                                                <td>
                                                    <?php
                                                    $array = [
                                                        '' => 'Select',
                                                        'yes' => 'Yes',
                                                        'no' => 'No'
                                                    ];
                                                    ?>
                                                    <?php echo Ddl::generateArrayDDL('active', $array, "", "", " class='select2 form-filter input-sm' ") ?>
                                                </td>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </form>
                            </div>
                        </div>
                        <div class="tab-pane" id="invoices_purchase">
                            <div class="table-container">
                                <div class="table-actions-wrapper">
                                    <button class="btn btn-sm btn-default table-group-action-submit" id="csv_download_received_btn" onclick="get_excel_purchase()" data-original-title="Download csv" title="Download csv"><span></span><i class="fa fa-download"></i>&nbsp;Download CSV</button>
                                </div>
                                <form action="credit_note.php?action=credit_note_download_excel&type=purchase" method="post" id="purchase_credit_note_form" >
                                    <input type="hidden" name="action" value="filter" />
                                    <table class="table table-striped table-bordered table-hover table-condensed" id="invoice_received_databale">
                                        <thead>
                                            <tr role="row" class="heading">
                                                <th width="75">Actions</th>
                                                <th>Credit Note Number</th>
                                                <th>Invoice Number</th>
                                                <th>Credit Date</th>
                                                <th>Created Date</th>
                                                <th>Account</th>
                                                <th>Net Amount</th>
                                                <th>Vat Amount</th>
                                                <th>Credit Amount</th>
                                                <th>Credit Note Type</th>
                                                <th>Generated By</th>
                                                <th>Active</th>
                                            </tr>
                                            <tr role="row" class="filter">
                                                <td>
                                                    <div class="margin-bottom-5">
                                                        <button class="btn btn-xs blue filter-submit btn-outline" ><i class="fa fa-search"></i> </button>
                                                        <button class="btn btn-xs red filter-cancel mt-ladda-btn ladda-button btn-outline"><i class="fa fa-times"></i> </button>
                                                    </div>

                                                </td>
                                                <td>
                                                    <input type="text" class="form-control form-filter input-sm" name="credit_note_number">
                                                </td>
                                                <td>
                                                    <input type="text" class="form-control form-filter input-sm" name="invoice_number">
                                                </td>
                                                <td>
                                                    <div class="input-group date date-picker margin-bottom-5" data-date-format="dd-mm-yyyy">
                                                        <input type="text" class="form-control form-filter input-sm" readonly name="credit_date_from" placeholder="From">
                                                        <span class="input-group-btn">
                                                            <button class="btn btn-sm default" type="button">
                                                                <i class="fa fa-calendar"></i>
                                                            </button>
                                                        </span>
                                                    </div>
                                                    <div class="input-group date date-picker" data-date-format="dd-mm-yyyy">
                                                        <input type="text" class="form-control form-filter input-sm" readonly name="credit_date_to" placeholder="To">
                                                        <span class="input-group-btn">
                                                            <button class="btn btn-sm default" type="button">
                                                                <i class="fa fa-calendar"></i>
                                                            </button>
                                                        </span>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="input-group date date-picker margin-bottom-5" data-date-format="dd-mm-yyyy">
                                                        <input type="text" class="form-control form-filter input-sm" readonly name="date_created_from" placeholder="From">
                                                        <span class="input-group-btn">
                                                            <button class="btn btn-sm default" type="button">
                                                                <i class="fa fa-calendar"></i>
                                                            </button>
                                                        </span>
                                                    </div>
                                                    <div class="input-group date date-picker" data-date-format="dd-mm-yyyy">
                                                        <input type="text" class="form-control form-filter input-sm" readonly name="date_created_to" placeholder="To">
                                                        <span class="input-group-btn">
                                                            <button class="btn btn-sm default" type="button">
                                                                <i class="fa fa-calendar"></i>
                                                            </button>
                                                        </span>
                                                    </div>
                                                </td>
                                                <td>
                                                    <?php
                                                    $accountParentId = 0;
                                                    if ($this->user->getUserType() == User::USER_TYPE_CORPORATE)
                                                        $accountParentId = $this->user->getUserAccountId();

                                                    $selectedAccount = "";
                                                    $allowedLevel = 0;
                                                    if (Permissions::checkFilePermission('hide_subaccount')) {
                                                        $allowedLevel = 1;
                                                    }
                                                    ?>
                                                    <?php echo Ddl::showTreeDropdown('user_account_id', 'user_account', 'user_account', 'id', $accountParentId, array("active_flag = '1'"), $selectedAccount, "Please Select Account", 'class="form-filter bs-select form-control input-sm" data-live-search="true"', "", "", 'logo', '../images/userlogo/thumbnail/', 'owe_16_',true,$allowedLevel); ?>
                                                </td>
                                                <td>
                                                    <input type="number" name="net_amount" class="form-control form-filter input-sm" >
                                                </td>
                                                <td>
                                                    <input type="number" name="vat_amount" class="form-control form-filter input-sm" >
                                                </td>
                                                <td>
                                                    <input type="number" name="credit_total" class="form-control form-filter input-sm" >
                                                </td>
                                                <td>
                                                    <?php
                                                    $note_type_array = [
                                                        '' => 'Select',
                                                        'PARTIAL' => 'PARTIAL',
                                                        'FULL' => 'FULL',
                                                        'OTHER' => 'OTHER'
                                                    ];
                                                    ?>
                                                    <?php echo Ddl::generateArrayDDL('credit_note_type', $note_type_array, "", "", " class='select2 form-filter input-sm' ") ?>
                                                </td>
                                                <td>
                                                    <input type="text" class="form-control form-filter input-sm" name="added_by">
                                                </td>
                                                <td>
                                                    <?php
                                                    $array = [
                                                        '' => 'Select',
                                                        'yes' => 'Yes',
                                                        'no' => 'No'
                                                    ];
                                                    ?>
                                                    <?php echo Ddl::generateArrayDDL('active', $array, "", "", " class='select2 form-filter input-sm' ") ?>
                                                </td>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div id="hidden_frm" style="display: none;">
            <form name="hiddenForm" id="hiddenForm" action="" method="POST">
                <input type="hidden" name="action" value="download" />
            </form>
        </div>
		
        <div class="modal fade" tabindex="-1" role="dialog" id="send_credit_note_email_modal" >
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">Send Email</h4>
                    </div>
                    <div class="modal-body">
                        <form method="post" action="credit_note.php?action=send_email_credit_note" enctype="multipart/form-data">
                            <input type="hidden" name="credit_note_number" id="send_credit_note_email_credit_note_number" />
                            <input type="hidden" name="credit_note_id" id="send_credit_note_email_credit_note_id" />
                            <div class="row">
                                <div class="col-md-6">
                                    <label class="label-account">To</label>
                                    <div class="form-group">
                                        <div class="input-group"> <span class="input-group-addon"> <i class="fa fa-star-o"></i> </span>
                                            <input class="form-control form-filter" id="send_credit_note_email_to" name="send_credit_note_email_to" type="text" placeholder="To" value="" rel="tooltip" data-original-title="To">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="label-account">CC</label>
                                    <div class="form-group">
                                        <div class="input-group"> <span class="input-group-addon"> <i class="fa fa-star-o"></i> </span>
                                            <input class="form-control form-filter" id="send_credit_note_email_cc" name="send_credit_note_email_cc" type="text" placeholder="CC" value="finance@oneworldexpress.com" rel="tooltip" data-original-title="CC">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <label class="label-account">Subject</label>
                                    <div class="form-group">
                                        <div class="input-group"> <span class="input-group-addon"> <i class="fa fa-star-o"></i> </span>
                                            <input class="form-control form-filter" id="send_credit_note_email_subject" name="send_credit_note_email_subject" type="text" placeholder="Subject" value="" rel="tooltip" data-original-title="Subject">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div id="send_email_inv_invoice_clone">
                                    <div class="col-sm-12">
                                        <div class="fileinput fileinput-new" data-provides="fileinput">
                                            <div class="form-group">
                                                <label> Attachment </label>
                                                <div class="input-group input-large">
                                                    <div class="form-control uneditable-input input-fixed input-xlarge" data-trigger="fileinput">
                                                        <i class="fa fa-file fileinput-exists"></i>&nbsp;
                                                        <span class="fileinput-filename"> </span>
                                                    </div>
                                                    <span class="input-group-addon btn default btn-file">
                                                        <span class="fileinput-new"> Select file </span>
                                                        <span class="fileinput-exists"> Change </span>
                                                        <input type="file" name="credit_note_csv_file[0]" id="credit_note_csv_file_0" class="credit_note_csv_file"> 
                                                    </span>
                                                    <a href="javascript:;" class="input-group-addon btn red fileinput-exists" data-dismiss="fileinput"> Remove </a>
<!--                                                    <a href="javascript:;" class="input-group-addon btn blue" id="upload_csv" >Upload</a>-->
                                                </div>
                                            </div>
                                        </div>
                                        <div class="margin-top-25 float-right">
                                            <button type="button" class="btn btn-success btn-sm add_more_email_attachment_send_inv_invoice">+</button>
                                            <button type="button" class="btn btn-danger btn-sm remove_email_attachment_send_inv_invoice initial-button">-</button>
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
                                            <textarea class="form-control" rows="12" name="send_credit_note_email_message" id="send_credit_note_email_message" ></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <input type="submit" value="send" class="btn btn btn-success" >
                                </div>
                            </div>
                        </form>
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
            .dropdown-menu>li>form>a{
                padding: 8px 16px;
                color: #6f6f6f;
                text-decoration: none;
                display: block;
                clear: both;
                font-weight: 300;
                line-height: 18px;
                white-space: nowrap;
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