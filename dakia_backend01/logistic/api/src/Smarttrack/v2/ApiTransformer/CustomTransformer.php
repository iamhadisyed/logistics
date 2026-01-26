<?php
namespace SmarttrackTransformer;

use Nocarrier\Hal;
use Smarttrack\ApiFunctions;

/**
 * Transform an Author (or collection of Authors) into Hal resource
 */
class CustomTransformer
{
	public function transformCollection($customErrorRes,$resourceName="apiResponse")
	{
//	    echo "<pre>"; print_r($customErrorRes); echo "</pre>"; die();
		$apiResp = ['status' => "error"];
		$hal = new Hal();
		$data =[
//			'message'         =>  "please fix the errors first!"
		];
		if(isset($customErrorRes['ERROR'])){
			$apiResp["errors"] = $customErrorRes['ERROR'];
		}
		if(isset($customErrorRes['MESSAGE'])){
			$apiResp["message"] = $customErrorRes['MESSAGE'];
		}
		if(isset($customErrorRes['STATUS']) && $customErrorRes['STATUS'] =="SUCCESS"){
			$apiResp["status"] = 'success';
		}
		$resource = new Hal(null, $data);
		$hal->setResource($resourceName, $resource);
		$hal->setData($apiResp);
		return $hal;
	}

}
