<?php
include_classes([
    'carrierdatafilelog.class',
    'carrierdatafilelogfilter.class' ,
    'parcelforcedepodetail.class',
    'parcelforcedepodetailfilter.class',
    'parcelforcehubdetails.class',
    'parcelforcehubdetailsfilter.class'
    ]);
class ParcelForce implements CarrierService {

    private $pdf;
    private $serviceValues = null;
    private $agentValues = null;
    private $constants = null;
    private $user = null;
    private $country = null;
    private $record_array = null;
    private $booking_file = null;

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
        if (trim(@$this->constants['PARCELFORCE_SHIPPER_COMPANY']) == '' || trim(@$this->constants['PARCELFORCE_SHIPPER_ADDRESS_LINE_1']) == '' || trim(@$this->constants['PARCELFORCE_SHIPPER_POSTCODE']) == '') {
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
                else {
                    $pieces = str_pad($parcel_idx + 1, 3, "0", STR_PAD_LEFT);
                    $checkdigit = LicencePlate::modParcelForce($resultArray["RANGE"]);
                    $licence_plate = $resultArray["PREFIX"] . $resultArray["RANGE"] . $checkdigit . $pieces;
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
            $page_size = array(152.4, 101.6);
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
        
    }

    public function sendData($tracking_numbers = array()) {
        $output = [];
        $agentServiceArray = [];
        $consignmentData = new ConsignmentFilter();
        $consignmentData->addFilter("     s.carrier_id= '164'", "servicefilter");
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
                     con.region 'country_region', c.service_id, c.weight, c.hawb, c.description, c.company, c.country_id,c.awb,c.date_created,c.value,c.number_pieces,
                      c.contact, c.address_line_1, c.address_line_2, c.city, c.postcode, c.telephone, c.other_routing_code, routing_code_eur,pc.tracking_number, c.currency ");

                    if (count($consignmentShipmentData) > 0) {
                        $consignmentIdArray = [];
                        $run_number = sprintf('%04d', CarrierDataFileLog::generateRunNumber($carrierId, $agentid));
                        $this->booking_file = "PGO1" . $run_number;

                        $carrierDataFileLog = new CarrierDataFileLog();
                        $carrierDataFileLog->setCarrierId($carrierId);
                        $carrierDataFileLog->setAgentId($agentid);
                        $carrierDataFileLog->setFileName($this->booking_file);
                        $carrierDataFileLog->setRunNumber($run_number);
                        $carrierDataFileLog->save();

                        foreach ($consignmentShipmentData as $consignmentItemData) {

                            $consignmentId = $consignmentItemData->getConsignmentId();
                            $consignmentIdArray[] = $consignmentId;
                            $carrierId = $consignmentItemData->getCarrierId();
                            $this->record_array[] = $this->getShipmentRecord($consignmentItemData);
                        }
                        $carrierId = $this->serviceValues->getCarrierId();
                        $agentid = trim($agentServiceArray['agent'][$key]);

                        if ($this->sendBookings($this->constants[$serviceid], $run_number)) {
                            if (!empty($consignmentIdArray)) {

                                $sql = "UPDATE consignment SET send_courier_data =1,  booked_file_id = '" . $this->booking_file . "' 
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



        $handling = $this->serviceValues->getCode();
        if($handling == "STPFR0024")
            $handling = "24";


        $serviceCode = str_replace("S", "", $handling);

        $this->pdf->setFont("Arial", "", 70);
        $this->pdf->setTextColor(0, 0, 0);
        $this->pdf->SetFillColor(255, 255, 255);
        $this->pdf->setXy(3, 1);
        $this->pdf->Cell(38, 3, $serviceCode, 1, 0, "C", true);

        $this->pdf->setFont("Arial", "", 12);
        $this->pdf->Text(40.2, 22.5, $parcel_idx + 1 . " of " . $parcel_count);

        $postcode = str_replace(" ", "", $consignment->getPostcode());
        $postcode_lenght = strlen($postcode);
        if ($postcode_lenght == 5)
            $first_part_postcode = substr(trim($postcode), 0, 2);
        else if ($postcode_lenght == 6)
            $first_part_postcode = substr(trim($postcode), 0, 3);
        else if ($postcode_lenght == 7)
            $first_part_postcode = substr(trim($postcode), 0, 4);

        $ParcelForceDepoDetailFilter = new ParcelForceDepoDetailFilter();
        $ParcelForceDepoDetailFilter->addFieldFilter('postcode', $first_part_postcode);
        $DepoList = $ParcelForceDepoDetailFilter->getColumnList('depo_name,depo_short_name,postcode,route_number');


        if (count($DepoList) > 0) {
            $DepoList = $DepoList[0];
            $depo_short_name = $DepoList->getDepoShortName();
            $route_number = $DepoList->getRouteNumber();
            $ParcelForceHubDetailsFilter = new ParcelForceHubDetailsFilter();
            $ParcelForceHubDetailsFilter->addFieldFilter('depo_name', $DepoList->getDepoName());
            $HubFilter = $ParcelForceHubDetailsFilter->getList();
            if (count($HubFilter) > 0) {
                $HubFilter = $HubFilter[0];
                $depo_number = $HubFilter->getDepoNumber();
                if ($handling == '24' || $handling == 'S09' || $handling == 'S10' || $handling == 'AM' || $handling == 'PM') {
                    $hub = $HubFilter->getMonHub24();
                    $chute = $HubFilter->getMonChute24();
                } else if ($handling == '48') {
                    $hub = $HubFilter->getMonHub48();
                    $chute = $HubFilter->getMonChute48();
                }
            }
        } else {
            $output['STATUS'] = 'ERROR';
            $output['MESSAGE'] = 'Service is not available at this Postcode.';
        }
        $this->pdf->setFont("Arial", "B", 15);
        $this->pdf->setTextColor(255, 255, 255);
        $this->pdf->SetFillColor(0, 0, 0);
        $this->pdf->setXy(43.5, 6.1);
        $this->pdf->Cell(14, 3, $hub, 1, 0, "C", true);

        $this->pdf->setXy(43.5, 12.0);
        $this->pdf->Cell(14, 3, $chute, 1, 0, "C", true);

        $image = realpath("../images/parcel-force-logo.jpg");
        $this->pdf->image($image, 70.5, 6, 30);

        $this->pdf->setTextColor(0, 0, 0);
        $this->pdf->setFont("Arial", "", 8);

        $this->pdf->Text(52.5, 19, $licence_plate);


        $pickupDate = time();
        $this->pdf->Text(60, 30.7, date("d/m/Y, D", time()));

        $this->pdf->setFont("Arial", "", 12);
        $postcodenum = $depo_short_name . $depo_number . $route_number;
        $this->pdf->write1DBarcode("*" . $first_part_postcode . "*", 'C128', 60, 38, '', 20);
        $this->pdf->Text(57, 60, $depo_short_name);
        $this->pdf->setTextColor(255, 255, 255);
        $this->pdf->SetFillColor(0, 0, 0);
        $this->pdf->setXy(72, 60);
        $this->pdf->Cell(13, 3, $depo_number, 1, 0, "C", true);
        $this->pdf->setTextColor(0, 0, 0);
        $this->pdf->Text(85, 60, $route_number);

        $this->pdf->setFont("Arial", "L", 11);

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



        $this->pdf->setFont("Arial", "L", 8);
        $this->pdf->Text(4, 67, "Special Instructions:");
        $this->pdf->Text(4, 70, $consignment->getNotes());






        $this->pdf->Text(25, 85, $licence_plate);
        //DRAW BLACK BACKGROUND
        $this->pdf->setTextColor(255, 255, 255);
        $this->pdf->SetFillColor(0, 0, 0);
        $this->pdf->setXy(60, 85);
        $this->pdf->Cell(20, 5, "", 1, 0, "C", true);

        $this->pdf->setTextColor(255, 255, 255);
        $this->pdf->SetFillColor(0, 0, 0);
        $this->pdf->setXy(85, 85);
        $this->pdf->Cell(5, 5, $serviceCode, 1, 0, "C", true);

        $this->pdf->setTextColor(0, 0, 0);

        $this->pdf->setFont("Arial", "B", 12);

        $this->pdf->line(3, 66, 98, 66);
        $this->pdf->line(3, 66, 3, 77);
        $this->pdf->line(3, 77, 98, 77);
        $this->pdf->line(98, 66, 98, 77);

        $this->pdf->setFont("Arial", "", 10);
        $this->pdf->Text(4, 35, $company);
        $this->pdf->Text(4, 39, $consignment->getContact());
        $this->pdf->Text(4, 43, $address1);
        $this->pdf->Text(4, 47, $address2);
        $this->pdf->Text(4, 51, $address3);

        $this->pdf->Text(4, 54, $consignment->getCity());

        $this->pdf->write1DBarcode($licence_plate, 'C128', 27, 92, 55, 38);

        $this->pdf->setFont("Arial", "", 15);
        $barcode_prefix = substr($licence_plate, 0, 2);
        $barcode = substr($licence_plate, 2, 9);
        $barcode_suffice = substr($licence_plate, -3);

        $this->pdf->Text(29, 129, $barcode_prefix);
        $this->pdf->Text(39, 129, $barcode);
        $this->pdf->Text(72, 129, $barcode_suffice);

        $this->pdf->line(3, 135, 98, 135);
        $this->pdf->line(3, 135, 3, 149);
        $this->pdf->line(3, 149, 98, 149);
        $this->pdf->line(98, 135, 98, 149);

        $this->pdf->setFont("Arial", "", 14);
        $this->pdf->Text(4, 60, $consignment->getPostcode());

        $this->pdf->StartTransform();
        $this->pdf->Rotate(90, 70, 80);

        $this->pdf->setFont("Arial", "", 10);

        $this->pdf->Text(20, 102, "Customer Ref: " . $consignment->getHawb());
        $this->pdf->setFont("Arial", "L", 10);

        $this->pdf->Text(20, 14, "FROM: ");
        $this->pdf->setFont("Arial", "L", 7);

        $this->pdf->Text(32, 14, $this->constants["PARCELFORCE_SHIPPER_COMPANY"]);
        $this->pdf->Text(32, 17, $this->constants["PARCELFORCE_SHIPPER_ADDRESS_LINE_1"]);
        $this->pdf->Text(32, 20, $this->constants["PARCELFORCE_SHIPPER_ADDRESS_LINE_2"]);
        $this->pdf->Text(32, 23, $this->constants["PARCELFORCE_SHIPPER_CITY"]);
        $this->pdf->Text(32, 26, $this->constants["PARCELFORCE_SHIPPER_POSTCODE"]);


        $this->pdf->StopTransform();

        $this->pdf->setFont("Arial", "", 8);
        $this->pdf->Text(5, 137, "Customer");
        $this->pdf->Text(5, 141, "Use Only");
        $this->pdf->setFont("Arial", "B", 14);
        $this->pdf->Text(88, 137, $serviceCode);
    }

    private function getShipmentRecord(Consignment $consignment) {


        $record = "2" . "+";
        $record .= $this->fld(2, "02") . "+";

        $awb = substr($consignment->getAwb(), 2, 9);

        $record .= $this->fld(9, $awb) . "+";
        $handling = $this->serviceValues->getCode();
        if ($handling == "S09")
            $service = "S09";
        else if ($handling == "S10")
            $service = "S10";
        else if ($handling == "AM")
            $service = "S12";
        else if ($handling == "PM")
            $service = "SPM";
        else if ($handling == "STPFR0024")
            $service = "SND";
        else if ($handling == "48")
            $service = "SUP";



        $record .= $service . "+";
//        if ($consignment->getHvlv() == 'Y') {
//            $record .= "ESAT" . "+";
//        } else {
        $record .= "+";
//        }


        $record .= "+";
        $record .= "+";
        $record .= $consignment->getHawb() . "+";
        $record .= "1" . "+" . "+";
        $record .= number_format($consignment->getWeight(), 2) * 100 . "+";
        $record .= $consignment->getNumberPieces() . "+";
        $record .= "+";



        $company = $consignment->getCompany();
        if ($company == "")
            $company = $consignment->getContact();

        $record .= $company . "+"; // 6 Your customers’ name see notes.

        $record .= $consignment->getAddressLine1() . "+"; // 6
        $record .= $consignment->getAddressLine2() . "+";
        $record .= $consignment->getAddressLine3() . "+";
        $record .= $consignment->getCity() . "+";
        $record .= str_replace("  ", " ", $consignment->getPostcode()) . "+";
        return $record;
    }

    private function sendBookings($ftpConstants, $run_number) {

        if (sizeof($this->record_array) > 0) {
            $path = SETTING_DIR_ASSETS . "data_send/parcelforce_booking/" . date("Y_m_d") . "/";
            if (!file_exists($path))
                @mkdir($path, 0777, TRUE);

            $file_path = $path . $this->booking_file;
            chmod($path, 0777);


            $record_count = sizeof($this->record_array) + 2;
            // create file
            $file_handle = @fopen($file_path, 'w');

            fwrite($file_handle, $this->getHeaderRecord($record_count, $run_number));
            fwrite($file_handle, "\r\n" . $this->getSenderAddressRecord($ftpConstants) . "\r\n");

            foreach ($this->record_array as $record) {
                fwrite($file_handle, $record);
            }
            fwrite($file_handle, "\r\n" . $this->getFooterRecord($record_count));
            // close file
            fclose($file_handle);

            if (isset($ftpConstants['PARCELFORCE_FTP_SITE']) && trim($ftpConstants['PARCELFORCE_FTP_SITE']) != '') {
                $SETTING_FTP_USER = $ftpConstants['PARCELFORCE_FTP_USERNAME'];
                $SETTING_FTP_PASSWORD = $ftpConstants['PARCELFORCE_FTP_PASSWORD'];
                $SETTING_FTP_SITE = $ftpConstants['PARCELFORCE_FTP_SITE'];

                $remote_file_path2 = "./in/" . $this->booking_file;
                $ftp_conn = ftp_connect($SETTING_FTP_SITE);
                
                if($ftp_conn){
                    $login = ftp_login($ftp_conn, $SETTING_FTP_USER, $SETTING_FTP_PASSWORD);
                    //ftp_pasv($ftp_conn, true) or die("Unable switch to passive mode");
                    // upload file
                    $isUpload = ftp_put($ftp_conn, $remote_file_path2, $file_path, FTP_ASCII);
                    if ($isUpload) {
                        echo "Successfully uploaded $remote_file_path.";
                    } else {
                        $message = 'There is an error while uploading the file to  FTP. File name is ' . $remote_file_path2;

                    }
                    // close connection
                    ftp_close($ftp_conn);
                    $returnparam = $isUpload;
                }
                else
                {
                    $message = "Unabel to connect to Yodel FTP  " . $SETTING_FTP_SITE;
                     $returnparam = false;
                }
                
            } else {
                $message = 'There is an error while uploading the file to  FTP. Constants not found in the system. file name is ' . $remote_file_path2;

                $returnparam = false;
            }
            if (trim($message) != '') {
                echo $message;
                $to = 'itsupport@oneworldexpress.com';
                $subject = 'SmartTrack Parcelforce data send to carrier issue';

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

    private function getHeaderRecord($record_count, $run_number) {
        $record = "0" . "+";

        $record .= $this->fld(2, "02") . "+";
        $record .= $this->fld(4, "SKEL") . "+";
        $record .= $this->fld(7, "MON0035") . "+";
        $record .= $this->fld(7, "P956724") . "+";
        $record .= $run_number . "+";
        ;
        $record .= $this->fld(8, date("Ymd", time())) . "+";
        ; // collection set to today!
        $record .= $this->fld(6, date("hms", time())) . "+";
        ; // collection set to today!
        return $record;
    }

    /**
     * Get footer record string
     *
     * @return string
     */
    private function getFooterRecord($record_count) {
        $record = "9" . "+";
        $record .= $this->fld(2, "02") . "+";
        $record .= $record_count + 1 . "+";


        return $record;
    }

    private function getSenderAddressRecord($constants) {

        $record = "1+02+" . $constants["PARCELFORCE_SHIPPER_COMPANY"] . "+" . $constants["PARCELFORCE_SHIPPER_ADDRESS_LINE_1"] . "+" . $constants["PARCELFORCE_SHIPPER_ADDRESS_LINE_2"] . "+++++" . $constants["PARCELFORCE_SHIPPER_CITY"] . "+" . $constants["PARCELFORCE_SHIPPER_POSTCODE"] . "+";
        //$record = "1+02+Mail Options+Waterside Trading Centre+Trumpets Way++++London+W7 2QD+";
        /* $record = "1". "+";  //Record-Type-Indicator
          $record .= $this->fld(2, "02"). "+"; //File Version Number
          $record .= "Mail Options " . "+"; //Sender's Name
          $record .= "39-45 Waterside Trading Centre," . "+" ;
          $record .= "Trumpets ways " . "+" ;
          $record .= "London, W7 2QD" . "+";
          $record .= "-" . "+" ;
          $record .= "-"  . "+";
          $record .= "UNITED KINGDOM" . "+" ;
          $record .= "" . "+";
          $record .= "" . "+";
          $record .= "" . "+";
          $record .= "" . "+"; */
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

    public function recycledShipment($consignment) {
        $output["STATUS"] = "SUCCESS";
        return $output;
    }

}
