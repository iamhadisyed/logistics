<?php
require_once("../includes/settings/config.inc.php");
include_classes([
        'iaddress.class',
    'consignment.class',
    'consignmentfilter.class',
    'parcel.class',
    'parcelfilter.class',
    'addressfilter.class',
    'address.class',
    'country.class',
    'countryfilter.class',
    'services.class',
    'servicefilter.class',
    'carrier.class',
    'carrierfilter.class',
    'dropoffuserlocation.class',
    'dropoffuserlocationfilter.class',
    'userservicesrouting.class',
    'userservicesroutingfilter.class',
    'agentdatafilter.class',
    'agentdata.class',
    'serviceagentmapping.class',
    'serviceagentmappingfilter.class',
    'carrierservicecustomizerules.class',
    'carrierservicecustomizerulesfilter.class',
    'carrierservicedefaultrules.class',
    'carrierservicedefaultrulesfilter.class',
    'remoteareas.class',
    'remoteareasfilter.class',
    'consignmentlog.class',
    'consignmentlogfilter.class',
    'currency.class',
    'customizedservicesrouting.class',
    'customizedservicesroutingfilter.class',
    'warehousefilter.class',
    'warehouse.class',
    'trackingfilter.class',
    'tracking.class',
    'trackingdatafilter.class',
    'trackingdata.class',
    'bagging.class',
    'baggingfilter.class'
    
]);
error_reporting(1);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

$trackingNoArray = array('OWEXGB00000122185US000070937',
'OWEXGB00000122187US000113607',
'OWEXGB00000122189US000115727',
'OWEXGB00000122191US000112347',
'OWEXGB00000122193US000113547',
'OWEXGB00000122195US000113547',
'OWEXGB00000122197US000114177',
'OWEXGB00000122199US000115187',
'OWEXGB00000122201US000112087',
'OWEXGB00000122203US000113607',
'OWEXGB00000122205US000606097',
'OWEXGB00000122207US000606167',
'OWEXGB00000122209US000606317',
'OWEXGB00000122211US000606407',
'OWEXGB00000122213US000601317',
'OWEXGB00000122215US000600047',
'OWEXGB00000122217US000601307',
'OWEXGB00000122219US000600897',
'OWEXGB00000122221US000330157',
'OWEXGB00000122223US000331297');

$bagging = new Bagging(12566);
$bagNumber = $bagging->getBagnumber();
if($bagNumber != "")
{
    $TrackingDataFilter = New TrackingDataFilter();
    $TrackingDataFilter->addTrackingNumberFilter($bagNumber);
    $BagtrackingList = $TrackingDataFilter->getList();
    if(count($BagtrackingList) > 0){
        foreach($BagtrackingList as $tracking){
            $status_code = $tracking->getStatusCodeId();
            $track_point = $tracking->getTrackPoint();
            $carrier_code = $tracking->getCarrierCode();
            $carrier_desc = $tracking->getCarrierDesc();
            $DateTime = $tracking->getDateCreated();
            $Signatory = $tracking->getsignatory();
            if($carrier_code != ""){
            foreach($trackingNoArray as $tracking_numbers){
                $parcelObj = new ParcelFilter();
                $parcelObj->addTrackingNumberFilter($tracking_numbers);
                $parcelDataArray = $parcelObj->getColumnList('p.id, p.tracking_number, p.consignment_id');
                if (count($parcelDataArray) > 0) {
                    $parcelData = $parcelDataArray[0];
                    $entityId = $parcelData->getId();
                }
                //Check if Track Point Exits
                $trackingFilter = new TrackingDataFilter();
                $trackingFilter->addTrackPointExistFilter($tracking_numbers, $status_code, $track_point, $carrier_code, $carrier_desc);
                $exitsTrackingList = $trackingFilter->getColumnList("id");
                if(count($exitsTrackingList) == 0){
                    $trackingData = array();
                    $trackingData = [
                        'user_id' => 0,
                        'entity_id' => $entityId,
                        'entity_type' => "parcel",
                        'tracking_number' => $tracking_numbers,
                        'track_point' => $track_point,
                        'date_created' => $DateTime,
                        'ip_address' => getClientIp(),
                        'status_code_id' => $status_code,
                        'carrier_code' => $carrier_code,
                        'carrier_desc' => $carrier_desc,
                        'signatory' => $Signatory
                    ];
                    
                    $trackingDataObj = new TrackingData($trackingData);			
                    $trackingDataObj->save();
                }
            }
            }
        }
    }
}


/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

