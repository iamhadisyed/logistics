<?php

class Kaab implements CarrierService {

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
       
        if(trim($consignment->getTelephone()) == "" && in_array($service->getCode(), array('STKAB0STD','STKAABXUK','STKAB0BUD')))
        {
            $returnOutput[] = "Please enter Telephone No.";
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
        if ((trim(@$this->constants['KAAB_URL']) == '' || trim(@$this->constants['KAAB_USERNAME']) == '' || trim(@$this->constants['KAAB_PASSWORD']) == '')
                && $this->constants["INTEGRATION_TYPE"] == "API") {
            $output['STATUS'] = 'ERROR';
            $output['MESSAGE'] = "Please check service constants. Please contact to itsupport@oneworldexpress.com";
            return $output;
        }

        if (trim(@$this->constants['INTEGRATION_TYPE']) == 'API') {
            $output = $this->apiLabel($this->constants, $consignment);
        }
        else
        { 
            $output = $this->KaabUntracked($consignment);
        }
        
        return $output;
    }

    private function apiLabel($constants, Consignment $consignment) {
        $parcel = $consignment->getParcels();
        $package = array();
        $weight = $consignment->getWeight() / $consignment->getNumberPieces();
        foreach ($parcel as $p) {
            $package['weight'] = number_format($weight, 2);
            $package['size_l'] = 2;
            $package['size_w'] = 2;
            $package['size_d'] = 2;

            $package['value'] = number_format($consignment->getValue(), 1);
            $package['content'] = utf8_encode($consignment->getDescription());
        }

        
        $sender = array();
        $sender['name'] = "";
        $sender['company'] = $constants["KAAB_SHIPPER_COMPANY"]; //"KAAB/OneworldExpress";
        $sender ['address_line_1'] = $constants["KAAB_SHIPPER_ADDRESSLINE1"];//"Grodkowska 40";
        $sender ['address_line_2'] = $constants["KAAB_SHIPPER_ADDRESSLINE2"];//"Nysa";
        $sender_coutry = $constants["KAAB_SHIPPER_COUNTRY"]; //"DK";
        if (is_int($sender_coutry) > 0) {
            $shipperCountry = new Country($sender_coutry);
            $sCountry = $shipperCountry->getName();
            $sIso = $shipperCountry->getIso();
        } else {
            $sCountry = $sender_coutry;
        }
        $sender ['country'] = $sCountry; //"PL";
        $sender ['zip_code'] = $constants["KAAB_SHIPPER_POSTCODE"];  //"48-300";
        $sender ['city'] = $constants["KAAB_SHIPPER_CITY"]; //"Poland";
        $sender ['tel'] = "+48 (77) 459-33-39";
        

        $receiver = array();
        $receiver['name'] = ($consignment->getContact());
        $receiver['company'] = ($consignment->getCompany());
        $receiver ['address_line_1'] = ($consignment->getAddressLine1());
        $receiver ['address_line_2'] = ($consignment->getAddressLine2());
        $receiver ['country'] = $this->country->getIso();
        $receiver ['zip_code'] = $consignment->getPostcode();
        $receiver ['city'] = $consignment->getCity();
        $receiver ['tel'] = ($consignment->getTelephone());



        //$receiver['desc'] = 'description';
        $additions = array();
        $additions[7] = 7;
        $options = array();
        $options['pickup_mode'] = 0;
       // $operatorId = $this->getOperatorId($this->country->getIso(), $this->serviceValues->getCode());
       // if($operatorId != '')
        //    $options['operator'] = $operatorId;
        //$options['additions'] = [7];

        $DATA = array(
            'package' => $package,
            'sender' => $sender,
            'receiver' => $receiver,
            'options' => $options
        );

        //print_r($DATA); die;
        // $login = "MOL";
        //$pass = "bd1ecea167e2801206abeae6e5d1e730d9596d5e1b1eac080b26ede36256b06b";
            $login =  $constants["KAAB_USERNAME"]; //"OneWorldExpress";
        $pass = $constants["KAAB_PASSWORD"]; //"91db84b421847711c8c8769f38809aa57f85d1b3ac8ed3913463a6317d1691ab";
        $licence_plate_array = array();
        if ($ch = curl_init()) {
            $url = $constants["KAAB_URL"]; //https://api.swiatprzesylek.pl/V1/courier/create-pre-routing
            // Set some options - we are passing in a useragent too here
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_USERPWD, $login . ':' . $pass);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($DATA));
            curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLINFO_HEADER_OUT, true);
            $response = curl_exec($ch);

            /*   $info = curl_getinfo($ch);
              print_r($info['request_header']); die;
             */
            curl_close($ch);
        }
        $json = json_decode($response);
         
        $consignment->setApiData(print_r($DATA, true), print_r($json, true), 'create-pre-routing');
        $status = $json->result;
        $response = $json->response;
        
        if ($status == "OK") {

            $tracking_number = $response->packages[0]->external_id;
            if($tracking_number == ""){
                $tracking_number = $response->packages[0]->package_id;                
            }
            if ($tracking_number != '') {
                $licence_plate_array[] = $tracking_number;
                if($response->packages[0]->result == "OK"){
                    $labelString = base64_decode($response->packages[0]->labels[0]);
                    $uniqueFileName = uniqid();
                    $fp = fopen("../_assets/pdf/" . date('Y_m_d') . '/' .$uniqueFileName . ".png", 'wb+');

                    fwrite($fp, $labelString);
                    fclose($fp);


                    $outputfilename = SETTING_DIR_ASSETS . "pdf/" . date("Y_m_d") . "/" . $consignment->getId() . ".pdf";

    //               // exec("convert -page 1026x1661+0+0 /var/www/vhosts/staging.smarttrack.co/_assets/pdf/" . date('Y_m_d') . "/" . $consignment->getId() . ".png -density 300 -page 1181x2000+5+5 /var/www/vhosts/staging.smarttrack.co/_assets/pdf/" . date('Y_m_d') . "/" . $consignment->getId() . ".pdf");
                    $convertCommad = "convert -page 1026x1661+0+0 " . SETTING_DIR_ASSETS . "pdf/" . date("Y_m_d") . "/" . $uniqueFileName  . ".png -density 300 -page 1181x2000+5+5 " . $outputfilename;
    //                $convertCommad = "convert " . SETTING_DIR_ASSETS . "pdf/" . date("Y_m_d") . "/" . $uniqueFileName . ".png " . $outputfilename;
                    $last_line = system($convertCommad, $retval);
                    if(file_exists($outputfilename)){
                       foreach ($parcel as $p) {
                            $p->setTrackingNumber($tracking_number);
                            $p->save();
                        }
                        $output['STATUS'] = 'SUCCESS';
                        $output['LABEL'] = date('Y_m_d') . '/' . $consignment->getId() . ".pdf";
                        $output['TRACKING_NUMBER'] = $licence_plate_array;

                    }else{
                        $output["STATUS"] = "ERROR";
                        $output["MESSAGE"]  = "Unable to create label for requested address.";
                    }
                }
                else
                {
                    $output["STATUS"] = "ERROR";
                    $output["MESSAGE"] =   $response->packages[0]->log;
                }
                return $output;
            } else {
                $packagestatus = $response->packages[0]->result;
                $output["STATUS"] = "ERROR";
                $output["MESSAGE"] =   $response->packages[0]->log;
                return $output;
            }
            
        } else {
            $error = $json->error;
            $error_desc = $error->desc;
            $error_details = $error->details;
            foreach ($error_details as $key => $value) {
                $error_message = implode(", ", $value); 
            }
            $output["STATUS"] = "ERROR";
            $output["MESSAGE"] =   $error_message;
            return $output;
        }
    }
    
    public function KaabUntracked( Consignment $consignment){
        $this->country = new Country($consignment->getCountryId());
        $pdf = new PdfBase(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $this->pdf = $pdf;
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
        // Generate label for each parecel
        foreach ($parcel_list as $parcel) {
            if ($parcel->getTrackingNumber() == '') {
                $resultArray = LicencePlate::getLicencePlateNumber($licence_plate_id);
                if (trim($resultArray['STATUS']) == 'ERROR')
                    return $resultArray;
                else {
                    $licence_plate = $resultArray["PREFIX"] . sprintf('%09d', $resultArray["RANGE"])  . $this->country->getIso();
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
            $page_size = array(100, 100);
            $this->pdf->AddPage("P", $page_size);
            $this->ediLabel($consignment, $licence_plate);

            $new_page_flag = true;
            ++$parcel_idx;
        }
            
        $this->pdf->IncludeJS("print();");
        $this->pdf->Output("../_assets/pdf/" . date('Y_m_d') . '/' . $consignment->getId() . ".pdf", "F");
        $output['STATUS'] = 'SUCCESS';
        //SETTING_MAIN_URL . "_assets/pdf/" .
        $output['LABEL'] = date('Y_m_d') . '/' . $consignment->getId() . ".pdf";
        $output['TRACKING_NUMBER'] = $licence_plate_array;
        return $output;
    }
    
    private function ediLabel($consignment, $licence_plate){
        
        $ppiImage = $this->getUntrackedPPI($this->country->getIso(), $this->serviceValues->getMailType());
        
        $this->pdf->setFont("helvetica", "B", 12);
        $mailType = str_replace("-","\r\n",$this->serviceValues->getMailType());
        
        if($mailType == "boxable"){
            $this->pdf->Text(7, 10, strtoupper($this->serviceValues->getMailType()));
        }else{
        // MultiCell($w, $h, $txt, $border=0, $align='J', $fill=0, $ln=1, $x='', $y='', $reseth=true, $stretch=0, $ishtml=false, $autopadding=true, $maxh=0)
            $this->pdf->MultiCell(35, 25, strtoupper($mailType), 0, 'C', 0, 1, 3, 7);
        }
        $image = realpath(SETTING_DIR_REMOTE . "images/".$ppiImage);
        $this->pdf->image($image, 35, 1, 65, 25);
        $this->pdf->rect(1, 1, 98, 98);
        $this->pdf->line(1, 26, 99, 26);
        $this->pdf->line(35, 1, 35, 26);
        $this->pdf->line(1, 45, 99, 45);
        $this->pdf->line(85, 45, 85, 85);
        $this->pdf->line(1, 85, 99, 85);
        $this->pdf->setFont("helvetica", "", 7);
        $this->pdf->Text(40, 95, $consignment->getHawb());
        $this->pdf->setFont("helvetica", "B", 10);
        
        $style = array(              
                'border' => false,
                'hpadding' => 'auto',
                'vpadding' => 1,
                'fgcolor' => array(0,0,0),
                'bgcolor' => false, //array(255,255,255),
                'text' => true,
                'font' => 'helvetica',
                'fontsize' => 12,
                'stretchtext' => 1                     
        );
        
        $this->pdf->write1DBarcode($licence_plate, 'C128', 15, 30, '70', 12, 0.5, $style, 'Y');
        //$this->pdf->Text(35, 40, $licence_plate);
        
        $address = "";
        $company = "";
        if ($consignment->getCompany() != "")
            $company = $consignment->getCompany();
        if ($consignment->getAddressLine1() != "")
            $address1 = $consignment->getAddressLine1();
        if ($consignment->getAddressLine2() != "")
            $address2 = $consignment->getAddressLine2();
        if ($consignment->getAddressLine3() != "")
            $address3 = $consignment->getAddressLine3();

        $this->pdf->setFont("Freesans", "B", 8);
        $this->pdf->Text(4, 50, $company);
        $this->pdf->Text(4, 55, $consignment->getContact());
        $this->pdf->Text(4, 59, @$address1);
        $this->pdf->Text(4, 63, @$address2);
        $this->pdf->Text(4, 67, @$address3);
        $this->pdf->Text(4, 71, $consignment->getCity());
        $this->pdf->Text(4, 75, $consignment->getPostcode());
        $this->pdf->Text(4, 79, $this->country->getName());

        $style = array(
            'position' => '',
            'align' => 'C',
            'stretch' => true,
            'fitwidth' => true,
            'cellfitalign' => '',
            'border' => false,
            'hpadding' => '1',
            'vpadding' => '1',
            'fgcolor' => array(0, 0, 0),
            'bgcolor' => false, //array(255,255,255),
            'text' => false,
            'font' => 'helvetica',
            'fontsize' => 8
        );

        $this->pdf->write1DBarcode($consignment->getHawb(), 'C128', 25, 86, '70', 10, 0.5, $style, 'Y');
        $this->pdf->StartTransform();
        $this->pdf->Rotate(90, 92, 17);

       
        //$this->pdf->Text(25, 10, "Sender:");
        $this->pdf->setFont("helvetica", "B", 10);
        $this->pdf->Text(38, 15, "K-342-P");
        
       /* $this->pdf->Text(25, 12.5, "One World Expres Inc Ltd");
        $this->pdf->Text(25, 15, "One World House");
        $this->pdf->Text(25, 17.5, "Pump Lane, Hayes");
        $this->pdf->Text(25, 20, "Middlesex, UB3 3NB, UK");*/
        $this->pdf->StopTransform();
    }
    
    private function getUntrackedPPI($iso, $boxable){
        $ppi_array = array();
        if($boxable == "boxable"){
            $ppi_array = array(
                        'AT'=>'kaab_PPI107.png',
                        'BE'=>'kaab_PPI47.png',
                        'BG'=>'kaab_PPI108.png',
                        'HR'=>'kaab_PPI108.png',
                        'CZ'=>'kaab_PPI107.png',
                        'DK'=>'kaab_PPI120.png',
                        'EE'=>'kaab_PPI108.png',
                        'FI'=>'kaab_PPI47.png',
                        'FR'=>'kaab_PPI120.png',
                        'DE'=>'kaab_PPI107.png',
                        'GR'=>'kaab_PPI120.png',
                        'HU'=>'kaab_PPI107.png',
                        'IE'=>'kaab_PPI47.png',
                        'IT'=>'kaab_PPI47.png',
                        'LV'=>'kaab_PPI107.png',
                        'LT'=>'kaab_PPI120.png',
                        'LU'=>'kaab_PPI120.png',
                        'NL'=>'kaab_PPI69.jpg',
                        'PL'=>'kaab_PPI116.png',
                        'PT'=>'kaab_PPI120.png',
                        'RO'=>'kaab_PPI121.png',
                        'SK'=>'kaab_PPI107.png',
                        'ES'=>'kaab_PPI108.png',
                        'SE'=>'kaab_PPI3_1.png',
                        'SI'=>'kaab_PPI107.png');
            
            /*$ppi_array = array(
                        'AT'=>'kaab_PPI107.png',
                        'BE'=>'kaab_PPI47.png',
                        'BG'=>'kaab_PPI47.png',
                        'HR'=>'kaab_PPI47.png',
                        'CZ'=>'kaab_PPI16.png',
                        'DK'=>'kaab_PPI4.png',
                        'EE'=>'kaab_PPI47.png',
                        'FI'=>'kaab_PPI47.png',
                        'FR'=>'kaab_PPI4.png',
                        'DE'=>'kaab_PPI107.png',
                        'GB'=>'kaab_PPI16.png',
                        'GR'=>'kaab_PPI47.png',
                        'HU'=>'kaab_PPI107.png',
                        'IE'=>'kaab_PPI47.png',
                        'IT'=>'kaab_PPI47.png',
                        'LV'=>'kaab_PPI107.png',
                        'LT'=>'kaab_PPI4.png',
                        'LU'=>'kaab_PPI47.png',
                        'NL'=>'kaab_PPI113.png',
                        'NL'=>'kaab_PPI113.png',
                        'PL'=>'kaab_PPI115.png',
                        'PT'=>'kaab_PPI47.png',
                        'RO'=>'kaab_PPI121.png',
                        'SK'=>'kaab_PPI107.png',
                        'ES'=>'kaab_PPI47.png',
                        'SE'=>'kaab_PPI3.png',
                        'SI'=>'kaab_PPI108.png');*/
        }
        else
        {
            $ppi_array = array(
                    'AT'=>'kaab_PPI120.png',
                    'BE'=>'kaab_PPI120.png',
                    'BG'=>'kaab_PPI108.png',
                    'HR'=>'kaab_PPI108.png',
                    'CZ'=>'kaab_PPI107.png',
                    'DK'=>'kaab_PPI120.png',
                    'EE'=>'kaab_PPI13.png',
                    'FI'=>'kaab_PPI47.png',
                    'FR'=>'kaab_PPI107.png',
                    'DE'=>'kaab_PPI108.png',
                    'GB'=>'kaab_PPI16.png',
                    'GR'=>'kaab_PPI120.png',
                    'HU'=>'kaab_PPI120.png',
                    'IE'=>'kaab_PPI47.png',
                    'IT'=>'kaab_PPI47.png',
                    'LV'=>'kaab_PPI107.png',
                    'LT'=>'kaab_PPI120.png',
                    'LU'=>'kaab_PPI120.png',
                    'NL'=>'kaab_PPI69.jpg',
                    'PL'=>'kaab_PPI116.png',
                    'PT'=>'kaab_PPI120.png',
                    'RO'=>'kaab_PPI121.png',
                    'SK'=>'kaab_PPI120.png',
                    'ES'=>'kaab_PPI120.png',
                    'SE'=>'kaab_PPI120.png',
                    'SI'=>'kaab_PPI107.png');
            /*$ppi_array = array(
                    'AT'=>'kaab_PPI47.png',
                    'BE'=>'kaab_PPI4.png',
                    'BG'=>'kaab_PPI47.png',
                    'HR'=>'kaab_PPI47.png',
                    'CZ'=>'kaab_PPI16.png',
                    'DK'=>'kaab_PPI4.png',
                    'EE'=>'kaab_PPI47.png',
                    'FI'=>'kaab_PPI47.png',
                    'FR'=>'kaab_PPI4.png',
                    'DE'=>'kaab_PPI108.png',
                    'GB'=>'kaab_PPI16.png',
                    'GR'=>'kaab_PPI4.png',
                    'HU'=>'kaab_PPI4.png',
                    'IE'=>'kaab_PPI47.png',
                    'IT'=>'kaab_PPI47.png',
                    'LV'=>'kaab_PPI4.png',
                    'LT'=>'kaab_PPI4.png',
                    'LU'=>'kaab_PPI16.png',
                    'NL'=>'kaab_PPI4.png',
                    'NL'=>'kaab_PPI4.png',
                    'PL'=>'kaab_PPI115.png',
                    'PT'=>'kaab_PPI4.png',
                    'RO'=>'kaab_PPI121.png',
                    'SK'=>'kaab_PPI4.png',
                    'ES'=>'kaab_PPI4.png',
                    'SE'=>'kaab_PPI4.png',
                    'SI'=>'kaab_PPI4.png');*/
        }
        return $ppi_array[$iso];
    }
    
    private function getOperatorId($countryIso, $serviceCode)
    {
        $countryArray = array();
        if($serviceCode == "STKAB0STD"){
            return "";
        }
        else if($serviceCode == "STKBHU1RO"){
            $countryArray["RO"] = 182; //GLS
        }
        else if($serviceCode == "STKBHU2RO"){
            $countryArray["RO"] = 183; // URGENT CARGIS
        }
        else if($serviceCode == "STKBHU3RO"){
            $countryArray["RO"] = ""; // SAMEDAY
        }
        else if($serviceCode == "STKBHU1BG"){
            $countryArray["RO"] = ""; // SPEEDY
        }
        else if($serviceCode == "STKBHU2BG"){
            $countryArray["RO"] = 181; //IN OUT 
        }
        else
        {
           $countryArray["PL"] = 236; //INPOST
           $countryArray["AT"] = 171; // GLS
           $countryArray["CZ"] = 207; //PPL(DHL CZ)
           $countryArray["SK"] = 208; //SPS _AL 
           $countryArray["HU"] = 176; //GLS HU _AL 
           $countryArray["RO"] = 182; //GLS RO _AL 
           $countryArray["BG"] = 181; //INOUT
           $countryArray["SI"] = 178; //GLS_SI _AL 
           $countryArray["DK"] = 234; //POSTNORD MyPack Home_J
           $countryArray["SE"] = 234; //POSTNORD MyPack Home_J
           $countryArray["FI"] = 83; //DHL DE Paket INT
           $countryArray["LT"] = 118; //LP EXPRESS
           $countryArray["LV"] = 118;//LP EXPRESS
           $countryArray["EE"] = 118;//LP EXPRESS
           $countryArray["GR"] = 184; //ACS GR _AL 
           $countryArray["HR"] = 177;  //GLS HR _AL 
        }
        
        return $countryArray[$countryIso];
            
    }

 
    public function tracking($trackingNumber, $trackBy = 'parcel', $EDI = false) 
    {
        $tracking = new Tracking();
        require_once("../includes/labels/kabbtrackingstatus.class.php");
        $consignmentFilter = new ConsignmentFilter();    
        $consignmentFilter->addAwbAndHawbOrFilter($trackingNumber); 
        $consignment = $consignmentFilter->getColumnList('*');
       
        $serviceAgentConstantFilter = new ServiceConstantValueFilter();
        $serviceAgentConstantFilter->addFilter("service_id = '" . $consignment[0]->getServiceId() . "' AND agent_id = '" . $consignment[0]->getAgentId() . "' ");
        $serviceAgentConstant = $serviceAgentConstantFilter->getConstantList("c.constant 'constant_name', scv.constant_value 'constant_value'");
        
        if (count($serviceAgentConstant) > 0) 
        {
            foreach ($serviceAgentConstant as $serviceAgentConstantData) {
                $this->constants[$serviceAgentConstantData->getConstantName()] = $serviceAgentConstantData->getConstantValue();
            }
        }
        
        $username  = $this->constants['KAAB_USERNAME'];
        $password  = $this->constants['KAAB_PASSWORD']; 
        $url = $this->constants['KAAB_TRACKING_URL'];
        
        $DATA = array(
                'ids' => array($trackingNumber)
        );

                if( $ch = curl_init()) 
                {
                    curl_setopt($ch, CURLOPT_URL , $url);
                    curl_setopt($ch, CURLOPT_USERPWD , $username.':'.$password);
                    curl_setopt($ch, CURLOPT_POST , true);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($DATA));
                    curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                    curl_setopt($ch, CURLINFO_HEADER_OUT, true);
                    $response = curl_exec($ch);    
                    curl_close($ch);
                }

                $result = json_decode($response, true);            
                

        if($result['result'] == 'OK')
        {
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
            
            $events = $result['response']['tts'][$trackingNumber]['stat_id_history'];
            
            ////////////////////// Carrier Received ////////////////////////////////
            
            $trackingDataFilterObj = new TrackingDataFilter();
            $trackingDataFilterObj->addFieldFilter('tracking_number', $trackingNumber);        
            $trackingDataFilterObj->addFilter("carrier_code not in ('','1','2')");
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
            $deliveredArray = array('40');  
            
            if ($entityId > 0 && count($events) > 0) 
            {
                $parcelEntity = new Parcel($entityId);
                $finalStatusCode = $parcelEntity->getParcelStatusCode();
                
                foreach ($events as $event) 
                {                  
                    $DateTime = $event['date'];
                    $EventCode = $event['id'];
                    $kaabStatusCode = KaabTrackingStatus::$kaab_status_code;
                    $EventDescription = $kaabStatusCode[$EventCode]; 
                    $ServiceAreaDescription = $event['location'];
                    $Signatory = '';
                    
                    ////////////////////// Carrier Received ////////////////////////////////                    
                    if($carrierReceivedCheck == 1 && $EventCode != '1' && $EventCode != '2')
                    {
                        $spTrackingStatus = '148';
                        $carrierReceivedCheck = 0 ;                        
                    }
                    else
                    {
                        $spTrackingStatus = KaabTrackingStatus::getOweStatusCode($EventCode);
                    }                   
                    ////////////////////// Carrier Received ////////////////////////////////
                    
                    $result = $tracking->saveTrackPoint($entityId, $trackBy, $DateTime, $trackingNumber, $spTrackingStatus, $ServiceAreaDescription, $EventCode, $EventDescription, $deliveredArray, $finalStatusCode);                                       
                    if($result == true)
                    {
                        break;
                    }
                }
                $tracking->saveConsignmentTrackingStatus($trackingNumber, 'KaabTrackingStatus');  
            }
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
        $output["STATUS"] = "SUCCESS";
        return $output;
    }
}
