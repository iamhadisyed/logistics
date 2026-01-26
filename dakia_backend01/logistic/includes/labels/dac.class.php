<?php
include_classes([
    'carrierdatafilelog.class',
    'carrierdatafilelogfilter.class' 
    ]);
class Dac implements CarrierService {

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
        if (trim(@$this->constants['DAC_SHIPPER_POSTCODE']) == '' || trim(@$this->constants['DAC_SHIPPER_COUNTRY']) == '' || trim(@$this->constants['DAC_SHIPPER_ADDRESS_LINE1']) == '') {
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
                    $barcode = $resultArray["PREFIX"] . $range . $resultArray["SUFIX"];
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
            $page_size = array(100, 150);
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

     public function tracking($trackingNumber, $trackBy = 'parcel', $EDI = false) 
    {
        $tracking = new Tracking();
        $deliveredArray = array( "02", "34");
        
        if($EDI == true && !empty($this->trackingServiceId) && !empty($this->trackingAgentId)){
        include_once(BASE_PATH."includes/labels/dactrackingstatus.class.php");
       
        $serviceAgentConstantFilter = new ServiceConstantValueFilter();
        $serviceAgentConstantFilter->addFilter("service_id = '" . $this->trackingServiceId . "' AND agent_id = '" . $this->trackingAgentId . "' ");
        $serviceAgentConstant = $serviceAgentConstantFilter->getConstantList("c.constant 'constant_name', scv.constant_value 'constant_value'");

        if (count($serviceAgentConstant) > 0) 
        {
            foreach ($serviceAgentConstant as $serviceAgentConstantData) 
            {
                $this->constants[$serviceAgentConstantData->getConstantName()] = $serviceAgentConstantData->getConstantValue();
            }
        }
       
        if ($EDI) 
        {
            $ftp_server = $this->constants['DAC_FTP_SITE'];
            $ftp_user = $this->constants['DAC_FTP_USER'];
            $ftp_pass = $this->constants['DAC_FTP_PASSWORD'];

            //     $ftp_server = "77.43.17.6";	
            //    $ftp_user = "MOP"; 
            //   $ftp_pass = "mop210314";
            $ftp_local_path = SETTING_DIR_ASSETS . "/tracking_data/DAC/";

            $conn_id = ftp_connect($ftp_server);
            ftp_login($conn_id, $ftp_user, $ftp_pass);
            ftp_pasv($conn_id, true);
            
            if($conn_id)
            {
                ftp_chdir ($conn_id, "courier_state");
                $arrfile =  ftp_nlist($conn_id, ".");
                if(sizeof($arrfile) > 0)
                {
                    foreach($arrfile as $filename)
                    {                        
                        $file_start = date("Ymd");				
                        if(strpos($filename, $file_start) !== false)
                        {
                            $fp = fopen($ftp_local_path . $filename, 'w'); 
                            $localFileName = $ftp_local_path . $filename;
                            if(ftp_get($conn_id, $localFileName, $filename, FTP_BINARY))
                            {
                                $handle = fopen($ftp_local_path.$filename, "r");			

                                if($handle) 
                                {						 
                                    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) 
                                    {
                                        $num = count($data);
                                        $row++;
                                        for ($c=0; $c < $num; $c++) 
                                        {
                                            $trackingNo = removeBomUtf8(str_replace('"','',$data[1]));
                                            $dateTime = date("Y-m-d H:i", strtotime($data[2]. " " . $data[3]));
                                            $EventCode = $data[5];	
                                            
                                            $dacTrackingStatus = DacTrackingStatus::$dac_status_code;
                                            $EventDescription = $dacTrackingStatus[ $statusCode] ."  ". $data[6];
                                            $trackPoint = $dacTrackingStatus[ $statusCode];
                                            $consignmentStatus = DacTrackingStatus::getConsignmentStatus($arrayData['trackingCode']);
                                            //$spTrackingStatus = DacTrackingStatus::getOweStatusCode($statusCode);
                                            
                                             ////////////////////// Carrier Received ////////////////////////////////

                                            $trackingDataFilterObj = new TrackingDataFilter();
                                            $trackingDataFilterObj->addFieldFilter('    tracking_number', $trackingNo);        
                                            $trackingDataFilterObj->addFilter("carrier_code not in ('')");
                                            $trackingEvents = $trackingDataFilterObj->getList();

                                            if(count($trackingEvents) > 0)
                                            {
                                                $carrierReceivedCheck = 0;   // there is already carrier received event                
                                                $trackingDataFilterObj = new TrackingDataFilter();
                                                $trackingDataFilterObj->addFieldFilter('    tracking_number', $trackingNo);        
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
                                            
                                            // Dont enter any other event code if it is against 148 Event Code
                                            if($carrierCodeCarrierReceived == $EventCode)
                                            {
                                                continue;
                                            }
                                            
                                            $entityId = 0;
                                            $parcelObj = new ParcelFilter();
                                            $parcelObj->addTrackingNumberFilter($trackingNo);
                                            $parcelDataArray = $parcelObj->getColumnList('p.id, p.tracking_number, p.consignment_id');
                                            if (count($parcelDataArray) > 0) {
                                                $parcelData = $parcelDataArray[0];
                                                $entityId = $parcelData->getId();
                                            }
                                            
                                            if($entityId >0)
                                            {
                                                $parcelEntity = new Parcel($entityId);
                                                $finalStatusCode = $parcelEntity->getParcelStatusCode();
                                                ////////////////////// Carrier Received ////////////////////////////////
                                                if($carrierReceivedCheck == 1)
                                                {
                                                    $spTrackingStatus = '148';
                                                    $carrierReceivedCheck = 0 ;                        
                                                }
                                                else
                                                {
                                                    $spTrackingStatus = DacTrackingStatus::getOweStatusCode($EventCode);
                                                } 
                                                ////////////////////// Carrier Received ////////////////////////////////
                                                $tracking->saveTrackPoint($entityId, $trackBy, $dateTime, $trackingNo, $spTrackingStatus, $ServiceAreaDescription, $EventCode, $EventDescription, $deliveredArray, $finalStatusCode);                                        
                                                $tracking->saveConsignmentTrackingStatus($trackingNo,'DacTrackingStatus');
                                            }                  
                                        }
                                    }
                                }
                            }
                            else 
                            {
                                echo "not able to get file<br>";
                            }
                        }
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
                        $this->booking_file = $booking_file = "eurob2c_" . date("YmdHis", time());

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

                                $sql = "UPDATE consignment SET send_courier_data= '1', booked_file_id = '" . $booking_file . "'
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

    private function addWayBill(Consignment $consignment, $parcel_idx, $licence_plate) {

        $this->pdf->line(3, 51, 98, 51);
        $this->pdf->line(3, 3, 98, 3);
        $this->pdf->line(98, 3, 98, 23.8);
        $this->pdf->line(3, 3, 3, 30);
        $this->pdf->line(35, 3, 35, 23.8);
        $this->pdf->Line(46, 51, 46, 60);
        $this->pdf->Line(76, 51, 76, 60);
        $this->pdf->line(3, 24, 98, 24);
        $this->pdf->line(3, 24, 3, 115);
        $this->pdf->line(3, 115, 98, 115);
        $this->pdf->line(98, 24, 98, 115);
        $this->pdf->line(3, 60, 98, 60);
        $this->pdf->line(3, 70, 3, 145);
        $this->pdf->line(98, 70, 98, 145);
        $this->pdf->line(3, 133, 98, 133);
        $this->pdf->line(3, 113, 3, 146);
        $this->pdf->line(3, 146, 98, 146);
        $this->pdf->line(98, 113, 98, 146);
        $this->pdf->line(30, 133, 30, 146);


        $logo =    User::getUserCompanyImages(false,$consignment->getUserId());
       
        if (file_exists($logo))
            $this->pdf->image($logo, 40, 4, 49, 15);
        $this->pdf->setFont("helvetica", "L", 9);
        $this->pdf->Text(45, 18.5, "www.oneworldexpress.com");



        $this->pdf->setFont("helvetica", "b", 13);
        $this->pdf->Text(5, 5, "EUROPEAN");
        $this->pdf->Text(6.5, 10, "TRACKED");
        $this->pdf->Text(8.5, 15, "PARCEL");

        $this->pdf->setFont("helvetica", "b", 10);

        if ($consignment->getValue() >= 0 && $consignment->getValue() < 15) {
            $this->pdf->Text(50, 53, "L");
        } else
        if ($consignment->getValue() >= 15 && $consignment->getValue() < 135) {
            $this->pdf->Text(50, 53, "M");
        } else
        if ($consignment->getValue() >= 135) {
            $this->pdf->Text(50, 53, "H");
        }
        $this->pdf->Text(5, 53, $account);


        if ($this->country->getRegion() == 'INT')
            $this->pdf->Text(80, 53, "NON EU");
        else if ($this->country->getRegion() == 'R1')
            $this->pdf->Text(80, 53, "EU");
        else if ($this->country->getRegion() == 'DBP')
            $this->pdf->Text(80, 53, "UK");

        $this->pdf->Text(5, 62, "Delivery Details:");

        $this->pdf->Text(12, 137, "DAC");
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


        $this->pdf->write1DBarcode($licence_plate, 'C128', 7, 18, '', 40, 2, $style, '');

        $this->pdf->write1DBarcode($consignment->getHawb(), 'C128', 15, 115, '', 15, 0.5, $style, '');

        $company = $consignment->getCompany();
        $address1 = $consignment->getAddressLine1();
        $address2 = $consignment->getAddressLine2();
        $address3 = $consignment->getAddressLine3();
        $this->pdf->setFont("helvetica", "", 10);
        $this->pdf->Text(5, 67, $consignment->getHawb());
        $this->pdf->Text(5, 71, $company);
        $this->pdf->Text(5, 75, $consignment->getContact());
        $this->pdf->Text(5, 79, $address1);
        $this->pdf->Text(5, 83, $address2);
        $this->pdf->Text(5, 87, $address3);
        $this->pdf->Text(5, 91, $consignment->getCity());
        $this->pdf->Text(5, 95, $consignment->getPostcode());
        $this->pdf->Text(55, 99, "Tel: " . $consignment->getTelephone());
        $fontname = $this->pdf->addTTFfont('../assets/fonts/simhei.ttf', 'TrueTypeUnicode', '', 32);
        $this->pdf->SetFont($fontname, '', 8);
        $this->pdf->Text(5, 105, $consignment->getNotes());


        $this->pdf->setFont("helvetica", "B", 11);
        $this->pdf->Text(5, 99, $this->country->getName());
        $this->pdf->setFont("helvetica", "L", 11);
        $this->pdf->Text(55, 95, $consignment->getReference());

        $this->pdf->setFont("helvetica", "B", 8);
        $this->pdf->Text(34, 133, "Return to:");
        $this->pdf->setFont("helvetica", "L", 8);
        $this->pdf->Text(34, 136, $this->constants['DAC_SHIPPER_ADDRESS_LINE1'] . " " . $this->constants['DAC_SHIPPER_ADDRESS_LINE2']);
        $this->pdf->Text(34, 139, $this->constants['DAC_SHIPPER_ADDRESS_LINE3'] . " " . $this->constants['DAC_SHIPPER_CITY'] . " " . $this->constants['DAC_SHIPPER_POSTCODE']);

        $shipper_country = $this->constants['DAC_SHIPPER_COUNTRY'];
        if ((int) $shipper_country > 0) {
            $shipperCountry = new Country($shipper_country);
            $sCountry = $shipperCountry->getName();
            $sIso = $shipperCountry->getIso();
        } else {
            $sCountry = $shipper_country;
        }
        $this->pdf->Text(34, 142, $sCountry . " " . $sIso);
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
                @mkdir($path, 0777, true);

            $file_path = $path . $this->booking_file . ".csv";
            chmod($path, 0777);

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

                $ftp_conn = ftp_connect($SETTING_FTP_SITE);
                if($ftp_conn){
                    $login = ftp_login($ftp_conn, $SETTING_FTP_USER, $SETTING_FTP_PASSWORD);
                    ftp_pasv($ftp_conn, true) or die("Unable switch to passive mode");
                    
                    $remote_file_path = "./manifests/" . $this->booking_file . ".csv";
                    // upload file
                    ftp_pasv($ftp_conn, true) or die("Unable switch to passive mode");
                    $isDacUpload = ftp_put($ftp_conn, $remote_file_path, $file_path, FTP_ASCII);
                    if ($isDacUpload) {
                        echo "Successfully uploaded $remote_file_path.";
                    } else {
                        $message = 'There is an error while uploading the file to DAC FTP. File name is ' . $remote_file_path;

                    }
                    ftp_close($ftp_conn);
                    $returnparam = $isDacUpload;
                }
                else
                {
                    $message = "Unabel to connect to DAC FTP  " . $SETTING_FTP_SITE_YODEL;
                    $returnparam = false;
                }
//                $ftp_object2 = new FTPfile($SETTING_FTP_USER, $SETTING_FTP_PASSWORD, $SETTING_FTP_SITE);
//                $remote_file_path2 = "./manifests/" . $this->booking_file . ".csv";
//                if (!$ftp_object2->put($remote_file_path2, $file_path, FTP_ASCII)) {
//                    mail("itsupport@oneworldexpress.com", "DAC LOGIN FAILED", "DAC LOGIN FAILED" . $this->booking_file);
//                }
//                $this->record_array = NULL;
//                return true;
            } else {
                $message = 'There is an error while uploading the file to DAC FTP. DAC constants not found in the system. file name is ' . $remote_file_path;
                $returnparam = false;
            }
            if (trim($message) != '') {
                echo $message;
                $to = 'itsupport@oneworldexpress.com';
                $subject = 'SmartTrack Dac data send to carrier issue';

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

    public function recycledShipment($consignment) {
        $output["STATUS"] = "SUCCESS";
        return $output;
    }

    private function removecommas($data) {
        return str_replace(",", " ", $data);
    }
    
     public function setTrackingParams($serviceId, $agentId)
    {        
        $this->trackingServiceId = $serviceId; 
        $this->trackingAgentId = $agentId;
    }
}
