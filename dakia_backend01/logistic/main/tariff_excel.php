<?php
// get settings
require_once("../includes/settings/config.inc.php");
//require_once("../Classes/PHPExcel.php");

include_classes([
    'PHPExcel'
    ], '3rdparty/phpexcel');


set_time_limit(0);

$user = SessionManager::getUser();
$cols = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z'];
$tariffId = (!empty($_GET['id']) ? $_GET['id'] : '0');

$data = TariffsFilter::getTariffExcelData($tariffId);

exit;
$userFilterSubObj = new UserAccountFilter();
$userFilterSubObj->addFieldFilter('id', $user->getUserAccountId());
$dataAccount = $userFilterSubObj->getList();
//logo
$objPHPExcel = new PHPExcel();
$objDataSheet = $objPHPExcel->getActiveSheet();
$objDataSheet->setTitle('Tariff Excel');

$headingArray = array(
    'font' => array(
        'bold' => true,
        'size' => 20,
        'name' => 'Calibri',
    )
);
$bold = array(
    'font' => array(
        'bold' => true,
    )
);
$styleForReport = array(
    'font' => array(
        'bold' => true,
        'color' => array('rgb' => 'FFFFFF'),
        'size' => 14,
        'name' => 'Calibri',
    ),
    'alignment' => array(//'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
    ),
    'borders' => array(
        'top' => array(
            'style' => PHPExcel_Style_Border::BORDER_THIN,
        ),
        'bottom' => array(
            'style' => PHPExcel_Style_Border::BORDER_THIN,
        )
    ),
    'fill' => array(
        'type' => PHPExcel_Style_Fill::FILL_SOLID,
        'color' => array('rgb' => 'F02D2D')
    ),
);
$styleServiceForReport = array(
    'font' => array(
        'bold' => true,
        'color' => array('rgb' => 'FFFFFF'),
        'size' => 14,
        'name' => 'Calibri',
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
    ),
    'fill' => array(
        'type' => PHPExcel_Style_Fill::FILL_SOLID,
        'color' => array('rgb' => 'F02D2D')
    ),
);
$centCss = array(
    'alignment' => array(
        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
    )
);

