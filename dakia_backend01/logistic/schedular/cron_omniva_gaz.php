<?php
ini_set('max_execution_time', '-1');
require_once(__DIR__ . "/../includes/settings/config.inc.php");


error_reporting(1);
ini_set('display_errors', 1);

//$csv = "C:/xampp/htdocs/smarttrackoptimization/_assets/omniva_gaz/locations_20210215.csv";
//$a = mb_convert_encoding("Cēsu novads", 'UTF-16LE', 'UTF-8');
//print_r($a);
//exit;

// Initialize a file URL to the variable  
 $url = "https://www.omniva.lv/locations.json";
$info = pathinfo($url); 
  
if ($info["extension"] == "json") { 
      
    /* Use file_get_contents() function 
    to get the file from url and use  
    file_put_contents() function to save 
    the file by using base name */   
    
    $jsonGaz =  file_get_contents($url) ;
    $arrayGaz = json_decode($jsonGaz, false, 512, JSON_UNESCAPED_UNICODE);
    $omnivaLocationArray = array();
    if(count($arrayGaz) > 0){
       
        foreach($arrayGaz as $row){
          $omnivaLocationArray[] =   "(
               '" . $row->ZIP . "','"
                  . remove_utf8_bom($row->NAME) . "','"
                  . $row->TYPE . "','"
                  . $row->A0_NAME . "','"
                  . $row->A1_NAME . "','"
                  . $row->A2_NAME . "','"
                  . $row->A3_NAME . "','"
                  . $row->A4_NAME . "','"
                  . $row->A5_NAME . "','"
                  . $row->A6_NAME . "','"
                  . $row->A7_NAME . "','"
                  . $row->A8_NAME . "','"
                  . $row->X_COORDINATE . "','"
                  . $row->Y_COORDINATE . "','"
                  . $row->SERVICE_HOURS . "','"
                  . $row->TEMP_SERVICE_HOURS . "','"
                  . $row->TEMP_SERVICE_HOURS_UNTIL . "','"
                  . $row->TEMP_SERVICE_HOURS_2 . "','"
                  . $row->comment_est . "','"
                  . $row->comment_eng . "','"
                  . $row->comment_rus . "','"
                  . $row->comment_lav . "','"
                  . $row->comment_lit . "','"
                  . $row->MODIFIED . "')";
        }
    }
} 
else {
    mail("mruga@oneworldexpress.com","Omniva Gaz", "Unable to download file");
}

if(count($omnivaLocationArray) > 0){
    
    //$dbConnection =	DbAccess3::connect(SETTING_DB_SERVER, SETTING_DB_USER, SETTING_DB_PASSWORD, SETTING_DB_DATABASE);
    $mysql_access = mysqli_connect(SETTING_DB_SERVER, SETTING_DB_USER, SETTING_DB_PASSWORD) or die(mysql_error());
    if (!$mysql_access) {
        die('Could not connect: ' . mysql_error());
    }
    mysqli_select_db($mysql_access, SETTING_DB_DATABASE);

    echo $sqlTrancate1 = "DELETE FROM omniva_location;";
    mysqli_query($mysql_access, $sqlTrancate1);
    
    
  echo $insertQuery = 'INSERT INTO `omniva_location` (`zip`,`postoffice_name`,`type`,`country`,`county`,`town`,`village`,`small_place`,`street`,`area`,`house_no`,`apartment_no`,`x_coordinate`,`y_coordinate`,`service_hours`,`temp_service_hours`,`temp_service_hours_until`,`temp_service_hours_2`,`comment_est`,`comment_eng`,`comment_russ`,`comment_latvia`,`comment_lith`,`modified`) 
            VALUES ' . implode(',', $omnivaLocationArray) . ';';
    mysqli_query($mysql_access,$insertQuery);
    
//$csvColumn = "zip, postoffice_name, type, country, county, town, village, small_place, street, area, house_no, apartment_no, x_coordinate, y_coordinate,service_hours,temp_service_hours,temp_service_hours_until,temp_service_hours_2,comment_est,comment_eng,comment_russ,comment_latvia,comment_lith,modified";
//echo $load_data_sql = "LOAD DATA LOCAL INFILE '" . $localFileName . "' INTO TABLE `omniva_location` FIELDS ENCLOSED BY '\"' 
//                            TERMINATED BY ';' LINES TERMINATED BY '\n' IGNORE 1 LINES ( ".$csvColumn."  ) ";

//$res = DbAccess3::runQueryWithError($load_data_sql);
//mail("itsupport@oneworldexpress.com","P2 Routine Imported", $localFileName);
}
exit;

function remove_utf8_bom($text)
{
    $bom = pack('H*','EFBBBF');
    $text = preg_replace("/^$bom/", '', $text);
    return $text;
}
?>