<?php

error_reporting(0);
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
require_once(__DIR__ . "/../includes/settings/config.inc.php");
require_once(SETTING_DIR_REMOTE . "includes/3rdparty/Net/SFTP.php");


include_classes([
    'ftpimplicitssl'
],'3rdparty');





ini_set('memory_limit', -1);
ini_set('max_execution_time', -1);
error_reporting(E_ALL);
ini_set('display_errors', '1');

$localFileName = SETTING_DIR_ASSETS."hermesgaz/";
$date = date('Y-m-d',strtotime("-1 days"));//date("Y-m-d");
echo $file_start = "POSFILE100_" . $date;


$ftpconnection = new Net_SFTP("sftp.hermescloud.co.uk");
$ftpconnection->login("client.oneworldexpress", "WFdWNbqyzV5n4gEy");
$remoteFile = "/Out/";

$getAllFiles = $ftpconnection->_list($remoteFile);
$localFilePath = "";
if (count($getAllFiles) > 0) {
    foreach ($getAllFiles as $keyFile => $valueFile) {
        $filename = $valueFile['filename'];
        if (strpos($filename, $file_start) !== false) {
            $localFilePath = $localFileName . $filename;
            $remoteFilePath = $remoteFile . $filename;
            $ftpconnection->get($remoteFilePath, $localFilePath);
        }
    }
}
mail("mruga@oneworldexpress.com", "Hermes Gaz", $localFilePath);
echo $localFilePath;
if($localFilePath == ""){
    echo "no file found";
    die;
}

$dbConnection =	DbAccess3::connect(SETTING_DB_SERVER, SETTING_DB_USER, SETTING_DB_PASSWORD, SETTING_DB_DATABASE);
$mysql_access = mysqli_connect(SETTING_DB_SERVER, SETTING_DB_USER, SETTING_DB_PASSWORD) or die(mysql_error());
if (!$mysql_access) {
    die('Could not connect: ' . mysql_error());
}
mysqli_select_db($dbConnection, SETTING_DB_DATABASE);

$sqlTrancate3 = "DELETE FROM hermes_next_day_depot";
mysqli_query($dbConnection,$sqlTrancate3);

$sqlTrancate1 = "DELETE FROM hermes_postcode_record;";
mysqli_query($dbConnection,$sqlTrancate1);

$sqlTrancate2 = "DELETE FROM sort_key_record;";
mysqli_query($dbConnection, $sqlTrancate2);


echo $localFilePath;

// for Reamus Product Service
//$file =  SETTING_DIR_ASSETS."hermesgaz/POSFILE100_2019-10-30_19-32-41.TXT"; //$localFilePath;
echo $file = $localFilePath;
$f = fopen($file, "r");
$ln = 0;
$counthermesrecord = 0;
$countsortrecord = 0;
$hermesNextDayInsert = "INSERT INTO `hermes_next_day_depot` (`depot_code`, `nextday_depot_code`) VALUES";
$hermesPostcodeRecordInsert = "INSERT INTO `hermes_postcode_record` (`fullpostcode`,`pos_pcd_postcode_excluded_indicator`,`sort_level_key`,`next_day_service`,`delivery_proof_service`, `date_update`) VALUES";
$sortKeyRecordInsert = "INSERT INTO `sort_key_record`(`pos_sld_sort_level_key`,`pos_sld_level_1_type`,`pos_sld_level_1_name`,`pos_sld_level_1_code`,`pos_sld_level_2_type`,`pos_sld_level_2_name`,`pos_sld_level_2_code`,`pos_sld_level_3_type`,`pos_sld_level_3_name`,`pos_sld_level_3_code`,`pos_sld_level_4_type`,`pos_sld_level_4_name`,`pos_sld_level_4_code`,`pos_sld_level_5_type`,`pos_sld_level_5_name`,`pos_sld_level_5_code`,`pos_sld_hermes_barcode_1_to_7`,`pos_sld_hermes_barcode_seq_key`, `date_update`) VALUES";

