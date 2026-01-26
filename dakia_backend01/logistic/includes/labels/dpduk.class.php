<?php
include_classes([
    'checkdigit.class',
    'carrierdatafilelog.class',
    'carrierdatafilelogfilter.class',
    'domesticdpd.class',
    'domesticdpdfilter.class',
    'checkdigit.class'
    ]);
include_classes([
    'pdfmerger'
    ], 'labels');
class DPDUK implements CarrierService {

    private $pdf;
    private $serviceValues = null;
    private $agentValues = null;
    private $constants = null;
    private $country = null;
    private $user = null;
    private $booking_file = null;
    private $record_array = null;

    public function __construct() {
        
    }

    public function validation(Consignment $consignment, Services $service, Country $country) {
        $returnOutput = array();
        $handling_dpd = str_replace('STDPDGB', '', $service->getCode());
        
        if ($service->getCode() == "STDPDGB12") {

            if (Consignment::getServiceAvailibility($consignment->getPostcode(), $handling_dpd) != "1") {
                $returnOutput[] = "The service is not available for this postcode " . $consignment->getPostcode();
                return $returnOutput;
            } else {
                $consignment->setRoutingCodeEur($consignment->find3DigitServiceCode($handling_dpd));
            }
        } else {
            if($country->getIso() != "IE"){
                if (Consignment::getServiceIntAvailibility($country->getIso(), $consignment->getPostcode(), "dpd_classic_deport") == "") {
                    $returnOutput[] = "The service is not available for this postcode " . $consignment->getPostcode();
                    return $returnOutput;
                }
            }

            if (strpos($consignment->getPostcode(), "-") > 0) {
                $split_postcode = explode("-", $consignment->getPostcode());
                $routing_postcode = $split_postcode[1];
            } else {
                $routing_postcode = $consignment->getPostcode();
            }

            if ($country->getIso() == "IE") {
                $consignment->setRoutingCodeEur($consignment->find3DigitServiceCode("11"));
            } else {

                $consignment->setOtherRoutingCode(Consignment::findRoutingCode($country->getIso(), $routing_postcode));
                $consignment->setRoutingCodeEur(Consignment::findCountrycodesRouting($country->getName()));
            }
            
        }
        $remoteAreaCheck = $this->remoteareas($consignment, $country, $sender);
        switch ($remoteAreaCheck) {
            case 'allowed':
                break;
            case 'not-allowed':
                $returnOutput[] = "The service is not allowed for ".$consignment->getPostcode()." postcode. ";
                break;
        }
        return $returnOutput;
    }

    public function remoteareas($consignment, $country, $sender) {
            $country			=	$country->getIso();
            $city				=	strtolower($consignment->getCity());

            $customerPostcode	=	strtoupper(str_replace(' ','', $consignment->getPostcode()));
            $customerPostcode	=	strtoupper(str_replace('-','', $customerPostcode));

            if( $country == 'DK' && ((int)$customerPostcode >= 100 && (int)$customerPostcode <= 999  ) )
            {
                    return "not-allowed";
            }
            else if( $country == 'FI' && ((int)$customerPostcode >= 22100 && (int)$customerPostcode <= 22990  ) )
            {
                    return "not-allowed";
            }
            else if( $country == 'FR' && ((int)$customerPostcode >= 20000 && (int)$customerPostcode <= 20999  ) )
            {
                    return "not-allowed";
            }
            else if( $country == 'FR' && ((int)$customerPostcode >= 98000  && (int)$customerPostcode <= 98599 ) )
            {
                    return "not-allowed";
            }
            else if( $country == 'FR' && (int)$customerPostcode == 85350   )
            {
                    return "not-allowed";
            }
            else if( $country == 'DE' && ((int)$customerPostcode == 25980 ) )
            {
                    return "not-allowed";
            }
            else if( $country == 'IT' && ((int)$customerPostcode == 232030 ) )
            {
                    return "not-allowed";
            }
            else if( $country == 'IT' && ((int)$customerPostcode >= 7010 && (int)$customerPostcode <= 7100  ) )
            {
                    return "not-allowed";
            }
            else if( $country == 'IT' && ((int)$customerPostcode >= 90010 && (int)$customerPostcode <= 98079  ) )
            {
                    return "not-allowed";
            }
            else if( $country == 'IT' && ((int)$customerPostcode >= 9010 && (int)$customerPostcode <= 9170  ) )
            {
                    return "not-allowed";
            }
            else if( $country == 'IT' && ((int)$customerPostcode == 47890  ) )
            {
                    return "not-allowed";
            }
            else if( $country == 'IT' && ((int)$customerPostcode == 120 ) )
            {
                    return "not-allowed";
            }
            else if( $country == 'PT' && ((int)$customerPostcode >= 9500 && (int)$customerPostcode <= 9999  ) )
            {
                    return "not-allowed";
            }
            else if( $country == 'PT' && ((int)$customerPostcode >= 9000 && (int)$customerPostcode <= 9499  ) )
            {
                   return "not-allowed";
            }
            else if( $country == 'ES' && ((int)$customerPostcode >= 7000 && (int)$customerPostcode <= 7999  ) )
            {
                   return "not-allowed";
            }
            else if( $country == 'ES' && ((int)$customerPostcode >= 35000 && (int)$customerPostcode <= 35999  ) )
            {
                    return "not-allowed";
            }
            else if( $country == 'ES' && ((int)$customerPostcode >= 38000 && (int)$customerPostcode <= 38999  ) )
            {
                    return "not-allowed";
            }
            return "allowed";
    }

