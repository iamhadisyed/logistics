<?php
require_once(__DIR__ . "/../../includes/settings/config.inc.php");
include_classes([
    'carrierservice.class',
    ],'general');
include_classes([
    'belgiumpost.class',
    ],'labels');

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
$servicesFilter->addCodeExactFilter("STBPMINIS");
$servicesObj = $servicesFilter->getColumnList('id');
$serviceId = $servicesObj[0]->getId();
$currentTime = date("Y-m-d H:i:s");

$begiumPost = new BelgiumPost();
$begiumPost->setTrackingParams($serviceId, 2);
$begiumPost->tracking("", 'parcel', true);

$cronOutput = "Tracking successfully updated";
echo $cronOutput;
