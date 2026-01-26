<?php
//include_once ('.config.inc.php'); 
require_once("../includes/settings/config.inc.php");
require_once('../includes/autoload/MarketplaceWebService/Samples/.config.inc.php');
require_once('../includes/autoload/MarketplaceWebService/Client.php');
require_once('../includes/autoload/MarketplaceWebService/Model/IdList.php');
require_once('../includes/autoload/MarketplaceWebService/Model/RequestReportRequest.php');
require_once('../includes/autoload/MarketplaceWebService/Model.php');
require_once('../includes/autoload/MarketplaceWebService/Model/GetReportListRequest.php');
require_once('../includes/autoload/MarketplaceWebService/Model/GetReportRequestListRequest.php');
require_once('../includes/autoload/MarketplaceWebService/Model/GetReportRequest.php');
require_once('../includes/mapping/usermarketplacesmappingfilter.class.php');
require_once('../includes/mapping/usermarketplacesmapping.class.php');
require_once('../includes/mapping/marketplaceorderfilter.class.php');
require_once('../includes/mapping/marketplaceorder.class.php');
require_once('../includes/mapping/country.class.php');
require_once('../includes/mapping/countryfilter.class.php');
require_once('../includes/mapping/marketplaceorderdetailsfilter.class.php');
require_once('../includes/mapping/marketplaceorderdetails.class.php');
require_once('../includes/mapping/marketplaces.class.php');
require_once('../includes/mapping/api2cart.class.php');

$user = SessionManager::getUser();
$_SESSION['userId'] = $user->getUserAccountId();

$userMarketPlaceMappingFilter = new UserMarketPlacesMappingFilter();
$userMarketPlaceMappingFilter->addFieldFilter("user_account_id", $_SESSION['userId']);
$userMarketPlaceMappingFilter->addFieldFilter("market_places_id", $_POST["marketPlace_id"]);
$result = $userMarketPlaceMappingFilter->getList();

if (count($result) > 0) {
    $marketPlaces = new MarketPlaces($result[0]->getMarketPlacesId());
    if ($marketPlaces->getIsApi2cart() == 1) {
        if(!empty($result[0]->getStoreKey())) {
            $api2CartObj = new Api2cart();
            $api2CartResult = $api2CartObj->fetchOrdersData($result[0]);
            if(empty($api2CartResult)) {
                $output["STATUS"] = "SUCCESS";
                $output["MESSAGE"] = "Orders Fetched Successfully.";
            } else {
                $output["STATUS"] = "ERROR";
                $output["MESSAGE"] = $api2CartResult;
            }
            echo json_encode($output, true);
            die;
        } else {
            $output["STATUS"] = "ERROR";
            $output["MESSAGE"] = "You have entered invalid Store Credentials";
            echo json_encode($output, true);
            die;
        }
    }
    $authdata = json_decode($result[0]->getAuthData());
    $_SESSION['AWS_ACCESS_KEY_ID'] = $authdata->AWS_ACCESS_KEY_ID;
    $_SESSION['AWS_SECRET_ACCESS_KEY'] = $authdata->AWS_SECRET_ACCESS_KEY;
    $_SESSION['MERCHANT_ID'] = $authdata->MERCHANT_ID;
    $_SESSION['MARKETPLACE_ID'] = $authdata->MARKETPLACE_ID;
}

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

$config = array(
    'ServiceURL' => $serviceUrl,
    'ProxyHost' => null,
    'ProxyPort' => -1,
    'MaxErrorRetry' => 3,
);

$service = new MarketplaceWebService_Client(
    $_SESSION['AWS_ACCESS_KEY_ID'],
    $_SESSION['AWS_SECRET_ACCESS_KEY'],
    $config,
    'oneworld',
    '2');
date_default_timezone_set('Europe/London');   // new line of code 

$marketplaceIdArray = array("Id" => $_SESSION['MARKETPLACE_ID']);
$createAfter = '';
$createBefore = '';

// getting last sync time 
// Nedd to add limit here. we just need 1 last record
$marketPlaceOrderFilter = new MarketPlaceOrderFilter();
$marketPlaceOrderFilter->addFieldFilter("    marketplace_id", $_POST["marketPlace_id"]);
$marketPlaceOrderFilter->AddOrderById("desc");
$result = $marketPlaceOrderFilter->getColumnList("create_time");

