<?php
include_classes([
    'carrierdatafilelog.class',
    'carrierdatafilelogfilter.class' 
    ]);

class CzechPost implements CarrierService {

    private $pdf;
    private $serviceValues = null;
    private $agentValues = null;
    private $constants = null;
    private $user = null;
    private $country = null;
    private $booking_file = null;
    private $record_array = null;

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

        $serviceAgentConstantFilter = new ServiceConstantValueFilter();
        $serviceAgentConstantFilter->addFilter("service_id = '" . $consignment->getServiceId() . "' AND agent_id = '" . $consignment->getAgentId() . "' ");
        $serviceAgentConstant = $serviceAgentConstantFilter->getConstantList("c.constant 'constant_name', scv.constant_value 'constant_value'");
        if (count($serviceAgentConstant) > 0) {
            foreach ($serviceAgentConstant as $serviceAgentConstantData) {
                $this->constants[$serviceAgentConstantData->getConstantName()] = $serviceAgentConstantData->getConstantValue();
            }
        }
        if (trim(@$this->constants['CZECHPOST_SHIPPER_CONTACT']) == '' || trim(@$this->constants['CZECHPOST_SHIPPER_ADDRESSLINE1']) == '' || trim(@$this->constants['CZECHPOST_SHIPPER_POSTCODE']) == '' || trim(@$this->constants['CZECHPOST_SHIPPER_CITY']) == '' || trim(@$this->constants['CZECHPOST_SHIPPER_COUNTRY']) == '' || trim(@$this->constants['CZECHPOST_PPI_1']) == '' || trim(@$this->constants['CZECHPOST_PPI_2']) == '' || trim(@$this->constants['CZECHPOST_PPI_3']) == '') {
            $output['STATUS'] = 'ERROR';
            $output['MESSAGE'] = "Please check service constants. Please contact to itsupport@oneworldexpress.com";
            return $output;
        }

        /*
         *  Get Tracking Number ranges
         */

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
                else {
                    $checkdigit = LicencePlate::mod11(sprintf('%08d', $resultArray["RANGE"]));
                    $licence_plate = $resultArray["PREFIX"] . sprintf('%08d', $resultArray["RANGE"]) . $checkdigit . $resultArray["SUFIX"];
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
            $page_size = array(150, 60);
            $this->pdf->AddPage("L", $page_size);
            $this->addWayBill($consignment, $this->constants, $licence_plate);

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
        $consignmentData->addFilter("     s.carrier_id= '193'", "servicefilter");
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
                $carrierId = $this->serviceValues->getCarrierId();

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
                        $run_number = CarrierDataFileLog::generateRunNumber($carrierId, $agentid);
                        $this->booking_file = "mm" . sprintf('%03d', $run_number) . "882.c98";

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
                            $this->record_array[] = $this->getShipmentRecord($consignmentItemData, $this->constants[$serviceid]);
                        }
                        $carrierId = $this->serviceValues->getCarrierId();
                        $agentid = trim($agentServiceArray['agent'][$key]);

