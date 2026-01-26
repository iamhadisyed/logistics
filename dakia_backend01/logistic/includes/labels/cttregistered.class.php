<?php

class CttRegistered implements CarrierService {

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

    public function label($consignment, $labelType = 'pdf', $size = '100x150') {

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
        if (trim(@$this->constants['CTT_SHIPPER_CITY']) == '' || trim(@$this->constants['CTT_SHIPPER_POSTCODE']) == '' || trim(@$this->constants['CTT_SHIPPER_ADDRESSLINE1']) == '' || trim(@$this->constants['CTT_SHIPPER_CONTACT']) == '') {
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
                    $checkdigit = LicencePlate::mod11($range);
                    $licence_plate = $resultArray["PREFIX"] . $range . $checkdigit . $resultArray["SUFIX"];
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

    public function tracking($trackingNumber, $trackBy = 'parcel', $EDI = false) {
        
    }

    public function sendData($tracking_numbers = array()) {
        
    }

    public function manifest($consignment) {
        
    }

    public function preAdvice($consignment) {
        
    }

    private function addWayBill(Consignment $consignment, $parcel_idx, $parcel_count, $awb) {


        $this->pdf->setFont("helvetica", "B", 9);

        $this->pdf->StartTransform();
        $this->pdf->Rotate(-270);
        //$this->pdf->Cell(5, 5, 'From', 1, 1, 'L',0,'');
        $this->pdf->Text(0, 3, "FROM");
        $this->pdf->StopTransform();


        $this->pdf->line(0, 0, 0, 150);
        $this->pdf->line(100, 0, 100, 150);
        $this->pdf->line(0, 150, 100, 150);
        $this->pdf->line(8, 0, 8, 30);
        $this->pdf->line(0, 0, 100, 0);
        $this->pdf->line(0, 30, 100, 30);
        $this->pdf->line(35, 0, 35, 30);
        $this->pdf->line(0, 56, 100, 56);
        $this->pdf->line(0, 63, 100, 63);

        $this->pdf->Image("../images/ctt-logo-indicia.jpg", 35, 0, 65, 30);

        $logo = $this->userAccount->getLogo();

        if (trim($logo) != '')
            $this->pdf->Image("../images/userlogo/" . $logo, 10, 5, 24, 20);
        else
            $this->pdf->Image("../images/logo-cn.jpg", 10, 5, 24, 20);

        $this->pdf->setFont("helvetica", "", 7);
        if (trim($consignment->getTelephone()) != '') {
            $this->pdf->Image("../images/telephone.png", 64, 38, 6, 6);
            $this->pdf->Text(68, 40, $consignment->getTelephone());
        }

        if (trim($consignment->getEmail()) != '') {
            $this->pdf->Text(60, 47, "E-mail:");
            $this->pdf->Text(60, 50, $consignment->getEmail());
        }

        $this->pdf->setFont("helvetica", "", 6);

        $this->pdf->Image("../images/ctt_weight.jpg", 3, 57, 6, 5);
        $this->pdf->Image("../images/ctt-scan.jpg", 50, 57, 6, 5);
        $this->pdf->Image("../images/ctt-signature.jpg", 85, 57, 6, 5);

        $this->pdf->Text(9, 58, $consignment->getWeight());

        $this->pdf->setFont("helvetica", "", 6);

        $this->pdf->Text(35, 3, "If undelivered");
        $this->pdf->Text(35, 6, $this->constants["CTT_SHIPPER_ADDRESSLINE1"]);
        $this->pdf->Text(35, 9, $this->constants["CTT_SHIPPER_ADDRESSLINE2"]);
        $this->pdf->Text(35, 12, $this->constants["CTT_SHIPPER_POSTCODE"]);
        $this->pdf->Text(35, 15, $this->constants["CTT_SHIPPER_CITY"]);

        $this->pdf->setFont("helvetica", "B", 14);

        $this->pdf->Text(3, 43, "TO");



        if ($consignment->getCompany() != "")
            $company = $consignment->getCompany();
        if ($consignment->getAddressLine1() != "")
            $address1 = $consignment->getAddressLine1();
        if ($consignment->getAddressLine2() != "")
            $address2 = $consignment->getAddressLine2();
        if ($consignment->getAddressLine3() != "")
            $address3 = $consignment->getAddressLine3();

        $this->pdf->setFont("helvetica", "", 7.5);

        $y = 32;
        $x = 18;
        $inc = 3.5;

        if ($company != '') {
            $this->pdf->Text($x, $y, ucwords(strtoupper($company)));
            $y += $inc;
        }

        if ($consignment->getContact() != '') {
            $this->pdf->Text($x, $y, ucwords(strtoupper($consignment->getContact())));
            $y += $inc;
        }

        if ($address1 != '') {
            $this->pdf->Text($x, $y, ucwords(strtoupper($address1)));
            $y += $inc;
        }

        if (trim($address2) != '') {
            $this->pdf->Text($x, $y, trim(strtoupper($address2)));
            $y += $inc;
        }

        if (trim($address3) != '') {
            $this->pdf->Text($x, $y, trim(strtoupper($address3)));
            $y += $inc;
        }

        if (trim($consignment->getCity()) != '') {
            $this->pdf->Text($x, $y, trim(strtoupper($consignment->getCity())) . "-" . strtoupper($consignment->getPostcode()));
            //$this->pdf->Text($x, $y, );
            $y += $inc;
        }



        if ($this->country->getName() != '') {
            //$this->pdf->setFont("helvetica", "B", 11);
            $this->pdf->Text($x, $y, strtoupper($this->country->getName()));
            $y += $inc;
        }

        $this->pdf->line(15, 30, 15, 56);

        $this->pdf->write1DBarcode($awb, 'C128', 11, 64, 120, 18, 0.5, $style, '');

        $this->pdf->setFont("helvetica", "", 10);

        $arr = str_split($awb);

        $this->pdf->Text(32, 83, $arr[0] . $arr[1] . " " . $arr[2] . $arr[3] . $arr[4] . $arr[5] . $arr[6] . $arr[7] . $arr[8] . $arr[9] . $arr[10] . " " .
                $arr[11] . $arr[12]);



        $this->pdf->line(0, 88, 100, 88);

        $this->pdf->setFont("helvetica", "B", 12);

        $this->pdf->Text(80, 90, "CN22");

        $this->pdf->setFont("helvetica", "B", 10);

        $this->pdf->Text(2, 88, "CUSTOMS DECLARATION");

        $this->pdf->setFont("helvetica", "B", 8);

        $this->pdf->Text(2, 92, "May be opened officially");

        $this->pdf->setFont("helvetica", "", 9);

        $this->pdf->line(0, 96, 100, 96);

        $this->pdf->Text(25, 97, "CTT Correios de Portugal, SA");

        $this->pdf->line(0, 102, 100, 102);


        $start = 105;
        $end = 107;

        $this->makeSquare(2, 4, $start, $end);
        $this->makeSquare(14, 16, $start, $end);
        //$this->makeSquare(24, 26, 102,  104);

        $this->makeSquare(44, 46, $start, $end);
        $this->makeSquare(58, 60, $start, $end);

        $this->makeSquare(86, 88, $start, $end);

        $this->pdf->line(86, $start, 88, $end);

        $this->pdf->line(88, $start, 86, $end);

        $this->pdf->Text(5, 104, "Gift        Comm. Sample        Docs        Returned Goods     Others");

        $this->pdf->line(0, 110, 100, 110);

        $this->pdf->Text(1, 112, "Quantity and detailed description of contents");

        $this->pdf->setFont("helvetica", "", 7);

        $this->pdf->Text(66, 112, "Weight (in Kg)");
        $this->pdf->Text(85, 112, "Value (USD)");


        $this->pdf->Text(71, 117, $consignment->getWeight());
        $this->pdf->Text(88, 117, $consignment->getValue());


        $this->pdf->line(66, 110, 66, 138);
        $this->pdf->line(85, 110, 85, 138);


        $this->pdf->line(0, 117, 100, 117);
        $this->pdf->Text(1, 117, $consignment->getDescription());
        $this->pdf->setFont("helvetica", "", 9);
        $this->pdf->line(0, 123, 100, 123);

        $this->pdf->Text(1, 123, "For commercial items only if known HS");
        $this->pdf->Text(1, 127, "tariff number and country of origin of goods");

        $this->pdf->setFont("helvetica", "", 7);

        $this->pdf->Text(67, 123, "Total Weight");
        $this->pdf->Text(71, 127, "(in Kg)");


        $this->pdf->Text(85, 123, "Total Value");
        $this->pdf->Text(88, 127, "(USD)");

        $this->pdf->Text(71, 133, $consignment->getWeight());
        $this->pdf->Text(88, 133, $consignment->getValue());


        $this->pdf->line(0, 131, 100, 131);

        $this->pdf->line(0, 138, 100, 138);



        $this->pdf->setFont("helvetica", "", 5);

        $this->pdf->Text(0, 140, "I, the undersigned, whose name and address are given on the item, certify that the particulars");
        $this->pdf->Text(0, 143, "given in thie declaration are correct and that this item does not contain any dangerous article or");
        $this->pdf->Text(0, 146, "articles prohibited by legislation or customs regulations. Date and sender's signature");

        $this->pdf->line(77, 138, 77, 150);
    }

    private function makeSquare($x1, $x2, $y1, $y2) {
        $this->pdf->line($x1, $y1, $x2, $y1);
        $this->pdf->line($x1, $y1, $x1, $y2);
        $this->pdf->line($x1, $y2, $x2, $y2);
        $this->pdf->line($x2, $y1, $x2, $y2);
    }

    public function recycledShipment($consignment) {
        $output["STATUS"] = "SUCCESS";
        return $output;
    }

}