// Already data is in the database
if(count($result)>0)
{
    $last_sync_time = date('Y-m-d H:i:s',strtotime($result[0]->getcreatetime()) - 3600);
    $dteStart = new DateTime(date('Y-m-d H:i:s'));
    $dteEnd = new DateTime($last_sync_time);
    $dteDiff  = $dteStart->diff($dteEnd); 
    
    
    if(($dteDiff->h >=1 && $dteDiff->i >= 1) || $dteDiff->i > 59 || $dteDiff->d >= 1)
    {       
        $createdAfter = $dteEnd;   // new line of code
        $beforeTime = date("Y-m-d H:i:s"); 
        $createdBefore = new DateTime($beforeTime);     
    }
    else
    {
        $output["STATUS"] = "SUCCESS";
        $output["MESSAGE"] = "All Orders has been fetched successfully.";
        echo json_encode($output); 
        die;
    }
}
// first time entry in database
else {
    $createdAfter = new DateTime('-7 day');
    $beforeTime = date("Y-m-d H:i:s");
    $createdBefore = new DateTime($beforeTime);
}

/**********************************************************************************************************************************
 * API Request to send Report Type and in response we will get Request ID */

$parameters = array(
    'Merchant' => $_SESSION['MERCHANT_ID'],
    'MarketplaceIdList' => $marketplaceIdArray,
    'ReportType' => '_GET_FLAT_FILE_ORDERS_DATA_',
    //'ReportType' => '_GET_FLAT_FILE_ACTIONABLE_ORDER_DATA_',
    'StartDate' => $createdAfter,
    'EndDate' => $createdBefore
);
$request = new MarketplaceWebService_Model_RequestReportRequest($parameters);
//$request->setReportOptions('ShowSalesChannel=true');
$reportRequestId = invokeRequestReport($service, $request);
$generatedRequestId = getReportResponseAws($reportRequestId);

/***********************************************************************************************************************************/

/***********************************************************************************************************************************
 * API Request to get Generated Report ID. Input of this API is the output of the "RequestReportRequest" which is Request ID        */
$counter = 0;
//$request = new MarketplaceWebService_Model_GetReportRequestListRequest();
//$request->setMerchant($_SESSION['MERCHANT_ID']);
//$request->setReportRequestIdList($reportRequestId);
//$generatedRequestId = invokeGetReportRequestList($service, $request,$reportRequestId, $counter);


/***********************************************************************************************************************************/

/***********************************************************************************************************************************
 * API Request to get Report Data. Input of this API is the output of the "GetReportRequestListRequest" which is Generated Report ID */
$parameters = array(
    'Merchant' => $_SESSION['MERCHANT_ID'],
    'Report' => @fopen('php://memory', 'rw'),
    'ReportId' => $generatedRequestId,
);
$request = new MarketplaceWebService_Model_GetReportRequest($parameters);
invokeGetReport($service, $request);
/************************************************************************************************************************************/

