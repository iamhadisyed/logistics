<?php

require_once("../includes/settings/config.inc.php");
//error_reporting(E_ALL);

require_once("../includes/settings/config.inc.php");
//error_reporting(E_ALL);
//ini_set('display_errors', 'On');



require_once("../includes/settings/config.inc.php");

include_classes([
    'carrierservice.class'
    ], 'general');
include_classes([
    'tourline.class', 'tourlinetrackingstatus.class','anpost.class','brtitaly.class',
    'yodel.class', 'yodeltrackingstatus.class', 'royalmail.class', 'royalmailtrackingstatus.class','hermes1.class',
    'yodel.class', 'yodeltrackingstatus.class', 'cttexpress.class',
    'huxloehermes.class', 'kaab.class', 'kabbtrackingstatus.class','asendiauk.class', 'asendiauktrackingstatus.class', 'ups.class',
    'kronosexpress.class', 'kronosexpresstrackingstatus.class', 'deutschepost.class', 'deutscheposttrackingstatus.class', 'viva.class',
    'vivatrackingstatus.class', 'parcelforyou.class','parcelforyoutrackingstatus.class', 'dhl.class','dhltrackingstatus.class','wmsfbo.class',
    'orangeconnex.class', 'integratedsolutions.class'
    ], 'labels');
