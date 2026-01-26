<?php

class SpringGlobal implements CarrierService {

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

        if (trim(@$this->constants['SPRING_ACCOUNT']) == '' || trim(@$this->constants['API_URL']) == '' || trim(@$this->constants['API_KEY']) == '') {
            $output['STATUS'] = 'ERROR';
            $output['MESSAGE'] = "Please check service constants. Please contact to itsupport@oneworldexpress.com";
            return $output;
        }

        if (trim(@$this->constants['INTEGRATION_TYPE']) == 'API') {
            $output = $this->apiLabel($this->constants, $consignment);
        }

        return $output;
    }

    private function apiLabel($constant, Consignment $consignment) {

        $output = array();

        $itemsDes = array();
        $parcelData = $consignment->getParcels();
        foreach ($parcelData as $parcel) {
            $length = $parcel->getLength();
            $height = $parcel->getHeight();
            $width = $parcel->getWidth();

            for($i=1; $i <= $consignment->getNumberPieces(); $i++) {

                $itemsDes[] = array(
                    "code" => ('GB' ),
                    "description" => ($consignment->getDescription() ),
                    "quantity" => (1),
                    "quantity_unit" => "pcs",
                    "weight" => ($consignment->getWeight() / $consignment->getNumberPieces()),
                    "price" => ($consignment->getValue() ),
                    "unit_price" => (1),
                    "country_of_manufacture_code" => ('GB'),
                    "tariff" => ('0' ),
                    "currency_code" => "EUR"
                );
            }
        }
        $hanlding = $this->serviceValues->getCode();
        $serviceHandlingCode = str_replace("SPR", "", $hanlding);

        $requestParamsArray = array();
        $requestParamsArray['account_number'] = $constant["SPRING_ACCOUNT"]; //"100004794"; // M
        $requestParamsArray['label'] = array(
            "product_code" => $serviceHandlingCode, // M
            "label_type" => "PDF", // M
            "declaration_type" => "SaleOfGoods", // O
            "label_size" => 0, // M
            "ship_date" => date("Y-m-d\TH:i:s\Z"), // M
            "declared_value" => $consignment->getValue(), // M
            "currency_code" => $consignment->getCurrency(), // M
            "weight" => $consignment->getWeight() * 1000, // M  and in grams
            "reference_number" => $consignment->getHawb(), //O
            "additional_reference1" => $consignment->getReference(), //O
            "additional_reference2" => $this->user->getUsername(), //O
            "content_description" => $consignment->getDescription(), //o
            "signature_name" => null, //O CN22 signature name
            "signature_title" => null, //O CN22 signature title
            "recycle" => false,
            "dimension_unit" => "cm",
            "length" => $length,
            "height" => $height,
            "width" => $width,
            "items" => $itemsDes,
            "destination" => array(
                "company" => $consignment->getCompany(),
                "attention" => $consignment->getContact(),
                "address_line1" => $consignment->getAddressLine1(),
                "address_line2" => $consignment->getAddressLine2(),
                "building_number" => "",
                "city" => $consignment->getCity(),
                "postal_code" => $consignment->getPostcode(),
                "state_code" => $consignment->getState(),
                "country_code" => $this->country->getIso(),
                "phone_number" => $consignment->getTelephone(),
                "email_address" => $consignment->getEmail(),
            ),
            "return" => array(
                "company" => $constant["RETURN_COMPANY"],
                "attention" => "Operation",
                "address_line1" => $constant["RETURN_ADDRESSLINE1"],
                "address_line2" => $constant["RETURN_ADDRESSLINE2"],
                "building_number" => null,
                "city" => $constant["RETURN_CITY"],
                "postal_code" => $constant["RETURN_POSTCODE"],
                "state_code" => null,
                "country_code" => $constant["RETURN_COUNTRY"],
                "phone_number" => $constant["RETURN_PHONE"],
                "email_address" => $constant["RETURN_EMAIL"]
            ),
        );


        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $constant["API_URL"] . "shipment/label/v1/create");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($requestParamsArray));

        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Content-Type: application/json",
            "apikey: " . $constant["API_KEY"]
        ));

        $orderResponse = json_decode(curl_exec($ch));
        $consignment->setApiData(json_encode($requestParamsArray), print_r($orderResponse, true), 'SPRING_GDS_LABEL');
        curl_close($ch);
        $order_message = '';

        if (sizeof($orderResponse) > 0) {
            if (count($orderResponse->errors) > 0)
                $order_message = implode(", ", $orderResponse->error);
            if (trim($orderResponse->status) == 'fail') {
                if (is_array($orderResponse->message->payload))
                    $order_message = implode(", ", $orderResponse->message->payload);
                else
                    $order_message = $orderResponse->message->payload;
            }

            if (trim($order_message) != "") {
                $output["STATUS"] = 'ERROR';
                $output["MESSAGE"] = $order_message;
            } else {
                $awb = $orderResponse->data->tracking_number;
                $albe = $orderResponse->data->labels;
                foreach ($albe as $labelcontenct)
                    $labelResponse = base64_decode($labelcontenct->content);
                // Generate label for each parecel
                foreach ($parcelData as $parcel) {
                    $parcel->setTrackingNumber($awb);
                    $parcel->setConsignmentID($consignment->getId());
                    $parcel->save();
                }
                $licence_plate_array[] = $awb;
                $consignment->setAwb($awb);
                

                $fileName = SETTING_DIR_ASSETS . "pdf/" . date('Y_m_d') . '/' . $consignment->getId() . ".pdf";
                file_put_contents($fileName, $labelResponse);
               
                $output['STATUS'] = 'SUCCESS';
                $output['LABEL'] = date('Y_m_d') . '/' . $consignment->getId() . ".pdf";
                $output['TRACKING_NUMBER'] = $licence_plate_array;
            }
        } else {
            $output["STATUS"] = 'ERROR';
            $output["MESSAGE"] = $orderResponse->error;
        }
        return $output;
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
       
    }

}
