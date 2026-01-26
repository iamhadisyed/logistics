<?php

namespace SmarttrackTransformer;

use Nocarrier\Hal;
use Smarttrack\ApiFunctions;

/**
 * Transform an Author (or collection of Authors) into Hal resource
 */
class GetDriverParcelTransformer {

    public function transformCollection($driverResp) {
        $apiResp = ['status' => "success", 'status_code' => 200, "count" => 0];
        $hal = new Hal();
        $count = 0;
        if(count($driverResp) <= 0){
            $error[] = "No parcel assigned to driver";
            $apiResp["status"] = "error";
            $apiResp["message"] = "No parcel assigned to driver";
            $apiResp["error"] = $error;
            $apiResp["status_code"] = 400;
            $hal->setData($apiResp);
        }else{
            if (count($driverResp) > 0) {
                foreach ($driverResp as $driverData) {
                    $count ++;
                    $hal->addResource('parcels', $this->transform($driverData));
                }
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

    public function transform($driverData) {
        $data = [
            'order_refrence' => $driverData->getHawb(),
            'company' => $driverData->getCompany(),
            'contact' => $driverData->getContact(),
            'address_line_1' => $driverData->getAddressLine1(),
            'address_line_2' => $driverData->getAddressLine2(),
            'address_line_3' => $driverData->getAddressLine3(),
            'city' => $driverData->getCity(),
            'state' => $driverData->getState(),
            'postcode' => $driverData->getPostcode(),
            'country' => $driverData->getCountryId(),
            'country_iso' => $driverData->getSenderCountryId(),
            'service_code' => $driverData->getServiceId(),
            'service' => $driverData->getCustomizedServiceId(),
            'telephone' => $driverData->getTelephone(),
            'tracking_number' => $driverData->getAwb(),
            'status_code' => $driverData->getConsignmentStatus(), //code
            'status' => ApiFunctions::getShipmentStatusStr($driverData->getConsignmentStatus())// str
        ];
        $resource = new Hal(null, $data);
        return $resource;
    }

}
