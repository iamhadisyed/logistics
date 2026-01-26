<?php
require_once("../includes/settings/config.inc.php");
require_once('../includes/autoload/MarketplaceWebService/Client.php');
//require_once('../includes/autoload/MarketplaceWebService/Interface.php');
require_once('../includes/autoload/MarketplaceWebService/Model.php');
require_once('../includes/autoload/MarketplaceWebService/ModelRequest.php');
require_once('../includes/autoload/MarketplaceWebService/ModelResponse.php');
require_once('../includes/autoload/MarketplaceWebService/Model/ContentType.php');
require_once('../includes/autoload/MarketplaceWebService/Model/IdList.php');
require_once('../includes/autoload/MarketplaceWebService/Model/SubmitFeedRequest.php');
require_once('../includes/autoload/MarketplaceWebService/Model/SubmitFeedResponse.php');
require_once('../includes/autoload/MarketplaceWebService/Model/SubmitFeedResult.php');
require_once('../includes/autoload/MarketplaceWebService/Model/FeedSubmissionInfo.php');
require_once('../includes/autoload/MarketplaceWebService/Model/ResponseMetadata.php');
require_once('../includes/mapping/marketplaceorderfilter.class.php');
require_once('../includes/mapping/marketplaceorder.class.php');
require_once('../includes/mapping/usermarketplacesmappingfilter.class.php');
require_once('../includes/mapping/usermarketplacesmapping.class.php');
require_once('../includes/mapping/consignmentfilter.class.php');
require_once('../includes/mapping/consignment.class.php');
require_once('../includes/mapping/carrier.class.php');
require_once('../includes/mapping/services.class.php');

if($_POST['dispatchArray'] != '')
{
    $orderReferenceArray = json_decode(stripslashes($_POST['dispatchArray']));
    $marketPlaceOrderFilter = new MarketPlaceOrderFilter();
    $marketPlaceOrderFilter->addFieldFilter('   marketplace_order_number',$orderReferenceArray[0]);
    $result = $marketPlaceOrderFilter->getColumnList('*');
    if(count($result > 0))
    {
        $marketPlaceId = $result[0]->getMarketPlaceId(); // MarketPlace id like 1 is for amazon
    }
}
else 
{
    $hawb               =   $_POST['hawb'];
    $marketPlaceObjId   =   $_POST['marketPlaceId'];
    $marketPlaceObj     =   new MarketPlaceOrder($marketPlaceObjId);
    $marketPlaceId      =   $marketPlaceObj->getMarketPlaceId();
    $orderReferenceArray[] = $hawb;
}


$user               =   SessionManager::getUser();
$_SESSION['userId'] =   $user->getUserAccountId();

$userMarketPlaceMappingFilter = new UserMarketPlacesMappingFilter();
$userMarketPlaceMappingFilter->addFieldFilter("user_account_id", $_SESSION['userId']);
$userMarketPlaceMappingFilter->addFieldFilter("market_places_id", $marketPlaceId);
$result = $userMarketPlaceMappingFilter->getList();

if(count($result)> 0)
{
    $authdata = json_decode($result[0]->getAuthData());
    $_SESSION['AWS_ACCESS_KEY_ID'] = $authdata->AWS_ACCESS_KEY_ID;
    $_SESSION['AWS_SECRET_ACCESS_KEY'] = $authdata->AWS_SECRET_ACCESS_KEY;
    $_SESSION['MERCHANT_ID'] = $authdata->MERCHANT_ID;
    $_SESSION['MARKETPLACE_ID'] = $authdata->MARKETPLACE_ID;
    $_SESSION['STORE_KEY'] = $result[0]->getStoreKey();
}
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

