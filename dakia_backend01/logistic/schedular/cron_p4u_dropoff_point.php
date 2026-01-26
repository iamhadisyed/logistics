<?php
require_once(__DIR__ . "/../includes/settings/config.inc.php");

include_classes([
    'parcelforudropoffpoint.class',
    'parcelforudropoffpointfilter.class'
]);
$API_KEY = "69f170ac506c6ab7";
$API_SECRET = "69f170ac506c6ab79487170a5593f413";

$url = "http://www.zasilkovna.cz/api/v3/69f170ac506c6ab7/branch.xml?type=address-delivery";

$xmlContent = file_get_contents($url);
echo "<pre>";
$xml = simplexml_load_string($xmlContent);
$json = json_encode($xml);
$array = json_decode($json,TRUE);
$arrayfromJson = $array["branches"]["branch"];

$insertQuery = "INSERT INTO `parcelforu_dropoff_point` (`name`,`place`,`address_line_1`,`city`,`postcode`,`statuscode`,`latitude`,`longitude`,`maxweight`,`label_routing`,`label_name`,`mon`,`tue`,`wed`,`thu`,`fri`,`sat`,`sun`,`country_iso`,`branch_id`) VALUES";
$insertDataArray = [];

foreach ($arrayfromJson as $info) {
    //print_r($info);
    $branchId =  $info["id"];
    //echo utf8_decode($info->name) . "<br />";
    $name = str_replace(",", " ", ($info["name"]));
    $name = str_replace("'", "\'", $name);
    $comapny = str_replace(",", " ", implode(" ", $info["place"]));
    $comapny = str_replace("'", "\'", $comapny);
    $addressline1 = str_replace(",", " ", implode(" ", $info["street"]));
    $addressline1 = str_replace("'", "\'", $addressline1);
    $city = implode(" ", $info["city"]);
    $postcode = $info["zip"];
    $countryIso = $info["country"];
    $statuscode = 1;
    $latitude = $info["latitude"];
    $longitude = $info["longitude"];
    $openingtimes = $info["openingHours"]["regular"];
    if (!is_object($openingtimes["monday"])) {
        $monOpeningTime = implode(" ", $openingtimes["monday"]);
    }
    if (!is_object($openingtimes["tuesday"])) {
        $tueOpeningTime = implode(" ", $openingtimes["tuesday"]);
    }
    if (!is_object($openingtimes["wednesday"])) {
        $wedOpeningTime = implode(" ", $openingtimes["wednesday"]);
    }
    if (!is_object($openingtimes["thursday"])) {
        $thuOpeningTime = implode(" ", $openingtimes["thursday"]);
    }
    if (!is_object($openingtimes["friday"])) {
        $friOpeningTime = implode(" ", $openingtimes["friday"]);
    }
    if (!is_object($openingtimes["saturday"])) {
        $satOpeningTime = implode(" ", $openingtimes["saturday"]);
    }
    if (!is_object($openingtimes["sunday"])) {
        $sunOpeningTime = implode(" ", $openingtimes["sunday"]);
    }
    $label_routing = $info["labelRouting"];
    $labelName = str_replace(",", " ", $info["labelName"]);
    $labelName = str_replace("'", "\'", $labelName);
    $maxWeight = $info["maxWeight"];
 
    $insertDataArray[] = "('" . mb_convert_encoding($name, 'UTF-8') . "', "
            . "'" . mb_convert_encoding($comapny, 'UTF-8') . "',"
            . " '" . mb_convert_encoding($addressline1, 'UTF-8') . "', '" . mb_convert_encoding($city, 'UTF-8') . "','" . $postcode . "', '" . $statuscode . "', "
            ." '" . $latitude . "', '" . $longitude . "',"
            . " '" . $maxWeight . "',"
            . "'". $label_routing ."', "
            . "'". mb_convert_encoding($labelName, 'UTF-8')  ."', "
            . " '" . $monOpeningTime . "',"
            . " '" . $tueOpeningTime . "', '" . $wedOpeningTime . "',"
            . " '" . $thuOpeningTime . "', '" . $friOpeningTime . "', "
            . "'" . $satOpeningTime . "', "
            . "'" . $sunOpeningTime . "', "
            . "'". $countryIso ."', "
            . "'" . $branchId . "')";
}
if (count($insertDataArray) > 0) {
    
    $p4utrancate = new parcelforuDropoffPointFilter();
    $p4utrancate->deletep4uRouting();
    $p4upickuppoint = new parcelforuDropoffPointFilter();
    echo $queryJoin = $insertQuery . implode(",", $insertDataArray);
    $p4upickuppoint->insertData($queryJoin);
}

//print_r($insertDataArray);
exit;
?>