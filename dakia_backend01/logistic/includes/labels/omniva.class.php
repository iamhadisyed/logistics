<?php

////////////////////////////////////////////////////
//
// Class for dealing Omniva Validation, Label, Pre-advice, Tracking, Manifest
//
////////////////////////////////////////////////////

/**
 * Omniva class
 * @package News Releases
 */
include_classes([
    'omnivalocation.class',
    'omnivalocationfilter.class',
]);
include_classes([
    'googledistancematrix.class'
        ], 'labels');

class Omniva implements CarrierService {

    private $pdf;
    private $serviceValues = null;
    private $constants = null;
    private $country = null;
    private $user = null;

    public function __construct() {
        
    }

    public function validation(Consignment $consignment, Services $service, Country $country) {
        $returnOutput = array();

        if (trim($consignment->getEmail()) == "" || trim($consignment->getTelephone() == "")) {
            $returnOutput[] = "Please enter Email and Telephone Number.";
        }
        return $returnOutput;
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
        if (trim(@$this->constants['OMNIVA_WSDL']) == '' || trim(@$this->constants['OMNIVA_USERNAME']) == '' || trim(@$this->constants['OMNIVA_PASSWORD']) == '' || trim(@$this->constants['OMNIVA_NAMESPACE']) == '') {
            $output['STATUS'] = 'ERROR';
            $output['MESSAGE'] = "Please check service constants. Please contact to itsupport@oneworldexpress.com";
            return $output;
        }

        //if ($this->constants['INTEGRATION_TYPE'] == "API") {
        $output = $this->apiLabel($this->constants, $consignment);
        //}


        return $output;
    }

