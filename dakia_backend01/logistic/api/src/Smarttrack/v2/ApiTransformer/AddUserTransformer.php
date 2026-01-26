<?php
namespace SmarttrackTransformer\V2;

use Smarttrack\V2\SmartTransformer;

/**
 * Transform an Author (or collection of Authors) into Hal resource
 */
class AddUserTransformer extends SmartTransformer {
    public function transform($userCreationResp){

    }
    public function transformCollection($userCreationResp)
    {
	    $apiResp = ['status' => "success",'status_code' => 201];
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
        $hal->addResource('createUserResponse', $resource);
        $hal->setData($apiResp);
        return $hal;
    }

}
