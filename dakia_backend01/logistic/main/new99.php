<?php

$client = new SoapClient(null, array(
    'location' => "http://oneworldexpress.co.uk/remote/main/test_smartsystemintegration.php?wsdl",
    'uri' => "http://oneworldexpress.co.uk/remote/main/index.php"));


//for($i=0; $i<30; $i++)
{
    $consignmentinformation = '||Loo Jileen||||St Martins Court 10 Paternoste||r Row||||London||GB||EC4M7HP||44 20 7182 3933||1||0.20||Items||0.00||EUR||Tikiting SL||TEST_MG006||||2S||||2507143e0f5b2c2b73f42045be89fc69||9fec40592924914eba24be5dc7458912||1||||||||||NONDOC||||||jileen.loo@cbrehotels.com||||||||||';
//$consignmentinformation = '||Philip Perry||Philip Perry||Haven Cottage Victoria Road||||||Yarmouth||GB||PO41 0QW||01983 760616||1||0.438||battery||7||USD||wanghaitao||||TP2||ITTEAM||developer||owe23||0||||||||||[OW00116] ||0.438%%1.0%%1.0%%1.0||||||||||||||';
//$consignmentinformation = '||Carlos Neto||Carlos Neto||Américo Rodrigues Barbosa n18 4 drt||||¬BRAGA||Braga||PT||4710-007||0 351966783457||1||0.750||Action Cameras  Sport DV||30||USD||wanghaitao||||ECX||SED(UK)||SED(UK)||!fs2)X1B||0||||||||||[OW00122] ||0.750%%12.0%%25.0%%10.0||||cs@oneworldexpress.com||||||||||';
//$consignmentinformation = "||Private Shipper||KATE HOWSAM ||4 GRANGE PARK||test||test||Karachi||PK||74000||-||1||0.257||Thank you.||1||USD||IMS||-||WS COURIER||ITSSHIP||ITSSHIP||ITSSHIP||0||0||0||0||NONDOC||||0.257%%1%%1%%1||||||||||||||";
//$consignmentinformation = "||no||Iris Mori||Mori Immobilien Martin-Luther-Ring 3 Oppenheim||||||Martin-Luther-Ring||GB||ub33nb||06133 579567||2||5.500||car dvd||80||USD||wanghaitao||||3HPA||ITTEAM||developer||%$30bhFE||0||||||||||[OW00816] ||5.500%%50.0%%50.0%%50.0&&5.500%%50.0%%50.0%%50.0||||||||||||||";
    /* $consignmentinformation = "||Jacqueline Panter||Jacqueline Panter||92 Lovell Road||||¬Cambs||CAMBRIDGE Cambs||GB||CB4 2QP||07961 870471||1||2.000||CAT TREE||5||USD||wanghaitao||||3HPA||OW00587||OW00587||OW00587||0||||||||||[OW00587] ||2.000%%1.0%%1.0%%0.0||||b605hjv06d3x4vh@marketplace.amazon.co.uk||||||||||";
      $consignmentinformation = "||Peggy Martin||Peggy Martin||4 Monnina Park||||¬NEWRY DOWN||Bessbrook NEWRY DOWN||GB||BT358PP||(085) 148-3682||1||6||Lamp||32||USD||wanghaitao||||12||ITTEAM||developer||developer||0||||||||||[OW00122] ||5.600%%22.0000%%100.0%%22.0000||||cs@oneworldexpress.com||||||||||";
      /*$consignmentinformation = "||Antonio Costa||Antonio Costa||Rua da Mãe de Deus 10||||¬REGIAO AUTONOMA DOS ACORES||Vila do Porto||PT||9580||911168510||1||1.000||Mobile phones||30||USD||wanghaitao||||ECX||SED(UK)||SED(UK)||SED(UK)||0||||||||||[OW00122] ||1.000%%12.0000%%18.0000%%12.0||||cs@oneworldexpress.com||||||||||"; */
//$results =  $client->__soapCall('', array('consignmentinformation' => $consignmentinformation));
    $results = $client->__soapCall('getLabels', array('consignmentinformation' => $consignmentinformation));
//$results =  $client->__soapCall('removeInvalidLabel', array('consignmentinformation' => "sue123||ITTEAM||developer||developer"));
//$results =  $client->__soapCall('importConsignmentData', array('consignmentinformation' => $consignmentinformation));
    print_r($results);
}
die;


/*
  $USERNAME	= array('developer');
  $PASSWORD	= array('owe23');
  /*
  $consignmentinformation = array (
  "0"  => $USERNAME,
  "1"  => $PASSWORD,
  "2"  => array (
  "0" => "||Apple||Robert||2 Roberts Rd ||Hillingdon||Middlesex||New York||AF||||02086058999||1||1.00||Books||1.00||GBP||John||SHOP22233||WPX||ITTEAM||developer||owe23||0||||||||||||||||||||||||||",
  "1" => "||Apple||Robert||2 Roberts Rd ||Hillingdon||Middlesex||New York||AF||||02086058999||1||1.00||Books||1.00||GBP||John||SHOP22233||WPX||ITTEAM||developer||owe23||0||||||||||||||||||||||||||",
  "2" => "||MISGUIDED LTD||NA NA||UNIT 8 CENTENARY PARK||CORONET WAY SALFORD||-||MANCHESTER CITY||GB||UB33NB||0000000000000000||1||0.25||CLOTHING||1.00||EUR||John||SHOP22233||RM2||ITTEAM||developer||owe23||0||||||||||||||||||||||||"
  ));



  $USERNAME	= array('developer');
  $PASSWORD	= array('owe23');

  $consignmentinformation = array (
  "0"  => $USERNAME,
  "1"  => $PASSWORD,
  "2"  => array (
  "0" => "||shipName||shipName||Anthony Benoit||490 E Main Street||Norwich CT||Norwich||GB||WC1A 1AA||6567178330||1||1||small box||111||GBP||BIBA Warehouse||sample-order||3HPA||ITTEAM||developer||owe23||0||||||||||testing delivery||1%%40.0%%30.0%%2.0%%box1||1||||||||||||",
  "1" => "||shipName||shipName||Anthony Benoit||490 E Main Street||Norwich CT||Norwich||GB||WC1A 1AA||6567178330||1||1||small box||111||GBP||BIBA Warehouse||sample-order||3HPA||ITTEAM||developer||owe23||0||||||||||testing delivery||1%%40.0%%30.0%%2.0%%box1||1||||||||||||" ));

  $client = new SoapClient(null, array('location' => "http://oneworldexpress.co.uk/remote/main/smartsystemintegration.php?wsdl",
  'uri'      => "http://oneworldexpress.co.uk/remote/main/index.php"));

  $results =  $client->__soapCall('getBulkLabels', array('consignmentinformation' => $consignmentinformation));
  print_r($results);


  die;


  $client = new SoapClient(null, array(
  'location' => "http://oneworldexpress.co.uk/remote/main/smartsystemintegration.php?wsdl",
  'uri'      => "http://oneworldexpress.co.uk/remote/main/index.php"));

  $results =  $client->__soapCall('getBulkLabels', array('consignmentinformation' => $consignmentinformation));
  print_r($results);
  die;

 */


