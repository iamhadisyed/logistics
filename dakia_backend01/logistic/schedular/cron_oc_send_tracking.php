<?php

require_once(__DIR__ . "/../includes/settings/config.inc.php");    
    include_classes([
        'consignment.class',
        'consignmentfilter.class',
        'tracking_data.class',
        'trackingdatatfilter.class',
        'user.class',
        'userfilter.class', 
        'licenceplate.class',
  	]);
    
include_classes([
    'SFTP'
    ], '3rdparty/Net');

$dbConnection = DbAccess3::connect(SETTING_DB_SERVER, SETTING_DB_USER, SETTING_DB_PASSWORD, SETTING_DB_DATABASE);

CreateTrackingFiles();

function AddHeader() 
{
    $header = "serialNumber@bagId@trackingNumber@switchToNewTrackingNumber@eventCode@eventDesc@eventTime@eventCountry@eventState@eventCity@eventDistrict@eventPostCode@specialOperDesc@status";
    return $header;
}

function CreateTrackingFiles() 
{
    $cr = "\r\n";
    $separator = "@";
    $csv = "";
    $AddHeader = "";

    $Date = date("Y-m-d H:i:s");
    $oneHourLess = date('Y-m-d H:i:s', strtotime($Date) - 3600);
    $fifteenMinute = date('Y-m-d H:i:s', strtotime($oneHourLess) + 900);
    
    $query =   "SELECT 
                td.id,
                c.service_id,
                c.awb,
                td.date_created,
                td.carrier_desc,
                td.status_code_id,
                td.track_point
                FROM
                consignment c
                INNER JOIN
                tracking_data td ON c.awb = td.tracking_number
                INNER JOIN 
                user u
                ON c.user_id = u.id
                
                WHERE
                u.user_account_id = '2356' and td.carrier_desc not in ('Arrived at Sort Facility Hayes - GBR','Departed Facility in Hayes - GBR') 
                and td.date_added >='2020-02-03 16:43:44' and
                td.date_added <='2020-02-04 10:39:07'";
    
    $result = DbAccess3::runQuery($query);

    if (mysqli_num_rows($result) > 0) 
    {
        $count = 1;
        while ($row = mysqli_fetch_array($result)) 
        {
            $id = $row['id'];
            $idArray[] = $id;
            
            $status_code_id = strtolower($row['status_code_id']);
            $description = strtolower($row['carrier_desc']);
           
            $code = '';
            if ($status_code_id == '148') 
            {
                if ($description == 'royal mail received') 
                {
                    $code = 'PIC';
                } 
                else 
                {
                    $code = 'ARR';
                }
            }
            
            if ($status_code_id == '146' && $description == 'awaiting collection from airline') 
            {
                $code = 'ARR';
            }
            
            if ($status_code_id == '117' && $description == 'customs clearance') 
            {
                $code = 'PCC';
            }
            
            if ($status_code_id == '111') 
            {
                $code = 'OFD';
            }
            
            if ($status_code_id == '140') 
            {
                if($row['service_id'] == '12' )
                {
                    $code = '1DEL';
                }
                else 
                {
                    $code = 'OFD'; // for whistl
                }
            }
            
            if ($status_code_id == '126') 
            {
                if ($description == 'item handed over to postal operator') 
                {
                    $code = 'PIC';
                } 
                else 
                {
                    $code = 'DEP';
                }
            }

            if ($status_code_id == '138') 
            {
                $code = 'RTS';
            }
            if ($status_code_id == '121') 
            {
                $code = '0DEL';
            }
            if ($status_code_id == "144" || $status_code_id == '137' || $status_code_id == '161' || $status_code_id == '0') 
            {
                $code = 'SCN';
            }
            if ($status_code_id == '123') 
            {
                $code = '0ATT';
            }
            if ($status_code_id == '135') 
            {
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
            $csv .= str_replace(',', ' ', $row['status_code_id'] . '-' . $row['carrier_desc']) . $separator; // event description

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
            
            if($count >= '80000')
            {
                sendTracking($csv, $count, $idArray);
                $count = 1 ;
                $csv = '';
                $idArray = array();
            }
            else
            {
                $count++;
            }
        }       
        sendTracking($csv, $count, $idArray);
    }
}

function sendTracking($csv, $count, $idArray)
{
    $fName = date("YmdHis") . '_OWE_' . ($count-1) . '_' . str_pad(LicencePlate::getLicencePlateNumber('EBAY_OC_FILE'), 2, 0, 0);
    $implodeArray = implode("','", $idArray);

    if (!mysqli_ping($dbConnection)) 
    {
        $dbConnection = DbAccess3::connect(SETTING_DB_SERVER, SETTING_DB_USER, SETTING_DB_PASSWORD, SETTING_DB_DATABASE);
    }

    $query = "update tracking_data set latitude = 1, parcel_image = '".$fName."' where id in ('".$implodeArray."')";

    DbAccess3::runQuery($query);

    $path = SETTING_DIR_ASSETS . "ocData/shipment-data-in/";
    echo $path;
    if (!file_exists($path))
    {
        @mkdir($path, 0777, TRUE);
    }
    $file_path = $path . $fName.'.csv';                
    $file_handle = @fopen($file_path, 'w');
    fwrite($file_handle, AddHeader() . "\r\n");

    fwrite($file_handle, $csv);
    fclose($file_handle);

    $ssh = new Net_SFTP("sftp.orangeconnex.com", 22);
    if (!$ssh->login('ocowesftp', "wmWWg1!VXbxDOyN2^I")) 
    {
        mail("itsupport@oneworldexpress.com", "OC LOGIN FAILED", "OC LOGIN FAILED" . $fName);
    } 
    else 
    {
        $remotefile = "/TRACKING_PENDING/" . $fName.'.tmp';
         //if ($ssh->put($remotefile, $file_path, NET_SFTP_LOCAL_FILE) == true) 
         {
                         //    $ssh->rename($remotefile, "/TRACKING_PENDING/".$fName . ".csv") ;
        //'Upload Successful!';
        }
    }
}
mysqli_close($dbConnection);
?>
