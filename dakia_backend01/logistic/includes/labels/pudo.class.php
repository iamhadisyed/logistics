<?php

class PUDO implements CarrierService {

    private $pdf;
    private $serviceValues = null;
    private $userAccount = null;
    private $country = null;
    private $booking_file;
    private $recordArray;

    public function __construct() {
        
    }

    public function validation(Consignment $consignment, Services $service, Country $country) {
         $returnOutput = array();
       
        if(trim($consignment->getTelephone()) == "")
        {
            $returnOutput[] = "Please enter Telephone No.";
        }
        if(trim($consignment->getEmail()) == "")
        {
            $returnOutput[] = "Please enter Email Address.";
        }
        return $returnOutput;
    }

    public function remoteareas($consignment, $carrierObject, $country) {
        
    }

    public function label($consignment, $labelType = 'pdf', $size = '') {

        $output = array();
        $this->serviceValues = new Services($consignment->getServiceId());
        $this->country = new Country($consignment->getCountryId());
        $user = new User($consignment->getUserId());
        $this->userAccount = new CustomerAccount($user->getUserAccountId());

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
        if (trim(@$this->constants['PUDO_SHIPPER_POSTCODE']) == '' || trim(@$this->constants['PUDO_SHIPPER_COUNTRY']) == '' || trim(@$this->constants['PUDO_SHIPPER_ADDRESS_LINE_1']) == '') {
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
                    $range = $resultArray["RANGE"];
                    $pieces = str_pad($parcel_idx + 1, 2, "0", STR_PAD_LEFT);
                    $barcode = $resultArray["PREFIX"] . $range . $resultArray["SUFIX"] . $pieces;
                    $licence_plate = $barcode;
                }
                $parcel->setTrackingNumber($licence_plate);
                $parcel->save();
            } else {
                $licence_plate = $parcel->getTrackingNumber();
            }
            
            $this->sendCustomerEmail($consignment, $this->agent);
            $licence_plate_array[$parcel_idx] = $licence_plate;

            $this->pdf->SetPrintFooter(false);
            $this->pdf->SetFooterMargin(0);
            $this->pdf->SetAutoPageBreak(false, 0);
            $page_size = array(120, 110);
            $this->pdf->AddPage("P", $page_size);
            $this->addWayBill($consignment, $parcel_idx, $licence_plate);

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
        $consignmentData->addFilter("     s.carrier_id= '213'", "servicefilter");
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
                     con.region 'country_region', c.service_id, c.weight, c.hawb, c.description, c.company, c.country_id,c.awb,c.date_created,c.value,c.date_booked,c.state,
                      c.contact, c.address_line_1, c.address_line_2, c.city, c.postcode, c.telephone, c.other_routing_code, routing_code_eur, c.number_pieces, pc.weight as parcel_weight " );
                    
