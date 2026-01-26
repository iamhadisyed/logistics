<?php

require_once("../includes/settings/config.inc.php");
include_classes([
    'tcpdf',
        ], '3rdparty/tcpdf');
include_classes([
    'iaddress.class',
    'consignment.class',
    'consignmentfilter.class',
    'country.class',
    'countryfilter.class',
]);



$consignment_id = $_GET['id'];
if ($consignment_id > 0) {
    $consignment = new Consignment($consignment_id); //	$consignmentData[0];
    $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
    $pdf->SetX(1.0);
    $glOrderPdf = new ProformaInvoice($pdf);
    $result = $glOrderPdf->AddHTML($consignment);
    header("location:" . $result);
} else {
    echo "No data found.";
}
?>