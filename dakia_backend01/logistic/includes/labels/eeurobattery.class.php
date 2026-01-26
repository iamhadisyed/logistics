<?php

class EEuroBattery implements CarrierService {

    private $pdf;
    private $serviceValues = null;
    private $userAccount = null;
    private $country = null;

    public function __construct() {
        $pdf = new PdfBase(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $this->pdf = $pdf;
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
         *  Get Service  constants 
         */
        $serviceAgentConstantFilter = new ServiceConstantValueFilter();
        $serviceAgentConstantFilter->addFilter("service_id = '" . $consignment->getServiceId() . "' AND agent_id = '" . $consignment->getAgentId() . "' ");
        $serviceAgentConstant = $serviceAgentConstantFilter->getConstantList("c.constant 'constant_name', scv.constant_value 'constant_value'");
        if (count($serviceAgentConstant) > 0) {
            foreach ($serviceAgentConstant as $serviceAgentConstantData) {
                $this->constants[$serviceAgentConstantData->getConstantName()] = $serviceAgentConstantData->getConstantValue();
            }
        }
        if (trim(@$this->constants['EURO_BATTERY_CLIENT_NUMBER']) == '') {
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
            $page_size = array(150, 100);
            $this->pdf->AddPage("L", $page_size);
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
        if ($this->country->getIso() == "BE")
            $Clientnumber = '10502113';
        else
            $Clientnumber = trim(@$this->constants['EURO_BATTERY_CLIENT_NUMBER']); //'10482764';

        $barcode = $licence_plate;
        $this->pdf->setFont("helvetica", '', 10);
        $this->pdf->text(10, 10, 'Containerweg 7');
        $this->pdf->text(10, 14, '2742 RA Waddinxveen');
        $this->pdf->rect(110, 7, 30, 9);
        $this->pdf->text(112, 8, 'FRANCO');
        if ($this->country->getIso() == 'BE') {
            $this->pdf->text(10, 6, 'COMP' . $consignment->getHawb());
            $this->pdf->text(10, 19, 'THE NETHERLANDS');
            $this->pdf->text(76, 19, 'POSTNL');
            $this->pdf->text(123, 19, 'CMR');
            $this->pdf->setFont("arial", 'B', 36);
            $this->pdf->text(10, 25, 'EU');
            $this->pdf->setFont("helvetica", '', 10);
        } else if ($consignment->getWeight() <= 10) {
            $this->pdf->text(10, 6, 'RMNL' . $consignment->getHawb());
            $this->pdf->text(10, 19, 'The Netherlands');
            $this->pdf->text(76, 19, 'DHL');
            $this->pdf->text(123, 19, '17');
        } else {
            $this->pdf->text(10, 6, 'RMNL' . $consignment->getHawb());
            $this->pdf->text(10, 19, 'The Netherlands');
            $this->pdf->text(76, 19, 'POSTNL');
            $this->pdf->text(123, 19, 'AVG');
        }
        $this->pdf->rect(75, 24, 63, 33);
        $this->pdf->rect(3, 3, 145, 94);

        $this->pdf->write1DBarcode($barcode, 'C39', 35, 65, 80, 24, 0.6, $style, 'N');
        $this->pdf->text(55, 90.5, '* ' . $barcode . ' *');
        $this->pdf->setfont('helvetica', '', '9');
        $this->pdf->Text(76, 25, $consignment->getCompany());
        $this->pdf->setfont('helvetica', 'B', '10');
        $this->pdf->Text(76, 29, $consignment->getContact());
        $this->pdf->Text(76, 33, str_replace("-", "", $consignment->getAddressLine1()));
        $this->pdf->Text(76, 37, str_replace("-", "", $consignment->getAddressLine2()));
        $this->pdf->Text(76, 41, str_replace("-", "", $consignment->getAddressLine3()));
        $this->pdf->Text(76, 45, $consignment->getPostcode() . ' ' . $consignment->getCity());
        if ($this->country->getIso() == "BE") {
            $this->pdf->Text(76, 49, $this->country->getName());
            $this->pdf->setfont('helvetica', '', '8');
            $this->pdf->Text(120, 57, "Collo 1");
        }
    }

    public function recycledShipment($consignment) {
        $output["STATUS"] = "SUCCESS";
        return $output;
    }

}
