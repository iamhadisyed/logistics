<?php

namespace SmarttrackTransformer;

use Nocarrier\Hal;
use Smarttrack\ApiFunctions;

/**
 * Transform an Author (or collection of Authors) into Hal resource
 */
class STTrackingTransformer {

    public function transformCollection($trackingData) {
        $apiResp = ['status' => "success", 'status_code' => 200, "count" => 0];
        $hal = new Hal();
        $count = 0;
        if(count($trackingData["tracking"]) <= 0){
            $error[] = "No parcel assigned to driver";
            $apiResp["status"] = "error";
            $apiResp["message"] = "No parcel assigned to driver";
            $apiResp["error"] = $error;
            $apiResp["status_code"] = 400;
            $hal->setData($apiResp);
        }else{
            if (count($trackingData["tracking"]) > 0) {
//                foreach ($trackingData["tracking"] as $trackingData) {
                    $count ++;
                    $hal->addResource('tracking_info', $this->transform($trackingData['tracking']));
//                }
                $apiResp["count"] = $count;
                $apiResp["message"] = "Records Found";
                $hal->setData($apiResp);
            } else {
                $apiResp["status"] = "error";
                $apiResp["message"] = "No Record Found!";
                $apiResp["status_code"] = 400;
                $hal->setData($apiResp);
            }
        }
        return $hal;
    }

    public function transform($trackingData) {
        $data = [
            "tracking_info" => [
                "carrier_name" => $trackingData['shipment_detail']['carrier_name'],
                "tracking_number" => $trackingData['shipment_detail']['tracking_number'],
                "standardized_status_code" => "",
                "carrier_status_code" => $trackingData['shipment_detail']['carrier_code'],
                "carrier_status_description" => $trackingData['shipment_detail']['carrier_status_desc'],
                "shipped_datetime" => $trackingData['shipment_detail']['shipped_datetime'],
                "estimate_delivery_time" => $trackingData['shipment_detail']['estimate_delivery_time'],
                "actual_delivery_datetime" => "",
                "shipping_problem_description" => "",
                "weight" => $trackingData['shipment_detail']['parcel_weight'],
                "dimensions" => [
                    "length" => $trackingData['shipment_detail']['parcel_length'],
                    "width" => $trackingData['shipment_detail']['parcel_width'],
                    "height" => $trackingData['shipment_detail']['parcel_height']
                ],
                "service" => [
                    "code" => $trackingData['shipment_detail']['service_code'],
                    "name" => $trackingData['shipment_detail']['service']
                ],
                "packaging" => "",
                "package_count" => 0,
                "last_event" => $trackingData['shipment_detail']['last_event'],
                "events" => $trackingData['shipment_detail']['events'],
                "shipping_problem" => false,
                "shipping_problem_code" => "",
                "error_description" => ""
            ]
        ];
        $resource = new Hal(null, $data);
        return $resource;
    }

}
