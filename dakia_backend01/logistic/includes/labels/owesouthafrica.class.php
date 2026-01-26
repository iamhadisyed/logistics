<?php
include_classes([
    'owesouthafricapostcode.class',
    'owesouthafricapostcodefilter.class',
    'owesouthafricaroutine.class',
    'owesouthafricaroutinefilter.class',
]);

class oweSouthAfrica implements CarrierService {

    private $pdf;
    private $serviceValues = null;
    private $constants = null;
    private $user = null;
    private $country = null;
    private $booking_file = null;
    private $record_array = null;

    public function __construct() {
        
    }

    public function validation(Consignment $consignment, Services $service, Country $country) {
        $this->isRemoteArea = 0;
        $returnOutput = array();
        
        $postcode = str_pad(str_replace("\r\n", '', trim($consignment->getPostcode())),4, 0,STR_PAD_LEFT );
        
        if(!preg_match("/^[0-9]{4}$/", $postcode)){
            $returnOutput[] = "South Africa postcode are four digits. Please check your postcode.";
        }
        else{
        $owesouthafricapostcode = new oweSouthAfricaPostcodeFilter();
        $owesouthafricapostcode->addFieldFilter("postcode", $postcode);
        $postcodelist = $owesouthafricapostcode->getList();

        if (count($postcodelist) > 0) {
            $zone = $postcodelist[0]->getZone();

            $mainOutlying = $postcodelist[0]->getMainOutlying();
            if ($zone != "") {
                $zonename = $mainOutlying;
                if ($mainOutlying == "O") {
                    $this->isRemoteArea = 1;
                }
            }
        } else {
            $zonename = "R";
            $this->isRemoteArea = 1;
        }
        $consignment->setRoutingCodeEur($zonename);

        }
        $carrierId = $service->getCarrierId();
        $carrierObject = new Carrier((int) $carrierId);
        /*
         * REMOTE AREA POSTCODE CHECK
         */
        $remoteAreaCheck = $this->remoteareas($consignment, $carrierObject, $sender);
        switch ($remoteAreaCheck) {
            case 'allowed':
                break;
            case 'not-allowed':
                $returnOutput[] = "Your postcode ( " . $consignment->getPostcode() . " ) is a remote area. Please contact to administrator to activate.";
                break;
        }
        return $returnOutput;
    }

    public function remoteareas($consignment, $carrierObject, $sender) {

        $serviceId = $consignment->getServiceId();
        $userId = $consignment->getUserId();

        $sql = "SELECT 
                id
            FROM
                user_services_routing
            WHERE 
                user_account_id in ( SELECT user_account_id FROM user WHERE id = '" . DbAccess3::escape($userId) . "'  ) 
                AND service_id = '" . DbAccess3::escape($serviceId) . "'
                AND is_remotearea = 1";

        $remoteareasUserCheck = Remoteareas::getRemoteareasListFromSql($sql);
        if (count($remoteareasUserCheck) > 0) {
            return 'allowed';
        } else {
            return 'not-allowed';
        }
    }

    public function isRemoteArea() {
        return $this->isRemoteArea;
    }

