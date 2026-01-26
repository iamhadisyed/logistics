<?php

// get settings
//require_once("includes/settings/common.inc.php");

class ApiTestPanel extends DbAccess3 {

    private $client_id ;
    private $client_secret ;
    private $grant_type ;
    private $url;
                
    private $headerContentType = 'application/json';
    private $postData = "";
    private $authorization = "";
    private $orderReference = "";
    private $trackingNumber = "";
    private $method = "POST";
    private $weight  ;
    private $length  ;
    private $width  ;
    private $height ;
    private $receiver_iso ;
    private $receiver_postcode ;
    
    
    private $handlingArray = [
        
                             "STYDL2CXN" => [
                                            "service_code" => "STYDL2CXN",
                                            "service_name" => "Yodel @MINI PACK 48",
                                            "carrier" => "Yodel",
                                            "sender_iso" => "GB",
                                            "receiver_iso" => "GB"
                                            ],
        
                              "STANPSTIE" =>  [
                                        "service_code" => "STANPSTIE",
                                        "service_name" => "ANPOST",
                                        "carrier" => "An Post",
                                        "sender_iso" => "GB",
                                        "receiver_iso" => "GB"
                                ],

                               "STAMX0PPX" => [
                                        "service_code" => "STAMX0PPX",
                                        "service_name" => "ARAMEX PRIOIRTY PARCEL EXPRESS",
                                        "carrier" => "Aramex",	
                                        "sender_iso" => "GB",
                                        "receiver_iso" => "GB"
                                ],
                               "STASESTND" => [
                                        "service_code" => "STASESTND",
                                        "service_name" => "ASENDIA UK",
                                        "carrier" => "ASENDIA UK",
                                        "sender_iso" => "GB",
                                        "receiver_iso" => "GB"
                                ],
                                "STWND0STD" => [
                                                    "service_code" => "STWND0STD",
                                                    "service_name" => "WN DIRECT",
                                                    "carrier" => "WN DIRECT",
                                                    "sender_iso" => "GB",
                                                    "receiver_iso" => "PL"
                                            ],

                                "STBRTSTND" => [
                                                       "service_code" => "STBRTSTND",
                                                       "service_name" => "BRT ITALY",
                                                       "carrier" => "BRT ITALY",
                                                       "sender_iso" => "GB",
                                                       "receiver_iso" => "IT"
                                               ],
                                 "STCTTEXPR" => [
                                                       "service_code" => "STCTTEXPR",
                                                       "service_name" => "CTT EXPRESSO",
                                                       "carrier" => "CTT",
                                                       "sender_iso" => "GB",
                                                       "receiver_iso" => "PT"
                                               ],
        
                                    "STDACSTND" => [
                                                       "service_code" => "STDACSTND",
                                                       "service_name" => "DAC ITALY",
                                                       "carrier" => "DAC",
                                                       "sender_iso" => "GB",
                                                       "receiver_iso" => "IT"
                                               ],
        
                                "STDEUMPPR" => [
                                                       "service_code" => "STDEUMPPR",
                                                       "service_name" => "PACKET UNTRACKED  PRIORITY",
                                                       "carrier" => "Deutsche Post",
                                                       "sender_iso" => "GB",
                                                       "receiver_iso" => "IE"
                                               ],
        
                                "STDEUPTPR" => [
                                                       "service_code" => "STDEUPTPR",
                                                       "service_name" => "PACKET TRACKED PRIORITY",
                                                       "carrier" => "Deutsche Post",
                                                       "sender_iso" => "GB",
                                                       "receiver_iso" => "ES"
                                               ],
        
                                "STDHL0WPX" => [
                                                       "service_code" => "STDHL0WPX",
                                                       "service_name" => "DHL EXPRESS WORLD WIDE (NON DOCS)",
                                                       "carrier" => "DHL UK",
                                                       "sender_iso" => "GB",
                                                       "receiver_iso" => "SG"
                                               ],
                                "STGLSNLST" => [
                                                       "service_code" => "STGLSNLST",
                                                       "service_name" => "GLS NL STANDARD",
                                                       "carrier" => "GLS NL",
                                                       "sender_iso" => "GB",
                                                       "receiver_iso" => "BG"
                                               ],
        
                                "STHUXSTND" => [
                                                       "service_code" => "STHUXSTND",
                                                       "service_name" => "HUX STANDARD",
                                                       "carrier" => "Huxloe",
                                                       "sender_iso" => "GB",
                                                       "receiver_iso" => "DE"
                                               ],
                                "STKAB0STD" => [
                                                       "service_code" => "STKAB0STD",
                                                       "service_name" => "KAAB",
                                                       "carrier" => "KAAB",
                                                       "sender_iso" => "GB",
                                                       "receiver_iso" => "SK"
                                               ],
        
                                "STOWESA01" => [
                                                       "service_code" => "STOWESA01",
                                                       "service_name" => "OWE SOUTH AFRICA",
                                                       "carrier" => "One World",
                                                       "sender_iso" => "GB",
                                                       "receiver_iso" => "ZA"
                                               ],
        
                                "STSWNREGM" => [
                                                       "service_code" => "STSWNREGM",
                                                       "service_name" => "SWEDEN POST REGISTERED",
                                                       "carrier" => "Sweden Post",
                                                       "sender_iso" => "GB",
                                                       "receiver_iso" => "IT"
                                               ],
        
                                "STVIVECOM" => [
                                                       "service_code" => "STVIVECOM",
                                                       "service_name" => "VIVA E-COM",
                                                       "carrier" => "VIVA",
                                                       "sender_iso" => "GB",
                                                       "receiver_iso" => "PK"
                                               ],
        
                                "STRYMINPR" => [
                                                       "service_code" => "STRYMINPR",
                                                       "service_name" => "ROYAL MAIL INTERNATIONAL PRIORITY ",
                                                       "carrier" => "ROYAL MAIL",
                                                       "sender_iso" => "GB",
                                                       "receiver_iso" => "DK"
                                               ],
        
                                "STHRM0002" => [
                                                       "service_code" => "STHRM0002",
                                                       "service_name" => "HERMES 2 DAY",
                                                       "carrier" => "Hermes UK",
                                                       "sender_iso" => "GB",
                                                       "receiver_iso" => "GB"
                                               ],
                
				];
    private $addresses=   [
            
            
           "sender" =>[
                         "GB" => [
                         "address_line_1" => "One World House, Pump Ln",
                         "city" => "Hayes",
                         "post_code" => "UB3 3NB",
                         "country_iso" => "GB",
                         ]
           ],
        
           "receiver" =>[
                         "GB" => [
                         "address_line_1" => "1 kiln close",
                         "city" => "Hayes",
                         "post_code" => "UB3 5DZ",
                         "country_iso" => "GB",
                         "state" => "Hayes",
                         ],
                  "PL" => [
                         "address_line_1" => "Sadowa 14n",
                         "city" => "NAKLO NAD NOTECIA",
                         "post_code" => "89-100",
                         "country_iso" => "PL",
                         "state" => "Hayes",
                         ],
                    "IT" => [
                         "address_line_1" => "VIALE NICOL PAGANINI, 1",
                         "city" => "SAN BENEDETTO",
                         "post_code" => "63074",
                         "country_iso" => "IT",
                         "state" => "TRONTO",
                         ],
                     "PT" => [
                         "address_line_1" => "Rua da calçada 28A",
                         "city" => "Caniço",
                         "post_code" => "9125-052",
                         "country_iso" => "PT",
                         "state" => "",
                         ],
                     "IE" => [
                         "address_line_1" => "189 WILLOW COURT",
                         "city" => "Ballincollig Co.Cork",
                         "post_code" => "P31 FK84",
                         "country_iso" => "IE",
                         "state" => "",
                         ],
                      "DE" => [
                         "address_line_1" => "189 WILLOW COURT",
                         "city" => "Ballincollig Co.Cork",
                         "post_code" => "P31 FK84",
                         "country_iso" => "DE",
                         "state" => "",
                         ],
                   "ES" => [
                         "address_line_1" => "carrer dels carders 17,2-1",
                         "city" => "barcelona",
                         "post_code" => "08003",
                         "country_iso" => "ES",
                         "state" => "",
                         ],
                   "SG" => [
                         "address_line_1" => "53 Grange Road 19-07 Spring Grove",
                         "city" => "Singapore",
                         "post_code" => "249565",
                         "country_iso" => "SG",
                         "state" => "",
                         ],
                    "BG" => [
                         "address_line_1" => "ul. Baba Tonka 48",
                         "city" => "Bulgaria",
                         "post_code" => "249565",
                         "country_iso" => "BG",
                         "state" => "Bulgaria",
                         ],
                   "SK" => [
                         "address_line_1" => "Piešťanská 1",
                         "city" => "Kosice",
                         "post_code" => "04011",
                         "country_iso" => "SK",
                         "state" => "",
                         ],
                     "ZA" => [
                         "address_line_1" => "11 Willowbrooke Lane",
                         "city" => "Kosice",
                         "post_code" => "7806",
                         "country_iso" => "ZA",
                         "state" => "",
                         ],
               ]
               
                
        ];
    private $currHandlingServiceCode ;

