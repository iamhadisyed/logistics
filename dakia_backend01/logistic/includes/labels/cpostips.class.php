<?php

include_classes([
    'serviceauthenticationtoken.class',
    'serviceauthenticationtokenfilter.class'
]);

class CpostIps implements CarrierService {

    private $pdf;
    protected $apiToken;

    public function validation(Consignment $consignment, Services $service, Country $country) {
        
    }

    public function remoteareas($consignment, $carrierObject, $sender) {
        
    }

    public function label($consignment, $type = "pdf", $size = "100x150") {

        $output = array();
        $this->serviceValues = new Services($consignment->getServiceId());
        $this->country = new Country($consignment->getCountryId());


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
                    $checkdigit = "";
                    if ($this->serviceValues->getCode() == "STCPOSTTR") {
                        $checkdigit = LicencePlate::mod11($resultArray["RANGE"]);
                    }
                    $licence_plate = $resultArray["PREFIX"] . $resultArray["RANGE"] . $checkdigit . $this->country->getIso();
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
            $this->addWayBill($consignment, $parcel_idx, $parcel_count, $licence_plate);

            $new_page_flag = true;
            ++$parcel_idx;
        }


        $this->pdf->IncludeJS("print();");
        $this->pdf->Output("../_assets/pdf/" . date('Y_m_d') . '/' . $consignment->getId() . ".pdf", "F");
        $output['STATUS'] = 'SUCCESS';
        $output['LABEL'] = date('Y_m_d') . '/' . $consignment->getId() . ".pdf";
        $output['TRACKING_NUMBER'] = $licence_plate_array;
        return $output;
    }

    public function tracking($trackingNumber, $trackBy, $EDI) {
        
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

    private function addWayBill(Consignment $consignment, $parcel_idx, $parcel_count, $licence_plate) {

        $handling = $this->serviceValues->getCode();
        $image = realpath("../images/cpostindicia.png");
        $this->pdf->image($image, 3, 3.5, 60, 20);


        $image1 = realpath("../images/logo.jpg");
        $this->pdf->image($image1, 65, 8, 30);

        $this->pdf->setFont("helvetica", "", 8);
        $this->pdf->MultiCell(45, 10, ucfirst(strtolower($consignment->getNotes())), 0, 'L', false, '', 18, 59);
        $this->pdf->line(3, 58, 98, 58);
        $this->pdf->line(3, 3, 98, 3);
        $this->pdf->line(98, 3, 98, 23.8);
        $this->pdf->line(3, 3, 3, 30);

        $this->pdf->setFont("helvetica", "b", 10);
        $this->pdf->Text(5, 59, "Notes:");

        if ($parcel_count > 0) {
            $this->pdf->setFont("helvetica", "b", 14);
            $this->pdf->Text(63, 59, "Pieces : " . ($parcel_idx + 1) . "/" . $parcel_count);
        }

        $this->pdf->setFont("helvetica", "", 12);
        $this->pdf->Text(5, 71, "Delivery Details:");


        $this->pdf->Text(11, 136.5, $this->country->getIso());
        $this->pdf->setFont("helvetica", "L", 9);

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


        $this->pdf->write1DBarcode($licence_plate, 'C128', 7, 25, '', 40, 2, $style, '');


        if ($this->country->getRegion() == "INT")
            $this->pdf->Text(7 + 25, 25 + 1, "For internal use only");


        $style = array(
            'position' => '',
            'align' => 'C',
            'stretch' => true,
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
            'stretchtext' => 1
        );

        $this->pdf->write1DBarcode($consignment->getHawb(), 'C128', 3, 115, '', 20, 2, $style, 'Y');



        $this->pdf->line(3, 24, 98, 24);
        $this->pdf->line(3, 24, 3, 115);
        $this->pdf->line(3, 115, 98, 115);
        $this->pdf->line(98, 24, 98, 115);
        $this->pdf->line(3, 70, 98, 70);
        $this->pdf->line(3, 70, 3, 145);
        $this->pdf->line(98, 70, 98, 145);
        $this->pdf->line(3, 133, 98, 133);
        $this->pdf->line(3, 113, 3, 146);
        $this->pdf->line(3, 146, 98, 146);
        $this->pdf->line(98, 113, 98, 146);
        $this->pdf->line(30, 133, 30, 146);
        $address = "";
        $company = "";
        if ($consignment->getCompany() != "")
            $company = $consignment->getCompany();
        if ($consignment->getAddressLine1() != "")
            $address1 = $consignment->getAddressLine1();
        if ($consignment->getAddressLine2() != "")
            $address2 = $consignment->getAddressLine2();
        if ($consignment->getAddressLine3() != "")
            $address3 = $consignment->getAddressLine3();



        $this->pdf->setFont("helvetica", "", 10);
        $this->pdf->Text(5, 77, $consignment->getHawb());
        $this->pdf->Text(5, 81, $company);
        $this->pdf->Text(5, 85, $consignment->getContact());
        $this->pdf->Text(5, 89, $address1);
        $this->pdf->Text(5, 93, $address2);
        $this->pdf->Text(5, 97, $address3);
        $this->pdf->Text(5, 101, $consignment->getCity());
        $this->pdf->Text(5, 105, $consignment->getPostcode());
        $this->pdf->setFont("helvetica", "B", 11);
        $this->pdf->Text(5, 109, $this->country->getName());
        $this->pdf->setFont("helvetica", "L", 11);

        $this->pdf->setFont("helvetica", "B", 8);
        $this->pdf->Text(34, 133, "if undelivered return to:");

        $this->pdf->setFont("helvetica", "L", 8);
        $this->pdf->Text(34, 136, "P.o.Box 10006");
        $this->pdf->Text(34, 139, "Curacao");
    }

    /*
     * Create Mawb
     */

    public function createMawb($data) {
        $output = array();

        /*
         * Create Dispatch
         */

        $dispatchRequest = array();

        $dispatchRequest["mail_category"] = $data['mail_category'];
        $dispatchRequest["mail_subclass"] = $data['mail_subclass'];
        $dispatchRequest["origin_office_location"] = $data['origin_office_location'];
        $dispatchRequest["destination_office_location"] = $data['destination_office_location'];
        // origin location needs to be dynmic when mruga provide service country codes
        $dispatchRequest["origin_location"] = $data['origin_location'];
        $dispatchRequest["destination_location"] = $data['destination_location'];
        $dispatchRequest["user_id"] = $data['user_id'];
        $dispatchRequest["flight_no"] = $data['flight_number'];

        $response = $this->prepareRequest(json_encode($dispatchRequest), "dispatch");
        if ($response["STATUS"] == "SUCCESS") {
            $dispatchResponse = $response["MESSAGE"];
            
            if ($dispatchResponse->response->success == false) {
                $output["STATUS"] = "ERROR";
                $output["MESSAGE"] = $dispatchResponse->response->error;
            } else {
                $output["STATUS"] = "SUCCESS";
                $output["MAWB_NUMBER"] = $dispatchResponse->response->data->fid;
            }
        } else {
            $output = $response;
        }
        return $output;
    }

    /*
     * Close Dispatch
     */

    public function closeMawb($mawbNo) {
        $output = array();
        $closeDispatchRequest = array();
        $closeDispatchRequest["dispatch_no"] = $mawbNo;
        $closeDispatchRequest["user_id"] = "oneworld";
        $closeDispatchRequest["close_consignment"] = "1";
        $response = $this->prepareRequest(json_encode($closeDispatchRequest), "closedispatch");
        if ($response["STATUS"] == "SUCCESS") {
            $closeDispatchResponse = $response["MESSAGE"];
            if ($closeDispatchResponse->response->success == false) {
                $output["STATUS"] = "ERROR";
                $output["MESSAGE"] = $closeDispatchResponse->response->error;
                return $output;
            } else {
                $output["STATUS"] = "SUCCESS";
                $output["MESSAGE"] = $mawbNo . " closed successfully.";
            }
        } else {
            $output = $response;
        }

        return $output;
    }

    /*
     * Add bag items to bag
     */

    public function addBagItems($consignmentList, $mawbNumber) {
        $mailItemArray = array();
        $mailItemArray["seal_no"] = "2346435";
        
        $mailItemArray["user_id"] = "oneworld";
        $mailItemArray["dispatch_no"] = $mawbNumber;

        $itemArrayList = array();
        $totalWeight = 0;
        foreach ($consignmentList as $consignment) 
        {
            $totalWeight += $consignment->getWeight();
            $senderCountry = new Country($consignment->getSenderCountryId());
            $receiverCountry = new Country($consignment->getCountryId());
            $itemArray = array();
            if(empty($consignment->getValue())) {
                $itemValue = 0.00;
            } else {
                $itemValue = $consignment->getValue();
            }
            $itemArray["item_id"] = $consignment->getTrackingNumber();
            $itemArray["item_weight"] = number_format($consignment->getWeight());
            $itemArray["item_currency"] = $consignment->getCurrency();
            $itemArray["item_value"] = $itemValue;
            $itemArray["origin"] = $senderCountry->getIso();
            $itemArray["destination"] = $receiverCountry->getIso();
            $itemArray["postal_status"] = "MINL";
            $itemArray["letter"] = array("handling_class" => "RG");

            $recipientArray = array();
            $recipientArray["name"] = $consignment->getContact();
            $recipientArray["fore_name"] = $consignment->getContact();
            $recipientArray["address"] = $consignment->getAddressLine1();
            $recipientArray["city"] = $consignment->getCity();
            $recipientArray["post_code"] = $consignment->getPostcode();
            $recipientArray["country_code"] = $receiverCountry->getIso();
            $recipientArray["phone_no"] = $consignment->getTelephone();//$consignment->getTelephone();

            $itemArray["recipient"] = $recipientArray;

            $senderArray = array();
            $senderArray["name"] = $consignment->getSenderName();
            $senderArray["fore_name"] = $consignment->getSenderName();
            $senderArray["address"] = $consignment->getSenderAddressLine1();
            $senderArray["city"] = $consignment->getSenderCity();
            $senderArray["post_code"] = $consignment->getSenderPostcode();
            $senderArray["country_code"] = $senderCountry->getIso();
            $senderArray["phone_no"] = $consignment->getSenderTelephone();//$consignment->getSenderTelephone();

            $itemArray["sender"] = $senderArray;
            $itemArrayList[] = $itemArray;
        }
        $mailItemArray["bag_weight"] = $totalWeight;
        $mailItemArray["items"] = $itemArrayList;
        $response = $this->prepareRequest(json_encode($mailItemArray), "mailitem");
        if ($response["STATUS"] == "SUCCESS") {
            $mailItemResponse = $response["MESSAGE"];
            if ($mailItemResponse->response->success == false) {
                $output["STATUS"] = "ERROR";
                $output["MESSAGE"] = $mailItemResponse->response->error;
            } else {
                $output["STATUS"] = "SUCCESS";
                $output["BAG_NUMBER"] = $mailItemResponse->response->bag_id;
            }
        } else {
            $output = $response;
        }
        return $output;
    }

    /*
     * Reopen Dispatch
     */

    public function reOpenDispatchItem($dispatchNo) {
        $output = array();
        $reopenDispatchRequest = array();
        $reopenDispatchRequest["dispatch_no"] = $dispatchNo;
        $reopenDispatchRequest["user_id"] = "oneworld";
        $response = $this->prepareRequest(json_encode($reopenDispatchRequest), "reopendispatch");
        if ($response["STATUS"] == "SUCCESS") {
            $reopenDispatchResponse = $response["MESSAGE"];
            if ($reopenDispatchResponse->response->success == false) {
                $output["STATUS"] = "ERROR";
                $output["MESSAGE"] = $reopenDispatchResponse->response->error;
                return $output;
            } else {
                $output["STATUS"] = "SUCCESS";
                $output["MESSAGE"] = $dispatchNo . " reopen successfully.";
            }
        } else {
            $output = $response;
        }
        return $output;
    }

    /*
     * Remove bag from Dispatch
     */

    public function removeBag($dispatchNo, $bagNo) {
        $output = array();
        $removeBagRequest = array();
        $removeBagRequest["bag_id"] = $bagNo;
        $removeBagRequest["dispatch_no"] = $dispatchNo;
        $removeBagRequest["user_id"] = "oneworld";
        $response = $this->prepareRequest(json_encode($removeBagRequest), "removebag");
        if ($response["STATUS"] == "SUCCESS") {
            $removeBagResponse = $response["MESSAGE"];
            if ($removeBagResponse->response->success == false) {
                $output["STATUS"] = "ERROR";
                $output["MESSAGE"] = $removeBagResponse->response->error;
                return $output;
            } else {
                $output["STATUS"] = "SUCCESS";
                $output["MESSAGE"] = $bagNo . " is removed from " . $dispatchNo . " dispatch.";
            }
        } else {
            $output = $response;
        }
        return $output;
    }

    /*
     * close Bag
     */

    public function closeBag($mawbNumber, $bagNo) {
        $output = array();
        $closeBagRequest = array();
        $closeBagRequest["bag_id"] = $bagNo;
        $closeBagRequest["dispatch_no"] = $mawbNumber;
        $closeBagRequest["user_id"] = "oneworld";
        $response = $this->prepareRequest(json_encode($closeBagRequest), "closebag");
        if ($response["STATUS"] == "SUCCESS") {
            $closeBagResponse = $response["MESSAGE"];
            if ($closeBagResponse->response->success == false) {
                $output["STATUS"] = "ERROR";
                $output["MESSAGE"] = $closeBagResponse->response->error;
                return $output;
            } else {
                $output["STATUS"] = "SUCCESS";
                $output["MESSAGE"] = $bagNo . " is closed.";
            }
        } else {
            $output = $response;
        }
        return $output;
    }

    /*
     * remove item from bag
     */

    public function removeItemBag($dispatchNo, $bagNo, $itemId) {
        $output = array();
        $removeItemBagRequest = array();
        $removeItemBagRequest["bag_id"] = $bagNo;
        $removeItemBagRequest["dispatch_no"] = $dispatchNo;
        $removeItemBagRequest["user_id"] = "oneworld";
        $removeItemBagRequest["item_id"] = $itemId;
        $response = $this->prepareRequest(json_encode($removeItemBagRequest), "removeitembag");
        if ($response["STATUS"] == "SUCCESS") {
            $removeItemBagResponse = $response["MESSAGE"];
            if ($removeItemBagResponse->response->success == false) {
                $output["STATUS"] = "ERROR";
                $output["MESSAGE"] = $removeItemBagResponse->response->error;
                return $output;
            } else {
                $output["STATUS"] = "SUCCESS";
                $output["MESSAGE"] = $itemId . " is removed from " . $bagNo . ".";
            }
        } else {
            $output = $response;
        }
        return $output;
    }

    /*
     * Get Token
     */

    public function authenticaticationToken() {

        $serviceAuthenticationTokenFilter = new serviceAuthenticationTokenFilter();
        $tokenList = $serviceAuthenticationTokenFilter->getAuthenticationToken();

        if (count($tokenList) == 0) {
            $output = $this->generateToken();
        } else {
            $output["STATUS"] = "SUCCESS";
            $output["MESSAGE"] = $tokenList[0]->getToken();
        }
        return $output;
    }

    private function generateToken() {
        $url = "https://ipstest.cpostinternational.com:8041/oauth/token";
        $postField = array(
            'grant_type' => 'password',
            'client_id' => '12',
            'client_secret' => 'Swp0oQ6Ddnl87OTGMoafMPFZOKUK8EFyeCXyAtFm',
            'username' => 'transexpress@gmail.com',
            'password' => '123456');

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => $postField,
        ));

        $response = curl_exec($curl);
        curl_close($curl);
        $output = array();
        $tokenResponse = json_decode($response);
        if ($tokenResponse->error) {
            $output["STATUS"] = "ERROR";
            $output["MESSAGE"] = $tokenResponse->error_description;
        } else {
            $tokenStartTime = date("Y-m-d H:i:s");
            $tokenExpireTime = date("Y-m-d ", strtotime("+1 day")) . date("H:i:s", $tokenResponse->expires_in);

            $accessToken = $tokenResponse->access_token;
            $refreshToken = $tokenResponse->refresh_token;

            $serviceAuthenticationToken = new serviceAuthenticationToken();
            $serviceAuthenticationToken->setToken($accessToken);
            $serviceAuthenticationToken->setRefreshToken($refreshToken);
            $serviceAuthenticationToken->setTokenStartTime($tokenStartTime);
            $serviceAuthenticationToken->setTokenExpireTime($tokenExpireTime);
            $serviceAuthenticationToken->setServiceName("CPOSTIPS");
            $serviceAuthenticationToken->save();

            $output["STATUS"] = "SUCCESS";
            $output["MESSAGE"] = $tokenResponse->access_token;
        }
        return $output;
    }

    private function prepareRequest($request, $urllocation) {
        $getToken = $this->authenticaticationToken();
        if ($getToken["STATUS"] == "ERROR") {
            return $getToken;
        } else {
            $authToken = $getToken["MESSAGE"];
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => "https://ipstest.cpostinternational.com:8041/api/v1/ips/" . $urllocation,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => $request,
                CURLOPT_HTTPHEADER => array(
                    "Accept: application/json",
                    "Authorization: Bearer  " . $authToken,
                    "Content-Type: application/json",
                    "Content-Type: text/plain"
                ),
            ));

            $responseJson = curl_exec($curl);
            $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            curl_close($curl);
            if ($http_code == 200) {
                $responseArray["STATUS"] = "SUCCESS";
                $responseArray["MESSAGE"] = json_decode($responseJson);
            } else if ($http_code == 401) {
                $response = $this->generateToken();
                if ($response["STATUS"] == "ERROR") {
                    return $response;
                } else {
                    $this->prepareRequest($request, $urllocation);
                }
            } else {
                $responseArray["STATUS"] = "ERROR";
                $responseArray["MESSAGE"] = json_decode($responseJson);
            }
        }
        return $responseArray;
    }

}

?>