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
    'bagging.class',
    'baggingfilter.class',
    'itemDetails.class',
    'itemDetailsFilter.class',
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
    'ups.class',
    'easysent.class',
    'owe.class',
    'deutschepost.class'
    ], 'labels');
include_classes([
    'nusoap'
    ], '3rdparty/nusoap');
error_reporting(1);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);


$consignment = new Consignment(667961);
$parcelIdArr[] = array('746081',
'746083',
'746085');
$parcelServiceIdArr[746081] = 310;
$parcelServiceIdArr[746083] = 310;
$parcelServiceIdArr[746085] = 310;

//$csv_result     =   Manifest::generateManifestCsv(12922);
//$parcel_csv_result     =   Manifest::generateManifestParcelCsv(12922, "warehouse");
Manifest::generateManifestPdf(12922);

exit;


$hermes = new EasySent();
$label = $hermes->sendData(array("0075001116A2100914231801250H", "0085170116A2100913982801250O"));
print_r($label);
exit;
$hermes->setTrackingParams(797, 1);
$hermes->tracking("T01NLA0000002621", "parcel", true);
exit;



$consignment = new Consignment(641468);
$warenpost = new Warenpost();
//$responseValidation = $warenpost->validation($consignment, $service, $country);
//print_r($responseValidation);
//die;
$response = $warenpost->label($consignment);
//$response = $warenpost->sendData(array("00340435086330000124"));

print_r($response);
exit;

/*$deuschepost = new DeutschePost();
$deuschepost->sendData(array("RS920666305DE"));
exit;*/

/*$consignment = new Consignment(604617);
$easysent = new EasySent();
$response = $easysent->label($consignment);
print_r($response);
exit;*/


$consignment = new Consignment(583290);
$royalMail = new parcelForYou();
$result = $royalMail->btocEuropeLabel($consignment);
print_r($result);
exit;

$consignment = new Consignment(602904);
$owe = new OWE();
$response = $owe->label($consignment);
print_r($response);
exit;




$warenpost = new Warenpost();
//$responseValidation = $warenpost->validation($consignment, $service, $country);
//print_r($responseValidation);
//die;
$response = $warenpost->label($consignment);
//$response = $warenpost->sendData(array("00340435086330000124"));

print_r($response);
exit;

/*
$trackingNo = '2LDE30169+99000919044021';

$warenpost = new Warenpost();
$warenpost->RotineBarcode();
exit;

$pgath = "C:/xampp/htdocs/smarttrackoptimization/_assets/pdf/2021_03_31/589352.pdf";
 $PDFMerger = new PDFMerger();
                $PDFMerger->addPDF($path);
                    $PDFMerger->merge('file',"test.pdf","hold");
                    exit;
*/
//echo "<pre>";
$parcelTrackingArr = array('OWEXGB00000122225DE000645467',
'OWEXGB00000122227DE000657797',
'OWEXGB00000122229DE000645467',
'OWEXGB00000122251AU000021537',
'OWEXGB00000122253AU000022237',
'OWEXGB00000122255AU000022237',
'OWEXGB00000122257AU000040077',
'OWEXGB00000122259AU000022107',
'OWEXGB00000122261AU000031507',
'OWEXGB00000122263AU000022237',
'OWEXGB00000122265AU000020197',
'OWEXGB00000122267AU000022057',
'OWEXGB00000122269AU000031757',
'OWEXGB00000122271DE000107857',
'OWEXGB00000122273DE000521347',
'OWEXGB00000122275DE000107857',
'OWEXGB00000122277DE000645467',
'OWEXGB00000122279DE000657797',
'OWEXGB00000122281DE000645467',
'OWEXGB00000122283DE000521347');
$consignmentFilter = new ConsignmentFilter();
            $consignmentFilter->addJoin("parcel p", "c.id = p.consignment_id");
            $consignmentFilter->addFilterNew("    p.tracking_number in ('".implode("','", $parcelTrackingArr)."')" );
            $consignmentList = $consignmentFilter->getListNew("c.id, p.tracking_number, c.awb, c.hawb, c.weight, c.number_pieces, c.user_id, c.service_id, c.description, c.company, c.contact,c.address_line_1, c.address_line_2, c.address_line_3, c.city, c.country_id, c.postcode, c.value, c.currency");
            $ManifestSummaryReport = new ManifestBrief();
$response = $ManifestSummaryReport->AddHTML($consignmentList, 11979);


print_r($response);
exit;


