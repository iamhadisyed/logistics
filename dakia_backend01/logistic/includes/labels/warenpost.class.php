<?php

include_classes([
    'warenpostgazetteer.class',
    'warenpostgazetteerfilter.class'
]);

class Warenpost implements CarrierService {

    private $pdf;
    private $serviceValues = null;
    private $constants = null;
    private $user = null;
    private $country = null;
    private $recordArray = null;
    private $totalWeight = null;

    public function __construct() {
        
    }

    public function validation(Consignment $consignment, Services $service, Country $country) {
       
    }

    public function remoteareas($consignment, $carrierObject, $sender) {
        
    }

    public function label($consignment, $labelType = 'pdf', $size = '100x150') {

        $output = array();
        $this->user = SessionManager::getUser();

        $this->serviceValues = new Services($consignment->getServiceId());
        $this->country = new Country($consignment->getCountryId());
        
        /*
         * Service COnstant */

          $serviceAgentConstantFilter = new ServiceConstantValueFilter();
          $serviceAgentConstantFilter->addFilter("service_id = '" . $consignment->getServiceId() . "' AND agent_id = '" . $consignment->getAgentId() . "' ");
          $serviceAgentConstant = $serviceAgentConstantFilter->getConstantList("c.constant 'constant_name', scv.constant_value 'constant_value'");
          if (count($serviceAgentConstant) > 0) {
          foreach ($serviceAgentConstant as $serviceAgentConstantData) {
          $this->constants[$serviceAgentConstantData->getConstantName()] = $serviceAgentConstantData->getConstantValue();
          }
          }
          //print_r($serviceAgentConstant); die;
          if (trim(@$this->constants['WARENPOST_ACCOUNT']) == '' || trim(@$this->constants['WARENPOST_PASSWORD']) == '' || trim(@$this->constants['WARENPOST_USERNAME']) == '') {
          $output['STATUS'] = 'ERROR';
          $output['MESSAGE'] = "Please check service constants. Please contact to itsupport@oneworldexpress.com";
          return $output;
          }
         
          if($this->constants['INTEGRATION_TYPE'] == 'API')
          {
              $output = $this->apiLabel($consignment);
          }
          //print_r($this->constants); die;
        /*
         *  Get Tracking Number ranges


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
          $licencePlate = new LicencePlate($licence_plate_id);
         */
/*
        $parcel_list = $consignment->getParcels();
        $parcel_count = sizeof($parcel_list);
        $parcel_idx = 0;
        $pdf = new PdfBase(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $this->pdf = $pdf;
        // Generate label for each parecel
        foreach ($parcel_list as $parcel) {
            if ($parcel->getTrackingNumber() == '') {
                $resultArray = LicencePlate::getLicencePlateNumber($licence_plate_id);

                if (trim($resultArray['STATUS']) == 'ERROR')
                    return $resultArray;
                else {
                    $licence_plate = $resultArray["PREFIX"] . $resultArray["RANGE"] . $resultArray["SUFIX"];
                }
                $parcel->setTrackingNumber($licence_plate);
                $parcel->save();
            } else {
                $licence_plate = $parcel->getTrackingNumber();
            }
            $licence_plate_array[$parcel_idx] = $licence_plate;

            $this->pdf->SetPrintFooter(false);
            $this->pdf->SetFooterMargin(0);
            $this->pdf->SetAutoPageBreak(false, 0);
            $page_size = array(100, 150);
            $this->pdf->AddPage("P", $page_size);
            $this->addWayBill($consignment, $parcel_idx, $licence_plate);
            ++$parcel_idx;
        }

        $this->pdf->IncludeJS("print();");
        $this->pdf->Output("../_assets/pdf/" . date('Y_m_d') . '/' . $consignment->getId() . ".pdf", "F");
        $output['STATUS'] = 'SUCCESS';
        $output['LABEL'] = date('Y_m_d') . '/' . $consignment->getId() . ".pdf";
        $output['TRACKING_NUMBER'] = $licence_plate_array;*/
        return $output;
    }

