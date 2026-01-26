<?php
require_once(__DIR__ . "/../../includes/settings/config.inc.php");
include_classes([
    'carrierservice.class'
    ], 'general');
include_classes([
    'iaddress.class',
    'consignment.class',
    'consignmentfilter.class',
    'parcel.class',
    'parcelfilter.class',
    'importdataapis.class',
	]);

include_classes([
    'czechpost.class'
	],'labels');



set_time_limit(-1);

$trackingNumberArray = ['RR079439048CZ'];
//$trackingNumberArray = [];

if(isset($_GET["TRACKINGNUMBER"]) && trim($_GET["TRACKINGNUMBER"]) !='')
{
    $trackingNumberArray   =   explode(",",$_GET["TRACKINGNUMBER"]);
}

$brtSendData  = new CzechPost();
$senddataResponce   =   $brtSendData->sendData($trackingNumberArray);
echo "<pre>";
print_r($senddataResponce);
echo "me here";
