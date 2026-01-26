<?php

namespace SmarttrackTransformer;

use Nocarrier\Hal;
use Smarttrack\ApiFunctions;

/**
 * Transform an Author (or collection of Authors) into Hal resource
 */
class AddMultiShipmentsTransformer {

    public function transformCollection($shipsRes) {
        $apiResp = ['status' => "success", 'status_code' => 200];
        $hal = new Hal();
        $errors = [];
        foreach ($shipsRes as $key => $shipRes) {
            $data = [
                'identifier' => $key,
                'shipment_number' => isset($shipRes['CONSIGNMENT_ID']) ? $shipRes['CONSIGNMENT_ID'] : "",
                'order_reference' => isset($shipRes['ORDER_REFERENCE']) ? $shipRes['ORDER_REFERENCE'] : ""
            ];

            if (isset($shipRes['ERROR'])) {
                $apiResp["status"] = "error";
                if (is_array($shipRes['ERROR'])) {
                    foreach ($shipRes['ERROR'] as $error) {
                        $errors[] = $error;
                    }
                }
            }

            if ($apiResp["status"] == "success") {
                $resource = new Hal(null, $data);
                $hal->addResource('shipmentResponse', $resource);
            } else {
                $hal->addResource('shipmentResponse', NULL);
            }
        }
        if (count($errors) > 0) {
            $apiResp["errors"] = $errors;
        }
        $hal->setData($apiResp);
        return $hal;
    }

}
