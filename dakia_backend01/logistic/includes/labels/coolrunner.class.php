<?php

class CoolRunner implements CarrierService {

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
        if(trim($consignment->getEmail()) == ""){
            $returnOutput[] = "Please enter email address.";
        }
        else if(trim($consignment->getTelephone()) == ""){
            $returnOutput[] = "Please enter telephone.";
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
        $parcel_list = $consignment->getParcels();
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
        if($consignment->getShipmentType() == "C"){
            if (trim(@$this->constants['INTEGRATION_TYPE']) =='' ||  trim(@$this->constants['COOLRUNNER_URL']) == '' || trim(@$this->constants['COOLRUNNER_USERNAME']) == '' || trim(@$this->constants['COOLRUNNER_PASSWORD']) == '' ) {
                $output['STATUS'] = 'ERROR';
                $output['MESSAGE'] = "Please check service constants. Please contact to itsupport@oneworldexpress.com";
                return $output;
            }
        }
        else if (trim(@$this->constants['INTEGRATION_TYPE']) =='' ||  trim(@$this->constants['COOLRUNNER_URL']) == '' || trim(@$this->constants['COOLRUNNER_USERNAME']) == '' || trim(@$this->constants['COOLRUNNER_PASSWORD']) == '' || trim(@$this->constants['COOLRUNNER_SHIPPER_NAME']) == '' || trim(@$this->constants['COOLRUNNER_SHIPPER_ADDRESSLINE1']) == '' || trim(@$this->constants['COOLRUNNER_SHIPPER_ADDRESSLINE2']) == '' || trim(@$this->constants['COOLRUNNER_SHIPPER_CITY']) == '' || trim(@$this->constants['COOLRUNNER_SHIPPER_POSTCODE']) == '') {
            $output['STATUS'] = 'ERROR';
            $output['MESSAGE'] = "Please check service constants. Please contact to itsupport@oneworldexpress.com";
            return $output;
        }
        if (trim(@$this->constants['INTEGRATION_TYPE']) == 'API' && $consignment->getShipmentType() == "C") {
            $output =  $this->getCollectionLabel($consignment, $this->constants, $parcel_list);
        } else if (trim(@$this->constants['INTEGRATION_TYPE']) == 'API') {
            $output = $this->apiLabel($this->constants, $consignment);
        }

        return $output;
    }

