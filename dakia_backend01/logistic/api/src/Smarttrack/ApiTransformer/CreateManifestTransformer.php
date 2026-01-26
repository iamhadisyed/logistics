<?php
namespace SmarttrackTransformer;

use Nocarrier\Hal;
use Smarttrack\ApiFunctions;

/**
 * Transform an Author (or collection of Authors) into Hal resource
 */
class CreateManifestTransformer
{
    public function transformCollection($userCreationResp)
    {
	    $apiResp = ['status' => "success",'status_code' => 200];
	    $hal = new Hal();
	    $data =[
		    'status'          =>  strtolower($userCreationResp['STATUS']),
		    'message'         =>  $userCreationResp['MESSAGE'],
		    'id'         =>  $userCreationResp['ID'],
		    'pdf_file'         =>  $userCreationResp['PDF_FILE'],
		    'csv_file'         =>  $userCreationResp['CSV_FILE']
	    ];
	    if(isset($userCreationResp['ERRORS']) && count($userCreationResp['ERRORS'])>0){
		    $apiResp['errors'] = $userCreationResp['ERRORS'];
		    $apiResp["status"] = "error";
            $data = [];
	    }
	    $resource = new Hal(null, $data);
        $hal->addResource('CreateManifestResponse', $resource);
        $hal->setData($apiResp);
        return $hal;
    }

}
