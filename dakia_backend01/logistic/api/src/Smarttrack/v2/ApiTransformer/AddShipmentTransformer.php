<?php

namespace SmarttrackTransformer\V2;

use Smarttrack\V2\SmartTransformer;

/**
 * Transform an Author (or collection of Authors) into Hal resource
 */
class AddShipmentTransformer extends SmartTransformer {

    public function transform($shipRes)
    {
        if(isset($shipRes['STATUS']) && $shipRes['STATUS'] == 'ERROR'){
            $message = "Please fix below error";

            if(isset($shipRes['CONSIGNMENT_ID']))
                $this->sr->setConsignmentId($shipRes['CONSIGNMENT_ID']);

            return $this->sr->error(400,$message,$shipRes['ERROR']);
        }
        $mapping = [
            'shipment_number' => 'CONSIGNMENT_ID',
            'order_reference' => 'ORDER_REFERENCE'
        ];
        $data = $this->mappingData( $shipRes, $mapping );
        if(isset($shipRes['STATUS']) && $shipRes['STATUS'] == 'SUCCESS'){
            $warnings = [];
            if(isset($shipRes['ERROR']) && !empty($shipRes['ERROR']))
                $warnings = $shipRes['ERROR'];
            $message = "Shipment created successfully";

            if(isset($shipRes['CONSIGNMENT_ID']))
                $this->sr->setConsignmentId($shipRes['CONSIGNMENT_ID']);

            return $this->sr->success(201,$message,$data,$warnings);
        }

        return $this->sr->error(400, "Please contact system administrator",["Whoops, looks like something went wrong."]);
    }

    public function transformCollection($shipRes)
    {
        $data['data'] = [];
        if(isset($shipRes['STATUS']) && $shipRes['STATUS'] == 'ERROR'){
            $message = "Please fix below error";
            return $this->sr->error(400,$message,$shipRes['ERROR']);
        }
        $mapping = [
            'shipment_number' => 'CONSIGNMENT_ID',
            'order_reference' => 'ORDER_REFERENCE',
            'label' => 'INSTANT_LABEL'
        ];
        $data['data'] = $this->mappingData( $shipRes, $mapping );
        if(isset($shipRes['STATUS']) && $shipRes['STATUS'] == 'SUCCESS'){
            $warnings = [];
            if(isset($shipRes['ERROR']) && !empty($shipRes['ERROR']))
                $warnings = $shipRes['ERROR'];
            $message = "Shipment created successfully";

            return $this->sr->success(201,$message,$data,$warnings);
        }
        return $this->sr->error(    400, "Please contact system administrator",["Whoops, looks like something went wrong."]);
    }
}
