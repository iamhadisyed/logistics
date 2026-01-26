<?php


require_once("../includes/settings/config.inc.php");
require_once ('../includes/autoload/MarketplaceWebServiceOrders/Samples/.config.inc.php');
require_once('../includes/autoload/MarketplaceWebServiceOrders/Client.php');
require_once('../includes/autoload/MarketplaceWebServiceOrders/Model/ListOrdersRequest.php');
require_once('../includes/autoload/MarketplaceWebServiceOrders/Model/MarketplaceIdList.php');
require_once('../includes/autoload/MarketplaceWebServiceOrders/Model/ListOrderItemsRequest.php');
require_once('../includes/autoload/MarketplaceWebServiceOrders/Model/ListOrdersByNextTokenRequest.php');
require_once('../includes/autoload/MarketplaceWebServiceOrders/Model/OrderStatusList.php');
require_once('../includes/autoload/MarketplaceWebServiceOrders/Model.php');

/* * **********************************************************************
 * Instantiate Implementation of MarketplaceWebServiceOrders
 * 
 * AWS_ACCESS_KEY_ID and AWS_SECRET_ACCESS_KEY constants 
 * are defined in the .config.inc.php located in the same 
 * directory as this sample
 * ********************************************************************* */
// United States:
//$serviceUrl = "https://mws.amazonservices.com/Orders/2011-01-01";
// United Kingdom
//$serviceUrl = "https://mws.amazonservices.co.uk/Orders/2011-01-01";
$serviceUrl = "https://mws-eu.amazonservices.com/Orders/2013-09-01";
// Germany
//$serviceUrl = "https://mws.amazonservices.es/Orders/2011-01-01";
//echo "<!-- amazon service : " . $serviceUrl . " -->";
//$serviceUrl = "https://mws.amazonservices.de/Orders/2011-01-01";
// France
//$serviceUrl = "https://mws.amazonservices.fr/Orders/2011-01-01";
// Japan
//$serviceUrl = "https://mws.amazonservices.jp/Orders/2011-01-01";
// China
//$serviceUrl = "https://mws.amazonservices.com.cn/Orders/2011-01-01";
// Canada
//$serviceUrl = "https://mws.amazonservices.ca/Orders/2011-01-01";

$user = SessionManager::getUser();
$_SESSION['userId'] = $user->getId();

$userMarketPlaceMappingFilter = new UserMarketPlacesMappingFilter();
$userMarketPlaceMappingFilter->addFieldFilter("user_account_id", $_SESSION['userId']);
$userMarketPlaceMappingFilter->addFieldFilter("market_places_id", $_POST["marketPlace_id"]);
$result = $userMarketPlaceMappingFilter->getList();

if(count($result)> 0)
{
    $authdata = json_decode($result[0]->getAuthData());
    $_SESSION['AWS_ACCESS_KEY_ID'] = $authdata->AWS_ACCESS_KEY_ID;
    $_SESSION['AWS_SECRET_ACCESS_KEY'] = $authdata->AWS_SECRET_ACCESS_KEY;
    $_SESSION['MERCHANT_ID'] = $authdata->MERCHANT_ID;
    $_SESSION['MARKETPLACE_ID'] = $authdata->MARKETPLACE_ID;
}

$config = array (
   'ServiceURL' => $serviceUrl,
   'ProxyHost' => null,
   'ProxyPort' => -1,
   'ProxyUsername' => null,
   'ProxyPassword' => null,
   'MaxErrorRetry' => 3,
 );

$service = new MarketplaceWebServiceOrders_Client(
        $_SESSION['AWS_ACCESS_KEY_ID'],
        $_SESSION['AWS_SECRET_ACCESS_KEY'],
        'oneworld',
        '2',
        $config);

/* * **********************************************************************
 * Uncomment to try out Mock Service that simulates MarketplaceWebServiceOrders
 * responses without calling MarketplaceWebServiceOrders service.
 *
 * Responses are loaded from local XML files. You can tweak XML files to
 * experiment with various outputs during development
 *
 * XML files available under MarketplaceWebServiceOrders/Mock tree
 *
 * ********************************************************************* */
//$service = new MarketplaceWebServiceOrders_Mock();

/* * **********************************************************************
 * Setup request parameters and uncomment invoke to try out 
 * sample for List Orders Action
 * ********************************************************************* */
$request = new MarketplaceWebServiceOrders_Model_ListOrdersRequest();