    public function label($consignment, $labelType = 'pdf', $size = '100x150') {

        $output = array();
        $this->serviceValues = new Services($consignment->getServiceId());
        $this->country = new Country($consignment->getCountryId());
        $this->user = SessionManager::getUser();
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
        if (trim(@$this->constants['DPD_SLID']) == '' || trim(@$this->constants['DPD_IDENTIFICATION']) == '' || trim(@$this->constants['DPD_SHIPPER_ADDRESS_LINE_1']) == '' || trim(@$this->constants['DPD_SHIPPER_POSTCODE']) == '') {
            $output['STATUS'] = 'ERROR';
            $output['MESSAGE'] = "Please check service constants. Please contact to itsupport@oneworldexpress.com";
            return $output;
        }
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
        
        // Generate label for each parecel
        foreach ($parcel_list as $parcel) {
            $pdf = new PdfBase(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
            $this->pdf = $pdf;
            if ($parcel->getTrackingNumber() == '') {
                $resultArray = LicencePlate::getLicencePlateNumber($licence_plate_id);
                if (trim($resultArray['STATUS']) == 'ERROR')
                    return $resultArray;
                else
                    $licence_plate = @$this->constants['DPD_IDENTIFICATION'] . @$this->constants['DPD_SLID'] . $resultArray["RANGE"];
                $parcel->setTrackingNumber($licence_plate);
                $parcel->save();
            }
            else {
                $licence_plate = $parcel->getTrackingNumber();
            }

            $licence_plate_array[$parcel_idx] = $licence_plate;

            $this->pdf->SetPrintFooter(false);
            $this->pdf->SetFooterMargin(0);
            $this->pdf->SetAutoPageBreak(false, 0);

            $serviceCode = $this->serviceValues->getCode();
            if ($serviceCode == "STDPDGB19" && $this->country->getIso() != "IE") {
                $this->STDPDGBEUR($consignment, $parcel_idx, $parcel_count, $licence_plate);
            } else {
                preg_match_all('!\d+!', $serviceCode, $matches);
                $functionName = str_replace($matches[0], '', $serviceCode);
                $this->$functionName($consignment, $parcel_idx, $parcel_count, $licence_plate);
            }

            // Save Piece Label
            $pieceFileName = "../_assets/pdf/" . date('Y_m_d') . '/' . $licence_plate . ".pdf";
            $this->pdf->IncludeJS("print();");
            $this->pdf->Output($pieceFileName, "F");
            $pieceLabelArray[] = $pieceFileName;
            $parcel->setParcelLabel(date('Y_m_d') . '/' . $licence_plate. ".pdf");
            $parcel->save();

            ++$parcel_idx;
        }
        
        // Merge Piece Label for consignment
        $mergeFileName = '../_assets/pdf/' . date('Y_m_d') . '/' . $consignment->getId() . ".pdf";
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
        
        $output['STATUS'] = 'SUCCESS';
        $output['LABEL'] = date('Y_m_d') . '/' . $consignment->getId() . ".pdf";
        $output['TRACKING_NUMBER'] = $licence_plate_array;
        return $output;
    }

    public function tracking($trackingNumber, $trackBy = 'parcel', $EDI = false) 
    {
        $tracking = new Tracking();
        $deliveredArray = array('1','60','70','417','451','459','Delivered to Private Address','Delivered'); 
        
        require_once(BASE_PATH."includes/labels/dpduktrackingstatus.class.php");
        
        $parcelFilter = new ParcelFilter();
        $parcelFilter->addLicensePlateFilter($trackingNumber);
        $result = $parcelFilter->getList();
        $consignmentId = $result[0]->getConsignmentId();
        
        $consignment = new Consignment($consignmentId);
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
        
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $this->constants["DPDUK_TRACKING_URL"]);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array("Content-Type: application/xml",
            "Accept: application/xml"));

        $xml = '<?xml version="1.0" encoding="UTF-8"?>
                <trackingrequest>
                    <user>' . $this->constants["DPDUK_TRACKING_USERNAME"] . '</user>
                    <password>' . $this->constants["DPDUK_TRACKING_PASSWORD"] . '</password>
                    <trackingnumbers>
                            <trackingnumber>' . $trackingNumber . '</trackingnumber>
                    </trackingnumbers>
                </trackingrequest>';

        curl_setopt($curl, CURLOPT_POSTFIELDS, $xml);

        if ($errno = curl_errno($curl)) {
            $error_message = curl_strerror($errno);
            echo "cURL error ({$errno}):\n {$error_message}";
        }
        $result = curl_exec($curl);
        $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        $xmlData = simplexml_load_string($result, "SimpleXMLElement", LIBXML_NOCDATA);
        $jsonData = json_encode($xmlData);
        $trackingResponse = json_decode($jsonData, TRUE);
        
        $trackingEventsTemp = $trackingResponse["trackingdetails"]["trackingdetail"]["trackingevents"]["trackingevent"];
       
