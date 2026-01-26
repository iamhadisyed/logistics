<?php

include_classes([
    'tourlineroutine.class',
    'tourlineroutinefilter.class',
]);

class Tourline implements CarrierService {

    private $pdf;
    private $serviceValues = null;
    private $constants = null;
    private $user = null;
    private $country = null;
    private $recordArray = null;
    private $booking_file = null;
    private $portugalRecordArray = null;
    private $SOAP = "";

    public function __construct() {
        
    }

    public function validation(Consignment $consignment, Services $service, Country $country) {
        $returnOutput = array();
        if ($consignment->getServiceType() == "C") {
            if (trim($consignment->getEmail()) == "") {
                $returnOutput[] = "Please enter email address.";
            }
            if (trim($consignment->getSenderEmail()) == "") {
                $returnOutput[] = "Please enter  collection email address.";
            }
            if (trim($consignment->getTelephone()) == "") {
                $returnOutput[] = "Please enter Telephone.";
            }
            if (trim($consignment->getSenderTelephone()) == "") {
                $returnOutput[] = "Please enter collection telephone.";
            }
        }
        return $returnOutput;
    }

    public function remoteareas($consignment, $carrierObject, $sender) {
        
    }

    public function label($consignment, $labelType = 'pdf', $size = '') {

        $output = array();
        $this->serviceValues = new Services($consignment->getServiceId());
        $this->country = new Country($consignment->getCountryId());
        $user = new User($consignment->getUserId());
        $this->userAccount = new CustomerAccount($user->getUserAccountId());
        $parcel_list = $consignment->getParcels();
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
        if ($consignment->getShipmentType() == "C") {
            if (trim(@$this->constants['TOURLINE_COLLECTION_USERNAME']) == '' || trim(@$this->constants['TOURLINE_COLLECTION_PASSWORD']) == '' || trim(@$this->constants['TOURLINE_API_CLIENT_CODE']) == '' || trim(@$this->constants['TOURLINE_API_AGENCY_CODE']) == '') {
                $output['STATUS'] = 'ERROR';
                $output['MESSAGE'] = "Please check service constants. Please contact to itsupport@oneworldexpress.com";
                return $output;
            }
        } else {
            if (trim(@$this->constants['TOURLINE_CLIENT_CODE']) == '' || trim(@$this->constants['TOURLINE_AGENCY_CODE']) == '' || trim(@$this->constants['TOURLINE_CLIENT_COUNTRY']) == '' || trim(@$this->constants['TOURLINE_CLIENT_ADDRESSLINE1']) == '') {
                $output['STATUS'] = 'ERROR';
                $output['MESSAGE'] = "Please check service constants. Please contact to itsupport@oneworldexpress.com";
                return $output;
            }
        }

        if (trim(@$this->constants['INTEGRATION_TYPE']) == 'API' && $consignment->getShipmentType() == "C") {
            return $this->getCollectionLabel($consignment, $this->constants, $parcel_list);
        } else {
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
                        $range = sprintf('%010d', $resultArray["RANGE"]);
                        $pieces = str_pad($parcel_idx + 1, 3, "0", STR_PAD_LEFT);
                        $barcode = $resultArray["PREFIX"] . $range . $resultArray["SUFIX"] . $pieces;
                        $licence_plate = $barcode;
                    }

                    if ($this->country->getIso() == "PT") {
                        $portugalLicencePlate = LicencePlate::getLicencePlateNumber(180);
                        if (trim($portugalLicencePlate['STATUS']) == 'ERROR')
                            return $portugalLicencePlate;
                        else {
                            $trackingNumber = $portugalLicencePlate["RANGE"] . LicencePlate::mod11($portugalLicencePlate["RANGE"]);
                            $PTbarcode = $portugalLicencePlate["PREFIX"] . $trackingNumber . $portugalLicencePlate["SUFIX"];
                            $portugalLicencePlate = $PTbarcode;
                        }
                        $parcel->setDoTrackingNumber($portugalLicencePlate);
                    }
                    $parcel->setTrackingNumber($licence_plate);
                    $parcel->save();
                } else {
                    $licence_plate = $parcel->getTrackingNumber();
                    if ($this->country->getIso() == "PT") {
                        $portugalLicencePlate = $parcel->getDoTrackingNumber();
                    }
                }

                $licence_plate_array[$parcel_idx] = $licence_plate;