function invokeGetReport(MarketplaceWebService_Interface $service, $request)
{
    try {
        $response = $service->getReport($request);

        if ($response->isSetGetReportResult()) {
            $getReportResult = $response->getGetReportResult();

            if ($getReportResult->isSetContentMd5()) {
                $getReportResult->getContentMd5() . "\n";
            }
        }

        if ($response->isSetResponseMetadata()) {
            $responseMetadata = $response->getResponseMetadata();
            if ($responseMetadata->isSetRequestId()) {
                $responseMetadata->getRequestId() . "\n";
            }
        }

        $dataString = str_replace(',', ' ', stream_get_contents($request->getReport()) . "\n");
        $headerArray = [];
        $dataArray = [];
        $combArray = [];
        $lines = explode(PHP_EOL, $dataString);

        $l = 0;
        $c = 0;

        foreach ($lines as $line) {
            if ($c == 0) {
                $myHeader[$l] = explode("\t", trim($line));
                $c++;
                continue;
            }
            $myArray[$l] = explode("\t", $line);
            $combArray[$c] = array_combine($myHeader[0], $myArray[$l]);
            $l++;
            $c++;
        }

        foreach ($combArray as $combineArray) {
            if($combineArray['order-id'] != '') {
                $hawb = $combineArray['order-id'];
            }
            $marketPlaceOrderFilter = new marketPlaceOrderFilter();
            $marketPlaceOrderFilter->addFieldFilter('    marketplace_order_number', $hawb);
            $ExistingRecord = $marketPlaceOrderFilter->getColumnList('id');
            
            if(count($ExistingRecord)>=1)
            {
                continue;
            }
                    
            $marketPlaceOrder = new MarketPlaceOrder();
            
            if ($combineArray['buyer-name'] != '') {
                $marketPlaceOrder->setReceiverName($combineArray['buyer-name']);
            }

            if ($combineArray['buyer-phone-number'] != '') {
                $marketPlaceOrder->setReceiverPhone($combineArray['buyer-phone-number']);
            }

            if ($combineArray['ship-state'] != '') {
                $marketPlaceOrder->setReceiverState($combineArray['ship-state']);
            }

            if ($combineArray['ship-city'] != '') {
                $marketPlaceOrder->setReceiverCity($combineArray['ship-city']);
            }

            if ($combineArray['ship-country'] != '') {
                $countryFilter = new CountryFilter();
                $countryFilter->addFieldFilter("iso", trim($combineArray['ship-country']));
                $countryId = $countryFilter->getColumnList("id");
                $marketPlaceOrder->setReceiverCountryId($countryId[0]->getId());

                $marketPlaceOrder->setUserId($_SESSION['userId']);
                $marketPlaceOrder->setMarketPlaceId($_POST["marketPlace_id"]);
            }

            if ($combineArray['ship-address-1'] != '') {
                $addressLine1 = trim($combineArray['ship-address-1']);
                if (strlen($addressLine1) > 30) {
                    $addressLine1substr = substr($addressLine1, 0, 29);
                    $addressLine1substr1 = substr($addressLine1, 30, strlen($addressLine1));
                    $marketPlaceOrder->setReceiverAddressLine1($addressLine1substr);
                    //die;
                } else {
                    $marketPlaceOrder->setReceiverAddressLine1($addressLine1);
                }
            }

            if ($combineArray['ship-address-2'] != '') {
                $marketPlaceOrder->setReceiverAddressLine2(substr($addressLine1substr1 . $combineArray['ship-address-2'], 0, 29));
                //.$combineArray['ship-address-3'];
            }

            if ($combineArray['ship-postal-code'] != '') {
                $marketPlaceOrder->setReceiverPostCode($combineArray['ship-postal-code']);
            }

            if ($combineArray['item-price'] != '') {
                $marketPlaceOrder->setOrderTotal($combineArray['item-price']);
            }

            if ($combineArray['ship-postal-code'] != '') {
                $marketPlaceOrder->setReceiverEmail($combineArray['buyer-email']);
            }

            if ($combineArray['order-id'] != '') {
                $marketPlaceOrder->setMarketPlaceOrderNumber($combineArray['order-id']);
                //$currentDate  = new DateTime(date('Y-m-d H:i:s'));    // new line of code  
                //$currentDateTime = $currentDate->format('Y-m-d H:i:s'); 
                //date('Y-m-d H:i:s',strtotime($combineArray['purchase-date']));
                $marketPlaceOrder->setCreateTime(date('Y-m-d H:i:s',strtotime($combineArray['purchase-date'])));
                // $marketPlaceOrder->setOrderStatus(''); // need to ask bcz we don't get it in response
                $marketPlaceOrder->setOrderStatus('Unshipped');
                $marketPlaceOrder->save();
            }

            $marketPlaceOrderId = $marketPlaceOrder->getId();
            $marketPlaceOrderDetails = new MarketPlaceOrderDetails();
            $marketPlaceOrderDetails->setMarketPlaceOrderId($marketPlaceOrderId);
            $marketPlaceOrderDetails->setMarketPlaceItemId($combineArray['order-item-id']);
            $marketPlaceOrderDetails->setSku($combineArray['sku']);
            $marketPlaceOrderDetails->setTitle($combineArray['product-name']);
            $marketPlaceOrderDetails->setQuantityPurchased($combineArray['quantity-purchased']);
            $marketPlaceOrderDetails->setItemPrice($combineArray['item-price']); //need to ask bcz we are not getting it in API call 
            $marketPlaceOrderDetails->setCurrency($combineArray['currency']);
            if ($marketPlaceOrderId != '') {
                $marketPlaceOrderDetails->save();
            } //break;
        }
        $output["STATUS"] = "SUCCESS";
        $output["MESSAGE"] = "All Orders has been fetched successfully.";
        echo json_encode($output, true);
        die;
    } catch (MarketplaceWebService_Exception $ex) {
        $output["STATUS"] = "ERROR";
        $output["MESSAGE"] = $ex->getMessage();
        echo json_encode($output, true);
        die;
    }
}

