<?php
class Aramex implements CarrierService {

    private $pdf;
    private $serviceValues = null;
    private $constants = null;
    private $country = null;
    private $user = null;
    private $agentValue = null;

    public function __construct() {
       
    }

    public function validation(Consignment $consignment, Services $service, Country $country) {
        $returnOutput = array();
        if ($consignment->getTelephone() == "") {
            $returnOutput[] = "Please enter telephone number.";    
        }
        if ($consignment->getEmail() == "") {
            $returnOutput[] = "Please enter Email Address.";    
        }
        
        return $returnOutput;
    }

    public function remoteareas($consignment, $carrierObject, $sender) {
        
    }

    public function label($consignment, $labelType = 'pdf', $size = ''){

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
        if (trim(@$this->constants['ARAMEX_SHIPPER_CONTACT']) == '' || trim(@$this->constants['ARAMEX_SHIPPER_ADDRESS_LINE_1']) == '' || trim(@$this->constants['ARAMEX_SHIPPER_CITY']) == '' 
                || trim(@$this->constants['ARAMEX_SHIPPER_POSTCODE']) == '' || trim(@$this->constants['ARAMEX_SHIPPER_COUNTRY']) == '' || trim(@$this->constants['ARAMEX_ACCOUNT_NUMBER']) == '' 
                || trim(@$this->constants['ARAMEX_ACCOUNT_PIN']) == '' || trim(@$this->constants['ARAMEX_USERNAME']) == '' || trim(@$this->constants['ARAMEX_PASSWORD']) == '' ) {
            $output['STATUS'] = 'ERROR';
            $output['MESSAGE'] = "Please check service constants. Please contact to itsupport@oneworldexpress.com";
            return $output;
        }

        $numbers = range(1, 20);
        shuffle($numbers);
        $uniqueValArr = array_slice($numbers, 0, 5);
        $uniqueVal = implode('', $uniqueValArr);

        $services = "";
        if ($this->serviceValues->getCode() == "STAMX0PPX") {
            $type_of_service = "PPX";
        } else if ($this->serviceValues->getCode() == "AMXINTDDP") {
            $type_of_service = "PPX";
            $services = "FRDM";
        } else {
            $type_of_service = "PDX";
        }


        $email = $consignment->getEmail();
       
        $country = trim(@$this->constants['ARAMEX_SHIPPER_COUNTRY']);
        if(is_numeric($country))
        {
            $countryObj = new Country($country);
            $countryIsoCode = $countryObj->getIso();
        }
        else
        {
            $countryFilterObj = new CountryFilter();
            $countryFilterObj->addFilter(" name = '".$country."'");
            $countryList = $countryFilterObj->getList();
            if(count($countryList) > 0)
            {
                $countryIsoCode = $countryList[0]->getIso();
            }
            else
            {
                $countryIsoCode = $country;
            }
         
        }
        $company = $consignment->getCompany();
        if(trim($company) == "")
        {
            $company = $consignment->getContact();
        }
        $params = array(
            'Shipments' => array(
                'Shipment' => array(
                    'Shipper' => array(
                        'Reference1' => trim(@$this->constants['ARAMEX_SHIPPER_CONTACT']),
                        'Reference2' => trim(@$this->constants['ARAMEX_SHIPPER_CONTACT']),
                        'AccountNumber' => trim(@$this->constants['ARAMEX_ACCOUNT_NUMBER']),
                        'PartyAddress' => array(
                            /* 'Line1' => 'Mecca St',
                              'Line2' => '',
                              'Line3' => '',
                              'City' => 'LONDON',
                              'StateOrProvinceCode' => '',
                              'PostCode' => 'UB3 3NB',
                              'CountryCode' => 'GB' */
                            'Line1' =>  trim(@$this->constants['ARAMEX_SHIPPER_ADDRESS_LINE_1']),//'Unit 39-45',
                            'Line2' =>  trim(@$this->constants['ARAMEX_SHIPPER_ADDRESS_LINE_2']),//'The Waterside Trading Centr',
                            'Line3' =>  trim(@$this->constants['ARAMEX_SHIPPER_ADDRESS_LINE_3']),//'Trumpers Way',
                            'City' =>  trim(@$this->constants['ARAMEX_SHIPPER_CITY']),//'London',
                            'StateOrProvinceCode' => '',
                            'PostCode' =>  trim(@$this->constants['ARAMEX_SHIPPER_POSTCODE']),//'W7 2QD',
                            'CountryCode' => $countryIsoCode//'GB'
                        ),
                        'Contact' => array(
                            'Department' => 'Operation Department',
                            'PersonName' => trim(@$this->constants['ARAMEX_SHIPPER_CONTACT']),
                            'Title' => '',
                            'CompanyName' => trim(@$this->constants['ARAMEX_SHIPPER_COMPANY']),
                            'PhoneNumber1' => trim(@$this->constants['ARAMEX_SHIPPER_TELEPHONE']),
                            'PhoneNumber1Ext' => '',
                            'PhoneNumber2' => '',
                            'PhoneNumber2Ext' => '',
                            'FaxNumber' => '',
                            'CellPhone' => trim(@$this->constants['ARAMEX_SHIPPER_TELEPHONE']),
                            'EmailAddress' => trim(@$this->constants['ARAMEX_SHIPPER_EMAIL']),
                            'Type' => ''
                        ),
                    ),
                    'Consignee' => array(
                        'Reference1' => $consignment->gethawb(),
                        'Reference2' => $consignment->getReference(),
                        'AccountNumber' => trim(@$this->constants['ARAMEX_ACCOUNT_NUMBER']),
                        'PartyAddress' => array(
                            'Line1' => $consignment->getAddressLine1(),
                            'Line2' => $consignment->getAddressLine2(),
                            'Line3' => $consignment->getAddressLine3(),
                            'City' => $consignment->getCity(),
                            'StateOrProvinceCode' => '',
                            'PostCode' => $consignment->getPostcode(),
                            'CountryCode' => $this->country->getIso()
                        ),
                        'Contact' => array(
                            'Department' => '',
                            'PersonName' => $consignment->getContact(),
                            'Title' => '',
                            'CompanyName' => $company,
                            'PhoneNumber1' => $consignment->getTelephone(),
                            'PhoneNumber1Ext' => '',
                            'PhoneNumber2' => '',
                            'PhoneNumber2Ext' => '',
                            'FaxNumber' => '',
                            'CellPhone' => $consignment->getTelephone(),
                            'EmailAddress' => $email,
                            'Type' => ''
                        ),
                    ),
                    'ThirdParty' => array(
                        'Reference1' => '',
                        'Reference2' => '',
                        'AccountNumber' => '',
                        'PartyAddress' => array(
                            'Line1' => '',
                            'Line2' => '',
                            'Line3' => '',
                            'City' => '',
                            'StateOrProvinceCode' => '',
                            'PostCode' => '',
                            'CountryCode' => ''
                        ),
                        'Contact' => array(
                            'Department' => '',
                            'PersonName' => '',
                            'Title' => '',
                            'CompanyName' => '',
                            'PhoneNumber1' => '',
                            'PhoneNumber1Ext' => '',
                            'PhoneNumber2' => '',
                            'PhoneNumber2Ext' => '',
                            'FaxNumber' => '',
                            'CellPhone' => '',
                            'EmailAddress' => '',
                            'Type' => ''
                        ),
                    ),
                    'Reference1' => $consignment->getHawb(),
                    'Reference2' => '',
                    'Reference3' => '',
                    'ForeignHAWB' => 'ABC ' . $uniqueVal,
                    'TransportType' => 0,
                    'ShippingDateTime' => time(),
                    'DueDate' => time(),
                    'PickupLocation' => 'Reception',
                    'PickupGUID' => '',
                    'Comments' => 'Shpt 0001',
                    'AccountingInstrcutions' => '',
                    'OperationsInstructions' => '',
                    'Details' => array(
                        'Dimensions' => array(
                            'Length' => 0,
                            'Width' => 0,
                            'Height' => 0,
                            'Unit' => 'cm',
                        ),
                        'ActualWeight' => array(
                            'Value' => $consignment->getWeight(),
                            'Unit' => 'Kg'
                        ),
                        'ProductGroup' => 'EXP',
                        'ProductType' => $type_of_service,
                        'PaymentType' => 'P',
                        'PaymentOptions' => '',
                        'Services' => $services,
                        'NumberOfPieces' => $consignment->getNumberPieces(),
                        'DescriptionOfGoods' => $consignment->getDescription(),
                        'GoodsOriginCountry' => 'GB',
                        'CashOnDeliveryAmount' => array(
                            'Value' => '',
                            'CurrencyCode' => ''
                        ),
                        'InsuranceAmount' => array(
                            'Value' => 0.1,
                            'CurrencyCode' => 'USD'
                        ),
                        'CollectAmount' => array(
                            'Value' => '',
                            'CurrencyCode' => 'USD'
                        ),
                        'CashAdditionalAmount' => array(
                            'Value' => 0.1,
                            'CurrencyCode' => 'USD'
                        ),
                        'CashAdditionalAmountDescription' => '',
                        'CustomsValueAmount' => array(
                            'Value' => $consignment->getValue(),
                            'CurrencyCode' => $consignment->getCurrency()
                        ),
                        'Items' => array(
                        )
                    ),
                ),
            ),
            /* 'ClientInfo' => array(
              'AccountCountryCode' => 'JO',
              'AccountEntity' => 'AMM',
              'AccountNumber' => '20016',
              'AccountPin' => '331421',
              'UserName' => 'testingapi@aramex.com',
              'Password' => 'R123456789$r',
              'Version' => '1.0'
              ), */
            'ClientInfo' => array(
                'AccountCountryCode' => $countryIsoCode,//'GB',
                'AccountEntity' => 'LON',
                'AccountNumber' => trim(@$this->constants['ARAMEX_ACCOUNT_NUMBER']), //100124
                'AccountPin' => trim(@$this->constants['ARAMEX_ACCOUNT_PIN']),//'868240',
                'UserName' =>  trim(@$this->constants['ARAMEX_USERNAME']),//'jas@mailoptions.co.uk',
                'Password' => trim(@$this->constants['ARAMEX_PASSWORD']),//'0412888$JHa',
                'Version' => '1.0'
            ),
            'Transaction' => array(
                'Reference1' => '001',
                'Reference2' => '',
                'Reference3' => '',
                'Reference4' => '',
                'Reference5' => '',
            ),
            'LabelInfo' => array(
                'ReportID' => 9201,
                'ReportType' => 'URL',
            ),
        );

        $parcel_list = $consignment->getParcels();
        if(count($parcel_list) > 0){
            foreach($parcel_list as $parcel){
                $parcelDescription = json_decode($parcel->getDescription());
                $parcelCountry = json_decode($parcel->getCommodityCode());
                $parcelQty = json_decode($parcel->getQty());
                $parcelValue = json_decode($parcel->getItemValue());
                $parcelHscode = json_decode($parcel->getHsCode());
                $parcelSku = json_decode($parcel->getItemSku());
                $parcelWeight = json_decode($parcel->getPWeight());
                $count = count($parcelDescription);
                if(count($parcelDescription) > 0){
                    foreach($parcelDescription as $key=>$desc){
                        $params['Shipments']['Shipment']['Details']['Items']['ShipmentItem'][] = array(
                            'PackageType' => 'Box',
                            'Quantity' => $parcelQty[$key],
                            'Weight' => array(
                                'Value' => $parcelWeight[$key],
                                'Unit' => 'Kg',
                            ),
                            'Comments' => '',
                            'Reference' => '',
                            'PiecesDimensions' => array(
                                'Dimensions' => array(
                                    'Length' => $parcel->getLength(),
                                    'Width' => $parcel->getWidth(),
                                    'Height' => $parcel->getHeight(),
                                    'Unit' => 'CM'
                                ),
                            ),
                            'CommodityCode' => $parcelHscode[$key],
                            'GoodsDescription' => $desc,
                            'CountryOfOrigin' => $parcelCountry[$key],
                            'CustomsValue' => array(
                                'CurrencyCode' => $consignment->getCurrency(),
                                'Value' => $parcelValue[$key]
                            ),
                            'ContainerNumber' => ''
                        );
                    }
                }
                else
                {
                    $params['Shipments']['Shipment']['Details']['Items']['ShipmentItem'][] = array(
                        'PackageType' => 'Box',
                        'Quantity' => $consignment->getNumberPieces(),
                        'Weight' => array(
                            'Value' => $consignment->getWeight() / $consignment->getNumberPieces(),
                            'Unit' => 'Kg',
                        ),
                        'Comments' => $consignment->getDescription(),
                        'Reference' => '',
                        'PiecesDimensions' => array(
                            'Dimensions' => array(
                                'Length' => $parcel->getLength(),
                                'Width' => $parcel->getWidth(),
                                'Height' => $parcel->getHeight(),
                                'Unit' => 'CM'
                            ),
                        ),
                        'CommodityCode' => '',
                        'GoodsDescription' => $consignment->getDescription(),
                        'CountryOfOrigin' => '',
                        'CustomsValue' => array(
                            'CurrencyCode' => $consignment->getCurrency(),
                            'Value' => $consignment->getValue() / $consignment->getNumberPieces()
                        ),
                        'ContainerNumber' => ''
                    );
                }
            }
        }
        else
        {
            $params['Shipments']['Shipment']['Details']['Items']['ShipmentItem'][] = array(
                'PackageType' => 'Box',
                'Quantity' => $consignment->getNumberPieces(),
                'Weight' => array(
                    'Value' => $consignment->getWeight(),
                    'Unit' => 'Kg',
                ),
                'Comments' => $consignment->getDescription(),
                'Reference' => '',
                'PiecesDimensions' => array(
                    'Dimensions' => array(
                        'Length' => '10',
                        'Width' => '10',
                        'Height' => '10',
                        'Unit' => 'CM'
                    ),
                ),
                'CommodityCode' => '000000',
                'GoodsDescription' => $consignment->getDescription(),
                'CountryOfOrigin' => '',
                'CustomsValue' => array(
                    'CurrencyCode' => $consignment->getCurrency(),
                    'Value' => $consignment->getValue()
                ),
                'ContainerNumber' => ''
            );
        }
        
               
        try {
        $soapClient = new SoapClient('../includes/labels/wsdl-aramex/shipping-services-api-v2-wsdl.wsdl', array('trace' => 1,  "exceptions" => 1));
        //$auth_call = $soapClient->call('CreateShipments', $params);
        $auth_call = $soapClient->CreateShipments($params);
        $consignment->setApiData(print_r($params, true), print_r($auth_call, true), 'CreateShipments');
            //echo $auth_call->Shipments->ProcessedShipment->Notifications; 
            if (isset($auth_call->Shipments->ProcessedShipment->Notifications->Notification)) {
                if (count($auth_call->Shipments->ProcessedShipment->Notifications->Notification) > 0) {

                    $message = '';
                    $error_array_status = $auth_call->Shipments->ProcessedShipment->Notifications->Notification;

                    if (is_array($error_array_status)) {
                        for ($i = 0; $i < count($error_array_status); $i++) {
                            $message .= $error_array_status[$i]->Code . "-" . $error_array_status[$i]->Message . ", ";
                            //$message.= $error_msg["Message"] .", ";
                        }
                    } else {
                        $message .= $error_array_status->Code . "-" . $error_array_status->Message . ", ";
                    }
                    $output['STATUS'] = 'ERROR';
                    $output['MESSAGE'] = $message;
                    return $output;

                }
            } else {
                $label_link = $auth_call->Shipments->ProcessedShipment->ShipmentLabel->LabelURL;
                
                $awb = $auth_call->Shipments->ProcessedShipment->ID;
                
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_AUTOREFERER, TRUE);
                curl_setopt($ch, CURLOPT_HEADER, 0);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_URL, $label_link);
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);

