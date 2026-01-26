<?php

class Hermes1 implements CarrierService {

    private $pdf;
    private $serviceValues = null;
    private $constants = null;
    private $user = null;
    private $country = null;
    private $agentValues = null;
    private $booking_file = null;

    public function __construct() {
        $pdf = new PdfBase(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $this->pdf = $pdf;
    }

    public function validation(Consignment $consignment, Services $service, Country $country) {
        $returnOutput = array();
        /* This method is not generic and only use for Hermes.
         * It is used to check whether the Hermes service is available
         * and it also sets the hermes parameter  if service is available
         */

        $Number_of_Barcodes = "1";
        $hermes_barcode_1_to_7 = "";
        $hermes_barcode_seq_key = "";
        $carrier_id = "00";
        $mod_id = "004";
        $mode_name = "COU-PNET";
        $sort_level_key = "";
        $level_1_type = "";
        $level_1_name = "";
        $level_1_code = "";
        $level_2_type = "";
        $level_2_name = "";
        $level_2_code = "";
        $level_3_type = "";
        $level_3_name = "";
        $level_3_code = "";
        $level_4_type = "";
        $level_4_name = "";
        $level_4_code = "";
        $level_5_type = "";
        $level_5_name = "";
        $level_5_code = "";
        $hermesparameters = "";
        $pos_pcd_postcode_excluded_indicator = "";
        $postcode = $consignment->getPostcode();

        $postcode_filter = strtoupper($postcode);
        $postcode_filter = substr($postcode_filter, 0, 8);
        $postcode_filterlength = mb_strlen(trim($postcode_filter));

        $postcode_outward = substr($postcode_filter, $postcode_filterlength - 3, $postcode_filterlength);
        $postcode_inward = substr($postcode_filter, 0, $postcode_filterlength - 3);
        $inwardpostcodelength = mb_strlen($postcode_inward);

        if ($inwardpostcodelength == 3) {

            $postcode_inward = $postcode_inward . "  ";
        } else if ($inwardpostcodelength == 4)
            $postcode_inward = $postcode_inward . " ";
        else if ($inwardpostcodelength == 2)
            $postcode_inward = $postcode_inward . "   ";
        else if ($inwardpostcodelength == 1)
            $postcode_inward = $postcode_inward . "    ";

        $postcode_filter = $postcode_inward . $postcode_outward;
        $postcode = $postcode_filter;

        $strlen = mb_strlen($postcode);

        //We will only use hermes COUPNET NETWORK PREFERENCE RECORD
        $postcodenum = mb_substr($postcode, 0, $strlen - ($strlen - 6));
        $halfpostcode = mb_substr($postcode, 0, $strlen - ($strlen - 4));


        $sql = "SELECT pos_pcd_postcode_excluded_indicator,sort_level_key   FROM `hermes_postcode_record` WHERE fullpostcode = '" . $postcode . "'  limit 0,1";
        $rs = DbAccess3::runQuery($sql);

        if (mysqli_num_rows($rs) == 0) {

            $sql = "SELECT pos_pcd_postcode_excluded_indicator,sort_level_key   FROM `hermes_postcode_record` WHERE fullpostcode = '" . $postcodenum . "' limit 0,1";

            $rs = DbAccess3::runQuery($sql);
            if (mysqli_num_rows($rs) == 0) {
                $sql = "SELECT pos_pcd_postcode_excluded_indicator,sort_level_key   FROM `hermes_postcode_record` WHERE fullpostcode = '" . $halfpostcode . "' limit 0,1";
                $rs = DbAccess3::runQuery($sql);
            }
        }


        if (mysqli_num_rows($rs) == 0) {
            $returnOutput[] = "PostCode is not in our range to provide service";
        } else {
            while ($row = mysqli_fetch_assoc($rs)) {
                $pos_pcd_postcode_excluded_indicator = $row["pos_pcd_postcode_excluded_indicator"];
                $sort_level_key = trim($row["sort_level_key"]);
            }
            /////for UPS
            if ($sort_level_key == "UPS" || $sort_level_key == "") {
                $carrier_id = "94";
                $mod_id = "099";
                $mode_name = "UPS STD";
                $Number_of_Barcodes = "2";
                $sort_level_key = 'UPS';
            }

            $sql = "SELECT *  FROM sort_key_record WHERE trim(pos_sld_sort_level_key) ='" . $sort_level_key . "'";
            $rs = DbAccess3::runQuery($sql);
            while ($row = mysqli_fetch_assoc($rs)) {
                $level_1_type = $row["pos_sld_level_1_type"];
                $level_1_name = $row["pos_sld_level_1_name"];
                $level_1_code = $row["pos_sld_level_1_code"];
                $level_2_type = $row["pos_sld_level_2_type"];
                $level_2_name = $row["pos_sld_level_2_name"];
                $level_2_code = $row["pos_sld_level_2_code"];
                $level_3_type = $row["pos_sld_level_3_type"];
                $level_3_name = $row["pos_sld_level_3_name"];
                $level_3_code = $row["pos_sld_level_3_code"];
                $level_4_type = $row["pos_sld_level_4_type"];
                $level_4_name = $row["pos_sld_level_4_name"];
                $level_4_code = $row["pos_sld_level_4_code"];
                $level_5_type = $row["pos_sld_level_5_type"];
                $level_5_name = $row["pos_sld_level_5_name"];
                $level_5_code = $row["pos_sld_level_5_code"];
                $hermes_barcode_1_to_7 = $row["pos_sld_hermes_barcode_1_to_7"];
                $hermes_barcode_seq_key = $row["pos_sld_hermes_barcode_seq_key"];
            }

            // }
        }

        if (trim($hermes_barcode_1_to_7) == '')
            $$returnOutput[] = "We are not able to provide service of your required postcode";


        $hermesparameters = $Number_of_Barcodes . "||" . $hermes_barcode_1_to_7 . "||" . $hermes_barcode_seq_key . "||" . $carrier_id . "||" . $mod_id . "||" . $mode_name . "||" . $sort_level_key . "||" . $level_1_type . "||" . $level_1_name . "||" . $level_1_code . "||" . $level_2_type . "||" . $level_2_name . "||" . $level_2_code . "||" . $level_3_type . "||" . $level_3_name . "||" . $level_3_code . "||" . $level_4_type . "||" . $level_4_name . "||" . $level_4_code . "||" . $level_5_type . "||" . $level_5_name . "||" . $level_5_code;
        $consignment->setOtherRoutingCode($hermesparameters);
        return $returnOutput;
    }

    public function remoteareas($consignment, $carrierObject, $sender) {
        
    }

    public function label($consignment, $labelType = 'pdf', $size = '100x150') {


        $output = array();
        $this->user = SessionManager::getUser();
        $this->serviceValues = new Services($consignment->getServiceId());
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
        if (trim(@$this->constants['HERMES_CLIENT_ID']) == '' || trim(@$this->constants['HERMES_CLIENT_NAME']) == '') {
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
                    $licence_plate = $resultArray["PREFIX"] . $resultArray["RANGE"] . $resultArray["SUFIX"];
                }
                $trackingNo = $this->createhermesawb($consignment, $licence_plate);
                $parcel->setTrackingNumber($trackingNo);
                $parcel->save();
            } else {
                $trackingNo = $parcel->getTrackingNumber();
            }
            $licence_plate_array[$parcel_idx] = $trackingNo;

            $this->pdf->SetPrintFooter(false);
            $this->pdf->SetFooterMargin(0);
            $this->pdf->SetAutoPageBreak(false, 0);
            $page_size = array(100, 150);
            $this->pdf->AddPage("P", $page_size);
            $this->addWayBill($consignment, $parcel_idx, $parcel_count, $trackingNo);
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
        try{
                $soap = new SoapClient("https://www.hermes-europe.co.uk/parceltrackingservice/services/parcelTrackingService?wsdl");
                $inputparameters1 = array(  'barcode' =>  $trackingNumber,
                                            'clientGroupId' => "99461",
                                            'clientLicence' => "6c88042e-3025-4e85-a2a5-dcce7ce4c440"
                                                                          );

                $xmlarr =	$soap->fetchTracking($inputparameters1);
                echo 'dasd';
                print_r($xmlarr);
                die;
                
                $total_parcelProgresses 	=	count($xmlarr->parcelProgresses);						
                $status	=	'Intransportation';  
                $deleveruDate	=	'';
                $courier_desc = '';
                $courier_status = '';
			
                if($total_parcelProgresses >0)
                {
                    for ($i=$total_parcelProgresses; $i >= 0; $i--)
                    {
			$eventCode = $xmlarr->trackingPoints[$i]->id;	                  
                        $dateTime	=	date("Y-m-d H:i:s", strtotime($xmlarr->parcelProgresses[$i]->parcelProgressId->timeStamp));
                        if(trim($dateTime) != '1970-01-01 01:00' && $dateTime != '1970-01-01 12:00')
                        {
                            $location = $xmlarr->trackingPoints[$i]->trackingPointExternalDescription;    
                            //$courier_status = $status;
                            $description = $xmlarr->trackingPoints[$i]->customerDescription;
                                            
                            $pos_signature = strpos($xmlarr->trackingPoints[$i]->customerDescription, 'Signature');				  
                            $pos_delivered = strpos($xmlarr->trackingPoints[$i]->customerDescription, 'Delivered');				  
                        }
                    }
		}
                
                }
                catch(Exception $e){
                    echo $e; 
                }	
    }