    public function tracking($trackingNumber, $trackBy = 'parcel', $EDI = false) {
        
    }
    public function apiLabel($consignment)
    {
        try 
        {
            $IntraShip = new SoapClient("https://cig.dhl.de/cig-wsdls/com/dpdhl/wsdl/geschaeftskundenversand-api/3.1/geschaeftskundenversand-api-3.1.wsdl",
            array('login' => 'testAppOwe_1',
                'password' => 'ZF8DTMgTPVYrY8agG8Cx0Ntt8p22y7',
                'location' => 'https://cig.dhl.de/services/production/soap',
                'soap_version' => SOAP_1_1,
                    'exceptions' => false,
                    'trace' => 1));
        } 
        catch (Exception $e) 
        {
            $output['STATUS'] = "ERROR";
            $output['MESSAGE'] = "DHL DE is not working, CIG authentication failed. Please contact to itsupport@oneworldexpress.com ";
        }
        
        $parcelFilter = new ParcelFilter();
        $parcelFilter->addFieldFilter("consignment_id",$consignment->getId());
        $parcelResult = $parcelFilter->getList();
        
        $country = new Country($consignment->getCountryId());
        $receiverCountry = $country->getIso();
        
        $auth = new stdClass();
        $auth->user = $this->constants['WARENPOST_USERNAME'];
        $auth->signature = $this->constants['WARENPOST_PASSWORD'];
        $auth->ekp = $this->constants['WARENPOST_ACCOUNT'];
        
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
                                'accountNumber' => $this->constants['WARENPOST_ACCOUNT'],
                                'customerReference' => $consignment->getHawb(),
                                'shipmentDate' => date('Y-m-d'),
                                'costCentre' => '',
                                'ShipmentItem' =>array(
                                    'weightInKG' => $parcelResult[0]->getWeight(),
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
                                        'countryISOCode' => $receiverCountry
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
        $shResponse = $IntraShip->__soapCall(createShipmentOrder, array($requestArray));
        
        //print_r($shResponse); 
        if($shResponse->Status->statusText != 'ok')
        {
            $output['STATUS'] = 'ERROR';
            if($shResponse->CreationState->LabelData->Status->statusMessage != '')
            {                
                $output['MESSAGE'] = implode('</br>',$shResponse->CreationState->LabelData->Status->statusMessage);
            }
            else
            {
                $output['MESSAGE'] = $shResponse->Status->statusText;
            }
        }
        else
        {            
            if($shResponse->CreationState->LabelData->Status->statusText != 'ok')
            {
                $output['STATUS'] = 'ERROR';
                $output['MESSAGE'] = $shResponse->CreationState->LabelData->Status->statusMessage;
            }
            else
            {
                $labelResponse = $shResponse->CreationState->LabelData->labelUrl;
                $trackingNumberArray[] = $shResponse->CreationState->shipmentNumber;
                $fileName = "../_assets/pdf/" . date('Y_m_d') . '/' . $consignment->getId() . ".pdf";
                file_put_contents($fileName, file_get_contents($labelResponse));
                $output["STATUS"] = 'SUCCESS';
                $output["LABEL"] = date('Y_m_d') . '/' . $consignment->getId() . ".pdf";
                $output["TRACKING_NUMBER"] = $trackingNumberArray;
            }
        }
        //print_r($output); die;
        return $output; 
    }
    
    public function sendData($tracking_numbers = array()) {
        $output = [];
        $agentServiceArray = [];
        $consignmentData = new ConsignmentFilter();
        $consignmentData->addFilter("     s.carrier_id= '187'", "servicefilter");
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
        }

        if (!empty($agentServiceArray['services'])) {

            foreach ($agentServiceArray['services'] as $key => $serviceid) {

                $serviceid = trim($serviceid);
                $agentid = trim($agentServiceArray['agent'][$key]);

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
                    // GET ALL THE CONSIGNMENT WITH THE PARCEL FOR PREPARE  FILE
                    $consignmentShipmentDataFilter = new ConsignmentFilter();
                    $consignmentShipmentDataFilter->addFilter("     c.service_id = '" . $serviceid . "' AND c.agent_id = '" . $agentid . "' ", "consignmentfilter");
                    $consignmentShipmentDataFilter->addFilter("     c.send_courier_data= '0'", "consignmentfilter");
                    if (!empty($tracking_numbers))
                        $consignmentShipmentDataFilter->addFilter("     AND pc.tracking_number in ('" . implode("','", $tracking_numbers) . "') and pc.tracking_number <> ''", "parcelJoinFilter");
                    $consignmentShipmentData = $consignmentShipmentDataFilter->getColumnList(" c.id 'consignment_id', s.carrier_id ,s.code 'service_code',
                     con.region 'country_region', c.service_id, c.weight, c.hawb, c.description, c.company, c.country_id,c.awb,c.date_created,c.value,
                      c.contact, c.address_line_1, c.address_line_2, c.city, c.postcode, c.telephone, c.other_routing_code, routing_code_eur, c.number_pieces ");

                    if (count($consignmentShipmentData) > 0) {
                        $consignmentIdArray = [];
                        $count = 1;
                        foreach ($consignmentShipmentData as $consignmentItemData) {

                            $consignmentId = $consignmentItemData->getConsignmentId();
                            $consignmentIdArray[] = $consignmentId;
                            $carrierId = $consignmentItemData->getCarrierId();
                            $this->totalWeight += $consignmentItemData->getWeight();
                            $this->recordArray[] = $this->shipmentRecord($consignmentItemData, $this->constants[$serviceid], $count);
                            $count++;
                        }
                        $carrierId = $this->serviceValues->getCarrierId();
                        $agentid = trim($agentServiceArray['agent'][$key]);

                        $this->booking_file = "1234567890TTVVnnn" . date("Ymd", time());

                        if ($this->sendBookings($this->constants[$serviceid])) {
                            if (!empty($consignmentIdArray)) {

                                $sql = "UPDATE consignment SET send_courier_data=1, booked_file_id = '" . $this->booking_file . "' 
                                        WHERE id IN ('" . implode("','", $consignmentIdArray) . "')  AND id <> '0' 
                                        AND  shipment_status not in ('" . Consignment::STATUS_RECYCLED . "','" . Consignment::STATUS_READY_TO_PRINT . "','" . Consignment::STATUS_INVALID . "')";
                                DbAccess3::runQuery($sql);
                                $output["STATUS"] = "SUCCESS";
                                $output["MESSAGE"] = "System has successfully send data.";
                            } else {
                                $output["STATUS"] = "ERROR";
                                $output["MESSAGE"] = "No consignment found to send data to carrier.";
                            }
                        } else {
                            $output["STATUS"] = "ERROR";
                            $output["MESSAGE"] = "Please check constants, System not able to find FTP details to send data to fastway. Please fix it ASAP";
                        }
                    } else {
                        
                    }
                }
            }
        }
    }

