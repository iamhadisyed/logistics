<?php
namespace Smarttrack\V2\Action;

use Error\ApiProblem;
use Error\Exception\ProblemException;
use Monolog\Logger;
use RKA\ContentTypeRenderer\HalRenderer;
use Smarttrack\V2\ApiFunctions;
use SmarttrackTransformer\V2\AddShipmentTransformer;
use Zend\InputFilter\Factory as InputFilterFactory;
use Smarttrack\V2\SmartAction;

class AddShipmentAction extends SmartAction {

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
        $this->logger->info("Add Shipment For User ", ['api_user_id' => $userId]);
        $userData = ApiFunctions::getUserById($userId);

        $saveLog=[];
        $saveLog['api']="AddShipmentAction";
        $fileLogDate = date("d-m-Y");
        $saveLog['time'] = date("d-m-Y H:i:s");
        $filePath = SETTING_DIR_ASSETS.'api_log';
        if (!is_dir($filePath)) {
            mkdir($filePath, 0777, true);
        }
        $fileFullName = $filePath."/".$fileLogDate."_api_log.html";

        $labelResponse = [];
        // Show error here
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

            $tarnsform = new AddShipmentTransformer($request, $response);
            return $tarnsform->transform($labelResponse);
        }

        $shipData = $request->getParams();
        $invalidParams = ApiFunctions::validateShipmentParams($shipData,['label_type','label_size']);
        $errors = ApiFunctions::ValidateShipnmentsDataTypes($shipData);
        if(count($errors) > 0){
            $labelResponse['STATUS'] = "ERROR";
            $labelResponse['ERROR'] = $errors;
            $labelResponse['MESSAGE'] = "Please fix below error(s)";

            $tarnsform = new AddShipmentTransformer($request, $response);
            return $tarnsform->transform($labelResponse);
        }
        if(count($invalidParams) > 0){
            $errorArr = [];
            foreach ($invalidParams as $invalidParam) {
                if(!is_array($invalidParam)){
                    $errorArr[] = $invalidParam." is invalid parameter.";
                }else if(is_array($invalidParam)){
                    foreach ($invalidParam as $key => $parcelParms){
                        foreach ($parcelParms as $parcelParm) {
                            $errorArr[] = "Parcel ".$key." has ".$parcelParm." invalid parameter.";
                        }
                    }
                }
            }
            $labelResponse['STATUS'] = "ERROR";
            $labelResponse['ERROR'] = $errorArr;
            $labelResponse['MESSAGE'] = "Please fix below error(s)";

            $tarnsform = new AddShipmentTransformer($request, $response);
            return $tarnsform->transform($labelResponse);
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
//            $shipData['order_reference'] = (!empty($shipData['order_reference']) ? $shipData['order_reference'] : time());
            $resData = ApiFunctions::addShipment($shipData, $userId);

            /*
             * Log System
            */
            $shipData = $request->getParams();
            $saveLog['request'] = $shipData;
            $saveLog['response'] = $resData;
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

            $tarnsform = new AddShipmentTransformer($request, $response);
            return $tarnsform->transform($resData);
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
