<?php
require_once("../includes/settings/config.inc.php");
include_classes([
    'pdfmerger'
    ], 'labels');
include_classes([
    'fpdi'
    ], '3rdparty/fpdi');

$arrayData = array('97624',
'97667',
'97716',
'97740',
'97748',
'97750',
'97752',
'97753',
'97757',
'97758',
'97759',
'97761',
'97762',
'97763',
'97765',
'97768',
'97772',
'97773',
'97775',
'97777',
'97780');

$pdf2 = new PDFMerger();
$prefix = date("Ymd_");
$mergeFileName = uniqid($prefix) . ".pdf";
foreach($arrayData as $consignemtId )
{
    $consignment = new Consignment($consignemtId); //	$consignmentData[0];
    $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
    $pdf->SetX(1.0);
    $glOrderPdf = new ProformaInvoice($pdf);
    $invoiceLink = $glOrderPdf->AddHTML($consignment,'', false, true);
    $pdf2->addPDF($invoiceLink, 'all');
    $labelLinl  = "../_assets/pdf/".$consignment->getLabelFile();
    $pdf2->addPDF($labelLinl, 'all');
 
    //$packingList = new PackingList($pdf);
    //$packingListLink = $packingList->AddHTML($consignment,'');
    //$pdf2->addPDF($packingListLink, 'all');
    
}
$filePatch      =       $mergeFileName;
$newFileVarUrls	=	SETTING_URL_LABEL.$filePatch ;
$newFileVar	=	SETTING_URL_LABEL_RELATIVE.$filePatch;
$pdf2->merge('file',$newFileVar);
            
echo $newFileVarUrls;       
?>
