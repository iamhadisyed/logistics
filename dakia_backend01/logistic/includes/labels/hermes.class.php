<?php
include_classes([
    'hermesdepotmapping.class',
    'hermesdepotmappingfilter.class',
    'carrierdatafilelog.class',
    'carrierdatafilelogfilter.class',
]);
include_classes([
    'pdfmerger'
    ], 'labels');

include_classes([
    'SFTP',
    ], '3rdparty/Net');
class Hermes implements CarrierService {

    private $pdf;
    private $serviceValues = null;
    private $constants = null;
    private $user = null;
    private $country = null;
    private $agentValues = null;
    private $booking_file = null;
    private $presortRecords = array();
    private $trackingServiceId = null;
    private $trackingAgentId = null;
        
    public function __construct() {
        
    }

    public function validation(Consignment $consignment, Services $service, Country $country) {
        $returnOutput = array();
        
        
        if($consignment->getTelephone() != ""){
            $phoneValidate = $this->ValidateTelephone($consignment->getTelephone());
            if($phoneValidate == false){
                $returnOutput[] = "Please enter valid Phone Number. ";
            }
        }
        
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
        $nextDayCheck = "";
        if(in_array($service->getCode(), array('STHRM0001', 'STHRM001S', 'STHRM0SUN')))
        {
            $nextDayCheck = " and next_day_service='Y'";
        }

        $sql = "SELECT pos_pcd_postcode_excluded_indicator,sort_level_key   FROM `hermes_postcode_record` WHERE fullpostcode = '" . $postcode . "' ". $nextDayCheck ."  limit 0,1";
        $rs = DbAccess3::runQuery($sql);

        if (mysqli_num_rows($rs) == 0) {

            $sql = "SELECT pos_pcd_postcode_excluded_indicator,sort_level_key   FROM `hermes_postcode_record` WHERE fullpostcode = '" . $postcodenum . "' ". $nextDayCheck ." limit 0,1";

            $rs = DbAccess3::runQuery($sql);
            if (mysqli_num_rows($rs) == 0) {
                $sql = "SELECT pos_pcd_postcode_excluded_indicator,sort_level_key   FROM `hermes_postcode_record` WHERE fullpostcode = '" . $halfpostcode . "'". $nextDayCheck ." limit 0,1";
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
                if(in_array($service->getCode(), array('STHRM0001', 'STHRM001S', 'STHRM0SUN'))){
                    $depotCode = substr($hermes_barcode_1_to_7, 0,2);
                    $sql = "SELECT *  FROM hermes_next_day_depot WHERE trim(depot_code) ='" . $depotCode . "'";
                    $rs = DbAccess3::runQuery($sql);
                    while ($row = mysqli_fetch_assoc($rs)) {
                        $nextDayDepot =  $row["nextday_depot_code"];
                    }
                    $hermes_barcode_1_to_7 = str_replace($depotCode, $nextDayDepot, $hermes_barcode_1_to_7);
                }
                
            }

            // }
        }

        if (trim($hermes_barcode_1_to_7) == '')
            $returnOutput[] = "We are not able to provide service of your required postcode";


        $hermesparameters = $Number_of_Barcodes . "||" . $hermes_barcode_1_to_7 . "||" . $hermes_barcode_seq_key . "||" . $carrier_id . "||" . $mod_id . "||" . $mode_name . "||" . $sort_level_key . "||" . $level_1_type . "||" . $level_1_name . "||" . $level_1_code . "||" . $level_2_type . "||" . $level_2_name . "||" . $level_2_code . "||" . $level_3_type . "||" . $level_3_name . "||" . $level_3_code . "||" . $level_4_type . "||" . $level_4_name . "||" . $level_4_code . "||" . $level_5_type . "||" . $level_5_name . "||" . $level_5_code;
        $consignment->setOtherRoutingCode($hermesparameters);
        $consignment->setRoutingCodeEur($level_1_code);
        return $returnOutput;
    }
    private function ValidateTelephone($string){
       
        $pattern = "/^((\+44\s?\d{4}|\(?\d{5}\)?)\s?\d{6})|((\+44\s?|0)7\d{3}\s?\d{6})$/";

        $match = preg_match($pattern,$string);
        return $match;
        
    }

    public function remoteareas($consignment, $carrierObject, $sender) {
        
    }

