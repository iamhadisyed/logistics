<?php

require_once("../includes/settings/config.inc.php");
include_classes([
    'pdfmerger'
    ], 'labels');

//echo "mruga";
// $jobid = $_POST['jobno'];
$pdf2 = new PDFMerger();
$invoice_file = array();
$consignmentIds = explode(',', $_POST['jobno']);

$proformaInvoice = new ProformaInvoiceBillingFilter();
$proformaInvoice->addIdArrayFilter($consignmentIds);
$profoma_list = $proformaInvoice->getColumnList('consignment_id, link_file');
//print_r($profoma_list);
$fileFlag = false;
$user = SessionManager::getUser();
if (count($profoma_list) > 0) {
    foreach ($profoma_list as $plist) {

        if (trim($plist->getLinkFile()) == "") {
            $consignment = new Consignment($plist->getConsignmentId());

            if (count($consignment) > 0) {
                if ($consignment->getHandling() == 'ECX' || $consignment->getHandling() == 'ESU' || $consignment->getHandling() == 'WPX' || $consignment->getHandling() == "WPXDDP" || $consignment->getHandling() == 'DOX' || $consignment->getHandling() == 'ASE' || $consignment->getHandling() == 'ASEDE' || $consignment->getHandling() == 'AMXINT' || $consignment->getHandling() == 'AMXEUR' || $consignment->getHandling() == 'AMXINTDOX' || $consignment->getHandling() == 'AMXEURDOX' || $consignment->getHandling() == 'AMXINTDDP') {

                    $glOrderPdf = new ProformaPDF();

                    $filename = $glOrderPdf->AddHTML($consignment);
                    $fileFlag = true;
                    $pdf2->addPDF($filename, 'all');
                } else {
                    $resultSet = "ERROR||NO shippment found to generate performa";
                }
            } else {
                $resultSet = "ERROR||Shippment record does not found for performa";
            }
        } else {

            $filename = $plist->getLinkFile();
            $fileFlag = true;
            $pdf2->addPDF($filename, 'all');
        }
    }

    if ($fileFlag == true) {
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $pdf->SetX(1.0);
        //$user = SessionManager::getUser();

        $outFile = SETTING_DIR_ASSETS . "pdf/" . date('Y_m_d') . "/" . time() . $user->getUserAccount() . ".pdf";

        $pdf->Output($outFile, "F");
        $pdf2->merge('file', $outFile);
        $resultSet = "SUCCESS||" . SETTING_MAIN_URL . "_assets/pdf/" . date('Y_m_d') . '/' . time() . $user->getUserAccount() . ".pdf";
    } else if (trim($resultSet) == '') {
        $resultSet = "ERROR||NO shippment selected for performa";
    }
} else {
    $resultSet = "ERROR||No Data Entered for Invoice.";
}

echo $resultSet;
?> 