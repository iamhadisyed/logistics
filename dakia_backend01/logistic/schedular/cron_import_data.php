<?php

//error_reporting(E_ALL & ~(E_NOTICE|E_WARNING));
require_once(__DIR__ . "/../includes/settings/config.inc.php");
include_classes([
    'bagging.class',
    'mawbfilter.class',
    'mawb.class',
    'services.class',
    'servicefilter.class'
]);
// convert latin1 to utf8
function convert_from_latin1_to_utf8_recursively($dat)
{
    if (is_string($dat)) {
        return utf8_encode($dat);
    } elseif (is_array($dat)) {
        $ret = [];
        foreach ($dat as $i => $d) $ret[ $i ] = convert_from_latin1_to_utf8_recursively($d);

        return $ret;
    } elseif (is_object($dat)) {
        foreach ($dat as $i => $d) $dat->$i = convert_from_latin1_to_utf8_recursively($d);

        return $dat;
    } else {
        return $dat;
    }
}

$filesData = scandir(SETTING_DIR_ASSETS."ebay_data/data_in");
$files = array_diff($filesData, array('..', '.'));
if(count($files) > 0){
    foreach ($files as $file) {
        $errorFound = false;
        // load json file
        $jsonString = file_get_contents(SETTING_DIR_ASSETS."ebay_data/data_in/".$file);
        // load file and covert in to 3 csv files
        $pureJsonStr = "";
        $pureJsonStr = convert_from_latin1_to_utf8_recursively($jsonString);
        $jsonData = json_decode($pureJsonStr, true);
        if(!empty($jsonData)){
            // Check if account is exist
            $userAccount = $jsonData['Account'];
            $userName = $jsonData['UserName'];
            $password = $jsonData['Password'];
            $hoauBagId = $jsonData['HoauBagId'];
            $bagWeight = $jsonData['BagWeight'];
            $mawbNumber = $jsonData['MAWB'];
            $flightNumber = $jsonData['FlightNumber'];
            $currency = $jsonData['Currency'];
            $serviceCode = $jsonData['LastMileServiceCode'];
            $lineHaulVendorName = $jsonData['LineHaulVendorName'];
            $lastMileVendorName = $jsonData['LastMileVendorName'];
            $finalDestinationCountry = $jsonData['FinalDestinationCountry'];
            $battery = $jsonData['Battery'];
            if (!mysqli_ping($dbConnection)) {
                $dbConnection = DbAccess3::connect(SETTING_DB_SERVER, SETTING_DB_USER, SETTING_DB_PASSWORD, SETTING_DB_DATABASE);
            }
            $query =   "SELECT
                id,user_pass,warehouse_id
                FROM
                `user`
                WHERE
                    user_name = '" . DbAccess3::escape($userName) . "'
                    AND is_deleted != 1 
                    AND active_flag = 1 
                    AND is_tc_agreed = 1  
                    ";
            $queryData = $dbConnection->query($query);
            if (mysqli_num_rows($queryData) > 0) {
                while ($record = mysqli_fetch_assoc ($queryData)){
                    $userPass = $record['user_pass'];
                    $userId = $record['id'];
                    $warehouseId = $record['warehouse_id'];
                }
            }else{
                sendEmail("No user found against username " .$userName,true,$file);
            }
            if(!password_verify(DbAccess3::escape($password), $userPass)){
                sendEmail("Password mismatch ".$password,true,$file);
            }
// Check destination country id
            $queryCoun =   "SELECT
                id
                FROM
                `country`
                WHERE
                    iso = '" . DbAccess3::escape($finalDestinationCountry) . "'  
                    ";
            $counData = $dbConnection->query($queryCoun);
            if (mysqli_num_rows($counData) > 0) {
                while ($recordCoun = mysqli_fetch_assoc ($counData)){
                    $countryId = $recordCoun['id'];
                }
            }else{
                sendEmail("Country id not found ".$finalDestinationCountry,true,$file);
            }
// Check if bag is already there and opened
            $sqlCheckBag = "SELECT * FROM `bagging` b WHERE b.`bagnumber` = '".DbAccess3::escape($hoauBagId)."' ";
            $bagData = $dbConnection->query($sqlCheckBag);
            if (mysqli_num_rows($bagData) > 0) {
//    $bagData
                while ($record = mysqli_fetch_assoc ($bagData)){
                    $isClosed = $record['is_closed'];
                    $closedDate = $record['closed_date'];
                    $closedBy = $record['closed_by'];
                    $bagId = $record['id'];
                }
                if($isClosed == "1" && $closedBy > 0){
                    sendEmail("Bag is closed ".$hoauBagId);
                }
            }else{
                // Create Bag from given data
                $bagging = new Bagging();
                $bagging->setBagnumber($hoauBagId);
                $bagging->setDateCreated(time());
                $bagging->setUserId($id);
                $bagging->setBagSourceCountryId('44');
                $bagging->setBagDestinationCountryId($countryId);
                $bagging->setWeight($bagWeight);
                $bagging->save();
                $bagId = $bagging->getId();
            }
// check if mawb is already exist
            $mawbFilter = new MawbFilter();
            $mawbFilter->addFilter("    mawb_number='".DbAccess3::escape($mawbNumber)."'");
            $mawbFilterObj = $mawbFilter->getList("id,mawb_status");
            if(count($mawbFilterObj) > 0){
                $mawbStatus = $mawbFilterObj[0]->getMawbStatus();// if o then its open
                $mawbId = $mawbFilterObj[0]->getId();
                if($mawbStatus == 'd'){
                    sendEmail("MAWB is dispatched ".$hoauBagId);
                }
            }else{
                $mawbObj = new Mawb();
                $mawbObj->setMawbSourceCountryId('44');
                $mawbObj->setMawbDestinationCountryId($countryId);
                $mawbObj->setMawbNumber($mawbNumber);
                $mawbObj->setIsActive('y');
                $mawbObj->setAddedDate(date("Y-m-d H:i:s",time()));
                $mawbObj->setUpdatedDate(date("Y-m-d H:i:s",time()));
                $mawbObj->setAddedBy($id);
                $mawbObj->save();
                $mawbId = $mawbObj->getId();
            }
// check service id exist LastMileServiceCode
//$serviceId = "244";
            $serviceFilter = new ServiceFilter();
            $serviceFilter->addFilter("    code='".DbAccess3::escape($serviceCode)."'");
            $serviceFilterObj = $serviceFilter->getColumnList("id,name");
            if(count($serviceFilterObj) > 0){
                $serviceName = $serviceFilterObj[0]->getName();// if o then its open
                $serviceId = $serviceFilterObj[0]->getId();
            }else{
                sendEmail("service not found in system ".$serviceCode,true,$file);
            }
// Create consignment csv
            $conData = [];
            $parcelData = [];
            $conBatchNumber = "";
            $item = [];
            $batchNumber = time();
            $sqlStr = "INSERT INTO `consignment` (
                                          user_id,
                                          service_id,
                                          warehouse_id,
                                          shipment_status,
                                          awb,
                                          consignment_status,
                                          hawb,
                                          date_created,
                                          company,
                                          contact,
                                          address_line_1,
                                          address_line_2,
                                          address_line_3,
                                          city,
                                          state,
                                          postcode,
                                          country_id,
                                          telephone,
                                          number_pieces,
                                          weight,
                                          vol_weight,
                                          description,
                                          notes,
                                          `value`,
                                          currency,
                                          sender_name,
                                          sender_company,
                                          sender_telephone,
                                          sender_address_line_1,
                                          sender_address_line_2,
                                          sender_address_line_3,
                                          sender_city,
                                          sender_postcode,
                                          sender_country_id,
                                          sender_state
                                        )
                                        VALUES ";
            $sqlParcelStr = 'INSERT INTO    `parcel` (
                                                consignment_id,
                                                tracking_number,
                                               `length`,
                                                width,
                                                height,
                                                weight,
                                                description,
                                                parcel_message,
                                                qty,
                                                itemvalue,
                                                hscode,
                                                pweight,
                                                owe_status_code,
                                                parcel_status_code
                                            )
                                            VALUES ';
            $sqlMawbParcelMappingStr = 'INSERT INTO `mawb_parcel_mapping` (
                                                              `mawb_id`,
                                                              `parcel_id`,
                                                              `wharehouse_id`,
                                                              `bag_id`,
                                                              `date_added`,
                                                              `added_by`) VALUES ';
            $sqlParcelBaggingMappingStr  ='INSERT INTO `parcel_bagging_mapping` (
                                                              `parcel_id`,
                                                              `bag_id`,
                                                              `added_by`,
                                                              `added_date`) 
                                                              VALUES ';
            $sqlBaggingServiceMappingStr = 'INSERT INTO `bagging_services_mapping` (
                                                              `bag_id`,
                                                              `service_id`) 
                                                                VALUES ';
            $count = 1;
            $totalRecords = count($jsonData['PackageInfoList']);
            foreach ($jsonData['PackageInfoList'] as $item) {
                /*
                 * First loop to get consignment and parcel
                 * Second loop for consignment and parcel add query
                 * In this loop we should make a array of tracking so we can assign parcels con= ids and also we can add
                 * mapping table:  mawb_parcel_mapping, bagging_parcel_mapping, bagging_service_mapping
                 *
                 */
                // Consignment add
                $sqlStr .=' ("'.$userId.'","'.
                    $serviceId.'","'.
                    $warehouseId.'",'.
                    "'13'".',"'.
                    $item['TrackingNumber'].'",'.
                    "'label created'".',"'.
                    $item['UniqueId'].'","'.
                    date('Y-m-d h:i:s', strtotime($item['OrderDate'])).'","'.
                    $item['ConsigneeBusinessName'].'","'.
                    $item['ConsigneeFullName'].'","'.
                    $item['ConsigneeAddr1'].'","'.
                    $item['ConsigneeAddr2'].'","'.
                    $item['ConsigneeAddr3'].'","'.
                    $item['ConsigneeCity'].'","'.
                    $item['ConsigneeState'].'","'.
                    $item['ConsigneeZipCode'].'",'.
                    "'44'".',"'.
                    $item['ConsigneePhone'].'",'.
                    '"1"'.',"'.
                    $item['PackageTotalWeight'].'","'.
                    $item['PackageVolume'].'","'.
                    $item['PackageDesc'].'","'.
                    $item['PackageDescCn'].'","'.
                    $item['PackageTotalValue'].'","'.
                    $item['Currency'].'","'.
                    $item['SellerFullName'].'","'.
                    $item['SellerBusinessName'].'","'.
                    $item['SellerPhone'].'","'.
                    $item['SellerAddr1'].'","'.
                    $item['SellerAddr2'].'","'.
                    $item['SellerAddr3'].'","'.
                    $item['SellerCity'].'","'.
                    $item['SellerZipCode'].'","'.
                    $countryId.'",'.
                    "'".$item['SellerState']."'"."),";
                $parDisp = $item['PackageDesc'];
                $parQuantity = "";
                $parHscode = "";
                $parSkuWeight = "";
                if(count($item['ItemInfoList']) > 0){
                    foreach ($item['ItemInfoList'] as $parcelItem) {
                        $parDisp .= '||'.$parcelItem['SkuDesc'];
                        $parQuantity .= $parcelItem['Quantity'].'||';
                        $parHscode .= $parcelItem['Hscode'].'||';
                        $parSkuWeight .= $parcelItem['SkuWeight'].'||';
                    }
                }
                // add parcel
                $sqlParcelStr .= ' ((SELECT id FROM consignment WHERE awb ="'.DbAccess3::escape($item['TrackingNumber']).'") ,"'.
                    $item['TrackingNumber'].'","'.
                    $item['PackageLength'].'","'.
                    $item['PackageWidth'].'","'.
                    $item['PackageHeight'].'","'.
                    $item['PackageTotalWeight'].'","'.
                    $parDisp.'","'.
                    $item['PackageDescCn'].'","'.
                    $parQuantity.'","'.
                    $item['PackageTotalValue'].'","'.
                    $parHscode.'","'.
                    $parSkuWeight.'","'.
                    "label created".'","'.
                    "13".'"'."),";
                // Add mawb_parcel_mapping
                $sqlMawbParcelMappingStr .= '  ("'.$mawbId.'" ,(SELECT id FROM `parcel` WHERE tracking_number ="'.DbAccess3::escape($item['TrackingNumber']).'"),"'.
                    $warehouseId.'","'.
                    $bagId.'","'.
                    date("Y-m-d H:i:s",time()).'","'.
                    $userId.'"'."),";

                $sqlParcelBaggingMappingStr .= ' (
                                                                (SELECT id FROM `parcel` WHERE tracking_number ="'.DbAccess3::escape                                              ($item['TrackingNumber']).'"),"'.
                    $bagId.'","'.
                    $userId.'","'.
                    date("Y-m-d H:i:s",time()).'"'."),";

                $sqlBaggingServiceMappingStr .= '  ("'.
                    $bagId.'","'.
                    $serviceId.'"'
                    ."),";
                if($count % 1000 == 0 || $count == $totalRecords){
                    // Add consignment
                    $sqlStr = rtrim($sqlStr,',');
                    $conQuery = $dbConnection->query($sqlStr);
                    if(!empty($conQuery->error)){
                        $errorFound = true;
                        sendEmail("Error description: " .$conQuery->error."<br/><br/>".$sqlStr);
                    }
                    // Add parcel
                    $sqlParcelStr = rtrim($sqlParcelStr,',');
                    $parcelQuery = $dbConnection->query($sqlParcelStr);
                    if(!empty($parcelQuery->error)){
                        $errorFound = true;
                        sendEmail("Error description: " .$parcelQuery->error."<br/><br/>".$sqlParcelStr);
                    }
                    // Add mawn_parcel_mapping
                    $sqlMawbParcelMappingStr = rtrim($sqlMawbParcelMappingStr,',');
                    $mawbParcelMappingQuery = $dbConnection->query($sqlMawbParcelMappingStr);
                    if(!empty($mawbParcelMappingQuery->error)){
                        $errorFound = true;
                        sendEmail("Error description: " .$mawbParcelMappingQuery->error."<br/><br/>".$sqlMawbParcelMappingStr);
                    }
                    // Add Bagging_parcel_mapping
                    $sqlParcelBaggingMappingStr = rtrim($sqlParcelBaggingMappingStr,',');
                    $parcelBaggingMappingQuery = $dbConnection->query($sqlParcelBaggingMappingStr);
                    if(!empty($parcelBaggingMappingQuery->error)){
                        $errorFound = true;
                        sendEmail("Error description: " .$parcelBaggingMappingQuery->error."<br/><br/>".$sqlParcelBaggingMappingStr);
                    }
                    // Add bagging_service_mapping
                    $sqlBaggingServiceMappingStr = rtrim($sqlBaggingServiceMappingStr,',');
                    $baggingServiceMappingQuery = $dbConnection->query($sqlBaggingServiceMappingStr);
                    if(!empty($baggingServiceMappingQuery->error)){
                        $errorFound = true;
                        sendEmail("Error description: " .$baggingServiceMappingQuery->error."<br/><br/>".$sqlBaggingServiceMappingStr);
                    }
                    // Empty Consignment
                    $sqlStr = "INSERT INTO `consignment` (
                                          user_id,
                                          service_id,
                                          warehouse_id,
                                          shipment_status,
                                          awb,
                                          consignment_status,
                                          hawb,
                                          date_created,
                                          company,
                                          contact,
                                          address_line_1,
                                          address_line_2,
                                          address_line_3,
                                          city,
                                          state,
                                          postcode,
                                          country_id,
                                          telephone,
                                          number_pieces,
                                          weight,
                                          vol_weight,
                                          description,
                                          notes,
                                          `value`,
                                          currency,
                                          sender_name,
                                          sender_company,
                                          sender_telephone,
                                          sender_address_line_1,
                                          sender_address_line_2,
                                          sender_address_line_3,
                                          sender_city,
                                          sender_postcode,
                                          sender_country_id,
                                          sender_state
                                        )
                                        VALUES";
                    // Empty Parcel
                    $sqlParcelStr = 'INSERT INTO    `parcel` (
                                                consignment_id,
                                                tracking_number,
                                               `length`,
                                                width,
                                                height,
                                                weight,
                                                description,
                                                parcel_message,
                                                qty,
                                                itemvalue,
                                                hscode,
                                                pweight,
                                                owe_status_code,
                                                parcel_status_code
                                            )
                                            VALUES';
                    // Empty mawb_parcel_mapping
                    $sqlMawbParcelMappingStr = 'INSERT INTO `mawb_parcel_mapping` (
                                                              `mawb_id`,
                                                              `parcel_id`,
                                                              `wharehouse_id`,
                                                              `bag_id`,
                                                              `date_added`,
                                                              `added_by`) VALUES';
                    // Empty bagging_parcel_mapping
                    $sqlParcelBaggingMappingStr  ='INSERT INTO `parcel_bagging_mapping` (
                                                              `parcel_id`,
                                                              `bag_id`,
                                                              `added_by`,
                                                              `added_date`) 
                                                              VALUES';
                    // Empty bagging_service_mapping
                    $sqlBaggingServiceMappingStr = 'INSERT INTO `bagging_services_mapping` (
                                                              `bag_id`,
                                                              `service_id`) 
                                                                VALUES ';
                    if($count == $totalRecords){
                        $dire = "data_processed";
                        if($errorFound)
                            $dire = "data_error";
                        rename(SETTING_DIR_ASSETS."ebay_data/data_in/".$file, SETTING_DIR_ASSETS."ebay_data/".$dire."/".$file);
                    }

                }
                $count++;