/* $client = new SoapClient(null, 
  array(
  'location' => "https://oneworldexpress.co.uk/remote/main/smartsystemintegration.php?wsdl",
  'uri'      => "https://oneworldexpress.co.uk/remote/main/index.php"));

 */
/* 						$client = new SoapClient(null, 
  array(
  'location' => "http://tracked.kaabnl.nl/fetchlabels.php?wsdl",
  'uri'      => "http://tracked.kaabnl.nl")); */
$client = new SoapClient(null, array(
    'location' => "http://oneworldexpress.co.uk/remote/main/smartsystemintegration.php?wsdl",
    'uri' => "http://oneworldexpress.co.uk/remote/main/index.php"));

//$results =  $client->__soapCall('importValidConsignmentData', array('consignmentinformation' => $consign));
//$consignmentinformation = "||MikeVybiral||MikeVybiral||LoganCottageGrangeLaneEastLangton||||¬Leicestershire||MarketHarborough Leicestershire||DE||10006||01858 545376||1||0.179||Socket||15.41||USD||wanghaitao||R1||E - EUROPE SMART A||FLYT||FLYT||FLYT.FLY||1||||||||||[OW00112] ||0.179%%1.0%%1.0%%1.0||||||||||||||";
//$consignmentinformation = 'TEST-123123123||Catherine Maguire||Catherine Maguire||77 Midcroft Ave||||¬North Lanarkshire||Glasgow North Lanarkshire||GB||G44 5RL||n a||1||0.6680||CS 64mm Stainless Ste||3.57||USD||wanghaitao||||3HPA||IMC||IMC||b5G@c1Q)||0||||||||||[OW00367] ||0.6680%%1.0%%1.0%%1.0||||0xxrqx61b5g92y4@marketplace.amazon.co.uk||||||||||';


$consignmentinformation = '||Sherlock Holmes||Sherlock Holmes||Heidelbersfgdelb 1||Heidelberg  eidelber||||Heidelbergs elber||GB||UB33nb||1234567||1||1||books||5||USD||wanghaitao||||12||ITTEAM||developer||owe23||0||||||||||[OW00039] 手机x1 手机壳x2 [S01-AB-03]x1||0.100%%18.0%%26.0%%10.0||||abc@123.com||||Lithium Ion battery||||||';



//$consignmentinformation = "||Private Shipper||KATE HOWSAM ||4 GRANGE PARK||test||test||Karachi||PK||74000||-||1||0.257||Thank you.||1||USD||IMS||-||WS COURIER||ITSSHIP||ITSSHIP||ITSSHIP||0||0||0||0||NONDOC||||0.257%%1%%1%%1||||||||||||||";
//$consignmentinformation = "||no||Iris Mori||Mori Immobilien Martin-Luther-Ring 3 Oppenheim||||||Martin-Luther-Ring||GB||ub33nb||06133 579567||2||5.500||car dvd||80||USD||wanghaitao||||3HPA||ITTEAM||developer||%$30bhFE||0||||||||||[OW00816] ||5.500%%50.0%%50.0%%50.0&&5.500%%50.0%%50.0%%50.0||||||||||||||";
/* $consignmentinformation = "||Jacqueline Panter||Jacqueline Panter||92 Lovell Road||||¬Cambs||CAMBRIDGE Cambs||GB||CB4 2QP||07961 870471||1||2.000||CAT TREE||5||USD||wanghaitao||||3HPA||OW00587||OW00587||OW00587||0||||||||||[OW00587] ||2.000%%1.0%%1.0%%0.0||||b605hjv06d3x4vh@marketplace.amazon.co.uk||||||||||";
  $consignmentinformation = "||Peggy Martin||Peggy Martin||4 Monnina Park||||¬NEWRY DOWN||Bessbrook NEWRY DOWN||GB||BT358PP||(085) 148-3682||1||6||Lamp||32||USD||wanghaitao||||12||ITTEAM||developer||developer||0||||||||||[OW00122] ||5.600%%22.0000%%100.0%%22.0000||||cs@oneworldexpress.com||||||||||";
  /*$consignmentinformation = "||Antonio Costa||Antonio Costa||Rua da Mãe de Deus 10||||¬REGIAO AUTONOMA DOS ACORES||Vila do Porto||PT||9580||911168510||1||1.000||Mobile phones||30||USD||wanghaitao||||ECX||SED(UK)||SED(UK)||SED(UK)||0||||||||||[OW00122] ||1.000%%12.0000%%18.0000%%12.0||||cs@oneworldexpress.com||||||||||"; */
