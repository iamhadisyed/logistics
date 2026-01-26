<?php

namespace Smarttrack\V2\Action;

use Error\ApiProblem;
use Error\Exception\ProblemException;
use Monolog\Logger;
use RKA\ContentTypeRenderer\ApiProblemRenderer;
use RKA\ContentTypeRenderer\HalRenderer;
use Smarttrack\V2\ApiFunctions;
use SmarttrackTransformer\V2\CreateSkuTransformer;
use Zend\InputFilter\Factory as InputFilterFactory;
use Smarttrack\V2\SmartAction;

class CreateSkuAction extends SmartAction {

    protected $logger;
    protected $renderer;

    public function __construct(Logger $logger, HalRenderer $renderer) {
        $this->logger = $logger;
        $this->renderer = $renderer;
    }

    public function __invoke($request, $response) {
        $userId = $request->getAttribute('api_user_id');
        $this->logger->info("Create SKU For User ", ['api_user_id' => $userId]);
        $userData = ApiFunctions::getUserById($userId);
        $skuResponse = [];
        // Show error here
        if ($userData->getId() < 1) {
            $errors = ['User does not exist.'];
            $skuResponse['STATUS'] = "ERROR";
            $skuResponse['ERROR'] = $errors;
            $skuResponse['MESSAGE'] = "Please fix below error(s)";

            $tarnsform = new CreateSkuTransformer($request, $response);
            return $tarnsform->transform($skuResponse);
        }

        $paramData = $request->getParams();

        
        //validating sku data
        $invalidParams = ApiFunctions::validateSkuParams($paramData);
       
        if(count($invalidParams) > 0){
            $errors = $invalidParams;
            $skuResponse['STATUS'] = "ERROR";
            $skuResponse['ERROR'] = $errors;
            $skuResponse['MESSAGE'] = "Please fix below error(s)";

            $tarnsform = new CreateSkuTransformer($request, $response);
            return $tarnsform->transform($skuResponse);
        }

        $createSkuResp = self::createsku($paramData,$userData);

        $tarnsform = new CreateSkuTransformer($request, $response);

        return $tarnsform->transform($createSkuResp);    
    }

    public static function createsku($param, $userObj)
    {
        
        $sku = trim($param['sku']);
        $skuName = trim($param['name']);
        $skuDescription =trim($param['description']);
        $length = $param['length'];
        $width = $param['width'];
        $height = $param['height'];
        $weight = $param['weight'];
        $price = ($param['price'] > 0) ? $param['price'] : 0.00;
        $currency = trim($param['currency']);
        $notes = trim($param['notes']);
        $customerId = $userObj->getId(); 
        
        $where = array("sku" => $sku);
        
        $skuFilter = new \SkuFilter();
        $skuFilter->addFilter($where);
        $skuList = $skuFilter->getList();

        if(count($skuList) > 0)
        {
            $response['STATUS'] = "ERROR";
            $response['MESSAGE'] = "Sku already exists.";
            $response['ERROR'][] = "Sku already exists.";
        }
        else
        {
            $skuObj = new \Sku();
            $skuObj->setSku($sku);
            $skuObj->setDeclaredName($skuName);
            $skuObj->setDescription($skuDescription);
            $skuObj->setLength($length);
            $skuObj->setWidth($width);
            $skuObj->setHeight($height);
            $skuObj->setNetWeight($weight);
            $skuObj->setPrice($price);
            $skuObj->setCurrency($currency);
            $skuObj->setNotes($notes);
            $skuObj->setCustomerId($userObj->getId());
            $skuObj->setDateCreated(strtotime(date('Y-m-d H:i:s')));
            $skuObj->save();

            $wms = new \Wms();
            $result = $wms->CreateSku($skuObj);
           
            if($result['status'] == "false")
            {
                $response['STATUS'] = "ERROR";
                $response['MESSAGE'] = "Please fix below error(s)";
                $response['ERROR'][] = $result["error"];

            }
            else
            {
                $response['STATUS'] = "SUCCESS";
                $response['MESSAGE'] = "Sku $sku created successfully.";
                $response['SKU'] = "Sku $sku created successfully.";
           
            }

        }

        return $response;


    }

    /**
     * Validate data to be applied to this entity
     *
     * @param  array $data
     * @return array
     */
    

}