$bagging = new Bagging(12375);
$ups = new UPS();
$response = $ups->bagLabel(array('PBFF2755525001'), $bagging);
print_r($response);
exit;

//$ptwo = new ptwo();
//$response = $ptwo->label($consignment);
//print_r($response);
//exit;
//$service = new services($consignment->getServiceId());
//$country = new Country($consignment->getCountryId());

        
//echo "<pre>";
//print_r($consignment);
//
/*$page_size = array(100, 100);
$pdf = new PdfBase(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false, true);
$pitney = new Commercialinvoice($pdf,$page_size);
$response =  $pitney->getCommercialinvoice($consignment,'9452836492');

$pdf->Output($consignment->getId() . ".pdf");
echo  $consignment->getId() . ".pdf";
print_r($response);
exit;*/
/*
$skypostal = new Skypostal();
$response = $skypostal->apiLabel($consignment);
print_r($response);
exit;
*/
/*
$dhlclass = new DHL();
$response = $dhlclass->reschedulePickup($consignment, "2020-07-10", "11:00", "17:00");
print_r($response);
exit;*/










$trackingNumber = array('0311164668082112',
'0359288668043413',
'0300764668090717',
'0346471668231218',
'0394271668226613',
'1168189668042710',
'0376472668565717',
'1102535668431711',
'1158792668700912',
'0302788668033114',
'0316437668042611',
'1115093668426914',
'0374072668567715',
'1120314668358312',
'1165194668063613',
'0333337668041416',
'0389937668041615',
'1109792668701616',
'0381087668297913',
'0382337668041719',
'1100933668377312',
'1106935668431814',
'0311164668090810',
'1101990668616215',
'1174790668615111',
'1101578668417416',
'0302388668039910',
'0333137668040910',
'1132933668385514',
'1139889668046517',
'1163935668442211',
'0347487668294213',
'1192290668615617',
'0311164668089012',
'1100183668195410',
'1159992668701318',
'1133333668377813',
'0356985668112417',
'0333137668041511',
'0370875668450018',
'0385637668042912',
'1102578668416715',
'1121889668042619',
'1801182668800616',
'1842482668803110',
'8716188668369513',
'1889845667938213',
'8733273668268816',
'8734572668294610',
'1801637667961313',
'8701270668278518',
'8701370668281515',
'8724675667858913',
'1801478669347213',
'1800878669362314',
'1854977668373213',
'1865782668802719',
'1838682668799513',
'8701070668281415',
'1831083668351713',
'8720980668992110',
'1800082668801716',
'1843883668351813',
'1800782668800916',
'1851178669349112',
'8726673668264618',
'8744579668498915',
'1803382668803411',
'1823776668476111',
'8780980668997414',
'1838782668803910',
'1851610667928017',
'1802482668803518',
'1809784667631615',
'1865078669348319',
'8727295667775019',
'1822774668562613',
'1865078669349811',
'8706595667775116',
'8745773668262517',
'8747997668284319',
'1800082668801815',
'0302788668034517',
'1843682668808118',
'1850610667928612',
'1854177668383117',
'1882577668379913',
'8734497668284013');

$hermes = new Hermes();
//$hermes->presortFile($trackingNumber);
//$response = $hermes->sendData($trackingNumber);
$response = $hermes->getBagLabel(array("T01NLA0000002316"));
print_r($response);
//
exit;
$consignmentfilter = new ConsignmentFilter();
$consignmentfilter->addFilterNew(" awb in ('RW610509125CW  ',
'RW610509134CW',
'RW610509148CW',
'RW610509151CW',
'RW610509165CW',
'RW610509179CW',
'RW610509182CW',
'RW610509196CW',
'RW610509205CW',
'RW610509219CW')");
$clist = $consignmentfilter->getListNew();

$cposips = new CpostIps();
//$response = $cposips->authenticateionToken();

//$response =  $cposips->closeBag("USMIAZCOBOGCAUZ00003", "73367875-9293-EA11-814C-0050563F4578");
//$response = $cposips->removeBag("USMIAZCOBOGCAUZ00003", "E50086A5-8F93-EA11-814C-0050563F4578");
//$response = $cposips->reOpenDispatchItem("USMIAZCOBOGCAUZ00002");

//$response = $cposips->addMailItem($clist);

$response = $cposips->closeDispatch("GBHMIDCOBOGCAUZ00002");
//$response = $cposips->createDispatch();
print_r($response);
exit;

