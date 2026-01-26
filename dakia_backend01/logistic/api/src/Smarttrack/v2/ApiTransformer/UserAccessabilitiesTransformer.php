<?php
namespace SmarttrackTransformer;

use Nocarrier\Hal;
use Smarttrack\ApiFunctions;

/**
 * Transform an Author (or collection of Authors) into Hal resource
 */
class UserAccessabilitiesTransformer
{

	public function transformCollection($userAccessabilities)
	{
		$apiResp = ['status' => "success",'status_code' => 200,"count"=>0];
		$hal = new Hal();
		$count = 0;
//		echo "<pre>"; print_r($userAccounts);echo "</pre>"; die();
		if(count($userAccessabilities ) > 0){
			foreach ($userAccessabilities as $userAccess) {
				$count++;
				$hal->addResource('accessabilities', $this->transformAccess($userAccess));
			}
			$apiResp["count"]   =   $count;
			$apiResp["message"] =   "Records Found";
			$hal->setData($apiResp);
		}else{
			$apiResp["status"] =   "error";
			$apiResp["message"] =   "No Record Found!";
			$apiResp["status_code"] =  400;
			$hal->setData($apiResp);
		}

		return $hal;
	}

	public function transformAccess($userAccess)
	{
		$data =[
			'groupName'              =>  $userAccess->getGroupName(),
			'groupCode'              =>  $userAccess->getGroupSlug()
		];
		$resource = new Hal(null, $data);
		return $resource;
	}
}
