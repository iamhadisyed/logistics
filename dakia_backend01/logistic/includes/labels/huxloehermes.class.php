<?php
include_classes([
    'convertxml2array.class',
    ], 'library');
include_classes([
    'pdfmerger',
    ], 'labels');
include_classes([
    'tcpdf',
    ], '3rdparty/tcpdf');
include_classes([
    'fpdi',
    ], '3rdparty/fpdi');
include_classes([
    'serviceconstantvalue.class',
    'serviceconstantvaluefilter.class'
    ]);
class HuxloeHermes implements CarrierService {

    private $pdf;
    private $serviceValues = null;
    private $constants = null;
    private $country = null;
    private $user = null;
    private $error_message = null;
    private $error_code = null;

    public function __construct() {
       
    }

    public function validation(Consignment $consignment, Services $service, Country $country) {

        $returnOutput = array();
		$companyDataCheck = trim(str_replace("."," ",str_replace("-"," ",$consignment->getCompany())));
        if (trim($companyDataCheck) != '') {
            $returnOutput[] = "This service is currently only available to Business To Customer (BToC), So, if you have put anything in the company field,  kindly remove that because it will not work for Business To Business (BToB).";
        }
         if ($consignment->getNumberPieces() > 1) {
             $returnOutput[] = "Service is not supported multi piece shipment. Please create one piece shipment.";
        }

        return $returnOutput;
    }

    public function remoteareas($consignment, $carrierObject, $sender) {
        
    }

    public function label($consignment, $labelType = 'pdf', $size = '100x150') {

        $output = array();
        $this->serviceValues = new Services($consignment->getServiceId());
        $this->country = new Country($consignment->getCountryId());
        $this->user = SessionManager::getUser();
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
        if (trim(@$this->constants['INTEGRATION_TYPE']) == ''|| trim(@$this->constants['HUXLOE_URL']) == '' || trim(@$this->constants['HUXLOE_USERNAME']) == '' || trim(@$this->constants['HUXLOE_PASSWORD']) == '') {
            $output['STATUS'] = 'ERROR';
            $output['MESSAGE'] = "Please check service constants. Please contact to itsupport@oneworldexpress.com";
            return $output;
        }

        if (trim(@$this->constants['INTEGRATION_TYPE']) == 'API') {
            $output = $this->apiLabel($this->constants, $consignment);
        }

        return $output;
    }

