<?php
namespace SmarttrackTransformer;

use Nocarrier\Hal;
use Smarttrack\ApiFunctions;

/**
 * Transform an Author (or collection of Authors) into Hal resource
 */
class VoidLabelTransformer
{
    public function transformCollection($userCreationResp)
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
        $hal->addResource('voidLabelResponse', $resource);
        $hal->setData($apiResp);
        return $hal;
    }

}