include_classes([    
    'iaddress.class',    
    'sku.class',
    'skufilter.class',
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
    'carrier.class','servicecountrytimefilter.class', 'servicecountrytime.class', 'warehouse.class', 'warehousefilter.class', 'marketplaceorder.class',
    'marketplaceorderfilter.class', 'marketplaceorderdetails.class', 'marketplaceorderdetailsfilter.class', 'marketplaces.class',
    'usermarketplacesmapping.class',
    'usermarketplacesmappingfilter.class',
    'wms.class',
    'flightinfofilter.class',
    'flightinfo.class',
    'flight.class',
    'flightmappingfilter.class',
    'flightmapping.class',
    'mawbparcelmapping.class',
    'mawbparcelmappingfilter.class',
    'mawb.class',
    'apidata.class',

   
    ]);

    set_time_limit(-1);

    error_reporting(E_ALL);
    ini_set('display_errors', '0');

    $con = new Consignment(681577);
    $service = new Services();
    $country = new Country();
    $integratedSolutions = new IntegratedSolutions();
    //$result = $integratedSolutions->validation($con, $service, $country);

    $result = $integratedSolutions->label($con);
    //echo json_encode($result);
    print_r($result);
    die;

    $service = new Services();

    $country = new Country();

    $orangeConnex = new OrangeConnex();
    $mawbId = 3597;
    $result = $orangeConnex->MawbInfo($mawbId);

    //$result = $orangeConnex->validation($con, $service, $country);
    //$bagId = 12739;
    //$result = $orangeConnex->ConfirmShipmentApi($bagId);
    //print_r($result);
    //$result = $orangeConnex->label($con);
    print_r($result);

    ///print_r($result);

    die;




    

    if($result->response->status->code == 'ERROR')
    {

    }
    elseif($result->response->status->code == 'OK')
    {
        $jobId = $result->response->consignment->jobid;
        $date = $result->response->consignment->ETA->date; 
        $service_time = $result->response->consignment->ETA->service_time;           
          
    }


    die;

    

    //GetOrderTransactionId();

    //$con = new Consignment(667040);

    //print_r($con);

    //die;

    


    die;

    $date = date("Y-m-d");
    $baggingList = array();

    $mawbList = array();


    $flightInfoFilter = new FlighInfoFilter();
    $flightInfoFilter->addFromFilter("    etd", $date);
    $flightInfoFilter->addToFilter("etd", date('Y-m-d', strtotime($date. ' + 1 days')));
    $flightInfoFilter->addToFilter("is_closed", 1);


    $flightList = $flightInfoFilter->getList();

    if(count($flightList) > 0)
    {
        $flightInfoObj = $flightList[0];       
        $flightMappingFilter = new FlightMappingFilter();
        $flightMappingFilter->addFieldFilter("    flight_info_id", $flightInfoObj->getId());
        $flightMappingList = $flightMappingFilter->getList();

        if(count($flightMappingList)> 0)
        {
            foreach($flightMappingList as $flightMappingObj)
            {
                $mawbId = $flightMappingObj->getMawbId();

                if($mawbId > 0)
                {
                    $mawb = new Mawb($mawbId);
                    $mawbList[] = $mawb;
                }
            }

            
        }       

    }

    echo count($mawbList);

    //return $baggingList;


    die;

   


    die;

    //echo date('Y-m-d H:i:s O');

    $consignment = new Consignment(654248);

    $transactionId = GetOrderTransactionId();

    DispatchOrder($consignment, $transactionId);

    //$items = $consignment->getItems();

    //print_r($items);

    die;



    //InsertMarketPlaceOrder($consignment);

    die;

    
    
    
    
    //$result = $orangeConnex->MawbInfo($mawb);
    
    //print_r($result);

    die;

    


    //die;


    OCValidationCall();
    //OCPackageLabelApi("GS10000000774680001010103G0N");

    function GetOrderTransactionId()
    {
        $serverUrl = 'https://api.ebay.com/ws/api.dll';      // server URL different for prod and sandbox
        $devID = '2da28c16-c0be-4118-a6b4-b1a5a79c476b';   // these prod keys are different from sandbox keys
        $appID = 'Muhammad-EbayOrde-PRD-445f64428-0dc8f9b3';
        $certID = 'PRD-45f64428ed9b-4446-49b0-bb6c-7ca9';
        $verb = 'GetOrders';	
        $siteID = 3;
        $compatabilityLevel = 717;    // eBay API version
        $userToken = "AgAAAA**AQAAAA**aAAAAA**/HK5YA**nY+sHZ2PrBmdj6wVnY+sEZ2PrA2dj6MFlYulC5aApAydj6x9nY+seQ**k4oDAA**AAMAAA**+RUJalm4pVj6lI8N6fMbwHq4bHO0KkB4B9JFxRLUDa2F3/Mox58MxB5WjpRh6P1yZY5e/FSXg+/P6R2s83qiRQTZZIXRlebgvSizp85E+XiTblrBhZDAy45CeHW4WY986bl0/K+B+K5z2IbV00FK+UcR8rfEYxgjZ3uaY/R9uRc9WvkJb0PJxJDKvh5/ZCq9ra6mbPcQcZdLOJjGqCGqS0Jlvty/Po8jR99V42ikkSyDZyBVS2Yi4//2zVzakg0BzFiziYOVSxVOO20DJHPphwCwf4+e1bh+kvAfVJyTJFJyvH7ZxKp/MAuf2uW75Nvcylx0zjsQ0QkhQ8PGA/G5v8p57FM53PA/SP0DbztQoOj/cjSk/rXFPs3AveDMq4HIGPDNsS32clSx33NkA/CyACfoHTxaXJAEiloqoyWsiL3MfU5p84Wf3GdmYGuZfJWxxD0ptB12svJua17M3orTnKsBptEZAoB1qZMlde++6QrVmP3FgQTb2X5+fIxnTOkjDHegHqHFSeazXDt5zB2DuUMbZXOLtkOkUeM00djk5itKc1qv1betgCk7P+pwASvmEU3PlM7SYxLV6wh7JsVbqmavZBav3Bxg0hStJfC4SNPFer7pO9zymxfo/Hk2JILBeurn1oC6ZDkxHk/GBOGlAa6/cbiWNQPGOaFmMQA/uV9en8PQWElKfQBVTaCOqjFcpiIQ4gAE8YKYY8t8fB3M+1YYjgFZNqeZSZQ+B/PSBBkLGWrKicArKabxfN1M6ZJ8";


        $CreateTimeFrom = date("Y-m-d");
        $CreateTimeFrom = "2021-06-18";
        $CreateTimeTo = date("Y-m-d");

        $CreateTimeFrom = date('Y-m-d',strtotime($CreateTimeFrom)).'T'.date('H:i:s',strtotime($CreateTimeFrom)).'.000Z';
		$CreateTimeTo   = date('Y-m-d',strtotime($CreateTimeTo.'+1 day')).'T'.date('H:i:s',strtotime($CreateTimeTo.'+1 day')).'.000Z';

        $requestXmlBody = '<?xml version="1.0" encoding="utf-8" ?>';
        $requestXmlBody .= '<GetOrdersRequest xmlns="urn:ebay:apis:eBLBaseComponents">';
        $requestXmlBody .= '<DetailLevel>ReturnAll</DetailLevel>';
        $requestXmlBody .= "<CreateTimeFrom>$CreateTimeFrom</CreateTimeFrom><CreateTimeTo>$CreateTimeTo</CreateTimeTo>";
        /*$requestXmlBody .= '<OrderIDArray> OrderIDArrayType';
        $requestXmlBody .= '<OrderID>'.$consignment->getHawb().'</OrderID>';
        $requestXmlBody .= '</OrderIDArray>';*/
        $requestXmlBody .= '<OrderRole>Seller</OrderRole><OrderStatus>Completed</OrderStatus>';
        $requestXmlBody .= "<RequesterCredentials><eBayAuthToken>".$userToken."</eBayAuthToken></RequesterCredentials>";
        $requestXmlBody .= '</GetOrdersRequest>';
				
        //Create a new eBay session with all details pulled in from included keys.php
        $session = new eBaySession($userToken, $devID, $appID, $certID, 
                                    $serverUrl, $compatabilityLevel, $siteID, $verb);

        
        //send the request and get response
        $responseXml = $session->sendHttpRequest($requestXmlBody);

        echo $responseXml;

        if (stristr($responseXml, 'HTTP 404') || $responseXml == '')
            die('<P>Error sending request');
        
        //print_r($responseXml);
        //Xml string is parsed and creates a DOM Document object
        $responseDoc = new DomDocument();
        $responseDoc->loadXML($responseXml);	
        
        //get any error nodes
        $errors = $responseDoc->getElementsByTagName('Errors');
        $response = simplexml_import_dom($responseDoc);
        $xml = $response->asXML();
        $result = simplexml_load_string($xml);

        print_r($result);

        return $result;

        
        //$entries = $response->PaginationResult->TotalNumberOfEntries;

        //$transactionid = $response->OrderArray->Order->TransactionArray->Transaction->TransactionID;

        return $transactionid;

    }


    function DispatchOrder($consignment, $transactionId)
    {
        $serverUrl = 'https://api.ebay.com/ws/api.dll';      // server URL different for prod and sandbox
        $devID = '2da28c16-c0be-4118-a6b4-b1a5a79c476b';   // these prod keys are different from sandbox keys
        $appID = 'Muhammad-EbayOrde-PRD-445f64428-0dc8f9b3';
        $certID = 'PRD-45f64428ed9b-4446-49b0-bb6c-7ca9';
        $verb = 'CompleteSale';	
        $siteID = 3;
        $compatabilityLevel = 717;    // eBay API version
        $userToken = "AgAAAA**AQAAAA**aAAAAA**jme5YA**nY+sHZ2PrBmdj6wVnY+sEZ2PrA2dj6MFmICiAZKLoASdj6x9nY+seQ**k4oDAA**AAMAAA**6MTeztOIIi2E7jPkLZ9LU6LAB1kTiYRoQ4Ny9ugBUApTZvSZt6DRXmYabLu2Wq4qtof/oQP2zHM+btTF/ZODC9IOELpKDXQExvnFFSX37zv0nsoNRlhdFc5QcN2Xfo3VPEfm8UtFCwpWmJSCYDyqSTnaoNXY1BZW4cIQkr9euqcV6s4MS97gerDRHRgj09lJg2aUnEGo4v1UiD9e9w46YNfjWTzjLzdfM/3lwbbIUkMpSKwvIVsjZQhAxbOnS/0jbSxL2LABZ5vRS219rqYGwRu4HBCk7SFDJHo1QvwwZoLuzvBefXUumGNpQLLflBEOCtwFd1FeemBKJGIPrf/v1yvjWOgLJd5RisiyUx0WKIii7fSv4c7FswOuZVOuUyjAoAlMziBw1eRx1tYMkfIJt6ZYowsl+Sa5MP7OrfFOXgZGQVr3O9T5D7No/q8akBvdXvoSF1t87O+UA6JDYRXJfxxdYdcjj9ikLVABKpFcnF+sl+Zb4b6XlETasdvBV+DhE3FnhSR8nLJifJLwMfmd/SgsRcvk/MRSJEEfITzOYGFHiOwW2t/SVl5e2hJ7qYy/yfJuPRe/qSkJ3XG8xs8dO590dg9PtthcHRcvztsnsQoniV15HkUdPZ1KlUIQ70uDUWc0vnGH7wyeKJFgqeId3BdzJFyFradapriiJoVjUocUDJRpUwpbXgm+YR4rwK3fLw+JQ43pMc+YaICNpDhvtPYDmP4XELvEofPKzQT9Yfe6JKz69ly0Zn0tDdkWqzSx";
        
        
        $awb = $consignment->getAwb();
        $transactionid = $consignment->getHawb();
        $marketPlaceItemId = "";
        $carrier = "YODEL";

        $where = array(
            "consignment_id" => $consignment->getId()
        );

        $itemDetailsFilter = new ItemDetailFilter();
        $itemDetailsFilter->where($where);
        $itemsDetailList = $itemDetailsFilter->getList();

        if (count($itemsDetailList) > 0) {
            $itemDetails = $itemsDetailList[0];
            $itemDetail = $itemDetails->getItemDetail();
            $itemDetailObj = json_decode($itemDetail, true);
            foreach ($itemDetailObj as $itemObj) {

                $marketPlaceItemId = $itemObj['item_sku'];

            }
        }
        

        echo "marketplaeItemId " . $marketPlaceItemId;

        $requestXmlBody = '<?xml version="1.0" encoding="utf-8" ?>';
        $requestXmlBody .= '<CompleteSaleRequest xmlns="urn:ebay:apis:eBLBaseComponents">';

        $requestXmlBody .= "<ItemID>$marketPlaceItemId</ItemID>";
        $requestXmlBody .= "<TransactionID>$transactionId</TransactionID>";
        $requestXmlBody .= "<Shipment>";
        $requestXmlBody .= "<ShipmentTrackingDetails>";
        $requestXmlBody .= "<ShipmentTrackingNumber>$awb</ShipmentTrackingNumber>";
        $requestXmlBody .= "<ShippingCarrierUsed>$carrier</ShippingCarrierUsed>";
        $requestXmlBody .= "</ShipmentTrackingDetails>";
        $requestXmlBody .= "</Shipment>";

        $requestXmlBody .= "<Shipped>true</Shipped>";
        $requestXmlBody .= "<RequesterCredentials><eBayAuthToken>$userToken</eBayAuthToken></RequesterCredentials>";
        $requestXmlBody .= '</CompleteSaleRequest>';

        $session = new eBaySession($userToken, $devID, $appID, $certID, $serverUrl, $compatabilityLevel, $siteID, $verb);

        //send the request and get response
        $responseXml = $session->sendHttpRequest($requestXmlBody);

        echo $responseXml;

        //mail("kazim@oneworldexpress.com", "Ebay Complete Salee", $requestXmlBody . $responseXml);

        if (stristr($responseXml, 'HTTP 404') || $responseXml == '')
            die('<P>Error sending request');

        //Xml string is parsed and creates a DOM Document object
        $responseDoc = new DomDocument();
        $responseDoc->loadXML($responseXml);


        //get any error nodes
        $errors = $responseDoc->getElementsByTagName('Errors');
        $response = simplexml_import_dom($responseDoc);

        //$entries = $response->PaginationResult->TotalNumberOfEntries;

        foreach($response as $result=>$val)
        {
            if(trim($result)=="Ack")
            {
                //echo $val; die;		
                if($val=="Success" || $val=="SUCCESS")
                {
                    echo "success: marked as shipped";	

                    //$rowlistData->setStatus(CONSIGNMENT::STATUS_BOOKED);
                    //$rowlistData->save();
                }
                else
                {
                    echo "failed";	

                    /*$rowlistData->setStatus(CONSIGNMENT::STATUS_INVALID);
                    $rowlistData->setMessage();
                    $rowlistData->save();
                    */
                }
            }
        }


    }

    function GetAuthString($rawUrl, $json)
    {
        $host = "https://test-gspk.orangeconnex.com:8443";

        $apiKey = "89ccb764-15c1-4a47-af0b-02628bae3529";
        
        $clientKey = "49de9e27-6f9d-496b-80ac-24fbf275fdf0";

        $authString =  $rawUrl . "|". $json . "|" . $apiKey .  "|" . $clientKey;

        $authKey = md5($authString);

        return $authKey;


    }
    

    function SetHeaders($authKey)
    {
        $headers = array(

            "ApiKey: 89ccb764-15c1-4a47-af0b-02628bae3529",
            "clientKey: 49de9e27-6f9d-496b-80ac-24fbf275fdf0",
            "Content-Type: application/json;charset=UTF-8",
            "vAuthorization: " . $authKey,
            "TimeStamp" => time()                   
        );

        return $headers;
    }

    
    function OCValidationCall()
    {
        /*
        $host = "https://test-gspk.orangeconnex.com:8443";
        $rawUrl =  "/api/shipment/package/v1/validation";

        $apiKey = "89ccb764-15c1-4a47-af0b-02628bae3529";
        $clientKey = "49de9e27-6f9d-496b-80ac-24fbf275fdf0";


        $authString =  $rawUrl . "|". $json . "|" . $apiKey .  "|" . $clientKey;
        */

        $con = new Consignment(594827);

        //echo "Message Id " . $con->getId();
        //echo "<br>";

        $parcelFilter = new ParcelFilter();
        $parcelFilter->addConsignmentIdFilter($con->getId());
        $parcelList = $parcelFilter->getList();

    
        if(count($parcelList) > 0)
        {
            $parcel = $parcelList[0];

            $countryId = $con->getCountryId();

            $country = new Country($countryId);

            $senderCountryId = $con->getSenderCountryId();

            $senderCountry = new Country($senderCountryId);

            $data = array(                  "MessageId" => $con->getId(), 
                                            "data" => array(
                                            "isEbayPlatform"=> 1,
                                            "serviceCode"=> "GS",
                                            "orderDate"=> "2021-04-25T12:04:00+0800", //date('Y-m-d\Th:m:s', strtotime("2021-04-25")) . '+0800',
                                            "consigneeFullName"=> "MKazim",
                                            "consigneePhone"=> $con->getTelephone(),
                                            "consigneeCountry"=> "CA", //$country->getIso(), 
                                            "consigneeState"=> "ON", //$con->getState(),
                                            "consigneeCity"=> "Sydney",  //$con->getCity(),
                                            "consigneeAddr1" => "13 Stonemeadow Dr.", //$con->getAddressLine1(),
                                            "consigneeZipCode"=> "K2M 2C8",//$con->getPostCode(),                                        
                                            "sellerFullName"=> $con->getSenderName(),
                                            "sellerPhone"=> "02088676060", //$con->getSenderTelephone(),
                                            "sellerCountry"=> "GB", //$senderCountry->getIso(),
                                            "sellerState"=> "London", //$con->getSenderState(),
                                            "sellerCity"=> $con->getSenderCity(),
                                            "sellerAddr1"=> "One World Express", //$con->getSenderAddressLine1(),
                                            "sellerZipCode"=> "UB3 3NB", //$con->getSenderPostCode(),
                                            "currency"=> "CAD",
                                            "battery"=> 0,
                                            "incoterm"=> 0,
                                            "importerGST" => "123456789",
                                            "packageTotalWeight"=> 500,
                                            "packageLength"=> 10.00,
                                            "packageWidth"=> 10.00,
                                            "packageHeight"=> 10.00,
                                            "packageTotalValue"=> 15.00,
                                            "itemInfoList"=> array(

                                                "sku"=> "X0012NJPC1",
                                                "skuDesc"=> "towel purple",
                                                "skuValue"=> 10.00,
                                                "currency"=> "CAD",
                                                "quantity"=> 1,
                                                "txnUnitPrice"=> 10.00,
                                                "txnQty"=> 1,
                                                "link" => "http://www.ebay.co.uk/itemid/123987401" 

                                            )
                                            
                                        
                                        
                                        ));
            
        }


        $json = json_encode($data);

        //echo $json;

    
        //echo $authString;
        $array = explode("|", $authString);
        //print_r($array);
        //echo "<br>";
        //echo "<br>";

        $rawUrl =  "/api/shipment/package/v1/validation";

        $authKey = GetAuthString($rawUrl, $json);

        //echo "<br><br>";
        //echo "Auth Key :"  . $authKey;

        $headers = SetHeaders($authKey);

        //echo cUrlGetData($rawUrl, $json, $headers);

    }



    function OCPackageLabelApi($ocTrackingNumber)
    {
        $rawUrl = "/api/shipment/package/v1/label";

        //$data = array(                  

        $data = array(

            "messageId" => "594827",
            "data" => array( 
                "ocTrackingNumber" => $ocTrackingNumber,
                "packageTotalWeight" => "0.5",
                "packageLength" => "40.00",
                "packageWidth" => "10.00",
                "packageHeight" => "10.00",
                "timeStamp" => time()
            )
        );

        $json = json_encode($data);

        $authKey = GetAuthString($rawUrl, $json);

        $headers = SetHeaders($authKey);

        echo "<br><br><br><br>";
        echo "Package Label Api";

        //$response = cUrlGetData($rawUrl, $json, $headers);
        //print_r($response);
        echo "<pre>";
        $result = json_decode($response, true);
        $pdf_content = $result["data"]["labelPDF"][0];
        $pdf_decoded = base64_decode ($pdf_content);
        //Write data back to pdf file
        $pdf = fopen ('oc_label.pdf','w');
        fwrite ($pdf,$pdf_decoded);
        //close output file
        fclose ($pdf);
        echo 'Done';

    }

    //echo time();
    
    

   

    die;


    

    //echo "test";

    //$ocBagLabel = new OcBagLabel();

    $skuOrder = new SkuOrder(73);
    
    $wms = new Wms();
    $result = $wms->CreateInboundBags($skuOrder);

    print_r($result);

    //echo "kasi";

    die;

    //require_once("../includes/labels/wms.class.php");

    //$GLOBALS['url'] = "https://www.smarttrack.co/api/";

    /*
    $sku = "abc001";

    $skuFilter = new SkuFilter();
    $where = array("sku" => $sku);
        $skuFilter->addFilter($where);
        print_r($skuFilter);
        $skuList = $skuFilter->getList();

    print_r($skuList);

    die;
    */

    $skuObj = new SKU();
    $skuObj->setSku("abc001");
    $skuObj->setDeclaredName("abc001");
    $skuObj->setDescription("abc001");
    $skuObj->setLength(10);
    $skuObj->setWidth(10);
    $skuObj->setHeight(10);
    //$skuObj->setNetWeight(0.5);
    //$skuObj->setPrice(10.00);
    //$skuObj->setCurrency("gbp");
    //$skuObj->setNotes("");
    $skuObj->setCustomerId(4724);
    $skuObj->setDateCreated(strtotime(date('Y-m-d H:i:s')));

    $skuObj->save();

    echo "<pre>";
    print_r($skuObj);

    die;



    //kazim check your code  ...
    //FetchOrders();

    //CreateLabelsForMarketPlaces();

    //SendMarketPlaceOrdersDataToWMS();

    //GetInventory();

    //CreateSKU();

    //ReadJsonToAddMarketPlaceOrders();

    /*
    function ReadJsonToAddMarketPlaceOrders()
    {
        $contents = file_get_contents("response.json");
        $json_array = json_decode($contents);
        print_r($json_array);
        
        die;
    }
	*/

    //TransferOfflineOrders();

    CreateInboundBag();

    function CreateInboundBag()
    {
        $client = new SoapClient('http://localhost:60154/WebService/SkuService.asmx?wsdl', array('trace' => true));
        $request = new stdClass();
        $request->ProcessCode = 'p3';
        $request->CustomerCode = 'BRANDS';
        $request->WarehouseCode = 'BM';
        $request->Weight = '1';
        $request->Length = '1';
        $request->Width = '1';
        $request->Height = '1';
        $request->Quantity = '2';
        $request->ExptectedArrivalTime = '2021-03-18T16:21:00';
        // $FbaContainerDetailDataModelList = new stdClass();
        try
        {
            for($i = 0 ; $i<2 ; $i++)
            {
                if($i == 0)
                {
                    $boxNumber = 'OWEBAG'.$i;
                }
                else
                {
                    $boxNumber = 'OWEBAG'.$i;
                }

                $FbaContainerDetailDataClass = new stdClass();
                $FbaContainerDetailDataClass->FbaId = $boxNumber;
                $FbaContainerDetailDataClass->Sku = 'SKUTEST001';
                $FbaContainerDetailDataClass->ExpectedQty = '1';
                $FbaContainerDetailDataClass->Length = '0.1';
                $FbaContainerDetailDataClass->Width = '0.1';
                $FbaContainerDetailDataClass->Height = '0.1';
                $FbaContainerDetailDataClass->Weight = '0.1';
                $FbaContainerDetailDataArr['FbaContainerDetailDataModel'][$i] = $FbaContainerDetailDataClass;
            }

            $request->FbaContainerDetailDataModelList = $FbaContainerDetailDataArr;
            $response = $client->CreateFbaCarton( array("request" => $request));
            print_r($response);
            echo "REQUEST:\n" . $client->__getLastRequest() . "\n";
      
        }
        catch (Exception $e)
        {
            print_r($e->getMessage());
        }
    }

    function TransferOfflineOrders()
    {
        error_reporting(E_ALL);
        ini_set('display_errors', '1');

        $strConsignmentId = "'204-5296895-8193112'";
        
        /*
        $key = "HDZDbvg3aQ8tpHdK";
        $content = "<td></td>";
        $md5 = md5($content . "2021-03-05 16:08" . $key);
        echo $md5;
        echo "<br>";
        $base64 = base64_encode($md5);
        echo $base64;
        die;
        */

        $consignmentFilter = new ConsignmentFilter();
        //$consignmentFilter->addFieldFilter('    user_id', '4724');
        //$consignmentFilter->addFilter('    date_created >= "2020-10-06 00:00:00" and date_created < "2020-10-07 00:00:00"');
        $consignmentFilter->addFilter("    hawb In($strConsignmentId) and shipment_status in ('13')");

        //print_r($consignmentFilter);

        //die;

        $result = $consignmentFilter->getListNew('*', '', false); /// debug false

        print_r($result);

        //die;

        foreach($result as $con)
        {
            $hawb = $con->getHawb();
            $ParcelDataModelObjArr = array();
            $count = 0;
            
            $country = new Country($con->getCountryId());
            $conIso = $country->getIso();
            $currency = $con->getCurrency();
            
            
            $parcelFilter = new ParcelFilter();
            $parcelFilter->addFieldFilter('    tracking_number',$con->getAwb());
            $parcelResult = $parcelFilter->getList();    
            
            
            if(count($parcelResult) > 0)
            {
                foreach($parcelResult as $parcel)
                {
                    $totalItems = $parcel->getNumberItem();
                    $itemsDescArray = json_decode($parcel->getDescription(), true);
                    $itemsQtyArray = json_decode($parcel->getQty(), true);
                    $itemsWeightArray = json_decode($parcel->getPWeight(), true);
                    $itemsValueArray = json_decode($parcel->getItemValue(), true);
                    $itemsSkuArray = json_decode($parcel->getItemSku(), true);

                    for($i=0; $i<count($itemsDescArray); $i++)
                    {
                        
                        $parcelDataModelClass = new stdClass();
                        $parcelDataModelClass->weight = $itemsWeightArray[$i];
                        $parcelDataModelClass->length = $parcel->getLength();
                        $parcelDataModelClass->width = $parcel->getWidth();
                        $parcelDataModelClass->height = $parcel->getHeight();
                        $parcelDataModelClass->sku = $itemsSkuArray[$i];
                        $parcelDataModelClass->description = $itemsDescArray[$i];
                        $parcelDataModelClass->value = $itemsValueArray[$i];
                        $parcelDataModelClass->currency = $currency;
                        $parcelDataModelClass->quantity = $itemsQtyArray[$i];
                        $ParcelDataModelObjArr['ParcelDataModel'][$count] =  $parcelDataModelClass;
                        $count++;


                    }


                }

            }
        
                        
            $service = new Services($con->getServiceId());
            $handlingCode = $service->getCode(); 

            $userId = $con->getUserId();



            
            /////////////////////////// Get User Account Name //////////////////////////////

            if($userId > 0)
            {
                $userObj = new User($userId);

                $userAccountId = $userObj->getUserAccountId();

                $userAccountObj = new CustomerAccount($userAccountId);

                $userAccountName =  $userAccountObj->getUserAccount();

            }


            try 
                {
                    $client = new SoapClient('http://wms-uk.oneworldexpress.cn/WebService/ShipmentService.asmx?wsdl', array('trace' => true));
                    $request = new stdClass();
                    $request->customerCode = $userAccountName;
                    $request->hawb = $con->getHawb();
                    $request->tracking_number = $con->getAwb();
                    $request->Reference = "";
                    $request->company = $con->getCompany();
                    $request->contact = $con->getContact();
                    $request->address_line_1 = $con->getAddressLine1();
                    $request->address_line_2 = $con->getAddressLine2();
                    $request->address_line_3 = $con->getAddressLine3();
                    $request->city = $con->getCity();
                    $request->postcode = $con->getPostCode();
                    $request->country_iso = $conIso;
                    $request->state = "";
                    $request->email = $con->getEmail();
                    $request->telephone = $con->getTelephone();
                    $request->value = $con->getValue();
                    $request->currency = $con->getCurrency();
                    $request->notes = $con->getNotes();
                
                    $request->servicecode = $handlingCode;
                    $request->weight = $con->getWeight();
                    $request->marketPlaceId = $marketPlaceName;
                    $request->label = 'https://www.smarttrack.co/_assets/pdf/'.$con->getlabelFile();
                    $request->consignmentNo = $con->getId(); 
                    //echo
                    $count = 0;

                    echo $userAccountName;

                    //die;
                    
    
                    
                    $request->ParcelData = $ParcelDataModelObjArr;
                    //die;
                    $response = $client->CreateShipment( array("request" => $request));
                    echo "<br>";
                    echo "sent Data To WMS";
                    print_r($response);
                    echo "REQUEST:\n" . $client->__getLastRequest() . "\n";                
                } 
                catch (Exception $e) 
                {
                    echo $e->getMessage();      
                }
            
            

        }
    }

	//SkipLastDigit();
	
	function SkipLastDigit()
	{
		$array = array('552006227893',
		'552006246016',
		'552006304129',
		'552006304662',
		'552006304679',
		'552006304808',
		'552006304952',
		'552006305270',
		'552006305348',
		'552006305355',
		'552006305836',
		'552006306024',
		'552006306161',
		'552006306413',
		'552006306567',
		'552006306741',
		'552006306970',
		'552006307335',
		'552006307366',
		'552006307403',
		'552006307434',
		'552006307472',
		'552006307595',
		'552006307663',
		'552006307731',
		'552006307939',
		'552006308141',
		'552006308264',
		'552006308448',
		'552006308608',
		'552006308684',
		'552006308974',
		'552006309032',
		'552006309278',
		'552006309391',
		'552006309414',
		'552006309636',
		'552006309681',
		'552006309759',
		'552006309834',
		'552006310076',
		'552006310700',
		'552006310724',
		'552006310915',
		'552006311059',
		'552006311080',
		'552006311141',
		'552006311219',
		'552006311622',
		'552006311660',
		'552006311899',
		'552006312056',
		'552006312087',
		'552006312247',
		'552006312278',
		'552006312285',
		'552006312483',
		'552006312612',
		'552006313275',
		'552006313527',
		'552006313824',
		'552006313916',
		'552006314210',
		'552006314357',
		'552006314524',
		'552006314531',
		'552006314975',
		'552006315026',
		'552006315248',
		'552006315309',
		'552006315354',
		'552006315361',
		'552006315408',
		'552006315576',
		'552006315606',
		'552006315675',
		'552006315804',
		'552006315842',
		'552006315903',
		'552006316054',
		'552006316085',
		'552006316207',
		'552006316276',
		'552006316481',
		'552006316573',
		'552006316610',
		'552006316665',
		'552006316771',
		'552006316849',
		'552006317167',
		'552006317235',
		'552006317778',
		'552006318065',
		'552006318461',
		'552006318515',
		'552006318553',
		'552006318737',
		'552006318768',
		'552006318836',
		'552006319673',
		'552006319796',
		'552006320273',
		'552006320464',
		'552006320563',
		'552006320631',
		'552006320730',
		'552006320839',
		'552006320938');

		foreach($array as $str)
		{
			echo substr($str, 0, 11);
			echo "<br>";
			//$newArray[] = substr($str, 0, 11);
		}

		//print_r($newArray);

		die;
	}

    //ReadAmazonProductJson();

    function ReadAmazonProductJson()
    {
        $json_data = file_get_contents("response_products.json");
        $products = json_decode($json_data, true);
        $productArray = $products["result"]["product"];
        $productList = array();
        print_r($productArray["result"]["product"]);

        $count = 0;
        foreach($productArray as $product)
        {
           
            $productList[$count]["sku"] = $product["u_sku"];
            $productList[$count]["name"] = $product["name"];
            $productList[$count]["description"] = $product["description"];
            $productList[$count]["notes"] = "";
            $productList[$count]["weight"] = 0.5;
            $productList[$count]["price"] = $product["price"]; 
            $productList[$count]["currency"] = 'GBP'; 
            $productList[$count]["hscode"] = $product["hscode"]; 
            $productList[$count]["length"] = 1; 
            $productList[$count]["width"] = 1; 
            $productList[$count]["height"] = 1; 
            $productList[$count]["active"] = 1; 

            $images = $product["images"];
            if(count($images) > 0)
            {
                $path = $images[0]["http_path"];
                $productList[$count]["image"] = $path;
            }  
            $count++;
            
        }

        

        $fp = fopen('amazon.csv', 'w');

        foreach ($productList as $fields) {
            fputcsv($fp, $fields);
        }

        fclose($fp);

        

        print_r($productList);
        die;  


    }

    function FetchOrders()
    {
        $userMarketPlaceMappingFilter = new UserMarketPlacesMappingFilter();
        $userMarketPlaceMappingFilter->addFieldFilter("user_account_id", 4778); // Brands
        $userMarketPlaceMappingFilter->addFieldFilter("market_places_id", 1); // Amazon
        $userMarketPlaceMappingObj = $userMarketPlaceMappingFilter->getList();

            if (count($userMarketPlaceMappingObj) > 0) 
            {
                $marketPlaces = new MarketPlaces($userMarketPlaceMappingObj[0]->getMarketPlacesId());
                include_classes([
                    strtolower($marketPlaces->getClassName()) . '.class',
                ]);
                $className = ucwords($marketPlaces->getClassName());
                //$authdata = json_decode($userMarketPlaceMappingObj[0]->getAuthData());
                if (!empty($className)) {
                    $marketPlaceClassObj = new $className($userMarketPlaceMappingObj[0]);
                    $result = $marketPlaceClassObj->fetchOrders();
                } else {
                    $result["STATUS"] = "ERROR";
                    $result["MESSAGE"] = "Market place is not configured.";
                }
            }
    }

    function CreateSKU()
    {
        try 
        {
            $client = new SoapClient('http://wms-uk.oneworldexpress.cn/WebService/SkuService.asmx?wsdl', array('trace' => true));
            $request = new stdClass();
            $request->Active = "Y";
            $request->SKU = "X00198HSTV";
            $request->CustomerID = "OLYMPIA";
            $request->SKU_Ref1 = "B07P6749TK";
            $request->WarehouseCode = "BM";
            $request->Hazard_Flag = '';
            $request->Active_Flag = '1';
            $request->Description  = 'Disposal Bag 90';
            $request->DeclaredNameEN = 'Disposal Bag 90';
            $request->DeclaredNameCN = '';
            $request->GrossWeight = '1';
            $request->NetWeight = '1.0';
            $request->Tare = "1";
            $request->Cube = "1.0";
            $request->Price = "1";
            $request->Length = "1";
            $request->Width = "1";
            $request->Height = "1";
        
            $request->Image = '';
            $request->HSCode = '';
            $request->FirstOP = '';
            print_r($request); //die;
            $response = $client->CreateSku( array("request" => $request));
            print_r($response);
            echo "REQUEST:\n" . $client->__getLastRequest() . "\n";                
        } 
        catch (Exception $e) 
        {
            echo $e->getMessage();      
        }
    }


    function GetInventory()
    {
        $client = new SoapClient('http://wms-uk.oneworldexpress.cn/WebService/SkuService.asmx?wsdl', array('trace' => true));
        $request = new stdClass();
        //$request->CustomerCode = "BRANDS";
        $request->PeriodDataModel = new stdClass();
        //$request->CreateOn->
        $request->CreateTime->BeginDateTime  = "2020-12-01";
        $request->CreateTime->EndDateTime  = "2020-12-02";

        $request->PageIndex = 1;
        $request->PageSize = 20;


        $response = $client->GetInventory( array("request" => $request));

        print_r($response);
                        
                        
        if($response->CreateFbaCartonResult->ErrorMsg != "")
        {
            $results['STATUS'] = 'ERROR';
            $results['ERROR'] = $response->CreateFbaCartonResult->ErrorMsg;
            $results['MESSAGE'] = $response->CreateFbaCartonResult->ErrorMsg;
        }

        

    }


    function SendMarketPlaceOrdersDataToWMS()
    {
        $userAccountName = "";
        $marketPlaceName = "";
        $rsDetail = "";

        //$date = date("Y-m-d");
        $date = "2021-01-11";
        //echo $date;

        //$date = "2020-11-17";

        
        $marketPlaceOrderFilter = new MarketPlaceOrderFilter();
        //$marketPlaceOrderFilter->addFilter("    marketplace_order_number in( '204-6134022-6635518')");

		$marketPlaceOrderFilter->addFilter("    consignment_id > 0 and order_status = 'Unshipped' and create_time >= '" . $date . "'");
        //$marketPlaceOrderFilter->addFilter("    consignment_id > 0 and order_status = 'Unshipped' and create_time >= '" . $date . "' and create_time <= '" . date('Y-m-d', strtotime($date. ' + 1 days')) . "'");
		/*
		$marketPlaceOrderFilter->addFilter("    marketplace_order_number in( '204-5859927-7943519',
        '203-5005614-2827525',
        '206-1653071-6182757',
        '205-5760411-2644332',
        '205-2544335-4990715',
        '204-9458188-1328314',
        '203-9758761-4224306',
        '203-8269414-2680300',
        '203-1787079-1496348',
        '203-1137542-7694713',
        '202-9871629-5116329',
        '202-9069100-4284348',
        '202-0766717-9500353',
        '026-4086599-9156361',
		'204-1704405-1617127')");
		*/
        $marketPlaceOrderResult = $marketPlaceOrderFilter->getList();

        print_r($marketPlaceOrderFilter);

        $conIdArray = array();

        if(count($marketPlaceOrderResult) > 0)
        {
            foreach($marketPlaceOrderResult as $marketPlaceOrder)
            {
                $conIdArray[] = $marketPlaceOrder->getConsignmentId() ;
            }
        }

        print_r($conIdArray);
        //die;

        if(count($conIdArray) > 0)
        {
            $strConsignmentId = "'" . implode("','", $conIdArray) . "'";


            $consignmentFilter = new ConsignmentFilter();
            //$consignmentFilter->addFieldFilter('    user_id', '4724');
            //$consignmentFilter->addFilter('    date_created >= "2020-10-06 00:00:00" and date_created < "2020-10-07 00:00:00"');
            $consignmentFilter->addFilterNew("    ID In($strConsignmentId)");

            //print_r($consignmentFilter);

            $result = $consignmentFilter->getListNew('*', '', false); /// debug false

            //print_r($result);

            
            foreach($result as $con)
            {
                $hawb = $con->getHawb();

                
                $country = new Country($con->getCountryId());
                $conIso = $country->getIso();
                
                
                $parcelFilter = new ParcelFilter();
                $parcelFilter->addFieldFilter('    tracking_number',$con->getAwb());
                $parcelResult = $parcelFilter->getList();
            
                $pweight = $parcelResult[0]->getWeight();
                $pheight = $parcelResult[0]->getHeight();
                $pwidth = $parcelResult[0]->getWidth();
                $plength = $parcelResult[0]->getLength();
                
                $service = new Services($con->getServiceId());
                $handlingCode = $service->getCode(); 

                $userId = $con->getUserId();

                
                /////////////////////////// Get User Account Name //////////////////////////////

                if($userId > 0)
                {
                    $userObj = new User($userId);

                    $userAccountId = $userObj->getUserAccountId();

                    $userAccountObj = new CustomerAccount($userAccountId);

                    $userAccountName =  $userAccountObj->getUserAccount();

                }

                ////////////////////////////////////////////////////////////////////////////////

                ////////////////////////////////// Get Market Place Name ///////////////////////

                $marketPlaceOrderFilterCon = new MarketPlaceOrderFilter();
                $marketPlaceOrderFilterCon->addFilter("    consignment_id =  " . $con->getId());
                $marketPlaceOrderConResult = $marketPlaceOrderFilterCon->getList();

                if(count($marketPlaceOrderConResult) > 0)
                {
                    //print_r($marketPlaceOrderConResult);

                    $marketPlaceOrderCon = $marketPlaceOrderConResult[0];
                    $marketPlaces = new MarketPlaces($marketPlaceOrderCon->getMarketPlaceId());
                    $marketPlaceName = $marketPlaces->getTitle();

                    echo "Order Id " .  $marketPlaceOrderCon->getId();

                    $mkpOrderDetailsFilter = new MarketPlaceOrderDetailsFilter();
                    $rsDetail = $mkpOrderDetailsFilter->GetOrderDetailByMarketPlaceId($marketPlaceOrderCon->getId());

                    //print_r($rsDetail);

                    //die;

                    /*

                    $mkpOrderDetailsFilter = new MarketPlaceOrderDetailsFilter();
                    $mkpOrderDetailsFilter->addFieldFilter('    marketplace_order_id', $marketPlaceOrderCon->getId());
                    $rsDetail = $mkpOrderDetailsFilter->getList();

                    */

                    //print_r($rsDetail);

                    //die;
                  



                    ///print_r($marketPlaceName);
                    //die;
           
            
                }


                //////////////////////////////////////////////////////////////////////////////////////////////

                echo "Sending Data To WMS";
                echo "account name " .  $userAccountName;
                error_reporting(E_ALL);
                ini_set('display_errors', 1);


                
                try 
                {
                    $client = new SoapClient('http://wms-uk.oneworldexpress.cn/WebService/ShipmentService.asmx?wsdl', array('trace' => true));
                    $request = new stdClass();
                    $request->customerCode = $userAccountName;
                    $request->hawb = $con->getHawb();
                    $request->tracking_number = $con->getAwb();
                    $request->Reference = "";
                    $request->company = $con->getCompany();
                    $request->contact = $con->getContact();
                    $request->address_line_1 = $con->getAddressLine1();
                    $request->address_line_2 = $con->getAddressLine2();
                    $request->address_line_3 = $con->getAddressLine3();
                    $request->city = $con->getCity();
                    $request->postcode = $con->getPostCode();
                    $request->country_iso = $conIso;
                    $request->state = "";
                    $request->email = $con->getEmail();
                    $request->telephone = $con->getTelephone();
                    $request->value = $con->getValue();
                    $request->currency = $con->getCurrency();
                    $request->notes = $con->getNotes();
                
                    $request->servicecode = $handlingCode;
                    $request->weight = $con->getWeight();
                    $request->marketPlaceId = $marketPlaceName;
                    $request->label = 'https://www.smarttrack.co/_assets/pdf/'.$con->getlabelFile();
                    $request->consignmentNo = $con->getId(); 
                    //echo
                    $count = 0;

                    echo $userAccountName;

                    //die;
                    
    
                    foreach($rsDetail as $sku)
                    {
                        $parcelDataModelClass = new stdClass();
                        $parcelDataModelClass->weight = $pweight;
                        $parcelDataModelClass->length = $plength;
                        $parcelDataModelClass->width = $pwidth;
                        $parcelDataModelClass->height = $pheight;
                        $parcelDataModelClass->sku = $sku->getSku();
                        $parcelDataModelClass->description = $sku->getTitle();
                        $parcelDataModelClass->value = $sku->getItemPrice();
                        $parcelDataModelClass->currency = $sku->getCurrency();
                        $parcelDataModelClass->quantity = $sku->getQuantityPurchased();
                        $ParcelDataModelObjArr['ParcelDataModel'][$count] =  $parcelDataModelClass;
                        $count++;
                    }
                    $request->ParcelData = $ParcelDataModelObjArr;
                    print_r($request); 
                    //die;
                    $response = $client->CreateShipment( array("request" => $request));
                    echo "<br>";
                    echo "sent Data To WMS";
                    print_r($response);
                    echo "REQUEST:\n" . $client->__getLastRequest() . "\n";                
                } 
                catch (Exception $e) 
                {
                    echo $e->getMessage();      
                }
            
        }

                        

        }

        die;
        
        //echo count($result); die;
        
    }


    function CreateLabelsForMarketPlaces()
    {
        $marketPlaceOrderFilter = new MarketPlaceOrderFilter();
        $marketPlaceOrderFilter->addFilter("    (consignment_id is null or consignment_id = 0)  and order_status = 'Unshipped' and marketplace_id = 1 and marketplace_order_number like '%-%'");
        $marketPlaceOrderResult = $marketPlaceOrderFilter->getList();

        echo "<br> Total Amazon Orders : " . count($marketPlaceOrderResult) . "<br>";
        //die;

        print_r($marketPlaceOrderFilter);
        //die;

        foreach($marketPlaceOrderResult as $marketPlaceOrder)
        {
            GetLabel($marketPlaceOrder);
        }
    }

    function InsertMarketPlaceOrder($consignment)
    {
        $marketPlaceOrderFilter = new MarketPlaceOrderFilter();
        $marketPlaceOrderDetailFilter = new MarketPlaceOrderDetailsFilter();
        $marketPlaceOrderDetailFilter->addFieldFilter("marketplace_order_number", $consignment->getHawb());
        $marketPlaceOrderDetailFilterResult = $marketPlaceOrderDetailFilter->getList();

        if(count($marketPlaceOrderDetailFilterResult) == 0)
        {
            $marketPlaceOrder = new MarketPlaceOrder();
            $marketPlaceOrder->setMarketPlaceOrderNumber($consignment->getHawb());
            $marketPlaceOrder->setCreateTime(date("Y-m-d G:i:s"));
            $marketPlaceOrder->setOrderStatus("UnShipped");
            $marketPlaceOrder->setReceiverName($consignment->getContact());
            $marketPlaceOrder->setReceiverAddressLine1($consignment->getAddressLine1());
            $marketPlaceOrder->setReceiverAddressLine2($consignment->getAddressLine2());
            $marketPlaceOrder->setReceiverPostCode($consignment->getPostCode());
            $marketPlaceOrder->setReceiverEmail($consignment->getEmail());
            $marketPlaceOrder->setConsignmentId($consignment->getId());
            $marketPlaceOrder->setUserId($consignment->getUserId());
            $marketPlaceOrder->setReceiverPhone($consignment->getTelephone());
            $marketPlaceOrder->setReceiverState($consignment->getState());
            $marketPlaceOrder->setReceiverCity($consignment->getCity());
            $marketPlaceOrder->setReceiverCountryId($consignment->getCountryId());
            $marketPlaceOrder->setMarketPlaceId(2);
            $marketPlaceOrderId = $marketPlaceOrder->save();

            if($marketPlaceOrderId > 0)
            {
                echo "MarketPlace Order " . $marketPlaceOrderId . "saved.";
                echo "<br>";

                $items = $consignment->getItems();

                if(count($items) > 0){
                    foreach ($items as $item){
                        $itemValue = $item['item_value'];
                        $marketPlaceItemId = $item['item_sku'];
                        $title = $item['item_description'];
                        $quantityPurchased = $item['no_of_items'];

                        $marketPlaceOrderDetail = new MarketPlaceOrderDetail();
                        $marketPlaceOrderDetail->setMarketPlaceOrderId($marketPlaceOrderId);
                        $marketPlaceOrderDetail->setItemPrice($itemValue);
                        $marketPlaceOrderDetail->setCurrency($consignment->getCurrency());
                        $marketPlaceOrderDetail->setQuantityPurchased($quantityPurchased);
                        $marketPlaceOrderDetailId = $marketPlaceOrderDetail->save();

                        echo "MarketPlace Order $marketPlaceOrderDetailId saved.";                      
                       
                    }
                }
                else
                {
                    echo "MarketPlaceOrderDetail not saved.";

                }

            }
            else
            {
                echo "MarketPlace Order not saved.";
            }


        }
    }


    function GetLabel($marketPlaceOrder)
    {
        //print_r($marketPlaceOrder);
        $marketPlaceOrderDetailFilter = new MarketPlaceOrderDetailsFilter();
        $marketPlaceOrderDetailFilter->addFieldFilter("marketplace_order_id", $marketPlaceOrder->getId());
        $marketPlaceOrderDetailFilterResult = $marketPlaceOrderDetailFilter->getList();

        if(count($marketPlaceOrderDetailFilterResult) > 0)
        {
            $curl = curl_init();

            $marketPlaceOrderDetail = $marketPlaceOrderDetailFilterResult[0];
            $skuName =  trim($marketPlaceOrderDetail->getSku());
            $service = "";

            $skuFilter = new SkuFilter();
            $skuFilter->addFilter("sku = '" . $marketPlaceOrderDetail->getSku() . "'");
            $skuFilterResult = $skuFilter->getList();



            if(count($skuFilterResult) > 0)
            {

                $skuObj = $skuFilterResult[0];

                $length = $skuObj->getLength();
                $width = $skuObj->getWidth();
                $height = $skuObj->getHeight();
                $weight = $skuObj->getGrossWeight();



                if($length <= 0)
                {
                    $length = 1;
                }
                if($width <= 0)
                {
                    $width = 1;
                }
                if($height <= 0)
                {
                    $height = 1;
                }
                if($weight <= 0)
                {
                    $weight = 0.5;
                }
                // quantity
                
            }
            else
            {
                $length = 1;
                $width = 1;
                $height = 1;
                $weight = 0.5;

            }



            $quantity = $marketPlaceOrderDetail->getQuantityPurchased();






            $parcelList = array();
            $i = 0;

            //for($i=0;$i<$quantity;$i++)
            {
                $parcel = array(

                    "length" => $length,
                    "width" => $width,
                    "height" => $height,
                    "weight" => $weight

                );

                $parcelList[$i] = $parcel;
            }





            if($skuName == 'B2Y-HEA-2WB' || 
               $skuName == 'B2Y-HEA-2WE' || 
               $skuName == 'B2Y-HEA-2WD' ||
               $skuName == 'B2Y-HEA-2W')
            {
                if($quantity == 1 || $quantity == 2)
                    $service = "STYDL2CXN";
                elseif($quantity >= 3)
                {
                    $service = "STYDL02CP";
                }

            }
            elseif($skuName == '0F-P387-10PD')
            {
                $service = "STYDL02CP";
            }
            elseif($skuName == 'ES-GSAU-7XA8')
            {
                if($quantity <= 5)
                {
                    $service = "STYDL2CXN";
                    //$service = "STYDL02CP";
                }
                else
                {
                    $service = "STYDL02CP";
                }
            }
            elseif($skuName == 'QI-YYGF-1EMR')
            {
                if($quantity <= 2)
                {
                    $service = "STYDL2CXN";
                }
                else
                {
                    $service = "STYDL02CP";
                }
            }
            elseif($skuName == '8R-9GRD-ZS6W')
            {
                if($quantity <= 2)
                {
                    $service = "STYDL2CXN";
                }
                else
                {
                    $service = "STYDL02CP";
                }
            }
            elseif($skuName == 'FH-VR2G-17VJ')
            {
                if($quantity <= 3)
                {
                    $service = "STYDL2CXN";
                }
                else
                {
                    $service = "STYDL02CP";
                }
            }
            elseif($skuName == '13-YAA9-I6ZP')
            {
                if($quantity == 1)
                {
                    $service = "STYDL2CXN";
                }
                else
                {
                    $service = "STYDL02CP";
                }
            }
            
            else if($skuName == "0700461659273")
            {
                $service = "STYDL2CXN";
            }
            elseif($skuName == "SU0006813")
            {
                $service  = "STYDL2CXN";
            }
            elseif($skuName == "B2Y-HT-001")
            {
                $service  = "STYDL2CXN";
            }
            else
            {
                $service = "STYDL02CP";
                //echo $skuName . " not found <br>";
                //die;
            }

            echo "Order Number || Quantity || Service" . "<br>";
            echo $marketPlaceOrder->getMarketPlaceOrderNumber() . " " . $quantity . " " . $service . "<br>";


            if($service != "")
            {
                $countryId = $marketPlaceOrder->getReceiverCountryId();

                $country = new Country($countryId);

                $receiverCountryIso = $country->getIso();

                //print_r($skuFilterResult);
                //die;

                $postCode = str_replace("  ", " ", $marketPlaceOrder->getReceiverPostCode());
                //echo "PostCode " .  $postCode;
                //die;
                    

                
                $request = array(

                    "order_reference" => $marketPlaceOrder->getMarketPlaceOrderNumber(),
                    "reference" => "",
                    "service_code" => $service,

                    "sender_country_iso" => "GB",
                    "sender_contact" =>  "Brands",
                    "sender_address_line_1" =>  "One World House, Pump Ln",
                    "sender_address_line_2" =>  "Hayes",
                    "sender_address_line_3" =>  "",
                    "sender_city" =>  "London",
                    "sender_postcode" =>  "UB3 3NB",

                    "receiver_contact" =>  $marketPlaceOrder->getReceiverName(),
                    "receiver_address_line_1" =>  $marketPlaceOrder->getReceiverAddressLine1(),
                    "receiver_address_line_2" =>  $marketPlaceOrder->getReceiverAddressLine2(),                     
                    "receiver_city" =>  $marketPlaceOrder->getReceiverCity(),
                    "receiver_state" =>  $marketPlaceOrder->getReceiverState(),
                    "receiver_postcode" =>  $postCode,
                    "receiver_country_iso" =>  $receiverCountryIso,
                    "receiver_telephone" =>  $marketPlaceOrder->getReceiverPhone(),
                    "receiver_email" =>  $marketPlaceOrder->getReceiverEmail(),

                    "description" =>  $marketPlaceOrderDetail->getTitle(),
                    "value" => $marketPlaceOrderDetail->getItemPrice(),
                    "currency" =>  $marketPlaceOrderDetail->getCurrency(),

                    "parcel" => $parcelList
                );
                
                curl_setopt($curl, CURLOPT_HTTPHEADER, array( 'Content-Type: application/json',
                                                            'Authorization: Bearer ' . GetToken())
                );  
                
                //echo $GLOBALS['url'] . "generate-label";

                echo json_encode($request);

                //die;
                
                curl_setopt($curl, CURLOPT_POST, 1);
                curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($request));
                curl_setopt($curl, CURLOPT_URL, $GLOBALS['url'] . "v2/generate-label");
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);

                $result = curl_exec($curl);
                echo "<pre>";
                print_r($result);
                if(!$result)
                {
                    die("Connection Failure");
                }
                else
                {
                    $resultArray = json_decode($result, true);
                    print_r($resultArray);
                    if($resultArray["sucess"] == "true")
                    {
                        $id = $resultArray["data"]["id"];
                        $conFilter = new ConsignmentFilter();
                        $conFilter->addFilterNew("    id = '" . $id);
                        $conList = $conFilter->getList();
                        if(count($conList) > 0)
                        {
                            $con = $conList[0];
                            $marketPlaceOrder->setConsignmentId($id);
                            $marketPlaceOrder->save();
                            echo "$id id saved";
                            //die;
                        }
                        
                        
                        //die;
                    }
                    else
                    {
                        $error = $resultArray["errors"][0];
                        //echo $error;
                        $conFilter = new ConsignmentFilter();
                        $conFilter->addFilterNew("    hawb = '" . $marketPlaceOrder->getMarketPlaceOrderNumber() . "'");
                        $conList = $conFilter->getList();
                        if(count($conList) > 0)
                        {
                            $con = $conList[0];
                            //echo $con->getLabelFile();
                            $id = pathinfo($con->getLabelFile(), PATHINFO_FILENAME);
                            echo $id;
                            //die;
                            //print_r($con);
                            $marketPlaceOrder->setConsignmentId($id);
                            $marketPlaceOrder->save();
                            //die;
                        }


                        //$marketPlaceOrder->setConsignmentId();
                        //die;
                    }

                    //$token = $resultArray["access_token"];
                }

                
                curl_close($curl);
                    
        
                print_r($request);
            }
            
            


            
            //die;

                    //$result = curl_exec($curl);


            

            
        }
        //print_r($marketPlaceOrderDetailFilterResult);
        //die;
        
    }

    function GetToken()
    {
        $token = "";
        $curl = curl_init();
        $auth_data = array(
            'client_id'         => 'eac4802b06553be613bc4633a745db37',
            'client_secret'     => '3e9e793952e7e484b05d6ba1211a31f5',
            'grant_type'        => 'client_credentials'
        );

        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $auth_data);
        curl_setopt($curl, CURLOPT_URL, $GLOBALS['url'] . "token");
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        $result = curl_exec($curl);
        print_r($result);
        if(!$result)
        {
            die("Connection Failure");
        }
        else
        {
            $resultArray = json_decode($result, true);
            $token = $resultArray["access_token"];
        }

        curl_close($curl);

        return $token;
    }

    

    
    //print_r($marketPlaceOrderResult);
    die;

    

    /*
    
    require_once("../includes/labels/wmsfbo.class.php");


    $wmsfbo = New WMSFBO();
    $consignment = new Consignment(378871);
    $fboResult = $wmsfbo->label($consignment,'pdf',''); 
                       
    /*
    $skuBoxDetailFilter = new SkuBoxDetailFilter();
    $skuBoxDetailFilter->where('sku_order_id = 5');
    $bagList = $skuBoxDetailFilter->getList("bag_number");
    
    print_r($bagList);
        
    
    /*
    
    try
{
//$sku = new Sku(16);
$client = new SoapClient('http://wms-uk.oneworldexpress.cn/WebService/SkuService.asmx?wsdl', array('trace' => true));
$request = new stdClass();
$request->ProcessCode = 'p3';
$request->CustomerCode = 'BRANDS';
$request->WarehouseCode = 'BM';
$request->Weight = '1';
$request->Length = '1';
$request->Width = '1';
$request->Height = '1';
$request->Quantity = '2';
$request->ExptectedArrivalTime = '2020-10-19T10:38:00';
// $FbaContainerDetailDataModelList = new stdClass();
for($i = 0 ; $i<2 ; $i++)
{
if($i == 0)
{
$boxNumber = 'OWEBAG1';
}
else
{
$boxNumber = 'OWEBAG2';
}
$FbaContainerDetailDataClass = new stdClass();
$FbaContainerDetailDataClass->FbaId = $boxNumber;
$FbaContainerDetailDataClass->Sku = '1';
$FbaContainerDetailDataClass->ExpectedQty = '1';
$FbaContainerDetailDataClass->Length = '0.1';
$FbaContainerDetailDataClass->Width = '0.1';
$FbaContainerDetailDataClass->Height = '0.1';
$FbaContainerDetailDataClass->Weight = '0.1';
$FbaContainerDetailDataArr['FbaContainerDetailDataModel'][$i] = $FbaContainerDetailDataClass;
}
$request->FbaContainerDetailDataModelList = $FbaContainerDetailDataArr;
$response = $client->CreateFbaCarton( array("request" => $request));
print_r($response);
echo "REQUEST:\n" . $client->__getLastRequest() . "\n";
}
catch (Exception $e)
{
print_r($e->getMessage());
}
die;
*/
    /*  
    $skuOrderId = "106";
    //die;
    try
                {
                    $client = new SoapClient('http://wms-uk.oneworldexpress.cn/WebService/SkuService.asmx?wsdl', array('trace' => true));

                    $skuorder = new SkuOrder($skuOrderId);
                    $warehouseId = $skuorder->getWarehouseId();
                    
                                    
                    $userObj = new User($skuorder->getUserId());
                    $userAccountId = $userObj->getUserAccountId();              
                    $userAccountObj = new CustomerAccount($userAccountId);
                    $userAccount = $userAccountObj->getUserAccount();
                    
                    
                    $warehouseObj = new Warehouse($warehouseId);
                    $warehouseCode = $warehouseObj->getWarehouseCode();
                    
                    $warehouseCode2Digit = "";
                    
                    if($warehouseCode == 'BHX')
                        $warehouseCode2Digit = "BM";
                    
                        

                    $request = new stdClass();
                    $request->ProcessCode = $skuorder->getShipmentReference();
                    $request->CustomerCode = $userAccountObj->getUserAccount();
                    $request->WarehouseCode = $warehouseCode2Digit;
                    $request->Weight = '1';
                    $request->Length = '1';
                    $request->Width = '1';
                    $request->Height = '1';
                    $request->Quantity = '2';
                    $request->ExptectedArrivalTime = date('Y-m-d');
                    
                    $skuBoxDetailFilter = new SkuBoxDetailFilter();
                    $skuBoxDetailFilter->addFilter(" sku_order_id = '".$skuOrderId."'");
                    $skuBoxDetailList = $skuBoxDetailFilter->getList();
                    
                    
                    if(count($skuBoxDetailList) > 0)
                    {
                        $FbaContainerDetailDataArr = array();
                        $i = 0;
                        
                        foreach ($skuBoxDetailList as $skuBox) 
                        {
                            $skuId = $skuorder->getSkuId();
                            $skuObj = new Sku($skuId);
                            $FbaContainerDetailDataClass = new stdClass();
                            $FbaContainerDetailDataClass->FbaId = $skuBox->getBagNumber();
                            $FbaContainerDetailDataClass->Sku = $skuObj->getSku();
                            $FbaContainerDetailDataClass->ExpectedQty = $skuBox->getNumberPieces();
                            $FbaContainerDetailDataClass->Length = $skuBox->getBoxLength();
                            $FbaContainerDetailDataClass->Width = $skuBox->getBoxWidth();
                            $FbaContainerDetailDataClass->Height = $skuBox->getBoxHeight();
                            $FbaContainerDetailDataClass->Weight = $skuBox->getBoxWeight();
                            $FbaContainerDetailDataArr['FbaContainerDetailDataModel'][$i++] =  $FbaContainerDetailDataClass;
                            
                        }
                        
                        
                        $request->FbaContainerDetailDataModelList = $FbaContainerDetailDataArr;
                                                
                        $response = $client->CreateFbaCarton( array("request" => $request));
                        
                        
                        if($response->CreateFbaCartonResult->ErrorMsg != "")
                        {
                            $results['STATUS'] = 'ERROR';
                            $results['ERROR'] = $response->CreateFbaCartonResult->ErrorMsg;
                            $results['MESSAGE'] = $response->CreateFbaCartonResult->ErrorMsg;
                        }
                        
                        
                    }
                }
                catch(Exception $e)
                {
                    $results['STATUS'] = 'ERROR';
                    $results['ERROR'][] = $e;
                    $results['MESSAGE'] = $e;
                            
                }

*/

