<?php

namespace Smarttrack\V2\Action;

use Error\ApiProblem;
use Error\Exception\ProblemException;
use Monolog\Logger;
use RKA\ContentTypeRenderer\ApiProblemRenderer;
use RKA\ContentTypeRenderer\HalRenderer;
use Smarttrack\V2\ApiFunctions;
use SmarttrackTransformer\V2\CreateInboundSkuBagOrderTransformer;
use Zend\InputFilter\Factory as InputFilterFactory;
use Smarttrack\V2\SmartAction;
use LicencePlate;
use Wms;
use SkuBoxDetailFilter;


class CreateInboundSkuBagOrderAction extends SmartAction {

    protected $logger;
    protected $renderer;

    public function __construct(Logger $logger, HalRenderer $renderer) {
        $this->logger = $logger;
        $this->renderer = $renderer;
    }

    public function __invoke($request, $response) {
        $userId = $request->getAttribute('api_user_id');
        $this->logger->info("Create Inbound Bag Order For User ", ['api_user_id' => $userId]);
        $userData = ApiFunctions::getUserById($userId);
        $skuResponse = [];
        // Show error here
        if ($userData->getId() < 1) {
            $errors = ['User does not exist.'];
            $skuResponse['STATUS'] = "ERROR";
            $skuResponse['ERROR'] = $errors;
            $skuResponse['MESSAGE'] = "Please fix below error(s)";

            $tarnsform = new CreateInboundSkuBagOrderTransformer($request, $response);
            return $tarnsform->transform($skuResponse);
        }

        $paramData = $request->getParams();
        
        //validating sku data
        $invalidParams = ApiFunctions::validateInboundSkuBagOrderParams($paramData);

        if(count($invalidParams) > 0){
            $errors = $invalidParams;
            $skuResponse['STATUS'] = "ERROR";
            $skuResponse['ERROR'][] = $errors;
            $skuResponse['MESSAGE'] = "Please fix below error(s)";

            $tarnsform = new CreateInboundSkuBagOrderTransformer($request, $response);
            return $tarnsform->transform($skuResponse);
        }

        $packageInfos = $paramData['packageInfos'];


        $invalidParams = self::CheckUniqueBagNumber($packageInfos,$userData);

        if(count($invalidParams) > 0){
            $errors = $invalidParams;
            $skuResponse['STATUS'] = "ERROR";
            $skuResponse['ERROR'][] = $errors;
            $skuResponse['MESSAGE'] = "Please fix below error(s)";

            $tarnsform = new CreateInboundSkuBagOrderTransformer($request, $response);
            return $tarnsform->transform($skuResponse);
        }


        $invalidParams = self::ValidateSku($packageInfos,$userData);

        if(count($invalidParams) > 0){
            $errors = $invalidParams;
            $skuResponse['STATUS'] = "ERROR";
            $skuResponse['ERROR'][] = $errors;
            $skuResponse['MESSAGE'] = "Please fix below error(s)";

            $tarnsform = new CreateInboundSkuBagOrderTransformer($request, $response);
            return $tarnsform->transform($skuResponse);
        }

        $invalidParams = self::ValidateBagNumbers($packageInfos);

        if(count($invalidParams) > 0){
            $errors = $invalidParams;
            $skuResponse['STATUS'] = "ERROR";
            $skuResponse['ERROR'][] = $errors;
            $skuResponse['MESSAGE'] = "Please fix below error(s)";

            $tarnsform = new CreateInboundSkuBagOrderTransformer($request, $response);
            return $tarnsform->transform($skuResponse);
        }

        $CreateInboundBagOrderResp = self::CreateInboundBagOrder($paramData,$userData);

        $tarnsform = new CreateInboundSkuBagOrderTransformer($request, $response);

        return $tarnsform->transform($CreateInboundBagOrderResp);    
    }