//    $item['conBatch'] = $batchNumber;
//    $item['conId'] = time().$item['UniqueId'];
//    $item['userId'] = $id;
//    $item['ItemInfoList'][0]['conBatch'] = $item['conBatch'];
//    $item['ItemInfoList'][0]['conId'] = $item['conId'];
//    $parcelData[] = $item['ItemInfoList'][0];
//    unset($item['ItemInfoList']);
//    $conData[] = $item;
            }
        }else{
            sendEmail("No data found in this file ".$file,true,$file);
        }
    }
}else{
    sendEmail("No file found in this directory ",true);
}

//
//// Create consignment csv header
//$conHeader = 'TrackingNumber,UniqueId,SwitchToNewTrackingNumber,PackageDesc,PackageDescCn,OrderDate,ConsigneeFirstName,ConsigneeMiddleName,ConsigneeLastName,ConsigneeFullName,ConsigneeBusinessName,ConsigneePhone,ConsigneeCountry,ConsigneeState,ConsigneeCity,ConsigneeDistrict,ConsigneeAddr1,ConsigneeAddr2,ConsigneeAddr3,ConsigneeZipCode,SellerFirstName,SellerMiddleName,SellerLastName,SellerFullName,SellerBusinessName,SellerPhone,SellerCountry,SellerState,SellerCity,SellerDistrict,SellerAddr1,SellerAddr2,SellerAddr3,SellerZipCode,ReturnFirstName,ReturnMiddleName,ReturnLastName,ReturnFullName,ReturnBusinessName,ReturnPhone,ReturnCountry,ReturnState,ReturnCity,ReturnDistrict,ReturnAddr1,ReturnAddr2,ReturnAddr3,ReturnZipCode,ServiceProductId,LastMileServiceCode,PackageTotalWeight,PackageLength,PackageWidth,PackageHeight,PackageVolume,PackageTotalValue,Currency,Battery,Incoterm,SpecialOperDesc1,ImporterEORI,ImportMethod,conBatch,userId,conId';
//$conPath = SETTING_DIR_ASSETS."ebay_data/csv/";
//$conFileName = "con".time().$jsonData['HoauBagId'].".csv";
//generateCsv($conData,$conHeader,$conPath,$conFileName,"consignment");//$data,$headers,$path,$fileName
//// Create parcel csv header
//$parHeader = 'Sku,SkuDesc,SkuDescCn,SkuWeight,SkuValue,Currency,Quantity,Hscode,Link,TxnUnitPrice,TxnQty,SkuListingDesc,conBatch,conId';
//$parPath = SETTING_DIR_ASSETS."ebay_data/csv/";
//$parFileName = "par".time().$jsonData['HoauBagId'].".csv";
//generateCsv($parcelData,$parHeader,$parPath,$parFileName,"parcel");//$data,$headers,$path,$fileName
//
//
//// Load Data into ebay_cron_consignment_temp
//$loadDataSqlCon = "LOAD DATA LOCAL INFILE '".$conPath.$conFileName."' INTO TABLE `ebay_cron_consignment_temp` FIELDS ENCLOSED BY '\"'
//                                        TERMINATED BY ',' LINES TERMINATED BY '\n' IGNORE 1 LINES (
//                                        ".$conHeader."
//                                    )";
//$dbConnection->query($loadDataSqlCon);
//// Load Data into ebay_cron_parcel_temp
//$loadDataSqlPar = "LOAD DATA LOCAL INFILE '".$parPath.$parFileName."' INTO TABLE `ebay_cron_parcel_temp` FIELDS ENCLOSED BY '\"'
//                                        TERMINATED BY ',' LINES TERMINATED BY '\n' IGNORE 1 LINES (
//                                        ".$parHeader."
//                                    )";
//$dbConnection->query($loadDataSqlPar);



