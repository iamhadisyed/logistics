<?php

//require_once('email/PHPMailerAutoload.php');
require_once("../includes/settings/config.inc.php");

$consignment = new Consignment(17343736);


$label = new CPostLabel();
echo $result = $label->AddConsignment($consignment);
exit;


$from_name = "mruga";
$from_address = "itsupport@oneworldexpress.com";
$to_name = "mruga";
$to_address = "mruga@oneworldexpress.com, tahir@oneworldexpress.com";
$startTime = "20170118 140000";
$endTime = "20170118 150000";
$subject = "My Test Subject";
$description = "My Awesome Description";
$location = "Joe's House";
sendIcalEvent($from_name, $from_address, $to_name, $to_address, $startTime, $endTime, $subject, $description, $location);

function sendIcalEvent($from_name, $from_address, $to_name, $to_address, $startTime, $endTime, $subject, $description, $location) {
    $domain = 'exchangecore.com';

    //Create Email Headers
    $mime_boundary = "----Meeting Booking----" . MD5(TIME());

    $headers = "From: " . $from_name . " <" . $from_address . ">\n";
    $headers .= "Reply-To: " . $from_name . " <" . $from_address . ">\n";
    $headers .= "MIME-Version: 1.0\n";
    $headers .= "Content-Type: multipart/alternative; boundary=\"$mime_boundary\"\n";
    $headers .= "Content-class: urn:content-classes:calendarmessage\n";

    //Create Email Body (HTML)
    $message = "--$mime_boundary\r\n";
    $message .= "Content-Type: text/html; charset=UTF-8\n";
    $message .= "Content-Transfer-Encoding: 8bit\n\n";
    $message .= "<html>\n";
    $message .= "<body>\n";
    $message .= '<p>Dear ' . $to_name . ',</p>';
    $message .= '<p>' . $description . '</p>';
    $message .= "</body>\n";
    $message .= "</html>\n";
    $message .= "--$mime_boundary\r\n";

    $ical = 'BEGIN:VCALENDAR' . "\r\n" .
            'PRODID:-//Microsoft Corporation//Outlook 10.0 MIMEDIR//EN' . "\r\n" .
            'VERSION:2.0' . "\r\n" .
            'METHOD:REQUEST' . "\r\n" .
            'BEGIN:VTIMEZONE' . "\r\n" .
            'TZID:Eastern Time' . "\r\n" .
            'BEGIN:STANDARD' . "\r\n" .
            'DTSTART:20091101T020000' . "\r\n" .
            'RRULE:FREQ=YEARLY;INTERVAL=1;BYDAY=1SU;BYMONTH=11' . "\r\n" .
            'TZOFFSETFROM:-0000' . "\r\n" .
            'TZOFFSETTO:-0000' . "\r\n" .
            'TZNAME:EST' . "\r\n" .
            'END:STANDARD' . "\r\n" .
            'BEGIN:DAYLIGHT' . "\r\n" .
            'DTSTART:20090301T020000' . "\r\n" .
            'RRULE:FREQ=YEARLY;INTERVAL=1;BYDAY=2SU;BYMONTH=3' . "\r\n" .
            'TZOFFSETFROM:-0500' . "\r\n" .
            'TZOFFSETTO:-0400' . "\r\n" .
            'TZNAME:EDST' . "\r\n" .
            'END:DAYLIGHT' . "\r\n" .
            'END:VTIMEZONE' . "\r\n" .
            'BEGIN:VEVENT' . "\r\n" .
            'ORGANIZER;CN="' . $from_name . '":MAILTO:' . $from_address . "\r\n" .
            'ATTENDEE;CN="' . $to_name . '";ROLE=REQ-PARTICIPANT;RSVP=TRUE:MAILTO:' . $to_address . "\r\n" .
            'LAST-MODIFIED:' . date("Ymd\TGis") . "\r\n" .
            'UID:' . date("Ymd\TGis", strtotime($startTime)) . rand() . "@" . $domain . "\r\n" .
            'DTSTAMP:' . date("Ymd\TGis") . "\r\n" .
            'DTSTART;TZID="Eastern Time":' . date("Ymd\THis", strtotime($startTime)) . "\r\n" .
            'DTEND;TZID="Eastern Time":' . date("Ymd\THis", strtotime($endTime)) . "\r\n" .
            'TRANSP:OPAQUE' . "\r\n" .
            'SEQUENCE:1' . "\r\n" .
            'SUMMARY:' . $subject . "\r\n" .
            'LOCATION:' . $location . "\r\n" .
            'CLASS:PUBLIC' . "\r\n" .
            'PRIORITY:5' . "\r\n" .
            'BEGIN:VALARM' . "\r\n" .
            'TRIGGER:-PT15M' . "\r\n" .
            'ACTION:DISPLAY' . "\r\n" .
            'DESCRIPTION:Reminder' . "\r\n" .
            'END:VALARM' . "\r\n" .
            'END:VEVENT' . "\r\n" .
            'END:VCALENDAR' . "\r\n";
    $message .= 'Content-Type: text/calendar;name="meeting.ics";method=REQUEST' . "\n";
    $message .= "Content-Transfer-Encoding: 8bit\n\n";
    $message .= $ical;

    $mailsent = mail($to_address, $subject, $message, $headers);

    return ($mailsent) ? (true) : (false);
}

