<?php

include_once("pdfmerger.php");

class CanadaPost implements CarrierService {

    private $pdf;
    private $serviceValues = null;
    private $senderCountryObj = null;
    private $consigneeCountryObj = null;
    private $country = null;
    private $user;
    private $count_orders; // CANADA POST OFFLINE
    private $count_items; // CANADA POST OFFLINE
    private $count_packages; // CANADA POST OFFLINE

    public function __construct() {
        
    }

    public function validation(Consignment $consignment, Services $service, Country $country) {
        
    }

    public function remoteareas($consignment, $carrierObject, $sender) {
        
    }

    public function label($consignment, $labelType = 'pdf', $size = '100x150') {

        $output = array();
        $pdf = new PdfBase(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $this->pdf = $pdf;
        $this->serviceValues = new Services($consignment->getServiceId());
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
        if (trim(@$this->constants['INTEGRATION_TYPE']) == 'API') {
            if (trim(@$this->constants['API_HOST_NAME']) == '' || trim(@$this->constants['API_USERNAME']) == '' || trim(@$this->constants['API_PASSWORD']) == '' || trim(@$this->constants['API_CUSTOMERNO']) == '' || trim(@$this->constants['API_CONTRACTNO']) == '') {
                $output['STATUS'] = 'ERROR';
                $output['MESSAGE'] = "Please check service constants. Please contact to itsupport@oneworldexpress.com";
                return $output;
            }
        }
        
        
        if (trim(@$this->constants['INTEGRATION_TYPE']) == 'API') {
            $output = $this->apiLabel($this->constants, $consignment);
        } else {
            $output = $this->ediLabel($this->constants, $consignment);
        }

        return $output;
    }

    private function ediLabel($constantValue, $consignment) {
        /*
         *  Get Tracking Number ranges
         */

        $serviceRangeMappingFilter = new ServiceRangeMappingFilter();
        $serviceRangeMappingFilter->addFilter("service_id = '" . $consignment->getServiceId() . "' AND agent_id = '" . $consignment->getAgentId() . "' ");
        $serviceRange = $serviceRangeMappingFilter->getList(" licence_plate_id ");
        if (count($serviceRange) > 0) {
            $licence_plate_id = (int) $serviceRange[0]->getLicencePlateId();
        }
        if ($licence_plate_id < 0) {
            $output['STATUS'] = 'ERROR';
            $output['MESSAGE'] = "This service does not have tracking number range. Please contact to itsupport@oneworldexpress.com";
            return $output;
        }

        $parcel_list = $consignment->getParcels();
        $parcel_count = sizeof($parcel_list);
        $parcel_idx = 0;
        $this->customerNo = $constantValue["API_CUSTOMERNO"];
        
        $mergeFileName = BASE_PATH . "includes/labels/canadapost/canada-post-label.pdf";

        $PDFMerger = new PDFMerger();
        $PDFMerger->addPDF($mergeFileName);
        
       
        try {

            foreach ($parcel_list as $parcel) {
                if ($parcel->getTrackingNumber() == '') {
                    $resultArray = LicencePlate::getLicencePlateNumber($licence_plate_id);
                    if (trim($resultArray['STATUS']) == 'ERROR')
                        return $resultArray;
                    else {
                        $checkdigit = LicencePlate::mod10($this->customerNo . $resultArray["RANGE"]);
                        $licence_plate = $this->customerNo . $resultArray["RANGE"] . $checkdigit;
                    }
                    $parcel->setTrackingNumber($licence_plate);
                    $parcel->save();
                } else {
                    $licence_plate = $parcel->getTrackingNumber();
                }
                

                $response = $PDFMerger->mergeCanadaPost('file', "../_assets/pdf/" . date('Y_m_d') . '/' . $consignment->getId() . ".pdf", '', $consignment, $licence_plate);
                if ($response["STATUS"] == "ERROR") {
                    return $response;
                }
                $licence_plate_array[] = $licence_plate;

                $new_page_flag = true;
                ++$parcel_idx;
            }
        } catch (Exception $e) {
            echo 'Caught exception: ', $e->getMessage(), "\n";
        }


        //$this->pdf->IncludeJS("print();");
        //$this->pdf->Output("../_assets/pdf/" . date('Y_m_d') . '/' . $consignment->getId() . ".pdf", "F");
        $output['STATUS'] = 'SUCCESS';
        $output['LABEL'] = date('Y_m_d') . '/' . $consignment->getId() . ".pdf";
        $output['TRACKING_NUMBER'] = $licence_plate_array;


        return $output;
    }

    private function addWayBill(Consignment $consignment, $parcel_idx, $licence_plate, $parcel) {


        if (trim($consignment->getCompany()) != "")
            $company = $consignment->getCompany();
        if (trim($consignment->getContact()) != "")
            $contact = $consignment->getContact();
        if (trim($consignment->getAddressLine1()) != "")
            $address1 = utf8_decode($consignment->getAddressLine1());
        if (trim($consignment->getAddressLine2()) != "")
            $address2 = $consignment->getAddressLine2();
        if (trim($consignment->getAddressLine3()) != "")
            $address3 = $consignment->getAddressLine3();

        $this->pdf->setFont("helvetica", "", 9);
        $this->pdf->setTextColor(0, 0, 0);

        $y = 40;
        $x = 2;
        $offset = 4;

        if ($contact != '') {
            $this->pdf->Text($x, $y, strtoupper($contact));
            $y += $offset;
        }
        if ($company != '') {
            $this->pdf->Text($x, $y, strtoupper($company));
            $y += $offset;
        }

        if ($address1 != '') {
            $this->pdf->Text($x, $y, strtoupper($address1));
            $y += $offset;
        }

        //$this->pdf->Text($x, $y, $address2 . ' ' . $address3);

        $state = trim(strtolower($consignment->getState()));
        if ($state == 'newfoundland and labrador') {
            $state = "NL";
        } else if ($state == "prince edward island") {
            $state = "PE";
        } else if ($state == "nova scotia") {
            $state = "NS";
        } else if ($state == "quebec") {
            $state = "QC";
        } else if ($state == "ontario") {
            $state = "ON";
        } else if ($state == "manitoba") {
            $state = "MB";
        } else if ($state == "saskatchewan") {
            $state = "SK";
        } else if ($state == "alberta") {
            $state = "AB";
        } else if ($state == "british columbia") {
            $state = "BC";
        } else if ($state == "yukon") {
            $state = "YT";
        } else if ($state == "northwest territories") {
            $state = "NT";
        } else if ($state == "nunavut") {
            $state = "NU";
        }

        $this->pdf->Text($x, 49, strtoupper($consignment->getCity()) . " " .$state." ". strtoupper($consignment->getPostCode()));

        $this->pdf->line(2, 60, 150, 60); // ending line for the address

        $this->pdf->line(50, 68, 100, 68); // one more line in the address

        $this->pdf->line(50, 60, 50, 80); // middle line
        /////////////////////////////////

        $this->pdf->line(2, 123, 100, 123); // one more line in the address

        $this->pdf->line(70, 123, 70, 142);



        //// bottom lines

        $this->pdf->line(2, 139, 70, 139); // one more line in the address

        $this->pdf->SetLineStyle(array('width' => 0.5, 'cap' => 'butt', 'join' => 'miter', 'color' => array(0, 0, 0)));

        $this->pdf->line(2, 142, 150, 142); // one more line in the address

        $this->pdf->line(86, 138, 86, 142); // one more line in the address
        /////////////////////////////


        $this->pdf->setFont("helvetica", "B", 25);
        $this->pdf->Text(2, 65, strtoupper($consignment->getPostCode()));


        //$style3 = array('width' => 2, 'cap' => 'square', 'join' => 'round', 'dash' => '10,5', 'color' => array(0, 0, 0));                        
        //$this->pdf->Line(5, 84, 150, 84, $style3);
        //$this->pdf->SetFillColor(255,255,128);
        //$this->pdf->SetTextColor(0,0,128);

        $serviceCode = "2";

        $postCode = trim($consignment->getPostCode());
        $digits = preg_replace("/[^0-9.]/", "", $consignment->getPostCode());
        $digits = str_replace(" ", "", $digits);
        $letters = preg_replace('/[0-9]/', '', $consignment->getPostCode());
        $letters = str_replace(" ", "", $letters);

        $barCode = $serviceCode . $letters . $digits . $licence_plate . "00000";
        $trackingNumber = $licence_plate;

        $this->pdf->write1DBarcode($barCode, 'C128', 12, 85, '', 20, 0.4, '', '');

        $this->pdf->setFont("helvetica", "B", 7);
        $this->pdf->Text(2, 108, "TRACKING NUMBER");

        $this->pdf->setFont("helvetica", "B", 9);
        $this->pdf->Text(36, 107, chunk_split($trackingNumber, 4));

        $this->pdf->setFont("helvetica", "", 7);
        $this->pdf->setTextColor(255, 255, 255);
        $this->pdf->SetFillColor(0, 0, 0);
        $this->pdf->Rect(3, 123, 14, '3', 'F');


        /* dynamic
          $this->pdf->setTextColor(0, 0, 0);
          $this->pdf->setFont("helvetica", "", 6);
          $this->pdf->Text(2, 127, "One World Express Inc. Ltd.");
          $this->pdf->Text(2, 129, "502 MAIN ST N");
          $this->pdf->Text(2, 131, "MONTREAL QC H2B 1A0");
         */


        $this->pdf->setFont("helvetica", "B", 6);
        $this->pdf->setTextColor(0, 0, 0);
        //$this->pdf->Text(70, 124, (int) $parcel->getLength() . "x" . (int) $parcel->getWidth() . "x" . (int) $parcel->getHeight() . "cm");
        $this->pdf->Text(92, 124, $consignment->getWeight());

        /*
          $this->pdf->setFont("helvetica", "", 6);
          $this->pdf->Text(89, 127, "KG VE/EV");

          $this->pdf->setFont("helvetica", "B", 6);

          $this->pdf->Text(70, 130, "MANIFEST REQ");
          $this->pdf->Text(70, 132, "MANIFESTE REQ");

          $this->pdf->setFont("helvetica", "", 6);
          $this->pdf->Text(70, 139, "P/F:  " . $this->customerNo);

          $this->pdf->setFont("helvetica", "B", 10);
          $this->pdf->Text(87, 138, "A/C");
         */


        $this->pdf->setFont("helvetica", "", 6);
        //$this->pdf->Text(2, 139, "VIN/NIF 441                  SPEC 3696 V2.0");
        $this->pdf->Text(40, 144, "PIN / NIP:" . $trackingNumber);
        $this->pdf->Text(40, 146, "Ref./Ref:" . $consignment->getHawb());


        $parcel->setTrackingNumber($trackingNumber);
        $parcel->save();

        return $trackingNumber;
    }

    private function apiLabel($constantsArray, Consignment $consignment) {
        $output = array();
        $this->hostName = trim(@$constantsArray['API_HOST_NAME']);
        $opts = array('ssl' =>
            array(
                'verify_peer' => true,
                'cafile' => SETTING_DIR_REMOTE . 'includes/labels/wsdl-canadapost/cert/cacert.pem',
                'peer_name' => $this->hostName
            ),
            'http' => array(
                'protocol_version' => 1.0,
            ),
        );

        $this->ctx = stream_context_create($opts);

        // Set WS Security UsernameToken
        $this->WSSENS = 'http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd';

        $pdf = new PdfBase(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $this->pdf = $pdf;

        require_once("../includes/labels/wsdl-canadapost/canadapostsoapclass.php");
        $wsdl = SETTING_DIR_REMOTE . 'includes/labels/wsdl-canadapost/shipment.wsdl';
        // SOAP URI
        //
                                $location = 'https://' . $this->hostName . '/rs/soap/shipment/v8';
        $client = new CanadaPostSoapClient($wsdl, array('trace' => 1, 'location' => $location, 'features' => SOAP_SINGLE_ELEMENT_ARRAYS, 'stream_context' => $this->ctx));

        $usernameToken = new stdClass();
        $usernameToken->Username = new SoapVar(@$constantsArray['API_USERNAME'], XSD_STRING, null, null, null, $this->WSSENS);
        $usernameToken->Password = new SoapVar(@$constantsArray['API_PASSWORD'], XSD_STRING, null, null, null, $this->WSSENS);
        $content = new stdClass();
        $content->UsernameToken = new SoapVar($usernameToken, SOAP_ENC_OBJECT, null, null, null, $this->WSSENS);
        $header = new SOAPHeader($this->WSSENS, 'Security', $content);
        $client->__setSoapHeaders($header);

        try {

            $mailedBy = @$constantsArray['API_CUSTOMERNO'];
            $groupId = $consignment->getHawb();

            $requestedShippingPoint = 'H2B1A0';
            $mailingDate = date('Y-m-d'); //'2012-10-24';
            $contractId = @$constantsArray['API_CONTRACTNO']; // '43546034'; //'42708517';//'0042708517';

            $parcel = new ParcelFilter();
            $parcel->addConsignmentIdFilter($consignment->getId());
            $parcelList = $parcel->getList();

            if (count($parcelList) > 0) {
                $length = $parcelList[0]->getLength();
                $Width = $parcelList[0]->getWidth();
                $Height = $parcelList[0]->getHeight();
            }
            if ($length <= 0 || $Width <= 0 || $Height <= 0) {
                $length = 10;
                $Width = 10;
                $Height = 10;
            }

            $state = trim($consignment->getState());

            $requestdataSoapApi = array(
                'locale' => 'EN',
                'mailed-by' => $mailedBy,
                'shipment' => array(
                    //The validation expects this structure. However, this element will be removed and replaced only with ns1:group-id or ns1:transmit-shipment. 
                    'groupIdOrTransmitShipment' => array(
                        'ns1:group-id' => $groupId
                    //'ns1:transmit-shipment' => 'true'
                    ),
                    'requested-shipping-point' => $requestedShippingPoint,
                    'cpc-pickup-indicator' => 'true',
                    'expected-mailing-date' => $mailingDate,
                    'delivery-spec' => array(
                        'service-code' => 'DOM.EP',
                        'sender' => array(
                            'name' => '',
                            'company' => 'One World Express Inc Ltd.',
                            'contact-phone' => '02088676060',
                            'address-details' => array(
                                'address-line-1' => '502 MAIN ST N',
                                'city' => 'MONTREAL',
                                'prov-state' => 'QC',
                                'country-code' => 'CA',
                                'postal-zip-code' => 'H2B1A0'
                            )
                        ),
                        'destination' => array(
                            'name' => $consignment->getContact(),
                            'company' => $consignment->getCompany(),
                            'address-details' => array(
                                'address-line-1' => utf8_encode($consignment->getAddressLine1()),
                                'city' => utf8_encode($consignment->getCity()),
                                'prov-state' => $state,
                                'country-code' => $this->country->getIso(),
                                'postal-zip-code' => utf8_encode($consignment->getPostCode())
                            )
                        ),
                        'options' => array(
                            'option' => array(
                                'option-code' => 'DC'
                            )
                        ),
                        'parcel-characteristics' => array(
                            'weight' => number_format($consignment->getWeight(), 2),
                            'dimensions' => array(
                                'length' => $length,
                                'width' => $Width,
                                'height' => $Height
                            ),
                            'unpackaged' => false,
                            'mailing-tube' => false
                        ),
                        'notification' => array(
                            'email' => (trim($consignment->getEmail()) != '' ? $consignment->getEmail() : 'cs@oneworldexpress.com' ),
                            'on-shipment' => true,
                            'on-exception' => false,
                            'on-delivery' => true
                        ),
                        'print-preferences' => array(
                            'output-format' => '4x6'
                        ),
                        'preferences' => array(
                            'show-packing-instructions' => false,
                            'show-postage-rate' => false,
                            'show-insured-value' => true
                        ),
                        'settlement-info' => array(
                            'contract-id' => $contractId,
                            'intended-method-of-payment' => 'Account'
                        ),
                        'references' => array(
                            'cost-centre' => 'ccent',
                            'customer-ref-1' => $consignment->getReference(),
                            'customer-ref-2' => $consignment->getNotes()
                        )
                    )
                )
            );

            // Execute Request
            $result = $client->__soapCall('CreateShipment', array('create-shipment-request' => $requestdataSoapApi
                    ), NULL, NULL);
            // Parse Response               
            if (isset($result->{'shipment-info'})) {
                $shipmentid = $result->{'shipment-info'}->{'shipment-id'};
                $trackingpin = $result->{'shipment-info'}->{'tracking-pin'};
                foreach ($result->{'shipment-info'}->{'artifacts'}->{'artifact'} as $artifact) {
                    $artifac_id = $artifact->{'artifact-id'};
                }


                if (trim($artifac_id) != '') {
                    $artifact_result = $this->getShipmentArtifact($artifac_id, $consignment->getId(), $constantsArray);

                    if ($artifact_result["STATUS"] == "SUCCESS") {

                        if (count($parcelList) > 0) {
                            $parcelList[0]->setTrackingNumber($shipmentid);
                            $parcelList[0]->save();
                        }

                        $licence_plate_array[] = $shipmentid;
                        $output['STATUS'] = 'SUCCESS';
                        //SETTING_MAIN_URL . "_assets/pdf/" .
                        $output['LABEL'] = $artifact_result["MESSAGE"];
                        $output['TRACKING_NUMBER'] = $licence_plate_array;

                        return $output;
                    } else {

                        return $artifact_result;
                    }
                }
            } else {
                foreach ($result->{'messages'}->{'message'} as $message) {
                    //echo 'Error Code: ' . $message->code . "\n";
                    $output['STATUS'] = 'ERROR';
                    //SETTING_MAIN_URL . "_assets/pdf/" .
                    $output['MESSAGE'] = $message->description . "\n\n";
                   return $output;
                }
            }
        } catch (SoapFault $exception) {
            //                echo 'Fault Code: ' . trim($exception->faultcode) . "\n"; 
            $output['STATUS'] = 'ERROR';
            $output['MESSAGE'] = trim($exception->getMessage());
            return $output;
        }
    }
    //        GET LABEL BASED ON ARTIFACT ID
    private function getShipmentArtifact($artifact_id, $consignmentId, $constantsArray) {
        $output = "";
        $wsdl = SETTING_DIR_REMOTE . 'includes/labels/wsdl-canadapost/artifact.wsdl';
        // SOAP URI
        $location = 'https://' . $this->hostName . '/rs/soap/artifact';


        $client = new SoapClient($wsdl, array('location' => $location, 'features' => SOAP_SINGLE_ELEMENT_ARRAYS, 'stream_context' => $this->ctx));

        // Set WS Security UsernameToken
        $usernameToken = new stdClass();
        $usernameToken->Username = new SoapVar($constantsArray['API_USERNAME'], XSD_STRING, null, null, null, $this->WSSENS);
        $usernameToken->Password = new SoapVar($constantsArray['API_PASSWORD'], XSD_STRING, null, null, null, $this->WSSENS);
        $content = new stdClass();
        $content->UsernameToken = new SoapVar($usernameToken, SOAP_ENC_OBJECT, null, null, null, $this->WSSENS);

        $header = new SOAPHeader($this->WSSENS, 'Security', $content);
        $client->__setSoapHeaders($header);
        try {
            $mailedBy = $constantsArray['API_CUSTOMERNO'];

            $result = $client->__soapCall('GetArtifact', array(
                'get-artifact-request' => array(
                    'locale' => 'EN',
                    'artifact-id' => $artifact_id,
                    'page-index' => '0'
                )
            ));

            if (isset($result->{'artifact-data'})) {
                if (strpos($result->{'artifact-data'}->{'mime-type'}, 'application/pdf') !== FALSE) {
                    $fileLoc = SETTING_DIR_ASSETS . 'pdf/' . date('Y_m_d') . '/' . $consignmentId . ".pdf";
                } else {
                    $fileLoc = SETTING_DIR_ASSETS . 'pdf/' . date('Y_m_d') . '/' . $consignmentId . ".zpl";
                }

                //            echo 'Decoding to' . $fileLoc . "\n";
                $fp = fopen($fileLoc, 'w');
                stream_filter_append($fp, 'convert.base64-decode');
                fwrite($fp, $result->{'artifact-data'}->{'image'});
                fclose($fp);
                $output["STATUS"] = "SUCCESS";
                $output["MESSAGE"] = date('Y_m_d') . '/' . $consignmentId . ".pdf";
            } else {
                foreach ($result->{'messages'}->{'message'} as $message) {
                    $output["STATUS"] = "ERROR";
                    $output["MESSAGE"] = $message->description;
                }
            }
        } catch (SoapFault $exception) {
            //echo 'Fault Code: ' . trim($exception->faultcode) . "\n"; 
            $output["STATUS"] = "ERROR";
            $output["MESSAGE"] = trim($exception->getMessage());
        }
        return $output;
    }

    public function GetReturnLabel($constantsArray, Consignment $consignment) {

        try {


            $wsdl = SETTING_DIR_REMOTE . 'includes/labels/wsdl-canadapost/authreturn.wsdl';
            //require_once("../includes/autoload/canadapostsoapclass.php");
            $this->hostName = $constantsArray["API_HOST_NAME"];
            $location = 'https://' . $this->hostName . '/rs/soap/authreturn/v2';
            $this->WSSENS = 'http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd';

            $opts = array('ssl' =>
                array(
                    'verify_peer' => false,
                    'cafile' => SETTING_DIR_REMOTE . 'includes/labels/wsdl-canadapost/cert/cacert.pem',
                    'CN_match' => $this->hostName
                )
            );



            $ctx = stream_context_create($opts);


            $client = new SoapClient($wsdl, array('location' => $location, 'features' => SOAP_SINGLE_ELEMENT_ARRAYS, 'stream_context' => $ctx));

            $usernameToken = new stdClass();
            $usernameToken->Username = new SoapVar($constantsArray["API_USERNAME"], XSD_STRING, null, null, null, $this->WSSENS);
            $usernameToken->Password = new SoapVar($constantsArray["API_PASSWORD"], XSD_STRING, null, null, null, $this->WSSENS);
            $content = new stdClass();
            $content->UsernameToken = new SoapVar($usernameToken, SOAP_ENC_OBJECT, null, null, null, $this->WSSENS);
            $header = new SOAPHeader($this->WSSENS, 'Security', $content);
            $client->__setSoapHeaders($header);




            $mailedBy = $constantsArray["API_CUSTOMERNO"];
            $groupId = $consignment->getHawb();

            $contractId = $constantsArray["API_CONTRACTNO"];

            $parcel = new ParcelFilter();
            $parcel->addConsignmentIdFilter($consignment->getId());
            $parcelList = $parcel->getList();


            $result = $client->__soapCall('CreateAuthorizedReturn', array(
                'create-authorized-return-request' => array(
                    'locale' => 'FR',
                    'mailed-by' => $mailedBy,
                    'authorized-return' => array(
                        'service-code' => 'DOM.EP',
                        'returner' => array(
                            'name' => $consignment->getSenderContact(),
                            'company' => $consignment->getSenderCompany(),
                            'domestic-address' => array(
                                'address-line-1' => $consignment->getSenderAddressLine1(),
                                'city' => $consignment->getSenderCity(),
                                'province' => $consignment->getSenderAddressLine3(),
                                'postal-code' => $consignment->getSenderPostCode()
                            )
                        ),
                        'receiver' => array(
                            'name' => 'John Doe',
                            'company' => 'ACME Corp',
                            'domestic-address' => array(
                                'address-line-1' => '123 Postal Drive',
                                'city' => 'Ottawa',
                                'province' => 'ON',
                                'postal-code' => 'K1P5Z9'
                            )
                        ),
                        'parcel-characteristics' => array(
                            'weight' => 15
                        ),
                        'print-preferences' => array(
                            'encoding' => 'PDF'
                        ),
                        'settlement-info' => array(
                            'contract-id' => $contractId
                        )
                    )
                )
                    ), NULL, NULL);


            //print_r($result);

            $trackingpin = "";

            if (isset($result->{'authorized-return-info'})) {

                $trackingpin = $result->{'authorized-return-info'}->{'tracking-pin'};
                foreach ($result->{'authorized-return-info'}->{'artifacts'}->{'artifact'} as $artifact) {
                   $artifac_id = $artifact->{'artifact-id'};
                }
            }

            //$candaPost = new CanadaPost();
            //$this->getShipmentArtifact($artifac_id, $consignment->getId());

            if (trim($artifac_id) != '') {
//                                                                                            echo $artifac_id;
                $artifact_result = $this->getShipmentArtifact($artifac_id, $consignment->getId());
                $label_response = explode("||", $artifact_result);
                if ($artifact_result["STATUS"] == "SUCCESS") {

                    if (count($parcelList) > 0) {
                        $parcelList[0]->setTrackingNumber($trackingpin);
                        $parcelList[0]->save();
                    }
                    $licence_plate_array[] = $trackingpin;
                    $output['STATUS'] = 'SUCCESS';
                    //SETTING_MAIN_URL . "_assets/pdf/" .
                    $output['LABEL'] = $artifact_result["MESSAGE"];
                    $output['TRACKING_NUMBER'] = $licence_plate_array;

                    return $output;
                } else {
                    return $artifact_result;
                }
            } else {
                $output["STATUS"] = "ERROR";
                foreach ($result->{'messages'}->{'message'} as $message) {
                    //echo 'Error Code: ' . $message->code . "\n";
                    $output["MESSAGE"] = $message->description . "\n\n";
                }
            }
        } catch (SoapFault $exception) {
            $output["STATUS"] = "ERROR";
            $output["MESSAGE"] = trim($exception->getMessage());
        }
        return $output;
    }

    public function tracking($trackingNo, $trackBy = 'shipment', $EDI = false) 
    {
        
        require_once '../includes/labels/canadaposttrackingstatus.class.php';
        $consignmentFilter   =     new ConsignmentFilter();
        $consignmentFilter->addawbFilter($trackingNo);
        $list = $consignmentFilter->getColumnList('*');

        $userProperties = parse_ini_file(SETTING_DIR_REMOTE. 'includes/canadapost/user.ini');

        $username = $userProperties['username']; 
        $password = $userProperties['password'];

        $service_url = "https://soa-gw.canadapost.ca/vis/track/pin/".$trackingNo."/details";    

        $curl = curl_init($service_url); // Create REST Request
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2); 
        curl_setopt($curl, CURLOPT_CAINFO, SETTING_DIR_REMOTE. 'includes/canadapost/cert/cacert.pem'); // Mozilla cacerts
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($curl, CURLOPT_USERPWD, $username . ':' . $password);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Accept:application/vnd.cpc.track+xml', 'Accept-Language:en-CA'));
        $curl_response = curl_exec($curl); // Execute REST Request            
            
        $xml = simplexml_load_string($curl_response);
        $json = json_encode($xml);
        $Response = json_decode($json, TRUE);
        $trackingArray = $Response['significant-events']['occurrence']; 
        curl_close($curl_response);    
            
        if(count($trackingArray) > 0)
        {
            foreach ($trackingArray as $event ) 
            {
                $entityId = 0;
                if ($trackBy == 'shipment') 
                {
                    $shipmenObj = new ConsignmentFilter();
                    $shipmenObj->addawbFilter($trackingNo);
                    $shipmentDataArray = $shipmenObj->getColumnList('c.awb');
                    if (count($shipmentDataArray) > 0) 
                    {
                        $shipmentData = $shipmentDataArray[0];
                        $entityId = $shipmentData->getId();
                    }
                }
                else if ($trackBy == 'parcel') 
                {
                    $parcelObj = new ParcelFilter();
                    $parcelObj->addTrackingNumberFilter($trackingNo);
                    $parcelDataArray = $parcelObj->getColumnList('p.id, p.tracking_number','p.consignment_id');
                    if (count($parcelDataArray) > 0) 
                    {
                        $parcelData = $parcelDataArray[0];
                        $entityId = $parcelData->getConsignmentId();
                    }
                }

                $EventCode = $event['event-identifier'];
                $Signatory = '';
                $canadaPostTrackingStatus = CanadaPostTrackingStatus::getOweStatusCode($EventCode);
                $dateTime = $event['event-date'] . " " . $event['event-time'];
                $ServiceAreaDescription = $event['event-site'];
                $EventDescription = $event['event-description'];    
                
                $trackingDataFilter = new TrackingDataFilter();
                $trackingDataFilter->addTrackPointExistFilter($trackingNo, $canadaPostTrackingStatus, $ServiceAreaDescription, $EventCode, $EventDescription);
                $trackingDataExistsObj = $trackingDataFilter->getColumnList("t.id");
                
                if (count($trackingDataExistsObj) == 0) 
                {
                    $trackingData = [
                        'user_id' => 0,
                        'entity_id' => $entityId,
                        'entity_type' => 'shipment',
                        'tracking_number' => $trackingNo,
                        'track_point' => $ServiceAreaDescription,
                        'date_created' => $dateTime,
                        'ip_address' => getClientIp(),
                        'status_code_id' => $canadaPostTrackingStatus,
                        'carrier_code' => $EventCode,
                        'carrier_desc' => $EventDescription,
                        'signatory' => $Signatory
                        ];
                    $trackingDataObj = new TrackingData($trackingData);                   
                    $trackingDataObj->save();
                }
            }// end foreach 
            
            $trackingDataFilter = new TrackingDataFilter();
            $trackingDataFilter->addTrackingNumberFilter($trackingNo);
            $trackingDataFilter->AddOrderByDate(false);
            $trackingDataObj = $trackingDataFilter->getColumnList('entity_id,entity_type,carrier_code,status_code_id');
                        
            if (count($trackingDataObj) > 0) 
            {
                $trackingDataObj = $trackingDataObj[0];
                $carrierCode = $trackingDataObj->getCarrierCode();
                $oweTrackingStatusCode = $trackingDataObj->getStatusCodeId();
                $entityType = $trackingDataObj->getEntityType();
                $entityId = $trackingDataObj->getEntityId();
                $consignmentStatusCode = CanadapostTrackingStatus::getConsignmentStatus($oweTrackingStatusCode);
                $consignmentId = 0;
                if ($entityType == 'parcel') 
                {
                    $parcelObj = new Parcel($entityId);
                    $dateTime = date("Y-m-d H:i:s");
                    $parcelObj->setParcelStatusCode($consignmentStatusCode);
                    $parcelObj->setLastTrackingUpdate(strtotime($dateTime));
                    $parcelObj->save();
                    $consignmentId = $parcelObj->getConsignmentId();
                } 
                else 
                {
                    $consignmentId = $entityId;
                    $parcelObj = new Parcel();
                    $parcelObj->bulkUpdate("parcel_status_code='" . $consignmentStatusCode . "', last_tracking_update=NOW()", "consignment_id = '" . $consignmentId . "'");
                }
                $ConsignmentObj = new Consignment($consignmentId);
                $consignmentStatus = isset(Consignment::$database_status_array[$consignmentStatusCode]) ? Consignment::$database_status_array[$consignmentStatusCode] : '';
                $ConsignmentObj->setShipmentStatus($consignmentStatusCode);
                if(empty($ConsignmentObj->getDateScanned()))
                {
                    Tracking::setScanDate($ConsignmentObj);
                }
                if($consignmentStatus != '')
                    $ConsignmentObj->setConsignmentStatus($consignmentStatus);
                $ConsignmentObj->save();
            }   
        }
    }

    public function sendData($tracking_numbers = array()) {

        $output = [];
        $agentServiceArray = [];
        $consignmentData = new ConsignmentFilter();
        $consignmentData->addFilter("     s.carrier_id= '195'", "servicefilter");
        $consignmentData->addFilter("     c.send_courier_data= '0'", "consignmentfilter");
        if (!empty($tracking_numbers)) {
            $consignmentData->addFilter("AND pc.tracking_number in ('" . implode("','", $tracking_numbers) . "') and pc.tracking_number <> ''", "parcelJoinFilter");
        }
        $consignmentData->addGroupBy(" c.service_id, c.agent_id ");

        //print_r($consignmentData);
        
        $consignmentServiceAgent = $consignmentData->getColumnList("c.service_id, c.agent_id");
        
        
       
        //print_r($consignmentServiceAgent);
        
        //die;



        if (count($consignmentServiceAgent) > 0) {         
           
            
            foreach ($consignmentServiceAgent as $agentData) {
                $agentServiceArray['services'][] = $agentData->getServiceId();
                $agentServiceArray['agent'][] = $agentData->getAgentId();          
                
            }    
            
            
        } else {
            $output["STATUS"] = "ERROR";
            $output["MESSAGE"] = "Please check we did not find any agent and services for data to send.";
            return $output;
        }

        if (!empty($agentServiceArray['services'])) {


            //print_r($agentServiceArray['services']);            
            //die;

            foreach ($agentServiceArray['services'] as $key => $serviceid) {

                $serviceid = trim($serviceid);
                $agentid = trim($agentServiceArray['agent'][$key]);

                $agentObject = new AgentData($agentid);
                $agentCountryObject = new Country($agentObject->getCountryId());
                $this->serviceValues = new Services($serviceid);



                // GET CONSTANTS AS PER SERVICE AND AGENT
                $serviceAgentConstantFilter = new ServiceConstantValueFilter();
                $serviceAgentConstantFilter->addFilter("service_id = '" . $serviceid . "' AND agent_id = '" . $agentid . "' ");
                $serviceAgentConstant = $serviceAgentConstantFilter->getConstantList("c.constant 'constant_name', scv.constant_value 'constant_value'");
               
                //print_r($serviceAgentConstant);
                
                if (count($serviceAgentConstant) > 0) {
                    foreach ($serviceAgentConstant as $serviceAgentConstantData) {
                        $this->constants[$serviceid][$serviceAgentConstantData->getConstantName()] = $serviceAgentConstantData->getConstantValue();
                    }
                }

                
                //print_r($serviceAgentConstant);
                //die;
                
                //echo "service id " .  $serviceid;
                
                //print_r($this->constants[$serviceid]);
                //die;
               
                
                if (!empty($this->constants[$serviceid])) {
                    // GET ALL THE CONSIGNMENT WITH THE PARCEL FOR PREPARE  FILE
                    $consignmentShipmentDataFilter = new ConsignmentFilter();
                    $consignmentShipmentDataFilter->addFilter("     c.service_id = '" . $serviceid . "' AND c.agent_id = '" . $agentid . "' ", "consignmentfilter");
                    $consignmentShipmentDataFilter->addFilter("     c.send_courier_data= '0'", "consignmentfilter");
                    if (!empty($tracking_numbers))
                        $consignmentShipmentDataFilter->addFilter("     AND pc.tracking_number in ('" . implode("','", $tracking_numbers) . "') and pc.tracking_number <> ''", "parcelJoinFilter");
                    $consignmentShipmentData = $consignmentShipmentDataFilter->getColumnList(" c.id 'consignment_id', s.carrier_id ,s.code 'service_code',
                    con.region 'country_region', c.service_id, c.weight, c.hawb, c.description, c.company,
                    c.contact, c.address_line_1, c.address_line_2, c.city, c.postcode, c.telephone, 
                    c.sender_name, c.sender_company, c.sender_address_line_1, c.sender_address_line_2, c.sender_address_line_3, sender_city, sender_postcode, sender_country_id, sender_state");

                    

                    if (count($consignmentShipmentData) > 0) {

                        $hawbArray = array();
                        foreach ($consignmentShipmentData as $consignment) {
                            $hawbArray[] = $consignment->getHawb();
                        }

                        $constantValue = $this->constants[$serviceid];

                        $this->senderCountryObj = new Country($consignment->getSenderCountryId());
                        $this->consigneeCountryObj = new Country($consignment->getCountryId());

                        //echo "integration type " .  $this->constants[$serviceid]['INTEGRATION_TYPE'];   
                        //print_r($constantValue);                        
                        //die;



                        if (trim(@$this->constants[$serviceid]['INTEGRATION_TYPE']) == 'API') 
                        {

                                // GET ALL THE CONSIGNMENT WITH THE PARCEL FOR PREPARE YODEL FILE
                                $wsdl = SETTING_DIR_REMOTE . 'includes/labels/wsdl-canadapost/manifest.wsdl';
                                // SOAP URI
                                $location = 'https://' . $constantValue['API_HOST_NAME'] . '/rs/soap/manifest/v8';
                                $this->WSSENS = 'http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd';

                                $client = new SoapClient($wsdl, array('location' => $location, 'features' => SOAP_SINGLE_ELEMENT_ARRAYS, 'stream_context' => $this->ctx));


                                // Set WS Security UsernameToken
                                $usernameToken = new stdClass();
                                $usernameToken->Username = new SoapVar($constantValue['API_USERNAME'], XSD_STRING, null, null, null, $this->WSSENS);
                                $usernameToken->Password = new SoapVar($constantValue['API_PASSWORD'], XSD_STRING, null, null, null, $this->WSSENS);
                                $content = new stdClass();
                                $content->UsernameToken = new SoapVar($usernameToken, SOAP_ENC_OBJECT, null, null, null, $this->WSSENS);
                                $header = new SOAPHeader($this->WSSENS, 'Security', $content);
                                $client->__setSoapHeaders($header);

                                try {
                                    $mailedBy = $constantValue['API_CUSTOMERNO'];
                                    //$groupId = $consignment->getHawb();
                                    $requestedShippingPoint = 'H2B1A0';
                                    // Execute Request     
                                    $result = $client->__soapCall('TransmitShipments', array(
                                        'transmit-shipments-request' => array(
                                            'locale' => 'EN',
                                            'mailed-by' => $mailedBy,
                                            'transmit-set' => array(
                                                'group-ids' => array('group-id' => $hawbArray),
                                                'requested-shipping-point' => $requestedShippingPoint,
                                                'cpc-pickup-indicator' => 'true',
                                                'detailed-manifests' => true,
                                                'method-of-payment' => 'Account',
                                                'manifest-address' => array(
                                                    'manifest-company' => 'One World Express',
                                                    'phone-number' => '02088676080',
                                                    'address-details' => array(
                                                        'address-line-1' => '1230 Tako RD.',
                                                        'city' => 'Ottawa',
                                                        'prov-state' => 'ON',
                                                        'country-code' => 'CA',
                                                        'postal-zip-code' => 'H2B1A0'
                                                    )
                                                )
                                            )
                                        )
                                            ), NULL, NULL);
                                    $manifestAll = array();

                                    //                            echo "<pre>";
                                    //   print_r($result);
                                    //                            echo "REQUEST:\n" . $client->__getLastRequest() . "\n";
                                    //                            die;
                                    // Parse Response
                                    if (isset($result->{'manifests'})) {
                                        foreach ($result->{'manifests'}->{'manifest-id'} as $manifestId) {
                                            $manifest_id = $manifestId;
                                            $manifestAll[] = $manifest_id;

                                           /* if($manifest_id != "")
                                              {
                                              $mani_response = $this->getManifestArtifactId($manifest_id);
                                              return $mani_response;

                                              } */
                                        }
                                        $output["STATUS"] = "SUCCESS";
                                        $output["MESSAGE"] = $manifestAll;
                                    } else {
                                        foreach ($result->{'messages'}->{'message'} as $message) {
                                            $output["STATUS"] = "ERROR";
                                            $output["MESSAGE"] = $message->description;
                                        }
                                    }
                                } catch (SoapFault $exception) {
                                    $output["STATUS"] = "ERROR";
                                    $output["MESSAGE"] = trim($exception->getMessage());
                                }
                            } 
                        else 
                        {
                            

                            $this->SendDataToLivingsTon($consignmentShipmentData , $constantValue);   
                            
                            $this->SendDataToCanadaPost($consignmentShipmentData, $constantValue);
                            
                            
                            
                            
                            
                            
                        }
                    }
                    

                   
                }
                //print_r($output);
                return $output;
            }
        }
    }
    
    public function createPdfManifest($consignmentShipmentData, $pdf, $text)
    {
        
        
        $pdf->SetPrintFooter(false);
        $pdf->SetHeaderMargin(0);
        $pdf->SetFooterMargin(0);
        $pdf->SetAutoPageBreak(false, 0);
        $page_size = array(215, 279);
           
            
        $pdf->AddPage("P", $page_size);
        $pdf->setFont("helvetica", "B", 7);
        $flightnumber = "";
        $format = "";
        $bag_type = "";
        $bag_value = "";
        $pdf->Image('../images/canadapost.jpg', 8, 5, 55, 20, '', '', '', '', 300);
        $pdf->Text(61, 11, "Parcels - Detailed Manifest");
        $pdf->Text(61, 15, "Colis - Manifeste d?taill?");
        $pdf->Text(10, 25, "ABC"); 
        $pdf->Text(10, 28, "4 LAKESHORE WEST"); 
        $pdf->Text(10, 33, "OAKVILLE ON L6K OJ6"); 
        $pdf->Text(10, 37, "CIF ACMA: Yes"); 
        $pdf->Text(10, 40, "Customer Reference R?f?rence du client"); 

        $pdf->Text(90, 28, "BART MUIR");
        $pdf->Text(90, 31, "999999999");
        $pdf->Text(161, 13, $counterId);
        $pdf->Rect(194, 11, 5, 5);
        $i = 1;
        $pdf->Text(195, 12, $i);
            //$pdf->setFont("helvetica", "B", 6);

        
        $pdf->Text(152, 17, $text);

        
        
        $pdf->Rect(147, 21, 53, 25);
        $pdf->Text(148, 23,"Paid by Customer No. No du client/compte");
        $pdf->Text(165, 26, "4011243"); // customer number
        $pdf->Text(152, 30, "Method of Payment Mode de paiement");
        $pdf->Text(160, 34, "Account / Porter au Compte");
        $pdf->Text(155, 38, "Contract No. No de la convention");
        $pdf->Text(165, 42, "43546034");
        $pdf->Line(6, 49, 202, 49);
        $pdf->Text(10, 52, "Deposit Summary / Sommaire du d?p?t");
        $pdf->Text(120, 51, "Induction Site #/ Lieu de d?p?t:");
        $pdf->Text(192, 51, "i086");
        $pdf->Text(120, 55, "Shipment Date / Date de l'envoi");
        $pdf->Text(186, 55, date('d/m/Y'));
        $pdf->setFont("helvetica", "", 6);
        $pdf->Text(120, 59, "(The Induction Date may be different. / La date de d?p?t pourrait ?tre diff?rente.)");
        $pdf->write1DBarcode($counterId, 'C128', 16 , 57, '30', 10 , 0.4, '', '');  
        $pdf->setFont("helvetica", "", 7);
        $pdf->Text(20, 68, $counterId);        
        $pdf->Text(10, 72, "Article Number");
        $pdf->Text(10, 75, "Num?ro d'article");
        $pdf->Text(35, 72, "Service Description");
        $pdf->Text(35, 75, "Description du Service");
        $pdf->Text(80, 72, "Pieces");
        $pdf->Text(80, 75, "Articles");
        $pdf->Text(100, 72, "Cubed Items");
        $pdf->Text(100, 75, "Articles cub?s");
     //   $pdf->Text(120, 72, "Value");
      //  $pdf->Text(120, 75, "Value");
        $pdf->Line(6, 80, 202, 80);
        $pdf->setFont("helvetica", "", 7);
        $pdf->Text(25, 80.5, "Expedited Parcel USA? / Colis acc?l?r?s - ?-U ?");
        $pdf->Text(25, 84.3, "Priority/Priorit?");
        $pdf->Text(15, 80.5, "967");
        $pdf->Text(15, 84.5, "1469"); 
        $pdf->Text(15, 88, "Total weight(kg)VE - Poids total(kg)/EV:");
        $pdf->Line(6, 91.5, 202, 91.5);
        $pdf->Text(10, 93, "Deposit Details / Details du depot");
        $pdf->Rect(8, 96, 197, 7);
        $pdf->Text(9, 98, "#");
        $pdf->Line(12, 96, 12, 103);
        $pdf->Text(17, 97, "item id");
        $pdf->Text(13, 100, "No de l'article");                
        $pdf->Line(35, 96, 35, 103);
        $pdf->Text(35, 97, "WT(kg)/VE");
        $pdf->Text(35, 100, "Pds(kg)/EV");                
        $pdf->Line(49, 96, 49, 103);
       $pdf->Text(50, 98, "DEST");                
        $pdf->Line(60, 96, 60, 103);
        $pdf->Text(64, 98, "Options");                
        $pdf->Line(75, 96, 75, 103);

        $pdf->Text(76, 98, "#");
        $pdf->Line(79, 96, 79, 103);
        $pdf->Text(83, 97, "item id");
        $pdf->Text(80, 100, "No de l'article");
        $pdf->Line(101.5, 96, 101.5, 103);
        $pdf->Text(101.5, 97, "WT(kg)/VE");
        $pdf->Text(101.5, 100, "Pds(kg)/EV");      
        $pdf->Line(116, 96, 116, 103);
        $pdf->Text(116, 98, "DEST");     
        $pdf->Line(125, 96, 125, 103);
        $pdf->Text(127, 98, "Options"); 
        $pdf->Line(142, 96, 142, 103);

        $pdf->Text(143, 98, "#");
        $pdf->Line(146, 96, 146, 103);
        $pdf->Text(150, 97, "item id");
        $pdf->Text(148, 100, "No de l'article");
        $pdf->Line(168.5, 96, 168.5, 103);
        $pdf->Text(168, 97, "WT(kg)/VE");
        $pdf->Text(168, 100, "Pds(kg)/EV");    
        $pdf->Line(181.5, 96, 181.5, 103);
        $pdf->Text(182, 98, "DEST");     
        $pdf->Line(190, 96, 190, 103);
        $pdf->Text(193, 98, "Options"); 

        $pdf->setFont("helvetica", "", 8);
        $pdf->Text(10, 200, "The Customer warrants that the order details listed above are");
        $pdf->Text(10, 203, "prepared in accordance with the terms and conditions specified in the"); 
        $pdf->Text(10, 206, "Customer's Agreement and has been validated for accuracy of"); 
        $pdf->Text(10, 209, "information contained within."); 
        $pdf->Text(10, 212, "Le Client garantit que les d?tails de la commande ci-dessus ont ?t?"); 
        $pdf->Text(10, 215, "pr?par?s en conformit? avec les termes et conditions sp?cifi?s dans"); 
        $pdf->Text(10, 218, "l'accord du client et a ?t? valid? pour l'exactitude des renseignements"); 
        $pdf->Text(10, 221, "qu'il contient."); 
        $pdf->setFont("helvetica", "B", 9);
        $pdf->Text(10, 226, "Authorized Customer Signature Signature authoris?e du client");
        $pdf->Text(129, 226, "Acceptance Signature Signature de I'acceptation");
        $pdf->Line(10, 237, 100, 237);
        $pdf->Line(129, 237, 200, 237);
        $pdf->Text(10, 245, "This document must accompany your mailing to the Accepting Location.");
        $pdf->Text(10, 248, "Ce document doit accompagner votre envoi au bureau de d?p?t.");           


        $i = 1 ;
        $x = 9; 
        $y = 104;
        $totalValue = 0; 
        $totalWeight = 0;
        $pdf->SetFont('helvetica','', 6);         


        foreach($consignmentShipmentData as $shipment)
        {              
            $pdf->Text($x-1, $y, $i);                
            $pdf->Text($x+4, $y, $shipment->getAwb());  
            $j++;
            $pdf->Text($x+30, $y, $shipment->getWeight());
            $pdf->Text($x+41, $y, substr($shipment->getPostcode(), 0, 3));                
            if($i%3==0)
            {
                $x = 9; 
                $y = $y + 4;                 
            }
            else
            {
                $x = $x + 67;
            }
            $i++;
            $totalWeight += $shipment->getWeight();
            $totalValue  += $shipment->getValue();
        }

        $pdf->Text(82, 81, $i-1);
        $pdf->Text(60, 88, $totalWeight. "KG");
            
            
    }

    public function manifest($consignmentShipmentData) {        
       
        $i = 3; // three copies of manifest
        
        $pdf = new PdfBase(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);        
        
        for($i=1; $i<=3;$i++)
        {
            
            if($i==1)
            {                
                $text = "Accepting Location Lieu de d?p?t'";                
                $this->CreatePdfManifest($consignmentShipmentData, $pdf, $text);

            }
            else if($i == 2)
            {
                $text = "Customer Copy";                
                $this->CreatePdfManifest($consignmentShipmentData, $pdf, $text);
            }
            else if($i ==3)
            {
                $text = "Data Entry Saisie des donn?es";                       
                $this->CreatePdfManifest($consignmentShipmentData, $pdf, $text);
            }
            
        }
        
        $pdf->Output(SETTING_DIR_ASSETS . "pdf/" . date('Y_m_d') . "/" . uniqid() . ".pdf", "F");            
            
    }                        
    

    // GET MANIFEST ARTIFACT ID 
    public function getManifestArtifactId($manifest_id, $tracking_numbers) {

        $output = [];
        $agentServiceArray = [];
        $consignmentData = new ConsignmentFilter();
        $consignmentData->addFilter("     s.carrier_id= '195'", "servicefilter");
        $consignmentData->addFilter("     c.send_courier_data= '0'", "consignmentfilter");
        if (!empty($tracking_numbers)) {
            $consignmentData->addFilter("AND pc.tracking_number in ('" . implode("','", $tracking_numbers) . "') and pc.tracking_number <> ''", "parcelJoinFilter");
        }
        $consignmentData->addGroupBy(" c.service_id, c.agent_id ");

        $consignmentServiceAgent = $consignmentData->getColumnList("c.service_id, c.agent_id");

        if (count($consignmentServiceAgent) > 0) {
            foreach ($consignmentServiceAgent as $agentData) {
                $agentServiceArray['services'][] = $agentData->getServiceId();
                $agentServiceArray['agent'][] = $agentData->getAgentId();
            }
        } else {
            $output["STATUS"] = "ERROR";
            $output["MESSAGE"] = "Please check we did not find any agent and services for data to send.";
            return $output;
        }

        if (!empty($agentServiceArray['services'])) {

            foreach ($agentServiceArray['services'] as $key => $serviceid) {

                $serviceid = trim($serviceid);
                $agentid = trim($agentServiceArray['agent'][$key]);

                $agentObject = new AgentData($agentid);
                $agentCountryObject = new Country($agentObject->getCountryId());
                $this->serviceValues = new Services($serviceid);

                // GET CONSTANTS AS PER SERVICE AND AGENT
                $serviceAgentConstantFilter = new ServiceConstantValueFilter();
                $serviceAgentConstantFilter->addFilter("service_id = '" . $serviceid . "' AND agent_id = '" . $agentid . "' ");
                $serviceAgentConstant = $serviceAgentConstantFilter->getConstantList("c.constant 'constant_name', scv.constant_value 'constant_value'");
                if (count($serviceAgentConstant) > 0) {
                    foreach ($serviceAgentConstant as $serviceAgentConstantData) {
                        $this->constants[$serviceid][$serviceAgentConstantData->getConstantName()] = $serviceAgentConstantData->getConstantValue();
                    }
                }

                if (!empty($this->constants[$serviceid])) {

                    $constantValue = $this->constants[$serviceid];

                    $this->hostName = $constantValue["API_HOST_NAME"];
                    $wsdl = SETTING_DIR_REMOTE . 'includes/labels/wsdl-canadapost/manifest.wsdl';
                    // SOAP URI
                    $location = 'https://' . $this->hostName . '/rs/soap/manifest/v8';

                    $client = new SoapClient($wsdl, array('location' => $location, 'features' => SOAP_SINGLE_ELEMENT_ARRAYS, 'stream_context' => $this->ctx));
                    $this->WSSENS = 'http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd';
                    // Set WS Security UsernameToken
                    $usernameToken = new stdClass();
                    $usernameToken->Username = new SoapVar($constantValue["API_USERNAME"], XSD_STRING, null, null, null, $this->WSSENS);
                    $usernameToken->Password = new SoapVar($constantValue["API_PASSWORD"], XSD_STRING, null, null, null, $this->WSSENS);
                    $content = new stdClass();
                    $content->UsernameToken = new SoapVar($usernameToken, SOAP_ENC_OBJECT, null, null, null, $this->WSSENS);
                    $header = new SOAPHeader($this->WSSENS, 'Security', $content);
                    $client->__setSoapHeaders($header);

                    try {
                        $mailedBy = $constantValue["API_CUSTOMERNO"];

                        // Execute Request 
                        $result = $client->__soapCall('GetManifestArtifactId', array(
                            'get-manifest-artifact-id-request' => array(
                                'locale' => 'EN',
                                'mailed-by' => $mailedBy,
                                'manifest-id' => $manifest_id
                            )
                                ), NULL, NULL);

                        // Parse Response
                        if (isset($result->{'manifest'})) {
                            $po_number = $result->{'manifest'}->{'po-number'};
                            $artifact_id = $result->{'manifest'}->{'artifact-id'};
                            if ($artifact_id != "") {
                                $manifest_link = $this->getManifestArtifact($artifact_id, $constantValue);
                                //print_r($manifest_link);
                                $output["STATUS"] = "SUCCESS";
                                $output["MESSAGE"] = "../../_assets/pdf/2018_11_30/60226.pdf";
                                return $output;
                                //$output = $manifest_link;
                            }
                        } else {
                            foreach ($result->{'messages'}->{'message'} as $message) {
                                $output["STATUS"] = "ERROR";
                                $output["MESSAGE"] = $message->description;
                                return $output;
                                //echo 'Error Code: ' . $message->code . "\n";
                            }
                        }
                    } catch (SoapFault $exception) {
                        $output["STATUS"] = "ERROR";
                        $output["MESSAGE"] = trim($exception->getMessage());
                        return $output;
                        //echo 'Fault Code: ' . trim($exception->faultcode) . "\n"; 
                    }
                }
            }
        }
    }

    // GET MANIFEST USING ARTIFACT IF
    public function getManifestArtifact($artifact_id, $constantValue) {

        $output = [];
        $wsdl = SETTING_DIR_REMOTE . 'includes/labels/wsdl-canadapost/artifact.wsdl';

        // SOAP URI
        $location = 'https://' . $this->hostName . '/rs/soap/artifact';

        $opts = array('ssl' =>
            array(
                'verify_peer' => true,
                'cafile' => SETTING_DIR_REMOTE . 'includes/labels/wsdl-canadapost/cert/cacert.pem',
                'peer_name' => $this->hostName
            ),
            'http' => array(
                'protocol_version' => 1.0,
            ),
        );

        $this->ctx = stream_context_create($opts);

        $client = new SoapClient($wsdl, array('location' => $location, 'features' => SOAP_SINGLE_ELEMENT_ARRAYS, 'stream_context' => $this->ctx));

        // Set WS Security UsernameToken
        $usernameToken = new stdClass();
        $usernameToken->Username = new SoapVar($constantValue["API_USERNAME"], XSD_STRING, null, null, null, $this->WSSENS);
        $usernameToken->Password = new SoapVar($constantValue["API_PASSWORD"], XSD_STRING, null, null, null, $this->WSSENS);
        $content = new stdClass();
        $content->UsernameToken = new SoapVar($usernameToken, SOAP_ENC_OBJECT, null, null, null, $this->WSSENS);
        $header = new SOAPHeader($this->WSSENS, 'Security', $content);
        $client->__setSoapHeaders($header);

        try {
            $mailedBy = $constantValue["API_CUSTOMERNO"];
            // Execute Request
            $result = $client->__soapCall('GetArtifact', array(
                'get-artifact-request' => array(
                    'locale' => 'EN',
                    'mailed-by' => $mailedBy,
                    'artifact-id' => $artifact_id
                )
                    ), NULL, NULL);

            // Parse Response
            if (isset($result->{'artifact-data'})) {
                // Decoding base64 certificate to a file
                $filename = date("YmdHis") . ".pdf";
                $fileLoc = SETTING_DIR_ASSETS . 'manifest/' . $filename;
                //echo 'Decoding to' . $fileLoc . "\n";
                $fp = fopen($fileLoc, 'w');
                stream_filter_append($fp, 'convert.base64-decode');
                fwrite($fp, $result->{'artifact-data'}->{'image'});
                fclose($fp);
                $output["STATUS"] = "SUCCESS";
                $output["MESSAGE"] = "../_assets/manifest/" . $filename;
                return $output;
            } else {
                foreach ($result->{'messages'}->{'message'} as $message) {

                    //echo 'Error Code: ' . $message->code . "\n";
                    $output["STATUS"] = "ERROR";
                    $output["MESSAGE"] = $message->description;
                }
                return $output;
            }
        } catch (SoapFault $exception) {
            $output["STATUS"] = "ERROR";
            $output["MESSAGE"] = trim($exception->getMessage());
            return $output;
            //echo 'Fault Code: ' . trim($exception->faultcode) . "\n"; 
        }
    }

    public function preAdvice($consignment) {
        
    }

    public function recycledShipment($consignment) {
        $output["STATUS"] = "SUCCESS";
        return $output;
    }
    
    private function SendDataToCanadaPost($consignmentShipmentData, $constantValue)
    {
        $this->record_array = array();
        
        //print_r($constantValue);        
        
        $siteId = $constantValue['CANADA_POST_SITE_ID'];
        
        foreach($consignmentShipmentData as $consignment)
        {
                $this->record_array[] = $this->getCanadaPostOfflineFileRecord($consignment, $siteId);
                $this->lineNumber++;
                $this->count_items++;
        }
        
        //print_r($this->record_array);        
        //die;
        
        
        
                
        if(sizeof($this->record_array) > 0)
        {
            $fftin_file = $this->getLinkFile();
            $run_number = $fftin_file->getId();
            // create full file name
            $file_name = "14H2" .  str_pad(substr("00$run_number", - 4), 9, "0", STR_PAD_LEFT) . ".txt";
            $fftin_file->setFileName($file_name);
            $fftin_file->setSentDate(time());
            $fftin_file->save();
            
            $file_path = SETTING_DIR_ASSETS. "/data_send/canadapost_booking/" . $file_name;
            
            
            $record_count = sizeof($this->record_array) + 2;
            $file_handle = fopen($file_path, 'w');                

            fwrite($file_handle,  $this->getCanadaPostOfflineHeaderRecord($constantValue));                      
            fwrite($file_handle, "\r\n");  


            foreach($this->record_array as $record)
            {
                fwrite($file_handle, $record);                   
            }

            fwrite($file_handle,  $this->getCanadaPostOfflineTrailerRecord());             
            fclose($file_handle);                                          
        }
        
       
        
    }    
    
    private function SendDataToLivingsTon($consignmentShipmentData, $constantValue)
    {
        
        $clientId = $constantValue['CANADA_POST_LIVINGSTON_CLIENT_ID'];
        
        foreach ($consignmentShipmentData as $consignment) 
        {
            $this->record_array[] = $this->getFileRecord($consignment);            
            
            $this->record_array[] = $this->getVendorRecord($consignment);  
            
            $parcel_list = $consignment->getParcels();
            
            $this->count_items = count($parcel_list);

            foreach ($parcel_list as $parcel) 
            {
                $this->record_array[] = $this->getRecordPerItem($consignment, $clientId);
                $this->record_array[] = $this->getRecordPerPackage($consignment, $clientId);                                
            }                        
            
        }
        
        if(sizeof($this->record_array) > 0)                    
        {
                $this->record_array[] = $this->getTrailorRecord();
                
                 

              //print_r($this->record_array); die;
                $fftin_file = $this->getLinkFile();
                $run_number = $fftin_file->getId();

                // create full file name
                $file_name = "PSOW" .  str_pad(substr("00$run_number", - 4), 9, "0", STR_PAD_LEFT);
                $fftin_file->setFileName($file_name);
                $fftin_file->setSentDate(time());
                $fftin_file->save();
                $file_path = SETTING_DIR_ASSETS. "data_send/canadapost_booking/" . $file_name;
                
                
                $record_count = sizeof($this->record_array) + 2;
                $file_handle = fopen($file_path, 'w');
                

                foreach($this->record_array as $record)
                {
                        fwrite($file_handle, $record);                   

                }
                
                fclose($file_handle);
                
                
                //echo "File Path " . $file_path;
                //print_r($this->record_array);                
                //die;

                $ftp_object = new FTPfile("1WorldExpress", "_6pRA#5von", "ftpca.livingstonintl.com");
                $file_name = $fftin_file->getFileName();
                $remote_file_path="/1WorldExpress/".$fftin_file->getFileName();
                
                //$isFTPUpload = $ftp_object->put($remote_file_path, $file_path, FTP_BINARY) ;

                if(!$isFTPUpload)
                {
                        mail("itsupport@oneworldexpress.com", "CanadaPost FTP Upload Failed", "CanadaPost FTP Upload Failed" . $file_name);                                      
                }


        }        
        
            

    }    
    
    private function getCanadaPostOfflineHeaderRecord($constantValue)
    {
        $delimiter = ",";
        
        $siteId = $constantValue['CANADA_POST_SITE_ID'];        
        $customerNumber = $constantValue['API_CUSTOMERNO'];
        $contractNumber = $constantValue['API_CONTRACTNO'];
        
        
        $record = "\"14F\"" . $delimiter; // version 1
        $record .= '"'.$siteId.'"' . $delimiter; // site id 2
        $record .= "\"i086\"" . $delimiter; // induction site 3        
        $record .= "\"F4412914711257\"" . $delimiter;        

        $record .= '""' . $delimiter; // customer reference number 5
         //echo $record; 
        $record .= '"'.date("dmY").'"'. $delimiter; // mailing date  6
        $record .= '"'.$customerNumber .'"'. $delimiter; // customer number 7            
        $record .= '"'.$contractNumber .'"'. $delimiter; // customer number 8            
        $record .= "\"0\"" . $delimiter;   // billing 9
        $record .= "\"0\"" . $delimiter;  // pick-up for a free indicator  10
        $record .= "\"1\"" . $delimiter; // inbound freight 11
        $record .= '"0"'; // status 12

       return $record; 
    }
        
    private function getCanadaPostOfflineTrailerRecord()
    {
           $delimeter = ",";
           
           $record = "";
           $record .= "\"14T\"" . $delimeter;
           $record .= '"'.sizeof($this->record_array).'"'. $delimeter;
           $record .= "\"\"" . $delimeter; 
           $record .= "\"\"" . $delimeter; 
           $record .= "\"\"" . $delimeter; 
           $record .= "\"\"" . $delimeter; 
           $record .= "\"\"";            
           return $record; 
        
    }

    private function getCanadaPostOfflineFileRecord(Consignment $consignment, $lineNumber)
    {
            
           
            $state       =             trim($consignment->getState());  

            if ($state == 'Newfoundland and Labrador')
            {
                $state = "NL";
            }

            else if($state == "Prince Edward Island")
            {
                $state = "PE";
            }

            else if($state == "Nova Scotia")
            {
                $state = "NS";
            }

            else if($state == "Quebec") 
            {
                $state = "QC";
            }

            else if(trim($state) == "Ontario")
            {
                $state = "ON";
            }

            else if($state == "Manitoba")
            {
                $state = "MB";
            }

            else if($state == "Saskatchewan")
            {
                $state = "SK";
            }

            else if($state == "Alberta")
            {
                $state = "AB";
            }

            else if($state == "British Columbia")
            {
                $state = "BC";
            }

            else if($state == "Yukon")
            {
                $state = "YT";
            }

            else if($state == "Northwest Territories")
            {
                $state = "NT";
            }

            else if($state == "Nunavut")
            {
                $state = "NU";
            }

            $length = "";
            $width = "";
            $height = ""; 

            $parcels = $consignment->getParcels(false);
            if(count($parcels) > 0)
            {
                $parcel = $parcels[0];
                $length = $parcel->getLength();
                $width = $parcel->getWidth();
                $height = $parcel->getHeight();                    
            }
            if($consignment->getWeight() != '')
            {
                $weightInGms = $consignment->getWeight() * 1000;
            }


            $delimiter = ",";


            $record .= "\"14D\"" . $delimiter; // D1
            $record .= '"'.$lineNumber.'"' . $delimiter; // D2 manifest line number 15 
            $record .= '"'.$consignment->getTrackingNumber().'"'. $delimiter; // D3 16 tracking number

            $record .= "\"967\"" . $delimiter; // article number // 17 domestic usa
            $record .= '"'.$weightInGms.'"'. $delimiter; // 18 weight in grams                

            $volWeight = ($length*$width*$height)/5000; 
            $record .= '"'.$volWeight.'"'. $delimiter; // vomumetric weight

         
            if($length > 0)
            {
                $record .= '"'.$length.'"'. $delimiter; // 21 mm
            }
            else
            {
                $record .= "\"\"" . $delimiter; // 21 mm
            }
            if($width >0)
            {
                $record .= '"'.$width.'"'. $delimiter; //  22 mm
            }
            else
            {
                $record .= "\"\"" . $delimiter; // 21 mm
            }
            if($height >0)
            {
                $record .= '"'.$height.'"'. $delimiter; // 23 mm
            }
            else
            {
                $record .= "\"\"" . $delimiter; // 21 mm
            }

            $record .= '"'.$consignment->getAddressLine1().'"'. $delimiter; // 24
            $record .= '"'.$consignment->getAddressLine2().'"'. $delimiter; // 25
            $record .= '"'.$consignment->getCity().'"'. $delimiter; // 26
            $record .= '"'.$state.'"'. $delimiter; // 27 /// need to check 
            $record .= '"'.$consignment->getPostCode().'"'. $delimiter; // 28

            $record .= '"'.$consignment->getCountryIsoCode().'"'. $delimiter; // 29
            $record .= "\"0\"" . $delimiter; // 30 optional shipping charges
            $record .= "\"0\"" . $delimiter;  // 31 optional charges
            $record .= "\"0\"" . $delimiter; // 32 optional Cod indicator               
            $record .= "\"0.00\"" . $delimiter; // 33 cod value 19

            $record .= "\"0\"" . $delimiter; // 35 signature optional 20
            $record .= "\"0\"" . $delimiter; // 36 oversize optional 21
            $record .= "\"0\"" . $delimiter; // 37 unpackaged 22
            $record .= "\"0\"" . $delimiter; // 38 coverage indicator 23
            $record .= "\"0.00\"" . $delimiter; // 39 coverage amount 24

            $record .= "\"1\"". $delimiter; // 40 delivery confirmatio indicator 25
            $record .= "\"0\"" . $delimiter; // 43 proof of age 26
            $record .= "\"0\"" . $delimiter; // 43 proof of age 27
            $record .= "\"0\"" . $delimiter; // 43 proof of age 28
            $record .= "\"0\"" . $delimiter; // 46 safe drop 27
            $record .= "\"0\"" . $delimiter; // 47 card for pickup
            $record .= "\"0\"" . $delimiter; // 48 leave to door
            $record .= "\"0\"" . $delimiter; // 49 proof of age
            $record .= "\"\"".$delimiter; // 50 sender email 1
            $record .= "\"\"".$delimiter; // 51 sneder email 2                 
            $record .= '"'.$consignment->getEmail() . '"'.$delimiter; // 52 receiver email
            $record .= "\"0\"". $delimiter; // Deliver to Door 55
           // $record .= "\"\"".$delimiter; // Receiver Name 56
            $record .= "\"\"".$delimiter; // Receiver Phone #57
            $record .= "\"\"".$delimiter; // Receiver Email 2
            $record .= "\"\"".$delimiter; // PIN Reference 
            $record .= "\"\"".$delimiter; //  PIN Reference 2
            $record .= "\"\"".$delimiter; //  Carded Only 
            $record .= "\"\"".$delimiter; // Cost Centre Reference Number
            $record .= "\"\"".$delimiter; // Mailing Tube Indicator
            $record .= "\"\"".$delimiter; // Proof of Identity
            $record .= "\"\"".$delimiter; // Deliver to Post Office Flag
            $record .= "\"\"".$delimiter; // Post Office ID
            $record .= "\"\"".$delimiter; // SMS 
            $record .= '"'.$consignment->getContact().'"'. $delimiter;  // 71
            $record .= '"'.$consignment->getAddressLine1().'"'. $delimiter; // 72                
            $record .= '"'.$consignment->getAddressLine2().'"'. $delimiter; // 73
            $record .= '"'.$consignment->getCity().'"'. $delimiter;  // 74
            $record .= '"'.$state.'"'. $delimiter; // 75
            $record .= '"'.$consignment->getPostCode().'"'. $delimiter; // 76
            $record .= '"'.$consignment->getCountryIsoCode().'"'. $delimiter; // 77
            $record .= '"'.$consignment->getCompany().'"'. $delimiter; // 78
            $record .= '"0"';                
            $record .= "\r\n";
            return $record;
    }
    
    private function getFileRecord(Consignment $consignment, $clientId) {

        $record = "";
        $record .= $this->fld(1, "O");
        $record .= $this->fld(8, $clientId); // constants 
        $record .= $this->fld(20, $consignment->getHawb());
        $record .= $this->fld(35, $consignment->getContact());
        $record .= $this->fld(35, utf8_decode($consignment->getAddressLine1()));
        $record .= $this->fld(35, utf8_decode($consignment->getAddressLine2()));
        $record .= $this->fld(25, utf8_decode($consignment->getCity()));
        $record .= $this->fld(2, $consignment->getState());
        $record .= $this->fld(10, str_replace(" ", "", $consignment->getPostCode()));
        $record .= $this->fld(3, $this->senderCountryObj->getIso()); /// country iso code
        $record .= $this->fld(10, "");
        $record .= $this->fld(8, date("Ymd"));
        $record .= "\r\n";
        return $record;
    }

    private function getVendorRecord(Consignment $consignment, $clientId) {
        $record = "";
        $record .= $this->fld(1, "V");
        $record .= $this->fld(8, $clientId);
        $record .= $this->fld(20, $consignment->getHawb());
        $record .= $this->fld(35, $consignment->getSenderName()); // vendor name
        $record .= $this->fld(35, $consignment->getSenderAddressLine1()); // vendor address line 1
        $record .= $this->fld(35, $consignment->getSenderAddressLine2()); // vendor address line 2
        $record .= $this->fld(25, $consignment->getSenderCity()); // sender city
        $record .= $this->fld(2, $consignment->getState());
        $record .= $this->fld(10, $consignment->getSenderPostCode());
        $record .= $this->fld(3, $this->senderCountryObj->getIso());
        $record .= $this->fld(36, "");
        $record .= "\r\n";
        return $record;
    }

    private function getTrailorRecord($clientId) {
        $record = "";
        $record .= $this->fld(1, "T");
        $record .= $this->fld(8, $clientId);
        $record .= $this->fld(6, date("ymd"));
        //$record .= $this->fld(3, "RR" . $this->getRandomChracter()); /// DDP
        $record .= $this->fld(3, "SS" . $this->getRandomChracter()); /// DDU
        $record .= $this->fld(11, "");
        $record .= $this->fld(6, $this->count_orders, true);
        $record .= $this->fld(6, $this->count_items, true);
        //$record .= $this->fld(6, sizeof($this->bagNumber_array),true);
        //$record .= $this->fld(6, $this->count_packages,true);
        $record .= $this->fld(6, $this->count_orders, true);


        $record .= $this->fld(163, "");
        $record .= "\r\n";
        return $record;
    }

    private function getRecordPerPackage(Consignment $consignment, $clientId) {
        $weightArray = explode(".", $consignment->getWeight());
        $weightArrayPart1 = str_pad($weightArray[0], 4, "0", STR_PAD_LEFT);

        $weightArrayPart1 = $this->fld(4, $weightArrayPart1, true);

        $weightArrayPart2 = str_pad($weightArray[1], 4, "0", STR_PAD_RIGHT);


        $weightArrayPart2 = $this->fld(4, $weightArrayPart2, true);

        $record .= $this->fld(1, "P");
        $record .= $this->fld(8, $clientId);
        $record .= $this->fld(20, $consignment->getHawb());
        $record .= $this->fld(20, $consignment->getBagNumber()); // package id 
        $record .= $this->fld(8, $weightArrayPart1 . $weightArrayPart2);
        $record .= $this->fld(1, "K"); // unit of measure
        $record .= $this->fld(1, ""); // over size code
        $record .= $this->fld(5, "");  // insurance amount
        $record .= $this->fld(3, ""); // insuranc currency code
        $record .= $this->fld(3, ""); // carrier code
        $record .= $this->fld(3, ""); // shipment option
        $record .= $this->fld(5, ""); // freight cost
        $record .= $this->fld(25, ""); // manifest id
        $record .= $this->fld(107, ""); // filler

        $record .= "\r\n";
        return $record;
    }

    private function getRecordPerItem(Consignment $consignment, $clientId) {

        $valueArray = explode(".", $consignment->getValue());
        $valuePart1 = $valueArray[0];
        $valuePart1 = $this->fld(5, $valuePart1, true);

        $valuePart2 = $valueArray[1];
        $valuePart2 = $this->fld(2, $valuePart2, true);

        $record .= $this->fld(1, "I");
        $record .= $this->fld(8, $clientId);
        $record .= $this->fld(20, $consignment->getHawb());
        $record .= $this->fld(25, substr($consignment->getDescription(), 0, 15)); //SKU/
        $record .= $this->fld(50, $consignment->getDescription());
        $record .= $this->fld(5, $consignment->getNumberPieces(), true);
        $record .= $this->fld(7, $valuePart1 . $valuePart2);
        $record .= $this->fld(3, $consignment->getCurrency());
        $record .= $this->fld(10, ""); // H.S
        $record .= $this->fld(2, "CA");
        $record .= $this->fld(1, ""); // pst
        $record .= $this->fld(30, ""); // pst
        $record .= $this->fld(48, ""); // pst

        $record .= "\r\n";
        return $record;
    }

    private function fld($len, $data, $numerical_flag = false) {
        $fld = "";
        // Char fields should be left justified and space filled to the end of the field and in UPPERCASE at all times.
        // Integer fields should be right justified and zero filled to the start of the field.
        if ($numerical_flag) {
            $fld = substr(str_repeat("0", $len) . $data, 0 - $len); // take from right, -ve start pos.
        } else {
            $fld = substr($data . str_repeat(" ", $len), 0, $len);
        }
        return strToUpper($fld);
    }

    private function getLinkFile() {

        if ($this->link_file == null || trim($this->link_file) == '') {
            // use id as run number
            $fftin_file = new EuroDayDefFile();
            $fftin_file->setFileName("temp");
            $fftin_file->setSentDate(time(0)); // => need valid date; can also be solved later in Dbaccess3
            $fftin_file->save();
            $run_number = $fftin_file->getId();
            $file_name = "OneWorld-manifest-" . $run_number;
            $fftin_file->setFileName($file_name);
            $fftin_file->setSentDate(time());
            $fftin_file->save();
            $this->link_file = $fftin_file;
        }
        return $this->link_file;
    }

    private function getRandomChracter() {
        $seed = str_split('ABCDEFGHIJKLMNOPQRSTUVWXYZ' . '0123456789');
        shuffle($seed);
        $rand = '';
        foreach (array_rand($seed, 5) as $k) {
            $rand .= $seed[$k];
            return $rand;
        }
    }

}
