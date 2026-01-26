<?php

class KronosExpress implements CarrierService {

    private $pdf;
    private $serviceValues = null;
    private $userAccount = null;
    private $country = null;
    private $booking_file = null;
    private $record_array = null;

    public function __construct() {
        
    }

    public function validation(Consignment $consignment, Services $service, Country $country) {
        
    }

    public function remoteareas($consignment, $carrierObject, $country) {
        
    }

    public function label($consignment, $labelType = 'pdf', $size = '') {

        $output = array();
        $this->serviceValues = new Services($consignment->getServiceId());
        $this->country = new Country($consignment->getCountryId());
        $user = new User($consignment->getUserId());
        $this->userAccount = new CustomerAccount($user->getUserAccountId());

        /*
         * Service COnstant
         */
        $serviceAgentConstantFilter = new ServiceConstantValueFilter();
        $serviceAgentConstantFilter->addFilter("service_id = '" . $consignment->getServiceId() . "' AND agent_id = '" . $consignment->getAgentId() . "' ");
        $serviceAgentConstant = $serviceAgentConstantFilter->getConstantList("c.constant 'constant_name', scv.constant_value 'constant_value'");
        if (count($serviceAgentConstant) > 0) {
            foreach ($serviceAgentConstant as $serviceAgentConstantData) {
                $this->constants[$serviceAgentConstantData->getConstantName()] = $serviceAgentConstantData->getConstantValue();
            }
        }
        if (trim(@$this->constants["KRONOS_USERNAME"]) == '' || trim(@$this->constants["KRONOS_PASSWORD"]) == '' || trim(@$this->constants["KRONOS_UNIQUEKEY"]) == '' || trim(@$this->constants['KRONOS_SHIPPER_POSTCODE']) == '' || trim(@$this->constants['KRONOS_SHIPPER_COUNTRY']) == '' || trim(@$this->constants['KRONOS_SHIPPER_ADDRESS_LINE1']) == '') {
            $output['STATUS'] = 'ERROR';
            $output['MESSAGE'] = "Please check service constants. Please contact to itsupport@oneworldexpress.com";
            return $output;
        }


        /*
         *  Get Tracking Number ranges
         */

        $serviceRangeMappingFilter = new ServiceRangeMappingFilter();
        $serviceRangeMappingFilter->addFilter("service_id = '" . $consignment->getServiceId() . "' AND agent_id = '" . $consignment->getAgentId() . "' ");
        $serviceRange = $serviceRangeMappingFilter->getList(" licence_plate_id ");
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
                    $range = $resultArray["RANGE"];
                    $barcode = $resultArray["PREFIX"] . $range . $resultArray["SUFIX"];
                    $licence_plate = $barcode;
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
            $response = $this->addWayBill($consignment, $parcel_idx, $licence_plate);
            if($response["STATUS"] == "ERROR"){
                return $response;
            }

            ++$parcel_idx;
        }



