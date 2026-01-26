<?php
namespace SmarttrackTransformer;

use Nocarrier\Hal;
use Smarttrack\ApiFunctions;

/**
 * Transform an Author (or collection of Authors) into Hal resource
 */
class QuotationsTransformer
{
    public function transformCollection($quotesRes)
    {
//	    echo "<pre>"; print_r($quotesRes); echo "</pre>"; die();
	    $apiResp = ['status' => "success",'status_code' => 200];
	    $hal = new Hal();
	    $data =[
//		    'status'          =>  strtolower($quotesRes['STATUS']),
		    'quotations'      => isset($quotesRes['QUOTATIONS']) ? $quotesRes['QUOTATIONS'] : "NOT FOUND",
		    'message'         =>  $quotesRes['MESSAGE']
	    ];
	    if(isset($quotesRes['ERROR'])){
		    $apiResp["errors"] = $quotesRes['ERROR'];
		    $apiResp["status"] = "error";
	    }
	    $resource = new Hal(null, $data);
        $hal->setResource('quotationsResponse', $resource);
	    $hal->setData($apiResp);
        return $hal;
    }

}
