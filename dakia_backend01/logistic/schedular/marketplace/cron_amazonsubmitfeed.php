<?php

//error_reporting(E_ALL & ~(E_NOTICE|E_WARNING));
require_once("../includes/settings/config.inc.php");

include_classes([
    'iaddress.class',
    'consignment.class',
    'consignmentfilter.class',
    'parcel.class',
    'parcelfilter.class'
]);
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


use Aws\Sqs\SqsClient;

include_classes([
    'marketplaceorderdetails.class',
    'marketplaceorderdetails.class',
    'marketplaceorder.class',
    'marketplacesfilter.class',
    'marketplacesauthenticatefield.class',
    'marketalacesauthenticatefieldfilter.class',
    'marketplaceorderfilter.class',
    'usermarketplacesmappingfilter.class',
    'usermarketplacesmapping.class',
    'marketplaces.class',
    'services.class',
    'carrier.class',
    'consignment.class',
    'consignmentfilter.class',
    'productFilter.class',
    'products.class'
]);

DbAccess3::connect(SETTING_DB_SERVER, SETTING_DB_USER, SETTING_DB_PASSWORD, SETTING_DB_DATABASE);
$AmazonProductApiResultFilter = new AmazonProductApiResultFilter();
$AmazonProductApiResultFilter->addFilter("feed_type = 'product'");
$AmazonProductApiResultFilter->addFilter("feed_status = 'SUBMITTED'");
$submittedProductList = $AmazonProductApiResultFilter->getList();
echo '<pre>';
print_r($submittedProductList);
echo '</pre>';
die;
foreach($submittedProductList as $productFeedList)
{
    $productSubmitFeedIdArray[] = $productFeedList->getFeedId();
}

function getReportResponseAws($productSubmitFeedIdArray)
{
    require_once '../includes/3rdparty/AmazonAws/aws-autoloader.php';
    $queueUrl = "https://sqs.us-east-2.amazonaws.com/095611935099/submitFeed";
    $credentials = new Aws\Credentials\Credentials('AKIAIMVDRT2XYX2SCMWQ', '/wqmPzz3vzf7Zqbtp8nHLdIEQpl/0fChgDXu8axN');
    $config = array(
        'region' => 'us-east-2',
        'version' => 'latest',
        'credentials' => $credentials
    );

    $client = new SqsClient($config, [
        //'profile' => 'default',
        'region' => 'us-east-2',
        'version' => '2012-11-05'
    ]);

    $result = $client->receiveMessage(array(
        'QueueUrl' => $queueUrl,
        'MaxNumberOfMessages' => 10,
        'WaitTimeSeconds' => 0,
    ));

    foreach ($result->get('Messages') as $message) {
        $xml = $message['Body'];
        print_r($xml).'sadasd'; die;
        $statusArray = simplexml_load_string($xml);
        $submitFeedIdFromNotification = $statusArray->NotificationPayload->FeedProcessingFinished->SubmitFeedId;

        if (in_array($submitFeedIdFromNotification, $productSubmitFeedIdArray)) {
            echo '<pre>';
            print_r($statusArray);
            echo '</pre>';
            die;
            $generatedRequestId = $statusArray->NotificationPayload->ReportProcessingFinishedNotification->ReportId;
            $reportStatus = $statusArray->NotificationPayload->ReportProcessingFinishedNotification->ReportProcessingStatus;
            if ($reportStatus == 'DONE_NO_DATA') {
                $output["STATUS"] = "SUCCESS";
                $output["MESSAGE"] = "Unshipped Parcels are not available";
                return $output;
            }
            return $generatedRequestId;
        }
    }
    return false;
}
/*
$parameters = array (
  'Merchant' => 'A3LX344APRTG2Z',
  'FeedProcessingStatusList' => array ('Status' => array ('_DONE_', '_SUBMITTED_')),
  'FeedSubmissionIdList' => array('Id' => $productFeedArray), 
);
$request = new MarketplaceWebService_Model_GetFeedSubmissionListRequest($parameters);
invokeGetFeedSubmissionList($service, $request);

function invokeGetFeedSubmissionList(MarketplaceWebService_Interface $service, $request) 
  {
      try {
              $response = $service->getFeedSubmissionList($request);
              
                echo ("Service Response\n");
                echo ("=============================================================================\n");

                echo("        GetFeedSubmissionListResponse\n");
                if ($response->isSetGetFeedSubmissionListResult()) { 
                    echo("            GetFeedSubmissionListResult\n");
                    $getFeedSubmissionListResult = $response->getGetFeedSubmissionListResult();
                    if ($getFeedSubmissionListResult->isSetNextToken()) 
                    {
                        echo("                NextToken\n");
                        echo("                    " . $getFeedSubmissionListResult->getNextToken() . "\n");
                    }
                    if ($getFeedSubmissionListResult->isSetHasNext()) 
                    {
                        echo("                HasNext\n");
                        echo("                    " . $getFeedSubmissionListResult->getHasNext() . "\n");
                    }
                    $feedSubmissionInfoList = $getFeedSubmissionListResult->getFeedSubmissionInfoList();
                    foreach ($feedSubmissionInfoList as $feedSubmissionInfo) {
                        echo("                FeedSubmissionInfo\n");
                        if ($feedSubmissionInfo->isSetFeedSubmissionId()) 
                        {
                            echo("                    FeedSubmissionId\n");
                            echo("                        " . $feedSubmissionInfo->getFeedSubmissionId() . "\n");
                        }
                        if ($feedSubmissionInfo->isSetFeedType()) 
                        {
                            echo("                    FeedType\n");
                            echo("                        " . $feedSubmissionInfo->getFeedType() . "\n");
                        }
                        if ($feedSubmissionInfo->isSetSubmittedDate()) 
                        {
                            echo("                    SubmittedDate\n");
                            echo("                        " . $feedSubmissionInfo->getSubmittedDate()->format(DATE_FORMAT) . "\n");
                        }
                        if ($feedSubmissionInfo->isSetFeedProcessingStatus()) 
                        {
                            echo("                    FeedProcessingStatus\n");
                            echo("                        " . $feedSubmissionInfo->getFeedProcessingStatus() . "\n");
                        }
                        if ($feedSubmissionInfo->isSetStartedProcessingDate()) 
                        {
                            echo("                    StartedProcessingDate\n");
                            echo("                        " . $feedSubmissionInfo->getStartedProcessingDate()->format(DATE_FORMAT) . "\n");
                        }
                        if ($feedSubmissionInfo->isSetCompletedProcessingDate()) 
                        {
                            echo("                    CompletedProcessingDate\n");
                            echo("                        " . $feedSubmissionInfo->getCompletedProcessingDate()->format(DATE_FORMAT) . "\n");
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
 }*/
?>





