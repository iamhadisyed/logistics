<?php 
namespace SmarttrackTransformer;

use Nocarrier\Hal;
use Smarttrack\ApiFunctions;

/**
 * Transform an Author (or collection of Authors) into Hal resource
 */
class GetDropOffLocationTransformer {

    public function transformCollection($shipsRes) {        
        $hal = new Hal();
        if ($shipsRes['STATUS'] == "ERROR") {
            $apiResp = ['status' => "error", 'status_code' => 200];
            $apiResp["message"] = $shipsRes['MESSAGE'];
            $apiResp["errors"] = $shipsRes['ERROR'];
            $apiResp["_embedded"] = ['dropOffLocations' => []];
        } else {
            $apiResp = ['status' => "success", 'status_code' => 200];
            $apiResp["message"] = "Dropoff Location";
            $apiResp["_embedded"] = ['dropOffLocations' => $shipsRes['RESPONSE']];
        }
        $hal->setData($apiResp);
        return $hal;
    }

}
