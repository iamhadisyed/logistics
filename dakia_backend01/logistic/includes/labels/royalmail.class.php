<?php

include_classes([
    'royalMailFtpCodes.class',
    'royalmailtrackingstatus.class',
], 'labels');
include_classes([
    'agentdata.class',
    'agentdatafilter.class',
    'carrierdatafilelog.class',
    'carrierdatafilelogfilter.class',
    'parcelbaggingmapping.class',
    'parcelbaggingmappingfilter.class',
    'bagging.class',
    'baggingfilter.class',
    'parcel.class',
    'consignment.class',
    'services.class',
    'reconciliationbagdata.class'
]);
include_classes([
    'tcpdf',
], '3rdparty/tcpdf');
include_classes([
    'fpdi',
], '3rdparty/fpdi');

class RoyalMail implements CarrierService {

    private $pdf;
    private $serviceValues = null;
    private $agentValues = null;
    private $constants = null;
    private $warehouse = null;
    private $user = null;
    private $link_file = null;
    private $meterNumber = null;
    private $record_array = null;
    private $array_length = 0;
    private $royalmail_file;
    private $_files; //['form.pdf']  ["1,2,4, 5-19"]
    private $fpdi;
    private $manifest_number = '';
    private $warehouseId= '';

    public function __construct() {

    }

    public function validation(Consignment $consignment, Services $service, Country $country) {

        $handling = $service->getCode();
        if(in_array($handling, array('STRYMRM1L', 'STRYMRM2L', 'STRYMRM1P', 'STRYMRM2P', 'STRYMRM1E', 'STRYMRM2E', 'STRYM0TP2','STRYMTP2S', 'STRM1CLSM','STRM2NDMD'))) 
        {
            $returnOutput = array();
            $RoyalMailSortCode = $this->getRoyalMailSortCode($consignment->getPostcode(), 4);
            if ($RoyalMailSortCode != '') {
                $consignment->setOtherRoutingCode($RoyalMailSortCode);
            } else {
                $returnOutput[] = "Postcode is invalid, Please provide correct postcode";
            }
            return $returnOutput;
        }
    }

    public function getRoyalMailSortCode($postcode, $level, $debug = false) {
        $sortcode = '';

        $postCode = strtoupper(trim(str_replace(' ', '', $postcode)));
        $postcodeLength = strlen($postCode);
        $maxLevel = $postcodeLength - 3;
        if ($level > $maxLevel)
            $level = $maxLevel;
        $postalCodePart = substr($postCode, 0, $level);
        $sql = "select sortcode from royalmail_sortcode where postcode_area = '$postalCodePart'";

        $rs = DbAccess3::runQuery($sql);
        if (mysqli_num_rows($rs) > 0) {
            $row = mysqli_fetch_assoc($rs);
            $sortcode = $row['sortcode'];
            return $sortcode;
        } else {
            if ($level > 0) {
                $level = $level - 1;
                return $this->getRoyalMailSortCode($postcode, $level, $debug);
            } else {
                return '';
            }
        }
    }

    public function remoteareas($consignment, $carrierObject, $sender) {

    }

