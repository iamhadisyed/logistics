<?php

include_classes([
    'ukmailauthentication.class',
    'ukmailauthenticationfilter.class',
]);

class UKMail implements CarrierService {

    private $pdf;
    private $serviceValues = null;
    private $agentValues = null;
    private $constants = null;
    private $user = null;
    private $country = null;

    public function __construct() {
        
    }

    public function validation(Consignment $consignment, Services $service, Country $country) {
        $returnOutput = array();
        if (trim($consignment->getEmail()) == "") {
            $returnOutput[] = "Please enter email address.";
        }
        if (trim($consignment->getTelephone()) == "") {
            $returnOutput[] = "Please enter Telephone No.";
        }
        $remoteAreaCheck = $this->remoteareas($consignment, "", "");
        switch ($remoteAreaCheck) {
            case 'allowed':
                break;
            case 'not-allowed':
                $returnOutput[] = "The service is not allowed for " . $consignment->getPostcode() . " postcode. ";
                break;
        }
        
        if($consignment->getShipmentType() == "C" && ($consignment->getCollectionDate() == '' || $consignment->getCollectionDate() == NULL )){
            $returnOutput[] = "Please select collection date and time.";
        }
        return $returnOutput;
    }

    public function remoteareas($consignment, $carrierObject, $sender) {
        $notArrayInUkmail = array('ZE', 'IM', 'JE', 'GY', 'BT', 'HS', 'D1', 'PA', 'KW1', 'KW2', 'KW3', 'KW4', 'KW5', 'KW6', 'KW7', 'KW8', 'KW9', 'IV1', 'IV2', 'IV3', 'IV4', 'IV5', 'IV6', 'IV7', 'IV8', 'IV9', 'D34', 'D30',
            'D45', 'D47', 'D49', 'D44', 'D48', 'D26', 'D36', 'D33', 'D41', 'D46', 'D40', 'D28', 'D43', 'D25', 'D29', 'D32', 'D39', 'D42', 'D38', 'D37',
            'D31', 'D35', 'D27', 'PH4', 'PH5', 'PH6', 'PH7', 'PH8', 'PH9', 'PH10', 'PH11', 'PH12', 'PH13', 'PH14', 'PH15', 'PH16', 'PH17', 'PH18', 'PH19', 'PH20', 'PH21', 'PH22', 'PH23', 'PH24', 'PH25', 'PH26', 'PH27',
            'PH28', 'PH29', 'PH30', 'PH31', 'PH32', 'PH33', 'PH34', 'PH35', 'PH36', 'PH37', 'PH38', 'PH39', 'PH40', 'PH41', 'PH42', 'PH43', 'PH44', 'PH49',
            'PH50',
            'KW10', 'KW11', 'KW12', 'KW13', 'KW14', 'KW15', 'KW16', 'KW17', 'KA27', 'KA28',
            'PA20', 'PA21', 'PA22', 'PA23', 'PA24', 'PA25', 'PA26', 'PA27', 'PA28', 'PA29', 'PA30', 'PA31', 'PA32', 'PA33', 'PA34', 'PA35', 'PA36', 'PA37', 'PA38',
            'PA39', 'PA40', 'PA41', 'PA41', 'PA42', 'PA43', 'PA44', 'PA45', 'PA46', 'PA47', 'PA48', 'PA49', 'PA60', 'PA61', 'PA62', 'PA63', 'PA64', 'PA65', 'PA66',
            'PA67', 'PA68', 'PA69', 'PA70', 'PA71', 'PA72', 'PA73', 'PA74', 'PA75', 'PA76', 'PA77', 'PA78',
            'IV10', 'IV11', 'IV12', 'IV13', 'IV14', 'IV15', 'IV16', 'IV17', 'IV18', 'IV19', 'IV20', 'IV21',
            'IV22', 'IV23', 'IV24', 'IV25', 'IV26', 'IV27', 'IV28', 'IV30', 'IV31', 'IV32', 'IV36', 'IV37', 'IV38', 'IV39', 'IV40',
            'IV41', 'IV42', 'IV43', 'IV44', 'IV45', 'IV46', 'IV47', 'IV48', 'IV49', 'IV51', 'IV52', 'IV53', 'IV54', 'IV55', 'IV56', 'IV63',
            'AB31', 'AB32', 'AB33', 'AB34', 'AB35', 'AB36', 'AB37', 'AB38', 'AB40', 'AB41', 'AB42', 'AB43', 'AB44', 'AB45', 'AB46', 'AB47', 'AB48', 'AB49',
            'AB50', 'AB51', 'AB52', 'AB53', 'AB54', 'AB55', 'AB56',
            'PO30', 'PO31', 'PO32', 'PO33', 'PO34', 'PO35', 'PO36', 'PO37', 'PO38', 'PO39', 'PO40', 'PO41',
            'TR21', 'TR22', 'TR23', 'TR24', 'TR25');
        $postcode = substr(str_replace(" ", "", $consignment->getPostcode()), 0, -3);
        if (in_array($postcode, $notArrayInUkmail)) {
            return "not-allowed";
        }

        return "allowed";
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

        if (trim(@$this->constants['UKMAIL_USERNAME']) == '' || trim(@$this->constants['UKMAIL_ACCOUNT']) == '' || trim(@$this->constants['UKMAIL_PASSWORD']) == '') {
            $output['STATUS'] = 'ERROR';
            $output['MESSAGE'] = "Please check service constants. Please contact to itsupport@oneworldexpress.com";
            return $output;
        }

        if (trim(@$this->constants['INTEGRATION_TYPE']) == 'API') {
            if ($consignment->getShipmentType() == "C") {
                $output = $this->apiCollectionLabel($this->constants, $consignment);
            } else {
                $output = $this->apiLabel($this->constants, $consignment);
            }
        }

        return $output;
    }

