<?php
require_once("../includes/settings/config.inc.php");

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
    'product.class',
    'productfilter.class'
]);

include_classes([
    'Client',
    'Model',
        ], 'autoload/MarketplaceWebService');

include_classes([
    'IdList',
    'RequestReportRequest',
    'GetReportListRequest',
    'GetReportRequestListRequest',
    'GetReportRequest',
    'SubmitFeedRequest',
    'GetFeedSubmissionResultRequest',
    'GetFeedSubmissionResultResponse',
        ], 'autoload/MarketplaceWebService/Model');

class Amazon
{
    public $userMarketPlaceMappingObj;
    public $service;
    public $config; 
    public $serviceUrl; 
    public $marketplaceIdArray;
    
    public function __construct($userMarketPlaceMappingObj) 
    {
        $this->userMarketPlaceMappingObj = $userMarketPlaceMappingObj;
        
        $authData = json_decode($this->userMarketPlaceMappingObj->getAuthData());
        
        $_SESSION['MERCHANT_ID'] = $authData->MERCHANT_ID;
        $_SESSION['MARKETPLACE_MWSAUTH'] = $authData->MARKETPLACE_MWSAUTH;
        $this->serviceUrl = "https://mws.amazonservices.co.uk";
        
        $this->config = array(
            'ServiceURL' => $this->serviceUrl,
            'ProxyHost' => null,
            'ProxyPort' => -1,
            'MaxErrorRetry' => 3,
        );

        $this->service = new MarketplaceWebService_Client(
            'AKIAJBUWT3ZBRDV3QITA',//$_SESSION['AWS_ACCESS_KEY_ID'],
            '6y9yurr8KHXaj9Rvt83ACZyZYw2gamkvpXtu1tIe',//$_SESSION['AWS_SECRET_ACCESS_KEY'],
            $this->config,
            'oneworld',
            '2');
        $this->marketplaceIdArray = array("Id" => array('A1F83G8C2ARO7P','A13V1IB3VIYZZH','APJ6JRA9NG5V4','A1805IZSGTT6HS','A1RKKUPIHCS9HS','A1PA6795UKMFR9','A1C3SOZRARQ6R3'));
    }
    
    public function fetchOrders()
    {
        $user = SessionManager::getUser();
        $_SESSION['userId'] = $user->getUserAccountId();

        $createAfter = '';
        $createBefore = '';
        date_default_timezone_set('Europe/London');   // new line of code 
        //echo $this->userMarketPlaceMappingObj->getMarketPlacesId(); 
        //echo $this->userMarketPlaceMappingObj->getUserAccountId(); die;
        // getting last sync time
        // Nedd to add limit here. we just need 1 last record
        $marketPlaceOrderFilter = new MarketPlaceOrderFilter();
        $marketPlaceOrderFilter->addFieldFilter("    marketplace_id", $this->userMarketPlaceMappingObj->getMarketPlacesId());
        $marketPlaceOrderFilter->addFieldFilter("    user_id", $this->userMarketPlaceMappingObj->getUserAccountId());
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
            $createdAfter = new DateTime('-50 day');
            $beforeTime = date("Y-m-d H:i:s");
            $createdBefore = new DateTime($beforeTime);
        }

        //print_r($createdAfter); die;
        /**********************************************************************************************************************************
         * API Request to send Report Type and in response we will get Request ID */

        $parameters = array(
            'Merchant' => $_SESSION['MERCHANT_ID'],
            'MarketplaceIdList' => $this->marketplaceIdArray,
            'ReportType' => '_GET_FLAT_FILE_ORDERS_DATA_',
            'MWSAuthToken' => $_SESSION['MARKETPLACE_MWSAUTH'],
           // 'ReportType' => '_GET_FLAT_FILE_ACTIONABLE_ORDER_DATA_',
            'StartDate' => $createdAfter,
            'EndDate' => $createdBefore
        );
        $request = new MarketplaceWebService_Model_RequestReportRequest($parameters);
       // $request->setMWSAuthToken('amzn.mws.46bbc88a-5b2f-253b-027a-084a3d59bd21'); // Optional
        $reportRequestId = $this->invokeRequestReport($this->service, $request);
        
