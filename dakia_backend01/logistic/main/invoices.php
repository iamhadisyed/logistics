<?php
////////////////////////////////////////////////////
//
// Controller for Admin - Invoices list page
//
////////////////////////////////////////////////////
// get settings

require_once("../includes/settings/config.inc.php");

// set up local page class
class Page extends BasePage {
    /*     * *
     * This page's content
     * @return void 
     */

    private $page_count;

    public function init() {

        $this->breadCrumb['data'] = array(
            'index.php' => Translation::GetCaption("HOME"),
            'Manage invoices'
        );

        $user = SessionManager::getUser();
        if ($user->getUserType() != "finance" && $user->getUserType() != "admin") {
            util_redirect("index.php");
        }

        $this->userSession = SessionManager::getUser();
        if (isset($_POST['action']) && $_POST['action'] == 'GET_ACCOUNT_DETAIL') {
            $dataArray = array();
            $account = $this->form_vars['account'];
            $filter = new UserAccountFilter();
            $filter->addUserAccountFilter($account);
            $userGetColumn = $filter->getColumnList('id, user_account, user_type');
            if (count($userGetColumn) > 0) {
                foreach ($userGetColumn as $userData) {
                    if ($userData->getUserType() == 'corporateclient') {
                        $userId = $userData->getId();
                        break;
                    } else
                        $userId = $userData->getId();
                }
            }
            $dataArray['USER_ID'] = $userId;
            echo json_encode($dataArray);
            die;
        }
        else if (isset($_POST['action']) && $_POST['action'] == 'INVOICE_DETAILS') {
            $invoiceid = $this->form_vars['invoiceid'];
            $invoceFilterData = new InvoiceFilter();
//            $invoceFilterData->InvoiceNoFilter();
            $invoceFilterData->addQueryFilter(" inv.is_deleted <> 'Y' and inv.invoice_no = '" . $invoiceid . "'");
            $InvoiceList = $invoceFilterData->getInvoiceUserList();
            $dataArray = array();
            if (count($InvoiceList) > 0) {
                $dataArray['INVOICE_NO'] = 'INV' . $InvoiceList[0]->getInvoiceNo();
                $dataArray['INVOICE_FILE'] = 'INV' . $InvoiceList[0]->getInvoiceNo() . '.pdf';
                $dataArray['USER_NAME'] = $InvoiceList[0]->getFileDataType();
                $dataArray['USER_EMAIL'] = $InvoiceList[0]->getInvoiceFile();
                $dataArray['PARENTID'] = $InvoiceList[0]->getCreditType();
                $dataArray['ACCOUNT'] = $InvoiceList[0]->getAccount();
                $dataArray['DATED'] = date("d F Y", strtotime($InvoiceList[0]->getInvoiceDate()));
            }
            echo json_encode($dataArray);
            die;
            /*
             *

             */
        }
        if (isset($_POST['action']) && $_POST['action'] == 'SEND_EMAIL') {
            $invoiceids = array();
            $invoiceids = $this->form_vars['invoiceids'];


            $data_link = "";
            $inv_link = "";
            $date_link_msg = "";
            $msg = "Dear Sir/Madam  ,\n\n Please find link for invoice. Dated : " . date("Y-m-d") . "\n\n Your Invoice";
            foreach ($invoiceids as $value) {
                $invoceFilterData = new InvoiceFilter();
                $invoceFilterData->InvoiceNoFilter($value);
                $InvoiceList = $invoceFilterData->getInvoiceUserList();
                $dataArray = array();
                if (count($InvoiceList) > 0) {
                    $dataArray['INVOICE_NO'] = 'INV' . $InvoiceList[0]->getId();
                    $dataArray['INVOICE_FILE'] = 'INV' . $InvoiceList[0]->getId() . '.pdf';
                    $dataArray['INVOICE_FILE_CSV'] = 'INV-' . $InvoiceList[0]->getId() . '.csv';
                    $dataArray['USER_NAME'] = $InvoiceList[0]->getFileDataType();
                    $dataArray['USER_EMAIL'] = $InvoiceList[0]->getInvoiceFile();
                    $dataArray['PARENTID'] = $InvoiceList[0]->getCreditType();
                    $dataArray['ACCOUNT'] = $InvoiceList[0]->getAccount();
                    $dataArray['DATED'] = date("d F Y", strtotime($InvoiceList[0]->getInvoiceDate()));

                    $dataArraya[$count] = $dataArray['INVOICE_NO'];
                    $CsvFile = SETTING_MAIN_URL . "/InvoicesFiles/csv/" . $dataArray['INVOICE_FILE_CSV'];
                    if (file_exists($CsvFile)) {
                        $data_link = $CsvFile;
                    } else {
                        $data_link = SETTING_MAIN_URL . "/main/invoice_details.php?id=" . $dataArray['INVOICE_NO'] . "&action=Display_Invoice_csv";
                    }
                    $inv_link = SETTING_MAIN_URL . "/InvoicesFiles/pdf/" . $dataArray['INVOICE_FILE'] . "";
                    $msg .= " \n" . $inv_link . "";
                    $date_link_msg .= "\n" . $data_link . "";
                }
            }
            $msg .= "\n\n Your Data:" . $date_link_msg;
            $msg .= "\n\n " . formatMessages(ERROR_CONTACT_FINANCE) . "\n\n";
            $dataArray['msg'] = $msg;
            echo json_encode($dataArray);
            die;
        }

        if (isset($_POST['action']) && $_POST['action'] == 'INVOICE_EMAIL_SENT') {
            $inv_arr = array();
            $invoicenumber = $this->form_vars['invoicenumber'];
            if (preg_match('/,/', $invoicenumber)) {
                $inv_arr = explode(",", $invoicenumber);
                $invoiceId = str_replace('INV', '', $invoicenumber);
                $toemail = $this->form_vars['toemail'];
                $ccemail = $this->form_vars['ccemail'];
                $subjectemail = $this->form_vars['subjectemail'];
                $bodyemail = nl2br($this->form_vars['bodyemail']);


                $headers = 'MIME-Version: 1.0' . "\r\n";
                $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
                $headers .= 'Cc:' . $ccemail . "\r\n";
                $headers .= 'From: Finance OneWorldExpress<finance@oneworldexpress.com>' . "\r\n";
                mail($toemail, $subjectemail, "<font face='Calibri'><span style='font-size:12px !important;'>" . $bodyemail . "</span></font>", $headers);
                foreach ($inv_arr as $value) {
                    Invoices::runQuery("UPDATE invoices SET is_email = 'YES' WHERE id = '" . $value . "'");
                }
            } else {
                $invoiceId = str_replace('INV', '', $invoicenumber);
                $toemail = $this->form_vars['toemail'];
                $ccemail = $this->form_vars['ccemail'];
                $subjectemail = $this->form_vars['subjectemail'];
                $bodyemail = nl2br($this->form_vars['bodyemail']);


                $headers = 'MIME-Version: 1.0' . "\r\n";
                $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
                $headers .= 'Cc:' . $ccemail . "\r\n";
                $headers .= 'From: One World Express <finance@oneworldexpress.com>' . "\r\n";
                mail($toemail, $subjectemail, "<font face='Calibri'><span style='font-size:12px !important;'>" . $bodyemail . "</span></font>", $headers);
                Invoices::runQuery("UPDATE invoices SET is_email = 'YES' WHERE id = '" . $invoiceId . "'");
            }
            die;
        }



        $CouObj = new InvoiceFilter();
        if (isset($_POST["form_action"])) {
            $_SESSION["INVOICES_SEARCH"] = $_POST;
            $INVOICES_SEARCH = @$_SESSION["INVOICES_SEARCH"];
            $this->INVOICES_SEARCH = $INVOICES_SEARCH;
        } else {

            $INVOICES_SEARCH = @$_SESSION["INVOICES_SEARCH"];
            $this->INVOICES_SEARCH = $INVOICES_SEARCH;
        }

        if (@$_POST["form_action"] == 'btnLabelMerge') {
            $fileFlag = false;
            $generateLabel = $this->form_vars['deleteConsignments'];
            $generatelabelstr = implode(',', $generateLabel);

            if (count($generatelabelstr) > 0) {
                $pdf2 = new PDFMerger();
                // Create pdf
                //$pdf = new PdfBase(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
                //$pdf->SetX(1.0);
                //$filter->addServiceTypeFilter("DBP");
                $filterCon = new InvoiceFilter();
                //$filter->addStatusFilter(Consignment::STATUS_READY_TO_PRINT);
                $filterCon->addIdArrayFilter($generatelabelstr);
                //$filterCon->AddOrderByID();
                //$filterCon->addCommentIntoQuery('btnLabelMerge Query in client_list.php on line 209');
                $consignment_array = $filterCon->getColumnList('invoice_file, account, id');

                if (count($consignment_array) > 0) {
                    foreach ($consignment_array as $consignmentInformation) {
                        $fileNameNew = '../InvoicesFiles/pdf/' . $consignmentInformation->getInvoiceFile();
                        if (trim($fileNameNew) != '') {
                            $fileFlag = true;
                            $pdf2->addPDF($fileNameNew, 'all');
                        }
                    }
                    if ($fileFlag == true) {
                        $outFile = '../InvoicesFiles/temp/MERGE_INVOICE_' . date("d_M_Y__H_i_s", time()) . '.pdf';
                        //$pdf->Output($outFile, "F");
                        $pdf2->merge('file', $outFile);

                        echo '<script language="javascript">';
                        echo "window.open('" . $outFile . "','','width=400,height=300,screenX=50,left=50,screenY=50,top=50,status=yes,menubar=yes');";
                        echo 'document.location.href="../main/invoices.php";</script>';
                        die;
                    }
                }
            }
            util_redirect("../main/invoices.php");
        } else if (isset($INVOICES_SEARCH["form_action"]) && (@$INVOICES_SEARCH["form_action"] == 'Submit' || @$INVOICES_SEARCH["form_action"] == 'ok')) {

            $invoice_number_from = str_replace('inv', '', strtolower($INVOICES_SEARCH["invoice_number_from"]));
            $invoice_number_to = str_replace('inv', '', strtolower($INVOICES_SEARCH["invoice_number_to"]));
            if (trim($invoice_number_from) != "" && trim($invoice_number_to) == "") {
                $CouObj->InvoiceNoFilter($invoice_number_from);
            } else if (trim($invoice_number_from) != "" && trim($invoice_number_to) != "") {
                $CouObj->InvoiceNoRangeFilter($invoice_number_from, $invoice_number_to);
                //$CouObj->InvoiceNoRangeFilter($invoice_number);
            }

            if (isset($INVOICES_SEARCH["date_printed"]) && $INVOICES_SEARCH["date_printed"] != '')
                $CouObj->fromDateFilter(date('Y-m-d 00:00:00', strtotime($INVOICES_SEARCH["date_printed"])));
            if (isset($INVOICES_SEARCH["date_printed1"]) && $INVOICES_SEARCH["date_printed1"] != '')
                $CouObj->toDateFilter(date('Y-m-d 23:59:59', strtotime($INVOICES_SEARCH["date_printed1"])));

            $CouObj->IsActiveFilter('Y');
            $CouObj->IsDeletedFilter('N');

            if ($INVOICES_SEARCH["Account"] != "--- Please Select ---" && $INVOICES_SEARCH["Account"] != "") {
                $CouObj->addFieldFilter('account', $INVOICES_SEARCH["Account"]);
            }
            //$CouObj->orderBySort(' id DESC ');
        }
        if (isset($_POST["form_action"]) && isset($INVOICES_SEARCH["form_action"]) && $INVOICES_SEARCH["form_action"] == 'EXPORT_SAGE') {

            $invoice_number_from = str_replace('inv', '', strtolower($INVOICES_SEARCH["invoice_number_from"]));
            $invoice_number_to = str_replace('inv', '', strtolower($INVOICES_SEARCH["invoice_number_to"]));
            if (trim($invoice_number_from) != "" && trim($invoice_number_to) == "") {
                $CouObj->InvoiceNoFilter($invoice_number);
            } else if (trim($invoice_number_from) != "" && trim($invoice_number_to) != "") {
                $CouObj->InvoiceNoRangeFilter($invoice_number_from, $invoice_number_to);
                //$CouObj->InvoiceNoRangeFilter($invoice_number);
            }
            if (isset($INVOICES_SEARCH["date_printed"]) && $INVOICES_SEARCH["date_printed"] != '')
                $CouObj->fromDateFilter(date('Y-m-d 00:00:00', strtotime($INVOICES_SEARCH["date_printed"])));
            if (isset($INVOICES_SEARCH["date_printed1"]) && $INVOICES_SEARCH["date_printed1"] != '')
                $CouObj->toDateFilter(date('Y-m-d 23:59:59', strtotime($INVOICES_SEARCH["date_printed1"])));

            $CouObj->IsActiveFilter('Y');
            $CouObj->IsDeletedFilter('N');

            if ($INVOICES_SEARCH["Account"] != "--- Please Select ---" && $INVOICES_SEARCH["Account"] != "") {
                $CouObj->addFieldFilter('account', $INVOICES_SEARCH["Account"]);
            }
            $CouObj->orderBySort(' invoice_no DESC ');
            $getList = $CouObj->getList();
            $this->getExcelFile($getList);
        } else {
            $CouObj->IsActiveFilter('Y');
            $CouObj->IsDeletedFilter('N');
            $CouObj->orderBySort(' invoice_no DESC ');
        }

        /*
         *  END
         * Ajax Handling for Consignment table value
         */
        if (isset($_GET['action']) && $_GET['action'] == "invoices_ajax") {
            $this->table_msg = "Invoices in total";
            /*
             * Set columns orders for sorting
             */
            /* if (isset($this->form_vars['order'][0]['column']) && $this->form_vars['order'][0]['column'] != 0) {
              $dataTableColumnId = $this->form_vars['order'][0]['column'];
              $orderBy = $this->form_vars['order'][0]['dir'];
              $orderFalse = TRUE;
              if ($orderBy == 'desc') {
              $orderFalse = FALSE;
              }
              $dataTableColumnName = $this->form_vars['columns'][$dataTableColumnId]['data'];

              if(trim($dataTableColumnName) == 'account')
              $dataTableColumnName = 'user_account';
              if(trim($dataTableColumnName) == 'service_type')
              $dataTableColumnName = 'service_name';
              if(trim($dataTableColumnName) == 'shipment_status')
              $dataTableColumnName = 'consignment_status';
              if(trim($dataTableColumnName) == 'country_iso_code')
              $dataTableColumnName = 'country_name';




              }
             */

            /*
             * Column filter
             * For search
             */
            if (isset($this->form_vars['action']) && $this->form_vars['action'] == 'filter') {
                $invoiceFilterNew = new InvoiceFilter();

                $searchInvDateFrom = $this->form_vars['search_inv_date_from'];
                $searchInvDateTo = $this->form_vars['search_inv_date_to'];

                $searchRaisedDateFrom = $this->form_vars['search_raised_date_from'];
                $searchRaisedDateTo = $this->form_vars['search_raised_date_to'];

                if (!empty($searchInvDateFrom) || !empty($searchInvDateTo))
                    $invoiceFilterNew->addDateFilter($searchInvDateFrom, $searchInvDateTo, 'invoiced');

                if (!empty($searchRaisedDateFrom) || !empty($searchRaisedDateTo))
                    $invoiceFilterNew->addDateFilter($searchRaisedDateFrom, $searchRaisedDateTo, 'raised');

                $searchAccount = $this->form_vars['search_account'];
                if (!empty($searchAccount))
                    $invoiceFilterNew->addFilter("user_account.user_account LIKE '" . trim($searchAccount) . "%'");
                else {
                    if ($user->getUserType() == User::USER_TYPE_CORPORATE) {
                        $invoiceFilterNew->addFilter(' user_account.user_account_id ="' . $user->getUserAccountId() . '"');
                    }
                }

                $searchInvoiceNo = $this->form_vars['search_invoice_no'];
                if (!empty($searchInvoiceNo)) {
                    $invoiceType = $invoiceNo = array();

                    if (preg_match_all('/([a-z ]+[0-9]+)/i', $searchInvoiceNo, $mt)) {
                        $nrmt = count($mt[0]);
                        for ($i = 0; $i < $nrmt; $i++) {
                            if (preg_match('/([a-z ]+)([0-9]+)/i', $mt[0][$i], $mt2)) {
                                $invoiceType[$i] = trim($mt2[1]);
                                $invoiceNo[$i] = trim($mt2[2]);
                            }
                        }
                    }
                    if (count($invoiceNo) > 0)
                        $invoiceFilterNew->addFilter("invoices.invoice_no in ('" . implode("','", $invoiceNo) . "')");
                }

                /*

                  if(trim($dataTableColumnName)!= '')
                  {
                  if(trim($dataTableColumnName) == 'user_account' || trim($dataTableColumnName) == 'Account' )
                  $this->consignment_filter->AddOrderBy('user_account', 'order_by',$orderFalse);
                  else
                  $this->consignment_filter->AddOrderBy($dataTableColumnName, 'order_by',$orderFalse);

                  }else
                  $this->consignment_filter->AddOrderBy('date_created', 'order_by',false);

                 */
            }
            else {
                $invoiceFilterNew = new InvoiceFilter();
                if ($user->getUserType() == User::USER_TYPE_CORPORATE) {
                    $invoiceFilterNew->addFilter(' user.user_account_id ="' . $user->getUserAccountId() . '"');
                }
            }

            /*
             * Set pagination & Encode data into Json form to return to DataTable
             */
            $invoiceFilterNew->addQueryFilter(" invoices.is_deleted <> 1");
            $invoiceFilterNew->orderBySort('invoice_no DESC');
            $invoiceFilterNew->limitFilter('1');

            $iTotalRecords = $invoiceFilterNew->getPagingCount();
            $iDisplayLength = intval($_REQUEST['length']);
            $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
            $iDisplayStart = intval($_REQUEST['start']);
            $sEcho = intval($_REQUEST['draw']);
            $end = $iDisplayStart + $iDisplayLength;
            $end = $end > $iTotalRecords ? $iTotalRecords : $end;
            $invoiceFilterNew->setRowsPerPage($iDisplayLength);
// the offset of the list, based on current page
            $invoiceFilterNew->setOffset($iDisplayStart);
            $invoicingObjs = $invoiceFilterNew->getInvoiceAccountPagingList("invoices.id, 
            invoices.invoice_no, invoices.invoice_type, invoices.user_account_id, invoices.net_amount, invoices.vat, invoices.total_amount,
            invoices.weight, invoices.currency, invoices.exchange_rate, invoices.invoice_date, invoices.date_created, invoices.date_updated,
            invoices.date_deleted, invoices.added_by, invoices.is_active, invoices.is_deleted, invoices.credit_amount, invoices.credit_type,
            invoices.is_email, invoices.is_paid, invoices.paid_date, invoices.salepot_id, user_account.user_account 'user_account', user.user_name ");
            $invoiceDataArr = array();
            foreach ($invoicingObjs as $invoiceObj) {

                $invoicesArr['option'] = '<input type="checkbox" name="chkEmails[]" data-inv_id="' . $invoiceObj->getId() . '" id="chkEmails"  class="chkEmailcls" />';
                $invoicesArr['invoice_no'] = $invoiceObj->getInvoiceType() . $invoiceObj->getInvoiceNo();
                $invoicesArr['invoice_date'] = date('d M Y', strtotime($invoiceObj->getInvoiceDate()));
                $invoicesArr['raised_date'] = date('d M Y', strtotime($invoiceObj->getDateCreated()));
                ;
                $invoicesArr['account'] = '<span class="btn btn-xs default blue-stripe account-details" data-account="' . $invoiceObj->getUserAccountId() . '" data-action="GET_ACCOUNT_DETAIL">' . $invoiceObj->getUserAccount() . '</span>';
                $invoicesArr['net_amount'] = number_format(($invoiceObj->getTotalAmount() - $invoiceObj->getVat()), 2);
                $invoicesArr['vat'] = number_format($invoiceObj->getVat(), 2);
                $invoicesArr['total_amount'] = number_format($invoiceObj->getTotalAmount(), 2);
                $invoicesArr['currency'] = $invoiceObj->getCurrency();
                $invoicesArr['generated_by'] = $invoiceObj->getUserName();
                $invoicesArr['active'] = ($invoiceObj->getIsActive() == 1) ? 'YES' : 'NO';
                $invoicesArr['action'] = "";

                $invoicesArr['action'] .= '<a class="btn btn-xs default blue-stripe" href="../invoices/INV/pdf/' . $invoiceObj->getInvoiceType() . $invoiceObj->getInvoiceNo() . '.pdf" target="_blank" >PDF</a>';
                $CsvFile = '../InvoicesFiles/csv/INV-' . $invoiceObj->getInvoiceNo() . '.csv';

                if (file_exists($CsvFile))
                    $invoicesArr['action'] .= '<a class="btn btn-xs default blue-stripe" href="' . $CsvFile . '" >CSV</a>';
                else
                    $invoicesArr['action'] .= '<a class="btn btn-xs default blue-stripe" href="invoice_details.php?id=' . $invoiceObj->getInvoiceNo() . '&action=Display_Invoice_csv" >CSV</a>';
                $varEmail = ($invoiceObj->getIsEmail() == 'YES') ? 'Resend Email' : 'Send Email';
                $invoicesArr['action'] .= '<a href="#" title="Email Invoice INV' . $invoiceObj->getId() . '" data-target="#email-invoice" data-toggle="modal"> 
                    <input class="btn btn-xs default blue-stripe" id="btn_email" name="btn_email" type="button" value="' . $varEmail . '" onClick="emailData(\'' . $invoiceObj->getInvoiceNo() . '\');">
                </a>';
                if (in_array($this->userSession->getUserType(), array(User::USER_TYPE_FINANCE, User::USER_TYPE_ACCOUNT))) {
                    if ($countInvoiceFilterId == $invoiceObj->getInvoiceNo())
                        $invoicesArr['action'] .= '<a class="btn btn-xs default blue-stripe" href="invoice_details.php?id=' . $invoiceObj->getInvoiceNo() . '&action=confirmed_delete" onclick="return confirm(\'Are you sure you want to delete Invoice  INV' . $invoiceObj->getInvoiceNo() . '?\')">Delete</a>';
                }

                $invoiceDataArr[] = $invoicesArr;
            }

            $invoiceDataarr['data'] = $invoiceDataArr;
            $invoiceDataarr['draw'] = $sEcho;
            $invoiceDataarr['recordsTotal'] = $iTotalRecords;
            $invoiceDataarr['recordsFiltered'] = $iTotalRecords;
            echo json_encode($invoiceDataarr);
            die;
        }
        //	End Tahir Code
        $_SESSION['invoice_filter'] = $CouObj;
    }

    /*     * *
     * Insert content into HEAD section of html page.
     */

    public function renderHead() {
        
    }

    protected function addPagelavelCss() {
        ?>
        <link rel="stylesheet" type="text/css" href="../assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/select2/css/select2-bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/bootstrap-sweetalert/sweetalert.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/icheck/skins/all.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/bootstrap-select/css/bootstrap-select.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/jquery-multi-select/css/multi-select.css" rel="stylesheet" type="text/css" />
        <style type="text/css">
            .help-block-error{
                display: none !important;
            }
        </style>



        <?php
    }

    public function addPagelavelJs() {
        ?>
        <script type="text/javascript" src="../assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js" type="text/javascript"></script>
        <script src="../assets/global/scripts/datatable.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/datatables/datatables.min.js" type="text/javascript"></script>
        <script type="text/javascript" src="../assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js" type="text/javascript"></script>
        <script type="text/javascript" src="../assets/global/plugins/select2/js/select2.full.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/bootstrap-sweetalert/sweetalert.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/bootstrap-select/js/bootstrap-select.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/bootstrap-sweetalert/sweetalert.min.js" type="text/javascript"></script>
        <script src="../assets/global/scripts/app.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/bootstrap-select/js/bootstrap-select.min.js" type="text/javascript"></script>
        <?php
    }

    public function renderFooter() {
        ?>

        <script type="text/javascript">

            var clients = function () {
                var handleClients = function () {
                    grid = new Datatable();

                    grid.init({
                        src: $("#manage-invoices"),
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
                                "url": "invoices.php?action=invoices_ajax", // ajax source
                                headers: {

                                },
                            },
                            "bStateSave": true,

                            "columns": [{
                                    "data": "option", "bSortable": false
                                },
                                {"data": "invoice_no", 'sClass': 'name'
                                },
                                {
                                    "data": "invoice_date", 'sClass': 'date-created'
                                },
                                {
                                    "data": "raised_date", 'sClass': ''
                                },
                                {
                                    "data": "account", 'sClass': 'name'
                                },
                                {
                                    "data": "net_amount", "bSortable": false
                                },
                                {
                                    "data": "vat", "bSortable": false
                                },
                                {
                                    "data": "total_amount", "bSortable": false
                                },
                                {
                                    "data": "currency", "bSortable": false
                                },
                                {
                                    "data": "generated_by"
                                },
                                {
                                    "data": "active", "bSortable": false
                                },
                                {
                                    "data": "action", "bSortable": false
                                },
                            ]
                        }
                    });
                }
                return {
                    //main function to initiate the module
                    init: function () {
                        handleClients();
                    }
                };
            }();
            $(document).ready(function () {
                clients.init();
                if ($('.date-picker').length > 0) {
                    //init date pickers
                    $('.date-picker').datepicker({
                        autoclose: true
                    });
                }

            });
            $(document).ready(function () {
                $("#btnSubmit").click(function () {
                    $("#form_action").val("Submit");
                    $("#adminForm").submit();
                });
                $("#btnExport").click(function () {
                    $("#form_action").val("EXPORT_SAGE");
                    $("#adminForm").submit();
                })

                $('input[name="deleteConsignments[]"]').change(function () {
                    if ($('input[name="deleteConsignments[]"]:checked').length > 0)
                    {
                        $('#btn_multi_div').show();
                        $('#btn_regenerate_shipment').show();
                    } else
                    {
                        $('#btn_multi_div').hide();
                        $('#btn_regenerate_shipment').hide();
                    }
                });

                // Create Labels
                $("#btnLabelMerge").click(function () {
                    if (!consignmentSelectionConfirmation('There is no invoice to merge.', 'Please select invoice to merge.'))
                    {
                        return false;
                    }

                    if (confirm('Are you sure you want to merge invoice(s)'))
                    {
                        $("#form_action").val("btnLabelMerge");
                        $("#adminForm").submit();
                    }
                });


                // Check box for Email Hadi
                $('.chkEmailcls').click(function () {


                    if ($(".chkEmailcls:checkbox:checked").length > 0)
                    {
                        $("#btnEmail").prop('disabled', false);
                    } else
                    {
                        $("#btnEmail").prop('disabled', true);
                    }
                });

                $("#btnEmail").prop('disabled', true);
                $(".chkEmailcls").click(function () {

                    $("#btnEmail").prop('disabled', false);
                });

                $(".account-details").click(function () {
                    var e = $(this);
                    var account = e.data('account');
                    var action = e.data('action');
                    $.ajax({
                        url: 'invoices.php',
                        data: {account: account, action: action},
                        type: 'post',
                        success: function (response) {
                            var obj = jQuery.parseJSON(response);
                            var userId = obj.USER_ID;
                            window.open('customers_details.php?id=' + userId, '_blank', '');
                            //window.location	=	'customers_details.php?id='+userId;
                        }
                    });
                });
            });