                $pdf_decoded = curl_exec($ch);
              
                $mergeFileName = '../_assets/pdf/' . date('Y_m_d') . '/' . $consignment->getId() . ".pdf";
                $pdf = fopen($mergeFileName, 'w');
                fwrite($pdf, $pdf_decoded);
                fclose($pdf);

              
                $parcel_list = $consignment->getParcels();
                $parcel_count = sizeof($parcel_list);
                foreach ($parcel_list as $parcel) {
                    $parcel->setTrackingNumber($awb);
                    $parcel->save();
                }

                $licence_plate_array[0] = $awb;             
                $output['STATUS'] = 'SUCCESS';
                $output['LABEL'] = date('Y_m_d') . '/' . $consignment->getId() . ".pdf";
                $output['TRACKING_NUMBER'] = $licence_plate_array;
                return $output;
            }

        } catch (SoapFault $fault) {
            $output['STATUS'] = 'ERROR';
            $output['MESSAGE'] = $fault->faultstring;
            return $output;
        }



        
    }

    public function tracking($trackingNumber, $trackBy = 'parcel', $EDI = false) 
   {
       $tracking = new Tracking();
       $deliveredArray = array('SH005');
       
       require_once('../includes/labels/aramextrackingstatus.class.php');
       require_once('../includes/3rdparty/nusoap/nusoap.php');
       
       $consignmentFilter = new ConsignmentFilter();    
       $consignmentFilter->addAwbAndHawbOrFilter($trackingNumber); 
       $consignment = $consignmentFilter->getColumnList('*');
       
       if(count($consignment)> 0)
       {
            $serviceAgentConstantFilter = new ServiceConstantValueFilter();
            $serviceAgentConstantFilter->addFilter("service_id = '" . $consignment[0]->getServiceId() . "' AND agent_id = '" . $consignment[0]->getAgentId() . "' ");
            $serviceAgentConstant = $serviceAgentConstantFilter->getConstantList("c.constant 'constant_name', scv.constant_value 'constant_value'");

            if (count($serviceAgentConstant) > 0) 
            {
                foreach ($serviceAgentConstant as $serviceAgentConstantData) {
                    $this->constants[$serviceAgentConstantData->getConstantName()] = $serviceAgentConstantData->getConstantValue();
                }
            }
        }
        $soapClient = new SoapClient('../includes/labels/wsdl-aramex/shipments-tracking-api-wsdl.wsdl');
        
        $trackingResult = array();
        
	$params = array(
		  'ClientInfo'  => array(
                                            'AccountCountryCode' => $this->constants['ARAMEX_ACCOUNT_COUNTRY_CODE'],
                                            'AccountEntity'      => $this->constants['ARAMEX_ACCOUNT_ENTITY'],
                                            'AccountNumber'      => $this->constants['ARAMEX_ACCOUNT_NUMBER'],
                                            'AccountPin'         => $this->constants['ARAMEX_ACCOUNT_PIN'],
                                            'UserName'           => $this->constants['ARAMEX_USERNAME'],
                                            'Password'           => $this->constants['ARAMEX_PASSWORD'],
                                            'Version'            => '1.0'
					),

		'Transaction' 	=> array(
                                            'Reference1'	 => '001' 
					),
		'Shipments'	=> array(
					    $trackingNumber
					)
	);
        
	// calling the method and printing results
	try 
        {
            $auth_call = $soapClient->TrackShipments($params);
            $trackingResponse = $auth_call->TrackingResults->KeyValueOfstringArrayOfTrackingResultmFAkxlpY->Value->TrackingResult;                                
            if (count($trackingResponse) > 0) 
            {                
                $entityId = 0;
                if ($trackBy == 'parcel') 
                {
                    $parcelObj = new ParcelFilter();
                    $parcelObj->addTrackingNumberFilter($trackingNumber);
                    $parcelDataArray = $parcelObj->getColumnList('p.id, p.tracking_number, p.consignment_id');
                    if (count($parcelDataArray) > 0) 
                    {
                        $parcelData = $parcelDataArray[0];
                        $entityId = $parcelData->getId();
                    }
                } 
                else if ($trackBy == 'shipment') 
                {
                    $shipmenObj = new ConsignmentFilter();
                    $shipmenObj->addawbFilter($trackingNumber);
                    $shipmentDataArray = $shipmenObj->getColumnList('c.awb');
                    if (count($shipmentDataArray) > 0) 
                    {
                        $shipmentData = $shipmentDataArray[0];
                        $entityId = $shipmentData->getId();
                    }
                }

                if ($entityId > 0) 
                {
                    $parcelEntity = new Parcel($entityId);
                    $finalStatusCode = $parcelEntity->getParcelStatusCode();

                    foreach ($trackingResponse as $event) 
                    {
                        $dateTime = explode('T',$event->UpdateDateTime);
                        $Date = date("Y-m-d", strtotime(trim($dateTime[0])));
                        $Time = trim($dateTime[1]);
                        $DateTime = $Date . " " . $Time;

                        $EventCode = $event->UpdateCode;
                        $EventDescription = $event->UpdateDescription;
                        //$ServiceAreaCode = $event['location'];
                        $ServiceAreaDescription = $event->UpdateLocation;
                        $Signatory = '';

                        $spTrackingStatus = AramexTrackingStatus::getOweStatusCode($EventCode);
                        $tracking->saveTrackPoint($entityId, $trackBy, $DateTime, $trackingNumber, $spTrackingStatus, $ServiceAreaDescription, 
                                                   $EventCode, $EventDescription, $deliveredArray, $finalStatusCode);
                    }
                    $tracking->saveConsignmentTrackingStatus($trackingNumber, 'AramexTrackingStatus');           
                }
            }       
	} 
        catch (SoapFault $fault) 
        {
            mail("kiran.iftikhar@oneworldexpress.com", "Aramex Tracking not working", $fault->faultstring);
	}
        return $trackingResult;
    }

    public function sendData($tracking_numbers = array()) {
        
    }

    public function manifest($consignment) {
        
    }

    public function preAdvice($consignment) {
        
    }
    
    public function recycledShipment($consignment) {
            $output["STATUS"]   =   "SUCCESS";
            return $output;
        }
    
}