        $generatedRequestId = $this->getReportResponseAws($reportRequestId);
        

        /***********************************************************************************************************************************/

        /***********************************************************************************************************************************
         * API Request to get Generated Report ID. Input of this API is the output of the "RequestReportRequest" which is Request ID        */
        $counter = 0;

        /***********************************************************************************************************************************
         * API Request to get Report Data. Input of this API is the output of the "GetReportRequestListRequest" which is Generated Report ID */
        $parameters = array(
            'Merchant' => $_SESSION['MERCHANT_ID'],
            'Report' => @fopen('php://memory', 'rw'),
            'ReportId' => $generatedRequestId,
            'MWSAuthToken' => $_SESSION['MARKETPLACE_MWSAUTH']
        );
        $request = new MarketplaceWebService_Model_GetReportRequest($parameters);
        $result = $this->invokeGetReport($this->service, $request); 
        return $result;
    }


    function invokeGetReport(MarketplaceWebService_Interface $service, $request)
    {
        date_default_timezone_set('Europe/London');   // new line of code 
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

            foreach ($combArray as $combineArray) 
            {
                if($combineArray['order-id'] != '') 
                {
                    $hawb = $combineArray['order-id'];
                }
                
                if($combineArray['order-item-id'] != '') 
                {
                    $itemId = $combineArray['order-item-id'];
                }
                $marketPlaceOrderFilter = new marketPlaceOrderFilter();
                $marketPlaceOrderFilter->addFieldFilter('    marketplace_order_number', $hawb);
                $rs = $marketPlaceOrderFilter->getColumnList('id');
                
                if(count($rs) >= 1)
                {
                    $marketPlaceOrderDetailsFilter = new marketPlaceOrderDetailsFilter();
                    $marketPlaceOrderDetailsFilter->addFieldFilter('    marketplace_order_id', $rs[0]->getId());
                    $marketPlaceOrderDetailsFilter->addFieldFilter('    marketplace_item_id', $itemId);
                    $ExistingRecord = $marketPlaceOrderDetailsFilter->getColumnList('id');
                    
                    if(count($ExistingRecord) == 0)
                    {
                        $marketPlaceOrderDetails = new MarketPlaceOrderDetails();
                        $marketPlaceOrderDetails->setMarketPlaceOrderId($rs[0]->getId());
                        $marketPlaceOrderDetails->setMarketPlaceItemId($combineArray['order-item-id']);
                        $marketPlaceOrderDetails->setSku($combineArray['sku']);
                        $marketPlaceOrderDetails->setTitle($combineArray['product-name']);
                        $marketPlaceOrderDetails->setQuantityPurchased($combineArray['quantity-purchased']);
                        $marketPlaceOrderDetails->setItemPrice($combineArray['item-price']); //need to ask bcz we are not getting it in API call
                        $marketPlaceOrderDetails->setCurrency($combineArray['currency']);
                        $marketPlaceOrderDetails->save();
                    }
                }
                
                else 
                {
                    $marketPlaceOrder = new MarketPlaceOrder();

                    if ($combineArray['recipient-name'] != '') 
                    {
                        $marketPlaceOrder->setReceiverName($combineArray['recipient-name']);
                    }

                    if ($combineArray['buyer-phone-number'] != '') 
                    {
                        $marketPlaceOrder->setReceiverPhone($combineArray['buyer-phone-number']);
                    }

                    if ($combineArray['ship-state'] != '') 
                    {
                        $marketPlaceOrder->setReceiverState($combineArray['ship-state']);
                    }

                    if ($combineArray['ship-city'] != '') {
                    $marketPlaceOrder->setReceiverCity($combineArray['ship-city']);
                    }

                    if ($combineArray['ship-country'] != '') 
                    {
                        $countryFilter = new CountryFilter();
                        $countryFilter->addFieldFilter("iso", trim($combineArray['ship-country']));
                        $countryId = $countryFilter->getColumnList("id");
                        $marketPlaceOrder->setReceiverCountryId($countryId[0]->getId());

                        $marketPlaceOrder->setUserId($_SESSION['userId']);
                        $marketPlaceOrder->setMarketPlaceId($_POST["marketPlace_id"]);
                    }

                    if ($combineArray['ship-address-1'] != '') 
                    {
                        $addressLine1 = trim($combineArray['ship-address-1']);
                        if (strlen($addressLine1) > 30) 
                        {
                            $addressLine1substr = substr($addressLine1, 0, 29);
                            $addressLine1substr1 = substr($addressLine1, 30, strlen($addressLine1));
                            $marketPlaceOrder->setReceiverAddressLine1($addressLine1substr);
                            //die;
                        } 
                        else 
                        {
                            $marketPlaceOrder->setReceiverAddressLine1($addressLine1);
                        }
                    }

                    if ($combineArray['ship-address-2'] != '') 
                    {
                        $marketPlaceOrder->setReceiverAddressLine2(substr($addressLine1substr1 . $combineArray['ship-address-2'], 0, 29));
                    //.$combineArray['ship-address-3'];
                    }

                    if ($combineArray['ship-postal-code'] != '') 
                    {
                        $marketPlaceOrder->setReceiverPostCode($combineArray['ship-postal-code']);
                    }

                    if ($combineArray['item-price'] != '') 
                    {
                        $marketPlaceOrder->setOrderTotal($combineArray['item-price']);
                    }

                    if ($combineArray['ship-postal-code'] != '') 
                    {
                        $marketPlaceOrder->setReceiverEmail($combineArray['buyer-email']);
                    }

                    if ($combineArray['order-id'] != '') 
                    {
                        $marketPlaceOrder->setMarketPlaceOrderNumber($combineArray['order-id']);
                        $marketPlaceOrder->setCreateTime(date('Y-m-d H:i:s',strtotime($combineArray['purchase-date'])));
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
                    if ($marketPlaceOrderId != '') 
                    {
                        $marketPlaceOrderDetails->save();
                    } //break;
                }
            }
            $output["STATUS"] = "SUCCESS";
            $output["MESSAGE"] = "All Orders has been fetched successfully.";
            return $output;
        } catch (MarketplaceWebService_Exception $ex) {
            $output["STATUS"] = "ERROR";
            $output["MESSAGE"] = $ex->getMessage();
            return $output;
        }
    }

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
        
        if (count($result->get('Messages')) == 0) {
            return $this->getReportResponseAws($reportId);
        }

        foreach ($result->get('Messages') as $message) {
            $xml = $message['Body'];
            $statusArray = simplexml_load_string($xml);
            $reportRequestIdFromNotification = $statusArray->NotificationPayload->ReportProcessingFinishedNotification->ReportRequestId;

            if ($reportRequestIdFromNotification == '') {
                return $this->getReportResponseAws($reportId);
            } elseif ($reportRequestIdFromNotification != $reportId) {

                if ($result = $client->deleteMessage([
                    'QueueUrl' => $queueUrl, // REQUIRED
                    'ReceiptHandle' => $message['ReceiptHandle'] // REQUIRED
                ]))

                    return $this->getReportResponseAws($reportId);
            } elseif ($reportRequestIdFromNotification == $reportId) {
                $generatedRequestId = $statusArray->NotificationPayload->ReportProcessingFinishedNotification->ReportId;
                $reportStatus = $statusArray->NotificationPayload->ReportProcessingFinishedNotification->ReportProcessingStatus;
                if ($reportStatus == 'DONE_NO_DATA') {
                    $output["STATUS"] = "SUCCESS";
                    $output["MESSAGE"] = "Unshipped Parcels are not available";
                    return $output;
                }
              //  echo $generatedRequestId; die;
                return $generatedRequestId;
            }
        }
        return false;
    }

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

    public function dispatchLabel($hawb, $requestType = "normal")
    {
        $feed = '<?xml version="1.0" encoding="UTF-8"?>
        <AmazonEnvelope xsi:noNamespaceSchemaLocation="amzn-envelope.xsd" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
        <Header>
            <DocumentVersion>1.01</DocumentVersion>
            <MerchantIdentifier>AYHIDCM8MEZBT</MerchantIdentifier>
        </Header>
        <MessageType>OrderFulfillment</MessageType>';
        $count = 1;
        if(is_array($hawb))
        {
            $orderReferenceArray = $hawb;
        }
        else 
        {
            $orderReferenceArray[] = $hawb;
        }
        
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
       
        $marketplaceIdArray = array("Id" => array('A1F83G8C2ARO7P'));
        
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
          'MWSAuthToken' => $_SESSION['MARKETPLACE_MWSAUTH'],          
        );
        rewind($feedHandle);
        
        $request = new MarketplaceWebService_Model_SubmitFeedRequest($parameters);
        
        $resultValue = $this->invokeSubmitFeed($this->service, $request, $orderReferenceArray, $requestType);
        return $resultValue;
    }
    
    public function productListingApi($productIdArray, $categoryIdArray, $requestType= '')
    {
        
        $feed = '<?xml version="1.0" ?>
                <AmazonEnvelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="amznenvelope.xsd">
                <Header>
                    <DocumentVersion>1.01</DocumentVersion>
                    <MerchantIdentifier>'.$_SESSION['MERCHANT_ID'].'</MerchantIdentifier>
                </Header>
                
                <MessageType>Product</MessageType>
                <PurgeAndReplace>false</PurgeAndReplace>';  
                
                $counter = 1;
                foreach($productIdArray as $productId)
                {
                    $productFilter = new ProductFilter();
                    $productFilter->addFilter("id ='".$productId."'");
                    $productResult = $productFilter->getList();  
                    $productResult = $productResult[0];
                    // RecommendedBrowseNode is category ID
                    $feed .= '<Message>
                               <MessageID>'.$counter.'</MessageID>
                               <OperationType>Update</OperationType>
                               <Product>
                               <SKU>'.$productResult->getSku().'</SKU>
                               <StandardProductID>
                                   <Type>ASIN</Type>
                                   <Value>'.$productResult->getASIN().'</Value>
                               </StandardProductID>
                               <LaunchDate>'.date('Y-m-d\Th:i:s\Z').'</LaunchDate>
                               <DescriptionData>
                                   <Title>'.$productResult->getProductName().'</Title>';
                    
                                    foreach($categoryIdArray as $nodeId)
                                    {                                        
                                    $feed .= '<RecommendedBrowseNode>'.$nodeId.'</RecommendedBrowseNode>';                                    
                                    }
                               $feed .= '</DescriptionData>              
                               </Product>
                            </Message>';
                    $counter++;
                }
        $feed .= '</AmazonEnvelope>';
        
        $feedHandle = @fopen('php://temp', 'rw+');
        fwrite($feedHandle, $feed);
        rewind($feedHandle);
        $parameters = array (
          'Merchant' => $_SESSION['MERCHANT_ID'],
          'MarketplaceIdList' => array("Id" => array('A1F83G8C2ARO7P')),
          'FeedType' => '_POST_PRODUCT_DATA_',
          'FeedContent' => $feedHandle,
          'PurgeAndReplace' => false,
          'ContentMd5' => base64_encode(md5(stream_get_contents($feedHandle), true)),
        );
        rewind($feedHandle);

        $request = new MarketplaceWebService_Model_SubmitFeedRequest($parameters);
        $resultValue = $this->invokeSubmitFeedListing($this->service, $request,'','');
        return $resultValue;
    }
    
    public function priceListingApi($productIdArray, $requestType = '')
    {
        $feed = '<?xml version="1.0" encoding="utf-8"?>
                <AmazonEnvelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="amznenvelope.xsd">
                <Header>
                <DocumentVersion>1.01</DocumentVersion>
                <MerchantIdentifier>'.$_SESSION['MERCHANT_ID'].'</MerchantIdentifier>
                </Header>
                <MessageType>Price</MessageType>';
        $counter = 1;
        foreach($productIdArray as $productId)
        {
            $productFilter = new ProductFilter();
            $productFilter->addFilter("id ='".$productId."'");
            $productResult = $productFilter->getList();  
            $productResult = $productResult[0];

            $feed.='<Message>
                        <MessageID>'.$counter.'</MessageID>
                        <Price>
                        <SKU>'.$productResult->getSku().'</SKU>
                        <StandardPrice currency="GBP">'.$productResult->getPrice().'</StandardPrice> 
                        </Price>
                    </Message>';
            $counter++;
        }
        $feed .= '</AmazonEnvelope>';
        
        $feedHandle = @fopen('php://temp', 'rw+');
        fwrite($feedHandle, $feed);
        rewind($feedHandle);
        $parameters = array (
            'Merchant' => $_SESSION['MERCHANT_ID'],
            'MarketplaceIdList' => array("Id" => array('A1F83G8C2ARO7P')),
            'FeedType' => '_POST_PRODUCT_PRICING_DATA_',
            'FeedContent' => $feedHandle,
            'PurgeAndReplace' => false,
            'ContentMd5' => base64_encode(md5(stream_get_contents($feedHandle), true)),
            );
        rewind($feedHandle);

        $request = new MarketplaceWebService_Model_SubmitFeedRequest($parameters);
        $resultValue = $this->invokeSubmitFeedListing($this->service, $request,'','');
        return $resultValue;
    }
    
    public function inventoryListingApi($productIdArray, $requestType='')
    {
        $feed = '<?xml version="1.0" encoding="utf-8" ?>
                <AmazonEnvelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="amzn-envelope.xsd">
                <Header>
                <DocumentVersion>1.01</DocumentVersion>
                <MerchantIdentifier>AYHIDCM8MEZBT</MerchantIdentifier>
                </Header>
                <MessageType>Inventory</MessageType>';
        $counter = 1;
        foreach($productIdArray as $productId)
        {
            $productFilter = new ProductFilter();
            $productFilter->addFilter("id ='".$productId."'");
            $productResult = $productFilter->getList();  
            $productResult = $productResult[0];
            
            $feed .= '<Message>
                <MessageID>'.$counter.'</MessageID>
                <OperationType>Update</OperationType>
                <Inventory>
                <SKU>'.$productResult->getSku().'</SKU>
                <Quantity>'.$productResult->getQuantity().'</Quantity>
                <FulfillmentLatency>1</FulfillmentLatency>
                </Inventory>
                </Message>';                
            $counter++;
        }
        
        $feed .= '</AmazonEnvelope>';
       // print_r($feed); die;
        $feedHandle = @fopen('php://temp', 'rw+');
        fwrite($feedHandle, $feed);
        rewind($feedHandle);
        $parameters = array (
            'Merchant' => 'A3LX344APRTG2Z',
            'MarketplaceIdList' => $marketplaceIdArray,
            'FeedType' => '_POST_INVENTORY_AVAILABILITY_DATA_',
            'FeedContent' => $feedHandle,
            'PurgeAndReplace' => false,
            'ContentMd5' => base64_encode(md5(stream_get_contents($feedHandle), true)),
            );
        rewind($feedHandle);

        $request = new MarketplaceWebService_Model_SubmitFeedRequest($parameters);
        $resultValue = $this->invokeSubmitFeedListing($this->service, $request,'','');
        return $resultValue;        
    }
    
    public function imageListingApi(MarketplaceWebService_Interface $service, $request, $orderReferenceArray, $requestType)
    {
        
    }
    
    public function relationshipApi(MarketplaceWebService_Interface $service, $request, $orderReferenceArray, $requestType)
    {
        
    }
    
    public function invokeSubmitFeed(MarketplaceWebService_Interface $service, $request, $orderReferenceArray, $requestType="")
    {
        try {
	    $response = $service->submitFeed($request);
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
                                $marketplaceOrderFilter = new MarketPlaceOrderFilter();
                                $marketplaceOrderFilter->addFieldFilter('    marketplace_order_number',$hawb);
                                $res = $marketplaceOrderFilter->getColumnList('*');
                                $marketPlaceObj = new MarketPlaceOrder($res[0]->getId());
                                $marketPlaceObj->setOrderStatus('Shipped');
                                $marketPlaceObj->setShippedDate(date('Y-m-d H:i:s')); // need to change this with shipped_date column
                                $marketPlaceObj->save();
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
                        if($requestType == 'api') {
                            return $output;
                        } else {
                            echo json_encode($output);
                            die;
                        }
                    }                   
            	} 
	    } 
            else
            {
                $output["STATUS"] = "ERROR";
                $output["MESSAGE"] = "We are unable to submit information on amazon";
                if($requestType == 'api') {
                    return $output;
                } else {
                    echo json_encode($output);
                    die;
                }
            }
        } 
        catch (MarketplaceWebService_Exception $ex) 
        {
            $output["STATUS"] = "ERROR";
            $output["MESSAGE"] = "We are unable to submit information on amazon".$ex;
			if($requestType == 'api') {
				return $output;
			} else {
				echo json_encode($output);
				die;
			}
        }
    }
    
    public function invokeSubmitFeedListing(MarketplaceWebService_Interface $service, $request, $orderReferenceArray, $requestType)
    {
        try {
                $response = $service->submitFeed($request);
                if ($response->isSetSubmitFeedResult()) 
                { 
                    $submitFeedResult = $response->getSubmitFeedResult();
                    if ($submitFeedResult->isSetFeedSubmissionInfo()) 
                    { 
                        $feedSubmissionInfo = $submitFeedResult->getFeedSubmissionInfo();
                        if ($feedSubmissionInfo->isSetFeedProcessingStatus() && $feedSubmissionInfo->isSetFeedProcessingStatus() == '_SUBMITTED_') 
                        {
                            $output["STATUS"] = "SUCCESS";
                            $output["MESSAGE"] = $feedSubmissionInfo->feedSubmissionId;
                            return $output;                        
                        }                   
                    } 
                } 
                else
                {
                    $output["STATUS"] = "ERROR";
                    $output["MESSAGE"] = "We are unable to submit information on amazon";
                    return $output;              
                }
            } 
            catch (MarketplaceWebService_Exception $ex) 
            {
                $output["STATUS"] = "ERROR";
                $output["MESSAGE"] = $ex->getMessage();             
                return $output;
            }
    }
    
    public function invokeGetFeedSubmissionResult($feedId) 
    {
        $filename = 'file.xml';
        $parameters = array (
          'Marketplace' => 'A1F83G8C2ARO7P', 
          'Merchant' => $_SESSION['MERCHANT_ID'],
          'FeedSubmissionId' => $feedId,
          'FeedSubmissionResult' => @fopen('file.xml', 'w+'),
        );

        $request = new MarketplaceWebService_Model_GetFeedSubmissionResultRequest($parameters);

        try 
        {
            $result = $this->service->getFeedSubmissionResult($request);
            $response = file_get_contents('file.xml');
            @unlink('file.xml');
            $xml = new SimpleXMLElement($response);
           
            $errorResponse = $xml->Message->ProcessingReport->ProcessingSummary->MessagesWithError;
            $warningResponse = $xml->Message->ProcessingReport->ProcessingSummary->MessagesWithWarning;   
            if($errorResponse != '0' || $warningResponse != '0')
            {
                $errorMessage = $xml->Message->ProcessingReport->Result->ResultDescription; 
                $output['STATUS'] = 'ERROR';
                $output['MESSAGE'] = $errorMessage;
            }
            else
            {
                $output['STATUS'] = 'SUCCESS';
                $output['MESSAGE'] = 'SUCCESS';
            }
        }

        catch (MarketplaceWebService_Exception $ex) 
        {            
            $output['STATUS'] = 'ERROR';
            $output['MESSAGE'] = $ex->getMessage();
        }        
        return $output;
    }
//}
    
    
}
