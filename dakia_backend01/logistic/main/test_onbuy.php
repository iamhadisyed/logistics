<?php

require_once("../includes/settings/config.inc.php");
//error_reporting(E_ALL);
//ini_set('display_errors', 'On');

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


    //onBuy Auth Key API

    

$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => 'https://api.onbuy.com/v2/auth/request-token',
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'POST',
  CURLOPT_POSTFIELDS => array('secret_key' => 'sk_test_d5002e15b3564bac893fc303c868297f','consumer_key' => 'ck_test_b9d321ea35ed4c849ecf7ad037ca4384'),
  CURLOPT_HTTPHEADER => array(
    'Content-Type: application/x-www-form-urlencoded',
    'Cookie: PHPSESSID=f83d3b0c36295ac1bf31ece31cf8aff0'
  ),
));

$response = curl_exec($curl);

curl_close($curl);
echo $response;
    

// onBuy Create Batch Listing

$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => 'https://api.onbuy.com/v2/listings',
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'POST',
  CURLOPT_POSTFIELDS =>'{
    "site_id": 2000,
    "listings": [
        {
            "opc": "PN8JV6",
            "condition": "average",
            "price": 2.98,
            "stock": 16,
            "delivery_weight": 16,
            "handling_time": 252,
            "free_returns": "false",
            "warranty": 252,
            "sale_price": 12.02,
            "sale_start_date": "2021-05-10 00:00:00",
            "sale_end_date": "2021-05-20 00:00:00"
        }
    ]
}',
  CURLOPT_HTTPHEADER => array(
    'Content-Type: application/x-www-form-urlencoded',
    'Authorization: 86E56980-9714-4B79-92A9-472B043F2485',
    'Cookie: PHPSESSID=cfc81f7f7e6478d1c9ad3c21f1702979'
  ),
));

$response = curl_exec($curl);

curl_close($curl);
echo $response;
die;