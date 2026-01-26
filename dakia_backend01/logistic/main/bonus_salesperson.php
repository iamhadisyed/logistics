<?php
require_once("../includes/settings/config.inc.php");
include_classes([
    'tcpdf'
    ], '3rdparty/tcpdf');
include_classes([
    'carrierservice.class'
    ], 'general');
include_classes([
    'user.class',
    'userfilter.class',
    'useraccount.class',
    'useraccountfilter.class',
    'salespotcomission.class',
    'salespotcomissionfilter.class',
    'invoices.class',
    'invoicesfilter.class'
    ]);


class Page extends BasePage {
    /*     * *
     * Controller logic
     */

    public $LondonInvoiceFilter;
    public $LondonTotal;
    public $BriminghamInvoiceFilter;
    public $BriminghamTotal;
    public $msg;
    public $CompanyPot;
    public $SalePot = 0;
    public $SalePotId;
    public $UserSalePot;
    private $record_perpage = 25;
    private $company_total_comission = 0;

    protected function init() {
        $this->breadCrumb['data'] = array('index.php' => Translation::GetCaption("HOME"),
            "Salepot"
        );
	$this->msg = '';
	$user = $this->user = SessionManager::getUser();
	/*if ($user->getUserType() != "finance" && $user->getUserType() != "admin" && $user->getUserType() != "sales") {
	    util_redirect("index.php");
	}*/
    // Get Company total sale
        $userAccountOb = New CustomerAccount($this->user->getUserAccountId());
        $salesPotComissionFilter = New SalesPotComissionFilter();
        $salesPotComissionFilter->addFieldFilter("    paid_by",$this->user->getUserAccountId());
        $salesPotComissionFilter->addFieldFilter("    sale_user_id",$this->user->getId());
        $salesPotComissionObj = $salesPotComissionFilter->getColumnList('SUM(company_comission) AS company_comission,SUM(sales_comission) AS sales_comission');
        if(count($salesPotComissionObj) > 0){
            $this->company_total_comission = round($salesPotComissionObj[0]->getCompanyComission(),3)." ".$userAccountOb->getBillingCurrency();
            $this->SalePot = round($salesPotComissionObj[0]->getSalesComission(),3)." ".$userAccountOb->getBillingCurrency();;
        }
        // Handle sale data table
        if(isset($_GET['action']) && $_GET['action'] == "data_table_ajax"){
            $user = Sessionmanager::getUser();
            $salesPotComissionFilterObj = New SalesPotComissionFilter();
            $salesPotComissionFilterObj->addJoin("`invoices` i","i.id","spc.`invoice_id`","JOIN");
            $salesPotComissionFilterObj->addJoin("customer_account ua","ua.`id`","i.`user_account_id`","JOIN");
            $salesPotComissionFilterObj->addFieldFilter("spc.paid_by",$this->user->getUserAccountId());
            $salesPotComissionFilterObj->addFieldFilter("spc.sale_user_id",$this->user->getId());

            /*
            * Column filter
            * For search
            */
            if (isset($this->form_vars['action']) && $this->form_vars['action'] == 'filter') {

                $searchName = $this->form_vars['user_saleperson'];
                if (!empty($searchName))
                    $salesPotComissionFilterObj->addFieldFilter("    sale_user_id",$searchName);

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

                //$functionName = 'AddOrderBy' . $dataTableColumnName;
//                    echo $functionName; die;
                $salesPotComissionFilterObj->AddOrderBy(strtolower($dataTableColumnName), $orderFalse);
            }else{
                $salesPotComissionFilterObj->AddOrderBy("spc.id");
            }
            /*
             * Pagination Logic Implemented
             *
             */
            $iTotalRecords = $salesPotComissionFilterObj->getPagingCount();
            $iDisplayLength = intval($_REQUEST['length']);
            $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
            $iDisplayStart = intval($_REQUEST['start']);
            $sEcho = intval($_REQUEST['draw']);
            $end = $iDisplayStart + $iDisplayLength;
            $end = $end > $iTotalRecords ? $iTotalRecords : $end;
            $salesPotComissionFilterObj->setRowsPerPage($iDisplayLength);
            // the offset of the list, based on current page
            $salesPotComissionFilterObj->setOffset($iDisplayStart);
            $dataObjs = $salesPotComissionFilterObj->getPagingList("spc.*,i.`invoice_no`,i.`date_created` AS invoice_date_created,i.`net_amount`,ua.`user_account`");
            $dataTableArr = array();
            $dataArrJson = array();

            foreach ($dataObjs as $dataObjArr) {
                $arrSingleData = array();
                $userObj = New User($dataObjArr->getSaleUserId());
                $arrSingleData['invoice_no'] = $dataObjArr->getInvoiceNo();
                $arrSingleData['invoice_date_created'] = date('d-m-Y',strtotime($dataObjArr->getInvoiceDateCreated()));
                $arrSingleData['user_account'] = $dataObjArr->getUserAccount();
                $arrSingleData['net_amount'] = $dataObjArr->getNetAmount();
                $arrSingleData['sale_pot_percentage'] = $dataObjArr->getSalePotPercentage();
                $arrSingleData['sales_comission'] = $dataObjArr->getSalesComission();
                $arrSingleData['sale_user_id'] = $userObj->getFirstName().' '.$userObj->getLastName().' '.'[ '.$userObj->getUserName().' ]';
                $arrSingleData['option'] = '';
                $arrSingleData['action'] = '<span class="pull-right label label-sm bg-green-jungle bg-font-green-jungle line-height-2"> Paid</span>';
                $dataTableArr [] = $arrSingleData;
            }
            $dataArrJson['data'] = $dataTableArr;
            $dataArrJson['draw'] = $sEcho;
            $dataArrJson['recordsTotal'] = $iTotalRecords;
            $dataArrJson['recordsFiltered'] = $iTotalRecords;
            echo json_encode($dataArrJson);
            die;
        }



	//Genrate PDF file
	//$objInvoiceFilterTotal = new InvoiceFilter();
	//$this->LondonTotal = $objInvoiceFilterTotal->getLBTotalRecords('london');
	//$this->BriminghamTotal = $objInvoiceFilterTotal->getLBTotalRecords('birmingham');

	if (isset($_GET['action']) && $_GET['action'] = 'bonus_ajax') {
        $this->applyFilter($this->form_vars);
	    $page_no = isset($_POST['pageno']) ? $_POST['pageno'] : 1;
	    $invoive_type = isset($_POST['type']) ? $_POST['type'] : 'london';

	    $offset = ($page_no - 1) * $this->record_perpage;
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
            $InvoiceFilter = $this->invoiceFilter->getList("inv.*, ua.user_account,ua.sale_date, ua.sales_pot_time_period,spc.is_paid AS sales_pot_is_paid,ua.sales_pot_percentage AS sale_pot_percentage,ua.sales_rate AS company_pot_percentage,u_sale.user_name AS sale_user_agent,"
                    . "CONCAT(u.first_name,' ',u.last_name) AS user_name, "
                    . "(select rightsymbol from currency where id =currency_id ) as currency_id ,"
                    . "(SELECT user_name from user where id = inv.added_by) as added_by");
            $iTotalRecords = $this->invoiceFilter->getCount();
            //End  Get Count
            $setDataArr = array();
            
	    $htmlData = '';
	    if (count($InvoiceFilter) > 0) {
		foreach ($InvoiceFilter as $data) {
                $dateFrom = date('Y-m-d', strtotime(date('Y-m-d',strtotime($data->getSaleDate())).'+'.$data->getSalesPotTimePeriod().' months' ));
                $dateTo=date('Y-m-d',$data->getInvoiceDate());
                $currentArr = array();
//                $currentArr['actions'] = '';

                $currentArr['invoice_no'] = $data->getInvoiceNo();
                $currentArr['invoice_date'] = date("d-m-Y", $data->getInvoiceDate());
                $currentArr['user_account'] = $data->getUserAccount();
                $currentArr['net_amount'] = $data->getNetAmount() . ' ' . $data->getCurrencyId();
                $currentArr['vat_amount'] = $data->getVat() . ' ' . $data->getCurrencyId();
                $currentArr['total_amount'] = $data->getTotalAmount() . ' ' . $data->getCurrencyId();
                $currentArr['added_by'] = $data->getAddedBy();
                $currentArr['sale_pot_percentage'] = $data->getSalePotPercentage();
//                $currentArr['company_pot_percentage'] = $data->getCompanyPotPercentage();
                $currentArr['sale_user_agent'] = $data->getSaleUserAgent();
//                $currentArr['company_pot_value'] = round(($data->getCompanyPotPercentage()/100)*$data->getNetAmount(),3);
                $currentArr['sale_pot_value'] = round(($data->getSalePotPercentage()/100)*$data->getNetAmount(),3);

                $currentArr['is_active'] = ($data->getIsActive() == 'Y' ? 'Yes' : 'No');
                $currentArr['pay_now'] = '<span class="label label-sm label-warning"> Pending</span>';
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
	
//	$objComision = new SalesPotComissionFilter();
//	$objComision->addIsNotPaidFilter();
//	$Data = $objComision->getColumnList('company_comission,sales_comission');
//	if (count($Data)>0 && isset($Data[0])) {
//            $objComisionData = $Data[0];
//	    $this->CompanyPot = $objComisionData->getCompanyComission();
//	    $this->SalePot = $objComisionData->getSalesComission();
//	    $this->SalePotId = $objComisionData->getId();
//	} else {
//	    $this->CompanyPot = 0;
//	    $this->SalePot = 0;
//	    $this->SalePotId = 0;
//	}

	Sessionmanager::checkUserAccess(USER::PRIVILEGE_ADDUSER);
	$logMessage = array();
	// common initialisation for ths page


	$this->setTitle("Bonus");
	$objInvoiceFilter = new InvoiceFilter();
	//$this->LondonInvoiceFilter = $objInvoiceFilter->GetLondonCustomerInvoice('0','99999');
	//$this->BriminghamInvoiceFilter = $objInvoiceFilter->GetBriminghamCustomerInvoice('0','99999');


	//Export CSV
	if (isset($_POST['export_csv']) && $_POST['export_csv'] == "export_csv") {
	    $csvContent = "Invoice No,Invoice Date,Account, Amount,VAT,Currency ,Generated,Warehouse" . "\n";

	    foreach ($this->LondonInvoiceFilter as $data) {
		$csvContent .= preg_replace('/[\$,]/', '', $data->getInvoiceNo()) . ',';
		$csvContent .= preg_replace('/[\$,]/', '', $data->getInvoiceDate()) . ',';
		$csvContent .= preg_replace('/[\$,]/', '', $data->getAccount()) . ',';
		$csvContent .= preg_replace('/[\$,]/', '', $data->getInvoiceAmount()) . ',';
		$csvContent .= preg_replace('/[\$,]/', '', $data->getVat()) . ',';
		$csvContent .= preg_replace('/[\$,]/', '', $data->getCurrency()) . ',';
		$csvContent .= preg_replace('/[\$,]/', '', $data->getAddedBy()) . ',';
		$csvContent .= 'London ,';
		$csvContent .= "\n";
	    }
	    foreach ($this->BriminghamInvoiceFilter as $data) {
		$csvContent .= preg_replace('/[\$,]/', '', $data->getInvoiceNo()) . ',';
		$csvContent .= preg_replace('/[\$,]/', '', $data->getInvoiceDate()) . ',';
		$csvContent .= preg_replace('/[\$,]/', '', $data->getAccount()) . ',';
		$csvContent .= preg_replace('/[\$,]/', '', $data->getInvoiceAmount()) . ',';
		$csvContent .= preg_replace('/[\$,]/', '', $data->getVat()) . ',';
		$csvContent .= preg_replace('/[\$,]/', '', $data->getCurrency()) . ',';
		$csvContent .= preg_replace('/[\$,]/', '', $data->getAddedBy()) . ',';
		$csvContent .= 'Brimingham ,';
		$csvContent .= "\n";
	    }
	    header("Content-type: application/force-download");
	    header("Content-Transfer-Encoding: Binary");
	    header("Content-length: " . strlen($csvContent));
	    header('Content-Type: application/excel');
	    header('Content-Disposition: attachment; filename=SalePot-' . date('d-m-Y') . '.csv');
	    echo $csvContent;
	    exit;
	}
    }

    protected function applyFilter($form_vars) {
        $this->form_vars = $form_vars;
        $this->invoiceFilter = new InvoiceFilter();
        $this->invoiceFilter->join("user u", ['inv.added_by' => 'u.id']);
        $this->invoiceFilter->join("customer_account ua", ['inv.user_account_id' => 'ua.id']);
        $this->invoiceFilter->join("user u_sale", ['ua.sales_person' => 'u_sale.id']);
        $this->invoiceFilter->join("sales_pot_comission spc", ['inv.id' => 'spc.invoice_id'] ,'LEFT');
        $this->invoiceFilter->where(['inv.invoice_by' => $this->user->getUserAccountId()]);
        $this->invoiceFilter->where(['ua.sales_person' => $this->user->getId()]);
        $this->invoiceFilter->where(['ua.check_list_sales_pot' => 1]);
//        $this->invoiceFilter->where(['inv.is_active' => 1]);
        $this->invoiceFilter->where(['inv.is_deleted' => 0]);
        $this->invoiceFilter->where('spc.is_paid IS NULL');
        $this->invoiceFilter->where(' ua.sale_date <= inv.invoice_date
                                  AND CASE 
                                        WHEN ua.sale_date < -1 THEN 1=1
                                    ELSE
                                        inv.invoice_date <= DATE_ADD(ua.sale_date, INTERVAL `sales_pot_time_period` 
                                    MONTH) END ');
        $this->invoiceFilter->orderBy("inv.id",'desc');

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
    
    /**
     * Override to show the menu
     *
     */
    public function HandleLondonBrimingham() {
	$salesPotComissionFilter = new SalesPotComissionFilter();
	$salesPotComissionFilter->addIsNotPaidFilter();
	$salesPotComissionList = $salesPotComissionFilter->getList();

	if (count($salesPotComissionList) > 0) {
	    $salesPotComission = $salesPotComissionList[0];
	} else {
	    $salesPotComission = new SalesPotComission();
	    $salesPotComission->setDateAdded(date("Y-m-d H:i:s"));
	    $salesPotComission->setNoOfShipments(0);
	    $salesPotComission->setComission(0);
	    $salesPotComission->setCompanyComission(0);
	    $salesPotComission->setSalesComission(0);
	    $salesPotComission->setIsPaid(0);
	    $salesPotComission->save();
	}

	$sales_pot_comission_id = $salesPotComission->getId();
	$sales_pot_comission = $salesPotComission->getComission();
	$sales_pot_company_comission = $salesPotComission->getCompanyComission();
	$sales_pot_sales_comission = $salesPotComission->getSalesComission();
	$sales_pot_consignments = $salesPotComission->getNoOfShipments();

	$totalComission = $sales_pot_comission;
	$totalCompanyComission = $sales_pot_company_comission;
	$totalSalesComission = $sales_pot_sales_comission;
	$totalConsignments = $sales_pot_consignments;
	$current_commision = 0;
	$london_user_sales_percentage_arr = isset($_POST['london_user_sales_percentage']) ? $_POST['london_user_sales_percentage'] : $_POST['birmingham_user_sales_percentage'];
	if (isset($_POST['londonblkbtn']) || isset($_POST['bringhamblkbtn'])) {
	    $chk_invoice = (isset($_POST['chk_invoice'])) ? $_POST['chk_invoice'] : $_POST['chk_invoice_birmingham'];
	    $london_user_sales_amount_arr = (isset($_POST['london_user_sales_amount'])) ? $_POST['london_user_sales_amount'] : $_POST['birmingham_user_sales_amount'];
	    foreach ($chk_invoice as $invoiceNo => $invoiceType) {
		$InvoiceDetailFilter = new InvoiceDetailFilter();
		$london_user_sales_percentage = $london_user_sales_percentage_arr[$invoiceNo];
		$ReturnCommissionObj = $InvoiceDetailFilter->GetCommision($invoiceNo, $invoiceType, $sales_pot_comission_id);
		if (!empty($ReturnCommissionObj)) {
		    $BasicCharges = $ReturnCommissionObj[0]->getBasicCharges();
		} else {
		    $BasicCharges = $london_user_sales_amount_arr[$invoiceNo];
		}
		$CreditNoteFilter = new CreditNoteFilter();
		$CreditNoteFilter->addFieldFilter("cn_number", $invoiceNo);
		$CreditNoteFilter->IsNotPaidFilter();
		$CreditNoteResult = $CreditNoteFilter->getColumnList("cn_amount");
		if (!empty($CreditNoteResult)) {
		    $CreditAmount = $CreditNoteResult[0]->getCnAmount();
		    $CreditId = $CreditNoteResult[0]->getId();
		    $CreditAmountPer = $CreditAmount * $london_user_sales_percentage_arr[$invoiceNo] / 100;
		    $CreditNoteFilter->UpdateSalepotId($CreditId, $sales_pot_comission_id);
		}
		$comission = $BasicCharges * $london_user_sales_percentage_arr[$invoiceNo] / 100;
		$comission = $comission - $CreditAmountPer;
		$totalComission += $comission;
		$current_commision += $comission;
		$totalConsignments++;
	    }
	} else if ((isset($_POST['single_invoice_no']) && !empty($_POST['single_invoice_no'])) || (isset($_POST['single_invoice_no_birmingham']) && !empty($_POST['single_invoice_no_birmingham']))) {
	     $invoiceType = isset($_POST['single_invoice_type']) ? $_POST['single_invoice_type'] : $_POST['single_invoice_type_birmingham'];
            $londoncheckedinvoice = isset($_POST['single_invoice_no']) ? $_POST['single_invoice_no'] : $_POST['single_invoice_no_birmingham'];
            $single_invoice_amount = isset($_POST['single_invoice_amount']) ? $_POST['single_invoice_amount'] : $_POST['single_invoice_amount_birmingham'];
	        $InvoiceDetailFilter = new InvoiceDetailFilter();
		$ReturnCommissionObj = $InvoiceDetailFilter->GetCommision($londoncheckedinvoice, $invoiceType, $sales_pot_comission_id);
		if (!empty($ReturnCommissionObj)) {
		    $BasicCharges = $ReturnCommissionObj[0]->getBasicCharges();
		} else {
		    $BasicCharges = $single_invoice_amount;
		}
		$CreditNoteFilter = new CreditNoteFilter();
		$CreditNoteFilter->addFieldFilter("cn_number", $londoncheckedinvoice);
		$CreditNoteFilter->IsNotPaidFilter();
		$CreditNoteResult = $CreditNoteFilter->getColumnList("cn_amount");
		if (!empty($CreditNoteResult)) {
		    $CreditAmount = $CreditNoteResult[0]->getCnAmount();
		    $CreditId = $CreditNoteResult[0]->getId();
		    $CreditAmountPer = $CreditAmount * $london_user_sales_percentage_arr[$londoncheckedinvoice] / 100;
		    $CreditNoteFilter->UpdateSalepotId($CreditId, $sales_pot_comission_id);
		}
		$comission = $BasicCharges * $london_user_sales_percentage_arr[$londoncheckedinvoice] / 100;
		$comission = $comission - $CreditAmountPer;
		$totalComission += $comission;
		$current_commision += $comission;
		$totalConsignments++;
	}

	$SalesComission = 0.6 * $current_commision;
	$CompanyComission = 0.4 * $current_commision;
	$totalCompanyComission += $CompanyComission;
	$totalSalesComission += $SalesComission;
	$salesPotComission->setComission($totalComission);
	$salesPotComission->setNoOfShipments($totalConsignments);
	$salesPotComission->setDateAdded(date("Y-m-d H:i:s"));
	$salesPotComission->setCompanyComission($totalCompanyComission);
	$salesPotComission->setSalesComission($totalSalesComission);
	$salesPotComission->save();
	$this->msg = 'Data Successfully Saved.';
    }

    public function renderMenu() {
	$menu = new Adminmenu(Adminmenu::CUSTOMERS);
	$menu->render();
    }

    /*     * **************************** */
    /*     * *	defaultrouting        * */
    /*     * **************************** */
    /*     * *
     * Insert content in to HTML Head section
     */

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
                    var datatableurl = "bonus_salesperson.php?action=bonus_ajax";
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
                                // {"data": "actions", "bSortable": false},
                                {"data": "invoice_no"},
                                {"data": "invoice_date"},
                                {"data": "user_account"},
                                {"data": "net_amount"},
                                // {"data": "company_pot_percentage"},
                                // {"data": "company_pot_value"},
                                {"data": "sale_pot_percentage"},
                                {"data": "sale_pot_value"},
                                {"data": "sale_user_agent"},
                                {"data": "pay_now"}
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
            var gridNew = null;
            var DataTableFun2 = function () {
                var handleDataTable2 = function () {
                    gridNew = new Datatable();
                    gridNew.init({
                        src: $("#manage-data-table"),
                        onSuccess: function (gridNew) {
                            // execute some code after table records loaded
                        },
                        onError: function (gridNew) {
                            // execute some code on network or other general error
                        },
                        dataTable: {// here you can define a typical datatable settings from http://datatables.net/usage/options
                            "lengthMenu": [
                                [20, 50, 100, 150],
                                [20, 50, 100, 150] // change per page values here
                            ],
                            "pageLength": 20, // default record count per page
                            "ajax": {
                                "url": "bonus_salesperson.php?action=data_table_ajax", // ajax source
                                headers: {

                                },
                            },
                            "bStateSave": true,
                            "columns": [
                                {"data": "option", "bSortable": false},
                                {"data": "invoice_no", "bSortable": false},
                                {"data": "invoice_date_created", "bSortable": false},
                                {"data": "user_account", "bSortable": false},
                                {"data": "net_amount", "bSortable": false},
                                {"data": "sale_pot_percentage", "bSortable": false},
                                {"data": "sales_comission", "bSortable": false},
                                {"data": "sale_user_id", "bSortable": false},
                                {"data": "action", "bSortable": false}
                            ]
                        }
                    });
                }
                return {
                    //main function to initiate the module
                    init: function () {
                        handleDataTable2();
                    }
                };
            }();
            $(document).ready(function () {
                DataTableFun.init();
                DataTableFun2.init();
            
		$(document).on('click', '.btn_indvisual', function () {
            var single_invoice_no   =   $(this).data('invoice_no');
            var single_invoice_id   =   $(this).data('invoice_id');
            var single_invoice_type =   $(this).data('invoice_type');
            var single_invoice_amount   =   $(this).data('invoice_amount');
            var single_user_account   =   $(this).data('user_account');
            swal({
                    title: "Are you want to pay?",
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
                        $.ajax({
                            url: "bonus_salesperson.php",
                            data: {
                                single_invoice_no:single_invoice_no,
                                single_invoice_id:single_invoice_id,
                                single_invoice_type:single_invoice_type,
                                single_invoice_amount:single_invoice_amount,
                                single_user_account:single_user_account,
                                action: 'PAY_SINGLE_INVOICE'
                            },
                            type: "POST",
                            dataType: "json",
                            async: true,
                        })
                            // Code to run if the request succeeds (is done);
                            // The response is passed to the function
                            .done(function (json) {
                                grid.getDataTable().ajax.reload();
                                if (json.status == 'success') {
                                    $('#res_div').show().html("");
                                    $('#res_div').addClass('alert-success').removeClass('alert-danger');
                                    $('#res_div').show().html(json.message);
                                } else {
                                    $('#res_div').show().html("");
                                    $('#res_div').addClass('alert-danger').removeClass('alert-success');
                                    $('#res_div').show().html(json.message);
                                }
                                $(window).scrollTop(0);
                            })
                            // Code to run if the request fails; the raw request and
                            // status codes are passed to the function
                            .fail(function (xhr, status, errorThrown) {
                                console.log("Error: " + errorThrown);
                                console.log("Status: " + status);
                                console.dir(xhr);
                            })
                            // Code to run regardless of success or failure;
                            .always(function (xhr, status) {
                                // alert( "The request is complete!" );
                            });
                        }
                });
		});
		
		$('#export_csv').click(function (e) {
		    $('#adminForm').submit();
		});
		$("#chkLondon").click(function () {
		    if ($('.class_chk_invoice').is(':checked')) {
			$('.class_chk_invoice').prop('checked', false);
			$.uniform.update('.class_chk_invoice');
			$('.btn_indvisual').removeAttr('disabled');
		    } else {
			$('.class_chk_invoice').prop('checked', true);
			// $.uniform.update('.class_chk_invoice');
			$('.btn_indvisual').attr('disabled', 'disabled');
		    }
		});
		$("#export_pdf").click(function () {
            swal({
                    title: "Are you sure you want to mark as paid?",
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
                    var table_data = $("#table_data").html();
                    table_data = btoa(table_data);
                    $("#action").val(table_data);
                    $("#adminForm").submit();
                });
		});
	    });
	    function loadData(type, pageno) {
		$("#" + type + "_data_tbl tbody").html('Loading');
		$.post('bonus_salesperson.php', {func: 'getInvoices', pageno: pageno, type: type}, function (data) {
		    $("#" + type + "_data_tbl tbody").html(data);
		});
	    }
	</script>
	<?php
    }

    /*     * *
     * Content View
     */

    public function renderBody() {
	$user = SessionManager::getUser();
	?>
	<div class="portlet light">
	    <div class="portlet-title">
		<div class="caption"> <i class="icon-users"></i>
            Salepot
		</div>
		<div class="tools"> <a href="javascript:;" class="collapse"> </a> </div>
	    </div>

	    <div class="portlet-body">
	    	<div id="res_div" style="display: none;" class="col-md-12 alert alert-success"></div>
		<?php if ($this->SalePotId > 0) { ?>    
	    	<div class="row">
	    	    <div class="col-md-12">
	    		<button type="submit" class="btn btn-primary pull-right" id="export_csv" name="export_csv" value="export_csv">Download Excel</button>
	    	    </div>
	    	</div>
		<?php } ?>
		<ul class="nav nav-tabs">
		    <li class="active"><a data-toggle="tab" href="#london-tab">Outstanding</a></li>
<!--		    <li><a data-toggle="tab" href="#companypot-tab">Company Pot</a></li>-->
		    <li><a data-toggle="tab" href="#salespot-tab">Sales Pot</a></li>
		</ul>
                <div class="tab-content">
		    <div id="london-tab" class="tab-pane fade in active">
			<div class="table-scrollable"> 
			    <table class="table table-striped table-bordered table-hover table-condensed" id="invoice_send_databale">
				<thead>
				    <tr>
                        <th>Invoice No</th>
                        <th>Invoice Date</th>
                        <th>Account</th>
                        <th>Invoiced Amount</th>
<!--                        <th>Company Pot % </th>-->
<!--                        <th>Company Pot Amount </th>-->
                        <th>Sale Pot % </th>
                        <th>Sale Pot Amount </th>
                        <th>Sales Person </th>
                        <th>Action</th>
				    </tr>
				</thead>
				<tbody>
				    
				</tbody>
			    </table>                                
			    <input type="hidden" name="single_invoice_no" id="single_invoice_no" value="" />
			    <input type="hidden" name="single_invoice_type" id="single_invoice_type" value="" />
			    <input type="hidden" name="single_invoice_amount" id="single_invoice_amount" value="" />
			</div>
			
		    </div>
    
<!--		    <div id="companypot-tab" class="tab-pane fade">-->
<!--			<div class="row">-->
<!--			    <div class="col-md-12 center">-->
<!--				<h3 class="center"><b>Company Commision : --><?php //echo round($this->company_total_comission,3) . ' GBP'; ?><!--</b></h3>-->
<!--			    </div>-->
<!--			</div>-->
<!--		    </div>-->
		    <div id="salespot-tab" class="tab-pane fade">
                <div class="row">
                    <div class="col-md-12">
                        <div class="portlet light">
                            <div class="portlet-title">
                                <div class="caption"> <i class="fa fa-dropbox"></i>
                                    <b>Sales Commission : <span id="sale_commission_span"><?php echo $this->SalePot; ?></span></b>
                                </div>
                                <div class="actions"></div>
                                <div class="tools"> </div>
                            </div>
                            <div class="portlet-body">
                                <div class="table-container">
                                    <table class="table table-striped table-bordered table-hover" id="manage-data-table">
                                        <thead>
                                        <tr role="row" class="heading">
                                            <th>Options</th>
                                            <th>Invoice No</th>
                                            <th>Invoice Date</th>
                                            <th>Account</th>
                                            <th>Invoiced Amount</th>
                                            <th>Sale Pot % </th>
                                            <th>Sale Pot Amount </th>
                                            <th>Sales Person </th>
                                            <th>Action </th>
                                        </tr>
                                        <tr role="row" class="filter">
                                            <td>
                                                <button class="btn btn-sm btn-default blue btn-outline pull-left margin-bottom filter-submit"><i class="fa fa-search"></i></button>
                                                <button class="btn btn-sm btn-default red btn-outline pull-left filter-cancel margin-bottom"><i class="fa fa-times"></i></button>
                                            </td>
                                            <td> </td>
                                            <td> </td>
                                            <td> </td>
                                            <td> </td>
                                            <td> </td>
                                            <td> </td>
                                            <td> </td>
                                            <td> </td>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
		    </div>
		</div>
	    </div> <!-- End portlet body -->
	    <?php
	}

    }
    /* ------------------------------------------------------------------------------ */
// create and render page
    $PageObj = new Page(CONFIG_TEMPLATE_ADMIN);
    $PageObj->show();
    