<?php

ini_set('default_socket_timeout', 120);
header('Content-Type: text/html; charset=utf-8');
require_once(__DIR__ . "/../includes/settings/config.inc.php");

include_classes([
    'iaddress.class',
    'consignment.class',
    'consignmentfilter.class',
    'parcel.class',
    'parcelfilter.class',
    'importdataapis.class',
	]);
$connectCustom = mysql_connect(SETTING_DB_SERVER, SETTING_DB_USER, SETTING_DB_PASSWORD);
mysql_select_db(SETTING_DB_DATABASE, $connectCustom) or die(mysql_error());

echo '<pre>';
$allFiles = array();
//Open directory to get names of all file.
if ($handle = opendir(SETTING_DIR_REMOTE . "_assets/china-data/data-in/")) {


//	Reading name of all file in the directory
    while (false !== ($entry = readdir($handle))) {
        if ($entry != "." && $entry != "..") {
            $file = $entry;
            // Reading File start
            $fileName = SETTING_DIR_REMOTE . "_assets/china-data/data-in/" . $file;
            $filePart = explode('.', $fileName);
            if (trim($filePart[(count($filePart) - 1)]) != 'csv') {
                $htmlErrors = "Dear All<br><br> Please find the below file. It cannot be processed. " . $fileName . "<br><br><br> Thanks<br><br>Smart System Team";
                $to_error = "ITSupport@oneworldexpress.com";
                $from_error = "ITSupport@oneworldexpress.com";
                $subject_error = "CSV FILE IMPORTED VIA FTP";

                //begin of HTML message
                $message_error = '<html>
				  <body bgcolor="#DCEEFC">
						<b> ' . $htmlErrors . '<br>
					 </body>
				</html>';
                //end of message
                $headers = "From: $from\r\n";
                $headers .= "Content-type: text/html\r\n";
                // now lets send the email.
                mail($to_error, $subject_error, $message_error, $headers);
                continue;
            }

            $resultArray = '';
            $resultArrayError = array();

            $skipFirstRow = true;
            $allInsertQuery = array();
            $allHawbNumbers = array();
            $allCarrier = array();

            $handleFile = fopen($fileName, "r");
            // Insertion query First Part for identify the column names 
            $inserQuery = "INSERT INTO `consignment` (`consignment_status`,`account`,`awb`,`hawb`,`service`,`handling`,`reference`,`date_submitted`,`date_imported`,`date_received`,`date_booked`,`date_delivered`,`date_exported`,`company`,`contact`,`address_line_1`,`address_line_2`,`address_line_3`,`city`,`country`,`country_iso_code`,`postcode`,`telephone`,`number_pieces`,`weight`,`hv_lv`,`description`,`value`,`currency`,`notes`,`routing_code`,`username`,`service_type`,`both_checked`,`pallet_lifts`,`full_pallet`,`half_pallet`,`quarter_pallet`,`is_doc`,`number_boxes`,`email`,`itemtype`,`flight_number`,`bag_number`,`mawb`, user_code ) 
			VALUES ";


            $inserQueryParcel = "INSERT INTO `parcel` (`consignment_id`,`licence_plate`) 
			SELECT id, awb FROM consignment WHERE hawb in ";




//echo '<br>me here<br>';
// Reading column from CSV to import the data.
            $countNumber = 0;
            $allHawbNumbersParcel = array();
            while (($data = fgetcsv($handleFile, 1000, ",")) !== FALSE) {
                // the condion skipvery first row of the csv. 
                if ($skipFirstRow) {
                    $skipFirstRow = false;
                    continue;
                }
//	echo '<br>me here 1<br>';

                $accountNumber = mysql_real_escape_string($data[0]);
                $userRecordSet = mysql_query("SELECT id, user_name, user_account, user_code FROM user WHERE user_account ='" . $accountNumber . "'");
                //$userRecord				=	new UserAccountFilter();
                //$userRecord->addUserAccountFilter($accountNumber);
                ///$getUserList	=	$userRecord->getColumnList("id, user_name");
                if (mysql_num_rows($userRecordSet) > 0) {
                    $getUserList = mysql_fetch_assoc($userRecordSet);
                    $username = $getUserList['user_name'];
                    $user_code = $getUserList['user_code'];
                } else {
                    $username = "CronJob";
                    $user_code = "0";
                }
                $hawbNumber = mysql_real_escape_string($data[1]);
                $Service = mysql_real_escape_string($data[2]);
                $service_code = mysql_real_escape_string($data[3]);

                // Getting carrier name and service name from service table
                //$courierRecord			=	new ServiceFilter();
                //$getCourierList			=	$courierRecord->getColumnList("name, code, carrier");
                $serviceRecordSet = mysql_query("SELECT name, code, carrier FROM services WHERE code = '" . $service_code . "'");
                if (mysql_num_rows($serviceRecordSet) > 0) {
                //if(count($getCourierList) > 0)
                    $getCourierList = mysql_fetch_assoc($serviceRecordSet);
                    $serviceName = $getCourierList['name'];
                    $allCarrier[] = $getCourierList['carrier'];
                    $currentCarrierName = $getCourierList['carrier'];
                } else {
                    $serviceName = "";
                    $allCarrier[] = "";
                    $currentCarrierName = "";
                }

                $reference = mysql_real_escape_string($data[4]);
                $date_submitted = mysql_real_escape_string($data[5]);
                $Company = mysql_real_escape_string($data[6]);
                $Contact = mysql_real_escape_string($data[7]);
                $AddressLine1 = mysql_real_escape_string($data[8]);
                $AddressLine2 = mysql_real_escape_string($data[9]);
                $AddressLine3 = mysql_real_escape_string($data[10]);
                $City = mysql_real_escape_string($data[11]);
                $Country = mysql_real_escape_string($data[12]);
                if (strlen($Country) == 3) {
                    $Country = mysql_real_escape_string($data[12]);

                    $countryRecordSet = mysql_query("SELECT iso, name FROM country WHERE iso = '" . $Country . "'");
                    if (mysql_num_rows($countryRecordSet) > 0) {
                        $countryList = mysql_fetch_assoc($countryRecordSet);
                        $Country = $countryList['iso'];
                        $CountryIso = $countryList['name'];
                    }
                } else {
                    $countryRecordSet = mysql_query("SELECT iso, name FROM country WHERE name = '" . $Country . "'");
                    if (mysql_num_rows($countryRecordSet) > 0) {
                        $countryList = mysql_fetch_assoc($countryRecordSet);
                        $CountryIso = $countryList['iso'];
                    }
                }

                $PostCode = mysql_real_escape_string($data[13]);
                $Telephone = mysql_real_escape_string($data[14]);
                $numberOfPieces = mysql_real_escape_string($data[15]);
                $Weight = mysql_real_escape_string($data[16]);
                $Description = mysql_real_escape_string($data[17]);
                $Value = mysql_real_escape_string($data[18]);
                $Currency = mysql_real_escape_string($data[19]);
                $notes = mysql_real_escape_string($data[20]);
                $routingOption = mysql_real_escape_string($data[21]);
                $FullPallet = mysql_real_escape_string($data[22]);
                $HalfPallet = mysql_real_escape_string($data[23]);
                $QuarterPallet = mysql_real_escape_string($data[24]);
                $BulkWeight = str_replace('||', '%%', mysql_real_escape_string($data[25]));
                $Width = str_replace('||', '%%', mysql_real_escape_string($data[26]));
                $Heigh = str_replace('||', '%%', mysql_real_escape_string($data[27]));
                $Length = str_replace('||', '%%', mysql_real_escape_string($data[28]));
                if (trim($BulkWeight) != '') {
                    $demin = $BulkWeight . "&&" . $Width . "&&" . $Heigh . "&&" . $Length;
                } else {
                    $demin = '';
                }

                $numberOfBoxes = "";
                $email = mysql_real_escape_string($data[31]);
                $transactionId = "";
                $itemType = mysql_real_escape_string($data[29]);
                $trackingNumber = mysql_real_escape_string($data[30]);
                $flightNumber = mysql_real_escape_string($data[32]);
                $bagnumber = mysql_real_escape_string($data[33]);
                $mawb = mysql_real_escape_string($data[34]);

                $date_submitted = date("Y-m-d", strtotime(str_replace("/", "-", $date_submitted)));

                $resultHawb = mysql_query("SELECT id, hawb FROM consignment WHERE hawb = '" . $hawbNumber . "'") or die(mysql_error());

                if (mysql_num_rows($resultHawb) <= 0) {
                    // Array to store the shipment information  and then it will use for query
                    $allInsertQuery[] = "('received','" . $accountNumber . "','" . $trackingNumber . "','" . $hawbNumber . "','" . $Service . "','" . $service_code . "','" . $reference . "','" . $date_submitted . "','" . date("Y-m-d") . "','" . date("Y-m-d") . "',NULL,NULL,NULL,'" . $Company . "','" . $Contact . "','" . $AddressLine1 . "','" . $AddressLine2 . "','" . $AddressLine3 . "','" . $City . "','" . $Country . "','" . $CountryIso . "','" . $PostCode . "','" . $Telephone . "'," . $numberOfPieces . "," . $Weight . ",'LV','" . $Description . "'," . $Value . ",'" . $Currency . "','" . $notes . "','S','" . $username . "','" . $serviceName . "','','','" . $FullPallet . "','" . $HalfPallet . "','" . $QuarterPallet . "','','" . $numberOfBoxes . "','" . $email . "','" . $itemType . "','" . $flightNumber . "','" . $bagnumber . "','" . $mawb . "', '" . $user_code . "')";

                    $insertCarrierArrayQuery[$currentCarrierName][] = "('received','" . $accountNumber . "','" . $trackingNumber . "','" . $hawbNumber . "','" . $Service . "','" . $service_code . "','" . $reference . "','" . $date_submitted . "','" . date("Y-m-d") . "','" . date("Y-m-d") . "',NULL,'" . date("Y-m-d") . "',NULL,NULL,NULL,NULL,NULL,'" . $Company . "','" . $Contact . "','" . $AddressLine1 . "','" . $AddressLine2 . "','" . $AddressLine3 . "','" . $City . "','" . $Country . "','" . $CountryIso . "','" . $PostCode . "','" . $Telephone . "'," . $numberOfPieces . "," . $Weight . ",'LV','" . $Description . "'," . $Value . ",'" . $Currency . "','" . $notes . "','S','" . $flightNumber . "','" . $bagNumber . "','" . $username . "','" . $username . "','" . $packageType . ")";
                    $allHawbNumbers[] = $hawbNumber;
                    $allHawbNumbersParcel[] = $hawbNumber;
                } else {
//		echo '<br>me here4<br>';
                }
                if ($countNumber % 50 == 0) {
                    if (count($allInsertQuery) > 0) {
                        $valueQueryString = implode(",", $allInsertQuery);
                        $querySting = $inserQuery . $valueQueryString;
                        mysql_query($querySting, $connectCustom) or die(mysql_error());
                        $allInsertQuery = array();
                        $hawbParcelData = $inserQueryParcel . "('" . implode("','", $allHawbNumbersParcel) . "');";

                        mysql_query($hawbParcelData, $connectCustom) or die(mysql_error());
                        $allHawbNumbersParcel = array();
                    }
                }

                $countNumber++;
            }

            if (count($allInsertQuery) > 0) {
                $valueQueryString = implode(",", $allInsertQuery);
                $querySting = $inserQuery . $valueQueryString;
                mysql_query($querySting, $connectCustom) or die(mysql_error());

                $hawbParcelData = $inserQueryParcel . "('" . implode("','", $allHawbNumbersParcel) . "');";

                mysql_query($hawbParcelData, $connectCustom) or die(mysql_error());
                $allHawbNumbersParcel = array();
            }
            mysql_close();
            fclose($handleFile);


            if (count($allHawbNumbers) > 0) {
                $htmls = implode("<br />", $allHawbNumbers);
                $to = "ITSupport@oneworldexpress.com";
                $from = "ITSupport@oneworldexpress.com";
                $subject = "CSV FILE IMPORTED VIA FTP";

                //begin of HTML message
                $message = '<html>
	  <body bgcolor="#DCEEFC">
		
			<b> ' . $countNumber . ' consignments has been uploaded to smart system.<br>
		 </body>
	</html>';
                //end of message
                $headers = "From: $from\r\n";
                $headers .= "Content-type: text/html\r\n";
                // now lets send the email.
                mail($to, $subject, $message, $headers);
            }
            echo "Me here 1<br>";
// Moving file to data-out folder
            if (copy(SETTING_DIR_REMOTE . "_assets/china-data/data-in/" . $file, SETTING_DIR_REMOTE . "_assets/china-data/data-out/" . date('YmdHis', time()) . " - " . $file)) {
                unlink(SETTING_DIR_REMOTE . "_assets/china-data/data-in/" . $file);
            }
            $countSubsystemImport = 0;
            $countSubsystemNotImport = 0;
            $subSystemResult = '';
            if (count($insertCarrierArrayQuery) > 0) {
                print_r($insertCarrierArrayQuery);
                foreach ($insertCarrierArrayQuery as $carrierName => $inserArrayToDB) {
                    $carrierNameAmend = strtoupper(str_replace(" ", "-", $carrierName));
                    switch ($carrierNameAmend) {
                        case "REGISTERED-POST":
                            $server = "localhost";
                            $dbUser = "usrRumba21";
                            $dbPassword = "Q1DoUzrs4z";
                            $dbName = "rumba21";
                            $subSystemResult .= connectSubSytemAndProcess($server, $dbUser, $dbPassword, $dbName, $carrierNameAmend, $inserArrayToDB) . "\n";
                            break;

                        case "ROYAL-MAIL":
                            $server = "localhost";
                            $dbUser = "usrRumba8";
                            $dbPassword = "Q1DoUzrs4z";
                            $dbName = "rumba8";
                            $subSystemResult .= connectSubSytemAndProcess($server, $dbUser, $dbPassword, $dbName, $carrierNameAmend, $inserArrayToDB) . "\n";
                            break;
                    }
                }

                if (strlen($subSystemResult) > 0) {
                    // logFile  creating 
                    $logErrorFile = fopen(SETTING_DIR_REMOTE . "_assets/china-data/data-log/" . date('YmdHis', time()) . '-log-' . str_replace(".csv", ".txt", $file), 'w');
                    fwrite($logErrorFile, $subSystemResult);
                    fclose($logErrorFile);
                }

                if (count($allHawbNumbers) > 0) {
                    $htmls = implode("<br />", $allHawbNumbers);
                    $to = "ITSupport@oneworldexpress.com";
                    $from = "ITSupport@oneworldexpress.com";
                    $subject = "CSV DATA IMPORTED TO SUB-SYSTEMS";

                    //begin of HTML message
                    $message = '<html>
		  <body bgcolor="#DCEEFC">
			Total Found in File =	' . $countNumber . '<br>
			Successfully Imported	=	' . $countSubsystemImport . '<br>
			Not Imported	=	' . $countSubsystemNotImport . '<br>
				<b>' . $ImportErrors . '</b><br>
				' . $subSystemResult . '<br>
			 </body>
		</html>';
                    //end of message
                    $headers = "From: $from\r\n";
                    $headers .= "Content-type: text/html\r\n";
                    // now lets send the email.
                    mail($to, $subject, $message, $headers);
                }
            }

// Redirectory End 
            break;
        }
    }

    closedir($handle);
}

