<?php

require_once("../includes/settings/config.inc.php");

//require_once("/var/www/vhosts/oneworldexpress.co.uk/httpdocs/remote/includes/settings/local_settings/oneworldexpress_co_uk.php");
//require_once(SETTING_DIR_REMOTE . "includes/settings/config.inc.php");
//require_once(SETTING_DIR_REMOTE . "includes/library/dbaccess3.class.php");
//require_once(SETTING_DIR_REMOTE . "includes/library/iaddress.class.php");
//require_once(SETTING_DIR_REMOTE . "includes/mapping/currency.class.php");
//require_once(SETTING_DIR_REMOTE . "includes/mapping/currencyfilter.class.php");

include_classes([
        'iaddress.class'
        ], 'library');

include_classes([
    'currency.class','currencyfilter.class'
        ]);

DbAccess3::connect(SETTING_DB_SERVER, SETTING_DB_USER, SETTING_DB_PASSWORD, SETTING_DB_DATABASE);

$currencyFilter = new CurrencyFilter();
$currencyFilter->addFieldFilter('isactive', '1');
$currencyData = $currencyFilter->getList();
if (count($currencyData) > 0) {
    foreach ($currencyData as $currencyItem) {
        $baseCurrencySymbol = 'GBP';
        $currencyRightSymbol = trim($currencyItem->getRightsymbol());
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
            echo "1 : GBP  = " . $currecnyValuse . " : " . $currencyRightSymbol;
            echo "<br>";
            $currencyItem->setCurrencyexchangerate($currecnyValuse);
            $currencyItem->save();
        }
    }
}

?>