exit;
SendDataToCourier::sendCarrierData(array('DR9887413810M'), 1);
exit;





//Create a new PHPMailer instance
$mail = new PHPMailer;
//Set who the message is to be sent from
$mail->setFrom('tahir@oneworldexpress.com', 'First Last');
//Set an alternative reply-to address
$mail->addReplyTo('tahir@oneworldexpress.com', 'First Last');
//Set who the message is to be sent to
$mail->addAddress('tahir@oneworldexpress.com', 'John Doe');
//Set the subject line
$mail->Subject = 'PHPMailer mail() test';
//Read an HTML message body from an external file, convert referenced images to embedded,
//convert HTML into a basic plain-text alternative body
//$mail->msgHTML(file_get_contents('mm294882.c98'), '/var/www/vhosts/oneworldexpress.co.uk/httpdocs/remote/_assets/eurobtoc_booking/czech_files_int/');
//Replace the plain text body with one created manually
$mail->AltBody = 'This is a plain-text message body';
$mail->Body = 'This is a plain-text message body';
//Attach an image file
$mail->addAttachment('/var/www/vhosts/oneworldexpress.co.uk/httpdocs/remote/_assets/eurobtoc_booking/czech_files_int/mm294882.c98');

//send the message, check for errors
if (!$mail->send()) {
    echo "Mailer Error: " . $mail->ErrorInfo;
} else {
    echo "Message sent!";
}
;


die;
/*
  mail_attachment("mm294882.c98", "/var/www/vhosts/oneworldexpress.co.uk/httpdocs/remote/_assets/eurobtoc_booking/czech_files_int/","mruga@oneworldexpress.com", "mruga@oneworldexpress.com","mruga","mruga@oneworldexpress.com","test","test");
  function mail_attachment($filename, $path, $mailto, $from_mail, $from_name, $replyto, $subject, $message)
  {
  $file = $path.$filename;
  $file_size = filesize($file);
  $handle = fopen($file, "r");
  $content = fread($handle, $file_size);
  fclose($handle);
  $content = chunk_split(base64_encode($content));
  $uid = md5(uniqid(time()));
  $header = "From: ".$from_name." <".$from_mail.">\r\n";
  $header .= "Reply-To: ".$replyto."\r\n";
  $header .= "MIME-Version: 1.0\r\n";
  $header .= "Content-Type: multipart/mixed; boundary=\"".$uid."\"\r\n\r\n";
  $header .= "This is a multi-part message in MIME format.\r\n";
  $header .= "--".$uid."\r\n";
  $header .= $message."\r\n\r\n";
  $header .= "--".$uid."\r\n";
  $header .= "Content-Type: application/octet-stream; name=\"".$filename."\"\r\n"; // use different content types here
  $header .= "Content-Transfer-Encoding: base64\r\n";
  $header .= "Content-Disposition: attachment; filename=\"".$filename."\"\r\n\r\n";
  $header .= $content."\r\n\r\n";
  $header .= "--".$uid."--";
  if (mail($mailto, $subject, "test", $header)) {
  echo "mail send ... OK"; // or use booleans here
  } else {
  echo "mail send ... ERROR!";
  }
  }

 */
exit;

