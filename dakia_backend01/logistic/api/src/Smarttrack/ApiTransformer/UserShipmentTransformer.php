<?php

namespace SmarttrackTransformer;

use Nocarrier\Hal;
use Smarttrack\ApiFunctions;

/**
 * Transform an Author (or collection of Authors) into Hal resource
 */
class UserShipmentTransformer
{

    public function transformShipmentCollection($trackingData)
    {
        $apiResp = ['status' => "success", 'status_code' => 200, "count" => 0];
        $hal = new Hal();
        $count = 0;
        if (count($trackingData['data']) <= 0) {
            $error[] = "No Data Found";
            $apiResp["status"] = "error";
            $apiResp["message"] = "No Data Found";
            $apiResp["error"] = $error;
            $apiResp["status_code"] = 400;
            $apiResp["pages_count"] = $trackingData['total_pages'];
            $hal->setData($apiResp);
        } else {
            if (count($trackingData['data']) > 0) {
                foreach ($trackingData['data'] as $tracking) {
                    $count++;
                    $hal->addResource('data', $this->transform($tracking));
                }
                $apiResp["count"] = $count;
                $apiResp["message"] = "Records Found";
                $apiResp["pages_count"] = $trackingData['total_pages'];
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

    public function transform($trackingData)
    {
        $data = $trackingData;
        $resource = new Hal(null, $data);
        return $resource;
    }

}