            function consignmentSelectionConfirmation(errorMessage, errorMessagenew)
            {
                if (typeof (document.getElementsByName("deleteConsignments[]")) == 'undefined')
                {
                    alert(errorMessage);
                    return false;
                }
                var deleteConsign = document.getElementsByName("deleteConsignments[]");
                var txt = "";
                var i;
                for (i = 0; i < deleteConsign.length; i++) {
                    if (deleteConsign[i].checked) {
                        txt = txt + deleteConsign[i].value + "";
                    }
                }

                if (txt == "")
                {
                    alert(errorMessage);
                    return false;
                }
                return true;
            }

            function SetPageSize()
            {
                document.getElementById('adminForm').submit();
            }
            function checkedAll(group)
            {

                var count = 0;
                if (count == 0)
                {

                    $("#btnEmail").prop('disabled', true);
                    for (var i = 0, len = group.length; i < len; i++)
                    {

                        if (group[i].checked == false)
                            group[i].click();
                        count = 1;
                    }
                } else
                {
                    $("#btnEmail").prop('disabled', false);
                    for (var i = 0, len = group.length; i < len; i++)
                    {

                        if (group[i].checked == true)
                            group[i].click();
                        count = 0;
                    }
                }
            }
            function send_email() {
                $("#btnEmail").prop('disabled', true);
                //                event.preventDefault();
                var searchIDs = $(".chkEmailcls:checkbox:checked").map(function () {
                    return $(this).attr("data-inv_id");
                }).toArray();
                $("#pricing_details_msg").hide();
                $.ajax({
                    url: 'invoices.php',
                    data: {action: 'SEND_EMAIL', invoiceids: searchIDs},
                    type: 'post',
                    success: function (response) {
                        var obj = jQuery.parseJSON(response);
                        //                        $('#email_to').val(obj.USER_EMAIL);
                        var username = obj.USER_NAME;
                        if (obj.PARENTID == '166')
                        {
                            $('#email_to').val('camille@oneworldexpress.com, michelle@oneworldexpress.com, taoliu@oneworldexpress.cn');
                            $('#email_cc').val('finance@oneworldexpress.com, tao.fang@oneworldexpress.cn');
                            username = 'Camille';

                        } else if (obj.ACCOUNT == 'YANWEN')
                        {
                            $('#email_to').val('cheyenne.ma@yw56.com.cn, lisa@yw56.com.cn');
                            $('#email_cc').val('finance@oneworldexpress.com, mahj@yw56.com.cn');
                            username = 'Lisa/Cheyenne';


                        }
                        $('#email_invoice').val(searchIDs);
                        $('#email_body').val("");
                        var signature = "";
                        var bodytext = "";
                        signature = $("#signature_body").val();
                        bodytext = obj.msg;
                        bodytext += signature;
                        $('#email-invoice').modal('show');
                        $('#email_body').val(bodytext);
                        $("#btnEmail").prop('disabled', false);
                    }
                });




            }

