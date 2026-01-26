<?php
require_once("../includes/settings/config.inc.php");
include_classes([
        'iaddress.class',
    'consignment.class',
    'consignmentfilter.class',
    'parcel.class',
    'parcelfilter.class',
    'addressfilter.class',
    'address.class',
    'country.class',
    'countryfilter.class',
    'services.class',
    'servicefilter.class',
    'carrier.class',
    'carrierfilter.class',
    'dropoffuserlocation.class',
    'dropoffuserlocationfilter.class',
    'userservicesrouting.class',
    'userservicesroutingfilter.class',
    'agentdatafilter.class',
    'agentdata.class',
    'serviceagentmapping.class',
    'serviceagentmappingfilter.class',
    'carrierservicecustomizerules.class',
    'carrierservicecustomizerulesfilter.class',
    'carrierservicedefaultrules.class',
    'carrierservicedefaultrulesfilter.class',
    'remoteareas.class',
    'remoteareasfilter.class',
    'consignmentlog.class',
    'consignmentlogfilter.class',
    'currency.class',
    'customizedservicesrouting.class',
    'customizedservicesroutingfilter.class',
    'paymentshistory.class',
    'paymentshistoryfilter.class',
    'consignmentcharges.class',
    'consignmentchargesfilter.class',
    'tariffs.class',
    'tariffsfilter.class',
    'serviceconstantvaluefilter.class',
    'serviceconstantvalue.class',
    'servicerangemappingfilter.class',
    'servicerangemapping.class',
    'licenceplatefilter.class',
    'licenceplate.class',
    'warehousefilter.class',
    'warehouse.class',
    'trackingfilter.class',
    'tracking.class',
    'trackingdatafilter.class',
    'trackingdata.class',
    
]);
include_classes([
    'pdfmerger',
    'parcelforyou.class',
    'royalmail.class',
    'hermes.class',
    'ptwo.class',
    'kronosexpress.class',
    'tourline.class',
    'coolrunner.class',
    'cpostips.class',
    'dhl.class',
    'skypostal.class',
    'pitneybowes.class',
    'asendiauk.class',
    'fastway.class',
    'kaab.class',
    'warenpost.class',
    'belgiumpost.class',
    'commercialinvoice.class',
    'warenpost.class',
    'omniva.class',
    'ups.class'
    ], 'labels');
include_classes([
    'nusoap'
    ], '3rdparty/nusoap');
echo SETTING_DIR_ASSETS . "pdf/palletlabel_3922.pdf";
echo "<br>";
echo SETTING_DIR_ASSETS . "/pdf/watermark.png ";
var_dump( exec("composite -gravity center -quality 100 -density 400 -dissolve 50% "
        . "" . SETTING_DIR_ASSETS . "/pdf/watermark.png "
        . "" . SETTING_DIR_ASSETS . "pdf/palletlabel_3922.pdf "
        . "" . SETTING_DIR_ASSETS . "pdf/xx.pdf ", $output));
                    
die;