                        if ($this->sendBookings($this->constants[$serviceid])) {
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

    private function addWayBill(Consignment $consignment, $constants, $licence_plate) {

        $this->pdf->SetXY(0, 0);
        $y1 = $this->pdf->GetY();
        $x1 = $this->pdf->GetX();

        $this->pdf->SetFont('Arial', 'B', 8.5);
        $this->pdf->Text($x1, $y1 + 3.5, $constants["CZECHPOST_SHIPPER_CONTACT"]); //"KAAB"
        $this->pdf->Text($x1, $y1 + 6.5, $constants["CZECHPOST_SHIPPER_ADDRESSLINE1"]);  //"P.O.Box n. 37"
        $this->pdf->Text($x1, $y1 + 9.5, $constants["CZECHPOST_SHIPPER_POSTCODE"] . $constants["CZECHPOST_SHIPPER_CITY"]); //"225 37 Praha 025"
        $shipper_country = $this->constants['CZECHPOST_SHIPPER_COUNTRY'];
        if ((int) $shipper_country > 0) {
            $shipperCountry = new Country($shipper_country);
            $sCountry = $shipperCountry->getName();
        } else {
            $sCountry = $shipper_country;
        }
        $this->pdf->Text($x1, $y1 + 12.5, $sCountry);

        $this->pdf->Rect(90, 3, 15, 11);
        $image = realpath("../images/czechpost.jpg");
        $this->pdf->image($image, 92, 5, 10, 7);
        $this->pdf->Rect(105, 3, 10, 11);
        $this->pdf->Text($x1 + 107, $y1 + 6, "ZR");
        $this->pdf->Rect(115, 3, 30, 11);
        $this->pdf->Text($x1 + 127, $y1 + 4, $constants["CZECHPOST_PPI_1"]); //"T.P."
        $this->pdf->Text($x1 + 121, $y1 + 7, $constants["CZECHPOST_PPI_2"]); //#"2013 / 0960"
        $this->pdf->Text($x1 + 117, $y1 + 10, $constants["CZECHPOST_PPI_3"]); //"225 00 Praha 025"

        $contact = $consignment->getContact();
        if ($contact == "")
            $contact = $consignment->getCompany();
        $this->pdf->SetFont('Arial', '', 7);
        $this->pdf->Text($x1 + 80, $y1 + 20, $consignment->getContact() . "," . $consignment->getCompany());
        $this->pdf->SetFont('Arial', '', 6);
        $this->pdf->Text($x1 + 80, $y1 + 23, $consignment->getAddressLine1() . "," . $consignment->getAddressLine2() . "," . $consignment->getAddressLine3());
        $this->pdf->SetFont('Arial', '', 6);
        $this->pdf->Text($x1 + 80, $y1 + 27, $consignment->getPostcode() . "," . $consignment->getCity());
        $this->pdf->Text($x1 + 80, $y1 + 31, $this->country->getName());
        $this->pdf->Text($x1 + 80, $y1 + 34, $consignment->getReference() . "," . $consignment->getTelephone());


        $this->pdf->SetFont('Arial', 'B', 25);
        $this->pdf->Text($x1, $y1 + 20, "R");
        $this->pdf->SetFont('Arial', '', 5);
        $this->pdf->Text($x1, $y1 + 28.5, "Doporučeně");
        $this->pdf->Text($x1, $y1 + 30, "Recommandé");
        $image = realpath("../images/czechpost.jpg");
        $this->pdf->image($image, $x1 + 1, 32, 10, 7);

        $this->pdf->SetFont('Arial', '', 7);
        $this->pdf->Text($x1 + 23, $y1 + 17, $constants["CZECHPOST_PPI_3"]);
        $this->pdf->Line($x1 + 19, $y1 + 20, $x1 + 66, $y1 + 20);

        $this->pdf->write1DBarcode($licence_plate, 'C128', $x + 25, $y + 25, 40, 5.5, 10, $style = array('padding' => '-5'), 'N'); // Tracking Barcode	
        $this->pdf->SetFont('Arial', '', 9);
        $this->pdf->Text($x1 + 28, $y + 36, $licence_plate);



        $this->pdf->Line($x1 + 140, $y1 + 45, $x1 + 140, $y1 + 49, $style = array(
            'width' => '0.5', 'padding' => '-5'));
        $this->pdf->Line($x1 + 141.5, $y1 + 45, $x1 + 141.5, $y1 + 49, $style = array(
            'width' => '0.7'));
        $this->pdf->Line($x1 + 143, $y1 + 45, $x1 + 143, $y1 + 49, $style = array(
            'width' => '1'));

        $this->pdf->SetAutoPageBreak(false, 0);
    }

    private function getShipmentRecord(Consignment $consignment, $constant) {

        $this->country = new country($consignment->getCountryId());

        $record = "";

        $record .= $this->fld(13, $consignment->getAwb()) . ";"; // consignment identification number  - 1
        $record .= date("Ymd") . ";"; // Date of the data handover for posting -2
        $record .= date("h:m:s") . ";"; //Time of the data handover for posting  -3
        $contact = trim($consignment->getContact()) . "-" . trim($consignment->getCompany());
        $record .= iconv('utf-8', 'CP852', substr($contact, 0, 30)) . ";"; //Surname and name of addressee / company name -4
//				$record .= iconv('utf-8', 'CP852',$this->fld(30,$consignment->getCompany())). ";"; //Surname and name of addressee / company name -4
        //$poscode = str_replace(" ", "", $consignment->getPostCode());
        //$record .= $this->fld(5,$poscode) . ";"; //Addressee�s postcode -5
        $record .= ";";  //-5
        $record .= $this->country->getIso() . ";";  //-6
        $record .= substr(trim($consignment->getCity()), 0, 40) . ";"; //Municipality -7
        $record .= ";"; //Municipal district -8
        $record .= iconv('utf-8', 'CP852', substr(trim($consignment->getAddressLine1()), 0, 40)) . ";"; //Street -9


        $record .= iconv('utf-8', 'CP852', filter_var($consignment->getAddressLine1(), FILTER_SANITIZE_NUMBER_INT)) . ";"; //House number -10
        $record .= iconv('utf-8', 'CP852', filter_var($consignment->getAddressLine1(), FILTER_SANITIZE_NUMBER_INT)) . ";"; //House number -11
        //$record .= substr(trim($consignment->getTelephone()),0,20) . ";"; //Addressees telephone number -12
        $record .= ";"; // Addressees telephone number -12
        $record .= "cs@oneworldexpress.com" . ";"; //Addressee�s email address -13
        $record .= ";"; // Amount of expected delivery charges in CZK -14
        $record .= number_format($consignment->getWeight(), 3) . ";"; // Consignment weight in kg -15
        $record .= ";"; // COD amount in CZK -16
        $record .= ";"; // Declared value in CZK -17
        $record .= "53+9;";  // Services required with respect to the consignment*)  -18
        $record .= ";"; // Consignment status -19
        $record .= ";"; // Franking machine number -20
        $record .= ";";   // Variable symbol of COD money order **) -21
        $record .= ";";  // Main consignment identification number in a multiple piece consignment  -22
        $record .= ";";  // Serial number of the consignment in a multiple piece consignment -23
        $record .= ";"; // Number of consignment pieces in a multiple piece consignment -24
        $record .= ";"; // Consignor identification number  -25
        //$parcelinfo = $consignment->getParcels();
        $record .= ";"; // Consignment variable symbol  -26
        $record .= "" . ";"; // Consignment dimensions - length  -27
        $record .= "" . ";"; // Consignment dimensions - width   -28
        $record .= "" . ";"; // Consignment dimensions - height  -29
        $record .= "P;"; // Type of person (individual/legal entity) -30
        $record .= ";"; //MRN code  -31
        $record .= ";"; // Mail container code  -32
        $record .= $constant["CZECHPOST_SHIPPER_PHONE"] . ";"; // Sender�s telephone number  -33  "+48774593339" 
        $record .= $constant["CZECHPOST_SHIPPER_EMAIL"] . ";"; // Sender�s email address  -34 sender@kaab.com
        $record .= "" . ";"; // Number of pallets -35
        $record .= "" . ";"; // Contact person�s surname and name -36
        $record .= $constant["CZECHPOST_SHIPPER_CONTACT"] . ";"; // Sender�s name  -37 "Arnold" Bokhorst
        $record .= ";"; // Sender�s surname  -38
        $record .= $constant["CZECHPOST_SHIPPER_COMPANY"] . ";"; // Business name  -39"Joanna Kocon Kaab"
        $record .= ";"; // Sender�s registration number (I?O) -40
        $record .= $constant["CZECHPOST_SHIPPER_POSTCODE"] . ";"; // Sender�s postcode  -41
        $shipper_country = $this->constants['CZECHPOST_SHIPPER_COUNTRY'];
        if ((int) $shipper_country > 0) {
            $shipperCountry = new Country($shipper_country);
            $sCountry = $shipperCountry->getIso();
        } else {
            $sCountry = $shipper_country;
        }
        $record .= $sCountry . ";"; // Sender�s ISO country code  -42
        $record .= $constant["CZECHPOST_SHIPPER_CITY"] . ";"; // Municipality of the Sender�s address  -43
        $record .= ";"; // Municipal district of the Sender�s address  -44
        $record .= " " . ";"; // Street of the Sender�s address   -45
        $record .= " " . ";"; // House number �?.p.� of the Sender�s address  -46
        $record .= ";"; // House number �?.o.� of the Sender�s address  -47
        $record .= ";" . "\r\n"; // Dutiable conent   -48


        return $record;
    }

    private function sendBookings($ftpConstants) {

        if (sizeof($this->record_array) > 0) {
            $path = SETTING_DIR_ASSETS . "data_send/czech_booking/" . date("Y_m_d") . "/";
            if (!file_exists($path))
                @mkdir($path, 0777, TRUE);

            $file_path = $path . $this->booking_file;
            chmod($path, 0777);

            // create file
            $file_handle = @fopen($file_path, 'w');

            foreach ($this->record_array as $record) {
                fwrite($file_handle, $record);
            }
            // close file
            fclose($file_handle);



//            $my_mail = "it@kaabnl.nl, kaab@kaabnl.nl, pkocon@kaabnl.nl, operations@kaabnl.nl, itsupport@oneworldexpress.com";
            $mail = 'itsupport@oneworldexpress.com';
            $replyto = "itsupport@oneworldexpress.com";
            $subject = 'CZECH INTERNATION AND EUROPE PRE ALERT PRE ALERT Advise File' . date('YmdHms') . ' One World Express';
            $message = 'Please find attached Pre-advice file';



            $this->mail_attachment($this->booking_file, $path, $mail, $replyto, "One World Express", $replyto, $subject, $message);


            $this->link_file = NULL;
            $this->record_array = NULL;
        }
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

    private function mail_attachment($filename, $path, $mailto, $from_mail, $from_name, $replyto, $subject, $message) {

        $file = $path . $filename;

        //Create a new PHPMailer instance
        $mail = new PHPMailer;

        //Set who the message is to be sent from
        $mail->setFrom($from_mail, $from_name);
        //Set an alternative reply-to address
        $mail->addReplyTo($replyto);
        //Set who the message is to be sent to
        $receipts_email = explode(",", $mailto);
        foreach ($receipts_email as $to) {
            $mail->addAddress($to, '');
        }
        //Set the subject line
        $mail->Subject = $subject;
        //Read an HTML message body from an external file, convert referenced images to embedded,
        //convert HTML into a basic plain-text alternative body
        //$mail->msgHTML(file_get_contents('mm294882.c98'), '/var/www/vhosts/oneworldexpress.co.uk/httpdocs/remote/_assets/eurobtoc_booking/czech_files_int/');
        //Replace the plain text body with one created manually
        $mail->AltBody = $message;
        $mail->Body = $message;

        //Attach an image file
        $mail->addAttachment($file);

        //send the message, check for errors
        if (!$mail->send()) {
            $mail->ErrorInfo;
        }
    }

    public function recycledShipment($consignment) {
        $output["STATUS"] = "SUCCESS";
        return $output;
    }

}
