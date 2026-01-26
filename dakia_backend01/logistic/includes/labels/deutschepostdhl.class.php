<?php
include_classes([
    'carrierdatafilelog.class',
    'deutschepostdhlcargocode.class' ,
    'deutschepostdhlcargocodefilter.class',
    'deutschepostdhlstreetcode.class',
    'deutschepostdhlstreetcodefilter.class'
    ]);
class DeutschePostDhl implements CarrierService {

    private $pdf;
    private $serviceValues = null;
    private $agentValues = null;
    private $constants = null;
    private $user = null;
    private $country = null;
    private $booking_file = null;
    private $record_array = null;

    public function __construct() {
        
    }

    public function validation(Consignment $consignment, Services $service, Country $country) {
        $returnOutput = array();
        $deutschepostdhlCargoCodeFilter = new DeutschepostDhlCargoCodeFilter();
        $deutschepostdhlCargoCodeFilter->where("start_postcode <= CAST('" . $consignment->getPostcode() . "' AS UNSIGNED) and end_postcode >= CAST('" . $consignment->getPostcode() . "' AS UNSIGNED)");
        $cargoList = $deutschepostdhlCargoCodeFilter->getList();
        if (count($cargoList) <= 0) {
            $returnOutput[] = "This service is not available on " . $consignment->getPostcode() . " postcode.";
        }
        return $returnOutput;
    }

    public function remoteareas($consignment, $carrierObject, $sender) {
        
    }

