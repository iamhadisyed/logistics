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
    'dpduk.class'
	],'labels');


//require_once '../../includes/labels/.class.php';

set_time_limit(-1);


//$trackingNumberArray = ['15502414140005'];
////$trackingNumberArray = [];
//
//if(isset($_GET["TRACKINGNUMBER"]) && trim($_GET["TRACKINGNUMBER"]) !='')
//{
//    $trackingNumberArray   =   explode(",",$_GET["TRACKINGNUMBER"]);
//}
//
//$dpdSendData  = new DPDUK();
//$senddataResponce   =   $dpdSendData->sendData($trackingNumberArray);
//echo "<pre>";
//print_r($senddataResponce);
//echo "me here";

echo $sql = "SELECT 
    c.awb
FROM
    consignment c
        LEFT JOIN
    services s ON c.service_id = s.id
WHERE
    s.carrier_id = '66'
        AND c.send_courier_data = '0'
        AND awb != ''
        AND date_created >= '2019-05-09'
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
   $wndirect  = new DPDUK();
$senddataResponce   =   $wndirect->sendData($trackingNumberArray);
}

echo "<pre>";
print_r($senddataResponce);
echo "me here";

