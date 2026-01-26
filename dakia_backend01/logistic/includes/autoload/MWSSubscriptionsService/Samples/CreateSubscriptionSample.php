<?php
require_once('../includes/autoload/MWSSubscriptionsService/Samples/.config.inc.php');
require_once('../includes/autoload/MWSSubscriptionsService/client.php');
require_once('../includes/autoload/MWSSubscriptionsService/Model/RegisterDestinationInput.php');
require_once('../includes/autoload/MWSSubscriptionsService/Model/Destination.php');
require_once('../includes/autoload/MWSSubscriptionsService/Model/AttributeKeyValue.php');
require_once('../includes/autoload/MWSSubscriptionsService/Model/AttributeKeyValueList.php');

/************************************************************************
 * 
 * Instantiate Implementation of MWSSubscriptionsService
 *
 * AWS_ACCESS_KEY_ID and AWS_SECRET_ACCESS_KEY constants
 * are defined in the .config.inc.php located in the same
 * directory as this sample
 ***********************************************************************/
// More endpoints are listed in the MWS Developer Guide
// North America:
//$serviceUrl = "https://mws.amazonservices.com/Subscriptions/2013-07-01";
// Europe
$serviceUrl = "https://mws-eu.amazonservices.com/Subscriptions/2013-07-01";
// Japan
//$serviceUrl = "https://mws.amazonservices.jp/Subscriptions/2013-07-01";
// China
//$serviceUrl = "https://mws.amazonservices.com.cn/Subscriptions/2013-07-01";


 $config = array (
   'ServiceURL' => $serviceUrl,
   'ProxyHost' => null,
   'ProxyPort' => -1,
   'ProxyUsername' => null,
   'ProxyPassword' => null,
   'MaxErrorRetry' => 3,
 );

 $service = new MWSSubscriptionsService_Client(
        'AKIAJBUWT3ZBRDV3QITA',
        '6y9yurr8KHXaj9Rvt83ACZyZYw2gamkvpXtu1tIe',
        'oneworld',
        '2',
        $config);

/************************************************************************
 * Instantiate Implementation of MWSSubscriptionsService
 *
 * AWS_ACCESS_KEY_ID and AWS_SECRET_ACCESS_KEY constants
 * are defined in the .config.inc.php located in the same
 * directory as this sample
 ***********************************************************************/
// More endpoints are listed in the MWS Developer Guide
// North America:
//$serviceUrl = "https://mws.amazonservices.com/Subscriptions/2013-07-01";
// Europe
//$serviceUrl = "https://mws-eu.amazonservices.com/Subscriptions/2013-07-01";
// Japan
//$serviceUrl = "https://mws.amazonservices.jp/Subscriptions/2013-07-01";
// China
//$serviceUrl = "https://mws.amazonservices.com.cn/Subscriptions/2013-07-01";


 $config = array (
   'ServiceURL' => $serviceUrl,
   'ProxyHost' => null,
   'ProxyPort' => -1,
   'ProxyUsername' => null,
   'ProxyPassword' => null,
   'MaxErrorRetry' => 3,
 );

 $service = new MWSSubscriptionsService_Client(
        AWS_ACCESS_KEY_ID,
        AWS_SECRET_ACCESS_KEY,
        APPLICATION_NAME,
        APPLICATION_VERSION,
        $config);

/************************************************************************
 * Uncomment to try out Mock Service that simulates MWSSubscriptionsService
 * responses without calling MWSSubscriptionsService service.
 *
 * Responses are loaded from local XML files. You can tweak XML files to
 * experiment with various outputs during development
 *
 * XML files available under MWSSubscriptionsService/Mock tree
 *
 ***********************************************************************/
 // $service = new MWSSubscriptionsService_Mock();

/************************************************************************
 * Setup request parameters and uncomment invoke to try out
 * sample for Create Subscription Action
 ***********************************************************************/
 // @TODO: set request. Action can be passed as MWSSubscriptionsService_Model_CreateSubscription
 $request = new MWSSubscriptionsService_Model_CreateSubscriptionInput();
 $request->setSellerId('A3LX344APRTG2Z');
 $request->setMarketplaceId('A1F83G8C2ARO7P'); 
 
 $request->setNotificationType('ReportProcessingFinished');
 $keyvalue = new MWSSubscriptionsService_Model_AttributeKeyValue();
 $keyvalue->setKey('sqsQueueUrl');
 $keyvalue->setValue('https://sqs.us-east-2.amazonaws.com/095611935099/order_notification');
 
 $attributes = new MWSSubscriptionsService_Model_AttributeKeyValueList();
 $attributes->setmember($keyvalue);
 
 $destination = new MWSSubscriptionsService_Model_Destination();
 $destination->setDeliveryChannel('SQS');
 $destination->setAttributeList($attributes);
 
 // object or array of parameters
 $request->setDestination($destination);
 invokeCreateSubscription($service, $request);

/**
  * Get Create Subscription Action Sample
  * Gets competitive pricing and related information for a product identified by
  * the MarketplaceId and ASIN.
  *
  * @param MWSSubscriptionsService_Interface $service instance of MWSSubscriptionsService_Interface
  * @param mixed $request MWSSubscriptionsService_Model_CreateSubscription or array of parameters
  */

  function invokeCreateSubscription(MWSSubscriptionsService_Interface $service, $request)
  {
      try {
        $response = $service->CreateSubscription($request);

        echo ("Service Response\n");
        echo ("=============================================================================\n");

        $dom = new DOMDocument();
        $dom->loadXML($response->toXML());
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = true;
        echo $dom->saveXML();
        echo("ResponseHeaderMetadata: " . $response->getResponseHeaderMetadata() . "\n");

     } catch (MWSSubscriptionsService_Exception $ex) {
        echo("Caught Exception: " . $ex->getMessage() . "\n");
        echo("Response Status Code: " . $ex->getStatusCode() . "\n");
        echo("Error Code: " . $ex->getErrorCode() . "\n");
        echo("Error Type: " . $ex->getErrorType() . "\n");
        echo("Request ID: " . $ex->getRequestId() . "\n");
        echo("XML: " . $ex->getXML() . "\n");
        echo("ResponseHeaderMetadata: " . $ex->getResponseHeaderMetadata() . "\n");
     }
 }

