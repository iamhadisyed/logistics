<?php
class MailMissionSolution implements CarrierService {

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
        if (trim(@$this->constants['MMS_APIURL']) == '' ) {
            $output['STATUS'] = 'ERROR';
            $output['MESSAGE'] = "Please check service constants. Please contact to itsupport@oneworldexpress.com";
            return $output;
        }
        
          if (trim(@$this->constants['INTEGRATION_TYPE']) == 'API') {
            $output = $this->apiLabel($this->constants, $consignment);
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
    
    private function apiLabel($constant, $consignment)
    {
        $pdf = new PdfBase(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $this->pdf = $pdf;
        $parcel_list = $consignment->getParcels();
        $parcel_idx = 0;

        // Generate label for each parecel
        foreach ($parcel_list as $parcel) {
            $this->pdf->SetPrintFooter(false);
            $this->pdf->SetFooterMargin(0);
            $this->pdf->SetAutoPageBreak(false, 0);


            $page_size = array(100, 150);

            $this->pdf->AddPage("P", $page_size);

            $url = $constant["MMS_APIURL"]; //'http://customers.mailmissionsolutions.nl/api02/action/966F2EC5A610C48FA114FEB7E9E4F19C67A395A7/101';
            //set up weight numbers.
            if ($consignment->getWeight() <= 3)
                $weight = '1';
            else if (($consignment->getWeight() > 3) && ($consignment->getWeight() <= 10))
                $weight = '2';
            else if (($consignment->getWeight() > 10) && ($consignment->getWeight() <= 30))
                $weight = '3';


            $string = $consignment->getAddressLine1();
            $add1 = explode(" ", $string);
            foreach ($add1 as $doorno) {
                if (strpos($doorno, "/"))
                    $door = $doorno;
                else if (strpos($doorno, "-"))
                    $door = $doorno;
                else if (ctype_alnum($doorno))
                    $door = $doorno;
            }

            if ($door == "")
                $door = "00";

            preg_match_all('/^(\d*)(\w*)$/', $door, $matches);
            $house_number = $matches[1][0];
            $house_extension = $matches[2][0];



            $address_line1 = $consignment->getAddressLine1();
            list($alpha, $num) = sscanf($address_line1, "%[A-Z a-z ]%d");
            if ($num != "") {
                $address_line1 = $alpha;
                $address_line2 = $num;
            } else {
                $address_line2 = $consignment->getAddressLine2();
            }



            $params = array("referentie" => substr($consignment->getHawb(), 0, 9),
                "CreatedBy" => "OWE",
                "bedrijfsnaam" => @$consignment->getCompany(), //company
                "afdeling" => '', //section department
                "voornaam" => @$consignment->getContact(), //first name
                "tussenvoegsel" => '', //middle name
                "achternaam" => '', //last name/surname
                "email" => "orangepost1@pakketjesknop.nl", //email
                "inhoud" => @$consignment->getDescription(), //content?
                "inhoudopetiket" => '', //not sure
                "straat" => $address_line1, //addressline1
                //"huisnummer" => $address_line2   ,//line2
                "huisnummer" => $house_number, //line2							
                "huisnummertoevoeging" => $house_extension, //str_replace("-","",$consignment->getAddressLine3()),//house number
                "postcode" => @$consignment->getPostcode(), //postcode
                "plaats" => $consignment->getCity(), //place?
                "telefoonnummer" => @$consignment->getTelephone(), //telephone number
                "handtekeningvo" => "0", //need a sign
                "gewichtscategorie" => $weight, //weight catergory 1 - 0-3 kg,     2 – 3-10kg,     3 - 10-30kg
                "gewicht" => @$consignment->getWeight(), //weight not needed
                "afmetingl" => "", //length not needed
                "afmetingb" => "", //width not needed
                "afmetingh" => "", //height not needed
                "nabb" => "0", //leave with neibour 0 = no 1 = yes
                "verlader" => "8"//leave with neibour 0 = no 1 = yes
            );

            if ($url != '') {
                //prepare connection to connect
                $ch = curl_init($url);
                //set curl options
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                //execute.
                $result = curl_exec($ch);
                //close connection
                curl_close($ch);

                $obj = json_decode($result); //decode json into a object.
                print_r($obj);
                
               
                if (@$obj->state == 'true') {
                   $link = $obj->url; // getlink
                    $link = str_replace('\\', '', $link);

                    $pdfpage = file_get_contents(trim($link));
                    $path = "../_assets/pdf/" . date('Y_m_d') . '/' . $consignment->getId() . ".pdf";
                    //$path = $consignment->getId().".pdf";
                    $fp = fopen($path, 'w+');
                    fwrite($fp, $pdfpage);
                    fclose($fp);

                    $licence_plate_array = array();
                    $licence_plate_array[] = trim(@$obj->Barcode);
//                    $parcel->setTrackingNumber(trim(@$obj->{'Barcode'}));
//                    $parcel->save();
                    $this->pdf->IncludeJS("print();");
                    $output['STATUS'] = 'SUCCESS';
                    $output['LABEL'] = date('Y_m_d') . '/' . $consignment->getId() . ".pdf";
                    $output['TRACKING_NUMBER'] = $licence_plate_array;
                    return $output;
                } else {
                    $output['STATUS'] = 'ERROR';
                    $output['MESSAGE'] = @$obj->message;
                    return $output;
                }
            } else {
                $output['STATUS'] = 'ERROR';
                $output['MESSAGE'] = "Please add Api URL to Agent.";
                return $output;
            }
            ++$parcel_idx;
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
