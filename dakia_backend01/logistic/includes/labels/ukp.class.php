<?php
include_classes([
    'carrierdatafilelog.class',
    'carrierdatafilelogfilter.class' 
    ]);
class UKP implements CarrierService {

    private $pdf;
    private $serviceValues = null;
    private $constants = null;
    private $user = null;
    private $country = null;
    private $booking_file = null;
    private $record_array = null;
    private $trackingServiceId  = null;
    private $trackingAgentId =  null;

    public function __construct() {
        
    }

    public function validation(Consignment $consignment, Services $service, Country $country) {
        $returnOutput = array();
		
		if($consignment->getValue() <= 0)
		{
			$returnOutput[] = "Please enter shipment value.";
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
                $parcel->setTrackingNumber($licence_plate);
                $parcel->save();
            } else {
                $licence_plate = $parcel->getTrackingNumber();
            }
            $licence_plate_array[$parcel_idx] = $licence_plate;

            $this->pdf->SetPrintFooter(false);
            $this->pdf->SetFooterMargin(0);
            $this->pdf->SetAutoPageBreak(false, 0);
            $page_size = array(120, 80);
            $this->pdf->AddPage("L", $page_size);
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
        require_once(BASE_PATH . "includes/3rdparty/Net/SFTP.php");
        
        if($EDI == true && !empty($this->trackingServiceId) && !empty($this->trackingAgentId))
        { 
            include_once(BASE_PATH."includes/labels/ukptrackingstatus.class.php");
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
           /* $ftp_server = $this->constants['ASENDIAUK_TRACKING_SERVER'];
            $ftp_user = $this->constants['ASENDIAUK_TRACKING_USERNAME'];
            $ftp_pass = $this->constants['ASENDIAUK_TRACKING_PASSWORD'];
            $ftp_local_path = SETTING_DIR_ASSETS . "tracking_data/ASENDIA/";
            $conn_id = ftp_connect($ftp_server);
            */
            $ftp_server = "sftp.ukpworldwide.com";	
            $ftp_user = "oneworldexpress"; 
            $ftp_pass = "r2qLnx4EpRvz7xhR";
	
            $ftp_local_path = SETTING_DIR_ASSETS . "tracking_data/UKP/csv_in/";
            $ftpconnection = new Net_SFTP($ftp_server);
            $ftpconnection->login($ftp_user, $ftp_pass);

            $remotefile="/Tracking/";
            $remotefileprocessed="/Processed/";
            $getAllFiles = $ftpconnection->_list($remotefile);
      
            if(count($getAllFiles)>0)
            {
                foreach($getAllFiles as $keyFile=>$valueFile)
                {
                    $filename = $valueFile['filename'];
                    if(in_array($filename,array('.','..') ))
                    {
                        continue;
                    }
                    
                    $remote_file		=	$remotefile.$filename;
                    $remote_file_processed	=	$remotefileprocessed.$filename;
                    $localFileName              =       $ftp_local_path . $filename;
                    $ftpconnection->get($remote_file,$localFileName );
                    $ftpconnection->rename($remote_file,$remote_file_processed);
                    //break;
                }
            }
            
            $path = SETTING_DIR_ASSETS . 'tracking_data/UKP/csv_in/';
            if (!file_exists($path))
                @mkdir($path, 0777, true);
            
            $path = SETTING_DIR_ASSETS . 'tracking_data/UKP/csv_processed/';
            if (!file_exists($path))
                @mkdir($path, 0777, true);
            
            $fileScannedFolder			=	SETTING_DIR_ASSETS . 'tracking_data/UKP/csv_in/';
            $fileScannedFolderProcessed         =	SETTING_DIR_ASSETS . 'tracking_data/UKP/csv_processed/';
            $filesall = $files = preg_grep('/^([^.])/', scandir($fileScannedFolder)); // replace . and .. from array
           
            if(count($filesall)>0)
            {
                foreach($filesall as $keyFileIndex => $valueFileIndex)	
                {
                    $fullFileName	=	$fileScannedFolder.$valueFileIndex;
                    if(file_exists($fullFileName))
                    {
                        $handle = fopen($fullFileName, "r");			
                        if ($handle) 
                        {
                            $fileNameProcessed = $fileScannedFolderProcessed . $valueFileIndex;
                            $firstRow = 0;
                            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) 
                            {
                                if($firstRow == 0)
                                {
                                    $firstRow = 1;
                                    continue;
                                }
                                else
                                {    
                                    $trackingNo = $data[0];
                                    $dateTimeStr = explode(" ", $data[3]);
                                    $dateStr = $dateTimeStr[0];
                                    $time = $dateTimeStr[1];
                                    $date = str_replace('/', '-', $dateStr); 
                                    $DateTime = $date.' '.$time;
                                    $EventCode = $data[1];
                                    $EventDescription = $data[2];
                                    $ServiceAreaDescription = '';
                                    $Signatory = '';
                                    $spTrackingStatus = UkpTrackingStatus::getOweStatusCode($EventCode);
                                
                                    $entityId = 0;
                                    
                                    $parcelObj = new ParcelFilter();
                                    $parcelObj->addTrackingNumberFilter($trackingNo);
                                    $parcelDataArray = $parcelObj->getColumnList('p.id, p.tracking_number, p.consignment_id');
                                    if (count($parcelDataArray) > 0) 
                                    {
                                        $parcelData = $parcelDataArray[0];
                                        $entityId = $parcelData->getId();
                                    }

                                    $trackingDataFilter = new TrackingDataFilter();
                                    $trackingDataFilter->addTrackPointExistFilter($trackingNo, $spTrackingStatus, $trackPoint, $EventCode, $EventDescription);
                                    $trackingDataExistsObj = $trackingDataFilter->getColumnList("t.entity_id, t.entity_type");
                                    
                                    if (count($trackingDataExistsObj) == 0 && $entityId > 0)
                                    {
                                        $trackingData = [
                                            'user_id' => 0,
                                            'entity_id' => $entityId,
                                            'entity_type' => $trackBy,
                                            'tracking_number' => $trackingNo,
                                            'track_point' => $trackPoint,
                                            'date_created' => date('Y-m-d H:i', strtotime($DateTime)),
                                            'ip_address' => getClientIp(),
                                            'status_code_id' => $spTrackingStatus,
                                            'carrier_code' => $EventCode,
                                            'carrier_desc' => $EventDescription,
                                            'signatory' => ""
                                        ];
                                       
                                        $trackingDataObj = new TrackingData($trackingData);
                                        $trackingDataObj->save();
                                       
                                        /// update consignment status
                                        $trackingDataFilter = new TrackingDataFilter();
                                        $trackingDataFilter->addTrackingNumberFilter($trackingNo);
                                        $trackingDataFilter->AddOrderByDate(false);
                                        $trackingDataObj = $trackingDataFilter->getColumnList('entity_id,entity_type,carrier_code,status_code_id,date_created');

                                        if (count($trackingDataObj) > 0)
                                        {
                                            $trackingDataObj = $trackingDataObj[0];
                                            $carrierCode = $trackingDataObj->getCarrierCode();
                                            $oweTrackingStatusCode = $trackingDataObj->getStatusCodeId();
                                            $entityType = $trackingDataObj->getEntityType();
                                            $entityId = $trackingDataObj->getEntityId();
                                            $consignmentStatusCode = UkpTrackingStatus::getConsignmentStatus($oweTrackingStatusCode);
                                            $consignmentId = 0;
                                            if ($entityType == 'parcel') 
                                            {
                                                $parcelObj = new Parcel($entityId);
                                                $dateTime = date("Y-m-d H:i:s");
                                                $parcelObj->setParcelStatusCode($consignmentStatusCode);
                                                $parcelObj->setLastTrackingUpdate(strtotime($dateTime));
                                                $parcelObj->save();
                                                $consignmentId = $parcelObj->getConsignmentId();
                                            } 
                                            else 
                                            {
                                                $consignmentId = $entityId;
                                                $parcelObj = new Parcel();
                                                $parcelObj->bulkUpdate("parcel_status_code='" . $consignmentStatusCode . "', last_tracking_update=NOW()", "consignment_id = '" . $consignmentId . "'");
                                            }
                                            $ConsignmentObj = new Consignment($consignmentId);
                                            $consignmentStatus = isset(Consignment::$database_status_array[$consignmentStatusCode]) ? Consignment::$database_status_array[$consignmentStatusCode] : '';
                                            $ConsignmentObj->setShipmentStatus($consignmentStatusCode);

                                            if($consignmentStatusCode == Consignment::STATUS_DELIVERED){
                                                $ConsignmentObj->setDateDelivered($trackingDataObj->getDateCreated());
                                            }
                                            if ($consignmentStatus != '')
                                                $ConsignmentObj->setConsignmentStatus($consignmentStatus);
                                            $ConsignmentObj->save();
                                        }
                                            //echo "tracking done: ".$trackingNo."<br />";
                                    }
                                    else
                                    {
                                        //echo "No tracking done: ".$trackingNo."<br />";
                                    }                                    
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
        $consignmentData->addFilter("     s.carrier_id= '206'", "servicefilter");
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
                    $consignmentShipmentData = $consignmentShipmentDataFilter->getColumnList(" c.id 'consignment_id', s.carrier_id ,s.code 'service_code', c.state, c.email,
                     con.region 'country_region', c.service_id, c.weight, c.hawb, c.description, c.company, c.country_id,c.awb,c.date_created,c.value,c.number_pieces,
                      c.contact, c.address_line_1, c.address_line_2, c.city, c.postcode, c.telephone, c.other_routing_code, routing_code_eur,pc.tracking_number, c.currency, c.sender_address_line_1,
                      c.sender_address_line_2, c.sender_address_line_3, c.sender_city, c.sender_postcode, c.sender_country_id, c.sender_state, c.sender_telephone, c.sender_email, c.sender_company, c.sender_name");

                    if (count($consignmentShipmentData) > 0) {
                        $consignmentIdArray = [];
                        $run_number = CarrierDataFileLog::generateRunNumber($carrierId, $agentid);
                        $filedate = str_replace('-','', date('Y-m-d Hms'));
                        $this->booking_file = "ONE WORLD EXPRESS INC LTD OneWorld-manifest-" . $filedate . ".csv";

                        $carrierDataFileLog = new CarrierDataFileLog();
                        $carrierDataFileLog->setCarrierId($carrierId);
                        $carrierDataFileLog->setAgentId($agentid);
                        $carrierDataFileLog->setFileName($filedate);
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
                             
                                $sql = "UPDATE consignment SET send_courier_data = 1, booked_file_id = '" . $filedate . "' 
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
        $this->pdf->line(2, 2, 119, 2);
        $this->pdf->line(2, 78, 119, 78);
        $this->pdf->line(2, 2, 2, 78);
        $this->pdf->line(119, 2, 119, 78);

        $this->pdf->setFont("helvetica", "", 13);

        $this->pdf->Text(75, 15, $this->serviceValues->getName());
        
//        $this->pdf->Text(70, 25, "Value");
//        $this->pdf->Text(85, 25, $consignment->getValue());
//        $this->pdf->Text(100, 25, $consignment->getCurrency());



        $style = array(
            'border' => false,
            'hpadding' => 'auto',
            'vpadding' => 'auto',
            'fgcolor' => array(0, 0, 0),
            'bgcolor' => false, //array(255,255,255),
            'text' => true,
            'font' => 'helvetica',
            'fontsize' => 12,
            'stretchtext' => 4
        );

        $this->pdf->write1DBarcode($licence_plate, 'C128', '3', '5', '60', 20, 0.9, $style, 'N');

        $company = "";
        if ($consignment->getCompany() != "")
            $company = $consignment->getCompany();
        if ($consignment->getAddressLine1() != "")
            $address1 = $consignment->getAddressLine1();
        if ($consignment->getAddressLine2() != "")
            $address2 = $consignment->getAddressLine2();
        if ($consignment->getAddressLine3() != "")
            $address3 = $consignment->getAddressLine3();



        $this->pdf->setFont("helvetica", "", 10);
        $this->pdf->Text(5, 35, $company);
        $this->pdf->Text(5, 39, $consignment->getContact());
        $this->pdf->Text(5, 43, $address1);
        //      echo $address1; die;
        $this->pdf->Text(5, 47, $address2);
        $this->pdf->Text(5, 51, $address3);
        $this->pdf->Text(5, 54, $consignment->getCity());
        $this->pdf->Text(5, 58, $consignment->getPostcode());
        //$this->pdf->Text(55, , "Tel: ".$consignment->getTelephone());
        $this->pdf->setFont("helvetica", "B", 11);
        $this->pdf->Text(5, 62, $this->country->getIso());
        return;
    }

    private function getShipmentRecord(Consignment $consignment, $constant) {
        $this->country = new Country($consignment->getCountryId());
        $parcels = $consignment->getParcels();
        $idx = 0;
        $record = "";

        if (count($parcels) > 1) {
            foreach ($parcels as $parcel) {
                if ($idx == 0) {
                    $record .= $this->getConsignmentRecord($consignment);
                    $idx++;
                } else {
                    $record .= $this->getPieceRecord($consignment, $parcel);
                }
            }
        } else {
            $record .= $this->getConsignmentRecord($consignment);
        }
        return $record;
    }

    private function getConsignmentRecord(Consignment $consignment) {
        $record = '';

        if ($consignment->getSenderAddressLine1() != '') {
            $record .= $consignment->getAwb() . ",";
            $record .= $this->removecommas($consignment->getContact()) . ",";    // M version id
            $record .= $this->removecommas($consignment->getCompany()) . ",";         // M shipment reco
            $record .= $this->removecommas($consignment->getAddressLine1()) . ",";    // D awb
            if ($consignment->getAddressLine2() == '-') {
                $record .= '" "' . ",";
            } else
                $record .= $this->removecommas($consignment->getAddressLine2()) . ",";

            $record .= '" "' . ","; // address line 3



            $record .= $this->removecommas($consignment->getCity()) . ",";
            //$record .=  $this->removecommas($consignment->getAddressLine3()) . ","; //3  ConsigneeStateOrProvince to be filled as it will help with the address validation
            $record .= $this->removecommas(@$consignment->getState()) . ","; //3  ConsigneeStateOrProvince to be filled as it will help with the address validation
            $record .= $consignment->getPostcode() . ",";
            $record .= $this->country->getIso() . ","; //1 the ConsigneeCountry and ShipperCountry to be populated with ISO-2 code of the country (US, GB etc)
            $record .= $consignment->getTelephone() . ",";
            $record .= "K" . ",";
            $record .= $consignment->getWeight() . ",";
            $record .= $this->removecommas(str_replace(array("\n", "\t", "\r"), '', $consignment->getDescription())) . ",";
            $record .= $consignment->getAwb() . ",";
            $record .= '" "' . ",";
            $record .= "1" . ",";  // Number Of Pieces always 1
            $record .= "CN" . ",";
            $record .= $consignment->getValue() / $consignment->getNumberPieces() . ",";
            $record .= $consignment->getCurrency() . ",";
            $record .= "N" . ",";  //4  ExpressRelease to be filled with N
            $record .= $consignment->getEmail() . ",";
            $record .= "Y" . ",";  //5 TrackingRequired to be filled with Y
            $record .= $consignment->getHawb() . ",";  // 7 OrderRefNumber to be populated with the customer’s reference number or with the UniqueParcelID
            $record .= "" . ",";  // 6 TransactionID to be empty
            $record .= "UKP" . ",";


            
            $record .= $this->removecommas($consignment->getSenderName()) . ",";
            
            $record .= $this->removecommas($consignment->getSenderCompany()) . ",";
            $record .= $this->removecommas($consignment->getSenderAddressLine1()) . ",";
            $record .= $this->removecommas($consignment->getSenderAddressLine2()) . ",";
            $record .= $this->removecommas($consignment->getSenderCity()) . ",";
            $record .= "" . ",";
            $record .= $this->removecommas($consignment->getSenderPostCode()) . ",";
            $senderCountry = new Country($consignment->getSenderCountryId());
            $record .= $senderCountry->getIso() . ","; //1  
            $record .= $consignment->getSenderTelephone() . ",";
            $record .= $consignment->getSenderEmail() . ",";
            $record .= "" . ",";
            $record .= "\r\n";
        }

        return $record;
    }

    private function getPieceRecord(Consignment $consignment, $parcel) {
        $record = "";

        if ($consignment->getSenderAddressLine1() != '') {
            $record .= $parcel->getTrackingNumber() . ",";
            $record .= $this->removecommas($consignment->getContact()) . ",";    // M version id
            $record .= $this->removecommas($consignment->getCompany()) . ",";         // M shipment reco
            $record .= $this->removecommas($consignment->getAddressLine1()) . ",";    // D awb
            // D destination code => DHL database (country/city/zip => airport)
            if ($consignment->getAddressLine2() == '-') {
                $record .= '" "' . ",";
            } else
                $record .= $this->removecommas($consignment->getAddressLine2()) . ",";
            $record .= '" "' . ","; // address line 3



            $record .= $this->removecommas($consignment->getCity()) . ",";
            //$record .=  $this->removecommas($consignment->getAddressLine3()) . ","; //3  ConsigneeStateOrProvince to be filled as it will help with the address validation
            $record .= $this->removecommas(@$consignment->getAddressLine3()) . ","; //3  ConsigneeStateOrProvince to be filled as it will help with the address validation
            $record .= $consignment->getPostcode() . ",";
            $record .= $this->country->getIso() . ","; //1 the ConsigneeCountry and ShipperCountry to be populated with ISO-2 code of the country (US, GB etc)
            $record .= $consignment->getTelephone() . ",";
            $record .= "K" . ",";
            $record .= $consignment->getWeight() . ",";
            $record .= $this->removecommas($consignment->getDescription()) . ",";
            $record .= $consignment->getAwb() . ",";
            $record .= '" "' . ",";
            $record .= "1" . ",";  // Number Of Pieces always 1
            $record .= "CN" . ",";
            $record .= $consignment->getValue() / $consignment->getNumberPieces() . ",";
            $record .= $consignment->getCurrency() . ",";
            $record .= "N" . ",";  //4  ExpressRelease to be filled with N
            $record .= $consignment->getEmail() . ",";
            $record .= "Y" . ",";  //5 TrackingRequired to be filled with Y
            $record .= $consignment->getHawb() . ",";  // 7 OrderRefNumber to be populated with the customer’s reference number or with the UniqueParcelID
            $record .= "" . ",";  // 6 TransactionID to be empty
            $record .= "UKP" . ",";
            $record .= $this->removecommas($consignment->getSenderName()) . ",";
            $record .= $this->removecommas($consignment->getSenderCompany()) . ",";
            $record .= $this->removecommas($consignment->getSenderAddressLine1()) . ",";
            $record .= $this->removecommas($consignment->getSenderAddressLine2()) . ",";
            $record .= $this->removecommas($consignment->getSenderCity()) . ",";
            $record .= "" . ",";
            $record .= $this->removecommas($consignment->getSenderPostCode()) . ",";
            $senderCountry = new Country($consignment->getSenderCountryId());
            $record .= $senderCountry->getIso() . ","; //1  
            $record .= $consignment->getSenderTelephone() . ",";
            $record .= $consignment->getSenderEmail() . ",";
            $record .= "" . ",";
            $record .= "\r\n";
        }


        return $record;
    }
    
    public function sendBookings($ftpConstants) {
        require_once(SETTING_DIR_REMOTE . "includes/3rdparty/Net/SFTP.php");
        if (sizeof($this->record_array) > 0) {
            
            $path = SETTING_DIR_ASSETS . "data_send/ukp_booking/" . date("Y_m_d") . "/";
            if (!file_exists($path))
                @mkdir($path, 0777, TRUE);

            $file_path = $path . $this->booking_file;
            chmod($path, 0777);

            $record_count = sizeof($this->record_array) + 2;
	
            $file_handle = fopen($file_path, 'w');
            // write file content
            fwrite($file_handle, $this->getHeaderRecord());
            foreach($this->record_array as $record)
            {
                    fwrite($file_handle, $record);
            }
            fclose($file_handle);
		
            if (isset($ftpConstants['UKP_FTP_SITE']) && trim($ftpConstants['UKP_FTP_SITE']) != '') {
                $SETTING_FTP_USER = $ftpConstants['UKP_FTP_USER'];
                $SETTING_FTP_PASSWORD = $ftpConstants['UKP_FTP_PASSWORD'];
                $SETTING_FTP_SITE = $ftpConstants['UKP_FTP_SITE'];


                $ssh = new Net_SFTP($SETTING_FTP_SITE);
                if (!$ssh->login($SETTING_FTP_USER, $SETTING_FTP_PASSWORD)) {
                    mail("itsupport@oneworldexpress.com", "UKP LOGIN FAILED", "UKP LOGIN FAILED" . $this->booking_file);
                    //exit('Login Failed');
                }
                $remotefile="/Prealerts/". $this->booking_file ;
                $ssh->put($remotefile, $file_path, NET_SFTP_LOCAL_FILE);
                $this->booking_file = NULL;
                $this->record_array = NULL;
                return true;
            } else {
                return false;
            }
        }
    }
    
    private function getHeaderRecord()
	{
		$record  = '"UniqueParcelID"' . ",";			// M record header
		$record .= '"ConsigneeName"' . ",";				// M version id
		$record .= '"ConsigneeBusinessName"' . ",";			// M header record id
		$record .= '"ConsigneeStreetAddress1"' . ",";		// Generic Account Number
		$record .= '"ConsigneeStreetAddress2"' . ",";		// Generic Contract Code
		$record .=  '"ConsigneeStreetAddress3"'  . ",";		// Batch Number
		$record .=  '"ConsigneeCity"' . ","  ; // collection set to today!
		$record .=  '"ConsigneeStateOrProvince"'. ",";   // collection set to today!
		$record .=  '"ConsigneePostalCode"'. ",";   // collection set to today!
                $record .=  '"ConsigneeCountry"'  . ",";
		$record .= '"ConsigneeTelephone"'  . ",";
		$record .=  '"WeightCode"'  . ",";  //wire number
		$record .=  '"Weight"'. ",";
                $record .=  '"ItemDescription"'. ",";
                $record .= '"SKU"' . ",";			// M record header
		$record .= '"HarmonizationCode"' . ",";				// M version id
		$record .= '"LineItemQuantity"' . ",";			// M header record id
		$record .= '"CountryOfOrigin"' . ",";		// Generic Account Number
		$record .= '"CustomsValue"' . ",";		// Generic Contract Code
		$record .=  '"CurrencyCode"'  . ",";		// Batch Number
		$record .=  '"ExpressRelease"' . ","  ; // collection set to today!
		$record .=  '"ConsigneeEmail"'. ",";   // collection set to today!
		$record .=  '"TrackingRequired"'. ",";   // collection set to today!
                $record .=  '"OrderRefNumber"'  . ",";
		$record .= '"TransactionID"'  . ",";
		$record .= '"ServiceCode"'  . ",";  //wire number
		$record .=  '"ShipperName"'. ",";
                $record .=  '"ShipperBusinessName"'. ",";
                $record .=  '"ShipperStreetAddress1"'. ",";   // collection set to today!
                $record .=  '"ShipperStreetAddress2"'  . ",";
		$record .= '"ShipperCity"'  . ",";
		$record .= '"ShipperStateOrProvince"'  . ",";  //wire number
		$record .=  '"ShipperPostalCode"'. ",";
                $record .=  '"ShipperCountry"'. ",";
                $record .= '"ShipperTelephone"'  . ",";  //wire number
		$record .=  '"ShipperEmail"'. ",";
                $record .=  '"EORI"';
		

		$record .= "\r\n";
		return $record;
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
