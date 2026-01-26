<?php

namespace Smarttrack\Action;

use Error\ApiProblem;
use Error\Exception\ProblemException;
use Monolog\Logger;
use RKA\ContentTypeRenderer\ApiProblemRenderer;
use RKA\ContentTypeRenderer\HalRenderer;
use Smarttrack\ApiFunctions;
use SmarttrackTransformer\ValidateServiceDimensionTransformer;
use SmarttrackTransformer\CustomTransformer;
use Zend\InputFilter\Factory as InputFilterFactory;

class ValidateServiceDimensionAction {

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
        $this->logger->info("Validate service weight and dimensions ", ['api_user_id' => $userId]);
        $userData = ApiFunctions::getUserById($userId);
        if ($userData->getId() < 1) {
            $problem = new ApiProblem(
                    'Could not find user', 'http://zenddesk.smarttrack.com', 404
            );
            throw new ProblemException($problem);
        }
        $errosArray = [];
        $validatedServiceData = [];
        $shipmentsData = $request->getParams();
        if (is_array($shipmentsData) && isset($shipmentsData['parcel'])) {
            //validate shipments data 1 by 1
            $parcelsTotalWeight = 0;
            foreach ($shipmentsData['parcel'] as $parcelArr) {
                if (trim($parcelArr['weight']) != "" && is_numeric($parcelArr['weight']))
                    $parcelsTotalWeight += $parcelArr['weight'];
            }
            $shipmentsData['weight'] = $parcelsTotalWeight;
            $data = $this->validate($shipmentsData, ['service_code', 'sender_country_iso', 'receiver_country_iso']);
            if ($data['status'] == "success") {
                if (isset($data['data']) && is_array($data['data'])) {
                    $validServiceDims = array_merge($shipmentsData, $data['data']);
                    $shipRes = ApiFunctions::validateServiceDimension($validServiceDims, $userId);
                    if (isset($shipRes["STATUS"]) && $shipRes["STATUS"] == "ERROR") {
                        $errosArray[] = isset($shipRes["ERROR"]) ? $shipRes["ERROR"] : "";
                    }
                }
            } else {
                $errosArray[] = $data['errorMessage'];
            }

            if (count($errosArray) > 0) {
                if (!empty($errosArray) && count($errosArray) > 0) {
                    $shipmentsValidateResArray['STATUS'] = "ERROR";
                    $shipmentsValidateResArray['MESSAGE'] = "Please fix the errors first!";
                    $shipmentsValidateResArray['ERROR'] = $errosArray;
                    $errorTransformer = new ValidateServiceDimensionTransformer();
                    $hal = $errorTransformer->transformCollection($shipmentsValidateResArray);
                    $resultData = $this->renderer->render($request, $response, $hal);
                    ApiFunctions::AddApiLog("validate-service-dimensions", $shipmentsData,$resultData ,$userId);
                    return $resultData;
                }
            } else {
                $shipmentsValidateResArray['STATUS'] = "SUCCESS";
                $shipmentsValidateResArray['MESSAGE'] = "successfully validate";
                $errorTransformer = new ValidateServiceDimensionTransformer();
                $hal = $errorTransformer->transformCollection($shipmentsValidateResArray);
                $resultData = $this->renderer->render($request, $response, $hal);
                ApiFunctions::AddApiLog("validate-service-dimensions", $shipmentsData,$resultData ,$userId);
                return $resultData;
            }
        } else {
            $shipmentsValidateResArray['STATUS'] = "ERROR";
            $shipmentsValidateResArray['MESSAGE'] = "Parcel data is not valid";
            $shipmentsValidateResArray['ERROR'] = [$shipmentsValidateResArray['MESSAGE']];
            $errorTransformer = new ValidateServiceDimensionTransformer();
            $hal = $errorTransformer->transformCollection($shipmentsValidateResArray);
            $resultData = $this->renderer->render($request, $response, $hal);
            ApiFunctions::AddApiLog("validate-service-dimensions", $shipmentsData,$resultData ,$userId);
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
            'service_code' => [
                'required' => true,
            ],
            'sender_country_iso' => [
                'required' => true,
            ],
            'receiver_country_iso' => [
                'required' => true,
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
