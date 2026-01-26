<?php

class GlsPoland implements CarrierService {

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

    public function label($consignment, $labelType = 'pdf', $size = '100x150') {

        $output = array();
        $this->serviceValues = new Services($consignment->getServiceId());
        $this->user = SessionManager::getUser();
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
        if (trim(@$this->constants['GLSPL_WSDL']) == ''  || trim(@$this->constants['GLSPL_USERNAME']) == '' || trim(@$this->constants['GLSPL_PASSWORD']) == '') {
            $output['STATUS'] = 'ERROR';
            $output['MESSAGE'] = "Please check service constants. Please contact to itsupport@oneworldexpress.com";
            return $output;
        }
        // Generate label for each parecel

        $parcel_list = $consignment->getParcels();
        $parcel_idx = 0;
         $pdf = new PdfBase(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $this->pdf = $pdf;
        foreach ($parcel_list as $parcel) {
            
           $output = $this->addWayBill($consignment, $this->constants);

           ++$parcel_idx;
        }
        return $output;
    }

    public function tracking($trackingNumber, $trackBy, $EDI){
        
    }

    public function  sendData($tracking_numbers = array()){
        
    }

    public function manifest($consignment) {
        
    }

    public function preAdvice($consignment) {
        
    }

    private function addWayBill(Consignment $consignment, $constants) {
        
       
        $wsdl = $constants["GLSPL_WSDL"];
        $username = $constants["GLSPL_USERNAME"];
        $password = $constants["GLSPL_PASSWORD"];
        
        define("SERVER_URL", $wsdl);
        define("SERVER_LOGIN", $username);
        define("SERVER_PASS", $password);
        $hClient = new SoapClient(SERVER_URL);
        
        try {
            /// Login to Server
            $loginCredit = new stdClass();
            $loginCredit->user_name = SERVER_LOGIN;
            $loginCredit->user_password = SERVER_PASS;

            $loginClient = $hClient->adeLogin($loginCredit);
            $loginSession = $loginClient->return->session;
            $oCons = new stdClass();
            $oCons->session = $loginSession;
            $oCons->consign_prep_data = new stdClass();
            $oCons->consign_prep_data->rname1 = $consignment->getCompany() . ".";
            $oCons->consign_prep_data->rname2 = $consignment->getContact() . ".";
            $oCons->consign_prep_data->rname3 = $consignment->getReference() . ".";

            $oCons->consign_prep_data->rcountry = $this->country->getIso();
            $oCons->consign_prep_data->rzipcode = $consignment->getPostCode();
            $oCons->consign_prep_data->rcity = $consignment->getCity();
            $oCons->consign_prep_data->rstreet = $consignment->getAddressLine1() . " " . $consignment->getAddressLine2() . " " . $consignment->getAddressLine3();

            $oCons->consign_prep_data->rphone = $consignment->getTelephone();
            $oCons->consign_prep_data->rcontact = $consignment->getCompany() . " " . $consignment->getContact();

            $oCons->consign_prep_data->references = $consignment->getHawb();
            $oCons->consign_prep_data->notes = $consignment->getDescription();
            $oCons->consign_prep_data->weight = $consignment->getWeight();
            $oCons->consign_prep_data->quantity = $consignment->getNumberPieces(); // overwrited by ParcelsArray


            $oCons->consign_prep_data->sendaddr = new stdClass();
            $oCons->consign_prep_data->sendaddr->name1 =  $constants["GLSPL_SHIPPER_COMPANY"]; //'KAAB';
            $oCons->consign_prep_data->sendaddr->name2 = $constants["GLSPL_SHIPPER_NAME"]; // 'ARNOLD';
            $oCons->consign_prep_data->sendaddr->name3 = '';
            $shipper_country = $this->constants['GLSPL_SHIPPER_COUNTRY'];
        if ((int) $shipper_country > 0) {
            $shipperCountry = new Country($shipper_country);
            $sCountry = $shipperCountry->getIso();
        } else {
            $sCountry = $shipper_country;
        }
            $oCons->consign_prep_data->sendaddr->country = $sCountry;//'PL';
            $oCons->consign_prep_data->sendaddr->zipcode = $constants["GLSPL_SHIPPER_POSTCODE"]; //'48-300';
            $oCons->consign_prep_data->sendaddr->city = $constants["GLSPL_SHIPPER_CITY"]; //'NYSA';
            $oCons->consign_prep_data->sendaddr->street = $constants["GLSPL_SHIPPER_ADDRESSLINE1"]; //'GRODKOWSKA 40';


            $oCons->consign_prep_data->srv_bool = new stdClass();
            $oCons->consign_prep_data->srv_bool->cod = 0;

            $cod_amount = $consignment->getValue();
            if ($cod_amount == 0)
                $cod_amount = 5;
            $oCons->consign_prep_data->srv_bool->cod_amount = $cod_amount;



            $oCons->consign_prep_data->parcels = new stdClass();

            $oParcel = new stdClass();
            $oParcel->reference = $consignment->getHawb();
            $oParcel->weight = $consignment->getWeight();
            $oCons->consign_prep_data->parcels->items[] = $oParcel;
            /*
              $oParcel = new stdClass();
              $oParcel->reference = 'Ref. parc02';
              $oParcel->weight = '1.22';
              $oCons->consign_prep_data->parcels->items[] = $oParcel;

              $oParcel = new stdClass();
              $oParcel->reference = 'Ref. parc03';
              $oParcel->weight = '1.33';
              $oCons->consign_prep_data->parcels->items[] = $oParcel;
             */
            echo "<pre>";
            
            $boxInsertClient = $hClient->adePreparingBox_Insert($oCons);
            print_r($boxInsertClient);
            exit;
            $consignmentId = $boxInsertClient->return->id;
            $labelInput = new stdClass();
            $labelInput->session = $loginSession;
            $labelInput->id = $consignmentId;
            $labelInput->mode = 'roll_160x100_pdf';

            $labelClient = $hClient->adePreparingBox_GetConsignLabels($labelInput);
            $szLabels = base64_decode($labelClient->return->labels);
           
            $fileNameLabe = $consignment->getId() . ".pdf";
            $path = SETTING_DIR_ASSETS . 'pdf/' . date('Y_m_d') . '/' . $consignment->getId() . ".pdf";
            $fp = fopen($path, 'wb+');
            fwrite($fp, $szLabels);
            fclose($fp);
            echo $fileNameLabe;
            exec("convert  -density 203  ".SETTING_DIR_ASSETS."pdf/".date("Y_m_d")."/".$fileNameLabe." ".SETTING_DIR_ASSETS."pdf/".$consignment->getId().".png", $output);
            exit;
            $oCons1 = new stdClass();
            $oCons1->session = $loginSession;
            $oCons1->id = $consignmentId;
            $consignment_details = $hClient->adePreparingBox_GetConsign($oCons1);
            $awb = $consignment_details->return->parcels->items->number;
            $licence_plate_array[] = $awb;

            $parcel = new ParcelFilter();
            $parcel->addConsignmentIdFilter($consignment->getId());
            $parcelList = $parcel->getList();
            if (count($parcelList) > 0) {
                $parcelList[0]->setTrackingNumber($awb);
                $parcelList[0]->save();
            } else {
                $parcel = new Parcel();
                $parcel->setTrackingNumber($awb);
                $parcel->save();
            }


            $oSess = new stdClass();
            $oSess->session = $loginSession;
            $oClient = $hClient->adeLogout($oSess);
            $this->pdf->IncludeJS("print();");
            $this->pdf->Output("../_assets/pdf/" . date('Y_m_d') . '/' . $consignment->getId() . ".pdf", "F");
            $output['STATUS'] = 'SUCCESS';
            $output['LABEL'] = date('Y_m_d') . '/' . $consignment->getId() . ".pdf";
            $output['TRACKING_NUMBER'] = $licence_plate_array;
        } catch (SoapFault $fault) {
            $output['STATUS'] = 'ERROR';
            $output['MESSAGE'] = $fault->faultcode . ', FaultString: ' . $fault->faultstring;
        }
        return $output;
    }

    public function recycledShipment($consignment) {
        $output["STATUS"] = "SUCCESS";
        return $output;
    }

}
