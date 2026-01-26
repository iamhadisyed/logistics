<?php

require_once("../includes/settings/config.inc.php");
//error_reporting(E_ALL);
//ini_set('display_errors', 'On');

include_classes([
    'carrierservice.class'
    ], 'general');
include_classes([
    'tourline.class', 'tourlinetrackingstatus.class','anpost.class','brtitaly.class',
    'yodel.class', 'yodeltrackingstatus.class', 'royalmail.class', 'royalmailtrackingstatus.class','hermes1.class','hermes.class', 'hermestrackingstatus.class',
    'yodel.class', 'yodeltrackingstatus.class', 'cttexpress.class',
    'huxloehermes.class', 'kaab.class', 'kabbtrackingstatus.class','asendiauk.class', 'asendiauktrackingstatus.class', 'ups.class',
    'kronosexpress.class', 'kronosexpresstrackingstatus.class', 'deutschepost.class', 'deutscheposttrackingstatus.class', 'viva.class',
    'vivatrackingstatus.class', 'parcelforyou.class','parcelforyoutrackingstatus.class', 'dhl.class','dhltrackingstatus.class','wmsfbo.class',
    'belgiumpost.class', 'ptwo.class', 'omniva.class', 'ocbaglabel.class', 'ocbaglabelcanada.class', 'ocbaglabelaust.class','ocbaglabelde.class',
    'ocbaglabeljfk.class'
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
    'licenceplate.class',
    'licenceplatefilter.class',
    'consignmentfilter.class', 
    'consignmentrelabelfilter.class',
    'consignmentrelabel.class', 
    'countryfilter.class',
    'country.class',
    'bagging.class',
    'serviceagentmappingfilter.class',
    'serviceagentmapping.class',
    'services.class',
    'carrier.class','servicecountrytimefilter.class', 'servicecountrytime.class', 'warehouse.class', 'marketplaceorder.class',
    'marketplaceorderfilter.class', 'marketplaceorderdetails.class', 'marketplaceorderdetailsfilter.class', 
    'marketplaces.class','amazonproductapiresult.class','productmarketplacemappingfilter.class','productmarketplacemapping.class'
    ]);

$hermes = new Hermes();
$hermes->tracking('T01NLA0000002619', 'parcel', true);
die;

$dateTime = date("Y-m-d H:i:s");
$appSecret = 'tWZzslp5ciorMb3sFcYC73gmmRIy1Hnz2yIHvwL1';
$link = 'http://b.flux.easysent.com:8133/wgs/v1/openapi/trackRecord?trackingNumber=6A20746022272';
$queryStr ="trackingNumber=".utf8_encode('6A20746022272');

$signature = md5($queryStr.$appSecret.$dateTime);
//echo $queryStr.$appSecret.$dateTime; die;
//$signature = '7cf9a08d1ce55139f96bb3bb79d95cc1';
$curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $link,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_HTTPHEADER => array(
                'appKey: ' . 'vDVF0PsuZ+1hJfGGrX+o',
                'signature:'. $signature,
                'requestDate:' . $dateTime,
                'languageCode: en'
                
            ),
        ));

        $response = curl_exec($curl);
        print_r($response);
        curl_close($curl);
        return json_decode($response);
        die;



$baglabel = new OcBagLabel();
$baglabel->generateOcBagLabel(12593,'','JFKNY');
die;

/*
$baglabel = new OcBagLabel();
$baglabel->generateOcBagLabel('','');
die;


$baglabel = new OcBagLabelJFK();
$baglabel->generateOcBagLabel('','');
die;


$baglabel = new OcBagLabelDE();
$baglabel->generateOcBagLabel('','');
die;


$baglabel = new OcBagLabelCanada();
$baglabel->generateOcBagLabel('','');
die;



$baglabel = new OcBagLabelDE();
$baglabel->generateOcBagLabel('','');
die;
*/











$yodel = new Yodel();
$yodel->tracking('JD0002210164268622', 'parcel', false);
die;

    $submittedPricingList = ['60658018684'];
    foreach($submittedPricingList as $pricingFeedList) // if multiple products in a feed
                    {
                        $AmazonProductApiResultFilter = new AmazonProductApiResult();
                        $AmazonProductApiResultFilter->setFeedType('Pricing');
                        $AmazonProductApiResultFilter->setFeedStatus('test');
                        $AmazonProductApiResultFilter->setFeedId('test');
                        $AmazonProductApiResultFilter->setProductId('100');
                        $AmazonProductApiResultFilter->setFeedMessage('sdas');
                        $AmazonProductApiResultFilter->setUserId('2127');
                        $AmazonProductApiResultFilter->setAddedAt(date("Y-m-d H:i:s", time()));
                        $AmazonProductApiResultFilter->save();
                        // delete that feed from sqs queue       
                        
                        //Filter to get category ID from amazon_marketplace_mapping table 
                        $productMarketPlaceMappingFilter = new ProductMarketPlaceMappingFilter();
                        $productMarketPlaceMappingFilter->addFilter("    user_id = '2127' and product_id ='10' and marketplace_id = '1'");
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
                    die;
    $omniva = new Omniva();
    $omniva->tracking('','parcel','true');
    die;

$username='7103139';
        $password='QUUYyzcH';
        $URL='https://edixml.post.ee/epteavitus/events/unsent/for-client-code/7103139';

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$URL);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30); //timeout after 30 seconds
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
        curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");
        $result=curl_exec ($ch);
        $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);   //get status code
        curl_close ($ch);
        print_r($result);
        die;
$ptwo = new Ptwo();
$ptwo->tracking('0199999200000176','parcel','');
die;

$belgiumPost = new BelgiumPost();
$belgiumPost->tracking('','parcel','true');
die;
/*
$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => 'https://staging-api.ubsend.io/v1/shipments/register',
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'POST',
  CURLOPT_POSTFIELDS =>'{
   "sender": {
        "firstName": "John",
        "lastName": "Test",
        "addressLine1": "Laplandsgade 4A",
        "city": "benhavn",
        "postCode": "2300",
        "country": "DK",
        "contactInfo": {
            "dialCode": "+45",
            "telephone": "82388238",
            "email": "john.test@ubsend.com"
        }
    },
    "recipient": {
        "firstName": "Peter",
        "lastName": "Testing",
        "addressLine1": "Bahnhofstrasse 4a/8",
        "city": "Aarhus",		
        "postCode": "8001",
        "country": "CH",
        "contactInfo": {
            "dialCode": "+41",
            "telephone": "82388238",
			"email": "peter.testing@ubsend.com"
        }
    },
   "collectionInfo":{
      "type":"DROP_OFF",
      "date":null,
      "preferredTime":null,
      "instructions":null
   },
   "description":null,
   "deliveryInstructions":null,
   "recipientPays":null,
   "carrierProduct":{
      "carrier":"GLS_DK",
      "productId":"GLS_DK_BUSINESS_PARCEL_EURO"
   },
   "externalShipment":null,
   "shipmentType":"REGULAR",
   "parcels":[
       {
            "count": 1,
            "weight": 10,
            "length": 120,
            "width": 50,
            "height": 50,
            "description": "my box"
        }
   ],
	"type": "PDF",
	"layout": "A6"
}',
  CURLOPT_HTTPHEADER => array(
    'ClientId: 12609',
    'Authorization: Bearer eyJraWQiOiJiaXUrcFM0a3p4cDhnNDQyV21SZHZYQjNVWWpSemFsVWx4ZDhkOHoxRnM4PSIsImFsZyI6IlJTMjU2In0.eyJzdWIiOiJkOTJjZDI1NS1hMjgzLTRjNmEtOTAyNy05YjEyZjliNzc3YTAiLCJldmVudF9pZCI6IjVkODBiOTZhLTM4YTQtNDk2NS04NjA1LTkzMDNmN2UzN2VhOCIsInRva2VuX3VzZSI6ImFjY2VzcyIsInNjb3BlIjoiYXdzLmNvZ25pdG8uc2lnbmluLnVzZXIuYWRtaW4iLCJhdXRoX3RpbWUiOjE2MTEyMzk4ODMsImlzcyI6Imh0dHBzOlwvXC9jb2duaXRvLWlkcC5ldS13ZXN0LTEuYW1hem9uYXdzLmNvbVwvZXUtd2VzdC0xXzVrSVhybVc1NyIsImV4cCI6MTYxMTU4MzY0OSwiaWF0IjoxNjExNTgwMDQ5LCJqdGkiOiIyZWUwNzdkMS05ZWY5LTRlOTYtYmEyYy1kYzE2NDI1OTUyNGUiLCJjbGllbnRfaWQiOiI3cWxmaDJtbzFhYXJhamxtMHZkZTdrNDBycyIsInVzZXJuYW1lIjoiYXBpLXVzZXItc3RhZ2luZ0BvbmV3b3JsZGV4cHJlc3MuY29tIn0.jHO3KZy_HWYWrnjo7gztZ7XBqrvsQ-rotWpq2Nol_bkzC3ZwO2LUHN0ru_nO4kdXNhHpQKpsXp8cNeNDMvAan_BDkhfUXPX8IfRj7jX6lR8hOTcpv-c31LC-aW--bXsMaLlQSm5O2Kt6lBWZVV0c1_awEvuTNj2qTS_4gAcw0ufTmNoYRG1latS5T8RBPWgfbuffv7r3XKIGuKRW76aphg8byVvncHoLO9uRZq-2feczfvjobgFiK_7ICFJNnopLkwOhBD_iQ70SnN-hyPuRfe-VhZAF9peb4PHelQwB3V-z-o_PB3kRVAY5KSB3NP_-QXLNsrqRC6D5PlzfoPiliQ',
    'Content-Type: application/json'
  ),
));

$response = curl_exec($curl);

curl_close($curl);
echo '<pre>';
print_r(json_decode($response));

die; */
$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => 'https://staging-api.ubsend.io/v1/shipments/600ec55b04d94b729b7b2536/label.pdf',
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'GET',
  CURLOPT_HTTPHEADER => array(
    'ClientId: 12609',
    'Authorization: Bearer eyJraWQiOiJiaXUrcFM0a3p4cDhnNDQyV21SZHZYQjNVWWpSemFsVWx4ZDhkOHoxRnM4PSIsImFsZyI6IlJTMjU2In0.eyJzdWIiOiJkOTJjZDI1NS1hMjgzLTRjNmEtOTAyNy05YjEyZjliNzc3YTAiLCJldmVudF9pZCI6IjVkODBiOTZhLTM4YTQtNDk2NS04NjA1LTkzMDNmN2UzN2VhOCIsInRva2VuX3VzZSI6ImFjY2VzcyIsInNjb3BlIjoiYXdzLmNvZ25pdG8uc2lnbmluLnVzZXIuYWRtaW4iLCJhdXRoX3RpbWUiOjE2MTEyMzk4ODMsImlzcyI6Imh0dHBzOlwvXC9jb2duaXRvLWlkcC5ldS13ZXN0LTEuYW1hem9uYXdzLmNvbVwvZXUtd2VzdC0xXzVrSVhybVc1NyIsImV4cCI6MTYxMTU4MzY0OSwiaWF0IjoxNjExNTgwMDQ5LCJqdGkiOiIyZWUwNzdkMS05ZWY5LTRlOTYtYmEyYy1kYzE2NDI1OTUyNGUiLCJjbGllbnRfaWQiOiI3cWxmaDJtbzFhYXJhamxtMHZkZTdrNDBycyIsInVzZXJuYW1lIjoiYXBpLXVzZXItc3RhZ2luZ0BvbmV3b3JsZGV4cHJlc3MuY29tIn0.jHO3KZy_HWYWrnjo7gztZ7XBqrvsQ-rotWpq2Nol_bkzC3ZwO2LUHN0ru_nO4kdXNhHpQKpsXp8cNeNDMvAan_BDkhfUXPX8IfRj7jX6lR8hOTcpv-c31LC-aW--bXsMaLlQSm5O2Kt6lBWZVV0c1_awEvuTNj2qTS_4gAcw0ufTmNoYRG1latS5T8RBPWgfbuffv7r3XKIGuKRW76aphg8byVvncHoLO9uRZq-2feczfvjobgFiK_7ICFJNnopLkwOhBD_iQ70SnN-hyPuRfe-VhZAF9peb4PHelQwB3V-z-o_PB3kRVAY5KSB3NP_-QXLNsrqRC6D5PlzfoPiliQ'
  ),
));