            function UrlExists(url)
            {
                var http = new XMLHttpRequest();
                http.open('HEAD', url, false);
                http.send();
                return http.status != 404;
            }
            function emailData(invoiceId)
            {
                $('#pop-send-btn').removeAttr('disabled');
                $("#pricing_details_msg").hide();
                $.ajax({
                    url: 'invoices.php',
                    data: {action: 'INVOICE_DETAILS', invoiceid: invoiceId},
                    type: 'post',
                    success: function (response) {
                        var obj = jQuery.parseJSON(response);
                        $('#email_to').val(obj.USER_EMAIL);
                        var username = obj.USER_NAME;
                        if (obj.PARENTID == '166')
                        {
                            $('#email_to').val('camille@oneworldexpress.com, michelle@oneworldexpress.com, taoliu@oneworldexpress.cn');
                            $('#email_cc').val('finance@oneworldexpress.com, tao.fang@oneworldexpress.cn');
                            username = 'Camille';

                        } else if (obj.ACCOUNT == 'YANWEN')
                        {
                            $('#email_to').val('cheyenne.ma@yw56.com.cn, lisa@yw56.com.cn');
                            $('#email_cc').val('finance@oneworldexpress.com, mahj@yw56.com.cn');
                            username = 'Lisa/Cheyenne';

                        }
                        var signature = $('#signature_body').val();
                        $('#email_invoice').val(obj.INVOICE_NO);
                        $('#email_subject').val(obj.ACCOUNT + ",  " + obj.INVOICE_NO);
                        //username	=	obj.USER_NAME;
                        var urlCsvNumber = '<?php echo SETTING_MAIN_URL; ?>InvoicesFiles/csv/INV-' + invoiceId + '.csv';
                        if (UrlExists(urlCsvNumber) === true)
                        {
                            var invoiceData = '<?php echo SETTING_MAIN_URL; ?>InvoicesFiles/csv/INV-' + invoiceId + ".csv";
                        } else
                        {
                            var invoiceData = '<?php echo SETTING_MAIN_URL; ?>main/invoice_details.php?id=#INVOICEID#&action=Display_Invoice_csv';
                        }
                        var bodytext = "Dear " + username + ",\n\nPlease see below links for invoice number #INVOICENUMBER# dated " + obj.DATED + " and CSV back-up data.\n\nYour Invoice\n#INVOICELINK#\n Your Data: \n " + invoiceData + " \n\nIf you have any queries, please contact us at finance@oneworldexpress.com\n\n";
                        bodytext += signature; //
                        bodytext = bodytext.replace('#INVOICEID#', invoiceId);
                        bodytext = bodytext.replace('#INVOICENUMBER#', obj.INVOICE_NO);
                        bodytext = bodytext.replace('#INVOICELINK#', '<?php echo SETTING_MAIN_URL; ?>InvoicesFiles/pdf/' + obj.INVOICE_FILE);
                        $('#email_body').val(bodytext);
                        //var obj = jQuery.parseJSON(data);//$('#service').html('<option value="">Select Service</option>' + data);
                    }
                });

            }