                    if (count($consignmentShipmentData) > 0) {
                        $consignmentIdArray = [];
                        foreach ($consignmentShipmentData as $consignmentItemData) {

                            $consignmentId = $consignmentItemData->getConsignmentId();
                            $consignmentIdArray[] = $consignmentId;
                            $carrierId = $consignmentItemData->getCarrierId();
                            $this->recordArray[] = $this->getConsignmentRecord($consignmentItemData, $this->constants[$serviceid]);
                        }
                        $carrierId = $this->serviceValues->getCarrierId();
                        $agentid = trim($agentServiceArray['agent'][$key]);

                        $this->booking_file = "MA123_123456789.txt";


                        if ($this->sendBookings($this->constants[$serviceid])) {
                            if (!empty($consignmentIdArray)) {

                                $sql = "UPDATE consignment SET send_courier_data = 1, booked_file_id = '" . $this->booking_file . "' , 
                                                date_booked = CASE
                                                    WHEN date_booked <= 0 THEN " . time() . "
                                                    WHEN date_booked IS NULL THEN " . time() . "
                                                END  
                                        WHERE id IN (" . implode("','", $consignmentIdArray) . ") AND id <> '' AND id <> '0' 
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

        $this->pdf->setFont("helvetica", "", 7);
        $shipper_country = $this->constants['PUDO_SHIPPER_COUNTRY'];
        if ((int) $shipper_country > 0) {
            $shipperCountry = new Country($shipper_country);
            $sCountry = $shipperCountry->getIso();
        } else {
            $sCountry = $shipper_country;
        }
        $this->pdf->Text(5, 5, "FROM/DE:". $this->constants['PUDO_SHIPPER_COMPANY']);
        $this->pdf->Text(5, 8, $this->constants['PUDO_SHIPPER_ADDRESS_LINE_1'].$this->constants['PUDO_SHIPPER_ADDRESS_LINE_2']);
        $this->pdf->Text(5, 11, $this->constants['PUDO_SHIPPER_CITY'].$sCountry.$this->constants['PUDO_SHIPPER_POSTCODE']);
        $this->pdf->Text(5, 14, "Tel: ". $this->constants['PUDO_SHIPPER_TELEPHONE']);

        $this->pdf->setFont("helvetica", "", 10);
        $this->pdf->Text(5, 20, "TO/A: " . $consignment->getCompany());
        $this->pdf->Text(5, 25, $consignment->getContact());
        $this->pdf->Text(5, 30, $consignment->getAddressLine1());
        $this->pdf->Text(5, 35, $consignment->getAddressLine2() . " " . $consignment->getAddressLine3());
        $this->pdf->Text(5, 40, $consignment->getCity() . " " . $consignment->getPostcode());
        $this->pdf->Text(5, 45, $consignment->getTelephone());
        $this->pdf->Text(5, 50, "WEIGHT/POIDS");
        $this->pdf->Text(70, 50, "PIECES/COLIS");
        $this->pdf->setFont("helvetica", "", 8);
        $this->pdf->Text(5, 54, number_format($consignment->getWeight(), 1) . " Kgs");
        $this->pdf->Text(80, 54, $parcel_idx + 1 . " of/de " . $consignment->getNumberPieces());


        $style = array(
            'position' => '',
            'align' => 'L',
            'stretch' => false,
            'cellfitalign' => '',
            'border' => false,
            'fgcolor' => array(0, 0, 0),
            'bgcolor' => false, //array(255,255,255),
            'text' => true,
            'stretchtext' => 1,
            'font' => 'helvetica',
            'fontsize' => 8,
        );


        $smartBarcodeprefix = "*1";
        $postcode = strtolower(str_replace(" ", "", $consignment->getPostcode()));
        $pcode = $this->toNum($postcode);

        $numericPostcode = str_pad($pcode, 9, "0", STR_PAD_RIGHT);
        $weight = (number_format($consignment->getWeight(), 1) * 10);
        $wholeweight = str_pad($weight, 4, "0", STR_PAD_LEFT);
        $weightType = '2';
        $smartBarcode = $smartBarcodeprefix . $numericPostcode . $wholeweight . $weightType;
        $checkDigit = $this->mod10($smartBarcode);
        $barcode = $smartBarcode . $checkDigit;

        $this->pdf->write1DBarcode($barcode, 'C128', '10', '60', '', 25.4, 0.5, $style, 'N');
        $this->pdf->write1DBarcode($licence_plate, 'C128', '10', '90', '', 25.4, 0.5, $style, 'N');

        $this->pdf->Line(65, 10, 90, 10);
        $this->pdf->Line(65, 17, 90, 17);
        $this->pdf->Line(65, 24, 90, 24);
        $this->pdf->Line(65, 31, 90, 31);
        $this->pdf->Line(65, 38, 90, 38);
        $this->pdf->Line(65, 10, 65, 38);
        $this->pdf->Line(90, 10, 90, 38);
        $this->pdf->Line(78, 10, 78, 24);
        $this->pdf->setFont("helvetica", "B", 13);
        $this->pdf->Text(66, 24.5, $consignment->getPostcode());
        $this->pdf->Text(66, 31.5, "GROUND");
    }
    
    public function sendCustomerEmail($consignment, $dropoffEmail)
    {
        
        $agent = new AgentData($consignment->getAgentId());
        $serviceId = $consignment->getServiceId();
        if(!empty($serviceId) && $serviceId > 0){
            $serviceValue = new services($serviceId);
            $carrierId = $serviceValue->getCarrierId();
            $carrierValue = new Carrier($carrierId);
            $carrierLogo = $carrierValue->getLogo();
            $carrierName = $carrierValue->getCarrier();
            
        }
        $consignmentId = base64_encode($consignment->getId());
        $contact = ucfirst(strtolower($consignment->getContact()));
        $senderadd1 = (trim($consignment->getSenderAddressLine1())!= "")? $consignment->getSenderAddressLine1() . "<br />" : "";
        $senderadd2 = (trim($consignment->getSenderAddressLine2())!= "")? $consignment->getSenderAddressLine2() . "<br />" : "";
        $senderadd3 = (trim($consignment->getSenderAddressLine3())!= "")? $consignment->getSenderAddressLine3() . "<br />" : "";
        $sendercomapny = (trim($consignment->getSenderCompany())!= "")? $consignment->getSenderCompany() . "<br />" : "";
        $senderCity = (trim($consignment->getSenderCity())!= "")? $consignment->getSenderCity() . "<br />" : "";
        $senderpostcode = (trim($consignment->getSenderPostcode())!= "")? $consignment->getSenderPostcode() . "<br />" : "";
        $sendertel = (trim($consignment->getSenderTelephone())!= "")? $consignment->getSenderTelephone() . "<br />" : "";
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";

// More headers
        $headers .= 'From: SmartTrack<smart@smarttrack.co>' . "\r\n";
        
        if($dropoffEmail != 1)
        {
            $subject = "Your Shipment is on its way!";
        }
        else
        {
            $subject = "Your Drop Off location is changed.";
        }
        $html = "";
        if($carrierLogo != ''){
            $html .= '<img src="'.SETTING_DIR_ASSETS.'images/carrierlogo/thumbnail/owe_100_'.$carrierlogo.'" alt="'.$carrierName.'" /><br /><br />';
        }
        $html .= "Dear " . $contact . ", <br /><br />" ;
        $html .= "Thank you for choosing ".$agent->getAgentName().". <br /><br /> ";
        if($dropoffEmail == '1')
        {
            $html .= "We have received request for change of drop off location. We have updated your drop off location. Below is new drop off location.<br /><br />" ;
        }
        else
        {
            $html .= "We have received your order which will be delivered to the following drop off location: <br /><br />" ;
        }
        $html .= "<b>".$sendercomapny . $senderadd1 . $senderadd2 . $senderadd3 . $senderCity . $senderpostcode . $sendertel." </b><br /><br />";
        $html .= "Please find your tracking number: <br /><br /><b>" . $consignment->getAwb() . "</b>";
        $html .= "<br /><br />you can track your shipment here:<br /><br />".  "https://www.smarttrack.co/tracking.php?tracking_number=".$consignment->getAwb();
        $html .= "<br /><br />If the drop off point selected is not suitable and you would like to change your drop off point please click on below link below to select an alternative drop off point (if available)*:  <br /><br />";
        $html .= "https://www.smarttrack.co/change_dropofflocation.php?id=".$consignmentId;
        $html .= "<br /><br /><div style='font-size: 10px;'>*Kindly be aware that once the parcel has cleared customs and is being delivered to the specified location, a change of address will not be possible. Any items not collected will automatically be returned to sender after 14 days.</div><br />";
        $html .= "Best Regards, <br /> " . $agent->getAgentName() . "<br /><br />";
        $html .= '<img src="'.SETTING_DIR_ASSETS.'images/smart-track-logo.png" class="login-logo login-6" alt="Smart Track">';
        
        
        mail($consignment->getEmail(), $subject, $html, $headers);
        
        
        
    }
    

    private function toNum($postcode) {

        $length = strlen($postcode);
        for ($i = 0; $i < $length; $i++) {
            if (!is_numeric($postcode[$i])) {
                $asciiValue = ord($postcode[$i]);

                $avalue = $asciiValue - 96;
                $mystring[$i] = str_pad($avalue, 2, "0", STR_PAD_LEFT);
            } else {
                $mystring[$i] = $postcode[$i];
            }
            // $return_value += ($alpha_flip[$postcode[$i]] + 1) * pow(26, ($length - $i - 1));
        }
        $return_value = implode("", $mystring);
        return $return_value;
    }

    private function mod10($val) {

        $arr = str_split($val);
        $checkdigit = ($arr[0] * 2) + ($arr[1] * 1) + ($arr[2] * 2) + ($arr[3] * 1) + ($arr[4] * 2) + ($arr[5] * 1) + ($arr[6] * 2) + ($arr[7] * 1) + ($arr[8] * 2) + ($arr[9] * 1) + ($arr[10] * 2) + ($arr[11] * 1) + ($arr[12] * 2) + ($arr[13] * 1) + ($arr[14] * 2) + ($arr[15] * 1) + ($arr[16] * 2);
        $remainder = $checkdigit % 10;

        $checkdigit = 10 - $remainder;

        if ($checkdigit == 10) {
            $checkdigit = 0;
        } elseif ($checkdigit == 11) {
            $checkdigit = 5;
        }

        return $checkdigit;
    }
    
    private function getHeaderRecord($consignment, $constants)
    {
        $record = "";
        $record .= "12345678"; //CANPAR ACCOUNT
        $record .= "12345"; //Manifest 
        
        
    }
    
    private function getConsignmentRecord($consignment, $constants)
    {
       
        $record = "";
        $record .= $consignment->getAwb()  . "," ; // PACKEGE ID
        $record .= "1"  . "," ; // PIece Number 
        $record .= $consignment->getNumberPieces()  . "," ; // Total PIece 
        $record .= "0"  . "," ; // Minimum weight flag
        $poundWeight = $consignment->getWeight() * 2.205;
        $record .= number_format($poundWeight,1 )  . "," ; // weight in pound
        $record .= number_format($consignment->getWeight(),1 )  . "," ; // weight in kg
        
        $poundPieceWeight = $consignment->getParcelWeight() * 2.205;
        $record .= number_format($poundPieceWeight,1 )  . "," ; // weight of piece in pound
        $record .= number_format($consignment->getParcelWeight(),1 )  . "," ; // weight of piece in kg
        
        $record .= "0"  . "," ; // Signature flag
        $record .= substr($consignment->getNotes(), 0, 40)  . "," ; // Special Instruction
        $record .= ""  . "," ; // CANPAR Secondary Package ID
        $record .= $consignment->getHawb()  . "," ; // Customer Reference Number
        $record .= ""  . "," ; // Customer Cost Centre
        $record .= ""  . "," ; // Customer Order Number
        $record .= ""  . "," ; // CANPAR Sales Rep. ID
        $record .= "1"  . "," ; // Service Type
        $record .= "0"  . "," ; // freight_charges
        $record .= "0"  . "," ; // Valuation_charge
        $record .= "0"  . "," ; // Declared Value
        $record .= "0"  . "," ; // C.O.D Service Charges
        $record .= "0"  . "," ; // cod_value_for_all_pieces_in_shipment
        $record .= "0"  . "," ; // Harmonized code
        $record .= "0"  . "," ; // Pickup Tag
        $record .= "0"  . "," ; // Extra care
        $record .= "0"  . "," ; // GST
        $record .= "0"  . "," ; // Quebec Sales Tax
        $record .= date("Ymd")  . "," ; // Shipping Date
        $record .= ""  . "," ; // Delivery Zone
        $record .= "0"  . "," ; // C.O>D value for current Piece
        $record .= "12345678"  . "," ; // Canpar Shipping Account
        $record .= ""  . "," ; // Sender Name
        $record .= ""  . "," ; // Sender Address Line 1
        $record .= ""  . "," ; // Sender Address Line 2
        $record .= ""  . "," ; // Sender Address Line 3
        $record .= ""  . "," ; // Sender City
        $record .= ""  . "," ; // Sender Province
        $record .=  ""  . "," ; // Sender Postcode
        $record .= ""  . "," ; // Sender Contact
        $record .= ""  . "," ; // Consignnee ID
        $record .= substr($consignment->getContact(), 0 ,40)  . "," ; // Consignee Name
        $record .= substr($consignment->getAddressLine1(), 0 ,40)  . "," ; // Consignee Add 1
        $record .= substr($consignment->getAddressLine2(), 0 ,40)  . "," ; // Consignee Add 2
        $record .= substr($consignment->getAddressLine3(), 0 ,40)  . "," ; // Consignee Add 3
        $record .= substr($consignment->getCity(), 0 ,30)  . "," ; // Consignee City
        $record .= substr($consignment->getState(), 0 ,2)  . "," ; // Consignee State
        $record .= substr($consignment->getPostcode(), 0 ,10)  . "," ; // Consignee State
        $record .= ""  . "," ; // Consignee Attention Name
        $record .= $consignment->getAwb()  . "," ; // Shipment ID No
        $record .= "1"  . "," ; // Service Code
        $record .= "1"  . "," ; // Carrier ID
        $record .= "0"  . "," ; // Pieces Cube Weight Pound
        $record .= "0"  . "," ; // Pieces Cube Weight
        $record .= "12345"  . "," ; // Manifest Number
        $record .= ""  . "," ; // CANPAR THIRD PACKAGE ID
        $record .= "0"  . "," ; // Number of Extra Care Pieces in Shipment
        $record .= ""  . "," ; // Type of COD Payment
        $record .= ""  . "," ; // Required Date of Post-Dated Cheque
        $record .= ""  . "," ; // Cube Dimensions of Piece in units dictated by rate code
        $record .= ""  . "," ; // Consignee E-Mail Address
        $record .= "0"  . "," ; // Extended Area charge
        $record .= ""  . "," ; // Self-assessed Residential flag
        $record .= "0"  . "," ; // Self-assessed Residential Rate
        $record .= "0"  . "," ; // Minimum Weight
        $record .= ""  . "," ; // Enhanced Service Flag
        $record .= "0"  . "," ; // Fuel Surcharge Amount
        $record .= ""  . "," ; // Priority Delivery Service Type
        $record .= "0"  . "," ; // Priority Delivery Service Charges
        $record .= ""  . "," ; // Chain of signature
        $record .= "0"  . "," ; // Chain of signature charges
        $record .= ""  . "," ; // Dang Good
        $record .= "0"  . "," ; // Dang Good Charges
        $record .= "0"  . "," ; // Rural Charges
        $record .= ""  . "," ; // Country Code
        $record .= ""  . "," ; // Collect_Charge
        $record .= ""  . "," ; // TAX CODE
        $record .= "N"  . "," ; // Over_Max_Weight_Flag
        $record .= "0"  . "," ; // Over_Max_Weight_Charge
        $record .= "N"  . "," ; // Over_Max_Size_Flag
        $record .= "0"  . "," ; // Over_Max_Size_charge
        $record .= ""  . "," ; // Future Use 74
        $record .= ""  . "," ; // Future Use 75
        $record .= ""  . "," ; // Future Use 76
        $record .= ""  . "," ; // Future Use 77
        $record .= ""  . "," ; // Future Use 78
        $record .= ""  . "," ; // Future Use 79
        $record .= ""  . "," ; // Future Use 80
        $record .= ""  . "," ; // Future Use 81
        $record .= ""  . "," ; // Future Use 82
        $record .= ""  . "," ; // Future Use 83
        

        
        
        return $record;
        
        
        
        
        
        
    }
    
     private function sendBookings($ftpConstants) {
        if (sizeof($this->recordArray) > 0) {
            $path = SETTING_DIR_ASSETS . "data_send/pudo_data/" . date("Y_m_d") . "/";
            if (!file_exists($path))
                @mkdir($path, 0777, true);

            $file_path = $path . $this->booking_file ;
            chmod($path, 0777);

            // create file
            $file_handle = @fopen($file_path, 'w');

            //$csvHeader = "Reference code(D.A.C.),	Reference Code(client),	Origin,	Shipper,	Reference AWB,	Reference Job,	Addressee Name,	Addressee 		Company, Street Address,	ZIP Code,	City,	Country Code,	e-mail address,	Telephone,	N. of Items,	Product Unit Weight,					        Content of the Shipment,	TARIC Code,	VAT Number - SSN,	Product Unit Value,	Currency";


            //fwrite($file_handle, $csvHeader . "\r\n");

            foreach ($this->recordArray as $record) {
                fwrite($file_handle, $record);
            }
            // close file
            fclose($file_handle);
            return true;
//
//            if (isset($ftpConstants['DAC_FTP_SITE']) && trim($ftpConstants['DAC_FTP_SITE']) != '') {
//                $SETTING_FTP_USER = $ftpConstants['DAC_FTP_USER'];
//                $SETTING_FTP_PASSWORD = $ftpConstants['DAC_FTP_PASSWORD'];
//                $SETTING_FTP_SITE = $ftpConstants['DAC_FTP_SITE'];
//
//                $ftp_object2 = new FTPfile($SETTING_FTP_USER, $SETTING_FTP_PASSWORD, $SETTING_FTP_SITE);
//                $remote_file_path2 = "./manifests/" . $this->booking_file . ".csv";
//                if (!$ftp_object2->put($remote_file_path2, $file_path, FTP_ASCII)) {
//                    mail("itsupport@oneworldexpress.com", "DAC LOGIN FAILED", "DAC LOGIN FAILED" . $this->booking_file);
//                }
//
//                $this->link_file = NULL;
//                $this->record_array = NULL;
//                return true;
//            } else {
//                return false;
//            }
        }
    }

    public function recycledShipment($consignment) {
        $output["STATUS"] = "SUCCESS";
        return $output;
    }

}
