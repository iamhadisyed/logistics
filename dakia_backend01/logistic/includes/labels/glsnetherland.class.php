<?php

class GlsNetherland implements CarrierService {

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

    public function label($consignment, $labelType = 'pdf', $size = '100x150') {

        $output = array();
        $this->serviceValues = new Services($consignment->getServiceId());
        $this->user = SessionManager::getUser();
        $this->country = new Country($consignment->getCountryId());

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
        if (trim(@$this->constants['GLSNL_SOCKET_URL']) == '') {
            $output['STATUS'] = 'ERROR';
            $output['MESSAGE'] = "Please check service constants. Please contact to itsupport@oneworldexpress.com";
            return $output;
        }


        $parcel_list = $consignment->getParcels();
        $parcel_idx = 0;
        $pdf = new PdfBase(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $this->pdf = $pdf;
        foreach ($parcel_list as $parcel) {
            if ($parcel->getTrackingNumber() == '') {
                $resultArray = LicencePlate::getLicencePlateNumber($licence_plate_id);
                if (trim($resultArray['STATUS']) == 'ERROR')
                    return $resultArray;
                else {
                    $tnumber = $resultArray["PREFIX"] . $resultArray["RANGE"];
                    $checkdigit = LicencePlate::mod10($tnumber);
                    $licence_plate = $tnumber . $checkdigit;
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
            $resultLabel = $this->addWayBill($consignment, $parcel_idx, $licence_plate);
            if(trim($resultLabel["STATUS"]) == "ERROR"){
                $output = $resultLabel;
                return $output;
            }   

            $new_page_flag = true;
            ++$parcel_idx;
        }
        $this->pdf->IncludeJS("print();");
        $this->pdf->Output("../_assets/pdf/" . date('Y_m_d') . '/' . $consignment->getId() . ".pdf", "F");
        $output['STATUS'] = 'SUCCESS';
        $output['LABEL'] = date('Y_m_d') . '/' . $consignment->getId() . ".pdf";
        $output['TRACKING_NUMBER'] = $licence_plate_array;
        return $output;
    }
    
    private function NPDInternetChecksum ($cData) 
    {
        $nPos = '';
        $nChk = '';
        $nAsc = '';

        for ($i = 0; $i < strlen($cData); $i++) 
        {
            $nAsc = ord(substr($cData, $i, 1));
            if ($nAsc >= 65 && $nAsc <= 90)
            {
                $nAsc = $nAsc - 64;
            }
            elseif ($nAsc >= 48 && $nAsc <= 57)
            {
                $nAsc = $nAsc - 21;
            }
            $nChk = $nChk + (($i + 1) * $nAsc);
        }
        return $nChk;
    }
    // we dont have any tracking numbers for this service
    public function tracking($trackingNumber, $trackBy = 'parcel', $EDI = false) 
    {  
        $tracking = new Tracking();
        include_once(BASE_PATH."includes/labels/glsnltrackingstatus.class.php");
        $entityId = 0;
        if ($trackBy == 'parcel') 
        {
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

        if($entityId > 0)
        {
            $parcelEntity = new Parcel($entityId);
            $finalStatusCode = $parcelEntity->getParcelStatusCode();
                
            $cNPDVerladerNummer = "21520008";
            $cUwOrderNummer = $trackingNumber;
            $nUwEncryptieCode = "541";	

            $nControleGetal = $this->NPDInternetChecksum ($cNPDVerladerNummer . $cUwOrderNummer);
            $nControleGetal += $nUwEncryptieCode;

            $cURL = "http://services.gls-netherlands.com/tracking/ttlink.asp?";
            $cURL .= "NVRL=" . $cNPDVerladerNummer ;
            $cURL .= "&NDOC=" . $cUwOrderNummer;
            $cURL .= "&CHK=" . $nControleGetal;
            $cURL .= "&lang=EN";

            $trackingDataFilter = new TrackingDataFilter();                    
            $trackingDataFilter->addTrackingNumberFilter($keycode);
            $trackingDataFilter->addWarehouseIdFilter(0);
            $trackingList = $trackingDataFilter->getColumnList("id");
            $response = file_get_contents($cURL);
            
         //   print_r($response); die;
            $postable	=	strpos($response, '<table id="scandata_table"');
            $table_start_part =  substr ($response, $postable, strlen($response) );
            
            $pos2 = strpos($table_start_part, '</table>'); 
            $table_end_part =  substr($table_start_part, 0 , $pos2 ) . '</table>';	

            $DOM = new DOMDocument;
            @$DOM->loadHTML($table_end_part);
            @$trs = $DOM->getElementsByTagName('tr');
            
            $counter = 0; 
            
            ////////////////////// Carrier Received ////////////////////////////////
            
            $trackingDataFilterObj = new TrackingDataFilter();
            $trackingDataFilterObj->addFieldFilter('    tracking_number', $trackingNumber);        
            $trackingDataFilterObj->addFilter("carrier_code not in ('')");
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
            
            foreach($trs as $tr) 
            {
                if($counter == 0)
                {
                    $counter++;
                    continue;
                }
                $tds = $tr->getElementsByTagName('td');                
                $date = $tds->item(1)->nodeValue;
                $time = $tds->item(2)->nodeValue;

                $dateArray = explode("-", $date);

                $day = $dateArray[0];
                $month = $dateArray[1];
                $year = $dateArray[2];

                $DateTime = $year ."-" . $month . "-" . $day . " " . $time;                
                $EventCode = $tds->item(6)->nodeValue; 
                $EventDescription = trim($tds->item(7)->nodeValue);     
                $ServiceAreaDescription = trim($tds->item(4)->nodeValue);
                $ServiceAreaCode = $tds->item(3)->nodeValue;
                $Signatory = '';
                
                // Dont enter any other event code if it is against 148 Event Code
                if($carrierCodeCarrierReceived == $EventCode)
                {
                    continue;
                }
                
                $deliveredArray = array('3.0');  
                    
                ////////////////////// Carrier Received ////////////////////////////////
                if($carrierReceivedCheck == 1)
                {
                    $spTrackingStatus = '148';
                    $carrierReceivedCheck = 0 ;                        
                }
                else
                {
                    $spTrackingStatus = GlsNLTrackingStatus::getOweStatusCode($EventCode);
                }            
                ////////////////////// Carrier Received ////////////////////////////////
                
                $result = $tracking->saveTrackPoint($entityId, $trackBy, $DateTime, $trackingNumber, $spTrackingStatus, $ServiceAreaDescription, $EventCode, $EventDescription, $deliveredArray, $finalStatusCode);                                        
                if($result == true)
                    {
                        break;
                    }
                }               
                $tracking->saveConsignmentTrackingStatus($trackingNumber, 'YodelTrackingStatus');   
            }               
        }

    public function sendData($tracking_numbers = array()) {        
    }

    public function manifest($consignment) {        
    }

    public function preAdvice($consignment) {        
    }

    private function addWayBill(Consignment $consignment, $parcel_idx, $licence_plate) 
    {
        $output = array();
        $output["STATUS"] = "SUCCESS";
        $piece_no = $parcel_idx + 1;
        $owe_gls_number = $licence_plate;
        $shipment_type = "EBP";
        if ($this->country->getIso() == "NL")
            $shipment_type = "BP";

        $company = $consignment->getCompany();
        if ($company == "")
            $company = $consignment->getContact();
        $email = $consignment->getEmail();

        //Socket Request Data formated
        $socketData = "\\\\\\\\\\GLS\\\\\\\\\\T8904:" . $piece_no . "|T8905:" . $consignment->getNumberPieces() . "|T100:" . $this->country->getIso() . "|T330:" . $consignment->getPostcode() . "|T545:" . date("d.m.Y", time()) . "|T050:Smart System|T051:4.0.22|T090:NOPRINT|T853:Ref.No:." . $consignment->getHawb() . ".|T530:" . $consignment->getWeight() . "|T800:Afzender:|T8914:5280000000|T8915:" . $this->constants["GLS_CUSTOMER_NO"] . "|T810:Unique " . $this->constants["GLS_SHIPPER_COMPANY"] . "|T811:Uni~Code on Smart System v4.0|T820: " . $this->constants["GLS_SHIPPER_ADDRESSLINE1"] . "|T821:NL|T822:" . $this->constants["GLS_SHIPPER_POSTCODE"] . "|T823:" . $this->constants["GLS_SHIPPER_CITY"] . "|T860:" . $company . "|T861:" . $consignment->getContact() . "|T863:" . $consignment->getAddressLine1() . " |T864:" . $consignment->getCity() . "|T758:" . $consignment->getTelephone() . "|T805:21520008|T206:" . $shipment_type . "|T207:|T620:$owe_gls_number|T854:" . $consignment->getHawb() . "|T8700:NL1000|T1229:" . $email . "|/////GLS/////";
        /*  //Example of Socket Data
          $socketData = "\\\\\\\\\\GLS\\\\\\\\\\T8904:001|T8905:001|T100:NL|T330:3543AG|T050:Label-Lite|T051:4.0.22|T090:NOPRINT:NOSAVE|T853:Ref.No:|T530:5|T800:Afzender:|T8914:5281234567|T8915:5280000001|T810:Unique CommonLabel|T811:Uni~Code on Label-Litev4.0|T820:Proostwetering40A|T821:NL|T822:3543AG|T823:UTRECHT|T860:GLSNetherlandsBV|T861:DepartementIT|T863:Proostwetering40|T864:UTRECHT|T759:|T758:|T805:12345678|T206:BP|T207:|T620:$owe_gls_number|T854:N35430|T8700:NL3500|/////GLS/////";
         */

        //Create Socket 
        $socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        //Connect Socket
        socket_connect($socket, $this->constants["GLSNL_SOCKET_URL"], '3033'); //unibox.gls-netherlands.com
        //Write data to  Server
        $write_data = socket_write($socket, $socketData, strlen($socketData));
        $fullResult = '';
        // Start Reading Content from Socket
        while ($resp = socket_read($socket, 1000)) {
            $fullResult .= $resp;
            if (strpos($fullResult, "/////GLS/////") !== false)
                break;
        }
        socket_close($socket);

        $fullResult = str_replace("/////GLS/////", "", $fullResult);
        $fullResult = str_replace("\\\\\\\\\\GLS\\\\\\\\\\", "", $fullResult);

        $ArrfullResult = explode("|", $fullResult);

        $consignment->setApiData(print_r($socketData, true), print_r($ArrfullResult, true), "GLS NL LABEL");
        for ($i = 0; $i < count($ArrfullResult); ++$i) {
                if (strpos($ArrfullResult[$i], 'T712') !== false) {
                    $output["STATUS"] =  "ERROR";
                    $output["MESSAGE"] = $ArrfullResult[$i];
                    return $output;
                }
            }
        if (strpos($fullResult, 'RESULT:E000:') !== false) {
            for ($i = 0; $i < count($ArrfullResult); ++$i) {
                if (strpos($ArrfullResult[$i], 'RESULT:E000:') !== false)
                    $awb_track = str_replace('RESULT:E000:', '', $ArrfullResult[$i]);
                if (strpos($ArrfullResult[$i], 'T8902') !== false)
                    $left_matrix = str_replace('T8902:', '', $ArrfullResult[$i]);
                if (strpos($ArrfullResult[$i], 'T400') !== false)
                    $tracking_number = str_replace('T400:', '', $ArrfullResult[$i]);
                if (strpos($ArrfullResult[$i], 'T101') !== false)
                    $final_location = str_replace('T101:', '', $ArrfullResult[$i]);
                if (strpos($ArrfullResult[$i], 'T310') !== false)
                    $inbound_sorting_flag = str_replace('T310:', '', $ArrfullResult[$i]);
                if (strpos($ArrfullResult[$i], 'T110') !== false)
                    $outbound_sorting_flag = str_replace('T110:', '', $ArrfullResult[$i]);
                if (strpos($ArrfullResult[$i], 'T8951') !== false)
                    $zip_code_text = str_replace('T8951:', '', $ArrfullResult[$i]);
                if (strpos($ArrfullResult[$i], 'T320') !== false)
                    $tour_number = str_replace('T320:', '', $ArrfullResult[$i]);
                if (strpos($ArrfullResult[$i], 'T330') !== false)
                    $zip_code = str_replace('T330:', '', $ArrfullResult[$i]);
                if (strpos($ArrfullResult[$i], 'T8913') !== false)
                    $track_id = str_replace('T8913:', '', $ArrfullResult[$i]);
                if (strpos($ArrfullResult[$i], 'T500') !== false)
                    $pickup_location = str_replace('T500:', '', $ArrfullResult[$i]);
                if (strpos($ArrfullResult[$i], 'T510') !== false)
                    $station_id = str_replace('T510:', '', $ArrfullResult[$i]);
                if (strpos($ArrfullResult[$i], 'T540') !== false)
                    $date_printed = str_replace('T540:', '', $ArrfullResult[$i]);
                if (strpos($ArrfullResult[$i], 'T541') !== false)
                    $time_printed = str_replace('T541:', '', $ArrfullResult[$i]);
                if (strpos($ArrfullResult[$i], 'T530') !== false)
                    $weight = str_replace('T530:', '', $ArrfullResult[$i]);
                if (strpos($ArrfullResult[$i], 'T8904') !== false)
                    $current_number_of_pieces = str_replace('T8904:', '', $ArrfullResult[$i]);
                if (strpos($ArrfullResult[$i], 'T8905') !== false)
                    $total_number_of_pieces = str_replace('T8905:', '', $ArrfullResult[$i]);
                if (strpos($ArrfullResult[$i], 'T520') !== false)
                    $routing_date = str_replace('T520:', '', $ArrfullResult[$i]);
                if (strpos($ArrfullResult[$i], 'T860') !== false)
                    $receiver_name = str_replace('T860:', '', $ArrfullResult[$i]);
                if (strpos($ArrfullResult[$i], 'T861') !== false)
                    $receiver_name1 = str_replace('T861:', '', $ArrfullResult[$i]);
                if (strpos($ArrfullResult[$i], 'T863') !== false)
                    $recepient_address = str_replace('T863:', '', $ArrfullResult[$i]);
                if (strpos($ArrfullResult[$i], 'T100') !== false)
                    $recepient_country_code = str_replace('T100:', '', $ArrfullResult [$i]);
                if (strpos($ArrfullResult[$i], 'T864') !== false)
                    $city = str_replace('T864:', '', $ArrfullResult[$i]);
                if (strpos($ArrfullResult[$i], 'T330') !== false)
                    $zip_code = str_replace('T330:', '', $ArrfullResult[$i]);
                if (strpos($ArrfullResult[$i], 'T8958') !== false)
                    $contact = str_replace('T8958:', '', $ArrfullResult[$i]);
                if (strpos($ArrfullResult[$i], 'T8959') !== false)
                    $phone = str_replace('T8959:', '', $ArrfullResult[$i]);
                if (strpos($ArrfullResult[$i], 'T8960') !== false)
                    $note1 = str_replace('T8960:', '', $ArrfullResult[$i]);
                if (strpos($ArrfullResult[$i], 'T759') !== false)
                    $contact_1 = str_replace('T759:', '', $ArrfullResult[$i]);
                if (strpos($ArrfullResult[$i], 'T758') !== false)
                    $phone_1 = str_replace('T758:', '', $ArrfullResult[$i]);
                if (strpos($ArrfullResult[$i], 'T923') !== false)
                    $note1_1 = str_replace('T923:', '', $ArrfullResult[$i]);
                if (strpos($ArrfullResult[$i], 'T854') !== false)
                    $ref_no_1 = str_replace('T854:', '', $ArrfullResult[$i]);
                if (strpos($ArrfullResult[$i], 'T620') !== false)
                    $hawb_no = str_replace('T620:', '', $ArrfullResult[$i]);
                if (strpos($ArrfullResult[$i], 'T853') !== false)
                    $ref_no = str_replace('T853:', '', $ArrfullResult[$i]);
                if (strpos($ArrfullResult[$i], 'T8914') !== false)
                    $contact_id = str_replace('T8914:', '', $ArrfullResult[$i]);
                if (strpos($ArrfullResult[$i], 'T8915') !== false)
                    $customer_id = str_replace('T8915:', '', $ArrfullResult[$i]);
                if (strpos($ArrfullResult[$i], 'T8903') !== false)
                    $right_matrix = str_replace('T8903:', '', $ArrfullResult[$i]);
            }
        }
        $receiver_name = $consignment->getContact();
        ;
        $receiver_name2 = $consignment->getAddressLine2();

        $right_matrix = iconv("CP1252", "UTF-8", $right_matrix);
        $right_matrix = preg_replace("/¬*/", "|", $right_matrix);
        $right_matrix = str_pad($right_matrix, 113, " ");


        $style5 = array(
            'border' => false,
            'vpadding' => 'auto',
            'hpadding' => 'auto',
            'fgcolor' => array(0, 0, 0),
            'bgcolor' => false, //array(255,255,255)
            'module_width' => 3, // width of a single module in points
            'module_height' => 3 // height of a single module in points
        );
        $this->pdf->write2DBarcode($right_matrix, 'DATAMATRIX', 69, 28.5, 24, 24, $style5, 'n');
        $style = array('width' => 1);
        $style1 = array('width' => 0.5);
        $style2 = array('width' => 0.25);
        $this->pdf->line(1, 2, 97, 2, $style);
        $this->pdf->setFont("helvetica", "b", 27);
        $this->pdf->Text(35, 3, $inbound_sorting_flag);
        $this->pdf->Text(5, 3, $outbound_sorting_flag);
        $this->pdf->Text(70, 3, $final_location);
        $this->pdf->line(1, 15, 97, 15, $style1);
        $this->pdf->setFont("helvetica", "b", 22);
        $this->pdf->Text(3, 16, $tour_number);
        $this->pdf->setFont("helvetica", "b", 7);
        $this->pdf->Text(30, 16, $zip_code_text);
        $this->pdf->setFont("helvetica", "b", 12);
        $this->pdf->Text(30, 20, $zip_code);
        $this->pdf->setFont("helvetica", "b", 7);
        $this->pdf->Text(50, 16, "Your GLS Track ID");
        $this->pdf->setFont("helvetica", "b", 12);
        $this->pdf->Text(50, 20, $track_id);
        $this->pdf->line(1, 25, 97, 25, $style1);
        $this->pdf->setFont("helvetica", "b", 35);
        $this->pdf->line(3, 26, 7, 26);
        $this->pdf->line(3, 26, 3, 30);
        $this->pdf->line(3, 51, 7, 51);
        $this->pdf->line(3, 51, 3, 47);
        $this->pdf->line(28, 26, 25, 26);
        $this->pdf->line(28, 26, 28, 29);
        $this->pdf->line(28, 51, 28, 47);
        $this->pdf->line(28, 51, 25, 51);
        $this->pdf->setFont("helvetica", "b", 20);
        $style4 = array(
            'border' => false,
            'vpadding' => 'auto',
            'hpadding' => 'auto',
            'fgcolor' => array(0, 0, 0),
            'bgcolor' => false, //array(255,255,255)
            'module_width' => 1, // width of a single module in points
            'module_height' => 1 // height of a single module in points
        );
        $this->pdf->write2DBarcode($left_matrix, 'DATAMATRIX', 4, 27, 23, 23, $style4, 'n');


        $style6 = array(
            'position' => '',
            'align' => 'C',
            'stretch' => false,
            'fitwidth' => true,
            'cellfitalign' => '',
            'border' => false,
            'hpadding' => 'auto',
            'vpadding' => 'auto',
            'fgcolor' => array(0, 0, 0),
            'bgcolor' => false, //array(255,255,255),
            'text' => true,
            'font' => 'helvetica',
            'fontsize' => 8,
            'stretchtext' => 0
        );
        if ($this->country->getIso() != "NL") {
            $this->pdf->write1DBarcode($tracking_number, 'I25', 29, 30, '', 20, 0.3, $style6, 'Y');
        }
        $this->pdf->line(1, 52, 97, 52, $style1);
        $this->pdf->setFont("helvetica", "b", 10);
        $this->pdf->Text(1, 53, $pickup_location);
        $this->pdf->Text(15, 52.5, $station_id);

        $this->pdf->StartTransform();
        $this->pdf->Rotate(270, 80, 70);

        $this->pdf->setFont("helvetica", "B", 9);

        $this->pdf->Text(68, 53, "Afzender:    Klantnr:" . $this->constants["GLS_CUSTOMER_NO"] . "    Contact ID: $contact_id"); //5280043483 customer No


        $this->pdf->Text(68, 56, $this->constants["GLS_SHIPPER_COMPANY"]); //"ORANGE POST NV"
        $this->pdf->Text(68, 59, $this->constants["GLS_SHIPPER_ADDRESSLINE1"]); //"Westerdreef 5K"
        $this->pdf->Text(68, 62, $this->constants["GLS_SHIPPER_CITY"]); //"Nieuw-Vennep"
        $this->pdf->Text(68, 65, $this->constants["GLS_SHIPPER_POSTCODE"]); //"NL-2152CS "

        $this->pdf->write1DBarcode("OP" . $owe_gls_number, 'C128', 101, 58, 28, '', 0.5);
        $this->pdf->setFont("helvetica", "", 9);

        $this->pdf->Text(150, 67, "OP" . $owe_gls_number);
        $this->pdf->StopTransform();

        $this->pdf->setTextColor(255, 255, 255);
        $this->pdf->SetFillColor(0, 0, 0);
        $this->pdf->setXy(30, 2);

        $this->pdf->Cell(13, 13, "", 1, 0, "C", true);
        $this->pdf->setXy(70, 2);
        $this->pdf->Cell(35, 13, "", 1, 0, "C", true);
        $this->pdf->setFont("helvetica", "B", 27);
        $this->pdf->Text(35, 3, $inbound_sorting_flag);

        $this->pdf->Text(70, 3, $final_location);
        $this->pdf->setTextColor(0, 0, 0);
        $this->pdf->setFont("helvetica", "B", 27);

        $this->pdf->Text(51, 3, $recepient_country_code);
        $this->pdf->Text(5, 3, $outbound_sorting_flag);

        $this->pdf->setFont("helvetica", "b", 5);

        $this->pdf->setFont("helvetica", "b", 8);
        $this->pdf->Text(20, 53, $date_printed);
        $this->pdf->setFont("helvetica", "b", 8);
        $this->pdf->Text(35, 53, $time_printed);
        $this->pdf->setFont("helvetica", "b", 10);
        $this->pdf->Text(45, 53, $weight . " " . "KG");
        $this->pdf->setFont("helvetica", "b", 8);
        $this->pdf->Text(59, 53, "$current_number_of_pieces/$total_number_of_pieces");
        $this->pdf->setFont("helvetica", "b", 8);
        $this->pdf->Text(70, 53, "RTG $routing_date E2.00");
        $this->pdf->line(1, 57, 97, 57, $style2);
        $this->pdf->line(1, 57, 1, 147, $style2);
        $this->pdf->line(79, 57, 79, 147, $style2);

        $this->pdf->line(97, 57, 97, 147, $style2);
        $this->pdf->setFont("helvetica", "b", 12);
        $this->pdf->Text(2, 83, $consignment->getCompany());
        $this->pdf->setFont("helvetica", "b", 10);
        $this->pdf->Text(2, 87, $consignment->getContact());
        $this->pdf->setFont("helvetica", "b", 12);
        $this->pdf->Text(2, 91, $recepient_address);
        $this->pdf->setFont("helvetica", "", 9);
        $this->pdf->Text(2, 95, $consignment->getAddressline2());
        $this->pdf->Text(2, 99, $consignment->getAddressline3());


        $this->pdf->setFont("helvetica", "b", 13);
        $this->pdf->Text(2, 103, "$recepient_country_code  $zip_code");
        $this->pdf->Text(2, 107, "$city");
        $this->pdf->line(1, 83, 79, 83, $style2);
        $this->pdf->line(1, 113, 79, 113, $style2);
        $this->pdf->setFont("helvetica", "b", 10);





        $this->pdf->write1DBarcode($owe_gls_number, 'I25', 15, 114, '', 15, 0.3, $style6, 'Y');

        $this->pdf->setFont("helvetica", "L", 8);
        $this->pdf->line(1, 129, 79, 129, $style2);
        $this->pdf->setFont("helvetica", "b", 8);
        $this->pdf->Text(2, 130, "Contact: " . " " . $consignment->getContact());
        $this->pdf->Text(2, 133, "Phone: " . " " . $consignment->getTelephone());
        $this->pdf->Text(2, 136, "Noted: " . " " . $consignment->getDescription());
        $this->pdf->Text(2, 139, "Ref.No: " . " " . $consignment->getReference() . "     Hawb: " . $ref_no_1);




        $this->pdf->Text(2, 142, "www.gls-group.eu");
        $this->pdf->line(1, 147, 97, 147, $style2);




        return $output;
    }

    public function recycledShipment($consignment) {
        $output["STATUS"] = "SUCCESS";
        return $output;
    }

}
