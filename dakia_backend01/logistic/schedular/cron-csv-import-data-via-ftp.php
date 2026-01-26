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
$connectCustom = mysqli_connect(SETTING_DB_SERVER, SETTING_DB_USER, SETTING_DB_PASSWORD);
mysqli_select_db(SETTING_DB_DATABASE, $connectCustom) or die(mysqli_error());



$allFiles = array();
//Open directory to get names of all file.
if ($handle = opendir('/var/www/vhosts/oneworldexpress.co.uk/httpdocs/remote/_assets/china-data/data-in/')) {
//	Reading name of all file in the directory
    while (false !== ($entry = readdir($handle))) {
        if ($entry != "." && $entry != "..") {
            $file = $entry;
            // Reading File start
            $fileName = '/var/www/vhosts/oneworldexpress.co.uk/httpdocs/remote/_assets/china-data/data-in/' . $file;
            $resultArray = '';
            $resultArrayError = array();
            $skipFirstRow = true;
            $allInsertQuery = array();
            $allHawbNumbers = array();
            $allCarrier = array();
            $handleFile = fopen($fileName, "r");
            // Insertion query First Part for identify the column names 
            $inserQuery = "INSERT INTO `consignment` (`consignment_status`,`account`,`awb`,`hawb`,`service`,`handling`,`reference`,`date_submitted`,`date_imported`,`date_received`,`date_booked`,`date_delivered`,`date_exported`,`company`,`contact`,`address_line_1`,`address_line_2`,`address_line_3`,`city`,`country`,`country_iso_code`,`postcode`,`telephone`,`number_pieces`,`weight`,`hv_lv`,`description`,`value`,`currency`,`notes`,`routing_code`,`username`,`service_type`,`both_checked`,`pallet_lifts`,`full_pallet`,`half_pallet`,`quarter_pallet`,`is_doc`,`number_boxes`,`email`,`itemtype`, user_code ) 
			VALUES ";
// Reading column from CSV to import the data.
            $countNumber = 0;
            while (($data = fgetcsv($handleFile, 1000, ",")) !== FALSE) {
                // the condion skipvery first row of the csv. 
                if ($skipFirstRow) {
                    $skipFirstRow = false;
                    continue;
                }
//	echo '<br>me here 1<br>';

                $accountNumber = mysqli_real_escape_string($data[0]);
                $userRecordSet = mysqli_query("SELECT id, user_name, user_account, user_code FROM user WHERE user_account ='" . $accountNumber . "'");
                //$userRecord				=	new UserAccountFilter();
                //$userRecord->addUserAccountFilter($accountNumber);
                ///$getUserList	=	$userRecord->getColumnList("id, user_name");
                if (mysqli_num_rows($userRecordSet) > 0) {
                    $getUserList = mysqli_fetch_assoc($userRecordSet);
                    $username = $getUserList['user_name'];
                    $user_code = $getUserList['user_code'];
                } else {
                    $username = "CronJob";
                    $user_code = 0;
                }
                $hawbNumber = mysqli_real_escape_string($data[1]);
                $Service = mysqli_real_escape_string($data[2]);
                $service_code = mysqli_real_escape_string($data[3]);

                // Getting carrier name and service name from service table
                //$courierRecord			=	new ServiceFilter();
                //$getCourierList			=	$courierRecord->getColumnList("name, code, carrier");
                if (strpos($service_code, "RTN"))
                    $service_s = str_replace("RTN", "", $service_code);
                else
                    $service_s = $service_code;

                $serviceRecordSet = mysqli_query("SELECT name, code, carrier FROM services WHERE code = '" . $service_s . "'");
                if (mysqli_num_rows($serviceRecordSet) > 0) {
                //if(count($getCourierList) > 0)
                    $getCourierList = mysqli_fetch_assoc($serviceRecordSet);
                    $serviceName = $getCourierList['name'];
                    $allCarrier[] = $getCourierList['carrier'];
                } else {
                    $serviceName = "";
                    $allCarrier[] = '';
                }

                $reference = mysqli_real_escape_string($data[4]);
                $date_submitted = mysqli_real_escape_string($data[5]);
                $Company = mysqli_real_escape_string($data[6]);
                $Contact = mysqli_real_escape_string($data[7]);
                $AddressLine1 = mysqli_real_escape_string($data[8]);
                $AddressLine2 = mysqli_real_escape_string($data[9]);
                $AddressLine3 = mysqli_real_escape_string($data[10]);
                $City = mysqli_real_escape_string($data[11]);
                $Country = mysqli_real_escape_string($data[12]);
                if (strlen($Country) == 3) {
                    $Country = mysqli_real_escape_string($data[12]);

                    $countryRecordSet = mysqli_query("SELECT iso, name FROM country WHERE iso = '" . $Country . "'");
                    if (mysqli_num_rows($countryRecordSet) > 0) {
                        $countryList = mysqli_fetch_assoc($countryRecordSet);
                        $Country = $countryList['iso'];
                        $CountryIso = $countryList['name'];
                    }
                } else {
                    $countryRecordSet = mysqli_query("SELECT iso, name FROM country WHERE name = '" . $Country . "'");
                    if (mysqli_num_rows($countryRecordSet) > 0) {
                        $countryList = mysqli_fetch_assoc($countryRecordSet);
                        $CountryIso = $countryList['iso'];
                    }
                }
//	echo '<br>me here2<br>';
                $PostCode = mysqli_real_escape_string($data[13]);
                $Telephone = mysqli_real_escape_string($data[14]);
                $numberOfPieces = mysqli_real_escape_string($data[15]);
                $Weight = mysqli_real_escape_string($data[16]);
                $Description = mysqli_real_escape_string($data[17]);
                $Value = mysqli_real_escape_string($data[18]);
                $Currency = mysqli_real_escape_string($data[19]);
                $notes = mysqli_real_escape_string($data[20]);
                $routingOption = mysqli_real_escape_string($data[21]);
                $FullPallet = mysqli_real_escape_string($data[22]);
                $HalfPallet = mysqli_real_escape_string($data[23]);
                $QuarterPallet = mysqli_real_escape_string($data[24]);
                $BulkWeight = str_replace('||', '%%', mysqli_real_escape_string($data[25]));
                $Width = str_replace('||', '%%', mysqli_real_escape_string($data[26]));
                $Heigh = str_replace('||', '%%', mysqli_real_escape_string($data[27]));
                $Length = str_replace('||', '%%', mysqli_real_escape_string($data[28]));
                if (trim($BulkWeight) != '') {
                    $demin = $BulkWeight . "&&" . $Width . "&&" . $Heigh . "&&" . $Length;
                } else {
                    $demin = '';
                }

                $numberOfBoxes = "";
                $email = mysqli_real_escape_string($data[31]);
                $transactionId = "";
                $itemType = mysqli_real_escape_string($data[29]);
                $trackingNumber = mysqli_real_escape_string($data[30]);
                $date_submitted = date("Y-m-d", strtotime(str_replace("/", "-", $date_submitted)));

                /* $consignmentHawb	=	new	ConsignmentFilter();
                  $consignmentHawb->addHawbExactFilter(trim($hawbNumber));
                  $consignmentHawb->addStatusFilterNotIn('recycled');
                  $getConsignmentHawbData	= $consignmentHawb->getColumnList('id, hawb'); */


                $resultHawb = mysqli_query("SELECT id, hawb FROM consignment WHERE hawb = '" . $hawbNumber . "' and consignment_status not in ('invalid', 'valid', 'recycled')") or die(mysqli_error());



                //if(count($getConsignmentHawbData)<=0)
                if (mysqli_num_rows($resultHawb) <= 0) {
//		echo '<br>me here3<br>';
                    // Array to store the shipment information  and then it will use for query
                    $allInsertQuery[] = "('received','" . $accountNumber . "','" . $trackingNumber . "','" . $hawbNumber . "','" . $Service . "','" . $service_code . "','" . $reference . "','" . $date_submitted . "','" . date("Y-m-d") . "','" . date("Y-m-d") . "',NULL,NULL,NULL,'" . $Company . "','" . $Contact . "','" . $AddressLine1 . "','" . $AddressLine2 . "','" . $AddressLine3 . "','" . $City . "','" . $Country . "','" . $CountryIso . "','" . $PostCode . "','" . $Telephone . "'," . $numberOfPieces . "," . $Weight . ",'LV','" . $Description . "'," . $Value . ",'" . $Currency . "','" . $notes . "','S','" . $username . "','" . $serviceName . "','','','" . $FullPallet . "','" . $HalfPallet . "','" . $QuarterPallet . "','','" . $numberOfBoxes . "','" . $email . "','" . $itemType . "','" . $user_code . "')";


                    $allHawbNumbers[] = $hawbNumber;
                } else {
//		echo '<br>me here4<br>';
                }
                // execute query for 100 shipments 
                if ($countNumber % 50 == 0) {
//		echo '<br>me here5<br>';
                    if (count($allInsertQuery) > 0) {
//			echo '<br>me here6<br>';
                        $valueQueryString = implode(",", $allInsertQuery);
                        $querySting = $inserQuery . $valueQueryString;
                        //		echo "<br>";
//echo $querySting;
                        mysqli_query($querySting, $connectCustom) or die(mysqli_error());
                        //DbAccess3::runQuery($querySting);
                        $allInsertQuery = array();
                    }
                }

                $countNumber++;
            }

            if (count($allInsertQuery) > 0) {
                $valueQueryString = implode(",", $allInsertQuery);
                $querySting = $inserQuery . $valueQueryString;
//	DbAccess3::runQuery($querySting);
                mysqli_query($querySting, $connectCustom) or die(mysqli_error());
                //echo "<br>";
            }
            mysqli_close();


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
// Moving file to data-out folder
            if (copy("/var/www/vhosts/oneworldexpress.co.uk/httpdocs/remote/_assets/china-data/data-in/" . $file, "/var/www/vhosts/oneworldexpress.co.uk/httpdocs/remote/_assets/china-data/data-out/" . date('YmdHis', time()) . " - " . $file)) {
                unlink("/var/www/vhosts/oneworldexpress.co.uk/httpdocs/remote/_assets/china-data/data-in/" . $file);
            }
            DbAccess3::connect(SETTING_DB_SERVER, SETTING_DB_USER, SETTING_DB_PASSWORD, SETTING_DB_DATABASE);

            $ImportErrors = '';
            // Adding Record to subsubsystem	
            foreach ($allHawbNumbers as $key => $hawb) {
                $consignment = new ConsignmentFilter();
                $consignment->addHawbExactFilter($hawb);
                $consignment->addStatusFilter('received');
                $getConsignmentData = $consignment->getColumnList("consignment_status,account,awb,hawb,service,handling,reference,date_submitted,date_imported,date_received,date_booked,date_delivered,date_exported,company,contact,address_line_1,address_line_2,address_line_3,city,country,country_iso_code,postcode,telephone,number_pieces,weight,hv_lv,description,value,currency,notes,routing_code,username,service_type,both_checked,pallet_lifts,full_pallet,half_pallet,quarter_pallet,is_doc,number_boxes,email,itemtype");
                if (count($getConsignmentData) > 0) {
                    /*  Data import starts here  */
                    //Import data to sub systems 
                    $mixedCreator['consignment'] = $getConsignmentData[0];
                    $mixedCreator['password'] = '';
                    $mixedCreator['carrier'] = $allCarrier[$key];

                    $importDataSubsystem = new Importdataapis($mixedCreator); //  Calling the constructor
                    $resultsimportDataSub = $importDataSubsystem->importDataConsignment();
                    $expLoadRe = explode('||', $resultsimportDataSub);
                    if (trim(@$expLoadRe[0]) == "SUCCESS") {

                        $getConsignmentData[0]->save();
                    } else {
                        $ImportErrors .= "HAWB = " . $getConsignmentData[0]->getHawb() . $resultsimportDataSub . "\n\r";
                    }
                }
            }

            if (strlen($ImportErrors) > 0) {

                // logFile  creating 
                $logErrorFile = fopen('/var/www/vhosts/oneworldexpress.co.uk/httpdocs/remote/_assets/china-data/data-log/' . date('YmdHis', time()) . '-error-' . str_replace(".csv", ".txt", $file), 'w');
                fwrite($logErrorFile, $ImportErrors);
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
		
			<b>' . $ImportErrors . '<br>
		 </body>
	</html>';
                //end of message
                $headers = "From: $from\r\n";
                $headers .= "Content-type: text/html\r\n";
                // now lets send the email.
                mail($to, $subject, $message, $headers);
            }
// Redirectory End 
            break;
        }
    }

    closedir($handle);
}

echo "Message has been sent....!";
print "Import done";
?>