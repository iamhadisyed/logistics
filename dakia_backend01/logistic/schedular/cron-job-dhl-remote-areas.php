<?php
//	Important
//	Please change email before putting it live
//	Please remove the break function from line number 130 for full file compliation 
//
require_once(__DIR__ . "/../includes/settings/config.inc.php");

include_classes([
    'iaddress.class',
    'consignment.class',
    'consignmentfilter.class',
    'parcel.class',
    'parcelfilter.class',
    'importdataapis.class',
    'trackingdata.class',
    'trackingdatafilter.class',
    'consignmenthold.class',
    'consignmentlog.class',
    'trackingdata.class',
    'consignmentlogfilter.class'
	]);
DbAccess3::connect(SETTING_DB_SERVER, SETTING_DB_USER, SETTING_DB_PASSWORD, SETTING_DB_DATABASE);
$connection = mysqli_connect(SETTING_DB_SERVER, SETTING_DB_USER, SETTING_DB_PASSWORD) 
		or die("Could not connect to database server.");
mysqli_select_db($connection, SETTING_DB_DATABASE)
				or die("Could not select database");
				
				
				

$countCsvFile	=	0;
$insertVar		=	array();
$lastPosition = file_get_contents('last_position.txt');
if (($handle = fopen("DHL_Final-20160401.csv", "r")) !== FALSE) 
{
	fseek($handle, $lastPosition);
	
    while (($data = fgetcsv($handle)) !== FALSE) 
	{
		if(trim($data[5]) == 'ALLCODES')
			$from_postcode		=	'';
		else
			$from_postcode		=	str_replace("'",'',$data[5]);
		
		if(trim($data[6]) == 'ALLCODES')
			$to_postcode		=	'';
		else
			$to_postcode		=	str_replace("'",'',$data[6]);
		
		if(trim($data[4]) != '')
		   $city_name			=	str_replace("'",'',$data[4]);
		else
		   $city_name			=	str_replace("'",'',$data[3]);
		$country_iso			=	str_replace("'",'',$data[1]);
		$postcode_name		=	str_replace("'",'','DHL2016APR-'.$country_iso);
		
		$insertVar[]	=	"('".trim($from_postcode)."','".trim($to_postcode)."','".trim($postcode_name)."','".trim($city_name)."','".trim($country_iso)."')";
		$countCsvFile++;	
		  
		if($countCsvFile >= 25000)
		{
			break;
		}
		
    }
	$insertQuery	=	'INSERT INTO `test_postcode_user_service_charges`( `from_postcode`, `to_postcode`, `postcode_name`, `city_name`, `country_iso`) VALUES';
    $insertQuery	.=	implode(',',$insertVar);
	
	echo $insertQuery;
	if(count($insertVar)>0)
	{
		
		mysqli_query($connection, $insertQuery) or die( mysqli_error());
		file_put_contents('last_position.txt', ftell($handle)); 
		echo "SUCCESS";
	}
	
	
	
	
	
	fclose($handle);
}
?>