$request->setSellerId($_SESSION['MERCHANT_ID']);

$createAfter = '';
$createBefore = '';

// getting last sync time 
// Nedd to add limit here. we just need 1 last record
$marketPlaceOrderFilter = new MarketPlaceOrderFilter();
$marketPlaceOrderFilter->addFieldFilter("    marketplace_id", "1");
$marketPlaceOrderFilter->AddOrderById("desc");
$result  =  $marketPlaceOrderFilter->getColumnList("create_time");
//print_r($result); die;
// Already data is in the database
if(count($result)>0)
{
    $last_sync_time = $result[0]->getcreatetime(); 
    $dteStart = new DateTime(date('Y-m-d H:i:s', strtotime('-1 hour')));
    $dteEnd = new DateTime($last_sync_time);
    $dteDiff  = $dteStart->diff($dteEnd); 
   
            if($dteDiff->h >=1  || $dteDiff->m > 59 || $dteDiff->d >= 1)
            {
            $_POST['createdAfter'] = $last_sync_time;
            $_POST['createdBefore'] = date('Y-m-d H:i:s', strtotime('-1 hour'));
            }
            else
            {
            return;
            }
}
// first time entry in database
else
{
    $_POST['createdAfter']  = date('Y-m-d H:i:s', strtotime('-1 day'));
    $_POST['createdBefore'] = date('Y-m-d H:i:s', strtotime('-1 hour'));
}

  
  if (trim($_POST['createdAfter']) != '') {
   // $postDateObj = new DateTime($_POST['createdAfter']);
    $createAfter = gmdate('Y-m-d\TH:i:s\Z', strtotime($_POST['createdAfter'])); 
  }

if (trim($_POST['createdBefore']) != '') {
   // $postDateObj = new DateTime($_POST['createdBefore']);
    $createBefore = gmdate('Y-m-d\TH:i:s\Z', strtotime($_POST['createdBefore']));    
}

$request->setCreatedAfter($createAfter);
if(!empty($createBefore)){
    $request->setCreatedBefore($createBefore);
}

// Set the marketplaces queried in this ListOrdersRequest
$request->setMarketplaceId($_SESSION['MARKETPLACE_ID']); 
invokeListOrders($service, $request);
/**
 * List Orders Action Sample
 * ListOrders can be used to find orders that meet the specified criteria.
 *   
 * @param MarketplaceWebServiceOrders_Interface $service instance of MarketplaceWebServiceOrders_Interface
 * @param mixed $request MarketplaceWebServiceOrders_Model_ListOrders or array of parameters
 */
//$idForeign = array();
//$orderid = array();

function invokeListOrders(MarketplaceWebServiceOrders_Interface $service, $request)
{
   // global $data; 
    //$request_item = new MarketplaceWebServiceOrders_Model_ListOrderItemsRequest();
  // $request->setSellerId($_SESSION['MERCHANT_ID']);
   //echo $_SESSION['MERCHANT_ID']; die;
//print_r($request); die;
    try {
        $response = $service->listOrders($request);
        $xml = simplexml_load_string($response->toXML());        
       
        $json = json_encode($xml);
        $arr = json_decode($json,TRUE); 
        
        $array = $arr['ListOrdersResult']['Orders']['Order'];
        $nextToken = $arr['ListOrdersResult']['NextToken'];
      
        fetchOrders($service,$array);  
       
        if($nextToken != '')
        {
           $request = new MarketplaceWebServiceOrders_Model_ListOrdersByNextTokenRequest();
           $request->setSellerId($_SESSION['MERCHANT_ID']);
           $request->setNextToken($nextToken);
           invokeListOrdersByNextToken($service, $request); 
        }
    } 
    catch (MarketplaceWebServiceOrders_Exception $ex) {
           
        if($ex->getStatusCode() == 403)
        {
            $arr = "An invalid AWSAccessKeyId value was used or The signature used does not match the server's calculated signature value.";
        }
        elseif($ex->getStatusCode() == 400)
        {
            $arr = "There was an error reading the input stream or An invalid parameter value was used, or the request size exceeded the maximum accepted size, or the request expired.";
        }
        elseif($ex->getStatusCode() == 401)
        {
            $arr = "Access was denied.";
        }
        elseif($ex->getStatusCode() == 404)
        {
            $arr = "An invalid API section or operation value was used, or an invalid path was used.";
        }
        elseif($ex->getStatusCode() == 500)
        {
            $arr = "There was an internal service failure.";
        }
        elseif($ex->getStatusCode() == 503)
        {
            $arr = "The total number of requests in an hour was exceeded or The frequency of requests was greater than allowed";
        }
        
        $output["STATUS"] = "ERROR";
        $output["MESSAGE"] = $arr;
        echo json_encode($output); 
        die;
    }    
}

