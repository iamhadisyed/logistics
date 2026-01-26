<?php
include_classes([
    'swedenposttrackingstatus.class'
],'labels');

class SwedenPost implements CarrierService {

    private $pdf;
    private $serviceValues = null;
    private $constants = null;
    private $user = null;
    private $userAccount = null;
    private $country = null;

    public function __construct() {
        
    }

    public function validation(Consignment $consignment, Services $service, Country $country) {
        
    }

    public function remoteareas($consignment, $carrierObject, $sender) {
        
    }

    public function label($consignment, $labelType = 'pdf', $size = '100x150') {

        $output = array();
        $this->user = SessionManager::getUser();
        $this->userAccount = new CustomerAccount($this->user->getUserAccountId());
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

        if (trim(@$this->constants['SWEDENPOST_CUSTOMER_NO']) == '' && in_array($this->serviceValues->getCode(), array("STSWNREGM", 'STSPINTL2')) ) {
            $output['STATUS'] = 'ERROR';
            $output['MESSAGE'] = "Please check service constants. Please contact to itsupport@oneworldexpress.com";
            return $output;
        }


        /*
         *  Get Tracking Number ranges
         */


        $serviceRangeMappingFilter = new ServiceRangeMappingFilter();
        $serviceRangeMappingFilter->addFilter("service_id = '" . $consignment->getServiceId() . "' AND agent_id = '" . $consignment->getAgentId() . "' ");
        $serviceRange = $serviceRangeMappingFilter->getList();
        
        $licenceplateArray = array();
        if (count($serviceRange) > 0) {
            foreach ($serviceRange as $serviceRan)
            {
                $licenceplateArray[] = (int) $serviceRan->getLicencePlateId();
            }
        }        
        else {
             $output['STATUS'] = 'ERROR';
            $output['MESSAGE'] = "This service does not have tracking number range. Please contact to itsupport@oneworldexpress.com";
            return $output;
        }
        if(count($licenceplateArray) > 0)
        {
            $licenceplate_id = implode(",", $licenceplateArray);
            $licenceplate = new LicencePlateFilter();
            $licenceplate->addFilter("id in (".$licenceplate_id.") and FIND_IN_SET('".$consignment->getCountryId()."', country_list)");
            $licenceplateList = $licenceplate->getList();
            if(count($licenceplateList) > 0)
            {
                $licence_plate_id = $licenceplateList[0]->getId();
            }
            else
            {
                $licence_plate_id = (int) $serviceRange[0]->getLicencePlateId();
            }
            
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
                $resultArray = LicencePlate::getLicencePlateNumber($licence_plate_id, $consignment->getCountryId());
                if (trim($resultArray['STATUS']) == 'ERROR')
                    return $resultArray;
                else {
                    $checkdigit = LicencePlate::mod11($resultArray["RANGE"]);
                    $licence_plate = $resultArray["PREFIX"] . $resultArray["RANGE"] . $checkdigit . $resultArray["SUFIX"];
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

    public function tracking($trackingNumber, $trackBy = 'parcel', $EDI = false) {
        $response = file_get_contents("https://api2.postnord.com/rest/shipment/v4/trackandtrace/findByIdentifier.json?apikey=eaa6ebabb9b38f54099b14e3ee25161b&callback=angular.callbacks._7&id=$trackingNumber&locale=en");
        $response = str_replace("/**/angular.callbacks._7(", "", $response);
        $response = str_replace(");", "", $response);
        //$response = "[" . $response . "]";
        $resultArray = json_decode($response, true);
        $TrackingInformationResponse = $resultArray['TrackingInformationResponse'];
        $shipments = $TrackingInformationResponse['shipments'];
        if (!empty($shipments)) {
            foreach ($shipments as $shipment) {
                $items = $shipment['items'];
                foreach ($items as $item) {
                    $trackingNo = $item['itemId'];
                    $events = $item['events'];

                    $entityId = 0;
                    if ($trackBy == 'parcel') {
                        $parcelObj = new ParcelFilter();
                        $parcelObj->addTrackingNumberFilter($trackingNo);
                        $parcelDataArray = $parcelObj->getColumnList('p.id, p.tracking_number, p.consignment_id');
                        if (count($parcelDataArray) > 0) {
                            $parcelData = $parcelDataArray[0];
                            $entityId = $parcelData->getId();
                        }
                    } else if ($trackBy == 'shipment') {
                        $shipmenObj = new ConsignmentFilter();
                        $shipmenObj->addawbFilter($trackingNo);
                        $shipmentDataArray = $shipmenObj->getColumnList('c.awb');
                        if (count($shipmentDataArray) > 0) {
                            $shipmentData = $shipmentDataArray[0];
                            $entityId = $shipmentData->getId();
                        }
                    }
                    //echo "<pre>"; print_r($events); echo "<pre>"; exit;
                    if (count($events) > 0 && $entityId > 0) {
                        foreach ($events as $event) {
                            $DateTime = $event['eventTime'];
                            $EventCode = $event['eventCode'];
                            $EventDescription = (isset($event['eventDescription']) ? $event['eventDescription'] : "");
                            $ServiceAreaCode = (isset($event['location']['countryCode']) ? $event['location']['countryCode'] : "");
                            $ServiceAreaDescription = (isset($event['location']['country']) ? $event['location']['country'] : "");
                            $Signatory = '';
                            $spTrackingStatus = SwedenpostTrackingStatus::getOweStatusCode($EventCode);

                            $trackingDataFilter = new TrackingDataFilter();
                            $trackingDataFilter->addTrackPointExistFilter($trackingNo, $spTrackingStatus, $ServiceAreaDescription, $EventCode, $EventDescription);
                            $trackingDataExistsObj = $trackingDataFilter->getColumnList("t.id");
                            if (count($trackingDataExistsObj) == 0) {
                                $trackingData = [
                                    'user_id' => 0,
                                    'entity_id' => $entityId,
                                    'entity_type' => $trackBy,
                                    'tracking_number' => $trackingNo,
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
                        $trackingDataFilter->addTrackingNumberFilter($trackingNo);
                        $trackingDataFilter->AddOrderByDate(false);
                        $trackingDataObj = $trackingDataFilter->getColumnList('entity_id,entity_type,carrier_code,status_code_id');

                        if (count($trackingDataObj) > 0) {
                            $trackingDataObj = $trackingDataObj[0];
                            $carrierCode = $trackingDataObj->getCarrierCode();
                            $oweTrackingStatusCode = $trackingDataObj->getStatusCodeId();
                            $entityType = $trackingDataObj->getEntityType();
                            $entityId = $trackingDataObj->getEntityId();
                            $consignmentStatusCode = SwedenpostTrackingStatus::getConsignmentStatus($oweTrackingStatusCode);
                            $consignmentId = 0;
                            if ($entityType == 'parcel') {
                                $parcelObj = new Parcel($entityId);
                                $dateTime = date("Y-m-d H:i:s");
                                $parcelObj->setParcelStatusCode($consignmentStatusCode);
                                $parcelObj->setLastTrackingUpdate(strtotime($dateTime));
                                $parcelObj->save();
                                $consignmentId = $parcelObj->getConsignmentId();
                            } else {
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
                            if ($consignmentStatus != '')
                                $ConsignmentObj->setConsignmentStatus($consignmentStatus);
                            $ConsignmentObj->save();
                        }
                    }
                }
            }
        }
    }

    public function sendData($tracking_numbers = array()) {
        $output = [];
        $agentServiceArray = [];
        $consignmentData = new ConsignmentFilter();
        $consignmentData->addFilter("     s.carrier_id= '186'", "servicefilter");
        $consignmentData->addFilter("    ( c.send_courier_data= '0' or c.send_courier_data is null)", "consignmentfilter");
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
                    $consignmentData->addFilter("    ( c.send_courier_data= '0' or c.send_courier_data is null)", "consignmentfilter");
                    if (!empty($tracking_numbers))
                        $consignmentShipmentDataFilter->addFilter("     AND pc.tracking_number in ('" . implode("','", $tracking_numbers) . "') and pc.tracking_number <> ''", "parcelJoinFilter");
                    $consignmentShipmentData = $consignmentShipmentDataFilter->getColumnList(" c.id 'consignment_id', s.carrier_id ,s.code 'service_code',
                     con.region 'country_region', c.service_id, c.weight, c.hawb, c.description, c.company, c.country_id,c.awb,c.date_created,c.value,c.number_pieces,
                      c.contact, c.address_line_1, c.address_line_2, c.city, c.postcode, c.telephone, c.other_routing_code, routing_code_eur,pc.tracking_number, c.eori_number, c.currency, c.vat_number, c.email, c.sender_country_id, c.ioss_number ");
                    
                    if (count($consignmentShipmentData) > 0) {
                        $consignmentIdArray = [];
                        $this->dom = new DOMDocument('1.0', 'utf-8');
                        foreach ($consignmentShipmentData as $consignmentItemData) {

                            $consignmentId = $consignmentItemData->getConsignmentId();
                            $consignmentIdArray[] = $consignmentId;
                            $carrierId = $consignmentItemData->getCarrierId();
                            $this->record_array[] = $this->getItemXml($consignmentItemData, $this->constants[$serviceid]);
                        }
                        $carrierId = $this->serviceValues->getCarrierId();
                        $agentid = trim($agentServiceArray['agent'][$key]);
                        $this->booking_file = "manifest_".date("YmdHis");
                        
                        if ($this->sendBookings($this->constants[$serviceid])) {
                            if (!empty($consignmentIdArray)) {

                                $sql = "UPDATE consignment SET send_courier_data = 1, booked_file_id = '" . $this->booking_file . "'
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

    private function addWayBill(Consignment $consignment, $parcel_idx, $licence_plate) {

        $this->pdf->line(2, 2, 98, 2);
        $this->pdf->line(2, 148, 98, 148);
        $this->pdf->line(2, 2, 2, 148);
        $this->pdf->line(98, 2, 98, 148);
        $this->pdf->line(2, 25, 98, 25);
        $this->pdf->line(2, 70, 98, 70);
        $this->pdf->line(2, 115, 98, 115);


        $font_name = 'helvetica';

        $handling = $this->serviceValues->getCode();
        $this->pdf->setFont($font_name, "L", 7);
        $prefixvalue = substr($licence_plate, 0, 2);
        

        if($this->serviceValues->getIsUntrack() == 0){
            $imageWeight = realpath(SETTING_DIR_REMOTE . "images/Weight.jpg");
            $imageScan = realpath(SETTING_DIR_REMOTE . "images/Scan barcode.jpg");
            $imageSignature = realpath(SETTING_DIR_REMOTE . "images/Signature.jpg");
            if ($prefixvalue == "LB" || $prefixvalue == "LM" || $prefixvalue == "UF") {
                $this->pdf->image($imageWeight, 5, 71, 10);
                $this->pdf->image($imageScan, 45, 71, 10);
            } else {
                $this->pdf->image($imageWeight, 5, 71, 10);
                $this->pdf->image($imageScan, 45, 71, 10);
                $this->pdf->image($imageSignature, 85, 71, 10);
            }
            $this->pdf->setFont($font_name, "B", 11);
            $this->pdf->Text(3, 83, "Payment Type:");
            $this->pdf->Text(3, 88, "Cust NO:   " . $this->constants['SWEDENPOST_CUSTOMER_NO']);
        }
        
        if(!empty($consignment->getIossNumber())){
            $imageIoss = realpath(SETTING_DIR_REMOTE . "images/swedent_ioss.png");
            $this->pdf->image($imageIoss, 20, 71, 10);
        }
        if($prefixvalue == "UF") {
            $image = realpath(SETTING_DIR_REMOTE . "images/sweden_post_level2_ppi.png");
            $this->pdf->image($image, 40, 2, 58, 22);
        }        
        else if ($prefixvalue == "LB") {
            $image1 = SETTING_DIR_REMOTE . "images/sweden_post_canada.png";
            $this->pdf->image($image1, 25, 1, 15);
        }
        else if ($prefixvalue == "LM"){
            $image = realpath(SETTING_DIR_REMOTE . "images/swedenpost_ppi_lm.png");
            $this->pdf->image($image, 40, 2, 58, 22);
        }
        else{
            $image = realpath(SETTING_DIR_REMOTE . "images/swedenpost_ppi.png");
            $this->pdf->image($image, 40, 2, 58, 22);
        }
        


        $this->pdf->setFont("Arial", "", 6);
        $this->pdf->Text(5, 10, "If undeliverable return to:");
        $this->pdf->setFont("Arial", "", 8);

        $this->pdf->Text(5, 13, "PO Box " . $this->constants['SWEDENPOST_RETURN_POBOX']); // 2093
        $this->pdf->Text(5, 16, "SE-20226 Malmo");
        $this->pdf->setFont($font_name, "B", 10);

        $this->pdf->Rect(68, 50, 28, 8);
        $this->pdf->Text(70, 51, $this->userAccount->getUserAccount(), false, false, true, 0, 0, 'C');
//        $this->pdf->Line(68, 59, 96, 59);
//        $this->pdf->Text(70, 60, $this->country->getRegion(), false, false, true, 0, 0, 'C');

        $address = "";
        $company = "";
        if ($consignment->getCompany() != "")
            $company = $consignment->getCompany();
        if ($consignment->getAddressLine1() != "")
            $address1 = $consignment->getAddressLine1();
        if ($consignment->getAddressLine2() != "")
            $address2 = $consignment->getAddressLine2();
        if ($consignment->getAddressLine3() != "")
            $address3 = $consignment->getAddressLine3();





        $this->pdf->setFont($font_name, "L", 10);
        $this->pdf->Text(3, 95, "Additional Services:");

        $this->pdf->setFont($font_name, "B", 10);
        $weight_in_gram = $consignment->getWeight() * 1000;
        $this->pdf->Text(3, 109, "Weight: " . $weight_in_gram . " g");
        $this->pdf->setFont($font_name, "", 10);
        $this->pdf->Text(50, 109, $consignment->getReference());

        $this->pdf->SetFont($font_name, 'B', 22);
        $this->pdf->Circle(88, 100, 5);

        if ($consignment->getValue() < 15)
            $this->pdf->Text(85, 95, "L");
        else if ($consignment->getValue() >= 15 && $consignment->getValue() < 135)
            $this->pdf->Text(85, 95, "M");
        else if ($consignment->getValue() >= 135)
            $this->pdf->Text(85, 95, "H");




        $this->pdf->setFont("dejavusans", "", 10);



        $this->pdf->Text(5, 27, $consignment->getCompany());
        $this->pdf->Text(5, 31, $consignment->getContact());
        $this->pdf->Text(5, 35, $address1);
        $this->pdf->Text(5, 39, $address2);
        $this->pdf->Text(5, 43, $address3);
        $this->pdf->Text(5, 47, $this->country->getIso() . "-" . $consignment->getPostcode() . "-" . $consignment->getCity());
        $this->pdf->Text(5, 51, $this->country->getName());
        $this->pdf->Text(5, 55, "Tel: " . $consignment->getTelephone());

        $this->pdf->Text(5, 63, "Ref: " . $consignment->getHawb());




        $style = array(
            'position' => '',
            'align' => 'C',
            'stretch' => true,
            'fitwidth' => true,
            'cellfitalign' => '',
            'border' => false,
            'hpadding' => '1',
            'vpadding' => '1',
            'fgcolor' => array(0, 0, 0),
            'bgcolor' => false, //array(255,255,255),
            'text' => false,
            'font' => 'freesans',
            'fontsize' => 8,
            'stretchtext' => 1
        );

        $arr = str_split($licence_plate);

        if ($this->serviceValues->getIsUntrack() == 0) {
            $this->pdf->setFont($font_name, "B", 60);
            if ($prefixvalue == "LM" || $prefixvalue == "LB")
                $this->pdf->Text(5, 117, "L");
            else if ($prefixvalue == "LM" || $prefixvalue == "EB")
                $this->pdf->Text(5, 117, "E");
            else if ($prefixvalue == "UF")
                $this->pdf->Text(5, 117, "U");
            else
                $this->pdf->Text(5, 117, "R");
            $this->pdf->setFont($font_name, "L", 13);
            $tracking_number = $arr[0] . $arr[1] . " " . $arr[2] . $arr[3] . " " . $arr[4] . $arr[5] . $arr[6] . " " . $arr[7] . $arr[8] . $arr[9] . " " . $arr[10] . " " . $arr[11] . $arr[12];
            //echo $tracking_number; exit;
            $this->pdf->Text(5, 142, "Shipment-ID:  " . $tracking_number);

            $this->pdf->write1DBarcode($licence_plate, 'C128', 25, 120, '', 20, .35, $style, '');
        }
        else {

            $this->pdf->setFont($font_name, "L", 13);
            $this->pdf->Text(15, 142, "Shipment-ID :  " . $consignment->getHawb());

            $this->pdf->write1DBarcode($consignment->getHawb(), 'C128', 17, 120, '', 20, .35, $style, '');
        }
    }
    
     private function sendBookings($ftpConstants) {
        if (sizeof($this->record_array) > 0) {
            
            $path = SETTING_DIR_ASSETS . "data_send/swedenpost_booking/" . date("Y_m_d") . "/";
            if (!file_exists($path))
                @mkdir($path, 0777, TRUE);

            $file_path = $path . $this->booking_file.".xml";
            chmod($path, 0777);

           $this->getXML($this->booking_file, $ftpConstants);
           
           $this->dom->save($file_path);
          
            $remote_file_path2 = "./directlink/Out/" . $this->booking_file . ".xml";
//
//  
            $all_success = false;
            $ftp_object1 = new FTPfile("mruga_assets", "a0x79l*P", "213.246.110.102");
            $ftp_object1->passive();
            if ($ftp_object1->put($remote_file_path2, $file_path, FTP_ASCII)) {
                $all_success = true;
//                echo "successfully uploaded $file\n";
            } else {
                mail("itsupport@oneworldexpress.com", "UNABLE TO SEND SWEDEN POST DATA", "UNABLE TO SEND SWEDEN POST DATA" . $this->booking_file.".xml");
//                echo "There was a problem while uploading $file\n";
            }
         return $all_success;   
        }

        return false;
			

    }
    
    private function getXML($filename, $constant) {
        
        
        $dom_doc =  new DOMDocument('1.0', 'utf-8');
        $this->dom->formatOutput = true;
        $element = $this->dom->appendChild($this->dom->createElement('manifest'));
        $xml_manifestname = $this->dom->createElement("manifest_name", $filename.".xml");
        $element->appendChild($xml_manifestname);
        $xml_customerid = $this->dom->createElement("customer_id", "CFI");
        $element->appendChild($xml_customerid);
        $xml_customername = $this->dom->createElement("customer_name", "Mail Option Ltd.");
        $element->appendChild($xml_customername);
        $xml_customerno = $this->dom->createElement("customer_no", "30120080540");
        $element->appendChild($xml_customerno);
        $xml_customervat = $this->dom->createElement("customer_vat", "");
        $element->appendChild($xml_customervat);

        $xml_items = $this->dom->createElement("items");
        $element->appendChild($xml_items);
      
        if(sizeof($this->record_array) > 0)
	{
            $orgdoc = new DOMDocument;
				
            foreach($this->record_array as $records)
            {
                //print_r($records);
                $orgdoc->loadXML($records);


                $node = $orgdoc->getElementsByTagName("item")->item(0);
                $ItemImport =  $orgdoc->saveXML();


                // Import the node, and all its children, to the document
                $Item = $this->dom->importNode($node, true);
                // And then append it to the "<root>" node
                $xml_items->appendChild($Item);
            }
            $sweden_XMl =  $this->dom->saveXML();
            return $sweden_XMl;
        }
    }
    
    private function getItemXml($consignment, $constant)
    {
        $this->country = new Country($consignment->getCountryId());
        $dom_doc =  new DOMDocument('1.0', 'utf-8');
        $xml_item = $dom_doc->createElement("item");
        $dom_doc->appendChild($xml_item);
        $xml_item_receiver = $dom_doc->createElement("receiver_name", utf8_encode($consignment->getContact()));
        $xml_item->appendChild($xml_item_receiver);
        $xml_receiver_add1 = $dom_doc->createElement("receiver_address1", utf8_encode($consignment->getAddressLine1()));
        $xml_item->appendChild($xml_receiver_add1);
        $xml_receiver_add2 = $dom_doc->createElement("receiver_address2", utf8_encode($consignment->getAddressLine2()));
        $xml_item->appendChild($xml_receiver_add2);
        $xml_receiver_accesscode = $dom_doc->createElement("receiver_accesscode");
        $xml_item->appendChild($xml_receiver_accesscode);
        $xml_receiver_city = $dom_doc->createElement("receiver_city", utf8_encode($consignment->getCity()));
        $xml_item->appendChild($xml_receiver_city);
        $xml_receiver_postcode = $dom_doc->createElement("receiver_post_code", utf8_encode($consignment->getPostcode()));
        $xml_item->appendChild($xml_receiver_postcode);
        $xml_receiver_country = $dom_doc->createElement("receiver_country_code", utf8_encode($this->country->getIso()));
        $xml_item->appendChild($xml_receiver_country);
        $xml_receiver_email = $dom_doc->createElement("receiver_email_address", utf8_encode($consignment->getEmail()));
        $xml_item->appendChild($xml_receiver_email);
        $xml_receiver_contact = $dom_doc->createElement("receiver_contact_person", utf8_encode($consignment->getContact()));
        $xml_item->appendChild($xml_receiver_contact);
        $xml_receiver_phone = $dom_doc->createElement("receiver_phone_no", utf8_encode($consignment->getTelephone()));
        $xml_item->appendChild($xml_receiver_phone);
        $xml_receiver_mobile = $dom_doc->createElement("receiver_mobile_no");
        $xml_item->appendChild($xml_receiver_mobile);
        $xml_receiver_state = $dom_doc->createElement("receiver_state");
        $xml_item->appendChild($xml_receiver_state);
        $xml_sender_name = $dom_doc->createElement("sender_name", "Mail Option Ltd.");
        $xml_item->appendChild($xml_sender_name);
        $xml_sender_add1 = $dom_doc->createElement("sender_address1", "Unit 39-45, The Waterside Trading Centre");
        $xml_item->appendChild($xml_sender_add1);
        $xml_sender_add2 = $dom_doc->createElement("sender_address2");
        $xml_item->appendChild($xml_sender_add2);
        $xml_sender_city = $dom_doc->createElement("sender_city", "London");
        $xml_item->appendChild($xml_sender_city);
        $xml_sender_postcode = $dom_doc->createElement("sender_post_code", "W7 2QD");
        $xml_item->appendChild($xml_sender_postcode);
        $xml_sender_country = $dom_doc->createElement("sender_country_code", "GB");
        $xml_item->appendChild($xml_sender_country);
        $xml_sender_email = $dom_doc->createElement("sender_email_address");
        $xml_item->appendChild($xml_sender_email);
        $xml_sender_contact = $dom_doc->createElement("sender_contact_person");
        $xml_item->appendChild($xml_sender_contact);
        $xml_sender_phone = $dom_doc->createElement("sender_phone_no");
        $xml_item->appendChild($xml_sender_phone);
        $xml_sender_tax = $dom_doc->createElement("sender_tax_id", $consignment->getIossNumber());
        $xml_item->appendChild($xml_sender_tax);
        $xml_return_name = $dom_doc->createElement("return_name", "Mail Option Ltd.");
        $xml_item->appendChild($xml_return_name);
        $xml_return_add1 = $dom_doc->createElement("return_address1", "PO Box 2093");
        $xml_item->appendChild($xml_return_add1);
        $xml_return_add2 = $dom_doc->createElement("return_address2");
        $xml_item->appendChild($xml_return_add2);
        $xml_return_city = $dom_doc->createElement("return_city", "MALMO");
        $xml_item->appendChild($xml_return_city);
        $xml_return_postcode = $dom_doc->createElement("return_post_code", "20226");
        $xml_item->appendChild($xml_return_postcode);
        $xml_return_country = $dom_doc->createElement("return_country_code", "SE");
        $xml_item->appendChild($xml_return_country);
        $xml_order_no = $dom_doc->createElement("order_no", $consignment->getHawb());
        $xml_item->appendChild($xml_order_no);
        $xml_tracking_no = $dom_doc->createElement("tracking_no", $consignment->getAwb());
        $xml_item->appendChild($xml_tracking_no);
        /*$xml_manifest_date = $dom_doc->createElement("manifest_date", date("Y-m-d H:i:s"));
        $xml_item->appendChild($xml_manifest_date);
        $xml_waybill_no = $dom_doc->createElement("waybill_no");
        $xml_item->appendChild($xml_waybill_no);
        $xml_shipment_no = $dom_doc->createElement("shipment_no");
        $xml_item->appendChild($xml_shipment_no);
        $xml_invoice_no = $dom_doc->createElement("invoice_no");
        $xml_item->appendChild($xml_invoice_no);
        $xml_additional_service_point = $dom_doc->createElement("additional_service_point");
        $xml_item->appendChild($xml_additional_service_point);*/
        $xml_description_content = $dom_doc->createElement("description_content", $consignment->getDescription());
        $xml_item->appendChild($xml_description_content);
        $xml_total_weight = $dom_doc->createElement("total_weight", $consignment->getWeight());
        $xml_item->appendChild($xml_total_weight);
        $xml_notification = $dom_doc->createElement("notification");
        $xml_item->appendChild($xml_notification);
        $xml_option1 = $dom_doc->createElement("option1");
        $xml_item->appendChild($xml_option1);
       /* $xml_receiving_point = $dom_doc->createElement("receiving_point");
        $xml_item->appendChild($xml_receiving_point);
        $xml_injection_point = $dom_doc->createElement("injection_point");
        $xml_item->appendChild($xml_injection_point);
        $xml_carrier_service_code = $dom_doc->createElement("carrier_service_code");
        $xml_item->appendChild($xml_carrier_service_code);*/
        $xml_content = $dom_doc->createElement("content", $consignment->getDescription());
        $xml_item->appendChild($xml_content);
        $xml_quantity_of_content = $dom_doc->createElement("quantity_of_content", $consignment->getNumberPieces());
        $xml_item->appendChild($xml_quantity_of_content);
        $xml_invoice_amount = $dom_doc->createElement("invoice_amount", $consignment->getValue());
        $xml_item->appendChild($xml_invoice_amount);
        $xml_invoice_currency = $dom_doc->createElement("invoice_currency", $consignment->getCurrency());
        $xml_item->appendChild($xml_invoice_currency);
        $xml_customs_total_weight = $dom_doc->createElement("customs_total_weight", $consignment->getWeight());
        $xml_item->appendChild($xml_customs_total_weight);
        $xml_type_of_goods = $dom_doc->createElement("type_of_goods", "4");
        $xml_item->appendChild($xml_type_of_goods);
        $xml_returns = $dom_doc->createElement("returns", "0");
        $xml_item->appendChild($xml_returns);
        /*$xml_hs_number2 = $dom_doc->createElement("hs_number2");
        $xml_item->appendChild($xml_hs_number2);
        $xml_country_of_origin = $dom_doc->createElement("country_of_origin");
        $xml_item->appendChild($xml_country_of_origin);*/
         if($consignment->getServiceCode() == "STSPINTL2" && $consignment->getIossNumber() != "")
        {
            $carrier_code = "408";
        }
        else if($consignment->getServiceCode() == "STSPINTL2"){
            $carrier_code = "200";
        }
        else if($consignment->getIossNumber() != "")
        {
            $carrier_code = "405";
        }
        else
        {
            $carrier_code = "300";
        }
        $xml_notification_level = $dom_doc->createElement("notification_level", $carrier_code);
        $xml_item->appendChild($xml_notification_level);
        $xml_shipment_reference = $dom_doc->createElement("shipment_reference", $consignment->getHawb());
        $xml_item->appendChild($xml_shipment_reference);
        $xml_contains_lithium = $dom_doc->createElement("contains_lithium", "N");
        $xml_item->appendChild($xml_contains_lithium);
        
        
        $parcel_list = $consignment->getParcels();
        if(count($parcel_list) > 0){
            foreach($parcel_list as $parcel){
                $parcelDescription = json_decode($parcel->getDescription());
                $parcelCountry = json_decode($parcel->getCommodityCode());
                $parcelQty = json_decode($parcel->getQty());
                $parcelValue = json_decode($parcel->getItemValue());
                $parcelHscode = json_decode($parcel->getHsCode());
                $parcelSku = json_decode($parcel->getItemSku());
                $parcelWeight = json_decode($parcel->getPWeight());
                if(count($parcelDescription) > 0){
                    foreach($parcelDescription as $key=>$desc){
                        $articals = $dom_doc->createElement("articles");
                        $artical = $dom_doc->createElement("article");
                        $name =$dom_doc->createElement("name", $desc);
                        $artical->appendChild($name);
                        $qty = ($parcelQty[$key] > 0) ? $parcelQty[$key] : $consignment->getNumberPieces();
                        $unit_quantity =$dom_doc->createElement("unit_quantity", $qty);
                        $artical->appendChild($unit_quantity);
                        $unit_weight =$dom_doc->createElement("unit_weight", $parcelWeight[$key]);
                        $artical->appendChild($unit_weight);
                        $unit_value =$dom_doc->createElement("unit_value", $parcelValue[$key]);
                        $artical->appendChild($unit_value);
                        $hs_tariff_no =$dom_doc->createElement("hs_tariff_no", $parcelHscode[$key]);
                        $artical->appendChild($hs_tariff_no);
                        $country_of_origin =$dom_doc->createElement("country_of_origin", $parcelCountry[$key]);
                        $artical->appendChild($country_of_origin);
                        $articals->appendChild($artical);
                        $xml_item->appendChild($articals);
                    }
                }
                else
                {
                    $articals = $dom_doc->createElement("articles");
                    $artical = $dom_doc->createElement("article");
                    $name =$dom_doc->createElement("name", $consignment->getDescription());
                    $artical->appendChild($name);
                    $unit_quantity =$dom_doc->createElement("unit_quantity", $consignment->getNumberPieces());
                    $artical->appendChild($unit_quantity);
                    $unit_weight =$dom_doc->createElement("unit_weight", $consignment->getWeight());
                    $artical->appendChild($unit_weight);
                    $unit_value =$dom_doc->createElement("unit_value", $consignment->getValue());
                    $artical->appendChild($unit_value);
                    $hs_tariff_no =$dom_doc->createElement("hs_tariff_no", "");
                    $artical->appendChild($hs_tariff_no);
                    $senderCountry = new Country($consignment->getSenderCountryId());
                    $country_of_origin =$dom_doc->createElement("country_of_origin", $senderCountry->getIso());
                    $artical->appendChild($country_of_origin);
                    $articals->appendChild($artical);
                    $xml_item->appendChild($articals);
                }
            }
        }
        
       /* if($consignment->getCountryIsoCode() == "BR")
        {
            $xml_Sender_state = $dom_doc->createElement("Sender_state");
            $xml_item->appendChild($xml_Sender_state, $consignment->getState());
            $xml_Recipient_tax_ID = $dom_doc->createElement("Recipient_tax_ID");
            $xml_item->appendChild($xml_Recipient_tax_ID, "");
            $xml_Recipient_state = $dom_doc->createElement("Recipient_state");
            $xml_item->appendChild($xml_Recipient_state, $consignment->getState());
            $xml_value_total = $dom_doc->createElement("value_total");
            $xml_item->appendChild($xml_value_total, $consignment->getValue());
            $xml_value_dutiable = $dom_doc->createElement("value_dutiable");
            $xml_item->appendChild($xml_value_dutiable, $consignment->getValue());
            $xml_currency_code_freight = $dom_doc->createElement("currency_code_freight");
            $xml_item->appendChild($xml_currency_code_freight, $consignment->getCurrency());
            $xml_FreightValue = $dom_doc->createElement("FreightValue");
            $xml_item->appendChild($xml_FreightValue, $consignment->getValue());
            $xml_return_mode = $dom_doc->createElement("return_mode");
            $xml_item->appendChild($xml_return_mode, "1");
            $xml_insure_returns = $dom_doc->createElement("insure_returns");
            $xml_item->appendChild($xml_insure_returns, "0");
            $xml_Commodity_unit_value = $dom_doc->createElement("Commodity_unit_value");
            $xml_item->appendChild($xml_Commodity_unit_value, $consignment->getValue());
            
        }*/
        return $dom_doc->saveXML();
    }

    public function recycledShipment($consignment) {
        $output["STATUS"] = "SUCCESS";
        return $output;
    }

}
