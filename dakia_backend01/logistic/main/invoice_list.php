<?php
// get settings
require_once("../includes/settings/config.inc.php");
require_once("../Classes/PHPExcel.php");
require_once("../includes/library/vendor/autoload.php");

include_classes([ 'ddl.inc'
                ],'library');
include_classes([
     'iaddress.class',
    'carrier.class',
    'carrierfilter.class',
    'country.class',
    'countryfilter.class',
    'consignmentcharges.class',
    'consignmentchargesfilter.class',
    'invoices.class',
    'invoicesfilter.class',
    'invoicedetail.class',
    'invoicedetailfilter.class',
    'consignment.class',
    'consignmentfilter.class',
    'consignmentchargestypes.class',
    'consignmentchargestypesfilter.class',
    'currency.class',
    'parcel.class',
    'parcelfilter.class',
    'paymentshistory.class',
    'paymentshistoryfilter.class',
    'services.class' ,
'servicefilter.class',]);
class Page extends BasePage {
    /*     * *
     * Controller logic
     */

    private $user = "";
    private $invoiceFilter = "";
    private $invoice_type = "INV";
    private $nominalCodes = [   'Management Fee'=>4007,
                                'Fulfilment'=>4905,
                                'Fulfillment'=>4905,
                                'Rental'=>4904,
                                'Man Power'=>4900,
                                'Property Insurance'=>4900
                            ];

    protected function init() {
        $invoiceType = "";
        if(isset($_GET['itype']) && ($_GET['itype'] == "mni")) {
            $this->invoice_type = $_GET['itype'];
            $invoiceType = "Manual Invoices";
        } else {
            $invoiceType = "Invoices";
        }
        $this->breadCrumb['data'] = array(
            'index.php' => Translation::GetCaption("HOME"),
            $invoiceType
        );
        
        $this->user = SessionManager::getUser();
        $this->apiContext = new PayPal\Rest\ApiContext(
            new \PayPal\Auth\OAuthTokenCredential(
                'AWzXgXKz5EXfPz8DPvUuIwokqUQP0DOhsnsvESfIM6HND12Om8L6uLZOmLpiAPI5KVuqKsbo3Gr902Wh',     // ClientID
                'EHupkcKcyz8eVkvB83fUV7jSVul5ClzpGm5RKO_9vTzeOO1VCK9vzoYtgkkAVY1dee5cleAPDIpUbg0N'      // ClientSecret
            )
        );
        
        /*
         * DataTable handlings
         */
        if (isset($_GET['action']) && $_GET['action'] == "invoice_ajax") {
            
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
                if($dataTableColumnName != "Active") {
                    $this->invoiceFilter->orderBy(strtolower($dataTableColumnName), $orderFalse);
                }
            } else {
                $this->invoiceFilter->orderBy(strtolower("inv.invoice_no"), "DESC");
            }
            /*
             * Pagination Logic Implemented
             * 
             */
            $iDisplayLength = intval($_REQUEST['length']);
            $iDisplayLength = $iDisplayLength < 0 ? 20 : $iDisplayLength;
            $iDisplayStart = intval($_REQUEST['start']);
            $sEcho = intval($_REQUEST['draw']);
            $end = $iDisplayStart + $iDisplayLength;
            $this->invoiceFilter->setRowsPerPage($iDisplayLength);
            $this->invoiceFilter->setOffset($iDisplayStart);
            $invoiceFilterObj = $this->invoiceFilter->getList("inv.*, ua.user_account, CONCAT(u.first_name,' ',u.last_name) AS user_name");
            $iTotalRecords = $this->invoiceFilter->getCount();
            $setDataArr = array();
            $invoiceQueryFilter = new InvoiceFilter();
            $invoiceQueryFilter->where(['inv.invoice_type' => "INV"]);
            $invoiceMaxIdObj = $invoiceQueryFilter->getList('MAX(inv.id) as last_insert_id');
            $lastInsertId = 0;
            if(count($invoiceMaxIdObj) > 0) {
                $lastInsertId = $invoiceMaxIdObj[0]->getLastInsertId();
            }
            foreach ($invoiceFilterObj as $key => $obj) {
                $account = new CustomerAccount($obj->getUserAccountId());
                if (!empty($obj->getInvoiceDate())) {
                    $invoice_date = formatDate(date("d-m-Y", $obj->getInvoiceDate()));
                } else {
                    $invoice_date = "";
                }
                if (!empty($obj->getDateCreated())) {
                    $date_created = formatDate(date("d-m-Y", $obj->getDateCreated()));
                } else {
                    $date_created = "";
                }
                if($this->invoice_type == "mni") {
                    $pdfUrl = SETTING_URL . '_assets/InvoicesFiles/manual_pdf/';
                    $csvUrl = "";
                } else {
                    $pdfUrl = SETTING_URL . '_assets/InvoicesFiles/pdf/';
                    $csvUrl = SETTING_URL . '_assets/InvoicesFiles/csv/';
                }
                if ($obj->getCsv() != "") {
                    $csv = $csvUrl . $obj->getCsv();
                } else {
                    $csv = '';
                }
                if ($obj->getPdf() != "") {
                    $pdf = $pdfUrl . $obj->getPdf();
                } else {
                    $pdf = '';
                }
                $summaryPdfUrl = SETTING_URL . '_assets/InvoicesFiles/summary_pdf/';
                if ($obj->getSummaryPdf() != "") {
                    $summaryPdf = $summaryPdfUrl . $obj->getSummaryPdf();
                } else {
                    $summaryPdf = '';
                }
                if(!empty($obj->getCurrencyId())) {
                    $currencyObj = new Currency($obj->getCurrencyId());
                    $currency = $currencyObj->getRightsymbol();
                } else {
                    $currency = "GBP";
                }
                $currentArr = array();
                if($obj->getIsEmail()>0)
                    $currentArr['invoice_no'] = '<span class="label label-sm remote-area-shipment bg-font-blue-chambray line-height-2">'.$obj->getInvoiceNo().'</span>';
                else
                    $currentArr['invoice_no'] = $obj->getInvoiceNo();
                $currentArr['invoice_date'] = $invoice_date;
                $currentArr['date_created'] = $date_created;
                if($this->user->getUserType() != User::USER_TYPE_CLIENT) {
                    $currentArr['user_account_id'] = $obj->getUserAccount();
                }
                $currentArr['net_amount'] = ($obj->getNetAmount() + $obj->getFuelCharges()) . " " . $currency;
                $currentArr['vat'] = $obj->getVat() . " " . $currency;
                $currentArr['total_amount'] = $obj->getTotalAmount() . " " . $currency;
                if($this->invoice_type == "mni") {
                    $currentArr['invoice_heading'] = $obj->getInvoiceHeading();
                }
                
                $currentArr['is_paid'] = "";
                if(trim($_GET['type']) != "purchase")
                    $currentArr['added_by'] = $obj->getUserName();
                if($obj->getIsPaid() == 0 && empty($obj->getPaidDate())) {
                    $currentArr['is_paid'] = '<span class="label label-sm label-danger bg-font-blue-chambray line-height-2">Unpaid</span>';
                } else if($obj->getIsPaid() == 0 && !empty($obj->getPaidDate())) {
                    $currentArr['is_paid'] = "Awaiting confirmation of receipt."
                            . "";
                } else {
                    $paidInvoiceBy = new CustomerAccount($obj->getUserAccountId());
                    $currentArr['is_paid'] = "Paid <br /> " . date("Y-m-d" , $obj->getPaidDate()) . " <br /> " . $paidInvoiceBy->getCompany();
                }
                if($obj->getIsCancel() == 1) 
                    $currentArr['is_paid'] = "Cancelled";
               /* else 
                    $currentArr['is_paid'] = "";*/
                $queryStr = "";
                $type = "";
                $clickFunction = "";
                $currentArr['actions'] = '';
                $currentArr['actions'] .= '<div class="btn-group" data-container="body" >
                                            <button class="btn btn-xs blue mt-ladda-btn ladda-button btn-outline dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Tools
                                                <i class="fa fa-angle-down"></i>
                                            </button>
                                        <ul class="dropdown-menu" >';
                if($this->invoice_type == "INV") {
                    if($_GET['type'] == "sale") {
                        if($obj->getIsCancel() == 0) {
                            $currentArr['actions'] .= '<li>
                                                        <a title="Download Pdf" href="javascript:;" data-invoice_id="' . $obj->getId() . '" data-biling_email="' . $account->getBillingEmail() . '" data-company="' . (trim($account->getBillingContact())!='' ? $account->getBillingContact():$account->getCompany()) . '" data-invoice_date="' . $invoice_date . '"  data-invoice_number="' . $obj->getInvoiceNo() . '"  data-invoice_pdf="' . $pdf . '" onclick="send_email_auto_invoice(this)"  >
                                                            <span class="fa fa-download"></span> '.(($obj->getIsEmail()>0)?'Resend Email':'Send Email').'
                                                        </a>
                                                    </li>';
                        }
                        if(($lastInsertId == $obj->getId()) && ($obj->getIsCancel() == 0) && ($obj->getIsPaid() == 0)) {
                            $currentArr['actions'] .= '<li>
                                                            <a title="Delete" href="javascript:;" onclick="delete_auto_invoice(' . $obj->getId() . ')" >
                                                                <span class="fa fa-trash"></span> Cancel
                                                            </a>
                                                        </li>';
                        }
                        $queryStr .= "?type=sale";
                        $type = "sale";
                    } else {
                        $type = "purchase";
                        $clickFunction = "updateReadInvoice(".$obj->getId().")";
                    }
                    if ($pdf != "") {
                        $currentArr['actions'] .= '<li>
                                                        <a title="Download Pdf" href="' . $pdf . '" data-pdf_link="' . $pdf . '" target="_blank" onclick="'.$clickFunction.'" >
                                                            <span class="fa fa-download"></span> Download Pdf
                                                        </a>
                                                    </li>';
                    }
                    if ($summaryPdf != "") {
                        $currentArr['actions'] .= '<li>
                                                        <a title="Download Summary Pdf" href="' . $summaryPdf . '" data-summary_pdf_link="' . $summaryPdf . '" target="_blank" onclick="'.$clickFunction.'" >
                                                            <span class="fa fa-download"></span> Download Summary Pdf
                                                        </a>
                                                    </li>';
                    }
					if(trim($csv) != '')
					{
                    $currentArr['actions'] .= '<li>
                                                    <a title="Download CSV" href="'.$csv.'"  >
                                                        <span class="fa fa-download"></span> Download CSV
                                                    </a>
                                                    <form action="invoice_list.php?action=single_invoice_csv" id="single_invoice_' . $obj->getId() . '_csv" method="post">
                                                        <input type="hidden" name="invoice_id" value="' . $obj->getId() . '" />
                                                        
                                                    </form>
                                                </li>';
					}
					else{
						
					
                    $currentArr['actions'] .= '<li>
                                                    <form action="invoice_list.php?action=single_invoice_csv" id="single_invoice_' . $obj->getId() . '_csv" method="post">
                                                        <input type="hidden" name="invoice_id" value="' . $obj->getId() . '" />
                                                        <a title="Download CSV" href="javascript:;" onclick="document.getElementById('."'".'single_invoice_' . $obj->getId() . '_csv'."'".').submit()" >
                                                            <span class="fa fa-download"></span> Download CSV
                                                        </a>
                                                    </form>
                                                </li>';
                    }
					if($account->getIsPrepaid() == 0 && ($obj->getIsCancel() == 0)) {
                        if($obj->getIsPaid() == 0 && empty($obj->getPaidDate())) {    
                            $currentArr['actions'] .= '<li>
                                                            <a title="Pay Now" href="javascript:;" onclick="pay_invoice_model(' . $obj->getId() . ',' . $obj->getTotalAmount() . ', ' . "'" . 'invoice_list.php' . $queryStr . "'" . ',' . "'" . $obj->getInvoiceNo() . "'" . ',' . "'" . $type . "'" . ',' . "'" . $currency . "'" . ' )" >
                                                                <span class="fa fa-money"></span> Pay Now
                                                            </a>
                                                        </li>';    
                        }
                    }
                      $currentArr['actions'] .= "<li>"
                                            . "<a href='' id='user-audit-detail-view' data-target='#user-audit-view-modal' data-log_key='" . $obj->getId() . "' data-log_name='invoices' data-toggle='modal'> <i class='fa fa-list'></i> View Audit</a>"
                                            . "</li>";
                } else {
                    if($_GET['type'] == "sale") {
                        $queryStr .= "?itype=mni&type=sale";
                        $type = "sale";
                    } else {
                        $queryStr .= "?itype=mni&type=purchase";
                        $type = "purchase";
                        $clickFunction = "updateReadInvoice(".$obj->getId().")";
                    }
                    if ($pdf != "") {
                        if ($summaryPdf != "") {
                        $currentArr['actions'] .= '<li>
                                                        <a title="Download Summary Pdf" href="' . $summaryPdf . '" data-summary_pdf_link="' . $summaryPdf . '" target="_blank" onclick="'.$clickFunction.'" >
                                                            <span class="fa fa-download"></span> Download Summary Pdf
                                                        </a>
                                                    </li>';
                        }
                        $currentArr['actions'] .= '<li>
                                                        <a title="Download Pdf" href="' . $pdf . '" data-pdf_link="' . $pdf . '" target="_blank" onclick="'.$clickFunction.'" >
                                                            <span class="fa fa-download"></span> Download Pdf
                                                        </a>
                                                    </li>';
                        if($_GET['type'] == "sale") {
                                $currentArr['actions'] .= '<li>
                                                        <form action="javascript:;" id="reset_pdf_' . $obj->getId() . '" method="post">
                                                            <input type="hidden" name="invoice_id" value="' . $obj->getId() . '" />
                                                            <input type="hidden" name="pdf" value="' . $obj->getPdf() . '" />
                                                            <a title="Reset Pdf" href="javascript:;" onclick="reset_pdf('."'".'reset_pdf_' . $obj->getId() ."'".')" >
                                                                <span class="fa fa-download"></span> Reset Pdf
                                                            </a>
                                                        </form>
                                                    </li>';
                        
                                $currentArr['actions'] .= '<li>
                                                        <a title="'.(($obj->getIsEmail()>0)?'Resend Email':'Send Email').'" href="javascript:;" data-invoice_id="' . $obj->getId() . '" data-biling_email="' . $account->getBillingEmail() . '" data-company="' . (trim($account->getBillingContact())!='' ? $account->getBillingContact():$account->getCompany()) . '" data-invoice_date="' . $invoice_date . '"  data-invoice_number="' . $obj->getInvoiceNo() . '"  data-invoice_pdf="' . $pdf . '"   data-attachment_files="' .$previousAttachmentFiles . '"  onclick="send_email_manual_invoice(this)"  >
                                                            <span class="fa fa-download"></span> '.(($obj->getIsEmail()>0)?'Resend Email':'Send Email').'
                                                        </a>
                                                    </li>';
                                
                        }
                        $currentArr['actions'] .= '<li>
                                                        <a title="Download Data Files"data-invoice_id="' . $obj->getId() . '" data-biling_email="' . $account->getBillingEmail() . '" data-company="' . (trim($account->getBillingContact())!='' ? $account->getBillingContact():$account->getCompany()) . '" data-invoice_date="' . $invoice_date . '"  data-invoice_number="' . $obj->getInvoiceNo() . '"  data-invoice_pdf="' . $pdf . '"   data-attachment_files="' .$previousAttachmentFiles . '"  onclick="get_data_files(this)"  >
                                                            <span class="fa fa-download"></span> Download Data Files
                                                        </a>
                                                    </li>';
                    } else {
                        if($_GET['type'] == "sale") {
                            $currentArr['actions'] .= '<li>
                                                            <a title="Edit" href="manual_invoices_details.php?id=' . $obj->getId() . '" >
                                                                <span class="fa fa-download"></span> Edit
                                                            </a>
                                                        </li>';
                        }
                    }
                    if($account->getIsPrepaid() == 0) {
                        if($obj->getIsPaid() == 0 && empty($obj->getPaidDate())) {    
                            $currentArr['actions'] .= '<li>
                                                            <a title="Pay Now" href="javascript:;" onclick="pay_invoice_model(' . $obj->getId() . ',' . $obj->getTotalAmount() . ', ' . "'" . 'invoice_list.php?itype=' . $queryStr . "'" . ',' . "'" . $obj->getInvoiceNo() . "'" . ',' . "'" . $type . "'" . ',' . "'" . $currency . "'" . ')" >
                                                                <span class="fa fa-money"></span> Pay Now
                                                            </a>
                                                        </li>';    
                        }
                    }
                    if($_GET['type'] == "sale") {
                      $currentArr['actions'] .= "<li>"
                                            . "<a href='' id='user-audit-detail-view' data-target='#user-audit-view-modal' data-log_key='" . $obj->getId() . "' data-log_name='invoices' data-toggle='modal'> <i class='fa fa-list'></i> View Audit</a>"
                                            . "</li>";
                    }
                }
              
                $currentArr['actions'] .= '</ul>
                                            </div>';
                if($_GET['type'] == "purchase") {
                    if($obj->getIsCancel() == 1) {
                        $currentArr['actions'] = '';
                    }
                }
                $setDataArr [] = $currentArr;
            }
            $setDataArrJson['data'] = $setDataArr;
            $setDataArrJson['draw'] = $sEcho;
            $setDataArrJson['recordsTotal'] = $iTotalRecords;
            $setDataArrJson['recordsFiltered'] = $iTotalRecords;
            echo json_encode($setDataArrJson);
            die;
        }
        
