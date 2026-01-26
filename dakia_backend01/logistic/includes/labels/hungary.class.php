<?php
include_classes([
    'checkdigit.class',
    'carrierdatafilelog.class',
    'carrierdatafilelogfilter.class' 
    ]);
class Hungary implements CarrierService {

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

    public function label($consignment, $labelType = 'pdf', $size = '') {

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
        $pdf = new PdfBase(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $this->pdf = $pdf;
        // Generate label for each parecel
        foreach ($parcel_list as $parcel) {
            if ($parcel->getTrackingNumber() == '') {
                $resultArray = LicencePlate::getLicencePlateNumber($licence_plate_id);
                if (trim($resultArray['STATUS']) == 'ERROR')
                    return $resultArray;
                else {
                    $checkdigit = LicencePlate::mod11($resultArray["RANGE"]);
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

    public function tracking($trackingNumber, $trackBy = 'parcel', $EDI = false) {
        
    }

    public function sendData($tracking_numbers = array()) {
        
    }

    public function manifest($consignment) {
        
    }

    public function preAdvice($consignment) {
        
    }

    private function addWayBill(Consignment $consignment, $parcel_idx, $licence_plate) {
        $handling = $this->serviceValues->getCode();
//        if ($handling != "REGHUNUTR" && $handling != "REGHUNUTREUR" && $handling == "REGHUNUTRINT") {
//            $image = realpath("../images/Magyar_Posta_Logo.jpg");
//            $this->pdf->image($image, 80, 126, 17);
//        }

         $logo =    User::getUserCompanyImages(false,$consignment->getUserId());
       
        if (file_exists($logo))
        $this->pdf->image($logo, 1, 6, 45);

        $this->pdf->line(2, 2, 98, 2);
        $this->pdf->line(2, 148, 98, 148);
        $this->pdf->line(2, 2, 2, 148);
        $this->pdf->line(98, 2, 98, 148);
        $this->pdf->line(49, 2, 49, 25);

        $this->pdf->setFont("helvetica", "L", 8);
        $this->pdf->Text(70, 6, "P.P");
        $this->pdf->Text(65, 10, "PRIORITARE");
        $this->pdf->Text(49, 14, "MAGYAR POSTA - BUDAPEST 1005");
        $this->pdf->Text(68, 18, "KAAB");

        $this->pdf->line(2, 25, 98, 25);
        $this->pdf->line(2, 80, 98, 80);
        $this->pdf->line(2, 97, 98, 97);

        $this->pdf->setFont("helvetica", "B", 10);
        $weight_in_gram = $consignment->getWeight() * 1000;
        $this->pdf->Text(5, 90, "Weight: " . $weight_in_gram . " g");

        $this->pdf->SetFont('helvetica', 'B', 22);
        $this->pdf->Circle(88, 90, 5);

        if ($consignment->getValue() < 15)
            $this->pdf->Text(85, 85, "L");
        else if ($consignment->getValue() >= 15 && $consignment->getValue() < 135)
            $this->pdf->Text(84, 85, "M");
        else if ($consignment->getValue() >= 135)
            $this->pdf->Text(85, 85, "H");

        $company = "";
        if ($consignment->getCompany() != "")
            $company = $consignment->getCompany();


        $address = "";
        if ($consignment->getAddressLine1() != "")
            $address1 = $consignment->getAddressLine1();
        else
            $address1 = '';
        if ($consignment->getAddressLine2() != "")
            $address2 = $consignment->getAddressLine2();
        else
            $address2 = '';
        if ($consignment->getAddressLine3() != "")
            $address3 = $consignment->getAddressLine3();
        else
            $address3 = '';

        $this->pdf->setFont("helvetica", "", 10);
        $this->pdf->Text(5, 27, $company);
        $this->pdf->Text(5, 31, $consignment->getContact());
        $this->pdf->Text(5, 35, $address1);
        $this->pdf->Text(5, 39, $address2);
        $this->pdf->Text(5, 43, $address3);
        $this->pdf->Text(5, 50, $this->country->getIso() . "-" . $consignment->getPostcode() . "-" . $consignment->getCity());
        $this->pdf->setFont("helvetica", "B", 10);
        $this->pdf->Text(5, 55, $this->country->getName());
        $this->pdf->setFont("helvetica", "", 10);
        $this->pdf->Text(5, 65, "TEL: " . $consignment->getTelephone());
        $this->pdf->Text(5, 70, $consignment->getReference());

        $this->pdf->setFont("helvetica", "B", 16);
        $this->pdf->Text(5, 82, "Ref No:" . $consignment->getHawb());


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
            'font' => 'helvetica',
            'fontsize' => 8,
            'stretchtext' => 1
        );


        if ($handling != "REGHUNUTR" && $handling != "REGHUNUTREUR" && $handling != "REGHUNUTRINT") {

            $this->pdf->write1DBarcode($licence_plate, 'C128', 28, 105, '', 20, .35, $style, '');
            $arr = str_split($licence_plate);
            $tracking_number = $arr[0] . $arr[1] . " " . $arr[2] . $arr[3] . $arr[4] . " " . $arr[5] . $arr[6] . $arr[7] . " " . $arr[8] . $arr[9] . $arr[10] . " " . $arr[11] . $arr[12];

            $this->pdf->setFont("helvetica", "L", 13);
            $this->pdf->Text(33, 127, $tracking_number);

            $this->pdf->SetTextColor(255, 0, 0);
            $this->pdf->setFont("helvetica", "B", 60);
            $this->pdf->Text(5, 102, "R");
            $this->pdf->setFont("helvetica", "L", 9);
            $this->pdf->Text(5, 127, "Nemzetkozi");
            $this->pdf->Text(5, 131, "ajanlott");
            $this->pdf->SetTextColor(0, 0, 0);
            $this->pdf->setFont("helvetica", "L", 10);
            $this->pdf->Text(25, 136, "Please scan - Signature required");
            $this->pdf->Text(20, 139, "Veuillez scanner - Remise contre Signature");
            $this->pdf->Text(28, 100, "H-1005 BUDAPEST");
        } else {

            $this->pdf->write1DBarcode($consignment->getHawb(), 'C128', 28, 105, '', 20, .35, $style, '');
            $this->pdf->setFont("helvetica", "L", 13);
            $this->pdf->Text(33, 127, $consignment->getHawb());
        }
    }

    public function recycledShipment($consignment) {
        $output["STATUS"] = "SUCCESS";
        return $output;
    }

}
