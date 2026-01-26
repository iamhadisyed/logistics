<?php
namespace Smarttrack\V2\Action;

use Doctrine\DBAL\Driver\IBMDB2\DB2Driver;
use Error\ApiProblem;
use Error\Exception\ProblemException;
use Monolog\Logger;
use RKA\ContentTypeRenderer\ApiProblemRenderer;
use RKA\ContentTypeRenderer\HalRenderer;
use Slim\App;
use Smarttrack\V2\ApiFunctions;
use SmarttrackTransformer\V2\JDGenerateLabelTransformer;
use Zend\InputFilter\Factory as InputFilterFactory;
use Smarttrack\V2\SmartAction;

class JDGenerateLabelAction extends SmartAction
{
    protected $logger;
    protected $renderer;
    protected $authorMapper;
    protected $userServiceRoutingMapper;

    public function __invoke($request, $response)
    {
        $generateLabelRes=[];
        // Validate User Here
        $jdTracking = 'HDZDbvg3aQ8tpHdK';
        $shipDataReq = $request->getParams();
        $tokenCode = $shipDataReq['encrypt_data'];
        $timeStamp = $shipDataReq['timestamp'];
        $logisticsInfo = urldecode($shipDataReq['logistics_info']);
        $md5String = $logisticsInfo.$timeStamp.$jdTracking;
        $base64String = urlencode(base64_encode(md5($md5String)));
        $tokenCheck = ApiFunctions::JdAccessToken($jdTracking);
        if ($base64String != $tokenCode || $tokenCheck['STATUS'] == "ERROR") {
            $errorsArray['STATUS'] = "ERROR";
            $errorsArray['MESSAGE'] = "Access token not found";
            $errorsArray['ERROR'][] = "Access token not found";
            $tarnsform = new JDGenerateLabelTransformer($request, $response);
            return $tarnsform->transform($errorsArray);
            die;
        }
        $userId = $tokenCheck['USER_ID'];
        $shipDataReq = json_decode($logisticsInfo,true);
        $invalidParams = ApiFunctions::validateJDShipmentParams($shipDataReq);
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

            $tarnsform = new JDGenerateLabelTransformer($request, $response);
            return $tarnsform->transform($labelResponse);
        }
        $parcelArray = [];
        $description = "";
        foreach ($shipDataReq['packageInfos'] as $packageInfo) {
            $parcelArray[] = [
                                "weight"=>number_format((float)$packageInfo['weight'], 2, '.', ''),
                                "length"=>number_format((float)$packageInfo['length'], 2, '.', ''),
                                "width"=>number_format((float)$packageInfo['width'], 2, '.', ''),
                                "height"=>number_format((float)$packageInfo['height'], 2, '.', '')
                            ];
            if(isset($packageInfo['goods']) && count($packageInfo['goods']) > 0){
                foreach($packageInfo['goods'] as $goods){
                    if($description == ""){
                        $description = $goods['goodsNameEn'];
                    }
                }
            }
        }
        $shipData = [
            "sender_country_iso"=>$shipDataReq['sender']['country'],
            "service_code"=>$shipDataReq["orderDetail"]["serviceCode"],
            "order_reference"=>$shipDataReq["orderId"],
            "reference"=>$shipDataReq["waybillCode"],
            "sender_contact"=>$shipDataReq['sender']['name'],
            "sender_address_line_1"=>$shipDataReq['sender']['address'],
            "sender_address_line_2"=>"",
            "sender_address_line_3"=>"",
            "sender_city"=>$shipDataReq['sender']['city'],
            "sender_postcode"=>$shipDataReq['sender']['zipCode'],
            "receiver_country_iso"=>$shipDataReq['receiver']['country'],
            "receiver_contact"=>$shipDataReq['receiver']['name'],
            "receiver_address_line_1"=>$shipDataReq['receiver']['address'],
            "receiver_city"=>$shipDataReq['receiver']['city'],
            "receiver_postcode"=>$shipDataReq['receiver']['zipCode'],
            "description"=>$description,//$shipDataReq["remark"],
            "value"=>$shipDataReq['orderDetail']["goodsValue"],
            "currency"=>$shipDataReq['orderDetail']["currency"],
            "itemtype"=>"Packets",
            "notes"=>$shipDataReq["remark"],
            "tracking_number"=>"",
            "shipment_type"=>"D",
            "eori_number"=>"",
            "vat_number"=>"",
            "ioss_number"=>"",
            "parcel"=>$parcelArray,
            "label_type"=>$shipDataReq["labelSpecification"]['imageType'],
            "label_size"=>$shipDataReq["labelSpecification"]['labelSize']
        ];

    
        $parcelsTotalWeight = 0;
        if(isset($parcelArray) && count($parcelArray) > 0) {
            foreach ($parcelArray as $parcelArr) {
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
                $shipmentNumber  = isset($shipRes['ORDER_REFERENCE']) ? $shipRes['ORDER_REFERENCE'] : '';//isset($shipRes['CONSIGNMENT_ID']) ? $shipRes['CONSIGNMENT_ID'] : 0;
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
//        $this->respond( new JDGenerateLabelTransformer($request, $response) );
//        $transformer = new JDGenerateLabelTransformer($this->renderer, $request, $response);
//        $response = $transformer->transformCollection($generateLabelRes);
        $tarnsform = new JDGenerateLabelTransformer($request, $response);
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
