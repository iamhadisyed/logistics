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
    private $accountSalePersonList = "";
    private $activeTab = "";
    private $pdf = "";

    protected function init() {
        $this->breadCrumb['data'] = array('index.php' => Translation::GetCaption("HOME"),
            "Sales Pot"
        );
        $this->msg = '';
        $user = $this->user = SessionManager::getUser();
        $userFilter = New UserFilter();
        $userFilter->addFieldFilter("    user_account_id",$this->user->getUserAccountId());
        $userFilter->addFieldFilter("user_type","sales_agent");
        $this->accountSalePersonList = $userFilter->getColumnList('first_name,last_name,user_name');
        /*if ($user->getUserType() != "finance" && $user->getUserType() != "admin" && $user->getUserType() != "sales") {
            util_redirect("index.php");
        }*/
        //Get users for salepot
//        $this->form_vars['user_sale_person']
        $salesPotComissionFilterObj = New SalesPotComissionFilter();
        $salesPotComissionFilterObj->addFieldFilter("    paid_by",$this->user->getUserAccountId());
        if(isset($this->form_vars['user_sale_person']) && !empty($this->form_vars['user_sale_person'])){
            $this->activeTab = 'active';
            $salesPotComissionFilterObj->addFieldFilter("    sale_user_id",$this->form_vars['user_sale_person']);
        }
        $salesPotComissionFilterObj->AddOrderById();
        $this->UserSalePot = $salesPotComissionFilterObj->getColumnList('*');
//        echo "<pre>";
//        print_r($this->UserSalePot);
//        echo "</pre>";
//        die;
        // Get Company total sale
//        $salesPotComissionFilter = New SalesPotComissionFilter();
//        $salesPotComissionFilter->addFieldFilter("    paid_by",$this->user->getUserAccountId());
////        echo "<pre>";
////        print_r($this->form_vars['user_sale_person']);
////        echo "</pre>";
////        die;
//        if(isset($this->form_vars['user_sale_person']) && !empty($this->form_vars['user_sale_person'])){
//            $salesPotComissionFilter->addFieldFilter("    sale_user_id",$this->form_vars['user_sale_person']);
//        }
        // Handle sale data table
        if(isset($_GET['action']) && $_GET['action'] == "data_table_ajax"){
            $user = Sessionmanager::getUser();
            $salesPotComissionFilterObj = New SalesPotComissionFilter();
            $salesPotComissionFilterObj->addFieldFilter("    paid_by",$this->user->getUserAccountId());

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
            $dataObjs = $salesPotComissionFilterObj->getPagingList();
            $dataTableArr = array();
            $dataArrJson = array();

            foreach ($dataObjs as $dataObjArr) {
                $arrSingleData = array();
                $userObj = New User($dataObjArr->getSaleUserId());
                $arrSingleData['date_created'] = date('d-m-Y',strtotime($dataObjArr->getDateCreated()));
                $arrSingleData['user_name'] = $userObj->getFirstName().' '.$userObj->getLastName().' '.'[ '.$userObj->getUserName().' ]';
                $arrSingleData['sale_pot_percentage'] = $dataObjArr->getSalePotPercentage() . " %";
                $arrSingleData['sales_comission'] = $dataObjArr->getSalesComission();
                $arrSingleData['option'] = "";
                $dataTableArr [] = $arrSingleData;
            }
            $dataArrJson['data'] = $dataTableArr;
            $dataArrJson['draw'] = $sEcho;
            $dataArrJson['recordsTotal'] = $iTotalRecords;
            $dataArrJson['recordsFiltered'] = $iTotalRecords;
            echo json_encode($dataArrJson);
            die;
        }
        if(isset($this->form_vars['action']) && $this->form_vars['action'] == "get_commission"){
            $userAccountOb = New CustomerAccount($this->user->getUserAccountId());
            // sale Agent total pot
            $salesPotComissionFilter = New SalesPotComissionFilter();
            $salesPotComissionFilter->addFieldFilter("    paid_by",$this->user->getUserAccountId());
            $salesPotComissionObj = $salesPotComissionFilter->getColumnList('SUM(company_comission) AS company_comission,SUM(sales_comission) AS sales_comission');
            if(count($salesPotComissionObj) > 0){
                $companyTotalComission = $salesPotComissionObj[0]->getCompanyComission();
                $salePot = $salesPotComissionObj[0]->getSalesComission();
            }
            $message['status'] = 'success';
            $message['company_commission'] = $companyTotalComission." ".$userAccountOb->getBillingCurrency();
            $message['sale_commission'] = $salePot." ".$userAccountOb->getBillingCurrency();
            echo json_encode($message);
            die;
        }
        //Genrate PDF file
        if (isset($_POST['action']) && $_POST['action'] != "") {
            if($this->form_vars['action'] == "PAY_SINGLE_INVOICE"){
                $response['status'] = "error";
                $response['message'] = "It's not a valid invoice [ ".$this->form_vars['single_invoice_no']." ]";

                // First check that if user account's Sales POT is active
                $userAccount = $this->form_vars['single_user_account'];
                $singleInvoiceId = $this->form_vars['single_invoice_id'];
                $invoiceAmount = $this->form_vars['single_invoice_amount'];
                $invoiceNo = $this->form_vars['single_invoice_no'];
                $invoiceFilter = New InvoiceFilter();
                $invoiceFilter->addFilter(['inv.id'=>$singleInvoiceId],'=');
                $invoiceFilter->join("invoice_detail AS id","id.invoice_id = inv.id");
                $invoiceData = $invoiceFilter->getList('COUNT(id.consignment_id) AS no_shipment');

                $userAccountObj = New CustomerAccount($userAccount);
                if($userAccountObj->getCheckListSalesPot() == "1"){
                    // Then check if it is in between valid date  $userAccountObj->getSalesPotTimePeriod()
                    $dateFrom = date('Y-m-d', strtotime(date('Y-m-d',strtotime($userAccountObj->getSaleDate())).'+'.$userAccountObj->getSalesPotTimePeriod().' months' ));
                    $dateTo=date('Y-m-d');
                    if ($dateFrom >= $dateTo) {
                        //valid
                        // Then check what percentage will effect amount
                        $invoiceObj = New Invoices($singleInvoiceId);
                        $invoiceObj->getCurrencyId();
                        $userAccountObj->getBillingCurrency();
                        $fromCountryCurrencySym = '';
                        $toCountryCurrencySym = '';
                        if($invoiceObj->getCurrencyId() == 0){
                            $fromCountryCurrencySym = 'GBP';
                        }else{
                            $currencyObj1 = New Currency($invoiceObj->getCurrencyId());
                            $fromCountryCurrencySym = $currencyObj1->getRightsymbol();
                        }
                        if($fromCountryCurrencySym != $userAccountObj->getBillingCurrency()){
                            $currencyObj2 = New Currency($userAccountObj->getBillingCurrency());
                            $toCountryCurrencySym = $currencyObj2->getRightsymbol();
                            $invoiceComission = ($invoiceAmount/100)*$userAccountObj->getSalesPotPercentage();
                            $companyComission = ($invoiceAmount/100)*$userAccountObj->getSalesRate();
                            $invoiceComission = Currency::convertCurrency($fromCountryCurrencySym, $toCountryCurrencySym, $invoiceComission);
                            $companyComission = Currency::convertCurrency($fromCountryCurrencySym, $toCountryCurrencySym, $companyComission);
                        }else{
                            $invoiceComission = ($invoiceAmount/100)*$userAccountObj->getSalesPotPercentage();
                            $companyComission = ($invoiceAmount/100)*$userAccountObj->getSalesRate();
                        }
                        // Now insert data into sales_pot_comission table
                        $salesPotComission = New SalesPotComission();
                        $salesPotComission->setDateAdded(date("Y-m-d H:i:s"));
                        $salesPotComission->setNoOfShipments($invoiceData[0]->getNoShipment());
                        $salesPotComission->setComission($invoiceComission);
                        $salesPotComission->setIsPaid('1');
                        $salesPotComission->setPaidBy($this->user->getUserAccountId());
                        $salesPotComission->setPaidDate(time());
                        $salesPotComission->setCompanyComission($companyComission);
                        $salesPotComission->setSalesComission($invoiceComission);
                        $salesPotComission->setCompanySalePercentage($userAccountObj->getSalesRate());
                        $salesPotComission->setSalePotPercentage($userAccountObj->getSalesPotPercentage());
                        $salesPotComission->setSaleUserId($userAccountObj->getSalesPerson());
                        $salesPotComission->setInvoiceId($singleInvoiceId);
                        $salesPotComission->setAddedBy($this->user->getId());
                        $salesPotComission->setSalepotTableData(serialize($userAccountObj));
                        $salesPotComission->save();
                        $response['status'] = "success";
                        $response['message'] = "Sale commission is paid successfully";
                    }
                }else{
                    $response['status'] = "error";
                    $response['message'] = "Invoice [ ".$this->form_vars['single_invoice_no']." ] sale pot is not active for user account";
                }
            }
            if($this->form_vars['action'] == "PAY_ALL_INVOICE"){
                if($this->form_vars['operation'] == "bulk_pay") {
                    $invoicesId = $this->form_vars['invoice_id'];
                    $response['status'] = "error";
                    $response['message'] = "It's not a valid invoice";
                    $invoiceNotFound = '';
                    foreach ($this->form_vars['invoice_id'] as $invoiceId) {
                        // Ger user account from invoice
                        $invoiceObj = New Invoices($invoiceId);
                        // check if invoice is alreay paid
                        $isAlreadyPaid = false;
                        $salesPotComissionFilter = New SalesPotComissionFilter();
                        $salesPotComissionFilter->addFieldFilter("    invoice_id", $invoiceId);
                        $salesPotComissionObj = $salesPotComissionFilter->getList('id');
                        if (count($salesPotComissionObj) > 0) {
                            $isAlreadyPaid = true;
                        }
                        // First check that if user account's Sales POT is active
                        $userAccount = $invoiceObj->getUserAccountId();
                        $invoiceAmount = $invoiceObj->getNetAmount();
                        $invoiceNo = $invoiceId;
                        $invoiceFilter = New InvoiceFilter();
                        $invoiceFilter->addFilter(['inv.id' => $invoiceNo], '=');
                        $invoiceFilter->join("invoice_detail AS id", "id.invoice_id = inv.id");
                        $invoiceData = $invoiceFilter->getList('COUNT(id.consignment_id) AS no_shipment');
                        $userAccountObj = New CustomerAccount($userAccount);
                        if ($userAccountObj->getCheckListSalesPot() == "1" && !$isAlreadyPaid) {
                            // Then check if it is in between valid date  $userAccountObj->getSalesPotTimePeriod()
                            $dateFrom = date('Y-m-d', strtotime(date('Y-m-d', strtotime($userAccountObj->getSaleDate())) . '+' . $userAccountObj->getSalesPotTimePeriod() . ' months'));
                            $dateTo = date('Y-m-d');
                            if ($dateFrom >= $dateTo) {
                                //valid
                                // Then check what percentage will effect amount
//                                $userAccountObj->getSalesPotPercentage();
//                                $invoiceObj->getCurrencyId();
//                                $userAccountObj->getBillingCurrency();
                                    $fromCountryCurrencySym = '';
                                    $toCountryCurrencySym = $userAccountObj->getBillingCurrency();
                                if ($invoiceObj->getCurrencyId() == 0) {
                                    $fromCountryCurrencySym = 'GBP';
                                }else{
                                    $currencyObj1 = New Currency($invoiceObj->getCurrencyId());
                                    $fromCountryCurrencySym = $currencyObj1->getRightsymbol();
                                }
                                if ($fromCountryCurrencySym != $toCountryCurrencySym) {
//                                    $currencyObj2 = New Currency($userAccountObj->getBillingCurrency());
//                                    $toCountryCurrencySym = $currencyObj2->getRightsymbol();
                                    $invoiceComission = ($invoiceAmount / 100) * $userAccountObj->getSalesPotPercentage();
                                    $companyComission = ($invoiceAmount / 100) * $userAccountObj->getSalesRate();
                                    $invoiceComission = Currency::convertCurrency($fromCountryCurrencySym, $toCountryCurrencySym, $invoiceComission);
                                    $companyComission = Currency::convertCurrency($fromCountryCurrencySym, $toCountryCurrencySym, $companyComission);
                                } else {
                                    $invoiceComission = ($invoiceAmount / 100) * $userAccountObj->getSalesPotPercentage();
                                    $companyComission = ($invoiceAmount / 100) * $userAccountObj->getSalesRate();
                                }
                                // Now insert data into sales_pot_comission table
                                $salesPotComission = New SalesPotComission();
                                $salesPotComission->setDateAdded(date("Y-m-d H:i:s"));
                                $salesPotComission->setNoOfShipments($invoiceData[0]->getNoShipment());
                                $salesPotComission->setComission($invoiceComission);
                                $salesPotComission->setIsPaid('1');
                                $salesPotComission->setPaidBy($this->user->getUserAccountId());
                                $salesPotComission->setPaidDate(time());
                                $salesPotComission->setCompanyComission($companyComission);
                                $salesPotComission->setSalesComission($invoiceComission);
                                $salesPotComission->setCompanySalePercentage($userAccountObj->getSalesRate());
                                $salesPotComission->setSalePotPercentage($userAccountObj->getSalesPotPercentage());
                                $salesPotComission->setSaleUserId($userAccountObj->getSalesPerson());
                                $salesPotComission->setInvoiceId($invoiceNo);
                                $salesPotComission->setAddedBy($this->user->getId());
                                $salesPotComission->setSalepotTableData(serialize($userAccountObj));
                                $salesPotComission->save();
                                $response['status'] = "success";
                            } else {
                                $invoiceNotFound .= "invoice [" . $invoiceObj->getInvoiceNo() . "] date is already passed <br>";
                            }
                        } else {
                            $invoiceNotFound .= "invoice [" . $invoiceObj->getInvoiceNo() . "] sale pot is not active for user account or invoice is already paid <br>";
                        }
                        if (empty($invoiceNotFound) && $response['status'] == "success") {
                            $response['message'] = "Sale commission is paid successfully <br>" . $invoiceNotFound;
                        } else {
                            $response['message'] = "It's not a valid for bonus <br>" . $invoiceNotFound;
                        }
                    }

                }
                if($this->form_vars['operation'] == "generate_pdf") {
                    $response['status'] = "success";
                    $invDataHtml = '';
                    if(count($this->UserSalePot) > 0){
                        foreach ($this->UserSalePot as $invData){
//                            $invData->getInvoiceId();
//                            $invData->getSaleUserId();
                            $invData->getCompanyComission();
                            $invData->getSalesComission();
                            $invData->getCompanySalePercentage();
                            $invData->getSalePotPercentage();
                            $invoiceObj = New Invoices($invData->getInvoiceId());
                            $userObjNew = New User($invData->getSaleUserId());
                            $userAccountObjNew = New CustomerAccount($invoiceObj->getUserAccountId());
                            $invDataHtml .= '
                                            <tr>
                                                <td>'.$invoiceObj->getInvoiceNo().'</td>
                                                <td>'.date("d-m-Y",$invoiceObj->getInvoiceDate()).'</td>
                                                <td>'.$userAccountObjNew->getUserAccount().'</td>
                                                <td>'.$invoiceObj->getNetAmount().'</td>
                                                <td>'.$invData->getCompanySalePercentage().'</td>
                                                <td>'.$invData->getCompanyComission().'</td>
                                                <td>'.$invData->getSalePotPercentage().'</td>
                                                <td>'.$invData->getSalesComission().'</td>
                                                <td>'.$userObjNew->getFirstName().' '.$userObjNew->getLastName().'</td>
                                                <td>Paid</td>
                                            </tr>
                                            ';
                        }
                    }
                    $commonStyle = 'style="font-size: 24px;font-weight: bold;height: 20px;"';
                    $commonStyle1 = 'style="font-size: 24px;font-weight: bold;"';
                    $commonStyle2 = 'style="border-bottom: 1px solid black;font-size: 24px;font-weight: bold;height: 20px;"';
                    $logo =    User::getUserCompanyImages(false,$this->user->getId());
                    $comapnyLogoHtml = ' <img width="100" height="80" src="'.$logo.'"  /> ';
                    $todayDate = date('m-d-Y');
                    $curTime = time();
                    $curFileName = $this->user->getUserName().'_'.$curTime;
                    $fileName = $curFileName.'_'.'salepot_team_commision.pdf';
                    $userAccountNewObj = New CustomerAccount($this->user->getUserAccountId());
                    $html = '
<table width="100%">
    <tbody>
    <tr>
        <td width="5%"> &nbsp; </td>
        <td width="90%">
            <table>
                <tbody>
                    <tr>
                        <td width="33%">
                            <table height="90">
                                <tbody>
                                <tr>
                                    <td '.$commonStyle1.'>'.$this->user->getAddress().'</td>
                                </tr>
                                <tr>
                                    <td '.$commonStyle1.'>'.$this->user->getAddress2().'</td>
                                </tr>
                                <tr>
                                    <td '.$commonStyle1.'>'.$this->user->getAddress3().'</td>
                                </tr>
                                <tr>
                                    <td '.$commonStyle1.'>'.$this->user->getPhone().'</td>
                                </tr>
                                <tr>
                                    <td '.$commonStyle1.'>Email: '.$this->user->getEmail().'</td>
                                </tr>
                                </tbody>
                            </table>
                        </td>
                        <td width="34%" style="font-size: 50px;font-weight: bold;"> Sale Pot </td>
                        <td width="33%">
                            <table height="90">
                                <tbody>
                                    <tr>
                                        <td>'.$comapnyLogoHtml.'</td>
                                    </tr>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                </tbody>
            </table>
            <br/><br/><br/>
            <table width="100%" border="1">
                <tbody>
                <tr>
                    <td '.$commonStyle.'>&nbsp;&nbsp;&nbsp;Report ID</td>
                    <td '.$commonStyle.'>&nbsp;&nbsp;&nbsp;'.$curFileName.'</td>
                </tr>
                <tr>
                    <td '.$commonStyle.'>&nbsp;&nbsp;&nbsp;DATE</td>
                    <td '.$commonStyle.'>&nbsp;&nbsp;&nbsp;'.$todayDate.'</td>
                </tr>
                <tr>
                    <td '.$commonStyle.'>&nbsp;&nbsp;&nbsp;ACCOUNT</td>
                    <td '.$commonStyle.'>&nbsp;&nbsp;&nbsp;'.$userAccountNewObj->getUserAccount().'</td>
                </tr>
                </tbody>
            </table>
            <br/><br/>
            <table width="100%" height="auto">
                <tbody>
                <tr>
                    <td '.$commonStyle2.'>Invoice No</td>
                    <td '.$commonStyle2.'>Invoice Date</td>
                    <td '.$commonStyle2.'>Account</td>
                    <td '.$commonStyle2.'>Invoiced Amount</td>
                    <td '.$commonStyle2.'>Company Pot % </td>
                    <td '.$commonStyle2.'>Company Pot Amount </td>
                    <td '.$commonStyle2.'>Sale Pot % </td>
                    <td '.$commonStyle2.'>Sale Pot Amount </td>
                    <td '.$commonStyle2.'>Sales Person </td>
                    <td '.$commonStyle2.'>Action</td>
                </tr>
                '.$invDataHtml.'
                </tbody>
            </table>
        </td>
        <td width="5%"> &nbsp; </td>
    </tr>
</table>
';
                    // initiate FPDI
                    $pdf = new TCPDF();

                    // set document information
                    $pdf->SetCreator(PDF_CREATOR);
                    $pdf->SetAuthor('One World Express');
                    $pdf->SetTitle('Sales Pot Commission');
                    $pdf->SetSubject('OWE Sales Pot Commission');
                    // set default header data
//                    $pdf->SetHeaderData(SETTING_DIR_REMOTE."images/inner-logo.png", PDF_HEADER_LOGO_WIDTH, "OWE Sales Pot Commission", "by " . $user->getFullName() . " On " . date("l jS M, Y G:i:s"), true);
                    // set header and footer fonts
//                    $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
                    $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
                    // set default monospaced font
                    $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

                    // set margins
                    $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
                    $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
                    $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
                    $pdf->SetPrintHeader(false);
                    // add a page
                    $pdf->AddPage();
                    $pdf->SetFont('freeserif', '', 8);
                    // now write some text above the imported page
                    $pdf->SetFont('Helvetica');
                    $pdf->writeHTML($html, true, false, true, false, '');
                    $todayDate = date('m-d-Y');
                    $path1 = SETTING_DIR_ASSETS ."sale_pot_report/";
                    if (!file_exists($path1))
                        @mkdir($path1, 0777);

                    $path = SETTING_DIR_ASSETS ."sale_pot_report/".$todayDate.'/';
                    if (!file_exists($path))
                        @mkdir($path, 0777);

                    $fileName = $this->user->getUserName().'_'.time().'_'.'salepot_team_commision.pdf';
                    $fileCompletePath = '../_assets/sale_pot_report/'.$todayDate.'/'.$fileName;
                    $pdf->Output($fileCompletePath, 'F');
                    $response['message'] = "<a target='_blank' href='$fileCompletePath'>Download  Sales Pot Commission Pdf</a>";
                }

            }
            echo json_encode($response);
            die;
            include_once(SETTING_DIR_REMOTE . "main/fpdi/fpdi.php");
            $table_data = base64_decode($_POST['action']);
            $salepot_id = $_POST['salepot_id'];
            if (isset($_POST['salespot_distribution_data']) && !empty($_POST['salespot_distribution_data'])) {
                $salespot_distribution_data = base64_decode($_POST['salespot_distribution_data']);
            }
            //SAVE TABLE DATA TO SALE POT COMMISION
//	    $SalesPotComissionFilter = new SalesPotComission($salepot_id);
//	    $SalesPotComissionFilter->setSalepotTableData($salespot_distribution_data);
//	    $SalesPotComissionFilter->setIsPaid("1");
//	    $SalesPotComissionFilter->setPaidBy($user->getId());
//	    $SalesPotComissionFilter->setPaidDate(time('Y-m-d h:i:s'));
//	    $SalesPotComissionFilter->save();
            // initiate FPDI
            $pdf = new TCPDF();

            // set document information
            $pdf->SetCreator(PDF_CREATOR);
            $pdf->SetAuthor('One World Express');
            $pdf->SetTitle('Sales Pot Commission');
            $pdf->SetSubject('OWE Sales Pot Commission');
            // set default header data
//            $pdf->SetHeaderData(SETTING_DIR_REMOTE."images/inner-logo.png", PDF_HEADER_LOGO_WIDTH, "OWE Sales Pot Commision", "by " . $user->getFullName() . " On " . date("l jS M, Y G:i:s"), true);
            // set header and footer fonts
//            $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
            $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
            // set default monospaced font
            $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

            // set margins
            $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
            $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
            $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

            // add a page
            $pdf->AddPage();
            $pdf->SetFont('freeserif', '', 8);
            // now write some text above the imported page
            $pdf->SetFont('Helvetica');
            $pdf->writeHTML($table_data, true, false, true, false, '');

            $pdf->Output('salepot_team_commision.pdf', 'F');
        }

        //$objInvoiceFilterTotal = new InvoiceFilter();
        //$this->LondonTotal = $objInvoiceFilterTotal->getLBTotalRecords('london');
        //$this->BriminghamTotal = $objInvoiceFilterTotal->getLBTotalRecords('birmingham');

        if (isset($_GET['action']) && $_GET['action'] == 'bonus_ajax') {
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
            $InvoiceFilter = $this->invoiceFilter->getList("inv.*, ua.user_account,ua.billing_currency,ua.sale_date, ua.sales_pot_time_period,spc.is_paid AS sales_pot_is_paid,ua.sales_pot_percentage AS sale_pot_percentage,ua.sales_rate AS company_pot_percentage,u_sale.user_name AS sale_user_agent,"
                . "CONCAT(u.first_name,' ',u.last_name) AS user_name, "
                . "(select rightsymbol from currency where id =currency_id ) as currency_id ,"
                . "(SELECT user_name from user where id = inv.added_by) as added_by"
            );
            $iTotalRecords = $this->invoiceFilter->getCount();
            //End  Get Count
            $setDataArr = array();

            $htmlData = '';
            if (count($InvoiceFilter) > 0) {
                foreach ($InvoiceFilter as $data) {
                    $dateFrom = date('Y-m-d', strtotime(date('Y-m-d',strtotime($data->getSaleDate())).'+'.$data->getSalesPotTimePeriod().' months' ));
                    $dateTo=date('Y-m-d',$data->getInvoiceDate());
                    $currentArr = array();
                    $currentArr['actions'] = '<input type="checkbox" name="chk_invoice_' . $invoive_type . '[' . $data->getInvoiceNo() . ']"  class="class_chk_invoice_' . $invoive_type . '" value="'.$data->getId().'">
                                        <input type="hidden" name="' . $invoive_type . '_user_sales_percentage[' . $data->getInvoiceNo() . ']" value="' . $data->getCreditAmount() . '"/>
                                        <input type="hidden" name="' . $invoive_type . '_user_sales_amount[' . $data->getInvoiceNo() . ']" value="' . $data->getNetAmount() . '"/> ';

                    $currentArr['invoice_no'] = $data->getInvoiceNo();
                    $currentArr['invoice_date'] = date("d-m-Y", $data->getInvoiceDate());
                    $currentArr['user_account'] = $data->getUserAccount();
                    $currentArr['net_amount'] = $data->getNetAmount() . ' ' . $data->getCurrencyId();
                    $currentArr['vat_amount'] = $data->getVat() . ' ' . $data->getCurrencyId();
                    $currentArr['total_amount'] = $data->getTotalAmount() . ' ' . $data->getCurrencyId();
                    $currentArr['added_by'] = $data->getAddedBy();
                    $currentArr['sale_pot_percentage'] = $data->getSalePotPercentage();
                    $currentArr['company_pot_percentage'] = $data->getCompanyPotPercentage();
                    $currentArr['sale_user_agent'] = $data->getSaleUserAgent();
                    $currentArr['company_pot_value'] = round(($data->getCompanyPotPercentage()/100)*$data->getNetAmount(),3).' '.$data->getBillingCurrency();
                    $currentArr['sale_pot_value'] = round(($data->getSalePotPercentage()/100)*$data->getNetAmount(),3).' '.$data->getCurrencyId();

                    $currentArr['is_active'] = ($data->getIsActive() == 'Y' ? 'Yes' : 'No');
                    if($data->getSalesPotIsPaid() == '1'){
                        $currentArr['pay_now'] = '<span class="pull-right label label-sm bg-green-jungle bg-font-green-jungle line-height-2"> Paid</span>';
                    }else{
                        $currentArr['pay_now'] = '<button type="button" data-invoice_no="' . $data->getInvoiceNo() . '" data-invoice_id="' . $data->getId() . '" data-invoice_type="PDF" data-invoice_amount="' . $data->getNetAmount() . '" data-user_account="' . $data->getUserAccountId() . '" class="btn btn-xs btn-primary pull-right btn_indvisual" name="btn_indvisual">Pay</button>';
                    }
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
        if(isset($_POST['action']) && $_POST['action'] == "generate_pdf"){
            $pdf = new PdfBase(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
            $this->pdf = $pdf;
            $this->pdf->SetPrintFooter(false);
            $this->pdf->SetFooterMargin(0);
            $this->pdf->SetAutoPageBreak(false, 0);

            $page_size = array (80, 100);

            $this->pdf->AddPage("L",$page_size);
            $this->pdf->setFont("helvetica", "B", 15);
            $this->pdf->Text(30, 15, "HUB - RUGBY");
            $this->pdf->setFont("helvetica", "", 12);
            $this->pdf->Text(35, 25, "Origin-China");
            $this->pdf->setFont("helvetica", "B", 13);
            $this->pdf->Text(30, 40, "Depot - " . $sackCode . " " .$sackDepotName);
            $style = array(
                'border' => false,
                'hpadding' => 'auto',
                'vpadding' => 1,
                'fgcolor' => array(0,0,0),
                'bgcolor' => false, //array(255,255,255),
                'text' => true,
                'font' => 'helvetica',
                'fontsize' => 12,
                'stretchtext' => 1
            );


            $this->pdf->write1DBarcode($licence_plate, 'I25', 25, 50, '', 20, 0.5, $style, 'Y');
            $this->pdf->setFont("helvetica", "", 10);
            $this->pdf->Text(70, 75, "Bag Ref:" . $depotCode);
            $fileName = "baglabel_" . date("YmdHis") . ".pdf";
            $this->pdf->Output("../_assets/export_manifest/".$fileName, "F");
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
        $this->invoiceFilter->join("sales_pot_comission spc", ['inv.id' => 'spc.invoice_id'],'LEFT');
        $this->invoiceFilter->where(['inv.invoice_by' => $this->user->getUserAccountId()]);
        $this->invoiceFilter->where(['ua.check_list_sales_pot' => 1]);
        $this->invoiceFilter->where(['u_sale.is_sale_pot_eligible' => 1]);
//        $this->invoiceFilter->where(['inv.is_active' => 1]);
        $this->invoiceFilter->where(['inv.is_deleted' => 0]);
        $this->invoiceFilter->where(['inv.is_cancel' => 0]);
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
                    var datatableurl = "bonus.php?action=bonus_ajax";
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
                                {"data": "user_account"},
                                {"data": "net_amount"},
                                {"data": "company_pot_percentage"},
                                {"data": "company_pot_value"},
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
                                    url: "bonus.php",
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
                            if (isConfirm) {
                                var table_data = $("#table_data").html();
                                table_data = btoa(table_data);
                                $("#action").val(table_data);
                                $("#adminForm").submit();
                            }
                        });
                });
                <?php
                    if(isset($this->activeTab) && !empty($this->activeTab)){
                ?>
                    $('a[href="#salespot-tab"]').click();
                <?php
                    }
                ?>
            });
            function loadData(type, pageno) {
                $("#" + type + "_data_tbl tbody").html('Loading');
                $.post('bonus.php', {func: 'getInvoices', pageno: pageno, type: type}, function (data) {
                    $("#" + type + "_data_tbl tbody").html(data);
                });
            }
            $("#londonbtn").click(function () {
                var ids = [];
                $('.class_chk_invoice_london:checked').map(function () {
                    ids.push(this.value);
                }).get();
                var operation = $('#operation_sel').val();
                if(operation != "generate_pdf") {
                    if (typeof ids !== 'undefined' && ids.length > 0) {
                        swal({
                                title: "Do you want to pay invoices",
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
                                        url: 'bonus.php',
                                        data: {
                                            invoice_id: ids, action: 'PAY_ALL_INVOICE', operation: operation
                                        },
                                        type: 'post',
                                        dataType: 'json',
                                        success: function (data) {
                                            grid.getDataTable().ajax.reload();
                                            if (data.status == 'success') {
                                                $('#res_div').show().html("");
                                                $('#res_div').addClass('alert-success').removeClass('alert-danger');
                                                $('#res_div').show().html(data.message);
                                            } else {
                                                $('#res_div').show().html("");
                                                $('#res_div').addClass('alert-danger').removeClass('alert-success');
                                                $('#res_div').show().html(data.message);
                                            }
                                            // $('#res_div').show().html(data.message).delay(5000).slideUp(500);
                                            $(window).scrollTop(0);
                                        },
                                        error: function () {
                                            //alert('error handing here');
                                        }
                                    });
                                }
                            });
                    } else {
                        swal("", "Please select invoices for this operation", "info");
                    }
                }else if(operation == "generate_pdf"){
                    swal({
                            title: "Do you want to generate pdf of invoices",
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
                                    url: 'bonus.php',
                                    data: {
                                        action: 'PAY_ALL_INVOICE', operation: operation
                                    },
                                    type: 'post',
                                    dataType: 'json',
                                    success: function (data) {
                                        grid.getDataTable().ajax.reload();
                                        if (data.status == 'success') {
                                            $('#res_div').show().html("");
                                            $('#res_div').addClass('alert-success').removeClass('alert-danger');
                                            $('#res_div').show().html(data.message);
                                        } else {
                                            $('#res_div').show().html("");
                                            $('#res_div').addClass('alert-danger').removeClass('alert-success');
                                            $('#res_div').show().html(data.message);
                                        }
                                        // $('#res_div').show().html(data.message).delay(5000).slideUp(500);
                                        $(window).scrollTop(0);
                                    },
                                    error: function () {
                                        //alert('error handing here');
                                    }
                                });
                            }
                        });
                }
            });
            $(document).on('click', '#search_user', function () {
                var userSalePot = $("#user_saleperson").val();
                if(userSalePot != "" && userSalePot !="Select Sale User"){
                    $("#user_sale_person").val(userSalePot);
                    $("#frm_user_sale_person").submit();
                }else{
                    swal("","Please select sales person", "info");
                }
            });
        </script>
        <script type="text/javascript">
            function get_commission(){
                $.ajax({
                    type: "POST",
                    url: "bonus.php",
                    data: {action: "get_commission"},
                    dataType: "json",
                    success: function (data) {
                        if (data.status == "success") {
                           $("#company_commission_span").html("");
                           $("#company_commission_span").html(data.company_commission);
                            $("#sale_commission_span").html("");
                            $("#sale_commission_span").html(data.sale_commission);
                        } else {
                        }
                    },
                    error: function () {
                        alert('error handing here');
                    }
                });
            }
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
                                "url": "bonus.php?action=data_table_ajax", // ajax source
                                headers: {

                                },
                            },
                            "bStateSave": true,
                            "columns": [
                                {"data": "option", "bSortable": false},
                                {"data": "date_created"},
                                {"data": "user_name", "bSortable": false},
                                {"data": "sale_pot_percentage"},
                                {"data": "sales_comission"}
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
            // Reload data table command
            //                grid.getDataTable().ajax.reload();
            $(document).on('click', '#salepot_li', function () {
                gridNew.getDataTable().ajax.reload();
            });
            $(document).on('click', '.commission_tab', function () {
                get_commission();
            });
            
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
                Sales Pot
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
                <li class="active" ><a data-toggle="tab" href="#london-tab">Outstanding</a></li>
                <li><a class="commission_tab" data-toggle="tab" href="#companypot-tab">Company Pot</a></li>
                <li id="salepot_li" ><a class="commission_tab" data-toggle="tab" href="#salespot-tab">Sales Pot</a></li>
            </ul>
            <div class="tab-content">

                <div id="london-tab" class="tab-pane fade in <?php echo (empty($this->activeTab) ? 'active' : ''); ?> ">
                    <button class="btn btn-sm btn-default table-group-action-submit pull-right" id="londonbtn" type="button">
                        <i class="fa fa-check"></i> Submit</button>
                    <select class="table-group-action-input form-control input-inline input-small input-sm pull-right" id="operation_sel" name="operation_sel" style="width:150px !important; margin-right: 5px;margin-bottom: 8px;">
                        <option value="bulk_pay">Bulk Pay</option>
                        <option value="generate_pdf">Download Pdf</option>
                    </select>
                    <div class="table-scrollable">
                        <table class="table table-striped table-bordered table-hover table-condensed" id="invoice_send_databale">
                            <thead>
                            <tr>
                                <th>
                                    <input type="checkbox" name="chkLondon" id="chkLondon" class="chkAllLondon group-checkable">
                                </th>
                                <th>Invoice No</th>
                                <th>Invoice Date</th>
                                <th>Account</th>
                                <th>Invoiced Amount</th>
                                <th>Company Pot % </th>
                                <th>Company Pot Amount </th>
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
                    </div> <!-- table-scrollable -->
                </div><!-- End london-tab -->

                <div id="companypot-tab" class="tab-pane fade">
                    <div class="row">
                        <div class="col-md-12 center">
                            <h3 class="center"><b>Company Commission : <span id="company_commission_span"><?php echo round($this->company_total_comission,3) . ' GBP'; ?></span></b></h3>
                        </div>
                    </div>
                </div>
                <div id="salespot-tab" class="tab-pane fade">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="portlet light">
                                <div class="portlet-title">
                                    <div class="caption"> <i class="fa fa-dropbox"></i>
                                        <b>Sales Team Commission : <span id="sale_commission_span"><?php echo round($this->SalePot,3) . ' GBP'; ?></span></b>
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
                                                <th>Date</th>
                                                <th>User</th>
                                                <th>Salepot Percentage</th>
                                                <th>Salepot Amount</th>
                                            </tr>
                                            <tr role="row" class="filter">
                                                <td>
                                                    <button class="btn btn-sm btn-default blue btn-outline pull-left margin-bottom filter-submit"><i class="fa fa-search"></i></button>
                                                    <button class="btn btn-sm btn-default red btn-outline pull-left filter-cancel margin-bottom"><i class="fa fa-times"></i></button>
                                                </td>
                                                <td>
                                                </td>
                                                <td>
                                                    <select id="user_saleperson" name="user_saleperson" class="form-filter form-control">
                                                        <option value="">Select Sale User</option>
                                                        <?php if(count($this->accountSalePersonList) > 0) {
                                                            foreach ($this->accountSalePersonList as $accountSalePersonList) { ?>
                                                                <option value="<?php echo $accountSalePersonList->getId(); ?>"><?php echo $accountSalePersonList->getFirstName().' '.$accountSalePersonList->getLastName().' ['.$accountSalePersonList->getUserName().' ]';  ?></option>
                                                            <?php } }?>
                                                    </select>
                                                </td>
                                                <td>
                                                </td>
                                                <td>
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
                    </div>
                </div>
            </div>
        </div> <!-- End portlet body -->
        <input type="hidden" name="action"  value="" />
        <form action="bonus.php" method="post" id="frm_user_sale_person">
            <input type="hidden" name="user_sale_person" value="" id="user_sale_person">
        </form>
        <?php
    }

}
/* ------------------------------------------------------------------------------ */
// create and render page
$PageObj = new Page(CONFIG_TEMPLATE_ADMIN);
$PageObj->show();
    