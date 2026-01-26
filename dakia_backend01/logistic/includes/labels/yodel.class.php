<?php
include_classes([
    'include_list',
    ], 'reamus');
include_classes([
    'carrierdatafilelog.class',
    'carrierdatafilelogfilter.class',
    'supplierinvoices.class',
    'supplierinvoicesfilter.class'
]);
class Yodel implements CarrierService {

    private $pdf;
    private $user = null;
    private $serviceValues = null;
    private $agentValues = null;
    private $constants = null;
    private $countryName = null;
    private $consignment = null;
    private $record_idx = 0;
    private $link_file = null;
    private $routing_code = null;
    private $isRemoteArea = 0;
    private $customValueCount = null;
    /* const IN_TRANSIT = 42;
      const BAD_ADDRESS = 47;
      const DAMAGED = 47;
      const AWAITING_COLLECTION = 33;
      const ADDRESS_QUERY = 33;
      const RETURNED_TO_SENDER = 24;
      const CONSIGNEE_NOT_KNOWN = 47;
      const REFUSED_BY_CONSIGNEE = 24;
      const DELAY = 44;
      const PARCEL_DATA_RECEIVED_AWAITING_COLL = 15; */

    public function __construct() {
        
    }

    public function getDeliveryNetwork() {

        if ($this->delivery_network == null) {
            $this->delivery_network = DeliveryNetwork::deliveryNetworkFactory($this);
        }

        return $this->delivery_network;
    }

    public function validation(Consignment $consignment, Services $service, Country $country) {

        $returnOutput = array();
        $consignment->setCountryIsoCode($country->getIso());
        $consignment->setCountry($country->getName());

        /*
         * P.O.Box address validation
         */
        
        $addressLine1 = strtoupper($consignment->getAddressLine1());
        $addressLine2 = strtoupper($consignment->getAddressLine2());
        $addressLine3 = strtoupper($consignment->getAddressLine3());

        if(strpos($addressLine1, "P.O.BOX") !== false || strpos($addressLine2, "P.O.BOX") !== false || strpos($addressLine3, "P.O.BOX") !== false){
          $returnOutput[] = "Unable to send parcel to P.O.BOX Address";  
        } 
        /*
         * Post Code Validation
         */
        

        //$serviceToValidate = $this->ValidateYodelPostcode($postCodeConsignment, $service->getCode());
        //$networkDay = new DeliveryNetworkDay($consignment);
        $serviceToValidate = $this->getHandling($service->getCode());
        /* if (!$networkDay->isAddressValidDomestic($serviceToValidate)) {
          foreach ($networkDay->getErrorList() as $errorItems) {
          $returnOutput[] = $errorItems;
          }
          } */
        $postcode = substr(trim($consignment->getPostcode()), 0,2);
        if($country->getIso() == "IE" && strtoupper($postcode) != 'BT'){
            $originalPostcode = $consignment->getPostcode();
            $consignment->setPostcode("DB999AA");
            $consignment->setState($originalPostcode);
        }
       // else
        {
            $networkDay = new DeliveryNetworkDay($consignment);
            if (!$networkDay->isAddressValidDomestic($serviceToValidate)) {
            //print_r($networkDay->getErrorList());
                foreach ($networkDay->getErrorList() as $errorItems) {
                    $returnOutput[] = $errorItems;
                }
            }
            $carrierId = $service->getCarrierId();
            $carrierObject = new Carrier((int) $carrierId);
            /*
             * REMOTE AREA POSTCODE CHECK
             */
            $remoteAreaCheck = $this->remoteareas($consignment, $carrierObject, $sender);
            switch ($remoteAreaCheck) {
                case 'allowed':
                    break;
                case 'not-allowed':
                    $returnOutput[] = "Your postcode ( " . $consignment->getPostcode() . " ) is a remote area. Please contact to administrator to activate.";
                    break;
            }
        }
        
        /*
         * No UK shipment allow only single piece
         */

        $two_level_postcode = substr($consignment->getPostcode(),0,2);
        $serviceCode = $service->getCode();
        if ($serviceCode == "STYDL1CSP" || $serviceCode == "STYDL1CSN" || $serviceCode == "STYDL1CEN" || $serviceCode == "STYDL1EEP" || $serviceCode == "STYDL1EEN" || strtoupper($two_level_postcode) == "BT") {
            if($consignment->getNumberPieces() > 1){
                $returnOutput[] = "Service allow single piece shipment only.";
            }
        }
        
        /*
         * Itemwise details require check for NI shipments 
         */
        
        if(strtoupper($two_level_postcode) == "BT"){
            $itemDetails = $consignment->getItems();
            if(count($itemDetails) > 0){
                $errorMessage = true;
                foreach($itemDetails as $item){
                    $errorMessage = false;
                    $itemQty = $item["no_of_items"];
                    $itemDesc = $item["item_description"];
                    $itemValue = $item["item_value"];
                    $itemHscode = $item["hscode"];
                    $itemManufactureCountry = $item["manufacture_country_iso"];
                    if(trim($itemQty) <= 0 || trim($itemDesc) == "" || trim($itemValue) <= 0 || trim($itemHscode) == "" || trim($itemManufactureCountry) == "" ){
                        $errorMessage = true;
                    }
                }
                if($errorMessage){
                    $returnOutput[] = "Please provide itemized details for each item in parcels like value , qty, hscode and other required fields.";
                }
            }
            else
            {
                $returnOutput[] = "Please provide itemized details for each item in parcels like value , qty, hscode and other required fields.";
            }
            
            if(trim($consignment->getEoriNumber()) == ""){
                $returnOutput[] = "Please enter valid EORI number";
            }
        }
        
        
        return $returnOutput;
    }