$response = curl_exec($curl);

curl_close($curl);
file_put_contents('test.pdf', $response);
//echo $response;

die;
$curl = curl_init();
curl_setopt_array($curl, array(
  CURLOPT_URL => 'https://staging-api.ubsend.io/v1/auth/login',
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'POST',
  CURLOPT_POSTFIELDS =>'{"username":"api-user-staging@oneworldexpress.com","password":"HScuOwb$w.%67WW=l2T>"}',
  CURLOPT_HTTPHEADER => array(
    'Content-Type: application/json',
    'ClientId: 12609'
  ),
));

$response = curl_exec($curl);

curl_close($curl);
echo '<pre>';

print_r(json_decode($response));
die;

echo basename('https://m.media-amazon.com/images/I/41pG7-cf3CL.jpg');
die;
try 
            {
                $client = new SoapClient('http://wms-uk.oneworldexpress.cn/WebService/ShipmentService.asmx?wsdl', array('trace' => true));
                $request = new stdClass();
                $request->customerCode = "OW00022";
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
                $request->notes = "Test";
               
                $request->servicecode = $handlingCode;
                $request->weight = $con->getWeight();
                $request->marketPlaceId = $marketPlaceName;
                $request->label = 'https://www.smarttrack.co/_assets/pdf/'.$con->getlabelFile();
                $request->consignmentNo = $con->getId(); 
                //echo
                $count = 0;
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
                print_r($request); die;
                $response = $client->CreateShipment( array("request" => $request));
                print_r($response);
                echo "REQUEST:\n" . $client->__getLastRequest() . "\n";                
            } 
            catch (Exception $e) 
            {
                echo $e->getMessage();		
            }            
        //}
	die;
        
$image = substr('../_assets/images/sku_images/test.jpg', 29);
                echo $image; die;
                
 $consignment = new Consignment(378885);
    $wmsfbo = new WMSFBO();
    $wmsfbo->label($consignment, 'pdf', '');
    die;
$myArr = [1, 2, 3, 4];

array_push($myArr, 5, 8);
print_r($myArr); // [1, 2, 3, 4, 5, 8]

$myArr[] = -1;
print_r($myArr); // [1, 2, 3, 4, 5, 8, -1]
die;
GetInventory();
    function GetInventory()
	{
		$client = new SoapClient('http://wms-uk.oneworldexpress.cn/WebService/SkuService.asmx?wsdl', array('trace' => true));
		$request = new stdClass();
		$request->CustomerCode = "BRANDS";
		//$request->Sku = "B2Y-HEA-2W";
		$request->PageIndex = 1;
		$request->PageSize = 20;


		$response = $client->GetInventory( array("request" => $request));
echo '<pre>';
		print_r($response);} die;
    $users = json_encode(array('User name' => 'testAppOwe_1', 'Password' => 'V18J4NUGaOmL1rMBgD08iJD5HzyoOR'));
$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => "https://cig.dhl.de/services/production/rest",
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "POST",
  CURLOPT_HTTPHEADER => array(
    "Content-Type: application/x-www-form-urlencoded",
    "Authorization: Basic testAppOwe_1:V18J4NUGaOmL1rMBgD08iJD5HzyoOR"
  ),
));

var_dump(curl_exec($curl));

curl_close($curl);
echo $response;
die;