    private function apiLabel($constants, $consignment) {
        require_once('../includes/3rdparty/nusoap/nusoap.php');
        $output = array();
        try {
            $wsdl = $constants["OMNIVA_WSDL"];
            $username = $constants["OMNIVA_USERNAME"];
            $password = $constants["OMNIVA_PASSWORD"];
            $namespace = $constants["OMNIVA_NAMESPACE"];

            $postcode = $consignment->getPostcode();
            preg_match_all('!\d+!', $postcode, $matches);
            $handling = $this->serviceValues->getCode();
            if ($handling == 'STOMVBALT') {
                $productName = "Courier Mail-order";
                if ($this->country->getIso() == "LV" || $this->country->getIso() == "LT") {

                    $param = ' <sch:businessToClientMsgRequest>
                                <partner xmlns="">7103139</partner>
                                <interchange msg_type="elsinfov1" xmlns="">
                                       <header file_id="123456" sender_cd="7103139"/>
                                       <item_list>
                                          <item service="QH">
                                                 <add_service>
                                                        <option code="CL"/>
                                                 </add_service>
                                                 <measures weight="' . $consignment->getWeight() . '"/>
                                                 <comment>' . $consignment->getDescription() . '</comment>
                                                 <partnerId>PAC01</partnerId>
                                                 <receiverAddressee>
                                                        <person_name>' . $consignment->getContact() . '</person_name>
                                                        <phone>' . $consignment->getTelephone() . '</phone>
                                                        <address postcode="' . $matches[0][0] . '" deliverypoint="' . $consignment->getCity() . '" country="' . $this->country->getIso() . '" street="' . $consignment->getAddressLine1() . '" /> 
                                                 </receiverAddressee>
                                                 <re85turnAddressee>
                                                        <person_name>One World</person_name>
                                                        <phone>6549549</phone>
                                                        <mobile>521234</mobile>
                                                       <email>info@omniva.ee</email>
                                                       <address postcode="10001" deliverypoint="Tallinn" country="EE" street="Pallasti 28"/>
                                                 </returnAddressee>
                                          </item>
                                       </item_list>
                                </interchange>
                        </sch:businessToClientMsgRequest>';
                } else if ($this->country->getIso() == 'EE') {
                    $param = '<sch:businessToClientMsgRequest>
                                <partner xmlns="">7103139</partner>
                                <interchange msg_type="elsinfov1" xmlns="">
                                   <header file_id="123456" sender_cd="7103139"/>
                                   <item_list>
                                          <item service="QH">
                                                <measures weight="' . $consignment->getWeight() . '"/>
                                                <comment>' . $consignment->getDescription() . '</comment>
                                                <partnerId>PAC01</partnerId>
                                                <receiverAddressee>
                                                       <person_name>' . $consignment->getContact() . '</person_name>
                                                       <phone>' . $consignment->getTelephone() . '</phone>
                                                       <address postcode="' . $matches[0][0] . '" deliverypoint="' . $consignment->getCity() . '" country="' . $this->country->getIso() . '" street="' . $consignment->getAddressLine1() . '" /> 
                                                </receiverAddressee>
                                                <returnAddressee>
                                                       <person_name>EESTI POSTMARK</person_name>
                                                       <phone>6549549</phone>
                                                       <mobile>521234</mobile>
                                                       <email>info@omniva.ee</email>
                                                       <address postcode="10001" deliverypoint="Tallinn" country="EE" street="Pallasti 28"/>
                                                </returnAddressee>
                                          </item>
                                   </item_list>
                                </interchange>
                         </sch:businessToClientMsgRequest>';
                }

                $address = $consignment->getAddressLine1() . " " . $consignment->getAddressLine2() . " " . $consignment->getAddressLine3();
                $postcode = $consignment->getPostcode();
                $city = $consignment->getCity();
                $telephone = $consignment->getTelephone();
            } else if ($this->serviceValues->getCode() == 'STOPABALT') {
                $productName = "Parcel Machine Service";
                $param = '<sch:businessToClientMsgRequest>
                            <partner xmlns="">7103139</partner>
                            <interchange msg_type="elsinfov1" xmlns="">
                                <header file_id="123456" sender_cd="7103139"/>
                                <item_list>
                                    <item service="PA">
                                          <add_service>
                                          <option code="ST"/>
                                          <option code="SF"/>
                                          </add_service>
                                           <measures weight="' . $consignment->getWeight() . '"/>
                                           <partnerId>PAC01</partnerId>
                                           <receiverAddressee>
                                                  <person_name>' . $consignment->getContact() . '</person_name>
                                                  <mobile>' . $consignment->getTelephone() . '</mobile>
                                                  <email>' . $consignment->getEmail() . '</email>
                                                  <address country="' . $this->country->getIso() . '" offloadPostcode="' . $consignment->getPostcode() . '"/>
                                           </receiverAddressee>
                                           <returnAddressee>
                                                  <person_name>One World Express</person_name>
                                                  <phone>6549549</phone>
                                                  <mobile>21234654</mobile>
                                                  <email>info@omniva.ee</email>
                                                  <address postcode="LV-1055" deliverypoint="Riga" country="LV" street="Daugavgrivas Street 114"/>
                                           </returnAddressee>
                                    </item>
                                </item_list>
                            </interchange>
                        </sch:businessToClientMsgRequest>';

                $address = $consignment->getCompany() . " " . $consignment->getAddressLine1() . " " . $consignment->getAddressLine2() . " " . $consignment->getAddressline3();
                $postcode = $consignment->getPostcode();
                $city = $consignment->getCity();
                $telephone = $consignment->getTelephone();
            }
            else if ($this->serviceValues->getCode() == 'STOMBLTRT') {
                $productName = "Parcel machine – courier delivery";
                $param = '<sch:businessToClientMsgRequest>
                            <partner xmlns="">7103139</partner>
                            <interchange msg_type="elsinfov1" xmlns="">
                                <header file_id="123456" sender_cd="7103139"/>
                                <item_list>
                                    <item service="PK">
                                          <add_service>
                                          <option code="GM"/>
                                          </add_service>
                                           <measures weight="' . $consignment->getWeight() . '"/>
                                           <partnerId>PAC01</partnerId>
                                           <receiverAddressee>
                                                <person_name>Omniva</person_name>
                                                <mobile>28802424</mobile>
                                                <email>'.$consignment->getEmail().'</email>
                                                <address country="LV" deliverypoint="Mārupe" postcode="LV-2167" street="Dzirnieku iela 24"/>
                                            </receiverAddressee>
                                           <returnAddressee>
                                                  <person_name>Omniva</person_name>                                                 <mobile>28802424</mobile>
                                                  <email>'.$consignment->getEmail().'</email>
                                                  <address postcode="LV-2167" deliverypoint="Mārupe" country="LV" street="Dzirnieku iela 24"/>
                                           </returnAddressee>
                                    </item>
                                </item_list>
                            </interchange>
                        </sch:businessToClientMsgRequest>';

                $address = "Omniva Dzirnieku iela 24 ";
                $postcode = "LV-2167";
                $city = "Mārupe";
                $telephone = "28802424";
            }
            $client = new nusoap_client($wsdl, true);

            $client->setCredentials($username, $password);
            //print_r($param);
            $response = $client->call('businessToClientMsg', $param, 'http://service.core.epmx.application.eestipost.ee/xsd');

            $consignment->setApiData(print_r($param, true), print_r($response, true), 'businessToClientMsg');
            if ($response["prompt"] == "Messages successfully received!") {
                $licence_plate = $response["savedPacketInfo"]["barcodeInfo"]["barcode"];
                $parcel_list = $consignment->getParcels();
                $parcel_list[0]->setTrackingNumber($licence_plate);
                $parcel_list[0]->save();
            } else {
                $error = $response["faultyPacketInfo"]["barcodeInfo"]["message"];
                if (trim($error) == "")
                    $error = $response["prompt"];
                $output["STATUS"] = "ERROR";
                $output["MESSAGE"] = $error;
                return $output;
            }
        } catch (Exception $ex) {
            $output["STATUS"] = "ERROR";
            $output["MESSAGE"] = $ex->getMessage();
            return $output;
        }
//        echo "fasdfasdf";
//        echo $licence_plate;
//        exit;
        $pdf = new PdfBase(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $this->pdf = $pdf;
        $this->pdf->SetPrintFooter(false);
        $this->pdf->SetFooterMargin(0);
        $this->pdf->SetAutoPageBreak(false, 0);
        $page_size = array(100, 150);
        $this->pdf->AddPage("P", $page_size);
        $this->pdf->setFont("freesans", "L", 7);

        $this->pdf->Line(50, 0, 50, 100);
        $this->pdf->Line(0, 35, 100, 35);
        $this->pdf->Line(0, 100, 100, 100);
        // $this->pdf->Line(0, 135, 135, 135);

        $this->pdf->setFont("freesans", "B", 8);

        $this->pdf->Text(1, 0, "Saatja: One World Express Inc.");
        $this->pdf->setFont("freesans", "", 8);
        $this->pdf->Text(1, 4, "One World House");
        $this->pdf->Text(1, 8, "Pump Lane, Hayes, Middlesex");
        $this->pdf->Text(1, 12, "London, UB3 3NB");

        $image = SETTING_DIR_REMOTE . "images/omniva_logo.png";
        $this->pdf->image($image, 52, 3, 15);

        $this->pdf->Text(70, 1, "AS Eesti Post,");
        $this->pdf->Text(70, 4, "Pallasti 28, 10001");
        $this->pdf->Text(70, 8, "Tallinn");
        $this->pdf->Text(70, 15, "E-post:");
        $this->pdf->Text(70, 19, "info@omniva.ee");
        $this->pdf->Text(70, 26, "Kliendiinfo: 6616616");

        $this->pdf->Text(1, 36, "Vastu võetud");
        $this->pdf->setFont("freesans", "B", 13);

        $this->pdf->MultiCell(40, 1, $productName, 0, 'L', 0, 1, 1, 50);
        //$this->pdf->Text(1,50, "Pakiautomaadi teenus");	

        $this->pdf->setFont("freesans", "", 12);
        
        if ($this->serviceValues->getCode() == 'STOMBLTRT'){
            $this->pdf->MultiCell(40,1, "Apmaksāts sūtītājs - SMS", 0, 'L', 0, 1, 1, 65);
            $this->pdf->Text(52, 78, "Piezīmes:" . $consignment->getContact());
        }
        else
            $this->pdf->MultiCell(40, 1, "Paki saabumise SMS, Paki saabumise e-kiri", 0, 'L', 0, 1, 1, 65);

        $this->pdf->setFont("freesans", "", 8);
        $this->pdf->Text(52, 36, "Saaja/mahalaadimise aadress");
        $contact = $consignment->getContact();
        if ($contact == "")
            $contact = $consignment->getCompany();
        $this->pdf->setFont("freesans", "", 10);
        $this->pdf->Text(52, 41, $contact);
        $this->pdf->Text(52, 46, "Tel: " . $telephone);
        $this->pdf->MultiCell(40, 1, utf8_decode($address), 0, 'L', 0, 1, 52, 53);
        $this->pdf->Text(52, 65, $postcode);
        $this->pdf->Text(52, 70, utf8_encode($city));
        $this->pdf->Text(52, 75, $this->country->getName());
        
        // $this->pdf->MultiCell(40, 1, "Märkused: " . $consignment->getNotes(), 0, 'L', 0, 1, 52, 80);
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
            'text' => true,
            'font' => 'helvetica',
            'fontsize' => 8,
            'stretchtext' => 4
        );

        $this->pdf->write1DBarcode($licence_plate, 'C128', 20, 105, 55.5, 25, 1, $style, 'Y');
        // $this->pdf->Text(30, 128, $licence_plate);
        $licence_plate_array[0] = $licence_plate;



        $this->pdf->Output(SETTING_DIR_ASSETS . "pdf/" . date('Y_m_d') . '/' . $consignment->getId() . ".pdf", "F");
        $this->pdf->IncludeJS("print();");
        $output['STATUS'] = 'SUCCESS';
        $output['LABEL'] = date('Y_m_d') . '/' . $consignment->getId() . ".pdf";
        $output['TRACKING_NUMBER'] = $licence_plate_array;
        return $output;
    }

