<?php
namespace Smarttrack\Action;

use Error\ApiProblem;
use Error\Exception\ProblemException;
use Monolog\Logger;
use RKA\ContentTypeRenderer\ApiProblemRenderer;
use RKA\ContentTypeRenderer\HalRenderer;
use Smarttrack\ApiFunctions;
use SmarttrackTransformer\AddShipmentTransformer;
use Zend\InputFilter\Factory as InputFilterFactory;

class AddShipmentAction {

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
        $this->logger->info("Add Shipment For User ", ['api_user_id' => $userId]);
        $userData = ApiFunctions::getUserById($userId);
        if ($userData->getId() < 1) {
            $problem = new ApiProblem(
                'Could not find user', 'http://zenddesk.smarttrack.com', 404
            );
            throw new ProblemException($problem);
        }
        $shipData = $request->getParams();
        $errors = ApiFunctions::ValidateShipnmentsDataTypes($shipData);
        if(count($errors)) {
            $errorStr = implode("<br>", $errors);
            $res['STATUS'] = "ERROR";
            $res['MESSAGE'] = $errorStr;

            $transformer = new AddShipmentTransformer();
            $hal = $transformer->transformCollection($res);
            $resultData = $this->renderer->render($request, $response, $hal);
            ApiFunctions::AddApiLog("addShipnment", $shipData,$resultData ,$userId);
        } else {
            $parcelsTotalWeight = 0;
            if (isset($shipData['parcel']) && count($shipData['parcel']) > 0) {
                foreach ($shipData['parcel'] as $parcelArr) {
                    if (trim($parcelArr['weight']) != "" && is_numeric($parcelArr['weight'])) {
                        $parcelsTotalWeight += $parcelArr['weight'];
                    }
                }
            }
            $shipData['weight'] = $parcelsTotalWeight;
            $shipData['save_invalid'] = 0;
            $shipData['api_uuid'] = isset($shipData['uuid']) ? $shipData['uuid'] : "";
            $shipData['order_reference'] = (!empty($shipData['order_reference']) ? $shipData['order_reference'] : time());
            //$data = $this->validate($shipData, ['sender_contact', 'sender_address_line_1', 'sender_city', 'sender_postcode', 'sender_country_iso', 'receiver_contact', 'receiver_address_line_1', 'receiver_city', 'receiver_postcode', 'receiver_country_iso', 'service_code', 'weight', 'description', 'uuid']);
                $resData = ApiFunctions::addShipment($shipData, $userId);
                $transformer = new AddShipmentTransformer();
                $hal = $transformer->transformCollection($resData);
                $resultData = $this->renderer->render($request, $response, $hal);
                ApiFunctions::AddApiLog("addShipnment", $shipData,$resultData ,$userId);
        }
        return $resultData;
    }

    /**
     * Validate data to be applied to this entity
     *
     * @param  array $data
     * @return array
     */
    public function validate($data, $elements = []) {
        $inputFilter = $this->createInputFilter($elements);
        $inputFilter->setData($data);

        if ($inputFilter->isValid()) {
            return $inputFilter->getValues();
        }

        $problem = new ApiProblem(
            'Validation failed', 'about:blank', 400
        );
        $problem['errors'] = $inputFilter->getMessages();

        throw new ProblemException($problem);
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