$users = array('testAppOwe_1' => 'V18J4NUGaOmL1rMBgD08iJD5HzyoOR');
    $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://cig.dhl.de/services/production/rest");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        $header = base64_encode("testAppOwe_1:V18J4NUGaOmL1rMBgD08iJD5HzyoOR");
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Accept: application-json",
            "Authorization: Basic ". $users 
        ));

        var_dump(curl_exec($ch));
        
        curl_close($ch);
        die;
        
   
    $pdf = new PdfBase(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
    $skuOrder = new SKUOrder(9);
    $processCode = $skuOrder->getShipmentReference();
    $skuboxDetailFilter = new SkuBoxDetailFilter();
        $skuboxDetailFilter->addFilter("sku_order_id ='".$skuOrder->getId()."'");
        $result = $skuboxDetailFilter->getList();
        if(count($result) > 0)
        {
            $count = 1;
            foreach($result as $boxDetail)
            {
                addWayBill($processCode, $boxDetail, $pdf, $count);
                $count++;
            }
         $pdf->Output("../_assets/pdf/" . date('Y_m_d') . '/' . '123test' . ".pdf", "F");  
        }
        
    
    function addWayBill($processCode, $boxDetail, $pdf, $count) {
        // Generate label for each parecel
        $pdf->SetPrintFooter(false);
        $pdf->SetFooterMargin(0);
        $pdf->SetAutoPageBreak(false, 0);
        $page_size = array(100, 150);
        $pdf->AddPage("P", $page_size);
        
        $output = array();
        $output["STATUS"] = "SUCCESS";
        $pdf->setFont("helvetica", "", 11);

        $style = array(
            'position' => '',
            'align' => 'L',
            'stretch' => false,
            'cellfitalign' => '',
            'border' => false,
            'fgcolor' => array(0, 0, 0),
            'bgcolor' => false, //array(255,255,255),
            'text' => true,
            'stretchtext' => 1
        );
        $pdf->line(0,30,100,30);
        $pdf->write1DBarcode($processCode, 'C128', '3', '35', '', 12, 0.35, $style, 'N');
        $pdf->write1DBarcode($processCode.str_pad($count, 3,'0',STR_PAD_LEFT), 'C128', '8.5', '85', '', 12, 0.4, $style, 'N');        
        
        $pdf->rect(70,35,22,12);
        $pdf->line(70,42,92,42);
        $pdf->setFont("helvetica", "B", 13);
        $pdf->Text(74, 35, "OWE");
        $pdf->setFont("helvetica", "", 9);
        $pdf->Text(74, 42.5, "Stock In");
        $pdf->Text(13, 109.5, "SKUTEST");
        $pdf->setFont("helvetica", "B", 9);
        $pdf->Text(5, 109.5, "SKU:");
        $pdf->rect(6,58,85,18);
        $pdf->line(6,67,91,67);
        
        $pdf->line(25,58,25,76);
        $pdf->line(48,58,48,76);
        $pdf->line(70,58,70,76);
        $pdf->setFont("helvetica", "B", 11);
        $pdf->Text(8, 60, "FROM");
        $pdf->Text(8, 69, "CUST");
        $pdf->Text(55, 60, "TO");
        $pdf->Text(50, 69, "PIECES");
        $pdf->line(0,108,100,108);
        $pdf->line(0,115,100,115);
        
    }
    die;
    //$ParcelDataModelObjArr['ParcelDataModel'][$count] =  $parcelDataModelClass;
/*
try 
    {
        $sku = new Sku(16);
        $client = new SoapClient('http://wms-uk.oneworldexpress.cn/WebService/SkuService.asmx?wsdl', array('trace' => true));
        $request = new stdClass();
        $request->SKU = 'danish123456yyy';
        $request->CustomerID = 'BRANDS';//$sku->getCustomerId();
        $request->SKU_Ref1 = '';
        $request->Active_Flag = $sku->getActive();
        $request->Descr_E = $sku->getDescription();
        $request->DeclaredNameEN = $sku->getDeclaredName();
        $request->GrossWeight = $sku->getGrossWeight();
        $request->NetWeight = '0.00';
        $request->Price = $sku->getPrice();
        $request->SKULength = $sku->getLength();
        $request->SKUWidth = $sku->getWidth();
        $request->SKUHeight = $sku->getHeight();                
        $request->HSCode = '';                
        print_r($request);
        $response = $client->CreateSku( array("request" => $request));
        print_r($response);
        echo "REQUEST:\n" . $client->__getLastRequest() . "\n";                
    } 
    catch (Exception $e) 
    {
        return $e->getMessage();		
    }         
    die;
    */
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
        //  $FbaContainerDetailDataModelList = new stdClass();
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
            $FbaContainerDetailDataArr['FbaContainerDetailDataModel'][$i] =  $FbaContainerDetailDataClass;
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


try 
            {
                $sku = new Sku(16);
                $client = new SoapClient('http://wms-uk.oneworldexpress.cn/WebService/SkuService.asmx?wsdl', array('trace' => true));
                $request = new stdClass();
                $request->SKU = $sku->getSku();
                $request->CustomerID = 'BRANDS';//$sku->getCustomerId();
                $request->WarehouseCode = "1";
                $request->SKU_Ref1 = '';
                $request->Hazard_Flag = '';
                $request->Active_Flag = $sku->getActive();
                $request->Descr_C = '';
                $request->Descr_E = $sku->getDescription();
                $request->DeclaredNameEN = $sku->getDeclaredName();
                $request->DeclaredNameCN = '';
                $request->GrossWeight = $sku->getGrossWeight();
                $request->NetWeight = $sku->getNetWeight();
                $request->Tare = "1";
                $request->Cube = "1.0";
                $request->Price = $sku->getPrice();
                $request->SKULength = $sku->getLength();
                $request->SKUWidth = $sku->getWidth();
                $request->SKUHeight = $sku->getHeight();
                 $request->ImageAddress = '';
                $request->HSCode = '';
                $request->FirstOP = '';
                print_r($request);
                $response = $client->CreateSku( array("request" => $request));
                print_r($response);
                echo "REQUEST:\n" . $client->__getLastRequest() . "\n";                
            } 
            catch (Exception $e) 
            {
                return $e->getMessage();		
            }         
            die;
try 
            {
                $client = new SoapClient('http://wms-uk.oneworldexpress.cn/WebService/SkuService.asmx?wsdl', array('trace' => true));
                $request = new stdClass();
                $request->SKU = "testsku123";
                $request->CustomerID = "BRANDS";
                $request->SKU_Ref1 = "test123";
                $request->WarehouseCode = "1";
                $request->Hazard_Flag = '';
                $request->Active_Flag = '1';
                $request->Descr_C = '';
                $request->Descr_E = 'test';
                $request->DeclaredNameEN = 'test';
                $request->DeclaredNameCN = '';
                $request->GrossWeight = '1';
                $request->NetWeight = '1.0';
                $request->Tare = "1";
                $request->Cube = "1.0";
                $request->Price = "1";
                $request->SKULength = "1";
                $request->SKUWidth = "1";
                $request->SKUHeight = "1";
                $request->ImageAddress = '';
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
       
	die;

/////////////////////////************ WMS Add Shipment Api **************/////////////////////////////
        /*try 
        {
            $url = 'http://wms-uk.oneworldexpress.cn/WebService/ShipmentService.asmx?wsdl';
          //  $request = new stdClass();
            //$request->hawb = '206-5202285-1457130';//$con->getHawb();
            //$response = $client->ShipmentAllocation($request->hawb);
       //     print_r($client, true);
         //   echo "REQUEST:\n" . $client->__getLastRequest() . "\n";                
            
            //$url = 'https://secure.softwarekey.com/solo/webservices/XmlCustomerService.asmx?WSDL';
$client = new SoapClient($url);

$xmlr = new SimpleXMLElement("<ShipmentAllocation></ShipmentAllocation>");
$xmlr->addChild('hawb', '206-1281098-4945164');
//$xmlr->addChild('UserID', $userID);
//$xmlr->addChild('UserPassword', $userPassword);
//$xmlr->addChild('Email', $customerEmail);

$params = new stdClass();
$params->hawb = $xmlr->asXML();
print_r($params); 
$result = $client->ShipmentAllocation($params);
print_r($result); 
        } 
        catch (Exception $e) 
        {
            echo $e->getMessage();		
        }            
     die; */
     
        /////////////////////////************ WMS Add Shipment Api **************/////////////////////////////
	/*$consignmentFilter = new ConsignmentFilter();
        $consignmentFilter->addFieldFilter('    user_id', '4724');
        $consignmentFilter->addFilter('date_created >= "2020-10-06 00:00:00" and date_created < "2020-10-07 00:00:00"');
        $result = $consignmentFilter->getListNew('*');
        //echo count($result); die;
        foreach($result as $con)
        {
            $hawb = $con->getHawb();
            $marketPlaceOrderFilter = new MarketPlaceOrderFilter();
            $marketPlaceOrderFilter->addFieldFilter('    marketplace_order_number',$hawb );
            $marketPlaceOrderResult = $marketPlaceOrderFilter->getList();
            
            $marketplaceID = $marketPlaceOrderResult[0]->getMarketPlaceId();
            $marketPlaces = new MarketPlaces($marketplaceID);
            $marketPlaceName = $marketPlaces->getTitle();

            $mkpOrderDetailsFilter = new MarketPlaceOrderDetailsFilter();
            $mkpOrderDetailsFilter->addFieldFilter('    marketplace_order_id',$marketPlaceOrderResult[0]->getId());
            $rsDetail = $mkpOrderDetailsFilter->getList();
            
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
            
          */  
            

        
     
        
    $dhl = new HuxloeHermes();
    $dhl->tracking('AT001095000','shipment', '');
  die;
    $TrackingDataFilter = new TrackingDataFilter();
    $TrackingDataFilter->addFilter("tracking_number in ('JD0002210164151400',
'JD0002210164151689',
'JD0002210164152235',
'JD0002210164152358',
'JD0002210164152439',
'JD0002210164152609',
'JD0002210164152619',
'JD0002210164152621',
'JD0002210164152655',
'JD0002210164152664',
'JD0002210164152683',
'JD0002210164152890',
'JD0002210164152894',
'JD0002210164152902',
'JD0002210164152911',
'JD0002210164152922',
'JD0002210164152930',
'JD0002210164152944',
'JD0002210164152945',
'JD0002210164153060',
'JD0002210164153061',
'JD0002210164153062',
'JD0002210164153063',
'JD0002210164153065',
'JD0002210164153066',
'JD0002210164153079',
'JD0002210164153081',
'JD0002210164153083',
'JD0002210164153086',
'JD0002210164153088',
'JD0002210164153089',
'JD0002210164153090',
'JD0002210164153091',
'JD0002210164153093',
'JD0002210164153094',
'JD0002210164153113',
'JD0002210164153114',
'JD0002210164153116',
'JD0002210164153117',
'JD0002210164153118',
'JD0002210164153120',
'JD0002210164153121',
'JD0002210164153123',
'JD0002210164153124',
'JD0002210164153129',
'JD0002210164153130',
'JD0002210164153138',
'JD0002210164153173',
'JD0002210164153174',
'JD0002210164153183',
'JD0002210164153185',
'JD0002210164153186',
'JD0002210164153187',
'JD0002210164153189',
'JD0002210164153190',
'JD0002210164153193',
'JD0002210164153200',
'JD0002210164153209',
'JD0002210164153236',
'JD0002210164153237',
'JD0002210164153238',
'JD0002210164153239',
'JD0002210164153240',
'JD0002210164153241',
'JD0002210164153242',
'JD0002210164153243',
'JD0002210164153244',
'JD0002210164153245',
'JD0002210164153246',
'JD0002210164153256',
'JD0002210164153347',
'JD0002210164153348',
'JD0002210164153401',
'JD0002210164153410',
'JD0002210164153421',
'JD0002210164153423',
'JD0002210164153424',
'JD0002210164153425',
'JD0002210164153426',
'JD0002210164153429',
'JD0002210164153430',
'JD0002210164153431',
'JD0002210164153433',
'JD0002210164153434',
'JD0002210164153435',
'JD0002210164153440',
'JD0002210164153451',
'JD0002210164153452',
'JD0002210164153453',
'JD0002210164153461',
'JD0002210164153462',
'JD0002210164153464',
'JD0002210164153470',
'JD0002210164153473',
'JD0002210164153481',
'JD0002210164153532',
'JD0002210164153533',
'JD0002210164153757',
'JD0002210164153758',
'JD0002210164153759',
'JD0002210164153760',
'JD0002210164153761',
'JD0002210164153762',
'JD0002210164153763',
'JD0002210164153767',
'JD0002210164153768',
'JD0002210164153769',
'JD0002210164153776',
'JD0002210164153777',
'JD0002210164153822',
'JD0002210164153824',
'JD0002210164153825',
'JD0002210164153826',
'JD0002210164153840',
'JD0002210164153855',
'JD0002210164153864',
'JD0002210164153878',
'JD0002210164153880',
'JD0002210164153881',
'JD0002210164153882',
'JD0002210164153883',
'JD0002210164153884',
'JD0002210164153885',
'JD0002210164153887',
'JD0002210164153900',
'JD0002210164153901',
'JD0002210164153958',
'JD0002210164153967',
'JD0002210164158754',
'JD0002210164158968',
'JD0002210164159310',
'JD0002210164159323',
'JD0002210164159347',
'JD0002210164159489',
'JD0002210164159517',
'JD0002210164159555',
'JD0002210164159558',
'JD0002210164159581',
'JD0002210164159594',
'JD0002210164159602',
'JD0002210164159616',
'JD0002210164160293',
'JD0002210164160294',
'JD0002210164160300',
'JD0002210164160301',
'JD0002210164160436',
'JD0002210164160437',
'JD0002210164160441',
'JD0002210164160441',
'JD0002210164160442',
'JD0002210164160444')");
    $result = $TrackingDataFilter->getColumnList('*');
    $trackingNumber = $result[0]->getTrackingNumber();
    
    foreach($result as $res)
    {
        $dateCreated1 = $res->getDateCreated();
        $trackingNumber1 = $res->getTrackingNumber();
        
        if($res->getStatusCodeId() == '121')
        {
            $trackingNumber = $res->getTrackingNumber();
            $dateCreated = $res->getDateCreated();
          //  continue;
        }
        
        if($trackingNumber == $trackingNumber1 && $dateCreated < $dateCreated1)
        {
            $afterDeliveredArray[] = $res->getStatusCodeId().'-'.$res->getTrackingNumber().'-'.$res->getId();
            //$timeofStatus = $res->getDateCreated();
        }
    }
    
    echo '<pre>';
    print_r($afterDeliveredArray);
    die;
    
    $parcel4you = new parcelForYou();
    $parcel4you->tracking('Z1966974248','parcel', true);
    die;

    $tourline = new Tourline();
    $tourline->tracking('DAC100012LHR','', true);
    die;

    $vivaTracking = new Viva();
    $vivaTracking->tracking('VI50005808', 'parcel', false);
    die; 
    
    $deutchpostTracking = new DeutschePost();
    $deutchpostTracking->tracking('LY097881504DE', 'parcel', false);
    die;


//$koronosexpressTracking = new KronosExpress();
//$koronosexpressTracking->tracking('OWE100000012', 'parcel', false);
//die;

{
        $mailNo = 'JD0002210164148918';//$trackingData['mailNo'];
        $sign = //$trackingData['sign'];
        $cpCode = 'ONEWORLD';//$trackingData['cpCode'];
        
        $aliBabaTrackingMap = [
            111 => "GTMS_DELIVERING",
            112 => "SC_INBOUND_FAILURE",
            113 => "SC_SIGN_IN_SUCCESS",
            114 => "PU_SIGN_IN_SUCCESS",
            115 => "SC_INBOUND_FAILURE",
            116 => "SC_INBOUND_FAILURE",
            117 => "CC_EX_SUCCESS",
            118 => "SC_INBOUND_FAILURE",
            119 => "SC_INBOUND_FAILURE",
            120 => "SC_INBOUND_FAILURE",
            121 => "GTMS_SIGNED",
            122 => "GTMS_SIGNED",
            123 => "GTMS_SIGNED",
            124 => "SC_INBOUND_FAILURE",
            125 => "GTMS_SIGN_FAILURE",
            126 => "GTMS_DELIVERING",
            127 => "GMT_DEL_FAILURE",
            128 => "SC_INBOUND_FAILURE",
            129 => "GTMS_ACCEPT",
            130 => "SC_INBOUND_FAILURE",
            131 => "PU_PICKUP_FAILURE",
            133 => "PU_PICKUP_SUCCESS",
            134 => "Partially Collected",
            135 => "SC_INBOUND_FAILURE",
            136 => "SC_INBOUND_FAILURE",
            137 => "GTMS_ACCEPT",
            138 => "RETURNED",
            140 => "SC_SIGN_IN_SUCCESS",
            141 => "SC_SIGN_IN_SUCCESS",
            142 => "SC_INBOUND_FAILURE",
            143 => "RETURNED",
            144 => "SC_SIGN_IN_SUCCESS",
            145 => "GTMS_DO_ARRIVE",
            146 => "SC_SIGN_IN_SUCCESS",
            147 => "GTMS_SIGNED",
            148 => "PU_SIGN_IN_SUCCESS"
        ];
        $trackingObj = new Tracking(); 
        $trackingData = $trackingObj->GetTracking($mailNo);
        
        $status = "true";
        $statusCode = "S01";
        $message = "";
        if(isset($trackingData['message']))
        {
            $message = $trackingData['message'];
        }		
        
       
		{
                        $codeArray = array('PU_PICKUP_SUCCESS','PU_SIGN_IN_SUCCESS','SC_INBOUND_SUCCESS','SC_SIGN_IN_SUCCESS','SC_OUTBOUND_SUCCESS',
                                     'SC_HO_OUT_SUCCESS','LH_POST_COLLECTION','CC_EX_SUCCESS','LH_ARRIVE','GTMS_ACCEPT','GTMS_SC_ARRIVE','LH_DEPART',
                                     'GTMS_DO_ARRIVE','GTMS_DELIVERING','GTMS_RE_DELIVERING', 'CC_EX_START', 'RT_INBOUND');
                        
			$returnData = '<response>
                        <mailNo>'.$mailNo.'</mailNo>
                        <cpcode>'.$cpCode.'</cpcode >
                        <success>'.$status.'</success>
                        <code>'.$statusCode.'</code>
                        <message>'.$message.'</message>';
        
			if(!empty($trackingData['tracking']) && count($trackingData['tracking']) > 0){
				$destinationCountry = "";
				$originCountry = "";
                                
                                if(!empty($trackingData['tracking']['shipment_detail'])){
					$destinationCountry = $trackingData['tracking']['shipment_detail']['destination_country'];
					$originCountry = $trackingData['tracking']['shipment_detail']['origin_country'];
				}
                                
				$trackingDataEvent = "";
				if(!empty($trackingData['tracking']['tracking_events'])){
					$ocData = [];
					$dcData = [];
					foreach ($trackingData['tracking']['tracking_events'] as $dateEvent => $dateEventData) {
						foreach ($dateEventData as $eventData) {
							if($eventData['tracking_country'] == $originCountry){
								$ocData[] = $eventData;
							}else{
								$dcData[] = $eventData;
							}
						}
					}
                                        $ocData = array_reverse($ocData);
                                        $dcData = array_reverse($dcData);
                                        
                                        if(!empty($ocData)){
						$trackingDataEvent .= '<group><type>OC</type>';
                                                
                                                $index = 0;
                                                foreach($ocData as $ocTrack)
                                                {
                                                        $code = $codeArray[$index];
                                                        $index++;
                                                        $trackingDataEvent .= '<track>
													   <time>'.$ocTrack['date_time'].'</time>
													   <country>'.$originCountry.'</country>
													   <city>'.$originCountry.'</city>
													   <facilityName>'.$ocTrack['track_point'].'</facilityName>
													   <timeZone>+8</timeZone>
													   <desc>'.$ocTrack['carrier_desc'].'</desc>';
                                                        $trackingDataEvent .= '<actionCode>'.$code.'</actionCode>
													</track>';                                                        
						}
						$trackingDataEvent .= '</group>';
					}
                                        
					if(!empty($dcData)){
						$trackingDataEvent .= '<group>
												<type>DC</type>';
						foreach($dcData as $dcTrack){
                                                        if($dcTrack['status_code_id'] == 121)
                                                        {
                                                            $code = 'GTMS_SIGNED';
                                                        }
                                                        else
                                                        {
                                                            $code = $codeArray[$index];
                                                            $index++;
                                                        }
							$trackingDataEvent .= '<track>
                                                                                <time>'.$dcTrack['date_time'].'</time>
                                                                                <country>'.$destinationCountry.'</country>
                                                                                <city>'.$destinationCountry.'</city>
                                                                                <facilityName>'.$dcTrack['track_point'].'</facilityName>
                                                                                <timeZone>+8</timeZone>
                                                                                <desc>'.$dcTrack['carrier_desc'].'</desc>
                                                                                <actionCode>'.$code.'</actionCode>
                                                                              </track>';                                                                               
						}
						$trackingDataEvent .= '</group>';
					}                                       
				}
				$returnData .=    '<tracesElement>
								   <destinationCountry>'.$destinationCountry.'</destinationCountry>
								   '.$trackingDataEvent.'
								 </tracesElement>';
			}
			$returnData .= '</response>';
			echo $returnData;
			die;
			
		}
    }
    
    die;

    
$upsTracking = new UPS();
$upsTracking->tracking('1Z3985RW6822840859', 'parcel', false);
die;

require_once '../includes/3rdparty/AmazonAws/aws-autoloader.php';
$queueUrl = "https://sqs.us-east-2.amazonaws.com/095611935099/order_notification";

use Aws\Sqs\SqsClient; 
use Aws\Exception\AwsException;
 
$credentials = new Aws\Credentials\Credentials('AKIAIMVDRT2XYX2SCMWQ' ,'/wqmPzz3vzf7Zqbtp8nHLdIEQpl/0fChgDXu8axN');

$config = array(
    
    'region'  => 'us-east-2',
    'version' => 'latest',
    'credentials' => $credentials
  );

$client = new SqsClient($config, [
    //'profile' => 'default',
    'region' => 'us-east-2',
    'version' => '2012-11-05'
]);


try {
$result = $client->receiveMessage([
    'AttributeNames' => ['All'],
    'MaxNumberOfMessages' => 10,
    'QueueUrl' => $queueUrl,
]);

foreach( $result->get('Messages') as $message ){

    $xml = $message['Body'];
    echo '<pre>';
            $statusArray = simplexml_load_string($xml);  
            print_r($statusArray);
            echo $reportRequestIdFromNotification =  $statusArray->NotificationPayload->ReportProcessingFinishedNotification->ReportRequestId;
$result = $client->deleteMessage([
            'QueueUrl' => $queueUrl, // REQUIRED
            'ReceiptHandle' => $message['ReceiptHandle'] // REQUIRED
        ]);
}

} catch (AwsException $e) {
    // output error message if fails
    error_log($e->getMessage());
}

die;


$url = "https://sqs.us-east-2.amazonaws.com/095611935099/order_notification";
        
        $ch = curl_init($url);
  //      curl_setopt($ch, CURLOPT_POST, 1);
    //    curl_setopt($ch, CURLOPT_POSTFIELDS, $myvars);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($ch);
print_r($response);
die;

$date = date("Y-m-d H:i:s");
$startDate = date('Y-m-d H:i:s', strtotime('-1 hours', strtotime($date)));
$currentDate  = new DateTime($startDate);      

print_r($currentDate);
die;

$asendia = new AsendiaUk();
$asendia->tracking('9L25418022093', 'parcel', false);
die;

$hermes1 = new Hermes1();
$hermes1->tracking('8528594969131419', 'parcel', false);
die;

$kaab = new Kaab();
$kaab->tracking('00370726207253404509', 'parcel', false);
die;




ini_set('soap.wsdl_cache_enabled',0);
ini_set('soap.wsdl_cache_ttl',0);

$service_url= 'http://cttexpressows.ctt.pt/CTTEWSPool/EventosWS.svc?wsdl';
$client     = new SoapClient($service_url , array("trace" => 1, "exception" => 0));
$detailresult = $client->GetEstadoObjecto('0b9299ac-76c3-4ecd-a07a-f31fab2b9d26','EQ445440388PT');
print_r($detailresult);
die;


$ctt = new CttExpress();
$ctt->tracking('EQ445441542PT', 'parcel', false);
die;





$consignmentArray = array(
    81299,
81475,
81810,
82876,
82877,
82952,
99007,
82977,
83378,
83489,
101036,
83513,
83515,
83888,
87581,
87755,
88124,
99800,
88260,
88754,
89399,
89398,
90084,
90085,
90086,
90588,
90589,
90590,
90591,
91894,
93620,
93714,
95222,
96902,
97060,
97061,
97442,
97443,
97444,
97445,
97446,
97447,
97448,
97668,
97913,
97914,
98392,
98393,
98394,
98395,
98396,
99801,
98604,
98606,
98607,
98609,
98610,
101035,
98800,
98801,
98802,
98803,
98804,
98806,
98975,
98976,
98977,
98978,
99121,
99122,
99123,
99458,
99459,
99460,
99467,
99469,
99470,
99471,
99776,
99777,
99778,
100137,
99779,
99780,
99781,
99947,
99948,
99950,
99952,
99954,
100121,
100122,
100123,
101285,
100125,
100127,
100128,
100323,
100768,
100808,
100809,
100810,
100811,
101167,
101280,
101923,
101924,
101925,
101926,
102483,
102557,
102558,
102559,
102561,
102801,
102803,
103073,
103074,
103075,
103261,
103262,
103264,
103265,
103266,
103270,
103276,
103277,
103283,
103284,
103285,
103671,
103673,
103674,
103677,
103694,
103693,
103695,
103702,
);
// Royal Mail 
$consignmentArray = array(
    80987,
81198,
81502,
81868,
82104,
82122,
82142,
82322,
82339,
82579,
82610,
82821,
82887,
82905,
83026,
83029,
83030,
83032,
83034,
83047,
83081,
83323,
83324,
83325,
83343,
83348,
83520,
83736,
84243,
84698,
87835,
87836,
87837,
87838,
87843,
87977,
88106,
88109,
88389,
88747,
89387,
89391,
89417,
89425,
89761,
90108,
90205,
90225,
90374,
90454,
90689,
90690,
90925,
90935,
90970,
90978,
90996,
91035,
91170,
91185,
91407,
91459,
91464,
91752,
91889,
92248,
92288,
92289,
92959,
92963,
92964,
92965,
93031,
93038,
93316,
93726,
96410,
96413,
96418,
96420,
96422,
96426,
96428,
99804,
96433,
96437,
100508,
97364,
);

$consignmentArray =  array(
81868,
89387,
91889,
92959,
92963,
92964,
92965,
93031,
93038,
97364
);

$consignmentArray = array(
    89385,
78303,
78266,
78333,
78334,
78385,
78386,
78390,
78395,
78421,
78426,
78516,
78517,
78518,
78520,
78522,
78524,
78527,
78529,
78531,
78534,
78548,
78549,
78550,
78552,
78554,
78562,
78594,
78596,
78597,
78600,
78602,
78604,
78606,
78610,
78611,
78766,
78892,
79003,
79128,
79130,
79301,
79498,
79555,
79563,
79567,
79623,
79624,
79626,
79627,
79638,
79646,
79649,
79715,
79716,
79717,
79718,
79719,
79720,
79738,
79753,
79757,
79821,
79854,
79862,
79954,
80108,
80143,
80152,
80188,
80209,
80210,
80238,
80239,
80249,
80268,
80304,
80339,
80342,
80347,
80367,
80381,
80423,
80447,
80524,
80525,
80568,
80575,
80576,
80577,
80578,
80579,
80580,
80581,
80585,
80612,
80622,
80733,
80758,
80917,
80918,
80932,
80952,
80957,
80958,
80965,
80996,
81140,
81146,
81736,
81778,
81902,
81906,
82032,
82114,
82228,
82395,
82427,
82434,
82454,
82512,
82533,
82558,
82708,
82740,
82853,
82929,
83097,
83165,
81299,
83059,
97450,
97456,
97449,
97460,
97461,
97462,
97463,
97467,
97468,
97470,
97472,
97473,
97475,
97477,
97480,
97483,
97484,
97486,
97487,
97489,
97491,
97492,
97493,
97494,
97495,
97496,
97498,
97499,
97501,
97502,
97503,
97504,
97505,
97506,
97594,
97645,
97682,
97685,
97686,
97699,
97703,
97704,
97706,
97708,
97709,
97712,
97714,
97715,
97723,
97724,
97726,
97728,
97730,
97731,
97732,
97733,
97734,
97813,
97816,
97817,
97819,
97815,
99722
);

$consignmentArray =  array(
    81346,
97481,
97725,
97812,
97825,
100812,
100819,
100842,
100887,
100894,
100900,
100902,
100905,
100907,
101070,
101072,
101079,
101082,
101084,
101085,
101086,
101087,
101088,
101091,
101093,
101098,
101099,
101100,
101103,
101104,
101106,
101107,
101110,
101111,
101112,
101113,
101115,
101116,
101117,
101118,
101254,
101262,
101264,
101266,
101127,
101270,
101128,
101133,
101120,
101122,
101124,
101123,
101126,
101129,
101271,
101125,
101288,
101294,
101295,
101300,
101318,
101317,
101293,
101325,
101514,
101515,
101531,
101533,
101534,
101535,
101537,
101538,
101540,
101544,
101545,
101547,
101548,
101549,
101555,
101578,
101580,
101581,
101636,
101639,
101778,
101779,
101780,
101781,
101799,
101816,
101819,
101823,
101824,
101828,
101829,
101832,
101835,
101836,
101837,
102181,
102183,
102186,
102187,
102190,
102198,
102208,
102212,
102215,
102218,
102220,
102234,
102237,
102253,
102271,
102273,
102275,
102277,
102278,
102279,
102282,
102281,
102283,
102286,
102288,
102290,
102296,
102291,
102298,
102294,
102299,
102300,
102303,
102304,
102308,
102319,
102320,
102322,
102324,
102327,
102333,
102336,
102338,
102342,
102332,
102335,
102339,
102505,
102529,
102543,
102544,
102562,
102565,
102570,
102571,
102572,
102751,
102760,
102773,
102774,
102776,
102781,
102783,
102784,
102786,
102794,
102796,
102800,
102802,
102817,
102818,
102819,
102821,
102823,
102843,
102844,
102850,
102852,
102853,
102854,
102857,
102856,
102859,
102864,
102861,
102870,
102866,
102865,
102873,
102876,
102877,
102879,
102881,
102883,
102872,
102874,
102880,
103051,
103062,
103067,
102884,
103069,
102885,
102889,
102890,
102891,
102893,
102894,
102896,
102897,
102895,
103082,
103095,
103113,
103125,
103126,
103127,
103123,
103119,
103118,
103116,
103115,
103112,
103111,
103135,
103137,
103255,
103263,
103324,
103354,
103362,
103367,
103371,
103384
);
foreach($consignmentArray as $arrayId)
{
    $ConsignmentObj = new Consignment($arrayId);
    $trackingDataFilter = new TrackingDataFilter();
    $trackingDataFilter->addFieldFilter('    tracking_number',$ConsignmentObj->getAwb());
    $trackingDataFilter->addFilter("   status_code_id != 144 AND carrier_code is not null AND warehouse_id = 0");
    $trackingDataExistsObj = $trackingDataFilter->getColumnList("t.id, t.date_created");
            
    if(count($trackingDataExistsObj) > 0)
    {
        $ConsignmentObj->setDateScanned(strtotime($trackingDataExistsObj[0]->getDateCreated()));
        $ConsignmentObj->eventKey = 'scanDate';
        $ConsignmentObj->save();
    }
}
die;
        
        $trackingNumber = 'JD0002210164120827';
        $trackingDataFilter = new TrackingDataFilter();
        $trackingDataFilter->addFieldFilter('    tracking_number',$trackingNumber);
        $trackingDataFilter->addFieldFilter('    status_code_id',146);
        $trackingDataExistsObj = $trackingDataFilter->getColumnList("t.id");
        if(count($trackingDataExistsObj) <= 0)
        {      
            $trackingDataFilter = new TrackingDataFilter();
            $trackingDataFilter->addFieldFilter('    tracking_number',$trackingNumber);
            $trackingDataFilter->addFieldFilter('    status_code_id',148);
            $trackingDataExistsObj = $trackingDataFilter->getColumnList("t.id, t.date_created");
            
            if(count($trackingDataExistsObj) >0)
            {
                $consignmentFilter = new ConsignmentFilter();
                $consignmentFilter->addFieldFilter('    awb', $trackingNumber);
                $conObj = $consignmentFilter->getListNew("id");
                $consignment = new Consignment($conObj[0]->getId());
                $consignment->setDateScanned(strtotime($trackingDataExistsObj[0]->getDateCreated()));
                $consignment->save();
            }
        }
die;



$royalmail = new RoyalMail();
$royalmail->tracking('JV790931939GB', 'parcel', false);
die;

		$trackingNumber = 'JV790933118GB';
	        $service_url = "https://api.royalmail.net/mailpieces/v2/$trackingNumber/events";
		$headers = array('X-IBM-Client-Secret: O5vN0wX5qX4vM6oD6cV6mG8uX6iQ5tV3cM3nS3kE6oD4sV8fE1',
				  'X-IBM-Client-Id: 9509a877-9374-44d5-a5b4-fff2ac586bc9',
				  'Accept: application/json', 
				  'Host: api.royalmail.net'
				  );
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $service_url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch,CURLOPT_HTTPHEADER, $headers);
		//curl_setopt($ch, CURLINFO_HEADER_OUT, true);
		$server_output = curl_exec ($ch);		
		//$info = curl_getinfo($ch);		
		curl_close ($ch);

		$trackDetailArray = json_decode($server_output, true);
	        $trackDetailArray = array_reverse($trackDetailArray['mailPieces']['events']);   
                die;
                
                $service_url = "https://api.royalmail.net/mailpieces/v2/summary?mailPieceId=".trim($trackingNumber);
		$headers = array('X-IBM-Client-Secret: O5vN0wX5qX4vM6oD6cV6mG8uX6iQ5tV3cM3nS3kE6oD4sV8fE1',
						  'X-IBM-Client-Id: 9509a877-9374-44d5-a5b4-fff2ac586bc9',
						  'Accept: application/json', 
						  'Host: api.royalmail.net'
						);
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $service_url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch,CURLOPT_HTTPHEADER, $headers);
		$server_output = curl_exec ($ch);		
		$info = curl_getinfo($ch);
		curl_close ($ch);

		$trackDetailArray = json_decode($server_output, true);
		echo '<pre>';
		print_r($trackDetailArray);
		die;
		$trackDetailArray = $trackDetailArray['mailPieces'][0]['summary'];
		
		//print_r($trackDetailArray);	
		/*
		$dateTime = $trackDetailArray['eventDate'] . " " . $trackDetailArray['eventTime'];
		$location = $trackDetailArray['summaryLine'];
                $summaryStatus = trim($trackDetailArray['status']);
                 * 
                 */
                
                
                $summaryStatus = $trackDetailArray['summaryLine'];
                $dateTime = date("Y-m-d G:i", strtotime($trackDetailArray['lastEventDateTime']));
                $location =  $trackDetailArray['lastEventLocationName'];
                
               
		
		if(trim($dateTime) != '' && $dateTime !=  '1970-01-01 0:00')
		{
                        /*
                        if(strtolower($summaryStatus) == 'delivery attempted')
                            $setPODStatus($consignmentDataInstant, $summaryStatus, $dateTime);*/
                    
                        if(in_array($trackingNumber, $royalMailNumbersArray))
                        {
                            $status = "Intransportation";
                            
                           

                            //echo $status;                            
                            
                            
                            if($dateTime >= date("Y-m-d", strtotime("-3 months")))
                            {
                                  $htmlNew	.=	'<tr>
                                             <td>'.$dateTime.'</td> <!-- Title -->
                                             <td>'.$location.'</td>
                                             <td>'.$status.'</td>
                                             <td></td> 				
                                         </tr>';
                                  
                                 //$setPODStatus($consignmentData, $status);
                                  
                                 
                            }
                            
                            if($summaryStatus == Consignment::STATUS_DELIVERED || $summaryStatus == "Collected" || $status == 'Delivered')
                            {
                                
                                $status = "Intransportation";
                                $summaryStatus = "Intransportation";
                                $finalStatus = "Intransportation";
                                $location = "Intransportation";
                               
                            }
                              
             
                        }
                        else
                        {
                            
                            //echo $status;
                            
                            
                            $htmlNew	.=
                                         '<tr>
                                             <td>'.$dateTime.'</td> <!-- Title -->
                                             <td>'.$location.'</td>
                                             <td>'.$summaryStatus.'</td>
                                             <td></td> 				
                                         </tr>';
                            
                            
                            
                            
                            if(strpos($location, 'delivered') !== false)
                            {
                                    $dateDelivered = $dateTime;
                                    $status = Consignment::STATUS_DELIVERED;
                                    //$setPODStatus($consignmentDataInstant, $summaryStatus, $dateDelivered);
                            }
                            
                            
                            if($finalStatus == 'Delivered' || $summaryStatus == 'Delivered' || $summaryStatus == 'Collected' || $finalStatus == "Collected" || $summaryStatus == "Collected" || strtolower($conStatus) == 'delivered')
                            {
                                    $status = Consignment::STATUS_DELIVERED;
                                    $dateDelivered = $dateTime;
                                    
                                    
                            }
                            
                            
                            
                            
                         }
                        
                         /*
                    
			$htmlNew		.=	'<tr>
								 <td>'.$dateTime.'</td> <!-- Title -->
								 <td>'.$location.'</td>
								 <td></td>
								 <td></td> 				
								 </tr>';*/
                        
                        
                        
                        //echo $location;
								 
			
		
		}
                
                
                
                
               
                 
                 
               
                 
                
                
               
                
                $consignmentFilter   =     new ConsignmentFilter();
                $consignmentFilter->addFieldFilter("hawb", $consignmentDataInstant->getAwb());
                $consignmentFilter->addFieldNotFilter("consignment_status", "recycled");
                $consignmentFilter->addFieldNotFilter("consignment_status", "invalid");
                $consignmentFilter->addFilter("handling not like 'RTN%'");
                $dhllist = $consignmentFilter->getColumnList($getConsignmentColumns());	
                
                
                
                
               
                
                
               
                $finalMilestatus = '';
                $lastTrackPoint = '';
              
                
                    ///////////////////////////// final mile tracking starting from here /////////////////////////
               
                    if(count($dhllist) > 0)
                    {
                   //$conDhl = $dhllist[0];
                   //$courierTracking .= $GetDHLTrackingHTML($conDhl->getAwb());     
                        

                        if(count($dhllist) > 0)
                        {
                            $conDhl = $dhllist[0];
                            
                            //if($conDhl->getHandling() != 'VIVEXPEUR')
                            {
                                $client = new SoapClient(null, 
                                array(
                                                'location' => "http://oneworldexpress.co.uk/remote/main/smartsystemintegration.php?wsdl",
                                                'uri'      => "http://oneworldexpress.co.uk/remote/main/index.php"));   
                                
                                
                                //echo $conDhl->getAwb();


                            //print_r(array($tracking_number));
                            //5263767364
                                $results =  $client->__soapCall('getMultiTracking', array('consignmentinformation' => array($conDhl->getAwb())));    
                                $array = json_decode($results[1], true);

                                if(count($array[0]['Tracking']['History']) > 0)
                                {
                                    $trackingArray = $array[0]['Tracking']['History'];
                                    $finalStatus = $array[0]['Tracking']['Status'];                         
                                    //$DateArray = array();
                                    for($i=1;$i<=count($trackingArray);$i++)
                                    {
                                        //if(!in_array($trackingArray[$i]['DateTime'], $DateArray))
                                        if($trackingArray[$i]['DateTime'] != '')
                                        {
                                            $lastTrackPoint = $trackingArray[$i]['TrackPoint'];
                                            $courierTracking .= "<tr>";
                                            $courierTracking .= "<td>" . $trackingArray[$i]['DateTime'] . "</td>";					
                                            $courierTracking .= "<td>" . $trackingArray[$i]['TrackPoint'] . "</td>";					
                                            $courierTracking .= "<td>" . $trackingArray[$i]['EventContent'] . "</td>";
                                            $courierTracking .= "<td>" . $trackingArray[$i]['Other'] . "</td>";                        
                                            $courierTracking .= "</tr>";	
                                            //$DateArray[] = $trackingArray[$i]['DateTime'];     
                                        }

                                    }
                                }                        

                                if($conDhl->getConsignmentStatus() == Consignment::STATUS_DELIVERED)
                                {
                                    $finalMilestatus =  Consignment::STATUS_DELIVERED;
                                }    
                                
                            }                            
                        }


                    }
                
                if($finalMilestatus == Consignment::STATUS_DELIVERED && $consignmentDataInstant->getHandling() == 'T48RTN')
                {
                    $status =  Consignment::STATUS_DELIVERED;
                }               
                elseif($consignmentDataInstant->getHandling() != 'T48RTN')
                {
                        if($consignmentDataInstant->getConsignmentStatus() == Consignment::STATUS_DELIVERED)
                        {
                            $status =  Consignment::STATUS_DELIVERED;
                        }

                }
                else
                {
                    $status = "Intransportation";
                }
                
                
                
               
                
                
               
              
               
		
		//echo $status;	
                /*
                if($trackingNumber == 'MU187910172GB')    
                {
                    echo "Tracking number "  . $trackingNumber;
                    die;
                }
                */
                
                
                
                
		
		
		$html 	= "<div id='table_container' class='table-bordered'>";
		$html	.=	$getItemTrackingDetail($trackingNumber, $originCountry , $country, '', $status, $mailData, $dateDelivered);
		$html	.=	'<table id="events"  class="table table-striped table-bordered table-advance table-hover" > <!-- Stored number tracking information here -->
          <tr>
            <th>Date Time</th> <!-- Title -->
            <th>Track Point</th>
            <th>Event Content</th>
            <th>Other</th> 
          </tr>';
                
                
                
               
		
		$html   .= $oneworldDataTracking($trackingNumber);  
		$html	.= $chinaTracking($trackingNumber);                 
		$html	.= $oneworldInternalTrackingAsc($trackingNumber);	
		$html	.= $htmlNew;
                $html   .= $courierTracking;
		
                
		$html	.=	'</table>';
		$html 	.= "</div>";
                
                
		
		$html = $sortHtml($html, $trackingNumber, $originCountry, $country, $otherData, $status, $mailData, date('Y-m-d G:i', strtotime($dateDelivered)));
                
                if($finalMilestatus == Consignment::STATUS_DELIVERED && $consignmentDataInstant->getHandling() == 'T48RTN')
                {
                    
                    if($consignmentDataInstant->getId() > 0)

                    {
                        $sql_update_columns = "date_booked = null, date_delivered = null, consignment_status = 'received'"; // setting columns
                        $sql_where_clause = " id = " . $consignmentDataInstant->getId() . "  and isinvoiced != 'Y' and handling = 'T48RTN'";
                        $consignmentDataInstant->bulkUpdate($sql_update_columns, $sql_where_clause);
                        
                        $setPODStatus($consignmentDataInstant, $lastTrackPoint);
                        
                        //$sql_update_columns = "courier_status = '" . $lastTrackPoint. "', last_status = ". $lastTrackPoint. "'"; // setting columns
                        //$sql_where_clause = " consignment_id = " . $consignmentDataInstant->getId();
                        //$consignmentDataInstant->bulkUpdate($sql_update_columns, $sql_where_clause);
                        
                        //echo $sql_update_columns . " " . $sql_where_clause;
                    }					
                    
                }

	die;






