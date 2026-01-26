<?php

class Spring implements CarrierService {

    private $pdf;
    private $serviceValues = null;
    private $agentValues = null;
    private $constants = null;
    private $user = null;
    private $userAccount = null;
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
        $this->userAccount = new CustomerAccount($this->user->getUserAccountId());
        $this->country = new Country($consignment->getCountryId());


        $parcel_list = $consignment->getParcels();
        $parcel_count = sizeof($parcel_list);
        $parcel_idx = 0;
        $pdf = new PdfBase(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $this->pdf = $pdf;
        // Generate label for each parecel
        foreach ($parcel_list as $parcel) {
            if ($parcel->getTrackingNumber() == '') {
                $licence_plate = $consignment->getHawb();
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

    private function addWayBill(Consignment $consignment, $parcel_idx, $parcel_count, $licence_plate) {

        $handling = $this->serviceValues->getCode();

        $account = $this->userAccount->getUserAccount();

        $image = realpath("../images/springuk.jpg");
        $this->pdf->image($image, 5, 4, 65);

        $this->pdf->line(1, 51, 98, 51);
        $this->pdf->line(1, 1, 98, 1);
        $this->pdf->line(98, 1, 98, 23.8);
        $this->pdf->line(1, 1, 1, 30);
        $this->pdf->Line(50, 51, 50, 60);

        $this->pdf->line(1, 24, 98, 24);
        $this->pdf->line(1, 24, 1, 115);
        $this->pdf->line(1, 115, 98, 115);
        $this->pdf->line(98, 24, 98, 115);
        $this->pdf->line(1, 60, 98, 60);
        $this->pdf->line(1, 70, 1, 145);
        $this->pdf->line(98, 70, 98, 145);
        $this->pdf->line(1, 133, 98, 133);
        $this->pdf->line(1, 113, 1, 146);
        $this->pdf->line(1, 146, 98, 146);
        $this->pdf->line(98, 113, 98, 146);
        $this->pdf->line(30, 133, 30, 146);


        $this->pdf->setFont("helvetica", "b", 10);
        if ($consignment->getValue() >= 0 && $consignment->getValue() < 15) {
            $this->pdf->Text(25, 53, "L");
        } else
        if ($consignment->getValue() >= 15 && $consignment->getValue() < 135) {
            $this->pdf->Text(25, 53, "M");
        } else
        if ($consignment->getValue() >= 135) {
            $this->pdf->Text(25, 53, "H");
        }
        $this->pdf->Text(62, 53, $account);

        $this->pdf->Text(5, 62, "Delivery Details:");
        $this->pdf->Text(12, 137, $handling);


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
        $this->pdf->setFont("helvetica", "", 10);
        $this->pdf->Text(55, 95, $consignment->getReference());
        $this->pdf->setFont("helvetica", "B", 18);
        $this->pdf->Text(55, 136, "GLM 318");
    }

    public function recycledShipment($consignment) {
        $output["STATUS"] = "SUCCESS";
        return $output;
    }

}
