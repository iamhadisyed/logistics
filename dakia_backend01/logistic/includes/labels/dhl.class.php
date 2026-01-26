<?php
include_classes([
    'convertxml2array.class',
    ], 'library');
include_classes([
    'pdfmerger','dhltrackingstatus.class',
    ], 'labels');
include_classes([
    'tcpdf',
    ], '3rdparty/tcpdf');
include_classes([
    'fpdi',
    ], '3rdparty/fpdi');
include_classes([
    'serviceconstantvalue.class',
    'serviceconstantvaluefilter.class'
    ]);
class DHL implements CarrierService {

    private $pdf;
    private $serviceValues = null;
    private $agentValues = null;
    private $constants = null;
    private $country = null;
    private $user = null;
    private $bookingRef = null;
    private $collectionDate = null;
    private $reschedule = false;

    public function __construct() {
        
    }

    public function validation(Consignment $consignment, Services $service, Country $country) {
        
        $returnOutput = array();
        /*
         *  Get Service  constants 
         */
        $serviceAgentConstantFilter = new ServiceConstantValueFilter();
        $serviceAgentConstantFilter->addFilter("service_id = '" . $consignment->getServiceId() . "' AND agent_id = '" . $consignment->getAgentId() . "' ");
        $serviceAgentConstant = $serviceAgentConstantFilter->getConstantList("c.constant 'constant_name', scv.constant_value 'constant_value'");
        if (count($serviceAgentConstant) > 0) {
            foreach ($serviceAgentConstant as $serviceAgentConstantData) {
                $this->constants[$serviceAgentConstantData->getConstantName()] = $serviceAgentConstantData->getConstantValue();
            }
        }

        $postcodeValidation = $this->IsValidDHLPostCode($consignment, $service, $country);
        switch ($postcodeValidation) {
            case 'allowed':
                break;
            case 'not-allowed':
                $returnOutput[] = "Unable to provide service at this postcode.";
                break;
            case 'blocked':
                $returnOutput[] = "Your postcode is a remote area. Please contact to administrator to activate.";
                break;
            case "custommessage":
                $returnOutput[] = $consignment->getMessage();
                break;
        }

       /* if ($consignment->getShipmentType() == "C") {
            $collectionTimeValidation = $this->getCapibilityRequest($consignment, $service, $country);
            if ($collectionTimeValidation["STATUS"] == "ERROR") {
                $returnOutput[] = $collectionTimeValidation["MESSAGE"];
            }
       }*/
	   // 199 ID Is for spain. and here we are changeing spain country to canary Islan ig if postcode starts with 35 and 38 and country is spain 
	   if(
			$consignment->getCountryId() == 199 && 
			trim($consignment->getPostCode()) != '' && 
			trim($consignment->getPostCode()) != '-' && 
			in_array(substr(trim($consignment->getPostCode()),0,2) , array('35', '38'))
		){
			$consignment->setCountryId(262);
		}
		return $returnOutput;
    }

    public function IsValidDHLPostCode($consignment, $service, $country) {
        /*
         * REMOTE AREA POSTCODE CHECK
         */
        $handling = $service->getCode();
        $account = $consignment->getAccount();
        $postCode = $consignment->getPostcode();
        $countryIso = $country->getIso();
        $city = $consignment->getCity();
        $userId = $consignment->getUserId();
        $serviceId = $service->getId();
        $countryId = $consignment->getCountryId();

        if($countryIso == 'PT')
        {
            if(strlen($postCode)==7){
                $postCode = substr($postCode, 0,4)."-".substr($postCode, 4,3);
                $consignment->setPostcode($postCode);
            }
        } elseif(in_array($countryIso, array( 'GR', 'CZ')))
        {
            if(strlen($postCode)==5){
                $postCode = substr($postCode, 0,3)." ".substr($postCode, 3,2);
                $consignment->setPostcode($postCode);
            }
        } elseif($countryIso == 'NL')
        {
            if(strlen($postCode)==6){
                $postCode = substr($postCode, 0,4)." ".substr($postCode, 4,2);
                $consignment->setPostcode($postCode);
            }
        } 
        
        if (trim($city) == '-' && $postCode == '-')
            return "not-allowed";

        $url = "http://xmlpi-ea.dhl.com/XMLShippingServlet";

        $httpPostObj = new HttpCommunication($url);

        $messageTime = Date("Y-m-d") . "T" . Date("H:i:s-01:00");

        $requestXml = '<?xml version="1.0" encoding="UTF-8"?>
                        <req:DCTRequest xmlns:req="http://www.dhl.com" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
                        <GetQuote>
                              <Request>
                                      <ServiceHeader>
                                              <MessageTime>' . $messageTime . '</MessageTime>
                                              <SiteID>TransglobeUK</SiteID>
                                              <Password>pKbVr8sQ3</Password>
                                      </ServiceHeader>
                              </Request>
                              <From>
                                      <CountryCode>GB</CountryCode>
                                      <Postalcode>UB3 3NB</Postalcode>
                                      <City>LONDON</City>
                                      <Suburb>Dorset</Suburb>
                              </From>
                              <BkgDetails>
                                      <PaymentCountryCode>GB</PaymentCountryCode>
                                      <Date>' . date("Y-m-d") . '</Date>
                                      <ReadyTime>PT09H00M</ReadyTime>
                                      <DimensionUnit>CM</DimensionUnit>
                                      <WeightUnit>KG</WeightUnit>
                                      <NumberOfPieces>1</NumberOfPieces>
                                      <ShipmentWeight>2.5</ShipmentWeight>
                                      <Pieces>
                                              <Piece>
                                                      <PieceID>1</PieceID>
                                                      <PackageTypeCode>BOX</PackageTypeCode>
                                                      <Height>1.4</Height>
                                                      <Depth>5.5</Depth>
                                                      <Width>3.1</Width>
                                                      <Weight>2.5</Weight>
                                              </Piece>
                                      </Pieces>
                                      <PaymentAccountNumber>181080000</PaymentAccountNumber>
                                      <IsDutiable>N</IsDutiable>
                              </BkgDetails>
                              <To>
                                                      <CountryCode>' . $countryIso . '</CountryCode>
                                                      <Postalcode>' . $postCode . '</Postalcode>
                                                      <City>' . $city . '</City>
                                                      <Suburb></Suburb>
                                              </To>
                                      </GetQuote>
                              </req:DCTRequest>';

        if ($httpPostObj->post($requestXml)) {


            $responseXml = $httpPostObj->getResponse();

            $array = ConvertXMLToArray::xml2array($responseXml);

            $consignment->setApiData(print_r($requestXml, true), print_r($array, true), "DHL_REMOTEAREA_CHECK");

            $conditionCode = $array['res:DCTResponse']['GetQuoteResponse']['Note']['Condition']['ConditionCode'];

            if ($conditionCode == '3024' || $conditionCode == '3021') {
                $conditonCode = $array['res:DCTResponse']['GetQuoteResponse']['Note']['Condition']['ConditionCode'];
                $conditonMessage = $array['res:DCTResponse']['GetQuoteResponse']['Note']['Condition']['ConditionData'];
                $consignment->setMessage($conditonMessage);
                $consignment->setStatus(Consignment::STATUS_INVALID);


                return "custommessage";
            } else {
                $result1 = $array['res:DCTResponse']['GetQuoteResponse']['BkgDetails']['QtdShp'][0]['QtdShpExChrg'][0]['GlobalServiceName'];
                $result2 = $array['res:DCTResponse']['GetQuoteResponse']['BkgDetails']['QtdShp'][1]['QtdShpExChrg'][0]['GlobalServiceName'];

                if ($result1 == 'REMOTE AREA DELIVERY' || $result2 == 'REMOTE AREA DELIVERY') {
                    $sql = "SELECT 
                        count(*) 'id'
                    FROM
                        user_services_routing
                    WHERE 
                        user_account_id in ( SELECT user_account_id FROM user WHERE id = '" . $userId . "'  )"
                            . "AND service_id = '" . $serviceId . "' and country_id = '" . $countryId . "' and is_remotearea = 1";

                    $remoteareasUserCheck = UserServicesRouting::getPartnerServicesListFromSql($sql);

                    if (count($remoteareasUserCheck) > 0 && $remoteareasUserCheck[0]->getId() > 0) {
                        $consignment->setRemoteCharges('1');

                        return "allowed";
                    } else {
                        $consignment->setRemoteCharges('1');
                        $consignment->setMessage("Your postcode is a remote area. Please contact to administrator to activate.");
                        $consignment->setConsignmentStatus(Consignment::$database_status_array[Consignment::STATUS_INVALID]);
                        $consignment->setShipmentStatus(Consignment::STATUS_INVALID);

                        return "blocked";
                    }
                } else {
                    $consignment->setRemoteCharges('0');

                    return "allowed";
                }
            }
        }
    }

