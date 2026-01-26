<?php
require_once(__DIR__ . "/../includes/settings/config.inc.php");

include_classes([
    'parcelforupickuppoint.class',
    'parcelforupickuppointfilter.class'
]);
$API_KEY = "69f170ac506c6ab7";
$API_SECRET = "69f170ac506c6ab79487170a5593f413";

$url = "https://www.zasilkovna.cz/api/v4/" . $API_KEY . "/branch.json?lang=eng";

$jsonContent = file_get_contents($url);
$arrayfromJson =  json_decode($jsonContent, false, 512, JSON_UNESCAPED_UNICODE);
echo "<pre>";

$insertDataArray = [];

foreach ($arrayfromJson->data as $info) {
    //print_r($info);
    $branchId =  $info->id;
    //echo utf8_decode($info->name) . "<br />";
    $name = str_replace(",", " ", ($info->name));
    $name = str_replace("'", "\'", $name);
    $comapny = str_replace(",", " ", $info->place);
    $comapny = str_replace("'", "\'", $comapny);
    $addressline1 = str_replace(",", " ", $info->street);
    $addressline1 = str_replace("'", "\'", $addressline1);
    $city = $info->city;
    $postcode = $info->zip;
    $countryIso = $info->country;
    $statuscode = $info->status->statusId;
    $statusdescription = $info->status->description;
    $latitude = $info->latitude;
    $longitude = $info->longitude;
    $openingtimes = $info->openingHours->regular;
    if (!is_object($openingtimes->monday)) {
        $monOpeningTime = $openingtimes->monday;
    }
    if (!is_object($openingtimes->tuesday)) {
        $tueOpeningTime = $openingtimes->tuesday;
    }
    if (!is_object($openingtimes->wednesday)) {
        $wedOpeningTime = $openingtimes->wednesday;
    }
    if (!is_object($openingtimes->thursday)) {
        $thuOpeningTime = $openingtimes->thursday;
    }
    if (!is_object($openingtimes->friday)) {
        $friOpeningTime = $openingtimes->friday;
    }
    if (!is_object($openingtimes->saturday)) {
        $satOpeningTime = $openingtimes->saturday;
    }
    if (!is_object($openingtimes->sunday)) {
        $sunOpeningTime = $openingtimes->sunday;
    }
    $label_routing = $info->labelRouting;
   
    $insertDataArray[] = "('" . $name . "', "
            . "'" . $comapny  . "',"
            . " '" . $addressline1 . "', '" . $city . "','" . str_replace(" ", "", $postcode) . "', '" . $countryIso . "', '" . $statuscode . "', "
            . "'" . $statusdescription . "', '" . $latitude . "', '" . $longitude . "',"
            . " '" . mb_convert_encoding($monOpeningTime,'UTF-8','ASCII') . "',"
            . " '" . $tueOpeningTime . "', '" . $wedOpeningTime . "',"
            . " '" . $thuOpeningTime . "', '" . $friOpeningTime . "', "
            . "'" . $satOpeningTime . "', "
            . "'" . $sunOpeningTime . "', "
            . "'". $label_routing ."', "
            . "'" . $branchId . "')";
}
if (count($insertDataArray) > 0) {
    
     $mysql_access = mysqli_connect(SETTING_DB_SERVER, SETTING_DB_USER, SETTING_DB_PASSWORD) or die(mysql_error());
    if (!$mysql_access) {
        die('Could not connect: ' . mysql_error());
    }
    mysqli_select_db($mysql_access, SETTING_DB_DATABASE);

    echo $sqlTrancate1 = "truncate table parcelforu_pickup_point;";
    mysqli_query($mysql_access, $sqlTrancate1);
    
    $insertIntoQuery = "INSERT INTO `parcelforu_pickup_point` (`name`,`company`,`address_line_1`,`city`,`postcode`,`country_iso`,`statuscode`,`status_description`,`latitude`,`longitude`,`mon`,`tue`,`wed`,`thu`,`fri`,`sat`,`sun`,`label_routing`,`branch_id`) VALUES ";
    $insertQuery = $insertIntoQuery . implode(',', $insertDataArray) . ';';
    mysqli_query($mysql_access,$insertQuery);
   
    /*
    $p4utrancate = new parcelforuPickupPointFilter();
    $p4utrancate->deletep4uRouting();
    $p4upickuppoint = new parcelforuPickupPointFilter();
    $queryJoin = $insertQuery . implode(",", $insertDataArray);
    $p4upickuppoint->insertData($queryJoin);*/
}

print_r($insertDataArray);
exit;
?>