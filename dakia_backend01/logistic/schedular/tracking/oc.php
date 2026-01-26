<?php
require_once(__DIR__ . "/../../includes/settings/config.inc.php");
include_classes([
    'carrierservice.class',
    ],'general');
include_classes([
    'orangeconnex.class',
    'octrackingstatusde.class',
    'octrackingstatusus.class',
    'octrackingstatusca.class',
    'octrackingstatusau.class'
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
$servicesFilter->addCodeExactFilter("STORGES01");
$servicesObj = $servicesFilter->getColumnList('id'); 
$serviceId = $servicesObj[0]->getId();
$currentTime = date("Y-m-d H:i:s");

$orangeConnex = new OrangeConnex();
$orangeConnex->setTrackingParams($serviceId, 1);
$orangeConnex->tracking("", 'parcel', true);

$cronOutput = "Tracking successfully updated";
//mail("irshadali18@gmail.com","Smart Track SWP Cron",$cronOutput);
echo $cronOutput;
