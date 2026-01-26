<?php
include_classes([
    'ecompincode.class',
    'ecompincodefilter.class'
    ]);
class Ecom implements CarrierService {

    private $pdf;
    private $serviceValues = null;
    private $userAccount = null;
    private $country = null;
    private $booking_file = null;
    private $record_array = null;
    private $isLinkService =false;

    public function __construct() {
        
    }

    public function validation(Consignment $consignment, Services $service, Country $country) {
        $returnOutput = array();
        $senderPostcode = $consignment->getSenderPostcode();
        $ecompincodeFilter = new EcomPincodeFilter();
        $ecompincodeFilter->addFilter("pincode = '".DbAccess3::escape($senderPostcode)."'");
        $senderPinCode = $ecompincodeFilter->getList();
        if(count($senderPinCode) <= 0){
             $returnOutput[] = "Sorry We are unable providing service to " . $senderPostcode . " postcode.";
        }
        return $returnOutput;
    }

    public function remoteareas($consignment, $carrierObject, $country) {
        
    }

    public function label($consignment, $labelType = 'pdf', $size = '') {

        $output = array();
        $output['STATUS'] = 'SUCCESS';
        $output['LABEL'] = date('Y_m_d') . '/321562'. ".pdf";
        $output['TRACKING_NUMBER'] = array("JD0002210164183950");
        return $output;
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
        if (trim(@$this->constants['ECOM_API_URL']) == '' || trim(@$this->constants['ECOM_API_USERNAME']) == '' || trim(@$this->constants['ECOM_API_PASSWORD']) == '') {
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
                    $licence_plate = $resultArray["PREFIX"] . $range . $resultArray["SUFIX"];
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
            $response = $this->addWayBill($consignment, $parcel_idx, $licence_plate, $parcel);
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
    
    private function addWayBill(Consignment $consignment, $parcel_idx, $licence_plate, $parcel) 
    {
        $output = array();
        if($this->isLinkService){
            $postCode = "110037";
        }
        else
        {
           $postCode = $consignment->getPostcode();
        }
        $ecompincodeFilter = new ecompincodeFilter();
        $ecompincodeFilter->addFilter("pincode = '".$postCode."'");
        $receiverPinCode = $ecompincodeFilter->getList();
        
        if(count($receiverPinCode) > 0)
        {
            $stateCode = $receiverPinCode[0]->getStateCode();
            $dcCode = $receiverPinCode[0]->getDccode();
            $routeCode = $receiverPinCode[0]->getRoute();
            $this->pdf->setFont("helvetica", "L", 7);
            $image = realpath("../images/logo.png");
            $this->pdf->image($image, 45, 7, 40, 35, strtoupper($img[1]), '', '', false, 700, '', false, false, 0, '', false, false);

            $this->pdf->setFont("helvetica", "", 9);
           

            $this->pdf->line(3, 54, 98, 54);
            $this->pdf->line(3, 3, 98, 3);
            $this->pdf->line(98, 3, 98, 23.8);
            $this->pdf->line(3, 3, 3, 30);
            $this->pdf->line(41, 3, 41, 23.8);

            $this->pdf->setFont("helvetica", "b", 13);
            $this->pdf->Text(12, 10, "ECOM");
            $this->pdf->Text(8.5, 15, "EXPRESS");

            $this->pdf->setFont("helvetica", "", 9);
            $this->pdf->Text(5, 55, "Notes: " . ucfirst(strtolower($consignment->getDescription())));
            $this->pdf->Text(5, 60, "Weight : " . number_format($consignment->getWeight(),2). " Kgs");
            $this->pdf->Text(50, 60, "Dims : " . $parcel->getLength() ." X ". $parcel->getWidth() . " X " . $parcel->getHeight());
            $this->pdf->Text(5, 64, "Ref: " .$consignment->getReference());

            $this->pdf->setFont("helvetica", "b", 13);
            $this->pdf->Text(30, 72, "Delivery Details:");
            $this->pdf->Text(11, 136.5, "IN");
            $this->pdf->setFont("helvetica", "L", 9);
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
                'bgcolor' => false,
                'text' => true,
                'font' => 'helvetica',
                'fontsize' => 15,
                'stretchtext' => 1
            );
            $this->pdf->write1DBarcode($licence_plate, 'C128', 7, 20, '', 40, 2, $style, '');

            $style = array(
                'position' => '',
                'align' => 'C',
                'stretch' => true,
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
                'stretchtext' => 1
            );
            $this->pdf->write1DBarcode($consignment->getHawb(), 'C128', 3, 115, '', 20, 2, $style, 'Y');

            $this->pdf->line(3, 24, 98, 24);
            $this->pdf->line(3, 24, 3, 115);
            $this->pdf->line(3, 115, 98, 115);
            $this->pdf->line(98, 24, 98, 115);
            $this->pdf->line(3, 70, 98, 70);
            $this->pdf->line(3, 70, 3, 145);
            $this->pdf->line(98, 70, 98, 145);
            $this->pdf->line(3, 133, 98, 133);
            $this->pdf->line(3, 113, 3, 146);
            $this->pdf->line(3, 146, 98, 146);
            $this->pdf->line(98, 113, 98, 146);
            $this->pdf->line(30, 133, 30, 146);

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

            $this->pdf->setFont("helvetica", "L", 11);
            
            if($this->isLinkService){
                
                $this->pdf->Text(5, 81, "E Com Shipping Solutions Pvt. Ltd");
                $this->pdf->Text(5, 85, "Chirag Singal");
                $this->pdf->Text(5, 89, "A 60, Deep Ganga House");
                $this->pdf->Text(5, 93, "Mahipal Pur");
                $this->pdf->Text(5, 97, "Main Road");
                $this->pdf->Text(5, 101, "New Delhi");
                $this->pdf->Text(5, 105, "110037");
                $this->pdf->setFont("helvetica", "B", 11);
                $this->pdf->Text(5, 109, "INDIA");
            }
            else
            {
                $this->pdf->Text(5, 81, $company);
                $this->pdf->Text(5, 85, $consignment->getContact());
                $this->pdf->Text(5, 89, $address1);
                $this->pdf->Text(5, 93, $address2);
                $this->pdf->Text(5, 97, $address3);
                $this->pdf->Text(5, 101, $consignment->getCity());
                $this->pdf->Text(5, 105, $consignment->getPostcode());
                $this->pdf->setFont("helvetica", "B", 11);
                $this->pdf->Text(5, 109, $consignment->getCountry());
            }
            $this->pdf->Text(60, 109, $routeCode);

            if ($this->serviceValues->getCode() == 'STECOMEXP') 
            {
                $this->pdf->Text(80, 80, 'PPD');
            } 
            else if ($this->serviceValues->getCode() == 'ICOMCOD') 
            {
                $this->pdf->Text(80, 80, 'COD');
                $this->pdf->setFont("helvetica", "B", 9);
                $this->pdf->Text(5, 65, 'Collectable Amount : ' . $consignment->getValue());
                $this->pdf->setFont("helvetica", "B", 11);
            } 
            else if ($this->serviceValues->getCode() == 'ICOMREV') 
            {
                $this->pdf->Text(80, 80, 'REV');
            }

            $senderCountryId = $consignment->getSenderCountryId();
            $senderCountry = new Country($senderCountryId);
            $this->pdf->setFont("helvetica", "L", 11);
            $this->pdf->setFont("helvetica", "B", 8);
            $this->pdf->Text(34, 133, "if undelivered return to:");
            $this->pdf->setFont("helvetica", "L", 8);
            $this->pdf->Text(34, 136, $consignment->getSenderCompany());
            $this->pdf->Text(34, 139, $consignment->getSenderAddressLine1().','.$consignment->getSenderAddressLine2().','.$consignment->getSenderPostCode());
            $this->pdf->Text(34, 142, $consignment->getSenderCity().','. $senderCountry->getName());
            
            
            $response = $this->bookPickup($consignment, $licence_plate, $parcel);
            return $response;
        }
        else
        {
            $output["STATUS"] = "ERROR";
            $output["MESSAGE"] = "Sorry We are unable providing service to " . $consignment->postcode() . " postcode.";
            return $output;
        }
    }
    
    private function bookPickup(Consignment $consignment, $licence_plate, $parcel){
        $output = array();
            if($this->isLinkService){
                $contact = "Chirag Singal";
                $addressLine1 = "A 60, Deep Ganga House,";
                $addressLine2 = "Mahipal Pur,";
                $addressLine3 = "Main Road";
                $city = "New Delhi";
                $pincode = "110037";
                $state = "DL";
               
            }
            else
            {
                $ecompincodeFilter = new ecompincodeFilter();
                $ecompincodeFilter->addFilter("pincode = '".$consignment->getPostcode()."'");
                $receiverPinCode = $ecompincodeFilter->getList();
                $contact = $consignment->getContact();
                $addressLine1 = $consignment->getAddressLine1();
                $addressLine2 = $consignment->getAddressLine2();
                $addressLine3 = $consignment->getAddressLine3();
                $city = $consignment->getCity();
                $pincode = $consignment->getPostcode();
                $state = $receiverPinCode[0]->getStateCode();
            }
        $telpphone = str_replace(" ","", $consignment->getTelephone());
         $requestArray = array(array(
            "AWB_NUMBER" => $licence_plate,
            "ORDER_NUMBER" => $consignment->getHawb(),
            "PRODUCT" => "PPD",
            "CONSIGNEE" => $contact,
            "CONSIGNEE_ADDRESS1" => $addressLine1,
            "CONSIGNEE_ADDRESS2" => $addressLine2,
            "CONSIGNEE_ADDRESS3" => $addressLine3,
            "DESTINATION_CITY" => $city,
            "PINCODE" => $pincode,
            "STATE" => $state,
            "MOBILE" => $telpphone,
            "TELEPHONE" => $telpphone,
            "ITEM_DESCRIPTION" => $consignment->getDescription(),
            "PIECES" => $consignment->getNumberPieces(),
            "COLLECTABLE_VALUE" => "0",
            "DECLARED_VALUE" => $consignment->getValue(),
            "ACTUAL_WEIGHT" => $consignment->getWeight(),
            "VOLUMETRIC_WEIGHT" => "",
            "LENGTH" => $parcel->getLength(),
            "BREADTH" => $parcel->getWidth(),
            "HEIGHT" => $parcel->getHeight(),
            "PICKUP_NAME" => $consignment->getSenderName(),
            "PICKUP_ADDRESS_LINE1" => $consignment->getSenderAddressLine1(),
            "PICKUP_ADDRESS_LINE2" => $consignment->getSenderAddressLine2(),
            "PICKUP_PINCODE" => $consignment->getSenderPostcode(),
            "PICKUP_PHONE" => $consignment->getSenderTelephone(),
            "PICKUP_MOBILE" => $consignment->getSenderTelephone(),
            "RETURN_NAME" => $consignment->getSenderName(),
            "RETURN_ADDRESS_LINE1" => $consignment->getSenderAddressLine1(),
            "RETURN_ADDRESS_LINE2" => $consignment->getSenderAddressLine2(),
            "RETURN_PINCODE" => $consignment->getSenderPostcode(),
            "RETURN_PHONE" => $consignment->getSenderTelephone(),
            "RETURN_MOBILE" => $consignment->getSenderTelephone(),
            "ADDONSERVICE" => "",
            "DG_SHIPMENT" => "false",
            "ADDITIONAL_INFORMATION" => array(
            "SELLER_TIN" =>"",
            "INVOICE_NUMBER" => $consignment->getId(),
            "INVOICE_DATE" => date("d-M-Y"),
            "ESUGAM_NUMBER" => "",
            "ITEM_CATEGORY" => "ELECTRONICS",
            "PACKING_TYPE" => "",
            "PICKUP_TYPE" => "",
            "RETURN_TYPE" => "",
            "PICKUP_LOCATION_CODE" => "",
            "SELLER_GSTIN" => "GISTN988787",
            "GST_HSN" => "HSN_BLAH_BLAH",
            "GST_ERN" => "",
            "GST_TAX_NAME" => "DELHI GST",
            "GST_TAX_BASE" => 4149.0,
            "DISCOUNT" => 0.0,
            "GST_TAX_RATE_CGSTN" => 9.0,
            "GST_TAX_RATE_SGSTN" => 9.0,
            "GST_TAX_RATE_IGSTN" => 0.0,
            "GST_TAX_TOTAL" => 746.82,
            "GST_TAX_CGSTN" => 373.41,
            "GST_TAX_SGSTN" => 373.41,
            "GST_TAX_IGSTN" => 0.0)));
            $jsonencodeRequest = json_encode($requestArray);           
            $hdrs = array(
                        'content-type: application/x-www-form-urlencoded',
                        'host: api.ecomexpress.in'
       );
            
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, "https://api.ecomexpress.in/apiv3/manifest_awb/");
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, 'username=oneworldexpress962746_pro&password=ZuS2rVp88WhbWFuB&json_input='.$jsonencodeRequest);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $hdrs);                
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        
        try 
        {
            $response = curl_exec($curl);
            print_r($response);
            exit;
            $responseJson = json_decode($response);
            
            $consignment->setApiData(print_r($jsonencodeRequest, true), print_r($responseJson, true), "PICK UP REQUEST ECOM");
                    
            if($responseJson->shipments[0]->success == false){
                $manifestResponse =  $responseJson->shipments[0]->reason;
                $output["STATUS"] = "ERROR";
                $output["MESSAGE"] = $manifestResponse;
            }
            else
            {
                $output["STATUS"] = "SUCCESS";
            }
         
        } 
        catch (Exception $e) {
            $output["STATUS"] = "ERROR";
            $output["MESSAGE"] = $e;
        }
        return $output;
    }
   

    private function cancelPickup($consignment){
        
       $output = array();
        $trackingNumber  = $consignment->getAwb();
         $hdrs = array(
            'content-type: application/x-www-form-urlencoded',
            'host: api.ecomexpress.in');

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, 'https://api.ecomexpress.in/apiv2/cancel_awb/');
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, "username=oneworldexpress962746_pro&password=ZuS2rVp88WhbWFuB&awbs=".$trackingNumber);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $hdrs);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        try {
            $result = curl_exec($curl);
            $response = json_decode($result);
            $consignment->setApiData($trackingNumber, print_r($response, true), "PICK UP CANCEL ECOM");
            foreach($response as $r){
                if(!$r->success){
                    $output["STATUS"] = "ERROR";
                    $output["MESSAGE"] = $r->reason;
                    
                }
                else
                    $output["STATUS"] = "SUCCESS";
            }
           
        } 
        catch (Exception $e) 
        {
            $output["STATUS"] = "ERROR";
            $output["MESSAGE"] = $e;
        }
        return $output;
    }
   
    public function tracking($trackingNumber, $trackBy = 'parcel', $EDI = false) 
    {
        include_once(BASE_PATH."includes/labels/ecomtrackingstatus.class.php");
        $tracking = new Tracking();
        $url = 'https://plapi.ecomexpress.in/track_me/api/mawbd/';
        $hdrs = array('host: plapi.ecomexpress.in');

        $curlRequest = "awb=$trackingNumber&order=&username=oneworldexpress962746_pro&password=ZuS2rVp88WhbWFuB";
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POST, 0);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $curlRequest);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $hdrs);                
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        try 
        {            
            $result = curl_exec($curl);
            if (!curl_errno($result)) 
            {
                $info = curl_getinfo($result);
            }
            $array = json_decode($result, true);
            
            if($array['message'] !== 'Forbidden')
            {
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
            
                $xml = new SimpleXMLElement($result);
                
                $trackingArray = $xml->object->field[36]->object;
                $shipmentPickeupUpCount = 0;

                ////////////////////// Carrier Received ////////////////////////////////
            
                $trackingDataFilterObj = new TrackingDataFilter();
                $trackingDataFilterObj->addFieldFilter('    tracking_number', $trackingNumber);        
                $trackingDataFilterObj->addFilter("carrier_code not in ('','310','001')");
                $trackingEvents = $trackingDataFilterObj->getList();

                if(count($trackingEvents) > 0)
                {
                    $carrierReceivedCheck = 0;   // there is already carrier received event                
                    $trackingDataFilterObj = new TrackingDataFilter();
                    $trackingDataFilterObj->addFieldFilter('    tracking_number', $trackingNumber);        
                    $trackingDataFilterObj->addFilter("status_code_id = '148'");
                    $CarrierReceivedObj = $trackingDataFilterObj->getList();            
                    $carrierCodeCarrierReceived = $CarrierReceivedObj[0]->getCarrierCode();
                    $carrierReceivedStatusCode = $CarrierReceivedObj[0]->getStatusCodeId();
                }
                else
                {
                    $carrierReceivedCheck = 1; // No carrier received Event
                } 
                ////////////////////// Carrier Received ////////////////////////////////
                
                if($entityId > 0)
                {
                    $parcelEntity = new Parcel($entityId);
                    $finalStatusCode = $parcelEntity->getParcelStatusCode();
                    
                    foreach($trackingArray as $track)
                    {
                        $trackingArrayTemp[] = $track->field;
                    }
                    $trackingArrayReverse = array_reverse($trackingArrayTemp);
                    
                    foreach($trackingArrayReverse as $track)
                    {
                        $dateTimeArray = explode(",", $track[0]);
                        $dayMonthArray = explode(" ", $dateTimeArray[0]);
                        $day = $dayMonthArray['0'];
                        $month = $dayMonthArray['1'];
                        $year = str_replace(" ", "", $dateTimeArray[1]);
                        $time = $dateTimeArray[2];
                        $DateTime = date("Y-m-d G:i", strtotime($year . "-" .  date('m', strtotime($month)) . "-"  . $day . " " . $time));
                        $EventDescription = strip_tags($track[1]);
                        $ServiceAreaDescription = strip_tags($track[6]);
                        $EventCode = trim($track[3]);
                        
                        // Dont enter any other event code if it is against 148 Event Code
                        if($carrierCodeCarrierReceived == $EventCode)
                        {
                            continue;
                        }
                        //////////////////////////////////////////////////////////////////
                        
                        $spTrackingStatus = EcomTrackingStatus::getOweStatusCode($EventCode);   
                        $deliveredArray = array('999');  

                        ////////////////////// Carrier Received ////////////////////////////////
                        
                        if($carrierReceivedCheck == 1 && $EventCode != '310' && $EventCode != '001')
                        {
                            $spTrackingStatus = '148';
                            $carrierReceivedCheck = 0 ;                        
                        }
                        else
                        {
                            $spTrackingStatus = EcomTrackingStatus::getOweStatusCode($EventCode);
                        } 
                        ////////////////////// Carrier Received ////////////////////////////////
                        $result = $tracking->saveTrackPoint($entityId, $trackBy, $DateTime, $trackingNumber, $spTrackingStatus, $ServiceAreaDescription, $EventCode, $EventDescription, $deliveredArray, $finalStatusCode);                                        
                        if($result == true)
                        {
                            break;
                        }
                    }               
                    $tracking->saveConsignmentTrackingStatus($trackingNumber, 'EcomTrackingStatus');    
                }
            }
        }
        catch(Exception $e)
        {
                return $e->message;
        }
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
    
    public function setLinkService($flag = false)
    {
        $this->isLinkService = $flag;
    }

    
}
