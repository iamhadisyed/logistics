<?php

namespace SmarttrackTransformer;

use Nocarrier\Hal;
use Smarttrack\ApiFunctions;

/**
 * Transform an Author (or collection of Authors) into Hal resource
 */
class AddShipmentTransformer {

    public function transformCollection($shipRes) {
        $errMsg['shipmentErrors'] = "";
        $apiResp = ['status' => "success", 'status_code' => 200];
        $hal = new Hal();
        $data = [
//		    'status'          =>  strtolower($shipRes['STATUS']),
//		    'message'         =>  $shipRes['MESSAGE'],
//		    'shipmentNumber'   =>  $shipRes['CONSIGNMENT_ID']
        ];
        if (isset($shipRes['STATUS']) && $shipRes['STATUS'] == "ERROR") {
            $errMsg['shipmentErrors'] = explode('<br>', $shipRes['MESSAGE']);
            $apiResp["errors"] = $errMsg;
            $apiResp["status"] = "error";
        } else if (isset($shipRes['ERROR'])) {
            $errMsg['shipmentErrors'] = $shipRes['MESSAGE'];
            $apiResp["errors"] = $errMsg;
            $apiResp["status"] = "error";
        } else {
            $data['shipment_number'] = isset($shipRes['CONSIGNMENT_ID']) ? $shipRes['CONSIGNMENT_ID'] : "Not Found";
            $data['order_reference'] = isset($shipRes['ORDER_REFERENCE']) ? $shipRes['ORDER_REFERENCE'] : "Not Found";
            if (isset($shipRes['INSTANT_LABEL']))
                $data['label'] = $shipRes['INSTANT_LABEL'];
        }
        $resource = new Hal(null, $data);
        $hal->addResource('shipmentResponse', $resource);
        $hal->setData($apiResp);
        return $hal;
    }

}