        if (isset($_GET['action']) && $_GET['action'] == 'invoices_download_csv') {
            $this->applyFilter($this->form_vars);
            $this->invoiceFilter->where(['inv.is_cancel' => "0"]);
            $invoiceFilterObj = $this->invoiceFilter->getList("inv.*, ua.user_account, CONCAT(u.first_name,' ',u.last_name) AS user_name");
            
            $heading = ["Type", "Account REF", "Nominal Account REF", "Department ID", "Date", "REF", "Details", "Net Amount", "Tax Code", "Tax Amount"]; 
            if (!empty($invoiceFilterObj)) {
                $objPHPExcel = new PHPExcel();
                $rowNum = 1;
                $colNum = 'A';
                foreach ($heading as $h) {
                    $cell_name = $colNum.$rowNum;
                    $objPHPExcel->getActiveSheet()->getStyle( $cell_name )->getFont()->setBold( true );
                    $objPHPExcel->getActiveSheet()->SetCellValue($cell_name, $h);
                    $colNum++;
                } 
                $rowNum=2;
                foreach($invoiceFilterObj as $invoiceObj) {
                    $colNum = 'A';
                    $objPHPExcel->getActiveSheet()->SetCellValue($colNum.$rowNum, "SI");
                    $colNum++;
                    $receivedAccountObj = new CustomerAccount($invoiceObj->getUserAccountId());
                    $objPHPExcel->getActiveSheet()->SetCellValue($colNum.$rowNum, $receivedAccountObj->getUserAccount());
                    $colNum++;
 
                    $nominalCode = $this->checkNominal($invoiceObj->getInvoiceHeading());
                    $objPHPExcel->getActiveSheet()->SetCellValue($colNum.$rowNum, $nominalCode);
                    $colNum++;
                    $objPHPExcel->getActiveSheet()->SetCellValue($colNum.$rowNum, "");
                    $colNum++;
                     if (!empty($invoiceObj->getInvoiceDate())) {
                        $invoiceDate = formatDate(date("d-m-Y", $invoiceObj->getInvoiceDate()));
                    } else {
                        $invoiceDate = "";
                    }
                    $objPHPExcel->getActiveSheet()->SetCellValue($colNum.$rowNum, $invoiceDate);
                    $colNum++;
                    $invoiceNumber = $invoiceObj->getInvoiceNo();
                    $objPHPExcel->getActiveSheet()->SetCellValue($colNum.$rowNum, $invoiceNumber);
                    $colNum++;
                    $objPHPExcel->getActiveSheet()->SetCellValue($colNum.$rowNum, $invoiceObj->getInvoiceHeading());
                    $colNum++;
                    $netAmount = ($invoiceObj->getNetAmount() + $invoiceObj->getFuelCharges());
                    $objPHPExcel->getActiveSheet()->SetCellValue($colNum.$rowNum, $netAmount);
                    $colNum++;
                    $objPHPExcel->getActiveSheet()->SetCellValue($colNum.$rowNum, "");
                    $colNum++;
                    $vat = $invoiceObj->getVat();
                    $objPHPExcel->getActiveSheet()->SetCellValue($colNum.$rowNum, $vat);
                    $rowNum++;
                }
                $fileName = "invoices_list_" . time();
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
               
               $this->flashMsg->success("Excel download successfully");
               return;
            }
            else
            {
                $this->flashMsg->error("Unable to download excel.");
                return;
            }
             
            //echo "excel download successfully";
            //die;
        }
        
        if (isset($_GET['action']) && $_GET['action'] == 'single_invoice_csv') {
            $output = array();
            $invoiceId = $this->form_vars['invoice_id'];
            $this->readInvoiceUpdate($invoiceId);
            $invoiceDetailFilter = new InvoiceDetailFilter();
            $invoiceDetailFilter->addFieldFilter("invoice_id", $invoiceId);
            $invoiceDetailFilterObj = $invoiceDetailFilter->getList();
            $consignmentIds = array();
            if (count($invoiceDetailFilterObj) > 0) {
                foreach ($invoiceDetailFilterObj as $obj) {
                    $consignmentIds[] = $obj->getConsignmentId();
                }
            }
            $csvData = $this->invoicesCsv($consignmentIds, $invoiceId);
            $output['path'][] = $csvData;
            die;
        }
        
        if (isset($_GET['action']) && $_GET['action'] == 'reset_pdf') {
            $output = [];
            $invoice_id = $this->form_vars['invoice_id'];
            $pdf = $this->form_vars['pdf'];
            if (trim($pdf) != '') {
                unlink('../_assets/InvoicesFiles/manual_pdf/' . $pdf);
            }
            $invoiceObj = new Invoices($invoice_id);
            $invoiceObj->setPdf('');
            $invoiceObj->save();
            $output['status'] = "success";
            $output['message'] = "Invoice is ready for edit.";
            echo json_encode($output);
            die;
        }
        
