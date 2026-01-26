<?php

namespace SmarttrackTransformer\V2;

use Smarttrack\V2\SmartTransformer;

/**
 * Transform an Author (or collection of Authors) into Hal resource
 */
class AddMultiShipmentsTransformer extends SmartTransformer {

    public function transform($shipRes)
    {
        $warnings = [];
        if(count($shipRes) > 0){
            if(isset($shipRes['DATA'])){
                $foundSuccess = false;
                foreach ($shipRes['DATA'] as $key =>  $item) {
                    if($item['STATUS'] == "SUCCESS"){
                        $shipRes['STATUS'] = 'SUCCESS';
                        $foundSuccess = true;
                    }
                    if(!$foundSuccess){
                        $shipRes['STATUS'] = 'ERROR';
                    }
                }
            }
            if(!isset($shipRes['STATUS'])){
                foreach ($shipRes as $key =>  $item) {
                    if($item['STATUS'] == "SUCCESS"){
                        $shipRes['STATUS'] = 'SUCCESS';
                    }
                }
            }
        }else{
            $shipRes['STATUS'] = 'ERROR';
            $shipRes['ERROR'] = "no consignment added";
        }
        if(isset($shipRes['STATUS']) && $shipRes['STATUS'] == 'ERROR'){
            $message = "Please fix below error";
            $error = [];
            if(isset($shipRes['ERROR']['DATA'])){
                if(is_array($shipRes['ERROR']['DATA'])){
                    foreach ($shipRes['ERROR']['DATA'] as $key  => $errorData) {
                        $shipRes['ERROR']["shipment_" . $key] = $errorData['ERROR'];
                    }
                }
            }
            if(isset($shipRes['ERROR']['DATA'])){
                unset($shipRes['ERROR']['DATA']);
            }
            return $this->sr->error(400,$message,$shipRes['ERROR']);
            die;
        }
        $mapping = [
            'shipment_number' => 'CONSIGNMENT_ID',
            'order_reference' => 'ORDER_REFERENCE',
            'custom_identifier' => 'custom_identifier'
        ];
        foreach ($shipRes['DATA'] as $key =>  $dataCon) {
            if(isset($dataCon['ERROR']) && !empty($dataCon['ERROR'])){
                $warnings["shipment_" . $key] = $dataCon['ERROR'];
            }else{
                $data["shipment_" . $key] = $this->mappingData( $dataCon, $mapping );
            }
        }
        if(isset($shipRes['ERROR']['DATA'])){
            unset($shipRes['ERROR']['DATA']);
        }
        if(isset($shipRes['STATUS']) && $shipRes['STATUS'] == 'SUCCESS'){
            if(isset($shipRes['ERROR']) && !empty($shipRes['ERROR'])) {
                if (empty($warnings)) {
                    $warnings = $shipRes['ERROR'];
                }
            }
            $message = "Shipment created successfully";
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
        $data['data'] = $this->mappingData( $shipRes['DATA'], $mapping );
        if(isset($shipRes['STATUS']) && $shipRes['STATUS'] == 'SUCCESS'){
            $warnings = [];
            if(isset($shipRes['ERROR']) && !empty($shipRes['ERROR']))
                $warnings = $shipRes['ERROR'];
            $message = "Shipment created successfully";

            return $this->sr->success(201,$message,$data,$warnings);
        }
        return $this->sr->error(    400, "Please contact system administrator",["Whoops, looks like something went wrong."]);
    }

//    public function transformCollection($shipsRes) {
//        $apiResp = ['status' => "success", 'status_code' => 200];
//        $hal = new Hal();
//        $errors = [];
//        foreach ($shipsRes as $key => $shipRes) {
//            $data = [
//                'identifier' => $key,
//                'shipment_number' => isset($shipRes['CONSIGNMENT_ID']) ? $shipRes['CONSIGNMENT_ID'] : "",
//                'order_reference' => isset($shipRes['ORDER_REFERENCE']) ? $shipRes['ORDER_REFERENCE'] : ""
//            ];
//
//            if (isset($shipRes['ERROR'])) {
//                $apiResp["status"] = "error";
//                if (is_array($shipRes['ERROR'])) {
//                    foreach ($shipRes['ERROR'] as $error) {
//                        $errors[] = $error;
//                    }
//                }
//            }
//
//            if ($apiResp["status"] == "success") {
//                $resource = new Hal(null, $data);
//                $hal->addResource('shipmentResponse', $resource);
//            } else {
//                $hal->addResource('shipmentResponse', NULL);
//            }
//        }
//        if (count($errors) > 0) {
//            $apiResp["errors"] = $errors;
//        }
//        $hal->setData($apiResp);
//        return $hal;
//    }

}
