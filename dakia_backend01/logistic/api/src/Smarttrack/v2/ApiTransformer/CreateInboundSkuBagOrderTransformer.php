<?php

namespace SmarttrackTransformer\V2;

use Smarttrack\V2\SmartTransformer;


/**
 * Transform an Author (or collection of Authors) into Hal resource
 */
class CreateInboundSkuBagOrderTransformer extends SmartTransformer {

    public function transform($skuRes)
    {
        if(isset($skuRes['STATUS']) && $skuRes['STATUS'] == 'ERROR'){
            $message = "Please fix below error";

           
            return $this->sr->error(400,$message,$skuRes['ERROR']);
        }
        $mapping = [
            'order_reference' => 'ORDER_REFERENCE'
        ];

        $data = $this->mappingData( $skuRes, $mapping );
        
        if(isset($skuRes['STATUS']) && $skuRes['STATUS'] == 'SUCCESS'){
            $warnings = [];
            if(isset($skuRes['ERROR']) && !empty($skuRes['ERROR']))
                $warnings = $skuRes['ERROR'];

            $message = "sku inbound bag order created successfully";

            return $this->sr->success(201,$message,$data,$warnings);
            
        }

        return $this->sr->error(400, "Please contact system administrator",["Whoops, looks like something went wrong."]);
    }

    public function transformCollection($skuRes)
    {
        $data['data'] = [];
        if(isset($skuRes['STATUS']) && $skuRes['STATUS'] == 'ERROR'){
            $message = "Please fix below error";
            return $this->sr->error(400,$message,$skuRes['ERROR']);
        }
        $mapping = [
            'skument_number' => 'CONSIGNMENT_ID',
            'order_reference' => 'ORDER_REFERENCE',
            'label' => 'INSTANT_LABEL'
        ];
        $data['data'] = $this->mappingData( $skuRes, $mapping );
        if(isset($skuRes['STATUS']) && $skuRes['STATUS'] == 'SUCCESS'){
            $warnings = [];
            if(isset($skuRes['ERROR']) && !empty($skuRes['ERROR']))
                $warnings = $skuRes['ERROR'];
            $message = "skument created successfully";

            return $this->sr->success(201,$message,$data,$warnings);
        }
        return $this->sr->error(    400, "Please contact system administrator",["Whoops, looks like something went wrong."]);
    }
}
