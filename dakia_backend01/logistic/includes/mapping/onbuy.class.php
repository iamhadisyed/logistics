<?php
 
class Onbuy extends DbAccess3 
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
            $this->secretKey = $authData->secret_key; // these prod keys are different from sand'box keys
            $this->consumerKey = $authData->consumer_key; // Client Id            
        }
        else
        {
            $decodedArray = json_decode($plateformData);
            $this->secretKey = $decodedArray->secret_key; // these prod keys are different from sandbox keys
            $this->consumerKey = $decodedArray->consumer_key; // Client Id            
        }
        //$this->serverUrl = 'https://api.ebay.com/ws/api.dll';      // server URL different for prod and sandbox
 
        $this->authCode = '';
        $this->authToken ="";
        $this->refreshToken ="";
        //$this->ruName= "Kiran_Iftikhar-KiranIft-Onewor-ewlwpt";
    }
    
    /*public function getRedirection() 
    {
        header("Location: https://auth.ebay.com/oauth2/authorize?client_id=KiranIft-Oneworld-PRD-8ef64c93a-37c48f64&response_type=code&redirect_uri=Kiran_Iftikhar-KiranIft-Onewor-ewlwpt&scope=https://api.ebay.com/oauth/api_scope https://api.ebay.com/oauth/api_scope/sell.marketing.readonly https://api.ebay.com/oauth/api_scope/sell.marketing https://api.ebay.com/oauth/api_scope/sell.inventory.readonly https://api.ebay.com/oauth/api_scope/sell.inventory https://api.ebay.com/oauth/api_scope/sell.account.readonly https://api.ebay.com/oauth/api_scope/sell.account https://api.ebay.com/oauth/api_scope/sell.fulfillment.readonly https://api.ebay.com/oauth/api_scope/sell.fulfillment https://api.ebay.com/oauth/api_scope/sell.analytics.readonly https://api.ebay.com/oauth/api_scope/sell.finances https://api.ebay.com/oauth/api_scope/sell.payment.dispute https://api.ebay.com/oauth/api_scope/commerce.identity.readonly https://api.ebay.com/oauth/api_scope/commerce.identity.email.readonly https://api.ebay.com/oauth/api_scope/commerce.identity.phone.readonly https://api.ebay.com/oauth/api_scope/commerce.identity.address.readonly&prompt=login");
        exit();
        //return $redirection;//exit();
    }*/
 
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
        $token = $this->getToken();
        
        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://api.onbuy.com/v2/orders?site_id=2000&status=open&modified_since=2021-06-01%2008:13:14',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_HTTPHEADER => array(
          'Authorization: '.$token,
          'Content-Type: application/x-www-form-urlencoded',
          'Cookie: PHPSESSID=c1d9a8e080e61dc07cff2550b21d154d'
        ),
      ));

        $response = curl_exec($curl);
        
        curl_close($curl);
        $ordersArray = json_decode($response);
        //$ordersArray = $array->orders;
       
        foreach($ordersArray->results as $orderArray)
        {
            $hawb = $orderArray->order_id;
            $creationDate = date('Y-m-d H:i:s',strtotime($orderArray->date));
            $totalOrderPrice = $orderArray->price_subtotal;
            $orderStatus = $orderArray->status;
        
            $marketPlaceOrderFilter = new marketPlaceOrderFilter();
            $marketPlaceOrderFilter->addFieldFilter('    marketplace_order_number', $hawb);
            $rs = $marketPlaceOrderFilter->getColumnList('id');

            if(count($rs) >= 1)
            {
                continue;
            }
            else
            {
                $marketPlaceOrder = new MarketPlaceOrder();
                $marketPlaceOrder->setMarketPlaceOrderNumber($hawb);
                $marketPlaceOrder->setUserId($_SESSION['userId']);
                $marketPlaceOrder->setMarketPlaceId($_POST["marketPlace_id"]);
                $marketPlaceOrder->setCreateTime($creationDate);
                $marketPlaceOrder->setOrderStatus($orderStatus);
                $marketPlaceOrder->setOrderTotal($totalOrderPrice);
                    
                $receiverName = $orderArray->delivery_address->name;
                $marketPlaceOrder->setReceiverName($receiverName);
                    
                $addressLine1 = $orderArray->delivery_address->line_1;
                $marketPlaceOrder->setReceiverAddressLine1($addressLine1);
                    
                $addressLine2 = $orderArray->delivery_address->line_2;
                $marketPlaceOrder->setReceiverAddressLine2($addressLine2);
                    
                $city = $orderArray->delivery_address->town;
                $marketPlaceOrder->setReceiverCity($city);
                    
                $stateOrProvince = $orderArray->delivery_address->county;
                $marketPlaceOrder->setReceiverState($stateOrProvince);
                    
                $postalCode = $orderArray->delivery_address->postcode;
                $marketPlaceOrder->setReceiverPostCode($postalCode);

                $countryCode = $orderArray->delivery_address->country_code;   
                $countryFilter = new CountryFilter();
                $countryFilter->addFieldFilter("iso", trim($countryCode));
                $countryId = $countryFilter->getColumnList("id");
                $marketPlaceOrder->setReceiverCountryId($countryId[0]->getId());
                        
                $phoneNumber = $orderArray->buyer->phone;
                $marketPlaceOrder->setReceiverPhone($phoneNumber);
                    
                $email = $orderArray->buyer->email;
                $marketPlaceOrder->setReceiverEmail($email);
                    
                //$shippingCarrierCode = $shippingAddress->shippingStep->shippingCarrierCode;
                $marketPlaceOrder->save();
                //}                
                
                $lineItemsArray = $orderArray->products;
                
                foreach($lineItemsArray as $lineItem)
                {
                    $marketPlaceOrderId = $marketPlaceOrder->getId();
                    $marketPlaceOrderDetails = new MarketPlaceOrderDetails();
                    $marketPlaceOrderDetails->setMarketPlaceOrderId($marketPlaceOrderId);
                    
                    $marketplaceitemId = $lineItem->onbuy_internal_reference;
                    $marketPlaceOrderDetails->setMarketPlaceItemId($marketplaceitemId);
                    
                    $title = $lineItem->name;
                    $marketPlaceOrderDetails->setTitle($title);
                    
                    $sku = $lineItem->sku;
                    $marketPlaceOrderDetails->setSku($sku);
                    
                    $itemValue = $lineItem->unit_price;
                    $marketPlaceOrderDetails->setItemPrice($itemValue);
                    
                    $itemCurrency = $orderArray->currency_code;                
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
    
    public function getToken()
    {
        $user = SessionManager::getUser();
        $_SESSION['userId'] = $user->getUserAccountId();
        $userToken = $_SESSION['userMarketPlaceMappingObj']->getUserToken();
        
        if($userToken != '')
        {
            $userTokenDecoded = json_decode($userToken);
            $userAccessTokenExpiry = '900';
            $lastDateTime = $userTokenDecoded->onbuyTokenExpireTime;
            $token = $userTokenDecoded->onbuyAuthToken;
            $currentDateTime = date('Y-m-d H:i:s');
            $diff = strtotime($currentDateTime) - $lastDateTime;

            if($diff > $userAccessTokenExpiry) // Token Expired
            {        
                    $token = $this->callTokenApi();
            }            
        }
        
        if($userToken == '')
        {            
                $token = $this->callTokenApi();
        }
        return $token;
    }
    
    public function callTokenApi()
    {
        $curl = curl_init();
        curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://api.onbuy.com/v2/auth/request-token',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => 'secret_key='.$this->secretKey.'&consumer_key='.$this->consumerKey,
        CURLOPT_HTTPHEADER => array(
          'Content-Type: application/x-www-form-urlencoded',
        ),
        ));
        $res = curl_exec($curl);            
        $result = json_decode($res);

        if($result != null)
        {
            $user = SessionManager::getUser();
            $_SESSION['userId'] = $user->getUserAccountId();

            $expireTime = $result->expires_at;
            $this->authToken = $result->access_token;

            $userMarketPlaceMappingFilter = new userMarketPlacesMappingFilter();
            $userMarketPlaceMappingFilter->addFieldFilter('user_account_id', $_SESSION['userId']); 
            $userMarketPlaceMappingFilter->addFieldFilter('market_places_id', '94');
            $userArray = $userMarketPlaceMappingFilter->getList();

            if(count($userArray) > 0 )
            {
                $table_id = $userArray[0]->getId();
                $userMarketPlaceMapping = new userMarketPlacesMapping($table_id);
                $userTokenArray['onbuyAuthToken'] =  $this->authToken;
                $userTokenArray['onbuyTokenExpireTime'] = $expireTime;
                $userTokenArrayJson = json_encode($userTokenArray);
                $userMarketPlaceMapping->setUserToken($userTokenArrayJson);
                $userMarketPlaceMapping->save();
            }
        }
        return $this->authToken;
    }
    
    function dispatchLabel($hawb, $requestType = "normal")
    {
        $token = $this->getToken();    
        
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
            $success = 0; 
            $error = 0;
            if(count($resdetails) >0)
            {
                foreach($resdetails as $itemdispatch)
                {
                    $quantity = $itemdispatch->getQuantityPurchased();
                    $sku = $itemdispatch->getSku();
                    
                    $curl = curl_init();
                    curl_setopt_array($curl, array(
                    CURLOPT_URL => 'https://api.onbuy.com/v2/orders/dispatch',
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'PUT',
                    CURLOPT_POSTFIELDS =>'{
                    "site_id": 2000,
                    "orders": [{
                    "order_id":"'.$hawb.'",
                    "products": [{
                    "sku": "'.$sku.'",
                    "quantity": '.$quantity.',
                    "tracking": {
                    "supplier_name": "'.$serviceName.'",
                    "number": "'.$trackingNumber.'"                    
                    }
                }]
            }]
        }',
                    CURLOPT_HTTPHEADER => array(
                      'Authorization: '.$token,
                      'Content-Type: application/json'
                    ),
                  ));

                    $response = curl_exec($curl);
                    curl_close($curl);
                    $resultArray = json_decode($response); 
                    
                    if($resultArray->results->$hawb->success == 1)
                    {
                        $success = 1;
                    }
                    else
                    {
                        $error = 1;
                    }
                }
            }            
    
        if($success == '1' && $error == 0)
        {
            $marketPlaceObj = new MarketPlaceOrder($res[0]->getId());
            $marketPlaceObj->setOrderStatus('Shipped');
            $marketPlaceObj->setShippedDate(date('Y-m-d H:i:s')); // need to change this with shipped_date column
            $marketPlaceObj->save();

            $output["STATUS"] = "SUCCESS";
            $output["MESSAGE"] = "Shipment has been dispatched successfully.";
            return $output;
        }        
    }}
}
