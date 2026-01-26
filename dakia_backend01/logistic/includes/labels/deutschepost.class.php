<?php

class DeutschePost implements CarrierService {

    private $pdf;
    private $serviceValues = null;
    private $country = null;
    private $user;
    private $constants = null;
    private $record_array = null;
    private $bagFileData = [];
    private $parcelFileData = [];

    public function __construct() {
        
    }

    public function validation(Consignment $consignment, Services $service, Country $country) {
        
    }

    public function remoteareas($consignment, $carrierObject, $sender) {
        
    }

    public function label($consignment, $labelType = 'pdf', $size = '100x150') {

       
        $output = array();
        $this->serviceValues = new Services($consignment->getServiceId());
        $this->country = new Country($consignment->getCountryId());
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
        
        if (trim(@$this->constants['API_NAME']) == '' || trim(@$this->constants['API_DESCRIPTION']) == '' || trim(@$this->constants['API_URL']) == '' || trim(@$this->constants['API_CLIENTID']) == '' || trim(@$this->constants['API_SECRET']) == '' || trim(@$this->constants['API_USER_ID']) == '') {
            $output['STATUS'] = 'ERROR';
            $output['MESSAGE'] = "Please check service constants. Please contact to itsupport@oneworldexpress.com";
            return $output;
        }
        if (trim(@$this->constants['INTEGRATION_TYPE']) == 'API') {
            $output = $this->apiLabel($this->constants, $consignment);
        } else {
            $output = $this->ediLabel($this->constants, $consignment);
        }
        return $output;
    }

