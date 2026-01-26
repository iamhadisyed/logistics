<?php

//error_reporting(E_ALL & ~(E_NOTICE|E_WARNING));
require_once(__DIR__ . "/../includes/settings/config.inc.php");
include_classes([
    'bagging.class',
    'mawbfilter.class',
    'mawb.class',
    'services.class',
    'servicefilter.class',
    'parcel.class',
    'parcelfilter.class',
    'parcelbaggingmapping.class',
    'parcelbaggingmappingfilter.class',
    'baggingservicesmapping.class',
    'mawbparcelmapping.class',
    'userservicesrouting.class',
    'userservicesroutingfilter.class',
    'customizedservicesrouting.class',
    'customizedservicesroutingfilter.class',
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
// gets directories and files
if ($handle = opendir(SETTING_DIR_ASSETS."user_data_csv/")) {
    while (false !== ($entry = readdir($handle))) {
        if($entry =='.' || $entry =='..'){
        }else{
            $filesData = scandir(SETTING_DIR_ASSETS."user_data_csv/".$entry);
            $files = array_diff($filesData, array('..', '.'));
            if(count($files) > 0){
                if (!mysqli_ping($dbConnection)) {
                    $dbConnection = DbAccess3::connect(SETTING_DB_SERVER, SETTING_DB_USER, SETTING_DB_PASSWORD, SETTING_DB_DATABASE);
                }
                $accountdirect	=	'';

                $accountdirect	=	$entry;

                $query =   "SELECT
                                id,user_pass,warehouse_id
                                FROM
                                `user`
                                WHERE
                                    user_name = '$accountdirect'
                                    AND is_deleted != 1 
                                    AND active_flag = 1 
                                    AND is_tc_agreed = 'y'  
                                    ";
                $queryData = $dbConnection->query($query);
                if (mysqli_num_rows($queryData) > 0) {
                    while ($record = mysqli_fetch_assoc ($queryData)){
                        $userPass = $record['user_pass'];
                        $userId = $record['id'];
                        $warehouseId = $record['warehouse_id'];
                        $userAccountId = $record['user_account_id'];
                    }
                }else{
                    sendEmail("No user found against username $entry",true,'',$entry);
                }
                foreach ($files as $file) {
                    $errorFound = false;
                    // load csv file
                    echo SETTING_DIR_ASSETS . "user_data_csv/" . $entry."/" . $file . "<br>";
                    $csvString = file_get_contents(SETTING_DIR_ASSETS . "user_data_csv/" . $entry ."/". $file);
                    $csvData = "";
                    $csvData = explode("<br />", nl2br($csvString));
                    foreach($csvData as $k => $array) {
                        $csvData[$k] = explode(',',$array);
                    }

                    echo "<pre>";
                    print_r($csvData);
                    echo "</pre>";
                    die;


                    if(!empty($csvData)){
                        $count = 1;
                        $totalRecords = count($csvData);
                        $sqlStr = "INSERT INTO `consignment` (
                                                          user_id,
                                                          service_id,
                                                          warehouse_id,
                                                          shipment_status,
                                                          awb,
                                                          consignment_status,
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
                                                          sender_state,
                                                          hawb,
                                                          sender_email,
                                                          email,
                                                          itemtype,
                                                          description,
                                                          routing_code,
                                                          vol_demonimator,
                                                          customized_service_id,
                                                          weight_type,
                                                          charge_weight,
                                                          vol_weight,
                                                          agent_id,
                                                          date_label_created)
                                                        VALUES() ";
                        $sqlParcelStr = 'INSERT INTO    `parcel` (
                                                                consignment_id,
                                                                tracking_number,
                                                               `length`,
                                                                width,
                                                                height,
                                                                weight,
                                                                description,
                                                                owe_status_code,
                                                                parcel_status_code)
                                                            VALUES() ';
                        $mawbBaggingArr = [];
                        $mawbIdArr = [];
                        $bagIdArr = [];
                        foreach ($csvData as $arr) {
                            if(count($arr)> 1){
                                $agentTrackingNumber = $arr[0];
                                $mawb = $arr[1];
                                $masterDate = $arr[2];
                                $flight = $arr[3];
                                $hawb = $arr[4];
                                $cient = $arr[5];
                                $shipper = $arr[6];
                                $originCountry = $arr[7];
                                $shipperAddress = $arr[8];
                                $shipperCity = $arr[9];
                                $shipperZip = $arr[10];
                                $consignee = $arr[11];
                                $cneeTel = $arr[12];
                                $cneeAddress = $arr[13];
                                $cneeAddress2 = $arr[14];
                                $cneeAddress3 = $arr[15];
                                $contact = $arr[16];
                                $cneeCity = $arr[17];
                                $cneeZip = $arr[18];
                                $pieces = $arr[19];
                                $contents = $arr[20];
                                $value = $arr[21];
                                $deadWeigth = $arr[22];
                                $volWeight = $arr[23];
                                $notes = $arr[24];
                                $countryCode = $arr[25];
                                $jobId = $arr[26];
                                $destCountryName = $arr[27];
                                $serviceCode = $arr[28];
                            $dateParts = explode("-",$masterDate);
                            if(count($dateParts)>0){
                                if(strlen($dateParts[2])== 4){
                                    $LabelCreatedDate = $dateParts[2]."-".$dateParts[0]."-".$dateParts[1];
                                } else {
                                    $LabelCreatedDate = $masterDate;
                                }
                            }else{
                                if(trim($masterDate)!= '')
                                    $LabelCreatedDate = $masterDate;
                                else
                                    $LabelCreatedDate = '';
                            }

                            $mawb = substr_replace(str_replace(' ', '', $mawb),"-",3,0);
                            // Check source country id
                            if(!empty($mawb)) {
                                // check if mawb is already exist
                                $mawbFilter = new MawbFilter();
                                $mawbFilter->addFilter("    mawb_number='" . DbAccess3::escape($mawb) . "'");
                                $mawbFilterObj = $mawbFilter->getList("id,mawb_status");
                                if (count($mawbFilterObj) > 0) {
                                    $mawbStatus = $mawbFilterObj[0]->getMawbStatus();// if o then its open
                                    $mawbId = $mawbFilterObj[0]->getId();
                                    if ($mawbStatus == 'd') {
                                        sendEmail("MAWB is dispatched " . $mawb);
                                    }
                                } else {
                                    $mawbObj = new Mawb();
                                    $mawbObj->setMawbNumber($mawb);
                                    $mawbObj->setIsActive('y');
                                    $mawbObj->setAddedDate(date("Y-m-d H:i:s", time()));
                                    $mawbObj->setUpdatedDate(date("Y-m-d H:i:s", time()));
                                    $mawbObj->setAddedBy($userId);
                                    $mawbObj->save();
                                    $mawbId = $mawbObj->getId();
                                }
                                $mawbIdArr[$mawb] = $mawbId;

                            }
                            // check service id exist LastMileServiceCode
                            $serviceFilter = new ServiceFilter();
                            $serviceFilter->addFilter("    code='".DbAccess3::escape($serviceCode)."'");
                            $serviceFilterObj = $serviceFilter->getList();
                            if(count($serviceFilterObj) > 0){
                                $serviceName = $serviceFilterObj[0]->getName();// if o then its open
                                $serviceId = $serviceFilterObj[0]->getId();
                                $volumetricDenominatorService = $serviceFilterObj[0]->getVolumetricDenominator();
                                $girth = $serviceFilterObj[0]->getGirth();
                                $serviceGrithFormula = $serviceFilterObj[0]->getGirthFormula();
                            }else{
                                sendEmail("service not found in system ".$serviceCode,true,$file,$entry);
                            }
                            $customizedServicesRouting = NULL;
                            if($serviceFilterObj[0]->getIsCustomized() == "1"){
                                $customizedServicesRoutingFilter = new CustomizedServicesRoutingFilter();
                                $customizedServicesRoutingFilter->addJoin('services s', 's.id=csr.service_id');
                                $customizedServicesRoutingFilter->addFieldFilter('customize_service_id', $serviceId);
                                $customizedServicesRoutingFilter->addFieldFilter('status', '1');
                                $customizedServicesRouting = $customizedServicesRoutingFilter->getColumnList("csr.service_id,csr.from_weight,csr.to_weight,s.volumetric_denominator");
                                if(count($customizedServicesRouting) > 0){
                                    $weightNotFound = true;
                                    $newServiceId = "";
                                    $customizedServiceId = "";
                                    $volDemonimator = "";
                                    $routingCode = "S";
                                    if($weightNotFound){

                                    }
                                }else{
                                    $volDemonimator = 5000;
                                }
                            }else{
                                $customizedServiceId = '';
                                $newServiceId = $serviceId;
                                $volDemonimator = $volumetricDenominatorService;
                                $routingCode = 'S';
                            }
                            $maxvolweight = 0;
                            $totalvolweight = 0;
                            $brandNewServiceObj = new Services($newServiceId);
                            $brandNewServiceWeightType = $brandNewServiceObj->getWieghtType(); // 1 for pp and 2 for shipment
                            $weightType = 'PS';
                            if($brandNewServiceWeightType)
                                $weightType = 'PP';
                            foreach ($csvData['parcel'] as $parelData) {
                                $parcelWeight = $parelData['weight'];
                                $parcelLength = $parelData['length'];
                                $parcelWidth = $parelData['width'];
                                $parcelHeight = $parelData['height'];
                                $itemDescription = $parelData['items'];
                                if(!empty($volDemonimator) && $volDemonimator > 0){
                                    $volweight = (($parcelWidth * $parcelHeight * $parcelLength) / $volDemonimator);
                                    if($maxvolweight < $volweight)
                                        $maxvolweight = $volweight;
                                    $totalvolweight = $totalvolweight + $volweight;
                                }
                            }
                            // Check if service is agreed
                            $userServicesRoutingFilter = new UserServicesRoutingFilter();
                            $userServicesRoutingFilter->addFieldFilter("user_account_id", $userAccountId);
                            if($customizedServiceId == 0 || $customizedServiceId == '')
                                $userServicesRoutingFilter->addFieldFilter("service_id", $newServiceId);
                            else
                                $userServicesRoutingFilter->addFieldFilter("service_id", $customizedServiceId);

                            $userServicesRouting = $userServicesRoutingFilter->getColumnList("is_agreed,from_weight,to_weight,is_over_label");

                            if (!mysqli_ping($dbConnection)) {
                                $dbConnection = DbAccess3::connect(SETTING_DB_SERVER, SETTING_DB_USER, SETTING_DB_PASSWORD, SETTING_DB_DATABASE);
                            }


//echo $receiverCountryId."<br>";
//echo $count." % 1000 == 0 || ".$count. "== ".$totalRecords."<br>";
//                                ,
//                                agent_id,
//                                                          date_label_created
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
                                foreach ($mawbBaggingArr as $mawbNumber => $mawbData) {
                                    $mawbId = $mawbIdArr[$mawbNumber];
                                    foreach ($mawbData as $bagNumber => $parcelBagData) {
                                        $bagId = $bagIdArr[$bagNumber];
                                        if($bagId > 0){
                                            foreach ($parcelBagData as $parcelTracking) {
                                                $parcelFilter = new ParcelFilter();
                                                $parcelFilter->addFieldFilter('    tracking_number',$parcelTracking);
                                                $parcelFilterObj = $parcelFilter->getColumnList('id');
                                                if(count($parcelFilterObj) > 0){
                                                    $parcelBaggingMapping = new ParcelBaggingMapping();
                                                    $parcelBaggingMapping->setParcelId($parcelFilterObj[0]->getId());
                                                    $parcelBaggingMapping->setBagId($bagId);
                                                    $parcelBaggingMapping->setAddedBy($userId);
                                                    $parcelBaggingMapping->setAddedDate(time());
                                                    $parcelBaggingMapping->save();

                                                    $baggingServiceMapping = new BaggingServicesMapping();
                                                    $baggingServiceMapping->setBagId($bagId);
                                                    $baggingServiceMapping->setServiceId($serviceId);
                                                    $baggingServiceMapping->save();

                                                    if($mawbId > 0){
                                                        $mawbParcelMapping = new MawbParcelMapping();
                                                        $mawbParcelMapping->setMawbId($mawbId);
                                                        $mawbParcelMapping->setParcelId($parcelFilterObj[0]->getId());
                                                        $mawbParcelMapping->setWharehouseId($warehouseId);
                                                        $mawbParcelMapping->setBagId($bagId);
                                                        $mawbParcelMapping->setDateAdded(time());
                                                        $mawbParcelMapping->setAddedBy($userId);
                                                        $mawbParcelMapping->save();
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                                // Empty Consignment
                                $sqlStr = "INSERT INTO `consignment` (
                                                          user_id,
                                                          service_id,
                                                          warehouse_id,
                                                          shipment_status,
                                                          awb,
                                                          consignment_status,
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
                                                          sender_state,
                                                          hawb,
                                                          sender_email,
                                                          email,
                                                          itemtype,
                                                          description,
                                                          routing_code,
                                                          vol_demonimator,
                                                          customized_service_id,
                                                          weight_type,
                                                          charge_weight,
                                                          vol_weight,
                                                          agent_id,
                                                          date_label_created
                                                        )
                                                        VALUES()";
                                // Empty Parcel
                                $sqlParcelStr = 'INSERT INTO    `parcel` (
                                                                consignment_id,
                                                                tracking_number,
                                                               `length`,
                                                                width,
                                                                height,
                                                                weight,
                                                                description,
                                                                owe_status_code,
                                                                parcel_status_code)
                                                            VALUES()';
                                if($count == $totalRecords){
                                    $dire = "data_processed";
                                    if($errorFound)
                                        $dire = "data_error";
                                    rename(SETTING_DIR_ASSETS."user_data_csv/".$entry."/".$file, SETTING_DIR_ASSETS."user_data_csv/".$entry."/".$dire."/".$file);
                                }
                            }
                            $count++;
                            }
                        }
                        rename(SETTING_DIR_ASSETS."user_data_csv/".$entry."/".$file, SETTING_DIR_ASSETS."user_data_csv/".$entry. $file);
                    }else{
                        sendEmail("No data found in this file ".$file,true,$file,$entry);
                    }
                }
            }else{
                ///sendEmail("No file found in this directory ".print_r( $filesData,true),true,'',$entry);
            }
        }
    }
    closedir($handle);
}



function sendEmail($messageData,$stop=false,$file="",$folder=""){
    $to = "ITSupport@oneworldexpress.com";
//        $from = "ITSupport@oneworldexpress.com";
//        $to = "tahir@oneworldexpress.com";
    $from = "smart@smarttrack.co";
    $subject = "Cron JSON FILE IMPORTED VIA FTP";

    //begin of HTML message
    echo  $message = '<html>
		  <body bgcolor="#DCEEFC">
			
				<b>'.$messageData.'</b><br>
			 </body>
		</html>';

    //end of message
    $headers = "From: $from\r\n";
    $headers .= "Content-type: text/html\r\n";
    // now lets send the email.
    mail($to, $subject, $message, $headers);
}
?>