echo "<pre>";
echo json_encode( unserialize('a:41:{s:18:"sender_country_iso";s:2:"GB";s:12:"service_code";s:9:"STDHL0WPX";s:15:"order_reference";s:14:"LS000119986-01";s:14:"sender_contact";s:19:"London Sock Company";s:12:"sender_email";s:23:"JMANLEY@iforcegroup.com";s:14:"sender_company";s:19:"London Sock Company";s:11:"sender_name";s:13:"Janine Manley";s:21:"sender_address_line_1";s:15:"Long Croft Road";s:21:"sender_address_line_2";s:5:"Corby";s:21:"sender_address_line_3";s:16:"Northamptonshire";s:11:"sender_city";s:16:"Northamptonshire";s:12:"sender_state";s:0:"";s:15:"sender_postcode";s:8:"NN18 8EY";s:16:"sender_telephone";s:11:"07818531639";s:20:"receiver_country_iso";s:2:"AE";s:16:"receiver_contact";s:15:"Craig Henderson";s:14:"receiver_email";s:29:"craigbrianhenderson@gmail.com";s:16:"receiver_company";s:0:"";s:23:"receiver_address_line_1";s:25:"Address Residence Jlt Clu";s:23:"receiver_address_line_2";s:25:"Apartment 3108 New Dubai ";s:23:"receiver_address_line_3";s:5:"Dubai";s:13:"receiver_city";s:5:"Dubai";s:14:"receiver_state";s:0:"";s:17:"receiver_postcode";s:0:"";s:18:"receiver_telephone";s:11:"07368389768";s:11:"fullpallets";N;s:11:"halfpallets";N;s:10:"qtrpallets";N;s:11:"palletlifts";N;s:5:"value";i:34;s:8:"currency";N;s:8:"itemtype";N;s:5:"notes";N;s:11:"description";s:100:"SCK32 RED SPOT M42 44                    Mayfair Merino Wool Red Spot SCK32 NAVY M42 44 GREY        ";s:18:"consignment_status";N;s:16:"insurance_amount";N;s:6:"parcel";a:1:{i:0;a:6:{s:6:"weight";d:0.32600000000000001;s:6:"length";d:0.90000000000000002;s:5:"width";d:0.90000000000000002;s:6:"height";d:0.40000000000000002;s:9:"itemvalue";d:0;s:5:"items";a:6:{i:0;a:7:{s:16:"item_description";s:69:"SCK32-RED-SPOT-M42-44                    Mayfair Merino Wool Red Spot";s:11:"No_of_items";i:1;s:10:"item_value";d:0.01;s:6:"weight";d:0.065000000000000002;s:9:"tariff_No";N;s:6:"hscode";s:10:"6115950000";s:23:"manufacture_country_iso";s:2:"PT";}i:1;a:7:{s:16:"item_description";s:70:"SCK32-NAVY-M42-44-GREY                   Mayfair Merino Wool Navy & Gr";s:11:"No_of_items";i:1;s:10:"item_value";d:0.01;s:6:"weight";d:0.065000000000000002;s:9:"tariff_No";N;s:6:"hscode";s:10:"6115950000";s:23:"manufacture_country_iso";s:2:"PT";}i:2;a:7:{s:16:"item_description";s:70:"SCK32-RED-STRIPE-M42-44                  Mayfair Merino Wool Red Strip";s:11:"No_of_items";i:1;s:10:"item_value";d:0.01;s:6:"weight";d:0.065000000000000002;s:9:"tariff_No";N;s:6:"hscode";s:10:"6115950000";s:23:"manufacture_country_iso";s:2:"PT";}i:3;a:7:{s:16:"item_description";s:67:"PACKAGING-3PAIR-GIFT-BOX                Packaging - 3 Pair Gift Box";s:11:"No_of_items";i:1;s:10:"item_value";d:0.01;s:6:"weight";d:0.001;s:9:"tariff_No";N;s:6:"hscode";s:8:"48191000";s:23:"manufacture_country_iso";s:2:"PT";}i:4;a:7:{s:16:"item_description";s:64:"SCK32-NAVY-M42-44-ORANGE                Mayfair Merino Wool Navy";s:11:"No_of_items";i:1;s:10:"item_value";d:17;s:6:"weight";d:0.065000000000000002;s:9:"tariff_No";N;s:6:"hscode";s:10:"6115950000";s:23:"manufacture_country_iso";s:2:"PT";}i:5;a:7:{s:16:"item_description";s:70:"SCK32-NAVY-SPOT-M42-44                   Mayfair Merino Wool Navy Spot";s:11:"No_of_items";i:1;s:10:"item_value";d:17;s:6:"weight";d:0.065000000000000002;s:9:"tariff_No";N;s:6:"hscode";s:10:"6115950000";s:23:"manufacture_country_iso";s:2:"PT";}}}}s:10:"label_type";s:3:"pdf";s:10:"label_size";s:7:"100x150";s:6:"weight";d:0.32600000000000001;s:8:"api_uuid";s:0:"";}'));
die;
$url = "https://www.myparcellabel.co.uk/";
$username = "oneworldapi";
$password = "16Sept6214";

$trackingNumber = '24146174114591';

$opts = array(
    'http' => array(
        'method' => "GET",
        'header' => "Authorization: Basic " . base64_encode("$username:$password")
    )
);

$context = stream_context_create($opts);
$data = file_get_contents($url . "login", false, $context);
$xmlResponse = simplexml_load_string($data);

if ($xmlResponse->Status == 'Success') {
    $auth_token = $xmlResponse->AuthToken;
    $client_name = $xmlResponse->ClientName;

    //$hermesGermanyTrNo =  substr($trackingNumber, 1, strlen($trackingNumber));

    if (substr($trackingNumber, 0, 1) == 0) {
        $hermesGermanyTrNo = substr($trackingNumber, 1);
    } else
        $hermesGermanyTrNo = $trackingNumber;



    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://www.myparcellabel.co.uk/Tracking/" . $hermesGermanyTrNo);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'AuthToken: ' . $auth_token,
        'Carrier: HERMESDE'
    ));

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 300);
    $data = curl_exec($ch);
    print_r($data);
}


