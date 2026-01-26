<?php
class AuthDataV1 
{ 
  public $login; 
  public $masterFid; 
  public $password; 
}; 
class DpdPoland implements CarrierService {

    private $pdf;
    private $serviceValues = null;
    private $agentValues = null;
    private $constants = null;
    private $user = null;
    private $country = null;

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
        if (trim(@$this->constants['DPDPOLAND_URL']) == '' || trim(@$this->constants['DPDPOLAND_USERNAME']) == '' || trim(@$this->constants['DPDPOLAND_PASSWORD']) == '' || trim(@$this->constants['DPDPOLAND_MASTERFID']) == '' || trim(@$this->constants['DPD_SHIPPER_ADDRESS_LINE_1']) == '' || trim(@$this->constants['DPD_SHIPPER_POSTCODE']) == '') {
            $output['STATUS'] = 'ERROR';
            $output['MESSAGE'] = "Please check service constants. Please contact to itsupport@oneworldexpress.com";
            return $output;
        }

        if (trim(@$this->constants['INTEGRATION_TYPE']) == 'API') {
            $output = $this->apiLabel($this->constants, $consignment);
        }

        return $output;
    }

    private function apiLabel($constants, Consignment $consignment) {
        
        $output = array();
       
        $authData = new AuthDataV1(); 

        $authData->login=  $constants["DPDPOLAND_USERNAME"]; //"11264101"; 
        $authData->masterFid= $constants["DPDPOLAND_MASTERFID"]; // "112641"; 
        $authData->password= $constants["DPDPOLAND_PASSWORD"]; //"dmUU08DwyyqAc01I"; 
        $fid = $constants["DPDPOLAND_MASTERFID"];
        /*

          $authData->login="test";
          $authData->masterFid="1495";
          $authData->password="KqvsoFLT2M";
         */
        $services = "";
        $service_type = "INTERNATIONAL";
        if ($this->country->getIso() == "PL") {
            $service_type = "DOMESTIC";
            /* $services=  "<Services> 
              <COD>
              <Amount>". $consignment->getValue() ."</Amount>
              <Currency>PLN</Currency>
              </COD>
              </Services>"; */
        }


//$test_url= 'https://dpdservicesdemo.dpd.com.pl/DPDPackageXmlServicesService/DPDPackageXmlServices?wsdl' ;
        $live_url = $constants["DPDPOLAND_URL"]; //'https://dpdservices.dpd.com.pl/DPDPackageXmlServicesService/DPDPackageXmlServices?WSDL';
// Klient webservice 
        $sender_coutry = $constants["DPD_SHIPPER_COUNTRY"]; //"DK";
        if ((int) $sender_coutry > 0) {
            $shipperCountry = new Country($sender_coutry);
            $sCountry = $shipperCountry->getName();
            $sIso = $shipperCountry->getIso();
        } else {
            $sCountry = $sender_coutry;
        }
        $client = new SoapClient($live_url , array('features' => SOAP_SINGLE_ELEMENT_ARRAYS));
        $openUMLFV1 = " 
            <Packages> 
                <Package> 
                    <PayerType>SENDER</PayerType> 
                    <Sender> 
                        <FID>$fid</FID> 
                        <Company>".$constants["DPD_SHIPPER_COMPANY_NAME"]."</Company> 
                        <Name>".$constants["DPD_SHIPPER_CONTACT"]."</Name> 
                        <Address>".$constants["DPD_SHIPPER_ADDRESS_LINE_1"]."</Address> 
                        <City>".$constants["DPD_SHIPPER_CITY"]."</City> 
                        <CountryCode>".$sCountry."</CountryCode> 
                        <PostalCode>".$constants["DPD_SHIPPER_POSTCODE"]."</PostalCode> 
                        <Phone>".$constants["DPD_SHIPPER_TELEPHONE"]."</Phone> 
                        <Email>".$constants["DPD_SHIPPER_EMAIL"]."</Email> 
                    </Sender> 
                    <Receiver> 
                         <Company>" . str_replace("&", "", $consignment->getCompany()) . ".</Company> 
                        <Name>" . str_replace("&", "", $consignment->getContact()) . ".</Name> 
                        <Address>" . str_replace("&", "", $consignment->getAddressLine1()) . " " . $consignment->getAddressLine2() . " " . $consignment->getAddressLine3() . "</Address> 
                        <City>" . str_replace("&", "", $consignment->getCity()) . "</City> 
                        <CountryCode>" . $this->country->getIso() . "</CountryCode> 
                        <PostalCode>" . str_replace("-", "", $consignment->getPostcode()) . "</PostalCode> 
                        <Phone>" . $consignment->getTelephone() . "</Phone> 
                        <Email>cs@oneworldexpress.com</Email> 
                    </Receiver> 
                <Reference>" . str_replace("&", "", $consignment->getHawb()) . "</Reference> 
                    <Ref1>" . str_replace("&", "", $consignment->getReference()) . "</Ref1> 
                    <Ref2></Ref2> 
                    <Ref3></Ref3>$services
                            <Parcels> 
                        <Parcel> 
                            <Weight>" . $consignment->getWeight() . "</Weight> 
                            <Content>" . str_replace("&", "", $consignment->getDescription()) . "</Content> 
                            <CustomerData1>" . str_replace("&", "", $consignment->getNotes()) . "</CustomerData1> 
                        </Parcel> 

                    </Parcels> 
                </Package> 
            </Packages> 
            ";

        $params1->pkgNumsGenerationPolicyV1 = "";
        $params1->openUMLXV1 = $openUMLFV1;
        $params1->authDataV1 = $authData;




        $result = $client->generatePackagesNumbersXV1($params1);
        $xml = simplexml_load_string($result->return);
        $status = $xml->Status;

        if ($status != "OK") {
            $error_details = $result->return;
            $output['STATUS'] = 'ERROR';
            $output['MESSAGE'] = $error_details;
            return $output;
            
        } else {
            $sessionId = $xml->SessionId;
            $packageId = $xml->Packages->Package->PackageId;
            $parcelId = $xml->Packages->Package->Parcels->Parcel->ParcelId;
            $waybill = $xml->Packages->Package->Parcels->Parcel->Waybill;
            $reference = $xml->Packages->Package->Reference;

            $dpdServiceParam1 = "<DPDServicesParamsV1> 
                                <Policy>STOP_ON_FIRST_ERROR</Policy> 
                                <Session> 
					<SessionType>$service_type</SessionType> 
					<SessionId>". $sessionId ."</SessionId> 
                                </Session> 
                                </DPDServicesParamsV1> ";

            $params2->dpdServicesParamsXV1 = $dpdServiceParam1;
            $params2->outputDocFormatV1 = "PDF";
            $params2->outputDocPageFormatV1 = "LBL_PRINTER";
            $params2->authDataV1 = $authData;
            $result = $client->generateSpedLabelsXV1($params2);
            
            $xml = simplexml_load_string($result->return);
            $pdf1 = $xml->DocumentData;
            //echo $xml->Session->StatusInfo->Status;   
            $results = base64_decode($pdf1);
            // echo  $results;
            $path = '../_assets/pdf/' . date('Y_m_d') . '/' . $consignment->getId() . ".pdf";
            $fp = fopen($path, 'wb+');
            fwrite($fp, $results);
            fclose($fp);
            
            $licence_plate_array[] = $waybill;
            $output['STATUS'] = 'SUCCESS';
            $output['LABEL'] = date('Y_m_d') . '/' . $consignment->getId() . ".pdf";
            $output['TRACKING_NUMBER'] = $licence_plate_array;
            return $output;
            
            
        }
    }

    // can't implement as we dont have any latest shipment of this service . last shipment is of 2016
    private function GetDPDPLTracking($trackingNumber)
    {
        $signature = '';
    
        $url = "https://www.trackinggo.com/dpd-poland-tracking.html?search=".$trackingNumber;     
        $response = file_get_contents($url, true);		    

                $postable = strpos($response, '<ul class="time_axis">');
                $table_start_part =  substr ($response, $postable, strlen($response) );
                $pos2 = strpos($table_start_part, '</ul>');
                $table_end_part =  substr($table_start_part, 0 , $pos2 ) . '</ul>';	

                $doc = new DOMDocument();
                $doc->loadHTML($table_end_part);
                $divs = $doc->getElementsByTagName('ul');

                $i = 0;

                $xpath = new DOMXPath($doc);        
                foreach ($xpath->query('//span') as $child) 
                {
                    if($i%2 == 0)
                    {
                        $dateTime = @$child->nodeValue;
                        $innerHtmlNew .= "<tr>";
                        $innerHtmlNew .= "<td>" .@$child->nodeValue."</td>"; 
                    }
                    else
                    {
                        if(strpos($this->makeUTF8(trim($child->nodeValue)), 'Przesyka dorczona') !== false)
                        {
                            $newstatus = Consignment::STATUS_DELIVERED;
                            $signature = $this->makeUTF8(trim($child->nodeValue));
                        }
                        
                        $innerHtmlNew .= "<td>" .  $this->makeUTF8(trim($child->nodeValue))."</td>"; 
                        $innerHtmlNew .= "<td></td>"; 
                        $innerHtmlNew .= "<td></td>";                     
                        $innerHtmlNew .= "</tr>";	 
                    }                   
                    $i++;
                }   
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
