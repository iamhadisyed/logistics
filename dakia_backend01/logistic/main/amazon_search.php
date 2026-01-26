<?php
require_once("../includes/settings/config.inc.php");
require_once ('../includes/autoload/MarketplaceWebServiceOrders/Samples/.config.inc.php'); 
require_once('../includes/autoload/MarketplaceWebServiceOrders/Client.php');
require_once('../includes/autoload/MarketplaceWebServiceOrders/Model/ListOrdersRequest.php');
require_once('../includes/autoload/MarketplaceWebServiceOrders/Model/MarketplaceIdList.php');
require_once('../includes/autoload/MarketplaceWebServiceOrders/Model/ListOrderItemsRequest.php');
require_once('../includes/autoload/MarketplaceWebServiceOrders/Model/GetOrderRequest.php');
require_once('../includes/autoload/MarketplaceWebServiceOrders/Model/OrderIdList.php');
require_once('../includes/autoload/MarketplaceWebServiceOrders/Model.php');

/************************************************************************
 * Instantiate Implementation of MarketplaceWebServiceOrders
 * 
 * AWS_ACCESS_KEY_ID and AWS_SECRET_ACCESS_KEY constants 
 * are defined in the .config.inc.php located in the same 
 * directory as this sample
 ***********************************************************************/
// United Kingdom
$serviceUrl = "https://mws.amazonservices.es/Orders/2011-01-01";
$config = array (
   'ServiceURL' => $serviceUrl,
   'ProxyHost' => null,
   'ProxyPort' => -1,
   'MaxErrorRetry' => 5,
 );

 $service = new MarketplaceWebServiceOrders_Client(
        AWS_ACCESS_KEY_ID,
        AWS_SECRET_ACCESS_KEY,
        APPLICATION_NAME,
        APPLICATION_VERSION,
        $config);

 	//	$data = array();