die;

$soap = new SoapClient("http://www.hermes-europe.co.uk/parceltrackingservice/services/parcelTrackingService?wsdl");
$inputparameters1 = array('barcode' => '24146135116374',
    'clientGroupId' => "99046",
    'clientLicence' => "68f10e31-fdbf-41e6-b668-66cd4ea3e7e0"
);
$xmlarr = $soap->fetchTracking($inputparameters1);
print_r($xmlarr);

die;

$consignmentarray = '||Cetronic SL ESB15033061||Cetronic SL ESB15033061||Calle Palomar 22 Bajo||||¬La Coruna||La Coruña La Coruna||ES||15004||0034981145106||1||0.240||GADGET||4.98||USD||wanghaitao||||CACEXP||DX||DX||OkR6a!(9||0||||||||||[OW00004] Lithium Battery||0.240%%1%%1%%1||||||||Lithium Ion battery||||||';
$client = new SoapClient(null, array(
    'location' => "https://oneworldexpress.co.uk/remote/main/smartsystemintegration.php?wsdl",
    'uri' => "https://oneworldexpress.co.uk/remote/main/index.php"));
////////////////////////////////////////////  NON-ROUTING  ///////////////////////////////////////////////
$resultas = $client->__soapCall('getLabels', array('consignmentinformation' => $consignmentarray));
print_r($resultas);
die;



$removeInvalidLabelStr = "ABC123||ECOMLOG||ecomlog||Europeparcel1@";
echo "<pre>";
$client = new SoapClient(null, array(
    'location' => "https://oneworldexpress.co.uk/remote/main/smartsystemintegration.php?wsdl",
    'uri' => "https://oneworldexpress.co.uk/remote/main/index.php"));
////////////////////////////////////////////  NON-ROUTING  ///////////////////////////////////////////////
echo "API ON STRING BASE";
$resultas = $client->__soapCall('removeInvalidLabel', array('consignmentinformation' => $removeInvalidLabelStr));
print_r($resultas);




die;


echo "<pre>";
$consignmentarray = 'MICHELLE||F)54$Nqr';
$client = new SoapClient(null, array(
    'location' => "https://oneworldexpress.co.uk/remote/main/smartsystemintegration.php?wsdl",
    'uri' => "https://oneworldexpress.co.uk/remote/main/index.php"));
////////////////////////////////////////////  NON-ROUTING  ///////////////////////////////////////////////
echo "API ON STRING BASE";
$resultas = $client->__soapCall('enabledServices', array('servicesinformation' => $consignmentarray));
print_r($resultas);

$consignmentarray = 'martin12||Martin123@';
$resultas = $client->__soapCall('enabledServices', array('servicesinformation' => $consignmentarray));
print_r($resultas);

echo "<br><br><br><br><br>";
echo "API ON JSON BASE";
$consignmentarray = json_encode(array('username' => 'MICHELLE', 'password' => 'F)54$Nqr'));
$resultas = $client->__soapCall('enabledServices', array('servicesinformation' => $consignmentarray, 'dataType' => 'JSON'));
print_r($resultas);


$consignmentarray = json_encode(array('username' => 'martin12', 'password' => 'Martin123@'));

$resultas = $client->__soapCall('enabledServices', array('servicesinformation' => $consignmentarray, 'dataType' => 'JSON'));
print_r($resultas);


die;




die;