$brt = new BrtItaly();
$brt->tracking('6220500038', 'parcel', false);
die;

$anpost = new Anpost();
$anpost->tracking('CE772515092IE','parcel', true);
die;

$tracking = new Tracking();
$tracking->updateCarrierTracking('CE772523669IE', '235', '1');
die;



$tourline = new Tourline();
$tourline->tracking('DAC100012LHR','', true);
die;

$dac = new Dac();
$dac->tracking('DAC100012LHR','', true);
die;




$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => "https://www.smarttrack.co/api/token",
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "POST",
  CURLOPT_POSTFIELDS => "grant_type=smarttrack_app&client_id=kaab&client_secret=Cybernet123%40",
  CURLOPT_HTTPHEADER => array(
    "Accept: */*",
    "Cache-Control: no-cache",
    "Connection: keep-alive",
    "Content-Type: application/x-www-form-urlencoded",
    "Host: www.smarttrack.co",
    "Postman-Token: c1a6f0e5-9f07-4ede-8af9-ca8fdde38615,eee3f1aa-b7eb-4ac2-8eea-4153267a36dd",
    "User-Agent: PostmanRuntime/7.15.0",
    "accept-encoding: gzip, deflate",
    "cache-control: no-cache",
    "content-length: 69",
    "cookie: gl__session=l09kqno23eedus9tlijbq15uu7"
  ),
));