    private function ediLabel($constantsArray, Consignment $consignment) {
        $pdf = new PdfBase(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $this->pdf = $pdf;
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
            if ($parcel->getTrackingNumber() == '') {
                $resultArray = LicencePlate::getLicencePlateNumber($licence_plate_id);
                if (trim($resultArray['STATUS']) == 'ERROR')
                    return $resultArray;
                else {
                    $checkdigit = LicencePlate::mod11($resultArray["RANGE"]);
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
            $page_size = array(150, 100);
            $this->pdf->AddPage("P", $page_size);
            $this->addWayBill($consignment, $parcel_idx, $licence_plate);

            $new_page_flag = true;
            ++$parcel_idx;
        }

        /*if ($this->country->getRegion() == Consignment::SERVICE_INTERNATIONAL) {
            $this->pdf->AddPage("P", $page_size);

            $this->addCN22($consignment);

            //  $this->pdf->AddPage("P",$page_size);
            // $this->addDecleration($consignment);
        }*/


        $this->pdf->IncludeJS("print();");
        $this->pdf->Output("../_assets/pdf/" . date('Y_m_d') . '/' . $consignment->getId() . "_" . time() . ".pdf", "F");
        $output['STATUS'] = 'SUCCESS';
        $output['LABEL'] = date('Y_m_d') . '/' . $consignment->getId() . "_" . time() . ".pdf";
        $output['TRACKING_NUMBER'] = $licence_plate_array;
        return $output;
    }

    private function apiLabel($constantsArray, Consignment $consignmentObject) {
        $output = array();
        $this->apiUrl = trim(@$this->constants['API_URL']); //'https://api.deutschepost.com/';
        if (substr($this->apiUrl, -1) != '/')
            $this->apiUrl = $this->apiUrl . '/';
        $this->apiName = trim(@$this->constants['API_NAME']); //"One World Express Inc Ltd";
        $this->apiDescription = trim(@$this->constants['API_DESCRIPTION']); //"One World Express Inc Ltd (6295318410), DPI customer";
        $this->apiUSERID = trim(@$this->constants['API_USER_ID']); //'cs@oneworldexpress.com';
        $this->apiclientId = trim(@$this->constants['API_CLIENTID']); //'9d4d2894-aec3-474f-97be-bafd2afb42f1';
        $this->apisecret = trim(@$this->constants['API_SECRET']); //'3d60516c-5c21-43e7-9472-77615b84c1c9';

        $parcel_list = $consignmentObject->getParcels();
        if (count($parcel_list) > 1) {
            $output["STATUS"] = 'ERROR';
            $output["MESSAGE"] = 'This service support single piece shipment';
        } else {
            $authenticationInfo = $this->authenticateionToken();


            if (trim($authenticationInfo->access_token) != '') {
                $output = $this->apiLabelRequest($consignmentObject, $authenticationInfo->access_token);
                //OLD API LABEL
               // $output = $this->apiLabelRequestDefered($consignmentObject, $authenticationInfo->access_token);
            } else {
                $output["STATUS"] = 'ERROR';
                $output["MESSAGE"] = $authenticationInfo->error;
            }
        }

        return $output;
    }

    public function tracking($trackingNumber, $trackBy = '', $EDI = false) {
        $tracking = new Tracking();
        $deliveredArray = array('Delivered', 'Final Delivery');  
        
        include_once(BASE_PATH."includes/labels/deutscheposttrackingstatus.class.php");
        $response = file_get_contents("https://www.packet.deutschepost.com//web/portal-europe/packet_traceit?barcode=" . $trackingNumber);
        //print_r($response); die;
        $postable = strpos($response, '<table>');
        $table_start_part = substr($response, $postable, strlen($response));
        $pos2 = strpos($table_start_part, '</table>');
        $table_end_part = substr($table_start_part, 0, $pos2);
        $dom = new DOMDocument();
        $dom->loadHTML($table_end_part);
        $tables = $dom->getElementsByTagName('table');
        //print_r($tables); die;
        if (count($tables->item(0)) > 0) {
            $entityId = 0;
            $parcelObj = new ParcelFilter();
            $parcelObj->addTrackingNumberFilter($trackingNumber);
            $parcelDataArray = $parcelObj->getColumnList('p.id, p.tracking_number, p.consignment_id');
            if (count($parcelDataArray) > 0) {
                $parcelData = $parcelDataArray[0];
                $entityId = $parcelData->getId();
            }
            
            ////////////////////// Carrier Received ////////////////////////////////
            
            $trackingDataFilterObj = new TrackingDataFilter();
            $trackingDataFilterObj->addFieldFilter('    tracking_number', $trackingNumber);        
            $trackingDataFilterObj->addFilter("carrier_code not in ('','shipment information uploaded to deutsche post')");
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
            
            $rows = $tables->item(0)->getElementsByTagName('tr');
            
            $parcelEntity = new Parcel($entityId);
            $finalStatusCode = $parcelEntity->getParcelStatusCode();
            
            foreach ($rows as $row) 
            {
                $cols = $row->getElementsByTagName('td');
                $dateArray = explode(".", trim($cols->item(0)->nodeValue));
                $day = $dateArray[0];
                $month = $dateArray[1];
                $year = $dateArray[2];
                
                if (count($dateArray) > 2 && $entityId > 0) {
                    
                    $DateTime = $year . "-" . $month . "-" . $day;                    
                    $EventDescription = trim($cols->item(1)->nodeValue);
                    $EventCode = strtolower($EventDescription);                    
                    $ServiceAreaCode = '';
                    $ServiceAreaDescription = '';
                    $Signatory = '';
                    // Dont enter any other event code if it is against 148 Event Code
                    if($carrierCodeCarrierReceived == $EventCode)
                    {
                        continue;
                    }
                    //$spTrackingStatus = deutschepostTrackingStatus::getOweStatusCode($EventCode);
                    ////////////////////// Carrier Received ////////////////////////////////
                    if($carrierReceivedCheck == 1 && $EventCode != 'shipment information uploaded to deutsche post' )
                    {
                        $spTrackingStatus = '148';
                        $carrierReceivedCheck = 0 ;                        
                    }
                    else
                    {
                        $spTrackingStatus = deutschepostTrackingStatus::getOweStatusCode($EventCode);
                    }            
                    ////////////////////// Carrier Received ////////////////////////////////
                    $result = $tracking->saveTrackPoint($entityId, $trackBy, $DateTime, $trackingNumber, $spTrackingStatus, $ServiceAreaDescription, $EventCode, $EventDescription, $deliveredArray, $finalStatusCode);
                    if($result == true)
                    {
                        break;
                    }
                }
            } 
            $tracking->saveConsignmentTrackingStatus($trackingNumber, 'deutschepostTrackingStatus');  
        }
    }

    public function sendData($tracking_numbers = array()) {
        
        $output = [];
        $agentServiceArray = [];
        $consignmentData = new ConsignmentFilter();
        $consignmentData->addFilter("     s.carrier_id= '189'", "servicefilter");
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
                    $consignmentShipmentData = $consignmentShipmentDataFilter->getColumnList(" c.id 'consignment_id', s.carrier_id ,c.awb");

                    if (count($consignmentShipmentData) > 0) {
                        $consignmentIdArray = [];

                        foreach ($consignmentShipmentData as $consignmentItemData) {

                            $consignmentId = $consignmentItemData->getConsignmentId();
                            $consignmentIdArray[] = $consignmentId;
                            $carrierId = $consignmentItemData->getCarrierId();
                            $this->record_array[] = $consignmentItemData->getAwb();
                        }
                        $carrierId = $this->serviceValues->getCarrierId();
                        $agentid = trim($agentServiceArray['agent'][$key]);
                        
                        $response = $this->orderDispatchData($this->constants[$serviceid], $this->record_array);
                        
                        if($response["STATUS"] == "ERROR"){
                            return $response;
                        }
                        else if (!empty($consignmentIdArray)) {
                                $response_data = $response["DATA"];
                                $manifestNo = $response_data["awb"]; 
                                $sql = "UPDATE consignment SET booked_file_id = '" . $manifestNo . "' , 
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

    public function manifest($consignment) {
        
    }

    public function preAdvice($consignment) {
        
    }

    private function addWayBill(Consignment $consignment, $parcel_idx, $licence_plate) {


        $this->pdf->setFont("helvetica", "", 8);

        if ($this->serviceValues->getCode() == "DEUPSTREGEURPRO" || $this->serviceValues->getCode() == "DEUPSTREGINTPRO")
            $image = "../images/deutsche_post_ppi.png";
        else
            $image = "../images/deutschepostppitracked.jpg";
        if (file_exists($image)) {
            $fitbox = 'C';
            $fitbox[1] = 'M';
            $this->pdf->image($image, 35, 3, 65, 27, '', '', '', false, 700, '', false, false, 0, $fitbox, false, false);
        }
        $this->pdf->Text(5, 31, "CustRef:");
        $this->pdf->Text(25, 31, $consignment->getHawb());
        $awb = $licence_plate;

        $first_two = substr($awb, 0, 2);
        $second_two = substr($awb, 2, 2);
        $next_three = substr($awb, 4, 3);
        $next_three_no = substr($awb, 7, 3);
        $last_three = substr($awb, 10, 3);
        $this->pdf->Text(30, 49, $first_two . " " . $second_two . " " . $next_three . " " . $next_three_no . " " . $last_three);
        $this->pdf->line(21, 52.5, 67, 52.5);

        $this->pdf->SetFont('Arial', '', '35', '', 'default', true);
        $this->pdf->Text(6, 48, "R", false, false, true, 0, 0, '', false, '', 0, false, 'T', 'M', false);

        $this->pdf->write1DBarcode($awb, 'C128', 21, 53, 46, 7, 0.7);
        $this->pdf->setFont("Arial", "", 8);
        if ($this->country->getRegion() == "R1") {
            $this->pdf->Text(6, 61, "Einschreiben");
        } else {
            $this->pdf->Text(6, 61, "Recommandé");
        }
        $address = $consignment->getContact() . " " . $consignment->getCompany() . "\r\n" . $consignment->getAddressLine1() . "  " . $consignment->getAddressLine2() . " " . $consignment->getAddressLine3() . " \r\n" . $consignment->getPostcode() . " " . strtoupper($consignment->getCity()) . "\r\n" . strtoupper($this->country->getName());
        $this->pdf->MultiCell("59", "10", $address, 0, 'L', 0, 1, 7, 66);
    }

    private function addCN22(Consignment $consignment) {


        $this->pdf->line(2, 2, 75, 2);
        $this->pdf->line(2, 12, 75, 12);
        $this->pdf->line(2, 90, 2, 2);

        $this->pdf->line(2, 90, 75, 90);
        $this->pdf->line(2, 25, 75, 25);
        $this->pdf->line(50, 25, 50, 55);
        $this->pdf->line(2, 45, 75, 45);
        $this->pdf->line(2, 55, 75, 55);
        $this->pdf->line(2, 71, 75, 71);
        $this->pdf->line(10, 20, 10, 20);


        $this->pdf->setFont("helvetica", "", 9);
        $this->pdf->Text(2, 4, "CUSTOMS DECLARATION");
        $this->pdf->Text(60, 4, "CN22");

        $this->pdf->setFont("helvetica", "", 7);
        $this->pdf->Text(2, 8, "Postal Aministration (May be opened officially");
        $this->pdf->Text(60, 8, "Important!");

        $this->pdf->setXy(5, 14);
        $this->pdf->Cell("3", "3", '', 1, 0, '', 0, '');
        $this->pdf->setXy(5, 20);
        $this->pdf->Cell("3", "3", '', 1, 0, '', 0, '');
        $this->pdf->setXy(40, 14);
        $this->pdf->Cell("3", "3", '', 1, 0, '', 0, '');
        $this->pdf->setXy(40, 20);
        $this->pdf->Cell("3", "3", '', 1, 0, '', 0, '');


        $this->pdf->Text(15, 14, "Gift");
        $this->pdf->Text(15, 20, "Printed Matter");
        $this->pdf->Text(50, 14, "Sample");
        $this->pdf->Text(50, 20, "Others");



        $this->pdf->Text(2, 26, "Detailed description of contents");
        $this->pdf->Text(2, 30, str_replace("&"," - ",$consignment->getDescription()));

        $this->pdf->Text(52, 26, "Value");
        $this->pdf->Text(52, 30, $consignment->getValue());


        $this->pdf->Text(2, 47, "Origin Country");
        $this->pdf->Text(25, 47, "Total Weight in Kg");
        $this->pdf->Text(52, 47, "Total Value");


        $this->pdf->setFont("helvetica", "", 8);
        $this->pdf->Text(2, 51, $consignment->getCountry());
        $this->pdf->Text(25, 51, $consignment->getWeight());
        $this->pdf->Text(52, 51, $consignment->getCurrency());
        $this->pdf->Text(58, 51, $consignment->getValue());


        $this->pdf->setFont("helvetica", "", 7);

        $this->pdf->MultiCell("70", "10", "I, hereby undersigned, whose name and address are given on the item certify that the particulars given in this declaration are correct			and that this item does not contain any dangerous articles or articles prohibited by legislation or by postal or customs regulations.", 0, 'L', 0, 1, 2, 55);

        $this->pdf->setFont("helvetica", "", 9);
        $this->pdf->Text(2, 72, "Date and Sender's Signature");
        $this->pdf->Text(15, 76, date("d-M-Y"));

        $image1 = SETTING_URL . "images/signature.png";
        if (file_exists($image1)) {
            $this->pdf->image($image1, 45, 72, 28, 15);
        }
        $style = array('width' => 1);
        $this->pdf->line(75, 90, 75, 2, $style);
    }

    public function recycledShipment($consignment) {
        $output["STATUS"] = "SUCCESS";
        return $output;
    }

    private function authenticateionToken() {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->apiUrl . "v1/auth/accesstoken");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        $header = base64_encode($this->apiclientId . ':' . $this->apisecret);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Accept: application-json",
            "Authorization: Basic " . $header
        ));

        $authenticateionToken = curl_exec($ch);
        curl_close($ch);

        //var_dump($authenticateionToken);
        //die;
        return $tokenReponse = json_decode($authenticateionToken);
    }

    private function apiLabelRequestDefered(Consignment $consignment, $accessToken = '') {
        $output["STATUS"] = "SUCCESS";
        $parcel_list = $consignment->getParcels();
        $serviceDataObject = new Services($consignment->getServiceId());
        $countryDataObject = new Country($consignment->getCountryId());
        if ($serviceDataObject->getId() <= 0) {
            $output["STATUS"] = 'ERROR';
            $output["MESSAGE"] = "Service not found. Please check your shipment form";
            return $output;
        }
        if ($countryDataObject->getId() <= 0) {
            $output["STATUS"] = 'ERROR';
            $output["MESSAGE"] = "Country not found. Please check your shipment form";
            return $output;
        }



        $hscode = '';
        $returnItemWanted = 0;
        $ConsignmentHscodeFilter = new ConsignmentHscodeFilter();
        $ConsignmentHscodeFilter->addconsignmentFilter($consignment->getId());
        $hscodeList = $ConsignmentHscodeFilter->getList();
        if (count($hscodeList) > 0) {
            $hscodeList = $hscodeList[0];
            $hscode = $hscodeList->getHscode();
        }


        $parcel_count = sizeof($parcel_list);
        $parcel_idx = 0;
        $parcelContents = array();
        foreach ($parcel_list as $parcel) {
            $parcelContents[] = array(
//                "contentPieceHsCode" => (trim($hscode)!= '' ? $hscode : '0' ),//
//                "contentPieceHsCode"=> "",
                "contentPieceDescription" => str_replace("&"," - ",$consignment->getDescription()) ,//"Trousers",
                "contentPieceValue" => trim(number_format($consignment->getValue(),2)),
                "contentPieceNetweight" => ($consignment->getWeight() * 1000),
               // "contentPieceOrigin" => "DE",
                "contentPieceAmount" => $consignment->getNumberPieces()
            );
        }
        $request1 = array(
                    "id" => 1,
                    "product" => $this->constants["DEUTSCHEPOST_PRODUCT_CODE"], //"GPT",
                    "serviceLevel" => $this->constants["DEUTSCHEPOST_SERVICE_LEVEL"], // "PRIORITY",
                    "custRef" => trim($consignment->getHawb()),
                    "recipient" => $consignment->getContact(),
                    "recipientPhone" => trim($consignment->getTelephone()),
                    "recipientFax" => "",
                    "recipientEmail" => trim($consignment->getEmail()),
                    "addressLine1" => trim($consignment->getAddressLine1()),
                    "addressLine2" => (trim($consignment->getAddressLine2()) != '-' ? trim($consignment->getAddressLine2()) : ''),
                    "addressLine3" => (trim($consignment->getAddressLine3()) != '-' ? trim($consignment->getAddressLine3()) : ''),
                    "city" => trim($consignment->getCity()),
                    "state" => "",
                    "postalCode" => trim($consignment->getPostcode()),
                    "destinationCountry" => trim($countryDataObject->getIso()),
                    "returnItemWanted" => $returnItemWanted,
                    "shipmentAmount" => trim($consignment->getValue()),
                    "shipmentCurrency" => trim($consignment->getCurrency()),
                    "shipmentGrossWeight" => ($consignment->getWeight() * 1000),
                    "shipmentNaturetype" => "GIFT",
                    "senderTaxId" => $consignment->getIossNumber(),
                    "contents" => $parcelContents
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->apiUrl . "dpi/shipping/v1/customers/6295318410/items");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($request1));

        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Content-Type: application/json",
            "Accept: application/json",
            "ThirdPartyVendor-ID: 3pv_one_world_express",
            "Authorization: Bearer " . $accessToken
        ));