    private function apiLabel($constant, Consignment $consignment) {

        $output = array();

        $ukmailfilter = new UkmailAuthenticationFilter();
        $list = $ukmailfilter->addDateCreatedFilter();
        $USERNAME = $constant["UKMAIL_USERNAME"];
        $PASSWORD = $constant["UKMAIL_PASSWORD"];
        $ACCOUNT = $constant["UKMAIL_ACCOUNT"];
        $UKMAIL_URL_AUTH = $constant["UKMAIL_URL_AUTH"];
        if (count($list) == 0) {

           $getToken = $this->getAuthenticationToken($consignment);
            if($getToken["STATUS"] == "ERROR"){
                return $getToken;
            }
            else
                $AuthenticationToken = $getToken["MESSAGE"];
        } else {
            $AuthenticationToken = $list[0]->getAuthenticationToken();
        }
        $UKMAIL_URL_COLLECTION = $constant["UKMAIL_URL_COLLECTION"];
        $BookCollectionWebRequest = new stdClass();
        $BookCollectionWebRequest->AuthenticationToken = $AuthenticationToken;
        $BookCollectionWebRequest->Username = $USERNAME;
        $BookCollectionWebRequest->AccountNumber = $ACCOUNT;
        $BookCollectionWebRequest->ClosedForLunch = 'false';
        $BookCollectionWebRequest->EarliestTime = date('Y-m-d', strtotime('+2 Weekday', time())) . 'T' . '11:48:00.000';
        $BookCollectionWebRequest->LatestTime = date('Y-m-d', strtotime('+2 Weekday', time())) . 'T' . '17:00:00.000';
        $BookCollectionWebRequest->RequestedCollectionDate = date('Y-m-d', strtotime('+2 Weekday', time())) . 'T' . '11:48:00.000';
        $BookCollectionWebRequest->SpecialInstructions = $consignment->getNotes();
        $BookCollection = new stdClass();
        $BookCollection->request = $BookCollectionWebRequest;
        $requestCookingStringApi = $BookCollectionWebRequest;

        $soapClientCollection = new SoapClient($UKMAIL_URL_COLLECTION);
        $BookCollectionWebResponse = $soapClientCollection->BookCollection($BookCollection);
        $resultLabelAuth = $BookCollectionWebResponse->BookCollectionResult->Result;

        if (strtolower(trim($resultLabelAuth)) == "failed") {
            $output["STATUS"] = "ERROR";
            $output["MESSAGE"] = $errorDesc = $BookCollectionWebResponse->BookCollectionResult->Errors->UKMWebError->Description;
            $errorCode = $BookCollectionWebResponse->BookCollectionResult->Errors->UKMWebError->Code;
            $consignment->setApiData(print_r($BookCollectionWebRequest, true), print_r($BookCollectionWebResponse, true), 'UKMAIL_BOOKING_FAIL');
            if($errorCode == "2050"){
                $getToken = $this->getAuthenticationToken($consignment);
                if($getToken["STATUS"] == "ERROR"){
                    return $getToken;
                }
            }
            return $output;
            
        } else
            $consignment->setApiData(print_r($BookCollectionWebRequest, true), print_r($BookCollectionWebResponse, true), 'UKMAIL_BOOKING_SUCCESS');

        //mail("mruga@oneworldexpress.com", "UKMAIL", $AuthenticationToken);
        if (count($this->error_list) <= 0) {
            $AddDomesticConsignmentWebRequest = new stdClass();
            $AddDomesticConsignmentWebRequest->AuthenticationToken = $AuthenticationToken;

            $AddDomesticConsignmentWebRequest->Username = $USERNAME;
            $AddDomesticConsignmentWebRequest->AccountNumber = $ACCOUNT;
            $AddDomesticConsignmentWebRequest->CollectionDate = date('Y-m-d', strtotime('+2 Weekday', time())) . 'T' . '11:48:00.000';
            $AddDomesticConsignmentWebRequest->Address->Address1 = $consignment->getAddressLine1();
            $AddDomesticConsignmentWebRequest->Address->Address2 = $consignment->getAddressLine2();
            $AddDomesticConsignmentWebRequest->Address->Address3 = $consignment->getAddressLine3();
            if (trim($this->country->getIso()) == 'GB')
                $AddDomesticConsignmentWebRequest->Address->CountryCode = 'GBR';
            else
                $AddDomesticConsignmentWebRequest->Address->CountryCode = $this->country->getIso();



            $AddDomesticConsignmentWebRequest->Address->County = '';
            $AddDomesticConsignmentWebRequest->Address->PostalTown = $consignment->getCity();
            $AddDomesticConsignmentWebRequest->Address->Postcode = $consignment->getPostCode();
            $AddDomesticConsignmentWebRequest->AlternativeRef = $consignment->getReference();
            //$AddDomesticConsignmentWebRequest->BusinessName = (trim($consignment->getContact())!= '') ? $consignment->getContact() :   $consignment->getCompany();//'OneworldExpress' ;

            $AddDomesticConsignmentWebRequest->BusinessName = (trim($consignment->getCompany()) == '') ? $consignment->getContact() : $consignment->getCompany();

            $AddDomesticConsignmentWebRequest->CollectionJobNumber = $BookCollectionWebResponse->BookCollectionResult->CollectionJobNumber;

            $AddDomesticConsignmentWebRequest->ConfirmationOfDelivery = "true";
            if (trim($consignment->getContact()) !== "")
                $AddDomesticConsignmentWebRequest->ContactName = $consignment->getContact();
            else
                $AddDomesticConsignmentWebRequest->ContactName = $consignment->getCompany();

            $AddDomesticConsignmentWebRequest->CustomerRef = $consignment->getHawb();
            $AddDomesticConsignmentWebRequest->Email = $consignment->getEmail();

            $AddDomesticConsignmentWebRequest->Items = $consignment->getNumberPieces();
            $AddDomesticConsignmentWebRequest->ServiceKey = '1';
            $AddDomesticConsignmentWebRequest->SpecialInstructions1 = $consignment->getDescription();
            $AddDomesticConsignmentWebRequest->SpecialInstructions2 = $consignment->getNotes();
            $AddDomesticConsignmentWebRequest->Telephone = $consignment->getTelephone();
            $AddDomesticConsignmentWebRequest->Weight = $consignment->getWeight();
            $AddDomesticConsignmentWebRequest->BookIn = "false";
            $AddDomesticConsignmentWebRequest->CODAmount = '0.00';
            $AddDomesticConsignmentWebRequest->ConfirmationEmail = $consignment->getEmail();
            $AddDomesticConsignmentWebRequest->ConfirmationTelephone = $consignment->getTelephone();
            $AddDomesticConsignmentWebRequest->ExchangeOnDelivery = "false";
            $AddDomesticConsignmentWebRequest->ExtendedCover = '0';
            $AddDomesticConsignmentWebRequest->LongLength = "false";
            $AddDomesticConsignmentWebRequest->PreDeliveryNotification = 'NonRequired';
            $AddDomesticConsignmentWebRequest->SecureLocation1 = 'secure 1';
            $AddDomesticConsignmentWebRequest->SecureLocation2 = 'secure 2';
            $AddDomesticConsignmentWebRequest->SignatureOptional = "false";
            $AddDomesticConsignmentWebRequest->LabelFormat = 'PNG6x4';


            $AddDomesticConsignment = new stdClass();
            $AddDomesticConsignment->request = $AddDomesticConsignmentWebRequest;

            $UKMAIL_URL_CONSIGNMENT = $constant["UKMAIL_URL_CONSIGNMENT"];

            $soapClientConsignment = new SoapClient($UKMAIL_URL_CONSIGNMENT);
            $AddDomesticConsignmentWebResponse = $soapClientConsignment->AddDomesticConsignmentDeferred($AddDomesticConsignment);

            $resultLabel = $AddDomesticConsignmentWebResponse->AddDomesticConsignmentDeferredResult->Result;
            if (strtolower(trim($resultLabel)) == "failed") {
                $consignment->setApiData(print_r($AddDomesticConsignmentWebRequest, true), print_r($AddDomesticConsignmentWebResponse, true), 'UKMAIL_LABEL_FAIL');

                if (isset($AddDomesticConsignmentWebResponse->AddDomesticConsignmentDeferredResult->Errors->UKMWebError->Description) && trim($AddDomesticConsignmentWebResponse->AddDomesticConsignmentDeferredResult->Errors->UKMWebError->Description) != '') {
                    $errorDesc = $AddDomesticConsignmentWebResponse->AddDomesticConsignmentDeferredResult->Errors->UKMWebError->Description;
                    $errorCode = $AddDomesticConsignmentWebResponse->AddDomesticConsignmentDeferredResult->Errors->UKMWebError->Code;
                } else if (count($AddDomesticConsignmentWebResponse->AddDomesticConsignmentDeferredResult->Errors->UKMWebError) > 0) {
                    foreach ($AddDomesticConsignmentWebResponse->AddDomesticConsignmentDeferredResult->Errors->UKMWebError as $errorDetails) {
                        $errorDesc .= $errorDetails->Description . ',';
                        $errorCode .= $errorDetails->Code . ',';
                    }
                } else {
                    $errorDesc = $AddDomesticConsignmentWebResponse->AddDomesticConsignmentDeferredResult->Errors->UKMWebError->Description;
                    $errorCode = $AddDomesticConsignmentWebResponse->AddDomesticConsignmentDeferredResult->Errors->UKMWebError->Code;
                }

                $this->error_list[] = $errorDesc;
                $output["STATUS"] = "ERROR";
                $output["MESSAGE"] = $errorDesc;
                return $output;
            } else
                $consignment->setApiData(print_r($AddDomesticConsignmentWebRequest, true), print_r($AddDomesticConsignmentWebResponse, true), 'UKMAIL_LABEL_SUCCESS');
        }

        $responseStringApi = print_r($AddDomesticConsignmentWebResponse, true);

        $consignmentNumber = $AddDomesticConsignmentWebResponse->AddDomesticConsignmentDeferredResult->ConsignmentNumber;

        $trackingReferenceNuamber = $BookCollectionWebResponse->BookCollectionResult->CollectionJobNumber;
        $consignment->setReference($trackingReferenceNuamber);
        $consignment->setAwb($consignmentNumber);
        $licence_plate_array[] = $consignmentNumber;



        $labelList = $AddDomesticConsignmentWebResponse->AddDomesticConsignmentDeferredResult->Labels->base64Binary;
        $pdf = new PdfBase(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $this->pdf = $pdf;

        $page_size = array(150, 100);

        if (sizeof($labelList) > 0 && is_array($labelList) && sizeof($this->error_list) <= 0) {

            foreach ($labelList as $label) {

                $data = $label;
                file_put_contents("file", $data, LOCK_EX);
                $this->pdf->SetX(1.0);
                $page_size = array(200, 150);
                $this->pdf->AddPage("L", $page_size);
                $this->pdf->Image('@' . $data, 1, 1, 210, 170, '', '', '', false, 900, '', false, false, 0, false, false, false, false, array());
            }

            $this->pdf->Output(SETTING_DIR_ASSETS . "pdf/" . date('Y_m_d') . '/' . $consignment->getId() . ".pdf", "F");
            $this->pdf->IncludeJS("print();");
            $output['STATUS'] = 'SUCCESS';
            $output['LABEL'] = date('Y_m_d') . '/' . $consignment->getId() . ".pdf";
            $output['TRACKING_NUMBER'] = $licence_plate_array;
            return $output;
        } else if (!empty($labelList) && sizeof($this->error_list) <= 0) {

            $data = $labelList;
            file_put_contents("file", $data, LOCK_EX);
            $this->pdf->SetX(1.0);
            $page_size = array(200, 150);
            $this->pdf->AddPage("L", $page_size);
            $this->pdf->Image('@' . $data, 1, 1, 210, 170, '', '', '', false, 900, '', false, false, 0, false, false, false, false, array());
            $this->pdf->Output(SETTING_DIR_ASSETS . "pdf/" . date('Y_m_d') . '/' . $consignment->getId() . ".pdf", "F");
            $this->pdf->IncludeJS("print();");
            $output['STATUS'] = 'SUCCESS';
            $output['LABEL'] = date('Y_m_d') . '/' . $consignment->getId() . ".pdf";
            $output['TRACKING_NUMBER'] = $licence_plate_array;
            return $output;
        } else {
            $consignment->setStatus(Consignment::STATUS_INVALID);
            $errorMessage = implode(', ', $this->error_list);
            $consignment->setMessage($errorMessage);
            $output["STATUS"] = "ERROR";
            $output["MESSAGE"] = $errorMessage;
            return $output;
        }
    }

    private function apiCollectionLabel($constant, Consignment $consignment) {

        $output = array();

        $ukmailfilter = new UkmailAuthenticationFilter();
        $list = $ukmailfilter->addDateCreatedFilter();
        $USERNAME = $constant["UKMAIL_USERNAME"];
        $PASSWORD = $constant["UKMAIL_PASSWORD"];
        $ACCOUNT = $constant["UKMAIL_ACCOUNT"];
        $UKMAIL_URL_AUTH = $constant["UKMAIL_URL_AUTH"];

        if (count($list) == 0) {
            $getToken = $this->getAuthenticationToken($consignment);
            if($getToken["STATUS"] == "ERROR"){
                return $getToken;
            }
            else
                $AuthenticationToken = $getToken["MESSAGE"];
            
        } else {
            $AuthenticationToken = $list[0]->getAuthenticationToken();
        }

        $UKMAIL_URL_COLLECTION = $constant["UKMAIL_URL_COLLECTION"];
        $BookCollectionWebRequest = new stdClass();
        $BookCollectionWebRequest->AuthenticationToken = $AuthenticationToken;
        $BookCollectionWebRequest->Username = $USERNAME;
        $BookCollectionWebRequest->AccountNumber = $ACCOUNT;
        $BookCollectionWebRequest->ClosedForLunch = "false";
        $BookCollectionWebRequest->EarliestTime = date('Y-m-d', strtotime($consignment->getCollectionDate())) . 'T' . $consignment->getCollectionStartTime() . ':00.000';
        $BookCollectionWebRequest->LatestTime = date('Y-m-d', strtotime($consignment->getCollectionDate())) . 'T' . $consignment->getCollectionEndTime() . ':00.000';
        $BookCollectionWebRequest->RequestedCollectionDate = date('Y-m-d', strtotime($consignment->getCollectionDate())) . 'T' . $consignment->getCollectionStartTime() . ':00.000';
        $BookCollectionWebRequest->SpecialInstructions = $consignment->getNotes();

        $BookCollection = new stdClass();
        $BookCollection->request = $BookCollectionWebRequest;
        $requestCookingStringApi = print_r($BookCollectionWebRequest, true);

        $soapClientCollection = new SoapClient($UKMAIL_URL_COLLECTION);
        $BookCollectionWebResponse = $soapClientCollection->BookCollection($BookCollection);
        $resultLabelAuth = $BookCollectionWebResponse->BookCollectionResult->Result;
        if (strtolower(trim($resultLabelAuth)) == "failed") {
            $output["STATUS"] = "ERROR";
            $output["MESSAGE"] = $errorDesc = $BookCollectionWebResponse->BookCollectionResult->Errors->UKMWebError->Description;
            $errorCode = $BookCollectionWebResponse->BookCollectionResult->Errors->UKMWebError->Code;
            $consignment->setApiData(print_r($BookCollectionWebRequest, true), print_r($BookCollectionWebResponse, true), 'UKMAIL_BOOKING_FAIL');
            if ($errorCode == "2050") {
                $getToken = $this->getAuthenticationToken($consignment);
                if($getToken["STATUS"] == "ERROR"){
                    return $getToken;
                }
            }
            return $output;
        } else
            $consignment->setApiData(print_r($BookCollectionWebRequest, true), print_r($BookCollectionWebResponse, true), 'UKMAIL_BOOKING_SUCCESS');

        //mail("mruga@oneworldexpress.com", "UKMAIL", $AuthenticationToken);
        if (count($this->error_list) <= 0) {
            $AddDomesticConsignmentWebRequest = new stdClass();
            $AddDomesticConsignmentWebRequest->AuthenticationToken = $AuthenticationToken;

            $AddDomesticConsignmentWebRequest->Username = $USERNAME;
            $AddDomesticConsignmentWebRequest->AccountNumber = $ACCOUNT;
            $AddDomesticConsignmentWebRequest->CollectionDate = date('Y-m-d', strtotime($consignment->getCollectionDate())) . 'T' . $consignment->getCollectionStartTime() . ':00.000';
            $AddDomesticConsignmentWebRequest->CollectionAddress->Address1 = $consignment->getSenderAddressLine1();
            $AddDomesticConsignmentWebRequest->CollectionAddress->Address2 = $consignment->getSenderAddressLine2();
            $AddDomesticConsignmentWebRequest->CollectionAddress->Address3 = $consignment->getSenderAddressLine3();
            $senderCountry = new Country($consignment->getSenderCountryId());
            if (trim($senderCountry->getIso()) == 'GB')
                $AddDomesticConsignmentWebRequest->CollectionAddress->CountryCode = 'GBR';
            else
                $AddDomesticConsignmentWebRequest->CollectionAddress->CountryCode = $senderCountry->getIso();



            $AddDomesticConsignmentWebRequest->CollectionAddress->County = '';
            $AddDomesticConsignmentWebRequest->CollectionAddress->PostalTown = $consignment->getSenderCity();
            $AddDomesticConsignmentWebRequest->CollectionAddress->Postcode = $consignment->getSenderPostCode();
            $AddDomesticConsignmentWebRequest->AlternativeRef = $consignment->getReference();

            $AddDomesticConsignmentWebRequest->CollectionBusinessName = $consignment->getSenderCompany();
            $AddDomesticConsignmentWebRequest->CollectionJobNumber = $BookCollectionWebResponse->BookCollectionResult->CollectionJobNumber;

            $AddDomesticConsignmentWebRequest->ConfirmationOfDelivery = true;
            if (trim($consignment->getSenderName()) !== "")
                $AddDomesticConsignmentWebRequest->CollectionContactName = $consignment->getSenderName();
            else
                $AddDomesticConsignmentWebRequest->CollectionContactName = $consignment->getSenderCompany();

            $AddDomesticConsignmentWebRequest->CollectionCustomerRef = $consignment->getHawb();
            $AddDomesticConsignmentWebRequest->CollectionEmail = $consignment->getEmail();

            $AddDomesticConsignmentWebRequest->CollectionOpenLunchtime = "false";
            $AddDomesticConsignmentWebRequest->CollectionLatestPickup = date('Y-m-d', strtotime($consignment->getCollectionDate())) . 'T' . $consignment->getCollectionEndTime() . ':00.000';
            $AddDomesticConsignmentWebRequest->CollectionSpecialInstructions1 = $consignment->getDescription();
            $AddDomesticConsignmentWebRequest->CollectionSpecialInstructions2 = $consignment->getNotes();
            $AddDomesticConsignmentWebRequest->CollectionTelephone = $consignment->getTelephone();
            $AddDomesticConsignmentWebRequest->CollectionTimeReady = date('Y-m-d', strtotime($consignment->getCollectionDate())) . 'T' . $consignment->getCollectionStartTime() . ':00.000';
            $AddDomesticConsignmentWebRequest->DeliverySpecialInstructions1 = $consignment->getNotes();
            $AddDomesticConsignmentWebRequest->DeliverySpecialInstructions2 = '';
            $AddDomesticConsignmentWebRequest->DescriptionOfGoods1 = '';
            $AddDomesticConsignmentWebRequest->DescriptionOfGoods2 = '';
            $AddDomesticConsignmentWebRequest->ServiceKey = '451';

            $AddDomesticConsignmentWebRequest->DeliveryContactName = $consignment->getContact();
            $AddDomesticConsignmentWebRequest->DeliveryAddress->Address1 = $consignment->getAddressLine1();
            $AddDomesticConsignmentWebRequest->DeliveryAddress->Address2 = $consignment->getAddressLine2();
            $AddDomesticConsignmentWebRequest->DeliveryAddress->Address3 = $consignment->getAddressLine3();
            if (trim($this->country->getIso()) == 'GB' || trim($this->country->getIso()) == '')
                $AddDomesticConsignmentWebRequest->DeliveryAddress->CountryCode = 'GBR';
            else
                $AddDomesticConsignmentWebRequest->DeliveryAddress->CountryCode = $this->country->getIso();

            $AddDomesticConsignmentWebRequest->DeliveryAddress->County = '';
            $AddDomesticConsignmentWebRequest->DeliveryAddress->PostalTown = $consignment->getCity();
            $AddDomesticConsignmentWebRequest->DeliveryAddress->Postcode = $consignment->getPostCode();
            $AddDomesticConsignmentWebRequest->DeliveryBusinessName = $consignment->getCompany();
            $AddDomesticConsignmentWebRequest->DeliveryTelephone = $consignment->getTelephone();
            $AddDomesticConsignmentWebRequest->DeliveryEmail = $consignment->getEmail();

            $AddDomesticConsignmentWebRequest->BookIn = "false";
            $AddDomesticConsignmentWebRequest->ConfirmationEmail = $consignment->getEmail();
            $AddDomesticConsignmentWebRequest->ConfirmationTelephone = $consignment->getTelephone();


            $AddDomesticConsignment = new stdClass();
            $AddDomesticConsignment->request = $AddDomesticConsignmentWebRequest;

            $UKMAIL_URL_CONSIGNMENT = $constant["UKMAIL_URL_CONSIGNMENT"];

            $soapClientConsignment = new SoapClient($UKMAIL_URL_CONSIGNMENT);
            $AddDomesticConsignmentWebResponse = $soapClientConsignment->AddSendToThirdParty($AddDomesticConsignment);

            $resultLabel = $AddDomesticConsignmentWebResponse->AddSendToThirdPartyResult->Result;
            if (strtolower(trim($resultLabel)) == "failed") {
                $consignment->setApiData(print_r($AddDomesticConsignmentWebRequest, true), print_r($AddDomesticConsignmentWebResponse, true), 'UKMAIL_LABEL_FAIL');

                $errorDesc = $AddDomesticConsignmentWebResponse->AddSendToThirdPartyResult->Errors->UKMWebError->Description;
                $errorCode = $AddDomesticConsignmentWebResponse->AddSendToThirdPartyResult->Errors->UKMWebError->Code;
                $this->error_list[] = $errorDesc;
                $output["STATUS"] = "ERROR";
                $output["MESSAGE"] = $errorDesc;
                return $output;
            } else {
                $consignment->setApiData(print_r($AddDomesticConsignmentWebRequest, true), print_r($AddDomesticConsignmentWebResponse, true), 'UKMAIL_LABEL_SUCCESS');
                $consignmentNumber = $AddDomesticConsignmentWebResponse->AddSendToThirdPartyResult->ConsignmentNumber;

                $trackingReferenceNuamber = $BookCollectionWebResponse->BookCollectionResult->CollectionJobNumber;
                $consignment->setCollectionConfirmationNo($trackingReferenceNuamber);
                $consignment->setAwb($consignmentNumber);
                $licence_plate_array[] = $consignmentNumber;
                $output['STATUS'] = 'SUCCESS';
                $output['LABEL'] = "";
                $output['TRACKING_NUMBER'] = $licence_plate_array;
                return $output;
            }
        }
    }

    public function tracking($trackingNumber, $trackBy = 'parcel', $EDI = false) {
        require_once("../includes/labels/ukmailtrackingstatus.class.php");
        $consignmentFilter = new ConsignmentFilter();
        $consignmentFilter->addAwbArrayFilter($trackingNumber);
        $result = $consignmentFilter->getConList('*');
        $consignment = $result[0];

        $serviceAgentConstantFilter = new ServiceConstantValueFilter();
        $serviceAgentConstantFilter->addFilter("service_id = '" . $consignment->getServiceId() . "' AND agent_id = '" . $consignment->getAgentId() . "' ");
        $serviceAgentConstant = $serviceAgentConstantFilter->getConstantList("c.constant 'constant_name', scv.constant_value 'constant_value'");

        if (count($serviceAgentConstant) > 0) {
            foreach ($serviceAgentConstant as $serviceAgentConstantData) {
                $this->constants[$serviceAgentConstantData->getConstantName()] = $serviceAgentConstantData->getConstantValue();
            }
        }

        $dateDelivered = '';
        $UKMAIL_USERNAME = $this->constants["UKMAIL_USERNAME"];
        $UKMAIL_PASSWORD = $this->constants["UKMAIL_PASSWORD"];
        $UKMAIL_ACCOUNT_NUMBER = $this->constants["UKMAIL_ACCOUNT"];
        $UKMAIL_URL_AUTH = $this->constants["UKMAIL_URL_AUTH"];
        $UKMAIL_URL_TRACKING = $this->constants["UKMAIL_URL_TRACKING"];
        $innerHtmlNew = '';

        try {
            $LoginWebRequest = new stdClass();
            $LoginWebRequest->Username = $UKMAIL_USERNAME;
            $LoginWebRequest->Password = $UKMAIL_PASSWORD;
            $Login->loginWebRequest = $LoginWebRequest;
            $soapClient = new SoapClient($UKMAIL_URL_AUTH);
            $LoginResponse = $soapClient->Login($Login);

            $AuthenticationToken = $LoginResponse->LoginResult->AuthenticationToken;
            $client = new SoapClient(trim($UKMAIL_URL_TRACKING));
            $params->UserName = $this->constants["UKMAIL_USERNAME_TRACKING"];
            $params->Password = $this->constants["UKMAIL_PASSWORD_TRACKING"];
            $params->Token = $AuthenticationToken;
            $params->ConsignmentNumber = $trackingNumber;
            $params->ConsignmentKey = 0;
            // $params->MaxResults 	= '100';
            $result = $client->ConsignmentTrackingGetFullDetailsV3($params);

            // print_r($result); die;

            if ($result->ConsignmentTrackingGetFullDetailsV3Result->ResultState == 'Failed') {
                $result->ConsignmentTrackingGetFullDetailsV3Result->Errors->Error->FriendlyMessage;
            }

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
                
            if (count($result->ConsignmentTrackingGetFullDetailsV3Result->FullConsignmentDetails->ConsignmentStatuses->GetConsignmentDetailsStatus) > 0 && $entityId >0) {
                
                $parcelEntity = new Parcel($entityId);
                $finalStatusCode = $parcelEntity->getParcelStatusCode();
                
                if (count($result->ConsignmentTrackingGetFullDetailsV3Result->FullConsignmentDetails->ConsignmentPods->GetFullConsignmentDetailsPodV2) > 0) {
                    foreach ($result->ConsignmentTrackingGetFullDetailsV3Result->FullConsignmentDetails->ConsignmentPods as $poddata) {
                        $Signatory = $poddata->PodRecipientName;
                    }
                }

                foreach ($result->ConsignmentTrackingGetFullDetailsV3Result->FullConsignmentDetails->ConsignmentStatuses->GetConsignmentDetailsStatus as $consignmentTrackingData) {
                    
                    $DateTime = $consignmentTrackingData->StatusTimeStamp;
                    $EventCode = $consignmentTrackingData->StatusCode;
                    $EventDescription = $consignmentTrackingData->StatusDescription;
                    $ServiceAreaDescription = $consignmentTrackingData->StatusDescription;
                                      
                    // Dont enter any other event code if it is against 148 Event Code
                    if($carrierCodeCarrierReceived == $EventCode)
                    {
                        continue;
                    }
                    
                    $deliveredArray = array('5','DT01','DT02','DT03','DT04','DT05','DT06','DT07','DT08','DT09','DT10');  
                    
                    ////////////////////// Carrier Received ////////////////////////////////
                    if($carrierReceivedCheck == 1 && $EventCode != '1'  && $EventCode != '')
                    {
                        $spTrackingStatus = '148';
                        $carrierReceivedCheck = 0 ;                        
                    }
                    else
                    {
                        $spTrackingStatus = UkMailTrackingStatus::getOweStatusCode($EventCode);
                    }            
                    ////////////////////// Carrier Received ////////////////////////////////
                   $result = $tracking->saveTrackPoint($entityId, $trackBy, $DateTime, $trackingNumber, $spTrackingStatus, $ServiceAreaDescription, $EventCode, $EventDescription, $deliveredArray, $finalStatusCode);                                        
                   if($result == true)
                    {
                        break;
                    }
                }               
                $tracking->saveConsignmentTrackingStatus($trackingNumber, 'UkMailTrackingStatus');                
            }
        } catch (Exception $ex) {
            
        }
    }

    public function sendData($tracking_numbers = array()) {
        
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
        $output = array();
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

        $consignmentNumber = $consignment->getAwb();

        $ukmailfilter = new UkmailAuthenticationFilter();
        $list = $ukmailfilter->addDateCreatedFilter();
        $UKMAIL_USERNAME = $this->constants["UKMAIL_USERNAME"];
        $UKMAIL_PASSWORD = $this->constants["UKMAIL_PASSWORD"];
        $UKMAIL_URL_AUTH = $this->constants["UKMAIL_URL_AUTH"];

        if (count($list) == 0) {
            $getToken = $this->getAuthenticationToken($consignment);
            if($getToken["STATUS"] == "ERROR"){
                return $getToken;
            }
            else
                $AuthenticationToken = $getToken["MESSAGE"];
        } else {
            $AuthenticationToken = $list[0]->getAuthenticationToken();
        }

        $CancelConsignmentRequest = new stdClass();
        $CancelConsignmentRequest->AuthenticationToken = $AuthenticationToken;
        $CancelConsignmentRequest->Username = $UKMAIL_USERNAME;
        $CancelConsignmentRequest->ConsignmentNumber = $consignmentNumber;
        $CancelConsignment = new stdClass();
        $CancelConsignment->request = $CancelConsignmentRequest;
        $UKMAIL_URL_CANCEL = $this->constants["UKMAIL_URL_CANCEL"];
        $soapCancelConsignment = new SoapClient($UKMAIL_URL_CANCEL);
        if ($consignment->getShipmentType() == "C") {
            $CancelConsignmentWebResponse = $soapCancelConsignment->CancelReturn($CancelConsignment);
            $requestObject = "CancelReturnResult";            
        } else {
            $CancelConsignmentWebResponse = $soapCancelConsignment->CancelConsignment($CancelConsignment);
            $requestObject = "CancelConsignmentResult";            
        }
        if (strtolower(trim($CancelConsignmentWebResponse->$requestObject->Result)) == 'failed') {
            $consignment->setApiData(print_r($CancelConsignmentRequest, true), print_r($CancelConsignmentWebResponse, true), 'UKMAIL_CALCEL_FAILED');
            $output["STATUS"] = "ERROR";
            $errorCode = $CancelConsignmentWebResponse->$requestObject->Errors->UKMWebError->Code;
            $output["MESSAGE"] = $CancelConsignmentWebResponse->$requestObject->Errors->UKMWebError->Description;
            if ($errorCode == "2050") {
                $getToken = $this->getAuthenticationToken($consignment);
                if($getToken["STATUS"] == "ERROR"){
                    return $getToken;
                }
            }
            return $output;
        }
        $consignment->setApiData(print_r($CancelConsignmentRequest, true), print_r($CancelConsignmentWebResponse, true), 'UKMAIL_CALCEL_SUCCESS');

        $output["STATUS"] = "SUCCESS";
        return $output;
    }

    private function getAuthenticationToken($consignment) {
        $UKMAIL_USERNAME = $this->constants["UKMAIL_USERNAME"];
        $UKMAIL_PASSWORD = $this->constants["UKMAIL_PASSWORD"];
        $UKMAIL_URL_AUTH = $this->constants["UKMAIL_URL_AUTH"];
        $LoginWebRequest = new stdClass();
        $LoginWebRequest->Username = $UKMAIL_USERNAME;
        $LoginWebRequest->Password = $UKMAIL_PASSWORD;
        $Login->loginWebRequest = $LoginWebRequest;
        $soapClient = new SoapClient($UKMAIL_URL_AUTH);
        $authCountToken = 0;
        do {
            $LoginResponse = $soapClient->Login($Login);

            if (strtolower(trim($LoginResponse->LoginResult->Result)) == 'failed') {

                $consignment->setApiData(print_r($LoginWebRequest, true), print_r($LoginResponse, true), 'UKMAIL_AUTH_FAIL');
                $authCountToken++;
                if ($authCountToken > 2)
                $output["STATUS"] = "ERROR";
                $output["MESSAGE"] = $LoginResponse->LoginResult->Errors->UKMWebError->Description;
                return $output;
            } else {
                $consignment->setApiData(print_r($LoginWebRequest, true), print_r($LoginResponse, true), 'UKMAIL_AUTH_SUCCESS');
                break;
            }
        } while ($authCountToken <= 2);


        $AuthenticationToken = $LoginResponse->LoginResult->AuthenticationToken;
        $resultLabelAuth = $LoginResponse->LoginResult->Result;

        $UkmailAuthentication = new UkmailAuthentication();

        $UkmailAuthentication->setAuthenticationToken($AuthenticationToken);
        $UkmailAuthentication->setDateCreated(strtotime(date("Y-m-d H:i:s")));
        $UkmailAuthentication->setUserAccount($this->user->getUserAccountId());
        $UkmailAuthentication->save();
        $output["STATUS"] = "SUCCESS";
        $output["MESSAGE"] = $AuthenticationToken;
        return $output;
    }

}
