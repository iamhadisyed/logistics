<?php

class EasySent implements CarrierService {

    private $pdf;
    private $serviceValues = null;
    private $agentValues = null;
    private $constants = null;
    private $user = null;
    private $country = null;

    public function __construct() {
        
    }

    public function validation(Consignment $consignment, Services $service, Country $country) {
            $itemDetails = $consignment->getItems();
            if(count($itemDetails) > 0){
                $errorMessage = true;
                foreach($itemDetails as $item){
                    $errorMessage = false;
                    $itemQty = $item["no_of_items"];
                    $itemDesc = $item["item_description"];
                    $itemValue = $item["item_value"];
                    $itemHscode = $item["hscode"];
                    $itemSku = $item["item_sku"];
                    $itemManufactureCountry = $item["manufacture_country_iso"];
                    if(trim($itemQty) <= 0 || trim($itemDesc) == "" || trim($itemValue) <= 0 || trim($itemHscode) == "" || trim($itemManufactureCountry) == "" ){
                        $errorMessage = true;
                    }
                    if(($service->getCode() == "STESYHERT" || $service->getCode() == "STESYBPST") && $itemSku == ""){
                        $errorMessage = true;
                    }
                }
                if($errorMessage){
                    $returnOutput[] = "Please provide itemized details for each item in parcels like value , qty, hscode and other required fields.";
                }
            }
            else
            {
                $returnOutput[] = "Please provide itemized details for each item in parcels like value , qty, hscode and other required fields.";
            }
            
            if(trim($consignment->getCompany()) == "" && $service->getCode() == "STESYDHLX"){
                $returnOutput[] = "Please enter company name.";
            }
            return $returnOutput;
    }

    public function remoteareas($consignment, $carrierObject, $sender) {
        
    }

    public function label($consignment, $labelType = 'pdf', $size = '') {
        
        $output = array();
        $this->serviceValues = new Services($consignment->getServiceId());
        $this->user = SessionManager::getUser();
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
        if ((trim(@$this->constants['EASYSENT_NAMESPACE']) == '' || trim(@$this->constants['EASYSENT_APISECRET']) == '' || trim(@$this->constants['EASYSENT_APIKEY']) == '')) {
          $output['STATUS'] = 'ERROR';
          $output['MESSAGE'] = "Please check service constants. Please contact to itsupport@oneworldexpress.com";
          return $output;
          }

        $output = $this->apiLabel($this->constants, $consignment);

        return $output;
    }

