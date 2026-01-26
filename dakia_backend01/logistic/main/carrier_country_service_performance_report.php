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
                    'country.class',
                    'countryfilter.class',
                    'iaddress.class',
                    'carrier.class',
                    'carrierfilter.class',
                    'consignment.class',
                    'consignmentfilter.class',
                    'services.class' ,
                    'servicefilter.class',
                    'trackingdata.class', 
                    'trackingdatafilter.class',
 
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
    private $showReport = '';
    private $service_chart = array();
    private $barChartForCountry = array();
    private $barCountryName = array();
    private $userCorporate = 0;
    private $allUserCarrierId = array();
    private $allUserServicesId = array();

    protected function init() {

        $this->breadCrumb['data'] = array(
            'index.php' => Translation::GetCaption("HOME"),
            'carrier_country_service_performance_report.php' => 'Carrier country service performance report'
        );

        $this->user = SessionManager::getUser();
         $user_account_id = $this->user->getUserAccountId();
        if ($this->user->getUserType() == USER::USER_TYPE_ADMIN) {
                $user_account_id = '';
        }
        $this->showReport = (!empty($_POST['show_shipments'])?$_POST['show_shipments']:'all');
//        if($this->user->getUserType() === User::USER_TYPE_CLIENT)
//            $this->userAccountId =  $this->user->getUserAccountId();
        $allouedAcccounts = [];
        if ($this->user->getUserType() == USER::USER_TYPE_CORPORATE) {
            $this->userCorporate = $this->user->getId();
        }
        
        $user_account_id = (!empty($this->form_vars['user_account_id'])? $this->form_vars['user_account_id']: $this->user->getUserAccountId());
        $selectAccountSearchType = $this->form_vars['show_shipments'];
        if(empty($selectAccountSearchType)) {
            $selectAccountSearchType = "all";
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
        $this->userAccountId = $user_account_id;
        
        if(empty($_POST['service_id']) && empty($_POST['search_Carrier_id'])){
            $data = Carrier::getCarriersServersFromUserAccount($user_account_id,'',true);
            $this->allUserCarrierId = $data['carrierIds'];
            $this->allUserServicesId = $data['serviceIds'];
        }
        $this->serviceId = (!empty($_POST['service_id']) ? $_POST['service_id'] : $this->allUserServicesId);
        $this->carrierId = (!empty($_POST['search_Carrier_id']) ? $_POST['search_Carrier_id'] : implode(',', $this->allUserCarrierId));
        /*         * DataTable handlings
         */
        if (isset($_GET['action']) && $_GET['action'] == "carrier_country_service") {
            $this->user = SessionManager::getUser();
           
            $user_account_id = (!empty($this->form_vars['user_account_id'])? $this->form_vars['user_account_id']: $this->user->getUserAccountId());
            $selectAccountSearchType = $this->form_vars['show_shipments'];
            if(empty($selectAccountSearchType)) {
                $selectAccountSearchType = "all";
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
            if(empty($_GET['service_id']) && empty($_GET['search_Carrier_id'])){
                $data = Carrier::getCarriersServersFromUserAccount($user_account_id,'',true);
                $this->allUserCarrierId = $data['carrierIds'];
                $this->allUserServicesId = $data['serviceIds'];
            }
            
            $this->serviceId = (!empty($_GET['service_id']) ? explode(',',$_GET['service_id']) : $this->allUserServicesId);
            $this->countryId = (!empty($_GET['country']) ? $_GET['country'] : '');
            $this->carrierId = (!empty($_GET['search_Carrier_id']) ? $_GET['search_Carrier_id'] : implode(',',  $this->allUserCarrierId));

            $toDate = (!empty($_GET['toDate']) ? date('Y-m-d', strtotime($_GET['toDate'])) : '');
            $fromDate = (!empty($_GET['fromDate']) ? date('Y-m-d', strtotime($_GET['fromDate'])) : '');
            $trackingDataFilter = new TrackingDataFilter();
//            $totalRecords = $trackingDataFilter->getCarrierServicePerformanceReport($fromDate, $toDate, $this->carrierId, $this->serviceId, $this->countryId, $this->userCorporate, '1');
//            $iTotalRecords = $totalRecords[0]->getId();
//            $iDisplayLength = intval($_REQUEST['length']);
//            $iTotalRecords = (!empty($iTotalRecords) ? $iTotalRecords : '0');
//            $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
//            $iDisplayStart = intval($_REQUEST['start']);
//            $sEcho = intval($_REQUEST['draw']);
//            $end = $iDisplayStart + $iDisplayLength;
//            $end = $end > $iTotalRecords ? $iTotalRecords : $end;
//            $trackingDataFilter->setRowsPerPage($iDisplayLength);
//            $trackingDataFilter->setOffset($iDisplayStart);
            $trackingObj = $trackingDataFilter->getCarrierServicePerformanceReport($fromDate, $toDate, $this->carrierId, DbAccess3::escape($this->serviceId), DbAccess3::escape($this->countryId), $allouedAcccounts,'');
            $countryArr = [];
            $countryServiceArray=[];
            $totalNoOfShipment = 0;

           $servicesCount=2;
            if(!empty($trackingObj)){
                $transitTime;
                $transitDayCal = 0;
                $transitTime = 0;

                $serviceArr;
                $noOfDaysTransit = 0;
                $countryArr = [];
                $countryServiceArray =[];
                $countryServiceTransitArray = [];
                $countryArray =[];
                
                foreach ($trackingObj as $key => $data) {

                    $deliveredStatusCount = '';
                    $serviceName = (!empty($data->getName()) ? $data->getName() : '');
                    $trackingCreatedDate = explode(',', $data->getGroupDateCreated());
                    $trackingStatusCodes = explode(',', $data->getGroupStatusCode());
                    $carrierRecived = array_search(148, $trackingStatusCodes);
                    $deliveredStatusCount = array_search(122, $trackingStatusCodes);
                    if($deliveredStatusCount == 0)
                        $deliveredStatusCount = array_search(121, $trackingStatusCodes);

                    $transitTime = (!empty($data->getTransitTime()) ? $data->getTransitTime() : '');
                    $serviceId = $data->getServiceId();
                    $daysOfDelivered = 0;

                    if ($deliveredStatusCount!==FALSE ) {
                        $countryServiceArray[$data->getCountryName()][$serviceName] = isset($countryServiceArray[$data->getCountryName()][$serviceName]) ? $countryServiceArray[$data->getCountryName()][$serviceName] = $countryServiceArray[$data->getCountryName()][$serviceName]+1 : 1; 
                        
                        //$daysOfDelivered = 0;
                        $firstIndexCarrierReceived = $trackingCreatedDate[$carrierRecived];
                        $LastIndexDelivery = $trackingCreatedDate[$deliveredStatusCount];
                        $date1 = date_create($firstIndexCarrierReceived);
                        $date2 = date_create($LastIndexDelivery);
                        $diff = date_diff($date2, $date1);
                        $daysOfDelivered = $diff->d;
                        //$transitDayCal =0;
                        $transitDayCal = $transitDayCal + $daysOfDelivered;
                        $countryArray[$data->getCountryName()][$serviceName] = [$daysOfDelivered];
                        $countryServiceTransitArray[$data->getCountryName()][$serviceName][] = $daysOfDelivered;
                        $countryArr[$data->getCountryName()][$serviceName] = array(
                            'service' => $serviceName,
                            'notransitdays' => array_sum($countryServiceTransitArray[$data->getCountryName()][$serviceName]),
                            'carrier_tranist_time' => $transitTime,
                            'no_of_shipment' => $countryServiceArray[$data->getCountryName()][$serviceName],
                            'countryname' => $data->getCountryName()
                        );
                    }

                }
            }
            $noOfDaysTransit = 0;
            if (!empty($countryArr)) {
                $tablularCountryService = [];
                foreach ($countryArr as $index => $data) {
                    foreach ($data as $key => $value) {
                        $efficiency = 0;
                        $noOfDaysTransit = $value['notransitdays'] / $value['no_of_shipment'];
                        $transitTime = $value['carrier_tranist_time'];
                        if (!empty($noOfDaysTransit)) {
                            $efficiency = $transitTime / $noOfDaysTransit * 100;
                        }

                        $tablularCountryService[$value['service']][$index] = ['avg_ttime' => number_format((float) $noOfDaysTransit, 2, '.', ''), 'shipments' => $value['no_of_shipment'], 'ttime' => $transitTime, 'efficiency' => number_format((float) $efficiency, 2, '.', '') . '%'];
                    }
                }
            }
            $currentArr = array();
            $currentArr['countryname'] = '';
            $currentArr['ttime'] = '';
            $currentArr['shipments'] = '';
            $currentArr['avg_ttime'] = '';
            $currentArr['efficiency'] = '';
            $setDataArr[] = $currentArr;
            if (!empty($tablularCountryService)) {
                foreach ($tablularCountryService as $key => $data) {
                    $currentArr = array();
                    $currentArr['countryname'] = '<b>' . $key . '</b>';
                    $currentArr['ttime'] = '';
                    $currentArr['shipments'] = '';
                    $currentArr['avg_ttime'] = '';
                    $currentArr['efficiency'] = '';
                    $setDataArr[] = $currentArr;
                    foreach ($data as $indx => $value) {
                        $currentArr = array();
                        $currentArr['countryname'] = $indx;
                        //$currentArr['service'] = '';
                        $currentArr['ttime'] = $value['ttime'];
                        $currentArr['shipments'] = $value['shipments'];
                        $currentArr['avg_ttime'] = $value['avg_ttime'];
                        $currentArr['efficiency'] = $value['efficiency'];
                        $setDataArr[] = $currentArr;
                    }
                }
            }
            $setDataArrJson['data'] = ((count($setDataArr) > 0) ? $setDataArr : []);
            $setDataArrJson['draw'] = 1;
            $setDataArrJson['recordsTotal'] = 1000;
            $setDataArrJson['recordsFiltered'] = 1000;
            echo json_encode($setDataArrJson);
            die;
        }
        
        // END TABLE
        if (isset($_GET['action']) && $_GET['action'] == 'get_carrier_services') {
            $user_account_id = $this->form_vars['user_account_id'];
            $selected_carrier = $this->form_vars['selected_carrier'];
            $selected_service = (!empty($this->form_vars['selected_service'])?explode(',',$this->form_vars['selected_service']):'');
            
            $consignmentList = array();
            if (!empty($user_account_id)) {
                $return = array();
                $userAccountArry = CustomerAccount::accountSubAccount($user_account_id, 0, true);
                $ids = implode(",", $userAccountArry);
                $userFilter = new UserFilter();
                $userIds = $userFilter->getUserIdsFromAccountIds($ids);
                $consignmentFilterObj = new ConsignmentFilter();
                $consignmentFilterObj->addFilterIn("    c.user_id", $userIds, "consignmentfilter");
                $consignmentList = $consignmentFilterObj->getColumnList("DISTINCT(CASE WHEN c.customized_service_id > 0 THEN c.customized_service_id ELSE c.service_id END) AS service_id");
                $serviceIds = array();
                foreach ($consignmentList as $con) {
                    $serviceIds[] = $con->getServiceId();
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
            //$service_option = "<option value=''>Select Service</option>";
            foreach ($services as $sr) {
                $selected = "";
                if(in_array($sr->getId(),$selected_service)) {
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



        $this->countryId = (!empty($_POST['country']) ? $_POST['country'] : '');
        
        $toDate = (!empty($_POST['to_date']) ? date('Y-m-d', strtotime($_POST['to_date'])) : '');
        $fromDate = (!empty($_POST['from_date']) ? date('Y-m-d', strtotime($_POST['from_date'])) : '');
        $where = '';
        $trackindDataFilter = new TrackingDataFilter();
       
        $trackingObj = $trackindDataFilter->getCarrierServicePerformanceReport($fromDate, $toDate, $this->carrierId, $this->serviceId, DbAccess3::escape($this->countryId), $allouedAcccounts);

        if (!empty($trackingObj)) {
            $transitTime;
            $transitDayCal = 0;
            $transitTime = 0;
            
            $serviceArr;
            $noOfDaysTransit = 0;
            $countryArr = [];
            $countryServiceArray =[];
            $countryArray =[];
            $countryServiceTransitArray=[];
            foreach ($trackingObj as $key => $data) {
               
                $deliveredStatusCount = '';
                $serviceName = (!empty($data->getName()) ? $data->getName() : '');
                $trackingCreatedDate = explode(',', $data->getGroupDateCreated());
                $trackingStatusCodes = explode(',', $data->getGroupStatusCode());
                $carrierRecived = array_search(148, $trackingStatusCodes);
                $deliveredStatusCount = array_search(122, $trackingStatusCodes);
                if($deliveredStatusCount == 0)
                    $deliveredStatusCount = array_search(121, $trackingStatusCodes);
                $transitTime = (!empty($data->getTransitTime()) ? $data->getTransitTime() : '');
                $serviceId = $data->getServiceId();
 
                if ($deliveredStatusCount !== FALSE) {
                    $countryServiceArray[$data->getCountryName()][$serviceName] = isset($countryServiceArray[$data->getCountryName()][$serviceName]) ? $countryServiceArray[$data->getCountryName()][$serviceName] = $countryServiceArray[$data->getCountryName()][$serviceName]+1 : 1; 

                    $firstIndexCarrierReceived = $trackingCreatedDate[$carrierRecived];
                    $LastIndexDelivery = $trackingCreatedDate[$deliveredStatusCount];
                    $date1 = date_create($firstIndexCarrierReceived);
                    $date2 = date_create($LastIndexDelivery);
                    $daysOfDelivered = 0;
                    if(!empty($LastIndexDelivery) && !empty($firstIndexCarrierReceived)){
                        $diff = date_diff($date2, $date1);
                        $daysOfDelivered = $diff->d;
                        $countryArray[$data->getCountryName()][$serviceName] = array($daysOfDelivered);     
                        $countryServiceTransitArray[$data->getCountryName()][$serviceName][] = $daysOfDelivered;
                        $countryArr[$data->getCountryName()][$serviceName] = array(
                            'service' => $serviceName,
                            'notransitdays' => array_sum($countryServiceTransitArray[$data->getCountryName()][$serviceName]),
                            'carrier_tranist_time' => $transitTime,
                            'no_of_shipment' => $countryServiceArray[$data->getCountryName()][$serviceName],
                            'countryname' => $data->getCountryName()
                        );
                        
                    }
                   
                }
            }
            $noOfDaysTransit = 0;
            if (!empty($countryArr)) {
                $tmp = [];
                foreach ($countryArr as $index => $data) {
                    foreach ($data as $key => $value) {
                        $efficiency = 0;
                        $noOfDaysTransit = $value['notransitdays'] / $value['no_of_shipment'];
                        $transitTime = $value['carrier_tranist_time'];
                        if (!empty($noOfDaysTransit)) {
                            $efficiency = $transitTime / $noOfDaysTransit * 100;
                        }
                        $this->barCountryTransitTime[] = array(
                            'shiplabel' => 'Shipments',
                            'noship' => $value['no_of_shipment'],
                            'countryname' => $index,
                            'average_tranist_time' => 'Average Transit Time',
                            'service' => $value['service'],
                            'value' => number_format((float) $noOfDaysTransit, 2, '.', ''),
                            'transit_time' => $transitTime,
                            'averagedays' => number_format((float) $noOfDaysTransit, 2, '.', '') . ' days',
                            'servicetranist' => $value['service'] . ' (' . $transitTime . ' days)',
                            'average_effciency_time' => 'Efficiency',
                            'effciency' => number_format((float) $efficiency, 2, '.', '') . '%'
                        );
                        $this->barCountryName[] = [$index];
                        $this->barChartForCountry[] = [$index, number_format((float) $noOfDaysTransit, 2, '.', '')];
                        $tmp[$value['service']][$index] = ['avg_ttime' => number_format((float) $noOfDaysTransit, 2, '.', ''), 'shipments' => $value['no_of_shipment'], 'ttime' => $transitTime, 'efficiency' => number_format((float) $efficiency, 2, '.', '')];
                    }
                }
            }

            if (isset($_POST['download_file']) && $_POST['download_file'] == "download_excel") {
                $cols = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z'];
                $headerStyle = array(
                    'font' => array(
                        'bold' => true,
                        'size' => '16'
                    ),
                );
                $boldCenterStyle = array(
                    'alignment' => array(
                        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    ),
                    'font' => array(
                        'bold' => true
                    ),
                );
                $centerStyle = array(
                    'alignment' => array(
                        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    )
                );
                $boldStyle = array(
                    'font' => array(
                        'bold' => true
                    )
                );
                $objPHPExcel = new PHPExcel();
                $objGraphSheet = $objPHPExcel->getActiveSheet();
                $objGraphSheet->setTitle('Graph');
                //$objGraphSheet = $objPHPExcel->setActiveSheetIndex(0);
                $servicesNames = [];
                $servicesNames = array_keys($tmp);
                $startColIndex = 1;
                $step = 3;
                $dataStartRow = 24;
                foreach ($servicesNames as $key => $serviceName) {
                    $headingIndex = $startColIndex;
                    $objPHPExcel->getActiveSheet()->mergeCells($cols[$startColIndex] . $dataStartRow. ":" . $cols[($startColIndex + $step)] . $dataStartRow);
                    $objPHPExcel->getActiveSheet()->getStyle($cols[$startColIndex] . $dataStartRow)->applyFromArray($boldCenterStyle);
                    $objPHPExcel->getActiveSheet()->setCellValue($cols[$startColIndex++] . $dataStartRow, $serviceName);
                    $startColIndex += ($step + 1);
                    
                    
                    $objPHPExcel->getActiveSheet()->setCellValue($cols[$headingIndex] . ($dataStartRow + 1), "T.Time (days)");
                    $objPHPExcel->getActiveSheet()->getColumnDimension($cols[$headingIndex])->setWidth(15);
                    $objPHPExcel->getActiveSheet()->getStyle($cols[$headingIndex] . ($dataStartRow + 1))->applyFromArray($centerStyle);
                    
                    $objPHPExcel->getActiveSheet()->setCellValue($cols[++$headingIndex] . ($dataStartRow + 1), "Total Delivered Shipments");
                    $objPHPExcel->getActiveSheet()->getColumnDimension($cols[$headingIndex])->setWidth(22);
                    $objPHPExcel->getActiveSheet()->getStyle($cols[$headingIndex] . ($dataStartRow + 1))->applyFromArray($centerStyle);
                    
                    $objPHPExcel->getActiveSheet()->setCellValue($cols[++$headingIndex] . ($dataStartRow + 1), "Avg.T.Time");
                    $objPHPExcel->getActiveSheet()->getColumnDimension($cols[$headingIndex])->setWidth(15);
                    $objPHPExcel->getActiveSheet()->getStyle($cols[$headingIndex] . ($dataStartRow + 1))->applyFromArray($centerStyle);

                    $objPHPExcel->getActiveSheet()->setCellValue($cols[++$headingIndex] . ($dataStartRow + 1), "Efficienty (%)");
                    $objPHPExcel->getActiveSheet()->getColumnDimension($cols[$headingIndex])->setWidth(15);
                    $objPHPExcel->getActiveSheet()->getStyle($cols[$headingIndex] . ($dataStartRow + 1))->applyFromArray($centerStyle);
                         
                }
                
                $totalServices = count($servicesNames);
                
                array_unshift($servicesNames, '');
                $dataArr = [];
                $dataArr[] = $servicesNames;
                ///////////////////
                $countriesArr = [];
                foreach ($tmp as $service => $data) {
                    foreach ($data as $country => $countryData) {
                        $countriesArr[] = $country;
                    }
                }
                $countriesArr = array_unique($countriesArr);                
                ////////////////////////////                
                $dataStartRow = 26;
                foreach ($countriesArr as $country) {
                    $dataStartCol = 0;
                    $tmpArr = [];
                    $tmpArr[] = $country;
                    $isFirst = true;
                    $objPHPExcel->getActiveSheet()->setCellValue($cols[$dataStartCol] . $dataStartRow, $country);
                    $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
                    foreach ($servicesNames as $services) {
                        if ($isFirst) {
                            $isFirst = FALSE;
                            continue;
                        }
                        $ttime = (isset($tmp[$services][$country]) &&  $tmp[$services][$country]['ttime'] != "")? $tmp[$services][$country]['ttime'] : '0.00';
                        $shipments = isset($tmp[$services][$country]) ? $tmp[$services][$country]['shipments'] : '0.00';
                        $avgTtime = isset($tmp[$services][$country]) ? $tmp[$services][$country]['avg_ttime'] : '0.00';
                        $efficiency = isset($tmp[$services][$country]) ? $tmp[$services][$country]['efficiency'] : '0.00';
                        $tmpArr[] = $avgTtime;

                        $objPHPExcel->getActiveSheet()->setCellValue($cols[++$dataStartCol] . $dataStartRow, $ttime);
                        $objPHPExcel->getActiveSheet()->getStyle($cols[$dataStartCol] . $dataStartRow)->applyFromArray($centerStyle);
                        $objPHPExcel->getActiveSheet()->setCellValue($cols[++$dataStartCol] . $dataStartRow, $shipments);
                        $objPHPExcel->getActiveSheet()->getStyle($cols[$dataStartCol] . $dataStartRow)->applyFromArray($centerStyle);
                        $objPHPExcel->getActiveSheet()->setCellValue($cols[++$dataStartCol] . $dataStartRow, $avgTtime);
                        $objPHPExcel->getActiveSheet()->getStyle($cols[$dataStartCol] . $dataStartRow)->applyFromArray($centerStyle);
                        $objPHPExcel->getActiveSheet()->setCellValue($cols[++$dataStartCol] . $dataStartRow, $efficiency);
                        $objPHPExcel->getActiveSheet()->getStyle($cols[$dataStartCol] . $dataStartRow)->applyFromArray($centerStyle);
                        
                        $dataStartCol++;
                    }
                    $dataStartRow++;
                    $dataArr[] = $tmpArr;
                }
                
                $this->barChartForCountry = $dataArr;
                
                $objGraphDataSheet = $objPHPExcel->createSheet(1);
                $objGraphDataSheet->setTitle('GraphData');
                $objGraphDataSheet = $objPHPExcel->setActiveSheetIndex(1);
                
                $objGraphDataSheet->fromArray(
                        $this->barChartForCountry
                );
                
                $totalCountries = count($countriesArr);                
                $dataseriesLabels = [];
                for ($i = 1; $i <= $totalServices; $i++) {
                    if (isset($cols[$i])) {
                        $col = $cols[$i];
                        $dataseriesLabels[] = new PHPExcel_Chart_DataSeriesValues('String', 'GraphData!$' . $col . '$1', null, 1); //	2010
                    }
                }
                $xAxisTickValues = array(
                    new PHPExcel_Chart_DataSeriesValues('String', 'GraphData!$A$2:$A$' . ($totalCountries), null, ($totalCountries - 1)), //	Q1 to Q4
                );
                $dataSeriesValues = [];
                for ($j = 1; $j <= $totalServices; $j++) {
                    if (isset($cols[$j])) {
                        $col = $cols[$j];
                        $dataSeriesValues[] = new PHPExcel_Chart_DataSeriesValues('Number', 'GraphData!$' . $col . '$2:$' . $col . '$' . ($totalCountries), null, ($totalCountries - 1));
                    }
                }
                //	Build the dataseries
                $series = new PHPExcel_Chart_DataSeries(
                        PHPExcel_Chart_DataSeries::TYPE_BARCHART, // plotType
                        PHPExcel_Chart_DataSeries::GROUPING_STACKED, // plotGrouping
                        range(0, count($dataSeriesValues) - 1), // plotOrder
                        $dataseriesLabels, // plotLabel
                        $xAxisTickValues, // plotCategory
                        $dataSeriesValues        // plotValues
                );
                $series->setPlotDirection(PHPExcel_Chart_DataSeries::DIRECTION_VERTICAL);
                $plotarea = new PHPExcel_Chart_PlotArea(null, array($series));
                $legend = new PHPExcel_Chart_Legend(PHPExcel_Chart_Legend::POSITION_RIGHT, null, false);
                $title = new PHPExcel_Chart_Title('Country Average Delivery Time');
                $yAxisLabel = new PHPExcel_Chart_Title('');
                $chart = new PHPExcel_Chart(
                        'chart1', // name
                        $title, // title
                        $legend, // legend
                        $plotarea, // plotArea
                        true, // plotVisibleOnly
                        0, // displayBlanksAs
                        null, // xAxisLabel
                        $yAxisLabel  // yAxisLabel
                );
                //	Set the position where the chart should appear in the worksheet
                $chart->setTopLeftPosition('B2');
                $chart->setBottomRightPosition('L20');
                //	Add the chart to the worksheet
                $objGraphSheet = $objPHPExcel->setActiveSheetIndex(0);


                //exit;
                $objPHPExcel->getActiveSheet()->addChart($chart);
                header('Content-Type: application/vnd.ms-excel');
                header('Content-Disposition: attachment;filename=carrier-country-service-performance-report' . date('d-m-Y') . '.xls'); // file name of excel
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
        <!--        <script src="../assets/global/plugins/datatables/datatables.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js" type="text/javascript"></script>-->
        <script src="../assets/global/plugins/bootstrap-sweetalert/sweetalert.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js" type="text/javascript"></script>

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
            var initbarChartCountryTransit = function () {
                var chart2 = AmCharts.makeChart("barchart_country", {
                    "theme": "light",
                    "type": "serial",
                    "startDuration": 1,
                    "fontFamily": 'Open Sans',
                    "color": '#888',
                    "dataProvider":<?php echo json_encode($this->barCountryTransitTime); ?>,
                    "valueAxes": [{
                            "position": "left",
                            "axisAlpha": 0,
                            "gridAlpha": 0
                        }],
                    "graphs": [{
                            "balloonText": "<b>[[countryname]]<br/>[[shiplabel]]</b>: [[noship]]<br/><b>[[servicetranist]]</b><br/><b>[[average_tranist_time]]:</b> [[averagedays]]<br/><b>[[average_effciency_time]]:</b>[[effciency]]",
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
                    "categoryField": "countryname",
                    "categoryAxis": {
                        "gridPosition": "start",
                        "axisAlpha": 0,
                        "gridAlpha": 0,
                        "labelRotation": 40,
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
            initbarChartCountryTransit();



            var grid = null;
            var DataTableFun = function () {
                    var handleDataTable = function () {
                        <?php
                        if(is_integer((int) $_POST['to_date']) || strtotime($_POST['to_date'] != false)) {
                            $toDate =  $_POST['to_date'];
                        } else {
                            $toDate = '';
                        }
                        ?>
                    var toDate = '<?= (!empty($_POST['to_date']) ? '&toDate=' . $toDate : ''); ?>';
                    var fromDate = '<?= (!empty($_POST['from_date']) ? '&fromDate=' . $_POST['from_date'] : ''); ?>';
                    var search_Carrier_id = '<?= (!empty($_POST['search_Carrier_id']) ? '&search_Carrier_id=' . $_POST['search_Carrier_id'] : ''); ?>';
                    var service_id = '<?= (!empty($_POST['service_id'] && is_array($_POST['service_id'])) ? '&service_id=' . implode(',',$_POST['service_id']) : ''); ?>';
                    var search_Code = '<?= (!empty($_POST['search_Code']) ? '&search_Code=' . $_POST['search_Code'] : ''); ?>';
                    var country = '<?= (!empty($_POST['country'] && is_integer($_POST['country'])) ? '&country=' . $_POST['country'] : ''); ?>';
                    var user_account_id = '<?= (!empty($_POST['user_account_id']) ? '&user_account_id=' . (int) $_POST['user_account_id'] : ''); ?>';
                    var show_shipments = '<?= (!empty($_POST['show_shipments']) ? '&show_shipments=' . $_POST['show_shipments'] : '&show_shipments=all'); ?>';
                    var datatableurl = "carrier_country_service_performance_report.php?action=carrier_country_service"+ toDate + fromDate + search_Carrier_id + search_Code + country  + service_id + user_account_id +show_shipments;
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
                            "scrollX": false,
                            "bStateSave": false,
                            "paging":   false,
                            "info":     false,
                            "autoWidth": false,
                            "columns": [
                                {"data": "countryname"},
                                {"data": "ttime", "className": "text-center"},
                                {"data": "shipments", "className": "text-center"},
                                {"data": "avg_ttime", "className": "text-center"},
                                {"data": "efficiency", "className": "text-center"},
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
                //});
//                }
                var userAccountId = '<?php echo $this->user->getUserAccountId(); ?>';
                var selected = '<?php echo $this->carrierId ?>';
                get_carriers(userAccountId);
                function get_carriers(userAccountId) {
                    var selected_carrier = '<?php echo $this->carrierId; ?>';
                    var selected_service = '<?php echo (!empty($_POST['service_id'] && (is_array($this->serviceId) || is_integer($this->serviceId)))?implode(',',$this->serviceId):''); ?>';
                    $.ajax({
                        type: "POST",
                        url: "carrier_country_service_performance_report.php?action=get_carrier_services",
                        data: {user_account_id: userAccountId,selected_carrier:selected_carrier,selected_service:selected_service },
                        dataType: "json",
                        success: function (data) {
                            $('#carriers').html(data.carrier_option);
                            $('#service_id').html(data.services_option);
                            $('#carriers').select2();
                            $('#service_id').select2();
                          //  $('#carriers').trigger('change');
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
                
                
                
                setTimeout(function () {
                    $('.amcharts-chart-div a').remove();
                }, 3000);
                
                $('#carriers').change(function () {
                    var carrierId = $(this).val();
                    var select2service = $("#service_id").select2();
                    select2service.val($('#service_id option:first').val());
                    change_carriers(carrierId);
                    
                });
                $("#carriers").trigger("change");
                $('.download_file').click(function () {
                    $('#download_file').val('download_excel');
                    $('#admin_form').submit();
                    setTimeout(function () {
                        $('#download_file').val('');
                    }, 3000);
                });
                <?php if(!empty($this->showReport)){?>
                    $('#show_shipments').val('<?= $this->showReport;?>').trigger('change');
                <?php }?>
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
                    Carrier Country Delivered Performance Report
                </div>
                <div class="actions">
                    <a href="javascript:{};" class="btn blue download_file"><i class="fa fa-download"></i> Download Excel File</a>
                </div>
            </div>
            <div class="portlet light bordered">
                <div class="portlet-title">
                    <div class="caption">
                       <span id="account_name"></span>Report Filters
                    </div>
                </div>
                <div class="portlet-body">
                    <form class="form-horizontal1" action="" id="admin_form" method="POST" name="admin_form" enctype="multipart/form-data">
                        <div class="row">
                            <?php  
                                if($this->user->getUserType() != USER::USER_TYPE_CLIENT){
                            ?>
                            <div class="col-md-3">
                                <label class="label-account">Show Report </label>
                                <div class="form-group">
                                    <select id="show_shipments" name="show_shipments" class="form-filter bs-select form-control" >
                                        <option value="all">Selected Account &amp; Sub Accounts</option>
                                        <option value="own">Selected Account</option>
                                        <option value="subaccount">Sub Accounts</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
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
                           
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label >Carrier</label>
                                    <div class="first_form_col">
                                        <div class="input-group">
                                            <div class="input-group-addon"> <i class="fa fa-shopping-cart"></i> </div>
                                            <?php
                                             echo Ddl::generateArrayDDL('search_Carrier_id', array("" => "Select Carrier"), $this->carrierId, '', 'class="form-filter select2 form-control" ', "", 'carriers'); 
                                            ?>                                            
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label class="label-account">Select Service</label>
                               <div class="form-group">
                                    <div id="service_div">
                                        <?php echo Ddl::generateArrayDDL('service_id[]', array("" => "Select Service"), '', '', ' rel="tooltip" multiple="multiple" title="Select Service" class="form-filter select2 form-control" ', "", $dd_id = 'service_id'); ?>
                                    </div>
                                </div>
                            </div> 
                        </div>
                        <div class="row">
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
                                <div class="input-group date-picker input-daterange" data-date="2018-01-20" data-date-format="dd-mm-yyyy">
                                    <input type="text" class="form-control" name="from_date" id="from" value="<?= (!empty($_POST['from_date']) ? formatDate($_POST['from_date']) : formatDate(date("Y-m-d", strtotime("-30 day")))); ?>" data-original-title="" title="">
                                    <span class="input-group-addon"> to </span>
                                    <input type="text" class="form-control" name="to_date" id="to" value="<?= (!empty($_POST['to_date']) ? formatDate($_POST['to_date']) : formatDate(date("Y-m-d", strtotime('today UTC')))); ?>" data-original-title="" title="">
                                    <input type="hidden" class="" name="download_file" id="download_file" value=""> 

                                </div>
                            </div>                             
                            <div class="col-md-3">
                                <label class="control-label">&nbsp;</label><br>
                                <button class="btn btn-primary" type="submit">Filter Report</button>
                            </div>
                        </div>
                         <?php }else {?>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label >Carrier</label>
                                    <div class="first_form_col">
                                        <div class="input-group">
                                            <div class="input-group-addon"> <i class="fa fa-shopping-cart"></i> </div>
                                            <?php
                                             echo Ddl::generateArrayDDL('search_Carrier_id', array("" => "Select Carrier"), $this->carrierId, '', 'class="form-filter select2 form-control" ', "", 'carriers'); 
                                            ?>                                            
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label class="label-account">Select Service</label>
                               <div class="form-group">
                                    <div id="service_div">
                                        <?php echo Ddl::generateArrayDDL('service_id[]', array("" => "Select Service"), '', '', ' rel="tooltip" multiple="multiple" title="Select Service" class="form-filter select2 form-control" ', "", $dd_id = 'service_id'); ?>
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
                                <div class="input-group date-picker input-daterange" data-date="2018-01-20" data-date-format="yyyy-mm-dd">
                                    <input type="text" class="form-control" name="from_date" id="from" value="<?= (!empty($_POST['from_date']) ? $_POST['from_date'] : date("Y-m-d", strtotime("-30 day"))); ?>" data-original-title="" title="">
                                    <span class="input-group-addon"> to </span>
                                    <input type="text" class="form-control" name="to_date" id="to" value="<?= (!empty($_POST['to_date']) ? formatDate($_POST['to_date']) : date("Y-m-d")); ?>" data-original-title="" title="">
                                    <input type="hidden" class="" name="download_file" id="download_file" value=""> 

                                </div>
                            </div>                             
                        </div>
                        <div class="row">
                            <div class="col-md-12 text-center">
                                <label class="control-label">&nbsp;</label><br>
                                <button class="btn btn-primary" type="submit">Filter Report</button>
                            </div>
                        </div>
                         <?php }?>
                    </form>
                </div>
            </div>
        </div>
        <div class="portlet light bordered">
            <div class="portlet-title">
                <div class="caption"> <i class="fa fa-archive"></i>
                 Performance
               </div>
                <div class="actions"></div>
                <div class="tools"> </div>
            </div>
            <div class="portlet-body">
                <div class="tabbable-line">
                    <ul class="nav nav-tabs">
                        <li class="active">
                            <a href="#graphichal_view" data-toggle="tab">Graphical View</a>
                        </li>
                        <li>
                            <a href="#tabular" data-toggle="tab">Tabular</a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane active" id="graphichal_view">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="portlet light bordered">
                                        <div class="portlet-title">
                                            <div class="caption">
                                                <i class="icon-bar-chart font-green-haze"></i>
                                                Countries Average Transit Time
                                            </div>
                                            <div class="tools">
                                                <a href="javascript:;" class="collapse"></a>
                                                <a href="javascript:;" class="fullscreen"></a>
                                            </div>
                                        </div>
                                        <div class="portlet-body">
                                            <div id="barchart_country" class="chart" style="height: 400px;"></div>   
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane" id="tabular">
                            <div>
                                <table class="table table-striped table-bordered table-hover" id="manage-data-table">
                                    <thead>
                                        <tr role="row" class="heading">
                                            <th>Country Name</th>
                                            <th>Delivery Aim Time</th>
                                            <th>Total Delivered Shipments</th>
                                            <th>Avg Transit Time (Total Delivered Shipments)</th>
                                            <th>Efficiency</th>
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