$consignment = new Consignment(70703);
$ctt = new ptwo();
$response = $ctt->sendData(array('0199999200000015','0199999200000091','0199999200000084','0199999200000077','0199999200000060'));
print_r($response);
exit;

// no shortest distance found, yet
  $shortest = -1;
$postcode = "72419";
$ptworoutine = new pTwoRoutineFilter();
$ptworoutine->addFieldFilter("postcode", $postcode);
$postcodeList = $ptworoutine->getList();
if(count($postcodeList) > 0){
    $cityArray = array();
    $streetArray = array();
    $city = "freudnveiler";
    foreach($postcodeList as $postcode){
        $cityArray[$postcode->getId()] = $postcode->getCity();
        $streetArray[$postcode->getCity()][$postcode->getId()] = $postcode->getStreet();
    }
    $closestcity = closest($city, $cityArray, $shortest, 3);
    
    echo $closestcity . "<br />";
    
    $searchStreet = $streetArray[$closestcity];
    $addressLine1 = "Im Schlbwinkel 5";
    $AddressSplit = GenericFunctions::getDoorNumber($addressLine1);
    $number = $AddressSplit['number'];
    if($number <= 0){
        echo "please enter door number in address Line 1.";
        exit;
    }
    $steet1 = $AddressSplit['street'];
    $closestStreet = closest($steet1, $searchStreet, $shortest, 4);
    if($closestStreet != ""){
        foreach($searchStreet as $key => $value){
            if($value == $closestStreet){
                echo $routineId = $key;
                $ptwofilter = new pTwoRoutineFilter();
                $routineRecord = $ptwofilter->checkStreetNumber($number, $routineId);
                if(count($routineRecord) > 0){
                   return $routineRecord[0]->getSortInfo(); 
                }
                
            }
        }
    }
    exit;
}   
function closest($input,$words,&$shortest, $sensitivity){
    
    
        // loop through words to find the closest
    foreach ($words as $word) {

        // calculate the distance between the input word,
        // and the current word
        $lev = levenshtein($input, $word);

        // check for an exact match
        if ($lev == 0) {

            // closest word is this one (exact match)
            $closest = $word;
            $shortest = 0;

            // break out of the loop; we've found an exact match
            break;
        }
        
        // if this distance is less than the next found shortest
        // distance, OR if a next shortest word has not yet been found
        if ($lev <= $shortest || $shortest < 0) {
            // set the closest match, and shortest distance
            $closest  = $word;
            $shortest = $lev;
        }


    }
     if($shortest <= $sensitivity)
     {
        return $closest;
    } else {
        return 0;
    }
    
    
    }
   
exit;


$consignment = new Consignment(70703);
$ctt = new parcelForYou();
$response = $ctt->ediLabel($consignment);
print_r($response);
exit;


$kexpress = new CoolRunner();
$response = $kexpress->label($consignment);
//$response = $kexpress->sendData(array("0079170079172147483647001"));
print_r($response);
exit;
//$ln = $kexpress->getServices();
print_r($ln);
exit;

 


$host = 'staging.smarttrack.co';
if($socket =@ fsockopen($host, 80, $errno, $errstr, 30)) {
echo 'online!';
fclose($socket);
} else {
echo 'offline.';
}
exit;
mail("mruga@oneworldexpress.com","TEST MAIL", "TEST MAIL");
exit;

$parcelId = array(137892,
137974,
137975,
137976,
137977,
137978,
137979,
137980,
137981,
137982,
137985);

foreach($parcelId as $parcel){
    $parcellist = new Parcel($parcel);
    $trackingNumber = $parcellist->getTrackingNumber();
    $trackingDataFilter = new TrackingDataFilter();
    $trackingDataFilter->addFilter("tracking_number = '".$trackingNumber."'");
    $trackingList= $trackingDataFilter->getColumnList("id, date_created", " id desc", 1);
    if(count($trackingList) > 0){
        $lasttrackpointDate = $trackingList[0]->getDateCreated();
        $DateTime = date("Y-m-d H:i:s",strtotime('-2 hours',  strtotime($lasttrackpointDate)));
        $DateTime = date("Y-m-d H:i:s", strtotime($DateTime. ' 10 days'));
    }
    $ServiceAreaDescription = "Delivered";
    $spTrackingStatus = 121;
    $EventCode = "";
    $EventDescription= "Delivered";
    $Signatory = "";
            
    $trackingData = [
            'user_id' => 0,
            'entity_id' => $parcel,
            'entity_type' => 'parcel',
            'tracking_number' => $trackingNumber,
            'track_point' => $ServiceAreaDescription,
            'date_created' => $DateTime,
            'ip_address' => getClientIp(),
            'status_code_id' => $spTrackingStatus,
            'carrier_code' => $EventCode,
            'carrier_desc' => $EventDescription,
            'signatory' => $Signatory
        ];
        $trackingDataObj = new TrackingData($trackingData);
        print_r($trackingData);
        
        $trackingDataObj->save();
    
    
}
die;