        $this->pdf->IncludeJS("print();");
        $this->pdf->Output("../_assets/pdf/" . date('Y_m_d') . '/' . $consignment->getId() . ".pdf", "F");
        $output['STATUS'] = 'SUCCESS';
        $output['LABEL'] = date('Y_m_d') . '/' . $consignment->getId() . ".pdf";
        $output['TRACKING_NUMBER'] = $licence_plate_array;
        return $output;
    }

    private function addWayBill(Consignment $consignment, $parcel_idx, $licence_plate) {
        
        $output["STATUS"] = "SUCCESS";
        $announceAwb = $this->AnnounceAWB($consignment, $licence_plate);
        
        if($announceAwb["STATUS"] == "ERROR"){
            
            return $announceAwb;
        }
        $this->pdf->line(3, 51, 98, 51);
        $this->pdf->line(3, 3, 98, 3);
        $this->pdf->line(98, 3, 98, 23.8);
        $this->pdf->line(3, 3, 3, 30);
        $this->pdf->line(35, 3, 35, 23.8);
        $this->pdf->Line(46, 51, 46, 60);
        $this->pdf->Line(76, 51, 76, 60);
        $this->pdf->line(3, 24, 98, 24);
        $this->pdf->line(3, 24, 3, 115);
        $this->pdf->line(3, 115, 98, 115);
        $this->pdf->line(98, 24, 98, 115);
        $this->pdf->line(3, 60, 98, 60);
        $this->pdf->line(3, 70, 3, 145);
        $this->pdf->line(98, 70, 98, 145);
        $this->pdf->line(3, 133, 98, 133);
        $this->pdf->line(3, 113, 3, 146);
        $this->pdf->line(3, 146, 98, 146);
        $this->pdf->line(98, 113, 98, 146);
        $this->pdf->line(30, 133, 30, 146);


        $logo = "../images/logo.jpg";

        if (file_exists($logo))
            $this->pdf->image($logo, 40, 4, 49, 15);
        $this->pdf->setFont("helvetica", "L", 9);
        //$this->pdf->Text(45, 18.5, "www.oneworldexpress.com");


        $this->pdf->setFont("helvetica", "b", 13);
        $this->pdf->Text(10, 5, "Kronos");
        $this->pdf->Text(10, 10, "Express");
        
        $this->pdf->setFont("helvetica", "b", 10);

        if ($consignment->getValue() >= 0 && $consignment->getValue() < 15) {
            $this->pdf->Text(50, 53, "L");
        } else
        if ($consignment->getValue() >= 15 && $consignment->getValue() < 135) {
            $this->pdf->Text(50, 53, "M");
        } else
        if ($consignment->getValue() >= 135) {
            $this->pdf->Text(50, 53, "H");
        }


        if ($this->country->getRegion() == 'INT')
            $this->pdf->Text(80, 53, "NON EU");
        else if ($this->country->getRegion() == 'R1')
            $this->pdf->Text(80, 53, "EU");
        else if ($this->country->getRegion() == 'DBP')
            $this->pdf->Text(80, 53, "UK");

        $this->pdf->Text(5, 62, "Delivery Details:");

        $this->pdf->Text(12, 137, "OWE");
        $pieces = str_pad($parcel_idx + 1, 3, "0", STR_PAD_LEFT);


        $style = array(
            'position' => '',
            'align' => 'C',
            'stretch' => true,
            'fitwidth' => true,
            'cellfitalign' => '55',
            'border' => false,
            'hpadding' => '7',
            'vpadding' => '7',
            'fgcolor' => array(0, 0, 0),
            'bgcolor' => false, //array(255,255,255),
            'text' => true,
            'font' => 'helvetica',
            'fontsize' => 15,
            'stretchtext' => 1
        );


        $this->pdf->write1DBarcode($licence_plate, 'C128', 7, 18, '', 40, 2, $style, '');

        $this->pdf->write1DBarcode($consignment->getHawb(), 'C128', 15, 115, '', 15, 0.5, $style, '');

        $company = $consignment->getCompany();
        $address1 = $consignment->getAddressLine1();
        $address2 = $consignment->getAddressLine2();
        $address3 = $consignment->getAddressLine3();
        $this->pdf->setFont("helvetica", "", 10);
        $this->pdf->Text(5, 67, $consignment->getHawb());
        $this->pdf->Text(5, 71, $company);
        $this->pdf->Text(5, 75, $consignment->getContact());
        $this->pdf->Text(5, 79, $address1);
        $this->pdf->Text(5, 83, $address2);
        $this->pdf->Text(5, 87, $address3);
        $this->pdf->Text(5, 91, $consignment->getCity());
        $this->pdf->Text(5, 95, $consignment->getPostcode());
        $this->pdf->Text(55, 99, "Tel: " . $consignment->getTelephone());
        $fontname = $this->pdf->addTTFfont('../assets/fonts/simhei.ttf', 'TrueTypeUnicode', '', 32);
        $this->pdf->SetFont($fontname, '', 8);
        $this->pdf->Text(5, 105, $consignment->getNotes());


        $this->pdf->setFont("helvetica", "B", 11);
        $this->pdf->Text(5, 99, $this->country->getName());
        $this->pdf->setFont("helvetica", "L", 11);
        $this->pdf->Text(55, 95, $consignment->getReference());

        $this->pdf->setFont("helvetica", "B", 8);
        $this->pdf->Text(34, 133, "Return to:");
        $this->pdf->setFont("helvetica", "L", 8);
        $this->pdf->Text(34, 136, $this->constants['KRONOS_SHIPPER_ADDRESS_LINE1'] . " " . $this->constants['KRONOS_SHIPPER_ADDRESS_LINE2']);
        $this->pdf->Text(34, 139, $this->constants['KRONOS_SHIPPER_ADDRESS_LINE3'] . " " . $this->constants['KRONOS_SHIPPER_CITY'] . " " . $this->constants['KRONOS_SHIPPER_POSTCODE']);

        $shipper_country = $this->constants['KRONOS_SHIPPER_COUNTRY'];
        if ((int) $shipper_country > 0) {
            $shipperCountry = new Country($shipper_country);
            $sCountry = $shipperCountry->getName();
            $sIso = $shipperCountry->getIso();
        } else {
            $sCountry = $shipper_country;
        }
        $this->pdf->Text(34, 142, $sCountry . " " . $sIso);
        return $output;
    }
    
    public function AnnounceAWB($consignment, $licence_plate){
        $xml = file_get_contents('../includes/labels/kronosexpress/Announce.txt');
        $xml = str_replace('@username', $this->constants["KRONOS_USERNAME"], $xml);
        $xml = str_replace('@password', $this->generateDigest($this->constants["KRONOS_PASSWORD"]), $xml);
        $xml = str_replace('@awb', $licence_plate, $xml);
        $xml = str_replace('@code', '1234', $xml);
        $xml = str_replace('@name', $consignment->getContact(), $xml);
        $xml = str_replace('@surname', $consignment->getContact(), $xml);
        $xml = str_replace('@address', $consignment->getAddressLine1(), $xml);
        $xml = str_replace('@post', $consignment->getPostcode(), $xml);
        $xml = str_replace('@city', $consignment->getCity(), $xml);
        $xml = str_replace('@phone', $consignment->getTelephone(), $xml);
        $xml = str_replace('@comments', $consignment->getNotes(), $xml);
        $xml = str_replace('@wc', 'N9', $xml);
        $xml = str_replace('@weight', $consignment->getWeight(), $xml);
        $xml = str_replace('@type', '002', $xml);
        $xml = str_replace('@email', $consignment->getEmail(), $xml);
        $services = '';
        $cod = 0;
        $homedelivery = false;
        if ($cod != 0) {
            $service = file_get_contents('../includes/labels/kronosexpress/AnnounceServices.txt');
            $service = str_replace('@code', '003', $service);
            $service = str_replace('@details', $cod, $service);
            $services .= $service . "\r\n";
        }
        if ($homedelivery == true) {
            $service = file_get_contents('../includes/labels/kronosexpress/AnnounceServices.txt');
            $service = str_replace('@code', '090', $service);
            $service = str_replace('@details', '', $service);
            $services .= $service;
        } else {
            $service = file_get_contents('../includes/labels/kronosexpress/AnnounceServices.txt');
            $service = str_replace('@code', '091', $service);
            $service = str_replace('@details', '', $service);
            $services .= $service;
        }
        $xml = str_replace("@services", $services, $xml);
        $hash = $this->generateDigest($xml . $this->constants["KRONOS_UNIQUEKEY"]);
        $xml = str_replace('<Hash></Hash>', '<Hash>' . $hash . '</Hash>', $xml);
        $responsexml = $this->HttpPost($xml);
        $xml2Array = xml2array($responsexml);
        $result = $xml2Array["soap:Envelope"]["soap:Body"]["AnnounceAWBResponse"]["AnnounceAWBResult"];
        $consignment->setApiData(print_r($xml, true), print_r($result, true), 'createLabelImage');
        $output["STATUS"] =  "SUCCESS";
        if(strtolower($result["Status"]) !== "success" && strtolower($result["Status"]) !== "exists"){
            $output["STATUS"] =  "ERROR";
            $output["MESSAGE"] = $result["Status"];
        }
        return $output;
    }
    
    public function getServices() {
        $xml = file_get_contents('GetServices.txt');
        $xml = str_replace('@username', $this->constants["KRONOS_USERNAME"], $xml);
        $xml = str_replace('@password', $this->generateDigest($this->constants["KRONOS_PASSWORD"]), $xml);
        $xml = str_replace('<Hash></Hash>', '<Hash>' . $this->generateDigest($xml . $this->constants["KRONOS_UNIQUEKEY"]) . '</Hash>', $xml);
        //echo $this->generateDigest($xml . 'klohah01h8ah1808h');
        echo $xml;
        $xmlresponse =  $this->HttpPost($xml);
        var_dump($xmlresponse);
        die;
        
        
    }

    private function HttpPost($xml) {
        $url = 'https://services.kronosexpress.com/EshopWS.asmx';//'http://courier.kronosexpress.com/EshopWS.asmx';
        $post_data = array($xml);
        $stream_options = array(
            'http' => array(
                'method' => 'POST',
                'protocol_version' => 1.1,
                'header' => array(
                    'Content-type: text/xml; charset=utf-8',
                    'Content-Length: ' . strlen($xml),
                    'Expect: 100-continue',
                    'Connection: close',
                    'SOAP:Action',
                ),
                'content' => $xml,
            ),
        );
        $context = stream_context_create($stream_options);
        try {
            $response = file_get_contents($url, null, $context);
            return $response;
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }

    private function generateDigest($value) {
        $hash = hash('sha256', mb_convert_encoding($value, 'UTF-16LE'), true);
        return $this->hexToStr($hash);
    }

    private function hexToStr($string) {
        $hex = "";
        for ($i = 0; $i < strlen($string); $i++) {
            if (ord($string[$i]) < 16) {
                $hex .= "0";
            }

            $hex .= dechex(ord($string[$i]));
        }
        return ($hex);
    }

    public function tracking($trackingNumber, $trackBy = 'parcel', $EDI = false) {
    
    /*
     * GET TRACKING EVENTS
     */    
     /*   $ln = $this->TraceEvents('en');
        $myfile = fopen('traceevents.xml','w'); 
        fwrite($myfile, $ln);
        fclose($myfile);   */
        
    /*
     * GET TRACKING
    */
    
    $xml = file_get_contents('../includes/labels/kronosexpress/TrackAndTrace2.txt');
    $xml = str_replace('@username', 'vivade', $xml);
    $xml = str_replace('@password', $this->generateDigest('1234'), $xml);
    $xml = str_replace('@awb', "OWE100000011", $xml);
    $xml = str_replace('<Hash></Hash>', '<Hash>' . $this->generateDigest($xml . 'klohah01h8ah1808h') . '</Hash>', $xml);
    $xmlResponse = $this->HttpPost($xml);
    print_r($xmlResponse);
    }
    
    function TrackAndTraceWithReceiversName($awb)
    {
       
    }
    
    private function TraceEvents($lang)
    {
        $xml = file_get_contents('../includes/labels/kronosexpress/TraceEvents.txt');
        $xml = str_replace('@username', 'vivade', $xml);
        $xml = str_replace('@password', $this->generateDigest('1234'), $xml);
        $xml = str_replace('@lang', $lang, $xml);
        $xml = str_replace('<Hash></Hash>', '<Hash>' . $this->generateDigest($xml . 'klohah01h8ah1808h') . '</Hash>', $xml);
        return $this->HttpPost($xml);
    }

    public function sendData($tracking_numbers = array()) {
        
    }

    public function manifest($consignment) {
        
    }

    public function preAdvice($consignment) {
        
    }

    public function recycledShipment($consignment) {
        return $this->cancelPickup($consignment);
    }

}
