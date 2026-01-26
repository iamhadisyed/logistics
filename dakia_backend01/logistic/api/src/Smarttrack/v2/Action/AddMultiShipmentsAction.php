<?php

namespace Smarttrack\V2\Action;

use Monolog\Logger;
use RKA\ContentTypeRenderer\HalRenderer;
use Smarttrack\V2\ApiFunctions;
use SmarttrackTransformer\V2\AddMultiShipmentsTransformer;
use Zend\InputFilter\Factory as InputFilterFactory;
use Smarttrack\V2\SmartAction;

class AddMultiShipmentsAction extends SmartAction {

    protected $logger;
    protected $renderer;
    protected $authorMapper;
    protected $userServiceRoutingMapper;

    public function __construct(Logger $logger, HalRenderer $renderer) {
        $this->logger = $logger;
        $this->renderer = $renderer;
    }

    public function __invoke($request, $response) {
        $userId = $request->getAttribute('api_user_id');
        $this->logger->info("Getting Lable For User ", ['api_user_id' => $userId]);
        $userData = ApiFunctions::getUserById($userId);

        $saveLog=[];
        $saveLog['api']="GenerateLabelAction";
        $fileLogDate = date("d-m-Y");
        $saveLog['time'] = date("d-m-Y H:i:s");
        $filePath = SETTING_DIR_ASSETS.'api_log';
        if (!is_dir($filePath)) {
            mkdir($filePath, 0777, true);
        }
        $fileFullName = $filePath."/".$fileLogDate."_api_log.html";


        if ($userData->getId() < 1) {
            $errors = ['User does not exist.'];
            $labelResponse['STATUS'] = "ERROR";
            $labelResponse['ERROR'] = $errors;
            $labelResponse['MESSAGE'] = "Please fix below error(s)";

            /*
             * Log System
            */
            $shipData = $request->getParams();
            $saveLog['request'] = $shipData;
            $saveLog['response'] = $labelResponse;
            $allFileData = json_encode($saveLog);

            if (!is_dir($filePath)) {
                mkdir($filePath, 0777, true);
            }
            $current = file_get_contents($fileFullName);
            $current .= $allFileData." \n";
            file_put_contents($fileFullName, $current);
            /*
             * End Log System
             */

            $tarnsform = new AddMultiShipmentsTransformer($request, $response);
            return $tarnsform->transform($labelResponse);
        }
        $errosArray = [];
        $validatedConsignmentData = [];
        $shipmentsData = $request->getParams();
        if(empty($shipmentsData)){
            $errors = ['No parameter is submitted.'];
            $labelResponse['STATUS'] = "ERROR";
            $labelResponse['ERROR'] = $errors;
            $labelResponse['MESSAGE'] = "Please fix below error(s)";

            /*
             * Log System
            */
            $shipData = $request->getParams();
            $saveLog['request'] = $shipData;
            $saveLog['response'] = $labelResponse;
            $allFileData = json_encode($saveLog);

            if (!is_dir($filePath)) {
                mkdir($filePath, 0777, true);
            }
            $current = file_get_contents($fileFullName);
            $current .= $allFileData." \n";
            file_put_contents($fileFullName, $current);
            /*
             * End Log System
             */

            $tarnsform = new AddMultiShipmentsTransformer($request, $response);
            return $tarnsform->transform($labelResponse);
        }
        if (is_array($shipmentsData) && isset($shipmentsData['shipments'])) {
            //validate shipments data 1 by 1
            $errorArr = [];
            foreach ($shipmentsData['shipments'] as $key => $shipmentsData) {
                // Just tells that if any invalid param entred
                $invalidParams = ApiFunctions::validateShipmentParams($shipmentsData);
                if(count($invalidParams) > 0){
                    foreach ($invalidParams as $invalidParam) {
                        if(!is_array($invalidParam)){
                            $errorArr["shipment_" . $key][] = $invalidParam." is invalid parameter.";
                        }else if(is_array($invalidParam)){
                            foreach ($invalidParam as $parcelKey => $parcelParms){
                                foreach ($parcelParms as $parcelParm) {
                                    $errorArr["shipment_" .$key."_". $parcelKey][] = "Parcel ".$key." has ".$parcelParm." invalid parameter.";
                                }
                            }
                        }
                    }
                }
                if(count($errorArr["shipment_" . $key]) == 0)  {
                    $parcelsTotalWeight = 0;
                    if (isset($shipmentsData['parcel']) && count($shipmentsData['parcel']) > 0) {
                        foreach ($shipmentsData['parcel'] as $parcelArr) {
                            if (trim($parcelArr['weight']) != "" && is_numeric($parcelArr['weight']))
                                $parcelsTotalWeight += $parcelArr['weight'];
                        }
                    }
                    $shipmentsData['weight'] = $parcelsTotalWeight;
                    $shipmentsData['api_uuid'] = isset($shipmentsData['uuid']) ? $shipmentsData['uuid'] : "";
                    $shipmentsData['order_reference'] = (!empty($shipmentsData['order_reference']) ? $shipmentsData['order_reference'] : str_replace(".", "", microtime(true)));
                    $shipmentsData['save_invalid'] = 0;
                    $errors = ApiFunctions::ValidateShipnmentsDataTypes($shipmentsData);
                        if(count($errors)) {
                            $errorStr = implode("<br>", $errors);
                            foreach ($errors as $error) {
                                $errorArr["shipment_" . $key][] = $error;
                            }
                        }else{
                            $shipRes = ApiFunctions::validateShipment($shipmentsData, $userId);
                            if (isset($shipRes["STATUS"]) && $shipRes["STATUS"] == "SUCCESS") {
                                $validatedConsignmentData[$key] = isset($shipRes["DATA"]) ? $shipRes["DATA"] : $shipmentsData;
                            } else {
                                $errorArr["shipment_" . $key] = isset($shipRes["ERROR"]) ? $shipRes["ERROR"] : "";
                            }
                        }
                }
            } // End for each
        }
        //add shipments now
        $shipmentsCreateResArray = [];
        foreach ($validatedConsignmentData as $key => $shipData) {
            $shipmentsCreateResArray[$key] = ApiFunctions::addShipment($shipData, $userId);
            if(isset($shipData['custom_identifier']))
                $shipmentsCreateResArray[$key]['custom_identifier'] = $shipData['custom_identifier'];
        }
        $errorArr['DATA'] = $shipmentsCreateResArray;
        if(count($errorArr) > 0)  {
            $errorArr['ERROR'] = $errorArr;
            $errorArr['STATUS'] = "ERROR";
            if(count($validatedConsignmentData) > 0)
                $errorArr['STATUS'] = "SUCCESS";
            $errorArr['MESSAGE'] = "Please fix below error(s)";
        }

        /*
             * Log System
            */
        $shipData = $request->getParams();
        $saveLog['request'] = $shipData;
        $saveLog['response'] = $errorArr;
        $allFileData = json_encode($saveLog);

        if (!is_dir($filePath)) {
            mkdir($filePath, 0777, true);
        }
        $current = file_get_contents($fileFullName);
        $current .= $allFileData." \n";
        file_put_contents($fileFullName, $current);
        /*
         * End Log System
         */

        $tarnsform = new AddMultiShipmentsTransformer($request, $response);
        return $tarnsform->transform($errorArr);
    }