class eBaySession
{
	private $requestToken;
	private $devID;
	private $appID;
	private $certID;
	private $serverUrl;
	private $compatLevel;
	private $siteID;
	private $verb;
	
	/**	__construct
		Constructor to make a new instance of eBaySession with the details needed to make a call
		Input:	$userRequestToken - the authentication token fir the user making the call
				$developerID - Developer key obtained when registered at http://developer.ebay.com
				$applicationID - Application key obtained when registered at http://developer.ebay.com
				$certificateID - Certificate key obtained when registered at http://developer.ebay.com
				$useTestServer - Boolean, if true then Sandbox server is used, otherwise production server is used
				$compatabilityLevel - API version this is compatable with
				$siteToUseID - the Id of the eBay site to associate the call iwht (0 = US, 2 = Canada, 3 = UK, ...)
				$callName  - The name of the call being made (e.g. 'GeteBayOfficialTime')
		Output:	Response string returned by the server
	*/
	public function __construct($userRequestToken, $developerID, $applicationID, $certificateID, $serverUrl,
								$compatabilityLevel, $siteToUseID, $callName)
	{
		$this->requestToken = $userRequestToken;
		$this->devID = $developerID;
		$this->appID = $applicationID;
		$this->certID = $certificateID;
		$this->compatLevel = $compatabilityLevel;
		$this->siteID = $siteToUseID;
		$this->verb = $callName;
        $this->serverUrl = $serverUrl;	
	}	
	
