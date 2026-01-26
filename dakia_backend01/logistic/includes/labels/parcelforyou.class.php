<?php

include_classes([
    'parcelforupickuppoint.class',
    'parcelforupickuppointfilter.class',
    'parcelforudropoffpoint.class',
    'parcelforudropoffpointfilter.class'
]);
include_classes([
    'googledistancematrix.class'
        ], 'labels');

class parcelForYou implements CarrierService {

    private $pdf;
    private $serviceValues = null;
    private $constants = null;
    private $user = null;
    private $country = null;
    private $booking_file = null;
    private $record_array = null;
    private $trackingServiceId = null;
    private $trackingAgentId = null;

    public function __construct() {
        
    }

    public function validation(Consignment $consignment, Services $service, Country $country) {
        $returnOutput = array();
        if (trim($consignment->getEmail()) == "") {
            $returnOutput[] = "Please enter email address.";
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
         * Service COnstant
         */
        $serviceAgentConstantFilter = new ServiceConstantValueFilter();
        $serviceAgentConstantFilter->addFilter("service_id = '" . $consignment->getServiceId() . "' AND agent_id = '" . $consignment->getAgentId() . "' ");
        $serviceAgentConstant = $serviceAgentConstantFilter->getConstantList("c.constant 'constant_name', scv.constant_value 'constant_value'");
        if (count($serviceAgentConstant) > 0) {
            foreach ($serviceAgentConstant as $serviceAgentConstantData) {
                $this->constants[$serviceAgentConstantData->getConstantName()] = $serviceAgentConstantData->getConstantValue();
            }
        }
        if (trim(@$this->constants['PACKETA_API_PASSWORD']) == '' || trim(@$this->constants['PACKETA_ESHOP'] == '')) {
            $output['STATUS'] = 'ERROR';
            $output['MESSAGE'] = "Please check service constants. Please contact to itsupport@oneworldexpress.com";
            return $output;
        }

        if ($this->serviceValues->getCode() == "STP4UEXAM") {
            $output = $this->btocEuropeLabel($consignment);
        } else {
            $output = $this->apiLabel($consignment);
            //$output = $this->ediLabel($consignment);
        }
        return $output;
    }

    public function tracking($trackingNumber, $trackBy = 'parcel', $EDI = false) {
        include_once(BASE_PATH . "includes/labels/parcelforyoutrackingstatus.class.php");
        $tracking = new Tracking();
        $packetTrackingRequest = "<packetTracking>
                                        <apiPassword>69f170ac506c6ab79487170a5593f413</apiPassword>
                                        <packetId>" . $trackingNumber . "</packetId>
                                    </packetTracking>";

        $trackingResponse = $this->curlRequest($packetTrackingRequest);

        if (strtolower($trackingResponse->status) == "ok") {
            $trackingArray = $trackingResponse->result->record;
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
            $trackingDataFilterObj->addFilter("carrier_code not in ('','1','999')");
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

            if (count($trackingArray) > 0 && $entityId > 0) {
                $parcelEntity = new Parcel($entityId);
                $finalStatusCode = $parcelEntity->getParcelStatusCode();
                // print_r($trackingArray); 
                foreach ($trackingArray as $event) {
                    print_r($event);
                    $DateTime = date("Y-m-d H:i:s", strtotime(trim($event->dateTime)));
                    $EventCode = $event->statusCode;
                    $EventDescription = ParcelforYouTrackingStatus::$parcelforyou_status_code[$EventCode];

                    if (strpos($EventDescription, 'branchId')) {
                        $EventDescription = str_replace('branchId', $event->branchId, $EventDescription);
                    }

                    if (strpos($EventDescription, 'destinationBranchId')) {
                        $EventDescription = str_replace('destinationBranchId', $event->destinationBranchId, $EventDescription);
                    }

                    if (strpos($EventDescription, 'externalTrackingCode')) {
                        $EventDescription = str_replace('externalTrackingCode', $event->externalTrackingCode, $EventDescription);
                    }

                    $ServiceAreaDescription = $event->codeText;

                    // Dont enter any other event code if it is against 148 Event Code
                    if ($carrierCodeCarrierReceived == $EventCode) {
                        continue;
                    }

                    $deliveredArray = array('5');

                    ////////////////////// Carrier Received ////////////////////////////////
                    if ($carrierReceivedCheck == 1 && $EventCode != '1' && $EventCode != '999') {
                        $spTrackingStatus = '148';
                        $carrierReceivedCheck = 0;
                    } else {
                        $spTrackingStatus = ParcelforYouTrackingStatus::getOweStatusCode($EventCode);
                    }
                    ////////////////////// Carrier Received ////////////////////////////////

                    $tracking->saveTrackPoint($entityId, $trackBy, $DateTime, $trackingNumber, $spTrackingStatus, $ServiceAreaDescription, $EventCode, $EventDescription, $deliveredArray, $finalStatusCode);
                }
                $tracking->saveConsignmentTrackingStatus($trackingNumber, 'ParcelforYouTrackingStatus');
            }
        }
    }

    public function sendData($tracking_numbers = array()) {
        
    }

    public function manifest($consignment) {
        
    }

    public function preAdvice($consignment) {
        
    }

    public function btocEuropeLabel(Consignment $consignment) {
        $this->country = new Country($consignment->getCountryId());

        $parcel_list = $consignment->getParcels();
        $parcel_idx = 0;
        $senderCountry = new Country($consignment->getSenderCountryId());
        $requestXML = '<?xml version="1.0" encoding="UTF-8"?>
                        <Prealert>
                        <AuthenticationKey>'.$this->constants['PACKETA_API_PASSWORD'].'</AuthenticationKey>
                        <LayoutType>P01</LayoutType>
                        <LayoutVersion>2.4</LayoutVersion>
                        <LayoutPlatform>L01</LayoutPlatform>
                        <PrealertReference>' . $consignment->getHawb() . '</PrealertReference>
                        <Shipment>
                            <OrderNumber>' . $consignment->getHawb() . '6</OrderNumber>
                            <OrderReference />
                            <OrderContent />
                            <ShippingMethod>PARCELPLUS</ShippingMethod>
                            <CountryCodeOrigin>NLD</CountryCodeOrigin>
                            <PurchaseDate>' . date("Y-m-d") . '</PurchaseDate>
                            <Currency>' . $consignment->getCurrency() . '</Currency>
                            <ShippingCosts />
                            <HandlingCosts />';
                            //<CustomsService>DDU</CustomsService>
        $requestXML .= '<TYPNumber />
                            <ShipperAddress>
                                <CompanyName>PARCEL4YOU</CompanyName>
                                <AddressLine1>PARCEL4YOU P/S , Poul Larsens Vej 8C</AddressLine1>
                                <AddressLine2></AddressLine2>
                                <HouseNumber></HouseNumber>
                                <HouseNumberExtension></HouseNumberExtension>
                                <CityOrTown>Silkeborg</CityOrTown>
                                <StateOrProvince></StateOrProvince>
                                <ZIPCode>8600</ZIPCode>
                                <CountryCode>DNK</CountryCode>
                                <EORI>' . $consignment->getEoriNumber() . '</EORI>
                                <VAT>' . $consignment->getVatNumber() . '</VAT>
                            </ShipperAddress>
                            <ShipmentAddress>
                                <AddressType>DL1</AddressType>
                                <ConsigneeName>' . $consignment->getContact() . '</ConsigneeName>
                                <CompanyName>' . $consignment->getCompany() . '</CompanyName>
                                <Street>' . $consignment->getAddressLine1() . '</Street>
                                <AdditionalAddressInfo>' . $consignment->getAddressLine2() . '</AdditionalAddressInfo>
                                <HouseNumber />
                                <HouseNumberExtension />
                                <CityOrTown>' . $consignment->getCity() . '</CityOrTown>
                                <StateOrProvince />
                                <ZIPCode>' . $consignment->getPostcode() . '</ZIPCode>
                                <CountryCode>' . $this->country->getIso3() . '</CountryCode>
                            </ShipmentAddress>';
        if (count($parcel_list) > 0) {
            $parcelCount = 0;
            foreach ($parcel_list as $parcel) {
                $parcelCount++;
                $requestXML .= '<ShipmentPackage>
                                <PackageNumber>' . $parcelCount . '</PackageNumber>
                                <PackageBarcode>'.$consignment->getHawb().'</PackageBarcode>
                                <PackageWeight>' . ($parcel->getWeight() * 1000) . '</PackageWeight>
                                <DimensionHeight>' . number_format($parcel->getHeight(), 0) . '</DimensionHeight>
                                <DimensionWidth>' . number_format($parcel->getWidth(), 0) . '</DimensionWidth>
                                <DimensionLength>' . number_format($parcel->getLength(), 0) . '</DimensionLength>
                            </ShipmentPackage>';

                $parcelDescription = json_decode($parcel->getDescription());
                $parcelCountry = json_decode($parcel->getCommodityCode());
                $parcelQty = json_decode($parcel->getQty());
                $parcelValue = json_decode($parcel->getItemValue());
                $parcelHscode = json_decode($parcel->getHsCode());
                $parcelSku = json_decode($parcel->getItemSku());
                $parcelWeight = json_decode($parcel->getPWeight());
                $count = count($parcelDescription);
                if (count($parcelDescription) > 0) {
                    foreach ($parcelDescription as $key => $desc) {
                        $requestXML .= '<ShipmentContentCustoms>
                                <PackageNumber>' . $parcelCount . '</PackageNumber>
                                <SKUCode>' . $parcelSku[$key] . '</SKUCode>
                                <SKUDescription>' . $desc . '</SKUDescription>
                                <Quantity>' . $parcelQty[$key] . '</Quantity>
                                <Price>' . $parcelValue[$key] . '</Price>
                                <Category />
                                <ImageUrl />
                            </ShipmentContentCustoms>';
                    }//<HSCode>' . $parcelHscode[$key] . '</HSCode>                                 <TaricCode></TaricCode>
                } else {
                    $requestXML .= '<ShipmentContentCustoms>
                                <PackageNumber>' . $parcelCount . '</PackageNumber>
                                <SKUCode>000000</SKUCode>
                                <SKUDescription>' . $consignment->getDescription() . '</SKUDescription>
                                <Quantity>' . $consignment->getNumberOfPieces() . '</Quantity>
                                <Price>' . $consignment->getValue() . '</Price>
                                <Category />
                                   <ImageUrl />
                            </ShipmentContentCustoms>';
                }
            }
        }
        $requestXML .= '<ShipmentContact>
                                <PhoneNumber>' . $consignment->getTelephone() . '</PhoneNumber>
                                <SMSNumber />
                                <EmailAddress>' . $consignment->getEmail() . '</EmailAddress>
                                <PersonalNumber />
                            </ShipmentContact>
                        </Shipment>
                        <PrealertValidation>
                        <TotalShipments>1</TotalShipments>
                        <MailAddressConfirmation />
                        <MailAddressError />
                        <Timezone />
                    </PrealertValidation>
                </Prealert>';
        echo "<pre>";

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://prealert-test.customer-pages.com/',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $requestXML,
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/xml'
            ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);
        $xmlArray = (array) simplexml_load_string($response);
        $consignment->setApiData(print_r($requestXML, true), print_r($xmlArray, true), 'Pre-alert');
        if ($xmlArray['Code'] == 3000) {
            $output['STATUS'] = "ERROR";
            $output['MESSAGE'] = $xmlArray['Details'];
            return $output;
        }

        if ($xmlArray['Result'] == "OK") {
            $shipmentDetails = $xmlArray['Shipment'];
            $barcodeNumber = (string) $shipmentDetails->TYPNumber;
            foreach ($parcel_list as $parcel) {
                $parcel->setTrackingNumber($barcodeNumber);
                $parcel->save();
            }
            $pdf = new PdfBase(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
            $this->pdf = $pdf;

            $this->pdf->SetPrintFooter(false);
            $this->pdf->SetFooterMargin(0);
            $this->pdf->SetAutoPageBreak(false, 0);
            $page_size = array(150, 104);
            $this->pdf->AddPage("P", $page_size);
            $this->exAmsterdamLabel($consignment, $barcodeNumber);
            /*
              $this->pdf->Rect(3, 5, 58, 20);
              $this->pdf->Rect(65, 5, 35, 20);
              $this->pdf->Rect(3, 27, 58, 10);
              $this->pdf->Rect(65, 27, 35, 10);
              $this->pdf->Rect(3, 38, 58, 8);
              $this->pdf->Rect(3, 47, 58, 25);
              $this->pdf->Rect(3, 73, 85, 25);
              $this->pdf->Rect(3, 99, 85, 26);
              $this->pdf->Rect(3, 127, 85, 22);

              $image = realpath("../images/b2ceurope.jpg");
              $this->pdf->image($image, 5, 6, 40, 18);

              $companyimage = realpath("../images/logo.png");
              $this->pdf->image($companyimage, 65, 5, 30, 18);

              $this->pdf->setFont("Arial", "", 10);
              $this->pdf->Text(5, 28, "Order Number");
              $this->pdf->Text(5, 32, $consignment->getHawb());
              $this->pdf->Text(65, 28, "Shipping Method");
              $this->pdf->Text(65, 32, "PARCEL PLUS");
              $this->pdf->Text(5, 39, "TYP Number");
              $this->pdf->Text(5, 42, "12345678");

              $this->pdf->Text(5, 48, "Shipper");
              $this->pdf->Text(5, 52, "One World Express Ltd");
              $this->pdf->Text(5, 56, "One World House");
              $this->pdf->Text(5, 60, "Pump Lane");
              $this->pdf->Text(5, 64, "Hayes, Middlesex");
              $this->pdf->Text(5, 68, "UB3 3NB");

              $this->pdf->Text(5, 74, "Consignee");
              $this->pdf->Text(5, 78, $consignment->getCompany());
              $this->pdf->Text(5, 82, $consignment->getContact());
              $this->pdf->Text(5, 86, $consignment->getAddressLine1() . " " . $consignment->getAddressLine2() . " " . $consignment->getAddressLine3());
              $this->pdf->Text(5, 90, $consignment->getCity() . " " . $consignment->getPostcode());
              $this->pdf->Text(5, 94, $this->country->getIso());


              $style = array(
              'position' => '',
              'align' => 'C',
              'stretch' => true,
              'fitwidth' => true,
              'cellfitalign' => '5',
              'border' => false,
              'hpadding' => '0',
              'vpadding' => '0',
              'fgcolor' => array(0, 0, 0),
              'bgcolor' => false, //array(255,255,255),
              'text' => true
              );
              $this->pdf->write1DBarcode($consignment->getHawb(), 'C128', 5, 101, 95, 20, '', $style, '');
              $this->pdf->write1DBarcode($barcodeNumber, 'C128', 5, 128, 95, 16, '', $style, ''); */
            $licence_plate_array[] = $barcodeNumber;
            $this->pdf->IncludeJS("print();");
            $this->pdf->Output("../_assets/pdf/" . date('Y_m_d') . '/' . $consignment->getId() . ".pdf", "F");
            $output['STATUS'] = 'SUCCESS';
            $output['LABEL'] = date('Y_m_d') . '/' . $consignment->getId() . ".pdf";
            $output['TRACKING_NUMBER'] = $licence_plate_array;
            return $output;
        }
    }

    private function exAmsterdamLabel(Consignment $consignment, $licence_plate) {

        $this->pdf->line(3, 51, 98, 51);
        $this->pdf->line(3, 3, 98, 3);
        $this->pdf->line(98, 3, 98, 23.8);
        $this->pdf->line(3, 3, 3, 30);
        $this->pdf->line(35, 3, 35, 23.8);
        $this->pdf->Line(46, 51, 46, 60);

        $this->pdf->line(3, 24, 98, 24);
        $this->pdf->line(3, 24, 3, 115);
        $this->pdf->line(3, 105, 98, 105);
        $this->pdf->line(98, 24, 98, 115);
        $this->pdf->line(3, 60, 98, 60);
        $this->pdf->line(3, 70, 3, 145);
        $this->pdf->line(98, 70, 98, 145);
        $this->pdf->line(3, 133, 98, 133);
        $this->pdf->line(3, 113, 3, 146);
        $this->pdf->line(3, 146, 98, 146);
        $this->pdf->line(98, 113, 98, 146);
        //  $this->pdf->line(30, 133, 30, 146);

        $logo = SETTING_URL . 'images/Parcel4you_logo.png';
        $this->pdf->image($logo, 40, 4, 40, 15);
        $this->pdf->setFont("helvetica", "L", 9);
        //$this->pdf->Text(45, 18.5, "www.oneworldexpress.com");


        $this->pdf->setFont("helvetica", "b", 13);
        //  $this->pdf->Text(5, 5, "INTERNATIONAL");
        $this->pdf->Text(6.5, 10, "PARCEL");
        $this->pdf->Text(8.5, 15, "PLUS");


        $this->pdf->setFont("helvetica", "b", 10);


        $this->pdf->Text(50, 53, "Weight: " . $consignment->getWeight());
        $piece = "Pieces: " . $consignment->getNumberPieces();
        $this->pdf->Text(5, 53, $piece);

        $this->pdf->setFont("helvetica", "b", 12);
        $destinationWarehouseId = $consignment->getDestinationWarehouseId();

        $style = array(
            'position' => 'C',
            'align' => 'C',
            'stretch' => true,
            'fitwidth' => true,
            'cellfitalign' => '',
            'border' => false,
            'hpadding' => '1',
            'vpadding' => '0',
            'fgcolor' => array(0, 0, 0),
            'bgcolor' => false, //array(255,255,255),
            'text' => true,
            'font' => 'helvetica',
            'fontsize' => 8,
            'stretchtext' => 4
        );



        $this->pdf->write1DBarcode($consignment->getHawb(), 'C128', 5, 26, 95, 20, '0.4', $style, 'N');
        //    $this->pdf->write1DBarcode($licence_plate, 'C128', 7, 18, '', 40, 2, $style, '');
        $this->pdf->write1DBarcode($licence_plate, 'C128', 5, 110, 95, 16, 0.5, $style, 'N');


        $company = $consignment->getCompany();
        $address1 = $consignment->getAddressLine1();
        $address2 = $consignment->getAddressLine2();
        $address3 = $consignment->getAddressLine3();
        $contact = $consignment->getContact();
        $city = $consignment->getCity();
        $postcode = $consignment->getPostcode();
        $tel = $consignment->getTelephone();
        $country = $this->country->getName();
        $this->pdf->Text(5, 62, "Delivery Details:");
        $this->pdf->setFont("helvetica", "", 10);
        $this->pdf->Text(5, 67, $consignment->getHawb());
        $this->pdf->Text(5, 71, $company);
        $this->pdf->Text(5, 75, $contact);
        $this->pdf->Text(5, 79, $address1);
        $this->pdf->Text(5, 83, $address2);
        $this->pdf->Text(5, 87, $address3);
        $this->pdf->Text(5, 91, $city);
        $this->pdf->Text(5, 95, $postcode);
        $this->pdf->Text(55, 99, "Tel: " . $tel);


        $this->pdf->setFont("helvetica", "B", 11);
        $this->pdf->Text(5, 99, $country);
        $this->pdf->setFont("helvetica", "L", 11);
        $this->pdf->Text(55, 95, $consignment->getReference());

        $this->pdf->setFont("helvetica", "B", 8);
        $this->pdf->Text(5, 133, "Return to:");
        $this->pdf->setFont("helvetica", "L", 8);
        $this->pdf->Text(5, 136, "PARCEL4YOU P/S , Poul Larsens Vej 8C");
        $this->pdf->Text(5, 139, "8600 Silkeborg");
        $this->pdf->Text(5, 142, "Denmark");
    }

    /*
     * Not using below function and its connected function. It was used before for PUDO but its change via API
     */

    public function oldediLabel(Consignment $consignment) {
        $parcel_list = $consignment->getParcels();
        $parcel_idx = 0;
        $pdf = new PdfBase(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $this->pdf = $pdf;
        // Generate label for each parecel
        foreach ($parcel_list as $parcel) {
            $resultArray = $this->getTrackingNumber($consignment);
            print_r($resultArray);
            if (trim($resultArray['STATUS']) == 'ERROR')
                return $resultArray;
            else {
                $licence_plate = $resultArray['MESSAGE'];
            }
            $parcel->setTrackingNumber($licence_plate);
            $parcel->save();
            //} else {
            //    $licence_plate = $parcel->getTrackingNumber();
            //}
            $licence_plate_array[$parcel_idx] = $licence_plate;

            $this->pdf->SetPrintFooter(false);
            $this->pdf->SetFooterMargin(0);
            $this->pdf->SetAutoPageBreak(false, 0);
            $page_size = array(200, 100);
            $this->pdf->AddPage("L", $page_size);
            $this->addWayBill($consignment, $licence_plate);
            ++$parcel_idx;
        }

        $this->pdf->IncludeJS("print();");
        $this->pdf->Output("../_assets/pdf/" . date('Y_m_d') . '/' . $consignment->getId() . ".pdf", "F");
        $output['STATUS'] = 'SUCCESS';
        $output['LABEL'] = date('Y_m_d') . '/' . $consignment->getId() . ".pdf";
        $output['TRACKING_NUMBER'] = $licence_plate_array;
        return $output;
    }

    private function apiLabel(Consignment $consignment) {

        $output = array();
        $errormessage = "";

        $AddressSplit = GenericFunctions::getDoorNumber($consignment->getAddressLine1());
        $number = $AddressSplit['number'];
        if ($number == "") {
            $steet1 = $consignment->getAddressLine1();
        } else {
            $number = $AddressSplit['number'] . $AddressSplit['numberAddition'];
            $steet1 = $AddressSplit['street'];
        }
        if ($this->serviceValues->getCode() == "") {
            $addressId = $consignment->getRoutingCodeEur();
        } else {
            $addressId = $this->getAddressId($this->country->getIso());
        }
        $customAttribute = "";
        if ($this->country->getIso() == "UA") {
            $currency = new CurrencyFilter();
            $currency->addFieldFilter("rightsymbol", "EUR");
            $currencyList = $currency->getColumnList("currency_converter('" . $consignment->getCurrency() . "','EUR'," . $consignment->getValue() . ") as currencyname");
            if (count($currencyList) > 0) {
                $eurValue = $currencyList[0]->getCurrencyName();
            }

            $senderCountry = new Country($consignment->getSenderCountryId());
            $customAttribute = "
                                <customsDeclaration>
                                    <deliveryCostEur>" . number_format($eurValue, 2) . "</deliveryCostEur>
                                    <deliveryCost>" . number_format($consignment->getValue(), 2) . "</deliveryCost> 
                                     <items>
                                        <item>
                                            <customsCode>94013000</customsCode>
                                            <valueEur>" . number_format($eurValue, 2) . "</valueEur>
                                            <value>" . number_format($consignment->getValue(), 2) . "</value>
                                            <productEan>1234456787980</productEan>
                                            <productNameEn>" . $consignment->getDescription() . "</productNameEn>
                                            <productName>" . $consignment->getDescription() . "</productName>
                                            <unitsCount>" . $consignment->getNumberPieces() . "</unitsCount>    
                                            <countryOfOrigin>" . $senderCountry->getIso() . "</countryOfOrigin>
                                            <currency>" . $consignment->getCurrency() . "</currency>
                                            <invoiceNumber>999999</invoiceNumber>
                    			    <invoiceIssueDate>" . date("Y-m-d") . "</invoiceIssueDate>
                    			    <weight>" . $consignment->getWeight() * 1000 . "</weight>
                    			    <isFoodBook>false</isFoodBook>
                    			    <isVoc>false</isVoc>    
                                        </item>
                                     </items>
                                </customsDeclaration>";
        }
        //69f170ac506c6ab79487170a5593f413   muj-eshop.cz
        $createPacketRequest = "<createPacket>
                                <apiPassword>" . $this->constants["PACKETA_API_PASSWORD"] . "</apiPassword>
                                <packetAttributes>
                                    <number>" . $consignment->getId() . "</number>
                                    <name>" . $consignment->getContact() . "</name>
                                    <surname>" . $consignment->getContact() . "</surname>
                                    <email>" . $consignment->getEmail() . "</email>
                                    <phone>" . $consignment->getTelephone() . "</phone>
                                    <addressId>" . $addressId . "</addressId>
                                    <street>$steet1</street>
                                    <houseNumber>$number</houseNumber>
                                    <city>" . $consignment->getCity() . "</city>
                                    <zip>" . $consignment->getPostcode() . "</zip>
                                    <value>" . number_format($consignment->getValue(), 2) . "</value>
                                    <weight>" . number_format($consignment->getWeight(), 0) . "</weight>
                                    <eshop>" . $this->constants["PACKETA_ESHOP"] . "</eshop>" .
                $customAttribute
                . "
                                </packetAttributes>                               
                            </createPacket>";
        $createPacketResponse = $this->curlRequest($createPacketRequest);
        $consignment->setApiData(print_r($createPacketRequest, true), print_r($createPacketResponse, true), "createPacket");
        if (strtolower($createPacketResponse->status) == "ok") {
            $barcode = $createPacketResponse->result->barcode;
            $packetId = $createPacketResponse->result->id;
            if ($this->country->getIso() != "UA" && $this->serviceValues->getCode() != "STP4UPUDO") {
                $packetCourierNoRequest = "
                                        <packetCourierNumber>
                                            <apiPassword>" . $this->constants["PACKETA_API_PASSWORD"] . "</apiPassword>
                                            <packetId>" . $barcode . "</packetId>
                                        </packetCourierNumber>";

                $packetCourierNoResponse = $this->curlRequest($packetCourierNoRequest);
                $consignment->setApiData(print_r($packetCourierNoRequest, true), print_r($packetCourierNoResponse, true), "packetCoueriNumber");
                if (strtolower($packetCourierNoResponse->status) == "ok") {
                    $courierId = $packetCourierNoResponse->result;
                    $packetCourierPdfRequest = "<packetLabelPdf>
                                                    <apiPassword>" . $this->constants["PACKETA_API_PASSWORD"] . "</apiPassword>
                                                    <packetId>" . $barcode . "</packetId>
                                                    <format>A6 on A6</courierNumber>
                                                    <offset>0</offset>
                                                </packetLabelPdf>";
                    $packetCourierLabelResponse = $this->curlRequest($packetCourierPdfRequest);
                    $consignment->setApiData(print_r($packetCourierPdfRequest, true), print_r($packetCourierLabelResponse, true), "packetCourierPdfRequest");
                    if (strtolower($packetCourierLabelResponse->status) == "ok") {
                        $base64pdf = base64_decode($packetCourierLabelResponse->result);
                        $outputfilename = "../_assets/pdf/" . date('Y_m_d') . '/' . $consignment->getId() . ".pdf";
                        file_put_contents($outputfilename, $base64pdf);
                    } else {
                        $errormessage .= $packetCourierLabelResponse->string . "<br />";
                        $output["STATUS"] = "ERROR";
                        $output["MESSAGE"] = $errormessage;
                        return $output;
                    }
                } else {

                    $errormessage .= $packetCourierNoResponse->string . "<br />";
                    $output["STATUS"] = "ERROR";
                    $output["MESSAGE"] = $errormessage;
                    return $output;
                }
            } else {
                $packetCourierPdfRequest = "<packetsLabelsPdf>
                                                    <apiPassword>" . $this->constants["PACKETA_API_PASSWORD"] . "</apiPassword>
                                                    <packetIds>
                                                        <id>" . $packetId . "</id>
                                                    </packetIds>
                                                    <format>A6 on A6</format>
                                                    <offset>0</offset>
                                                </packetsLabelsPdf>";
                $packetCourierLabelResponse = $this->curlRequest($packetCourierPdfRequest);
                $consignment->setApiData(print_r($packetCourierPdfRequest, true), print_r($packetCourierLabelResponse, true), "packetCourierPdfRequest");
                if (strtolower($packetCourierLabelResponse->status) == "ok") {
                    $base64pdf = base64_decode($packetCourierLabelResponse->result);
                    $outputfilename = "../_assets/pdf/" . date('Y_m_d') . '/' . $consignment->getId() . ".pdf";
                    file_put_contents($outputfilename, $base64pdf);
                } else {
                    $errormessage .= $packetCourierLabelResponse->string . "<br />";
                    $output["STATUS"] = "ERROR";
                    $output["MESSAGE"] = $errormessage;
                    return $output;
                }
            }

            $licence_plate_array[] = $barcode;
            $output["STATUS"] = "SUCCESS";
            $output['LABEL'] = date('Y_m_d') . '/' . $consignment->getId() . ".pdf";
            $output["TRACKING_NUMBER"] = $licence_plate_array;
        } else {
            $error = $createPacketResponse->detail->attributes->fault;

            foreach ($error as $e) {
                $errormessage .= $e->fault . "<br />";
            }

            $output["STATUS"] = "ERROR";
            $output["MESSAGE"] = $errormessage;
        }
        return $output;
    }

    private function getTrackingNumber(Consignment $consignment) {
        $createPacketRequest = "<createPacket>
                                <apiPassword>69f170ac506c6ab79487170a5593f413</apiPassword>
                                <packetAttributes>
                                    <number>" . $consignment->getId() . "</number>
                                    <name>" . $consignment->getContact() . "</name>
                                    <surname>" . $consignment->getContact() . "</surname>
                                    <email>" . $consignment->getEmail() . "</email>
                                    <addressId>21</addressId>
                                    <value>" . number_format($consignment->getValue(), 2) . "</value>
                                    <eshop>muj-eshop.cz</eshop>
                                </packetAttributes>
                            </createPacket>";
        $createPacketResponse = $this->curlRequest($createPacketRequest);
        $consignment->setApiData(print_r($createPacketRequest, true), print_r($createPacketResponse, true), "createPacket");
        if (strtolower($createPacketResponse->status) == "ok") {
            $barcode = $createPacketResponse->result->barcode;
            $output["STATUS"] = "SUCCESS";
            $output["MESSAGE"] = $barcode;
        } else {
            $error = $createPacketResponse->detail->attributes->fault;
            if (is_array($error)) {
                foreach ($error as $e) {
                    $errormessage .= $e->fault . "<br />";
                }
            } else {
                $errormessage = $error->fault;
            }
            $output["STATUS"] = "ERROR";
            $output["MESSAGE"] = $errormessage;
        }
        return $output;
    }

    private function getAddressId($countryIso) {
        $countryAddress = array();
        $countryAddress["CZ"] = 106;
        $countryAddress["SK"] = 131;
        $countryAddress["HU"] = 4159;
        $countryAddress["RO"] = 590;
        $countryAddress["BG"] = 4015;
        $countryAddress["PL"] = 1438;
        $countryAddress["SI"] = 4949;
        $countryAddress["HR"] = 4646;
        $countryAddress["UA"] = 1160;

        return $countryAddress[$countryIso];
    }

    private function getReturnCode() {
        $requestFileds = "<senderGetReturnRouting>
                            <apiPassword>69f170ac506c6ab79487170a5593f413</apiPassword>
                            <senderLabel>muj-eshop.cz</senderLabel>
                           </senderGetReturnRouting>";
        $responsearray = $this->curlRequest($requestFileds);


        if (strtolower($responsearray->status) == "ok") {
            $returncode1 = $responsearray->result->routingSegment[0];
            $returncode2 = $responsearray->result->routingSegment[1];
            $output["STATUS"] = "SUCCESS";
            $output["RETURNCODE1"] = $returncode1;
            $output["RETURNCODE2"] = $returncode2;
        } else {
            $errormessage = "";

            $errors = $responsearray->fault;

            $output["STATUS"] = "ERROR";
            $output["MESSAGE"] = $errors;
        }
        return $output;
    }

    private function curlRequest($requestXml) {


        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://www.zasilkovna.cz/api/rest",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => $requestXml,
            CURLOPT_HTTPHEADER => array(
                "Content-Type: application/xml"
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        $xmltoArray = simplexml_load_string($response);
        $jsonarray = json_encode($xmltoArray);
        $responsearray = json_decode($jsonarray);
        return $responsearray;
    }

    private function addWayBill(Consignment $consignment, $licence_plate) {

        $returnCode = $this->getReturnCode();

        if ($returnCode["STATUS"] = "SUCCESS") {
            $returncodeno1 = $returnCode["RETURNCODE1"];
            $returncodeno2 = $returnCode["RETURNCODE2"];
        }
        $this->pdf->setFont("helvetica", "", 14);
        $this->pdf->Text(5, 8, "Sender:");
        $this->pdf->Text(5, 15, "Oneworldexpress.com");
        $this->pdf->setFont("helvetica", "B", 14);
        $this->pdf->Text(5, 22, "obj. 132456");
        $this->pdf->setFont("helvetica", "", 14);
        $this->pdf->Text(5, 29, $returncodeno1);
        $this->pdf->Text(5, 36, $returncodeno2);

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
            'text' => false
        );
        $this->pdf->write1DBarcode($licence_plate, 'C128', 100, 5, '', 35, 2, $style, '');

        $barcodePart1 = substr($licence_plate, 0, 1);
        $barcodePart2 = substr($licence_plate, 1, 3);
        $barcodePart3 = substr($licence_plate, 4, 3);
        $barcodeLastPart = substr($licence_plate, -3);

        $this->pdf->setFont("helvetica", "", 14);
        $this->pdf->Text(125, 35, $barcodePart1 . "  " . $barcodePart2 . "  " . $barcodePart3);

        $this->pdf->setFont("helvetica", "b", 16);
        $this->pdf->Text(150, 34, $barcodeLastPart);

        $image = realpath("../images/logo-label-en.png");
        $this->pdf->image($image, 15, 60, 60);

        $this->pdf->setFont("helvetica", "", 14);
        $this->pdf->Text(100, 54, "Receiver:");
        $this->pdf->setFont("helvetica", "b", 16);
        $this->pdf->Text(100, 60, $consignment->getContact());


        $parcel4you = new parcelforuPickupPointFilter();
        $parcel4you->addFieldFilter("postcode", "37001");
        $plist = $parcel4you->getList();
        $this->pdf->SetTextColor(255, 255, 255);
        $this->pdf->setFont("helvetica", "B", 17);
        $this->pdf->setXY(100, 70);
        $this->pdf->cell(40, 10, $plist[0]->getLabelRouting(), 1, 1, 'C', 1);

        $this->pdf->SetTextColor(0, 0, 0);
        $this->pdf->SetFont('dejavusans', '', 16);
        // MultiCell($w, $h, $txt, $border=0, $align='J', $fill=0, $ln=1, $x='', $y='', $reseth=true, $stretch=0, $ishtml=false, $autopadding=true, $maxh=0)
        $this->pdf->MultiCell(80, 30, ($plist[0]->getName()), 0, 'L', 0, 1, 100, 82);
    }

    public function recycledShipment($consignment) {
        $output["STATUS"] = "SUCCESS";
        return $output;
    }

    public function getDropOffLocation($postcode, $iso, $city = '') {
        $latLngArray = GoogleDistanceMatrix::getLatitudeLongitude('', $city, $postcode, $iso);
        if ($latLngArray["STATUS"] == "ERROR") {
            return $latLngArray;
        }
        $lat = $latLngArray["LAT"];
        $lng = $latLngArray["LNG"];

        $parcelforyoufilter = new parcelforuPickupPointFilter();
        $listLocation = $parcelforyoufilter->getLatLng($lat, $lng);
        print_r($listLocation);
        exit;
        if (count($listLocation) > 0) {
            $dropoffDetails = array();

            foreach ($listLocation as $location) {

                $storedetail = array();
                $storedetail["lat"] = $location->getLatitude();
                $storedetail["lng"] = $location->getLongitude();
                $storedetail["companyname"] = $location->getCompany();
                $storedetail["addressline1"] = $location->getAddressLine1();
                $storedetail["addressline2"] = '';
                $storedetail["city"] = $location->getCity();
                $storedetail["postcode"] = $location->getPostcode();
                $storedetail["country"] = $location->getCountryIso();
                $storedetail["telephone"] = '';
                $storedetail["mon"] = $location->getMon();
                $storedetail["tue"] = $location->getTue();
                $storedetail["wed"] = $location->getWed();
                $storedetail["thu"] = $location->getThu();
                $storedetail["fri"] = $location->getFri();
                $storedetail["sat"] = $location->getSat();
                $storedetail["sun"] = $location->getSun();
                $storedetail["branchId"] = $location->getBranchId();

                $dropoffDetails[] = $storedetail;
            }

            return $dropoffDetails;
        }
    }

}
