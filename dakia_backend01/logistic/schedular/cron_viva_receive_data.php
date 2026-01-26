<?php

require_once(__DIR__ . "/../includes/settings/config.inc.php");
ini_set('max_execution_time', '-1');
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
]);
include_classes([
    'SFTP'
        ], '3rdparty/Net');
error_reporting(1);
ini_set('display_errors', '1');

$url = "orbitran.wqxs.com";
$username = "oneworld";
$password = "0ne29493*";

$ftpconnection = new Net_SFTP($url);
$ftpconnection->login($username, $password);

 $list= $ftpconnection->nlist();
if (count($list) > 0) {
   $date = date("Ymd");
   $prefix = "DHL_Manifest_".$date;
    $matches = preg_grep("/^$prefix.*/i", $list);
//    if (count($matches) != 1) {
//        die("No file or more than one file matches the pattern: " . implode(",", $matches));
//    }
    if(count($matches) > 0){
    $matches = array_values($matches);
    $folder_path = SETTING_DIR_ASSETS . "viva_data_received/" . date("Y_m_d") . "/Processed/";
    if (!file_exists($folder_path))
        @mkdir($folder_path, 0777, true);
    $localfilepath = SETTING_DIR_ASSETS . "viva_data_received/" . date("Y_m_d") . "/";
        foreach($matches as $filename){
            $localFileName = $localfilepath . $filename;
            $ftpconnection->get($filename, $localFileName);
        }
    }
    else
    {
        die("No Matching files found.");
    }
    $notAddedShipments = array();
    $filesall = scandir($localfilepath);
    
    if (count($filesall) > 0) {
        foreach ($filesall as $keyFileIndex => $valueFileIndex) {
            
            if (in_array($valueFileIndex, array('.', '..', 'Processed')))
                continue;

            $fullFileName = $localfilepath . $valueFileIndex;
            $renameFilename = $folder_path . $valueFileIndex;
            echo $fullFileName . "<br />";
            $xmlData = simplexml_load_file($fullFileName);
            echo "<pre>";
            //print_r($xmlData);
            $shipmentData = $xmlData->Shipment;
            if (count($shipmentData) > 0) {
                foreach ($shipmentData as $sData) {

                    $consignmentArray = array();
                    $vivaHawb = $sData->HAWB;
                    $hawb = $sData->HawbId;
                    $hawbId = Consignment::checkDuplicateHawb($hawb);
                    echo $hawbId . "<br />";

                    $awb = $sData->AgentHawb;
                    $customer = $sData->Customer;

                    if ($customer != '') {
                        $userFilter = new UserFilter();
                        $userFilter->addFieldFilter("user_name", $customer);
                        $userList = $userFilter->getList();
                        if (count($userList) > 0) {
                            $userid = $userList[0]->getId();
                        } else {
                            $notAddedShipments[] = $hawb;
                            continue;
                        }
                    } else {
                        $userid = "2227";
                        //  $notAddedShipments[] = $hawb;
                        //  continue;
                    }



                    $reference = $sData->CustomerReference;
                    $contact = $sData->ConsigneeName;
                    $addressLine1 = $sData->ConsigneeAddress1;
                    $addressLine2 = $sData->ConsigneeAddress2;
                    $addressLine3 = $sData->ConsigneeAddress3;
                    $city = $sData->ConsigneeTown;
                    $postcode = $sData->ConsigneeZipCode;
                    $receivercountry = $sData->ConsigneeCountry;

                    $telephone = $sData->ConsigneePhone;
                    $senderContact = $sData->ShipperName;
                    $senderAddressLine1 = $sData->ShipperAddress1;
                    $senderAddressLine2 = $sData->ShipperAddress2;
                    $senderAddressLine3 = $sData->ShipperAddress3;
                    $senderCity = $sData->ShipperTown;
                    $senderPostcode = $sData->ShipperZipCode;
                    $senderCountry = $sData->ShipperCountry;

                    $pieces = $sData->Pieces;
                    $weight = $sData->Weight;
                    $length = $sData->CollOrder->CollItem->X;
                    $width = $sData->CollOrder->CollItem->Y;
                    $height = $sData->CollOrder->CollItem->Z;
                    $vivaService = $sData->Service;

                    $description = $sData->Description;
                    $value = $sData->Value;
                    $currency = $sData->ValueCurrency;
                    $mawb = $sData->MAWB;



                    $consignment = new Consignment();
                    $consignment->setUserId($userid);
                    $consignment->setAgentId(1);
                    $consignment->setServiceId(23);
                    $consignment->setWarehouseId(10);
                    $consignment->setShipmentStatus(Consignment::STATUS_LABEL_CREATED);
                    $consignment->setShipmentType('D');
                    $consignment->setAwb($awb);
                    $consignment->setConsignmentStatus(Consignment::$database_status_array[Consignment::STATUS_LABEL_CREATED]);
                    $consignment->setHawb($hawbId);
                    $consignment->setReference($reference);
                    $consignment->setDateCreated(date("Y-m-d H:i:s"));
                    $consignment->setDateLabelCreated(time());
                    $consignment->setContact($contact);
                    $consignment->setAddressLine1($addressLine1);
                    $consignment->setAddressLine2($addressLine2);
                    $consignment->setAddressLine3($addressLine3);
                    $consignment->setCity($city);
                    $consignment->setPostcode($postcode);
                    $consignment->setNumberPieces(1);
                    $consignment->setIsCustomerBillable(1);

                    $countryFilter = new CountryFilter();
                    $countryFilter->addFieldFilter("iso", $receivercountry);
                    $countryList = $countryFilter->getColumnList("id");
                    if (count($countryList) > 0)
                        $consignment->setCountryId($countryList[0]->getId());

                    $consignment->setTelephone($telephone);
                   // $consignment->setNumberPieces($pieces);
                    $consignment->setWeightType("PP");
                    $consignment->setWeight(floatval($weight));
                    $consignment->setChargeWeight(floatval($weight));
                    $consignment->setDescription($description);
                    $consignment->setValue(floatval($value));
                    $consignment->setCurrency($currency);
                    $consignment->setNotes($vivaHawb);

                    $consignment->setOtherRoutingCode($vivaService);
                    $consignment->setConsignmentType('outbound');

                    $consignment->setSenderName($senderContact);
                    $consignment->setSenderAddressLine1($senderAddressLine1);
                    $consignment->setSenderAddressLine2($senderAddressLine2);
                    $consignment->setSenderAddressLine3($senderAddressLine3);
                    $consignment->setSenderCity($senderCity);
                    $consignment->setSenderPostcode($senderPostcode);

                    $sendercountryFilter = new CountryFilter();
                    $sendercountryFilter->addFieldFilter("iso", $senderCountry);
                    $sendercountryList = $sendercountryFilter->getColumnList("id");
                    if (count($sendercountryList) > 0)
                        $consignment->setSenderCountryId($sendercountryList[0]->getId());
                    
                    $consignment->save();
                    $consignmentId = $consignment->getId();

                    $parcel = new Parcel();
                    $parcel->setConsignmentId($consignmentId);
                    $parcel->setTrackingNumber($awb);
                    $parcel->setLength(floatval($length));
                    $parcel->setWidth(floatval($width));
                    $parcel->setHeight(floatval($height));
                    $parcel->setOweStatusCode(Consignment::$database_status_array[Consignment::STATUS_LABEL_CREATED]);
                    $parcel->setParcelStatusCode(Consignment::STATUS_LABEL_CREATED);
                    $parcel->save();
                    
                    $outTariff = Consignment::consignment_label_pricing($consignment);
                }

                print_r($notAddedShipments);
                if (sizeof($notAddedShipments) > 0) {
                    $message = "Below shipments are rejected. Please open there account into smart track and reupload shipments.";
                    foreach ($notAddedShipments as $notadd) {
                        $message .= $notadd . "<br>";
                    }

                    $email = "mruga@oneworldexpress.com;";
                    $subject = " UNABLE TO IMPORT DATA TO SMART TRACK";
                    $headers = "From: itsupport@oneworldexpress.com \r\n";
                    $headers .= "Reply-To: itsupport@oneworldexpress.com \r\n";
                    $headers .= "MIME-Version: 1.0\r\n";
                    $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
                    mail($email, $subject, $message, $headers);
                }
            }

            rename($fullFileName, $renameFilename);
        }
    }
}
?>