$marketplaceIdList = new MarketplaceWebServiceOrders_Model_MarketplaceIdList();
$marketplaceIdList->setId(array(MARKETPLACE_ID));	 

	 	 $request_getOrder = new MarketplaceWebServiceOrders_Model_GetOrderRequest();
 		 $request_getOrder->setSellerId(MERCHANT_ID);
 		 // Set the list of AmazonOrderIds
 		 $orderIds = new MarketplaceWebServiceOrders_Model_OrderIdList();
 		 $orderIds->setId($_SESSION["order_id"]);
		 
		 $request_getOrder->setAmazonOrderId($orderIds);
		 invokeGetOrder($service, $request_getOrder);		
 /*
   Get Orders
   This Function will get the records in chunks of 10
 
  * Get Order Action
  * This operation takes up to 50 order ids and returns the corresponding orders.
 */
  function invokeGetOrder(MarketplaceWebServiceOrders_Interface $service, $request) 
  {
      try {
		  	  
			  //print_r($request);
              $response = $service->getOrder($request);
			  
			  //print_r($response);
			  
			  
			  
			  if ($response->isSetGetOrderResult()) 
			   { 
                    $getOrderResult = $response->getGetOrderResult();
                    if ($getOrderResult->isSetOrders()) 
					{ 
                        $orders = $getOrderResult->getOrders();
                        $memberList = $orders->getOrder();
                        foreach ($memberList as $member) 
						{
                            if ($member->isSetAmazonOrderId()) 
                            {
                                $index = $member->getAmazonOrderId();
								$data[$index]["AmazonOrderId"] = $member->getAmazonOrderId();
                            }
                          /*  if ($member->isSetSellerOrderId()) 
                            {
                                $data[$index]["SellerOrderId"] = $member->getSellerOrderId();
                            }*/
                            if ($member->isSetPurchaseDate()) 
                            {
                                $data[$index]["PurchaseDate"] = $member->getPurchaseDate();
                            }
                            /*if ($member->isSetLastUpdateDate()) 
                            {
                                $data[$index]["LastUpdateDate"] = $member->getLastUpdateDate();
                            }*/
                            if ($member->isSetOrderStatus()) 
                            {
                                $data[$index]["OrderStatus"] = $member->getOrderStatus();
                            }
                           /* if ($member->isSetFulfillmentChannel()) 
                            {
                                $data[$index]["FulfillmentChannel"] = $member->getFulfillmentChannel();
                            }
                            if ($member->isSetSalesChannel()) 
                            {
                          		$data[$index]["SalesChannel"] = $member->getSalesChannel();
                            }
							
                            if ($member->isSetOrderChannel()) 
                            {
                                $data[$index]["OrderChannel"] = $member->getOrderChannel();
                            }
                            if ($member->isSetShipServiceLevel()) 
                            {
                                $data[$index]["ShipServiceLevel"] = $member->getShipServiceLevel();
                            }
                            if ($member->isSetShippingAddress()) { 
                                $shippingAddress = $member->getShippingAddress();
                                if ($shippingAddress->isSetName()) 
                                {
                                    $data[$index]["Name"] = $shippingAddress->getName();
                                }
                                if ($shippingAddress->isSetAddressLine1()) 
                                {
                                   $data[$index]["AddressLine1"] = $shippingAddress->getAddressLine1();
                                }
                                if ($shippingAddress->isSetAddressLine2()) 
                                {
                                    $data[$index]["AddressLine2"] =  $shippingAddress->getAddressLine2();
                                }
                                if ($shippingAddress->isSetAddressLine3()) 
                                {
                                    $data[$index]["AddressLine3"] = $shippingAddress->getAddressLine3();
                                }
                                if ($shippingAddress->isSetCity()) 
                                {
                                    $data[$index]["City"] = $shippingAddress->getCity();
                                }
                                if ($shippingAddress->isSetCounty()) 
                                {
                                    $data[$index]["Country"] = $shippingAddress->getCounty();
                                }
                                if ($shippingAddress->isSetDistrict()) 
                                {
                                    $data[$index]["District"] = $shippingAddress->getDistrict();
                                }
                                if ($shippingAddress->isSetStateOrRegion()) 
                                {
                                    $data[$index]["StateOrRegion"] = $shippingAddress->getStateOrRegion();
                                }
                                if ($shippingAddress->isSetPostalCode()) 
                                {
                                     $data[$index]["PostalCode"] = $shippingAddress->getPostalCode();
                                }
                                if ($shippingAddress->isSetCountryCode()) 
                                {
                                     $data[$index]["CountryCode"] = $shippingAddress->getCountryCode();
                                }
                                if ($shippingAddress->isSetPhone()) 
                                {
                                     $data[$index]["Phone"] = $shippingAddress->getPhone();
                                }
                            } 
                            if ($member->isSetOrderTotal()) { 
                                $orderTotal = $member->getOrderTotal();
                                if ($orderTotal->isSetCurrencyCode()) 
                                {
                                     $data[$index]["CurrencyCode"] = $orderTotal->getCurrencyCode();
                                }
                                if ($orderTotal->isSetAmount()) 
                                {
                                     $data[$index]["Amount"] = $orderTotal->getAmount();
                                }
                            } 
                            if ($member->isSetNumberOfItemsShipped()) 
                            {
                                 $data[$index]["NumberOfItemsShipped"] = $member->getNumberOfItemsShipped();
                            }
                            if ($member->isSetNumberOfItemsUnshipped()) 
                            {
                                $data[$index]["NumberOfItemsUnshipped"] = $member->getNumberOfItemsUnshipped();
                            }*/
                        }
                    } 
                }             

     } catch (MarketplaceWebServiceOrders_Exception $ex) {
         echo("Caught Exception: " . $ex->getMessage() . "\n");
         echo("Response Status Code: " . $ex->getStatusCode() . "\n");
         echo("Error Code: " . $ex->getErrorCode() . "\n");
         echo("Error Type: " . $ex->getErrorType() . "\n");
         echo("Request ID: " . $ex->getRequestId() . "\n");
         echo("XML: " . $ex->getXML() . "\n");
     }
	$_SESSION["filter"] = $data;
 }
                

            
