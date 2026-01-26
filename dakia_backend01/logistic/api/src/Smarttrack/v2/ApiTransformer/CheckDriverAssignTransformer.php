<?php 
namespace SmarttrackTransformer;

use Nocarrier\Hal;
use Smarttrack\ApiFunctions;

/**
 * Transform an Author (or collection of Authors) into Hal resource
 */
class CheckDriverAssignTransformer {

    public function transformCollection($driverResp) {
        $apiResp = ['status' => "success", 'status_code' => 200];
        $hal = new Hal();
        $hal->setData($driverResp);
         
        /*
        if (isset($driverResp['status']) && $driverResp['status'] == "success") {
            if (isset($driverResp['driver_assigned']) && $driverResp['driver_assigned']) {
                $apiResp["status"] = "success";
                $apiResp["message"] = $driverResp['message'];
                $apiResp["status_code"] = 200;
                $hal->setData($apiResp);
                $data['drive_assign'] = "yes";
                $resources = new Hal(null, $data);
                $hal->setResource('response', $resources);
            } else {
                $apiResp["status"] = "success";
                $apiResp["message"] = $driverResp['message'];
                $apiResp["status_code"] = 200;
                $hal->setData($apiResp);
                $data['drive_assign'] = "no";
                $resources = new Hal(null, $data);
                $hal->setResource('response', $resources);
            }
        } else {
            $apiResp["status"] = "error";
            $apiResp["error"] = $driverResp['message'];
            $apiResp["status_code"] = 200;
            $data['message'] = $driverResp['message'];
            $resources = new Hal(null, $data);
            $hal->setResource('response', $resources);
            $hal->setData($apiResp);
        }
         */
        return $hal;
    }

}
