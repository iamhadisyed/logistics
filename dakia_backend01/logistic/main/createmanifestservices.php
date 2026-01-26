<?php 
////////////////////////////////////////////////////
//
// Create Order Confirmation PDF Alex 01/07/2014
//
////////////////////////////////////////////////////

require_once("../includes/settings/config.inc.php");
set_time_limit(600);

$fromdate = $_GET["fromdate"];
$todate = $_GET["todate"];
$value = $_GET["value"];
$servicetype = $_GET["type"];
$carrier = $_GET["carrier"];
$handling = $_GET["handling"];
if (strlen($fromdate) == 10) {
    if (strlen($todate) == 10) {
        
    }
} else {
    echo 'Select From Date';
    exit();
}



//build the pdf.
GlOrderPdfAll::buildPDFDocuments($fromdate, $todate, $value, $servicetype, $carrier, $handling);