while ($line = fgets($f)) {
    ++$ln;
    if ($line === FALSE)
        print ("FALSE\n");
    else {
        if ($ln > 1) {
            $hermesPostcodeRecordSql = "";
            $hermesNextDaySql = "";
            $sortKeySql = "";
            $strlen = strlen($line);
            $recordtype = substr($line, 0, 2);
            if($recordtype == "73"){
                $depotCode = substr($line, 7, 2);
                $nextDayDepotCode = substr($line, 9, 2);
                $hermesNextDaySql = $hermesNextDayInsert . "('".$depotCode."' , '".$nextDayDepotCode."')";
                if (!mysqli_ping($dbConnection)) {
                    $dbConnection = DbAccess3::connect(SETTING_DB_SERVER, SETTING_DB_USER, SETTING_DB_PASSWORD, SETTING_DB_DATABASE);
                }
                mysqli_query($dbConnection, $hermesNextDaySql);
            }
            else if ($recordtype == "59") {
                $modid = substr($line, 10, 3);
                
                if($modid == "004"){
                    $counthermesrecord++;
                    $fullPostcode = substr($line, 13, 8);
                    //$inwardPostcode = substr($line,18,3);
                    $postcodeExcludedIndicator = substr($line, 21, 1);
                    $sortLevelkey = substr($line, 58, 8);
                    $deliveryServiceNextDayFlag = substr($line, 163, 1);
                    $signDeliveryFlag = substr($line, 214, 1);

                    $hermesPostcodeRecordSql = $hermesPostcodeRecordInsert . " ('" . trim($fullPostcode) . "', '" . trim($postcodeExcludedIndicator) . "', '" . trim($sortLevelkey) . "', '" . trim($deliveryServiceNextDayFlag) . "','".trim($signDeliveryFlag)."','".$date."');";
                    if (!mysqli_ping($dbConnection)) {
                        $dbConnection = DbAccess3::connect(SETTING_DB_SERVER, SETTING_DB_USER, SETTING_DB_PASSWORD, SETTING_DB_DATABASE);
                    }
                    mysqli_query($dbConnection, $hermesPostcodeRecordSql);
                }

            } else if ($recordtype == "60") {
                 $countsortrecord++;
                $posSldSortLevelKey = substr($line, 10, 8);
                $posSldLevel1Type = substr($line, 18, 8);
                $posSldLevel1Name = substr($line, 26, 8);
                $posSldLevel1Code = substr($line, 34, 8);
                $posSldLevel2Type = substr($line, 42, 8);
                $posSldLevel2Name = substr($line, 50, 8);
                $posSldLevel2Code = substr($line, 58, 8);
                $posSldLevel3Type = substr($line, 66, 8);
                $posSldLevel3Name = substr($line, 74, 8);
                $posSldLevel3Code = substr($line, 82, 8);
                $posSldLevel4Type = substr($line, 90, 8);
                $posSldLevel4Name = substr($line, 98, 8);
                $posSldLevel4Code = substr($line, 106, 8);
                $posSldLevel5Type = substr($line, 114, 8);
                $posSldLevel5Name = substr($line, 122, 8);
                $posSldLevel5Code = substr($line, 130, 8);
                $posSldHermesBarcode1to7 = substr($line, 138, 7);
                $posSldBarcodeSeqKey = substr($line, 145, 7);

                $sortKeySql = $sortKeyRecordInsert . " ( '" . trim($posSldSortLevelKey) . "', "
                        . "'" . trim($posSldLevel1Type) . "', "
                        . "'" . trim($posSldLevel1Name) . "', "
                        . "'" . trim($posSldLevel1Code) . "', "
                        . "'" . trim($posSldLevel2Type) . "', "
                        . "'" . trim($posSldLevel2Name) . "', "
                        . "'" . trim($posSldLevel2Code) . "', "
                        . "'" . trim($posSldLevel3Type) . "', "
                        . "'" . trim($posSldLevel3Name) . "', "
                        . "'" . trim($posSldLevel3Code) . "', "
                        . "'" . trim($posSldLevel4Type) . "', "
                        . "'" . trim($posSldLevel4Name) . "', "
                        . "'" . trim($posSldLevel4Code) . "', "
                        . "'" . trim($posSldLevel5Type) . "', "
                        . "'" . trim($posSldLevel5Name) . "', "
                        . "'" . trim($posSldLevel5Code) . "', "
                        . "'" . trim($posSldHermesBarcode1to7) . "', "
                        . "'" . trim($posSldBarcodeSeqKey) . "'," 
                        . "'" . trim($date)."'"
                        . " );";
                if (!mysqli_ping($dbConnection)) {
                        $dbConnection = DbAccess3::connect(SETTING_DB_SERVER, SETTING_DB_USER, SETTING_DB_PASSWORD, SETTING_DB_DATABASE);
                    }
                mysqli_query($dbConnection, $sortKeySql);
            } else {
                continue;
            }
            
        }
    }
}
echo $counthermesrecord . "  ---- " . $countsortrecord;





mysqli_close($mysql_access);
die;