    private function getCapibilityRequest($consignment, $service, $country) {
        $output = array();
        $output["STATUS"] = "SUCCESS";
        $messageTime = Date("Y-m-d") . "T" . Date("H:i:s-01:00");
        $msgRef = $this->getMesageId($consignment->getId(), true);

        $handling = $service->getCode();
        if (($handling == 'STDHL0WPX' || $handling == 'STDHLCWPX' || $handling == 'STDHLCINT'  ||  $handling == 'SPDHLEXCO'|| $handling == 'STDHLWPXH' || $handling == 'STDHLWPXV' || $handling == 'SPDHLEXHW' || $handling == 'STDHL0DDP') )
                //&& strtoupper($country->getRegion()) == "INT") 
                {
            $globalProductCode = 'P';
            $isDutiable = 'Y';
        } else {
            $isDutiable = 'N';
        }

        $senderCountry = new Country($consignment->getSenderCountryId());
        $region = $senderCountry->getRegionCollection();
        $senderCountryCode = $senderCountry->getIso();

        if($consignment->getShipmentType() == "C"){
            $collectionDate = date("Y-m-d", strtotime($consignment->getCollectionDate()));
            $earliestLatestTime = $consignment->getCollectionStartTime();
            $earliestTime = explode(':', $earliestLatestTime);
            $readyTime = 'PT' . $earliestTime[0] . 'H' . $earliestTime[1] . 'M';
        }
        else
        {
            $collectionDate = date("Y-m-d");
            $readyTime = 'PT13H00M';
        }
        $termsofTrade = "DDU";
        if($handling == "STDHL0DDP")
        {
            $termsofTrade = 'DDP';
        }
        $capibilityRequestXml = '<?xml version="1.0" encoding="UTF-8"?>
                                    <p:DCTRequest xmlns:p="http://www.dhl.com" xmlns:p1="http://www.dhl.com/datatypes"
                                    xmlns:p2="http://www.dhl.com/DCTRequestdatatypes"
                                    xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
                                    xsi:schemaLocation="http://www.dhl.com DCTRequestdatatypes_global.xsd ">
                                   <GetCapability>
                                           <Request>
                                                   <ServiceHeader>
                                                           <MessageTime>' . $messageTime . '</MessageTime>
                                                              <MessageReference>' . $msgRef . '</MessageReference>
                                                            <SiteID>' . @$this->constants['DHL_API_USERNAME'] . '</SiteID>
                                                            <Password>' . @$this->constants['DHL_API_PASSWORD'] . '</Password>
                                                   </ServiceHeader>
                                           </Request>
                                           <From>
                                                   <CountryCode>' . $senderCountryCode . '</CountryCode>
                                                   <Postalcode>' . $consignment->getSenderPostCode() . '</Postalcode>
                                                   <City>' . $consignment->getSenderCity() . '</City>
                                           </From>
                                           <BkgDetails>
                                                   <PaymentCountryCode>GB</PaymentCountryCode>
                                                   <Date>' . $collectionDate . '</Date>
                                                   <ReadyTime>' . $readyTime . '</ReadyTime>
                                                   <DimensionUnit>CM</DimensionUnit>
                                                   <WeightUnit>KG</WeightUnit>
                                                   <IsDutiable>' . $isDutiable . '</IsDutiable>
                                                   <NetworkTypeCode>AL</NetworkTypeCode>
                                                   <QtdShp>
                                                        <QtdShpExChrg>
                                                            <SpecialServiceType>OSINFO</SpecialServiceType>
                                                        </QtdShpExChrg>
	                                           </QtdShp> 
                                           </BkgDetails>
                                           <To>
                                                   <CountryCode>' . $country->getIso() . '</CountryCode>
                                                   <Postalcode>' . $consignment->getPostCode() . '</Postalcode>
                                           </To>
                                           <Dutiable>
                                                   <DeclaredCurrency>' . $consignment->getCurrency() . '</DeclaredCurrency>
                                                   <DeclaredValue>' .($consignment->getValue()<=0 ? 10 :$consignment->getValue()) . '</DeclaredValue>
                                           </Dutiable>
                                   </GetCapability>
                           </p:DCTRequest>';


        $url = $this->constants["DHL_WSDL"];
        $httpPostObj = new HttpCommunication($url);



        if ($httpPostObj->post($capibilityRequestXml)) {
            // create response object from the xml received from DHL
            $capibilityResponseXml = $httpPostObj->getResponse();
            $consignment->setApiData(print_r($capibilityRequestXml, true), print_r($capibilityResponseXml, true), 'CAPABILITY REQUEST');
            $xml = simplexml_load_string($capibilityResponseXml);
            $json = json_encode($xml);
            $array = json_decode($json, TRUE);
            $status = $array["Response"]["Status"]["ActionStatus"];
            $capibilityError = $array["GetCapabilityResponse"]["Note"]["Condition"]["ConditionCode"];
            

            if (strtolower($status) == "error") {
                $errorDescription = $array["Response"]["Status"]["Condition"]["ConditionData"];
                $output["STATUS"] = "ERROR";
                $output["MESSAGE"] = $errorDescription;
            }
            else if ($capibilityError != '') {
                $errorDescription = $array["GetCapabilityResponse"]["Note"]["Condition"]["ConditionData"];
                $output["STATUS"] = "ERROR";
                $output["MESSAGE"] = $errorDescription;
            }
            else {
                $isPaperLessAvailable = false;
                $availableServices = $array["GetCapabilityResponse"]["Srvs"]["Srv"];
                foreach($availableServices as $serv){
                    if($serv["GlobalProductCode"] == $globalProductCode){
                        $availableGlobalOptions = $serv["MrkSrv"];
                        foreach($availableGlobalOptions as $options){
                            if($options["LocalServiceType"] == "WY" && $options["GlobalServiceName"] == "PAPERLESS TRADE"){
                                $isPaperLessAvailable = true;
                               
                                break;
                            }
                        }
                        break;
                    }
                }
                $output["STATUS"] = "SUCCESS";
                $output["MESSAGE"] = $isPaperLessAvailable;
            }
            
            return $output;
        }
    }

    public function remoteareas($consignment, $carrierObject, $sender) {
        
    }

