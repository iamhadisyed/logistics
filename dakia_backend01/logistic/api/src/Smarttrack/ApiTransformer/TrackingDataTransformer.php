<?php
namespace SmarttrackTransformer;

use Nocarrier\Hal;
use Smarttrack\ApiFunctions;

/**
 * Transform an Author (or collection of Authors) into Hal resource
 */
class TrackingDataTransformer
{
    public function transformCollection($trackingData)
    {
	    $apiResp = ['status' => "success",'status_code' => 200];
		if(isset($trackingData['status']) && strtolower($trackingData['status'])=="error"){
			$apiResp["status"] = strtolower($trackingData['status']);
			if(isset($trackingData['message'])){
				$apiResp["errors"] = $trackingData['message'];
			}
		}
	    $data =[
//		    'status'          =>  strtolower($userCreationResp['STATUS']),
		    'message'         =>  isset($trackingData['message']) ? $trackingData['message'] : "",
		    'tracking'         =>  isset($trackingData['tracking']) ? $trackingData['tracking'] : ""
	    ];
	    $hal = new Hal();
        $count = 1;//count($trackingData);
	    $resource = new Hal(null, $data);
        $hal->setResource('trackingData', $resource);
        $hal->setData($apiResp);
        return $hal;
    }

    public function transform($userService)
    {
	    
       $data =[
	      'serviceId'          =>  $userService->getId(),

       ];

//        $resource = new Hal('/api/get-services' . $data['service_id'], $data);
	    $resource = new Hal(null, $data);

//        $resource->addLink('carriers', '/service/' . $data['service_id'] . '/carrier');

        return $resource;
    }
}