	/**	sendHttpRequest
		Sends a HTTP request to the server for this session
		Input:	$requestBody
		Output:	The HTTP Response as a String
	*/
	public function sendHttpRequest($requestBody)
	{
		//build eBay headers using variables passed via constructor
		$headers = $this->buildEbayHeaders();
		
		//initialise a CURL session
		$connection = curl_init();
//		print_r
		//set the server we are using (could be Sandbox or Production server)
		curl_setopt($connection, CURLOPT_URL, $this->serverUrl);
		
		//stop CURL from verifying the peer's certificate
		curl_setopt($connection, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($connection, CURLOPT_SSL_VERIFYHOST, 0);
		
		//set the headers using the array of headers
		curl_setopt($connection, CURLOPT_HTTPHEADER, $headers);
		
		//set method as POST
		curl_setopt($connection, CURLOPT_POST, 1);
		
		//set the XML body of the request
		curl_setopt($connection, CURLOPT_POSTFIELDS, $requestBody);
		
		//set it to return the transfer as a string from curl_exec
		curl_setopt($connection, CURLOPT_RETURNTRANSFER, 1);
		
		//Send the Request
		$response = curl_exec($connection);
		
		//close the connection
		curl_close($connection);
		//print_r($response); echo 'sdfsdfsdfsf';exit;
		//return the response
		return $response;
	}
	
	
	
	/**	buildEbayHeaders
		Generates an array of string to be used as the headers for the HTTP request to eBay
		Output:	String Array of Headers applicable for this call
	*/
	private function buildEbayHeaders()
	{
		$headers = array (
			//Regulates versioning of the XML interface for the API
			'X-EBAY-API-COMPATIBILITY-LEVEL: ' . $this->compatLevel,
			
			//set the keys
			'X-EBAY-API-DEV-NAME: ' . $this->devID,
			'X-EBAY-API-APP-NAME: ' . $this->appID,
			'X-EBAY-API-CERT-NAME: ' . $this->certID,
			
			//the name of the call we are requesting
			'X-EBAY-API-CALL-NAME: ' . $this->verb,			
			
			//SiteID must also be set in the Request's XML
			//SiteID = 0  (US) - UK = 3, Canada = 2, Australia = 15, ....
			//SiteID Indicates the eBay site to associate the call with
			'X-EBAY-API-SITEID: ' . $this->siteID,
		);
		
		return $headers;
	}
}
				

?>
				
			
				
						
									
		
	
	