            function validateEmail()
            {
                $("#pricing_details_msg").hide();
                var errorString = ''
                var toemail = $('#email_to').val();
                var subjectemail = $('#email_subject').val();
                var bodyemail = $('#email_body').val();
                var ccemail = $('#email_cc').val();
                var invoiceemail = $('#email_invoice').val();
                if (toemail == '')
                {
                    errorString += 'Please enter customer email address. <br>';
                }
                if (subjectemail == '')
                {
                    errorString += 'Please enter subject of email. <br>';
                }
                if (bodyemail == '')
                {
                    errorString += 'Please enter body of email. <br>';
                }

                if (errorString != '')
                {

                    if ($("#pricing_details_msg").hasClass("alert-success"))
                        $("#pricing_details_msg").removeClass("alert-success");
                    $("#pricing_details_msg").addClass("alert-danger");
                    $("#pricing_details_msg").html(errorString);
                    $("#pricing_details_msg").show();

                } else
                {
                    $('#pop-send-btn').attr('disabled', 'disabled');
                    $.ajax({
                        url: 'invoices.php',
                        data: {
                            action: 'INVOICE_EMAIL_SENT',
                            toemail: toemail,
                            subjectemail: subjectemail,
                            bodyemail: bodyemail,
                            ccemail: ccemail,
                            invoicenumber: invoiceemail,
                        },
                        type: 'post',
                        success: function (response) {
                            if ($("#pricing_details_msg").hasClass("alert-danger"))
                                $("#pricing_details_msg").removeClass("alert-danger");
                            $("#pricing_details_msg").addClass("alert-success");
                            $("#pricing_details_msg").html('Email has been successfully sent.');
                            $("#pricing_details_msg").show();
                            $("#btn_email").val('Resend Email');
                        }
                    });

                }

            }



