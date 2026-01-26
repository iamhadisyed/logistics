<?php
 
class Ebay extends DbAccess3 
{
    protected $devID;
    protected $appID;
    protected $certID;
    protected $clientID;
    protected $serverUrl;
    public $userToken;
    protected $paypalEmailAddress;
    protected $ruName;
 
    public function __construct($userMarketPlaceMappingObj='',$plateformData= '')
    {
        if(is_object($userMarketPlaceMappingObj))
        {
            $_SESSION['userMarketPlaceMappingObj'] = $userMarketPlaceMappingObj;
            $authData = json_decode($_SESSION['userMarketPlaceMappingObj']->getAuthData());
            $this->devID = $authData->devID; // these prod keys are different from sand'box keys
            $this->appID = $authData->appID;; // Client Id
            $this->certID = $authData->certID; // Client Secret
        }
        else
        {
            $decodedArray = json_decode($plateformData);
            $this->devID = $decodedArray->DEVELOPER_ID; // these prod keys are different from sandbox keys
            $this->appID = $decodedArray->App_ID; // Client Id
            $this->certID = $decodedArray->CERT_ID; // Client Secret          
        }
        $this->serverUrl = 'https://api.ebay.com/ws/api.dll';      // server URL different for prod and sandbox
 
        $this->authCode = '';
        $this->authToken ="";
        $this->refreshToken ="";
        $this->ruName= "Kiran_Iftikhar-KiranIft-Onewor-ewlwpt";
    }
    
    public function getRedirection() 
    {
        header("Location: https://auth.ebay.com/oauth2/authorize?client_id=KiranIft-Oneworld-PRD-8ef64c93a-37c48f64&response_type=code&redirect_uri=Kiran_Iftikhar-KiranIft-Onewor-ewlwpt&scope=https://api.ebay.com/oauth/api_scope https://api.ebay.com/oauth/api_scope/sell.marketing.readonly https://api.ebay.com/oauth/api_scope/sell.marketing https://api.ebay.com/oauth/api_scope/sell.inventory.readonly https://api.ebay.com/oauth/api_scope/sell.inventory https://api.ebay.com/oauth/api_scope/sell.account.readonly https://api.ebay.com/oauth/api_scope/sell.account https://api.ebay.com/oauth/api_scope/sell.fulfillment.readonly https://api.ebay.com/oauth/api_scope/sell.fulfillment https://api.ebay.com/oauth/api_scope/sell.analytics.readonly https://api.ebay.com/oauth/api_scope/sell.finances https://api.ebay.com/oauth/api_scope/sell.payment.dispute https://api.ebay.com/oauth/api_scope/commerce.identity.readonly https://api.ebay.com/oauth/api_scope/commerce.identity.email.readonly https://api.ebay.com/oauth/api_scope/commerce.identity.phone.readonly https://api.ebay.com/oauth/api_scope/commerce.identity.address.readonly&prompt=login");
        exit();
        //return $redirection;//exit();
    }
 
