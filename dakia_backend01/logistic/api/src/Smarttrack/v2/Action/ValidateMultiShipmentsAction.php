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

class ValidateMultiShipmentsAction {

    protected $logger;
    protected $renderer;
    protected $authorMapper;
    protected $userServiceRoutingMapper;

    public function __construct(Logger $logger, HalRenderer $renderer) {
        $this->logger = $logger;
        $this->renderer = $renderer;
    }

    public function __invoke($request, $response) {
//	    echo "<pre>"; print_r($request->getParams()); echo "</pre>"; die();
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
                $parcelsTotalWeight = 0;
                if (isset($shipmentsData['parcel']) && count($shipmentsData['parcel']) > 0) {
                    foreach ($shipmentsData['parcel'] as $parcelArr) {
                        if (trim($parcelArr['weight']) != "" && is_numeric($parcelArr['weight']))
                            $parcelsTotalWeight += $parcelArr['weight'];
                    }
                }
                $shipmentsData['weight'] = $parcelsTotalWeight;
                $data = $this->validate($shipmentsData, ['sender_contact', 'sender_address_1', 'sender_city', 'sender_postcode', 'sender_country_iso', 'receiver_contact', 'receiver_address_1', 'receiver_city', 'receiver_postcode', 'receiver_country_iso', 'service_code', 'weight', 'description', 'uuid']);
                //$data = $this->validate($shipmentsData, ['countryIso', 'serviceCode','contact','address_line_1','city','postcode','weight','description']);
                if ($data['status'] == "success") {
                    if (isset($data['data']) && is_array($data['data'])) {
                        if (empty($shipmentsData['order_reference'])) {
                            $shipmentsData['order_reference'] = time() . mt_rand(1000, 999999);
                        }
                        $validConsignment = array_merge($shipmentsData, $data['data']);
                        $shipRes = ApiFunctions::validateShipment($validConsignment, $userId);
                        if (isset($shipRes["STATUS"]) && $shipRes["STATUS"] == "SUCCESS") {
                            $validatedConsignmentData[] = isset($shipRes["DATA"]) ? $shipRes["DATA"] : $validConsignment;
                        } else {
                            $errosArray["shipment_" . $key] = isset($shipRes["ERROR"]) ? $shipRes["ERROR"] : "";
                        }
                    }
                } else {
                    $errosArray["shipment_" . $key] = $data['errorMessage'];
                }
            }

            if (count($errosArray) > 0) {
                if (!empty($errosArray) && count($errosArray) > 0) {
                    $shipmentsValidateResArray['STATUS'] = "ERROR";
                    $shipmentsValidateResArray['MESSAGE'] = "Please fix the errors first!";
                    $shipmentsValidateResArray['ERROR'] = $errosArray;
                    $errorTransformer = new CustomTransformer();
                    $hal = $errorTransformer->transformCollection($shipmentsValidateResArray);
                    $resultData = $this->renderer->render($request, $response, $hal);
                    ApiFunctions::AddApiLog("validate-multi-shipments", $shipmentsData,$resultData ,$userId);
                    return $resultData;
                }
            } else {
                $shipmentsValidateResArray['STATUS'] = "SUCCESS";
                $shipmentsValidateResArray['MESSAGE'] = "successfully validate";
//				$shipmentsValidateResArray['ERROR'] = [$shipmentsValidateResArray['MESSAGE']];
                $errorTransformer = new CustomTransformer();
                $hal = $errorTransformer->transformCollection($shipmentsValidateResArray);
                $resultData = $this->renderer->render($request, $response, $hal);
                ApiFunctions::AddApiLog("validate-multi-shipments", $shipmentsData,$resultData ,$userId);
                return $resultData;
            }
        } else {
            $shipmentsValidateResArray['STATUS'] = "ERROR";
            $shipmentsValidateResArray['MESSAGE'] = "Oops! something went wrong. please try again later!";
            $shipmentsValidateResArray['ERROR'] = [$shipmentsValidateResArray['MESSAGE']];
            $errorTransformer = new CustomTransformer();
            $hal = $errorTransformer->transformCollection($shipmentsValidateResArray);
            $resultData = $this->renderer->render($request, $response, $hal);
            ApiFunctions::AddApiLog("validate-multi-shipments", $shipmentsData,$resultData ,$userId);
            return $resultData;
        }
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
                            'pattern' => '/(^\d*\.?\d*[1-9]+\d*$)|(^[1-9]+\d*\.\d*$)/',
                            'messages' => array('regexNotMatch' => "Weight must be greater than 0")
                        ],
                    ]
                ]
            ],
            'description' => [
                'required' => true
            ]
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
