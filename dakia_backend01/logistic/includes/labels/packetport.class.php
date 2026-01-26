<?php

class PacketPort implements CarrierService {

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
        $serviceAgentConstantFilter = new ServiceConstantValueFilter();
        $serviceAgentConstantFilter->addFilter("service_id = '" . $consignment->getServiceId() . "' AND agent_id = '" . $consignment->getAgentId() . "' ");
        $serviceAgentConstant = $serviceAgentConstantFilter->getConstantList("c.constant 'constant_name', scv.constant_value 'constant_value'");
        if (count($serviceAgentConstant) > 0) {
            foreach ($serviceAgentConstant as $serviceAgentConstantData) {
                $this->constants[$serviceAgentConstantData->getConstantName()] = $serviceAgentConstantData->getConstantValue();
            }
        }
        if (trim(@$this->constants['PACPO_APIURL']) == '') {
            $output['STATUS'] = 'ERROR';
            $output['MESSAGE'] = "Please check service constants. Please contact to itsupport@oneworldexpress.com";
            return $output;
        }

        if (trim(@$this->constants['INTEGRATION_TYPE']) == 'API') {
            $output = $this->apiLabel($this->constants, $consignment);
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

    private function apiLabel($constant, $consignment) {
        $output =array();
        $isDataSend = false;
        $parcellist = $consignment->getParcels(true);
        $parcel = $parcellist[0];
        $url = $constant["PACPO_APIURL"];  // 'https://api.packetport.co.uk/create';

        $input = array();

        $input['customerRef'] = $consignment->getHawb();
        $input['recipientName'] = $consignment->getContact();
        $input['companyName'] = $consignment->getCompany();
        $input['address1'] = $consignment->getAddressLine1();
        $input['address2'] = $consignment->getAddressLine2();
        $input['city'] = $consignment->getCity();
        $input['postalCode'] = $consignment->getPostCode();
        $input['stateOrProvince'] = $consignment->getCity();
        $input['country'] = $this->country->getIso();
        $input['recipientTelephone'] = $consignment->getTelephone();
        $input['recipientEmail'] = $consignment->getEmail();
        
        $input['service'] = "PACPO"; // collection
        



        $input['description'] = $consignment->getDescription();
        $input['customsValue'] = $consignment->getValue();
        $input['currency'] = $consignment->getCurrency();
        $input['weight'] = $consignment->getWeight();
        if ($parcel->getLength() == '' || $parcel->getLength() == 0)
            $input['dim1'] = '1';
        else
            $input['dim1'] = $parcel->getLength();
        if ($parcel->getWidth() == '' || $parcel->getWidth() == 0)
            $input['dim2'] = '1';
        else
            $input['dim2'] = $parcel->getWidth();

        if ($parcel->getHeight() == '' || $parcel->getHeight() == 0)
            $input['dim3'] = '1';
        else
            $input['dim3'] = $parcel->getHeight();

        $input['weightUnit'] = 'KILO';
        $input['tokenId'] = $constant["PACKPO_TOKEN"]; //AC3DCFF33D
        $input['returnLabel'] = true;

        $jsonResult = json_encode($input, true);
        // print_r($jsonResult);


        $post_fields = '';

        foreach ($input as $key => $value) {
            $post_fields .= $key . '=' . $value . '&';
        }

        rtrim($post_fields, '&');


        $headers = array();
        $headers[] = 'Authorization: Basic ' . base64_encode($constant["PACKPO_TOKEN"].":".$constant["PACKPO_TOKEN"]);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fields);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $result = curl_exec($ch);
        curl_close($ch);
        $jsonResult = json_decode($result, true);
        $consignment->setApiData($post_fields, $jsonResult, 'PACKETPORT_API');

        if ($jsonResult['status'] == 'error') {
            print_r($jsonResult['res']);
            exit;
            $output["STATUS"] = 'ERROR';
            $output["MESSAGE"] = $jsonResult['res'];
            return $output;
        } else {
            $labelImage = $jsonResult["res"]["label"];
            $tracking = $jsonResult["res"]["tracking"];
            $licence_plate_array[] = $tracking;
            $pdf_decoded = base64_decode($labelImage);
            $mergeFileName = "../_assets/pdf/" . date('Y_m_d') . '/' . $consignment->getId() . ".pdf";

            $pdf = fopen($mergeFileName, 'w');
            fwrite($pdf, $pdf_decoded);
            fclose($pdf);
            $output['STATUS'] = 'SUCCESS';
            $output['LABEL'] = date('Y_m_d') . '/' . $consignment->getId() . ".pdf";
            $output['TRACKING_NUMBER'] = $licence_plate_array;
            return $output;
        }


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
