<?php
class Airbus implements CarrierService {

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
        $licencePlate = new LicencePlate($licence_plate_id);
        
        $parcel_list = $consignment->getParcels();
        $parcel_count = sizeof($parcel_list);
        $parcel_idx = 0;
        $pdf = new PdfBase(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $this->pdf = $pdf;
        // Generate label for each parecel
        foreach ($parcel_list as $parcel) 
        {
            if ($parcel->getTrackingNumber() == '') 
            {
                $resultArray = LicencePlate::getLicencePlateNumber($licence_plate_id);
                if (trim($resultArray['STATUS']) == 'ERROR')
                    return $resultArray;
                else {
                   $licence_plate = $resultArray["PREFIX"] . $resultArray["RANGE"] . $resultArray["SUFIX"];                   
                }
                $parcel->setTrackingNumber($licence_plate);
                $parcel->save();
            } 
            
            else 
            {
                $licence_plate = $parcel->getTrackingNumber();
            }
            
            $licence_plate_array[$parcel_idx] = $licence_plate;
            $this->pdf->SetPrintFooter(false);
            $this->pdf->SetFooterMargin(0);
            $this->pdf->SetAutoPageBreak(false, 0);
            $page_size = array(100, 150);
            $this->pdf->AddPage("P", $page_size);
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
        include_once(BASE_PATH."includes/labels/anposttrackingstatus.class.php");
        $ftp_local_path = SETTING_DIR_ASSETS . "tracking_data/ANPOST/";
        $filename = 'SampleCDTFile.txt';
        $handle = fopen($ftp_local_path.$filename, "r");
        if ($handle) 
        {
            while (($data = fgetcsv($handle, 1000, "+")) !== FALSE) 
            {
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
                ftp_close($conn_id);            
    }


    public function sendData($tracking_numbers = array()) {
       
    }

    public function manifest($consignment) {
        
    }

    public function preAdvice($consignment) {
        
    }

    private function addWayBill(Consignment $consignment, $licence_plate) 
    {
        $user = SessionManager::getUser();        
        $this->pdf->setFont("helvetica", "B", 34);
        $this->pdf->Text(57, 2, "STD");
        $this->pdf->setFont("helvetica", "L", 10);
        $this->pdf->Text(47, 16, "STANDARD PARCEL POST");
        $this->pdf->Text(47, 20, "NO SIGNATURE REQUIRED");
        
        $weightImage = realpath("../images/Weight.jpg");
        $this->pdf->image($weightImage, 16, 76, 10);
        
        $scannerImage = realpath("../images/Scan barcode.jpg");
        $this->pdf->image($scannerImage, 36, 76, 10);
        
        $telephoneImage = realpath("../images/telephone.png");
        $this->pdf->image($telephoneImage, 80,25, 4);        
        
        $image = realpath ("../images/airbus.png");
        $this->pdf->image($image, 5, 6, 35, 13);                    
        
        $this->pdf->rect(1, 1, 99, 146);
        $this->pdf->rect(2, 2, 97, 144);
        $this->pdf->line(2, 25, 99, 25);
        $this->pdf->setFont("helvetica", "B", 20);
        $this->pdf->line(2, 45, 99, 45);
        $this->pdf->StartTransform();
        $this->pdf->Rotate(90,18,24);
        $this->pdf->setFont("helvetica", "B", 8);
        $this->pdf->Text(1, 10, "FROM");
        $this->pdf->StopTransform();
        $this->pdf->setFont("helvetica", "L", 7);
        
        $this->pdf->Text(11, 26, $this->constants['AIRBUS_SENDER_COMPANY']);
        $this->pdf->Text(11, 29, $this->constants['AIRBUS_SENDER_ADDRESS1']);
        $this->pdf->Text(11, 32, $this->constants['AIRBUS_SENDER_ADDRESS2']);
        $this->pdf->Text(11, 35, $this->constants['AIRBUS_SENDER_ADDRESS3']);
        $this->pdf->Text(11, 38, $this->constants['AIRBUS_SENDER_POSTCODE']);
        $this->pdf->Text(11, 41, $this->constants['AIRBUS_SENDER_COUNTRY']);
        $this->pdf->Text(84, 26, $this->constants['AIRBUS_SENDER_TELEPHONE']);
        $this->pdf->Text(84, 29, $this->constants['AIRBUS_CUST_ACCOUNT']);
        $this->pdf->Text(71, 29, "Cust. Acc:");
        
        $this->pdf->setFont("helvetica", "B", 14);
        $this->pdf->line(2, 73, 99, 73);
        $this->pdf->line(15, 45, 15, 73);
        $this->pdf->Text(4, 58, "TO");
        
        if ($consignment->getCompany() != "")
            $company = $consignment->getCompany();
        if ($consignment->getContact() != "")
            $contact = $consignment->getContact();
        if ($consignment->getAddressLine1() != "")
            $address1 = $consignment->getAddressLine1();
        if ($consignment->getAddressLine2() != "")
            $address2 = $consignment->getAddressLine2();
        if ($consignment->getAddressLine3() != "")
            $address3 = $consignment->getAddressLine3();

        $this->pdf->setFont("helvetica", "L", 8);
        $this->pdf->Text(17, 47, $contact);
        $this->pdf->Text(17, 50, $company);        
        $this->pdf->Text(17, 53, @$address1);
        $this->pdf->Text(17, 56, @$address2);
        $this->pdf->Text(17, 59, @$address3);
        $this->pdf->Text(17, 62, $consignment->getCity());
        $this->pdf->Text(17, 65, $consignment->getPostcode());
        $this->pdf->Text(17, 69, $consignment->getCountry());
        if($consignment->getTelephone() != '')
        {
            $this->pdf->Text(78, 46, $consignment->getTelephone());
            $this->pdf->image($telephoneImage, 70,45, 6); 
        }
        
        $this->pdf->line(2, 88, 99, 88);
        $this->pdf->setFont("helvetica", "L", 9);
        $this->pdf->write1DBarcode($code, 'C128', 9, 114, '99', 16, 0.5);
        return;
    }

    public function recycledShipment($consignment) {
        $output["STATUS"] = "SUCCESS";
        return $output;
    }


    private function getShipmentRecord(Consignment $consignment, $constant) {

        $this->country = new country($consignment->getCountryId());
        $awb = $consignment->getAwb();

        $record = "";

        $record .= ","; //REFERENCE AWB
        $record .= $awb . ","; //REFERENCE CODE CLIENT
        $record .= "GB" . ","; //ORIGIN
        $record .= $constant["DAC_SHIPPER_CONTACT"] . ","; //SHIPPER "ONE WORLD EXPRESS" 
        $record .= $this->removecommas($consignment->getHawb()) . ","; //REFERENCE CODE CLIENT
        $record .= $this->booking_file . ","; //REFERENCE JOB

        $record .= $this->removecommas($consignment->getContact()) . ","; // ADDRESSEE NAME
        $record .= $this->removecommas($consignment->getCompany()) . ","; // ADDRESSEE COMPANY

        $record .= $this->removecommas($consignment->getAddressLine1()) . " " . $this->removecommas($consignment->getAddressLine2()) .
                $this->removecommas($consignment->getAddressLine3()) . ","; // STREET ADDRESS
        $record .= $this->removecommas(str_pad(trim($consignment->getPostcode()), 5, "0", STR_PAD_LEFT)) . ",";  // ZIP NAME
        $record .= $this->removecommas($consignment->getCity()) . ",";  // CITY
        $record .= $this->removecommas($this->country->getIso()) . ",";  // COUNTRY CODE
        $record .= ","; // email address

        $record .= $this->removecommas($consignment->getTelephone()) . ",";  // TELEPHONE
        $record .= $this->removecommas($consignment->getNumberPieces()) . ","; // NUMBER OF ITEMS

        $record .= $this->removecommas($consignment->getWeight()) . ","; // PRODUCT UNIT WEIGHT
        $record .= $this->removecommas($consignment->getDescription()) . ","; // CONTENT OF THE SHIPMENT
        $record .= "" . ","; //TARIC CODE
        $record .= "" . ","; //VAT NUMBER
        $record .= "" . ","; //PRODUCT UNTT VALUE
        $record .= $consignment->getCurrency() . ","; //CURRENCY
        $record .= "\r\n";
        return $record;
    }

    private function sendBookings($ftpConstants) {

        if (sizeof($this->record_array) > 0) {
            $path = SETTING_DIR_ASSETS . "data_send/dac_booking/" . date("Y_m_d") . "/";
            if (!file_exists($path))
                @mkdir($path, 0777, TRUE);

            $file_path = $path . $this->booking_file . ".csv";
            chmod($path, 0777, true);

            // create file
            $file_handle = @fopen($file_path, 'w');

            $csvHeader = "Reference code(D.A.C.),	Reference Code(client),	Origin,	Shipper,	Reference AWB,	Reference Job,	Addressee Name,	Addressee 		Company, Street Address,	ZIP Code,	City,	Country Code,	e-mail address,	Telephone,	N. of Items,	Product Unit Weight,					        Content of the Shipment,	TARIC Code,	VAT Number - SSN,	Product Unit Value,	Currency";
            fwrite($file_handle, $csvHeader . "\r\n");

            foreach ($this->record_array as $record) {
                fwrite($file_handle, $record);
            }
            // close file
            fclose($file_handle);

            if (isset($ftpConstants['DAC_FTP_SITE']) && trim($ftpConstants['DAC_FTP_SITE']) != '') {
                $SETTING_FTP_USER = $ftpConstants['DAC_FTP_USER'];
                $SETTING_FTP_PASSWORD = $ftpConstants['DAC_FTP_PASSWORD'];
                $SETTING_FTP_SITE = $ftpConstants['DAC_FTP_SITE'];

                $ftp_object2 = new FTPfile($SETTING_FTP_USER, $SETTING_FTP_PASSWORD, $SETTING_FTP_SITE);
                $remote_file_path2 = "./manifests/" . $this->booking_file . ".csv";
                if (!$ftp_object2->put($remote_file_path2, $file_path, FTP_ASCII)) {
                    mail("itsupport@oneworldexpress.com", "DAC LOGIN FAILED", "DAC LOGIN FAILED" . $this->booking_file);
                }

                $this->link_file = NULL;
                $this->record_array = NULL;
                return true;
            } else {
                return false;
            }
        }
    }

    private function removecommas($data) 
    {
        return str_replace(",", " ", $data);
    }

}
