<?php

require_once("../includes/settings/config.inc.php");
ini_set ( 'max_execution_time', -1); 
include_classes([
    'testapipanel.class',
]);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


for ($i = 0; $i < 1000; $i++) {
    $testApi = new ApiTesting();
    $results = [];
    $results['accessToken'] = $testApi->accessToken();

    if ($results['accessToken']['apiStatus'] == 'success') {
        $accessToken = json_decode($results['accessToken']['response']);
        if (isset($accessToken->access_token) && $accessToken->access_token != '') {
            $results['accessToken']['methodStatus'] = "success";
            $testApi->setAuthorizationToken($accessToken->access_token);

            /////////////////ADD SHIPMENT REQUEST/////////////////
            $results['addShipment'] = $testApi->addShipment();
            $addShipmentResponse = json_decode($results['addShipment']['response']);
            $results['addShipment']['methodStatus'] = $addShipmentResponse->status;
            if ($results['addShipment']['methodStatus'] == 'success') {
                $addShipmentResponseDetail = $addShipmentResponse->_embedded->shipmentResponse[0];
                if (isset($addShipmentResponseDetail->order_reference) && $addShipmentResponseDetail->order_reference != "") {

                    $testApi->setOrderReference($addShipmentResponseDetail->order_reference);
                    ///////////////// GET LABEL REQUEST/////////////////
                    $results['getLabel'] = $testApi->getLabel();
                    $getLabelResponse = json_decode($results['getLabel']['response']);
                    $results['getLabel']['methodStatus'] = $getLabelResponse->status;
                    $getLabelResponseDetail = $getLabelResponse->_embedded->labelResponse;
                    echo $addShipmentResponseDetail->order_reference . " /  " . $getLabelResponseDetail->tracking_number[0] . "<br>";
                    $testApi->setTrackingNumber($getLabelResponseDetail->tracking_number[0]);
                    /////////////////GET VOID LABEL REQUEST/////////////////
                    $results['voidLabels'] = $testApi->getVoidLabels();
                    $getvoidLabelsResponse = json_decode($results['voidLabels']['response']);
                    $results['voidLabels']['methodStatus'] = $getvoidLabelsResponse->status;
                }
            }
        } else {
            $results['accessToken']['methodStatus'] = "error";
        }
    }
}