function mail_attachment($filename, $path, $mailto, $from_mail, $from_name, $replyto, $subject, $message) {
    $file = $path . $filename;
    $file_size = filesize($file);
    $handle = fopen($file, "r");
    $content = fread($handle, $file_size);
    fclose($handle);
    $content = chunk_split(base64_encode($content));
    $uid = md5(uniqid(time()));
    $header = "From: " . $from_name . " <" . $from_mail . ">\r\n";
    $header .= "Reply-To: " . $replyto . "\r\n";
    $header .= "MIME-Version: 1.0\r\n";
    $header .= "Content-Type: multipart/mixed; boundary=\"" . $uid . "\"\r\n\r\n";
    $header .= "This is a multi-part message in MIME format.\r\n";
    $header .= "--" . $uid . "\r\n";
    $header .= "Content-type:text/plain; charset=iso-8859-1\r\n";
    $header .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
    $header .= $message . "\r\n\r\n";
    $header .= "--" . $uid . "\r\n";
    $header .= "Content-Type: application/octet-stream; name=\"" . $filename . "\"\r\n"; // use different content types here
    $header .= "Content-Transfer-Encoding: base64\r\n";
    $header .= "Content-Disposition: attachment; filename=\"" . $filename . "\"\r\n\r\n";
    $header .= $content . "\r\n\r\n";
    $header .= "--" . $uid . "--";
    if (mail($mailto, $subject, "", $header)) {
        echo "mail send ... OK"; // or use booleans here
    } else {
        echo "mail send ... ERROR!";
    }
}

$service_url = 'http://wsr.brt.it:10041/web/GetIdSpedizioneByIdColloService/GetIdSpedizioneByIdCollo?wsdl';
$client = new SoapClient($service_url, array('trace' => true,
    'exceptions' => true,
        ));


$obj = new stdClass();
$obj->arg0 = new stdClass();
$obj->arg0->CLIENTE_ID = "0058550";
$obj->arg0->COLLO_ID = "6221008";

$result = $client->getidspedizionebyidcollo($obj);
$tracking_result = $result->return;
$success = $tracking_result->ESITO;
if ($success == "0") {
    $SPEDIZIONE_ID = $tracking_result->SPEDIZIONE_ID;
    $SPEDIZIONE_ANNO = $tracking_result->SPEDIZIONE_ANNO;

    $service_url = 'http://wsr.brt.it:10041/web/BRT_TrackingByBRTshipmentIDService/BRT_TrackingByBRTshipmentID?wsdl';
    $client = new SoapClient($service_url, array('trace' => true,
        'exceptions' => true,
    ));


    $obj = new stdClass();
    $obj->arg0 = new stdClass();
    $obj->arg0->LINGUA_ISO639_ALPHA2 = "";
    $obj->arg0->SPEDIZIONE_ANNO = $SPEDIZIONE_ANNO;
    $obj->arg0->SPEDIZIONE_BRT_ID = $SPEDIZIONE_ID;


    $detailresult = $client->brt_trackingbybrtshipmentid($obj);
    $result_success = $detailresult->return->ESITO;
    if ($result_success == "0") {
        $tracking_details = $detailresult->return->LISTA_EVENTI;
    }
    print_r($detailresult);
}
exit;

$xml_data = '<?xml version="1.0" encoding="UTF-8"?>
				<xs:schema xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:xs="http://www.w3.org/2001/XMLSchema" xmlns:wsu="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-utility-1.0.xsd" xmlns:wsp1_2="http://schemas.xmlsoap.org/ws/2004/09/policy" xmlns:wsp="http://www.w3.org/ns/ws-policy" xmlns:wsam="http://www.w3.org/2007/05/addressing/metadata" xmlns:tns="http://getidspedizionebyrma.wsbeans.iseries" xmlns:soap="http://schemas.xmlsoap.org/wsdl/soap/" xmlns="http://schemas.xmlsoap.org/wsdl/" targetNamespace="http://getidspedizionebyrma.wsbeans.iseries" version="1.0">
				 <xs:complexType name="getidspedizionebyrmaInput">
			 	 <xs:sequence>
					<xs:element name="CLIENTE_ID" type="xs:integer" minOccurs="1"/>
				 	<xs:element name="RIFERIMENTO_MITTENTE_ALFABETICO" type="xs:string" default="RE188059868"  minOccurs="1"/>
				  </xs:sequence>
				 </xs:complexType>
			    </xs:schema>';
print_r($xml_data);

//setting the curl parameters.
$ch = curl_init($service_url);
curl_setopt($ch, CURLOPT_MUTE, 1);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: text/xml'));
curl_setopt($ch, CURLOPT_POSTFIELDS, "$xml_data");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
$output = curl_exec($ch);
curl_close($ch);
print_r($output);
die;

$consignment = new Consignment('14041032');
$label = new LapostPriorityLabel();
echo $results = $label->AddConsignment($consignment);
die;


$label = new UPULabel();
echo $results = $label->AddConsignment($consignment);
die;