//$results =  $client->__soapCall('', array('consignmentinformation' => $consignmentinformation));
$results = $client->__soapCall('getLabels', array('consignmentinformation' => $consignmentinformation));

//$results =  $client->__soapCall('removeInvalidLabel', array('consignmentinformation' => "sue123||ITTEAM||developer||developer"));
//$results =  $client->__soapCall('importConsignmentData', array('consignmentinformation' => $consignmentinformation));



echo "<pre>";
print_r($results);
echo "<br>";
die;



/* $consignmentinformation = array ( 

  "0" => "7900671466910778",
  "1" => "9020075466878873",
  "2" => "6015806466878276"

  );
  print_r($consignmentinformation);
  $client = new SoapClient(null, array(
  'location' => "http://oneworldexpress.co.uk/remote/main/smartsystemintegration.php?wsdl",
  'uri'      => "http://oneworldexpress.co.uk/remote/main/index.php"));
  $results =  $client->__soapCall('getMultiTracking', array('consignmentinformation' => $consignmentinformation));
  print_r($results);
  die;
  //$consign = "||Richard Guttridge||Richard Guttridge||3rd Floor 64 Bridge Street||||||Manchester||GB||M33BN||01618391986||1||0.099||Lavalier Microphone||5.00||USD||SELEAD||144229665462||DX1||MICHELLE||MICHELLE||michelle.com||0||||||||||||||||||||||||||";

  ini_set('default_socket_timeout', 120);
  $consignmentinformation = '{
  "UserName": "MICHELLE",
  "Password": "michelle.com",
  "Awb": "JD0002251443848310",
  "Weight": "2.600"
  }';
  $client = new SoapClient(null,
  array(
  'location' => "http://oneworldexpress.co.uk/remote/main/smartsystemintegration.php?wsdl",
  'uri'      => "http://oneworldexpress.co.uk/remote/main/index.php"));
  $results =  $client->__soapCall('updateConsignmentWeight', array('consignmentinformation' => $consignmentinformation));
  print_r($results);
  die;
  $trackingInformation = "YPS||YPS.COM||CHN10000010289CUW||Item arrived at UK Hub||UK||Item arrived at UK Hub||";

  //$trackingInformation = "TURBUS||TURBUS||RR282908869HU||scanned||parcel scanned in spain||SPAIN";


  $client = new SoapClient(null, array(
  'location' => "http://oneworldexpress.co.uk/remote/main/smartsystemintegration.php?wsdl",
  'uri'      => "http://oneworldexpress.co.uk/remote/main/index.php"));
  $results =  $client->__soapCall('addTracking', array('consignmentinformation' => $trackingInformation));

  print_r($results);


  die;
 */

$consign = "||MR Nondedeu Nathalie||MR Nondedeu Nathalie||quartier pas de pouyen||||||le beausset||FR||83330||0638986062||1||0.01||SHABBIR||8||USD||wanghaitao||||ASE||4444||atul||atul4444!||0||||||||||[OW00589]||0.200%%1.0%%1.0%%1.0||||cs@oneworldexpress.com||||||||||";

//echo $consign;
//$consignmentinformation = "TEST96345KA-2||potter rex||potter rex||3 paycocke way ||||||coggeshall Essex||CW||CO61QD||01376561924||3||0.630||7\" Inch Car Touch Screen GPS||25||USD||wanghaitao(sync)||OW1507080006110||CURA||CPOST||CPOST||CPOST.COM||0||||||||||||0.630%%1%%1%%1||||||||Lithum battery||||||";



$client = new SoapClient(null, array(
    'location' => "http://oneworldexpress.co.uk/remote/main/smartsystemintegration.php?wsdl",
    'uri' => "http://oneworldexpress.co.uk/remote/main/index.php"));

//$results =  $client->__soapCall('importValidConsignmentData', array('consignmentinformation' => $consign));

$results = $client->__soapCall('getLabels', array('consignmentinformation' => $consign));

print_r($results);

die;





$client = new SoapClient(null, array('location' => 'http://oneworldexpress.co.uk/remote/main/smartsystemintegration.php?wsdl',
    'uri' => 'http://oneworldexpress.co.uk/remote/main/index.php'));


echo $consignmentinformation = "3334652-1-1-1-1-1-1||4444||atul||atul.com";
echo '<br>';
$results = $client->__soapCall('removeInvalidLabel', array('consignmentinformation' => $consignmentinformation));
print_r($results);
die;

//$trackingInformation = "TURBUS||TURBUS||" . 'YPS1000' .$Parcel_ID . "||". $status;

/* 	$trackingInformation = "TURBUS||TURBUS||RR282908869HU||scanned||parcel scanned in spain||SPAIN";


  $client = new SoapClient(null, array(
  'location' => "http://oneworldexpress.co.uk/remote/main/smartsystemintegration.php?wsdl",
  'uri'      => "http://oneworldexpress.co.uk/remote/main/index.php"));
  $results =  $client->__soapCall('addTracking', array('consignmentinformation' => $trackingInformation));

  print_r($results);


  die; */

