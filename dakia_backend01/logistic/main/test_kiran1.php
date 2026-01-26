<?php

require_once("../includes/settings/config.inc.php");
require_once("AuthentificationType.php");

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

  $filename = 'https://cig.dhl.de/gkvlabel/SANDBOX/dhl-vls/gw/shpmntws/printShipment?token=x5xzrHE7ctmqPqk33k%2BKkBwbvIfYP4elMQsBFM%2BJOdiT2bmoaXXzris%2Ftz9jBtdVFLY5cCENit0Jnd9aXuxoNFlOy4BlgiaqcqKudbXGxg6P91b7fjqJIw7jxPsiLcNB';
    file_put_contents('test.pdf', file_get_contents($filename));
    die;
     try {
    	$IntraShip = new SoapClient("https://cig.dhl.de/cig-wsdls/com/dpdhl/wsdl/geschaeftskundenversand-api/3.1/geschaeftskundenversand-api-3.1.wsdl",
        array('login' => 'testAppOwe_1',
            'password' => 'ZF8DTMgTPVYrY8agG8Cx0Ntt8p22y7',
            'location' => 'https://cig.dhl.de/services/production/soap',
            'soap_version' => SOAP_1_1,
                'exceptions' => false,
                'trace' => 1));
            } catch (Exception $e) {
            return "ERROR||DHL DE is not working, So authentication token is not able to generate. Please contact to itsupport@oneworldexpress.com ".$e;
    }
	$conArray = ['98179','99561','99580'];
        
        foreach($conArray as $conId)
        {
            
        $consignment = new Consignment($conId);
        $parcelFilter = new ParcelFilter();
        $parcelFilter->addFieldFilter("consignment_id",$conId);
        $parcelResult = $parcelFilter->getList();
        
        $devAuthHeader = null;
        $auth = new stdClass();
        $auth->user = 'itsupportoneworld';
        $auth->signature = 'Cybernet123$';
        $auth->ekp = '62953184106201';
        //$auth->action = 'createShipmentOrder';
        
        $authHeader = new SoapHeader('http://dhl.de/webservice/cisbase', 'Authentification', $auth);
        $IntraShip->__setSoapHeaders($authHeader);
	
        $requestArray = array(
            'CreateShipmentOrderRequest' => "1",
                'Version' => array(
                    'majorRelease' => '3',
                    'minorRelease' => '1'),
                'ShipmentOrder' => array(
                        'sequenceNumber' => '',
                        'Shipment' => array(
                            'ShipmentDetails' => array(
                                'product' => 'V62WP',
                                'accountNumber' => '62953184106201',
                                'customerReference' => $consignment->getHawb(),
                                'shipmentDate' => date('Y-m-d'),
                                'costCentre' => '',
                                'ShipmentItem' =>array(
                                    'weightInKG' => $parcelResult[0]->getWeight()/100,
                                    'lengthInCM' => $parcelResult[0]->getLength(),
                                    'widthInCM' =>  $parcelResult[0]->getWidth(),
                                    'heightInCM' => $parcelResult[0]->getHeight(),
                                ),
                                'Service' => '',
                                'Notification' => array(
                                    'recipientEmailAddress' => 'itsupport@oneworldexpress.com',
                                ),
                            ),
                            'Shipper' => array(
                                'Name' => array(
                                    'name1' => 'Oneworld',
                                ),
                                'Address' => array(
                                    'streetName' => 'Vegesacker Heerstr.',
                                    'streetNumber' => '111',
                                    'zip' => '28757',
                                    'city' => 'Bremen',
                                    'Origin' => array(
                                        'country' => 'Germnay',
                                        'countryISOCode' => 'DE'
                                    ),
                                ),
                            ),
                            'Receiver' =>array(
                                'name1' => $consignment->getContact(),
                                'Address' => array(
                                    'name2' => $consignment->getCompany(),
                                    'name3' => '',
                                    'streetName' => $consignment->getAddressLine2(),
                                    'streetNumber' => $consignment->getAddressLine1(),
                                    'zip' => $consignment->getPostCode(),
                                    'city' => $consignment->getCity(),
                                    'Origin' => array(
                                        'country' => '',
                                        'countryISOCode' => 'DE'
                                    ),
                                ),
                                'Communication' => array( 
                                    'phone' => $consignment->getTelephone(), 
                                    'email' => $consignment->getEmail(), 
                                    'contactPerson' => $consignment->getContact(), 
                                ),
                            ),
                        ),
                        'PrintOnlyIfCodeable active' => '1',
                    ),
                    'labelResponseType' => 'URL',
                
        );
       //  $IntraShip->__setSoapHeaders($authHeader);
        try {
            $shResponse = $IntraShip->__soapCall(createShipmentOrder, array($requestArray));
            print_r($shResponse);
           die;
        }  catch(SoapFault $exception) {
        } die;
        }
       // echo "Fault Code: {$exception->getMessage()}";}
       // echo '<pre>';
       // echo "REQUEST:\n" . $IntraShip->__getLastRequest() . "\n";
       // echo '<pre>';
        //echo "REQUEST:\n" . $IntraShip->__getLastResponse() . "\n";
