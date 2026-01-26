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
    private $barCountryTransitTime = array();
    private $headerStyle;
    private $styleForReport;
    private $carrierId = '';
    private $serviceId = '';
    private $countryId = '';
    private $userAccountId = '';
    private $getUserAccountId = '';
    private $service_chart = array();
    private $excelServiceNameBarChart = array();
    private $excelServiceBarChart;
    private $allUserCarrierId = array();
    private $allUserServicesId = array();

    protected function init() {

        $this->breadCrumb['data'] = array(
            'index.php' => Translation::GetCaption("HOME"),
            "Carrier Performance Report"
        );
         $this->user = SessionManager::getUser();
        $user_account_id = $this->user->getUserAccountId();
        $data = Carrier::getCarriersServersFromUserAccount($user_account_id,"",true);
        $this->allUserCarrierId = $data['carrierIds'];
        $this->allUserServicesId = $data['serviceIds'];
        /*
         * DataTable handlings
         */
        if (isset($_GET['action']) && $_GET['action'] == "carrier_service") {
            $this->user = SessionManager::getUser();
            if ($this->user->getUserType() == USER::USER_TYPE_CORPORATE) {
                $this->userCorporate = $this->user->getId();
            }
            $user_account_id = $this->user->getUserAccountId();
            $data = Carrier::getCarriersServersFromUserAccount($user_account_id,"",true);
            $this->allUserCarrierId = $data['carrierIds'];
            $this->allUserServicesId = $data['serviceIds'];
            $this->serviceId = (!empty($_GET['service_id']) ? explode(',', $_GET['service_id']) : $this->allUserServicesId);
            $this->carrierId = (!empty($_GET['search_Carrier_id']) ? $_GET['search_Carrier_id'] : implode(',',  $this->allUserCarrierId));

            $toDate = (!empty($_GET['toDate']) ? date('Y-m-d', strtotime($_GET['toDate'])) : '');
            $fromDate = (!empty($_GET['fromDate']) ? date('Y-m-d', strtotime($_GET['fromDate'])) : '');
            $trackingDataFilter = new TrackingDataFilter();
            $totalRecords = $trackingDataFilter->get_carrier_service_performance($fromDate, $toDate, $this->carrierId, $this->serviceId, '', $this->userCorporate, '1');
            $iTotalRecords = $totalRecords[0]->getId();
            $iDisplayLength = intval($_REQUEST['length']);
            $iTotalRecords = (!empty($iTotalRecords) ? $iTotalRecords : '0');
            $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
            $iDisplayStart = intval($_REQUEST['start']);
            $sEcho = intval($_REQUEST['draw']);
            $end = $iDisplayStart + $iDisplayLength;
            $end = $end > $iTotalRecords ? $iTotalRecords : $end;
            $trackingDataFilter->setRowsPerPage($iDisplayLength);
            $trackingDataFilter->setOffset($iDisplayStart);
            $trackingObj = $trackingDataFilter->get_carrier_service_performance($fromDate, $toDate, $this->carrierId, $this->serviceId, '', $this->userCorporate,'','no');
            if(!empty($trackingObj)){
                $transitDayCal = 0;
                $transitTime = 0;
                $totalNoOfShipment = 0;
                $serviceArr;
                $noOfDaysTransit = 0;
                $countryArr;
                $shipmentServiceArray =[];
                $shipService = [];
                foreach ($trackingObj as $key => $data) {
                    //$transitTime[];
                    $serviceName = (!empty($data->getName()) ? $data->getName() : '');
                    $trackingCreatedDate = explode(',', $data->getGroupDateCreated());
                    $trackingStatusCodes = explode(',', $data->getGroupStatusCode());
                    $carrierRecived = array_search(148, $trackingStatusCodes);
                    if(in_array(122,$trackingStatusCodes))
                        $deliveredStatusCount = array_search(122, $trackingStatusCodes);
                    else if(in_array(121,$trackingStatusCodes))
                        $deliveredStatusCount = array_search(121, $trackingStatusCodes);
                        
                    $transitTime = (!empty($data->getTransitTime()) ? $data->getTransitTime() : '');
                    $totalNoOfShipment = $totalNoOfShipment + 1;
                    $serviceId = $data->getServiceId();
                    if ($deliveredStatusCount!==FALSE && (in_array(121,$trackingStatusCodes) || in_array(122,$trackingStatusCodes))) {
                        $shipmentServiceArray[$serviceId] = isset($shipmentServiceArray[$serviceId]) ? $shipmentServiceArray[$serviceId] = $shipmentServiceArray[$serviceId]+1 : 1; 
                        $firstIndexCarrierReceived = $trackingCreatedDate[$deliveredStatusCount];
                        $LastIndexDelivery = $trackingCreatedDate[$carrierRecived];
                        $date1 = date_create($firstIndexCarrierReceived);
                        $date2 = date_create($LastIndexDelivery);
                        $diff = date_diff($date1, $date2);
                        $daysOfDelivered = $diff->d;
                        $daysOfD[] = $daysOfDelivered;
                        $shipService[$serviceId] = array_sum($daysOfD);
                        $serviceArr[$serviceId] = array(
                            'service' => $serviceName,
                            'notransitdays' => $shipService[$serviceId],
                            'carrier_tranist_time' => $transitTime,
                            'no_of_shipment' => $shipmentServiceArray[$serviceId],
                            'countryname' => $data->getCountryName()
                        );
                    }
                }
                if (!empty($serviceArr)) {
                    $noOfDaysTransit = 0;
                    foreach ($serviceArr as $index => $data) {
                        $efficiency = 0;
                        $noOfDaysTransit = $data['notransitdays'] / $data['no_of_shipment'];
                        $transitTime = $data['carrier_tranist_time'];
                        if (!empty($noOfDaysTransit)) {
                            $efficiency = $transitTime / $noOfDaysTransit * 100;
                        }
                        $this->service_chart[] = array('shiplabel'=>'Shipments',  'noship' => $data['no_of_shipment'], 'average_tranist_time' => 'Average Transit Time', 'service' => $data['service'], 'value' => number_format((float) $noOfDaysTransit, 2, '.', ''), 'transit_time' => (!empty($transitTime))?$transitTime:'0', 'averagedays' => number_format((float) $noOfDaysTransit, 2, '.', '') , 'servicetranist' => $data['service'] . ' (' . $transitTime . ' days)', 'average_effciency_time' => 'Efficiency', 'effciency' => number_format((float) $efficiency, 2, '.', ''));
                    }
                }
            }

            if(!empty($this->service_chart)){
                foreach($this->service_chart as $key => $data){
                    $currentArr = array();
                    $currentArr['servicename'] =  $data['service'];
                    $currentArr['ttime'] = $data['transit_time'];
                    $currentArr['shipments'] = $data['noship'];
                    $currentArr['avg_ttime'] = $data['value'];
                    $currentArr['efficiency'] = $data['effciency'];
                    $setDataArr[] = $currentArr;

                }
            }else{
                $currentArr = array();
                $currentArr['servicename'] =  '';
                $currentArr['ttime'] = '';
                $currentArr['shipments'] = '';
                $currentArr['avg_ttime'] = '';
                $currentArr['efficiency'] = '';
                $setDataArr[] = $currentArr;
            }
            
            $setDataArrJson['data'] = (!empty($setDataArr)?$setDataArr:'0');
            $setDataArrJson['draw'] = $sEcho;
            $setDataArrJson['recordsTotal'] = $iTotalRecords;
            $setDataArrJson['recordsFiltered'] = $iTotalRecords;
            echo json_encode($setDataArrJson);
            die;
        }
        
        
        
        
        if (isset($this->form_vars['func']) && $this->form_vars['func'] == 'get_carrier_services') {
            $carrierId = $this->form_vars['carrier_id'];
            $serviceId = explode(',', $this->form_vars['service_id']);

            $ServiceFilter = new ServiceFilter();
            $ServiceFilter->addCarrierFilter($carrierId);
            $carrierFilter = new CarrierFilter();
            $carrierFilter->addFilter("usr.user_account_id = '" . $this->user->getUserAccountId() . "'");
            $allowedServicesObj = $carrierFilter->getCarrierSetupList('DISTINCT s.id');
            $allowedServices = [];
            if (!empty($allowedServicesObj)) {
                foreach ($allowedServicesObj as $allowedService) {
                    $allowedServices[] = $allowedService->getId();
                }
                $ServiceFilter->addFilter(' ser.id IN (' . implode(",", $allowedServices) . ')');
            } else {
                $ServiceFilter->addFilter(" ser.id = '-50000'");
            }
            $servicesList = $ServiceFilter->getCarrierServicesList('ser.id, ser.name, ser.code');
            $output = '<select name="service_id[]" id="service_id" class="form-control validate_check" multiple="multiple" data-original-title="" title=""><option value="">Select Service</option>';
            if (count($servicesList) > 0) {
                foreach ($servicesList as $service) {
                    $selected = (in_array($service->getId(), $serviceId) ? ' selected="selected"' : '');
                    $output .= '<option value="' . $service->getId() . '"' . $selected . '>' . $service->getName() . ' [' . $service->getCode() . ']</option>';
                }
            }
            $output .= '</select>';
            echo $output;
            exit;
        }
        if (isset($_GET['action']) && $_GET['action'] == "get_carrier_services") {
            $selected = $this->form_vars['selected'];
            $this->user = SessionManager::getUser();
            $user_account_id = $this->user->getUserAccountId();
            $return = Carrier::getCarriersServersFromUserAccount($user_account_id, $selected,true,true);
            echo json_encode($return);
            die;
        }
        
        $this->serviceId = (!empty($_POST['service_id']) ? $_POST['service_id'] : $this->allUserServicesId);
        
        $this->countryId = (!empty($_POST['country']) ? $_POST['country'] : '');
        $this->carrierId = (!empty($_POST['search_Carrier_id']) ? $_POST['search_Carrier_id'] : implode(',',$this->allUserCarrierId));
        $this->userAccountId = (!empty($_POST['user_account_id']) ? $_POST['user_account_id'] : '');

        $toDate = (!empty($_POST['to_date']) ? date('Y-m-d', strtotime($_POST['to_date'])) : '');
        $fromDate = (!empty($_POST['from_date']) ? date('Y-m-d', strtotime($_POST['from_date'])) : '');
        $where = '';
        $trackindDataFilter = new TrackingDataFilter();
        if (!empty($fromDate) && !empty($toDate)) {
            $where = ' WHERE DATE(t.date_created) >= "' . $fromDate . '" AND DATE(t.date_created) <= "' . $toDate . '"';
        }
        $trackingObj = $trackindDataFilter->get_carrier_service_performance($fromDate, $toDate, $this->carrierId, $this->serviceId, $this->countryId);
        
        if (!empty($trackingObj)) {
            //$transitTime;
            $transitDayCal = 0;
            $transitTime = 0;
            $totalNoOfShipment = 0;
            $serviceArr;
            $noOfDaysTransit = 0;
            $countryArr;
            $shipmentServiceArray = [];
            $shipService = [];
            foreach ($trackingObj as $key => $data) {
                //$transitTime[];
                $serviceName = (!empty($data->getName()) ? $data->getName() : '');
                $trackingCreatedDate = explode(',', $data->getGroupDateCreated());
                $trackingStatusCodes = explode(',', $data->getGroupStatusCode());
                $carrierRecived = array_search(148, $trackingStatusCodes);
                $deliveredStatusCount = array_search(122, $trackingStatusCodes);
                $transitTime = (!empty($data->getTransitTime()) ? $data->getTransitTime() : '');
                $totalNoOfShipment = $totalNoOfShipment + 1;
                $serviceId = $data->getServiceId();
                if ($deliveredStatusCount!==FALSE ) {
                    $shipmentServiceArray[$serviceId] = isset($shipmentServiceArray[$serviceId]) ? $shipmentServiceArray[$serviceId] = $shipmentServiceArray[$serviceId]+1 : 1; 
                    $firstIndexCarrierReceived = $trackingCreatedDate[$carrierRecived];
                    $LastIndexDelivery = $trackingCreatedDate[$deliveredStatusCount];

                    $date1 = date_create($firstIndexCarrierReceived);
                    $date2 = date_create($LastIndexDelivery);
                    $diff = date_diff($date2, $date1);
                    $daysOfDelivered = $diff->d;
                    $transitDayCal = $transitDayCal + $daysOfDelivered;
                    $daysOfD[] = $daysOfDelivered;
                    $shipService[$serviceId] = array_sum($daysOfD);
                    
                    $serviceArr[$serviceId] = array(
                        'service' => $serviceName,
                       'notransitdays' => $transitDayCal,
                        'carrier_tranist_time' => $transitTime,
                        'no_of_shipment' => $shipmentServiceArray[$serviceId],
                        'countryname' => $data->getCountryName(),
                        'noofdayDeliver' => $daysOfDelivered
                    );
                }
            }
//            echo '<pre>';
//            print_r($shipService);
//            print_r($serviceArr);
//            echo '</pre>';
//            die();
            //die();
            if (!empty($serviceArr)) {
                $noOfDaysTransit = 0;
                foreach ($serviceArr as $index => $data) {
                    $efficiency = 0;
                    $noOfDaysTransit = $data['notransitdays'] / $data['no_of_shipment'];
                    $transitTime = $data['carrier_tranist_time'];
                    if (!empty($noOfDaysTransit)) {
                        $efficiency = $transitTime / $noOfDaysTransit * 100;
                    }
                    $this->excelServiceNameBarChart[$data['service']] = $data['service'];
                    $this->excelServiceBarChart[] = [$data['service'], number_format((float) $noOfDaysTransit, 2, '.', '')];
                    $this->service_chart[] = array('shiplabel'=>'Total Delivered Shipments',  'noship' => $data['no_of_shipment'], 'average_tranist_time' => 'Average Transit Time', 'service' => $data['service'], 'value' => number_format((float) $noOfDaysTransit, 2, '.', ''), 'transit_time' => (!empty($transitTime))?$transitTime:'0', 'averagedays' => number_format((float) $noOfDaysTransit, 2, '.', '').' days' , 'servicetranist' => $data['service'] . ' (' . $transitTime . ' days)', 'average_effciency_time' => 'Efficiency', 'effciency' => number_format((float) $efficiency, 2, '.', '').'%');
                }
            }
            
            if (isset($_POST['download_file']) && $_POST['download_file'] == "download_excel") {
                
                $servicesNames = [];
                $servicesNames = array_keys($this->excelServiceNameBarChart);
                array_push($servicesNames, '');
                $servicesNames = array_reverse($servicesNames);
               
                $cols = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z'];
                $objPHPExcel = new PHPExcel();
                $objGraphSheet = $objPHPExcel->getActiveSheet();
                $objGraphSheet->setTitle('Graph');
                $objPHPExcel->getActiveSheet()->setCellValue('A18', "Over All Delivered Time Report");
                $objPHPExcel->getActiveSheet()->setCellValue('A20', "Service");
                $objPHPExcel->getActiveSheet()->setCellValue('B20', "T.Time (days)");
                $objPHPExcel->getActiveSheet()->setCellValue('C20', "Total Delivered Shipments");
                $objPHPExcel->getActiveSheet()->setCellValue('D20', "Avg T.Time (days)");
                $objPHPExcel->getActiveSheet()->setCellValue('E20', "Efficiency (%)");
                $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(30);
                $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(30);
                $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(32);
                $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
                $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
                $count =20;
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
                $objPHPExcel->getActiveSheet()->getStyle('A18')->applyFromArray($headerStyle);
                $objPHPExcel->getActiveSheet()->getStyle('B20:F20')->applyFromArray($boldCenterStyle);
                $objPHPExcel->getActiveSheet()->getStyle('A20')->applyFromArray($boldStyle);

                foreach($this->service_chart as $index => $data){
                    $count = $count + 1;
                    $efficiency = str_replace('%','', $data['effciency']);
                    $avgDays = str_replace('days','', $data['averagedays']);
                    $objPHPExcel->getActiveSheet()->setCellValue('A' . $count, $data['service']);
                    $objPHPExcel->getActiveSheet()->setCellValue('B' . $count, $data['transit_time']);
                    $objPHPExcel->getActiveSheet()->setCellValue('C' . $count, $data['noship']);
                    $objPHPExcel->getActiveSheet()->setCellValue('D' . $count, $avgDays);
                    $objPHPExcel->getActiveSheet()->setCellValue('E' . $count, $efficiency);
                    $objPHPExcel->getActiveSheet()->getStyle('B'.$count.':F'.$count)->applyFromArray($centerStyle);
                }
                
                $objGraphDataSheet = $objPHPExcel->createSheet(1);
                $objGraphDataSheet->setTitle('GraphData');
                $objGraphDataSheet = $objPHPExcel->setActiveSheetIndex(1);      
                // bar Chart
                $this->excelServiceBarChart[] = $servicesNames;
                $this->excelServiceBarChart = array_reverse($this->excelServiceBarChart);
                $objGraphDataSheet->fromArray(
                    $this->excelServiceBarChart
                );
                $totalCountries = count($this->excelServiceBarChart);
                $totalServices = count($this->excelServiceBarChart[0]) - 1;
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
                    PHPExcel_Chart_DataSeries::TYPE_BARCHART,		// plotType
                    PHPExcel_Chart_DataSeries::GROUPING_STACKED,	// plotGrouping
                    range(0, count($dataSeriesValues)-1),			// plotOrder
                    $dataseriesLabels,								// plotLabel
                    $xAxisTickValues,								// plotCategory
                    $dataSeriesValues								// plotValues
                );
                $series->setPlotDirection(PHPExcel_Chart_DataSeries::DIRECTION_VERTICAL);
                $plotarea = new PHPExcel_Chart_PlotArea(null, array($series));
                $legend = new PHPExcel_Chart_Legend(PHPExcel_Chart_Legend::POSITION_RIGHT, null, false);
                $title = new PHPExcel_Chart_Title('Over All Average Delivery Time');
                $yAxisLabel = new PHPExcel_Chart_Title('');
                $chart = new PHPExcel_Chart(
                    'chart1',		// name
                    $title,			// title
                    $legend,		// legend
                    $plotarea,		// plotArea
                    true,			// plotVisibleOnly
                    0,				// displayBlanksAs
                    null,			// xAxisLabel
                    $yAxisLabel		// yAxisLabel
                );
                //	Set the position where the chart should appear in the worksheet
                $chart->setTopLeftPosition('A2');
                $chart->setBottomRightPosition('F15');
                $objGraphSheet = $objPHPExcel->setActiveSheetIndex(0);
                //	Add the chart to the worksheet
                 $objPHPExcel->getActiveSheet()->addChart($chart);

                header('Content-Type: application/vnd.ms-excel');
                header('Content-Disposition: attachment;filename=carrier-service-performance-report' . date('d-m-Y') . '.xls'); // file name of excel
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
            var initbarChartSerivceTransit = function () {
                var chart2 = AmCharts.makeChart("barchart_service", {
                    "theme": "light",
                    "type": "serial",
                    "startDuration": 1,
                    "fontFamily": 'Open Sans',
                    "color": '#888',
                    "dataProvider":<?php echo json_encode($this->service_chart); ?>,
                    "valueAxes": [{
                            "position": "left",
                            "axisAlpha": 0,
                            "gridAlpha": 0
                        }],
                    "graphs": [{
                            "balloonText": "<b>[[shiplabel]]:</b> [[noship]]<br/><b>[[average_tranist_time]]: </b>[[averagedays]]<br/><b>[[average_effciency_time]]: </b>[[effciency]]",
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
                    "categoryField": "servicetranist",
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

            var grid = null;
            var DataTableFun = function () {
                    var handleDataTable = function () {
                    var toDate = '<?= (!empty($_POST['to_date']) ? '&toDate=' . $_POST['to_date'] : ''); ?>';
                    var fromDate = '<?= (!empty($_POST['from_date']) ? '&fromDate=' . $_POST['from_date'] : ''); ?>';
                    var search_Carrier_id = '<?= (!empty($_POST['search_Carrier_id']) ? '&search_Carrier_id=' . $_POST['search_Carrier_id'] : ''); ?>';
                    var service_id = '<?= (!empty($_POST['service_id']) ? '&service_id=' . implode(',',$_POST['service_id']) : ''); ?>';
                    var search_Code = '<?= (!empty($_POST['search_Code']) ? '&search_Code=' . $_POST['search_Code'] : ''); ?>';
                    var datatableurl = "carrier_service_performance_report.php?action=carrier_service"+ toDate + fromDate + search_Carrier_id + search_Code  + service_id;
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
                             "paging":   false,
                             "info":     false,
                            "bStateSave": false,
                            "autoWidth": false,
                            "columns": [
                                {"data": "servicename"},
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



            function loadCarrierServices(carrierId, serviceId) {
                $.ajax({
                    type: "POST",
                    url: "carrier_service_performance_report.php",
                    data: {func: "get_carrier_services", carrier_id: carrierId, service_id: serviceId},
                    dataType: "html",
                    success: function (data) {
                        $("#services_span").html(data);
                        $("#service_id").select2();
                        //loadCarrierZones();
                    },
                    error: function () {
                        alert('Service are not loaded.');
                    }
                });
            }

            $(document).ready(function () {
                DataTableFun.init();
                if ($('.date-picker').length > 0) {
                    //init date pickers
                    $('.date-picker').datepicker({
                        autoclose: true
                    });
                }
                // $('#manage-data-table button.filter-submit').click();

                function get_carriers(userAccountId, selected) {
                    $.ajax({
                        type: "POST",
                        url: "carrier_service_performance_report.php?action=get_carrier_services",
                        data: {user_account_id: userAccountId, selected: selected},
                        dataType: "json",
                        success: function (data) {
                            $('#carriers').html(data.carrier_option);
                            $('#carriers').select2();
                            $('#carriers').trigger('change');
                        },
                        error: function () {
                            //alert('error handing here');
                        }
                    });
                }
                var userAccountId = '<?php echo $this->user->getUserAccountId(); ?>';
                var selected = '<?php echo $this->carrierId ?>';
                get_carriers(userAccountId, selected);
                setTimeout(function () {
                    $('.amcharts-chart-div a').remove();
                }, 3000);
                $('#carriers').change(function () {
                    var carrierId = $(this).val();
                    var serviceId = '<?= implode(',', $this->serviceId); ?>';
                    loadCarrierServices(carrierId, serviceId);
                    //loadCarrierZones();
                });
                $('.download_file').click(function () {
                    $('#download_file').val('download_excel');
                    $('#admin_form').submit();
                    setTimeout(function () {
                        $('#download_file').val('');
                    }, 3000);
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
                    Carrier Delivered Time Performance
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
                            $colSpan = '12';
                            //                            if ($this->user->getUserType() != User::USER_TYPE_CLIENT ) {
                            ?>
                            <!--                                <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label >Account</label>
                                                                    <div class="first_form_col">
                                                                        <div class="input-group">
                                                                            <div class="input-group-addon"> <i class="fa fa-user"></i> </div>
                            <?php
                            if ($this->user->getUserType() == User::USER_TYPE_ADMIN) {
                                $accountParentId = 0;
                            } else {
                                $accountParentId = $this->user->getUserAccountId();
                            }

                            $selectedAccount = (!empty($this->userAccountId) ? $this->userAccountId : '');
                            $allowedLevel = 0;
                            if (Permissions::checkFilePermission('hide_subaccount')) {
                                $allowedLevel = 1;
                            }
                            echo Ddl::showTreeDropdown('user_account_id', 'user_account', 'user_account', 'id', $accountParentId, array("active_flag = '1'"), $selectedAccount, "Please Select Account", 'class="form-filter bs-select form-control input-sm" data-live-search="true"', "", "", 'logo', '../images/userlogo/thumbnail/', 'owe_16_',true,$allowedLevel);
                            ?>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>-->
                            <?php
                            //                                $colSpan = '6';
                            //                            }
                            ?>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label >Carrier</label>
                                    <div class="first_form_col">
                                        <div class="input-group">
                                            <div class="input-group-addon"> <i class="fa fa-shopping-cart"></i> </div>
                                            <?php
                                            $search_Carrier_id = (!empty($this->carrierId) ? $this->carrierId : '');
                                            echo Ddl::generateArrayDDL('search_Carrier_id', array("" => "Select Carrier"), $search_Carrier_id, '', 'class="form-filter select2 form-control" ', "", $did_id = 'carriers');
                                            ?>                                            
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label >Services</label>
                                    <div class="first_form_col">
                                        <div class="input-group">
                                            <div class="input-group-addon"> <i class="fa fa-user"></i> </div>
                                            <span id="services_span">
                                                <select name="service_id" id="service_id" class="form-control select2 validate_check" multiple="multiple">
                                                    <option value="">Select Service</option>
                                                </select>
                                            </span>                                                
                                        </div>
                                    </div>
                                </div>
                            </div> 

                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <label class="control-label">Date Ranges </label>
                                <div class="input-group date-picker input-daterange" data-date="20/01/2018" data-date-format="mm/dd/yyyy">
                                    <input type="text" class="form-control" name="from_date" id="from" value="<?= (!empty($_POST['from_date']) ? $_POST['from_date'] : ''); ?>" data-original-title="" title="">
                                    <span class="input-group-addon"> to </span>
                                    <input type="text" class="form-control" name="to_date" id="to" value="<?= (!empty($_POST['to_date']) ? $_POST['to_date'] : ''); ?>" data-original-title="" title=""> 
                                    <input type="hidden" class="" name="download_file" id="download_file" value=""> 

                                </div>
                            </div>
                            <div class="col-md-2">
                                <label class="control-label">&nbsp;</label><br>
                                <button class="btn btn-default" type="submit">Filter Report</button>
                            </div>
                        </div>
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
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane active" id="graphichal_view">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="portlet light bordered">
                                        <div class="portlet-title">
                                            <div class="caption">
                                                <i class="icon-bar-chart font-green-haze"></i>
                                                Overall Average Delivered Time
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
                        <div class="tab-pane" id="tabular">
                            <div class="table-container margin-top-10">
                                <table class="table table-striped table-bordered table-hover" id="manage-data-table">
                                    <thead>
                                        <tr role="row" class="heading">
                                            <th>Service Name</th>
                                            <th>T.Time (days)</th>
                                            <th>Total Delivered Shipments</th>
                                            <th>Avg T.Time (days)</th>
                                            <th>Efficiency ( % )</th>
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

        <?php
    }

}

// class

/* ------------------------------------------------------------------------------ */
// create and render page
$page = new Page(CONFIG_TEMPLATE_ADMIN);
$page->show();
?>