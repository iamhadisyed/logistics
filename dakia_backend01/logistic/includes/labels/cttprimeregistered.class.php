<?php

class CTTPrimeRegistered implements CarrierService {

    private $pdf;
    private $serviceValues = null;
    private $userAccount = null;
    private $country = null;
    private $constants = null;

    public function __construct() {
        
    }

    public function validation(Consignment $consignment, Services $service, Country $country) {
        
    }

    public function remoteareas($consignment, $carrierObject, $country) {
        
    }

    public function label($consignment, $labelType = 'pdf', $size = '100x100') {

        $output = array();
        $this->serviceValues = new Services($consignment->getServiceId());
        $this->country = new Country($consignment->getCountryId());
        $this->userAccount = new CustomerAccount($consignment->getUserId());

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
        if (trim(@$this->constants['CTT_SHIPPER_CITY']) == '' || trim(@$this->constants['CTT_SHIPPER_POSTCODE']) == '' || trim(@$this->constants['CTT_SHIPPER_ADDRESSLINE1']) == '' ) {
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
        $licencePlate = new LicencePlate($licence_plate_id);



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
                    $licence_plate = $resultArray["PREFIX"] . sprintf('%07d',$range). $resultArray["SUFIX"];
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
            $this->addWayBill($consignment, $licence_plate);

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

    public function tracking($trackingNumber, $trackBy = 'parcel', $EDI = false) {
        
    }

    public function sendData($tracking_numbers = array()) {
        
    }

    public function manifest($consignment) {
        
    }

    public function preAdvice($consignment) {
        
    }

    private function addWayBill(Consignment $consignment, $awb) {

        $this->pdf->setFont("helvetica", "B", 9);
        $this->pdf->line(0, 0, 0, 100);
        $this->pdf->line(100, 0, 100, 100);
        $this->pdf->line(0, 100, 100, 100);
        $this->pdf->line(0, 0, 100, 0);
        $this->pdf->line(5, 0, 5, 30);
        $this->pdf->line(8, 30, 8, 70);
        $this->pdf->line(0, 30, 100, 30);
        $this->pdf->line(37, 0, 37, 30);
        $this->pdf->line(8, 63, 100, 63);
        $this->pdf->line(0, 70, 100, 70);
        $this->pdf->line(5, 26, 37, 26);
        $this->pdf->line(20, 70, 20, 100);
        $this->pdf->Image("../images/ctt_prime_registered_ppi.png", 40, 1,58, 27);
        $this->pdf->setFont("helvetica", "", 8);
        $this->pdf->Image("../images/ctt_weight.jpg", 10, 64, 6, 5);
        $this->pdf->Image("../images/logo.png", 6, 2, 24, 10);
        $this->pdf->Text(17, 65, $consignment->getWeight());
        $this->pdf->setFont("helvetica", "", 7);
        $this->pdf->Text(6, 11, "Sender Details:");
        $this->pdf->Text(6, 14, $this->constants["CTT_SHIPPER_ADDRESSLINE1"]);
        $this->pdf->Text(6, 17, $this->constants["CTT_SHIPPER_ADDRESSLINE2"]);
        $this->pdf->Text(6, 20, $this->constants["CTT_SHIPPER_CITY"]);
        $this->pdf->Text(5.5, 23, $this->constants["CTT_SHIPPER_POSTCODE"]);
        $this->pdf->Text(6, 27, "Ref:");
        
        $this->pdf->setFont("helvetica", "B", 10);
        $this->pdf->StartTransform();
       
	$this->pdf->Rotate(90, 5, 17);
        $this->pdf->setFont("freesans", "B", 9);
        $this->pdf->Text(1, 13, "FROM");
        $this->pdf->StopTransform();
        
        $this->pdf->StartTransform();
        $this->pdf->Rotate(90, 10, 41);
        $this->pdf->Text(0, 33, "TO");
        $this->pdf->StopTransform();

        if ($consignment->getCompany() != "")
            $company = ($consignment->getCompany());
        if ($consignment->getAddressLine1() != "")
            $address1 = ($consignment->getAddressLine1());
        if ($consignment->getAddressLine2() != "")
            $address2 = ($consignment->getAddressLine2());
        if ($consignment->getAddressLine3() != "")
            $address3 = ($consignment->getAddressLine3());

        $this->pdf->setFont("freesans", "", 10);

        $y = 32;
        $x = 9;
        $inc = 5;

        if (trim($company) != '') {
            $this->pdf->Text($x, $y, ucwords(strtoupper(trim($company))));
            $y += $inc;
        }

        if (trim($consignment->getContact()) != '') {
            $this->pdf->Text($x, $y, ucwords(strtoupper(trim($consignment->getContact()))));
            $y += $inc;
        }
        
        if(trim($consignment->getTelephone()) != ''){
            $this->pdf->Image("../images/telephone1.png", 60, 32,5, 5);
            $this->pdf->Text(68, 32, ucwords(strtoupper(trim($consignment->getTelephone()))));
        }
        if(trim($consignment->getEmail()) != ''){
            $this->pdf->Text(68, 36,"E-Mail " . ucwords(strtoupper(trim($consignment->getEmail()))));
        }

        if (trim($address1) != '') {
            $this->pdf->Text($x, $y, ucwords(strtoupper(trim($address1))));
            $y += $inc;
        }

        if (trim($address2) != '') {
            $this->pdf->Text($x, $y, trim(strtoupper(trim($address2))));
            $y += $inc;
        }

        if (trim($address3) != '') {
            $this->pdf->Text($x, $y, trim(strtoupper(trim($address3))));
            $y += $inc;
        }

        if (trim($consignment->getCity()) != '') {
            $this->pdf->Text($x, $y, trim(strtoupper(trim($consignment->getCity()))));
            $y += $inc;
        }

        if (trim($consignment->getPostcode()) != '') {
            $this->pdf->Text($x, $y, strtoupper($consignment->getPostcode()) . "  ". strtoupper($consignment->getCountry()) );
            $y += $inc;
        }
        $this->pdf->setFont("freesans", "B", 6);
        $this->pdf->Image("../images/nosignature.jpg", 5, 75, 6, 6);
        $this->pdf->Text(3, 82, "No Signature");
        $this->pdf->Image("../images/Scan.jpg", 5, 87, 6, 6);
        $this->pdf->Text(3, 94, "Scan");
        
        $this->pdf->write1DBarcode($awb, 'C128', 30, 74, 70, 15, 0.4, $style, '');
        $this->pdf->setFont("freesans", "", 10);
        $arr = str_split($awb);	
        $this->pdf->Text(45, 92, $arr[0].$arr[1]. " " .$arr[2].$arr[3].$arr[4].$arr[5].$arr[6].$arr[7].$arr[8].$arr[9].$arr[10]. " " .$arr[11].$arr[12]);
        $postCode = str_replace("-", "", $consignment->getPostcode());
        $arr_postCode = str_split($postCode);
        $this->pdf->write1DBarcode("ZIP". $postCode, 'C39', 63, 48, 30, 10, 0.5, $style, '');
        $this->pdf->Text(67, 58, "ZIP" . $arr_postCode[0] . $arr_postCode[1] . $arr_postCode[2] .$arr_postCode[3] .$arr_postCode[4].$arr_postCode[5] .$arr_postCode[6]);
    }

    public function recycledShipment($consignment) {
        $output["STATUS"] = "SUCCESS";
        return $output;
    }

}