    public static function CreateInboundBagOrder($param, $userObj)
    {
        $userId = $userObj->getId();

        $warehouse = $param['warehouse'];
        $packageInfos = $param['packageInfos'];

        $WMSLicencePlate = LicencePlate::getLicencePlateNumber(202);
        if (trim($WMSLicencePlate['STATUS']) == 'ERROR')
        {
            $response['STATUS'] = "ERROR"; 
            $response['MESSAGE'] = "Sku Inbound Bag reference number error " .  $WMSLicencePlate;
            return $response;
        }
        else 
        {
            $trackingNumber = $WMSLicencePlate["RANGE"];
            $WMSbarcode = $WMSLicencePlate["PREFIX"] . $trackingNumber . $WMSLicencePlate["SUFIX"];
            $WMSLicencePlate = $WMSbarcode;
        }

        $skuOrder = new \SkuOrder();
        $skuOrder->setShipmentReference($WMSLicencePlate);
        $skuOrder->setDateCreated(time());
        $skuOrder->setUserId($userObj->getId());
        $skuOrder->save();

        $skuOrderId = $skuOrder->getId();

        foreach($packageInfos as $package)
        {
            $totalQty = 0;
            $skuListArray = $package['skuList'];
            //$quantity = $package['quantity'];
            $bagNumber = $package['bag_number'];
            $bagWeight = $package['weight'];
            $bagLength = $package['length'];
            $bagWidth = $package['width'];
            $bagHeight = $package['height'];

            $skuBoxDetail = new \SkuBoxDetail();
            $skuBoxDetail->setBagNumber($bagNumber);
            $skuBoxDetail->setSkuOrderId($skuOrderId);
            $skuBoxDetail->setNumberPieces($totalQty);
            $skuBoxDetail->setBoxWeight($bagWeight);
            $skuBoxDetail->setBoxLength($bagLength);
            $skuBoxDetail->setBoxWidth($bagWidth);
            $skuBoxDetail->setBoxHeight($bagHeight);
            $skuBoxDetail->setUserId($userId);
            $skuBoxDetail->setDateCreated(time());
            $skuBoxDetail->save();
            $skuBoxDetailId = $skuBoxDetail->getId();


            if(count($skuListArray) > 0)
            {
                foreach($skuListArray as $skuArray)
                {
                    $sku = $skuArray['sku'];
                    $quantity = $skuArray['quantity'];

                    $totalQty += $quantity;

                    $where = array('sku' => $sku);

                    $skuFilter = new \SkuFilter();
                    $skuFilter->where($where);
                    $skuList = $skuFilter->getList();

                    
                    if(count($skuList) > 0)
                    {
                        $skuObj = $skuList[0];
                        $skuId = $skuObj->getId();
                        $skuOrderMapping = new \SkuOrderMapping();
                        $skuOrderMapping->setSkuId($skuId);
                        $skuOrderMapping->setSkuOrderId($skuOrderId);
                        $skuOrderMapping->setShippedQuantity($quantity);
                        $skuOrderMapping->save();

                        $skuBoxMapping = new \SkuBoxMapping();
                        $skuBoxMapping->setSkuId($skuId);
                        $skuBoxMapping->setSkuQuantity($quantity);
                        $skuBoxMapping->setSkuBoxDetailId($skuBoxDetailId);
                        $skuBoxMapping->save();
                      
                    }
                }
                
            }

            

            

        }

        $wms = new Wms();
        $wms->CreateInboundBags($skuOrder);
        
        $response['STATUS'] = "SUCCESS"; 
        $response['ORDER_REFERENCE'] = $WMSLicencePlate;
        $response['MESSAGE'] = "Sku Inbound Bag(s) created successfully.";
        
        return $response;


    }

    public static function CheckUniqueBagNumber($packageInfos)
    {
        $uniqueBagInboundOrder = array();
        
        foreach($packageInfos as $package)
        {
            $bag_number = trim($package['bag_number']);
            if(!in_array($bag_number, $uniqueBagInboundOrder))
                $uniqueBagInboundOrder[] = $bag_number;
            else
            {
                $error[] = "Same Bag number " . $bag_number . " used in inbound sku order.";
            }
        }

        return $error;

    }

    public static function ValidateBagNumbers($packageInfos)
    {
        $uniqueBagInboundOrder = array();
        
        foreach($packageInfos as $package)
        {
            $bag_number = trim($package['bag_number']);
           
            $where = array('bag_number' => $bag_number);

            $skuBoxDetailFilter = new SkuBoxDetailFilter();
            $skuBoxDetailFilter->where($where);
            $skuBoxDetailList = $skuBoxDetailFilter->getList();

            if(count($skuBoxDetailList) > 0)
            {
                $error[] = $bag_number . " already exists.";                
            }


        }

        return $error;
    }

    public static function ValidateSku($packageInfos)
    {
        $error = array();

        //print_r($packageInfos);

        foreach($packageInfos as $package)
        {
            $skuListArray = $package['skuList'];
            
            $uniqueSkuInBag = array();

            foreach($skuListArray as $skuArray)
            {
                $sku = $skuArray['sku'];
                $where = array('sku' => $sku);
                $skuFilter = new \SkuFilter();
                $skuFilter->where($where);
                $skuList = $skuFilter->getList();
                if(count($skuList) == 0)
                {
                    $error[] = "Sku " . $sku . " does not exist.";
                    //return $error;                
                }
                else
                {
                    $skuObj = $skuList[0];
                    $sku = $skuObj->getSku();
                    if(!in_array($sku, $uniqueSkuInBag))
                        $uniqueSkuInBag[] = $sku;
                    else
                    {
                        $error[] = "Repeated Sku " . $sku . " in bag number " . $package["bag_number"];                
                    }
                }
            }
            
        }

        return $error;
    }

    /**
     * Validate data to be applied to this entity
     *
     * @param  array $data
     * @return array
     */
    

}
