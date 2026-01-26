<?php
include_classes([
    'postitaliarouting.class',
    'postitaliaroutingfilter.class'
    ]);
class PostItalia implements CarrierService {

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
        $this->country = new Country($consignment->getCountryId());

      

        /*
         *  Get Tracking Number ranges
         */

        $serviceRangeMappingFilter = new ServiceRangeMappingFilter();
        $serviceRangeMappingFilter->addFilter("service_id = '" . $consignment->getServiceId() . "' AND agent_id = '" . $consignment->getAgentId() . "' ");
        $serviceRange = $serviceRangeMappingFilter->getList(false);
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
                    $licence_plate = $resultArray["PREFIX"] . str_pad($resultArray["RANGE"], 7, 0, STR_PAD_LEFT) . $resultArray["SUFIX"];
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
           $output = $this->addWayBill($consignment, $this->constants, $licence_plate);
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

        $output = array();
        $company = $consignment->getCompany();
        $contact = $consignment->getContact();
        $postcode = $consignment->getPostcode();
        $postItaliaRoutingFlr = new PostItaliaRoutingFilter();
        $postItaliaRoutingFlr->addFilter("zip_code = '" . sprintf('%05d',$postcode) . "'");
        $result = $postItaliaRoutingFlr->getList();

        if (count($result) > 0) {
            foreach ($result as $res) {
                $this->province = $res->getProvince();
                $this->ProvinceIsoCode = $res->getProvinceIsoCode();
                $this->routingFile = $res->getRoutingFile();
            }
        } else {
            $output["STATUS"] = "ERROR";
            $output["MESSAGE"] = "Unable to Provide Service at " . $postcode . " postcode.";
            return $output;
        }


        $this->pdf->setFont("helvetica", "L", 7);
        $image = realpath("../images/postitalia.jpg");
        $image_crono = realpath("../images/crono.png");
        $y = 2;
        $x = 2;
        $w = 40;
        $h = 35;

        $this->pdf->Rect(1, 1, 98, 148);
        $this->pdf->Rect(7, 110, 87, 24);

        $this->pdf->Text(8, 116, strtoupper($consignment->getDescription()));
        $this->pdf->Text(8, 120, round($consignment->getWeight(), 2) . " KG");

        $this->pdf->Line(7, 115, 94, 115);
        $this->pdf->Line(7, 124, 94, 124);
        $this->pdf->setFont("helvetica", "B", 8);
        $this->pdf->Text(7, 126, "RESO al MITTENTE da restituire a SDA BOLOGNA: BOLOGNA");
        $this->pdf->Text(7, 130, "INTERPORTO – Comparto 13.5 - 40010 BENTIVOGLIO (BO)");
        // $this->pdf->Text(7, 131, "");
        $this->pdf->setFont("helvetica", "L", 7);
        $this->pdf->Text(1, 142, "This is a non-negotiable waybill regulated exclusively by the 'general terms of transport',");
        $this->pdf->Text(1, 145, "included in the sales form, which the sender declares having read and accepted.");

        $this->pdf->image($image, $x, $y, $w, $h, '', '', '', false, 700, '', false, false, 0, '', false, false);
        $this->pdf->image($image_crono, $x + 45, $y + 3, $w - 24, $h - 20, '', '', '', false, 700, '', false, false, 0, '', false, false);

        $style = array(
            'position' => '',
            'align' => 'C',
            'stretch' => false,
            'fitwidth' => true,
            'cellfitalign' => '',
            'border' => false,
            'hpadding' => 'auto',
            'vpadding' => 'auto',
            'fgcolor' => array(0,0,0),
            'bgcolor' => false, //array(255,255,255),
            'text' => true,
            'font' => 'helvetica',
            'fontsize' => 7,
            'stretchtext' => 1
            );
        $this->pdf->write1DBarcode(sprintf('%05d',$consignment->getPostcode()), 'C128', 56, 17, '50', 23, 0.3, $style, 'Y');
        $this->pdf->setFont("helvetica", "", 7);
        $style1 = array(
            'position' => '',
            'align' => 'C',
            'stretch' => false,
            'fitwidth' => true,
            'cellfitalign' => '',
            'border' => false,
            'hpadding' => 'auto',
            'vpadding' => 'auto',
            'fgcolor' => array(0,0,0),
            'bgcolor' => false, //array(255,255,255),
            'text' => false,
            );
        $this->pdf->write1DBarcode($licence_plate, 'C128', 46, 41, 50, 20, 0.3, $style1, 'Y');
        //$this->pdf->write1DBarcode($licence_plate, 'C128', 46, 41, '52', 17, 0.3, '', 'Y');
        $this->pdf->Text(48, 39, $licence_plate);

