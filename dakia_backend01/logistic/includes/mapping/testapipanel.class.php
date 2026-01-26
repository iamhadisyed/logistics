<?php

// get settings
//require_once("includes/settings/common.inc.php");

class ApiTesting extends DbAccess3 {

    private $client_id = 'tahirfinance';
    private $client_secret = 'Cybernet123@';
    private $grant_type = 'smarttrack_app';
    private $url = 'http://staging.smarttrack.co';
    private $headerContentType = 'application/json';
    private $postData = "";
    private $authorization = "";
    private $orderReference = "";
    private $trackingNumber = "";
    private $method = "POST";

    public function __construct() {
        
    }

    public function curlRequest() {
        $return = [];
        $url = $this->url . $this->link;
        $return['apiurl'] = $url;
         if($this->method=="POST"){
            $ch = curl_init($url);
         }else{
             $ch = curl_init();
              curl_setopt($ch, CURLOPT_URL,$url);
              
         }
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLINFO_HEADER_OUT, true);
        
        if($this->method=="POST"){
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $this->postData);
        }

        // Set HTTP Header for POST request 
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: ' . $this->headerContentType, $this->authorization)
        );

        // receive server response ...
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $server_output = curl_exec($ch);
        
        $return['jsonDecodeError'] = $this->checkJsonDecodeError($server_output);
        $return['originalResponse'] =  $server_output ;
        $return['response'] = $this->removeBomUtf8($server_output);


        if (curl_errno($ch)) {
            $return['apiStatus'] = 'error';
            $return['details'] = 'Couldn\'t send request: ' . curl_error($ch);
        } else {
            // check the HTTP status code of the request
            $resultStatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $return['details'] = 'HTTP status code: ' . $resultStatus;
            if ($resultStatus == 200) {

                $return['apiStatus'] = 'success';
            } else {

                $return['apiStatus'] = 'error';
            }
        }
        curl_close($ch);
        return $return;
    }

    public function accessToken() {
        $this->link = "/api/token";
        $this->headerContentType = "application/x-www-form-urlencoded";
        $this->postData = http_build_query(
                array(
                    'grant_type' => $this->grant_type,
                    'client_id' => $this->client_id,
                    'client_secret' => $this->client_secret,
                )
        );
        $result = $this->curlRequest();
        return $result;
    }

    public function addShipment() {
        $this->link = "/api/add-shipment";
        $this->headerContentType = "application/json";
        $this->postData = '{
                            "sender_country_iso":"PL",
                            "service_code":"STYDL2CXN",
                            "order_reference":"",
                            "sender_contact":"Janina Pawłowska",
                            "sender_email":null,
                            "sender_company":"Efero sp. z.o.o. sp.k.",
                            "sender_name":"Janina Pawłowska",
                            "sender_address_line_1":"Wysokogorska 3a/8,",
                            "sender_address_line_2":null,
                            "sender_address_line_3":null,
                            "sender_city":"BOLKOW",
                            "sender_state":null,
                            "sender_postcode":"59-420",
                            "sender_telephone":"+48699955224",
                            "receiver_country_iso":"GB",
                            "receiver_contact":"Aleksandra Pawlowska",
                            "receiver_email":null,
                            "receiver_company":"Aleksandra Pawlowska",
                            "receiver_address_line_1":"44 OLD FORT LODGE",
                            "receiver_address_line_2":null,
                            "receiverr_address_line_3":null,
                            "receiver_city":"CRAIGAVON",
                            "receiver_state":"COUNTY ARMAGH",
                            "receiver_postcode":"UB3 3NB",
                            "receiver_telephone":"+447594656514",
                            "fullpallets":0,
                            "halfpallets":0,
                            "qtrpallets":0,
                            "palletlifts":0,
                            "value":"330",
                            "currency":"PLN",
                            "item_type":"Packets",
                            "notes":"test",
                            "description":"Zestaw kuchenny", 
                            "parcel":
                                  [{"weight" :2.3,
                            "height":10,
                            "width":10,
                            "length":10
                                  }]
                            }';
        return $this->curlRequest();
    }

    public function getLabel() {
        $this->link = "/api/get-label";
        $this->headerContentType = "application/json";
        $this->postData = '{"order_reference":"'.$this->orderReference.'","label_type":"pdf","label_size":"100x150"}';
        return $this->curlRequest();
    }
    
      public function getTracking(){
        $this->method = "GET";
       $this->link = "/api/get-tracking/".$this->trackingNumber;
        $this->headerContentType = "";
        return $this->curlRequest();
    }
    
    public function getVoidLabels(){
        $this->method = "POST";
        $this->link = "/api/VoidLabels";
        $this->headerContentType = "multipart/form-data";
        $this->postData =  ['hawb[0]'=>$this->orderReference ]  ;
        return $this->curlRequest();
    }
    
    public function setAuthorizationToken($authorization) {
        $this->authorization = "Authorization: Bearer " . $authorization;
    }
    
        public function setOrderReference($orderReference) {
        $this->orderReference = $orderReference;
    }

      public function setTrackingNumber($trackingData) {
     
          $this->trackingNumber = $trackingData; 
    }
    
    function removeBomUtf8($s){
        if(substr($s,0,3)==chr(hexdec('EF')).chr(hexdec('BB')).chr(hexdec('BF'))){
             return substr($s,3);
         }else{
             return $s;
         }
     }
     
     function checkJsonDecodeError($json){
            $jsonDecodeError = "";
            $jsonDecode = json_decode($json);
            switch (json_last_error()) {
                case JSON_ERROR_NONE:
                     $jsonDecodeError =  ' - No errors';
                break;
                case JSON_ERROR_DEPTH:
                     $jsonDecodeError =  ' - Maximum stack depth exceeded';
                break;
                case JSON_ERROR_STATE_MISMATCH:
                     $jsonDecodeError =  ' - Underflow or the modes mismatch';
                break;
                case JSON_ERROR_CTRL_CHAR:
                     $jsonDecodeError =  ' - Unexpected control character found';
                break;
                case JSON_ERROR_SYNTAX:
                     $jsonDecodeError =  ' - Syntax error, malformed JSON';
                break;
                case JSON_ERROR_UTF8:
                     $jsonDecodeError =  ' - Malformed UTF-8 characters, possibly incorrectly encoded';
                break;
                default:
                     $jsonDecodeError =  ' - Unknown error';
                break;
            } 
            
            return $jsonDecodeError;
     }

}