$consignmentarray['hawb'] = '';
$consignmentarray['company'] = 'One World Express';
$consignmentarray['contact'] = 'mruga';
$consignmentarray['address1'] = 'this i t ';
$consignmentarray['address2'] = 'Hayes';
$consignmentarray['address3'] = '';
$consignmentarray['city'] = 'LONDON';
$consignmentarray['countrycode'] = 'GB';
$consignmentarray['postcode'] = 'UB3 3NB'; //'KW14 7XF';
$consignmentarray['telephone'] = '02088676060';
$consignmentarray['numberpieces'] = '2';
$consignmentarray['weight'] = '3.3';
$consignmentarray['description'] = 'Items';
$consignmentarray['value'] = '0.00';
$consignmentarray['currency'] = 'EUR';
$consignmentarray['sendername'] = 'Tikiting SL';
$consignmentarray['reference'] = 'TEST_MG006';
$consignmentarray['handlingcode'] = '3HPA'; //hermes 2 day sign
$consignmentarray['account'] = 'ITTEAM';
$consignmentarray['username'] = 'developer';
$consignmentarray['password'] = 'Team123@';
$consignmentarray['both_checked'] = '0';
$consignmentarray['fullpallet'] = '';
$consignmentarray['halfpallet'] = '';
$consignmentarray['quarterpallet'] = '';
$consignmentarray['documentType'] = 'NONDOC';
$consignmentarray['notes'] = '';
$consignmentarray['dimension'] = '';
$consignmentarray['numberBoxes'] = '';
$consignmentarray['email'] = 'cs@oneworldexpress.com';
$consignmentarray['transactionid'] = '';
$consignmentarray['itemType'] = '';
$consignmentarray['blank'] = '';
$consignmentarray['blank2'] = '';
$consignmentarray['blank3'] = '';
$consignmentarray['platform'] = '';

// JSON ENCODE STRING FOR TESTING PURPOSE
$requestShipmentJson = array('consignmentinformation' => json_encode($consignmentarray), 'dataType' => 'JSON', 'labelType' => 'PDF');


// JSON ENCODE STRING FOR TESTING PURPOSE
$requestShipmentString = array('consignmentinformation' => implode('||', $consignmentarray));



$client = new SoapClient(null, array(
    'location' => "http://staging.oneworldexpress.co.uk/optimization/main/smartsystemintegration.php?wsdl",
    'uri' => "http://staging.oneworldexpress.co.uk/optimization/main/index.php"));

$results = $client->__soapCall('getLabels', $requestShipmentJson);
echo "<pre>
<strong>Json String</strong><br>";
print_r($requestShipmentJson);
echo "<br>";
print_r($results);

echo "<br>";
echo "<strong>Simple String </strong><br>";
print_r($requestShipmentString);
echo "<br>";
$resultString = $client->__soapCall('getLabels', $requestShipmentJson);
print_r($resultString);
die; /**/

$information = array();
$information[] = '7e6c28e9d3b6a384a0550674886b09fc';
$information[] = 'ca3ccb6d96dd4310a0414333194d21ad';
$information[] = 'TESTOMS';
$information[] = 'GB';
$information[] = 'GB';
$information[] = '3HPA';
$information[] = '1';
$information[] = '1';
$information[] = 'GBP';



//	$information = 'YPS||P1(s!0rW||YPS||GB||CN||YPS_ROUTING||1||1||GBP';


$client = new SoapClient(null, array(
    'location' => "http://staging.oneworldexpress.co.uk/remote/main/tariffapi.php?wsdl",
    'uri' => "http://staging.oneworldexpress.co.uk/remote/main/tariffapi.php?wsdl"));


/////////////////////////////////////////////  NON-ROUTING  ///////////////////////////////////////////////


$resultas = $client->__soapCall('GetTariffCode', array('information' => implode('||', $information)));


/////////////////////////////////////////////  ROUTING  ///////////////////////////////////////////////////
//  $resultas = $client->__soapCall('GetTariffCodeByProductName', array('information' => implode('||',$information)));


print_r($resultas);

die;







//||||Loo Jileen||St Martins Court 10 Paternoste||r Row||||London||GB||EC4M7HP||44 20 7182 3933||1||0.20||Items||0.00||EUR||Tikiting SL||TEST_MG006||UK TRACKED 48||ANBGLO||ANBGLOLN||Anbgl0.com||1||||||||NONDOC||||||||jileen.loo@cbrehotels.com||||||||||||
//||||marius leistrumas||Flat 2 Beechcroft Lodge||32 Devonshire Road||||London||GB||sm2 5hq||||1||3.3||manual booking||19.75||GBP||LINNWORKS||DBP||UK SERVICES||ANBGLOLN||ANBGLO||Anbgl0.com||1||||||||||||3300%%1%%1%%1||||mleistrumas@yahoo.co.uk||||||||||
//$string	=	j


/* $consignmentinformation ="PHPBooks||SMARTSTREAM TECHNOLOGIES INC||MARK THOMSON|| 61 BROADWAY SUITE 710||NEW YORK||NY||NEW YORK||US||10006||2124585658||4||20||Programming Books of PHP||10.30||USD||Muhammad Kazim||Reference||INTE||4444||atul||atul.com||0||0||0||0||NONDOCS||Notes||3%%3%%3%%3&&4%%4%%4%%4&&20%%20%%30%%3&&20%%20%%30%%34||20||||||||||||				   						  ";
 */
