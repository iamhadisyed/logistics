<?php

require_once(__DIR__ . "/../includes/settings/config.inc.php");


include_classes(['carrierservice.class'],
        'general');

include_classes([
    'iaddress.class',
    'consignment.class',
    'consignmentfilter.class',
    'agentdata.class',
    'agentdatafilter.class',
    'country.class',
    'countryfilter.class',
    'services.class',
    'servicesfitler.class',
    'serviceconstantvalue.class',
    'serviceconstantvaluefilter.class',
    'parcel.class',
    'parcelfilter.class'
]);

/*
 * query for fetching records which are scanned  and data not send do yodel
 */
$consignmentObj = [];
$date_created =  date("Y-m-d"); //date("Y-m-d");
 $sql = "SELECT 
     c.id,
     c.awb,
     td.date_created as date_created,
     td.status_code_id,
     td.warehouse_id as warehouse_id,
     c.date_scanned,
     c.send_courier_data
FROM
    consignment c inner join services s on s.id = c.service_id 
    inner join  parcel p on p.consignment_id = c.id
    inner join tracking_data td on td.entity_id = p. id
WHERE
 s.carrier_id in ('16')
			and td.status_code_id = '146' AND td.warehouse_id = 9 
                        and c.send_courier_data <> 2 and td.date_created >= '".$date_created."'
            group by c.id";
$consignmentObj = Consignment::getConsignmentListFromSql($sql);

$recordArray = array();
if (count($consignmentObj) > 0) {
    
    /*
     * creating records for yodel file
     */
   echo  $fileTime = date("His");
    $recordTime = date('Hi', strtotime("+15 minutes", strtotime(date("His")))) ;
    foreach ($consignmentObj as $consignment) {
        echo $consignment->getAwb() . "<br>";
        $recordArray[] = createYodelRecord($consignment, $recordTime);
    }
    
    /*
     * sending file to yodel
     */
    if (count($recordArray) > 0) {
        sendBooking($recordArray, $fileTime);
    }
}

/*
 * creating record for yodel file in array
 */

function createYodelRecord($consignment, $recordTime) {
    $separator = ",";

    $record = 'PDT_AP42_E' . $separator; // RECORD_TYPE
    $awb = $consignment->getAwb();
    echo $awb . "<br/>";
    $warehouse_id = $consignment->getWarehouseId();
    $dateDispatched = $consignment->getDateCreated();
    if (trim($dateDispatched) == "") {
        return;
    }
    $dateDispatch = date('ymd', strtotime($dateDispatched));
    $record .= $dateDispatch . $separator; // YYMMDD
    $tracktime =  $recordTime;
    $record .= $tracktime. $separator; // HHMM

    if ($warehouse_id == 9) {
        $record .= "5558" . $separator;                          // ROUTE_CODE
        $record .= $separator;                          // SERVICE_AREA
        $record .= $separator;                          // LOCATION
        $record .= "5558" . $separator;                          // OPUNIT
        $record .= "T15533" . $separator;                          // EMPLOYEE_ID
        $record .= 'A' . $separator;                         // CYCLE_RUN_CODE
        $record .= $awb . $separator;                        // PARCEL NO
        $record .= '1' . $separator;                          // ROUTINE_CODE
        $record .= "CH" . $separator;                       // SCAN_CODE
    } else {
        $record .= "5560" . $separator;                          // ROUTE_CODE
        $record .= $separator;                          // SERVICE_AREA
        $record .= $separator;                          // LOCATION
        $record .= "5560" . $separator;                          // OPUNIT
        $record .= "T15576" . $separator;                          // EMPLOYEE_ID
        $record .= 'A' . $separator;                         // CYCLE_RUN_CODE
        $record .= $awb . $separator;                        // PARCEL NO
        $record .= '1' . $separator;                          // ROUTINE_CODE
        $record .= "CH" . $separator;                       // SCAN_CODE
    }
    $record .= $separator;                         // VEHICLE_REG
    $record .= $separator;                         // OPUNIT_CODE
    $record .= $separator;                         // OPUNIT_NAME
    $record .= $separator;                         // AGENT
    $record .= $separator;                         // AGENT_NMR
    $record .= $separator;                         // HANDLING_UNIT
    $record .= $separator;                         // FREE_TEXT
    $record .= $separator;                         // ITEM_LENGTH
    $record .= $separator;                         // ITEM_WIDTH
    $record .= $separator;                         // ITEM_HEIGHT
    $record .= $separator;                         // ITEM_WEIGHT 

    $record .= "\r\n";



    if ($consignment->getSendCourierData() == "1") {
        $consignment->setSendCourierData(2);
        $consignment->save();
    }
//    } else {
//        $yodelSendData = new Yodel();
//        $senddataResponce = $yodelSendData->sendData(array($consignment->getAwb()));
//        $consignment->setSendCourierData(2);
//        //$consignment->save();
//    }
    return $record;
}


/*
 *  creating header record
 */
function getHeaderRecord() {
    $scannerId = "W555801";
    $separator = "|";

    $record = '"H' . $separator;  // RECORD_TYPE
    $record .= $scannerId . $separator; // SCANNER_ID
    $record .= date("Ymd") . $separator; // DATE
    $record .= 'Y2.02.00.00"';  // VERSION
    $record .= "\r\n";
    return $record;
}

/*
 * creating footer record
 */
function getFooterRecord() {
    $separator = "|";

    $record = '"T' . $separator;         // M REC_TYPE
    $record .= 'live"';   // M T_DATA	
    $record .= "\r\n";

    return $record;
}

/*
 * sending data to yodel ftp 
 */
function sendBooking($recordArray, $fileTime) {
    if (sizeof($recordArray) > 0) {
        $scannerId = "W555801";
        $time = $fileTime;
        $filename = $scannerId . $time[0] . "." . $time[1] . $time[2] . $time[3] . $time[4] . $time[5];

        $path = SETTING_DIR_ASSETS . "data_send/yodel_presort_booking/" . date("Y_m_d") . "/";
        if (!file_exists($path))
            @mkdir($path, 0777, true);



        $file_path = $path . $filename;
        chmod($path, 0777);
        // create file
        $file_handle = @fopen($file_path, 'w');
        fwrite($file_handle, getHeaderRecord());
        foreach ($recordArray as $record) {
            fwrite($file_handle, $record);
        }

        fwrite($file_handle, getFooterRecord());
        //
        // close file
        fclose($file_handle);

        $local_file = realpath($file_path);

        $ftp_object = new FTPfile("oneworld", "serv1ce", "cs.yodel.co.uk");

        $remote_file_path = "./ltr/" . $filename;
        if($ftp_object->put($remote_file_path, $local_file, FTP_BINARY)){
            echo "file uploaded Successfully";
        }
        else
        {
            mail("mruga@oneworldexpress.com", "YODEL-PRESORT UNABLE TO UPLOAD FILE", "YODEL-PRESORT UNABLE TO UPLOAD FILE : " . $filename);
        }
        $recordArray = NULL;

    }
}