    private function apiLabel($constants, Consignment $consignment) {
       
        $output = array();
        $this->login = $constants["COOLRUNNER_USERNAME"]; //"jana.ratica@parcel4you.com";
        $this->password = $constants["COOLRUNNER_PASSWORD"]; // "apw0pmf22m4jrw0nkrmrrty4bm5o6h76";
        $url_link = $constants["COOLRUNNER_URL"];

        $droppoint_request = array(
            'country_code' => $this->country->getIso(),
            'postcode' => $consignment->getPostcode(),
            'street' => $consignment->getAddressLine1(),
            'number_of_droppoints' => '15'
        );
        $sender_name = $constants["COOLRUNNER_SHIPPER_NAME"];  //"DAO";
        $sender_street1 = $constants["COOLRUNNER_SHIPPER_ADDRESSLINE1"]; //"Dao 365 A/S";
        $sender_street2 = $constants["COOLRUNNER_SHIPPER_ADDRESSLINE2"]; //"Tonne Kjærsvej 40";
        $sender_city = $constants["COOLRUNNER_SHIPPER_CITY"]; //"Fredericia";
        $sender_postcode = $constants["COOLRUNNER_SHIPPER_POSTCODE"]; //"7000";
        $sender_coutry = $constants["COOLRUNNER_SHIPPER_COUNTRY"]; //"DK";
        if ((int) $sender_coutry > 0) {
            $shipperCountry = new Country($sender_coutry);
            $sCountry = $shipperCountry->getName();
            $sIso = $shipperCountry->getIso();
        } else {
            $sCountry = $sender_coutry;
        }
        
        if ($this->country->getIso() == "DK") {
        $carrier = "dao"; //"dao";
        $endpoint = "dao"; //"dao";
        $carrier_service = "delivery";
            $data = array(
                'receiver_zipcode' => $consignment->getPostcode(),
                'receiver_street1' => $consignment->getAddressLine1(),
            );

            $destination = "validateaddress";
            $this->url = $url_link . $destination; //'https://api.coolrunner.dk/v1/' 
            $add_result = $this->prepareRequest(json_encode($data));
          
            $consignment->setApiData(print_r($data, true), print_r($add_result, true), 'ValidateAddress');

            if (strtoupper($add_result->result->status) != "OK") {
                $output["STATUS"] = "ERROR";
                $output["MESSAGE"] = $add_result->result->emessage;
                return $output;
            }
        } else if ($this->country->getIso() == "SE") {
            $carrier = "dhl";
            $endpoint = "dhl";
             $carrier_service = "droppoint";
        } else if ($this->country->getIso() == "FI") {
            $carrier = "posti";
            $endpoint = "posti";
            $carrier_service = "droppoint";
        }
        /* else if($consignment->getCountryIsoCode() == "NO")
          {
          $sender_name = "Helthjem";
          $sender_street1 = "Jogstadveien 25";
          $sender_street2 = "";
          $sender_city = "Norway";
          $sender_postcode = "2007";
          $sender_coutry = $consignment->getCountryIsoCode();
          }
         */
        $this->url = $url_link . 'droppoints/' . $endpoint;
        $droppoint_response = $this->prepareRequest(http_build_query($droppoint_request));
        $consignment->setApiData(print_r($droppoint_request, true), print_r($dropoint_result, true), 'DropPointRequest');


        if (strtolower($droppoint_response->status) == "ok") {
            $drop_result = $droppoint_response->result[0];
            $droppoint_id = $drop_result->droppoint_id;
            $droppoint_name = $drop_result->name;
            $droppoint_street1 = $drop_result->address->street;
            $droppoint_zipcode = $drop_result->address->postal_code;
            $droppoint_city = $drop_result->address->city;
            $droppoint_country = $drop_result->address->country_code;
        }
        $company = $consignment->getCompany();
        if (($company) == "")
            $company = $consignment->getContact();

        $request_params = array(
            'receiver_name' => utf8_encode($consignment->getContact()),
            'receiver_name' => utf8_encode($company),
            'receiver_attention' => "",
            'receiver_street1' => utf8_encode($consignment->getAddressLine1()),
            'receiver_street2' => utf8_encode($consignment->getAddressLine2() . " " . $consignment->getAddressLine3()),
            'receiver_zipcode' => $consignment->getPostcode(),
            'receiver_city' => utf8_encode($consignment->getCity()),
            'receiver_country' => $this->country->getIso(),
            'receiver_phone' => $consignment->getTelephone(),
            'receiver_email' => $consignment->getEmail(),
            'receiver_notify' => 1,
            'receiver_notify_sms' => $consignment->getTelephone(),
            'receiver_notify_email' => $consignment->getemail(),
            'sender_name' => $sender_name,
            'sender_attention' => "",
            'sender_street1' => $sender_street1,
            'sender_street2' => $sender_street2,
            'sender_zipcode' => $sender_postcode,
            'sender_city' => $sender_city,
            'sender_country' => $sender_coutry,
            'sender_phone' => "02088676060",
            'sender_email' => "",
            'droppoint' => 1,
            'droppoint_id' => $droppoint_id,
            'droppoint_name' => $droppoint_name,
            'droppoint_street1' => $droppoint_street1,
            'droppoint_zipcode' => $droppoint_zipcode,
            'droppoint_city' => $droppoint_city,
            'droppoint_country' => $droppoint_country,
            'carrier' => $carrier,
            'carrier_product' => "private",
            'carrier_service' => "droppoint",
            'length' => 10,
            'width' => 10,
            'height' => 10,
            'weight' => number_format($consignment->getWeight(), 2) * 1000,
            'reference' => $consignment->getHawb(),
            'description' => utf8_encode($consignment->getDescription()),
            'comment' => "",
            'label_format' => "A4",
            'insurance' => 0,
            'insurance_value' => 0,
            'insurance_currency' => "",
            'customs_value' => 0,
            'customs_currency' => ""
        );


        $this->url = $url_link . 'shipment/create';
        $response_data = $this->prepareRequest(http_build_query($request_params));
        $consignment->setApiData(print_r($request_params, true), print_r($response, true), 'ShipmentCreate');

        if (strtolower($response_data->status) == "ok") {
            $licence_plate_array = array();

            $success_response = $response_data->result;
            $licence_plate_array[] = $success_response->package_number;
            $pdf_file = base64_decode($success_response->pdf_base64);
            $path = '../_assets/pdf/' . date('Y_m_d') . '/' . $consignment->getId() . ".pdf";
            $fp = fopen($path, 'wb+');
            fwrite($fp, $pdf_file);
            fclose($fp);

            $pdf = new PdfBase(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
            $this->pdf = $pdf;
            $parcel_list = $consignment->getParcels();
            $parcel_list[0]->setTrackingNumber($success_response->package_number);
            $parcel_list[0]->save();
            $this->pdf->IncludeJS("print();");
            $output['STATUS'] = 'SUCCESS';
            $output['LABEL'] = date('Y_m_d') . '/' . $consignment->getId() . ".pdf";
            $output['TRACKING_NUMBER'] = $licence_plate_array;
            return $output;
            // return "SUCCESS||" . $tracking_number;
        } else {
            $output['STATUS'] = 'ERROR';
            $output['MESSAGE'] = $response_data->message;
            return $output;
        }
    }
    
    public function getCollectionLabel(Consignment $consignment, $constants, $parcel) {
         $output = array();
        $this->login =  $constants["COOLRUNNER_USERNAME"]; //"jana.ratica@parcel4you.com"; 
        $this->password = $constants["COOLRUNNER_PASSWORD"]; // "apw0pmf22m4jrw0nkrmrrty4bm5o6h76"; 
        $this->url = $constants["COOLRUNNER_URL"];//"https://api.coolrunner.dk/v3/shipments/return"; 
        
        $senderCountry = new Country($consignment->getSenderCountryId());
        $requestArray = array();
        $senderArray = array();
        $senderArray["name"] = $consignment->getSenderName();
        $senderArray["attention"] = $consignment->getSenderName();
        $senderArray["street1"] = $consignment->getSenderAddressLine1() ." ". $consignment->getSenderAddressLine2() ." " . $consignment->getSenderAddressLine3();
        $senderArray["zip_code"] = $consignment->getSenderPostcode();
        $senderArray["city"] = $consignment->getSenderCity();
        $senderArray["country"] = $senderCountry->getIso();
        $senderArray["phone"] = $consignment->getSenderTelephone();
        $senderArray["email"] = $consignment->getSenderEmail();
        
        $requestArray["sender"] = $senderArray;
        
        $recevierArray = array();
        $recevierArray["name"] = $consignment->getContact();
        $recevierArray["attention"] = $consignment->getContact();
        $recevierArray["street1"] = $consignment->getAddressLine1() ." ". $consignment->getAddressLine2() ." " . $consignment->getAddressLine3();
        $recevierArray["zip_code"] = $consignment->getPostcode();
        $recevierArray["city"] = $consignment->getCity();
        $recevierArray["country"] = $this->country->getIso();
        $recevierArray["phone"] = $consignment->getTelephone();
        $recevierArray["email"] = $consignment->getEmail();
        $recevierArray["notify_sms"] = $consignment->getTelephone();
        $recevierArray["notify_email"] = $consignment->getEmail();
        
        $requestArray["receiver"] = $recevierArray;
        
        $requestArray["length"] =$parcel[0]->getLength();
        $requestArray["width"] = $parcel[0]->getWidth();
        $requestArray["height"] = $parcel[0]->getHeight();
        $requestArray["weight"] = $consignment->getWeight();
        $requestArray["carrier"] = "dhl";
        $requestArray["carrier_product"] = "return";
        $requestArray["carrier_service"] = "";
        $requestArray["reference"] = $consignment->getHawb();
        $requestArray["description"] = $consignment->getDescription();
        $requestArray["comment"] = $consignment->getNotes();
        $requestArray["label_format"] = "LabelPrint";
        $requestArray["servicepoint_id"] = 0;
        
        $requestJson = json_encode($requestArray);
        
        $response = $this->prepareRequest($requestJson, "C");
        $consignment->setApiData(print_r($requestJson, true), print_r($response, true), "RETURN REQUEST");
        if($response->package_number != "")
        {
            $trackingNo = $response->package_number;
            $labelLink = "https://api.coolrunner.dk/v3/return/373221512538043856/label";//$response->_links->label;
            $this->url = $labelLink;
            $outputfilename = "../_assets/pdf/" . date('Y_m_d') . '/' . $consignment->getId() . ".pdf";
            $labelResponse = $this->prepareRequest('', "C", "GET", $outputfilename);
            if($labelResponse){
                foreach ($parcel as $p) {
                    $p->setTrackingNumber($trackingNo);
                    $p->save();
                }

                $output['STATUS'] = 'SUCCESS';
                $output['LABEL'] = date('Y_m_d') . '/' . $consignment->getId() . ".pdf";
                $licence_plate_array[] = $trackingNo;
                $output['TRACKING_NUMBER'] = $licence_plate_array;
            }
            else
            {
                $output["STATUS"] = "ERROR";
                $output["MESSAGE"] = "Unable to print label.";
            }
            
        }
        else {
            $output["STATUS"] = "ERROR";
            $output["MESSAGE"] = "Unable to create label. Either postcode is invalid or not supported.";
        }
        return $output;
    }

    private function prepareRequest($request, $requestType = "", $method = "POST", $filename= "") {
        $result = "";
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $this->url);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_USERPWD, $this->login . ":" . $this->password);
        if($requestType == "C"){
            curl_setopt($ch, CURLOPT_HTTPHEADER,  array(
                "X-Developer-Id: One Wolrd Express",
                "Content-Type: application/json"
            ));
        }
        if($method == "GET"){
            $fp = fopen ($filename, 'w+');
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST,"GET");
            curl_setopt($ch, CURLOPT_FILE, $fp);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

        }
        else{
        //curl_setopt($ch, CURLOPT_POST , true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $request);
        }
        $result = curl_exec($ch);
        $info = curl_getinfo($ch);
        curl_close($ch);
        if($method == "GET"){
         if($info["http_code"] == 200){
             return true;
         }   
         else
         {
             return false;
         }
        }else
        {
            return json_decode($result);
        }
    }

    public function tracking($trackingNumber, $trackBy = 'parcel', $EDI = false) 
    {
        require_once(BASE_PATH."includes/labels/coolrunnertrackingstatus.class.php");
        
        $consignmentFilter = new ConsignmentFilter();
        $consignmentFilter->addAwbArrayFilter($trackingNumber);
        $result            = $consignmentFilter->getConList();
        $consignment       = $result[0];
        
        $serviceAgentConstantFilter = new ServiceConstantValueFilter();
        $serviceAgentConstantFilter->addFilter("service_id = '" . $consignment->getServiceId() . "' AND agent_id = '" . $consignment->getAgentId() . "' ");
        $serviceAgentConstant = $serviceAgentConstantFilter->getConstantList("c.constant 'constant_name', scv.constant_value 'constant_value'");
        
        if (count($serviceAgentConstant) > 0) 
        {
            foreach ($serviceAgentConstant as $serviceAgentConstantData) 
            {
                $this->constants[$serviceAgentConstantData->getConstantName()] = $serviceAgentConstantData->getConstantValue();
            }
        }
        $entityId = 0;
        if ($trackBy == 'parcel') 
        {
            $parcelObj = new ParcelFilter();
            $parcelObj->addTrackingNumberFilter($trackingNumber);
            $parcelDataArray = $parcelObj->getColumnList('p.id, p.tracking_number, p.consignment_id');

            if (count($parcelDataArray) > 0) 
            {
                $parcelData = $parcelDataArray[0];
                $entityId = $parcelData->getId();
            }
        } 
        else if ($trackBy == 'shipment') 
        {
            $shipmenObj = new ConsignmentFilter();
            $shipmenObj->addawbFilter($trackingNumber);
            $shipmentDataArray = $shipmenObj->getColumnList('c.awb');
            if (count($shipmentDataArray) > 0) 
            {
                $shipmentData = $shipmentDataArray[0];
                $entityId = $shipmentData->getId();
            }
        }
       
        if($entityId > 0)
        {
            $login = $this->constants['COOLRUNNER_USERNAME'];
            $pass = $this->constants['COOLRUNNER_PASSWORD'];
            $url = $this->constants['COOLRUNNER_URL_TRACKING'].$trackingNumber;
            
            $ch = curl_init();
            curl_setopt( $ch, CURLOPT_URL, $url );
            curl_setopt( $ch, CURLOPT_USERPWD, $login . ":" . $pass);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);       
            $result = curl_exec( $ch );
            $jsonArray = json_decode($result, true);
            
            if($jsonArray['status'] == 'ok')
            {
                $trackingHistory = $jsonArray['tracking']['history'];
                if(count($trackingHistory) > 0)
                {
                    foreach($trackingHistory as $trackPoint)
                    {
                        $DateTime = $trackPoint['time'];
                        $EventCode = $trackPoint['message'];
                        $EventDescription = $trackPoint['message'];
                        $ServiceAreaDescription = '';
                        $Signatory = '';
                        $spTrackingStatus = CoolRunnerTrackingStatus::getOweStatusCode($EventCode);

                        $trackingDataFilter = new TrackingDataFilter();
                        $trackingDataFilter->addTrackPointExistFilter($trackingNo, $spTrackingStatus, $ServiceAreaDescription, $EventCode, $EventDescription, $DateTime);
                        $trackingDataExistsObj = $trackingDataFilter->getColumnList("t.id");
                        if (count($trackingDataExistsObj) == 0) 
                        {
                            $trackingData = [
                                'user_id' => 0,
                                'entity_id' => $entityId,
                                'entity_type' => $trackBy,
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
                            $trackingDataObj->save();
                        }
                    }

                    $trackingDataFilter = new TrackingDataFilter();
                    $trackingDataFilter->addTrackingNumberFilter($trackingNumber);
                    $trackingDataFilter->AddOrderByDate(false);
                    $trackingDataObj = $trackingDataFilter->getColumnList('entity_id,entity_type,carrier_code,status_code_id');

                    if (count($trackingDataObj) > 0) 
                    {
                        $trackingDataObj = $trackingDataObj[0];
                        $carrierCode = $trackingDataObj->getCarrierCode();
                        $oweTrackingStatusCode = $trackingDataObj->getStatusCodeId();
                        $entityType = $trackingDataObj->getEntityType();
                        $entityId = $trackingDataObj->getEntityId();
                        $consignmentStatusCode = CacesaExpressTrackingStatus::getConsignmentStatus($oweTrackingStatusCode);
                        $consignmentId = 0;
                        if ($entityType == 'parcel') 
                        {
                            $parcelObj = new Parcel($entityId);
                            $parcelObj->setParcelStatusCode($consignmentStatusCode);
                            $parcelObj->save();
                            $consignmentId = $parcelObj->getConsignmentId();
                        } 
                        else 
                        {
                            $consignmentId = $entityId;
                            $parcelObj = new Parcel();
                            $parcelObj->bulkUpdate("parcel_status_code='" . $consignmentStatusCode . "'", "consignment_id = '" . $consignmentId . "'");
                        }
                        $ConsignmentObj = new Consignment($consignmentId);
                        $consignmentStatus = isset(Consignment::$database_status_array[$consignmentStatusCode]) ? Consignment::$database_status_array[$consignmentStatusCode] : '';
                        $ConsignmentObj->setShipmentStatus($consignmentStatusCode);
                        if(empty($ConsignmentObj->getDateScanned()))
                        {
                            Tracking::setScanDate($ConsignmentObj);
                        }

                        if($consignmentStatusCode == Consignment::STATUS_DELIVERED)
                        {
                            $ConsignmentObj->setDateDelivered($trackingDataObj->getDateCreated());
                        }
                        if ($consignmentStatus != '')
                            $ConsignmentObj->setConsignmentStatus($consignmentStatus);
                        $ConsignmentObj->save();
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

}
