<?php

class posteItalianeUntracked implements CarrierService {

    private $pdf;
    private $serviceValues = null;
    private $constants = null;
    private $user = null;
    private $country = null;

    public function __construct() {
        
    }

    public function validation(Consignment $consignment, Services $service, Country $country) {
         $returnOutput = array();
        $postcode = trim($consignment->getPostcode());
        if($postcode != "")
        {
            $postfilter = new posteItalianeUntrackedRoutineFilter();
            $postfilter->addFieldFilter("postcode", $postcode);
            $list = $postfilter->getList();
            if(count($list) > 0)
            {
                $sortation = $list[0]->getSortation();
                $consignment->setRoutingCodeEur($sortation);
            }
            else
            {
                 $returnOutput[] = "Unable to provide service to " . $postcode . " postcode.";
            }
        }
        return $returnOutput;
    }

    public function remoteareas($consignment, $carrierObject, $sender) {
        
    }

    public function label($consignment, $labelType = 'pdf', $size = '100x150') {

        $output = array();
        $this->user = SessionManager::getUser();

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
                    $licence_plate = $resultArray["PREFIX"] . $resultArray["RANGE"] . $resultArray["SUFIX"];
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

        $this->pdf->line(1, 1, 99, 1);
        $this->pdf->line(1, 1, 1, 99);
        $this->pdf->line(99, 1, 99, 99);
        $this->pdf->line(1, 99, 99, 99);
        $this->pdf->line(70, 5, 95, 5);
        $this->pdf->line(70, 15, 95, 15);
        $this->pdf->line(70, 25, 95, 25);
        $this->pdf->line(70, 5, 70, 25);
        $this->pdf->line(95, 5, 95, 25);
        
        
        $this->pdf->setFont("helvetica", "b", 10);
        $this->pdf->Text(3, 5, "MITTENTE:");
       // $this->pdf->Text(3, 9, "GFS");
        $this->pdf->setFont("helvetica", "", 9);
        $this->pdf->Text(3, 13, "CORREOS CAM-2 GFS");
        $this->pdf->Text(3, 17, "Ctra. Villaverde-Vallecas km. 3,5");
        $this->pdf->Text(3, 21, "28070 Madrid, Spain");
        
        $this->pdf->setFont("helvetica", "b", 11);
        $this->pdf->Text(71, 5, "FRANQUEO");
        $this->pdf->Text(71, 10, "PAGADO");
        $this->pdf->setFont("helvetica", "", 10);
        $this->pdf->Text(71, 16, "Distribucion");
        $this->pdf->Text(71, 20, "Internacional");
        $this->pdf->setFont("helvetica", "b", 10);
        
        
        $this->pdf->Text(71, 30, $consignment->getRoutingCodeEur());
        
        $this->pdf->setFont("helvetica", "b", 12);
        $this->pdf->Text(3, 30, "DESTINATARIO:");
        
        $this->pdf->setFont("helvetica", "", 10);
        $this->pdf->Text(3, 35, $consignment->getContact());
        $this->pdf->Text(3, 40, $consignment->getAddressLine1() . " " . $consignment->getAddressLine2() . " " . $consignment->getAddressLine3());
        $this->pdf->Text(3, 45, $consignment->getPostcode() . " " . $consignment->getCity());
        $this->pdf->Text(3, 50, $consignment->getTelephone());
        
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

        $this->pdf->Text(3, 60, "SPEDIZIONE ORDINARIA", false, false, true, 0, 0, 'C', false );
        $this->pdf->write1DBarcode($licence_plate, 'C128', 7, 60, '', 40, 2, $style, '');

       
    }

    public function recycledShipment($consignment) {
        $output["STATUS"] = "SUCCESS";
        return $output;
    }

}
