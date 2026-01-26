<?php

error_reporting(1);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
ini_set('memory_limit', '-1');
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
function AddHeader() {
    $header = "eBayTrackingStatus,ShipmentTrackingStatus,TrackingNumber,UserProvidedCarrierName,CarrierCode,EventDescription,EventDate,EventTime,EventTimeZone,EventCity,EventState,EventPostCode,EventCountry,CompanyName,SignedBy,EstimatedDeliveryDate,EstimatedDeliveryTime,EstimatedDeliveryTimeZone,ServiceCode,ServiceName,Weight,WeightUOM,Width,Height,Length,DimensionUOM,DestinationCity,DestinationState,DestinationPostalCode,DestinationCountry,SourceCity,SourceState,SourcePostalCode,SourceCountry,ShippingFee,ShippingFeeCurrency";
    return $header;
}

function CreateTrackingFiles() {
    $cr = "\r\n";
    $separator = ",";
    $csv = "";
    $AddHeader = "";

    $Date = date("Y-m-d H:i:s");
    $oneHourLess = date('Y-m-d H:i:s', strtotime($Date) - 3600);
    $fifteenMinute = date('Y-m-d H:i:s', strtotime($oneHourLess) + 900);
    
    $query = "SELECT 
    c.id,
    hawb,
    awb,
    dropoff_consignment_tracking,
    dispatch_consignment_tracking,
    bag_number
FROM
    consignment c
        INNER JOIN
    consignment_dropoff_mapping cdm ON c.id = cdm.dropoff_consignment_id
WHERE
    c.user_id = '4831'
        AND c.shipment_type = 'DO'
        AND c.awb != ''
        AND c.id IN (
        '640761' , 
        '640763',
        '640765',
        '640767',
        '640769',
        '640771',
        '640773',
        '640782',
        '640784',
        '640786',
        '642350',
        '642352',
        '642354',
        '642356',
        '642358',
        '642360',
        '642362',
        '642364',
        '642366',
        '642368',
        '642370',
        '642372',
        '642424',
        '642426',
        '642428',
        '642430',
        '642432',
        '642434',
        '642436',
        '642438',
        '642440',
        '642442',
        '642444',
        '643225',
        '643227',
        '643229',
        '643231',
        '643233',
        '643235',
        '643237',
        '643239',
        '643252',
        '643254',
        '643256',
        '643258',
        '643260',
        '643262',
        '643264',
        '643266',
        '643268')
        AND shipment_status NOT IN ('11' , '22') and bag_number != ''";
            
    $sendShipmentTrackingData = DbAccess3::runQuery($query);
    $endToEndTrackingNumbers = [];
    
    if (mysqli_num_rows($sendShipmentTrackingData) > 0) 
    {
        $count = 1;
        while ($row = mysqli_fetch_array($sendShipmentTrackingData)) 
        {   
            $endToEndTrackingNumbers[] = $row['dropoff_consignment_tracking'];
            $endToEndTrackingNumbers[] = $row['dispatch_consignment_tracking'];
            $endToEndTrackingNumbers[] = $row['bag_number'];
        }
    }
    $arrayString = implode("','",$endToEndTrackingNumbers);
    $arrayString = "OWEXGB00000134015US1157218296','92748902410401000191597498','1Z3985RW6819485375',
'OWEXGB00000134016US1135451566','92748902410401000191447441','1Z3985RW6819485375',
'OWEXGB00000134017US1123444436','92748902410401000191209933','1Z3985RW6819485375',
'OWEXGB00000134018US1141730086','92748902410401000191330514','1Z3985RW6819485375',
'OWEXGB00000134019US1136011626','92748902410401000191371661','1Z3985RW6819485375',
'OWEXGB00000134020US0709346666','92748902410401000191660970','1Z3985RW6819485375',
'OWEXGB00000134021US000115186','92748902410401000191580919','1Z3985RW6819485375',
'OWEXGB00000134022US000114176','92748902410401000191549305','1Z3985RW6819485375',
'OWEXGB00000134023US000112346','92748902410401000191221515','1Z3985RW6819485375',
'OWEXGB00000134024US000113546','92748902410401000191675141','1Z3985RW6819485375',
'OWEXGB00000134049US9029270346','92748902410402000191582806','1Z3985RW6819485375',
'OWEXGB00000134050US9030213376','92748902410402000191656316','1Z3985RW6819485375',
'OWEXGB00000134051US9050336196','92748902410402000191381010','1Z3985RW6819485375',
'OWEXGB00000134052DE000657796','H1002531221100301044','1Z3985RW6821083321',
'OWEXGB00000134053US9050134616','92748902410402000191443084','1Z3985RW6819485375',
'OWEXGB00000134054US9066038336','92748902410402000191386916','1Z3985RW6819485375',
'OWEXGB00000134055DE000645466','H1002531221100501044','1Z3985RW6821083321',
'OWEXGB00000134056US9062034016','92748902410402000191346644','1Z3985RW6819485375',
'OWEXGB00000134057US9030213376','92748902410402000191625381','1Z3985RW6819485375',
'OWEXGB00000134058US9173225156','92748902410402000191363634','1Z3985RW6819485375',
'OWEXGB00000134059US9176554306','92748902410402000191249464','1Z3985RW6819485375',
'OWEXGB00000134060US9177621476','92748902410402000191306297','1Z3985RW6819485375',
'OWEXGB00000134067AU000040076','33XA8040937401000931505','1Z3985RW6821083321',
'OWEXGB00000134068AU000022236','33XA6063319201000931502','1Z3985RW6821083321',
'OWEXGB00000134069AU000021536','33XA6063983401000931505','1Z3985RW6821083321',
'OWEXGB00000134070AU000022236','33XA6060947401000931502','1Z3985RW6821083321',
'OWEXGB00000134071AU000031756','33XA7041163001000931508','1Z3985RW6821083321',
'OWEXGB00000134072AU000022056','33XA6063930601000931507','1Z3985RW6821083321',
'OWEXGB00000134073AU000020196','33XA6061926201000931502','1Z3985RW6821083321',
'OWEXGB00000134074AU000022236','33XA6060719601000931505','1Z3985RW6821083321',
'OWEXGB00000134075AU000031506','33XA7044539001000931508','1Z3985RW6821083321',
'OWEXGB00000134076AU000022106','33XA6062493301000931503','1Z3985RW6821083321',
'OWEXGB00000134077CA00L5M5K96','4006318783815349','1Z3985RW6821083321',
'OWEXGB00000134133DE000645466','H1002531221528301044','1Z3985RW6821083321',
'OWEXGB00000134134DE000521346','H1002531221528401005','1Z3985RW6821083321',
'OWEXGB00000134135DE000645466','H1002531221528501044','1Z3985RW6821083321',
'OWEXGB00000134136DE000657796','H1002531221528701044','1Z3985RW6821083321',
'OWEXGB00000134137DE000107856','H1002531221529901072','1Z3985RW6821083321',
'OWEXGB00000134138DE000521346','H1002531221529401005','1Z3985RW6821083321',
'OWEXGB00000134139DE000107856','H1002531221529501072','1Z3985RW6821083321',
'OWEXGB00000134140DE000645466','H1002531221529601044','1Z3985RW6821083321',
'OWEXGB00000134143CA00L6L6A46','4006318783754280','1Z3985RW6821083321',
'OWEXGB00000134144CA00L5M5K96','4006318783949327','1Z3985RW6821083321',
'OWEXGB00000134145CA00L6Y6A46','4006318783932343','1Z3985RW6821083321',
'OWEXGB00000134146CA00L5N0C76','4006318783867621','1Z3985RW6821083321',
'OWEXGB00000134147CA00L5M5V16','4006318783819569','1Z3985RW6821083321',
'OWEXGB00000134148CA00M1S1S66','4006318784165771','1Z3985RW6821083321',
'OWEXGB00000134149CA00L5M5V16','4006318784198458','1Z3985RW6821083321',
'OWEXGB00000134150CA00L5N0C76','4006318784085895','1Z3985RW6821083321',
'OWEXGB00000134151CA00L6Y6A46','4006318783737498','1Z3985RW6821083321',
'OWEXGB00000142676US6008911895','92748902410411000191691896','1Z3985RW6828304427',
'OWEXGB00000142677US6012326715','92748902410411000191614444','1Z3985RW6828304427',
'OWEXGB00000142678US000601305','92748902410411000191272972','1Z3985RW6828304427',
'OWEXGB00000142679US6008911895','92748902410411000191349704','1Z3985RW6828304427',
'OWEXGB00000142680US6064035085','92748902410411000191359765','1Z3985RW6828304427',
'OWEXGB00000142681US6063120395','92748902410411000191279919','1Z3985RW6828304427',
'OWEXGB00000142682US6060919085','92748902410411000191224797','1Z3985RW6828304427',
'OWEXGB00000142683US6000459415','92748902410411000191301603','1Z3985RW6828304427',
'OWEXGB00000142684US6063120395','92748902410411000191633636','1Z3985RW6828304427";
    $query =    "Select * from tracking_data where tracking_number in('".$arrayString."') and (data_send is null or data_send = 0)";
                //and tdh.entry_date >='". $oneHourLess. "' and
                //tdh.entry_date <='". $fifteenMinute."'";
    
    //echo $query ; die;
    $result = DbAccess3::runQuery($query);
    $path = SETTING_DIR_ASSETS . "endToEnd/".date('Y-m-d')."/";
    $processedPath = SETTING_DIR_ASSETS . 'endToEnd/'.date('Y-m-d').'/data_processed/';

    if (!file_exists($path))
    {        
        mkdir($path, 0777);
    }

    if (!file_exists($processedPath))
    {
        mkdir($processedPath, 0777);
    }
    
    if (mysqli_num_rows($result) > 0) {
        $count = 1;
        while ($row = mysqli_fetch_array($result)) 
        {        
            $status_code = strtolower($row['status_code_id']);
            $carrier_code = strtolower($row['carrier_code']);            
            $description = strtolower($row['carrier_desc']);
            $track_point = strtolower($row['track_point']);
            $tracking_number = $row['tracking_number'];
            $ebayCode = '';
            /*
            if ($status_code == '144') 
            {
                $ebayCode = 'MANIFEST';
            }
            
            if ($status_code == '148') 
            {
                $ebayCode = 'FIRST_SCAN';
            }
            
            if ($status_code == '111') {
                $ebayCode = 'OUT_FOR_DELIVERY';                
            }
            
            if($status_code == '121')
            {
                $ebayCode= 'DELIVERED';
            }
            if($status_code == '123')
            {
                $ebayCode = 'DELIVERY_ATTEMPT';
            }
            
            if(($status_code == '137' || $status_code == '146' || $status_code == '126' || $status_code == '117' || $status_code == '145') && 
            strtolower($track_point) != 'arrived destination countr' && strtolower($track_point) != "departed lhr")
            {
                $ebayCode = 'IN_TRANSIT';
            }
            */
            if(strtolower($track_point) == 'departed lhr')
            {
                $ebayCode = 'ATD';
            }
            
            if(strtolower($track_point) == 'arrived destination country')
            {
                $ebayCode = 'ATA';
            }
            
            if($ebayCode == '')
            {
                continue;
            }
                        
            $id = $row['id'];
            $idArray[] = $id;
                  
            //$csv .=  "EBAYP" . str_pad(LicencePlate::getLicencePlateNumber('EBAY_OC'), 10, 0, 0) . $separator; // serial number
            $csv .= $ebayCode . $separator; //eBay Mapped Code
            $csv .= $status_code . $separator;
            $csv .= $tracking_number . $separator;

            $csv .= '' . $separator; //
            $csv .= '' . $separator; //
            
            $csv .= str_replace(',', ' ', $description) . $separator; //description 
            $dateTimeArray = explode(' ', $row['date_created']);
           
            $date = $dateTimeArray[0];
            $time = $dateTimeArray[1];
            //continue;
            $csv .= $date . $separator;
            $csv .= $time . $separator; //event state
            $csv .= '' . $separator; //event city
            $csv .= '' . $separator; //event district
            $csv .= '' . $separator; //event postcode
            $csv .= '' . $separator; //Special oper description
            $csv .= '' . $separator; //Special oper description
            $csv .= '' . $separator;
            $csv .= '' . $separator;
            $csv .= '' . $separator;
            $csv .= '' . $separator;
            $csv .= '' . $separator;
            $csv .= '' . $separator;
            $csv .= '' . $separator;
            $csv .= '' . $separator;
            $csv .= '' . $separator;
            $csv .= '' . $separator;
            $csv .= '' . $separator;
            $csv .= '' . $separator;
            $csv .= '' . $separator;
            $csv .= '' . $separator;
            $csv .= '' . $separator;
            $csv .= '' . $separator;
            $csv .= '' . $separator;
            $csv .= '' . $separator;
            $csv .= '' . $separator;
            $csv .= '' . $separator;
            $csv .= '' . $separator;
            $csv .= '' . $separator;
            $csv .= '' . $separator;
            $csv .= $cr;
            
                      
            if($count >= '80000')
            {
                $fName = "EBAYTRK_1.0_00001_OWE".date('Ymd').'_'.time('His');
                $implodeArray = implode("','", $idArray);
        
                if (!mysqli_ping($dbConnection)) 
                {
                    $dbConnection = DbAccess3::connect(SETTING_DB_SERVER, SETTING_DB_USER, SETTING_DB_PASSWORD, SETTING_DB_DATABASE);
                }

                $query = "update tracking_data set hub = 1, pod_image = '".$fName."' where id in ('".$implodeArray."')";

                //DbAccess3::runQuery($query);
                
                $file_path = $path . $fName.'.csv';
                $file_handle = @fopen($file_path, 'w');
                fwrite($file_handle, AddHeader() . "\r\n");

                fwrite($file_handle, $csv);
                fclose($file_handle);

                $ssh = new Net_SFTP("sftp-gs.orangeconnex.com", 22);
                if (!$ssh->login('gspkow', "I!MYQ7KOe8zL!G6JZR")) 
                {
                  //  mail("itsupport@oneworldexpress.com", "OC LOGIN FAILED", "OC LOGIN FAILED" . $fName);
                } 
                else 
                {
                    $filesall = scandir($path);
                    foreach($filesall as $file)
                    {
                        if($file == '.' || $file == '..' || $file == 'data_processed')
                        {
                            continue;
                        }
                        $fName = pathinfo($file, PATHINFO_FILENAME);
                        $remotefile = "/TRACKING_PENDING/" . $fName.'.tmp';
                        if ($ssh->put($remotefile, $path . $fName.'.csv', NET_SFTP_LOCAL_FILE) == true) 
                        {
                            $ssh->rename($remotefile, "/TRACKING_PENDING/".$fName . ".csv");
                            rename($path.$file, $processedPath.$file);
                        }                
                    }
                    if(count($filesall) > 10)
                    {
                        mail("itsupport@oneworldexpress.com", "OC LOGIN FAILED", "OC LOGIN FAILED" , 'From: kiran.iftikhar@oneworldexpress.com');
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
       
        $fName = "EBAYTRK_1.0_00001_OWE_".date('Ymd').'_'.date('His');
        //echo $fName; //die;
        $implodeArray = implode("','", $idArray);
        if (!mysqli_ping($dbConnection)) 
        {
            $dbConnection = DbAccess3::connect(SETTING_DB_SERVER, SETTING_DB_USER, SETTING_DB_PASSWORD, SETTING_DB_DATABASE);
        }
        
        $query = "update tracking_data set data_send = 1, pod_image = '".$fName."' where id in ('".$implodeArray."')";
        
        //DbAccess3::runQuery($query);
            
        $file_path = $path . $fName.'.csv';
        
        chmod($path, 0777);
        $file_handle = @fopen($file_path, 'w');
        fwrite($file_handle, AddHeader() . "\r\n");
        fwrite($file_handle, $csv);
        fclose($file_handle);
die;
        $ssh = new Net_SFTP("sftp-gs.orangeconnex.com", 22);
        if (!$ssh->login('gspkow', "I!MYQ7KOe8zL!G6JZR")) 
        {
          //  mail("itsupport@oneworldexpress.com", "OC LOGIN FAILED", "OC LOGIN FAILED" . $fName);
        } 
        else  
        {
            $filesall = scandir($path);
            foreach($filesall as $file)
            {
                if($file == '.' || $file == '..' || $file == 'data_processed')
                {
                    continue;
                }
                $fName = pathinfo($file, PATHINFO_FILENAME);
                $remotefile = "/TRACKING_PENDING/" . $fName.'.tmp';
                if ($ssh->put($remotefile, $path . $fName.'.csv', NET_SFTP_LOCAL_FILE) == true) 
                {
                    $ssh->rename($remotefile, "/TRACKING_PENDING/".$fName . ".csv");
                    rename($path.$file, $processedPath.$file);
                }                
            }
            if(count($filesall) > 10)
            {
                mail("itsupport@oneworldexpress.com", "OC LOGIN FAILED", "OC LOGIN FAILED" , 'From: kiran.iftikhar@oneworldexpress.com');
            }
        }
    }
}
mysqli_close($dbConnection);
?>
