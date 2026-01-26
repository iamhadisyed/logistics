<?php

class ViaEurope implements CarrierService {

    private $pdf;
    private $serviceValues = null;
    private $userAccount = null;
    private $country = null;
    private $record_array = null;
    private $bulkParcelNumber = null;
    private $waybillRef = null;
    private $manifestId = null;
    private $waybillMawb = null;

    public function __construct() {
        
    }

    public function validation(Consignment $consignment, Services $service, Country $country) {
        
    }

    public function remoteareas($consignment, $carrierObject, $country) {
        
    }

    public function label($consignment, $labelType = 'pdf', $size = '') {

        $output = array();
        $this->serviceValues = new Services($consignment->getServiceId());
        $this->country = new Country($consignment->getCountryId());
        $user = new User($consignment->getUserId());
        $this->userAccount = new CustomerAccount($user->getUserAccountId());

        /*
         * Service Constant
         */
        $serviceAgentConstantFilter = new ServiceConstantValueFilter();
        $serviceAgentConstantFilter->addFilter("service_id = '" . $consignment->getServiceId() . "' AND agent_id = '" . $consignment->getAgentId() . "' ");
        $serviceAgentConstant = $serviceAgentConstantFilter->getConstantList("c.constant 'constant_name', scv.constant_value 'constant_value'");
        if (count($serviceAgentConstant) > 0) {
            foreach ($serviceAgentConstant as $serviceAgentConstantData) {
                $this->constants[$serviceAgentConstantData->getConstantName()] = $serviceAgentConstantData->getConstantValue();
            }
        }
//        if (trim(@$this->constants['VIAEUROPE_SHIPPER_POSTCODE']) == '' || trim(@$this->constants['OWE_SHIPPER_COUNTRY']) == '' || trim(@$this->constants['OWE_SHIPPER_ADDRESS_LINE1']) == '') {
//            $output['STATUS'] = 'ERROR';
//            $output['MESSAGE'] = "Please check service constants. Please contact to itsupport@oneworldexpress.com";
//            return $output;
//        }


        /*
         *  Get Tracking Number ranges
         */

        $serviceRangeMappingFilter = new ServiceRangeMappingFilter();
        $serviceRangeMappingFilter->addFilter("service_id = '" . $consignment->getServiceId() . "' AND agent_id = '" . $consignment->getAgentId() . "' ");
        $serviceRange = $serviceRangeMappingFilter->getList(" licence_plate_id ");
        if (count($serviceRange) > 0) {
            $licence_plate_id = (int) $serviceRange[0]->getLicencePlateId();
        }
        if ($licence_plate_id < 0) {
            $output['STATUS'] = 'ERROR';
            $output['MESSAGE'] = "This service does not have tracking number range. Please contact to itsupport@oneworldexpress.com";
            return $output;
        }

        $parcel_list = $consignment->getParcels();
        $parcel_count = sizeof($parcel_list);
        $parcel_idx = 0;
        $pdf = new PdfBase(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $this->pdf = $pdf;
        // Generate label for each parecel
        foreach ($parcel_list as $parcel) {
            if ($parcel->getTrackingNumber() == '') {
                $resultArray = LicencePlate::getLicencePlateNumber($licence_plate_id);
                if (trim($resultArray['STATUS']) == 'ERROR')
                    return $resultArray;
                else {
                    $range = $resultArray["RANGE"];
                    $barcode = $resultArray["PREFIX"] . $range . $resultArray["SUFIX"];
                    $licence_plate = $barcode;
                }

                $parcel->setTrackingNumber($licence_plate);
                $parcel->save();
            } else {
                $licence_plate = $parcel->getTrackingNumber();
            }
            $licence_plate_array[$parcel_idx] = $licence_plate;

            $this->pdf->SetPrintFooter(false);
            $this->pdf->SetFooterMargin(0);
            $this->pdf->SetAutoPageBreak(false, 0);
            $page_size = array(100, 150);
            $this->pdf->AddPage("P", $page_size);
            $this->addWayBill($consignment, $parcel_idx, $licence_plate);

            ++$parcel_idx;
        }



        $this->pdf->IncludeJS("print();");
        $this->pdf->Output("../_assets/pdf/" . date('Y_m_d') . '/' . $consignment->getId() . ".pdf", "F");
        $output['STATUS'] = 'SUCCESS';
        $output['LABEL'] = date('Y_m_d') . '/' . $consignment->getId() . ".pdf";
        $output['TRACKING_NUMBER'] = $licence_plate_array;
        return $output;
    }

    public function tracking($trackingNumber, $trackBy = 'parcel', $EDI = false) {
        
    }

    public function sendData($tracking_numbers = array()) {
        $output = [];
        $output["STATUS"] = "SUCCESS";
        if (is_array($tracking_numbers)) {
            $trackingnumberImlode = "'".implode("','", $tracking_numbers)."'";
            $mawbfilter = new MawbParcelMappingFilter();
            $mawbfilter->addJoin("parcel p", "p.id", "mpm.parcel_id", "INNER JOIN");
            $mawbfilter->addFilterIn("    p.tracking_number" ,$trackingnumberImlode);
            $mawbfilter->addFilter(" bag_id > 0");
            $mawbmappinglist = $mawbfilter->getColumnList(" mpm.*");
            if (count($mawbmappinglist) > 0) {
                $oldmawb = "";
                foreach ($mawbmappinglist as $mmlist) {
                    if ($mmlist->getMawbId() > 0) {
                        $mawbNumber = $mmlist->getMawbId();
                        if($oldmawb != $mawbNumber){
                            $mawblist = new Mawb($mmlist->getMawbId());
                            $createWaybillResponse = $this->createWayBill($mawblist->getMawbNumber());
                        }
                        
                        if ($createWaybillResponse) {
                            $bagId = $mmlist->getBagId();
                            $bagging = new Bagging($bagId);
                            $patchWaybill = $this->patchWaybillWithBulkParcel($bagging->getBagNumber());
                            if (!$patchWaybill) {
                                mail("mruga@oneworldexpress.com", "Via Europe Error at patch Waybill", $patchWaybill);
                            }
                        }
                        else
                        {
                            $output["STATUS"] = "ERROR";
                            $output["MESSAGE"] = $createWaybillResponse;
                        }
                        
                    } else {
                        $output["STATUS"] = "ERROR";
                        $output["MESSAGE"] = "Mawb number cannot be blank.";
                    }
                    $oldmawb = $mmlist->getMawbId();
                }
            }
            return json_decode($output);
        }

        


           /* if (!empty($consignmentIdArray)) {

                $sql = "UPDATE consignment SET send_courier_data = 1, booked_file_id = '" . $this->bulkParcelNumber . "'
                                    WHERE id IN ('" . implode("','", $consignmentIdArray) . "') AND id <> '0' 
                                    AND  shipment_status not in ('" . Consignment::STATUS_RECYCLED . "','" . Consignment::STATUS_READY_TO_PRINT . "','" . Consignment::STATUS_INVALID . "')";
                DbAccess3::runQuery($sql);
                $output["STATUS"] = "SUCCESS";
                $output["MESSAGE"] = "System has successfully send data.";
            } else {
                $output["STATUS"] = "ERROR";
                $output["MESSAGE"] = "No consignment found to send data to carrier.";
            }*/
        return json_encode($output);
    }

    public function manifest($consignment) {
        
    }

    public function preAdvice($consignment) {
        
    }

    private function addWayBill(Consignment $consignment, $parcel_idx, $licence_plate) {

        $this->pdf->line(3, 51, 98, 51);
        $this->pdf->line(3, 3, 98, 3);
        $this->pdf->line(98, 3, 98, 23.8);
        $this->pdf->line(3, 3, 3, 30);
        $this->pdf->line(35, 3, 35, 23.8);
        $this->pdf->Line(46, 51, 46, 60);
        $this->pdf->Line(76, 51, 76, 60);
        $this->pdf->line(3, 24, 98, 24);
        $this->pdf->line(3, 24, 3, 115);
        $this->pdf->line(3, 115, 98, 115);
        $this->pdf->line(98, 24, 98, 115);
        $this->pdf->line(3, 60, 98, 60);
        $this->pdf->line(3, 70, 3, 145);
        $this->pdf->line(98, 70, 98, 145);
        $this->pdf->line(3, 133, 98, 133);
        $this->pdf->line(3, 113, 3, 146);
        $this->pdf->line(3, 146, 98, 146);
        $this->pdf->line(98, 113, 98, 146);
        $this->pdf->line(30, 133, 30, 146);


        $logo = User::getUserCompanyImages(false, $consignment->getUserId());

        if (file_exists($logo))
            $this->pdf->image($logo, 40, 4, 49, 15);
        $this->pdf->setFont("helvetica", "L", 9);
        //$this->pdf->Text(45, 18.5, "www.oneworldexpress.com");


        $this->pdf->setFont("helvetica", "b", 13);
        $this->pdf->Text(5, 5, "EUROPEAN");
        $this->pdf->Text(6.5, 10, "TRACKED");
        $this->pdf->Text(8.5, 15, "PARCEL");

        $this->pdf->setFont("helvetica", "b", 10);

        if ($consignment->getValue() >= 0 && $consignment->getValue() < 15) {
            $this->pdf->Text(50, 53, "L");
        } else
        if ($consignment->getValue() >= 15 && $consignment->getValue() < 135) {
            $this->pdf->Text(50, 53, "M");
        } else
        if ($consignment->getValue() >= 135) {
            $this->pdf->Text(50, 53, "H");
        }
        $this->pdf->Text(5, 53, $account);


        if ($this->country->getRegion() == 'INT')
            $this->pdf->Text(80, 53, "NON EU");
        else if ($this->country->getRegion() == 'R1')
            $this->pdf->Text(80, 53, "EU");
        else if ($this->country->getRegion() == 'DBP')
            $this->pdf->Text(80, 53, "UK");

        $this->pdf->Text(5, 62, "Delivery Details:");

        $this->pdf->Text(12, 137, "OWE");
        $pieces = str_pad($parcel_idx + 1, 3, "0", STR_PAD_LEFT);


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
            'text' => true,
            'font' => 'helvetica',
            'fontsize' => 15,
            'stretchtext' => 1
        );


        $this->pdf->write1DBarcode($licence_plate, 'C128', 7, 18, '', 40, 2, $style, '');

        $this->pdf->write1DBarcode($consignment->getHawb(), 'C128', 15, 115, '', 15, 0.5, $style, '');

        $company = $consignment->getCompany();
        $address1 = $consignment->getAddressLine1();
        $address2 = $consignment->getAddressLine2();
        $address3 = $consignment->getAddressLine3();
        $this->pdf->setFont("helvetica", "", 10);
        $this->pdf->Text(5, 67, $consignment->getHawb());
        $this->pdf->Text(5, 71, $company);
        $this->pdf->Text(5, 75, $consignment->getContact());
        $this->pdf->Text(5, 79, $address1);
        $this->pdf->Text(5, 83, $address2);
        $this->pdf->Text(5, 87, $address3);
        $this->pdf->Text(5, 91, $consignment->getCity());
        $this->pdf->Text(5, 95, $consignment->getPostcode());
        $this->pdf->Text(55, 99, "Tel: " . $consignment->getTelephone());
        $fontname = $this->pdf->addTTFfont('../assets/fonts/simhei.ttf', 'TrueTypeUnicode', '', 32);
        $this->pdf->SetFont($fontname, '', 8);
        $this->pdf->Text(5, 105, $consignment->getNotes());


        $this->pdf->setFont("helvetica", "B", 11);
        $this->pdf->Text(5, 99, $this->country->getName());
        $this->pdf->setFont("helvetica", "L", 11);
        $this->pdf->Text(55, 95, $consignment->getReference());

        $this->pdf->setFont("helvetica", "B", 8);
        $this->pdf->Text(34, 133, "Return to:");
        $this->pdf->setFont("helvetica", "L", 8);
        $this->pdf->Text(34, 136, $this->constants['OWE_SHIPPER_ADDRESS_LINE1'] . " " . $this->constants['OWE_SHIPPER_ADDRESS_LINE2']);
        $this->pdf->Text(34, 139, $this->constants['OWE_SHIPPER_ADDRESS_LINE3'] . " " . $this->constants['OWE_SHIPPER_CITY'] . " " . $this->constants['OWE_SHIPPER_POSTCODE']);

        $shipper_country = $this->constants['OWE_SHIPPER_COUNTRY'];
        if ((int) $shipper_country > 0) {
            $shipperCountry = new Country($shipper_country);
            $sCountry = $shipperCountry->getName();
            $sIso = $shipperCountry->getIso();
        } else {
            $sCountry = $shipper_country;
        }
        $this->pdf->Text(34, 142, $sCountry . " " . $sIso);
    }

