<?php
namespace Smarttrack\V2\Action;

use Error\ApiProblem;
use Error\Exception\ProblemException;
use Monolog\Logger;
use RKA\ContentTypeRenderer\ApiProblemRenderer;
use RKA\ContentTypeRenderer\HalRenderer;
use Slim\App;
use Smarttrack\V2\ApiFunctions;
use SmarttrackTransformer\V2\GenerateLabelTransformer;
use Zend\InputFilter\Factory as InputFilterFactory;
use Smarttrack\V2\SmartAction;

class GenerateLabelAction extends SmartAction
{
    protected $logger;
    protected $renderer;
    protected $authorMapper;
    protected $userServiceRoutingMapper;

    public function __invoke($request, $response)
    {
        $generateLabelRes=[];
        $saveLog=[];
        $saveLog['api']="GenerateLabelAction";

        $userId = $request->getAttribute('api_user_id');
        $this->logger->info("Getting Lable For User ", ['api_user_id' => $userId]);
        $userData = ApiFunctions::getUserById($userId);

        $fileLogDate = date("d-m-Y");
        $saveLog['time'] = date("d-m-Y H:i:s");
        $filePath = SETTING_DIR_ASSETS.'api_log';
        if (!is_dir($filePath)) {
            mkdir($filePath, 0777, true);
        }
        $fileFullName = $filePath."/".$fileLogDate."_api_log.html";

        if($userData->getId() < 1){
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

            $tarnsform = new GenerateLabelTransformer($request, $response);
            return $tarnsform->transform($labelResponse);
        }
        $shipData = $request->getParams();
        $saveLog['request'] = $shipData;

        $invalidParams = ApiFunctions::validateShipmentParams($shipData,['label_type','label_size']);
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
            $saveLog['response'] = $labelResponse;

        /*
        * Log System
        */
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
            $tarnsform = new GenerateLabelTransformer($request, $response);
            return $tarnsform->transform($labelResponse);
        }

        $parcelsTotalWeight = 0;
        if(isset($shipData['parcel']) && count($shipData['parcel']) > 0) {
            foreach ($shipData['parcel'] as $parcelArr) {
                if (trim($parcelArr['weight']) != "" && is_numeric($parcelArr['weight']))
                    $parcelsTotalWeight += $parcelArr['weight'];
            }
        }
        $shipData['weight'] = $parcelsTotalWeight;
        $shipData['api_uuid'] = isset($shipData['uuid']) ? $shipData['uuid'] : "";
        $shipData["label_type"] = (!empty($shipData["label_type"]) ? $shipData["label_type"] : 'pdf');
        $shipData["label_size"] = (!empty($shipData["label_size"]) ? $shipData["label_size"] : '100x150');
        $errors = ApiFunctions::ValidateShipnmentsDataTypes($shipData);
        if(count($errors)) {
            $generateLabelRes['STATUS'] = "ERROR";
            $generateLabelRes['MESSAGE'] = $errors;
            $generateLabelRes['ERROR'] = $errors;
        } else {
            $shipRes = ApiFunctions::addShipment($shipData, $userId);
            if(isset($shipRes['STATUS']) && strtolower($shipRes['STATUS'])=="success"){
                $shipmentNumber  = isset($shipRes['CONSIGNMENT_ID']) ? $shipRes['CONSIGNMENT_ID'] : 0;
                $instantLable  = isset($shipRes['INSTANT_LABEL']) ? $shipRes['INSTANT_LABEL'] : '';
                $instantLableBinStr  = isset($shipRes['LABEL_BIN_STR']) ? $shipRes['LABEL_BIN_STR'] : '';
                $trackingNumber  = isset($shipRes['TRACKING_NUMBER']) ? $shipRes['TRACKING_NUMBER'] : '';
                $orderReference  = isset($shipRes['ORDER_REFERENCE']) ? $shipRes['ORDER_REFERENCE'] : '';
                $labelDataRes = null;
                //if(empty($instantLable) || empty($trackingNumber)){
                    $labelDataRes = ApiFunctions::getLabel($orderReference, $shipData["label_type"], $shipData["label_size"]);
                /*}else{
                    $generateLabelRes['LABEL'] = $instantLable;
                    $generateLabelRes['LABEL_BIN_STR'] = $instantLableBinStr;
                    $generateLabelRes['TRACKING_NUMBER'] = $trackingNumber;
                }*/
                if(isset($labelDataRes['STATUS']) && strtolower($labelDataRes['STATUS'])=="success"){
                    $generateLabelRes = $labelDataRes;
                }else{
                    $generateLabelRes['STATUS']  = isset($labelDataRes['STATUS']) ? $labelDataRes['STATUS'] : 'ERROR';
                    $generateLabelRes['MESSAGE'] = isset($labelDataRes['MESSAGE']) ? $labelDataRes['MESSAGE'] : "Oops! Something went wrong and we couldn't process your request." ;
                    $generateLabelRes['ERROR'] = isset($labelDataRes['ERROR']) ? $labelDataRes['ERROR'] : ["Oops! Something went wrong and we couldn't process your request."];
                }
                $generateLabelRes['CONSIGNMENT_ID'] = $shipmentNumber;
            }else{
                $generateLabelRes['STATUS'] = $shipRes['STATUS'];
                $generateLabelRes['MESSAGE'] = $shipRes['MESSAGE'];
                $generateLabelRes['ERROR'] = $shipRes['ERROR'];
            }
        }
//        $this->respond( new GenerateLabelTransformer($request, $response) );
//        $transformer = new GenerateLabelTransformer($this->renderer, $request, $response);
//        $response = $transformer->transformCollection($generateLabelRes);
        /*
         * Log System
         */
        $generateLabelResTemp = $generateLabelRes;
        unset($generateLabelResTemp['LABEL_BIN_STR']);
        $saveLog['response'] = $generateLabelResTemp;
        $allFileData = json_encode($saveLog);

        $current = file_get_contents($fileFullName);
        $current .= $allFileData." \n";
        file_put_contents($fileFullName, $current);
        /*
         * End Log System
         */
        $tarnsform = new GenerateLabelTransformer($request, $response);
        return $tarnsform->transform($generateLabelRes);
    }
    /**
     * Validate data to be applied to this entity
     *
     * @param  array $data
     * @return array
     */
    public function validate($data, $elements = [])
    {
        /*$inputFilter = $this->createInputFilter($elements);
        $inputFilter->setData($data);

        if ($inputFilter->isValid()) {
            return $inputFilter->getValues();
        }

        $problem = new ApiProblem(
            'Validation failed',
            'about:blank',
            400
        );
        $problem['errors'] = $inputFilter->getMessages();

        throw new ProblemException($problem);*/
    }
    protected function createInputFilter($elements = [])
    {
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
                        'options'=>[
                            'pattern'=>'/(^\d*\.?\d*[1-9]+\d*$)|(^[1-9]+\d*\.\d*$)/',
                            'messages' => array('regexNotMatch' =>"Weight must be greater than 0")
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
                $specification,
                function ($key) use ($elements) {
                    return in_array($key, $elements);
                },
                ARRAY_FILTER_USE_KEY
            );
        }

        $factory = new InputFilterFactory();
        $inputFilter = $factory->createInputFilter($specification);

        return $inputFilter;
    }
}
