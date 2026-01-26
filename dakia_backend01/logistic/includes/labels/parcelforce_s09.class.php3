<?php

class ParcelForce implements CarrierService {

    private $pdf;
    private $serviceValues = null;
    private $constants = null;

    public function __construct() {
        $pdf = new PdfBase(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $this->pdf = $pdf;
    }

    public function validation($consignment) {
        
    }

    public function remoteareas($postcode) {
        
    }

    public function label($consignment) {

        $output = array();
        $this->serviceValues = new Services($consignment->getServiceId());
        /*
         *  Get Tracking Number ranges
         */
        
        $serviceAgentConstantFilter =   new ServiceConstantValueFilter();
        $serviceAgentConstantFilter->addFilter("service_id = '".$consignment->getServiceId()."' AND agent_id = '".$consignment->getAgentId()."' ");
        $serviceAgentConstant   =    $serviceAgentConstantFilter->getConstantList("c.constant 'constant_name', scv.constant_value 'constant_value'");
        if(count($serviceAgentConstant)>0)
        {
            foreach($serviceAgentConstant  as $serviceAgentConstantData){
                $this->constants[$serviceAgentConstantData->getConstantName()]     =   $serviceAgentConstantData->getConstantValue();
            }
        }
        
        if(trim(@$this->constants['PARCELFORCE_SHIPPER_COMPANY']) == '' 
                ||  trim(@$this->constants['PARCELFORCE_SHIPPER_ADDRESS_LINE_1']) == '' 
                ||  trim(@$this->constants['PARCELFORCE_SHIPPER_CITY']) == '' 
                ||  trim(@$this->constants['PARCELFORCE_SHIPPER_POSTCODE']) == '' 
        )
        {
            $output['STATUS']       =   'ERROR';
            $output['MESSAGE']      =   "Please check service constants. Please contact to itsupport@oneworldexpress.com";
            return $output;
        }

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
                    $checkdigit = LicencePlate::modParcelForce($resultArray["RANGE"]);
                    $pieces  = str_pad($parcel_idx + 1, 3, "0", STR_PAD_LEFT);
                    $licence_plate = $resultArray["PREFIX"] . $resultArray["RANGE"] .$checkdigit. $pieces;
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
            $page_size = array(152.4, 101.6);
            $this->pdf->AddPage("P", $page_size);
            $this->addWayBill($consignment, $parcel_idx, $parcel_count, $licence_plate);

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

    public function tracking($trackingNumber) {
        
    }

    public function sendData($tracking_numbers = array()) {
        
    }

    public function manifest($consignment) {
        
    }

    public function preAdvice($consignment) {
        
    }

    private function addWayBill(Consignment $consignment, $parcel_idx,$parcel_count, $licence_plate) {
        $handling = $this->serviceValues->getCode();

        if ($handling == "S09" || $handling == "S10")
            $serviceCode = str_replace("S", "", $handling);
        else
            $serviceCode = $handling;

        $this->pdf->setFont("Arial", "", 70);
        $this->pdf->setTextColor(0, 0, 0);
        $this->pdf->SetFillColor(255, 255, 255);
        $this->pdf->setXy(3, 1);
        $this->pdf->Cell(38, 3, $serviceCode, 1, 0, "C", true);

        $this->pdf->setFont("Arial", "", 12);
        $this->pdf->Text(40.2, 22.5, $parcel_idx + 1 . " of " . $parcel_count);

        $postcode = str_replace(" ", "", $consignment->getPostcode());
        $postcode_lenght = strlen($postcode);
        if ($postcode_lenght == 5)
            $first_part_postcode = substr(trim($postcode), 0, 2);
        else if ($postcode_lenght == 6)
            $first_part_postcode = substr(trim($postcode), 0, 3);
        else if ($postcode_lenght == 7)
            $first_part_postcode = substr(trim($postcode), 0, 4);

        $ParcelForceDepoDetailFilter = new ParcelForceDepoDetailFilter();
        $ParcelForceDepoDetailFilter->addFieldFilter('postcode', $first_part_postcode);
        $DepoList = $ParcelForceDepoDetailFilter->getColumnList('depo_name,depo_short_name,postcode,route_number');


        if (count($DepoList) > 0) {
            $DepoList = $DepoList[0];
            $depo_short_name = $DepoList->getDepoShortName();
            $route_number = $DepoList->getRouteNumber();
            $ParcelForceHubDetailsFilter = new ParcelForceHubDetailsFilter();
            $ParcelForceHubDetailsFilter->addFieldFilter('depo_name', $DepoList->getDepoName());
            $HubFilter = $ParcelForceHubDetailsFilter->getList();
            if (count($HubFilter) > 0) {
                $HubFilter = $HubFilter[0];
                $depo_number = $HubFilter->getDepoNumber();
                if ($handling == '24' || $handling == 'S09' || $handling == 'S10' || $handling == 'AM' || $handling == 'PM') {
                    $hub = $HubFilter->getMonHub24();
                    $chute = $HubFilter->getMonChute24();
                } else if ($handling == '48') {
                    $hub = $HubFilter->getMonHub48();
                    $chute = $HubFilter->getMonChute48();
                }
            }
        } else {
            return "ERROR||Service is not available at this Postcode.";
        }
        $this->pdf->setFont("Arial", "B", 15);
        $this->pdf->setTextColor(255, 255, 255);
        $this->pdf->SetFillColor(0, 0, 0);
        $this->pdf->setXy(43.5, 6.1);
        $this->pdf->Cell(14, 3, $hub, 1, 0, "C", true);

        $this->pdf->setXy(43.5, 12.0);
        $this->pdf->Cell(14, 3, $chute, 1, 0, "C", true);

        $image = realpath("../images/parcel-force-logo.jpg");
        $this->pdf->image($image, 70.5, 6, 30);

        $this->pdf->setTextColor(0, 0, 0);
        $this->pdf->setFont("Arial", "", 8);

        $this->pdf->Text(52.5, 19, $licence_plate);


        $pickupDate = time();
        $this->pdf->Text(60, 30.7, date("d/m/Y, D", time()));

        $this->pdf->setFont("Arial", "", 12);
        $postcodenum = $depo_short_name . $depo_number . $route_number;
        $this->pdf->write1DBarcode("*" . $first_part_postcode . "*", 'C128', 60, 38, '', 20);
        $this->pdf->Text(57, 60, $depo_short_name);
        $this->pdf->setTextColor(255, 255, 255);
        $this->pdf->SetFillColor(0, 0, 0);
        $this->pdf->setXy(72, 60);
        $this->pdf->Cell(13, 3, $depo_number, 1, 0, "C", true);
        $this->pdf->setTextColor(0, 0, 0);
        $this->pdf->Text(85, 60, $route_number);
        $this->pdf->setFont("Arial", "L", 11);

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
        
        $this->pdf->Text(4, 67, "Special Instructions:");
        $fontname = $this->pdf->addTTFfont('../assets/fonts/simhei.ttf', 'TrueTypeUnicode', '', 32);
        $this->pdf->SetFont($fontname, '', 8);
        $this->pdf->Text(4, 70, $consignment->getNotes());
        $this->pdf->setFont("Arial", "L", 8);





        $this->pdf->Text(25, 85, $licence_plate);
        //DRAW BLACK BACKGROUND
        $this->pdf->setTextColor(255, 255, 255);
        $this->pdf->SetFillColor(0, 0, 0);
        $this->pdf->setXy(60, 85);
        $this->pdf->Cell(20, 5, "", 1, 0, "C", true);

        $this->pdf->setTextColor(255, 255, 255);
        $this->pdf->SetFillColor(0, 0, 0);
        $this->pdf->setXy(85, 85);
        $this->pdf->Cell(5, 5, $serviceCode, 1, 0, "C", true);

        $this->pdf->setTextColor(0, 0, 0);

        $this->pdf->setFont("Arial", "B", 12);

        $this->pdf->line(3, 66, 98, 66);
        $this->pdf->line(3, 66, 3, 77);
        $this->pdf->line(3, 77, 98, 77);
        $this->pdf->line(98, 66, 98, 77);

        $this->pdf->setFont("Arial", "", 10);
        $this->pdf->Text(4, 35, $company);
        $this->pdf->Text(4, 39, $consignment->getContact());
        $this->pdf->Text(4, 43, $address1);
        $this->pdf->Text(4, 47, $address2);
        $this->pdf->Text(4, 51, $address3);

        $this->pdf->Text(4, 54, $consignment->getCity());

        $this->pdf->write1DBarcode($licence_plate, 'C128', 27, 92, 55, 38);

        $this->pdf->setFont("Arial", "", 15);
        $barcode_prefix = substr($licence_plate, 0, 2);
        $barcode = substr($licence_plate, 2, 9);
        $barcode_suffice = substr($licence_plate, -3);

        $this->pdf->Text(29, 129, $barcode_prefix);
        $this->pdf->Text(39, 129, $barcode);
        $this->pdf->Text(72, 129, $barcode_suffice);

        $this->pdf->line(3, 135, 98, 135);
        $this->pdf->line(3, 135, 3, 149);
        $this->pdf->line(3, 149, 98, 149);
        $this->pdf->line(98, 135, 98, 149);

        $this->pdf->setFont("Arial", "", 14);
        $this->pdf->Text(4, 60, $consignment->getPostcode());

        $this->pdf->StartTransform();
        $this->pdf->Rotate(90, 70, 80);

        $this->pdf->setFont("Arial", "", 10);

        $this->pdf->Text(30, 102, "Customer Ref: " . $consignment->getHawb());
        $this->pdf->setFont("Arial", "L", 10);

        $this->pdf->Text(20, 14, "FROM: ");
        $this->pdf->setFont("Arial", "L", 7);

        $this->pdf->Text(32, 14, trim(@$this->constants['PARCELFORCE_SHIPPER_COMPANY']));
        $this->pdf->Text(32, 17, trim(@$this->constants['PARCELFORCE_SHIPPER_ADDRESS_LINE_1']));
        $this->pdf->Text(32, 20, trim(@$this->constants['PARCELFORCE_SHIPPER_ADDRESS_LINE_2']));
        $this->pdf->Text(32, 23, trim(@$this->constants['PARCELFORCE_SHIPPER_CITY']));
        $this->pdf->Text(32, 26, trim(@$this->constants['PARCELFORCE_SHIPPER_POSTCODE']));


        $this->pdf->StopTransform();

        $this->pdf->setFont("Arial", "", 8);
        $this->pdf->Text(5, 137, "Customer");
        $this->pdf->Text(5, 141, "Use Only");
        $this->pdf->setFont("Arial", "B", 14);
        $this->pdf->Text(88, 137, $serviceCode);

    }

    public function recycledShipment($consignment) {
            $output["STATUS"]   =   "SUCCESS";
            return $output;
        }
}
