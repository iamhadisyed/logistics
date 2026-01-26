

<?php

// get settings
require_once("../includes/settings/config.inc.php");
/*
  apiId:
  9b58a9ef14be3750220aa30da9dd22f6

  secret:
  DxsSpEgTQB91pB8fcbb13ef3lstiljb06alxlu02

 */
$consignment = new Consignment('11559394');
$label = new SpringUntracked();
$results = $label->AddConsignment($consignment);



/*
  $consignment = new Consignment('11440612');
  $label = new SecuredMailUntracked();
  $results = $label->AddConsignment($consignment);

 */

//	$label = new SolvakiaPostLabel();
//  $results = $label->AddConsignment($consignment);

echo $results;
die;

$ch = curl_init();

$data = array(
    'appId' => '9b58a9ef14be3750220aa30da9dd22f6',
    'secret' => 'DxsSpEgTQB91pB8fcbb13ef3lstiljb06alxlu02'
);

curl_setopt($ch, CURLOPT_URL, 'https://system-dev.fhb.sk/api/joy/login');
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
curl_setopt($ch, CURLOPT_HEADER, FALSE);
curl_setopt($ch, CURLOPT_POST, TRUE);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$json = json_decode($response);


if ($httpCode != 200) {
    die('Error: ' . $json->message);
}

//$hawb = $consignment->gethawb();

$data = array(
    'id' => 1231233331,
    'name' => 'Libusa Kosiková',
    'street' => 'SNP 15',
    'city' => 'Nizna Polianka',
    'psc' => '86100',
    'country' => 'sk',
    'email' => 'test@example.com',
    'phone' => '00421554994669',
    'variableSymbol' => 1234,
    'cod' => 10,
    'note' => 'Test api'
);


curl_setopt($ch, CURLOPT_URL, 'https://system-dev.fhb.sk/api/joy/packages');
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
curl_setopt($ch, CURLOPT_HEADER, FALSE);
curl_setopt($ch, CURLOPT_POST, TRUE);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    "Content-Type: application/json",
    "X-Authentication-Simple: " . base64_encode($json->token))
);




$response = curl_exec($ch);
echo $response;
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$json = json_decode($response);

if ($httpCode != 201) {
    die('Error: ' . $json1->message);
}
//print_r($response);
print_r($json);
$urllink = $json->_links->self->href;
//// die('Response: ' . $json1->_links->self->href);
//$urllink = str_replace('id','ids',$urllink);



$ch = curl_init();

$data = array(
    'appId' => '9b58a9ef14be3750220aa30da9dd22f6',
    'secret' => 'DxsSpEgTQB91pB8fcbb13ef3lstiljb06alxlu02'
);

curl_setopt($ch, CURLOPT_URL, 'https://system-dev.fhb.sk/api/joy/login');
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
curl_setopt($ch, CURLOPT_HEADER, FALSE);
curl_setopt($ch, CURLOPT_POST, TRUE);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));

$response = curl_exec($ch);
echo $response;
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$json = json_decode($response);


if ($httpCode != 200) {
    die('Error: ' . $json->message);
}


$ch = curl_init();
//curl_setopt($ch, CURLOPT_URL, "https://system-dev.fhb.sk" .$urllink );
curl_setopt($ch, CURLOPT_URL, "https://system-dev.fhb.sk/api/joy/labels?ids=1231233331");  // /api/joy/packages?id=344343546344634 
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
curl_setopt($ch, CURLOPT_HEADER, FALSE);

curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    "Content-Type: application/json",
    "X-Authentication-Simple: " . base64_encode($json->token)
));


$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$json = json_decode($response);

if ($httpCode != 200) {
    die('Error: ' . $json->message);
}


$results = base64_decode($json->labels);
//echo $results;



$fp = fopen("shakir1.pdf", 'wb+');

fwrite($fp, $results);
fclose($fp);

die;