    public function manifest($consignment) {
        
    }

    public function preAdvice($consignment) {
        
    }

    private function addWayBill(Consignment $consignment, $parcel_idx, $licence_plate) {

        $this->pdf->line(1, 1, 99, 1);
        $this->pdf->line(1, 1, 1, 149);
        $this->pdf->line(1, 149, 99, 149);
        $this->pdf->line(99, 1, 99, 149);
        $this->pdf->line(1, 15, 99, 15);


        $deuschepostLogo = realpath("../images/deutschepost.png");

        $y = 1;
        $x = 5;
        $w = 40;
        $h = 20;
        $this->pdf->image($deuschepostLogo, $x, $y, $w, $h, '', '', '', false, 700, '', false, false, 0, '', false, false);


        $this->pdf->setFont("ARIAL", "b", 11);
        $this->pdf->Text(50, 5, "WARENPOST");

        $this->pdf->setFont("ARIAL", "", 8);
        $y = 14;
        $this->pdf->Text(3, $y += 3, "Von:   " . "One World Express Inc");
        $this->pdf->Text(11, $y += 3, "One Word House");
        $this->pdf->Text(11, $y += 3, "Hayes");
        $this->pdf->Text(11, $y += 3, "Pump lane");
        $this->pdf->Text(11, $y += 3, "53113 Bonn");
        $this->pdf->Text(70, 20, "Kontakt Absender:");
        $this->pdf->Text(70, 23, "0228 182-25414");


        //top left corner border
        $this->pdf->Line(2, 33, 7, 33);
        $this->pdf->Line(2, 33, 2, 38);
        //top bottom corner border
        $this->pdf->Line(2, 55, 7, 55);
        $this->pdf->Line(2, 50, 2, 55);
        //top right corner border
        $this->pdf->Line(63, 33, 68, 33);
        $this->pdf->Line(68, 33, 68, 38);
        //top right corner border
        $this->pdf->Line(63, 55, 68, 55);
        $this->pdf->Line(68, 50, 68, 55);

        $this->pdf->setFont("ARIAL", "", 9);

        $y = $y += 2;
        $this->pdf->Text(3, $y += 3, "An:   " . utf8_encode($consignment->getContact()));
        if ($consignment->getCompany() != "") {
            $this->pdf->Text(11, $y += 3, utf8_encode($consignment->getCompany()));
        }
        $this->pdf->Text(11, $y += 3, utf8_encode($consignment->getAddressLine1()));
        if ($consignment->getAddressLine2() != "") {
            $this->pdf->Text(11, $y += 3, utf8_encode($consignment->getAddressLine2()));
        }
        if ($consignment->getAddressLine3() != "") {
            $this->pdf->Text(11, $y += 3, utf8_encode($consignment->getAddressLine3()));
        }
        $this->pdf->Text(11, $y += 3, $consignment->getPostcode() . " " . utf8_encode($consignment->getCity()));
        $this->pdf->Text(11, $y += 3, $this->country->getName());
        $this->pdf->setFont("ARIAL", "", 8);
        $this->pdf->Text(70, 35, "Kontakt Empfanger:");
        $this->pdf->Text(70, 38, $consignment->getTelephone());

        $this->pdf->line(1, 56, 98, 56);
        $this->pdf->line(1, 62, 98, 62);
        $this->pdf->line(1, 73, 98, 73);
        $this->pdf->line(1, 90, 98, 90);
        $this->pdf->line(1, 97, 98, 97);
        $this->pdf->line(33, 90, 33, 97);
        $this->pdf->line(66, 90, 66, 97);


        $this->pdf->Text(3, 63, "Abrechnungsnr.:  62953184106201");
        $this->pdf->Text(3, 66, "Referenzner.:  " . $consignment->getHawb());
        $this->pdf->Text(3, 69, "Sendungsnr.:  " . $consignment->getAwb());

        $this->pdf->Text(60, 63, "Gewicht:");
        $this->pdf->Text(85, 63, "Anzahl:");
        $this->pdf->setFont("ARIAL", "B", 10);
        $this->pdf->Text(60, 66, number_format($consignment->getWeight(), 2));
        $this->pdf->Text(85, 66, $consignment->getNumberPieces());

        $warenpostlogo = realpath("../images/warenpost_logo.png");

        $y = 74;
        $x = 5;
        $w = 60;
        $h = 15;
        $this->pdf->image($warenpostlogo, $x, $y, $w, $h, '', '', '', false, 700, '', false, false, 0, '', false, false);

        //$this->pdf->setFont("ARIAL", "", 10);
        //$this->pdf->Text(3, 91, "GO GREEN");
        
        $routingCode = explode("||", $consignment->getOtherRoutingCode());
        $aLort = $routingCode[0];
        $widestreetCode = $routingCode[1];
        $hnr1000 = sprintf("%03d", $routingCode[2]);
        $streetCode = $routingCode[3];
        $townCode = $routingCode[4];
        $houseno = $routingCode[5];
        
        $postcode = str_replace(" ", "", $consignment->getPostcode());
        $routineBarcode = $postcode . $streetCode .$hnr1000. "62";
        
        $checkDigit = $this->checkDigitRoutineBarcode($routineBarcode);
        $routineBarcode = $routineBarcode . $checkDigit;
        
        $style = array(              
                'border' => false,
                'hpadding' => 'auto',
                'vpadding' => 1,
                'fgcolor' => array(0,0,0),
                'bgcolor' => false, //array(255,255,255),
                'text' => true,
                'font' => 'helvetica',
                'fontsize' => 12,
                'stretchtext' => 1                     
        );
        
        $this->pdf->write1DBarcode($routineBarcode, 'I25', 5, 98, '80', 25, 0.5, $style, 'Y');
        
        $this->pdf->write1DBarcode("CY707999002DE", 'C128', 5, 125, '80', 20, 0.5, $style, 'Y');
        
        return;
    }
    
    

