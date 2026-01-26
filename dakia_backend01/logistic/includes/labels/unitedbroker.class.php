<?php
include_classes([
    'serviceauthenticationtoken.class',
    'serviceauthenticationtokenfilter.class'
]);
class UnitedBroker implements CarrierService {

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
        
          if (trim(@$this->constants['UNITEDBROKER_CLIENTID']) == '' || trim(@$this->constants['UNITEDBROKER_PASSWORD']) == '' || trim(@$this->constants['UNITEDBROKER_USERNAME']) == '') {
          $output['STATUS'] = 'ERROR';
          $output['MESSAGE'] = "Please check service constants. Please contact to itsupport@oneworldexpress.com";
          return $output;
          }
         
          if($this->constants['INTEGRATION_TYPE'] == 'API')
          {
              $output = $this->apiLabel($consignment);
          }          
        return $output;
    }

    public function tracking($trackingNumber, $trackBy = 'parcel', $EDI = false) {
        
    }
    public function apiLabel($consignment)
    {
        
        $serviceAuthenticationTokenFilter = new serviceAuthenticationTokenFilter();
        $serviceAuthenticationTokenFilter->addFilter("service_name = '".$this->serviceValues->getName()."'");
        $this->authFilter = $serviceAuthenticationTokenFilter->getList();

        if(count($this->authFilter) > 0)
        {
            $expireTime    = $this->authFilter[0]->getTokenStartTime(); 
            $currentTime   = new DateTime(date('Y-m-d H:i:s', strtotime('-1 hour')));
            $lastTokenTime = new DateTime($expireTime);
            $dteDiff  = $currentTime->diff($lastTokenTime); 
          
            if($dteDiff->h >=1)
            {
                $this->getRefreshToken($consignment);
            }
        }
        else
        {
            $this->getToken($consignment);
        }
        
        $shipmentResult = $this->createShipment($consignment);
        $shipmentId = $shipmentResult->id;
        
        if($shipmentId != '')
        {
            $label = $this->getLabel($shipmentId);
            foreach($shipmentResult->parcels as $trackingNumber)
            {
                $trackingNumberArray[] = $trackingNumber->carrierReference;
            }
            $trackingNumberArray[] = $shipmentResult->parcels->carrierReference;
            $fileName = "../_assets/pdf/" . date('Y_m_d') . '/' . $consignment->getId() . ".pdf";
            file_put_contents($fileName, $label);
            $output["STATUS"] = 'SUCCESS';
            $output["LABEL"] = date('Y_m_d') . '/' . $consignment->getId() . ".pdf";
            $output["TRACKING_NUMBER"] = $trackingNumberArray;
        }
        else
        if($shipmentResult->message != '')
        {
            $output['STATUS'] = 'ERROR';
            $output['MESSAGE'] = $shipmentResult->message;
        }
        return $output; 
    }
    
    public function getToken($consignment)
    {
        
        $stagingUrl = 'https://staging-api.ubsend.io/v1/auth/login';
        $clienntId = $this->constants['UNITEDBROKER_CLIENTID'];
        $userName = $this->constants['UNITEDBROKER_USERNAME'];
        $password = $this->constants['UNITEDBROKER_PASSWORD'];
        //$params = array("username"=>"api-user-staging@oneworldexpress.com","password"=>"HScuOwb$w.%67WW=l2T>");
        
        $curl = curl_init();
        curl_setopt_array($curl, array(
        CURLOPT_URL => $stagingUrl,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => '{"username":"api-user-staging@oneworldexpress.com","password":"HScuOwb$w.%67WW=l2T>"}',
        CURLOPT_HTTPHEADER => array(
          'Content-Type: application/json',
          'ClientId:'. $clientId
        ),
    ));

    $response = curl_exec($curl);
    $resultArray = json_decode($response);
    $accessToken = $resultArray->accessToken; 
    $refreshToken = $resultArray->refreshToken;
    
    $service = new Services($consignment->getServiceId());
    $serviceAuthenticationTokenFilter = new serviceAuthenticationTokenFilter();
    $serviceAuthenticationTokenFilter->addFilter("service_name = '".$service->getName()."'");
    $result = $serviceAuthenticationTokenFilter->getList();
    
    if(count($result) > 0)
    {
        $serviceAuthenticationToken = new serviceAuthenticationToken($result[0]->getId());
    }
    else
    {
        $serviceAuthenticationToken = new serviceAuthenticationToken();
    }
    
    $serviceAuthenticationToken->setToken($accessToken);
    $serviceAuthenticationToken->setRefreshToken($refreshToken);
    $serviceAuthenticationToken->setServiceName($service->getName());
    
    $fiveMinutes = time() - (65 * 60);
    $nexthour = time() - (5*60);
    
    $serviceAuthenticationToken->setTokenStartTime(date('Y-m-d H:i:s', $fiveMinutes));
    $serviceAuthenticationToken->setTokenExpireTime(date('Y-m-d H:i:s', $nexthour));
    $serviceAuthenticationToken->save();
    //die;
    }
    
    public function getRefreshToken($consignment)
    {
        
        $stagingUrl = 'https://staging-api.ubsend.io/v1/auth/refresh-token';
        $clienntId = $this->constants['UNITEDBROKER_CLIENTID'];
        
        $curl = curl_init();
        curl_setopt_array($curl, array(
        CURLOPT_URL => $stagingUrl,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => $this->authFilter[0]->getRefreshToken(),
        CURLOPT_HTTPHEADER => array(
          'Content-Type: application/json',
          'ClientId:'. $clientId
        ),
    ));

    $response = curl_exec($curl);
    $resultArray = json_decode($response);
    
    $accessToken = $resultArray->accessToken; 
    $refreshToken = $resultArray->refreshToken;
    
    $serviceAuthenticationToken = new serviceAuthenticationToken($this->authFilter[0]->getId());
    
    $serviceAuthenticationToken->setToken($accessToken);
    $serviceAuthenticationToken->setRefreshToken($refreshToken);
    
    $fiveMinutes = time() - (65 * 60);
    $nexthour = time() - (5*60);
    
    $serviceAuthenticationToken->setTokenStartTime(date('Y-m-d H:i:s', $fiveMinutes));
    $serviceAuthenticationToken->setTokenExpireTime(date('Y-m-d H:i:s', $nexthour));
    $serviceAuthenticationToken->save();
    }
    
    public function createShipment($consignment)
    {
        
        if($consignment->getContact() == '')
        {
            $name = $consignment->getCompany();
        }
        else
        {
            $name = $consignment->getContact();
        }
        $countryIso = $this->country->getIso();
        
        $createShipmenturl = 'https://staging-api.ubsend.io/v1/shipments/register';
        $curl = curl_init();
        curl_setopt_array($curl, array(
        CURLOPT_URL => $createShipmenturl,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS =>'{
        "sender": {
            "firstName": "Oneworld Express",
            "lastName": "Oneworld House",
            "addressLine1": "Pump Lane, Hayes",
            "city": "London",
            "postCode": "UB3 3NB",
            "country": "GB",
            "contactInfo": {
                "dialCode": "+44",
                "telephone": "82388238",
                "email": "itsupport@oneworldexpress.com"
            }
        },
        "recipient": {
            "firstName": "'.$name.'",
            "lastName": "-",
            "addressLine1": "'.$consignment->getAddressLine1().'",
            "city": "'.$consignment->getCity().'",		
            "postCode": "'.$consignment->getPostCode().'",
            "country": "'.$countryIso.'",
            "contactInfo": {
                "dialCode": "+45",
                "telephone": "'.$consignment->getTelephone().'",
                "email": "'.$consignment->getEmail().'"
            }
        },
        "collectionInfo":{
           "type":"DROF_OFF",
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
    'ClientId:'.$this->constants['UNITEDBROKER_CLIENTID'],
    'Authorization: Bearer '.$this->authFilter[0]->getToken(),
    'Content-Type: application/json'
  ),
));
        $response = curl_exec($curl);
        curl_close($curl);
        $shipmentResult = json_decode($response);
        return $shipmentResult;

        }
        
    public function getLabel($shipmentId)
    {
        $getLabelUrl = 'https://staging-api.ubsend.io/v1/shipments/'.$shipmentId.'/label.pdf';
        
        $curl = curl_init();
        curl_setopt_array($curl, array(
          CURLOPT_URL => $getLabelUrl,
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'GET',
          CURLOPT_HTTPHEADER => array(
            'ClientId: 12609',
            'Authorization: Bearer '.$this->authFilter[0]->getToken()
          ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);
        return $response;      
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