$consign = "||YUXING SONG||YUXING SONG||Ground Floor||Unit 1 Talbot Mill||||Manchester||GB||ub33nb||7872840850||13||227.10||Make up bags, Cushion Cases,Accessory，Cake Mould，Kitchen Taps,Pet Clothes||2000||USD||wanghaitao(sync)||OW1508120006842||A|A||MICHELLE||MICHELLE||michelle.com||0||1||0||0||NONDOC||NOTES||227.10%%1%%1%%1||||jasonatoxford@gmail.com||||||||||";
//$consign =       "||Test||Test||Am Vespucio Nte 1630||test||Sao Paulo||Santiago||IT|10002||07916923313||1||0.25||Test||10||GBP||wanghaitao sync||||PRIORITY||BESSKY||BESSKY||BESSKY||0||||||||||||||||mruga@one.com||||||||||";
//$consign = "||EGS-EglobalSol||Adnan Rahim||Av. Brigadeiro Faria Lima, 3989, Itaim Bibi||||Sao Paulo||Itaim Bibi||GB||ub33nb||1343554432||1||0.47||Toys & Hobbies/Spor||36.83||USD||-||1233445||2||4444||atul||atul.com||0||||||||||||||||adnan.rahim@eglobalsolution.com||||||||||";
//$consignmentinformation = "||potter rex||potter rex||3 paycocke way ||||||coggeshall Essex||CW||CO61QD||01376561924||1||0.630||7\" Inch Car Touch Screen GPS||25||USD||wanghaitao(sync)||OW1507080006110||CURA||CPOST||CPOST||CPOST.COM||0||||||||||||0.630%%1%%1%%1||||||||Lithum battery||||||";
//$consignmentinformation = "YPS10002131172||dfgh||dfghd dfghdfgh||dhdgfh||dghdgfh||||dfghdgfh||CW||345345||23423424||1||2.00||N/A||32.31||GBP||dfsgs||34435||CURA||CPOST||CPOST||CPOST.COM||0||||||||||||||||||||||||||";
//$consignmentinformation = "YPS10002131172_2||potter rex||potter rex||3 paycocke way ||||||coggeshall Essex||CW||CO61QD||01376561924||1||0.630||7\" Inch Car Touch Screen GPS||25||USD||dfsgs||34435||CURA||CPOST||CPOST||CPOST.COM||0||||||||||||0.630%%1%%1%%1||||||||Lithum battery||||||";
//$consign	=	"||potter rex||potter rex||3 paycocke way ||-||-||coggeshall Essex||CW||CO61QD||01376561924||1||0.630||7 Inch Car Touch Screen GPS||25||USD||wanghaitao(sync)||OW1507080006110||CURA||CPOST||CPOST||CPOST.COM||0||||||||||||||||||||||||||";
//$consign = "BEFA009_1||Company||John Snow||Street1||Street1||||City GD||US||10000||134567890||1||1.20||Test||10||USD||wanghaitao sync||SBXP1507200000003||REGPOSTINTUTR||MICHELLE||MICHELLE||michelle.com||0||||||||||TEst||1.200%%1%%1%%1||||||||||||||";
//$consign = "RA999920150717001_3||MERIDIAN LOGISTIC SRL||MERIDIAN LOGISTIC SRL||VIA DELL\'INDUSTRIA 19||||||FASANO (BR)||IT||72015||3391227288||10||212.810||Nail art supplies bicycle bulb||1266||USD||wanghaitao(sync)||OW1507210012678||ESU||HUANOU||HUANOU||HUANOU||0||||||||||||212.810%%40%%69%%30||||||||Lithum battery||||||";
//$consign = "||Banke Alabi||Banke Alabi||147 windmill lane||||||London||GB||U6 9DP||07971808181||1||0.693||Dress||22||USD||wanghaitao(sync)||OW2015072320798||TP2||SELEAD||SELEAD||SELEAD.com||0||||||||||||0.693%%0%%0%%0||||||||||||||";
//$consign	=	"||potter rex||potter rex||3 paycocke way ||-||-||coggeshall Essex||CW||CO61QD||01376561924||1||0.630||7 Inch Car Touch Screen GPS||25||USD||wanghaitao(sync)||OW1507080006110||CURA||CPOST||CPOST||CPOST.COM||0||||||||||||||||||||||||||";
//$consign = "20150728-UK-6-40||4PX  FULFILLMENT （UK） LTD||HENRY||Unit 5 Trident Way||International Trading Estate||||Southall  Middlesex||GB||UB2 5LF||||40||500.000||Scooter||2800||USD||wanghaitao(sync)||OW1507280006867||DOM||SELDHL||SELEAD(DHL)||SELEAD(DHL)||0||||||||||||500.000%%25%%66%%20||||||||||||||";
//$consignmentinformation =	"FR-OWE-2||Apple||Robert||2 Roberts Rd ||Hillingdon||Middlesex||New York||AF||||02086058999||1||1.00||Books||1.00||GBP||John||SHOP22233||WPX||4444||atul||atul.com||0||||||||||||||||||||||||||";
//$consignmentinformation = "||EGS-EglobalSol||Adnan Rahim||Alameda Santos, 2233||||Sao Paulo||ABC||BR||12345||1343554432||1||1.50||Tablet PC||211.26||USD||-||-||SKYPBR||C2YBR||C2YBR||c2ybr.com||1||||||||||||||||adnan.rahim@eglobalsolution.com||||||||||";
//$consign = 				  "123TESTNEW||EGS-EglobalSol||Adnan Rahim||Av. Brigadeiro Faria Lima, 3989, Itaim Bibi||||Sao Paulo||Itaim Bibi||BR||04538-133||1343554432||1||0.47||Toys & Hobbies/Spor||36.83||USD||-||1233445||SKYPBR||C2YBR||C2YBR||c2ybr.com||1||||||||||||||||adnan.rahim@eglobalsolution.com||||||||||";
//$consign = 	"RE117471841-2||Pedro Moreno delgado||Pedro Moreno delgado||Calle Montevideo n1||||||Sevilla||ES||41013||665273838||1||0.050||Case||2.68||USD||wanghaitao(sync)||OW1508050000079||CORUTR||BESSKY||BESSKY||BESSKY||0||||||||||ZSY5012606B[1-7N42]X1 ||0.050%%1%%1%%1||||bpphsjf12mjjv6c@marketplace.amazon.es||||||||||";
//$consign = 	'||Ali Dharamshi||Ali Dharamshi||116 st Paul\'s road||||||Birmingham||de||10006||07852200624||1||0.444||Bike Bag||12.4||USD||wanghaitao(sync)||OW1508250013649||19EURDPDDE||4444||atul||atul.com||0||||||||||||0.444%%1%%1%%1||||||||Lithum battery||||||';

