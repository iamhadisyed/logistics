<?php

include_classes([
    'pdfmerger',
    ], 'labels');
include_classes([
    'fpdi',
    ], '3rdparty/fpdi');

class SFExpress implements CarrierService {

    private $pdf;
    private $serviceValues = null;
    private $constants = null;
    private $country = null;
    private $user = null;

    public function __construct() {
       
    }

    public function validation(Consignment $consignment, Services $service, Country $country) {
        $returnOutput = array();
        if ($consignment->getTelephone() == "") {
            $returnOutput[] = "Please enter telephone number.";
            return $returnOutput;
        }
    }

    public function remoteareas($consignment, $carrierObject, $sender) {
        
    }

    public function label($consignment, $labelType = 'pdf', $size = '') {

        $output = array();
        $this->serviceValues = new Services($consignment->getServiceId());
        $this->country = new Country($consignment->getCountryId());
        $this->user = SessionManager::getUser();


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
        if ($this->constants['INTEGRATION_TYPE'] == '' || trim(@$this->constants['SFEXPRESS_WSDL']) == '' || trim(@$this->constants['SFEXPRESS_ACCOUNT']) == '' || trim(@$this->constants['SFEXPRESS_PASSWORD']) == '') {
            $output['STATUS'] = 'ERROR';
            $output['MESSAGE'] = "Please check service constants. Please contact to itsupport@oneworldexpress.com";
            return $output;
        }

        if ($this->constants['INTEGRATION_TYPE'] == "API") {
            $output = $this->apiLabel($this->constants, $consignment);
        }


        return $output;
    }

    private function apiLabel($constants, $consignment) {
         $pdf = new PdfBase(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $this->pdf = $pdf;
        $output = array();
        $url = $constants["SFEXPRESS_WSDL"];
        if ($this->serviceValues->getCode() == "WPXSING") {

            $agent = "DHL";
            $service = "INT";
            $requestXml = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\r\n<ProcessShipmentRequest>\r\n    <WebAuthenticationDetail>\r\n            <UserCredential>\r\n                    <Account>" . $constants["SFEXPRESS_ACCOUNT"] . "</Account>\r\n                    <Password>" . $constants["SFEXPRESS_PASSWORD"] . "</Password>\r\n            </UserCredential>\r\n    </WebAuthenticationDetail>\r\n    <TransactionDetail>\r\n            <CustomerTransactionId>123456789</CustomerTransactionId>\r\n    </TransactionDetail>\r\n    <RequestedShipment>\r\n            <Hawb>" . ($consignment->getHawb()) . "</Hawb>\r\n            <Service>" . $service . "</Service>\r\n            <Mawb>" . $consignment->getMawb() . "</Mawb>\r\n            <Date>" . date("d/m/Y") . "</Date>\r\n            <Company>" . $consignment->getCompany() . "</Company>\r\n            <Contact>" . $consignment->getContact() . "</Contact>\r\n            <Address1>" . $consignment->getAddressLine1() . "</Address1>\r\n            <Address2>" . $consignment->getAddressLine2() . "</Address2>\r\n            <Address3>" . $consignment->getAddressLine3() . "</Address3>\r\n            <Town>" . $consignment->getCity() . "</Town>\r\n            <Country>" . $this->country->getIso() . "</Country>\r\n            <Postcode>" . $consignment->getPostCode() . "</Postcode>\r\n            <telephone>" . $consignment->getTelephone() . "</telephone>\r\n            <noOfPieces>" . $consignment->getNumberPieces() . "</noOfPieces>\r\n            <Weight>" . $consignment->getWeight() . "</Weight>\r\n            <DoxNonDox>NDX</DoxNonDox>\r\n            <Description>" . $consignment->getDescription() . "</Description>\r\n            <Value>" . $consignment->getValue() . "</Value>\r\n            <Weight>" . $consignment->getWeight() . "</Weight>\r\n            <Currency>" . $consignment->getCurrency() . "</Currency>\r\n            <Agent>" . $agent . "</Agent>\r\n            <Notes>" . $consignment->getNotes() . "</Notes>\r\n    </RequestedShipment>\r\n </ProcessShipmentRequest>";
        } else {

            $requestXml = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\r\n<ProcessShipmentRequest>\r\n    <WebAuthenticationDetail>\r\n            <UserCredential>\r\n                    <Account>" . $constants["SFEXPRESS_ACCOUNT"] . "</Account>\r\n                    <Password>" . $constants["SFEXPRESS_PASSWORD"] . "</Password>\r\n            </UserCredential>\r\n    </WebAuthenticationDetail>\r\n    <TransactionDetail>\r\n            <CustomerTransactionId>123456789</CustomerTransactionId>\r\n    </TransactionDetail>\r\n    <RequestedShipment>\r\n            <Hawb>" . ($consignment->getHawb()) . "</Hawb>\r\n            <Service>" . $this->serviceValues->getCode() . "</Service>\r\n            <Mawb>" . $consignment->getMawb() . "</Mawb>\r\n            <Date>" . date("d/m/Y") . "</Date>\r\n            <Company>" . $consignment->getCompany() . "</Company>\r\n            <Contact>" . $consignment->getContact() . "</Contact>\r\n            <Address1>" . $consignment->getAddressLine1() . "</Address1>\r\n            <Address2>" . $consignment->getAddressLine2() . "</Address2>\r\n            <Address3>" . $consignment->getAddressLine3() . "</Address3>\r\n            <Town>" . $consignment->getCity() . "</Town>\r\n            <Country>" . $this->country->getIso() . "</Country>\r\n            <Postcode>" . $consignment->getPostCode() . "</Postcode>\r\n            <telephone>" . $consignment->getTelephone() . "</telephone>\r\n            <noOfPieces>" . $consignment->getNumberPieces() . "</noOfPieces>\r\n            <Weight>" . $consignment->getWeight() . "</Weight>\r\n            <DoxNonDox>NDX</DoxNonDox>\r\n            <Description>" . $consignment->getDescription() . "</Description>\r\n            <Value>" . $consignment->getValue() . "</Value>\r\n            <Weight>" . $consignment->getWeight() . "</Weight>\r\n            <Currency>" . $consignment->getCurrency() . "</Currency>\r\n            <Agent>" . $this->serviceValues->getCode() . "</Agent>\r\n            <Notes>" . $consignment->getNotes() . "</Notes>\r\n    </RequestedShipment>\r\n </ProcessShipmentRequest>";
        }
        
     
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url . "processShipmentWebservice.php",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => $requestXml,
            CURLOPT_HTTPHEADER => array(
                "Cache-Control: no-cache",
                "Content-Type: text/xml",
            ),
        ));

