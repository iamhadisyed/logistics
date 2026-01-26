<?php

include_classes([
    'pbshipping',
        ], 'labels/pbshipping/lib');

class PitneyBowes implements CarrierService {

    private $pdf;
    private $serviceValues = null;
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

        if (trim(@$this->constants['API_KEY']) == '' || trim(@$this->constants['API_PASSWORD']) == '' || trim(@$this->constants['MERCHANT_EMAIL']) == '' || trim(@$this->constants['DEVELOPER_ID']) == '') {
            $output['STATUS'] = 'ERROR';
            $output['MESSAGE'] = "Please check service constants. Please contact to itsupport@oneworldexpress.com";
            return $output;
        }
        
        if (trim(@$this->constants['INTEGRATION_TYPE']) == 'API') {
            $output = $this->apiLabel($consignment);
        }

        return $output;
    }

    public function apiLabel(Consignment $consignment) {
        try {


            $parcels = $consignment->getParcels();

            if (count($parcels) > 0) {
                $parcel = $parcels[0];
            }

            $xtra_headers = array();
            
            
            $api_key = $this->constants['API_KEY'];//"zwYsxZfTttqQFKg2gqAKgPA3hb0zZDdj";
            $api_secret = $this->constants['API_PASSWORD']; //"iHWcgTaacdlGXQXn";        
            $merchant_email = $this->constants['MERCHANT_EMAIL'];//"kazim@oneworldexpress.com";
            $dev_id = $this->constants['DEVELOPER_ID'];//"51405713";

            $carrierServiceCode = $this->serviceValues->getCarrierServiceCode();
            if ($carrierServiceCode == "PBPR" || $carrierServiceCode == "USPSFCM") {
                if ($carrierServiceCode == "PBPR")
                    $serviceId = "PM";
                else if ($carrierServiceCode == "USPSFCM")
                    $serviceId = "FCM";
                

                $my_rate_request = array(
                    "carrier" => "usps",
                    "serviceId" => $serviceId,
                    "parcelType" => "PKG",
                    "specialServices" => array(
                        array(
                            "specialServiceId" => "Ins",
                            "inputParameters" => array(
                                array("name" => "INPUT_VALUE", "value" => "50")
                            )
                        ),
                        array(
                            "specialServiceId" => "DelCon",
                            "inputParameters" => array(
                                array("name" => "INPUT_VALUE", "value" => "0")
                            )
                        )
                    ),
                    "inductionPostalCode" => "06810"
                );
            } 
            elseif ($carrierServiceCode == "PBPRG") {
               
                $my_rate_request = array(
                    "carrier" => "usps",
                    "serviceId" => "PM",
                    "parcelType" => "PKG",
                    "specialServices" => array(
                        array(
                            "specialServiceId" => "Ins",
                            "inputParameters" => array(
                                array("name" => "INPUT_VALUE", "value" => "50")
                            )
                        ),
                        array(
                            "specialServiceId" => "DelCon",
                            "inputParameters" => array(
                                array("name" => "INPUT_VALUE", "value" => "0")
                            )
                        )
                    ),
                    "inductionPostalCode" => "06810"
                );
            } 
            elseif ($carrierServiceCode == "PBS") {
              
                $xtra_headers = array(
                    'X-PB-ShipmentGroupId: 100001',
                    'X-PB-Integrator-CarrierId:987654321'
                );


                $my_rate_request = array(
                    "carrier" => "NEWGISTICS",
                    "serviceId" => "PRCLSEL",
                    "parcelType" => "PKG",
                    "currencyCode" => "USD"
                );
            } 
            elseif ($carrierServiceCode == "PBSLW") {
             
                $my_rate_request = array(
                    "carrier" => "NEWGISTICS",
                    "serviceId" => "PRCLSEL",
                    "parcelType" => "PKG",
                    "currencyCode" => "USD"
                );


                $xtra_headers = array(
                    'X-PB-ShipmentGroupId: 100001',
                    'X-PB-Integrator-CarrierId:987654321'
                );
            } 
            elseif ($carrierServiceCode == "PRCLSEL" || $carrierServiceCode == 'BPM' || $carrierServiceCode == 'PSLW' || $carrierServiceCode == 'FCM' || $carrierServiceCode == 'NEWPM') {
                /////////////////////////////////////////// NEWGISTICS ////////////////////////////////////////////////////////////
                if ($carrierServiceCode == 'NEWPM')
                    $serviceid = "PM";
                else
                    $serviceid = $carrierServiceCode;
              
                $my_rate_request = array(
                    "carrier" => "NEWGISTICS",
                    "serviceId" => $serviceid,
                    "parcelType" => "PKG",
                    "currencyCode" => "USD"
                );


                $xtra_headers = array(
                    'X-PB-IntegratorId:987654321'
                );
            }


            $origin_addr = array(
                "company" => "One World Express",
                "name" => "Atul Bhakta",
                "addressLines" => array("381 BLAIR ROAD"),
                "cityTown" => "Avenel",
                "stateProvince" => "New Jersey",
                "postalCode" => "07001",
                "countryCode" => "US"
            );

            $dest_addr = array(
                "company" => $consignment->getCompany(),
                "name" => $consignment->getContact(),
                "addressLines" => array($consignment->getAddressLine1() ." ,". $consignment->getAddressLine2() . " , " . $consignment->getAddressLine3()),
                "cityTown" => $consignment->getCity(),
                "stateProvince" => $consignment->getState(),
                "postalCode" => $consignment->getPostCode(),
                "countryCode" => $this->country->getIso(),
                "phone" => $consignment->getTelephone(),
                "email" => $consignment->getEmail()
            );

            $my_parcel = array(
                "weight" => array(
                    "unitOfMeasurement" => "OZ",
                    "weight" => $consignment->getWeight() * 35.26
                ),
                "dimension" => array(
                    "unitOfMeasurement" => "CM",
                    "length" => $parcel->getLength(),
                    "width" => $parcel->getWidth(),
                    "height" => $parcel->getHeight()
                    //"irregularParcelGirth" => 0.002
                )
            );

            $my_shipment_document = array(
                "type" => "SHIPPING_LABEL",
                "contentType" => "URL",
                "size" => "DOC_4X6",
                "fileFormat" => "PDF",
                "printDialogOption" => "NO_PRINT_DIALOG"
            );

            PBShipping::$is_production = false;
            /*
             *  Authentication call
             */
            $auth_obj = new PBShippingAuthentication($api_key, $api_secret);
            $errorList = $auth_obj->auth_info['errors'];
            if (count($errorList) > 0) {
                $output["STATUS"] = "ERROR";
                $output["MESSAGE"] = trim($errorList['0']['errorDescription']);
                return $output; 
            }


            /*
             * Merchant Registered Call
             */
            $developer = new PBShippingDeveloper(array("developerId" => $dev_id));
            $res = $developer->registerMerchantIndividualAccount($auth_obj, $merchant_email);
            $account_num = $res["paymentAccountNumber"];
            $postal_reporting_number = $res["postalReportingNumber"];

            /*
             * Verify Address Call
             */
            $addressValidationResponse = PBShippingAddress::verify($auth_obj, $dest_addr);
            
            $consignment->setApiData(print_r($dest_addr, true), print_r($addressValidationResponse, true), "ADDRESS_VERIFICATION");
            if($addressValidationResponse["status"] == "NOT_CHANGED"){
                $output["STATUS"] = "ERROR";
                $output["MESSAGE"] = "Unable to provide service to these address.";
                return $output;
            }

            $shipment = new PBShippingShipment(array(
                "fromAddress" => $origin_addr,
                "toAddress" => $addressValidationResponse,
                "parcel" => $my_parcel,
                "rates" => array($my_rate_request)
            ));
            
            if ($carrierServiceCode == 'PBSLW' || $carrierServiceCode == 'PBS') {
                $shipment["shipmentOptions"] = array(
                    array("name" => "SHIPPER_ID", "value" => "9014952121"),
                );
            } elseif ($carrierServiceCode == "PRCLSEL" || $carrierServiceCode == 'BPM' || $carrierServiceCode == 'PSLW' || $carrierServiceCode == 'FCM' || $carrierServiceCode == 'NEWPM') { // newgistics
                
                $shipment["shipmentOptions"] = array(
                    array("name" => "CLIENT_FACILITY_ID", "value" => "1601"),
                    array("name" => "CARRIER_FACILITY_ID", "value" => "1304"),
                    array("name" => "SHIPPER_ID", "value" => "9014952121"),
                );
            } else {
                $shipment["shipmentOptions"] = array(
                    array("name" => "SHIPPER_ID", "value" => $postal_reporting_number),
                    array("name" => "ADD_TO_MANIFEST", "value" => true)
                );


                $rates = $shipment->getRates($auth_obj, $this->get_pb_tx_id(), true);
                $shipment["rates"] = $rates;
            }


            $shipment["documents"] = array($my_shipment_document);
            $shipment_orig_tx_id = $this->get_pb_tx_id();
            
            /*
             * Create Shipment Call
             */
            $createShipmentResponse = $shipment->createAndPurchase($auth_obj, $shipment_orig_tx_id, true, $xtra_headers);
            $consignment->setApiData(print_r($shipment, true), print_r($createShipmentResponse, true), "CREATE SHIPMENT RESPONSE");

            
            if ($createShipmentResponse[0]["errorCode"] != "") {
                $output["STATUS"] = "ERROR";
                $output["MESSAGE"] = $createShipmentResponse[0]["message"] . ", " . $createShipmentResponse[0]["additionalInfo"];
                return $output;
            } else {
                $labelLink = $createShipmentResponse['documents'][0]['contents'];
                $trackingNumber = $createShipmentResponse['parcelTrackingNumber'];
                $licence_plate_array = array();
                $licence_plate_array[] = $trackingNumber;
                $mergeFileName = SETTING_DIR_REMOTE . '_assets/pdf/' . date('Y_m_d') . '/' . $consignment->getId() . '.pdf';
                file_put_contents($mergeFileName,file_get_contents($labelLink));
                $parcel->setTrackingNumber($trackingNumber);
                $parcel->save();
                $output['STATUS'] = 'SUCCESS';
                $output['LABEL'] = date('Y_m_d') . '/' . $consignment->getId() . ".pdf";
                $output['TRACKING_NUMBER'] = $licence_plate_array;
                return $output;

               /* if (in_array(trim($consignment->getServiceType()), array("IN-EXP-TRACKED-POSTAL", "IN-EXP-TRACKED-PARCEL", "IN-DOM"))) {
                    $this->pdf->SetPrintFooter(false);
                    $this->pdf->SetFooterMargin(0);
                    $this->pdf->SetAutoPageBreak(false, 0);

                    $page_size = array(100, 150);
                   
                    $commercialInvoiceClass = new CommercialInvoice($this->pdf, $page_size);
                    $commercialInvoiceClass->getCommercialInvoice($consignment, $consignment->getAwb());

                    $mergeFileNameCn22 = SETTING_DIR_ASSETS . "pdf/" . date('Y_m_d') . '/' . $consignment->getId() . "cn22.pdf";
                    $this->pdf->Output($mergeFileNameCn22, "F");

                    $mergeFileName = SETTING_DIR_REMOTE . '_assets/pdf/' . date('Y_m_d') . '/' . $consignment->getId() . ".pdf";
                    $PDFMerger = new PDFMerger();
                    $PDFMerger->addPDF($mergeFileName);
                    $PDFMerger->addPDF($mergeFileNameCn22);
                    try {
                        $PDFMerger->merge('file', $mergeFileName);
                    } catch (Exception $e) {
                        echo 'Caught exception: ', $e->getMessage(), "\n";
                    }
                }*/

            }
        } catch (SoapFault $exception) {
            $output["STATUS"] = "ERROR";
            $output["MESSAGE"] = trim($exception->getMessage());
            return $output; 
        }
    }
    
    private function get_pb_tx_id() 
    {
            return gmdate("YmdHis") . substr((string) microtime(), 2, 7);
    }

    public function tracking($trackingNumber, $trackBy = 'parcel', $EDI = false) {
        
    }

    public function sendData($tracking_numbers = array()) {
        
    }

    public function manifest($consignment) {
        $api_key = "zwYsxZfTttqQFKg2gqAKgPA3hb0zZDdj";
        $api_secret = "iHWcgTaacdlGXQXn";        
        $merchant_email = "kazim@oneworldexpress.com";
        $dev_id = "51405713";
        
        PBShipping::$is_production = false;
            /*
             *  Authentication call
             */
        $auth_obj = new PBShippingAuthentication($api_key, $api_secret);
        echo "<pre>";
        print_r($auth_obj);
        $errorList = $auth_obj->auth_info['errors'];
        if (count($errorList) > 0) {
            $output["STATUS"] = "ERROR";
            $output["MESSAGE"] = trim($errorList['0']['errorDescription']);
            return $output; 
        }
        $origin_addr = array(
                "company" => "One World Express",
                "name" => "Atul Bhakta",
                "addressLines" => array("381 BLAIR ROAD"),
                "cityTown" => "Avenel",
                "stateProvince" => "New Jersey",
                "postalCode" => "07001",
                "countryCode" => "US"
            );
        
        $manifestRequest = array (
                    'carrier' => 'NEWGISTICS',
                    'submissionDate' => date("Y-m-d"),
                    'fromAddress' => $origin_addr,
                    'parameters' => 
                    array (
                      array ('name' => 'SHIPPER_ID','value' => '9014952121'),
                      array ('name' => 'CLIENT_ID','value' => 'NGST'),
                    ),
                    'parcelTrackingNumbers' => 
                    array (
                      '420100369261292700522000000004',
                      '420100199261292700522000000003'
                    ),
                  );
        
        $manifest = new PBShippingManifest($manifestRequest);
        $shipment_orig_tx_id = $this->get_pb_tx_id();
        $createManifestResponse = $manifest->create($auth_obj, $shipment_orig_tx_id);
        print_r($createManifestResponse);
        exit;
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