    public function label($consignment, $labelType = 'pdf', $size = '100x150') {
        $pdf = new PdfBase(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $this->pdf = $pdf;
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

            ++$parcel_idx;

            if ($labelType == 'pdf'){
                $this->labelPdf($consignment, $parcel_idx, $licence_plate, $parcel_count, $parcel);
            }                
            else
                $filename = $this->labelZpl($consignment, $parcel_idx, $licence_plate, $parcel_count, $parcel);
        }
        
        if ($labelType == 'pdf') {
            $filename = date('Y_m_d') . '/' . $consignment->getId() . ".pdf";
            $this->pdf->IncludeJS("print();");
            $this->pdf->Output("../_assets/pdf/" . $filename, "F");
        }


        $output['STATUS'] = 'SUCCESS';
        $output['LABEL'] = $filename;
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

    private function labelZpl(Consignment $consignment, $parcel_idx, $licence_plate, $parcel_count, $parcel) {
        

        $this->labelPdf($consignment, $parcel_idx, $licence_plate, $parcel_count, $parcel);

        $routingcode = $consignment->getRoutingCodeEur();
        if ($routingcode == "O")
            $zonename = "Outer";
        else if ($routingcode == "M")
            $zonename = "Main";
        else
            $zonename = "Regional";


        $zplFileText = "";
        $zplFileText .= "^XA" . "\r";

        //$zplFileText .= "^FO10,5^GFA,5336,4336,36,,:::::::::::::::::iQFC::::::::::::::::::::PFEL01hSFCOFP07hQFCMFER03hPFCLFET03hOFCKFEV07hNFCKF8W0hNFCJFCX01hMFCJFg07hLFCIF8gG0hLFCFFEV01K03hKFCFFES0NFC3hKFCFFEQ03OFC3hKFCFFEP07PFC3hKFCFFEO07QFC3hKFCFFEN03RFC3hKFCFFEM01SFC3hKFCFFEM0TFC3hKFCFFEL03TFC3hKFCFFEK01UFC3hKFCFFEK07UFC3hKFCFFEK0VFC3hKFCFFEJ03VFC3hKFCFFEJ0WFC3hKFCFFEI01WFC3hKFCFFEI07WFC3hKFCFFE001XFC3hKFCFFE003XFC3hKFCFFE007XFC3hKFCFFE01YFC3hKFCFFE03YFC3hKFCFFE07YFC3hKFCFFE1gFC3hHF0DFCFFE1gFC3hHF0D4CFFE3gFC3hHF0F9CFFE3gFC3NFE3gRF0F9CFFE3OF003JF803FC3NFC1gRF0FFCFFE3E03FE01F8I07FFEI0FC3NFC1gRF0FFCFFE3E01FE03FJ03FFCI07C3NFC1gRF0FFCFFE3E01FE01EJ01FF8I07C3NFC3gRF0FFCFFE3E01FE03EK0FFJ07C3hHF0FFCFFE3E01FE01EK07F00707C3hHF0FFCFFE3E03FE03E01F807E01FE7C3VF87JFC0KF8MF83IF0FFCFFE3E03FE03E01FE03E03FF7C3NFC3KFC00IFE001FF860FF807FC007FF0FFCFFE3E03FE03E01FE03E03IFC3NFC3KFI03FFCI07F800FE007F8001FF0FFCFFE3E03FE03E01FE01C07IFC3NFC3JFEI01FF8I03F800FC007EJ0FF0FFCFFE3E03FE03E03FF01C07IFC3NFC3JFCJ0FFJ01F800FI07CJ07F0FFCFFE3E03FE03E03FF01E03IFC3NFC3JF80FE07E03F81F80FF01FFC0FE07F0FFCFFE3E03FE03E03FF00E03IFC3NFC3JF83FF07C0FFE0F81FE07FF83FF03F0FFCFFE3E03FE03E01FF00E00IFC3NFC3JF07FF83C1FFE0F83FC0IF03FE01F0FFCFFE3E03FE03E01FF80E007FFC3NFC3JF07FF83C1IF0783FC1IF07F807F0FFCFFE3E03FE03E01FF80F003FFC3NFC3E01F0IFC183IF0783FC1IF07E00FF0FFCFFE3E03FE03E01FF80F800FFC3NFC3E01E0IFC183IF0787F83IF0FC03FF0FFCFFE3E03FE03E01FF80FC003FC3NFC3E01E0IFC183IF0787F83FFE0F00IF0FFCFFE3E03FE03E01FF80FE001FC3NFC3E01E0IFC183IF0787F83FFE0C03IF0FFCFFE3E03FE03E01FF80FFI0FC3NFC3IFE0IFC183IF0787F83FFE0C0FE1F0FFCFFE3E03FE03E01FF80FFC007C3NFC3IFE0IFC183IF0787FC3IF0C1FE0F0FFCFFE3E03FE03E01FF80FFE007C3NFC3IFE0IFC1C3IF0787FC1IF047FE1F0FFCFFE3E03FE03E01FF80IF803C3NFC3IFE0IFC3C1IF0787FC1IF07FFC1F0FFCFFE3E03FE03E01FF00IFC03C3NFC3IFE0IF83C1IF0787FC0IF03FF81F0FFCFFE3E03FE03E01FF00IFE03C3NFC3IFE0IF03E0FF90787FE07FF81FF83F0FFCFFE3E03FE03E01FF01IFE01C3NFC3IFE08FC07E03F10787FF00FFC0FE03F0FFCFFE3E01FE03E01FF01IFE01C3NFC3IFE08I0FFI018787FF8007CJ07F0FFCFFE3E01FE03E01FE01IFE01C3NFC3IFE08001FF80010787FFC007EJ0FF0FFCFFE3F01FE03E01FE03IFE03C3NFC3IFE08003FFC0010787FFE007F8001FF0FFCFFE3F01FE03E01FC03EFFE03C3NFC3IFE0C00JF0070787IF807FC007FF0FFCFFE1F00FE03E03F803E3FC03C3SFE0FC7JFE3SFC7LFCFFE1F003003E01F007E0F003C3SFE0gPFCFFE1F8J03EK0FEJ07C3SFE0gPFCFFE1F8J01EK0FEJ0FC3SFE0gPFCFFE1FCJ03EJ01FEJ0FC3SFE0gPFCIF1FEJ07EJ07FEI03FC3SFE0gPFCIF1FF8003FEJ0IF8007FC7SFE0gPFCIF1IF83FFE01LF83FF87SFE0gPFCIF0MFE01PF87SFE0gPFC:IF87LFE01PF0hLFCIF87LFE03PF0hLFC:IFC3LFE01OFE1hLFC:IFE1LFE01OFC3hLFCIFE0LFE03OF83hLFCJF0LFE01OF87hLFCJF07KFE01OF0hMFCJF83KFE01NFE0hMFCJFC1KFE03NFC1hMFCJFE0KFE01NF83hMFCKF07JFE01NF07hMFCKF03JFE03MFE0hNFCKFC0TF81hNFCKFE07SF03hNFCLF01RFC07hNFCLFC07QF81hOFCLFE03PFE03hOFCMF80PF80hPFCMFC03NFE03hPFCNF00NF807hPFCNFC03LFE01hQFCOF00LF807hQFCOFC03JFE01hRFCPF00JF807hRFCPFC03FFC01hSFCQF007F007hSFCQFC01C01hTFCRFJ07JFCDhNFCRFE003KFCDhNFCSF80MF7hNFCSFE3MF7hNFCiQFC:::::::::::::::::::,::::::::^FS" . "\r";

        $zplFileText .= "^FO380,30^GFA,3456,3456,36,,::::::::::::::::P01IF,P0JFE,O03E0C7F8,O070070FC,N01C00183F,N01J0C1F8,S063FC,S0373E,S03C1F,S07C0F,O04001EC078,O0400F8603C,O0C03C0603C007FC007E07EI07C00FF03F80FE01FF001FFC007F803FFC,O0C1E0030FE01IF00FF0FF1IFE00FF87FE1FF07FFC07IFC0FFC07IFE,O0DFI031FE03IF80FF0FF1IFE00FF87FE1FF0IFE07IFE0FFC07JF,K0E01FF8I01F8E07IFC0FF0FF0IFE00FF87FE1FF1JF03JF07FC07JF8,J01JF8J01F0F0JFE0FF8FF0IFE00FF87FE3FF3JF83JF87FC07JFC,O08J07C0F0JFE0FF8FF0IFE00FF87FE3FF3JF83JF87FC07JFE,O08I01FC071KF0FF8FF0IFE00FF87FE3FE7JF83JFC7FC07JFE,O08I07CC071FF9FF0FFCFF0IFE007F8FFE3FE7FCFFC3FEFFC7FC07FEFFE,O08003F0C0F1FF1FF0FFCFF0FF8I07F8FFE3FE7FCFFC3FE7FC7FC07FE7FE,O0803F8043F1FF1FF0FFCFF0FF8I07F8FFE3FE7FC7FC3FE7FC7FC07FE7FE,J06J0CFFC0067F1FF1FF0FFEFF0FF8I07FCFFE3FE7FC7FC3FE7FC7FC07FE7FE,J07LFEI07E71FF1FF0FFEFF0FF8I07FCFFE3FE7FC7FC3FE7FC7FC07FE7FE,K01JF8J07871FF1FF0FFEFF0FF8I07FCFFE7FE7FC7FC3FE7FC7FC07FE7FE,O0CJ01F071FF1FF0KF0FF8I07FCFFE7FE7FC7FC3FE7FC7FC07FE7FE,O04J07E071FF1FF0KF0IFC007FCIF7FC7FC7FC3FE7FC7FC07FE7FE,O06I03F6071FF1FF0KF0IFE007FDIF7FC7FC7FC3JF87FC07FE7FE,O06003F860F1FF1FF0KF0IFE003FDIF7FC7FC7FC3JF87FC07FE7FE,O0601FE061F1FF1FF0KF0IFE003FDIF7FC7FC7FC3IFE07FC07FE7FE,K0F8007FFE0067E1FF1FF0KF0IFE003FDIF7FC7FC7FC3IFE07FC07FE7FE,J01LFCI07FE1FF1FF0KF0IFE003FDIF7FC7FC7FC3JF07FC07FE7FE,M03FB8J079C1FF1FF0KF0IFE003FDIF7FC7FC7FC3JF87FC07FE7FE,O018I01F1C1FF1FF0KF0FF8I03IFBIFC7FC7FC3FE7FC7FC07FE7FE,P08I07C381FF1FF0KF0FF8I03IFBIF87FC7FC3FE7FC7FC07FE7FE,P04003EC381FF1FF0KF0FF8I03IFBIF87FC7FC3FE7FC7FC07FE7FE,P0601F8C701FF1FF0FF7FF0FF8I01IF9IF87FC7FC3FE7FC7FC07FE7FE,P037FC08E01FF1FF0FF7FF0FF8I01IF9IF87FC7FC3FE7FC7FC07FE7FE,N0JF8009E01FF1FF0FF7FF0FF8I01IF9IF87FC7FC3FE7FC7FC07FE7FE,N0F00C0013C01FF1FF0FF7FF0FF8I01IF1IF87FCFFC3FE7FC7FC07FE7FE,Q060017801FF1FF0FF3FF0FF8I01IF1IF87FCFFC3FE7FC7FC07FE7FE,Q03003E001FF3FF0FF3FF0JF001IF1IF87FCFFC3FE7FC7FFE7FEFFE,R0807C001KF0FF3FF0JF001IF0IF07JF83FE7FC7FFE7JFE,T0F8I0JFE0FF1FF0JF001IF0IF03JF83FE7FC7FFE7JFE,S07EJ0JFE0FF1FF0JFI0IF0IF03JF03FE7FC7FFE7JFC,Q01FFK07IFC0FF1FF0JFI0IF0IF01JF03FE7FC7FFE7JFC,Q01M03IF80FF0FF1JFI0FFE0IF00IFE03FE7FC7FFE7JF8,Y01IF00FF0FF1JFI0FFE07FF007FF803FE7FC7FFE3IFE,g0FFC00FE07EW01FE,,:gU071O01L04O030C,gU071O01X04,gU03P01X04,gU0180093424809240600CI064A192B243,gU01C649B03692505A590D01164C19B36498,gV0C469B02236181A580DC3124819A3459,gV0C469B022D61C59180CE3164819A3458,gU01C66DB036961BDBD80D61B66C19B36CF8,gU03C2CDB034DB1B4FEE0D80A36C1B332EF,hH02L026O018,hH07L03EO018,,::::::::::::::::^FS" . "\r";

        $zplFileText .= "^FO10,10^GB780,940,3^FS" . "\r";
        $zplFileText .= "^FO10,130^GB780,1,3^FS" . "\r";
        $zplFileText .= "^FO300,10^GB1,120,3^FS" . "\r";
        //$zplFileText .= "^CF0,60" . "\r";
        //$zplFileText .= "^FO380,50^FDOne World Express.^FS" . "\r";

        $zplFileText .= "^CFA,40";
        $zplFileText .= "^FO90,60^FD" . $zonename . " ^FS";
        $zplFileText .= "^FX hawb barcode" . "\r";
        $zplFileText .= "^FO90,160^BY3^BCN,140,Y,N,N" . "\r";
        $zplFileText .= "^FD" . $licence_plate . "^FS" . "\r";
        $zplFileText .= "^FO10,340^GB780,1,3^FS" . "\r";
        $zplFileText .= "^CFA,30" . "\r";
        $zplFileText .= "^FO50,360^FDNotes: " . $consignment->getNotes() . "^FS" . "\r";
        $zplFileText .= "^FO50,400^FDPieces: " . $parcel_idx . "/" . $parcel_count . "^FS" . "\r";
        $zplFileText .= "^FO10,440^GB780,1,3^FS" . "\r";
        $zplFileText .= "^FX Second section with recipient address and permit information." . "\r";
        $zplFileText .= "^CF0,50" . "\r";
        $zplFileText .= "^FO270,470^FDDelivery Details:^FS" . "\r";
        $zplFileText .= "^CFA,20" . "\r";


        $zplFileText .= "^CFA,20" . "\r";
        $zplFileText .= "^FO50,525^FD" . $consignment->getContact() . "^FS" . "\r";
        $zplFileText .= "^FO50,550^FD" . $consignment->getCompany() . "^FS" . "\r";
        $zplFileText .= "^FO50,575^FD" . $consignment->getAddressLine1() . "^FS" . "\r";
        $zplFileText .= "^FO50,600^FD" . $consignment->getAddressLine2() . "^FS" . "\r";
        $zplFileText .= "^FO50,625^FD" . $consignment->getAddressLine3() . "^FS" . "\r";
        $zplFileText .= "^FO50,650^FD" . $consignment->getCity() . "^FS" . "\r";
        $zplFileText .= "^FO50,675^FD" . $consignment->getPostcode() . "^FS" . "\r";
        $zplFileText .= "^CFA,30" . "\r";
        $zplFileText .= "^FO50,700^FD" . $this->country->getName() . "^FS" . "\r";
        $zplFileText .= "^FO420,700^FDTel:" . $consignment->getTelephone() . "^FS" . "\r";
        $zplFileText .= "^CFA,15" . "\r";


        $zplFileText .= "^FO10,740^GB780,1,3^FS" . "\r";
        $zplFileText .= "^FO90,750^BY3^BCN,80,Y,N,N" . "\r";
        $zplFileText .= "^FD" . $consignment->getHawb() . "^FS" . "\r";

        $zplFileText .= "^FO10,860^GB780,1,3^FS" . "\r";
        $zplFileText .= "^FO300,860^GB1,90,3^FS" . "\r";
        $zplFileText .= "^FX Top section with company logo, name and address." . "\r";
        $zplFileText .= "^FX Fourth section (the two boxes on the bottom)." . "\r";

        $zplFileText .= "^CF0,80" . "\r";
        $zplFileText .= "^FO120,880^FD" . $this->country->getIso() . "^FS" . "\r";
        $zplFileText .= "^CF0,20" . "\r";
        $zplFileText .= "^FO320,870^FDif undelivered return to:^FS" . "\r";
        $zplFileText .= "^FO330,890^FDOne World Express IncOne World House,^FS" . "\r";
        $zplFileText .= "^FO330,910^FDPump Ln, Hayes, Greater London UB3 3NB^FS" . "\r";
        $zplFileText .= "^XZ";


        if ($zplFileText == "")
            return false;

        $id_num = $consignment->getId();
        $file_name = date('Y_m_d') . '/' . $id_num . ".zpl";
        $file_path = "../_assets/pdf/" . $file_name;
        $file_handle = fopen($file_path, 'w+');
        if ($file_handle == null) {
            return false;
        }


        // write label text into file
        fwrite($file_handle, $zplFileText);
        fclose($file_handle);
        return $file_name;
    }

    private function labelPdf(Consignment $consignment, $parcel_idx, $licence_plate, $parcel_count, $parcel) {

        $routingcode = $consignment->getRoutingCodeEur();
        if ($routingcode == "O")
            $zonename = "Outer";
        else if ($routingcode == "M")
            $zonename = "Main";
        else
            $zonename = "Regional";

        

        $this->pdf->SetPrintFooter(false);
        $this->pdf->SetFooterMargin(0);
        $this->pdf->SetAutoPageBreak(false, 0);
        $page_size = array(100, 150);
        $this->pdf->AddPage("P", $page_size);

        $this->pdf->line(3, 51, 98, 51);
        $this->pdf->line(3, 3, 98, 3);
        $this->pdf->line(98, 3, 98, 23.8);
        $this->pdf->line(3, 3, 3, 30);
        $this->pdf->line(35, 3, 35, 23.8);
        $this->pdf->Line(46, 51, 46, 60);
        $this->pdf->Line(76, 51, 76, 60);
        $this->pdf->line(3, 24, 98, 24);
        $this->pdf->line(3, 24, 3, 115);
        $this->pdf->line(3, 115, 98, 115);
        $this->pdf->line(98, 24, 98, 115);
        $this->pdf->line(3, 60, 98, 60);
        $this->pdf->line(3, 70, 3, 145);
        $this->pdf->line(98, 70, 98, 145);
        $this->pdf->line(3, 133, 98, 133);
        $this->pdf->line(3, 113, 3, 146);
        $this->pdf->line(3, 146, 98, 146);
        $this->pdf->line(98, 113, 98, 146);
        $this->pdf->line(30, 133, 30, 146);


        $logo = User::getUserCompanyImages(false, $consignment->getUserId());

        if (file_exists($logo))
            $this->pdf->image($logo, 40, 4, 49, 15);
        $this->pdf->setFont("helvetica", "L", 9);
        $this->pdf->Text(45, 18.5, "www.oneworldexpress.com");



        $this->pdf->setFont("helvetica", "b", 13);

        $this->pdf->Text(6.5, 10, $zonename);


        $this->pdf->setFont("helvetica", "b", 10);
        $this->pdf->Text(6, 53, "Ids: " . $consignment->getNotes());

        $this->pdf->setFont("helvetica", "b", 9);
        $this->pdf->Text(46, 51, "Parcel Wgt: " . number_format($parcel->getWeight(),2));
        $this->pdf->Text(46, 56, "Total Wgt: " . number_format($consignment->getWeight(),2));


        $this->pdf->Text(5, 53, $account);
        $this->pdf->Text(78, 51, "No. of Pcs");
        $this->pdf->Text(83, 55, $parcel_idx ." / ". $parcel_count);


        $this->pdf->Text(5, 62, "Delivery Details:");

        $this->pdf->Text(12, 137, "ZA");
        $pieces = str_pad($parcel_idx + 1, 3, "0", STR_PAD_LEFT);


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


        $this->pdf->write1DBarcode($licence_plate, 'C128', 7, 18, '', 40, 2, $style, '');

        $this->pdf->write1DBarcode($consignment->getHawb(), 'C128', 15, 115, '', 15, 0.5, $style, '');

        $company = $consignment->getCompany();
        $address1 = $consignment->getAddressLine1();
        $address2 = $consignment->getAddressLine2();
        $address3 = $consignment->getAddressLine3();
        $this->pdf->setFont("helvetica", "", 10);
        $this->pdf->Text(5, 67, $consignment->getHawb());
        $this->pdf->Text(5, 71, $company);
        $this->pdf->Text(5, 75, $consignment->getContact());
        $this->pdf->Text(5, 79, $address1);
        $this->pdf->Text(5, 83, $address2);
        $this->pdf->Text(5, 87, $address3);
        $this->pdf->Text(5, 91, $consignment->getCity());
        $this->pdf->Text(5, 95, $consignment->getPostcode());
        $this->pdf->Text(55, 99, "Tel: " . $consignment->getTelephone());
        //$fontname = $this->pdf->addTTFfont('../assets/fonts/simhei.ttf', 'TrueTypeUnicode', '', 32);
        $this->pdf->SetFont('helvetica', '', 8);
        $this->pdf->Text(5, 105, $consignment->getNotes());


        $this->pdf->setFont("helvetica", "B", 11);
        $this->pdf->Text(5, 99, $this->country->getName());
        $this->pdf->setFont("helvetica", "L", 11);
        $this->pdf->Text(55, 95, $consignment->getReference());

        $this->pdf->setFont("helvetica", "B", 8);
        $this->pdf->Text(34, 133, "if undelivered return to:");
        $this->pdf->Text(34, 138, "One World Express IncOne World House");
        $this->pdf->Text(34, 141, "Pump Ln, Hayes, Greater London UB3 3NB");
        $this->pdf->setFont("helvetica", "L", 8);
        $this->pdf->Text(34, 136, $this->constants['DAC_SHIPPER_ADDRESS_LINE1'] . " " . $this->constants['DAC_SHIPPER_ADDRESS_LINE2']);
        $this->pdf->Text(34, 139, $this->constants['DAC_SHIPPER_ADDRESS_LINE3'] . " " . $this->constants['DAC_SHIPPER_CITY'] . " " . $this->constants['DAC_SHIPPER_POSTCODE']);

        $shipper_country = $this->constants['DAC_SHIPPER_COUNTRY'];
        if ((int) $shipper_country > 0) {
            $shipperCountry = new Country($shipper_country);
            $sCountry = $shipperCountry->getName();
            $sIso = $shipperCountry->getIso();
        } else {
            $sCountry = $shipper_country;
        }
        $this->pdf->Text(34, 142, $sCountry . " " . $sIso);

        //    $this->pdf->IncludeJS("print();");
       

        //$id_num = $consignment->getId();
        //$file_name = date('Y_m_d') . '/' . $id_num . ".pdf";
        return ;
    }

    public function recycledShipment($consignment) {
        $output["STATUS"] = "SUCCESS";
        return $output;
    }

}