    private function apiLabel($constants, Consignment $consignment) {
        $parcel_list = $consignment->getParcels();
        $packageListArray = array();
        if(count($parcel_list) > 0){
            foreach($parcel_list as $parcel){
            $parcelDescription = json_decode($parcel->getDescription());
            $parcelCountry = json_decode($parcel->getCommodityCode());
            $parcelQty = json_decode($parcel->getQty());
            $parcelValue = json_decode($parcel->getItemValue());
            $parcelHscode = json_decode($parcel->getHsCode());
            $parcelSku = json_decode($parcel->getItemSku());
            $parcelWeight = json_decode($parcel->getPWeight());  
            $itemArray = array();
            if(count($parcelDescription) > 0){
                    foreach($parcelDescription as $key=>$desc){
                     $itemArray[] =   array(
                                    'productName' => $desc,
                                    'productNameEn' => $desc,
                                    'productSku' => $parcelSku[$key],
                                    'hsCode' => $parcelHscode[$key],
                                    'quantity' => $parcelQty[$key],
                                    'unitPrice' => number_format($parcelValue[$key],2),
                                    'unitWeight' => number_format($parcelWeight[$key],2)
                                ); 
                    }
            }
            $packageList[] = array(
                            'packageRecord' =>
                            array(
                                //'sonOrderNumber' => '',
                                //'boxNumber' => '',
                                'weight' => number_format($parcel->getWeight(), 2),
                                'length' => number_format($parcel->getLength(), 2),
                                'width' =>  number_format($parcel->getWidth(), 2),
                                'height' => number_format($parcel->getHeight(), 2),
                            ),
                            'itemList' =>$itemArray,
                        );
            }
            $packageListArray = $packageList;
        }
        $createShipmentArray = array();
        $senderCountry = new Country($consignment->getSenderCountryId());
        $createShipmentArray["instructionList"] = array(
                        //'fbaCode' => '1006',
                        'channelCode' => $this->serviceValues->getCarrierServiceCode(), //ES_02 ,  DhlEconomySelect
                        'userOrderNumber' => $consignment->getHawb() ,
                        'declaredValue' => number_format($consignment->getValue(),2),
                        'remark' => $consignment->getNotes(),
                        'sender' =>
                        array(
                            'company' =>"One World Express",
                            'contactName' => $consignment->getSenderName(),
                            'telephone' => $consignment->getSenderTelePhone(),
                            'countryCode' => $senderCountry->getIso(),
                            'state' => $consignment->getSenderState(),
                            'city' => $consignment->getSenderCity(),
                            'street' => $consignment->getSenderAddressLine1(),
                            'street1' => $consignment->getSenderAddressLine2(),
                            'street2' => $consignment->getSenderAddressLine3(),
                            'county' => '',
                            'zipCode' => $consignment->getSenderPostcode(),
                            'zip4' => '',
                        ),
                        'recipient' =>
                        array(
                            'company' => $consignment->getCompany(),
                            'contactName' => $consignment->getContact(),
                            'telephone' => $consignment->getTelephone(),
                            'countryCode' => $this->country->getIso(),
                            'state' => $consignment->getState(),
                            'city' => $consignment->getCity(),
                            'street' => $consignment->getAddressLine1(),
                            'street1' =>$consignment->getAddressLine2(),
                            'street2' => $consignment->getAddressLine3(),
                            'county' => '',
                            'zipCode' => $consignment->getPostcode(),
                            'zip4' => '',
                        ),
                'packageDetailList' => $packageListArray
            
        );
        $namespace = "http://b.flux.easysent.com:8133/wgs/v1/internationalexpress/".$constants['EASYSENT_NAMESPACE'];
        $responseArray = $this->curlRequest($createShipmentArray, $namespace);
        $consignment->setApiData(print_r($createShipmentArray, true), print_r($responseArray, true), $constants['EASYSENT_NAMESPACE']);
        if(strtoupper($responseArray->errorMessage) == "SUCCESS"){
            $errorCode = $responseArray->instructionList[0]->errorCode;
            if($errorCode > 0){
                $output["STATUS"] = "ERROR";
                $output["MESSAGE"] = $responseArray->instructionList[0]->errorMessage;
                //print_r($output);
                return $output;
            }
            else
            {
                $instructionList = $responseArray->instructionList;
                foreach($instructionList as $response){
                    
                    $fastLabelPath = $response->fastLabelPath;
                    $mainTrackingNumber = $response->mainTrackingNumber;
                    $trackingNumber = $response->shipments->trackingNumber;
                    $userOrderNumber = $response->userOrderNumber;
                    $instructionNumber = $response->instructionNumber;
                    // For Collisiomo Service only where label come in same request
                    if($fastLabelPath != '' && $this->serviceValues->getCode() == "STESYCOFR"){
                        $licence_plate_array[] = $mainTrackingNumber;
                        $parcel_list[0]->setTrackingNumber($tracking_number);
                        $parcel_list[0]->save();
                        $base64Decode = base64_decode($fastLabelPath);
                        $fileName = '../_assets/pdf/' . date('Y_m_d') . '/' . $consignment->getId() . ".pdf";
                        $fp = fopen($fileName, 'wb+');
                        fwrite($fp, $base64Decode);
                        fclose($fp);
                        $output['STATUS'] = 'SUCCESS';
                        $output['LABEL'] = date('Y_m_d') . '/' . $consignment->getId() . ".pdf";
                        $output['TRACKING_NUMBER'] = $licence_plate_array;
                        return $output;
                    }
                    // when label does not come with create shipment request then call for label request
                    $labelRequest["instructionList"] = array (
                                                            array (
                                                              'userOrderNumber' => $userOrderNumber,
                                                              'instructionNumber' => $instructionNumber,
                                                            ),
                                                          );
                    sleep(20);
                    $labelResponseArray = $this->curlRequest($labelRequest, "http://b.flux.easysent.com:8133/wgs/v1/internationalexpress/exprssShipmentTrackingNumbers");
                    $labelInstructionList = $labelResponseArray->instructionList;
                    $consignment->setApiData(print_r($labelRequest, true), print_r($labelResponseArray, true), "fetchLabel");
                    if(count($labelInstructionList) > 0){
                        foreach($labelInstructionList as $linstruction){
                            $errorCode = $linstruction->errorCode;
                            $failReason = $linstruction->failReason;
                            if($failReason != ''){
                                $output["STATUS"] = "ERROR";
                                $output["MESSAGE"] = $failReason;
                                return $output;
                            }
                            $shipmentDetails = $linstruction->shipments;
                            foreach($shipmentDetails as $sDetail){
                            $trackingNumber = $sDetail->trackingNumber;
                                $labelUrl = $sDetail->labelUrls[0];
                                
                                if($labelUrl != ''){
                                    $licence_plate_array[] = $trackingNumber;
                                    $parcel_list[0]->setTrackingNumber($trackingNumber);
                                    $parcel_list[0]->save();
                                    if (strpos($labelUrl, 'png') !== false || strpos($labelUrl, 'PNG') !== false) {
                                        $fileName = '../_assets/pdf/' . date('Y_m_d') . '/' . $consignment->getId() . ".png";
                                        file_put_contents($fileName, fopen($labelUrl, 'r'));
                                        $pdf = new PdfBase(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
                                        $this->pdf = $pdf;
                                        $this->pdf->SetPrintFooter(false);
                                        $this->pdf->SetFooterMargin(0);
                                        $this->pdf->SetAutoPageBreak(false, 0);
                                        $page_size = array(100, 150);
                                        $page_type = "P";
                                        $image_width = 100;
                                        $image_height = 150;
                                        $this->pdf->AddPage($page_type, $page_size);
                                        $image = realpath($fileName);
                                        $this->pdf->image($fileName, 1, 1, $image_width, $image_height);
                                        $this->pdf->Output("../_assets/pdf/" . date('Y_m_d') . '/' . $consignment->getId() . ".pdf", "F");

                                    }
                                    else{
                                        $fileName = '../_assets/pdf/' . date('Y_m_d') . '/' . $consignment->getId() . ".pdf";
                                        file_put_contents($fileName, fopen($labelUrl, 'r'));
                                    }
                                    
                                    $output['STATUS'] = 'SUCCESS';
                                    $output['LABEL'] = date('Y_m_d') . '/' . $consignment->getId() . ".pdf";
                                    $output['TRACKING_NUMBER'] = $licence_plate_array;
                                    return $output;
                                }
                            }
                        }
                    }
                }
            }
        }
        else
        {
            $output["STATUS"] = "ERROR";
            $output["MESSAGE"] = $responseArray->errMsg;
            return $output;
        }
        
    }
    
    private function curlRequest($request, $namespace, $debug=false){
        $jsonRequest = json_encode($request);
        $dateTime = date("Y-m-d H:i:s");
        $apiSecret = $this->constants["EASYSENT_APISECRET"]; //"0XoVcWT4u64vvC4mIBAIS0tBHyukK/5QPeznlunc" ; test credentials
        $signature = md5($jsonRequest.$apiSecret.$dateTime);
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $namespace,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $jsonRequest,
            CURLOPT_HTTPHEADER => array(
                'appKey: ' . $this->constants["EASYSENT_APIKEY"], //"wCB01qESG14WADAWv1bt", test credentials
                'signature:'. $signature,
                'requestDate:' . $dateTime,
                'languageCode: en',
                'Content-Type: application/json'
            ),
        ));