    public function recycledShipment($consignment) {
        $output["STATUS"] = "SUCCESS";
        return $output;
    }

    public function createBulkParcelLabel($bagId) {
        $clientRef = "BULK" . date("YmdHis");

        $bagging = new Bagging($bagId);
        $bagging->setBagNumber($clientRef);
        $bagging->save();
        $createBulkParcelUrl = "parcels";

        $createBulkParcelRequest = '{
                    "type": "bulk",
                    "client_ref": "' . $clientRef . '",
                    "delivery": {
                      "address": {
                        "name1": "One World Express",
                        "shortcode": "NL1437EP-2",
                        "phone": "02088676060"
                      },
                      "service": "BEX",
                      "weight": 10000,
                      "dimensions": [10, 20, 30]
                    }
                  }';

        $createBulkParcelResponse = $this->curlRequest($createBulkParcelUrl, "POST", $createBulkParcelRequest);
        $createBulkParcelResponse = json_decode($createBulkParcelResponse);
        $error = $createBulkParcelResponse->errors;
        if (count($error) > 0) {
            $output["STATUS"] = "ERROR";
            $output["MESSAGE"] = implode(" , ", $error);
        } else {
            ini_set('max_execution_time', 1200);
            sleep(30);
            $getLabelBulkParcelUrl = "parcels/" . $clientRef . "/delivery";
            $getLabelBulkParcelResponse = $this->curlRequest($getLabelBulkParcelUrl, "GET", "");
            $labelBulkResponse = json_decode($getLabelBulkParcelResponse);

            $bagBarcodeNumber = $labelBulkResponse->tracking_code;
            $baglabelUrl = $labelBulkResponse->label_download_url;
            if ($baglabelUrl == '') {
                $getLabelBulkParcelUrl = "parcels/" . $clientRef . "/delivery";
                $getLabelBulkParcelResponse = $this->curlRequest($getLabelBulkParcelUrl, "GET", "");
                $labelBulkResponse = json_decode($getLabelBulkParcelResponse);
            }
            $file = uniqid();
            $fileName = SETTING_DIR_ASSETS . "bag_pdf/" . $file . ".pdf";
            $bagLabelfile = file_get_contents($baglabelUrl);
            file_put_contents($fileName, $bagLabelfile);
            $output["STATUS"] = "SUCCESS";
            $output["FILENAME"] = $fileName;
        }
        return json_encode($output);
    }

    public function createFlatParcel($trackingNoArray) {
        $createFlatParcelUrl = "parcels";
        $output = array();

        $responseStatus = true;
        if (count($trackingNoArray) > 0) {
            $consignmentFilter = new ConsignmentFilter();
            $consignmentFilter->addFilter("     awb in ('" . implode("'", $trackingNoArray) . "')");
            $list = $consignmentFilter->getColumnList('c.id,awb,hawb,contact, address_line_1,city, c.country_id, postcode, c.weight, c.value, number_pieces, sender_country_id, c.description');
            if (count($list) > 0) {
                $output["STATUS"] = "SUCCESS";
                foreach ($list as $consignment) {
                    $this->country = new Country($consignment->getCountryId());
                    $shipperCountry = new Country($consignment->getSenderCountryId());
                    $createFlatParcelRequest = '{
                        "type": "flat",
                        "barcode": "' . $consignment->getAwb() . '",
                        "client_ref": "' . $consignment->getHawb() . '",
                        "bulk_parcel_client_ref": "' . $this->bulkParcelNumber . '",
                        "declaration": {
                          "consignee": {
                            "name1": "' . $consignment->getContact() . '",
                            "street": "' . $consignment->getAddressLine1() . '",
                            "zip": "' . $consignment->getPostcode() . '",
                            "country": "' . $this->country->getIso() . '",
                            "city": "' . $consignment->getCity() . '"
                          },
                          "type": "sale_b2c_3",
                          "origin_client_ref": "' . $consignment->getHawb() . '",
                          "country_of_export": "CN",
                          "products": [
                            {
                              "description": "' . $consignment->getDescription() . '",
                              "taric": "8205200000",
                              "base_value": 1000,
                              "quantity": ' . $consignment->getNumberPieces() . ',
                              "weight": ' . $consignment->getWeight() * 1000 . ',
                              "country_of_origin": "CN",
                              "client_ref": "' . $consignment->getHawb() . '"
                            }
                          ]
                        }
                      }';

                    $getFlatParcelResponse = $this->curlRequest($createFlatParcelUrl, "POST", $createFlatParcelRequest);
                    $flatParcelResponse = json_decode($getFlatParcelResponse);
                    $error = $flatParcelResponse->errors;
                    if (count($error) > 0) {
                        $responseStatus = false;
                        $errorMessage .= $consignment->getAwb() . " - " . implode(" , ", $error);
                    }
                }
                if ($responseStatus == false) {
                    $output["STATUS"] = "ERROR";
                    $output["MESSAGE"] = $errorMessage;
                }
            }
        }
        return json_encode($output);
    }

    private function createWayBill($mawb) {
        $createWaybillUrl = "waybills";
        $this->waybillRef = "WB" . date("YmdHis");
        $this->waybillMawb;
        $manifest = new Manifest($this->manifestId);
        $manifest->setMawb($this->waybillRef);
        $manifestFile =  SETTING_DIR_ASSETS . "manifest/pdf/" . $manifest->getPdfFile();
        $manifest->save();
        $b64Doc = (base64_encode(file_get_contents($manifestFile)));
        $createWaybillRequest = '{
            "type": "air",
            "carrier_ref": "'. $this->waybillMawb.'",
            "client_ref": "' . $this->waybillRef . '",
            "units": '.$manifest->getPieces().',
            "vol_weight": '. (int)($manifest->getWeight() * 1000).',
            "manifest_base64": "' . $b64Doc . '",
            "address_shortcode": "NL1437EP-2"}';
        $getWaybillResponse = $this->curlRequest($createWaybillUrl, "POST", $createWaybillRequest);
        if ($getWaybillResponse != '') {
            $waybillResponse = json_decode($getWaybillResponse);
            return implode(" , ", $waybillResponse->errors);
        }
        return true;
    }

    public function patchWaybillWithBulkParcel($bulkParcelNumber) {

        
        $createWaybillUrl = "parcels/" . $bulkParcelNumber;
        
        $patchWaybillRequest = '{
            "waybill_client_ref": "' . $this->waybillRef . '"}';
        $getWaybillResponse = $this->curlRequest($createWaybillUrl, "PATCH", $patchWaybillRequest);
        if ($getWaybillResponse != '') {
            $waybillResponse = json_decode($getWaybillResponse);
            return implode(" , ", $waybillResponse->errors);
        }
        return true;
    }

    private function curlRequest($url, $reqeustType, $postfield = "") {

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://app-sandbox.viaeurope.com/api/v4/" . $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $reqeustType,
            CURLOPT_POSTFIELDS => $postfield,
            CURLOPT_HTTPHEADER => array(
                "Accept: application/json",
                'Authorization: Token token="b413c860478ecb177fb247ceae5ac6eb"',
                "Content-Type: application/json",
                "cache-control: no-cache"
            ),
        ));

        $response = curl_exec($curl);
        $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        //echo $httpcode;
        $err = curl_error($curl);
       // print_r($response);
       // print_r($err);
        curl_close($curl);

        if ($err) {
            return "cURL Error #:" . $err;
        } else {
            return $response;
        }
    }
    
    public function setManifestId($manifestId)
    {
        $this->manifestId = $manifestId;
    }
     public function setWaybillMawb($waybill)
    {
        $this->waybillMawb = $waybill;
    }

}
