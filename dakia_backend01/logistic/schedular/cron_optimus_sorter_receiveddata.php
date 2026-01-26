<?php

require_once(__DIR__ . "/../includes/settings/config.inc.php");

include_classes([
    'invoicetemplate.class'
        ], 'invoices');
include_classes([
    'tcpdf'
        ], '3rdparty/tcpdf');

include_classes([
    'iaddress.class',
    'consignment.class',
    'consignmentfilter.class',
    'parcel.class',
    'parcelfilter.class',
    'country.class',
    'countryfilter.class',
    'invoices.class',
    'invoicesfilter.class',
    'invoices.class',
    'invoicesfilter.class',
    'licenceplate.class',
    'licenceplatefilter.class',
    'notfoundrecord.class',
    'notfoundrecordfilter.class',
    'userservicesrouting.class',
    'userservicesroutingfilter.class',
    'trackingdata.class',
    'trackingdatafilter.class',
    'consignmentstatuslog.class',
    'consignmentstatuslogfilter.class',
    'consignmentcharges.class',
    'consignmentchargesfilter.class',
]);
ini_set('max_execution_time', '-1');
//require __DIR__ . "/../includes/settings/local_settings/" . $localSettingsFile . ".php";
//require __DIR__ . "/../includes/settings/common.inc.php";

$path = SETTING_DIR_ASSETS . "optimus_sorter/Outbox/" . date("Y_m_d") . "/";
$folder_path = SETTING_DIR_ASSETS . "optimus_sorter/Outbox/" . date("Y_m_d") . "/Processed/";


$ftp_url = "213.246.110.102";
$ftp_username = "optimus_s_mruga";
$ftp_password = "M]{45^J4@E7>3rm#";