echo $pdf_file_name = BagLabel::buildPDFDocuments('28');
exit;


echo $pdf_file_name = ManifestSummaryReportTest::SavePDFFile($consignment, $manifestid);
exit;
$tracking_array = array('JD0002257356938212', 'JD0002257356936622', '09445935022776');
SendDataToCourier::sendCarrierData($tracking_array, 1);
exit;

$consignment = new Consignment('14041032');
$label = new CPostLabel();
echo $results = $label->AddConsignment($consignment);
die;



$result = ConsignmentFilter::getAgentId('ECX', '0', 'C');
print_r($result);
exit;
$consignment = new Consignment('13587801');
$label = new GLSStandardNLTest();
echo $results = $label->AddConsignment($consignment);
die;


$consignment = new Consignment('13547426');
$label = new BrtItalyLabel();
echo $results = $label->AddConsignment($consignment);
die;

$userAccount = new CustomerAccount();
$result = $user->getUserAccountList(array('71'), '', 'PROFM');
echo '<select id="account" name="account"  class="form-control"  rel="tooltip" title="User Account">';
echo '<option value="">Select Account</option>';
echo $result;
echo '</select>';
//print_r($result);
exit;

$consignment = new Consignment('11319899');
$label = new DPDPoland();
echo $results = $label->AddConsignment($consignment);
die;


$manifestFilter = new ManifestDataFilter();
$manifestFilter->addIdFilterIn("'32398','32399'");
$list = $manifestFilter->getList();
SendCollectionRequest('8963580', $list);
exit;

function SendCollectionRequest($pickupId, $manifestList) {

    $service_url = 'https://api-ops-test.azurewebsites.net/api/hub/data/pickup/8963587';

    $username = "test";
    $password = "test";
    $headers = array();
    $headers[] = 'Content-Type: application/json';

    $curl = curl_init($service_url);
    $curl_post_data = $json;
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    // don't knpow how to use get - let's hope it's the default :)
    // curl_setopt($curl, CURLOPT_POST, true);
    //curl_setopt($curl, CURLOPT_POSTFIELDS,  json_encode($arrayCollection));
    curl_setopt($curl, CURLOPT_USERPWD, "$username:$password");
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

    $curl_response = curl_exec($curl);

    echo "API RESULT\n";
    echo "\n";
    echo curl_getinfo($curl, CURLINFO_HTTP_CODE);
    echo "\n";
    echo ($curl_response);
    curl_close($curl);
}

$id_array = array('11927296',
    '11927263'
);
$consignment = new ConsignmentFilter();
$consignment->addIdArrayFilter($id_array);
$list = $consignment->getList();
$pdf_file_name = ManifestSummaryReport::SavePDFFile($list, '29794');
echo $pdf_file_name;
die;




$pickNoteReport = new PickNoteReport();
echo $pickupFileName = $pickNoteReport->SavePDFFile('8963563', array('32192', '32191'), "Pickup");
exit;

$langFilter = new LanguageKeysFilter();
$langFilter->addLanguageFilter("de-DE");
$list = $langFilter->getColumnList("keyword, caption");
foreach ($list as $l) {
    echo utf8_encode($l->getCaption()) . "<br/>";
}


$consignment = new Consignment('13108311');
$label = new TURPCKExpress();
echo $results = $label->AddConsignment($consignment);
die;



$id_array = array('12884285',
    '12884284',
    '12884008',
    '12884007',
    '12883969',
    '12883787',
    '12883786'
);
$consignment = new ConsignmentFilter();
$consignment->addIdArrayFilter($id_array);
$list = $consignment->getColumnList('awb,contact,company,hawb,country,weight', 10);

$manifest = new GenerateBriefManifestReport();
echo $manifest->AddHTML($list, "22");
die;

$label = new CTTExpressManifest();
echo $results = $label->AddConsignment($list);
die;

$consignment = new Consignment('10537306');
$label = new PilotSpain();
echo $results = $label->AddConsignment($consignment);
die;


echo $manifestid = TrackingData::SendEmail(array('3SRM4000001'), Sessionmanager::getUser());
exit;

$label = new KBCroatiaLabel();
echo $results = $label->AddConsignment($consignment);
die;



$label = new BringLabel();
echo $results = $label->AddConsignment($consignment);
die;




$arr_update_scan_numbers = array("JD0002257356254508");

SendDataToCourier::sendCarrierData($arr_update_scan_numbers, 1);
exit;




$label = new SecuredMailUntracked();
echo $results = $label->AddConsignment($consignment);
die;






print_r($results);
?>