$consign = '||Marcus Nilsson||Marcus Nilsson||björkvägen 5||||||hörnefors||SE||90 532||728517737||1||0.630||Brand phones||76.46||USD||SELEAD||144075424628||19EURDPD||SED(UK)||SED(UK)||SED(UK)||0||||||||||||||||||||||||||';

$client = new SoapClient(null, array(
    'location' => "http://oneworldexpress.co.uk/remote/main/smartsystemintegration.php?wsdl",
    'uri' => "http://oneworldexpress.co.uk/remote/main/index.php"));

//$results =  $client->__soapCall('importValidConsignmentData', array('consignmentinformation' => $consign));

$results = $client->__soapCall('getLabels', array('consignmentinformation' => $consign));

print_r($results);

die();





/*
  $client = new SoapClient(null,
  array('location' =>  'http://oneworldexpress.co.uk/remote/main/smartsystemintegration.php?wsdl',
  'uri' => 'http://oneworldexpress.co.uk/remote/main/index.php'));


  $consignmentinformation ="||Margareth Tagliapietra||Margareth Tagliapietra||Av. Dom Nery||Cx Postal 4045||V. EMBAREValinhos||EMBAREValinhos||BR||13271-970||193881.1324||1||0.35||-||5.41||USD||-||-||CORBR||C2YBR||C2YBR||c2ybr.com||1||||||||||||||||atul@oneworldexpress.com||||||||||";
  $jsonData = '{"UserType": "client",
  "UserName": "test",
  "UserPass": "test",
  "UserAccount": "test",
  "Company": "company",
  "FullName": "FullName",
  "Address": "Address",
  "Email": "Email",
  "ParentUser": "ParentUser",
  "Country": "Country",
  "Services": ["Yodel", "Hermes"]}';
  $results =  $client->__soapCall('addUserInformation', array('userInformation' => $jsonData));
  print_r($results);
  die;
 */

/* try {

  $consignmentinformation = 'FJ803936614GB';

  $client = new SoapClient(null, array(
  'location' => "http://oneworldexpress.co.uk/remote/main/smartsystemintegration.php?wsdl",
  'uri'      => "http://oneworldexpress.co.uk/remote/main/index.php"));
  $results =  $client->__soapCall('getTracking', array('consignmentinformation' => $consignmentinformation));
  print_r($results);
  }
  catch (SoapFault $e) {
  $result = array(
  'erro' => $e->faultstring
  );
  print_r($result);
  }


  die; */





//$consignmentinformation ="25125115023||Tanja Pinxten||||Robijnstraat 18||||||BREE||BE||3960||499251722||1||5.61||Shipment||0.00||EUR||Vitalisage||25125115023.100002709||||MISFUL||misful||misful.com||1||0||0||0||NONDOC||||||||tanjapinxten@hotmail.com||||||||||";
/*
  $consignmentinformation ="||ABC CO LTD||Hello World||123 LONDON ROAD||||Acre||Acre||BR||12324||111222333444||1||0.80||-||40.50||USD||-||-||||CPOST||CPOST||CPOST.com||1||||||||||||||||support@eglobalsolution.com||||||||||
  ";
  $consignmentinformation = "TID1501604CN||a||a||JD ADDRESS||||||WL||GB||HA3 6LP||18882||1||0.2||THSD||2||GBP||WINIT||WINIT||3HPA||WINIT||WINIT||WINIT.COM||0||||||||||||||||||||||||||";
  $consignmentinformation ="||test123||tesst||9 Temasek Boulevard||Suite 20-01, Suntec Tower 2||-||UNITED KINGDOM||GB||38989||2||1||0.55||1||1.00||GBP||-||m3566/wwt0||INT||4444||atul||atul.com||1||||||||||||||||||||||||||";

  $client = new SoapClient(null, array(
  'location' => "http://hermes.oneworldexpress.co.uk/remote/main/webservicetracking.php?wsdl",
  'uri'      => "http://hermes.oneworldexpress.co.uk/remote/main/index.php"));

  /*
  $consignmentinformation = "X15062201017||Justo  Colunga Hidalgo||Justo  Colunga Hidalgo||Avenida Pablo Iglesias||Nº 20 4ºDcha||||Gijón Asturias||ES||33205||676464964||1||0.560||adaptor||10||USD||wanghaitao(sync)||OW1507020002279||CORREOS||WEIJU||WEIJU||WEIJU.COM||0||||||||||||0.560%%1%%1%%1||||||||||||||"; */

//$consignmentinformation = "||Jan SÝKORA||Jan SÝKORA||Basteckeho 2552||||||Prague||CZ||15500||420724577562||1||0.50||Men's 2015 Climbing ||36.25||USD||Freipost UK||2D1031D5449||KB||demo||demo||demodemo||0||||||||||||||||||||||||||";

$consignmentinformation = "||Jan SÝKORA||Jan SÝKORA||Basteckeho 2552||||||Prague||CZ||15500||420724577562||1||0.50||Men's 2015 Climbing ||36.25||USD||Freipost UK||2D1031D5449||KB||LOGCON||logicons||logicons.net||0||||||||||||||||||||||||||";

$consignmentinformation = "FR-OWE-2||Apple||Robert||2 Roberts Rd ||Hillingdon||Middlesex||New York||AF||||02086058999||1||1.00||Books||1.00||GBP||John||SHOP22233||WPX||4444||atul||atul.com||0||||||||||||||||||||||||||";
//$consignmentinformation = "HU14350270957856||saif alradhi||saif alradhi||ul. Jana Pawla || 28||-||Poznan||PL||69-965||513939906||1||0.263||Cell Phone||22||USD||wanghaitao||OW2015062400310||REGPOSTHUNEUR||SELEAD||SELEAD||SELEAD.com||0||||||||||Lithum battery||0.262%%0%%0%%0||||||||Lithum battery||RR281590901HU||2015-06-24 08:50:46||";
//$consignmentinformation = "ES14357928181316||WISDOM EDEM||WISDOM EDEM||C/DE LES FLORS||NO1 A 5-1||-||VIC||ES||08500||631478262||1||9.4870||Boots of Leather||22||USD||SELEAD||143581739174||CORREOS||SELEAD||SELEAD||SELEAD.COM||0||||||||||||||||||||||||||";


