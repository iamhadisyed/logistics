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

if ($handle = opendir(SETTING_DIR_ASSETS . "user_data_csv/")) {
    while (false !== ($entry = readdir($handle))) {
        if ($entry != "." && $entry != "..") {
            if (in_array($entry, ["kangaroo", 'owe', 'pivotal',
                        'deutest',
                        'dpost',
                        'profm',
                        'ukmell',
                        'dpost',
                        'agora',
                        'extfli',
                        'itsship',
                        'rad2',
                        'rad',
                        'kaabpro',
                        'rake&revo',
                        'exp1',
                        'rev',
                        '5269903623',
                        '5268638661',
                        '5262115660',
                        '5272724929',
                        'yourclothings',
                        'rangeplus'])) {
                /* make dir if not exist */
                if (!file_exists(SETTING_DIR_ASSETS . "user_data/" . $entry)) {
                    mkdir(SETTING_DIR_ASSETS . "user_data/" . $entry, 0777, true);
                }
                if (!file_exists(SETTING_DIR_ASSETS . "user_data/" . $entry . "/data_in")) {
                    mkdir(SETTING_DIR_ASSETS . "user_data/" . $entry . "/data_in", 0777, true);
                }
                if (!file_exists(SETTING_DIR_ASSETS . "user_data/" . $entry . "/data_processed")) {
                    mkdir(SETTING_DIR_ASSETS . "user_data/" . $entry . "/data_processed", 0777, true);
                }
                if (!file_exists(SETTING_DIR_ASSETS . "user_data/" . $entry . "/data_error")) {
                    mkdir(SETTING_DIR_ASSETS . "user_data/" . $entry . "/data_error", 0777, true);
                }
                if (!file_exists(SETTING_DIR_ASSETS . "user_data/" . $entry . "/data_out")) {
                    mkdir(SETTING_DIR_ASSETS . "user_data/" . $entry . "/data_error", 0777, true);
                }

                if (!file_exists(SETTING_DIR_ASSETS . "user_data_csv/" . $entry)) {
                    mkdir(SETTING_DIR_ASSETS . "user_data_csv/" . $entry, 0777, true);
                }
                if (!file_exists(SETTING_DIR_ASSETS . "user_data_csv/" . $entry . "/data_in")) {
                    mkdir(SETTING_DIR_ASSETS . "user_data_csv/" . $entry . "/data_in", 0777, true);
                }
                if (!file_exists(SETTING_DIR_ASSETS . "user_data_csv/" . $entry . "/data_processed")) {
                    mkdir(SETTING_DIR_ASSETS . "user_data_csv/" . $entry . "/data_processed", 0777, true);
                }
                if (!file_exists(SETTING_DIR_ASSETS . "user_data_csv/" . $entry . "/data_error")) {
                    mkdir(SETTING_DIR_ASSETS . "user_data_csv/" . $entry . "/data_error", 0777, true);
                }
                if (!file_exists(SETTING_DIR_ASSETS . "user_data_csv/" . $entry . "/data_out")) {
                    mkdir(SETTING_DIR_ASSETS . "user_data_csv/" . $entry . "/data_error", 0777, true);
                }
                /* make dir if not exist */
                $filesData = scandir(SETTING_DIR_ASSETS . "user_data_csv/" . $entry . "/data_in");
                $files = array_diff($filesData, array('..', '.'));
                if (count($files) > 0) {
                    if (!mysqli_ping($dbConnection)) {
                        $dbConnection = DbAccess3::connect(SETTING_DB_SERVER, SETTING_DB_USER, SETTING_DB_PASSWORD, SETTING_DB_DATABASE);
                    }
                    $accountdirect = '';
                    $accountdirect = $entry;

                    $query = "SELECT
                                user.id,user.user_pass,user.warehouse_id,user.user_account_id,
                                user.first_name,
                                user.phone,
                                user.email,
                                user.address,
                                user.address_2,
                                user.address_3,
                                user.city,
                                user.postcode

                            FROM
                                `user` INNER JOIN user_account ON user.user_account_id = user_account.id
                                WHERE
                                    LOWER(user_account.user_account) = LOWER('" . $accountdirect . "')
                                    AND user.is_deleted != 1 
                                    AND user_account.active_flag = 1 
                                    AND user.is_tc_agreed = 'y'  
                                    ";

                    $queryData = $dbConnection->query($query);
                    $userPass = '';
                    $userId = '';
                    $warehouseId = '';
                    $userAccountId = '';
                    if (mysqli_num_rows($queryData) > 0) {
                        while ($record = mysqli_fetch_assoc($queryData)) {
                            $userPass = $record['user_pass'];
                            $userId = $record['id'];
                            $warehouseId = $record['warehouse_id'];
                            $userAccountId = $record['user_account_id'];
                            $sender_contact = $sender_company = $sender_name = $record['first_name'];
                            $sender_telephone = $record['phone'];
                            $sender_email = $record['email'];
                            $sender_address_line_1 = $record['address'];
                            $sender_address_line_2 = $record['address_2'];
                            $sender_address_line_3 = $record['address_3'];
                            $sender_city = $record['city'];
                            $sender_postcode = $record['postcode'];
                        }
                    } else {
                        sendEmail("No user found against username $entry", true, '', $entry);
                    }
                    foreach ($files as $file) {
                        $errorFound = false;
                        // load json file
                        // echo SETTING_DIR_ASSETS."user_data_csv/".$entry."/data_in/".$file."<br>";
                        $filePath = SETTING_DIR_ASSETS . "user_data_csv/" . $entry . "/data_in/" . $file;
                        $row = 1;
                        $finalArray = [];
                        if (($handle = fopen($filePath, "r")) !== FALSE) {
                            while (($data = fgetcsv($handle, 1000000)) !== FALSE) {

                                if ($row > 1) {
                                    $data = array_map("convertUtf8",$data );
                                    
                                    if ($entry == "kangaroo") {

                                        $sender_country_iso = $data['7'];
                                        $itemyype = $data['29'];
                                        if(strtoupper($itemyype) == "DOX")
                                            $service_code = "STDHL0DOX";
                                        else
                                            $service_code = "STDHL0WPX";
                                        $order_reference = $data['4'];
                                        if ($order_reference == "") {
                                            $order_reference = 'csv_' . strtolower($accountdirect) . '_' . time();
                                        }
                                        $tracking_number = $data['0'];
                                        $sender_contact = $data['6'];
                                        $sender_email = "";
                                        $sender_company = $data['6'];
                                        $sender_name = $data['6'];
                                        $s_address = $data['8'];
                                        $addressArr = explode("\n", wordwrap($s_address, 24));
                                        $sender_address_line_1 = isset($addressArr['0']) ? $addressArr['0'] : '';
                                        $sender_address_line_2 = isset($addressArr['1']) ? $addressArr['1'] : '';
                                        ;
                                        $sender_address_line_3 = isset($addressArr['2']) ? $addressArr['2'] : '';
                                        ;
                                        $sender_city = $data['9'];
                                        $sender_state = "";
                                        $sender_postcode = $data['10'];
                                        $sender_telephone = "";
                                        $receiver_country_iso = $data['25'];
                                        $receiver_contact = $data['16'];
                                        if ($receiver_contact == "") {
                                            $receiver_contact = $data['11'];
                                        }
                                        $receiver_email = "";
                                        $receiver_company = $data['11'];
                                        $receiver_telephone = $data['12'];
                                        $receiver_address_line_1 = $data['13'];
                                        $receiver_address_line_2 = '';
                                        if (strlen($receiver_address_line_1) > 28) {
                                            $receiverAddressArr = explode("\n", wordwrap($receiver_address_line_1, 28));
                                            $receiver_address_line_1 = $receiverAddressArr[0];
                                            $receiver_address_line_2 = isset($receiverAddressArr[1]) ? ' ' . $receiverAddressArr[1] : '';
                                            $receiver_address_line_2 .= isset($receiverAddressArr[2]) ? ' ' . $receiverAddressArr[2] : '';
                                            $receiver_address_line_2 .= isset($receiverAddressArr[3]) ? ' ' . $receiverAddressArr[3] : '';
                                        }
                                        $receiver_address_line_2 .= ' ' . $data['14'];
                                        $receiver_address_line_3 = '';
                                        if (strlen($receiver_address_line_2) > 28) {
                                            $receiverAddressArr2 = explode("\n", wordwrap($receiver_address_line_1, 28));
                                            $receiver_address_line_3 = $receiverAddressArr2[0];
                                            $receiver_address_line_3 .= isset($receiverAddressArr2[1]) ? ' ' . $receiverAddressArr2[1] : '';
                                            $receiver_address_line_3 .= isset($receiverAddressArr2[2]) ? ' ' . $receiverAddressArr2[2] : '';
                                            $receiver_address_line_3 .= isset($receiverAddressArr2[3]) ? ' ' . $receiverAddressArr2[3] : '';
                                        }
                                        $receiver_address_line_3 .= $data['15'];
                                        $receiver_city = $data['17'];
                                        $receiver_state = "";
                                        $receiver_postcode = $data['18'];
                                        $goodsValue = explode(" ", $data['21']);
                                        $value = $goodsValue["0"];
                                        $currency = (trim(@$goodsValue[1]) == "" ? "GBP" : @$goodsValue[1]);
                                        $itemyype = $data['29'];
                                        $notes = $data['24'];
                                        $description = $data['20'];
                                        $bag_number = "";
                                        $mawb = $data['1'];
                                        $parcel_tracking_number = $data['0'];
                                        $vol_weight = $data['23'];
                                        $no_of_pieces = $data['19'];
                                        $parcel_weight = $vol_weight;
                                        if ($vol_weight > 0 && $no_of_pieces > 0) {
                                            $parcel_weight = formatNumber($vol_weight / $no_of_pieces, 3);
                                        }
                                        $parcel_length = "1";
                                        $parcel_width = "1";
                                        $parcel_height = "1";
                                        $item_description = "";
                                        $item_no_of_items = "1";
                                        $item_item_value = $value;
                                        $item_weight = $parcel_weight;
                                        $item_tariff_no = "";
                                        $item_hscode = "";
                                        $item_manufacture_country_iso = $sender_country_iso;
                                        $shipment_type = "D";
                                        $collection_end_time = "";
                                        $collection_start_time = "";
                                        $collection_date = date('Y-m-d');
                                        $itemArr = [];
                                        $itemArr[] = [
                                            "item_description" => $description,
                                            "no_of_items" => $item_no_of_items,
                                            "item_value" => $item_item_value,
                                            "weight" => $item_weight,
                                            "tariff_no" => $item_tariff_no,
                                            "hscode" => $item_hscode,
                                            "manufacture_country_iso" => $item_manufacture_country_iso
                                        ];
                                        $parcelArr = [];
                                        for ($parcelArrya = 0; $parcelArrya < $no_of_pieces; $parcelArrya++) {
                                            $parcelArr[] = [
                                                "tracking_number" => $parcel_tracking_number,
                                                "weight" => $parcel_weight,
                                                "length" => $parcel_length,
                                                "width" => $parcel_width,
                                                "height" => $parcel_height,
                                                "items" => $itemArr,
                                            ];
                                        }
                                    } else if (in_array($entry, ['pivotal',
                                                'deutest',
                                                'dpost',
                                                'profm',
                                                'ukmell',
                                                'dpost',
                                                'agora',
                                                'extfli',
                                                'itsship',
                                                'rad2',
                                                'rad',
                                                'kaabpro',
                                                'rake&revo',
                                                'exp1',
                                                'rev',
                                                '5269903623',
                                                '5268638661',
                                                '5262115660',
                                                '5272724929',
                                                'yourclothings',
                                                'rangeplus', 'owe'])) {

                                        $collection_date = str_replace(".", "-", $data['0']);
                                        $sender_country_iso = $data['1'];
                                        $receiver_country_iso = $data['2'];

                                        $service_code = $data['3'];
                                        $order_reference = $data['4'];
                                        if ($order_reference == "") {
                                            $order_reference = 'csv_' . strtolower($accountdirect) . '_' . time();
                                        }
                                        $sender_company = trim($data['5']) <> '' ? $data['5'] : $sender_company;
                                        $sender_contact = trim($data['6']) <> '' ? $data['6'] : $sender_contact;
                                        $sender_email = trim($data['7']) <> '' ? $data['7'] : $sender_email;
                                        $sender_telephone = trim($data['8']) <> '' ? $data['8'] : $sender_telephone;
                                        $sender_name = trim($data['6']) <> '' ? $data['6'] : $sender_name;
                                        //$s_address = $data['8'];
                                        //$addressArr = explode("\n", wordwrap($s_address, 24));
                                        $sender_address_line_1 = trim($data['9']) <> '' ? $data['9'] : $sender_address_line_1; //= isset($addressArr['0']) ? $addressArr['0'] : '';
                                        $sender_address_line_2 = trim($data['10']) <> '' ? $data['10'] : $sender_address_line_2; //= isset($addressArr['1']) ? $addressArr['1'] : '';
                                        ;
                                        $sender_address_line_3 = trim($data['11']) <> '' ? $data['11'] : $sender_address_line_3; //= isset($addressArr['2']) ? $addressArr['2'] : '';
                                        ;
                                        $sender_city = trim($data['12']) <> '' ? $data['12'] : $sender_city;
                                        $sender_state = $data['13'];
                                        $sender_postcode = trim($data['14']) <> '' ? $data['14'] : $sender_postcode;


                                        $receiver_company = $data['15'];
                                        $receiver_contact = $data['16'];
                                        $receiver_email = $data['17'];

                                        $receiver_telephone = $data['18'];
                                        $receiver_address_line_1 = $data['19'];
                                        $receiver_address_line_2 = '';
                                        if (strlen($receiver_address_line_1) > 28) {
                                            $receiverAddressArr = explode("\n", wordwrap($receiver_address_line_1, 28));
                                            $receiver_address_line_1 = $receiverAddressArr[0];
                                            $receiver_address_line_2 = isset($receiverAddressArr[1]) ? ' ' . $receiverAddressArr[1] : '';
                                            $receiver_address_line_2 .= isset($receiverAddressArr[2]) ? ' ' . $receiverAddressArr[2] : '';
                                            $receiver_address_line_2 .= isset($receiverAddressArr[3]) ? ' ' . $receiverAddressArr[3] : '';
                                        }
                                        $receiver_address_line_2 .= ' ' . $data['20'];
                                        $receiver_address_line_3 = '';
                                        if (strlen($receiver_address_line_2) > 28) {
                                            $receiverAddressArr2 = explode("\n", wordwrap($receiver_address_line_1, 28));
                                            $receiver_address_line_3 = $receiverAddressArr2[0];
                                            $receiver_address_line_3 .= isset($receiverAddressArr2[1]) ? ' ' . $receiverAddressArr2[1] : '';
                                            $receiver_address_line_3 .= isset($receiverAddressArr2[2]) ? ' ' . $receiverAddressArr2[2] : '';
                                            $receiver_address_line_3 .= isset($receiverAddressArr2[3]) ? ' ' . $receiverAddressArr2[3] : '';
                                        }
                                        $receiver_address_line_3 .= $data['21'];
                                        $receiver_city = $data['22'];
                                        $receiver_state = $data['23'];
                                        $receiver_postcode = $data['24'];
                                        $reference = $data['25'];

                                        $value = trim($data['26']) == '' ? 0 : $goodsValue["26"];
                                        $currency = $data['27'];
                                        $itemyype = $data['28'];
                                        $notes = $data['29'];
                                        $description = $data['30'];
                                        $parcel_length = $data['33'];
                                        $parcel_width = $data['34'];
                                        $parcel_height = $data['35'];


                                        $bag_number = $data['36'];

                                        $tracking_number = $data['37'];
                                        $mawb = $data['38'];
                                        $eori_number = $data['40'];

                                        $parcel_tracking_number = $data['37'];
                                        $no_of_pieces = 1;
                                        $parcel_weight = 0;

                                        $item_description = "";
                                        $item_no_of_items = "1";
                                        $item_item_value = $value;
                                        $item_weight = $parcel_weight;
                                        $item_tariff_no = "";
                                        $item_hscode = "";
                                        $item_manufacture_country_iso = $sender_country_iso;
                                        $shipment_type = "D";
                                        $collection_end_time = "";
                                        $collection_start_time = "";

                                        $itemArr = [];
                                        /*
                                          DECLARED_CONTENT_AMOUNT_1	DETAILED_CONTENT_DESCRIPTIONS_1	DECLARED_NETWEIGHT_1	DECLARED_VALUE_1	DECLARED_HS_CODE_1	DECLARED_ORIGIN_COUNTRY_1

                                         *                                          */

//DECLARED_CONTENT_AMOUNT_1	DETAILED_CONTENT_DESCRIPTIONS_1	DECLARED_NETWEIGHT_1	DECLARED_VALUE_1	DECLARED_HS_CODE_1	DECLARED_ORIGIN_COUNTRY_1


                                        for ($itemArrayIndex = 0; $itemArrayIndex <= 4; $itemArrayIndex++) {
                                            if (trim($data[(42 + ($itemArrayIndex * 6) )]) == "")
                                                break;
                                            // echo $data[(43 +($itemArrayIndex * 6) )] ."<br>";
                                            //echo $data[(43 +($itemArrayIndex * 6) )] ."<br>";
                                            $parcel_weight += $data[(43 + ($itemArrayIndex * 6) )];

                                            $value += $data[(44 + ($itemArrayIndex * 6) )];
                                            $itemArr[] = [
                                                "item_description" => $data[(42 + ($itemArrayIndex * 6) )],
                                                "no_of_items" => $data[(41 + ($itemArrayIndex * 6) )],
                                                "item_value" => $data[(44 + ($itemArrayIndex * 6) )],
                                                "weight" => ($data[(43 + ($itemArrayIndex * 6))] / 1000),
                                                "tariff_no" => 0,
                                                "hscode" => $data[(45 + ($itemArrayIndex * 6) )],
                                                "manufacture_country_iso" => $data[(46 + ($itemArrayIndex * 6) )]
                                            ];
                                        }

                                        $parcelArr = [];
                                        for ($parcelArrya = 0; $parcelArrya < $no_of_pieces; $parcelArrya++) {
                                            $parcelArr[] = [
                                                "tracking_number" => $parcel_tracking_number,
                                                "weight" => $parcel_weight / 1000,
                                                "length" => $parcel_length,
                                                "width" => $parcel_width,
                                                "height" => $parcel_height,
                                                "items" => $itemArr,
                                            ];
                                        }
                                    }

                                    $finalArray[] = [
                                        "sender_country_iso" => $sender_country_iso,
                                        "service_code" => $service_code,
                                        "order_reference" => $order_reference,
                                        "tracking_number" => $tracking_number,
                                        "sender_contact" => $sender_contact,
                                        "sender_email" => $sender_email,
                                        "sender_company" => $sender_company,
                                        "sender_name" => $sender_name,
                                        "sender_address_line_1" => $sender_address_line_1,
                                        "sender_address_line_2" => $sender_address_line_2,
                                        "sender_address_line_3" => $sender_address_line_3,
                                        "sender_city" => $sender_city,
                                        "sender_state" => $sender_state,
                                        "sender_postcode" => $sender_postcode,
                                        "sender_telephone" => $sender_telephone,
                                        "receiver_country_iso" => $receiver_country_iso,
                                        "receiver_contact" => $receiver_contact,
                                        "receiver_email" => $receiver_email,
                                        "receiver_company" => $receiver_company,
                                        "receiver_telephone" => $receiver_telephone,
                                        "receiver_address_line_1" => $receiver_address_line_1,
                                        "receiver_address_line_2" => $receiver_address_line_2,
                                        "receiver_address_line_3" => $receiver_address_line_3,
                                        "receiver_city" => $receiver_city,
                                        "receiver_state" => $receiver_state,
                                        "receiver_postcode" => $receiver_postcode,
                                        "value" => $value,
                                        "currency" => $currency,
                                        "itemyype" => $itemyype,
                                        "notes" => $notes,
                                        "description" => $description,
                                        "bag_number" => $bag_number,
                                        "mawb" => $mawb,
                                        "parcel" => $parcelArr,
                                        "shipment_type" => $shipment_type,
                                        "collection_end_time" => $collection_end_time,
                                        "collection_start_time" => $collection_start_time,
                                        "collection_date" => $collection_date,
                                        "reference" => @$reference,
                                        "eori_number" => @$eori_number
                                    ];
                                }
                                $row++;
                            }
                            fclose($handle);
                        }

                        $jsonStr = json_encode($finalArray);
                        $jsonFileName = time();
                        $fileNameSplit = explode('.', $file);
                        $jsonFileName .= '_';
                        $jsonFileName .= isset($fileNameSplit[0]) ? $fileNameSplit[0] . '.json' : '.json';
                        $jsonFile = SETTING_DIR_ASSETS . "user_data/" . $entry . "/data_in/" . $jsonFileName;
                        file_put_contents($jsonFile, $jsonStr);
                        $moveFilePath = SETTING_DIR_ASSETS . "user_data_csv/" . $entry . "/data_processed/" . $file;
                        rename($filePath, $moveFilePath);
                    }
                }
            }
        }
    }
    closedir($handle);
}

function sendEmail($messageData, $stop = false, $file = "", $folder = "") {
    $to = "ITSupport@oneworldexpress.com";
//        $from = "ITSupport@oneworldexpress.com";
//        $to = "tahir@oneworldexpress.com";
    $from = "smart@smarttrack.co";
    $subject = "Cron JSON FILE IMPORTED VIA FTP";

    //begin of HTML message
    echo $message = '<html>
		  <body bgcolor="#DCEEFC">
			
				<b>' . $messageData . '</b><br>
			 </body>
		</html>';

    //end of message
    $headers = "From: $from\r\n";
    $headers .= "Content-type: text/html\r\n";
    // now lets send the email.
    mail($to, $subject, $message, $headers);
    if ($stop) {
        if (!empty($file)) {
            rename(SETTING_DIR_ASSETS . "user_data_csv/kangaroo/data_in/" . $file, SETTING_DIR_ASSETS . "user_data_csv/kangaroo/data_error/" . $file);
        }
    }
}


function convertUtf8($fdata){
    return iconv( mb_detect_encoding( $fdata ), 'utf-8', $fdata );
}

?>