        $orderResponse = json_decode(curl_exec($ch));
        $consignment->setApiData(json_encode($request1), print_r($orderResponse, true), 'DEUTSCHEPOST_SHIPMENT_SUCCESS');
        curl_close($ch);
        if (sizeof($orderResponse) > 0) {
            if (count($orderResponse->message) > 0)
                $order_message = implode(", ", $orderResponse->message);
            else if (count($orderResponse->messages) > 0)
                $order_message = implode(", ", $orderResponse->messages);
            if (trim($order_message) != "") {
                $output["STATUS"] = 'ERROR';
                $output["MESSAGE"] = $order_message;
            } else {
                $trackingNumberArray = array();

                $orderId = $orderResponse->orderId;
                $itemId = $orderResponse->id;
                $trackingNumber = $orderResponse->barcode;

                $parcel_list = $consignment->getParcels();
                // Generate label for each parecel
                foreach ($parcel_list as $parcel) {
                    $parcel->setTrackingNumber($trackingNumber);
                    $parcel->setConsignmentID($consignment->getId());
                    $parcel->save();

                    $trackingNumberArray[] = $trackingNumber;
                }

                $headerLabel = array(
                    "Content-Type: application/json",
                    // "Accept: application/json",
                    "ThirdPartyVendor-ID: 3pv_one_world_express",
                    "Authorization: Bearer " . $accessToken
                );

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $this->apiUrl . "dpi/shipping/v1/customers/6295318410/items/" . $trackingNumber . "/label");
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
                curl_setopt($ch, CURLOPT_HEADER, FALSE);
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headerLabel);
                $labelResponse = curl_exec($ch);


