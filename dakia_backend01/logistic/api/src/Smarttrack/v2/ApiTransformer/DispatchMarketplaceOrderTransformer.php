<?php
namespace SmarttrackTransformer\V2;

use Smarttrack\V2\SmartTransformer;


/**
 * Transform an Author (or collection of Authors) into Hal resource
 */
class DispatchMarketplaceOrderTransformer extends SmartTransformer {


    public function transform($shipRes)
    {
        if(isset($shipRes['STATUS']) && $shipRes['STATUS'] == 'ERROR'){
            $message = "Please fix below error";
            $errors[] = $shipRes['ERROR'];
            return $this->sr->error(400,$message,$errors);
        }
        if(isset($shipRes['STATUS']) && $shipRes['STATUS'] == 'SUCCESS'){
            $warnings = [];
            $data = [];
            if (isset($shipRes['STATUS']) && ($shipRes['STATUS'] == "SUCCESS") ) {
                $message = "Shipment dispatched successfully.";
                $data['marketplace_order_number'] = $shipRes['marketplace_order_number'];
            } else {
                return $this->sr->error(400, "Please contact system administrator", ["Whoops, looks like something went wrong."]);
            }
            return $this->sr->success(201,$message,$data,$warnings);
        }
        return $this->sr->error(400, "Please contact system administrator",["Whoops, looks like something went wrong."]);
    }
}
