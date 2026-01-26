<?php

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

        if (trim(@$this->constants['UPSCOL_ACCESSKEY']) == '' || trim(@$this->constants['UPSCOL_USERID']) == '' || trim(@$this->constants['UPSCOL_PASSWORD']) == ''
                || trim(@$this->constants['UPSCOL_ACCOUNTNO']) == '') {
            $output['STATUS'] = 'ERROR';
            $output['MESSAGE'] = "Please check service constants. Please contact to itsupport@oneworldexpress.com";
            return $output;
        }

        if (trim(@$this->constants['INTEGRATION_TYPE']) == 'API') {
            $output = $this->apiCollectionLabel($this->constants, $consignment);
        }

        return $output;
    }

    private function apiCollectionLabel($constant, Consignment $consignment) {

        $output = array();
      
        $senderCountry = new Country($consignment->getSenderCountryId());
        $senderCountryIso = $senderCountry->getIso();

      
        $requestoption['RequestOption'] = '1';
        $request['Request'] = $requestoption;
        $request['RatePickupIndicator'] = 'N';
        //$account['AccountNumber']= '0E463V';
        $account['AccountNumber'] = $this->constants['UPSCOL_ACCOUNTNO'];//'V40V96';
        $account['AccountCountryCode'] = 'DE';
        $shipper['Account'] = $account;
        $request['Shipper'] = $shipper;
        $pickupdateinfo['CloseTime'] = str_replace(":", "", $consignment->getCollectionEndTime());
        $pickupdateinfo['ReadyTime'] = str_replace(":", "", $consignment->getCollectionStartTime());

        $pickupdateinfo['PickupDate'] = str_replace("-", "", date("Y-m-d", strtotime($consignment->getCollectionDate())));
        $request['PickupDateInfo'] = $pickupdateinfo;

        $pickupaddress['CompanyName'] = utf8_encode($consignment->getCompany());
        $pickupaddress['ContactName'] = utf8_encode($consignment->getContact());

        $pickupaddress['AddressLine'] = utf8_encode($consignment->getSenderAddressLine1()) . " " .
                                        utf8_encode($consignment->getSenderAddressLine2()) . " " .
                                        utf8_encode($consignment->getSenderAddressLine3());

        $pickupaddress['Room'] = '';
        $pickupaddress['Floor'] = '';
        $pickupaddress['City'] = utf8_encode($consignment->getSenderCity());
        $pickupaddress['StateProvince'] = utf8_encode($consignment->getSenderAddressLine3());
        $pickupaddress['Urbanization'] = '';
        $pickupaddress['PostalCode'] = utf8_encode($consignment->getSenderPostCode());
        $pickupaddress['CountryCode'] = $senderCountryIso;


        if (trim($consignment->getCompany()) == '')
            $pickupaddress['ResidentialIndicator'] = 'Y';
        else
            $pickupaddress['ResidentialIndicator'] = 'N';

        $pickupaddress['PickupPoint'] = '';
        $phone['Number'] = $consignment->getSenderTelePhone();
        $phone['Extension'] = '';
        $pickupaddress['Phone'] = $phone;
        $request['PickupAddress'] = $pickupaddress;
        $request['AlternateAddressIndicator'] = 'Y';
        $pickuppiece['ServiceCode'] = '001';
        $pickuppiece['Quantity'] = $consignment->getNumberPieces();
        $pickuppiece['DestinationCountryCode'] = $senderCountryIso;
        $pickuppiece['ContainerCode'] = '01';
        $request['PickupPiece'] = $pickuppiece;
        $totalweight['Weight'] = $consignment->getWeight();
        $totalweight['UnitOfMeasurement'] = 'KGS';
        $request['TotalWeight'] = $totalweight;

        $request['OverweightIndicator'] = 'N';
        $request['PaymentMethod'] = '01';
        $request['SpecialInstruction'] = '';
        $request['ReferenceNumber'] = utf8_encode($consignment->getHawb());
        $cnfrmemailaddr = array
            (
            utf8_encode($consignment->getEmail()),
            "ITSupport@oneworldexpress.com"
        );
        $notification['ConfirmationEmailAddress'] = $cnfrmemailaddr;
        $notification['UndeliverableEmailAddress'] = '';
        $request['Notification'] = $notification;



        $mode = array
            (
            'soap_version' => 'SOAP_1_1', // use soap 1.1 client
            'trace' => 1
        );


        $client = new SoapClient($this->wsdl, $mode);
        $client->__setLocation($this->endpointurl);

        $usernameToken['Username'] = $this->userid;
        $usernameToken['Password'] = $this->constants["UPSCOL_PASSWORD"];
        $serviceAccessLicense['AccessLicenseNumber'] = $this->constants["UPSCOL_ACCESSKEY"];
        $upss['UsernameToken'] = $usernameToken;
        $upss['ServiceAccessToken'] = $serviceAccessLicense;

        $header = new SoapHeader('http://www.ups.com/XMLSchema/XOLTWS/UPSS/v1.0', 'UPSSecurity', $upss);
        $client->__setSoapHeaders($header);

        if (strcmp($this->operation, "ProcessPickupCreation") == 0) {


            $resp = $client->__soapCall($this->operation, array($request));

            //print_r($resp);

            $consignment->setApiData(json_encode($request), json_encode($resp), 'ups collection');

            if ($resp->Response->ResponseStatus->Description == 'Success') {
                return "SUCCESS||" . $resp->PRN;
            } else {
                return "ERROR||";
            }

            //get status
            //echo "Response Status: " . $resp->Response->ResponseStatus->Description ."\n";
            //save soap request and response to file
            //echo "Output File name : " .  $outputFileName . "<br><br>";
            //$fw = fopen($outputFileName , 'w');
            //fwrite($fw , "Request: \n" . $client->__getLastRequest() . "\n");
            //fwrite($fw , "Response: \n" . $client->__getLastResponse() . "\n");
            //fclose($fw);
        }
    }

    public function tracking($trackingNumber, $trackBy = 'parcel', $EDI = false) {
        
    }

    public function sendData($tracking_numbers = array()) {
        
    }

    public function manifest($consignment) {
        
    }

    public function preAdvice($consignment) {
        
    }

    public function recycledShipment($consignment) {
        $output = array();

        $output["STATUS"] = "SUCCESS";
        return $output;
    }

}
