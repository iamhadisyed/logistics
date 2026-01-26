<?php

include_classes([
    'carrierdatafilelog.class',
    'carrierdatafilelogfilter.class'
]);

class FastWay implements CarrierService {

    private $pdf;
    private $serviceValues = null;
    private $constants = null;
    private $user = null;
    private $userAccount = null;
    private $country = null;
    private $record_array = null;

    public function __construct($debug = false) {
        if ($debug) {
            $this->apiUrl = "https://sandbox.scurri.co.uk/api/v1/company/";
        } else {
            $this->apiUrl = "https://scurri.co.uk/api/v1/company/";
        }
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

        if (trim(@$this->constants['INTEGRATION_TYPE']) == 'API') {
            if (trim(@$this->constants['FASTWAY_ACCOUNT']) == '' || trim(@$this->constants['FASTWAY_USERNAME']) == '' || trim(@$this->constants['FASTWAY_PASSWORD']) == '') {
                $output['STATUS'] = 'ERROR';
                $output['MESSAGE'] = "Please check service constants. Please contact to itsupport@oneworldexpress.com";
                return $output;
            }
            $output = $this->apiLabel($this->constants, $consignment);
        } else {
            $output = $this->ediLabel($consignment, $labelType, $size);
        }
        return $output;
    }