//switch (json_last_error()) {
//    case JSON_ERROR_NONE:
//        echo ' - No errors';
//        break;
//    case JSON_ERROR_DEPTH:
//        echo ' - Maximum stack depth exceeded';
//        break;
//    case JSON_ERROR_STATE_MISMATCH:
//        echo ' - Underflow or the modes mismatch';
//        break;
//    case JSON_ERROR_CTRL_CHAR:
//        echo ' - Unexpected control character found';
//        break;
//    case JSON_ERROR_SYNTAX:
//        echo ' - Syntax error, malformed JSON';
//        break;
//    case JSON_ERROR_UTF8:
//        echo ' - Malformed UTF-8 characters, possibly incorrectly encoded';
//        break;
//    default:
//        echo ' - Unknown error';
//        break;
//}

//include_classes([
//    'iaddress.class',
//    'consignment.class',
//    'consignmentfilter.class',
//    'parcel.class',
//    'parcelfilter.class'
//]);
//DbAccess3::connect(SETTING_DB_SERVER, SETTING_DB_USER, SETTING_DB_PASSWORD, SETTING_DB_DATABASE);
//$query =   "SELECT 
//                pbm.bag_id, t.date_created, b.bagnumber, t.tracking_number
//                FROM
//                parcel p INNER JOIN tracking_data t 
//                ON p.id = t.entity_id
//                INNER JOIN parcel_bagging_mapping pbm
//                ON p.id = pbm.parcel_id
//                INNER JOIN bagging b 
//                ON b.id = pbm.bag_id
//                WHERE
//                        t.status_code_id = '140'
//                    AND t.carrier_desc = 'Scan Collected by Receipt Depot'
//                    AND t.date_created >= '" . $startDate . "'
//                    AND t.date_created < '" . $endDate . "' group by pbm.bag_id";
//while ($record = mysqli_fetch_assoc ($rs))
//{
//    $bagNumber = $record['bagnumber'];
//    $dateCreated = $record['date_created'];
//    $dateTime = explode(' ', $dateCreated);
//    $time = explode(':',$dateTime[1]);
//    $hour = $time[0];
//    $randomHour = getScannedRandomHour($hour+1);
//    $scannedDate = date("Y-m-d " . $randomHour . ":" . sprintf("%02d", $randomMinute));
//
//    $arrayData['tracking_number'] = $record['tracking_number'];
//    $arrayData['status_code_id'] = "161";
//    $arrayData['date_created'] = $scannedDate;
//    $arrayData['carrier_desc'] = "Scanned At Dispatch Hub";
//    $arrayData['track_point'] = "United Kingdom";
//
//    if (!mysqli_ping($dbConnection)) {
//        $dbConnection = DbAccess3::connect(SETTING_DB_SERVER, SETTING_DB_USER, SETTING_DB_PASSWORD, SETTING_DB_DATABASE);
//    }
//
//    $insertQueryScanned = "INSERT INTO tracking_data "
//        . " (entity_id , tracking_number,status_code_id, carrier_desc, track_point, date_created, warehouse_id, ip_address) "
//        . "SELECT  "
//        . "     p.id,p.tracking_number,'" . mysqli_escape_string($dbConnection,$arrayData['status_code_id']) . "',"
//        . "     '" . mysqli_escape_string($dbConnection, $arrayData['carrier_desc']) . "','" .
//        mysqli_escape_string($dbConnection, $arrayData['track_point']) . "',"
//        . "     '" . mysqli_escape_string($dbConnection, $arrayData['date_created']) . "',10,'213.246.110.102'"
//        . " FROM "
//        . "     parcel p INNER JOIN tracking_data td "
//        . " ON "
//        . "     td.entity_id = p.id "
//        . " WHERE "
//        . "     p.tracking_number = '" . mysqli_escape_string($dbConnection,$arrayData['tracking_number']) . "' "
//        . "     AND '" . mysqli_escape_string($dbConnection,$arrayData['status_code_id']) .
//        "' not in (select td.status_code_id from tracking_data td where td.entity_id = p.id AND '" .
//        mysqli_escape_string($dbConnection, $arrayData['carrier_desc']) . "' = td.carrier_desc ) "
//        . " LIMIT 1";
//    "<br>";
//    DbAccess3::runQuery($insertQueryScanned);
//}

