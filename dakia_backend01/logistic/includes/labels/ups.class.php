<?php

include_classes([
    'pdfmerger'
        ], 'labels');

class UPS implements CarrierService {

    private $pdf;
    private $serviceValues = null;
    private $agentValues = null;
    private $constants = null;
    private $user = null;
    private $country = null;
    private $url = null;
    private $login = null;
    private $password = null;

    public function __construct() {
        
    }

    public function validation(Consignment $consignment, Services $service, Country $country) {
        $returnOutput = array();

        if (trim($consignment->getTelephone()) == "") {
            $returnOutput[] = "Please enter Telephone No.";
        }
        if (trim($consignment->getEmail()) == "") {
            $returnOutput[] = "Please enter Email Address.";
        }

        return $returnOutput;
    }

    public function remoteareas($consignment, $carrierObject, $sender) {
        
    }

    public function label($consignment, $labelType = 'pdf', $size = '') {

        $output = array();
        $this->serviceValues = new Services($consignment->getServiceId());
        $this->user = SessionManager::getUser();
        $this->country = new Country($consignment->getCountryId());

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
        if (trim(@$this->constants['UPS_ACCESSCODE']) == '' || trim(@$this->constants['UPS_SHIPPER_NAME']) == '' || trim(@$this->constants['UPS_SHIPPER_ATTENTIONNAME']) == '' || trim(@$this->constants['UPS_SHIPPER_NUMBER']) == '' || trim(@$this->constants['UPS_SHIPPER_ADDRESS']) == '' || trim(@$this->constants['UPS_SHIPPER_CITY']) == '' || trim(@$this->constants['UPS_SHIPPER_POSTCODE']) == '' || trim(@$this->constants['UPS_SHIPPER_COUNTRY']) == '' || trim(@$this->constants['UPS_USERNAME']) == '' || trim(@$this->constants['UPS_PASSWORD']) == '' || trim(@$this->constants['UPS_NAMESPACE']) == '') {
            $output['STATUS'] = 'ERROR';
            $output['MESSAGE'] = "Please check service constants. Please contact to itsupport@oneworldexpress.com";
            return $output;
        }

        $parcel_list = $consignment->getParcels();
        $parcel_idx = 0;
        $pdf = new PdfBase(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $this->pdf = $pdf;
        // Generate label for each parecel
        $output = $this->addWayBill($consignment, $this->constants, $parcel_list);
        return $output;
    }

    private function addWayBill(Consignment $consignment, $constants, $parcel_list) {
        $output = array();
        ini_set('default_socket_timeout', 3600);
        ini_set('soap.wsdl_cache_enabled', 0);
        ini_set('soap.wsdl_cache_ttl', 0);
        //Configuration
        //$access = "8CD3E84AD9E4B84A";

        $access = $this->constants['UPS_ACCESSCODE']; //"BD91C655E8DF8BD0";////"9D0634F2D2DF59E4";
        $userid = $this->constants["UPS_USERNAME"];  //"oneworldexpress";////"Kaab1";
        $passwd = $this->constants["UPS_PASSWORD"]; //"Cybernet123@";//"Abc12345";



        $wsdl = SETTING_DIR_REMOTE."includes/labels/upsconfig/Ship.wsdl";
        $operation = "ProcessShipment";
        //	test shipment url
        //	$endpointurl = 'https://wwwcie.ups.com/webservices/Ship'; 

        $endpointurl = $this->constants["UPS_NAMESPACE"]; //'https://onlinetools.ups.com/webservices/Ship';

        $mode = array
            (
            'soap_version' => 'SOAP_1_1', // use soap 1.1 client
            'trace' => 1
        );

        // initialize soap client
        $client = new SoapClient($wsdl, $mode);
        //set endpoint url
        $client->__setLocation($endpointurl);

        try {
            //create soap header
            $usernameToken['Username'] = trim($userid);
            $usernameToken['Password'] = trim($passwd);
            $serviceAccessLicense['AccessLicenseNumber'] = trim($access);
            $upss['UsernameToken'] = $usernameToken;
            $upss['ServiceAccessToken'] = $serviceAccessLicense;
            $header = new SoapHeader('http://www.ups.com/XMLSchema/XOLTWS/UPSS/v1.0', 'UPSSecurity', $upss);

            $client->__setSoapHeaders($header);

            //if (strcmp($operation, "ProcessShipment") == 0) 
            {

                $requestString = array($this->processShipment($consignment));
                //get response
                
                $resp = $client->__soapCall('ProcessShipment', $requestString);

                $consignment->setApiData(print_r($requestString, true), print_r($resp, true), 'ProcessShipment');
                if (strtolower($resp->Response->ResponseStatus->Description) == "success") {
                    $trackingNumber = $resp->ShipmentResults->PackageResults->TrackingNumber;
                    $licence_plate_array[] = $trackingNumber;
                    $labelImage = base64_decode($resp->ShipmentResults->PackageResults->ShippingLabel->GraphicImage);
                    $internationForm = base64_decode($resp->ShipmentResults->Form->Image->GraphicImage);
                    $uniqueFileName = uniqid();
                    $fileName = '../_assets/pdf/' . date('Y_m_d') . '/' . $uniqueFileName . ".GIF";
                    file_put_contents($fileName, $labelImage);

                    $this->pdf->SetPrintFooter(false);
                    $this->pdf->SetFooterMargin(0);
                    $this->pdf->SetAutoPageBreak(false, 0);
                    $page_size = array(150, 100);
                    $this->pdf->AddPage("P", $page_size);
                    $this->pdf->StartTransform();
                    $this->pdf->Rotate(-90, 160, 90);
                    $this->pdf->Image($fileName, 70, 150, 175, 100, '', '', '', false, 300, '', false, false, 0, false, false, false, false, array());
                    $this->pdf->StopTransform();
                    $labelPdf = "../_assets/pdf/" . date('Y_m_d') . '/' . $consignment->getId() . ".pdf";
                    $this->pdf->Output($labelPdf, "F");

                    if ($internationForm != '') {
                        $uniqueFileNameForm = uniqid();
                        $formFileName = '../_assets/pdf/' . date('Y_m_d') . '/' . $uniqueFileNameForm . ".pdf";
                        $pdfarchive = fopen($formFileName, 'w');
                        fwrite($pdfarchive, $internationForm);
                        fclose($pdfarchive);
                        $PDFMerger = new PDFMerger();
                        $PDFMerger->addPDF($labelPdf);
                        $PDFMerger->addPDF($formFileName);
                        try {
                            $PDFMerger->merge('file', $labelPdf);
                        } catch (Exception $e) {
                            echo 'Caught exception: ', $e->getMessage(), "\n";
                        }
                    }
                    $parcel_list[0]->setTrackingNumber($trackingNumber);
                    $parcel_list[0]->save();

                    $output['STATUS'] = 'SUCCESS';
                    $output['LABEL'] = date('Y_m_d') . '/' . $consignment->getId() . ".pdf";
                    $output['TRACKING_NUMBER'] = $licence_plate_array;
                } else {
                    print_r($resp);
                    $output["STATUS"] = "ERROR";
                    $output["MESSAGE"] = ($resp->Response->ResponseStatus->Description != "") ? $resp->Response->ResponseStatus->Description : "Unable to create label. Please try again.";
                }
                return $output;
            }
           
        } catch (Exception $ex) {
            mail("mruga@oneworldexpress.com","UPS BOX ERROR",print_r($ex, true));
			mail("irshadali18@gmail.com","UPS BOX ERROR",print_r($ex->getTraceAsString(), true));
			mail("shabbir@oneworldexpress.com","UPS BOX ERROR",print_r($ex->getTraceAsString(), true));
            preg_match('/<err:Description>(.*?)err:Description>/', $client->__getLastResponse(), $display);
//return $display[1];
            $output['STATUS'] = 'ERROR';
            $output['MESSAGE'] = substr($display[1], 0, -3);
            return $output;
        }
    }

    private function processShipment($consignment) {

        $shipperCountryCode = @$this->constants['UPS_SHIPPER_COUNTRY'];
        if (is_numeric($shipperCountryCode)) {
            $countryObj = new Country($shipperCountryCode);
            $shipperIso = $countryObj->getIso();
        } else {
            $shipperIso = $shipperCountryCode;
        }

        if ($this->serviceValues->getCode() == "STUPSDROP") {
            $company = @$this->constants['UPS_SHIPPER_NAME'];
            $contact = @$this->constants['UPS_SHIPPER_NAME'];
            $address1 = @$this->constants['UPS_SHIPPER_ADDRESS'];
            $city = @$this->constants['UPS_SHIPPER_CITY'];
            $postcode = @$this->constants['UPS_SHIPPER_POSTCODE'];
            $telephone = @$this->constants['UPS_SHIPPER_PHONE'];
            $state = "";
            $isocode = $shipperIso;
        } else {

            if ($consignment->getCompany() != "")
                $company = $consignment->getCompany();
            else {
                $company = $consignment->getContact();
            }
            if ($consignment->getAddressLine1() != "") {
                $address1 = $consignment->getAddressLine1();
            }
            if ($consignment->getAddressLine2() != "") {
                $address2 = $consignment->getAddressLine2();
            }
            if ($consignment->getAddressLine3() != "") {
                $address3 = $consignment->getAddressLine3();
            }

            $city = $consignment->getCity();

            $contact = $consignment->getContact();
            $telephone = $consignment->getTelephone();


            $postcode = $consignment->getPostcode();
            $isocode = $this->country->getIso();
            $state = $consignment->getState();
        }
        $description = $consignment->getDescription();
        $weight = $consignment->getWeight();
        //create soap request
        $requestoption['RequestOption'] = 'nonvalidate';
        $requestoption['SubVersion'] = '1807';
        $request['Request'] = $requestoption;

        $shipment['Description'] = $description;

        $shipper['Name'] = $consignment->getSenderName();//@$this->constants['UPS_SHIPPER_NAME']; //'"One World Express Inc Ltd";//KAAB';
        $shipper['AttentionName'] = $consignment->getSenderName();//@$this->constants['UPS_SHIPPER_ATTENTIONNAME']; //"One World Express Inc Ltd";// 'KAAB';
        //$shipper['TaxIdentificationNumber'] = '';
        // $shipper['ShipperNumber'] = '237V9E';
        $phone['Number'] = ($consignment->getSenderTelephone() == "" ? "02088676060" : $consignment->getSenderTelephone());//@$this->constants['UPS_SHIPPER_PHONE']; //"02088676060";//'0048774593339';
        $shipper['Phone'] = $phone;
        $shipper['ShipperNumber'] = @$this->constants['UPS_SHIPPER_NUMBER']; //"3985RW";//'R2E500';

        $address['AddressLine'] = $consignment->getSenderAddressLine1();//@$this->constants['UPS_SHIPPER_ADDRESS']; //"UPS West London";//'GRODKOWSKA 40';
        $address['City'] = $consignment->getSenderCity();//@$this->constants['UPS_SHIPPER_CITY']; //"FELTHAM";//'NYSA';
        $address['StateProvinceCode'] = "";//@$this->constants['UPS_SHIPPER_STATE']; //"";//'MD';
        $address['PostalCode'] = $consignment->getSenderPostcode();//@$this->constants['UPS_SHIPPER_POSTCODE']; //"TW140UU";//'48300';

        $senderCountryId = $consignment->getSenderCountryId();
        $senderCountry = new Country($senderCountryId);
        $address['CountryCode'] = $senderCountry->getIso();//$shipperIso; //'PL';
        $shipper['Address'] = $address;
        $shipment['Shipper'] = $shipper;

        $shipto['Name'] = $company;
        $shipto['AttentionName'] = $contact;
        $phone2['Number'] = $telephone;
        $shipto['Phone'] = $phone2;
        $shipto['EMailAddress'] = $consignment->getEmail();
        $addressTo['AddressLine'][0] = $address1 . " " . $address2 . " " . $address3;
        //$addressTo['AddressLine'][1] = $address2;
        //$addressTo['AddressLine'][2] = $address3;
        $addressTo['City'] = $city;
        $addressTo['StateProvinceCode'] = $state;
        $addressTo['PostalCode'] = $postcode;
        $addressTo['CountryCode'] = $isocode;
        $shipto['Address'] = $addressTo;
        if(!empty($consignment->getIossNumber())){
            $vendorInfo["VendorCollectIDNumber"] = $consignment->getIossNumber();
            $vendorInfo["VendorCollectIDTypeCode"] = "0356"; //0356 - IOSS
            $shipto['VendorInfo'] = $vendorInfo;
        }
        $shipment['ShipTo'] = $shipto;

        /*
          $shipfrom['Name'] = "One World Express Inc Ltd";//@$this->constants['UPS_SHIPPER_NAME']; //'KAAB';
          $shipfrom['AttentionName'] = "Gordon Shuttleworth";//@$this->constants['UPS_SHIPPER_ATTENTIONNAME']; //'KAAB';
          $addressFrom['AddressLine'] = "UPS West London";//@$this->constants['UPS_SHIPPER_ADDRESS']; //'GRODKOWSKA 40';
          $addressFrom['City'] = "FELTHAM";//@$this->constants['UPS_SHIPPER_CITY']; //'NYSA';
          $addressFrom['StateProvinceCode'] = "";//@$this->constants['UPS_SHIPPER_STATECODE']; //'MD';
          $addressFrom['PostalCode'] = "TW14 0UU"; //@$this->constants['UPS_SHIPPER_POSTCODE']; //'48300';
          $shipperCountryCode = "GB";//@$this->constants['UPS_SHIPPER_COUNTRY'];
          $addressFrom['CountryCode'] = $shipperIso; //'PL';
          $phone3['Number'] = "02088676060";// @$this->constants['UPS_SHIPPER_PHONE']; //'0048774593339';
          $shipfrom['Address'] = $addressFrom;
          $shipfrom['Phone'] = $phone3; */
        // $shipment['ShipFrom'] = $shipfrom;

        $billshipper['AccountNumber'] = $this->constants['UPS_SHIPPER_NUMBER']; //"3985RW";//'R2E500';

        $shipmentcharge['Type'] = '01';
        $shipmentcharge['BillShipper'] = $billshipper;
        $paymentinformation['ShipmentCharge'] = $shipmentcharge;
        $shipment['PaymentInformation'] = $paymentinformation;

        $ReferenceNumber['Value'] = $consignment->getHawb();
        $shipment['ReferenceNumber'] = $ReferenceNumber;

        $service['Code'] = $this->serviceValues->getCarrierServiceCode(); //'11';
        $service['Description'] = '';
        $shipment['Service'] = $service;

        $soldTo['Name'] = $company;
        $soldTo['AttentionName'] = $contact;
        $soldTo['TaxIdentificationNumber'] = $consignment->getEoriNumber();
        $phone3['Number'] = $consignment->getTelephone();
        $soldTo['Phone'] = $phone3;
        $soldTo['EMailAddress'] = $consignment->getEmail();
        $addressFrom1['AddressLine'] = $address1 . " " . $address2 . " " . $address3;
        $addressFrom1['City'] = $city;
        $addressFrom1['StateProvinceCode'] = $state;
        $addressFrom1['PostalCode'] = $postcode;
        $addressFrom1['CountryCode'] = $isocode;
        $soldTo['Address'] = $addressFrom1;
        $soldToContact['SoldTo'] = $soldTo;


        $customData['FormType'] = "01"; // 01 Commercial
        $customData['Contacts'] = $soldToContact;

        $parcel_list = $consignment->getParcels();
        if (count($parcel_list) > 0) {
            foreach ($parcel_list as $parcel) {
                $parcelDescription = json_decode($parcel->getDescription());
                $parcelCountry = json_decode($parcel->getCommodityCode());
                $parcelQty = json_decode($parcel->getQty());
                $parcelValue = json_decode($parcel->getItemValue());
                $parcelHscode = json_decode($parcel->getHsCode());
                $parcelSku = json_decode($parcel->getItemSku());
                $parcelWeight = json_decode($parcel->getPWeight());
                $count = count($parcelDescription);
                if (count($parcelDescription) > 0) {
                    foreach ($parcelDescription as $key => $desc) {
                        $itemData['Description'] = $desc;
                        $itemDetail['Number'] = $parcelQty[$key];

                        $unit2['Code'] = 'PCS';
                        $unit2['Description'] = 'PCS';
                        $itemDetail['UnitOfMeasurement'] = $unit2;
                        $itemDetail['Value'] = $parcelValue[$key];
                        $itemData['Unit'] = $itemDetail;

                        $weight['Code'] = 'KGS';
                        $weight['Description'] = 'Kilograms';
                        $weightDetail['UnitOfMeasurement'] = $weight;
                        $weightDetail['Weight'] = number_format($parcelWeight[$key], 1);
                        $itemData['ProductWeight'] = $weightDetail;

                        $itemData['CommodityCode'] = $parcelHscode[$key];
                        $itemData['OriginCountryCode'] = $parcelCountry[$key];
                        $itemData['ProductCurrencyCode'] = $consignment->getCurrency();
                        $customData['Product'][] = $itemData;
                    }
                } else {
                    $senderCountry = new Country($consignment->getSenderCountryId());
                    $itemData['Description'] = $consignment->getDescription();
                    $itemDetail['Number'] = $consignment->getNumberPieces();

                    $unit2['Code'] = 'KGS';
                    $unit2['Description'] = 'Kilograms';
                    $itemDetail['UnitOfMeasurement'] = $unit2;
                    $itemDetail['Value'] = $consignment->getValue();
                    $itemData['Unit'] = $itemDetail;

                    $weight['Code'] = 'KGS';
                    $weight['Description'] = 'Kilograms';
                    $weightDetail['UnitOfMeasurement'] = number_format($weight, 1);
                    $weightDetail['Weight'] = $weight;
                    $itemData['ProductWeight'] = $weightDetail;

                    $itemData['CommodityCode'] = '';
                    $itemData['OriginCountryCode'] = $senderCountry->getIso();
                    $itemData['ProductCurrencyCode'] = $consignment->getCurrency();
                    $customData['Product'][] = $itemData;
                }
            }
        }
        $customData['InvoiceNumber'] = $consignment->getId();
        $customData['InvoiceDate'] = date("Ymd");
        $customData['PurchaseOrderNumber'] = "";
        if ($this->serviceValues->getCode() == "STUPS0DDP") {
            $customData['TermsOfShipment'] = "DDP";
        } else {
            $customData['TermsOfShipment'] = "DDU";
        }

        $customData['ReasonForExport'] = "SALE";
        $customData['DeclarationStatement'] = "I hereby certify that the information on this invoice is true and correct and the contents and value of this shipment is as stated above."; // 01 Commercial

        $customData['CurrencyCode'] = $consignment->getCurrency();

        $customInfo['InternationalForms'] = $customData;
        $shipment['ShipmentServiceOptions'] = $customInfo;


        $package['Description'] = $consignment->getDescription();
        $packaging['Code'] = '02';
        $packaging['Description'] = $consignment->getDescription();
        $package['Packaging'] = $packaging;
        $unit2['Code'] = 'KGS';
        $unit2['Description'] = 'Kilograms';
        $packageweight['UnitOfMeasurement'] = $unit2;
        $packageweight['Weight'] = $consignment->getWeight();
        $package['PackageWeight'] = $packageweight;
        /* $unit['Code'] = 'CM';
          $unit['Description'] = 'Centemeters';
          $dimensions['UnitOfMeasurement'] = $unit;
          $dimensions['Length'] = "1";
          $dimensions['Width'] = "1";
          $dimensions['Height'] = "1";
          $package['Dimensions'] = $dimensions;
         */
        $shipment['Package'] = $package;


        $labelimageformat['Code'] = 'GIF';
        $labelimageformat['Description'] = 'GIF';
        $labelspecification['LabelImageFormat'] = $labelimageformat;
        $labelspecification['HTTPUserAgent'] = 'Mozilla/4.5';
        $shipment['LabelSpecification'] = $labelspecification;
        $request['Shipment'] = $shipment;
        //  echo "Request.......\n";
        //print_r($request);
        //   echo "\n\n";


        return $request;
    }

    public function tracking($trackingNumber, $trackBy = 'parcel', $EDI = false) {
        include_once(BASE_PATH . "includes/labels/upstrackingstatus.class.php");
        $tracking = new Tracking();
        $TrackingArray = array(
            "UPSSecurity" => array(
                "Usernametoken" => array
                    (
                    "Username" => "OneWorldDispatch",
                    "Password" => "Send123"
                )
                ,
                "ServiceAccessToken" => array(
                    "AccessLicenseNumber" => "0D5C7B4AE5AED038"
                )
            ),
            "TrackRequest" => array(
                "Request" => array(
                    "RequestOption" => "1",
                    "TransactionReference" => array(
                        "CustomerContext" => "Your Test Case Summary Description"
                    )
                ),
                "InquiryNumber" => $trackingNumber
            )
        );

        $url = "https://onlinetools.ups.com/rest/Track";
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($TrackingArray));

        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($ch);

        $entityId = 0;
        if ($trackBy == 'parcel') {
            $parcelObj = new ParcelFilter();
            $parcelObj->addTrackingNumberFilter($trackingNumber);
            $parcelDataArray = $parcelObj->getColumnList('p.id, p.tracking_number, p.consignment_id');

            if (count($parcelDataArray) > 0) {
                $parcelData = $parcelDataArray[0];
                $entityId = $parcelData->getId();
            }
        } else if ($trackBy == 'shipment') {
            $shipmenObj = new ConsignmentFilter();
            $shipmenObj->addawbFilter($trackingNumber);
            $shipmentDataArray = $shipmenObj->getColumnList('c.awb');
            if (count($shipmentDataArray) > 0) {
                $shipmentData = $shipmentDataArray[0];
                $entityId = $shipmentData->getId();
            }
        }

        $trackingResponse = json_decode($response, true);
        $trackingArrayOriginalTmp = $trackingResponse['TrackResponse']['Shipment']['Package']['Activity'];
        if (isset($trackingArrayOriginalTmp[0]))
            $trackingArrayOriginal = $trackingArrayOriginalTmp;
        else
            $trackingArrayOriginal[0] = $trackingArrayOriginalTmp;
        $trackingArray = array_reverse($trackingArrayOriginal);
        $totalTrackPoints = count($trackingArray[0]['ActivityLocation']);

        ////////////////////// Carrier Received ////////////////////////////////

        $trackingDataFilterObj = new TrackingDataFilter();
        $trackingDataFilterObj->addFieldFilter('    tracking_number', $trackingNumber);
        $trackingDataFilterObj->addFilter("carrier_code not in ('','MP')");
        $trackingEvents = $trackingDataFilterObj->getList();

        if (count($trackingEvents) > 0) {
            $carrierReceivedCheck = 0;   // there is already carrier received event                
            $trackingDataFilterObj = new TrackingDataFilter();
            $trackingDataFilterObj->addFieldFilter('    tracking_number', $trackingNumber);
            $trackingDataFilterObj->addFilter("status_code_id = '148'");
            $CarrierReceivedObj = $trackingDataFilterObj->getList();
            if (count($CarrierReceivedObj) > 0) {
                $carrierCodeCarrierReceived = $CarrierReceivedObj[0]->getCarrierCode();
                $carrierReceivedStatusCode = $CarrierReceivedObj[0]->getStatusCodeId();
            }
        } else {
            $carrierReceivedCheck = 1; // No carrier received Event
        }
        ////////////////////// Carrier Received ////////////////////////////////

        if ($entityId > 0) {
            $parcelEntity = new Parcel($entityId);
            $finalStatusCode = $parcelEntity->getParcelStatusCode();

            foreach ($trackingArray as $event) {
                // print_r($event); die;
                $countryCode = $event['ActivityLocation']['Address']['CountryCode'];
                $ServiceAreaDescription = ucwords(strtolower($event['ActivityLocation']['Address']['City'])) . $countryCode;
                $EventDescription = ucwords(utf8_encode($event['Status']['Description']));
                $EventCode = $event['Status']['Code'];
                $Signatory = $event['ActivityLocation']['SignedForByName'];
                if(strtoupper($EventCode) == "ZO"){
                    $EventDescription = str_replace("â¢", "", $EventDescription);
                }
                
                $dateArray = $event['Date'];
                $timeArray = $event['Time'];
                $date = $dateArray[0] . $dateArray[1] . $dateArray[2] . $dateArray[3] . "-" . $dateArray[4] . $dateArray[5] . "-" . $dateArray[6] . $dateArray[7];
                $time = $timeArray[0] . $timeArray[1] . ":" . $timeArray[2] . $timeArray[3] . ":" . $timeArray[4] . $timeArray[5];
                $DateTime = $date . " " . $time;

                // Dont enter any other event code if it is against 148 Event Code
                if ($carrierCodeCarrierReceived == $EventCode) {
                    continue;
                }
                //$spTrackingStatus = UPSTrackingStatus::getOweStatusCode($EventCode);   
                $deliveredArray = array('KB');

                ////////////////////// Carrier Received ////////////////////////////////
                if ($carrierReceivedCheck == 1 && $EventCode != 'MP') {
                    $spTrackingStatus = '148';
                    $carrierReceivedCheck = 0;
                } else {
                    $spTrackingStatus = UPSTrackingStatus::getOweStatusCode($EventCode);
                }
                ////////////////////// Carrier Received ////////////////////////////////

                $result = $tracking->saveTrackPoint($entityId, $trackBy, $DateTime, $trackingNumber, $spTrackingStatus, $ServiceAreaDescription, $EventCode, $EventDescription, $deliveredArray, $finalStatusCode);
                if ($result == true) {
                    break;
                }
            }
            $tracking->saveConsignmentTrackingStatus($trackingNumber, 'UPSTrackingStatus');
        }
    }