    public function label($consignment, $labelType = 'pdf', $size = '100x150') {
        $output = array();
        $this->serviceValues = new Services($consignment->getServiceId());
		// 199 ID Is for spain. and here we are changeing spain country to canary Islan ig if postcode starts with 35 and 38 and country is spain 
		/*if(
			$consignment->getCountryId() == 199 && 
			trim($consignment->getPostCode()) != '' && 
			trim($consignment->getPostCode()) != '-' && 
			in_array(substr(trim($consignment->getPostCode()),0,2) , array('35', '38'))
		){
			$this->country = new Country(262);
		}
		else*/ 
			$this->country = new Country($consignment->getCountryId());	
			
        
        
		$this->user = SessionManager::getUser();
        $parcels = $consignment->getParcels();

        /*
         * Agent Values
         */

        $agentFilter = new ServiceAgentMappingDataFilter();
        $agentFilter->addFilter("serviceid = '" . $consignment->getServiceId() . "' AND agentid = '" . $consignment->getAgentId() . "' ");
        $agentList = $agentFilter->getList();
        if (count($agentList) > 0) {
            $this->agentValues = $agentList[0];
        }

        /*
         *  Get Service  constants 
         */
        $serviceAgentConstantFilter = new ServiceConstantValueFilter();
        $serviceAgentConstantFilter->addFilter("service_id = '" . $consignment->getServiceId() . "' AND agent_id = '" . $consignment->getAgentId() . "' ");
        $serviceAgentConstant = $serviceAgentConstantFilter->getConstantList("c.constant 'constant_name', scv.constant_value 'constant_value'");
        if (count($serviceAgentConstant) > 0) {
            foreach ($serviceAgentConstant as $serviceAgentConstantData) {
                $this->constants[$serviceAgentConstantData->getConstantName()] = $serviceAgentConstantData->getConstantValue();
            }
        }
        if (trim(@$this->constants['DHL_BILLING_ACCOUNT_NUMBER']) == '' || trim(@$this->constants['DHL_SHIPPER_ACCOUNT_NUMEBR']) == '') {
            $output['STATUS'] = 'ERROR';
            $output['MESSAGE'] = "Please check service constants. Please contact to itsupport@oneworldexpress.com";
            return $output;
        }

        $logo =    User::getUserCompanyImages(false,$consignment->getUserId());
       
        // Get user logo in base64
        $imageFormat = strtoupper(pathinfo($logo, PATHINFO_EXTENSION));
        $base64Logo = $this->image_to_base64($logo);

        // Get Message ID		
        $messageTime = Date("Y-m-d") . "T" . Date("H:i:s-01:00");
        $msgRef = $this->getMesageId($consignment->getId(), true);

        $handling = $this->serviceValues->getCode();
        
        $senderCountry = new Country($consignment->getSenderCountryId());

        if (($handling == 'STDHL0WPX' || $handling == 'STDHLCWPX'  || $handling == 'SPDHLEXCO' || $handling == 'STDHL0DDP' || $handling == 'STDHLWPXH' || $handling == 'STDHLWPXV' || $handling == 'SPDHLEXHW') && strtoupper($this->country->getRegion()) == "INT") {
            $globalProductCode = 'P';
            $localProductCode = 'P';
            $isDutiable = 'Y';
        } else
        if (($handling == 'STDHL0WPX' || $handling == 'STMMSMMC' || $handling == 'STDHL0DDP' || $handling == 'STDHLCINT' || $handling == 'STDHLWPXH' || $handling == 'STDHLWPXV' || $handling == "STDHLCWPX" || $handling == 'SPDHLEXCO'  || $handling == 'SPDHLEXHW') && strtoupper($this->country->getRegion()) == "R1") {
            $globalProductCode = 'P';
            $localProductCode = 'P';
            $isDutiable = 'Y';
        }
         else
        if (( $handling == 'STDHLCINT' ) && strtoupper($senderCountry->getRegion()) == "INT") {
            $globalProductCode = 'P';
            $localProductCode = 'P';
            $isDutiable = 'Y';
        }
         else
        if (( $handling == 'STDHLCINT' ) && strtoupper($senderCountry->getRegion()) == "R1") {
            $globalProductCode = 'P';
            $localProductCode = 'P';
            $isDutiable = 'Y';
        }
         else
        if (( $handling == 'STDHLCINT' ) && strtoupper($senderCountry->getRegion()) == "DBP") {
            $globalProductCode = 'N';
            $localProductCode = 'C';
            $isDutiable = 'N';
        }
        else
        if ($handling == 'STDHL0DOX' ) {
            $globalProductCode = 'D';
            $localProductCode = 'D';
            $isDutiable = 'N';
        } 
        else if ($handling == 'STDHL0ESU') {
            $globalProductCode = 'H';
            $localProductCode = 'I';
            $isDutiable = 'Y';
        } else
        if (($handling == 'STDHLDOM' || $handling == 'STDHLCWPX' || $handling == 'SPDHLEXHW') && strtoupper($this->country->getRegion()) == "DBP") {
            $globalProductCode = 'N';
            $localProductCode = 'C';
            $isDutiable = 'N';
        }

        $termsofTrade = "DDU";
        if($handling == "STDHL0DDP")
        {
            $termsofTrade = 'DDP';
        }
        
        if (sizeof($this->country) > 0) {
        
            $recCountryIso = $this->country->getIso();

            if ($recCountryIso == 'CW')
                $recCountryIso = 'XC';
            $recCountryName = $this->country->getName();
        }
        else {
            $output['STATUS'] = 'ERROR';
            $output['MESSAGE'] = "Counrty Iso not found for  " . $this->country->getName();
            return $output;
        }
        if ($consignment->getShipmentType() == "C") {
            $regionCollectionCountry = $consignment->getSenderCountryId();
            $earliestLatestTime = $consignment->getCollectionStartTime();
            $earliestTime = explode(':', $earliestLatestTime[0]);
            $readyTime = 'PT' . $earliestTime[0] . 'H' . $earliestTime[1] . 'M';
        } else {
            $regionCollectionCountry = $this->serviceValues->getOriginCountry();
        }

        $regionCollection = new Country($regionCollectionCountry);
        if (count($regionCollection) <= 0) {
            $output['STATUS'] = 'ERROR';
            $output['MESSAGE'] = "No Consignment found";
            return $output;
        }
        $region = $regionCollection->getRegionCollection();
        $senderCountryCode = $regionCollection->getIso();


        if (trim($consignment->getCompany()) == '')
            $company = $consignment->getContact();
        else
            $company = $consignment->getCompany();

        if ($consignment->getReference() != '')
            $reference = $consignment->getHawb() . ' - ' . $consignment->getReference();
        else
            $reference = $consignment->getHawb();


        $itemType = "";

        if (strtolower($consignment->getItemType()) == "lithium ion battery") {
            $itemType = "Lithium Ion Batteries in compliance with Section II of PI967";
        }
        if (strtolower($consignment->getItemType()) == "lithium metal battery") {
            $itemType = "Lithium Metal Batteries in compliance with Section II of PI970";
        }

        /*if($consignment->getShipmentType() == "C" && $consignment->getSenderCountryId() == "225" && $consignment->getAgentId() == "1"){
            $accountNumber = "181080000";
        }
        else
        {*/
            $accountNumber = $this->constants['DHL_SHIPPER_ACCOUNT_NUMEBR'];
        //}
            $telephone = trim($consignment->getTelephone());
            if($telephone == ""){
                $telephone = "00442088676060";
            }
        $requestXml = '<?xml version="1.0" encoding="UTF-8"?>
                        <req:ShipmentRequest xmlns:req="http://www.dhl.com" 
                        xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" 
                        xsi:schemaLocation="http://www.dhl.com ship-val-global-req-6.2.xsd" schemaVersion="6.2">
                        
                        <Request>
                            <ServiceHeader>
                                <MessageTime>' . $messageTime . '</MessageTime>
                                <MessageReference>' . $msgRef . '</MessageReference>
                                <SiteID>' . trim(@$this->constants['DHL_API_USERNAME']) . '</SiteID>
                                <Password>' . trim(@$this->constants['DHL_API_PASSWORD']) . '</Password>
                            </ServiceHeader>
                            <MetaData>
                                <SoftwareName>smarttrack</SoftwareName>
                                <SoftwareVersion>v1.1.1</SoftwareVersion>
                            </MetaData>
                        </Request>
                            <RegionCode>' . $region . '</RegionCode>
                            <LanguageCode>en</LanguageCode>
                            <PiecesEnabled>Y</PiecesEnabled>
                        <Billing>
                            <ShipperAccountNumber>'.$accountNumber.'</ShipperAccountNumber>
                            <ShippingPaymentType>S</ShippingPaymentType>
                            <BillingAccountNumber>'.$accountNumber.'</BillingAccountNumber>
                        </Billing>
                        <Consignee>
                            <CompanyName><![CDATA[' . $this->utfEncode($company) . ']]></CompanyName>
                            <AddressLine><![CDATA[' . $this->utfEncode($consignment->getAddressLine1()) . ']]></AddressLine>
                            <AddressLine><![CDATA[' . $this->utfEncode($consignment->getAddressLine2()) . ']]></AddressLine>		 
                            <AddressLine><![CDATA[' . $this->utfEncode($consignment->getAddressLine3()) . ']]></AddressLine>
                            <City>' . $this->utfEncode((trim($consignment->getCity()) == '-' ? '' : $consignment->getCity())) . '</City>
                            <Division>' . $this->utfEncode($consignment->getState()) . '</Division>			
                            <PostalCode>' . (trim($consignment->getPostCode()) == '-' ? '' : $consignment->getPostCode()) . '</PostalCode>
                            <CountryCode>' . $recCountryIso . '</CountryCode>
                            <CountryName>' . $recCountryName . '</CountryName>		
                            <Contact>
                                    <PersonName><![CDATA[' . $this->utfEncode($consignment->getContact()) . ']]></PersonName>
                                    <PhoneNumber>' . $telephone . '</PhoneNumber>
                            </Contact>
                        </Consignee>';

        if($this->serviceValues->getProformaInvoice() == 1 &&  $isDutiable == "Y"){
        //Check if paperless Trade Available
        $capabilityCheck = $this->getCapibilityRequest($consignment, $this->serviceValues, $this->country);
        $paperLessRequst = "";
            if ($capabilityCheck["STATUS"] == "SUCCESS" ) {
            $isPaperLess = $capabilityCheck["MESSAGE"];
            // if paperless available
            if($isPaperLess){
                
                // Check if user upload invoice file. If so then send request as embeded invoice to dhl
                $invoicePath = "../_assets/paperless_invoice/";
                $fileName = md5($consignment->getId()) . ".pdf";
                if(file_exists($invoicePath . $fileName)){
                   $invoiceFile = $invoicePath . $fileName;
                }
                else
                {
                    
                    $invoicepdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
                    $invoicepdf->SetX(1.0);
                    $glOrderPdf = new ProformaInvoice($invoicepdf);
                    $invoiceFile = $glOrderPdf->AddHTML($consignment); 
                }
                if($invoiceFile != ''){
                    
                    $base64Invoice = $this->image_to_base64($invoiceFile);
                    $requestXml .= '<Dutiable>
                                <DeclaredValue>' . number_format(($consignment->getValue()<=0?10:$consignment->getValue()), 2, '.', '') . '</DeclaredValue>
                                <DeclaredCurrency>' . $consignment->getCurrency() . '</DeclaredCurrency>
                                <TermsOfTrade>' . $termsofTrade . '</TermsOfTrade>
                            </Dutiable>';
                    $paperLessRequst .= '<SpecialService>
                                <SpecialServiceType>WY</SpecialServiceType>
                            </SpecialService>
                            <DocImages>
                                <DocImage>
                                <Type>CIN</Type>
                                <Image>'.$base64Invoice.'</Image>
                                <ImageFormat>PDF</ImageFormat>
                                </DocImage>
                            </DocImages>'; 
                }
                else
                {
                    // If user does not upload invoice then use DHL invoice and send data in request
                    
                }
            }
            // if not available
            else
            {
                $requestXml .= '<Dutiable>
                            <DeclaredValue>' . number_format(($consignment->getValue()<=0?10:$consignment->getValue()), 2, '.', '') . '</DeclaredValue>
                            <DeclaredCurrency>' . $consignment->getCurrency() . '</DeclaredCurrency>
                            <TermsOfTrade>' . $termsofTrade . '</TermsOfTrade>
                        </Dutiable>';
            }
        }
            else
            {
                return $capabilityCheck;
            }
        }
        
        /*
         * for Brexit
         */
        
        if($consignment->getCountryId() != $this->user->getCountryId() && $isDutiable == "Y"){
            $requestXml .= '<UseDHLInvoice>N</UseDHLInvoice>
                            <DHLInvoiceLanguageCode>en</DHLInvoiceLanguageCode>
                            <DHLInvoiceType>CMI</DHLInvoiceType>
                            <ExportDeclaration>
                                <InvoiceNumber>'.$consignment->getId().'</InvoiceNumber>
                                <InvoiceDate>'.date("Y-m-d").'</InvoiceDate>';
            if(count($parcels) > 0){
                foreach($parcels as $keyParcel=>$parcel){
                    $parcelDescription = json_decode($parcel->getDescription());
                    $parcelCountry = json_decode($parcel->getCommodityCode());
                    $parcelQty = json_decode($parcel->getQty());
                    $parcelValue = json_decode($parcel->getItemValue());
                    $parcelHscode = json_decode($parcel->getHsCode());
                    $parcelSku = json_decode($parcel->getItemSku());
                    $parcelWeight = json_decode($parcel->getPWeight());
                    $count = count($parcelDescription);
                    $i = 0;
                    if(count($parcelDescription) > 0){
                        foreach($parcelDescription as $key=>$desc){
                            $i++;
                            $requestXml   .=  '<ExportLineItem>
                                    <LineNumber>'.$i.'</LineNumber>
                                    <Quantity>'.$parcelQty[$key].'</Quantity>
                                    <QuantityUnit>BOX</QuantityUnit>
                                    <Description>'.$desc.'</Description>
                                    <Value>'.$parcelValue[$key].'</Value>
                                    <CommodityCode>'.($parcelHscode[$key] == '' ? '000000' : $parcelHscode[$key]).'</CommodityCode>
                                    <Weight>
                                        <Weight>'.$parcelWeight[$key].'</Weight>
                                        <WeightUnit>K</WeightUnit>
                                    </Weight>
                                    <GrossWeight>
                                        <Weight>'.$parcelWeight[$key].'</Weight>
                                        <WeightUnit>K</WeightUnit>
                                    </GrossWeight>
                                </ExportLineItem>';
                        }
                    }
                    else
                    {
                        $numberOfPieces = $consignment->getNumberPieces();
                        $requestXml   .=  '<ExportLineItem>
                                    <LineNumber>1</LineNumber>
                                    <Quantity>1</Quantity>
                                    <QuantityUnit>BOX</QuantityUnit>
                                    <Description>'.$consignment->getDescription().'</Description>
                                    <Value>'.number_format($consignment->getValue()/$numberOfPieces,2).'</Value>
                                    <CommodityCode>000000</CommodityCode>
                                    <Weight>
                                        <Weight>'.number_format($consignment->getWeight()/$numberOfPieces,2).'</Weight>
                                        <WeightUnit>K</WeightUnit>
                                    </Weight>
                                    <GrossWeight>
                                        <Weight>'.number_format($consignment->getWeight()/$numberOfPieces,2).'</Weight>
                                        <WeightUnit>K</WeightUnit>
                                    </GrossWeight>
                                </ExportLineItem>';
                    }
                }
        }
            $requestXml .= '</ExportDeclaration>';
        }

        $requestXml .= '<Reference>
                    <ReferenceID>' . $this->utfEncode($reference) . '</ReferenceID>		
                </Reference>	
		<ShipmentDetails>
                    <NumberOfPieces>' . $consignment->getNumberPieces() . '</NumberOfPieces>
                    <Pieces>' . $this->getXMLNumberOfPieces($consignment, $parcels) . '</Pieces>
                    <Weight>' . $consignment->getWeight() . '</Weight>
                    <WeightUnit>K</WeightUnit>
                    <GlobalProductCode>' . $globalProductCode . '</GlobalProductCode>
                    <LocalProductCode>' . $localProductCode . '</LocalProductCode>';
        if ($consignment->getShipmentType() == "C") {
            $requestXml .= '<Date>' . date("Y-m-d", strtotime($consignment->getCollectionDate())) . '</Date>';
        } else {
            $requestXml .= '<Date>' . date('Y-m-d') . '</Date>';
        }
//'.$consignment->getValue().'
        $requestXml .= '<Contents><![CDATA[' . substr($this->utfEncode($consignment->getDescription() . PHP_EOL . $itemType),0,85) . ']]></Contents>
                    <DimensionUnit>C</DimensionUnit>';
        
        if($consignment->getIsInsured() == 1)
           $requestXml .= '<InsuredAmount>'.number_format(($consignment->getValue()<=0?10:$consignment->getValue()), 2, '.', '').'</InsuredAmount>';   
        $requestXml .= '<IsDutiable>' . $isDutiable . '</IsDutiable>
                    <CurrencyCode>' . $consignment->getCurrency() . '</CurrencyCode>		
                </ShipmentDetails>';
        if ($consignment->getShipmentType() == "C") {
            $senderCountry = new Country($consignment->getSenderCountryId());
            $requestXml .= '<Shipper>
		<ShipperID>' . $consignment->getHawb() . '</ShipperID>
		<CompanyName>' . $this->utfEncode($consignment->getSenderCompany()) . '</CompanyName>
		<AddressLine>' . $this->utfEncode($consignment->getSenderAddressLine1()) . '</AddressLine>
		<AddressLine>' . $this->utfEncode($consignment->getSenderAddressLine2()) . '</AddressLine>
		<AddressLine>' . $this->utfEncode($consignment->getSenderAddressLine3()) . '</AddressLine>
		<City>' . $this->utfEncode($consignment->getSenderCity()) . '</City>
		<Division>' . $this->utfEncode(@$consignment->getSenderState()) . '</Division>			
		<PostalCode>' . $this->utfEncode($consignment->getSenderPostCode()) . '</PostalCode>
		<CountryCode>' . $senderCountry->getIso() . '</CountryCode>
		<CountryName>' . $this->utfEncode($senderCountry->getName()) . '</CountryName>
                <FederalTaxId>'.$this->utfEncode($consignment->getVatNumber()).'</FederalTaxId>
                <EORI_No>'.$this->utfEncode($consignment->getEoriNumber()).'</EORI_No>
		<Contact>
			<PersonName>' . $this->utfEncode($consignment->getSenderName()) . '</PersonName>
			<PhoneNumber>' . $this->utfEncode($consignment->getSenderTelephone()) . '</PhoneNumber>			
		</Contact>';
           if($this->country->getRegion() == "R1" && trim($consignment->getIossNumber()) != ""){
            $requestXml .=    '<RegistrationNumbers>
                    <RegistrationNumber>
                    <Number>'.$this->utfEncode($consignment->getIossNumber()) .'</Number>
                    <NumberTypeCode>SDT</NumberTypeCode>
                    <NumberIssuerCountryCode>GB</NumberIssuerCountryCode>
                    </RegistrationNumber>
                </RegistrationNumbers>';
            }
	$requestXml .= '</Shipper>	';
            
        } else {
            $requestXml .= '<Shipper>
                    <ShipperID>' . $consignment->getHawb() . '</ShipperID>
                    <CompanyName>' . $this->constants["DHL_SHIPPER_COMPANY"] . '</CompanyName>
                    <AddressLine>' . $this->constants["DHL_SHIPPER_ADDRESSLINE_1"] . '</AddressLine>
                    <AddressLine>' . $this->constants["DHL_SHIPPER_ADDRESSLINE_2"] . '</AddressLine>
                    <City>' . $this->constants["DHL_SHIPPER_CITY"] . '</City>
                    <PostalCode>' . $this->constants["DHL_SHIPPER_POSTCODE"] . '</PostalCode>';
            $shipper_country = $this->constants['DHL_SHIPPER_COUNTRY'];
            if ((int) $shipper_country > 0) {
                $shipperCountry = new Country($shipper_country);
                $sCountry = $shipperCountry->getName();
                $siso = $shipperCountry->getIso();
            } else {
                $siso = $shipper_country;
            }
            $requestXml .= '<CountryCode>' . $siso . '</CountryCode>
                        <CountryName>' . $sCountry . '</CountryName>'
                    . '<FederalTaxId>'.$this->utfEncode($consignment->getVatNumber()).'</FederalTaxId>
                        <EORI_No>'.$this->utfEncode($consignment->getEoriNumber()).'</EORI_No>';
            


            $telephone = $consignment->getTelephone();
            if ($telephone == "")
                $telephone = '00442088676060';
            $requestXml .= '
        <Contact>
        <PersonName>' . $this->utfEncode($consignment->getSenderName()) . '</PersonName>
        <PhoneNumber>' . $telephone . '</PhoneNumber>
        </Contact>';
        if($this->country->getRegion() == "R1" && trim($consignment->getIossNumber()) != ""){
            $requestXml .=    '<RegistrationNumbers>
                    <RegistrationNumber>
                    <Number>'.$this->utfEncode($consignment->getIossNumber()) .'</Number>
                    <NumberTypeCode>SDT</NumberTypeCode>
                    <NumberIssuerCountryCode>GB</NumberIssuerCountryCode>
                    </RegistrationNumber>
                </RegistrationNumbers>';
            }
	$requestXml .= '</Shipper>	';
            
          if($handling == "STDHL0DDP")
          {
              $requestXml .= '<SpecialService>
                                    <SpecialServiceType>DD</SpecialServiceType>
                            </SpecialService>';
          }
        }
        
        if($consignment->getIsInsured() == 1){
                // For insurance
                $requestXml .= '<SpecialService> 
                                <SpecialServiceType>I</SpecialServiceType> 
                                <ChargeValue>'.number_format(($consignment->getValue()<=0?10:$consignment->getValue()), 2, '.', '').'</ChargeValue> 
                                <CurrencyCode>'. $consignment->getCurrency().'</CurrencyCode> 
                                </SpecialService>';
            }
        
        $requestXml .= $paperLessRequst;
        $requestXml .= '
        <LabelImageFormat>PDF</LabelImageFormat>
        <Label>
        <Logo>Y</Logo>
        <CustomerLogo>
        <LogoImage>' . $base64Logo . '</LogoImage>
        <LogoImageFormat>'.$imageFormat.'</LogoImageFormat>
        </CustomerLogo>
        <Resolution>200</Resolution>
        </Label>
        <SinglePieceImage>Y</SinglePieceImage>    
        </req:ShipmentRequest>';
        
        //$url = "https://xmlpitest-ea.dhl.com/XMLShippingServlet"; // test url
        $url = $this->constants["DHL_WSDL"]; //"http://xmlpi-ea.dhl.com/XMLShippingServlet";
        $httpPostObj = new HttpCommunication($url);
		
        if ($httpPostObj->post($requestXml)) {

            // create response object from the xml received from DHL
            $responseXml = $httpPostObj->getResponse();
            
            $xml = simplexml_load_string($responseXml);
            $json = json_encode($xml);
            $array = json_decode($json, TRUE);
           
            $consignment->setApiData(print_r($requestXml, true), print_r($array, true), 'LABEL');
            
            $error_array_status = $array["Response"]["Status"]["Condition"];
            
            if (        trim(@$array["Response"]["Status"]["ActionStatus"]) == "Error" || 
                        trim(@$array["Response"]["Status"]["Condition"]["ConditionData"]) != '' ||
                        count($array["Response"]["Status"]["Condition"]) > 0) {

                $message = '';
                foreach ($error_array_status as $error_msg) {
                    if (is_array($error_msg))
                        $message .= $error_msg["ConditionData"] . ", ";
                    else
                        $message .= $error_msg;
                }
                if ($message == '') {
                    $message = "Some error occurred from DHL Api ";
                }

                $output['STATUS'] = 'ERROR';
                $output['MESSAGE'] = $message;
                return $output;
            } elseif ($array["Note"]["ActionNote"] == "Success") {

                if ($consignment->getShipmentType() == 'C') {
                    $result = $this->sendRequestToDhl($consignment, $parcels, $this->constants, $accountNumber);
                    if ($result['STATUS'] == 'ERROR') {
                        return $result;
                    }
                }


                $awb = $array["AirwayBillNumber"];
                $licence_plate_array[] = $awb;

                //$labelImage = $array["LabelImage"]["OutputImage"];
                $labelImage = $array["LabelImage"]["MultiLabels"]["MultiLabel"];
                
                //Decode pdf content
                $numberOfPieces = $consignment->getNumberPieces();
                $pieceLabelArray = array();
                $archiveLabel = "";
                $responsePieceArray = array();
                foreach($labelImage as $label){
                    $docname = $label["DocName"];
                    $piecelabel = "";
                    if(strpos($docname,"Archive") !== false){
                        $archiveLabel = $label["DocImageVal"];
                        $pdf_decoded_archive = base64_decode($archiveLabel);
                        $archiveFileName = '../_assets/pdf/' . date('Y_m_d') . '/' . $docname . ".pdf";
                        $pdfarchive = fopen($archiveFileName, 'w');
                        fwrite($pdfarchive, $pdf_decoded_archive);
                        fclose($pdfarchive);
                        //$pieceLabelArray[] = $archiveFileName;
                    }
                    else
                    {
                        $pieceNo = str_replace("TransportLabel_", "", $docname);
                        $piecelabel = $label["DocImageVal"];
                        $pdf_decoded_piece = base64_decode($piecelabel);
                        $pieceFileName = '../_assets/pdf/' . date('Y_m_d') . '/' . $pieceNo . ".pdf";
                        $pdfpiece = fopen($pieceFileName, 'w');
                        fwrite($pdfpiece, $pdf_decoded_piece);
                        fclose($pdfpiece);
                        $pieceLabelArray[] = $pieceFileName;
                        $responsePieceArray[] = $pieceFileName;
                        
                        $PDFMerger = new PDFMerger();
                        $PDFMerger->addPDF($pieceFileName);
                        $PDFMerger->addPDF($archiveFileName);
                        try {
                            $PDFMerger->mergeDhl('file', $pieceFileName, '', $consignment);
                        } catch (Exception $e) {
                            echo 'Caught exception: ', $e->getMessage(), "\n";
                        }
                    }
                }
                


                $mergeFileName = '../_assets/pdf/' . date('Y_m_d') . '/' . $consignment->getId() . ".pdf";
               /* $pdf = fopen($mergeFileName, 'w');
                fwrite($pdf, $pdf_decoded);
                fclose($pdf);
                */

                $PDFMerger = new PDFMerger();
                foreach($pieceLabelArray as $key=>$pieceLabel){
                    if($key == (count($pieceLabelArray)-1))
                        $PDFMerger->addPDF($pieceLabel);
                    else
                        $PDFMerger->addPDF($pieceLabel, 1);
                }
                try {
                    $PDFMerger->merge('file', $mergeFileName);
                } catch (Exception $e) {
                    echo 'Caught exception: ', $e->getMessage(), "\n";
                }


                $count = 0;
                $arr_pieces = $array['Pieces'];
                $arr_LicencePlate = array();
                if ($consignment->getNumberPieces() == 1) {
                    foreach ($arr_pieces as $piece) {

                        if (trim($piece['LicensePlate']) != '') {
                            $licence_plate_array[] = $piece['LicensePlate'];
                            $parcel = $parcels[$count++];
                            //$parcel = new Parcel();
                            $arr_LicencePlate[] = $piece['LicensePlate'];
                            $parcel->setTrackingNumber($piece['LicensePlate']);
                            $parcel->setConsignmentID($consignment->getId());
                            $parcel->setParcelLabel(date('Y_m_d') . '/' . $piece['LicensePlate']. ".pdf");
                            $parcel->save();
                        }
                    }
                } else {
                    foreach ($arr_pieces['Piece'] as $piece) {
                        if (trim($piece['LicensePlate']) != '') {
                            $licence_plate_array[] = $piece['LicensePlate'];
                            //$parcel = new Parcel();
                            $parcel = $parcels[$count++];
                            $arr_LicencePlate[] = $piece['LicensePlate'];
                            $parcel->setTrackingNumber($piece['LicensePlate']);
                            $parcel->setConsignmentID($consignment->getId());
                            $parcel->setParcelLabel(date('Y_m_d') . '/' . $piece['LicensePlate']. ".pdf");
                            $parcel->save();
                        }
                    }
                }
                

                //$this->pdf->Output("../_assets/pdf/" . date('Y_m_d') . '/' . $consignment->getId() . ".pdf", "F");
                $output['STATUS'] = 'SUCCESS';
                $output['LABEL'] = date('Y_m_d') . '/' . $consignment->getId() . ".pdf";
                $output['TRACKING_NUMBER'] = $licence_plate_array;
                $output['PARCEL_LABEL'] = $responsePieceArray;
                return $output;
            }
        } else {
            $consignment->setApiData(print_r($requestXml, true), '', 'FAILED_TO_CALL_API');
        }
    }

