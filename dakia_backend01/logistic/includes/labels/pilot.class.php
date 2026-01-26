<?php

class Pilot implements CarrierService {

    private $pdf;
    private $serviceValues = null;
    private $agentValues = null;
    private $constants = null;
    private $user = null;
    private $country = null;

    public function __construct() {
       
    }

    public function validation(Consignment $consignment, Services $service, Country $country) {
        
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
        if (trim(@$this->constants['INTEGRATION_TYPE'])== '' ||  trim(@$this->constants['PILOT_URL']) == '' || trim(@$this->constants['PILOT_USERNAME']) == '' || trim(@$this->constants['PILOT_PASSWORD']) == '' || trim(@$this->constants['PILOT_CLIENT_CODE']) == '') {
            $output['STATUS'] = 'ERROR';
            $output['MESSAGE'] = "Please check service constants. Please contact to itsupport@oneworldexpress.com";
            return $output;
        }

        if (trim(@$this->constants['INTEGRATION_TYPE']) == 'API') {
            $output = $this->apiLabel($this->constants, $consignment);
        }
        return $output;
    }

    private function apiLabel($constants, Consignment $consignment) {
        require_once('../includes/3rdparty/nusoap/nusoap.php');
        $API_URL =  $constants["PILOT_URL"]; //"http://www.tkttracking.com/tikitingwebserviceextra.asmx?wsdl";
	$API_USER =  $constants["PILOT_USERNAME"];  //"oneworld";
	$API_PASSWORD =  $constants["PILOT_PASSWORD"]; // "ow1001";
	$API_CLIENT_CODE =  $constants["PILOT_CLIENT_CODE"]; // "67";
        
        $output = array();
        $proxyhost = '';
        $proxyport = '';
        $proxyusername = '';
        $proxypassword = '';
        $clientProvience = $consignment->getState();
        if ($clientProvience == "")
            $clientProvience = "-";
        
        $client = new nusoap_client($API_URL, 'wsdl', $proxyhost, $proxyport, $proxyusername, $proxypassword);
        $header = '';
        $createShipmentParam = array(
            'Ship' => array(
                'user' => $API_USER,
                'password' => $API_PASSWORD,
                'shipmentidentification' => $consignment->getHawb(),
                'boxidentification' => $consignment->getHawb(),
                'clientcode' => $API_CLIENT_CODE,
                'shipmenttype' => '1',
                'boxweight' => $consignment->getWeight() * 1000,
                'departuredate' => date("Ymd"),
                'destinationclientname' => utf8_encode($consignment->getCompany()),
                'destinationclientsurname' => utf8_encode($consignment->getContact()),
                'destinationclientaddress1' => utf8_encode($consignment->getAddressLine1()),
                'destinationclientaddress2' => utf8_encode($consignment->getAddressLine2()),
                'destinationclienttown' => utf8_encode($consignment->getCity()),
                'destinationclientpostalcode' => $consignment->getPostcode(),
                'destinationclientprovince' => $clientProvience,
                'destinationclientcountry' => $this->country->getIso(),
                'destinationclientphonenumber1' => $consignment->getTelephone(),
                'destinationclientphonenumber2' => '',
                'destinationclientemail' => $consignment->getEmail(),
                'deliverobservations' => '',
                'freefield1' => '',
                'freefield2' => '',
                'freefield3' => '',
                'cod' => '0.00',
                'warehousecode' => '0',
                'totallabels' => $consignment->getNumberPieces(),
                'typelabel' => 'PDF',
            )
        );
        $createShipmentParamResponse = $client->call('CreateShipment', $createShipmentParam);

        
        if (!isset($createShipmentParamResponse['CreateShipmentResult']['Errors']['id'])) {
            
            $waybill = $createShipmentParamResponse['CreateShipmentResult']['WebLabel']['CourierId'];
            $trackingNumber = $createShipmentParamResponse['CreateShipmentResult']['WebLabel']['BARCODE'];
            $licence_plate_array[] = $trackingNumber;
            $parcelList = $consignment->getParcels();
            if (count($parcelList) > 0) {
                $parcelList[0]->setTrackingNumber($trackingNumber);
                $parcelList[0]->save();
            }
            $pdf1 = $createShipmentParamResponse['CreateShipmentResult']['WebLabel']['LabelBytes'];
            $results = base64_decode($pdf1);

            $path = SETTING_DIR_ASSETS . 'pdf/' . date('Y_m_d') . '/' . $consignment->getId() . ".pdf";
            $fp = fopen($path, 'wb+');
            fwrite($fp, $results);
            fclose($fp);
            
//            if ($consignment->getAccount() == 'WOLANSKI') {
//                exec("convert  -density 300  /var/www/vhosts/oneworldexpress.co.uk/httpdocs/remote/_assets/pdf/" . date('Y_m_d') . "/" . $consignment->getId() . ".pdf /var/www/vhosts/oneworldexpress.co.uk/httpdocs/remote/_assets/label_images/" . $consignment->getId() . ".png", $output);
//
//                exec("convert  -density 300  /var/www/vhosts/oneworldexpress.co.uk/httpdocs/remote/_assets/label_images/" . $consignment->getId() . ".png /var/www/vhosts/oneworldexpress.co.uk/httpdocs/remote/_assets/pdf/" . date('Y_m_d') . "/" . $consignment->getId() . ".pdf", $output);
//            }
            $output['STATUS'] = 'SUCCESS';
            $output['LABEL'] = date('Y_m_d') . '/' . $consignment->getId() . ".pdf";
            $output['TRACKING_NUMBER'] = $licence_plate_array;
            return $output;
        } else {
            $error = $createShipmentParamResponse['CreateShipmentResult']['Errors']['description'];
            $output["STATUS"] = "ERROR";
            $output["MESSAGE"] =   $error;
            return $output;
        }
    }