        if (count($trackingEventsTemp) > 0) {
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
            
            
            ////////////////////// Carrier Received ////////////////////////////////
            
            $trackingDataFilterObj = new TrackingDataFilter();
            $trackingDataFilterObj->addFieldFilter('    tracking_number', $trackingNumber);        
            $trackingDataFilterObj->addFilter("carrier_code not in ('','0')");
            $trackingCarrierEvents = $trackingDataFilterObj->getList();
                       
            if(count($trackingCarrierEvents) > 0)
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
            
            if(isset($trackingEventsTemp['date'])){
                $trackingEvents[0] = $trackingEventsTemp;
            }else{
                $trackingEvents = $trackingEventsTemp;
            }
            if ($entityId > 0) 
            {
                $parcelEntity = new Parcel($entityId);
                $finalStatusCode = $parcelEntity->getParcelStatusCode();
                
                foreach ($trackingEvents as $event) 
                {                   
                    $DateTime = $event['date'];
                    $EventCode = $event['code'];                   
                    $EventDescription = $event['description'];
                    
                    if($EventCode == 17)
                    {
                        if(stripos($EventDescription, "Redelivery request") !== false)
                        {
                            $EventCode = "Redelivery request";
                        }
                        else
                        if(stripos($EventDescription, "Delivered to Private Address") !== false)
                        {
                            $EventCode = "Delivered to Private Address";
                        }
                        else
                        if(stripos(strtolower($EventDescription), "delivered") !== false)
                        {
                            $EventCode = "Delivered";
                        }
                        else
                        {
                            $EventCode = $EventDescription;
                        }
                    } 
                    $ServiceAreaDescription = $event['locality'];
                    $Signatory = '';
                    if($carrierCodeCarrierReceived == $EventCode)
                    {
                        continue;
                    }
                    $spTrackingStatus = DPDUKTrackingStatus::getOweStatusCode($EventCode);
                    
                    ////////////////////// Carrier Received ////////////////////////////////
                    if($carrierReceivedCheck == 1 && $EventCode != '0' )
                    {
                        $spTrackingStatus = '148';
                        $carrierReceivedCheck = 0 ;                        
                    }
                    else
                    {
                        $spTrackingStatus = DPDUKTrackingStatus::getOweStatusCode($EventCode);
                    }            
                    ////////////////////// Carrier Received ////////////////////////////////
                    $result = $tracking->saveTrackPoint($entityId, $trackBy, $DateTime, $trackingNumber, $spTrackingStatus, $ServiceAreaDescription, $EventCode, $EventDescription, $deliveredArray, $finalStatusCode);                                                            
                    if($result == true)
                    {
                        break;
                    }
                }                
                $tracking->saveConsignmentTrackingStatus($trackingNumber, 'DPDUKTrackingStatus'); 
            }
        }
    }

    public function sendData($tracking_numbers = array()) {
        $output = [];
        $agentServiceArray = [];
        $consignmentData = new ConsignmentFilter();
        $consignmentData->addFilter("     s.carrier_id= '66'", "servicefilter");
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
                     con.region 'country_region', c.service_id, c.weight, c.hawb, c.description, c.company, c.country_id,c.awb,c.date_created,c.value,c.date_booked,
                      c.contact, c.address_line_1, c.address_line_2, c.city, c.postcode, c.telephone,c.email, c.other_routing_code, routing_code_eur, c.number_pieces, c.ioss_number, c.vat_number, c.eori_number, c.currency ");

                    if (count($consignmentShipmentData) > 0) {
                        $consignmentIdArray = [];
                        foreach ($consignmentShipmentData as $consignmentItemData) {

                            $consignmentId = $consignmentItemData->getConsignmentId();
                            $consignmentIdArray[] = $consignmentId;
                            $carrierId = $consignmentItemData->getCarrierId();
                            $serviceCode = $this->serviceValues->getCode();
                            preg_match_all('!\d+!', $serviceCode, $matches);
                            // $functionName = "consignmentData" . str_replace($matches[0], "", $serviceCode);
                            $this->record_array[] = $this->consignmentData($consignmentItemData, $this->constants[$serviceid]);
                        }
                        // set file name
                        // - save new file temporarily in database to get ID for use as run number
                        $carrierId = $this->serviceValues->getCarrierId();
                        $agentid = trim($agentServiceArray['agent'][$key]);

                        $run_number = CarrierDataFileLog::generateRunNumber($carrierId, $agentid, $dateChecked = true);
                        $run_number = str_pad(trim($run_number), 3, "0", STR_PAD_LEFT);
                        $datenumber = str_pad(date("z", time()) + 1, 3, "0", STR_PAD_LEFT);
                        $this->booking_file = $bookingfile = $this->constants[$serviceid]["DPD_AUTH_NUMBER"] . "-" . $run_number . "." . $datenumber;
                        $carrierDataFileLog = new CarrierDataFileLog();
                        $carrierDataFileLog->setCarrierId($carrierId);
                        $carrierDataFileLog->setAgentId($agentid);
                        $carrierDataFileLog->setFileName($this->booking_file);
                        $carrierDataFileLog->setRunNumber($run_number);
                        $carrierDataFileLog->save();


                        if ($this->sendBookings($this->constants[$serviceid], $run_number, $serviceCode)) {
                            if (!empty($consignmentIdArray)) {

                                $sql = "UPDATE consignment SET send_courier_data = 1, booked_file_id = '" . $bookingfile . "' 
                                        WHERE id IN ('" . implode("','", $consignmentIdArray) . "') AND id <> '0' 
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

    private function STDPDGB(Consignment $consignment, $parcel_idx, $parcel_count, $licence_plate) {
        $page_size = array(102, 105);
        $this->pdf->AddPage("P", $page_size);

        $this->pdf->line(0, 28, 60, 28);  // 1st horizontal half line
        $this->pdf->line(60, 0, 60, 40);
        $this->pdf->line(90, 0, 90, 40);
        $this->pdf->line(47, 28, 47, 40);

        $this->pdf->line(0, 40, 105, 40); //2nd horizontal full line
        $this->pdf->line(0, 40.2, 105, 40.2); //2nd horizontal full line
        $this->pdf->line(0, 40.3, 105, 40.3); //2nd horizontal full line

        $this->pdf->setFont("helvetica", "L", 10);
        $address = "";
        $address2 = "";
        $address3 = "";
        $company = "";
        if ($consignment->getCompany() != "")
            $company = $consignment->getCompany();
        if ($consignment->getAddressLine1() != "")
            $address1 = $consignment->getAddressLine1();
        if ($consignment->getAddressLine2() != "")
            $address2 = $consignment->getAddressLine2();
        if ($consignment->getAddressLine3() != "")
            $address3 = $consignment->getAddressLine3();

        $this->pdf->Text(3, 4, $consignment->getContact());
        $this->pdf->Text(3, 8, $address1);
        $this->pdf->Text(3, 12, $address2);
        $this->pdf->Text(3, 16, $address3);
        $this->pdf->Text(3, 20, $consignment->getCity());
        $this->pdf->Text(3, 24, $consignment->getPostcode());

        $this->pdf->setFont("helvetica", "L", 7);
        $this->pdf->Text(3, 27.5, "Contact");
        $this->pdf->Text(3, 30.5, "Phone  ");
        $this->pdf->Text(3, 33.5, "Info   ");
        //$this->pdf->Text(3, 36.5,"Consignment   " );

        $this->pdf->setFont("helvetica", "", 7);
        $this->pdf->Text(12, 27.5, $consignment->getContact());
        $this->pdf->Text(12, 30.5, $consignment->getTelephone());
        $fontname = $this->pdf->addTTFfont('../assets/fonts/simhei.ttf', 'TrueTypeUnicode', '', 32);
        $this->pdf->SetFont($fontname, '', 8);
        $this->pdf->Text(12, 33.5, $consignment->getNotes());

        $this->pdf->setFont("helvetica", "L", 5);
        $this->pdf->Text(49.50, 28.5, "Packages");
        $this->pdf->Text(49.50, 34, "Weight");

        $this->pdf->setFont("helvetica", "B", 9);
        $this->pdf->Text(46.50, 30.5, ($parcel_idx + 1) . " of " . $parcel_count);
        $this->pdf->setFont("helvetica", "B", 7);
        $this->pdf->Text(46.50, 36, number_format($consignment->getWeight(), 1) . " KG");
        $this->pdf->setFont("helvetica", "B", 9);

        $parameters = explode("|", $consignment->getRoutingCodeEur());

        $digit3servicecode = $parameters[0];
        $priority = $parameters[1];
        $service = $parameters[2];

        if ($priority == "Y") {
            $this->pdf->setFont("helvetica", "B", 15);
            $this->pdf->setTextColor(255, 255, 255);
            $this->pdf->SetFillColor(10, 10, 10);
            $this->pdf->setXy(70, 41.5);
            $this->pdf->Cell(30, 10, $service, 1, 0, "C", true);
            $this->pdf->setTextColor(0, 0, 0);
            $this->pdf->SetFillColor(255, 255, 255);
        } else {
            $this->pdf->setFont("helvetica", "B", 15);
            $this->pdf->Text(75, 40, $service);
        }


        if ($this->country->getIso() == "IE") {
            $pos = strpos(strtolower($consignment->getCity()), "dublin");
            if ($pos === false) {
                $postcode = "ZZ75 0AA";
                $irelandpostcode = $postcode;
            } else {
                $postcode = "ZZ71 0AA";
                $irelandpostcode = $postcode;
            }
        } else {
            $postcode = $consignment->getPostcode();
        }

        $postcode = substr($postcode, 0, -2);
        $domesticDpd = new DomesticDPDFilter();
        $domesticDpd->addFieldFilter("postcode_sector", $postcode);
        $domesticList = $domesticDpd->getColumnList("dpd_depot, ilk_depot");

        if (count($domesticList) > 0) {
            $domestic = $domesticList[0];
            $dpd_depot = $domestic->getDpdDepot();
            $depo_number = $domestic->getIlkDepot();
            if (trim($dpd_depot) == "0028") {
                $this->pdf->setFont("helvetica", "B", 15);
                $this->pdf->setTextColor(255, 255, 255);
                $this->pdf->SetFillColor(10, 10, 10);
                $this->pdf->setXy(5, 60);
                $this->pdf->Cell(20, 7, "LOCAL", 1, 0, "C", true);
                $this->pdf->setTextColor(0, 0, 0);
                $this->pdf->SetFillColor(255, 255, 255);
            }
        }


        $post_code = str_replace(" ", "", $consignment->getPostcode());
        $this->pdf->setFont("helvetica", "L", 8);
        $this->pdf->Text(85, 47, "Service");
        $this->pdf->Text(3, 47, "Track");
        $this->pdf->Text(28, 65, Date("d/m/Y h:m") . "  Routing Ver: 60");
        $this->pdf->setFont("helvetica", "L", 9);

        if ($this->country->getIso() == "IE") {
            $this->pdf->Text(35, 60, $digit3servicecode . "-IE-$irelandpostcode");
            $this->pdf->setFont("helvetica", "L", 28);
            $this->pdf->Text(25, 50, "IE -" . $irelandpostcode);
        } else {
            $this->pdf->Text(35, 60, $digit3servicecode . "-GB-$post_code");
            $this->pdf->setFont("helvetica", "L", 28);
            $this->pdf->Text(25, 50, "GB -" . $consignment->getPostcode());
        }

        if ($this->country->getIso() == "IE") {
            $posi = strpos(strtolower($consignment->getCity()), "dublin");
            if ($posi === false) {
                $post_code = "0000002";
            } else {
                $post_code = "0000001";
            }
        }


        $post_code = str_replace(" ", "", $post_code);
        $post_code = str_pad(trim($post_code), 7, "0", STR_PAD_LEFT);  //postcode

        $origin_identification = trim(@$this->constants['DPD_IDENTIFICATION']);  //original identification
        $account_identification = trim(@$this->constants['DPD_SLID']);  //original slid


        $awb = $licence_plate;
        $parcel_number_first_one = substr($awb, 7, 1);
        $parcel_number_first_four = substr($awb, 8, 4);  // first 4 parcel number
        $parcel_number_last_two = substr($awb, 12, 4);  // last two parcel number


        $Digit3countryIsoCode = $this->country->getNumcode();

        $trackingnumbertext = $awb;
        $barcodetrackingnumbertext = $post_code . $awb . $digit3servicecode . $this->country->getNumcode();
        $first4number = substr($barcodetrackingnumbertext, 0, 4);
        $second4number = substr($barcodetrackingnumbertext, 4, 4);
        $third4number = substr($barcodetrackingnumbertext, 8, 4);
        $fourth4number = substr($barcodetrackingnumbertext, 12, 4);
        $fifth4number = substr($barcodetrackingnumbertext, 16, 4);
        $sixth4number = substr($barcodetrackingnumbertext, 20, 4);
        $last3number = substr($barcodetrackingnumbertext, 24, 3);
        $barcodetext = "%" . $post_code . $awb . $digit3servicecode . $this->country->getNumcode();

        $digit14servicecode = ISO7064Decoder::NumberToChar(ISO7064Decoder::calculateCheckDigit($trackingnumbertext));
        $digit28servicecode = ISO7064Decoder::NumberToChar(ISO7064Decoder::calculateCheckDigit($barcodetrackingnumbertext));

        $this->pdf->setFont("helvetica", "B", 15);
        $this->pdf->Text(3, 41, $origin_identification);
        $this->pdf->setFont("helvetica", "L", 13);

        $first_eight_number = $account_identification;
        $this->pdf->Text(15, 41.5, "$first_eight_number $parcel_number_first_four  $parcel_number_last_two $digit14servicecode");

        $style['position'] = 'C';
        $this->pdf->write1DBarcode($barcodetext, 'C128', 0, 70, '', 25, 0.5, $style, 'Y');
        $this->pdf->setFont("helvetica", "L", 10);
        $this->pdf->Text(22, 100, "$first4number $second4number $third4number $fourth4number $fifth4number $sixth4number $last3number $digit28servicecode");

        $this->pdf->StartTransform();
        $this->pdf->Rotate(270, 80, 70);


        $this->pdf->setFont("helvetica", "L", 7);
        $this->pdf->Text(13, 60, "Sender Account    " . trim(@$this->constants['DPD_ACCOUNT_NUMBER']));
        $this->pdf->Text(13, 90, "Delivery Address");

        $this->pdf->Text(13, 63, trim(@$this->constants['DPD_SHIPPER_COMPANY_NAME']));
        $this->pdf->Text(13, 66, trim(@$this->constants['DPD_SHIPPER_ADDRESS_LINE_1']));
        $this->pdf->Text(13, 69, trim(@$this->constants['DPD_SHIPPER_ADDRESS_LINE_2']));
        $this->pdf->Text(13, 72, trim(@$this->constants['DPD_SHIPPER_ADDRESS_LINE_3']) . trim(@$this->constants['DPD_SHIPPER_CITY']));
        $this->pdf->Text(13, 75, trim(@$this->constants['DPD_SHIPPER_POSTCODE']));
        $this->pdf->Text(13, 79, "Phone " . trim(@$this->constants['DPD_SHIPPER_TELEPHONE']));
        $this->pdf->setFont("helvetica", "", 9);
        $this->pdf->Text(13, 85, "Ref " . $consignment->getHawb());

        $this->pdf->setFont("helvetica", "L", 12);
        $this->pdf->Text(25, 50, "DPD");
        $this->pdf->Text(15, 54, "www.dpd.co.uk");
        $this->pdf->StopTransform();
        return $awb;
    }

    private function STDPDGBEUR(Consignment $consignment, $parcel_idx, $parcel_count, $licence_plate) {
        $page_size = array(102, 105);
        $this->pdf->AddPage("P", $page_size);
        $this->pdf->line(0, 28, 65, 28);  // 1st horizontal half line
        $this->pdf->line(65, 0, 65, 46);
        $this->pdf->line(90, 0, 90, 46);
        $this->pdf->line(47, 28, 47, 46);


        $this->pdf->line(0, 46, 105, 46); //2nd horizontal full line
        $this->pdf->line(0, 46.2, 105, 46.2); //2nd horizontal full line
        $this->pdf->line(0, 46.3, 105, 46.3); //2nd horizontal full line


        $this->pdf->setFont("helvetica", "L", 11);
        $address = "";
        $address2 = "";
        $address3 = "";
        $company = "";
        if ($consignment->getCompany() != "")
            $company = $consignment->getCompany();
        if ($consignment->getAddressLine1() != "")
            $address1 = $consignment->getAddressLine1();
        if ($consignment->getAddressLine2() != "")
            $address2 = $consignment->getAddressLine2();
        if ($consignment->getAddressLine3() != "")
            $address3 = $consignment->getAddressLine3();

        $this->pdf->Text(3, 3, $consignment->getContact());
        $this->pdf->Text(3, 6.5, $company);
        $this->pdf->Text(3, 10, $address1);
        $this->pdf->Text(3, 13.5, $address2);
        $this->pdf->Text(3, 17, $address3);
        $this->pdf->Text(3, 20.5, $consignment->getCity());

        $this->pdf->setFont("helvetica", "", 7);
        $this->pdf->Text(3, 30.5, "Contact");
        $this->pdf->Text(3, 34.5, "Phone  ");
        $this->pdf->Text(3, 38, "Info   ");

        $this->pdf->setFont("helvetica", "", 8.5);

        $this->pdf->Text(12, 30.5, $consignment->getContact());
        $this->pdf->Text(12, 34.5, $consignment->getTelephone());
        $fontname = $this->pdf->addTTFfont('../assets/fonts/simhei.ttf', 'TrueTypeUnicode', '', 32);
        $this->pdf->SetFont($fontname, '', 8);
        $this->pdf->Text(12, 38, substr($consignment->getNotes(), 0, 25));


        $this->pdf->setFont("helvetica", "", 5);
        $this->pdf->Text(49.50, 28.5, "Packages");
        $this->pdf->Text(49.50, 36, "Weight");

        $this->pdf->setFont("helvetica", "B", 8);
        $this->pdf->Text(49, 30.5, ($parcel_idx + 1) . " of " . $parcel_count);
        $this->pdf->Text(48.50, 39, number_format($consignment->getWeight(), 1) . " KG");

        $this->pdf->setFont("helvetica", "B", 10);

        $parameters = explode("|", $consignment->getRoutingCodeEur());
        $iso_number = $parameters[0];
        $allow_express = $parameters[1];
        $allow_classic = $parameters[2];
        $eu_country = $parameters[3];
        $shipping_advice = $parameters[4];

        $parameters = explode("|", $consignment->getOtherRoutingcode());
        $zipcode_from = $parameters[0];
        $zipcode_to = $parameters[1];
        $air_express_depot = $parameters[2];
        $air_express_osort = $parameters[3];
        $air_express_dsort = $parameters[4];
        $dpd_classic_deport = $parameters[5];
        //  echo $dpd_classic_deport; exit;
        $dpd_classic_osort = $parameters[6];
        $dpd_classic_dsort = $parameters[7];


        $service = "D"; //"D"; DPD CLASSIC SERVICE USE D , DPD EXPPRESS PACK SERVICE USE IE2
        $depot = $dpd_classic_deport;
        $osort = $dpd_classic_osort;
        $dsort = $dpd_classic_dsort;
        $digit3servicecode = "101"; //"101 for CLASSIC SERVICE" 136 FOR ExPRESS SERVICE; 

        $this->pdf->setFont("helvetica", "B", 17);
        $this->pdf->Text(90, 45.5, $service);


        if (strtoupper($this->country->getIso()) == "NL") {
            $pos = strpos($consignment->getPostcode(), "-");
            if ($pos > 0) {
                $arrpostcode = explode("-", $consignment->getPostcode());
                $postcode = $arrpostcode[1] . $arrpostcode[0];
                $consignment->setPostCode($postcode);
            }
        }
        $this->pdf->setFont("helvetica", "L", 10);
        $this->pdf->Text(3, 24, $consignment->getPostcode());
        $post_code = str_replace(" ", "", $consignment->getPostcode());
        $this->pdf->setFont("helvetica", "L", 7);
        $this->pdf->Text(90, 52, "Service");
        $this->pdf->Text(3, 51, "Track");
        $this->pdf->Text(28, 65, Date("d/m/Y h:m") . "  Routing Ver: 60");
        $this->pdf->setFont("helvetica", "L", 9);
        $this->pdf->Text(35, 60, $digit3servicecode . "-" . $this->country->getIso() . "- $post_code");
        $this->pdf->setFont("helvetica", "L", 25);
        $this->pdf->Text(25, 50, $this->country->getIso() . "-" . $depot);
        $this->pdf->Text(7, 59, $osort);
        $this->pdf->Text(75, 59, $dsort);
        $this->pdf->setFont("helvetica", "L", 13);

        $this->pdf->SetXY(5, 55);

        $this->pdf->setFont("helvetica", "L", 25);
        $post_code = str_replace("-", "", $post_code);
        $post_code = str_pad(trim($post_code), 7, "0", STR_PAD_LEFT);  //postcode

        $post_code = strtoupper($post_code);

        $account_identification = trim(@$this->constants['DPD_SLID']);  //original identification
        $origin_identification = trim(@$this->constants['DPD_IDENTIFICATION']);  //original slid


        $awb = $licence_plate;
        $parcel_number_first_one = substr($awb, 7, 1);
        $parcel_number_first_four = substr($awb, 8, 4);  // first 4 parcel number
        $parcel_number_last_two = substr($awb, 12, 4);  // last two parcel number

        $trackingnumbertext = $awb;
        $barcodetrackingnumbertext = $post_code . $awb . $digit3servicecode . $this->country->getNumCode();

        $first4number = substr($barcodetrackingnumbertext, 0, 4);
        $second4number = substr($barcodetrackingnumbertext, 4, 4);
        $third4number = substr($barcodetrackingnumbertext, 8, 4);
        $fourth4number = substr($barcodetrackingnumbertext, 12, 4);
        $fifth4number = substr($barcodetrackingnumbertext, 16, 4);
        $sixth4number = substr($barcodetrackingnumbertext, 20, 4);
        $last3number = substr($barcodetrackingnumbertext, 24, 3);
        $barcodetext = "%" . $post_code . $awb . $digit3servicecode . $this->country->getNumCode();


        $digit14servicecode = ISO7064Decoder::NumberToChar(ISO7064Decoder::calculateCheckDigit($trackingnumbertext));
        $digit28servicecode = ISO7064Decoder::NumberToChar(ISO7064Decoder::calculateCheckDigit($barcodetrackingnumbertext));
        $this->pdf->setFont("helvetica", "B", 15);
        $this->pdf->Text(3, 46, $origin_identification);
        $this->pdf->setFont("helvetica", "L", 13);
        $style['position'] = 'C';
        //  echo $account_identification."-".$parcel_number_first_one . "-" . $parcel_number_first_four . "-" . $parcel_number_last_two . "-" . $digit14servicecode;
        $first_eight_number = $account_identification;
        $this->pdf->Text(15, 46.5, "$first_eight_number $parcel_number_first_four  $parcel_number_last_two $digit14servicecode");
        $this->pdf->write1DBarcode($barcodetext, 'C128', 0, 70, '', 25, 0.5, $style, 'Y');
        $this->pdf->setFont("helvetica", "L", 10);
        $this->pdf->Text(22, 95, "$first4number $second4number $third4number $fourth4number $fifth4number $sixth4number $last3number$digit28servicecode");


        $this->pdf->setFont("helvetica", "B", 10);
        $this->pdf->StartTransform();
        $this->pdf->Rotate(270, 80, 70);

        $this->pdf->setFont("helvetica", "L", 7);

        $this->pdf->Text(13, 60, "Sender  Account : " . trim(@$this->constants['DPD_ACCOUNT_NUMBER']));


        $this->pdf->Text(12, 90, "Delivery Address");
        $this->pdf->Text(13, 63, trim(@$this->constants['DPD_SHIPPER_COMPANY_NAME']));
        $this->pdf->Text(13, 66, trim(@$this->constants['DPD_SHIPPER_ADDRESS_LINE_1']));
        $this->pdf->Text(13, 69, trim(@$this->constants['DPD_SHIPPER_ADDRESS_LINE_2']) . trim(@$this->constants['DPD_SHIPPER_ADDRESS_LINE_3']) . trim(@$this->constants['DPD_SHIPPER_CITY']));
        $this->pdf->Text(13, 72, trim(@$this->constants['DPD_SHIPPER_POSTCODE']));
        $this->pdf->Text(13, 75, "Phone " . trim(@$this->constants['DPD_SHIPPER_TELEPHONE']));
        $this->pdf->setFont("helvetica", "", 8);
        $this->pdf->Text(13, 80, "Ref 1: " . $consignment->getHawb());
        $this->pdf->setFont("helvetica", "L", 12);
        $this->pdf->Text(25, 50, "DPD");
        $this->pdf->Text(15, 54, "www.dpd.co.uk");


        $this->pdf->StopTransform();

        $this->pdf->setFont("helvetica", "L", 7);
    }

    private function sendBookings($ftpConstants, $runNumber, $serviceCode) {
        preg_match_all('!\d+!', $serviceCode, $matches);
        $serviceNameFolder = str_replace($matches[0], '', $serviceCode);
        $headerfunctionName = "getHeaderRecord" ;
        $footerfunctionName = "getFooterRecord" ;
        $tokenFunctionName = "getTokenRecord" ;
        $isUpload = true;
        if (sizeof($this->record_array) > 0) {


            $path = SETTING_DIR_ASSETS . "data_send/dpd_booking/" . $serviceNameFolder . "/" . date("Y_m_d") . "/";
            if (!file_exists($path))
                @mkdir($path, 0777, TRUE);

            $file_path = $path . $this->booking_file;
            chmod($path, 0777);
            // record count including header & footer
            $record_count = sizeof($this->record_array);

            // create file
            $file_handle = @fopen($file_path, 'w');
            fwrite($file_handle, $this->$headerfunctionName($runNumber, $ftpConstants));

            foreach ($this->record_array as $record) {
                fwrite($file_handle, $record);
            }
            fwrite($file_handle, $this->$tokenFunctionName());
            fwrite($file_handle, $this->$footerfunctionName($record_count));

            // close file
            fclose($file_handle);


            if (isset($ftpConstants['DPD_FTP_SITE']) && trim($ftpConstants['DPD_FTP_SITE']) != '') {
                
                $SETTING_FTP_USER_DPD = $ftpConstants['DPD_FTP_USER'];
                $SETTING_FTP_PASSWORD_DPD = $ftpConstants['DPD_FTP_PASSWORD'];
                $SETTING_FTP_SITE_DPD = $ftpConstants['DPD_FTP_SITE'];


                // FTP file to Yodel
                // - default port#

                $ftp_conn = ftp_connect($SETTING_FTP_SITE_DPD);
                if($ftp_conn){
                    $login = ftp_login($ftp_conn, $SETTING_FTP_USER_DPD, $SETTING_FTP_PASSWORD_DPD);
                    ftp_pasv($ftp_conn, true) or die("Unable switch to passive mode");
                    
                     $remote_file_path = "./IN/" . $this->booking_file;
                    // upload file
                    ftp_pasv($ftp_conn, true) or die("Unable switch to passive mode");
                    $isUpload = ftp_put($ftp_conn, $remote_file_path, $file_path, FTP_BINARY);
              /*  $ftp_object = new FTPfile($SETTING_FTP_USER_DPD, $SETTING_FTP_PASSWORD_DPD, $SETTING_FTP_SITE_DPD);

               
                $isUpload = $ftp_object->put($remote_file_path, $file_path, FTP_BINARY);
*/
                $this->booking_file = NULL;
                $this->record_array = NULL;
                if ($isUpload === false) {
                    mail("itsupport@oneworldexpress.com", "DPD LOGIN FAILED", "DPD LOGIN FAILED" . $this->booking_file);
                }
            }

            return $isUpload;
            }
        }
    }

    private function consignmentData(Consignment $consignment, $constants) {
        $this->country = new Country($consignment->getCountryId());
        if ($this->country->getIso() == "IE") {
            $pos = strpos(strtolower($consignment->getCity()), "dublin");
            if ($pos === false) {
                $post_code = "0000002";
                $irelandpostcode = "ZZ75 0AA";
            } else {
                $post_code = "0000001";
                $irelandpostcode = "ZZ71 0AA";
            }
        } else {
            $post_code = str_replace(" ", "", $consignment->getPostcode());
        }

        $post_code = str_pad(trim($post_code), 7, "0", STR_PAD_LEFT);  //postcode

        $awb = $consignment->getAwb();
        if ($this->country->getRegion() == 'INT' || $this->country->getRegion() == 'R1') { //&& $consignment->getCountryIsoCode() != "IE"
            $digit3servicecode = '101'; //'DPD CLASSIC PARCEL USE 101' // DPD EXRESS 136;
        } else {
            $parameters = explode("|", $consignment->getRoutingCodeEur());
            $digit3servicecode = $parameters[0];
            $priority = $parameters[1];
            $service = $parameters[2];
            
        }
        $depotDetails = explode("|", $consignment->getOtherRoutingCode());
        $dpd_classic_deport = $depotDetails[5];
        $barcodetext = "%" . $post_code . $awb . $digit3servicecode;

        $record = "61" . ",";    // Record Identifier 1

        $record .= $awb . ",";    // Parcel Number 2
        if ($this->country->getRegion() == 'INT' || $this->country->getRegion() == 'R1') { // && $consignment->getCountryIsoCode() != 'IE'
            $record .= "19" . ",";         //2 digit service 19 classic parcel // 39 for Express 3
        } else {
            $record .= "12" . ",";         //2 digit service 19 classic parcel		// 32 for Express 3
        }
        $record .= number_format($consignment->getWeight(), 1) . ","; //4

        $record .= "N" . ",";     //  Insurance 5
        $record .= "1" . ",";    // number of pieces 6
        if ($this->country->getIso() == "IE")
            $record .= $this->removecommas($irelandpostcode) . ","; //postcode 7
        else
            $record .= $this->removecommas($consignment->getPostcode()) . ","; //postcode 7
        $record .= $barcodetext . ","; // 28 digit barcode content 8
        if ($consignment->getContact() != "")
            $contacttext = $consignment->getContact();
        else
            $contacttext = $consignment->getCompany();

        $record .= "\"" . $this->removecommas($contacttext) . "\",";         // M shipment record id 9
        $record .= "\"" . $this->removecommas($consignment->getAddressLine1()) . "\",";  // 10 D destination code => DHL database (country/city/zip => airport)
        $record .= "\"" . $this->removecommas($consignment->getAddressLine2()) . "\","; //11
        $record .= "\"" . $this->removecommas($consignment->getAddressLine3()) . "\","; //12
        $record .= "\"" . $this->removecommas($consignment->getCity()) . "\","; //13
        $record .= "\"" . $this->removecommas($consignment->getAddressLine3()) . "\"" . ","; //14
        $record .= $this->country->getIso() . ","; //15
        $record .= "\"" . $constants["DPD_SLID"] . "\"" . ","; //16

        $record .= "\"" . $consignment->getHawb() . "\"" . ","; //17
        $record .= "\"" . $consignment->getReference() . "\"" . ","; //18
        $record .= "\"\"" . ","; //19
        $record .= "\"" . $consignment->getNotes() . "\"" . ","; //20
        $record .= "" . ","; //21
        $record .= "" . ","; //22
        $record .= "" . ","; //23
        $record .= "" . ","; //24
        $record .= "" . ","; //25
        $record .= "" . ","; //26
        $record .= "" . ","; //27
        $record .= "" . ","; //28
        $record .= "" . ","; //29

        if ($this->country->getRegion() == 'INT' || $this->country->getRegion() == 'R1') {
            $record .= "\"" . $this->removecommas($consignment->getDescription()) . "\"" . ","; //30
            $record .= $consignment->getCurrency() . ","; //31
            $record .= $consignment->getValue() . ","; //32
        } else {
            $record .= "" . ",";  //30
            $record .= "" . ",";  //31
            $record .= "" . ",";  //32
        }


        $record .= "" . ","; //33
        $record .= "" . ","; //34
        $record .= $consignment->getTelephone() . ","; //35
        $email = $consignment->getEmail();

        $record .= $email . ","; //email 36
        $record .= "" . ","; //37
        $record .= "" . ",";  //38		        
        $record .= "" . ","; //39
        $record .= "" . ","; //40			
        $record .= $constants["DPD_SHIPPER_COMPANY_NAME"] . ","; //41
        $record .= $constants["DPD_SHIPPER_ADDRESS_LINE_1"] . ",";  // D destination code => DHL database (country/city/zip => airport) 42
        $record .= $constants["DPD_SHIPPER_ADDRESS_LINE_2"] . ","; //43
        $record .= $constants["DPD_SHIPPER_ADDRESS_LINE_3"] . ","; //44
        $record .= $constants["DPD_SHIPPER_CITY"] . ","; //45
        $record .= "" . ","; //46	 
        $record .= $constants["DPD_SHIPPER_POSTCODE"] . ","; //47
        $shipper_country = $this->constants['DPD_SHIPPER_COUNTRY'];
        if ((int) $shipper_country > 0) {
            $shipperCountry = new Country($shipper_country);
            $sCountry = $shipperCountry->getIso();
        } else {
            $sCountry = $shipper_country;
        }
        $record .= "GB" . ","; //48
        $record .= $constants["DPD_SHIPPER_TELEPHONE"] . ",";  //posting location 49
        $record .= $constants["DPD_SHIPPER_CONTACT"] . ",";  //posting hub 50
        $record .= "" . ","; //51
        $record .= "" . ","; //52			        
        $record .= "01" . ","; //53
        $record .= "" . ","; //54			
        $record .= "OUT" . ","; //55			
        $record .= "" . ","; //56			
        $record .= "" . ","; //57			
        $record .= "\"" . $this->removecommas($contacttext) . "\","; //58

        $record .= "\"" . $this->removecommas($consignment->getTelephone()) . "\","; //59
        $record .= "\"" . $this->removecommas($consignment->getEmail()) . "\","  ; //60
        $record .= "" . ","; //61			
        $record .= "" . ","; //62
        $record .= "" . ","; //63
        $record .= "" . ","; //64
        $record .= "" . ","; //65
        $record .= "" . ","; //66
        $record .= "" . ","; //67
        $record .= "" . ","; //68
        $record .= "" . ","; //69
        $record .= date("d/m/Y") . ","; //70
        $record .= "" . ","; //71
        $record .= "" . ","; //72
        $record .= "" . ","; //73
        $record .= "" . ","; //74
        $record .= $this->country->getIso() . "-" . $dpd_classic_deport . ","; //75 Routing Code
        $record .= "" . ","; //76
        $record .= "N" . ","; //77
        $record .= "" . ","; //78
        $record .= "" . ","; //79
        
        
        $record .= "DAP" . ","; //80
        $record .= "" . ","; //81
        $record .= "" . ","; //82
        $record .= "DPDGroup UK" . ","; //83
        $record .= "Roebuck Lane" . ","; //84
        $record .= " " . ","; //85
        $record .= "Smethwick" . ","; //86
        $record .= "Birmingham" . ","; //87
        $record .= " " . ","; //88
        $record .= "B66 1BY" . ","; //89
        $record .= "GB" . ","; //90
        $record .= "" . ","; //91
        $record .= "" . ","; //92
        $record .= "" . ","; //93
        $record .= "" . ","; //94
        $record .= "" . ","; //95
        $record .= "" . ","; //96
        $record .= "" . ","; //97
        $record .= "" . ","; //98
        $record .= "" . ","; //99
        $record .= "" . ","; //100
        $record .= "" . ","; //101
        $record .= "" . ","; //102
        $record .= "N" . ","; //103
        $record .= "N" . ","; //104
        $record .= "" . ","; //105
        $record .= "" . ","; //106
        $record .= "" . ","; //107
        $record .= "" . ","; //108
        $record .= "" . ","; //109
        $record .= "" . ","; //110
        $record .= "" . ","; //111
        $record .= "" . ","; //112
        $record .= "" . ","; //113
        $record .= "" . ","; //114
        $record .= "" . ","; //115
        $record .= "" . ","; //116
        $record .= "" . ","; //117
        $record .= "" . ","; //118
        $record .= "" . ","; //119
        $record .= "" . ","; //120
        $record .= "" . ","; //121
        $record .= "" . ","; //122
        $record .= "" . ","; //123
        $record .= "" . ","; //124
        $record .= "" . ","; //125
        $record .= "" . ","; //126
        $record .= "" . ","; //127
        $record .= "" . ","; //128
        $record .= "" . ","; //129
        $record .= "" . ","; //130
        $record .= "" . ","; //131
        $record .= "" . ","; //132
        $record .= $consignment->getIossNumber() . ","; //133
        $record .= "N" . ","; //134
        $record .= $consignment->getHawb() . ","; //135
        $record .= $consignment->getNumberPieces() . ","; //136
        $record .= "" . ","; //137
        $record .= "" . ","; //138
        $record .= "" . ","; //139
        $record .= "" . ","; //140
        $record .= "" . ","; //141
        $record .= "" . ","; //142
        $record .= "" . ","; //143
        $record .= "" . ","; //144
        $record .= "" . ","; //145
        $record .= "" . ","; //146
        $record .= "" . ","; //147
        $record .= "" . ","; //148
        $record .= "" . ","; //149
        $record .= "" . ","; //150
        $record .= $consignment->getEoriNumber() . ","; //151
        $record .= $consignment->getVatNumber() . ","; //152
        $record .= "" . ","; //153
        $record .= "" . ","; //154
        $record .= "" ; //155
        
        $record .= "\r\n";
        $parcel_list = $consignment->getParcels();
        if(count($parcel_list) > 0){
            foreach($parcel_list as $parcel){
                $parcelDescription = json_decode($parcel->getDescription());
                $parcelCountry = json_decode($parcel->getCommodityCode());
                $parcelQty = json_decode($parcel->getQty());
                $parcelValue = json_decode($parcel->getItemValue());
                $parcelHscode = json_decode($parcel->getHsCode());
                $parcelSku = json_decode($parcel->getItemSku());
                $parcelUrl = json_decode($parcel->getItemUrl());
                $parcelWeight = json_decode($parcel->getPWeight());
                if(count($parcelDescription) > 0){
                    foreach($parcelDescription as $key=>$desc){
                        $record .= "63" . ","; //1
                        $record .= $parcel->getTrackingNumber().","; // 2
                        $record .= $desc.","; // 3
                        $record .= $desc .","; // 4
                        $record .= $parcelValue[$key].","; // 5
                        $record .= $parcelCountry[$key].","; // 6
                        $record .= $parcelHscode[$key].","; // 7
                        $record .= $parcelWeight[$key].",";// 8
                        $record .= $parcelQty[$key].","; // 9
                        $record .= "".","; // 10
                        $record .= $parcelSku[$key] .","; // 11
                        $record .= $parcelUrl[$key]; // 12
                        $record .= "\r\n";
                    }
                }

            }
        }
        return $record;
    }

    private function getTokenRecord() {

        $record = "68" . ",";        // M record header
        $record .= "PRODUCT" . ",";    // M version id
        $record .= "EDI";
        $record .= "\r\n";
        return $record;
    }

    /**
     * Get Header Record string
     *
     * @return string
     */
    private function getHeaderRecord($run_number, $constant) {
        $record = "60" . ",";   // M record type

        /* if($handling == "DPDDOM32")
          {
          $record .= "02" . ",";				// M business unit
          $record .= self::DOMESTIC_ACCOUNT_NUMBER  . ",";			// DPD SLID Number
          }
          else
          { */
        $record .= "01" . ",";    // M business unit
        $record .= $constant["DPD_SLID"] . ",";   // DPD SLID Number
        //}
        $record .= "" . ",";     //collection depot number not used
        $record .= date("d/m/Y", time()) . ","; // collection set to today!
        $record .= date("h:m", time()) . ",";   // collection set to today!
        $record .= substr($run_number, -5) . ",";  // Transmission number
        $record .= "EDI" . ",";  //software type
        $record .= "" . ",";  //source pathname on client machine
        $record .= $constant["DPD_SHIPPER_COMPANY_NAME"] . ",";
        $record .= "IATA" . ",";
        $record .= "P";
        $record .= "\r\n";
        return $record;
    }

    /**
     * Get footer record string
     *
     * @return string
     */
    private function getFooterRecord($record_count) {
        $record = "69" . ",";   // M record header
        $record .= $record_count . ",";   // total number of shipmetns
        $record .= $record_count;   // total number of shipmetns

        return $record;
    }

    public function removecommas($data) {
        return str_replace(",", " ", $data);
    }

    public function recycledShipment($consignment) {
        $output["STATUS"] = "SUCCESS";
        return $output;
    }

    public function reconciliation_data($headingArr,$carrierId,$relPath, $file2,$new_csv_file_created,$filePath,$batchNumber) {
        $output = [];
//        $carrierObj = new Carrier($carrierId);
//        @$csv_file = $file;
//        if (!empty($csv_file['name'])) {
//            $file_name = $csv_file['name'];
//            $path_parts = pathinfo($file_name);
//            $ext = strtolower($path_parts['extension']);
//            $basename = $path_parts['basename'];
//            if ($ext == 'csv') {
//                $carrierName = str_replace(" ","_",$carrierObj->getCarrier());
                $user = SessionManager::getUser();
                $userId = $user->getId();
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
                        $invoice_number = '';
                        $account_number = '';
                        while (($data = fgetcsv($handle)) !== FALSE) {
                            if($row > 0) {
                                $invoice_number = $data['3'];
                                $account_number = $data['1'];
                            }
                            if($row > 5) {
                                $serviceObj = new ServiceFilter();
                                $serviceObj->addCarrierFilter(      $carrierId);
                                $serviceObj->addFieldFilter('       carrier_service_code',      $data['4'] . $data['6']);
                                $serviceObj = $serviceObj->getColumnList('code');
                                if(!empty($serviceObj)) {
                                    $service_code = $serviceObj[0]->getCode();
                                } else {
                                    $service_code = '';
                                }
                                $agent_reference_number = '';
                                $collection_date = $data['0'];
                                $delivery_country = $data['31'];
                                $mawb = '';
                                $awb = $data['3'];
                                $hawb = $data['11']; //important

                                $service_name = '';
//                                $service_code = ''; //important
                                if(!empty($data['12'])) {
                                    $weight = $data['12'];
                                } else {
                                    $weight = 0;
                                }
                                $vol_weight = '';
                                $length = '';
                                $width = '';
                                $height = '';
                                $number_of_pieces = $data['13'];
                                if(!empty($data['15'])) {
                                    $basic_charges = $data['15'];
                                } else {
                                    $basic_charges = 0;
                                }

                                if(!empty($data['17'])) {
                                    $fuel_charges = $data['17'];
                                } else {
                                    $fuel_charges = 0;
                                }
                                if($data['14'] == 'C' || $data['14'] == 'c') {
                                    if (!empty($basic_charges) || !empty($fuel_charges)) {
                                        $total = ($basic_charges + $fuel_charges);
                                        $vat = ($total * 20) / 100;
                                    } else {
                                        $vat = 0;
                                    }
                                } else {
                                    $vat = 0;
                                }

                                $total_amount =  $fuel_charges + $basic_charges + $vat;
                                $notes = '';

                                $output['invoice_number'] = $invoice_number;
                                $supplierInvoiceFilter = new SupplierInvoicesFilter();
                                $supplierInvoiceFilter->where(['si.invoice_number' => $invoice_number]);
                                $supplierInvoiceFilterObjs = $supplierInvoiceFilter->getList();
                                if(count($supplierInvoiceFilterObjs)) {
                                    $output['status'] = 'error';
                                    $output['message'] = 'This file is already processed';
                                    $output['new_invoice_save'] = 1;
                                    break;
                                }
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
                if($row > 0) {
                    $returnArr['invoice_number'] = $data['3'];
                }
                if($row > 5) {
                    $returnArr['collection_date'] = $data['0'];
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
