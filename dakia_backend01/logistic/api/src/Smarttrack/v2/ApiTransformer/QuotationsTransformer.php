<?php
namespace SmarttrackTransformer\V2;

use Smarttrack\V2\SmartTransformer;

/**
 * Transform an Author (or collection of Authors) into Hal resource
 */
class QuotationsTransformer  extends SmartTransformer {
    public function transform($shipRes)
    {
        if(isset($shipRes['STATUS']) && $shipRes['STATUS'] == 'ERROR'){
            $message = "Please fix below error";
            return $this->sr->error(400,$message,$shipRes['ERROR']);
        }
        if(isset($shipRes['STATUS']) && $shipRes['STATUS'] == 'SUCCESS'){
            $message = $shipRes['MESSAGE'];
            $data = $this->array_change_key_case_recursive($shipRes['QUOTATIONS']);
            $warnings = [];
            if(isset($shipRes['ERROR']) && !empty($shipRes['ERROR']))
                $warnings = $shipRes['ERROR'];

            return $this->sr->success(200,$message,$data,$warnings);
        }

        return $this->sr->error(400, "Please contact system administrator",["Whoops, looks like something went wrong."]);
    }
//    public function transformCollection($quotesRes)
//    {
////	    echo "<pre>"; print_r($quotesRes); echo "</pre>"; die();
//	    $apiResp = ['status' => "success",'status_code' => 200];
//	    $hal = new Hal();
//	    $data =[
////		    'status'          =>  strtolower($quotesRes['STATUS']),
//		    'quotations'      => isset($quotesRes['QUOTATIONS']) ? $quotesRes['QUOTATIONS'] : "NOT FOUND",
//		    'message'         =>  $quotesRes['MESSAGE']
//	    ];
//	    if(isset($quotesRes['ERROR'])){
//		    $apiResp["errors"] = $quotesRes['ERROR'];
//		    $apiResp["status"] = "error";
//	    }
//	    $resource = new Hal(null, $data);
//        $hal->setResource('quotationsResponse', $resource);
//	    $hal->setData($apiResp);
//        return $hal;
//    }

}
