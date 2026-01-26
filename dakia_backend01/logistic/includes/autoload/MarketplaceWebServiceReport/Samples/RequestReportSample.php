<?php

//require_once("../includes/settings/common.inc.php");
//require_once ('../includes/autoload/MarketplaceWebServiceReport/Samples/.config.inc.php');
///httpdocs/remote/includes/autoload/MarketplaceWebServiceOrders
//echo getcwd();

require_once('../Client.php');
//require_once('../includes/autoload/MarketplaceWebServiceReport/Model/ListOrdersRequest.php');
require_once('../Model/IdList.php');
//require_once('../includes/autoload/MarketplaceWebServiceReport/Model/ListOrderItemsRequest.php');
//require_once('../includes/autoload/MarketplaceWebServiceReport/Model/OrderStatusList.php');
require_once('../Model.php');

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
 * Report  Sample
 */

include_once ('.config.inc.php'); 

/************************************************************************
* Uncomment to configure the client instance. Configuration settings
* are:
*
* - MWS endpoint URL
* - Proxy host and port.
* - MaxErrorRetry.
***********************************************************************/
// IMPORTANT: Uncomment the approiate line for the country you wish to
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
     'AKIAJBUWT3ZBRDV3QITA', 
     '6y9yurr8KHXaj9Rvt83ACZyZYw2gamkvpXtu1tIe', 
     $config,
     'oneworld',
     '2');
 
 //{"AWS_ACCESS_KEY_ID":"","AWS_SECRET_ACCESS_KEY":"6y9yurr8KHXaj9Rvt83ACZyZYw2gamkvpXtu1tIe","MERCHANT_ID":"A3LX344APRTG2Z","MARKETPLACE_ID":"A1F83G8C2ARO7P"}
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
 * sample for Report Action
 ***********************************************************************/
// Constructing the MarketplaceId array which will be passed in as the the MarketplaceIdList 
// parameter to the RequestReportRequest object.
$marketplaceIdArray = array("Id" => array('A1F83G8C2ARO7P'));

 // @TODO: set request. Action can be passed as MarketplaceWebService_Model_ReportRequest
 // object or array of parameters
 
$parameters = array (
   'Merchant' => 'A3LX344APRTG2Z',
   'MarketplaceIdList' => $marketplaceIdArray,
   'ReportType' => '_GET_MERCHANT_LISTINGS_DATA_',
   'ReportOptions' => 'ShowSalesChannel=true',
  // 'MWSAuthToken' => '<MWS Auth Token>', // Optional
);
 
 $request = new MarketplaceWebService_Model_RequestReportRequest($parameters);
 
// $request = new MarketplaceWebService_Model_RequestReportRequest();
// $request->setMarketplaceIdList($marketplaceIdArray);
// $request->setMerchant(MERCHANT_ID);
// $request->setReportType('_GET_MERCHANT_LISTINGS_DATA_');
// $request->setMWSAuthToken('<MWS Auth Token>'); // Optional

// Using ReportOptions
// $request->setReportOptions('ShowSalesChannel=true');
 
 invokeRequestReport($service, $request);
 
/**
  * Get Report List Action Sample
  * returns a list of reports; by default the most recent ten reports,
  * regardless of their acknowledgement status
  *   
  * @param MarketplaceWebService_Interface $service instance of MarketplaceWebService_Interface
  * @param mixed $request MarketplaceWebService_Model_GetReportList or array of parameters
  */
  function invokeRequestReport(MarketplaceWebService_Interface $service, $request) 
  {
      try {
              $response = $service->requestReport($request);
              
                echo ("Service Response\n");
                echo ("=============================================================================\n");

                echo("        RequestReportResponse\n");
                if ($response->isSetRequestReportResult()) { 
                    echo("            RequestReportResult\n");
                    $requestReportResult = $response->getRequestReportResult();
                    
                    if ($requestReportResult->isSetReportRequestInfo()) {
                        
                        $reportRequestInfo = $requestReportResult->getReportRequestInfo();
                          echo("                ReportRequestInfo\n");
                          if ($reportRequestInfo->isSetReportRequestId()) 
                          {
                              echo("                    ReportRequestId\n");
                              echo("                        " . $reportRequestInfo->getReportRequestId() . "\n");
                          }
                          if ($reportRequestInfo->isSetReportType()) 
                          {
                              echo("                    ReportType\n");
                              echo("                        " . $reportRequestInfo->getReportType() . "\n");
                          }
                          if ($reportRequestInfo->isSetStartDate()) 
                          {
                              echo("                    StartDate\n");
                              echo("                        " . $reportRequestInfo->getStartDate()->format(DATE_FORMAT) . "\n");
                          }
                          if ($reportRequestInfo->isSetEndDate()) 
                          {
                              echo("                    EndDate\n");
                              echo("                        " . $reportRequestInfo->getEndDate()->format(DATE_FORMAT) . "\n");
                          }
                          if ($reportRequestInfo->isSetSubmittedDate()) 
                          {
                              echo("                    SubmittedDate\n");
                              echo("                        " . $reportRequestInfo->getSubmittedDate()->format(DATE_FORMAT) . "\n");
                          }
                          if ($reportRequestInfo->isSetReportProcessingStatus()) 
                          {
                              echo("                    ReportProcessingStatus\n");
                              echo("                        " . $reportRequestInfo->getReportProcessingStatus() . "\n");
                          }
                      }
                } 
                if ($response->isSetResponseMetadata()) { 
                    echo("            ResponseMetadata\n");
                    $responseMetadata = $response->getResponseMetadata();
                    if ($responseMetadata->isSetRequestId()) 
                    {
                        echo("                RequestId\n");
                        echo("                    " . $responseMetadata->getRequestId() . "\n");
                    }
                } 

                echo("            ResponseHeaderMetadata: " . $response->getResponseHeaderMetadata() . "\n");
     } catch (MarketplaceWebService_Exception $ex) {
         echo("Caught Exception: " . $ex->getMessage() . "\n");
         echo("Response Status Code: " . $ex->getStatusCode() . "\n");
         echo("Error Code: " . $ex->getErrorCode() . "\n");
         echo("Error Type: " . $ex->getErrorType() . "\n");
         echo("Request ID: " . $ex->getRequestId() . "\n");
         echo("XML: " . $ex->getXML() . "\n");
         echo("ResponseHeaderMetadata: " . $ex->getResponseHeaderMetadata() . "\n");
     }
 }
 
?>

                                                                                
