<?php
// get settings
require_once("../includes/settings/config.inc.php");
require_once("../Classes/PHPExcel.php");

class Page extends BasePage {
    /*     * *
     * Controller logic
     */

    private $filerColumn = '*';
    private $params = "";
    private $averageWeightbarChart = array();
    private $deliveryRatioPieChart = array();
    private $deliveryOverViewPieChart = array();
    private $goodReturnPieChart = array();
    private $exHubPieChart = array();
    private $lineChartTransitTime = array();
    private $headerStyle;
    private $styleForReport;
    private $carrierId = '';
    private $serviceId = '';
    private $countryId = '';
    private $userAccountId = '';
    private $getUserAccountId = '';
    private $showReport = '';
    private $service_chart = array();
    private $fieldPermission = array();
    private $showTemplate;

    protected function init() {
        $this->breadCrumb['data'] = array(
            'index.php' => Translation::GetCaption("HOME"),
            "Tracking Status Report"
        );
        
        $this->user = SessionManager::getUser();
        $reportCustomizeSettingsFilter = new ReportCustomizeSettingsFilter();
        if(!empty($this->form_vars['report_template'])){
            $this->showTemplate = $this->form_vars['report_template'];
        }else{
            $this->fieldPermission = $this->defaultPermissionOfReport();
        }

        $this->showReport = (!empty($_POST['show_shipments'])?$_POST['show_shipments']:'all');
        $allouedAcccounts = [];
        if ($this->user->getUserType() == USER::USER_TYPE_CORPORATE) {
            $this->userCorporate = $this->user->getId();
        }
        
        $user_account_id = (!empty($this->form_vars['user_account_id'])? $this->form_vars['user_account_id']: $this->user->getUserAccountId());
        $selectAccountSearchType = $this->form_vars['show_shipments'];
        if(empty($selectAccountSearchType)) {
            $selectAccountSearchType = "all";
        }
        /*
         * DataTable handlings
         */

        if (isset($_GET['action']) && $_GET['action'] == "get_carrier_services") {
            $user_account_id = $this->form_vars['user_account_id'];
            $selected_carrier = $this->form_vars['selected_carrier'];
            $selected_service = $this->form_vars['selected_service'];
            $consignmentList = array();
            if (!empty($user_account_id)) {
                $return = array();
                $userAccountArry = CustomerAccount::accountSubAccount($user_account_id, 0, true);
                $userServicesRoutingFilter = new UserServicesRoutingFilter();
                $userServicesRoutingFilter->addFilterIn("    user_account_id",$userAccountArry);
                $userServiceObj = $userServicesRoutingFilter->getColumnList(" service_id",false,true);
                $serviceIds = array();
                if(count($userServiceObj) > 0) {
                    foreach($userServiceObj as $service) {
                        $serviceIds[] = $service->getServiceId();
                    }
                }
            }            
            $serviceFilterObj = new ServiceFilter();
            if (!empty($serviceIds)) {
                $serviceFilterObj->addFilterIn("ser.id", $serviceIds);
            }
            $services = $serviceFilterObj->getColumnList("ser.id, ser.name, ser.code, ser.carrier_id");
            $carrierIds = array();
            foreach ($services as $ser) {
                if (!in_array($ser->getCarrierId(), $carrierIds)) {
                    $carrierIds[] = $ser->getCarrierId();
                }
            }
            $carrierFilterObj = new CarrierFilter();
            $carrierFilterObj->addFilterIn("c1.id", $carrierIds);
            $carriers = $carrierFilterObj->getColumnListIn("c1.id,c1.carrier,c1.logo,c1.carrier_display_name");
            $service_option = "<option value=''>Select Service</option>";
            foreach ($services as $sr) {
                $selected = "";
                if($sr->getId() == $selected_service) {
                    $selected = "selected='selected'";
                }
                $service_option .= "<option value='" . $sr->getId() . "' " . $selected . " class='serviceOption carrier_" . $sr->getCarrierId() . "'>" . $sr->getName() . "</option>";
            }
            $carrier_option = "<option value=''>Select Carrier</option>";
            foreach ($carriers as $cr) {
                $selected = "";
                if($cr->getId() == $selected_carrier) {
                    $selected = "selected='selected'";
                }
                $carrier_option .= "<option value='" . $cr->getId() . "' " . $selected . " >" . $cr->getCarrierDisplayName() . "</option>";
            }

            $return['services_option'] = $service_option;
            $return['carrier_option'] = $carrier_option;
            echo json_encode($return);
            die;
        }
        
        
        if (isset($_POST['download_file']) && $_POST['download_file'] == "download_excel") {
            if (!empty($user_account_id)) {
                if ($selectAccountSearchType == 'all') {
                    $allouedAcccounts = CustomerAccount::accountSubAccount($user_account_id, 0, true);
                } else if ($selectAccountSearchType == 'own') {
                    $allouedAcccounts = [$user_account_id];
                } else if ($selectAccountSearchType == 'subaccount') {
                    $allouedAcccounts = CustomerAccount::accountSubAccount($user_account_id, 0, false);
                }
            }
            if($this->user->getUserType() != 'client'){
            $reportCustomizeSettingsFilter->where(['rcs.id' => $this->showTemplate]);
            $reportCustomizeSettingsFilterObj = $reportCustomizeSettingsFilter->getList();
            if(count($reportCustomizeSettingsFilterObj) > 0)
                $this->fieldPermission = unserialize($reportCustomizeSettingsFilterObj[0]->getFieldsData());
            }else if($this->user->getUserType() == 'client'){
                unset($this->fieldPermission);
            }
            $cols = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z'];            
            $widthChart = 0.00;
            $heighthChart = 0.00;
            $lengththChart = 0.00;
            $weightChart = 0.00;
            $volumetricWeightChart = 0.00;
            $volumetricLitreChart = 0.00;
            $deliveryOnTime = [];
            $deliveryOutTime = [];
            $returnOwe = [];
            $undeliveredParcel = [];
            $trackindDataFilter = new TrackingDataFilter();
            $totalDaysOfDeliveryExHub = [];
            $toDate = (!empty($_POST['to_date']) ? date('Y-m-d', strtotime($_POST['to_date'])) : '');
            $fromDate = (!empty($_POST['from_date']) ? date('Y-m-d', strtotime($_POST['from_date'])) : '');
            $trackingNumberReport = (!empty($_POST['tracking_nums'])?str_replace("|", ",", $_POST['tracking_nums']):'');
            $this->countryId = (!empty($_POST['country']) ? $_POST['country'] : '');
            $this->serviceId = (!empty($_POST['service_id']) ? $_POST['service_id'] : '');
            $this->carrierId = (!empty($_POST['search_Carrier_id']) ? $_POST['search_Carrier_id'] : '');
            $this->userAccountId = (!empty($_POST['user_account_id'])?$_POST['user_account_id']:'');
            $this->showReport = (!empty($_POST['show_shipments'])?$_POST['show_shipments']:'all');
            if($this->user->getUserType() ==  User::USER_TYPE_CLIENT){
                $this->userAccountId = $this->user->getUserAccountId();                  
                $this->showReport = '';
            }
            if($this->showReport == 'own'){
              $this->userAccountId = (!empty($_POST['user_account_id'])?$_POST['user_account_id']:$this->user->getUserAccountId());
            }
            $objPHPExcel = new PHPExcel();
            $objGraphSheet = $objPHPExcel->getActiveSheet();
            $objGraphSheet->setTitle('Graph');
            // Set Permission For Column
            if(!empty($this->fieldPermission)){
                $columnCount  = 0;
                $trackTheCols = [];
                $trackAlphabetic = [];
                foreach($this->fieldPermission as $key => $columnData){
                    $objPHPExcel->getActiveSheet()->SetCellValue($cols[$columnCount].'20', $columnData['title']);
                    $trackTheCols[$cols[$columnCount]] = $columnData['key'];
                    $trackAlphabetic[] =$cols[$columnCount];
                    $columnCount++;
                }
            }else{
                
            }
//            echo '<pre>';
//            print_r($trackAlphabetic);
//            echo '</pre>';
//            die();
            if($this->user->getUserType() == 'client'){
                $objPHPExcel->getActiveSheet()->SetCellValue('A18', "Detailed Report");
                $objPHPExcel->getActiveSheet()->SetCellValue('A20', "Date");
                $objPHPExcel->getActiveSheet()->SetCellValue('B20', "Tracking Number");
                $objPHPExcel->getActiveSheet()->SetCellValue('C20', "HAWB");
                $objPHPExcel->getActiveSheet()->SetCellValue('D20', "Service");
                $objPHPExcel->getActiveSheet()->SetCellValue('E20', "City");
                $objPHPExcel->getActiveSheet()->SetCellValue('F20', "Country");
                $objPHPExcel->getActiveSheet()->SetCellValue('G20', "Weight(Kg)");
                $objPHPExcel->getActiveSheet()->SetCellValue('H20', "Volumetric Weight");
                $objPHPExcel->getActiveSheet()->SetCellValue('I20', "Volumetric Litre");
                $objPHPExcel->getActiveSheet()->SetCellValue('J20', "L X W X H");
                $objPHPExcel->getActiveSheet()->SetCellValue('K20', "Last Event Tracking Date");
                $objPHPExcel->getActiveSheet()->SetCellValue('L20', "Status");
                $objPHPExcel->getActiveSheet()->SetCellValue('M20', "Tracking Detail");
                $objPHPExcel->getActiveSheet()->SetCellValue('N20', "Total Transit Time (Calendar Days)");                
                $objPHPExcel->getActiveSheet()->SetCellValue('O20', "Total Transit Time (Working Days)");
            }
            $this->styleForReport = array(
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
            $leftStyleForReport = array(
                'font' => array(
                    'bold' => true,
                ),
                'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
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
            $this->headerStyle = array(
                'font' => array(
                    'bold' => true,
                    'size' => '16'
                ),
            );
            $style = array(
                'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                )
            );
           $leftAlgn = array( 
                    'alignment' => array(
                        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                    )
               );
            foreach(range('A','U') as $columnID) {
                $objPHPExcel->getActiveSheet()->getColumnDimension($columnID)
                    ->setAutoSize(true);
            }
            $objPHPExcel->getActiveSheet()->getStyle('A18')->applyFromArray($this->headerStyle);
            //$objPHPExcel->getActiveSheet()->getStyle('A20:C20')->applyFromArray($this->styleForReport);
            $objPHPExcel->getActiveSheet()->getStyle('D20:F20')->applyFromArray($leftStyleForReport);
            if($this->user->getUserType() == 'client')
                $objPHPExcel->getActiveSheet()->getStyle('G20:O20')->applyFromArray($this->styleForReport);
//            else
//                $objPHPExcel->getActiveSheet()->getStyle('G20:U20')->applyFromArray($this->styleForReport);
            
            $objPHPExcel->getActiveSheet()->getStyle('M20')->applyFromArray($leftStyleForReport);
            $trackingObj = $trackindDataFilter->getDailyTrackingDataReport($fromDate, $toDate, $allouedAcccounts,$this->carrierId, $this->serviceId, $this->countryId, $this->userAccountId,$trackingNumberReport,$this->showReport);
            $count = 20;
            $carrierOrderReceived = 0;
            $totalShipmentOfHub = [];
            $countingAlpha = count($trackTheCols);
            foreach ($trackingObj as $key => $data) {
                $undeliveredParcel[] = 1;
                $daysOfDelivered = 0;
                $deliveryWorkingDays = 0;
                    
                $count = $count + 1;
                $dateLabel = '';
                if ($data->getDateLabelCreated() > 0) {
                    $dateLabel = date("Y-m-d", $data->getDateLabelCreated());
                }
                $account = (!empty($data->getUserAccount()) ? $data->getUserAccount() : '');
                $lastEventTrackingDate = (!empty($data->getGroupLastTrackingDate()) ? $data->getGroupLastTrackingDate() : '');
                $trackingNumber = (!empty($data->getTrackingNumber()) ? $data->getTrackingNumber() : '');
                $trackingNumber = ParseTrackingNumber::Parse($trackingNumber);
                $trackingDetail = explode(',',$data->getGroupCarrierDesc());
                $transitTime = (!empty($data->getTransitTime()) ? $data->getTransitTime() : '');
                $deleivery_ex_hub = explode(',', $data->getGroupDateCreated());
                $trackingStatusCodes = explode(',', $data->getGroupStatusCode());
                $orderSubmittedKey = array_search(144, $trackingStatusCodes);
                $hubReceivedKey = array_search(146, $trackingStatusCodes);
                $carrierReceivedKey = array_search(148, $trackingStatusCodes);
                if(in_array('121',$trackingStatusCodes))
                    $deliveredStatusCount = array_search(121, $trackingStatusCodes);
                else if(in_array('122',$trackingStatusCodes))
                    $deliveredStatusCount = array_search(122, $trackingStatusCodes);
                else if(in_array('123',$trackingStatusCodes))
                    $deliveredStatusCount = array_search(123, $trackingStatusCodes);
                else
                    $deliveredStatusCount = '';
                $daysOfDifference = '0';
                $daysOfDifferenceOrderReceived = 0;
                $daysOfCarrierReceived = 0;
                if ($orderSubmittedKey!==FALSE) {
                    $firstIndexOrderSubmitted = $deleivery_ex_hub[$orderSubmittedKey];
                    $LastIndexOrderSubmitted = end($deleivery_ex_hub);
                   // echo $firstIndexOrderSubmitted.'-'.$LastIndexOrderSubmitted.'<br/>';
                    $date1 = date_create($firstIndexOrderSubmitted);
                    $date2 = date_create($LastIndexOrderSubmitted);
                    $diff = date_diff($date2, $date1);
                    $daysOfDifferenceOrderReceived = $diff->d;
                    
                }
                if ($hubReceivedKey!==FALSE) {
                    $firstIndexDelvieryExHUb = $deleivery_ex_hub[$hubReceivedKey];
                    $LastIndexDelvieryExHUb = $deleivery_ex_hub[$orderSubmittedKey];
                    $date1 = date_create($firstIndexDelvieryExHUb);
                    $date2 = date_create($LastIndexDelvieryExHUb);
                    $daysOfDifference = 0;
                    if(!empty($firstIndexDelvieryExHUb) && !empty($LastIndexDelvieryExHUb)){
                        $diff = date_diff($date2, $date1);
                        $daysOfDifference = $diff->d;
                        $totalDaysOfDeliveryExHub[]  = $diff->d;
                        $totalShipmentOfHub[] = 1;
                    }
                }
                if ($carrierReceivedKey!==FALSE) {
                    $firstIndexOrderSubmitted = $deleivery_ex_hub[$carrierReceivedKey];
                    $LastIndexOrderSubmitted = $deleivery_ex_hub[$hubReceivedKey];
                    $date1 = date_create($firstIndexOrderSubmitted);
                    $date2 = date_create($LastIndexOrderSubmitted);
                    $diff = date_diff($date2, $date1);
                    $daysOfCarrierReceived = $diff->d;
                    //$undeliveredParcel[] = 1;
                }
                if ($deliveredStatusCount!==FALSE) {
                    $firstIndexCarrierReceived = $deleivery_ex_hub[$carrierReceivedKey];
                    $LastIndexDelivery = $deleivery_ex_hub[$deliveredStatusCount];

                    $date1 = date_create($firstIndexCarrierReceived);
                    $date2 = date_create($LastIndexDelivery);
                    $diff = date_diff($date2, $date1);

                    $flagOfDelivered = 'N/A';
                    if(in_array(143,$trackingStatusCodes)){
                        $status = (array_key_exists(143, Tracking::$oneworld_status_code) ? Tracking::$oneworld_status_code['143'] : 'N/A');
                        $trackingDescription = array_search('143', $trackingStatusCodes);
                        if(!empty($undeliveredParcel)){
                            array_pop($undeliveredParcel);
                        }

                    }else if(!empty($LastIndexDelivery) && !empty($firstIndexCarrierReceived)) {
                        $date1 = date_create($firstIndexCarrierReceived);
                        $date2 = date_create($LastIndexDelivery);
                        $diff = date_diff($date2, $date1);
                        $daysOfDelivered = $diff->d;
                        // Wokring Days Calculate
                        $deliveryWorkingDays = $daysOfDelivered;//$this->number_of_working_days($firstIndexCarrierReceived, $LastIndexDelivery);
                        if (!empty($undeliveredParcel)) {
                            array_pop($undeliveredParcel);
                        }
                        if (!empty($deliveryWorkingDays) && $deliveryWorkingDays <= $transitTime) {
                            $flagOfDelivered = 'Yes';
                            $deliveryOnTime[] = 1;
                        } else if ($deliveryWorkingDays > $transitTime) {
                            $deliveryOutTime[] = 1;
                            $flagOfDelivered = 'No';
                        } else {
                            $deliveryOutTime[] = 1;
                            $flagOfDelivered = 'No';
                        }
                    }
                }
                $hawbNumber = (!empty($data->getHawb()) ? $data->getHawb() : '');
                $name = (!empty($data->getName()) ? $data->getName() : '');
                $postCode = (!empty($data->getPostCode()) ? $data->getPostCode() : '');
                $weight = (!empty($data->getWeight()) ? $data->getWeight() : '');
                $length = (!empty($data->getLength()) ? $data->getLength() : '');
                $width = (!empty($data->getWidth()) ? $data->getWidth() : '');
                $height = (!empty($data->getHeight()) ? $data->getHeight() : '');
                $deliveryAddress = $data->getAddressLine1() . ' ' . $data->getAddressLine2() . ' ' . $data->getAddressLine3();
                $vloumetricWeight = $length * $width * $height / 5000;
                $volumetricLitre = $length * $width * $height / 1000;
                $transit = (!empty($transitTime) ? $transitTime : 'N/A');
                //$statusCode = explode(',',$trackingStatusCodes);
                // Returned
                $trackingDescription = '';
                /*if(in_array(143,$trackingStatusCodes)){
                    $status = (array_key_exists(143, Tracking::$oneworld_status_code) ? Tracking::$oneworld_status_code['143'] : 'N/A');
                    $trackingDescription = array_search('143', $trackingStatusCodes);
                    
                }else{*/
                    $status = (array_key_exists($trackingStatusCodes[0], Tracking::$oneworld_status_code) ? Tracking::$oneworld_status_code[$trackingStatusCodes[0]] : 'N/A');
                //}
                trim($trackingDescription);
                // City FOR ip_address 
                $city = $data->getIpAddress();
                $countryName = $data->getWarehouseId();
                $objPHPExcel->getActiveSheet()->getStyle('A' . $count)->applyFromArray($style);
                $objPHPExcel->getActiveSheet()->getStyle('B' . $count)->applyFromArray($style)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
                $objPHPExcel->getActiveSheet()->getStyle('C' . $count)->applyFromArray($style);
                
                $objPHPExcel->getActiveSheet()->getStyle('C'.$count)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
                if($this->user->getUserType() == 'client'){
                    $objPHPExcel->getActiveSheet()->getStyle('G' . $count.':L'.$count)->applyFromArray($style);
                    $objPHPExcel->getActiveSheet()->getStyle('N' . $count.':O'.$count)->applyFromArray($style);
                    $objPHPExcel->getActiveSheet()->SetCellValue('A' . $count, $dateLabel);
                    if (is_numeric($trackingNumber)) { 
                        $objPHPExcel->getActiveSheet()->setCellValueExplicit('B' . $count, $trackingNumber,PHPExcel_Cell_DataType::TYPE_STRING);
                    }else{
                        $objPHPExcel->getActiveSheet()->setCellValue('B' . $count, $trackingNumber);
                    }
                    $objPHPExcel->getActiveSheet()->SetCellValue('C' . $count, $hawbNumber);
                    $objPHPExcel->getActiveSheet()->SetCellValue('D' . $count, $name);
                    $objPHPExcel->getActiveSheet()->SetCellValue('E' . $count, $city);
                    $objPHPExcel->getActiveSheet()->SetCellValue('F' . $count, $countryName);
                    $objPHPExcel->getActiveSheet()->SetCellValue('G' . $count, $weight);
                    $objPHPExcel->getActiveSheet()->SetCellValue('H' . $count, $vloumetricWeight);
                    $objPHPExcel->getActiveSheet()->SetCellValue('I' . $count, $volumetricLitre);
                    $objPHPExcel->getActiveSheet()->SetCellValue('J' . $count, $length . 'X' . $width . 'X' . $height);
                    $objPHPExcel->getActiveSheet()->SetCellValue('K' . $count, date('d-m-Y', strtotime($deleivery_ex_hub[0])));
                    $objPHPExcel->getActiveSheet()->SetCellValue('L' . $count, $status);
                    $objPHPExcel->getActiveSheet()->SetCellValue('M' . $count, ($trackingDescription =='')?$trackingDetail[0]:$trackingDetail[$trackingDescription]);
                    $totalNodaysDelivered = $daysOfDelivered + $daysOfDifference + $daysOfCarrierReceived;
                    $objPHPExcel->getActiveSheet()->SetCellValue('N' . $count, $totalNodaysDelivered);
                    $totalNodaysBussinessDelivered = $deliveryWorkingDays + $daysOfDifference + $daysOfCarrierReceived;
                    $objPHPExcel->getActiveSheet()->SetCellValue('O' . $count, $totalNodaysBussinessDelivered);
                    
                }else{
                    $color = '';
                    if($flagOfDelivered === 'Yes'){
                        $color = '32CD32';              
                    }else if($flagOfDelivered === 'No'){
                        $color = 'FF0000';
                    }else if($flagOfDelivered === 'N/A'){
                        $color ='FF8C00';
                    }else{
                        $color = 'FFFFFF';
                    }
                    
                    //$objPHPExcel->getActiveSheet()->getStyle('G' . $count.':U'.$count)->applyFromArray($style);
                    $objPHPExcel->getActiveSheet()->getStyle('M'.$count)->applyFromArray($leftAlgn);
                    $countingAlpha = 0;
                    if(in_array('date',$trackTheCols)){
                        $objPHPExcel->getActiveSheet()->SetCellValue($trackAlphabetic[$countingAlpha] . $count, $dateLabel);
                        $objPHPExcel->getActiveSheet()->getStyle($trackAlphabetic[$countingAlpha] . $count)->applyFromArray($style);
                        $countingAlpha++;
                    }
                    if(in_array('tracking_number',$trackTheCols)){
                        $objPHPExcel->getActiveSheet()->getStyle($trackAlphabetic[$countingAlpha] . $count)->applyFromArray($style);
                        if (is_numeric($trackingNumber)) { 
                            $objPHPExcel->getActiveSheet()->setCellValueExplicit($trackAlphabetic[$countingAlpha] . $count, $trackingNumber,PHPExcel_Cell_DataType::TYPE_STRING);
                        }else{
                            $objPHPExcel->getActiveSheet()->setCellValue($trackAlphabetic[$countingAlpha] . $count, $trackingNumber);
                        }
//                        $objPHPExcel->getActiveSheet()->SetCellValue($trackAlphabetic[$countingAlpha] . $count, $trackingNumber);
                        $countingAlpha++;
                    }                    
                    if(in_array('hawb',$trackTheCols)){
                        $objPHPExcel->getActiveSheet()->SetCellValue($trackAlphabetic[$countingAlpha] . $count, $hawbNumber);
                        $countingAlpha++;
                    }     
                    if(in_array('service',$trackTheCols)){
                        $objPHPExcel->getActiveSheet()->SetCellValue($trackAlphabetic[$countingAlpha] . $count, $name);
                        $objPHPExcel->getActiveSheet()->getStyle($trackAlphabetic[$countingAlpha] . $count)->applyFromArray($style);
                        $countingAlpha++;
                    }                    
                    if(in_array('city',$trackTheCols)){
                        $objPHPExcel->getActiveSheet()->SetCellValue($trackAlphabetic[$countingAlpha] . $count, $city);
                        $objPHPExcel->getActiveSheet()->getStyle($trackAlphabetic[$countingAlpha] . $count)->applyFromArray($style);
                        $countingAlpha++;
                    }                    
                    if(in_array('country',$trackTheCols)){
                        $objPHPExcel->getActiveSheet()->SetCellValue($trackAlphabetic[$countingAlpha] . $count, $countryName);
                        $objPHPExcel->getActiveSheet()->getStyle($trackAlphabetic[$countingAlpha] . $count)->applyFromArray($style);
                        $countingAlpha++;
                    }
                    if(in_array('weight',$trackTheCols)){
                        $objPHPExcel->getActiveSheet()->SetCellValue($trackAlphabetic[$countingAlpha] . $count, $weight);
                        $objPHPExcel->getActiveSheet()->getStyle($trackAlphabetic[$countingAlpha] . $count)->applyFromArray($style);
                        $countingAlpha++;
                    }
                    if(in_array('volumetric_weight',$trackTheCols)){
                        $objPHPExcel->getActiveSheet()->SetCellValue($trackAlphabetic[$countingAlpha] . $count, $vloumetricWeight);
                        $objPHPExcel->getActiveSheet()->getStyle($trackAlphabetic[$countingAlpha] . $count)->applyFromArray($style);
                        $countingAlpha++;
                    }
                    if(in_array('volumetric_liter',$trackTheCols)){
                        $objPHPExcel->getActiveSheet()->SetCellValue($trackAlphabetic[$countingAlpha]. $count, $volumetricLitre);
                        $objPHPExcel->getActiveSheet()->getStyle($trackAlphabetic[$countingAlpha] . $count)->applyFromArray($style);
                        $countingAlpha++;
                    }
                    if(in_array('lxwxh',$trackTheCols)){
                        $objPHPExcel->getActiveSheet()->SetCellValue($trackAlphabetic[$countingAlpha]. $count, $length . 'X' . $width . 'X' . $height);
                        $objPHPExcel->getActiveSheet()->getStyle($trackAlphabetic[$countingAlpha] . $count)->applyFromArray($style);
                        $countingAlpha++;
                    }
                    if(in_array('last_event_tracking_date',$trackTheCols)){
                        $lastEventOFTracking = explode(',',$lastEventTrackingDate);
                        $objPHPExcel->getActiveSheet()->SetCellValue($trackAlphabetic[$countingAlpha]. $count, $lastEventOFTracking[0]);
                        $objPHPExcel->getActiveSheet()->getStyle($trackAlphabetic[$countingAlpha] . $count)->applyFromArray($style);
                        $countingAlpha++;
                    }
                    if(in_array('status',$trackTheCols)){
                        $objPHPExcel->getActiveSheet()->SetCellValue($trackAlphabetic[$countingAlpha]. $count, $status);
                        $objPHPExcel->getActiveSheet()->getStyle($trackAlphabetic[$countingAlpha] . $count)->applyFromArray($style);
                        $countingAlpha++;
                    }
                    if(in_array('tracking_detail',$trackTheCols)){
                        $objPHPExcel->getActiveSheet()->SetCellValue($trackAlphabetic[$countingAlpha]. $count, ($trackingDescription =='')?$trackingDetail[0]:$trackingDetail[$trackingDescription]);
                        $objPHPExcel->getActiveSheet()->getStyle($trackAlphabetic[$countingAlpha] . $count)->applyFromArray($style);
                        $countingAlpha++;
                    }
                    if(in_array('delivery_on_time',$trackTheCols)){
                        $objPHPExcel->getActiveSheet()->SetCellValue($trackAlphabetic[$countingAlpha] . $count, $flagOfDelivered);
                        $objPHPExcel->getActiveSheet()->getStyle($trackAlphabetic[$countingAlpha] . $count)->applyFromArray($style);
                        $countingAlpha++;
                    }
                    if(in_array('delivery_aim_working_days',$trackTheCols)){
                        $objPHPExcel->getActiveSheet()->SetCellValue($trackAlphabetic[$countingAlpha]. $count, $transit);
                        $objPHPExcel->getActiveSheet()->getStyle($trackAlphabetic[$countingAlpha] . $count)->applyFromArray($style);
                        $countingAlpha++;
                    }
                    if(in_array('total_no_of_days_booking_to_hub_received',$trackTheCols)){
                        $objPHPExcel->getActiveSheet()->SetCellValue($trackAlphabetic[$countingAlpha] . $count, $daysOfDifference);
                        $objPHPExcel->getActiveSheet()->getStyle($trackAlphabetic[$countingAlpha] . $count)->applyFromArray($style);
                        $countingAlpha++;
                    }
                    if(in_array('total_no_of_days_hub_received_to_carrier_received',$trackTheCols)){
                        $objPHPExcel->getActiveSheet()->SetCellValue($trackAlphabetic[$countingAlpha] . $count, $daysOfCarrierReceived);
                        $objPHPExcel->getActiveSheet()->getStyle($trackAlphabetic[$countingAlpha] . $count)->applyFromArray($style);
                        $countingAlpha++;
                    }
                    if(in_array('total_no_of_days_from_carrier_received_calendar_days',$trackTheCols)){
                        $objPHPExcel->getActiveSheet()->SetCellValue($trackAlphabetic[$countingAlpha] . $count, $daysOfDelivered);
                        $objPHPExcel->getActiveSheet()->getStyle($trackAlphabetic[$countingAlpha] . $count)->applyFromArray($style);
                        $countingAlpha++;
                    }
                    if(in_array('total_no_of_working_days_from_carrier_received',$trackTheCols)){
                        $totalNodaysDelivered = $daysOfDelivered + $daysOfDifference + $daysOfCarrierReceived;
                        $objPHPExcel->getActiveSheet()->getStyle($trackAlphabetic[$countingAlpha].$count)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB($color);
                        $objPHPExcel->getActiveSheet()->SetCellValue($trackAlphabetic[$countingAlpha] . $count, $deliveryWorkingDays);
                        $objPHPExcel->getActiveSheet()->getStyle($trackAlphabetic[$countingAlpha] . $count)->applyFromArray($style);
                        $countingAlpha++;
                    }
                    if(in_array('total_transit_time_calendar_days',$trackTheCols)){
                        $objPHPExcel->getActiveSheet()->SetCellValue($trackAlphabetic[$countingAlpha] . $count, $totalNodaysDelivered);
                        $objPHPExcel->getActiveSheet()->getStyle($trackAlphabetic[$countingAlpha] . $count)->applyFromArray($style);
                        $countingAlpha++;
                    }
                    if(in_array('total_transit_time_working_days',$trackTheCols)){
                        $totalNodaysBussinessDelivered = $deliveryWorkingDays + $daysOfDifference + $daysOfCarrierReceived;
                        $objPHPExcel->getActiveSheet()->SetCellValue($trackAlphabetic[$countingAlpha] . $count, $totalNodaysBussinessDelivered);
                        $objPHPExcel->getActiveSheet()->getStyle($trackAlphabetic[$countingAlpha] . $count)->applyFromArray($style);
                        $countingAlpha++;
                    }
                    $objPHPExcel->getActiveSheet()->getStyle('A20:'.end(array_keys($trackTheCols)).'20')->applyFromArray($this->styleForReport);
                }
                
                //$objPHPExcel->getActiveSheet()->getStyle('B' . $count)->applyFromArray($style)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
                // Graph in excel 
                $widthChart = $widthChart + $width;
                $lengththChart = $lengththChart + $length;
                $heighthChart = $heighthChart + $height;
                $weightChart = $weightChart + $weight;
                $volumetricWeightChart = $volumetricWeightChart + $length * $width * $height / 5000;
                $volumetricLitreChart = $volumetricLitreChart + $length * $width * $height / 1000;

                if(in_array(143,$trackingStatusCodes)){
                     $returnOwe[] = 1;
                }

            }
            $totalHub = array_sum($totalDaysOfDeliveryExHub);
            $totalShipHub = array_sum($totalShipmentOfHub);
            $avrHubReceived = $totalHub / $totalShipHub;
            $barChartHubReceived = [];
            $barChartHubReceived[0] = ['','Average Hub Recevied (Days)'];
            $barChartHubReceived[1] = ['Average Hub Recevied (Days)',number_format($avrHubReceived,2)];
            $carrierPerformance = ['','Delivered On Time (Total)','Delivered Out Time (Total)','Returned (Total)','Undelivered (Total)'];
            $this->deliveryOverViewPieChart[0] = ['','Delivered On Time (Total)','Delivered Out Time (Total)','Returned (Total)','Undelivered (Total)'];
            $this->deliveryOverViewPieChart[1] = ['Delivered On Time', array_sum($deliveryOnTime)];
            $this->deliveryOverViewPieChart[2] = ['Delivered Out Time', array_sum($deliveryOutTime)];
            $this->deliveryOverViewPieChart[3] = ['Returned', array_sum($returnOwe)];
            $this->deliveryOverViewPieChart[4] = ['Undelivered', array_sum($undeliveredParcel)];
            
            $objGraphDataSheet = $objPHPExcel->createSheet(1);
            $objGraphDataSheet->setTitle('GraphData');
            $objGraphDataSheet = $objPHPExcel->setActiveSheetIndex(1);
            $objGraphDataSheet->fromArray(
                    $this->deliveryOverViewPieChart
            );
            $dataseriesLabels = [];
            $totalPerformance = count($carrierPerformance);
            $totalStatus = count($this->deliveryOverViewPieChart);
            for ($i = 1; $i <= $totalPerformance; $i++) {
                if (isset($cols[$i])) {
                    $col = $cols[$i];
                    $dataseriesLabels[] = new PHPExcel_Chart_DataSeriesValues('String', 'GraphData!$' . $col . '$1', null, 1); //	2010
                }
            }
            $xAxisTickValues = array(
                new PHPExcel_Chart_DataSeriesValues('String', 'GraphData!$A$2:$A$' . ($totalStatus), null, ($totalStatus - 1)), //	Q1 to Q4
            );
            $dataSeriesValues = [];
            for ($j = 1; $j <= $totalPerformance; $j++) {
                if (isset($cols[$j])) {
                    $col = $cols[$j];
                    $dataSeriesValues[] = new PHPExcel_Chart_DataSeriesValues('Number', 'GraphData!$' . $col . '$2:$' . $col . '$' . ($totalStatus), null, ($totalStatus - 1));
                }
            }            
            $seriesPie = new PHPExcel_Chart_DataSeries(
                    PHPExcel_Chart_DataSeries::TYPE_PIECHART, NULL, range(0, count($dataSeriesValues) - 1), $dataseriesLabels, $xAxisTickValues, $dataSeriesValues, null, null, true);
            //Delivery Ratio Pie Chart
            $legendPie = new PHPExcel_Chart_Legend(PHPExcel_Chart_Legend::POSITION_RIGHT, NULL, false);
            $layout1 = new PHPExcel_Chart_Layout();
//            $layout1->setShowVal(TRUE);
            $layout1->setShowPercent(TRUE);
            $plotareaPie = new PHPExcel_Chart_PlotArea($layout1, array($seriesPie));
            $titlePie = new PHPExcel_Chart_Title('Delivery Status Ratio');
            $chartPie = new PHPExcel_Chart(
                    'chartPie', $titlePie, $legendPie, $plotareaPie, true, 0, NULL, NULL
            );
            $chartPie->setTopLeftPosition('A1');
            $chartPie->setBottomRightPosition('D12');
            $objGraphSheet = $objPHPExcel->setActiveSheetIndex(0);
            $objPHPExcel->getActiveSheet()->addChart($chartPie);
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename=shipping-status-report' . date('d-m-Y') . '.xls'); // file name of excel
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

        if (isset($_GET['action']) && $_GET['action'] == "tracking_list") {
            $setDataArr = array();
            //$user_account_id = (!empty($this->form_vars['user_account_id'])? $this->form_vars['user_account_id']: $this->user->getUserAccountId());
            $this->userAccountId = (!empty($_GET['user_account_id'])?$_GET['user_account_id']:$this->user->getUserAccountId());
            $selectAccountSearchType = $_GET['show_shipments'];
            
            if(empty($selectAccountSearchType)) {
                $selectAccountSearchType = "all";
            }
            $allouedAcccounts = [];
            if (!empty($user_account_id)) {
                if ($selectAccountSearchType == 'all') {
                    $allouedAcccounts = CustomerAccount::accountSubAccount($this->userAccountId, 0, true);
                } else if ($selectAccountSearchType == 'own') {
                    $allouedAcccounts = [$this->userAccountId];
                } else if ($selectAccountSearchType == 'subaccount') {
                    $allouedAcccounts = CustomerAccount::accountSubAccount($this->userAccountId, 0, false);
                }
            }
            $trackindDataFilter = new TrackingDataFilter();
            $toDate = (!empty($_GET['toDate']) ? date('Y-m-d', strtotime($_GET['toDate'])) : '');
            $fromDate = (!empty($_GET['fromDate']) ? date('Y-m-d', strtotime($_GET['fromDate'])) : '');
            $this->countryId = (!empty($_GET['country']) ? $_GET['country'] : '');
            $this->serviceId = (!empty($_GET['service_id']) ? $_GET['service_id'] : '');
            $this->carrierId = (!empty($_GET['search_Carrier_id']) ? $_GET['search_Carrier_id'] : '');
            
            
            //$this->userAccountId = (!empty($_POST['user_account_id'])?$_POST['user_account_id']:'');
            //$this->showReport = (!empty($_POST['show_shipments'])?$_POST['show_shipments']:'all');
            if($this->user->getUserType() ==  User::USER_TYPE_CLIENT){
                $this->userAccountId = $this->user->getUserAccountId();
                $this->showReport = '';
            }
            $trackingObj = $trackindDataFilter->getDailyTrackingDataReport($fromDate, $toDate, $allouedAcccounts,$this->carrierId, $this->serviceId, $this->countryId, $this->userAccountId,'',$this->showReport);
            $iTotalRecords = $trackindDataFilter->getDailyTrackingDataCountReport2();
                        $iDisplayLength = intval($_REQUEST['length']);
            $iTotalRecords = (!empty($iTotalRecords) ? $iTotalRecords[0]->getId() : '0');
            //echo $iTotalRecords;
            $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
            $iDisplayStart = intval($_REQUEST['start']);
            $sEcho = intval($_REQUEST['draw']);
            $end = $iDisplayStart + $iDisplayLength;
            $end = $end > $iTotalRecords ? $iTotalRecords : $end;
            $trackindDataFilter->setRowsPerPage($iDisplayLength);
            $trackindDataFilter->setOffset($iDisplayStart);
            //$trackingObj = $trackindDataFilter->getDailyTrackingDataReport($fromDate, $toDate, $allouedAcccounts,$this->carrierId, $this->serviceId, $this->countryId, $this->userAccountId,'',$this->showReport);
            $carrierOrderReceived = 0;
            $count = 0;
            foreach ($trackingObj as $key => $data) {
                $dateLabel = '';
                $daysOfDelivered = 0;
                if ($data->getDateLabelCreated() > 0) {
                    $dateLabel = date("Y-m-d", $data->getDateLabelCreated());
                }
                $account = (!empty($data->getUserAccount()) ? $data->getUserAccount() : '');
                $trackingNumber = (!empty($data->getTrackingNumber()) ? $data->getTrackingNumber() : '');
                if ($key == 0) {
                    $first_tracking_number = $trackingNumber;
                }
                $lastEventTrackingDate = (!empty($data->getGroupLastTrackingDate()) ? $data->getGroupLastTrackingDate() : '');
                $transitTime = (!empty($data->getTransitTime()) ? $data->getTransitTime() : '');
                $deleivery_ex_hub = explode(',', $data->getGroupDateCreated());
                $trackingStatusCodes = explode(',', $data->getGroupStatusCode());
                $orderSubmittedKey = array_search(144, $trackingStatusCodes);
                $hubReceivedKey = array_search(146, $trackingStatusCodes);
                $carrierReceivedKey = array_search(148, $trackingStatusCodes);
                if(in_array('121',$trackingStatusCodes))
                    $deliveredStatusCount = array_search(121, $trackingStatusCodes);
                else if(in_array('122',$trackingStatusCodes))
                    $deliveredStatusCount = array_search(122, $trackingStatusCodes);
                else if(in_array('123',$trackingStatusCodes))
                    $deliveredStatusCount = array_search(123, $trackingStatusCodes);
                else
                    $deliveredStatusCount = ''; 
                
                $daysOfDifference = '0';
                $daysOfDifferenceOrderReceived = 0;
                $daysOfCarrierReceived = 0;
                if ($orderSubmittedKey!==FALSE ) {
                    $firstIndexOrderSubmitted = $deleivery_ex_hub[$orderSubmittedKey];
                    $LastIndexOrderSubmitted = end($deleivery_ex_hub);
                   // echo $firstIndexOrderSubmitted.'-'.$LastIndexOrderSubmitted.'<br/>';
                    $date1 = date_create($firstIndexOrderSubmitted);
                    $date2 = date_create($LastIndexOrderSubmitted);
                    $diff = date_diff($date2, $date1);
                    $daysOfDifferenceOrderReceived = $diff->d;
                    
                }
                if ($hubReceivedKey!==FALSE ) {
                    $firstIndexDelvieryExHUb = $deleivery_ex_hub[$hubReceivedKey];
                    $LastIndexDelvieryExHUb = $deleivery_ex_hub[$orderSubmittedKey];
                    $date1 = date_create($firstIndexDelvieryExHUb);
                    $date2 = date_create($LastIndexDelvieryExHUb);
                    $daysOfDifference = 0;
                    if(!empty($firstIndexDelvieryExHUb) && !empty($LastIndexDelvieryExHUb)){
                        $diff = date_diff($date2, $date1);
                        $daysOfDifference = $diff->d;
                    }
                                        
                }
                if ($carrierReceivedKey!==FALSE ) {
                    $firstIndexOrderSubmitted = $deleivery_ex_hub[$carrierReceivedKey];
                    $LastIndexOrderSubmitted = $deleivery_ex_hub[$hubReceivedKey];
                    $date1 = date_create($firstIndexOrderSubmitted);
                    $date2 = date_create($LastIndexOrderSubmitted);
                    $daysOfCarrierReceived = 0;
                    if(!empty($firstIndexOrderSubmitted) && !empty($LastIndexOrderSubmitted)){
                        $diff = date_diff($date2, $date1);
                        $daysOfCarrierReceived = $diff->d;
                    }
                }
                if ($deliveredStatusCount!==FALSE ) {
                    $firstIndexCarrierReceived = $deleivery_ex_hub[$carrierReceivedKey];
                    $LastIndexDelivery = $deleivery_ex_hub[$deliveredStatusCount];
                   // $daysOfDelivered = 0;
                    if(!empty($LastIndexDelivery) && !empty($firstIndexCarrierReceived)){
                        $date1 = date_create($firstIndexCarrierReceived);
                        $date2 = date_create($LastIndexDelivery);
                        $diff = date_diff($date2, $date1);
                        $daysOfDelivered = $diff->d;
                    }

                    
                    $flag = '<span class="badge badge-roundless badge-info">N/A</span>';;
                    if (!empty($daysOfDelivered) && $daysOfDelivered <= $transitTime) {
                        $flag = '<span class="badge badge-roundless badge-success">Yes</span>';
                    }else if (!empty($daysOfDelivered) &&$daysOfDelivered === $transitTime) {
                         $flag = '<span class="badge badge-roundless badge-success">Yes</span>';
                    }else if(!empty($daysOfDelivered) && $daysOfDelivered >= $transitTime){
                        $flag = '<span class="badge badge-roundless badge-danger">No</span>';
                    }
                }
                $last_tarcking_number = $trackingNumber;
                $hawbNumber = (!empty($data->getHawb()) ? $data->getHawb() : '');
                $name = (!empty($data->getName()) ? $data->getName() : '');
                $postCode = (!empty($data->getPostCode()) ? $data->getPostCode() : '');
                $weight = (!empty($data->getWeight()) ? $data->getWeight() : '');
                $length = (!empty($data->getLength()) ? $data->getLength() : '');
                $width = (!empty($data->getWidth()) ? $data->getWidth() : '');
                $height = (!empty($data->getHeight()) ? $data->getHeight() : '');
                $trackingDetail = explode(',',$data->getGroupCarrierDesc());
                $deliveryAddress = $data->getAddressLine1() . ' ' . $data->getAddressLine2() . ' ' . $data->getAddressLine3();
                $currentArr = array();
                $clientArr = array();
                $vloumeLitre = 0;
                $currentArr['date_label_created'] = date('d-m-Y', strtotime($dateLabel));
                $currentArr['user_account'] = $account;
                $currentArr['tracking_nuumber'] = $trackingNumber;
                //$currentArr['tracking_date'] = date('d-m-Y', strtotime($data->getDateCreated()));
                $lastEventOFTracking = explode(',',$lastEventTrackingDate);
                $currentArr['tracking_date'] = date('d-m-Y H:i:s',  strtotime($lastEventOFTracking[0]));
                $currentArr['width'] = $width;
                $currentArr['length'] = $length;
                $currentArr['height'] = $height;
                $currentArr['volumetric_weight'] = $length * $width * $height / 5000;
                $vloumeLitre = $length * $width * $height / 1000;
                $currentArr['volumetric_litre'] = (!empty($vloumeLitre)?$vloumeLitre:'0');
                $vloumetricWeight = $length * $width * $height / 5000;
                $currentArr['transit_time'] = (!empty($transitTime) ? $transitTime : '<span class="badge badge-roundless badge-danger">N/A</span>');
                //$currentArr['tracking_detail'] = $data->getCarrierDesc();
                $currentArr['tracking_detail'] = $trackingDetail[0];
                
                //$currentArr['delivery_ontime'] = ($transitTime <= $daysOfCarrierReceived) ?'<span class="badge badge-roundless badge-danger">No</span>': '<span class="badge badge-roundless badge-warning">YES</span>';
                $currentArr['delivery_ontime'] = $flag;
                //$currentArr['delivery_outtime'] = ($transitTime > $daysOfDifference) ? '<label class="label label-success">Yes</label>' : '<label class="label label-danger">No</label>';
                $undelivered = (array_key_exists($data->getStatusCodeId(), Tracking::$oneworld_status_code) ? '1' : '<span class="badge badge-roundless badge-danger">N/A</span>');
                $status = (array_key_exists($trackingStatusCodes[0], Tracking::$oneworld_status_code) ? Tracking::$oneworld_status_code[$trackingStatusCodes[0]] : 'N/A');
                $currentArr['status'] = $status;
                $statusOfParcel = $undelivered;
                $currentArr['total_days_ex_hubs'] =  $daysOfDifference . '<br/>' . $daysOfCarrierReceived.'<br>'.$daysOfDelivered;
                $setDataArr [] = $currentArr;
                
            }
            //exit;   
            $setDataArrJson['data'] = $setDataArr;
            $setDataArrJson['draw'] = $sEcho;
            $setDataArrJson['recordsTotal'] = $iTotalRecords;
            $setDataArrJson['recordsFiltered'] = $iTotalRecords;
            echo json_encode($setDataArrJson);
            die;
        }

        $this->countryId = (!empty($_POST['country']) ? $_POST['country'] : '');
        $this->serviceId = (!empty($_POST['service_id']) ? $_POST['service_id'] : '');
        $this->carrierId = (!empty($_POST['search_Carrier_id']) ? $_POST['search_Carrier_id'] : '');
        $this->userAccountId = (!empty($_POST['user_account_id'])?$_POST['user_account_id']:'');
        $this->showReport = (!empty($_POST['show_shipments'])?$_POST['show_shipments']:'all');
//        echo '<pre>';
//        print_r($this->showReport);
//        echo '</pre>';
//        die();
        if($this->user->getUserType() ==  User::USER_TYPE_CLIENT){
            $this->userAccountId = $this->user->getUserAccountId();
            $this->showReport ='';
        }
        if($this->showReport == 'own'){
              $this->userAccountId = (!empty($_POST['user_account_id'])?$_POST['user_account_id']:$this->user->getUserAccountId());
        }
        
        $toDate = (!empty($_POST['to_date']) ? date('Y-m-d', strtotime($_POST['to_date'])) : '');
        $fromDate = (!empty($_POST['from_date']) ? date('Y-m-d', strtotime($_POST['from_date'])) : '');
        $where = '';
        $trackindDataFilter = new TrackingDataFilter();
        if (!empty($fromDate) && !empty($toDate)) {
            $where = ' WHERE DATE(t.date_created) >= "' . $fromDate . '" AND DATE(t.date_created) <= "' . $toDate . '"';
        }
        if (!empty($user_account_id)) {
            if ($selectAccountSearchType == 'all') {
                $allouedAcccounts = CustomerAccount::accountSubAccount($user_account_id, 0, true);
            } else if ($selectAccountSearchType == 'own') {
                $allouedAcccounts = [$user_account_id];
            } else if ($selectAccountSearchType == 'subaccount') {
                $allouedAcccounts = CustomerAccount::accountSubAccount($user_account_id, 0, false);
            }
        }
        $trackingClassObj = new Tracking();
        $trackingObj = $trackindDataFilter->getDailyTrackingDataReport($fromDate, $toDate, $allouedAcccounts,$this->carrierId, $this->serviceId, $this->countryId, $this->userAccountId,'',$this->showReport);
        $widthChart = 0.00;
        $heighthChart = 0.00;
        $lengththChart = 0.00;
        $weightChart = 0.00;
        $volumetricWeightChart = 0.00;
        $volumetricLitreChart = 0.00;
        $returnOwe = [];
        $undeliveredParcel = [];
        $totalDeliveryDayOFExHub = [];
        $noOfTransitTime = 0;
        $serviceName = '';
        $totalShipmentOfHub = [];

        if (!empty($trackingObj)) {
           $deliveryOnTime=[];
           $deliveryOutTime = [];
            foreach ($trackingObj as $key => $data) {
                $undeliveredParcel[] = 1;
                $dateLabel = '';
                if ($data->getDateLabelCreated() > 0) {
                    $dateLabel = date("Y-m-d", $data->getDateLabelCreated());
                }
                $account = (!empty($data->getUserAccount()) ? $data->getUserAccount() : '');
                $trackingNumber = (!empty($data->getTrackingNumber()) ? $data->getTrackingNumber() : '');
                if ($key == 0) {
                    $first_tracking_number = $trackingNumber;
                }
                $serviceName = (!empty($data->getName()) ? $data->getName() : '');
                $deleivery_ex_hub = explode(',', $data->getGroupDateCreated());
                $trackingStatusCodes = explode(',', $data->getGroupStatusCode());
                $transitTime = (!empty($data->getTransitTime()) ? $data->getTransitTime() : '');                
                $orderSubmittedKey = array_search(144, $trackingStatusCodes);
                $receivedHub = array_search(146, $trackingStatusCodes);
                $carrierReceivedKey = array_search(148, $trackingStatusCodes);
                $receivedParcel = 'khota';
                if(in_array(121,$trackingStatusCodes)){
                    $deliveredStatusCount = array_search(121, $trackingStatusCodes);
                    $receivedParcel = 121;
                }else if(in_array(122,$trackingStatusCodes)){
                    $deliveredStatusCount = array_search(122, $trackingStatusCodes);
                    $receivedParcel = 122;
                }
                else if(in_array(123,$trackingStatusCodes)){
                    $deliveredStatusCount = array_search(123, $trackingStatusCodes);
                    $receivedParcel = 123;
                }
                
                $daysOfDifference = '0';
                if ($carrierReceivedKey!==FALSE && in_array(148,$trackingStatusCodes)) {
                    $firstIndexOrderSubmitted = $deleivery_ex_hub[$carrierReceivedKey];
                    $LastIndexOrderSubmitted = $deleivery_ex_hub[$orderSubmittedKey];
                    $date1 = date_create($firstIndexOrderSubmitted);
                    $date2 = date_create($LastIndexOrderSubmitted);
                    $diff = date_diff($date2, $date1);
                    $daysOfCarrierReceived = $diff->d;
                }
                if ($receivedHub!==FALSE && in_array(146,$trackingStatusCodes)) {
                    $firstIndexDelvieryExHUb = $deleivery_ex_hub[$receivedHub];
                    $LastIndexDelvieryExHUb = $deleivery_ex_hub[$orderSubmittedKey];
                    $date1 = date_create($firstIndexDelvieryExHUb);
                    $date2 = date_create($LastIndexDelvieryExHUb);
                    $daysOfDifference = 0;
                    if(!empty($firstIndexDelvieryExHUb) && !empty($LastIndexDelvieryExHUb)){
                        $diff = date_diff($date2, $date1);
                        $daysOfDifference = $diff->d;
                        $totalDeliveryDayOFExHub[] = $diff->d;
                        $totalShipmentOfHub[] = 1;
                    }

                }
                if ($orderSubmittedKey!==FALSE) {
                    $firstIndexOrderSubmitted = $deleivery_ex_hub[$orderSubmittedKey];
                    $LastIndexOrderSubmitted = end($deleivery_ex_hub);
                    $date1 = date_create($firstIndexOrderSubmitted);
                    $date2 = date_create($LastIndexOrderSubmitted);
                    $daysOfDifferenceOrderReceived = 0;
                    if(!empty($firstIndexDelvieryExHUb) && !empty($LastIndexDelvieryExHUb)){
                        $diff = date_diff($date2, $date1);
                        $daysOfDifferenceOrderReceived = $diff->d;
                    }
                }
                if(in_array(143,$trackingStatusCodes)){
                    if(!empty($undeliveredParcel)){
                        array_pop($undeliveredParcel);
                    }
                    $returnOwe[] = 1;
                }else if ($deliveredStatusCount!==FALSE && in_array($receivedParcel,$trackingStatusCodes)) {
                    if(!empty($undeliveredParcel)){
                        array_pop($undeliveredParcel);
                    }
                    $firstIndexCarrierReceived = $deleivery_ex_hub[$carrierReceivedKey];
                    $LastIndexDelivery = $deleivery_ex_hub[$deliveredStatusCount];
                    $date1 = date_create($firstIndexCarrierReceived);
                    $date2 = date_create($LastIndexDelivery);
                    $diff = date_diff($date2, $date1);
                    $daysOfDelivered = 0;
                    if(!empty($LastIndexDelivery) && !empty($firstIndexCarrierReceived)){
                        $date1 = date_create($firstIndexCarrierReceived);
                        $date2 = date_create($LastIndexDelivery);
                        $diff = date_diff($date2, $date1);
                        $daysOfDelivered = $diff->d;
                    }

                    if (!empty($daysOfDelivered) && $daysOfDelivered <= $transitTime) {
                        $deliveryOnTime[] = 1;
                    }else if($daysOfDelivered > $transitTime){
                       // echo '<pre>';
//                    echo $firstIndexCarrierReceived.'=>'.$LastIndexDelivery.'='.$diff->d.'-Tranist Time=>'.$data->getTransitTime();
//                    echo '</pre>';
                        $deliveryOutTime[] = 1;
                    }else{
                        $deliveryOutTime[] = 1;
                    }
                }
                $daysOfDifference = '0';

                $noOfTransitTime = $daysOfCarrierReceived - array_sum($totalDeliveryDayOFExHub);
                $this->lineChartTransitTime[$serviceName][] = $noOfTransitTime;
                $last_tarcking_number = $trackingNumber;
                $weight = (!empty($data->getWeight()) ? $data->getWeight() : '');
                $length = (!empty($data->getLength()) ? $data->getLength() : '');
                $width = (!empty($data->getWidth()) ? $data->getWidth() : '');
                $height = (!empty($data->getHeight()) ? $data->getHeight() : '');
                $transitTime = (!empty($data->getTransitTime()) ? $data->getTransitTime() : '');
                $widthChart = $widthChart + $width;
                $lengththChart = $lengththChart + $length;
                $heighthChart = $heighthChart + $height;
                $weightChart = $weightChart + $weight;
                $volumetricWeightChart = $volumetricWeightChart + $length * $width * $height / 5000;
                $volumetricLitreChart = $volumetricLitreChart + $length * $width * $height / 1000;
            }
        }
        $this->averageWeightbarChart[0] = array(
            'dim' => 'Weight (Kg)',
            'value' => $weightChart
        );
        $this->averageWeightbarChart[1] = array(
            'dim' => 'Volmetric Weight (Kg)',
            'value' => $volumetricWeightChart
        );
        $this->averageWeightbarChart[2] = array(
            'dim' => 'Width (cm)',
            'value' => $widthChart
        );
        $this->averageWeightbarChart[3] = array(
            'dim' => 'Length (cm)',
            'value' => $lengththChart
        );
        $this->averageWeightbarChart[4] = array(
            'dim' => 'Height (cm)',
            'value' => $heighthChart
        );
        $this->averageWeightbarChart[4] = array(
            'dim' => 'Volume Litre',
            'value' => $volumetricLitreChart
        );
        // Pie Chart 
//        echo '<pre>';
//        print_r($deliveryOnTime);
//        echo "<br />";
//        print_r($deliveryOutTime);
//        echo "<br />";
//        print_r($returnOwe);
//        echo "<br />";
//        print_r($undeliveredParcel);
//        echo '</pre>';
//        die();

        $totalHub = array_sum($totalDeliveryDayOFExHub);
        $shipmentTotal = array_sum($totalShipmentOfHub);
        $averageDayOfHubRecived = $totalHub / $shipmentTotal;
        $this->deliveryRatioPieChart[0] = array('caption' => 'Delivered Ontime', 'value' => array_sum($deliveryOnTime));
        $this->deliveryRatioPieChart[1] = array('caption' => 'Delivered Outtime', 'value' => array_sum($deliveryOutTime));
        $this->deliveryOverViewPieChart[0] = array('caption' => 'Delivered Ontime', 'value' => array_sum($deliveryOnTime));
        $this->deliveryOverViewPieChart[1] = array('caption' => 'Delivered Outtime', 'value' => array_sum($deliveryOutTime));
        $this->deliveryOverViewPieChart[2] = array('caption' => 'Returned', 'value' => array_sum($returnOwe));
        $this->deliveryOverViewPieChart[3] = array('caption' => 'Undelivered', 'value' => array_sum($undeliveredParcel));
        $this->goodReturnPieChart[0] = array('caption' => 'Returned', 'value' => array_sum($returnOwe));
        $this->exHubPieChart[0] = array('caption' => 'Average Day Of Hub Received', 'value' => number_format($averageDayOfHubRecived,2),'hubDays'=>'Total Delivered Hub Received','TotalHub'=>$totalHub.' days','TotalShipment'=>$totalShipmentOfHub,'valueWithDays'=>number_format($averageDayOfHubRecived,2).' days');

        foreach ($this->lineChartTransitTime as $key => $data) {
            $this->service_chart[] = array('service' => $key, 'value' => array_sum($data));
        }

    }

    /**
     * Page-specific buttons
     */
    private function defaultPermissionOfReport(){
        $defaulPermission[] = array(
		'key'=> 'date',
		'title' => 'Date'
	);
        $defaulPermission[] =array(
		'key'=> 'tracking_number',
		'title' => 'Tracking Number'
	);
        $defaulPermission[] =array(
		'key'=> 'hawb',
		'title' => 'HAWB'
	);
        $defaulPermission[] = array(
		'key'=> 'city',
		'title' => 'City'
	);
        $defaulPermission[] = array(
		'key'=> 'country',
		'title' => 'Country'
	);
        $defaulPermission[] = array(
		'key'=> 'weight',
		'title' => 'Weight'
	);
        $defaulPermission[] = array(
		'key'=> 'volumetric_weight',
		'title' => 'Volumetric Weight'
	);
        $defaulPermission[] =array(
		'key'=> 'volumetric_liter',
		'title' => 'Volumetric Liter'
	);
        $defaulPermission[] = array(
		'key'=> 'lxwxh',
		'title' => 'LxWxH'
	);
        $defaulPermission[] = array(
		'key'=> 'last_event_tracking_date',
		'title' => 'Last Event Tracking Date'
	);
        $defaulPermission[] = array(
		'key'=> 'status',
		'title' => 'Status'
	);
        $defaulPermission[] = array(
		'key'=> 'tracking_detail',
		'title' => 'Tracking Detail'
	);
        $defaulPermission[] = array(
		'key'=> 'delivery_on_time',
		'title' => 'Delivery On Time'
	);
        $defaulPermission[] = array(
		'key'=> 'delivery_aim_working_days',
		'title' => 'Delivery Aim (Working Days)'
	);
        $defaulPermission[] = array(
		'key'=> 'total_no_of_days_booking_to_hub_received',
		'title' => 'Total No of Days (Booking To Hub Received)'
	);
        $defaulPermission[] = array(
		'key'=> 'total_no_of_days_hub_received_to_carrier_received',
		'title' => 'Total No of Days  (Hub Received to carrier received)'
	); 
        $defaulPermission[] = array(
		'key'=> 'total_no_of_days_from_carrier_received_calendar_days',
		'title' => 'Total No of Days From Carrier Received (Calendar Days)'
	);
        $defaulPermission[] = array(
		'key'=> 'total_no_of_working_days_from_carrier_received',
		'title' => 'Total No of Working Days From Carrier Received'
	);
        $defaulPermission[] = array(
		'key'=> 'total_transit_time_calendar_days',
		'title' => 'Total Transit Time (Calendar Days)'
	);
        $defaulPermission[] = array(
		'key'=> 'total_transit_time_working_days',
		'title' => 'Total Transit Time (Working Days)'
	);
        
        return $defaulPermission;
    }
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
        <link href="../assets/global/plugins/icheck/skins/all.css" rel="stylesheet" type="text/css" />

        <?php
    }

    public function addPagelavelJs() {
        ?>
        <script src="../assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.js" type="text/javascript"></script>
        <script type="text/javascript" src="../assets/global/plugins/select2/js/select2.full.min.js" type="text/javascript"></script>
        <script src="../assets/global/scripts/app.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/bootstrap-select/js/bootstrap-select.min.js" type="text/javascript"></script>

        <script src="../assets/global/scripts/datatable.js" type="text/javascript"></script>
<!--        <script src="../assets/global/plugins/datatables/datatables.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js" type="text/javascript"></script>-->
        <script src="../assets/global/plugins/bootstrap-sweetalert/sweetalert.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js" type="text/javascript"></script>

        <script src="../assets/global/scripts/datatable.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/datatables/datatables.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/icheck/icheck.min.js" type="text/javascript"></script>

        
        <script src="../assets/global/plugins/amcharts/amcharts/amcharts.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/amcharts/amcharts/serial.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/amcharts/amcharts/pie.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/amcharts/amcharts/radar.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/amcharts/amcharts/themes/light.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/amcharts/amcharts/themes/patterns.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/amcharts/amcharts/themes/chalk.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/amcharts/ammap/ammap.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/amcharts/ammap/maps/js/worldLow.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/amcharts/amstockcharts/amstock.js" type="text/javascript"></script>
        <script src="../assets/pages/scripts/charts-amcharts.min.js" type="text/javascript"></script>
        <script type="text/javascript">
            var initbarChartSerivceTransit = function() {
            var chart2 = AmCharts.makeChart("barchart_service", {
            "theme": "light",
                    "type": "serial",
                    "startDuration": 1,
                    "fontFamily": 'Open Sans',
                    "color":    '#888',
        <?php //if (!empty($years) && empty($months)) {    ?>
            "dataProvider":<?php echo json_encode($this->service_chart); ?>,
        <?php //} else if (!empty($years) && !empty($months)) {    ?>
            //                "dataProvider":<?php //echo json_encode(array_values($array_days));  ?>,
        <?php //}    ?>
            "valueAxes": [{
            "position": "left",
                    "axisAlpha": 0,
                    "gridAlpha": 0
            }],
                    "graphs": [{
                    "balloonText": "[[category]]: <b>[[value]]</b>",
                            "colorField": "color",
                            "fillAlphas": 0.85,
                            "lineAlpha": 0.1,
                            "type": "column",
                            "topRadius": 1,
                            "valueField": "value"
                    }],
                    "depth3D": 20,
                    "angle": 30,
                    "chartCursor": {
                    "categoryBalloonEnabled": false,
                            "cursorAlpha": 0,
                            "zoomable": true
                    },
        <?php //if (!empty($years) && empty($months)) {  ?>
            "categoryField": "service",
        <?php //} else if (!empty($years) && !empty($months)) {  ?>
        //                "categoryField": "Day",
        <?php //}  ?>

            "categoryAxis": {
            "gridPosition": "start",
                    "axisAlpha": 0,
                    "gridAlpha": 0,
                    "labelRotation": 10,
            },
                    "exportConfig": {
                    "menuTop": "20px",
                            "menuRight": "20px",
                            "menuItems": [{
                            "icon": '/lib/3/images/export.png',
                                    "format": 'png'
                            }]
                    }
            }, 0);
            }
            initbarChartSerivceTransit();
            var initPieChartDeliveryRatio = function () {
            var chart = AmCharts.makeChart("chart-pie", {
            "type": "pie",
                    "theme": "light",
                    "fontFamily": 'Open Sans',
                    "color": '#888',
                    "depth3D": 30,
                    "angle": "30",
                    "labelRadius": - 35,
                    "labelText": "[[percents]]%",
                    "dataProvider": <?php echo json_encode($this->deliveryRatioPieChart); ?>,
                    "valueField": "value",
                    "titleField": "caption",
                    "radius": '150',
                    "balloonText": "[[balloon]]: [[percents]]% ([[value]])\n[[caption]]",
                    "legend": {
                    "position": "bottom",
                            "marginLeft": 10,
                            "autoMargins": false
                    },
                    "exportConfig": {
                    menuItems: [{
                    icon: '/lib/3/images/export.png',
                            format: 'png'
                    }]
                    }
            });
            $('#chart-pie').closest('.portlet').find('.fullscreen').click(function () {
            chart.invalidateSize();
            });
            };
            initPieChartDeliveryRatio();
            var initPieChartOverView = function () {
            var chart = AmCharts.makeChart("chart-pie-overview", {
            "type": "pie",
                    "theme": "light",
                    "fontFamily": 'Open Sans',
                    "color": '#888',
                    "labelRadius": - 35,
                    "labelText": "[[percents]]%",
                    "depth3D": 30,
                    "dataProvider": <?php echo json_encode($this->deliveryOverViewPieChart); ?>,
                    "valueField": "value",
                    "titleField": "caption",
                    "angle": "30",
                    "radius": '150',
                    "balloonText": "[[balloon]]: [[percents]]% ([[value]])\n[[caption]]",
                    "legend": {
                    "position": "bottom",
                            "marginTop": 1,
                            "autoMargins": false
                    },
                    "exportConfig": {
                    menuItems: [{
                    icon: '/lib/3/images/export.png',
                            format: 'png'
                    }]
                    }

            });
            $('#chart-pie-overview').closest('.portlet').find('.fullscreen').click(function () {
            chart.invalidateSize();
            });
            };
            initPieChartOverView();
            var initChartAverageWeight = function () {
            var chartData = <?php echo json_encode($this->averageWeightbarChart); ?>;
            var chart = AmCharts.makeChart("average_weight_chart", {
            "type": "serial",
                    "theme": "light",
                    "dataProvider": chartData,
                    "valueAxes": [{
                    title: "",
                            "gridColor": "#FFFFFF",
                            "gridAlpha": 0.2,
                            "dashLength": 0
                    }],
                    "gridAboveGraphs": true,
                    "startDuration": 1,
                    "graphs": [{
                    "balloonText": "[[category]]: <b>[[value]]</b>",
                            "fillAlphas": 0.8,
                            "lineAlpha": 0.2,
                            "type": "column",
                            "valueField": "value"
                    }],
                    "chartCursor": {
                    "categoryBalloonEnabled": false,
                            "cursorAlpha": 0,
                            "zoomable": false
                    },
                    "categoryField": "dim",
                    "categoryAxis": {
                    "gridPosition": "start",
                            //"labelRotation": 45,
                            "gridAlpha": 0,
                            "tickPosition": "start",
                            "tickLength": 20
                    },
                    "export": {
                    "enabled": true
                    }

            });
            }
            initChartAverageWeight();
            var initChartGoodReturn = function () {
            var chartData = <?php echo json_encode($this->goodReturnPieChart); ?>;
            var chart = AmCharts.makeChart("good_return_chart", {
            "type": "serial",
                    "theme": "light",
                    "dataProvider": chartData,
                    "valueAxes": [{
                    title: "",
                            "gridColor": "#FFFFFF",
                            "gridAlpha": 0.2,
                            "dashLength": 0
                    }],
                    "gridAboveGraphs": true,
                    "startDuration": 1,
                    "graphs": [{
                    "balloonText": "[[caption]]: <b>[[value]]</b>",
                            "fillAlphas": 0.8,
                            "lineAlpha": 0.2,
                            "type": "column",
                            "valueField": "value"
                    }],
                    "chartCursor": {
                    "categoryBalloonEnabled": false,
                            "cursorAlpha": 0,
                            "zoomable": false
                    },
                    "categoryField": "caption",
                    "categoryAxis": {
                    "gridPosition": "start",
                            //"labelRotation": 45,
                            "gridAlpha": 0,
                            "tickPosition": "start",
                            "tickLength": 20
                    },
                    "export": {
                    "enabled": true
                    }

            });
            }
            initChartGoodReturn();
            var initChartExHub = function () {
            var chartData = <?php echo json_encode($this->exHubPieChart); ?>;
            var chart = AmCharts.makeChart("exHub_chart", {
            "type": "serial",
                    "theme": "light",
                    "dataProvider": chartData,
                    "valueAxes": [{
                    title: "",
                            "gridColor": "#FFFFFF",
                            "gridAlpha": 0.2,
                            "dashLength": 0
                    }],
                    "gridAboveGraphs": true,
                    "startDuration": 1,
                    "graphs": [{
                    "balloonText": "[[caption]]: <b>[[valueWithDays]]</b><br/>[[hubDays]]: <b>[[TotalHub]]</b>",
                            "fillAlphas": 0.8,
                            "lineAlpha": 0.2,
                            "type": "column",
                            "valueField": "value"
                    }],
                    "chartCursor": {
                    "categoryBalloonEnabled": false,
                            "cursorAlpha": 0,
                            "zoomable": false
                    },
                    "categoryField": "caption",
                    "categoryAxis": {
                    "gridPosition": "start",
                            //"labelRotation": 45,
                            "gridAlpha": 0,
                            "tickPosition": "start",
                            "tickLength": 20
                    },
                    "export": {
                    "enabled": true
                    }

            });
            }
            initChartExHub();
            var grid = null;
            var DataTableFun = function () {
            var handleDataTable = function () {
            var toDate = '<?= (!empty($_POST['to_date']) ? '&toDate=' . $_POST['to_date'] : ''); ?>';
            var fromDate = '<?= (!empty($_POST['from_date']) ? '&fromDate=' . $_POST['from_date'] : ''); ?>';
            var search_Carrier_id = '<?= (!empty($_POST['search_Carrier_id']) ? '&search_Carrier_id=' . $_POST['search_Carrier_id'] : ''); ?>';
            var service_id = '<?= (!empty($_POST['service_id']) ? '&service_id=' . $_POST['service_id'] : ''); ?>';
            var search_Code = '<?= (!empty($_POST['search_Code']) ? '&search_Code=' . $_POST['search_Code'] : ''); ?>';
            var country = '<?= (!empty($_POST['country']) ? '&country=' . $_POST['country'] : ''); ?>';
            var user_account_id = '<?= (!empty($_POST['user_account_id']) ? '&user_account_id=' . $_POST['user_account_id'] : ''); ?>';
            var show_shipments = '<?= (!empty($_POST['show_shipments']) ? '&show_shipments=' . $_POST['show_shipments'] : '&show_shipments=all'); ?>';
            var datatableurl = "carrier_performance_report.php?action=tracking_list" + toDate + fromDate + search_Carrier_id + search_Code + country + user_account_id + service_id+show_shipments;
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
                        "url": datatableurl, // ajax source
                                headers: {
                                },
                        },
//                        "language": [
//                            "infoEmpty": "No records available - Got it?",
//                        ],                
//                        "scrollX": false,
//                        "paging":   true,
//                        "info":     false,
//                        "bStateSave": false,
//                        "bProcessing": true,
                        "pageLength": 20, // default record count per page
                        "autoWidth": false,
                        
                        "columns": [
                            {"data": "date_label_created", "bSortable": false, "className": "text-center"},
                            {"data": "tracking_nuumber", "className": "text-center"},
                            {"data": "transit_time", "className": "text-center"},
                            {"data": "tracking_date", "className": "text-center"},
                            {"data": "status", "className": "text-center"},
                            {"data": "tracking_detail", "className": "text-center"},
                            {"data": "delivery_ontime", "className": "text-center"},
                            {"data": "total_days_ex_hubs", "className": "text-center"},
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
            if ($('.date-picker').length > 0) {
            //init date pickers
            $('.date-picker').datepicker({
            autoclose: true
            });
            }
            // $('#manage-data-table button.filter-submit').click();
            var userAccountId = '<?php echo $this->user->getUserAccountId(); ?>';
            var selected = '<?php echo $this->carrierId ?>';
            get_carriers(userAccountId, selected);
            setTimeout(function () {
            $('.amcharts-chart-div a').remove();
            }, 3000);
            $('.download_file').click(function () {
            $('#download_file').val('download_excel');
            $('#admin_form').submit();
            setTimeout(function () {
            $('#download_file').val('');
            }, 3000);
            });

            $('#user_account_id').change(function () {
                var userAccountId = $(this).val();
                get_carriers(userAccountId);
            });
            $('#carriers').change(function () {
                var carrierId = $(this).val();
                change_carriers(carrierId);
            });
            var id = "";
            $(".field_check_box").on('ifChecked', function (event){
                id = $(this).val();  
                $('#' + id + '_field').removeAttr("disabled");
            });
            $(".field_check_box").on('ifUnchecked', function (event) {
               id = $(this).val();
               $('#' + id + '_field').attr("disabled","disabled");
            });
            change_template_btn();
            });
            function get_carriers(userAccountId) {
                var selected_carrier = '<?php echo $this->carrierId; ?>';
                var selected_service = '<?php echo $this->serviceId; ?>';
                $.ajax({
                    type: "POST",
                    url: "carrier_performance_report.php?action=get_carrier_services",
                    data: {user_account_id: userAccountId,selected_carrier:selected_carrier,selected_service:selected_service },
                    dataType: "json",
                    success: function (data) {
                        $('#carriers').html(data.carrier_option);
                        $('#service_id').html(data.services_option);
                        $('#carriers').select2();
                        $('#service_id').select2();
                        $('#carriers').trigger('change');
                    },
                    error: function () {
                        //alert('error handing here');
                    }
                });
            }
            function change_carriers(carrierId) {
                $('.serviceOption').attr("disabled", "disabled");
                $('.carrier_' + carrierId).removeAttr("disabled");
               
                $('#service_id').select2();
            }
            $(document.body).on("change","#service_id",function(){
                if(this.value)
                    $('.filter_report').removeAttr('disabled');
                else
                    $('.filter_report').attr('disabled','disabled');

            });
            <?php if(!empty($this->showReport)){?>
            $('#show_shipments').val('<?= $this->showReport;?>').trigger('change');
            <?php }
            if(!empty($this->serviceId)){
                ?>
                $('.filter_report').removeAttr('disabled');
            <?php }?>
           function save_template() {
                var template_id = $('#template_id').val();
                var form_data = $("#report_template_form").serializeArray();
                $.ajax({
                    type: "POST",
                    url: "carrier_performance_report.php?action=add_template",
                    data: form_data,
                    dataType: "json",
                    success: function (data) {
                        if(data.status == "success") {
                            swal("Success",data.message, "success");
                            if(template_id == "") {
                                // Create the DOM option that is pre-selected by default
                                var newState = new Option(data.title, data.id, true, true);
                                // Append it to the select
                                $("#report_template").append(newState).trigger('change');
                            }
                            change_template_btn();
                        }
                    },
                    error: function () {
                     //alert('error handing here');
                    }
                });
           }
           function change_template_btn() {
               var report_template = $('#report_template').val();
               if(report_template == "") {
                   $('#add_template_modal_btn').show();
                   $('#edit_template_modal_btn').hide();
               } else {
                   $('#edit_template_modal_btn').show();
                   $('#add_template_modal_btn').hide();
               }
           }
           function edit_template_modal() {
                $('.field_check_box').iCheck('uncheck');
                $('.field_title_box').attr("disabled","disabled");
                var report_template = $('#report_template').val();
                var form_data = new FormData();
                form_data.append('report_template', report_template);
                form_data.append('action', 'get_template_data');
                $.ajax({
                        url: "carrier_performance_report.php",
                        cache: false,
                        contentType: false,
                        processData: false,
                        data: form_data,
                        type: 'post',
                        dataType: 'json',
                        success: function (result) {
                            $('#template_id').val(result.data.id);
                            $('#report_title').val(result.data.report_title);
                            var fields = result.data.fields_data;
                            $.each(fields, function (key, data) {
                                $('#'+data['key']+'_check').iCheck('check');
                                $('#'+data['key']+'_field').removeAttr("disabled")
                            });
                            $('#add_template_modal').modal('show');
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
                    Tracking Status Report
                </div>
                <div class="actions">
                    <a href="javascript:{};" class="btn blue download_file"><i class="fa fa-download"></i> Download Excel File</a>
                </div>
            </div>
            <div class="portlet light bordered">
                <div class="portlet-title">
                    <div class="caption">
                        <span id="account_name"></span>Report Filters
                        <!--<span class="caption-helper">distance stats...</span>-->
                    </div>
                </div>
                <?php 
                if(!empty($_POST) && empty($_POST['service_id'])){
                ?>
                    <div class="alert alert-danger">Please select the service.  </div>
                <?php 
                }?>
                <div class="portlet-body">
                    <form class="form-horizontal1" action="" id="admin_form" method="POST" name="admin_form" enctype="multipart/form-data">
                        <div class="row">
                            <?php $colSpan = '3'; 
                                if($this->user->getUserType() != USER::USER_TYPE_CLIENT){ 
                                $colSpan = '3'; 
                            ?>
                            <div class="col-md-<?= $colSpan;?>">
                                <label class="label-account">Show Report </label>
                                <div class="form-group">
                                    <select id="show_shipments" name="show_shipments" class="form-filter bs-select form-control" >
                                        <option value="all">Selected Account &amp; Sub Accounts</option>
                                        <option value="own">Selected Account</option>
                                        <option value="subaccount">Sub Accounts</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-<?= $colSpan;?>">
                                <label class="label-account">Select Account</label>
                                <div class="form-group">
                                    <div id="user_content">
                                        <?php
                                        $accountParentId = 0;
                                        if ($this->user->getUserType() == User::USER_TYPE_CORPORATE) {
                                            $accountParentId = $this->user->getUserAccountId();
                                        }
                                        $selectedAccount = (!empty($this->userAccountId) ? $this->userAccountId : '');
                                        $allowedLevel = 0;
                                        if (Permissions::checkFilePermission('hide_subaccount')) {
                                            $allowedLevel = 1;
                                        }
                                        ?>
                                        <?php echo Ddl::showTreeDropdown('user_account_id', 'user_account', 'user_account', 'id', $accountParentId, array("active_flag = '1'"), $selectedAccount, "Please Select Account", 'class="form-filter bs-select form-control" data-live-search="true"', "", "", 'logo', '../images/userlogo/thumbnail/', 'owe_16_', true,$allowedLevel); ?>
                                    </div>
                                </div>
                            </div>
                           
                            <div class="col-md-<?php echo $colSpan; ?>">
                                <label class="label-account">Select Carrier</label>
                                <div class="form-group">
                                    <div id="carriers_div">
                                        <?php echo Ddl::generateArrayDDL('search_Carrier_id', array("" => "Select Carrier"), $this->carrierId, '', 'class="form-filter select2 form-control" ', "", 'carriers'); ?>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-<?php echo $colSpan; ?>">
                                <label class="label-account">Select Service</label>
                                <div class="form-group">
                                    <div id="service_div">
                                        <?php echo Ddl::generateArrayDDL('service_id', array("" => "Select Service"), '', '', ' rel="tooltip" title="Select Service" class="form-filter select2 form-control" ', "", $dd_id = 'service_id'); ?>
                                    </div>
                                </div>
                            </div>
                        </div>                        
                        <div class="row">
                            <div class="col-md-<?php echo $colSpan; ?>">
                                <div class="form-group">
                                    <label >Country</label>
                                    <div class="first_form_col">
                                        <div class="input-group">
                                            <div class="input-group-addon"> <i class="fa fa-flag"></i> </div>
                                            <?php
                                            $country = (!empty($this->countryId) ? $this->countryId : '');
                                            echo Ddl::generateCountryDDL('country', $country, 'iso', 'class="form-filter bs-select form-control" data-live-search="true" data-container="body" data-size="8"');
                                            ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-<?php echo $colSpan; ?>">
                                <label class="control-label">Date Ranges </label>
                                <!--                                <div class="input-group">-->
                                <div class="input-group date-picker input-daterange" data-date="20/01/2018" data-date-format="mm/dd/yyyy">
                                    <input type="text" class="form-control" name="from_date" id="from" value="<?= (!empty($_POST['from_date']) ? $_POST['from_date'] : ''); ?>" data-original-title="" title="">
                                    <span class="input-group-addon"> to </span>
                                    <input type="text" class="form-control" name="to_date" id="to" value="<?= (!empty($_POST['to_date']) ? $_POST['to_date'] : ''); ?>" data-original-title="" title=""> 
                                    <input type="hidden" class="" name="download_file" id="download_file" value=""> 
                                </div>
                                <!--                                </div>-->
                            </div> 
                            <div class="col-md-<?php echo $colSpan; ?>">
                                <label class="control-label">Template</label>
                                <?php 
                                    $reportCustomizeSettingsFilter = new ReportCustomizeSettingsFilter();
                                    $reportCustomizeSettingsFilter->where(['rcs.account_id' => $this->user->getUserAccountId(),'rcs.report_key' => "tracking_status_report"]);
                                    $reportCustomizeSettingsFilterObjs = $reportCustomizeSettingsFilter->getList();
                                    $array = [];
                                    if(count($reportCustomizeSettingsFilterObjs) > 0) {
                                        $array[''] = "Default";
                                        foreach($reportCustomizeSettingsFilterObjs as $reportCustomizeSettingsFilterObj) {
                                            $array[$reportCustomizeSettingsFilterObj->getId()] = $reportCustomizeSettingsFilterObj->getReportTitle();
                                        }
                                    }
                                    echo Ddl::generateArrayDDL('report_template', $array, '', '', ' rel="tooltip" title="Select Service" class="form-filter select2 form-control" onchange="change_template_btn()" ', "", 'report_template');
                                ?>
                            </div>
                            <div class="col-md-<?php echo $colSpan; ?>">
                                <label class="control-label">&nbsp;</label><br>
                                <?php if(Permissions::checkFilePermission('user_report_settings')) {  ?>
                                <button class="btn btn-primary" type="button" id="edit_template_modal_btn" onclick="edit_template_modal()" >Edit Template</button>
                                <?php } ?>
                                <button class="btn btn-primary filter_report" type="submit" disabled="disabled">Filter Report</button>
                            </div>
                        </div>
                         <?php }else if($this->user->getUserType() == USER::USER_TYPE_CLIENT){ ?>
                        <div class="row">
                            <div class="col-md-3">
                                <label class="label-account">Select Carrier</label>
                                <div class="form-group">
                                    <div id="carriers_div">
                                        <?php echo Ddl::generateArrayDDL('search_Carrier_id', array("" => "Select Carrier"), $this->carrierId, '', 'class="form-filter select2 form-control" ', "", 'carriers'); ?>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label class="label-account">Select Service</label>
                                <div class="form-group">
                                    <div id="service_div">
                                        <?php echo Ddl::generateArrayDDL('service_id', array("" => "Select Service"), '', '', ' rel="tooltip" title="Select Service" class="form-filter select2 form-control" ', "", $dd_id = 'service_id'); ?>
                                    </div>
                                </div>
                            </div>                            
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label >Country</label>
                                    <div class="first_form_col">
                                        <div class="input-group">
                                            <div class="input-group-addon"> <i class="fa fa-flag"></i> </div>
                                            <?php
                                            $country = (!empty($this->countryId) ? $this->countryId : '');
                                            echo Ddl::generateCountryDDL('country', $country, 'iso', 'class="form-filter bs-select form-control" data-live-search="true" data-container="body" data-size="8"');
                                            ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label class="control-label">Date Ranges </label>
                                <!--                                <div class="input-group">-->
                                <div class="input-group date-picker input-daterange" data-date="20/01/2018" data-date-format="mm/dd/yyyy">
                                    <input type="text" class="form-control" name="from_date" id="from" value="<?= (!empty($_POST['from_date']) ? $_POST['from_date'] : ''); ?>" data-original-title="" title="">
                                    <span class="input-group-addon"> to </span>
                                    <input type="text" class="form-control" name="to_date" id="to" value="<?= (!empty($_POST['to_date']) ? $_POST['to_date'] : ''); ?>" data-original-title="" title=""> 
                                    <input type="hidden" class="" name="download_file" id="download_file" value=""> 
                                </div>
                                <!--                                </div>-->
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 text-center">
                                <label class="control-label">&nbsp;</label><br>
                                <button class="btn btn-primary filter_report" type="submit" disabled="disabled">Filter Report</button>
                            </div>
                        </div>
                        <?php }?>
                    </form>
                </div>
            </div>
        </div>
        <div class="portlet light bordered">
            <div class="portlet-body">
                <div class="tabbable-line">
                    <ul class="nav nav-tabs">
                        <li class="active">
                            <a href="#graphichal_view" data-toggle="tab">Graphical View</a>
                        </li>
                        <li>
                            <a href="#tabular" data-toggle="tab">Tabular</a>
                        </li>
<!--                        <li>
                            <a href="#tranist_tab" data-toggle="tab">Transit Time</a>
                        </li>-->
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane active" id="graphichal_view">
                            <div class="row">
                                <div class="col-md-12 col-xs-12 col-sm-12">
                                    <div class="portlet light bordered">
                                        <div class="portlet-title">
                                            <div class="caption ">
                                                <span class="caption-subject font-dark bold uppercase">Average Weights and Dimensions</span>
                                            </div>
                                            <div class="actions">
                                                <a class="btn btn-circle btn-icon-only btn-default fullscreen" href="#"> </a>
                                            </div>
                                        </div>
                                        <div class="portlet-body">
                                            <div id="average_weight_chart" class="CSSAnimationChart"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 col-xs-12 col-sm-12">
                                    <div class="portlet light bordered">
                                        <div class="portlet-title">
                                            <div class="caption ">
                                                <span class="caption-subject font-dark bold uppercase">Good Returned</span>
                                            </div>
                                            <div class="actions">
                                                <a class="btn btn-circle btn-icon-only btn-default fullscreen" href="#"> </a>
                                            </div>
                                        </div>
                                        <div class="portlet-body">
                                            <div id="good_return_chart" class="CSSAnimationChart"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 col-xs-12 col-sm-12">
                                    <div class="portlet light bordered">
                                        <div class="portlet-title">
                                            <div class="caption ">
                                                <span class="caption-subject font-dark bold uppercase">Average Hub Received Days</span>
                                            </div>
                                            <div class="actions">
                                                <a class="btn btn-circle btn-icon-only btn-default fullscreen" href="#"> </a>
                                            </div>
                                        </div>
                                        <div class="portlet-body">
                                            <div id="exHub_chart" class="CSSAnimationChart"></div>
                                        </div>
                                    </div>
                                </div>

                            </div>                             
                            <div class="row">
                                <div class="col-md-6 col-xs-12 col-sm-12">
                                    <!-- BEGIN CHART PORTLET-->
                                    <div class="portlet light bordered">
                                        <div class="portlet-title">
                                            <div class="caption">
                                                
                                                <span class="caption-subject font-dark bold uppercase"> Delivery Ratio</span>
                                            </div>
                                            <div class="tools">
                                                <a href="javascript:;" class="fullscreen"> </a>
                                            </div>
                                        </div>
                                        <div class="portlet-body">
                                            <div id="chart-pie" class="chart"> </div>
                                        </div>
                                    </div>
                                    <!-- END CHART PORTLET-->
                                </div>
                                <!--            </div>
                                            <div class="row">-->
                                <div class="col-md-6 col-xs-12 col-sm-12">
                                    <!-- BEGIN CHART PORTLET-->
                                    <div class="portlet light bordered">
                                        <div class="portlet-title">
                                            <div class="caption">
                                                
                                                <span class="caption-subject font-dark bold uppercase"> Delivery Overview</span>
                                            </div>
                                            <div class="tools">
                                                <a href="javascript:;" class="fullscreen"> </a>
                                            </div>
                                        </div>
                                        <div class="portlet-body">
                                            <div id="chart-pie-overview" class="chart" style="height: 300px;"> </div>
                                        </div>
                                    </div>
                                    <!-- END CHART PORTLET-->
                                </div>
                            </div>                                                    


                        </div>
                        <div class="tab-pane" id="tabular">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="portlet light bordered">
                                        <div class="portlet-title">
                                            <div class="tools"> </div>
                                        </div>
                                       <div class="table-container margin-top-10">
                                            <table class="table table-striped table-bordered table-hover" id="manage-data-table">
                                                <thead>
                                                    <tr role="row" class="">
                                                        <th width="100">Date</th>
                                                        <th>Tracking No</th>
                                                        <th width="100">Delivery Aim (Working Days)</th>
                                                        <th width="100">Last Event Tracking Date</th>
                                                        <th>Status</th>
                                                        <th>Tracking Detail</th>
                                                        <th>Delivery On Time</th>
                                                        <th width="125">Total No of Days <br/>(Hub Received)<br>(Carrier Received)<br>(Delivered)</th>
                                                    </tr>
                                                </thead>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane" id="tranist_tab">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="portlet light bordered">
                                        <div class="portlet-title">
                                            <div class="caption">
                                                <i class="icon-bar-chart font-green-haze"></i>
                                                Average Transit Time
                                            </div>
                                            <div class="tools">
                                                <a href="javascript:;" class="collapse"></a>
                                                <a href="javascript:;" class="fullscreen"></a>
                                            </div>
                                        </div>
                                        <div class="portlet-body">
                                            <div id="barchart_service" class="chart" style="height: 400px;"></div>   
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
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
            .dataTables_wrapper .dataTables_processing{
                position: absolute;
                top: 60%;
                left: 50%;
            }
            #select2-service_id-results .select2-results__option[aria-disabled=true] {
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