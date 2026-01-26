<?php
// get settings
require_once("../includes/settings/config.inc.php");
require_once("../Classes/PHPExcel.php");

include_classes([   
                  'ivisualcomponent','ddl.inc'
                ],'library');

include_classes([  
                    'invoices.class',
                    'invoicesfilter.class',
                    'agentdata.class',
                    'agentdatafilter.class',
                    'iaddress.class',
                    'carrier.class',
                    'carrierfilter.class',
                    'consignment.class',
                    'consignmentfilter.class',
                    'services.class' ,
                    'servicefilter.class',
                    'userservicesrouting.class',
                    'userservicesroutingfilter.class'
                        ]);
class Page extends BasePage {
    /*     * *
     * Controller logic
     */

    private $filerColumn = '*';
    private $params = "";
    private $barCountryTransitTime = array();
    private $headerStyle;
    private $styleForReport;
    private $carrierId = '';
    private $serviceId = '';
    private $countryId = '';
    private $userAccountId = '';
    private $getUserAccountId = '';
    private $service_chart = array();
    private $barChartForCountry = array();
    private $barCountryName = array();
    private $userCorporate = 0;
    private $allUserCarrierId = array();
    private $allUserServicesId = array();
    private $noRecordFound = '';
    private $dateType = '';

    
    protected function init() {

        $this->breadCrumb['data'] = array(
            'index.php' => Translation::GetCaption("HOME"),
            'label_generation_report.php' => "Label Generation Report"
        );

        $this->user = SessionManager::getUser();
       
        $allouedAcccounts = [];
        $selectedAccountName = "";
        if ($this->user->getUserType() == USER::USER_TYPE_CORPORATE) {
            $this->userCorporate = $this->user->getId();
        }
        
        $user_account_id = (!empty($this->form_vars['user_account_id'])? $this->form_vars['user_account_id']: $this->user->getUserAccountId());
        $selectAccountSearchType = $this->form_vars['selectAccountSearchType'];
        if ($this->user->getUserType() == USER::USER_TYPE_CLIENT) {
            $selectAccountSearchType = "own";
        } else {
            $selectAccountSearchType = $this->form_vars['selectAccountSearchType'];
        }
        if (!empty($user_account_id)) {
            if ($selectAccountSearchType == 'all') {
                $allouedAcccounts = CustomerAccount::accountSubAccount($user_account_id, 0, true);
            } else if ($selectAccountSearchType == 'own') {
                $allouedAcccounts = [$user_account_id];
            } else if ($selectAccountSearchType == 'subaccount') {
                $allouedAcccounts = CustomerAccount::accountSubAccount($user_account_id, 0, false);
            }         
              $userAccountObj = new CustomerAccount($user_account_id);
              $selectedAccountName  = $userAccountObj->getUserAccount();       
        }
        
        $data = Carrier::getCarriersServersFromUserAccount($user_account_id,true); // getting all services and carriers that are assigned to that user 
        $this->allUserCarrierId = $data['carrierIds'];
        $this->allUserServicesId = $data['serviceIds'];
        /*         * DataTable handlings
         */
        // Select Carrier Drop Down ///
        if (isset($_GET['action']) && $_GET['action'] == "get_carrier_services") {
            $user_account_id = $this->form_vars['user_account_id'];
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
                $services = $serviceFilterObj->getColumnList("ser.id, ser.name, ser.code, ser.carrier_id");
            }
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
                $service_option .= "<option value='" . $sr->getId() . "' class='serviceOption carrier_" . $sr->getCarrierId() . "'>" . $sr->getName() . "</option>";
            }
            $carrier_option = "<option value=''>Select Carrier</option>";
            foreach ($carriers as $cr) {
                $carrier_option .= "<option value='" . $cr->getId() . "'>" . $cr->getCarrierDisplayName() . "</option>";
            }

