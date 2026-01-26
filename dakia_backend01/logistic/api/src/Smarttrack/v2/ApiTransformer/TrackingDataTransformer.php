<?php
namespace SmarttrackTransformer\V2;

use Smarttrack\V2\SmartTransformer;

/**
 * Transform an Author (or collection of Authors) into Hal resource
 */
class TrackingDataTransformer  extends SmartTransformer {
    public function transform($shipRes)
    {
        if(isset($shipRes['STATUS']) && $shipRes['STATUS'] == 'ERROR'){
            $message = $shipRes['MESSAGE'];
            $data = [$message];
            return $this->sr->error(400,$message,$data);
        }else {
            $warnings = [];
            if(isset($shipRes['ERROR']) && !empty($shipRes['ERROR']))
                $warnings = $shipRes['ERROR'];

            return $this->sr->success(200,$shipRes['MESSAGE'],$shipRes['tracking'],$warnings);
        }

        return $this->sr->error(400, "Please contact system administrator",["Whoops, looks like something went wrong."]);
    }



//    public function transformCollection($trackingData)
//    {
//	    $apiResp = ['status' => "success",'status_code' => 200];
//		if(isset($trackingData['status']) && strtolower($trackingData['status'])=="error"){
//			$apiResp["status"] = strtolower($trackingData['status']);
//			if(isset($trackingData['message'])){
//				$apiResp["errors"] = $trackingData['message'];
//			}
//		}
//	    $data =[
////		    'status'          =>  strtolower($userCreationResp['STATUS']),
//		    'message'         =>  isset($trackingData['message']) ? $trackingData['message'] : "",
//		    'tracking'         =>  isset($trackingData['tracking']) ? $trackingData['tracking'] : ""
//	    ];
//	    $hal = new Hal();
//        $count = 1;//count($trackingData);
//	    $resource = new Hal(null, $data);
//        $hal->setResource('trackingData', $resource);
//        $hal->setData($apiResp);
//        return $hal;
//    }

//    public function transform($userService)
//    {
//
//       $data =[
//	      'serviceId'          =>  $userService->getId(),
//
//       ];
//
////        $resource = new Hal('/api/get-services' . $data['service_id'], $data);
//	    $resource = new Hal(null, $data);
//
////        $resource->addLink('carriers', '/service/' . $data['service_id'] . '/carrier');
//
//        return $resource;
//    }
}