    public function authorizationToken($codeAuth)
    {
        $link = "https://api.ebay.com/identity/v1/oauth2/token";
        $ch = curl_init($link);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/x-www-form-urlencoded',
            'Authorization: Basic '.base64_encode($this->appID.':'.$this->certID),
            'X-EBAY-C-MARKETPLACE-ID: EBAY_GB'
        ));
        curl_setopt($ch, CURLHEADER_SEPARATE, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "grant_type=authorization_code&code=".$codeAuth."&redirect_uri=".$this->ruName);
        $response = curl_exec($ch);
        
        $json = json_decode($response, true);
        
        $info = curl_getinfo($ch);
        curl_close($ch);
        if($json != null)
        {
            $this->authToken = $json["access_token"];
            $this->refreshToken = $json["refresh_token"];
            
            $userMarketPlaceMappingFilter = new userMarketPlacesMappingFilter();
            $userMarketPlaceMappingFilter->addFieldFilter('user_account_id', '4778'); // get this id from session  
            $userMarketPlaceMappingFilter->addFieldFilter('market_places_id', '2'); // again can we hardcode it ?
            $userArray = $userMarketPlaceMappingFilter->getList();
            
            if(count($userArray) > 0 )
            {
                $table_id = $userArray[0]->getId();
                $userMarketPlaceMapping = new userMarketPlacesMapping($table_id);                
            }
            else
            {
                $userMarketPlaceMapping = new userMarketPlacesMapping();
                $userMarketPlaceMapping->setMarketPlacesId('2');
                $userMarketPlaceMapping->setUserAccountId('4778');
                $userMarketPlaceMapping->setActive('1');            
            }
            $userTokenArray['ebayAuthToken'] =  $this->authToken;
            $userTokenArray['ebayRefreshToken'] = $this->refreshToken;
            $userTokenArray['ebayLastTokenTime'] = strtotime(date('Y-m-d H:i:s'));
            $userTokenArrayJson = json_encode($userTokenArray);
            $userMarketPlaceMapping->setUserToken($userTokenArrayJson);
            $userMarketPlaceMapping->save();
        }        
    }
 
    public function refreshToken($refreshToken)
    {
        $this->refreshToken = $refreshToken;
        $link = "https://api.ebay.com/identity/v1/oauth2/token";
        $ch = curl_init($link);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/x-www-form-urlencoded',
            'Authorization: Basic '.base64_encode($this->appID.':'.$this->certID),
            'X-EBAY-C-MARKETPLACE-ID: EBAY_GB'
        ));
        
        curl_setopt($ch, CURLHEADER_SEPARATE, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "grant_type=refresh_token&refresh_token=".$this->refreshToken."&scope=https://api.ebay.com/oauth/api_scope https://api.ebay.com/oauth/api_scope/sell.marketing.readonly https://api.ebay.com/oauth/api_scope/sell.marketing https://api.ebay.com/oauth/api_scope/sell.inventory.readonly https://api.ebay.com/oauth/api_scope/sell.inventory https://api.ebay.com/oauth/api_scope/sell.account.readonly https://api.ebay.com/oauth/api_scope/sell.account https://api.ebay.com/oauth/api_scope/sell.fulfillment.readonly https://api.ebay.com/oauth/api_scope/sell.fulfillment https://api.ebay.com/oauth/api_scope/sell.analytics.readonly");
        $response = curl_exec($ch);
        $json = json_decode($response, true);
        $info = curl_getinfo($ch);
        curl_close($ch);
        
        if($json != null)
        {
            $this->authToken = $json["access_token"];
            $userMarketPlaceMappingFilter = new userMarketPlacesMappingFilter();
            $userMarketPlaceMappingFilter->addFieldFilter('user_account_id', '4778'); // get this id from session  
            $userMarketPlaceMappingFilter->addFieldFilter('market_places_id', '2');
            $userArray = $userMarketPlaceMappingFilter->getList();
            
            if(count($userArray) > 0 )
            {
                $table_id = $userArray[0]->getId();
                $userMarketPlaceMapping = new userMarketPlacesMapping($table_id);
                $userTokenArray['ebayAuthToken'] =  $this->authToken;
                $userTokenArray['ebayRefreshToken'] = $this->refreshToken;
                $userTokenArray['ebayLastTokenTime'] = strtotime(date('Y-m-d H:i:s'));
                $userTokenArrayJson = json_encode($userTokenArray);
                $userMarketPlaceMapping->setUserToken($userTokenArrayJson);
                $userMarketPlaceMapping->save();
            }
           // $this->getOrders($this->authToken);
        }
        return $this->authToken;
    }
    
    public function getIdentity()
    {        
        $user = SessionManager::getUser();
        $_SESSION['userId'] = $user->getUserAccountId();
        $userToken = $_SESSION['userMarketPlaceMappingObj']->getUserToken();
       
        if($userToken != '')
        {
            $userTokenDecoded = json_decode($userToken);
            $userRefreshTokenExpiry = '47304000';
            $userAccessTokenExpiry = '7200';
            $refreshToken = $userTokenDecoded->ebayRefreshToken;
            $lastDateTime = $userTokenDecoded->ebayLastTokenTime;
            $currentDateTime = date('Y-m-d H:i:s');
            $diff = strtotime($currentDateTime) - $lastDateTime;

            if($diff > $userAccessTokenExpiry) // Token Expired
            {        
                if($diff < $userRefreshTokenExpiry)
                {
                    $authToken = $this->refreshToken($refreshToken);
                }                
            }
            else
            {
                $authToken = $userTokenDecoded->ebayAuthToken; 
            }
        }
        //echo $authToken; die;
        if($authToken != '')
        {
            $headers = [
            'Content-Type: application/json',
            'Authorization: Bearer '.$authToken,
            'X-EBAY-C-MARKETPLACE-ID: EBAY_GB'
           ];
            
            $curl = curl_init('https://apiz.ebay.com/commerce/identity/v1/user/');
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
            $response = curl_exec($curl);
            curl_close($curl);
           
            $array = json_decode($response);
            echo '<pre>';
            print_r($array);
            echo '</pre>';
            die;
        }     
    }
    
    public function fetchOrders()
    {
        $user = SessionManager::getUser();
        $_SESSION['userId'] = $user->getUserAccountId();
        $userToken = $_SESSION['userMarketPlaceMappingObj']->getUserToken();
       
        if($userToken != '')
        {
            $userTokenDecoded = json_decode($userToken);
            $userRefreshTokenExpiry = '47304000';
            $userAccessTokenExpiry = '7200';
            $refreshToken = $userTokenDecoded->ebayRefreshToken;
            $lastDateTime = $userTokenDecoded->ebayLastTokenTime;
            $currentDateTime = date('Y-m-d H:i:s');
            $diff = strtotime($currentDateTime) - $lastDateTime;

            if($diff > $userAccessTokenExpiry) // Token Expired
            {        
                if($diff < $userRefreshTokenExpiry)
                {
                    $authToken = $this->refreshToken($refreshToken);
                }                
            }
            else
            {
                $authToken = $userTokenDecoded->ebayAuthToken; 
            }
        }
        //$userTokenArrayDecoded = json_decode($userTokenArray);
        //$authToken = $userTokenArrayDecoded->ebayAuthToken; 
        //echo $authToken.'sdas'; die;
        $headers = [
         'Content-Type: application/json',
         'Authorization: Bearer '.$authToken,
         'X-EBAY-C-MARKETPLACE-ID: EBAY_GB'
        ];
        $curl = curl_init();
        curl_setopt_array($curl, [
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_HTTPHEADER => $headers,
        CURLOPT_URL => 'https://api.ebay.com/sell/fulfillment/v1/order?filter=creationdate:%5B2021-04-01T15:05:43.026Z..%5D&limit=50&offset=0'
        ]);
        // Send the request & save response to $resp
        $resp = curl_exec($curl);
        curl_close($curl);
        $array = json_decode($resp);
        
        $ordersArray = $array->orders;
        //print_r($ordersArray).'sdsd'; die;
        foreach($ordersArray as $orderArray)
        {
            $hawb = $orderArray->orderId;
            $creationDate = date('Y-m-d H:i:s',strtotime($orderArray->creationDate));
            $totalOrderPrice = $orderArray->pricingSummary->total->value;
            //echo $totalOrderPrice; die;
            $marketPlaceOrderFilter = new marketPlaceOrderFilter();
            $marketPlaceOrderFilter->addFieldFilter('    marketplace_order_number', $hawb);
            $rs = $marketPlaceOrderFilter->getColumnList('id');

            if(count($rs) >= 1)
            {
                continue;
            }
            else
            {
                $shipAddressArray = $orderArray->fulfillmentStartInstructions;   
                foreach($shipAddressArray as $shippingAddress)
                {
                    $shipToArray = $shippingAddress->shippingStep->shipTo;
                    
                    $marketPlaceOrder = new MarketPlaceOrder();
                    $marketPlaceOrder->setMarketPlaceOrderNumber($hawb);
                    $marketPlaceOrder->setUserId($_SESSION['userId']);
                    $marketPlaceOrder->setMarketPlaceId($_POST["marketPlace_id"]);
                    $marketPlaceOrder->setCreateTime($creationDate);
                    $marketPlaceOrder->setOrderStatus('Unshipped');
                    $marketPlaceOrder->setOrderTotal($totalOrderPrice);
                    
                    $receiverName = $shipToArray->fullName;
                    $marketPlaceOrder->setReceiverName($receiverName);
                    
                    $receiverAddress = $shipToArray->contactAddress;
                    $addressLine1 = $receiverAddress->addressLine1;
                    $marketPlaceOrder->setReceiverAddressLine1($addressLine1);
                    
                    $addressLine2 = $receiverAddress->addressLine2;
                    $marketPlaceOrder->setReceiverAddressLine2($addressLine2);
                    
                    $city = $receiverAddress->city;
                    $marketPlaceOrder->setReceiverCity($city);
                    
                    $stateOrProvince = $receiverAddress->stateOrProvince;
                    $marketPlaceOrder->setReceiverState($stateOrProvince);
                    
                    $postalCode = $receiverAddress->postalCode;
                    $marketPlaceOrder->setReceiverPostCode($postalCode);
                    
                    $countryCode = $receiverAddress->countryCode;   
                    $countryFilter = new CountryFilter();
                    $countryFilter->addFieldFilter("iso", trim($countryCode));
                    $countryId = $countryFilter->getColumnList("id");
                    $marketPlaceOrder->setReceiverCountryId($countryId[0]->getId());
                        
                    $phoneNumber = $shipToArray->primaryPhone->phoneNumber;
                    $marketPlaceOrder->setReceiverPhone($phoneNumber);
                    
                    $email = $shipToArray->email;
                    $marketPlaceOrder->setReceiverEmail($email);
                    
                    $shippingCarrierCode = $shippingAddress->shippingStep->shippingCarrierCode;
                    $marketPlaceOrder->save();
                }                
                
                $lineItemsArray = $orderArray->lineItems;
                foreach($lineItemsArray as $lineItem)
                {
                    $marketPlaceOrderId = $marketPlaceOrder->getId();
                    $marketPlaceOrderDetails = new MarketPlaceOrderDetails();
                    $marketPlaceOrderDetails->setMarketPlaceOrderId($marketPlaceOrderId);
                    
                    $marketplaceitemId = $lineItem->lineItemId;
                    $marketPlaceOrderDetails->setMarketPlaceItemId($marketplaceitemId);
                    
                    $title = $lineItem->title;
                    $marketPlaceOrderDetails->setTitle($title);
                    
                    $sku = $lineItem->legacyItemId;
                    $marketPlaceOrderDetails->setSku($sku);
                    
                    $itemCost = $lineItem->lineItemCost;
                    $itemValue = $itemCost->value;
                    $marketPlaceOrderDetails->setItemPrice($itemValue);
                    
                    $itemCurrency = $itemCost->currency;                
                    $marketPlaceOrderDetails->setCurrency($itemCurrency);
                    
                    $quantity = $lineItem->quantity;
                    $marketPlaceOrderDetails->setQuantityPurchased($quantity);
                    $marketPlaceOrderDetails->save();
                }
            }
        } 
        $output["STATUS"] = "SUCCESS";
        $output["MESSAGE"] = "All Orders has been fetched successfully.";
        return $output;
    }
    
    function dispatchLabel($hawb, $requestType = "normal")
    {
        
        $user = SessionManager::getUser();
        $_SESSION['userId'] = $user->getUserAccountId();
        $userToken = $_SESSION['userMarketPlaceMappingObj']->getUserToken();
        
        $consignmentFilter = new ConsignmentFilter();
        $consignmentFilter->addFieldFilter('    hawb', $hawb);
        $result = $consignmentFilter->getColumnList('*');

        if(count($result) >0 )
        {
            $trackingNumber = $result[0]->getAwb();
            $service = new Services($result[0]->getServiceId());
            $serviceName = $service->getName();
            
            $marketplaceOrderFilter = new MarketPlaceOrderFilter();
            $marketplaceOrderFilter->addFieldFilter('    marketplace_order_number',$hawb);
            $res = $marketplaceOrderFilter->getColumnList('*');

            $marketplaceOrderDetailsFilter = new MarketPlaceOrderDetailsFilter();
            $marketplaceOrderDetailsFilter->addFieldFilter('    marketplace_order_id',$res[0]->getId());
            $resdetails = $marketplaceOrderDetailsFilter->getColumnList('*');
            
            if(count($resdetails) >0)
            {
                $quantity = $resdetails[0]->getQuantityPurchased();
                $lineItemId = $resdetails[0]->getMarketPlaceItemId();
            }            
        }
        
      
        if($userToken != '')
        {
            $userTokenDecoded = json_decode($userToken);
            $userRefreshTokenExpiry = '47304000';
            $userAccessTokenExpiry = '7200';
            $refreshToken = $userTokenDecoded->ebayRefreshToken;
            $lastDateTime = $userTokenDecoded->ebayLastTokenTime;
            $currentDateTime = date('Y-m-d H:i:s');
            $diff = strtotime($currentDateTime) - $lastDateTime;

            if($diff > $userAccessTokenExpiry) // Token Expired
            {        
                if($diff < $userRefreshTokenExpiry)
                {
                    $authToken = $this->refreshToken($refreshToken);
                }                
            }
            else
            {
                $authToken = $userTokenDecoded->ebayAuthToken; 
            }
        }
        $params = [
                    "lineItems" => [["lineItemId" => $lineItemId, "quantity" => $quantity]],
                    "shippingCarrierCode" => $serviceName,
                    "trackingNumber" => $trackingNumber            
           ];
        $headers = [
         'Content-Type: application/json',
         'Authorization: Bearer '.$authToken,
         'X-EBAY-C-MARKETPLACE-ID: EBAY_GB'
        ];
        
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_URL, 'https://api.ebay.com/sell/fulfillment/v1/order/'.$hawb.'/shipping_fulfillment');
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_ENCODING , '');
        $result = curl_exec($ch);

        curl_close($curl);
        $array = json_decode($resp);  
        if($array == '')
        {
            $marketPlaceObj = new MarketPlaceOrder($res[0]->getId());
            $marketPlaceObj->setOrderStatus('Shipped');
            $marketPlaceObj->setShippedDate(date('Y-m-d H:i:s')); // need to change this with shipped_date column
            $marketPlaceObj->save();
            
            $output["STATUS"] = "SUCCESS";
            $output["MESSAGE"] = "Shipment has been dispatched successfully.";
            return $output;
        }
    }
}