    public function apiLabel($constantsArray, Consignment $consignmentObject) {
        $this->apiAccount = trim(@$this->constants['FASTWAY_ACCOUNT']); //"One World Express Inc Ltd";
        $this->apiUsername = trim(@$this->constants['FASTWAY_USERNAME']); //"One World Express Inc Ltd (6295318410), DPI customer";
        $this->apiPassword = trim(@$this->constants['FASTWAY_PASSWORD']); //'cs@oneworldexpress.com';
        $parcel_list = $consignmentObject->getParcels();
        if (count($parcel_list) > 1) {
            $output["STATUS"] = 'ERROR';
            $output["MESSAGE"] = 'This service support single piece shipment';
        } else {
            $serviceDataObject = new Services($consignmentObject->getServiceId());
            $countryDataObject = new Country($consignmentObject->getCountryId());

            $consignmentArray = [];
            $consignmentArray['carrier'] = "Fastway";
            $consignmentArray['warehouse_id'] = "rapidx-logistics|RAPIDX LOGISTICS";
            switch($serviceDataObject->getCode()){
                case "STFST0KYL":
                    $consignmentArray['service_id'] = "Fastway|KY - Local Parcel KY"; //"Fastway|NY - National Satchel NY";
                break;
                case "STFST0WYN":
                    $consignmentArray['service_id'] = "Fastway|WY - National Parcel WY"; //"Fastway|NY - National Satchel NY";
                break;
            }
            
            
            $consignmentArray['identifier'] = $consignmentObject->getHawb();
            foreach ($parcel_list as $parcelKey => $parcelItems) {
                $getDescription = (array) json_decode($parcelItems->getDescription());
                $getQty = (array) json_decode($parcelItems->getQty());
                $getItemsku = (array) json_decode($parcelItems->getItemsku());
                $getCommodityCode = (array) json_decode($parcelItems->getCommodityCode());
                $getHsCode = (array) json_decode($parcelItems->getHsCode());
                $getValue = (array) json_decode($parcelItems->getItemValue());
                $getPWeight = (array) json_decode($parcelItems->getPWeight());
                if (trim($parcelItems->getDescription()) != '' && count($getDescription) > 0) {
                    foreach ($getDescription as $descKey => $descValue) {
                        $consignmentArray['packages'][$parcelKey]["items"][] = [
                            "weight" => $getPWeight[$descKey],
                            "value" => $getValue[$descKey],
                            "fabric_content" => $descValue,
                            "harmonisation_code" => $getHsCode[$descKey],
                            "country_of_origin" => $getCommodityCode[$descKey],
                            "sku" => $getItemsku[$descKey],
                            "quantity" => $getQty[$descKey],
                            "name" => $descValue
                        ];
                    }
                } else {

                    $consignmentArray['packages'][$parcelKey]["items"][] = [
                        "weight" => $parcelItems->getWeight(),
                        "value" => $parcelItems->getValue(),
                        "fabric_content" => $parcelItems->getDescription(),
                        "harmonisation_code" => $parcelItems->getHsCode(),
                        "country_of_origin" => $parcelItems->getCommodityCode(),
                        "sku" => $parcelItems->getItemsku(),
                        "quantity" => $parcelItems->getQty(),
                        "name" => $parcelItems->getDescription()
                    ];
                }

                $consignmentArray['packages'][$parcelKey]["length"] = $parcelItems->getLength();
                $consignmentArray['packages'][$parcelKey]["height"] = $parcelItems->getHeight();
                $consignmentArray['packages'][$parcelKey]["width"] = $parcelItems->getWidth();
                $consignmentArray['packages'][$parcelKey]["reference"] = $consignmentObject->getReference();
            }

            $consignmentArray['recipient']['address'] = ["country" => $countryDataObject->getIso(),
                "postcode" => $consignmentObject->getPostcode(),
                "city" => $consignmentObject->getCity(),
                "address2" => $consignmentObject->getAddressLine2(),
                "address1" => $consignmentObject->getAddressLine1(),
                "state" => $consignmentObject->getState()
            ];
            $consignmentArray['recipient']['contact_number'] = $consignmentObject->getTelephone();
            $consignmentArray['recipient']['email_address'] = $consignmentObject->getEmail();
            $consignmentArray['recipient']['company_name'] = $consignmentObject->getCompany();
            $consignmentArray['recipient']['name'] = $consignmentObject->getContact();
            $consignmentArray['order_number'] = $consignmentObject->getHawb();
            $consignmentArray['options']['package_type'] = "Parcel";
            $consignmentArray['options']['signed'] = "yes";
            $consignmentJson = json_encode([$consignmentArray]);
         
            $consigmentSubmitResponse = $this->fastwaySubmitConsignmentData($consignmentJson);
            $consigmentSubmitArray = json_decode($consigmentSubmitResponse);
            
            $errorObj    = (array)$consigmentSubmitArray->errors;
            if (        isset($consigmentSubmitArray->errors) 
                    && !empty($errorObj)
                    && count($consigmentSubmitArray->errors)>0) {
                foreach($consigmentSubmitArray->errors as $errorDataItem){
                    $errorItem = $errorDataItem;
                }
                $output["STATUS"] = 'ERROR';
                $output["MESSAGE"] = implode(", ", $errorItem );
                echo "sdfads";
                die;
            } else {
                foreach($consigmentSubmitArray->success as $consignmentHawb )
                    $consignmentNumber = $consignmentHawb;
                //$conignmentStatus   =   $this->fastwayConsignmentStatus($consignmentNumber);
                $conignmentLabelResponse = $this->fastwayLabel($consignmentNumber);
                $consigmentSubmitArray = (array) json_decode($conignmentLabelResponse);
               
                if (trim($consigmentSubmitArray["labels"]) != '' || $consigmentSubmitArray["labels"] != 'null') {
                    $labelPdf = trim($consigmentSubmitArray["labels"]);
                }
                $conignmentStatusResponse =  json_decode($this->fastwayConsignmentStatus($consignmentNumber));
                if (trim($conignmentStatusResponse->current_status->rejection_reason) != '' && trim($conignmentStatusResponse->current_status->rejection_reason) != 'null') {
                    $output["STATUS"] = 'ERROR';
                    $output["MESSAGE"] = trim($conignmentStatusResponse->current_status->rejection_reason);
                } else {

                    $fileName = SETTING_DIR_ASSETS . "pdf/" . date('Y_m_d') . '/' . $consignmentObject->getId() . ".pdf";
                    $labelDataFile = base64_decode($labelPdf);
                    $awb = $conignmentStatusResponse->consignment_number ;
                    file_put_contents($fileName, $labelDataFile);
                    $consignmentObject->save();

                    $output["STATUS"] = 'SUCCESS';
                    $output["LABEL"] = date('Y_m_d') . '/' . $consignmentObject->getId() . ".pdf";
                    $output["TRACKING_NUMBER"][] = $awb;
                }
            }
        }

        return $output;
    }