/*

  /api/joy/packages?id=344343546344634
  ///api/joy/packages?id=137384522234
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, "https://system-dev.fhb.sk" . $urllink );
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
  curl_setopt($ch, CURLOPT_HEADER, FALSE);

  curl_setopt($ch, CURLOPT_HTTPHEADER, array(
  "Content-Type: application/json",
  "X-Authentication-Simple: " . base64_encode($json->token)
  ));




  $response = curl_exec($ch);
  $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
  $json = json_decode($response);
  echo $response ;
  if ($httpCode != 200) {
  die('Error: ' . $json->message);
  }
  print_r($json);

  //header('Content-type: application/pdf');
  //die(base64_decode($json->labels));







  die;
  $ch = curl_init();

  $data = array(
  'appId' => '9b58a9ef14be3750220aa30da9dd22f6',
  'secret' => 'DxsSpEgTQB91pB8fcbb13ef3lstiljb06alxlu02');
  $ch = curl_init();

  curl_setopt($ch, CURLOPT_URL, 'https://system-dev.fhb.sk/api/joy/login');

  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE); curl_setopt($ch, CURLOPT_HEADER, FALSE);

  curl_setopt($ch, CURLOPT_POST, TRUE);

  curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

  curl_setopt($ch, CURLOPT_HTTPHEADER, 'Content-Type: application/json');

  $response = curl_exec($ch);

  $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

  $json = json_decode($response);

  if ($httpCode != 200) { die('Error: ' . $json->message);

  } //die($json->token);

  $data = array(
  'id' => 1234,
  'name' => 'Libusa Kosiková',
  'street' => 'SNP 15',
  'city' => 'Nizna Polianka',
  'psc' => '86100',
  'country' => 'sk',
  'email' => 'test@example.com',
  'phone' => '00421554994669',
  'variableSymbol' => 1234,
  'cod' => 10,
  'note' => 'Test api',
  );

  curl_setopt($ch, CURLOPT_URL, 'https://system-dev.fhb.sk/api/joy/packages');
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
  curl_setopt($ch, CURLOPT_HEADER, FALSE);
  curl_setopt($ch, CURLOPT_POST, TRUE);

  curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
  curl_setopt($ch, CURLOPT_HTTPHEADER,
  "Content-Type: application/json",
  "X-Authentication-Simple: " . base64_encode($json->token)
  );


  $response = curl_exec($ch);
  print_r( $response);
  $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
  $json = json_decode($response);

  if ($httpCode != 201) {
  die('Error: ' . $json->message);
  }

  print_r($json);

  die;



  echo "sdsfsdf";
  //header('Content-type: application/pdf');
  //die(base64_decode($json->labels));
  //die;


  /*
  $consignment = new Consignment('10550841');
  $label = new PacketPort();
  echo $results = $label->AddConsignment($consignment);
  die;
 */


/* require_once("dhl-germany/GeschaeftskundenversandClient.php");
  require_once 'dhl-germany/Geschaeftskundenversand/GeschaeftskundenversandRequestBuilder.php';
  function IncludeAllFile()
  {
  foreach (glob("dhl-germany/Geschaeftskundenversand/GeschaeftskundenversandWS/*.php") as $filename)
  {
  @include_once $filename;
  }
  }
  IncludeAllFile();
  $consignment = new ConsignmentFilter();
  $consignment->addIdFilter('3691552');
  $list = $consignment->getList();
  $gkvClient = new GlOrderPdfDHLGermany();
  echo $label = $gkvClient->main($list[0], "delete");
  die;
 */
//echo "nmrgu7a";
//
//$orderid = $_GET['hawb'];

$consignment = new Consignment('10167991');




//$pdf = new PdfBase(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', true);
//$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
//$pdf->SetX(1.0);				
//$label	 = new GlOrderPdfCzechPostRotate();
//$results = $label->AddConsignment($consignment);
//brazil Correos POST Integration
//$label = new GlOrderPdfBrazilPost();
//echo $results = $label->AddConsignment($consignment);
//TURBUS POST INTEGRATION


//$label	 = new TurBusPost();
//echo $results = $label->AddConsignment($consignment);
//UKMAIL
//$label	 = new GlOrderPdfUK();
//echo $results = $label->getConsignmentLabel($consignment);
//INPOST INTEGRATION
//$label	 = new GlOrderPdfInPost();
//echo $results = $label->AddConsignment($consignment);
//PONY INTEGRATION
//$label = new GlOrderPdfPony();
//echo $results = $label->AddConsignment($consignment);
//
/* $optimus = new OptimusSortersFile();
  if($optimus->addConsignment())
  {
  $optimus->sendBookings();
  echo "mruga";
  } */

// 	$pdf->Output($results , 'I');
//MEXICO POST
//$label = new EstafetaPost();
//echo $results = $label->AddConsignment($consignment);
//DX INTEGRATION
//$label = new glorderpdfdx();
//echo $results = $label->AddConsignment($consignment);
//DHL GERMANY INTEGRATION
//$label = new GlOrderPdfDhlGermany();
//echo $results = $label->AddConsignment($consignment);
//Dlink Sweden DLinkSweden
//$label = new DLinkSweden();
//echo $results = $label->AddConsignment($consignment);
//MALAYSIA POST
//$label = new PriorityPostNl();
//echo $results = $label->AddConsignment($consignment);
//Cacesa Express
//$label = new CacesaExpress();
//echo $results = $label->AddConsignment($consignment);
//ctt express
//$label = new CttExpresso();
//echo $results = $label->AddConsignment($consignment);
//asendia germnay
$label = new Laposte();
echo $results = $label->AddConsignment($consignment);



//WSP Chile
//$label = new WSPStandard();
//echo $results = $label->AddConsignment($consignment);
//intime ukraine
//$label = new  Intime();
//echo $results = $label->AddConsignment($consignment);
// GLS Netherland
//$label = new GLSStandardNL();
//echo $results = $label->AddConsignment($consignment);
//TMS SMART POST
//$label = new TmsSmartPost();
//echo $results = $label->AddConsignment($consignment);
//ECOM SMALL PACKET
//$label = new EcomSmallPacketNL();
//echo $results = $label->AddConsignment($consignment);
//ECOM PACKET AND PARCEL
//$label = new EcomSmallPacketParcelNL();
//echo $results = $label->AddConsignment($consignment);
?>