//die;

////const WS_URL = 'geschaeftskundenversand-api-3.1.wsdl';
////const WS_TOKEN = '887E99B5F89BB18BEA12B204B620D236';
////const WS_KEY = 'wr5qjqh4gj';
//
//error_reporting(1);
//ini_set("display_errors", true);
////require_once('../includes/3rdparty/nusoap/nusoap.php');
//
//$proxyhost = '';
//$proxyport = '';
////$client = new SoapClient(WS_URL, array("location" => WS_URL, "url" => 'https://cig.dhl.de/services/production/soap'));
////print_r($client); die;
////$client->decode_utf8 = 0;
////$client->soap_defencoding = 'UTF-8';
////$client->setCredentials('testAppOwe_1','ZF8DTMgTPVYrY8agG8Cx0Ntt8p22y7','basic');
//$apiauth =array('UserName'=>'testAppOwe_1','Password'=>'ZF8DTMgTPVYrY8agG8Cx0Ntt8p22y7','url' => 'https://cig.dhl.de/services/production/soap');
//
////$wsdl = 'http://sitename.com/service.asmx?WSDL';
////$header = SoapHeader(array('user' => 'itsupportoneworld', 'signature' => 'Cybernet123$'));
//
//$UserId = new SoapHeader("UserId", 'itsupportoneworld');
//$Password = new SoapHeader("Password", 'Cybernet123$');
//$wsAction = new SoapHeader('Action', 'CreateShipmentOrder');
////$wsTo = new SoapHeader($WSXSD, 'To', $URLWS);
//            
//$client = new SoapClient(WS_URL, $apiauth); 
////$soap->__setSoapHeaders($header);   
//$client->__setSoapHeaders(array($UserId, $Password, $wsAction));
//
////$data = $soap->methodname($header);   
////print_r($soap); 
////die;
//$USERID = 'itsupportoneworld';
//$USERPWD = 'Cybernet123$';
//
////$client->setHeaders('<cis:Authentification> 
////         <cis:user>itsupportoneworld</cis:user> 
////         <cis:signature>Cybernet123$</cis:signature> 
////      </cis:Authentification>');
////print_r($client); die;
//
//if ($client->fault) {
//    echo '<h2>Fault</h2>';
//    echo 'safdasdf';
//} else {
//    //$err = $client->getError();
//    if ($err) {
//        echo '<h2>Constructor error</h2><pre>' . $err . '</pre>';
//    }else{
//        $header = '';
//        $requestArray = array(
//            'CreateShipmentOrderRequest' => array(
//                'version' => array(
//                    'majorRelease' => '3',
//                    'minorRelease' => '1'),
//                'ShipmentOrder' => array(
//                        'sequenceNumber' => '',
//                        'Shipment' => array(
//                            'ShipmentDetails' => array(
//                                'Product' => 'V62WP',
//                                'accountNumber' => '62953184106201',
//                                'customerReference' => '12345',
//                                'shipmentDate' => date('Y-m-d'),
//                                'costCentre' => '',
//                                'ShipmentItem' =>array(
//                                    'weightInKG' => '1',
//                                    'lengthInCM' => '25',
//                                    'widthInCM' => '15',
//                                    'heightInCM' => '1',
//                                ),
//                                'Service' => '',
//                                'Notification' => array(
//                                    'recipientEmailAddress' => 'empfaenger@test.de',
//                                ),
//                            ),
//                            'Shipper' => array(
//                                'Name' => array(
//                                    'name1' => 'test1',
//                                ),
//                                'Address' => array(
//                                    'streetName' => 'stephenson Wharf',
//                                    'streetNumber' => '19',
//                                    'zip' => '28757',
//                                    'city' => 'Bremen',
//                                    'origin' => array(
//                                        'country' => '',
//                                        'countryISOCode' => 'DE'
//                                    ),
//                                ),
//                            ),
//                            'Receiver' =>array(
//                                'name' => 'test',
//                                'Address' => array(
//                                    'name2' => 'name2',
//                                    'name3' => 'name3',
//                                    'StreetName' => 'test',
//                                    'streetNumber' => '19',
//                                    'zip' => '28195',
//                                    'city' => 'Bremen',
//                                    'origin' => array(
//                                        'country' => '',
//                                        'countryISOCode' => 'DE'
//                                    ),
//                                ),
//                                'Communication' => array( 
//                                    'phone' => '00447939528622', 
//                                    'email' => 'empfaenger@test.de', 
//                                    'contactPerson' => 'Kontaktperson', 
//                                ),
//                            ),
//                        ),
//                        'PrintOnlyIfCodeable active' => '1',
//                    ),
//                    'labelResponseType' => 'URL',
//                ),
//        );
//            $getPackageResponse = $client->CreateShipmentOrder(array("params" => '<ns:CreateShipmentOrderRequest> 
//         <ns:Version> 
//            <majorRelease>3</majorRelease> 
//            <minorRelease>1</minorRelease> 
//         </ns:Version> 
//         <ShipmentOrder> 
//            <sequenceNumber></sequenceNumber> 
//            <Shipment> 
//               <ShipmentDetails> 
//                  <product>V62WP</product> 
//                  <cis:accountNumber>${#Project#testAccountNumberV62WP}</cis:accountNumber> 
//                  <customerReference>Ref. 123456</customerReference> 
//                  <shipmentDate>${#Project#testDate}</shipmentDate> 
//                  <costCentre></costCentre> 
//                  <ShipmentItem> 
//                     <weightInKG>1</weightInKG> 
//                     <lengthInCM>25</lengthInCM> 
//                     <widthInCM>15</widthInCM> 
//                     <heightInCM>1</heightInCM> 
//                  </ShipmentItem> 
//                  <Service> 
//                  </Service> 
//                  <Notification> 
//                     <recipientEmailAddress>empfaenger@test.de</recipientEmailAddress> 
//                  </Notification> 
//               </ShipmentDetails> 
//               <Shipper> 
//                  <Name> 
//                     <cis:name1>Absender Zeile 1</cis:name1> 
//                     <cis:name2>Absender Zeile 2</cis:name2> 
//                     <cis:name3>Absender Zeile 3</cis:name3> 
//                  </Name> 
//                  <Address> 
//                     <cis:streetName>Vegesacker Heerstr.</cis:streetName> 
//                     <cis:streetNumber>111</cis:streetNumber> 
//                     <cis:zip>28757</cis:zip> 
//                     <cis:city>Bremen</cis:city> 
//                     <cis:Origin> 
//                        <cis:country></cis:country> 
//                        <cis:countryISOCode>DE</cis:countryISOCode> 
//                     </cis:Origin> 
//                  </Address> 
//                  <Communication> 
//                     <!--Optional:--> 
//                     <cis:phone>+49421987654321</cis:phone> 
//                     <cis:email>absender@test.de</cis:email> 
//                     <!--Optional:--> 
//                     <cis:contactPerson>Kontaktperson Absender</cis:contactPerson> 
//                  </Communication> 
//               </Shipper> 
//               <Receiver> 
//                  <cis:name1>Empfänger Zeile 1</cis:name1> 
//                  <Address> 
//                     <cis:name2>Empfänger Zeile 2</cis:name2> 
//                     <cis:name3>Empfänger Zeile 3</cis:name3> 
//                     <cis:streetName>An der Weide</cis:streetName> 
//                     <cis:streetNumber>50a</cis:streetNumber> 
//                     <cis:zip>28195</cis:zip> 
//                     <cis:city>Bremen</cis:city> 
//                     <cis:Origin> 
//                        <cis:country></cis:country> 
//                        <cis:countryISOCode>DE</cis:countryISOCode> 
//                     </cis:Origin> 
//                  </Address> 
//                  <Communication> 
//                     <cis:phone>+49421123456789</cis:phone> 
//                     <cis:email>empfaenger@test.de</cis:email> 
//                     <cis:contactPerson>Kontaktperson Empfänger</cis:contactPerson> 
//                  </Communication> 
//               </Receiver> 
//            </Shipment> 
//            <PrintOnlyIfCodeable active="1"/> 
//         </ShipmentOrder> 
//         <labelResponseType>URL</labelResponseType> 
//         <groupProfileName></groupProfileName> 
//         <labelFormat></labelFormat> 
//         <labelFormatRetoure></labelFormatRetoure> 
//         <combinedPrinting>0</combinedPrinting> 
//      </ns:CreateShipmentOrderRequest>'));
//          echo "Request:<pre>
//".htmlspecialchars($client->request, ENT_QUOTES)."</pre>
//";
//echo "Response:<pre>
//".htmlspecialchars($client->response, ENT_QUOTES)."</pre>
//";
//}}
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
///*
//
//
//
//
//    try{
//    $ENDPOINT = 'geschaeftskundenversand-api-3.1.wsdl';
//    $params = ["soap_version" => SOAP_1_2, "trace" => 1, "exceptions" => 1];
//    $SOAP = new SoapClient($ENDPOINT, $params);
//    $WSNAMESPACE = 'https://cig.dhl.de/services/sandbox/soap';
//    $USERID = 'itsupportoneworld';
//    $USERPWD = 'Cybernet123$';
//    $UserId = new SoapHeader($WSNAMESPACE, "UserId", $USERID);
//    $Password = new SoapHeader($WSNAMESPACE, "Signature", $USERPWD);
//    $wsAction = new SoapHeader($WSNAMESPACE, 'Action', 'CreateShipmentOrder');
//    $SOAP->__setSoapHeaders(array($UserId, $Password, $wsAction));
//    $requestArray[] = '';
//    
//    
//    $requestArray = array(
//            'CreateShipmentOrderRequest' => array(
//                'version' => array(
//                    'majorRelease' => '3',
//                    'minorRelease' => '1'),
//                'ShipmentOrder' => array(
//                        'sequenceNumber' => '',
//                        'Shipment' => array(
//                            'ShipmentDetails' => array(
//                                'Product' => 'V62WP',
//                                'accountNumber' => '62953184106201',
//                                'customerReference' => '12345',
//                                'shipmentDate' => date('Y-m-d'),
//                                'costCentre' => '',
//                                'ShipmentItem' =>array(
//                                    'weightInKG' => '1',
//                                    'lengthInCM' => '25',
//                                    'widthInCM' => '15',
//                                    'heightInCM' => '1',
//                                ),
//                                'Service' => '',
//                                'Notification' => array(
//                                    'recipientEmailAddress' => 'empfaenger@test.de',
//                                ),
//                            ),
//                            'Shipper' => array(
//                                'Name' => array(
//                                    'name1' => 'test1',
//                                ),
//                                'Address' => array(
//                                    'streetName' => 'stephenson Wharf',
//                                    'streetNumber' => '19',
//                                    'zip' => '28757',
//                                    'city' => 'Bremen',
//                                    'origin' => array(
//                                        'country' => '',
//                                        'countryISOCode' => 'DE'
//                                    ),
//                                ),
//                            ),
//                            'Receiver' =>array(
//                                'name' => 'test',
//                                'Address' => array(
//                                    'name2' => 'name2',
//                                    'name3' => 'name3',
//                                    'StreetName' => 'test',
//                                    'streetNumber' => '19',
//                                    'zip' => '28195',
//                                    'city' => 'Bremen',
//                                    'origin' => array(
//                                        'country' => '',
//                                        'countryISOCode' => 'DE'
//                                    ),
//                                ),
//                                'Communication' => array( 
//                                    'phone' => '00447939528622', 
//                                    'email' => 'empfaenger@test.de', 
//                                    'contactPerson' => 'Kontaktperson Empfänger', 
//                                ),
//                            ),
//                        ),
//                        'PrintOnlyIfCodeable active' => '1',
//                    ),
//                    'labelResponseType' => 'URL',
//                ),
//        );
//        $retval = $SOAP->CreateShipmentOrder($requestArray);
//        print_r($retval);
//    }
//    catch (Exception $e) {
//        echo $e->getMessage();
//    }
    