    public function tracking($trackingNumber, $trackBy = 'parcel', $EDI = false) {
        $tracking = new Tracking();
        $date = date('Y-m-d\TH:i:s', strtotime('-5 day', strtotime(date('Y-m-d H:i:s'))));
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://edixml.post.ee/epteavitus/events/from/' . $date . '/for-client-code/7103139',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'Authorization: Basic NzEwMzEzOTpRVVVZeXpjSA=='
            ),
        ));

        $response = curl_exec($curl);
        //print_r($response);
        //   $trackingXml = file_get_contents('https://edixml.post.ee/epteavitus/events/from/2021-02-10T11:27:37/for-client-code/7103139');  
        //$trackingXml = file_get_contents('C:\xampp\htdocs\smarttrackoptimization\includes\labels\test.txt');  
        $xml = new SimpleXMLElement($response);
        //print_r($xml); die;
        include_once(BASE_PATH . "includes/labels/omnivatrackingstatus.class.php");


        if (count($xml) > 0) {
            foreach ($xml as $trackingArray) {
                $trackingNumber = $trackingArray->packetCode;

                $eventCode = trim($trackingArray->eventCode);

                $eventDate = date('Y-m-d H:i:s', strtotime($trackingArray->eventDate) + 3600);
                $eventDescription = OmnivaTrackingStatus::$omniva_status_code[$eventCode];

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
                $trackingDataFilterObj->addFilter("carrier_code not in ('','PACKET_EVENT_SAVED')");
                $trackingEvents = $trackingDataFilterObj->getList();

                if (count($trackingEvents) > 0) {
                    $carrierReceivedCheck = 0;   // there is already carrier received event                
                    $trackingDataFilterObj = new TrackingDataFilter();
                    $trackingDataFilterObj->addFieldFilter('    tracking_number', $trackingNumber);
                    $trackingDataFilterObj->addFilter("status_code_id = '148'");
                    $CarrierReceivedObj = $trackingDataFilterObj->getList();
                    if (count($CarrierReceivedObj) > 0) {
                        $carrierCodeCarrierReceived = $CarrierReceivedObj[0]->getCarrierCode();
                        $carrierReceivedStatusCode = $CarrierReceivedObj[0]->getStatusCodeId();
                    }
                } else {
                    $carrierReceivedCheck = 1; // No carrier received Event
                }
                ////////////////////// Carrier Received ////////////////////////////////


                if ($entityId > 0) {
                    $parcelEntity = new Parcel($entityId);
                    $finalStatusCode = $parcelEntity->getParcelStatusCode();

                    $DateTime = $eventDate;
                    $EventCode = $eventCode;
                    $Signatory = '';
                    $EventDescription = $eventDescription;
                    $ServiceAreaDescription = '';

                    $deliveredArray = array('PACKET_EVENT_DELIVERED');

                    ////////////////////// Carrier Received ////////////////////////////////
                    if ($carrierReceivedCheck == 1 && $EventCode != 'PACKET_EVENT_SAVED') {
                        $spTrackingStatus = '148';
                        $carrierReceivedCheck = 0;
                    } else {
                        $spTrackingStatus = OmnivaTrackingStatus::getOweStatusCode($EventCode);
                    }
                    //echo $spTrackingStatus; 
                    ////////////////////// Carrier Received ////////////////////////////////

                    $result = $tracking->saveTrackPoint($entityId, $trackBy, $DateTime, $trackingNumber, $spTrackingStatus, $ServiceAreaDescription, $EventCode, $EventDescription, $deliveredArray, $finalStatusCode);
                }
                $tracking->saveConsignmentTrackingStatus($trackingNumber, 'OmnivaTrackingStatus');
            }
        }
        //   die;        
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

    private function closest($input, $words, &$shortest, $sensitivity, $debug = false) {

        $matchArray = array();
        // loop through words to find the closest
        foreach ($words as $key => $word) {

            // calculate the distance between the input word,
            // and the current word
            $lev = levenshtein($input, $word);
            if ($debug) {
                echo $word . "-" . $lev . "-" . $shortest . "-" . $sensitivity;
                echo "\r\n";
            }
            // check for an exact match
            if ($lev == 0) {

                // closest word is this one (exact match)
                $closest = $key;
                $shortest = 0;
                $matchArray[$key] = $word;


                // break out of the loop; we've found an exact match
                // break;
            }

            // if this distance is less than the next found shortest
            // distance, OR if a next shortest word has not yet been found
            if ($lev <= $shortest || $shortest < 0) {
                // set the closest match, and shortest distance
                $closest = $key;
                $shortest = $lev;
                $matchArray[$key] = $word;
            }
        }
        if ($debug) {
            print_r($matchArray);
        }
        if ($shortest <= $sensitivity) {
            return $matchArray;
        } else {
            return $matchArray;
        }
    }

    /*
     * Fetch Nearest Parcel Shop based on address
     */

    public function getDropOffLocation($postcode, $iso, $city = '') {
        if ($iso == "" || $city == "") {
            return "";
        }
        $omnivaLocationFilter = new OmnivaLocationFilter();
        $omnivaLocationFilter->addFilter("country='" . $iso . "' and type = 0"); //and zip like '".$postcodeMatch."%'
        $omnivaLocationList = $omnivaLocationFilter->getList("country, zip, town, id, county");
        if (count($omnivaLocationList) > 0) {
            $destinationpostcode = array();
            $destinationMatch = array();

            foreach ($omnivaLocationList as $omnivaLoc) {
                if (trim($omnivaLoc->getTown()) != '')
                    $destinationMatch[$omnivaLoc->getId()] = utf8_decode($omnivaLoc->getTown());
                else
                    $destinationMatch[$omnivaLoc->getId()] = utf8_decode($omnivaLoc->getCounty());
            }
            $shortest = -1;
            $closedMatch = $this->closest($city, $destinationMatch, $shortest, 1);
            //$closedMatch = array("7904" => "Riga");
            if (count($closedMatch) > 0) {
                $matchedId = array();
                foreach ($closedMatch as $key => $match) {
                    $matchedId[] = $key;
                }
                $omnivaLocationFilter = new OmnivaLocationFilter();
                $omnivaLocationFilter->addFilter("id in ('" . implode("','", $matchedId) . "')");
                $omnivaMatchList = $omnivaLocationFilter->getList();
                if (count($omnivaMatchList) > 0) {
                    $dropoffDetails = array();
                    $i = 0;
                    foreach ($omnivaMatchList as $location) {
                        $storedetail = array();
                        $storedetail["lat"] = $location->getYCoordinate();
                        $storedetail["lng"] = $location->getXCoordinate();
                        $storedetail["companyname"] = $location->getPostofficeName();
                        $storedetail["addressline1"] = $location->getStreet() . " " . $location->getHouseNo();
                        $storedetail["addressline2"] = '';
                        $storedetail["city"] = ($location->getTown() != "" ? $location->getTown() : $location->getCounty());
                        $storedetail["postcode"] = $location->getZip();
                        $storedetail["country"] = $location->getCountry();
                        $storedetail["telephone"] = '';
                        $storedetail["mon"] = '';
                        $storedetail["tue"] = '';
                        $storedetail["wed"] = '';
                        $storedetail["thu"] = '';
                        $storedetail["fri"] = '';
                        $storedetail["sat"] = '';
                        $storedetail["sun"] = ' ';
                        $storedetail["branchId"] = '';
                        $i++;
                        $dropoffDetails[] = $storedetail;
                        //$destinationpostcode[] = $matchRecord->getYCoordinate() . ',' . trim($matchRecord->getXCoordinate());
                    }
                    return $dropoffDetails;
                }
            }
        }
    }

}
