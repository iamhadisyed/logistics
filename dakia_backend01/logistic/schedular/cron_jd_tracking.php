<?php

require_once(__DIR__ . "/../includes/settings/config.inc.php");

include_classes([
    'consignment.class',
    'consignmentfilter.class',
    'tracking_data.class',
    'trackingdatatfilter.class',
    'useraccount.class',
    'useraccountfilter.class',
    'user.class',
    'userfilter.class'
]);

$dbConnection = DbAccess3::connect(SETTING_DB_SERVER, SETTING_DB_USER, SETTING_DB_PASSWORD, SETTING_DB_DATABASE);

$startDate = date("Y-m-d H:i:s", strtotime('-1 hour'));
$endDate = date("Y-m-d H:i:s");

$query = "SELECT t.tracking_number, t.status_code_id, t.carrier_desc, t.date_created, t.track_point, c.hawb, con.iso, c.city
                      from 
                      consignment c 
                      INNER JOIN user u ON c.user_id = u.id
                      INNER JOIN tracking_data t ON t.tracking_number = c.awb
                      INNER JOIN country con ON con.id = c.country_id
                      where 
                      t.tracking_number = 'LM239357831SE'
                      #u.user_account_id = '2294' AND 
                      #t.date_added >= '" . $startDate . "' AND t.date_added <= '" . $endDate . "' "
        . "           and c.awb != '' order by t.id desc";

//echo $query; // die;
$rs = DbAccess3::runQuery($query);

if (mysqli_num_rows($rs) > 0) {
    while ($row = mysqli_fetch_array($rs)) {
       $trackingArray = array();
       $tracesArray = array();
       $trackingDescription = $row["status_code_id"];
       if($trackingDescription == ""){
           $trackingDescription = "Others";
       }
       $trackingArray["list"][] = array(
           "logisticsProviderCode" => "OWE",
           "waybillCode" => $row["tracking_number"],
           "orderId" => $row["hawb"],
           "traces" => array(array(
               "operateTime"=>$row["date_created"],
               "desc"=> $trackingDescription,
               "originalScanType" => $row["status_code_id"],
               "country" => $row["iso"],
               "city" => $row["city"],
               "timeZone" => "Europe/London"
           ))
       );
       curlRequest($trackingArray);
    }
    
}
mysqli_close($dbConnection);

function curlRequest($request){
    echo "<pre>";
    $requestParam = [];
    $jsonRequest = json_encode($request);
    print_r($jsonRequest);
    $requestParam[] = $urlencodedRequest = rawurlencode($jsonRequest);
    $chinadate = new DateTime("now", new DateTimeZone('Asia/Shanghai') );
    $currentTime = $chinadate->format("Y-m-d H:i:s");
    $apiKey = 'HDZDbvg3aQ8tpHdK';
    $encryRequest = $jsonRequest.$currentTime.$apiKey;
    $encryptData = urlencode(base64_encode(md5($encryRequest)));

      
    $requestasdf = 'timestamp='.$currentTime.'&encrypt_data='.$encryptData.'&logistics_provider_code=OWE&logistics_info='.$urlencodedRequest;
    $curl = curl_init();
    curl_setopt_array($curl, array(
      CURLOPT_URL => 'https://trace-glsc.jd.com/trace/push',//'https://trace-glsc.jd.com/trace/push',
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'POST',
      CURLOPT_POSTFIELDS => $requestasdf,
      CURLOPT_HTTPHEADER => array(
        'Content-Type: application/x-www-form-urlencoded'
      ),
    ));

    $response = curl_exec($curl);
    $responseArray = json_decode($response);
    if($responseArray->errorDesc !== ""){
        mail("mruga@oneworldexpress.com", "JD Tracking PUSH Fail", print_r($responseArray, true));
    }
    curl_close($curl);
    echo $response;

}

function mappTracking($statuscode){
    $trackingArray = array('144'=>	'Receive Shipment Data',
                            '141'=>	'Check Dimension at HUB',
                            '126'=>	'Dispatch from HUB',
                            '134'=>	'If multi piece Shipment then only half collected by carrier',
                            '114'=>	'Collected By carrier',
                            '140'=>	'Collected By carrier',
                            '133'=>	'Collected By carrier',
                            '148'=>	'Carrier Received Shipment',
                            '146'=>	'Arrived At carrier HUB',
                            '145'=>	'Departed From Carrier',
                            '149'=>	'If International then Arrived at Destination Country',
                            '128'=>	'Held by Custom',
                            '153'=>	'Seize by Custom',
                            '117'=>	'Custom Clearance',
                            '156'=>	'Forwarded to Connecting Carrier or Flight',
                            '111'=>	'Out for delivery',
                            '123'=>	'Tried to delivered but could not',
                            '154'=>	'No one at delivery place so left card',
                            '147'=>	'If multi piece then deliver one of the piece',
                            '121'=>	'Delivered',
                            '138'=>	'Return',
                            '152'=>	'Destroy shipment',
                            '155'=>	'Others',
                            '112'=>	'Address Problem',
                            '115'=>	'Problem',
                            '116'=>	'Consignee Unavailable',
                            '118'=>	'Damaged Parcel',
                            '119'=>	'Damaged',
                            '120'=>	'Delayed',
                            '127'=>	'Failure',
                            '130'=>	'Label Problem',
                            '124'=>	'Pending',
                            '125'=>	'Undelivered',
                            '131'=>	'Not Picket Up',
                            '136'=>	'Hold',
                            '157'=>	'Lost',
                            '150'=>	'Misroute'
                            );
    return $trackingArray[$statuscode];
}
?>
