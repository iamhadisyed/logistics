<?php

////////////////////////////////////////////////////
//
// List of tariffs
//
////////////////////////////////////////////////////

// get settings
require_once("../includes/settings/config.inc.php");

// check admin user is authenticated
if (!isset($_SESSION['admin']['id']) OR is_null($_SESSION['admin']['id'])) {util_redirect("login.php");}

//
$tariff_name	=	(isset($_REQUEST['tariff_name'])			 ? strip_tags($_REQUEST['tariff_name']) : '');


$service_id = util_request_num("service_id");
$collection_rateband_id = util_request_num("collection_rateband_id");
$destination_rateband_id = util_request_num("destination_rateband_id");
$collection_postcode_group_id = util_request_num("collection_postcode_group_id");

$destRateBand = new RateBand($destination_rateband_id);

$fileName = (trim($tariff_name) != "" )? $tariff_name : "tarrifs";

/*header('Expires: 0');
header('Cache-control: private');
header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
header('Content-Description: File Transfer');
header('Content-Type: application/csv');
header('Content-disposition: attachment; filename=' . $fileName . '.csv');*/


$tarrifNameChk	=	new TariffFilter();
$tarrifNameChk->addFieldFilter("courier_service_id", $service_id);
$tarrifNameChk->addFieldFilter("collection_rateband_id", $collection_rateband_id);
$tarrifNameChk->addFieldFilter("destination_rateband_id", $destination_rateband_id);
if(trim($tariff_name) != '')
				$tarrifNameChk->addFieldFilter('customer_id',$tariff_name);
$tarrifNameChk->addFieldOrderBy(' weight_from asc');
$tarrifList		= $tarrifNameChk->getList();
// Get list of tariffs
ob_flush();
header("Content-type: text/csv");
header('Content-disposition: attachment; filename=' . $fileName . '.csv');
header("Pragma: no-cache");
header("Expires: 0");

echo "Weight," . $fileName;

foreach ($tarrifList as $tariff)
{
//	if ($tariff->getWeightFrom() > 0)
	{
		echo "\n" . $tariff->getWeightTo();
		echo "," . $tariff->getExtraTariff();
	}
}
?> 


