<?php

require_once(__DIR__ . "/../includes/settings/config.inc.php");
include_classes([
    'tracking_data.class'
  	]);

include_classes([
    'SFTP'
    ], '3rdparty/Net');

/*include_classes([
    'sessionmanager.class'
    ], 'autoload');

require_once(SETTING_DIR_REMOTE . "includes/autoload/sessionmanager.class.php");
require_once(SETTING_DIR_REMOTE . "main/Net/SFTP.php");
*/
$connectCustom = mysqli_connect(SETTING_DB_SERVER, SETTING_DB_USER, SETTING_DB_PASSWORD);
mysqli_select_db(SETTING_DB_DATABASE, $connectCustom) or die(mysqli_error());

CreateTrackingFiles();

function AddHeader() {
    $header = "serialNumber@bagId@trackingNumber@switchToNewTrackingNumber@eventCode@eventDesc@eventTime@eventCountry@eventState@eventCity@eventDistrict@eventPostCode@specialOperDesc@status";
    return $header;
}

function CreateTrackingFiles() {
    //$connectCustom = createConnection();
    $cr = "\r\n";
    $separator = "@";
    $csv = "";
    $AddHeader = "";

    $Date = date("Y-m-d H:i:s");
    $oneHourLess = date('Y-m-d H:i:s', strtotime($Date) - 3600);
    $fifteenMinute = date('Y-m-d H:i:s', strtotime($oneHourLess) + 900);
    
    $query =   "SELECT 
                tdh.id,
                c.user_id,
                c.mawb,
                c.awb,
                c.hawb,
                c.service_id
                tdh.date_created,
                tdh.carrier_desc,
                tdh.status_code_id,
                FROM
                consignment c
                INNER JOIN
                tracking_data tdh ON c.id = tdh.consignment_id
                WHERE
                c.user_id = '2461' and tdh.description != 'Hayes Sorting Centre' and tdh.date_added >='". $oneHourLess. "' and
                tdh.date_added <='". $fifteenMinute."'";
        
    $result = mysql_query($query);
      
    if (mysql_num_rows($result) > 0) {
        $count = 1;
        while ($row = mysql_fetch_array($result)) {
            $id = $row['id'];
            $idArray[] = $id;
            
            $status_code = strtolower($row['status_code_id']);
            $description = strtolower($row['description']);
            
            $code = '';
            if ($status_code == 'received') {
                if ($description == 'royal mail received') {
                    $code = 'PIC';
                } else {
                    $code = 'ARR';
                }
            }
            if ($status_code == 'arrived london heathrow' && $description == 'awaiting collection from airline') {
                $code = 'ARR';
            }
            if ($status_code == 'customs clearance' && $description == 'london heathrow') {
                $code = 'PCC';
            }
            if ($status_code == 'ready for delivery') {
                $code = 'OFD';
            }
            
            if ($status_code == 'collected') 
            {
                if($row['handling'] == 'OWE48TRAC' )
                {
                    $code = '1DEL';
                }
                else 
                {
                    $code = 'OFD';
                }
            }
            
            if ($status_code == 'dispatched') {
                if ($description == 'item handed over to postal operator') {
                    $code = 'PIC';
                } else {
                    $code = 'DEP';
                }
            }

            if ($status_code == 'returned' || $status_code == 'returned to sender') {
                $code = 'RTS';
            }
            if ($status_code == 'delivered') {
                $code = '0DEL';
            }
            if ($status_code == "we're expecting it" || $status_code == 'in transit' || $status_code == "we've got it" || $status_code == 'scanned' || $status_code == '0') {
                $code = 'SCN';
            }
            if ($status_code == 'delivery attempted') {
                $code = '0ATT';
            }
            if ($status_code == 'pending') {
                $code = '1ATT';
            }
            
            if(($code == 'RTS' || $code == 'OFD') && substr($row['awb'], -3) == 'GBL')
            {
                continue;
            }
            
            if(($code == 'RTS') && substr($row['awb'],0, 2) == '0B')
            {
                continue;
            }
                    
            $csv .=  "EBAYP" . str_pad(LicencePlate::getLicencePlateNumber('EBAY_OC'), 10, 0, 0) . $separator; // serial number
            $csv .= '' . $separator; //BAG ID
            $csv .= $row['awb'] . $separator;

            $csv .= '' . $separator; //switch to new tracking number   
            $csv .= $code . $separator; // event code 
            $csv .= str_replace(',', ' ', $row['status_code'] . '-' . $row['description']) . $separator; // event description

            $date_utc = new DateTime($row['date_created'], new DateTimeZone("UTC"));
            $csv .= $date_utc->format(DateTime::ISO8601) . $separator; // event time                     
            $csv .= 'GB' . $separator;
            $csv .= '' . $separator; //event state
            $csv .= '' . $separator; //event city
            $csv .= '' . $separator; //event district
            $csv .= '' . $separator; //event postcode
            $csv .= '' . $separator; //Special oper description
            $csv .= '0'; //status                    
            $csv .= $cr;
            
          //  $trackingDataHistory = new TrackingData($id);
          //  $trackingDataHistory->setHub('1');
          //  $trackingDataHistory->save();
            
            if($count >= '80000')
            {
                $fName = date("YmdHis") . '_OWE_' . ($count-1) . '_' . str_pad(LicencePlate::getLicencePlateNumber('EBAY_OC_FILE'), 2, 0, 0);
                $implodeArray = implode("','", $idArray);
        
                if (!mysqli_ping($dbConnection)) 
                {
                    $dbConnection = DbAccess3::connect(SETTING_DB_SERVER, SETTING_DB_USER, SETTING_DB_PASSWORD, SETTING_DB_DATABASE);
                }

                $query = "update tracking_data_history set hub = 1, pod_image = '".$fName."' where id in ('".$implodeArray."')";

                DbAccess3::runQuery($query);
                
                $path = SETTING_DIR_ASSETS . "ocData/shipment-data-in/";
                $file_path = $path . $fName.'.csv';
                
                $file_handle = @fopen($file_path, 'w');
                fwrite($file_handle, AddHeader() . "\r\n");

                fwrite($file_handle, $csv);
                fclose($file_handle);

                $ssh = new Net_SFTP("sftp.orangeconnex.com", 22);
                if (!$ssh->login('ocowesftp', "wmWWg1!VXbxDOyN2^I")) 
                {
                    mail("itsupport@oneworldexpress.com", "OC LOGIN FAILED", "OC LOGIN FAILED" . $fName, 'From: kiran.iftikhar@oneworldexpress.com');
                } 
                else 
                {
                    $remotefile = "/TRACKING_PENDING/" . $fName.'.tmp';
                     if ($ssh->put($remotefile, $file_path, NET_SFTP_LOCAL_FILE) == true) {
					 $ssh->rename($remotefile, "/TRACKING_PENDING/".$fName . ".csv") ;
                    'Upload Successful!';
                    }
                } 
                $count = 1 ;
                $csv = '';
                $idArray = array();
            }
            else
            {
                $count++;
            }
        }
       
        $fName = date("YmdHis") . '_OWE_' . ($count-1) . '_' . str_pad(LicencePlate::getLicencePlateNumber('EBAY_OC_FILE'), 2, 0, 0);
        $implodeArray = implode("','", $idArray);
        
        if (!mysqli_ping($dbConnection)) 
        {
            $dbConnection = DbAccess3::connect(SETTING_DB_SERVER, SETTING_DB_USER, SETTING_DB_PASSWORD, SETTING_DB_DATABASE);
        }

        $query = "update tracking_data_history set hub = 1, pod_image = '".$fName."' where id in ('".$implodeArray."')";

        DbAccess3::runQuery($query);
                
        $path = SETTING_DIR_ASSETS . "ocData/shipment-data-in/";
        if (!file_exists($path))
            @mkdir($path, 0777);

        $file_path = $path . $fName.'.csv';

        chmod($path, 0777);

        $file_handle = @fopen($file_path, 'w');
        fwrite($file_handle, AddHeader() . "\r\n");
        fwrite($file_handle, $csv);
        fclose($file_handle);

        $ssh = new Net_SFTP("sftp.orangeconnex.com", 22);
        if (!$ssh->login('ocowesftp', "wmWWg1!VXbxDOyN2^I")) {
            mail("itsupport@oneworldexpress.com", "OC LOGIN FAILED", "OC LOGIN FAILED" . $fName, 'From: kiran.iftikhar@oneworldexpress.com');
        } else {
            $remotefile = "/TRACKING_PENDING/" . $fName.'.tmp';
            if ($ssh->put($remotefile, $file_path, NET_SFTP_LOCAL_FILE) == true) 
            {
                $ssh->rename($remotefile, "/TRACKING_PENDING/".$fName . ".csv") ;
                'Upload Successful!';
            }
        }
    }
}
mysqli_close($dbConnection);
?>