        </script>
        <!--<script language="javascript" src="includes/3rdparty/calendar/calendar.js"></script>-->
        <?php
    }

    public function renderBody() {


        if (isset($this->INVOICES_SEARCH)) {
            foreach ($this->INVOICES_SEARCH as $key => $val)
                $$key = $val;
        }
        ?>
        <div class="portlet light">
            <div class="portlet-title">
                <div class="caption"> <i class="glyphicon glyphicon-List"></i>Invoice List </div>
                <div class="tools"> <a href="javascript:;" class="collapse"></a> </div>
            </div>
            <div class="portlet-body">
                <div style="min-height:200px;"  data-rail-color="blue" data-handle-color="blue">
                    <div class="table">

                        <table class="table table-striped table-bordered table-hover table-checkable" id="manage-invoices">
                            <thead>
                                <tr role="row" class="heading">
                                    <th width="1%">
                                        <label class="mt-checkbox mt-checkbox-single mt-checkbox-outline">
                                            <input type='checkbox' name='chkEmail'  id="chkEmail" onclick='checkedAll(chkEmails);' class="group-checkable"/>
                                            <span></span>
                                        </label>
                                    </th>
                                    <th>Invoice No</th>
                                    <th>Invoice Date</th>
                                    <th>Raised Date</th>
                                    <th>Account</th>
                                    <th>Net Amount</th>
                                    <th>VAT</th>
                                    <th>Total Amount</th>
                                    <th>Currency</th>
                                    <th>Generated By</th>
                                    <th>Status</th>
                                    <th>Action</th>

                                </tr>
                                <tr role="row" class="filter">
                                    <td>
                                        <div class="margin-bottom-5">
                                            <button class="btn btn-sm yellow filter-submit margin-bottom"><i class="fa fa-search"></i></button>
                                            <input type="hidden" class="form-filter" name="selected_manifest" id="selected_manifest" value=""  />
                                        </div>
                                        <button class="btn btn-sm red filter-cancel"><i class="fa fa-times"></i></button>
                                    </td>
                                    <td>
                                        <input type="text" class="form-control form-filter input-sm" name="search_invoice_no">

                                    </td>
                                    <td>
                                        <div class="input-group date date-picker margin-bottom-5" data-date-format="yyyy-mm-dd">
                                            <input type="text" class="form-control form-filter input-sm" readonly name="search_inv_date_from" placeholder="From">
                                            <span class="input-group-btn">
                                                <button class="btn btn-sm default" type="button"><i class="fa fa-calendar"></i></button>
                                            </span>
                                        </div>
                                        <div class="input-group date date-picker" data-date-format="yyyy-mm-dd">
                                            <input type="text" class="form-control form-filter input-sm" readonly name="search_inv_date_to" placeholder="To">
                                            <span class="input-group-btn">
                                                <button class="btn btn-sm default" type="button"><i class="fa fa-calendar"></i></button>
                                            </span>
                                        </div>
                                    </td>

                                    <td>
                                        <div class="input-group date date-picker margin-bottom-5" data-date-format="yyyy-mm-dd">
                                            <input type="text" class="form-control form-filter input-sm" readonly name="search_raised_date_from" placeholder="From">
                                            <span class="input-group-btn">
                                                <button class="btn btn-sm default" type="button"><i class="fa fa-calendar"></i></button>
                                            </span>
                                        </div>
                                        <div class="input-group date date-picker" data-date-format="yyyy-mm-dd">
                                            <input type="text" class="form-control form-filter input-sm" readonly name="search_raised_date_to" placeholder="To">
                                            <span class="input-group-btn">
                                                <button class="btn btn-sm default" type="button"><i class="fa fa-calendar"></i></button>
                                            </span>
                                        </div>
                                    </td>
                                    <td>
                                        <input type="text" class="form-control form-filter input-sm" name="search_account">
                                    </td>

                                    <td>
                                        <!-- VAT -->
                                    </td>
                                    <td>
                                        <!-- Total Amount	-->
                                    </td>
                                    <td>
                                        <!-- Currency	-->
                                    </td>
                                    <td>
                                        <!-- Generated By		-->
                                    </td>
                                    <td>
                                        <!-- Active/Inactive-->
                                    </td>
                                    <td>
                                        <!-- Action-->
                                    </td>
                                    <td>
                                        <!-- Action-->
                                    </td>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>


        <div class="modal fade bs-modal-lg" tabindex="-1" role="dialog" id="email-invoice" >
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">Email</h4>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-success" id="pricing_details_msg">Emailed successfully</div>
                        <input type="hidden" name="action" id="action" value="EMAIL_INVOICE_DETAIL" />
                        <input type="hidden" name="invoice_id" id="invoice_id" value="" />
                        <div class="row">
                            <div class="col-md-12">
                                <fieldset class="fsStyle">
                                    <div class="row">
                                        <div class="form-group col-md-6">
                                            <label class="control-label font-green-soft">To:</label>
                                            <input type="text" name="email_to" id="email_to" value="" class="form-control input-sm customer_charges" />
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label class="control-label font-green-soft">CC:</label>
                                            <input type="text" name="email_cc" id="email_cc" value="finance@oneworldexpress.com" class="form-control input-sm customer_charges" />
                                            <input type="hidden" name="email_invoice" id="email_invoice" value="0"/>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="form-group col-md-12">
                                            <label class="control-label font-green-soft">Subject:</label>
                                            <input type="text" name="email_subject" id="email_subject" value="Please find the invoive " class="form-control input-sm customer_charges" />
                                        </div>
                                    </div>
                                    <div class="row">                                        
                                        <div class="form-group col-md-12">
                                            <label class="control-label font-green-soft">Message</label>
                                            <textarea name="email_body" id="email_body" class="form-control input-sm customer_charges" ></textarea>
                                            <textarea name="signature_body" id="signature_body" class="form-control input-sm customer_charges hidden" ><?php echo (trim($this->userSession->getUserSignature()) != '') ? $this->userSession->getUserSignature() : 'Thanks and Regards\r\nZahid Khan\r\nBusiness Development Manager\r\nOne World Express Inc. Ltd\r\nOne World House, Pump lane, Hayes, Middlesex UB3 3NB \r\n0208 8676060    0208 8676070    www.oneworldexpress.com'; ?></textarea>
                                        </div>

                                    </div>
                                </fieldset>
                            </div>

                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-success"  id="pop-send-btn" onclick="return validateEmail();" >Send Email</button>
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
        $menu = new Adminmenu(Adminmenu::INVOICES);
        //echo $menu ;
        $menu->render();
    }

    /*     * *
     * Controller logic goes here
     */

    public function getExcelFile($getInvoiceList) {
        require_once(SETTING_DIR_REMOTE . "Classes/PHPExcel.php");
        //date_default_timezone_set('Europe/London');
        /** PHPExcel */
        // Create new PHPExcel object
        $objPHPExcel = new PHPExcel();
        $excel_column = 1;

        $FontBoldArray = array(
            'font' => array(
                'bold' => true,
                'size' => 10,
                'name' => 'Calibri',
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER)
        );

        $border = array(
            'borders' => array(
                'top' => array(
                    'style' => 'thick'
                ),
                'bottom' => array(
                    'style' => 'thick'
                )
            )
        );
        // Set properties
        $objPHPExcel->getProperties()->setCreator("One World Express ")
                ->setLastModifiedBy("Accounting Syste,")
                ->setTitle("Office 2007 XLSX Sales Invoice")
                ->setSubject("Office 2007 XLSX Sales Invoice")
                ->setDescription("This document is generated from the system, generated using PHP classes.")
                ->setKeywords("office 2007 openxml php")
                ->setCategory("Sales Invoice");
        // Add some data
        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A1', 'TYPE')
                ->setCellValue('B1', 'ACCOUNT REF')
                ->setCellValue('C1', 'NOMINAL ACCOUNT REF')
                ->setCellValue('D1', 'DEPARTMENT ID')
                ->setCellValue('E1', 'DATE')
                ->setCellValue('F1', 'REF')
                ->setCellValue('G1', 'DETAILS')
                ->setCellValue('H1', 'NET AMOUNT')
                ->setCellValue('I1', 'TAX CODE')
                ->setCellValue('J1', 'TAX AMOUNT');
        $objPHPExcel->getActiveSheet()->getStyle('A1:J1')->applyFromArray($FontBoldArray);
        $objPHPExcel->getActiveSheet()->getStyle('A1:J1')->applyFromArray($border);
        // Miscellaneous glyphs, UTF-8
        if (count($getInvoiceList) > 0) {
            $countInvoceDataLine = 2;
            foreach ($getInvoiceList as $invoiceData) {
                $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('A' . $countInvoceDataLine, 'SI')
                        ->setCellValue('B' . $countInvoceDataLine, $invoiceData->getAccount())
                        ->setCellValue('C' . $countInvoceDataLine, '4000')
                        ->setCellValue('D' . $countInvoceDataLine, '')
                        ->setCellValue('E' . $countInvoceDataLine, date('d/m/Y', strtotime($invoiceData->getInvoiceDate())))
                        ->setCellValue('F' . $countInvoceDataLine, 'INV' . $invoiceData->getInvoiceNo())
                        ->setCellValue('G' . $countInvoceDataLine, 'Freight charges')
                        ->setCellValue('H' . $countInvoceDataLine, ($invoiceData->getTotalInvAmount() - $invoiceData->getVat()))
                        ->setCellValue('I' . $countInvoceDataLine, '')
                        ->setCellValue('J' . $countInvoceDataLine, $invoiceData->getVat());
                $countInvoceDataLine++;
            }
        }

        // Rename sheet
        $objPHPExcel->getActiveSheet()->setTitle('Sage Report 1');
        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        //$objPHPExcel->setActiveSheetIndex(0);
        // Redirect output to a client’s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="sage-report-' . time() . '.xlsx"');
        header('Cache-Control: max-age=0');

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        ob_clean();
        $objWriter->save('php://output');
        exit;
    }

}

/* ------------------------------------------------------------------------------ */
// create and render page
$PageObj = new Page(CONFIG_TEMPLATE_ADMIN);
$PageObj->show();
?>
