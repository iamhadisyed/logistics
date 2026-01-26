<?php

include_classes([
    'ptworoutine.class',
    'ptworoutinefilter.class'
]);

class ptwo implements CarrierService {

    private $pdf;
    private $serviceValues = null;
    private $agentValues = null;
    private $constants = null;
    private $user = null;
    private $country = null;
    private $vabRecordArray = null;
    private $vatRecordArray = null;
    private $brtitaly_VAB_file = null;
    private $brtitaly_VAT_file = null;

    public function __construct() {
        
    }

    public function validation(Consignment $consignment, Services $service, Country $country) {
        $returnOutput = array();
        $output = $this->getSortCode($consignment);
        if($output["STATUS"] == "SUCCESS"){
            $routineCode = $output["MESSAGE"];
            $consignment->setOtherRoutingCode($routineCode);
        }
        else
        {
            if($consignment->getCustomizedServiceId() == '683')
            {
                $consignment->setAgentId('1');
                $consignment->setServiceId('681');                
            }
            else
            {
                $returnOutput[] = $output["MESSAGE"];
            }
        }
        return $returnOutput;
    }

    public function remoteareas($consignment, $carrierObject, $sender) {
        
    }

    public function label($consignment, $labelType = 'pdf', $size = '100x150') {

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
        if ( trim(@$this->constants['PTWO_HUB']) == '' || trim(@$this->constants['PTWO_SENDER_DEPO']) == '') {
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
        $licencePlate = new LicencePlate($licence_plate_id);



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
                    $barcodeNumber = $resultArray["PREFIX"] . sprintf("%07d", $resultArray["RANGE"]) . $resultArray["SUFIX"];
                    $checkdigit = LicencePlate::mod10($barcodeNumber);
                    $licence_plate = $barcodeNumber . $checkdigit;
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
            $page_size = array(80, 150);
            $this->pdf->AddPage("L", $page_size);
            $this->addWayBill($consignment, $parcel, $licence_plate);

           
            $licence_plate_array[$parcel_idx] = $licence_plate;

            $new_page_flag = true;
            ++$parcel_idx;
        }

        $this->pdf->IncludeJS("print();");
        $this->pdf->Output("../_assets/pdf/" . date('Y_m_d') . '/' . $consignment->getId() . ".pdf", "F");
        $output['STATUS'] = 'SUCCESS';
        $output['LABEL'] = date('Y_m_d') . '/' . $consignment->getId() . ".pdf";
        $output['TRACKING_NUMBER'] = $licence_plate_array;
        return $output;
    }

    public function tracking($trackingNumber, $trackBy = 'parcel', $EDI = false) 
    {
        $tracking = new Tracking();
        
        $url = 'https://pmr.promail-logistics.de/WSPromailRoutung/api/Sendung/GetTracking';
        $params = json_encode(array("token" => "8e884d57503996a558dbf811ecfa2c93", "tracking_no" => $trackingNumber));

        $curl = curl_init();
        curl_setopt_array($curl, array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_POSTFIELDS =>$params,
          CURLOPT_HTTPHEADER => array(
              'Content-Type: application/json'
            ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);
        $responseArray = json_decode($response);
        
        if($responseArray->error_code == '')
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
            $trackingDataFilterObj->addFilter("carrier_code not in ('','10')");
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
           
            if ($responseArray->event_id != '' && $entityId > 0) 
            {
                $parcelEntity = new Parcel($entityId);
                $finalStatusCode = $parcelEntity->getParcelStatusCode();

                $DateTime = $responseArray->timestamp;
                $EventCode = $responseArray->event_id;
                $Signatory = '';
                $EventDescription = $responseArray->event;
                $ServiceAreaDescription = $responseArray->location;
                    
                // Dont enter any other event code if it is against 148 Event Code
                if($carrierCodeCarrierReceived == $EventCode)
                {
                   // continue;
                }
                    
                $deliveredArray = array('70','200');  
                    
                ////////////////////// Carrier Received ////////////////////////////////
                if($carrierReceivedCheck == 1 && $EventCode != '10' )
                {
                    $spTrackingStatus = '148';
                    $carrierReceivedCheck = 0 ;                        
                }
                else
                {
                    $spTrackingStatus = PtwoTrackingStatus::getOweStatusCode($EventCode);
                }            
                ////////////////////// Carrier Received ////////////////////////////////

                $result = $tracking->saveTrackPoint($entityId, $trackBy, $DateTime, $trackingNumber, $spTrackingStatus, $ServiceAreaDescription, $EventCode, $EventDescription, $deliveredArray, $finalStatusCode);                                        
                if($result == true)
                {
                    //break;
                }
            }               
            $tracking->saveConsignmentTrackingStatus($trackingNumber, 'PtwoTrackingStatus');  
        }
    }        

