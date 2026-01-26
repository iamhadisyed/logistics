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

/*    include_classes([
    'SFTP'
    ], '3rdparty/Net');
*/

    $dbConnection = DbAccess3::connect(SETTING_DB_SERVER, SETTING_DB_USER, SETTING_DB_PASSWORD, SETTING_DB_DATABASE);
    $Date = date("Y-m-d");
    $startDate = date('Y-m-d', strtotime('-2 days', strtotime($Date)));	
    $endDate   = date('Y-m-d', strtotime('-1 days', strtotime($Date)));	
    
    $query =   "SELECT 
                   t.tracking_number, t.id
                FROM
                tracking_data t INNER JOIN parcel p 
                ON p.tracking_number = t.tracking_number 
                WHERE  
                    t.status_code_id = '161'
                AND t.carrier_desc = 'Scanned At Dispatch Hub'
                AND t.date_created >= '2020-02-04 03:10:00'
                AND t.date_created <= '2020-02-04 04:10:00' 
                AND t.latitude is null";
    
    $rs = DbAccess3::runQuery($query);    
       
    $mawbArray = array();
    $randomHour = getDispatchRandomHour(); 
    $randomMinute = getRandomMinute();
    $dateTime = date('Y-m-d'.' '. $randomHour . ":" . sprintf("%02d", $randomMinute));
    
    $counter = 0;
    $skipAwb = 0;
  
    while ($objArray = mysqli_fetch_assoc ($rs))
    {		
        $counter = 0;        
        $objArray['tracking_number'];
        echo "<br>";
            
        $arrayData['tracking_number'] = $objArray['tracking_number'];
        $arrayData['status_code_id'] = "126";
        $arrayData['date_created'] = $dateTime;
        $arrayData['carrier_desc'] = "Item handed over to Postal Operator";
        $arrayData['track_point'] = "United Kingdom";

        if (!mysqli_ping($dbConnection)) {
            $dbConnection = DbAccess3::connect(SETTING_DB_SERVER, SETTING_DB_USER, SETTING_DB_PASSWORD, SETTING_DB_DATABASE);
        }
            
        $insertQueryTrackingDataIHOTPO = "INSERT INTO tracking_data "
                . " (entity_id , tracking_number,status_code_id, carrier_desc, track_point, date_created, warehouse_id, ip_address) "
                . "SELECT  "
                . "     p.id,p.tracking_number,'" . $arrayData['status_code_id'] . "',"
                . "     '" . $arrayData['carrier_desc'] . "','" . $arrayData['track_point'] . "',"
                . "     '" . $arrayData['date_created'] . "',10,'213.246.110.102'"
                . " FROM "
                . "     parcel p inner join tracking_data td "
                . " ON "
                . "     td.entity_id = p.id "
                . " WHERE "
                . "     p.tracking_number = '" . $arrayData['tracking_number'] . "' "
               // . "     AND (trim(td.longitude) is null OR TRIM(td.longitude) = '')"
                . "     AND '" . $arrayData['status_code_id'] . "' not in (select status_code_id from tracking_data where td.entity_id = p.id AND '" . 
                        $arrayData['carrier_desc'] . "' = description ) "
                . " LIMIT 1";
        "<br>";
        echo $insertQueryTrackingDataIHOTPO; 
        DbAccess3::runQuery($insertQueryTrackingDataIHOTPO);

      //  $updateAddedTrackingDataIHOTPO = "UPDATE tracking_data SET longitude = '1' WHERE id <> 0 and trim(id) <> '' and  id = '".$objArray['id']."'";
      //  "<br>";
      //  DbAccess3::runQuery($updateAddedTrackingDataIHOTPO);

        $counter++;
        if($counter == 60)
        {
            $randomHour = getDispatchRandomHour();
            $randomMinute = getRandomMinute();
            $dateTime = date('Y-m-d'.' '. $randomHour . ":" . sprintf("%02d", $randomMinute));
            $counter = 0;        
        }
        echo $counter.'----------'.$dateTime;
        echo "<br>";
    }
    
	
    function getDispatchRandomHour()
    {
	return rand(5,7);
    }

    function getRandomMinute()
    {
        return rand(1, 60);
    }
   