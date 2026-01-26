<?php

namespace SmarttrackTransformer;

use Nocarrier\Hal;
use Smarttrack\ApiFunctions;

/**
 * Transform an Author (or collection of Authors) into Hal resource
 */
class ServicesTransformer {

    public function transformCollection($userServices) {
        $apiResp = ['status' => "success", 'status_code' => 200, "count" => 0];
        $hal = new Hal();
        $count = 0;
        if(isset($userServices['STATUS']) == 'ERROR'){
            $apiResp["status"] = "error";
            $apiResp["message"] = $userServices['MESSAGE'];
            $apiResp["error"] = $userServices['ERROR'];
            $apiResp["status_code"] = 400;
            $hal->setData($apiResp);
        }else{
            if (count($userServices) > 0) {
                foreach ($userServices as $userService) {
                    $count ++;
                    $hal->addResource('service', $this->transform($userService));
                }
                $apiResp["count"] = $count;
                $apiResp["message"] = "Records Found";
                $hal->setData($apiResp);
            } else {
                $apiResp["status"] = "error";
                $apiResp["message"] = "No Record Found!";
                $apiResp["status_code"] = 400;
                $hal->setData($apiResp);
            }
        }
        return $hal;
    }

    public function transform($userService) {
        $serviceDeliveryCountries = ApiFunctions::getServiceDeliveryCountries($userService->getId());
        $deliveryCountries = [];
        foreach ($serviceDeliveryCountries as $serviceCountry) {
            $dC['countryIso'] = $serviceCountry->getDeliveryCountryIso();
            $dC['countryName'] = $serviceCountry->getDeliveryCountryName();
            $dC['transitTime'] = $serviceCountry->getServiceTransitTime();
            $deliveryCountries[] = $dC;
        }
//	    echo "<pre>"; print_r($serviceDeliveryCountries); echo "</pre>";die();
//	    $dlCountryData = explode("&*SP*&",$userService->getServiceDeliveryCountries());
//	    foreach($dlCountryData as $data){
//		    $cDateArray = explode("&|&",$data);
//		    $dC['countryIso'] =$cDateArray[0];
//		    $dC['countryName'] =$cDateArray[1];
//		    $dC['transitTime'] =$cDateArray[2];
//		    $deliveryCountries[]=$dC;
//	    }

        $data = [
//	      'serviceId'          =>  $userService->getId(),
            'serviceName' => $userService->getName(),
            'serviceCode' => $userService->getCode(),
            'serviceDesc' => $userService->getDescription(),
            'originCountryName' => $userService->getOriginCountryName(),
            'originCountryIso' => $userService->getOriginCountryIso(),
            'deliveryCountries' => $deliveryCountries,
            'from_weight' => $userService->getFromWeight(),
            'to_weight' => $userService->getToWeight(),
            'validation_type' => $userService->getValidationType(),
            'max_length' => $userService->getMaxLength(),
            'max_width' => $userService->getMaxWidth(),
            'max_height' => $userService->getMaxHeight(),
            'maximum_dim_formula' => $userService->getMaximumDimFormula(),
            'maximum_allowed_dimension' => $userService->getMaximumAllowedDimension()
        ];

//        $resource = new Hal('/api/get-services' . $data['service_id'], $data);
        $resource = new Hal(null, $data);

//        $resource->addLink('carriers', '/service/' . $data['service_id'] . '/carrier');

        return $resource;
    }

}