    public function tracking($trackingNumber, $trackBy = 'shipment', $EDI = false) {
        
        $tracking = new Tracking();
        $consignmentFilter = new ConsignmentFilter();
        $consignmentFilter->addAwbAndHawbOrFilter($trackingNumber);
        $consignment = $consignmentFilter->getColumnList('*');
      
        if(count($consignment) <= 0)
        {
            $parcelFilter = new ParcelFilter();
            $parcelFilter->addTrackingNumberFilter($trackingNumber);
            $parcel = $parcelFilter->getColumnList('*');
            if(count($parcel) > 0)
            {
                $consignmentId = $parcel[0]->getConsignmentId();
                $consignment[] = New Consignment($consignmentId);
                $trackBy = 'parcel';
            }
        }
       
        if(count($consignment) > 0)
        {
            $serviceAgentConstantFilter = new ServiceConstantValueFilter();
            $serviceAgentConstantFilter->addFilter("service_id = '" . $consignment[0]->getServiceId() . "' AND agent_id = '" . $consignment[0]->getAgentId() . "' ");
            $serviceAgentConstant = $serviceAgentConstantFilter->getConstantList("c.constant 'constant_name', scv.constant_value 'constant_value'");

            if (count($serviceAgentConstant) > 0) {
                foreach ($serviceAgentConstant as $serviceAgentConstantData) {
                    $this->constants[$serviceAgentConstantData->getConstantName()] = $serviceAgentConstantData->getConstantValue();
                }
            }


            $username = $this->constants['DHL_API_USERNAME'];
            $password = $this->constants['DHL_API_PASSWORD'];
            $url = $this->constants['DHL_WSDL'];

            $msgRef = time();
            $request = new DhlTrackingRequest();
            $request->setHeaderInfo($msgRef, $username, $password);
            $trackBy = $trackBy == 'shipment' ? 'shipment' : 'parcel';
            if ($trackBy == 'shipment')
                $request->setAirwayBill($trackingNumber);
            else if ($trackBy == 'parcel')
                $request->setLPNumber($trackingNumber);
            $request->setDetailLevel(DhlTrackingRequest::DETAIL_ALL);

            $requestXml = $request->getXmlRequest();

            // send reqeust to DHL
            $httpPostObj = new HttpCommunication($url);
            if ($httpPostObj->post($requestXml)) {
                // create response object from the xml received from DHL
                $responseXml = $httpPostObj->getResponse();
                $responseXml = utf8_encode($responseXml);
                //print_r($responseXml); die;
                $dhlResponse = xml2array($responseXml);
                if (isset($dhlResponse['req:TrackingResponse']['AWBInfo'])) {
                    $AWBInfo = [];
                    $_AWBInfo = $dhlResponse['req:TrackingResponse']['AWBInfo'];
                    if (!isset($_AWBInfo[0])) {
                        $AWBInfo[0] = $_AWBInfo;
                    } else {
                        $AWBInfo = $_AWBInfo;
                    }
                    
                    foreach ($AWBInfo as $trackingInfo) {
                        $trackingNo = $trackingInfo['AWBNumber'];
                        $Status = $trackingInfo['Status']['ActionStatus'];
                        if ($Status == 'success') {
                            
                            $_ShipmentEvent = isset($trackingInfo['ShipmentInfo']['ShipmentEvent']) ? $trackingInfo['ShipmentInfo']['ShipmentEvent'] : [];
                            $entityId = 0;
                            if ($trackBy == 'shipment') {
                                $shipmenObj = new ConsignmentFilter();
                                $shipmenObj->addawbFilter($trackingNo);
                                $shipmentDataArray = $shipmenObj->getColumnList('c.awb');
                                if (count($shipmentDataArray) > 0) {
                                    $shipmentData = $shipmentDataArray[0];
                                    $entityId = $shipmentData->getId();
                                }
                            } else if ($trackBy == 'parcel') {
                                $_trackingNo = isset($trackingInfo['TrackedBy']['LPNumber']) ? $trackingInfo['TrackedBy']['LPNumber'] : $trackingInfo['AWBNumber'];
                                $trackingNo = $_trackingNo;
                                $parcelObj = new ParcelFilter();
                                $parcelObj->addTrackingNumberFilter($_trackingNo); 
                                $parcelDataArray = $parcelObj->getColumnList('p.id, p.tracking_number, p.consignment_id');
                             
                                if (count($parcelDataArray) > 0) {
                                    $parcelData = $parcelDataArray[0];
                                    $entityId = $parcelData->getId();
                                }
                            }
                      //echo $entityId; die;      
                            ////////////////////// Carrier Received ////////////////////////////////
            
                            $trackingDataFilterObj = new TrackingDataFilter();
                            $trackingDataFilterObj->addFieldFilter('    tracking_number', $trackingNo);        
                            $trackingDataFilterObj->addFilter("carrier_code not in ('','CC')");
                            $trackingEvents = $trackingDataFilterObj->getList();
//print_r($trackingEvents); die;
                            if(count($trackingEvents) > 0)
                            {
                                $carrierReceivedCheck = 0;   // there is already carrier received event                
                                $trackingDataFilterObj = new TrackingDataFilter();
                                $trackingDataFilterObj->addFieldFilter('    tracking_number', $trackingNo);        
                                $trackingDataFilterObj->addFilter("status_code_id = '148'");
                                $CarrierReceivedObj = $trackingDataFilterObj->getList();   
                                if(count($CarrierReceivedObj) >0 )
                                {
                                    $carrierCodeCarrierReceived = $CarrierReceivedObj[0]->getCarrierCode();
                                    $carrierReceivedStatusCode = $CarrierReceivedObj[0]->getStatusCodeId();   
                                }
                            }
                            else
                            {
                                $carrierReceivedCheck = 1; // No carrier received Event
                            } 
                            ////////////////////// Carrier Received ////////////////////////////////
                            if (count($_ShipmentEvent) > 0 && $entityId > 0) {
                                
                                if (!isset($_ShipmentEvent[0])) {
                                         $ShipmentEvent[0] = $_ShipmentEvent;
                                } 
                                else {
                                         $ShipmentEvent = $_ShipmentEvent;
                                }
                                if ($trackBy == 'shipment') 
                                {
                                    $consignmentEntity = new Consignment($entityId);
                                    $finalStatusCode = $consignmentEntity->getShipmentStatus();                                    
                                }
                                else
                                {
                                    $parcelEntity = new Parcel($entityId);
                                    $finalStatusCode = $parcelEntity->getParcelStatusCode();                                    
                                }
                               //echo $finalStatusCode; die;
                                $deliveredArray = ['OK'];
                                $isDelervedStatus = false;
                                foreach ($ShipmentEvent as $event) {
                                   
                                    $Date = $event['Date']; 
                                    $Time = $event['Time'];
                                    $DateTime = $Date . " " . $Time;

                                    $ServiceEvent = $event['ServiceEvent']; 
                                    
                                    $EventCode = $ServiceEvent['EventCode'];
                                    $EventDescription = $ServiceEvent['Description'];

                                    $ServiceArea = $event['ServiceArea'];
                                    $ServiceAreaCode = $ServiceArea['ServiceAreaCode'];
                                    $ServiceAreaDescription = $ServiceArea['Description'];
                                    $Signatory = is_string($event['Signatory']) ? $event['Signatory'] : '';
                                    
                                    // Dont enter any other event code if it is against 148 Event Code
                                    if($carrierCodeCarrierReceived == $EventCode)
                                    {
                                        continue;
                                    }
                    
                                    ////////////////////// Carrier Received ////////////////////////////////
                                    if($carrierReceivedCheck == 1 && $EventCode != 'CC' )
                                    {
                                        $spTrackingStatus = '148';
                                        $carrierReceivedCheck = 0 ;                        
                                    }
                                    else
                                    {
                                        $spTrackingStatus = DhlTrackingStatus::getOweStatusCode($EventCode);
                                    }            
                                    ////////////////////// Carrier Received ////////////////////////////////
                                    if($spTrackingStatus == 121)
                                            $isDelervedStatus = true;
                                    $result = $tracking->saveTrackPoint($entityId, $trackBy, $DateTime, $trackingNo, $spTrackingStatus, $ServiceAreaDescription, $EventCode, $EventDescription, $deliveredArray, $finalStatusCode);
                                    if($result == true)
                                    {
                                        break;
                                    }
                                }
                                
                                if($isDelervedStatus == true && $trackBy == 'parcel'){
                                        $this->tracking($consignment[0]->getAwb());
                                }
                                $tracking->saveConsignmentTrackingStatus($trackingNo, 'DhlTrackingStatus');
                            }
                        }
                    }
                }
            }
        }
   }

