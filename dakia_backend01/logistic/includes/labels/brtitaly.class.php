<?php

class BrtItaly implements CarrierService {

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
        /*
         * REMOTE AREA POSTCODE CHECK
         */
        $carrierObject = new Carrier();
        $remoteAreaCheck = $this->remoteareas($consignment, $carrierObject, '');
        switch ($remoteAreaCheck) {
            case 'allowed':
                break;
            case 'not-allowed':
                $returnOutput[] = "Your postcode is a remote area. Please contact to administrator to activate.";
            case 'block':
                $returnOutput[] = "Your postcode is a remote area, service is not available in this area.";
        }
        return $returnOutput;
    }

    public function remoteareas($consignment, $carrierObject, $sender) {

        $postCode = (int) $consignment->getPostCode();
        if (in_array($postCode, array('98055', '98050', '80071', '7046', '80070', '80070', '22060', '57034', '98055', '57031', '57032', '80073', '9014', '57037', '80074', '57038', '57034', '91023', '98050', '80070', '80075', '58019', '58012', '58012', '58012', '98050', '80077', '80077', '80077', '80077', '28838', '58012', '7024', '71040', '91017', '7024', '57034', '80076', '92010', '92010', '4027', '98050', '91023', '98050', '92010', '98055', '23030', '57037', '98050', '57030', '57033', '91010', '57034', '80073', '7024', '25050', '80075', '98050', '91017', '91017', '80075', '98050', '25050', '98055', '57034', '80077', '57030', '57030', '4027', '57036', '57037', '57030', '80079', '98055', '57038', '57039', '71040', '57037', '71040', '57034', '80070', '80077', '57034', '98050', '91017', '57034', '98050', '23030', '90010', '30100', '30121', '30122', '30123', '30124', '30125', '30126', '30132', '30133', '30135', '30141', '30142', '4020', '98050'))) {
            $consignment->setMessage("Your postcode is a remote area, service is not available in this area.");
            $consignment->setStatus(Consignment::STATUS_INVALID);
            $consignment->setRemoteCharges('YES');

            return 'block';
        }
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
        if (trim(@$this->constants['BRT_SHIPPER_POSTCODE']) == '' || trim(@$this->constants['BRT_SHIPPER_COUNTRY']) == '' || trim(@$this->constants['BRT_SHIPPER_ADDRESS_LINE1']) == '') {
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
                    $licence_plate = $resultArray["PREFIX"] . sprintf('%07d', $resultArray["RANGE"]) . $resultArray["SUFIX"];
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
            $this->addWayBill($consignment, $parcel_idx, $parcel_count, $licence_plate);

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
         
        $tracking = new Tracking();
        $deliveredArray = array("704"); 

        include_once(BASE_PATH."includes/labels/brtitalytrackingstatus.class.php");
        $this->user = SessionManager::getUser();
        $service_url='http://wsr.brt.it:10041/web/GetIdSpedizioneByIdColloService/GetIdSpedizioneByIdCollo?wsdl';
        $client     = new SoapClient($service_url, array('trace' => true, 'exceptions' => true ));

        $obj = new stdClass();
        $obj->arg0 =  new stdClass();
        $obj->arg0->CLIENTE_ID = "0058550";
        $obj->arg0->COLLO_ID = $trackingNumber;

        $result = $client->getidspedizionebyidcollo($obj); 

        $tracking_result = $result->return;

        $success = $tracking_result->ESITO;

        if($success == "0")
        {
            $SPEDIZIONE_ID = $tracking_result->SPEDIZIONE_ID; // shipment ID
            $SPEDIZIONE_ANNO = $tracking_result->SPEDIZIONE_ANNO; // expedition year

            $service_url='http://wsr.brt.it:10041/web/BRT_TrackingByBRTshipmentIDService/BRT_TrackingByBRTshipmentID?wsdl';
            $client     = new SoapClient($service_url, array('trace' => true, 'exceptions' => true,  ));

            $obj = new stdClass();
            $obj->arg0 =  new stdClass();
            $obj->arg0->LINGUA_ISO639_ALPHA2 = "";
            $obj->arg0->SPEDIZIONE_ANNO = $SPEDIZIONE_ANNO; // expedition year
            $obj->arg0->SPEDIZIONE_BRT_ID = $SPEDIZIONE_ID; // shipment ID

            $detailresult = $client->brt_trackingbybrtshipmentid($obj);
            $result_success = trim($detailresult->return->ESITO);
            
            if($result_success == "0"){
                $tracking_details = array_reverse($detailresult->return->LISTA_EVENTI);  
                foreach($tracking_details as $trackPoint)
                {
                    $entityId = 0;
                    if ($trackBy == 'shipment')
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
                    else if ($trackBy == 'parcel')
                    {
                        $parcelObj = new ParcelFilter();
                        $parcelObj->addTrackingNumberFilter($trackingNumber);
                        $parcelDataArray = $parcelObj->getColumnList('p.id, p.tracking_number,p.consignment_id');
                        if (count($parcelDataArray) > 0)
                        {
                            $parcelData = $parcelDataArray[0];
                            $entityId = $parcelData->getId();
                        }
                    }
                    
                    if($entityId > 0)
                    {
                        $parcelEntity = new Parcel($entityId);
                        $finalStatusCode = $parcelEntity->getParcelStatusCode();
                        
                        $ServiceAreaDescription = $trackPoint->EVENTO->FILIALE; // location
                        $EventDescription = $trackPoint->EVENTO->DESCRIZIONE;	// description
                        $DateTime = date("Y-m-d G:i:s", strtotime($trackPoint->EVENTO->DATA . " " . $trackPoint->EVENTO->ORA));

                        $EventCode = $trackPoint->EVENTO->ID;
                        $Signatory = '';
                        $spTrackingStatus = BrtItalyTrackingStatus::getOweStatusCode($EventCode);
                        $tracking->saveTrackPoint($entityId, $trackBy, $DateTime, $trackingNumber, $spTrackingStatus, $ServiceAreaDescription, 
                                                   $EventCode, $EventDescription, $deliveredArray, $finalStatusCode);
                    }
                }
                $tracking->saveConsignmentTrackingStatus($trackingNumber, 'BrtItalyTrackingStatus');
            }
        }
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
                        foreach ($consignmentShipmentData as $consignmentItemData) {

                            $consignmentId = $consignmentItemData->getConsignmentId();
                            $consignmentIdArray[] = $consignmentId;
                            $carrierId = $consignmentItemData->getCarrierId();
                            $this->vatRecordArray[] = $this->create_FNVATFile($consignmentItemData, $this->constants[$serviceid]);
                            $this->vabRecordArray[] = $this->create_FNVABFile($consignmentItemData, $this->constants[$serviceid]);
                        }
                        $carrierId = $this->serviceValues->getCarrierId();
                        $agentid = trim($agentServiceArray['agent'][$key]);

                        $this->brtitaly_VAB_file = "FNVAB_es_ASCII" . date("YmdHis", time());
                        $this->brtitaly_VAT_file = "FNVAT_es_ASCII" . date("YmdHis", time());

                        if ($this->sendBookings($this->constants[$serviceid])) {
                            if (!empty($consignmentIdArray)) {

                                $sql = "UPDATE consignment SET send_courier_data=1, booked_file_id = '" . $this->brtitaly_VAB_file . "' 
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

    private function addWayBill(Consignment $consignment, $parcel_idx, $parcel_count, $licence_plate) {

        $this->pdf->line(3, 58, 98, 58);
        $this->pdf->line(3, 3, 98, 3);
        $this->pdf->line(98, 3, 98, 23.8);
        $this->pdf->line(3, 3, 3, 30);
        $this->pdf->line(35, 3, 35, 23.8);
        $this->pdf->line(3, 24, 98, 24);
        $this->pdf->line(3, 24, 3, 115);
        $this->pdf->line(3, 115, 98, 115);
        $this->pdf->line(98, 24, 98, 115);
        $this->pdf->line(3, 70, 98, 70);
        $this->pdf->line(3, 70, 3, 145);
        $this->pdf->line(98, 70, 98, 145);
        $this->pdf->line(3, 133, 98, 133);
        $this->pdf->line(3, 113, 3, 146);
        $this->pdf->line(3, 146, 98, 146);
        $this->pdf->line(98, 113, 98, 146);
        $this->pdf->line(30, 133, 30, 146);


        $logo =    User::getUserCompanyImages(false,$consignment->getUserId());
       
        $this->pdf->Image($logo, 67, 8, 30);

        $fontname = $this->pdf->addTTFfont('../assets/fonts/simhei.ttf', 'TrueTypeUnicode', '', 32);
        $this->pdf->SetFont($fontname, '', 8);
        $this->pdf->multicell(55, 15, ucfirst(strtolower($consignment->getNotes())), 0, 'L', '', '', 18, 59);

        $this->pdf->setFont("helvetica", "b", 13);
        $this->pdf->Text(5.5, 10, "BRT ITALY");
        $this->pdf->setFont("helvetica", "b", 10);
        $this->pdf->Text(5, 59, "Notes:");

        if ($parcel_count > 0) {
            $this->pdf->setFont("helvetica", "b", 10);
            $this->pdf->Text(70, 59, "Pieces : " . ($parcel_idx + 1) . "/" . $parcel_count);
        }

        $this->pdf->setFont("helvetica", "b", 13);

        $this->pdf->Text(30, 72, "Delivery Details");


        $this->pdf->Text(11, 136.5, $this->country->getIso());
        $this->pdf->setFont("helvetica", "L", 9);
        $pieces = str_pad($parcel_idx + 1, 3, "0", STR_PAD_LEFT);



        $style = array(
            'position' => '',
            'align' => 'C',
            'stretch' => true,
            'fitwidth' => true,
            'cellfitalign' => '55',
            'border' => false,
            'hpadding' => '7',
            'vpadding' => '7',
            'fgcolor' => array(0, 0, 0),
            'bgcolor' => false, //array(255,255,255),
            'text' => true,
            'font' => 'helvetica',
            'fontsize' => 15,
            'stretchtext' => 1
        );
        $this->pdf->write1DBarcode($licence_plate, 'C128', 7, 25, '', 40, 2, $style, '');


        $style1 = array(
            'position' => '',
            'align' => 'C',
            'stretch' => true,
            'fitwidth' => true,
            'cellfitalign' => '',
            'border' => false,
            'hpadding' => 'auto',
            'vpadding' => 'auto',
            'fgcolor' => array(0, 0, 0),
            'bgcolor' => false, //array(255,255,255),
            'text' => true,
            'font' => 'helvetica',
            'fontsize' => 8,
            'stretchtext' => 1
        );
        $this->pdf->write1DBarcode($consignment->getHawb(), 'C128', 3, 115, '', 20, 2, $style1, 'Y');
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



        $this->pdf->setFont("helvetica", "", 10);
        $this->pdf->Text(5, 81, $company);
        $this->pdf->Text(5, 85, $consignment->getContact());
        $this->pdf->Text(5, 89, $address1);
        $this->pdf->Text(5, 93, $address2);
        $this->pdf->Text(5, 97, $address3);
        $this->pdf->Text(5, 101, $consignment->getCity());
        $this->pdf->Text(5, 105, $consignment->getPostcode());
        $this->pdf->Text(55, 109, "Tel: " . $consignment->getTelephone());
        $this->pdf->setFont("helvetica", "B", 11);
        $this->pdf->Text(5, 109, $this->country->getName());
        $this->pdf->setFont("helvetica", "B", 8);
        $this->pdf->Text(34, 133, "if undelivered return to:");

        $this->pdf->setFont("helvetica", "L", 8);
        $this->pdf->Text(34, 136, $this->constants['BRT_SHIPPER_ADDRESS_LINE1'] . " " . $this->constants['BRT_SHIPPER_ADDRESS_LINE2']);
        $this->pdf->Text(34, 139, $this->constants['BRT_SHIPPER_ADDRESS_LINE3'] . " " . $this->constants['BRT_SHIPPER_CITY'] . " " . $this->constants['BRT_SHIPPER_POSTCODE']);

        $shipper_country = $this->constants['BRT_SHIPPER_COUNTRY'];
        if ((int) $shipper_country > 0) {
            $shipperCountry = new Country($shipper_country);
            $sCountry = $shipperCountry->getName();
            $sIso = $shipperCountry->getIso();
        } else {
            $sCountry = $shipper_country;
        }
        $this->pdf->Text(34, 142, $sCountry . " " . $sIso);
    }

    function create_FNVATFile(Consignment $consignment, $constantValue) {
        $record = "";

        $parcelList = $consignment->getParcels();
        if (count($parcelList) > 0) {
            foreach ($parcelList as $parcel) {
                $record .= $this->fld(1, ""); //VIRTUAL DELETE LEN 1 1
                $record .= $this->fld(8, $constantValue["SENDER_CUSTOMER_CODE"], true); //SENDER CUSTOMER CODE len 7 2 "0058780",
                $record .= $this->fld(4, $constantValue["DEPARTURE_CODE"], true); //DEPARTURE DEPOT len 3 3 "005"
                $record .= $this->fld(5, date("Y"), true); //YEAR 4 4
                $record .= $this->fld(3, "0", true); //SERIES NUMBER 2 5
                $awb = $parcel->getTrackingNumber();

                $record .= $this->fld(8, $parcel->getTrackingNumber(), true); //SHIPMENT NUMBER LEN 7 6
                $record .= $this->fld(1, "E") . ""; //Record Type 1 7
                $record .= $this->fld(35, $parcel->getTrackingNumber()); //PARCEL NUMBER 35 8
                $record .= "\r\n";
                if ($consignment->getTelephone() != '') {
                    $record .= $this->fld(1, ""); //VIRTUAL DELETE LEN 1 1
                    $record .= $this->fld(8, $constantValue["SENDER_CUSTOMER_CODE"], true); //SENDER CUSTOMER CODE len 7 2
                    $record .= $this->fld(4, $constantValue["DEPARTURE_CODE"], true); //DEPARTURE DEPOT len 3 3
                    $record .= $this->fld(5, date("Y"), true); //YEAR 4 4
                    $record .= $this->fld(3, "0", true); //SERIES NUMBER 2 5
                    $record .= $this->fld(8, $parcel->getTrackingNumber(), true); //SHIPMENT NUMBER LEN 7 6
                    $record .= $this->fld(1, "B") . ""; //Record Type 1 7
                    $record .= $this->fld(35, $consignment->getTelephone()); //PARCEL NUMBER 35 8
                    $record .= "\r\n";
                }
                if ($consignment->getEmail() != '') {
                    $record .= $this->fld(1, ""); //VIRTUAL DELETE LEN 1 1
                    $record .= $this->fld(8, $constantValue["SENDER_CUSTOMER_CODE"], true); //SENDER CUSTOMER CODE len 7 2
                    $record .= $this->fld(4, $constantValue["DEPARTURE_CODE"], true); //DEPARTURE DEPOT len 3 3
                    $record .= $this->fld(5, date("Y"), true); //YEAR 4 4
                    $record .= $this->fld(3, "0", true); //SERIES NUMBER 2 5
                    $record .= $this->fld(8, $parcel->getTrackingNumber(), true); //SHIPMENT NUMBER LEN 7 6
                    $record .= $this->fld(1, "I") . ""; //Record Type 1 7
                    $record .= $this->fld(35, $consignment->getEmail()); //PARCEL NUMBER 35 8
                    $record .= "\r\n";
                }
            }
            return $record;
        }
    }

    private function create_FNVABFile(Consignment $consignment, $constantValue) {

        $this->country = new Country($consignment->getCountryId());
        $record = "";
        $record .= $this->fld(1, "") . " "; //VIRTUAL DELETE LEN 1 1
        $record .= $this->fld(7, $constantValue["SENDER_CUSTOMER_CODE"]) . " "; //SENDER CUSTOMER CODE len 7 2
        $record .= $this->fld(3, $constantValue["DEPARTURE_CODE"]) . " "; //DEPARTURE DEPOT len 3 3
        $record .= date("Y") . " "; //YEAR 4 4
        $record .= date("md") . " "; //MONTH AND DATE 4 5 
        $record .= $this->fld(2, "0", true) . " "; //SERIES NUMBER 2 6
        $awb = $consignment->getAwb();

        $record .= $this->fld(7, $awb, true); //SHIPMENT NUMBER LEN 7 7
        $record .= $this->fld(2, "1"); //Delivery freight Type Code 2 8
        $record .= $this->fld(4, 0, true); //Arrival Depot 3 9

        $record .= $this->fld(35, utf8_encode($consignment->getContact())); //CONSIGNMENT COMPANY NAME 35 10
        $record .= $this->fld(35, utf8_encode($consignment->getCompany())); //CONSIGNMENT CONTACT NAME 35 11
        $record .= $this->fld(35, utf8_encode($consignment->getAddressLine1() . " " . $consignment->getAddressLine2())); //CONSIGNMENT CONTACT NAME 35 12
        $record .= $this->fld(9, $consignment->getPostcode()); //CONSIGNMENT POSTCODE 9 13
        $record .= $this->fld(35, $consignment->getCity()); //CONSIGNMENT CITY 35 14
        $record .= $this->fld(2, ""); //CONSIGNMENT PROVINCE 2 15
        $contryIsoCode = $this->country->getIso();
        if ($contryIsoCode == "IT")
            $contryIsoCode = "";
        $record .= $this->fld(3, $contryIsoCode); //ISO CODE 3 16
        $record .= $this->fld(2, ""); //CLOSING SHIFT 2 17
        $record .= $this->fld(2, ""); //CLOSING SHIFT 2 18
        $record .= $this->fld(4, "300", true); //Pricing Condition 3 19
        $record .= $this->fld(1, "C"); //Service Type 1 20 H=10:30 Service E=Priority Service C=Express Service D=Distribution Service
        $record .= $this->fld(15, "0,000", true); //Shipment Insurance Amount 13 21
        $record .= $this->fld(3, ""); //Shipment Insurance Amount 3 22

        $record .= $this->fld(15, utf8_encode("E-COMMERCE")); //Shipment Description 15 23
        $record .= $this->fld(6, $consignment->getNumberPieces(), true); //Number of Parcel 5 24
        $weight = number_format($consignment->getWeight(), 1);
        if ($weight < 1) {
            $weight = 1;
        }
        $record .= $this->fld(9, str_replace(".", ",", $weight), true); //Weight 7 25
        $record .= $this->fld(7, "0,000", true); //Volumn 5 26
        $record .= $this->fld(15, "0,000", true); //Quantity to be invoiced 13 27
        $record .= $this->fld(15, "0,000", true); //Cash On delivery 13 28
        $record .= $this->fld(2, ""); //Cash On delivery payment type 2 29
        $record .= $this->fld(3, ""); //currency Cash On delivery 3 30
        $record .= $this->fld(2, ""); //COD MANAGEMENT CODE 2 31
        $record .= $this->fld(16, $consignment->getId(), true); //NUMERICSENDER REFERENCE 15 32	
        $record .= $this->fld(15, $consignment->getHawb()); //Parcel reference 15 33	

        $record .= $this->fld(8, "0", true); //Number of PARCEL 7 34
        $record .= $this->fld(8, "0", true); //NUMBER OF PARCEL NUMEBR TO 7 35	
        $record .= $this->fld(1, " "); //X=PARCEL CUMULATIVE 1 36		
        $record .= $this->fld(35, utf8_encode(htmlspecialchars($consignment->getNotes()))); //NOTES 35 37	
        $record .= $this->fld(35, ""); //Extension of notes 35 38	
        $record .= $this->fld(3, "0", true); //Delivery Zone 2 39	
        $record .= $this->fld(2, "7Q"); //Parcel Handling code 2 40	
        $record .= $this->fld(1, ""); //Hold for pickup 1 41	
        $record .= $this->fld(9, "0", true); //Delivery Date Required 8 42
        $record .= $this->fld(1, ""); //Delivery Type 1 43	
        $record .= $this->fld(5, "0", true); //Delivery Time 4 44	
        $record .= $this->fld(2, ""); //Taxation Code 2 45	
        $record .= $this->fld(1, ""); //Flag Sender's Tariff 1 46	
        $record .= $this->fld(15, "0,000", true); //Declared Parcel Value 13 47	
        $record .= $this->fld(3, ""); //Currecny Declared Parcel Value 3 48	
        $record .= $this->fld(2, ""); //Delivery Management Code 2 49	
        $record .= $this->fld(2, ""); //HOLD ON STOCK 2 50	
        $record .= $this->fld(2, ""); //VARIOUS PARTICULAR MANAGEMENT CODE 2 51	
        $record .= $this->fld(1, ""); //1st Particular Delivery 1 52	
        $record .= $this->fld(1, ""); //2nd Particular Delivery  1 53	
        $record .= $this->fld(1, ""); //Social Code  1 54		
        $record .= $this->fld(10, "0", true); //TYPE AND QUANTITY PALLET  9 55	
        $record .= $this->fld(25, ""); //ORIGINAL SENDER COMPANY NAME 25 56
        $record .= $this->fld(9, ""); //ZIPCOE 9 57
        $record .= $this->fld(3, ""); //Country of Original sender  3 58			
        $record .= "\r\n";


        return $record;
    }

    private function sendBookings($ftpConstants) {
        $isUpload = true;
        if (sizeof($this->vatRecordArray) > 0) {

            $path = SETTING_DIR_ASSETS . "data_send/brtitaly_booking/" . date("Y_m_d") . "/";
            if (!file_exists($path))
                @mkdir($path, 0777, true);

            $file_path = $path . $this->brtitaly_VAT_file . ".dat";
            $chek_file_path = $path . $this->brtitaly_VAT_file . ".chk";
            chmod($path, 0777);


            //Create Check FIle
            $file_handle_chk = fopen($chek_file_path, 'w');
            fclose($file_handle_chk);

            // create file
            $file_handle_brt = fopen($file_path, 'w');
            foreach ($this->vatRecordArray as $record) {
                fwrite($file_handle_brt, $record);
            }
            // close file
            fclose($file_handle_brt);

            if (isset($ftpConstants['BRT_FTP_SITE']) && trim($ftpConstants['BRT_FTP_SITE']) != '') {
                $SETTING_FTP_USER_BRT = $ftpConstants['BRT_FTP_USER'];
                $SETTING_FTP_PASSWORD_BRT = $ftpConstants['BRT_FTP_PASSWORD'];
                $SETTING_FTP_SITE_BRT = $ftpConstants['BRT_FTP_SITE'];


                $ftp_object1 = new FTPfile($SETTING_FTP_USER_BRT, $SETTING_FTP_PASSWORD_BRT, $SETTING_FTP_SITE_BRT);
                $remote_file_path = "./IN/" . $this->brtitaly_VAT_file . ".dat";


                if (!$ftp_object1->put($remote_file_path, $file_path, FTP_BINARY)) {
                    $isUpload = false;
                }

                $ftp_object1 = new FTPfile($SETTING_FTP_USER_BRT, $SETTING_FTP_PASSWORD_BRT, $SETTING_FTP_SITE_BRT);
                $remote_file_path = "./IN/" . $this->brtitaly_VAT_file . ".chk";


                if (!$ftp_object1->put($remote_file_path, $chek_file_path, FTP_BINARY)) {
                    $isUpload = false;
                }
                $this->vatRecordArray = NULL;
            }
        }
        
         if (sizeof($this->vabRecordArray) > 0) {

            $path = SETTING_DIR_ASSETS . "data_send/brtitaly_booking/" . date("Y_m_d") . "/";
            if (!file_exists($path))
                @mkdir($path, 0777, true);

            $file_path_vab = $path . $this->brtitaly_VAB_file . ".dat";
            $chek_file_path_vab = $path . $this->brtitaly_VAB_file . ".chk";
            chmod($path, 0777);


            //Create Check FIle
            $file_handle_chk_vab = fopen($chek_file_path_vab, 'w');
            fclose($file_handle_chk_vab);

            // create file
            $file_handle_brt_vab = fopen($file_path_vab, 'w');
            foreach ($this->vabRecordArray as $record) {
                fwrite($file_handle_brt_vab, $record);
            }
            // close file
            fclose($file_handle_brt_vab);

            if (isset($ftpConstants['BRT_FTP_SITE']) && trim($ftpConstants['BRT_FTP_SITE']) != '') {
                $SETTING_FTP_USER_BRT = $ftpConstants['BRT_FTP_USER'];
                $SETTING_FTP_PASSWORD_BRT = $ftpConstants['BRT_FTP_PASSWORD'];
                $SETTING_FTP_SITE_BRT = $ftpConstants['BRT_FTP_SITE'];


                $ftp_object1 = new FTPfile($SETTING_FTP_USER_BRT, $SETTING_FTP_PASSWORD_BRT, $SETTING_FTP_SITE_BRT);
                $remote_file_path = "./IN/" . $this->brtitaly_VAB_file . ".dat";


                if (!$ftp_object1->put($remote_file_path, $file_path_vab, FTP_BINARY)) {
                    $isUpload = false;
                }

                $ftp_object1 = new FTPfile($SETTING_FTP_USER_BRT, $SETTING_FTP_PASSWORD_BRT, $SETTING_FTP_SITE_BRT);
                $remote_file_path = "./IN/" . $this->brtitaly_VAB_file . ".chk";


                if (!$ftp_object1->put($remote_file_path, $chek_file_path_vab, FTP_BINARY)) {
                    $isUpload = false;
                }
                $this->vabRecordArray = NULL;
            }
        }
		
	
        
        if ($isUpload === false) {
            mail("itsupport@oneworldexpress.com", "BRT LOGIN FAILED", "BRT LOGIN FAILED" . $this->brtitaly_VAB_file);
        }
        return $isUpload;
    }

    public function recycledShipment($consignment) {
        $output["STATUS"] = "SUCCESS";
        return $output;
    }

    private function fld($len, $data, $numerical_flag = false) {
        $fld = "";
        // Char fields should be left justified and space filled to the end of the field and in UPPERCASE at all times.
        // Integer fields should be right justified and zero filled to the start of the field.
        if ($numerical_flag) {
            $fld = substr(str_repeat(" ", $len) . $data, 0 - $len); // take from right, -ve start pos.
        } else {
            $fld = substr($data . str_repeat(" ", $len), 0, $len);
        }
        return strToUpper($fld);
    }

}
