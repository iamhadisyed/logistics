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
        if(!Permissions::checkFilePermission('ebay_outbound_report.php'))
                util_redirect ("index.php");
         $this->breadCrumb['data'] = array(
            'index.php' => Translation::GetCaption("HOME"),
            'ebay_outbound_report.php' => "Ebay MAWB Outbound Report"
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
            $mawbNumber = trim($this->form_vars['mawb_number']);
            if($mawbNumber != ""){
                $mawbNumbers = nl2br($mawbNumber);
                $MawbNumbersArray = explode('<br />', $mawbNumbers);
            }
            $serviceId = $this->form_vars['service_id'];
            $carrierId = DbAccess3::escape($this->form_vars['search_Carrier_id']);
            $dateType = $this->form_vars['date_type'];
            $fromDate = (!empty($this->form_vars['from_date']) ? date('Y-m-d', strtotime($this->form_vars['from_date'])) : "");
            $toDate = (!empty($this->form_vars['to_date']) ? date('Y-m-d', strtotime($this->form_vars['to_date'])) : "");
            
               
                
            //if ($diffLabelReport->days <= '30') 
            {
               
                $objPHPExcel = new PHPExcel();
                $objSheetData = $objPHPExcel->getActiveSheet();
                $objSheetData->setTitle('UK Outbound Report');
                
                $dataStyle = array(
                    'font' => array(
                        'size' => 10
                    ),
                    'alignment' => array(
                        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER
                    ),
                );
                
                $styleForReport = array(
                    'font' => array(
                        'bold' => true,
                        'size' => 12
                    ),
                    'alignment' => array(
                        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                        'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
                    ),
                    'borders' => array(
                        'allborders' => array(
                            'style' => PHPExcel_Style_Border::BORDER_MEDIUM,
                        )
                    ),
                    'fill' => array(
                        'type' => PHPExcel_Style_Fill::FILL_SOLID,
                        'color' => array('rgb' => 'D3D3D3')
                    )
                );
                $objPHPExcel->getActiveSheet()->getStyle('A1:U1')->applyFromArray($styleForReport);
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A1', 'First mile Tracking No');
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('B1', 'OWE Super Tracking No');
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('C1', 'Last Mile order number');
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('D1', 'Mawb');
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('E1', 'Destination');
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('F1', 'Parcel Drop off to UPS shop time');
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('G1', 'A-scan time/Parcel scanning time');
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('H1', 'Carton delivered to UPS Hub time');
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('I1', 'OWE pick up from UPS Hub time');
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('J1', 'Carton RDC in(warehouse internal check)');
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('K1', 'Parcel RDC in(Time of arrival at OWE warehouse)');
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('L1', 'Last mile order print time');
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('M1', 'RDC Out(Time of depart of OWE warehouse');
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('N1', 'Destination port');
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('O1', 'Actual Flight departure');
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('P1', 'Actual Flight Arrival');
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('Q1', 'Leg 1 & 2 Total Transit (Days)');
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('R1', 'Customs Cleared');
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('S1', 'Delivered');
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('T1', '3rd Leg SLA (Arrived Destination to Delivered)');
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('U1', 'Total End To End SLA (UPS drop off to Delivered)');
                

                foreach(range('A','U') as $columnID) {
                    $objPHPExcel->getActiveSheet()->getColumnDimension($columnID)
                        ->setAutoSize(true);
                }
                //$objPHPExcel->getActiveSheet()->getColumnDimension('A:M')->setWidth(50);
                $objPHPExcel->getActiveSheet()->getRowDimension('1')->setRowHeight(30);

                $consignmentObj = new ConsignmentFilter();
                $createdLabelDataReportObj = $consignmentObj->getMawbOutboundReport($fromDate, $toDate, $carrierId, $serviceId, $user_account_id, $dateType, $MawbNumbersArray);
                if (count($createdLabelDataReportObj) > 0) {
                    


                    $startColIndex = 0;
                    $step = 2;
                    $dataStartRow = 2;
                    $headingIndex = 1;
                    $cellArr = [];
                    foreach ($createdLabelDataReportObj as $key => $data) {
                        
                        $objPHPExcel->getActiveSheet()->getStyle('A'.$dataStartRow.':U'.$dataStartRow)->applyFromArray($dataStyle);
                        $objPHPExcel->getActiveSheet()->setCellValueExplicit('A'.$dataStartRow, $data->first_mile_order_no, PHPExcel_Cell_DataType::TYPE_STRING);
                        $objPHPExcel->getActiveSheet()->setCellValueExplicit('B'.$dataStartRow, $data->owe_super_tracking_no,PHPExcel_Cell_DataType::TYPE_STRING);
                        $objPHPExcel->getActiveSheet()->setCellValueExplicit('C'.$dataStartRow, $data->last_mile_tracking_no,  PHPExcel_Cell_DataType::TYPE_STRING);
                        $objPHPExcel->getActiveSheet()->setCellValue('D'.$dataStartRow, $data->mawb_number);
                        $objPHPExcel->getActiveSheet()->setCellValue('E'.$dataStartRow, $data->destination);
                        $objPHPExcel->getActiveSheet()->setCellValue('F'.$dataStartRow, date("d/m/Y H:i" , strtotime($data->parcel_dropoff_ups_shop)));
                        $objPHPExcel->getActiveSheet()->setCellValue('G'.$dataStartRow, date("d/m/Y H:i" , strtotime($data->parcel_scanning_time)));
                        $objPHPExcel->getActiveSheet()->setCellValue('H'.$dataStartRow, date("d/m/Y H:i" , strtotime($data->carton_delivery_to_hub_scan)));
                        $objPHPExcel->getActiveSheet()->setCellValue('I'.$dataStartRow, date("d/m/Y H:i" , strtotime($data->owe_pick_up_ups_hub)));
                        $objPHPExcel->getActiveSheet()->setCellValue('J'.$dataStartRow, date("d/m/Y H:i" , strtotime($data->time_arrival_owe_warehouse))); //Carton RDC in(warehouse internal check)
                        $objPHPExcel->getActiveSheet()->setCellValue('K'.$dataStartRow, date("d/m/Y H:i" , strtotime($data->time_arrival_owe_warehouse)));
                        $objPHPExcel->getActiveSheet()->setCellValue('L'.$dataStartRow, date("d/m/Y H:i", $data->date_label_created));
                        $objPHPExcel->getActiveSheet()->setCellValue('M'.$dataStartRow, date("d/m/Y H:i" , strtotime($data->time_departed_owe_warehouse)));
                        $objPHPExcel->getActiveSheet()->setCellValue('N'.$dataStartRow, $data->destinationport);
                        $objPHPExcel->getActiveSheet()->setCellValue('O'.$dataStartRow, date("d/m/Y H:i" , strtotime($data->actual_flight_departure)));
                        $objPHPExcel->getActiveSheet()->setCellValue('P'.$dataStartRow,  ($data->actual_flight_arrival != '') ? date("d/m/Y H:i" , strtotime($data->actual_flight_arrival)) : '');
                        $endToEndSLA = $this->getWorkdays($data->parcel_dropoff_ups_shop, $data->actual_flight_arrival, false, "YYYY-MM-DD H:I:S");
                       
                        $objPHPExcel->getActiveSheet()->setCellValue('Q'.$dataStartRow, $endToEndSLA); //ETA
                        $objPHPExcel->getActiveSheet()->setCellValue('R'.$dataStartRow, $data->custom_clearance); //Custom Clearance 
                        $objPHPExcel->getActiveSheet()->setCellValue('S'.$dataStartRow, $data->buyer_delivered); //Delivered
                        $slaFinalLeg = $this->getWorkdays($data->actual_flight_arrival, $data->buyer_delivered, false, "YYYY-MM-DD H:I:S");
                        $objPHPExcel->getActiveSheet()->setCellValue('T'.$dataStartRow, $slaFinalLeg); //Delivered
                        $last_status_date = $data->last_status_date;
                        $buyer_delivered = ((trim($data->buyer_delivered) == '' )?trim($data->buyer_delivered) : '');
                        if($buyer_delivered != "")
                            $endToEndSLA = $this->getWorkdays($data->parcel_dropoff_ups_shop, $buyer_delivered, false, "YYYY-MM-DD H:I:S");
                        else
                            $endToEndSLA = $this->getWorkdays($data->parcel_dropoff_ups_shop, $last_status_date, false, "YYYY-MM-DD H:I:S");
                        $objPHPExcel->getActiveSheet()->setCellValue('U'.$dataStartRow, $endToEndSLA); //Delivered
                        $dataStartRow++;

                        
                    }
                    header('Content-Type: application/vnd.ms-excel');
                    header('Content-Disposition: attachment;filename=weekly_outboud_report_' . date('d-m-Y') . '.xlsx'); // file name of excel
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
           
            }
        }
        
        else if (isset($this->form_vars['action']) && $this->form_vars['action'] == "ebay_order_volume_report") {
            $mawbNumber = trim($this->form_vars['mawb_number']);
            if($mawbNumber != ""){
                $mawbNumbers = nl2br($mawbNumber);
                $MawbNumbersArray = explode('<br />', $mawbNumbers);
            }
            $serviceId = $this->form_vars['service_id'];
            $carrierId = DbAccess3::escape($this->form_vars['search_Carrier_id']);
            $dateType = $this->form_vars['date_type'];
            $fromDate = (!empty($this->form_vars['from_date']) ? date('Y-m-d', strtotime($this->form_vars['from_date'])) : "");
            $toDate = (!empty($this->form_vars['to_date']) ? date('Y-m-d', strtotime($this->form_vars['to_date'])) : "");
            
               
                
            //if ($diffLabelReport->days <= '30') 
            {
               
                $objPHPExcel = new PHPExcel();
                $objSheetData = $objPHPExcel->getActiveSheet();
                $objSheetData->setTitle('Order Volume Daily Report');
                
                $dataStyle = array(
                    'font' => array(
                        'size' => 10
                    ),
                    'alignment' => array(
                        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER
                    ),
                );
                
                $styleForHeadingReport = array(
                    'font' => array(
                        'bold' => true,
                        'size' => 12,
                        'color' => array('rgb' => 'FFFFF')
                    ),
                    'alignment' => array(
                        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                        'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
                    ),
                    'borders' => array(
                        'allborders' => array(
                            'style' => PHPExcel_Style_Border::BORDER_THIN,
                        )
                    ),
                    'fill' => array(
                        'type' => PHPExcel_Style_Fill::FILL_SOLID,
                        'color' => array('rgb' => '1F497D')
                    )
                    
                );
                
                $styleForReport = array(
                    'font' => array(
                        'bold' => true,
                        'size' => 11,
                        'color' => array('rgb' => 'FFFFFF')
                    ),
                    'alignment' => array(
                        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                        'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
                    ),
                    'borders' => array(
                        'allborders' => array(
                            'style' => PHPExcel_Style_Border::BORDER_THIN,
                        )
                    ),
                    'fill' => array(
                        'type' => PHPExcel_Style_Fill::FILL_SOLID,
                        'color' => array('rgb' => '1F497D')
                    )
                );
                
                /*
                 * Header
                 */
                $objPHPExcel->getActiveSheet()->getStyle('A1:J1')->applyFromArray($styleForHeadingReport);
                $objPHPExcel->getActiveSheet()->mergeCells('A1:J1');
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A1', 'Order Volume Daily Report');
                
                /*
                 * Country Header
                 */
                $objPHPExcel->getActiveSheet()->getStyle('A2:K2')->applyFromArray($dataStyle);
                $objPHPExcel->getActiveSheet()->mergeCells('D2:F2');
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('D2', 'USA');
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('G2', 'DE');
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('H2', 'CA');
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('I2', 'AU');
                
                /*
                 * Report Header
                 */
                $objPHPExcel->getActiveSheet()->getStyle('A3:J3')->applyFromArray($styleForReport);
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('B3', 'Date');
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('B3', 'Delivery Method');
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('C3', 'Product');
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('D3', 'ORD');
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('E3', 'JFK');
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('F3', 'LAX');
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('G3', 'AMS');
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('H3', 'YVR');
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('I3', 'SYD');
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('J3', 'Total');
                
                
               foreach(range('A','C') as $columnID) {
                    $objPHPExcel->getActiveSheet()->getColumnDimension($columnID)->setWidth(20);
                }
                foreach(range('D','J') as $columnID) {
                    $objPHPExcel->getActiveSheet()->getColumnDimension($columnID)->setWidth(10);
                }
                $objPHPExcel->getActiveSheet()->getRowDimension('1')->setRowHeight(30);

                $consignmentObj = new ConsignmentFilter();
                $createdLabelDataReportObj = $consignmentObj->getMawbOutboundReport($fromDate, $toDate, $carrierId, $serviceId, $user_account_id, $dateType, $MawbNumbersArray, '', true);
                if (count($createdLabelDataReportObj) > 0) {
                
                    $startColIndex = 0;
                    $step = 2;
                    $dataStartRow = 4;
                    $headingIndex = 1;
                    $cellArr = [];
                    foreach ($createdLabelDataReportObj as $key => $data) {
                        if($key == 0){
                            $owepickupDate = date("Y-m-d", strtotime($data->owe_pick_up_ups_hub));
                        }
                        $objPHPExcel->getActiveSheet()->getStyle('A'.$dataStartRow.':K'.$dataStartRow)->applyFromArray($dataStyle);
                        $objPHPExcel->getActiveSheet()->setCellValueExplicit('A'.$dataStartRow, date("d/m/Y" , strtotime($data->owe_pick_up_ups_hub)));
                        $objPHPExcel->getActiveSheet()->setCellValueExplicit('B'.$dataStartRow, "Drop off");
                        $objPHPExcel->getActiveSheet()->setCellValueExplicit('C'.$dataStartRow, "Standard");
                        $destinationWarehouse = strtolower($data->destination_warehouse);
                        $totalShipment = $data->total_shipment;
                        $totalforDay += $data->total_shipment;
                        
                        if($destinationWarehouse == "ord-mix" || $destinationWarehouse == "ord-pa"){
                            $cellValue = $objPHPExcel->getActiveSheet()->getCell('D'.$dataStartRow)->getValue();
                            $objPHPExcel->getActiveSheet()->setCellValue('D'.$dataStartRow, $totalShipment + $cellValue);
                        }
                        if($destinationWarehouse == "jfk-mix" || $destinationWarehouse == "jfk-pa"){
                            $cellValue = $objPHPExcel->getActiveSheet()->getCell('E'.$dataStartRow)->getValue();
                            $objPHPExcel->getActiveSheet()->setCellValue('E'.$dataStartRow, $totalShipment + $cellValue);
                        }
                        if($destinationWarehouse == "lax-mix")
                            $objPHPExcel->getActiveSheet()->setCellValue('F'.$dataStartRow, $totalShipment);
                        if($destinationWarehouse == "ams-her" || $destinationWarehouse == "ams-dhl"){
                            $cellValue = $objPHPExcel->getActiveSheet()->getCell('G'.$dataStartRow)->getValue();
                            $objPHPExcel->getActiveSheet()->setCellValue('G'.$dataStartRow, $totalShipment + $cellValue);
                        }
                        if($destinationWarehouse == "yvr")
                            $objPHPExcel->getActiveSheet()->setCellValue('H'.$dataStartRow, $totalShipment);
                        if($destinationWarehouse == "syd")
                            $objPHPExcel->getActiveSheet()->setCellValue('I'.$dataStartRow, $totalShipment);
                        
                        
                        $objPHPExcel->getActiveSheet()->setCellValue('J'.$dataStartRow, $totalforDay);
                        
                        if($owepickupDate != date("Y-m-d", strtotime($data->owe_pick_up_ups_hub))){
                            $dataStartRow++;
                            $owepickupDate = date("Y-m-d", strtotime($data->owe_pick_up_ups_hub));
                        }
                        
                    }
                    $totalRow = $dataStartRow+2;
                    $objPHPExcel->getActiveSheet()->getStyle('A'.$totalRow.':J'.$totalRow)->applyFromArray($styleForReport);
                    $objPHPExcel->getActiveSheet()->mergeCells('A'.$totalRow.':C'.$totalRow);
                    $objPHPExcel->getActiveSheet()->setCellValue("A".$totalRow, "Total ")
                                                        ->setCellValue("D".$totalRow, "=SUM(D4:D".$dataStartRow.")")
                                                        ->setCellValue("E".$totalRow, "=SUM(E4:E".$dataStartRow.")")
                                                        ->setCellValue("F".$totalRow, "=SUM(F4:F".$dataStartRow.")")
                                                        ->setCellValue("G".$totalRow, "=SUM(G4:G".$dataStartRow.")")
                                                        ->setCellValue("H".$totalRow, "=SUM(H4:H".$dataStartRow.")")
                                                        ->setCellValue("I".$totalRow, "=SUM(I4:I".$dataStartRow.")")
                                                        ->setCellValue("J".$totalRow, "=SUM(J4:J".$dataStartRow.")");

                    header('Content-Type: application/vnd.ms-excel');
                    header('Content-Disposition: attachment;filename=order_volume_report_' . date('d-m-Y') . '.xlsx'); // file name of excel
                    header('Cache-Control: max-age=0');
                    header('Cache-Control: max-age=1');
                    header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
                    header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
                    header('Cache-Control: cache, must-revalidate');
                    header('Pragma: public'); // HTTP/1.0

                    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
                    $objWriter->setPreCalculateFormulas();
                    $objWriter->save('php://output');
                    exit;
                } else {
                    $this->noRecordFound = 'No record found to download';
                }
           
            }
        }
        
        
        
    }
    function getWorkdays($date1, $date2, $workSat = FALSE, $patron = NULL) {
            if(trim($date1)=='' || $date2 == '')
            {
                return '';
            }
        if (!defined('SATURDAY'))
            define('SATURDAY', 6);
        if (!defined('SUNDAY'))
            define('SUNDAY', 0);
        // Array of all public festivities
        $publicHolidays = array('01-01', '01-06', '04-25', '05-01', '06-02', '08-15', '11-01', '12-08', '12-25', '12-26');
        // The Patron day (if any) is added to public festivities
        if ($patron) {
            $publicHolidays[] = $patron;
        }
        /*
         * Array of all Easter Mondays in the given interval
         */
        $yearStart = date('Y', strtotime($date1));
        $yearEnd = date('Y', strtotime($date2));
        for ($i = $yearStart; $i <= $yearEnd; $i++) {
            $easter = date('Y-m-d', easter_date($i));
            list($y, $m, $g) = explode("-", $easter);
            $monday = mktime(0, 0, 0, date($m), date($g) + 1, date($y));
            $easterMondays[] = $monday;
        }
        $start = strtotime($date1);
        $end = strtotime($date2);
        $workdays = 0;
        for ($i = $start; $i <= $end; $i = strtotime("+1 day", $i)) {
            $day = date("w", $i);  // 0=sun, 1=mon, ..., 6=sat
            $mmgg = date('m-d', $i);
            if ($day != SUNDAY &&
                    !in_array($mmgg, $publicHolidays) &&
                    !in_array($i, $easterMondays) &&
                    !($day == SATURDAY && $workSat == FALSE)) {
                $workdays++;
            }
        }
        return intval($workdays);
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
                    $('#action').val("download_excel_report");
                    $('#ebay_outbound_report').submit();
                });
                
                $('.download_order_volume').click(function () {
                    $('#action').val("ebay_order_volume_report");
                    $('#ebay_order_volume_report').submit();
                });
                
            });

            function get_carriers(userAccountId,show_shipnment_account) {
                if(userAccountId > 0 && show_shipnment_account != "") {
                    $.ajax({
                        type: "POST",
                        url: "ebay_outbound_report.php",
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
                    Ebay Outbound MAWB Report
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
                    <form action="ebay_outbound_report.php" id="ebay_outbound_report" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="action" id="action" value="download_excel_report" >
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

                              
                                <div class="input-group date-picker input-daterange" data-date="20-01-2018" data-date-format="dd-mm-yyyy">
                                    <input type="text" class="form-control" name="from_date" id="from" value="<?php echo (!empty($_POST['from_date']) ? $_POST['from_date'] : ''); ?>" >
                                    <span class="input-group-addon"> to </span>
                                    <input type="text" class="form-control" name="to_date" id="to" value="<?php echo (!empty($_POST['to_date'])) ? $_POST['to_date'] : ''; ?><?php echo (!empty($_POST['to_date']) && strtotime($_POST['to_date'] == true)) ? $_POST['to_date'] : ''; ?>">
                                </div>   <label class="control-label" data-toggle="tooltip">Date Ranges </label>
                            </div>
                             </div>
                            </div>
                            <div class="col-md-<?php echo $colSpan; ?>">
                                <div class="form-group">
                                    <div class="has-float-label input-icon right">
                                        <i class="fa fa-file"></i>
                                        <textarea name="mawb_number" id="bag_number" type="text" placeholder="MAWB Number" rel="tooltip" data-original-title="MAWB Number" class="form-control form-filter" style="resize: none; height: 143px;"></textarea>
                                        <label for="mawb_number" class="label-account">MAWB Number</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <label class="control-label">&nbsp;</label><br>
                                <button type="submit" class="btn blue download_file_btn"><i class="fa fa-download"></i> Download Ebay Outbound Report</button>
                                <button type="submit" class="btn blue download_order_volume"><i class="fa fa-download"></i> Download Ebay Order Volume Report</button>
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