    public function sendData($tracking_numbers = array()) {

        $output = [];
        $agentServiceArray = [];
        $consignmentData = new ConsignmentFilter();
        $consignmentData->addFilter("     s.carrier_id= '222'", "servicefilter");
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
                      c.contact, c.address_line_1, c.address_line_2, c.city, c.postcode, c.telephone, c.other_routing_code, routing_code_eur, c.number_pieces, pc.tracking_number, pc.do_tracking_number ");
                    
                    if (count($consignmentShipmentData) > 0) {
                        $consignmentIdArray = [];
                        $carrierId = $this->serviceValues->getCarrierId();
                        $run_number = CarrierDataFileLog::generateRunNumber($carrierId, $agentid, false);
                        $fileRunNo = sprintf("%06d", $run_number);
                        foreach ($consignmentShipmentData as $consignmentItemData) {
                            $this->country = new Country($consignmentItemData->getCountry());
                            $consignmentId = $consignmentItemData->getConsignmentId();
                            $consignmentIdArray[] = $consignmentId;
                            $this->recordArray[] = $this->getShipmentRecord($consignmentItemData, $this->constants[$serviceid], $fileRunNo);
                        }
                        
                        
                        $this->booking_file = "T_".$this->constants[$serviceid]['PTWO_CUSTOMER_CODE'] . "_" . $fileRunNo . ".csv";
                        $carrierDataFileLog = new CarrierDataFileLog();
                        $carrierDataFileLog->setCarrierId($carrierId);
                        $carrierDataFileLog->setAgentId($agentid);
                        $carrierDataFileLog->setFileName($this->booking_file);
                        $carrierDataFileLog->setRunNumber($run_number);
                        $carrierDataFileLog->save();
                        
                        if ($this->sendBookings($this->constants[$serviceid])) {
                            if (!empty($consignmentIdArray)) {

                                echo $sql = "UPDATE consignment SET send_courier_data =1, booked_file_id = '" . $this->booking_file . "' 
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

    private function addWayBill(Consignment $consignment, $parcel, $trackingNo) {

        $output = array();
        
        // Box outer Line
       // $this->pdf->line(1, 1, 149, 1);
        $this->pdf->line(1, 1, 1, 79);
        $this->pdf->line(1, 79, 149, 79);
        $this->pdf->line(149, 1, 149, 79);
        
        // Header Line between Logo
        $this->pdf->line(100, 1, 100, 15);
        
        // Line Under Header
        $this->pdf->line(1, 15, 149, 15);
        
        // Barcode Line
        $this->pdf->line(1, 30, 130, 30);
        
        // Return Address Barcode Line
        $this->pdf->line(130, 15, 130, 79);
        
        // Second Barcode VerticalLine
        $this->pdf->line(110, 30, 110, 79);
        
        $x = 5;
        $y = 5;
        $this->pdf->setFont("helvetica", "B", 25);
        $this->pdf->Text($x + 10, 1, trim("Warensendung")); // Goods
        
        $logo = "../images/logo.jpg";
        if (file_exists($logo)) {
            $this->pdf->image($logo, 105, 0, 35, 13, '', '', '', false, 700);
        }
        
        $style = array(              
                'border' => false,
                'hpadding' => 'auto',
                'vpadding' => 1,
                'fgcolor' => array(0,0,0),
                'bgcolor' => false, //array(255,255,255),
                'text' => false,
                'font' => 'helvetica',
                'fontsize' => 10,
                'stretchtext' => 1                     
        );
        $this->pdf->write1DBarcode($trackingNo, 'I25', $x + 2, $y + 10, 100, 10, 0.5, $style, 'Y');
        
        $this->pdf->StartTransform();
        $this->pdf->Rotate(270, 60, 81);
        $this->pdf->write1DBarcode($trackingNo, 'C128', $x + 2, $y + 10, 50, 10, 0.5, $style, '');
        $this->pdf->StopTransform();
        $this->pdf->StartTransform();
        $this->pdf->Rotate(270, 72, 80);
        $this->pdf->setFont("helvetica", "", 9);
        $this->pdf->Text($x + 5, $y+0, trim("One World Express, One World House")); 
        $this->pdf->Text($x + 5, $y+5, trim("Pump Lane, Hayes, Middlesex, UB3 3NB"));
        $this->pdf->Text($x + 5, $y+10, "             United Kingdom"); 
        $this->pdf->StopTransform();
        
        $routinecode = explode("||", $consignment->getOtherRoutingCode());
        $partner = $routinecode[0];
        $sortinfo = $routinecode[1];
        $logistic = $this->constants["PTWO_SENDER_DEPO"];//"P000";
        $hub = $this->constants["PTWO_HUB"];//2;
        $redresskz = "Z";
        //$partner = $this->constants["PTWO_RECEIVER_DEPO"];//"P003";

        $this->pdf->setFont("helvetica", "B", 15);
        $this->pdf->Text($x + 85, $y + 60,$partner ); // partner $response->logistics
        
        $this->pdf->setFont("helvetica", "", 8);
        $this->pdf->Text($x + 2, $y + 21, $logistic. " " . $hub . " " . $redresskz .  " " . $partner ." " . $sortinfo . " " . $trackingNo);

        $this->pdf->setFont("helvetica", "B", 10);
        $this->pdf->Text($x, $y + 28, "Receiver:");
        $this->pdf->line($x, $y+33, $x+17, $y+33);
        $this->pdf->setFont("helvetica", "", 10);
        $this->pdf->Text($x, $y + 35, trim($consignment->getCompany()));
        $this->pdf->Text($x, $y + 40, trim($consignment->getContact()));
        $this->pdf->Text($x, $y + 45, trim($consignment->getAddressLine1() . " " . str_replace("-", "", $consignment->getAddressLine2()) . " " . str_replace("-", "", $consignment->getAddressLine3())));
        $this->pdf->Text($x, $y + 50, trim($consignment->getPostcode()) . " " . trim($consignment->getCity()));
        $this->pdf->Text($x, $y + 55, trim($this->country->getName()));

        return;
        //$response =  $this->ptwocreateShipment($consignment, $parcel);
        

        //if ($response->error_message == "" && $response->tracking_no != '') {

            /* $trackingNo = $response->tracking_no;
              $otherRoutineCode = $response->sortinfo ."|".$response->logistics ."|".$response->ebub ;
              $consignment->setOtherRoutingCode($otherRoutineCode);
              $consignment->save();
             */

            
            $x = 5;
            $y = 5;
            $this->pdf->setFont("helvetica", "", 8);

            $logo = "../images/logo.jpg";


            if (file_exists($logo)) {
                $fitbox = 'C';
                $fitbox[1] = 'M';
                $this->pdf->image($logo, 90, 10, 35, 25, '', '', '', false, 700, '', false, false, 0, $fitbox, false, false);
            }

            $companyLogo = "../images/ptwo.jpg";
            if (file_exists($companyLogo)) {
                $fitbox = 'C';
                $fitbox[1] = 'M';
                $this->pdf->image($companyLogo, 75, 10, 20, 18, '', '', '', false, 700, '', false, false, 0, $fitbox, false, false);
            }

            $style = "";
            $this->pdf->write1DBarcode($trackingNo, 'I25', $x + 2, $y + 10, '', 8, 0.5, $style, 'Y');
            $this->pdf->StartTransform();
            $this->pdf->Rotate(270, 72, 78);
            $this->pdf->write1DBarcode($trackingNo, 'C128', $x + 2, $y + 10, '', 8, 0.5);
            $this->pdf->setFont("helvetica", "B", 12);
            $this->pdf->Text($x + 10, $y, trim("Warensendung")); // Goods

            $this->pdf->StopTransform();
            
            
            $routinecode = explode("||", $consignment->getOtherRoutingCode());
            $partner = $routinecode[0];
            $sortinfo = $routinecode[1];
            $logistic = $this->constants["PTWO_SENDER_DEPO"];//"P000";
            $hub = $this->constants["PTWO_HUB"];//2;
            $redresskz = "Z";
            //$partner = $this->constants["PTWO_RECEIVER_DEPO"];//"P003";
            
            $this->pdf->setFont("helvetica", "B", 15);
            $this->pdf->Text($x + 100, $y + 50,$partner ); // partner $response->logistics

            $this->pdf->setFont("helvetica", "", 10);
            $this->pdf->Text($x + 2, $y, "One World Express, One World House, Pump Lane, London, UK, UB3 3NB");

            $this->pdf->Text($x + 2, $y + 25, $logistic. " " . $hub . " " . $redresskz .  " " . $partner ." " . $sortinfo . " " . $trackingNo);
            /*$this->pdf->Text($x + 15, $y + 25, $hub); //$response->ehub
            $this->pdf->Text($x + 25, $y + 25, "E+Z");
            $this->pdf->Text($x + 35, $y + 25, $response->logistics); // partner
            $this->pdf->Text($x + 50, $y + 25, $response->sortinfo); // sortinfo
            $this->pdf->Text($x + 80, $y + 25, $response->tracking_no); // shipmentid*/
            //		$this->pdf->Text($x, $y+30, "6431=MA-Items");
            $this->pdf->Text($x, $y + 35, trim($consignment->getContact()));
            $this->pdf->Text($x, $y + 40, trim($consignment->getCompany()));
            $this->pdf->Text($x, $y + 45, trim($consignment->getAddressLine1() . " " . str_replace("-", "", $consignment->getAddressLine2()) . " " . str_replace("-", "", $consignment->getAddressLine3())));
            $this->pdf->Text($x, $y + 50, trim($consignment->getPostcode()) . " " . trim($consignment->getCity()));


            $this->pdf->setFont("helvetica", "B", 15);
            // $this->pdf->Text($x + 100, $y + 60, $registerData["PARTNER"]); // partner
           // $output["STATUS"] = "SUCCESS";
           // $output["TRACKING_NO"] = $response->tracking_no;
        /*} else {
            $output["STATUS"] = "ERROR";
            $output["MESSAGE"] = ($response->error_message != '') ? $response->error_message : "Unable to provide service to this address.";
        }*/
        //return $output;
    }

    private function getSortCode($consignment) {
        $output = array();
        $shortest = -1;
        $postcode = str_replace(" ", "", $consignment->getPostcode());
        $ptworoutine = new pTwoRoutineFilter();
        $ptworoutine->addFieldFilter("postcode", $postcode);
        $postcodeList = $ptworoutine->getList();
        if (count($postcodeList) > 0) {
            $cityArray = array();
            $streetArray = array();
            $city = strtolower($consignment->getCity());
            foreach ($postcodeList as $postcode) {
                $cityArray[$postcode->getId()] = strtolower($postcode->getCity());
                $streetArray[strtolower($postcode->getCity())][$postcode->getId()] = str_replace("-"," ", str_replace(".","", strtolower($postcode->getStreet())));
            }
            $closestcity = $this->closest($city, $cityArray, $shortest, 3);
            
            if ($closestcity != "") {
                $shortest = -1;
                $searchStreet = $streetArray[$closestcity];
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
                $closestStreet = $this->closest($filterStreet, $searchStreet, $shortest, 3);
                if ($closestStreet != "") {
                    foreach ($searchStreet as $key => $value) {
                        if ($value == $closestStreet) {
                            $routineId = $key;
                            $ptwofilter = new pTwoRoutineFilter();
                            $routineRecord = $ptwofilter->checkStreetNumber($number, $routineId);
                            
                            if (count($routineRecord) > 0) {
                                $output["STATUS"] = "SUCCESS";
                                $output["MESSAGE"] = $routineRecord[0]->getLogistik() ."||".$routineRecord[0]->getSortInfo();
                                return $output;
                            }
                            else
                            {
                                $output["STATUS"] = "ERROR";
                                $output["MESSAGE"] = "Unable to provide service at " . $consignment->getAddressLine1() . " Address.";
                                //return $output;
                            }
                        }
                    }
                } else {
                    $output["STATUS"] = "ERROR";
                    $output["MESSAGE"] = "Unable to provide service at " . $consignment->getAddressLine1() . " Address.";
                    return $output;
                }
            } else {
                $output["STATUS"] = "ERROR";
                $output["MESSAGE"] = "Unable to provide service at " . $consignment->getCity() . " City.";
            }
        } else {
            $output["STATUS"] = "ERROR";
            $output["MESSAGE"] = "Unable to provide service at " . $consignment->getPostcode() . " Postcode.";
        }
        return $output;
    }

    private function streetFilter($street){
        $streetArray = array("strasse", "street");
        $filterStreet = str_replace($streetArray, "str", $street);
        $filterStreet = str_replace("-", " ", $filterStreet);
        $filterStreet = str_replace(",", " ", $filterStreet);
        return $filterStreet;
    }
    
    private function closest($input, $words, &$shortest, $sensitivity, $debug = false) {
       
        // loop through words to find the closest
        foreach ($words as $word) {

            // calculate the distance between the input word,
            // and the current word
            $lev = levenshtein($input, $word);
           
            // check for an exact match
            if ($lev == 0) {
                // closest word is this one (exact match)
                $closest = $word;
                $shortest = 0;

                // break out of the loop; we've found an exact match
                break;
            }

            // if this distance is less than the next found shortest
            // distance, OR if a next shortest word has not yet been found
            if ($lev <= $shortest || $shortest < 0) {
                // set the closest match, and shortest distance
                $closest = $word;
                $shortest = $lev;
            }
        }
        if ($shortest <= $sensitivity) {
            return $closest;
        } else {
            return 0;
        }
    }

    private function ptwocreateShipment($consignment, $parcel) {

        $AddressSplit = GenericFunctions::getDoorNumber($consignment->getAddressLine1());

        $number = $AddressSplit['number'];
        if ($number == "") {
            //$number = "1";
            $steet1 = $consignment->getAddressLine1();
        } else {
            $number = $AddressSplit['number'] . $AddressSplit['numberAddition'];
            $steet1 = $AddressSplit['street'];
        }

        $street = $steet1 . " " . str_replace("-", "", $consignment->getAddressLine2()) . " " . str_replace("-", "", $consignment->getAddressLine3());
        $reference = $consignment->getHawb();


        $requestArray = array();
        $requestArray["token"] = $this->constants["PTWO_TOKEN"]; //"e05a3ff09fd3adbc908a6875032b9ca2";//"8e884d57503996a558dbf811ecfa2c93" staging;
        $requestArray["reference"] = $reference;
        $requestArray["reference_no"] = $consignment->getHawb();
        $requestArray["pickup_date"] = "";
        $requestArray["product_code"] = "";
        $requestArray["format"] = "C4";
        $requestArray["width"] = ($parcel->getWidth() * 10);
        $requestArray["height"] = ($parcel->getHeight() * 10);
        $requestArray["thickness"] = ($parcel->getLength() * 10);
        $requestArray["weight"] = ($consignment->getWeight() * 1000);
        $requestArray["readdress"] = "Z";
        $requestArray["country"] = $this->country->getIso3();
        $requestArray["zip"] = $consignment->getPostcode();
        $requestArray["city"] = $consignment->getCity();
        $requestArray["district"] = null;
        $requestArray["street"] = utf8_encode($street);
        $requestArray["street_no"] = $number;
        $requestArray["first_name"] = $consignment->getContact();
        $requestArray["last_name"] = null;
        $requestArray["tracking_no"] = null;
        $requestArray["sortinfo"] = null;
        $requestArray["logistics"] = null;
        $requestArray["ehub"] = null;
        $requestArray["lettertext"] = null;
        $requestArray["error_message"] = null;
        $requestJson = json_encode($requestArray);

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://pmr.promail-logistics.de/WSPromailRoutung/api/Sendung/SetActive", //staging"https://pmr.promail-logistics.de/WSPromailRoutungTest/api/Sendung/SetActive",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => $requestJson,
            CURLOPT_HTTPHEADER => array(
                "Content-Type: application/json"
            ),
        ));

        $jsonResponse = curl_exec($curl);
        $response = json_decode($jsonResponse);
        $consignment->setApiData(print_r($requestJson, true), print_r($response, true), 'createLabelImage');
        curl_close($curl);
        return $response;
    }

  
    
    private function getShipmentRecord($consignment, $constants, $runno){
       $parcel_list = $consignment->getParcels();
       $seprator = ";";
       $record = "";
       
       $record .= $runno . $seprator; //AuftragID
       $record .= $this->AnsiEncode($consignment->getHawb()). $seprator; //Auftragsnr
       $record .= $this->AnsiEncode($consignment->getHawb()) . $seprator; //Auftragsname
       $record .= $constants["PTWO_CUSTOMER_CODE"] . $seprator; //Quelle
       $record .= $constants["PTWO_HUB"] . $seprator; //EHub
       $record .= date("dmY") . $seprator; //Einlieferdatum
       $record .= number_format($parcel_list[0]->getLength()). $seprator; //length
       $record .= number_format($parcel_list[0]->getWidth()) . $seprator; //Width
       $record .= number_format($parcel_list[0]->getHeight()) . $seprator; //height
       $record .= ($consignment->getWeight() *1000) . $seprator; //weight
       $record .= "Z" . $seprator; //RedressKZ
       $record .= $this->AnsiEncode($consignment->getContact()) . $seprator; //Prename
       $record .= "" . $seprator; //Surname
       $record .= "" . $seprator; //Land
       $record .= $consignment->getPostcode() . $seprator; //Postcode
       $record .= $this->AnsiEncode($consignment->getCity()) . $seprator; //City
       $record .= "" . $seprator; //District
       
        $addressLine1 = $consignment->getAddressLine1();
        $AddressSplit = GenericFunctions::getDoorNumber($addressLine1);
        $number = $AddressSplit['number'];        
        $street1 = $AddressSplit['street'];
       
       $record .= $this->AnsiEncode($street1) . $seprator; //Street
       $record .= $number . $seprator; //House Numebr
       $record .= "" . $seprator; //House Numebr
       $record .= "" . $seprator; //Reference
       $record .= $consignment->getAwb() . $seprator; //UPOC
       $routinecode = explode("||", $consignment->getOtherRoutingCode());
       $receiverDepo = $routinecode[0];
       $sortinfo = $routinecode[1];
       $routineInfo = $constants["PTWO_SENDER_DEPO"]. " " . $constants["PTWO_HUB"] . " " . "Z" .  " " . $receiverDepo ." " . $sortinfo . " " . $consignment->getAwb() . " " . $runno;
               
       $record .= $routineInfo . $seprator; //UPOC
       $record .= $receiverDepo . $seprator ; //UPOC
       $record .= $sortinfo ; //SORT INFO
       $record .= "\r\n";
       return $record;
    }
    
    private function sendBookings($ftpConstants) {
        require_once(SETTING_DIR_REMOTE . "includes/3rdparty/Net/SFTP.php");
        if (sizeof($this->recordArray) > 0) {
            $path = SETTING_DIR_ASSETS . "data_send/ptwo_booking/" . date("Y_m_d") . "/";
            if (!file_exists($path))
                @mkdir($path, 0777, TRUE);

            $file_path = $path . $this->booking_file;
            chmod($path, 0777);

            // create file
            $file_handle = @fopen($file_path, 'w');
            foreach ($this->recordArray as $record) {
                fwrite($file_handle, $record);
            }
            // close file
            fclose($file_handle);

            
            if (isset($ftpConstants['PTWO_FTP_SITE']) && trim($ftpConstants['PTWO_FTP_SITE']) != '') {
                $SETTING_FTP_USER = $ftpConstants['PTWO_FTP_USER'];
                $SETTING_FTP_PASSWORD = $ftpConstants['PTWO_FTP_PASSWORD'];
                $SETTING_FTP_SITE = $ftpConstants['PTWO_FTP_SITE'];

                $ssh = new Net_SFTP($SETTING_FTP_SITE, 22222);
                if (!$ssh->login($SETTING_FTP_USER, $SETTING_FTP_PASSWORD)) {
                    mail("itsupport@oneworldexpress.com", "PTWO LOGIN FAILED", "PTWO LOGIN FAILED" . $this->booking_file);
                }
                else
                {
                    echo "login successfully";
                }
                $remotefile = "/Import/" . $this->booking_file ;
                $isUpload = $ssh->put($remotefile, $file_path, NET_SFTP_LOCAL_FILE);
                     


                $this->link_file = NULL;
                $this->recordArray = NULL;
                return $isUpload;
            } 
        }
    }
    
    private function AnsiEncode($data) {
        $fld = "";
        $fld = iconv( mb_detect_encoding( $data ), 'Windows-1252//TRANSLIT', $data );
        return $fld;
    }

    public function recycledShipment($consignment) {
        $output["STATUS"] = "SUCCESS";
        return $output;
    }

}
