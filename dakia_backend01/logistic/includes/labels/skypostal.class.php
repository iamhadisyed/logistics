<?php

class Skypostal implements CarrierService {

    private $pdf;
    private $serviceValues = null;
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

        if (trim(@$this->constants['INTEGRATION_TYPE']) == '' || trim(@$this->constants['USER_CODE']) == '' || trim(@$this->constants['COPA_ID']) == '' || trim(@$this->constants['USER_KEY']) == ''
                || trim(@$this->constants['APP_KEY']) == '' || trim(@$this->constants['MERCHANT_NAME']) == '' || trim(@$this->constants['USERNAME']) == '') {
            $output['STATUS'] = 'ERROR';
            $output['MESSAGE'] = "Please check service constants. Please contact to itsupport@oneworldexpress.com";
            return $output;
        }

        if (trim(@$this->constants['INTEGRATION_TYPE']) == 'API') {
            $output = $this->apiLabel($consignment);
        }

        return $output;
    }

    public function apiLabel(Consignment $consignment) {
        $output = array();
        
        $idNumber = "";
        $idsearchString = "";
        $iata_code = "";
        $ctry_iso_code = "";
        if($this->country->getIso() == "BR"){
            $boxid = "522697";
            $rate_service_code = "3";
            $idNumber = "05706854866";
            $idsearchString = "This is a test with a CPF:05706854866 included";
        }
        else if($this->country->getIso() == "MX"){
            $boxid = "751896";
            $rate_service_code = "1";
        }
        else if($this->country->getIso() == "CL"){
            $boxid = "751897";
            $rate_service_code = "1";
        }
        else if($this->country->getIso() == "CO"){
            $boxid = "751910";
            $rate_service_code = "1";
        }
        else if($this->country->getIso() == "US"){
            $boxid = "909425";
            $rate_service_code = "201";
            $iata_code = "MIA";
            $ctry_iso_code = $this->country->getIso();
        }

        $senderCountry = new Country($consignment->getSenderCountryId());
        $parcel_list = $consignment->getParcels();
        $requestArray = array(
            'user_info' =>
            array(
                'user_code' => $this->constants["USER_CODE"],//595,
                'user_key' => $this->constants["USER_KEY"],//'52C31r82l2O0p5Rk0RXGoIR3978k1l',
                'app_key' => $this->constants["APP_KEY"], //'ys19DwC4F31L724v06744i8stqu6q50s',
            ),
            'shipment_info' =>
            array(
                'box_id' => $boxid,
                'copa_id' => $this->constants["COPA_ID"],//621,
                'ssa_copa_id' => NULL,
                'merchant' =>
                array(
                    'name' => $this->constants["MERCHANT_NAME"],//'One World',
                    'email' => $this->constants["USERNAME"],//'MERCHANT@ONEWORLD.COM',
                    'address' =>
                    array(
                        'country_code' => NULL,
                        'country_iso_code' => NULL,
                        'country_name' => NULL,
                        'state_code' => NULL,
                        'state_name' => NULL,
                        'county_code' => NULL,
                        'county_name' => NULL,
                        'city_code' => 0,
                        'city_name' => NULL,
                        'zip_code' => NULL,
                        'neighborhood' => NULL,
                        'address_01' => NULL,
                        'address_02' => NULL,
                        'address_03' => NULL,
                    ),
                    'return_address' =>
                    array(
                        'country_code' => NULL,
                        'country_iso_code' => NULL,
                        'country_name' => NULL,
                        'state_code' => NULL,
                        'state_name' => NULL,
                        'county_code' => NULL,
                        'county_name' => NULL,
                        'city_code' => 0,
                        'city_name' => NULL,
                        'zip_code' => NULL,
                        'neighborhood' => NULL,
                        'address_01' => 'Address to return the package',
                        'address_02' => NULL,
                        'address_03' => NULL,
                    ),
                    'phone' =>
                    array(
                        0 =>
                        array(
                            'phone_type' => 1,
                            'phone_number' => '11111111',
                        ),
                    ),
                ),
                'shipper' =>
                array(
                    'name' => $consignment->getSenderName(),
                    'email' => $consignment->getSenderEmail(),
                    'address' =>
                    array(
                        'country_code' => NULL,
                        'country_iso_code' => $senderCountry->getIso(),
                        'country_name' => NULL,
                        'state_code' => NULL,
                        'state_name' => NULL,
                        'county_code' => NULL,
                        'county_name' => NULL,
                        'city_code' => 0,
                        'city_name' => $consignment->getSenderCity(),
                        'zip_code' => $consignment->getSenderPostcode(),
                        'neighborhood' => NULL,
                        'address_01' => $consignment->getSenderAddressLine1(),
                        'address_02' => $consignment->getSenderAddressLine2(),
                        'address_03' => $consignment->getSenderAddressLine3(),
                    ),
                    'return_address' =>
                    array(
                        'country_code' => NULL,
                        'country_iso_code' => $senderCountry->getIso(),
                        'country_name' => NULL,
                        'state_code' => NULL,
                        'state_name' => NULL,
                        'county_code' => NULL,
                        'county_name' => NULL,
                        'city_code' => 0,
                        'city_name' => $consignment->getSenderCity(),
                        'zip_code' => $consignment->getSenderPostcode(),
                        'neighborhood' => NULL,
                        'address_01' => $consignment->getSenderAddressLine1(),
                        'address_02' => $consignment->getSenderAddressLine2(),
                        'address_03' => $consignment->getSenderAddressLine3(),
                    ),
                    'phone' =>
                    array(
                    ),
                ),
                'sender' =>
                array(
                    'name' => $consignment->getSenderName(),
                    'email' => $consignment->getSenderEmail(),
                    'address' =>
                    array(
                        'country_code' => NULL,
                        'country_iso_code' => $senderCountry->getIso(),
                        'country_name' => NULL,
                        'state_code' => NULL,
                        'state_name' => NULL,
                        'county_code' => NULL,
                        'county_name' => NULL,
                        'city_code' => 0,
                        'city_name' => NULL,
                        'zip_code' => NULL,
                        'neighborhood' => NULL,
                        'address_01' => NULL,
                        'address_02' => NULL,
                        'address_03' => NULL,
                    ),
                    'return_address' =>
                    array(
                        'country_code' => NULL,
                        'country_iso_code' => NULL,
                        'country_name' => NULL,
                        'state_code' => NULL,
                        'state_name' => NULL,
                        'county_code' => NULL,
                        'county_name' => NULL,
                        'city_code' => 0,
                        'city_name' => NULL,
                        'zip_code' => NULL,
                        'neighborhood' => NULL,
                        'address_01' => NULL,
                        'address_02' => NULL,
                        'address_03' => NULL,
                    ),
                    'phone' =>
                    array(
                    ),
                ),
                'consignee' =>
                array(
                    'first_name' => $consignment->getContact(),
                    'last_name' => $consignment->getContact(),
                    'email' => $consignment->getEmail(),
                    'id_number' => $idNumber, //
                    'id_search_string' => $idsearchString,
                    'address' =>
                    array(
                        'country_code' => NULL,
                        'country_iso_code' => $this->country->getIso(),
                        'country_name' => NULL,
                        'state_code' => 0,
                        'state_name' => NULL,
                        'county_code' => NULL,
                        'county_name' => NULL,
                        'city_code' => 0,
                        'city_name' => utf8_encode($consignment->getCity()),
                        'zip_code' => $consignment->getPostcode(),
                        'neighborhood' => NULL,
                        'address_01' => utf8_encode($consignment->getAddressLine1()),
                        'address_02' => utf8_encode($consignment->getAddressLine2()),
                        'address_03' => utf8_encode($consignment->getAddressLine3()),
                    ),
                    'phone' =>
                    array(
                        0 =>
                        array(
                            'phone_type' => 1,
                            'phone_number' => $consignment->getTelephone(),
                        ),
                    ),
                ),
                'options' =>
                array(
                    'include_label_data' => false,
                    'include_label_zpl' => false,
                    'zpl_encode_base64' => false,
                    'zpl_label_dpi' => 203,
                    'include_label_image' => false,
                    'include_label_image_format' => 'PNG',
                    'manifest_type' => 'DDU',
                    'insurance_code' => 0,
                    'rate_service_code' => $rate_service_code,
                    'generate_label_default' => false,
                    'return_if_exists' => true,
                    'additional_services' =>
                    array(
                        'zipcode_validation' => 0,
                        'id_validation' => 0,
                        'harmonization_code_validation' => 1,
                    ),
                ),
                'data' =>
                array(
                    'external_tracking' => $consignment->getHawb(),
                    'reference_date' => date("Y-m-d"),
                    'reference_number_01' => $consignment->getReference(),
                    'reference_number_02' => NULL,
                    'reference_number_03' => NULL,
                    'tax' => NULL,
                    'value' => number_format($consignment->getValue(),2),
                    'discount' => 0,
                    'freight' => 0,
                    'insurance' => 0,
                    'currency_iso_code' => 'USD',
                    'dimension_01' => $parcel_list[0]->getLength(),
                    'dimension_02' => $parcel_list[0]->getWidth(),
                    'dimension_03' => $parcel_list[0]->getHeight(),
                    'dimension_unit' => 'CM',
                    'weight' => $consignment->getWeight(),
                    'weight_unit' => 'KG',
                    'items' =>
                    array(
                        0 =>
                        array(
                            'hs_code' => '4901',
                            'family_product' => 'OTR',
                            'serial_number' => NULL,
                            'imei_number' => '',
                            'description' => $consignment->getDescription(),
                            'product_brand' => '',
                            'product_name' => '',
                            'product_model' => '',
                            'quantity' => $consignment->getNumberPieces(),
                            'tax' => NULL,
                            'value' => $consignment->getValue(),
                            'weight' => $consignment->getWeight(),
                        ),
                    ),
                    'point_of_entry' =>
                    array(
                        'ctry_iso_code' => $ctry_iso_code,
                        'iata_code' => $iata_code,
                    ),
                ),
            ),
        );

        $requestJson = json_encode($requestArray);
        $consignment->setApiData(print_r($requestArray,1), $requestJson, "Label reqeust");
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api-test.skypostal.com/wcf-services/service-shipment.svc/shipment/new-shipment",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => $requestJson,
            CURLOPT_HTTPHEADER => array(
                "Content-Type: application/json"
            ),
        ));


        $response = curl_exec($curl);
        curl_close($curl);
        $responseArray = json_decode($response);
        //echo "<pre>";
        //print_r($responseArray);
        if ($responseArray->error->error_description == "") {
            $licence_plate_array = array();
            $parcel_list = $consignment->getParcels();
            foreach ($responseArray->data as $key => $data) {
                $error = $data->error;
                if(count($error) > 0){
                    foreach($error as $e){
                        $errormessage .= $e->error_description . "<br />";
                    }
                    $output['STATUS'] = 'ERROR';
                    $output['MESSAGE'] = $errormessage;
                    return $output;
                }
                $trackingNo = $data->label_tracking_number_01;
                $licence_plate_array[] = $trackingNo;
                $pdflink = $data->label_url_pdf;

                $filename = "../_assets/pdf/" . date('Y_m_d') . '/' . $consignment->getId() . ".pdf";
                file_put_contents($filename, file_get_contents($pdflink));
                $parcel_list[$key]->setTrackingNumber($trackingNo);
                $parcel_list[$key]->save();
            }
            $output['STATUS'] = 'SUCCESS';
            $output['LABEL'] = date('Y_m_d') . '/' . $consignment->getId() . ".pdf";
            $output['TRACKING_NUMBER'] = $licence_plate_array;
            return $output;
        }
        else
        {
            $output['STATUS'] = 'ERROR';
            $output['MESSAGE'] = $responseArray->error->error_description;
            return $output;
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
