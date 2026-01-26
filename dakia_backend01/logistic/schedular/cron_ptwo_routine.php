<?php
ini_set('max_execution_time', '-1');
require_once(__DIR__ . "/../includes/settings/config.inc.php");

include_classes([
    'SFTP'
],'3rdparty/Net');
include_classes([
    'ftpimplicitssl'
],'3rdparty');

error_reporting(1);
ini_set('display_errors', 1);

$path = SETTING_DIR_ASSETS . "ptwo_routine/";
if (!file_exists($path))
    @mkdir($path, 0777, true);


$dbConnection =	DbAccess3::connect(SETTING_DB_SERVER, SETTING_DB_USER, SETTING_DB_PASSWORD, SETTING_DB_DATABASE);
$mysql_access = mysqli_connect(SETTING_DB_SERVER, SETTING_DB_USER, SETTING_DB_PASSWORD) or die(mysql_error());
if (!$mysql_access) {
    die('Could not connect: ' . mysql_error());
}
mysqli_select_db($dbConnection, SETTING_DB_DATABASE);



$url = "213.214.19.210";
$username = "P000_Oneworld";
$password = "OWE1234!";

$ftpconnection = new Net_SFTP($url, 22222);
$ftpconnection->login($username, $password);

$remotefile = "/Export/";

$getAllFiles= $ftpconnection->_list($remotefile);
$todayDate = "20210215"; //date("Ymd"); 
echo $todayFileName = "Strukturdaten_" . $todayDate .".csv";
$localFileName = SETTING_DIR_ASSETS . 'ptwo_routine/' . $todayFileName;
if (count($getAllFiles) > 0) {
    foreach ($getAllFiles as $keyFile => $valueFile) {

        $filename = $valueFile['filename'];
        if (in_array($filename, array('.', '..')))
            continue;
        
        if($filename = $todayFileName){
            $remote_file = $remotefile . $filename;
            $localFileName = SETTING_DIR_ASSETS . 'ptwo_routine/' . $filename;
            $ftpconnection->get($remote_file, $localFileName);
        }
    }
}

if(file_exists($localFileName)){
    $sqlTrancate1 = "DELETE FROM ptwo_routine;";
    mysqli_query($dbConnection,$sqlTrancate1);


$csvColumn = "logistik,postcode,city,district,street,house_number_from,additional_from,house_number_to,addtional_to,house_number_filter,sortinfo";
echo $load_data_sql = "LOAD DATA LOCAL INFILE '" . $localFileName . "' INTO TABLE `ptwo_routine` CHARACTER SET latin1 FIELDS ENCLOSED BY '\"' 
                            TERMINATED BY ';' LINES TERMINATED BY '\r\n' IGNORE 1 LINES ( ".$csvColumn."  ) ";

$res = DbAccess3::runQueryWithError($load_data_sql);
mail("itsupport@oneworldexpress.com","P2 Routine Imported", $localFileName);
}
exit;

?>