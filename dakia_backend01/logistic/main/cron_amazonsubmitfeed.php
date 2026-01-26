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
//require_once('../includes/autoload/MarketplaceWebService/Model/GetFeedSubmissionResultRequest.php');


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
    'products.class',
    'amazonproductapiresultfilter.class',
    'amazonproductapiresult.class',
    'amazon.class', 
    'productmarketplacemapping.class',
    'productmarketplacemappingfilter.class'
]);

DbAccess3::connect(SETTING_DB_SERVER, SETTING_DB_USER, SETTING_DB_PASSWORD, SETTING_DB_DATABASE);
$AmazonProductApiResultFilter = new AmazonProductApiResultFilter();
$AmazonProductApiResultFilter->addFilter("    feed_type = 'Product'");
$AmazonProductApiResultFilter->addFilter("feed_status = 'SUBMITTED'");
$submittedProductList = $AmazonProductApiResultFilter->getList();
if(count($submittedProductList) >0)
{
    foreach($submittedProductList as $productFeedList)
    {
        $productSubmitFeedIdArray[] = $productFeedList->getFeedId();
    }
}

$AmazonProductApiResultFilter = new AmazonProductApiResultFilter();
$AmazonProductApiResultFilter->addFilter("    feed_type = 'Inventory'");
$AmazonProductApiResultFilter->addFilter("feed_status = 'SUBMITTED'");
$submittedInventoryList = $AmazonProductApiResultFilter->getList();

if(count($submittedInventoryList) >0)
{
    foreach($submittedInventoryList as $inventoryFeedList)
    {
        $inventorySubmitFeedIdArray[] = $inventoryFeedList->getFeedId();
    }
}

$AmazonProductApiResultFilter = new AmazonProductApiResultFilter();
$AmazonProductApiResultFilter->addFilter("    feed_type = 'Pricing'");
$AmazonProductApiResultFilter->addFilter("feed_status = 'SUBMITTED'");
$submittedPricingList = $AmazonProductApiResultFilter->getList();

if(count($submittedPricingList) >0)
{
    foreach($submittedPricingList as $pricingFeedList)
    {
        $pricingSubmitFeedIdArray[] = $pricingFeedList->getFeedId();
    }
}
getSubmitFeedResponseAws($productSubmitFeedIdArray,$inventorySubmitFeedIdArray, $pricingSubmitFeedIdArray);

function getSubmitFeedResponseAws($productSubmitFeedIdArray, $inventorySubmitFeedIdArray, $pricingSubmitFeedIdArray)