/*function invokeGetReportRequestList(MarketplaceWebService_Interface $service, $request, $reportRequestIdAPI, $counter) 
{
    try {
            $response = $service->getReportRequestList($request);
            
            if ($response->isSetGetReportRequestListResult()) 
            { 
                $getReportRequestListResult = $response->getGetReportRequestListResult();
                if ($getReportRequestListResult->isSetNextToken()) 
                {
                    $getReportRequestListResult->getNextToken();
                }
                
                if ($getReportRequestListResult->isSetHasNext()) 
                {                    
                    $getReportRequestListResult->getHasNext();
                }
                
                $reportRequestInfoList = $getReportRequestListResult->getReportRequestInfoList();
                $reportRequestInfo = $reportRequestInfoList[0];
                print_r($reportRequestInfo);
                
                if ($reportRequestInfo->isSetReportRequestId()) 
                {                        
                    $reportRequestId = $reportRequestInfo->getReportRequestId();
                    if($reportRequestIdAPI != $reportRequestId)
                    {
                        echo 'report ID Not Equal'; 
                        $counter++;
                        sleep(20);
                        if($counter == 3)
                        {
                            $output["STATUS"] = "ERROR";
                            $output["MESSAGE"] = "Too many requests";
                            echo json_encode($output, true); 
                            die;
                        }
                        else
                        {
                            invokeGetReportRequestList($service, $request,$reportRequestIdAPI, $counter);                       
                        }
                    }
                    else
                    {
                        if ($reportRequestInfo->isSetReportProcessingStatus()) 
                        {
                            $status = $reportRequestInfo->getReportProcessingStatus();
                            if($status == "_SUBMITTED_" || $status == "_IN_PROGRESS_")
                            {
                                sleep(5);
                                invokeGetReportRequestList($service, $request,$reportRequestIdAPI, $counter);
                            }  
                            else 
                            if($status == "_CANCELLED_")
                            {
                                $output["STATUS"] = "ERROR";
                                $output["MESSAGE"] = "Report Has been Cancelled";
                                echo json_encode($output); 
                                die;
                            }
                            else
                            if($status == "_DONE_NO_DATA_")
                            {
                                $output["STATUS"] = "SUCCESS";
                                $output["MESSAGE"] = "All Order has been fetched successfully";
                                echo json_encode($output); 
                                die;
                            }
                            else
                            if($status == "_DONE_")
                            {
                                if ($reportRequestInfo->isSetGeneratedReportId()) 
                                {
                                   $generatedRequestId = $reportRequestInfo->getGeneratedReportId();                                    
                                  // return $generatedRequestId;
                                }
                            }
                        }                            
                    }
                }
                    
                if ($reportRequestInfo->isSetGeneratedReportId()) 
                {
                    $generatedRequestId = $reportRequestInfo->getGeneratedReportId();
                    //break;
                }
                   
                if ($reportRequestInfo->isSetStartDate()) 
                {
                   $reportRequestInfo->getStartDate()->format(DATE_ISO8601);                        
                }

                if ($reportRequestInfo->isSetEndDate()) 
                {
                   $reportRequestInfo->getEndDate()->format(DATE_ISO8601); //die;                        
                }
                    
                if ($reportRequestInfo->isSetReportType()) 
                {
                    $reportRequestInfo->getReportType();
                }

                if ($reportRequestInfo->isSetSubmittedDate()) 
                {
                    $reportRequestInfo->getSubmittedDate()->format(DATE_ISO8601);
                }                               
            } 
            if ($response->isSetResponseMetadata()) 
            { 
                $responseMetadata = $response->getResponseMetadata();
                if ($responseMetadata->isSetRequestId()) 
                {
                    $responseMetadata->getRequestId();
                }
            } 
            //echo $generatedRequestId;
            return $generatedRequestId; 
            
     } catch (MarketplaceWebService_Exception $ex) {
        $output["STATUS"] = "ERROR";
        $output["MESSAGE"] = $ex->getMessage();
        echo json_encode($output); 
        die;
     }
 }
 */
