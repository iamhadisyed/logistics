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
    'agentdata.class',
    'agentdatafilter.class',
	]);

include_classes([
    'yodel.class'
	],'labels');



set_time_limit(-1);


//$trackingNumberArray = [
//'JD0002210164113377',
//'JD0002210164113376',
//'JD0002210164113379',
//'JD0002210164113378'];
//
// $yodelSendData  = new Yodel();
//    $senddataResponce   =   $yodelSendData->sendData($trackingNumberArray);
//    exit;
//
//$consignment = new consignment(106153);
//$trackingNumber[] = $consignment->getAWB();
//$yodel = new PUDO();
//$label = $yodel->sendData($trackingNumber);
//print_r($label);
//exit;

echo $sql = "SELECT 
    c.awb
FROM
    consignment c
        LEFT JOIN
    services s ON c.service_id = s.id
WHERE
    s.carrier_id = '16'
        AND (c.send_courier_data = '0' or c.send_courier_data is null)
        AND awb != ''
        AND date_created >= '2019-03-27'
        AND c.shipment_status not in ('".Consignment::STATUS_INVALID."', '".Consignment::STATUS_RECYCLED."','".Consignment::STATUS_CANCELLED."') limit 1000";
$consignmentObj = Consignment::getConsignmentListFromSql($sql);

echo count($consignmentObj);
if(count($consignmentObj) > 0)
{
    foreach($consignmentObj as $consignment)
    {
        $trackingNumberArray[] = $consignment->getAwb();
    }
    print_r($trackingNumberArray);
    $yodelSendData  = new Yodel();
    $senddataResponce   =   $yodelSendData->sendData($trackingNumberArray);
}


//if(isset($_GET["TRACKINGNUMBER"]) && trim($_GET["TRACKINGNUMBER"]) !='')
//{
//    $trackingNumberArray   =   explode(",",$_GET["TRACKINGNUMBER"]);
//}
?>