            $return['services_option'] = $service_option;
            $return['carrier_option'] = $carrier_option;
//            $return['agent_option'] = $agent_option;
            echo json_encode($return);
            die;
        }
        // End Select Carrier Drop Down ///
        
        $this->serviceId = (!empty($_POST['service_id']) ? $_POST['service_id'] : '');
        $this->carrierId = (!empty($_POST['search_Carrier_id']) ? $_POST['search_Carrier_id'] : '');
        $fromDate = (!empty($_POST['from_date']) ? date('Y-m-d', strtotime($_POST['from_date'])) : date('Y-m-d', strtotime('-30 days')));
        $toDate = (!empty($_POST['to_date']) ? date('Y-m-d', strtotime($_POST['to_date'])) : date('Y-m-d'));
        $dateFrom = date_create($fromDate);
        $dateTo = date_create($toDate);
        $diffLabelReport = date_diff($dateFrom, $dateTo);
        if($diffLabelReport->days <= '30')
        {
            $this->dateType = (!empty($_POST['date_type'])?$_POST['date_type']:'');
            if (isset($_POST['download_file']) && $_POST['download_file'] == "download_excel") {
                $cols = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z','AA','AB','AC','AD','AE','AF','AG','AH','AI','AJ','AK','AL','AM','AN','AO','AP','AQ','AR','AS','AT','AU','AV','AW','AX','AY','AZ','BA','BB','BC','BD','BE','BF','BG','BH','BI','BJ','BK','BL','BM','BN','BO','BP','BQ','BR','BS','BT','BU','BV','BW','BX','BY','BZ','CA','CB','CC','CD','CE','CF','CG','CH','CI','CJ','CK','CL','CM','CN','CO','CP','CQ','CR','CS','CT','CU','CV','CW','CX','CY','CZ','DA','DB','DC','DD','DE','DF','DG','DH','DI','DJ','DK','DL','DM','DN','DO','DP','DQ'];
                $objPHPExcel = new PHPExcel();
                $objSheetData = $objPHPExcel->getActiveSheet();
                $objSheetData->setTitle('Label Created Report');
                $FontBoldArray = array(
                    'font' => array(
                        'bold' => true,
                        'size' => 12,
                        'name' => 'Calibri',
                    ),
                     'alignment' => array(
                        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                        'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER)
                );
                $boldFont = array(
                    'font' => array(
                        'bold' => true,
                        'size' => 14,
                        'name' => 'Calibri',
                    )
                );
                $headingArray = array(
                    'font' => array(
                        'bold' => true,
                        'size' => 20,
                        'name' => 'Calibri',
                    )
                );
                $scondHeadingArray = array(
                    'font' => array(
                        'bold' => true,
                        'size' => 13,
                        'name' => 'Calibri',
                    )
                );
                $objPHPExcel->getActiveSheet()->getStyle('A3:B3')->applyFromArray($FontBoldArray);
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A1','Label Generated Report ('.$fromDate.' To '.$toDate.' )');
                $dateTypeArr= array(
                  'Filtered By: Date Created' => 'dateCreated',
                  'Filtered By: Date Scanned' => 'dateScanned',
                  'Filtered By: Date Delivered' => 'dateDelivered',
                  'Filtered By: Date Dispatched' => 'dateBooked',
                  'Filtered By: Date Label Created' => 'dateLabelCreated',
                );
                $LabelOfFilter = array_search($this->dateType, $dateTypeArr);
                if(!empty($selectedAccountName)){
                    $LabelOfFilter .= "\nAccount: ".$selectedAccountName; 
                }
                 if(!empty($this->carrierId)){
                    $carrierObj  = new Carrier($this->carrierId);
                     $LabelOfFilter .= "\nCarrier: ".$carrierObj->getCarrier();
                 }
                $objPHPExcel->getActiveSheet()->getStyle('A2')->getAlignment()->setWrapText(true);
                $objPHPExcel->getActiveSheet()->getRowDimension('1')->setRowHeight(-1);  
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A2',$LabelOfFilter);
                $objPHPExcel->getActiveSheet()->getStyle('A1')->applyFromArray($headingArray);
                $objPHPExcel->getActiveSheet()->getStyle('A2')->applyFromArray($scondHeadingArray);
                $TextLeftArray = array(
                    'font' => array(
                        'size' => 12,
                        'name' => 'Calibri',
                    ),
                     'alignment' => array(
                        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                        'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER)            
                );
                $centerStyle = array(
                    'alignment' => array(
                        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
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
                $objPHPExcel->getActiveSheet()->getStyle('A3:B3')->applyFromArray($FontBoldArray);
                $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('A3', 'Service Type')
                        ->setCellValue('B3', 'Service Code');

                $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(30);
                $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(30);            

                $consignmentObj = new ConsignmentFilter();
                $createdLabelDataReportObj = $consignmentObj->getLabelCreatedReport($fromDate,$toDate, $allouedAcccounts, $this->carrierId,  $this->serviceId,$user_account_id,  $this->dateType);
                $serviceName = [];
                $dataDateArr = [];
                $DateArr = [];
                $newArr = [];
                $arrDate = [];
                $createLabelReportArr = [];
                if(!empty($createdLabelDataReportObj)){            
                    foreach($createdLabelDataReportObj as $key => $data){                
                        $createLabelReportArr[$data->getCode()][$data->getDateLabelCreated()] = array(
                            'serviceName' => $data->getServiceName(),
                            'serviceCode' => $data->getCode(),
                            'weight' => $data->getWeight(),
                            'totalShipment' => $data->getId(),
                            'date' => $data->getDateLabelCreated()
                        );
                        $serviceName[$data->getCode()]  = $data->getServiceName();
                    }
                    $dateRange = [];
                    if(!empty($fromDate)){
                        $begin = new DateTime($fromDate);
                    }

                    if(!empty($toDate)){
                        $end = new DateTime($toDate);
                        $end = $end->modify( '+1 day' );
                    }

                    $period = new DatePeriod(
                        $begin,
                        new DateInterval('P1D'),
                        $end
                    );
                    // Date 
                    foreach ($period as $key => $value) {
                        $DateArr[] = $value->format('d-m-Y');

                    }
                    // End
                    $finalArray  = [];
                    $totalAllserivceArr = [];


                    foreach($createLabelReportArr as $serviceCode => $dataArr){                
                        foreach ($period as $key => $value) {
                            $dateVal = $value->format('Y-m-d');   
                            if(!isset($dataArr[$dateVal])){
                                $finalArray[$serviceCode][$dateVal] = array(
                                    'serviceName' => $serviceName[$serviceCode],
                                    'serviceCode' => $serviceCode,
                                    'weight' => 0.00,
                                    'totalShipment' => 0,
                                    'date' => $dateVal
                                );

                             }else{
                                $finalArray[$serviceCode][$dateVal] = $dataArr[$dateVal];
                            }
                        }
                    }

                //exit;
                    $objPHPExcel->getActiveSheet()->getStyle('A3:CT3')->applyFromArray($styleForReport);
                    $startColIndex = 2;
                    $step = 2;
                    $dataStartRow = 2; 
                    $headingIndex = 2;
                    $isFirst = true;
                    $cellArr = [];
                    //$count = count($DateArr);
                    foreach($DateArr as $key => $data){
                        if($isFirst){
                            $isFirst = false;
                        }
                        //$headingIndex = $startColIndex;
                        $objPHPExcel->getActiveSheet()->mergeCells($cols[$startColIndex] . $dataStartRow. ":" . $cols[($startColIndex + $step)] . $dataStartRow);
                        $objPHPExcel->getActiveSheet()->getStyle($cols[$startColIndex] . $dataStartRow)->applyFromArray($FontBoldArray);
                        $objPHPExcel->getActiveSheet()->setCellValue($cols[$startColIndex] . $dataStartRow, $data);

                        $startColIndex += ($step + 1);
                        $objPHPExcel->getActiveSheet()->setCellValue($cols[$headingIndex] . ($dataStartRow + 1), "Number Of Shipment");
                        $objPHPExcel->getActiveSheet()->getColumnDimension($cols[$headingIndex])->setWidth(25);
                        $objPHPExcel->getActiveSheet()->getStyle($cols[$headingIndex] . ($dataStartRow + 1))->applyFromArray($centerStyle);
                        $cellArr[] = $cols[$headingIndex];
                        $headingIndex = $headingIndex + 1;
                        $objPHPExcel->getActiveSheet()->setCellValue($cols[$headingIndex] . ($dataStartRow + 1), "Total Weight");
                        $objPHPExcel->getActiveSheet()->getColumnDimension($cols[$headingIndex])->setWidth(25);
                        $objPHPExcel->getActiveSheet()->getStyle($cols[$headingIndex] . ($dataStartRow + 1))->applyFromArray($centerStyle);
                        $cellArr[] = $cols[$headingIndex];
                        $headingIndex = $headingIndex + 1;
                        $objPHPExcel->getActiveSheet()->setCellValue($cols[$headingIndex] . ($dataStartRow + 1), "Average Weight");
                        $objPHPExcel->getActiveSheet()->getColumnDimension($cols[$headingIndex])->setWidth(25);
                        $objPHPExcel->getActiveSheet()->getStyle($cols[$headingIndex] . ($dataStartRow + 1))->applyFromArray($centerStyle);
                        $cellArr[] = $cols[$headingIndex];
                        $headingIndex = $headingIndex + 1;
                    }
                    $startTotalindex = end(array_keys($cellArr));
                    $startTotalindex = $startTotalindex + 3;
                    $cellForTotalCal = [];
                    for( $i = 1; $i <= 1; $i++ ){
                        $objPHPExcel->getActiveSheet()->setCellValue($cols[$startTotalindex] . ($dataStartRow + 1), "Total Shipment Of Particular Service");
                        $objPHPExcel->getActiveSheet()->getColumnDimension($cols[$startTotalindex])->setWidth(38);
                        $objPHPExcel->getActiveSheet()->getStyle($cols[$startTotalindex] . ($dataStartRow + 1))->applyFromArray($FontBoldArray);
                        $cellForTotalCal[] = $cols[$startTotalindex];
                        $startTotalindex = $startTotalindex + 1;
                        $objPHPExcel->getActiveSheet()->setCellValue($cols[$startTotalindex] . ($dataStartRow + 1), "Total Weight Of Particular Service");
                        $objPHPExcel->getActiveSheet()->getColumnDimension($cols[$startTotalindex])->setWidth(38);
                        $objPHPExcel->getActiveSheet()->getStyle($cols[$startTotalindex] . ($dataStartRow + 1))->applyFromArray($FontBoldArray);
                        $cellForTotalCal[] = $cols[$startTotalindex];
                        $startTotalindex = $startTotalindex + 1;
                        $objPHPExcel->getActiveSheet()->setCellValue($cols[$startTotalindex] . ($dataStartRow + 1), "Total Avg Weight Of Particular Service");
                        $objPHPExcel->getActiveSheet()->getColumnDimension($cols[$startTotalindex])->setWidth(38);
                        $objPHPExcel->getActiveSheet()->getStyle($cols[$startTotalindex] . ($dataStartRow + 1))->applyFromArray($FontBoldArray);
                        $cellForTotalCal[] = $cols[$startTotalindex];
                        $startTotalindex = $startTotalindex + 1;
                    }
                    // Service Name and Service Code

                    $dataStartRows = 4;
                    $calculateTotalweightService = [];     
                    $calculateTotalAvgWeightService = [];     
                    $calculateTotalShipmentService = [];  
                    $startTotalindex = end(array_keys($cellArr));
                    $startTotalindex = $startTotalindex + 3;
                    $dayTotals = [];
                    $cell = [];
                    $TotalOfAllShipmentService = 0;
                    $TotalOfAllShipmentWeight = 0;
                    $TotalOfAllShipmentAvgWeight = 0;
                    $totalCellService;
                    foreach($finalArray as $serviceCode => $data){
                        $avgWeight = 0;
                        $alphabeticIndex = 0;
                        $serviceTotalShipments = 0;
                        $serviceTotalWeight = 0;
                        $serviceAvgWeight = 0;

                        $objPHPExcel->getActiveSheet()->setCellValue($cols[$alphabeticIndex++] . ($dataStartRows), $serviceName[$serviceCode]);
                        $cell[] = ($dataStartRows);
                        $objPHPExcel->getActiveSheet()->setCellValue($cols[$alphabeticIndex++] . ($dataStartRows), $serviceCode);

                        foreach($data as $ind => $value){
                            $serviceTotalShipments += $value['totalShipment'];
                            $serviceTotalWeight += $value['weight'];
                            if($value['totalShipment'] > 0)
                                $serviceAvgWeight = $value['weight'] / $value['totalShipment'];

                            //die();
                            $objPHPExcel->getActiveSheet()->setCellValue($cols[$alphabeticIndex] . ($dataStartRows), $value['totalShipment']);
                            $objPHPExcel->getActiveSheet()->getStyle($cols[$alphabeticIndex] . ($dataStartRows))->applyFromArray($centerStyle);

                            $alphabeticIndex++;

                            $objPHPExcel->getActiveSheet()->setCellValue($cols[$alphabeticIndex] . ($dataStartRows), $value['weight']);
                            $objPHPExcel->getActiveSheet()->getStyle($cols[$alphabeticIndex] . ($dataStartRows))->applyFromArray($centerStyle);

                            $alphabeticIndex++;

                            $avgWeight =(!empty($value['weight']) && !empty($value['totalShipment']))? round($value['weight'] / $value['totalShipment'],2):'0.00';

                            $objPHPExcel->getActiveSheet()->setCellValue($cols[$alphabeticIndex] . ($dataStartRows), round($avgWeight,2),2);
                            $objPHPExcel->getActiveSheet()->getStyle($cols[$alphabeticIndex] . ($dataStartRows))->applyFromArray($centerStyle);

                            $alphabeticIndex++;

                            if(isset($dayTotals[$ind])){
                                $dayTotals[$ind]['dayTotalShipments'] += $value['totalShipment'];
                                $dayTotals[$ind]['dayTotalWeight'] += $value['weight'];
                            }else{
                                $dayTotals[$ind]['dayTotalShipments'] = $value['totalShipment'];
                                $dayTotals[$ind]['dayTotalWeight'] = $value['weight'];
                            }
                        }
                        $TotalOfAllShipmentService += $serviceTotalShipments;
                        $TotalOfAllShipmentWeight += $serviceTotalWeight;
                        $TotalOfAllShipmentAvgWeight += $serviceAvgWeight;
                        $objPHPExcel->getActiveSheet()->setCellValue($cols[$alphabeticIndex] . ($dataStartRows), $serviceTotalShipments);
                        $objPHPExcel->getActiveSheet()->getStyle($cols[$alphabeticIndex] . ($dataStartRows))->applyFromArray($centerStyle);
                        $totalCellService[] = $cols[$alphabeticIndex];
                        $objPHPExcel->getActiveSheet()->setCellValue($cols[++$alphabeticIndex] . ($dataStartRows), round($serviceTotalWeight,2));
                        $objPHPExcel->getActiveSheet()->getStyle($cols[$alphabeticIndex] . ($dataStartRows))->applyFromArray($centerStyle);
                        $totalCellService[] = $cols[$alphabeticIndex];
                        $objPHPExcel->getActiveSheet()->setCellValue($cols[++$alphabeticIndex] . ($dataStartRows), round($serviceAvgWeight,2));
                        $objPHPExcel->getActiveSheet()->getStyle($cols[$alphabeticIndex] . ($dataStartRows))->applyFromArray($centerStyle);
                        $totalCellService[] = $cols[$alphabeticIndex];
                        $dataStartRows++;            
                    }
                    $totalCellService = array_unique($totalCellService);
                    $alphaIndexOne = $totalCellService[0];
                    $alphaIndexTwo = $totalCellService[1];
                    $alphaIndexThree = $totalCellService[2];
                    $objPHPExcel->getActiveSheet()->setCellValue($alphaIndexOne . ($dataStartRows), $TotalOfAllShipmentService);
                    $objPHPExcel->getActiveSheet()->getStyle($alphaIndexOne . ($dataStartRows))->applyFromArray($centerStyle)->applyFromArray($boldFont);
                    $objPHPExcel->getActiveSheet()->setCellValue($alphaIndexTwo . ($dataStartRows), round($TotalOfAllShipmentWeight,2));
                    $objPHPExcel->getActiveSheet()->getStyle($alphaIndexTwo . ($dataStartRows))->applyFromArray($centerStyle)->applyFromArray($boldFont);
                    $objPHPExcel->getActiveSheet()->setCellValue($alphaIndexThree . ($dataStartRows), round($TotalOfAllShipmentAvgWeight,2));
                    $objPHPExcel->getActiveSheet()->getStyle($alphaIndexThree . ($dataStartRows))->applyFromArray($centerStyle)->applyFromArray($boldFont);

                    $Total = end($cell);
                    $Total = $Total + 1;
                    $objPHPExcel->getActiveSheet()->setCellValue('B'.$Total, 'Total');
                    $objPHPExcel->getActiveSheet()->getStyle('B'.$Total)->applyFromArray($boldFont);
                    $alphabeticIndex = 1;

                    foreach($dayTotals as $date => $dateTotals){
                        $dayTotalWeight = $dateTotals['dayTotalWeight'];
                        $dayTotalShipments = $dateTotals['dayTotalShipments'];                
                        $dayAvgWeight = 0;
                        if($dayTotalShipments > 0)
                            $dayAvgWeight = $dayTotalWeight/$dayTotalShipments;

                        $objPHPExcel->getActiveSheet()->setCellValue($cols[++$alphabeticIndex] . ($dataStartRows), $dayTotalShipments);
                        $objPHPExcel->getActiveSheet()->getStyle($cols[$alphabeticIndex] . ($dataStartRows))->applyFromArray($centerStyle)->applyFromArray($boldFont);

                        $objPHPExcel->getActiveSheet()->setCellValue($cols[++$alphabeticIndex] . ($dataStartRows), round($dayTotalWeight,2));
                        $objPHPExcel->getActiveSheet()->getStyle($cols[$alphabeticIndex] . ($dataStartRows))->applyFromArray($centerStyle)->applyFromArray($boldFont);

                        $objPHPExcel->getActiveSheet()->setCellValue($cols[++$alphabeticIndex] . ($dataStartRows), round($dayAvgWeight,2));
                        $objPHPExcel->getActiveSheet()->getStyle($cols[$alphabeticIndex] . ($dataStartRows))->applyFromArray($centerStyle)->applyFromArray($boldFont);
                    }


        //             die();



                    // End
                    header('Content-Type: application/vnd.ms-excel');
                    header('Content-Disposition: attachment;filename=label-generated-report' . date('d-m-Y') . '.xlsx'); // file name of excel
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
                }else{
                    $this->noRecordFound = 'No record found to download';
                }
            }
        }else{
             $this->noRecordFound = 'Date ranges filter must be less than and euqal to 30 days. ';
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
        <script src="../assets/global/plugins/bootstrap-sweetalert/sweetalert.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js" type="text/javascript"></script>
        <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
        <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

        <script src="../assets/global/scripts/datatable.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/datatables/datatables.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js" type="text/javascript"></script>


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
            $(document).ready(function () {
                //DataTableFun.init();
                if ($('.date-picker').length > 0) {
                    //init date pickers
                    var today = new Date();
                    $('.date-picker').datepicker({
                        format: 'dd-mm-yyyy',
                        autoclose:true,
                        endDate: 'today',
                        maxDate: today
                    }).on('changeDate', function (ev) {
                       $(this).datepicker('hide');
                    });

                }
                
                var userAccountId = '<?php echo $this->user->getUserAccountId(); ?>';
                var selected = '<?php echo $this->carrierId ?>';
                get_carriers(userAccountId, selected);
                
              
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
                
                   setTimeout(function () {
                    $('.msg').remove();
                }, 20000);
                
            });

            function get_carriers(userAccountId) {
                $.ajax({
                    type: "POST",
                    url: "label_generation_report.php?action=get_carrier_services",
                    data: {user_account_id: userAccountId},
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
                $('#service_id').val([]);
                $('#service_id').select2();
                
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
                    Label Generation Report
                </div>
            </div>
            <div class="portlet light bordered">
                               
                <div class="portlet-title">
                    <div class="caption">
                      <span id="account_name"></span>Report Filters
                    </div>
                </div>
                <?php if(!empty($this->noRecordFound)){?>
                <div class="col-md-12 alert alert-danger msg">
                    <?php echo $this->noRecordFound;?>
                </div>
                <?php }?> 
                <div class="portlet-body">
                    <form class="form-horizontal1" action="" id="admin_form" method="POST" name="admin_form" enctype="multipart/form-data">
                       
                        <div class="row">
                            <?php $colSpan = '6'; if($this->user->getUserType() != USER::USER_TYPE_CLIENT){ 
                                $colSpan = '3'; 
                            ?>
                            <div class="col-md-<?php echo $colSpan; ?>">
                                <label class="label-account">Show Shipment </label>
                                <div class="form-group">
                                    <?php
                                    $showShipments = array(
                                        'all' => "Selected Account & Sub Accounts",
                                        'own' => "Selected Account",
                                        'subaccount' => "Sub Accounts",
                                    );
                                    echo Ddl::generateArrayDDL('selectAccountSearchType', $showShipments, '', '', 'class="form-filter select2 form-control" ', "", 'selectAccountSearchType');
                                    ?>
                                </div>
                            </div>
                            <div class="col-md-<?php echo $colSpan; ?>">
                                <label class="label-account">Select Account</label>
                                <div class="form-group">
                                    <div id="user_content">
                                        <?php
                                        $accountParentId = 0;
                                        $includeParent = true;
                                        if ($this->user->getUserType() == User::USER_TYPE_CORPORATE) {
                                            $accountParentId = $this->user->getUserAccountId();
                                            $includeParent = false;
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
                            <?php } ?>
                            <div class="col-md-<?php echo $colSpan; ?>">
                                <label class="label-account">Select Carrier</label>
                                <div class="form-group">
                                    <div id="carriers_div">
                                        <?php echo Ddl::generateArrayDDL('search_Carrier_id', array("" => "Select Carrier"), '', '', 'class="form-filter select2 form-control" ', "", 'carriers'); ?>
                                        <?php //echo Ddl::generateCarrierDDLWithImage('search_Carrier_id',$search_Carrier_id,'id', ' class="bs-select input-sm form-control form-filter " data-container="body" required="" data-live-search="true"  data-show-subtext="true"');?>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-<?php echo $colSpan; ?>">
                                <label class="label-account">Select Service</label>
                                <div class="form-group">
                                    <div id="service_div">
                                        <?php echo Ddl::generateArrayDDL('service_id[]', array("" => "Select Service"), '', '', ' rel="tooltip" title="Select Service" class="form-filter select2 form-control"  multiple="multiple"', "", $dd_id = 'service_id'); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-<?php echo $colSpan; ?>">
                                <label class="label-account">Date Type </label>
                                <div class="form-group">
                                    <select id="date_type" name="date_type" class="form-filter bs-select form-control" title="" placeholder="" tabindex="-1" aria-hidden="true" data-original-title="">
                                        <option value="dateCreated" <?= (!empty($this->dateType) && $this->dateType == 'dateCreated'?'selected="selected"':'')?>>Date Created</option>
                                        <option value="dateLabelCreated" <?= (!empty($this->dateType) && $this->dateType == 'dateLabelCreated'?'selected="selected"':'')?>>Date label Created</option>
                                        <option value="dateBooked" <?= (!empty($this->dateType) && $this->dateType == 'dateBooked'?'selected="selected"':'')?>>Date Dispatched</option>
                                        <option value="dateDelivered" <?= (!empty($this->dateType) && $this->dateType == 'dateDelivered'?'selected="selected"':'')?>>Date Delivered</option>
                                        <option value="dateScanned" <?= (!empty($this->dateType) && $this->dateType == 'dateScanned'?'selected="selected"':'')?>>Date Scanned</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-<?php echo $colSpan; ?>">
                                <label class="control-label" data-toggle="tooltip">Date Ranges </label>
                                <div class="input-group date-picker input-daterange" data-date="20/01/2018" data-date-format="mm/dd/yyyy">
                                    <input type="text" class="form-control" name="from_date" id="from" value="<?= (!empty($_POST['from_date']) ? $_POST['from_date'] : ''); ?>" >
                                    <span class="input-group-addon"> to </span>
                                    <input type="text" class="form-control" name="to_date" id="to" value="<?php echo (!empty($_POST['to_date']) && is_integer((int)$_POST['to_date'])) ? (int)$_POST['to_date'] : ''; ?><?php echo (!empty($_POST['to_date']) && strtotime($_POST['to_date'] == true)) ? $_POST['to_date'] : ''; ?>">
                                    <input type="hidden" class="" name="download_file" id="download_file" value=""> 

                                </div>
                            </div>
                            <div class="col-md-3">
                                <label class="control-label">&nbsp;</label><br>
                                <button type="submit" class="btn blue download_file"><i class="fa fa-download"></i> Download Excel File</button>
                            </div>
                        </div>
                    </form>
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
        <style>
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