//foreach ($handling as $servicecode) {
//    $c_filter = new ConsignmentFilter();
//    $total_record = $c_filter->getShipmentWithoutAgent($servicecode);
//    if (count($total_record) > 0) {
//        if ($total_record[0]->getId() > 0) {
//            echo $servicecode;
//            $agentId = ConsignmentFilter::getAgentId(trim($servicecode));
//            if (count($agentId) > 0) {
//                echo $agent_id = $agentId[0]->getAgentId();
//                $c_filter = new ConsignmentFilter();
//                $clist = $c_filter->updateShipmentWithoutAgent($servicecode, $agent_id);
//                break;
//            }
//        } else {
//            echo $servicecode . "continue<br>";
//            continue;
//        }
//    }
//}
//function generateCsv($data,$headers,$path,$fileName,$columType="consignment"){
//    $csv = $headers."\n";//Column headers
//    foreach ($data as $record){
//        if($columType == "consignment") {
//                $csv .= cleanCsvCall($record['TrackingNumber']) . ',' . cleanCsvCall($record['UniqueId']) . ',' . cleanCsvCall($record['SwitchToNewTrackingNumber']) . ',' . cleanCsvCall($record['PackageDesc']) . ',' . cleanCsvCall($record['PackageDescCn']) . ',' . cleanCsvCall($record['OrderDate']) . ',' . cleanCsvCall($record['ConsigneeFirstName']) . ',' . cleanCsvCall($record['ConsigneeMiddleName']) . ',' . cleanCsvCall($record['ConsigneeLastName']) . ',' . cleanCsvCall($record['ConsigneeFullName']) . ',' . cleanCsvCall($record['ConsigneeBusinessName']) . ',' . cleanCsvCall($record['ConsigneePhone']) . ',' . cleanCsvCall($record['ConsigneeCountry']) . ',' . cleanCsvCall($record['ConsigneeState']) . ',' . cleanCsvCall($record['ConsigneeCity']) . ',' . cleanCsvCall($record['ConsigneeDistrict']) . ',' . cleanCsvCall($record['ConsigneeAddr1']) . ',' . cleanCsvCall($record['ConsigneeAddr2']) . ',' . cleanCsvCall($record['ConsigneeAddr3']) . ',' . cleanCsvCall($record['ConsigneeZipCode']) . ',' . cleanCsvCall($record['SellerFirstName']) . ',' . cleanCsvCall($record['SellerMiddleName']) . ',' . cleanCsvCall($record['SellerLastName']) . ',' . cleanCsvCall($record['SellerFullName']) . ',' . cleanCsvCall($record['SellerBusinessName']) . ',' . cleanCsvCall($record['SellerPhone']) . ',' . cleanCsvCall($record['SellerCountry']) . ',' . cleanCsvCall($record['SellerState']) . ',' . cleanCsvCall($record['SellerCity']) . ',' . cleanCsvCall($record['SellerDistrict']) . ',' . cleanCsvCall($record['SellerAddr1']) . ',' . cleanCsvCall($record['SellerAddr2']) . ',' . cleanCsvCall($record['SellerAddr3']) . ',' . cleanCsvCall($record['SellerZipCode']) . ',' . cleanCsvCall($record['ReturnFirstName']) . ',' . cleanCsvCall($record['ReturnMiddleName']) . ',' . cleanCsvCall($record['ReturnLastName']) . ',' . cleanCsvCall($record['ReturnFullName']) . ',' . cleanCsvCall($record['ReturnBusinessName']) . ',' . cleanCsvCall($record['ReturnPhone']) . ',' . cleanCsvCall($record['ReturnCountry']) . ',' . cleanCsvCall($record['ReturnState']) . ',' . cleanCsvCall($record['ReturnCity']) . ',' . cleanCsvCall($record['ReturnDistrict']) . ',' . cleanCsvCall($record['ReturnAddr1']) . ',' . cleanCsvCall($record['ReturnAddr2']) . ',' . cleanCsvCall($record['ReturnAddr3']) . ',' . cleanCsvCall($record['ReturnZipCode']) . ',' . cleanCsvCall($record['ServiceProductId']) . ',' . cleanCsvCall($record['LastMileServiceCode']) . ',' . cleanCsvCall($record['PackageTotalWeight']) . ',' . cleanCsvCall($record['PackageLength']) . ',' . cleanCsvCall($record['PackageWidth']) . ',' . cleanCsvCall($record['PackageHeight']) . ',' . cleanCsvCall($record['PackageVolume']) . ',' . cleanCsvCall($record['PackageTotalValue']) . ',' . cleanCsvCall($record['Currency']) . ',' . cleanCsvCall($record['Battery']) . ',' . cleanCsvCall($record['Incoterm']) . ',' . cleanCsvCall($record['SpecialOperDesc1']) . ',' . cleanCsvCall($record['ImporterEORI']) . ',' . cleanCsvCall($record['ImportMethod']). ',' . cleanCsvCall($record['conBatch']). ',' . cleanCsvCall($record['userId']). ',' . cleanCsvCall($record['conId']) . ',' . "\n"; //Append data to csv
//        }else if ($columType == "parcel"){
//            $csv .= '"'.cleanCsvCall($record['Sku']) . '",' . cleanCsvCall($record['SkuDesc']) . ',' . cleanCsvCall($record['SkuDescCn']) . ',' . cleanCsvCall($record['SkuWeight']) . ',' . cleanCsvCall($record['SkuValue']) . ',' . cleanCsvCall($record['Currency']) . ',' . cleanCsvCall($record['Quantity']) . ',' . cleanCsvCall($record['Hscode']) . ',' . cleanCsvCall($record['Link']) . ',' . cleanCsvCall($record['TxnUnitPrice']) . ',' . cleanCsvCall($record['TxnQty']) . ',' . cleanCsvCall($record['SkuListingDesc']). ',' . cleanCsvCall($record['conBatch']). ',' . cleanCsvCall($record['conId']). ',' . "\n"; //Append data to csv
//        }
//
//    }
//    $fileFullPath = $path.$fileName;
//    $csvHandler = fopen ($fileFullPath,'w');
//    fwrite ($csvHandler,$csv);
//    fclose ($csvHandler);
//}
function sendEmail($messageData,$stop=false,$file=""){
//        $to = "ITSupport@oneworldexpress.com";
//        $from = "ITSupport@oneworldexpress.com";
        $to = "tahir@oneworldexpress.com";
        $from = "tahir@oneworldexpress.com";
        $subject = "EBAY JSON FILE IMPORTED VIA FTP";

        //begin of HTML message
        $message = '<html>
		  <body bgcolor="#DCEEFC">
			
				<b>'.$messageData.'</b><br>
			 </body>
		</html>';
        //end of message
        $headers = "From: $from\r\n";
        $headers .= "Content-type: text/html\r\n";
        // now lets send the email.
        mail($to, $subject, $message, $headers);
        if($stop) {
            if(!empty($file)){
                rename(SETTING_DIR_ASSETS."ebay_data/data_in/".$file, SETTING_DIR_ASSETS."ebay_data/data_error/".$file);
            }
        }
}
?>





