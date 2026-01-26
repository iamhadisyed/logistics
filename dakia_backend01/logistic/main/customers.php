<?php
// get settings
require_once("../includes/settings/config.inc.php");

include_classes([
    'country.class',
    'countryfilter.class',
    
]);

// set up local page class
class Page extends BasePage {

    private $user_filter;
    public $tariffs;
    public $user = null;

    /*     * *
     * Controller logic goes here
     */

    public function init() {
		/*include_classes([
			'currency.class',
			'paymentshistory.class',
			'paymentshistoryfilter.class',
			'consignmentcharges.class',
			'consignmentchargesfilter.class'
		]);
		$accountFilter = new UserAccountFilter();
		$accountFilter->addFilter("	    account_balance <= 0 OR account_balance IS NULL");
		$ofset = isset($_GET['os']) ? ($_GET['os'] * 100) : 0;
		$accountFilter->setOffset($ofset);
		$accountFilter->setRowsPerPage(100);

		$userAccounts = $accountFilter->getPagingColumnList('id, account_balance');
		foreach($userAccounts as $userAccount){
			//echo "<pre>"; print_r($userAccount); echo "</pre>";
			CustomerAccount::updateBalance($userAccount->getId());
			$account = new CustomerAccount($userAccount->getId());
			echo "<pre>"; print_r($account->getId().' <=> '. $account->getAccountBalance()); echo "</pre>";
		}
		echo "Done";
		exit;*/
        $this->user = SessionManager::getUser();
        if ($this->user->getUserType() == User::USER_TYPE_CLIENT) {
            util_redirect("403.php");
            exit;
        }
        if(isset($_GET['save']) && $_GET['save']=="success"){
           $this->flashMsg->success("Account saved successfully"); 
        }else if(isset($_GET['save']) && $_GET['save']=="updated"){
            $this->flashMsg->success("Account updated successfully");
        }
        // check admin user is authenticated
        $this->user_filter = new UserAccountFilter();

        $this->breadCrumb['data'] = array(
            'index.php' => Translation::GetCaption("HOME"),
            Translation::GetCaption("ACCOUNTS")
        );

        if (isset($_GET['action']) && trim($_GET['action']) == 'DOWNLOAD_ALL_CUSTOMER_TARIFF') {
            require_once(SETTING_DIR_REMOTE . "/Classes/PHPExcel.php");
            $objPHPExcel = new PHPExcel();
            $tariff_name = trim($_GET['tariff_name']);
            $service_id = trim($_GET['serviceid']);
            $carrier = trim($_GET['carrier']);
            $service_name = trim($_GET['service_name']);
            $user_account = trim($_GET['user_account']);


            //New Idea

            $TariffFilter = new TariffFilter();

            $TarObj = new TariffFilter();
            $TarObj->addFieldFilter('courier_service_id', $service_id);
            $TarObj->addFieldFilter('customer_id', $tariff_name);
            $this->tariffs = $TarObj->getTarifListDetail();
            $detailsFormulla = Tariff::formullaDetails();
            $tariff_date = date("d-m-Y");

            //New Idea

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
                    ->setLastModifiedBy("Customers System")
                    ->setTitle("Office 2007 XLSX Sales Invoice")
                    ->setSubject("Office 2007 XLSX Sales Invoice")
                    ->setDescription("This document is generated from the system, generated using PHP classes.")
                    ->setKeywords("office 2007 openxml php")
                    ->setCategory("Tariffs");
            // Add some data
            $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A1', 'Tariff Name')
                    ->setCellValue('B1', 'User Account')
                    ->setCellValue('C1', 'Tariff Date')
                    ->setCellValue('D1', 'Available Date')
                    ->setCellValue('A4', 'How Formulla Wok')
                    ->setCellValue('A11', 'Carrier')
                    ->setCellValue('B11', 'Service')
                    ->setCellValue('C11', 'From Country')
                    ->setCellValue('D11', 'To Country')
                    ->setCellValue('E11', 'From Weight')
                    ->setCellValue('F11', 'To Weight')
                    ->setCellValue('G11', 'Basic Charge')
                    ->setCellValue('H11', 'Unit Charges')
                    ->setCellValue('I11', 'Formulla')
                    ->setCellValue('J11', 'Ancellary Charges')
                    ->setCellValue('K11', 'Ancellary Chagres Unit');
            $objPHPExcel->getActiveSheet()->getStyle('A1:D1')->applyFromArray($FontBoldArray);
            $objPHPExcel->getActiveSheet()->getStyle('A1:D1')->applyFromArray($border);
            $objPHPExcel->getActiveSheet()->getStyle('A11:K11')->applyFromArray($FontBoldArray);
            $objPHPExcel->getActiveSheet()->getStyle('A11:K11')->applyFromArray($border);
            $objPHPExcel->getActiveSheet()->getStyle('A4:A4')->applyFromArray($FontBoldArray);
            $objPHPExcel->getActiveSheet()->getStyle('A4:A4')->applyFromArray($border);

            $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A2', $tariff_name)/* Found */
                    ->setCellValue('B2', $user_account)/* Found */
                    ->setCellValue('C2', $tariff_date)/* Found */
                    ->setCellValue('D2', "")/* Found */
                    ->setCellValue('A5', "Q")/* Found */
                    ->setCellValue('A6', "ITMCHR")/* Found */
                    ->setCellValue('A7', "REG")/* Found */
                    ->setCellValue('A8', "CHRG")/* Found */
                    ->setCellValue('A9', "FRMW")/* Found */
                    ->setCellValue('A10', "ceil")/* Found */
                    ->setCellValue('B5', $detailsFormulla['Q'])/* Found */
                    ->setCellValue('B6', $detailsFormulla['ITMCHR'])/* Found */
                    ->setCellValue('B7', $detailsFormulla['REG'])/* Found */
                    ->setCellValue('B8', $detailsFormulla['CHRG'])/* Found */
                    ->setCellValue('B9', $detailsFormulla['FRMW'])/* Found */
                    ->setCellValue('B10', $detailsFormulla['ceil'])/* Found */
                    ->setCellValue('J12', "")
                    ->setCellValue('K12', ""); /* Found */
            $count = 12;

            foreach ($this->tariffs as $tariffs) {
                $FromCountryData = $TariffFilter->getCountryNameByRateband($tariffs->getCollectionRatebandId());
                $FromCountryData = $FromCountryData[0];
                $FromCountry = $FromCountryData->getCollectionRatebandId();
                $ToCountryData = $TariffFilter->getCountryNameByRateband($tariffs->getDestinationRatebandId());
                $ToCountryData = $ToCountryData[0];
                $ToCountry = $ToCountryData->getCollectionRatebandId();


                $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('A' . $count, $carrier)
                        ->setCellValue('B' . $count, $service_name)/* Found */
                        ->setCellValue('C' . $count, $FromCountry)/* Found */
                        ->setCellValue('D' . $count, $ToCountry)/* Found */
                        ->setCellValue('E' . $count, $tariffs->getWeightFrom())/* Found */
                        ->setCellValue('F' . $count, $tariffs->getWeightTo())
                        ->setCellValue('G' . $count, $tariffs->getTariff())/* Found */
                        ->setCellValue('H' . $count, $tariffs->getAddUnitCost())
                        ->setCellValue('I12', $tariffs->getFormula());
                $count++;
            }



            $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);

            function saveExcelToLocalFile($objWriter) {


                // make sure you have permission to write to directory
                $file_name = "";
                $file_name = 'export-excel-' . time() . '.xlsx';
                $filePath = '../_assets/csv/' . $file_name;
                $fileUrl = SETTING_MAIN_URL_ACCOUNT . '_assets/csv/' . $file_name;

                $objWriter->save($filePath);
                header('Content-disposition: attachment; filename=' . $file_name);
                header('Content-type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                header('Content-Length: ' . filesize($fileUrl));
                header('Content-Transfer-Encoding: binary');
                header('Cache-Control: must-revalidate');
                header('Pragma: public');
                ob_clean();
                flush();
                readfile($fileUrl);
            }

            saveExcelToLocalFile($objWriter);
            exit;
        }
//            Download Excel
        if (isset($this->form_vars['func']) && $this->form_vars['func'] == 'download_excel') {
            unset($this->user_filter);
            $this->user_filter = new UserAccountFilter();
            if ($this->user->getUserType() == User::USER_TYPE_CORPORATE) {
                $this->user_filter->addFieldFilter('parentid', $this->user->getUserAccountId());
            }

            $searchAccount = $this->form_vars['excel_Account'];
            $searchParentAccount = $this->form_vars['excel_ParentAccount'];
            $searchCompany = $this->form_vars['excel_Company'];
            $searchFullName = $this->form_vars['excel_FullName'];
            $searchEmail = $this->form_vars['excel_Email'];
            $searchCountry = $this->form_vars['excel_Country'];
            $searchActive = $this->form_vars['excel_Active'];

            if (!empty($searchAccount))
                $this->user_filter->addFieldLikeFilter('ua.user_account', $searchAccount);
            if (!empty($searchParentAccount))
                $this->user_filter->addFieldFilter('ua.parentid', $searchParentAccount);
            if (!empty($searchCompany))
                $this->user_filter->addFieldLikeFilter('ua.company', $searchCompany);
            if (!empty($searchFullName))
                $this->user_filter->addFieldLikeFilter('ua.full_name', $searchFullName);
            if (trim($searchEmail) != '')
                $this->user_filter->addFieldFilter('ua.email', $searchEmail);
            if (!empty($searchCountry))
                $this->user_filter->addFieldFilter('ua.country', $searchCountry);
            if (trim($searchActive) != '') {
                $this->user_filter->addFieldFilter('ua.active_flag', $searchActive);
            }
            $searchDateCreated = $this->form_vars['search_date_created'];
            if (!empty($searchDateCreated)) {
                $this->user_filter->addFieldFilter(" DATE_FORMAT(ua.date_created, '%Y-%m-%d') ", $searchDateCreated);
            }

            if (PHP_SAPI == 'cli')
                die('This should only be run from a Web Browser');

            /** Include PHPExcel */
            require_once '../includes/library/PHPExcel-1.8/PHPExcel.php';
            // Create new PHPExcel object
            $objPHPExcel = new PHPExcel();
            // Set document properties
            $objPHPExcel->getProperties()->setCreator("[One World Express]")
                    ->setTitle("User Accounts")
                    ->setSubject("Smart Track System User Accounts")
                    ->setDescription("Smart Track System User Accounts")
                    ->setKeywords("User Account List,User Accounts")
                    ->setCategory("User Accounts");

            $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A1:Z1');

            $activeSheet = $objPHPExcel->setActiveSheetIndex(0);
            $activeSheet->mergeCells('A1:Z1');
            $activeSheet->setCellValue('A1', "Smart Track User Accounts");
            $objPHPExcel->getActiveSheet()->getStyle('A1')->applyFromArray(array('font' => array('size' => 16, 'bold' => true), 'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER, 'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER)));
            $objPHPExcel->getActiveSheet()->getRowDimension(1)->setRowHeight(30);
            $activeSheet->setCellValue('A2', "Company Logo");
            $activeSheet->setCellValue('B2', "Account");
            $activeSheet->setCellValue('C2', "Parent Account");
            $activeSheet->setCellValue('D2', "Company");
            $activeSheet->setCellValue('E2', "Contact Name");
            $activeSheet->setCellValue('F2', "Email");
            $activeSheet->setCellValue('G2', "Alternative Email");
            $activeSheet->setCellValue('H2', "Country");
            $activeSheet->setCellValue('I2', "Phone");
            $activeSheet->setCellValue('J2', "Billing Email");
            $activeSheet->setCellValue('K2', "Return Address");
            $activeSheet->setCellValue('L2', "Billing Currency");
            $activeSheet->setCellValue('M2', "Is Vat Chargable");
            $activeSheet->setCellValue('N2', "Rate of VAT Chargeable");
            $activeSheet->setCellValue('O2', "Trade Reference Name 1");
            $activeSheet->setCellValue('P2', "Trade Reference Address 1");
            $activeSheet->setCellValue('Q2', "Trade Reference Email 1");
            $activeSheet->setCellValue('R2', "Trade Reference Phone Number 1");
            $activeSheet->setCellValue('S2', "Trade Reference Name 2");
            $activeSheet->setCellValue('T2', "Trade Reference Address 2");
            $activeSheet->setCellValue('U2', "Trade Reference Email 2");
            $activeSheet->setCellValue('V2', "Trade Reference Phone Number 2");
            $activeSheet->setCellValue('W2', "Company Registration Number");
            $activeSheet->setCellValue('X2', "Company Registered Address");
            $activeSheet->setCellValue('Y2', "Company Registered Postcode");
            $activeSheet->setCellValue('Z2', "Company Registered Country");
            $activeSheet->setCellValue('AA2', "Created");
            $objPHPExcel->getActiveSheet()->getStyle('A2:AA2')->applyFromArray(array('font' => array('bold' => true), 'alignment' => array('vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER)));
            $objPHPExcel->getActiveSheet()->getRowDimension(2)->setRowHeight(20);
            $customerObjs = $this->user_filter->getColumnList("ua.*");
            if (count($customerObjs) > 0) {
                $rowNum = 3;
                foreach ($customerObjs as $customerObj) {
                    if ($customerObj->getLogo() != '' && file_exists("../images/userlogo/thumbnail/owe_16_" . $customerObj->getLogo())) {
                        $gdLogoImage = imagecreatefrompng('../images/userlogo/thumbnail/owe_16_' . $customerObj->getLogo());
                        $objLogoDrawing = new PHPExcel_Worksheet_MemoryDrawing();
                        $objLogoDrawing->setName('Company Logo');
                        $objLogoDrawing->setDescription('Company Logo');
                        $objLogoDrawing->setImageResource($gdLogoImage);
                        $objLogoDrawing->setRenderingFunction(PHPExcel_Worksheet_MemoryDrawing::RENDERING_PNG);
                        $objLogoDrawing->setMimeType(PHPExcel_Worksheet_MemoryDrawing::MIMETYPE_DEFAULT);
                        $objLogoDrawing->setHeight(30);
                        $objLogoDrawing->setCoordinates('A' . $rowNum);
                        $objLogoDrawing->setWorksheet($objPHPExcel->getActiveSheet());
                    }
                    $countryName = new Country($customerObj->getCountryId());
                    $userClassParentr = new CustomerAccount($customerObj->getParentId());
                    $isVatChargable = $customerObj->getVatChargable() == 0 ? 'No' : 'Yes';
                    $regCountry = new Country($customerObj->getRegCountry());
                    $tradeRegCountryName = $customerObj->getRegCountry();
                    $activeSheet->setCellValue('B' . $rowNum, $customerObj->getUserAccount());
                    $activeSheet->setCellValue('C' . $rowNum, $userClassParentr->getUserAccount());
                    $activeSheet->setCellValue('D' . $rowNum, $customerObj->getCompany());
                    $activeSheet->setCellValue('E' . $rowNum, $customerObj->getFullName());
                    $activeSheet->setCellValue('F' . $rowNum, $customerObj->getEmail());
                    $activeSheet->setCellValue('G' . $rowNum, $customerObj->getAlternativeEmail());
                    $activeSheet->setCellValue('H' . $rowNum, $countryName->getName());
                    $activeSheet->setCellValue('I' . $rowNum, $customerObj->getTelephone());
                    $activeSheet->setCellValue('J' . $rowNum, $customerObj->getBillingEmail());
                    $activeSheet->setCellValue('K' . $rowNum, $customerObj->getReturnAddress());
                    $activeSheet->setCellValue('L' . $rowNum, $customerObj->getBillingCurrency());
                    $activeSheet->setCellValue('M' . $rowNum, $isVatChargable);
                    $activeSheet->setCellValue('N' . $rowNum, $customerObj->getVatValue());
                    $activeSheet->setCellValue('O' . $rowNum, $customerObj->getTradeNameI());
                    $activeSheet->setCellValue('P' . $rowNum, $customerObj->getTradeAddressI());
                    $activeSheet->setCellValue('Q' . $rowNum, $customerObj->getTradeEmailI());
                    $activeSheet->setCellValue('R' . $rowNum, $customerObj->getTradePhoneI());
                    $activeSheet->setCellValue('S' . $rowNum, $customerObj->getTradeNameIi());
                    $activeSheet->setCellValue('T' . $rowNum, $customerObj->getTradeAddressIi());
                    $activeSheet->setCellValue('U' . $rowNum, $customerObj->getTradeEmailIi());
                    $activeSheet->setCellValue('V' . $rowNum, $customerObj->getTradePhoneIi());
                    $activeSheet->setCellValue('W' . $rowNum, $customerObj->getRegNumber());
                    $activeSheet->setCellValue('X' . $rowNum, $customerObj->getRegAddress());
                    $activeSheet->setCellValue('Y' . $rowNum, $customerObj->getRegPostcode());
                    $activeSheet->setCellValue('Z' . $rowNum, $regCountry->getName());
                    $activeSheet->setCellValue('AA' . $rowNum, formatDate(date("d-m-Y",$customerObj->getDateCreated())));
//                    $activeSheet->setCellValue('G' . $rowNum, ($customerObj->getActive() ? 'Yes' : 'No'));

                    $activeSheet->getRowDimension($rowNum)->setRowHeight(50);
                    $objPHPExcel->getActiveSheet()->getStyle('A' . $rowNum . ':Z' . $rowNum)->applyFromArray(array('alignment' => array('vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER)));
                    $rowNum++;
                }
            }
            unset($this->user_filter);
            // Rename worksheet
            $objPHPExcel->getActiveSheet()->setTitle('Smart Track User Accounts');


            // Set active sheet index to the first sheet, so Excel opens this as the first sheet
            $objPHPExcel->setActiveSheetIndex(0);


            $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
            foreach (range('B', 'Z') as $columnID) {
                $objPHPExcel->getActiveSheet()->getColumnDimension($columnID)->setAutoSize(true);
            }

            $fileName = "user_accounts_" . time() . ".xlsx";

            // Redirect output to a client’s web browser (Excel2007)
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="' . $fileName . '"');
            header('Cache-Control: max-age=0');
            // If you're serving to IE 9, then the following may be needed
            header('Cache-Control: max-age=1');

            // If you're serving to IE over SSL, then the following may be needed
            header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
            header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
            header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
            header('Pragma: public'); // HTTP/1.0

            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
            $objWriter->save('php://output');
            exit;
        }
        //Handle Customer Ajax
        if (isset($_GET['getCustomerAjax']) && $_GET['getCustomerAjax'] == 'customers_ajax') {
            /*
             * Set columns orders for sorting
             */
            unset($this->user_filter);
            $this->user_filter = new UserAccountFilter();
            if ($this->user->getUserType() == User::USER_TYPE_CORPORATE) {
                $this->user_filter->addFieldFilter('parentid', $this->user->getUserAccountId());
            }
            if (isset($this->form_vars['order'][0]['column']) && $this->form_vars['order'][0]['column'] != 0) {
                $dataTableColumnId = $this->form_vars['order'][0]['column'];
                $orderBy = $this->form_vars['order'][0]['dir'];
                $orderFalse = TRUE;
                if ($orderBy == 'desc') {
                    $orderFalse = FALSE;
                }
                $dataTableColumnName = $this->form_vars['columns'][$dataTableColumnId]['data'];
                if ($dataTableColumnName == "user_parent_account")
                    $dataTableColumnName = "parentid";
                $this->user_filter->AddOrderBy($dataTableColumnName, $orderFalse);
            }
            /*
             * Column filter
             * For search
             */
            if (isset($this->form_vars['action']) && $this->form_vars['action'] == 'filter') {

                $searchAccount = $this->form_vars['search_Account'];
                if (!empty($searchAccount))
                    $this->user_filter->addFieldLikeFilter('user_account', $searchAccount);

                $searchCompany = $this->form_vars['search_Company'];
                if (!empty($searchCompany))
                    $this->user_filter->addFieldLikeFilter('company', $searchCompany);

                $searchFullName = $this->form_vars['search_FullName'];
                if (!empty($searchFullName))
                    $this->user_filter->addFieldLikeFilter('full_name', $searchFullName);

                $searchParentAccount = $this->form_vars['search_ParentAccount'];
                if (!empty($searchParentAccount)) {
                    $this->user_filter->addFieldFilter('ua.parentid', $searchParentAccount);
                }

                $searchDateCreated = $this->form_vars['search_date_created'];
                if (!empty($searchDateCreated)) {
                    $this->user_filter->addFieldFilter(" DATE_FORMAT(ua.date_created, '%Y-%m-%d') ", date("Y-m-d",strtotime($searchDateCreated)));
                }

                $searchEmail = $this->form_vars['search_Email'];
                if (!empty($searchEmail))
                    $this->user_filter->addFieldLikeFilter('email', $searchEmail);

                $searchActive = $this->form_vars['search_Active'];
                if (trim($searchActive) != '') {
                    $this->user_filter->addFieldFilter('active_flag', $searchActive);
                }
                $searchCountry = $this->form_vars['search_Country'];
                if (!empty($searchCountry))
                    $this->user_filter->addFieldFilter('country_id', $searchCountry);
            }
            /*
             * Pagination Logic Implemented
             * 
             */
            $iTotalRecords = $this->user_filter->getPagingCount();
            $iDisplayLength = intval($_REQUEST['length']);
            $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
            $iDisplayStart = intval($_REQUEST['start']);
            $sEcho = intval($_REQUEST['draw']);
            $end = $iDisplayStart + $iDisplayLength;
            $end = $end > $iTotalRecords ? $iTotalRecords : $end;
            $this->user_filter->setRowsPerPage($iDisplayLength);
            // the offset of the list, based on current page
            $this->user_filter->setOffset($iDisplayStart);
            $customerObjs = $this->user_filter->getPagingList();
            $customerDataArr = array();
            foreach ($customerObjs as $customerObj) {
                $customerArr = array();
                $customerLogo = "";
                $customerProfileImage = "";
                // has this customer got an account
                $accountName = "";
                $accountId = "";
                if ($customerObj->getId() > 0) {
                    $account = $customerObj->getUserAccount();
                    $accountName = $customerObj->getCompany();
                    $accountId = $customerObj->getId();
                }
                $customerArr['actions'] = '';
//                    if (in_array($this->user->getUserType(), array(USER::USER_TYPE_ADMIN, USER::USER_TYPE_ACCOUNT, USER::USER_TYPE_FINANCE))) {
                $customerArr['actions'] = '<div class="dropdown">
                                                <a href="#" role="button" id="dropdownMenuLink1" data-bs-toggle="dropdown" aria-expanded="false">
                                                    <i class="ri-more-2-fill"></i>
                                                </a>
                                             <ul class="dropdown-menu" aria-labelledby="dropdownMenuLink1">';
                if (Permissions::checkFilePermission('accounts_edit_permission')) {
                    $customerArr['actions'] .= '<li>
                                                    <a class="dropdown-item" href="customers_details.php?id=' . $customerObj->getId() . '" >
                                                         Edit
                                                    </a>
                                                </li>';
                }
//                if (Permissions::checkFilePermission('accounts_edit_permission')) {
//                    $customerArr['actions'] .= '<li>
//                                                    <a title="Edit" href="additional_contacts.php?id=' .base64_encode( $customerObj->getId()) . '" >
//                                                        <i class="glyphicon glyphicon-plus"></i> Add Additional Contacts
//                                                    </a>
//                                                </li>';
//                }
                if (Permissions::checkFilePermission('accounts_view_permission')) {
                    $customerArr['actions'] .= '<li>
                                                    <a href="user_view.php?id=' . base64_encode($customerObj->getId()) . '" class="dropdown-item">
                                                         View
                                                    </a>
                                                </li>';
                }
                if (Permissions::checkFilePermission('accounts_user_permission')) {
                    $customerArr['actions'] .= '<li>
                                                    <a href="list_users.php?account=' . base64_encode($customerObj->getId()) . '" class="dropdown-item">
                                                         Users
                                                    </a>
                                                </li>';
                }
//                if (Permissions::checkFilePermission('accounts_account_tree_permission')) {
//                    $customerArr['actions'] .= '<li>
//                                                    <a href="account_hierarchical_view.php?id=' . base64_encode($customerObj->getId()) . '" title="Hierarchical View">
//                                                        <i class="fa fa-sitemap"></i> Account Tree
//                                                    </a>
//                                                </li>';
//                    }
//                if (Permissions::checkFilePermission('api_key_expiry_date_update')) {
//                    $customerArr['actions'] .= '<li>
//                                                    <a href="api_keys.php?account_id=' . base64_encode($customerObj->getId()) . '" title="Hierarchical View">
//                                                        <i class="fa fa-sitemap"></i> API Keys
//                                                    </a>
//                                                </li>';
//                }

//                                                <li>
//                                                    <a href="client_list.php?saccount='.base64_encode($customerObj->getId()).'" title="Manage Shipments">
//                                                        <i class="fa fa-shopping-cart"></i> Manage Shipments
//                                                    </a>
//                                                </li>
//                if (Permissions::checkFilePermission('accounts_manage_services_permission')) {
//                    $customerArr['actions'] .= '<li>
//                                                    <a href="add_services.php?id=' . base64_encode($customerObj->getId()) . '" title="Manage Serviecs">
//                                                        <i class="fa fa-plug"></i> Manage Services
//                                                    </a>
//                                                </li>';
//                }
               // if ($customerObj->getIsPrepaid()) {
//                    if (Permissions::checkFilePermission('accounts_recharge_account_permission')) {
//                $customerArr['actions'] .= '<li>
//                                 <a href="" id="user-audit-detail-view" data-target="#user-audit-view-modal" data-log_key="' . $customerObj->getId() . '"
//                                 data-log_name="user_account" data-toggle="modal"> <i class="fa fa-list"></i> View Audit
//                                 </a>
//                                 </li>';
  //                  }
    //            }
//                if (Permissions::checkFilePermission('accounts_payment_history')) {
//                    $customerArr['actions'] .= '<li>
//                                                    <a href="payment_history.php?id=' . base64_encode($customerObj->getId() ). '" title="Payment History">
//                                                        <i class="fa fa-money"></i> Payment History
//                                                    </a>
//                                                </li>';
//                }
//                $customerArr['actions'] .= '<li>
//                                                <a href="invoice_templates.php?id=' . $customerObj->getId() . '" title="Invoice Templates">
//                                                    <i class="fa fa-plug"></i> Invoice Template
//                                                </a>
//                                            </li>';
                $customerArr['actions'] .= '</ul>
                                        </div>';
                $customerArr['actions'] .= ''
                //. '<a href="list_extra_charges.php?id=' . $customerObj->getId() . '" class="btn btn-xs blue btn-outline" title="Show Extra Charges" ><span class="glyphicon glyphicon-screenshot"></span></a>'
                //. '<a href="#" class="btn btn-xs blue btn-outline" title="Show Tariff" onclick="return show_user_base_tariff(' . $customerObj->getUserAccount() . ')"><span class="glyphicon glyphicon-stats"></span</a>
                ;
//                    }
                if ($customerObj->getLogo() != '' && file_exists("../images/userlogo/thumbnail/owe_16_" . $customerObj->getLogo())) {
                    $customerLogo = '<div class="img-wrapper" style="margin-right: 10px;float: left;">
                                                <img  class="customer_img" src="../images/userlogo/thumbnail/owe_16_' . $customerObj->getLogo() . '" alt="' . $customerObj->getUserAccount() . '" uib-popover="' . $customerObj->getUserAccount() . '" popover-trigger="mouseenter" >
                                            </div>';
                } else {
                    $customerLogo = '<div class="img-wrapper" style="margin-right: 10px;float: left;">
                                                <img  class="customer_img" src="../images/No-image-found.jpg" alt="' . $customerObj->getUserAccount() . '" uib-popover="' . $customerObj->getUserAccount() . '" popover-trigger="mouseenter" >
                                            </div>';
                }
                $customerArr['user_account'] = $customerLogo . '<div style="float: left;margin-right: 5px;">' . $customerObj->getUserAccount() . '</div>';
                if ($this->user->getUserType() == User::USER_TYPE_ADMIN) {
                    $userClassParentr = new CustomerAccount($customerObj->getParentId());
                    if ($userClassParentr->getLogo() != '' && file_exists("../images/userlogo/thumbnail/owe_16_" . $userClassParentr->getLogo())) {
                        $customerParentLogo = '<div class="img-wrapper" style="margin-right: 10px;float: left;">
                                                <img  class="customer_img" src="../images/userlogo/thumbnail/owe_16_' . $userClassParentr->getLogo() . '" alt="' . $userClassParentr->getUserAccount() . '" uib-popover="' . $userClassParentr->getUserAccount() . '" popover-trigger="mouseenter" >
                                            </div>';
                    } else {
                        $customerParentLogo = '<div class="img-wrapper" style="margin-right: 10px;float: left;">
                                                <img  class="customer_img" src="../images/No-image-found.jpg" alt="' . $userClassParentr->getUserAccount() . '" uib-popover="' . $customerObj->getUserAccount() . '" popover-trigger="mouseenter" >
                                            </div>';
                    }

                    $customerArr['user_parent_account'] = $customerParentLogo . '<div style="float: left;margin-right: 5px;">' . $userClassParentr->getUserAccount() . '</div>';
                }
                $customerArr['company'] = $customerObj->getCompany();
                $customerArr['full_name'] = $customerObj->getFullName();
                $customerArr['email'] = $customerObj->getEmail();
                $country = $customerObj->getCountryId();
                $countryName = "";
                if ($country != '') {
                    $countryList = new Country($country);
                    if (count($countryList) > 0) {
                        $countryName = '<img src=\'../assets/global/img/flags/' . strtolower($countryList->getIso()) . '.png\' /> ' . $countryList->getIso();
                    }
                    $customerArr['country'] = $countryName;
                } else {
                    $customerArr['country'] = $country;
                }
                $customerArr['users']  = '<div class="text-center">' . ($customerObj->getActiveUsers()>0 ? '<a href="list_users.php?account=' . base64_encode($customerObj->getId()) . '" title="Users">
                                                        <span class="label label-sm label-success">'.$customerObj->getActiveUsers().' Active Users</span>
                                                    </a>' : '<span class="label label-sm label-danger">0 Active Users</span>') . '</div>';
                $customerArr['active_flag'] = '<div class="text-center">' . ($customerObj->getActive() ? '<span class="label label-sm label-success">Yes</span>' : '<span class="label label-sm label-danger">No</span>') . '</div>';
                $customerArr['date_created'] = date("d-m-Y", $customerObj->getDateCreated() );
                $customerDataArr [] = $customerArr;
            }
            $customerDataArrJson['data'] = $customerDataArr;
            $customerDataArrJson['draw'] = $sEcho;
            $customerDataArrJson['recordsTotal'] = $iTotalRecords;
            $customerDataArrJson['recordsFiltered'] = $iTotalRecords;
            echo json_encode($customerDataArrJson);
            die;
        }
    }

    /*     * *
     * This page's content
     * @return void
     */

    public function renderBody() {
        foreach ($this->form_vars as $key => $val) {
            $$key = $val;
        }
        ?>
        <div class="row">
            <input type="hidden" name="form_action" id="form_action" value="<?php echo @$form_action; ?>"  />
            <form method="post" action="customers.php" id="frm_export_excel">
                <input type="hidden" name="func" value="download_excel" />
                <input type="hidden" id="excel_Account" name="excel_Account" value="" />
                <input type="hidden" id="excel_ParentAccount" name="excel_ParentAccount" value="" />
                <input type="hidden" id="excel_Company" name="excel_Company" value="" />
                <input type="hidden" id="excel_FullName" name="excel_FullName" value="" />
                <input type="hidden" id="excel_Email" name="excel_Email" value="" />
                <input type="hidden" id="excel_Country" name="excel_Country" value="" />
                <input type="hidden" id="search_Active" name="search_Active" value="" />
            </form>
            <div class="col-xl-12">
                <div class="card">
                    <div class="card-header align-items-center d-flex">
                        <h4 class="card-title mb-0 flex-grow-1">Customer Accounts</h4>

                        <div class="flex-shrink-0">
                            <?php
                    if(Permissions::checkFilePermission('customers_details.php'))
                    {
                        ?>
                            <a href="customers_details.php" class="btn btn-primary">Create Customer</a>
                            <?php } ?>
                            <!--                            <div class="form-check form-switch form-switch-right form-switch-md">-->
                            <!--                                <label for="card-tables-showcode" class="form-label text-muted">Show Code</label>-->
                            <!--                                <input class="form-check-input code-switcher" type="checkbox" id="card-tables-showcode">-->
                            <!--                            </div>-->
                        </div>
                    </div><!-- end card header -->

                    <div class="card-body">
                        <p class="text-muted mb-4"><code><?php $this->flashMsg->display(); ?></code> </p>

                        <div class="live-preview">
                            <div class="table-responsive table-card">
                                <table class="table align-middle table-nowrap table-striped-columns mb-0" id="manage-data-table">
                                    <thead class="table-light">
                                    <tr>
                                        <th scope="col" style="width: 46px;"> Action </th>
                                        <th scope="col">Created</th>
                                        <th scope="col">Customer</th>
                                        <?php if ($this->user->getUserType() == User::USER_TYPE_ADMIN) { ?>
<!--                                            <th scope="col">Parent Account</th>-->
                                        <?php } ?>
                                        <th scope="col">Company</th>
                                        <th scope="col">Contact Name</th>
                                        <th scope="col">Email</th>
                                        <th scope="col">Country</th>
                                        <th scope="col">Users</th>
                                        <th scope="col">Active</th>
                                    </tr>
                                    </thead>
                                    <tbody>

                                    </tbody>
                                </table>
                            </div>
                        </div>

                    </div><!-- end card-body -->
                </div><!-- end card -->
            </div><!-- end col -->
        </div><!-- end row -->

        <form id="download_tariff_frm" name="download_tariff_frm" method="post">
            <div class="modal fade bs-modal-lg" tabindex="-1" role="dialog" id="download_tariff" >
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title">Downloal Tariff With Respect to Services</h4>
                        </div>
                        <div class="modal-body">
                            <div class="alert alert-success" id="download_tariff_msg"></div>
                            <input type="hidden" name="action" id="action" value="DOWNLOAD_TARIFF" />                            
                            <div class="row">

                                <div class="col-md-12">
                                    <fieldset class="fsStyle">
                                        <legend class="legendStyle">Services Listed</legend>
                                        <div class="row">
                                            <div class="col-md-12 download-tariff-service-area">
                                                <div class="form-group col-md-4">
                                                    Service 1
                                                </div>
                                                <div class="form-group col-md-4">
                                                    Service 1
                                                </div>
                                                <div class="form-group col-md-4">
                                                    Service 1
                                                </div>
                                                <div class="form-group col-md-4">
                                                    Service 1
                                                </div>
                                            </div>
                                        </div>

                                    </fieldset>
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
        </form>


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
       
        <?php
    }

    public function renderFooter() {
        ?>
        <script type="text/javascript">
            function SetPageSize()
            {
                document.getElementById('adminForm').submit();
            }
            function show_user_base_tariff(dataId)
            {
            $.post(
                    "ajaxTariffs.php",
            {action:'SHOW_ALL_CUSTOMER_TARIFF', customer_account:dataId},
                    function(data)
                    {
                    $('#display-tariff-' + dataId).html(data);
                    $('.download-tariff-popup').click(function(e){
                    $("#download_tariff_msg").hide();
                    $("#download_tariff").modal("show");
                    $(".download-tariff-service-area").html("Please wait, we are dealing your request.");
                    var elm = $(this);
                    var tariff_name = elm.data('tariff-name');
                    var action = elm.data('action');
                    $.post(
                            "ajaxTariffs.php",
                    {action:action, tariff_name:tariff_name},
                            function(data){
                            $(".download-tariff-service-area").html(data);
                            });
                    });
                    });
        //			$('#'+dataId).html(servicename + 'this is test ');
            $('#display-tariff-' + dataId).toggle();
            return false;
            }
            function createUrl(dataTarifName)
            {
            $.post(
                    "ajaxTariffs.php",
            {action:'SHOW_URL_CUSTOMER_TARIFF', customer_tariff:dataTarifName},
                    function(data)
                    {
                    window.location = data;
                    });
            return false;
            }

            $(function() {
                $('.selectpicker').on('change', function(){
            });
            });</script>

        <script type="text/javascript">
            var DataTableFun = function () {
            var handleDataTable = function () {
            var grid = new Datatable();
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
                            "url": "customers.php?getCustomerAjax=customers_ajax", // ajax source
                                    headers: {

                                    },
                            },
                            "bStateSave": true,
                            "columns": [
                            {"data": "actions", "bSortable": false},
                            {"data": "date_created"},
                            {"data": "user_account"},
        <?php if ($this->user->getUserType() == User::USER_TYPE_ADMIN) { ?>
                                // {"data": "user_parent_account"},
        <?php } ?>
                            {"data": "company"},
                            {"data": "full_name"},
                            {"data": "email"},
                            {"data": "country"},
                            {"data": "users"},
                            {"data": "active_flag"}

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
                if ($('.date-picker').length > 0) {
                    //init date pickers
                    var today = new Date();
                    $('.date-picker').datepicker({
                        format: 'dd-mm-yyyy',
                        autoclose:true,
                        endDate: "today",
                        maxDate: today
                    }).on('changeDate', function (ev) {
                        $(this).datepicker('hide');
                    });


                    $('.date-picker').keyup(function () {
                        if (this.value.match(/[^0-9]/g)) {
                            this.value = this.value.replace(/[^0-9^-]/g, '');
                        }
                    });
                }
            DataTableFun.init();
            // Handle download excel
            $("#export_excel").click(function(){
            $("#excel_Account").val($("#search_Account").val());
            $("#excel_ParentAccount").val($("#search_ParentAccount").val());
            $("#excel_Company").val($("#search_Company").val());
            $("#excel_FullName").val($("#search_FullName").val());
            $("#excel_Email").val($("#search_Email").val());
            $("#excel_Country").val($("#search_Country").val());
            $("#excel_Active").val($("#search_Active").val());
            $("#frm_export_excel").submit();
            });
        <?php if (!empty($_GET['parent_id']) && trim($_GET['parent_id']) != "") { ?>
                $(".filter-submit").click();
        <?php } ?>
            });</script>
        <?php
    }

    protected function addPagelavelCss() {
        ?>
        <link href="../assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/select2/css/select2-bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="../assets/global/plugins/bootstrap-select/css/bootstrap-select.css" rel="stylesheet" type="text/css" />
        <link rel="stylesheet" href="../assets/global/css/bootstrap-select.min.css" />
        <link href="<?=SETTING_MAIN_ASSETS;?>css/common.css" rel="stylesheet" type="text/css" />
        <style type="text/css">
            table td div {
                font-weight: 300;
            }
            table td  {
                font-weight: 300;
            }
        </style>
        <?php
    }

    public function addPagelavelJs() {
        ?>
        <script src="../assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js" type="text/javascript"></script>


        <script type="text/javascript" src="../assets/global/plugins/select2/js/select2.full.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.js" type="text/javascript"></script>
        <script src="../assets/global/scripts/app.min.js" type="text/javascript"></script>
        <script src="../js/bootstrap-select.min.js"></script>
        <script src="../assets/global/scripts/datatable.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/datatables/datatables.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js" type="text/javascript"></script>

        <?php
    }

}

/* ------------------------------------------------------------------------------ */
// create and render page
$PageObj = new Page(CONFIG_TEMPLATE_ADMIN);
$PageObj->show();
?>
