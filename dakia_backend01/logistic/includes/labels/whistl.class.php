<?php

class Whistl implements CarrierService {

    private $pdf;
    private $serviceValues = null;
    private $constants = null;
    private $user = null;
    private $country = null;
    private $recordArray = null;
    private $booking_file = null;

    public function __construct() {
        
    }

    public function validation(Consignment $consignment, Services $service, Country $country) {
        
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
        if (trim(@$this->constants['WHISTL_SHIPPER_POSTCODE']) == '' || trim(@$this->constants['WHISTL_SHIPPER_COUNTRY']) == '' || trim(@$this->constants['WHISTL_SHIPPER_ADDRESS_LINE1']) == '') {
            $output['STATUS'] = 'ERROR';
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
                $licence_plate = $resultArray["PREFIX"] . $consignment->getId() . $resultArray["SUFIX"];
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
        
    }

    public function sendData($tracking_numbers = array()) {
        $output = [];
        $agentServiceArray = [];
        $consignmentData = new ConsignmentFilter();
        $consignmentData->addFilter("     s.carrier_id= '174'", "servicefilter");
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
                     con.region 'country_region', c.service_id, c.weight, c.hawb, c.description, c.company, c.country_id,c.awb,c.date_created,c.value,c.date_booked,
                      c.contact, c.address_line_1, c.address_line_2, c.city, c.postcode, c.telephone, c.other_routing_code, routing_code_eur, c.number_pieces ");

                    if (count($consignmentShipmentData) > 0) {
                        $consignmentIdArray = [];
                        foreach ($consignmentShipmentData as $consignmentItemData) {

                            $consignmentId = $consignmentItemData->getConsignmentId();
                            $consignmentIdArray[] = $consignmentId;
                            $carrierId = $consignmentItemData->getCarrierId();
                            $this->recordArray[] = $this->exportTNTRow($consignmentItemData, $this->constants[$serviceid]);
                        }
                        $carrierId = $this->serviceValues->getCarrierId();
                        $agentid = trim($agentServiceArray['agent'][$key]);

                        $run_number = CarrierDataFileLog::generateRunNumber($carrierId, $agentid, true);
                        $serialNumber = $run_number;
                        $this->booking_file = "L123551" .$serialNumber. $this->datecode();

                        $carrierDataFileLog = new CarrierDataFileLog();
                        $carrierDataFileLog->setCarrierId($carrierId);
                        $carrierDataFileLog->setAgentId($agentid);
                        $carrierDataFileLog->setFileName($this->booking_file);
                        $carrierDataFileLog->setRunNumber($run_number);
                        $carrierDataFileLog->save();
                        


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

        $image = realpath("../images/logo.jpg");
        $this->pdf->image($image, 40, 4, 49);


        $this->pdf->line(3, 58, 98, 58);
        $this->pdf->line(3, 3, 98, 3);
        $this->pdf->line(98, 3, 98, 23.8);
        $this->pdf->line(3, 3, 3, 30);
        $this->pdf->line(35, 3, 35, 23.8);


        $cnRoy = '../images/c9-10002.jpg';
        $this->pdf->Image($cnRoy, $x1 + 10, $y + 5, 15, 18);

        $this->pdf->setFont("helvetica", "b", 10);
        $this->pdf->Text(15, 61, ucfirst($this->user->getUserName()));

        if ($consignment->getValue() >= 0 && $consignment->getValue() < 15) {
            $this->pdf->Text(56, 61, "L");
        } else
        if ($consignment->getValue() >= 15 && $consignment->getValue() < 135) {
            $this->pdf->Text(56, 61, "M");
        } else
        if ($consignment->getValue() >= 135) {
            $this->pdf->Text(56, 61, "H");
        }


        //$this->pdf->Text(62, 61, $this->user->getUserName());

        $this->pdf->Line(46, 58, 46, 70);
        $this->pdf->Line(68, 58, 68, 70);

        $this->pdf->Text(80, 61, "UK");

        $this->pdf->Text(5, 72, "Delivery Details:");

        $this->pdf->Text(10, 136.5, "RM|L");


        $this->pdf->setFont("helvetica", "L", 9);
        $this->pdf->Text(45, 18.5, "www.oneworldexpress.com");

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
            'text' => true,
            'font' => 'helvetica',
            'fontsize' => 8,
            'stretchtext' => 1
        );
        $this->pdf->write1DBarcode($consignment->getHawb(), 'C128', 3, 115, '', 20, 2, $style, 'Y');

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


        $company = "";
        if ($consignment->getCompany() != "")
            $company = $consignment->getCompany();
        if ($consignment->getAddressLine1() != "")
            $address1 = $consignment->getAddressLine1();
        if ($consignment->getAddressLine2() != "")
            $address2 = $consignment->getAddressLine2();
        if ($consignment->getAddressLine3() != "")
            $address3 = $consignment->getAddressLine3();


        $addressContents = $company . "\r\n";
        $addressContents = $consignment->getContact() . "\r\n";
        $addressContents .= $address1 . "\r\n";
        $addressContents .= $address2 . "\r\n";
        $addressContents .= $address3 . "\r\n";
        $addressContents .= $consignment->getCity() . "\r\n";
        $addressContents .= $consignment->getPostcode() . "\r\n";
        $addressContents .= $consignment->getCountry() . "\r\n";
        $this->pdf->MultiCell(80, 40, $addressContents, 0, 'L', 0, 1, 5, 78, true);

        $this->pdf->setFont("helvetica", '', 8);
        //$this->pdf->Text(55, 78, $consignment->getHawb());
        $this->pdf->Text(5, 103, "Email: " . $consignment->getEmail());
        $this->pdf->Text(55, 97, "Tel: " . $consignment->getTelephone());
        $this->pdf->MultiCell(80, 30, $consignment->getDescription() . "\r\n" . $consignment->getReference(), 0, 'L', 0, 1, 5, 107, true);

        $this->pdf->setFont("helvetica", "L", 11);
        $this->pdf->setFont("helvetica", "B", 8);
        $this->pdf->Text(34, 133, "Return to:");
        $this->pdf->setFont("helvetica", "L", 8);
        $this->pdf->Text(34, 136, $this->constants['WHISTL_SHIPPER_ADDRESS_LINE1'] . " " . $this->constants['WHISTL_SHIPPER_ADDRESS_LINE2']);
        $this->pdf->Text(34, 139, $this->constants['WHISTL_SHIPPER_ADDRESS_LINE3'] . " " . $this->constants['WHISTL_SHIPPER_CITY'] . " " . $this->constants['WHISTL_SHIPPER_POSTCODE']);

        $shipper_country = $this->constants['WHISTL_SHIPPER_COUNTRY'];
        if ((int) $shipper_country > 0) {
            $shipperCountry = new Country($shipper_country);
            $sCountry = $shipperCountry->getName();
            $sIso = $shipperCountry->getIso();
        } else {
            $sCountry = $shipper_country;
        }
        $this->pdf->Text(34, 142, $sCountry . " " . $sIso);
    }

    private function exportTNTRow($consignment) {


        $service = "L";

        $record .= "RM" . ",";
        $record .= "L123551" . ",";
        $record .= "1" . ",";
        $record .= "1" . ",";
        $datecollection = date("d/m/Y", strtotime($consignment->getDateCreated()));
        //echo $datecollection; exit;
        if ($datecollection == '0000-00-00' || $datecollection == '01-01-1970') {
            $record .= "" . ",";
        } else {
            $record .= $datecollection . ",";
        }
        
        $postCode = ($this->formatUKPostcode($consignment->getPostcode()));
        
        $record .= preg_replace('/[\$,]/', '', $postCode) . ",";
        $record .= preg_replace('/[\$,]/', '', utf8_encode($consignment->getAddressLine1())) . ",";
        $record .= preg_replace('/[\$,]/', '', utf8_encode($consignment->getAddressLine2())) . ",";
        $record .= preg_replace('/[\$,]/', '', utf8_encode($consignment->getCity())) . ",";
        $record .= preg_replace('/[\$,]/', '', '') . ",";
        $record .= $consignment->getAwb() . ",";
        $record .= "PW72FSGB" . ",";
        $record .= $service . ",";

        if ($consignment->getWeight() != "0.00") {
            $weight = $consignment->getWeight() . ",";
            $record .= ($weight * 1000) . ",";
        } else {
            $record .= "200" . ",";
        }
        $record .= preg_replace('/[\$,]/', '', utf8_encode($consignment->getContact())) . ",";
        $telephone = ($consignment->getTelephone() != '' ? $consignment->getTelephone() : "02088676060");
        
        $record .= preg_replace('/[\$,]/', '', utf8_encode($telephone)) . ",";
        $record .= preg_replace('/[\$,]/', '', utf8_encode($consignment->getEmail())) . ",";
        $record .= "" . ","; // carrier product id 
        $record .= "" . ",";
        $record .= "" . ",";
        $record .= $consignment->getHawb() ;


        //
        $record .= "\r\n";
        //
        return $record;
    }
    
    private function formatUKPostcode($postcode)
	{
		$postcode = strtoupper(preg_replace("/[^A-Za-z0-9]/", '', $postcode));
	
		if(strlen($postcode) == 5) {
			$postcode = substr($postcode,0,2).' '.substr($postcode,2,3);
		}
		elseif(strlen($postcode) == 6) {
			$postcode = substr($postcode,0,3).' '.substr($postcode,3,3);
		}
		elseif(strlen($postcode) == 7) {
			$postcode = substr($postcode,0,4).' '.substr($postcode,4,3);
		}
	
		return $postcode;
	}

    private function sendBookings($ftpConstants) {
        require_once(SETTING_DIR_REMOTE . "includes/3rdparty/Net/SFTP.php");
        $isUpload = true;
        if (sizeof($this->recordArray) > 0) {

            $file_path = SETTING_DIR_ASSETS . "data_send/whistl_booking/" . date("Y_m_d") . "/";
            if (!file_exists($file_path))
                @mkdir($file_path, 0777, TRUE);
            $filename = $this->booking_file;
            chmod($file_path, 0777);


           
            $file_path_TNT = $file_path . $filename . ".csv";
            $file_handle_TNT_post = fopen($file_path_TNT, 'w');

            fwrite($file_handle_TNT_post . "\r\n");
            foreach ($this->recordArray as $record) {

                fwrite($file_handle_TNT_post, $record);
            }
            fclose($file_handle_TNT_post);
            //echo file_get_contents($file_path_TNT);
            //die;
            if (isset($ftpConstants['WHISTL_FTP_SITE']) && trim($ftpConstants['WHISTL_FTP_SITE']) != '') {
                $ftpconnection = new Net_SFTP(trim($ftpConstants['WHISTL_FTP_SITE']), 22);
                $ftpconnection->login($ftpConstants['WHISTL_FTP_USER'], $ftpConstants['WHISTL_FTP_PASSWORD']);        
                
                $remote_file_path = "/inbox/" . $filename . ".pnp";
                $isUpload = $ftpconnection->put($remote_file_path, $file_path_TNT, FTP_ASCII) ;
                
            }
        }
        if ($isUpload === false) {
            mail("itsupport@oneworldexpress.com", "WHISTL LOGIN FAILED", "WHISTL LOGIN FAILED" . $this->booking_file);
        }
        return $isUpload;
    }

    private function datecode() {
        $today = date("d-m-Y");
        $date = explode("-", $today);
        $day = $date[0];
        $month = $date[1];
        $datecode = "";

        if ($day != "") {
            if ($day == "01")
                $day = "A";
            if ($day == "02")
                $day = "B";
            if ($day == "03")
                $day = "C";
            if ($day == "04")
                $day = "D";
            if ($day == "05")
                $day = "E";
            if ($day == "06")
                $day = "F";
            if ($day == "07")
                $day = "G";
            if ($day == "08")
                $day = "H";
            if ($day == "09")
                $day = "I";
            if ($day == "10")
                $day = "J";
            if ($day == "11")
                $day = "K";
            if ($day == "12")
                $day = "L";
            if ($day == "13")
                $day = "M";
            if ($day == "14")
                $day = "N";
            if ($day == "15")
                $day = "O";
            if ($day == "16")
                $day = "P";
            if ($day == "17")
                $day = "Q";
            if ($day == "18")
                $day = "R";
            if ($day == "19")
                $day = "S";
            if ($day == "20")
                $day = "T";
            if ($day == "21")
                $day = "U";
            if ($day == "22")
                $day = "V";
            if ($day == "23")
                $day = "W";
            if ($day == "24")
                $day = "X";
            if ($day == "25")
                $day = "Y";
            if ($day == "26")
                $day = "Z";
            if ($day == "27")
                $day = "2";
            if ($day == "28")
                $day = "3";
            if ($day == "29")
                $day = "4";
            if ($day == "30")
                $day = "5";
            if ($day == "31")
                $day = "6";
            $datecode = $day;
        }
        if ($month != "") {
            if ($month == "01")
                $month = "A";
            if ($month == "02")
                $month = "B";
            if ($month == "03")
                $month = "C";
            if ($month == "04")
                $month = "D";
            if ($month == "05")
                $month = "5";
            if ($month == "06")
                $month = "F";
            if ($month == "07")
                $month = "G";
            if ($month == "08")
                $month = "H";
            if ($month == "09")
                $month = "I";
            if ($month == "10")
                $month = "J";
            if ($month == "11")
                $month = "K";
            if ($month == "12")
                $month = "L";
            $datecode .= $month;
        }
        return $datecode;
    }

    public function recycledShipment($consignment) {
        $output["STATUS"] = "SUCCESS";
        return $output;
    }

}
