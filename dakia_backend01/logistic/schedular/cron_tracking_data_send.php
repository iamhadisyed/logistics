<?php 
require_once(__DIR__ . "/../includes/settings/config.inc.php");
    
    include_classes([
        'consignment.class',
        'consignmentfilter.class',
        'tracking_data.class',
        'trackingdatatfilter.class', 
        'useraccount.class',
        'useraccountfilter.class',
        'user.class',
        'userfilter.class'
  	]);
    include_classes([
        'SFTP.php'
        ], '3rdparty/Net');
  
$dbConnection = DbAccess3::connect(SETTING_DB_SERVER, SETTING_DB_USER, SETTING_DB_PASSWORD, SETTING_DB_DATABASE);
if(!$dbConnection)
{
    mail("itsupport@oneworldexpress.com", "Send Tracking Database Connection", "Send Tracking Database Connection Failed", 'From: kiran.iftikhar@oneworldexpress.com');
}

function AddHeader() 
{
    $header = "serialNumber@bagId@trackingNumber@switchToNewTrackingNumber@eventCode@eventDesc@eventTime@eventCountry@eventState@eventCity@eventDistrict@eventPostCode@specialOperDesc@status";
    return $header;
}

CreateTrackingFiles();
function CreateTrackingFiles()
{
    $startDate = date("Y-m-d H:i:s", strtotime('-1 hour'));
    $endDate = date("Y-m-d H:i:s"); 
    $cr = "\r\n";
    $separator = ",";
    
    $query = "SELECT id, user_account from user_account where send_tracking_data = '1'";
    $result = DbAccess3::runQuery($query);
    if (mysqli_num_rows($result) > 0) 
    {
        while ($row = mysqli_fetch_array($result)) 
        {
            $accountId = $row['id'];
            $accountName = $row['user_account'];
            //echo $accountName; 
            
            /*$ftpCredentials = $row['ftp_credentials'];
            $ftpCred = json_decode($ftpCredentials);
            
            $userName = $ftpCred->userName;
            $password = $ftpCred->password;
            $ipAddress = $ftpCred->url;
            */
           
            $path = SETTING_DIR_ASSETS . "user_data/".strtolower($accountName)."/data_tracking/";
            
            if (!file_exists($path))
                @mkdir($path, 0777, TRUE);
            
            $query = "SELECT t.tracking_number, t.status_code_id, t.carrier_desc, t.date_created, t.track_point 
                      from 
                      consignment c 
                      INNER JOIN user u ON c.user_id = u.id
                      INNER JOIN tracking_data t ON t.tracking_number = c.awb
                      where u.user_account_id = ".$accountId." AND 
                      t.date_added >= '".$startDate."' AND t.date_added <= '".$endDate."' and c.awb != '' order by t.id desc";
            
            echo $query;// die;
            $rs = DbAccess3::runQuery($query);
            
            if (mysqli_num_rows($rs) > 0) 
            {
                while ($innerrow = mysqli_fetch_array($rs)) 
                {
                    $csv .= $innerrow['tracking_number'] . $separator;
                    $csv .= $innerrow['status_code_id'] . $separator;
                    $csv .= $innerrow['track_point'] . $separator;
                    $csv .= $innerrow['carrier_desc'] . $separator;
                    $csv .= $innerrow['date_created'] . $separator;
                    $csv .= $cr;
                }
                $fName = "TRACKINGEVENT-" . date("Ymd_His") . ".csv";
                $file_path = $path . $fName;
                                
                chmod($path, 0777);
                
                $file_handle = @fopen($file_path, 'w');
                fwrite($file_handle, AddHeader() . $cr);
                fwrite($file_handle, $csv);
                fclose($file_handle);
                
                if($accountName == 'IFORCE')
                {
                    $backupPath = SETTING_DIR_ASSETS . "user_data/".strtolower($accountName)."/data_processed/";
                    if (!file_exists($backupPath))
                        @mkdir($backupPath, 0777, TRUE);
                    $backup_file_path = $backupPath . $fName;
                    chmod($backupPath, 0777);
                    $file_handle = @fopen($backup_file_path, 'w');
                    fwrite($file_handle, AddHeader() . $cr);
                    fwrite($file_handle, $csv);
                    fclose($file_handle);
                }                
                $csv = '';                
            }            
        }
    }
}
mysqli_close($dbConnection);
?>