//
//	Connect to subsystem and insert the records
//
function connectSubSytemAndProcess($server, $dbUser, $dbPassword, $dbName, $carrierNameAmend, $inserArrayToDB) {
    return;
    global $countSubsystemImport;
    global $countSubsystemNotImport;
    $connectSubSystemCustom = mysql_connect($server, $dbUser, $dbPassword);
    mysql_select_db($dbName, $connectSubSystemCustom) or die(mysql_error());

    switch ($carrierNameAmend) {
        case "REGISTERED-POST":
            // INSERT INTO SUBSYSTEM REGISTER POST
            $inserQueryRegisteredPost = "INSERT INTO `consignment` (`consignment_status`,`account`,`awb`,`hawb`,`service`,`handling`,`reference`,`date_submitted`,`date_imported`,`date_printed`,`printed_file_id`,`date_received`,`date_booked`,`date_delivered`,`date_returned`,`booked_file_id`,`date_exported`,`company`,`contact`,`address_line_1`,`address_line_2`,`address_line_3`,`city`,`country`,`country_iso_code`,`postcode`,`telephone`,`number_pieces`,`weight`,`hv_lv`,`description`,`value`,`currency`,`notes`,`routing_code`,`flight_number`,`bag_number`,`sender_name`,`username`,`packagetype` ) VALUES ";
            $valueQueryString = implode(",", $inserArrayToDB);
            $querySting = $inserQueryRegisteredPost . $valueQueryString;

            break;

        case "ROYAL-MAIL":
            // INSERT INTO SUBSYSTEM REGISTER POST
            $inserQueryRegisteredPost = "INSERT INTO `consignment` (`consignment_status`,`account`,`awb`,`hawb`,`service`,`handling`,`reference`,`date_submitted`,`date_imported`,`date_printed`,`printed_file_id`,`date_received`,`date_booked`,`date_delivered`,`date_returned`,`booked_file_id`,`date_exported`,`company`,`contact`,`address_line_1`,`address_line_2`,`address_line_3`,`city`,`country`,`country_iso_code`,`postcode`,`telephone`,`number_pieces`,`weight`,`hv_lv`,`description`,`value`,`currency`,`notes`,`routing_code`,`flight_number`,`bag_number`,`sender_name`,`username`,`packagetype`) VALUES  ";
            $valueQueryString = implode(",", $inserArrayToDB);
            $querySting = $inserQueryRegisteredPost . $valueQueryString;

            break;
    }
    // VERIFY INSERTED RECORD TO SUBSYSTEM
    if (mysql_query($querySting, $connectSubSystemCustom)) {
        $generalResult = "SUCCESS||" . $carrierNameAmend . "||" . count($inserArrayToDB) . " Record has been added to database";
        $countSubsystemImport += count($inserArrayToDB);
    } else {
        $generalResult = "ERROR||" . $carrierNameAmend . "||" . mysql_error() . " ";
        $countSubsystemNotImport += count($inserArrayToDB);
    }
    mysql_close($connectSubSystemCustom);
    return $generalResult;
}

echo "Message has been sent....!";
print "Import done";
?>