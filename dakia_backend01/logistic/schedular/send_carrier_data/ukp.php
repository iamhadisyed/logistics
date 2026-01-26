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
    'ukp.class'
	],'labels');


set_time_limit(-1);



$trackingNumberArray = ['UP50000003'];//,'JD0002210161109974','JD0002210161109973' JD0002210161109975
//$trackingNumberArray = [];

if(isset($_GET["TRACKINGNUMBER"]) && trim($_GET["TRACKINGNUMBER"]) !='')
{
    $trackingNumberArray   =   explode(",",$_GET["TRACKINGNUMBER"]);
}

$yodelSendData  = new UKP();
$senddataResponce   =   $yodelSendData->sendData($trackingNumberArray);
echo "<pre>";
print_r($senddataResponce);
echo "me here";
