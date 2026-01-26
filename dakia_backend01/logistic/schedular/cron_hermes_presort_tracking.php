<?php
require_once(__DIR__ . "/../includes/settings/config.inc.php");

include_classes([
    'iaddress.class',
    'consignment.class',
    'consignmentfilter.class',
    'parcel.class',
    'parcelfilter.class',
    'carrierdatafilelog.class',
    'carrierdatafilelogfilter.class'
    ]);

include_classes([
    'hermes.class'
	],'labels');

$sql = "SELECT 
    c.awb
FROM
    consignment c
        LEFT JOIN
    services s ON c.service_id = s.id
WHERE
    s.carrier_id = '147'
        AND c.send_courier_data = '1'
        AND awb != ''
        AND date_created >= '2020-10-29'
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
$hermesPreSort  = new Hermes();
$senddataResponce   =   $hermesPreSort->presortFile($trackingNumberArray);
}

echo "<pre>";
print_r($senddataResponce);
echo "me here";