    private function sendBookings($ftpConstants) {
        $isUpload = true;
        if (sizeof($this->recordArray) > 0) {

            $path = SETTING_DIR_ASSETS . "data_send/warenpost_booking/" . date("Y_m_d") . "/";
            if (!file_exists($path))
                @mkdir($path, 0777, true);

            $file_path = $path . $this->booking_file . ".dat";
            chmod($path, 0777);



            // create file
            $file_handle = fopen($file_path, 'w');

            // Header Record
            $headerRecord = $this->headerRecord();
            fwrite($file_handle, $headerRecordrecord);

            // Message Header Record
            $messageHeaderRecord = $this->messageHeaderRecord(count($this->recordArray));
            fwrite($file_handle, $messageHeaderRecord);

            //Partner Role Record
            $partnerRecord = $this->partnerRoleRecord();
            fwrite($file_handle, $partnerRecord);


            foreach ($this->recordArray as $record) {
                fwrite($file_handle, $record);
            }

            //Message End Record
            $messageEndRecord = $this->messageEndRecord(count($this->recordArray));
            fwrite($file_handle, $messageEndRecord);

            //Footer Record
            $footerRecord = $this->footerRecord(1);
            fwrite($file_handle, $footerRecord);

            // close file
            fclose($file_handle);

            $this->recordArray = NULL;
        }

        if ($isUpload === false) {
            mail("itsupport@oneworldexpress.com", "BRT LOGIN FAILED", "BRT LOGIN FAILED" . $this->brtitaly_VAB_file);
        }
        return $isUpload;
    }

