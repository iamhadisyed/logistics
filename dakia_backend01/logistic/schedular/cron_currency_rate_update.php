<?php

require_once(__DIR__ . "/../includes/settings/config.inc.php");


include_classes([
    'iaddress.class',
    'currency.class',
    'currencyfilter.class',
]);
error_reporting(E_ALL);
ini_set("display_errors", "1");
set_time_limit(-1);

$dbConnection = DbAccess3::connect(SETTING_DB_SERVER, SETTING_DB_USER, SETTING_DB_PASSWORD, SETTING_DB_DATABASE);

$currencyFilter = new CurrencyFilter();
$currencyFilter->addFieldFilter('		isactive', '1');
if(isset($_POST['cid']) && !empty($_POST['cid'])){
	$currencyFilter->addFieldFilter('		id', $_POST['cid']);
}
$currencyData = $currencyFilter->getList();
$output = ['status' => 'success', 'message'=> 'Updated successfully', 'data' => []];
if (count($currencyData) > 0) {
	$data = [];
    foreach ($currencyData as $currencyItem) {
        $baseCurrencySymbol = 'GBP';
        $currencyRightSymbol = trim($currencyItem->getRightsymbol());
		$currencyId = $currencyItem->getId();
        if (trim($currencyRightSymbol) == 'GBP') {
            continue;
        }
        $urlData = 'http://coinmill.com/rss/GBP_' . trim($currencyRightSymbol) . '.xml';
        $xmlDataCurrency = file_get_contents($urlData);

        $arrayXmlData = xml2array($xmlDataCurrency);
        //<link>http://coinmill.com/GBP_USD.html</link>
        $DataLink = $arrayXmlData['rss']['channel']['item']['link'];
        $data_link = explode('_', $DataLink);
        $convertedCurrency = trim(str_replace('.html', '', $data_link[1]));
        $dataCurrency = $arrayXmlData['rss']['channel']['item']['description'];

        $discriptionPart = explode('<br/>', $dataCurrency);

        if ($currencyRightSymbol == $convertedCurrency) {
            $currecnyPart = explode('=', $discriptionPart[0]);
        } else {
            $currecnyPart = explode('=', $discriptionPart[1]);
        }

        $pos = strpos($currecnyPart[1], $currencyRightSymbol);
        if ($pos === false) {
            $currecnyUpdateValues = $currecnyPart[0];
        } else {
            $currecnyUpdateValues = $currecnyPart[1];
        }

        $currecnyValuse = trim(str_replace(trim($currencyRightSymbol), '', $currecnyUpdateValues));

        if ($currecnyValuse != '' && $currecnyValuse > 0) {
			$output['data'] += [$currencyId => ['id' => $currencyId, 'code' => $currencyRightSymbol, 'rate' => $currecnyValuse]];
            //echo $currencyId." => "."1 : GBP  = " . $currecnyValuse . " : " . $currencyRightSymbol;
            //echo "<br>";
            $currencyItem->setCurrencyexchangerate($currecnyValuse);
            $currencyItem->save();
        }
    }
}else{
	$output['status'] = "error";
	$output['message'] = "No curreny available to update";
}
mysqli_close($dbConnection);

echo json_encode($output);

?>
