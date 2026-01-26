<?php

namespace SmarttrackTransformer\V2;

use Smarttrack\V2\SmartTransformer;
use Smarttrack\V2\ApiFunctions;

/**
 * Transform an Author (or collection of Authors) into Hal resource
 */
class ServicesTransformer  extends SmartTransformer {
    public function transform($userService) {
        $mapping = [];
        if(count($userService) > 0 && $userService['STATUS'] != 'ERROR') {
            $mapping['STATUS'] = "SUCCESS";
            $mapping['count'] = count($userService);
            $i = 0;
            foreach ($userService as $userServiceData) {
                $deliveryCountries = [];
                $serviceDeliveryCountries = ApiFunctions::getServiceDeliveryCountries($userServiceData->getId());
                foreach ($serviceDeliveryCountries as $serviceCountry) {
                    $dC['country_iso'] = $serviceCountry->getDeliveryCountryIso();
                    $dC['country_name'] = $serviceCountry->getDeliveryCountryName();
                    $dC['transit_time'] = $serviceCountry->getServiceTransitTime();
                    $deliveryCountries[] = $dC;
                }
                $mapping['data'][$i] = [
                    'service_id' => $userServiceData->getId(),
                    'service_name' => $userServiceData->getName(),
                    'service_code' => $userServiceData->getCode(),
                    'service_description' => $userServiceData->getDescription(),
                    'origin_country_name' => $userServiceData->getOriginCountryName(),
                    'origin_country_iso' => $userServiceData->getOriginCountryIso(),
                    'from_weight' => $userServiceData->getFromWeight(),
                    'to_weight' => $userServiceData->getToWeight(),
                    'delivery_countries' => $deliveryCountries,
                    'validation_type' => $userServiceData->getValidationType(),
                ];
                if($userServiceData->getValidationType() == 'courier') {
                    $mapping['data'][$i]['max_length'] = $userServiceData->getMaxLength();
                    $mapping['data'][$i]['max_width'] = $userServiceData->getMaxWidth();
                    $mapping['data'][$i]['max_height'] = $userServiceData->getMaxHeight();
                    $mapping['data'][$i]['girth_value'] = $userServiceData->getGirth();
                    $mapping['data'][$i]['girth_formula'] = $userServiceData->getGirthFormula();
                    $mapping['data'][$i]['max_volumetric_weight'] = $userServiceData->getmaxVolumetricWeight();
                    $mapping['data'][$i]['volumetric_denominator'] = $userServiceData->getVolumetricDenominator();
                } elseif ($userServiceData->getValidationType() == 'mail') {
                    $mapping['data'][$i]['maximum_dim_formula'] = $userServiceData->getMaximumDimFormula();
                    $mapping['data'][$i]['maximum_allowed_dimension'] = $userServiceData->getMaximumAllowedDimension();
                }
                unset($deliveryCountries);
                $i++;
            }
            if (isset($userService['STATUS']) && $userService['STATUS'] == 'ERROR') {
                $message = "Please fix below error";
                $error [] = $userService['ERROR'];
                return $this->sr->error(400, $message, $error);
            }
            if (isset($mapping['STATUS']) && $mapping['STATUS'] == 'SUCCESS') {
                $warnings = [];
                if (isset($mapping['ERROR']) && !empty($mapping['ERROR']))
                    $warnings = $mapping['ERROR'];

                $message = "Service data found";

                return $this->sr->success(200, $message, $mapping['data'], $warnings);
            }
        } else {
            return $this->sr->error(400,  $userService['MESSAGE'],[$userService['ERROR']]);
        }
        return $this->sr->error(400, "Please contact system administrator",["Whoops, looks like something went wrong."]);
    }

}
