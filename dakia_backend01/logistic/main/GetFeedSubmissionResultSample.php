<?php
require_once('../includes/autoload/MarketplaceWebService/Samples/.config.inc.php');
require_once('../includes/autoload/MarketplaceWebService/Client.php');
require_once('../includes/autoload/MarketplaceWebService/Model/IdList.php');
require_once('../includes/autoload/MarketplaceWebService/Model/RequestReportRequest.php');
require_once('../includes/autoload/MarketplaceWebService/Model.php');
require_once('../includes/autoload/MarketplaceWebService/Model/GetReportListRequest.php');
require_once('../includes/autoload/MarketplaceWebService/Model/GetReportRequestListRequest.php');
require_once('../includes/autoload/MarketplaceWebService/Model/GetReportRequest.php');
require_once('../includes/autoload/MarketplaceWebService/Model/SubmitFeedRequest.php');
require_once('../includes/autoload/MarketplaceWebService/Model/GetFeedSubmissionResultRequest.php');
require_once('../includes/autoload/MarketplaceWebService/Model/GetFeedSubmissionResultResponse.php');
/** 
 *  PHP Version 5
 *
 *  @category    Amazon
 *  @package     MarketplaceWebService
 *  @copyright   Copyright 2009 Amazon Technologies, Inc.
 *  @link        http://aws.amazon.com
 *  @license     http://aws.amazon.com/apache2.0  Apache License, Version 2.0
 *  @version     2009-01-01
 */
/******************************************************************************* 

 *  Marketplace Web Service PHP5 Library
 *  Generated: Thu May 07 13:07:36 PDT 2009
 * 
 */

/**
 * Get Feed Submission Result  Sample
 */

//include_once ('.config.inc.php'); 
//require_once ("C:\wamp\www\am\Client.php");
/************************************************************************
* Uncomment to configure the client instance. Configuration settings
* are:
*
* - MWS endpoint URL
* - Proxy host and port.
* - MaxErrorRetry.
***********************************************************************/
// IMPORTANT: Uncomment the appropriate line for the country you wish to
// sell in:
// United States:
//$serviceUrl = "https://mws.amazonservices.com";
// United Kingdom
$serviceUrl = "https://mws.amazonservices.co.uk";
// Germany
//$serviceUrl = "https://mws.amazonservices.de";
// France
//$serviceUrl = "https://mws.amazonservices.fr";
// Italy
//$serviceUrl = "https://mws.amazonservices.it";
// Japan
//$serviceUrl = "https://mws.amazonservices.jp";
// China
//$serviceUrl = "https://mws.amazonservices.com.cn";
// Canada
//$serviceUrl = "https://mws.amazonservices.ca";
// India
//$serviceUrl = "https://mws.amazonservices.in";

$config = array (
  'ServiceURL' => $serviceUrl,
  'ProxyHost' => null,
  'ProxyPort' => -1,
  'MaxErrorRetry' => 3,
);

/************************************************************************
 * Instantiate Implementation of MarketplaceWebService
 * 
 * AWS_ACCESS_KEY_ID and AWS_SECRET_ACCESS_KEY constants 
 * are defined in the .config.inc.php located in the same 
 * directory as this sample
 ***********************************************************************/
 $service = new MarketplaceWebService_Client(
     AWS_ACCESS_KEY_ID, 
     AWS_SECRET_ACCESS_KEY, 
     $config,
     APPLICATION_NAME,
     APPLICATION_VERSION);

/************************************************************************
 * Uncomment to try out Mock Service that simulates MarketplaceWebService
 * responses without calling MarketplaceWebService service.
 *
 * Responses are loaded from local XML files. You can tweak XML files to
 * experiment with various outputs during development
 *
 * XML files available under MarketplaceWebService/Mock tree
 *
 ***********************************************************************/
 // $service = new MarketplaceWebService_Mock();

/************************************************************************
 * Setup request parameters and uncomment invoke to try out 
 * sample for Get Feed Submission Result Action
 ***********************************************************************/
 // @TODO: set request. Action can be passed as MarketplaceWebService_Model_GetFeedSubmissionResultRequest
 // object or array of parameters
$filename = 'file.xml';
$parameters = array (
  'Marketplace' => 'A1F83G8C2ARO7P', 
  'Merchant' => 'A3LX344APRTG2Z',
  'FeedSubmissionId' => '60359018674',
  'FeedSubmissionResult' => @fopen('file.xml', 'w+'),
);

$request = new MarketplaceWebService_Model_GetFeedSubmissionResultRequest($parameters);

try 
    {
        $result = $service->getFeedSubmissionResult($request);
        $response = file_get_contents('file.xml');
        @unlink('file.xml');
        $xml = new SimpleXMLElement($response);
        
        $errorResponse = $xml->Message->ProcessingReport->ProcessingSummary->MessagesWithError;
        $warningResponse = $xml->Message->ProcessingReport->ProcessingSummary->MessagesWithWarning;       
    }

catch (MarketplaceWebService_Exception $ex) 
{
    echo "<pre>";
    echo("Caught Exception: " . $ex->getMessage() . "\n");
    echo("Response Status Code: " . $ex->getStatusCode() . "\n");
    echo("Error Code: " . $ex->getErrorCode() . "\n");
    echo("Error Type: " . $ex->getErrorType() . "\n");
    echo("Request ID: " . $ex->getRequestId() . "\n");
    echo("XML: " . $ex->getXML() . "\n");
    echo("ResponseHeaderMetadata: " . $ex->getResponseHeaderMetadata() . "\n");
    echo "=================================";

}
// }
?>
                              
