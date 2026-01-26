<?php
namespace SmarttrackTransformer\V2;

use Smarttrack\V2\SmartTransformer;

/**
 * Transform an Author (or collection of Authors) into Hal resource
 */
class AddBaggingTransformer extends SmartTransformer
{
    public function transform($shipRes)
    {
        if(isset($shipRes['STATUS']) && $shipRes['STATUS'] == 'ERROR'){
            $message = $shipRes['MESSAGE'];
            return $this->sr->error(400,$message,$shipRes['ERROR']);
        }
        //$data = $this->mappingData( $shipRes );
        if(isset($shipRes['STATUS']) && $shipRes['STATUS'] == 'SUCCESS'){
            $warnings = [];
            if(isset($shipRes['ERROR']) && !empty($shipRes['ERROR']))
                $warnings = $shipRes['ERROR'];
            $message = "Bag created Successfully.";
            $data = ['bag_number'=>$shipRes['BAG_NUMBER'],"bag_label"=>$shipRes['BAG_LABEL']];
            return $this->sr->success(200,$message,$data,$warnings);
        }

        return $this->sr->error(400, "Please contact system administrator",["Whoops, looks like something went wrong."]);
    }

    /*public function transformCollection($userCreationResp)
    {
	    $apiResp = ['status' => "success",'status_code' => 200];
	    $hal = new Hal();
	    $data =[
//		    'status'          =>  strtolower($userCreationResp['STATUS']),
		    'message'         =>  $userCreationResp['MESSAGE']
	    ];
	    if(isset($userCreationResp['ERRORS']) && count($userCreationResp['ERRORS'])>0){
		    $apiResp['errors'] = $userCreationResp['ERRORS'];
		    $apiResp["status"] = "error";
	    }
	    $resource = new Hal(null, $data);
        $hal->addResource('AddScanningResponse', $resource);
        $hal->setData($apiResp);
        return $hal;
    }*/

}