$response = curl_exec($curl);
$err = curl_error($curl);

curl_close($curl);

if ($err) {
  echo "cURL Error #:" . $err;
} else {
  echo $response;
}

die;




///////////////// LIVE /////////////////
    $trackingNumber = '3348909011362580';	
    $url = "https://www.myparcellabel.co.uk/";
    $username = "oneworldapi";
    $password = "16Sept6214";
    //echo "tracking number " . $password;		

    $opts = array(
    'http' => array(
    'method' => "GET",
    'header' => "Authorization: Basic " . base64_encode("$username:$password")
    )
);

       $context = stream_context_create($opts);
       $data = file_get_contents($url. "login", false, $context);
       $xmlResponse = simplexml_load_string($data);
       print_r($xmlResponse);
       
        if ( substr( $trackingNumber, 0, 1 ) == 0 )
				$hermesGermanyTrNo = substr($trackingNumber, 1);	 
			 else
				 $hermesGermanyTrNo = $trackingNumber;	 
                         
       if ($xmlResponse->Status == 'Success') {			
            $auth_token = $xmlResponse->AuthToken;
            $client_name = $xmlResponse->ClientName;               
        		 
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "https://myparcellabel.co.uk/tracking/".$hermesGermanyTrNo);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
             'Content-type: application/xml',
             'authToken: '.trim($auth_token),
             'Carrier: Hermes'
           ));

			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 300);
			$data = curl_exec($ch);
			$xml = new SimpleXMLElement($data);
                        $json = json_encode($xml);
                        $jsonArray = json_decode($json, true);
                        echo '<pre>';
                        print_r($jsonArray);
                        die;
                        
			$result = $xml2array($data);
			$trackingResult = $result['TrackingResult']['Events']['TrackingEvent'][1];
			
			$api_status = trim($result['TrackingResult']['Status']);
			
			if($api_status != 'Failed')
			{		
			
				if(count($trackingResult) > 0)
				{
					$trackingResult = $result['TrackingResult']['Events']['TrackingEvent'];			
					
						foreach($trackingResult as $event)
						{
							
							 $desc = $event['Event'];
							 
							 $htmlNew	.=	'<tr>
											 <td>'. date("Y-m-d G:i", strtotime($event['DateTimeStamp'])).'</td>
											 <td>'.$desc.'</td>
											 <td></td>
											 <td></td> 				
											 </tr>';
										 
							 if(trim($desc) == 'The parcel has been delivered.')
							 {
								$status = Consignment::STATUS_DELIVERED; 
							 }
													 
		
						}	
				}
		    }
			curl_close($ch);
       }
