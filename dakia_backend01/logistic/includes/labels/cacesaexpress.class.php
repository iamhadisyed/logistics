<?php

class CacesaExpress implements CarrierService {

    private $pdf;
    private $serviceValues = null;
    private $agentValues = null;
    private $constants = null;
    private $user = null;
    private $country = null;

    public function __construct() {
        
    }

    public function validation(Consignment $consignment, Services $service, Country $country) {
        $returnArray = array();
        if($consignment->getValue() <= 0)
        {
            $returnArray[] = "Value must be greater than 0";
        }
        return $returnArray;
    }

    public function remoteareas($consignment, $carrierObject, $sender) {
        
    }

    public function label($consignment, $labelType = 'pdf', $size = '') {

        $output = array();
        $this->serviceValues = new Services($consignment->getServiceId());
        $this->user = SessionManager::getUser();
        $this->country = new Country($consignment->getCountryId());

        /*
         * Service COnstant
         */
        $serviceAgentConstantFilter = new ServiceConstantValueFilter();
        $serviceAgentConstantFilter->addFilter("service_id = '" . $consignment->getServiceId() . "' AND agent_id = '" . $consignment->getAgentId() . "' ");
        $serviceAgentConstant = $serviceAgentConstantFilter->getConstantList("c.constant 'constant_name', scv.constant_value 'constant_value'");
        if (count($serviceAgentConstant) > 0) {
            foreach ($serviceAgentConstant as $serviceAgentConstantData) {
                $this->constants[$serviceAgentConstantData->getConstantName()] = $serviceAgentConstantData->getConstantValue();
            }
        }
        if (trim(@$this->constants['INTEGRATION_TYPE']) == '' || trim(@$this->constants['CACESA_WSDL']) == '' || trim(@$this->constants['CACESA_USERNAME']) == '' || trim(@$this->constants['CACESA_PASSWORD']) == '') {
            $output['STATUS'] = 'ERROR';
            $output['MESSAGE'] = "Please check service constants. Please contact to itsupport@oneworldexpress.com";
            return $output;
        }
        if (trim(@$this->constants['INTEGRATION_TYPE']) == 'API') {
            $output = $this->apiLabel($this->constants, $consignment);
        } else {
            $output['STATUS'] = 'ERROR';
            $output['MESSAGE'] = "Please check integration type for this services.";
        }
        return $output;
    }

