<?php
include_classes([
    'carrierdatafilelog.class',
    'carrierdatafilelogfilter.class' 
    ]);

class CttExpress implements CarrierService {

    private $pdf;
    private $serviceValues = null;
    private $userAccount = null;
    private $country = null;
    private $constants = null;
    private $booking_file = null;
    private $record_array = null;
    private $dom = null;

    public function __construct() {
        
    }

    public function validation(Consignment $consignment, Services $service, Country $country) {
        $carrierId = $service->getCarrierId();

        $carrierObject = new Carrier((int) $carrierId);
        /*
         * REMOTE AREA POSTCODE CHECK
         */
        $remoteAreaCheck = $this->remoteareas($consignment, $carrierObject, $sender);
        switch ($remoteAreaCheck) {
            case 'allowed':
                break;
            case 'not-allowed':
                $returnOutput[] = "Your postcode is a remote area. Please contact to administrator to activate.";
                break;
            case 'blocked':
                $returnOutput[] = "Your postcode is a remote area. Please contact to administrator to activate.";
                break;
        }
        return $returnOutput;
    }

    public function remoteareas($consignment, $carrierObject, $country) {

        $postCode = substr($consignment->getPostcode(), 0, 1);
        $countryId = $consignment->getCountryId();
        $serviceId = $consignment->getServiceId();
        $userId = $consignment->getUserId();
        $postCodeLength = 1;
        $sql = "SELECT 
                    remotearea_check , remoteareas_groups_id
                FROM
                        carrier c INNER JOIN 
                    remoteareas_groups rag ON c.id = rag.carrier_id
                        INNER JOIN
                    remoteareas ra ON rag.id = ra.remoteareas_groups_id "
                . " AND rag.carrier_id = '" . $carrierObject->getId() . "'"
                . " AND ra.country_id= '" . $countryId . "'"
                . "AND ("
                . "     SUBSTRING(LOWER(from_postcode),1," . $postCodeLength . ") = '" . strtolower(DbAccess3::escape($postCode)) . "' "
                . "     OR  SUBSTRING(LOWER(to_postcode),1," . $postCodeLength . ") = '" . strtolower(DbAccess3::escape($postCode)) . "'
                        ) group by ra.remoteareas_groups_id";
        $remoteareas = Remoteareas::getRemoteareasListFromSql($sql);
        if (count($remoteareas) > 0) {
            $remoteareas = $remoteareas[0];
            $remoteAreaServices = $remoteareas->getRemoteareaCheck();
            $remoteAreaGroup = $remoteareas->getRemoteareasGroupsId();

            if (trim($remoteAreaServices) == 'c')
                $tableName = 'remotearea_charges_carrier_user';
            else
                $tableName = 'remotearea_charges_services_user';

            $sql = "SELECT 
                    count(*) 'id'
                FROM
                    " . $tableName . "
                WHERE 
                    user_account_id in ( SELECT user_account_id FROM user WHERE id = '" . $userId . "'  )"
                    . "AND remotearea_group_id = '" . $remoteAreaGroup . "'";

            $remoteareasUserCheck = Remoteareas::getRemoteareasListFromSql($sql);
            if (count($remoteareasUserCheck) > 0) {
                if ($remoteareasUserCheck[0]->getId() > 0) {
                    $consignment->setRemoteCharges('1');
                    $consignment->setConsignmentStatus(Consignment::$database_status_array[Consignment::STATUS_READY_TO_PRINT]);
                    $consignment->setShipmentStatus(Consignment::STATUS_READY_TO_PRINT);
                    return 'allowed';
                }
            } else {
                $consignment->setRemoteCharges('1');
                $this->consignment->setMessage("Your postcode is a remote area. Please contact to administrator to activate remote area postcode.");
                $this->consignment->setStatus(Consignment::STATUS_INVALID);
                return 'blocked';
            }
        }
    }

    public function label($consignment, $labelType = 'pdf', $size = '') {

        $output = array();
        $this->serviceValues = new Services($consignment->getServiceId());
        $this->country = new Country($consignment->getCountryId());
        $this->userAccount = new CustomerAccount($consignment->getUserId());

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
        if (trim(@$this->constants['CTT_SHIPPER_POSTCODE']) == '' || trim(@$this->constants['CTT_SHIPPER_CITY']) == '' || trim(@$this->constants['CTT_SHIPPER_ADDRESSLINE1']) == '') {
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
                    $range = sprintf('%04d', $resultArray["RANGE"]);
                    $checkdigit = LicencePlate::mod11($range);
                    $licence_plate = $resultArray["PREFIX"] . $range . $checkdigit . $resultArray["SUFIX"];
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
            $this->addWayBill($consignment, $licence_plate);

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

    public function tracking($trackingNumber, $trackBy = 'parcel', $EDI = false) 
    {
        include_once(BASE_PATH . "includes/labels/cttexpresstrackingstatus.class.php");
        $response = file_get_contents("http://www.ctt.pt/feapl_2/app/open/cttexpresso/objectSearch/objectSearch.jspx;jsessionid=30tMKI9WtBKTB47YkcNv3g__.si_part6_node3?objects=$trackingNumber");
        $arrayString = explode('<table class="full-width">', $response);
        $arrayString1 = explode('</table>', $arrayString[2]);

        $postable = '<table class="full-width">';
        $tableCon = str_replace('<tr class="group">', '', $arrayString1[0]);
        $tableCon = str_replace("\n", '', $tableCon);
        $tableCon = str_replace("\r", '', $tableCon);
        $tableCon = str_replace("\r", '', $tableCon);
        $tableCon = str_replace('																						', '', $tableCon);

        $tableCon = str_replace("</tr><td>", '<td>', $tableCon);
        $postable .= $tableCon;
        $postable .= '</table>';
        $table_end_part = $postable;

        $doc = new DOMDocument();
        $doc->loadHTML($table_end_part);
        $rows = $doc->getElementsByTagName('tr');

        $courier_desc = '';
        $i = 0;
        $date = "";
        $latestDate = "";
        for ($i = 0; $i < $rows->length; $i++) 
        {
            $cols = $rows->item($i)->getElementsbyTagName("td");
            $signature = '';
            
            if (strpos($cols->item(0)->nodeValue, "Hora") === false) 
            {
                if (strpos($cols->item(0)->nodeValue, ":") !== false) 
                {
                    $date = "";
                    $time = $cols->item(0)->nodeValue;
                    $cols->item(1)->nodeValue;
                    $desc = trim($cols->item(1)->nodeValue);
                    $title = utf8_encode(trim($cols->item(3)->nodeValue));
                    $signature = trim(utf8_encode($cols->item(5)->nodeValue));
                } 
                else 
                {
                    $date = $date = $cols->item(0)->nodeValue;
                    $time = $cols->item(1)->nodeValue;
                    $desc = trim($cols->item(2)->nodeValue);
                    $cols->item(2)->nodeValue;
                    $title = utf8_encode(trim($cols->item(4)->nodeValue));
                    $signature = trim(utf8_encode($cols->item(5)->nodeValue));
                }

                if (trim($date) == "") 
                {
                    $date = date("Y-m-d", strtotime($latestDate));
                    $date = $date . " " . $time;
                    $latestDate = date("Y-m-d G:i:s", strtotime($date));
                }
                if (strpos($date, ",") !== false) 
                {
                    $date = trim(str_replace("Junho", "June", $date));
                    $date = trim(str_replace("Julho", "July", $date));
                    $date = trim(str_replace("Agosto", "August", $date));
                    $date = trim(str_replace("Setembro", "September", $date));
                    $date = trim(str_replace("Outubro", "October", $date));
                    $date = trim(str_replace("Novembro", "November", $date));
                    $date = trim(str_replace("Dezembro", "December", $date));
                    $date = trim(str_replace("Janeiro", "January", $date));
                    $date = trim(str_replace("Fevereiro", "February", $date));
                    $date = trim(str_replace("MarÃ§o", "March", $date));
                    $date = trim(str_replace("Abril", "April", $date));
                    $date = trim(str_replace("Maio", "May", $date));
                    $date = trim(str_replace("Junho", "June", $date));
                    $dateArr = explode(" ", $date);
                    $day = $dateArr[1];
                    $month = date("m", strtotime($dateArr[2]));
                    $year = $dateArr[3];
                    $date = $year . "-" . $month . "-" . $day . " " . $time;
                    $latestDate = date("Y-m-d G:i:s", strtotime($date));
                }

                if ($signature == '') 
                {
                    $courier_desc = $signature;
                }
            }
            $statusCode = $desc;
            $trackPoint = $desc;
            $spTrackingStatus = CttExpressTrackingStatus::getOweStatusCode(trim($statusCode));
            
            $entityId = 0;
            $parcelObj = new ParcelFilter();
            $parcelObj->addTrackingNumberFilter($trackingNumber);
            $parcelDataArray = $parcelObj->getColumnList('p.id, p.tracking_number, p.consignment_id');
            if (count($parcelDataArray) > 0) 
            {
                $parcelData = $parcelDataArray[0];
                $entityId = $parcelData->getId();
            }

            $trackingDataFilter = new TrackingDataFilter();
            $trackingDataFilter->addTrackPointExistFilter($trackingNumber, $spTrackingStatus, $trackPoint, $statusCode, $desc, $latestDate);
            $trackingDataExistsObj = $trackingDataFilter->getColumnList("t.entity_id, t.entity_type");
            
            if (count($trackingDataExistsObj) == 0 && $entityId > 0) 
            {
                if (trim($statusCode) != '' && trim($desc) != '') 
                {
                    $trackingData = [
                        'user_id' => 0,
                        'entity_id' => $entityId,
                        'entity_type' => $trackBy,
                        'tracking_number' => $trackingNumber,
                        'track_point' => $trackPoint,
                        'date_created' => $latestDate,
                        'ip_address' => getClientIp(),
                        'status_code_id' => $spTrackingStatus,
                        'carrier_code' => $statusCode,
                        'carrier_desc' => $desc,
                        'signatory' => ""
                    ];
                } 
            }
                $trackingDataObj = new TrackingData($trackingData);
                $trackingDataObj->save();
        }
        
        /// update consignment status
        $trackingDataFilter = new TrackingDataFilter();
        $trackingDataFilter->addTrackingNumberFilter($trackingNumber);
        $trackingDataFilter->AddOrderByDate(false);
        $trackingDataObj = $trackingDataFilter->getColumnList('entity_id,entity_type,carrier_code,status_code_id,date_created');

        if (count($trackingDataObj) > 0) 
        {
            $trackingDataObj = $trackingDataObj[0];
            $carrierCode = $trackingDataObj->getCarrierCode();
            $oweTrackingStatusCode = $trackingDataObj->getStatusCodeId();
            $entityType = $trackingDataObj->getEntityType();
            $entityId = $trackingDataObj->getEntityId();
            $consignmentStatusCode = CttExpressTrackingStatus::getConsignmentStatus($oweTrackingStatusCode);

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

            if ($consignmentStatusCode == Consignment::STATUS_DELIVERED) 
            {
                $ConsignmentObj->setDateDelivered($trackingDataObj->getDateCreated());
            }
            
            if ($consignmentStatus != '')
            {
                $ConsignmentObj->setConsignmentStatus($consignmentStatus);
            }
            $ConsignmentObj->save();
        }            
    }

    public function sendData($tracking_numbers = array()) {
        $output = [];
        $agentServiceArray = [];
        $consignmentData = new ConsignmentFilter();
        $consignmentData->addFilter("     s.carrier_id= '154'", "servicefilter");
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
                        $this->dom = new DOMDocument('1.0', 'utf-8');
                        $consignmentIdArray = [];
                        $run_number = CarrierDataFileLog::generateRunNumber($carrierId, $agentid);
                        $this->booking_file = "5xj" . sprintf('%04d', $run_number);

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

    public function manifest($consignments) {        
        $cttManifest = new cttexpressmanifest();
        $returnPdf = $cttManifest->AddConsignment($consignments);
        return $returnPdf;
        
//        $output = [];
//        if (is_array($trackingNumber)) {
//            $consignmentData = new ConsignmentFilter();
//            $consignmentData->addFilter("AND pc.tracking_number in ('" . implode("','", $trackingNumber) . "') and pc.tracking_number <> ''", "parcelJoinFilter");
//             $consignmentData->addGroupBy(" c.service_id, c.agent_id ");
//            $consignmentServiceAgent = $consignmentData->getColumnList("c.service_id, c.agent_id");
//            if (count($consignmentServiceAgent) > 0) {
//            foreach ($consignmentServiceAgent as $agentData) {
//                $agentServiceArray['services'][] = $agentData->getServiceId();
//                $agentServiceArray['agent'][] = $agentData->getAgentId();
//            }
//            } else {
//                $output["STATUS"] = "ERROR";
//                $output["MESSAGE"] = "Please check we did not find any agent and services for data to send.";
//                return $output;
//            }
//            
//            if (!empty($agentServiceArray['services'])) {
//
//                foreach ($agentServiceArray['services'] as $key => $serviceid) {
//
//                    $serviceid = trim($serviceid);
//                    $agentid = trim($agentServiceArray['agent'][$key]);
//
//                    $this->serviceValues = new Services($serviceid);
//
//                    // GET CONSTANTS AS PER SERVICE AND AGENT
//                    $serviceAgentConstantFilter = new ServiceConstantValueFilter();
//                    $serviceAgentConstantFilter->addFilter("service_id = '" . $serviceid . "' AND agent_id = '" . $agentid . "' ");
//                    $serviceAgentConstant = $serviceAgentConstantFilter->getConstantList("c.constant 'constant_name', scv.constant_value 'constant_value'");
//                    if (count($serviceAgentConstant) > 0) {
//                        foreach ($serviceAgentConstant as $serviceAgentConstantData) {
//                            $this->constants[$serviceid][$serviceAgentConstantData->getConstantName()] = $serviceAgentConstantData->getConstantValue();
//                        }
//                    }
//                  
//                    if (!empty($this->constants[$serviceid])) {
//                    // GET ALL THE CONSIGNMENT WITH THE PARCEL FOR PREPARE  FILE
//                    $consignmentShipmentDataFilter = new ConsignmentFilter();
//                    $consignmentShipmentDataFilter->addFilter("     c.service_id = '" . $serviceid . "' AND c.agent_id = '" . $agentid . "' ", "consignmentfilter");
//                    $consignmentShipmentDataFilter->addFilter("     AND pc.tracking_number in ('" . implode("','", $trackingNumber) . "') and pc.tracking_number <> ''", "parcelJoinFilter");
//                        $consignmentShipmentData = $consignmentShipmentDataFilter->getColumnList(" c.id 'consignment_id', s.carrier_id ,s.code 'service_code',
//                            con.region 'country_region', c.service_id, c.weight, c.hawb, c.description, c.company, c.country_id,c.awb,c.date_created,c.value,c.number_pieces,
//                            c.contact, c.address_line_1, c.address_line_2, c.city, c.postcode, c.telephone, c.other_routing_code, routing_code_eur,pc.tracking_number, c.currency ");
//
//                        if (count($consignmentShipmentData) > 0) {
//                            $pdf = new PdfBase(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
//                            $this->pdf = $pdf;
//                            $this->pdf->SetPrintFooter(false);
//                            $this->pdf->SetFooterMargin(0);
//                            $this->pdf->SetAutoPageBreak(false, 0);
//                            $page_size = array(210, 300);
//                            $this->pdf->AddPage("P", $page_size);
//                            $this->addManifest($consignmentShipmentData);
//                            $new_page_flag = true;
//                            $filename = time()."manifestreport.pdf";
//                            echo $fileNameNew	=	"../_assets/". $filename;
//                            $this->pdf->Output($fileNameNew, 'F');
//                            return $fileNameNew;
//                        }
//                    }
//                }
//            }
//        }
    }

    private function addManifest($consignment_list) {
       
        $shipment_count = count($consignment_list);
        $first_awb = $consignment_list[0]->getAwb();
        $last_awb = $consignment_list[$shipment_count - 1]->getAwb();
        $this->pdf->SetFont('Times', '', 9);

        $this->pdf->Image('../images/ctt_logo.JPG', 3, 10, 53, 20);
        $this->pdf->SetFont('Times', '', 6);
        $this->pdf->SetTextColor(0, 0, 0);

        
        $this->pdf->Text(57, 6, $this->constants["CTT_SHIPPER_COMPANY"]);
        $this->pdf->Text(57, 9, "SERVIÇOS POSTAIS E DE LOGÍSTICA, S.A.");
        $this->pdf->Text(57, 12, "LICENCE No. 4940/2000");
        $this->pdf->Text(57, 15, "Tax ID No.: 504 520 296");
        $this->pdf->Text(57, 18, "SHARE CAPITAL: 5,000,000 EURO");
        $this->pdf->Text(57, 21, $this->constants["CTT_SHIPPER_ADDRESSLINE1"]);
        $this->pdf->Text(57, 24, $this->constants["CTT_SHIPPER_ADDRESSLINE2"]);
        $this->pdf->Text(57, 27, $this->constants["CTT_SHIPPER_POSTCODE"] . " " . $this->constants["CTT_SHIPPER_CITY"]);
        $this->pdf->Text(57, 30, "www.cttexpresso.pt");

        $this->pdf->Image('../images/ctt_quality.png', 95, 12, 40);
        $this->pdf->SetFont('Times', 'I', 12);
        $this->pdf->Text(140, 12, "Certificate of Acceptance");
        $this->pdf->Text(160, 20, "48");
        $this->pdf->Image('../images/ctt_header.png', 3, 30, 200, 20);

        $this->pdf->Line(3, 42, 203, 42);
        $this->pdf->Line(105, 42, 105, 90);
        $this->pdf->Line(3, 70, 203, 70);
        $this->pdf->Line(3, 90, 203, 90);
        $this->pdf->Line(52, 70, 52, 90);
        $this->pdf->Line(157, 70, 157, 90);
        $this->pdf->Line(3, 96, 203, 96);


        $this->pdf->SetFont('Times', '', 9);
        $this->pdf->Text(3, 44, "Identificação do Cliente");
        $this->pdf->SetFont('Times', '', 11);
        $this->pdf->Text(5, 47, $this->constants["CTT_SHIPPER_COMPANY"]);
        $this->pdf->Text(5, 51, $this->constants["CTT_SHIPPER_ADDRESSLINE1"] . $this->constants["CTT_SHIPPER_ADDRESSLINE2"]);
        $this->pdf->Text(5, 60, $this->constants["CTT_SHIPPER_POSTCODE"] . " " . $this->constants["CTT_SHIPPER_CITY"]);
        $this->pdf->Text(5, 64, "PORTUGAL");

        $this->pdf->SetFont('Times', '', 9);
        $this->pdf->MultiCell(100, 1, "Sempre que o cliente solicitar prova da aceitação, este certificado será
										preenchido em duplicado.
										O original destina-se ao Aceitante.
									    O duplicado constituirá a prova da aceitação. ", 0, 'C', 0, 1, 105, 47);

        $this->pdf->SetFont('Times', '', 10);
        $this->pdf->Text(5, 71, "Cliente No." . $this->constants["CTT_CUSTOMER_NO"] ); // 11706560
        $this->pdf->write1DBarcode($this->constants["CTT_CUSTOMER_NO"], 'C39', 20, 76, '30', 10, 1);
        $this->pdf->SetFont('Times', '', 9);
        $this->pdf->Text(22, 86, "*1 1 7 0 6 5 6 0*");
        $this->pdf->SetFont('Arial', 'B', 9);


        $this->pdf->SetFont('Times', '', 10);
        $this->pdf->Text(55, 71, "Contrato No. ". $this->constants["CTT_CUSTOMER_NO"] );//300234701
        $this->pdf->write1DBarcode($this->constants["CTT_CUSTOMER_NO"] , 'C39', 65, 76, '30', 10, 1);
        $this->pdf->SetFont('Times', '', 9);
        $this->pdf->Text(67, 86, "*3 0 0 2 3 4 7 0 1 *");
        $this->pdf->SetFont('Arial', 'B', 9);

        $this->pdf->SetFont('Times', '', 8);
        $this->pdf->Text(105, 70, "Primeiro Objecto");
        $this->pdf->Text(105, 72.5, "*" . $first_awb . "*");
        $this->pdf->write1DBarcode($first_awb, 'C39', 110, 76, '40', 10, 2);
        $this->pdf->SetFont('Times', '', 7);
        $this->pdf->Text(118, 86, "*" . $first_awb . "*");
        $this->pdf->SetFont('Arial', 'B', 9);

        $this->pdf->SetFont('Times', '', 8);
        $this->pdf->Text(157, 70, "Ultimo Objecto");
        $this->pdf->Text(157, 72.5, "" . $last_awb . "");
        $this->pdf->write1DBarcode($last_awb, 'C39', 160, 76, '40', 10, 2);
        $this->pdf->SetFont('Times', '', 7);
        $this->pdf->Text(167, 86, "*" . $last_awb . "*");
        $this->pdf->SetFont('Arial', 'B', 11);

        $this->pdf->Text(5, 91, "Expedição Nº. 1");
        $this->pdf->Text(35, 91, "Date: " . date("d/m/Y"));

        $this->pdf->Text(10, 98, "Nº do Objecto");
        $this->pdf->Text(43, 98, "Referência");
        $this->pdf->Text(64, 98, "Nome do Destinatário");
        $this->pdf->Text(105, 98, "Qtd.");
        $this->pdf->Text(123, 98, "País");
        $this->pdf->Text(140, 96, "Serv.");
        $this->pdf->Text(140, 100, "Esp");
        $this->pdf->Text(150, 96, "Importância");
        $this->pdf->Text(150, 100, " a Cobrar");
        $this->pdf->Text(172, 96, "Peso");
        $this->pdf->Text(172, 100, "(Kg)");
        $this->pdf->Text(184, 98, "Observ.");

        $this->pdf->Line(43, 96, 43, 107);
        $this->pdf->Line(63, 96, 63, 107);
        $this->pdf->Line(105, 96, 105, 107);
        $this->pdf->Line(115, 96, 115, 107);
        $this->pdf->Line(140, 96, 140, 107);
        $this->pdf->Line(150, 96, 150, 107);
        $this->pdf->Line(172, 96, 172, 107);
        $this->pdf->Line(183, 96, 183, 107);
        $this->pdf->Line(3, 107, 203, 107);

        $this->pdf->SetFont('Arial', '', 9);
        $y = 108;
        $total_weight = 0;
        $total_pieces = 0;
        $count = 0;
        foreach ($consignment_list as $consignment) {
            $count++;

            if ($count == 9) {
                $this->pdf->AddPage();
                $count = 0;
                $y = 10;
            }

            $this->pdf->write1DBarcode($consignment->getAwb(), 'C39', 1, $y, '40', 10, 2);
            $this->pdf->Text(5, $y + 10, "*" . $consignment->getAwb() . "*");
            $this->pdf->Text(43, $y, "Ref 1");
            $city = str_pad($consignment->getCity(), 20);

            $this->pdf->SetFont('Arial', '', 8);
            $this->pdf->MultiCell(40, 9, strtolower($consignment->getContact() . "\r\n" . $consignment->getAddressLine1() . "\r\n" . $consignment->getPostcode() . $city), 0, 'L', 0, 1, 64, $y, true, 1);

            $this->pdf->SetFont('Arial', '', 9);
            $this->pdf->Text(105, $y, $consignment->getNumberPieces());
            $this->pdf->Text(116, $y, $consignment->getCountry());
            $this->pdf->Text(150, $y, '');
            $this->pdf->Text(172, $y, $consignment->getWeight());


            $y1 = $y;
            $y = $y + 18;
            $this->pdf->Line(43, $y1, 43, $y);
            $this->pdf->Line(63, $y1, 63, $y);
            $this->pdf->Line(105, $y1, 105, $y);
            $this->pdf->Line(115, $y1, 115, $y);
            $this->pdf->Line(140, $y1, 140, $y);
            $this->pdf->Line(150, $y1, 150, $y);
            $this->pdf->Line(150, $y1 + 8, 172, $y1 + 8);
            $this->pdf->Line(172, $y1, 172, $y);
            $this->pdf->Line(183, $y1, 183, $y);

            $this->pdf->Line(3, $y - 1, 203, $y - 1);
            $total_weight += $consignment->getWeight();
            $total_pieces += $consignment->getNumberPieces();
        }

        $this->pdf->Image('../images/ctt_footer.png', 3, 250, 205);
        $this->pdf->Text(40, 251, "0");
        $this->pdf->Text(55, 257, $total_pieces);
        $this->pdf->Text(55, 261, $total_pieces);
        $this->pdf->Text(172, 257, number_format($total_weight, 2) . " Kg");
        $this->pdf->SetFont('Arial', '', 8);
        $this->pdf->Text(5, 294, date("d-m-Y H:i:s"));
    }

    public function preAdvice($consignment) {
        
    }

    private function addWayBill(Consignment $consignment, $licence_plate) {

        $this->pdf->write1DBarcode($licence_plate, 'C39', 28, 2, '70', 25, 1);

        $this->pdf->Text(13.5, 28, $licence_plate);
        $image = realpath("../images/ctt_logo1.png");
        $this->pdf->image($image, 1, 1, 26, 25);

        $this->pdf->setFont("helvetica", "B", 11);
        $this->pdf->Text(3, 35, "Cliente:");
        $this->pdf->Text(50, 35, "Contrato:");
        $this->pdf->Text(3, 39, "Ref:");

        $this->pdf->setFont("helvetica", "", 11);
        $this->pdf->Text(20, 35, "11706560");
        $this->pdf->Text(70, 35, "300234701");
        $this->pdf->Text(15, 39, "ref 1");


        $this->pdf->line(3, 45, 98, 45);
        $this->pdf->line(3, 80, 98, 80);
        $this->pdf->line(3, 45, 3, 149);
        $this->pdf->line(3, 119, 98, 119);
        $this->pdf->line(98, 45, 98, 149);
        $this->pdf->line(3, 134, 98, 134);
        $this->pdf->line(3, 149, 98, 149);

        $this->pdf->setFont("helvetica", "B", 13);
        $this->pdf->Text(3, 46, "Expedidor:");
        $this->pdf->line(4, 51, 28, 51);
        $this->pdf->Text(3, 82, "Destinatario:");
        $this->pdf->line(4, 87, 32, 87);


        $this->pdf->setFont("helvetica", "B", 15);
        $this->pdf->Text(65, 27, "1/1");

        $this->pdf->setFont("helvetica", "B", 27);
        $this->pdf->Text(79, 25.5, "48");

        $this->pdf->setFont("helvetica", "B", 11);
        $this->pdf->Text(3, 56, $this->constants["CTT_SHIPPER_COMPANY"]);
        $this->pdf->Text(3, 60, $this->constants["CTT_SHIPPER_CONTACT"]);
        $this->pdf->Text(3, 64, $this->constants["CTT_SHIPPER_ADDRESSLINE1"]);
        $this->pdf->Text(3, 68, $this->constants["CTT_SHIPPER_ADDRESSLINE2"]);
        $this->pdf->Text(3, 72, $this->constants["CTT_SHIPPER_POSTCODE"] . " " . $this->constants["CTT_SHIPPER_CITY"]);

        $contact = $consignment->getContact();
        if ($contact == "" || $contact == "-")
            $contact = $consignment->getCompany();
        $this->pdf->Text(3, 90, $contact);
        $this->pdf->MultiCell(90, 10, $consignment->getAddressLine1() . " " . $consignment->getAddressLine2(), 0, 'L', 0, 0, 3, 94);
        $this->pdf->Text(3, 102, $consignment->getCity());
        $this->pdf->Text(3, 106, "Tlf:: " . $consignment->getTelephone());
        $this->pdf->Text(3, 110, "Obs:");
        $this->pdf->Text(3, 114, $consignment->getPostcode() . " " . $consignment->getCity());

        $this->pdf->setFont("helvetica", "B", 10);
        $this->pdf->Text(35, 120, "Peso da Remessa");
        $this->pdf->setFont("helvetica", "B", 14);
        $this->pdf->Text(35, 126, $consignment->getWeight() . " KG");
        $ref = $consignment->getNotes();

        $fontname = $this->pdf->addTTFfont('../assets/fonts/simhei.ttf', 'TrueTypeUnicode', '', 32);
        $this->pdf->SetFont($fontname, '', 8);
        if (strlen($ref) > 30) {
            $wrapref = chunk_split($ref, 30) . "\n";

            $reference = explode("\n", $wrapref);
            $y = 135;
            foreach ($reference as $refe) {
                $this->pdf->Text(5, $y, $refe);
                $y += 3;
            }
        } else {
            $this->pdf->Text(5, 135, $ref);
        }

        if (trim($this->userAccount->getLogo()) != '')
            $this->pdf->Image("../images/userlogo/" . $this->userAccount->getLogo(), 55, 135, 40);
        else
            $this->pdf->Image("../images/logo.jpg", 55, 135, 40);

        $this->pdf->setFont("freesans", "L", 13);
    }

    private function getShipmentRecord(Consignment $consignment, $constant) {
        $this->country = new Country($consignment->getCountryId());

        $EMSdom = new DOMDocument('1.0', 'utf-8');

        $xml_EMS = $EMSdom->createElement("EMS");
        $EMSdom->appendChild($xml_EMS);

        $xml_GIN = $EMSdom->createElement("GIN", $consignment->getAwb()); //Number of transport/shipment bill
        $xml_EMS->appendChild($xml_GIN);

        $xml_PIA = $EMSdom->createElement("PIA", "ENCF008.01"); //Subproduct code
        $xml_EMS->appendChild($xml_PIA);

        $xml_QTY = $EMSdom->createElement("QTY", $consignment->getNumberPieces()); //Quantity of items in the shipment
        $xml_EMS->appendChild($xml_QTY);

        $xml_MEA = $EMSdom->createElement("MEA");
        $xml_EMS->appendChild($xml_MEA);

        $xml_WTR = $EMSdom->createElement("WTR", ($consignment->getWeight() * 1000)); //Actual weight in Grams
        $domAttribute_WTR = $EMSdom->createAttribute('UNIT');
        // Value for the created attribute
        $domAttribute_WTR->value = "GRM";
        $xml_WTR->appendChild($domAttribute_WTR);
        $xml_MEA->appendChild($xml_WTR);

        $xml_ZTX = $EMSdom->createElement("ZTX"); //Taxation zone
        $xml_EMS->appendChild($xml_ZTX);

        $EMS_NAD = $EMSdom->createElement("NAD");
        $xml_EMS->appendChild($EMS_NAD);

        $contact = $consignment->getCompany();
        if ($contact == "")
            $contact = $consignment->getContact();
        $EMS_NME = $EMSdom->createElement("NME", $contact); //Addressee�s name
        $EMS_NAD->appendChild($EMS_NME);

        $EMS_ADR = $EMSdom->createElement("ADR", $consignment->getAddressLine1() . " " . $consignment->getAddressLine2()); //Adressee�s address
        $EMS_NAD->appendChild($EMS_ADR);

        $EMS_CPL = $EMSdom->createElement("CPL");
        $EMS_NAD->appendChild($EMS_CPL);

        $EMS_PTC = $EMSdom->createElement("PTC", $consignment->getPostcode()); //Postcode
        $EMS_CPL->appendChild($EMS_PTC);

        $EMS_CTY = $EMSdom->createElement("CTY", $consignment->getCity()); //Locality
        $EMS_CPL->appendChild($EMS_CTY);

        $EMS_CTR = $EMSdom->createElement("CTR", $this->country->getIso()); //Country ISO
        $EMS_NAD->appendChild($EMS_CTR);

        $EMS_TEL = $EMSdom->createElement("TEL", $consignment->getTelephone()); //Telephone
        $EMS_NAD->appendChild($EMS_TEL);

        $EMS_RFF = $EMSdom->createElement("RFF", $consignment->getReference()); //Customer reference
        $EMS_NAD->appendChild($EMS_RFF);

        $EMS_TLM = $EMSdom->createElement("TLM"); //Moile Number
        $EMS_NAD->appendChild($EMS_TLM);

        $EMS_EMAIL = $EMSdom->createElement("EMAIL"); //Email
        $EMS_NAD->appendChild($EMS_EMAIL);

        $EMS_IIO = $EMSdom->createElement("IIO");
        $xml_EMS->appendChild($EMS_IIO);

        $EMS_OID = $EMSdom->createElement("OID", $consignment->getAwb()); //Item number
        $EMS_IIO->appendChild($EMS_OID);

        $EMS_CDAT = $EMSdom->createElement("CDAT");
        $xml_EMS->appendChild($EMS_CDAT);

        $EMS_OBSC = $EMSdom->createElement("OBSC");
        $xml_EMS->appendChild($EMS_OBSC);

        $EMS_INRM = $EMSdom->createElement("INRM", "N");
        $xml_EMS->appendChild($EMS_INRM);

        $EMS_LOCAV = $EMSdom->createElement("LOCAV");
        $xml_EMS->appendChild($EMS_LOCAV);

        return $EMSdom->saveXML();
    }

    public function getXML($constants, $runnumber) {
        //$this->dom = new DOMDocument('1.0', 'utf-8');
        $this->dom->formatOutput = true;
        //$element = $this->dom->createElementNS('http://www.w3.org/2001/XMLSchema-instance', 'ACTPRDX', '');
        //$this->dom->appendChild($element);
        // root manifest
        $element = $this->dom->appendChild($this->dom->createElement('ACTPRDX'));

        // identifier	

        $element->appendChild($this->dom->createAttribute('xmlns:xsi'))->appendChild($this->dom->createTextNode("http://www.w3.org/2001/XMLSchema-instance"));



        $element->appendChild($this->dom->createAttribute('xmlns:xsd'))->appendChild($this->dom->createTextNode("http://www.w3.org/2001/XMLSchema"));


        $xml_UNB = $this->dom->createElement("UNB");
        $element->appendChild($xml_UNB);
        $xml_SNDID = $this->dom->createElement("SNDID", $constants["CTT_SENDER_ID"]); //Sender identification "1BP206"
        $xml_UNB->appendChild($xml_SNDID);
        $xml_RCVID = $this->dom->createElement("RCVID", "POSTLOG"); //Receiver identification
        $xml_UNB->appendChild($xml_RCVID);
        $xml_DTM = $this->dom->createElement("DTM", date("YmdHis")); //Date and Time
        $xml_UNB->appendChild($xml_DTM);
        $xml_INTREF = $this->dom->createElement("INTREF", $runnumber); //Sequence file number
        $xml_UNB->appendChild($xml_INTREF);

        $xml_UNH = $this->dom->createElement("UNH");
        $element->appendChild($xml_UNH);
        $xml_MESREF = $this->dom->createElement("MESREF", $runnumber); //Sequence message number
        $xml_UNH->appendChild($xml_MESREF);
        $xml_VRS = $this->dom->createElement("VRS", "2"); //Message version
        $xml_UNH->appendChild($xml_VRS);
        $xml_RLS = $this->dom->createElement("RLS", "11"); //Release
        $xml_UNH->appendChild($xml_RLS);


        $xml_GIA = $this->dom->createElement("GIA");
        $domAttribute = $this->dom->createAttribute('TYPE');
        // Value for the created attribute
        $domAttribute->value = '1';
        $xml_GIA->appendChild($domAttribute);
        $element->appendChild($xml_GIA);

        $xml_LOC = $this->dom->createElement("LOC", "92128"); //Location / Node
        $domAttribute_LOC = $this->dom->createAttribute('TYPE');
        // Value for the created attribute
        $domAttribute_LOC->value = '4';
        $xml_LOC->appendChild($domAttribute_LOC);
        $xml_GIA->appendChild($xml_LOC);

        $xml_NGI = $this->dom->createElement("NGI");
        $xml_GIA->appendChild($xml_NGI);
        $xml_PGI = $this->dom->createElement("PGI", "99"); //Product type code
        $xml_NGI->appendChild($xml_PGI);
        $xml_NRG = $this->dom->createElement("NRG", $runnumber); //Acceptance bill number
        $xml_NGI->appendChild($xml_NRG);
        $xml_DTM = $this->dom->createElement("DTM", date("YmdHis")); //Date and Time
        $xml_NGI->appendChild($xml_DTM);


        $xml_PNA = $this->dom->createElement("PNA");
        $domAttribute_PNA = $this->dom->createAttribute('TYPE');
        // Value for the created attribute
        $domAttribute_PNA->value = '1';
        $xml_PNA->appendChild($domAttribute_PNA);
        $xml_GIA->appendChild($xml_PNA);

        $xml_CLNT = $this->dom->createElement("CLNT", $constants["CTT_CUSTOMER_NO"]); //Contractual customer identification number  11706560
        $xml_PNA->appendChild($xml_CLNT);

        $xml_CONT = $this->dom->createElement("CONT", $constants["CTT_CONTRACT_NO"]); //Contract identification number "300234701"
        $xml_PNA->appendChild($xml_CONT);

        $xml_NIF = $this->dom->createElement("NIF", $constants["CTT_TAX_NO"]); //Tax identification number "720542861"
        $xml_PNA->appendChild($xml_NIF);

        $xml_NAD = $this->dom->createElement("NAD");
        $xml_PNA->appendChild($xml_NAD);
        $xml_NME = $this->dom->createElement("NME", $constants["CTT_SHIPPER_CONTACT"]); //Addressee�s name
        $xml_NAD->appendChild($xml_NME);
        $xml_ADR = $this->dom->createElement("ADR", $constants["CTT_SHIPPER_ADDRESSLINE1"]); //Adressee�s address
        $xml_NAD->appendChild($xml_ADR);
        $xml_CPL = $this->dom->createElement("CPL");
        $xml_NAD->appendChild($xml_CPL);
        $xml_PTC = $this->dom->createElement("PTC", $constants["CTT_SHIPPER_POSTCODE"]); //Postcode
        $xml_CPL->appendChild($xml_PTC);
        $xml_CTY = $this->dom->createElement("CTY", $constants["CTT_SHIPPER_CITY"]); //Locality
        $xml_CPL->appendChild($xml_CTY);
        $xml_CTR = $this->dom->createElement("CTR", "PT"); //Country ISO
        $xml_NAD->appendChild($xml_CTR);

        $xml_PAI = $this->dom->createElement("PAI");
        $domAttribute_PAI = $this->dom->createAttribute('TYPE');
        // Value for the created attribute
        $domAttribute_PAI->value = '1';
        $xml_PAI->appendChild($domAttribute_PAI);
        $xml_GIA->appendChild($xml_PAI);

        $xml_CUX = $this->dom->createElement("CUX", "EUR"); //Currency: PTE/EUR
        $xml_PAI->appendChild($xml_CUX);

        if (sizeof($this->record_array) > 0) {
            $orgdoc = new DOMDocument;

            foreach ($this->record_array as $records) {
                //print_r($records);
                $orgdoc->loadXML($records);


                $node = $orgdoc->getElementsByTagName("EMS")->item(0);
                $EMSImport = $orgdoc->saveXML();


                // Import the node, and all its children, to the document
                $EMS = $this->dom->importNode($node, true);
                // And then append it to the "<root>" node
                $xml_GIA->appendChild($EMS);
            }
            $GIA_CNT = $this->dom->createElement("CNT", sizeof($this->record_array));
            $xml_GIA->appendChild($GIA_CNT);

            $xml_CNT = $this->dom->createElement("CNT", "1");
            $element->appendChild($xml_CNT);

            $CTTXML = $this->dom->saveXML();
            return $CTTXML;
        }
    }

    public function sendBookings($ftpConstants, $run_number) {
        $isUpload = true;
        $filename = $this->booking_file;
        if (sizeof($this->record_array) > 0) {
            $path = SETTING_DIR_ASSETS . "data_send/ctt_booking/" . date("Y_m_d") . "/";
            if (!file_exists($path))
                @mkdir($path, 0777, true);

            $file_path = $path . $filename . ".xml";
            chmod($path, 0777);



            $this->getXML($ftpConstants, $run_number);
            $this->dom->save($file_path);



            if (isset($ftpConstants['CTT_FTP_SITE']) && trim($ftpConstants['CTT_FTP_SITE']) != '') {
                $SETTING_FTP_USER = $ftpConstants['CTT_FTP_USERNAME'];
                $SETTING_FTP_PASSWORD = $ftpConstants['CTT_FTP_PASSWORD'];
                $SETTING_FTP_SITE = $ftpConstants['CTT_FTP_SITE'];
                $remote_file_path2 = "./" . $filename . ".exp";

                $ftp_object1 = new FTPfile($SETTING_FTP_USER, $SETTING_FTP_PASSWORD, $SETTING_FTP_SITE);

                $ftp_object1->passive();
                if (!$ftp_object1->put($remote_file_path2, $file_path, FTP_ASCII)) {
                    $isUpload = false;
                    mail("itsupport@oneworldexpress.com", "UNABLE TO SEND CTT DATA", "UNABLE TO SEND CTT DATA" . $filename);
                }
            }
        }
        return $isUpload;
    }

    public function recycledShipment($consignment) {
        $output["STATUS"] = "SUCCESS";
        return $output;
    }

}
