<?php
include_classes([
    'checkdigit.class',
    'carrierdatafilelog.class',
    'carrierdatafilelogfilter.class' 
    ]);
class DPDNL implements CarrierService {

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

        if (Consignment::getServiceIntAvailibility($country->getIso(), $consignment->getPostcode(), "dpd_classic_deport") == "") {
            $returnOutput[] = "The service is not available for this postcode " . $consignment->getPostcode();
            return $returnOutput;
        }
        if ($country->getIso() == 'NL')
            $routing_postcode = substr($consignment->getPostcode(), 0, 4);
        else
            $routing_postcode = $consignment->getPostcode();
        if ($country->getIso() == "IE") {
            $consignment->setRoutingCodeEur($consignment->find3DigitServiceCode("11"));
        } else {

            $consignment->setOtherRoutingCode(Consignment::findRoutingCode($country->getIso(), $routing_postcode));
            $consignment->setRoutingCodeEur(Consignment::findCountrycodesRouting($country->getName()));
        }
    }

    public function remoteareas($consignment, $carrierObject, $sender) {
        
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
        $pdf = new PdfBase(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $this->pdf = $pdf;
        // Generate label for each parecel
        foreach ($parcel_list as $parcel) {
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
            preg_match_all('!\d+!', $serviceCode, $matches);
            $functionName = str_replace($matches[0], '', $serviceCode);
            $this->$functionName($consignment, $parcel_idx, $parcel_count, $licence_plate);
            ++$parcel_idx;
        }
        $comma_separated_tracking_numbers = implode($licence_plate_array, ",");
        $this->pdf->IncludeJS("print();");
        $this->pdf->Output("../_assets/pdf/" . date('Y_m_d') . '/' . $consignment->getId() . ".pdf", "F");
        $output['STATUS'] = 'SUCCESS';
        $output['LABEL'] = date('Y_m_d') . '/' . $consignment->getId() . ".pdf";
        $output['TRACKING_NUMBER'] = $licence_plate_array;
        return $output;
    }

    // dpd DE and dpd NL tracking are coming from same URL 

   public function tracking($trackingNumber, $trackBy = 'parcel', $EDI = false) 
    {
        require_once("../includes/labels/dpddetrackingstatus.class.php");
        $tracking = new Tracking();
        $deliveredArray = array('Parcel delivered');
        
        $provider = "dpd";
	$response = file_get_contents("http://www.track-parcel.co.uk/wp-content/plugins/delivery/ajax.php?trackingcode=".$trackingNumber."&provider=$provider");	
        
        $eventArray = json_decode($response, true); 			
	$trackingResponse = $eventArray['status_table'];
        
        if (count($trackingResponse) > 0) 
        {
            $entityId = 0;
            if ($trackBy == 'parcel') 
            {
                $parcelObj = new ParcelFilter();
                $parcelObj->addTrackingNumberFilter($trackingNumber);
                $parcelDataArray = $parcelObj->getColumnList('p.id, p.tracking_number, p.consignment_id');
                if (count($parcelDataArray) > 0) {
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
            $trackingDataFilterObj->addFilter("carrier_code not in ('')");
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
            
            if ($entityId > 0) 
            {
                $parcelEntity = new Parcel($entityId);
                $finalStatusCode = $parcelEntity->getParcelStatusCode();
                
                foreach ($trackingResponse as $event) 
                {
                    $Date               = $event[0];
                    $dateTimeArr        = explode(" ", $Date);		

                    $date = $dateTimeArr[0];
                    $time = $dateTimeArr[1];	
                    $DateTime = date('Y-m-d G:i', strtotime(@$date . " " . @$time));
                    
                                         
                    $EventCode              = $event[2];
                    $EventDescription       = $event[2];
                    $ServiceAreaDescription = $event[1];
                    $Signatory              = '';
                    
                    // Dont enter any other event code if it is against 148 Event Code
                    if($carrierCodeCarrierReceived == $EventCode)
                    {
                        continue;
                    }
                        
                    $spTrackingStatus = DPDDETrackingStatus::getOweStatusCode($EventCode);
                    
                    ////////////////////// Carrier Received ////////////////////////////////
                    if($carrierReceivedCheck == 1)
                    {
                        $spTrackingStatus = '148';
                        $carrierReceivedCheck = 0 ;                        
                    }
                    else
                    {
                        $spTrackingStatus = DPDDETrackingStatus::getOweStatusCode($EventCode);
                    }            
                    ////////////////////// Carrier Received ////////////////////////////////
                    
                   $result = $tracking->saveTrackPoint($entityId, $trackBy, $DateTime, $trackingNumber, $spTrackingStatus, $ServiceAreaDescription, $EventCode, $EventDescription, $deliveredArray, $finalStatusCode);                                        
                   if($result == true)
                    {
                        break;
                    }
                }
                $tracking->saveConsignmentTrackingStatus($trackingNumber, 'DPDDETrackingStatus');      
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
                      c.contact, c.address_line_1, c.address_line_2, c.city, c.postcode, c.telephone,c.email, c.other_routing_code, routing_code_eur, c.number_pieces, c.vol_weight ");

                    if (count($consignmentShipmentData) > 0) {
                        $consignmentIdArray = [];
                        foreach ($consignmentShipmentData as $consignmentItemData) {

                            $consignmentId = $consignmentItemData->getConsignmentId();
                            $consignmentIdArray[] = $consignmentId;
                            $carrierId = $consignmentItemData->getCarrierId();
                            $serviceCode = $this->serviceValues->getCode();
                            preg_match_all('!\d+!', $serviceCode, $matches);
                            $functionName = "consignmentData" . str_replace($matches[0], '', $serviceCode);

                            $this->record_array[] = $this->$functionName($consignmentItemData, $this->constants[$serviceid]);
                        }
                        // set file name
                        // - save new file temporarily in database to get ID for use as run number
                        $carrierId = $this->serviceValues->getCarrierId();
                        $agentid = trim($agentServiceArray['agent'][$key]);

                        $run_number = CarrierDataFileLog::generateRunNumber($carrierId, $agentid, $dateChecked = false);
                        $selectedTime = date("H:i:s");
                        $endTime = strtotime("+10 minutes", strtotime($selectedTime));
                        $this->booking_file = "MPSEXPDATA_" . $this->constants[$serviceid]["DPD_NL_DELIS_ID"] . "_CUST_" . $this->constants[$serviceid]["DPD_IDENTIFICATION"] . "_D" . date("Ymd") . "T" . date('his', $endTime);

                        $carrierDataFileLog = new CarrierDataFileLog();
                        $carrierDataFileLog->setCarrierId($carrierId);
                        $carrierDataFileLog->setAgentId($agentid);
                        $carrierDataFileLog->setFileName($this->booking_file);
                        $carrierDataFileLog->setRunNumber($run_number);
                        $carrierDataFileLog->save();


                        if ($this->sendBookings($this->constants[$serviceid], $run_number, $serviceCode)) {
                            if (!empty($consignmentIdArray)) {

                                $sql = "UPDATE consignment SET booked_file_id = '" . $this->booking_file . "' 
                                        WHERE id IN (" . implode("','", $consignmentIdArray) . ")  AND id <> '0' 
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

    private function STDPDNL(Consignment $consignment, $parcel_idx, $parcel_count, $licence_plate) {
        $page_size = array(146, 105);
        $this->pdf->AddPage("P", $page_size);

        $this->pdf->line(0, 44, 72, 44);  // 1st horizontal half line
        $this->pdf->line(72, 15, 72, 60);
        $this->pdf->line(90, 15, 90, 60);
        $this->pdf->line(57, 44, 57, 60);
        $this->pdf->line(0, 13, 110, 13); //2nd horizontal full line
        $this->pdf->line(0, 60, 110, 60); //2nd horizontal full line
        $this->pdf->line(0, 65, 110, 65); //2nd horizontal full line
        $this->pdf->line(0, 78, 110, 78); //2nd horizontal full line

        $this->pdf->setFont("helvetica", "L", 7);
        $this->pdf->Text(3, 3, "Schade die aan de buitenzijde niet zichtbaar is, moet binnen 7 dagen");
        $this->pdf->Text(3, 6, "na uitlevering schriftetijk aan DPD gemeld worden. / Notification on damage which is not");
        $this->pdf->Text(3, 9, "  recognisable from the outside  has to be submitted to DPD within 7 days in writing.");
        $this->pdf->setFont("helvetica", "L", 12.5);
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




        $this->pdf->Text(3, 14, utf8_encode($company));
        $this->pdf->Text(3, 18, utf8_encode($address1));
        $this->pdf->Text(3, 22, utf8_encode($address2));
        $this->pdf->Text(3, 26, utf8_encode($address3));
        $this->pdf->Text(3, 30, utf8_encode($consignment->getCity()));
        $this->pdf->Text(3, 34, utf8_encode($consignment->getCountry()));
        $this->pdf->setFont("helvetica", "L", 7);
        $this->pdf->Text(3, 44.5, "Contact");
        $this->pdf->Text(3, 47.5, "Phone  ");
        $this->pdf->Text(3, 50.5, "Info   ");
        $this->pdf->Text(3, 53.5, "Hawb   ");
        $this->pdf->Text(3, 56.5, "Ref   ");
        $this->pdf->setFont("helvetica", "L", 7);

        $this->pdf->Text(14, 44.5, utf8_encode($consignment->getContact()));
        $this->pdf->Text(14, 47.5, utf8_encode($consignment->getTelephone()));
        $this->pdf->Text(14, 50.5, utf8_encode($consignment->getNotes()));
        $this->pdf->Text(14, 53.5, utf8_encode($consignment->getHawb()));
        $this->pdf->Text(14, 56.5, utf8_encode($consignment->getReference()));


        $this->pdf->setFont("helvetica", "L", 8);
        $this->pdf->Text(57, 45, "Packages");
        $this->pdf->Text(57, 52, "Weight");

        $this->pdf->setFont("helvetica", "B", 8);
        $this->pdf->Text(57, 48.5, ($parcel_idx + 1) . " of " . $parcel_count);
        $this->pdf->Text(57, 55, $consignment->getWeight() . " KG");

        $this->pdf->setFont("helvetica", "B", 10);
        $this->pdf->Text(32, 60, "DPD CLASSIC SERVICE");
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
        $dpd_classic_osort = $parameters[6];
        $dpd_classic_dsort = $parameters[7];

        $service = "D";
        $depot = $dpd_classic_deport;
        $osort = $dpd_classic_osort;
        $dsort = $dpd_classic_dsort;
        // echo  $consignment->getOtherRoutingcode(); exit;
//        $parentid = $user->getParentId();
//        if ($parentid == '66' || $parentid == '175' || $parentid == '176' || $parentid == '582') {
//            $digit3servicecode = "101";
//        } else {
        if ($consignment->getWeight() > 3)
            $digit3servicecode = "101";
        else
            $digit3servicecode = "136";
        //}
        $this->pdf->setFont("helvetica", "B", 17);
        $this->pdf->Text(97, 81, $service);


        if (strtoupper($this->country->getIso()) == "NL") {
            $pos = strpos($consignment->getPostcode(), "-");
            if ($pos > 0) {
                $arrpostcode = explode("-", $consignment->getPostcode());
                $postcode = $arrpostcode[1] . $arrpostcode[0];
                $consignment->setPostCode($postcode);
            }
        }
        $this->pdf->setFont("helvetica", "L", 15);
        $this->pdf->Text(3, 38, $consignment->getPostcode());
        $post_code = str_replace(" ", "", $consignment->getPostcode());
        $this->pdf->setFont("helvetica", "L", 7);
        $this->pdf->Text(90, 89, "Service");
        $this->pdf->Text(3, 89, "Track");
        $this->pdf->setFont("helvetica", "L", 9);
        $this->pdf->Text(28, 106, Date("d/m/Y h:m") . "  Routing Ver: 20170103");
        $this->pdf->setFont("helvetica", "L", 9);
        $this->pdf->Text(35, 99, $digit3servicecode . "-" . $this->country->getIso() . "- $post_code");
        $this->pdf->setFont("helvetica", "L", 25);
        $this->pdf->Text(25, 89, $this->country->getIso() . "-" . $depot);
        if ($consignment->getWeight() < 3) {
            $this->pdf->Image("../images/cross.png", 2, 92, 7, 10);
        }
        $this->pdf->Text(7, 102, $osort);
        $this->pdf->Text(75, 96, $dsort);

        $post_code = str_replace("-", "", $post_code);
        $post_code = str_pad(trim($post_code), 7, "0", STR_PAD_LEFT);  //postcode

        $post_code = strtoupper($post_code);

        $origin_identification = trim(@$this->constants['DPD_IDENTIFICATION']); //self::DPD_IDENTIFICATION_NL;  //original identification
        $account_identification = trim(@$this->constants['DPD_SLID']); //self::DPD_SLID_NL;  //original identification
        // parcel number
        $parcel_number_first_four = substr($licence_plate, 8, 4);  // first 4 parcel number
        $parcel_number_last_two = substr($licence_plate, 12, 4);  // last two parcel number

        $trackingnumbertext = $licence_plate;
        $barcodetrackingnumbertext = $post_code . $licence_plate . $digit3servicecode . $this->country->getNumCode();
        $first4number = substr($barcodetrackingnumbertext, 0, 4);
        $second4number = substr($barcodetrackingnumbertext, 4, 4);
        $third4number = substr($barcodetrackingnumbertext, 8, 4);
        $fourth4number = substr($barcodetrackingnumbertext, 12, 4);
        $fifth4number = substr($barcodetrackingnumbertext, 16, 4);
        $sixth4number = substr($barcodetrackingnumbertext, 20, 4);
        $last3number = substr($barcodetrackingnumbertext, 24, 3);
        $barcodetext = "%" . $post_code . $licence_plate . $digit3servicecode . $this->country->getNumCode();




        $digit14servicecode = ISO7064Decoder::NumberToChar(ISO7064Decoder::calculateCheckDigit($trackingnumbertext));
        $digit28servicecode = ISO7064Decoder::NumberToChar(ISO7064Decoder::calculateCheckDigit($barcodetrackingnumbertext));

        $this->pdf->setFont("helvetica", "B", 25);
        $this->pdf->Text(3, 79, $origin_identification);
        $this->pdf->setFont("helvetica", "L", 18);
        $this->pdf->Text(25, 81.5, "$account_identification $parcel_number_first_four  $parcel_number_last_two");
        $this->pdf->setFont("helvetica", "L", 11);
        $this->pdf->Text(67.5, 84, $digit14servicecode);

        $style['position'] = 'C';
        $this->pdf->write1DBarcode($barcodetext, 'C128', 0, 111, '', 25, 0.5, $style, 'Y');



        $this->pdf->setFont("helvetica", "L", 10);
        $this->pdf->Text(22, 137, "$first4number $second4number $third4number $fourth4number $fifth4number $sixth4number $last3number$digit28servicecode");

        $this->pdf->StartTransform();
        $this->pdf->Rotate(270, 80, 70);
        $this->pdf->setFont("helvetica", "L", 7);
        $this->pdf->Text(23, 60, "Sender Account : ");
        $this->pdf->Text(23, 78, "Delivery Address");
        $image = realpath("../images/dpd-logo.jpg");
        $this->pdf->image($image, 40, 45, 30);
        $this->pdf->setFont("helvetica", "L", 7);


        $this->pdf->Text(23, 63, trim(@$this->constants['DPD_SHIPPER_CONTACT'])); //"ORANGE POST N.V"
        $this->pdf->Text(23, 66, trim(@$this->constants['DPD_SHIPPER_ADDRESS_LINE_1'])); //"WESTERDREEF 5K"
        $this->pdf->Text(23, 69, trim(@$this->constants['DPD_SHIPPER_CITY']));
        $this->pdf->Text(23, 72, trim(@$this->constants['DPD_SHIPPER_POSTCODE'])); //"2152CS"
        $this->pdf->Text(23, 75, "Phone " . trim(@$this->constants['DPD_SHIPPER_TELEPHONE'])); //"085 273 60 65"
        $this->pdf->Text(23, 48, "Depot " . trim(@$this->constants['DPD_NL_DEPOT_NUMBER']));
        $this->pdf->Text(23, 52, trim(@$this->constants['DPD_NL_DEPOT_ADDRESSLINE1'])); //"cacaoweg 20-50"
        $depotCountry = trim(@$this->constants['DPD_NL_DEPOT_COUNTRY']);
        if (is_numeric($depotCountry)) {
            $depotCountryObj = new Country($depotCountry);
            $countryIso = $depotCountryObj->getIso();
        } else
            $countryIso = $depotCountry;

        $this->pdf->Text(23, 56, trim(@$this->constants['DPD_NL_DEPOT_POSTCODE']) . "-" . $countryIso); //"NL-1047BM-Amsterdam"


        $this->pdf->StopTransform();
    }

    private function sendBookings($ftpConstants, $runNumber, $serviceCode) {
        preg_match_all('!\d+!', $serviceCode, $matches);
        $serviceNameFolder = str_replace($matches[0], '', $serviceCode);
        $headerfunctionName = "getHeaderRecord" . $serviceNameFolder;
        $footerfunctionName = "getFooterRecord" . $serviceNameFolder;
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
            fwrite($file_handle, $this->$footerfunctionName($runNumber));

            // close file
            fclose($file_handle);

            $file_pathsem = $file_path . ".sem";
            $file_handle3 = fopen($file_pathsem, 'w');

            fclose($file_handle3);



            if (isset($ftpConstants['DPD_FTP_SITE']) && trim($ftpConstants['DPD_FTP_SITE']) != '') {
                $SETTING_FTP_USER_DPD = $ftpConstants['DPD_FTP_USER'];
                $SETTING_FTP_PASSWORD_DPD = $ftpConstants['DPD_FTP_PASSWORD'];
                $SETTING_FTP_SITE_DPD = $ftpConstants['DPD_FTP_SITE'];


                // FTP file to Yodel
                // - default port#

                $ftp_object = new FTPfile($SETTING_FTP_USER_DPD, $SETTING_FTP_PASSWORD_DPD, $SETTING_FTP_SITE_DPD);

                $remote_file_path = "./" . $this->booking_file;
                $isUpload = $ftp_object->put($remote_file_path, $file_path, FTP_BINARY);

                $ftp_object = new FTPfile($SETTING_FTP_USER_DPD, $SETTING_FTP_PASSWORD_DPD, $SETTING_FTP_SITE_DPD);
                $remote_file_path = "./" . $this->booking_file . ".sem";
                $ftp_object->put($remote_file_path, $file_pathsem, FTP_ASCII);

                $this->booking_file = NULL;
                $this->record_array = NULL;
                if ($isUpload === false) {
                    mail("itsupport@oneworldexpress.com", "DPDNL LOGIN FAILED", "DPDNL LOGIN FAILED" . $this->booking_file);
                }
            }

            return $isUpload;
        }
    }

    private function consignmentDataSTDPDNL(Consignment $consignment, $constants) {
        $this->country = new Country($consignment->getCountryId());
        if ($consignment->getContact() <> '')
            $contact = utf8_encode($consignment->getContact());
        else
            $contact = utf8_encode($consignment->getCompany());
        //remove incorrect characters from string.
        $postcode = preg_replace('/[^A-Za-z0-9]/', "", $consignment->getPostcode());
        //get a total of 35 characters
        $address = substr(@utf8_encode($consignment->getAddressLine1()) . " " . @utf8_encode($consignment->getAddressLine2()), 0, 35);


        $vol_weight = $consignment->GetVolWeightOfConsignment($consignment->getId(), $this->serviceValues->getVolumetricDenominator());
        $parcel_weight = number_format($consignment->getWeight(), 1);

        if ($parcel_weight > $vol_weight["vol_weight"]) {
            $weight = $parcel_weight;
        } else {
            $weight = $vol_weight;
        }


        $record = 'HEADER;';

        if (trim($consignment->getTelephone()) != "") {
            $record .= 'B2C' . $consignment->getAWB() . date('Ymd') . ';';
        } else {
            $record .= 'MPS' . $consignment->getAWB() . date('Ymd') . ';';
        }


        $record .= ';'; //1 = no compelte delivery / 2 = complete delivery
        $record .= ';';   //create complete delivery / fix date delivery label for pick-up 0 = no \ 1 = yes
        $record .= $consignment->getHawb() . ";";     // sender's reference1 O
        $record .= $consignment->getReference() . ';';     // sender's reference2 O
        $record .= ';';     // sender's reference3 O
        $record .= ';';     // sender's reference4 O
        $record .= $consignment->getNumberPieces() . ';'; //number of parcels per consignment
        $record .= ';'; //Volume per consignment in CM3 O
        $weightRound = $weight * 100;
        $record .= $weightRound . ';'; //SHIPMENT WEIGHT O
        $record .= $constants["DPD_IDENTIFICATION"] . ";"; //Sending Depot Depot 0516
        $record .= $constants["DPD_CUSTOMER_NO"] . ';'; //Customer Number 066456
        $record .= ';'; //Customer Number SUBID O
        $record .= $constants["DPD_NL_DELIS_ID"] . ';'; //DELIS-UserID oneworld51
        $record .= $constants["DPD_SHIPPER_CONTACT"] . ';'; //'ORANGE POST N.V;'; //sender name 1
        $record .= ';'; //sender name 2 O
        $record .= $constants["DPD_SHIPPER_ADDRESS_LINE_1"] . ';'; //'WESTERDREEF;'; //SENDER STREET
        $record .= ';'; //HOUSE NUMBER O
        $record .= '528;';
        $record .= $constants["DPD_SHIPPER_POSTCODE"] . ';'; //'2152CS;';
        $record .= $constants["DPD_SHIPPER_CITY"] . ';'; //'NIEUW-VENNEP;';
        $record .= ';'; //SENDER CONTACT PERSON O
        $record .= ';'; //SENDER TELEPHONE O
        $record .= ';'; //SENDER FAX O
        $record .= ';'; //SENDER EMAIL O
        $record .= ';'; //SENDER COMMENTS O
        $record .= ';'; //SENDER ILN O
        $record .= ';'; //CONSIGNMENT ENTRY DATE O
        $record .= ';'; //ENTRY TIME O
        $record .= ';'; //USERID WHO MADE THE ENTRY O
        $record .= 'P;'; //HARDWAREID FLAG
        $record .= ';'; //RECIPIENT DEPOT O
        $record .= ';'; //ESORT O
        $record .= ';'; //CUSTOMER NUMBER O
        $record .= $contact . ';'; //R-NAME1
        $record .= ';'; //R-NAME2 O
        $record .= $address . ";"; //R-STREET
        //if ($consignment->getAddressLine3() != "")
        //$record .= substr($consignment->getAddressLine3(),0,7). ";"; //NUMBER O 
        //else
        $record .= "-;"; //NUMBER O


        $record .= sprintf('%03d', $this->country->getNumcode()) . ';'; //ISO 3 CODE NEED UPDATING.

        $record .= ';'; //state Odfg
        $record .= $postcode . ';'; //POSTCODE;
        $record .= $consignment->getCity() . ';'; //city;
        $record .= ';'; //rCONTACT O
        $record .= $consignment->getTelephone() . ";"; //RTELEPHONE O
        $record .= ';'; //RFAX O
        $record .= ';'; //REMAIL O
        $record .= ';'; //COMMENTS O

        $record .= ';'; //ILN NUMBER O

        if ($weight > 3)
            $record .= '101;'; //SHIPMENT TYPE???
        else
            $record .= '136;'; //SHIPMENT TYPE???


        $record .= date('Ymd') . ';'; //DATE WHEN SHIPMENT O
        $record .= ';'; //EXPECTED TRANSFER TO SYSTEM O
        $record .= ';'; //LATE PICK UP
        $record .= ';'; //MODIFIED INSTRUCTION FLAG
        $record .= ';'; //POD
        $record .= ';'; //???
        $record .= "\r\nPARCEL;"; //PARCEL START
        if ($consignment->getTelephone() != '') {
            $record .= 'B2C' . $consignment->getAWB() . date('Ymd') . ';'; //MPSID
        } else {
            $record .= 'MPS' . $consignment->getAWB() . date('Ymd') . ';'; //MPSID
        }

        $record .= $consignment->getAWB() . ';'; //PARCEL NUMBER
        $record .= ';'; //CUSTOMER REFERENCE NUMBER 1 O
        $record .= ';'; //CUSTOMER REFERENCE NUMBER 2 O
        $record .= ';'; //CUSTOMER REFERENCE NUMBER 3 O
        $record .= ';'; //CUSTOMER REFERENCE NUMBER 4 O
        $record .= $constants["DPD_NL_DELIS_ID"] . ';'; //oneworld51;'; //DELIS-USERID??

        if ($consignment->getTelephone() != '') {
            if ($weight > 3)
                $record .= '327;'; //SHIPMENT TYPE???
            else
                $record .= '328;'; //SHIPMENT TYPE???
        }
        else {
            if ($weight > 3)
                $record .= '101;'; //SHIPMENT TYPE???
            else
                $record .= '136;'; //SHIPMENT TYPE???
        }

        $record .= ';'; //VOLUME O
        $record .= ';'; //PARCEL WEIGHT O
        $record .= ';'; //INSURANCE O
        $record .= ';'; //INCREASED INSURANCE
        $record .= ';'; //CURRENCY CODE O
        $record .= ';'; //PARCEL CONTENTS O
        // $record .= ';';//limited quantities hazardous goods O
        //MSG Start HERE
        if ($consignment->getTelephone() != '') {

            $record .= "\r\nMSG;"; //MESSAGE START
            $record .= 'B2C' . $consignment->getAWB() . date('Ymd') . ';'; //MPSID
            $record .= "3" . ';'; //1-email, 3-MSG
            $record .= $consignment->getTelephone() . ';'; //1-email, 3-MSGsd
            $record .= "904" . ';'; //IDM Notification
            $record .= "EN" . ';'; //Language
            $record .= ';';
            $record .= ';';
            $record .= ';';
            $record .= ';';
            $record .= ';';
            $record .= ';';
            $record .= ';';
            $record .= ';';
            $record .= ';';
            $record .= ';';
            $record .= ';';
            $record .= ';';
            $record .= ';';
            $record .= ';';
            $record .= ';';
            $record .= ';';
        }






        $record .= "\r\n";
        return $record;
    }

    /**
     * Get Header Record string
     *
     * @return string
     */
    private function getHeaderRecordSTDPDNL($run_number, $constant) {
        //filename convention for naming of the file. MPSEXPDATA_jkkoolen_CUST_0522_D20140408T125943


        $record = '#FILE;oneworld51;' . $constant["DPD_IDENTIFICATION"] . ';' . date('Ymd') . ';' . date('hms') . ';' . $run_number . ';' . "\r\n";
        $record .= "#DEF;MPSEXP:HEADER;MPSID;MPSCOMP;MPSCOMPLBL;MPSCREF1;MPSCREF2;MPSCREF3;MPSCREF4;MPSCOUNT;MPSVOLUME;MPSWEIGHT;SDEPOT;SCUSTID;SCUSTSUBID;DELISUSR;SNAME1;SNAME2;SSTREET;SHOUSENO;SCOUNTRYN;SPOSTAL;SCITY;SCONTACT;SPHONE;SFAX;SEMAIL;SCOMMENT;SILN;CDATE;CTIME;CUSER;HARDWARE;RDEPOT;ESORT;RCUSTID;RNAME1;RNAME2;RSTREET;RHOUSENO;RCOUNTRYN;RSTATE;RPOSTAL;RCITY;RCONTACT;RPHONE;RFAX;REMAIL;RCOMMENT;RILN;MPSSERVICE;MPSSDATE;MPSSTIME;LATEPICKUP;UMVER;UMVERREF;PODMAN;;
#DEF;MPSEXP:PARCEL;MPSID;PARCELNO;CREF1;CREF2;CREF3;CREF4;DELISUSR;SERVICE;VOLUME;WEIGHT;HINSURE;HINSAMOUNT;HINSCURRENCY;HINSCONTENT;;
#DEF;MPSEXP:COD;MPSID;PARCELNO;NAMOUNT;NCURR;NINKASSO;NPURPOSE;SBKCODE;SBKNAME;SACCOUNT;SACCNAME;IBAN;BIC;;
#DEF;MPSEXP:PICKUP;PTYPE;MPSID;PNAME1;PNAME2;PSTREET;PHOUSENO;PCOUNTRYN;PPOSTAL;PCITY;PCONTACT;PPHONE;PFAX;PEMAIL;PILN;PDATE;PTOUR;PQUANTITY;PDAY;PFROMTIME1;PTOTIME1;PFROMTIME2;PTOTIME2;;
#DEF;MPSEXP:INVOICE;MPSID;INAME1;INAME2;ISTREET;IHOUSENO;ICOUNTRYN;IPOSTAL;ICITY;ICONTACT;IPHONE;IFAX;IEMAIL;IILN;;
#DEF;MPSEXP:PERS;MPSID;PERSDELIVERY;PERSFLOOR;PERSBUILDING;PERSDEPARTMENT;PERSNAME;PERSPHONE;PERSID;ODEPOT;ONAME1;ONAME2;OSTREET;OHOUSENO;OCOUNTRYN;OSTATE;OPOSTAL;OCITY;OPHONE;OEMAIL;OILN;;
#DEF;MPSEXP:MSG;MPSID;MSGTYPE1;MSGVALUE1;MSGRULE1;MSGLANG1;MSGTYPE2;MSGVALUE2;MSGRULE2;MSGLANG2;MSGTYPE3;MSGVALUE3;MSGRULE3;MSGLANG3;MSGTYPE4;MSGVALUE4;MSGRULE4;MSGLANG4;MSGTYPE5;MSGVALUE5;MSGRULE5;MSGLANG5;;
#DEF;MPSEXP:SWAP;MPSID;PARCELNO;PARCELNOBACK;SERVICEBACK;;
#DEF;MPSEXP:INTER;MPSID;PARCELNO;PARCELTYPE;CAMOUNT;CURRENCY;CTERMS;CCONTENT;CTARIF;;
#DEF;MPSEXP:DELIVERY;MPSID;PODINFO1;PODINFO2;PODINFO3;PODINFO4;PODINFO5;CUSTOMERINFO1;CUSTOMERINFO2;CUSTOMERINFO3;CUSTOMERINFO4;CUSTOMERINFO5;DELIVERYDAY;DELIVERYDATE_FROM;DELIVERYDATE_TO;TIMEFRAME_FROM;TIMEFRAME_TO;;
";
        $record .= "\r\n";
        return $record;
    }

    /**
     * Get footer record string
     *
     * @return string
     */
    private function getFooterRecordSTDPDNL($runNumber) {
        $record = "#END;$runNumber;";   // M footer

        return $record;
    }

    public function removecommas($data) {
        return str_replace(",", " ", $data);
    }

    public function recycledShipment($consignment) {
        $output["STATUS"] = "SUCCESS";
        return $output;
    }

}