    private function fastwaySubmitConsignmentData($consignmentJson = []) {
        $curl = curl_init();
      
        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->apiUrl . $this->apiAccount . '/consignments/',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $consignmentJson,
            CURLOPT_HTTPHEADER => array(
                'Authorization: Basic ' . base64_encode($this->apiUsername . ":" . $this->apiPassword),
                'Content-Type: application/json'
            ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);
        return $response;
    }

    private function fastwayConsignmentStatus($hawbNumber) {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->apiUrl . $this->apiAccount . '/consignment/' . $hawbNumber . "/",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'Authorization: Basic ' . base64_encode($this->apiUsername . ":" . $this->apiPassword),
                'Content-Type: application/json'
            ),
        ));
        $response = curl_exec($curl);
        curl_close($curl);
        return $response;
    }

    private function fastwayLabel($hawbNumber) {

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->apiUrl . $this->apiAccount . '/consignment/' . $hawbNumber . "/documents/?documenttype=application/pdf&label_quantity=1&invoice_quantity=3",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'Authorization: Basic ' . base64_encode($this->apiUsername . ":" . $this->apiPassword),
                'Content-Type: application/json'
            ),
        ));
        $response = curl_exec($curl);
        curl_close($curl);
        return $response;
    }

    //$arrayBarcode contain HAWB
    public function orderDispatchData($constants, $arrayBarcode = array()) {
        if (count($arrayBarcode) <= 0) {
            $output["STATUS"] = 'ERROR';
            $output["MESSAGE"] = 'Tracking Number cannot be blank.';
        }

        $this->apiAccount = trim(@$this->constants['FASTWAY_ACCOUNT']); //"One World Express Inc Ltd";
        $this->apiUsername = trim(@$this->constants['FASTWAY_USERNAME']); //"One World Express Inc Ltd (6295318410), DPI customer";
        $this->apiPassword = trim(@$this->constants['FASTWAY_PASSWORD']); //'cs@oneworldexpress.com';

        $request1 = json_encode([
            "consignment_ids" => $arrayBarcode,
            "carrier_id" => "fastway",
            "warehouse_id" => "rapidx-logistics|RAPIDX LOGISTICS"
        ]);

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->apiUrl . $this->apiAccount . '/manifest/',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $request1,
            CURLOPT_HTTPHEADER => array(
                'Authorization: Basic ' . base64_encode($this->apiUsername . ":" . $this->apiPassword),
                'Content-Type: application/json'
            ),
        ));

        $response = curl_exec($curl);
