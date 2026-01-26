<?php

require_once(__DIR__ . "/../includes/settings/config.inc.php");
require_once(__DIR__ . "/../includes/3rdparty/Net/SFTP.php");

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
    'parcelfilter.class',
    'carrierdatafilelog.class',
    'carrierdatafilelogfilter.class'
]);

$consignmentObj = [];
$date_created =  date("Y-m-d"); //date("Y-m-d");
 $sql = "SELECT 
     c.id,
     c.awb,
     #td.date_created as date_created,
     #td.status_code_id,
     #td.warehouse_id as warehouse_id,
     c.date_scanned,
     c.send_courier_data
FROM
    consignment c inner join services s on s.id = c.service_id 
    inner join  parcel p on p.consignment_id = c.id
    #inner join tracking_data td on td.entity_id = p. id
WHERE send_courier_data='2' and s.carrier_id = '147' group by c.id";
 //s.carrier_id in ('16')
//			and td.status_code_id = '146' AND td.warehouse_id = 9 
//                        and c.send_courier_data <> 2 and td.date_created >= '".$date_created."'
//            group by c.id";
$consignmentObj = Consignment::getConsignmentListFromSql($sql);

$recordArray = array();
if (count($consignmentObj) > 0) {
    
    /*
     * creating records for file
     */
    $consignmentIdArray = array();
    foreach ($consignmentObj as $consignment) {
        $consignmentIdArray[] = $consignment->getId();
        $recordArray[] = createRecord($consignment);
    }
    
    /*
     * sending file to yodel
     */
    if (count($recordArray) > 0) {
        if(sendBooking($recordArray, $consignmentIdArray)){
            mail("mruga@oneworldexpress.com","Hermes ASCAN", $filename ." ". count($consignmentIdArray));
            echo $sql = "UPDATE consignment SET send_courier_data = '3'
                                WHERE id IN ('" . implode("','", $consignmentIdArray) . "') AND id <> '0' 
                                AND  shipment_status not in ('" . Consignment::STATUS_RECYCLED . "','" . Consignment::STATUS_READY_TO_PRINT . "','" . Consignment::STATUS_INVALID . "')";
            DbAccess3::runQuery($sql);
        }

    }
}

/*
 * creating record for yodel file in array
 */

function createRecord($consignment) {
    $separator = ",";

    $record = $consignment->getAwb() . $separator; // tracking number
    $record .= "2145" . $separator; //client id
    $record .= "CC001" . $separator; //Extrernal track point
    $record .= date("dmY H:i:s"); // clearance datetime
    
    
    $record .= "\r\n";



    return $record;
}


/*
 *  creating header record
 */
function getHeaderRecord($runnumber) {
    
    $separator = ",";

    $record = '01' . $separator;  // RECORD_TYPE
    $record .= "HICKS" . $separator; // Custom clearance agent name
    $record .= $runnumber . $separator; // batch number
    $record .= date("dmY H:i:s");  // datetime
    $record .= "\r\n";
    return $record;
}

/*
 * creating footer record
 */
function getFooterRecord($count) {
    $separator = ",";

    $record = '99' . $separator;         // Record Type
    $record .= 'HICKS' . $separator;   // Custom clearance agent name
    $record .= $count ;   // record count

    return $record;
}

/*
 * sending data to yodel ftp 
 */
function sendBooking($recordArray, $consignmentIdArray) {
    if (sizeof($recordArray) > 0) {
        $filename = "CUSTOMS_FILE_2145_".date("Y-m-d_H-i-s") . ".csv";
        

        $path = SETTING_DIR_ASSETS . "data_send/hermes_presort_booking/" . date("Y_m_d") . "/";
        if (!file_exists($path))
            @mkdir($path, 0777, true);


        
        $run_number = CarrierDataFileLog::generateRunNumber("20001", "20001", false);
        $carrierDataFileLog = new CarrierDataFileLog();
        $carrierDataFileLog->setCarrierId("20001");
        $carrierDataFileLog->setAgentId("20001");
        $carrierDataFileLog->setFileName($filename);
        $carrierDataFileLog->setRunNumber($run_number);
        $carrierDataFileLog->save();
        $file_path = $path . $filename;
        chmod($path, 0777);
        // create file
        $file_handle = @fopen($file_path, 'w');
        fwrite($file_handle, getHeaderRecord($run_number));
        $count = count($recordArray);
        foreach ($recordArray as $record) {
            fwrite($file_handle, $record);
        }

        fwrite($file_handle, getFooterRecord($count));
        //
        // close file
        fclose($file_handle);
         $ssh = new Net_SFTP("sftp.hermescloud.co.uk");
        if (!$ssh->login('client.oneworldexpressstrategic', "XLqM8eBB5xQHqqd9")) {
         mail("mruga@oneworldexpress.com", "HERMES LOGIN FAILED", "HERMES LOGIN FAILED" . $fname); 
        }
        $remotefile="./Customs In/". $filename;
        if ($ssh->put($remotefile, $file_path, NET_SFTP_LOCAL_FILE) == true) 
            return true;
        else
        {
            mail("mruga@oneworldexpress.com", "Hermes A-scan", "Hermes A-scan " . $filename);
        }
        $recordArray = NULL;

    }
}