    private function headerRecord() {
        $record = "";
        $record .= "SADK;"; // Satzart [Recordtype]
        $record .= "320;"; //Version of the CSV format
        $record .= "1234567890;"; //ICR Number
        $record .= "21" . date("ymd") . ";"; //File date CCYYMMDD
        $record .= date("Hi") . ";"; //File time
        $record .= "1234567890;"; //Customer number of the creator of this transmission file EKP No
        $record .= "SMARTTRACK;"; //Name and version of the software used to create the posting list
        $record .= "DPAG-EDICC;"; //Recipient of the file Constant: DPAG-EDICC 
        $record .= "1;"; //1 = test message; empty = live;
        $record .= "\r\n";
        return $record;
    }

    private function messageHeaderRecord($totalCount) {
        $record = "";
        $record .= "SANK;"; // Message header record first record of the message
        $record .= "1;"; //Unique message reference of sender. Always 1
        $record .= "21" . date("ymd") . ";"; //Date of posting/handover
        $record .= "1234567890;"; //EKP number of the customer; this number is mandatory.
        $record .= "62" . ";"; //Specification of the procedure
        $record .= "01;"; //This field contains the twodigit participation number.
        $record .= ";"; //The Postexpress customer number (not the EKP) may only be used in procedure 72. Otherwise the field remains empty.
        $record .= "07;"; //Place of acceptance
        $record .= "6363;"; //Number of the parcel centre
        $record .= number_format($this->totalWeight, 3) . ";"; //Total weight of all items on the posting list in kilograms
        $record .= ";"; //Number of all parcels in this message. Mandatory field for DHL Europaket 
        $record .= ";"; //Number of all pallets in this message. Always empty
        $record .= $totalCount . ";"; //Number of shipments
        $record .= ";"; //Shipment Number Always empty
        $record .= ";"; //Customer specific nummber for the order
        $record .= ";"; //Poster's reference number for the entire posting
        $record .= "\r\n";
        return $record;
    }

    private function partnerRoleRecord() {
        $record = "";
        $record .= "SAPA;"; // Partner Role Record
        $record .= "OY;"; // Type of partner role
        $record .= "1234567890;"; //EKP no. of the respective partner role
        $record .= "One World Express;"; //Name field 1
        $record .= ";"; //Name field 2
        $record .= ";"; //Name field 3
        $record .= "One Wolrd House"; //Street
        $record .= ";"; //House Number
        $record .= "Frankfurt;"; // Town City
        $record .= "72072;"; //Post Code
        $record .= "DE;"; //Country
        $record .= "02088676060;"; //Name or department of the contact
        $record .= ";"; //Name or department of the contact
        $record .= ";"; //Phone of the contact
        $record .= ";"; //Fax    of the contact
        $record .= "cs@oneworldexpress.com;"; //Email     
        $record .= ";"; //VAT No
        $record .= ";"; //EXW account no.
        $record .= ";"; //Region
        $record .= "\r\n";
        return $record;
    }