    public function sendData($tracking_numbers = array()) {
        
    }

    public function manifest($consignment) {
        
    }

    public function preAdvice($consignment) {
        
    }

    public function getDropOffLocation($postcode, $iso = '', $city = '') {

        $output = array();
//        $this->serviceValues = new Services($consignment->getServiceId());
//        $this->user = SessionManager::getUser();
//        $this->country = new Country($consignment->getCountryId());
//
//        /*
//         *  Get Service  constants 
//         */
//        $serviceAgentConstantFilter = new ServiceConstantValueFilter();
//        $serviceAgentConstantFilter->addFilter("service_id = '" . $consignment->getServiceId() . "' AND agent_id = '" . $consignment->getAgentId() . "' ");
//        $serviceAgentConstant = $serviceAgentConstantFilter->getConstantList("c.constant 'constant_name', scv.constant_value 'constant_value'");
//        if (count($serviceAgentConstant) > 0) {
//            foreach ($serviceAgentConstant as $serviceAgentConstantData) {
//                $this->constants[$serviceAgentConstantData->getConstantName()] = $serviceAgentConstantData->getConstantValue();
//            }
//        }
//        if (trim(@$this->constants['UPS_ACCESSCODE']) == '' || trim(@$this->constants['UPS_SHIPPER_NAME']) == '' || trim(@$this->constants['UPS_SHIPPER_ATTENTIONNAME']) == '' || trim(@$this->constants['UPS_SHIPPER_NUMBER']) == '' || trim(@$this->constants['UPS_SHIPPER_ADDRESS']) == '' || trim(@$this->constants['UPS_SHIPPER_CITY']) == '' || trim(@$this->constants['UPS_SHIPPER_POSTCODE']) == '' || trim(@$this->constants['UPS_SHIPPER_COUNTRY']) == '' || trim(@$this->constants['UPS_USERNAME']) == '' || trim(@$this->constants['UPS_PASSWORD']) == '' || trim(@$this->constants['UPS_NAMESPACE']) == '') {
//            $output['STATUS'] = 'ERROR';
//            $output['MESSAGE'] = "Please check service constants. Please contact to itsupport@oneworldexpress.com";
//            return $output;
//        }
//        $addressLine1 = $consignment->getAddressLine1();
//        $addressLine2 = $consignment->getAddressLine2();
//        $addressLine3 = $consignment->getAddressLine3();
//        $postCode = $consignment->getPostcode();
//        $countryCode = $this->country->getIso();

        $accessRequestArray = array(
            'AccessLicenseNumber' => '0D5C7B4AE5AED038', //$this->constants['UPS_ACCESSCODE'], 
            'UserId' => 'OneWorldDispatch', //$this->constants['UPS_USERNAME'],
            'Password' => 'Send123'//$this->constants['UPS_PASSWORD']
        );




        $locatorRequest = array(
            'Request' => array
                (
                'RequestAction' => 'Locator',
                'RequestOption' => '1',
                'TransactionReference' => array(
                    'CustomerContext' => 'XOLT Sample Code'
                )
            ),
            'OriginAddress' => array(
                'PhoneNumber' => '02088676060',
                'AddressKeyFormat' => array(
                   // 'AddressLine' => "",
                   // 'PoliticalDivision2' => "",
                   // 'PoliticalDivision1' => "",
                    'SingleLineAddress' => $postcode,
                   // 'PostcodeExtendedLow' => "",
                    'CountryCode' => "GB" //$countryCode
                ),
            ),
            'Translate' => array('Locale' => 'en_GB'),
            'UnitOfMeasurement' => array('Code' => 'KM'),
            'LocationSearchCriteria' => array(
                'MaximumListSize' => "20"
            )
        );
        /*
         * 
         */
        


        $LocationArray['AccessRequest'] = $accessRequestArray;
        $LocationArray['LocatorRequest'] = $locatorRequest;

        $url = "https://onlinetools.ups.com/rest/Locator"; //$this->constants['UPS_NAMESPACE'];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($LocationArray));

        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($ch);
        $jsonResponse = json_decode($response);
        $dropoffDetails = array();
        if ($jsonResponse->LocatorResponse->Response->ResponseStatusDescription == "Success") {
            $dropLocation = $jsonResponse->LocatorResponse->SearchResults->DropLocation;

            foreach ($dropLocation as $drop) {
                $storedetail = array();
                $storedetail["lat"] = $drop->Geocode->Latitude;
                $storedetail["lng"] = $drop->Geocode->Longitude;
                $storedetail["companyname"] = $drop->AddressKeyFormat->ConsigneeName;
                $storedetail["addressline1"] = $drop->AddressKeyFormat->AddressLine;
                $storedetail["city"] = $drop->AddressKeyFormat->PoliticalDivision2;
                $storedetail["postcode"] = $drop->AddressKeyFormat->PostcodePrimaryLow;
                $storedetail["country"] = $drop->AddressKeyFormat->CountryCode;
                $storedetail["telephone"] = $drop->PhoneNumber;
                $daysofweek = $drop->OperatingHours->StandardHours->DayOfWeek;
               
                foreach ($daysofweek as $day) {
                    $dayOpenHoursLen = strlen($day->OpenHours);
                    $dayCloseHoursLen = strlen($day->CloseHours);
                    $dayofWeek = $day->Day;
                    $dayArray = array('', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun');
                    if ($dayOpenHoursLen == 3) {
                        $openhour = sprintf("%02d", substr($day->OpenHours, 0, 1));
                        $openminhute = sprintf("%02d", substr($day->OpenHours, 1, 2));
                    } else if ($dayOpenHoursLen == 4) {
                        $openhour = sprintf("%02d", substr($day->OpenHours, 0, 2));
                        $openminute = sprintf("%02d", substr($day->OpenHours, 2, 2));
                    }
                    if ($dayCloseHoursLen == 3) {
                        $closehour = sprintf("%02d", substr($day->CloseHours, 0, 1));
                        $closeminute = sprintf("%02d", substr($day->CloseHours, 1, 2));
                    } else if ($dayCloseHoursLen == 4) {

                        $closehour = sprintf("%02d", substr($day->CloseHours, 0, 2));
                        $closeminute = sprintf("%02d", substr($day->CloseHours, 2, 2));
                    }
                    $storedetail[$dayArray[$dayofWeek]] = $openhour . ":" . $openminute . " - " . $closehour . ":" . $closeminute;
                }
                
                $LocationAttribute = $drop->LocationAttribute;                
                foreach($LocationAttribute as $lDetail){
                    if($lDetail->OptionType->Description == "AdditionalServices"){
                        $AddtionalOptionsArray = $lDetail->OptionCode;
                        foreach($AddtionalOptionsArray as $additionOptions ){
                            if(count($additionOptions->TransportationPickUpSchedule->PickUp)> 0 && $additionOptions->Name == "Standard"){
                                $pickupinfor = $additionOptions->TransportationPickUpSchedule->PickUp;
                                foreach($pickupinfor as $pickup){
                                    $PickUpDetails = "";
                                    $nonPickUpDetail = "";
                                    $dayofWeek = $pickup->DayOfWeek;
                                    $dayArray = array('', 'Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat');
                                    $PickUpDetails = @$pickup->PickUpDetails->PickUpTime;
                                    $nonPickUpDetail = @$pickup->PickUpDetails->NoPickUpIndicator;
                                    $storedetail["latestPickUp"][$dayArray[$dayofWeek]] = $PickUpDetails . $nonPickUpDetail;
                                }
                            }
                        }
                    }
                }
                $dropoffDetails[] = $storedetail;
            }
        }
        
        return $dropoffDetails;
    }

    /**
     * generateLabel
     * @param string new pass phrase <p>
     * Must be a string or array.
     * </p>
     */
    public function recycledShipment($consignment) {
        $output["STATUS"] = "SUCCESS";
        return $output;
    }

    public function bagLabel($trackingNumberArray, $baggingObj = '', $serviceCodeArray = '') {
        if (count($trackingNumberArray) == 0) {
            $output['STATUS'] = 'ERROR';
            $output['MESSAGE'] = 'Cannot find tracking Number for bagging.';
            $output['ERROR'][]= 'Cannot find tracking Number for bagging.';
            return $output;
        }

        if ($baggingObj->getId() > 0) {
           $output = $this->createConsignmentArray($trackingNumberArray, $baggingObj, $serviceCodeArray);
           return $output;
        } else {
            return;
        }
    }

    private function createConsignmentArray($trackingNumberArray, $baggingObj, $serviceCode) {
        $output = array();
        $userId = $baggingObj->getUserId();
        $user = New User($userId);
        $userAccountId = $user->getUserAccountId();
        $userAccount = new CustomerAccount($userAccountId);
        $consignmentArray = array();
        $consignmentArray['order_reference'] = strtoupper("ST" . substr($$userAccountId, 0, 3) . generateRandomString(3) . time());
        $consignmentArray['awb'] = "";
        $service = new Services(309);
        $consignmentArray['service'] = $service->getId();
        $consignmentArray['is_product'] = 0;

        $consignmentArray['sender_country'] = $baggingObj->getBagSourceCountryId();
        $consignmentArray['receiver_country'] = $baggingObj->getBagDestinationCountryId();
        $consignmentArray['sender_company'] = $userAccount->getCompany();
        $consignmentArray['sender_contact'] = $user->getFirstName() . ' ' . $user->getLastName();
        $consignmentArray['sender_email'] = $user->getEmail();
        $consignmentArray['sender_telephone'] = $user->getTelephone();
        $consignmentArray['sender_address_line_1'] = $user->getAddress();
        $consignmentArray['sender_address_line_2'] = $user->getAddress2();
        $consignmentArray['sender_address_line_3'] = $user->getAddress3();
        $consignmentArray['sender_city'] = $user->getCity();
        $consignmentArray['sender_state'] = '';
        $consignmentArray['sender_postcode'] = $user->getPostcode();

        $consignmentArray['receiver_company'] = "One World Expres Inc";
        $consignmentArray['receiver_contact'] = "One World Express";
        $consignmentArray['receiver_email'] = "cs@oneworldexpress.com";
        $consignmentArray['receiver_telephone'] = "02088676060";
        $consignmentArray['receiver_address_line_1'] = "One World House";
        $consignmentArray['receiver_address_line_2'] = "Pump Lane";
        $consignmentArray['receiver_address_line_3'] = "Hayes";
        $consignmentArray['receiver_city'] = "London";
        $consignmentArray['receiver_state'] = "";
        $consignmentArray['receiver_postcode'] = "UB3 3NB";

        $consignmentArray['reference'] = "";
        $consignmentArray['item_value'] = "1";
        $consignmentArray['item_currency'] = "GBP";
        $consignmentArray['item_type'] = "";
        $consignmentArray['notes'] = "";
        $consignmentArray['description'] = "Consolidate Items";
        $consignmentArray['shipment_type'] = "DO";
        $consignmentArray['is_customer_billable'] = "0";

        $weightCount = 0;
        $consignmentArray['parcel'][$weightCount]['weight'] = trim($baggingObj->getActualWeight());
        $consignmentArray['parcel'][$weightCount]['length'] = trim($baggingObj->getLength());
        $consignmentArray['parcel'][$weightCount]['height'] = trim($parcelHeightArr[$pi]);
        $consignmentArray['parcel'][$weightCount]['width'] = trim($parcelWidthArr[$pi]);
        $consignmentArray['parcel'][$weightCount]['itemvalue'] = 0;

        $output = Consignment::saveShipment($consignmentArray, $userId, false, '', 'csv');
        
        if (trim($output['STATUS']) == 'ERROR') {
            $output['STATUS'] .= 'ERROR'; 
            $output['MESSAGE'] = '<br />' . $consignmentArray['order_reference'] . " - " . $output['MESSAGE'];
            
        } else {
            $consignmentId = $output['CONSIGNMENT_ID'];
            if ($consignmentId > 0) {
                $consignment = new Consignment($consignmentId);
                $labelReturn = Consignment::getInstantLabel($consignment, '', '', true);
                if (isset($labelReturn['STATUS']) && $labelReturn['STATUS'] == "ERROR") {
                        $labelReturn["CONSIGNMENT_ID"] = $consignment->getId();
                        return $labelReturn;
                }
                else
                {
                    $output = array();
                    $output['STATUS'] = 'SUCCESS';
                    $output['BAG_NUMBER'] = $labelReturn["AWB"];
                    $output['LABEL'] = $labelReturn["LABEL"];
                }
            }
        }
        return $output;
    }

    public function reconciliation_data($headingArr, $carrierId, $relPath, $new_csv_file_created, $filePath, $batchNumber) {
        ini_set('memory_limit', '-1');
        $output = [];
        $comaSeptHeading = implode(",", $headingArr);
        $tableColumn = rtrim($comaSeptHeading, ',');
        $csvStr = $tableColumn;
        $csvStr .= "\r\n";
        $templateCheck = 1;
        $checkExist = 0;
        $output['new_invoice_save'] = 0;
        $output['total_weight'] = 0;
        $output['total_pieces'] = 0;
        $output['total_amount'] = 0;
        $dataCsv = [];
        $row = 1;
        $account_number = '';
        $invoice_number = '';
        $agent_reference_number = '';
        $collection_date = '';
        $delivery_country = '';
        $mawb = '';
        $awb = '';
        $hawb = '';
        $service_name = 'UPS';
        $service_code = "STUPSDROP";
        $weight = 0.00;
        $volWeight = 0.00;
        $length = 0.00;
        $width = 0.00;
        $height = 0.00;
        $number_of_pieces = 0;
        $basic_charges = 0.00;
        $fuel_charges = 0.00;
        $vat = 0.00;
        $total_amount = 0.00;
        $notes = '';
        $currency = '';
        if (($handle = fopen($relPath, "r")) !== FALSE) {
            while (($data = fgetcsv($handle)) !== FALSE) {
                $delivery_country = $data[3];
                $collection_date = $data[4];
                $invoice_number = $data[5];
                $currency = $data[9];
                $weight = $data[10];
                $number_of_pieces = $data[18];
                $trackingNumber = $data['20']; /* $awb = $data[13]; */
                $charge_type = $data[43];
                $charges = $data[52];
                if ($charge_type == "FRT") {
                    $basic_charges = formatNumber($charges);
                }
                if ($charge_type == "FSC") {
                    $fuel_charges = formatNumber($charges);
                }
                if ($charge_type == "TAX") {
                    $vat = formatNumber($charges);
                }
                $notes = $data[45];
                $dataCsv[$trackingNumber][] = [
                    'account_number' => $account_number,
                    'invoice_number' => $invoice_number,
                    'agent_reference_number' => $agent_reference_number,
                    'collection_date' => $collection_date,
                    'delivery_country' => $delivery_country,
                    'mawb' => $mawb,
                    'awb' => $trackingNumber,
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
                    'currency' => $currency
                ];
                $row++;
            }
        } else {
            $output['status'] = 'error';
            $output['message'] = 'File can not open please check permission';
        }
        if ($output['status'] != "error") {
            if (count($dataCsv)) {
                foreach ($dataCsv as $dataArr) {
                    $account_number = '';
                    $invoice_number = '';
                    $agent_reference_number = '';
                    $collection_date = '';
                    $delivery_country = '';
                    $mawb = '';
                    $awb = '';
                    $hawb = '';
                    $service_name = 'UPS';
                    $service_code = "STUPSDROP";
                    $weight = 0.00;
                    $volWeight = 0.00;
                    $length = 0.00;
                    $width = 0.00;
                    $height = 0.00;
                    $number_of_pieces = 0;
                    $basic_charges = 0.00;
                    $fuel_charges = 0.00;
                    $additional_charges = 0.00;
                    $vat = 0.00;
                    $total_amount = 0.00;
                    $notes = '';
                    $currency = '';
                    foreach ($dataArr as $parcelCharge) {
                        $invoice_number = $parcelCharge['invoice_number'];
                        $collection_date = $parcelCharge['collection_date'];
                        $delivery_country = $parcelCharge['delivery_country'];
                        $awb = $parcelCharge['awb'];
                        $service_name = $parcelCharge['service_name'];
                        $service_code = $parcelCharge['service_code'];
                        $weight = $parcelCharge['weight'];
                        $number_of_pieces += $parcelCharge['number_of_pieces'];
                        $basic_charges += $parcelCharge['basic_charges'];
                        $fuel_charges += $parcelCharge['fuel_charges'];
                        $vat += $parcelCharge['vat'];
                        $notes = $parcelCharge['notes'];
                        $currency = $parcelCharge['currency'];
                    }
                    $total = $fuel_charges + $basic_charges + $additional_charges;
                    $total_amount = $total + $vat;
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
                        'additional_charges' => $additional_charges,
                        'vat' => $vat,
                        'total_amount' => $total_amount,
                        'notes' => $notes,
                        'currency' => $currency
                    ];
                    $comaSept = implode(",", $dt);
                    $csvStr .= rtrim($comaSept, ',');
                    $csvStr .= "\r\n";
                    $output['total_weight'] += $weight;
                    $output['total_pieces'] += $number_of_pieces;
                    $output['total_amount'] += $total_amount;
                    $output['currency'] = $currency;
                }
            }
        }
        if ($output['status'] != "error" && !$output['return']) {
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
                $output['template'] = $templateCheck;
                $output['data'] = $res;
            }
        }
        return $output;
    }

    public function getSummeryData($relPath) {
        $returnArr = [];
        $row = 1;
        $isInvoiceType = false;
        if (($handle = fopen($relPath, "r")) !== FALSE) {
            while (($data = fgetcsv($handle)) !== FALSE) {
                if ($row == 1) {
                    $returnArr['invoice_number'] = $data[5];
                    $returnArr['collection_date'] = $data[4];
                    $isInvoiceType = true;
                }
                if ($isInvoiceType) {
                    break;
                }
                $row++;
            }
        }
        return $returnArr;
    }

}
