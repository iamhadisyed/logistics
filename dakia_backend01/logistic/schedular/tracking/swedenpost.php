<?php
require_once(__DIR__ . "/../../includes/settings/config.inc.php");
include_classes([
    'carrierservice.class',
    ],'general');
include_classes([
    'swedenpost.class',
    'swedenposttrackingstatus.class'
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

$servicesFilter = new ServiceFilter();
$servicesFilter->addCodeExactFilter("STSWNREGM");
$servicesObj = $servicesFilter->getColumnList('id');
$serviceId = $servicesObj[0]->getId();
$currentTime = date("Y-m-d H:i:s");
$sql = "SELECT
          p.id,
          c.`awb`,
          p.`tracking_number` AS mawb
        FROM
          consignment c
          JOIN parcel p
            ON p.`consignment_id` = c.`id` AND p.`parcel_status_code` NOT IN ('".Consignment::STATUS_DELIVERED."')
        WHERE c.`service_id` = '".$serviceId."'
          AND c.`shipment_status` NOT IN ('".Consignment::STATUS_NEW."','".Consignment::STATUS_READY_TO_PRINT."','".Consignment::STATUS_INVALID."','".Consignment::STATUS_DELIVERED."','".Consignment::STATUS_CLOSE."','".Consignment::STATUS_RECYCLED."')
          AND p.`tracking_number` != '' AND ((TIME_TO_SEC(TIMEDIFF('".$currentTime."', p.last_tracking_update)) / 3600 > 2) OR p.last_tracking_update IS NULL) LIMIT 150";

$consignmentObj = Consignment::getConsignmentListFromSql($sql);
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

        $swedenPost = new SwedenPost();
        $swedenPost->tracking($trackingNo);

        $parcelObj = new Parcel($parcelId);
        $cronOutput .= $parcelObj->getId()." - ".$trackingNo." tracking done on ".date("Y-m-d H:i:s",$parcelObj->getLastTrackingUpdate())."<br />";
    }
}else{
    $cronOutput = 'Tracking upto date.';
}
//mail("irshadali18@gmail.com","Smart Track SWP Cron",$cronOutput);
echo $cronOutput;
