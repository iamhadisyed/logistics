<?php
require_once("../includes/settings/config.inc.php");
// Test Server
define("SERVER_URL", "https://asendia-delta.mpm.metapack.com/BlackBox/BlackBox.svc?wsdl");
define("USER_ID", "oneworld.api");
define("PASSWORD", "lO@lmkhs3686xXk#p@w34");
$consignment = new Consignment('15855388');
$client = new nusoap_client('https://asendia-delta.mpm.metapack.com/BlackBox/BlackBox.svc');
$err = $client->getError();
if ($err) {
    echo '<h2>Constructor error</h2><pre>' . $err . '</pre>';
} else 
{
 $client->soap_defencoding = 'utf-8';
    $client->useHTTPPersistentConnection(); // Uses http 1.1 instead of 1.0
    $soapaction = "http://xlogics.eu/blackbox/BlackBoxContract/PrintParcel";
			
$request_body = '<s:Envelope xmlns:s="http://schemas.xmlsoap.org/soap/envelope/">
			<s:Header>
				<wsse:Security xmlns:wsse="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd">
					<wsse:UsernameToken>
						<wsse:Username>'.USER_ID.'</wsse:Username>
						<wsse:Password Type="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-username-token-profile-1.0#PasswordText">'.PASSWORD.'</wsse:Password>
					</wsse:UsernameToken>
				</wsse:Security>
				<h:Authentication xmlns:i="http://www.w3.org/2001/XMLSchema-instance" xmlns:h=" http://xlogics.eu/blackbox">
					<h:Culture>EN</h:Culture>
					<h:UnitName>One World Express Inc. Ltd</h:UnitName>
				</h:Authentication>
			</s:Header>
			<s:Body>
			<PrintParcelRequest xmlns="http://xlogics.eu/blackbox"> 
				<InputParameters xmlns:i="http://www.w3.org/2001/XMLSchema-instance"> 
					<ShippingParameter> 
						<Name>Shipment.RefNo</Name> 
						<Value>'.$consignment->getHawb().'</Value> 
					</ShippingParameter> 
					<ShippingParameter> 
						<Name>AA.Service</Name> 
						<Value>COLA</Value> 
					</ShippingParameter> 
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
						<Value></Value> 
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
						<Value>'.$consignment->getCompany().'</Value> 
					</ShippingParameter> 
					<ShippingParameter> 
						<Name>Receiver.Name1</Name> 
						<Value>'.$consignment->getContact().'</Value> 
					</ShippingParameter> 
					<ShippingParameter>
						<Name>Receiver.Name2</Name> 
						<Value>'.$consignment->getContact().'</Value> 
					</ShippingParameter> 
					<ShippingParameter> 
						<Name>Receiver.HouseNo</Name> 
						<Value>1</Value> 
					</ShippingParameter> 
					<ShippingParameter> 
						<Name>Receiver.Street</Name> 
						<Value>'.$consignment->getAddressLine1().'</Value> 
					</ShippingParameter> 
					<ShippingParameter> 
						<Name>Receiver.AddressDetails</Name> 
						<Value>'. $consignment->getAddressLine2() . " " . $consignment->getAddressLine3() .'</Value> 
					</ShippingParameter> 
					<ShippingParameter> 
						<Name>Receiver.City</Name> 
						<Value>'.$consignment->getCity().'</Value> 
					</ShippingParameter> 
					<ShippingParameter> 
						<Name>Receiver.Province</Name> 
						<Value></Value> 
					</ShippingParameter> 
					<ShippingParameter> 
						<Name>Receiver.Postcode</Name> 
						<Value>'.$consignment->getPostcode().'</Value> 
					</ShippingParameter> 
					<ShippingParameter> 
						<Name>Receiver.Country</Name> 
						<Value>'.$consignment->getCountryIsoCode().'</Value> 
					</ShippingParameter> 
					<ShippingParameter> 
						<Name>Receiver.Telephone</Name> 
						<Value></Value> 
					</ShippingParameter> 
					<ShippingParameter>
						 <Name>Receiver.Mobile</Name> 
						 <Value></Value> 
					</ShippingParameter> 
					<ShippingParameter> 
						<Name>Receiver.Email</Name> 
						<Value></Value> 
					</ShippingParameter> 
					<ShippingParameter> 
						<Name>Parcel.Weight</Name> 
						<Value>'.$consignment->getWeight().'</Value> 
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
					</ShippingParameter> 
					<ShippingParameter>
						<Name>ProductParcel</Name> 
						<Value> 
						</Value> 
					</ShippingParameter> 
				</InputParameters> 
			</PrintParcelRequest>
		</s:Body>
	</s:Envelope>';


    $result = $client->send($request_body, $soapaction); 
    
  print_r($result);
     if($result["ExitStatus"]["Status"] == "Success")
	 {
		 $output = $result["OutputParameters"]["ShippingParameter"];

				 echo $tracking_number = $output[0]["Value"];
				 $unique_refernce = $output[1]["Value"];
				 $pdfContent =  base64_decode($output[2]["Value"]);
				$fileName  = '../_assets/pdf/'.date('Y_m_d').'/'. "test123.pdf"; 
				$fp = fopen($fileName, 'wb+');
				fwrite( $fp, $pdfContent );
				fclose( $fp );
				
				
					  echo "SUCCESS||". SETTING_MAIN_URL . "_assets/pdf/".date('Y_m_d')."/test123.pdf||" .  $tracking_number ;	  
				
		
	 }
	 else
	 {
		 echo $result["ExitStatus"]["StatusDetails"]["StatusDetail"]["Message"];
	 }
    }
	exit;

$headers = array(
    "Content-type: text/xml",
    "Content-length: " . strlen($xml_data),
    "Connection: close",
);

$ch = curl_init(); 
curl_setopt($ch, CURLOPT_URL,SERVER_URL);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $xml_data);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

$data = curl_exec($ch); 
//echo $data;
if(curl_errno($ch))
    print curl_error($ch);
else
    curl_close($ch);
	
	print_r($data);
exit;


$stream_options = array(
    'http' => array(
       'method'  => 'POST',
       'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
       'content' => ($xml_data),
    ),
);

$context  = stream_context_create($stream_options);
$response = file_get_contents(SERVER_URL, null, $context);

$response_xml = simplexml_load_string($response);
$response = $response_xml->response;
print_r($response);
exit;
$status = $response->status;
$status_code = '';

foreach($status->attributes() as $key => $value){
   $status_code = $value;
}
if($status_code == 'OK'){
    $job = $response->job;
    foreach($job->attributes() as $key => $value){
        echo "<b>".$key.":</b>  ".$value."<br />";
    }
    $consignment = $job->consignment;
    foreach($consignment->attributes() as $key => $value){
        echo "<b>Job Consignment ".$key.":</b>  ".$value."<br />";
    }
    echo "<b>Job Reference:</b> ".$job->reference."<br />";
    $deadlineDateTime = $job->deadlineDateTime;
    foreach($deadlineDateTime->attributes() as $key => $value){
        echo "<b>Job Deadline ".$key.":</b>  ".$value."<br />";
    }
    $labelData = $job->labelData;
    echo "<b>Label URL:</b> ".$labelData->url;
}else{
    echo "<b>Status Code:</b> ".$status_code;
}
?>