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
    'eurodaydeffile.class',
    'eurodaydeffilefilter.class',
    'carrierdatafilelog.class',
    'carrierdatafilelogfilter.class',
    'country.class',
    'countryfilter.class',
    'services.class',
    'servicesfilter.class',
    'serviceconstantvaluefilter.class',
    'serviceconstantvalue.class',
    'importdataapis.class',
	]);

include_classes([
    'parcelforce.class'
	],'labels');



error_reporting(E_ALL);
ini_set("display_errors","1");
set_time_limit(-1);

$trackingNumberArray = ['PBFF2000025001', 'PBFF2000025001'];
//$trackingNumberArray = [];

if(isset($_GET["TRACKINGNUMBER"]) && trim($_GET["TRACKINGNUMBER"]) !='')
{
    $trackingNumberArray   =   explode(",",$_GET["TRACKINGNUMBER"]);
}

$brtSendData  = new ParcelForce();
$senddataResponce   =   $brtSendData->sendData($trackingNumberArray);
echo "<pre>";
print_r($senddataResponce);
echo "me here";