    public function sendData($tracking_numbers = array()) {
        
    }
    public function manifest($consignment) {
        
    }

    public function preAdvice($consignment) {
        
    }

    private function image_to_base64($account) {
//        $path_to_image = "../images/logo.png";
//
//        if ($account == "PROFM") {
//            $path_to_image = "../images/userlogo/PROFMLogo.png";
//        } else {
//            $path_to_image = "../images/logo.png";
//        }
//
//        $type = pathinfo($path_to_image, PATHINFO_EXTENSION);
        $image = file_get_contents($account);

        $base64 = base64_encode($image);
        return $base64;
    }

    public function sendRequestToDhl($consignment, $parcellist, $constant, $account_number) {

        $handling = $this->serviceValues->getCode();
        $globalProductCode = '';
        if ($handling == "STDHLCWPX" || $handling == 'SPDHLEXHW' || $handling == 'STDHLCINT')
            $globalProductCode = "P";
        else if ($handling == "STDHL0DOX")
            $globalProductCode = "D";
        else if ($handling == "STDHL0ECX")
            $globalProductCode = "U";
        elseif ($handling == 'STDHLDOM') {
            $globalProductCode = 'N';
        }



        $cid = $consignment->getId();
        $senderCountry = new Country($consignment->getSenderCountryId());
        $regionCollection = $senderCountry->getRegionCollection();
        $senderISOCode = $senderCountry->getIso();

        $time = Date("Y-m-d") . "T" . Date("H:i:s-01:00");
        $msgRef = $this->getMesageId($cid, true);

        $earliest_time = $consignment->getCollectionStartTime();
        $latest_time = $consignment->getCollectionEndTime();

        $parcellist = $parcellist[0];
        $pickupdate = date("Y-m-d", strtotime($consignment->getCollectionDate()));

     
        //$account_number = $constant['DHL_SHIPPER_ACCOUNT_NUMEBR'];
        $siteID = $constant['DHL_API_USERNAME'];
        $password = $constant['DHL_API_PASSWORD'] ;


        $requestXml = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>
            <req:BookPURequest xmlns:req=\"http://www.dhl.com\" xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" 
            xsi:schemaLocation=\"http://www.dhl.com book-pickup-global-req.xsd\" schemaVersion=\"1.0\">
            <Request>
                <ServiceHeader>
                    <MessageTime>" . $time . "</MessageTime>
                    <MessageReference>" . $msgRef . "</MessageReference>
                    <SiteID>" . $siteID . "</SiteID>
                    <Password>" . $password . "</Password>
                </ServiceHeader>
           </Request>
            <RegionCode>" . $regionCollection . "</RegionCode>
             <Requestor>
                 <AccountType>D</AccountType>
                 <AccountNumber>" . $account_number . "</AccountNumber>
                 <RequestorContact>
                     <PersonName>Andy Chitty</PersonName>
                     <Phone>020 8995 0162</Phone>
                     <PhoneExtension></PhoneExtension>
                 </RequestorContact>
                         <CompanyName>" . $this->utfEncode($consignment->getSenderCompany()) . "</CompanyName>
             </Requestor>
             <Place>
                 <LocationType>B</LocationType>
                 <CompanyName>" . $this->utfEncode($consignment->getSenderCompany()) . "</CompanyName>
                 <Address1>" . $this->utfEncode($consignment->getSenderAddressLine1()) . "</Address1>
                 <Address2></Address2>
                         <PackageLocation>".($consignment->getRoutingCodeEur() == "" ? "reception" : $consignment->getRoutingCodeEur())."</PackageLocation>
                         <City>" . $this->utfEncode($consignment->getSenderCity()) . "</City>
                         <StateCode></StateCode>
                 <DivisionName></DivisionName>
                 <CountryCode>" . $senderISOCode . "</CountryCode>

                 <PostalCode>" . $this->utfEncode($consignment->getSenderPostcode()) . "</PostalCode>
             </Place>
             <Pickup>
                 <PickupDate>" . $pickupdate . "</PickupDate>
                 <ReadyByTime>" . $earliest_time . "</ReadyByTime>
                 <CloseTime>" . $latest_time . "</CloseTime>
                 <Pieces>" . $consignment->getNumberPieces() . "</Pieces>
                 <weight>
                     <Weight>" . $consignment->getWeight() . "</Weight>
                     <WeightUnit>K</WeightUnit>
                 </weight>
             </Pickup>
             <PickupContact>
                 <PersonName>" . $consignment->getSenderName() . "</PersonName>
                 <Phone>" . $consignment->getSenderTelephone() . "</Phone>
                 <PhoneExtension></PhoneExtension>
             </PickupContact>
             <ShipmentDetails>
                 <AccountType>D</AccountType>
                 <AccountNumber>" . $account_number . "</AccountNumber>
                 <BillToAccountNumber>" . $account_number . "</BillToAccountNumber>
                 <AWBNumber>" . $consignment->getAwb() . "</AWBNumber>
                 <NumberOfPieces>" . $consignment->getNumberPieces() . "</NumberOfPieces>
                 <Weight>" . $consignment->getWeight() . "</Weight>
                 <WeightUnit>K</WeightUnit>        

                 <DoorTo>DD</DoorTo>		
                 <DimensionUnit>C</DimensionUnit>
                 <Pieces>
                     <Weight>" . $consignment->getWeight() . "</Weight>
                     <Width>" . (int) $parcellist->getWidth() . "</Width>
                     <Height>" . (int) $parcellist->getHeight() . "</Height>
                     <Depth>" . (int) $parcellist->getLength() . "</Depth>
                 </Pieces>       
             </ShipmentDetails>
         </req:BookPURequest>";

        $url = $constant["DHL_WSDL"]; //"http://xmlpi-ea.dhl.com/XMLShippingServlet";
        $httpPostObj = new HttpCommunication($url);

        if ($httpPostObj->post($requestXml)) {
            // create response object from the xml received from DHL
            $responseXml = $httpPostObj->getResponse();
            $xmlSimple = simplexml_load_string($responseXml);
            $consignment->setApiData(print_r($requestXml, true), print_r($xmlSimple, true), 'collection_booking');
            if ($xmlSimple->Response->Status->ActionStatus == "Error") {
                $message = (isset($xmlSimple->Response->Status->Condition->ConditionCode) ? $xmlSimple->Response->Status->Condition->ConditionCode : '') . " " . (isset($xmlSimple->Response->Status->Condition->ConditionData) ? $xmlSimple->Response->Status->Condition->ConditionData : '');
                $output["STATUS"] = "ERROR";
                $output["MESSAGE"] = $message;
            } else {
                $xmlSimple = (array)$xmlSimple;
                $confirmationNumber = $xmlSimple["ConfirmationNumber"];
                //foreach($confirmationNumber as $c){
                    $consignment->setCollectionConfirmationNo($confirmationNumber);
                //}
                $consignment->save();
                $output["STATUS"] = "SUCCESS";
            }
            return $output;
        }
    }

