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
    'warehouse.class',
    'warehousefilter.class'
]);

include_classes([
    'royalmail.class'
        ], 'labels');

$warehouseFilter = new WarehouseFilter();
$warehouseFilter->addFieldFilter('is_active', 1);
$warehouselist = $warehouseFilter->getList();
if (count($warehouselist) > 0) {
    $trackingNumberArray = array();
    foreach($warehouselist as $warehouse){
        $warehouseId = $warehouse->getId();
        $sql = "SELECT 
        c.awb
        FROM
        consignment c
            LEFT JOIN
        services s ON c.service_id = s.id
        WHERE
        s.carrier_id = '15'
            AND ( c.send_courier_data= '0' or c.send_courier_data is null)
            AND awb != ''
            AND date_created >= '2019-03-27'
            AND c.shipment_status not in ('" . Consignment::STATUS_INVALID . "', '" . Consignment::STATUS_RECYCLED . "','" . Consignment::STATUS_CANCELLED . "')  and warehouse_id = '".$warehouseId."' limit 1000";
        
        echo "<br />";
        $consignmentObj = Consignment::getConsignmentListFromSql($sql);

        echo count($consignmentObj);
        echo "<br />";
        if (count($consignmentObj) > 0) {
            foreach ($consignmentObj as $consignment) {
                $trackingNumberArray[] = $consignment->getAwb();
            }
            print_r($trackingNumberArray);
            $royalMailSendData = new RoyalMail();
            $royalMailSendData->setWarehouseId($warehouseId);
            $senddataResponce = $royalMailSendData->sendData($trackingNumberArray);
        }
    }
}