    public function tracking($trackingNumber, $trackBy = 'parcel', $EDI = false) 
    {
        $consignmentFilter   =     new ConsignmentFilter(); 
	$consignmentFilter->addawbFilter($trackingNumber);
	$list = $consignmentFilter->getConList();	
        
	if(count($list) > 0)
	{          
            if($list[0]->getCountryId() == '150')
            {
                $this->GetPilotNLTracking($list, $trackBy);
            }
            else
            {
                $this->GetPilotHungaryTracking($list,  $trackBy);
            }
        }
    }
    
    private function GetPilotNLTracking($conObj)
    {
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
            $countryId = $conObj[0]->getCountryId();
            $country = new Country($countryId);
            $isoCode = $country[0]->getIso();
            $trackingNumber = $conObj[0]->getAwb();
            $postcode = $conObj[0]->getPostCode();

            $url = "https://www.internationalparceltracking.com/api/shipment?barcode=".$trackingNumber."&country=".$isoCode."&language=en&postalCode=".$postcode;
            $response = file_get_contents($url);
            $jsonArray = json_decode($response, true);
            $trackingArray = $jsonArray['trackingEvents'];
     
            foreach($trackingArray as $tracking)
            {
                $DateTime = date("Y-m-d G:i", strtotime($tracking['dateTime']));
                $EventCode = $tracking['description']; //$response['SCODE'];
                $EventDescription = $tracking['description']; 
                $ServiceAreaDescription = $tracking['location'];
                $Signatory = '';
                $spTrackingStatus = PilotTrackingStatus::getOweStatusCode($EventCode);

                $trackingDataFilter = new TrackingDataFilter();
                $trackingDataFilter->addTrackPointExistFilter($trackingNumber, $spTrackingStatus, $ServiceAreaDescription, $EventCode, $EventDescription, $DateTime);
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
            $trackingDataObj = $trackingDataFilter->getColumnList('entity_id,entity_type,carrier_code,status_code_id,date_created');
            if (count($trackingDataObj) > 0) 
            {
                $trackingDataObj = $trackingDataObj[0];
                $carrierCode = $trackingDataObj->getCarrierCode();
                $oweTrackingStatusCode = $trackingDataObj->getStatusCodeId();
                $entityType = $trackingDataObj->getEntityType();
                $entityId = $trackingDataObj->getEntityId();
                $consignmentStatusCode = PilotTrackingStatus::getConsignmentStatus($oweTrackingStatusCode);
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
    
    private function GetPilotHungaryTracking($conObj, $trackBy)
    {
        $entityId = 0;
        $trackBy = 'shipment';
        
        $trackingNumber = $conObj[0]->getAwb();
        
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
            //$trackingNumber = $conObj[0]->getAwb();
            $response = file_get_contents("https://tracking.expressone.hu/?trackingNr=".$trackingNumber);    
            
            $postable = strpos($response, '<table cellspacing="0" cellpadding="4" id="ContentPlaceHolder1_GridView1" style="border-collapse:collapse;">');
            $table_start_part =  substr ($response, $postable, strlen($response) );	
            $pos2 = strpos($table_start_part, '</table>');
            $table_end_part =  substr($table_start_part, 0 , $pos2 );

            $DOM = new DOMDocument;
            @$DOM->loadHTML($table_end_part);
            @$trs = $DOM->getElementsByTagName('tr');
            
            foreach($trs as $tr)
            {					
                $tds = $tr->getElementsByTagName('td');
                $DateTime = $tds->item(1)->nodeValue;
                $EventCode = utf8_encode($tds->item(3)->nodeValue); //$response['SCODE'];
                $EventDescription = utf8_encode($tds->item(3)->nodeValue); 
                $ServiceAreaDescription = $tds->item(4)->nodeValue;
                $Signatory = '';
                $spTrackingStatus = PilotTrackingStatus::getOweStatusCode($EventCode);

                $trackingDataFilter = new TrackingDataFilter();
                $trackingDataFilter->addTrackPointExistFilter($trackingNumber, $spTrackingStatus, $ServiceAreaDescription, $EventCode, $EventDescription, $DateTime);
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
            $trackingDataObj = $trackingDataFilter->getColumnList('entity_id,entity_type,carrier_code,status_code_id,date_created');
            if (count($trackingDataObj) > 0) 
            {
                $trackingDataObj = $trackingDataObj[0];
                $carrierCode = $trackingDataObj->getCarrierCode();
                $oweTrackingStatusCode = $trackingDataObj->getStatusCodeId();
                $entityType = $trackingDataObj->getEntityType();
                $entityId = $trackingDataObj->getEntityId();
                $consignmentStatusCode = PilotTrackingStatus::getConsignmentStatus($oweTrackingStatusCode);
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
