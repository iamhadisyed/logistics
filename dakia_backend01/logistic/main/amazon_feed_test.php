<?php
require_once("../includes/settings/config.inc.php");
require_once("../includes/settings/config.inc.php");
require_once('../includes/autoload/MarketplaceWebService/Samples/.config.inc.php');
require_once('../includes/autoload/MarketplaceWebService/Client.php');
require_once('../includes/autoload/MarketplaceWebService/Model/IdList.php');
require_once('../includes/autoload/MarketplaceWebService/Model/RequestReportRequest.php');
require_once('../includes/autoload/MarketplaceWebService/Model.php');
require_once('../includes/autoload/MarketplaceWebService/Model/GetReportListRequest.php');
require_once('../includes/autoload/MarketplaceWebService/Model/GetReportRequestListRequest.php');
require_once('../includes/autoload/MarketplaceWebService/Model/GetReportRequest.php');
require_once('../includes/autoload/MarketplaceWebService/Model/SubmitFeedRequest.php');
require_once('../includes/autoload/MarketplaceWebService/Model/GetFeedSubmissionListRequest.php');

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
]);

include_classes([
    'carrierservice.class'
    ], 'general');
include_classes([
    'tourline.class', 'tourlinetrackingstatus.class','anpost.class','brtitaly.class',
    'yodel.class', 'yodeltrackingstatus.class', 'royalmail.class', 'royalmailtrackingstatus.class','hermes1.class',
    'yodel.class', 'yodeltrackingstatus.class', 'cttexpress.class',
    'huxloehermes.class', 'kaab.class', 'kabbtrackingstatus.class','asendiauk.class', 'asendiauktrackingstatus.class', 'ups.class',
    'kronosexpress.class', 'kronosexpresstrackingstatus.class', 'deutschepost.class', 'deutscheposttrackingstatus.class', 'viva.class',
    'vivatrackingstatus.class', 'parcelforyou.class','parcelforyoutrackingstatus.class', 'dhl.class','dhltrackingstatus.class','wmsfbo.class'
    ], 'labels');
include_classes([    
    'iaddress.class',    
    'sku.class',
    'consignment.class',
    'parcel.class',
    'parcelfilter.class',
    'trackingdata.class',
    'tracking.class',
    'trackingdatafilter.class',
    'consignmentfilter.class', 
    'consignmentrelabelfilter.class',
    'consignmentrelabel.class', 
    'countryfilter.class',
    'country.class',
    'serviceagentmappingfilter.class',
    'serviceagentmapping.class',
    'services.class',
    'carrier.class','servicecountrytimefilter.class', 'servicecountrytime.class', 'warehouse.class', 'marketplaceorder.class',
    'marketplaceorderfilter.class', 'marketplaceorderdetails.class', 'marketplaceorderdetailsfilter.class', 'marketplaces.class'
    ]);
    
    //$last_sync_time = date('Y-m-d H:i:s',strtotime('2021-02-04 12:00:00'));
    //$dteStart       = new DateTime(date('Y-m-d H:i:s'));
    $stTime         = new DateTime('2021-02-04 12:00:00');
            
    $serviceUrl = "https://mws.amazonservices.co.uk";
    
    $config = array(
            'ServiceURL' => $serviceUrl,
            'ProxyHost' => null,
            'ProxyPort' => -1,
            'MaxErrorRetry' => 3,
        );
    
    $service = new MarketplaceWebService_Client(
            'AKIAJBUWT3ZBRDV3QITA',
            '6y9yurr8KHXaj9Rvt83ACZyZYw2gamkvpXtu1tIe',
            $config,
            'oneworld',
            '2');
    $marketplaceIdArray = array("Id" => array('A1F83G8C2ARO7P'));
    
    $parameters = array (
  'Merchant' => 'A3LX344APRTG2Z',
  'FeedProcessingStatusList' => array ('Status' => array ('_DONE_', '_SUBMITTED_')),
  //'FeedTypeList' => array('Type' => array('_POST_ORDER_DATA_')),
  'FeedSubmissionIdList' => array('Id' => array('60243018667', '60242018667')),
  //'SubmittedFromDate' => $stTime,
);
//
$request = new MarketplaceWebService_Model_GetFeedSubmissionListRequest($parameters);

