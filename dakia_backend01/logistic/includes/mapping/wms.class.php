<?php

include_classes([
    'skuordermappingfilter.class',
    'skuordermapping.class'
]);

class Wms  {

   private $user;

    public function __construct() {


        
    }


    public function CreateSku($skuInfo) {

        try
        {
            $result = array('status' => false, 'error' => '');

            $length = $skuInfo->getLength();
            $width = $skuInfo->getWidth();
            $height = $skuInfo->getHeight();

            $cube = $length * $width * $height;
            $userId = $skuInfo->getCustomerId();

            
            $user = new User($userId);
            $userAccountID	    =	$user->getUserAccountId();
            $userAccount = new CustomerAccount($userAccountID);

            $userAccount = $userAccount->getUserAccount();

            $client = new SoapClient('http://wms-uk.oneworldexpress.cn/WebService/SkuService.asmx?wsdl', array('trace' => true));
            $request = new stdClass();
            $request->Active = "Y";
            $request->SKU = $skuInfo->getSku();
            $request->CustomerID = $userAccount;
            $request->SKU_Ref1 = $skuInfo->getSku();
            $request->WarehouseCode = 'BM';
            $request->Hazard_Flag = '';
            $request->Active_Flag = '1';
            $request->Description  = $skuInfo->getDescription();
            $request->DeclaredNameEN = $skuInfo->getDeclaredName();
            $request->DeclaredNameCN = '';
            $request->GrossWeight = $skuInfo->getNetWeight();
            $request->NetWeight = $skuInfo->getNetWeight();
            $request->Tare = "1";
            $request->Cube = $cube;
            $request->Price = $skuInfo->getPrice();
            $request->Length = $length;
            $request->Width = $width;
            $request->Height = $height;
        
            $request->Image = $skuInfo->getImage();
            $request->HSCode = $skuInfo->getHSCode();
            $request->FirstOP = '';
            $response = $client->CreateSku( array("request" => $request));

            if(isset($response->CreateSkuResult->Success))
            {
                if($response->CreateSkuResult->Success == 1)
                {
                    $result = array(
                        'status' => 'true',
                        'error'  => ''
                    );
                }
                else
                {
                    $result = array(
                        'status' => "false",
                        'error'  => $response->CreateSkuResult->ErrorMsg
                    ); 
                }
                
                 
            }
            else
            {
                $result = array(
                    'status' => "false",
                    'error'  => "Some internal error has occurred."
                );  
            }
             
        } 
        catch (Exception $e) 
        {
            $result = array(
                'status' => 'false',
                'error'  => $e->getMessage()
            ); 

        }

        //print_r($result);

        return $result;
        
    }

    public function CreateInboundBags($skuOrder) 
    {

        try
        {
            $result = array('status' => false, 'error' => '');

            $processCode = $skuOrder->getShipmentReference();
            $customerCode = "";
            $warehouseCode = "";

            if($skuOrder->getUserId() > 0)
            {
                $userObj = new User($skuOrder->getUserId());
            
                if($userObj->getUserAccountId() > 0)
                {
                    $userAccountId = $userObj->getUserAccountId();
                    $userAccountObj = new CustomerAccount($userAccountId);
                    $customerCode = $userAccountObj->getUserAccount();

                }
            }

            if($skuOrder->getWarehouseId() > 0)
            {
                $warehouseId = $skuOrder->getWarehouseId();
                $warehouse = new Warehouse($warehouseId);
                $warehouseCode = $warehouse->getWarehouseCode();
            }
            else
            {
                $warehouseCode = 'BM';
            }

            
            $where = array('sku_order_id' => $skuOrder->getId());

            $skuBoxDetailFilter = new SkuBoxDetailFilter();
            $skuBoxDetailFilter->where($where);
            $skuBoxDetailList = $skuBoxDetailFilter->getList();

            //print_r($skuBoxDetailList);
            //die;

            if(count($skuBoxDetailList) > 0)
            {
                $count = 0;
                foreach($skuBoxDetailList as $skuBoxDetail)
                {
                    $skuBoxMappingFilter = new SkuBoxMappingFilter();
                    $where = array('sku_box_detail_id' => $skuBoxDetail->getId());
                    $skuBoxMappingFilter->where($where);
                    $skuBoxMappingList = $skuBoxMappingFilter->getList();

                    //print_r($skuBoxMappingList);
                    //die;

                    if(count($skuBoxMappingList) > 0)
                    {
                        foreach($skuBoxMappingList as $skuBoxMappingObj)
                        {
                            $skuId = $skuBoxMappingObj->getSkuId();
                            $sku = new Sku($skuId);

                            $skuBoxDetailId = $skuBoxMappingObj->getSkuBoxDetailId();

                            $skuBoxDetail = new SkuBoxDetail($skuBoxDetailId);

                            $FbaContainerDetailDataClass = new stdClass();
                            $FbaContainerDetailDataClass->FbaId = $skuBoxDetail->getBagNumber();
                            $FbaContainerDetailDataClass->Sku = $sku->getSku();
                            $FbaContainerDetailDataClass->ExpectedQty = $skuBoxMappingObj->getSkuQuantity();
                            $FbaContainerDetailDataClass->Length = 1;
                            $FbaContainerDetailDataClass->Width = 1;
                            $FbaContainerDetailDataClass->Height = 1;
                            $FbaContainerDetailDataClass->Weight = 1;
                            $FbaContainerDetailDataArr['FbaContainerDetailDataModel'][$count++] = $FbaContainerDetailDataClass;
                            //print_r($FbaContainerDetailDataArr);


                        }
                        
                    }                 
                               
                }
            }

            $client = new SoapClient('http://wms-uk.oneworldexpress.cn/WebService/SkuService.asmx?wsdl', array('trace' => true));
            $request = new stdClass();
            $request->ProcessCode = $processCode;
            $request->CustomerCode = $customerCode;
            $request->WarehouseCode = $warehouseCode;
            $request->Weight = 1;
            $request->Length = 1;
            $request->Width = 1;
            $request->Height = 1;
            $request->ExptectedArrivalTime = '2021-03-15T11:42:00';
            $request->Quantity = 1;
            
                
            $request->FbaContainerDetailDataModelList = $FbaContainerDetailDataArr;
            
            $response = $client->CreateFbaCarton( array("request" => $request));

        }
        catch (Exception $e)
        {
            print_r($e->getMessage());
        }

        return $result;
        
        
    }

}
