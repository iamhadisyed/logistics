<?php

class LapostePriority implements CarrierService {

    private $pdf;
    private $serviceValues = null;
    private $userAccount = null;
    private $constants = null;
    private $user = null;
    private $country = null;

    public function __construct() {
        
    }

    public function validation(Consignment $consignment, Services $service, Country $country) {
        
    }

    public function remoteareas($consignment, $carrierObject, $sender) {
        
    }

    public function label($consignment, $labelType = 'pdf', $size = '') {

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
            $page_size = array(100, 100);
            $this->pdf->AddPage("P", $page_size);
            $this->addWayBill($consignment, $this->constants, $licence_plate);
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

    private function addWayBill(Consignment $consignment, $constants, $licence_plate) {

        $this->pdf->line(3, 51, 98, 51);
        $this->pdf->line(3, 3, 98, 3);
        $this->pdf->line(98, 3, 98, 23.8);
        $this->pdf->line(3, 3, 3, 30);
        $this->pdf->line(35, 3, 35, 27);
        $this->pdf->Line(46, 51, 46, 60);
        $this->pdf->Line(58, 51, 58, 60);
        $this->pdf->Line(76, 51, 76, 60);
        $this->pdf->line(3, 27, 98, 27);
        $this->pdf->line(3, 24, 3, 99);
        $this->pdf->line(3, 99, 98, 99);
        $this->pdf->line(98, 24, 98, 99);
        $this->pdf->line(3, 60, 98, 60);
        $this->pdf->line(3, 70, 3, 99);
        $this->pdf->line(98, 70, 98, 99);
        $this->pdf->line(3, 99, 98, 99);


        $image = realpath(SETTING_DIR_REMOTE . "images/logo.jpg");
        $this->pdf->image($image, 4, 8, 30);
        $image1 = realpath(SETTING_DIR_REMOTE . "images/laposte_priority.jpg");
        $this->pdf->image($image1, 38, 4, 60, 22);

        $this->pdf->setFont("helvetica", "b", 10);
        $this->pdf->Text(20, 53, $this->country->getIso());

        if ($consignment->getValue() >= 0 && $consignment->getValue() < 15) {
            $this->pdf->Text(50, 53, "L");
        } else
        if ($consignment->getValue() >= 15 && $consignment->getValue() < 135) {
            $this->pdf->Text(50, 53, "M");
        } else
        if ($consignment->getValue() >= 135) {
            $this->pdf->Text(50, 53, "H");
        }
        $this->pdf->Text(60, 53, $this->userAccount->getUserAccount());
        if ($this->country->getRegion() == 'INT')
            $this->pdf->Text(80, 53, "NON EU");
        else if ($this->country->getRegion() == 'R1')
            $this->pdf->Text(80, 53, "EU");
        else if ($this->country->getRegion() == 'DBP')
            $this->pdf->Text(80, 53, "UK");

        $this->pdf->Text(5, 62, "Delivery Details:");

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


        $this->pdf->write1DBarcode($licence_plate, 'C128', 7, 22, '', 35, 2, $style, '');

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
        $this->pdf->Text(5, 67, $consignment->getHawb());
        $this->pdf->Text(5, 71, $company);
        //$this->pdf->Text(55, 75, "Email: ".$consignment->getEmail());
        $this->pdf->Text(5, 75, $consignment->getContact());
        $this->pdf->MultiCell(85, 12, $address1 . " " . $address2 . " " . $address3, 0, 'L', false, 1, 5, 79);
        $this->pdf->Text(5, 87, $consignment->getCity() . " " . $consignment->getPostcode());
//			$this->pdf->Text(55, 99, "Tel: ".$consignment->getTelephone());
        $ref = $consignment->getNotes();
        $this->pdf->setFont("helvetica", "", 10);
        $this->pdf->SetFont('kozminproregular', '', 7);
        if (strlen($ref) > 45) {

            $wrapref = chunk_split($ref, 45) . "\n";

            $reference = explode("\n", $wrapref);
            //print_r($reference);	
            $y = 92;
            foreach ($reference as $refe) {

                $this->pdf->Text(5, $y, $refe);
                $y += 5;
            }
        } else {
            $this->pdf->Text(5, 92, $ref);
        }

        $this->pdf->setFont("helvetica", "B", 11);
        $this->pdf->Text(5, 99, $this->country->getName());

        return;
    }

    public function recycledShipment($consignment) {
        $output["STATUS"] = "SUCCESS";
        return $output;
    }

}