        if (isset($_GET['action']) && $_GET['action'] == 'delete_auto_invoice') {
            /* This is cancel invoice not delete */
            $output = [];
            $invoice_id = $this->form_vars['invoice_id'];
            $invoiceObj = new Invoices($invoice_id);
            $invoiceDetailFilter = new InvoiceDetailFilter();
            $invoiceDetailFilter->addFieldFilter("   invoice_id", $invoice_id);
            $invoiceDetailFilterObjs = $invoiceDetailFilter->getList();
            $consignmentIds = [];
            if(count($invoiceDetailFilterObjs) > 0) {
                foreach($invoiceDetailFilterObjs as $obj) {
                    $consignmentIds[] = $obj->getConsignmentId();
                }
            }
            $userAccountObj = new CustomerAccount($invoiceObj->getUserAccountId());
            /* update invoice_id in consignment charges table */
            if(count($consignmentIds) > 0) {
                foreach($consignmentIds as $consignmentId) {
                    ConsignmentCharges::updateInvoiceId($userAccountObj->getId(), $consignmentId, 0);
                }
            }
            /* add payment history reverse transaction */
            $date_added = time();
            $added_by = $this->user->getId();
            $date_update = time();
            if($userAccountObj->getIsPrepaid() == 0) {
                /* Add payment transcation into payment history table for invoice charges */
                $invoiceMsg = "Reverse invoiced amount for invoice# ".$invoiceObj->getInvoiceNo();
                $paymentsHistory = new PaymentsHistory();
                $paymentsHistory->setAccountId($userAccountObj->getId());
                $paymentsHistory->setAmount($invoiceObj->getTotalAmount());
                $paymentsHistory->setAmountCurrencyId($invoiceObj->getCurrencyId());
                $paymentsHistory->setPaymentDetail($invoiceMsg);
                $paymentsHistory->setUserCurrencyId($invoiceObj->getCurrencyId());
                $paymentsHistory->setDebit("0.00");
                $paymentsHistory->setCredit($invoiceObj->getTotalAmount());
                $paymentsHistory->setInvoiceId($invoiceObj->getId());
                $paymentsHistory->setPaymentStatus("completed");
                $paymentsHistory->setIsCompleted("yes");
                $paymentsHistory->setDateAdded($date_added);
                $paymentsHistory->setAddedBy($added_by);
                $paymentsHistory->save();
				/* update account balance */
				CustomerAccount::updateBalance($userAccountObj->getId());
				/******************************/
            }
            /* delete invoice */
            $invoiceFilterObj = new Invoices($invoice_id);
            $invoiceFilterObj->setIsCancel(1);
            $invoiceFilterObj->save();
            
            $output['status'] = "success";
            $output['message'] = "Invoice deleted successfully.";
            echo json_encode($output);
            die;
        }
        
        if (isset($_GET['action']) && $_GET['action'] == 'send_email_auto_invoice') {
            $mailObj = new SendEmail();
            $sendTo = explode(",", $this->form_vars['send_inv_email_to']);
            $sendCc = explode(",", $this->form_vars['send_inv_email_cc']);
            $sendSubject = $this->form_vars['send_inv_email_subject'];
            $sendMessage = $this->form_vars['send_inv_email_message'];
            $invoiceId = $this->form_vars['invoice_id'];
            $invoice = new Invoices($invoiceId);
            $invoice->setIsEmail(1);
            $invoice->save();
           
            $output = [];
            $output['email_response'] = $mailObj->autoInvoiceEmailSend($sendTo, $sendSubject, $sendMessage, $sendCc);
            
            if((isset($output['email_response']['status'])) && ($output['email_response']['status'] == "success")) {
                $this->flashMsg->success($output['email_response']['message']);
            }
        }
        
