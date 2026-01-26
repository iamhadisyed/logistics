<?php

class OWE implements CarrierService {

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
        $this->sender_country_id = new Country($consignment->getSenderCountryId());
        $user = new User($consignment->getUserId());
        $this->userAccount = new CustomerAccount($user->getUserAccountId());
        $trackingPostcode = str_replace(" ","",$consignment->getPostcode());
        $trackingPostcode = str_replace(",","",$trackingPostcode);
        $trackingPostcode = str_replace("-","",$trackingPostcode);
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
        if (trim(@$this->constants['OWE_SHIPPER_POSTCODE']) == '' || trim(@$this->constants['OWE_SHIPPER_COUNTRY']) == '' || trim(@$this->constants['OWE_SHIPPER_ADDRESS_LINE1']) == '') {
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
        foreach ($parcel_list as $parcelkey=>$parcel) {
            if ($parcel->getTrackingNumber() == '') {
                if(trim($this->serviceValues->getCode()) == "STOWESTND")
                    $service_prefix = 'ST';
                else 
                    $service_prefix = 'EX';
                    
                    /*$parcel->setTrackingNumber($consignment->getHawb()."".$parcelkey);
                    $parcel->save();
                    $licence_plate = $consignment->getHawb()."".$parcelkey;*/
                    $resultArray = LicencePlate::getLicencePlateNumber($licence_plate_id);
                    if (trim($resultArray['STATUS']) == 'ERROR')
                        return $resultArray;
                    else {
                       // str_pad($input, 10, "-=", STR_PAD_LEFT)
                        $range = str_pad($resultArray["RANGE"],11, "0", STR_PAD_LEFT);
                        $trackingPostcode = str_pad($trackingPostcode,8, "0", STR_PAD_LEFT);
                        $barcode = strtoupper($this->userAccount->getTrackingOrderPrefix() .$service_prefix.$this->sender_country_id->getIso(). $range .$this->country->getIso().$trackingPostcode.LicencePlate::mod11($range));
                        $licence_plate = $barcode;
                    }
                    $parcel->setTrackingNumber($licence_plate);
                    $parcel->save();
                    
                    /*
                } else {
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
                }*/
                
            } else {
                $licence_plate = $parcel->getTrackingNumber();
            }
            $licence_plate_array[$parcel_idx] = $licence_plate;

            $this->pdf->SetPrintFooter(false);
            $this->pdf->SetFooterMargin(0);
            $this->pdf->SetAutoPageBreak(false, 0);
            $page_size = array(100, 150);
            $this->pdf->AddPage("P", $page_size);
            if($this->serviceValues->getCarrierId() == "185"){
                $this->smartLabel($consignment, $parcel_idx, $licence_plate);
            }
            else
            {
                $this->addWayBill($consignment, $parcel_idx, $licence_plate);
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

     public function tracking($trackingNumber, $trackBy = 'parcel', $EDI = false) 
    {
        
    }

    public function sendData($tracking_numbers = array()) {
       
    }

    public function manifest($consignment) {
        
    }

    public function preAdvice($consignment) {
        
    }

    private function addWayBill(Consignment $consignment, $parcel_idx, $licence_plate) {

        $this->pdf->line(3, 51, 98, 51);
        $this->pdf->line(3, 3, 98, 3);
        $this->pdf->line(98, 3, 98, 23.8);
        $this->pdf->line(3, 3, 3, 30);
        $this->pdf->line(35, 3, 35, 23.8);
        $this->pdf->Line(46, 51, 46, 60);
        
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
        $this->pdf->line(79, 133, 79, 146);

        if($this->serviceValues->getCode() == "STSVLINEX"){
            $logo =    SETTING_URL.'images/Linex.png';
        } else if($this->userAccount->getUserAccount() == "S2WEBAY"){
            $logo =    SETTING_URL.'images/logo.png';
        } else 
        {
            $logo =    User::getUserCompanyImages(false,$consignment->getUserId());
        }
        $this->pdf->image($logo, 45, 4, 40, 15);
        $this->pdf->setFont("helvetica", "L", 9);
        //$this->pdf->Text(45, 18.5, "www.oneworldexpress.com");


        $this->pdf->setFont("helvetica", "b", 13);
      //  $this->pdf->Text(5, 5, "INTERNATIONAL");
        $this->pdf->Text(6.5, 8, "TRACKED");
        $this->pdf->Text(8.5, 13, "PARCEL");
        

        $this->pdf->setFont("helvetica", "b", 10);

        
        $this->pdf->Text(50, 53, "Weight: ". $consignment->getWeight());
        $piece = "Pieces:  ". ($parcel_idx + 1) . " / " . $consignment->getNumberPieces();
        $this->pdf->Text(5, 53, $piece);
        
        $this->pdf->setFont("helvetica", "b", 12);
        $destinationWarehouseId = $consignment->getDestinationWarehouseId();
        if($destinationWarehouseId != "" && $destinationWarehouseId > 0){
            $warehouse = new Warehouse($destinationWarehouseId);
            $this->pdf->MultiCell(30, 15, $warehouse->getWarehouseCode(), 0, 'C', false, 2, 1, 136);
            //$this->pdf->Text(5, 137, , '', false, true, 0, 1, 'C', false,'',0);
           // $this->pdf->Text(10, 137, $warehouse->getWarehouseCode());
        }
        else{
            $this->pdf->Text(10, 137, "OWE");
        }
        $this->pdf->setFont("helvetica", "", 8);
       $this->pdf->MultiCell(20, 15, $this->userAccount->getUserAccount(), 0, 'C', false, 2, 79, 136);

        $style = array(
            'position' => '',
            'align' => 'C',
            'stretch' => false,
            'fitwidth' => true,
            'cellfitalign' => '',
            'border' => false,
            'hpadding' => 'auto',
            'vpadding' => 'auto',
            'fgcolor' => array(0,0,0),
            'bgcolor' => false, //array(255,255,255),
            'text' => true,
            'font' => 'helvetica',
            'fontsize' => 8,
            'stretchtext' => 4
        );


        $this->pdf->write1DBarcode($licence_plate, 'C128', 5, 26, 93, 22, 2, $style, 'N');
    //    $this->pdf->write1DBarcode($licence_plate, 'C128', 7, 18, '', 40, 2, $style, '');
        $this->pdf->write1DBarcode($consignment->getHawb(), 'C128', 20, 117, '', 15, 0.5, $style, 'N');

        if($consignment->getShipmentType() == "C"){
            $senderCountry = new Country($consignment->getSenderCountryId());
            $company = $consignment->getSenderCompany();
            $address1 = $consignment->getSenderAddressLine1();
            $address2 = $consignment->getSenderAddressLine2();
            $address3 = $consignment->getSenderAddressLine3();
            $contact = $consignment->getSenderName();
            $city = $consignment->getSenderCity();
            $postcode = $consignment->getSenderPostcode();
            $tel = $consignment->getSenderTelephone();
            $country = $senderCountry->getName();
            $this->pdf->Text(5, 62, "Collection Details:");
        }
        else
        {
            $company = $consignment->getCompany();
            $address1 = $consignment->getAddressLine1();
            $address2 = $consignment->getAddressLine2();
            $address3 = $consignment->getAddressLine3();
            $contact = $consignment->getContact();
            $city = $consignment->getCity();
            $postcode = $consignment->getPostcode();
            $tel = $consignment->getTelephone();
            $country = $this->country->getName();
            $this->pdf->Text(5, 62, "Delivery Details:");
        }
        $this->pdf->setFont("helvetica", "", 10);
        $this->pdf->Text(5, 67, $consignment->getHawb());
        $this->pdf->Text(5, 71, $company);
        $this->pdf->Text(5, 75, $contact);
        $this->pdf->Text(5, 79, $address1);
        $this->pdf->Text(5, 83, $address2);
        $this->pdf->Text(5, 87, $address3);
        $this->pdf->Text(5, 91, $city);
        $this->pdf->Text(5, 95, $postcode);
        $this->pdf->Text(55, 99, "Tel: " . $tel);
        $fontname = $this->pdf->addTTFfont('../assets/fonts/simhei.ttf', 'TrueTypeUnicode', '', 32);
        $this->pdf->SetFont($fontname, '', 8);
        $this->pdf->Text(5, 105, "Notes: " . $consignment->getNotes());


        $this->pdf->setFont("helvetica", "B", 11);
        $this->pdf->Text(5, 99, $country);
        $this->pdf->setFont("helvetica", "L", 11);
        $this->pdf->Text(55, 95, $consignment->getReference());

        $this->pdf->setFont("helvetica", "B", 8);
        $this->pdf->Text(34, 133, "Return to:");
        $this->pdf->setFont("helvetica", "L", 8);
        $this->pdf->Text(34, 136, $this->constants['OWE_SHIPPER_ADDRESS_LINE1'] . " " . $this->constants['OWE_SHIPPER_ADDRESS_LINE2']);
        $this->pdf->Text(34, 139, $this->constants['OWE_SHIPPER_ADDRESS_LINE3'] . " " . $this->constants['OWE_SHIPPER_CITY'] . " " . $this->constants['OWE_SHIPPER_POSTCODE']);

        $shipper_country = $this->constants['OWE_SHIPPER_COUNTRY'];
        if ((int) $shipper_country > 0) {
            $shipperCountry = new Country($shipper_country);
            $sCountry = $shipperCountry->getName();
            $sIso = $shipperCountry->getIso();
        } else {
            $sCountry = $shipper_country;
        }
        $this->pdf->Text(34, 142, $sCountry . " " . $sIso);
    }
    
    private function smartLabel(Consignment $consignment, $parcel_idx, $licence_plate) {
        
        $this->pdf->SetLineStyle(array('width' => 0.7, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(1, 1, 1)));
        $this->pdf->RoundedRect(3, 3, 95, 145, 11, '1111');

        /*
         * Horizontal Lines
         */
        $this->pdf->line(3, 30, 98, 30);
        $this->pdf->line(3, 55, 98, 55);
        $this->pdf->line(3, 65, 98, 65);
        $this->pdf->line(3, 105, 98, 105);
        $this->pdf->line(3, 125, 98, 125);
        
        /*
         * Vertical Lines
         */
        $this->pdf->line(50, 55, 50, 65);
        $this->pdf->line(27, 125, 27, 148);
        $this->pdf->line(73, 125, 73, 148);
        
        /*
         * Logo
         */
        //$this->pdf->setPNGQuality(75);
        $logo =    SETTING_URL.'images/logo.png';
        $fitbox  = 'C ';
        $fitbox[1] = 'M';
        $this->pdf->image($logo, 18, 3, 60, 30, 'PNG', '', '', true, 300, '', false, false, 0, $fitbox, false, false);
        
        /*
         * Both Barcode
         */
        $style = array(
            'position' => '',
            'align' => 'C',
            'stretch' => false,
            'fitwidth' => true,
            'cellfitalign' => '',
            'border' => false,
            'hpadding' => 'auto',
            'vpadding' => 'auto',
            'fgcolor' => array(0,0,0),
            'bgcolor' => false, //array(255,255,255),
            'text' => true,
            'font' => 'helvetica',
            'fontsize' => 8,
            'stretchtext' => 4
        );


        $this->pdf->write1DBarcode($licence_plate, 'C128', 5, 32, 91, 22, 2, $style, 'N');
        $this->pdf->write1DBarcode($consignment->getHawb(), 'C128', 15, 109, '', 15, 0.5, $style, 'N');
        
        /*
         * Weight & Piece Info
         */
        $this->pdf->setFont("helvetica", "b", 10);
        $this->pdf->Text(53, 58, "Weight: ". number_format($consignment->getWeight(), 3));
        $piece = "Pieces:  ". ($parcel_idx + 1) . " / " . $consignment->getNumberPieces();
        $this->pdf->Text(5, 58, $piece);
        
        /*
         * Delivery Info
         */
        
            $company = $consignment->getCompany();
            $address1 = $consignment->getAddressLine1();
            $address2 = $consignment->getAddressLine2();
            $address3 = $consignment->getAddressLine3();
            $contact = $consignment->getContact();
            $city = $consignment->getCity();
            $postcode = $consignment->getPostcode();
            $tel = $consignment->getTelephone();
            $country = $this->country->getName();
            $this->pdf->Text(5, 67, "Ship to:");
            $this->pdf->setFont("helvetica", "", 10);
            $y=70;
            if(!empty($company))
            {
                $y = $y+4;
                $this->pdf->Text(5, $y, $company);
            }
            if(!empty($contact))
            {
                $y = $y+4;
                $this->pdf->Text(5, $y, $contact);
            }
            if(!empty($address1))
            {
                $y = $y+4;
                $this->pdf->Text(5, $y, $address1);
            }
            if(!empty($address2))
            {
                $y = $y+4;
                $this->pdf->Text(5, $y, $address2);
            }
            if(!empty($address3))
            {
                $y = $y+4;
                $this->pdf->Text(5, $y, $address3);
            }
            if(!empty($city))
            {
                $y = $y+4;
                $this->pdf->Text(5, $y, $city . " " . $postcode);
            }
            $this->pdf->Text(5, $y+4, $country);
            if(!empty($tel))
                $this->pdf->Text(55, 99, "Tel: " . $tel);
            
            /*
             * Return Address
             */
            $this->pdf->setFont("helvetica", "B", 8);
            $this->pdf->Text(28, 126, "Return to:");
            $this->pdf->setFont("helvetica", "", 8);
            $this->pdf->Text(28, 130, $this->constants['OWE_SHIPPER_ADDRESS_LINE1'] . ", " . $this->constants['OWE_SHIPPER_ADDRESS_LINE2']);
            $this->pdf->Text(28, 134, $this->constants['OWE_SHIPPER_ADDRESS_LINE3'] . ", " . $this->constants['OWE_SHIPPER_CITY'] . " " . $this->constants['OWE_SHIPPER_POSTCODE']);

            $shipper_country = $this->constants['OWE_SHIPPER_COUNTRY'];
            if ((int) $shipper_country > 0) {
                $shipperCountry = new Country($shipper_country);
                $sCountry = $shipperCountry->getName();
                $sIso = $shipperCountry->getIso();
            } else {
                $sCountry = $shipper_country;
            }
            $this->pdf->Text(28, 138, $sCountry . " " . $sIso);
            
            /*
             * Account & Warehouse
             */
            $this->pdf->setFont("helvetica", "b", 10);
            $destinationWarehouseId = $consignment->getDestinationWarehouseId();
            if($destinationWarehouseId != "" && $destinationWarehouseId > 0){
                $warehouse = new Warehouse($destinationWarehouseId);
                $this->pdf->MultiCell(30, 15, $warehouse->getWarehouseCode(), 0, 'C', false, 2, 1, 134);
                //$this->pdf->Text(5, 137, , '', false, true, 0, 1, 'C', false,'',0);
               // $this->pdf->Text(10, 137, $warehouse->getWarehouseCode());
            }
            else{
                $this->pdf->Text(10, 134, "OWE");
            }
            $this->pdf->setFont("helvetica", "b", 8);
           $this->pdf->MultiCell(20, 15, $this->userAccount->getUserAccount(), 0, 'C', false, 2, 74, 134);

    }

   
    public function recycledShipment($consignment) {
        $output["STATUS"] = "SUCCESS";
        return $output;
    }

    private function removecommas($data) {
        return str_replace(",", " ", $data);
    }

}