$conn_id = ftp_connect($ftp_url);
if (!$conn_id) {
    echo "Old System FTP connection failed";
} else {
    ftp_login($conn_id, $ftp_username, $ftp_password);
    ftp_pasv($conn_id, true);
    $remoteFilePath = "/Outbox/Processed/";
    $arrfile = ftp_nlist($conn_id, $remoteFilePath);



    if (!file_exists($folder_path))
        @mkdir($folder_path, 0777, true);

    if (sizeof($arrfile) > 0) {
        $matchfile = array();
        foreach ($arrfile as $filename) {
            $filename = str_replace("/Outbox/Processed/", "", $filename);
            $file_start = "SEPI";
            if (strpos($filename, $file_start) !== false) {

                $localFilePath = $path . $filename; //$linkFile->getFileName();
                $renameFileName = $folder_path . $filename;
                chmod($path, 0777);
                $fp = fopen($localFilePath, 'w');
                if (ftp_fget($conn_id, $fp, $remoteFilePath . $filename, FTP_ASCII, FTP_AUTORESUME)) {
                    if (!ftp_put($conn_id, $remoteFilePath . $filename, "" . $localFilePath, FTP_ASCII)) {
                        echo "Old Server FTP PUT Fail";
                    } else {
                        ftp_delete($conn_id, $remoteFilePath . $filename);
                    }
                }
            }
        }
    }
}
$filesall = scandir($path);
if (sizeof($filesall) > 0) {

    foreach ($filesall as $fileIndex => $file) {
        if (in_array($file, array('.', '..', 'Processed')))
            continue;

        $localFilePath = $path . $file;
        $renameFileName = $folder_path . $file;
        $handle = fopen($localFilePath, "r");
        $notFoundArr = array();
        $trackDataHoldArr = array();
        $rejectedshipments = array();
        $holdCount = 0;

        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
            $num = count($data);
            @$row++;

            for ($c = 0; $c < $num; $c++) {
                $sorter_data = $data[$c];
                //print_r($sorter_data); die;
                if ($sorter_data != "") {
                    $s_data = explode(";", $sorter_data);

                    if ($s_data[0] == "H") {
                        $res = $s_data[1];
                        echo $res . "<br />";

                        $weight = number_format($s_data[2], 3);
                        $length = number_format($s_data[3], 3);
                        $width = number_format($s_data[4], 3);
                        $height = number_format($s_data[5], 3);
                        $volumn = number_format($s_data[6], 3);
                        $filename = strtoupper($s_data[7]);
                        $rejectedcode = $s_data[8];
                        $chute_number = $s_data[9];
                        $date_create = ($s_data[10] . " " . $s_data[11]);
                        $sortingType = $s_data[12];

                        $filename1 = explode(".", $filename);


                        if ($res != "") {
                            $cons_list = 0;
                            $res = ParseTrackingNumber::Parse($res);
                            $consign = new ConsignmentFilter();
                            $consign->addFilter("      (awb = '" . $res . "' or  hawb = '" . $res . "') and  c.shipment_status not in ('" . Consignment::STATUS_RECYCLED . "','" . Consignment::STATUS_INVALID . "') ", "consignmentfilter");
                            $cons_list = $consign->getColumnList('c.id, c.user_id, c.awb, c.hawb, c.weight, c.charge_weight, c.update_weight,c.date_scanned, ua.user_account as user_account, '
                                    . '                         c.vol_weight,c.vol_demonimator,c.warehouse_id, s.code as service_code, '
                                    . '                         s.validation_type as service_using, c.is_dead_weight_chargable, c.service_id, c.country_id,c.number_pieces, c.postcode, c.shipment_type');

                            if (count($cons_list) <= 0) {

                                $consign = new ConsignmentFilter();
                                $consign->addFilter("      c.shipment_status not in ('" . Consignment::STATUS_RECYCLED . "','" . Consignment::STATUS_INVALID . "') ", "consignmentfilter");
                                $consign->addFilter("      pc.tracking_number = '" . $res . "'  ", "parcelfilter");
                                $cons_list = $consign->getColumnList('c.id, c.user_id, pc.tracking_number as awb, c.hawb, c.weight,  c.charge_weight, c.update_weight,c.date_scanned, ua.user_account as user_account, '
                                        . '                         c.vol_weight,c.vol_demonimator,c.warehouse_id, s.code as service_code, '
                                        . '                         s.validation_type as service_using, c.is_dead_weight_chargable, c.service_id, c.country_id,c.number_pieces, c.postcode, c.shipment_type');
                            }


                            $trackingNumberArrayParcel = array();
                            if (count($cons_list) > 0) {
                                foreach ($cons_list as $clist) {

                                    /* $consignmentCharges = new ConsignmentChargesFilter();
                                      //consignment_id in (select id from consignment where awb = '".$clist->getAwb()."')
                                      $consignmentCharges->addFilter("
                                      consignment_id in ('".$clist->getId()."')
                                      and account_id in (2317, 2309,2310,4699,4700,4701,4702,4703,4704,4705,4706, 4778)
                                      and (invoice_id <= 0 or invoice_id is null) and cost_type = 'customer'
                                      ") ;
                                      $consignmentChargesList = $consignmentCharges->getList("id");
                                      echo "<br />invoice count - " . count($consignmentChargesList) . "<br />";
                                      if(count($consignmentChargesList) <= 0){
                                      continue;
                                      } */
                                    if (in_array($clist->getServiceCode(), array('STRYMRM1P', 'STRYMRM1L', 'STRYMRM2P', 'STRYMRM2L'))) {
                                        $res = $clist->getAwb();
                                    }
                                    $trackingNumber_array[] = $clist->getAwb();
                                    $denomitor = $clist->getVolDemonimator();
                                    if ($denomitor == "0" || $denomitor == "")
                                        $denomitor = 5000;
                                    $vol_weight = number_format(((($length * $width * $height ) / $denomitor)), 3);
                                    $account = $clist->getUserAccount();
                                    $original_weight = $clist->getWeight();
                                    $original_chargebleWeight = $clist->getChargeWeight();
                                    $oldConsignmentStatus = $clist->getConsignmentStatus();
                                    if ($clist->getIsDeadWeightChargable() == 1) {
                                        $chargableWeight = $weight;
                                    } else if ($clist->getServiceUsing() == "mail") {
                                        $chargableWeight = $weight;
                                    } else {
                                        if ($vol_weight > $weight)
                                            $chargableWeight = $vol_weight;
                                        else
                                            $chargableWeight = $weight;
                                    }
                                    if ($original_chargebleWeight < $chargableWeight) {
                                        $clist->setChargeWeight($chargableWeight);
                                    }
                                    $clist->setWeight($weight);
                                    $clist->setUpdateWeight($original_weight);
                                    $clist->setVolWeight($vol_weight);
                                    $clist->setSorterImage($filename);
                                    $clist->setDateScanned(strtotime($date_create));

                                    if (trim($rejectedcode) == "" || $rejectedcode == "ZERODIMS" || $rejectedcode == "CUM") {
                                        $holdstatusCode = false;
                                        if ($original_weight < $weight && $account == "SHIP2WORLD" && $clist->getShipmentType() != "DO") {
                                            $holdstatusCode = true;
                                            $clist->setConsignmentStatus(Consignment::$database_status_array[Consignment::STATUS_HOLD]);
                                            $clist->setShipmentStatus(Consignment::STATUS_HOLD);
                                        }
                                        else{
                                            $newConsignmentStatus = Consignment::$database_status_array[Consignment::STATUS_RECEIVED];
                                            $clist->setConsignmentStatus(Consignment::$database_status_array[Consignment::STATUS_RECEIVED]);
                                            $clist->setShipmentStatus(Consignment::STATUS_RECEIVED);
                                        }
                                    }
                                    
                                    

                                    $totalShipmentWeight = 0;

                                    $ParcelFilter = new ParcelFilter();
                                    $ParcelFilter->addConsignmentIdFilter($clist->getId());
                                    $ParcelFilter->addLicensePlateFilter(trim($clist->getAwb()));
                                    $parcellist = $ParcelFilter->getList();

                                    if (count($parcellist) > 0) {
                                        foreach ($parcellist as $p) {
                                            $oldwidth = $p->getWidth();
                                            $oldHeight = $p->getHeight();
                                            $oldLength = $p->getLength();
                                            $parcelId = $p->getId();
                                            $parcel_weight = $p->getWeight();
                                            $p->setWeight($weight);
                                            $p->setUpdateWeight($parcel_weight);
                                            $p->setWidth($width);
                                            $p->setHeight($height);
                                            $p->setLength($length);
                                            $p->setChuteSorted($chute_number);
                                            $p->setSortType($sortingType);
                                            
                                            if($holdstatusCode){
                                                $p->setParcelStatusCode(Consignment::STATUS_HOLD);
                                                $p->setOweStatusCode(Consignment::$database_status_array[Consignment::STATUS_HOLD]);
                                            }
                                            else{
                                                $p->setParcelStatusCode(Consignment::STATUS_RECEIVED);
                                                $p->setOweStatusCode(Consignment::$database_status_array[Consignment::STATUS_RECEIVED]);
                                            }
                                            
                                            $p->save();

                                            $totalShipmentWeight += $weight;

                                            $newdimesions = $length . "X" . $width . "X" . $height;
                                            $olddimesions = $oldLength . "X" . $oldwidth . "X" . $oldHeight;

                                            //$ConsignmentLog = new ConsignmentLog();
                                            // $ConsignmentLog->createlog("Sorter has updated dimesion ", $p->getConsignmentId(), 'A', '4282', $olddimesions, $newdimesions);
                                        }


                                        $clist->setWeight($totalShipmentWeight);
                                    }
                                    
                                    

                                   /* if ($original_weight < $weight && $account == "SHIP2WORLD") {
                                        

                                        $clist->setConsignmentStatus(Consignment::$database_status_array[Consignment::STATUS_HOLD]);
                                        $clist->setShipmentStatus(Consignment::STATUS_HOLD);
                                        //$ConsignmentLog = new ConsignmentLog();
                                        //  $ConsignmentLog->createlog("Sorter has updated Weight", $clist->getId(), "A",4282, $original_weight, $weight);
                                    }*/

                                    if ($rejectedcode != "") {

                                        if ($rejectedcode == "OVW") {
                                            $rejectmessage = "Over Weight";
                                            $trackingmessage = "Over Weight";
                                        } elseif ($rejectedcode == "OOG") {
                                            $rejectmessage = "Out Of Gauge";
                                            $trackingmessage = "Out Of Gauge";
                                        } elseif ($rejectedcode == "OGG") {
                                            $rejectmessage = "Out Of Girth";
                                            $trackingmessage = "Out Of Girth";
                                        } elseif ($rejectedcode == "CSU") {
                                            $rejectmessage = "Carrier Service Unknown";
                                            $trackingmessage = "Received";
                                        } elseif ($rejectedcode == "CSN") {
                                            $rejectmessage = "Carrier Service No tConfigured On Chute";
                                            $trackingmessage = "Received";
                                        } elseif ($rejectedcode == "OTH") {
                                            $rejectmessage = "Other Error";
                                            $trackingmessage = "Received";
                                        } elseif ($rejectedcode == "ZERODIMS" || $rejectedcode == "CUM") {
                                            $rejectmessage = "Zero Dimension from Sorter";
                                            $trackingmessage = "Received";
                                        } else {
                                            $rejectmessage = "Other Error";
                                            $trackingmessage = "Received";
                                        }

                                        $rejectedshipments[] = $res;

                                        echo $res . "-" . $weight . "-" . $width . "X" . $height . "X" . $length . "-" . $filename . "<br>";

                                        $trackDataHoldArr[$holdCount]['parcel_id'] = $parcelId;
                                        $trackDataHoldArr[$holdCount]['old_status'] = $oldConsignmentStatus;
                                        $trackDataHoldArr[$holdCount]['new_status'] = $newConsignmentStatus;
                                        $trackDataHoldArr[$holdCount]['message'] = $rejectmessage . "  " . $rejectedcode;
                                        $trackDataHoldArr[$holdCount]['added_by'] = "4296";
                                        $trackDataHoldArr[$holdCount]['date_added'] = date("Y-m-d H:i:s");


                                        $holdCount++;
                                    }
                                    $clist->save();

                                    $outTariff = Consignment::consignment_label_pricing($clist);

                                    $latestDate = date("Y-m-d G:i:s", strtotime($date_create));



                                    $trackingDataFilter = new TrackingDataFilter();
                                    if($holdstatusCode){
                                        $statusCode= 136;
                                        $trackPoint = "HOLD - Overweight or Out of Gauge";
                                    }
                                    else
                                    {
                                        $statusCode = 146;
                                        $trackPoint = "Birmingham Sorting Centre - GBR";
                                    }
                                    $trackingDataFilter->addTrackPointExistFilter(trim($clist->getAwb()), $statusCode, $trackPoint, '', "Birmingham Sorting Centre", $latestDate);
                                    $trackingDataExistsObj = $trackingDataFilter->getColumnList("t.entity_id, t.entity_type", "");
                                    if (count($trackingDataExistsObj) == 0 && $parcelId > 0) {

                                        $trackingData = [
                                            'user_id' => 4282,
                                            'entity_id' => $parcelId,
                                            'entity_type' => "parcel",
                                            'tracking_number' => $clist->getAwb(),
                                            'track_point' => $trackPoint,
                                            'date_created' => $latestDate,
                                            'ip_address' => getClientIp(),
                                            'status_code_id' => $statusCode,
                                            'carrier_code' => '',
                                            'carrier_desc' => "Birmingham Sorting Centre",
                                            'signatory' => "",
                                            'warehouse_id' => '9'
                                        ];
                                        $trackingDataObj = new TrackingData($trackingData);
                                        $trackingDataObj->save();
                                    }
                                }
                            } else {

                                $notfound[] = $res;
                                $notFoundArr[$holdCount]['scanned_by'] = 4282;
                                $notFoundArr[$holdCount]['tracking_number'] = $res;
                                $notFoundArr[$holdCount]['reason'] = "NOTFOUND";
                                $notFoundArr[$holdCount]['date_created'] = date("Y-m-d H:i:s", $date_create);
                                $notFoundArr[$holdCount]['weight'] = $weight;
                                $notFoundArr[$holdCount]['width'] = $width;
                                $notFoundArr[$holdCount]['height'] = $height;
                                $notFoundArr[$holdCount]['length'] = $length;
                                $notFoundArr[$holdCount]['image'] = $filename;
                                $holdCount++;
                                //  continue;
                            }
                        }
                    } else {

                        continue;
                    }
                }
            }
        }

        if (sizeof($notFoundArr) > 0) {
            NotFoundRecord::bulkDataInsert($notFoundArr);
            $message = "Shipment Not Found in Smart System. <br>";
            foreach ($notfound as $nfound) {
                $message .= $nfound . "<br>";
            }
            $email = "itsupport@oneworldexpress.com";
            $subject = " NOT FOUND IN SMART SYSTEM" . $matchfile[0];
            $headers = "From: itsupport@oneworldexpress.com \r\n";
            $headers .= "Reply-To: itsupport@oneworldexpress.com \r\n";
            $headers .= "MIME-Version: 1.0\r\n";
            $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
            mail($email, $subject, $message, $headers);
        }
        if (sizeof($rejectedshipments) > 0) {
            $message = "Below Shipments get Rejected by Sorter <br>";
            foreach ($rejectedshipments as $reject) {
                $message .= $reject . "<br>";
            }
            $email = "mruga@oneworldexpress.com; kiran.iftikhar@oneworldexpress.com; kazim@oneworldexpress.com";
            $subject = " OPTIMUS SORTER NOT FOUND";
            $headers = "From: itsupport@oneworldexpress.com \r\n";
            $headers .= "Reply-To: itsupport@oneworldexpress.com \r\n";
            $headers .= "MIME-Version: 1.0\r\n";
            $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
            mail($email, $subject, $message, $headers);
        }
        //print_r($trackDataHoldArr);

        if (count($trackDataHoldArr) > 0) {
            ConsignmentStatusLog::bulkDataInsert($trackDataHoldArr);
        }
        fclose($handle);

        rename($localFilePath, $renameFileName);
    }
}
?>