/*

  $client = new SoapClient(null,
  array('location' =>  'http://oneworldexpress.co.uk/remote/main/smartsystemintegration.php?wsdl',
  'uri' => 'http://oneworldexpress.co.uk/remote/main/index.php'));


  echo $consignmentinformation ="X15062201017||WEIJU||WEIJU||WEIJU.COM";
  echo '<br>';
  $results =  $client->__soapCall('removeInvalidLabel', array('consignmentinformation' => $consignmentinformation));
  print_r($results);
  die; */


//echo $_SERVER['REMOTE_ADDR'].'<br>';
//require_once("../includes/settings/config.inc.php");
/*
  $USERNAME	= array('atul');
  $PASSWORD	= array('atul.com');

  $consignmentinformation = array (
  "0"  => $USERNAME,
  "1"  => $PASSWORD,
  "2"  => array (
  "0" => "FR-OWE-221||Apple||Robert||2 Roberts Rd ||Hillingdon||Middlesex||New York||AF||||02086058999||1||1.00||Books||1.00||GBP||John||SHOP22233||WPX||4444||atul||atul.com||0||||||||||||||||||||||||||",
  "1" => "FR-OWE-222||Apple||Robert||2 Roberts Rd ||Hillingdon||Middlesex||New York||AF||||02086058999||1||1.00||Books||1.00||GBP||John||SHOP22233||WPX||4444||atul||atul.com||0||||||||||||||||||||||||||",
  "2" => "||MISGUIDED LTD||NA NA||UNIT 8 CENTENARY PARK||CORONET WAY SALFORD||-||MANCHESTER CITY||GB||UB33NB||0000000000000000||1||0.25||CLOTHING||1.00||EUR||John||SHOP22233||RM2||4444||||atul||atul.com||0||||||||||||||||||||||||"
  ));



  //$consignmentinformation['CONSIGNMENT'][]= "FR-OWE-105||Apple||Robert||2 Roberts Rd ||Hillingdon||Middlesex||New York||AF||||02086058999||1||1.00||Books||1.00||GBP||John||SHOP22233||WPX||4444||atul||atul.com||0||||||||||||||||||||||||||";
  //$consignmentinformation['CONSIGNMENT'][]= "FR-OWE-205||Apple||Robert||2 Roberts Rd ||Hillingdon||Middlesex||New York||AF||||02086058999||1||1.00||Books||1.00||GBP||John||SHOP22233||WPX||4444||atul||atul.com||0||||||||||||||||||||||||||";
  //$consignmentinformation['CONSIGNMENT'][]= "||MISGUIDED LTD||NA NA||UNIT 8 CENTENARY PARK||CORONET WAY SALFORD||-||MANCHESTER CITY||GB||UB33NB||0000000000000000||1||0.25||CLOTHING||1.00||EUR||John||SHOP22233||RM2||4444||||atul||atul.com||0||||||||||||||||||||||||";



  $client = new SoapClient(null, array(
  'location' => "http://oneworldexpress.co.uk/remote/main/smartsystemintegration.php?wsdl",
  'uri'      => "http://oneworldexpress.co.uk/remote/main/index.php"));

  $results =  $client->__soapCall('getBulkLabels', array('consignmentinformation' => $consignmentinformation));
  print_r($results);
  die;
 */

/* $consignmentinformation12= "||MISGUIDED LTD||NA NA||UNIT 8 CENTENARY PARK||CORONET WAY SALFORD||-||MANCHESTER CITY||UNITED KINGDOM||M50 1RE||0000000000000000||1||0.25||CLOTHING||1.00||EUR||-||RM2||RM48||MILESEXP||||MILESEXP||alberttown";
  $consignmentinformation12="||MISGUIDED LTD||NA NA||UNIT 8 CENTENARY PARK||CORONET WAY SALFORD||-||MANCHESTER CITY||UNITED KINGDOM||M50 1RE||02088366060||1||0.25||CLOTHING||1.00||EUR||-||RM2||RM48||MILESEXP||||MILESEXP||alberttown";* /

  $clienta = new SoapClient(null, array(
  'location' => "http://trackedmail.oneworldexpress.co.uk/remote/main/fetchlabels.php?wsdl",
  'uri'      => "http://trackedmail.oneworldexpress.co.uk/remote/main/index.php"));

  $resultas =  $clienta->__soapCall('getLabels', array('consignmentinformation' => $consignmentinformation12));
  print_r($resultas);

  /*$consignmentinformation ="PHPBooks||SMARTSTREAM TECHNOLOGIES INC||MARK THOMSON|| 61 BROADWAY SUITE 710||NEW YORK||NY||NEW YORK||US||10006||2124585658||4||20||Programming Books of PHP||10.30||USD||Muhammad Kazim||Reference||INTE||4444||atul||atul.com||0||0||0||0||NONDOCS||Notes||3%%3%%3%%3&&4%%4%%4%%4&&20%%20%%30%%3&&20%%20%%30%%34||20||||||||||||				   						  ";
 */
/* Hawb || company || contact || address1 || address2 || address3 || city ||country code|| postcode || telephone || numberpieces || weight || description || value || currency || sendername || reference || service code||account || username||password||Warehouse Location || Storage Location || Sequence No || empty4 || empty5 */