die;

$mailNo = 'JD1234567890123456';
$cpCode = 'ONEWORLD';
$status = true;
$statusCode = "S01";
$message = 'abc test';

$client = new SoapClient(null, 
            array(
                    'location' => "https://oneworldexpress.co.uk/remote/main/smartsystemintegration.php?wsdl",
                    'uri'      => "https://oneworldexpress.co.uk/remote/main/index.php"));   

            $results =  $client->__soapCall('getTracking', array('consignmentinformation' => $mailNo.'||json'));
            $trackingData = json_decode($results);
            //echo '<pre>';
          // print_r($trackingData); //die;
            $message = $trackingData->Tracking->Status;

            if(trim($message) == "not found")
            {
                $status = "false";
                $statusCode = "C01";
                $message = "Mailno doesn�t exist";

                $returnData = '<response>
                               <mailNo>'.$mailNo.'</mailNo>
                               <cpcode>'.$cpCode.'</cpcode >
                               <success>'.$status.'</success>
                               <code>'.$statusCode.'</code>
                               <message>'.$message.'</message>';
                                $returnData .= '</response>';
                return $returnData;
            }
            else
            {
                $returnData = '<response>
                               <mailNo>'.$mailNo.'</mailNo>
                               <cpcode>'.$cpCode.'</cpcode >
                               <success>'.$status.'</success>
                               <code>'.$statusCode.'</code>
                               <message>Tracking Found</message>';
                if(!empty($trackingData->Tracking) && count($trackingData->Tracking) > 0)
                {
                    $destinationCountry = "";
                    $originCountry = "";

                    if(!empty($trackingData->Tracking))
                    {
                        $originCountry = $trackingData->Tracking->OriginCountry;
                        $destinationCountry = $trackingData->Tracking->DestinationCountry;

                        $countryFilter = new CountryFilter();
                        $countryFilter->addIsoFilter($destinationCountry);
                        $destCountryList = $countryFilter->getColumnList("name");

                        if(count($destCountryList) > 0)
                        {
                            $destCountry = $destCountryList[0];
                            $destCountryName = $destCountry->getName();
                        }

                        $countryFilter = new CountryFilter();
                        $countryFilter->addIsoFilter($originCountry);
                        $originCountryList = $countryFilter->getColumnList("name");

                        if(count($originCountryList) > 0)
                        {
                            $originCountry = $originCountryList[0];
                            $originCountryName = $originCountry->getName();
                        }
                    }
                    $trackingDataEvent = "";
                    if(!empty($trackingData->Tracking->History))
                    {
                        $ocData = [];
                        $dcData = [];
                       
                        foreach ($trackingData->Tracking->History as $dateEventData)
                        {
                            if($dateEventData->TrackPoint == 'Manifest Generate')
                            {
                                $dateEventData->TrackPoint = $originCountryName;
                                $code = 'PU_PICKUP_SUCCESS';
                            }
                            
                            if($dateEventData->TrackPoint == $originCountryName)
                            {
                                $ocData[] = $dateEventData;
                            }
                            else
                            {
                                $dcData[] = $dateEventData;
                            }
                        }
                        
                        //total 16 
                        $codeArray = array('PU_SIGN_IN_SUCCESS','SC_INBOUND_SUCCESS','SC_SIGN_IN_SUCCESS','SC_OUTBOUND_SUCCESS',
                                           'SC_HO_OUT_SUCCESS','LH_POST_COLLECTION','CC_EX_SUCCESS','LH_ARRIVE','GTMS_ACCEPT','GTMS_SC_ARRIVE','LH_DEPART',
                                           'LH_ARRIVE','GTMS_DO_ARRIVE','GTMS_DELIVERING','GTMS_RE_DELIVERING');
                        $index = 0;
                        if(!empty($ocData))
                        {
                            $trackingDataEvent .= '<group>
                                                    <type>OC</type>';
                            foreach($ocData as $ocTrack)
                            {
                                $eventContent = "";
                                if($ocTrack->EventContent == '')
                                {
                                    $eventContent = $ocTrack->Other;
                                }
                                else
                                {
                                    $eventContent = $ocTrack->EventContent;
                                }
                                
                                 if (strpos($eventContent, 'delivered') !== false) 
                                {
                                    $code = "GTMS_SIGNED";
                                }
                                if ($code == '')
                                {
                                    $code = $codeArray[$index];
                                    $index++;
                                }
                                
                                if($ocTrack->Other == '')
                                {
                                    $description = $ocTrack->TrackPoint .', '.$eventContent;                                 
                                }
                                else
                                {
                                    $description =  $ocTrack->TrackPoint .', '. $eventContent.', '.$ocTrack->Other;
                                }
                                
                                
                                $trackingDataEvent .= '<track>
                                                        <time>'.date("Y-m-d H:i:s", strtotime($ocTrack->DateTime)).'</time>
                                                        <country>'.$originCountryName.'</country> 
                                                        <city>'.$originCountryName.'</city>
                                                        <facilityName>'.$ocTrack->Other.'</facilityName>
                                                        <timeZone>+8</timeZone>
                                                        <desc>'.$description.'</desc>
                                                        <actionCode>'.$code.'</actionCode>
                                                     </track>';
                                $code = '';
                            }
                            $trackingDataEvent .= '</group>';
                        }
                        if(!empty($dcData))
                        {
                            $trackingDataEvent .= '<group>
                                                    <type>DC</type>';
                            foreach($dcData as $dcTrack)
                            {
                                $eventContent = "";
                                $statusoneworld = $trackingData->Tracking->Status;
                                
                                if ($code == '')
                                {
                                    $code = $codeArray[$index];
                                    $index++;
                                }
                                
                                if($dcTrack->EventContent == '')
                                {
                                    $eventContent = $dcTrack->Other;
                                }
                                else
                                {
                                    $eventContent = $dcTrack->EventContent;
                                }
                                
                                 if (strpos(strtolower($eventContent), 'delivered') !== false) 
                                {
                                    $code = "GTMS_SIGNED";
                                }
                                
                                if($ocTrack->Other == '')
                                {
                                    $description = $dcTrack->TrackPoint .', '.$eventContent;                                 
                                }
                                else
                                {
                                    $description =  $dcTrack->TrackPoint .', '. $eventContent.', '.$dcTrack->Other;
                                }
                                
                                $trackingDataEvent .= '<track>
                                                    <time>'.date("Y-m-d H:i:s", strtotime($dcTrack->DateTime)).'</time>
                                                    <country>'.$destCountryName.'</country> 
                                                    <city>'.$destCountryName.'</city>
                                                    <facilityName>'.$dcTrack->Other.'</facilityName>
                                                    <timeZone>+8</timeZone>
                                                    <desc>'.$description.'</desc>
                                                    <actionCode>'.$code.'</actionCode>
                                                 </track>';
                            $code = '';
                            }
                            $trackingDataEvent .= '</group>';
                        }
                    }
                    $returnData .=    '<tracesElement>
                                       <destinationCountry>'.$destCountryName.'</destinationCountry>
                                       '.$trackingDataEvent.'
                                     </tracesElement>';
                }
                $returnData .= '</response>';
                echo $returnData;
                die;
            }


        