        $response = curl_exec($curl);
        if($debug){
            echo $namespace;
            echo $signature . "<br />" . $dateTime;
            print_r($jsonRequest);
            echo "<br />";
            print_r($response);
        }
        curl_close($curl);
        return json_decode($response);
    }

    public function tracking($trackingNumber, $trackBy = 'parcel', $EDI = false) {
        
    }

    public function sendData($tracking_numbers = array()) {
        $output = [];
        $agentServiceArray = [];
        $consignmentData = new ConsignmentFilter();
        $consignmentData->addFilter("     s.carrier_id= '236'", "servicefilter");
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
                            $this->record_array[] = $consignmentItemData->getHawb();
                        }
                        $this->booking_file = "STEASY".date("Ymdhis");
                        
                        /*
                         * Create Bag
                         */
                        
                        $bagRequest = array("sackNumber"=> $this->booking_file, "theThirdNumbers" => $this->record_array);
                            
                        $namespace = "http://b.flux.easysent.com:8133/wgs/v1/bind/sack";
                        $responseArray = $this->curlRequest($bagRequest, $namespace);
                        print_r($responseArray);
                        if ($responseArray->status == "SUCCESS") {
                                $sql = "UPDATE consignment SET send_courier_data = 1, booked_file_id = '" . $this->booking_file . "'
                                        WHERE id IN ('" . implode("','", $consignmentIdArray) . "') AND id <> '0' 
                                        AND  shipment_status not in ('" . Consignment::STATUS_RECYCLED . "','" . Consignment::STATUS_READY_TO_PRINT . "','" . Consignment::STATUS_INVALID . "')";
                                DbAccess3::runQuery($sql);
                                $output["STATUS"] = "SUCCESS";
                                $output["MESSAGE"] = "System has successfully send data.";
                        } else {
                            $output["STATUS"] = "ERROR";
                            $output["MESSAGE"] = $responseArray->errMsg;
                        }
                    } else {
                        $output["STATUS"] = "ERROR";
                        $output["MESSAGE"] = "Unable to find shipments.";
                    }
                    return $output;
                }
            }
        }
    }

    public function manifest($consignment) {
        
    }

    public function preAdvice($consignment) {
        
    }

    /**
     * generateLabel
     * @param string new pass phrase <p>
     * Must be a string or array.
     * </p>
     */
    public function recycledShipment($consignment) {
        $output["STATUS"] = "SUCCESS";
        return $output;
    }

}
