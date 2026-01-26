<?php
class Anpost implements CarrierService {

    private $pdf;
    private $serviceValues = null;
    private $userAccount = null;
    private $country = null;
    private $booking_file = null;
    private $record_array = null;

    public function __construct() {
        
    }

    public function validation(Consignment $consignment, Services $service, Country $country) {
        
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
                    $checkdigit = LicencePlate::mod11($range);
                    $barcode = $resultArray["PREFIX"] . $range . $checkdigit . $resultArray["SUFIX"];
                    $licence_plate = $barcode;
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
            $this->addWayBill($consignment, $licence_plate);

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
        //if($EDI == true && !empty($this->trackingServiceId) && !empty($this->trackingAgentId))
        {
            include_once(BASE_PATH."includes/labels/anposttrackingstatus.class.php");
           /* $serviceAgentConstantFilter = new ServiceConstantValueFilter();
            $serviceAgentConstantFilter->addFilter("service_id = '" . $this->trackingServiceId . "' AND agent_id = '" . $this->trackingAgentId . "' ");
            $serviceAgentConstant = $serviceAgentConstantFilter->getConstantList("c.constant 'constant_name', scv.constant_value 'constant_value'");

            if (count($serviceAgentConstant) > 0) 
            {
                foreach ($serviceAgentConstant as $serviceAgentConstantData)
                {
                    $this->constants[$serviceAgentConstantData->getConstantName()] = $serviceAgentConstantData->getConstantValue();
                }
            }
            $ftp_server = $this->constants['ANPOST_TRACKING_SERVER'];
            $ftp_user = $this->constants['ANPOST_TRACKING_USERNAME'];
            $ftp_pass = $this->constants['ANPOST_TRACKING_PASSWORD'];

            $ftp_local_path = SETTING_DIR_ASSETS . "tracking_data/ANPOST/";
            
             if (!file_exists($ftp_local_path))
                @mkdir($ftp_local_path, 0777, TRUE);

            $conn_id = ftp_connect($ftp_server);
            if(!$conn_id){
                echo "FTP connection failed";
                exit;
            }else{
                $loginRes = ftp_login($conn_id, $ftp_user, $ftp_pass);
                if(!$loginRes) {
                    echo "FTP Login Failed, check your username and password.";
                    exit;
                }
                ftp_pasv($conn_id, true);
                $arrfile =  ftp_nlist($conn_id, "/OUT");
                    if($arrfile === false) {
                    echo "Unable List FTP Files.";
                    exit;
                }
                if(sizeof($arrfile) > 0){
                    foreach($arrfile as $filename){
                        ///////// Need to adjust it according to Airbus Specifications //////////////
                        /*
                        $file_start = date('Ymd', strtotime(date("Ymd")));
                        if(strpos($filename, $file_start) !== false){
                            $filename = str_replace("/OUT/", "", $filename);
                            
                            $fp = fopen($ftp_local_path . $filename, 'w'); 

                            if(ftp_fget($conn_id, $fp, "/OUT/".$filename, FTP_ASCII, FTP_AUTORESUME)){

                               */
                               $ftp_local_path = SETTING_DIR_ASSETS . "tracking_data/ANPOST/";
                               $filename = 'SampleCDTFile.txt';
                                $handle = fopen($ftp_local_path.$filename, "r");
                                if ($handle) {
                                    while (($data = fgetcsv($handle, 1000, "+")) !== FALSE) {
                                        $dateTime = date('Y-m-d h:i:s' , strtotime($data[6]));
                                        $trackingNo = removeBomUtf8(str_replace('"','',$data[4]));
                                        $statusCode = $data[5];
                                        $anpostDescriptionArray = AnpostTrackingStatus::$anpost_status_code;
                                        $desc = $anpostDescriptionArray[$statusCode];                                        
                                        $trackPoint = $data[7];                                       
                                        $spTrackingStatus = AnpostTrackingStatus::getOweStatusCode($statusCode);
                                        
                                        $entityId = 0;
                                        $parcelObj = new ParcelFilter();
                                        $parcelObj->addTrackingNumberFilter($trackingNo);
                                        $parcelDataArray = $parcelObj->getColumnList('p.id, p.tracking_number, p.consignment_id');
                                        if (count($parcelDataArray) > 0) {
                                            $parcelData = $parcelDataArray[0];
                                            $entityId = $parcelData->getId();
                                        }

                                        $trackingDataFilter = new TrackingDataFilter();
                                        $trackingDataFilter->addTrackPointExistFilter($trackingNo, $spTrackingStatus, $trackPoint, $statusCode, $desc);
                                        $trackingDataExistsObj = $trackingDataFilter->getColumnList("t.entity_id, t.entity_type");
                                        //echo "Tracking Count: ".count($trackingDataExistsObj)." - "."<br />";
                                        if (count($trackingDataExistsObj) == 0 && $entityId > 0){
                                            $trackingData = [
                                                'user_id' => 0,
                                                'entity_id' => $entityId,
                                                'entity_type' => $trackBy,
                                                'tracking_number' => $trackingNo,
                                                'track_point' => $trackPoint,
                                                'date_created' => $dateTime,
                                                'ip_address' => getClientIp(),
                                                'status_code_id' => $spTrackingStatus,
                                                'carrier_code' => $statusCode,
                                                'carrier_desc' => $desc,
                                                'signatory' => ""
                                            ];
                                            $trackingDataObj = new TrackingData($trackingData);
                                            $trackingDataObj->save();

                                            /// update consignment status
                                            $trackingDataFilter = new TrackingDataFilter();
                                            $trackingDataFilter->addTrackingNumberFilter($trackingNo);
                                            $trackingDataFilter->AddOrderByDate(false);
                                            $trackingDataObj = $trackingDataFilter->getColumnList('entity_id,entity_type,carrier_code,status_code_id,date_created');

                                            if (count($trackingDataObj) > 0){
                                                $trackingDataObj = $trackingDataObj[0];
                                                $carrierCode = $trackingDataObj->getCarrierCode();
                                                $oweTrackingStatusCode = $trackingDataObj->getStatusCodeId();
                                                $entityType = $trackingDataObj->getEntityType();
                                                $entityId = $trackingDataObj->getEntityId();
                                                $consignmentStatusCode = AnpostTrackingStatus::getConsignmentStatus($oweTrackingStatusCode);
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

                                                if($consignmentStatusCode == Consignment::STATUS_DELIVERED){
                                                    $ConsignmentObj->setDateDelivered($trackingDataObj->getDateCreated());
                                                }
                                                if ($consignmentStatus != '')
                                                    $ConsignmentObj->setConsignmentStatus($consignmentStatus);
                                                $ConsignmentObj->save();
                                            }
                                            //echo "tracking done: ".$trackingNo."<br />";
                                        }else{
                                            //echo "No tracking done: ".$trackingNo."<br />";
                                        }
                                    } //end while
                                }				
                            }
                 //       }
               //     }
             //   }
                ftp_close($conn_id);
            }


