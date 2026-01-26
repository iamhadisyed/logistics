<?php
namespace SmarttrackTransformer\V2;
use Smarttrack\V2\SmartTransformer;
/**
 * Transform an Author (or collection of Authors) into Hal resource
 */
class JDGenerateLabelTransformer extends SmartTransformer
{
    public function transform($shipRes)
    {
        $isScuccess = false;
        $consId = "";
        if(isset($shipRes['STATUS']) && $shipRes['STATUS'] == 'ERROR'){
            $message = "Please fix below error";
            if(isset($shipRes['CONSIGNMENT_ID'])){
                $this->sr->setConsignmentId($shipRes['CONSIGNMENT_ID']);
                $consId = $shipRes['CONSIGNMENT_ID'];
            }
            $error  = $shipRes['ERROR'];
            $error = implode(" ",$error);
            $isScuccess = false;
            $images = [];
            $labels[] = [
                "images"=>$images,
                "packageID"=>"",
                "trackingNumber"=>"",
                "labelType"=>"OUTBOUND_LABEL",
                "imageType"=>"PDF"
            ];
            $dataArray = [
                "reason"=>$error,
                "orderId"=>$consId,
                "isSuccess"=>$isScuccess,
                "logisticsProviderCode"=>"OWE",
                "waybillCode"=>"",
                "labels"=>$labels,
            ];
            echo json_encode($dataArray);
            die;
        }
        if(isset($shipRes['STATUS']) && $shipRes['STATUS'] == 'SUCCESS'){
            $warnings = [];
            $warningsRes = "";
            $isScuccess = true;
            if(isset($shipRes['ERROR']) && !empty($shipRes['ERROR'])){
                $isScuccess = false;
                $warnings = $shipRes['ERROR'];
                $warningsRes = implode(" ",$warnings);
            }
            if(isset($shipRes['CONSIGNMENT_ID'])){
                $this->sr->setConsignmentId($shipRes['CONSIGNMENT_ID']);
                $consId = $shipRes['CONSIGNMENT_ID'];
            }
            if(!empty($shipRes['LABEL'])) {
                $images[] = $shipRes['LABEL_BIN_STR'];
                $labels[] = [
                    "images"=>$images,
                    "packageID"=>"",
                    "trackingNumber"=>$shipRes['AWB'],//$shipRes['TRACKING_NUMBER'],
                    "labelType"=>"OUTBOUND_LABEL",
                    "imageType"=>"PDF"
                ];
            } else {
                $labels = [];
            }

            $dataArray = [
                        "reason"=>$warningsRes,
                        "orderId"=>$consId,
                        "isSuccess"=>$isScuccess,
                        "logisticsProviderCode"=>"OWE",
                        "waybillCode"=>$shipRes['AWB'],
                        "labels"=>$labels,
                    ];
            echo json_encode($dataArray);
            die;
        }
        $isScuccess = false;
        $images = [];
        $labels[] = [
            "images"=>$images,
            "packageID"=>"",
            "trackingNumber"=>"",
            "labelType"=>"OUTBOUND_LABEL",
            "imageType"=>"PDF"
        ];
        $dataArray = [
            "reason"=>"Whoops, looks like something went wrong. Please contact system administrator",
            "orderId"=>"",
            "isSuccess"=>$isScuccess,
            "logisticsProviderCode"=>"OWE",
            "waybillCode"=>"",
            "labels"=>$labels,
        ];
        echo json_encode($dataArray);
        die;
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
