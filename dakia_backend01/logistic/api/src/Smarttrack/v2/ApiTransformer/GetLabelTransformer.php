<?php
namespace SmarttrackTransformer\V2;

use Smarttrack\V2\SmartTransformer;


/**
 * Transform an Author (or collection of Authors) into Hal resource
 */
class GetLabelTransformer extends SmartTransformer {


    public function transform($shipRes)
    {
        if(isset($shipRes['STATUS']) && $shipRes['STATUS'] == 'ERROR'){
            $message = "Please fix below error";
            return $this->sr->error(400,$message,$shipRes['ERROR']);
        }
        $mapping = [
//            'shipment_number' => 'CONSIGNMENT_ID',
            'shipment_number' => 'AWB',
            'order_reference' => 'ORDER_REFERENCE',
            'tracking_number' => 'TRACKING_NUMBER',
            'parcel_label' => 'PARCEL_LABEL',
            'label' => 'LABEL',
            'label_bin_str' => 'LABEL_BIN_STR'
        ];
        $data = $this->mappingData( $shipRes, $mapping );
//        $data['label_bin_str'] = base64_decode($data['label_bin_str']);
        if(isset($shipRes['STATUS']) && $shipRes['STATUS'] == 'SUCCESS'){
            $warnings = [];
            if(isset($shipRes['ERROR']) && !empty($shipRes['ERROR']))
                $warnings = $shipRes['ERROR'];
            if (isset($shipRes['LABEL']))
                $message = "Shipment label generated successfully.";
            else
                return $this->sr->error(400, "Please contact system administrator",["Whoops, looks like something went wrong."]);

            if(!empty($shipRes['PARCEL_LABEL'])) {
                $data['parcel_label'] = $shipRes['PARCEL_LABEL'];
            } else {
                $data['parcel_label'] = [];
            }
            return $this->sr->success(201,$message,$data,$warnings);
        }

        return $this->sr->error(400, "Please contact system administrator",["Whoops, looks like something went wrong."]);
    }
}
