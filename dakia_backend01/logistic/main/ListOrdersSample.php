<?php
/*******************************************************************************
 * Copyright 2009-2017 Amazon Services. All Rights Reserved.
 * Licensed under the Apache License, Version 2.0 (the "License"); 
 *
 * You may not use this file except in compliance with the License. 
 * You may obtain a copy of the License at: http://aws.amazon.com/apache2.0
 * This file is distributed on an "AS IS" BASIS, WITHOUT WARRANTIES OR 
 * CONDITIONS OF ANY KIND, either express or implied. See the License for the 
 * specific language governing permissions and limitations under the License.
 *******************************************************************************
 * PHP Version 5
 * @category Amazon
 * @package  Marketplace Web Service Orders
 * @version  2013-09-01
 * Library Version: 2017-02-22
 * Generated: Thu Mar 02 12:41:08 UTC 2017
 */

/**
 * List Orders Sample
 */

require_once('../includes/autoload/MarketplaceWebServiceOrders/Samples/.config.inc.php');


require_once('../includes/autoload/MarketplaceWebServiceOrders/Client.php');
require_once('../includes/autoload/MarketplaceWebServiceOrders/Model/ListOrdersRequest.php');
require_once('../includes/autoload/MarketplaceWebServiceOrders/Model/MarketplaceIdList.php');
require_once('../includes/autoload/MarketplaceWebServiceOrders/Model/ListOrderItemsRequest.php');
require_once('../includes/autoload/MarketplaceWebServiceOrders/Model/OrderStatusList.php');
require_once('../includes/autoload/MarketplaceWebServiceOrders/Model.php');

/************************************************************************
 * Instantiate Implementation of MarketplaceWebServiceOrders
 *
 * AWS_ACCESS_KEY_ID and AWS_SECRET_ACCESS_KEY constants
 * are defined in the .config.inc.php located in the same
 * directory as this sample
 ***********************************************************************/
// More endpoints are listed in the MWS Developer Guide
// North America:
//$serviceUrl = "https://mws.amazonservices.com/Orders/2013-09-01";
// Europe
$serviceUrl = "https://mws-eu.amazonservices.com/Orders/2013-09-01";
// Japan
//$serviceUrl = "https://mws.amazonservices.jp/Orders/2013-09-01";
// China
//$serviceUrl = "https://mws.amazonservices.com.cn/Orders/2013-09-01";


 $config = array (
   'ServiceURL' => $serviceUrl,
   'ProxyHost' => null,
   'ProxyPort' => -1,
   'ProxyUsername' => null,
   'ProxyPassword' => null,
   'MaxErrorRetry' => 3,
 );

 $service = new MarketplaceWebServiceOrders_Client(
        'AKIAJBUWT3ZBRDV3QITA',
        '6y9yurr8KHXaj9Rvt83ACZyZYw2gamkvpXtu1tIe',
        'oneworld',
        '2',
        $config);

/************************************************************************
 * Uncomment to try out Mock Service that simulates MarketplaceWebServiceOrders
 * responses without calling MarketplaceWebServiceOrders service.
 *
 * Responses are loaded from local XML files. You can tweak XML files to
 * experiment with various outputs during development
 *
 * XML files available under MarketplaceWebServiceOrders/Mock tree
 *
 ***********************************************************************/
 // $service = new MarketplaceWebServiceOrders_Mock();

/************************************************************************
 * Setup request parameters and uncomment invoke to try out
 * sample for List Orders Action
 ***********************************************************************/
 // @TODO: set request. Action can be passed as MarketplaceWebServiceOrders_Model_ListOrders
$request = new MarketplaceWebServiceOrders_Model_ListOrdersRequest();
$request->setSellerId('A3LX344APRTG2Z');
$request->setMarketplaceId('A1F83G8C2ARO7P'); 

//$request->setCreatedAfter('2019-12-18T14:32:16.50-07');
$createdAfter  = new DateTime('-1 day');
$request->setCreatedAfter($createdAfter->format(DATE_ISO8601));   

$start = date('Y-m-d H:i:s');
$startdate = date('Y-m-d H:i:s',strtotime('-3 minutes',strtotime($start)));

$createdBefore  = new DateTime($startdate);
$request->setCreatedBefore($createdBefore->format(DATE_ISO8601));

invokeListOrders($service, $request);

/**
  * Get List Orders Action Sample
  * Gets competitive pricing and related information for a product identified by
  * the MarketplaceId and ASIN.
  *
  * @param MarketplaceWebServiceOrders_Interface $service instance of MarketplaceWebServiceOrders_Interface
  * @param mixed $request MarketplaceWebServiceOrders_Model_ListOrders or array of parameters
  */

  function invokeListOrders(MarketplaceWebServiceOrders_Interface $service, $request)
  {
      try {
        $response = $service->ListOrders($request);
        echo '<pre>';
        foreach($response->ListOrdersResult->Orders as $orders)
        {
            print_r($orders); die;
            echo $orders->AmazonOrderId;
            
        }        
     } catch (MarketplaceWebServiceOrders_Exception $ex) {
        echo("Caught Exception: " . $ex->getMessage() . "\n");
        echo("Response Status Code: " . $ex->getStatusCode() . "\n");
        echo("Error Code: " . $ex->getErrorCode() . "\n");
        echo("Error Type: " . $ex->getErrorType() . "\n");
        echo("Request ID: " . $ex->getRequestId() . "\n");
        echo("XML: " . $ex->getXML() . "\n");
        echo("ResponseHeaderMetadata: " . $ex->getResponseHeaderMetadata() . "\n");
     }
 }