    /**
     * Validate data to be applied to this entity
     *
     * @param  array $data
     * @return array
     */
    public function validate($data, $elements = []) {
        $resp = ["status" => "error", "errorMessage" => "", "data" => []];
        $inputFilter = $this->createInputFilter($elements);
        $inputFilter->setData($data);

        if ($inputFilter->isValid()) {
            $resp["status"] = "success";
            $resp["data"] = $inputFilter->getValues();
        } else {
            $resp["errorMessage"] = $inputFilter->getMessages();
        }
        return $resp;
    }

    protected function createInputFilter($elements = []) {
        $specification = [
            'sender_contact' => [
                'required' => true,
            ],
            'sender_address_line_1' => [
                'required' => true,
            ],
            'sender_city' => [
                'required' => true,
            ],
            'sender_postcode' => [
                'required' => true,
            ],
            'sender_country_iso' => [
                'required' => true,
            ],
            'service_code' => [
                'required' => true,
                'filters' => [
                    ['name' => 'StringTrim'],
                    ['name' => 'StripTags'],
                ]
            ],
            'receiver_contact' => [
                'required' => true
            ],
            'receiver_address_line_1' => [
                'required' => true
            ],
            'receiver_city' => [
                'required' => true
            ],
            'receiver_postcode' => [
                'required' => true
            ],
            'weight' => [
                'required' => true,
                'validators' => [
                    [
                        'name' => 'Regex',
                        'options' => [
                            'pattern'=>'/(^\d*\.?\d*[1-9]+\d*$)|(^[1-9]+\d*\.\d*$)/',
                            'messages' => array('regexNotMatch' => "Weight must be greater than 0")
                        ],
                    ]
                ]
            ],
            'description' => [
                'required' => true
            ],
        ];

        if ($elements) {
            $specification = array_filter(
                $specification, function ($key) use ($elements) {
                return in_array($key, $elements);
            }, ARRAY_FILTER_USE_KEY
            );
        }

        $factory = new InputFilterFactory();
        $inputFilter = $factory->createInputFilter($specification);

        return $inputFilter;
    }

}