    private function shipmentRecord($consignment, $constant, $count) {
        $country = new Country($consignment->getCountryId());
        $record = "";
        $record .= "SAPO;";
        $record .= $count . ";"; //Unique, consecutive number of the item within the file
        $record .= "1;"; //Number of packages of the item
        $record .= "PK;"; //Type of packaging or type of shipment item
        $record .= number_format($consignment->getWeight(), 3) . ";"; //weight
        $record .= ";"; //Volumn
        $record .= ";"; //Length
        $record .= ";"; //Width
        $record .= ";"; //Height
        $record .= substr($consignment->getHawb(), 0, 35) . ";"; //Customer Reference No
        $record .= $consignment->getAwb() . ";"; //Tracking Number
        $record .= ";"; //Routing code or Leitcode of the package.
        $record .= ";"; //Product Key
        $record .= substr($consignment->getHawb(), 0, 35) . ";"; //Receipt Ref No
        $record .= substr($consignment->getContact(), 0, 35) . ";"; //Name 1 of receipt
        $record .= substr($consignment->getCompany(), 0, 35) . ";"; //Name 2 of receipt
        $record .= ";"; //Name 3
        $record .= "Packstation;"; //When addressing Packstations, the term "Packstation" must be entered here. When addressing P.O. boxes (VF62), the term "Postfach" [P.O. box] in conjunction with the P.O. box number must be entered here.         
        $record .= "5;"; // House Number
        $record .= substr($consignment->getCity(), 0, 35) . ";"; // City
        $record .= substr($consignment->getPostCode(), 0, 17) . ";"; // Postcoded
        $record .= $country->getIso() . ";"; // Land
        $record .= ";"; // Information on contact
        $record .= ";"; // Information on contact
        $record .= substr($consignment->getTelephone(), 0, 35) . ";"; // Telephone
        $record .= ";"; // Fax
        $record .= substr($consignment->getEmail(), 0, 70) . ";"; // Email
        $record .= ";"; // VAT No
        $record .= ";"; // EXW Number
        $record .= ";"; // Region
        $record .= ";"; // Date of Birth
        $record .= ";"; // ID No
        $record .= ";"; // ID TYpe
        $record .= ";"; // Issuing Authority
        $record .= ";"; // Minimum age of receipt
        $record .= ";"; // Registered address town and city
        $record .= ";"; // Registerd address disctrict
        $record .= ";"; // Registered Address street
        $record .= ";"; // Contract Handling
        $record .= ";"; // Contact
        $record .= ";"; // IDP-CONF
        $record .= ";"; // Nationality
        $record .= ";"; // Retail Outlet mail address
        $record .= ";"; // IDP free text field 2
        $record .= ";"; // PostNumber of receipt
        $record .= "\r\n";

        /*
         * Additional Record
         */
        $record .= "SAZU;";
        $record .= "ZIWO;"; //The service codes are listed and explained in the Annex (example 
        $record .= ";"; //The service attributes are listed and explained in the Annex
        $record .= ";"; //Required to specify the cash-on-delivery amount in EUR
        $record .= ";"; //Currency Code
        $record .= ";"; //Not Used
        $record .= ";"; //Not Used
        $record .= ";"; //Not Used
        $record .= ";"; //Not Used
        $record .= ";"; //Not Used
        $record .= ";"; //Not Used
        $record .= ";"; //Not Used
        $record .= ";"; //Not Used
        $record .= ";"; //Route No
        $record .= ";"; //BIC
        $record .= ";"; //IBAN
        $record .= "\r\n";



        return $record;
    }

    private function messageEndRecord($totalCount) {
        $record = "";
        $record .= "SANE;";
        $record .= "1;"; // Unique message reference of the sender: Always 1.
        $record .= $totalCount . ";"; // number of items
        $record .= $totalCount . ";"; // number of Addition record
        $record .= "1;"; // number of partner Role Record
        $record .= "0;"; // number of custom Record
        $record .= "0;"; // number of custom detail Record
        $record .= "0;"; // number of document Record
        $record .= "0;"; // number of Courier Record
        $record .= "\r\n";

        return $record;
    }

    private function footerRecord($runNumber) {
        $record .= "";
        $record .= "SADE;";
        $record .= $runNumber . ";"; //Unique, ascending, gapless number per accounting number; number is incremented by 1 for each physical transmission
        $record .= "0;";
    }

    public function recycledShipment($consignment) {
        $output["STATUS"] = "SUCCESS";
        return $output;
    }

