<?php

namespace Smarttrack\Action;

use Error\ApiProblem;
use Error\Exception\ProblemException;
use Monolog\Logger;
use RKA\ContentTypeRenderer\ApiProblemRenderer;
use RKA\ContentTypeRenderer\HalRenderer;
use Smarttrack\ApiFunctions;
use SmarttrackTransformer\AddMultiShipmentsTransformer;
use SmarttrackTransformer\AddShipmentTransformer;
use SmarttrackTransformer\CustomTransformer;
use Zend\InputFilter\Factory as InputFilterFactory;

class AddMultiShipmentsAction {

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
        if ($userData->getId() < 1) {
            $problem = new ApiProblem(
                'Could not find user', 'http://zenddesk.smarttrack.com', 404
            );
            throw new ProblemException($problem);
        }
        $errosArray = [];
        $validatedConsignmentData = [];

        $shipmentsData = $request->getParams();
        if (is_array($shipmentsData) && isset($shipmentsData['shipments'])) {
            //validate shipments data 1 by 1
            foreach ($shipmentsData['shipments'] as $key => $shipmentsData) {
                $errors = ApiFunctions::ValidateShipnmentsDataTypes($shipmentsData);
                if(count($errors)) {
                    $errosArray["shipment_" . $key] = $errors;
                } else {
                    $parcelsTotalWeight = 0;
                    if (isset($shipmentsData['parcel']) && count($shipmentsData['parcel']) > 0) {
                        foreach ($shipmentsData['parcel'] as $parcelArr) {
                            if (trim($parcelArr['weight']) != "" && is_numeric($parcelArr['weight']))
                                $parcelsTotalWeight += $parcelArr['weight'];
                        }
                    }
                    $shipmentsData['weight'] = $parcelsTotalWeight;
//                    $data = $this->validate($shipmentsData, ['sender_contact', 'sender_address_line_1', 'sender_city', 'sender_postcode', 'sender_country_iso', 'receiver_contact', 'receiver_address_line_1', 'receiver_city', 'receiver_postcode', 'receiver_country_iso', 'service_code', 'weight', 'description', 'uuid']);
                    //$data = $this->validate($shipmentsData, ['countryIso', 'serviceCode','contact','address_line_1','city','postcode','weight','description','uuid']);
//                    if ($data['status'] == "success") {
//                        if (isset($data['data']) && is_array($data['data'])) {
                    $shipmentsData['api_uuid'] = isset($shipmentsData['uuid']) ? $shipmentsData['uuid'] : "";
                    $shipmentsData['order_reference'] = (!empty($shipmentsData['order_reference']) ? $shipmentsData['order_reference'] : str_replace(".", "", microtime(true)));
                    $shipmentsData['save_invalid'] = 0;
//                            $validConsignment = array_merge($shipmentsData, $data['data']);
                    $errors = ApiFunctions::ValidateShipnmentsDataTypes($shipmentsData);
                    if(count($errors)) {
                        $errorStr = implode("<br>", $errors);
                        $res['STATUS'] = "ERROR";
                        $res['MESSAGE'] = $errorStr;

                        $transformer = new AddShipmentTransformer();
                        $hal = $transformer->transformCollection($res);
                        $resultData = $this->renderer->render($request, $response, $hal);
                        ApiFunctions::AddApiLog("addMultiShipnment", $shipmentsData,$resultData ,$userId);
                        $errosArray["shipment_" . $key] = isset($errors["ERROR"]) ? $errors["ERROR"] : "";
                    }else{
                        $shipRes = ApiFunctions::validateShipment($shipmentsData, $userId);
                        if (isset($shipRes["STATUS"]) && $shipRes["STATUS"] == "SUCCESS") {
                            $validatedConsignmentData[$key] = isset($shipRes["DATA"]) ? $shipRes["DATA"] : $shipmentsData;
                        } else {
                            $errosArray["shipment_" . $key] = isset($shipRes["ERROR"]) ? $shipRes["ERROR"] : "";
                        }
                    }
//                        }
//                    } else {
//                        $errosArray["shipment_" . $key] = $data['errorMessage'];
//                    }
                }
            }
            if (count($errosArray) > 0) {
                if (!empty($errosArray) && count($errosArray) > 0) {
                    $shipmentsCreateResArray['STATUS'] = "ERROR";
                    $shipmentsCreateResArray['MESSAGE'] = "Please fix the errors first!";
                    $shipmentsCreateResArray['ERROR'] = $errosArray;
                    $errorTransformer = new CustomTransformer();
                    $hal = $errorTransformer->transformCollection($shipmentsCreateResArray);
                    return $this->renderer->render($request, $response, $hal);
                }
            }
        }
        //add shipments now
        $shipmentsCreateResArray = [];
        foreach ($validatedConsignmentData as $key => $shipData) {
            $shipmentsCreateResArray[$key] = ApiFunctions::addShipment($shipData, $userId);
        }
        $transformer = new AddMultiShipmentsTransformer();
        $hal = $transformer->transformCollection($shipmentsCreateResArray);
        $resultData = $this->renderer->render($request, $response, $hal);
        ApiFunctions::AddApiLog("addMultiShipnment", $shipmentsData,$resultData ,$userId);
        return $resultData;
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
            /* 'uuid' => [
              'required' => true,
              'filters' => [
              ['name' => 'StringTrim'],
              ['name' => 'StripTags'],
              ]
              ], */
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
