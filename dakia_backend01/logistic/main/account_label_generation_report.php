<?php
// get settings
require_once("../includes/settings/config.inc.php");
require_once("../Classes/PHPExcel.php");
include_classes([   
                    'ivisualcomponent','ddl.inc'
                ],'library');
include_classes([   
                    'iaddress.class',
                    'carrier.class',
                    'carrierfilter.class',
                    'consignment.class',
                    'consignmentfilter.class',
                    'invoices.class',
                    'invoicesfilter.class',
                    'services.class' ,
                    'servicefilter.class',
                    'userservicesrouting.class',
                    'userservicesroutingfilter.class',]);
class Page extends BasePage {
    /*     * *
     * Controller logic
     */
    private $carrierId = '';
    private $serviceId = '';
    private $serAccountId = '';
    private $noRecordFound = '';

    protected function init() {

         $this->breadCrumb['data'] = array(
            'index.php' => Translation::GetCaption("HOME"),
            'account_label_generation_report.php' => "Accounts Label Generation Report"
        );
        $this->user = SessionManager::getUser();

        /*         * DataTable handlings
         */
        if (isset($this->form_vars['action']) && $this->form_vars['action'] == "get_carrier_services") {
            $show_shipnment_account = $this->form_vars['show_shipnment_account'];
            $user_account_id = $this->form_vars['user_account_id'];
            if (!empty($user_account_id)) {
                $return = array();
                $userAccountArry = [$user_account_id];
                if($show_shipnment_account == "all" ) {
                    $userAccountArry = CustomerAccount::accountSubAccount($user_account_id, 0, true);
                } else if($show_shipnment_account == "subaccount") {
                    $userAccountArry = CustomerAccount::accountSubAccount($user_account_id, 0, false);
                }
                $userServicesRoutingFilter = new UserServicesRoutingFilter();
                $userServicesRoutingFilter->addFilterIn("     user_account_id", $userAccountArry);
                $userServiceObj = $userServicesRoutingFilter->getColumnList(" service_id", false, true);
                $serviceIds = array();
                if (count($userServiceObj) > 0) {
                    foreach ($userServiceObj as $service) {
                        $serviceIds[] = $service->getServiceId();
                    }
                }
            }
            $serviceFilterObj = new ServiceFilter();
            if (!empty($serviceIds)) {
                $serviceFilterObj->addFilterIn("ser.id", $serviceIds);
                $services = $serviceFilterObj->getColumnList("ser.id, ser.name, ser.code, ser.carrier_id");
            }
            
            $service_option = "<option value=''>Select Service</option>";
            foreach ($services as $sr) {
                $service_option .= "<option value='" . $sr->getId() . "' class='serviceOption carrier_" . $sr->getCarrierId() . "'>" . $sr->getName() . "</option>";
            }
            
            $carrierIds = array();
            foreach ($services as $ser) {
                if (!in_array($ser->getCarrierId(), $carrierIds)) {
                    $carrierIds[] = $ser->getCarrierId();
                }
            }
            $carrierFilterObj = new CarrierFilter();
            $carrierFilterObj->addFilterIn("        c1.id", $carrierIds);
            $carriers = $carrierFilterObj->getColumnListIn("c1.id,c1.carrier,c1.logo,c1.carrier_display_name");
            
            $carrier_option = "<option value=''>Select Carrier</option>";
            foreach ($carriers as $cr) {
                $carrier_option .= "<option value='" . $cr->getId() . "'>" . $cr->getCarrierDisplayName() . "</option>";
            }
            $return['services_option'] = $service_option;
            $return['carrier_option'] = $carrier_option;
            echo json_encode($return);
            die;
        }
        
        if (isset($this->form_vars['action']) && $this->form_vars['action'] == "download_excel_report") {
            $serviceId = $this->form_vars['service_id'];
            $carrierId = DbAccess3::escape($this->form_vars['search_Carrier_id']);
            $dateType = $this->form_vars['date_type'];
            $fromDate = (!empty($this->form_vars['from_date']) ? date('Y-m-d', strtotime($this->form_vars['from_date'])) : date('Y-m-d', strtotime('-30 days')));
            $toDate = (!empty($this->form_vars['to_date']) ? date('Y-m-d', strtotime($this->form_vars['to_date'])) : date('Y-m-d'));
            $dateFrom = date_create($fromDate);
            $dateTo = date_create($toDate);
            $diffLabelReport = date_diff($dateFrom, $dateTo);
            
               $allouedAcccounts = [];
                $selectedAccountName ="";
                $user_account_id = (empty($this->form_vars['user_account_id']) ? $this->user->getUserAccountId() : $this->form_vars['user_account_id']);
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
                        $allouedAcccounts[] = $user_account_id;
                    } else if ($selectAccountSearchType == 'subaccount') {
                        $allouedAcccounts = CustomerAccount::accountSubAccount($user_account_id, 0, false);
                    }
                    
                        $userAccountObj         = new CustomerAccount($user_account_id);
                        $selectedAccountName    = $userAccountObj->getUserAccount();
                }

                
            if ($diffLabelReport->days <= '30') {
                $cols = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', 'AA', 'AB', 'AC', 'AD', 'AE', 'AF', 'AG', 'AH', 'AI', 'AJ', 'AK', 'AL', 'AM', 'AN', 'AO', 'AP', 'AQ', 'AR', 'AS', 'AT', 'AU', 'AV', 'AW', 'AX', 'AY', 'AZ', 'BA', 'BB', 'BC', 'BD', 'BE', 'BF', 'BG', 'BH', 'BI', 'BJ', 'BK', 'BL', 'BM', 'BN', 'BO', 'BP', 'BQ', 'BR', 'BS', 'BT', 'BU', 'BV', 'BW', 'BX', 'BY', 'BZ', 'CA', 'CB', 'CC', 'CD', 'CE', 'CF', 'CG', 'CH', 'CI', 'CJ', 'CK', 'CL', 'CM', 'CN', 'CO', 'CP', 'CQ', 'CR', 'CS', 'CT', 'CU', 'CV', 'CW', 'CX', 'CY', 'CZ', 'DA', 'DB', 'DC', 'DD', 'DE', 'DF', 'DG', 'DH', 'DI', 'DJ', 'DK', 'DL', 'DM', 'DN', 'DO', 'DP', 'DQ'];
                $objPHPExcel = new PHPExcel();
                $objSheetData = $objPHPExcel->getActiveSheet();
                $objSheetData->setTitle('Account Label Created Report');
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
                        'size' => 15,
                        'name' => 'Calibri',
                    )
                );
                $objPHPExcel->getActiveSheet()->getStyle('A3:B3')->applyFromArray($FontBoldArray);
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A1', 'Account Label Generated Report ('.$toDate.' To '.$fromDate.')' );
                $dateTypeArr = array(
                    'Filtered By: Date Created' => 'dateCreated',
                    'Filtered By: Date Scanned' => 'dateScanned',
                    'Filtered By: Date Delivered' => 'dateDelivered',
                    'Filtered By: Date Dispatched' => 'dateBooked',
                    'Filtered By: Date Label Created' => 'dateLabelCreated',
                );
                $LabelOfFilter = array_search($dateType, $dateTypeArr);
                if(!empty($selectedAccountName)){
                    $LabelOfFilter .= "\nAccount: ".$selectedAccountName; 
                }
                if(!empty($carrierId)){
                    $carrierObj  = new Carrier($carrierId);
                     $LabelOfFilter .= "\nCarrier: ".$carrierObj->getCarrier();
                }
                $objPHPExcel->getActiveSheet()->getStyle('A2')->getAlignment()->setWrapText(true);
                $objPHPExcel->getActiveSheet()->getRowDimension('1')->setRowHeight(-1);  
                
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A2', $LabelOfFilter);
                $objPHPExcel->getActiveSheet()->getStyle('A1')->applyFromArray($headingArray);
                $objPHPExcel->getActiveSheet()->getStyle('A2')->applyFromArray($scondHeadingArray);
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
                        ->setCellValue('A3', 'Account');

                $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(50);
                $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(30);

             
                $consignmentObj = new ConsignmentFilter();
                $createdLabelDataReportObj = $consignmentObj->getAccountWiseLabelCreatedReport($fromDate, $toDate, $allouedAcccounts, $carrierId, $serviceId, $user_account_id, $dateType);
                $serviceName = [];
                $DateArr = [];
                $createLabelReportArr = [];
                if (!empty($createdLabelDataReportObj)) {
                    foreach ($createdLabelDataReportObj as $key => $data) {
                        $createLabelReportArr[$data->getCode()][$data->getDateLabelCreated()] = array(
                            'serviceName' => $data->getServiceName(),
                            'weight' => $data->getWeight(),
                            'totalShipment' => $data->getId(),
                            'date' => $data->getDateLabelCreated()
                        );
                        $serviceName[$data->getCode()] = $data->getServiceName() ." - ". $data->getCode();
                    }
                    $dateRange = [];
                    if (!empty($fromDate)) {
                        $begin = new DateTime($fromDate);
                    }

                    if (!empty($toDate)) {
                        $end = new DateTime($toDate);
                        $end = $end->modify('+1 day');
                    }

                    $period = new DatePeriod(
                            $begin, new DateInterval('P1D'), $end
                    );
                    // Date 
                    foreach ($period as $key => $value) {
                        $DateArr[] = $value->format('d-m-Y');
                    }
/*                    echo "<pre>";
                    print_r($DateArr);
                    print_r();
                    
                    die;*/
                    $DateArr = array_reverse($DateArr,true);
                    // End
                    $finalArray = [];
                    $totalAllserivceArr = [];


                    foreach ($createLabelReportArr as $serviceCode => $dataArr) {
                        foreach ($period as $key => $value) {
                            $dateVal = $value->format('Y-m-d');
                            if (!isset($dataArr[$dateVal])) {
                                $finalArray[$serviceCode][$dateVal] = array(
                                    'serviceName' => $serviceName[$serviceCode],
                                    'weight' => 0.00,
                                    'totalShipment' => 0,
                                    'date' => $dateVal
                                );
                            } else {
                                $finalArray[$serviceCode][$dateVal] = $dataArr[$dateVal];
                            }
                        }
                    }

                    $objPHPExcel->getActiveSheet()->getStyle('A3:CT3')->applyFromArray($styleForReport);
                    $startColIndex = 1;
                    $step = 2;
                    $dataStartRow = 2;
                    $headingIndex = 1;
                    $cellArr = [];
                    foreach ($DateArr as $key => $data) {
                        $objPHPExcel->getActiveSheet()->mergeCells($cols[$startColIndex] . $dataStartRow . ":" . $cols[($startColIndex + $step)] . $dataStartRow);
                        $objPHPExcel->getActiveSheet()->getStyle($cols[$startColIndex] . $dataStartRow)->applyFromArray($FontBoldArray);
                        $objPHPExcel->getActiveSheet()->setCellValue($cols[$startColIndex] . $dataStartRow, $data);

                        $startColIndex += ($step + 1);
                        $objPHPExcel->getActiveSheet()->setCellValue($cols[$headingIndex] . ($dataStartRow + 1), "Number Of Shipment");
                        $objPHPExcel->getActiveSheet()->getColumnDimension($cols[$headingIndex])->setWidth(25);
                        $objPHPExcel->getActiveSheet()->getStyle($cols[$headingIndex] . ($dataStartRow + 1))->applyFromArray($centerStyle);
                        $cellArr[] = $cols[$headingIndex];
                        $headingIndex++;
                        $objPHPExcel->getActiveSheet()->setCellValue($cols[$headingIndex] . ($dataStartRow + 1), "Total Weight");
                        $objPHPExcel->getActiveSheet()->getColumnDimension($cols[$headingIndex])->setWidth(25);
                        $objPHPExcel->getActiveSheet()->getStyle($cols[$headingIndex] . ($dataStartRow + 1))->applyFromArray($centerStyle);
                        $cellArr[] = $cols[$headingIndex];
                        $headingIndex++;
                        $objPHPExcel->getActiveSheet()->setCellValue($cols[$headingIndex] . ($dataStartRow + 1), "Average Weight");
                        $objPHPExcel->getActiveSheet()->getColumnDimension($cols[$headingIndex])->setWidth(25);
                        $objPHPExcel->getActiveSheet()->getStyle($cols[$headingIndex] . ($dataStartRow + 1))->applyFromArray($centerStyle);
                        $cellArr[] = $cols[$headingIndex];
                        $headingIndex++;
                    }
                    $startTotalindex = end(array_keys($cellArr));
                    $startTotalindex = $startTotalindex + 2;
                    $cellForTotalCal = [];
                    for ($i = 1; $i <= 1; $i++) {
                        $objPHPExcel->getActiveSheet()->setCellValue($cols[$startTotalindex] . ($dataStartRow + 1), "Total Shipment Of Particular Account");
                        $objPHPExcel->getActiveSheet()->getColumnDimension($cols[$startTotalindex])->setWidth(38);
                        $objPHPExcel->getActiveSheet()->getStyle($cols[$startTotalindex] . ($dataStartRow + 1))->applyFromArray($FontBoldArray);
                        $cellForTotalCal[] = $cols[$startTotalindex];
                        $startTotalindex = $startTotalindex + 1;
                        $objPHPExcel->getActiveSheet()->setCellValue($cols[$startTotalindex] . ($dataStartRow + 1), "Total Weight Of Particular Account");
                        $objPHPExcel->getActiveSheet()->getColumnDimension($cols[$startTotalindex])->setWidth(38);
                        $objPHPExcel->getActiveSheet()->getStyle($cols[$startTotalindex] . ($dataStartRow + 1))->applyFromArray($FontBoldArray);
                        $cellForTotalCal[] = $cols[$startTotalindex];
                        $startTotalindex = $startTotalindex + 1;
                        $objPHPExcel->getActiveSheet()->setCellValue($cols[$startTotalindex] . ($dataStartRow + 1), "Total Avg Weight Of Particular Account");
                        $objPHPExcel->getActiveSheet()->getColumnDimension($cols[$startTotalindex])->setWidth(38);
                        $objPHPExcel->getActiveSheet()->getStyle($cols[$startTotalindex] . ($dataStartRow + 1))->applyFromArray($FontBoldArray);
                        $cellForTotalCal[] = $cols[$startTotalindex];
                        $startTotalindex = $startTotalindex + 1;
                    }
                    // Service Name and Service Code
                    $dataStartRows = 4;
                    $startTotalindex = end(array_keys($cellArr));
                    $startTotalindex = $startTotalindex + 3;
                    $dayTotals = [];
                    $cell = [];
                    $TotalOfAllShipmentService = 0;
                    $TotalOfAllShipmentWeight = 0;
                    $TotalOfAllShipmentAvgWeight = 0;
                    $totalCellService = [];
                    foreach ($finalArray as $serviceCode => $data) {
                        $avgWeight = 0;
                        $alphabeticIndex = 0;
                        $serviceTotalShipments = 0;
                        $serviceTotalWeight = 0;
                        $serviceAvgWeight = 0;

                        $objPHPExcel->getActiveSheet()->setCellValue($cols[$alphabeticIndex++] . ($dataStartRows), $serviceName[$serviceCode]);
                        $cell[] = ($dataStartRows);
//                        $objPHPExcel->getActiveSheet()->setCellValue($cols[$alphabeticIndex++] . ($dataStartRows), $serviceCode);

                        foreach ($data as $ind => $value) {
                            $serviceTotalShipments += $value['totalShipment'];
                            $serviceTotalWeight += $value['weight'];
                            if ($value['totalShipment'] > 0)
                                $serviceAvgWeight = round($value['weight'] / $value['totalShipment'],2);

                            $objPHPExcel->getActiveSheet()->setCellValue($cols[$alphabeticIndex] . ($dataStartRows), $value['totalShipment']);
                            $objPHPExcel->getActiveSheet()->getStyle($cols[$alphabeticIndex] . ($dataStartRows))->applyFromArray($centerStyle);

                            $alphabeticIndex++;

                            $objPHPExcel->getActiveSheet()->setCellValue($cols[$alphabeticIndex] . ($dataStartRows), $value['weight']);
                            $objPHPExcel->getActiveSheet()->getStyle($cols[$alphabeticIndex] . ($dataStartRows))->applyFromArray($centerStyle);

                            $alphabeticIndex++;

                            $avgWeight = (!empty($value['weight']) && !empty($value['totalShipment'])) ? round($value['weight'] / $value['totalShipment'], 2) : '0.00';

                            $objPHPExcel->getActiveSheet()->setCellValue($cols[$alphabeticIndex] . ($dataStartRows), round($avgWeight,2), 2);
                            $objPHPExcel->getActiveSheet()->getStyle($cols[$alphabeticIndex] . ($dataStartRows))->applyFromArray($centerStyle);

                            $alphabeticIndex++;

                            if (isset($dayTotals[$ind])) {
                                $dayTotals[$ind]['dayTotalShipments'] += $value['totalShipment'];
                                $dayTotals[$ind]['dayTotalWeight'] += $value['weight'];
                            } else {
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
                        $objPHPExcel->getActiveSheet()->setCellValue($cols[++$alphabeticIndex] . ($dataStartRows), $serviceTotalWeight);
                        $objPHPExcel->getActiveSheet()->getStyle($cols[$alphabeticIndex] . ($dataStartRows))->applyFromArray($centerStyle);
                        $totalCellService[] = $cols[$alphabeticIndex];
                        $objPHPExcel->getActiveSheet()->setCellValue($cols[++$alphabeticIndex] . ($dataStartRows), $serviceAvgWeight);
                        $objPHPExcel->getActiveSheet()->getStyle($cols[$alphabeticIndex] . ($dataStartRows))->applyFromArray($centerStyle);
                        $totalCellService[] = $cols[$alphabeticIndex];
                        $dataStartRows++;
                    }
                    $totalCellService = array_unique($totalCellService);
                    $alphaIndexOne = $totalCellService[0];
                    $alphaIndexTwo = $totalCellService[1];
                    $alphaIndexThree = $totalCellService[2];
                    $objPHPExcel->getActiveSheet()->setCellValue($alphaIndexOne . ($dataStartRows), $TotalOfAllShipmentService);
                    $objPHPExcel->getActiveSheet()->getStyle($alphaIndexOne . ($dataStartRows))->applyFromArray($centerStyle);
                    $objPHPExcel->getActiveSheet()->setCellValue($alphaIndexTwo . ($dataStartRows), $TotalOfAllShipmentWeight);
                    $objPHPExcel->getActiveSheet()->getStyle($alphaIndexTwo . ($dataStartRows))->applyFromArray($centerStyle);
                    $objPHPExcel->getActiveSheet()->setCellValue($alphaIndexThree . ($dataStartRows), $TotalOfAllShipmentAvgWeight);
                    $objPHPExcel->getActiveSheet()->getStyle($alphaIndexThree . ($dataStartRows))->applyFromArray($centerStyle);

                    $Total = end($cell);
                    $Total = $Total + 1;
                    $objPHPExcel->getActiveSheet()->setCellValue('A' . $Total, 'Total');
                    $objPHPExcel->getActiveSheet()->getStyle('A' . $Total)->applyFromArray($boldFont);
                    $alphabeticIndex = 0;

                    foreach ($dayTotals as $date => $dateTotals) {
                        $dayTotalWeight = $dateTotals['dayTotalWeight'];
                        $dayTotalShipments = $dateTotals['dayTotalShipments'];
                        $dayAvgWeight = 0;
                        if ($dayTotalShipments > 0)
                            $dayAvgWeight = $dayTotalWeight / $dayTotalShipments;

                        $objPHPExcel->getActiveSheet()->setCellValue($cols[++$alphabeticIndex] . ($dataStartRows), $dayTotalShipments);
                        $objPHPExcel->getActiveSheet()->getStyle($cols[$alphabeticIndex] . ($dataStartRows))->applyFromArray($centerStyle);

                        $objPHPExcel->getActiveSheet()->setCellValue($cols[++$alphabeticIndex] . ($dataStartRows), $dayTotalWeight);
                        $objPHPExcel->getActiveSheet()->getStyle($cols[$alphabeticIndex] . ($dataStartRows))->applyFromArray($centerStyle);

                        $objPHPExcel->getActiveSheet()->setCellValue($cols[++$alphabeticIndex] . ($dataStartRows), $dayAvgWeight);
                        $objPHPExcel->getActiveSheet()->getStyle($cols[$alphabeticIndex] . ($dataStartRows))->applyFromArray($centerStyle);
                    }
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
                } else {
                    $this->noRecordFound = 'No record found to download';
                }
            } else {
                $this->noRecordFound = 'Date ranges filter must be less than and euqal to 30 days. ';
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
        <script src="../assets/global/plugins/bootstrap-sweetalert/sweetalert.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js" type="text/javascript"></script>
        <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
        <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

        <script src="../assets/global/scripts/datatable.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/datatables/datatables.min.js" type="text/javascript"></script>
        <script src="../assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js" type="text/javascript"></script>


        <script type="text/javascript">
            $(document).ready(function () {
                //DataTableFun.init();
                if ($('.date-picker').length > 0) {
                    //init date pickers
                    var today = new Date();
                    $('.date-picker').datepicker({
                        format: 'dd-mm-yyyy',
                        autoclose: true,
                        endDate: 'today',
                        maxDate: today
                    }).on('changeDate', function (ev) {
                        $(this).datepicker('hide');
                    });

                }

                var userAccountId = '<?php echo $this->user->getUserAccountId() ?>';
                var show_shipnment_account = $('#selectAccountSearchType').val();
                get_carriers(userAccountId, show_shipnment_account);
                
                $('#selectAccountSearchType').change(function () {
                    userAccountId = $('#user_account_id').val();
                    get_carriers(userAccountId,show_shipnment_account);
                });
                
                $('#user_account_id').change(function () {
                    userAccountId = $(this).val();
                    get_carriers(userAccountId,show_shipnment_account);
                });
                
                $('#carriers').change(function () {
                    var carrierId = $(this).val();
                    change_carriers(carrierId);

                });
                
                setTimeout(function () {
                    $('.msg').remove();
                }, 20000);
                
                $('.download_file_btn').click(function () {
                    $('#account_label_generation_report').submit();
                });
            });

            function get_carriers(userAccountId,show_shipnment_account) {
                if(userAccountId > 0 && show_shipnment_account != "") {
                    $.ajax({
                        type: "POST",
                        url: "account_label_generation_report.php",
                        data: {user_account_id: userAccountId,show_shipnment_account: show_shipnment_account,action: "get_carrier_services"},
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
                    Accounts Label Generation Report
                </div>
            </div>
            <div class="portlet light bordered">
                <div class="portlet-title">
                    <div class="caption">
                        <span id="account_name"></span>Report Filters
                    </div>
                </div>
                <?php if (!empty($this->noRecordFound)) { ?>
                    <div class="col-md-12 alert alert-danger msg">
                        <?php echo $this->noRecordFound; ?>
                    </div>
                <?php } ?> 
                <div class="portlet-body">
                    <form action="account_label_generation_report.php" id="account_label_generation_report" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="action" value="download_excel_report" >
                        <div class="row">
                            <?php
                            $colSpan = '6';
                            if ($this->user->getUserType() != USER::USER_TYPE_CLIENT) {
                                $colSpan = '3';
                                ?>
                                <div class="col-md-<?php echo $colSpan; ?>">
                                    
                                    <div class="form-group ">
                                        <div class="has-float-label input-icon right">
                                        
                                        <?php
                                        $showShipments = array(
                                            'all' => "Selected Account & Sub Accounts",
                                            'own' => "Selected Account",
                                            'subaccount' => "Sub Accounts",
                                        );
                                        echo Ddl::generateArrayDDL('selectAccountSearchType', $showShipments, '', '', 'class="form-filter select2 form-control" ', "", 'selectAccountSearchType');
                                        ?>
                                        <label class="label-account">Show Shipment </label>
                                    </div>
                                     </div>
                                </div>
                                <div class="col-md-<?php echo $colSpan; ?>">
                                    
                                    <div class="form-group ">
                                        <div class="has-float-label input-icon right">
                                        
                                        <div id="user_content">
                                            <?php
                                            $accountParentId = 0;
                                            $includeParent = true;
                                            if ($this->user->getUserType() != User::USER_TYPE_ADMIN) {
                                                $accountParentId = $this->user->getUserAccountId();
                                                $includeParent = false;
                                            }
                                            $selectedAccount = $this->user->getUserAccountId();
                                            $allowedLevel = 0;
                                            if (Permissions::checkFilePermission('hide_subaccount')) {
                                                $allowedLevel = 1;
                                            }
                                            ?>
                                            <?php echo Ddl::showTreeDropdown('user_account_id', 'user_account', 'user_account', 'id', $accountParentId, array("active_flag = '1'"), $selectedAccount, "Please Select Account", 'class="form-filter bs-select form-control" data-live-search="true"', "", "", 'logo', '../images/userlogo/thumbnail/', 'owe_16_', true, $allowedLevel); ?><label class="label-account">Select Account</label>
                                        </div>
                                         </div>
                                    </div>
                                </div>
                            <?php } ?>
                            <div class="col-md-<?php echo $colSpan; ?>">
                                
                                <div class="form-group ">
                                    <div class="has-float-label input-icon right">
                                   
                                    <div id="carriers_div">
                                        <?php echo Ddl::generateArrayDDL('search_Carrier_id', array("" => "Select Carrier"), '', '', 'class="form-filter select2 form-control" ', "", 'carriers'); ?> <label class="label-account">Select Carrier</label>
                                    </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-<?php echo $colSpan; ?>">
                                
                                <div class="form-group ">
                                     <div class="has-float-label input-icon right">
                                 
                                    <div id="service_div">
                                        <?php echo Ddl::generateArrayDDL('service_id[]', array("" => "Select Service"), '', '', ' rel="tooltip" title="Select Service" class="form-filter select2 form-control"  multiple="multiple"', "", $dd_id = 'service_id'); ?>
                                           <label class="label-account">Select Service</label>
                                    </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-<?php echo $colSpan; ?>">
                              
                                <div class="form-group ">
                                    <div class="has-float-label">
                                    
                                    <select id="date_type" name="date_type" class="form-filter bs-select form-control" title="" placeholder="" tabindex="-1" aria-hidden="true" data-original-title="">
                                        <option value="dateCreated" <?= (!empty($this->dateType) && $this->dateType == 'dateCreated' ? 'selected="selected"' : '') ?>>Date Created</option>
                                        <option value="dateLabelCreated" <?= (!empty($this->dateType) && $this->dateType == 'dateLabelCreated' ? 'selected="selected"' : '') ?>>Date label Created</option>
                                        <option value="dateBooked" <?= (!empty($this->dateType) && $this->dateType == 'dateBooked' ? 'selected="selected"' : '') ?>>Date Dispatched</option>
                                        <option value="dateDelivered" <?= (!empty($this->dateType) && $this->dateType == 'dateDelivered' ? 'selected="selected"' : '') ?>>Date Delivered</option>
                                        <option value="dateScanned" <?= (!empty($this->dateType) && $this->dateType == 'dateScanned' ? 'selected="selected"' : '') ?>>Date Scanned</option>
                                    </select>
                                      <label class="label-account">Date Type </label>
                                </div>
                                </div>
                            </div>
                            <div class="col-md-<?php echo $colSpan; ?>">
                                <div class="form-group ">
                                     <div class="has-float-label input-icon right">

                              
                                <div class="input-group date-picker input-daterange" data-date="20/01/2018" data-date-format="mm/dd/yyyy">
                                    <input type="text" class="form-control" name="from_date" id="from" value="<?php echo (!empty($_POST['from_date']) ? $_POST['from_date'] : ''); ?>" >
                                    <span class="input-group-addon"> to </span>
                                    <input type="text" class="form-control" name="to_date" id="to" value="<?php echo (!empty($_POST['to_date']) && is_integer((int)$_POST['to_date'])) ? (int)$_POST['to_date'] : ''; ?><?php echo (!empty($_POST['to_date']) && strtotime($_POST['to_date'] == true)) ? $_POST['to_date'] : ''; ?>">
                                </div>   <label class="control-label" data-toggle="tooltip">Date Ranges </label>
                            </div>
                             </div>
                            </div>
                            <div class="col-md-<?php echo $colSpan; ?>">
                                <label class="control-label">&nbsp;</label><br>
                                <button type="submit" class="btn blue download_file_btn"><i class="fa fa-download"></i> Download Excel File</button>
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