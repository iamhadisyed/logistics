<?php
include_classes([
    'Client',
    ], '3rdparty/labelary');
include_classes([
    'Base',
    'Printers'    
    ], '3rdparty/labelary/Endpoint');
include_classes([
    'pdfmerger',
    ], 'labels');

class AsendiaUk implements CarrierService {

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
        $returnOutput = array();
        $countryIso = $country->getIso();
        $customerPostcode = strtoupper(str_replace(' ', '', $consignment->getPostcode()));
        $customerPostcode = strtoupper(str_replace('-', '', $customerPostcode));
        $customerPostcode = (int) $customerPostcode;
        if ($countryIso == 'FR' && $customerPostcode >= 100 && $customerPostcode <= 200) {
            $returnOutput[] = "The service is not allowed for France postcode 00100 to 00200 postcode.";
        }

        if ($service->getCode() == "STASEPREM") {
            if (trim($consignment->getEmail()) == "") {
                $returnOutput[] = "Please enter email address.";
            } else if (trim($consignment->getTelephone()) == "") {
                $returnOutput[] = "Please enter telephone.";
            }
        }
        return $returnOutput;
    }

    public function remoteareas($consignment, $carrierObject, $sender) {
        $postCodeConsignment = $consignment->getPostcode();
        $customerPostcode = strtoupper(str_replace(' ', '', $postCodeConsignment));
        $customerPostcode = strtoupper(str_replace('-', '', $customerPostcode));
        $customerPostcode = (int) $customerPostcode;
        if ($country == 'FR' && $customerPostcode >= 100 && $customerPostcode <= 200) {
            $output['STATUS'] = 'ERROR';
            $output['MESSAGE'] = "The service is not allowed for France postcode 00100 to 00200 postcode.";
            return $output;
        }
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

        if(in_array($this->serviceValues->getCode(), array('STASEPRON', 'STASEPRBX', 'STASEPRPK', 'STASEECPK', 'STASEECBX'))){
            if(trim(@$this->constants['ASENDIAUK_PASSWORD']) == ''){
                $output['STATUS'] = 'ERROR';
                $output['MESSAGE'] = "Please check service constants. Please contact to itsupport@oneworldexpress.com";
                return $output;
            }
        }
        else if ((trim(@$this->constants['INTEGRATION_TYPE']) == '' || trim(@$this->constants['ASENDIAUK_WSDL']) == '' || trim(@$this->constants['ASENDIAUK_USERNAME']) == '' || trim(@$this->constants['ASENDIAUK_PASSWORD']) == '' || trim(@$this->constants['ASENDIAUK_NAMESPACE']) == '') && $this->serviceValues->getCode() != "STASEPRON") {
            $output['STATUS'] = 'ERROR';
            $output['MESSAGE'] = "Please check service constants. Please contact to itsupport@oneworldexpress.com";
            return $output;
        }

        if (trim(@$this->constants['INTEGRATION_TYPE']) == 'API') {
            if (in_array($this->serviceValues->getCode(), array('STASEPRON', 'STASEPRBX', 'STASEPRPK', 'STASEECPK', 'STASEECBX'))){
                $output = $this->apiAsendiaProntoLabel($consignment);
            } else {
                $output = $this->apiLabel($this->constants, $consignment);
            }
        }

        return $output;
    }

    private function apiLabel($constant, Consignment $consignment) {
        require_once('../includes/3rdparty/nusoap/nusoap.php');
        $output = array();
        // const WSDL = 'https://asendia-delta.mpm.metapack.com/BlackBox/BlackBox.svc?wsdl';
        $WSDL = $constant["ASENDIAUK_WSDL"]; //'https://asendia.mpm.metapack.com/BlackBox/BlackBox.svc?wsdl';
        $api_userName = $constant["ASENDIAUK_USERNAME"]; //'oneworld.api';
        $api_password = $constant["ASENDIAUK_PASSWORD"]; // 'lO@lmkhs3686xXk#p@w34'; //KAAB2016

        $client = new nusoap_client($WSDL);
        $err = $client->getError();
        if ($err) {
            $output["STATUS"] = "ERROR";
            $output["MESSAGE"] = 'Constructor error' . $err;
            return $output;
        } else {
            $client->soap_defencoding = 'utf-8';
            $client->useHTTPPersistentConnection(); // Uses http 1.1 instead of 1.0

            $soapaction = $constant["ASENDIAUK_NAMESPACE"];  //"http://xlogics.eu/blackbox/BlackBoxContract/PrintParcel";

            $request_body = '<s:Envelope xmlns:s="http://schemas.xmlsoap.org/soap/envelope/">
							<s:Header>
								<wsse:Security xmlns:wsse="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd">
									<wsse:UsernameToken>
										<wsse:Username>' . $api_userName . '</wsse:Username>
										<wsse:Password Type="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-username-token-profile-1.0#PasswordText">' . $api_password . '</wsse:Password>
									</wsse:UsernameToken>
								</wsse:Security>
								<h:Authentication xmlns:i="http://www.w3.org/2001/XMLSchema-instance" xmlns:h="http://xlogics.eu/blackbox">
									<h:Culture>EN</h:Culture>
									<h:UnitName>' . $constant["ASENDIAUK_UNITNAME"] . '</h:UnitName>
								</h:Authentication>
							</s:Header>
							<s:Body>
							<PrintParcelRequest xmlns="http://xlogics.eu/blackbox"> 
								<InputParameters xmlns:i="http://www.w3.org/2001/XMLSchema-instance"> 
									<ShippingParameter> 
										<Name>Shipment.RefNo</Name> 
										<Value>' . $consignment->getHawb() . '</Value> 
									</ShippingParameter> 
									<ShippingParameter> 
										<Name>AA.Service</Name>';
            if ($this->serviceValues->getCode() == "STASEPREM") {
                $request_body .= '<Value>PTLP</Value> ';
            } else if ($this->serviceValues->getCode() == "STASECOOM") {
                $request_body .= '<Value>COLAOM</Value> ';
            } else if ($this->serviceValues->getCode() == "STASECOLE") {
                $request_body .= '<Value>COLE</Value> ';
            } else if ($this->serviceValues->getCode() == "STASECOFR") {
                $request_body .= '<Value>COLEOM</Value> ';
            } else if ($this->serviceValues->getCode() == "STASEFTGM" || $this->serviceValues->getCode() == "STASEFTGN") {
                $request_body .= '<Value>FTGM</Value> ';
            } else if ($this->serviceValues->getCode() == "STASEFTGP") {
                $request_body .= '<Value>FTGP</Value> ';
            } else {
                $request_body .= '<Value>COLA</Value> ';
            }
            $request_body .= '</ShippingParameter> 
									<ShippingParameter> 
										<Name>Shipment.ReturnValue</Name> 
										<Value>ShipmentIdentcode;AA.UniqueReference</Value> 
									</ShippingParameter> 
									<ShippingParameter> 
										<Name>AA.Reference2</Name> 
										<Value></Value> 
									</ShippingParameter> 
									<ShippingParameter> 
										<Name>AA.EmailAlert</Name> 
										<Value>FALSE</Value> 
									</ShippingParameter> 
									<ShippingParameter> 
										<Name>AA.DocumentsOnly</Name> 
										<Value>FALSE</Value> 
									</ShippingParameter> 
									<ShippingParameter> 
										<Name>AA.ExportType</Name> 
										<Value>Gift</Value> 
									</ShippingParameter> 
									<ShippingParameter> 
										<Name>AA.InvoiceNo</Name> 
										<Value>'. $consignment->getIossNumber().'</Value> 
									</ShippingParameter> 
									<ShippingParameter> 
										<Name>AA.VatNo</Name> 
										<Value></Value> 
									</ShippingParameter> 
									<ShippingParameter> 
										<Name>Receiver.RefNo</Name> 
										<Value></Value> 
									</ShippingParameter> 
									<ShippingParameter> 
										<Name>Receiver.CompanyName</Name> 
										<Value>' . htmlspecialchars($consignment->getCompany()) . '</Value> 
									</ShippingParameter> 
									<ShippingParameter> 
										<Name>Receiver.Name1</Name> 
										<Value>' . htmlspecialchars($consignment->getContact()) . '</Value> 
									</ShippingParameter> 
									<ShippingParameter>
										<Name>Receiver.Name2</Name> 
										<Value>-</Value> 
									</ShippingParameter> 
									<ShippingParameter> 
										<Name>Receiver.HouseNo</Name> 
										<Value></Value> 
									</ShippingParameter> 
									<ShippingParameter> 
										<Name>Receiver.Street</Name> 
										<Value>' . htmlspecialchars($consignment->getAddressLine1()) . '</Value> 
									</ShippingParameter> 
									<ShippingParameter> 
										<Name>Receiver.AddressDetails</Name> 
										<Value>' . htmlspecialchars($consignment->getAddressLine2()) . " " . htmlspecialchars($consignment->getAddressLine3()) . '</Value> 
									</ShippingParameter> 
									<ShippingParameter> 
										<Name>Receiver.City</Name> 
										<Value>' . htmlspecialchars($consignment->getCity()) . '</Value> 
									</ShippingParameter> 
									<ShippingParameter> 
										<Name>Receiver.Province</Name> 
										<Value></Value> 
									</ShippingParameter> 
									<ShippingParameter> 
										<Name>Receiver.Postcode</Name> 
										<Value>' . $consignment->getPostcode() . '</Value> 
									</ShippingParameter> 
                                                                        <ShippingParameter> 
										<Name>Receiver.Country</Name> 
										<Value>' . $this->country->getIso() . '</Value> 
									</ShippingParameter> 
									<ShippingParameter> 
										<Name>Receiver.Telephone</Name> 
										<Value>' . $consignment->getTelephone() . '</Value> 
									</ShippingParameter> 
									<ShippingParameter>
										 <Name>Receiver.Mobile</Name> 
										 <Value></Value> 
									</ShippingParameter> 
									<ShippingParameter> 
										<Name>Receiver.Email</Name> 
										<Value>' . $consignment->getEmail() . '</Value> 
									</ShippingParameter> 
									<ShippingParameter> 
										<Name>Parcel.Weight</Name> 
										<Value>' . $consignment->getWeight() . '</Value> 
									</ShippingParameter> 
									<ShippingParameter> 
										<Name>Parcel.Length</Name> 
										<Value></Value> 
									</ShippingParameter> 
									<ShippingParameter> 
										<Name>Parcel.Width</Name> 
										<Value></Value> 
									</ShippingParameter> 
									<ShippingParameter> 
										<Name>Parcel.Height</Name> 
										<Value></Value> 
									</ShippingParameter> ';
            
                $productParcel = "<Table>";
                $parcels = $consignment->getParcels();
                $senderCountry = new Country($consignment->getSenderCountryId());
                
                foreach ($parcels as $p) {
                    $parcelDescription = json_decode($p->getDescription());
                    $parcelCountry = json_decode($p->getCommodityCode());
                    $parcelQty = json_decode($p->getQty());
                    $parcelValue = json_decode($p->getItemValue());
                    $parcelHscode = json_decode($p->getHsCode());
                    $parcelSku = json_decode($p->getItemSku());
                    $parcelWeight = json_decode($p->getPWeight());
                    $count = 1;
                    if(count($parcelDescription) > 0){
                        foreach($parcelDescription as $key => $desc){
                            $productParcel .= "<Row> "
                            . " <ASLineItemNo>" . $count . "</ASLineItemNo>"
                            . " <ASProductDesc>" . $this->utfEncode($desc) . "</ASProductDesc>"
                            . " <ASUnitQuantity>" . $parcelQty[$key] . "</ASUnitQuantity>"
                            . " <ASUnitValue>" . $parcelValue[$key] . "</ASUnitValue>"
                            . " <ASCurrency>" . $consignment->getCurrency() . "</ASCurrency>"
                            . " <ASUnitWeight>" . $parcelWeight[$key] . "</ASUnitWeight> "
                            . " <ASCountry>" . $parcelCountry[$key] . "</ASCountry>"
                            . " <ASHSTariff>".$parcelHscode[$key]."</ASHSTariff>"
                            . " </Row>";
                            $count++;
                        }
                    }
                    else{
                        $productParcel .= "<Row> "
                            . " <ASLineItemNo>" . $count . "</ASLineItemNo>"
                            . " <ASProductDesc>" . $this->utfEncode($consignment->getDescription()) . "</ASProductDesc>"
                            . " <ASUnitQuantity>" . $count . "</ASUnitQuantity>"
                            . " <ASUnitValue>" . $consignment->getValue() . "</ASUnitValue>"
                            . " <ASCurrency>" . $consignment->getCurrency() . "</ASCurrency>"
                            . " <ASUnitWeight>" . $p->getWeight() . "</ASUnitWeight> "
                            . " <ASCountry>" . $senderCountry->getIso() . "</ASCountry>"
                            . " <ASHSTariff>53050090</ASHSTariff>"
                            . " </Row>";
                    }
                    
                }
                
                $productParcel .= "</Table>";
                $request_body .= '     				<ShippingParameter>
										<Name>ProductParcel</Name> 
										<Value> <![CDATA[' . $productParcel . ']]>
										</Value> 
									</ShippingParameter>';
            
            if ($this->serviceValues->getCode() == "STASEPREM") {
                $request_body .= '                               <ShippingParameter>
										<Name>ProductQuantity</Name> 
										<Value>1</Value> 
									</ShippingParameter> 
									<ShippingParameter>
										<Name>ProductDescription</Name> 
										<Value><![CDATA[' . $this->utfEncode($consignment->getDescription()) . ']]></Value>  
									</ShippingParameter> ';
            }
            $request_body .= '					</InputParameters> 
							</PrintParcelRequest>
						</s:Body>
					</s:Envelope>';

            $result = $client->send($request_body, $soapaction);
            $consignment->setApiData(print_r($request_body, true), print_r($result, true), 'PrintParcelRequest');
            if ($result["ExitStatus"]["Status"] == "Success") {
                $output = $result["OutputParameters"]["ShippingParameter"];

                $tracking_number = $output[0]["Value"];
                $licence_plate_array[] = $tracking_number;
                $parcelList = $consignment->getParcels();
                
                $unique_refernce = $output[1]["Value"];
                $pdfContent = base64_decode($output[2]["Value"]);
                $fileName = '../_assets/pdf/' . date('Y_m_d') . '/' . $consignment->getId() . ".pdf";
                $fp = fopen($fileName, 'wb+');
                fwrite($fp, $pdfContent);
                fclose($fp);
                
                $pieceFileName = '../_assets/pdf/' . date('Y_m_d') . '/' . $tracking_number . ".pdf";
                $fp = fopen($fileName, 'wb+');
                fwrite($fp, $pdfContent);
                fclose($fp);
                if (count($parcelList) > 0) {
                    $parcelList[0]->setTrackingNumber($tracking_number);
                    $parcelList[0]->setParcelLabel(date('Y_m_d') . '/' . $tracking_number. ".pdf");
                    $parcelList[0]->save();
                }
                $output['STATUS'] = 'SUCCESS';
                $output['LABEL'] = date('Y_m_d') . '/' . $consignment->getId() . ".pdf";
                $output['TRACKING_NUMBER'] = $licence_plate_array;
                return $output;
            } else {
                $message = $result["ExitStatus"]["StatusDetails"]["StatusDetail"]["Message"];
                if (trim($message) == "") {
                    $message = $result["faultcode"];
                }
                $output["STATUS"] = "ERROR";
                $output["MESSAGE"] = $message;
                return $output;
            }
        }
        }

        private function apiAsendiaProntoLabel(Consignment $consignment){
            require_once '../vendor/autoload.php';
            $senderCountry = new Country($consignment->getSenderCountryId());
            $parcelList = $consignment->getParcels();
            $parcel = $parcelList[0];
            
            if(count($parcelList) > 0){
                foreach($parcelList as $parcel){
                    $parcelDescription = json_decode($parcel->getDescription());
                    $parcelCountry = json_decode($parcel->getCommodityCode());
                    $parcelQty = json_decode($parcel->getQty());
                    $parcelValue = json_decode($parcel->getItemValue());
                    $parcelHscode = json_decode($parcel->getHsCode());
                    $parcelSku = json_decode($parcel->getItemSku());
                    $parcelWeight = json_decode($parcel->getPWeight());
                    
                    $parcelItem = array();
                    if(count($parcelDescription) > 0){
                        foreach($parcelDescription as $key => $desc){
                            $parcelItem[] = 
                                array (
                                'SKU' => $parcelSku[$key],
                                'ProductType' => $desc,
                                'Composition' => 'composition',
                                'DetailedDescription' => $desc,
                                'HTSCode' => $parcelHscode[$key],
                                'CountryOfOrigin' => $parcelCountry[$key],
                                'Quantity' => $parcelQty[$key],
                                'Weight' => $parcelWeight[$key],
                                'Value' => $parcelValue[$key],
                                'RetailerProductURL' => NULL,
                                'DestinationHTSCode' => $parcelHscode[$key],
                                'CustomsProcedureCode' => NULL,
                                'DGNFlag' => 'False',
                                'DGNCategory' => NULL,
                                'ECCN_EAR' => NULL,
                              
                            );
                        }
                    }
                    else{
                        $parcelItem[] = 
                           array (
                                'SKU' => 'sku',
                                'ProductType' => $consignment->getDescription(),
                                'Composition' => 'composition',
                                'DetailedDescription' => $consignment->getDescription(),
                                'HTSCode' => NULL,
                                'CountryOfOrigin' => $senderCountry->getIso3(),
                                'Quantity' => 1,
                                'Weight' => $consignment->getWeight(),
                                'Value' => $consignment->getValue(),
                                'RetailerProductURL' => NULL,
                                'DestinationHTSCode' => NULL,
                                'CustomsProcedureCode' => NULL,
                                'DGNFlag' => 'False',
                                'DGNCategory' => NULL,
                                'ECCN_EAR' => NULL,
                              
                        );
                    }
                }
            }
            
         $requestArray = array (
                        'Parcels' => 
                        array (
                          0 => 
                          array (
                            'ParcelCode' => $consignment->getHawb().date("s"),
                            'OrderNumber' => $consignment->getHawb().date("s"),
                            'ConsignmentCode' => $consignment->getHawb().date("s"),
                            'ServiceCode' => $this->serviceValues->getCarrierServiceCode(),//'INTL',
                            'Consignee' => 
                            array (
                              'RecipientName' => $consignment->getContact(),
                              'Address1' => $consignment->getAddressLine1(),
                              'Address2' => $consignment->getAddressLine2(),
                              'City' => $consignment->getCity(),
                              'State' => $consignment->getState(),
                              'Postcode' => $consignment->getPostcode(),
                              'CountryCode' => $this->country->getIso(),
                              'Email' => $consignment->getEmail(),
                              'HomeNo' => $consignment->getTelephone(),
                              'MobileNo' => $consignment->getTelephone(),
                            ),
                            'ParcelItems' => 
                              $parcelItem,
                            'ParcelLabel' => 
                            array (
                              'Format' => 'ZPL',
                              'Dimension' => '6x4',
                            ),
                            'ParcelWeight' => number_format($consignment->getWeight(), 2) ,
                            'Height ' => $parcel->getHeight(),
                            'Length ' => $parcel->getLength(),
                            'Width ' => $parcel->getWidth(),
                            'CurrencyCode' => $consignment->getCurrency(),
                            'DeliveryLocationCode' => NULL,
                            'Deliverytype' => $this->serviceValues->getCarrierServiceCode(),
                            'CustomsPaymentMethod' => 'DDU',
                            'CountryRegistrationNumber' => $consignment->getIossNumber(),
                            'GSTCollected ' => NULL,
                            'ParcelValue' => $consignment->getValue()
                          ),
                        ),
                      );
            $jsonRequest = json_encode($requestArray);
        $curl = curl_init();

        $api_password = $this->constants["ASENDIAUK_PASSWORD"]; // 'sandbox - 09703F8DF00B42729E41B1A44ABD548C
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://wnglobalapi.com/v1/label/getlabel", //https://sandbox.wnglobalapi.com/v1/label/getlabel
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => $jsonRequest,
            CURLOPT_HTTPHEADER => array(
                "api-key: ". $api_password,
                "Content-Type: application/json"
            ),
        ));
        $response = curl_exec($curl);
        
        curl_close($curl);
        $responseArray = json_decode($response);
        $consignment->setApiData(print_r($jsonRequest, true), print_r($responseArray, true), 'PrintParcelRequest');
        //print_r($responseArray);
        if($responseArray->Status == "Success"){
            $parcelDetails = $responseArray->Parcels;
            foreach($parcelDetails as $parcels){
                $trackingNo = $parcels->TrackingNumber;
                $licence_plate_array[] = $trackingNo;
                $parcel->setTrackingNumber($trackingNo);
                $parcel->setParcelLabel(date('Y_m_d') . '/' . $trackingNo. ".pdf");
                $parcel->save();
                $parcelLabel = $parcels->ParcelLabels;
                foreach($parcelLabel as $label){
                    $labelString = $label->Label;
                    $zplContent = base64_decode($labelString);
                    
                    $labelary = new Labelary\Client();

                    try {
                            $response = $labelary->printers->labels([
                            'zpl' => $zplContent,
                            'response' => 'application/pdf',
                            //'rotate' => 180
                        ]);
                        $json = json_decode($response);

                        $pdfContent = base64_decode($json->label);
                        $fileName = '../_assets/pdf/' . date('Y_m_d') . '/' . $consignment->getId() . ".pdf";
                        $fp = fopen($fileName, 'wb+');
                        fwrite($fp, $pdfContent);
                        fclose($fp);
                        
                        $pieceFileName = '../_assets/pdf/' . date('Y_m_d') . '/' . $trackingNo . ".pdf";
                        $fp = fopen($pieceFileName, 'wb+');
                        fwrite($fp, $pdfContent);
                        fclose($fp);
                        
                        $output['STATUS'] = 'SUCCESS';
                        $output['LABEL'] = date('Y_m_d') . '/' . $consignment->getId() . ".pdf";
                        $output['TRACKING_NUMBER'] = $licence_plate_array;
                        return $output;
                    } catch (Exception $e) {
                        echo $e->getMessage();
                    }
                    
                    
                    
                  
                    
                }
            }
        }
        else
        {
            $errorMessage = $responseArray->StatusMessage;
            $errorArray = $responseArray->Parcels;
            foreach($errorArray as $e){
                if($e->ParcelStatus == "Error"){
                    $errorMessage .= "<br />" . $e->ParcelStatusMessage;
                }
            }
            $output['STATUS'] = 'ERROR';
            $output['MESSAGE'] = $errorMessage;
            return $output;
        }
       // print_r($response);
    }

    public function tracking($trackingNumber, $trackBy = 'parcel', $EDI = false) {
        $tracking = new Tracking();
        $deliveredArray = array("1000", "1007");

        if ($EDI == true && !empty($this->trackingServiceId) && !empty($this->trackingAgentId)) {
            include_once(BASE_PATH . "includes/labels/asendiatrackingstatus.class.php");
            $serviceAgentConstantFilter = new ServiceConstantValueFilter();
            $serviceAgentConstantFilter->addFilter("service_id = '" . $this->trackingServiceId . "' AND agent_id = '" . $this->trackingAgentId . "' ");
            $serviceAgentConstant = $serviceAgentConstantFilter->getConstantList("c.constant 'constant_name', scv.constant_value 'constant_value'");

            if (count($serviceAgentConstant) > 0) {
                foreach ($serviceAgentConstant as $serviceAgentConstantData) {
                    $this->constants[$serviceAgentConstantData->getConstantName()] = $serviceAgentConstantData->getConstantValue();
                }
            }
            $ftp_server = $this->constants['ASENDIAUK_TRACKING_SERVER'];
            $ftp_user = trim($this->constants['ASENDIAUK_TRACKING_USERNAME']);
            $ftp_pass = trim($this->constants['ASENDIAUK_TRACKING_PASSWORD']);

            $ftp_local_path = SETTING_DIR_ASSETS . "tracking_data/ASENDIA/";

            $conn_id = ftp_connect($ftp_server);

            if (!$conn_id) {
                echo "FTP connection failed";
                exit;
            } else {
                $loginRes = ftp_login($conn_id, $ftp_user, $ftp_pass);
                if (!$loginRes) {
                    echo "FTP Login Failed, check your username and password.";
                    exit;
                }
                ftp_pasv($conn_id, true);
                $arrfile = ftp_nlist($conn_id, "/OUT");
                $arrfile = array_reverse($arrfile);

                if ($arrfile === false) {
                    echo "Unable List FTP Files.";
                    exit;
                }
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
                ftp_close($conn_id);
            }
        }
    }
    
    public function trackingPronto($trackingNumber, $trackBy = 'parcel', $EDI = false) {
        $tracking = new Tracking();
        $deliveredArray = array("16", "29", "49", "50", "51","54", "55","7","WNCT53", "WNCT55");

        if ($EDI == true && !empty($this->trackingServiceId) && !empty($this->trackingAgentId)) {
            include_once(BASE_PATH . "includes/labels/asendiatrackingstatus.class.php");
            $ftp_server = 'ftp.wnconsign.com';//$this->constants['ASENDIAUK_TRACKING_SERVER'];
            $ftp_user = 'OneWorldExpressGLB';//trim($this->constants['ASENDIAUK_TRACKING_USERNAME']);
            $ftp_pass = 'w0rld1!3xpress';//trim($this->constants['ASENDIAUK_TRACKING_PASSWORD']);

            $ftp_local_path = SETTING_DIR_ASSETS . "tracking_data/ASENDIA/";

            $conn_id = ftp_connect($ftp_server);

            if (!$conn_id) {
                echo "FTP connection failed";
                exit;
            } else {
                $loginRes = ftp_login($conn_id, $ftp_user, $ftp_pass);
                if (!$loginRes) {
                    echo "FTP Login Failed, check your username and password.";
                    exit;
                }
                ftp_pasv($conn_id, true);
                $arrfile = ftp_nlist($conn_id, "/OneWorldExpressGLB/OUT");
                $arrfile = array_reverse($arrfile);

                if ($arrfile === false) {
                    echo "Unable List FTP Files.";
                    exit;
                }
               // $header = 1; 
                if (sizeof($arrfile) > 0) {
                    foreach ($arrfile as $filename) {
                        if ($filename == '.' || $filename == '..') {
                            continue;
                        }
                        $fp = fopen($ftp_local_path . $filename, 'w');
                        if (ftp_fget($conn_id, $fp, "/OneWorldExpressGLB/OUT/" . $filename, FTP_ASCII, FTP_AUTORESUME)) {
                            ftp_rename($conn_id, "/OneWorldExpressGLB/OUT/".$filename, "/OneWorldExpressGLB/PROCESSED/".$filename);
                
                            $handle = fopen($ftp_local_path . $filename, "r");
                            $ftp_local_path = SETTING_DIR_ASSETS . "tracking_data/ASENDIA/";
                            if ($handle) {
                                while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                                   // if($header == 1)
                                    //{
                                     //   $header = 0; 
                                      //  continue;
                                    //}
                                    $trackingNumber = removeBomUtf8(str_replace('"', '', $data[1]));
                                    ////////////////////// Carrier Received ////////////////////////////////

                                    $trackingDataFilterObj = new TrackingDataFilter();
                                    $trackingDataFilterObj->addFieldFilter('    tracking_number', $trackingNumber);
                                    $trackingDataFilterObj->addFilter("carrier_code not in ('','34')");
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
                                    $trackingNumber = removeBomUtf8(str_replace('"', '', $data[1]));

                                    $hawb = trim($data[0]);
                                    $EventCode = $data[3];
                                    $date = str_replace('/', '-', $data[8]);
                                    $EventDescription = $data[4];

                                    if (strpos($date, "|") !== false) {
                                        $dateAndDesc = explode("|", $date);
                                        $date = $dateAndDesc[0];
                                        $DateTime = date("Y-m-d H:i:s", strtotime($date));
                                        $EventDescription = str_replace('"', "", $dateAndDesc[1]);
                                    } else {
                                        $DateTime = date("Y-m-d H:i:s", strtotime($date));
                                        //$EventDescription = $data[4] . ' - ' . $data[6];
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
                                        if ($carrierReceivedCheck == 1 && $EventCode != '34') {
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
                ftp_close($conn_id);
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

    public function setTrackingParams($serviceId, $agentId) {
        $this->trackingServiceId = $serviceId;
        $this->trackingAgentId = $agentId;
    }

    private function utfEncode($str, $encoding = "") {
        $str = preg_replace('/[^(\x20-\x7F)]*/', '', $str);
        $str = str_replace('&', 'and', $str);
        $str = str_replace('<', '&lt;', $str);
        $str = str_replace('>', '&gt;', $str);
        $str = str_replace("'", "", $str);

        if ($str !== "") {
            if (empty($encoding) && self::isUTF8($str))
                $encoding = "UTF-8";
            if (empty($encoding))
                $encoding = mb_detect_encoding($str, 'UTF-8, ISO-8859-1');
            if (empty($encoding))
                $encoding = "ISO-8859-1"; //  if charset can't be detected, default to ISO-8859-1
            return $encoding == "UTF-8" ? $str : @mb_convert_encoding($str, "UTF-8", $encoding);
        }
    }

    private function isUTF8($str) {
        return preg_match('%^(?:
         [\x09\x0A\x0D\x20-\x7E]           # ASCII
       | [\xC2-\xDF][\x80-\xBF]            # non-overlong 2-byte
       | \xE0[\xA0-\xBF][\x80-\xBF]        # excluding overlongs
       | [\xE1-\xEC\xEE\xEF][\x80-\xBF]{2} # straight 3-byte
       | \xED[\x80-\x9F][\x80-\xBF]        # excluding surrogates
       | \xF0[\x90-\xBF][\x80-\xBF]{2}     # planes 1-3
       | [\xF1-\xF3][\x80-\xBF]{3}         # planes 4-15
       | \xF4[\x80-\x8F][\x80-\xBF]{2}     # plane 16
   )*$%xs', $str);
    }

    public function reconciliation_data($headingArr,$carrierId,$relPath,$new_csv_file_created,$filePath,$batchNumber) {
        ini_set('memory_limit', '-1');
        $output = [];
        $comaSeptHeading = implode(",", $headingArr);
        $tableColumn = rtrim($comaSeptHeading, ',');
        $csvStr = $tableColumn;
        $csvStr .= "\r\n";
        $templateCheck = 1;
        $checkExist = 0;
        $output['new_invoice_save'] = 0;
        $output['total_weight'] = 0;
        $output['total_pieces'] = 0;
        $output['total_amount'] = 0;
        $row = 1;
        $dataArr = [];
        if (($handle = fopen($relPath, "r")) !== FALSE) {
            $fuelChargePercentage = 0.00;
            while (($data = fgetcsv($handle)) !== FALSE) {
                if ($row > 2) {
                    $account_number = "";
                    $invoice_number = $data[2];
                    $agent_reference_number = "";
                    $collection_date = $data[0];
                    $delivery_country = $data[5];
                    $mawb = "";
                    $awb = $data[3];
                    $hawb = $data[4];
                    $service_name = "";
                    $service_code = $data[9];
                    $weight = $data[6];
                    $number_of_pieces = $data[10];
                    $basic_charges = formatNumber($data[11]);
                    $fuel_charges = formatNumber($data[12]);
                    $vat = formatNumber($data[14]);
                    $additional_charges = 0.00;
                    $total_amount = $data[15];
                    $notes = "";
                    $currency = "GBP";
                    $output['invoice_number'] = $invoice_number;
//                                $supplierInvoiceFilter = new SupplierInvoicesFilter();
//                                $supplierInvoiceFilter->where(['si.invoice_number' => $invoice_number]);
//                                $supplierInvoiceFilter->where(['si.account_id' => $user->getUserAccountId()]);
//                                $supplierInvoiceFilterObjs = $supplierInvoiceFilter->getList();
//                                if (count($supplierInvoiceFilterObjs)) {
//                                    $output['status'] = 'error';
//                                    $output['message'] = 'This file is already processed';
//                                    $output['new_invoice_save'] = 1;
//                                    break;
//                                }
                    $output['invoice_date'] = $collection_date;
                    $length = "0.00";
                    $height = "0.00";
                    $width = "0.00";
                    $volWeight = "0.000";
                    $weight = formatNumber($weight, 3);
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
                        'number_of_pieces' => $number_of_pieces,
                        'basic_charges' => $basic_charges,
                        'fuel_charges' => $fuel_charges,
                        'additional_charges' => $additional_charges,
                        'vat' => $vat,
                        'total_amount' => $total_amount,
                        'notes' => $notes,
                        'currency' => $currency,
                    ];
                    $output['total_weight'] += $weight;
                    $output['total_pieces'] += $number_of_pieces;
                    $output['total_amount'] += $total_amount;
                    $output['currency'] = $currency;
                    $dataArr[] = $dt;
                    $comaSept = implode(",", $dt);
                    $csvStr .= rtrim($comaSept, ',');
                    $csvStr .= "\r\n";
                }
                $row++;
            }
        } else {
            $output['status'] = 'error';
            $output['message'] = 'File can not open please check permission';
        }
        if ($output['status'] != "error" && !$output['return']) {
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
            } else {
                $sql = "SELECT * FROM reconciliation_data WHERE batch_number='" . $batchNumber . "' ";
                $resultSql = DbAccess3::runQuery($sql);
                $res = [];
                while ($obj = mysqli_fetch_object($resultSql)) {
                    $res[] = $obj;
                }
                $output['status'] = 'success';
                $output['file_path'] = $filePath;
                $output['batch_number'] = $batchNumber;
                $output['template'] = $templateCheck;
                $output['data'] = $res;
            }
        }
        return $output;
    }
    public function getSummeryData($relPath){
        $returnArr = [];
        $row = 1;
        $isInvoiceType = false;
        $isDataSet = true;
        if (($handle = fopen($relPath, "r")) !== FALSE) {
            while (($data = fgetcsv($handle)) !== FALSE) {
                if ($row > 2) {
                    $returnArr['invoice_number'] = $data[2];
                    $returnArr['collection_date'] = $data[0];
                    $isInvoiceType = true;
                }
                if($isInvoiceType){
                    break;
                }
                $row++;
            }
        }
        return $returnArr;
    }
}