    private function getMesageId($id, $bookingFlag = true) {
        $code = ($bookingFlag ? "1" : "2");
        $code .= substr("0000000000000000000000000000" . $id, -27);
        return $code;
    }

    private function getXMLNumberOfPieces($consignment, $parcels) {
        $xml = "";
        $numberOfPieces = $consignment->getNumberPieces();
        //$parcel = $parcels[$i];
        //for ($i = 0; $i < $numberOfPieces; $i++) 
        foreach($parcels as $parcel) {
            
            $xml .= '<Piece>
        <Weight>' . (($parcel->getWeight() > 0) ? $parcel->getWeight() : (number_format(($consignment->getWeight() / $numberOfPieces), 2))) . '</Weight>
        <Width>' . (($parcel->getWidth() > 0.999) ? number_format($parcel->getWidth(), 0) : '1') . '</Width>
        <Height>' . (($parcel->getHeight() > 0.999) ? number_format($parcel->getHeight(), 0) : '1') . '</Height>
        <Depth>' . (($parcel->getLength() > 0.999) ? number_format($parcel->getLength(), 0) : '1') . '</Depth>
        </Piece>

        ';
        }
        return $xml;
    }

   // private function utfEncode($data, $format='') {
       /* $fld = "";
        if($format == 'ANSI'){
            $fld = iconv( mb_detect_encoding( $data ), 'Windows-1252//TRANSLIT', $data );
        }
        else{
            $fld = iconv( mb_detect_encoding( $data ), 'utf-8', $data );
        }*/
      //  return $data;
    //}
	
	    private	function utfEncode($str,$encoding = "") {
              $str = preg_replace('/[^(\x20-\x7F)]*/','', $str);
              $str = str_replace('&','and', $str);
              $str = str_replace('<','&lt;', $str);
          $str = str_replace('>','&gt;', $str);
          $str = str_replace("'","", $str);

              if ($str !== "") {
                    if (empty($encoding) && self::isUTF8($str))
                      $encoding = "UTF-8";
                    if (empty($encoding))
                      $encoding = mb_detect_encoding($str,'UTF-8, ISO-8859-1');
                    if (empty($encoding))
                      $encoding = "ISO-8859-1"; //  if charset can't be detected, default to ISO-8859-1
                    return $encoding == "UTF-8" ? $str : @mb_convert_encoding($str,"UTF-8",$encoding);
                    }
              }

   private function isUTF8($str) {
   return preg_match('%^(?:
         [\x09\x0A\x0D\x20-\x7E]           # ASCII
       | [\xC2-\xDF][\x80-\xBF]            # non-overlong 2-byte
       | \xE0[\xA0-\xBF][\x80-\xBF]        # excluding overlongs
       | [\xE1-\xEC\xEE\xEF][\x80-\xBF]{2} # straight 3-byte
       | \xED[\x80-\x9F][\x80-\xBF]        # excluding surrogates
       | \xF0[\x90-\xBF][\x80-\xBF]{2}     # planes 1-3
       | [\xF1-\xF3][\x80-\xBF]{3}         # planes 4-15
       | \xF4[\x80-\x8F][\x80-\xBF]{2}     # plane 16
   )*$%xs', $str);
  }
  
