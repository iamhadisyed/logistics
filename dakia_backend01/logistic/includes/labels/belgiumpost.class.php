<?php

require_once(SETTING_DIR_REMOTE . "includes/3rdparty/Net/SFTP.php");

class BelgiumPost implements CarrierService {

    private $pdf;
    private $serviceValues = null;
    private $agentValues = null;
    private $constants = null;
    private $user = null;
    private $country = null;
    private $trackingServiceId = null;
    private $trackingAgentId = null;

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
       /* if ((trim(@$this->constants['KAAB_URL']) == '' || trim(@$this->constants['KAAB_USERNAME']) == '' || trim(@$this->constants['KAAB_PASSWORD']) == '') && $this->constants["INTEGRATION_TYPE"] == "API") {
            $output['STATUS'] = 'ERROR';
            $output['MESSAGE'] = "Please check service constants. Please contact to itsupport@oneworldexpress.com";
            return $output;
        }
*/
        if (trim(@$this->constants['INTEGRATION_TYPE']) == 'API') {
            $output = $this->apiLabel($this->constants, $consignment);
        }


        return $output;
    }

    private function apiLabel($constants, Consignment $consignment) {
        $this->country = new Country($consignment->getCountryId());
        $parcel = $consignment->getParcels();
        //header( "content-type: application/xml; charset=ISO-8859-15" );

         if($this->serviceValues->getCode() == "STBPMINIS"){
            $url = "https://api.landmarkglobal.com/v2/Ship.php";
            $region = "Client UK";
            $firstTag = "ShipRequest";
        }
        else
        {
            $url = "https://api.landmarkglobal.com/v2/Import.php";
            $region = "Landmark UK";
            $firstTag = "ImportRequest";
        }
        $dom = new DOMDocument("1.0", "ISO-8859-15");
        $dom->encoding = 'utf-8';
        $dom->xmlVersion = '1.0';
        $dom->formatOutput = true;
        $root = $dom->createElement($firstTag);

        $login = $dom->createElement('Login');
        $username = $dom->createElement('Username', 'Mail_Options_API');
        $login->appendChild($username);
        $password = $dom->createElement('Password', '226633A525544c@');
        $login->appendChild($password);
        $root->appendChild($login);

        $test = $dom->createElement("Test", "false");
        $root->appendChild($test);

        $clientId = $dom->createElement("ClientID", "1554");
        $root->appendChild($clientId);

        $AccountNumber = $dom->createElement("AccountNumber", "");
        $root->appendChild($AccountNumber);

        $Reference = $dom->createElement("Reference", $consignment->getHawb());
        $root->appendChild($Reference);

        $ShipTo = $dom->createElement("ShipTo");

        $Name = $dom->createElement("Name", $consignment->getContact());
        $ShipTo->appendChild($Name);

        $Attention = $dom->createElement("Attention", $consignment->getContact());
        $ShipTo->appendChild($Attention);

        $Address1 = $dom->createElement("Address1", $consignment->getAddressLine1());
        $ShipTo->appendChild($Address1);

        $Address2 = $dom->createElement("Address2", $consignment->getAddressLine2());
        $ShipTo->appendChild($Address2);

        $Address3 = $dom->createElement("Address3", $consignment->getAddressLine3());
        $ShipTo->appendChild($Address3);

        $City = $dom->createElement("City", $consignment->getCity());
        $ShipTo->appendChild($City);

        $State = $dom->createElement("State", $consignment->getState());
        $ShipTo->appendChild($State);

        $PostalCode = $dom->createElement("PostalCode", $consignment->getPostcode());
        $ShipTo->appendChild($PostalCode);

        $Country = $dom->createElement("Country", $this->country->getIso());
        $ShipTo->appendChild($Country);

        $Phone = $dom->createElement("Phone", $consignment->getTelephone());
        $ShipTo->appendChild($Phone);

        $Email = $dom->createElement("Email", $consignment->getEmail());
        $ShipTo->appendChild($Email);

        $ConsigneeTaxID = $dom->createElement("ConsigneeTaxID");
        $ShipTo->appendChild($ConsigneeTaxID);

        $root->appendChild($ShipTo);

        $ShippingLane = $dom->createElement("ShippingLane");
        $Region = $dom->createElement("Region", $region);
        $ShippingLane->appendChild($Region);
        $root->appendChild($ShippingLane);

        $ShipMethod = $dom->createElement("ShipMethod", $this->serviceValues->getCarrierServiceCode()); //LGINTBPVSP
        $root->appendChild($ShipMethod);

        $OrderTotal = $dom->createElement("OrderTotal", number_format($consignment->getValue(), 2));
        $root->appendChild($OrderTotal);

        $OrderInsuranceFreightTotal = $dom->createElement("OrderInsuranceFreightTotal", 0.00);
        $root->appendChild($OrderInsuranceFreightTotal);

        $ShipmentInsuranceFreight = $dom->createElement("ShipmentInsuranceFreight", 0.00);
        $root->appendChild($ShipmentInsuranceFreight);

        $ItemsCurrency = $dom->createElement("ItemsCurrency", $consignment->getCurrency());
        $root->appendChild($ItemsCurrency);

        $IsCommercialShipment = $dom->createElement("IsCommercialShipment", 0);
        $root->appendChild($IsCommercialShipment);
        
        $ProductLabel = $dom->createElement("ProduceLabel", true);
        $root->appendChild($ProductLabel);
        
        $LabelFormat = $dom->createElement("LabelFormat", "PDF");
        $root->appendChild($LabelFormat);

        $LabelEncoding = $dom->createElement("LabelEncoding", "LINKS");
        $root->appendChild($LabelEncoding);
       
        $VendorInformation = $dom->createElement("VendorInformation");
        $IOSSNo = $dom->createElement("IOSSNumber", $consignment->getIossNumber());
        $VendorInformation->appendChild($IOSSNo);
        $VendorName = $dom->createElement("VendorName", "Mail Options Ltd");
        $VendorInformation->appendChild($VendorName);

        $VendorAddress1 = $dom->createElement("VendorAddress1", "Unit 39-45, The Waterside Trading Centre,");
        $VendorInformation->appendChild($VendorAddress1);

        $VendorAddress2 = $dom->createElement("VendorAddress2", "Trumpers Way");
        $VendorInformation->appendChild($VendorAddress2);

        $VendorCity = $dom->createElement("VendorCity", "London");
        $VendorInformation->appendChild($VendorCity);

        $VendorState = $dom->createElement("VendorState");
        $VendorInformation->appendChild($VendorState);

        $VendorPostalCode = $dom->createElement("VendorPostalCode", "W7 2QD");
        $VendorInformation->appendChild($VendorPostalCode);

        $VendorCountry = $dom->createElement("VendorCountry", "UK");
        $VendorInformation->appendChild($VendorCountry);

        $VendorBusinessNumber = $dom->createElement("VendorBusinessNumber");
        $VendorInformation->appendChild($VendorBusinessNumber);
        $root->appendChild($VendorInformation);


        $count = 0;
        if (count($parcel) > 0) {
            foreach($parcel as $p) {
                if ($count == 0) {
                    $packages = $dom->createElement("Packages");
                    $Items = $dom->createElement("Items");
                }
                $Package = $dom->createElement("Package");
                $WeightUnit = $dom->createElement("WeightUnit", "KG");
                $Package->appendChild($WeightUnit);

                $Weight = $dom->createElement("Weight", number_format($p->getWeight(), 2));
                $Package->appendChild($Weight);

                $DimensionsUnit = $dom->createElement("DimensionsUnit", "CM");
                $Package->appendChild($DimensionsUnit);

                $Length = $dom->createElement("Length", number_format($p->getLength(), 2));
                $Package->appendChild($Length);

                $Width = $dom->createElement("Width", number_format($p->getWidth(), 2));
                $Package->appendChild($Width);

                $Height = $dom->createElement("Height", number_format($p->getHeight(), 2));
                $Package->appendChild($Height);

                $PackageReference = $dom->createElement("PackageReference");
                $Package->appendChild($PackageReference);

                $packages->appendChild($Package);
                $itemDescription = json_decode($p->getDescription());
                
                $itemSku = json_decode($p->getItemSku());
                $itemQty = json_decode($p->getQty());
                $itemValue = json_decode($p->getItemValue());
                $itemHscode = json_decode($p->getHscode());
                $itemCountry = json_decode($p->getCommodityCode());

                if (count($itemDescription) > 0) {
                    foreach ($itemDescription as $key => $desc) {
                        $Item = $dom->createElement("Item");
                        $Sku = $dom->createElement("Sku", $itemSku[$key]);
                        $Item->appendChild($Sku);

                        $Quantity = $dom->createElement("Quantity", $itemQty[$key]);
                        $Item->appendChild($Quantity);

                        $UnitPrice = $dom->createElement("UnitPrice", $itemValue[$key]);
                        $Item->appendChild($UnitPrice);

                        $Description = $dom->createElement("Description", $desc);
                        $Item->appendChild($Description);

                        $HSCode = $dom->createElement("HSCode", $itemHscode[$key]);
                        $Item->appendChild($HSCode);

                        $CountryOfOrigin = $dom->createElement("CountryOfOrigin", $itemCountry[$key]);
                        $Item->appendChild($CountryOfOrigin);
                        $Items->appendChild($Item);
                    }
                }
                $count++;
            }
        }
        $root->appendChild($packages);
        $root->appendChild($Items);

        $dom->appendChild($root);
        $xmlRequest = $dom->saveXML();
        
        //print_r($xmlRequest);
        $curl = curl_init();

       
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => 'RQXML='.$xmlRequest,
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/x-www-form-urlencoded'
            ),
        ));

        $response = curl_exec($curl);
        $responseArray = new SimpleXMLElement($response);
        curl_close($curl);
        $consignment->setApiData(print_r($xmlRequest, true), print_r($responseArray, true), 'createShipment');
        $status =  (string)$responseArray->Result->Success;
        
        if($status !== "false"){
            $licence_plate_array  = array();
            $packages = $responseArray->Result->Packages->Package;
            $trackingNo = $packages->TrackingNumber;
            
            foreach($trackingNo as $t){
                if (count($parcel) > 0) {
                    $parcel[0]->setTrackingNumber( (string)$t);
                    $parcel[0]->save();
                }
                $licence_plate_array[]=  (string)$t;
            }
            $labellink = $packages->LabelLink;
            $fileName = '../_assets/pdf/' . date('Y_m_d') . '/' . $consignment->getId() . ".pdf";
            $PDF_CONTENTS = file_get_contents($labellink);
            file_put_contents($fileName, $PDF_CONTENTS);
          
            $output['STATUS'] = 'SUCCESS';
            $output['LABEL'] = date('Y_m_d') . '/' . $consignment->getId() . ".pdf";
            $output['TRACKING_NUMBER'] = $licence_plate_array;
        }
        else
        {
            $errors = $responseArray->Errors->Error;
            foreach ($errors as $e){
                $e .= (string)$e->ErrorMessage . "<br />";
            }
            $output['STATUS'] = "ERROR";
            $output["MESSAGE"] = $e;
        }
        return $output;
    }
    
    public function setTrackingParams($serviceId, $agentId) {
        $this->trackingServiceId = $serviceId;
        $this->trackingAgentId = $agentId;
    }

    public function tracking($trackingNumber, $trackBy = 'parcel', $EDI = false) 
    {
        $tracking = new Tracking();
        $deliveredArray = array("500");

        if ($EDI == true && !empty($this->trackingServiceId) && !empty($this->trackingAgentId)) 
        {
            include_once(BASE_PATH . "includes/labels/belgiumposttrackingstatus.class.php");
            $serviceAgentConstantFilter = new ServiceConstantValueFilter();
            $serviceAgentConstantFilter->addFilter("service_id = '" . $this->trackingServiceId . "' AND agent_id = '" . $this->trackingAgentId . "' ");
            $serviceAgentConstant = $serviceAgentConstantFilter->getConstantList("c.constant 'constant_name', scv.constant_value 'constant_value'");

            if (count($serviceAgentConstant) > 0) {
                foreach ($serviceAgentConstant as $serviceAgentConstantData) {
                    $this->constants[$serviceAgentConstantData->getConstantName()] = $serviceAgentConstantData->getConstantValue();
                }
            }
          //  print_r($this->constants); die;
            $sftp_server = 'sftp.landmarkglobal.com';//trim($this->constants['BELGIUMPOST_TRACKING_SERVER']);
            $sftp_user = 'landmark1554user';//trim($this->constants['BELGIUMPOST_TRACKING_USERNAME']);
            $sftp_pass = 'QhfmnnnhxpzZGwS';//trim($this->constants['BELGIUMPOST_TRACKING_PASSWORD']);

            $localPath = SETTING_DIR_ASSETS . "tracking_data/BELGIUMPOST/";
            if(!file_exists($localPath))
            {
                @mkdir($localPath, 0777);
            }
            $remotePath = "/OUTBOUND/Tracking/";
            $remoteProcessedPath = "/OUTBOUND/processed/";
                
            $ftpConnection = new Net_SFTP($sftp_server, 2222);
            if (!$ftpConnection->login($sftp_user, $sftp_pass)) 
            {
                echo "SFTP Login Failed, check your username and passwor";
                exit;
            } 
                
            else 
            {                
                $getAllFiles = $ftpConnection->_list($remotePath);
           //     print_r(count($getAllFiles)); die;
                if (count($getAllFiles) > 0) 
                {
                    foreach ($getAllFiles as $keyFile => $valueFile) 
                    {
                        $fileName = $valueFile['filename'];
                        if (in_array($fileName, array('.', '..')))
                            continue;
                        $remoteFile = $remotePath . $fileName;                        
                        $remoteProcessedFile = $remoteProcessedPath . $fileName;
                        $localFile = $localPath . $fileName;
                        
                        $ftpConnection->get($remoteFile, $localFile);
                        $ftpConnection->rename($remoteFile, $remoteProcessedFile);    
                        
                        $handle = fopen($localFile, "r");
                        $header = 0; 
                        
                        if ($handle) 
                        {
                            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) 
                            {
                                if($header == 0)
                                {
                                    $header++;
                                    continue;
                                }
                                $trackingNumber = removeBomUtf8(str_replace('"', '', $data[2]));
                                
                                ////////////////////// Carrier Received ////////////////////////////////
                                $trackingDataFilterObj = new TrackingDataFilter();
                                $trackingDataFilterObj->addFieldFilter('    tracking_number', $trackingNumber);
                                $trackingDataFilterObj->addFilter("carrier_code not in ('','50','94')");
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

                                $dateTime = '';
                                $hawb = trim($data[1]);
                                $EventCode = $data[9];
                                $dateArray = explode('T', $data[11]);
                                $TimeArray = explode('-', $dateArray[1]);
                                $date = $dateArray[0];
                                $time = $TimeArray[0];
                                $DateTime = $date.' '.$time;
                                $EventDescription = $data[10];           
                                $ServiceAreaDescription = $data[12];

                                // Dont enter any other event code if it is against 148 Event Code
                                if ($carrierCodeCarrierReceived == $EventCode) {
                                    continue;
                                }

                                $spTrackingStatus = BelgiumPostTrackingStatus::getOweStatusCode($EventCode);
                                $entityId = 0;
                                $parcelObj = new ParcelFilter();
                                $parcelObj->addTrackingNumberFilter($trackingNumber);
                                $parcelDataArray = $parcelObj->getColumnList('p.id, p.tracking_number, p.consignment_id');
                                if (count($parcelDataArray) > 0) {
                                    $parcelData = $parcelDataArray[0];
                                    $entityId = $parcelData->getId();
                                }

                                if ($entityId > 0) {
                                    $parcelEntity = new Parcel($entityId);
                                    $finalStatusCode = $parcelEntity->getParcelStatusCode();
                                    ////////////////////// Carrier Received ////////////////////////////////
                                    if ($carrierReceivedCheck == 1 && $EventCode != '50' && $EventCode != '94') {
                                        $spTrackingStatus = '148';
                                        $carrierReceivedCheck = 0;
                                    } else {
                                        $spTrackingStatus = BelgiumPostTrackingStatus::getOweStatusCode($EventCode);
                                    }
                                    ////////////////////// Carrier Received ////////////////////////////////
                                    $tracking->saveTrackPoint($entityId, $trackBy, $DateTime, $trackingNumber, $spTrackingStatus, $ServiceAreaDescription, $EventCode, $EventDescription, $deliveredArray, $finalStatusCode);
                                    $tracking->saveConsignmentTrackingStatus($trackingNumber, 'BelgiumPostTrackingStatus');
                                }                                  
                                
                            }
                        }
                    }
                }
/*
                if (sizeof($arrfile) > 0) {
                    foreach ($arrfile as $filename) {
                        if ($filename == '.' || $filename == '..') {
                            continue;
                        }
                        $file_start = date('Ymd', strtotime(date("Ymd")));
                        if (strpos($filename, $file_start) !== false) {
                            $filename = str_replace("/OUT/", "", $filename);
                            $fp = fopen($ftp_local_path . $filename, 'w');
                            if (ftp_fget($conn_id, $fp, "/OUT/" . $filename, FTP_ASCII, FTP_AUTORESUME)) {
                                $handle = fopen($ftp_local_path . $filename, "r");
                                $ftp_local_path = SETTING_DIR_ASSETS . "tracking_data/ASENDIA/";
                                if ($handle) {
                                    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                                        $trackingNumber = removeBomUtf8(str_replace('"', '', $data[0]));
                                        ////////////////////// Carrier Received ////////////////////////////////

                                        $trackingDataFilterObj = new TrackingDataFilter();
                                        $trackingDataFilterObj->addFieldFilter('    tracking_number', $trackingNumber);
                                        $trackingDataFilterObj->addFilter("carrier_code not in ('','100','103')");
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

                                        $num = count($data);
                                        $dateTime = '';
                                        $trackingNumber = removeBomUtf8(str_replace('"', '', $data[0]));
                                        $hawb = trim($data[2]);
                                        $EventCode = $data[3];
                                        $date = str_replace('/', '-', $data[5]);
                                        $EventDescription = $data[4] . ' - ' . $data[6];

                                        if (strpos($date, "|") !== false) {
                                            $dateAndDesc = explode("|", $date);
                                            $date = $dateAndDesc[0];
                                            $DateTime = date("Y-m-d H:i:s", strtotime($date));
                                            $EventDescription = str_replace('"', "", $dateAndDesc[1]);
                                        } else {
                                            $DateTime = date("Y-m-d H:i:s", strtotime($date));
                                            $EventDescription = $data[4] . ' - ' . $data[6];
                                        }
                                        $ServiceAreaDescription = '';

                                        // Dont enter any other event code if it is against 148 Event Code
                                        if ($carrierCodeCarrierReceived == $EventCode) {
                                            continue;
                                        }

                                        $spTrackingStatus = AsendiaTrackingStatus::getOweStatusCode($EventCode);
                                        $entityId = 0;
                                        $parcelObj = new ParcelFilter();
                                        $parcelObj->addTrackingNumberFilter($trackingNumber);
                                        $parcelDataArray = $parcelObj->getColumnList('p.id, p.tracking_number, p.consignment_id');
                                        if (count($parcelDataArray) > 0) {
                                            $parcelData = $parcelDataArray[0];
                                            $entityId = $parcelData->getId();
                                        }

                                        if ($entityId > 0) {
                                            $parcelEntity = new Parcel($entityId);
                                            $finalStatusCode = $parcelEntity->getParcelStatusCode();
                                            ////////////////////// Carrier Received ////////////////////////////////
                                            if ($carrierReceivedCheck == 1 && $EventCode != '100' && $EventCode != '103') {
                                                $spTrackingStatus = '148';
                                                $carrierReceivedCheck = 0;
                                            } else {
                                                $spTrackingStatus = AsendiaTrackingStatus::getOweStatusCode($EventCode);
                                            }
                                            ////////////////////// Carrier Received ////////////////////////////////
                                            $tracking->saveTrackPoint($entityId, $trackBy, $DateTime, $trackingNumber, $spTrackingStatus, $ServiceAreaDescription, $EventCode, $EventDescription, $deliveredArray, $finalStatusCode);
                                            $tracking->saveConsignmentTrackingStatus($trackingNumber, 'AsendiaTrackingStatus');
                                        }
                                    } //end while
                                }
                            }
                        }
                    }
                }
                ftp_close($conn_id);*/
            }
        }
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
    public function recycledShipment($consignment) {
        $output["STATUS"] = "SUCCESS";
        return $output;
    }

}
