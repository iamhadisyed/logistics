<?php
class RoyalMailInternationTracked implements CarrierService {

    private $pdf;
    private $serviceValues = null;
    private $agentValues = null;
    private $constants = null;
    private $user = null;
    private $country = null;

    public function __construct() {
        $pdf = new PdfBase(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $this->pdf = $pdf;
    }

    public function validation(Consignment $consignment, Services $service, Country $country) {
        
    }

    public function remoteareas($consignment, $carrierObject, $sender) {
        
    }

    public function label($consignment) {

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
                    $nextNumber = $resultArray["RANGE"];
                    $checkdigit = LicencePlate::mod11($nextNumber);

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
            $page_size = array(95, 87);
            $this->pdf->AddPage("P", $page_size);
            $this->addWayBill($consignment, $parcel_idx, $parcel_count, $licence_plate);

            $new_page_flag = true;
            ++$parcel_idx;
        }


        $data = $consignment->getDescription();

        if (count(array_intersect(explode(' ', $data), array('battery', 'batteries', 'lithuim', 'litthium', 'lithium battery', 'lithium batteries', 'li battery', 'batery', 'bateries')))) {
            $this->addCautionLabel("lithium_ion_battery.png");
        } else if (count(array_intersect(explode(' ', $data), array('medicines', 'medicine', 'aftershave', 'nail varnish', 'varnish', 'toiletry', 'medicinal', 'aerosols', 'aerosol', 'toilet', 'perfume', 'perfumes', 'alcohal', 'fragrance', 'fragrances', 'alcohol', 'scent')))) {
            $this->addCautionLabel("limited-quantity.jpg");
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

    private function addWayBill(Consignment $consignment, $parcel_idx, $parcel_count, $licence_plate) {

        // BORDER LINES
        $this->pdf->line(1, 1, 86, 1);
        $this->pdf->line(1, 94, 86, 94);
        $this->pdf->line(1, 1, 1, 94);
        $this->pdf->line(86, 1, 86, 94);

        $this->pdf->line(1, 59, 86, 59);
        $this->pdf->line(1, 90, 86, 90);

        $this->pdf->line(1, 20, 86, 20);




        $handling = $this->serviceValues->getCode();
        $this->pdf->setFont("freesans", "L", 8);
        if ($handling == "RMINTRS") {
            $this->pdf->Text(62, 33, "Signature");
            $this->pdf->Text(62, 54, "Signature");
            $image = "../images/Int_Tracked&Signed.jpg";
            $this->pdf->image($image, 3, 1, 50);
            $image = "../images/R_underlined.jpg";
            $this->pdf->image($image, 38, 4, 13);
            $image = "../images/ppi.jpg";
            $this->pdf->image($image, 56, 1, 25);
            $image = "../images/AIR_MAIL_PAR_AVION.jpg";
            $this->pdf->image($image, 3, 23, 23);
            $image = "../images/GB_Recommande.jpg";
            $this->pdf->image($image, 45, 21, 27);
        } else if ($consignment->getHandling() == "RMINTS") {
            $this->pdf->Text(62, 33, "Signature");
            $this->pdf->Text(62, 54, "Signature");
            $image = "../images/Int_Signed.jpg";
            $this->pdf->image($image, 3, 1, 50);
            $image = "../images/R_underlined.jpg";
            $this->pdf->image($image, 38, 4, 13);
            $image = "../images/ppi.jpg";
            $this->pdf->image($image, 56, 1, 25);
            $image = "../images/AIR_MAIL_PAR_AVION.jpg";
            $this->pdf->image($image, 3, 23, 23);
            $image = "../images/GB_Recommande.jpg";
            $this->pdf->image($image, 45, 21, 27);
        }


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
            'font' => 'freesans',
            'fontsize' => 8,
            'stretchtext' => 1
        );

        $this->pdf->write1DBarcode($licence_plate, 'C128', 25, 38, '55', 15, .30, $style, '');

        $first2digit = substr($licence_plate, 0, 2);
        $second4digit = substr($licence_plate, 2, 4);
        $third4digit = substr($licence_plate, 6, 4);
        $lastdigit = substr($licence_plate, 10, 11);

        $display_barcode = $first2digit . " " . $second4digit . " " . $third4digit . " " . $lastdigit;

        $this->pdf->setFont("freesans", "B", 10);
        $this->pdf->Text(42, 27, $display_barcode);
        $this->pdf->Text(25, 33, $display_barcode);
        $this->pdf->Text(25, 54, $display_barcode);

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



        $this->pdf->setFont("freesans", "L", 8.5);
        $this->pdf->Text(55, 60, $consignment->getHawb());
        $this->pdf->Text(5, 60, $company);
        $this->pdf->Text(5, 64, $consignment->getContact());
        $this->pdf->Text(5, 68, $address1);
        $this->pdf->Text(5, 71, @$address2);
        $this->pdf->Text(5, 74, @$address3);
        $this->pdf->Text(5, 78, $consignment->getCity());
        $this->pdf->Text(5, 82, $consignment->getPostcode());
        $this->pdf->Text(5, 86, $this->country->getIso());




        $this->pdf->setFont("freesans", "L", 6.5);
        $this->pdf->Text(3, 91, "Return to: One World Express, One World House, Pump Lane, Hayes, UB3 3NB, UK");
    }
    
     private function addCautionLabel($cautiontypeimage) {

        $this->pdf->SetPrintFooter(false);
        $this->pdf->SetFooterMargin(0);
        $this->pdf->SetAutoPageBreak(false, 0);

        $page_size = array(95, 87);

        $this->pdf->AddPage("P", $page_size);

        $image = "../images/" . $cautiontypeimage;
        $this->pdf->image($image, 1, 1, 86, 94);
    }

    public function recycledShipment($consignment) {
        $output["STATUS"] = "SUCCESS";
        return $output;
    }

}
