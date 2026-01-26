<?php
class LapostePost implements CarrierService {

    private $pdf;
    private $serviceValues = null;
    private $agentValues = null;
    private $constants = null;
    private $user = null;
    private $country = null;

    const WSDL = 'https://ws.colissimo.fr/sls-ws/SlsServiceWS?wsdl';
    const contractNumber = '827292';
    const api_password = 'OSDA2016'; //KAAB2016

    public function __construct() {
       
    }

    public function validation(Consignment $consignment, Services $service, Country $country) {
        
    }

    public function remoteareas($consignment, $carrierObject, $sender) {
        
    }

    public function label($consignment, $labelType = 'pdf', $size = '') {
         $pdf = new PdfBase(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $this->pdf = $pdf;
        $output = array();
        $this->serviceValues = new Services($consignment->getServiceId());
        $this->user = SessionManager::getUser();
        $this->country = new Country($consignment->getCountryId());


        try {
            $addressLine2 = $consignment->getAddressLine2();
            if ($addressLine2 == "")
                $addressLine2 = "-";

            $addressLine3 = $consignment->getAddressLine3();
            if ($addressLine3 == "")
                $addressLine3 = "-";

            $generateLabelParam = array(
                'generateLabelRequest' => array(
                    'contractNumber' => self::contractNumber,
                    'password' => self::api_password,
                    'outputFormat' => array(
                        'x' => '0',
                        'y' => '0',
                        'outputPrintingType' => 'PDF_10x15_300dpi',
                    ),
                    'letter' => array(
                        'service' => array(
                            'productCode' => 'DOS',
                            'depositDate' => date("Y-m-d"),
                            'mailBoxPicking' => 'false',
                            'transportationAmount' => '1040',
                            'totalAmount' => '1222',
                            'orderNumber' => $consignment->getHawb(),
                            'commercialName' => 'KAAB',
                            'returnTypeChoice' => '2',
                        ),
                        'parcel' => array(
                            'weight' => '1',
                            'nonMachinable' => 'false',
                            'instructions' => $consignment->getNotes(),
                        ),
                        'customsDeclarations' => array(
                            'includeCustomsDeclarations' => '1',
                            'contents' => array(
                                'article' => array(
                                    'description' => $consignment->getDescription(),
                                    'quantity' => $consignment->getNumberPieces(),
                                    'weight' => $consignment->getWeight(),
                                    'value' => $consignment->getValue(),
                                    'hsCode' => '0102',
                                    'originCountry' => 'PL',
                                ),
                                'category' => array(
                                    'value' => '1',
                                ),
                            ),
                        ),
                        'sender' => array(
                            'senderParcelRef' => '',
                            'address' => array(
                                'companyName' => 'KAAB',
                                'lastName' => 'Piotr',
                                'firstName' => 'Arnold',
                                'line0' => ' PFC ERSTEIN',
                                'line1' => 'c/o Asendia Germany',
                                'line2' => '-',
                                'line3' => '-',
                                'countryCode' => 'FR',
                                'city' => 'ERSTEIN Cédex',
                                'zipCode' => '67119',
                                'phoneNumber' => '0048774593339',
                                'mobileNumber' => '0048505507307',
                                'doorCode1' => '12ZZ2',
                                'doorCode2' => '121FD',
                                'email' => 'kaab@kaabnl.nl',
                                'intercom' => '',
                                'language' => 'PL',
                            ),
                        ),
                        'addressee' => array(
                            'addresseeParcelRef' => $consignment->getHawb(),
                            'codeBarForReference' => 'false',
                            'serviceInfo' => 'service info',
                            'address' => array(
                                'companyName' => $consignment->getCompany(),
                                'lastName' => '.',
                                'firstName' => $consignment->getContact(),
                                'line0' => $consignment->getAddressLine1(),
                                'line1' => $addressLine2,
                                'line2' => $addressLine3,
                                'line3' => '-',
                                'countryCode' => 'FR',
                                'city' => $consignment->getCity(),
                                'zipCode' => $consignment->getPostcode(),
                                'phoneNumber' => $consignment->getTelephone(),
                                'mobileNumber' => '',
                                'doorCode1' => $consignment->getReference(),
                                'doorCode2' => $consignment->getNotes(),
                                'email' => $consignment->getEmail(),
                                'intercom' => '324RR',
                                'language' => 'FR',
                            ),
                        ),
                    ),
                ),
            );
            $generateLabelResponse = $this->generateLabel($generateLabelParam);
            print_r($generateLabelResponse);
            echo "nmrugsa";
            exit;
            if ($apiGenratelable->getErrorCode() == 0) {

                $waybill = $generateLabelResponse['parcelNumber'];
                //echo "<strong>PDF Url:</strong> ".$generateLabelResponse['pdfUrl']."<br />";
                $pdfContent = $generateLabelResponse['Label'];
                $fileName = '../_assets/pdf/' . date('Y_m_d') . '/' . $consignment->getId() . ".pdf";
                if (file_put_contents($fileName, $pdfContent)) {
                    $parcel = new ParcelFilter();
                    $parcel->addConsignmentIdFilter($consignment->getId());
                    $parcelList = $parcel->getList();
                    if (count($parcelList) > 0) {
                        $parcelList[0]->setTrackingNumber($waybill);
                        $parcelList[0]->save();
                    }

                    return "SUCCESS||" . SETTING_MAIN_URL . "_assets/pdf/" . date('Y_m_d') . "/" . $consignment->getId() . ".pdf||" . $waybill;
                }
            } else {
                //echo "<strong>ERROR CODE:</strong> ".$apiGenratelable->getErrorCode()."<br />";
                //echo "<strong>ERROR:</strong> ".$apiGenratelable->getErrorMessage();
                $error = $apiGenratelable->getErrorCode() . "-" . $apiGenratelable->getErrorMessage();
                return "ERROR||" . $error;
            }
        } catch (Exception $ex) {
            echo $ex->getMessage();
        }



//        $this->pdf->IncludeJS("print();");
//        $this->pdf->Output("../_assets/pdf/" . date('Y_m_d') . '/' . $consignment->getId() . ".pdf", "F");
//        $output['STATUS'] = 'SUCCESS';
//        $output['LABEL'] = date('Y_m_d') . '/' . $consignment->getId() . ".pdf";
//        $output['TRACKING_NUMBER'] = $licence_plate_array;
//        return $output;
    }

    public function tracking($trackingNumber, $trackBy = 'parcel', $EDI = false) {
        
    }

    public function sendData($tracking_numbers = array()) {
        
    }

    public function manifest($consignment) {
        
    }

    public function preAdvice($consignment) {
        
    }


    /**
     * generateLabel
     * @param string new pass phrase <p>
     * Must be a string or array.
     * </p>
     */
    public function generateLabel($generateLabelParam) {

        $client = new SoapClient(self::WSDL, array("trace" => 1, "exception" => 0));
       //print_r($client);
       // $client_error = $client->getError();
       // if ($client_error)
         //   throw new ErrorException($client_error);
       //print_r($generateLabelParam);
       //print_r($client->__getFunctions());
        echo "asdfasdfasf";
        try{
            $rawResponse = $client->__soapCall("generateLabel", $generateLabelParam, NULL, NULL,  $output_headers);
            //print_r( $output_headers);
        }
        catch(Exception $e)
        {
            echo $e->getMessage();
        }
        echo $client->__getLastRequest();
        //print_r($rawResponse);
        exit;
        //$this->call('generateLabel', $generateLabelParam);

        $rawResponse = $this->responseData;
        //echo "<pre>"; print_r($rawResponse); echo "</pre>";
        $soapStart = strpos($rawResponse, '<soap:Envelope');
        $soapEnd = strpos($rawResponse, 'soap:Envelope>');
        $soapresponse = substr($rawResponse, $soapStart, (($soapEnd - $soapStart) + strlen('soap:Envelope>')));


        $soapresponse = iconv('UTF-8', 'UTF-8//IGNORE', $soapresponse);

        $xml = simplexml_load_string($soapresponse);
        $return = $xml->children('soap', true)->Body->children('ns2', true)->children()->return;
        $message = (array) $return->messages;



        $labelResponse = (array) $return->labelResponse;
        $this->error_code = $message['id'];
        $this->error_message = $message['messageContent'];
        $response = array();


        if ($message['id'] == 0) {
            $parcelNumber = $labelResponse['parcelNumber'];
            $pdfUrl = $labelResponse['pdfUrl'];

            $response['parcelNumber'] = $parcelNumber;
            $response['pdfUrl'] = $pdfUrl;
            $response['DownloadlabelPath'] = '';
            $pdfStart = strpos($rawResponse, '%PDF');
            $pdfContent = substr($rawResponse, $pdfStart);
            $response['Label'] = $pdfContent;
        }
        return $response;
    }

    public function recycledShipment($consignment) {
        $output["STATUS"] = "SUCCESS";
        return $output;
    }

}