$glsnl = new GlsNetherland();
$glsnl->tracking('21520008700083','parcel','');
die;

$coolrun = new CoolRunner();
$coolrun->tracking('6229590945', 'parcel', false);
die;




$hermes = new HuxloeHermes();
$hermes->tracking('3394901011362583', 'parcel', false);
die;

$dpduk = new DPDUK(); //15502414102021
$dpduk->tracking('15502414101956', 'parcel', false);
die;



$cacesa = new CacesaExpress();
$cacesa->tracking('0028037097504405001072004895', 'parcel', false);
die;












$brt = new BrtItaly();
$brt->tracking('6220047553', 'parcel', false);
die;




$wndirect = new WNDirect();
$wndirect->tracking('', 'parcel', true);
die;


$dpdde = new DPDDE(); //15502414102021
$dpdde->tracking('09446081542401', 'parcel', false);
die;




$ukmail = new UKMail();
$ukmail->tracking('40894470009506', 'parcel', false);
die;

$yodel = new Yodel();
$yodel->tracking('JD0002210163461227', 'parcel', true);
die;

$whisl = new Whistl();
$whisl->tracking('OWE60464WHI', 'parcel', true);
die;






$fastway = new FastWay();
$fastway->tracking('OC1000002033', 'shipment', false);
die;