    public function label($consignment, $labelType = 'pdf', $size = '100x150') {

        $pdf = new PdfBase(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $this->pdf = $pdf;

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
                    $range = sprintf("%09d", $resultArray["RANGE"]);
                    $licence_plate = $resultArray["PREFIX"] . $range . $resultArray["SUFIX"];
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
            
            // Save Piece Label
            $pieceFileName = "../_assets/pdf/" . date('Y_m_d') . '/' . $trackingNo . ".pdf";
            $this->pdf->IncludeJS("print();");
            $this->pdf->Output($pieceFileName, "F");
            $pieceLabelArray[] = $pieceFileName;
            $parcel->setParcelLabel(date('Y_m_d') . '/' . $trackingNo. ".pdf");
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
            include_once(BASE_PATH."includes/labels/hermestrackingstatus.class.php");
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
            
            
            $sftp_server = $this->constants['HERMES_FTP_SITE'];
            $sftp_user = $this->constants['HERMES_FTP_USER'];
            $sftp_pass = $this->constants['HERMES_FTP_PASSWORD'];
            
            $localPath = SETTING_DIR_ASSETS . "tracking_data/HERMES/data_in/";
            if (!file_exists($localPath))
                @mkdir($localPath, 0777, TRUE);
            
            $remotePath ="/Archive/";
            $remoteProcessedPath  ="/Out/Processed/";

            $ftpConnection = new Net_SFTP($sftp_server);
            if (!$ftpConnection->login($sftp_user, $sftp_pass)) 
            {
                echo "SFTP Login Failed, check your username and passwor";
                exit;
            } 
                
            else 
            {                
                $getAllFiles = $ftpConnection->_list($remotePath);
                if (count($getAllFiles) > 0) 
                {
                    foreach ($getAllFiles as $keyFile => $valueFile) 
                    {
                        $fileName = $valueFile['filename'];
                        $processFileName = "PARCEL_TRACKING2145_".date("Y-m-d");
                        if (in_array($fileName, array('.', '..')))
                            continue;
                        if(strpos($fileName, $processFileName) === false)
                                continue;
                        $remoteFile = $remotePath . $fileName;                        
                        $remoteProcessedFile = $remoteProcessedPath . $fileName;
                        $localFile = $localPath . $fileName;
                        
                        $ftpConnection->get($remoteFile, $localFile);
                        $ftpConnection->rename($remoteFile, $remoteProcessedFile);    
                        
                        $handle = fopen($localFile, "r");
                        $header = 0; 
                        
                        if ($handle) 
                        {
                            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) 
                            {
                              if($header == 0)
                                {
                                    $header++;
                                    continue;
                                }  
                   //                 $fileScannedFolder			=	SETTING_DIR_ASSETS . 'tracking_data/HERMES/data_in/';
                     //               $fileScannedFolderProcessed         =	SETTING_DIR_ASSETS . 'tracking_data/HERMES/data_processed/';
                       //             $handle = fopen($ftp_local_path . $filename, "r");
                                  //  if ($handle)
                                   // {
                                        //$row = 1;
                                        
                                        //while (($data = fgetcsv($handle, 1000, ",")) !== FALSE)
                                        //{
                                            $carrierCodeCarrierReceived = 0;
                                            
                                            if($data[0] != 'TRACK')
                                            {
                                                continue;
                                            }
                                            else
                                            {
                                                $trackingNumber = $data[3];
                                                
                                                ////////////////////// Carrier Received ////////////////////////////////

                                                $trackingDataFilterObj = new TrackingDataFilter();
                                                $trackingDataFilterObj->addFieldFilter('    tracking_number', $trackingNumber);        
                                                $trackingDataFilterObj->addFilter("carrier_code not in ('','1012','1663','1084')");
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

                                                
                                                $EventCode = $data[6];
                                                $dateTime = substr($data[7], 0, 2) . '-' . substr($data[7], 2, 2) . '-' . substr($data[7], 4, 8) . ' ' . substr($data[8], 0, 2) . ':' . substr($data[8], 2, 2);
                                                $dateTime =  date('Y-m-d H:i', strtotime($dateTime));
                                                $EventDescription = HermesTrackingStatus::$hermes_status_code[$EventCode];
                                                $trackPoint = HermesTrackingStatus::$hermes_status_code[$EventCode];
                                            }
                                            // Dont enter any other event code if it is against 148 Event Code
                                            if($carrierCodeCarrierReceived == $EventCode)
                                            {
                                                continue;
                                            }
                                            
                                            $spTrackingStatus = HermesTrackingStatus::getOweStatusCode($EventCode);

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
                                                if($carrierReceivedCheck == 1 &&  $EventCode != '1012' &&  $EventCode != '1084')
                                                {
                                                    $spTrackingStatus = '148';
                                                    $carrierReceivedCheck = 0 ;                        
                                                }
                                                else
                                                {
                                                    $spTrackingStatus = HermesTrackingStatus::getOweStatusCode($EventCode);
                                                } 
                                                //echo $spTrackingStatus; die;
                                                ////////////////////// Carrier Received ////////////////////////////////
                                                $tracking->saveTrackPoint($entityId, $trackBy, $dateTime, $trackingNumber, $spTrackingStatus, $ServiceAreaDescription, $EventCode, $EventDescription, $deliveredArray, $finalStatusCode);                                        
                                                $tracking->saveConsignmentTrackingStatus($trackingNumber, 'HermesTrackingStatus');                      
                                            }
                                        }                                      
                                    }
                                }
                            } // end if getAlla files
                            //else
                            //{
                              //  mail("kiran.iftikhar@oneworldexpress.com", "File Copied fail (HERMES)", $filename);
                              //  break;
                            //}
                            //rename($fileScannedFolder.$localFileName, $fileScannedFolderProcessed.$localFileName); //move file on local side                            
            } //end else 
        }// if EDI true end 
    }// end function

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
                    $consignmentData->addFilter("    ( c.send_courier_data= '0' or c.send_courier_data is null)", "consignmentfilter");
                    if (!empty($tracking_numbers))
                        $consignmentShipmentDataFilter->addFilter("     AND pc.tracking_number in ('" . implode("','", $tracking_numbers) . "') and pc.tracking_number <> ''", "parcelJoinFilter");
                    $consignmentShipmentData = $consignmentShipmentDataFilter->getColumnList(" c.id 'consignment_id', s.carrier_id ,s.code 'service_code',
                     con.region 'country_region', c.service_id, c.weight, c.hawb, c.description, c.company, c.country_id,c.awb,c.date_created,c.value,c.number_pieces,
                      c.contact, c.address_line_1, c.address_line_2, c.city, c.postcode, c.telephone, c.other_routing_code, routing_code_eur,pc.tracking_number, c.eori_number, c.currency, c.sender_country_id, c.email, c.telephone ");

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
                        $this->booking_file = "PARCEL_PRE_ADVICE" . $this->constants[$serviceid]["HERMES_CLIENT_ID"] . "_" . date("Y-m-d_H-i-s", time());

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
        
        
        $hermes_barcode_display = mb_substr($trackingNo, 0, $strlen - ($strlen - 1)) . "-";
        $hermes_barcode_display1 = mb_substr($trackingNo, 1, $strlen - ($strlen - 4)) . "-";
        $hermes_barcode_display2 = mb_substr($trackingNo, 5, $strlen - ($strlen - 1)) . "-";
        $hermes_barcode_display3 = mb_substr($trackingNo, 6, $strlen - ($strlen - 9)) . "-";
        $hermes_barcode_display4 = mb_substr($trackingNo, 15, $strlen - ($strlen - 1));
        $hermes_barcode_display = $hermes_barcode_display . $hermes_barcode_display1 . $hermes_barcode_display2 . $hermes_barcode_display3 . $hermes_barcode_display4;

        $this->pdf->setFont("helvetica", "", 10);



        $this->pdf->Text(5, 1, "www.oneworldexpress.com");




//       
//        $this->centreText(12, 47, 65, $hermes_barcode_display, $this->pdf);
         $this->pdf->setFont("helvetica", "", 13);
        $this->pdf->Text(8, 55, $hermesparametersarray[9]);

       
        $this->pdf->line(7, 55, 16, 55);
        $this->pdf->line(7, 55, 7, 60);
        $this->pdf->line(7, 60, 16, 60);
        $this->pdf->line(16, 60, 16, 55);




        $x = 50;
        $this->pdf->Text($x, 55, $hermesparametersarray[7]);
        $this->pdf->Text($x + 30, 55, $hermesparametersarray[8]);

        $this->pdf->line(78, 54, 96, 54);
        $this->pdf->line(78, 54, 78, 61);
        $this->pdf->line(78, 61, 96, 61);
        $this->pdf->line(96, 61, 96, 54);
        $this->pdf->setFont("helvetica", "B", 13);
        $this->pdf->Text($x, 62, $hermesparametersarray[10]);
        $this->pdf->Text($x + 30, 62, $hermesparametersarray[11]);


        $this->pdf->setFont("helvetica", "L", 10);
        $this->pdf->Text($x, 67, $hermesparametersarray[13]);
        $this->pdf->Text($x + 30, 67, $hermesparametersarray[14]);
        $this->pdf->Text($x, 71, $hermesparametersarray[16]);
        $this->pdf->Text($x + 30, 71, $hermesparametersarray[17]);

        
        $this->pdf->Text(8, 61, "0");
        $this->pdf->Text(8, 67, "0");
    
        $this->pdf->setFont("helvetica", "B", 12);
        if(in_array($this->serviceValues->getCode(), array('STHRM0001', 'STHRM001S', 'STHRM0SUN')))
            $this->pdf->Text(8, 71, "Next Day");
        else
            $this->pdf->Text(8, 71, "2DAY");
        
        
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

        $this->pdf->line(8, 76, 92, 76);
        $this->pdf->line(8, 76, 8, 120);
        $this->pdf->line(8, 120, 92, 120);
        $this->pdf->line(92, 120, 92, 76);

        $y = 74;
        $this->pdf->setFont("helvetica", "", 9);
        if($company != "")
            $this->pdf->Text(10, $y+=4, $company);
        if($consignment->getContact() != "")
            $this->pdf->Text(10, $y+=4, $consignment->getContact());
        if($address != "")
            $this->pdf->Text(10, $y+=4, $address);
        if($address1 != "")
            $this->pdf->Text(10, $y+=4, $address1 . " " . $address2);
        
        $this->pdf->Text(10, $y+=4, $consignment->getCity());
        $this->pdf->Text(10, $y+=4, "United Kingdom");

        $this->pdf->Text(10, $y+=4, $consignment->getPostcode());
        if($consignment->getTelephone() != "")
        $this->pdf->Text(10, $y+=4, "Tel: " . $consignment->getTelephone() );
        $this->pdf->Text(10, $y+=4, "Ref No: " . $consignment->getHawb());

         $this->pdf->Rect(8, 110, 60, 7, 'D');
         if ($this->serviceValues->getCode() == "STHRM001S" || $this->serviceValues->getCode() == "STHRM002S") {
            $this->pdf->setFont("helvetica", "B", 10);
            $this->pdf->Text(9, 112, "POD");
        }
        $this->pdf->Text(30, 112, "COU-PNET");
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
        
        $this->pdf->write1DBarcode($trackingNo, 'C128', 10, 125, '', 20, 0.5, $style, 'Y');
        $this->pdf->setFont("helvetica", "", 12);
        $this->centreText(14, 144, 65, $hermes_barcode_display, $this->pdf);
    
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
         if($this->serviceValues->getCode() == "STHRM0001" || $this->serviceValues->getCode() == "STHRM0SUN"){
            $barcodeMatrix .= "NDAY"; // Service Name
        }
        else if($this->serviceValues->getCode() == "STHRM002S"){
            $barcodeMatrix .= "POD";  //Pcl-Rec-Service-List
        }
        else if($this->serviceValues->getCode() == "STHRM001S"){
            $barcodeMatrix .= "NDAY||POD";  //Pcl-Rec-Service-List
        }
        else{
            $barcodeMatrix .= "2DAY"; // Service Name
        }
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
        $telephoneScruble = "";
        if($consignment->getTelephone() != ""){
            $telephoneScruble = $this->TelephoneScrumble($consignment->getTelephone());
        }
        $barcodeMatrix .= $telephoneScruble;  //Phone
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
        $barcodeMatrix .= $hermesparametersarray[9] . $hermesparametersarray[18];   //Courier Round Id
        $barcodeMatrix .= "||";   //Data Seprator
        $barcodeMatrix .= "";   //Parcel Shop Id
        $barcodeMatrix .= "||";   //Data Seprator
        $barcodeMatrix .= "";   //Parcel Shop Name
        $barcodeMatrix .= "||";   //Data Seprator
        $barcodeMatrix .= $hermesparametersarray[9];   //Depot Id
        $barcodeMatrix .= "||";   //Data Seprator
        $barcodeMatrix .= $hermesparametersarray[12];   //Van Route Id
        $barcodeMatrix .= "++";  //Seprator
        $barcodeMatrix .= "";   //Client AR Link
        $barcodeMatrix .= "||";   //Data Seprator
        $barcodeMatrix .= "";   //Customer AR Link
        $barcodeMatrix .= "++";   //Data Seprator
        $barcodeMatrix .= $this->constants["HERMES_SENDER_COMPANY"];   //Return Name
        $barcodeMatrix .= "||";   //Data Seprator
        $barcodeMatrix .= $this->constants["HERMES_SENDER_ADD_LINE_1"];
        ;   //Return Address Line 1
        $barcodeMatrix .= "||";   //Data Seprator
        $barcodeMatrix .= $this->constants["HERMES_SENDER_ADD_LINE_2"];
        ;   //Return Address Line 2
        $barcodeMatrix .= "||";   //Data Seprator
        $barcodeMatrix .= $this->constants["HERMES_SENDER_ADD_LINE_3"];
        ;   //Return Address Line 3
        $barcodeMatrix .= "||";   //Data Seprator
        $barcodeMatrix .= $this->constants["HERMES_SENDER_CITY"];
        ;   //Return Address Line 4
        $barcodeMatrix .= "||";   //Data Seprator
        $barcodeMatrix .= "";   //Return Address Line 5
        $barcodeMatrix .= "||";   //Data Seprator
        $barcodeMatrix .= "";   //Return Address Line 6
        $barcodeMatrix .= "||";   //Data Seprator
        $barcodeMatrix .= $this->constants["HERMES_SENDER_POSTCODE"];
        ;   //Return Postcode
        $barcodeMatrix .= "++";  //Seprator
        if($this->serviceValues->getCode() == "STHRM0SUN" ){
            $dispatchDate = date("dmY", strtotime("next Saturday"));
        }
        else
        {
           $dispatchDate = date("dmY");
        }
        $barcodeMatrix .= $dispatchDate;   //Despatch Date
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

        $this->pdf->write2DBarcode($barcodeMatrix, 'PDF417,2,5', 8, 10, '', 37, $style, '', 'Y');
    }

    private function TelephoneScrumble($phone){
         $phone = str_replace(array('(', ')', ' ', ',', '.', '|'), '', $phone);
         $dateDispatchPattern = date("Ymd").date("Ymd").date("Ymd").date("Ymd");
         $telephonelength = strlen($phone);
         $datePattern = substr($dateDispatchPattern, 0, $telephonelength);
         $datePatternArray = str_split($datePattern);
         $phoneArray = str_split($phone);
         
         $outputTelephone = "";
         foreach($datePatternArray as $key => $dateArr){
             if($phoneArray[$key] == "+"){
                 $outputTelephone .= "+";
             }
             else{
                $total = $dateArr + $phoneArray[$key];
                if($total >= 10){
                    $total = $total - 10;
                }
                $outputTelephone .= $total;
             }
         }
         return $outputTelephone;
    }
    private function centreText($x, $y, $width, $msg) {
        $leftMargin = ($width - $this->pdf->GetStringWidth($msg)) / 2;
        //
        $this->pdf->setXy($x + $leftMargin, $y, $msg);
        $this->pdf->Write(1, $msg);
    }
    
    private function createhermesawb($consignment, $licence_plate) {

        //if($account == "4444")
        // {
        $prefix = "T";
        $clientBase36 = "01NL"; //  Base 36 for 100
        $sortationIndicator = "A";
        
        $awbno = $licence_plate;
        
        $barcode = $prefix.$clientBase36.$sortationIndicator.$awbno;
        $checkdigit = $this->CheckSumAlphaNumeric($barcode);
        $barcode = $barcode.$checkdigit;
        return $barcode;
        
    }
    
     private function CheckSumAlphaNumeric($barcode){
        $barcode = str_replace("T", 1, $barcode);
        $barcode = str_replace("N", 5, $barcode);
        $barcode = str_replace("L", 3, $barcode);
        $barcode = str_replace("A", 2, $barcode);
        $arr = str_split($barcode);
        $checkdigit =     ($arr[0] * 2) 
                        + ($arr[1] * 1) 
                        + ($arr[2] * 2) 
                        + ($arr[3] * 1) 
                        + ($arr[4] * 2) 
                        + ($arr[5] * 1) 
                        + ($arr[6] * 2) 
                        + ($arr[7] * 1) 
                        + ($arr[8] * 2) 
                        + ($arr[9] * 1) 
                        + ($arr[10] * 2) 
                        + ($arr[11] * 1) 
                        + ($arr[12] * 2) 
                        + ($arr[13] * 1) 
                        + ($arr[14] * 2);

        $checkdigit = $checkdigit % 10;

        
        if ($checkdigit % 10 == 0) {
            $checkdigit = 0;
        } else {
            $checkdigit = $checkdigit;
        }
        return $checkdigit;
    }
    
    
    
    private function getCheckDigit($awbno){
        
        $arr = str_split($awbno);
        $checkdigit = ($arr[0] * 3) + ($arr[1] * 1) + ($arr[2] * 3) + ($arr[3] * 1) + ($arr[4] * 3) + ($arr[5] * 1) + ($arr[6] * 3) + ($arr[7] * 1) + ($arr[8] * 3) + ($arr[9] * 1) + ($arr[10] * 3) + ($arr[11] * 1) + ($arr[12] * 3) + ($arr[13] * 1) + ($arr[14] * 3);




        $checkdigit = $checkdigit % 10;


        if ($checkdigit % 10 == 0) {
            $checkdigit = 0;
        } else {
            $checkdigit = 10 - $checkdigit;
        }
        return $checkdigit;
    }
    
   

    private function getShipmentRecord(Consignment $consignment, $constant) {
        $handling = $this->serviceValues->getCode();
        $parcel_list = $consignment->getParcels();
        $this->country = new Country($consignment->getCountryId());
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
        $record = '"'."PREADVICE".'",';  //Pcl-Rec-Record-Type
        $record .= '"'.$constant["HERMES_CLIENT_ID"].'",'; //Pcl-Rec-Client-ID 854

        $record .= '"",';  //Pcl-Rec-Client-Child-ID
        $record .= '"'.$constant["HERMES_CLIENT_NAME"].'",';  //Pcl-Rec-Client-Name

        $record .= '"",'; // 5  //Pcl-Rec-Client-Child-Name
        $record .= '"'.$hermesparametersarray[3].'",';  //Pcl-Rec-Carrier-ID
        $record .= '"'.$hermesparametersarray[4].'",'; //Pcl-Rec-MOD-ID
        $record .= '"",'; // 5  //Pcl-Rec-Node-Type
        $record .= '"",'; // 5  //Pcl-Rec-Node-ID
        //$record .= $this->fld(18, $hermesparametersarray[5]);   //Pcl-Rec-MOD-Name

        $record .= '"'.$consignment->getAWB() . '",'; //Pcl-Rec-Hermes-Barcode-Number

        $record .= '"'. $consignment->getHawb() . '",'; //Pcl-Rec-Customer-Reference-1
        $record .= '"",'; //Pcl-Rec-Customer-Reference-2
        //
        $record .= '"'. $company . '",'; // 6 Your customers’ name see notes.

        $record .= '"'. $consignment->getAddressLine1() . '",'; // 6
        $record .= '"'. $consignment->getAddressLine2(). '",';
        $record .= '"'.$consignment->getAddressLine3(). '",';
        $record .= '"'.$consignment->getCity(). '",';
        $record .= '"'.$consignment->getState(). '",';
        $record .= '"",';
        $record .= '"'. $consignment->getPostcode() . '",';
        $record .= '"'. $this->country->getIso() . '",';
        $record .= '"' . $consignment->getTelephone() .'",';
        $record .= '"",';   //Pcl-Rec-Customer-Work-Phone-No
        $record .= '"",';  //Pcl-Rec-Customer-Mobile-Phone-No
        $record .= '"'. $consignment->getEmail() . '",'; //Pcl-Rec-Customer-E-Mail-Address
        $record .= '"",'; //Pcl-Rec-Customer-Sms-Alert-Group

        $record .= '"'. $hermesparametersarray[7]. '",'; //Pcl-Rec-Sort-Point-1-Type
        $record .= '"'. $hermesparametersarray[8]. '",'; //Pcl-Rec-Sort-Point-1-Name
        $record .= '"'.  $hermesparametersarray[9]. '",'; //Pcl-Rec-Sort-Point-1-Code

        $record .= '"'.  $hermesparametersarray[10]. '",'; //Pcl-Rec-Sort-Point-2-Type
        $record .= '"'.  $hermesparametersarray[11]. '",'; //Pcl-Rec-Sort-Point-2-Name
        $record .= '"'.  $hermesparametersarray[12]. '",'; //Pcl-Rec-Sort-Point-2-Code

        $record .= '"'.  $hermesparametersarray[13]. '",'; //Pcl-Rec-Sort-Point-3-Type
        $record .= '"'.  $hermesparametersarray[14]. '",'; //Pcl-Rec-Sort-Point-3-Name
        $record .= '"'.  $hermesparametersarray[15]. '",'; //Pcl-Rec-Sort-Point-3-Code


        $record .= '"'.  $hermesparametersarray[16]. '",'; //Pcl-Rec-Sort-Point-4-Type
        $record .= '"'.  $hermesparametersarray[17]. '",'; //Pcl-Rec-Sort-Point-4-Name
        $record .= '"'.  $hermesparametersarray[18]. '",'; //Pcl-Rec-Sort-Point-4-Code

        $record .= '"'.  $hermesparametersarray[19]. '",'; //Pcl-Rec-Sort-Point-5-Type
        $record .= '"'.  $hermesparametersarray[20]. '",'; //Pcl-Rec-Sort-Point-5-Name
        $record .= '"'.  $hermesparametersarray[21]. '",';  //Pcl-Rec-Sort-Point-5-Code
        //
        
        //
        $record .= '"'. $consignment->getWeight() * 1000 . '",'; //Pcl-Rec-Parcel-Weight
        $record .= '"'. $parcel_list[0]->getLength() . '",'; //Pcl-Rec-Parcel-Length
        $record .= '"'. $parcel_list[0]->getWidth() . '",'; // Pcl-Rec-Parcel-Width
        $record .= '"'. $parcel_list[0]->getHeight() . '",'; // 15 - Pcl-Rec-Parcel-Depth
        $record .= '"'. $consignment->getValue() * 100 . '",';  // 21 -  Pcl-Rec-Parcel-Value
        $record .= '"",'; //Pcl-Rec-Parcel-Type
        $record .= '"1"'. ","; //Pcl-Rec-Number-of-Parts
        $record .= '"1"'. ","; //Pcl-Rec-Number-of-Items
        $record .= '" "'. ","; //Pcl-Rec-Parcel-Description
        $record .= '" "'. ","; //Pcl-Rec-Parcel-Origin
        $record .= '"",'; //Pcl-Rec-Delivery-Message
        $record .= '"",'; //Pcl-Rec-Special-Instructions-1
        $record .= '"",';  //Pcl-Rec-Special-Instructions-2
        $record .= '"",'; //Pcl-Rec-Required-Delivery-Date //$this->fld(8, date("dmY", time()));
        $record .= '"",'; //Pcl-Rec-Required-Delivery-Time//$this->fld(8, date("dmY", time()));
        if($this->serviceValues->getCode() == "STHRM0SUN" ){
            $dispatchDate = date("dmY", strtotime("next Saturday"));
            $record .= '"' . $dispatchDate . '",'; //Pcl-Rec-Expected-Despatch-Date
        }
        else
        {
            $dispatchDate = time() + 86400;
            $record .= '"' . date("dmY", $dispatchDate) . '",'; //Pcl-Rec-Expected-Despatch-Date
        }
        $record .= '"",'; //Pcl-Rec-Consigned-Pin-Service-Flag
        if($this->serviceValues->getCode() == "STHRM0001" || $this->serviceValues->getCode() == "STHRM0SUN" ){
            $record .= '"NDAY",';
        }
        else if($this->serviceValues->getCode() == "STHRM002S"){
            $record .= '"2DAY,POD",';  //Pcl-Rec-Service-List
        }
        else if($this->serviceValues->getCode() == "STHRM001S"){
            $record .= '"NDAY,POD",';  //Pcl-Rec-Service-List
        }
        else{
            $record .= '"2DAY",';  //Pcl-Rec-Service-List
        }
        
        
        $record .= '"",';  //Pcl-What3words
        
        $record .= '"GB",';  //Pcl-Country-Of-Origin
        $record .= '"DDU",';  //Pcl-Incoterm
        $record .= '"",';  //Pcl-Duty-Paid-Value
        $record .= '"",';  //Pcl-VAT-Value
        $record .= '"'.$consignment->getCurrency().'",';  //Pcl-VAT-Value
        $itemrecord = "";
        if(count($parcel_list) > 0){
            foreach($parcel_list as $parcel){
                $parcelDescription = json_decode($parcel->getDescription());
                $parcelCountry = json_decode($parcel->getCommodityCode());
                $parcelQty = json_decode($parcel->getQty());
                $parcelValue = json_decode($parcel->getItemValue());
                $parcelHscode = json_decode($parcel->getHsCode());
                $parcelSku = json_decode($parcel->getItemSku());
                $parcelWeight = json_decode($parcel->getPWeight());
                $count = count($parcelDescription);
                if(count($parcelDescription) > 0){
                    foreach($parcelDescription as $key=>$desc){
                        $itemrecord .= $parcelSku[$key]; //Pcl-Rec-Item-SKU-Code
                        $itemrecord .= "||";
                        $itemrecord .= $desc; //Pcl-Rec-Item-SKU-Description
                        $itemrecord .= "||";
                        $itemrecord .= ($parcelHscode[$key] == '' ? '000000' : $parcelHscode[$key]); //Pcl-Rec-Item-HS-Code
                        $itemrecord .= "||";
                        $itemrecord .= $parcelCountry[$key]; //Pcl-Rec-Item-Country-Of-Manufacture
                        $itemrecord .= "||";
                        $itemrecord .= $parcelValue[$key] * 100 ; //Pcl-Rec-Item-Value
                        $itemrecord .= "||";
                        $itemrecord .= $parcelWeight[$key] * 1000 ; //Pcl-Rec-Item-Weight
                        $itemrecord .= "||";
                        $itemrecord .= $parcelQty[$key]; //Pcl-Rec-Item-Quantity
                        $itemrecord .= "||";
                        $itemrecord .= ""; //Pcl-Rec-CPC-Code
                        if($key < $count-1)
                        $itemrecord .= "++";
                    }

                }
                else
                {
                    $itemrecord .= ""; //Pcl-Rec-Item-SKU-Code
                    $itemrecord .= "||";
                    $itemrecord .= $consignment->getDescription(); //Pcl-Rec-Item-SKU-Description
                    $itemrecord .= "||";
                    $itemrecord .= "000000"; //Pcl-Rec-Item-HS-Code
                    $itemrecord .= "||";
                    $senderCountry = new Country($consignment->getSenderCountryId());
                    $itemrecord .= $senderCountry->getIso(); //Pcl-Rec-Item-Country-Of-Manufacture
                    $itemrecord .= "||";
                    $itemrecord .= $consignment->getValue() * 100 ; //Pcl-Rec-Item-Value
                    $itemrecord .= "||";
                    $itemrecord .= $consignment->getWeight() * 1000 ; //Pcl-Rec-Item-Weight
                    $itemrecord .= "||";
                    $itemrecord .= $consignment->getNumberPieces(); //Pcl-Rec-Item-Quantity
                    $itemrecord .= "||";
                    $itemrecord .= ""; //Pcl-Rec-CPC-Code
                }
            }
        }
        $record .= '"'.$itemrecord.'"';
        $record .= "\r\n"; //Pcl-Rec-Filler-4
        

        return $record;
    }

    

    private function sendBookings($ftpConstants, $run_number) {
        require_once(SETTING_DIR_REMOTE . "includes/3rdparty/Net/SFTP.php");
        if (sizeof($this->record_array) > 0) {
            $path = SETTING_DIR_ASSETS . "data_send/hermes_booking/" . date("Y_m_d") . "/";
            if (!file_exists($path))
                @mkdir($path, 0777, TRUE);

            $file_path = $path . $this->booking_file.".csv";
            chmod($path, 0777);
            $record_count = sizeof($this->record_array);
            // create file
            $file_handle = @fopen($file_path, 'w');
            fwrite($file_handle, $this->getHeaderRecord($record_count, $run_number, $ftpConstants));
            $record = implode('', $this->record_array);
            fwrite($file_handle, "$record");
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
                $remote_file_path = "./In/" . $this->booking_file . ".csv";
                $isUpload = $ssh->put($remote_file_path, $file_path, NET_SFTP_LOCAL_FILE);
                $returnparam = $isUpload;
            } else {
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
        $record = '"HEADER"' . ",";

        $record .= '"' . $constant["HERMES_CLIENT_ID"] .'",';
        $record .= '"' . $constant["HERMES_CLIENT_NAME"] . '",';
        $record .= '"' . $run_number . '",';
        $record .= '"' .  date("dmY", time()) . '",'; // collection set to today!
        $record .= '"' .  date("hm", time()) . '",'; // collection set to today!
        $record .= '"5.0"';
        $record .= "\r\n";
        //
        return $record;
    }

    private function getFooterRecord($record_count, $constant) {
        //$record = "\n";
        $record .= '"TRAILER"'.",";
        $record .= '"'.$constant["HERMES_CLIENT_ID"] . '",';
        $record .= '"' . $record_count . '"';

        return $record;
    }

    public function recycledShipment($consignment) {
        $output["STATUS"] = "SUCCESS";
        return $output;
    }

    public function bagLabel($trackingNumberArray) {
        if(count($trackingNumberArray) == 0){
            $output['STATUS'] = 'ERROR';
            $output['MESSAGE'] = 'Cannot find tracking Number for bagging.';
            return $output;
        }
        
        $consignmentFilter = new ConsignmentFilter();
        $consignmentFilter->addJoin("parcel p", "c.id = p.consignment_id");
        $consignmentFilter->addFilterNew("p.tracking_number in ('".implode("','", $trackingNumberArray)."')");
        $consignmentFilter->addGroupBy("c.routing_code_eur");
        $consignmentList = $consignmentFilter->getListNew("c.id, p.id as parcel_id, p.tracking_number, c.routing_code_eur");
        if(count($consignmentList) > 0){
            $depotCode = "";
            foreach($consignmentList as $consignment){
                if($consignment->getRoutingCodeEur() != $depotCode){
                    $depotCode = $this->getPrimeDepotCode($consignment->getRoutingCodeEur());
                }
                else
                {
                    $depotCode = $consignment->getRoutingCodeEur();
                }
            }
        }
        $output = array();
        if($depotCode != ""){
            $hermesDepotFilter = new HermesDepotMappingFilter();
            $hermesDepotFilter->addFilter(" sack_depot = '".$depotCode."'");
            $hermesDepotList = $hermesDepotFilter->getList();
            if(count($hermesDepotList) > 0){
                $sackCode = $hermesDepotList[0]->getSackDepot();
                $sackDepotName = $hermesDepotList[0]->getSackDepotName();
            }
            else
            {
                $output['STATUS'] = 'ERROR';
                $output['MESSAGE'] = 'Unable to locate Depot Details.';
                return $output;
                
            }
                
        }
        $pdf = new PdfBase(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $this->pdf = $pdf;
        $this->pdf->SetPrintFooter(false);
        $this->pdf->SetFooterMargin(0);
        $this->pdf->SetAutoPageBreak(false, 0);

        $page_size = array (80, 100);

        $this->pdf->AddPage("P",$page_size);

        $resultArray = LicencePlate::getLicencePlateNumber(200);

        if (trim($resultArray['STATUS']) == 'ERROR')
            return $resultArray;
        else {
            $licence_plate = $resultArray["PREFIX"] . sprintf('%07d', $resultArray["RANGE"]) . $resultArray["SUFIX"];
            $checkdigit = $this->getCheckDigit($licence_plate);
            $licence_plate = $licence_plate.$checkdigit;
        }
        //$licence_plate = "6100000193";
        
        $logo = "../images/Hermes_black_logo.jpg";
        $this->pdf->Image($logo, 3, 3, 30);
        $this->pdf->setFont("helvetica", "", 18);
        $this->pdf->Text(50, 1, "Pre-Sort");
        $this->pdf->setFont("helvetica", "B", 15);
        //$this->pdf->Text(30, 15, "HUB - RUGBY");
        $this->pdf->setFont("helvetica", "", 12);
        //$this->pdf->Text(35, 25, "Origin-China");
        $this->pdf->setFont("helvetica", "", 20);
        $this->pdf->Text(20, 20, "Depot - " . $sackCode, false, false, true, 0, 'J');
        $this->pdf->Text(15, 35, $sackDepotName, false, false, true, 0, '', 'J');// (30, 30, $sackDepotName, false);
        $style = array(              
                'border' => false,
                'hpadding' => 'auto',
                'vpadding' => 1,
                'fgcolor' => array(0,0,0),
                'bgcolor' => false, //array(255,255,255),
                'text' => true,
                'font' => 'helvetica',
                'fontsize' => 12,
                'stretchtext' => 1                     
        );

        
        $this->pdf->write1DBarcode($licence_plate, 'I25', 15, 60, '', 20, 0.5, $style, 'Y');
        $this->pdf->setFont("helvetica", "", 12);
        $this->pdf->Text(50, 90, "Gaz Ref: " . $depotCode);
        $fileName = "baglabel_" . date("YmdHis") . ".pdf";
        $this->pdf->Output("../_assets/export_manifest/".$fileName, "F");
        $output['STATUS'] = 'SUCCESS';
        $output['BAG_NUMBER'] = $licence_plate;
        $output['LABEL'] = 'export_manifest/'.$fileName;
        return $output;
        
    }

    public function presortFile($trackingNumber = array()) {
        require_once(SETTING_DIR_REMOTE . "includes/3rdparty/Net/SFTP.php");
        if (count($trackingNumber) > 0) {
            $consignmentFilter = new ConsignmentFilter();
            $consignmentFilter->addJoin(" parcel p", "p.consignment_id = c.id");
            $consignmentFilter->addJoin(" parcel_bagging_mapping pm", "p.id = pm.`parcel_id`");
            $consignmentFilter->addJoin(" bagging b", "b.id = pm.`bag_id`");
            $consignmentFilter->addFilterNew("  AND c.awb in('" . implode("','", $trackingNumber) . "') and c.awb <> '' and c.date_created >= '2020-10-29' and c.send_courier_data = '1'");
            $consignmentFilter->addGroupBy("b.bagnumber, c.routing_code_eur");
            $sortedConsignmentList = $consignmentFilter->getListNew("c.id, c.routing_code_eur, b.bagnumber");
            
            if (count($sortedConsignmentList) > 0) {
                $filename = 20;
                $record = "";   
                $contheadercount = count($sortedConsignmentList);
                $consignmentIdArray = array();
                $this->presortRecords = array();
                foreach ($sortedConsignmentList as $sortcode) {
                    $bagnumber = $sortcode->getBagnumber();
                    $consignmentShipmentDataFilter = new ConsignmentFilter();
                    $consignmentShipmentDataFilter->addJoin(" parcel p", "p.consignment_id = c.id");
                    $consignmentShipmentDataFilter->addJoin(" parcel_bagging_mapping pm", "p.id = pm.`parcel_id`");
                    $consignmentShipmentDataFilter->addJoin(" bagging b", "b.id = pm.`bag_id`");
                    if (!empty($trackingNumber))
                        $consignmentShipmentDataFilter->addFilterNew("     AND c.routing_code_eur = '".$sortcode->getRoutingCodeEur()."' AND  p.tracking_number in ('" . implode("','", $trackingNumber) . "') and p.tracking_number <> '' and b.bagnumber='".$sortcode->getBagNumber()."' ");
                    $consignmentShipmentData = $consignmentShipmentDataFilter->getListNew(" c.id 'consignment_id',c.hawb, c.awb, c.routing_code_eur,p.tracking_number, b.bagnumber ");
                    if (count($consignmentShipmentData) > 0) {
                        
                        $this->presortRecords[] = $this->parentHeaderRecord($sortcode->getRoutingCodeEur(), $bagnumber, count($consignmentShipmentData));
                        foreach ($consignmentShipmentData as $clist) {
                            $consignmentIdArray[] = $clist->getConsignmentId();
                            $this->presortRecords[] = $this->parentDispatchRecord($clist->getAwb(), $bagnumber);
                        }
                    }
                }
                $run_number = CarrierDataFileLog::generateRunNumber("20000", "20000", false);
                $fname = "WAREHOUSE_DESPATCH_" . date("Y-m-d")."_". date("H-i-s").".txt";
                $carrierDataFileLog = new CarrierDataFileLog();
                $carrierDataFileLog->setCarrierId("20000");
                $carrierDataFileLog->setAgentId("20000");
                $carrierDataFileLog->setFileName($fname);
                $carrierDataFileLog->setRunNumber($run_number);
                $carrierDataFileLog->save();
                $record .= $this->presortHeaderRecord($run_number);
                
                foreach ($this->presortRecords as $rec) {
                    $record .= $rec;
                }
                $record .= $this->clientTrailerRecord(count($this->presortRecords), $contheadercount);
                $path = SETTING_DIR_ASSETS . "data_send/hermes_presort_booking/" . date("Y_m_d") . "/";
                if (!file_exists($path))
                    @mkdir($path, 0777, TRUE);
                
                $file_path = $path . $fname;
                $filename++;
                chmod($path, 0777);

                // create file
                $file_handle = @fopen($file_path, 'w');
                fwrite($file_handle, $record);
                // close file
                fclose($file_handle);
                
                $ssh = new Net_SFTP("sftp.hermescloud.co.uk");
                if (!$ssh->login('client.oneworldexpressstrategic', "XLqM8eBB5xQHqqd9")) {
                 mail("mruga@oneworldexpress.com", "HERMES LOGIN FAILED", "HERMES LOGIN FAILED" . $fname); 
                }
                $remotefile="./In/". $fname;
                
                if ($ssh->put($remotefile, $file_path, NET_SFTP_LOCAL_FILE) == true) {
                    if (!empty($consignmentIdArray)) {

                        echo $sql = "UPDATE consignment SET send_courier_data = '2'
                                WHERE id IN ('" . implode("','", $consignmentIdArray) . "') AND id <> '0' 
                                AND  shipment_status not in ('" . Consignment::STATUS_RECYCLED . "','" . Consignment::STATUS_READY_TO_PRINT . "','" . Consignment::STATUS_INVALID . "')";
                        DbAccess3::runQuery($sql);
                        $output["STATUS"] = "SUCCESS";
                        $output["MESSAGE"] = "System has successfully send data.";
                    } else {
                        $output["STATUS"] = "ERROR";
                        $output["MESSAGE"] = "No consignment found to send data to carrier.";
                    }
                }
                
            }
        }

       
    }

    private function presortHeaderRecord($run_number) {
        $record = "HEADER" . ",";

        $record .= "2145" . ",";
        $record .= "One World Strategic" . ",";
        $record .= $run_number . ",";
        $record .= date("dmY", time())  . ","; // collection set to today!
        $record .= date("His") . ","; // collection set to today!
        $record .= "2.0";
        $record .= "\r\n";
        return $record;
    }
    private function parentHeaderRecord($parentSortCode, $bagNumber, $count) {
        $depotcode = $this->getPrimeDepotCode($parentSortCode);
        
        $record = "CONT_HEADER"  . ",";

        $record .= "2145". ","; // Client Id
        $record .= $bagNumber. ","; // bagnumber
        $record .= $depotcode. ","; // Parent Sort Level 1
        $record .= "". ","; //Parent Sort Level 2
        $record .= "". ","; //Parent Sort Level 3
        $record .= "". ","; //Parent Sort Level 4
        $record .= "". ",";//Parent Sort Level 5
        $record .= $count. ","; //Quantity of parcel in parent
        $record .= date("dmY", time()). ","; // Scan Date
        $record .= date("His"). ","; // Scan Time
        $record .= ""; // Trailer Record
       // $record .= $this->fld(10, ""); // Filler


        $record .= "\r\n";
        return $record;
    }
    
    public  function getPrimeDepotCode($depotCode){
        $depotArray = array();
        $depotArray["84"] = "84";
        $depotArray["93"] = "93";
        $depotArray["80"] = "93";
        $depotArray["79"] = "79";
        $depotArray["87"] = "87";
        $depotArray["18"] = "87";
        $depotArray["94"] = "94";
        $depotArray["32"] = "94";
        $depotArray["81"] = "81";
        $depotArray["15"] = "81";
        $depotArray["38"] = "38";
        $depotArray["60"] = "60";
        $depotArray["03"] = "11";
        $depotArray["55"] = "55";
        $depotArray["06"] = "55";
        $depotArray["59"] = "59";
        $depotArray["82"] = "82";
        $depotArray["02"] = "82";
        $depotArray["57"] = "57";
        $depotArray["11"] = "11";
        $depotArray["56"] = "56";
        $depotArray["85"] = "85";
        $depotArray["08"] = "85";
        $depotArray["10"] = "10";
        $depotArray["05"] = "10";
        $depotArray["88"] = "88";
        $depotArray["19"] = "88";
        $depotArray["89"] = "89";
        $depotArray["09"] = "09";
        $depotArray["91"] = "91";
        $depotArray["42"] = "91";
        $depotArray["96"] = "96";
        $depotArray["83"] = "83";
        $depotArray["90"] = "90";
        $depotArray["26"] = "90";
        $depotArray["95"] = "95";
        $depotArray["28"] = "95";
        $depotArray["58"] = "58";
        $depotArray["59"] = "59";
        $depotArray["78"] = "78";
        $depotArray["07"] = "78";
        $depotArray["92"] = "92";
        $depotArray["01"] = "92";
        $depotArray["86"] = "86";
        $depotArray["16"] = "86";
        $depotArray["36"] = "36";
        $depotArray["98"] = "98";
        $depotArray["97"] = "97";
        $depotArray["99"] = "99";
        return $depotArray[$depotCode];
    }

    private function parentDispatchRecord($trackingNumber, $bagNumber) {
        $record = "CONT_DETAIL". ","; 

        $record .= "2145". ",";  // Client Id
        $record .= $bagNumber. ",";  // Parent ID
        $record .= $trackingNumber; // Tracking Number
        //$record .= $this->fld(20, ""); // Filler


        $record .= "\r\n";
        return $record;
    }

    private function clientTrailerRecord($runNumber, $contRecordCount) {
        $record = "TRAILER". ","; 

        $record .= "2145". ",";  // Client Id
        $record .= ($runNumber + 2) . ",";  // Record Count
        $record .=  $contRecordCount. ",";  // Parent Child Header Count
        $record .= $runNumber - $contRecordCount. ",";  // Parent Child Detail Count
        $record .=  "0". ",";  // Despatch Parent Header Count
        $record .=  "0";  // Despatch Parent Header Count

        $record .= "\r\n";
        return $record;
    }

    public function setTrackingParams($serviceId, $agentId)
    {
        $this->trackingServiceId = $serviceId;
        $this->trackingAgentId = $agentId;
    }
}