    private function apiLabel($constants, $consignment) {
        $pdf = new PdfBase(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $this->pdf = $pdf;
        // $WS_URL = 'http://tws1.cacesa.com/wstest/wscacesa.asmx?wsdl'; //TEST URL
        $WS_URL = $constants['CACESA_WSDL']; //'http://tws1.cacesa.com/ws/wscacesa.asmx?wsdl';

        $WS_USER = $this->constants['CACESA_USERNAME']; //'onew';
        $WS_PASSWORD = $this->constants['CACESA_PASSWORD']; //'1world3$';
        $WS_Dir = 'ws/';
        $proxyhost = '';
        $proxyport = '';
        $proxyusername = '';
        $proxypassword = '';

        $client = new SoapClient($WS_URL, array("trace" => 1, "exception" => 0));
        $header = new SoapHeader("http://tws1.cacesa.com/wsTest/", "Authentication", array('User' => $WS_USER, 'Password' => $WS_PASSWORD), false);
        $authParam = array("isAuthenticated" => '1');
        $authResult = $client->__soapCall("isAuthenticated", $authParam, NULL, $header);
        if ($authResult->isAuthenticatedResult) {
            $paramPostCode = '<GetPostalCode xmlns="http://tws1.cacesa.com/wsTest/">
                <CONCOUNTRY>' . $this->country->getIso() . '</CONCOUNTRY>
                <CONCP>' . $consignment->getPostcode() . '</CONCP>
                <PUPCODE></PUPCODE>
                <REGISTERED>Y</REGISTERED>
                <ResultType>json</ResultType>
            </GetPostalCode>';
            $soapBody = new \SoapVar($paramPostCode, \XSD_ANYXML);
            try {

                // $postcode_response = $client->__soapCall("GetPostalCode", $paramPostCode, NULL, $header, $out); //, true, 'rpc'
                $postcode_response = $client->__soapCall("GetPostalCode", array($soapBody), null, $header, $out); //, true, 'rpc'

                $consignment->setApiData($paramPostCode, print_r($postcode_response, true), 'GetPostalCode');
                if ($postcode_response->faultcode != '') {
                    $output['STATUS'] = 'ERROR';
                    $output['MESSAGE'] = $postcode_response->faultstring;
                    return $output;
                } else {
                    
                    $GetPostCode_result = json_decode($postcode_response->GetPostalCodeResult);

                    $certificateCode = $GetPostCode_result[0]->PCODE;
                    if ($certificateCode == "ERR:001") {
                        $output['STATUS'] = 'ERROR';
                        $output['MESSAGE'] = "Incorrect Postcode.";
                        return $output;
                    }
                    if ($certificateCode != '') {
                        $contact = $consignment->getContact();
                        if ($contact == "")
                            $contact = $consignment->getCompany();

                        $param = ' <PrintPostalLabelDetailsXML xmlns="http://tws1.cacesa.com/wsTest/">
                                                <CERTIFICATECODE>' . $certificateCode . '</CERTIFICATECODE>
                                                <CONNAME>' . htmlspecialchars($contact) . '</CONNAME>
                                                <CONADDR1>' . htmlspecialchars($consignment->getAddressLine1()) . '</CONADDR1>
                                                <CONADDR2>' . htmlspecialchars($consignment->getAddressLine2()) . " " . htmlspecialchars($consignment->getAddressLine3()) . '</CONADDR2>
                                                <CONTOWN>' . ($consignment->getCity()) . '</CONTOWN>
                                                <CONCP>' . ($consignment->getPostcode()) . '</CONCP>
                                                <SHCOUNTRY />
                                                <CONCOUNTRY>' . ($this->country->getIso()) . '</CONCOUNTRY>
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
                      
                        $soapBodyPrint = new \SoapVar($param, \XSD_ANYXML);
                        $PrintPostalLabelDetailsResult = $client->__soapCall("PrintPostalLabelDetailsXML", array($soapBodyPrint), null, $header, $out); //, true, 'rpc'

                        $consignment->setApiData($param, print_r($PrintPostalLabelDetailsResult, true), 'PrintPostalLabelDetailsXML');

                        if ($PrintPostalLabelDetailsResult != '') {

                            $label_link = $PrintPostalLabelDetailsResult->PrintPostalLabelDetailsXMLResult->string;


                            if (strpos($label_link, 'ERR') !== false) {
                                $errorcode = str_replace("ERR:", "", $label_link);
                                if ($errorcode == "001") {
                                    $error_message = "REGISTERED field is invalid or empty";
                                } else if ($errorcode == "002") {
                                    $error_message = "CERTIFICATECODE is invalid";
                                } else if ($errorcode == "003") {
                                    $error_message = "Consignee's name is invalid or empty";
                                } else if ($errorcode == "004") {
                                    $error_message = "Consignee's address is invalid or empty";
                                } else if ($errorcode == "005") {
                                    $error_message = "Consignee's country is invalid or empty.";
                                } else if ($errorcode == "006") {
                                    $error_message = "Consignee's postal code is invalid or empty";
                                } else if ($errorcode == "007") {
                                    $error_message = "Consignee's town is empty";
                                } else if ($errorcode == "008") {
                                    $error_message = "Package number is empty or less than 1.";
                                } else if ($errorcode == "009") {
                                    $error_message = "Weight is empty or less than 0,1.";
                                } else if ($errorcode == "010") {
                                    $error_message = "Declared value is empty or less than 0,1";
                                } else if ($errorcode == "050") {
                                    $error_message = "An error has occurred during the transaction, verify incorrect characters or field's precision";
                                }
                                $output['STATUS'] = 'ERROR';
                                $output['MESSAGE'] = $error_message;
                                return $output;
                            } else {
                                $pdf_decoded = file_get_contents($label_link);
                                if (trim($pdf_decoded) == '') {
                                    $output['STATUS'] = 'ERROR';
                                    $output['MESSAGE'] = "We didn't recieved any response, the service is temprary unavailable. Please contact to itsupport@oneworldexpress.com.";
                                    return $output;
                                }
                                $path = SETTING_DIR_ASSETS . 'pdf/' . date('Y_m_d') . '/' . $consignment->getId() . ".pdf";
                                $fp = fopen($path, 'wb+');
                                fwrite($fp, $pdf_decoded);
                                fclose($fp);
                                $parcelList = $consignment->getParcels();
                                $licence_plate_array = array();
                                if (count($parcelList) > 0) {
                                    $licence_plate_array[] = $certificateCode;
                                    $parcelList[0]->setTrackingNumber($certificateCode);
                                    $parcelList[0]->save();
                                }
                                $this->pdf->IncludeJS("print();");
                                $output['STATUS'] = 'SUCCESS';
                                $output['LABEL'] = date('Y_m_d') . '/' . $consignment->getId() . ".pdf";
                                $output['TRACKING_NUMBER'] = $licence_plate_array;
                                return $output;
                            }
                        } else {
                            $output['STATUS'] = 'ERROR';
                            $output['MESSAGE'] = "Unable to create label.";
                            return $output;
                        }
                    } else {
                        $output['STATUS'] = 'ERROR';
                        $output['MESSAGE'] = "Invalid postcode or address.";
                        return $output;
                    }
                }
            } catch (Exception $e) {
                echo $e->getCode() . " => " . $e->getMessage();
            }
        } else {
            $output['STATUS'] = 'ERROR';
            $output['MESSAGE'] = 'Unable to connect to API.';
        }
        return $output;
    }

     public function tracking($trackingNumber, $trackBy = 'parcel', $EDI = false) 
    {
        $tracking = new Tracking();
        $deliveredArray = array('10459','CEL0');
        
        include_once(BASE_PATH . "includes/labels/cacesaexpresstrackingstatus.class.php");
        $consignmentFilter = new ConsignmentFilter();
        $consignmentFilter->addAwbArrayFilter($trackingNumber);
        $result = $consignmentFilter->getConList('*');
        $consignment = $result[0];

        $serviceAgentConstantFilter = new ServiceConstantValueFilter();
        $serviceAgentConstantFilter->addFilter("service_id = '" . $consignment->getServiceId() . "' AND agent_id = '" . $consignment->getAgentId() . "' ");
        $serviceAgentConstant = $serviceAgentConstantFilter->getConstantList("c.constant 'constant_name', scv.constant_value 'constant_value'");
        if (count($serviceAgentConstant) > 0) {
            foreach ($serviceAgentConstant as $serviceAgentConstantData) {
                $this->constants[$serviceAgentConstantData->getConstantName()] = $serviceAgentConstantData->getConstantValue();
            }
        }

        $API_URL = $this->constants['CACESA_WSDL']; //'http://tws1.cacesa.com/ws/wscacesa.asmx?wsdl';      
        $WS_USER = $this->constants['CACESA_USERNAME']; //'onew';
        $WS_PASSWORD = $this->constants['CACESA_PASSWORD']; //'1world3$';
        $proxyhost = '';
        $proxyport = '';

        //$API_URL = "http://tws1.cacesa.com/ws/wscacesa.asmx?wsdl";
        //$END_POINT = "http://www.packetport.co.uk/ws/v1/trackingEndpoint";
        //$API_TOKEN = "KQ0FP03O11s";
        //$WS_USER = 'onew';
        //$WS_PASSWORD = '1world3$';	
        $trackingNumber = '0028037097513267001072002866'; 
        $xml_data = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:wst="http://tws1.cacesa.com/wsTest/">
                    <soapenv:Header>
                           <wst:Authentication>
                                  <!--Optional:-->
                                  <wst:User>' . $WS_USER . '</wst:User>
                                  <!--Optional:-->
                                  <wst:Password>' . $WS_PASSWORD . '</wst:Password>
                           </wst:Authentication>
                    </soapenv:Header>
                    <soapenv:Body>
                           <wst:PostalStatus>
                                  <!--Optional:-->
                                  <wst:PostalNo>' . $trackingNumber . '</wst:PostalNo>
                                  <!--Optional:-->
                                  <wst:ResultType>xml</wst:ResultType>
                           </wst:PostalStatus>
                    </soapenv:Body>
                    </soapenv:Envelope>';

        $ch = curl_init($API_URL);
        curl_setopt($ch, CURLOPT_MUTE, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: text/xml'));
        curl_setopt($ch, CURLOPT_POSTFIELDS, "$xml_data");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $jsondata = curl_exec($ch);
        curl_close($ch);

        $soap = simplexml_load_string($jsondata);
        $response = $soap->children('http://schemas.xmlsoap.org/soap/envelope/')->Body->children()->PostalStatusResponse->PostalStatusResult;
        //print_r($response); die;
        //$responseArray = $this->xml2array($response);
        $xml = simplexml_load_string($response);
        $json = json_encode($xml);
        //print_r($json); die;
        $responseArray = json_decode($json, TRUE);
        //$responseArray = $responseArray['STATUS'];

        /*$responseArrayTmp = [];
        $responseArrayTmp = $responseArray['STATUS'];

        if (isset($responseArrayTmp[0])) {
            $responseArray = $responseArrayTmp;
        } else {
            $responseArray[] = $responseArrayTmp;
        }*/
        $courire_desc = '';

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

        if (count($responseArray) > 0 && $entityId > 0) 
        {
            $parcelEntity = new Parcel($entityId);
            $finalStatusCode = $parcelEntity->getParcelStatusCode();

            foreach ($responseArray as $response) {
                $date = str_replace("T00:00:00", "", $response['SDATE']);
                $time = $response['STIME'];
                $DateTime = date("Y-m-d G:i", strtotime($date . " " . $time));
                $EventCode = $response['SCODE'];
                $EventDescription = utf8_encode($response['F1']);
                $courire_desc = $desc;
                //$ServiceAreaCode = $event['location'];
                $ServiceAreaDescription = $EventDescription;
                $Signatory = '';
                $spTrackingStatus = CacesaExpressTrackingStatus::getOweStatusCode($EventCode);
                $tracking->saveTrackPoint($entityId, $trackBy, $DateTime, $trackingNumber, $spTrackingStatus, $ServiceAreaDescription, 
                                          $EventCode, $EventDescription, $deliveredArray, $finalStatusCode);                
            }
            $tracking->saveConsignmentTrackingStatus($trackingNumber, 'CacesaExpressTrackingStatus');           
        }
    }

    public function sendData($tracking_numbers = array()) {
        $output = [];
        $agentServiceArray = [];
        $consignmentData = new ConsignmentFilter();
        $consignmentData->addFilter("     s.carrier_id= '188'", "servicefilter");
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
                    $consignmentShipmentData = $consignmentShipmentDataFilter->getColumnList(" c.id 'consignment_id', s.carrier_id ,s.code 'service_code', c.state, c.email,
                     con.region 'country_region', c.service_id, c.weight, c.hawb, c.description, c.company, c.country_id,c.awb,c.date_created,c.value,c.number_pieces,
                      c.contact, c.address_line_1, c.address_line_2, c.city, c.postcode, c.telephone, c.other_routing_code, routing_code_eur,pc.tracking_number, c.currency, c.sender_address_line_1,
                      c.sender_address_line_2, c.sender_address_line_3, c.sender_city, c.sender_postcode, c.sender_country_id, c.sender_state, c.sender_telephone, c.sender_email, c.sender_company, c.sender_name");

                    if (count($consignmentShipmentData) > 0) {
                        $consignmentIdArray = [];

                        foreach ($consignmentShipmentData as $consignmentItemData) {
                           
                            $consignmentId = $consignmentItemData->getConsignmentId();
                            $consignmentIdArray[] = $consignmentId;
                            $carrierId = $consignmentItemData->getCarrierId();
                           $result = $this->sendDataToCacesa($consignmentItemData, $this->constants[$serviceid]);
                           if($result){
                            $sql = "UPDATE consignment SET send_courier_data = 1, booked_file_id = 'Data send'
                                    WHERE id IN ('$consignmentId')  AND id <> '0' 
                                    AND  shipment_status not in ('" . Consignment::STATUS_RECYCLED . "','" . Consignment::STATUS_READY_TO_PRINT . "','" . Consignment::STATUS_INVALID . "')";
                             DbAccess3::runQuery($sql);
                                $output["STATUS"] = "SUCCESS";
                                $output["MESSAGE"] = "System has successfully send data.";
                           }
                        }
                        
                        if (empty($consignmentIdArray)) {
                            $output["STATUS"] = "ERROR";
                            $output["MESSAGE"] = "No consignment found to send data to carrier.";
                        }
                        
                    } else {
                        
                    }
                }
            }
        }
    }