//identifier
        curl_close($curl);
        $orderResponse = (array) json_decode($response);
        if (isset($orderResponse["identifier"]) && trim(@$orderResponse["identifier"]) != '') {
            $output["STATUS"] = 'SUCCESS';
            $output["DATA"] = array('orderId' => $orderResponse["identifier"], 'awb' => $orderResponse["identifier"]);
        } else {
            $output["STATUS"] = 'ERROR';
            $output["MESSAGE"] = $orderResponse["detail"];
        }
        return $output;
    }

    public function ediLabel($consignment, $labelType = 'pdf', $size = '100x150') {

        $output = array();
        $this->user = SessionManager::getUser();
        $this->userAccount = new CustomerAccount($this->user->getUserAccountId());
        $this->serviceValues = new Services($consignment->getServiceId());
        $this->country = new Country($consignment->getCountryId());
        /*
         *  Get Tracking Number ranges
         */


        $serviceRangeMappingFilter = new ServiceRangeMappingFilter();
        $serviceRangeMappingFilter->addFilter("service_id = '" . $consignment->getServiceId() . "' AND agent_id = '" . $consignment->getAgentId() . "' ");
        $serviceRange = $serviceRangeMappingFilter->getList();
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
                    if ($this->serviceValues->getCode() == "STRPD0STD") {
                        $range = sprintf("%010d", $resultArray["RANGE"]);
                    } else {
                        $range = $resultArray["RANGE"];
                    }
                    $licence_plate = $resultArray["PREFIX"] . $range . $resultArray["SUFIX"];
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

            if ($this->serviceValues->getCode() == "STRPD0STD") {
                $this->addRapidCourier($consignment, $parcel_idx, $licence_plate);
            } else
                $this->addWayBill($consignment, $parcel_idx, $licence_plate);

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
        $tracking = new Tracking();
        require_once("../includes/labels/fastwaytrackingstatus.class.php");

        $irelandtracking = $trackingNumber;
        $url = "http://api.fastway.org/latest/tracktrace/detail/$irelandtracking?api_key=136fcf013c0555280c5cad83922f84e8";

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $trackingNumber);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($ch);
        $result = json_decode($response);
        $response = $result->result->Scans;

        $entityId = 0;
        if ($trackBy == 'parcel') {
            $parcelObj = new ParcelFilter();
            $parcelObj->addTrackingNumberFilter($trackingNumber);
            $parcelDataArray = $parcelObj->getColumnList('p.id, p.tracking_number, p.consignment_id');
            if (count($parcelDataArray) > 0) {
                $parcelData = $parcelDataArray[0];
                $entityId = $parcelData->getId();
            }
        } else if ($trackBy == 'shipment') {
            $shipmenObj = new ConsignmentFilter();
            $shipmenObj->addawbFilter($trackingNumber);
            $shipmentDataArray = $shipmenObj->getColumnList('c.awb');
            if (count($shipmentDataArray) > 0) {
                $shipmentData = $shipmentDataArray[0];
                $entityId = $shipmentData->getId();
            }
        }

        ////////////////////// Carrier Received ////////////////////////////////

        $trackingDataFilterObj = new TrackingDataFilter();
        $trackingDataFilterObj->addFieldFilter('    tracking_number', $trackingNumber);
        $trackingDataFilterObj->addFilter("carrier_code not in ('','1')");
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

        if (!empty($response) > 0) {
            $parcelEntity = new Parcel($entityId);
            $finalStatusCode = $parcelEntity->getParcelStatusCode();

            foreach ($response as $event) {
                $DateTime = $event->RealDateTime;
                $EventCode = $event->Status;
                $EventDescription = $event->StatusDescription;
                $ServiceAreaDescription = $event->Name;

                $Signatory = '';
                //$spTrackingStatus = FastwayTrackingStatus::getOweStatusCode($EventCode);
                // Dont enter any other event code if it is against 148 Event Code
                if ($carrierCodeCarrierReceived == $EventCode) {
                    continue;
                }

                $deliveredArray = array('3', '4', '6', 'AGT', 'ATL', 'HDN', 'NEI', '001', 'PAP', 'PRS', 'U26', 'U27', 'U49', 'U50', 'U51', 'YES', 'CLS');

                ////////////////////// Carrier Received ////////////////////////////////
                if ($carrierReceivedCheck == 1) {
                    $spTrackingStatus = '148';
                    $carrierReceivedCheck = 0;
                } else {
                    $spTrackingStatus = FastwayTrackingStatus::getOweStatusCode($EventCode);
                }
                ////////////////////// Carrier Received ////////////////////////////////

                $result = $tracking->saveTrackPoint($entityId, $trackBy, $DateTime, $trackingNumber, $spTrackingStatus, $ServiceAreaDescription, $EventCode, $EventDescription, $deliveredArray, $finalStatusCode);
                if ($result == true) {
                    break;
                }
            }
            $tracking->saveConsignmentTrackingStatus($trackingNumber, 'FastwayTrackingStatus');
        }
    }

    //}

    public function sendData($tracking_numbers = array()) {
        $output = [];
        $agentServiceArray = [];
        $consignmentData = new ConsignmentFilter();
        $consignmentData->addFilter("     s.carrier_id in ('159', '199')", "servicefilter");
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
                     con.region 'country_region', c.service_id, c.weight, c.hawb, c.description, c.company, c.country_id,c.awb,c.date_created,c.value,
                      c.contact, c.address_line_1, c.address_line_2, c.city, c.postcode, c.telephone, c.other_routing_code, routing_code_eur ");

                    if (count($consignmentShipmentData) > 0) {
                        $consignmentIdArray = [];
                        foreach ($consignmentShipmentData as $consignmentItemData) {

                            $consignmentId = $consignmentItemData->getConsignmentId();
                            $consignmentIdArray[] = $consignmentId;
                            $carrierId = $consignmentItemData->getCarrierId();
                            $this->record_array[] = $this->getShipmentRecord($consignmentItemData);
                        }
                        $carrierId = $this->serviceValues->getCarrierId();
                        $agentid = trim($agentServiceArray['agent'][$key]);
                        $run_number = CarrierDataFileLog::generateRunNumber($carrierId, $agentid);
                        $accountName = $this->constants[$serviceid]["ACCOUNT_NAME"];
                        $this->fastway_file = $fastway_file = $accountName . "-manifest-" . $run_number; //OneWorld

                        $carrierDataFileLog = new CarrierDataFileLog();
                        $carrierDataFileLog->setCarrierId($carrierId);
                        $carrierDataFileLog->setAgentId($agentid);
                        $carrierDataFileLog->setFileName($fastway_file);
                        $carrierDataFileLog->setRunNumber($run_number);
                        $carrierDataFileLog->save();

                        if ($this->sendBookings($this->constants[$serviceid])) {
                            if (!empty($consignmentIdArray)) {

                                $sql = "UPDATE consignment SET send_courier_data = 1,  booked_file_id = '" . $fastway_file . "' 
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

        $image = User::getUserCompanyImages(false, $consignment->getUserId());

        $this->pdf->image($image, 3, 1, 50);

        $this->pdf->setFont("helvetica", "B", 22);
        $this->pdf->Text(60, 8, "Next Day");

        $this->pdf->setFont("helvetica", "B", 11);
        $this->pdf->Text(3, 89, "Proof of Delivery");
        $this->pdf->setFont("helvetica", "B", 10);
        $this->pdf->line(3, 95, 98, 95);
        $this->pdf->line(3, 95, 3, 115);
        $this->pdf->line(3, 115, 98, 115);
        $this->pdf->line(98, 95, 98, 115);
        $this->pdf->Text(5, 97, "Print Name :");
        $this->pdf->Text(5, 103, "Signature :");
        $this->pdf->Text(5, 109, "Date :");

        //$this->pdf->setFont("helvetica", "L", 9);
        //$this->pdf->Text(10, 18.5, "www.oneworldexpress.com");


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
        $this->pdf->write1DBarcode($licence_plate, 'C128', 15, 25, '', 20, .55, $style, '');

        $this->pdf->setFont("helvetica", "B", 10);
        $this->pdf->Text(35, 45, $licence_plate);
        $this->pdf->line(3, 50, 98, 50);
        $this->pdf->line(3, 50, 3, 85);
        $this->pdf->line(3, 85, 98, 85);
        $this->pdf->line(98, 50, 98, 85);


        $address = "";
        $company = "";
        $address2 = "";
        $address3 = "";
        if ($consignment->getCompany() != "")
            $company = $consignment->getCompany();
        if ($consignment->getAddressLine1() != "")
            $address1 = $consignment->getAddressLine1();
        if ($consignment->getAddressLine2() != "")
            $address2 = $consignment->getAddressLine2();
        if ($consignment->getAddressLine3() != "")
            $address3 = $consignment->getAddressLine3();


        $this->pdf->setFont("helvetica", "L", 9);
        $this->pdf->Text(55, 51, "Ref No : " . $consignment->getHawb());
        $this->pdf->Text(5, 51, $company);
        $this->pdf->Text(5, 55, $consignment->getContact());
        $this->pdf->Text(5, 59, $address1);
        $this->pdf->Text(5, 63, $address2);
        $this->pdf->Text(5, 67, $address3);
        $this->pdf->Text(5, 71, $consignment->getCity());
        $this->pdf->Text(5, 75, $consignment->getPostcode());
        $this->pdf->Text(5, 79, $this->country->getName());
        $this->pdf->Text(55, 79, "Tel No : " . $consignment->getTelephone());

        $this->pdf->setFont("helvetica", "B", 12);

        $booking_date = date("d/m/Y");
        $this->pdf->Text(3, 120, $booking_date);
        $this->pdf->Text(3, 125, "Weight " . $consignment->getWeight() . " KGS");
        $this->pdf->Text(3, 130, "ITEM 1 OF 1");

        $this->pdf->setFont("helvetica", "B", 20);
        $this->pdf->Text(3, 135, "IRE");

        $datamatrix_data = $consignment->getCompany() . "|" . $consignment->getAddressLine1() . "|" . $consignment->getAddressLine2() . "|" . $consignment->getAddressLine3() . "|" . $consignment->getCity() . "||" . $consignment->getPostcode() . "|" . $consignment->getTelephone() . "||" . $consignment->getContact();

        $style = array(
            'border' => false,
            'vpadding' => 'auto',
            'hpadding' => 'auto',
            'fgcolor' => array(0, 0, 0),
            'bgcolor' => false, //array(255,255,255)
            'module_width' => 1, // width of a single module in points
            'module_height' => 1 // height of a single module in points
        );


        $this->pdf->write2DBarcode($datamatrix_data, 'DATAMATRIX', 65, 120, 50, 50, $style, 'N');
    }

    private function addRapidCourier(Consignment $consignment, $parcel_idx, $licence_plate) {

        $this->pdf->line(1, 1, 98, 1);
        $this->pdf->line(1, 1, 1, 147);
        $this->pdf->line(1, 147, 98, 147);
        $this->pdf->line(98, 1, 98, 147);

        $this->pdf->line(1, 25, 98, 25);
        $this->pdf->line(1, 65, 98, 65);
        $this->pdf->line(1, 90, 98, 90);



        $image = "../images/RapidX.jpg";

        $this->pdf->image($image, 3, 4, 50, 20);

        $address = "";
        $company = "";
        $address2 = "";
        $address3 = "";
        if ($consignment->getCompany() != "")
            $company = $consignment->getCompany();
        if ($consignment->getAddressLine1() != "")
            $address1 = $consignment->getAddressLine1();
        if ($consignment->getAddressLine2() != "")
            $address2 = $consignment->getAddressLine2();
        if ($consignment->getAddressLine3() != "")
            $address3 = $consignment->getAddressLine3();


        $this->pdf->setFont("Arial", "", 11);
        //=$this->pdf->Text(55, 51, "Ref No : " . $consignment->getHawb());
        $this->pdf->Text(5, 26, $company);
        $this->pdf->Text(5, 30, $consignment->getContact());
        $this->pdf->Text(5, 34, $address1);
        $this->pdf->Text(5, 38, $address2);
        $this->pdf->Text(5, 42, $address3);
        $this->pdf->Text(5, 46, $consignment->getCity());
        $this->pdf->Text(5, 50, $consignment->getPostcode());
        $this->pdf->Text(5, 54, $this->country->getName());
        $this->pdf->Text(5, 58, "Tel No : " . $consignment->getTelephone());

        $this->pdf->setFont("Arial", "B", 9);
        $senderCountry = new Country($consignment->getSenderCountryId());
        $this->pdf->Text(5, 68, "From:");
        $this->pdf->Text(5, 72, $consignment->getSenderName());
        $this->pdf->Text(5, 76, $consignment->getSenderAddressLine1());
        $this->pdf->Text(5, 80, $consignment->getSenderCity() . ", " . $senderCountry->getName());
        $this->pdf->Text(5, 84, $consignment->getSenderPostcode());

        $pindex = $parcel_idx + 1;
        $this->pdf->Text(5, 92, "Item:" . $pindex . " of " . $consignment->getNumberPieces());
        $this->pdf->Text(30, 92, "Weight:" . number_format($consignment->getWeight(), 2) . " KG");
        $this->pdf->Text(60, 92, "Print Date:" . date("d/m/Y"));


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
            'text' => true,
            'font' => 'helvetica',
            'fontsize' => 8,
            'stretchtext' => 1
        );
        $this->pdf->write1DBarcode($licence_plate, 'C128', 15, 105, '', 25, .55, $style, '');
    }

    private function getShipmentRecord(Consignment $consignment) {

        $this->country = new Country($consignment->getCountryId());

        if ($this->country->getIso() == "GB" && strtoupper($consignment->getCity()) == "LONDON") {
            $countrycode = "UK";
        } else if ($this->country->getIso() == "GB")
            $countrycode = "UKL";
        else if (strtoupper($this->country->getName()) == "UNITED STATES")
            $countrycode = "USA";
        else
            $countrycode = $this->country->getName();


        $weight = $consignment->getWeight();
        if ($weight <= 0)
            $weight = 0.5;

        $hawb = trim($consignment->getHawb());
        $awb = trim($consignment->getAwb());

        $record = "<Connote>
                        <OrderNumber>" . $hawb . "</OrderNumber>
                        <MasterIdentifier>" . $awb . "</MasterIdentifier>
                        <CarrierIdentifier>" . $awb . "</CarrierIdentifier>
                        <CreationDate>" . date("Y-m-d", strtotime($consignment->getDateCreated())) . "</CreationDate>
                        <ConsigneeAddress>
                            <Name1>" . $consignment->getContact() . "</Name1>
                            <Address1>" . $consignment->getAddressLine1() . "</Address1>
                            <Address2>" . $consignment->getAddressLine2() . "</Address2>
                            <Address3>" . $consignment->getAddressLine3() . "</Address3>
                            <Address4>" . $consignment->getCity() . "</Address4>
                            <PostCode>" . $consignment->getPostcode() . "</PostCode>
                            <CountryCode>" . $this->country->getIso() . "</CountryCode>                            
                        </ConsigneeAddress>
                        <ContactDetails>
                            <PhoneNumber1>" . $consignment->getTelephone() . "</PhoneNumber1>
                            <MobilePhone>" . $consignment->getTelephone() . "</MobilePhone>
                            <EmailAddress>" . $consignment->getEmail() . "</EmailAddress>
                        </ContactDetails>
                        <ConsignmentDetails>
                            <ItemDescription>" . $consignment->getDescription() . "</ItemDescription>
                            <Weight>" . $consignment->getWeight() . "</Weight>
                            <Width/>
                            <Length/>
                            <Height/>
                            <Value>" . $consignment->getValue() . "</Value>     
                        </ConsignmentDetails>
                        <EventDetails>
                            <Status/>
                            <ScanDateTime/>
                        </EventDetails>
                    </Connote>";

        $record .= "\r\n";
        // echo $record; die;
        return $record;
    }

    public function sendBookings($ftpConstants) {

        if (sizeof($this->record_array) > 0) {
            $linkFile = $this->fastway_file;
            $path = SETTING_DIR_ASSETS . "data_send/fastway_booking/" . date("Y_m_d") . "/";
            if (!file_exists($path))
                @mkdir($path, 0777, TRUE);

            $file_path = $path . $linkFile;
            chmod($path, 0777);

            // create file
            $file_handle = @fopen($file_path, 'w');
            fwrite($file_handle, "<?xml version='1.0'?> <Manifest>");
            foreach ($this->record_array as $record) {
                fwrite($file_handle, $record);
            }
            fwrite($file_handle, "</Manifest>");
            // close file
            fclose($file_handle);


            if (isset($ftpConstants['FASTWAY_FTP_SITE']) && trim($ftpConstants['FASTWAY_FTP_SITE']) != '') {
                $SETTING_FTP_USER_FASTWAY = $ftpConstants['FASTWAY_FTP_USER'];
                $SETTING_FTP_PASSWORD_FASTWAY = $ftpConstants['FASTWAY_FTP_PASSWORD'];
                $SETTING_FTP_SITE_FASTWAY = $ftpConstants['FASTWAY_FTP_SITE'];


                $ftp_object1 = new FTPfile($SETTING_FTP_USER_FASTWAY, $SETTING_FTP_PASSWORD_FASTWAY, $SETTING_FTP_SITE_FASTWAY);
                $remote_file_path1 = "./ManifeststoProcess/" . $this->fastway_file . ".xml";

                $ftp_object1->passive();
                if (!$ftp_object1->put($remote_file_path1, $file_path, FTP_ASCII)) {
                    mail("itsupport@oneworldexpress.com", "FASTWAY LOGIN FAILED", "FASTWAY LOGIN FAILED" . $this->fastway_file);
                }
                $this->link_file = NULL;
                $this->record_array = NULL;
                return true;
            } else {
                return false;
            }
        }
    }

    public function recycledShipment($consignment) {
        $output["STATUS"] = "SUCCESS";
        return $output;
    }

}
