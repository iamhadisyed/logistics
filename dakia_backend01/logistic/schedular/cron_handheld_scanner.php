<?php


error_reporting(E_ALL);
ini_set('display_errors', '0');

require_once(__DIR__ . "/../includes/settings/config.inc.php");


include_classes([    
    'iaddress.class',    
    'consignment.class',
    'parcel.class',
    'parcelfilter.class',
    'trackingdata.class',
    'tracking.class',
    'trackingdatafilter.class',
    'consignmentfilter.class', 
   
    
    ]);

    set_time_limit(-1);

    define("SCANNER_SETTING_DB_SERVER",   "213.246.110.102");
    define("SCANNER_SETTING_DB_USER",     "owescannerdbus3r");
    define("SCANNER_SETTING_DB_PASSWORD", "an0kh@L@L@.c0m");
    define("SCANNER_SETTING_DB_DATABASE", "OWEScannerDB2013");

    $conn = mysqli_connect(SCANNER_SETTING_DB_SERVER, SCANNER_SETTING_DB_USER, SCANNER_SETTING_DB_PASSWORD) or die(mysqli_error());
    
    $selectDb = mysqli_select_db($conn, SCANNER_SETTING_DB_DATABASE) or die(mysqli_error());

    $trackingNumberArray = GetScannedTrackingNumbers();

    if(count($trackingNumberArray) > 0)
    {
        AddTracking($trackingNumberArray);
    }

    function AddTracking($trackingNumberArray)
    {
        $foundNumbersArray = array();
        //print_r($trackingNumberArray);
        //die;

        foreach($trackingNumberArray as $key => $value)
        {
            $parseTrackingNumber = ParseTrackingNumber::Parse($value['TrackingNumber']);
            //echo $parseTrackingNumber;
            //echo "<br>";

            $ParcelFilter = new ParcelFilter();
            $ParcelFilter->addLicensePlateFilter(trim($parseTrackingNumber));
            $parcelList = $ParcelFilter->getList();

            if(count($parcelList) > 0)
            {
                $parcel = $parcelList[0];
                $parcel->setParcelStatusCode(Consignment::STATUS_RECEIVED);
                $parcel->setOweStatusCode(Consignment::$database_status_array[Consignment::STATUS_RECEIVED]);
                //echo "<pre>";
                //print_r($parcel);
                $parcel->save();

                $parcelId = $parcel->getId();
                $conId = $parcel->getConsignmentId();

                if($conId > 0)
                {
                    $foundNumbersArray[] = $value['TrackingNumber'];
                    $con = new Consignment($conId);
                    $trackingDataFilter = new TrackingDataFilter();
                    $statusCode = 133;
                    $trackPoint = "";
                    $desc = "Shipment picked up on route to sorting hub";
                    $latestDate = $value['DateTime'];
                    
                    $trackingDataFilter->addTrackPointExistFilter(trim($parseTrackingNumber), $statusCode, $trackPoint, $desc, "", $latestDate);
                    $trackingDataExistsObj = $trackingDataFilter->getColumnList("t.entity_id, t.entity_type", "");
                    if (count($trackingDataExistsObj) == 0 && $parcelId > 0) {

                        $trackingData = [
                            'user_id' => 4282,
                            'entity_id' => $parcelId,
                            'entity_type' => "parcel",
                            'tracking_number' => $parseTrackingNumber, //$con->getAwb(), multi-piece shipment
                            'track_point' => $trackPoint,
                            'date_created' => $latestDate,
                            'ip_address' => getClientIp(),
                            'status_code_id' => $statusCode,
                            'carrier_code' => '',
                            'carrier_desc' => $desc,
                            'signatory' => "",
                            //'warehouse_id' => '9'
                        ];

                        $trackingDataObj = new TrackingData($trackingData);
                        //echo "<pre>";
                        //print_r($trackingDataObj);
                        $trackingDataObj->save();
                    }
                }
                
            }
            else
            {
                echo "Tracking Number not found " . $trackingNumber;
                echo "<br>";
            }

            
        }

        echo  "<br><br>";
        echo "TOTAL FOUND ITEMS " . count($foundNumbersArray) . "<br><br>";


        if(count($foundNumbersArray) > 0)
            UpdateStatusOnHandHeldScanner($foundNumbersArray);
        
    }

    function GetScannedTrackingNumbers()
    {
        global $conn;
        $trackingNumberArray = array(); 
        //$date = date("Y-m-d");

        $date = "2021-07-09";


        $query = "select distinct trackingnumber, entrytime from transaction tr, tracking t where
        tr.transactionid = t.transactionid and entrytime >= '" . $date . "' and status = 0 and trackingnumber != '' limit 500";
                
        $result = mysqli_query($conn, $query) or die(mysqli_error());
        $num_rows = mysqli_num_rows($result);

        //echo $query;

        if($num_rows == 0 )
        {
            echo $num_rows . " records found.<br><br>";
            exit;
        }
        else
        {
            echo "TOTAL ITEMS $num_rows <br><br>";
            $count = 0;
            while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) 
            {                      
                $trackingNumberArray[$count]['TrackingNumber'] =  strtoupper($row['trackingnumber']);    
                $trackingNumberArray[$count]['DateTime'] = strtoupper($row['entrytime']);
                $count++;
            }    
        }

        return $trackingNumberArray;

    }

    function UpdateStatusOnHandHeldScanner($trackingNumberArray)
    {
        global $conn;

        if(count($trackingNumberArray) > 0)
        {
            $strTrackingNumbers = "'" . implode("','", $trackingNumberArray) . "'";

            $query = "update  tracking set status = 1 where trackingnumber in ($strTrackingNumbers)";
            echo  "<br>" .$query . "<br>";	   
            $result = mysqli_query($conn, $query) or die(mysql_error());
            
        }
       
    }

    if($conn)
    {
        mysqli_close($conn);   
        echo "Connection closed";

    }
				

?>
				
			
				
						
									
		
	
	