function invokeRequestReport(MarketplaceWebService_Interface $service, $request)
{
    try {
        $response = $service->requestReport($request);

        if ($response->isSetRequestReportResult()) {
            $requestReportResult = $response->getRequestReportResult();
            if ($requestReportResult->isSetReportRequestInfo()) {
                $reportRequestInfo = $requestReportResult->getReportRequestInfo();

                if ($reportRequestInfo->isSetReportRequestId()) {
                    $reportRequestId = $reportRequestInfo->getReportRequestId();
                }
                if ($reportRequestInfo->isSetReportType()) {
                    $reportRequestType = $reportRequestInfo->getReportType();
                }
                if ($reportRequestInfo->isSetStartDate()) {
                    $startDate = $reportRequestInfo->getStartDate()->format(DATE_ISO8601);
                }
                if ($reportRequestInfo->isSetEndDate()) {
                    $endDate = $reportRequestInfo->getEndDate()->format(DATE_ISO8601);
                }
                if ($reportRequestInfo->isSetSubmittedDate()) {
                    $submittedDate = $reportRequestInfo->getSubmittedDate()->format(DATE_ISO8601);
                }
                if ($reportRequestInfo->isSetReportProcessingStatus()) {
                    $processingStatus = $reportRequestInfo->getReportProcessingStatus();
                }
            }
        }
        if ($response->isSetResponseMetadata()) {
            $responseMetadata = $response->getResponseMetadata();
            if ($responseMetadata->isSetRequestId()) {
                $requestId = $responseMetadata->getRequestId();
            }
        }
        $response->getResponseHeaderMetadata();
        return $reportRequestId;
    } catch (MarketplaceWebService_Exception $ex) {
        $output["STATUS"] = "ERROR";
        $output["MESSAGE"] = $ex->getMessage();
        echo json_encode($output);
        die;
    }
}

use Aws\Sqs\SqsClient;

function getReportResponseAws($reportId)
{
    //sleep(2);
    require_once '../includes/3rdparty/AmazonAws/aws-autoloader.php';
    $queueUrl = "https://sqs.us-east-2.amazonaws.com/095611935099/order_notification";
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
    //print_r($result->get('Messages'));
    if (count($result->get('Messages')) == 0) {
        return getReportResponseAws($reportId);
    }

    foreach ($result->get('Messages') as $message) {
        $xml = $message['Body'];
        $statusArray = simplexml_load_string($xml);
        $reportRequestIdFromNotification = $statusArray->NotificationPayload->ReportProcessingFinishedNotification->ReportRequestId;

        if ($reportRequestIdFromNotification == '') {
            return getReportResponseAws($reportId);
        } elseif ($reportRequestIdFromNotification != $reportId) {

            if ($result = $client->deleteMessage([
                'QueueUrl' => $queueUrl, // REQUIRED
                'ReceiptHandle' => $message['ReceiptHandle'] // REQUIRED
            ]))

                return getReportResponseAws($reportId);
        } elseif ($reportRequestIdFromNotification == $reportId) {
            $generatedRequestId = $statusArray->NotificationPayload->ReportProcessingFinishedNotification->ReportId;
            $reportStatus = $statusArray->NotificationPayload->ReportProcessingFinishedNotification->ReportProcessingStatus;
            if ($reportStatus == 'DONE_NO_DATA') {
                $output["STATUS"] = "SUCCESS";
                $output["MESSAGE"] = "Unshipped Parcels are not available";
                echo json_encode($output, true);
                die;
            }
            return $generatedRequestId;
        }
    }
    die;
}

?>

                                                                                
