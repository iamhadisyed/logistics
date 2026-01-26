<?php
//session_start();
set_time_limit(0);

require_once("../includes/settings/config.inc.php");
include '../includes/3rdparty/phpexcel/PHPExcel.php';

if(isset($_POST) && !empty($_POST['tracking_nums'])){
    $tracking_numArr = explode("|", $_POST['tracking_nums']);
    $exportType = $_POST['export_type'];
    if($exportType == 'last'){
        $csvContents = 'Tracing No.,HAWB,Origin Country,Destination Country,Service,Company,Date Time,Status,Track Point,Event Content,Other'."\r\n";
        foreach ($tracking_numArr as $tracking_number) {
            $tracking_number = ParseTrackingNumber::Parse($tracking_number);
            $trackingData = array();
            if (isset($_SESSION["TRACKING_DATA"][$tracking_number])) {
                $trackingData = $_SESSION["TRACKING_DATA"][$tracking_number];
            } else {
                header("location:multitracking.php");
            }
            $csvContents .= "=\"" .$trackingData['Tracking_Number']. "\"" .",".$trackingData['Hawb'].",".$trackingData['Origin_Country'].",".$trackingData['Destination_Country'].",".$trackingData['Service'].",".$trackingData['Company'].",".$trackingData['trackingStatusDateTime'].",".(@$trackingData['trackingStatus'] == '' ? 'In Transit' : @$trackingData['trackingStatus']).",".$trackingData['trackingTrackPoint'].",".$trackingData['trackingStatusDesc'].",".""."\r\n";
        }
        header('Content-Type: application/csv');
        header('Content-Disposition: attachment; filename="tracking_result_last.csv"');
        echo $csvContents;
        exit();
    }else {
        $objPHPExcel = new PHPExcel();
        // Set properties
        $objPHPExcel->getProperties()->setCreator("One World Express")
            ->setLastModifiedBy("One World Express")
            ->setTitle("Shipments Tracking Result")
            ->setSubject("Tracking Result")
            ->setDescription("Shipments Tracking Result")
            ->setKeywords("Tracking")
            ->setCategory("Tracking");

        //Setting the default style of a workbook
        $objPHPExcel->getDefaultStyle()->getFont()->setName('Arial');
        $objPHPExcel->getDefaultStyle()->getFont()->setSize(10);

        $objPHPExcel->getActiveSheet()
            ->getStyle('A:H')
            ->getNumberFormat()
            ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);


        $objPHPExcel->getActiveSheet()->mergeCells('A1:N1');
        $objPHPExcel->getActiveSheet()->setCellValue('A1', 'Tracking Result');

        //    $objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setSize(18);
        $objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->mergeCells('A2:N2');

        $rowNum = 3;
        foreach ($tracking_numArr as $tracking_number) {
            $tracking_number = ParseTrackingNumber::Parse($tracking_number);
            $trackingDataRaw = array();
            if (isset($_SESSION["TRACKING_DATA"][$tracking_number])) {
                $trackingDataRaw = $_SESSION["TRACKING_DATA"][$tracking_number];
            } else {
                header("location:multitracking.php");
                //$trackingDataRaw = getTrackData($tracking_number);
            }

            //if(isset($trackingDataRaw['status']) && $trackingDataRaw['status'] == 'success'){
            $trackingData = $trackingDataRaw;
            //echo "<pre>"; print_r($trackingData); exit;
            $objPHPExcel->getActiveSheet()->mergeCells('A' . $rowNum . ':N' . $rowNum);
            $objPHPExcel->getActiveSheet()->setCellValue('A' . $rowNum, @$trackingData['Tracking_Number'] . ' ');
            $objPHPExcel->getActiveSheet()->getStyle('A' . $rowNum)->getFont()->setSize(12);
            $objPHPExcel->getActiveSheet()->getStyle('A' . $rowNum)->getFont()->setBold(true);

            $rowNum++;

            $objPHPExcel->getActiveSheet()->setCellValue('A' . $rowNum, 'Origin Country');
            $objPHPExcel->getActiveSheet()->getStyle('A' . $rowNum)->getFont()->setBold(true);
            $objPHPExcel->getActiveSheet()->setCellValue('B' . $rowNum, @$trackingData['Origin_Country']);

            $objPHPExcel->getActiveSheet()->setCellValue('C' . $rowNum, 'Destination Country');
            $objPHPExcel->getActiveSheet()->getStyle('C' . $rowNum)->getFont()->setBold(true);
            $objPHPExcel->getActiveSheet()->setCellValue('D' . $rowNum, @$trackingData['Destination_Country']);

            $objPHPExcel->getActiveSheet()->setCellValue('E' . $rowNum, 'Status');
            $objPHPExcel->getActiveSheet()->getStyle('E' . $rowNum)->getFont()->setBold(true);
            $objPHPExcel->getActiveSheet()->setCellValue('F' . $rowNum, (@$trackingData['trackingStatus'] == '' ? 'In Transit' : @$trackingData['trackingStatus']));

            $objPHPExcel->getActiveSheet()->setCellValue('G' . $rowNum, 'Transit Time');
            $objPHPExcel->getActiveSheet()->getStyle('G' . $rowNum)->getFont()->setBold(true);
            $objPHPExcel->getActiveSheet()->setCellValue('H' . $rowNum, @$trackingData['Transit_Time']);

            $objPHPExcel->getActiveSheet()->setCellValue('I' . $rowNum, 'Company');
            $objPHPExcel->getActiveSheet()->getStyle('I' . $rowNum)->getFont()->setBold(true);
            $objPHPExcel->getActiveSheet()->setCellValue('J' . $rowNum, @$trackingData['Company']);

            $objPHPExcel->getActiveSheet()->setCellValue('K' . $rowNum, 'Post Code');
            $objPHPExcel->getActiveSheet()->getStyle('K' . $rowNum)->getFont()->setBold(true);
            $objPHPExcel->getActiveSheet()->setCellValue('L' . $rowNum, @$trackingData['Postcode']);

            $objPHPExcel->getActiveSheet()->setCellValue('M' . $rowNum, 'Other');
            $objPHPExcel->getActiveSheet()->getStyle('M' . $rowNum)->getFont()->setBold(true);
            //$historyLast = count($trackingData['History']['Date_Time']) - 1;
            $objPHPExcel->getActiveSheet()->setCellValue('N' . $rowNum, @$trackingData['trackingStatusDesc']); //@$trackingData['History']['Other'][$historyLast]

            $rowNum++;

            $objPHPExcel->getActiveSheet()->mergeCells('A' . $rowNum . ':N' . $rowNum);
            $objPHPExcel->getActiveSheet()->setCellValue('A' . $rowNum, 'History');
            $objPHPExcel->getActiveSheet()->getStyle('A' . $rowNum)->getFont()->setBold(true);

            $rowNum++;
            if (isset($trackingData['trackEvents']) && !empty($trackingData['trackEvents'])) {
                foreach ($trackingData['trackEvents'] as $Date_Time => $trackEventDataArray) {
                    foreach ($trackEventDataArray as $trackEventData) {
                        $Track_Point = @$trackEventData['track_point'];
                        $Event_Content = @$trackEventData['event_content'];
                        $Other = @$trackEventData['carrier_desc'];
                        if (!empty($Date_Time)) {
                            $objPHPExcel->getActiveSheet()->setCellValue('A' . $rowNum, @$trackEventData['date_time']);

                            $distoryDetail = '';
                            if ($Track_Point != "")
                                $distoryDetail .= $Track_Point;
                            if ($Event_Content != "")
                                $distoryDetail .= ($distoryDetail == '' ? '' : ', ') . $Event_Content;
                            if ($Other != "")
                                $distoryDetail .= ($distoryDetail == '' ? '' : ', ') . $Other;
                            $objPHPExcel->getActiveSheet()->mergeCells('B' . $rowNum . ':N' . $rowNum);
                            $objPHPExcel->getActiveSheet()->setCellValue('B' . $rowNum, $distoryDetail);
                            $rowNum++;
                        }
                    }
                }
            }
            $objPHPExcel->getActiveSheet()->mergeCells('A' . $rowNum . ':N' . $rowNum);
            $rowNum++;
            //}
        }

        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('N')->setAutoSize(true);

        $objPHPExcel->getActiveSheet()->setTitle('Tracking Result');

        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $objPHPExcel->setActiveSheetIndex(0);

        $fileName = "tracking_result.xls";

        // Redirect output to a client's web browser (Excel5)
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $fileName . '"');
        header('Cache-Control: max-age=0');

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
    }
    exit;
}
?>