$config = array (
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

   $feed = '<?xml version="1.0" encoding="UTF-8"?>
    <AmazonEnvelope xsi:noNamespaceSchemaLocation="amzn-envelope.xsd" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
        <Header>
            <DocumentVersion>1.01</DocumentVersion>
            <MerchantIdentifier>AYHIDCM8MEZBT</MerchantIdentifier>
        </Header>
        <MessageType>OrderFulfillment</MessageType>';
  $count = 1;
foreach ($orderReferenceArray as $hawb)
{
    $congignmentCheck	=	new ConsignmentFilter();
    $congignmentCheck->addHawbFilter($hawb);
    if( $congignmentCheck->getCount()>0)
    {
	$rowlist	=	$congignmentCheck->getColumnList('*');
	$rowlistData	=	$rowlist[0];
    }

    else
    {
        $notFoundShipmentArray[] = $hawb;
        continue;
    }

    $storeName		=	'OneWorldExpress';
    $awb			=	$rowlistData->getAwb();//'OneWorldExpress';
    $id			=	$rowlistData->getId();//'OneWorldExpress';
    $serviceId              =       $rowlistData->getServiceId();
    $serviceObj             =       new Services($serviceId);
    $carrierId              =       $serviceObj->getCarrierId();
    $carrierObj             =       new Carrier($carrierId);
    $carrierName            =       $carrierObj->getCarrierDisplayName();
    $consignmentId          =       $rowlistData->getId();
    
    $dispatch_date=  date('Y-m-d\Th:i:s\Z'); 
    $feed .='<Message>
             <MessageID>'.$count.'</MessageID>
            <OperationType>Update</OperationType>
            <OrderFulfillment>
                <AmazonOrderID>'.trim($hawb).'</AmazonOrderID>           
                        <FulfillmentDate>'.$dispatch_date.'</FulfillmentDate>
                <FulfillmentData>
                    <CarrierName>'.$carrierName.'</CarrierName>
                    <ShippingMethod>Standard</ShippingMethod>
                                    <ShipperTrackingNumber>'.trim($awb).'</ShipperTrackingNumber>
                    </FulfillmentData>           
            </OrderFulfillment>
        </Message>';
    $count++;
}
    $feed .='</AmazonEnvelope>';
    //echo $feed;
    
    $marketplaceIdArray = array("Id" => $_SESSION['MARKETPLACE_ID']);
    $feedHandle = @fopen('php://temp', 'rw+');
    fwrite($feedHandle, $feed);
    rewind($feedHandle);
    $parameters = array (
      'Merchant' => $_SESSION['MERCHANT_ID'],
      'MarketplaceIdList' => $marketplaceIdArray,
      'FeedType' => '_POST_ORDER_FULFILLMENT_DATA_',
      'FeedContent' => $feedHandle,
      'PurgeAndReplace' => false,
      'ContentMd5' => base64_encode(md5(stream_get_contents($feedHandle), true)),
    );
    rewind($feedHandle);

    $request = new MarketplaceWebService_Model_SubmitFeedRequest($parameters);
    $resultValue = invokeSubmitFeed($service, $request, $orderReferenceArray);
/*    
    if($resultValue == 'SUCCESS')
    {
        $successArray[] = $hawb;
    }
    else
    {
        $errorArray[] = $hawb; 
    }
//}

if(count($successArray) > 0)
{
    $outputtemp["STATUS"] = "SUCCESS";
    foreach($successArray as $successhawb)
    {
        $successMessageArray[] = "Your order ".$successhawb." has been dispatched successfully";
    }
    $outputtemp["MESSAGE"] = implode(',', $successMessageArray);
    $output[] = $outputtemp;
}

if(count($errorArray) >0)
{
    $outputtemp["STATUS"] = "ERROR";
    foreach($errorArray as $errorHawb)
    {
        $outputtemp["MESSAGE"] = $errorhawb." has not been dispatched";
    }
    $output[] = $outputtemp;
}

if(count($notFoundShipmentArray) > 0)
{
    $outputtemp["STATUS"] = "ERROR";
    foreach($notFoundShipmentArray as $notFoundHawb)
    {
        $outputtemp["MESSAGE"] = $notFoundHawb." not Found";
    }    
    $output[] = $outputtemp;
}

echo json_encode($output);
die;
*/
function invokeSubmitFeed(MarketplaceWebService_Interface $service, $request, $orderReferenceArray) 
  {
    try {
	    $response = $service->submitFeed($request);
            //print_r($response); die;
            if ($response->isSetSubmitFeedResult()) 
            { 
		$submitFeedResult = $response->getSubmitFeedResult();
                if ($submitFeedResult->isSetFeedSubmissionInfo()) 
		{ 
                    $feedSubmissionInfo = $submitFeedResult->getFeedSubmissionInfo();
                    if ($feedSubmissionInfo->isSetFeedProcessingStatus() && $feedSubmissionInfo->isSetFeedProcessingStatus() == '_SUBMITTED_') 
                    {
                        foreach($orderReferenceArray as $hawb)
                        {
                            $consignmentFilter = new ConsignmentFilter();
                            $consignmentFilter->addFieldFilter('    hawb', $hawb);
                            $result = $consignmentFilter->getColumnList('*');
                            if(count($result) >0 )
                            {
                                //$consignmentObj = new Consignment($result[0]->getId());
                                //$consignmentObj->setShipmentStatus(Consignment::STATUS_DISPATCHED);
                                //$consignmentObj->save();  
                                
                                $marketplaceOrderFilter = new MarketPlaceOrderFilter();
                                $marketplaceOrderFilter->addFieldFilter('    marketplace_order_number',$hawb);
                                $res = $marketplaceOrderFilter->getColumnList('*');
                                $marketPlaceObj = new MarketPlaceOrder($res[0]->getId());
                                $marketPlaceObj->setOrderStatus('Shipped');
                                $marketPlaceObj->setShippedDate(date('Y-m-d H:i:s')); // need to change this with shipped_date column
                                $marketPlaceObj->save();
                                
                                $getOrderApiUrl = API2CART_URL.'order.update.json?api_key=' . API2CART_API_KEY . '&store_key=' . $_SESSION['STORE_KEY'] . '&date_finished=' .date("Y-m-d H:i:s", time()).'&order_id='.$marketPlaceObj->getMarketPlaceOrderNumber().'&order_status=Intransit&comment=Order comment&params=force_all';
                                $resultData = json_decode(file_get_contents($getOrderApiUrl), true);                                
                            }
                            else 
                            {
                                $notFoundArray[] = $hawb;
                            }
                        }
                        if(count($notFoundArray) >0 )
                        {
                            $message = count($notFoundArray).' Shipment/s not found. Remaining are dispatched';
                        }
                        else
                        {
                            $message = 'Shipment/s have been dispatched';                           
                        }
                        $output["STATUS"] = "SUCCESS";
                        $output["MESSAGE"] = $message;
                        echo json_encode($output);
                        die;
                    }                   
            	} 
	    } 
            else
            {
                $output["STATUS"] = "ERROR";
                $output["MESSAGE"] = "We are unable to submit information on amazon";             
                echo json_encode($output);
                die;
            }
       } 
    catch (MarketplaceWebService_Exception $ex) 
    {
        $output["STATUS"] = "ERROR";
        $output["MESSAGE"] = "We are unable to submit information on amazon";             
        echo json_encode($output);
        die;
    }
 }
                                                                
