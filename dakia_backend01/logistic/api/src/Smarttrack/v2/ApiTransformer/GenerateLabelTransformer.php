<?php
namespace SmarttrackTransformer\V2;
use Smarttrack\V2\SmartTransformer;
/**
 * Transform an Author (or collection of Authors) into Hal resource
 */
class GenerateLabelTransformer extends SmartTransformer
{
    public function transform($shipRes)
    {
        if(isset($shipRes['STATUS']) && $shipRes['STATUS'] == 'ERROR'){
            $message = "Please fix below error";

            if(isset($shipRes['CONSIGNMENT_ID']))
                $this->sr->setConsignmentId($shipRes['CONSIGNMENT_ID']);

            return $this->sr->error(400,$message,$shipRes['ERROR']);
        }
        $mapping = [
            'id' => 'CONSIGNMENT_ID',
            'order_reference' => 'ORDER_REFERENCE',
            'shipment_number' => 'AWB',
            'tracking_number' => 'TRACKING_NUMBER',
            'parcel_label' => 'PARCEL_LABEL',
            'label_url' => 'LABEL',
            'label_bin_str' => 'LABEL_BIN_STR'
        ];
        $data = $this->mappingData( $shipRes, $mapping );
        if(isset($shipRes['STATUS']) && $shipRes['STATUS'] == 'SUCCESS'){
            $warnings = [];
            if(isset($shipRes['ERROR']) && !empty($shipRes['ERROR']))
                $warnings = $shipRes['ERROR'];
            $message = "Shipment created successfully";

            if(isset($shipRes['CONSIGNMENT_ID']))
                $this->sr->setConsignmentId($shipRes['CONSIGNMENT_ID']);

            if(!empty($shipRes['PARCEL_LABEL'])) {
                $data['parcel_label'] = $shipRes['PARCEL_LABEL'];
            } else {
                $data['parcel_label'] = [];
            }
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
            'LABEL' => 'label_url',
            'LABEL_BIN_STR' => 'label_bin_str',
            'TRACKING_NUMBER' => 'tracking_number',
            'ORDER_REFERENCE' => 'order_reference',
            'CONSIGNMENT_ID' => 'id'
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