if (!empty($data)) {
    // IMAGES
    $logo = User::getUserCompanyImages(true);
    if (!empty($logo) && file_exists($logo)) {
        $objDrawing = new PHPExcel_Worksheet_Drawing();
        $objDrawing->setName('Company Logo');
        $objDrawing->setDescription('Company Logo');
        $objDrawing->setPath($logo);
        $objDrawing->setCoordinates('A1');
        //setOffsetX works properly
        $objDrawing->setOffsetX(5);
        $objDrawing->setOffsetY(5);
        //set width, height
        $objDrawing->setWidth(1000);
        $objDrawing->setHeight(100);
        $objDrawing->setWorksheet($objPHPExcel->getActiveSheet());
    }
    $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(42);
    $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(22);
    $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(22);
    $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(22);
    $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(22);
    $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(22);
    $objPHPExcel->getActiveSheet()->mergeCells('A13:F13');
    $objPHPExcel->getActiveSheet()->getStyle('A13:F13')->applyFromArray($styleServiceForReport);
    $objPHPExcel->getActiveSheet()->getStyle('A14:F14')->applyFromArray($styleForReport);
    $objPHPExcel->getActiveSheet()->getStyle('A9')->applyFromArray($styleForReport);
    $objPHPExcel->getActiveSheet()->getStyle('A10')->applyFromArray($styleForReport);
    $objPHPExcel->getActiveSheet()->SetCellValue('A1', "");
    $objPHPExcel->getActiveSheet()->SetCellValue('A9', "Client Name");
    $objPHPExcel->getActiveSheet()->SetCellValue('A10', "Date Issued");

    $objPHPExcel->getActiveSheet()->SetCellValue('A14', "Continent");
    $objPHPExcel->getActiveSheet()->SetCellValue('B14', "Country");
    $objPHPExcel->getActiveSheet()->SetCellValue('C14', "Currency");
    $objPHPExcel->getActiveSheet()->SetCellValue('D14', "ITEM");
    $objPHPExcel->getActiveSheet()->SetCellValue('E14', "KILO");
    $objPHPExcel->getActiveSheet()->SetCellValue('F14', "Transit Days");
    $count = 15;
    $toWeight = 0;
    $maxWidth = 0;
    $maxHeight = 0;
    $maxLength = 0;
    $endDate = '';
    foreach ($data as $data) {
        $maxWidth = $data->getUpdatedBy();
        $maxHeight = $data->getTariffsPricingRuleId();
        $maxLength = $data->getId();
        $endDate = $data->getEndDate();
        // Date Issued
        $objPHPExcel->getActiveSheet()->SetCellValue('B10', $data->getStartDate());
        //SERVICE NAME
        $objPHPExcel->getActiveSheet()->SetCellValue('A13', $data->getStatus());
        $toWeight = $data->getUserAccountId();
        $objPHPExcel->getActiveSheet()->SetCellValue('A' . $count, $data->getName());
        $objPHPExcel->getActiveSheet()->SetCellValue('B' . $count, $data->getTariffType());
        $objPHPExcel->getActiveSheet()->SetCellValue('C' . $count, $data->getCurrencyId());
        $objPHPExcel->getActiveSheet()->SetCellValue('D' . $count, $data->getCarrierId());
        $objPHPExcel->getActiveSheet()->SetCellValue('E' . $count, $data->getDescription());
        $objPHPExcel->getActiveSheet()->SetCellValue('F' . $count, $data->getServiceId());
        $count++;
    }

    $companyName = (!empty($dataAccount) ? $dataAccount[0]->getCompany() : '');
    $objPHPExcel->getActiveSheet()->SetCellValue('A' . $count, '© ' . $companyName);
    $objPHPExcel->getActiveSheet()->SetCellValue('F' . $count, 'E&O.E.');
    $objPHPExcel->getActiveSheet()->getStyle('A' . $count . ":F" . $count)->applyFromArray($bold);
    //
    $count = $count + 2;
    $staticCount = $count;
    for ($i = $staticCount; $i <= $staticCount + 8; $i++) {
        $objPHPExcel->getActiveSheet()->mergeCells('A' . $i . ':F' . $i);
    }
    $objPHPExcel->getActiveSheet()->SetCellValue('A' . $count, "Max Dimensions: length * width * height < $maxLength * $maxWidth * $maxHeight");
    $count++;
    $objPHPExcel->getActiveSheet()->SetCellValue('A' . $count, "Max Weight: $toWeightgm ($toWeight kg)");
    $count++;
    $objPHPExcel->getActiveSheet()->SetCellValue('A' . $count, "The above rates are based on pre-labeled shipments as per One World Routings");
    $count++;
    $objPHPExcel->getActiveSheet()->SetCellValue('A' . $count, "The rates offered are based on an average volume and send profile.");
    $count++;
    $objPHPExcel->getActiveSheet()->SetCellValue('A' . $count, "One World Express reserve the right to revise these rates should this profile change adversely.");
    $count++;
    $objPHPExcel->getActiveSheet()->SetCellValue('A' . $count, "Rates are valid untill " . date('d/m/Y', strtotime($endDate)) . " unless revoked earlier with 30 days notice.");
    $count++;
    $objPHPExcel->getActiveSheet()->SetCellValue('A' . $count, "Rates are subject to VAT if applicable.");
    $count++;
    $objPHPExcel->getActiveSheet()->SetCellValue('A' . $count, "Rates needs to be accepted within a maximum 30 days from date of issued.");
    $count++;
    $objPHPExcel->getActiveSheet()->SetCellValue('A' . $count, "Subject to the One World Express Terms and Conditions.");
    $objPHPExcel->getActiveSheet()->getStyle('A' . $count)->applyFromArray($bold);
    $count = $count + 2;
    $objPHPExcel->getActiveSheet()->SetCellValue('A' . $count, "Tariff Acceptance");
    $objPHPExcel->getActiveSheet()->getStyle('A' . $count)->applyFromArray($bold);
    $objPHPExcel->getActiveSheet()->mergeCells('A' . $count . ':C' . $count);
    $objPHPExcel->getActiveSheet()->mergeCells('D' . $count . ':F' . $count);
    $count++;
    $objPHPExcel->getActiveSheet()->SetCellValue('A' . $count, "Date");
    $objPHPExcel->getActiveSheet()->getStyle('A' . $count)->applyFromArray($bold);
    $objPHPExcel->getActiveSheet()->mergeCells('A' . $count . ':C' . $count);
    $objPHPExcel->getActiveSheet()->mergeCells('D' . $count . ':F' . $count);
    $count++;
    $objPHPExcel->getActiveSheet()->SetCellValue('A' . $count, "Name of the Company: ");
    $objPHPExcel->getActiveSheet()->SetCellValue('D' . $count, "One World Representative:  ");
    $objPHPExcel->getActiveSheet()->mergeCells('A' . $count . ':C' . $count);
    $objPHPExcel->getActiveSheet()->mergeCells('D' . $count . ':F' . $count);
    $count++;
    $objPHPExcel->getActiveSheet()->SetCellValue('A' . $count, "Position:");
    $objPHPExcel->getActiveSheet()->SetCellValue('D' . $count, "Position: ");
    $objPHPExcel->getActiveSheet()->mergeCells('A' . $count . ':C' . $count);
    $objPHPExcel->getActiveSheet()->mergeCells('D' . $count . ':F' . $count);
    $count++;
    $objPHPExcel->getActiveSheet()->mergeCells('A' . $count . ':C' . $count);
    $objPHPExcel->getActiveSheet()->mergeCells('D' . $count . ':F' . $count);
    $objPHPExcel->getActiveSheet()->SetCellValue('A' . $count, "Name:");

    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename=tariff-report-' . time() . '.xls'); // file name of excel
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
?>