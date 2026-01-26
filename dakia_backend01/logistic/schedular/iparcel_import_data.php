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
    'agentdata.class',
    'agentdatafilter.class',
    'serviceagentmappingfilter.class',
    'serviceagentmapping.class',
    'carrierservicecustomizerules.class',
    'carrierservicecustomizerulesfilter.class',
    'carrierservicedefaultrules.class',
    'carrierservicedefaultrulesfilter.class',
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
function convert_from_latin1_to_utf8_recursively($dat) {
    if (is_string($dat)) {
        return utf8_encode($dat);
    } elseif (is_array($dat)) {
        $ret = [];
        foreach ($dat as $i => $d)
            $ret[$i] = convert_from_latin1_to_utf8_recursively($d);

        return $ret;
    } elseif (is_object($dat)) {
        foreach ($dat as $i => $d)
            $dat->$i = convert_from_latin1_to_utf8_recursively($d);

        return $dat;
    } else {
        return $dat;
    }
}

if ($handle = opendir(SETTING_DIR_ASSETS . "user_data/")) {
    while (false !== ($entry = readdir($handle))) {

        if ($entry == '.' || $entry == '..') {
            
        } else {
            $filesData = scandir(SETTING_DIR_ASSETS . "user_data/" . $entry . "/data_in");
            $files = array_diff($filesData, array('..', '.'));
            if (count($files) > 0) {
                if (!mysqli_ping($dbConnection)) {
                    $dbConnection = DbAccess3::connect(SETTING_DB_SERVER, SETTING_DB_USER, SETTING_DB_PASSWORD, SETTING_DB_DATABASE);
                }
                $accountdirect = '';
                //if($entry =='iparcel')
                //	$accountdirect	=	'iparcel_usd';	
                //else
                $accountdirect = $entry;

                $query = "SELECT
                                user.id,user.user_pass,user.warehouse_id,user.user_account_id
                                FROM
                                `user` INNER JOIN user_account ON user.user_account_id = user_account.id
                                WHERE
                                    LOWER(user_account.user_account) = LOWER('" . $accountdirect . "')
                                    AND user.is_deleted != 1 
                                    AND user_account.active_flag = 1 
                                    AND user.is_tc_agreed = 'y'  
                                    ";
                $queryData = $dbConnection->query($query);
                if (mysqli_num_rows($queryData) > 0) {
                    while ($record = mysqli_fetch_assoc($queryData)) {
                        $userPass = $record['user_pass'];
                        $userId = $record['id'];
                        $warehouseId = $record['warehouse_id'];
                        $userAccountId = $record['user_account_id'];
                    }
                } else {
                    sendEmail("No user found against username $entry", true, '', $entry);
                }


                foreach ($files as $file) {
                    $errorFound = false;
                    // load json file
                    echo SETTING_DIR_ASSETS . "user_data/" . $entry . "/data_in/" . $file . "<br>";
                    $jsonString = file_get_contents(SETTING_DIR_ASSETS . "user_data/" . $entry . "/data_in/" . $file);
                    // load file and covert in to 3 csv files
                    $pureJsonStr = "";
                    $pureJsonStr = convert_from_latin1_to_utf8_recursively($jsonString);
                    $jsonData = json_decode($pureJsonStr, true);

                    if (!empty($jsonData)) {
                        $count = 1;
                        $totalRecords = count($jsonData);
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
                                                        VALUES ";
                        $sqlParcelStr = 'INSERT INTO    `parcel` (
                                                                consignment_id,
                                                                tracking_number,
                                                               `length`,
                                                                width,
                                                                height,
                                                                weight,
                                                                description,
                                                                qty,
                                                                itemvalue,
                                                                pweight,
                                                                tarrif_no,
                                                                hscode,
                                                                commoditycode,
                                                                owe_status_code,
                                                                parcel_status_code,number_item
                                                            )
                                                            VALUES ';

                        // Empty Parcel
                        $sqlItemDetailsStr = 'INSERT INTO    `item_details` (
                                                                consignment_id,
                                                                item_detail,
                                                                user_id,
                                                                parcel_count
                                                            )
                                                            VALUES';

                        $mawbBaggingArr = [];
                        $mawbIdArr = [];
                        $bagIdArr = [];
                        foreach ($jsonData as $conData) {
                            $senderCountryIso = removeChar($conData['sender_country_iso']);
                            $serviceCode = removeChar($conData['service_code']);
                            $orderReference = removeChar($conData['order_reference']);
                            $trackingNumber = removeChar($conData['tracking_number']);
                            $senderContact = removeChar($conData['sender_contact']);
                            $senderEmail = removeChar($conData['sender_email']);
                            $senderCompany = removeChar($conData['sender_company']);
                            $senderName = removeChar($conData['sender_name']);
                            $senderAddressLine1 = removeChar($conData['sender_address_line_1']);
                            $senderAddressLine2 = removeChar($conData['sender_address_line_2']);
                            $senderAddressLine3 = removeChar($conData['sender_address_line_3']);
                            $senderCity = removeChar($conData['sender_city']);
                            $senderState = removeChar($conData['sender_state']);
                            $senderPostcode = removeChar($conData['sender_postcode']);
                            $senderTelephone = removeChar($conData['sender_telephone']);
                            $receiverCountryIso = removeChar($conData['receiver_country_iso']);
                            $receiverContact = removeChar($conData['receiver_contact']);
                            $receiverEmail = removeChar($conData['receiver_email']);
                            $receiverCompany = removeChar($conData['receiver_company']);
                            $receiverTelephone = removeChar($conData['receiver_telephone']);
                            $receiverAddressLine1 = removeChar($conData['receiver_address_line_1']);
                            $receiverAddressLine2 = removeChar($conData['receiver_address_line_2']);
                            $receiverAddressLine3 = removeChar($conData['receiver_address_line_3']);
                            $receiverCity = removeChar($conData['receiver_city']);
                            $receiverState = removeChar($conData['receiver_state']);
                            $receiverPostcode = removeChar($conData['receiver_postcode']);
                            $value = removeChar($conData['value']);
                            $currency = removeChar($conData['currency']);
                            $itemyype = removeChar($conData['itemyype']);
                            $notes = removeChar($conData['notes']);
                            $description = removeChar($conData['description']);
                            $bagNumber = removeChar($conData['bag_number']);
                            $mawb = removeChar($conData['mawb']);

                            $dateParts = explode("-", removeChar($conData['collection_date']));
                            if (count($dateParts) > 0) {
                                if (strlen($dateParts[2]) == 4) {
                                    $LabelCreatedDate = $dateParts[2] . "-" . $dateParts[0] . "-" . $dateParts[1];
                                } else {
                                    $LabelCreatedDate = removeChar($conData['collection_date']);
                                }
                            } else {
                                if (trim($conData['collection_date']) != '')
                                    $LabelCreatedDate = removeChar($conData['collection_date']);
                                else
                                    $LabelCreatedDate = '';
                            }
                            $mawb = substr_replace(str_replace(' ', '', $mawb), "-", 3, 0);
                            // Get Parcel Data
                            $parcelTotalWeight = 0;
                            $maxParcelWeight = 0;
                            foreach ($conData['parcel'] as $parelData) {
                                $parcelTrackingNumber = removeChar($parelData['tracking_number']);
                                if (!empty($mawb)) {
                                    $mawbBaggingArr[$mawb][$bagNumber][] = $parcelTrackingNumber;
                                }
                                $parcelWeight = removeChar(($parelData['weight'] < 0.100 ? 0.100 : $parelData['weight']));
                                $parcelLength = removeChar($parelData['length']);
                                $parcelWidth = removeChar($parelData['width']);
                                $parcelHeight = removeChar($parelData['height']);
                                $itemDescription = $parelData['items'];
                                // Calcualte max weight of parcel
                                if ($maxParcelWeight < $parelData['weight']) {
                                    $maxParcelWeight = removeChar($parelData['weight']);
                                }
                                $parcelDetailsData = [];
                                foreach ($itemDescription as $key => $item) {


                                    $parcelDetailsData['item_description'][$key] = removeChar($item['item_description']);
                                    $parcelDetailsData['no_of_items'][$key] = removeChar($item['no_of_items']);
                                    $parcelDetailsData['item_value'][$key] = removeChar($item['item_value']);
                                    $parcelDetailsData['weight'][$key] = removeChar($item['weight']);
                                    $parcelDetailsData['tariff_no'][$key] = removeChar($item['tariff_no']);
                                    $parcelDetailsData['hscode'][$key] = removeChar($item['hscode']);
                                    $parcelDetailsData['manufacture_country_iso'][$key] = removeChar($item['manufacture_country_iso']);
                                    $itemDescription[$key] = array_map('removeChar', $item);
                                }
                                //$parcelDetailsData = "'" . json_encode($parcelDetailsData) . "'";
                                $parcelTotalWeight += $parcelWeight;
                                //                    // add parcel
                                $sqlParcelStr .= ' ((SELECT max(id) FROM consignment WHERE awb ="' . DbAccess3::escape($trackingNumber) . '" and hawb = "' . DbAccess3::escape($orderReference) . '" and shipment_status <> "22") ,"' .
                                        $parcelTrackingNumber . '",' .
                                        $parcelLength . ',' .
                                        $parcelWidth . ',' .
                                        $parcelHeight . ',' .
                                        $parcelWeight . ',' .
                                        "'" . json_encode($parcelDetailsData['item_description']) . "', " .
                                        "'" . json_encode($parcelDetailsData['no_of_items']) . "', " .
                                        "'" . json_encode($parcelDetailsData['item_value']) . "', " .
                                        "'" . json_encode($parcelDetailsData['weight']) . "', " .
                                        "'" . json_encode($parcelDetailsData['tariff_no']) . "', " .
                                        "'" . json_encode($parcelDetailsData['hscode']) . "', " .
                                        "'" . json_encode($parcelDetailsData['manufacture_country_iso']) . "', " .
                                        (trim($trackingNumber) == '' ? "'ready to print'," : "'label created',") .
                                        (trim($trackingNumber) == '' ? 12 : 13 ) . ",1),";



                                $sqlItemDetailsStr .= ' ((SELECT max(id) FROM consignment WHERE awb ="' . DbAccess3::escape($trackingNumber) . '" and hawb = "' . DbAccess3::escape($orderReference) . '" and shipment_status <> "22") ,' .
                                        "'" . json_encode($itemDescription) . "', " .
                                        "'" . $userId . "', " .
                                        "'0' " .
                                        "),";
                            }

                            // Check destination country id
                            $queryReceiverCoun = "SELECT
                                id
                                FROM
                                `country`
                                WHERE
                                    iso = '" . DbAccess3::escape($receiverCountryIso) . "'  
                                    ";
                            $counReceiverData = $dbConnection->query($queryReceiverCoun);
                            if (mysqli_num_rows($counReceiverData) > 0) {
                                while ($recordReceiverCoun = mysqli_fetch_assoc($counReceiverData)) {
                                    $receiverCountryId = $recordReceiverCoun['id'];
                                }
                            } else {
                                sendEmail("Receiver Country id not found " . $receiverCountryIso . " tracking number " . $trackingNumber . " and order reference number " . $orderReference, true, $file, $entry);
                            }
                            // Check source country id
                            $querySenderCoun = "SELECT
                                id
                                FROM
                                `country`
                                WHERE
                                    iso = '" . DbAccess3::escape((trim($senderCountryIso) == '') ? 'GB' : $senderCountryIso) . "'  
                                    ";
                            $counSenderData = $dbConnection->query($querySenderCoun);
                            if (mysqli_num_rows($counSenderData) > 0) {
                                while ($recordSenderCoun = mysqli_fetch_assoc($counSenderData)) {
                                    $senderCountryId = $recordSenderCoun['id'];
                                }
                            } else {
                                sendEmail("Sender Country id not found " . $senderCountryIso . " tracking number " . $trackingNumber . " and order reference number " . $orderReference, true, $file, $entry);
                            }
                            if (!empty($bagNumber)) {
                                // Check if bag is already there and opened
                                $sqlCheckBag = "SELECT * FROM `bagging` b WHERE b.`bagnumber` = '" . DbAccess3::escape($bagNumber) . "' ";
                                $bagData = $dbConnection->query($sqlCheckBag);
                                if (mysqli_num_rows($bagData) > 0) {
                                    //    $bagData
                                    while ($record = mysqli_fetch_assoc($bagData)) {
                                        $isClosed = $record['is_closed'];
                                        $closedDate = $record['closed_date'];
                                        $closedBy = $record['closed_by'];
                                        $bagId = $record['id'];
                                    }
                                    if ($isClosed == "1" && $closedBy > 0) {
                                        sendEmail("Bag is closed " . $bagNumber);
                                    }
                                } else {
                                    // Create Bag from given data
                                    $bagging = new Bagging();
                                    $bagging->setBagnumber($bagNumber);
                                    $bagging->setDateCreated(time());
                                    $bagging->setUserId($userId);
                                    $bagging->setBagSourceCountryId($senderCountryId);
                                    $bagging->setBagDestinationCountryId($receiverCountryId);
                                    $bagging->save();
                                    $bagId = $bagging->getId();
                                }
                                $bagIdArr[$bagNumber] = $bagId;
                            }
                            if (!empty($mawb)) {
                                // check if mawb is already exist
                                $mawbFilter = new MawbFilter();
                                $mawbFilter->addFilter("    mawb_number='" . DbAccess3::escape($mawb) . "'");
                                $mawbFilterObj = $mawbFilter->getList("id,mawb_status");
                                if (count($mawbFilterObj) > 0) {
                                    $mawbStatus = $mawbFilterObj[0]->getMawbStatus(); // if o then its open
                                    $mawbId = $mawbFilterObj[0]->getId();
                                    if ($mawbStatus == 'd') {
                                        sendEmail("MAWB is dispatched " . $mawb);
                                    }
                                } else {
                                    $mawbObj = new Mawb();
                                    $mawbObj->setMawbSourceCountryId($senderCountryId);
                                    $mawbObj->setMawbDestinationCountryId($receiverCountryId);
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
                            $serviceFilter->addFilter("    code='" . DbAccess3::escape($serviceCode) . "'");
                            $serviceFilterObj = $serviceFilter->getList();
                            if (count($serviceFilterObj) > 0) {
                                $serviceName = $serviceFilterObj[0]->getName(); // if o then its open
                                $serviceId = $serviceFilterObj[0]->getId();
                                $volumetricDenominatorService = $serviceFilterObj[0]->getVolumetricDenominator();
                                $girth = $serviceFilterObj[0]->getGirth();
                                $serviceGrithFormula = $serviceFilterObj[0]->getGirthFormula();
                            } else {
                                sendEmail("service not found in system " . $serviceCode, true, $file, $entry);
                            }

                            $customizedServicesRouting = NULL;
                            if ($serviceFilterObj[0]->getIsCustomized() == "1") {
                                $customizedServicesRoutingFilter = new CustomizedServicesRoutingFilter();
                                $customizedServicesRoutingFilter->addJoin('services s', 's.id=csr.service_id');
                                $customizedServicesRoutingFilter->addFieldFilter('country_id', $receiverCountryId);
                                $customizedServicesRoutingFilter->addFieldFilter('customize_service_id', $serviceId);
                                $customizedServicesRoutingFilter->addFieldFilter('status', '1');
                                $customizedServicesRouting = $customizedServicesRoutingFilter->getColumnList("csr.service_id,csr.from_weight,csr.to_weight,s.volumetric_denominator");
                                if (count($customizedServicesRouting) > 0) {
                                    $weightNotFound = true;
                                    $newServiceId = "";
                                    $customizedServiceId = "";
                                    $volDemonimator = "";
                                    $routingCode = "S";
                                    foreach ($customizedServicesRouting as $customizedServicesRoutingArr) {
                                        if ($customizedServicesRoutingArr->getFromWeight() < $parcelTotalWeight && $customizedServicesRoutingArr->getToWeight() >= $parcelTotalWeight) {
                                            $customizedServiceId = $serviceId;
                                            $newServiceId = $customizedServicesRoutingArr->getServiceId();
                                            $volDemonimator = $customizedServicesRoutingArr->getVolumetricDenominator();
                                            $routingCode = 'PR';
                                            $weightNotFound = false;
                                            break;
                                        }
                                    }
                                    if ($weightNotFound) {
                                        
                                    }
                                } else {
                                    $volDemonimator = 5000;
                                }
                            } else {
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
                            if (trim($brandNewServiceWeightType) == 1)
                                $weightType = 'PP';
                            foreach ($conData['parcel'] as $parelData) {
                                $parcelWeight = ($parelData['weight'] < 0.100 ? 0.100 : $parelData['weight']);
                                $parcelLength = $parelData['length'];
                                $parcelWidth = $parelData['width'];
                                $parcelHeight = $parelData['height'];
                                $itemDescription = $parelData['items'];
                                if (!empty($volDemonimator) && $volDemonimator > 0) {
                                    $volweight = (($parcelWidth * $parcelHeight * $parcelLength) / $volDemonimator);
                                    if ($maxvolweight < $volweight)
                                        $maxvolweight = $volweight;
                                    $totalvolweight = $totalvolweight + $volweight;
                                }
                            }
                            if ($weightType == 'PP') {
                                $weightToSend = $maxParcelWeight;
                                $vol_weight = $maxvolweight;
                            } else {
                                $weightToSend = $parcelTotalWeight;
                                $vol_weight = $totalvolweight;
                            }
                            if (!empty($volDemonimator) && $volDemonimator > 0) {
                                if ($vol_weight > $weightToSend) {
                                    $weightToSend = $vol_weight;
                                }
                            }


                            // Check if service is agreed
                            $userServicesRoutingFilter = new UserServicesRoutingFilter();
                            $userServicesRoutingFilter->addFieldFilter("user_account_id", $userAccountId);
                            if ($brandNewServiceObj->getIsCustomized() == "0") {
                                $userServicesRoutingFilter->addFieldFilter("country_id", $receiverCountryId);
                            }
                            //if ($customizedServiceId == 0 || $customizedServiceId == '')
                            $userServicesRoutingFilter->addFieldFilter("service_id", $newServiceId);
                            //else
                            //  $userServicesRoutingFilter->addFieldFilter("service_id", $customizedServiceId);

                            $userServicesRouting = $userServicesRoutingFilter->getColumnList("is_agreed,from_weight,to_weight,is_over_label");


                            if (count($userServicesRouting) > 0) {
                                // get all parent
                                $parentAccount = CustomerAccount::accountParentAccount($userAccountId);
                                foreach ($parentAccount as $parentAccountArr) {
                                    $isAgentSet = false;
                                    $isOwnContract = checkOwnContract($parentAccountArr->getId(), $newServiceId);
                                    if ($isOwnContract) {
                                        // Own contract set it's own agent
                                        $agentDataFilter = new AgentDataFilter();
                                        $agentDataFilter->addFieldFilter("user_id", $userAccountId);
                                        //                $agentDataFilter->
                                        $serviceAgentMappingDataFilter = new ServiceAgentMappingDataFilter();
                                        $serviceAgentMappingDataFilter->addAgentJoin(" AND ad.`user_id` = '" . $parentAccountArr->getId() . "' and ad.active = 1 ");
                                        $serviceAgentMappingDataFilter->addServiceIDFilter($newServiceId);
                                        $serviceAgentMappingDataFilter->addFilter("m.from_weight < '" . DbAccess3::escape($weightToSend) . "'");
                                        $serviceAgentMappingDataFilter->addFilter("m.to_weight >= '" . DbAccess3::escape($weightToSend) . "'");
                                        $serviceAgentMappingDataObj = $serviceAgentMappingDataFilter->getColumnList("m.agentid");
                                        if (count($serviceAgentMappingDataObj) > 0) {
                                            $ownAgentId = $serviceAgentMappingDataObj[0]->getAgentid();
                                            $agentId = $ownAgentId;
                                            $isAgentSet = true;
                                            break;
                                        }
                                    }
                                }

                                if (($customizedServiceId == 0 || $customizedServiceId == '') && !$isAgentSet) {
                                    if ($userServicesRouting[0]->getFromWeight() < $weightToSend && $userServicesRouting[0]->getToWeight() >= $weightToSend) {
                                        // Get service agent
                                        $carrierServiceCustomizeRulesFilter = new carrierServiceCustomizeRulesFilter();
                                        $carrierServiceCustomizeRulesFilter->addFilter("serviceid = " . $newServiceId);
                                        $carrierServiceCustomizeRulesFilter->addFilter("user_account_id = " . $userAccountId);
                                        /* $carrierServiceCustomizeRulesFilter->addFilter("from_weight <= ".$weightToSend);
                                          $carrierServiceCustomizeRulesFilter->addFilter("to_weight >= ".$weightToSend); */
                                        $carrierServiceCustomizeRulesFilter->addFilter("status = 1");
                                        $carrierServiceCustomizeRules = $carrierServiceCustomizeRulesFilter->getColumnList("agentid,from_weight,to_weight");
                                        if (count($carrierServiceCustomizeRules) > 0) {
                                            $weightFound = false;
                                            foreach ($carrierServiceCustomizeRules as $carrierServiceCustomizeRule) {
                                                if ($carrierServiceCustomizeRule->getFromWeight() < $weightToSend && $carrierServiceCustomizeRule->getToWeight() >= $weightToSend) {
                                                    $agentId = $carrierServiceCustomizeRule->getAgentid();
                                                    $weightFound = true;
                                                    break;
                                                }
                                            }
                                            if ($weightFound === false) {
                                                
                                            }
                                        } else {
                                            $carrierServiceDefaultRulesFilter = new carrierServiceDefaultRulesFilter();
                                            $carrierServiceDefaultRulesFilter->addFilter("serviceid = " . $newServiceId);
                                            $carrierServiceDefaultRulesFilter->addFilter("agent_type = 'outbound'");
                                            $carrierServiceDefaultRules = $carrierServiceDefaultRulesFilter->getColumnList("agentid,from_weight,to_weight");
                                            if (count($carrierServiceDefaultRules) > 0) {
                                                $weightFound = false;
                                                foreach ($carrierServiceDefaultRules as $carrierServiceDefaultRule) {
                                                    if ($carrierServiceDefaultRule->getFromWeight() < $weightToSend && $carrierServiceDefaultRule->getToWeight() >= $weightToSend) {
                                                        $agentId = $carrierServiceDefaultRule->getAgentid();
                                                        $weightFound = true;
                                                        break;
                                                    }
                                                }
                                                if ($weightFound === false) {
                                                    
                                                }
                                            } else {
                                                
                                            }
                                        }
                                    } else {
                                        
                                    }
                                } else if (!$isAgentSet) {
                                    // If service is customized
                                    if (count($customizedServicesRouting) > 0) {
                                        $weightFoundCustomized = false;
                                        foreach ($customizedServicesRouting as $customizedServicesRoutingArr) {
                                            if ($customizedServicesRoutingArr->getFromWeight() < $weightToSend && $customizedServicesRoutingArr->getToWeight() >= $weightToSend) {
                                                // Doublicate this code because we need customized service agent
                                                $carrierServiceCustomizeRulesFilter = new carrierServiceCustomizeRulesFilter();
                                                $carrierServiceCustomizeRulesFilter->addFilter("serviceid = " . $newServiceId);
                                                $carrierServiceCustomizeRulesFilter->addFilter("user_account_id = " . $userAccountId);
                                                $carrierServiceCustomizeRulesFilter->addFilter("from_weight < " . $weightToSend);
                                                $carrierServiceCustomizeRulesFilter->addFilter("to_weight >= " . $weightToSend);
                                                $carrierServiceCustomizeRulesFilter->addFilter("status = 1");
                                                $carrierServiceCustomizeRules = $carrierServiceCustomizeRulesFilter->getColumnList("agentid");
                                                if (count($carrierServiceCustomizeRules) > 0) {
                                                    $agentId = $carrierServiceCustomizeRules[0]->getAgentid();
                                                } else {
                                                    $carrierServiceDefaultRulesFilter = new carrierServiceDefaultRulesFilter();
                                                    $carrierServiceDefaultRulesFilter->addFilter("serviceid = " . $newServiceId);
                                                    $carrierServiceDefaultRulesFilter->addFilter("from_weight < " . $weightToSend);
                                                    $carrierServiceDefaultRulesFilter->addFilter("to_weight >= " . $weightToSend);
                                                    $carrierServiceDefaultRulesFilter->addFilter("agent_type = 'outbound'");
                                                    $carrierServiceDefaultRules = $carrierServiceDefaultRulesFilter->getColumnList("agentid");
                                                    if (count($carrierServiceDefaultRules) > 0) {
                                                        $agentId = $carrierServiceDefaultRules[0]->getAgentid();
                                                    } else {
                                                        break;
                                                    }
                                                }
                                                $weightFoundCustomized = true;
                                                break;
                                            }
                                        }
                                    }
                                }
                            }

                            if (!mysqli_ping($dbConnection)) {
                                $dbConnection = DbAccess3::connect(SETTING_DB_SERVER, SETTING_DB_USER, SETTING_DB_PASSWORD, SETTING_DB_DATABASE);
                            }
                            //echo $orderReference . "<br>";
                            $queryCon = "SELECT
                                id,hawb, awb, shipment_status FROM
                                `consignment`
                                WHERE
                                    awb = '$trackingNumber'
                                    AND hawb = '$orderReference' 
                                    AND shipment_status <> '22'
                                    ";
                            //			echo "<br>";
                            $queryDataCon = $dbConnection->query($queryCon);


                            if (mysqli_num_rows($queryDataCon) > 0) {
                                //$count++;
                            } else {
                                // Make consignemnt data str for query
                                // Consignment add
                                $sqlStr .= ' (' . $userId . ',' .
                                        (trim($newServiceId) != '' ? $newServiceId : 0) . ',' .
                                        $warehouseId . ',' .
                                        (trim($trackingNumber) == '' ? 12 : 13) . ',"' .
                                        $trackingNumber . '",
                                    "' . (trim($trackingNumber) == '' ? 'ready to print' : 'label created') . '","' .
                                        date("Y-m-d H:i:s", strtotime($LabelCreatedDate)) . '","' .
                                        $receiverCompany . '","' .
                                        $receiverContact . '","' .
                                        $receiverAddressLine1 . '","' .
                                        $receiverAddressLine2 . '","' .
                                        $receiverAddressLine3 . '","' .
                                        $receiverCity . '","' .
                                        $receiverState . '","' .
                                        $receiverPostcode . '",' .
                                        $receiverCountryId . ',"' .
                                        $receiverTelephone . '",' .
                                        count($conData['parcel']) . ',"' .
                                        $parcelTotalWeight . '","' .
                                        $notes . '","' .
                                        $value . '","' .
                                        $currency . '","' .
                                        $senderName . ' ' . $senderContact . '","' .
                                        $senderCompany . '","' .
                                        $senderTelephone . '","' .
                                        $senderAddressLine1 . '","' .
                                        $senderAddressLine2 . '","' .
                                        $senderAddressLine3 . '","' .
                                        $senderCity . '","' .
                                        $senderPostcode . '",' .
                                        $senderCountryId . ',"' .
                                        $senderState . '","' .
                                        $orderReference . '","' .
                                        $senderEmail . '","' .
                                        $receiverEmail . '","' .
                                        $itemyype . '","' .
                                        $description . '","' .
                                        $routingCode . '","' .
                                        $volDemonimator . '",' .
                                        (trim($customizedServiceId) == '' ? 0 : $customizedServiceId) . ',"' .
                                        $weightType . '","' .
                                        $weightToSend . '","' .
                                        $vol_weight . '",' .
                                        (trim($agentId) == '' ? 0 : $agentId) . ',' .
                                        (trim($trackingNumber) == '' ? '0' : (trim(strtotime($LabelCreatedDate)) == '' ? 0 : strtotime($LabelCreatedDate)))
                                        . '' .
                                        "),";
                            }

//echo $receiverCountryId."<br>";
//echo $count." % 1000 == 0 || ".$count. "== ".$totalRecords."<br>";
//                                ,
//                                agent_id,
//                                                          date_label_created
                            //echo "<pre>";
                            // echo $count . " % " . 1000 . " == 0 || " . $count . " == " . $totalRecords . "<br>";

                            if ($count % 1000 == 0 || $count == $totalRecords) {

                                if (!mysqli_ping($dbConnection)) {
                                    $dbConnection = DbAccess3::connect(SETTING_DB_SERVER, SETTING_DB_USER, SETTING_DB_PASSWORD, SETTING_DB_DATABASE);
                                }
// Add consignment
                                $sqlStr = rtrim($sqlStr, ',');
                                $conQuery = $dbConnection->query($sqlStr);

                                if (!empty($conQuery->error)) {
                                    $errorFound = true;
                                    sendEmail("Error description: " . $conQuery->error . "<br/><br/>" . $sqlStr);
                                } else {

                                    // Add parcel
                                    $sqlParcelStr = rtrim($sqlParcelStr, ',');

                                    $parcelQuery = $dbConnection->query($sqlParcelStr);
                                    if (!empty($parcelQuery->error)) {
                                        $errorFound = true;
                                        sendEmail("Error description: " . $parcelQuery->error . "<br/><br/>" . $sqlParcelStr);
                                    } else {
                                        // Add parcel
                                        $sqlItemDetailsStr = rtrim($sqlItemDetailsStr, ',');

                                        $itemDetailsQuery = $dbConnection->query($sqlItemDetailsStr);
                                        if (!empty($itemDetailsQuery->error)) {
                                            $errorFound = true;
                                            sendEmail("Error description: " . $itemDetailsQuery->error . "<br/><br/>" . $sqlItemDetailsStr);
                                        } else {
                                            foreach ($mawbBaggingArr as $mawbNumber => $mawbData) {
                                                $mawbId = $mawbIdArr[$mawbNumber];
                                                foreach ($mawbData as $bagNumber => $parcelBagData) {
                                                    $bagId = $bagIdArr[$bagNumber];
                                                    if ($bagId > 0) {
                                                        foreach ($parcelBagData as $parcelTracking) {
                                                            $parcelFilter = new ParcelFilter();
                                                            $parcelFilter->addFieldFilter('    tracking_number', $parcelTracking);
                                                            $parcelFilterObj = $parcelFilter->getColumnList('id');
                                                            if (count($parcelFilterObj) > 0) {
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

                                                                if ($mawbId > 0) {
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
                                                                qty,
                                                                itemvalue,
                                                                pweight,
                                                                tarrif_no,
                                                                hscode,
                                                                commoditycode,
                                                                owe_status_code,
                                                                parcel_status_code,
                                                                number_item
                                                            )
                                                            VALUES';

                                // Empty Parcel
                                $sqlItemDetailsStr = 'INSERT INTO    `item_details` (
                                                                consignment_id,
                                                                item_detail,
                                                                user_id,
                                                                parcel_count
                                                            )
                                                            VALUES';


                                if ($count == $totalRecords) {
                                    $dire = "data_processed";
                                    if ($errorFound)
                                        $dire = "data_error";
                                    rename(SETTING_DIR_ASSETS . "user_data/" . $entry . "/data_in/" . $file, SETTING_DIR_ASSETS . "user_data/" . $entry . "/" . $dire . "/" . $file);
                                }
                            }

                            $count++;
                        }
                        // echo "adfsdfasdf";
                        //die;
                        rename(SETTING_DIR_ASSETS . "user_data/" . $entry . "/data_in/" . $file, SETTING_DIR_ASSETS . "user_data/" . $entry . "/data_error/" . $file);
                    } else {
                        sendEmail("No data found in this file " . $file, true, $file, $entry);
                    }
                }
            } else {
                ///sendEmail("No file found in this directory ".print_r( $filesData,true),true,'',$entry);
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
            rename(SETTING_DIR_ASSETS . "user_data/iparcel/data_in/" . $file, SETTING_DIR_ASSETS . "user_data/iparcel/data_error/" . $file);
        }
    }
}

function removeChar($text) {
    $textNew = $text;
    $textNew = str_replace("=", '', preg_replace('/[\$,]/', '', $textNew));
    $textNew = str_replace("\\", "", preg_replace('/[\$,]/', '', $textNew));
    $textNew = str_replace("'", "", preg_replace('/[\$,]/', '', $textNew));
    $textNew = str_replace("|", '\|', preg_replace('/[\$,]/', '', $textNew));
    $textNew = str_replace("\r\n", '', preg_replace('/[\$,]/', '', $textNew));
    $textNew = str_replace("\r", '', preg_replace('/[\$,]/', '', $textNew));
    $textNew = str_replace("\n", '', preg_replace('/[\$,]/', '', $textNew));

    $textNew = preg_replace('/[\n,]/', '', $textNew);

    return $textNew;
}

?>