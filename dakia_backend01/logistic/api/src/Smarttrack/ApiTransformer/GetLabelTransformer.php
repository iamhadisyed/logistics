<?php
namespace SmarttrackTransformer;

use Nocarrier\Hal;
use Smarttrack\ApiFunctions;

/**
 * Transform an Author (or collection of Authors) into Hal resource
 */
class GetLabelTransformer
{
	public function transformCollection($shipRes)
	{
		$apiResp = ['status' => "success",'status_code' => 200];
		$hal = new Hal();
		$data =[
//			'status'          =>  strtolower($shipRes['STATUS']),
		];
		if(isset($shipRes['ERROR'])){
			$apiResp['errors'] = $shipRes['ERROR'];
			$apiResp["status"] = "error";
		}
		if(isset($shipRes['STATUS']) && $shipRes['STATUS'] == "ERROR"){
			$apiResp['errors'] = $shipRes['ERROR'];
			$apiResp["status"] = "error";
		}
		if(isset($shipRes['MESSAGE'])){
			$data['message'] = $shipRes['MESSAGE'];
		}
		if(isset($shipRes['LABEL'])){
			$data['label_url'] = $shipRes['LABEL'];
		}
        if(isset($shipRes['LABEL_BIN_STR'])){
            $data['Label_bin_str'] = $shipRes['LABEL_BIN_STR'];
        }
        if(isset($shipRes['TRACKING_NUMBER'])){
            $data['tracking_number'] = $shipRes['TRACKING_NUMBER'];
        }
        if(isset($shipRes['ORDER_REFERENCE'])){
            $data['order_reference'] = $shipRes['ORDER_REFERENCE'];
        }
        if(isset($shipRes['AWB'])){
            $data['shipment_number'] = $shipRes['AWB'];
        }
        if(isset($shipRes['PARCEL_LABEL'])){
            $data['parcel_label'] = $shipRes['PARCEL_LABEL'];
        }
        $resource = new Hal(null, $data);
        $hal->setResource('labelResponse', $resource);
		$hal->setData($apiResp);
		return $hal;
	}
}