    public function sendData($tracking_numbers = array()) {
       
    }

    public function manifest($consignment) {
        
    }

    public function preAdvice($consignment) {
        
    }

    private function addWayBill(Consignment $consignment, $licence_plate) {

        $number2 = $licence_plate;
        $this->pdf->SetPrintFooter(false);
        $this->pdf->SetFooterMargin(0);
        $this->pdf->SetAutoPageBreak(false, 0);

        $page_size = array(150.5, 100.5);

        $this->pdf->AddPage("P", $page_size);
        $image = realpath("../images/anpost_back.jpg");
        $this->pdf->Image($image, $x = 0, $y = 0, $w = 100.5, $h = 150.5, $type = '', $link = '', $align = '', $resize = false, $dpi = 300, $palign = '', $ismask = false, $imgmask = false, $border = 0, $fitbox = false, $hidden = false, $fitonpage = false, $alt = false, $altimgs = array());
        $this->pdf->SetFont('', $style = '', $size = 8, '', $subset = 'default', $out = true);
        $this->pdf->Text(14, 16, 'Delivered: ' . $number2, $fstroke = false, $fclip = false, $ffill = true, $border = 0, $ln = 0, $align = '', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M', $rtloff = false);
        $this->pdf->Text(75, 16, 'SIG REQ', $fstroke = false, $fclip = false, $ffill = true, $border = 0, $ln = 0, $align = '', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M', $rtloff = false);
        //set handling codes
        $this->pdf->write1DBarcode($number2, 'C128', 15, 20, '', 40, 0.46, $style, 'N');
        $this->pdf->SetFillColor($col1 = 0, $col2 = 0, $col3 = 0, $col4 = 0, $ret = false, $name = '');
        $this->pdf->Text(14, 50, 'Attempted Delivery: ' . $number2, $fstroke = false, $fclip = false, $ffill = true, $border = 0, $ln = 0, $align = '', $fill = true, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M', $rtloff = false);
        $this->pdf->Text(75, 50, 'SIG REQ', $fstroke = false, $fclip = false, $ffill = true, $border = 0, $ln = 0, $align = '', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M', $rtloff = false);
        $this->pdf->Text(14, 60, $number2[0] . $number2[1] . ' ' . $number2[2] . $number2[3] . $number2[4] . $number2[5] . ' ' . $number2[6] . $number2[7] . $number2[8] . $number2[9] . ' ' . $number2[10] . $number2[11] . $number2[12], $fstroke = false, $fclip = false, $ffill = true, $border = 0, $ln = 0, $align = '', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M', $rtloff = false);

        $this->pdf->SetFont('', $style = 'B', $size = 32, '', $subset = 'default', $out = true);
        $this->pdf->Text(70, 65, 'EMS', $fstroke = false, $fclip = false, $ffill = true, $border = 0, $ln = 0, $align = '', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M', $rtloff = false);

        $this->pdf->SetFont('', $style = '', $size = 10, '', $subset = 'default', $out = true);
        $this->pdf->Text(73, 85, 'COURIER', $fstroke = false, $fclip = false, $ffill = true, $border = 0, $ln = 0, $align = '', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M', $rtloff = false);
        $this->pdf->Text(73, 90, 'POST', $fstroke = false, $fclip = false, $ffill = true, $border = 0, $ln = 0, $align = '', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M', $rtloff = false);



        $this->pdf->SetFont('', $style = '', $size = 8, '', $subset = 'default', $out = true);
        $this->pdf->Text(7, 70, $consignment->getCompany(), $fstroke = false, $fclip = false, $ffill = true, $border = 0, $ln = 0, $align = '', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M', $rtloff = false);
        $this->pdf->Text(7, 73, $consignment->getContact(), $fstroke = false, $fclip = false, $ffill = true, $border = 0, $ln = 0, $align = '', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M', $rtloff = false);
        $this->pdf->Text(7, 76, $consignment->getAddressLine1(), $fstroke = false, $fclip = false, $ffill = true, $border = 0, $ln = 0, $align = '', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M', $rtloff = false);
        $this->pdf->Text(7, 79, $consignment->getAddressLine2(), $fstroke = false, $fclip = false, $ffill = true, $border = 0, $ln = 0, $align = '', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M', $rtloff = false);
        $this->pdf->Text(7, 82, $consignment->getAddressLine3(), $fstroke = false, $fclip = false, $ffill = true, $border = 0, $ln = 0, $align = '', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M', $rtloff = false);
        $this->pdf->Text(7, 85, $consignment->getCity(), $fstroke = false, $fclip = false, $ffill = true, $border = 0, $ln = 0, $align = '', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M', $rtloff = false);
        $this->pdf->Text(7, 88, $consignment->getPostcode(), $fstroke = false, $fclip = false, $ffill = true, $border = 0, $ln = 0, $align = '', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M', $rtloff = false);
        $this->pdf->Text(7, 91, "REF: " . $consignment->getReference(), $fstroke = false, $fclip = false, $ffill = true, $border = 0, $ln = 0, $align = '', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M', $rtloff = false);

        $this->pdf->SetFillColor($col1 = 255, $col2 = 255, $col3 = 255, $col4 = 255, $ret = false, $name = '');
        $this->pdf->Rect(55, 85, 10, 10, 'DF');
        $this->pdf->SetFont('', $style = 'B', $size = 20, '', $subset = 'default', $out = true);
        $this->pdf->SetTextColor(255, 255, 255);
        $this->pdf->Text(55, 85, 'IE', $fstroke = false, $fclip = false, $ffill = true, $border = 0, $ln = 0, $align = '', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M', $rtloff = false);
        $this->pdf->SetTextColor(0, 0, 0);
        $this->pdf->SetFont('', $style = '', $size = 16, '', $subset = 'default', $out = true);
        $this->pdf->Text(80, 120, '1 of 1', $fstroke = false, $fclip = false, $ffill = true, $border = 0, $ln = 0, $align = '', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M', $rtloff = false);
        $this->pdf->SetFont('', $style = '', $size = 8, '', $subset = 'default', $out = true);
        $this->pdf->Text(75, 108, 'SIGNATURE', $fstroke = false, $fclip = false, $ffill = true, $border = 0, $ln = 0, $align = '', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M', $rtloff = false);
        $this->pdf->Text(75, 111, 'REQUIRED', $fstroke = false, $fclip = false, $ffill = true, $border = 0, $ln = 0, $align = '', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M', $rtloff = false);
        $this->pdf->Text(4, 105, 'If undelivered please return to:', $fstroke = false, $fclip = false, $ffill = true, $border = 0, $ln = 0, $align = '', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M', $rtloff = false);
        $this->pdf->Text(4, 2, 'SÃNIÃš', $fstroke = false, $fclip = false, $ffill = true, $border = 0, $ln = 0, $align = '', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M', $rtloff = false);
        $this->pdf->Text(4, 5, 'SIGNATURE REQUIRED', $fstroke = false, $fclip = false, $ffill = true, $border = 0, $ln = 0, $align = '', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M', $rtloff = false);
        return $number2;
    }

    public function recycledShipment($consignment) {
        $output["STATUS"] = "SUCCESS";
        return $output;
    }



    private function removecommas($data) {
        return str_replace(",", " ", $data);
    }

}
