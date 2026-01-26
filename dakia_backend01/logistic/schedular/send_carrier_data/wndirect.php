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
    'wndirect.class'
	],'labels');



set_time_limit(-1);


//$trackingNumberArray = ['WN10000002','WN10000003'];
////$trackingNumberArray = [];
//
//if(isset($_GET["TRACKINGNUMBER"]) && trim($_GET["TRACKINGNUMBER"]) !='')
//{
//    $trackingNumberArray   =   explode(",",$_GET["TRACKINGNUMBER"]);
//}
//
//$dpdSendData  = new WNDirect();
//$senddataResponce   =   $dpdSendData->sendData($trackingNumberArray);
echo $sql = "SELECT 
    c.awb
FROM
    consignment c
        LEFT JOIN
    services s ON c.service_id = s.id
WHERE
    s.carrier_id = '198'
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
   $wndirect  = new WNDirect();
$senddataResponce   =   $wndirect->sendData($trackingNumberArray);
}

echo "<pre>";
print_r($senddataResponce);
echo "me here";