$manifestFile = "C:/xampp/htdocs/smarttrackoptimization/_assets/manifest/pdf/2019_11_05/1572949648.pdf";
$b64Doc = chunk_split(base64_encode(file_get_contents($manifestFile)));
echo $b64Doc;
exit;

//$curl = curl_init();
//
//curl_setopt_array($curl, array(
//  CURLOPT_URL => "https://www.zasilkovna.cz/api/rest",
//  CURLOPT_RETURNTRANSFER => true,
//  CURLOPT_ENCODING => "",
//  CURLOPT_MAXREDIRS => 10,
//  CURLOPT_TIMEOUT => 30,
//  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
//  CURLOPT_CUSTOMREQUEST => "POST",
//  CURLOPT_POSTFIELDS => "<createPacket>"
//                            . "<apiPassword>69f170ac506c6ab79487170a5593f413</apiPassword>"
//                            . "<packetAttributes>"
//                                . "<number>123456</number>"
//                                . "<name>Petr</name>"
//                                . "<surname></surname>"
//                                . "<email>petr@novak.cz</email>"
//                                . "<addressId>85</addressId>"
//                                . "<value>145.55</value>"
//                                . "<eshop>muj-eshop.cz</eshop>"
//                            . "</packetAttributes>"
//                        . "</createPacket>",
//  CURLOPT_HTTPHEADER => array(
//    "Accept: */*",
//    "Cache-Control: no-cache",
//    "Connection: keep-alive",
//    "Content-Type: application/xml",
//    "Host: www.zasilkovna.cz",
//    "cache-control: no-cache",
//    "content-length: 381"
//  ),
//));
//
//$response = curl_exec($curl);
//$err = curl_error($curl);
//
//curl_close($curl);
//
//if ($err) {
//  echo "cURL Error #:" . $err;
//} else {
//  $xmltoArray = simplexml_load_string($response) ;
//  $jsonarray = json_encode($xmltoArray);
//  $responsearray = json_decode($jsonarray);
//  print_r($responsearray);
//  if(strtolower($responsearray->status) == "ok")
//  {
//      $barcode = $responsearray->result->barcode;
//  }
//  else
//  {
//        $errormessage = "";
//        $output["STATUS"] = "ERROR";
//        $errors = $responsearray->detail->attributes->fault;
//        foreach ($errors as $e)
//        {
//            $errormessage .= $e->fault . "<br />";
//        }
//  }
//  echo $errormessage;
//}
//exit;


$consignment = new ConsignmentFilter();
$consignment->addFilter("awb in ('JV790939478GB',
'JV790939481GB',
'JV790939552GB',
'JV790939535GB',
'JV790939504GB',
'JV790939495GB',
'JV790939521GB',
'JV790939518GB',
'JV790939566GB',
'JV790939549GB',
'JV790939570GB')");
$consignmentlist = $consignment->getColumnList("*");

$royal = new RoyalMail();      
$royal->manifest($consignmentlist);  
    

die;

$ctt = new parcelForYou();
$response = $ctt->label($consignment);
print_r($response);
exit;
$postitaliane = new yodel();
$response = $postitaliane->label($consignment,'zpl');
print_r($response);
exit;
$outputfilename = "C:/xampp/htdocs/smarttrackoptimization/_assets/pdf/2019_04_29/104604.pdf";
//$outfile = "C:/xampp/htdocs/smarttrackoptimization/_assets/pdf/2019_04_29/mruga.pdf";
$PDFMerger = new PDFMerger();
                        $PDFMerger->addPDF($outputfile);
                        try {
                            $PDFMerger->mergeHuxloe('file', $outputfilename, '', $consignment);
                        } catch (Exception $e) {
                            echo 'Caught exception: ', $e->getMessage(), "\n";
                        }
exit;
$consignment = new Consignment(105994);
//$parcel[] = "RS150049494DE";
$asendiaLabel = new ups();
$result = $asendiaLabel->label($consignment, $labelType, $size);
//$result = $asendiaLabel->sendData($parcel);
print_r($result);
exit;
        
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




