<?php
namespace SmarttrackTransformer;

use Nocarrier\Hal;
use Smarttrack\ApiFunctions;

/**
 * Transform an Author (or collection of Authors) into Hal resource
 */
class GenerateLabelTransformer
{
	public function transformCollection($shipRes)
	{
		$apiResp = ['status' => "success",'status_code' => 200];
		$hal = new Hal();
		$data =[
		];
                if(isset($shipRes['MESSAGE']) && $shipRes['STATUS'] != 'ERROR'){
			$data['message'] = $shipRes['MESSAGE'];
		}
                if(isset($shipRes['STATUS']) && $shipRes['STATUS'] == 'ERROR'){
                    if(is_array($shipRes['MESSAGE']))
                        $errorMessage = implode (', ', $shipRes['MESSAGE']);
                    else
                        $errorMessage = $shipRes['MESSAGE'];
                    
                    $apiResp['errors'] = $data['message'] = isset($shipRes['MESSAGE']) ? $shipRes['MESSAGE'] : 'Label can not be generated please contact system administrator.';
                    $apiResp["status"] = "error";
                }
		if(isset($shipRes['ERROR'])){
                  //  $data['message'] = '';
                    if(is_array($shipRes['ERROR']))
                        $errorMessage = implode (', ', $shipRes['ERROR']);
                    else
                        $errorMessage = $shipRes['ERROR'];
                    $apiResp['errors'] = $data['message'] = $errorMessage;
                    $apiResp["status"] = "error";
		}
		if(isset($shipRes['LABEL'])){
                    $data['label'] = $shipRes['LABEL'];
		}
                if(isset($shipRes['LABEL_BIN_STR'])){
                    $data['Label_bin_str'] = $shipRes['LABEL_BIN_STR'];
                }
		if(isset($shipRes['TRACKING_NUMBER'])){
                    $data['trackingNumber'] = $shipRes['TRACKING_NUMBER'];
		}
        if(isset($shipRes['AWB'])){
            $data['shipment_number'] = $shipRes['AWB'];
        }
        if(isset($shipRes['PARCEL_LABEL'])){
            $data['parcel_label'] = $shipRes['PARCEL_LABEL'];
        }
		//print_r($data); exit;
		$resource = new Hal(null, $data);
		$hal->addResource('LabelResponse', $resource);
		$hal->setData($apiResp);
		return $hal;
	}
}