    public function tracking($trackingNumber, $trackBy = 'parcel', $EDI = false) 
    {
        
        $tracking = new Tracking();
        include_once(BASE_PATH."includes/labels/huxloehermestrackingstatus.class.php");
        $json = file_get_contents("https://www.hermesworld.com/TrackMyParcel/customersearch.json?trackingNumber=$trackingNumber");
        
        $array = json_decode($json, true);
        $trackingArray = $array['parcels'][0]['tracking']; 
        $trackingArray = array_reverse($trackingArray);
        
        if(count($trackingArray) > 0)
        {
            $entityId = 0;
            if ($trackBy == 'parcel') 
            {
                //echo $trackingNumber; die;
                $parcelObj = new ParcelFilter();
                $parcelObj->addTrackingNumberFilter($trackingNumber);
                $parcelDataArray = $parcelObj->getColumnList('p.id, p.tracking_number, p.consignment_id');
                
                if (count($parcelDataArray) > 0) 
                {
                    $parcelData = $parcelDataArray[0];
                    $entityId = $parcelData->getId();
                }
            } 
            else if ($trackBy == 'shipment') 
            {
                $shipmenObj = new ConsignmentFilter();
                $shipmenObj->addawbFilter($trackingNumber);
                $shipmentDataArray = $shipmenObj->getColumnList('c.awb');
                if (count($shipmentDataArray) > 0) 
                {
                    $shipmentData = $shipmentDataArray[0];
                    $entityId = $shipmentData->getId();
                }
            }
        //echo $entityId; die;    
            if($entityId >0 )
            {
                ////////////////////// Carrier Received ////////////////////////////////
            
                $trackingDataFilterObj = new TrackingDataFilter();
                $trackingDataFilterObj->addFieldFilter('    tracking_number', $trackingNumber);        
                $trackingDataFilterObj->addFilter("carrier_code not in ('','1012','1663')");
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
                $parcelEntity = new Parcel($entityId);
                $finalStatusCode = $parcelEntity->getParcelStatusCode();
                $deliveredArray = array('1520','1521','1609','1610','1288','4062','1263');
                //print_r($trackingArray); die;
                foreach($trackingArray as $event)
                { 
                    $date = $event['date'];
                    $dateArray = explode("/", $date);
                    $day = $dateArray[0];
                    $month = $dateArray[1];
                    $year = $dateArray[2];
                    $DateTime = $year . "-" . $month . "-" . $day . " " . $event['time'];
                   
                    $EventCode = $event['id'];
                    $EventDescription = $event['status'];
                    $ServiceAreaDescription = $event['status'];
                    $Signatory = "";
                    //echo $carrierCodeCarrierReceived; die;
                    // Dont enter any other event code if it is against 148 Event Code
                    if($carrierCodeCarrierReceived == $EventCode)
                    {
                        continue;
                    }
                    //$spTrackingStatus = HuxloeHermesTrackingStatus::getOweStatusCode($EventCode);                   
                     
                    ////////////////////// Carrier Received ////////////////////////////////
                    if($carrierReceivedCheck == 1 && $EventCode != '1012' && $EventCode != '1663')
                    {
                        $spTrackingStatus = '148';
                        $carrierReceivedCheck = 0 ;                        
                    }
                    else
                    {
                        $spTrackingStatus = HuxloeHermesTrackingStatus::getOweStatusCode($EventCode);
                    }
                    //echo $spTrackingStatus; die;
                    ////////////////////// Carrier Received ////////////////////////////////
                    $result = $tracking->saveTrackPoint($entityId, $trackBy, $DateTime, $trackingNumber, $spTrackingStatus, $ServiceAreaDescription, $EventCode, $EventDescription, $deliveredArray, $finalStatusCode);                    
                    if($result == true)
                    {
                        break;
                    }
                }
                $tracking->saveConsignmentTrackingStatus($trackingNumber, 'HuxloeHermesTrackingStatus');      
            }
        }
    }

    public function sendData($tracking_numbers = array()) {
        
    }

    public function manifest($consignment) {
        
    }

    public function preAdvice($consignment) {
        
    }