/* Hawb || company || contact || address1 || address2 || address3 || city ||country code|| postcode || telephone || numberpieces || weight || description || value || currency || sendername || reference || service code||account || username||password||Warehouse Location || Storage Location || Sequence No || empty4 || empty5 */

/* $consignmentinformation ="||company||test1||248, Uxbridge Road||Feltham||Middlesex||London||GB||UB3 3NB||11111111||1||1||description||1.00||GBP||ali||reference||ECX||4444||atul||atul.com||0||1||0||2||||||||||||||||||||";
 */
//echo 'dfs'; exit;
/* $consignmentinformation ="||Jean Pierre Burdet||Jean Pierre Burdet||La Plantaz||||||Cléry||GB||73460||33-609052650||1||0.5||Camping and Hiking Topographic||11.57||USD||LITB-EU||27705809||RM|L||4444||atul||atul.com||0||||||||||||||||||||||||||"; */
//echo 'sdsad'; exit;
//$consignmentinformation ="||MAXIM ROMERO NEGRE||MAXIM ROMERO NEGRE||CL CALDERON DE LA BARCA 53 BJ|| || ||GANDIA||ES||46701||34-962862717||1||0.5||Headphones||16.2||USD||LITB-EU||27775796||E - EUROPE SMART A||IMXMAIL||imxmail||password||1|| || || || || || || || || || || || ||";
//
//$consignmentinformation ="||HONG KONG YEE CHEN||Mr Ma||Marston Gate Fulfillment Centre||||||Ridgmont||GB||MK43 0ZA||07918 671190||1||12.01||480 pcs Heat Shrink Elect...||4530.00||CNY||win point pte ltd||FBAQDJB0DU001||E - EUROPE SMART A||IMXMAIL||imxmail||password||1||||||||||||12.01%%35.00%%40.00%%35.00||1||||||||||||";
//$consignmentinformation ="||MAXIM ROMERO NEGRE||MAXIM ROMERO NEGRE||CL CALDERON DE LA BARCA 53 BJ|| || ||GANDIA||ES||46701||34-962862717||1||0.5||Headphones||16.2||USD||LITB-EU||27775796||REGHUNUTREUR||LITB||LITB||litb.com||0|| || || || || || || || || || || || ||";
/* $consignmentinformation  = "||company||test1||320/62 bahnhofstr||Rheinbach||BERLIN||BERLIN||||11111111||1||12||description||1.00||GBP||ali||WPX||4444||reference||INT||Channel Islands"; */
//echo 'asa'; exit;

/* $consignmentinformation = "atul||atul.com";
  $results =  $client->__soapCall('enableServices', array('consignmentinformation' => $consignmentinformation));
  print_r($results); */


////dhl
/* $client = new SoapClient(null, array(
  'location' => "http://sandbox.oneworldexpress.co.uk/remote/main/smartsystemintegration.php?wsdl",
  'uri'      => "http://sandbox.oneworldexpress.co.uk/remote/main/index.php"));
  $consignmentinformation = "3664135";
  $results =  $client->__soapCall('getTracking', array('consignmentinformation' => $consignmentinformation));
  print_r($results);
 */
?>


<?php

/*
  require_once("../includes/settings/config.inc.php");


  //$consignmentinformation = "||oneworld||Bryan Thomsett||Herts, 34 Hare Street Road||as||yt||selbu||DE||4159||01763 2733003||1||1||Thank you.||1||GBP||Vivo Technologies Ltd.||refernce||19EURDPD||ATUL||atulbhakta||nikita2000||0";

  $client = new SoapClient(null, array(
  'location' => "http://hermes.oneworldexpress.co.uk/remote/main/webservicetracking.php?wsdl",
  'uri'      => "http://hermes.oneworldexpress.co.uk/remote/main/index.php"));

  //$results =  $client->__soapCall('getLabels', array('consignmentinformation' => $consignmentinformation));

  //print_r($results);

  /*$consignmentinformation = "atul||atul.com";
  $results =  $client->__soapCall('enableServices', array('consignmentinformation' => $consignmentinformation));
  print_r($results); */


////dhl
/* $consignmentinformation = "9510397466322278";
  $results =  $client->__soapCall('Tracking', array('consignmentinformation' => $consignmentinformation));
  print_r($results);
 */
?>





