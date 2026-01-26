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
    'brtitaly.class'
	],'labels');

$sql = "SELECT 
    c.awb
FROM
    consignment c
        LEFT JOIN
    services s ON c.service_id = s.id
WHERE
    s.carrier_id = '187'
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
$brtSendData  = new BrtItaly();
$senddataResponce   =   $brtSendData->sendData($trackingNumberArray);
}

echo "<pre>";
print_r($senddataResponce);
echo "me here";