    public function sendDataToCacesa($consignment, $constant) {
        require_once(BASE_PATH.'/includes/3rdparty/nusoap/nusoap.php');
        $country = new Country($consignment->getCountryId());
        http://tws1.cacesa.com/ws/wscacesa.asmx?wsdl
            $WS_URL = $constant["CACESA_WSDL"]; //'http://tws1.cacesa.com/ws/wscacesa.asmx?wsdl';
            //$WS_URL = 'http://tws1.cacesa.com/ws/wscacesa.asmx?wsdl';
            $WS_USER =   $constant["CACESA_USERNAME"]; //'onew';
            $WS_PASSWORD =  $constant["CACESA_PASSWORD"]; // '1world3$';
            $WS_Dir = 'ws/';
            $proxyhost = '';
            $proxyport = '';
            $proxyusername = '';
            $proxypassword = '';
            $client = new nusoap_client($WS_URL, 'wsdl', $proxyhost, $proxyport, $proxyusername, $proxypassword);
            $client->soap_defencoding = 'UTF-8';
            $client->decodeUTF8 = false;
            $err = $client->getError();
            if ($err) {
                return 'ERROR||<h2>Constructor error</h2><pre>' . $err . '</pre>';
            } else {
                //$header = array('Authentication' => array('User' => WS_USER, 'Password' => WS_PASSWORD));
                $header = '<Authentication xmlns="http://tws1.cacesa.com/wsTest/"><User>' . $WS_USER . '</User><Password>' . $WS_PASSWORD . '</Password></Authentication>';
                $authParam = array("isAuthenticated" => '1');

                // echo $header;
                // die;

                $authResult = $client->call('isAuthenticated', $authParam, '', '', $header, true);

                //print_r($authResult);
                //die;

                if ($client->fault) {
                    mail("itsupport@oneworldexpress.com", "UNABLE TO CONNECT CACESA", $consignment->getAwb() . "Unable to connect. Please try again after some time.");
                    return false; //"ERROR||Unable to connect. Please try again after some time.";
                } else {
                    $err = $client->getError();
                    if ($err) {
                        return false; //"ERROR||" . $err;
                    } else {
                        if ($authResult['isAuthenticatedResult']) {
                            $contact = utf8_encode($consignment->getContact());
                            if ($contact == "")
                                $contact = utf8_encode($consignment->getCompany());
                            
                            $manifestmapping = new ManifestEntityMappingFilter();
                            $manifestmapping->addFilter("     mem.entity_id in (select id from parcel where consignment_id = '". $consignment->getId() ."')");
                            $manifestList = $manifestmapping->getColumnList("id, manifest_id");
                           
                            if(count($manifestList) > 0){
                                $manifestId = $manifestList[0]->getManifestId();
                            }
                            $mawb = $manifestId;

                            if (trim($mawb) == '') {
                                $mawb = $consignment->getMawb();
                            }
                            
                            if ($country->getIso() == "ES")
                                $AirportCode = 'MAD';
                            else if ($country->getIso() == "PT")
                                $AirportCode = 'LIS';

                            $desc = $this->removeSymbols($consignment->getDescription());

                            $param = '<InsertEPostal xmlns = "http://tws1.cacesa.com/wsTest/">
                                    <CERTIFICATECODE>' . $consignment->getAwb() . '</CERTIFICATECODE>
                                    <BAG>31245</BAG>
                                    <ORIGIN>MAD</ORIGIN>
                                    <DESTINATION>' . $AirportCode . '</DESTINATION>
                                    <SHNAME>One World Express</SHNAME>
                                    <SHADDR1>One World House</SHADDR1>
                                    <SHADDR2>Pump Lane</SHADDR2>
                                    <SHTOWN>Hayes</SHTOWN>
                                    <SHCP>Ub3 3NB</SHCP>
                                    <SHPROV />
                                    <SHCOUNTRY>GB</SHCOUNTRY>
                                    <SHTAXNO />
                                    <SHCONTACT />
                                    <SHTEL />
                                    <SHMOBILE />
                                    <SHMAIL />
                                    <SHFAX />
                                    <SHREF />
                                    <CONNAME>' . utf8_encode($contact) . '</CONNAME>
                                    <CONADDR1>' . utf8_encode($consignment->getAddressLine1()) . '</CONADDR1>
                                    <CONADDR2 />
                                    <CONTOWN>' . utf8_encode($consignment->getCity()) . '</CONTOWN>
                                    <CONCP>' . utf8_encode($consignment->getPostcode()) . '</CONCP>
                                    <CONPROV />
                                    <CONCOUNTRY>' . utf8_encode($country->getIso()) . '</CONCOUNTRY>
                                    <CONTAXNO />
                                    <CONCONTACT />
                                    <CONTEL>' . utf8_encode($consignment->getTelephone()) . '</CONTEL>
                                    <CONMOBILE />
                                    <CONMAIL>' . utf8_encode($consignment->getEmail()) . '</CONMAIL>
                                    <CONFAX />
                                    <CONREF />
                                    <CONGOODSDESC>' . utf8_encode($desc) . '</CONGOODSDESC>
                                    <PACKAGES>' . utf8_encode($consignment->getNumberPieces()) . '</PACKAGES>
                                    <WEIGHT>' . number_format($consignment->getWeight(), 2) . '</WEIGHT>
                                    <VOL>0</VOL>
                                    <DELIVERYDESC />
                                    <REQINSTR />
                                    <NMAW>' . utf8_encode($mawb) . '</NMAW>
                                    <CURRENCY>GBP</CURRENCY>
                                    <DECVAL>' . number_format($consignment->getValue(), 2) . '</DECVAL>
                                    <REGISTERED>Y</REGISTERED>
                                    <PUPCODE />
                                    <ResultType>json</ResultType>
                             </InsertEPostal>';
                            $InsertEPostal = $client->call('InsertEPostal', $param, 'http://tws1.cacesa.com/wsTest/', '', $header); //, true, 'rpc'
                            //print_r($InsertEPostal,true);
                            //die;
                            //echo $InsertEPostal;

                            $consignment->setApiData($param, print_r($InsertEPostal, true), 'InsertEPostal');
                            $result = $InsertEPostal["InsertEPostalResult"];
                            //echo "<pre>";
                            //print_r($result,true);
                            //die;
                           //echo $result.  "error occurred";
                            //      die;

                            $result = str_replace("[", "", $result);
                            $result = str_replace("]", "", $result);
                            $result = str_replace('"', '', $result);

                            if (strpos($result, 'ERR') !== false) {


                                $errorcode = str_replace("ERR:", "", $result);
                                if ($errorcode == "001") {
                                    $error_message = "Airport of origin invalid";
                                } else if ($errorcode == "002") {
                                    $error_message = "Airport of destination invalid";
                                } else if ($errorcode == "003") {
                                    $error_message = "REGISTERED field is invalid or empty";
                                } else if ($errorcode == "004") {
                                    $error_message = "CERTIFICATECODE is invalid";
                                } else if ($errorcode == "005") {
                                    $error_message = "Consignee’s name is invalid or empty";
                                } else if ($errorcode == "006") {
                                    $error_message = "Consignee’s address is invalid or empty";
                                } else if ($errorcode == "007") {
                                    $error_message = "Consignee’s country is invalid or empty.";
                                } else if ($errorcode == "008") {
                                    $error_message = "Consignee’s postal code is invalid or empty";
                                } else if ($errorcode == "009") {
                                    $error_message = "Consignee’s town is empty";
                                } else if ($errorcode == "010") {
                                    $error_message = "Goods description is empty.";
                                } else if ($errorcode == "011") {
                                    $error_message = "Package number is empty or less than 1.";
                                } else if ($errorcode == "012") {
                                    $error_message = "Weight is empty or less than 0,1.";
                                } else if ($errorcode == "013") {
                                    $error_message = "Bag field is empty";
                                } else if ($errorcode == "014") {
                                    $error_message = "Mawb field is empty";
                                }
                              else if ($errorcode == "016") {
                                    $error_message = "Currency code is invalid";
                                } else if ($errorcode == "050") {
                                    $error_message = "An error has occurred during the transaction, verify incorrect characters or field's precision";
                                }
                                $headers = "From: itsupport@oneworldexpress.com\r\nReply-To: itsupport@oneworldexpress.com";
                                $headers = "MIME-Version: 1.0 \r\n";
                                $headers .= "Content-type: text/html; charset=iso-8859-1\r\n";

                                $message = "Unable to send data for below tracking number <br />" . $consignment->getAwb() . " " . $mawb . " " . $error_message;

                                mail("itsupport@oneworldexpress.com", "CACESA SEND DATA FAIL", $message . $error_message, $headers);

                                return false;
                            } else {
                                //$consignment->setHungaryDataSent(1);
                                //$consignment->save();
                                return true;
                            }
                        } else {
                            mail("itsupport@oneworldexpress.com", "CACESA Authentication Failed", $consignment->getAwb() . "CACESA Authentication Failed");
                        }
                    }
                }
            }
    }

    public function manifest($consignment) {
        
    }

    public function preAdvice($consignment) {
        
    }

    public function recycledShipment($consignment) {
        $output["STATUS"] = "SUCCESS";
        return $output;
    }
    
     private function removeSymbols($data)
        {
            return preg_replace('/[^\p{L}\p{N}\s]/u', '', $data);
        }
	

	private function removecommas($data)
	{
	   return str_replace(",", " ", $data);

	}

}
