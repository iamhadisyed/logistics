<?php

error_reporting(1);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
require_once(__DIR__ . "/../includes/settings/config.inc.php");
require_once(SETTING_DIR_REMOTE . "includes/3rdparty/Net/SFTP.php");


include_classes([
    'ftpimplicitssl'
],'3rdparty');





ini_set('memory_limit', -1);
ini_set('max_execution_time', -1);
error_reporting(E_ALL);
ini_set('display_errors', '1');

$localFileName = SETTING_DIR_ASSETS."warenpostgaz/";


$dbConnection =	DbAccess3::connect(SETTING_DB_SERVER, SETTING_DB_USER, SETTING_DB_PASSWORD, SETTING_DB_DATABASE);
$mysql_access = mysqli_connect(SETTING_DB_SERVER, SETTING_DB_USER, SETTING_DB_PASSWORD) or die(mysql_error());
if (!$mysql_access) {
    die('Could not connect: ' . mysql_error());
}
mysqli_select_db($dbConnection, SETTING_DB_DATABASE);


$sqlTrancate2 = "DELETE FROM warenpost_gazetteer;";
mysqli_query($dbConnection, $sqlTrancate2);



// for Reamus Product Service
//$file =  SETTING_DIR_ASSETS."hermesgaz/POSFILE100_2019-10-30_19-32-41.TXT"; //$localFilePath;
echo $file = $localFileName . "B2009245.DAT";
$f = fopen($file, "r");
$ln = 0;
$countrecord = 0;
$PostcodeRecordInsert = "INSERT INTO `warenpost_gazetteer` (`alort`,`schluessel`,`hnrvon`,`hnrbis`,`status`,`hnr_1000`,`stverz`,`name_sort`,`name_umlauts`,`street_abbreviation`,`house_number_type`,`postcode`,`street_code`,`town_code`) VALUES";

while ($line = fgets($f)) {
    ++$ln;
    if ($line === FALSE)
        print ("FALSE\n");
    else {
        if ($ln > 1) {
            $PostcodeRecordSql = "";
            $strlen = strlen($line);
            $recordtype = substr($line, 0, 2);
            if ($recordtype == "SB") {
                $alot = substr($line, 17, 8); // Alpha no of destination of last line of addresss
                $scluessel = substr($line, 25, 11); // Wide Street Code
                $hnrvon = substr($line, 36, 8); // first house no of street section
                $hnrbis = substr($line, 44, 8); // last house no of street section
                 
                /*
                 * (=G) : valid street section record
                    (=S) : key change: 1 street section -> 1
                    street section
                    (=N) : key change: 1 street section ->
                    several street sections
                    (=W): street section removed without
                    new entry
                 */
                $status = substr($line, 52, 1);
                $hnr1000 = substr($line, 53, 1); //1000th position of the house number for freight coding
                
                /*
                 * (=1) : In this town there is only one
                    postal code for delivery, i.e., of types
                    "6" or "7"
                    (=2) : This is one of the more than 210
                    towns with several delivery postal
                    codes.
                    corresponds to field PL06
                 */
                $stverz = substr($line, 54, 1);
                $name_sort = substr($line, 55, 46);
                $name_umlauts = substr($line, 101, 46);
                $street_abbreviation = substr($line, 147, 22);
                $hnr_type = substr($line, 170, 1);
                $postcode = substr($line, 171, 5);
                $street_code = substr($line, 176, 3);
                $town_code = substr($line, 179, 3);
                
                $countrecord++;
                    
                    $PostcodeRecordSql = $PostcodeRecordInsert . " ('" . trim($alot) . "', '" . trim($scluessel) . "', '" . trim($hnrvon) . "', '" . trim($hnrbis) . "', '" . trim($status) . "', '" . trim($hnr1000) . "', '" . trim($stverz) . "', '" . trim($name_sort) . "', '" . trim($name_umlauts) . "', '" . trim($street_abbreviation) . "', '" . trim($hnr_type) . "', '" . trim($postcode) . "', '" . trim($street_code) . "', '" . trim($town_code) . "');";
                    if (!mysqli_ping($dbConnection)) {
                        $dbConnection = DbAccess3::connect(SETTING_DB_SERVER, SETTING_DB_USER, SETTING_DB_PASSWORD, SETTING_DB_DATABASE);
                    }
                    mysqli_query($dbConnection, $PostcodeRecordSql);
                }
            else {
                continue;
            }
            } 
            
    }
}
echo $countrecord ;





mysqli_close($mysql_access);
die;