    public function label($consignment, $labelType = 'pdf', $size = '100x150') {

        $output = array();
        $this->serviceValues = new Services($consignment->getServiceId());
        $this->user = SessionManager::getUser();
        $this->warehouse = new Warehouse($this->user->getWarehouseid());
        $this->country = new Country($consignment->getCountryId());


        $serviceAgentConstantFilter = new ServiceConstantValueFilter();
        $serviceAgentConstantFilter->addFilter("service_id = '" . $consignment->getServiceId() . "' AND agent_id = '" . $consignment->getAgentId() . "' ");
        $serviceAgentConstant = $serviceAgentConstantFilter->getConstantList("c.constant 'constant_name', scv.constant_value 'constant_value'");
        if (count($serviceAgentConstant) > 0) {
            foreach ($serviceAgentConstant as $serviceAgentConstantData) {
                $this->constants[$serviceAgentConstantData->getConstantName()] = $serviceAgentConstantData->getConstantValue();
            }
        }


        if (trim(@$this->constants['ROYALMAIL_ACCOUNT_NUMBER']) == '') {
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
        $pdf = new PdfBase(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $this->pdf = $pdf;
        // Generate label for each parecel
        foreach ($parcel_list as $parcel) {
            if ($parcel->getTrackingNumber() == '') {

                $resultArray = LicencePlate::getLicencePlateNumber($licence_plate_id);
                if (trim($resultArray['STATUS']) == 'ERROR')
                    return $resultArray;
                else {
                    $checkdigit = "";
                    /*if (trim(@$this->serviceValues->getCode()) == "STRYMFP24") {
                        $checkdigit = "";
                        $trackingNumber = sprintf('%08d', $resultArray["RANGE"]);
                         $prefix = $resultArray["PREFIX"] ;
                    } else*/
                    if (trim(@$this->serviceValues->getCode()) == "STRYMRM1P" || trim(@$this->serviceValues->getCode()) == "STRYMRM1L"
                        || trim(@$this->serviceValues->getCode()) == "STRYMRM1E" || trim(@$this->serviceValues->getCode()) == "STRYMRM2P"
                        || trim(@$this->serviceValues->getCode()) == "STRYMRM2L" || trim(@$this->serviceValues->getCode()) == "STRYMINEC"
                        || trim(@$this->serviceValues->getCode()) == "STRYMRM2E" ||  trim(@$this->serviceValues->getCode()) == "STRYMFP24"
                        ||  trim(@$this->serviceValues->getCode()) == "STRM1CLSM" || trim(@$this->serviceValues->getCode()) == "STRM2NDMD"   ) {
                        $trackingNumber = sprintf('%08d', $resultArray["RANGE"]);
                        if($consignment->getWarehouseId() == "9" && trim(@$this->serviceValues->getCode()) != "STRYMFP24"){
                            $prefix = "0B0451399000";
                        }
                        else
                        {
                            $prefix = $resultArray["PREFIX"] ;
                        }
                        $trackingNo =  $prefix. $trackingNumber;
                        $total = $this->luhn_process($trackingNo);
                        $checkbit = $this->luhn_checkdigit($total);
                        $checkbit = $checkbit - $total;
                        $checkdigit = strtoupper(dechex($checkbit));
//                        $post_code = str_replace(" ", "", $consignment->getPostcode());
//                        $post_code = str_pad($post_code, 8, "0", STR_PAD_RIGHT);
//                        $trackingNumber = $resultArray["RANGE"] . $post_code;
                    } else {
                        $checkdigit = LicencePlate::mod11($resultArray["RANGE"]);
                        $trackingNumber = $resultArray["RANGE"];
                        $prefix = $resultArray["PREFIX"] ;
                    }
                    $licence_plate = $prefix . $trackingNumber . $checkdigit . $resultArray["SUFIX"];
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

            $functionName = $this->serviceValues->getCode();
            $retrunOBNumber = $this->$functionName($consignment, $parcel_idx, $licence_plate);
            
            $new_page_flag = true;
            ++$parcel_idx;
        }

        if (trim(@$this->serviceValues->getCode()) == "STRYM0DEJ" || trim(@$this->serviceValues->getCode()) == "STRYMINSN" || trim(@$this->serviceValues->getCode()) == "STRYM24RN" || trim(@$this->serviceValues->getCode()) == "STRYM48RN" || trim(@$this->serviceValues->getCode()) == "STRYM0TP2" || trim(@$this->serviceValues->getCode()) == "STRYMTP2S" || trim(@$this->serviceValues->getCode()) == "F2PRM24100" || trim(@$this->serviceValues->getCode()) == "STRYMFP24") {
            $description = $consignment->getDescription();
            if (count(array_intersect(explode(' ', $description), array('battery', 'batteries', 'lithuim', 'litthium', 'lithium battery', 'lithium batteries', 'li battery', 'batery', 'bateries')))) {
                $this->addCautionLabel("Lithium Ion Battery.png", $page_size);
            } else if (count(array_intersect(explode(' ', $description), array('medicines', 'medicine', 'aftershave', 'nail varnish', 'varnish', 'toiletry', 'medicinal', 'aerosols', 'aerosol', 'toilet', 'perfume', 'perfumes', 'alcohal', 'fragrance', 'fragrances', 'alcohol', 'scent')))) {
                $this->addCautionLabel("limited-quantity.jpg", $page_size);
            }
        }
        


        $comma_separated_tracking_numbers = implode($licence_plate_array, ",");
        $this->pdf->IncludeJS("print();");
        $this->pdf->Output("../_assets/pdf/" . date('Y_m_d') . '/' . $consignment->getId() . ".pdf", "F");
        $output['STATUS'] = 'SUCCESS';
        //SETTING_MAIN_URL . "_assets/pdf/" .
        $output['LABEL'] = date('Y_m_d') . '/' . $consignment->getId() . ".pdf";
        $output['TRACKING_NUMBER'] = $licence_plate_array;
        return $output;
    }

    // Royal Mail Tracked 24
    private function STRYM24RN($consignment, $parcel_idx, $licence_plate) {
        $page_size = array(151, 101);
        $this->pdf->AddPage("P", $page_size);
        if (trim(@$this->serviceValues->getCode()) == "STRYM24RN") {
            $image = realpath("../images/royal24LogoReturn.png");
        } else if (trim(@$this->serviceValues->getCode()) == "STRYM48RN") {
            $image = realpath("../images/royalLogoReturn.png");
        }
        $this->pdf->image($image, 2, 4, 90);

        $this->pdf->rect(1, 1, 99, 149);
        $this->pdf->rect(2, 2, 97, 147);
        $this->pdf->line(2, 32, 99, 32);
        $this->pdf->line(2, 75, 99, 75);
        $this->pdf->line(2, 109, 99, 109);
        $this->pdf->line(2, 117, 99, 117);

        $this->pdf->setFont("helvetica", "", 8);
        $this->pdf->Text(6, 122, 'Customer Reference');
        $this->pdf->Text(6, 126, $consignment->getHawb());
        $this->pdf->Text(6, 132, 'Department Reference');
        $this->pdf->Text(6, 136, $consignment->getHawb());
        $this->pdf->setFont("helvetica", "L", 7);
        $pieces = str_pad($parcel_idx + 1, 3, "0", STR_PAD_LEFT);
        $this->pdf->write1DBarcode($licence_plate, 'C128', 50, 48, '45', 17, .4);

        $this->barcode2D($consignment, $licence_plate, 'H');
        $prefix = substr($licence_plate, 0, 2);
        $firstdigits = substr($licence_plate, 2, 4);
        $seconddigits = substr($licence_plate, 6, 4);
        $lastdigits = substr($licence_plate, 10, 4); // need to change 4 to 3 when it will be live . beacuse live service has one less number

        $display_barcode = $prefix . " " . $firstdigits . " " . $seconddigits . " " . $lastdigits;
        $this->pdf->setFont("helvetica", "", 8);
        $this->pdf->Text(61, 66.5, $display_barcode);

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

        $this->pdf->setFont("helvetica", "L", 9);
        $this->pdf->Text(6, 76, $this->constants["ROYALMAIL_SHIPPER_CONTACT"]); //$company);
        $this->pdf->Text(6, 80, $this->constants["ROYALMAIL_SHIPPER_ADDRESSLINE1"]);
        $this->pdf->Text(6, 84, $this->constants["ROYALMAIL_SHIPPER_ADDRESSLINE2"]);
        $this->pdf->Text(6, 88, $this->constants["ROYALMAIL_SHIPPER_ADDRESSLINE3"]);
        $this->pdf->Text(6, 92, $this->constants["ROYALMAIL_SHIPPER_CITY"]);
        $this->pdf->Text(6, 96, $this->constants["ROYALMAIL_SHIPPER_POSTCODE"]);
        $shipper_country = $this->constants['ROYALMAIL_SHIPPER_COUNTRY'];
        if ((int) $shipper_country > 0) {
            $shipperCountry = new Country($shipper_country);
            $sCountry = $shipperCountry->getName();
        } else {
            $sCountry = $shipper_country;
        }
        $this->pdf->Text(6, 100, $sCountry);
        $this->pdf->setFont("helvetica", "B", 9);
        $this->pdf->Text(6, 111, "Post Office");
        $this->pdf->setFont("helvetica", "", 9);
        $this->pdf->Text(23, 111, " - scan the above right barcode");
        $this->pdf->Text(55, 104, $consignment->getReference());
        $this->pdf->StartTransform();
        $this->pdf->Rotate(90, 108, 33);

        $this->pdf->setFont("helvetica", "", 5.5);
        $this->pdf->Text(32, 14, "Sender's Address:");

        if ($company != '')
            $this->pdf->Text(50, 14, $company);


        $this->pdf->Text(32, 17, $address1 . $address2);
        $this->pdf->Text(32, 20, $address3 . $consignment->getPostcode());

        $this->pdf->StopTransform();
        return;
    }

    // Royal Mail 48 Return Label
    private function STRYM48RN($consignment, $parcel_idx, $licence_plate) {
        $this->STRYM24RN($consignment, $parcel_idx, $licence_plate);
    }

    // Frieght to post Royal Mail 24
    private function STRYMFP24($consignment, $parcel_idx, $licence_plate) {

        $page_size = array(151, 100);
        $this->pdf->AddPage("P", $page_size);
        $image = realpath("../images/royaluntracked24.png");
        $this->pdf->image($image, 3, 1, 90);


        $this->pdf->rect(1, 1, 99, 149);
        $this->pdf->rect(2, 2, 97, 147);
        $this->pdf->line(2, 27, 99, 27);
        $this->pdf->line(2, 68, 99, 68);
        $this->pdf->line(2, 109, 99, 109);
        $this->pdf->line(2, 117, 99, 117);

        $this->pdf->setFont("helvetica", "", 8);
        $this->pdf->Text(9, 117, 'Customer Reference');
        $this->pdf->Text(9, 120, $consignment->getHawb());
        $this->pdf->setFont("helvetica", "L", 7);
        $pieces = str_pad($parcel_idx + 1, 3, "0", STR_PAD_LEFT);

        $awb = $this->barcode2D($consignment, $licence_plate, F, 9, 40, 8, 30);

        $this->pdf->write1DBarcode(str_replace(" ", "", $awb), 'C128', 15, 128, '70', 10, .5);

        $this->pdf->setFont("helvetica", "", 8);
        $this->pdf->Text(30, 139, str_replace(" ", "", $awb));

        $fontname = $this->pdf->addTTFfont('../assets/fonts/simhei.ttf', 'TrueTypeUnicode', '', 32);
        $this->pdf->SetFont($fontname, '', 8);
        $this->pdf->MultiCell(90, 8, "Notes: " . $consignment->getNotes(), 0, 'L', 0, 1, 9, 142);

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



        $this->pdf->setFont("helvetica", "L", 9);
        $this->pdf->Text(9, 70, $company);
        $this->pdf->Text(9, 74, $consignment->getContact());
        $this->pdf->Text(9, 78, @$address1);
        $this->pdf->Text(9, 82, @$address2);
        $this->pdf->Text(9, 86, @$address3);
        $this->pdf->Text(9, 90, $consignment->getCity());
        $this->pdf->Text(9, 94, $consignment->getPostcode());
        $this->pdf->Text(9, 98, "UNITED KINGDOM");

        $this->pdf->Text(9, 111, "Special Instruction:");

        $this->pdf->Text(55, 105, $consignment->getReference());


        $this->pdf->StartTransform();
        $this->pdf->Rotate(90, 108, 33);

        $this->pdf->setFont("helvetica", "", 7);
        $this->pdf->Text(32, 11, "Return to:");



        $this->pdf->Text(47, 11, $this->constants["ROYALMAIL_SHIPPER_CONTACT"]);
        $this->pdf->Text(32, 15, $this->constants["ROYALMAIL_SHIPPER_ADDRESSLINE1"] . ", ");
        $this->pdf->Text(32, 18, $this->constants["ROYALMAIL_SHIPPER_ADDRESSLINE2"] . ", " . $this->constants["ROYALMAIL_SHIPPER_POSTCODE"]);
        $this->pdf->StopTransform();
        return;
    }

    // Frieght to post Royal Mail Standard 24 10 X 10 size label
    private function F2PRM24100($consignment, $parcel_idx, $licence_plate) {

        $page_size = array(100, 100);
        $this->pdf->AddPage("P", $page_size);
        $handling = $this->serviceValues->getCode();
        if (in_array($handling, array('STRYMRM2L', 'STRYMRM2P', 'STRYMRM2E')))     {
            $image = realpath("../images/royaluntracked48.png");
            $class = "G";
        }
        else{
            $image = realpath("../images/royaluntracked24.png");
            $class = "F";
        }
        $this->pdf->image($image, 2, 1, 50, 12);
        $rmCruciForm = "../images/royal-mail.png";
        $this->pdf->image($rmCruciForm, 80, 1, 10, 12);

        $this->pdf->rect(1, 1, 98, 98);
        $this->pdf->line(1, 13, 99, 13);
        $this->pdf->line(1, 20, 99, 20);
        $this->pdf->line(1, 50, 99, 50);
        $this->pdf->line(1, 77, 99, 77);
        $this->pdf->line(1, 84, 99, 84);
        $this->pdf->line(1, 117, 99, 117);

        $this->pdf->setFont("helvetica", "B", 16);
        $this->pdf->text(9, 13, $consignment->getOtherRoutingCode());
        
        $obNumber = $this->barcode2D($consignment, $licence_plate, $class, 4, 26, 4, 21);
        $style = array(
            'position' => '',
            'align' => 'C', 
           'stretch' => false,
            'fitwidth' => true,
            'cellfitalign' => '',
            'border' => false,
            'hpadding' => 'auto',
            'vpadding' => 'auto',
            'fgcolor' => array(0, 0, 0),
            'bgcolor' => false, //array(255,255,255),
            'text' => false
        );
        
        $this->pdf->write1DBarcode($licence_plate, 'C128', 15, 85, '70', 10, 0.5, $style);

        $this->pdf->setFont("helvetica", "", 8);
        $this->pdf->Text(10, 93, $licence_plate, false, false, true, 0, 0, 'C');

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

        $this->pdf->setFont("helvetica", "B", 8);
        $this->pdf->Text(4, 49, $company);
        $this->pdf->Text(4, 52, $consignment->getContact());
        $this->pdf->Text(4, 55, @$address1);
        $this->pdf->Text(4, 57, @$address2);
        $this->pdf->Text(4, 60, @$address3);
        $this->pdf->Text(4, 63, $consignment->getCity());
        $this->pdf->Text(4, 66, $consignment->getPostcode());
        $this->pdf->Text(4, 69, "UNITED KINGDOM");
        
        $this->pdf->setFont("helvetica", "", 6);
        $this->pdf->Text(4, 73, 'Customer Reference: ' . $consignment->getHawb());

        $this->pdf->Text(4, 78, "Special Instruction:");

        if (trim($consignment->getNotes()) == '')
            $this->pdf->Text(5, 80, $consignment->getReference());
        else
            $this->pdf->Text(5, 80, $consignment->getNotes());


        $this->pdf->StartTransform();
        $this->pdf->Rotate(90, 92, 17);

        $this->pdf->setFont("helvetica", "", 5);
        $this->pdf->Text(32, 10, "Return Address");

        $shipper_country = $this->constants['ROYALMAIL_SHIPPER_COUNTRY'];
        if ((int) $shipper_country > 0) {
            $shipperCountry = new Country($shipper_country);
            $sCountry = $shipperCountry->getIso();
        } else {
            $sCountry = $shipper_country;
        }

        $this->pdf->Text(32, 13, $this->constants["ROYALMAIL_SHIPPER_CONTACT"]);
        $this->pdf->Text(32, 16, $this->constants["ROYALMAIL_SHIPPER_ADDRESSLINE1"] . ", ");
        $this->pdf->Text(32, 19, $sCountry . ", " . $this->constants["ROYALMAIL_SHIPPER_POSTCODE"]);

        $this->pdf->StopTransform();
        return $obNumber;
    }

    // Royal Mail TP2 Label
    private function STRYM0TP2(Consignment $consignment, $parcel_idx, $licence_plate) {

        $page_size = array(151, 100);
        $this->pdf->AddPage("P", $page_size);
        if ($this->serviceValues->getCode() == "STRYMTP2S") {
            $image = realpath("../images/royalLogo.png");
        } else if ($this->serviceValues->getCode() == "STRYM0TP2") {
            $image = realpath("../images/royalLogoNs.png");
        } else if ($this->serviceValues->getCode() == "TP1") {
            $image = realpath("../images/royal24LogoNs.png");
        } else if ($this->serviceValues->getCode() == "TP1S") {
            $image = realpath("../images/royal24Logo.png");
        }

        if (file_exists($image)) {
            $this->pdf->image($image, 3, 1, 90);
        }
        $this->pdf->rect(1, 1, 99, 149);
        $this->pdf->rect(2, 2, 97, 147);
        $this->pdf->line(2, 27, 99, 27);
        $this->pdf->setFont("helvetica", "B", 18);
        $this->pdf->text(9, 29, $consignment->getOtherRoutingCode());
        $this->pdf->line(2, 37, 99, 37);

        $this->pdf->line(2, 75, 99, 75);
        $this->pdf->line(2, 109, 99, 109);
        $this->pdf->line(2, 117, 99, 117);


        $this->pdf->setFont("helvetica", "", 8);
        $this->pdf->Text(9, 117, 'Customer Reference');
        $this->pdf->Text(9, 120, $consignment->getHawb());
        $this->pdf->Text(9, 125, 'Department Reference');
        $this->pdf->Text(9, 128, '');



        $this->pdf->setFont("helvetica", "B", 21);
        $this->pdf->Text(9, 135, $consignment->getOtherRoutingCode());

        $this->pdf->setFont("helvetica", "L", 7);
        $pieces = str_pad($parcel_idx + 1, 3, "0", STR_PAD_LEFT);

        $this->pdf->write1DBarcode($licence_plate, 'C128', 50, 48, '45', 20, .4);
        $this->barcode2D($consignment, $licence_plate, "G");
        $prefix = substr($licence_plate, 0, 2);
        $firstdigits = substr($licence_plate, 2, 4);
        $seconddigits = substr($licence_plate, 6, 4);
        $lastdigits = substr($licence_plate, 10, 3);

        $display_barcode = $prefix . " " . $firstdigits . " " . $seconddigits . " " . $lastdigits;
        $this->pdf->setFont("helvetica", "", 8);
        $this->pdf->Text(61, 71, $display_barcode);


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



        $this->pdf->setFont("helvetica", "L", 9);
        $this->pdf->Text(9, 77, $company);
        $this->pdf->Text(9, 80, $consignment->getContact());
        $this->pdf->Text(9, 83, @$address1);
        $this->pdf->Text(9, 86, @$address2);
        $this->pdf->Text(9, 89, @$address3);
        $this->pdf->Text(9, 92, $consignment->getCity());
        $this->pdf->Text(9, 95, $consignment->getPostcode());
        $this->pdf->Text(9, 98, "UNITED KINGDOM");

        if (trim($consignment->getNotes()) != '') {
            $fontname = $this->pdf->addTTFfont('../assets/fonts/simhei.ttf', 'TrueTypeUnicode', '', 32);
            $this->pdf->SetFont($fontname, '', 8);
            $this->pdf->Text(9, 111, "Notes : " . $consignment->getNotes());
        }


        $this->pdf->setFont("helvetica", "L", 9);
        $this->pdf->Text(55, 105, $consignment->getReference());

        $this->pdf->StartTransform();
        $this->pdf->Rotate(90, 108, 33);


        $this->pdf->setFont("helvetica", "", 5);
        $this->pdf->Text(32, 11, "RETURN TO:");


        $this->pdf->Text(45, 11, $this->constants["ROYALMAIL_SHIPPER_CONTACT"]);
        $this->pdf->Text(32, 14, $this->constants["ROYALMAIL_SHIPPER_ADDRESSLINE1"] . ", ");
        $this->pdf->Text(32, 17, $this->constants["ROYALMAIL_SHIPPER_ADDRESSLINE2"] . ", " . $this->constants["ROYALMAIL_SHIPPER_POSTCODE"]);

        $this->pdf->StopTransform();
        $postcodenum = str_replace(" ", "", $consignment->getPostcode());
        $this->pdf->write1DBarcode("*" . trim($postcodenum) . "*", 'C128', 50, 120, '40', 20, .5);

        $this->pdf->setFont("helvetica", "B", 12);
        $this->pdf->Text(60, 141, "*" . $consignment->getPostcode() . "*");

        return;
    }

    // Royal Mail TP2S Labels
    private function STRYMTP2S(Consignment $consignment, $parcel_idx, $licence_plate) {
        $this->STRYM0TP2($consignment, $parcel_idx, $licence_plate);
    }

    // Royal Mail UK Untracked 1st & 2nd Class Letter and Parcel
    private function STRYMRM1P(Consignment $consignment, $parcel_idx, $licence_plate) {
        return $this->F2PRM24100($consignment, $parcel_idx, $licence_plate);
//        return;
//        $page_size = array(100, 100);
//        $this->pdf->AddPage("P", $page_size);
//        // BORDER LINES
//        $this->pdf->line(2, 2, 98, 2);
//        $this->pdf->line(2, 98, 98, 98);
//        $this->pdf->line(2, 2, 2, 98);
//        $this->pdf->line(98, 2, 98, 98);
//        //TOP LINE UNDER IMAGE
//        $this->pdf->line(2, 20, 98, 20);
//        //TOP TO MIDDLE LINE BETWEEN IMAGES
//        $this->pdf->line(50, 2, 50, 20);
//        // BOTTOM LINE UNDER BARCODE
//        $this->pdf->line(2, 35, 98, 35);
//        // BOTTOM HORIZONTALE LINE
//        $this->pdf->line(2, 85, 98, 85);
//        //RIGHT LINE NEXT TO RETURN ADDRESS
//        $this->pdf->line(10, 35, 10, 85);
//        // RIGHT LINE NEXT TO ADDRESS
//        $this->pdf->line(85, 35, 85, 98);
//        //LINE ABOVE PARCEL OR LATER
//        $this->pdf->line(85, 75, 98, 75);
//
//        $style = array(
//            'position' => '',
//            'align' => 'C',
//            'stretch' => true,
//            'fitwidth' => true,
//            'cellfitalign' => '',
//            'border' => false,
//            'hpadding' => 'auto',
//            'vpadding' => 'auto',
//            'fgcolor' => array(0, 0, 0),
//            'bgcolor' => false, //array(255,255,255),
//            'text' => true,
//            'font' => 'helvetica',
//            'fontsize' => 8,
//            'stretchtext' => 4
//        );
//
//        $this->pdf->write1DBarcode($licence_plate, 'C128', 15, 21, '', 14, 0.28, $style, 'N');
//
//
//        $this->pdf->setFont("helvetica", "B", 6);
//
//
//        if (trim($this->user->getLogo()) != '')
//            $image1 = realpath("../images/userlogo/" . $this->user->getLogo());
//        else
//            $image1 = realpath("../images/logo.jpg");
//
//
//        if (file_exists($image1)) {
//            $fitbox = 'C';
//            $fitbox[1] = 'M';
//            $this->pdf->image($image1, 10, 3, 30, 15, '', '', '', false, 700, '', false, false, 0, $fitbox, false, false);
//        }
//        $this->pdf->Text(10, 17, "www.oneworldexpress.com");
//
//
//        if (in_array($this->serviceValues->getCode(), array('STRYMRM1P', 'STRYMRM1L'))) {
//            $textonlabel = "1st Class";
//            $image = realpath("../images/1st Class.jpg");
//            $serviceLetterOnLabel = 'P';
//        } else if (in_array($this->serviceValues->getCode(), array('STRYMRM2P', 'STRYMRM2L'))) {
//            $textonlabel = "2nd Class";
//            $image = realpath("../images/2nd Class.jpg");
//            $serviceLetterOnLabel = 'L';
//        }
//
//        if (file_exists($image)) {
//            $this->pdf->image($image, 52, 3, 45, 15);
//        }
//
//        $this->pdf->setFont("helvetica", "B", 5);
//        $this->pdf->Text(81.5, 15, $this->constants['ROYALMAIL_PPI_TEXT']);
//        $this->pdf->setFont("helvetica", "B", 18);
//        $this->pdf->Text(88, 76, $serviceLetterOnLabel);
//
//        $this->pdf->StartTransform();
//        $this->pdf->Rotate(90, 70, 70);
//
//
//
//        $this->pdf->setFont("helvetica", "B", 8);
//
//        $this->pdf->Text(65, 88, $textonlabel . " Untracked");
//        // Stop Transformation
//        $this->pdf->setFont("helvetica", "B", 7.5);
//
//        $shipper_country = $this->constants['ROYALMAIL_SHIPPER_COUNTRY'];
//        if ((int) $shipper_country > 0) {
//            $shipperCountry = new Country($shipper_country);
//            $sCountry = $shipperCountry->getIso();
//        } else {
//            $sCountry = $shipper_country;
//        }
//        $companyaddress = $this->constants["ROYALMAIL_SHIPPER_CONTACT"];
//        $useraddress = $this->constants["ROYALMAIL_SHIPPER_ADDRESSLINE1"] . "," . $this->constants["ROYALMAIL_SHIPPER_ADDRESSLINE2"] . "," . $this->constants["ROYALMAIL_SHIPPER_ADDRESSLINE3"] . "," . $this->constants["ROYALMAIL_SHIPPER_POSTCODE"] . "," . $sCountry;
//
//        $this->pdf->setFont("helvetica", "", 6);
//        $this->pdf->Text(55, 3, "If Undelivered Return To: (" . $companyaddress . ")");
//        $this->pdf->setFont("helvetica", "", 5);
//        $this->pdf->Text(55, 6, $useraddress);
//        $this->pdf->StopTransform();
//
//        $this->pdf->setFont("helvetica", '', 10);
//        $address = "";
//        $company = "";
//        if ($consignment->getCompany() != "")
//            $company = $consignment->getCompany();
//        if ($consignment->getAddressLine1() != "")
//            $address1 = $consignment->getAddressLine1();
//        if ($consignment->getAddressLine2() != "")
//            $address2 = $consignment->getAddressLine2();
//        if ($consignment->getAddressLine3() != "")
//            $address3 = $consignment->getAddressLine3();
//
//
//        $this->pdf->Text(12, 40, ucwords($company));
//        $this->pdf->Text(12, 44, ucwords($consignment->getContact()));
//        $this->pdf->Text(12, 48, ucwords($address1));
//        $this->pdf->Text(12, 52, ucwords($address2));
//        $this->pdf->Text(12, 56, ucwords($address3));
//        $this->pdf->Text(12, 60, ucwords($consignment->getCity()));
//        $this->pdf->Text(12, 64, ucwords("United Kingdom"));
//
//        $this->pdf->setFont("helvetica", "B", 12);
//        $this->pdf->Text(12, 70, $consignment->getPostcode());
//
//        $fontname = $this->pdf->addTTFfont('../assets/fonts/simhei.ttf', 'TrueTypeUnicode', '', 32);
//        $this->pdf->SetFont($fontname, '', 8);
//        $this->pdf->Text(12, 78, "Notes: " . $consignment->getNotes());
//
//
//        $style1 = array(
//            'position' => '',
//            'align' => 'C',
//            'stretch' => true,
//            'fitwidth' => true,
//            'cellfitalign' => '',
//            'border' => false,
//            'hpadding' => 'auto',
//            'vpadding' => 'auto',
//            'fgcolor' => array(0, 0, 0),
//            'bgcolor' => false, //array(255,255,255),
//            'text' => true,
//            'font' => 'helvetica',
//            'fontsize' => 8,
//            'stretchtext' => 1
//        );
//
//
//        $this->pdf->write1DBarcode($consignment->getHawb(), 'C128', 15, 85, '', 15, 0.5, $style1, 'Y');
//
//
//
//        $zone = $this->getZone($consignment->getPostcode());
//        if (isset($zone)) {
//
//            if (strlen($zone) == 1) {
//                $this->pdf->setFont("helvetica", "B", 33);
//            } else {
//                $this->pdf->setFont("helvetica", "B", 33);
//            }
//            $this->pdf->Text(87, 84, $zone);
//        }
    }

    //Royal Mail RM2 Label
    private function STRYMRM2P(Consignment $consignment, $parcel_idx, $licence_plate) {
        return $this->F2PRM24100($consignment, $parcel_idx, $licence_plate);
        //  $this->STRYMRM1P($consignment, $parcel_idx, $licence_plate);
    }

    //Royal Mail RM1L Label
    private function STRYMRM1L(Consignment $consignment, $parcel_idx, $licence_plate) {
        return $this->F2PRM24100($consignment, $parcel_idx, $licence_plate);
        // $this->STRYMRM1P($consignment, $parcel_idx, $licence_plate);
    }

    //Royal Mail RM1 2kg  Label
    private function STRYMRM1E(Consignment $consignment, $parcel_idx, $licence_plate) {
        return $this->F2PRM24100($consignment, $parcel_idx, $licence_plate);
        // $this->STRYMRM1P($consignment, $parcel_idx, $licence_plate);
    }

    //Royal Mail RM1L 20 kg Label
    private function STRM1CLSM(Consignment $consignment, $parcel_idx, $licence_plate) {
        return $this->F2PRM24100($consignment, $parcel_idx, $licence_plate);
        // $this->STRYMRM1P($consignment, $parcel_idx, $licence_plate);
    }
    
    //Royal Mail RM2L 20 kg Label
    private function STRM2NDMD(Consignment $consignment, $parcel_idx, $licence_plate) {
        return $this->F2PRM24100($consignment, $parcel_idx, $licence_plate);
        // $this->STRYMRM1P($consignment, $parcel_idx, $licence_plate);
    }

    //Royal Mail RM2L Label
    private function STRYMRM2L(Consignment $consignment, $parcel_idx, $licence_plate) {
        return $this->F2PRM24100($consignment, $parcel_idx, $licence_plate);
        // $this->STRYMRM1P($consignment, $parcel_idx, $licence_plate);
    }

    //Royal Mail RM2 Packet Label STRYMRM2E
    private function STRYMRM2E(Consignment $consignment, $parcel_idx, $licence_plate) {
        return $this->F2PRM24100($consignment, $parcel_idx, $licence_plate);
    }

    // Royal Mail ECONOMY
    public function STRYMEBOX(Consignment $consignment, $parcel_idx, $licence_plate) {
        $page_size = array(100, 100);
        $this->pdf->AddPage("P", $page_size);

        $image = realpath(SETTING_DIR_REMOTE . "images/internationalnonpriority.png");
        $this->pdf->image($image, 3, 1, 90, 18);
        $this->pdf->rect(1, 1, 98, 98);
        $this->pdf->line(1, 20, 99, 20);
        $this->pdf->line(1, 52, 99, 52);
        $this->pdf->line(1, 78, 99, 78);
        $this->pdf->line(1, 85, 99, 85);
        $this->pdf->setFont("helvetica", "", 7);
        $this->pdf->Text(35, 95, $consignment->getHawb());
        $this->pdf->setFont("helvetica", "L", 7);
        $pieces = str_pad($parcel_idx + 1, 3, "0", STR_PAD_LEFT);
        $awb = $this->barcode2D($consignment, $licence_plate, "7",4, 27, 4,22 );
        $style = array(
            'position' => '',
            'align' => 'C',
            'stretch' => false,
            'fitwidth' => true,
            'cellfitalign' => '',
            'border' => true,
            'hpadding' => 'auto',
            'vpadding' => 'auto',
            'fgcolor' => array(0,0,0),
            'bgcolor' => false, //array(255,255,255),
            'text' => false,
            'font' => 'helvetica',
            'fontsize' => 8
        );
        $this->pdf->write1DBarcode(str_replace("-", "", $licence_plate), 'C128', 50, 27, '45', 10, .4);

        $prefix = substr($licence_plate, 0, 2);
        $firstdigits = substr($licence_plate, 2, 4);
        $seconddigits = substr($licence_plate, 6, 4);
        $lastdigits = substr($licence_plate, 10, 3);
        $display_barcode = $prefix . " " . $firstdigits . " " . $seconddigits . " " . $lastdigits;
        $this->pdf->setFont("helvetica", "", 8);
        $this->pdf->Text(60, 41, $display_barcode);

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

        $this->pdf->setFont("Freesans", "B", 8);
        $this->pdf->Text(4, 53, $company);
        $this->pdf->Text(4, 56, $consignment->getContact());
        $this->pdf->Text(4, 59, @$address1);
        $this->pdf->Text(4, 62, @$address2);
        $this->pdf->Text(4, 65, @$address3);
        $this->pdf->Text(4, 68, $consignment->getCity());
        $this->pdf->Text(4, 71, $consignment->getPostcode());
        $this->pdf->Text(4, 74, $this->country->getName());
        $this->pdf->setFont("Freesans", "", 8);
        $this->pdf->Text(4, 79, "Special Instruction: ". $consignment->getNotes());

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
            'fontsize' => 8
        );

        $this->pdf->write1DBarcode($consignment->getHawb(), 'C128', 15, 86, '70', 7, .5);
        
        
        $this->pdf->StartTransform();
        $this->pdf->Rotate(90, 89, 13);

        $this->pdf->setFont("helvetica", "", 6);
        $this->pdf->Text(25, 5, "Return Address");
        $shipper_country = $this->constants['ROYALMAIL_SHIPPER_COUNTRY'];
        if ((int) $shipper_country > 0) {
            $shipperCountry = new Country($shipper_country);
            $sCountry = $shipperCountry->getIso();
        } else {
            $sCountry = $shipper_country;
        }

        $this->pdf->Text(25, 8, $this->constants["ROYALMAIL_SHIPPER_CONTACT"]);
        $this->pdf->Text(25, 11, $this->constants["ROYALMAIL_SHIPPER_ADDRESSLINE1"]);
        $this->pdf->Text(25, 14, $this->constants["ROYALMAIL_SHIPPER_ADDRESSLINE2"]);
        $this->pdf->Text(25, 17, $this->constants["ROYALMAIL_SHIPPER_POSTCODE"]);
        $this->pdf->Text(25, 20,  $sCountry);

        $this->pdf->StopTransform();
        return $awb;
    }

    //Royal Mail Priority Label
    private function STRYMINPR(Consignment $consignment, $parcel_idx, $licence_plate) {
        $page_size = array(100, 100);
        $this->pdf->AddPage("P", $page_size);

        $image = realpath(SETTING_DIR_REMOTE . "images/internationalpriority.png");
        $this->pdf->image($image, 3, 1, 90, 16);
        $this->pdf->rect(1, 1, 98, 98);
        $this->pdf->line(1, 16, 99, 16);
        $this->pdf->line(1, 26, 99, 26);
        $this->pdf->line(1, 56, 99, 56);
        $this->pdf->line(1, 85, 99, 85);
        $this->pdf->setFont("helvetica", "", 7);
        $this->pdf->Text(35, 95, $consignment->getHawb());
        $this->pdf->setFont("helvetica", "B", 10);
        $this->pdf->Text(70, 17, 'AIR MAIL');
        $this->pdf->setFont("helvetica", "", 10);
        $this->pdf->Text(70, 21, "PAR AVION");
        $this->pdf->setFont("helvetica", "L", 7);
        $pieces = str_pad($parcel_idx + 1, 3, "0", STR_PAD_LEFT);
        $awb = $this->barcode2D($consignment, $licence_plate, "6",4, 32, 4,28 );
        $style = array(
            'position' => '',
            'align' => 'C',
            'stretch' => false,
            'fitwidth' => true,
            'cellfitalign' => '',
            'border' => true,
            'hpadding' => 'auto',
            'vpadding' => 'auto',
            'fgcolor' => array(0,0,0),
            'bgcolor' => false, //array(255,255,255),
            'text' => false,
            'font' => 'helvetica',
            'fontsize' => 8
        );
        $this->pdf->write1DBarcode(str_replace("-", "", $licence_plate), 'C128', 50, 30, '45', 10, .4);

        $prefix = substr($licence_plate, 0, 2);
        $firstdigits = substr($licence_plate, 2, 4);
        $seconddigits = substr($licence_plate, 6, 4);
        $lastdigits = substr($licence_plate, 10, 3);
        $display_barcode = $prefix . " " . $firstdigits . " " . $seconddigits . " " . $lastdigits;
        $this->pdf->setFont("helvetica", "", 8);
        $this->pdf->Text(60, 41, $display_barcode);

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

        $this->pdf->setFont("Freesans", "B", 8);
        $this->pdf->Text(4, 58, $company);
        $this->pdf->Text(4, 61, $consignment->getContact());
        $this->pdf->Text(4, 64, @$address1);
        $this->pdf->Text(4, 67, @$address2);
        $this->pdf->Text(4, 70, @$address3);
        $this->pdf->Text(4, 73, $consignment->getCity());
        $this->pdf->Text(4, 76, $consignment->getPostcode());
        $this->pdf->Text(4, 79, $this->country->getName());

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
            'fontsize' => 8
        );

        $this->pdf->write1DBarcode($consignment->getHawb(), 'C128', 15, 86, '70', 7, .5);
        $this->pdf->StartTransform();
        $this->pdf->Rotate(90, 92, 17);

        $this->pdf->setFont("helvetica", "", 6);
        $this->pdf->Text(25, 10, "Return:");
        $shipper_country = $this->constants['ROYALMAIL_SHIPPER_COUNTRY'];
        if ((int) $shipper_country > 0) {
            $shipperCountry = new Country($shipper_country);
            $sCountry = $shipperCountry->getIso();
        } else {
            $sCountry = $shipper_country;
        }

        $this->pdf->Text(33, 10, $this->constants["ROYALMAIL_SHIPPER_CONTACT"]);
        $this->pdf->Text(25, 13, $this->constants["ROYALMAIL_SHIPPER_ADDRESSLINE1"]);
        $this->pdf->Text(25, 16, $this->constants["ROYALMAIL_SHIPPER_ADDRESSLINE2"]);
        $this->pdf->Text(25, 19, $this->constants["ROYALMAIL_SHIPPER_POSTCODE"] ." " . $sCountry);

        $this->pdf->StopTransform();
        return $awb;
    }

    //Royal Mail International Priority Packet 
    private function STRYMPPKT(Consignment $consignment, $parcel_idx, $licence_plate) {
        $this->STRYMINPR($consignment, $parcel_idx, $licence_plate);
    }

    //Royal Mail International Priority Boxable
    private function STRYMPBOX(Consignment $consignment, $parcel_idx, $licence_plate) {
        $this->STRYMINPR($consignment, $parcel_idx, $licence_plate);
    }
    
    // Royal Mail Internation ROYAL MAIL CHORUS BRONZE PRIORITY LL (G)
    private function STCBLPCTE(Consignment $consignment, $parcel_idx, $licence_plate) {
        $this->STRYMINPR($consignment, $parcel_idx, $licence_plate);
    }
    // Royal Mail Internation Priority ROYAL MAIL CHORUS BRONZE PRIORITY PACKET (E)
    private function STCBPPKTE(Consignment $consignment, $parcel_idx, $licence_plate) {
        $this->STRYMINPR($consignment, $parcel_idx, $licence_plate);
    }
    
    // Royal Mail Internation Priority ROYAL MAIL CHORUS SILVER PRIORITY PACKET (E)
    private function STCSPPCTE(Consignment $consignment, $parcel_idx, $licence_plate) {
        $this->STRYMINPR($consignment, $parcel_idx, $licence_plate);
    }
    
    // Royal Mail Internation Priority RROYAL MAIL CHORUS SILVER PRIORITY LL (G)
    private function STCSLPCTE(Consignment $consignment, $parcel_idx, $licence_plate) {
        $this->STRYMINPR($consignment, $parcel_idx, $licence_plate);
    }
    // Royal Mail Internation Priority ROYAL MAIL CHORUS GOLD PRIORITY PACKET (E)
    private function STCGPPCTE(Consignment $consignment, $parcel_idx, $licence_plate) {
        $this->STRYMINPR($consignment, $parcel_idx, $licence_plate);
    }
    // Royal Mail Internation Priority ROYAL MAIL CHORUS GOLD PRIORITY LL (G) 
    private function STCGLPCTE(Consignment $consignment, $parcel_idx, $licence_plate) {
        $this->STRYMINPR($consignment, $parcel_idx, $licence_plate);
    }
    // Royal Mail Internation Priority Bag Label 
    private function STRYMBAGM(Consignment $consignment, $parcel_idx, $licence_plate) {
        $this->STRYMINPR($consignment, $parcel_idx, $licence_plate);
    }
    
    
    

    // Royal Mail International track and sign
    private function STRYM0DEJ(Consignment $consignment, $parcel_idx, $licence_plate) {

        $page_size = array(95, 87);
        $this->pdf->AddPage("P", $page_size);
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
        if ($handling == "STRYM0DEJ") {
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
        } else if ($handling == "STRYMINSN") {
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

        $this->pdf->setFont("helvetica", "B", 5);
        $this->pdf->Text(57, 15, $this->constants['ROYALMAIL_PPI_TEXT']);
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
        $this->pdf->Text(5, 86, $this->country->getName());

        $shipper_country = $this->constants['ROYALMAIL_SHIPPER_COUNTRY'];
        if ((int) $shipper_country > 0) {
            $shipperCountry = new Country($shipper_country);
            $sCountry = $shipperCountry->getIso();
        } else {
            $sCountry = $shipper_country;
        }
        $companyaddress = $this->constants["ROYALMAIL_SHIPPER_CONTACT"];
        $useraddress = $this->constants["ROYALMAIL_SHIPPER_ADDRESSLINE1"] . "," . $this->constants["ROYALMAIL_SHIPPER_ADDRESSLINE2"] . "," . $this->constants["ROYALMAIL_SHIPPER_ADDRESSLINE3"] . "," . $this->constants["ROYALMAIL_SHIPPER_POSTCODE"] . "," . $sCountry;


        $this->pdf->setFont("freesans", "L", 6.5);
        $this->pdf->Text(3, 91, "Return to: " . $companyaddress . ", " . $useraddress);
    }

    //Royal Mail International Sign
    private function STRYMINSN(Consignment $consignment, $parcel_idx, $licence_plate) {
        $this->STRYM0DEJ($consignment, $parcel_idx, $licence_plate);
    }

    // Royal Mail F2P International Tracked
    private function STRYMITF2P(Consignment $consignment, $parcel_idx, $licence_plate){
        $page_size = array(151, 100);
        $this->pdf->AddPage("P", $page_size);
        $image2 = "";
        $scanimage = "";
        $signimage = "";
        if ($this->serviceValues->getCode() == "STRYMITF2P") {
            $image = realpath("../images/InternationalTracked.png");
            $image2 = realpath("../images/Expres.jpg");
            $scanimage = realpath("../images/InternationalScanLogo.PNG");
        } else if ($this->serviceValues->getCode() == "STRMITSF2P") {
            $image = realpath("../images/InternationalTrackedSigned.jpg");
            $image2 = realpath("../images/rlogo_royalmail.jpg");
            $scanimage = realpath("../images/InternationalScanLogo.PNG");
            $signimage = realpath("../images/Signature.jpg");
        } else if ($this->serviceValues->getCode() == "STRYMIPF2P") {
            $image = realpath("../images/InternationalPriority.jpg");
        }
        if (file_exists($image)) {
            $this->pdf->image($image, 3, 5, 40, 20);
        }
        if (file_exists($image2)) {
            $this->pdf->image($image2, 55, 5, 15, 20);
        }
        $rmlogo = realpath("../images/PostageonAccountInternational.gif");
        if (file_exists($rmlogo)) {
            $this->pdf->image($rmlogo, 73, 5, 25);
        }

        $airmaillogo = realpath("../images/AirMail.jpg");
        if (file_exists($airmaillogo)) {
            $this->pdf->image($airmaillogo, 70, 27, 25, 10);
        }

        if (file_exists($scanimage)) {
            $this->pdf->image($scanimage, 85, 110, 10);
        }
        if (file_exists($signimage)) {
            $this->pdf->image($signimage, 70, 110, 10, 7);
        }
        $this->pdf->rect(1, 1, 99, 149);
        $this->pdf->rect(2, 2, 97, 147);
        $this->pdf->rect(5, 28, 30, 8);
        $this->pdf->line(2, 27, 99, 27);

        $this->pdf->line(2, 37, 99, 37);
        $this->pdf->line(2, 75, 99, 75);
        $this->pdf->line(2, 109, 99, 109);
        $this->pdf->line(2, 117, 99, 117);


        $this->pdf->setFont("helvetica", "", 12);
        $this->pdf->Text(15, 29, $this->country->getIso());

        $this->pdf->setFont("helvetica", "", 8);
        $this->pdf->Text(9, 117, 'Customer Reference');
        $this->pdf->Text(9, 120, $consignment->getHawb());
        $this->pdf->Text(9, 125, 'Department Reference');
        $this->pdf->Text(9, 128, '');

        $this->pdf->setFont("helvetica", "L", 7);
        $pieces = str_pad($parcel_idx + 1, 3, "0", STR_PAD_LEFT);

        $this->pdf->write1DBarcode($licence_plate, 'C128', 50, 48, '45', 20, .4);
        $this->barcode2D($consignment, $licence_plate, "G");
        $prefix = substr($licence_plate, 0, 2);
        $firstdigits = substr($licence_plate, 2, 4);
        $seconddigits = substr($licence_plate, 6, 4);
        $lastdigits = substr($licence_plate, 10, 3);

        $display_barcode = $prefix . " " . $firstdigits . " " . $seconddigits . " " . $lastdigits;
        $this->pdf->setFont("helvetica", "", 8);
        $this->pdf->Text(61, 71, $display_barcode);


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



        $this->pdf->setFont("helvetica", "L", 9);
        $this->pdf->Text(9, 77, $company);
        $this->pdf->Text(9, 80, $consignment->getContact());
        $this->pdf->Text(9, 83, @$address1);
        $this->pdf->Text(9, 86, @$address2);
        $this->pdf->Text(9, 89, @$address3);
        $this->pdf->Text(9, 92, $consignment->getCity());
        $this->pdf->Text(9, 95, $consignment->getPostcode());
        $this->pdf->Text(9, 98, $this->country->getName());

        if (trim($consignment->getNotes()) != '') {
            $fontname = $this->pdf->addTTFfont('../assets/fonts/simhei.ttf', 'TrueTypeUnicode', '', 32);
            $this->pdf->SetFont($fontname, '', 8);
            $this->pdf->Text(9, 111, "Notes : " . $consignment->getNotes());
        }

        $this->pdf->setFont("helvetica", "L", 9);
        $this->pdf->Text(55, 105, $consignment->getReference());

        $this->pdf->StartTransform();
        $this->pdf->Rotate(90, 108, 33);


        $this->pdf->setFont("helvetica", "", 5);
        $this->pdf->Text(32, 11, "RETURN TO:");


        $this->pdf->Text(45, 11, $this->constants["ROYALMAIL_SHIPPER_CONTACT"]);
        $this->pdf->Text(32, 14, $this->constants["ROYALMAIL_SHIPPER_ADDRESSLINE1"] . ", ");
        $this->pdf->Text(32, 17, $this->constants["ROYALMAIL_SHIPPER_ADDRESSLINE2"] . ", " . $this->constants["ROYALMAIL_SHIPPER_POSTCODE"]);

        $this->pdf->StopTransform();
        $this->pdf->write1DBarcode($consignment->getHawb(), 'C128', 50, 120, '40', 10, .5);

        $this->pdf->setFont("helvetica", "B", 12);
        $this->pdf->Text(60, 141, $consignment->getHawb());

        return;
    }

    // Royal Mail F2P International Tracked & Signed
    private function STRMITSF2P($consignment, $parcel_idx, $licence_plate){
        return $this->STRYMITF2P($consignment, $parcel_idx, $licence_plate);
    }

    // Royal Mail F2P International Priority
    private function STRYMIPF2P($consignment, $parcel_idx, $licence_plate){
        return $this->STRYMITF2P($consignment, $parcel_idx, $licence_plate);
    }

    private function getProductCode($handling) {
        if ($handling == "STRYM0TP2" || $handling == "STRYMTP2S") {
            $productCode .= 'TPL01';
        } 
        else if (in_array($handling, array('STRYMINPR', 'STRYMPPKT', 'STRYMPBOX', 'STCBPPKTE', 'STCBLPCTE', 'STCSPPCTE','STCSLPCTE','STCGPPCTE','STCGLPCTE', 'STRYMBAGM')))     {
            $productCode .= 'PS701';
        } else if ($handling == "STRYMEBOX") {
            $productCode .= 'OLS01';
        } else if (in_array($handling, array('STRYMFP24'))) {
            $productCode .= 'DEA01';
        }
        /*else if (in_array($handling, array('STRYMRM1E', 'STRYMRM2E')))     {
            $productCode .= 'CRL01';
        }*/
        else if (in_array($handling, array('STRYMRM1L', 'STRYMRM1P', 'STRYMRM1E', 'STRM1CLSM')))     {
            $productCode .= 'CRL01';//'SU101';
        }
        else if (in_array($handling, array( 'STRYMRM2L', 'STRYMRM2P',  'STRYMRM2E', 'STRM2NDMD')))     {
            $productCode .= 'CRL02';//'SU101';
        }
        else if ($handling == "STRYM0DEJ" || $handling == "STRYMINSN" || $handling == "STRMITSF2P") {
            $productCode .= 'DEJ01';
        }
        else if($handling == "STRYMITF2P"){
            $productCode .= 'DEI01';
        }
        else if($handling == "STRYMIPF2P"){
            $productCode .= 'DEM01';
        }

        return $productCode;
    }

    private function barcode2D(Consignment $consignment, $barcode, $class, $barcodex = 9, $barcodey = 47, $displayx = 8, $displayY = 38) {
        $countryID = 'JGB ';
        $infoTypeId = '6';
        $versionId = '2';
        if(in_array($this->serviceValues->getCode(), array('STRYMEBOX'))){
            $format = '10';
        }
        else if(in_array($this->serviceValues->getCode(), array('STRYMPBOX'))){
            $format = '08';
        }
        else if(in_array($this->serviceValues->getCode(), array('STRYMINPR','STRYMPPKT', 'STRYMPBOX','STCBPPKTE', 'STCBLPCTE', 'STCSPPCTE','STCSLPCTE','STCGPPCTE','STCGLPCTE', 'STRYMBAGM'))){
            $format = '10';
        }
        else{
            $format = '09';
        }
        //$class = $class; //'G';
        $mailType = '2';

        $royalMailSegment = $countryID . $infoTypeId . $versionId . $format . $class . $mailType;

        $channelId = '0B';
        if($consignment->getWareHouseId() == "9"){
            $accountNumber = '0451399000'; //'0209434000';0209434000
        }
        else
        {
            $accountNumber = $this->constants['ROYALMAIL_ACCOUNT_NUMBER']; //'0209434000';
        }

        $accNumFirstThree = substr($accountNumber, 0, 3) . ' ';
        $accNumSecondThree = substr($accountNumber, 3, 3) . ' ';
        $accNumLastFour = substr($accountNumber, 6, 9);

        if (in_array($this->serviceValues->getCode(), array('STRYMRM1P', 'STRYMRM1L', 'STRYMRM2P', 'STRYMRM2L','STRYMRM1E', 'STRYMRM2E', 'STRM2NDMD', 'STRM1CLSM'))) {
            $sequence = substr($barcode, 12, 8);
        }
        else  if(in_array($this->serviceValues->getCode(), array('STRYMINPR','STRYMPPKT', 'STRYMPBOX', 'STCBPPKTE', 'STCBLPCTE', 'STCSPPCTE','STCSLPCTE','STCGPPCTE','STCGLPCTE', 'STRYMBAGM'))){
            $sequence = substr($barcode,3,-2);
        }
        else {
            $sequence = substr($barcode, -8);
        }

        $sequence = str_replace(' ', '', $sequence);



        $customerseq = preg_replace('/[^A-Fa-f0-9]/', '', $sequence);
        $customerseq = $this->lowertoupper($customerseq);
        $customerseq = str_pad($customerseq, 8, '1');
        $customerseqFirstThree = substr($customerseq, 0, 3) . ' ';
        $customerseqSecoundThree = substr($customerseq, 3, 3) . ' ';
        $customerseqLast = substr($customerseq, 6, 8);

       /* if (in_array($this->serviceValues->getCode(), array('STRYMRM1P', 'STRYMRM1L','STRYMRM1E', 'STRYMRM2P', 'STRYMRM2L', 'STRYMRM2E'))) {
            $checkbit = substr($barcode, -1);
        } else {*/
            $luhnCode = $channelId . $accountNumber . $customerseq;
            $total = $this->luhn_process($luhnCode);
            $checkbit = $this->luhn_checkdigit($total);
            $checkbit = $checkbit - $total;
            $checkbit = strtoupper(dechex($checkbit));
        //}


        $textOnBarcode = $channelId . '-' . $accNumFirstThree . $accNumSecondThree . $accNumLastFour . '-' . $customerseqFirstThree . $customerseqSecoundThree . $customerseqLast . $checkbit;
        $dateOfProduction = date('dmy');
        $dateOfShipment = date('dmy');



        $product = $this->getProductCode($this->serviceValues->getCode());
        $itemType = str_pad('', 2, chr(32));
        if ($this->serviceValues->getCode() == "STRYMFP24") {
            $trackingNumber = str_pad('', 13, chr(32));
        }
        else  if(in_array($this->serviceValues->getCode(), array('STRYMINPR','STRYMPPKT','STRYMPBOX','STCBPPKTE', 'STCBLPCTE', 'STCSPPCTE','STCSLPCTE','STCGPPCTE','STCGLPCTE','STRYMBAGM'))){
            $trackingNumber = str_pad($barcode, 13, chr(32));
        }
        else {
            $trackingNumber = $barcode;
        }


        $address = str_replace(".", " ", $consignment->getAddressLine1());
        $buildingNumber = str_pad('', 4, chr(32));
        if (trim(@$this->serviceValues->getCode()) == "STRYM24RN" || trim(@$this->serviceValues->getCode()) == "STRYM48RN") {
            $buildingName = $this->lowertoupper("one world express");
            $postC = str_replace(' ', '', "UB3 3NB");
            $returnPost = str_replace(' ', '', (trim($consignment->getPostCode())));
            $despatchPostCode = str_pad('', 9, chr(32));
        } else {
            $buildingName = $this->lowertoupper(str_replace(".", " ",$consignment->getAddressLine1()));
            $postC = str_replace(' ', '', (trim($consignment->getPostCode())));
            $returnPost = str_replace(" ", "", $this->constants['ROYALMAIL_SHIPPER_POSTCODE']);
            $despatchPostCode = str_pad($returnPost, 9);
        }
        //$BName = strtolower(substr($consignment->getAddressLine1(),0,35));
        $buildingName = str_pad($buildingName, 35, chr(32));
        // }
        $postC = str_replace('-', '', $postC);
        $postC = $this->lowertoupper(($postC));
        $postcode = str_pad($postC, 7, chr(32)) . '9Z';
        if($this->country->getIso() == "GB"){
            $destination = "GBR";
        }
        else{
            $destination = str_pad($this->country->getIso(), 3, " ");
        }
        $returnPost = $this->lowertoupper($returnPost);
        $returnPostCode = str_pad($returnPost, 9);

        if ($this->serviceValues->getCode() == 'STRYMTP2S' || $this->serviceValues->getCode() == 'TP1S') {
            $requiredDelivery = 'S'; // signmed post
        } else {
            $requiredDelivery = ' ';
        }

        $itemWeight = str_pad($consignment->getWeight()* 1000, 7, chr(32));
        if(in_array($this->serviceValues->getCode(), array('STRYMINPR','STRYMPPKT','STRYMPBOX','STCBPPKTE', 'STCBLPCTE', 'STCSPPCTE','STCSLPCTE','STCGPPCTE','STCGLPCTE','STRYMEBOX','STRYMEPKT','STRYMBAGM'))){
            $weightType = '1'; // average
        }
        else{
            $weightType = '0'; // average
        }

        $cust_ref = substr($consignment->getHawb(), 0, 15);

        $customerReference1 = preg_replace('/[^A-Za-z0-9]/', '', $cust_ref);

        $customerReference1 = str_pad($this->lowertoupper($customerReference1), 16, chr(32));
        if ($this->serviceValues->getCode() == 'STRYMRM1L' || $this->serviceValues->getCode() == 'STRYMRM2L' || $this->serviceValues->getCode() == 'STRYMRM1P'
            || $this->serviceValues->getCode() == 'STRYMRM1E' || $this->serviceValues->getCode() == 'STRYMRM2P' || $this->serviceValues->getCode() == 'STRYMRM2E' || $this->serviceValues->getCode() == 'STRM1CLSM' || $this->serviceValues->getCode() == 'STRM2NDMD'  )
        {
            $trackingNumber = '             ';
        }

        $customerReference2 = str_pad('', 16, chr(32));

        if ($this->serviceValues->getCode() == 'STRYMRM1L' || $this->serviceValues->getCode() == 'STRYMRM2L' || $this->serviceValues->getCode() == 'STRYMRM1P'
            || $this->serviceValues->getCode() == 'STRYMRM1E' || $this->serviceValues->getCode() == 'STRYMRM2P'|| $this->serviceValues->getCode() == 'STRYMRM2E' ||  $this->serviceValues->getCode() == 'STRM1CLSM' || $this->serviceValues->getCode() == 'STRM2NDMD')
        {
            $trackingNumber = '             ';
        }
        $channelSegment = $channelId . $accountNumber . $customerseq . $checkbit . $itemWeight . $weightType . $dateOfProduction . $dateOfShipment . $product . $itemType . $trackingNumber . $buildingNumber . $buildingName . $postcode . $destination . $returnPostCode . $despatchPostCode . $requiredDelivery . $customerReference1 . $customerReference2;
        $registrySegment = '               ';
        $securitySegment = '                ';
        $datamatrix_data = $royalMailSegment . $channelSegment . $registrySegment . $securitySegment;

        // create new PDF document
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

        $this->pdf->write2DBarcode($datamatrix_data, 'DATAMATRIX', $barcodex, $barcodey, 23, 23, $style, '', 'Y');
        //$this->pdf->write2DBarcode($datamatrix_data, 'DATAMATRIX', 9, 47, 23, 23, $style, '', 'Y');
        $this->pdf->setFont("helvetica", "", 8);
        $this->pdf->Text($displayx, $displayY, $textOnBarcode);
        $consignment->setRoutingCodeEur($textOnBarcode);
        $consignment->save();
        return $textOnBarcode;
    }

    public function getZone($postcode) {
        $one_level_postcode = substr(trim($postcode), 0, 1);
        $two_level_postcode = substr(trim($postcode), 0, 2);
        $three_level_postcode = substr(trim($postcode), 0, 3);
        $four_level_postcode = substr(trim($postcode), 0, 4);
        $zone = "";
        if ($four_level_postcode == "BFPO")
            $zone = "0";
        else if ($four_level_postcode == "HWDC" || $four_level_postcode == "PRDC")
            $zone = "3";
        else if ($three_level_postcode == "W10" || $three_level_postcode == "W11" || $three_level_postcode == "W12" || $three_level_postcode == "W13")
            $zone = "0";
        else if ($two_level_postcode == "CV" ||
            $two_level_postcode == "MK" ||
            $two_level_postcode == "DE" ||
            $two_level_postcode == "WS" ||
            $two_level_postcode == "WV" ||
            $two_level_postcode == "ST" ||
            $two_level_postcode == "DY" ||
            $two_level_postcode == "NN" ||
            $two_level_postcode == "LE" ||
            $two_level_postcode == "WR" ||
            $two_level_postcode == "HR" ||
            $two_level_postcode == "PR" ||
            $two_level_postcode == "FY" ||
            $two_level_postcode == "BB" ||
            $two_level_postcode == "LA" ||
            $two_level_postcode == "BL" ||
            $two_level_postcode == "OL" ||
            $two_level_postcode == "SK" ||
            $two_level_postcode == "TF")
            $zone = "1";
        else if ($two_level_postcode == "LS" ||
            $two_level_postcode == "WF" ||
            $two_level_postcode == "HG" ||
            $two_level_postcode == "HU" ||
            $two_level_postcode == "DN" ||
            $two_level_postcode == "LN" ||
            $two_level_postcode == "NG" ||
            $two_level_postcode == "YO" ||
            $two_level_postcode == "BD" ||
            $two_level_postcode == "HX" ||
            $two_level_postcode == "HD" ||
            $two_level_postcode == "CH" ||
            $two_level_postcode == "LL" ||
            $two_level_postcode == "CW" ||
            $two_level_postcode == "WA" ||
            $two_level_postcode == "WN" ||
            $two_level_postcode == "SY")
            $zone = "2";
        else if ($two_level_postcode == "EC" ||
            $two_level_postcode == "W1" ||
            $two_level_postcode == "WC" ||
            $two_level_postcode == "CM" ||
            $two_level_postcode == "CO" ||
            $two_level_postcode == "CB" ||
            $two_level_postcode == "SS" ||
            $two_level_postcode == "PE")
            $zone = "3";
        else if ($two_level_postcode == "EH" ||
            $two_level_postcode == "FK" ||
            $two_level_postcode == "KY" ||
            $two_level_postcode == "TD" ||
            $two_level_postcode == "DD" ||
            $two_level_postcode == "PH" ||
            $two_level_postcode == "PA" ||
            $two_level_postcode == "ML" ||
            $two_level_postcode == "KA" ||
            $two_level_postcode == "BT" ||
            $two_level_postcode == "GY" ||
            $two_level_postcode == "JE" ||
            $two_level_postcode == "IM" ||
            $two_level_postcode == "CA" ||
            $two_level_postcode == "DG" ||
            $two_level_postcode == "IV" ||
            $two_level_postcode == "KW" ||
            $two_level_postcode == "HS" ||
            $two_level_postcode == "PL" ||
            $two_level_postcode == "TR" ||
            $two_level_postcode == "AB" ||
            $two_level_postcode == "ZE")
            $zone = "4";
        else if ($two_level_postcode == "NR" ||
            $two_level_postcode == "IP" ||
            $two_level_postcode == "RG" ||
            $two_level_postcode == "OX" ||
            $two_level_postcode == "SN" ||
            $two_level_postcode == "SW" ||
            $two_level_postcode == "KT" ||
            $two_level_postcode == "TW" ||
            $two_level_postcode == "GU" ||
            $two_level_postcode == "DL" ||
            $two_level_postcode == "DH" ||
            $two_level_postcode == "NE" ||
            $two_level_postcode == "SR" ||
            $two_level_postcode == "TS")
            $zone = "5";
        else if ($two_level_postcode == "BA" ||
            $two_level_postcode == "TA" ||
            $two_level_postcode == "BS" ||
            $two_level_postcode == "CF" ||
            $two_level_postcode == "NP" ||
            $two_level_postcode == "BH" ||
            $two_level_postcode == "DT" ||
            $two_level_postcode == "PO" ||
            $two_level_postcode == "SO" ||
            $two_level_postcode == "SP" ||
            $two_level_postcode == "SA" ||
            $two_level_postcode == "GL" ||
            $two_level_postcode == "LD" ||
            $two_level_postcode == "EX" ||
            $two_level_postcode == "TQ")
            $zone = "6";
        else if ($two_level_postcode == "CT" ||
            $two_level_postcode == "CR" ||
            $two_level_postcode == "BR" ||
            $two_level_postcode == "SM" ||
            $two_level_postcode == "BN" ||
            $two_level_postcode == "RH" ||
            $two_level_postcode == "ME" ||
            $two_level_postcode == "TN" ||
            $two_level_postcode == "DA" ||
            $two_level_postcode == "RM" ||
            $two_level_postcode == "IG" ||
            $two_level_postcode == "SE")
            $zone = "7";
        else if ($two_level_postcode == "HA" ||
            $two_level_postcode == "NW" ||
            $two_level_postcode == "SL" ||
            $two_level_postcode == "UB" ||
            $two_level_postcode == "W2" ||
            $two_level_postcode == "W3" ||
            $two_level_postcode == "W4" ||
            $two_level_postcode == "W5" ||
            $two_level_postcode == "W6" ||
            $two_level_postcode == "W7" ||
            $two_level_postcode == "W8" ||
            $two_level_postcode == "W9")
            $zone = "0";
        else if ($two_level_postcode == "AL" ||
            $two_level_postcode == "LU" ||
            $two_level_postcode == "SP" ||
            $two_level_postcode == "SG" ||
            $two_level_postcode == "EN" ||
            $two_level_postcode == "WD")
            $zone = "8";

        else if ($one_level_postcode == "B" || $one_level_postcode == "M")
            $zone = "1";
        else if ($one_level_postcode == "S" || $one_level_postcode == "L")
            $zone = "2";
        else if ($one_level_postcode == "N")
            $zone = "3";
        else if ($one_level_postcode == "G")
            $zone = "4";
        else if ($one_level_postcode == "E")
            $zone = "7";
        return $zone;
    }

    public function tracking($trackingNumber, $trackBy = 'parcel', $EDI = false) {

        $tracking = new Tracking();
        $deliveredArray = array('EVKLC', 'EVKLS', 'EVKNS', 'EVKOP', 'EVKSF', 'EVKSP', 'EVNFS', 'EVNLC',
            'EVNLP', 'EVNPO', 'EVPLA', 'EVKDN');

        $service_url = "https://api.royalmail.net/mailpieces/v2/$trackingNumber/events";
        $headers = array('X-IBM-Client-Secret: O5vN0wX5qX4vM6oD6cV6mG8uX6iQ5tV3cM3nS3kE6oD4sV8fE1',
            'X-IBM-Client-Id: 9509a877-9374-44d5-a5b4-fff2ac586bc9',
            'Accept: application/json',
            'Host: api.royalmail.net'
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $service_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $server_output = curl_exec($ch);
        curl_close($ch);

        $trackDetailArray = json_decode($server_output, true);

        $trackDetailArray = $trackDetailArray['mailPieces']['events'];

        if (count($trackDetailArray) > 0)
        {
            $entityId = 0;
            if ($trackBy == 'parcel') {
                $parcelObj = new ParcelFilter();
                $parcelObj->addTrackingNumberFilter($trackingNumber);
                $parcelDataArray = $parcelObj->getColumnList('p.id, p.tracking_number, p.consignment_id');

                if (count($parcelDataArray) > 0) {
                    $parcelData = $parcelDataArray[0];
                    $entityId = $parcelData->getId();
                }
            } else if ($trackBy == 'shipment') {
                $shipmenObj = new ConsignmentFilter();
                $shipmenObj->addawbFilter($trackingNumber);
                $shipmentDataArray = $shipmenObj->getColumnList('c.awb');
                if (count($shipmentDataArray) > 0) {
                    $shipmentData = $shipmentDataArray[0];
                    $entityId = $shipmentData->getId();
                }
            }

            ////////////////////// Carrier Received ////////////////////////////////

            $trackingDataFilterObj = new TrackingDataFilter();
            $trackingDataFilterObj->addFieldFilter('    tracking_number', $trackingNumber);
            $trackingDataFilterObj->addFilter("carrier_code not in ('','1')");
            $trackingEvents = $trackingDataFilterObj->getList();

            if(count($trackingEvents) > 0)
            {
                $carrierReceivedCheck = 0;   // there is already carrier received event
            }
            else
            {
                $carrierReceivedCheck = 1; // No carrier received Event
            }
            ////////////////////// Carrier Received ////////////////////////////////

            if ($entityId > 0) {
                $parcelEntity = new Parcel($entityId);
                $finalStatusCode = $parcelEntity->getParcelStatusCode();

                foreach ($trackDetailArray as $event) {
                    $DateTime1 = $event['eventDateTime'];
                    $dateArray = explode("T", $DateTime1);
                    $date = date("Y-m-d", strtotime(trim($dateArray[0])));
                    $timeArray = explode("+", $dateArray[1]);
                    $time = str_replace("Z", "", $timeArray[0]);
                    $DateTime = $date . ' ' . $time;

                    $EventCode = $event['eventCode'];
                    $EventDescription = $event['eventName'];
                    $ServiceAreaDescription = $event['locationName'];
                    $Signatory = is_string($event['signatory']) ? $event['signatory'] : '';

                    $spTrackingStatus = RoyalMailTrackingStatus::getOweStatusCode($EventCode);

                    ////////////////////// Carrier Received ////////////////////////////////
                    if($carrierReceivedCheck == 1 && $EventCode != '1' )
                    {
                        $spTrackingStatus = '148';
                        $carrierReceivedCheck = 0 ;
                    }
                    else
                    {
                        $spTrackingStatus = RoyalMailTrackingStatus::getOweStatusCode($EventCode);
                    }
                    ////////////////////// Carrier Received ////////////////////////////////

                    $result = $tracking->saveTrackPoint($entityId, $trackBy, $DateTime, $trackingNumber, $spTrackingStatus, $ServiceAreaDescription, $EventCode, $EventDescription, $deliveredArray, $finalStatusCode);
                    if($result == true)
                    {
                        break;
                    }
                }
                $tracking->saveConsignmentTrackingStatus($trackingNumber, 'RoyalMailTrackingStatus');
            }
        }
    }

    public function sendData($tracking_numbers = array()) {

        $output = [];
        $agentServiceArray = [];
        $consignmentData = new ConsignmentFilter();
        $consignmentData->addFilter("     s.carrier_id= '15'", "servicefilter");
        $consignmentData->addFilter("     ( c.send_courier_data= '0' or c.send_courier_data is null)", "consignmentfilter");
        if (!empty($tracking_numbers)) {
            $consignmentData->addFilter("AND pc.tracking_number in ('" . implode("','", $tracking_numbers) . "') and pc.tracking_number <> ''", "parcelJoinFilter");
        }
        $consignmentData->addGroupBy(" c.service_id, c.agent_id ");

        $consignmentServiceAgent = $consignmentData->getColumnList("c.service_id, c.agent_id");
        if (count($consignmentServiceAgent) > 0) {
            foreach ($consignmentServiceAgent as $agentData) {
                $agentServiceArray['services'][] = $agentData->getServiceId();
                $agentServiceArray['agent'][] = $agentData->getAgentId();
            }
        } else {
            $output["STATUS"] = "ERROR";
            $output["MESSAGE"] = "Please check we did not find any agent and services for data to send.";
        }

        if (!empty($agentServiceArray['services'])) {

            foreach ($agentServiceArray['services'] as $key => $serviceid) {
                $serviceid = trim($serviceid);
                $agentid = trim($agentServiceArray['agent'][$key]);

                $agentObject = new AgentData($agentid);
                $agentCountryObject = new Country($agentObject->getCountryId());
                $this->serviceValues = new Services($serviceid);

                // GET CONSTANTS AS PER SERVICE AND AGENT
                $serviceAgentConstantFilter = new ServiceConstantValueFilter();
                $serviceAgentConstantFilter->addFilter("service_id = '" . $serviceid . "' AND agent_id = '" . $agentid . "' ");
                $serviceAgentConstant = $serviceAgentConstantFilter->getConstantList("c.constant 'constant_name', scv.constant_value 'constant_value'");
                if (count($serviceAgentConstant) > 0) {
                    foreach ($serviceAgentConstant as $serviceAgentConstantData) {
                        $this->constants[$serviceid][$serviceAgentConstantData->getConstantName()] = $serviceAgentConstantData->getConstantValue();
                    }
                }
                if (!empty($this->constants[$serviceid])) {
                    // GET ALL THE CONSIGNMENT WITH THE PARCEL FOR PREPARE  FILE
                    $consignmentShipmentDataFilter = new ConsignmentFilter();
                    $consignmentShipmentDataFilter->addFilter("     c.service_id = '" . $serviceid . "' AND c.agent_id = '" . $agentid . "' ", "consignmentfilter");
                    $consignmentShipmentDataFilter->addFilter("     ( c.send_courier_data= '0' or c.send_courier_data is null)", "consignmentfilter");
                    if (!empty($tracking_numbers))
                        $consignmentShipmentDataFilter->addFilter("     AND pc.tracking_number in ('" . implode("','", $tracking_numbers) . "') and pc.tracking_number <> ''", "parcelJoinFilter");
                    $consignmentShipmentData = $consignmentShipmentDataFilter->getColumnList(" c.id 'consignment_id', s.carrier_id ,s.code 'service_code',
                     con.region 'country_region', c.service_id, c.weight, c.hawb, c.description, c.company,
                      c.contact, c.address_line_1, c.address_line_2, c.city, c.postcode, c.telephone, c.other_routing_code, routing_code_eur, c.country_id, c.currency, c.number_pieces, c.email, c.value, c.eori_number, c.sender_name, c.ioss_number, c.vat_number ");

                    if (count($consignmentShipmentData) > 0) {
                        $consignmentIdArray = [];
                        $this->record_array = null;
                        $this->record_idx = 0;
                        
                        foreach ($consignmentShipmentData as $consignmentItemData) {
                            $consignmentId = $consignmentItemData->getConsignmentId();
                            $consignmentIdArray[] = $consignmentId;
                            $serviceCode = $consignmentItemData->getServiceCode();
                            $carrierId = $consignmentItemData->getCarrierId();
                            $serviceType = $consignmentItemData->getCountryRegion();
                            $parcelOjects = $consignmentItemData->getParcels();
                            $parcel = [];
                            foreach ($parcelOjects as $parcels) {
                                $parcel[] = $parcels->getTrackingNumber();
                            }
                            $this->meterNumber = $this->constants[$serviceid]['ROYALMAIL_METER_NUMBER'];
                            if ($this->record_idx == 0) {
                                $this->record_array[] = $this->getConsignmentRecord($consignmentItemData, $this->constants[$serviceid]);
                                ++$this->record_idx; // starts at one
                            }
                            foreach ($parcel as $parcel_licencePlate) {
                                $this->record_array[] .= $this->getPieceRecord($parcel_licencePlate, $consignmentItemData, $parcelOjects, $this->constants[$serviceid]);
                            }
                        }
                        $carrierId = $this->serviceValues->getCarrierId();
                        $agentid = trim($agentServiceArray['agent'][$key]);
                        $this->run_number = $run_number = CarrierDataFileLog::generateRunNumber($carrierId, $agentid, false);
                        $this->royalmail_file = "COSS" . $this->meterNumber . "_PreAdvice3_" . str_pad(substr("00$run_number", - 4), 9, "0", STR_PAD_LEFT);

                        $carrierDataFileLog = new CarrierDataFileLog();
                        $carrierDataFileLog->setCarrierId($carrierId);
                        $carrierDataFileLog->setAgentId($agentid);
                        $carrierDataFileLog->setFileName($this->royalmail_file);
                        $carrierDataFileLog->setRunNumber($run_number);
                        $carrierDataFileLog->save();

                        if ($this->sendBookings($this->constants[$serviceid])) {
                            if (!empty($consignmentIdArray)) {

                                $sql = "UPDATE consignment SET send_courier_data = 1, booked_file_id = '" . $this->royalmail_file . "' 
                                        WHERE id IN ('" . implode("','", $consignmentIdArray) . "')  AND id <> '0' 
                                        AND  shipment_status not in ('" . Consignment::STATUS_RECYCLED . "','" . Consignment::STATUS_READY_TO_PRINT . "','" . Consignment::STATUS_INVALID . "')";
                                DbAccess3::runQuery($sql);
                                $output["STATUS"] = "SUCCESS";
                                $output["MESSAGE"] = "System has successfully send data.";
                            } else {
                                $output["STATUS"] = "ERROR";
                                $output["MESSAGE"] = "No consignment found to send data to carrier.";
                            }
                        } else {
                            $output["STATUS"] = "ERROR";
                            $output["MESSAGE"] = "Please check constants, System not able to find FTP details to send data to yodel. Please fix it ASAP";
                        }
                    } else {
                        $output["STATUS"] = "ERROR";
                        $output["MESSAGE"] = "Please check consignment filter is not working properly.";
                    }
                }
            }
        } else {
            $output["STATUS"] = "ERROR";
            $output["MESSAGE"] = "Please check constants, System not able to find service details to send data to yodel. Please fix it ASAP";
        }
    }

    private function getConsignmentRecord(Consignment $consignment, $constant) {

        $record = '"01"' . ",";        // M record header
        $record .= '"03"' . ",";    // M version id
        $record .= '"' . $constant['ROYALMAIL_SHIPPER_CONTACT'].'"' . ",";         // M shipment reco
        $record .= '"' . $constant['ROYALMAIL_SHIPPER_CONTACT'] . '"' . ","; //'TPL01'; '"ONE WORLD EXPRESS"' . ",";    // D awb
        $record .= '"' . $constant['ROYALMAIL_SHIPPER_ADDRESSLINE1'] . '"' . ","; //'TP'"ONE WORLD HOUSE"' . ",";  // D destination code => DHL database (country/city/zip => airport)
        $record .= '"' . $constant['ROYALMAIL_SHIPPER_ADDRESSLINE2'] . '"' . ","; //'TP'"PUMP LANE"' . ",";
        $record .= '"' . $constant['ROYALMAIL_SHIPPER_ADDRESSLINE3'] . '"' . ","; //'"HAYES"' . ",";
        $record .= '"GB"' . ",";
        $record .= '"' . $constant['ROYALMAIL_SHIPPER_CITY'] . '"' . ","; //'"LONDON"' . ",";
        $record .= '"' . $constant['ROYALMAIL_SHIPPER_POSTCODE'] . '"' . ","; //'"UB3 3NB"' . ",";
        $record .= '""' . ",";
        $record .= '"' . $constant['ROYALMAIL_SHIPPER_TELEPHONE'] . '"' . ",";
        $record .= '""' . ",";
        $record .= '""' . ",";
        $record .= '""' . ",";
        $record .= '"' . $constant['ROYALMAIL_SHIPPER_EMAIL'] . '"' . ",";

        if($constant['ROYALMAIL_POSTING_HUB'] != ''){
            $record .= '"'.$constant['ROYALMAIL_POSTING_HUB'].'"' . ",";  //posting hub
        }else
            $record .= '"002673"' . ",";  //posting hub
        if($constant['ROYALMAIL_POSTING_LOCATION'] != ''){
            $record .= '"'.$constant['ROYALMAIL_POSTING_LOCATION'].'"';  //posting hub
        }else
            $record .= '"9000341216"';  //posting location



        $record .= "\r\n";
        return $record;
    }

    private function getPieceRecord($parcel_licenceplate, Consignment $consignment, $parcel_list, $constant) {

        $country = new Country($consignment->getCountryId());

        $record = '"02"' . ",";        // M record type indicator
        $record .= '"03"' . ",";    // File version id

        $barcode = $parcel_licenceplate;
        $record .= '""' . ",";         // Consignment Number
        $product = $this->getProductCode($this->serviceValues->getCode());

        $record .= '"' . $product . '"' . ",";
        // $record .= '"' . $constant['ROYALMAIL_PRODUCT_CODE'] . '"' . ","; //'TPL01';
        $record .= '""' . ",";         // Weekend Handling Code
        $record .= '""' . ",";         // Spare Field
        $record .= '""' . ",";         // Spare Field#
        $record .= '""' . ",";         // Sender Refernece 1
        $record .= '""' . ",";         // Collection ID
        $record .= '""' . ",";         // Contract Number
        $record .= '""' . ",";         // Weight
        $record .= '""' . ",";         // Number of Items
        $record .= '""' . ",";         // Spare Field
        $record .= '"' . $this->removecommas(($consignment->getContact())) . '"' . ",";         // Contact
//
        $record .= '"' . $this->removecommas(($consignment->getAddressLine1())) . '"' . ",";  // Delivery Address1
        $record .= '"' . $this->removecommas(($consignment->getAddressLine2())) . '"' . ","; // Delivery Address2
        $record .= '"' . $this->removecommas(($consignment->getAddressLine3())) . '"' . ",";  // Delivery Address3
        $record .= '"' . $this->removecommas(($consignment->getCity())) . '"' . ",";   //City
        $record .= '"' . $this->removecommas(($consignment->getPostcode())) . '"' . ",";  //postcode
        $record .= '"' . $this->removecommas(($consignment->getContact())) . '"' . ",";    // Receipt Name
        $record .= '""' . ",";         // Building Number
        $record .= '""' . ",";         // Building Name
        $record .= '""' . ",";         // Optional Address 4
        $record .= '""' . ",";
        $record .= '"' . str_pad($country->getIso(),3) . '"' . ",";         // Country
        $record .= '""' . ",";         // Destination Code
        $routincode = str_replace("-", "", $consignment->getRoutingCodeEur());
        $routincode = str_replace(" ", "", $routincode);
        $record .= '"' . $routincode . '"' . ",";
        /*if (in_array($this->serviceValues->getCode(), array('STRYMFP24', 'STRYMRM1P','STRYMRM1E', 'STRYMRM1L', 'STRYMRM2P', 'STRYMRM2L', 'STRYMRM2E')))
            $record .= '""' . ",";  //Weight Type
        else {*/
            $weight = intval(($consignment->getWeight() / $consignment->getNumberPieces()) * 1000);
            $record .= '"' . str_pad($weight, 7) . '"' . ",";  //for weight
        //}
        if (in_array($this->serviceValues->getCode(), array('STRYMINPR','STRYMPPKT','STRYMPBOX','STCBPPKTE', 'STCBLPCTE', 'STCSPPCTE','STCSLPCTE','STCGPPCTE','STCGLPCTE','STRYMEBOX','STRYMEPKT','STRYMBAGM')))
            $record .= '"1"' . ",";  //Weight Type
        else
            $record .= '"0"' . ",";  //Weight Type
        $record .= '""' . ",";         // Type of Item

        if ($country->getRegion() == "DBP")
            $record .= '"09"' . ",";         // Format
        else if (in_array($this->serviceValues->getCode(), array('STRYMEBOX'))){
            $record .= '"07"' . ",";         // Format
        }
        else if (in_array($this->serviceValues->getCode(), array('STRYMEPKT'))){
            $record .= '"08"' . ",";         // Format
        }
        else {
            $record .= '"10"' . ",";         // Format
        }
        $record .= '""' . ",";         // Dimensions
        $record .= '"0"' . ",";         // Dimension Type
        $record .= '""' . ",";         // Declared Value
        $record .= '""' . ",";         // Price Paid
        $record .= '""' . ",";         // POL FAD Code
        $record .= '""' . ",";
        if (in_array($this->serviceValues->getCode(), array('STRYMFP24', 'STRYMRM1P','STRYMRM1E', 'STRYMRM1L', 'STRM1CLSM'))) {// Die Number
            $record .= '""' . ",";       // 1D Tracking Number
            $record .= '"F"' . ",";
        }
        else if (in_array($this->serviceValues->getCode(), array('STRYMEPKT', 'STRYMEBOX'))) {// Die Number
            $record .= '"' . $barcode . '"' . ",";         // 1D Tracking Number
            $record .= '"7"' . ",";
        }
        elseif ($country->getRegion() == "DBP") {
            $record .= '"' . $barcode . '"' . ",";         // 1D Tracking Number
            $record .= '"G"' . ",";
        } else {
            $record .= '"' . $barcode . '"' . ",";         // 1D Tracking Number
            $record .= '"6"' . ",";
        }


        // Class
        $record .= '""' . ",";         // Production date
        $record .= '""' . ",";         // Date of Shipment
        $record .= '""' . ",";         // tarrif Rate
        $record .= '""' . ",";
        if ($this->serviceValues->getCode() == "STRYMFP24" || $country->getRegion() != "DBP") {// Die Number
            $record .= '"GBR"' . ",";
            $record .= '""' . ",";      // Sort Code
        } else {
            $record .= '"' . $consignment->getOtherRoutingCode() . '"' . ",";
            if (!in_array($this->serviceValues->getCode(), array('STRYMRM1P','STRYMRM1E', 'STRYMRM1L', 'STRYMRM2P', 'STRYMRM2L', 'STRYMRM2E', 'STRM1CLSM' ,'STRM2NDMD'))){
                $record .= '"536000TL"' . ",";
            }
        }
        // Contract Code
        $record .= '""' . ",";         // Sender Reference 2
        $record .= '""' . ",";         // Service Enhance Ment
        $record .= '""' . ",";         // Recipient Contact No
        $record .= '""';         // Expected Date  of Delivery

        $record .= "\r\n";
        $this->array_length++;
        if (!in_array($this->serviceValues->getCode(), array('STRYMRM1P','STRYMRM1E', 'STRYMRM1L', 'STRYMRM2P', 'STRYMRM2L', 'STRYMRM2E', 'STRM1CLSM' ,'STRM2NDMD'))){
        // Detail Suppliment Record
            $record .= '"03"' . ",";
            $record .= '"03"' . ",";
            $record .= '"'.$routincode.'"' . ",";
            $record .= '"EM"' . ",";
            $record .= '"'.$consignment->getEmail().'"' ;
            $record .= "\r\n";
            $this->array_length++;
        }
        
        $record .= '"06"' . ",";        // M record type indicator
        $record .= '"03"' . ",";

        $record .= '""' . ",";
        $record .= '"' . $routincode . '"' . ",";
        $record .= '""' . ",";
        $record .= '"' . $consignment->getWeight() . '"' . ",";
        $record .= '""' . ",";
        $record .= '""' . ",";
        $record .= '"' . $consignment->getCurrency() . '"' . ",";
        $record .= '"' . str_replace('.', '', $consignment->getValue()) . '"' . ",";
        $record .= '""' . ",";
        $record .= '""' . ",";
        $record .= '""' . ",";
        $record .= '"'.str_replace('.', '', $consignment->getValue()).'"' . ","; // if DDU then blank else pass value
        $record .= '""' . ",";
        $record .= '"'. $consignment->getSenderName() . '"' . ","; // Sender Telephone
        $record .= '""' . ","; // Fax
        $record .= '""' . ","; //VAT No
        $record .= '""' . ",";
        $record .= '""' . ",";
        $record .= '""' . ",";
        $record .= '""' . ","; 
        $record .= '""' . ",";
        $record .= '""' . ",";
        $record .= '""' . ","; // Invoice  No
        $record .= '""' . ","; // Invoice Date
        $record .= '""' . ",";
        $record .= '""' . ",";
        $record .= '""' . ",";
        if ($consignment->getTelephone() != '') {
            $record .= '"' . $consignment->getTelephone() . '"' . ",";
        } else {
            $record .= '"02088676060"' . ",";  ////// telephone receipient
        }
        $record .= '"'.$consignment->getEoriNumber().'"' . ",";
        $record .= '""' . ",";
        $record .= '""' . ",";
        $record .= '""' . ",";
        $record .= '"O"' . ",";
        $record .= '"' . $consignment->getDescription() . '"' . ",";
        $ioss="";
        $dutyInfo = "DDU";
        if(trim($consignment->getIossNumber()) != ""){
            $ioss = $consignment->getIossNumber();
            $dutyInfo = "PRS";
        }
        $record .= '"'.$ioss.'"' . ","; //37 for a pre-registration scheme. E.g. Norway’s VOEC, Australia’s GST or the EU’s IOSS.
        $record .= '"'.$dutyInfo.'"' . ",";
        $record .= '"200"' . ","; //39  Postal Charges
        $record .= '""' . ",";
        $record .= '""' . ",";
        $record .= '""' . ","; //42
        $record .= '""' . ","; //43
        $record .= '""' . ","; //44
        $record .= '"' . $consignment->getContact() . '"' . ","; //45
        $record .= '"' . $consignment->getCompany() . '"' . ","; //46
        $record .= '""' . ","; //47
        $record .= '""' . ","; //48
        $record .= '""' . ","; //49
        $record .= '"' . $consignment->getCity() . '"' . ",";  //50
        $record .= '""'; //51
        $record .= "\r\n";
        $this->array_length++;
        
        if(count($parcel_list) > 0){
            foreach($parcel_list as $parcel){
                $parcelDescription = json_decode($parcel->getDescription());
                $parcelCountry = json_decode($parcel->getCommodityCode());
                $parcelQty = json_decode($parcel->getQty());
                $parcelValue = json_decode($parcel->getItemValue());
                $parcelHscode = json_decode($parcel->getHsCode());
                $parcelSku = json_decode($parcel->getItemSku());
                $parcelWeight = json_decode($parcel->getPWeight());
                $senderCountry = new Country($consignment->getSenderCountryId());
                if(count($parcelDescription) > 0){
                    foreach($parcelDescription as $key=>$desc){
                        $countryFilter = new CountryFilter();
                        $countryFilter->addFieldFilter("iso", $parcelCountry[$key]);
                        $manufactureCountry = $countryFilter->getList();
                        if(count($manufactureCountry) > 0){
                            $man_country = $manufactureCountry[0]->getIsoThree();
                        }
                        else{
                            $man_country = $senderCountry->getIsoThree();
                        }
                        $record .= '"07"' . ",";
                        $record .= '"03"' . ",";
                        $record .= '""' . ",";
                        $record .= '"' . $routincode . '"' . ",";
                        $record .= '"' . $parcelQty[$key] . '"' . ",";
                        $record .= '"' . $desc . '"' . ",";
                        $record .= '"'.$man_country.'"' . ",";
                        $record .= '"' . str_replace('.', '', $parcelValue[$key]) . '"' . ",";
                        $record .= '"' . number_format($parcelWeight[$key], 3) . '"' . ",";
                        $record .= '"'.$parcelHscode[$key].'"' . ","; // Others hscode
                        $record .= '""' . ",";
                        $record .= '""' . ",";
                        $record .= '""' . ",";
                        $record .= '""' . ",";
                        $record .= '"' . $consignment->getCurrency() . '"';
                        $record .= "\r\n";
                        $this->array_length++;
                    }
                }
                else
                {
                    $record .=  '"07"' . ",";	
                    $record .=  '"03"' . ","; 
                    $record .=  '""' . ",";              
                    $record .=  '"'.$routincode.'"' . ",";
                    $record .=  '"'.$consignment->getNumberPieces().'"' . ",";
                    $record .=  '"'.$consignment->getDescription().'"' . ",";
                    $record .=  '"'.$senderCountry->getIsoThree().'"' . ",";
                    $record .=  '"'.str_replace('.','',$consignment->getValue()).'"' . ",";
                    $record .=  '"'.$consignment->getweight().'"' . ",";
                    $record .=  '""' . ","; 
                    $record .=  '""' . ","; 
                    $record .=  '""' . ","; 
                    $record .=  '""' . ","; 
                    $record .=  '""' . ","; 
                    $record .=  '"'.$consignment->getCurrency().'"';
                    $record .= "\r\n";                
                    $this->array_length++;
                }
            }
        }

        return $record;
    }

    public function sendBookings($ftpConstants) {

        require_once(SETTING_DIR_REMOTE . "includes/3rdparty/Net/SFTP.php");
        if (sizeof($this->record_array) > 0) {
            $linkFile = $this->royalmail_file;

            $path = SETTING_DIR_ASSETS . "data_send/royalmail_booking/" . date("Y_m_d") . "/";
            if (!file_exists($path))
                @mkdir($path, 0777, TRUE);

            $file_path = $path . $linkFile . ".csv";
            chmod($path, 0777);


            // record count including header & footer
            $record_count = sizeof($this->record_array) + 3;

            // create file
            $file_handle = @fopen($file_path, 'w');
            fwrite($file_handle, $this->getHeaderRecord($this->run_number, $ftpConstants));

            foreach ($this->record_array as $record) {
                fwrite($file_handle, "$record");
            }
            fwrite($file_handle, $this->getFooterRecord());
            // close file
            fclose($file_handle);
            $local_file = realpath($file_path);



            if (isset($ftpConstants['ROYALMAIL_FTP_SITE']) && trim($ftpConstants['ROYALMAIL_FTP_SITE']) != '') {
                $SETTING_FTP_USER_ROYALMAIL = $ftpConstants['ROYALMAIL_FTP_USER'];
                $SETTING_FTP_PASSWORD_ROYALMAIL = $ftpConstants['ROYALMAIL_FTP_PASSWORD'];
                $SETTING_FTP_SITE_ROYALMAIL = $ftpConstants['ROYALMAIL_FTP_SITE'];

                $ssh = new Net_SFTP($SETTING_FTP_SITE_ROYALMAIL, 22022);
                if (!$ssh->login($SETTING_FTP_USER_ROYALMAIL, $SETTING_FTP_PASSWORD_ROYALMAIL)) {
                    mail("itsupport@oneworldexpress.com", "ROYALMAIL LOGIN FAILED", "ROYALMAIL LOGIN FAILED" . $this->royalmail_file);
                    //exit('Login Failed');
                }
                $remotefile = "/pub/tracked/incoming/" . $this->royalmail_file . ".csv";
                $ssh->put($remotefile, $file_path, NET_SFTP_LOCAL_FILE);
                $this->link_file = NULL;
                $this->record_array = NULL;
                $this->array_length = 0;
                return true;
            } else {
                return false;
            }
        }
    }

    private function getHeaderRecord($run_number, $ftpConstants) {
        $record = '"00"' . ",";   // M record header
        $record .= '"03"' . ",";    // M version id
        $record .= '"RMBS"' . ",";   // M header record id
        if($this->warehouseId == "9"){
            $record .= '"0451399000"' . ",";
        }
        else
        {
            $record .= '"' . $ftpConstants['ROYALMAIL_ACCOUNT_NUMBER'] . '"' . ",";  // Generic Account Number 0209434000
        }
        $record .= '"MULTIPLE"' . ",";  // Generic Contract Code
        $record .= '"' . substr($run_number, -4) . '"' . ",";  // Batch Number
        $record .= '""' . ","; // collection set to today!
        $record .= '""' . ",";   // collection set to today!
        $record .= '""' . ",";   // collection set to today!
        $record .= '"LIVE"' . ",";
        $record .= '"' . date('c') . '"' . ",";
        $record .= '"' . $ftpConstants['ROYALMAIL_METER_NUMBER'] . '"' . ",";  //wire number
        $record .= '""' . ",";
        $record .= '"0B"';

        $record .= "\r\n";
        return $record;
    }

    private function getFooterRecord() {
        $record = '"09"' . ",";   // M record header
        $record .= '"03"' . ",";    // M version id
        $record .= '"' . str_pad($this->array_length + 3, 5, "0", STR_PAD_LEFT) . '"';
        $record .= "\r\n";
        return $record;
    }

    private function removecommas($data) {
        return str_replace(",", " ", $data);
    }

    public function manifest($consignmentObj)
    {
        $this->fpdi = new FPDI();
        $output = [];

        if (count($consignmentObj) > 0)
        {
            $consignmentData = new ConsignmentFilter();
            foreach($consignmentObj as $consignmentObjArray)
            {
                $consignmentIds[] = $consignmentObjArray->getId();
                $parcelIds[] = $consignmentObjArray->getParcelId();
            }
            $consignmentIdsStr = implode("','", $consignmentIds);
            $parcelIdsStr = implode("','", $parcelIds);

            $consignmentData->addFilter("     AND pc.consignment_id in ('" . $consignmentIdsStr . "') and pc.tracking_number <> ''", "parcelJoinFilter");
            $consignmentData->addGroupBy(" c.service_id, c.agent_id ");
            $consignmentServiceAgent = $consignmentData->getColumnList("c.service_id, c.agent_id");

            if (count($consignmentServiceAgent) > 0)
            {
                foreach ($consignmentServiceAgent as $agentData)
                {
                    $agentServiceArray['services'][] = $agentData->getServiceId();
                    $agentServiceArray['agent'][] = $agentData->getAgentId();
                }
            }
            else
            {
                $output["STATUS"] = "ERROR";
                $output["MESSAGE"] = "Please check we did not find any agent and services for data to send.";
                return $output;
            }
            $manifestFilter = new ManifestEntityMappingFilter();
            $manifestFilter->addFilter("    entity_id in ('" . $parcelIdsStr . "')");
            $manifestFilter->addGroupBy("mem.manifest_id");
            $manifestIdArray = $manifestFilter->getColumnList('mem.manifest_id');

            if(count($manifestIdArray) >0)
            {
                $manifestId = $manifestIdArray[0]->getManifestId();
            }

            if (!empty($agentServiceArray['services']))
            {
                foreach ($agentServiceArray['services'] as $key => $serviceid)
                {
                    $serviceid = trim($serviceid);
                    $agentid = trim($agentServiceArray['agent'][$key]);
                    $this->serviceValues = new Services($serviceid);
                    // GET CONSTANTS AS PER SERVICE AND AGENT
                    $serviceAgentConstantFilter = new ServiceConstantValueFilter();
                    $serviceAgentConstantFilter->addFilter("service_id = '" . $serviceid . "' AND agent_id = '" . $agentid . "' ");
                    $serviceAgentConstant = $serviceAgentConstantFilter->getConstantList("c.constant 'constant_name', scv.constant_value 'constant_value'");
                    if (count($serviceAgentConstant) > 0)
                    {
                        foreach ($serviceAgentConstant as $serviceAgentConstantData)
                        {
                            $this->constants[$serviceid][$serviceAgentConstantData->getConstantName()] = $serviceAgentConstantData->getConstantValue();
                        }
                    }

                    if (!empty($this->constants[$serviceid]))
                    {
                        $accountNumber = $this->constants[$serviceid]['ROYALMAIL_ACCOUNT_NUMBER'];

                        // GET ALL THE CONSIGNMENT WITH THE PARCEL FOR PREPARE  FILE
                        $consignmentShipmentDataFilter = new ConsignmentFilter();
                        $consignmentShipmentDataFilter->addFilter("     c.service_id = '" . $serviceid . "' AND c.agent_id = '" . $agentid . "' ", "consignmentfilter");
                        $consignmentShipmentDataFilter->addFilter("     AND pc.consignment_id in ('" . implode("','", $consignmentIds) . "') and pc.tracking_number <> ''", "parcelJoinFilter");
                        $consignmentShipmentDataFilter->AddOrderBy("    c.id", "consignment", false);
                        $consignmentShipmentData = $consignmentShipmentDataFilter->getColumnList(" c.id as 'consignment_id', pc.tracking_number");


                        $totalShipments = count($consignmentShipmentData);
                        $secondNumber  = $consignmentShipmentData[0]->getTrackingNumber();
                        $firstNumber   = $consignmentShipmentData[$totalShipments-1]->getTrackingNumber();

                        if (count($consignmentShipmentData) > 0)
                        {
                            //$salesOrder = new PDFMergerSalesOrder();
                            $this->merge($totalShipments, $secondNumber, $firstNumber, $manifestId, $accountNumber);
                            $filename = time() . "manifestreport.pdf";
                            $fileNameNew = "../_assets/manifest/pdf/" . $filename;
                            $this->fpdi->Output($fileNameNew, 'F');
                            $output['file_path'] = $fileNameNew;
                            $output['FILE'] = $filename;
                            $output['STATUS'] = true;
                            $output['MESSAGE'] = "pdf generated successfully";
                        } else {
                            $output['STATUS'] = false;
                            $output['MESSAGE'] = "No consignment found for pdf";
                        }
                        return $output;

                    }
                }
            }
        }
    }

    public function merge($totalNumber, $secondNumber, $firstNumber, $manifestId, $accountNumber) {
        $this->fpdi = new FPDI();
        $this->fpdi->SetPrintHeader(false);
        $this->fpdi->SetPrintFooter(false);

        $this->fpdi->SetHeaderMargin(0);
        $this->fpdi->SetFooterMargin(0);
        $this->fpdi->SetAutoPageBreak(false, 0);

        $salesOrder1 = SETTING_DIR_REMOTE . "images/salesOrderS1.pdf";
        $salesOrder2 = SETTING_DIR_REMOTE . "images/salesOrderS2.pdf";

        //merger operations
        $filepages = 'all';
        $count = $this->fpdi->setSourceFile($salesOrder1);

        //add the pages
        if ($filepages == 'all') {
            for ($i = 1; $i <= $count; $i++) {
                $template = $this->fpdi->importPage($i);
                $size = $this->fpdi->getTemplateSize($template);
                $this->fpdi->AddPage('P', array(150, 100));
                $this->fpdi->useTemplate($template, 0.5, 4, 97, 140);

                // add information
                $y = 58.5;
                $x = 80;
                $this->fpdi->SetFont('helvetica', '', 8);
                // $this->fpdi->Text($x, $y, $totalNumber);
                // $this->fpdi->Text($x, $y+4, $trackedNumber);

                $style = array(
                    'position' => '',
                    'align' => '',
                    'stretch' => true,
                    'fitwidth' => true,
                    'cellfitalign' => '55',
                    'border' => false,
                    'hpadding' => '0',
                    'vpadding' => '0',
                    'fgcolor' => array(0, 0, 0),
                    'bgcolor' => false, //array(255,255,255),
                    'text' => false,
                    'font' => 'helvetica',
                    'fontsize' => 15,
                    'stretchtext' => 1
                );
                // $manifest_series = LicencePlate::getLicencePlateNumber('45');
                // $this->manifest_number = str_pad($manifest_series, 5, 0, STR_PAD_LEFT);
                // $this->fpdi->SetFont('helvetica', '', 7);
                $this->fpdi->write1DBarcode($accountNumber.'9000491451' . $manifestId, 'C128', 52, 8, '', 10, 4, $style, '');
                /// end add information

                $count = $this->fpdi->setSourceFile($salesOrder2);
                $template = $this->fpdi->importPage($i);
                $size = $this->fpdi->getTemplateSize($template);
                $this->fpdi->AddPage('P', array(150, 100));
                $this->fpdi->useTemplate($template, 0.5, 4, 97, 140);
                // $x= 10; $y =10;


                $this->fpdi->write1DBarcode($accountNumber.'9000491451' . $manifestId, 'C128', 52, 10, '', 8, 4, $style, '');
                $style1 = array(
                    'position' => '',
                    'align' => '',
                    'stretch' => false,
                    'fitwidth' => true,
                    'border' => false,
                    'hpadding' => '0',
                    'vpadding' => '0',
                    'fgcolor' => array(0, 0, 0),
                    'bgcolor' => false, //array(255,255,255),
                    'text' => false,
                    'font' => 'helvetica',
                    'fontsize' => 8,
                    'stretchtext' => 4
                );

                $this->fpdi->write1DBarcode($firstNumber, 'C128', 6, 60, '', 8, 0.17, $style1, '');
                $this->fpdi->Text(6, 70, $firstNumber);
                $this->fpdi->write1DBarcode($secondNumber, 'C128', 40, 60, '', 8, 0.17, $style1, '');
                $this->fpdi->Text(40, 70, $secondNumber);
                $this->fpdi->Text(75, 70, $totalNumber);
            }
        }
        return true;
    }

    public function preAdvice($consignment) {

    }

    private function lowertoupper($str) {
        $returnword = '';
        $i = 0;
        $strlen = strlen($str);

        for ($i = 0; $i < $strlen; $i++) {
            $numAscii = ord($str[$i]);
            if ($numAscii >= 97 && $numAscii <= 124) {
                $letterAscii = $numAscii - 32;
                $returnword .= chr($letterAscii);
            } else {
                $returnword .= $str[$i];
            }
        }

        return $returnword;
    }

    private function luhn_process($number) {
        // Set the string length and parity
        $number_length = strlen($number);
        $parity = $number_length % 2;

        // Loop through each digit and do the maths
        $total = 0;
        $number = strrev($number);
        for ($i = 0; $i < $number_length; $i++) {
            $digit = $number[$i];
            $digit = $this->codepoint($digit);

            // Multiply alternate digits by two
            if ($i % 2 == 0) {
                $digit *= 2;
                $digit = dechex($digit);
                // If the sum is two digits, add them together (in effect)
                if ($digit > 9) {
                    $digit -= 9;
                } else
                    if (!(is_numeric($digit))) {
                        $digit = $number[$i];
                        if (!(is_numeric($digit))) {
                            $digit = $this->codepoint($digit);
                        }
                    }
            }
            // Total up the digits
            $total += $digit;
        }
        return $total;
    }

    private function luhn_checkdigit($total) {
        while (!($total % 16 == 0)) {
            $total = $total + 1;
        }
        return $total;
    }

    private function codepoint($digit) {
        if ($digit == 'A')
            $digit = '10';
        elseif ($digit == 'B')
            $digit = '11';
        elseif ($digit == 'C')
            $digit = '12';
        elseif ($digit == 'D')
            $digit = '13';
        elseif ($digit == 'E')
            $digit = '14';
        elseif ($digit == 'F')
            $digit = '15';

        return $digit;
    }

    private function addCautionLabel($cautiontypeimage, $page_size) {
        $this->pdf->SetPrintFooter(false);
        $this->pdf->SetFooterMargin(0);
        $this->pdf->SetAutoPageBreak(false, 0);


        $this->pdf->AddPage("P", $page_size);
        $image = realpath("../images/" . $cautiontypeimage);
        $this->pdf->image($image, 1, 30, 100);
    }

    public function recycledShipment($consignment) {
        $output["STATUS"] = "SUCCESS";
        return $output;
    }

    public function setWarehouseId($warehouseId){
        $this->warehouseId = $warehouseId;
    }
    public function reconciliation_data($headingArr,$carrierId,$relPath,$new_csv_file_created,$filePath,$batchNumber) {
        /* First save bag data into DB then check if bag's parcel count is less then 50 then call internal function to save that
         parcels data into recon table other vise return message that parcels count is more then 50
        */
        $carrierObj = new Carrier($carrierId);
        $carrierName = str_replace(" ","_",$carrierObj->getCarrier());
        $output = [];
        $row = 1;
        $invoiceNumber = "";
        $invoiceType = "";
        $totalNumberOfPieces = 0;
        $totalWeight = 0;
        $totalAmount = 0;
        if (($handle = fopen($relPath, "r")) !== FALSE) {
            while (($data = fgetcsv($handle,0,"|")) !== FALSE) {
                if($row > 1 && empty($invoiceNumber)) {
                    $invoiceNumber = $data[2];
                    $invoiceType = $data[1];
                    $accountNumber = $data[3];
                    $invoiceDate = $data[12];
                }
                if($row > 2) {
                    // save to DB
                    $reconciliationBagData = New ReconciliationBagData();
                    $reconciliationBagData->setInvoiceNumber($invoiceNumber);
                    $reconciliationBagData->setBagNumber($data[23]);
                    $reconciliationBagData->setService($data[28]);
                    $reconciliationBagData->setWeight($data[30]);
                    $reconciliationBagData->setNumberOfPiece($data[29]);
                    $reconciliationBagData->setMatchedPiece("0.00");
                    $reconciliationBagData->setMatchedWeight("0.00");
                    $reconciliationBagData->setNumberOfParcelStatus("less");
                    $reconciliationBagData->setPoster($data[25]);
                    $reconciliationBagData->setPosterDate($data[24]);
                    $reconciliationBagData->setNetValue($data[32]);
                    $reconciliationBagData->setVatCode($data[33]);
                    $reconciliationBagData->setInvoiceType($invoiceType);
                    $reconciliationBagData->setServiceType($data[26]);
                    $reconciliationBagData->setAccountNumber($accountNumber);
                    $reconciliationBagData->setInvoiceDate($invoiceDate);
                    $reconciliationBagData->setWeightStatus("less");
                    $reconciliationBagData->setParcelStatus("success");
                    $reconciliationBagData->setStatus("error");
                    $reconciliationBagData->setMessage("");
                    $reconciliationBagData->save();
                    $totalNumberOfPieces += $data[29];
                    $totalWeight += $data[30];
                    $totalAmount += $data[32];
                }
                $row++;
            }
        }
        // Update supplier invoices table with latest data
        $upplierInvoicesFilter = New SupplierInvoicesFilter();
        $upplierInvoicesFilter->where(['invoice_number'=>$invoiceNumber]);
        $upplierInvoicesFilter->set(["total_weight"=>$totalWeight,"total_pieces"=>$totalNumberOfPieces,"total_amount"=>$totalAmount]);
        $upplierInvoicesFilter->update();

//        if($totalNumberOfPieces > 50){
//            $output['data'] = 50;
//        }else{
            $output = self::reconciliation_bag_data($headingArr,$invoiceNumber,$carrierName,$new_csv_file_created,$filePath,$batchNumber);
//        }
        return $output;
    }
    public function getSummeryData($relPath){
        $returnArr = [];
        $row = 1;
        $isInvoiceType = false;
        $isDataSet = true;
        if (($handle = fopen($relPath, "r")) !== FALSE) {
            while (($data = fgetcsv($handle,0,"|")) !== FALSE) {
                if($row > 1) {
                    if($data['1'] == 'Invoice' || $isInvoiceType == true){
                        if($isDataSet){
                            $isInvoiceType = true;
                            $returnArr['account_number'] = $data[3];
                            $returnArr['invoice_number'] = $data[2];
                            $returnArr['agent_reference_number'] = "";
                            $returnArr['collection_date'] = $data[7];
                            $isDataSet = false;
                        }
                    }
                }
                if($isInvoiceType){
                    break;
                }
                $row++;
            }
        }
        return $returnArr;
    }
    public function reconciliation_bag_data($headingArr,$invoiceNumber,$carrierName,$new_csv_file_created,$filePath,$batchNumber) {
        $user = SessionManager::getUser();
        $userId = $user->getId();
        $output = [];
        $headingArr[] ='status';
        $headingArr[] ='bag_number';
        $headingArr[] ='message';
        $comaSeptHeading = implode(",",$headingArr);
        $tableColumn = rtrim($comaSeptHeading,',');
        $csvStr = $tableColumn;
        $csvStr .= "\r\n";
        $templateCheck = 1;
        $checkExist = 0;
        $output['new_invoice_save'] = 0;
        $output['total_weight'] = 0;
        $output['total_pieces'] = 0;
        $output['total_amount'] = 0;
        $output['all_success'] = 1;
        $serviceDataArr = array("STRYMRM1L"=>"RM24",
            "STRYMRM1P"=>"RM24",
            "STRYMRM1E"=>"RM24",
            "STRYMRM2L"=>"RM48",
            "STRYMRM2P"=>"RM48",
            "STRYMRM2E"=>"RM48",
            "STRYM0TP2"=>"ROYAL MAIL TRACKED 48"
        );

        if (!empty($invoiceNumber)) {
            // Get data from Db against invoice number
            $reconciliationBagDataFilter = New ReconciliationBagDataFilter();
            $reconciliationBagDataFilter->addFieldFilter("    invoice_number",$invoiceNumber);
            $reconciliationBagDataObj = $reconciliationBagDataFilter->getList();
            if(count($reconciliationBagDataObj) > 0){
                $newFileName = strtolower($carrierName)."_" . $batchNumber . ".csv";
                $dateFolder = date('Y-m-d');
                $output['date_folder'] = $dateFolder;
                foreach ($reconciliationBagDataObj as $reconciliationBagDataArr) {
                    $bagNumber = $reconciliationBagDataArr->getBagNumber();
                    $bagWeight = $reconciliationBagDataArr->getWeight();
                    $number_of_pieces = $reconciliationBagDataArr->getNumberOfPiece();
                    $suplierServiceName = $reconciliationBagDataArr->getService();
                    $invoiceType = $reconciliationBagDataArr->getInvoiceType();
                    $bagTotalValue = $reconciliationBagDataArr->getNetValue();
                    $suplierTotalAmount = $reconciliationBagDataArr->getNetValue();
                    $suplierServiceType = $reconciliationBagDataArr->getServiceType();
                    $invoice_number = $reconciliationBagDataArr->getInvoiceNumber();
                    $account_number = $reconciliationBagDataArr->getAccountNumber();
                    $collection_date = $reconciliationBagDataArr->getInvoiceDate();
                    $agent_reference_number = "";

                    // Now implement the old logic
                    $firstSevice = "";
                    $secondSevice = "";
                    if($suplierServiceType == "1st & 2nd"){
                        $suplierServiceName = explode("/",$suplierServiceName);
                        $firstSevice = $suplierServiceName[0];
                        $secondSevice = $suplierServiceName[1];
                        $secondSevice = "RM".str_replace("No Barcode","",$secondSevice);
                    }
                    if($suplierServiceName == "ROYAL MAIL TRACKED 48 (HV)"){
                        $suplierServiceName = "ROYAL MAIL TRACKED 48";
                    }
                    if(!empty($bagNumber)){
                        // Check if bag exsist
                        $baggingFilter = new BaggingFilter();
                        $baggingFilter->addFieldFilter("    bagnumber", $bagNumber);
                        $bagObj = $baggingFilter->getColumnList("id,weight");
                        $bagCount = count($bagObj);
                        $bagIsVatAble = false;
                        if ($bagCount > 0) {
                            if($invoiceType == "T"){
                                $bagIsVatAble = true;
                            }
                            $bagParcelNumberStatus = 'match';
                            $bagParcelWeightStatus = 'match';
                            $bagId = $bagObj[0]->getId();
                            $bagDbWeight = $bagObj[0]->getWeight();
                            // Check parcel from bag
                            $parcelBagingMapping = new ParcelBaggingMappingFilter();
                            $parcelBagingMapping->addFieldFilter("    bag_id", $bagId);
                            $parcelBagingMapping->addGroupBy("parcel_id");
                            $parcelBagingMappingObj = $parcelBagingMapping->getColumnList("*");
                            $bagParcelCount = count($parcelBagingMappingObj);
                            $bagStatusMessage = '';
                            if ($bagParcelCount > $number_of_pieces) {
                                $bagParcelNumberStatus = 'more';
                                $bagStatus = "error";
                                $bagStatusMessage .= 'Bag and CSV parcel count doest not match. System parcel count is greater than CSV ';
                            } else if ($bagParcelCount < $number_of_pieces) {
                                $bagParcelNumberStatus = 'less';
                                $bagStatus = "error";
                                $bagStatusMessage .= 'Bag and CSV parcel count doest not match. System parcel count is less than CSV ';
                            }
                            if($bagDbWeight > $bagWeight){
                                $bagParcelWeightStatus = "more";
                                $bagStatus = "error";
                                $bagStatusMessage .= 'Bag and CSV parcel weight doest not match. System parcel weight is greater than CSV ';
                            }else if ($bagParcelCount < $number_of_pieces) {
                                $bagParcelWeightStatus = 'less';
                                $bagStatus = "error";
                                $bagStatusMessage .= 'Bag and CSV parcel weight doest not match. System parcel weight is less than CSV ';
                            }
                            if ($bagParcelCount > 0) {
                                $parcelCount = 1;
                                $parcelStatusMessage = "";
                                $parcelStatus = "success";
                                $parcelVat = 0.00;
                                foreach ($parcelBagingMappingObj as $bagParcel) {
                                    $basic_charges = formatNumber($bagTotalValue/$number_of_pieces);
                                    // If vat applied then calculate 0.20
                                    if($bagIsVatAble){
                                        $parcelVat = formatNumber($basic_charges*0.20);
                                    }
                                    $total_amount = $basic_charges + $parcelVat;
                                    // foreach loop varibal and CSV count
                                    if($parcelCount > $number_of_pieces){
                                        if ($bagParcelCount > $number_of_pieces) {
                                            $parcelStatus = "error";
                                            $basic_charges = 0.00;
                                            $parcelStatusMessage = 'Bag and CSV parcel count doest not match. System parcel count is greater than CSV ';
                                        } else if ($bagParcelCount < $parcelCount) {
                                            $parcelStatus = "error";
                                            $basic_charges = 0.00;
                                            $parcelStatusMessage = 'Bag and CSV parcel count doest not match. System parcel count is less than CSV ';
                                        }
                                    }
                                    $parcelCount++;
                                    $parcelIdArr[] = $bagParcel->getParcelId();
                                    // From parcel weight, and consignemnt id for join
                                    // From consignment getCountryId, getAwb , getHawb
                                    $parcelFilter = new ParcelFilter();
                                    $parcelDbData = $parcelFilter->getParcelSerivcesDataById($bagParcel->getParcelId());
                                    $delivery_country = "";
                                    $awb = "";
                                    $hawb = "";
                                    $parcelServiceName = "";
                                    $parcelServiceCarrierCode = "";
                                    $parcelServiceCode = "";
                                    $weight = "";
                                    if(count($parcelDbData) > 0 && !is_string($parcelDbData[0])){
                                        if(!empty($parcelDbData[0]->getConsignmentCountryIso())){
                                            $delivery_country = $parcelDbData[0]->getConsignmentCountryIso();
                                        }
                                        if(!empty($parcelDbData[0]->getConsignmentAwb())){
                                            $awb = $parcelDbData[0]->getConsignmentAwb();
                                        }
                                        if(!empty($parcelDbData[0]->getConsignmentHawb())){
                                            $hawb = $parcelDbData[0]->getConsignmentHawb();
                                        }
                                        if(!empty($parcelDbData[0]->getParcelServiceId())){
                                            $parcelServiceId = $parcelDbData[0]->getParcelServiceId();
                                        }
                                        if(!empty($parcelDbData[0]->getParcelServiceName())){
                                            $parcelServiceName = $parcelDbData[0]->getParcelServiceName();
                                        }
                                        if(!empty($parcelDbData[0]->getParcelServiceCarrierCode())){
                                            $parcelServiceCarrierCode = $parcelDbData[0]->getParcelServiceCarrierCode();
                                        }
                                        if(!empty($parcelDbData[0]->getParcelServiceCode())){
                                            $parcelServiceCode = $parcelDbData[0]->getParcelServiceCode();
                                        }
                                        if(!empty($parcelDbData[0]->getParcelServiceCode())){
                                            $parcelServiceCode = $parcelDbData[0]->getParcelServiceCode();
                                        }
                                        if(!empty($parcelDbData[0]->getWeight())){
                                            $weight = $parcelDbData[0]->getWeight();
                                        }
                                    }
                                    $mawb = "";

                                    $servicesNotFound = true;
                                    $finalServiceName = "";
                                    $service_code = "";
                                    if($suplierServiceType == "1st & 2nd" && ($firstSevice == "RM24" || $secondSevice == "RM48") ){
                                        if (array_key_exists($parcelServiceCode, $serviceDataArr )) {
                                            $finalServiceName = $parcelServiceName;
                                            $service_code = $parcelServiceCode;
                                        }
                                    }else{
                                        if($suplierServiceName == "ROYAL MAIL TRACKED 48"){
                                            if (array_key_exists($parcelServiceName, $serviceDataArr ) || array_key_exists($parcelServiceCarrierCode, $serviceDataArr ) || array_key_exists($parcelServiceCode, $serviceDataArr ) ) {
                                                $finalServiceName = $parcelServiceName;
                                                $service_code = $parcelServiceCode;
                                            }
                                        }
                                    }
                                    $serviceError = "";
                                    if(!empty($finalServiceName)){
                                        $service_name = $finalServiceName;
                                    }else{
                                        $service_name = $suplierServiceName;
                                        $serviceError = "Supplier service (".$suplierServiceName.") not matched with system name (".$parcelServiceName.")";
                                        $parcelStatusMessage = $serviceError;
                                        $parcelStatus = "error";
                                    }
//                                                        $basic_charges = formatNumber($dataNew[4]);
//                                                    $fuel_charges = formatNumber($dataNew[12]);
                                    if($parcelStatus == "error") {
                                        $output['all_success'] = 0;
                                    }
                                    $fuel_charges = "";
                                    $notes = "";
                                    $output['invoice_number'] = $invoice_number;
                                    $output['invoice_date'] = $collection_date;
                                    $length = "0.00";
                                    $height = "0.00";
                                    $width = "0.00";
                                    $volWeight = "0.000";

                                    $weight = formatNumber($weight, 3);
                                    if(!empty($awb) || !empty($hawb)){
                                        $dt = [
                                            'account_number' => $account_number,
                                            'invoice_number' => $invoice_number,
                                            'agent_reference_number' => $agent_reference_number,
                                            'collection_date' => $collection_date,
                                            'delivery_country' => $delivery_country,
                                            'mawb' => $mawb,
                                            'awb' => $awb,
                                            'hawb' => $hawb,
                                            'service_name' => $service_name,
                                            'service_code' => $service_code,
                                            'weight' => $weight,
                                            'vol_weight' => $volWeight,
                                            'length' => $length,
                                            'width' => $width,
                                            'height' => $height,
                                            'number_of_pieces' => "1",
                                            'basic_charges' => $basic_charges,
                                            'fuel_charges' => $fuel_charges,
                                            'additional_charges' => '',
                                            'vat' => $parcelVat,
                                            'total_amount' => $total_amount,
                                            'notes' => $notes,
                                            'status' => $parcelStatus,
                                            'bag_number' => $bagNumber,
                                            'message' => $parcelStatusMessage
                                        ];
                                        $dataArr[] = $dt;
                                        $comaSept = implode(",", $dt);
                                        $csvStr .= rtrim($comaSept, ',');
                                        $csvStr .= "\n";
                                        $vat = "";
                                    }
                                }
                            }
                        }
                        // Save Data into DB for Bag
                        else {
                            $bagParcelCount = 0;
                            $bagDbWeight = 0.00;
                            $bagParcelNumberStatus = 'less';
                            $bagParcelWeightStatus = 'less';
                            $bagStatus = "error";
                            $bagStatusMessage = 'Bag not found in system ';
                        }
                        $output['total_weight'] += $weight;
                        $output['total_pieces'] += $number_of_pieces;
                        $output['total_amount'] += $suplierTotalAmount;

                        $reconciliationBagData = New ReconciliationBagData($reconciliationBagDataArr->getId());
                        $reconciliationBagData->setMatchedPiece($bagParcelCount);
                        $reconciliationBagData->setMatchedWeight($bagDbWeight);
                        $reconciliationBagData->setNumberOfParcelStatus($bagParcelNumberStatus);
                        $reconciliationBagData->setWeightStatus($bagParcelWeightStatus);
                        $reconciliationBagData->setParcelStatus("success");
                        $reconciliationBagData->setStatus($bagStatus);
                        $reconciliationBagData->setMessage($bagStatusMessage);
                        $reconciliationBagData->save();
                    }
                }
                $myCsvFile = fopen($new_csv_file_created, "a") or die("Unable to open file!");
                fwrite($myCsvFile, $csvStr);
                fclose($myCsvFile);
                $dateNow = date('Y-m-d H:i:s');
                $load_data_sql = "LOAD DATA LOCAL INFILE '" . $new_csv_file_created . "' INTO TABLE `reconciliation_data`
                            FIELDS ENCLOSED BY '\"' 
                            TERMINATED BY ',' LINES TERMINATED BY '\n' IGNORE 1 LINES (
                                " . $tableColumn . "
                            ) 
                            SET  created_at='" . $dateNow . "', batch_number= '" . $batchNumber . "'";

                $res = DbAccess3::runQueryWithError($load_data_sql);
                if ($res === false) {
                    $error = DbAccess3::$dbError;
                    $output['status'] = 'error';
                    $output['message'] = $error[0];
                    // Delete from supllier invoice table and bagging table
                } else {
                    $sql = "SELECT * FROM reconciliation_data WHERE batch_number='" . $batchNumber . "' ";
                    $resultSql = DbAccess3::runQuery($sql);
                    $res = [];
                    while ($obj = mysqli_fetch_object($resultSql)) {
                        $res[] = $obj;
                    }
                    $output['status'] = 'success';
                    $output['batch_number'] = $batchNumber;
                    $output['template'] = $templateCheck;
                    $output['file_path'] = $filePath;
                    $output['data'] = $res;
                    // Update suplier invoice table
                    // Update supplier invoices table with latest data
                    $upplierInvoicesFilter = New SupplierInvoicesFilter();
                    $upplierInvoicesFilter->where(['invoice_number'=>$invoiceNumber]);
                    $upplierInvoicesFilter->set(["upload_file"=>$newFileName]);
                    $upplierInvoicesFilter->update();
                }
            }else{

            }
        } else {
            $output['status'] = 'error';
            $output['message'] = 'Invoice number not found in system';
        }
        return $output;
    }
}
