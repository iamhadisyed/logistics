<?php

class Cpost implements CarrierService {

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

    public function label($consignment,$type = "pdf",$size = "100x150") {

        $output = array();
        $this->serviceValues = new Services($consignment->getServiceId());
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
                    $checkdigit = "";
                    if($this->serviceValues->getCode() == "STCPOSTTR"){
                        $checkdigit = LicencePlate::mod11($resultArray["RANGE"]);
                    }
                    $licence_plate = $resultArray["PREFIX"] . $resultArray["RANGE"] . $checkdigit . $resultArray["SUFIX"];
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

    public function tracking($trackingNumber, $trackBy = 'parcel', $EDI='false') {
        
    }

    public function sendData($tracking_numbers = array()) {
        
    }

    public function manifest($consignment) {
        
    }

    public function preAdvice($consignment) {
        
    }

    private function addWayBill(Consignment $consignment, $parcel_idx, $parcel_count, $licence_plate) {

        $handling = $this->serviceValues->getCode();
        $image = realpath("../images/cpostindicia.png");
        $this->pdf->image($image, 3, 3.5, 60, 20);


        $image1 = realpath("../images/logo.jpg");
        $this->pdf->image($image1, 65, 8, 30);

        $this->pdf->setFont("helvetica", "", 8);
        $this->pdf->MultiCell(45, 10, ucfirst(strtolower($consignment->getNotes())), 0, 'L', false, '', 18, 59);
        $this->pdf->line(3, 58, 98, 58);
        $this->pdf->line(3, 3, 98, 3);
        $this->pdf->line(98, 3, 98, 23.8);
        $this->pdf->line(3, 3, 3, 30);

        $this->pdf->setFont("helvetica", "b", 10);
        $this->pdf->Text(5, 59, "Notes:");

        if ($parcel_count > 0) {
            $this->pdf->setFont("helvetica", "b", 14);
            $this->pdf->Text(63, 59, "Pieces : " . ($parcel_idx + 1) . "/" . $parcel_count);
        }

        $this->pdf->setFont("helvetica", "", 12);
        $this->pdf->Text(5, 71, "Delivery Details:");


        $this->pdf->Text(11, 136.5, $this->country->getIso());
        $this->pdf->setFont("helvetica", "L", 9);

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


        $this->pdf->write1DBarcode($licence_plate, 'C128', 7, 25, '', 40, 2, $style, '');


        if ($this->country->getRegion() == "INT")
            $this->pdf->Text(7 + 25, 25 + 1, "For internal use only");


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



        $this->pdf->setFont("helvetica", "", 10);
        $this->pdf->Text(5, 77, $consignment->getHawb());
        $this->pdf->Text(5, 81, $company);
        $this->pdf->Text(5, 85, $consignment->getContact());
        $this->pdf->Text(5, 89, $address1);
        $this->pdf->Text(5, 93, $address2);
        $this->pdf->Text(5, 97, $address3);
        $this->pdf->Text(5, 101, $consignment->getCity());
        $this->pdf->Text(5, 105, $consignment->getPostcode());
        $this->pdf->setFont("helvetica", "B", 11);
        $this->pdf->Text(5, 109, $this->country->getName());
        $this->pdf->setFont("helvetica", "L", 11);

        $this->pdf->setFont("helvetica", "B", 8);
        $this->pdf->Text(34, 133, "if undelivered return to:");

        $this->pdf->setFont("helvetica", "L", 8);
        $this->pdf->Text(34, 136, "P.o.Box 10006");
        $this->pdf->Text(34, 139, "Curacao");
    }

    public function recycledShipment($consignment) {
        $output["STATUS"] = "SUCCESS";
        return $output;
    }

}