//$request = new MarketplaceWebService_Model_GetFeedSubmissionListRequest();
//$request->setMerchant(MERCHANT_ID);
//
//$statusList = new MarketplaceWebService_Model_StatusList();
//$request->setFeedProcessingStatusList($statusList->withStatus('_SUBMITTED_'));
//
invokeGetFeedSubmissionList($service, $request);

           //60243018667, 60242018667                                                                 
/**
  * Get Feed Submission List Action Sample
  * returns a list of feed submission identifiers and their associated metadata
  *   
  * @param MarketplaceWebService_Interface $service instance of MarketplaceWebService_Interface
  * @param mixed $request MarketplaceWebService_Model_GetFeedSubmissionList or array of parameters
  */
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
 }
                            
die;

///////////////PRICE API///////////////////////////
    /*
$feed = '<?xml version="1.0" encoding="utf-8"?>
<AmazonEnvelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="amznenvelope.xsd">
<Header>
<DocumentVersion>1.01</DocumentVersion>
<MerchantIdentifier>A3LX344APRTG2Z</MerchantIdentifier>
</Header>
<MessageType>Price</MessageType>
<Message>
<MessageID>1</MessageID>
<Price>
<SKU>B2Y-sp-1</SKU>
<StandardPrice currency="GBP">21.99</StandardPrice> 
</Price>
</Message>
<Message>
<MessageID>2</MessageID>
<Price>
<SKU>B2Y-sp-2</SKU>
<StandardPrice currency="GBP">21.99</StandardPrice> 
</Price>
</Message>
</AmazonEnvelope>';
$feedHandle = @fopen('php://temp', 'rw+');
    fwrite($feedHandle, $feed);
    rewind($feedHandle);
    $parameters = array (
      'Merchant' => 'A3LX344APRTG2Z',
      'MarketplaceIdList' => $marketplaceIdArray,
      'FeedType' => '_POST_PRODUCT_PRICING_DATA_',
      'FeedContent' => $feedHandle,
      'PurgeAndReplace' => false,
      'ContentMd5' => base64_encode(md5(stream_get_contents($feedHandle), true)),
    );
    rewind($feedHandle);

    $request = new MarketplaceWebService_Model_SubmitFeedRequest($parameters);
    $resultValue = invokeSubmitFeed($service, $request);
    echo $resultValue;
    die;

  //////////////INVENTORY API ///////////////////////
 

$feed = '<?xml version="1.0" encoding="utf-8" ?>
<AmazonEnvelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="amzn-envelope.xsd">
<Header>
<DocumentVersion>1.01</DocumentVersion>
<MerchantIdentifier>AYHIDCM8MEZBT</MerchantIdentifier>
</Header>
<MessageType>Inventory</MessageType>
<Message>
<MessageID>1</MessageID>
<OperationType>Update</OperationType>
<Inventory>
<SKU>B2Y-sp-2</SKU>
<Quantity>10</Quantity>
<FulfillmentLatency>1</FulfillmentLatency>
</Inventory>
</Message>
</AmazonEnvelope>';

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
    $resultValue = invokeSubmitFeed($service, $request);
    echo $resultValue;
    die;

    */
    ///Relationship API 
    /*
    $feed = '<?xml version="1.0" ?>
            <AmazonEnvelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="amznenvelope.xsd">
            <Header>
                <DocumentVersion>1.01</DocumentVersion>
                <MerchantIdentifier>AYHIDCM8MEZBT</MerchantIdentifier>
            </Header>
            <MessageType>Relationship</MessageType>            
<Message>
<MessageID>1</MessageID>
<OperationType>Update</OperationType>
<Relationship>
<ParentSKU>B2Y-sp</ParentSKU>
<Relation>
<SKU>B2Y-sp-1</SKU>
<Type>Variation</Type>
</Relation>
<Relation>
<SKU>B2Y-sp-2</SKU>
<Type>Variation</Type>
</Relation>

        </Relationship>
        </Message>
        </AmazonEnvelope>';
    
    $feedHandle = @fopen('php://temp', 'rw+');
    fwrite($feedHandle, $feed);
    rewind($feedHandle);
    $parameters = array (
      'Merchant' => 'A3LX344APRTG2Z',
      'MarketplaceIdList' => $marketplaceIdArray,
      'FeedType' => '_POST_PRODUCT_RELATIONSHIP_DATA_',
      'FeedContent' => $feedHandle,
      'PurgeAndReplace' => false,
      'ContentMd5' => base64_encode(md5(stream_get_contents($feedHandle), true)),
    );
    rewind($feedHandle);

    $request = new MarketplaceWebService_Model_SubmitFeedRequest($parameters);
    $resultValue = invokeSubmitFeed($service, $request);
    echo $resultValue;
    die;
    */
    ///////PRODUCT API without variation 
    
    $feed = '<?xml version="1.0" ?>
            <AmazonEnvelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="amznenvelope.xsd">
            <Header>
                <DocumentVersion>1.01</DocumentVersion>
                <MerchantIdentifier>AYHIDCM8MEZBT</MerchantIdentifier>
            </Header>
            <MessageType>Product</MessageType>
            <PurgeAndReplace>false</PurgeAndReplace>
            
        <Message>
        <MessageID>1</MessageID>
            <OperationType>Update</OperationType>
            <Product>
                <SKU>B2Y-mug-1</SKU>
                <StandardProductID>
                    <Type>ASIN</Type>
                    <Value>B07WZV3K9J</Value>
                </StandardProductID>
                <LaunchDate>2021-02-01T00:00:01</LaunchDate>
                <DescriptionData>
                    <Title>Bodum Stainless Steel Travel Mug, Pastel Pink</Title>
                    <Brand>BODUM</Brand>
                    <Description>Classic travel mug from Bodum. This travel size vacuum mug is made from stainless steel for maximum heat retention. Coloured closable lid with a stopper for the opening. Slip proof silicone band around the middle of the mug to match the lid colour, featuring the Bodum logo along with the words - THE FRESH WAY TO BREW FRESH COFFEE and TEA. Dishwasher safe. Capacity 0.35L (12oz). Height 18cm, diameter at base 7cm. Not boxed. Caution: Please do always keep the mug upright. Due to safety reasons, the mug is not leakproof because of possible over pressure. Available in a range of colours.</Description>
                    <BulletPoint>Classic travel mug from Bodum</BulletPoint>
                    <Manufacturer>BODUM</Manufacturer>
                    <RecommendedBrowseNode>10294493031</RecommendedBrowseNode>
                </DescriptionData>              
            </Product>
        </Message>
        <Message>
        <MessageID>2</MessageID>
            <OperationType>Update</OperationType>
            <Product>
                <SKU>B2Y-mug-2</SKU>
                <StandardProductID>
                    <Type>ASIN</Type>
                    <Value>B07X2YLWQF</Value>
                </StandardProductID>
                <LaunchDate>2021-02-01T00:00:01</LaunchDate>
                <DescriptionData>
                    <Title>Bodum Stainless Steel Travel Mug, Pastel Pink</Title>
                    <Brand>BODUM</Brand>
                    <Description>Classic travel mug from Bodum. This travel size vacuum mug is made from stainless steel for maximum heat retention. Coloured closable lid with a stopper for the opening. Slip proof silicone band around the middle of the mug to match the lid colour, featuring the Bodum logo along with the words - THE FRESH WAY TO BREW FRESH COFFEE and TEA. Dishwasher safe. Capacity 0.35L (12oz). Height 18cm, diameter at base 7cm. Not boxed. Caution: Please do always keep the mug upright. Due to safety reasons, the mug is not leakproof because of possible over pressure. Available in a range of colours.</Description>
                    <BulletPoint>Classic travel mug from Bodum</BulletPoint>
                    <Manufacturer>BODUM</Manufacturer>
                    <RecommendedBrowseNode>10294493031</RecommendedBrowseNode>
                </DescriptionData>               
            </Product>
        </Message>        
    </AmazonEnvelope>';
    
    $feedHandle = @fopen('php://temp', 'rw+');
    fwrite($feedHandle, $feed);
    rewind($feedHandle);
    $parameters = array (
      'Merchant' => 'A3LX344APRTG2Z',
      'MarketplaceIdList' => $marketplaceIdArray,
      'FeedType' => '_POST_PRODUCT_DATA_',
      'FeedContent' => $feedHandle,
      'PurgeAndReplace' => false,
      'ContentMd5' => base64_encode(md5(stream_get_contents($feedHandle), true)),
    );
    rewind($feedHandle);

    $request = new MarketplaceWebService_Model_SubmitFeedRequest($parameters);
    $resultValue = invokeSubmitFeed($service, $request);
    echo $resultValue;
    die;
    
        ///////PRODUCT API Variation example Second Example 
    /*
    $feed = '<?xml version="1.0" ?>
            <AmazonEnvelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="amznenvelope.xsd">
            <Header>
                <DocumentVersion>1.01</DocumentVersion>
                <MerchantIdentifier>AYHIDCM8MEZBT</MerchantIdentifier>
            </Header>
            <MessageType>Product</MessageType>
            <PurgeAndReplace>false</PurgeAndReplace>
            <Message>
            <MessageID>1</MessageID>
            <OperationType>Update</OperationType>
            <Product>
                <SKU>B2Y-sp</SKU>
                <LaunchDate>2021-02-01T00:00:01</LaunchDate>
                <DescriptionData>
                    <Title>Bluetooth Speaker, ENACFIRE SoundBar, Portable Wireless Speakers 25-Hour Playtime Crystal Clear Stereo Sound Enhanced Bass IPX7 Waterproof Speaker with Built-in Microphone</Title>
                    <Brand>ENACFIRE</Brand>
                    <Description>The stereo active dual Bluetooth speakers combination creates an optimized sound filed for dynamic range and exceptional clarity across all music. True 360 degree surround sound, light up your backyard BBQ Feast, Birthday Party, Family Gathering, Holiday’s atmosphere on top of the world.</Description>
                    <BulletPoint>Double high-power 12 watts speakers deliver astonished sound volume that penetrate every single room even in an outdoor environment. Booming bass effect and zero distortion in highest volume provides perfect rich listening experiences without comprising the sound quality</BulletPoint>
                    <BulletPoint>With ENACFIRE SoundBar Portable Wireless Speaker , you can literally play the music consistently more than 25 hours on a single charge. All day, from the day to night, it can be with you anytime, anywhere, no matter where you go.</BulletPoint>
                    <Manufacturer>ENACFIRE</Manufacturer>
                    <RecommendedBrowseNode>60583031</RecommendedBrowseNode>
                    <RecommendedBrowseNode>60576021</RecommendedBrowseNode>
                </DescriptionData>
                <ProductData>
                    <Home>
                        <VariationData>
                            <Parentage>parent</Parentage>
                            <VariationTheme>Color</VariationTheme>
                        </VariationData>
                    </Home>
                </ProductData>
            </Product>
        </Message>
        <Message>
        <MessageID>2</MessageID>
            <OperationType>Update</OperationType>
            <Product>
                <SKU>B2Y-sp-1</SKU>
                <StandardProductID>
                    <Type>ASIN</Type>
                    <Value>B0881XGQ5W</Value>
                </StandardProductID>
                <LaunchDate>2021-02-01T00:00:01</LaunchDate>
                <DescriptionData>
                    <Title>Bluetooth Speaker, ENACFIRE SoundBar, Portable Wireless Speakers 25-Hour Playtime Crystal Clear Stereo Sound Enhanced Bass IPX7 Waterproof Speaker with Built-in Microphone</Title>
                    <Brand>ENACFIRE</Brand>
                    <Description>The stereo active dual Bluetooth speakers combination creates an optimized sound filed for dynamic range and exceptional clarity across all music. True 360 degree surround sound, light up your backyard BBQ Feast, Birthday Party, Family Gathering, Holiday’s atmosphere on top of the world.</Description>
                    <BulletPoint>Double high-power 12 watts speakers deliver astonished sound volume that penetrate every single room even in an outdoor environment. Booming bass effect and zero distortion in highest volume provides perfect rich listening experiences without comprising the sound quality</BulletPoint>
                    <BulletPoint>With ENACFIRE SoundBar Portable Wireless Speaker , you can literally play the music consistently more than 25 hours on a single charge. All day, from the day to night, it can be with you anytime, anywhere, no matter where you go.</BulletPoint>
                    <Manufacturer>ENACFIRE</Manufacturer>
                    <RecommendedBrowseNode>60583031</RecommendedBrowseNode>
                    <RecommendedBrowseNode>60576021</RecommendedBrowseNode>
                </DescriptionData>
                <ProductData>
                    <Home>                        
                        <VariationData>   
                            <Parentage>child</Parentage>
                            <VariationTheme>Color</VariationTheme>
                            
                            <Color>Blue</Color>
                        </VariationData>
                    </Home>
                </ProductData>
            </Product>
        </Message>
        <Message>
        <MessageID>3</MessageID>
            <OperationType>Update</OperationType>
            <Product>
                <SKU>B2Y-sp-2</SKU>
                <StandardProductID>
                    <Type>ASIN</Type>
                    <Value>B07NW3TW32</Value>
                </StandardProductID>
                <LaunchDate>2021-02-01T00:00:01</LaunchDate>
                <DescriptionData>
                    <Title>Bluetooth Speaker, ENACFIRE SoundBar, Portable Wireless Speakers 25-Hour Playtime Crystal Clear Stereo Sound Enhanced Bass IPX7 Waterproof Speaker with Built-in Microphone</Title>
                    <Brand>ENACFIRE</Brand>
                    <Description>The stereo active dual Bluetooth speakers combination creates an optimized sound filed for dynamic range and exceptional clarity across all music. True 360 degree surround sound, light up your backyard BBQ Feast, Birthday Party, Family Gathering, Holiday’s atmosphere on top of the world.</Description>
                    <BulletPoint>Double high-power 12 watts speakers deliver astonished sound volume that penetrate every single room even in an outdoor environment. Booming bass effect and zero distortion in highest volume provides perfect rich listening experiences without comprising the sound quality</BulletPoint>
                    <BulletPoint>With ENACFIRE SoundBar Portable Wireless Speaker , you can literally play the music consistently more than 25 hours on a single charge. All day, from the day to night, it can be with you anytime, anywhere, no matter where you go.</BulletPoint>
                    <Manufacturer>ENACFIRE</Manufacturer>
                    <RecommendedBrowseNode>60583031</RecommendedBrowseNode>
                    <RecommendedBrowseNode>60576021</RecommendedBrowseNode>
                </DescriptionData>
                <ProductData>
                    <Home>                        
                        <VariationData>   
                            <Parentage>child</Parentage>
                            <VariationTheme>Color</VariationTheme>
                            <Color>black</Color>
                        </VariationData>
                    </Home>
                </ProductData>
            </Product>
        </Message>        
    </AmazonEnvelope>';
    
    $feedHandle = @fopen('php://temp', 'rw+');
    fwrite($feedHandle, $feed);
    rewind($feedHandle);
    $parameters = array (
      'Merchant' => 'A3LX344APRTG2Z',
      'MarketplaceIdList' => $marketplaceIdArray,
      'FeedType' => '_POST_PRODUCT_DATA_',
      'FeedContent' => $feedHandle,
      'PurgeAndReplace' => false,
      'ContentMd5' => base64_encode(md5(stream_get_contents($feedHandle), true)),
    );
    rewind($feedHandle);

    $request = new MarketplaceWebService_Model_SubmitFeedRequest($parameters);
    $resultValue = invokeSubmitFeed($service, $request);
    echo $resultValue;
    die;
    */
