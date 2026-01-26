<?php

include_classes([
    'serviceconstantvalue.class',
    'serviceconstantvaluefilter.class',
    'carrierdatafilelog.class',
    'carrierdatafilelogfilter.class'
]);

class Nzpost implements CarrierService {

    private $pdf;
    public $debugMode = true;
    protected $apiUrl;
    protected $apiName;
    protected $apiDescription;
    protected $apiUSERID;
    protected $apiclientId;
    protected $apisecret;

    public function validation(Consignment $consignment, Services $service, Country $country) {
        
    }

    public function remoteareas($consignment, $carrierObject, $sender) {
        
    }

    public function label($consignment, $labelType = 'pdf', $size = '100x150') {


        $output = array();
        $this->serviceValues = new Services($consignment->getServiceId());
        $this->country = new Country($consignment->getCountryId());
        $this->user = SessionManager::getUser();
        $parcels = $consignment->getParcels();

        /*
         * Agent Values
         */

        $agentFilter = new ServiceAgentMappingDataFilter();
        $agentFilter->addFilter("serviceid = '" . $consignment->getServiceId() . "' AND agentid = '" . $consignment->getAgentId() . "' ");
        $agentList = $agentFilter->getList();
        if (count($agentList) > 0) {
            $this->agentValues = $agentList[0];
        }

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

        if (trim(@$this->constants['NZ_CLIENT_ID']) == '' || trim(@$this->constants['NZ_CLIENT_SECRET']) == '') {
            $output['STATUS'] = 'ERROR';
            $output['MESSAGE'] = "Please check service constants. Please contact to itsupport@oneworldexpress.com";
            return $output;
        }

        $this->service = new Services($consignment->getServiceId());
        $this->country = new Country($consignment->getCountryId());


        

        $this->apiUrl = "https://parcellabel-4-0-uat.au-s1.cloudhub.io/api/labelsV4/";
        $this->grantType = "client_credentials";
        $this->bearer_token = "Bearer";
        $this->username = "standard";
        $this->clientId = $this->constants['NZ_CLIENT_ID'];
        $this->clientSecret = $this->constants['NZ_CLIENT_SECRET'];
        
        $logo = User::getUserCompanyImages(false, $consignment->getUserId());
        $output = array();
        $parcel_list = $consignment->getParcels();
        if (count($parcel_list) > 1) {
            $output['STATUS'] = 'ERROR';
            $output['ERROR'][] = "This service support single piece shipment";
            $output['MESSAGE'] = "This service support single piece shipment";
            return $output;
        }
        $authenticationInfo = $this->authenticateionToken();
        
        if (trim($authenticationInfo->access_token) != '') {
            $accessToken = trim($authenticationInfo->access_token);
            $output["STATUS"] = "SUCCESS";
            $parcel_list = $consignment->getParcels();
            $handling = $this->service->getCode();
            $hscode = '';
            $returnItemWanted = 0;
                $hscode = '';

            /*$ConsignmentHscodeFilter = new ConsignmentHscodeFilter();
            $ConsignmentHscodeFilter->addconsignmentFilter($consignment->getId());
            $hscodeList = $ConsignmentHscodeFilter->getList();
            if (count($hscodeList) > 0) {
                $hscodeList = $hscodeList[0];
                $hscode = $hscodeList->getHscode();
            }
*/

            $parcel_count = sizeof($parcel_list);
            $parcel_idx = 0;
            $parcelContents = array();
            $parcelDims = array();
            //echo "label-s:".date("Y-m-d h:i:s u")."\t";
            foreach ($parcel_list as $parcel) {
                $parcelContents[] = array(
                    "content_number" => 1,
                    "description" => $consignment->getDescription(),
                    "harmonised_system_tariff" => $parcel->getTarrifNo(),
                    "quantity" => 1,
                    "weight_kg" => (int) $consignment->getWeight(),
                    "value" => ((int) $consignment->getValue() > 0 ? (int) $consignment->getValue() : 5)
                );
                $parcelDims = array(
                    "length_cm" => (int)(($parcel->getLength() > 1) ? $parcel->getLength() : 10),
                    "width_cm" => (int)(($parcel->getWidth() > 1) ? $parcel->getWidth() : 10),
                    "height_cm" => (int)(($parcel->getHeight() > 1) ? $parcel->getHeight() : 10)
                );
            }
            
            if (trim($this->country->getIso()) == 'AU') {
                $pickUpAddress = array(
                    "company_name" => "One World Express Inc Ltd.",
                    "street" => "Level 1, 923 Bourke St",
                    "suburb" => "",
                    "city" => "Waterloo",
                    "state" => "NSW",
                    "country_code" => "AU",
                    "postcode" => "2017"
                );
            } else if (trim($this->country->getIso()) == 'NZ') {
                $pickUpAddress = array(
                    "company_name" => "One World Express Inc Ltd.",
                    "street" => "Private Bag 211055",
                    "suburb" => "Laurence Stevens Drive",
                    "city" => "Auckland",
                    "state" => "",
                    //"country_code" => "NZ",
                    "country_code" => "NI",
                    "postcode" => "2154"
                );
            }
            $request1 = array(
                "sender_reference_1" => trim($consignment->getHawb()),
                "sender_reference_2" => trim($consignment->getReference()),
                "sender_details" => array(
                    "name" => "Yasmin",
                    "phone" => "02088676060",
                    "email" => "itsupport@oneworldexpress.com",
                    "fax" => "",
                    "customs_code" => "",
                    "signatory" => ""
                ),
                "pickup_address" => $pickUpAddress,
                "receiver_details" => array(
                    "name" => trim($consignment->getContact()),
                    "phone" => trim($consignment->getTelephone()),
                    "email" => trim($consignment->getEmail()),
                    "fax" => "",
                    "vat_number" => "",
                    "registration_number" => ""
                ),
                "delivery_address" => array(
                    "company_name" => trim($consignment->getCompany()),
                    "building_name" => "", 
                    "street" => trim($consignment->getAddressLine1() . " " . trim($consignment->getAddressLine2())),
                    "suburb" => "",
                    "city" => trim($consignment->getCity()),
                    "country_code" => trim($this->country->getIso()),
                    "state" => (trim($consignment->getState()) <> '' ? $consignment->getState() : $consignment->getAddressLine3()),
                    "postcode" => trim($consignment->getPostcode()),
                    "instructions" => trim($consignment->getNotes())
                ),
                "parcel_details" => [array(
                //"service_code"=>"TDEAUN",  // Working for standard service for australia  signature no ATL
                //"service_code"=>"TDEAUS",  //  signature with ATL
                //"service_code" => "TDEAUT", //no signature (but this has mandatory delivery instructions which need to go into column AT)
                //EXPRESS SERVICE
                //"service_code"=>"TDEAXS",  // Working for Express service SIGNATURE ON DELIVERY REQUIRED
                ///"service_code"=>"TDEAXN",    // Working for Express service No signature ATL
                //Extrasssss
                //"service_code"=>"TDEAXS",
                //"service_code"=>"TDEPXAU",
                //"service_code"=>"TDEPAUN",
                "service_code" => trim($this->service->getCode()), //"TDEPAU",
                "undeliverable_instructions" => "RETURN",
                "cms_codes" => array(),
                "insurance_required" => false,
                "nature_of_transaction_code" => "32",
                //11 = Sales of goods; 21 = Returned Goods; 31 = Gift; 32 = Commercial Sample; 91 = Documents; 999 = Other
                "postage_paid_amount" => 0.01,
                "currency" => "NZD",
                "receiver_charging_arrangement" => "DDU",
                "dimensions" => $parcelDims,
                "dangerous_goods" => array(
                    "hazard_class" => "",
                    "type_code" => "0000"
                ),
                "parcel_contents" => $parcelContents
                    )],
            );
           
            $request1 = json_encode($request1);
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $this->apiUrl );
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_HEADER, FALSE);
            curl_setopt($ch, CURLOPT_POST, TRUE);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $request1);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                "Content-Type: application/json",
                "client_id: " . $this->clientId,
                "user_name: " . $this->username,
                "Authorization: Bearer " . $accessToken
            ));

            $orderResponse = json_decode(curl_exec($ch));
            $consignment->setApiData(json_encode($request1), print_r($orderResponse, true), 'NZPOST_SHIPMENT_SUCCESS');
            curl_close($ch);
            if ($orderResponse->success) {
                $fileName = SETTING_DIR_ASSETS . "pdf/" . date('Y_m_d') . '/' . $consignment->getId() . ".pdf";
                $labelDataFile = base64_decode($orderResponse->label[0]->data);
                $awb = $orderResponse->label[0]->tracking_id;
                file_put_contents($fileName, $labelDataFile);
                $consignment->save();

                $output["STATUS"]               =   'SUCCESS';
                $output["LABEL"]                =   date('Y_m_d') . '/' . $consignment->getId() . ".pdf";
                $output["TRACKING_NUMBER"][]    =   $awb;
            } else {
                $output["STATUS"] = 'ERROR';
                if (isset($orderResponse->errors[0])) {
                    $output['ERROR'][] = $orderResponse->errors[0]->code . " : " . $orderResponse->errors[0]->details;
                    $output["MESSAGE"] = $orderResponse->errors[0]->code . " : " . $orderResponse->errors[0]->details;
                } else {
                    $output['ERROR'][] = $orderResponse->errors->code . " : " . $orderResponse->errors->details;
                    $output["MESSAGE"] = $orderResponse->errors->code . " : " . $orderResponse->errors->details;
                }
            }

            return $output;
        } else {
            $output["STATUS"] = 'ERROR';
            $output['ERROR'][] = $authenticationInfo->error;
            $output["MESSAGE"] = $authenticationInfo->error;
            return $output;
        }
    }

    /*
     * Add consignment to label
     */

    private function authenticateionToken() {
        //set POST variables
        $url = 'https://oauth.nzpost.co.nz/as/token.oauth2';
        $fields = array(
            'grant_type' => $this->grantType,
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret
        );

        //url-ify the data for the POST
        foreach ($fields as $key => $value) {
            $fields_string .= $key . '=' . $value . '&';
        }
        rtrim($fields_string, '&');
        $ch = curl_init();
        //set the url, number of POST vars, POST data
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, count($fields));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
        //execute post
        $result = curl_exec($ch);
        //close connection
        curl_close($ch);
        return $tokenReponse = json_decode($result);
    }

    public function tracking($trackingNumber, $trackBy, $EDI) {
        
    }

    public function sendData($tracking_numbers = array()) {
        
    }

    public function manifest($consignment) {
        
    }

    public function preAdvice($consignment) {
        
    }

    public function recycledShipment($consignment) {
        
    }

}

?>