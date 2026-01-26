<?php
include_classes([
    'include_list',
    ], 'reamus');
include_classes([
    'carrierdatafilelog.class',
    'carrierdatafilelogfilter.class' 
    ]);
class Yakit implements CarrierService {

    private $pdf;
    private $user = null;
    private $serviceValues = null;
    private $agentValues = null;
    private $constants = null;
    private $countryName = null;
    private $consignment = null;
    private $record_idx = 0;
    private $link_file = null;
    private $routing_code = null;
    private $isRemoteArea = 0;

    public function __construct() {
        
    }

    public function validation(Consignment $consignment, Services $service, Country $country) {

    
    }

    public function remoteareas($consignment, $carrierObject, $sender) {

    }

    public function isRemoteArea() {
        
    }

    public function label($consignment, $labelType = 'pdf', $size = '100x150') {
    }

    public function tracking($trackingNumber, $trackBy = 'parcel', $EDI = false) 
    {  
    }

    public function sendData($tracking_numbers = array()) {
    }

    public function manifest($consignment) {
        
    }

    public function preAdvice($consignment) {
        
    }

    public function recycledShipment($consignment) {
       
    }

   Public static function hsCodeDutyCalculator($fromCountryIso,$toCountryIso, $currency,$categories, $description, $quantity, $itemValue,  $weightUnit, $weight, $dimsUnit, $length, $width, $height){

		$output["status"] = true;
		$data = '{
                    "currencyUnit":' . $currency . ',
                    "countryList":["'.$fromCountryIso.'"],
                    "shipmentWeight":' . $weight . ',
                    "shipmentWeightUnit":"'.$weightUnit.'",
                    "length":' . $length . ',
                    "width":' . $width . ',
                    "height":' . $height . ',
                    "dimUnit":"'.$dimsUnit.'",
                    "items":[{
                                    "displayName":"' . (strtolower($categories) == 'others' ?  $description  : $categories ) . '",
                                    "description":"' . $description . '",
                                    "countryOfOrigin":"'.$toCountryIso.'",
                                    "itemWeight":' . $weight . ',
                                    "itemWeightUnit":"'.$weightUnit.'",
                                    "value": ' . $itemValue . ',
                                    "quantity":'.$quantity.'
                            }]
                    }';

		
        $data_string = $data;
        $ch = curl_init('https://shipping.yakit.com/api/yrate/shipmentQuote');

        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Authorization: Basic c2hhYmJpckBvbmV3b3JsZGV4cHJlc3MuY29tOjQ0NmM0ZWYwLTAwOTctNDA3MC1hNjhjLThiNmMyNWE4YzNhMA==',
            'Cache-Control: no-cache',
            'Version: 2',
            'Content-Type: application/json',
            'Content-Length: ' . strlen($data_string))
        );
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
		$results = curl_exec($ch);
		
        curl_close($ch);

        if (@$err) {
            $output["status"] = false;
            $output["message"] = $err;
            return $output;
        } else {
            $response = json_decode($results);
            if (!empty($response)) {
                $shipment_id = $response->ttdList[0]->shipment;
            }
            if(trim($shipment_id) == '' || (int)$shipment_id <=0){
                $output["status"] = false;
                $output["message"] = "Unable to get landed cost.";
                return $output;
            }
        }

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://shipping.yakit.com/api/yrate/getClassifications?shipmentId=" . $shipment_id,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                "Authorization: Basic c2hhYmJpckBvbmV3b3JsZGV4cHJlc3MuY29tOjQ0NmM0ZWYwLTAwOTctNDA3MC1hNjhjLThiNmMyNWE4YzNhMA==",
                "Cache-Control: no-cache",
                "Version: 2"),
        ));

        $resultsHscode 	= curl_exec($curl);
        $errHscode 		= curl_error($curl);
        curl_close($curl);
         if (@$err) {
            $output["status"] = false;
			$output["message"] = $err;
            return $output;
        } else {
            $responseHscode = json_decode($resultsHscode);
        }
		
		$output["shipment"] = $response;
		$output["hscode"] = $responseHscode;
		
		return $output;
		
   }
	

}