//$consignmentinformation ="||company||test1||248, Uxbridge Road||Feltham||Middlesex||London||GB||UB3 3NB||11111111||1||1||description||1.00||GBP||ali||reference||ECX||4444||atul||atul.com||0||1||0||2||||||||||||||||||||";
/*
  $consignmentinformation ="CB14080500232L||Thomas Pondysh||Thomas Pondysh||286 County Route 14 ||||||NY||US||12980||5188569767||0||0.60||FlashCard×1||6.19||USD||OW00005||OW00005||EURBTOC||MICHELLE||MICHELLE||michelle.com||||||||||||||||||||||||||||";


  $consignmentinformation  = "||company||test1||320/62 bahnhofstr||Rheinbach||BERLIN||BERLIN||||11111111||1||12||description||1.00||GBP||ali||WPX||4444||reference||INT||Channel Islands"; */

/* $consignmentinformation ="||Apple||Robert||2 Roberts Rd ||Hillingdon||||London||GB||UB3 4JJ||02086058999||1||1.00||Books||1.00||GBP||John||test0258963||TP2||BFELOGCH||BFELOGCH||BFELOGCH||0||||||||||||||||||||||||||";



  $client = new SoapClient(null, array(
  'location' => "http://oneworldexpress.co.uk/remote/main/smartsystemintegration.php?wsdl",
  'uri'      => "http://oneworldexpress.co.uk/remote/main/index.php"));

  $results =  $client->__soapCall('getLabels', array('consignmentinformation' => $consignmentinformation));

  print_r($results);


  /*$consignmentinformation = "atul||atul.com";
  $results =  $client->__soapCall('enableServices', array('consignmentinformation' => $consignmentinformation));
  print_r($results); */


////dhl
/*
  $client = new SoapClient(null, array(
  'location' => "http://oneworldexpress.co.uk/remote/main/smartsystemintegration.php?wsdl",
  'uri'      => "http://oneworldexpress.co.uk/remote/main/index.php"));

  //$consignmentinformation 	= "EUR1000004099LHR";		//INTERPOST
  //$consignmentinformation 	= "EUR1000004094LHR";		//INTERPOST
  //$consignmentinformation 	= "EUR1000004100LHR";		//INTERPOST


  //$consignmentinformation ='JD0002251443185074';    //Yodel
  //$consignmentinformation ='JD0002241571337147';    //Yodel
  //$consignmentinformation ='JD0002241571337141';    //Yodel


  //$consignmentinformation ='RE721521615SE'; // Registered Post
  //$consignmentinformation = 'RE721521655SE'; // Registered Post
  //$consignmentinformation ='RE721582107SE'; // Registered Post


  //$consignmentinformation ='FJ803936645GB'; // Royal Mail
  //$consignmentinformation ='FJ803999693GB';
  //$consignmentinformation ='FJ804044627GB';

  //$consignmentinformation ='FJ803936628GB';

  //$consignmentinformation = 'RE721521624SE';
  //$consignmentinformation = 'RE721512826SE'; tracking not coming

  //$consignmentinformation = 'RE721582265SE';




  //$consignmentinformation ='5539299883';
  //FJ803936628GB   5539274635  RE721582243SE CZL10440
  $consignmentinformation = 'RR237395735HU'; // Hungery Post
  $consignmentinformation = 'RE735913753SE'; // Hungery Post */

//$consignmentinformation = 'RE721512809SE'; // Hungery Post

/**/
/*
  $consignmentinformation = '1Z237V9E6891912128';

  $client = new SoapClient(null, array(
  'location' => "http://oneworldexpress.co.uk/remote/main/smartsystemintegration.php?wsdl",
  'uri'      => "http://oneworldexpress.co.uk/remote/main/index.php"));
  $results =  $client->__soapCall('getTracking', array('consignmentinformation' => $consignmentinformation));
  print_r($results);
  die;
 */
?>
<?php

/*
  require_once("../includes/settings/config.inc.php");

 */

//$consignmentinformation = "||oneworld||Bryan Thomsett||Herts, 34 Hare Street Road||as||yt||selbu||DE||4159||01763 2733003||1||1||Thank you.||1||GBP||Vivo Technologies Ltd.||refernce||1H||ATUL||atulbhakta||nikita2000||0";
//$consignmentinformation ="CB14080500232L||Thomas Pondysh||Thomas Pondysh||286 County Route 14 ||||||NY||GB||12980||5188569767||0||0.60||FlashCard×1||6.19||USD||OW00005||OW00005||1H||MICHELLE||MICHELLE||michelle.com||||||||||||||||||||||Lithium Battery||||||";



/* $consignmentinformation = "atul||atul.com";
  $results =  $client->__soapCall('enableServices', array('consignmentinformation' => $consignmentinformation));
  print_r($results); */


////dhl*/
//$consignmentinformation ="CB14080500232L||Thomas Pondysh||Thomas Pondysh||286 County Route 14 ||||||NY||GB||12980||5188569767||0||0.60||FlashCard×1||6.19||USD||OW00005||OW00005||1H||MICHELLE||MICHELLE||michelle.com||||||||||||||||||||||Lithium Battery||||||";
$client = new SoapClient(null, array(
    'location' => "http://oneworldexpress.co.uk/remote/main/smartsystemintegration.php?wsdl",
    'uri' => "http://oneworldexpress.co.uk/remote/main/index.php"));