        $this->pdf->setFont("helvetica", "", 7.1);
        $this->pdf->Text(2, 20, "MITTENTE");
        $this->pdf->Text(2, 23, "SENGI7");
        $this->pdf->Text(2, 26, "Unit 04,7/F BRIGHT WAY TOWER");
        $this->pdf->Text(2, 29, "n.33 MONG KOK RD KL - Hong Kong");

        $this->pdf->setFont("helvetica", "L", 8);
        if ($contact == '') {
            $this->pdf->Text(7, 64, $company);
        } else {
            $this->pdf->Text(7, 64, $contact);
        }
        $this->AddressLine1 = preg_replace('/[^a-zA-Z0-9\s]/', '', $consignment->getAddressLine1());
        $this->AddressLine2 = preg_replace('/[^a-zA-Z0-9\s]/', '', $consignment->getAddressLine2());

        $this->barcode2D($consignment, $licence_plate);
        $this->pdf->Text(7, 68, $this->AddressLine1 . ' ' . $this->AddressLine2);
        $this->pdf->Text(7, 72, $consignment->getPostcode() . ' ' . $consignment->getCity() . ' ' . $this->ProvinceIsoCode);
        $this->pdf->Text(7, 105, "Tel. Dest.:");
        $this->pdf->Text(27, 105, $consignment->getTelephone());
        $this->pdf->Text(11, 111, $this->routingFile);
        //$this->pdf->Line(1, 100, 99, 100);
        return;
    }

    public function barcode2D($consignment, $barcode) {

        $iso = $this->country->getIso();
        $dataMatrix = $barcode . "|";
        $dataMatrix .= trim($consignment->getHawb()) . "|";
        $dataMatrix .= trim($barcode) . "|";
        $dataMatrix .= trim($consignment->getContact()) . "|";
        $dataMatrix .= "|";
        $dataMatrix .= trim($this->AddressLine1) . trim($this->AddressLine2) . "|";
        $dataMatrix .= trim($consignment->getPostcode()) . "|";
        $dataMatrix .= trim($consignment->getCity()) . "|";
        $dataMatrix .= trim($this->ProvinceIsoCode) . "|";
        $dataMatrix .= trim($iso) . "|";
        $dataMatrix .= trim($consignment->getNumberPieces()) . "|";
        $dataMatrix .= "SENGI7" . "|";
        $dataMatrix .= "" . "|";
        $dataMatrix .= "Unit 04,7/F BRIGHT WAY TOWER n.33 MONG KOK RD KL" . "|";
        $dataMatrix .= "|";
        $dataMatrix .= "Hong Kong" . "|";
        $dataMatrix .= "HK|";
        $dataMatrix .= "CN|";
        $dataMatrix .= "CEC"; // service Code
        $dataMatrix .= "|";
        $dataMatrix .= "|";
        $dataMatrix .= "|";
        $dataMatrix .= "||";
        $dataMatrix .= $consignment->getTelephone() . "|";
        $dataMatrix .= "|";
        $dataMatrix .= "|";
        $dataMatrix .= round($consignment->getWeight(), 2) . "|";

        $this->pdf->write2DBarcode($dataMatrix, 'DATAMATRIX', 63, 67, 48, 48, '', '', '');
    }

    public function recycledShipment($consignment) {
        $output["STATUS"] = "SUCCESS";
        return $output;
    }

}
