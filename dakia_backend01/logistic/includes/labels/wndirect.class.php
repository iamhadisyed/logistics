<?php
include_classes([
    'carrierdatafilelog.class',
    'carrierdatafilelogfilter.class' 
    ]);
include_classes([
    'pdfmerger'
    ], 'labels');
class WNDirect implements CarrierService {

    private $pdf;
    private $serviceValues = null;
    private $constants = null;
    private $user = null;
    private $country = null;
    private $booking_file = null;
    private $record_array = null;
    private $trackingServiceId = null; 
    private $trackingAgentId = null;

    public function __construct() {
        
    }

    public function validation(Consignment $consignment, Services $service, Country $country) {
        $consignment->setSendCourierData(0);
        $returnOutput = array();
        $postcode = str_replace(" ","",$consignment->getPostcode());
        $firsttwochar = substr($postcode, 0, 2);
        $consignment->setSendCourierData(0);
        if(strtoupper($firsttwochar) == "BT" && $country->getIso() == "IE")
        {
            $returnOutput[] = $postcode . " belongs to United Kingdom, Please select United Kingdom in receiver country.";
        }
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
        if (trim(@$this->constants['WNDIRECT_SHIPPER_POSTCODE']) == '' || trim(@$this->constants['WNDIRECT_SHIPPER_COUNTRY']) == '' || trim(@$this->constants['WNDIRECT_SHIPPER_ADDRESS_LINE1']) == '') {
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
        
        // Generate label for each parecel
        foreach ($parcel_list as $parcel) {
            $pdf = new PdfBase(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
            $this->pdf = $pdf;
            if ($parcel->getTrackingNumber() == '') {
                $resultArray = LicencePlate::getLicencePlateNumber($licence_plate_id);

                if (trim($resultArray['STATUS']) == 'ERROR')
                    return $resultArray;
                else {
                    $licence_plate = $resultArray["PREFIX"] . $resultArray["RANGE"] . $resultArray["SUFIX"];
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
            
            // Save Piece Label
            $pieceFileName = "../_assets/pdf/" . date('Y_m_d') . '/' . $licence_plate . ".pdf";
            $this->pdf->IncludeJS("print();");
            $this->pdf->Output($pieceFileName, "F");
            $pieceLabelArray[] = $pieceFileName;
            $parcel->setParcelLabel(date('Y_m_d') . '/' . $licence_plate. ".pdf");
            $parcel->save();
            ++$parcel_idx;
        }
        
        // Merge Piece Label for consignment
        $mergeFileName = '../_assets/pdf/' . date('Y_m_d') . '/' . $consignment->getId() . ".pdf";
        $PDFMerger = new PDFMerger();
        foreach($pieceLabelArray as $key=>$pieceLabel){
            if($key == (count($pieceLabelArray)-1))
                $PDFMerger->addPDF($pieceLabel);
            else
                $PDFMerger->addPDF($pieceLabel, 1);
        }
        try {
            $PDFMerger->merge('file', $mergeFileName);
        } catch (Exception $e) {
            echo 'Caught exception: ', $e->getMessage(), "\n";
        }
        
        $output['STATUS'] = 'SUCCESS';
        $output['LABEL'] = date('Y_m_d') . '/' . $consignment->getId() . ".pdf";
        $output['TRACKING_NUMBER'] = $licence_plate_array;
        return $output;
    }

    public function tracking($trackingNumber, $trackBy = 'parcel', $EDI = false)
    {
        $tracking = new Tracking();
        $deliveredArray = array('49', '50', '54', '55', '29');
        
        if($EDI == true && !empty($this->trackingServiceId) && !empty($this->trackingAgentId))
        {
            include_once(BASE_PATH."includes/labels/wndirecttrackingstatus.class.php");
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

            $ftp_server = $this->constants['WNDIRECT_TRACKING_SERVER'];
            $ftp_user = $this->constants['WNDIRECT_TRACKING_USER'];
            $ftp_pass = $this->constants['WNDIRECT_TRACKING_PASSWORD'];
            $ftp_local_path = SETTING_DIR_ASSETS . "tracking_data/WNDIRECT/csv_in/";
            $remote_file_url ="/dmshared/out/";
            $remote_file_url_processed  ="/dmshared/archive/";

            //$ftp_server = "metapackftp.metapack.com";
            //$ftp_user = "oneworldexpressincltdimport";
            //$ftp_pass = "Onew0rldexpressincltd!mp0rt";
            //
            /////////////////////////////////Code to copy files on old System //////////////////////////////////////////
            $ftp_old_system = '213.246.110.102';
            $ftp_old_username = 'mruga';
            $ftp_old_password = 'wMfn90&0';

            $old_conn_id = ftp_connect($ftp_old_system);
            if(!$old_conn_id)
            {
                echo "Old System FTP connection failed";
                exit;
            }
            else
            {
                ftp_login($old_conn_id, $ftp_old_username, $ftp_old_password);
                ftp_pasv($old_conn_id, true);
            }

            ///////////////////////////////////END/////////////////////////////////////////////////////////////////////
            $conn_id = ftp_connect($ftp_server);

            if(!$conn_id)
            {
                echo "FTP connection failed";
                exit;
            }

            else
            {
                if (@ftp_login($conn_id, $ftp_user, $ftp_pass))
                {
                    ftp_pasv($conn_id, true);
                    $arrfile =  ftp_nlist($conn_id, $remote_file_url);

                    if($arrfile === false)
                    {
                        echo "Unable List FTP Files.";
                        exit;
                    }
                    if(sizeof($arrfile) > 0)
                    {
                        foreach($arrfile as $filename)
                        {
                            $localFileName = basename($filename);
                          
                            $fp = fopen($ftp_local_path . $localFileName, 'w');
                            
                            if (ftp_fget($conn_id, $fp, $filename,  FTP_ASCII, FTP_AUTORESUME))
                            {
                                if(!ftp_put($old_conn_id, '/httpdocs/remote/_assets/tracking_data/WNDIRECT/csv_in/'.$localFileName,$ftp_local_path . $localFileName, FTP_ASCII)){
                                    mail('itsupport@oneworldexpress.com', 'WNDIRECT', 'File failed to copy on old FTP'.$localFileName,'From:kiran.iftikhar@oneworldexpress.com');
                                }
                                if((ftp_rename($conn_id, $filename, $remote_file_url_processed . $localFileName)) !== false)
                                {
                                    $fileScannedFolder			=	SETTING_DIR_ASSETS . 'tracking_data//WNDIRECT/csv_in/';
                                    $fileScannedFolderProcessed         =	SETTING_DIR_ASSETS . 'tracking_data//WNDIRECT/csv_processed/';
                                    $handle = fopen($ftp_local_path . $localFileName, "r");
                                    if ($handle)
                                    {
                                        $row = 1;
                                        
                                        ////////////////////// Carrier Received ////////////////////////////////

                                        $trackingDataFilterObj = new TrackingDataFilter();
                                        $trackingDataFilterObj->addFieldFilter('    tracking_number', $trackingNumber);        
                                        $trackingDataFilterObj->addFilter("carrier_code not in ('','34')");
                                        $trackingEvents = $trackingDataFilterObj->getList();

                                        if(count($trackingEvents) > 0)
                                        {
                                            $carrierReceivedCheck = 0;   // there is already carrier received event                
                                            $trackingDataFilterObj = new TrackingDataFilter();
                                            $trackingDataFilterObj->addFieldFilter('    tracking_number', $trackingNumber);        
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
                                        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE)
                                        {
                                            if($row == 1)
                                            {
                                                $row++; continue;
                                            }
                                            $trackingNumber = trim($data[23]);
                                            $EventCode = trim($data[1]);
                                            $DateTime   = trim($data[12]);
                                            $EventDescription = $data[4];

                                            $trackPoint = '';
                                            // Dont enter any other event code if it is against 148 Event Code
                                            if($carrierCodeCarrierReceived == $EventCode)
                                            {
                                                continue;
                                            }
                                            
                                            $spTrackingStatus = WnDirectTrackingStatus::getOweStatusCode($EventCode);

                                            $entityId = 0;
                                            $parcelObj = new ParcelFilter();
                                            $parcelObj->addTrackingNumberFilter($trackingNumber);
                                            $parcelDataArray = $parcelObj->getColumnList('p.id, p.tracking_number, p.consignment_id');
                                            if (count($parcelDataArray) > 0)
                                            {
                                                $parcelData = $parcelDataArray[0];
                                                $entityId = $parcelData->getId();
                                            }
                                            
                                            if($entityId >0)
                                            {
                                                $parcelEntity = new Parcel($entityId);
                                                $finalStatusCode = $parcelEntity->getParcelStatusCode();
                                                ////////////////////// Carrier Received ////////////////////////////////
                                                if($carrierReceivedCheck == 1 && $EventCode != '34' )
                                                {
                                                    $spTrackingStatus = '148';
                                                    $carrierReceivedCheck = 0 ;                        
                                                }
                                                else
                                                {
                                                    $spTrackingStatus = WnDirectTrackingStatus::getOweStatusCode($EventCode);
                                                }            
                                                ////////////////////// Carrier Received ////////////////////////////////
                                                $tracking->saveTrackPoint($entityId, $trackBy, $DateTime, $trackingNumber, $spTrackingStatus, $ServiceAreaDescription, $EventCode, $EventDescription, $deliveredArray, $finalStatusCode);                                        
                                                $tracking->saveConsignmentTrackingStatus($trackingNumber, 'WnDirectTrackingStatus');                      
                                            }
                                        }                                      
                                    }
                                }
                            }
                            else
                            {
                                mail("kiran.iftikhar@oneworldexpress.com", "WNDirect File Copy Failed", $filename);
                                break;
                            }
                            rename($fileScannedFolder.$localFileName, $fileScannedFolderProcessed.$localFileName); //move file on local side                            
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
        $consignmentData->addFilter("     s.carrier_id= '198'", "servicefilter");
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
                        $run_number = CarrierDataFileLog::generateRunNumber($carrierId, $agentid,false);
                        $this->booking_file = "OW" . $run_number . ".csv";

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

        $this->pdf->line(3, 58, 98, 58);
        $this->pdf->line(3, 3, 98, 3);
        $this->pdf->line(98, 3, 98, 23.8);
        $this->pdf->line(3, 3, 3, 30);
        $this->pdf->line(41, 3, 41, 23.8);
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

        $image = realpath("../images/logo.jpg");
        $this->pdf->image($image, 5, 8, 32);
        $image = realpath("../images/wndirect.jpg");
        $this->pdf->image($image, 53, 8, 32);

        $this->pdf->setFont("helvetica", "b", 10);
        $this->pdf->Text(5, 59, "Notes:");
        $this->pdf->setFont("helvetica", "", 9);
        $this->pdf->Text(18, 59, ucfirst(strtolower($consignment->getNotes())));

        $this->pdf->setFont("helvetica", "b", 10);
        $this->pdf->Text(5, 64, "Pieces : " . ($parcel_idx + 1) . "/" . $consignment->getNumberPieces());


        $this->pdf->setFont("helvetica", "b", 13);
        $this->pdf->Text(30, 72, "Delivery Details:");


        $this->pdf->Text(11, 136.5, $this->country->getIso());
        $this->pdf->setFont("helvetica", "L", 9);

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
            $address1 = utf8_decode($consignment->getAddressLine1());
        if ($consignment->getAddressLine2() != "")
            $address2 = $consignment->getAddressLine2();
        if ($consignment->getAddressLine3() != "")
            $address3 = $consignment->getAddressLine3();



        $this->pdf->setFont("helvetica", "L", 11);
        //  $this->pdf->Text(55, 81, $consignment->getHawb());
        $this->pdf->Text(5, 81, $company);
        //$this->pdf->Text(55, 85, "Email: ".$consignment->getEmail());
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

        $this->pdf->Text(34, 136, $this->constants['WNDIRECT_SHIPPER_ADDRESS_LINE1'] . " " . $this->constants['WNDIRECT_SHIPPER_ADDRESS_LINE2']);
        $this->pdf->Text(34, 139, $this->constants['WNDIRECT_SHIPPER_ADDRESS_LINE3'] . " " . $this->constants['WNDIRECT_SHIPPER_CITY'] . " " . $this->constants['WNDIRECT_SHIPPER_POSTCODE']);

        $shipper_country = $this->constants['WNDIRECT_SHIPPER_COUNTRY'];
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
        $record = "";

        $record .= $consignment->getHawb() . ","; //// 1a order number
        $record .= $this->removecommas(utf8_encode($consignment->getContact())) . ","; //2 name
        $record .= $this->removecommas(utf8_encode($consignment->getAddressLine1())) . ",";  // 3 address line 1			
        $record .= $this->removecommas(utf8_encode($consignment->getAddressLine2())) . ","; // 4 address line 2
        $record .= $this->removecommas($consignment->getCity()) . ","; // 5 address city
        $record .= $this->removecommas($consignment->getState()) . ","; // 6 address state
        $record .= $consignment->getPostcode() . ","; // 7  postcode
        $record .= $this->country->getIso() . ","; // 8
        $record .= $consignment->getNumberPieces() . ","; //9
        $record .= $consignment->getWeight() . ","; //10
        $record .= $this->removecommas($consignment->getDescription()) . ","; //11
        $record .= $consignment->getValue() . ","; //12
        $record .= $this->country->getIso() . ","; //13
        $record .= ","; //14
        $record .= $consignment->getWeight() . ","; //15
        $record .= $consignment->getNumberPieces() . ",";  //16 
        $record .= $this->removecommas(utf8_encode($consignment->getDescription())) . ","; //17
        $record .= ","; //18
        $record .= $consignment->getEmail() . ","; //End contact email //19
        $record .= "-" . ","; //Product code (SKU) // 20
        $record .= $consignment->getTelephone() . ","; //End contact phone //21
        $record .= $consignment->getTelephone() . ","; // 22
        $record .= str_pad($consignment->getTrackingNumber(), 10) . ","; //23

        if ($this->country->getRegion() == 'INT')
            $record .= "INTL" . ","; //Delivery Type // 24
        else
            $record .= "" . ","; //Delivery Type //24

        $record .= str_pad($consignment->getTrackingNumber(), 10) . ",";   // "Carrier Consignment Code" //25
        $record .= "0" . ",";   // "Carrier Consignment Code" //26
        $record .= "INTL" . ","; // Carrier Service Code		//27
        $record .= "OneW" . ","; //28
        $record .= $constant["WNDIRECT_CUSTOMER_NO"] . ","; //Customer Number //29"2210"
        $record .= ","; //30
        $record .= $consignment->getWeight() . ","; //31
        $record .= $constant["WNDIRECT_SHIPPER_CONTACT"] . ",";  //contact //32
        $record .= $constant["WNDIRECT_SHIPPER_ADDRESS_LINE1"] . ",";  // sender address line 1 //33
        $record .= $constant["WNDIRECT_SHIPPER_ADDRESS_LINE2"] . ",";  // sender address line 2 //34
        $record .= $constant["WNDIRECT_SHIPPER_CITY"] . ",";   // city //35
        $record .= "." . ","; // sender address line 1 //36
        $record .= $constant["WNDIRECT_SHIPPER_POSTCODE"] . ","; // sender post code //37
        $shipper_country = $constant['WNDIRECT_SHIPPER_COUNTRY'];
        if ((int) $shipper_country > 0) {
            $shipperCountry = new Country($shipper_country);
            $sCountry = $shipperCountry->getName();
            $sIso = $shipperCountry->getIso();
        } else {
            $sCountry = $shipper_country;
        }
        $record .= "GBR" . ","; // country iso code // 38
        $record .= "GBP" . ","; // 39 
        $record .= "\r\n";

        return $record;
    }

    private function sendBookings($ftpConstants) {
        require_once(SETTING_DIR_REMOTE . "includes/3rdparty/Net/SFTP.php");
        if (sizeof($this->record_array) > 0) {
            $path = SETTING_DIR_ASSETS . "data_send/wndirect_booking/" . date("Y_m_d") . "/";
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

            
            if (isset($ftpConstants['WNDIRECT_FTP_SITE']) && trim($ftpConstants['WNDIRECT_FTP_SITE']) != '') {
                $SETTING_FTP_USER = $ftpConstants['WNDIRECT_FTP_USER'];
                $SETTING_FTP_PASSWORD = $ftpConstants['WNDIRECT_FTP_PASSWORD'];
                $SETTING_FTP_SITE = $ftpConstants['WNDIRECT_FTP_SITE'];

                $ssh = new Net_SFTP($SETTING_FTP_SITE);
                if (!$ssh->login($SETTING_FTP_USER, $SETTING_FTP_PASSWORD)) {
                    mail("itsupport@oneworldexpress.com", "WNDIRECT LOGIN FAILED", "WNDIRECT LOGIN FAILED" . $this->booking_file);
                }
                $remotefile = "./" . $this->booking_file ;
                $isUpload = $ssh->put($remotefile, $file_path, NET_SFTP_LOCAL_FILE);
                     


                $this->link_file = NULL;
                $this->record_array = NULL;
                return $isUpload;
            } 
        }
    }

    private function removecommas($data) {
        return str_replace(",", " ", $data);
    }

    public function recycledShipment($consignment) {
        $output["STATUS"] = "SUCCESS";
        return $output;
    }
    
    public function setTrackingParams($serviceId, $agentId)
    {        
        $this->trackingServiceId = $serviceId; 
        $this->trackingAgentId = $agentId;
    }

}