$brt = new CanadaPost();
$brt->tracking('4011243137811925', 'shipment', false);
die;








$consignment = new Consignment(86887);
$testYodelZpl = new DaiPost();
print_r($testYodelZpl->label($consignment,'pdf', ''));
die;


$consignment = new Consignment(67737);
$asendia = new AsendiaUk();
print_r($asendia->label($consignment,"pdf",""));
die;

$testTracking = new DPD();
echo $testTracking->tracking('15502414101948','', false);
die;

$consignment = new Consignment(757);
$cacesaLabel = new CoolRunner();
$result = $cacesaLabel->label($consignment);
print_r($result);
exit;
$WS_URL = 'http://tws1.cacesa.com/wstest/wscacesa.asmx?wsdl'; // TEST URL
//$WS_URL = 'http://tws1.cacesa.com/ws/wscacesa.asmx?wsdl';
$WS_USER = 'onew';
$WS_PASSWORD = '1world3$';
$WS_Dir = 'ws/';

$client = new SoapClient($WS_URL, array("trace" => 1, "exception" => 0));
$header = new SoapHeader("http://tws1.cacesa.com/wsTest/", "Authentication", array('User' => $WS_USER, 'Password' => $WS_PASSWORD), false);

$authParam = array("isAuthenticated" => '1');

$authResult = $client->__soapCall("isAuthenticated", $authParam, NULL, $header);
print_r($authResult);

$paramPostCode = array('GetPostalCode xmlns="http://tws1.cacesa.com/wsTest/"' => array(
        'CONCOUNTRY' => utf8_encode("ES"),
        'CONCP' => utf8_encode("41909"),
        'PUPCODE' => '',
        'REGISTERED' => 'Y',
        'ResultType' => 'json'
    )
);

$raw_req = '<GetPostalCode xmlns="http://tws1.cacesa.com/wsTest/">
                <CONCOUNTRY>ES</CONCOUNTRY>
                <CONCP>41909</CONCP>
                <PUPCODE></PUPCODE>
                <REGISTERED>Y</REGISTERED>
                <ResultType>json</ResultType>
            </GetPostalCode>';
$soapBody = new \SoapVar($raw_req, \XSD_ANYXML);
try {

    //  $postcode_response = $client->__soapCall("GetPostalCode", $paramPostCode,NULL, $header,$out); //, true, 'rpc'
    $postcode_response = $client->__soapCall("GetPostalCode", array($soapBody), null, $header, $out); //, true, 'rpc'
    //  echo "<pre>Out:<br />"; var_dump($out); echo "</pre>";
    echo "<pre>postcode response:<br />";
    var_dump($postcode_response);
    echo "</pre>";
} catch (Exception $e) {
    echo $e->getCode() . " => " . $e->getMessage();
}

die;
$WS_URL = 'http://tws1.cacesa.com/wstest/wscacesa.asmx?wsdl'; // TEST URL
//$WS_URL = 'http://tws1.cacesa.com/ws/wscacesa.asmx?wsdl';
$WS_USER = 'onew';
$WS_PASSWORD = '1world3$';
$WS_Dir = 'ws/';


$client = new SoapClient($WS_URL, array("trace" => 1, "exception" => 0));
$header = new SoapHeader("http://tws1.cacesa.com/wsTest/", "Authentication", array('User' => $WS_USER, 'Password' => $WS_PASSWORD), false);

$authParam = array("isAuthenticated" => '1');

$authResult = $client->__soapCall("isAuthenticated", $authParam, NULL, $header);
print_r($authResult);
if ($authResult->isAuthenticatedResult) {




    // var_dump($client->__getFunctions());
    $contact = $consignment->getContact();
    if ($contact == "")
        $contact = $consignment->getCompany();
    $paramPostCode = array('CONCOUNTRY' => 'ES',
        'CONCP' => '41909',
        'PUPCODE' => '',
        'REGISTERED' => 'Y',
        'ResultType' => 'json');

    print_r($paramPostCode);

    //  $client->__setSoapHeaders ( "Authentication", array('User' => $WS_USER, 'Password' => $WS_PASSWORD), false);

    $postcode_response = $client->__soapCall("GetPostalCode", $paramPostCode, null, $header, true); //, true, 'rpc'
//$postcode_response = $client->GetPostalCode($paramPostCode);
    echo "<pre>";
    var_dump($postcode_response);
    print_r($postcode_response);
    exit;
//$consignment->setApiData($paramPostCode, print_r($postcode_response, true), 'GetPostalCode');

    if ($postcode_response["faultcode"] != '') {
        return "ERROR||" . $postcode_response["faultstring"];
    } else {
        $contact = $consignment->getContact();
        if ($contact == "")
            $contact = $consignment->getCompany();
        $GetPostCode_result = json_decode($postcode_response["GetPostalCodeResult"]);


        $certificateCode = $GetPostCode_result[0]->PCODE;
        if ($certificateCode == "ERR:001") {
            return "ERROR|| Incorrect Postcode.";
        }
//	mail("mruga@oneworldexpress.com","asdf", $certificateCode);
        if ($certificateCode != '') {
            $param = ' <PrintPostalLabelDetailsXML xmlns="http://tws1.cacesa.com/wsTest/">
                                                                                        <CERTIFICATECODE>' . $certificateCode . '</CERTIFICATECODE>
                                                                                        <CONNAME>' . htmlspecialchars($contact) . '</CONNAME>
                                                                                        <CONADDR1>' . htmlspecialchars($consignment->getAddressLine1()) . '</CONADDR1>
                                                                                        <CONADDR2>' . htmlspecialchars($consignment->getAddressLine2()) . " " . htmlspecialchars($consignment->getAddressLine3()) . '</CONADDR2>
                                                                                        <CONTOWN>' . ($consignment->getCity()) . '</CONTOWN>
                                                                                        <CONCP>' . ($consignment->getPostcode()) . '</CONCP>
                                                                                        <SHCOUNTRY />
                                                                                        <CONCOUNTRY>' . ($consignment->getCountryIsoCode()) . '</CONCOUNTRY>
                                                                                        <CONCONTACT />
                                                                                        <CONTEL>' . ($consignment->getTelephone()) . '</CONTEL>
                                                                                        <PACKAGES>' . $consignment->getNumberPieces() . '</PACKAGES>
                                                                                        <WEIGHT>' . number_format($consignment->getWeight(), 2) . '</WEIGHT>
                                                                                        <DELIVERYDESC>' . htmlspecialchars($consignment->getDescription()) . '</DELIVERYDESC>
                                                                                        <REQINSTR />
                                                                                        <DECVAL>' . $consignment->getValue() . '</DECVAL>
                                                                                        <REGISTERED>Y</REGISTERED>
                                                                                        <PUPCODE />
                                                                                        <Language>EN</Language>
                                                                                        <ResultType>json</ResultType>
                                                                                    </PrintPostalLabelDetailsXML>';


            $PrintPostalLabelDetailsResult = $client->call('PrintPostalLabelDetailsXML', $param, 'http://tws1.cacesa.com/wstest/', '', $header); //, true, 'rpc'
            $consignment->setApiData($param, print_r($PrintPostalLabelDetailsResult, true), 'PrintPostalLabelDetailsXML');

            if ($PrintPostalLabelDetailsResult != '') {

                $label_link = $PrintPostalLabelDetailsResult["PrintPostalLabelDetailsXMLResult"]["string"];

                if (strpos($label_link, 'ERR') !== false) {
                    $errorcode = str_replace("ERR:", "", $label_link);
                    if ($errorcode == "001") {
                        $error_message = "REGISTERED field is invalid or empty";
                    } else if ($errorcode == "002") {
                        $error_message = "CERTIFICATECODE is invalid";
                    } else if ($errorcode == "003") {
                        $error_message = "Consigneeâ€™s name is invalid or empty";
                    } else if ($errorcode == "004") {
                        $error_message = "Consigneeâ€™s address is invalid or empty";
                    } else if ($errorcode == "005") {
                        $error_message = "Consigneeâ€™s country is invalid or empty.";
                    } else if ($errorcode == "006") {
                        $error_message = "Consigneeâ€™s postal code is invalid or empty";
                    } else if ($errorcode == "007") {
                        $error_message = "Consigneeâ€™s town is empty";
                    } else if ($errorcode == "008") {
                        $error_message = "Package number is empty or less than 1.";
                    } else if ($errorcode == "009") {
                        $error_message = "Weight is empty or less than 0,1.";
                    } else if ($errorcode == "010") {
                        $error_message = "Declared value is empty or less than 0,1";
                    } else if ($errorcode == "050") {
                        $error_message = "An error has occurred during the transaction, verify incorrect characters or field's precision";
                    }
                    return "ERROR||" . $error_message;
                } else {
                    $pdf_decoded = file_get_contents($label_link);
                    if (trim($pdf_decoded) == '') {
                        return "ERROR||We didn't recieved any response, the service is temprary unavailable. Please contact to itsupport@oneworldexpress.com.";
                    }
                    $path = SETTING_DIR_ASSETS . 'pdf/' . date('Y_m_d') . '/' . $consignment->getId() . ".pdf";
                    $fp = fopen($path, 'wb+');
                    fwrite($fp, $pdf_decoded);
                    fclose($fp);
                    $parcel = new ParcelFilter();
                    $parcel->addConsignmentIdFilter($consignment->getId());
                    $parcelList = $parcel->getList();
                    if (count($parcelList) > 0) {
                        $parcelList[0]->setTrackingNumber($certificateCode);
                        $parcelList[0]->save();
                    }
                    return "SUCCESS||" . SETTING_MAIN_URL . "_assets/pdf/" . date('Y_m_d') . "/" . $consignment->getId() . ".pdf||" . $certificateCode;
                }
            } else {
                return "ERROR||Unable to create label.";
            }
        } else {
            return "ERROR|| Invalid postcode or address.";
        }
    }
}