                $consignment->setApiData(print_r($headerLabel, true), print_r($labelResponse, true), 'DEUTSCHEPOST_LABEL_SUCCESS');
                curl_close($ch);


                $fileName = "../_assets/pdf/" . date('Y_m_d') . '/' . $consignment->getId() . ".pdf";
                file_put_contents($fileName, $labelResponse);
                $output["STATUS"] = 'SUCCESS';
                $output["LABEL"] = date('Y_m_d') . '/' . $consignment->getId() . ".pdf";
                $output["TRACKING_NUMBER"] = $trackingNumberArray;
            }
        } else {
            $output["STATUS"] = 'ERROR';
            $output["MESSAGE"] = $orderResponse->error;
        }
        return $output;
    }
    private function apiLabelRequest(Consignment $consignment, $accessToken = '') {
      
        $output["STATUS"] = "SUCCESS";
        $parcel_list = $consignment->getParcels();
        $serviceDataObject = new Services($consignment->getServiceId());
        $countryDataObject = new Country($consignment->getCountryId());
        if ($serviceDataObject->getId() <= 0) {
            $output["STATUS"] = 'ERROR';
            $output["MESSAGE"] = "Service not found. Please check your shipment form";
            return $output;
        }
        if ($countryDataObject->getId() <= 0) {
            $output["STATUS"] = 'ERROR';
            $output["MESSAGE"] = "Country not found. Please check your shipment form";
            return $output;
        }



        $hscode = '0';
        $returnItemWanted = 0;
      
        if($this->serviceValues->getCode() == "STDEUPTPR"){
           $prodcutCode = "GPT" ;
        }
        else if($this->serviceValues->getCode() == "STDEUPPPR" || $this->serviceValues->getCode() == "STDUPREGM"){
            $prodcutCode = "GPP" ;
        }
        else if($this->serviceValues->getCode() == "STDEUMPPR"){
            $prodcutCode = "GMP" ;
        }
        $parcel_count = sizeof($parcel_list);
        $parcel_idx = 0;
        $parcelContents = array();
        foreach ($parcel_list as $parcel) {
            $parcelContents[] = array(
//                "contentPieceHsCode" => (trim($hscode)!= '' ? $hscode : '0' ),//
//                "contentPieceHsCode"=> "",
                "contentPieceDescription" => $this->utfEncode(substr(str_replace("&"," - ",$consignment->getDescription()), 0, 20 )),
                "contentPieceValue" => trim(number_format($consignment->getValue(),2)),
                "contentPieceNetweight" => ($consignment->getWeight() * 1000),
               // "contentPieceOrigin" => "DE",
                "contentPieceAmount" => $consignment->getNumberPieces()
            );
        }
        
        $request1 = array("customerEkp" =>  $this->constants["CUSTOMER_EKP"],//"6295318410",
            "orderStatus" => "FINALIZE",
            "paperwork" => array(
                "contactName" => $this->utfEncode(substr(trim($consignment->getContact()) , 0, 25)),
                "pickupType" => "CUSTOMER_DROP_OFF",
                'jobReference' => substr( trim($consignment->getHawb()) , 0, 16),
                "awbCopyCount" => $consignment->getNumberPieces()
            ),
            "items" => array(
                0 => array(
                    "id" => 1,
                    "product" => $prodcutCode, //"GPT",
                    "serviceLevel" => "PRIORITY",
                    "custRef" => $this->utfEncode(trim($consignment->getHawb())),
                    "recipient" => $this->utfEncode(substr(trim($consignment->getContact()) , 0, 25)),
                    "recipientPhone" => $this->utfEncode(trim($consignment->getTelephone())),
                    "recipientFax" => "",
                    "recipientEmail" => $this->utfEncode(trim($consignment->getEmail())),
                    "addressLine1" => $this->utfEncode(trim($consignment->getAddressLine1())),
                    "addressLine2" => $this->utfEncode((trim($consignment->getAddressLine2()) != '-' ? trim($consignment->getAddressLine2()) : '')),
                    "addressLine3" => $this->utfEncode((trim($consignment->getAddressLine3()) != '-' ? trim($consignment->getAddressLine3()) : '')),
                    "city" => $this->utfEncode(trim($consignment->getCity())),
                    "state" => "",
                    "postalCode" => $this->utfEncode(trim($consignment->getPostcode())),
                    "destinationCountry" => trim($countryDataObject->getIso()),
                    "returnItemWanted" => $returnItemWanted,
                    "shipmentAmount" => $this->utfEncode(trim($consignment->getValue())),
                    "shipmentCurrency" => $this->utfEncode(trim($consignment->getCurrency())),
                    "shipmentGrossWeight" => ($consignment->getWeight() * 1000),
                    "shipmentNaturetype" => "GIFT",
                    "senderTaxId" => $consignment->getIossNumber(),
                    "contents" => $parcelContents))
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->apiUrl . "dpi/shipping/v1/orders");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($request1));

        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Content-Type: application/json",
            "Accept: application/json",
            "Authorization: Bearer " . $accessToken
        ));

        $orderResponse = json_decode(curl_exec($ch));
        $consignment->setApiData(json_encode($request1), print_r($orderResponse, true), 'DEUTSCHEPOST_SHIPMENT_SUCCESS');
        curl_close($ch);
        if (sizeof($orderResponse) > 0) {
            if (count($orderResponse->message) > 0)
                $order_message = implode(", ", $orderResponse->message);
            else if (count($orderResponse->messages) > 0)
                $order_message = implode(", ", $orderResponse->messages);
            if (trim($order_message) != "") {
                $output["STATUS"] = 'ERROR';
                $output["MESSAGE"] = $order_message;
            } else {
                $trackingNumberArray = array();

                $orderId = $orderResponse->orderId;
                $itemId = $orderResponse->shipments[0]->items[0]->id;
                $trackingNumber = $orderResponse->shipments[0]->items[0]->barcode;

                $parcel_list = $consignment->getParcels();
                // Generate label for each parecel
                foreach ($parcel_list as $parcel) {
                    $parcel->setTrackingNumber($trackingNumber);
                    $parcel->setConsignmentID($consignment->getId());
                    $parcel->save();

                    $trackingNumberArray[] = $trackingNumber;
                }

                $headerLabel = array(
                    "Content-Type: application/json",
                    // "Accept: application/json",
                    "Authorization: Bearer " . $accessToken
                );

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $this->apiUrl . "dpi/shipping/v1/items/" . $itemId . "/label");
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
                curl_setopt($ch, CURLOPT_HEADER, FALSE);
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headerLabel);
                $labelResponse = curl_exec($ch);


                $consignment->setApiData(print_r($headerLabel, true), print_r($labelResponse, true), 'DEUTSCHEPOST_LABEL_SUCCESS');
                curl_close($ch);


                $fileName = "../_assets/pdf/" . date('Y_m_d') . '/' . $consignment->getId() . ".pdf";
                file_put_contents($fileName, $labelResponse);
                $output["STATUS"] = 'SUCCESS';
                $output["LABEL"] = date('Y_m_d') . '/' . $consignment->getId() . ".pdf";
                $output["TRACKING_NUMBER"] = $trackingNumberArray;
            }
        } else {
            $output["STATUS"] = 'ERROR';
            $output["MESSAGE"] = $orderResponse->error;
        }
        return $output;
    }
    private function isUTF8($str) {
   return preg_match('%^(?:
         [\x09\x0A\x0D\x20-\x7E]           # ASCII
       | [\xC2-\xDF][\x80-\xBF]            # non-overlong 2-byte
       | \xE0[\xA0-\xBF][\x80-\xBF]        # excluding overlongs
       | [\xE1-\xEC\xEE\xEF][\x80-\xBF]{2} # straight 3-byte
       | \xED[\x80-\x9F][\x80-\xBF]        # excluding surrogates
       | \xF0[\x90-\xBF][\x80-\xBF]{2}     # planes 1-3
       | [\xF1-\xF3][\x80-\xBF]{3}         # planes 4-15
       | \xF4[\x80-\x8F][\x80-\xBF]{2}     # plane 16
   )*$%xs', $str);
  }
    private	function utfEncode($str,$encoding = "") {
              $str = preg_replace('/[^(\x20-\x7F)]*/','', $str);
              $str = str_replace('&','and', $str);
              $str = str_replace('<','&lt;', $str);
          $str = str_replace('>','&gt;', $str);
          $str = str_replace("'","", $str);

              if ($str !== "") {
                    if (empty($encoding) && self::isUTF8($str))
                      $encoding = "UTF-8";
                    if (empty($encoding))
                      $encoding = mb_detect_encoding($str,'UTF-8, ISO-8859-1');
                    if (empty($encoding))
                      $encoding = "ISO-8859-1"; //  if charset can't be detected, default to ISO-8859-1
                    return $encoding == "UTF-8" ? $str : @mb_convert_encoding($str,"UTF-8",$encoding);
                    }
              }

    public function orderDispatchData($constants, $arrayBarcode = array())
    {
        if(count($arrayBarcode) <= 0)
        {
            $output["STATUS"]   =   'ERROR';
            $output["MESSAGE"]   =  'Tracking Number cannot be blank.';
        }
        
         $this->apiUrl = trim(@$constants['API_URL']); //'https://api.deutschepost.com/';
        if (substr($this->apiUrl, -1) != '/')
            $this->apiUrl = $this->apiUrl . '/';
        $this->apiName = trim(@$constants['API_NAME']); //"One World Express Inc Ltd";
        $this->apiDescription = trim(@$constants['API_DESCRIPTION']); //"One World Express Inc Ltd (6295318410), DPI customer";
        $this->apiUSERID = trim(@$constants['API_USER_ID']); //'cs@oneworldexpress.com';
        $this->apiclientId = trim(@$constants['API_CLIENTID']); //'9d4d2894-aec3-474f-97be-bafd2afb42f1';
        $this->apisecret = trim(@$constants['API_SECRET']); //'3d60516c-5c21-43e7-9472-77615b84c1c9';
        
		$request1 = array(
                                "itemBarcodes" => $arrayBarcode,
                                "paperwork" => array(
                                                        "contactName"=> "Jason",
                                                        "awbCopyCount"=> count($arrayBarcode)
                                )
                                );
		$authenticationInfo =   $this->authenticateionToken();
		if(trim($authenticationInfo->access_token) != '')
		{
			$accessToken =trim($authenticationInfo->access_token);
			 $ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $this->apiUrl."dpi/shipping/v1/customers/6295318410/orders");
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
			curl_setopt($ch, CURLOPT_HEADER, FALSE);
			curl_setopt($ch, CURLOPT_POST, TRUE);
			curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($request1));
			curl_setopt($ch, CURLOPT_HTTPHEADER, array(
				"Content-Type: application/json",
				"Accept: application/json",
				"ThirdPartyVendor-ID: 3pv_one_world_express",
				"Authorization: Bearer " . $accessToken
			));
			$orderResponse = json_decode(curl_exec($ch));
                        
			curl_close($ch);
			if(trim(@$orderResponse->orderId) != '')
			{
				$output["STATUS"]   =   'SUCCESS';
				$output["DATA"]   	=  array('customerEkp'=>$orderResponse->customerEkp, 'orderId'=>$orderResponse->orderId, 'awb'=>$orderResponse->shipments[0]->awb);
			}
			else
			{
				$output["STATUS"]   =   'ERROR';
				$output["MESSAGE"]   =  $orderResponse->messages[0];
			}
		}
		else
		{
			$output["STATUS"]   =   'ERROR';
			$output["MESSAGE"]   =  $authenticationInfo->error;
		}
		return $output;
    }

    public function reconciliation_data($headingArr,$carrierId,$relPath, $relPath2,$new_csv_file_created, $new_csv_bag_file_created,$filePath,$batchNumber) {
        $output = [];
        $carrierObj = new Carrier($carrierId);
        $user = SessionManager::getUser();
        $userId = $user->getId();
        /* parcel csv */
        $comaSeptHeading = implode(",",$headingArr);
        $tableColumn = rtrim($comaSeptHeading,',');
        $csvParcelStr = $tableColumn;
        $csvParcelStr .= "\r\n";
        /* bag csv */
        $bagheadingArr = [
            'batch_number',
            'bag_number',
            'invoice_number',
            'country',
            'country_code',
            'weight',
            'number_of_piece',
            'matched_piece',
            'matched_weight',
            'net_value',
            'status',
            'message'
        ];
        $comaSeptBagHeading = implode(",",$bagheadingArr);
        $tableBagColumn = rtrim($comaSeptBagHeading,',');
        $csvBagStr = $tableBagColumn;
        $csvBagStr .= "\r\n";
        $templateCheck = 1;
        $checkExist = 0;
        $output['new_invoice_save'] = 0;
        $output['total_weight'] = 0;
        $output['total_pieces'] = 0;
        $output['total_amount'] = 0;
        $_bagFileData = [];
        $_parcelFileData = [];
        $isFile1 = false;
        $isFile2 = false;
        $row = 0;
        if (($handle = fopen($relPath, "r")) !== FALSE) {
            while (($data = fgetcsv($handle)) !== FALSE) {
                if($row == 0) {
                    if (!empty($data[0]) && strpos($data[0], 'Customer') !== false) {
                        $isFile1 = true;
                    } else {
                        $isFile2 = true;
                    }
                }
                if ($row > 0) {
                    if($isFile1) {
                        $_bagFileData = $this->bagFileData($data);
                    } else if($isFile2) {
                        $_parcelFileData = $this->parcelFileData($data);
                    }
                }
                $row++;
            }
        }
        $isFile1 = false;
        $isFile2 = false;
        $row = 0;
        if (($handle = fopen($relPath2, "r")) !== FALSE) {
            while (($data = fgetcsv($handle)) !== FALSE) {
                if($row == 0) {
                    if (!empty($data[0]) && strpos($data[0], 'Mail ID') !== false) {
                        $isFile2 = true;
                    } else {
                        $isFile1 = true;
                    }
                }
                if ($row > 0) {
                    if($isFile1) {
                        $_bagFileData = $this->bagFileData($data);
                    } else if($isFile2) {
                        $_parcelFileData = $this->parcelFileData($data);
                    }
                }
                $row++;
            }
        }
        $dataBagArr = [];
        /* make bag reconcilation */
        if(count($_bagFileData)) {
            foreach ($_bagFileData as $bagNumber => $countryDt) {
                foreach($countryDt as $country => $serviceDt) {
                    foreach($serviceDt as $serviceCode => $bagDt) {
                        $invoiceNumber = $bagDt['invoice_number'];
                        $weight = $bagDt['total_weight'];
                        $numberOfPiece = $bagDt['no_of_items'];
                        $turnover = $bagDt['turnover'];
                        $country = $bagDt['country'];
                        $countryCode = $bagDt['country_code'];
                        $bgDt = [
                            'batch_number' => $batchNumber,
                            'bag_number' => $bagNumber,
                            'invoice_number' => $invoiceNumber,
                            'country' => $country,
                            'country_code' => $countryCode,
                            'weight' => $weight,
                            'number_of_piece' => $numberOfPiece,
                            'matched_piece' => '',
                            'matched_weight' => '',
                            'net_value' => $turnover,
                            'status' => '',
                            'message' => ''
                        ];
                        $dataBagArr[] = $bgDt;
                        $comaSept = implode(",", $bgDt);
                        $csvBagStr .= rtrim($comaSept, ',');
                        $csvBagStr .= "\r\n";
                    }
                }
            }
        }
        if($output['status'] != "error" && !$output['return']) {
            $myCsvFile = fopen($new_csv_bag_file_created, "a") or die("Unable to open file!");
            fwrite($myCsvFile, $csvBagStr);
            fclose($myCsvFile);

            $dateNow = date('Y-m-d H:i:s');
            $load_data_sql = "LOAD DATA LOCAL INFILE '" . $new_csv_bag_file_created . "' INTO TABLE `reconciliation_bag_data`
                            FIELDS ENCLOSED BY '\"' 
                            TERMINATED BY ',' LINES TERMINATED BY '\n' IGNORE 1 LINES (
                                " . $tableBagColumn . "
                            )";
            $res = DbAccess3::runQueryWithError($load_data_sql);
            if ($res === false) {
                $error = DbAccess3::$dbError;
                $output['status'] = 'error';
                $output['message'] = $error[0];
            } else {
                $sql = "SELECT * FROM reconciliation_bag_data WHERE batch_number='" . $batchNumber . "' ";
                $resultSql = DbAccess3::runQuery($sql);
                $res = [];
                while ($obj = mysqli_fetch_object($resultSql)) {
                    $res[] = $obj;
                }
                $output['status'] = 'success';
                $output['file_path_2'] = $filePath;
                $output['batch_number'] = $batchNumber;
                $output['template'] = $templateCheck;
                $output['data_bag'] = $res;
            }
        }
        /* make parcel reconcilation */
        $dataParcelArr = [];
        if(count($_parcelFileData)) {
            foreach ($_parcelFileData as $bagNumber => $countryDt) {
                foreach ($countryDt as $countryCode => $serviceDt) {
                    foreach ($serviceDt as $serviceCode => $parcelDt) {
                        foreach ($parcelDt as $parcelData) {
                            $invoice_number = $_bagFileData[$bagNumber][$countryCode][$serviceCode]['invoice_number'];
                            $account_number = '';
                            $collection_date = $_bagFileData[$bagNumber][$countryCode][$serviceCode]['pickup_date'];
                            $delivery_country = $_bagFileData[$bagNumber][$countryCode][$serviceCode]['country'];
                            $service_name = $_bagFileData[$bagNumber][$countryCode][$serviceCode]['service_name'];
                            $service_code = $serviceCode;
                            $awb = $parcelData['tracking_number'];
                            $consignmentFilter = new ConsignmentFilter();
                            $consignmentFilter->addJoin('parcel p','c.id = p.consignment_id','inner');
                            $consignmentFilter->addFieldFilter('     p.tracking_number',$awb);
                            $consignmentFilterObjQ = $consignmentFilter->getListNew('c.*');
                            $hawb = '';
                            if(count($consignmentFilterObjQ)) {
                                $hawb = $consignmentFilterObjQ[0]->getHawb();
                            }
                            $agent_reference_number = '';
                            $mawb = '';
                            $weight = (($_bagFileData[$bagNumber][$countryCode][$serviceCode]['item_weight'] > 0) ? ($_bagFileData[$bagNumber][$countryCode][$serviceCode]['item_weight'] / 1000) : 0);
                            $volWeight = '';
                            $length = '';
                            $width = '';
                            $height = '';
                            $totalCharges = $_bagFileData[$bagNumber][$countryCode][$serviceCode]['turnover'];
                            $totalPieces = $_bagFileData[$bagNumber][$countryCode][$serviceCode]['no_of_items'];
                            $number_of_pieces = 1;
                            $basic_charges = ($totalCharges / $totalPieces);
                            $notes = '';
                            $fuel_charges = 0;
                            $vat = 0;
                            $total_charges = $basic_charges;
                            $currency = $_bagFileData[$bagNumber][$countryCode][$serviceCode]['currency'];;
                            $output['invoice_number'] = $invoice_number;
//                            $supplierInvoiceFilter = new SupplierInvoicesFilter();
//                            $supplierInvoiceFilter->where(['si.invoice_number' => $invoice_number]);
//                            $supplierInvoiceFilter->where(['si.account_id' => $user->getUserAccountId()]);
//                            $supplierInvoiceFilterObjs = $supplierInvoiceFilter->getList();
//                            if (count($supplierInvoiceFilterObjs)) {
//                                $output['status'] = 'error';
//                                $output['message'] = 'This file is already processed';
//                                $output['new_invoice_save'] = 1;
//                                break;
//                            }
                            $output['invoice_date'] = $collection_date;
                            $total_amount = $fuel_charges + $total_charges + $vat;
                            $dt = [
                                'account_number' => $account_number,
                                'invoice_number' => $invoice_number,
                                'agent_reference_number' => $agent_reference_number,
                                'collection_date' => $collection_date,
                                'delivery_country' => $delivery_country,
                                'mawb' => $mawb,
                                'awb' => $awb,
                                'hawb' => $hawb,
                                'service_name' => $service_name,
                                'service_code' => $service_code,
                                'weight' => $weight,
                                'vol_weight' => $volWeight,
                                'length' => $length,
                                'width' => $width,
                                'height' => $height,
                                'number_of_pieces' => $number_of_pieces,
                                'basic_charges' => $basic_charges,
                                'fuel_charges' => $fuel_charges,
                                'additional_charges' => '',
                                'vat' => $vat,
                                'total_amount' => $total_amount,
                                'notes' => $notes,
                                'currency' => $currency
                            ];
                            $output['total_weight'] += $weight;
                            $output['total_pieces'] += $number_of_pieces;
                            $output['total_amount'] += $total_amount;
                            $output['currency'] = $currency;
                            $dataParcelArr[] = $dt;
                            $comaSept = implode(",", $dt);
                            $csvParcelStr .= rtrim($comaSept, ',');
                            $csvParcelStr .= "\r\n";
                        }
                    }
                }
            }
        } else {
            $output['status'] = 'error';
            $output['message'] = 'No parcel found in file';
        }

        if($output['status'] != "error" && !$output['return']) {
            $myCsvFile = fopen($new_csv_file_created, "a") or die("Unable to open file!");
            fwrite($myCsvFile, $csvParcelStr);
            fclose($myCsvFile);

            $dateNow = date('Y-m-d H:i:s');
            $load_data_sql = "LOAD DATA LOCAL INFILE '" . $new_csv_file_created . "' INTO TABLE `reconciliation_data`
                            FIELDS ENCLOSED BY '\"' 
                            TERMINATED BY ',' LINES TERMINATED BY '\n' IGNORE 1 LINES (
                                " . $tableColumn . "
                            ) 
                            SET  created_at='" . $dateNow . "', batch_number= '" . $batchNumber . "'";

            $res = DbAccess3::runQueryWithError($load_data_sql);

            if ($res === false) {
                $error = DbAccess3::$dbError;
                $output['status'] = 'error';
                $output['message'] = $error[0];
            } else {
                $sql = "SELECT * FROM reconciliation_data WHERE batch_number='" . $batchNumber . "' ";
                $resultSql = DbAccess3::runQuery($sql);
                $res = [];
                while ($obj = mysqli_fetch_object($resultSql)) {
                    $res[] = $obj;
                }
                $output['status'] = 'success';
                $output['file_path'] = $filePath;
                $output['batch_number'] = $batchNumber;
                $output['template'] = $templateCheck;
                $output['data'] = $res;
            }
        }
        return $output;
    }
    public function bagFileData($data) {
        $serviceLevel = $data['9'];
        $format = $data['10'];
        $cheeckService = $serviceLevel.' '.$format;
        $carrierServiceCode = "";
        if($cheeckService == "Priority Packets") {
            $carrierServiceCode = "GPT";
        }
        $servicesFilter = new ServiceFilter();
        $servicesFilter->addFieldFilter('       carrier_service_code',$carrierServiceCode);
        $servicesFilterObjs = $servicesFilter->getList();
        $serviceName = "";
        if(count($servicesFilterObjs)) {
            $serviceName = $servicesFilterObjs[0]->getName();
        }
        $bagTracking = $data['6'];
        $destination = $data['12'];
        $noOfItems = $data['14'];
        $totalItemWeight = $data['15'];
        $turnover = $data['16'];
        $currency = "GBP";
        if($turnover != "") {
            $arr = explode(' ', $turnover);
            $turnover = (isset($arr[0]) ? $arr[0] : "");
            $currency = (isset($arr[1]) ? $arr[1] : "");
        }
        $itemWeight = $data['17'];
        $pickupDate = $data['5'];
        if($pickupDate != "") {
            $dateArr = explode("/",$pickupDate);
            $newDateFormate =  $dateArr[2].'-'.$dateArr[0].'-'.$dateArr[1];
            $pickupDate = formatDateTime($newDateFormate,'Y-m-d');
        }
        $docDate = $data['4'];
        if($docDate != "") {
            $dateArr = explode("/",$docDate);
            $newDateFormate =  $dateArr[2].'-'.$dateArr[0].'-'.$dateArr[1];
            $docDate = formatDateTime($newDateFormate,'Y-m-d');
        }
        $invoice_number = $data['3'];
        $country_code = $data['12'];
        $country = $data['13'];
        $this->bagFileData[$bagTracking][$destination][$carrierServiceCode] = [
            'invoice_number' => $invoice_number,
            'tracking_number' => $bagTracking,
            'no_of_items' => $noOfItems,
            'total_weight' => $totalItemWeight,
            'turnover' => $turnover,
            'currency' => $currency,
            'item_weight' => $itemWeight,
            'pickup_date' => $pickupDate,
            'doc_date' => $docDate,
            'country_code' => $country_code,
            'country' => $country,
            'service_name' => $serviceName,
            'service_code' => $carrierServiceCode
        ];
        return $this->bagFileData;
    }
    public function parcelFileData($data) {
        $bagNumber = $data['3'];
        $country = $data['9'];
        $serviceCode = str_replace('DEPO_DPI_', '', $data['10']);
        $parcelTracking = $data['2'];
        $parcelReceiveDate = $data['6'];
        $this->parcelFileData[$bagNumber][$country][$serviceCode][] = [
            'tracking_number' => $parcelTracking,
            'received_date' => $parcelReceiveDate
        ];
        return $this->parcelFileData;
    }
    public function getSummeryData($relPath){
        $returnArr = [];
        $row = 0;
        $isInvoiceType = false;
        $isDataSet = true;
        if (($handle = fopen($relPath, "r")) !== FALSE) {
            while (($data = fgetcsv($handle)) !== FALSE) {
                if($row > 0) {
                    $collectionDate = $data['5'];
                    $returnArr['invoice_number'] = $data['3'];
                    if($collectionDate != "") {
                        $dateArr = explode("/",$collectionDate);
                        $newDateFormate =  $dateArr[2].'-'.$dateArr[0].'-'.$dateArr[1];
                        $collectionDate = formatDateTime($newDateFormate,'Y-m-d');
                    }
                    $returnArr['collection_date'] = $collectionDate;
                    $isInvoiceType = true;
                }
                if($isInvoiceType){
                    break;
                }
                $row++;
            }
        }
        return $returnArr;
    }
}
