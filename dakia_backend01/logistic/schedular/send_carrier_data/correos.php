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
    'serviceagentmapping.class',
    'serviceagentmappingfilter.class',
    'services.class',
    'servicefilter.class',
    'serviceconstantvalue.class',
    'serviceconstantvaluefilter.class',
    'country.class',
    'countryfilter.class',
    'manifestentitymapping.class',
    'manifestentitymappingfilter.class',
    
    
    ]);
include_classes([
    'dpdde.class'
	],'labels');


set_time_limit(-1);

$trackingNumberArray = ['09446081541652'];
//$trackingNumberArray = [];

if(isset($_GET["TRACKINGNUMBER"]) && trim($_GET["TRACKINGNUMBER"]) !='')
{
    $trackingNumberArray   =   explode(",",$_GET["TRACKINGNUMBER"]);
}

$brtSendData  = new DPDDE();
$senddataResponce   =   $brtSendData->sendData($trackingNumberArray);
echo "<pre>";
print_r($senddataResponce);
echo "me here";
