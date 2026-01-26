<?php
require_once(__DIR__ . "/../../includes/settings/config.inc.php");
include_classes([
    'carrierservice.class'
    ], 'general');

include_classes([
    'pdfmerger', 'cn22.class'
    ], 'labels');
include_classes([
    'tcpdf'
    ], '3rdparty/tcpdf');

include_classes([
    'iaddress.class',
    'consignment.class',
    'consignmentfilter.class',
    'parcel.class',
    'parcelfilter.class',
    'importdataapis.class',
	]);

include_classes([
    'canadapost.class'
	],'labels');

$trackingNumberArray = ['848251543576911576', ''];

if (isset($_GET["TRACKINGNUMBER"]) && trim($_GET["TRACKINGNUMBER"]) != '') {
    $trackingNumberArray = explode(",", $_GET["TRACKINGNUMBER"]);
}

$canadapostSendData = new CanadaPost();
//$manifestIdArray = $canadapostSendData->sendData($trackingNumberArray);
$manifestIdArray[] = '848251543576965966';
echo "<pre>";
print_r($manifestIdArray);
echo "me here";
if (count($manifestIdArray) > 0) {
    $pdf2 = new PDFMerger();
    $prefix = date("Ymd_");
    $mergeFileName = uniqid($prefix) . ".pdf";
    foreach ($manifestIdArray as $manifestId) {
        echo "me1";
        $manifestResponse = $canadapostSendData->getManifestArtifactId($manifestId, $trackingNumberArray);
        if ($manifestResponse["STATUS"] == "SUCCESS") {
            echo $pdfLink = $manifestResponse["MESSAGE"];
            echo "<br />";
            if ($pdfLink != '') {  
                $pdf2->addPDF($pdfLink, 'all');
            }
        } else {
           return $output = $manifestResponse;
        }
        $filePatch  =   date('Y_m_d').'/'.$mergeFileName;
        $newFileVarUrls	=	"../../_assets/pdf/".$filePatch ;
        $newFileVar	=	"../../_assets/pdf/".$filePatch;
        $pdf2->merge('file',$newFileVar);
        echo $newFileVar;
    }
    echo $pdfLink;
    exit;
}