    public function sendData($tracking_numbers = array()) {
        $output = [];
        $agentServiceArray = [];
        $consignmentData = new ConsignmentFilter();
        $consignmentData->addFilter("     s.carrier_id= '147'", "servicefilter");
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
                    $consignmentShipmentDataFilter->addFilter("     c.send_courier_data= '0'", "consignmentfilter");
                    if (!empty($tracking_numbers))
                        $consignmentShipmentDataFilter->addFilter("     AND pc.tracking_number in ('" . implode("','", $tracking_numbers) . "') and pc.tracking_number <> ''", "parcelJoinFilter");
                    $consignmentShipmentData = $consignmentShipmentDataFilter->getColumnList(" c.id 'consignment_id', s.carrier_id ,s.code 'service_code',
                     con.region 'country_region', c.service_id, c.weight, c.hawb, c.description, c.company, c.country_id,c.awb,c.date_created,c.value,c.number_pieces,
                      c.contact, c.address_line_1, c.address_line_2, c.city, c.postcode, c.telephone, c.other_routing_code, routing_code_eur,pc.tracking_number ");

                    if (count($consignmentShipmentData) > 0) {
                        $consignmentIdArray = [];
                        foreach ($consignmentShipmentData as $consignmentItemData) {

                            $consignmentId = $consignmentItemData->getConsignmentId();
                            $consignmentIdArray[] = $consignmentId;
                            $carrierId = $consignmentItemData->getCarrierId();
                            $this->record_array[] = $this->getShipmentRecord($consignmentItemData, $this->constants[$serviceid]);
                        }
                        $carrierId = $this->serviceValues->getCarrierId();
                        $agentid = trim($agentServiceArray['agent'][$key]);
                        $run_number = CarrierDataFileLog::generateRunNumber($carrierId, $agentid, false);
                        $this->booking_file = "PARCEL_PRE_ADVICE".$this->constants[$serviceid]["HERMES_CLIENT_ID"]."_" . date("Y-m-d_H-i-s", time());

                        $carrierDataFileLog = new CarrierDataFileLog();
                        $carrierDataFileLog->setCarrierId($carrierId);
                        $carrierDataFileLog->setAgentId($agentid);
                        $carrierDataFileLog->setFileName($this->booking_file);
                        $carrierDataFileLog->setRunNumber($run_number);
                        $carrierDataFileLog->save();

                        if ($this->sendBookings($this->constants[$serviceid], $run_number)) {
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

    private function addWayBill(Consignment $consignment, $parcel_idx, $parcel_count, $trackingNo) {



        $hermesparameters = $consignment->getOtherRoutingCode();
        $hermesparametersarray = explode("||", $hermesparameters);
        $x = 10;
        $y = 20;

        $style = array(
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
            'text' => false,
            'font' => 'helvetica',
            'fontsize' => 8,
            'stretchtext' => 1
        );

        //  $this->pdf->write1DBarcode($trackingNo, 'I25', $x, $y, '', 30, 0.5, $style, 'Y');
        $this->Barcode2D($consignment, $trackingNo);

        $hermes_barcode_display = $trackingNo;
        $strlen = mb_strlen($trackingNo);
        $hermes_barcode_display = mb_substr($trackingNo, 0, $strlen - ($strlen - 2)) . "-";
        $hermes_barcode_display1 = mb_substr($trackingNo, 2, $strlen - ($strlen - 3)) . "-";
        $hermes_barcode_display2 = mb_substr($trackingNo, 5, $strlen - ($strlen - 2)) . "-";
        $hermes_barcode_display3 = mb_substr($trackingNo, 7, $strlen - ($strlen - 8)) . "-";
        $hermes_barcode_display4 = mb_substr($trackingNo, 15, $strlen - ($strlen - 1));
        $hermes_barcode_display = $hermes_barcode_display . $hermes_barcode_display1 . $hermes_barcode_display2 . $hermes_barcode_display3 . $hermes_barcode_display4;

        $this->pdf->setFont("helvetica", "B", 9);





        if ($this->user->getLogo() != "") {

            $image = "../images/userlogo/" . $this->user->getLogo();
            //$this->pdf->image($image, 8, 8, 32);
            $this->pdf->Text(5, 8, "www.oneworldexpress.com");
        } else {
            $image = "../images/logo.jpg";

            $this->pdf->Text(5, 8, "www.oneworldexpress.com");
        }
        if (file_exists($image)) {
            //   $this->pdf->image($image, 8, 8, 32, 15);
        }



//        $this->pdf->setFont("helvetica", "B", 10);
//        $this->centreText(12, 47, 65, $hermes_barcode_display, $this->pdf);

        $this->pdf->Text(8, 64, $hermesparametersarray[9]);

        if ($this->serviceValues->getCode() == "2S") {
            $this->pdf->setFont("helvetica", "B", 10);
            $this->pdf->Text(9, 73, "PRINTED NAMED REQUIRED");
            $this->pdf->Rect(8, 72, 51, 7, 'D');
        }
        $this->pdf->line(7, 63, 16, 63);
        $this->pdf->line(7, 63, 7, 69);
        $this->pdf->line(7, 69, 16, 69);
        $this->pdf->line(16, 69, 16, 63);




        $this->pdf->setFont("helvetica", "L", 10);
        $x = 60;
        $this->pdf->Text($x, 65, $hermesparametersarray[7]);
        $this->pdf->Text($x + 22, 65, $hermesparametersarray[8]);

        $this->pdf->line(80, 64, 94, 64);
        $this->pdf->line(80, 64, 80, 69);
        $this->pdf->line(80, 69, 94, 69);
        $this->pdf->line(94, 69, 94, 64);
        $this->pdf->setFont("helvetica", "B", 13);
        $this->pdf->Text($x, 69, $hermesparametersarray[10]);
        $this->pdf->Text($x + 22, 69, $hermesparametersarray[11]);


        $this->pdf->setFont("helvetica", "L", 10);
        $this->pdf->Text($x, 74, $hermesparametersarray[13]);
        $this->pdf->Text($x + 22, 74, $hermesparametersarray[14]);
        $this->pdf->Text($x, 78, $hermesparametersarray[16]);
        $this->pdf->Text($x + 22, 78, $hermesparametersarray[17]);

        $address = "";
        $company = "";
        if ($consignment->getCompany() != "")
            $company .= $consignment->getCompany();
        if ($consignment->getAddressLine1() != "")
            $address1 = $consignment->getAddressLine1();
        if ($consignment->getAddressLine2() != "")
            $address2 = $consignment->getAddressLine2();
        if ($consignment->getAddressLine3() != "")
            $address3 = $consignment->getAddressLine3();

        $this->pdf->line(8, 82, 70, 82);
        $this->pdf->line(8, 82, 8, 111);
        $this->pdf->line(8, 111, 70, 111);
        $this->pdf->line(70, 111, 70, 82);


        $this->pdf->setFont("helvetica", "B", 9);
        $this->pdf->Text(10, 84, $company);
        $this->pdf->Text(10, 87, $consignment->getContact());
        $this->pdf->Text(10, 90, $address);
        $this->pdf->Text(10, 93, $address1);
        $this->pdf->Text(10, 96, $address2);
        $this->pdf->Text(10, 99, $consignment->getCity());
        $this->pdf->Text(10, 102, "United Kingdom");

        $this->pdf->setFont("helvetica", "B", 12);
        $this->pdf->Text(23, 106, $consignment->getPostcode());
        $this->pdf->setFont("helvetica", "L", 10);

        $warehouse_storage = "";
        $warehouse_location = "";
        $quantity = "";
        $sequence = "";
        $style = array(
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
            'text' => false,
            'font' => 'helvetica',
            'fontsize' => 8,
            'stretchtext' => 4
        );

//        $warehouse_location = $consignment->getRoutingCode();
//        if ($warehouse_location != "") {
//
//            $pos = strpos($warehouse_location, "%%%");
//            if ($pos > 1) {
//
//                $storelocarray = explode("%%%", $warehouse_location);
//                if (isset($storelocarray[0]))
//                    $warehouse_storage = $storelocarray[0];
//                if (isset($storelocarray[1]))
//                    $warehouse_location = $storelocarray[1];
//                if (isset($storelocarray[2]))
//                    $quantity = $storelocarray[2];
//                if (isset($storelocarray[3]))
//                    $sequence = $storelocarray[3];
//            } else {
//
//
//
//                $this->pdf->write1DBarcode($warehouse_location, 'C128', 4, 50, '', 10, 0.5, $style, 'Y');
//                $this->pdf->setFont("helvetica", "b", 10);
//                $this->pdf->Text(8, 58, "W-Loc: " . $text);
//            }
//        }


        $this->pdf->Text(72, 85, "0");
        $this->pdf->Text(72, 88, "0");
        $this->pdf->setFont("helvetica", "L", 9);
        $this->pdf->Text(72, 100, $warehouse_storage);
        $this->pdf->Text(72, 103, $warehouse_location);
        $this->pdf->Text(72, 106, $quantity);
        $this->pdf->Text(72, 109, $sequence);
        $this->pdf->setFont("helvetica", "L", 10);

        $this->pdf->Text(72, 91, "2DAY");


        $this->pdf->write1DBarcode($trackingNo, 'I25', 4, 113, '', 20, 0.5, $style, 'Y');
        $this->pdf->setFont("helvetica", "B", 10);
        $this->centreText(8, 131, 65, $hermes_barcode_display, $this->pdf);
        //$this->pdf->setFont("helvetica", "b", 10);
        //$this->pdf->Text(8, 121, "Ref: " . $consignment->getHawb());

        $txt = ($parcel_idx + 1) . "/" . $parcel_count;
        $this->pdf->Text(72, 94, "Pieces: " . $txt);
        $x = 1;
        $y = 10;
    }

    private function Barcode2D($consignment, $trackingNo) {
        $hermesparameters = $consignment->getOtherRoutingCode();
        $hermesparametersarray = explode("||", $hermesparameters);
        
        $barcodeMatrix = "";

        $barcodeMatrix .= "[)"; // Message Header
        $barcodeMatrix .= "1.3"; // Version
        $barcodeMatrix .= "++";  //Seprator
        $barcodeMatrix .= $trackingNo;  //barcode
        $barcodeMatrix .= "++";  //Seprator
        $barcodeMatrix .= $this->constants["HERMES_CLIENT_ID"];  //client Id
        $barcodeMatrix .= "||";   //Data Seprator
        $barcodeMatrix .= "";  //Child Client Id
        $barcodeMatrix .= "++";  //Seprator
        $barcodeMatrix .= $consignment->getHawb();  //Cusomer Ref1
        $barcodeMatrix .= "||";   //Data Seprator
        $barcodeMatrix .= "";   //CusotmerRef2
        $barcodeMatrix .= "++";  //Seprator
        $barcodeMatrix .= "2DAY"; // Service Name
        $barcodeMatrix .= "||";   //Data Seprator
        $barcodeMatrix .= ""; // Service Name
        $barcodeMatrix .= "||";   //Data Seprator
        $barcodeMatrix .= ""; // Service Name
        $barcodeMatrix .= "++";  //Seprator
        $barcodeMatrix .= $consignment->getContact();  //Name
        $barcodeMatrix .= "||";   //Data Seprator
        $barcodeMatrix .= $consignment->getAddressLine1();  //Address Line 1
        $barcodeMatrix .= "||";   //Data Seprator
        $barcodeMatrix .= $consignment->getAddressLine2();  //Address Line 2
        $barcodeMatrix .= "||";   //Data Seprator
        $barcodeMatrix .= $consignment->getAddressLine3();  //Address Line 3
        $barcodeMatrix .= "||";   //Data Seprator
        $barcodeMatrix .= $consignment->getCity();  //City
        $barcodeMatrix .= "||";   //Data Seprator
        $barcodeMatrix .= "";  //Address line 5
        $barcodeMatrix .= "||";   //Data Seprator
        $barcodeMatrix .= "";  //Address line 6
        $barcodeMatrix .= "||";   //Data Seprator
        $barcodeMatrix .= $consignment->getPostcode();  //Postcode
        $barcodeMatrix .= "||";   //Data Seprator
        $barcodeMatrix .= "GB";  //Country
        $barcodeMatrix .= "||";   //Data Seprator
        $barcodeMatrix .= "";  //Phone
        $barcodeMatrix .= "||";   //Data Seprator
        $barcodeMatrix .= "";  //SMS ALRET
        $barcodeMatrix .= "||";   //Data Seprator
        $barcodeMatrix .= "";  //PIN
        $barcodeMatrix .= "++";  //Seprator
        $barcodeMatrix .= ($consignment->getWeight() * 1000);  //Weight in grams
        $barcodeMatrix .= "||";   //Data Seprator
        $barcodeMatrix .= "10";  //Length
        $barcodeMatrix .= "||";   //Data Seprator
        $barcodeMatrix .= "10";  //Width
        $barcodeMatrix .= "||";   //Data Seprator
        $barcodeMatrix .= "10";  //Height
        $barcodeMatrix .= "||";   //Data Seprator
        $barcodeMatrix .= $consignment->getValue() * 100;  //Value in pence
        $barcodeMatrix .= "||";   //Data Seprator
        $barcodeMatrix .= $consignment->getCurrency();  //Currency
        $barcodeMatrix .= "||";   //Data Seprator
        $barcodeMatrix .= "";   //Type
        $barcodeMatrix .= "++";  //Seprator
        $barcodeMatrix .= $consignment->getNotes();   //Delivery Message
        $barcodeMatrix .= "++";  //Seprator
        $barcodeMatrix .= "004";  //Delivery Method
        $barcodeMatrix .= "||";   //Data Seprator
        $barcodeMatrix .= $hermesparametersarray[17];   //Courier Round Id
        $barcodeMatrix .= "||";   //Data Seprator
        $barcodeMatrix .= "";   //Parcel Shop Id
        $barcodeMatrix .= "||";   //Data Seprator
        $barcodeMatrix .= "";   //Parcel Shop Name
        $barcodeMatrix .= "||";   //Data Seprator
        $barcodeMatrix .= $hermesparametersarray[9];   //Depot Id
        $barcodeMatrix .= "||";   //Data Seprator
        $barcodeMatrix .= $hermesparametersarray[11];   //Van Route Id
        $barcodeMatrix .= "++";  //Seprator
        $barcodeMatrix .= "";   //Client AR Link
        $barcodeMatrix .= "||";   //Data Seprator
        $barcodeMatrix .= "";   //Customer AR Link
        $barcodeMatrix .= "++";   //Data Seprator
        $barcodeMatrix .= $this->constants["HERMES_SENDER_COMPANY"];   //Return Name
        $barcodeMatrix .= "||";   //Data Seprator
        $barcodeMatrix .= $this->constants["HERMES_SENDER_ADD_LINE_1"];;   //Return Address Line 1
        $barcodeMatrix .= "||";   //Data Seprator
        $barcodeMatrix .= $this->constants["HERMES_SENDER_ADD_LINE_2"];;   //Return Address Line 2
        $barcodeMatrix .= "||";   //Data Seprator
        $barcodeMatrix .= $this->constants["HERMES_SENDER_ADD_LINE_3"];;   //Return Address Line 3
        $barcodeMatrix .= "||";   //Data Seprator
        $barcodeMatrix .= $this->constants["HERMES_SENDER_CITY"];;   //Return Address Line 4
        $barcodeMatrix .= "||";   //Data Seprator
        $barcodeMatrix .= "";   //Return Address Line 5
        $barcodeMatrix .= "||";   //Data Seprator
        $barcodeMatrix .= "";   //Return Address Line 6
        $barcodeMatrix .= "||";   //Data Seprator
        $barcodeMatrix .= $this->constants["HERMES_SENDER_POSTCODE"];;   //Return Postcode
        $barcodeMatrix .= "++";  //Seprator
        $barcodeMatrix .= date("dmY");   //Despatch Date
        $barcodeMatrix .= "||";   //Data Seprator
        $barcodeMatrix .= "";   //delviery Date
        $barcodeMatrix .= "(]";   //Message Trailer

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
            'font' => 'helvetica',
            'fontsize' => 8,
            'stretchtext' => 1
        );

        $this->pdf->write2DBarcode($barcodeMatrix, 'DATAMATRIX', 10, 20, 80, 28, $style, '', 'Y');
    }

    function centreText($x, $y, $width, $msg) {
        $leftMargin = ($width - $this->pdf->GetStringWidth($msg)) / 2;
        //
        $this->pdf->setXy($x + $leftMargin, $y, $msg);
        $this->pdf->Write(1, $msg);
    }

    private function createhermesawb($consignment, $licence_plate) {


        $awbno = $licence_plate;
        $routineBarcode = $consignment->getOtherRoutingCode();
        $hermesparametersarray = explode("||", $routineBarcode);
        $hermes_barcode_1_to_7 = $hermesparametersarray[1];

        $strlen = mb_strlen($awbno);
        $charawbwithout_7 = mb_substr($awbno, 1, $strlen - ($strlen - 7));

        $awbno = $charawbwithout_7 . "1";

        $awbno = $hermes_barcode_1_to_7 . $awbno;

        // echo $awbno . "<br>";


        $arr = str_split($awbno);
        $checkdigit = ($arr[0] * 3) + ($arr[1] * 1) + ($arr[2] * 3) + ($arr[3] * 1) + ($arr[4] * 3) + ($arr[5] * 1) + ($arr[6] * 3) + ($arr[7] * 1) + ($arr[8] * 3) + ($arr[9] * 1) + ($arr[10] * 3) + ($arr[11] * 1) + ($arr[12] * 3) + ($arr[13] * 1) + ($arr[14] * 3);




        $checkdigit = $checkdigit % 10;


        if ($checkdigit % 10 == 0) {
            $checkdigit = 0;
        } else {
            $checkdigit = 10 - $checkdigit;
        }

        return $awbno . $checkdigit;
    }

    private function getShipmentRecord(Consignment $consignment, $constant) {
        $handling = $this->serviceValues->getCode();
        if ($handling == "STHRM0002") {
            $handling = "2";
        }
        $hermesparametersarray = array();
        $company = $consignment->getCompany();
        $hermesparameters = $consignment->getOtherRoutingCode();
        $hermesparametersarray = explode("||", $hermesparameters);
        if ($company == "")
            $company = $consignment->getContact();
        //
        $record = "08";  //Pcl-Rec-Record-Type
        $record .= $this->fld(3, $constant["HERMES_CLIENT_ID"]); //Pcl-Rec-Client-ID 854

        $record .= $this->fld(3, "");  //Pcl-Rec-Client-Child-ID
        $record .= $this->fld(32, $constant["HERMES_CLIENT_NAME"]);  //Pcl-Rec-Client-Name

        $record .= $this->fld(32, ""); // 5  //Pcl-Rec-Client-Child-Name
        $record .= $this->fld(2, $hermesparametersarray[3]);  //Pcl-Rec-Carrier-ID
        $record .= $this->fld(3, $hermesparametersarray[4]); //Pcl-Rec-MOD-ID
        $record .= $this->fld(18, $hermesparametersarray[5]);   //Pcl-Rec-MOD-Name

        $record .= $this->fld(25, $consignment->getAWB()); //Pcl-Rec-Hermes-Barcode-Number

        if (trim($hermesparametersarray[5]) == "UPS STD") {
            $trackingNumber = $consignment->getAwb();
        } else {
            $trackingNumber = "";
        }



        $record .= $this->fld(30, $trackingNumber); //Pcl-Rec-Carrier-Barcode-Number-1
        $record .= $this->fld(30, ""); //Pcl-Rec-Carrier-Barcode-Number-2
        $record .= $this->fld(25, ""); //Pcl-Rec-Linked-Barcode-Number
        //
        $record .= $this->fld(32, $company); // 6 Your customers’ name see notes.

        $record .= $this->fld(32, $consignment->getAddressLine1()); // 6
        $record .= $this->fld(32, $consignment->getAddressLine2());
        $record .= $this->fld(32, $consignment->getCity());
        $record .= $this->fld(32, "");
        $record .= $this->fld(32, "");
        $record .= $this->fld(32, "");
        $record .= $this->fld(8, $consignment->getPostcode());
        $record .= $this->fld(15, $consignment->getTelephone());
        $record .= $this->fld(15, "");   //Pcl-Rec-Customer-Work-Phone-No
        $record .= $this->fld(15, "");  //Pcl-Rec-Customer-Mobile-Phone-No
        $record .= $this->fld(80, ""); //Pcl-Rec-Customer-E-Mail-Address

        $record .= $this->fld(8, $hermesparametersarray[7]); //Pcl-Rec-Sort-Point-1-Type
        $record .= $this->fld(8, $hermesparametersarray[8]); //Pcl-Rec-Sort-Point-1-Name
        $record .= $this->fld(8, $hermesparametersarray[9]); //Pcl-Rec-Sort-Point-1-Code

        $record .= $this->fld(8, $hermesparametersarray[10]); //Pcl-Rec-Sort-Point-2-Type
        $record .= $this->fld(8, $hermesparametersarray[11]); //Pcl-Rec-Sort-Point-2-Name
        $record .= $this->fld(8, $hermesparametersarray[12]); //Pcl-Rec-Sort-Point-2-Code

        $record .= $this->fld(8, $hermesparametersarray[13]); //Pcl-Rec-Sort-Point-3-Type
        $record .= $this->fld(8, $hermesparametersarray[14]); //Pcl-Rec-Sort-Point-3-Name
        $record .= $this->fld(8, $hermesparametersarray[15]); //Pcl-Rec-Sort-Point-3-Code


        $record .= $this->fld(8, $hermesparametersarray[16]); //Pcl-Rec-Sort-Point-4-Type
        $record .= $this->fld(8, $hermesparametersarray[17]); //Pcl-Rec-Sort-Point-4-Name
        $record .= $this->fld(8, $hermesparametersarray[18]); //Pcl-Rec-Sort-Point-4-Code

        $record .= $this->fld(8, $hermesparametersarray[19]); //Pcl-Rec-Sort-Point-5-Type
        $record .= $this->fld(8, $hermesparametersarray[20]); //Pcl-Rec-Sort-Point-5-Name
        $record .= $this->fld(8, $hermesparametersarray[21]); //Pcl-Rec-Sort-Point-5-Code
        //
        //$parcel_list = $consignment->getParcels();
        //
        $record .= $this->fld(7, $consignment->getWeight() * 1000, true); //Pcl-Rec-Parcel-Weight
        $record .= $this->fld(4, "0000"); //Pcl-Rec-Parcel-Length
        $record .= $this->fld(4, "0000"); // Pcl-Rec-Parcel-Width
        $record .= $this->fld(4, "0000"); // 15 - Pcl-Rec-Parcel-Depth
        $record .= $this->fld(4, "0000"); // 15 - Pcl-Rec-Parcel-Girth
        $record .= $this->fld(4, "0000"); // 15 - Pcl-Rec-Parcel-Combined-Dimension
        $record .= $this->fld(9, "000000000"); // 21 - Pcl-Rec-Parcel-Volume
        $record .= $this->fld(8, $consignment->getValue() * 100, true); // 21 -  Pcl-Rec-Parcel-Value
        $record .= $this->fld(1, ""); //Pcl-Rec-Fragile-Flag
        $record .= $this->fld(1, "");  //Pcl-Rec-Consigned-Flag
        $record .= $this->fld(1, ""); //Pcl-Rec-Installation-Flag
        $record .= $this->fld(1, "");  //Pcl-Rec-Hanging-Garment-Flag
        $record .= $this->fld(1, ""); //Pcl-Rec-Theft-Risk-Flag
        $record .= $this->fld(1, ""); //Pcl-Rec-Multiple-Parts-Flag
        $record .= $this->fld(1, "");  //Pcl-Rec-Stated-Day-Flag
        $record .= $this->fld(1, "");  //Pcl-Rec-Stated-Time-Flag
        $record .= $this->fld(1, $handling); //Pcl-Rec-Delivery-Service-Flag
        if ($handling != "2S")
            $record .= $this->fld(1, "");   //Pcl-Rec-Signature-Flag
        else
            $record .= $this->fld(1, "Y");   //Pcl-Rec-Signature-Flag
        $record .= $this->fld(27, ""); //	Pcl-Rec-Filler-1
        $record .= $this->fld(9, ""); //Pcl-Rec-Country-Code
        $record .= $this->fld(5, ""); //Pcl-Rec-Customer-Alert-Indicator
        $record .= $this->fld(4, "");  //Rec-Customer-Sms-Alert-Group
        $record .= $this->fld(46, ""); //Pcl-Rec-Filler-1a
        $record .= $this->fld(1, "");  //Pcl-Rec-Consigned-Pin-Service-Flag
        $record .= $this->fld(1, "");  //PPcl-Rec-Delivery-Content-Check-Flag
        $record .= $this->fld(1, "");  //Pcl-Rec-Store-Delivery-Flag
        $record .= $this->fld(1, "");  //Pcl-Rec-Household-Signature-Flag
        $record .= $this->fld(1, "");  //Pcl-Rec-Mid-Day-Delivery-Flag
        $record .= $this->fld(4, ""); //Req-Rec-Filler-3
        $record .= $this->fld(1, ""); //Pcl-Rec-Catalogue-Flag
        $record .= $this->fld(2, "01"); //Pcl-Rec-Number-of-Parts
        $record .= $this->fld(2, "01"); //Pcl-Rec-Number-of-Items
        $record .= $this->fld(32, $consignment->getDescription());
        $record .= $this->fld(8, ""); //Pcl-Rec-Parcel-Origin

        $record .= $this->fld(20, $consignment->getHawb());
        $record .= $this->fld(20, "");  //Pcl-Rec-Customer-Reference-2
        $record .= $this->fld(32, ""); //Pcl-Rec-Delivery-Message

        $record .= $this->fld(4, "1234"); //Pcl-Rec-Consigned-Pin-Number
        $record .= $this->fld(28, ""); //Pcl-Rec-System-Message
        $record .= $this->fld(32, ""); //Pcl-Rec-Special-Instructions-1
        $record .= $this->fld(32, "");  //Pcl-Rec-Special-Instructions-2
        $record .= $this->fld(8, "");  //Pcl-Rec-Required-Delivery-Date //$this->fld(8, date("dmY", time()));
        $record .= $this->fld(4, "");  //date("Hm", time())	//Pcl-Rec-Required-Delivery-Time
        $record .= $this->fld(1, ""); //Pcl-Rec-Delivery-Instruction-Flag
        $dispatchDate = time() + 86400;
        $record .= $this->fld(8, date("dmY", $dispatchDate)); //Pcl-Rec-Expected-Despatch-Date
        $record .= $this->fld(4, ""); //Pcl-Rec-Supplier-Code
        $record .= $this->fld(20, ""); //Pcl-Rec-Filler-4
        $record .= "\n"; //Pcl-Rec-Filler-4
        //

        return $record;
    }

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

    private function sendBookings($ftpConstants, $run_number) {
        require_once(SETTING_DIR_REMOTE . "includes/3rdparty/Net/SFTP.php");
        if (sizeof($this->record_array) > 0) {
            $path = SETTING_DIR_ASSETS . "data_send/hermes_booking/" . date("Y_m_d") . "/";
            if (!file_exists($path))
                @mkdir($path, 0777, TRUE);

            $file_path = $path . $this->booking_file;
            chmod($path, 0777);
            $record_count = sizeof($this->record_array) + 2;
            // create file
            $file_handle = @fopen($file_path, 'w');
            fwrite($file_handle,$this->getHeaderRecord($record_count, $run_number, $ftpConstants));
            $record	=	implode('',$this->record_array);
            fwrite($file_handle,"$record");
            fwrite($file_handle, $this->getFooterRecord($record_count, $ftpConstants));
            // close file
            fclose($file_handle);


            if (isset($ftpConstants['HERMES_FTP_SITE']) && trim($ftpConstants['HERMES_FTP_SITE']) != '') {
                $SETTING_FTP_USER = $ftpConstants['HERMES_FTP_USER'];
                $SETTING_FTP_PASSWORD = $ftpConstants['HERMES_FTP_PASSWORD'];
                $SETTING_FTP_SITE = $ftpConstants['HERMES_FTP_SITE'];

                $ssh = new Net_SFTP($SETTING_FTP_SITE);
                if (!$ssh->login($SETTING_FTP_USER, $SETTING_FTP_PASSWORD)) {
                    $message = 'There is an error while uploading the file to Hermes FTP. File name is ' . $file_path;
                }
                $remote_file_path = "./In/" . $this->booking_file . ".txt";
                $isUpload = $ssh->put($remote_file_path, $file_path, NET_SFTP_LOCAL_FILE);
                $returnparam = $isUpload;
            }
            else {
                $message = 'There is an error while uploading the file to DAC FTP. DAC constants not found in the system. file name is ' . $remote_file_path;
                $returnparam = false;
            }
            if (trim($message) != '') {
                echo $message;
                $to = 'itsupport@oneworldexpress.com';
                $subject = 'SmartTrack Hermes data send to carrier issue';

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

    private function getHeaderRecord($record_count, $run_number, $constant) {
        $record = "01";

        $record .= $this->fld(3, $constant["HERMES_CLIENT_ID"]);
        $record .= $this->fld(32, $constant["HERMES_CLIENT_NAME"]);
        $record .= $this->fld(5, $run_number, true);
        $record .= $this->fld(8, date("dmY", time())); // collection set to today!
        $record .= $this->fld(4, date("hm", time())); // collection set to today!
        $record .= $this->fld(20, "");
        //$record .= $this->fld(8, $record_count, true);
        //
        //$record .= $this->fld(6, "2", true);
        //$record .= $this->fld(6, "103", true);
        $record .= "\n";
        //
        return $record;
    }

    private function getFooterRecord($record_count, $constant) {
        //$record = "\n";
        $record .= "99";
        $record .= $this->fld(3, $constant["HERMES_CLIENT_ID"]);
        $record .= $this->fld(7, $record_count - 2, true);
        $record .= $this->fld(20, "");

        return $record;
    }

    public function recycledShipment($consignment) {
        $output["STATUS"] = "SUCCESS";
        return $output;
    }

}