    public function label($consignment, $labelType = 'pdf', $size = '') {

        $output = array();
        $this->serviceValues = new Services($consignment->getServiceId());
        $this->user = SessionManager::getUser();
        $this->country = new Country($consignment->getCountryId());

        $serviceAgentConstantFilter = new ServiceConstantValueFilter();
        $serviceAgentConstantFilter->addFilter("service_id = '" . $consignment->getServiceId() . "' AND agent_id = '" . $consignment->getAgentId() . "' ");
        $serviceAgentConstant = $serviceAgentConstantFilter->getConstantList("c.constant 'constant_name', scv.constant_value 'constant_value'");
        if (count($serviceAgentConstant) > 0) {
            foreach ($serviceAgentConstant as $serviceAgentConstantData) {
                $this->constants[$serviceAgentConstantData->getConstantName()] = $serviceAgentConstantData->getConstantValue();
            }
        }
        if (trim(@$this->constants['DEUTSCHEPOST_SENDER_NAME']) == '') {
            $output['STATUS'] = 'ERROR';
            $output['MESSAGE'] = "Please check service constants. Please contact to itsupport@oneworldexpress.com";
            return $output;
        }


        $parcel_list = $consignment->getParcels();
        $parcel_count = sizeof($parcel_list);
        $parcel_idx = 0;
        $pdf = new PdfBase(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $this->pdf = $pdf;
        // Generate label for each parecel
        foreach ($parcel_list as $parcel) {


            $this->pdf->SetPrintFooter(false);
            $this->pdf->SetFooterMargin(0);
            $this->pdf->SetAutoPageBreak(false, 0);
            $page_size = array(100, 150);
            $this->pdf->AddPage("L", $page_size);
            $response = $this->addWayBill($consignment, $this->constants);
            if ($response["STATUS"] == "SUCCESS") {
                $licence_plate_array[$parcel_idx] = $response["MESSAGE"];
            } else {
                return $response;
            }

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

    private function addWayBill(Consignment $consignment, $constants) {
        $output = array();
        $maxPrecision = 5;

        $this->pdf->setFont("helvetica", "", 9);
        //  $this->pdf->Text(3, 22 , "Safeplace" );
        $this->pdf->Text(5, 5, "Absender: ");
        $this->pdf->Text(5, 8, $constants["DEUTSCHEPOST_SENDER_NAME"]); //"Wolanski World Postal Service "
        $this->pdf->Text(5, 11, "GmbH");
        if ($consignment->getSenderAddressLine1() != '')
            $this->pdf->Text(5, 19, $consignment->getSenderAddressLine1());
        if ($consignment->getSenderAddressLine2() != '')
            $this->pdf->Text(5, 22, $consignment->getSenderAddressLine2());
        if ($consignment->getSenderAddressLine3() != '')
            $this->pdf->Text(5, 25, $consignment->getSenderAddressLine3());
        if ($consignment->getSenderCountryId() > 0) {
            $senderCountry = new Country($consignment->getSenderCountry());
            $this->pdf->Text(5, 28, $senderCountry);
        }


        $this->pdf->Text(5, 16, 'C/O:');
        $style = "";
        $this->pdf->setFont("helvetica", "B", 9);
        $this->pdf->Text(94, 5, "Deutsche Post DHL");
        $carrierId = $this->serviceValues->getCarrierId();

        $run_number = CarrierDataFileLog::generateRunNumber($carrierId, $consignment->getAgentId());
        $run_number = sprintf('%05d', $run_number);
        $deutscheRunnumber = "564333" . $run_number;
        $chkdigit = $this->Mod10CheckDigit($deutscheRunnumber);
        $deutscheRunumberBarcode = $deutscheRunnumber . $chkdigit;
        $this->pdf->write1DBarcode("$deutscheRunumberBarcode", 'I25', 85, 10, '', 23, 0.5, $style, 'Y');
        $this->pdf->setFont("helvetica", "", 9);
        $this->pdf->Text(95, 35, "56.4333." . $run_number . "  " . $chkdigit);

        $deutschepostdhlCargoCodeFilter = new DeutschepostDhlCargoCodeFilter();
        $deutschepostdhlCargoCodeFilter->where("start_postcode <= CAST('" . $consignment->getPostcode() . "' AS UNSIGNED) and end_postcode >= CAST('" . $consignment->getPostcode() . "' AS UNSIGNED)");
        $cargoList = $deutschepostdhlCargoCodeFilter->getList();

        if (count($cargoList) > 0) {
            $cargocode = $cargoList[0]->getCargoCode();
        }




        $this->pdf->setFont("helvetica", "B", 9);
        $this->pdf->Text(20, 45, "Paketzentrum " . $cargocode);
        $this->pdf->Text(85, 48, $consignment->getHawb());
        //echo $consignment->getReference(); die;
        if ($consignment->getReference() != '')
            $this->pdf->Text(105, 48, 'Ref: ' . $consignment->getReference());
        $this->pdf->line(85, 53, 130, 53);
        $postcode = $consignment->getPostcode();
        $streetaddress = GenericFunctions::getDoorNumber($consignment->getAddressLine1());
        //print_r($streetaddress); die;
        if (sizeof($streetaddress) > 0) {

            $doornumber = $streetaddress["number"];
            $streetname = trim(utf8_encode($streetaddress["street"]));
            $streetNumber = sprintf("%03d", $doornumber);
        }



        $deutschepostDhlStreetCodeFilter = new deutschepostDhlStreetCodeFilter();

        $deutschepostDhlStreetCodeFilter->addFilter("levenshtein(street,  '" . $streetname . "') <=" . $maxPrecision . " and zipcode = '" . trim($postcode) . "'");
        $deutschepoststreetlist = $deutschepostDhlStreetCodeFilter->getList();
        if (count($deutschepoststreetlist) > 0) {

            $streetDigiit = sprintf("%03d", $deutschepoststreetlist[0]->getStreetCode());

            $productCode = "00";
            $postcodeLength = strlen($postcode);

            if (!is_numeric($postcode) || $postcodeLength > 5) {
                $output["STATUS"] = "ERROR";
                $output["MESSAGE"] = "Postcode should be 5 digit only.";
                return $output;
            } else if (!is_numeric($streetNumber)) {
                $output["STATUS"] = "ERROR";
                $output["MESSAGE"] = "Please add street number in address line 1.";
                return $output;
            } else {
                $barcode = $postcode . $streetDigiit . $streetNumber . $productCode;
                $checkDigit = $this->Mod10CheckDigit($barcode);
                $licence_plate = $barcode . $checkDigit;
                $this->pdf->write1DBarcode($licence_plate, 'I25', 5, 50, '', 23, 0.5, $style, 'Y');
                $this->pdf->setFont("helvetica", "", 9);
                $barcodeDisplay = $postcode . "." . $streetDigiit . "." . $streetNumber . " " . $productCode . " " . $checkDigit;
                $this->pdf->Text(15, 75, $barcodeDisplay);
            }
            $this->pdf->setFont("helvetica", "", 9);
            $this->pdf->Text(85, 55, $consignment->getContact());

            $this->pdf->MultiCell(50, 5, $consignment->getAddressLine1() . " " . str_replace("-", "", $consignment->getAddressLine2()), 0, 'L', '', '', 85, 59);
            $this->pdf->Text(85, 64, $consignment->getPostcode() . " " . $consignment->getCity());
            $output["STATUS"] = "SUCCESS";
            $output["MESSAGE"] = $licence_plate;
            return $output;
        } else {
            $output["STATUS"] = "ERROR";
            $output["MESSAGE"] = "Invalid address please enter correct address.";
            return $output;
        }
    }

    public function recycledShipment($consignment) {
        $output["STATUS"] = "SUCCESS";
        return $output;
    }

    private function Mod10CheckDigit($trackingnumber) {
        $bln = true;
        $dblsum = 0;

        for ($intl = 0; $intl < strlen($trackingnumber); $intl++) {
            if ($bln) {
                $dblsum = $dblsum + substr($trackingnumber, $intl, 1) * 4;
                $bln = false;
            } else {
                $dblsum = $dblsum + substr($trackingnumber, $intl, 1) * 9;
                $bln = true;
            }
        }

        if (($dblsum % 10) == 0) {
            $modulo10 = 0;
        } else {
            $modulo10 = 10 - ($dblsum % 10);
        }
        return $modulo10;
    }

}