    public function remoteareas($consignment, $carrierObject, $sender) {

        $postCode = str_replace(" ", "", $consignment->getPostcode());
        $countryId = $consignment->getCountryId();
        $serviceId = $consignment->getServiceId();
        $customizedServiceId = $consignment->getCustomizedServiceId();
        $userId = $consignment->getUserId();


        $postCodeA = substr($postCode, 0, -3);

		$postCodeAPreCheck	=	substr($postCode, 0, 2);
		
				
				
         $sql = "SELECT 
                    remotearea_check , remoteareas_groups_id
                FROM
                        carrier c INNER JOIN 
                    remoteareas_groups rag ON c.id = rag.carrier_id
                        INNER JOIN
                    remoteareas ra ON rag.id = ra.remoteareas_groups_id  AND ra.is_deleted = 'N'"
                . " AND rag.carrier_id = '" . $carrierObject->getId() . "'"
                . " AND ra.country_id= '" . $countryId . "'"
                . "AND ("
                . "       LOWER(to_postcode) = '" . strtolower(DbAccess3::escape($postCodeA)) . "' "
                . "     OR  LOWER(to_postcode) = '" . strtolower(DbAccess3::escape($postCode)) . "'    
                        ) group by ra.remoteareas_groups_id";
        $remoteareas = Remoteareas::getRemoteareasListFromSql($sql);
	
		
		
		
        if (count($remoteareas) > 0 || in_array($postCodeAPreCheck, array('BT','NA','IM','JE','GY'))) {
            $sql = "SELECT 
                    id
                FROM
                    user_services_routing
                WHERE 
                    user_account_id in ( SELECT user_account_id FROM user WHERE id = '" . DbAccess3::escape($userId) . "'  ) 
                    AND service_id = '" . DbAccess3::escape(((trim($customizedServiceId) == '' || $customizedServiceId == 0)? $serviceId:$customizedServiceId )) . "'
                    AND is_remotearea = 1";

            $remoteareasUserCheck = Remoteareas::getRemoteareasListFromSql($sql);
            if (count($remoteareasUserCheck) > 0) {
                $this->isRemoteArea = 1;
                return 'allowed';
            } else {
                return 'not-allowed';
            }
        } else {
            $this->isRemoteArea = 0;
        }
    }

    public function isRemoteArea() {
        return $this->isRemoteArea;
    }

    private function getHandling($handling) {
        if ($handling == "STYDL03HS" || $handling == "STYDL003H")
            $serviceToValidate = "3H";
        else if ($handling == "STYDL01HS" || $handling == "STYDL001H")
            $serviceToValidate = "1H";
        else if ($handling == "STYDL3HPA")
            $serviceToValidate = "3HPA";
        else if ($handling == "STYDL01CN" || $handling == "STYDLN1CN")
            $serviceToValidate = "1CN";
        else if ($handling == "STYDL2CXN")
            $serviceToValidate = "2CXN";
        else if ($handling == "STYDL03HN")
            $serviceToValidate = "3HN";
        else if ($handling == "STYDLISLE")
            $serviceToValidate = "ISLE";
        else if ($handling == "STYDL1CEN")
            $serviceToValidate = "1CEN";
        else if ($handling == "STYDL1CSN")
            $serviceToValidate = "1CSN";
        else if ($handling == "STYDL1CSP")
            $serviceToValidate = "1CSP";
        else if ($handling == "STYDL02CP")
            $serviceToValidate = "2CP";
        else if ($handling == "STYDL2VLP")
            $serviceToValidate = "2VLP";
        else if ($handling == "STYDL1CEP")
            $serviceToValidate = "1CEP";
        else if ($handling == "STYDL01CP")
            $serviceToValidate = "1CP";
        else if ($handling == "STYDL01VP")
            $serviceToValidate = "1VP";
        else if ($handling == "STYDL1VSP")
            $serviceToValidate = "1VSP";
        else if ($handling == "STYDL02VP")
            $serviceToValidate = "2VP";
        else if ($handling == "STYDL1EEP")
            $serviceToValidate = "1EEP";
        else if ($handling == "STYDL1EEN")
            $serviceToValidate = "1EEN";
        else if ($handling == "STYDL12CN")
            $serviceToValidate = "2CN";
        else if ($handling == "STYDL01VN")
            $serviceToValidate = "1VN";
        else if ($handling == "STYDL1VSN")
            $serviceToValidate = "1VSN";
        else if ($handling == "STYDL02VN")
            $serviceToValidate = "2VN";
        else if ($handling == "STYDL2VPR")
            $serviceToValidate = "2VPR";
        else if ($handling == "STYDL2VLN")
            $serviceToValidate = "2VLN";
        
        return $serviceToValidate;
    }

    public function label($consignment, $labelType = 'pdf', $size = '100x150') {
        $labelType = (in_array($labelType, ['pdf', 'zpl']) ? $labelType : 'pdf');
        
        $output = array();
        $this->serviceValues = new Services($consignment->getServiceId());
        $this->countryName = new Country($consignment->getCountryId());

        $serviceAgentConstantFilter = new ServiceConstantValueFilter();
        $serviceAgentConstantFilter->addFilter("service_id = '" . $consignment->getServiceId() . "' AND agent_id = '" . $consignment->getAgentId() . "' ");
        $serviceAgentConstant = $serviceAgentConstantFilter->getConstantList("c.constant 'constant_name', scv.constant_value 'constant_value'");
        if (count($serviceAgentConstant) > 0) {
            foreach ($serviceAgentConstant as $serviceAgentConstantData) {
                $this->constants[$serviceAgentConstantData->getConstantName()] = $serviceAgentConstantData->getConstantValue();
            }
        }

        if (trim(@$this->constants['YODEL_SHIPPER_POSTCODE']) == '' || trim(@$this->constants['YODEL_SHIPPER_COUNTRY']) == '' || trim(@$this->constants['YODEL_SHIPPER_CONTACT']) == '' || trim(@$this->constants['YODEL_SHIPPER_CITY']) == '' || trim(@$this->constants['YODEL_SHIPPER_ADDRESS_LINE_1']) == '' || trim(@$this->constants['YODEL_SHIPPER_ACCOUNT_NUMBER']) == '' || trim(@$this->constants['YODEL_METER_NUMBER']) == '') {
            $output['STATUS'] = 'ERROR';
            $output['ERROR'][] = "Please check service constants. Please contact to itsupport@oneworldexpress.com";
            $output['MESSAGE'] = "Please check service constants. Please contact to itsupport@oneworldexpress.com";
            return $output;
        }
        /*
         *  Get Tracking Number ranges
         */
        $serviceRangeMappingFilter = new ServiceRangeMappingFilter();
        $serviceRangeMappingFilter->addFilter("service_id = '" . $consignment->getServiceId() . "' AND agent_id = '" . $consignment->getAgentId() . "' ");
        $serviceRange = $serviceRangeMappingFilter->getList(false);
        if (count($serviceRange) > 0) {
            $licence_plate_id = (int) $serviceRange[0]->getLicencePlateId();
        }
        if ($licence_plate_id < 0) {
            $output['STATUS'] = 'ERROR';
            $output['ERROR'][] = "This service does not have tracking number range. Please contact to itsupport@oneworldexpress.com";
            $output['MESSAGE'] = "This service does not have tracking number range. Please contact to itsupport@oneworldexpress.com";
            return $output;
        }

        $parcel_list = $consignment->getParcels();
        $parcel_idx = 0;
        $pdf = new PdfBase(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $this->pdf = $pdf;
        // Generate label for each parecel
        foreach ($parcel_list as $parcel) {
            if ($parcel->getTrackingNumber() == '') {
                $resultArray = LicencePlate::getLicencePlateNumber($licence_plate_id);
                if (trim($resultArray['STATUS']) == 'ERROR')
                    return $resultArray;
                else
                    $licence_plate = $resultArray["PREFIX"] . @$this->constants['YODEL_METER_NUMBER'] . $resultArray["RANGE"] . $resultArray["SUFIX"];
                $parcel->setTrackingNumber($licence_plate);
                $parcel->save();
            }
            else {
                $licence_plate = $parcel->getTrackingNumber();
            }
            $licence_plate_array[$parcel_idx] = $licence_plate;
            $labelsize = explode("x", strtolower(trim($size)));
            $width = trim($labelsize[0]);
            $height = trim($labelsize[1]);
            $labelSizeName = $width . "X" . $height;

            
            $functionName = "label" . strtolower($labelType) . $labelSizeName;
            if (method_exists(__CLASS__,$functionName)) {
               
                if ($labelType == 'pdf') {
                    // pdf label generation
                    $this->pdf->SetPrintFooter(false);
                    $this->pdf->SetFooterMargin(0);
                    $this->pdf->SetAutoPageBreak(false, 0);
                    $page_size = array($height, $width);
                    $this->pdf->AddPage("P", $page_size);
                }
              
               $fileName = $this->$functionName($consignment, $parcel_idx, $licence_plate, $parcel);
            } else {
                 if ($labelType == 'pdf') {
                    // pdf label generation
                    $this->pdf->SetPrintFooter(false);
                    $this->pdf->SetFooterMargin(0);
                    $this->pdf->SetAutoPageBreak(false, 0);
                    $page_size = array(150, 100);
                    $this->pdf->AddPage("P", $page_size);
                    $this->labelpdf100X150($consignment, $parcel_idx, $licence_plate, $parcel);
                }
                else if($labelType == 'zpl'){
                   $fileName =  $this->labelzpl100X150($consignment, $parcel_idx, $licence_plate);
                }                
            }
            ++$parcel_idx;
        }

        if ($labelType == 'pdf') {
            $fileName = date('Y_m_d') . '/' . $consignment->getId() . ".pdf";
            $this->pdf->IncludeJS("print();");
            $this->pdf->Output("../_assets/pdf/" . $fileName, "F");
        }
        $output['STATUS'] = 'SUCCESS';
        $output['LABEL'] = $fileName;
        $output['TRACKING_NUMBER'] = $licence_plate_array;
        return $output;
    }

    public function tracking($trackingNumber, $trackBy = 'parcel', $EDI = false) 
    {
        $tracking = new Tracking();
        $url = "https://tracking.yodel.co.uk/wrd/run/wt_xml_gen_pw.getParcelHistory";
        $myvars = "pcl_no=" . $trackingNumber;
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $myvars);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($ch);
        $xmlData = simplexml_load_string($response, "SimpleXMLElement", LIBXML_NOCDATA);
        $jsonData = json_encode($xmlData);
        $trackingResponse = json_decode($jsonData, TRUE);
        //echo "<pre>"; print_r($trackingResponse); echo "</pre>"; exit;
        
        if ($trackingResponse['response']['query_status'] == 0) 
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
      
            ////////////////////// Carrier Received ////////////////////////////////
            
            $trackingDataFilterObj = new TrackingDataFilter();
            $trackingDataFilterObj->addFieldFilter('    tracking_number', $trackingNumber);        
            $trackingDataFilterObj->addFilter("carrier_code not in ('','1')");
            $trackingEvents = $trackingDataFilterObj->getList();

            if(count($trackingEvents) > 0)
            {
                $carrierReceivedCheck = 0;   // there is already carrier received event                
                $trackingDataFilterObj = new TrackingDataFilter();
                $trackingDataFilterObj->addFieldFilter('    tracking_number', $trackingNumber);        
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
           
            $parcelStatusTmp = [];
            $parcelStatusTmp = $trackingResponse['parcel_status'];
            
            if (isset($parcelStatusTmp[0]))
                $parcelStatus = $parcelStatusTmp;
            else
                $parcelStatus[] = $parcelStatusTmp;
                        
            $parcelStatus = array_reverse($parcelStatus);
        //    echo "<pre>";
         //   print_r($parcelStatusTmp);
      //    echo count($parcelStatus)." > 0 && ".$entityId." > 0 && ".trim($parcelStatus[0])." != ''" ;
         // die;
 //           if (count($parcelStatus) > 0 && $entityId > 0 && trim($parcelStatus[0]) != '') 
            if (count($parcelStatus) > 0 && $entityId > 0) 
            {                
                //$parcelEntity = new Parcel($entityId);
                //$finalStatusCode = $parcelEntity->getParcelStatusCode();
                
                foreach ($parcelStatus as $event) 
                {
                    $parcelEntity = new Parcel($entityId);
                    $finalStatusCode = $parcelEntity->getParcelStatusCode();
                    $Date = date("Y-m-d", strtotime(trim($event['scan_date'])));
                    $Time = trim($event['scan_time']);
                    $DateTime = $Date . " " . $Time;
                    $EventCode = $event['status_code'];
                    $Signatory = is_string($event['signatory']) ? " Signed By - " . $event['signatory'] : '';
                    $EventDescription = $event['status_description'] . $Signatory;
                    $ServiceAreaDescription = $event['location'];
                    
                    if($EventCode == 'EA')
                    {
                        continue;
                    }
                    // Dont enter any other event code if it is against 148 Event Code
                    if($carrierCodeCarrierReceived == $EventCode)
                    {
                        continue;
                    }
                    
                    //$spTrackingStatus = YodelTrackingStatus::getOweStatusCode($EventCode);   
                    $deliveredArray = array('HD','MU','PE','Z','ZA','ZC','ZD','ZE','ZF','ZH','ZK','ZN','ZR','ZS','ZT');  
                    
                    ////////////////////// Carrier Received ////////////////////////////////
                    if($carrierReceivedCheck == 1 && $EventCode != '1' )
                    {
                        $spTrackingStatus = '148';
                        $carrierReceivedCheck = 0 ;                        
                    }
                    else
                    {
                        $spTrackingStatus = YodelTrackingStatus::getOweStatusCode($EventCode);
                    }            
                    ////////////////////// Carrier Received ////////////////////////////////
                   
                   $result = $tracking->saveTrackPoint($entityId, $trackBy, $DateTime, $trackingNumber, $spTrackingStatus, $ServiceAreaDescription, $EventCode, $EventDescription, $deliveredArray, $finalStatusCode);                                        
                    if($result == true)
                    {
                        break;
                    }
                }               
                $tracking->saveConsignmentTrackingStatus($trackingNumber, 'YodelTrackingStatus');  
            }
        }   
    }

    public static function yodelstateText($theState) {
        if (!isset(YodelTrackingStatus::$yodel_status_code[$theState]))
            return "Unknown";

        return YodelTrackingStatus::$yodel_status_code[$theState];
    }

    public function sendData($tracking_numbers = array()) {
        $output = [];
        
        $agentServiceArray = [];
        $consignmentData = new ConsignmentFilter();
        $consignmentData->addFilter("     s.carrier_id= '16'", "servicefilter");
        $consignmentData->addFilter("    ( c.send_courier_data= '0' or c.send_courier_data is null)", "consignmentfilter");
        if (!empty($tracking_numbers))
            $consignmentData->addFilter("     AND pc.tracking_number in ('" . implode("','", $tracking_numbers) . "') and pc.tracking_number <> ''", "parcelJoinFilter");
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
                $this->record_idx = 0;
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
                    // GET ALL THE CONSIGNMENT WITH THE PARCEL FOR PREPARE YODEL FILE
                    $consignmentShipmentDataFilter = new ConsignmentFilter();
                    $consignmentShipmentDataFilter->addFilter("     c.service_id = '" . $serviceid . "' AND c.agent_id = '" . $agentid . "' ", "consignmentfilter");
                    $consignmentShipmentDataFilter->addFilter("     ( c.send_courier_data= '0' or c.send_courier_data is null) ", "consignmentfilter");
                    if (!empty($tracking_numbers))
                        $consignmentShipmentDataFilter->addFilter("     AND pc.tracking_number in ('" . implode("','", $tracking_numbers) . "') and pc.tracking_number <> ''", "parcelJoinFilter");
                    $consignmentShipmentData = $consignmentShipmentDataFilter->getColumnList(" c.id 'consignment_id', s.carrier_id ,s.code 'service_code',
                     con.region 'country_region', c.service_id, c.weight, c.hawb, c.description, c.company,
                      c.contact, c.address_line_1, c.address_line_2, c.city, c.postcode, c.telephone, c.eori_number, c.country_id ");


                    if (count($consignmentShipmentData) > 0) {

                        $consignmentIdArray = [];
                        foreach ($consignmentShipmentData as $consignmentItemData) {
                            ///////////////////////////////////////////////////////////////////////////////////////////////////
                            ///
                            $consignmentId = $consignmentItemData->getConsignmentId();
                            $consignmentIdArray[] = $consignmentId;
                            $serviceCode = $consignmentItemData->getServiceCode();
                            $carrierId = $consignmentItemData->getCarrierId();
                            $serviceType = $consignmentItemData->getCountryRegion();
                            $parcelOjects = $consignmentItemData->getParcels();
                            
                            $parcel = [];
                            foreach ($parcelOjects as $parcels) {
                                $parcel[] = $parcels->getTrackingNumber();
                            }
                            $this->meterNumber = $this->constants[$serviceid]['YODEL_BOOKING_FILE_NUMBER'];
                            // Add sender address record
                            if ($this->record_idx == 0)// && $serviceCode == "3HPA") {
                                $this->record_array[] = $this->getSenderAddressRecord($agentObject, $agentCountryObject);
                            ++$this->record_idx;

                            // Check the note number (zero if not domestic)
                            $noteNumber = 0;
                            $noteNumber = $this->record_idx;

                            $this->record_idx = ( ++$this->record_idx % 1000);

                            // set manifest type and get handling code
                            $manifest_type = "FTP";
                            //if ($serviceType == "R1") {
                            //    $manifest_type = "EDI-EU";
                            //}
                            $handling = $this->getHandling($serviceCode);


                            // build consignment record
                            $record = $this->getConsignmentRecord($consignmentItemData, $noteNumber, $manifest_type, $handling, $parcel);
                            $this->record_array[] = $record;
                            $two_level_postcode = substr($consignmentItemData->getPostcode(),0,2);
                            $this->customValueCount = 0;
                            if ($serviceCode == "STYDL1CSP" || $serviceCode == "STYDL1CSN" || $serviceCode == "STYDL1CEN" || $serviceCode == "STYDL1EEP" || $serviceCode == "STYDL1EEN" || $two_level_postcode == "BT") {
                                $this->record_array[] = $this->CustomRecord($consignmentItemData, $parcelOjects);
                            }

                            // Address record
                            $record = $this->getDeliveryAddressRecord($consignmentItemData);
                            $this->record_array[] = $record;


                            foreach ($parcel as $parcel_licenceplate) {

                                $record = $this->getParcelRecord($parcel_licenceplate, "", $noteNumber);
                                $this->record_array[] = $record;
                            }
                        }
                        // set file name
                        // - save new file temporarily in database to get ID for use as run number
                        $carrierId = $this->serviceValues->getCarrierId();
                        $agentid = trim($agentServiceArray['agent'][$key]);

                        $run_number = CarrierDataFileLog::generateRunNumber($carrierId, $agentid);
                        $this->yodel_file = "UKD" . $this->constants[$serviceid]['YODEL_BOOKING_FILE_NUMBER'] . "." . substr("00$run_number", - 3);

                        $carrierDataFileLog = new CarrierDataFileLog();
                        $carrierDataFileLog->setCarrierId($carrierId);
                        $carrierDataFileLog->setAgentId($agentid);
                        $carrierDataFileLog->setFileName($this->yodel_file);
                        $carrierDataFileLog->setRunNumber($run_number);
                        $carrierDataFileLog->save();

                         // , 
                                            //date_booked = CASE
                                            //    WHEN date_booked <= 0 THEN " . time() . "
                                            //    WHEN date_booked IS NULL THEN " . time() . "
                                            //END  
                        if ($this->sendBookings($this->constants[$serviceid], $run_number)) {
                            if (!empty($consignmentIdArray)) {
                                 $sql = "UPDATE consignment SET send_courier_data = 1, booked_file_id = '" . $this->yodel_file . "'
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
                            $output["MESSAGE"] = "Please check constants, System not able to find FTP details to send data to yodel. Please fix it ASAP";
                        }
                    } else {
                        $output["STATUS"] = "ERROR";
                        $output["MESSAGE"] = "Please check consignment filter is not working properly.";
                    }
                } else {
                    $output["STATUS"] = "ERROR";
                    $output["MESSAGE"] = "Please check constants, System not able to find service details to send data to yodel. Please fix it ASAP";
                }
            }
        }
        return $output;
    }

    public function manifest($consignment) {
        
    }

    public function preAdvice($consignment) {
        
    }

    public function recycledShipment($consignment) {
        $output["STATUS"] = "SUCCESS";
        return $output;
    }

    private function getProductLine($handling_code) {
        $productLine = array();
        switch ($handling_code) {

            case "STYDL3HPA":
                $productLine['product_line_1'] = "YODEL @MINI";
                $productLine['product_line_2'] = "@MINI";
                break;

            case "STYDL01CN":
            case "STYDL1VSP":
                $productLine['product_line_1'] = "MON TO SAT";
                $productLine['product_line_2'] = "24";
                break;

            case "STYDL2CXN":
            case "STYDL02CP":
                $productLine['product_line_1'] = "MON TO SAT";
                $productLine['product_line_2'] = "48";
                break;

            case "1CSP":
                $productLine['product_line_1'] = "YODEL Channel Islands 72 POD";
                $productLine['product_line_2'] = "1CSP";
                break;

            case "1CSN":
                $productLine['product_line_1'] = "YODEL Channel Islands 72 Non POD";
                $productLine['product_line_2'] = "1CSN";
                break;

            case "1CEN":
            case "STYDL1CEN":
                $productLine['product_line_1'] = "YODEL Channel Islands 48 Non POD";
                $productLine['product_line_2'] = "1CEN";
                break;
            
            case "1CEP":
            case "STYDL1CEP":
                $productLine['product_line_1'] = "YODEL Channel Islands 48 POD";
                $productLine['product_line_2'] = "1CEP";
                break;
            
            case "STYDL001H":
            case "STYDL01HS":
                $productLine['product_line_1'] = "YODEL @HOME";
                $productLine['product_line_2'] = "YODEL @HOME 24";
                break;

            case "STYDL003H":
            case "STYDL03HS":
                $productLine['product_line_1'] = "YODEL HOME 72";
                $productLine['product_line_2'] = "YODEL @HOME 72";
                break;

            case "ISLE":
            case "STYDLISLE":
                $productLine['product_line_1'] = "YODEL ISLE";
                $productLine['product_line_2'] = "ISLE";
                break;

            case "1E":
                $productLine['product_line_1'] = "VAN MON TO FRI";
                $productLine['product_line_2'] = "24 POD";
                break;
            
            case "STYDL2VLP":
            case "STYDL02VP":
                $productLine['product_line_1'] = "Yodel XPECT 48";
                $productLine['product_line_2'] = "Yodel XPECT 48";
                break;
            
            case "STYDL1EEN":
                $productLine['product_line_1'] = "EXPRESS ISLE EXCHANGE";
                $productLine['product_line_2'] = "EXPRESS ISLE EXCHANGE";
                break;
            
            case "STYDL1EEP":
                $productLine['product_line_1'] = "EXPRESS 24 EXCHANGE";
                $productLine['product_line_2'] = "EXPRESS 24 EXCHANGE";
                break;

            case "STYDL12CN":
                $productLine['product_line_1'] = "XPRESS 48 NON POD";
                $productLine['product_line_2'] = "XPRESS 48 NON POD";
                break;

            case "STYDLN1CN":
                $productLine['product_line_1'] = "XPRESS 24 NON POD";
                $productLine['product_line_2'] = "XPRESS 24 NON POD";
                break;
            
            case "STYDL01VN":
                $productLine['product_line_1'] = "XPECT 24 NON POD";
                $productLine['product_line_2'] = "XPECT 24 NON POD";
                break;
            
            case "STYDL1VSN":
                $productLine['product_line_1'] = "XPECT 24 (MON-SAT) NON POD";
                $productLine['product_line_2'] = "XPECT 24 (MON-SAT) NON POD";
                break;
            
            case "STYDL02VN":
                $productLine['product_line_1'] = "XPECT 48 NON POD";
                $productLine['product_line_2'] = "XPECT 48 NON POD";
                break;
            
            case "STYDL2VPR":
                $productLine['product_line_1'] = "XPECT 48 RETURN POD";
                $productLine['product_line_2'] = "XPECT 48 RETURN POD";
                break;
            
            case "STYDL2VLN":
                $productLine['product_line_1'] = "XPECT 48 XL NON POD";
                $productLine['product_line_2'] = "XPECT 48 XL NON POD";
                break;
                
            default:
                $productLine['product_line_1'] = "YODEL EXPRESS";
                $productLine['product_line_2'] = "YODEL EXPRESS 24";
                break;
        }
        return $productLine;
    }

    private function labelpdf100X200($consignment, $parcel_idx, $licence_plate, $parcel) {
        $x = 5;
        $this->pdf->setFont("helvetica", "", 8);
        $this->pdf->Text($x, 5, "v2.3");

        $deliveryNetworkDay = new DeliveryNetworkDay($consignment);
        // $ConsignmentValidator = new ConsignmentValidator($consignment);

        $handling_code = $this->ValidateYodelPostcode($consignment->getPostcode(), $this->serviceValues->getCode());
        $productLine = $this->getProductLine($this->serviceValues->getCode());

        $product_line_1 = $productLine['product_line_1'];
        $product_line_2 = $productLine['product_line_2'];

        $this->pdf->setFont("helvetica", "B", 12);
        $this->pdf->Text($x + 15, 5, $product_line_1);
        $this->pdf->setFont("helvetica", "", 10);
        $this->pdf->Text($x + 15, 10, $product_line_2);
        $this->pdf->SetFont('helvetica', 'B', 22);

        $this->pdf->Circle(90, 20.5, 5);
        if ($consignment->getValue() < 24.426)
            $this->pdf->Text(87, 15, "L");
        else if ($consignment->getValue() >= 24.426 && $consignment->getValue() < 219.834)
            $this->pdf->Text(87, 15, "M");
        else if ($consignment->getValue() >= 219.834)
            $this->pdf->Text(87, 15, "H");
        $this->pdf->setFont("helvetica", "", 8);
        $this->pdf->Text($x, 15, "Yodel standard terms and conditions apply.");
        $this->pdf->Text($x, 22, "From:");
        $this->pdf->Text($x + 10, 22, $this->constants['YODEL_SHIPPER_CONTACT']);
        $this->pdf->Text($x + 10, 25, $this->constants['YODEL_SHIPPER_COMPANY_NAME']);
        $this->pdf->Text($x + 10, 28, $this->constants['YODEL_SHIPPER_ADDRESS_LINE_1']);
        $this->pdf->Text($x + 10, 31, $this->constants['YODEL_SHIPPER_ADDRESS_LINE_2']);
        $this->pdf->Text($x + 10, 34, $this->constants['YODEL_SHIPPER_ADDRESS_LINE_3']);
        $this->pdf->Text($x + 10, 37, $this->constants['YODEL_SHIPPER_CITY']);
        $this->pdf->Text($x + 10, 40, $this->constants['YODEL_SHIPPER_POSTCODE']);

        $shipper_country = $this->constants['YODEL_SHIPPER_COUNTRY'];
        if ((int) $shipper_country > 0) {
            $shipperCountry = new Country($shipper_country);
            $sCountry = $shipperCountry->getName();
        } else {
            $sCountry = $shipper_country;
        }
        $this->pdf->Text($x + 30, 40, $sCountry);
        //$meter_num = "10160"; //57356!!! //57357
        $target = "Meter: " . $this->constants['YODEL_BOOKING_FILE_NUMBER'];
        $this->pdf->Text($x + 50, 22, $target);
        $account = $consignment->getAccount();
        $userData = new UserFilter();
        $userData->addUserAccountFilter($account);
        $userdataList = $userData->getColumnList('logo, parentid');
        $image1 = "";

        if (count($userdataList) > 0) {
            if (trim($userdataList[0]->getLogo()) != '')
                $image1 = realpath("../images/userlogo/" . $userdataList[0]->getLogo());
            else
                $image1 = realpath("../images/logo.jpg");
        } else
            $image1 = realpath("../images/logo.jpg");
        if (file_exists($image1)) {
            $fitbox = 'C';
            $fitbox[1] = 'M';
            $this->pdf->image($image1, $x + 50, 28, 28, 15, '', '', '', false, 700, '', false, false, 0, $fitbox, false, false);
        }
        $this->pdf->setFont("helvetica", "", 8);

        $this->pdf->SetLineWidth(1);
        //top left corner border
        $this->pdf->Line(2, 45, 10, 45);
        $this->pdf->Line(2, 45, 2, 52);
        //top bottom corner border
        $this->pdf->Line(2, 70, 10, 70);
        $this->pdf->Line(2, 63, 2, 70);
        //top right corner border
        $this->pdf->Line(88, 45, 98, 45);
        $this->pdf->Line(98, 45, 98, 52);
        //top right corner border
        $this->pdf->Line(88, 70, 98, 70);
        $this->pdf->Line(98, 63, 98, 70);
        $this->pdf->setFont("helvetica", "", 10);
        $this->pdf->Text($x, 55, "To:");
        $this->pdf->Text($x + 10, 45, $consignment->getCompany());
        $this->pdf->Text($x + 10, 49, $consignment->getContact());
        $this->pdf->setFont("helvetica", "B", 11);
        $this->pdf->Text($x + 10, 53, $consignment->getAddressLine1());
        $this->pdf->Text($x + 10, 57, $consignment->getAddressLine2());
        $this->pdf->Text($x + 10, 61, $consignment->getAddressLine3());
        $this->pdf->setFont("helvetica", "", 10);
        $this->pdf->Text($x + 60, 45, $consignment->getTelephone());
        $this->pdf->Text($x + 10, 65, $consignment->getCity());
        $this->pdf->setFont("helvetica", "B", 10);
        $postcode = substr(trim($consignment->getPostcode()), 0,2);
        if($this->countryName->getIso() == "IE" && strtoupper($postcode) != 'BT'){
            $this->pdf->Text($x + 70, 65, $consignment->getState());
        }
        else
        {
            $this->pdf->Text($x + 70, 65, $consignment->getPostcode());
        }
        

        $this->pdf->setFont("helvetica", "", 9);
         
        if ($this->serviceValues->getCode() == "STYDL01HS" || $this->serviceValues->getCode() == "STYDL03HS")
            $this->pdf->Text(60, 73, "SIGNATURE REQUIRED");
        //  echo $handling_code;
        if ($handling_code == "STYDL001H" || $handling_code == "STYDL03H") {
            $this->pdf->SetTextColor(255, 255, 255);
            $this->pdf->setFont("helvetica", "B", 17);
            //          $this->pdf->image("../images/1h.png", 79, 99, 10);
            $this->pdf->setXY(75, 79);
            $this->pdf->cell(10, 10, $handling_code, 1, 1, 'C', 1);
        } else {
            $this->pdf->SetTextColor(255, 255, 255);
            $this->pdf->setFont("helvetica", "B", 17);
            $this->pdf->setXY(75, 79);
            $this->pdf->cell(20, 10, $handling_code, 1, 1, 'C', 1);
        }

        $this->pdf->SetTextColor(0, 0, 0);
        $this->pdf->setFont("helvetica", "", 7);

        $this->pdf->Text(5, 80, "Customer No:");
        $this->pdf->Text(25, 80, $this->constants['YODEL_SHIPPER_ACCOUNT_NUMBER']);
        $this->pdf->Text(5, 83, "Sender Ref:");
        $this->pdf->Text(25, 83, $consignment->getHawb());
        
        $this->pdf->Text(65, 90, "Date:");
        $this->pdf->Text(75, 90, date("m.d.y"));
        $this->pdf->Text(65, 93, "Ref:");
        $this->pdf->Text(75, 93, $consignment->getReference());
        $this->pdf->write1DBarcode($consignment->getHawb(), 'C128', 5, 72, 30, 7, 0.5);
        $this->pdf->setFont("helvetica", "B", 9);
        $text = ($parcel_idx + 1) . " / " . $consignment->getNumberPieces();
        $this->pdf->Text(85, 78, $text);
        $serviceCode = $this->getHandling($this->serviceValues->getCode());
        $deliveryNetworkDay->setServiceHandlingCode($serviceCode);

        if ($deliveryNetworkDay->getServiceHub() != "") {
            $this->pdf->setFont("helvetica", "B", 12);
            $servicestation = $deliveryNetworkDay->getServiceStation();
            $this->pdf->Text(5, 93, $servicestation);

            $iparr = explode("_", $servicestation);
            $consignment->setOtherRoutingCode($iparr[0]);
            $consignment->save();
            $this->routing_code = $iparr[0];
            $this->pdf->setFont("helvetica", "B", 25);
            $this->pdf->Text(75, 9, $iparr[0]);

            $this->pdf->setFont("helvetica", "B", 12);
            $serviceHub = $deliveryNetworkDay->getServiceHub();
            $this->pdf->Text(65, 96, $serviceHub);
        }
        
        $routineBarcode = $deliveryNetworkDay->getRoutingBarcode();
        $routineBarcode = str_replace(" ", "", $routineBarcode);
        $this->pdf->setFont("helvetica", "", 10);
        $this->pdf->write1DBarcode($routineBarcode, 'C128', 10, 101, 80, 18, 0.5);
        $this->pdf->Text(30, 119, $routineBarcode);
        $awb = "J" . $licence_plate;
        $this->pdf->write1DBarcode($awb, 'C128', 10, 125, 80, 18, 0.5);
        $firstfour = substr($licence_plate, 0, 4);
        $secondthree = substr($licence_plate, 4, 3);
        $thirdthree = substr($licence_plate, 7, 3);
        $fourthfour = substr($licence_plate, 10, 4);
        $lastfour = substr($licence_plate, -4);
        $tracking_number = "(J)" . $firstfour . " " . $secondthree . " " . $thirdthree . " " . $fourthfour . " " . $lastfour;
        $this->pdf->Text(30, 143, $tracking_number);
        
        $fontname = $this->pdf->addTTFfont('../assets/fonts/simhei.ttf', 'TrueTypeUnicode', '', 32);
        $this->pdf->SetFont($fontname, '', 8);
        $ref = $consignment->getNotes();
        //$this->pdf->Text(5, 160, $consignment->getDescription());
        $parcelSKU = json_decode($parcel->getItemSku());
        $parcelSkuString = implode(", ", $parcelSKU);
        $description = $consignment->getDescription() . " " . $parcelSkuString;
        if (trim($description)!= '') {
            if (strlen($description) > 50) {
                $wrapref = chunk_split($description, 50) . "\n";
                $reference = explode("\n", $wrapref);
                $y = 150;
                foreach ($reference as $refe) {
                    $this->pdf->Text(5, $y, $refe);
                    $y += 3;
                }
            } else {
                $this->pdf->Text(5, 150, $description);
            }
        }
        
        
        $item_type = $consignment->getItemType();
        //if (strtolower($item_type) == "normal" || trim($item_type) == "") {
            if (strlen($ref) > 80) {
                $wrapref = chunk_split($ref, 80) . "\n";
                $reference = explode("\n", $wrapref);
         //       $y = 188;
                foreach ($reference as $refe) {
                    $this->pdf->Text(5, $y, $refe);
                    $y += 3;
                }
            } else {
                $this->pdf->Text(5, 188, $ref);
            }
        //} else
        {
            if (strtolower($item_type) == "lithium battery") {
                $image3 = realpath("../images/caution2.jpg");
                if (file_exists($image3)) {
                    $this->pdf->image($image3, 5, 160, 90, 35);
                }
            } else if (strtolower($item_type) == "perfume") {
                $image4 = realpath("../images/perfume-label.jpg");
                if (file_exists($image4)) {
                    $this->pdf->image($image4, 5, 160, 22, 35);
                }
            } else if (strtolower($item_type) == "fire extinguisher") {
                $image4 = realpath("../images/ADR.jpg");
                if (file_exists($image4)) {
                    $this->pdf->image($image4, 5, 160, 90, 35);
                }
            }
        }
    }

    private function labelpdf100X150($consignment, $parcel_idx, $licence_plate, $parcel) {
      
        $x = 5;
        $this->pdf->setFont("helvetica", "", 8);
        $this->pdf->Text($x, 5, "v2.3");

        $deliveryNetworkDay = new DeliveryNetworkDay($consignment);
        // $ConsignmentValidator = new ConsignmentValidator($consignment);

        $handling_code = $this->ValidateYodelPostcode($consignment->getPostcode(), $this->serviceValues->getCode());
        $productLine = $this->getProductLine($this->serviceValues->getCode());

        $product_line_1 = $productLine['product_line_1'];
        $product_line_2 = $productLine['product_line_2'];

        $this->pdf->setFont("helvetica", "B", 12);
        $this->pdf->Text($x + 15, 5, $product_line_1);
        $this->pdf->setFont("helvetica", "", 10);
        $this->pdf->Text($x + 15, 10, $product_line_2);
        $this->pdf->SetFont('helvetica', 'B', 22);

        $this->pdf->Circle(90, 20.5, 5);
        if ($consignment->getValue() < 24.426)
            $this->pdf->Text(87, 15, "L");
        else if ($consignment->getValue() >= 24.426 && $consignment->getValue() < 219.834)
            $this->pdf->Text(87, 15, "M");
        else if ($consignment->getValue() >= 219.834)
            $this->pdf->Text(87, 15, "H");
        $this->pdf->setFont("helvetica", "", 8);
        $this->pdf->Text($x, 15, "Yodel standard terms and conditions apply.");
        $this->pdf->Text($x, 22, "From:");
        $this->pdf->Text($x + 10, 22, $this->constants['YODEL_SHIPPER_CONTACT']);
        $this->pdf->Text($x + 10, 25, $this->constants['YODEL_SHIPPER_COMPANY_NAME']);
        $this->pdf->Text($x + 10, 28, $this->constants['YODEL_SHIPPER_ADDRESS_LINE_1']);
        $this->pdf->Text($x + 10, 31, $this->constants['YODEL_SHIPPER_ADDRESS_LINE_2']);
        $this->pdf->Text($x + 10, 34, $this->constants['YODEL_SHIPPER_ADDRESS_LINE_3']);
        $this->pdf->Text($x + 10, 37, $this->constants['YODEL_SHIPPER_CITY']);
        $this->pdf->Text($x + 10, 40, $this->constants['YODEL_SHIPPER_POSTCODE']);

        $shipper_country = $this->constants['YODEL_SHIPPER_COUNTRY'];
        if ((int) $shipper_country > 0) {
            $shipperCountry = new Country($shipper_country);
            $sCountry = $shipperCountry->getName();
        } else {
            $sCountry = $shipper_country;
        }
        $this->pdf->Text($x + 30, 40, $sCountry);
        //$meter_num = "10160"; //57356!!! //57357
        $target = "Meter: " . $this->constants['YODEL_BOOKING_FILE_NUMBER'];
        $this->pdf->Text($x + 50, 22, $target);
        $account = $consignment->getAccount();
        $userData = new UserFilter();
        $userData->addUserAccountFilter($account);
        $userdataList = $userData->getColumnList('logo, parentid');
        $image1 = "";

        if (count($userdataList) > 0) {
            if (trim($userdataList[0]->getLogo()) != '')
                $image1 = realpath("../images/userlogo/" . $userdataList[0]->getLogo());
            else
                $image1 = realpath("../images/logo.jpg");
        } else
            $image1 = realpath("../images/logo.jpg");
        if (file_exists($image1)) {
            $fitbox = 'C';
            $fitbox[1] = 'M';
            $this->pdf->image($image1, $x + 50, 28, 28, 15, '', '', '', false, 700, '', false, false, 0, $fitbox, false, false);
        }
        $this->pdf->setFont("helvetica", "", 8);

        $this->pdf->SetLineWidth(1);
        //top left corner border
        $this->pdf->Line(2, 45, 10, 45);
        $this->pdf->Line(2, 45, 2, 52);
        //top bottom corner border
        $this->pdf->Line(2, 70, 10, 70);
        $this->pdf->Line(2, 63, 2, 70);
        //top right corner border
        $this->pdf->Line(88, 45, 98, 45);
        $this->pdf->Line(98, 45, 98, 52);
        //top right corner border
        $this->pdf->Line(88, 70, 98, 70);
        $this->pdf->Line(98, 63, 98, 70);
        $this->pdf->setFont("helvetica", "", 10);
        $this->pdf->Text($x, 55, "To:");
        $this->pdf->Text($x + 10, 45, $consignment->getCompany());
        $this->pdf->Text($x + 10, 49, $consignment->getContact());
        $this->pdf->setFont("helvetica", "B", 11);
        $this->pdf->Text($x + 10, 53, $consignment->getAddressLine1());
        $this->pdf->Text($x + 10, 57, $consignment->getAddressLine2());
        $this->pdf->Text($x + 10, 61, $consignment->getAddressLine3());
        $this->pdf->setFont("helvetica", "", 10);
        $this->pdf->Text($x + 60, 45, $consignment->getTelephone());
        $this->pdf->Text($x + 10, 65, $consignment->getCity());
        $this->pdf->setFont("helvetica", "B", 10);
        $postcode = substr(trim($consignment->getPostcode()), 0,2);
        if($this->countryName->getIso() == "IE" && strtoupper($postcode) != 'BT'){
            $this->pdf->Text($x + 70, 65, $consignment->getState());
        }
        else
        {
            $this->pdf->Text($x + 70, 65, $consignment->getPostcode());
        }
        

        $this->pdf->setFont("helvetica", "", 9);
         
        if ($this->serviceValues->getCode() == "STYDL01HS" || $this->serviceValues->getCode() == "STYDL03HS")
            $this->pdf->Text(60, 73, "SIGNATURE REQUIRED");
        //  echo $handling_code;
        if ($handling_code == "STYDL001H" || $handling_code == "STYDL03H") {
            $this->pdf->SetTextColor(255, 255, 255);
            $this->pdf->setFont("helvetica", "B", 17);
            //          $this->pdf->image("../images/1h.png", 79, 99, 10);
            $this->pdf->setXY(75, 79);
            $this->pdf->cell(10, 10, $handling_code, 1, 1, 'C', 1);
        } else {
            $this->pdf->SetTextColor(255, 255, 255);
            $this->pdf->setFont("helvetica", "B", 17);
            $this->pdf->setXY(75, 79);
            $this->pdf->cell(20, 10, $handling_code, 1, 1, 'C', 1);
        }

        $this->pdf->SetTextColor(0, 0, 0);
        $this->pdf->setFont("helvetica", "", 7);

        $this->pdf->Text(5, 80, "Customer No:");
        $this->pdf->Text(25, 80, $this->constants['YODEL_SHIPPER_ACCOUNT_NUMBER']);
        $this->pdf->Text(5, 83, "Sender Ref:");
        $this->pdf->Text(25, 83, $consignment->getHawb());
        $parcelSKU = json_decode($parcel->getItemSku());
        $parcelSkuString = implode(", ", $parcelSKU);
        $description = $consignment->getDescription() . " " . $parcelSkuString;
        $this->pdf->Text(5, 88, $description);
        $this->pdf->Text(65, 90, "Date:");
        $this->pdf->Text(75, 90, date("m.d.y"));
        $this->pdf->Text(65, 93, "Ref:");
        $this->pdf->Text(75, 93, $consignment->getReference());
        $this->pdf->write1DBarcode($consignment->getHawb(), 'C128', 5, 72, 30, 7, 0.5);
        $this->pdf->setFont("helvetica", "B", 9);
        $text = ($parcel_idx + 1) . " / " . $consignment->getNumberPieces();
        $this->pdf->Text(85, 78, $text);
        $serviceCode = $this->getHandling($this->serviceValues->getCode());
        $deliveryNetworkDay->setServiceHandlingCode($serviceCode);

        if ($deliveryNetworkDay->getServiceHub() != "") {
            $this->pdf->setFont("helvetica", "B", 12);
            $servicestation = $deliveryNetworkDay->getServiceStation();
            $this->pdf->Text(5, 93, $servicestation);

            $iparr = explode("_", $servicestation);
            $consignment->setOtherRoutingCode($iparr[0]);
            $consignment->save();
            $this->routing_code = $iparr[0];
            $this->pdf->setFont("helvetica", "B", 25);
            $this->pdf->Text(75, 9, $iparr[0]);

            $this->pdf->setFont("helvetica", "B", 12);
            $serviceHub = $deliveryNetworkDay->getServiceHub();
            $this->pdf->Text(65, 96, $serviceHub);
        }
        
        $routineBarcode = $deliveryNetworkDay->getRoutingBarcode();
        $routineBarcode = str_replace(" ", "", $routineBarcode);
        $this->pdf->setFont("helvetica", "", 10);
        $this->pdf->write1DBarcode($routineBarcode, 'C128', 10, 101, 80, 18, 0.5);
        $this->pdf->Text(30, 119, $routineBarcode);
        $awb = "J" . $licence_plate;
        $this->pdf->write1DBarcode($awb, 'C128', 10, 125, 80, 18, 0.5);
        $firstfour = substr($licence_plate, 0, 4);
        $secondthree = substr($licence_plate, 4, 3);
        $thirdthree = substr($licence_plate, 7, 3);
        $fourthfour = substr($licence_plate, 10, 4);
        $lastfour = substr($licence_plate, -4);
        $tracking_number = "(J)" . $firstfour . " " . $secondthree . " " . $thirdthree . " " . $fourthfour . " " . $lastfour;
        $this->pdf->Text(30, 143, $tracking_number);
    }

    private function labelzpl100X150($consignment, $parcel_idx, $licence_plate) {
       
        $deliveryNetworkDay = new DeliveryNetworkDay($consignment);
        $serviceCode = $this->getHandling($this->serviceValues->getCode());
        $deliveryNetworkDay->setServiceHandlingCode($serviceCode);
        $ConsignmentValidator = new ConsignmentValidator($consignment);
        $handling_code = $this->ValidateYodelPostcode($consignment->getPostcode(), $this->serviceValues->getCode());
        $productLine = $this->getProductLine($this->serviceValues->getCode());

        $product_line_1 = $productLine['product_line_1'];
        $product_line_2 = $productLine['product_line_2'];

        $zplFileText = "";
        $zplFileText = "^XA" . "\r";
        $zplFileText .= "^CF0,30" . "\r";
        $zplFileText .= "^FO30,30^FDv2.3^FS" . "\r";
        $zplFileText .= "^CF0,50" . "\r";
        $zplFileText .= "^FO130,35^FD" . str_replace("NON POD", "", $product_line_1) . "^FS" . "\r";
        $zplFileText .= "^CF0,30" . "\r";
        $zplFileText .= "^FO30,100^FDYodel standard terms and conditions apply.^FS" . "\r";


        //Create Circle and add value
        $zplFileText .= "^FO655,140 ^GC90,3,B^FS" . "\r";
        $zplFileText .= "^CFA,60" . "\r";
        if ($consignment->getValue() < 24.426)
            $zplFileText .= "^FO680,150^FDL^FS" . "\r";
        else if ($consignment->getValue() >= 24.426 && $consignment->getValue() < 219.834)
            $zplFileText .= "^FO680,150^FDM^FS" . "\r";
        else if ($consignment->getValue() >= 219.834)
            $zplFileText .= "^FO680,150^FDH^FS" . "\r";

        //Start From Addres
        $zplFileText .= "^CF0,30" . "\r";
        $zplFileText .= "^FO30,160^FDFrom: ^FS" . "\r";
        $zplFileText .= "^CF0,30" . "\r";
        $zplFileText .= "^FO100,160^FD" . $this->constants["YODEL_SHIPPER_COMPANY_NAME"] . "^FS" . "\r";
        $zplFileText .= "^FO100,185^FD" . $this->constants["YODEL_SHIPPER_CONTACT"] . " ^FS" . "\r";
        $zplFileText .= "^FO100,210^FD" . $this->constants["YODEL_SHIPPER_ADDRESS_LINE_1"] . "^FS" . "\r";
        $zplFileText .= "^FO100,235^FD" . $this->constants['YODEL_SHIPPER_ADDRESS_LINE_2'] . " ^FS";
        $zplFileText .= "^FO100,260^FD" . $this->constants['YODEL_SHIPPER_ADDRESS_LINE_3'] . "^FS" . "\r";
        $zplFileText .= "^FO100,285^FD" . $this->constants['YODEL_SHIPPER_CITY'] . "^FS" . "\r";
        $shipper_country = $this->constants['YODEL_SHIPPER_COUNTRY'];
        if ((int) $shipper_country > 0) {
            $shipperCountry = new Country($shipper_country);
            $sCountry = $shipperCountry->getName();
        } else {
            $sCountry = $shipper_country;
        }
        $zplFileText .= "^FO100,310^FD" . $shipper_country . "^FS" . "\r";
        $zplFileText .= "^FO400,310^FD" . $this->constants['YODEL_SHIPPER_POSTCODE'] . "^FS" . "\r";

        //Drawing Lines for Receiver Address
        $zplFileText .= "^FO10,360^GB1,70,8^FS" . "\r";
        $zplFileText .= "^FO10,360^GB70,1,8,^FS" . "\r";
        $zplFileText .= "^FO780,360^GB1,70,8^FS" . "\r";
        $zplFileText .= "^FO710,360^GB70,1,8,^FS" . "\r";
        $zplFileText .= "^FO10,500^GB1,70,8^FS" . "\r";
        $zplFileText .= "^FO10,570^GB70,1,8,^FS" . "\r";
        $zplFileText .= "^FO780,500^GB1,70,8^FS" . "\r";
        $zplFileText .= "^FO718,570^GB70,1,8,^FS" . "\r";

        //Meter No
        $zplFileText .= "^FO400,160^FDMeter: " . $this->constants['YODEL_METER_NUMBER'] . "^FS" . "\r";
        // Receiver Address
        $zplFileText .= "^CF0,35" . "\r";
        $zplFileText .= "^FO30,470^FDTo:^FS" . "\r";
        $zplFileText .= "^FO100,380^FD" . $consignment->getCompany() . "^FS" . "\r";
        $zplFileText .= "^CF0,30" . "\r";
        $zplFileText .= "^FO550,380^FD" . $consignment->getTelephone() . "^FS" . "\r";
        $zplFileText .= "^CF0,35" . "\r";
        $zplFileText .= "^FO100,410^FD" . $consignment->getContact() . "^FS" . "\r";
        $zplFileText .= "^CF0,45" . "\r";
        $zplFileText .= "^FO100,440^FD" . $consignment->getAddressLine1() . "^FS" . "\r";
        $zplFileText .= "^CF0,35" . "\r";
        $zplFileText .= "^FO100,475^FD" . $consignment->getAddressLine2() . "^FS" . "\r";
        $zplFileText .= "^FO100,505^FD" . $consignment->getAddressLine3() . "^FS" . "\r";
        $zplFileText .= "^FO100,535^FD" . $consignment->getCity() . "^FS" . "\r";
        $zplFileText .= "^CF0,40" . "\r";
        $postcode = substr(trim($consignment->getPostcode()), 0,2);
        if($this->countryName->getIso() == "IE" && strtoupper($postcode) != 'BT'){
            $zplFileText .= "^FO580,535^FD" . $consignment->getState() . "^FS" . "\r";
        }
        else
        {
            $zplFileText .= "^FO580,535^FD" . $consignment->getPostcode() . "^FS" . "\r";
        }
        
        $zplFileText .= "^CF0,30" . "\r";

        //Shipment Details
        if ($handling_code == "STYDL01HS" || $handling_code == "STYDL03HS") {
            $zplFileText .= "^FO30,585^FDSIGNATURE REQUIRED^FS" . "\r";
        }
        $zplFileText .= "^FO30,610^FDShipment No:^FS" . "\r";
        $zplFileText .= "^FO30,640^FDCustomer No: ^FS" . "\r";
        $zplFileText .= "^FO200,640^FD" . $this->constants['YODEL_SHIPPER_ACCOUNT_NUMBER'] . "^FS" . "\r";
        $zplFileText .= "^FO30,670^FDSender Ref: ^FS" . "\r";
        $zplFileText .= "^FO200,670^FD" . $consignment->getHawb() . "^FS" . "\r";
        $zplFileText .= "^CF0,26" . "\r";
        $description = $consignment->getDescription();
        $lengthDesc = strlen($description);
//        if($lengthDesc < 74)
//       {
        $zplFileText .= "^FO30,710^FD" . substr($consignment->getDescription(), 0, 52) . "^FS" . "\r";
//        }
//        else
//        {
//            $firsthalf = substr($description,0, 74);
//            $secondhalf = substr($description, 74, 74);
//            $zplFileText .= "^FO30,710^FD" . $firsthalf . "^FS" . "\r";
//            $zplFileText .= "^FO30,730^FD" . $secondhalf . "^FS" . "\r";
//        }
        $zplFileText .= "^CF0,30" . "\r";
        $zplFileText .= "^FO580,710^FDDate:^FS" . "\r";
        $zplFileText .= "^FO580,740^FDRef:^FS" . "\r";
        $zplFileText .= "^FO630,710^FD" . date("d.m.Y") . "^FS" . "\r";

        //Service Code in Back Ground
        $zplFileText .= "^LRY" . "\r";
        $zplFileText .= "^FO580,620" . "\r";
        $zplFileText .= "^GB160,80,80^FS" . "\r";
        $zplFileText .= "^FO590,640^CF0,80" . "\r";
        $zplFileText .= "^FD" . $handling_code . "^FS" . "\r";


        if ($deliveryNetworkDay->getServiceHub() != "") {


            $servicestation = $deliveryNetworkDay->getServiceStation();
            $iparr = explode("_", $servicestation);
            $consignment->setOtherRoutingCode($iparr[0]);
            $consignment->save();
            $serviceHub = $deliveryNetworkDay->getServiceHub();

            //ServiceHub and depo details
            $zplFileText .= "^CFC,40" . "\r";
            $zplFileText .= "^FO30,750^FD" . $servicestation . "^FS" . "\r";
            $zplFileText .= "^CF0,40" . "\r";
            $zplFileText .= "^FO580,765^FD" . $serviceHub . "^FS" . "\r";
            //$zplFileText .= "^FO670,765^FD25E^FS" . "\r";

            $zplFileText .= "^CF0,80" . "\r";
            $zplFileText .= "^FO600,80^FD" . $iparr[0] . "^FS" . "\r";
        }
        $routineBarcode = $deliveryNetworkDay->getRoutingBarcode();
        $routineBarcode = str_replace(" ", "", $routineBarcode);
        $awb = "J" . $licence_plate;

        //Barcode
        $zplFileText .= "^FO40,800^BY3" . "\r";
        $zplFileText .= "^BCN,140,Y,N,N" . "\r";
        $zplFileText .= "^FD" . $routineBarcode . "^FS" . "\r";
        $zplFileText .= "^FO40,1000^BY3" . "\r";
        $zplFileText .= "^BCN,140,Y,N,N" . "\r";
        $zplFileText .= "^FD" . $awb . "^FS" . "\r";
        $zplFileText .= "^XZ" . "\r";



        if ($zplFileText == "")
            return false;


        $id_num = $consignment->getId();
        $file_name = date('Y_m_d') . '/' .  $id_num . ".zpl";
        $file_path = "../_assets/pdf/" . $file_name;
        $file_handle = fopen($file_path, 'w+');
        if ($file_handle == null) {
            return false;
        }


        // write label text into file
        fwrite($file_handle, $zplFileText);
        fclose($file_handle);
        return $file_name;
    }

    public function getRoutingCode() {
        return $this->routing_code;
    }

    private function ValidateYodelPostcode($postcode, $handlingcode) {

        $handling = $this->getHandling($handlingcode);
        $valueToPass = $handling;
        $two_level_postcode = substr(trim($postcode), 0, 2);
        $three_level_postcode = substr(trim($postcode), 0, 3);
        $four_level_postcode = substr(trim($postcode), 0, 4);

        $four_level_array = $this->fourLevelPostcode();
        $three_level_array = $this->threeLevelPostcode();


        if ($two_level_postcode == "BT" && ($valueToPass == "1H" || $valueToPass == "1CN"))
            $valueToPass = "3HN";
        if ($two_level_postcode == "BT" && ($valueToPass == "3H" || $valueToPass == "2CXN" || $valueToPass == "2CP" || $valueToPass == "2VLP" ))
            $valueToPass = "3HN";

        else if (($two_level_postcode == "GY" || $two_level_postcode == "JE") && $handlingCode == "STYDL01HS")
            $valueToPass = "1CSP";
        else if (($two_level_postcode == "GY" || $two_level_postcode == "JE") && ($valueToPass == "1H" || $valueToPass == "1CN"))
            $valueToPass = "1CSN";
        else if (($two_level_postcode == "GY" || $two_level_postcode == "JE") && ($valueToPass == "2CXN"||  $valueToPass == "2CP" || $valueToPass == "2VLP"))
            $valueToPass = "1CEN";

        else if ($two_level_postcode == "ZE" || $two_level_postcode == "IM" || $two_level_postcode == "HS" || $four_level_postcode == "BFPO")
            $valueToPass = "ISLE";
        else if (in_array($four_level_postcode, $four_level_array))
            $valueToPass = "ISLE";
        else if (in_array($three_level_postcode, $three_level_array))
            $valueToPass = "ISLE";

        return $valueToPass;
    }

    private function threeLevelPostcode() {
        $three_level_array = array("IV1",
            "IV2",
            "IV3",
            "IV4",
            "IV5",
            "IV6",
            "IV7",
            "IV8",
            "IV9",
            "KW1",
            "KW2",
            "KW3",
            "KW5",
            "KW6",
            "KW7",
            "KW8",
            "KW9");
        return $three_level_array;
    }

    private function fourLevelPostcode() {
        $four_level_array = array("AB56",
            "PO40",
            "PO41",
            "IV10",
            "IV11",
            "IV12",
            "IV13",
            "IV14",
            "IV15",
            "IV16",
            "IV17",
            "IV18",
            "IV19",
            "IV20",
            "IV21",
            "IV22",
            "IV23",
            "IV24",
            "IV25",
            "IV26",
            "IV27",
            "IV28",
            "IV30",
            "IV31",
            "IV32",
            "IV37",
            "IV38",
            "IV39",
            "IV40",
            "IV41",
            "IV42",
            "IV43",
            "IV44",
            "IV45",
            "IV46",
            "IV47",
            "IV48",
            "IV49",
            "IV51",
            "IV52",
            "IV53",
            "IV54",
            "IV55",
            "IV56",
            "IV63",
            "KW10",
            "KW11",
            "KW12",
            "KW13",
            "KW14",
            "KW16",
            "KW17",
            "PA21",
            "PA22",
            "PA23",
            "PA24",
            "PA25",
            "PA26",
            "PA27",
            "PA28",
            "PA29",
            "PA30",
            "PA31",
            "PA32",
            "PA33",
            "PA34",
            "PA35",
            "PA36",
            "PA37",
            "PA38",
            "PA41",
            "PA42",
            "PA43",
            "PA44",
            "PA45",
            "PA46",
            "PA47",
            "PA60",
            "PA61",
            "PA62",
            "PA63",
            "PA64",
            "PA65",
            "PA66",
            "PA67",
            "PA68",
            "PA69",
            "PA71",
            "PA72",
            "PA73",
            "PA74",
            "PA75",
            "PA76",
            "PA77",
            "PA78",
            "PA80",
            "PH19",
            "PH20",
            "PH21",
            "PH22",
            "PH23",
            "PH24",
            "PH25",
            "PH26",
            "PH30",
            "PH31",
            "PH32",
            "PH33",
            "PH34",
            "PH35",
            "PH36",
            "PH37",
            "PH38",
            "PH39",
            "PH40",
            "PH41",
            "PH42",
            "PH43",
            "PH44",
            "PH49",
            "TR21",
            "TR22",
            "TR23",
            "TR24",
            "TR25",
            "KA27",
            "KA28",
            "PO30");
        return $four_level_array;
    }

    private function sendBookings($ftpConstants, $run_number) {
        if (sizeof($this->record_array) > 0) {
            //$linkFile = $this->getLinkFile($ftpConstants);

            $path = SETTING_DIR_ASSETS . "data_send/yodel_booking/" . date("Y_m_d") . "/";
            if (!file_exists($path))
                @mkdir($path, 0777, true);

            $file_path = $path . $this->yodel_file;//$linkFile->getFileName();
            chmod($path, 0777);
            // record count including header & footer
            $record_count = sizeof($this->record_array) + 2 + $this->customValueCount;

            // create file
            $file_handle = @fopen($file_path, 'w');
            fwrite($file_handle, $this->getHeaderRecord($record_count, $run_number, $ftpConstants));

            foreach ($this->record_array as $record) {
                fwrite($file_handle, "$record");
            }
            fwrite($file_handle,  $this->getFooterRecord($record_count));
            // close file
            fclose($file_handle);
            $local_file = realpath($file_path);


            $message = '';
            if (isset($ftpConstants['YODEL_FTP_SITE']) && trim($ftpConstants['YODEL_FTP_SITE']) != '') {
                $SETTING_FTP_USER_YODEL = $ftpConstants['YODEL_FTP_USER'];
                $SETTING_FTP_PASSWORD_YODEL = $ftpConstants['YODEL_FTP_PASSWORD'];
                $SETTING_FTP_SITE_YODEL = $ftpConstants['YODEL_FTP_SITE'];


                // FTP file to Yodel
                // - default port#

                /* $ftp_object = new FTPfile($SETTING_FTP_USER_YODEL, $SETTING_FTP_PASSWORD_YODEL, $SETTING_FTP_SITE_YODEL);
                  $ftp_object->passive();


                  $isYodelUpload = $ftp_object->put($remote_file_path, $local_file, FTP_BINARY); */
                $this->link_file = NULL;
                $this->record_array = NULL;

                $filename = $this->yodel_file; //$linkFile->getFileName();
                //"./stdcol/" .
                $remote_file_path = "./stdcol/" . $this->yodel_file; //$linkFile->getFileName();
                $ftp_conn = ftp_connect($SETTING_FTP_SITE_YODEL);
                if($ftp_conn){
                    $login = ftp_login($ftp_conn, $SETTING_FTP_USER_YODEL, $SETTING_FTP_PASSWORD_YODEL);
                    ftp_pasv($ftp_conn, true) or die("Unable switch to passive mode");
                    // upload file
                    $isYodelUpload = ftp_put($ftp_conn, $remote_file_path, $local_file, FTP_BINARY);
                    if ($isYodelUpload) {
                        echo "Successfully uploaded $remote_file_path.";
                    } else {
                        $message = 'There is an error while uploading the file to Yodel FTP. File name is ' . $remote_file_path;

    //                    echo "Error uploading $remote_file_path.";
                    }
                    // close connection
                    ftp_close($ftp_conn);
                    $returnparam = $isYodelUpload;
                }
                else
                {
                    $message = "Unabel to connect to Yodel FTP  " . $SETTING_FTP_SITE_YODEL;
                     $returnparam = false;
                }
            } else {
                $message = 'There is an error while uploading the file to Yodel FTP. Yodel constants not found in the system. file name is ' . $remote_file_path;

                $returnparam = false;
            }

            if (trim($message) != '') {
                echo $message;
                $to = 'itsupport@oneworldexpress.com';
                $subject = 'SmartTrack yodel data send to carrier issue';

                // To send HTML mail, the Content-type header must be set
                $headers[] = 'MIME-Version: 1.0';
                $headers[] = 'Content-type: text/html; charset=iso-8859-1';

                // Additional headers
                $headers[] = 'To: ITSUPPORT <noreply@@oneworldexpress.com>';
                $headers[] = 'From: NoReply <noreply@oneworldexpress.com>';
                $headers[] = 'X-Mailer: PHP/' . phpversion();
                mail($to, $subject, $message, implode("\r\n", $headers));
            }
            return $returnparam;
        }
    }

    private function getConsignmentRecord(Consignment $consignment, $noteNumber, $manifest_type, $handling, $parcel = array()) {

        $record = "";
        $record .= $this->fld(2, "CO");
        $serviceId = $consignment->getServiceId();

        @$accountNumber = $this->constants[$serviceId]['YODEL_SHIPPER_ACCOUNT_NUMBER'];
        @$contractNumber = $this->constants[$serviceId]['YODEL_CONTRACT_NUMBER'];
        @$scheduleNumber = $this->constants[$serviceId]['YODEL_CONTRACT_SCHEDULE_NUMBER'];

        $record .= $this->fld(9, $accountNumber);
        $record .= $this->fld(7, $contractNumber, true); // which constant is this one
        $record .= $this->fld(4, $scheduleNumber, true); //// which constant is this one

        $record .= $this->fld(10, "", true);
        $record .= $this->fld(3, $noteNumber, true); // 6
        $record .= $this->fld(10, date("d/m/Y", time())); // collection set to today!
        $record .= $this->fld(12, $parcel[0], true);
        $record .= $this->fld(12, $parcel[sizeof($parcel) - 1], true);
        $record .= $this->fld(6, sizeof($parcel), true);
        $record .= $this->fld(3, 0, true); // 11
        $record .= $this->fld(1, "C"); // 11-b
        $record .= $this->fld(4, 1, true); // always 0001
        $record .= $this->fld(7, number_format($consignment->getWeight(), 2), true); //
        $record .= $this->fld(4, ""); // length
        $record .= $this->fld(4, ""); // width

        $record .= $this->fld(4, ""); // 15 - height
        $record .= $this->fld(35, $consignment->getHAwb());
        $record .= $this->fld(5, ""); // cost centre
        $record .= $this->fld(35, ""); // 19
        $record .= $this->fld(8, $manifest_type);
        //
        $record .= $this->fld(7, ""); // 21 - volume
        $record .= $this->fld(7, "");
        $record .= $this->fld(6, sizeof($parcel), true);
        $record .= $this->fld(4, $handling);
        $record .= $this->fld(1, "");
        //
        $record .= $this->fld(1, "N");
        $record .= $this->fld(1, "N");
        $record .= $this->fld(70, substr(str_replace("\r\n", " ", $consignment->getDescription()), 0, 20));
        $record .= "\r\n";

        return $record;
    }

    /**
     * Gives a string formatted to fixed length
     *
     * @param int $len
     * @param string $data
     */
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

    private function getSenderAddressRecord($agentObject, $agentCountryObject) {

        $agent = $agentObject;
        $country = $agentCountryObject; //new Country($agent->getCountryId());

        $record = "AS";
        $record .= $this->fld(3, $country->getIso());
        $record .= $this->fld(6, "");
        $record .= $this->fld(35, $agent->getAgentName());
        $record .= $this->fld(35, "");
        //
        $record .= $this->fld(35, $agent->getAgentName());
        $record .= $this->fld(35, $agent->getAddressLine1());
        $record .= $this->fld(35, $agent->getAddressLine2());
        $record .= $this->fld(35, $agent->getCity());
        $record .= $this->fld(9, $agent->getPostCode());
        //
        $record .= $this->fld(8, "");
        $record .= $this->fld(12, "");
        $record .= $this->fld(35, $agent->getContactName());
        $record .= $this->fld(35, "");
        $record .= $this->fld(25, $agent->getTelephone());
        //
        $record .= $this->fld(25, ""); // 16
        $record .= $this->fld(17, "");
        $record .= $this->fld(40, "");
        $record .= $this->fld(17, "");
        $record .= $this->fld(17, "");
        $record .= "\r\n";
        return $record;
    }

    /**
     * Get delivery address record
     * @param iAddress
     * @return string
     */
    private function getDeliveryAddressRecord(Consignment $address) {
        // company mandatory, use name if company is blank
        $company = trim($this->makeUTF8($address->getCompany()));
        if ($company == "")
            $company = trim($this->makeUTF8($address->getContact()));

        $country = new Country($address->getCountryId());
        if ($company == "")
            $company = "-";
        $record = "AD";
        $record .= $this->fld(3, $country->getIso());
        $record .= $this->fld(6, "");
        $record .= $this->fld(35, $this->makeUTF8($company));
        $record .= $this->fld(35, "");
        $record .= $this->fld(35, $this->makeUTF8($address->getAddressLine1()));
        $record .= $this->fld(35, $this->makeUTF8($address->getAddressLine2()));
        $record .= $this->fld(35, $this->makeUTF8($address->getCity()));
        $record .= $this->fld(35, "");
        $record .= $this->fld(9, $this->format_postcode($this->makeUTF8($address->getPostcode()), "GB"));
        $record .= $this->fld(8, "");
        $record .= $this->fld(12, "");
        $record .= $this->fld(35, substr($this->makeUTF8($address->getContact()), 0, 20));
        $record .= $this->fld(35, "");
        $record .= $this->fld(25, $address->getTelephone());
        $record .= $this->fld(25, "");
        $record .= $this->fld(17, "");
        $record .= $this->fld(40, "");
        $record .= $this->fld(17, "");
        $record .= $this->fld(17, "");
        $record .= "\r\n";
        return $record;
    }

    /*     * **
     * Format postcode based on country.
     */

    private function format_postcode($postcode, $country_code) {
        $postcode = str_replace(" ", "", $postcode);
        if ($country_code == "GB") {
            $postcode = $this->address_FormatPostcode($postcode);
        }
        return $postcode;
    }

    private static function address_FormatPostcode($postcode) {
        $postcode = strToUpper(str_replace(" ", "", $postcode));

        $len = strlen($postcode);
        if ($len > 3) {
            $postcode = substr($postcode, 0, $len - 3) . " " . substr($postcode, - 3);
        }
        return $postcode;
    }

    public function makeUTF8($str, $encoding = "") {
        $str = preg_replace('/[^(\x20-\x7F)]*/', '', $str);
        $str = str_replace('&', 'and', $str);
        $str = str_replace('<', '&lt;', $str);
        $str = str_replace('>', '&gt;', $str);
        $str = str_replace("'", "", $str);

        if ($str !== "") {
            if (empty($encoding) && self::isUTF8($str))
                $encoding = "UTF-8";
            if (empty($encoding))
                $encoding = mb_detect_encoding($str, 'UTF-8, ISO-8859-1');
            if (empty($encoding))
                $encoding = "ISO-8859-1"; //  if charset can't be detected, default to ISO-8859-1
            return $encoding == "UTF-8" ? $str : @mb_convert_encoding($str, "UTF-8", $encoding);
        }
    }

    public function isUTF8($str) {
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

    /**
     * Get record for a parcel (consignment piece)
     *
     * @param ConsignmentPiece $parcel
     * @return string
     */
    private function getParcelRecord($parcel, $manifest_number, $note_number) {
        $record = "PA";
        $record .= $this->fld(10, $manifest_number, true);
        $record .= $this->fld(3, $note_number, true);
        $record .= $this->fld(35, $parcel);
        $record .= $this->fld(7, "");
        $record .= $this->fld(4, "");
        $record .= $this->fld(4, "");
        $record .= $this->fld(4, "");
        $record .= $this->fld(7, "");
        $record .= $this->fld(7, "");
        $record .= $this->fld(6, "PARCEL");
        $record .= $this->fld(2, "J");
        $record .= $this->fld(1, "");
        $record .= $this->fld(35, "");
        $record .= $this->fld(35, "");
        $record .= $this->fld(70, "");
        $record .= "\r\n";
        return $record;
    }

    private function CustomRecord($consignment, $parcelObj) {
        $record = "";
        foreach($parcelObj as $pkey => $parcel){
            //$parcel  = $parcel[0];
            $parcelDescription = json_decode($parcel->getDescription());
            $parcelCountry = json_decode($parcel->getCommodityCode());
            $parcelQty = json_decode($parcel->getQty());
            $parcelValue = json_decode($parcel->getItemValue());
            $parcelHscode = json_decode($parcel->getHsCode());
            $this->customValueCount = 0;
            if(count($parcelDescription) > 0){
                foreach($parcelDescription as $key=>$desc){
                    $this->customValueCount = $key;
                    $record .= $this->fld(6, "VALUES");
                    $record .= $this->fld(10, "");
                    $record .= $this->fld(50, "");
                    $record .= $this->fld(8, "", true);
                    $record .= $this->fld(3, "");
                    $record .= $this->fld(3, str_pad($parcelQty[$key], 3, 0, STR_PAD_LEFT));
                    $record .= $this->fld(50, $desc);
                    $record .= $this->fld(8, str_pad($parcelValue[$key], 8, 0, STR_PAD_LEFT));
                    $record .= $this->fld(3, "GBP");
                    $record .= $this->fld(8, "", true);
                    $record .= $this->fld(3, "");
                    $record .= $this->fld(10, "", true);
                    $record .= $this->fld(8, "", TRUE);
                    $record .= $this->fld(3, "");
                    $record .= $this->fld(10, "");
                    $record .= $this->fld(35, "");
                    $record .= $this->fld(35, "");
                    $record .= $this->fld(35, "");
                    $record .= $this->fld(10, date("d/m/Y", time()));
                    $record .= $this->fld(3, "DDU");
                    $record .= $this->fld(10, $parcelHscode[$key]); //HSCODE
                    $record .= $this->fld(35, "");
                    $record .= $this->fld(10, "");
                    $record .= $this->fld(20, "");
                    $record .= $this->fld(10, "");
                    $record .= $this->fld(10, "");
                    $record .= $this->fld(10, "");
                    $record .= $this->fld(17, "");
                    $record .= $this->fld(1, "");
                    $record .= $this->fld(8, "00003.00");
                    $record .= $this->fld(3, "GBP");
                    $record .= $this->fld(10,"Sales");
                    $record .= $this->fld(14,$consignment->getEoriNumber()); // GB720542861000 EORI NO
                    $countryFilter = new CountryFilter();
                    $countryFilter->addFieldFilter("iso", $parcelCountry[$key]);
                    $countryList = $countryFilter->getList();
                    if(count($countryList) > 0)
                        $manufacturyCountry = $countryList[0];
                    else
                        $manufacturyCountry = new Country($consignment->getSenderCountryId());
                    $record .= $this->fld(3,$manufacturyCountry->getNumCode());
                    $record .= "\r\n";
                }
            }
            else
            {
                $this->customValueCount = $pkey;
                $record .= $this->fld(6, "VALUES");
                $record .= $this->fld(10, "");
                $record .= $this->fld(50, "");
                $record .= $this->fld(8, "", true);
                $record .= $this->fld(3, "");
                $record .= $this->fld(3, str_pad($consignment->getNumberPieces(), 3, 0, STR_PAD_LEFT));
                $record .= $this->fld(50, $consignment->getDescription());
                $record .= $this->fld(8, str_pad($consignment->getValue(), 8, 0, STR_PAD_LEFT));
                $record .= $this->fld(3, "GBP");
                $record .= $this->fld(8, "", true);
                $record .= $this->fld(3, "");
                $record .= $this->fld(10, "", true);
                $record .= $this->fld(8, "", TRUE);
                $record .= $this->fld(3, "");
                $record .= $this->fld(10, "");
                $record .= $this->fld(35, "");
                $record .= $this->fld(35, "");
                $record .= $this->fld(35, "");
                $record .= $this->fld(10, date("d/m/Y", time()));
                $record .= $this->fld(3, "DDU");
                $record .= $this->fld(10, ''); //HSCODE
                $record .= $this->fld(35, "");
                $record .= $this->fld(10, "");
                $record .= $this->fld(20, "");
                $record .= $this->fld(10, "");
                $record .= $this->fld(10, "");
                $record .= $this->fld(10, "");
                $record .= $this->fld(17, "");
                $record .= $this->fld(1, "");
                $record .= $this->fld(8, "00003.00");
                $record .= $this->fld(3, "GBP");
                $record .= $this->fld(10,"Sales");
                $record .= $this->fld(14,$consignment->getEoriNumber()); // GB720542861000 EORI NO
                $manufacturyCountry = new Country($consignment->getCountryId());
                $record .= $this->fld(3,$manufacturyCountry->getNumCode());
                $record .= "\r\n";
            }
        }
        return $record;
    }

    /**
     * Get header record string
     *
     * @return string
     */
    private function getHeaderRecord($record_count, $run_number, $ftpConstants) {
        $record = "HD";
        $record .= $this->fld(5, $ftpConstants["YODEL_BOOKING_FILE_NUMBER"]);

        $record .= $this->fld(10, date("d/m/Y", time()));
        $record .= $this->fld(6, $run_number, true);
        $record .= $this->fld(8, $record_count, true);
        $record .= $this->fld(6, "2", true);
        $record .= $this->fld(6, $ftpConstants["YODEL_GAZ_VERSION"], true);
        $record .= "\r\n";
        return $record;
    }

    /**
     * Get footer record string
     *
     * @return string
     */
    private function getFooterRecord($record_count) {
        //echo $record_count; die;
        $record = "TR";
        $record .= $this->fld(8, $record_count, true);
        return $record;
    }

    private function getLinkFile($constant) {
        if ($this->link_file == null || trim($this->link_file) == '') {
            // set file name
            // - save new file temporarily in database to get ID for use as run number
            $DomesticDayDefFile = new DomesticDayDefFile();
            $meter_number = $constant['YODEL_BOOKING_FILE_NUMBER'];
            $DomesticDayDefFile->setFileName("temp");
            $DomesticDayDefFile->save();
            $run_number = $DomesticDayDefFile->getId();
            $filename = "UKD";
            $filename .= $meter_number . "." . substr("00$run_number", - 3);
            $DomesticDayDefFile->setFileName($filename);
            $DomesticDayDefFile->setSentDate(time());
            $DomesticDayDefFile->save();
            //
            $this->link_file = $DomesticDayDefFile;
        }
        return $this->link_file;
    }

  
    private function getBookingFileId() {
        $linkfile = $this->getLinkFile();
        return $linkfile->getFileName();
    }

    public function carrierCheckGazFiles() {
        $outputArray = [];
        $yodelGazFiles = ["DESTINATION_PRDSERVICES.TXT", "DESTINATION_EXCEPTION.TXT", "DESTINATION_STATION.TXT", "REAMUSID.TXT", "SERVICE.TXT"];
        $fileNotFoundArray = [];
        $fileNotFoundFlag = false;
        $className = strtolower(static::class);
        $gazzetierFilesPath = BASE_PATH . "gazzetier/" . $className . "/" . date("Y_m_d") . "/";
        foreach ($yodelGazFiles as $fileName) {
            $filePathcheck = $gazzetierFilesPath . $fileName;
            if (!file_exists($filePathcheck)) {
                $fileNotFoundArray[] = $fileName; //$filePathcheck;
                $fileNotFoundFlag = true;
            }
        }

        if ($fileNotFoundFlag) {
            $outputArray['status'] = "ERROR";
            $outputArray['FILES_NOT_FOUND'] = $fileNotFoundArray;
        } else {
            $outputArray['status'] = "SUCCESS";
            $outputArray['FILES'] = $yodelGazFiles;
        }
        return $outputArray;
    }

    public function carrierGazFilesProcess($filename) {
        //$yodelGazFiles  =   ["DESTINATION_PRDSERVICES.TXT","DESTINATION_EXCEPTION.TXT","DESTINATION_STATION.TXT","REAMUSID.TXT","SERVICE.TXT"];
        $output = [];

        $className = strtolower(static::class);
        $gazzetierFilesPath = BASE_PATH . "gazzetier/" . $className . "/" . date("Y_m_d") . "/" . $filename;
        if (!file_exists($gazzetierFilesPath)) {
            $output["status"] = "ERROR";
            $output["message"] = "File not available on the server. Please upload and try again";
            return $output;
        }

        switch ($filename) {
            case "DESTINATION_PRDSERVICES.TXT":
                // $local_file = "../GAZ153_TXT/DESTINATION_PRDSERVICES.TXT";  //. $file_name;

                $queryRestult = DbAccess3::runQueryWithError("TRUNCATE TABLE reamus_product_service");
                $file = $gazzetierFilesPath;
                $f = fopen($file, "r");
                $ln = 0;
                while ($line = fgets($f)) {
                    ++$ln;
                    if ($line === FALSE)
                        print ("FALSE\n");
                    else {
                        if ($ln > 1) {
                            $strlen = strlen($line);
                            $array = explode("|", $line);
                            $reamus_id = trim($array[0]);
                            $product_code = trim($array[1]);
                            $feature_code = trim($array[2]);
                            $exception = trim($array[3]);

                            $sql = "insert into reamus_product_service(reamus_id, product_code , feature_code, exception) values ('" . $reamus_id . "', '" . $product_code . "', '" . $feature_code . "', '" . $exception . "' ) ";
                            if ($queryRestult !== FALSE)
                                $queryRestult = DbAccess3::runQueryWithError($sql);
                            else {
                                $output["status"] = "ERROR";
                                $output["message"] = $filename . "File cannot be processed";
                            }
                        }
                    }
                }

                break;
            case "DESTINATION_EXCEPTION.TXT":
                // for destination _station
                // $local_file = "../GAZ153_TXT/DESTINATION_EXCEPTION.TXT";  //. $file_name;
                $queryRestult = DbAccess3::runQueryWithError("TRUNCATE TABLE reamus_exception");
                $file = $gazzetierFilesPath;
                $f = fopen($file, "r");
                $ln = 0;
                while ($line = fgets($f)) {
                    ++$ln;
                    if ($line === FALSE)
                        print ("FALSE\n");
                    else {
                        if ($ln > 1) {
                            $strlen = strlen($line);
                            $array = explode("|", $line);
                            $country_code = trim($array[0]);
                            $postcode_from = str_replace(" ", "", trim($array[3]));
                            $postcode_to = str_replace(" ", "", trim($array[4]));
                            $product_code = trim($array[5]);
                            $feature_code = trim($array[6]);
                            $hub_id = trim($array[7]);
                            $sql = "insert into reamus_exception(country_code, postcode_from , postcode_to, product_code,  feature_code) values ('" . $country_code . "', '" . $postcode_from . "', '" . $postcode_to . "', '" . $product_code . "', '" . $feature_code . "' ) ";
                            if ($queryRestult !== FALSE)
                                $queryRestult = DbAccess3::runQueryWithError($sql);
                            else {
                                $output["status"] = "ERROR";
                                $output["message"] = $filename . "File cannot be processed";
                            }
                        }
                    }
                }

                break;
            case "DESTINATION_STATION.TXT":
// for destination _station
                // $local_file = "../GAZ153_TXT/DESTINATION_STATION.TXT";  //. $file_name;
                $queryRestult = DbAccess3::runQueryWithError("TRUNCATE TABLE reamus_destination_station");
                $file = $gazzetierFilesPath;
                $queryRestult = DbAccess3::runQueryWithError("LOAD DATA LOCAL INFILE '" . $file . "'
                        INTO TABLE reamus_destination_station
                        FIELDS TERMINATED by '|'
                        LINES TERMINATED BY '\r\n'
                        IGNORE 1 LINES
                        (country_code,@dummy,@dummy, postcode_from , postcode_to, product_code,@dummy,@dummy, station_id, hub_id,@dummy)
                     SET ID = NULL");

                DbAccess3::runQueryWithError("UPDATE reamus_destination_station SET postcode_from = REPLACE(postcode_from, ' ', ''), postcode_to = REPLACE(postcode_to, ' ', '');");

                /*

                  $f = fopen ($file, "r");
                  $ln= 0;
                  while ($line= fgets ($f)) {
                  ++$ln;
                  if ($line===FALSE) print ("FALSE\n");
                  else
                  {
                  if ( $ln > 1)
                  {
                  $strlen = strlen($line);
                  $array = explode("|", $line);
                  $country_code = trim($array[0]);
                  $postcode_from = str_replace(" ", "", trim($array[3]));
                  $postcode_to = str_replace(" ", "", trim($array[4]));
                  $product_code = trim($array[5]);
                  $station_id = trim($array[8]);
                  $hub_id = trim($array[9]);
                  $sql = "insert into reamus_destination_station(country_code, postcode_from , postcode_to, product_code, station_id, hub_id) values ('". $country_code ."', '". $postcode_from ."', '" . $postcode_to . "', '" . $product_code . "', '" . $station_id . "',  '" . $hub_id . "' ) ";
                  if($queryRestult !== FALSE)
                  $queryRestult   =   DbAccess3::runQueryWithError($sql);
                  else
                  {
                  $output["status"] = "ERROR";
                  $output["message"]  =   $filename. "File cannot be processed";
                  }
                  }
                  }

                  } */

                break;
            case "REAMUSID.TXT":
                // for reamusid
                //$local_file = "../GAZ153_TXT/REAMUSID.TXT";  //. $file_name;
                $queryRestult = DbAccess3::runQueryWithError("TRUNCATE TABLE reamus_site");
                $file = $gazzetierFilesPath;
                $f = fopen($file, "r");
                $ln = 0;
                while ($line = fgets($f)) {
                    ++$ln;
                    if ($line === FALSE)
                        print ("FALSE\n");
                    else {
                        if ($ln > 1) {
                            $strlen = strlen($line);
                            $array = explode("|", $line);
                            $reamus_id = trim($array[0]);
                            $site = trim($array[1]);
                            $reamus_id2 = trim($array[2]);
                            $country_code = trim($array[3]);
                            $sql = "insert into reamus_site(reamus_id, site, reamus_id2, country_code) values ('" . $reamus_id . "', '" . $site . "', '" . $reamus_id2 . "', '" . $country_code . "') ";
                            if ($queryRestult !== FALSE)
                                $queryRestult = DbAccess3::runQueryWithError($sql);
                            else {
                                $output["status"] = "ERROR";
                                $output["message"] = $filename . "File cannot be processed";
                            }
                        }
                    }
                }
                break;
            case "SERVICE.TXT":
                // it is for reamus_service
                // $local_file = "../GAZ153_TXT/SERVICE.TXT";  //. $file_name;
                $queryRestult = DbAccess3::runQueryWithError("TRUNCATE TABLE reamus_service");
                $file = $gazzetierFilesPath;
                $f = fopen($file, "r");
                $ln = 0;
                while ($line = fgets($f)) {
                    ++$ln;
                    if ($line === FALSE)
                        print ("FALSE\n");
                    else {
                        if ($ln > 0) {
                            $strlen = strlen($line);
                            // $array = explode("|", $line);
                            $service_id = mb_substr($line, 0, $strlen - ($strlen - 3));
                            $service_description = mb_substr($line, 3, $strlen - ($strlen - 45));
                            $product_line1 = mb_substr($line, 48, $strlen - ($strlen - 15));
                            $product_line2 = mb_substr($line, 63, $strlen - ($strlen - 35));
                            $product_code = mb_substr($line, 98, $strlen - ($strlen - 2));
                            $date_code = mb_substr($line, 100, $strlen - ($strlen - 2));
                            $day_text = mb_substr($line, 102, $strlen - ($strlen - 1));
                            $time_code = mb_substr($line, 103, $strlen - ($strlen - 1));
                            $time_text = mb_substr($line, 104, $strlen - ($strlen - 1));
                            $handling = mb_substr($line, 105, $strlen - ($strlen - 10));
                            $feature_id = mb_substr($line, 115, $strlen - ($strlen - 3));
                            $feature_code = mb_substr($line, 118, $strlen - ($strlen - 2));
                            $file_type = mb_substr($line, 120, $strlen - ($strlen - 3));
                            $consignment_flag = mb_substr($line, 123, $strlen - ($strlen - 1));


                            $sql = "insert into reamus_service(service_id,service_description, product_line1, product_line2, product_code, date_code, day_text,
					         time_code, time_text, handling, feature_id, feature_code, file_type, consignment_flag) values('" . trim($service_id) . "', '" . trim($service_description) . "', '" . trim($product_line1) . "', '" . trim($product_line2) . "',
					        '" . trim($product_code) . "', '" . trim($date_code) . "', '" . trim($day_text) . "', '" . trim($time_code) . "' , '" . trim($time_text) . "', '" . trim($handling) . "',	'" . trim($feature_id) . "',  '" . trim($feature_code) . "',  '" . trim($file_type) . "', '" . trim($consignment_flag) . "' )    ";
                            if ($queryRestult !== FALSE) {
                                $queryRestult = DbAccess3::runQueryWithError($sql);
                            } else {
                                $output["status"] = "ERROR";
                                $output["message"] = $filename . "File cannot be processed";
                            }
                        }
                    }
                }

                break;
            default :
                $output["status"] = "ERROR";
                $output["message"] = $filename . " not match. ";
                break;
        }
        if ($output["status"] != 'ERROE') {
            if ($queryRestult === FALSE) {
                $output["status"] = "ERROR";
                $output["message"] = $filename . " not match. ";
            } else {
                $output["status"] = "SUCCESS";
                $output["message"] = $filename . " processed successfully. ";
            }
        }
        return $output;
    }

    public function reconciliation_data($headingArr,$carrierId,$relPath,$new_csv_file_created,$filePath,$batchNumber) {
        $output = [];
        $carrierObj = new Carrier($carrierId);
//        @$csv_file = $file;
//        if (!empty($csv_file['name'])) {
//            $file_name = $csv_file['name'];
//            $path_parts = pathinfo($file_name);
//            $ext = strtolower($path_parts['extension']);
//            $basename = $path_parts['basename'];
//            if ($ext == 'csv') {
                $carrierName = str_replace(" ","_",$carrierObj->getCarrier());
                $user = SessionManager::getUser();
                $userId = $user->getId();
                
                $csvStr = '';
                $templateCheck = 1;
                $checkExist = 0;
                $output['new_invoice_save'] = 0;
                $output['total_weight'] = 0;
                $output['total_pieces'] = 0;
                $output['total_amount'] = 0;
                    $row = 1;
                    $dataArr = [];
                    if (($handle = fopen($relPath, "r")) !== FALSE) {
                        $fuelChargePercentage = 0.00;
                        $invoiceNumber = '';
                        while (($data = fgetcsv($handle)) !== FALSE) {
//                            if(count($data) == 47) {
                                if ($row > 15) {
                                    /* heading is here */
                                    if(array_search('Surchrge Service Code', $data) !== false){
                                        $templateCheck = 2;
                                    }
                                    $output['template'] = $templateCheck;
                                }
                                if ($data['0'] == "Invoice Number") {
                                    $invoiceNumber = $data['1'];
                                    $output['invoice_number'] = $invoiceNumber;
//                                    $supplierInvoiceFilter = new SupplierInvoicesFilter();
//                                    $supplierInvoiceFilter->where(['si.invoice_number' => $invoiceNumber]);
//                                    $supplierInvoiceFilter->where(['si.account_id' => $user->getUserAccountId()]);
//                                    $supplierInvoiceFilterObjs = $supplierInvoiceFilter->getList();
//                                    if(count($supplierInvoiceFilterObjs)) {
//                                        $output['status'] = 'error';
//                                        $output['message'] = 'This file is already processed';
//                                        $output['new_invoice_save'] = 1;
//                                        break;
//                                    }
                                }
                                if ($data['0'] == "Invoice Date") {
                                    $output['invoice_date'] = $data['1'];
                                }
                                if ($data['1'] == "Fuel Surcharge") {
                                    $fuelChargePercentage = $data['3'];
                                }
                                if ($row > 16) {
                                    if ($data[5] != "") {
                                        if($templateCheck == 1) {
                                            $basicCharge = formatNumber($data['44']);
                                            if ($basicCharge == "") {
                                                $basicCharge = formatNumber($data['42']);
                                            }
                                            $subtotal = $basicCharge;
                                            $fuelCharges = 0.00;
                                            if ($fuelChargePercentage > 0) {
                                                $fuelCharges = formatNumber((($fuelChargePercentage / 100) * $basicCharge));
                                                $subtotal += $fuelCharges;
                                            }
                                            $vatPercentage = formatNumber($data['45']);
                                            $vatCharges = 0.00;
                                            if ($vatPercentage > 0) {
                                                $vatCharges = formatNumber((($vatPercentage / 100) * $subtotal));
                                                $subtotal += $vatCharges;
                                            }
                                            $length = formatNumber($data['37']);
                                            $actualLength = formatNumber($data['32']);
                                            if ($actualLength != "" && $actualLength > 0) {
                                                $length = $actualLength;
                                            }
                                            $height = formatNumber($data['38']);
                                            $actualHeight = formatNumber($data['33']);
                                            if ($actualHeight != "" && $actualHeight > 0) {
                                                $height = $actualHeight;
                                            }
                                            $width = formatNumber($data['39']);
                                            $actualWidth = formatNumber($data['34']);
                                            if ($actualWidth != "" && $actualWidth > 0) {
                                                $width = $actualWidth;
                                            }
                                            $volWeight = formatNumber($data['40'], 3);
                                            $actualVolWeight = formatNumber($data['35'], 3);
                                            if ($actualVolWeight != "" && $actualVolWeight > 0) {
                                                $volWeight = $actualVolWeight;
                                            }
                                            $weight = formatNumber($data['41'], 3);
                                            $actualWeight = formatNumber($data['36'], 3);
                                            if ($actualWeight != "" && $actualWeight > 0) {
                                                $weight = $actualWeight;
                                            }
                                            $collectionDate = $data['18'];
                                            if($collectionDate != "") {
                                                /* 24/07/2020  12:00:00 am */
                                                $dateArr = explode("/",$collectionDate);
                                                $arr = explode(" ",$dateArr[2]);
                                                $newDateFormate =  $arr[0].'-'.$dateArr[1].'-'.$dateArr[0];
                                                $collectionDate = formatDateTime($newDateFormate,'Y-m-d');
                                            }
                                            $currency = "GBP";
                                            $dt = [
                                                'account_number' => '',
                                                'invoice_number' => $invoiceNumber,
                                                'agent_reference_number' => '',
                                                'collection_date' => $collectionDate,
                                                'delivery_country' => $data['24'],
                                                'mawb' => '',
                                                'awb' => cleanCsvCall($data['10']),
                                                'hawb' => cleanCsvCall($data['17']),
                                                'service_name' => $data['14'],
                                                'service_code' => $data['13'],
                                                'weight' => $weight,
                                                'vol_weight' => $volWeight,
                                                'length' => $length,
                                                'width' => $width,
                                                'height' => $height,
                                                'number_of_pieces' => $data['11'],
                                                'basic_charges' => $basicCharge,
                                                'fuel_charges' => $fuelCharges,
                                                'additional_charges' => '',
                                                'vat' => $vatCharges,
                                                'total_amount' => $subtotal,
                                                'notes' => $data['46'],
                                                'currency' => $currency
                                            ];
                                            $output['total_weight'] += $weight;
                                            $output['total_pieces'] += $data['11'];
                                            $output['total_amount'] += $subtotal;
                                            $output['currency'] = $currency;
                                            $dataArr[] = $dt;
                                            $comaSept = implode(",", $dt);
                                            $csvStr .= rtrim($comaSept, ',');
                                            $csvStr .= "\r\n";
                                        } else if($templateCheck == 2) {
                                            $additionalCharges = formatNumber($data['41']);
                                            if ($additionalCharges == "") {
                                                $additionalCharges = formatNumber($data['39']);
                                            }
                                            $outOfGaugeLength = '';
                                            $outOfGaugeWeight = '';
                                            $outOfGaugeVolume = '';
                                            $lateOrMissingPan = '';
                                            if(in_array(trim($data['13']),array('OGW1','OGW2','OGW3'))){
                                                $outOfGaugeWeight = $additionalCharges;
                                                $additionalCharges = '';
                                            }
                                            else if(in_array(trim($data['13']),array('OGL1','OGL2','OGL3'))){
                                                $outOfGaugeLength = $additionalCharges;
                                                $additionalCharges = '';
                                            }
                                            else if(in_array(trim($data['13']),array('OGV1','OGV2','OGV3'))){
                                                $outOfGaugeVolume = $additionalCharges;
                                                $additionalCharges = '';
                                            }
                                            else if(in_array(trim($data['13']),array('PAN','PAN','PAN'))){
                                                $lateOrMissingPan = $additionalCharges;
                                                $additionalCharges = '';
                                            }
                                             
                                                
                                            $currency = "GBP";
                                            $fuelCharges = 0.00;
                                            $vatPercentage = formatNumber($data['42']);
                                            $vatCharges = formatNumber($data['43']);
                                            $subtotal = formatNumber($data['44']);
                                            $length = formatNumber($data['34']);
                                            $actualLength = formatNumber($data['29']);
                                            if ($actualLength != "" && $actualLength > 0) {
                                                $length = $actualLength;
                                            }
                                            $height = formatNumber($data['35']);
                                            $actualHeight = formatNumber($data['30']);
                                            if ($actualHeight != "" && $actualHeight > 0) {
                                                $height = $actualHeight;
                                            }
                                            $width = formatNumber($data['36']);
                                            $actualWidth = formatNumber($data['31']);
                                            if ($actualWidth != "" && $actualWidth > 0) {
                                                $width = $actualWidth;
                                            }
                                            $volWeight = formatNumber($data['37'], 3);
                                            $actualVolWeight = formatNumber($data['32'], 3);
                                            if ($actualVolWeight != "" && $actualVolWeight > 0) {
                                                $volWeight = $actualVolWeight;
                                            }
                                            $weight = formatNumber($data['38'], 3);
                                            $actualWeight = formatNumber($data['33'], 3);
                                            if ($actualWeight != "" && $actualWeight > 0) {
                                                $weight = $actualWeight;
                                            }
                                            $collectionDate = $data['18'];
                                            if($collectionDate != "") {
                                                /* 24/07/2020  12:00:00 am */
                                                $dateArr = explode("/",$collectionDate);
                                                $arr = explode(" ",$dateArr[2]);
                                                $newDateFormate =  $arr[0].'-'.$dateArr[1].'-'.$dateArr[0];
                                                $collectionDate = formatDateTime($newDateFormate,'Y-m-d');
                                            }
                                            $dt = [
                                                'account_number' => '',
                                                'invoice_number' => $invoiceNumber,
                                                'agent_reference_number' => '',
                                                'collection_date' => $collectionDate,
                                                'delivery_country' => '',
                                                'mawb' => '',
                                                'awb' => cleanCsvCall($data['10']),
                                                'hawb' => cleanCsvCall($data['17']),
                                                'service_name' => '',
                                                'service_code' => $data['15'],
                                                'weight' => $weight,
                                                'vol_weight' => $volWeight,
                                                'length' => $length,
                                                'width' => $width,
                                                'height' => $height,
                                                'number_of_pieces' => $data['11'],
                                                'basic_charges' => '',
                                                'fuel_charges' => $fuelCharges,
                                                'additional_charges' => $additionalCharges,
                                                'vat' => $vatCharges,
                                                'total_amount' => $subtotal,
                                                'notes' => $data['45'],
                                                'currency' => $currency,
                                                'surcharge_service_code' => $data['13'],
                                                'out_of_gauge_length' => $outOfGaugeLength,
                                                'out_of_gauge_weight' => $outOfGaugeWeight,
                                                'out_of_gauge_volume' => $outOfGaugeVolume,
                                                'late_or_missing_pan' => $lateOrMissingPan
                                                    
                                            ];
                                            //
                                            $output['total_weight'] += $weight;
                                            $output['total_pieces'] += $data['11'];
                                            $output['total_amount'] += $subtotal;
                                            $output['currency'] = $currency;
                                            $dataArr[] = $dt;
                                            $comaSept = implode(",", $dt);
                                            $csvStr .= rtrim($comaSept, ',');
                                            $csvStr .= "\r\n";
                                        }
                                    }
                                }
                                $row++;
//                            } else {
//                                $output['status'] = 'error';
//                                $output['return'] = true;
//                                $output['message'] = 'Invalid csv file';
//                                break;
//                            }
                        }
                    } else {
                        $output['status'] = 'error';
                        $output['message'] = 'File can not open please check permission';
                    }
                if($output['status'] != "error" && !$output['return']) {
                    if($templateCheck == 2){
                        $headingArr[] = 'surcharge_service_code';
                        $headingArr[] = 'out_of_gauge_length';
                        $headingArr[] = 'out_of_gauge_weight';
                        $headingArr[] = 'out_of_gauge_volume';
                        $headingArr[] = 'late_or_missing_pan';
                    }
                    $comaSeptHeading = implode(",",$headingArr);
                    
                    $tableColumn = rtrim($comaSeptHeading,',');
                     
                    $csvStr = $tableColumn."\r\n".$csvStr;
                
                
                    $myCsvFile = fopen($new_csv_file_created, "a") or die("Unable to open file!");
                    fwrite($myCsvFile, $csvStr);
                    fclose($myCsvFile);

                    $dateNow = date('Y-m-d H:i:s');
                    $load_data_sql = "LOAD DATA LOCAL INFILE '" . $new_csv_file_created . "' INTO TABLE `reconciliation_data`
                            FIELDS ENCLOSED BY '\"' 
                            TERMINATED BY ',' LINES TERMINATED BY '\n' IGNORE 1 LINES (
                                " . $tableColumn . "
                            ) 
                            SET  created_at='" . $dateNow . "', check_service_weight = '1',check_service_vol_weight = 0,  batch_number= '" . $batchNumber . "'";

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
    public function getSummeryData($relPath){
        $returnArr = [];
        $row = 1;
        $isInvoiceType = false;
        $isDataSet = true;
        if (($handle = fopen($relPath, "r")) !== FALSE) {
            while (($data = fgetcsv($handle)) !== FALSE) {
                if ($data['0'] == "Invoice Number") {
                    $returnArr['invoice_number'] = $data['1'];
                }
                if ($data['0'] == "Invoice Date") {
                    $collectionDate = $data['1'];
                    if($collectionDate != "") {
                        $dateArr = explode("/",$collectionDate);
                        $newDateFormate =  $dateArr[2].'-'.$dateArr[1].'-'.$dateArr[0];
                        $collectionDate = formatDateTime($newDateFormate,'Y-m-d');
                    }
                    $returnArr['collection_date'] = $collectionDate;
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