{
    require_once '../includes/3rdparty/AmazonAws/aws-autoloader.php';
    $queueUrl = "https://sqs.us-east-2.amazonaws.com/095611935099/submit_feed";
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
        $statusArray = simplexml_load_string($xml);
        $submitFeedIdFromNotification = $statusArray->NotificationPayload->FeedProcessingFinishedNotification->FeedSubmissionId;
        $feedType = $statusArray->NotificationPayload->FeedProcessingFinishedNotification->FeedType;
        $feedStatus = $statusArray->NotificationPayload->FeedProcessingFinishedNotification->FeedProcessingStatus;
        echo $submitFeedIdFromNotification; 
        
        if (in_array($submitFeedIdFromNotification, $productSubmitFeedIdArray)) 
        {
            $AmazonProductApiResultFilter = new AmazonProductApiResultFilter();
            $AmazonProductApiResultFilter->addFilter("    feed_id = '".$submitFeedIdFromNotification."'");
            $submittedProductList = $AmazonProductApiResultFilter->getList();

            $userId = $submittedProductList[0]->getUserId();
            $user = new User($userId);
            
            $userMarketPlaceMappingFilter = new UserMarketPlacesMappingFilter();
            $userMarketPlaceMappingFilter->addFieldFilter("user_account_id", $user->getUserAccountId());
            $userMarketPlaceMappingFilter->addFieldFilter("market_places_id", '1');
            $userMarketPlaceMappingObj = $userMarketPlaceMappingFilter->getList(); 
            $amazon = new Amazon($userMarketPlaceMappingObj[0]);
      
            if($feedType == '_POST_PRODUCT_DATA_' && $feedStatus == 'DONE')
            {
                // Call feedsubmissionresult api here and store the success and error msg in tables
                $responseArray = $amazon->invokeGetFeedSubmissionResult($submitFeedIdFromNotification);                
                if($responseArray['STATUS'] == 'SUCCESS')
                { echo 'product';
                    foreach($submittedProductList as $productFeedList) // if multiple products in a feed
                    {
                        $productIdInventoryArray[] = $productFeedList->getProductId(); // make array of products which return success on product API call
                        $AmazonProductApiResultFilter = new AmazonProductApiResult();
                        $AmazonProductApiResultFilter->setFeedType('Product');
                        $AmazonProductApiResultFilter->setFeedStatus($feedStatus);
                        $AmazonProductApiResultFilter->setFeedId(trim($submitFeedIdFromNotification));
                        $AmazonProductApiResultFilter->setProductId($productFeedList->getProductId());
                        $AmazonProductApiResultFilter->setFeedMessage($responseArray['MESSAGE']);
                        $AmazonProductApiResultFilter->setUserId($userId);
                        $AmazonProductApiResultFilter->setAddedAt(date("Y-m-d H:i:s", time()));
                        $AmazonProductApiResultFilter->save();
                        // delete that feed from sqs queue                    
                    }
                    
                    $responseArray = $amazon->inventoryListingApi($productIdInventoryArray, '');
                    if($responseArray['STATUS'] == 'SUCCESS')
                    {
                        foreach($productIdInventoryArray as $productIdInventory) // COMMA SEPARATED Ids 
                        {
                            $AmazonProductApiResult = new AmazonProductApiResult();
                            $AmazonProductApiResult->setFeedType('Inventory');
                            $AmazonProductApiResult->setFeedId($responseArray['MESSAGE']);
                            $AmazonProductApiResult->setProductId($productIdInventory);
                            $AmazonProductApiResult->setFeedStatus('SUBMITTED');
                            $AmazonProductApiResult->setUserId($userId);
                            $AmazonProductApiResult->setAddedAt(date("Y-m-d H:i:s", time()));
                            $AmazonProductApiResult->save(); 
                        }
                    }
                    else 
                    {
                        foreach($productIdInventoryArray as $productIdInventory)
                        {
                            $AmazonProductApiResult = new AmazonProductApiResult();
                            $AmazonProductApiResult->setFeedType('Inventory');
                            $AmazonProductApiResult->setFeedMessage($responseArray['MESSAGE']);
                            $AmazonProductApiResult->setProductId($productIdInventory);
                            $AmazonProductApiResult->setFeedStatus('ERROR');
                            $AmazonProductApiResult->setUserId($userId);
                            $AmazonProductApiResult->save(); 
                        }
                    }
                }
                else
                {
                    foreach($submittedProductList as $productFeedList) // if multiple products in a feed
                    {
                        //$productIdInventoryArray[] = $productFeedList->getProductId();
                        $AmazonProductApiResultFilter = new AmazonProductApiResult();
                        $AmazonProductApiResultFilter->setFeedType('Product');
                        $AmazonProductApiResultFilter->setFeedStatus($feedStatus);
                        $AmazonProductApiResultFilter->setFeedId(trim($submitFeedIdFromNotification));
                        $AmazonProductApiResultFilter->setProductId($productFeedList->getProductId());
                        $AmazonProductApiResultFilter->setFeedMessage($responseArray['MESSAGE']);
                        //$AmazonProductApiResultFilter->setFeedMessage('InActive(Product Problem)');
                        $AmazonProductApiResultFilter->setUserId($userId);
                        $AmazonProductApiResultFilter->setAddedAt(date("Y-m-d H:i:s", time()));
                        $AmazonProductApiResultFilter->save();
                        
                        //Filter to update
                        $productMarketPlaceMappingFilter = new ProductMarketPlaceMappingFilter();
                        $productMarketPlaceMappingFilter->addFilter("    usrer_id = '".$userId."' and product_id ='".$productFeedList->getProductId().'" and marketplace_id = "1"');
                        $categoryResult = $productMarketPlaceMappingFilter->getList();
                        $id = $categoryResult[0]->getId();
                        
                        $productMarketPlaceMapping = new ProductMarketPlaceMapping($id);
                        $productMarketPlaceMapping->setProductId($productFeedList->getProductId());
                        $productMarketPlaceMapping->setMarketPlaceId('1');
                        $productMarketPlaceMapping->setMessage($responseArray['MESSAGE']); 
                        $productMarketPlaceMapping->setStatus('ERROR'); 
                        $productMarketPlaceMapping->setUserId($userId);
                        $productMarketPlaceMapping->save();
                    }                    
                }
                if ($client->deleteMessage([
                    'QueueUrl' => $queueUrl, // REQUIRED
                    'ReceiptHandle' => $message['ReceiptHandle'] // REQUIRED
                ])){}
            }
        }
        
        if (in_array($submitFeedIdFromNotification, $inventorySubmitFeedIdArray)) 
        {
            $AmazonProductApiResultFilter = new AmazonProductApiResultFilter();
            $AmazonProductApiResultFilter->addFilter("    feed_id = '".$submitFeedIdFromNotification."'");
            $submittedInventoryList = $AmazonProductApiResultFilter->getList();

            $userId = $submittedInventoryList[0]->getUserId();  
            $user = new User($userId);
            
            $userMarketPlaceMappingFilter = new UserMarketPlacesMappingFilter();
            $userMarketPlaceMappingFilter->addFieldFilter("user_account_id", $user->getUserAccountId());
            $userMarketPlaceMappingFilter->addFieldFilter("market_places_id", '1');
            $userMarketPlaceMappingObj = $userMarketPlaceMappingFilter->getList(); 
            $amazon = new Amazon($userMarketPlaceMappingObj[0]);
      
            if($feedType == '_POST_INVENTORY_AVAILABILITY_DATA_' && $feedStatus == 'DONE')
            {
                // Call feedsubmissionresult api here and store the success and error msg in tables
                $responseArray = $amazon->invokeGetFeedSubmissionResult($submitFeedIdFromNotification);                
                if($responseArray['STATUS'] == 'SUCCESS')
                {
                    foreach($submittedInventoryList as $inventoryFeedList) // if multiple products in a feed
                    {
                        $productIdPricingArray[] = $inventoryFeedList->getProductId(); // push product id to array which return success to inventory API 
                        $AmazonProductApiResultFilter = new AmazonProductApiResult();
                        $AmazonProductApiResultFilter->setFeedType('Inventory');
                        $AmazonProductApiResultFilter->setFeedStatus($feedStatus);
                        $AmazonProductApiResultFilter->setFeedId(trim($submitFeedIdFromNotification));
                        $AmazonProductApiResultFilter->setProductId($inventoryFeedList->getProductId());
                        $AmazonProductApiResultFilter->setFeedMessage($responseArray['MESSAGE']);
                        $AmazonProductApiResultFilter->setUserId($userId);
                        $AmazonProductApiResultFilter->setAddedAt(date("Y-m-d H:i:s", time()));
                        $AmazonProductApiResultFilter->save();
                        // delete that feed from sqs queue                    
                    }
                    
                    $responseArray = $amazon->priceListingApi($productIdPricingArray, '');
                    
                    if($responseArray['STATUS'] == 'SUCCESS')
                    {
                        foreach($productIdPricingArray as $productIdPricing) // COMMA SEPARATED Ids 
                        {
                            $AmazonProductApiResult = new AmazonProductApiResult();
                            $AmazonProductApiResult->setFeedType('Pricing');
                            $AmazonProductApiResult->setFeedId($responseArray['MESSAGE']);
                            $AmazonProductApiResult->setProductId($productIdPricing);
                            $AmazonProductApiResult->setFeedStatus('SUBMITTED');
                            $AmazonProductApiResult->setAddedAt(date("Y-m-d H:i:s", time()));
                            $AmazonProductApiResult->setUserId($userId);
                            $AmazonProductApiResult->save(); 
                        }
                    }
                    else 
                    {
                        foreach($productIdPricingArray as $productIdPricing)
                        {
                            $AmazonProductApiResult = new AmazonProductApiResult();
                            $AmazonProductApiResult->setFeedType('Pricing');
                            $AmazonProductApiResult->setFeedMessage($responseArray['MESSAGE']);
                            $AmazonProductApiResult->setProductId($productIdPricing);
                            $AmazonProductApiResult->setFeedStatus('ERROR');
                            $AmazonProductApiResult->setUserId($userId);
                            $AmazonProductApiResult->setAddedAt(date("Y-m-d H:i:s", time()));
                            $AmazonProductApiResult->save(); 
                        }
                    }
                }
                else
                {
                    foreach($submittedInventoryList as $inventoryFeedList) // if multiple products in a feed
                    {
                        $AmazonProductApiResultFilter = new AmazonProductApiResult();
                        $AmazonProductApiResultFilter->setFeedType('Inventory');
                        $AmazonProductApiResultFilter->setFeedStatus($feedStatus);
                        $AmazonProductApiResultFilter->setFeedId(trim($submitFeedIdFromNotification));
                        $AmazonProductApiResultFilter->setProductId($inventoryFeedList->getProductId());
                        $AmazonProductApiResultFilter->setFeedMessage($responseArray['MESSAGE']);
                        //$AmazonProductApiResultFilter->setFeedMessage('InActive(Inventory Issue)');
                        $AmazonProductApiResultFilter->setUserId($userId);
                        $AmazonProductApiResultFilter->setAddedAt(date("Y-m-d H:i:s", time()));
                        $AmazonProductApiResultFilter->save();
                        // delete that feed from sqs queue   
                        
                        //Filter to update
                        $productMarketPlaceMappingFilter = new ProductMarketPlaceMappingFilter();
                        $productMarketPlaceMappingFilter->addFilter("    usrer_id = '".$userId."' and product_id ='".$inventoryFeedList->getProductId().'" and marketplace_id = "1"');
                        $categoryResult = $productMarketPlaceMappingFilter->getList();
                        $id = $categoryResult[0]->getId();
                        
                        $productMarketPlaceMapping = new ProductMarketPlaceMapping($id);
                        $productMarketPlaceMapping->setProductId($inventoryFeedList->getProductId());
                        $productMarketPlaceMapping->setMarketPlaceId('1');
                        $productMarketPlaceMapping->setMessage($responseArray['MESSAGE']); 
                        $productMarketPlaceMapping->setMessage('ERROR'); 
                        $productMarketPlaceMapping->setUserId($userId);
                        $productMarketPlaceMapping->save();
                    }                    
                }
                if ($client->deleteMessage([
                    'QueueUrl' => $queueUrl, // REQUIRED
                    'ReceiptHandle' => $message['ReceiptHandle'] // REQUIRED
                ])){}
            }
        }
        
        if (in_array($submitFeedIdFromNotification, $pricingSubmitFeedIdArray)) 
        {
            $AmazonProductApiResultFilter = new AmazonProductApiResultFilter();
            $AmazonProductApiResultFilter->addFilter("    feed_id = '".$submitFeedIdFromNotification."'");
            $submittedPricingList = $AmazonProductApiResultFilter->getList();

            $userId = $submittedPricingList[0]->getUserId();    
            $user = new User($userId);            
            
            $userMarketPlaceMappingFilter = new UserMarketPlacesMappingFilter();
            $userMarketPlaceMappingFilter->addFieldFilter("user_account_id", $user->getUserAccountId());
            $userMarketPlaceMappingFilter->addFieldFilter("market_places_id", '1');
            $userMarketPlaceMappingObj = $userMarketPlaceMappingFilter->getList(); 
            $amazon = new Amazon($userMarketPlaceMappingObj[0]);
            
            if($feedType == '_POST_PRODUCT_PRICING_DATA_' && $feedStatus == 'DONE')
            {
                echo 'price';
                $responseArray = $amazon->invokeGetFeedSubmissionResult($submitFeedIdFromNotification);                
                if($responseArray['STATUS'] == 'SUCCESS')
                {
                    foreach($submittedPricingList as $pricingFeedList) // if multiple products in a feed
                    {
                        $AmazonProductApiResultFilter = new AmazonProductApiResult();
                        $AmazonProductApiResultFilter->setFeedType('Pricing');
                        $AmazonProductApiResultFilter->setFeedStatus($feedStatus);
                        $AmazonProductApiResultFilter->setFeedId(trim($submitFeedIdFromNotification));
                        $AmazonProductApiResultFilter->setProductId($pricingFeedList->getProductId());
                        $AmazonProductApiResultFilter->setFeedMessage($responseArray['MESSAGE']);
                        $AmazonProductApiResultFilter->setUserId($userId);
                        $AmazonProductApiResultFilter->setAddedAt(date("Y-m-d H:i:s", time()));
                        $AmazonProductApiResultFilter->save();
                        // delete that feed from sqs queue       
                        
                        //Filter to get category ID from amazon_marketplace_mapping table 
                        $productMarketPlaceMappingFilter = new ProductMarketPlaceMappingFilter();
                        $productMarketPlaceMappingFilter->addFilter("    user_id = '".$userId."' and product_id ='".$pricingFeedList->getProductId()."' and marketplace_id = '1'");
                        $categoryResult = $productMarketPlaceMappingFilter->getList();
                        $categoryList = $categoryResult[0]->getCategoryId();
                        $id = $categoryResult[0]->getId();
                        
                        $productMarketPlaceMapping = new ProductMarketPlaceMapping($id);
                        $productMarketPlaceMapping->setProductId($pricingFeedList->getProductId());
                        $productMarketPlaceMapping->setMarketPlaceId('1');
                        $productMarketPlaceMapping->setMessage('Product is listed successfully');
                        $productMarketPlaceMapping->setStatus('Active');
                        $productMarketPlaceMapping->setUserId($userId); 
                        $productMarketPlaceMapping->setCategoryId($categoryList);
                        $productMarketPlaceMapping->save();                         
                    }
                }
                else
                {
                    foreach($submittedPricingList as $pricingFeedList) // if multiple products in a feed
                    {
                        $AmazonProductApiResultFilter = new AmazonProductApiResult();
                        $AmazonProductApiResultFilter->setFeedType('Pricing');
                        $AmazonProductApiResultFilter->setFeedStatus($feedStatus);
                        $AmazonProductApiResultFilter->setFeedId(trim($submitFeedIdFromNotification));
                        $AmazonProductApiResultFilter->setProductId($pricingFeedList->getProductId());
                        $AmazonProductApiResultFilter->setFeedMessage($responseArray['MESSAGE']);
                        //$AmazonProductApiResultFilter->setFeedMessage('InActive(Pricing Issue)');
                        $AmazonProductApiResultFilter->setUserId($userId);
                        $AmazonProductApiResultFilter->setAddedAt(date("Y-m-d H:i:s", time()));
                        $AmazonProductApiResultFilter->save();
                        // delete that feed from sqs queue  
                        
                         //Filter to get category ID from amazon_marketplace_mapping table 
                        $productMarketPlaceMappingFilter = new ProductMarketPlaceMappingFilter();
                        $productMarketPlaceMappingFilter->addFilter("    user_id = '".$userId."' and product_id ='".$pricingFeedList->getProductId().'" and marketplace_id = "1"');
                        $idResult = $productMarketPlaceMappingFilter->getList();
                        $id = $idResult[0]->getId();
                        
                        $productMarketPlaceMapping = new ProductMarketPlaceMapping($id);
                        $productMarketPlaceMapping->setProductId($pricingFeedList->getProductId());
                        $productMarketPlaceMapping->setMarketPlaceId('1');
                        $productMarketPlaceMapping->setMessage($responseArray['MESSAGE']);   
                        $productMarketPlaceMapping->setStatus('ERROR');
                        $productMarketPlaceMapping->setUserId($userId);
                        $productMarketPlaceMapping->save();                        
                    }                    
                }
                if ($client->deleteMessage([
                    'QueueUrl' => $queueUrl, // REQUIRED
                    'ReceiptHandle' => $message['ReceiptHandle'] // REQUIRED
                ])) {}
            }
        }
    }
}
?>





