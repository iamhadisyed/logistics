<?php
class RoyalMailUntrackedEconomy implements CarrierService {

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
                    $post_code = str_replace(" ", "", $consignment->getPostcode());
                    $post_code = str_pad($post_code, 8, "0", STR_PAD_RIGHT);
                    $licence_plate = $resultArray["PREFIX"] . $resultArray["RANGE"] . $post_code;
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
            $this->addWayBill($consignment, $parcel_idx, $licence_plate);

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

    private function addWayBill(Consignment $consignment, $parcel_idx, $licence_plate) {
        // BORDER LINES
        $this->pdf->line(2, 2, 98, 2);
        $this->pdf->line(2, 98, 98, 98);
        $this->pdf->line(2, 2, 2, 98);
        $this->pdf->line(98, 2, 98, 98);
        //TOP LINE UNDER IMAGE
        $this->pdf->line(2, 20, 98, 20);
        //TOP TO MIDDLE LINE BETWEEN IMAGES
        $this->pdf->line(50, 2, 50, 20);
        // BOTTOM LINE UNDER BARCODE
        $this->pdf->line(2, 35, 98, 35);
        // BOTTOM HORIZONTALE LINE
        $this->pdf->line(2, 85, 98, 85);
        //RIGHT LINE NEXT TO RETURN ADDRESS
        $this->pdf->line(10, 35, 10, 85);
        // RIGHT LINE NEXT TO ADDRESS
        $this->pdf->line(85, 35, 85, 98);
        


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
            'stretchtext' => 4
        );

        $this->pdf->write1DBarcode($licence_plate, 'C128', 20, 21, '', 14, 0.28, $style, 'N');

        $this->pdf->setFont("helvetica", "B", 6);


        if (trim($this->user->getLogo()) != '')
            $image1 = realpath("../images/userlogo/" . $this->user->getLogo());
        else
            $image1 = realpath("../images/logo.jpg");


        if (file_exists($image1)) {
            $fitbox = 'C';
            $fitbox[1] = 'M';
            $this->pdf->image($image1, 10, 3, 30, 15, '', '', '', false, 700, '', false, false, 0, $fitbox, false, false);
        }
        $this->pdf->Text(10, 17, "www.oneworldexpress.com");

        if ($this->serviceValues->getCode() == "PRIORITY") {
            $image = realpath("../images/international-1st-class.jpg");
        } else if ($this->serviceValues->getCode() == "ECONOMY") {
            $image = realpath("../images/international-2nd-class.jpg");
        }

        if (file_exists($image)) {
            $this->pdf->image($image, 52, 3, 45, 15);
        }

       
        $this->pdf->StartTransform();
        $this->pdf->Rotate(90, 70, 70);
        $this->pdf->setFont("helvetica", "B", 10);

        $this->pdf->Text(70, 88, $this->serviceValues->getCode());
        // Stop Transformation
        $this->pdf->setFont("helvetica", "B", 7.5);
        $companyaddress = "One World Express";
        $useraddress = "One World Express, Pump Lane, Hayes, UK, UB3 3NB";

        $this->pdf->setFont("helvetica", "", 6);
        $this->pdf->Text(55, 3, "If Undelivered Return To: (" . $companyaddress . ")");
        $this->pdf->setFont("helvetica", "", 5);
        $this->pdf->Text(55, 6, $useraddress);
        $this->pdf->StopTransform();

        $this->pdf->setFont("helvetica", '', 10);
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


        $this->pdf->Text(12, 40, ucwords($company));
        $this->pdf->Text(12, 44, ucwords($consignment->getContact()));
        $this->pdf->Text(12, 48, ucwords($address1));
        $this->pdf->Text(12, 52, ucwords($address2));
        $this->pdf->Text(12, 56, ucwords($address3));
        $this->pdf->Text(12, 60, ucwords($consignment->getCity()));
        $this->pdf->Text(12, 64, ucwords($this->country->getName()));

        $this->pdf->setFont("helvetica", "B", 12);
        $this->pdf->Text(12, 70, $consignment->getPostcode());

        $fontname = $this->pdf->addTTFfont('../assets/fonts/simhei.ttf', 'TrueTypeUnicode', '', 32);
        $this->pdf->SetFont($fontname, '', 8);
        $this->pdf->Text(12, 78, "Notes: " . $consignment->getNotes());


        $style1 = array(
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


        $this->pdf->write1DBarcode($consignment->getHawb(), 'C128', 15, 85, '', 15, 0.5, $style1, 'Y');



      
      $this->pdf->setFont("helvetica", "B", 20);
      $this->pdf->Text(85, 87, $this->country->getIso());
      
    }

    public function recycledShipment($consignment) {
        $output["STATUS"] = "SUCCESS";
        return $output;
    }

}