    private function getSortCode($consignment) {
        $output = array();
        $postcode = str_replace(" ", "", $consignment->getPostcode());
        $warenpostFilter = new warenpostGazetteerFilter();
        $warenpostFilter->addFieldFilter("postcode", $postcode);
        $postcodeList = $warenpostFilter->getList();
        if (count($postcodeList) > 0) {
            $streetArray = array();
            foreach ($postcodeList as $postcode) {
                $streetArray[$postcode->getId()] = str_replace("-", " ", str_replace(".", "", strtolower($postcode->getStreetAbbreviation())));
            }
            $shortest = -1;
            $addressLine1 = trim(strtolower($consignment->getAddressLine1()));
            $AddressSplit = GenericFunctions::getDoorNumber($addressLine1);

            $number = $AddressSplit['number'];
            if ($number <= 0) {
                $output["STATUS"] = "ERROR";
                $output["MESSAGE"] = "Please enter door number and street name in address line 1.";
                return $output;
            }
            $street1 = $AddressSplit['street'];
            $filterStreet = $this->streetFilter(strtolower($street1));
            $closestStreet = $this->closest($filterStreet, $streetArray, $shortest, 1);
            if (count($closestStreet) > 0) {
                $shortest = 1;
                $mostClosestStreet = $this->closest($filterStreet, $closestStreet, $shortest, 1);
                $searchStreet = $streetArray[$closestStreet];

                if (count($mostClosestStreet) != "") {
                    foreach ($mostClosestStreet as $key => $value) {
                        $routineId[] = $key;
                    }
                    $warenpostFilter = new warenpostGazetteerFilter();
                    $routineRecord = $warenpostFilter->checkStreetNumber($number, $routineId);
                    if (count($routineRecord) > 0) {
                        $output["STATUS"] = "SUCCESS";
                        $output["MESSAGE"] = $routineRecord[0]->getAlort() . "||" . $routineRecord[0]->getSchluessel() . "||" . $routineRecord[0]->getHnr1000() . "||" . $routineRecord[0]->getStreetCode() . "||" . $routineRecord[0]->getTownCode() ."||". $number;
                        return $output;
                    } else {
                        $output["STATUS"] = "ERROR";
                        $output["MESSAGE"] = "Unable to provide service at " . $consignment->getAddressLine1() . " Address.";
                        //return $output;
                    }
                } else {
                    $output["STATUS"] = "ERROR";
                    $output["MESSAGE"] = "Unable to provide service at " . $consignment->getAddressLine1() . " Address.";
                    return $output;
                }
            } else {
                $output["STATUS"] = "ERROR";
                $output["MESSAGE"] = "Unable to provide service at " . $consignment->getPostcode() . " Postcode.";
            }
        }
        return $output;
    }

    private function streetFilter($street) {
        $streetArray = array("strasse", "street");
        $filterStreet = str_replace($streetArray, "str", $street);
        $filterStreet = str_replace("-", " ", $filterStreet);
        $filterStreet = str_replace(",", " ", $filterStreet);
        $filterStreet = str_replace(".", " ", $filterStreet);
        return $filterStreet;
    }

    private function closest($input, $words, &$shortest, $sensitivity, $debug = false) {

        $matchArray = array();
        // loop through words to find the closest
        foreach ($words as $key => $word) {

            // calculate the distance between the input word,
            // and the current word
            $lev = levenshtein($input, $word);

            // check for an exact match
            if ($lev == 0) {
                // closest word is this one (exact match)
                $closest = $key;
                $shortest = 0;
                $matchArray[$key] = $word;


                // break out of the loop; we've found an exact match
                break;
            }

            // if this distance is less than the next found shortest
            // distance, OR if a next shortest word has not yet been found
            if ($lev <= $shortest || $shortest < 0) {
                // set the closest match, and shortest distance
                $closest = $key;
                $shortest = $lev;
                $matchArray[$key] = $word;
            }
        }
        if ($shortest <= $sensitivity) {
            return $matchArray;
        } else {
            return 0;
        }
    }
    
     private function checkDigitRoutineBarcode($barcode){
        
        $arr = str_split($barcode);
        $checkdigit =     ($arr[0] * 4) 
                        + ($arr[1] * 9) 
                        + ($arr[2] * 4) 
                        + ($arr[3] * 9) 
                        + ($arr[4] * 4) 
                        + ($arr[5] * 9) 
                        + ($arr[6] * 4) 
                        + ($arr[7] * 9) 
                        + ($arr[8] * 4) 
                        + ($arr[9] * 9) 
                        + ($arr[10] * 4) 
                        + ($arr[11] * 9) 
                        + ($arr[12] * 4) ;
                        
        $checkdigit = $checkdigit % 10;

        
        if ($checkdigit % 10 == 0) {
            $checkdigit = 0;
        } else {
            $checkdigit = $checkdigit;
        }
        return $checkdigit;
    }
}