                $this->pdf->SetPrintFooter(false);
                $this->pdf->SetFooterMargin(0);
                $this->pdf->SetAutoPageBreak(false, 0);
                $page_size = array(100, 150);
                $this->pdf->AddPage("L", $page_size);
                $response = $this->addWayBill($consignment, $parcel_idx, $licence_plate, $portugalLicencePlate);
                if ($response["STATUS"] == "ERROR") {
                    return $response;
                }
                ++$parcel_idx;
            }



            $this->pdf->IncludeJS("print();");
            $this->pdf->Output("../_assets/pdf/" . date('Y_m_d') . '/' . $consignment->getId() . ".pdf", "F");
            $output['STATUS'] = 'SUCCESS';
            $output['LABEL'] = date('Y_m_d') . '/' . $consignment->getId() . ".pdf";
            $output['TRACKING_NUMBER'] = $licence_plate_array;
            return $output;
        }
    }

    public function tracking($trackingNumber, $trackBy = 'parcel', $EDI = false) {
        $tracking = new Tracking();
        $deliveredArray = array("3");

        if ($EDI == true && !empty($this->trackingServiceId) && !empty($this->trackingAgentId)) {
            include_classes([
                'tourlinetrackingstatus.class'], 'labels');

            $serviceAgentConstantFilter = new ServiceConstantValueFilter();
            $serviceAgentConstantFilter->addFilter("service_id = '" . $this->trackingServiceId . "' AND agent_id = '" . $this->trackingAgentId . "' ");
            $serviceAgentConstant = $serviceAgentConstantFilter->getConstantList("c.constant 'constant_name', scv.constant_value 'constant_value'");

            if (count($serviceAgentConstant) > 0) {
                foreach ($serviceAgentConstant as $serviceAgentConstantData) {
                    $this->constants[$serviceAgentConstantData->getConstantName()] = $serviceAgentConstantData->getConstantValue();
                }
            }
            $ftp_server = $this->constants['TOURLINE_TRACKING_SERVER']; //213.246.110.102
            $ftp_user = $this->constants['TOURLINE_TRACKING_USERNAME']; //tourlinetracking
            $ftp_pass = $this->constants['TOURLINE_TRACKING_PASSWORD']; //hL&u6ZUy}`,KM$Ca

            $ftp_local_path = SETTING_DIR_ASSETS . "tracking_data/TOURLINE/";

            if (!file_exists($ftp_local_path)) {
                @mkdir($ftp_local_path, 0777, TRUE);
            }

            $conn_id = ftp_connect($ftp_server);

            if (!$conn_id) {
                echo "FTP connection failed";
                exit;
            } else {
                $loginRes = ftp_login($conn_id, $ftp_user, $ftp_pass);
                if (!$loginRes) {
                    echo "FTP Login Failed, check your username and password.";
                    exit;
                }

                ftp_pasv($conn_id, true);
                $arrfile = ftp_nlist($conn_id, "/");
                if ($arrfile === false) {
                    echo "Unable List FTP Files.";
                    exit;
                }

                if (sizeof($arrfile) > 0) {
                    foreach ($arrfile as $filename) {
                        if ($filename == '/processed') {
                            continue;
                        }

                        $fp = fopen($ftp_local_path . $filename, 'w');
                        if (ftp_fget($conn_id, $fp, "/" . $filename, FTP_ASCII, FTP_AUTORESUME)) {
                            $handle = fopen($ftp_local_path . $filename, "r");
                            if ($handle) {
                                while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                                    $arrayMerge = $data[0] . ',' . $data[1];
                                    $data = explode(';', $arrayMerge);

                                    //   print_r($data);
                                    $dateTime = '';
                                    $trackingNumber = removeBomUtf8(str_replace('"', '', $data[0])) . '001';
                                    $EventCode = $data[16];
                                    $tdate = explode(' ', $data[18]);
                                    $date1 = explode('/', $tdate[0]);
                                    $dateTime = $date1[0] . '-' . $date1[1] . '-' . $date1[2] . '20' . ' ' . $tdate[1];
                                    $dateTime = date('Y-m-d H:i:s', strtotime($dateTime));

                                    $EventDescription = TourlineTrackingStatus::getDescription($EventCode);
                                    ;
                                    $trackPoint = $data[17];
                                    $ServiceAreaDescription = '';
                                    $spTrackingStatus = TourlineTrackingStatus::getOweStatusCode($EventCode);

                                    ////////////////////// Carrier Received ////////////////////////////////
                                    $trackingDataFilterObj = new TrackingDataFilter();
                                    $trackingDataFilterObj->addFieldFilter('    tracking_number', $trackingNumber);
                                    $trackingDataFilterObj->addFilter("carrier_code not in ('','11')");
                                    $trackingEvents = $trackingDataFilterObj->getList();

                                    if (count($trackingEvents) > 0) {
                                        $carrierReceivedCheck = 0;   // there is already carrier received event                
                                        $trackingDataFilterObj = new TrackingDataFilter();
                                        $trackingDataFilterObj->addFieldFilter('    tracking_number', $trackingNumber);
                                        $trackingDataFilterObj->addFilter("status_code_id = '148'");
                                        $CarrierReceivedObj = $trackingDataFilterObj->getList();

                                        if (count($CarrierReceivedObj) > 0) {
                                            $carrierCodeCarrierReceived = $CarrierReceivedObj[0]->getCarrierCode();
                                            $carrierReceivedStatusCode = $CarrierReceivedObj[0]->getStatusCodeId();
                                        }
                                    } else {
                                        $carrierReceivedCheck = 1; // No carrier received Event
                                    }
                                    ////////////////////// Carrier Received ////////////////////////////////
                                    // Dont enter any other event code if it is against 148 Event Code
                                    if ($carrierCodeCarrierReceived == $EventCode) {
                                        continue;
                                    }

                                    $entityId = 0;
                                    $parcelObj = new ParcelFilter();
                                    $parcelObj->addTrackingNumberFilter($trackingNumber);
                                    $parcelDataArray = $parcelObj->getColumnList('p.id, p.tracking_number, p.consignment_id');
                                    if (count($parcelDataArray) > 0) {
                                        $parcelData = $parcelDataArray[0];
                                        $entityId = $parcelData->getId();
                                    }

                                    if ($entityId >= 0) {
                                        $parcelEntity = new Parcel($entityId);
                                        $finalStatusCode = $parcelEntity->getParcelStatusCode();
                                        ////////////////////// Carrier Received ////////////////////////////////
                                        if ($carrierReceivedCheck == 1) {
                                            $spTrackingStatus = '148';
                                            $carrierReceivedCheck = 0;
                                        } else {
                                            $spTrackingStatus = TourlineTrackingStatus::getOweStatusCode($EventCode);
                                        }
                                        ////////////////////// Carrier Received ////////////////////////////////

                                        $tracking->saveTrackPoint($entityId, $trackBy, $dateTime, $trackingNumber, $spTrackingStatus, $ServiceAreaDescription, $EventCode, $EventDescription, $deliveredArray, $finalStatusCode);
                                        $tracking->saveConsignmentTrackingStatus($trackingNumber, 'TourlineTrackingStatus');
                                    }
                                } //end while                           
                            }
                        }
                        ftp_rename($conn_id, '/' . $filename, '/processed/' . $filename);
                    }
                }
                ftp_close($conn_id);
            }
        }
    }

    public function sendData($tracking_numbers = array()) {

        $output = [];
        $agentServiceArray = [];
        $consignmentData = new ConsignmentFilter();
        $consignmentData->addFilter("     s.carrier_id= '214'", "servicefilter");
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
                     con.region 'country_region', c.service_id, c.weight, c.hawb, c.description, c.company, c.country_id,c.awb,c.date_created,c.value,c.date_booked,
                      c.contact, c.address_line_1, c.address_line_2, c.city, c.postcode, c.telephone, c.other_routing_code, routing_code_eur, c.number_pieces, pc.tracking_number, pc.do_tracking_number ");

                    if (count($consignmentShipmentData) > 0) {
                        $consignmentIdArray = [];
                        foreach ($consignmentShipmentData as $consignmentItemData) {
                            $this->country = new Country($consignmentItemData->getCountry());
                            $consignmentId = $consignmentItemData->getConsignmentId();
                            $consignmentIdArray[] = $consignmentId;
                            $carrierId = $consignmentItemData->getCarrierId();
                            $this->recordArray[] = $this->getShipmentRecord($consignmentItemData, $this->constants[$serviceid]);
                            if ($this->country->getIso() == "PT") {
                                $this->portugalRecordArray[] = $this->getPortugalRecord($consignmentItemData->getTrackingNumber(), $consignmentItemData->getDoTrackingNumber(), $this->constants[$serviceid]);
                            }
                        }
                        $carrierId = $this->serviceValues->getCarrierId();
                        $agentid = trim($agentServiceArray['agent'][$key]);

                        $this->booking_file = $this->constants[$serviceid]['TOURLINE_AGENCY_CODE'] . "_IN01_" . $this->constants[$serviceid]['TOURLINE_CLIENT_CODE'] . "_" . time() . ".txt";



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

    private function getCollectionLabel(Consignment $consignment, $constant, $parcel) {
        $output = array();
        $AGENCY = $this->constants["TOURLINE_API_AGENCY_CODE"];
        $CLICODE = $this->constants["TOURLINE_API_CLIENT_CODE"];

        $wsMethod = "PutRecogida";
        $senderCountry = new Country($consignment->getSenderCountryId());

        $putRecogidaRequest = array(
            'AdvancePayment' => 0,
            'AgencyClientCode' => $AGENCY,
            'ClientCode' => $CLICODE,
            'ClientCollectionValue' => 0,
            'ClientDepartment' => '',
            'ClientDocBarCode' => $consignment->getHawb(),
            'ClientReference' => $consignment->getHawb(),
            'ContactName' => $consignment->getSenderName(),
            'DeliveryDate' => $consignment->getCollectionDate(),
            'DeliveryMaxHour' => $consignment->getCollectionEndTime(),
            'DeliveryMaxHour2nd' => $consignment->getCollectionEndTime(),
            'DeliveryMinHour' => $consignment->getCollectionStartTime(),
            'DeliveryMinHour2nd' => $consignment->getCollectionStartTime(),
            'DestinManageRequired' => false,
            'DocumentsNum' => 0,
            'DuaValue' => 0,
            'FinalManagement' => true,
            'HasControl' => false,
            'Height' => 0,
            'IdentifyDestinRequired' => true,
            'ItemsNum' => $consignment->getNumberPieces(),
            'Lenght' => 0,
            'OriginManageRequired' => true,
            'PackagesNum' => $consignment->getNumberPieces(),
            'PickupCourierComments' => '',
            'PodScanRequired' => false,
            'RecipientAddress' => $consignment->getAddressLine1() . " " . $consignment->getAddressLine2() . " " . $consignment->getAddressLine3(),
            'RecipientCountry' => $this->country->getIso(),
            'RecipientEmail' => $consignment->getEmail(),
            'RecipientEmailNotify' => true,
            'RecipientIdentification' => '',
            'RecipientIdentityVerification' => false,
            'RecipientMobile' => $consignment->getTelephone(),
            'RecipientName' => $consignment->getContact(),
            'RecipientPhone' => $consignment->getTelephone(),
            'RecipientPhone2' => '',
            'RecipientPostalCode' => $consignment->getPostcode(),
            'RecipientSMSNotify' => false,
            'RecipientTown' => $consignment->getCity(),
            'ReferralName' => '',
            'RefundValue' => 0,
            'ReturnRequired' => false,
            'Saturday' => false,
            'SenderAddress' => $consignment->getSenderAddressLine1() . " " . $consignment->getSenderAddressLine2() . " " . $consignment->getSenderAddressLine3(),
            'SenderCountry' => $senderCountry->getIso(),
            'SenderDept' => '',
            'SenderEmail' => $consignment->getSenderEmail(),
            'SenderEmailNotify' => false,
            'SenderMobile' => $consignment->getSenderTelephone(),
            'SenderName' => $consignment->getSenderName(),
            'SenderPhone' => $consignment->getSenderTelephone(),
            'SenderPostalCode' => $consignment->getSenderPostcode(),
            'SenderSMSNotify' => false,
            'SenderTown' => $consignment->getSenderCity(),
            'ShippingComments' => '',
            'ShippingCostsDueValue' => 0,
            'ShippingType' => '19H',
            'ShippingValue' => number_format($consignment->getValue(), 2),
            'VehicleTypeCode' => 2,
            'Weight' => number_format($consignment->getWeight(), 2),
            'Width' => 0
        );
        $retval = $this->soapRequest($wsMethod, $putRecogidaRequest);
        $consignment->setApiData(print_r($putRecogidaRequest, true), print_r($retval, true), $wsMethod);
        if (property_exists($retval, 'ErrorCode')) {
            if (isset($retval->ErrorCode)) {
                $output["STATUS"] = "ERROR";
                $output["MESSAGE"] = "ErrorCode: {$retval->ErrorCode} ; ErrorMessage: {$retval->ErrorMessage}";
                return $output;
            } else {
                $licence_plate_array = array();
                $shipmentCode = $retval->ShipmentRecoCode;
                $trackingNo = $retval->ShipmentCode;
                if ($shipmentCode != '') {
                    $getLabelMethod = "GetEtiquetaImg";
                    $getLabelRequest = array(
                        'AgencyClientCode' => $AGENCY,
                        'ClientCode' => $CLICODE,
                        'ClientReference' => '',
                        'HistoryLevel' => false,
                        'ImageFormat' => 'PDF',
                        'LabelFormat' => 'SINGLE',
                        'ShipmentCode' => $shipmentCode
                    );
                    $getLabelResponse = $this->soapRequest($getLabelMethod, $getLabelRequest);
                    $consignment->setApiData(print_r($getLabelRequest, true), print_r($getLabelResponse, true), $getLabelMethod);
                    if (property_exists($getLabelResponse, 'ErrorCode')) {
                        if ($getLabelResponse->ErrorCode) {
                            $output["STATUS"] = "ERROR";
                            $output["MESSAGE"] = $getLabelResponse->ErrorCode . " - " . $getLabelResponse->ErrorMessage;
                            return $output;
                        } else {
                            $labelString = base64_decode($getLabelResponse->Label->base64Binary[0]);
                            $outputfilename = "../_assets/pdf/" . date('Y_m_d') . '/' . $consignment->getId() . ".pdf";
                            file_put_contents($outputfilename, $getLabelResponse->Label->base64Binary[0]);

                            foreach ($parcel as $p) {
                                $p->setDoTrackingNumber($shipmentCode);
                                $p->setTrackingNumber($trackingNo);
                                $p->save();
                            }

                            $output['STATUS'] = 'SUCCESS';
                            $output['LABEL'] = date('Y_m_d') . '/' . $consignment->getId() . ".pdf";
                            $licence_plate_array[] = $trackingNo;
                            $output['TRACKING_NUMBER'] = $licence_plate_array;
                            return $output;
                        }
                    }
                }
            }
        }
    }

    private function soapRequest($wsMethod, $requestArray, $cancelRequest = false) {
        $this->SOAP = "";
        $USERID = $this->constants["TOURLINE_COLLECTION_USERNAME"]; // test username '007917500';
        $USERPWD = $this->constants["TOURLINE_COLLECTION_PASSWORD"]; // test password 'CAL%741147';
        if($cancelRequest){
            $URLWS = "http://ws.tourlineexpress.com:8220/TourWsAgencias.svc";
            $ENDPOINT = "http://ws.tourlineexpress.com:8220/TourWsAgencias.svc?wsdl";
            $WSCONTRACT = "http://www.tourlineexpress.com/ITourWsAgencias/";
        }
        else
        {
            $URLWS = "http://ws.tourlineexpress.com:8200/Service1.svc"; //test url "http://ws.tourlineexpress.com:8300/Service1.svc";
            $ENDPOINT = "http://ws.tourlineexpress.com:8200/Service1.svc?wsdl";
            $WSCONTRACT = "http://www.tourlineexpress.com/IService1/";
        }
        
        $WSNAMESPACE = "http://www.tourlineexpress.com";
        $WSXSD = "http://www.w3.org/2005/08/addressing";
        try {
            $params = array("soap_version" => SOAP_1_2, "trace" => 1, "exceptions" => 1);
            $this->SOAP = new SoapClient($ENDPOINT, $params);

            $UserId = new SoapHeader($WSNAMESPACE, "UserId", $USERID);
            $Password = new SoapHeader($WSNAMESPACE, "Password", $USERPWD);
            $wsAction = new SoapHeader($WSXSD, 'Action', $WSCONTRACT . $wsMethod);
            $wsTo = new SoapHeader($WSXSD, 'To', $URLWS);
            $this->SOAP->__setSoapHeaders(array($UserId, $Password, $wsAction, $wsTo));

            $retval = $this->SOAP->$wsMethod($requestArray);

            if (is_soap_fault($retval)) {
                trigger_error("SOAP Fault: (faultcode: {$retval->faultcode}, faultstring: {$retval->faultstring})", E_USER_ERROR);
            } else if (property_exists($retval, 'HasError')) {
                return $retval;
            } else {
                trigger_error("ERROR DE WEBSERVICE", E_USER_ERROR);
            }
        } catch (Exception $e) {
            return ("Tourline Exception: {$e->getMessage()}");
        }
    }

    private function addWayBill(Consignment $consignment, $parcel_idx, $licence_plate, $portugalLicencePlate) {

        $output = array();
        $output["STATUS"] = "SUCCESS";
        $pieces = str_pad($parcel_idx + 1, 3, "0", STR_PAD_LEFT);

        $this->pdf->StartTransform();
        $this->pdf->setFont("helvetica", "", 5);

        $this->pdf->Rotate(90, 2, 85);
        $this->pdf->Text(2, 85, "TOURLINE EXPRESS MENSAJERIA S.L.U                B63238455");
        $this->pdf->StopTransform();



        $y = 5;
        $x = 5;
        $w = 35;
        $h = 15;

        $this->pdf->image("../images/ctt_tourline.png", $x, $y, $w, $h, "PNG", '', '', false, 900, '', false, false, 0, '', false, false);

        $style = array(
            'position' => '',
            'align' => 'L',
            'stretch' => false,
            'cellfitalign' => '',
            'border' => false,
            'fgcolor' => array(0, 0, 0),
            'bgcolor' => false, //array(255,255,255),
        );
        $this->pdf->write1DBarcode($consignment->getHawb(), 'C128', '5', '15', '', 7, 0.4, $style, 'N');
        $this->pdf->setFont("helvetica", "", 6);
        $this->pdf->Text(5, 23, "Ref  " . $consignment->getHawb());
        $this->pdf->Text(5, 25, "Cargo / CC: " . $this->constants['TOURLINE_AGENCY_CODE'] . " / " . $this->constants['TOURLINE_CLIENT_CODE']);
        $this->pdf->setFont("helvetica", "B", 9);
        $this->pdf->Text(5, 29, "Remitente:  Tlf:" . $this->constants['TOURLINE_CLIENT_TELEPHONE']);
        $this->pdf->Line(5, 32.5, 65, 32.5);
        $this->pdf->setFont("helvetica", "", 7);
        $shipper_country = $this->constants['TOURLINE_CLIENT_COUNTRY'];
        if ((int) $shipper_country > 0) {
            $shipperCountry = new Country($shipper_country);
            $sCountry = $shipperCountry->getIso();
        } else {
            $sCountry = $shipper_country;
        }
        $this->pdf->Text(5, 33, $this->constants['TOURLINE_CLIENT_COMAPNY']);
        $this->pdf->Text(5, 36, $this->constants['TOURLINE_CLIENT_ADDRESSLINE1']);
        $this->pdf->Text(5, 39, $this->constants['TOURLINE_CLIENT_ADDRESSLINE2'] . ", " . $this->constants['TOURLINE_CLIENT_ADDRESSLINE3']);
        $this->pdf->Text(5, 42, $this->constants['TOURLINE_CLIENT_POSTCODE'] . " " . $this->constants['TOURLINE_CLIENT_CITY'] . " " . $sCountry);
        $this->pdf->setFont("helvetica", "B", 9);
        $this->pdf->Text(5, 46, "Destinatario:   Tlf:" . $consignment->getTelephone());
        $this->pdf->Line(5, 50, 65, 50);
        $this->pdf->setFont("helvetica", "", 8);
        $this->pdf->Text(5, 53, $consignment->getContact());
        $this->pdf->Text(5, 56, $consignment->getAddressLine1());
        $this->pdf->Text(5, 59, $consignment->getAddressLine2() . " " . $consignment->getAddressLine3());
        $this->pdf->Text(5, 65, $consignment->getCity());
        $this->pdf->setFont("helvetica", "B", 8);
        $this->pdf->Text(5, 68, $consignment->getPostcode() . "    " . $consignment->getCity());

        $this->pdf->setFont("helvetica", "B", 8);
        $this->pdf->Text(5, 72, "Observaciones:  ");
        $this->pdf->setFont("helvetica", "", 8);
        $this->pdf->Text(30, 72, "Description " . $consignment->getDescription());
        $this->pdf->Line(5, 85, 145, 85);
        // Create Triangle
        $this->pdf->Line(15, 86, 5, 96);
        $this->pdf->Line(15, 86, 25, 96);
        $this->pdf->Line(5, 96, 25, 96);

        $this->pdf->setFont("helvetica", "", 7);
        $this->pdf->Text(7.5, 92, "Rembolso");
        $this->pdf->setFont("helvetica", "B", 9);
        $euro = $this->pdf->unichr(8364);
        $this->pdf->Text(5, 96, "CO " . $consignment->getValue() . $euro);

        $this->pdf->setFont("helvetica", "", 12);
        $this->pdf->Text(30, 86, "DOM  "); //DDA BACK ID 



        $this->pdf->setFont("helvetica", "B", 11);
        $this->pdf->Text(45, 2, "Para AmanhÃ£");
        $this->pdf->Text(50, 7, "24H");
        $this->pdf->Text(54, 11, $parcel_idx + 1 . "/" . $consignment->getNumberPieces() . " EC");

        if ($this->country->getIso() == "PT") {

            $style['align'] = 'R';
            $this->pdf->write1DBarcode($portugalLicencePlate, 'C39', '75', '2', '70', 14, 0.4, $style, 'N');
            $this->pdf->setFont("helvetica", "", 7);
            $this->pdf->Text(95, 17, "* " . $portugalLicencePlate . " *");
            $this->pdf->Text(75, 20, "OBJETO PERTENECIENTE A");
            $this->pdf->setFont("helvetica", "B", 9);
            $this->pdf->Text(115, 20, $portugalLicencePlate);
        }
        $this->pdf->write1DBarcode($licence_plate, 'C128', '72', '25', '72', 20, 0.4, $style, 'N');
        $first6num = substr($licence_plate, 0, 6);
        $second6num = substr($licence_plate, 6, 6);
        $third10num = substr($licence_plate, 12, 10);
        $this->pdf->setFont("helvetica", "", 7);
        $this->pdf->Text(85, 46, "Exped. " . $first6num . "-" . $second6num . "-" . $third10num . "-" . $pieces);
        $this->pdf->setFont("helvetica", "", 7);
        $this->pdf->Text(70, 52, date("Ymd H:i"));
        $this->pdf->Text(74, 55, "User: Offline");
        $this->pdf->setFont("helvetica", "", 9);
//        $this->pdf->Text(79, 58, "CCC:");
//        $this->pdf->Text(93, 58, $consignment->getValue() . $euro);
//        $this->pdf->Text(81, 61, "PD:");
//        $this->pdf->Text(93, 61, "1,00" . $euro);
        $this->pdf->setFont("helvetica", "B", 9);
        $this->pdf->Text(85, 65, date("d/m/Y"));
//        $this->pdf->setFont("helvetica", "B", 10);
//        $this->pdf->Text(85, 70, "16H - 19H");
        $this->pdf->setFont("helvetica", "", 7);
        $this->pdf->Text(91, 75, "Kg: " . $consignment->getWeight());
        if ($this->country->getIso() == "PT") {
            $pcode = str_replace(" ", "", $consignment->getPostcode());
            $postcode = substr($pcode, 0, 4);
        } else {
            $postcode = str_replace(" ", "", $consignment->getPostcode());
        }
        $tourlineRoutineFilter = new TourlineRoutineFilter();
        $tourlineRoutineFilter->addFieldFilter("postal_code", $postcode);
        $tourlineRoutine = $tourlineRoutineFilter->getList();
        if (count($tourlineRoutine) > 0) {
            $destinationAgency = $tourlineRoutine[0]->getAgencyCode();
            $destinationpostcode = str_pad($postcode, 5, "0", STR_PAD_LEFT);
            $routineBarcode = $destinationAgency . $destinationpostcode . "191";
            $this->pdf->write1DBarcode($routineBarcode, 'I25', '105', '51', '40', 30, 0.4, $style, 'N');
            $this->pdf->setFont("helvetica", "", 6);
            $this->pdf->Text(105, 82, "Direccionamento: " . $destinationAgency . "-" . $destinationpostcode . "-19-1");
            $this->pdf->setFont("helvetica", "", 15);
            $piece = str_pad($parcel_idx + 1, 2, "0", STR_PAD_LEFT);
            $this->pdf->Text(80, 90, $destinationAgency . " " . $consignment->getCity() . " P" . $piece);
        } else {
            $output["STATUS"] = "ERROR";
            $output["MESSAGE"] = "Unable to provide service to at" . $consignment->getPostcode() . ".";
        }
        return $output;
    }

    private function getShipmentRecord(Consignment $consignment, $constants) {

        $this->country = new Country($consignment->getCountryId());
        $postcode = str_replace(" ", "", $consignment->getPostcode());
        $tourlineRoutineFilter = new TourlineRoutineFilter();
        if ($this->country->getIso() == "PT") {
            $pcode = str_replace(" ", "", $consignment->getPostcode());
            $postcode = substr($pcode, 0, 4);
        } else {
            $postcode = str_replace(" ", "", $consignment->getPostcode());
        }
        $tourlineRoutineFilter->addFieldFilter("postal_code", sprintf('%05d', $postcode));
        $tourlineRoutine = $tourlineRoutineFilter->getList();
        if (count($tourlineRoutine) > 0) {
            $agencyCode = sprintf('%06d', $tourlineRoutine[0]->getAgencyCode());
        } else {
            $agencyCode = sprintf('%06d', "0");
        }
        $record = "";
        // $record .= $this->fld(6, "007917"); // Agency Client Code
        // $record .= "\r\n";
        $record .= $this->fld(6, $constants["TOURLINE_AGENCY_CODE"]); // Agency Client Code "007917"
        $record .= $this->fld(10, substr($consignment->getTrackingNumber(), 12, 10)); //Shipping Sub Code
        $record .= $this->fld(6, date("dmy")); //Date Delivery Note
        $record .= $this->fld(6, $agencyCode); //Agency Destination Code
        $record .= $this->fld(4, "24H"); //Service Type
        $record .= $this->fld(30, $constants['TOURLINE_CLIENT_COMAPNY']); //Sender
        $record .= $this->fld(30, $constants['TOURLINE_CLIENT_ADDRESSLINE1']); //sender Address
        $record .= $this->fld(30, $constants['TOURLINE_CLIENT_ADDRESSLINE2'] . " " . $constants['TOURLINE_CLIENT_ADDRESSLINE3']); //sender TOWN
        $record .= $this->fld(30, $consignment->getContact()); //Receipt
        $record .= $this->fld(50, $consignment->getAddressLine1() . " " . $consignment->getAddressLine2() . " " . $consignment->getAddressLine3()); //Receipt
        $record .= $this->fld(3, " "); //Reserved
        $record .= $this->fld(4, " "); //Reserved
        $record .= $this->fld(10, " "); //Reserved
        $record .= $this->fld(12, $consignment->getTelephone()); //Phone
        $record .= $this->fld(50, $consignment->getCity()); //Town
        $postcode = sprintf('%05d', $postcode);
        $record .= $postcode; //POST CODE
        $record .= $this->fld(3, "1"); //package number
        $record .= $this->fld(12, number_format($consignment->getWeight(), 2)); //weight
        $record .= $this->fld(1, ""); //Resrved
        $record .= $this->fld(12, $consignment->getValue()); //refund value
        $record .= $this->fld(30, ""); //Resrved
        $record .= $this->fld(1, ""); //Resrved
        $record .= $this->fld(12, $consignment->getValue()); //market value
        $record .= $this->fld(1, ""); //Resrved
        $record .= $this->fld(12, "0", true); //Resrved
        $record .= $this->fld(100, $consignment->getDescription()); //comment
        $record .= $this->fld(3, "0", true); //Doc
        $record .= $this->fld(3, "0", true); //Package
        $record .= $this->fld(3, "0", true); //Witdh
        $record .= $this->fld(3, "0", true); //Height
        $record .= $this->fld(3, "0", true); //Long
        $record .= $this->fld(5, ""); // TIME
        $record .= $this->fld(12, "0", true);
        $record .= $this->fld(1, "N");
        $record .= $this->fld(1, "N");
        $record .= $this->fld(1, "N");
        $record .= $this->fld(1, "N");
        $record .= $this->fld(1, "N");
        $record .= $this->fld(6, $constants["TOURLINE_AGENCY_CODE"], true); //AGENCY CLIENT CODE
        $record .= $this->fld(12, "0", true);
        $record .= $this->fld(6, $constants["TOURLINE_AGENCY_CODE"], true); //AGENCY CLIENT CODE
        $record .= $this->fld(5, $constants['TOURLINE_CLIENT_CODE']); //CUSTONMER CODE E
        $record .= $this->fld(6, $constants["TOURLINE_AGENCY_CODE"], true); //AGENCY CLIENT CODE
        $record .= $this->fld(12, ""); //RESERVED
        $record .= $this->fld(30, $consignment->getReference()); //REFERENCE
        $record .= $this->fld(1, ""); //RESERVED
        $record .= $this->fld(8, ""); //RESERVED
        $record .= $this->fld(8, ""); //RESERVED
        $record .= $this->fld(30, ""); //RESERVED
        $record .= $this->fld(1, "0"); //PENDENT
        $record .= $this->fld(1, "N"); //WITH CONTROL
        $record .= $this->fld(1, "N"); //DESTINATION ID
        $record .= $this->fld(31, "N"); //DEPARTMENT CODE
        $record .= $this->fld(1, "S"); //EMAIL RECEIVER
        $record .= $this->fld(128, $consignment->getEmail()); //EMAIL 
        $record .= $this->fld(1, "S"); //SMS RECEIVER
        $record .= $this->fld(30, $consignment->getTelephone()); //SMS
        $record .= $this->fld(128, ""); //FROM
        $record .= $this->fld(128, ""); //ASK FORM
        $record .= $this->fld(1, "N"); //SMS SENDER
        $record .= $this->fld(30, ""); //PHONE
        $record .= $this->fld(1, "N"); //EMAIL SENDER
        $record .= $this->fld(128, ""); //PHONE
        $record .= $this->fld(30, ""); //RESERVED
        $record .= $this->fld(128, ""); //RESERVED
        $record .= $this->fld(30, ""); //RESERVED
        $record .= $this->fld(6, ""); //RESERVED
        $record .= $this->fld(128, ""); //RESERVED
        $record .= $this->fld(128, ""); //RESERVED
        $record .= $this->fld(128, ""); //VALUE1
        $record .= $this->fld(128, ""); //VALUE2
        $record .= $this->fld(30, ""); //OPERA
        $record .= $this->fld(30, ""); //
        $record .= $this->fld(1, ""); //
        $record .= $this->fld(1, ""); //
        $record .= $this->fld(5, "10000"); //ORIGIN POSTCODE
        $record .= $this->fld(30, ""); //
        $record .= $this->fld(30, ""); //
        $record .= $this->fld(256, ""); //
        $record .= $this->fld(12, ""); //
        $record .= $this->fld(1, ""); //
        $record .= $this->fld(30, ""); //
        $record .= $this->fld(256, ""); //
        $record .= $this->fld(12, ""); //
        $record .= $this->fld(1, ""); //
        $record .= $this->fld(30, ""); //
        $record .= $this->fld(256, ""); //
        $record .= $this->fld(12, ""); //
        $record .= $this->fld(1, ""); //
        $record .= "\r\n";

        return $record;
    }

    private function getPortugalRecord($trackingNo, $portugalTrackingNo, $constant) {

        $record = "";
        $record .= $this->fld(6, $constant["TOURLINE_AGENCY_CODE"]); // Agency Client Code
        $record .= $this->fld(10, date("Y-m-d")); // Date
        $record .= $this->fld(25, $trackingNo); // Agency Client Code
        $record .= $this->fld(13, $portugalTrackingNo); // PORTUGAL NUMBER
        $record .= "\r\n";
        return $record;
    }

    private function sendBookings($ftpConstants) {

        $isUpload = true;
        if (sizeof($this->recordArray) > 0) {

            $path = SETTING_DIR_ASSETS . "data_send/tourline_booking/" . date("Y_m_d") . "/";
            if (!file_exists($path))
                @mkdir($path, 0777, true);

            $file_path = $path . $this->booking_file;
            chmod($path, 0777);

            // create file
            $file_handle_brt = fopen($file_path, 'w+');
            $header = $this->fld(6, $ftpConstants["TOURLINE_AGENCY_CODE"]); // Agency Client Code
            $header .= "\r\n";

            fwrite($file_handle_brt, $header);
            foreach ($this->recordArray as $record) {
                fwrite($file_handle_brt, $record);
            }
            // close file
            fclose($file_handle_brt);

            if (isset($ftpConstants['TOURLINE_FTP_SITE']) && trim($ftpConstants['TOURLINE_FTP_SITE']) != '') {
                $SETTING_FTP_USER = $ftpConstants['TOURLINE_FTP_USER'];
                $SETTING_FTP_PASSWORD = $ftpConstants['TOURLINE_FTP_PASSWORD'];
                $SETTING_FTP_SITE = $ftpConstants['TOURLINE_FTP_SITE'];


                $ftp_object1 = new FTPfile($SETTING_FTP_USER, $SETTING_FTP_PASSWORD, $SETTING_FTP_SITE);
                $remote_file_path = "./IN/" . $this->brtitaly_VAT_file . ".dat";


                if (!$ftp_object1->put($remote_file_path, $file_path, FTP_BINARY)) {
                    $isUpload = false;
                }
            }
            $this->recordArray = NULL;
        }

        if (sizeof($this->portugalRecordArray) > 0) {

            $path = SETTING_DIR_ASSETS . "data_send/tourline_booking/" . date("Y_m_d") . "/";
            if (!file_exists($path))
                @mkdir($path, 0777, true);
            $filename = $ftpConstants["TOURLINE_AGENCY_CODE"] . time() . ".txt";
            $file_path = $path . $filename;
            chmod($path, 0777);

            // create file
            $file_handle_brt = fopen($file_path, 'w+');
            foreach ($this->portugalRecordArray as $record) {
                fwrite($file_handle_brt, $record);
            }
            // close file
            fclose($file_handle_brt);

            if (isset($ftpConstants['TOURLINE_FTP_SITE']) && trim($ftpConstants['TOURLINE_FTP_SITE']) != '') {
                $SETTING_FTP_USER = $ftpConstants['TOURLINE_FTP_USER'];
                $SETTING_FTP_PASSWORD = $ftpConstants['TOURLINE_FTP_PASSWORD'];
                $SETTING_FTP_SITE = $ftpConstants['TOURLINE_FTP_SITE'];


                $ftp_object1 = new FTPfile($SETTING_FTP_USER, $SETTING_FTP_PASSWORD, $SETTING_FTP_SITE);
                $remote_file_path = "./IN/" . $this->brtitaly_VAT_file . ".dat";


                if (!$ftp_object1->put($remote_file_path, $file_path, FTP_BINARY)) {
                    $isUpload = false;
                }

                $this->recordArray = NULL;
            }
        }

        return $isUpload;
    }

    public function recycledShipment($consignment) {

        $parcel_list = $consignment->getParcels();

        /*
         *  Get Service  constants 
         */
        $serviceAgentConstantFilter = new ServiceConstantValueFilter();
        $serviceAgentConstantFilter->addFilter("service_id = '" . $consignment->getServiceId() . "' AND agent_id = '" . $consignment->getAgentId() . "' ");
        $serviceAgentConstant = $serviceAgentConstantFilter->getConstantList("c.constant 'constant_name', scv.constant_value 'constant_value'");
        if (count($serviceAgentConstant) > 0) {
            foreach ($serviceAgentConstant as $serviceAgentConstantData) {
                $this->constants[$serviceAgentConstantData->getConstantName()] = $serviceAgentConstantData->getConstantValue();
            }
        }
        if (trim(@$this->constants['TOURLINE_API_AGENCY_CODE']) == '' || trim(@$this->constants['TOURLINE_API_CLIENT_CODE']) == '' || trim(@$this->constants['TOURLINE_COLLECTION_USERNAME']) == '' || trim(@$this->constants['TOURLINE_COLLECTION_PASSWORD']) == '') {
            $output['STATUS'] = 'ERROR';
            $output['MESSAGE'] = "Please check service constants. Please contact to itsupport@oneworldexpress.com";
            return $output;
        }

        $cancelCollectionMethod = "ManageShippingDelivery";
        foreach ($parcel_list as $parcel) {

            $getCancelRequest = array(
                'AgencyClientCode' => $this->constants['TOURLINE_API_AGENCY_CODE'],
                'ClientCode' => $this->constants['TOURLINE_API_CLIENT_CODE'],
                'ShippingCode' => $parcel->getDoTrackingNumber(),
                'isCancellation' => true
            );
            $getCancelResponse = $this->soapRequest($cancelCollectionMethod, $getCancelRequest, true);
            $consignment->setApiData(print_r($getCancelRequest, true), print_r($getCancelResponse, true), $cancelCollectionMethod);
            if (property_exists($getCancelResponse, 'ErrorCode')) {
                if (isset($getCancelResponse->ErrorCode)) {
                    $output["STATUS"] = "ERROR";
                    $output["MESSAGE"] = "ErrorCode: {$getCancelResponse->ErrorCode} ; ErrorMessage: {$getCancelResponse->ErrorMessage}";
                } else {
                    $output["STATUS"] = "SUCCESS";
                }
            }
        }
        return $output;
    }

     private function fld($len, $data, $numerical_flag = false) {
        $fld = "";
        // Char fields should be left justified and space filled to the end of the field and in UPPERCASE at all times.
        // Integer fields should be right justified and zero filled to the start of the field.
        if ($numerical_flag) {
            //$fld = mb_substr($data. str_repeat(" ", $len),0,$len, "utf-8");
            $fdata = substr(str_repeat("0", $len) . $data, 0 - $len); // take from right, -ve start pos.
            $fld = iconv( mb_detect_encoding( $fdata ), 'Windows-1252//TRANSLIT', $fdata );
        } else {
            $fdata = mb_substr($data. str_repeat(" ", $len),0,$len, "utf-8");
            $fld = iconv( mb_detect_encoding( $fdata ), 'Windows-1252//TRANSLIT', $fdata );
            //$fld = substr($data . str_repeat(" ", $len), 0, $len);
        }
        return strToUpper($fld);
    }

    public function setTrackingParams($serviceId, $agentId) {
        $this->trackingServiceId = $serviceId;
        $this->trackingAgentId = $agentId;
    }

}
