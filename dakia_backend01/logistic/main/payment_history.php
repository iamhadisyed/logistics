<?php
// get settings
require_once("../includes/settings/config.inc.php");

include_classes([
    'PHPExcel'
    ], '3rdparty/phpexcel');

include_classes([
    'tcpdf'
    ], '3rdparty/tcpdf');
include_classes([
    'paymentshistory.class',
    'paymentshistoryfilter.class',
    'currency.class',
    'currencyfilter.class',
    'consignmentcharges.class',
    'consignmentchargesfilter.class'
]);

class Page extends BasePage {
    /*     * *
     * Controller logic
     */

    private $user = "";
    private $accountId = 0;
    private $checkChild = 0;
    private $userTotalBalance = 0;
    private $userAccountIdGolb = 0;
    private $paymentHistoryFilter = [];
    private $account_id = 0 ;
    private $account_id_encode = '';
    
    
    protected function init() {
        $this->breadCrumb['data'] = array(
            'index.php' => Translation::GetCaption("HOME"),
            "Payment History"
        );
        $this->account_id = base64_decode(util_get("id"));
        $this->account_id_encode = util_get("id");
        $this->user = SessionManager::getUser();
        if (isset($this->account_id) && $this->account_id > 0) {
            $this->accountId = $this->account_id;
            $this->userAccountIdGolb = $this->accountId;
        }else{
            $this->userAccountIdGolb = $this->user->getUserAccountId();
        }
        
        $this->userTotalBalance = getBalance($this->userAccountIdGolb);
        /*
         * DataTable handlings
         */
        if (isset($_GET['action']) && $_GET['action'] == "payment_history_ajax") {
            $this->paymentHistoryFilter = new PaymentsHistoryFilter();
            
            $userAccountId = 0;
            if($this->accountId > 0) {
                $userAccountId = $this->accountId;
            } else {
                $userAccountId = $this->user->getUserAccountId();
            }
            $this->paymentHistoryFilter->where(['ua.id' => $userAccountId]);
            $this->paymentHistoryFilter->where(['ph.is_completed' => "yes"]);
            /*
             * Column filter
             * For search
             */
            if (isset($this->form_vars['action']) && $this->form_vars['action'] == 'filter') {
                $this->form_filter($this->form_vars);
            }
            /*
             * Pagination Logic Implemented
             * 
             */
            $iDisplayLength = intval($_REQUEST['length']);
            $iDisplayLength = $iDisplayLength < 0 ? 20 : $iDisplayLength;
            $iDisplayStart = intval($_REQUEST['start']);
            $sEcho = intval($_REQUEST['draw']);
//            $end = $iDisplayStart + $iDisplayLength;
            $where = $this->paymentHistoryFilter->getWhere();
            $paymentHistoryFilterObjs = PaymentsHistory::getTransactionHistory($userAccountId, $where, $iDisplayLength, $iDisplayStart);
            $iTotalRecords = $this->paymentHistoryFilter->getCount();
            $setDataArr = array();
            foreach ($paymentHistoryFilterObjs as $paymentHistoryFilterObj) {
                if (!empty($paymentHistoryFilterObj->getDateAdded())) {
                    $date_created = formatDate(date("d-m-Y", $paymentHistoryFilterObj->getDateAdded()));
                } else {
                    $date_created = "";
                }
                $currentArr = array();
                $currentArr['date_added'] = $date_created;
                $currentArr['payment_method'] = ucwords(str_replace("_"," ",$paymentHistoryFilterObj->getPaymentMethod()));
                $currentArr['payment_detail'] = $paymentHistoryFilterObj->getPaymentDetail();
                $currentArr['amount'] = $paymentHistoryFilterObj->getAmount();
                $currentArr['credit'] = $paymentHistoryFilterObj->getCredit();
                $currentArr['debit'] = $paymentHistoryFilterObj->getDebit();
                $currentArr['action'] = $paymentHistoryFilterObj->getBalance();
                $setDataArr [] = $currentArr;
            }
            $setDataArrJson['data'] = $setDataArr;
            $setDataArrJson['draw'] = $sEcho;
            $setDataArrJson['recordsTotal'] = $iTotalRecords;
            $setDataArrJson['recordsFiltered'] = $iTotalRecords;
            echo json_encode($setDataArrJson);
            die;
        }
        if (isset($this->form_vars['excel_download']) && $this->form_vars['excel_download'] == "download_excel") {
            $objPHPExcel = new PHPExcel();
            $objDataSheet = $objPHPExcel->getActiveSheet();
            $objDataSheet->setTitle('Account Statement');

            $headingArray = array(
                'font' => array(
                    'bold' => true,
                    'size' => 20,
                    'name' => 'Calibri',
                )
            );                
             $styleForReport = array(
                'font' => array(
                    'bold' => true,
                ),
                'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                ),
                'borders' => array(
                    'top' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                    ),
                    'bottom' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                    )
                )
             ); 
            $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(22);
            $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(22);
            $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(32);
            $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(22);
            $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(22);
            $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(22);
            //$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(22);
            $objPHPExcel->getActiveSheet()->getStyle('A1')->applyFromArray($headingArray);
            $objPHPExcel->getActiveSheet()->getStyle('A2:F2')->applyFromArray($styleForReport);
            $objPHPExcel->getActiveSheet()->SetCellValue('A1', "Account Statement");
            $objPHPExcel->getActiveSheet()->SetCellValue('A2', "Booking Date");
            $objPHPExcel->getActiveSheet()->SetCellValue('B2', "Payment Method");
            $objPHPExcel->getActiveSheet()->SetCellValue('C2', "Description");
            //$objPHPExcel->getActiveSheet()->SetCellValue('D2', "Billing Id");
            $objPHPExcel->getActiveSheet()->SetCellValue('D2', "Dr Amount");
            $objPHPExcel->getActiveSheet()->SetCellValue('E2', "Cr Amount");
            $objPHPExcel->getActiveSheet()->SetCellValue('F2', "Balance");
            $count = '3';
            $userAccountId = 0;
            if($this->accountId > 0) {
                $userAccountId = $this->accountId;
            } else {
                $userAccountId = $this->user->getUserAccountId();
            }
            $this->paymentHistoryFilter = new PaymentsHistoryFilter();
            $this->form_filter($this->form_vars);
            $this->paymentHistoryFilter->where(['ua.id' => $userAccountId]);
            $this->paymentHistoryFilter->where(['ph.is_completed' => "yes"]);
            $where = $this->paymentHistoryFilter->getWhere();
            $paymentHistoryFilterObjs = PaymentsHistory::getTransactionHistory($userAccountId, $where);
            foreach ($paymentHistoryFilterObjs as $paymentHistoryFilterObj) {
                if (!empty($paymentHistoryFilterObj->getDateAdded())) {
                    $date_created = date("d-m-Y", $paymentHistoryFilterObj->getDateAdded());
                } else {
                    $date_created = "";
                }
                $objPHPExcel->getActiveSheet()->SetCellValue('A'.$count, $date_created);
                $objPHPExcel->getActiveSheet()->SetCellValue('B'.$count, ucwords(str_replace("_"," ",$paymentHistoryFilterObj->getPaymentMethod())));
                $objPHPExcel->getActiveSheet()->SetCellValue('C'.$count, $paymentHistoryFilterObj->getPaymentDetail().' '.$paymentHistoryFilterObj->getBillingId());
                //$objPHPExcel->getActiveSheet()->SetCellValue('D'.$count, $paymentHistoryFilterObj->getBillingId());
                if($this->checkChild > 0) {
                    $objPHPExcel->getActiveSheet()->SetCellValue('D'.$count, $paymentHistoryFilterObj->getCredit());
                    $objPHPExcel->getActiveSheet()->SetCellValue('E'.$count, $paymentHistoryFilterObj->getDebit());
                } else {
                    $objPHPExcel->getActiveSheet()->SetCellValue('D'.$count,$paymentHistoryFilterObj->getDebit());
                    $objPHPExcel->getActiveSheet()->SetCellValue('E'.$count,$paymentHistoryFilterObj->getCredit());
                }
                $balance = $paymentHistoryFilterObj->getBalance();
                $objPHPExcel->getActiveSheet()->SetCellValue('F'.$count, $balance);
                $count++;
            }

            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename=account-statement-' . time() . '.xls'); // file name of excel
            header('Cache-Control: max-age=0');
            header('Cache-Control: max-age=1');
            header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
            header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
            header('Cache-Control: cache, must-revalidate');
            header('Pragma: public'); // HTTP/1.0
            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
            $objWriter->setIncludeCharts(TRUE);
            $objWriter->save('php://output');
            exit;
        }
        
        if(isset($this->form_vars['pdf_download']) && $this->form_vars['pdf_download'] == "pdf_download"){
            if (!file_exists(SETTING_DIR_REMOTE . '_assets/account_statement/pdf/')) {
                mkdir(SETTING_DIR_REMOTE . '_assets/account_statement/pdf/', 0755, true);
            }
            $this->pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
            $logo = User::getUserCompanyImages(true);
            $this->pdf->setFont("Arial", "", 8);
            $this->pdf->SetAuthor('Smart Track');
            $this->pdf->SetTitle('Account Statement');
            $this->pdf->SetSubject('Account Statement');
            $this->pdf->SetMargins(PDF_MARGIN_LEFT, '15', PDF_MARGIN_RIGHT);
            $this->pdf->setPrintHeader(false);
            /*$this->pdf->setPrintFooter(false);*/
            $this->pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
            $this->pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

            $userAccountId = 0;
            if($this->accountId > 0) {
                $userAccountId = $this->accountId;
            } else {
                $userAccountId = $this->user->getUserAccountId();
            }
            $userFilterSubObj = new UserAccountFilter();
            $userFilterSubObj->addFieldFilter('id', $userAccountId);
            $dataAccount = $userFilterSubObj->getList();
            $companyName = (!empty($dataAccount)?ucwords($dataAccount[0]->getCompany()):'');
            $fullName = (!empty($dataAccount)?ucwords($dataAccount[0]->getFullName()):'');
            $UserAccountName = (!empty($dataAccount)?ucwords($dataAccount[0]->getUserAccount()):'');
            
            $this->paymentHistoryFilter = new PaymentsHistoryFilter();
            $this->form_filter($this->form_vars);
            $this->paymentHistoryFilter->where(['ua.id' => $userAccountId]);
            $this->paymentHistoryFilter->where(['ph.is_completed' => "yes"]);
            $where = $this->paymentHistoryFilter->getWhere();
            $paymentHistoryFilterObjs = PaymentsHistory::getTransactionHistory($userAccountId, $where);
            $isFalse = false;
            $countPage = (count($paymentHistoryFilterObjs)/ 22);
            $countPage = ceil($countPage);
            $minusPage = $countPage;
            $count =1;
            $page = 1;
            $perPage = (count($paymentHistoryFilterObjs) <= 22)?count($paymentHistoryFilterObjs):'22';
            $totalRecord = count($paymentHistoryFilterObjs);
            $closeBalance = "0.00 GBP";
            $datapdf = '';
            foreach ($paymentHistoryFilterObjs as $key => $paymentHistoryFilterObj) {
                if (!empty($paymentHistoryFilterObj->getDateAdded())) {
                    $date_created = date("d-m-Y", $paymentHistoryFilterObj->getDateAdded());
                } else {
                    $date_created = "";
                }
                $balance = $paymentHistoryFilterObj->getBalance();
                if($key == 0) {
                    $closeBalance = $balance;
                }
                $datapdf .= "<tr>";
                    $datapdf .= '<td valign="middle" align="center">'.$date_created.'</td>';
                    $datapdf .= '<td valign="middle">'.$paymentHistoryFilterObj->getPaymentDetail().' ' .$paymentHistoryFilterObj->getBillingId().'</td>';
                    $datapdf .= '<td valign="middle" align="right">'.$paymentHistoryFilterObj->getCredit().'</td>';
                    $datapdf .= '<td valign="middle" align="right">'.$paymentHistoryFilterObj->getDebit().'</td>';
                    $datapdf .= '<td valign="middle" align="right">'.$balance.'</td>';
                $datapdf .= "</tr>";
                if($page <= $countPage && $count == $perPage){
                    $headerHtml = '<style type="text/css">';
                    $headerHtml .= 'table{
                                        border-collapse: collapse;
                                        width:100%;
                                        font-size:20px;                                        
                                     }
                                     table tr td {
                                        height:27px;
                                        padding-top:5px;
                                        padding-left:5px;
                                        padding-right:5px;
                                        padding-bottom:5px;
                                        border: 0.5px solid #C0C9CC;
                                        vertical-align: middle;
                                     }
                                     table tr th {
                                        font-weight: bold;
                                        height:15px;
                                        padding-top:5px;
                                        padding-left:5px;
                                        padding-right:5px;
                                        padding-bottom:5px;
                                        border: 0.5px solid #C0C9CC;
                                        vertical-align: middle !important;                                        
                                     }
                                     ';
                    $headerHtml .= '</style>';
                    $htmls = '';
                    $count = 0;
                    //if(!$isFalse){
                    //    $isFalse = true;
                        $headerHtml .= '<div style="text-align:left;">
                                            
                                        </div>
                                        <table cellpadding="3" border="0" style="border: 0px solid #FFFFFF;">
                                            <tr>
                                                <td width="50%" style="border: 0px solid #FFFFFF;" >
                                                    <img src="'.$logo.'" style="text-align:center;width:100px;">
                                                </td>
                                                <td style="border: 0px solid #FFFFFF; font-size: 25px; font-weight: bold;text-align: right;vertical-align: middle;" width="50%" align="right" valign="middle">
                                                    Statement of Account
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="border: 0px solid #FFFFFF;" width="50%">Full Name : '.$fullName.'<br/>Company Name : '.$companyName.'<br/>Account Name : '.  ucwords(strtolower($UserAccountName)).'</td>
                                                <td style="border: 0px solid #FFFFFF; text-align: right;vertical-align: middle;" width="50%" align="right" valign="middle">
                                                &nbsp;<br/>&nbsp;<br /><b>Date:</b> '.date("d-m-Y").'                                                    
                                                </td>
                                            </tr>
                                        </table>';

                    //}
                    $htmls .= '<tr style="background-color: #C0C9CC;">
                                    <th valign="middle" width="15%">Booking Date</th>
                                    <th valign="middle" width="40%">Description</th>
                                    <th valign="middle" align="right" width="15%">Credit</th>
                                    <th valign="middle" align="right" width="15%">Debit</th>
                                    <th valign="middle" align="right" width="15%">Balance</th>
                                </tr>';
                    $finalHtml = $headerHtml.'<table cellpadding="3" width="100%">'.$htmls.$datapdf.'</table>';

                    $this->pdf->AddPage();
                    $this->pdf->writeHTML($finalHtml);
                    $datapdf = '';
                    $totalRecord = $totalRecord - $perPage;
                    if($page == ($countPage-1) ){
                        $perPage = $totalRecord;
                    }
                    $page++;
                    $minusPage--;
                }
                $count++;
            }
            //echo $finalHtml; exit;
            //  die();
            $this->pdf->writeHTML('<h3 style="text-align:right;">Closing Balance :'. $closeBalance.'</h3>');
            $PDFfileLocation = SETTING_DIR_REMOTE . '_assets/account_statement/pdf/';
            $fileNameInvoice = "Account-Statement" . time() . '.pdf';
            $PDF_Filename = $PDFfileLocation . $fileNameInvoice;
            $this->pdf->Output($PDF_Filename, 'I');

            $output['FILENAME'] = $fileNameInvoice;
            $output['LINK'] = $PDF_Filename;
            header('Content-Type: application/pdf');
            header('Content-Disposition: attachment;filename=Account-Statement'     . time() . '.pdf'); // file name of excel
            header('Cache-Control: max-age=0');
            header('Cache-Control: max-age=1');
            header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
            header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
            header('Cache-Control: cache, must-revalidate');
            header('Pragma: public');
            exit;
        }
    }
    
    public function form_filter($obj) {
        
        $this->form_vars = $obj;
        
        $date_created_from = $this->form_vars['date_from'];
        $date_created_to = $this->form_vars['date_to'];
        if (!empty($date_created_from) && !empty($date_created_to)) {
            $this->paymentHistoryFilter->whereBetween ('ph.date_added', date('Y-m-d 00:00:00', strtotime($date_created_from)), date('Y-m-d 23:59:59', strtotime($date_created_to)));
        }
        $payment_detail = $this->form_vars['payment_detail'];
        if (!empty($payment_detail)) {
            $this->paymentHistoryFilter->where("(ph.payment_detail LIKE '%" . $payment_detail . "%')");
        }

        $filterArray = [];
        $payment_method = $this->form_vars['payment_method'];
        if (!empty($payment_method)) {
            $filterArray['ph.payment_method'] = $payment_method;
        }
        $amount = $this->form_vars['amount'];
        if (!empty($amount)) {
            $filterArray['ph.amount'] = $amount;
        }
        $credit = $this->form_vars['credit'];
        if (!empty($credit)) {
            $filterArray['ph.credit'] = $credit;
        }
        $debit = $this->form_vars['debit'];
        if (!empty($debit)) {
            $filterArray['ph.debit'] = $debit;
        }
        $this->paymentHistoryFilter->where($filterArray);
    }

    public function getCurrencyFormat($amount, $currency) {
        $amt = number_format((float) $amount, 2, '.', '');
        return $amt . " " . $currency;
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
                    var accountId = '<?php echo $this->account_id_encode ?>';
                    var datatableurl = "payment_history.php?action=payment_history_ajax";
                    if(accountId != "") {
                        datatableurl = "payment_history.php?action=payment_history_ajax&id="+accountId;
                    } else {
                        datatableurl = "payment_history.php?action=payment_history_ajax";
                    }
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
                            "pageLength": 150, // default record count per page
                            "ajax": {
                                "url": datatableurl, // ajax source
                                headers: {

                                },
                            },
                            "bStateSave": true,
                            "columns": [
                                {"data": "date_added", "bSortable": false,"sClass":"text-center"},
                                {"data": "payment_method", "bSortable": false},
                                {"data": "payment_detail", "bSortable": false},
                                {"data": "amount", "bSortable": false,"sClass":"text-right"},
                                {"data": "credit", "bSortable": false,"sClass":"text-right"},
                                {"data": "debit", "bSortable": false,"sClass":"text-right"},
                                {"data": "action", "bSortable": false,"sClass":"text-right"}
                            ],
                            fnDrawCallback: function (row, data, index) {
                                
                            }
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
                if ($('.date-picker').length > 0) {
                    //init date pickers
                    $('.date-picker').datepicker({
                        autoclose: true
                    });
                }
                $(document).on('click','.download_file',function(){
                    $("#excel_download").val('download_excel');
                    $("#pdf_download").val('');
                    $("#form_payment_history").submit();
                });
                $(document).on('click','.pdf_file',function(){
                    $("#pdf_download").val('pdf_download');
                    $("#excel_download").val('');
                    $("#form_payment_history").submit();
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
                    Payment History
                </div>
                <div class="actions">
                    <?php
                    $bClass = "green";
                    $accountBalance = "0.00 GBP";
                    if(count($this->userTotalBalance) > 0) {
                        $accountBalance = $this->userTotalBalance['balance'];
                        if($accountBalance <= 0) {
                            $bClass = "red";
                        }
                    }
                    ?>
                    <span><label class="btn <?php echo $bClass; ?> btn-outline btn-sm active"><strong>Account Balance <?php echo $accountBalance; ?></strong></label></span>
                    <a href="javascript:{};" class="btn blue margin-left-10 download_file"><i class="fa fa-download"></i> Download Excel File</a>
                    <a href="javascript:{};" class="btn yellow margin-left-10 pdf_file"><i class="fa fa-download"></i> Download PDF File</a>
                </div>
            </div>
            <div class="portlet-body">
                <form id="form_payment_history" name="form_payment_history" action="" method="post">
                    <input type="hidden" id="excel_download" name="excel_download" value="">
                    <input type="hidden" id="pdf_download" name="pdf_download" value="">
                    <table class="table table-striped table-bordered table-hover table-condensed" id="manage-data-table">
                        <thead>
                            <tr role="row" class="heading">
                                <th style="width: 150px;">Date</th>
                                <th style="width: 100px;">Method</th>
                                <th>Description</th>
                                <th style="width: 100px;">Amount</th>
                                <th style="width: 100px;">Credit</th>
                                <th style="width: 100px;">Debit</th>
                                <th style="width: 100px;">Balance</th>
                            </tr>
                            <tr role="row" class="filter">
                                <td>
                                    <div class="input-group date date-picker margin-bottom-5" data-date-format="dd-mm-yyyy">
                                        <input type="text" class="form-control form-filter input-sm" readonly name="date_from" placeholder="From">
                                        <span class="input-group-btn">
                                            <button class="btn btn-sm default" type="button">
                                                <i class="fa fa-calendar"></i>
                                            </button>
                                        </span>
                                    </div>
                                    <div class="input-group date date-picker" data-date-format="dd-mm-yyyy">
                                        <input type="text" class="form-control form-filter input-sm" readonly name="date_to" placeholder="To">
                                        <span class="input-group-btn">
                                            <button class="btn btn-sm default" type="button">
                                                <i class="fa fa-calendar"></i>
                                            </button>
                                        </span>
                                    </div>
                                </td>
                                <td>
                                    <?php
                                        $pay_by_array = array("" => "Select Method","cash" => "Cash", "paypal" => "Paypal", "bank_transfer" => "Bank Transfer", "cheque" => "Cheque", "bonus" => "Bonus");
                                        echo Ddl::generateArrayDDL('payment_method', $pay_by_array, "", "", ' class="form-control form-filter input-xs select2" ', 'Select Payment Method', 'payment_method', 'Select Payment Method', '');
                                    ?>
                                </td>
                                <td>
                                    <input type="text" class="form-control form-filter input-xs" name="payment_detail" id ="payment_detail" />
                                </td>
                                <td>
                                    <input type="text" class="form-control form-filter input-xs" name="amount" id ="amount" />
                                </td>
                                <td>
                                    <input type="number" class="form-control form-filter input-xs" name="credit" id ="credit" />
                                </td>
                                <td>
                                    <input type="number" class="form-control form-filter input-xs" name="debit" id ="debit" />
                                </td>
                                <td>
                                    <div class="margin-bottom-5">
                                        <button class="btn btn-xs blue filter-submit btn-outline" ><i class="fa fa-search"></i> </button>
                                        <button class="btn btn-xs red filter-cancel mt-ladda-btn ladda-button btn-outline"><i class="fa fa-times"></i> </button>
                                    </div>
                                </td>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </form>
            </div>
        </div>
        <div id="hidden_frm" style="display: none;">

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