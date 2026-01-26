<?php
include_classes([
    'carrierdatafilelog.class',
    'carrierdatafilelogfilter.class' 
    ]);
include_classes([
    'fpdi',
    ], '3rdparty/fpdi');

 include_classes([
            'tcpdf',
            ], '3rdparty/tcpdf');
        
 include_classes([
            'pdfmerger'   ], 'labels');

class Eamglobal implements CarrierService {

    private $pdf;
    private $user = null;
    private $serviceValues = null;
    private $agentValues = null;
    private $constants = null;
    private $countryName = null;
    private $consignment = null;
    private $record_idx = 0;
    private $link_file = null;
    private $routing_code = null;
    private $isRemoteArea = 0;

    public function __construct() {
        
    }
    
    public function validation(Consignment $consignment, Services $service, Country $country) {
    }

    public function remoteareas($consignment, $carrierObject, $sender) {        
    }

    public function isRemoteArea() {       
    }

    public function label($consignment, $labelType = 'pdf', $size = '100x150') {
        $labelType = (in_array($labelType, ['pdf', 'zpl']) ? $labelType : 'pdf');        
        $output = array();
        $this->serviceValues = new Services($consignment->getServiceId());

        $serviceAgentConstantFilter = new ServiceConstantValueFilter();
        $serviceAgentConstantFilter->addFilter("service_id = '" . $consignment->getServiceId() . "' AND agent_id = '" . $consignment->getAgentId() . "' ");
        $serviceAgentConstant = $serviceAgentConstantFilter->getConstantList("c.constant 'constant_name', scv.constant_value 'constant_value'");
        if (count($serviceAgentConstant) > 0) {
            foreach ($serviceAgentConstant as $serviceAgentConstantData) {
                $this->constants[$serviceAgentConstantData->getConstantName()] = $serviceAgentConstantData->getConstantValue();
            }
        }

        $parcel_list = $consignment->getParcels();
        $pdf = new PdfBase(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $this->pdf = $pdf;
        // Generate label for each parecel
        foreach ($parcel_list as $parcel) 
        {
            $labelsize = explode("x", strtolower(trim($size)));
            $width = trim($labelsize[0]);
            $height = trim($labelsize[1]);
            $labelSizeName = $width . "X" . $height;
 
            $functionName = "label" . strtolower($labelType) . $labelSizeName;
                     
            // pdf label generation
            $this->pdf->SetPrintFooter(false);
            $this->pdf->SetFooterMargin(0);
            $this->pdf->SetAutoPageBreak(false, 0);
            $page_size = array(150, 100);
            $this->pdf->AddPage("P", $page_size);
            $this->addWayBill($consignment);                                        
        }

        if ($labelType == 'pdf') {
            $fileName = date('Y_m_d') . '/' . $consignment->getId() . ".pdf";
            $this->pdf->IncludeJS("print();");
            $this->pdf->Output("../_assets/pdf/" . $fileName, "F");
        }
        $output['STATUS'] = 'SUCCESS';
        $output['LABEL'] = $fileName;
        $output['TRACKING_NUMBER'] = $licence_plate_array;
        return $output;
    }
    
    private function addWayBill(Consignment $consignment) {
         $this->fpdi = new  FPDI();
        $url      = $this->constants['EAM_GLOBAL_URL'];
        $userName = $this->constants['EAM_GLOBAL_USERNAME'];
        $password = $this->constants['EAM_GLOBAL_PASSWORD'];
        $country = new Country($consignment->getCountryId());
        $senderCountry = new Country($consignment->getSenderCountryId());
        $service = new Services($consignment->getServiceId());
        
        $xmleamglobal = 
                    '<?xml version="1.0" encoding="UTF-8"?>
                    <ProcessShipmentRequest>
                        <WebAuthenticationDetail>
                                <UserCredential>
                                    <Account>'.$userName.'</Account>
                                    <Password>'.$password.'</Password>
                                </UserCredential>
                        </WebAuthenticationDetail>
                        <TransactionDetail>
                            <CustomerTransactionId>'.$consignment->getHawb().'</CustomerTransactionId>
                        </TransactionDetail>
                        <RequestedShipment>
                            <ShipperCompany>'.$consignment->getSenderCompany().'</ShipperCompany>
                            <ShipperContact>'.$consignment->getSenderName().'</ShipperContact>
                            <ShipperAddress1>'.$consignment->getSenderAddressLine1().'</ShipperAddress1>
                            <ShipperAddress2>'.$consignment->getSenderAddressLine2().'</ShipperAddress2>
                            <ShipperAddress3>'.$consignment->getSenderAddressLine3().'</ShipperAddress3>
                            <ShipperTown>'.$consignment->getSenderCity().'</ShipperTown>
                            <ShipperCountry>'.$senderCountry->getIso().'</ShipperCountry>
                            <ShipperPostcode>'.$consignment->getSenderPostCode().'</ShipperPostcode>
                            <ShipperTelephone>'.$consignment->getSenderTelephone().'</ShipperTelephone>
                            <Hawb>'.$consignment->getHawb().'</Hawb>
                            <Service>'.$service->getCode().'</Service>
                            <Mawb></Mawb>
                            <Date>'.date('d/m/Y',$consignment->getDateCreated()).'</Date>
                            <Company>'.$consignment->getCompany().'</Company>
                            <Contact>'.$consignment->getContact().'</Contact>
                            <Address1>'.$consignment->getAddressLine1().'</Address1>
                            <Address2>'.$consignment->getAddressLine2().'</Address2>
                            <Address3>'.$consignment->getAddressLine3().'</Address3>
                            <Town>'.$consignment->getCity().'</Town>
                            <Country>'.$country->getIso().'</Country>
                            <Postcode>'.$consignment->getPostCode().'</Postcode>
                            <telephone>'.$consignment->getTelephone().'</telephone>
                            <noOfPieces>'.$consignment->getNumberPieces().'</noOfPieces>
                            <Weight>'.$consignment->getWeight().'</Weight>
                            <DoxNonDox>NDX</DoxNonDox>
                            <Description>'.$consignment->getDescription().'</Description>
                            <Value>'.$consignment->getValue().'</Value>
                            <Currency>'.$consignment->getCurrency().'</Currency>
                            <Agent></Agent>
                            <Notes></Notes>
                        </RequestedShipment>
                    </ProcessShipmentRequest>'; 
    $site_url = $url;    
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $xmleamglobal);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $response = curl_exec($ch);
    
    $xml = simplexml_load_string($response);
    $res = json_encode($xml);
    $labelResponse = json_decode($res);
    $b64Doc = base64_encode(file_get_contents($labelResponse->LABEL->LINK));
    $pdfMerger = new PDFMerger();
    $mergeFileName = '../_assets/pdf/'."merged".$time.".pdf";
    $pdfMerger->addPDF('../'.base64_decode($b64Doc), 'all'); 
   
    print_r($b64Doc);    
    die;
    }
    public function tracking($trackingNumber, $trackBy = 'parcel', $EDI = false) 
    {}

    public function sendData($tracking_numbers = array()) {}

    public function manifest($consignment) {
        
    }

    public function preAdvice($consignment) {
        
    }
    public function recycledShipment($consignment) {
        $output["STATUS"] = "SUCCESS";
        return $output;
    }
}
