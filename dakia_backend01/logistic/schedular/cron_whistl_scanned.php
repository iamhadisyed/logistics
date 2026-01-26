<?php
    
    require_once(__DIR__ . "/../includes/settings/config.inc.php");
    
    include_classes([
        'consignment.class',
        'consignmentfilter.class',
        'tracking_data.class',
        'trackingdatatfilter.class', 
         'user.class',
        'userfilter.class'
  	]);

    $dbConnection = DbAccess3::connect(SETTING_DB_SERVER, SETTING_DB_USER, SETTING_DB_PASSWORD, SETTING_DB_DATABASE);
    $date = date('Y-m-d');
    //$startDate = date('Y-m-d'); 
    $startDate = date('Y-m-d', strtotime('-1 day', strtotime($date)));//just for testing purpose, actually we use the above line for start date
    $endDate   = date('Y-m-d', strtotime('+2 day', strtotime($startDate)));
    $mawbArray = array();

    
    $query =   "SELECT 
                pbm.bag_id, t.date_created, b.bagnumber, t.tracking_number
                FROM
                parcel p INNER JOIN tracking_data t 
                ON p.id = t.entity_id
                INNER JOIN parcel_bagging_mapping pbm
                ON p.id = pbm.parcel_id
                INNER JOIN bagging b 
                ON b.id = pbm.bag_id
                WHERE
                        t.status_code_id = '140'
                    AND t.carrier_desc = 'Scan Collected by Receipt Depot'
                    AND t.date_created >= '" . $startDate . "'
                    AND t.date_created < '" . $endDate . "' group by pbm.bag_id";
    
    
    $offsetInMinutes = getRandomOffset();
    $randomMinute = getRandomMinute();
    
    $rs = DbAccess3::runQuery($query);   
   
    while ($record = mysqli_fetch_assoc ($rs))
    { 
        $bagNumber = $record['bagnumber'];
        $dateCreated = $record['date_created'];
        $dateTime = explode(' ', $dateCreated);
        $time = explode(':',$dateTime[1]);
        $hour = $time[0];  
        $randomHour = getScannedRandomHour($hour+1);
        $scannedDate = date("Y-m-d " . $randomHour . ":" . sprintf("%02d", $randomMinute));
             
        $arrayData['tracking_number'] = $record['tracking_number'];
        $arrayData['status_code_id'] = "161";
        $arrayData['date_created'] = $scannedDate;
        $arrayData['carrier_desc'] = "Scanned At Dispatch Hub";
        $arrayData['track_point'] = "United Kingdom";

        if (!mysqli_ping($dbConnection)) {
            $dbConnection = DbAccess3::connect(SETTING_DB_SERVER, SETTING_DB_USER, SETTING_DB_PASSWORD, SETTING_DB_DATABASE);
        }

            $insertQueryScanned = "INSERT INTO tracking_data "
                    . " (entity_id , tracking_number,status_code_id, carrier_desc, track_point, date_created, warehouse_id, ip_address) "
                    . "SELECT  "
                    . "     p.id,p.tracking_number,'" . mysqli_escape_string($dbConnection,$arrayData['status_code_id']) . "',"
                    . "     '" . mysqli_escape_string($dbConnection, $arrayData['carrier_desc']) . "','" . 
                            mysqli_escape_string($dbConnection, $arrayData['track_point']) . "',"
                    . "     '" . mysqli_escape_string($dbConnection, $arrayData['date_created']) . "',10,'213.246.110.102'"
                    . " FROM "
                    . "     parcel p INNER JOIN tracking_data td "
                    . " ON "
                    . "     td.entity_id = p.id "
                    . " WHERE "
                    . "     p.tracking_number = '" . mysqli_escape_string($dbConnection,$arrayData['tracking_number']) . "' "
                    . "     AND '" . mysqli_escape_string($dbConnection,$arrayData['status_code_id']) . 
                            "' not in (select td.status_code_id from tracking_data td where td.entity_id = p.id AND '" . 
                            mysqli_escape_string($dbConnection, $arrayData['carrier_desc']) . "' = td.carrier_desc ) "
                    . " LIMIT 1";
            "<br>";         
            DbAccess3::runQuery($insertQueryScanned);       
    }
    
    function getScannedRandomHour($hour)
    {
		return rand($hour,21);
    }

    function getDispatchRandomHour()
    {
		return rand(15, 17);
    }

    function getRandomMinute()
    {
		return rand(1, 60);
    }

    function getRandomOffset()
    {
		return rand(1, 5);
    }