    public function __construct( ) {
        //by default set local
        $this->client_id = 'owecorp';
        $this->client_secret = 'Cybernet123@';
        $this->grant_type = 'smarttrack_app';
        $this->url = 'http://local.oneworldexpress.co.uk';
                
    }

    public function setApiCredentials($site,$client_id,$client_secret,$receiver_iso,$receiver_postcode,$manualSiteUrl=""){
        
         $this->grant_type = 'smarttrack_app';
         
       
         if(!empty($site)){
                    if($site=='1'){
                       $this->client_id = 'owecorp';
                        $this->client_secret = 'Cybernet123@';
                        $this->url = 'https://www.smarttrack.co';
                    }else  if($site=='2'){
                        $this->client_id = 'owecorp';
                        $this->client_secret = 'Cybernet123@';         
                        $this->url = 'http://beta.smarttrack.co';
                    }else  if($site=='3'){
                        $this->client_id = 'tahirfinance';
                        $this->client_secret = 'Cybernet123@';
                        $this->url = 'http://staging.smarttrack.co';
                    }else if($site=='4'){
                       $this->client_id = 'owecorp';
                        $this->client_secret = 'Cybernet123@';
                        $this->url = 'http://test.smarttrack.co';
                    }
                    else if($site=='dev'){
                       $this->client_id = 'owecorp';
                        $this->client_secret = 'Cybernet123@';
                        $this->url = 'http://developer.smarttrack.co';
                    }   
                    else if($site=='sandbox'){
                       $this->client_id = 'tahirfinance';
                        $this->client_secret = 'Cybernet123@';
                        $this->url = 'http://sandbox.smarttrack.co';
                    }   
                    else if($site=='local'){
                        $this->client_id = 'owecorp';
                        $this->client_secret = 'Cybernet123@';
                        $this->url = 'http://local.oneworldexpress.co.uk';
                    } 

         }
                
       if(!empty($manualSiteUrl)){
             $this->url = $manualSiteUrl;
        }

        if(!empty($client_id) && !empty($client_secret)){
             $this->client_id = $client_id;
            $this->client_secret = $client_secret;
        }
        
        if(!empty($receiver_iso)){
            $this->receiver_iso = $receiver_iso;
        }
        
        if(!empty($receiver_postcode)){
             $this->receiver_postcode = $receiver_postcode;
        }
                
     // echo  $this->client_id.'  -  '.$this->client_secret.'  -  '.$this->url;exit;
                
    }

