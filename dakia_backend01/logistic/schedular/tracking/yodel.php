<?php
require_once(__DIR__ . "/../../includes/settings/config.inc.php");
include_classes([
    'carrierservice.class',
    ],'general');
include_classes([
    'yodel.class',
    'yodeltrackingstatus.class'
    ],'labels');
include_classes([
    'nusoap',
    ],'nusoap');


include_classes([
    'iaddress.class',
    'consignment.class',
    'consignmentfilter.class',
    'parcel.class',
    'parcelfilter.class',
    'services.class',
    'servicefilter.class',
    'serviceconstantvalue.class',
    'serviceconstantvaluefilter.class',
    'tracking.class',
    'trackingdata.class',
    'trackingdatafilter.class',
    
    ]);


$startDate = date('Y-m-d 00:00:00', strtotime("-3 month"));
$endDate = date('Y-m-d H:i:s');

$services = [];
$serviceIds = "";
$servicesFilter = new ServiceFilter();
$serviceObjArr = $servicesFilter->getCarrierFromCode("STYDL003H");
if(count($serviceObjArr) > 0){
    $serviceObj = $serviceObjArr[0];
    $carrierId = $serviceObj->getCarrierId();

    $servicesFilter1 = new ServiceFilter();
    $servicesFilter1->addCarrierFilter($carrierId);
    $serviceObjArr1 = $servicesFilter1->getColumnList("ser.carrier_id");
    if(count($serviceObjArr1) > 0){
        foreach($serviceObjArr1 as $serviceObj1){
            $services[] = $serviceObj1->getId();
        }
        $serviceIds = "'".implode("','",$services)."'";
    }
}
$consignmentObj = [];
$currentTime = date("Y-m-d H:i:s");
if(!empty($serviceIds)) {
    $sql = "SELECT
              p.id,
              c.`awb`,
              p.`tracking_number` AS mawb
            FROM
              consignment c
              JOIN parcel p
                ON p.`consignment_id` = c.`id` AND p.`parcel_status_code` NOT IN ('" . Consignment::STATUS_DELIVERED . "')
              WHERE c.`service_id` IN(" . $serviceIds . ")
              AND p.`parcel_status_code` NOT IN ('" . Consignment::STATUS_NEW . "','" . Consignment::STATUS_READY_TO_PRINT . "','" . Consignment::STATUS_INVALID . "','" . Consignment::STATUS_DELIVERED . "','" . Consignment::STATUS_CLOSE . "','" . Consignment::STATUS_RECYCLED . "')
              AND c.`date_created` >= '".$startDate."' AND c.`date_created` <= '".$endDate."'   
              AND p.`tracking_number` != '' AND ((TIME_TO_SEC(TIMEDIFF('" . $currentTime . "', p.last_tracking_update)) / 3600 > 2) OR p.last_tracking_update IS NULL) LIMIT 1000";
    $consignmentObj = Consignment::getConsignmentListFromSql($sql);
}
$cronOutput = '';
if(count($consignmentObj) > 0){
    foreach($consignmentObj as $consignment){
        $awb = $consignment->getAwb();
        $trackingNo = $consignment->getMawb();
        $parcelId = $consignment->getId();

        $parcelObj = new Parcel($parcelId);
        $dateTime = date("Y-m-d H:i:s");
        $parcelObj->setLastTrackingUpdate(strtotime($dateTime));
        $parcelObj->save();

        $yodel = new Yodel();
        $yodel->tracking($trackingNo);

        $parcelObj = new Parcel($parcelId);
        $cronOutput .= $parcelObj->getId()." - ".$trackingNo." tracking done on ".date("Y-m-d H:i:s",$parcelObj->getLastTrackingUpdate())."<br />";
    }
}else{
    $cronOutput = 'Tracking upto date.';
}
//mail("irshadali18@gmail.com","Smart Track SWP Cron",$cronOutput);
echo $cronOutput;