    public function recycledShipment($consignment) {

        $output = array();
        if ($consignment->getShipmentType() == "C") {

            /*
             *  Get Service  constants 
             */
            $serviceAgentConstantFilter = new ServiceConstantValueFilter();
            $serviceAgentConstantFilter->addFilter("service_id = '" . $consignment->getServiceId() . "' AND agent_id = '" . $consignment->getAgentId() . "' ");
            $serviceAgentConstant = $serviceAgentConstantFilter->getConstantList("c.constant 'constant_name', scv.constant_value 'constant_value'");
            if (count($serviceAgentConstant) > 0) {
                foreach ($serviceAgentConstant as $serviceAgentConstantData) {
                    $this->constants[$serviceAgentConstantData->getConstantName()] = $serviceAgentConstantData->getConstantValue();
                }
            }
            if (trim(@$this->constants['DHL_BILLING_ACCOUNT_NUMBER']) == '' || trim(@$this->constants['DHL_SHIPPER_ACCOUNT_NUMEBR']) == '') {
                $output['STATUS'] = 'ERROR';
                $output['MESSAGE'] = "Please check service constants. Please contact to itsupport@oneworldexpress.com";
                return $output;
            }

            $sendercountry = new Country($consignment->getSenderCountryId());
            $region = $sendercountry->getRegionCollection();
            $iso = $sendercountry->getIso();
            
         
            $requestorName = Sessionmanager::getUser()->getUserName();
            $messageTime = Date("Y-m-d") . "T" . Date("H:i:s-01:00");
            $msgRef = $this->getMesageId($consignment->getId(), true);


            if ($region == '') {
                $output['STATUS'] = "ERROR";
                $output['MESSAGE'] = "Cancellation is not possible for this country";
                return $output;
            }
            
            if($this->reschedule){
                $collectionNo = $this->bookingRef;
                $collectionDate = Date('Y-m-d', strtotime($this->collectionDate));
            }
            else
            {
                $collectionNo = $consignment->getCollectionConfirmationNo();
                $collectionDate = Date('Y-m-d', strtotime($consignment->getCollectionDate()));
            }

            $xml = '<?xml version="1.0" encoding="UTF-8"?>
				<req:CancelPURequest xmlns:req="http://www.dhl.com" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" 
				xsi:schemaLocation="http://www.dhl.com cancel-pickup-global-req_EA.xsd" schemaVersion="1.0">
				<Request>
					<ServiceHeader>
						<MessageTime>' . $messageTime . '</MessageTime>
				   		<MessageReference>' . $msgRef . '</MessageReference>
                                                <SiteID>' . @$this->constants['DHL_API_USERNAME'] . '</SiteID>
                                                <Password>' . @$this->constants['DHL_API_PASSWORD'] . '</Password>
					</ServiceHeader>
				</Request>
				<RegionCode>' . $region . '</RegionCode>
				<ConfirmationNumber>' . $collectionNo . '</ConfirmationNumber>
				<RequestorName>' . $requestorName . '</RequestorName>
				<CountryCode>' . $iso . '</CountryCode>
				<PickupDate>' . $collectionDate . '</PickupDate>
				<CancelTime>' . Date('H:i') . '</CancelTime>
				</req:CancelPURequest>';

            if ($collectionNo == '') {
                $output['STATUS'] = "ERROR";
                $output['MESSAGE'] = "No Booking Record Found,Cancellation is not possible";
                return $output;
            }
           

            $url = $this->constants["DHL_WSDL"]; //"http://xmlpi-ea.dhl.com/XMLShippingServlet";
            $httpPostObj = new HttpCommunication($url);

            if ($httpPostObj->post($xml)) {
                // create response object from the xml received from DHL
                $responseXml = $httpPostObj->getResponse();
                
                $xmlSimple = simplexml_load_string($responseXml);
                $consignment->setApiData(print_r($xml, true), print_r($xmlSimple, true), 'collection cancel');

                if ($xmlSimple->Response->Status->ActionStatus == 'Error') {
                    $errormessage = $xmlSimple->Response->Status->Condition->ConditionData;
                    $output['STATUS'] = 'ERROR';
                    $output['MESSAGE'] = $errormessage;
                    return $output;
                } else {
                    $output["STATUS"] = "SUCCESS";
                   return $output;
                }
            }
        }
        else
        {
            $output["STATUS"] = "SUCCESS";
            return $output;
        }
    }

    public function reschedulePickup($consignment, $rescheduleDate="", $rescheduleStartTime="", $rescheduleEndTime=""){
         $output = array();
        if ($consignment->getShipmentType() == "C") {
            
            if($consignment->getAwb() == ""){
                $output['STATUS'] = 'ERROR';
                $output['MESSAGE'] = "Could not find any label. Please create label.";
                return $output;
            }
            
            /*
             *  Get Service  constants 
             */
            $serviceAgentConstantFilter = new ServiceConstantValueFilter();
            $serviceAgentConstantFilter->addFilter("service_id = '" . $consignment->getServiceId() . "' AND agent_id = '" . $consignment->getAgentId() . "' ");
            $serviceAgentConstant = $serviceAgentConstantFilter->getConstantList("c.constant 'constant_name', scv.constant_value 'constant_value'");
            if (count($serviceAgentConstant) > 0) {
                foreach ($serviceAgentConstant as $serviceAgentConstantData) {
                    $this->constants[$serviceAgentConstantData->getConstantName()] = $serviceAgentConstantData->getConstantValue();
                }
            }
            if (trim(@$this->constants['DHL_BILLING_ACCOUNT_NUMBER']) == '' || trim(@$this->constants['DHL_SHIPPER_ACCOUNT_NUMEBR']) == '') {
                $output['STATUS'] = 'ERROR';
                $output['MESSAGE'] = "Please check service constants. Please contact to itsupport@oneworldexpress.com";
                return $output;
            }
            
            $oldCollectionDate = $consignment->getCollectionDate();
            $oldCollectionStartTime = $consignment->getCollectionStartTime();
            $oldCollectionEndTime = $consignment->getCollectionEndTime();
            $oldCollectionConfirmationNo = $consignment->getCollectionConfirmationNo();
            
            $this->setCollectionDetails($consignment->getCollectionConfirmationNo(), $consignment->getCollectionDate());
           
            $consignment->setCollectionDate(date("Y-m-d", strtotime($rescheduleDate)));
            $consignment->setCollectionStartTime(date("H:i",strtotime($rescheduleStartTime)));
            $consignment->setCollectionEndTime(date("H:i",strtotime($rescheduleEndTime)));
            $consignment->save();
            $this->serviceValues = new Services($consignment->getServiceId());
            $this->country = new Country($consignment->getCountryId());
            $this->user = SessionManager::getUser();
            $parcels = $consignment->getParcels();
            $accountNumber = $this->constants['DHL_SHIPPER_ACCOUNT_NUMEBR'];
            /*
             * Rebook Pick up for new time.
             */

            $pickupResponse = $this->sendRequestToDhl($consignment, $parcels, $this->constants, $accountNumber);
            if($pickupResponse["STATUS"] == "SUCCESS"){
                $response = $this->recycledShipment($consignment);
                return $response;
            }
            else
            {
                $consignment->setCollectionDate($oldCollectionDate);
                $consignment->setCollectionStartTime($oldCollectionStartTime);
                $consignment->setCollectionEndTime($oldCollectionEndTime);
                $consignment->save();
                return $pickupResponse;
            }
           
            
            
            /*
             * Modify Pick Up Call work for AP and AM country only
             */
            /*$sendercountry = new Country($consignment->getSenderCountryId());
            $region = $sendercountry->getRegionCollection();
            $iso = $sendercountry->getIso();
            
         
            $requestorName = Sessionmanager::getUser()->getUserName();
            $messageTime = Date("Y-m-d") . "T" . Date("H:i:s-01:00");
            $msgRef = $this->getMesageId($consignment->getId(), true);

            $earliest_time = $consignment->getCollectionStartTime();
            $latest_time = $consignment->getCollectionEndTime();

            $pickupdate = "2020-07-07"; //date("Y-m-d", strtotime($consignment->getCollectionDate()));

           
            $xml = '<?xml version="1.0" encoding="UTF-8"?>
				<req:ModifyPURequest xmlns:req="http://www.dhl.com" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" 
                                xsi:schemaLocation="http://www.dhl.com modify-pickup-global-req_AP.xsd" schemaVersion="3.0">
				<Request>
					<ServiceHeader>
						<MessageTime>' . $messageTime . '</MessageTime>
				   		<MessageReference>' . $msgRef . '</MessageReference>
                                                <SiteID>' . @$this->constants['DHL_API_USERNAME'] . '</SiteID>
                                                <Password>' . @$this->constants['DHL_API_PASSWORD'] . '</Password>
					</ServiceHeader>
                                        <MetaData>
                                                <SoftwareName>XMLPI</SoftwareName>
                                                <SoftwareVersion>1.0</SoftwareVersion>
                                        </MetaData>
				</Request>
				<RegionCode>' . $region . '</RegionCode>
				<ConfirmationNumber>' . $consignment->getCollectionConfirmationNo() . '</ConfirmationNumber>
				<Requestor>
                                            <AccountType>D</AccountType>
                                            <AccountNumber>' . @$this->constants['DHL_SHIPPER_ACCOUNT_NUMEBR'] . '</AccountNumber>		
                                            <RequestorContact>
                                                    <PersonName>'.$consignment->getSenderName().'</PersonName>
                                                    <Phone>'.$consignment->getSenderTelephone().'</Phone>			
                                            </RequestorContact>
                                            <CompanyName>'.$consignment->getSenderCompany().'</CompanyName>
                                            <Address1>'.$consignment->getSenderAddressLine1().' </Address1>		
                                            <City>'.$consignment->getSenderCity().'</City>
                                            <CountryCode>'.$sendercountry->getIso().'</CountryCode>
                                </Requestor>
				<Place>
                                        <LocationType>B</LocationType>
                                        <CompanyName>'. $this->utfEncode($consignment->getSenderCompany()).'</CompanyName>
                                        <Address1>'.$this->utfEncode($consignment->getSenderAddressLine1()).'</Address1>
                                        <Address2>'.$this->utfEncode($consignment->getSenderAddressLine2()).'</Address2>
                                        <Address3>'.$this->utfEncode($consignment->getSenderAddressLine3()).'</Address3>
                                        <PackageLocation>'.($consignment->getRoutingCodeEur() == "" ? "reception" : $consignment->getRoutingCodeEur()).'</PackageLocation>
                                        <City>' . $this->utfEncode($consignment->getSenderCity()) . '</City>
                                        <CountryCode>' . $sendercountry->getIso() . '</CountryCode>
                                        <PostalCode>' . $this->utfEncode($consignment->getSenderPostcode()) . '</PostalCode>
                                </Place>
                                <Pickup>
                                        <PickupDate>'.$pickupdate.'</PickupDate>
                                        <PickupTypeCode>S</PickupTypeCode>
                                        <ReadyByTime>'.$earliest_time.'</ReadyByTime>
                                        <CloseTime>'.$latest_time.'</CloseTime>		
                                </Pickup>
                                <PickupContact>
                                        <PersonName>'.$this->utfEncode($consignment->getSenderName()).'</PersonName>
                                        <Phone>'.$this->utfEncode($consignment->getSenderTelephone()).'</Phone>
                                </PickupContact>
                                <OriginSvcArea></OriginSvcArea>
                        </req:ModifyPURequest>';
            
            

            if ($consignment->getCollectionConfirmationNo() == '') {
                $output['STATUS'] = "ERROR";
                $output['MESSAGE'] = "No Booking Record Found,Reschedule is not possible.";
                return $output;
            }
           

            $url = $this->constants["DHL_WSDL"]; //"http://xmlpi-ea.dhl.com/XMLShippingServlet";
            $httpPostObj = new HttpCommunication($url);

            if ($httpPostObj->post($xml)) {
                // create response object from the xml received from DHL
                $responseXml = $httpPostObj->getResponse();
                
                $xmlSimple = simplexml_load_string($responseXml);
                $consignment->setApiData(print_r($xml, true), print_r($xmlSimple, true), 'pick up reschedule');

                if ($xmlSimple->Response->Status->ActionStatus == 'Error') {
                    $errormessage = $xmlSimple->Response->Status->Condition->ConditionData;
                    $output['STATUS'] = 'ERROR';
                    $output['MESSAGE'] = $errormessage;
                    return $output;
                } else {
                    $output["STATUS"] = "SUCCESS";
                   return $output;
                }
            }*/
        }
    }
    