function invokeListOrdersByNextToken(MarketplaceWebServiceOrders_Interface $service, $request) 
  {
    
      try {
              $response = $service->listOrdersByNextToken($request);              
              $xml = simplexml_load_string($response->toXML());        
              $json = json_encode($xml);
              $arr = json_decode($json,TRUE);  
            
              $array = $arr['ListOrdersByNextTokenResult']['Orders']['Order'];
              $nextToken = $arr['listOrdersByNextTokenResult']['NextToken'];
            
              fetchOrders($service,$array);  
              if(isset($nextToken))
              {
                $request = new MarketplaceWebServiceOrders_Model_ListOrdersByNextTokenRequest();
                $request->setSellerId($_SESSION['MERCHANT_ID']);
                $request->setNextToken($nextToken);
                invokeListOrdersByNextToken($service, $request); 
              }

     } 
     catch (MarketplaceWebServiceOrders_Exception $ex) 
     {
        
            $output["STATUS"] = "ERROR";
            $output["MESSAGE"] = $ex->getMessage().'Token';
            echo json_encode($output); 
            die;
     }
 }
                        

function fetchOrders(MarketplaceWebServiceOrders_Interface $service, $array)
{
    $orderid = array();
    $idForeign = array();
    foreach ($array as $member) 
    {                    
                    $MarketPlaceOrder = new MarketPlaceOrder();
                    $MarketPlaceOrder->setMarketPlaceId('1');
                    $MarketPlaceOrderDetails = new MarketPlaceOrderDetails();
               
                    if ($member['AmazonOrderId'] != '') 
                    {
                        $MarketPlaceOrder->setmarketPlaceOrderNumber($member['AmazonOrderId']); 
                        $MarketPlaceOrder->setcreatetime(date('Y-m-d H:i:s', strtotime('-1 hour')));                        
                        array_push($orderid, $member['AmazonOrderId']);
                    }
                                   
                    if ($member['OrderStatus'] != '') {
                        $MarketPlaceOrder->setorderstatus($member['OrderStatus']);
                    }

                    if ($member['ShippingAddress'] != '') {
                        $shippingAddress = $member['ShippingAddress'];
                        if ($shippingAddress['Name']) {
                            $MarketPlaceOrder->setreceivername($shippingAddress['Name']);
                        }

                        if ($shippingAddress['AddressLine1']) {
                            $MarketPlaceOrder->setreceiveraddressline1($shippingAddress['AddressLine1']);
                        }

                        if ($shippingAddress['AddressLine2']) {
                            $MarketPlaceOrder->setreceiveraddressline2($shippingAddress['AddressLine2']);
                        }
                        
                        /*
                        if ($shippingAddress->isSetAddressLine3()) {
                            $data[$index]["AddressLine3"] = $shippingAddress->getAddressLine3();
                        }*/

                        if ($shippingAddress['City']) {
                            $MarketPlaceOrder->setreceivercity($shippingAddress['City']);
                        }

                        /*if ($shippingAddress->isSetCounty()) {
                            $data[$index]["County"] = $shippingAddress->getCounty();
                        }

                        if ($shippingAddress->isSetDistrict()) {
                            $data[$index]["District"] = $shippingAddress->getDistrict();
                        }*/

                        if ($shippingAddress['StateOrRegion']) {
                            $MarketPlaceOrder->setreceiverstate($shippingAddress['StateOrRegion']);
                        }

                        if ($shippingAddress['PostalCode']) {
                            $MarketPlaceOrder->setreceiverpostcode($shippingAddress['PostalCode']);
                        }

                        if ($shippingAddress['CountryCode']) 
                        {
                           // $MarketPlaceOrder->setreceivercountrycode($shippingAddress['CountryCode']);
                            $countryFilter = new CountryFilter();
                            $countryFilter->addFieldFilter("iso",$shippingAddress['CountryCode']);
                            $countryIdResult = $countryFilter->getColumnList("id, name");  
                            $conId = $countryIdResult[0]->getId();
                            $MarketPlaceOrder->setReceiverCountryId($conId);
                        }

                        if ($shippingAddress['Phone']) {
                            $MarketPlaceOrder->setreceiverphone($shippingAddress['Phone']);
                        }
                    }
                    if ($member['OrderTotal']) {
                        $orderTotal = $member['OrderTotal'];
                        /*if ($orderTotal->isSetCurrencyCode()) {
                            $data[$index]["CurrencyCode"] = $orderTotal->getCurrencyCode();
                        }*/

                        if ($orderTotal['Amount']) {
                            $MarketPlaceOrder->setordertotal($orderTotal['Amount']);
                        }
                    }
                    //echo $_SESSION['userId']; die;
                     $MarketPlaceOrder->setUserId($_SESSION['userId']);
                     $MarketPlaceOrder->save();
                     
                     $id = $MarketPlaceOrder->getId();
                     array_push($idForeign, $id);
    }
    $index = 0;
    $request_orderdetails = new MarketplaceWebServiceOrders_Model_ListOrderItemsRequest();
    $request_orderdetails->setSellerId($_SESSION['MERCHANT_ID']);
    foreach($orderid as $orderId)
    {
        $request_orderdetails->setAmazonOrderId($orderId);
        invokeListOrderItems($service, $request_orderdetails, $idForeign[$index], $MarketPlaceOrder);            
        $index++;
    }   
}
function invokeListOrderItems(MarketplaceWebServiceOrders_Interface $service, $request, $id, $MarketPlaceOrder) 
{
      try {
              $response = $service->listOrderItems($request);
              $xml = simplexml_load_string($response->toXML());        
              $json = json_encode($xml);
              $response = json_decode($json,TRUE);  
            
              if ($response['ListOrderItemsResult']['OrderItems']['OrderItem'] != '') { 
                  //  print_r($response['ListOrderItemsResult']['OrderItems']['OrderItem']); exit;
                    //$listOrderItemsResult = $response->getListOrderItemsResult();
                    
                   // if ($listOrderItemsResult[OrderItems] != '') //{ 
                        //$orderItems = $listOrderItemsResult->getOrderItems();
                        $memberList = $response['ListOrderItemsResult']['OrderItems'];
                   //     print_r($memberList); exit;
                        foreach ($memberList as $member) {
                      //      print_r($member['OrderItemId']); exit;
                            $MarketPlaceOrderDetails = new MarketPlaceOrderDetails();
                            // set foriegn key in marketplaceorderdetails table
                            $MarketPlaceOrderDetails->setmarketplaceorderid($id);
                            $MarketPlaceOrderDetails->setmarketplaceitemid($member['OrderItemId']);
                            
                            if ($member['ASIN'] != '') 
                            {
                                $MarketPlaceOrderDetails->setasin($member['ASIN']);
                            }
                            if ($member['SellerSKU'] != '') 
                            {
                                $MarketPlaceOrderDetails->setsku($member['SellerSKU']);
                            }
                            if ($member['Title'] != '') 
                            {
                                $MarketPlaceOrderDetails->settitle($member['Title']);
                            }
                            if ($member['QuantityOrdered'] != '' ) 
                            {
                                $MarketPlaceOrderDetails->setquantitypurchased($member['QuantityOrdered']);
                            }
                            
                            if ($member['ItemPrice'] != '') { 
                                $itemPrice = $member['ItemPrice'];
                                
                                if ($itemPrice['CurrencyCode'] != '') 
                                {
                                    $MarketPlaceOrderDetails->setCurrency($itemPrice['CurrencyCode']);
                                }
                                if ($itemPrice['Amount'] != '') 
                                {
                                    $MarketPlaceOrderDetails->setitemprice($itemPrice['Amount']);
                                }
                            } 
                            // print_r($MarketPlaceOrderDetails);
                            
                            $MarketPlaceOrderDetails->save();
                            /*if ($member->isSetShippingPrice()) { 
                                echo("                        ShippingPrice\n");
                                $shippingPrice = $member->getShippingPrice();
                                if ($shippingPrice->isSetCurrencyCode()) 
                                {
                                    echo("                            CurrencyCode\n");
                                    echo("                                " . $shippingPrice->getCurrencyCode() . "\n");
                                }
                                if ($shippingPrice->isSetAmount()) 
                                {
                                    echo("                            Amount\n");
                                    echo("                                " . $shippingPrice->getAmount() . "\n");
                                }
                            } 
                            if ($member->isSetGiftWrapPrice()) { 
                                echo("                        GiftWrapPrice\n");
                                $giftWrapPrice = $member->getGiftWrapPrice();
                                if ($giftWrapPrice->isSetCurrencyCode()) 
                                {
                                    echo("                            CurrencyCode\n");
                                    echo("                                " . $giftWrapPrice->getCurrencyCode() . "\n");
                                }
                                if ($giftWrapPrice->isSetAmount()) 
                                {
                                    echo("                            Amount\n");
                                    echo("                                " . $giftWrapPrice->getAmount() . "\n");
                                }
                            } 
                            if ($member->isSetItemTax()) { 
                                echo("                        ItemTax\n");
                                $itemTax = $member->getItemTax();
                                if ($itemTax->isSetCurrencyCode()) 
                                {
                                    echo("                            CurrencyCode\n");
                                    echo("                                " . $itemTax->getCurrencyCode() . "\n");
                                }
                                if ($itemTax->isSetAmount()) 
                                {
                                    echo("                            Amount\n");
                                    echo("                                " . $itemTax->getAmount() . "\n");
                                }
                            } 
                            if ($member->isSetShippingTax()) { 
                                echo("                        ShippingTax\n");
                                $shippingTax = $member->getShippingTax();
                                if ($shippingTax->isSetCurrencyCode()) 
                                {
                                    echo("                            CurrencyCode\n");
                                    echo("                                " . $shippingTax->getCurrencyCode() . "\n");
                                }
                                if ($shippingTax->isSetAmount()) 
                                {
                                    echo("                            Amount\n");
                                    echo("                                " . $shippingTax->getAmount() . "\n");
                                }
                            } 
                            if ($member->isSetGiftWrapTax()) { 
                                echo("                        GiftWrapTax\n");
                                $giftWrapTax = $member->getGiftWrapTax();
                                if ($giftWrapTax->isSetCurrencyCode()) 
                                {
                                    echo("                            CurrencyCode\n");
                                    echo("                                " . $giftWrapTax->getCurrencyCode() . "\n");
                                }
                                if ($giftWrapTax->isSetAmount()) 
                                {
                                    echo("                            Amount\n");
                                    echo("                                " . $giftWrapTax->getAmount() . "\n");
                                }
                            } 
                            if ($member->isSetShippingDiscount()) { 
                                echo("                        ShippingDiscount\n");
                                $shippingDiscount = $member->getShippingDiscount();
                                if ($shippingDiscount->isSetCurrencyCode()) 
                                {
                                    echo("                            CurrencyCode\n");
                                    echo("                                " . $shippingDiscount->getCurrencyCode() . "\n");
                                }
                                if ($shippingDiscount->isSetAmount()) 
                                {
                                    echo("                            Amount\n");
                                    echo("                                " . $shippingDiscount->getAmount() . "\n");
                                }
                            } 
                            if ($member->isSetPromotionDiscount()) { 
                                echo("                        PromotionDiscount\n");
                                $promotionDiscount = $member->getPromotionDiscount();
                                if ($promotionDiscount->isSetCurrencyCode()) 
                                {
                                    echo("                            CurrencyCode\n");
                                    echo("                                " . $promotionDiscount->getCurrencyCode() . "\n");
                                }
                                if ($promotionDiscount->isSetAmount()) 
                                {
                                    echo("                            Amount\n");
                                    echo("                                " . $promotionDiscount->getAmount() . "\n");
                                }
                            } 
                            if ($member->isSetPromotionIds()) { 
                                echo("                        PromotionIds\n");
                                $promotionIds = $member->getPromotionIds();
                                $member1List  =  $promotionIds->getPromotionId();
                                foreach ($member1List as $member1) { 
                                    echo("                            member\n");
                                    echo("                                " . $member1);
                                }	
                            } 
                        }*/
                            
                    } 
                //} 
  /*              if ($response->isSetResponseMetadata()) { 
                    echo("            ResponseMetadata\n");
                    $responseMetadata = $response->getResponseMetadata();
                    if ($responseMetadata->isSetRequestId()) 
                    {
                        echo("                RequestId\n");
                        echo("                    " . $responseMetadata->getRequestId() . "\n");
                    }*/
                }

     } catch (MarketplaceWebServiceOrders_Exception $ex) {
        $output["STATUS"] = "ERROR";
            $output["MESSAGE"] = $ex->getMessage();
            echo json_encode($output); 
            die;
     }
 }
 

