<?php
class DeutschePostUntracked implements CarrierService {

    private $pdf;
    private $serviceValues = null;
    private $country = null;

    public function __construct() {
        $pdf = new PdfBase(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $this->pdf = $pdf;
    }

    public function validation(Consignment $consignment, Services $service, Country $country) {
        
    }

    public function remoteareas($consignment, $carrierObject, $sender) {
        
    }

    public function label($consignment,$type = "pdf",$size = "100x150") {

        $output = array();
        $this->serviceValues = new Services($consignment->getServiceId());
        $this->country = new Country($consignment->getCountryId());



        $parcel_list = $consignment->getParcels();
        $parcel_count = sizeof($parcel_list);
        $parcel_idx = 0;
        
         /*
         *  Get Tracking Number ranges
         */
        $licence_plate_id = 0;
        $serviceRangeMappingFilter = new ServiceRangeMappingFilter();
        $serviceRangeMappingFilter->addFilter("service_id = '" . $consignment->getServiceId() . "' AND agent_id = '" . $consignment->getAgentId() . "' ");
        $serviceRange = $serviceRangeMappingFilter->getList(" licence_plate_id ");
        if (count($serviceRange) > 0) {
            $licence_plate_id = (int) $serviceRange[0]->getLicencePlateId();
        }
        
        // Generate label for each parecel
        foreach ($parcel_list as $parcel) {
            if ($parcel->getTrackingNumber() == '') {
                if($licence_plate_id > 0){
                    $resultArray = LicencePlate::getLicencePlateNumber($licence_plate_id);
                    if (trim($resultArray['STATUS']) == 'ERROR')
                        return $resultArray;
                    else {
                         $checkdigit = LicencePlate::mod11($resultArray["RANGE"]);
                        $licence_plate = $resultArray["PREFIX"] . sprintf('%08d',$resultArray["RANGE"]) .$checkdigit. $resultArray["SUFIX"] ;
                    }
                    $parcel->setTrackingNumber($licence_plate);
                }else{
                    $licence_plate = $consignment->getHawb();
                    $parcel->setTrackingNumber($licence_plate); 
                }
                $parcel->save();
            } else {
                $licence_plate = $parcel->getTrackingNumber();
            }
            
//            if ($parcel->getTrackingNumber() == '') {
//
//                $licence_plate = $consignment->getHawb();
//                $parcel->setTrackingNumber($licence_plate);
//                $parcel->save();
//            } else {
//                $licence_plate = $parcel->getTrackingNumber();
//            }
            $licence_plate_array[$parcel_idx] = $licence_plate;
            $this->pdf->SetPrintFooter(false);
            $this->pdf->SetFooterMargin(0);
            $this->pdf->SetAutoPageBreak(false, 0);
            $page_size = array(85, 85);
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

    public function tracking($trackingNumber, $trackBy, $EDI) {
        
    }

    public function sendData($tracking_numbers = array()) {
        
    }

    public function manifest($consignment) {
        
    }

    public function preAdvice($consignment) {
        
    }

    private function addWayBill(Consignment $consignment, $parcel_idx, $licence_plate) {



        $dpi = 300;
        $x = 5;
        $y = 25;

        $this->pdf->line(1, 1,84, 1);
        $this->pdf->line(1, 1, 1, 84);
        $this->pdf->line(1, 84, 84, 84);
        $this->pdf->line(84, 1, 84, 84);
        $this->pdf->line(1, 27, 84, 27);
        $this->pdf->line(1, 27, 1, 61);
        $this->pdf->line(84, 61, 84, 27);
        $this->pdf->line(1, 59, 84, 59);
        $this->pdf->line(1, 71, 84, 71);
        $this->pdf->line(62, 71.1, 62, 84);



        $image = realpath("../images/logo.jpg");

        $x = 5;
        $y = 3;
        $w = 30;
        $h = 22;


        $fitbox = 'C';
        $fitbox[1] = 'M';
        $this->pdf->Image($image, $x, $y, $w, $h, 'jpg', '', '', false, 700, '', false, false, 0, $fitbox, false, false);


        $image = "../images/deutsche_post_ppi.png";
        $this->pdf->image($image, 40, 3, 40, 20);
     

        $this->pdf->setFont("helvetica", "", 10);
        $address = "";
        $address1 = "";
        $address2 = "";
        $address3 = "";
        $company = "";
        if ($consignment->getCompany() != "")
            $company = $consignment->getCompany();
        if ($consignment->getAddressLine1() != "")
            $address1 = $consignment->getAddressLine1();
        if ($consignment->getAddressLine2() != "")
            $address2 = $consignment->getAddressLine2();
        if ($consignment->getAddressLine3() != "")
            $address3 = $consignment->getAddressLine3();

      

        $this->pdf->setFont("helvetica", "", 9);
        $this->pdf->Text(3, 30, $company);
        $this->pdf->Text(3, 33, $consignment->getContact());
        $this->pdf->Text(3, 36, $address1);
        $this->pdf->Text(3, 39, $address2);
        $this->pdf->Text(3, 42, $address3);
        $this->pdf->Text(3, 45, $consignment->getCity());
        $this->pdf->Text(3, 48, $this->country->getName());

        $this->pdf->setFont("helvetica", "B", 12);
        $this->pdf->Text(3, 52, $consignment->getPostcode());

        
        
        $style = array(
	'position' => '',
	'align' => 'C',
	'stretch' => true,
	'fitwidth' => true,
	'cellfitalign' => '',
	'border' => false,
	'hpadding' => '5',
	'vpadding' => 'auto',
	'fgcolor' => array(0,0,0),
	'bgcolor' => false, //array(255,255,255),
	'text' => true,
	'font' => 'helvetica',
	'fontsize' => 8,
	'stretchtext' => 4

        );

        $this->pdf->write1DBarcode($licence_plate, 'C128', 15, 58, '', 15, 0.5, $style, 'Y');
        $this->pdf->setFont("helvetica", "B", 10);

        $this->pdf->Text(10, 75, "www.oneworldexpress.com");
        $zone = $this->country->getIso();

        $this->pdf->setFont("helvetica", "B", 20);
        $this->pdf->Text(68, 73, $zone);

    }

    public function recycledShipment($consignment) {
        $output["STATUS"] = "SUCCESS";
        return $output;
    }

}