//$consignmentinformation = "||José Henrique da Silveira Jacob||José Henrique da Silveira Jacob||Rua Baldraco 75^ Casa 8 Cachambi ||0 ||||Rio De Janeiro||BR||20780-220||2173075002||1||0.21||PET FISH ZONE||17.19||USD||wanghaitao||wanghaitao||REGPOST INT||MICHELLE||MICHELLE||michelle.com||0||||||||||Lithum battery||||||||||Lithium Battery||YYRE736689232SE||2014-11-09 09:47:00||";						
//$consignmentinformation ="FAI033-22||EIZO Corporation||Rachel Duplessis||153 Shimokashiwano||HAKUSAN||postcode 924-8566||Ishikawa||||762776792||1||0.50||docs||0.00||GBP||-||DOX||TMA||m3620 / fa||INT||JAPAN";
//$consignmentinformation ="21501063||EDWARD||Edward Happy||1st Floor, Office 101 ||HELLOWORLD TOWN||Belo Horizonte||Belo Horizonte||CW||1234||1238901232190||1||0.25||-||4.01||USD||-||-||CURA||CPOST||CPOST||CPOST.com||0||||||||||||||||||||||||2015-01-06 13:48:00||";
//for ($i=0; $i<500; $i++)
{
    $consignmentinformation = " 12679450||DEVELOPER TEST||DEVELOPER TEST||286 County Route 14 ||||||NY||BR||12980||5188569767||1||1||FlashCard×1||1||USD||OW00005||OW00005||REGESTINT||4444||atul||atul.com||||||||||||||||||||||Lithium Battery||TEST-BY-DEV-001||2015-01-30 12:00:00||5";


    $results = $client->__soapCall('importConsignmentData', array('consignmentinformation' => $consignmentinformation));
    print_r($results);
    echo "<br>";
}
die;

/*
  $consignmentinformation ="||One World Express||Gordon||One World House||Pump lane||Hayes||London||United Kingdom||UB3 3NB||02088366060||2||10||sample||1.00||GBP||Atul Bhakta||REGPOST||INT||TEST||CN-SAMI9100-LCS-WT15||buylogicse1||buylogicse1||RE34534534534SE";

  $client = new SoapClient(null, array('location' => "http://registeredpost.oneworldexpress.co.uk/remote/main/fetchlabels.php?wsdl",
  'uri'      => "http://registeredpost.oneworldexpress.co.uk/remote/main/search.php"));



  echo $results =  $client->__soapCall('getData', array('consignmentinformation' => $consignmentinformation));
  print_r($results);



 */


/* 	 $consignmentinformation ="||SMARTSTREAM TECHNOLOGIES INC||MARK THOMSON|| 61 BROADWAY SUITE 710||HAYES||HAYES||LONDON||GB||UB3 3NB||2124585658||4||20||Programming Books of PHP||10.30||USD||Muhammad Kazim||Reference||12||4444||atul||atul.com||0||1||1||1||NONDOCS||Notes||3%%3%%3%%3&&4%%4%%4%%4&&20%%20%%30%%3&&20%%20%%30%%34||20||tahir@oneworldexpress.com||||||||||				   						  ";


  /* $consignmentinformation ="||Apple||Robert||2 Roberts Rd ||Hillingdon||||London||GB||UB3 4JJ||02086058999||1||1.00||Books||1.00||GBP||John||test0258963||TP2||BFELOGCH||BFELOGCH||BFELOGCH||0||||||||||||||||||||||||||"; */

/* 	echo $consignmentinformation ="21501047||-||Muhammad khandala||SHAMA PLAZA||BLOCK A3 KHARADAR||Sind||KARACHI||PK||74000||345345345345345||1||0.09||-||6.51||GBP||-||-||C2YUK||C2YUK||c2yuk||c2yuk.com||1||||||||||||||||||||||||||";
  echo "test"; */




/**/
// $consignmentinformation = "CP14121400379_FP||Uri Raz||Uri Raz||Hagibor Almoni 58|| ||||Tel-Aviv Tel-Aviv||IL||6722201||972527297676||1||0.172||PS3videogameseriesproducts×1;||10.29||USD||wanghaitao||wanghaitao||REGHUNUTRINT||FPRICE||FPRICE||FPRICE.COM||0||||||||||Lithum battery||0.172%%1.00%%1.00%%1.00||||fajar.andi@hotmail.com||||Lithium Battery||CP14121400379||2014-12-16 00:00:00||";



/* $soap = new SoapClient("http://www.hermes-europe.co.uk/parceltrackingservice/services/parcelTrackingService?wsdl");
  $inputparameters1 = array(  'barcode' => '9305405465807976',
  'clientGroupId' => "99046",
  'clientLicence' => "68f10e31-fdbf-41e6-b668-66cd4ea3e7e0"
  );
  $xmlarr 					=	$soap->fetchTracking($inputparameters1);
  print_r($xmlarr);
 */

/* 			$client = new SoapClient(null, 
  array('location' =>  'http://oneworldexpress.co.uk/remote/main/smartsystemintegration.php?wsdl',
  'uri' => 'http://oneworldexpress.co.uk/remote/main/index.php'));
  $trackingNumber ="RE745502338SE";
  $results =  $client->__soapCall('getTracking', array('consignmentinformation' => $trackingNumber)); */

/* * **********************
  DELETE API INTEGRATION
 * *********************** */


/* $client = new SoapClient(null, 
  array('location' =>  'http://oneworldexpress.co.uk/remote/main/smartsystemintegration.php?wsdl',
  'uri' => 'http://oneworldexpress.co.uk/remote/main/index.php'));


  echo $consignmentinformation ="test123||4444||atul||atul.com";
  echo '<br>';
  $results =  $client->__soapCall('removeInvalidLabel', array('consignmentinformation' => $consignmentinformation));
  print_r($results);
  die; */


/* $consignmentinformation = "michelle||michelle.com||RS261207619GB||mawb-123456||carton-96587||man-65289";
  echo $consignmentinformation;

  $client = new SoapClient(null,
  array('location' =>  'http://oneworldexpress.co.uk/remote/main/smartsystemintegration.php?wsdl',
  'uri' => 'http://oneworldexpress.co.uk/remote/main/index.php'));


  //echo $consignmentinformation ="test123||4444||atul||atul.com";
  echo '<br>';
  $results =  $client->__soapCall('updateConsignmentMawb', array('consignmentinformation' => $consignmentinformation));
  print_r($results);
  die;

 */
?>
  