        if (isset($_GET['action']) && $_GET['action'] == 'send_email_manual_invoice') {
            $mailObj = new SendEmail();
            $sendTo = explode(",", $this->form_vars['send_mni_email_to']);
            $sendCc = explode(",", $this->form_vars['send_mni_email_cc']);
            $sendSubject = $this->form_vars['send_mni_email_subject'];
            $sendMessage = $this->form_vars['send_mni_email_message'];
            $invoiceId = $this->form_vars['invoice_id'];
            $invoice = new Invoices($invoiceId);
            $invoice->setIsEmail(1);
            $previousAttachments = json_decode($invoice->getAttachedFiles());
            $invoice->save();
            $emailAttachmentFilesSent=[];
            $output = [];
            $attachment_file = $_FILES['inv_csv_file'];
            if (isset($attachment_file) && !empty($attachment_file['name'][0]) && is_array($attachment_file)) {
                foreach($attachment_file['name'] as $key => $fileName) {
                    $file_name = $fileName;
                    $path_parts = pathinfo($file_name);
                    $ext = strtolower($path_parts['extension']);
                    $filename =  $path_parts['filename'] ;
                    $basename = $path_parts['basename'];

                    if ($ext == 'csv' || $ext == 'xlsx' || $ext == 'pdf') {
                        $account = $this->user->getAccount();
                        $uploadPath = '../_assets/InvoicesFiles/manual_invoice_attachment/'; 
                        $new_file_name = $filename."_".time().".".$ext ; 
                         $relPath = $uploadPath . $new_file_name; 
                        if (!file_exists($uploadPath)) {
                             mkdir($uploadPath, 0775,true);
                                  if (!file_exists($uploadPath)) {      
                                     // echo " directory created";
                                  }
                        }
                        
                        if (move_uploaded_file($attachment_file['tmp_name'][$key], $relPath)) {
                            $emailAttachmentFiles[] = $relPath;
                            $emailAttachmentFilesSent[] = $new_file_name;
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
            if (count($emailAttachmentFilesSent) > 0) {
                $invoice = new Invoices($invoiceId);
                if(count($previousAttachments)>0)
                    $allAttachments = array_merge($emailAttachmentFilesSent, $previousAttachments);
                else
                    $allAttachments = $emailAttachmentFilesSent;
 
                $invoice->setAttachedFiles(json_encode($allAttachments));
                $invoice->save();
            }
            
                $this->flashMsg->success($output['email_response']['message']);
            }
        }
        
        ///////////////get previous attachment files///////////
         if (isset($_GET['action']) && $_GET['action'] == 'get_manual_invoice_attachments') {
            $output = [];
            $invoiceId = $this->form_vars['invoice_id'];
            $invoice = new Invoices($invoiceId);
            $html = "";
            $attachedFilesUrl = SETTING_URL . '_assets/InvoicesFiles/manual_invoice_attachment/';
            if ($invoice->getAttachedFiles() != "") {
                $html = ' <div class="form-group">
                            <ul class="feeds">';
                $previousAttachedList = array();
                $attachedFiles = json_decode($invoice->getAttachedFiles());
                foreach ($attachedFiles as $attFile) {
                    $html .= '
                                                <li><div class="col1">
                                                        <div class="cont">
                                                            <div class="cont-col1">
                                                                <div class="label label-sm label-success">
                                                                    <i class="fa fa-file"></i>
                                                                </div>
                                                            </div>
                                                            <div class="cont-col2">
                                                                <div class="desc"> <a href="' . $attachedFilesUrl . $attFile . '" target="_blank">' . $attFile . '</a> </div>
                                                            </div>
                                                        </div>
                                                    </div></li>';
                }
                $html .= '</ul></div> ';
            }

            $output['status'] = "success";
            $output['message'] = $html;
             echo json_encode($output);
            die;
        }
        
        
        if (isset($this->form_vars['action']) && $this->form_vars['action'] == 'pay_invoice') {
            $output = [];
            $type = "";
            if(isset($_GET['type'])) {
                $type = $_GET['type'];
            }
            $invoiceId = $this->form_vars['pay_invoice_id'];
            $this->readInvoiceUpdate($invoiceId);
            $invoiceObj = new Invoices($invoiceId);
            $amount = $invoiceObj->getTotalAmount();
            /* Default currency is GBP */
            $currencyId = 2;
            if((count($invoiceObj)) && $invoiceObj->getCurrencyId() > 0) {
                $currencyId = $invoiceObj->getCurrencyId();
            }
            $pay_by = $this->form_vars['pay_by'];
            $pay_date = strtotime($this->form_vars['pay_date']);
            $bank_ref_number = $this->form_vars['bank_ref_number'];
            $cheque_number = $this->form_vars['cheque_number'];
            $paymentDetail = $this->form_vars['description'];
            $paymentStatus = "pending";
            $isCompleted = "no";
            $debit = "0.00";
            $credit = $amount;
            $date_added = time();
            $added_by = $this->user->getId();
            $date_update = time();
            $update_by = $this->user->getId();
            if($type == "sale") {
                $invoiceObj->setIsPaid(1);
                $account_id = $invoiceObj->getUserAccountId();
            } else {
                $account_id = $this->user->getUserAccountId();
            }
            $invoiceObj->setPaidDate($pay_date);
            $invoiceObj->save();
            $paymenthistoryObj = new PaymentsHistory();
            if($pay_by == "bank_transfer") {
                $paymenthistoryObj->setBillingId($bank_ref_number);
            } else if($pay_by == "cheque") {
                $paymenthistoryObj->setBillingId($cheque_number);
            }
            if($type == "sale") {
                $paymentStatus = "completed";
                $isCompleted = "yes";
            }
            $paymenthistoryObj->setAccountId($account_id);
            $paymenthistoryObj->setAmount($amount);
            $paymenthistoryObj->setPaymentMethod($pay_by);
            $paymenthistoryObj->setPaymentDetail($paymentDetail);
            $paymenthistoryObj->setAmountCurrencyId($currencyId);
            $paymenthistoryObj->setUserCurrencyId($currencyId);
            $paymenthistoryObj->setDebit($debit);
            $paymenthistoryObj->setCredit($credit);
            $paymenthistoryObj->setPaymentStatus($paymentStatus);
            $paymenthistoryObj->setIsCompleted($isCompleted);
            $paymenthistoryObj->setInvoiceId($invoiceObj->getId());
            $paymenthistoryObj->setDateAdded($date_added);
            $paymenthistoryObj->setAddedBy($added_by);
            $paymenthistoryObj->setDateUpdated($date_update);
            $paymenthistoryObj->setUpdatedBy($update_by);
            $paymenthistoryObj->save();
			/* update account balance */
			CustomerAccount::updateBalance($account_id);
			/******************************/
            $output['status'] = "success";
            $output['message'] = "Invoice Payment successfully submit";
            echo json_encode($output);
            die();
        }
        
        if (isset($this->form_vars['action']) && $this->form_vars['action'] == 'paypal_create_payment') {
            $actual_link = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]";
            $return_url = $actual_link."/paypal_success.php";
            $cancel_url = $actual_link."/paypal_cancel.php";

            $invoiceObj = new Invoices($this->form_vars['pay_invoice_id']);
            $invoice_amount = $invoiceObj->getTotalAmount();
            $account_id = $this->user->getUserAccountId();
            $userAccountObj = new CustomerAccount($account_id);
            $userParentAccountObj = new CustomerAccount($userAccountObj->getParentid());
            if(!empty($userParentAccountObj->getPaypalClientId()) && !empty($userParentAccountObj->getPaypalClientSecret())) {
                $this->getPaypalApiContext($account_id);
                $invoiceObj->setIsPaid(1);
                $pay_date = $this->form_vars['pay_date'];
                $currencyId = 2;
                if((count($invoiceObj)) && $invoiceObj->getCurrencyId() > 0) {
                    $currencyId = $invoiceObj->getCurrencyId();
                }
                $invoiceObj->setPaidDate($pay_date);
                $invoiceObj->save();
                $currency = new Currency($currencyId);
                $debit = '0.00';
                $credit = $invoice_amount;
                /* Save payment in DB */
                $paymentStatus = "pending";
                $isCompleted = "no";
                $paymentDetail = $this->form_vars['description'];
                $pay_by = "paypal";
                $date_added = time();
                $added_by = $this->user->getId();
                $date_update = time();
                $update_by = $this->user->getId();
                $paymenthistoryObj = new PaymentsHistory();
                $paymenthistoryObj->setAccountId($account_id);
                $paymenthistoryObj->setAmount($invoice_amount);
                $paymenthistoryObj->setPaymentMethod("paypal");
                $paymenthistoryObj->setPaymentDetail($paymentDetail);
                $paymenthistoryObj->setAmountCurrencyId($currencyId);
                $paymenthistoryObj->setUserCurrencyId($currencyId);
                $paymenthistoryObj->setDebit($debit);
                $paymenthistoryObj->setCredit($credit);
                $paymenthistoryObj->setPaymentStatus($paymentStatus);
                $paymenthistoryObj->setIsCompleted($isCompleted);
                $paymenthistoryObj->setInvoiceId($invoiceObj->getId());
                $paymenthistoryObj->setDateAdded($date_added);
                $paymenthistoryObj->setAddedBy($added_by);
                $paymenthistoryObj->setDateUpdated($date_update);
                $paymenthistoryObj->setUpdatedBy($update_by);
                $paymenthistoryObj->save();
                /* create payment paypal */
                $payer = new PayPal\Api\Payer();
                $payer->setPaymentMethod('paypal');

                $amount = new PayPal\Api\Amount();
                $amount->setTotal($invoice_amount);
                $amount->setCurrency($currency->getRightsymbol());

                $transaction = new PayPal\Api\Transaction();
                $transaction->setAmount($amount)->setDescription("Pay Invoice")->setCustom($paymenthistoryObj->getId());

                $redirectUrls = new PayPal\Api\RedirectUrls();
                $redirectUrls->setReturnUrl($return_url)->setCancelUrl($cancel_url);

                $payment = new PayPal\Api\Payment();
                $payment->setIntent('sale')
                    ->setPayer($payer)
                    ->setTransactions(array($transaction))
                    ->setRedirectUrls($redirectUrls);
                $paymentData = $payment->create($this->apiContext);
                $paypal_payment_id = $paymentData->id;
                /* update payment historey */
                $paymentHistoryUpdate = new PaymentsHistory($paymenthistoryObj->getId());
                $paymentHistoryUpdate->setPaypalPaymentId($paypal_payment_id);
                $paymentHistoryUpdate->save();

                echo $paymentData;
                die;
            } else {
                $output['status'] = "error";
                $output['message'] = "Sorry Paypal is not set in account";
                echo json_encode($output);
                die;
            }
        }
        
        if (isset($this->form_vars['action']) && $this->form_vars['action'] == 'paypal_execute_payment') {
            $account_id = $this->user->getUserAccountId();
            $this->getPaypalApiContext($account_id);
            $paymentId = $this->form_vars['payment_id'];
            $payerId = $this->form_vars['payer_id'];
            $payment = PayPal\Api\Payment::get($paymentId, $this->apiContext);
            // Execute payment with payer ID
            $execution = new PayPal\Api\PaymentExecution();
            $execution->setPayerId($payerId);
            $result = $payment->execute($execution, $this->apiContext);
            /* Update payment success */
            $state = $result->state;
            $status = $result->payer->status;
            $payment_history_id = "";
            if($state == "approved" && ($status == "VERIFIED" ||  $status == "UNVERIFIED")) {
                $paypal_payment_id = $result->id;
                $payment_history_id = $result->transactions[0]->custom;
                $transaction_id = $result->transactions[0]->related_resources[0]->sale->id;
                $payer_email = $result->payer->payer_info->email;
                $payment_status = $result->transactions[0]->related_resources[0]->sale->state;
                $amount = $result->transactions[0]->related_resources[0]->sale->amount->total;
                $paymentHistoryObj = new PaymentsHistory($payment_history_id);
                /* update payment history */
                $paymentHistoryUpdate = new PaymentsHistory($payment_history_id);
                $paymentHistoryUpdate->setTxnId($transaction_id);
                $paymentHistoryUpdate->setSenderEmail($payer_email);
                $paymentHistoryUpdate->setPaymentStatus($payment_status);
                if(($payment_status == "completed") && ($amount == $paymentHistoryObj->getAmount()) && ($paypal_payment_id == $paymentHistoryObj->getPaypalPaymentId())) {
                    $paymentHistoryUpdate->setIsCompleted("yes");
                }
                $paymentHistoryUpdate->save();
				/* update account balance */
				CustomerAccount::updateBalance($paymentHistoryUpdate->getAccountId());
				/******************************/
            }
            /* Update payment success */
            echo $payment_history_id;
            die;
        }
        
        if (isset($this->form_vars['action']) && $this->form_vars['action'] == 'update_read_invoice') {
            $output = [];
            $invoiceId = $this->form_vars['invoice_id'];
            $invoice = new Invoices($invoiceId);
            $invoice->setIsRead(1);
            $invoice->save();
            $output['status'] = "success";
            $output['message'] = "Invoice Read Successfully";
            echo json_encode($output);
            die;
        }
        
    }
    
    public function readInvoiceUpdate($invoiceId) {
        $invoice = new Invoices($invoiceId);
        $invoice->setIsRead(1);
        $invoice->save();
        return;
    }

    public function getPaypalApiContext($account_id) {
        $userAccountObj = new CustomerAccount($account_id);
        $userParentAccountObj = new CustomerAccount($userAccountObj->getParentid());
        $this->apiContext = new PayPal\Rest\ApiContext(
            new \PayPal\Auth\OAuthTokenCredential(
                $userParentAccountObj->getPaypalClientId(),     // ClientID
                $userParentAccountObj->getPaypalClientSecret()      // ClientSecret
            )
        );
        $mode = 'SANDBOX';
        if(PAYPAL_LIVE) {
            $mode = 'LIVE';
        }
        $this->apiContext->setConfig(
            array(
                'mode' => $mode
            )
        );
    }
    
    protected function invoicesCsv($consignmentIds, $invoiceId) {
        $returnData = array();
        $chargesTotal = array();
        $heading = ["Account", "Invoice Date", "Label Created Date", "Dispatched Date", "Delivered Date", "Invoice Number", "Order Ref No", "Tracking Number", "MAWB","Consignment Type", "Status", "Carrier", "Service Name", "Sender City", "Sender Country", "Sender Postcode", "Receiver City", "Receiver Country", "Receiver Postcode", "Number of Pieces", "Weight", "Vol Weight"];
        $consignmentChargesTypeFilter = new ConsignmentChargesTypesFilter();
        $consignmentChargesTypeFilter->addFieldNotFilter("     charge_type", "agent");
        $consignmentChargesTypeFilter->addFieldNotFilter("     charges_key", "VAT");
        $consignmentChargesTypeFilterObj = $consignmentChargesTypeFilter->getList();
        if(count($consignmentChargesTypeFilterObj) > 0) {
            foreach ($consignmentChargesTypeFilterObj as $chargesTypeObj) {
                if($chargesTypeObj->getIsExtraCharge() == 0) {
                    $heading[] = $chargesTypeObj->getTitle();
                    $chargesTotal[$chargesTypeObj->getTitle()] = 0;
                }
            }
        }
        $heading[] = "Extra Charges";
        $heading[] = "Net Total";
        $heading[] = "Length";
        $heading[] = "Width";
        $heading[] = "Height";
        $fileLocation = SETTING_DIR_REMOTE . '_assets/InvoicesFiles/csv/';
        $fileName = "INV_". $invoiceId . "_" . time(). ".csv";
        $file_url = $fileLocation . $fileName;
        if(count($consignmentIds) > 0) {
            $returnString = "";
            foreach ($heading as $h) {
                $returnString .= $h . ",";
            }       
            $invoiceObj = new Invoices($invoiceId);
            $recievedUserObj = new CustomerAccount($invoiceObj->getUserAccountId());
            $totalNumberOfPieces = 0;
            $totalWeight = 0;
            $totalVolWeight = 0;
            $grandTotalExtraCharges = 0;
            $grandAllTotalExtraCharges = 0;
            foreach($consignmentIds as $consignmentId) {
                $parcelFilter = new ParcelFilter();
                $parcelFilter->addFieldFilter("consignment_id", $consignmentId);
                $parcelFilterObj = $parcelFilter->getColumnList("tracking_number,length,width,height");
                if(count($parcelFilterObj) > 0) {
                    foreach($parcelFilterObj as $key => $parcelObj) {
                        $consignmentObj = new Consignment($consignmentId);
                        $returnString .=  "\r\n";
                        $returnString .=  cleanCsvCall($recievedUserObj->getUserAccount()) . ",";
                        $returnString .=  date("d F Y",$invoiceObj->getInvoiceDate()) . ",";
                        $dateLabelCreated = (!empty($consignmentObj->getDateLabelCreated()) ? date("d F Y",$consignmentObj->getDateLabelCreated()) : "");
                        $returnString .=  $dateLabelCreated . ",";
                        $dateBooked = (!empty($consignmentObj->getDateBooked()) ? date("d F Y",$consignmentObj->getDateBooked()) : "");
                        $returnString .=  $dateBooked . ",";
                        $dateDelivered = (!empty($consignmentObj->getDateDelivered()) ? date("d F Y",$consignmentObj->getDateDelivered()) : "");
                        $returnString .=  $dateDelivered . ",";
                        $returnString .=  cleanCsvCall($invoiceObj->getInvoiceNo()) . ",";
                        
                        $returnString .=  cleanCsvCall($consignmentObj->getHawb(),'int') . ",";
                        $returnString .=  cleanCsvCall($parcelObj->getTrackingNumber(),'int') . ",";
                        $returnString .=  cleanCsvCall($consignmentObj->getMawb()) . ",";
                        $returnString .=  cleanCsvCall(strtoupper($consignmentObj->getConsignmentType())) . ",";
                        $shipment_status = $this->get_shipnment_status($consignmentObj->getShipmentStatus(),"csv");
                        $returnString .=  cleanCsvCall($shipment_status) . ",";
                        if($consignmentObj->getCustomizedServiceId()>0)
                            $serviceObj = new Services($consignmentObj->getCustomizedServiceId());
                        else 
                            $serviceObj = new Services($consignmentObj->getServiceId());
                        
                        $carrierObj = new Carrier($serviceObj->getCarrierId());
                        $returnString .=  cleanCsvCall($carrierObj->getCarrier()) . ",";
                        $returnString .=  cleanCsvCall($serviceObj->getName()) . ",";

                        $returnString .=  cleanCsvCall($consignmentObj->getSenderCity()) . ",";
                        $senderCountryObj = new Country($consignmentObj->getSenderCountryId());
                        $returnString .=  cleanCsvCall($senderCountryObj->getName()) . ",";
                        $returnString .=  cleanCsvCall($consignmentObj->getSenderPostcode()) . ",";
                        $countryObj = new Country($consignmentObj->getCountryId());
                        $returnString .=  cleanCsvCall($consignmentObj->getCity()) . ",";
                        $returnString .=  cleanCsvCall($countryObj->getName()) . ",";
                        $returnString .=  cleanCsvCall($consignmentObj->getPostcode()) . ",";
                        if($key == 0) {
                            $returnString .=  cleanCsvCall($consignmentObj->getNumberPieces()) . ",";
                            $returnString .=  cleanCsvCall($consignmentObj->getChargeWeight()) . ",";
                            $returnString .=  cleanCsvCall($consignmentObj->getVolWeight()) . ",";
                            $totalNumberOfPieces = $totalNumberOfPieces + $consignmentObj->getNumberPieces();
                            $totalWeight = $totalWeight + $consignmentObj->getChargeWeight();
                            $totalVolWeight = $totalVolWeight + $consignmentObj->getVolWeight();
                        } else {
                            $returnString .=  " " . ",";
                            $returnString .=  " " . ",";
                            $returnString .=  " " . ",";
                        }
                        $totalExtraCharges = 0;
                        $totalOtherCharges = 0;
                        if(count($consignmentChargesTypeFilterObj) > 0) {
                            foreach ($consignmentChargesTypeFilterObj as $chargesTypeObj) {
                                $consignmentChargesFilter = new ConsignmentChargesFilter();
                                $consignmentChargesFilter->addFieldFilter("     cc.consignment_id", $consignmentId);
                                $consignmentChargesFilter->addFieldFilter("     cc.invoice_id", $invoiceId);
                                $consignmentChargesFilter->addFieldFilter("     cc.cost_type", "customer");
                                $consignmentChargesFilter->addFieldFilter("     cc.charge_type_id", $chargesTypeObj->getId());
                                $consignmentChargesFilterObj = $consignmentChargesFilter->getList();
                                if($chargesTypeObj->getIsExtraCharge() == 0) {
                                    if($key == 0) {
                                        if(count($consignmentChargesFilterObj) > 0) {
                                            $returnString .= cleanCsvCall($consignmentChargesFilterObj[0]->getCost()) . ",";
                                            $totalOtherCharges = $totalOtherCharges + $consignmentChargesFilterObj[0]->getCost();
                                            $chargesTotal[$chargesTypeObj->getTitle()] = $chargesTotal[$chargesTypeObj->getTitle()] + $consignmentChargesFilterObj[0]->getCost();
                                        } else {
                                            $returnString .= 0 . ",";
                                        }
                                    } else {
                                        $returnString .= " " . ",";
                                    }
                                } else {
                                    if(count($consignmentChargesFilterObj) > 0) {
                                        $totalExtraCharges = $totalExtraCharges + $consignmentChargesFilterObj[0]->getCost();
                                    } 
                                }
                            }
                        }
                        if($key == 0) {
                            $returnString .=  cleanCsvCall($totalExtraCharges) . ",";
                            $returnString .=  cleanCsvCall(($totalExtraCharges + $totalOtherCharges)) . ",";
                            $grandTotalExtraCharges = $grandTotalExtraCharges + $totalExtraCharges;
                            $grandAllTotalExtraCharges = $grandAllTotalExtraCharges + ($totalExtraCharges + $totalOtherCharges);
                        } else {
                            $returnString .=  " " . ",";
                        }

                        $returnString .=  cleanCsvCall($parcelObj->getLength()) . ",";
                        $returnString .=  cleanCsvCall($parcelObj->getWidth()) . ",";
                        $returnString .=  cleanCsvCall($parcelObj->getHeight()) . ",";
                    }
                }
            }
            $returnString .=  "\r\n";
            foreach ($heading as $h) {
                $returnString .= " " . ",";
            }
            $returnString .=  "\r\n";
            $returnString .=  " " . ",";
            $returnString .=  " " . ",";
            $returnString .=  " " . ",";
            $returnString .=  " " . ",";
            $returnString .=  " " . ",";
            $returnString .=  " " . ",";
            $returnString .=  " " . ",";
            $returnString .=  " " . ",";
            $returnString .=  " " . ",";
            $returnString .=  " " . ",";     
            $returnString .=  " " . ",";
            $returnString .=  " " . ",";
            $returnString .=  " " . ",";
            $returnString .=  " " . ",";
            $returnString .=  " " . ",";
            $returnString .=  " " . ",";
            $returnString .=  " " . ",";
            $returnString .=  " " . ",";

            $returnString .=  " TOTAL :  " . ",";
            $returnString .=  cleanCsvCall($totalNumberOfPieces) . ",";
            $returnString .=  cleanCsvCall($totalWeight) . ",";
            $returnString .=  cleanCsvCall($totalVolWeight) . ",";
            foreach($chargesTotal as $key => $total) {
                $returnString .=  $total . ",";
            }
            $returnString .=  cleanCsvCall($grandTotalExtraCharges) . ",";
            $returnString .=  cleanCsvCall($grandAllTotalExtraCharges) . ",";
        }
        header("Content-type: text/csv");
        header("Content-Disposition: attachment; filename=" . $fileName);
        header("Pragma: no-cache");
        header("Expires: 0");
        echo $returnString;
        $returnData['FILENAME'] = $fileName;
        $returnData['LINK'] = $file_url;
        return $returnData;
        die;
    }
    
    protected function get_shipnment_status($status,$csv = "") {
        $c_status = Consignment::stateText(strtolower(trim($status)));
        if (strtolower($c_status) == "label created") {
            $shipment_status = '<span class="label label-sm bg-blue-chambray bg-font-blue-chambray line-height-2"> ' . Translation::GetCaption("PRINTED") . '</span>';
        } else if (strtolower($c_status) == "booked") {
            $shipment_status = '<span class="label label-sm bg-blue-dark bg-font-blue-dark line-height-2"> ' . Translation::GetCaption("SHIPPED") . '</span>'; //= Translation::GetCaption("SHIPPED");
        } else if (Consignment::STATUS_RECYCLED == trim($status)) {
            $shipment_status = '<span class="label label-sm label-danger line-height-2"> ' . Translation::GetCaption("RECYCLED") . '</span>';
        } else if (Consignment::STATUS_DELIVERED == trim($status)) {
            $shipment_status = '<span class="label label-sm bg-green-jungle bg-font-green-jungle line-height-2"> ' . Translation::GetCaption("DELIVERED") . '</span>';
        } else if (Consignment::STATUS_INVALID == trim($status)) {
            $shipment_status = '<span class="label label-sm label-warning line-height-2"> ' . Translation::GetCaption("INVALID") . '</span>';
        } else if (Consignment::STATUS_READY_TO_PRINT == trim($status)) {
            $shipment_status = '<span class="label label-sm label-info line-height-2"> ' . Translation::GetCaption("READY_TO_PRINT") . '</span>';
        } else if (Consignment::STATUS_READY_TO_PRINT == trim($status)) {
            $shipment_status = '<span class="label label-sm label-info line-height-2"> ' . Translation::GetCaption("VALID") . '</span>';
        } else {
            if ($c_status == "Unknown") {
                $shipment_status = "";
            } else {
                $shipment_status = '<span class="label label-sm bg-default bg-font-default line-height-2"> ' . Translation::GetCaption(strtoupper($c_status)) . '</span>';
            }
        }
        if($csv == "csv") {
            $shipment_status = strip_tags($shipment_status);
        }
        return $shipment_status;
    }
    
    protected function applyFilter($form_vars) {
        $this->form_vars = $form_vars;
        $this->invoiceFilter = new InvoiceFilter();
        $this->invoiceFilter->join("user u", ['inv.added_by' => 'u.id']);

        if($_GET['type'] == "sale") { // sale invoices
            $this->invoiceFilter->join("customer_account ua", ['inv.user_account_id' => 'ua.id']);
            $this->invoiceFilter->where(['inv.invoice_by' => $this->user->getUserAccountId()]);
        } else { 
            // purchase invoices
            $this->invoiceFilter->join("customer_account ua", ['inv.invoice_by' => 'ua.id']);  
            $this->invoiceFilter->where(['inv.user_account_id' => $this->user->getUserAccountId()]);
            $this->invoiceFilter->where(['inv.is_email' => 1]);
        }

//        if ($this->user->getUserType() == User::USER_TYPE_ADMIN) {
//            $userAccountArry = CustomerAccount::accountSubAccount($this->user->getUserAccountId(), 0, true);
//            $this->invoiceFilter->whereIn("inv.user_account_id", $userAccountArry);
//        } else {
//            $userAccountArry = CustomerAccount::accountSubAccount($this->user->getUserAccountId(), 0, false);
//            $this->invoiceFilter->whereIn("inv.user_account_id", $userAccountArry);
//        } 

        if($this->invoice_type == "mni") {
            $this->invoiceFilter->where(['inv.invoice_type' => "MNI"]);
        } else {
            $this->invoiceFilter->where(['inv.invoice_type' => "INV"]);
        }
        /*
         * Column filter
         * For search
         */
        if (isset($this->form_vars['action']) && $this->form_vars['action'] == 'filter') {
            $filterArray = [];

            $invoice_no_from = preg_replace("/[^0-9]+/", "", str_replace("INV",'',$this->form_vars['invoice_no_from']));
            $invoice_no_to = preg_replace("/[^0-9]+/", "", str_replace("INV",'',$this->form_vars['invoice_no_to']));


            if (!empty($invoice_no_from) && !empty($invoice_no_to))
            {
                $this->invoiceFilter->whereBetween ("REGEXP_REPLACE(inv.invoice_no, '[a-zA-Z]+', '')", $invoice_no_from, $invoice_no_to);
            }
            elseif (!empty($invoice_no_from) && empty($invoice_no_to))
                $filterArray["REGEXP_REPLACE(inv.invoice_no, '[a-zA-Z]+', '')"]= $invoice_no_from;
                //$filterArray['REPLACE(inv.invoice_no,"INVST","")'] = $invoice_no_from;
            elseif (empty($invoice_no_from) && !empty($invoice_no_to))
                $filterArray["REGEXP_REPLACE(inv.invoice_no, '[a-zA-Z]+', '')"] = $invoice_no_to;

            $account = $this->form_vars['user_account_id'];
            if (!empty($account))
                $filterArray['inv.user_account_id'] = $account;

            $net_amount = $this->form_vars['net_amount'];
            if (!empty($net_amount))
                $filterArray['inv.net_amount'] = $net_amount;

            $vat = $this->form_vars['vat'];
            if (!empty($vat))
                $filterArray['inv.vat'] = $vat;

            $total_amount = $this->form_vars['total_amount'];
            if (!empty($total_amount))
                $filterArray['inv.total_amount'] = $total_amount;
            
            $is_paid = $this->form_vars['is_paid'];
            if (is_numeric($is_paid) && $is_paid >= 0)
                $filterArray['inv.is_paid'] = $is_paid;

            $this->invoiceFilter->where($filterArray);

            $invoice_date_from = $this->form_vars['invoice_date_from'];
            $invoice_date_to = $this->form_vars['invoice_date_to'];
            if (!empty($invoice_date_from) && !empty($invoice_date_to))
                $this->invoiceFilter->whereBetween ('inv.invoice_date', date('Y-m-d 00:00:00', strtotime($invoice_date_from)), date('Y-m-d 23:59:59', strtotime($invoice_date_to)));
            
            $date_created_from = $this->form_vars['date_created_from'];
            $date_created_to = $this->form_vars['date_created_to'];
            if (!empty($date_created_from) && !empty($date_created_to))
                $this->invoiceFilter->whereBetween ('inv.date_created', date('Y-m-d 00:00:00', strtotime($date_created_from)), date('Y-m-d 23:59:59', strtotime($date_created_to)));

            $added_by = $this->form_vars['added_by'];
            if (!empty($added_by))
                $this->invoiceFilter->where("(u.first_name LIKE '%" . $added_by . "%' OR u.last_name LIKE '%" . $added_by . "%')");
        }
    }
    
    protected function checkNominal($details){
            $compare =  preg_replace("/charges/i", '', $details);
            $compare =  preg_replace("/charge/i", '', $compare);
            foreach($this->nominalCodes as  $nominal=>$code ){
                similar_text($compare,$nominal,$percent);
                if($percent>90){
                    return $code;
                }
               
            }
            return 4000;
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

        <script src="https://www.paypalobjects.com/api/checkout.js"></script>
        <script type="text/javascript">
            var grid = null;
            var DataTableFun = function () {
                var handleDataTable = function () {
                    var datatableurl = "invoice_list.php?action=invoice_ajax&type=sale&itype=<?php echo $this->invoice_type ?>";
                    grid = new Datatable();
                    grid.init({
                        src: $("#invoice_send_databale"),
                        onSuccess: function (grid,response) {
                            // execute some code after table records loaded
                            if (response.recordsTotal > 0) {
                                $("#csv_download_sale_btn").show();
                                $("#manual_csv_download_sale_btn").show();
                            } else {
                                $("#csv_download_sale_btn").hide();
                                $("#manual_csv_download_sale_btn").hide();
                            }
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
                            //btn-danger
                            "fnRowCallback": function( row, data) {
                                
                                if (data.is_paid == 'Cancelled') 
                                {
                                   $(row).css('text-decoration','line-through' ); 
                                //   $(row).removeClass('even').removeClass('onhold').addClass('onhold');
                                }
                                
                            },
                
                            "bStateSave": true,
                            "columns": [
                                {"data": "actions", "bSortable": false},
                                {"data": "invoice_no"},
                                {"data": "invoice_date"},
                                {"data": "date_created"},
                                <?php if($this->user->getUserType() != User::USER_TYPE_CLIENT) { ?>
                                {"data": "user_account_id"},
                                <?php } ?>
                                {"data": "net_amount"},
                                {"data": "vat"},
                                {"data": "total_amount"},
                                <?php if($this->invoice_type == "mni") { ?>
                                        {"data": "invoice_heading"},     
                                <?php } ?>
                                {"data": "is_paid"}/*,
                                {"data": "added_by"},
                                {"data": "active"}*/
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
            var InvoiceFromgrid = null;
            var InvoiceFromDataTableFun = function () {
                var InvoiceFromhandleDataTable = function () {
                    var datatableurl = "invoice_list.php?action=invoice_ajax&type=purchase&itype=<?php echo $this->invoice_type ?>";
                    InvoiceFromgrid = new Datatable();
                    InvoiceFromgrid.init({
                        src: $("#invoice_received_databale"),
                        onSuccess: function (grid,response) {
                            // execute some code after table records loaded
                            if (response.recordsTotal > 0) {
                                $("#csv_download_purchase_btn").show();
                                $("#manual_csv_download_purchase_btn").show();
                            } else {
                                $("#csv_download_purchase_btn").hide();
                                $("#manual_csv_download_purchase_btn").hide();
                            }
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
                            "fnRowCallback": function( row, data) {
                                
                                if (data.is_paid == 'Cancelled') 
                                {
                                    $(row).css('text-decoration','line-through' ); 
                                   //$(row).removeClass('even').removeClass('onhold').addClass('onhold');
                                }
                                
                            },
                            "bStateSave": true,
                            "columns": [
                                {"data": "actions", "bSortable": false},
                                {"data": "invoice_no"},
                                {"data": "invoice_date"},
                                {"data": "date_created"},
                                <?php if($this->user->getUserType() != User::USER_TYPE_CLIENT) { ?>
                                {"data": "user_account_id"},
                                <?php } ?>
                                {"data": "net_amount"},
                                {"data": "vat"},
                                {"data": "total_amount"},
                                <?php if($this->invoice_type == "mni") { ?>
                                        {"data": "invoice_heading"}, 
                                <?php } ?>
                                {"data": "is_paid"}/*,
                                {"data": "added_by"},
                                {"data": "active"}*/
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
                
                paypal.Button.render({
                    style: {
                        size: 'medium',
                        color: 'blue',
                        shape: 'rect',
                        label: 'checkout',
                        tagline: false
                    },
                    env: '<?php echo ((PAYPAL_LIVE) ? "production" : "sandbox") ?>', // Or 'production' ,'sandbox'
                    // Set up the payment:
                    // 1. Add a payment callback
                    payment: function(data, actions) {
                      // 2. Make a request to your server
                      var pay_invoice_id = $('#pay_invoice_id').val();
                      var amount = $('#amount').val();
                      var pay_date = $('#pay_date').val();
                      var description = $('#description').val();
                      return actions.request.post('/invoice_list.php', { 
                        pay_invoice_id: pay_invoice_id,
                        amount: amount,
                        pay_date: pay_date,
                        description: description,
                        action: "paypal_create_payment"
                      }).then(function(res) {
                          // 3. Return res.id from the response
                          if(res.id) {
                            return res.id;
                          } if(res.status == "error") {
                                swal("Sorry!", res.message, "error");
                          } else {
                                swal("Sorry!", "Please refresh you page and try again if error persist please contact to info@oneworldexpress.com  ", "error");
                          }
                        });
                    },
                    // Execute the payment:
                    // 1. Add an onAuthorize callback
                    onAuthorize: function(data, actions) {
                      // 2. Make a request to your server
                      return actions.request.post('/invoice_list.php', {
                        payment_id: data.paymentID,
                        payer_id:   data.payerID,
                        account_id:   account,
                        action: "paypal_execute_payment"
                      }).then(function(res) {
                          // 3. Show the buyer a confirmation message.
                          if(res > 0) {
                                grid.getDataTable().ajax.reload();
                                InvoiceFromgrid.getDataTable().ajax.reload();
                                $('#pay_invoice_modal').modal("hide");
                                swal("Success!", "Invoice pay successfully", "success");
                          } else {
                              alert("Please refresh you page and try again if error persist please contact to info@oneworldexpress.com  ");
                          }
                        });
                    }
                }, '#paypal_btn');
                
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
                        var invCsv = $(clone).find('.inv_csv_file').attr('name');
                        var invCsvId = $(clone).find('.inv_csv_file').attr('id');

                        $(this).remove();

                        $(clone).find('input').val('');
                        $(clone).find('.inv_csv_file').attr('name', invCsv.replace(/\d+/, elindex));
                        $(clone).find('.inv_csv_file').attr('id', invCsvId.replace(/\d+/, elindex));
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
            function get_csv_purchase() {
                $('#purchase_invoice_form').submit();
            }
            function get_csv_sale() {
                $('#sale_invoice_form').submit();
            }
            function get_manual_csv_purchase() {
                $('#purchase_invoice_form').submit();
            }
            function get_manual_csv_sale() {
                $('#sale_invoice_form').submit();
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
                            url: "invoice_list.php?itype=mni&action=reset_pdf",
                            data: form_data,
                            type: "post",
                            dataType: "json",
                            success: function (response) {
                                if(response.status == "success") {
                                    grid.getDataTable().ajax.reload();
                                    InvoiceFromgrid.getDataTable().ajax.reload();
                                }
                            }
                        });
                    }
                });
            }
            function delete_auto_invoice(invoiceId) {
                swal({
                    title: "<?php echo Translation::GetCaption("Are you sure want to cancel this invoice") ?>",
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
                        $.ajax({
                            url: "invoice_list.php?action=delete_auto_invoice",
                            data: {invoice_id:invoiceId},
                            type: "post",
                            dataType: "json",
                            success: function (response) {
                                if(response.status == "success") {
                                    grid.getDataTable().ajax.reload();
                                    InvoiceFromgrid.getDataTable().ajax.reload();
                                     swal({
                                        type: 'success',
                                        html:true,
                                        title: "Success!",
                                        text: response.message
                                    });
                                }
                            }
                        });
                    }
                });
            }
            function send_email_auto_invoice(obj) {
                var to = $(obj).data('biling_email');
                var invoice_number = $(obj).data('invoice_number');
                var invoice_id = $(obj).data('invoice_id');
                var pdf = $(obj).data('invoice_pdf');
                var invoiceDate = $(obj).data('invoice_date');
                var company = $(obj).data('company');
                var mesage = "Dear " + company + ", \n\n Invoice " + invoice_number + " dated " + invoiceDate + " now available to download via the SmartTrack portal.\n\nThanks,\n\nOne World Express Inc Ltd Finance Team."
                
                $('#send_inv_email_to').val(to);
                $('#send_inv_email_subject').val(invoice_number);
                $('#send_inv_email_message').val(mesage);
                $('#send_inv_email_invoice_number').val(invoice_id);
                
                $('#send_sale_auto_invoice_email_modal').modal('show');
            }
            function send_email_manual_invoice(obj) {
                var to = $(obj).data('biling_email');
                var invoice_number = $(obj).data('invoice_number');
                var invoice_id = $(obj).data('invoice_id');
                var pdf = $(obj).data('invoice_pdf');
                var invoiceDate = $(obj).data('invoice_date');
                var company = $(obj).data('company');
                var mesage = "Dear " + company + ", \n\n Invoice " + invoice_number + " dated " + invoiceDate + " now available to download via the SmartTrack portal.\n\nThanks,\n\nOne World Express Inc Ltd Finance Team."
                get_files(invoice_id);
                $('#send_mni_email_to').val(to);
                $('#send_mni_email_subject').val(invoice_number);
                $('#send_mni_email_message').val(mesage);
                $('#send_mni_email_invoice_number').val(invoice_id);
                
                $('#send_sale_manual_invoice_email_modal').modal('show');
            }
            
            function get_data_files(obj) {
                var to = $(obj).data('biling_email');
                var invoice_number = $(obj).data('invoice_number');
                var invoice_id = $(obj).data('invoice_id');
                var pdf = $(obj).data('invoice_pdf');
                var invoiceDate = $(obj).data('invoice_date');
                var company = $(obj).data('company');
                var mesage = "Dear " + company + ", \n\n Invoice " + invoice_number + " dated " + invoiceDate + " now available to download via the SmartTrack portal.\n\nThanks,\n\nOne World Express Inc Ltd Finance Team."
                get_files(invoice_id);
                 $.ajax({
                    url: "invoice_list.php?action=get_manual_invoice_attachments",
                    data: {invoice_id:invoice_id},
                    type: "post",
                    dataType: "json",
                    success: function (response) {
                        if(response.status == "success") {   
                             $('#data_files_list').html(response.message);
                        }
                    }
                });
                $('#show_data_file_modal').modal('show');
            }
            function get_files(invoiceId){ 
                var responseHtml ;
                $.ajax({
                                   url: "invoice_list.php?action=get_manual_invoice_attachments",
                                   data: {invoice_id:invoiceId},
                                   type: "post",
                                   dataType: "json",
                                   success: function (response) {
                                       if(response.status == "success") {   
                                           if($.trim(response.message)!= '')
                                            $('#previous_files_list').html("<label> Previous Uploads </label>"+response.message);
                                      
                                           
                                       }
                                   }
                               });
                           
                }
            function change_pay_by() {
                $('#btn_pay_invoice').show();
                $('#paypal_btn').hide();
                $('.pay_by_link_box').hide();
                $('#billing_currency').val('');
                $('#billing_currency').select2();
                var select_pay_by = $('#pay_by').val();
                if (select_pay_by == "cheque") {
                    $('#cheque_box').show();
                } else if (select_pay_by == "bank_transfer") {
                    $('#bank_ref_no_box').show();
                } else if (select_pay_by == "paypal") {
                    $('#btn_pay_invoice').hide();
                    $('#paypal_btn').show();
                }
            }
            function pay_invoice_model(invoiceId,amount,payUrl,invoiceNumber,type,currency) {
                change_pay_by();
                $('#amount').val(amount);
                $('#pay_invoice_id').val(invoiceId);
                $('#pay_url').val(payUrl);
                $('.pay_for_invoice').html(invoiceNumber);
                $('#description').html("Paid Invoice amount for invoice #" + invoiceNumber);
                $('#curreny_amount').html(currency);
                if(type == "sale") {
                    $('#pay_by option[value="paypal"]').detach();
                } else {
                    // Set the value, creating a new option if necessary
                    if (!$('#pay_by').find("option[value='paypal']").length) { 
                        // Create a DOM Option and pre-select by default
                        var newOption = new Option("Paypal", "paypal", true, true);
                        // Append it to the select
                        $('#pay_by').append(newOption).trigger('change');
                    }
                }
                $('#pay_invoice_modal').modal("show");
            }
            function pay_invoice_now() {
                var form_data = $("#pay_invoice_form").serializeArray();
                var payUrl = $('#pay_url').val();
                $.ajax({
                    type: "POST",
                    url: payUrl,
                    data: form_data,
                    dataType: "json",
                    success: function (data) {
                        grid.getDataTable().ajax.reload();
                        InvoiceFromgrid.getDataTable().ajax.reload();
                        $('#pay_invoice_modal').modal("hide");
                        swal("Success!", data.message, "success");
                    },
                    error: function () {
                        swal("Error!", "Something went wrong please try again later", "error");
                    }
                });
            }
            function updateReadInvoice(invoice_id) {
                var form_data = new FormData();
                form_data.append('invoice_id', invoice_id);
                form_data.append('action', 'update_read_invoice');
                $.ajax({
                        url: "invoice_list.php",
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
                    <?php if($this->invoice_type == "INV") { ?>
                    Invoices
                    <?php } else { ?>
                    Manual Invoices
                    <?php } ?>
                </div>
                <div class="actions">
                    <span class="btn-xs btn btn-warning">Note:</span>  All below invoices are generated from the system.
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
                <div class="tabbable-line">
                    <ul class="nav nav-tabs ">
                        <?php $active = ""; ?>
                        <?php if($this->user->getUserType() != User::USER_TYPE_CLIENT) { ?>
                        <li class="active">
                            <a href="#sale_invoices" data-toggle="tab"> Sales Orders Invoices</a>
                        </li>
                        <?php 
                        } else {
                            $active = "active";
                        }
                        ?>
                        <li class="<?php echo $active; ?>">
                            <a href="#purchase_invoices" data-toggle="tab"> Purchased Orders Invoices</a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <?php if($this->user->getUserType() != User::USER_TYPE_CLIENT) { ?>
                        <div class="tab-pane active" id="sale_invoices">
                            <div class="table-container">
                                <div class="table-actions-wrapper">
                                    <?php if($this->invoice_type == "INV") { ?>
                                        <button class="btn btn-sm btn-default table-group-action-submit" id="csv_download_sale_btn" onclick="get_csv_sale()" data-original-title="Download csv" title="Download csv"><span></span><i class="fa fa-download"></i>&nbsp;Download CSV</button>
                                        <?php $saleFormAction = "invoice_list.php?action=invoices_download_csv&type=sale"; ?>
                                    <?php } else { ?>
                                        <button class="btn btn-sm btn-default table-group-action-submit" id="manual_csv_download_sale_btn" onclick="get_manual_csv_sale()" data-original-title="Download csv" title="Download csv"><span></span><i class="fa fa-download"></i>&nbsp;Download CSV</button>
                                        <?php $saleFormAction = "invoice_list.php?action=invoices_download_csv&type=sale&itype=mni"; ?>
                                    <?php } ?>
                                </div>
                                <form action="<?php echo $saleFormAction; ?>" method="post" id="sale_invoice_form" >
                                    <input type="hidden" name="action" value="filter" />
                                    <table class="table table-striped table-bordered table-hover table-condensed" id="invoice_send_databale">
                                        <thead>
                                            <tr role="row" class="heading">
                                                <th width="75">Actions</th>
                                                <th>Invoice Number</th>
                                                <th>Invoice Date</th>
                                                <th>Created Date</th>
                                                <?php if($this->user->getUserType() != User::USER_TYPE_CLIENT) { ?>
                                                <th>Account</th>
                                                <?php } ?>
                                                <th>Net Amount</th>
                                                <th>Vat</th>
                                                <th>Total Amount</th>
                                                <?php if($this->invoice_type == "mni") { ?>
                                                <th>Invoice Heading</th>
                                                <?php } ?>
                                                <th>Is Paid</th>
                                                <!--<th>Generated By</th>
                                                <th>Active</th>-->
                                            </tr>
                                            <tr role="row" class="filter">
                                                <td>
                                                    <div class="margin-bottom-5">
                                                        <button class="btn btn-xs blue filter-submit btn-outline" ><i class="fa fa-search"></i> </button>
                                                        <button class="btn btn-xs red filter-cancel mt-ladda-btn ladda-button btn-outline"><i class="fa fa-times"></i> </button>
                                                    </div>

                                                </td>
                                                <td>
                                                    <div class="input-group margin-bottom-5" >
                                                        <input type="text" class="form-control form-filter input-sm" name="invoice_no_from" placeholder="Invoice No From" >
                                                    </div>
                                                    <div class="input-group margin-bottom-5" >
                                                        <input type="text" class="form-control form-filter input-sm" name="invoice_no_to" placeholder="Invoice No To" >
                                                    </div>
                                                    
                                                    
                                                </td>
                                                <td>
                                                    <div class="input-group date date-picker margin-bottom-5" data-date-format="yyyy-mm-dd">
                                                        <input type="text" class="form-control form-filter input-sm" readonly name="invoice_date_from" placeholder="From">
                                                        <span class="input-group-btn">
                                                            <button class="btn btn-sm default" type="button">
                                                                <i class="fa fa-calendar"></i>
                                                            </button>
                                                        </span>
                                                    </div>
                                                    <div class="input-group date date-picker" data-date-format="yyyy-mm-dd">
                                                        <input type="text" class="form-control form-filter input-sm" readonly name="invoice_date_to" placeholder="To">
                                                        <span class="input-group-btn">
                                                            <button class="btn btn-sm default" type="button">
                                                                <i class="fa fa-calendar"></i>
                                                            </button>
                                                        </span>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="input-group date date-picker margin-bottom-5" data-date-format="yyyy-mm-dd">
                                                        <input type="text" class="form-control form-filter input-sm" readonly name="date_created_from" placeholder="From">
                                                        <span class="input-group-btn">
                                                            <button class="btn btn-sm default" type="button">
                                                                <i class="fa fa-calendar"></i>
                                                            </button>
                                                        </span>
                                                    </div>
                                                    <div class="input-group date date-picker" data-date-format="yyyy-mm-dd">
                                                        <input type="text" class="form-control form-filter input-sm" readonly name="date_created_to" placeholder="To">
                                                        <span class="input-group-btn">
                                                            <button class="btn btn-sm default" type="button">
                                                                <i class="fa fa-calendar"></i>
                                                            </button>
                                                        </span>
                                                    </div>
                                                </td>
                                                <?php if($this->user->getUserType() != User::USER_TYPE_CLIENT) { ?>
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
                                                <?php } ?>
                                                <td>
                                                    <input type="number" name="net_amount" class="form-control form-filter input-sm" >
                                                </td>
                                                <td>
                                                    <input type="number" name="vat" class="form-control form-filter input-sm" >
                                                </td>
                                                <td>
                                                    <input type="number" name="total_amount" class="form-control form-filter input-sm" >
                                                </td>
                                                <?php if($this->invoice_type == "mni") { ?>
                                                <td>
                                                    <input type="text" name="invoice_heading" class="form-control form-filter input-sm" >
                                                </td>
                                                <?php } ?>
                                                <td>
                                                    <select class="form-control form-filter input-sm" name="is_paid">
                                                        <option>Select Option</option>
                                                        <option value="1">Paid</option>
                                                        <option value="0">Un Paid</option>
                                                    </select>
                                                </td>
                                                <!--<td>
                                                    <input type="text" class="form-control form-filter input-sm" name="added_by">
                                                </td>
                                                <td>
                                                    <select class="form-control input-sm form-filter" name="active">
                                                        <option value="">Select</option>
                                                        <option value="yes">Yes</option>
                                                        <option value="no">No</option>
                                                    </select>
                                                </td>-->
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </form>
                            </div>
                        </div>
                        <?php } ?>
                        <div class="tab-pane <?php echo $active; ?>" id="purchase_invoices">
                            <div class="table-container">
                                 <div class="table-actions-wrapper">
                                    <?php if($this->invoice_type == "INV") { ?>
                                        <button class="btn btn-sm btn-default table-group-action-submit" id="csv_download_purchase_btn" onclick="get_csv_purchase()" data-original-title="Download csv" title="Download csv"><span></span><i class="fa fa-download"></i>&nbsp;Download CSV</button>
                                        <?php $purchaseFormAction = "invoice_list.php?action=invoices_download_csv&type=purchase"; ?>
                                    <?php } else { ?>
                                        <button class="btn btn-sm btn-default table-group-action-submit" id="manual_csv_download_purchase_btn" onclick="get_manual_csv_purchase()" data-original-title="Download csv" title="Download csv"><span></span><i class="fa fa-download"></i>&nbsp;Download CSV</button>
                                        <?php $purchaseFormAction = "invoice_list.php?action=invoices_download_csv&type=purchase&itype=mni"; ?>
                                    <?php } ?>
                                 </div>
                                <form action="<?php echo $purchaseFormAction; ?>" method="post" id="purchase_invoice_form" >
                                    <input type="hidden" name="action" value="filter" />
                                    <table class="table table-striped table-bordered table-hover table-condensed" id="invoice_received_databale">
                                        <thead>

                                            <tr role="row" class="heading">
                                                <th width="75">Actions</th>
                                                <th>Invoice Number</th>
                                                <th>Invoice Date</th>
                                                <th>Created Date</th>
                                                <?php if($this->user->getUserType() != User::USER_TYPE_CLIENT) { ?>
                                                <th>Account</th>
                                                <?php } ?>
                                                <th>Net Amount</th>
                                                <th>Vat</th>
                                                <th>Total Amount</th>
                                                <?php if($this->invoice_type == "mni") { ?>
                                                <th>Invoice Heading</th>
                                                <?php } ?>
                                                <th>Is Paid</th>
                                                <!--<th>Generated By</th>
                                                <th>Active</th>-->
                                            </tr>
                                            <tr role="row" class="filter">
                                                <td>
                                                    <div class="margin-bottom-5">
                                                        <button class="btn btn-xs blue filter-submit btn-outline" ><i class="fa fa-search"></i> </button>
                                                        <button class="btn btn-xs red filter-cancel mt-ladda-btn ladda-button btn-outline"><i class="fa fa-times"></i> </button>
                                                    </div>

                                                </td>
                                                <td>
                                                    <div class="input-group margin-bottom-5" >
                                                        <input type="text" class="form-control form-filter input-sm" name="invoice_no_from" placeholder="Invoice No From" >
                                                    </div>
                                                    <div class="input-group margin-bottom-5" >
                                                        <input type="text" class="form-control form-filter input-sm" name="invoice_no_to" placeholder="Invoice No To" >
                                                    </div>
                                                    
                                                    
                                                </td>
                                                <td>
                                                    <div class="input-group date date-picker margin-bottom-5" data-date-format="yyyy-mm-dd">
                                                        <input type="text" class="form-control form-filter input-sm" readonly name="invoice_date_from" placeholder="From">
                                                        <span class="input-group-btn">
                                                            <button class="btn btn-sm default" type="button">
                                                                <i class="fa fa-calendar"></i>
                                                            </button>
                                                        </span>
                                                    </div>
                                                    <div class="input-group date date-picker" data-date-format="yyyy-mm-dd">
                                                        <input type="text" class="form-control form-filter input-sm" readonly name="invoice_date_to" placeholder="To">
                                                        <span class="input-group-btn">
                                                            <button class="btn btn-sm default" type="button">
                                                                <i class="fa fa-calendar"></i>
                                                            </button>
                                                        </span>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="input-group date date-picker margin-bottom-5" data-date-format="yyyy-mm-dd">
                                                        <input type="text" class="form-control form-filter input-sm" readonly name="date_created_from" placeholder="From">
                                                        <span class="input-group-btn">
                                                            <button class="btn btn-sm default" type="button">
                                                                <i class="fa fa-calendar"></i>
                                                            </button>
                                                        </span>
                                                    </div>
                                                    <div class="input-group date date-picker" data-date-format="yyyy-mm-dd">
                                                        <input type="text" class="form-control form-filter input-sm" readonly name="date_created_to" placeholder="To">
                                                        <span class="input-group-btn">
                                                            <button class="btn btn-sm default" type="button">
                                                                <i class="fa fa-calendar"></i>
                                                            </button>
                                                        </span>
                                                    </div>
                                                </td>
                                                <?php if($this->user->getUserType() != User::USER_TYPE_CLIENT) { ?>
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
                                                <?php } ?>
                                                <td>
                                                    <input type="number" name="net_amount" class="form-control form-filter input-sm" >
                                                </td>
                                                <td>
                                                    <input type="number" name="vat" class="form-control form-filter input-sm" >
                                                </td>
                                                <td>
                                                    <input type="number" name="total_amount" class="form-control form-filter input-sm" >
                                                </td>
                                                <?php if($this->invoice_type == "mni") { ?>
                                                <td>
                                                    <input type="text" name="invoice_heading" class="form-control form-filter input-sm" >
                                                </td>
                                                <?php } ?>
                                                <td>
                                                    <select class="form-control form-filter input-sm" name="is_paid">
                                                        <option>Select Option</option>
                                                        <option value="1">Paid</option>
                                                        <option value="0">Un Paid</option>
                                                    </select>
                                                </td>
                                                <!--<td>
                                                    <input type="text" class="form-control form-filter input-sm" name="added_by">
                                                </td>
                                                <td>
                                                    <select class="form-control input-sm form-filter" name="active">
                                                        <option value="">Select</option>
                                                        <option value="yes">Yes</option>
                                                        <option value="no">No</option>
                                                    </select>
                                                </td>-->
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
        <div class="modal fade" tabindex="-1" role="dialog" id="send_sale_auto_invoice_email_modal" >
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">Send Email</h4>
                    </div>
                    <div class="modal-body">
                        <form method="post" action="invoice_list.php?action=send_email_auto_invoice" enctype="multipart/form-data">
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
        
        
        
        <div class="modal fade" tabindex="-1" role="dialog" id="show_data_file_modal" >
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">Data Files To Download</h4>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-sm-12">
                                <div id="data_files_list"></div>
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
        
        <div class="modal fade" tabindex="-1" role="dialog" id="send_sale_manual_invoice_email_modal" >
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">Send Email</h4>
                    </div>
                    <div class="modal-body">
                        <form method="post" action="invoice_list.php?itype=mni&action=send_email_manual_invoice" enctype="multipart/form-data">
                            <input type="hidden" name="invoice_id" id="send_mni_email_invoice_number" />
                            <div class="row">
                                <div class="col-md-6">
                                    <label class="label-account">To</label>
                                    <div class="form-group">
                                        <div class="input-group"> <span class="input-group-addon"> <i class="fa fa-star-o"></i> </span>
                                            <input class="form-control form-filter" id="send_mni_email_to" name="send_mni_email_to" type="text" placeholder="To" value="" rel="tooltip" data-original-title="To">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="label-account">CC</label>
                                    <div class="form-group">
                                        <div class="input-group"> <span class="input-group-addon"> <i class="fa fa-star-o"></i> </span>
                                            <input class="form-control form-filter" id="send_mni_email_cc" name="send_mni_email_cc" type="text" placeholder="CC" value="finance@oneworldexpress.com" rel="tooltip" data-original-title="CC">
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
                                                        <input type="file" name="inv_csv_file[0]" id="inv_csv_file_0" class="inv_csv_file"> 
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
                            
                            <div class="col-sm-12">
                                  
                                                
                                <div id="previous_files_list"></div>
                                    
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <label class="label-account">Subject</label>
                                    <div class="form-group">
                                        <div class="input-group"> <span class="input-group-addon"> <i class="fa fa-star-o"></i> </span>
                                            <input class="form-control form-filter" id="send_mni_email_subject" name="send_mni_email_subject" type="text" placeholder="Subject" value="" rel="tooltip" data-original-title="Subject">
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
                                            <textarea class="form-control" rows="12" name="send_mni_email_message" id="send_mni_email_message" ></textarea>
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
        <div class="modal fade" tabindex="-1" role="dialog" id="pay_invoice_modal" >
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">Pay Now (#<span class="pay_for_invoice"></span>)</h4>
                    </div>
                    <form method="post" id="pay_invoice_form">
                        <div class="modal-body">
                            <input type="hidden" id="action" name="action" value="pay_invoice" />
                            <input type="hidden" id="pay_invoice_id" name="pay_invoice_id" value="" />
                            <input type="hidden" id="pay_url" name="pay_url" value="" />
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Amount</label>
                                        <div class="input-group">
                                            <span class="input-group-addon"> <i class="fa fa-shopping-cart"></i></span>
                                            <input type="number" name="amount" id="amount" class="form-control validate_check" placeholder="Enter Amount" value="" readonly="readonly" />
                                            <span class="input-group-addon" id="curreny_amount"> GBP</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Pay Date</label>
                                        <div class="input-group date date-picker" data-date-format="yyyy-mm-dd">
                                            <input type="text" class="form-control input-sm" readonly name="pay_date" id="pay_date" placeholder="To" value="<?php echo date('Y-m-d'); ?>" >
                                            <span class="input-group-btn">
                                                <button class="btn btn-sm default" type="button">
                                                    <i class="fa fa-calendar"></i>
                                                </button>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="label-account">Select Method</label>
                                        <?php
                                            $pay_by_array = array("cash" => "Cash", "paypal" => "Paypal", "bank_transfer" => "Bank Transfer", "cheque" => "Cheque");
                                            echo Ddl::generateArrayDDL('pay_by', $pay_by_array, "", "", ' class="form-control select2" onchange="change_pay_by()" ', 'Select Pay by', 'pay_by', 'Select Pay by', '');
                                        ?>
                                    </div>
                                </div>
                                <div class="col-md-6 pay_by_link_box" id="cheque_box">
                                    <div class="form-group">
                                        <label>Cheque#</label>
                                        <div class="input-group">
                                            <span class="input-group-addon"> <i class="fa fa-shopping-cart"></i></span>
                                            <input type="text" name="cheque_number" id="cheque_number" class="form-control" value="" />
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 pay_by_link_box" id="bank_ref_no_box">
                                    <div class="form-group">
                                        <label>Ref No</label>
                                        <div class="input-group">
                                            <span class="input-group-addon"> <i class="fa fa-shopping-cart"></i></span>
                                            <input type="text" name="bank_ref_number" id="bank_ref_number" class="form-control" value="" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label> Description </label>
                                        <div class="input-group">
                                            <span class="input-group-addon"> <i class="fa fa-shopping-cart"></i></span>
                                            <textarea class="form-control" name="description" id="description" ></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <div class="modal-footer">
                            <button type="button" id="paypal_btn" class="btn btn-primary"></button>
                            <input type="button" value="PAY" id="btn_pay_invoice" class="btn btn btn-success" onclick="pay_invoice_now()" >
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        </div>
                    </div>
                </form>
                <!-- /.modal-content --> 
            </div>
            <!-- /.modal-dialog --> 
        </div>
        </div>
        <input type="hidden" name="elindex-hardcode" id="elindex-hardcode" value="0" />
        <div id="hidden_frm" style="display: none;">
            <form name="hiddenForm" id="hiddenForm" action="" method="POST">
                <input type="hidden" name="action" value="download" />
            </form>
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
            #send_inv_email_message {
                resize: vertical;
            }
            #paypal_btn{
                padding: 0px;
                display: none;
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