        $response = curl_exec($curl);
        $dom = new DOMDocument;
        $dom->load($response);

        print_r($response);
        exit;
        $xml = new SimpleXMLElement($response);
        $awbNumber = $xml->LABEL->AWB;

        $labelLink = $xml->LABEL->LINK;

        $fileName = SETTING_DIR_REMOTE . "_assets/pdf/" . date('Y_m_d') . '/' . $consignment->getId() . ".pdf";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_AUTOREFERER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, $labelLink);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);

        $pdf_decoded = curl_exec($ch);

        $mergeFileName = '../_assets/pdf/' . date('Y_m_d') . '/' . $consignment->getId() . ".pdf";
        $pdf = fopen($mergeFileName, 'w');
        fwrite($pdf, $pdf_decoded);
        fclose($pdf);
        if ($agent == 'DHL') {

            $PDFMerger = new PDFMerger();
            $PDFMerger->addPDF($fileName);

            try {
                $PDFMerger->mergeDhlSing('file', $fileName, '', $consignment);
            } catch (Exception $e) {
                $output["STATUS"] = "ERROR";
                $output["MESSAGE"] = $e->getMessage();
                return $output;
            }
        }
        $parcel_list = $consignment->getParcels();
        $parcel_count = sizeof($parcel_list);
        $parcel_idx = 0;
        // Generate label for each parecel
        foreach ($parcel_list as $parcel) {
            $parcel->setTrackingNumber($awbNumber);
            $parcel->save();
            ++$parcel_idx;
        }


        $licence_plate_array[0] = $awbNumber;
        $consignment->setApiData($requestXml, $response, 'API');



        $this->pdf->IncludeJS("print();");
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

    public function recycledShipment($consignment) {
        $output["STATUS"] = "SUCCESS";
        return $output;
    }

}