///// PRODUCT API 
   /* $feed='<?xml version="1.0" encoding="utf-8" ?>
<AmazonEnvelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="amzn-envelope.xsd">
<Header>
<DocumentVersion>1.01</DocumentVersion>
<MerchantIdentifier>AYHIDCM8MEZBT</MerchantIdentifier>
</Header>
<MessageType>Product</MessageType>
<PurgeAndReplace>false</PurgeAndReplace>
<Message>
	<MessageID>1</MessageID>
	<OperationType>Update</OperationType>
	<Product>
            <SKU>BRD-010</SKU>
            <StandardProductID>
                    <Type>ASIN</Type>
                    <Value>B07NLD4KFF</Value>
            </StandardProductID>
            <DescriptionData>
                    <Title>Sleepdown Fitted Sheet Super Soft Easy Care Polycotton Bed Linen - Soft Pink - Single</Title>
                    <Brand>Sleepdown</Brand>
                    <Description>Bed Linen</Description>
                    <BulletPoint>Premium Peridae Range</BulletPoint>
                    <Manufacturer>Sadaqat Global Ltd</Manufacturer>                    
                    <IsGiftWrapAvailable>false</IsGiftWrapAvailable>
                    <IsGiftMessageAvailable>false</IsGiftMessageAvailable>
                    <RecommendedBrowseNode>60576021</RecommendedBrowseNode>
            </DescriptionData>
            <ProductData>
                <Home>
                    <ProductType>
                        <BedLinen>
                            <Material>Example Ingredients</Material>
                            <VariationData>    
                                <VariationTheme>Color</VariationTheme>
                                <Color>plum</Color>                                                    
                            </VariationData>	
                        </BedLinen>					
                    </ProductType>								
                </Home>								
            </ProductData>
	</Product>
    </Message>
    <Message>
	<MessageID>2</MessageID>
	<OperationType>Update</OperationType>
	<Product>
            <SKU>BRD-010_1</SKU>
            <StandardProductID>
                <Type>ASIN</Type>
                <Value>B07PW8LSVQ</Value>
            </StandardProductID>
             <DescriptionData>
                    <Title>Sleepdown Fitted Sheet Super Soft Easy Care Polycotton Bed Linen - Soft Pink - Single</Title>
                    <Brand>Sleepdown</Brand>
                    <Description>Bed Linen</Description>
                    <BulletPoint>Premium Peridae Range</BulletPoint>
                    <Manufacturer>Sadaqat Global Ltd</Manufacturer>                    
                    <IsGiftWrapAvailable>false</IsGiftWrapAvailable>
                    <IsGiftMessageAvailable>false</IsGiftMessageAvailable>
                    <RecommendedBrowseNode>60576021</RecommendedBrowseNode>
            </DescriptionData>
            <ProductData>
                <Home>
                    <ProductType>
                        <BedLinen>
                            <Material>Example Ingredients</Material>
                            <VariationData>                                      
                                <VariationTheme>Color</VariationTheme>
                                <Color>pink</Color>
                            </VariationData>	
                        </BedLinen>					
                    </ProductType>								
                </Home>								
            </ProductData>
	</Product>
    </Message>
</AmazonEnvelope>'; 
    
    
    $feedHandle = @fopen('php://temp', 'rw+');
    fwrite($feedHandle, $feed);
    rewind($feedHandle);
    $parameters = array (
      'Merchant' => 'A3LX344APRTG2Z',
      'MarketplaceIdList' => $marketplaceIdArray,
      'FeedType' => '_POST_PRODUCT_DATA_',
      'FeedContent' => $feedHandle,
      'PurgeAndReplace' => false,
      'ContentMd5' => base64_encode(md5(stream_get_contents($feedHandle), true)),
    );
    rewind($feedHandle);

    $request = new MarketplaceWebService_Model_SubmitFeedRequest($parameters);
    $resultValue = invokeSubmitFeed($service, $request);
    echo $resultValue;
    die;
 */   
    function invokeSubmitFeed(MarketplaceWebService_Interface $service, $request)
    {
        try {
	    $response = $service->submitFeed($request);
            print_r($response);
            die;
            if ($response->isSetSubmitFeedResult()) 
            { 
		$submitFeedResult = $response->getSubmitFeedResult();
                if ($submitFeedResult->isSetFeedSubmissionInfo()) 
		{ 
                    $feedSubmissionInfo = $submitFeedResult->getFeedSubmissionInfo();
                    print_r($feedSubmissionInfo); die;
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
            echo $ex->getMessage(); die;
            $output["STATUS"] = "ERROR";
            $output["MESSAGE"] = "We are unable to submit information on amazon";             
            echo json_encode($output);
            die;
        }
    }
?>