    public function curlRequest() {
                
            $return  = [];
                
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
        
        
        //set sending data value
        
         if($this->method=="POST"){
           $return['dataSent'] =  $this->postData ;    
         }else{
           $return['dataSent'] =  $url ;  
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
        /* if($this->currHandlingServiceCode && $this->currHandlingServiceCode!=''){
            $returnmulti[$this->currHandlingServiceCode] = $return;
            return $returnmulti;
        }else{
           return $return;
        }*/
        
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
        $currHandlingCodeData = $this->handlingArray[$this->currHandlingServiceCode];
                
        $receiver_company  = "Receiver Company";
        if($this->currHandlingServiceCode=='STHUXSTND'){
            $receiver_company = "";
        }
          $this->postData = '{
                            "sender_country_iso":"##sender_country_iso##",
                            "service_code":"##service_code##",
                            "order_reference":"",
                            "sender_contact":"Sender OneWorld IT",
                            "sender_email":null,
                            "sender_company":"Developer OneWorld Express I",
                            "sender_name":"Developer OneWorld",
                            "sender_address_line_1":"##sender_address_line_1##",
                            "sender_address_line_2":null,
                            "sender_address_line_3":null,
                            "sender_city":"##sender_city##",
                            "sender_state":null,
                            "sender_postcode":"##sender_post_code##",
                            "sender_telephone":"02088676060",
                            "receiver_country_iso":"##receiver_country_iso##",
                            "receiver_contact":"Receiver OneWorld IT",
                            "receiver_email":null,
                            "receiver_company":"'.$receiver_company.'",
                            "receiver_address_line_1":"Rua da calçada 28A",
                            "receiver_address_line_2":null,
                            "receiverr_address_line_3":null,
                            "receiver_city":"R city",
                            "receiver_state":"R state",
                            "receiver_postcode":"##receiver_postcode##",
                            "receiver_telephone":"+2088676060",
                            "fullpallets":0,
                            "halfpallets":0,
                            "qtrpallets":0,
                            "palletlifts":0,
                            "value":"330",
                            "currency":"PLN",
                            "item_type":"Packets",
                            "notes":"api testing shipment",
                            "description":"Test items", 
                            "parcel":
                                  [{"weight" :'.$this->weight.',
                            "height":'.$this->height.',
                            "width":'.$this->width.',
                            "length":'.$this->length.'
                                  }]
                            }';
         $senderIso = $currHandlingCodeData['sender_iso'];
                
         $senderTags = ["##sender_country_iso##", "##service_code##", "##sender_address_line_1##","##sender_city##","##sender_post_code##"];
         $senderReplacement   = [ $senderIso, $currHandlingCodeData['service_code'], $this->addresses['sender'][$senderIso]['address_line_1'], $this->addresses['sender'][$senderIso]['city'], $this->addresses['sender'][$senderIso]['post_code']];
         $this->postData = str_replace($senderTags, $senderReplacement, $this->postData);
         $receiverPostcode = "12345";
         if(!empty($this->receiver_iso)){
            $receiverIso = $this->receiver_iso; 
         }else{
            $receiverIso = $currHandlingCodeData['receiver_iso'];
              if(isset($this->addresses['receiver'][$receiverIso]['post_code']) && $this->addresses['receiver'][$receiverIso]['post_code']!='')
                $receiverPostcode = $this->addresses['receiver'][$receiverIso]['post_code'];
                
         }
         
         if(!empty($this->receiver_postcode)){
            $receiverPostcode = $this->receiver_postcode; 
         } 
                
                
         $receiverTags = ["##receiver_country_iso##","##receiver_postcode##"  ];
         $receiverReplacement   = [ $receiverIso ,$receiverPostcode];
           $this->postData = str_replace($receiverTags, $receiverReplacement, $this->postData);
         
                
       
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
        
       // $this->postData =   ['hawb[0]'=>1573488779 , 'hawb[1]'=>1573489753, 'hawb[2]'=>1573491671  ]  ;
          //$this->postData =  ['hawb[0]'=>$this->orderReference , 'hawb[1]'=>1573489753, 'hawb[2]'=>1573489733 , 'hawb[3]'=>1573491671  ]  ;    
//      /   $this->postData = json_encode( ['hawb[0]'=>$this->orderReference  ]) ;
       //$this->postData = "{'hawb[0]':1573489753}";
       // $this->postData =  [ 'hawb[0]'=>'1573489753'    ]  ;
        
        $this->postData =  ['hawb[0]'=>$this->orderReference];
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
     
     
  public function getActiveHandlingCodes(){
        return $this->handlingArray;
    }
    
      public function getAddresses(){
         return $this->addresses;
    }
    
    public function setSelectedHandlingCodes($selectedHandling){
        $this->currHandlingServiceCode = trim($selectedHandling);
    }
    
    public function setWeightDimensions($weight,$length,$width,$height){
        $this->weight =   $weight;
        $this->length =   $length;
        $this->width  =   $width;
        $this->height =  $height;
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