    private function apiLabel($constant, Consignment $consignment) {
        $pdf = new PdfBase(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $this->pdf = $pdf;
        $output = array();
		$companyDataCheck = trim(str_replace("."," ",str_replace("-"," ",$consignment->getCompany())));
		$contactDataCheck = trim(str_replace("."," ",str_replace("-"," ",$consignment->getContact())));
        if (trim($this->user->getUserAccount()) == 'PARCEL1') {
            if (trim($consignment->getCompany()) != trim($consignment->getContact()))
                $contactData = trim($consignment->getContact()) . " " . trim($consignment->getCompany());
            else
                $contactData = trim($consignment->getContact());
        }
        elseif (trim($companyDataCheck) != trim($contactDataCheck)) {
            if (trim($companyDataCheck) != '' && trim($companyDataCheck) != '-') {
                $output["STATUS"] = "ERROR";
                $output["MESSAGE"] = $this->getErrorCode() . ", This service is not able to provide service on a company address ";
            } else
                $contactData = trim($consignment->getContact());
        }
        else {
            $contactData = trim($consignment->getContact());
        }
        $this->server_url = $constant["HUXLOE_URL"]; //'https://dev.myparcellabel.co.uk/';//'https://www.myparcellabel.co.uk/'; //
        $this->username = $constant["HUXLOE_USERNAME"];//"oneworldapi"; //
        $this->password = $constant["HUXLOE_PASSWORD"]; //"16Sept6214"; //
        $loginData = $this->login();
       
        $weight = $consignment->getWeight() * 1000;
        if($this->country->getIso() == "GB"  && $this->serviceValues->getCode() == "STHUXSIGN"){
            $service = "H48POD";
            $carrier = 'HERMES';
        }
        
        else if($this->country->getIso() == "GB")
        {
            $service = "H48";
            $carrier = 'HERMES';
        }
        else if($this->country->getRegion() == "R1")
        {
            $service = "HWEU";
            $carrier = 'HermesWS';
        }
        
     

      //  if ($consignment->gethandling() != "HWSRT") {
            $ADDRESS1 = $consignment->getAddressLine1();
            $ADDRESS2 = $consignment->getAddressLine2();
            $ADDRESS3 = $consignment->getAddressLine3();
            $CITY = $consignment->getCity();
            $POSTCODE = $consignment->getPostcode();


        if ($loginData !== false) {
           $iso = $this->country->getIso();
            if($iso == "PT")
            {
                $iso = "PRT";
            }
            $senderCountry = new country($consignment->getSenderCountryId());
            $parcel_list = $consignment->getParcels();
            if(count($parcel_list) > 0){
                foreach($parcel_list as $parcel){
                    $parcelDescription = json_decode($parcel->getDescription());
                    $parcelCountry = json_decode($parcel->getCommodityCode());
                    $parcelQty = json_decode($parcel->getQty());
                    $parcelValue = json_decode($parcel->getItemValue());
                    $parcelHscode = json_decode($parcel->getHsCode());
                    $parcelSku = json_decode($parcel->getItemSku());
                    $parcelWeight = json_decode($parcel->getPWeight());
                    
                    $parcelItem = array();
                    if(count($parcelDescription) > 0){
                        foreach($parcelDescription as $key => $desc){
                            
                            $parcelItem[] = array(
                                'Parcel' => array(
                                    'Weight' => $parcelWeight[$key],
                                    'Contents' => $desc,
                                    'SKUCode' => $parcelSku[$key],
                                    'QuantityOfItems' => $parcelQty[$key],
                                    'Value' => ($parcelValue[$key] * 100),
                                    'Customs' => array(
                                        'DutyPaid' => false,
                                        'DutyValue' => '0.00',
                                        'DutyResponsibility' => 'Customer',
                                        'HSCode' => $parcelHscode[$key],
                                        'EORINumber' => $consignment->getEoriNumber(),
                                        'ExportReason' => 'Sales',
                                        'VATValue' => '0.00',
                                        'CountryOfOrigin' => $parcelCountry[$key],
                                        'VATNumber' => $consignment->getVatNumber(),
                                        'IOSSNumber' => $consignment->getIossNumber()
                                    )
                                )
                            );
                        }
                    }
                    else{
                        $parcelItem[] = array(
                            'Parcel' => array(
                                'Weight' => $consignment->getWeight(),
                                'Contents' => $consignment->getDescription(),
                                'SKUCode' => '',
                                'QuantityOfItems' => $consignment->getNumberPieces(),
                                'Value' => ($consignment->getValue() * 100),
                                'Customs' => array(
                                    'DutyPaid' => false,
                                    'DutyValue' => '0.00',
                                    'DutyResponsibility' => 'Customer',
                                    'HSCode' => '',
                                    'EORINumber' => $consignment->getEoriNumber(),
                                    'ExportReason' => 'Sales',
                                    'VATValue' => '0.00',
                                    'CountryOfOrigin' => $senderCountry->getIso(),
                                    'VATNumber' => $consignment->getVatNumber(),
                                    'IOSSNumbe' => $consignment->getIossNumber()
                                )
                            )
                        );
                    }
                }
            }
            $createLabelImageParam = array(
                'Request' => array(
                    'Customer' => array(
                        'ConsigneeName' => $contactData, //$consignment->getContact(),
                        'Address' => array(
                            'AddressLine1' => ($ADDRESS1),
                            'AddressLine2' => ($ADDRESS2) . $ADDRESS3,
                            'Town' => '',
                            'City' => $CITY,
                            'County' => '',
                            'Postcode' => $POSTCODE,
                            'CountryCode' => $iso),
                        'Contact' => array(
                            'HomePhoneNo' => $consignment->getTelephone(),
                            'MobilePhoneNo' => $consignment->getTelephone(),
                            'EmailAddress' => $consignment->getEmail()),
                       
                    ),
                    'Consignment' => array(
                        'Reference1' => $consignment->getHawb(),
                        'Service' => $service, //GERMANY HD1
                        'NoOfItems' => $consignment->getNumberPieces(),
                        'DespatchDate' => date("dmY"),
                        'Contents' => $consignment->getDescription(),
                        'Value' => ($consignment->getValue() * 100),
                        'Currency' => $consignment->getCurrency(),
                        'CountryOfOrigin' => $senderCountry->getIso(),
                        'DeliveryConfirmation' => '1',
                        'ConfirmationEmail' => $consignment->getEmail(),
                        'ConfirmationMobile' => $consignment->getTelephone(),
                        'ExtendedCover' => '0',
                        'Instructions1' => $consignment->getNotes(),
                        'Parcels' => $parcelItem,
                    ),
                    'Sender' => array(
                        'ContactName' => $constant["HUXLOE_SHIPPER_CONTACT"],
                        'Address' => array(
                        'AddressLine1' => $constant["HUXLOE_SHIPPER_ADDRESSLINE1"],// 'One World House',
                        'AddressLine2' => $constant["HUXLOE_SHIPPER_ADDRESSLINE2"],// 'Pump lane',
                        'Town' => '',
                        'City' => $constant["HUXLOE_SHIPPER_CITY"],
                        'County' => $constant["HUXLOE_SHIPPER_COUNTY"],
                        'Postcode' => $constant["HUXLOE_SHIPPER_POSTCODE"],
                        'CountryCode' => $constant["HUXLOE_SHIPPER_COUNTRY"]),
                        'ContactPhoneNo' => $constant["HUXLOE_SHIPPER_TELEPHONE"]
                        
                    ),
                ),
            );
            $labelImage = $this->createLabelImage($createLabelImageParam, $carrier);
            $consignment->setApiData(print_r($createLabelImageParam, true), print_r($labelImage, true), 'createLabelImage');
            if ($labelImage !== false) {
                $labelImage->Status;

                if (trim($labelImage->Status) == 'Success') {
                    $uniqueFileName = uniqid();
                    $this->pdf->SetPrintFooter(false);
                    $this->pdf->SetFooterMargin(0);
                    $this->pdf->SetAutoPageBreak(false, 0);
                    //if ($consignment->getHandling() == "HD1") {
                        $page_size = array(150, 100);
                        $page_type = "L";
                        $image_width = 150;
                        $image_height = 100;
//                    } else {
//                        $page_size = array(100, 150);
//                        $page_type = "P";
//                        $image_width = 100;
//                        $image_height = 150;
//                    }
                    $parcelList = $consignment->getParcels();
                    $countParcel = 0;
                    foreach ($labelImage->Labels as $label) {

                        $labelAttrip = $label->Label->attributes();
                        $labelAttrip = (array) $labelAttrip ;
                       
                        $licence_plate_array[]  = $labelAttrip['@attributes']['ConsignmentNumber'];
                        $pdfContent = base64_decode($label->Label);
                        $fileName = '../_assets/pdf/' . date('Y_m_d') . '/' . $uniqueFileName . ".png";


                        file_put_contents($fileName, $pdfContent);
                        $this->pdf->AddPage($page_type, $page_size);
                        $image = realpath($fileName);
                        $this->pdf->image($image, 1, 1, $image_width, $image_height);
                        $this->pdf->setXy(0, 95);
                        $this->pdf->Cell(3, 10, "", 1, 0, "L", true);
                        if (count($parcelList) > 0) {
                            $parcelList[$countParcel]->setTrackingNumber( $labelAttrip['@attributes']['ConsignmentNumber']);
                            $parcelList[$countParcel]->setParcelLabel(date('Y_m_d') . '/' . $labelAttrip['@attributes']['ConsignmentNumber']. ".pdf");
                            $parcelList[$countParcel]->save();
                        }
                        $pieceoutputfilename = SETTING_DIR_ASSETS . "pdf/" . date("Y_m_d") . "/" . $labelAttrip['@attributes']['ConsignmentNumber'] . ".pdf";
                        $convertCommad = "convert " . SETTING_DIR_ASSETS . "pdf/" . date("Y_m_d") . "/" . $uniqueFileName . ".png " . $pieceoutputfilename;
                        //echo $convertCommad;
                        //echo "<br />";
                        $last_line = system($convertCommad, $retval);
                        $countParcel++;
                    }
                    
                    //$this->pdf->Output("../_assets/pdf/" . date('Y_m_d') . '/g' . $consignment->getId() . ".pdf", "F");
                    $outputfilename = SETTING_DIR_ASSETS . "pdf/" . date("Y_m_d") . "/" . $consignment->getId() . ".pdf";
                    
                    
                    $convertCommad = "convert " . SETTING_DIR_ASSETS . "pdf/" . date("Y_m_d") . "/" . $uniqueFileName . ".png " . $outputfilename;
                    //echo $convertCommad;
                    //echo "<br />";
                    $last_line = system($convertCommad, $retval);
                    
                    if(file_exists($outputfilename)){
                        $PDFMerger = new PDFMerger();
                        $PDFMerger->addPDF($outputfilename);
                        try {
                            $PDFMerger->mergeHuxloe('file', $outputfilename, '', $consignment);
                        } catch (Exception $e) {
                            echo 'Caught exception: ', $e->getMessage(), "\n";
                        }
                        
                        $output["STATUS"] = "SUCCESS";
                        $output['TRACKING_NUMBER'] = $licence_plate_array;
                        $output["LABEL"] = date('Y_m_d') . '/' . $consignment->getId() . ".pdf";
                    
                    }else{
                        $output["STATUS"] = "ERROR";
                        $output["MESSAGE"]  = "Unable to create label for requested address.";
                    }
                    return $output;
                   // exec("convert " . SETTING_DIR_ASSETS . "pdf/" . date("Y_m_d") . "/" . $uniqueFileName . ".png " . SETTING_DIR_ASSETS . "pdf/" . date("Y_m_d") . "/" . $consignment->getId() . ".pdf", $output,1);
                   // print_r($output);
                    //unlink($fileName);
                   
                }
            } else {
                $output["STATUS"] = "ERROR";
                $output["MESSAGE"]  = $this->getErrorCode() . ", -" . $this->getErrorMessage();
                return $output;
            }
        } else {
                $output["STATUS"] = "ERROR";
                $output["MESSAGE"]  = $this->getErrorCode() . ", -" . $this->getErrorMessage();
                return $output;
        }
    }

    private function login() {
      
        $opts = array(
            'http' => array(
                'method' => "GET",
                'header' => "Authorization: Basic " . base64_encode("$this->username:$this->password")
            )
        );

        $context = stream_context_create($opts);
        $data = file_get_contents($this->server_url . "login", false, $context);
        $xmlResponse = simplexml_load_string($data);
        if ($xmlResponse->Status == 'Success') {
            $this->auth_token = $xmlResponse->AuthToken;
            $this->client_name = $xmlResponse->ClientName;
            return array("AuthToken" => $this->auth_token, "ClientName" => $this->client_name);
        } else {
            $responseError = $xmlResponse->Errors->ResponseError;
            $this->error_code = $responseError->ErrorCode;
            $this->error_message = $responseError->ErrorDescription;
            return false;
        }
    }
    
    public function getErrorCode() {
        return $this->error_code;
    }
    private function getErrorMessage() {
        return $this->error_message;
    }
    
     private function postRequest($func, $headerString, $requestParam) {
        $xml = new SimpleXMLElement('<CreateLabelRequest></CreateLabelRequest>');
        $this->prepareRequest($requestParam, $xml);
        $post_data = str_replace('<?xml version="1.0"?>', '', $xml->asXML());
        $context_options = array(
            'http' => array(
                'method' => "POST",
                'header' => "Content-type: application/x-www-form-urlencoded\r\n" .
                "Content-Length: " . strlen($post_data) . "\r\n" .
                "AuthToken: " . $this->auth_token . "\r\n" . $headerString,
                'content' => $post_data
            )
        );
      
        $context = stream_context_create($context_options);
        $data = file_get_contents($this->server_url . $func, false, $context);

        $xmlResponse = simplexml_load_string($data);
		
		
		return $xmlResponse;
		
		
    }
    private function prepareRequest($requestParam, &$xml) {
        foreach ($requestParam as $key => $value) {
            if(is_array($value) && is_numeric($key)){
                $this->prepareRequest($value, $xml);
            }
            else if (is_array($value)) {
                $subnode = $xml->addChild("$key");
                $this->prepareRequest($value, $subnode);
            } else {
                $xml->addChild("$key", htmlspecialchars("$value"));
            }   
            
        }
    }
     private function createLabelImage($param, $carrier) {
        $header = "Carrier :" .$carrier. "\r\n";
        $xmlResponse = $this->postRequest('CreateLabel', $header, $param);
        if ($xmlResponse->Status == 'Success') {
            return $xmlResponse;
        } else {
	
            $responseError = $xmlResponse->Errors->ResponseError;
            $this->error_code = $responseError->ErrorCode;
            $this->error_message = $responseError->ErrorDescription;
            return false;
        }
    }
    
    function removecommas($data) {
        return str_replace(",", " ", $data);
    }

    function recycledShipment($consignment) {
        $output["STATUS"] = "SUCCESS";
        return $output;
    }

    public function reconciliation_data($headingArr,$carrierId,$relPath, $file2,$new_csv_file_created,$filePath,$batchNumber) {
        $output = [];
//        $carrierObj = new Carrier($carrierId);
//        @$csv_file = $file;
//        if (!empty($csv_file['name'])) {
//            $file_name = $csv_file['name'];
//            $path_parts = pathinfo($file_name);
//            $ext = strtolower($path_parts['extension']);
//            $basename = $path_parts['basename'];
//            if ($ext == 'csv') {
//                $carrierName = str_replace(" ","_",$carrierObj->getCarrier());
                $user = SessionManager::getUser();
        $userId = $user->getId();
//                $batchNumber = $userId . "_" . time();
//                $newFileName = strtolower($carrierName)."_" . $batchNumber . "." . $ext;
//                $dateFolder = date('Y-m-d');
//                $output['date_folder'] = $dateFolder;

//                $filePath = RECONCILIATION_PATH. '/' . $carrierName.'/'.$dateFolder;
//                $relPath = RECONCILIATION_PATH.$carrierName.'/'.$dateFolder.'/'.$newFileName;
//                $output['upload_file'] = $newFileName;
//                if (!file_exists(RECONCILIATION_PATH)) {
//                    @mkdir(RECONCILIATION_PATH, 0775);
//                }
//                if (!file_exists(RECONCILIATION_PATH.$carrierName.'/')) {
//                    @mkdir(RECONCILIATION_PATH.$carrierName.'/', 0775);
//                }
//                if (!file_exists(RECONCILIATION_PATH.$carrierName.'/'.$dateFolder.'/')) {
//                    @mkdir(RECONCILIATION_PATH.$carrierName.'/'.$dateFolder.'/', 0775);
//                }
//                /* create new csv file and write data on it*/
//                $new_csv_file_name = strtolower($carrierName) . "_new_" . $batchNumber . ".csv";
//                $new_csv_file_created = RECONCILIATION_PATH.$carrierName.'/'.$dateFolder.'/'.$new_csv_file_name;
//                $new_csv_file_save = RECONCILIATION_URL.$carrierName.'/'.$dateFolder.'/'.$new_csv_file_name;

                $comaSeptHeading = implode(",",$headingArr);
                $tableColumn = rtrim($comaSeptHeading,',');
                $csvStr = $tableColumn;
                $csvStr .= "\r\n";
                $templateCheck = 1;
                $checkExist = 0;
                $output['new_invoice_save'] = 0;
                $output['total_weight'] = 0;
                $output['total_pieces'] = 0;
                $output['total_amount'] = 0;
//                if (move_uploaded_file($csv_file['tmp_name'], $relPath)) {
                    $row = 1;
                    $dataArr = [];
                    if (($handle = fopen($relPath, "r")) !== FALSE) {
                        $fuelChargePercentage = 0.00;
                        $invoice_number = '';
                        $account_number = '';
                        while (($data = fgetcsv($handle)) !== FALSE) {
                            if($row > 1) {
                                $invoice_number = $data['1'];
                                $collection_date = $data['4'];
                                $service_code = '';
                                $agent_reference_number = '';
                                $delivery_country = $data['9'];
                                $mawb = '';
                                $awb = $data['3'];
                                $hawb = $data['5']; //important

                                $service_name = '';
//                                $service_code = ''; //important
                                if(!empty($data['7'])) {
                                    $weight = floatval($data['7'] / 1000);
                                } else {
                                    $weight = 0;
                                }
                                $vol_weight = '';
                                $length = '';
                                $width = '';
                                $height = '';
                                $number_of_pieces = $data['13'];
                                if(!empty($data['19'])) {
                                    $basic_charges = $data['19'];
                                } else {
                                    $basic_charges = 0;
                                }

                                if(!empty($data['17'])) {
                                    $fuel_charges = $data['17'];
                                } else {
                                    $fuel_charges = 0;
                                }

                                $vat = 0;

                                $total_amount =  $fuel_charges + $basic_charges + $vat;
                                $notes = '';

                                $output['invoice_number'] = $invoice_number;
                                $supplierInvoiceFilter = new SupplierInvoicesFilter();
                                $supplierInvoiceFilter->where(['si.invoice_number' => $invoice_number]);
                                $supplierInvoiceFilterObjs = $supplierInvoiceFilter->getList();
                                if(count($supplierInvoiceFilterObjs)) {
                                    $output['status'] = 'error';
                                    $output['message'] = 'This file is already processed';
                                    $output['new_invoice_save'] = 1;
                                    break;
                                }
                                $output['invoice_date'] = $collection_date;
                                $length = "0.00";
                                $height = "0.00";
                                $width = "0.00";
                                $volWeight = "0.000";

                                $weight = formatNumber($weight, 3);

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
                                    'vat' => $vat,
                                    'total_amount' => $total_amount,
                                    'notes' => $notes,
                                ];

                                $output['total_weight'] += $weight;
                                $output['total_pieces'] += $number_of_pieces;
                                $output['total_amount'] += $total_amount;
                                $dataArr[] = $dt;
                                $comaSept = implode(",", $dt);
                                $csvStr .= rtrim($comaSept, ',');
                                $csvStr .= "\r\n";
                            }
                            $row++;
                        }
                    } else {
                        $output['status'] = 'error';
                        $output['message'] = 'File can not open please check permission';
                    }
//                } else {
//                    $output['status'] = 'error';
//                    $output['message'] = 'File upload error';
//                }
                if($output['status'] != "error" && !$output['return']) {
                    $myCsvFile = fopen($new_csv_file_created, "a") or die("Unable to open file!");
                    fwrite($myCsvFile, $csvStr);
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
                        $output['invoice_number'] = $invoice_number;
                        $output['template'] = $templateCheck;
                        $output['data'] = $res;
                    }
                }
//            } else {
//                $output['status'] = 'error';
//                $output['message'] = 'Please select valid csv file';
//            }
//        } else {
//            $output['status'] = 'error';
//            $output['message'] = 'Please select valid csv file';
//        }
        return $output;
    }
    public function getSummeryData($relPath){
        $returnArr = [];
        $row = 1;
        $isInvoiceType = false;
        $isDataSet = true;
        if (($handle = fopen($relPath, "r")) !== FALSE) {
            while (($data = fgetcsv($handle)) !== FALSE) {
                if($row > 1) {
                    $returnArr['invoice_number'] = $data['1'];
                    $returnArr['collection_date'] = $data['4'];
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