    private function setCollectionDetails($bookingRef, $collectionDate){
       $this->bookingRef = $bookingRef;
       $this->collectionDate = $collectionDate;
       $this->reschedule = true;
    }


    public function reconciliation_data($headingArr,$carrierId,$relPath, $file2,$new_csv_file_created,$filePath,$batchNumber) {
        $output = [];
        $carrierObj = new Carrier($carrierId);
//        @$csv_file = $file;
//        if (!empty($csv_file['name'])) {
            $serviceCodes = [
                'EXPRESS WORLDWIDE doc' => 'DOX',
                'EXPRESS WORLDWIDE non-doc' => 'WPX',
                'EXPRESS WORLDWIDE eu' => 'ECX',
                'ECONOMY SELECT eu/doc' => 'ESU',
                'EXPRESS DOMESTIC' => 'DOM',
            ];
//            $file_name = $csv_file['name'];
//            $path_parts = pathinfo($file_name);
//            $ext = strtolower($path_parts['extension']);
//            $basename = $path_parts['basename'];
//            if ($ext == 'csv') {
                $carrierName = str_replace(" ","_",$carrierObj->getCarrier());
                $user = SessionManager::getUser();
//                $userId = $user->getId();
//                $batchNumber = $userId . "_" . time();
//                $newFileName = strtolower($carrierName)."_" . $batchNumber . "." . $ext;
//                $dateFolder = date('Y-m-d');
//                $output['date_folder'] = $dateFolder;
//
//                $filePath = RECONCILIATION_PATH. '/' . $carrierName.'/'.$dateFolder;
//                $relPath = RECONCILIATION_PATH.$carrierName.'/'.$dateFolder.'/'.$newFileName;
//                $output['upload_file'] = $newFileName;
//                if (!file_exists(RECONCILIATION_PATH)) {
//                    @mkdir(RECONCILIATION_PATH, 0775);
//                }
//                if (!file_exists(RECONCILIATION_PATH.$carrierName.'/')) {
//                    @mkdir(RECONCILIATION_PATH.$carrierName.'/', 0775);
//                }
//                if (!file_exists(RECONCILIATION_PATH.$carrierName.'/'.$dateFolder.'/')) {
//                    @mkdir(RECONCILIATION_PATH.$carrierName.'/'.$dateFolder.'/', 0775);
//                }
//                /* create new csv file and write data on it*/
//                $new_csv_file_name = strtolower($carrierName) . "_new_" . $batchNumber . ".csv";
//                $new_csv_file_created = RECONCILIATION_PATH.$carrierName.'/'.$dateFolder.'/'.$new_csv_file_name;
//                $new_csv_file_save = RECONCILIATION_URL.$carrierName.'/'.$dateFolder.'/'.$new_csv_file_name;

                $comaSeptHeading = implode(",",$headingArr);
                $tableColumn = rtrim($comaSeptHeading,',');
                $csvStr = $tableColumn;
                $csvStr .= "\r\n";
                $templateCheck = 1;
                $checkExist = 0;
                $output['new_invoice_save'] = 0;
                $output['total_weight'] = 0;
                $output['total_pieces'] = 0;
                $output['total_amount'] = 0;
//                if (move_uploaded_file($csv_file['tmp_name'], $relPath)) {
                    $row = 1;
                    $dataArr = [];
                    if (($handle = fopen($relPath, "r")) !== FALSE) {
                        $fuelChargePercentage = 0.00;
                        $invoiceNumber = '';
                        while (($data = fgetcsv($handle)) !== FALSE) {

                            if($row > 2) {
                                $account_number = '';
                                $invoice_number = $data['3'];
                                $collection_date = $data['7'];
                                $agent_reference_number = '';
                                $delivery_country = $data['44'];
                                $mawb = '';
                                $awb = $data['23'];
                                if(!empty($data['27'])) {
                                    $hawb = $data['27'];
                                } else if(!empty($data['28'])) {
                                    $hawb = $data['28'];
                                } else if(!empty($data['29'])) {
                                    $hawb = $data['29'];
                                } else {
                                    $hawb = ''; //important
                                }

                                $service_name = $data['31'];
                                $service_code = $serviceCodes[trim($data['31'])]; //important
                                $weight = $data['68'];
                                $vol_weight = '';
                                $length = '';
                                $width = '';
                                $height = '';
                                $number_of_pieces = $data['32'];
                                $basic_charges = $data['71'] - $data['92'];
                                $fuel_charges = $data['92'];
                                $vat = $data['71'] - $data['70'];
                                $total_amount =  $data['71'];
                                $notes = '';

                                $output['invoice_number'] = $invoiceNumber;
//                                $supplierInvoiceFilter = new SupplierInvoicesFilter();
//                                $supplierInvoiceFilter->where(['si.invoice_number' => $invoice_number]);
//                                $supplierInvoiceFilterObjs = $supplierInvoiceFilter->getList();
//                                if(count($supplierInvoiceFilterObjs)) {
//                                    $output['status'] = 'error';
//                                    $output['message'] = 'This file is already processed';
//                                    $output['new_invoice_save'] = 1;
//                                    break;
//                                }
                                $output['invoice_date'] = $collection_date;
                                $length = "0.00";
                                $height = "0.00";
                                $width = "0.00";
                                $volWeight = "0.000";

                                $weight = formatNumber($weight, 3);

                                $dt = [
                                    'account_number' => $account_number,
                                    'invoice_number' => $invoice_number,
                                    'agent_reference_number' => $agent_reference_number,
                                    'collection_date' => $collection_date,
                                    'delivery_country' => $delivery_country,
                                    'mawb' => $mawb,
                                    'awb' => $awb,
                                    'hawb' => $hawb,
                                    'service_name' => $service_name,
                                    'service_code' => $service_code,
                                    'weight' => $weight,
                                    'vol_weight' => $volWeight,
                                    'length' => $length,
                                    'width' => $width,
                                    'height' => $height,
                                    'number_of_pieces' => $number_of_pieces,
                                    'basic_charges' => $basic_charges,
                                    'fuel_charges' => $fuel_charges,
                                    'vat' => $vat,
                                    'total_amount' => $total_amount,
                                    'notes' => $notes,
                                ];

                                $output['total_weight'] += $weight;
                                $output['total_pieces'] += $number_of_pieces;
                                $output['total_amount'] += $total_amount;
                                $dataArr[] = $dt;
                                $comaSept = implode(",", $dt);
                                $csvStr .= rtrim($comaSept, ',');
                                $csvStr .= "\r\n";
                            }
                            $row++;
                        }
                    } else {
                        $output['status'] = 'error';
                        $output['message'] = 'File can not open please check permission';
                    }
//                } else {
//                    $output['status'] = 'error';
//                    $output['message'] = 'File upload error';
//                }
                if($output['status'] != "error" && !$output['return']) {
                    $myCsvFile = fopen($new_csv_file_created, "a") or die("Unable to open file!");
                    fwrite($myCsvFile, $csvStr);
                    fclose($myCsvFile);

                    $dateNow = date('Y-m-d H:i:s');
                    $load_data_sql = "LOAD DATA LOCAL INFILE '" . $new_csv_file_created . "' INTO TABLE `reconciliation_data`
                            FIELDS ENCLOSED BY '\"' 
                            TERMINATED BY ',' LINES TERMINATED BY '\n' IGNORE 1 LINES (
                                " . $tableColumn . "
                            ) 
                            SET  created_at='" . $dateNow . "', batch_number= '" . $batchNumber . "'";

                    $res = DbAccess3::runQueryWithError($load_data_sql);
                    if ($res === false) {
                        $error = DbAccess3::$dbError;
                        $output['status'] = 'error';
                        $output['message'] = $error[0];
                    } else {
                        $sql = "SELECT * FROM reconciliation_data WHERE batch_number='" . $batchNumber . "' ";
                        $resultSql = DbAccess3::runQuery($sql);
                        $res = [];
                        while ($obj = mysqli_fetch_object($resultSql)) {
                            $res[] = $obj;
                        }
                        $output['status'] = 'success';
                        $output['file_path'] = $filePath;
                        $output['batch_number'] = $batchNumber;
                        $output['invoice_number'] = $invoice_number;
                        $output['template'] = $templateCheck;
                        $output['data'] = $res;
                    }
                }
//            } else {
//                $output['status'] = 'error';
//                $output['message'] = 'Please select valid csv file';
//            }
//        } else {
//            $output['status'] = 'error';
//            $output['message'] = 'Please select valid csv file';
//        }
        return $output;
    }
    public function getSummeryData($relPath){
        $returnArr = [];
        $row = 1;
        $isInvoiceType = false;
        $isDataSet = true;
        if (($handle = fopen($relPath, "r")) !== FALSE) {
            while (($data = fgetcsv($handle)) !== FALSE) {
                if($row > 2) {
                    $returnArr['collection_date'] = $data['7'];
                    $returnArr['invoice_number'] = $data['3'];
                    $isInvoiceType = true;
                }
                if($isInvoiceType){
                    break;
                }
                $row++;
            }
        }
        return $returnArr;
    }
}
