<?php

namespace Smarttrack\V2;

use Consignment;
use Parcel;
use ParcelFilter;
use Tariffs;
use UPS;
use User;
use UserAccountFilter;
use UserHasGroupsFilter;
use UserServicesRouting;
use WarehouseNewFilter;
use Bagging;
use BaggingFilter;
use CountryFilter;
use WarehouseFilter;
use ParcelBaggingMapping;
use ParcelBaggingMappingFilter;
use MarketPlaces;
use MarketPlacesfilter;
use OauthAccessTokens;
use Wms;

class ApiFunctions
{
    public static function getUserById($userId)
    {
        $userData = new User($userId);
        return $userData;
    }

    public static function getUserAccountDetails($apiUserData)
    {
        $userAccounts = new UserAccountFilter();
        if ($apiUserData->getUserType() == User::USER_TYPE_CORPORATE) {
            $userAccounts->addFieldFilter('id', $apiUserData->getUserAccountId());
        } else {
            die('You are not allowed to perform this action');//will discuss later
        }
        $customerObjs = $userAccounts->getList();
        return $customerObjs;
    }

    public static function getUserServices($accountId, $serviceId = '')
    {
        $userServiceRouting = new UserServicesRouting();
        return $userServiceRouting->getAllUserServices($accountId, $serviceId);
    }

    public static function getServiceDeliveryCountries($serviceId)
    {
        $userServiceRouting = new UserServicesRouting();
        return $userServiceRouting->getServiceDeliveryCountries($serviceId);
    }

    public static function getTracking($trackingNo)
    {
        $trackingNumber = cleanTrackingNo($trackingNo);
        $trackingObj = new \Tracking(); //  Calling the constructor
        return $trackingObj->GetTracking($trackingNumber);
    }

    public static function getMultiTracking($trackingNumbers)
    {
        $trackingObj = new \Tracking(); //  Calling the constructor
        return $trackingObj->GetMultiTracking($trackingNumbers);
    }

    public static function validateShipment($shipData, $user_id)
    {
        $errorsArray = [];
        $userObj = new \User($user_id);
        $serviceObj = new \Services();
        $serviceCode = (isset($shipData['service_code']) ? $shipData['service_code'] : null);
        $serviceObj = $serviceObj->getServiceByCode(isset($shipData['service_code']) ? $shipData['service_code'] : null);
        if ($serviceObj) {
            $serviceId = $serviceObj->getId();
            $shipData['service_type'] = $serviceId;
            $shipData['service_id'] = $serviceId;
            $shipData['is_product'] = $serviceObj->getIsCustomized();
        } else {
            $errorsArray['MESSAGE'] = "Invalid Service Code";
            $errorsArray['ERROR'][] = "serviceCode $serviceCode is invalid.System cannot find any service for the specified service code";
        }
        $senderCountry = \Country::getCountryFromIso(isset($shipData['sender_country_iso']) ? $shipData['sender_country_iso'] : "XXXXXXX");
        if ($senderCountry) {
            $senderCountryId = $senderCountry->getId();
            $shipData['country'] = $senderCountryId;
            $shipData['sender_country'] = $senderCountryId;
        } else {
            $errorsArray['MESSAGE'] = "Invalid Sender Country Iso";
            $errorsArray['ERROR'][] = "countryIso is invalid. Please enter a valid sender country iso.";
        }
        $receiverCountry = \Country::getCountryFromIso(isset($shipData['receiver_country_iso']) ? $shipData['receiver_country_iso'] : "XXXXXXX");
        if ($receiverCountry) {
            $receiverCountryId = $receiverCountry->getId();
            $shipData['country'] = $receiverCountryId;
            $shipData['receiver_country'] = $receiverCountryId;
        } else {
            $errorsArray['MESSAGE'] = "Invalid Receiver Country Iso";
            $errorsArray['ERROR'][] = "countryIso is invalid. Please enter a valid receiver country iso.";
        }

        if (!empty($errorsArray)) {
            $errorsArray['STATUS'] = "ERROR";
            return $errorsArray;
        } else {
            $shipData['user_id'] = $user_id;
            $consignment = \Consignment::getConsignmentObjectFromArray($shipData);
            $consignmentValidator = new \ConsignmentValidator($consignment, $shipData['parcel'], $userObj);
            $IsValidConsignment = $consignmentValidator->validateConsignment();
            if (!$IsValidConsignment) {
                $errorsArray['STATUS'] = "ERROR";
                $errorsArray['ERROR'] = $consignmentValidator->getErrorList();
                return $errorsArray;
            }
        }
        unset($shipData['serviceCode']);
        unset($shipData['countryIso']);
        return ["STATUS" => "SUCCESS", "DATA" => $shipData];
    }

    public static function addShipment($shipData, $user_id)
    {
        $shipData['save_invalid'] = 0;
        $errorsArray = [];
//        $userObj = new \User($user_id);
        $serviceObj = new \Services();
        $serviceCode = (isset($shipData['service_code']) ? $shipData['service_code'] : null);
        $serviceObj = $serviceObj->getServiceByCode(isset($shipData['service_code']) ? $shipData['service_code'] : null);
        if ($serviceObj) {
            $serviceId = $serviceObj->getId();
            $shipData['service_type'] = $serviceId;
            $shipData['service_id'] = $serviceId;
            $shipData['is_product'] = $serviceObj->getIsCustomized();
        } else {
            $errorsArray['MESSAGE'] = "Invalid Service Code";
            $errorsArray['ERROR'][] = "serviceCode $serviceCode is invalid.System cannot find any service for the specified service code";
        }
        $senderCountry = \Country::getCountryFromIso(isset($shipData['sender_country_iso']) ? $shipData['sender_country_iso'] : "XXXXXXX");
        if ($senderCountry) {
            $senderCountryId = $senderCountry->getId();
            $shipData['country'] = $senderCountryId;
            $shipData['sender_country'] = $senderCountryId;
        } else {
            $errorsArray['MESSAGE'] = "Invalid Sender Country Iso";
            $errorsArray['ERROR'][] = "countryIso is invalid. Please enter a valid sender country iso";
        }
        $receiverCountry = \Country::getCountryFromIso(isset($shipData['receiver_country_iso']) ? $shipData['receiver_country_iso'] : "XXXXXXX");
        if ($receiverCountry) {
            $receiverCountryId = $receiverCountry->getId();
            $shipData['country'] = $receiverCountryId;
            $shipData['receiver_country'] = $receiverCountryId;
        } else {
            $errorsArray['MESSAGE'] = "Invalid Receiver Country Iso";
            $errorsArray['ERROR'][] = "countryIso is invalid. Please enter a valid receiver country iso";
        }

        // Seller country iso check
        if(isset($shipData['seller_country_iso']) && !empty($shipData['seller_country_iso'])) {
            $sellerCountry = \Country::getCountryFromIso(isset($shipData['seller_country_iso']) ? $shipData['seller_country_iso'] : "XXXXXXX");
            if (!$sellerCountry) {
                $errorsArray['MESSAGE'] = "Invalid Seller Country Iso";
                $errorsArray['ERROR'][] = "countryIso is invalid. Please enter a valid seller country iso";
            }
        }
        //

        if (!empty($errorsArray)) {
            $errorsArray['STATUS'] = "ERROR";
            return $errorsArray;
        } else {
            $shipData['user_id'] = $user_id;
            $createShip = Consignment::saveShipment($shipData, $user_id, false, '', 'api');
            return $createShip;
        }
    }

    public static function getLabel($hawb, $labelType = "pdf", $labelSize = "100x150", $apiUserData = [])
    {
        $error['STATUS'] = "ERROR";
        $error['LABEL'] = null;
        $error['MESSAGE'] = "Order reference not found";
        $error['ERROR'][] = "Order reference not found";
        if (!empty($hawb)) {
            $consignmentFilter = new \ConsignmentFilter();
            $consignmentFilter->addFieldFilter("    hawb", $hawb);
            //$consignmentFilter->addFilter("    c.hawb = '".$hawb."' and c.shipment_status not in ('22')",'filter' );
          //  $consignmentFilter->addFieldEqualFilter("hawb", "=", $validateScannig);
            $consignmentFilter->addFieldEqualFilter("shipment_status", "<>", Consignment::STATUS_RECYCLED);
            $consignmentObjs = $consignmentFilter->getListNew();
            $consignmentObj = [];
            if (!empty($consignmentObjs)) {
                $consignmentObj = $consignmentObjs[0];
            }
/*            if(empty($consignmentObj)) {
                $consignmentFilter = new \ConsignmentFilter();
                $consignmentFilter->addFieldFilter("    hawb", $hawb);
                //$consignmentFilter->addFilter("    c.hawb = '".$hawb."' and c.shipment_status not in ('22')",'filter' );
                //  $consignmentFilter->addFieldEqualFilter("hawb", "=", $validateScannig);
                $consignmentFilter->addFieldEqualFilter("shipment_status", "=", Consignment::STATUS_INVALID);
                $consignmentFilter->addFieldEqualFilter("user_id", "=", $apiUserData->getId());
                $consignmentObjs = $consignmentFilter->getListNew();
                $consignmentObj = [];
                if (!empty($consignmentObjs)) {
                    $consignmentObj = $consignmentObjs[0];
                }
            }*/
            if ($consignmentObj && $consignmentObj->getId() > 0) {
                $createShip = Consignment::getInstantLabel($consignmentObj, $labelType, $labelSize, true);
                return $createShip;
            } else {
                return $error;
            }
        } else {
            return $error;
        }
    }

    public static function UserQuotes($quoteData, $userAccountId)
    {
        $originCountry = \Country::getCountryFromIso(isset($quoteData['origin_country_iso']) ? $quoteData['origin_country_iso'] : "XXXXXXX");
        if ($originCountry) {
            $quoteData['origin_country_id'] = $originCountry->getId();
        } else {
            $errorsArray['MESSAGE'] = "Invalid Country Iso";
            $errorsArray['ERROR'][] = "origin Country Iso is invalid. Please enter a valid country iso.";
        }

        $deliveryCountry = \Country::getCountryFromIso(isset($quoteData['delivery_country_iso']) ? $quoteData['delivery_country_iso'] : "XXXXXXX");
        if ($deliveryCountry) {
            $quoteData['delivery_country_id'] = $deliveryCountry->getId();
        } else {
            $errorsArray['MESSAGE'] = "Invalid Country Iso";
            $errorsArray['ERROR'][] = "delivery Country Iso is invalid. Please enter a valid country iso.";
        }

        if (!empty($errorsArray)) {
            $errorsArray['STATUS'] = "ERROR";
            return $errorsArray;
        }
        unset($quoteData['origin_country_iso']);
        unset($quoteData['delivery_country_iso']);
//		echo "<pre>"; print_r($quoteData); echo "</pre>"; die();
        $originPostcode = isset($quoteData['origin_postcode']) ? $quoteData['origin_postcode'] : "";
        $deliveryPostcode = isset($quoteData['delivery_postcode']) ? $quoteData['delivery_postcode'] : "";
        $originCity = isset($quoteData['origin_city']) ? $quoteData['origin_city'] : "";
        $deliveryCity = isset($quoteData['delivery_city']) ? $quoteData['delivery_city'] : "";

        if (!empty($quoteData['parcel'])) {
            $quoteWeight = 0;
            foreach($quoteData['parcel'] as $parcel) {
                $quoteWeight = $quoteWeight + $parcel['weight'];
            }
        } else {
            $quoteWeight = 0;
        }

        if (isset($quoteData['parcel']) && count($quoteData['parcel']) > 0) {
            $parcelCount = count($quoteData['parcel']);
        }
        if ($parcelCount < 0)
            $parcelCount = 0;
        $parcelLenghtError["ERROR"] = [];
        foreach ($quoteData['parcel'] as $parcelId => $quoteParcel) {
            foreach ($quoteParcel as $parcelColumn => $parcelItem) {
                if (!preg_match("/^\d+(\.\d{1,3})?$/", $parcelItem) || $parcelItem == "") {
                    $parcelLenghtError["ERROR"][$parcelId][$parcelColumn] = "Please enter valid [up to 3 decimal points] parcel " . ($parcelId) . " " . $parcelColumn;
                }
            }
        }
        if (!empty($parcelLenghtError["ERROR"]))
            return $parcelLenghtError;

        return $userQuotations = Tariffs::getUserQuotationsByAssignedServices($userAccountId, $quoteData['origin_country_id'], $quoteData['delivery_country_id'], $originPostcode, $deliveryPostcode, $originCity, $deliveryCity, $quoteWeight, $parcelCount, 0, 0, 0, 0, "");
    }

    public static function createUserAccount($accountData, $apiUserData)
    {
        $countryExt = \Country::getCountryFromIso(isset($accountData['country_iso']) ? $accountData['country_iso'] : "XXXXXXX");
        if ($countryExt) {
            $accountData['countryId'] = $countryExt->getId();
        } else {
            $errorsArray['MESSAGE'] = "Invalid Country Iso";
            $errorsArray['ERROR'][] = "CountryIso is invalid. Please enter a valid country iso.";
        }
        $companyRegCountry = \Country::getCountryFromIso(isset($accountData['company_reg_country_iso']) ? $accountData['company_reg_country_iso'] : "XXXXXXX");
        if ($companyRegCountry) {
            $accountData['companyRegCountryId'] = $companyRegCountry->getId();
        } else {
            $errorsArray['MESSAGE'] = "Invalid Country Iso";
            $errorsArray['ERROR'][] = "companyRegCountryIso is invalid. Please enter a valid country iso.";
        }
//		echo "<pre>"; print_r($errorsArray); echo "</pre>"; die();
        if (!empty($errorsArray)) {
            $errorsArray['STATUS'] = "ERROR";
            return $errorsArray;
        }
        unset($accountData['countryIso']);
        unset($accountData['companyRegCountryIso']);

        return \CustomerAccount::createUserAccountApi($accountData, $apiUserData);

    }

    public static function getUserAccounts($apiUserData)
    {
        $userAccounts = new UserAccountFilter();
        if ($apiUserData->getUserType() == User::USER_TYPE_CORPORATE) {
            $userAccounts->addFieldFilter('parentid', $apiUserData->getUserAccountId());
        } else {
            $userAccounts->addFieldFilter("    id",$apiUserData->getUserAccountId());
        }
        $customerObjs = $userAccounts->getList();
        return $customerObjs;
    }

    public function getUserAccessabilities($apiUserData)
    {
        $groupsStrIn = "";
        if ($apiUserData->getUserType() != User::USER_TYPE_ADMIN) {
            $userHasGroupsFilter = new UserHasGroupsFilter();
            $userHasGroupsFilter->addFilter(" admin_id = " . $apiUserData->getId());
            $groups = $userHasGroupsFilter->getList();

            $groupsStr = "";
            foreach ($groups as $group) {
                $groupsStr .= "'" . $group->getGroupId() . "',";
            }
            if (!empty($groupsStr))
                $groupsStrIn = " AND group_id IN (" . rtrim($groupsStr, ',') . ") ";
        }
        $groupsList = new \GroupsFilter();
        $groupsList->addFilter("is_deleted = 0 AND is_active = 1 " . $groupsStrIn . "");
        return $userGroupList = $groupsList->getList();

    }
    public function currencyConverter($fromCurrencyCode,$toCurrencyCode,$amount){
        $sqlCustomerPriceRecords = "SELECT currency_converter('" . $fromCurrencyCode . "', '" . $toCurrencyCode . "', '" . $amount . "' ) as customer_price";
        $resultCustomerPriceSql = \DbAccess3::runQuery($sqlCustomerPriceRecords);
        $objCustomerPrice = mysqli_fetch_assoc($resultCustomerPriceSql);
        $customerPrice = $objCustomerPrice['customer_price'];
        return $customerPrice;
    }

    public function GetWarehouseList($apiUserData)
    {
        $wareHouseFilter = new WarehouseNewFilter();
        $wareHouseFilter->addCountryJoin();
        return $wareHouseList = $wareHouseFilter->getList();

    }

    public static function addUser($accountData, $apiUserData)
    {
//		echo "<pre>"; print_r($accountData); echo "</pre>"; die();
        $countryExt = \Country::getCountryFromIso(isset($accountData['countryIso']) ? $accountData['countryIso'] : "XXXXXXX");
        if ($countryExt) {
            $accountData['countryId'] = $countryExt->getId();
        } else {
            $errorsArray['MESSAGE'] = "invalid parameters";
            $errorsArray['ERRORS'][] = "CountryIso is invalid. Please enter a valid country iso.";
        }
        //check if warehousecode is valid
        if (isset($accountData['warehouseCode'])) {
            $warehouseCodeCheck = new WarehouseNewFilter();
            $warehouseExist = $warehouseCodeCheck->getWarehouseByCode($accountData['warehouseCode']);
            if (count($warehouseExist) < 1) {
                $errorsArray['MESSAGE'] = "invalid parameters";
                $errorsArray['ERRORS'][] = "Warehouse code not exist. Please enter valid code!";
            } else {
                $accountData['warehouseId'] = isset($warehouseExist[0]) ? $warehouseExist[0]->getId() : NULL;
            }
        }
        //check if accessabilites are valid
        $accountData['permission_group'] = [];
        if (isset($accountData['accessabilities'])) {
            $accessabilitesCode = explode(",", $accountData['accessabilities']);
            $assignedAccessAbilies = [];
            $assignedAccessAbiliesIds = [];
            $userAccessAbilites = ApiFunctions::getUserAccessabilities($apiUserData);
            if (count($userAccessAbilites) > 0) {
                foreach ($userAccessAbilites as $accessability) {
                    $assignedAccessAbilies[$accessability->getGroupId()] = $accessability->getGroupSlug();
                }
            }
            //check if request accessabilities are from user assigned accessabilites
            $diff = array_diff($accessabilitesCode, $assignedAccessAbilies);

            if (count($diff) > 0) {
                $errorsArray['MESSAGE'] = "AccessError";
                $errorsArray['ERRORS'][] = "You can not assign these accessabilites (" . implode(",", $diff) . ").Please contact your system administrator for more help!";
            } else {
                $selectedAccessabilites = array_intersect($assignedAccessAbilies, $accessabilitesCode);
                if (count($selectedAccessabilites) > 0) {
                    $accountData['permission_group'] = array_keys($selectedAccessabilites);
                }
            }
        }
        //check duplication for username
        if (isset($accountData['userName'])) {
            $userfilter = new \UserFilter();
            $userfilter->addUserNameFilter(trim($accountData['userName']));
            $userfilter->getList();
            $checkuser = $userfilter->getCount();
            if ($checkuser > 0) {
                $errorsArray['MESSAGE'] = "Duplicate Fields";
                $errorsArray['ERRORS'][] = "Duplicated Username, please use another name!";
            }
        }

        if (!empty($errorsArray)) {
            $errorsArray['STATUS'] = "ERROR";
            return $errorsArray;
        }
        unset($accountData['countryIso']);
        return User::createUserApi($accountData, $apiUserData);

    }

    public static function addScanning($validateScannigData, $apiUserData)
    {
        
        if(empty($validateScannigData['tracking_number'])){
                $response['STATUS'] = "ERROR";
                $response['MESSAGE'] = " Parcel tracking number is required.";
                $response['ERROR'][] = " Parcel tracking number is required.";
                return $response;
            }
            
        // Set variables
        $errorsArray = [];
        $dateTime = $validateScannigData['scan_time'];
        $countryId = $apiUserData->getCountryId();
        $country = new \Country($countryId);
        $trackpoint = '';
        $countryIso3 = '';
        $warehouseName = '';
        $carrierDesc = '';
        if (count($country) > 0)
            $countryIso3 = $country->getIso3();

        $warehouseid = $apiUserData->getWarehouseId();
        if ($warehouseid > 0) {
            $warehouseObj = new \Warehouse($warehouseid);
            $warehouseName = $warehouseObj->getWarehouseName();
        }
        $trackpoint = $warehouseName . " - " . $countryIso3;
        if ($validateScannigData['status_code'] == 146) {
            if (!empty($warehouseName))
                $carrierDesc = 'Arrived at Sort Facility ' . $trackpoint;
        } else if ($validateScannigData['status_code'] == 144) {
            if (!empty($warehouseName))
                $carrierDesc = 'Departed Facility in ' . $trackpoint;
        } else {
            $carrierDesc =  \Tracking::$oneworld_status_desc[$validateScannigData['status_code']];
            $trackpoint = "";
        }
        if(!empty($validateScannigData['track_point'])){
			$trackpoint = $validateScannigData['track_point'];
		}
                
        // Check if parcel is exist
        $parcelFilter = new \ParcelFilter();
        $parcelFilter->addFieldFilter("tracking_number", \ParseTrackingNumber::Parse($validateScannigData['tracking_number']));
        $parcelObj = $parcelFilter->getColumnList("tracking_number");
        if (count($parcelObj) > 0) {
            // Parcel Exist
            // Check if parcel is already scaned with same warehouse and status code
            if(self::alreadyScannedParcelSameStatusCode($validateScannigData['tracking_number'],$warehouseid,$validateScannigData['status_code'])){
                $response['STATUS'] = "ERROR";
                $response['MESSAGE'] = $validateScannigData['tracking_number']." Parcel is already scanned";
                $response['ERROR'][] = $validateScannigData['tracking_number']." Parcel is already scanned";
                return $response;
            }
            $conStatus = \Tracking::$oneworld_consignment_code_mapping[$validateScannigData['status_code']];
            \Parcel::setParcelStatus($parcelObj[0]->getId(), $conStatus);
            changeConStatusByParcelStatus($parcelObj[0]->getId());
            //Save data into tracking_data table
            $trackingData = new \TrackingData();
            $trackingData->setEntityId($parcelObj[0]->getId());
            $trackingData->setEntityType('parcel');
            $trackingData->setTrackingNumber(\ParseTrackingNumber::Parse($validateScannigData['tracking_number']));
            $trackingData->setUserId($apiUserData->getId());
            $trackingData->setTrackPoint($trackpoint);
            $trackingData->setDateCreated($dateTime);
            $trackingData->setStatusCodeId($validateScannigData['status_code']);
            $trackingData->setIpAddress($validateScannigData['ip_address']);
            $trackingData->setCarrierDesc($carrierDesc);
            $trackingData->setWarehouseId($warehouseid);

            if (!empty($validateScannigData['parcel_image'])) {
                $imageReturnData = self::saveImageFromApi($validateScannigData['parcel_image'], "parcel_img");
                $imageFilePath = $imageReturnData['file_name'];
                $trackingData->setParcelImage($imageFilePath);
                if (!empty($imageReturnData['return'])) {
                    $errorsArray = $imageReturnData['return'];
                }
            }
            if (!empty($validateScannigData['signature']))
                $trackingData->setSignatory($validateScannigData['signature']);

            if (!empty($validateScannigData['signature_image'])) {
                $imageReturnData = self::saveImageFromApi($validateScannigData['signature_image'], "pod_img");
                $imageFilePathPod = $imageReturnData['file_name'];
                $trackingData->setPodImage($imageFilePathPod);
                if (!empty($imageReturnData['return'])) {
                    $errorsArray = $imageReturnData['return'];
                }
            }
            if (!empty($validateScannigData['latitude']))
                $trackingData->setLatitude($validateScannigData['latitude']);

            if (!empty($validateScannigData['longitude']))
                $trackingData->setLongitude($validateScannigData['longitude']);

            $trackingData->save();
            if ($trackingData->getId() > 0) {
                $response['STATUS'] = "SUCCESS";
                $response['MESSAGE'] = "Parcel scanned successfully";
            } else {
                $errorsArray['MESSAGE'] = "Parcel not scanned";
                $errorsArray['ERROR'][] = "Parcel not scanned, please check your scanning data!";
            }
        } else {
            // Parcel Not Exist
            $errorsArray['MESSAGE'] = "Parcel not found";
            $errorsArray['ERROR'][] = "Parcel not found in system, please scan other parcel!";
        }
        if (!empty($errorsArray)) {
            $errorsArray['STATUS'] = "ERROR";
            return $errorsArray;
        } else {
            return $response;
        }
    }

    public static function saveImageFromApi($data, $fileName, $type = "png")
    {
        $returnData = [];
        $errorsArray = [];
        if (!file_exists("../_assets/images/pod_images")) {
            mkdir("../_assets/images/pod_images", 0777, true);
        }
        $data = base64_decode($data);
        if ($data === false) {
            $errorsArray['STATUS'] = "ERROR";
            $errorsArray['MESSAGE'] = "Invalid image data, Please varify your encoded string";
            $errorsArray['ERROR'][] = "Invalid image data, Please varify your encoded string";
            $returnData['return'] = $errorsArray;
            $returnData['type'] = "";
            $returnData['file_name'] = "";
            return $returnData;
        }
        $type = self::RetrieveExtension($data);
        if (!in_array($type, ['jpg', 'jpeg', 'gif', 'png'])) {
            $errorsArray['STATUS'] = "ERROR";
            $errorsArray['MESSAGE'] = "Invalid image type, allow image types are 'jpg', 'jpeg','gif', 'png'";
            $errorsArray['ERROR'][] = "Invalid image data, allow image types are 'jpg', 'jpeg','gif', 'png'";
            $returnData['return'] = $errorsArray;
            $returnData['type'] = "";
            $returnData['file_name'] = "";
            return $returnData;
        }
        $fileName = $fileName . "_" . time() . "." . $type;
        $filePath = "../_assets/images/pod_images/" . $fileName;
        file_put_contents($filePath, $data);
        $returnData['return'] = $errorsArray;
        $returnData['type'] = $type;
        $returnData['file_name'] = $fileName;
        return $returnData;
    }

    public static function RetrieveExtension($data)
    {
        if (!file_exists("../_assets/images/pod_images/temp")) {
            mkdir("../_assets/images/pod_images/temp", 0777, true);
        }
        $tempFile = "../_assets/images/pod_images/temp/" . time();
        $validExtensions = ['png', 'jpeg', 'jpg', 'gif'];
        file_put_contents($tempFile, $data);
        $contentType = finfo_file(finfo_open(FILEINFO_MIME_TYPE), $tempFile);
        if (substr($contentType, 0, 5) !== 'image') {
            $extension = "not image";
            unlink($tempFile);
            return $extension;
        }
        $extension = ltrim($contentType, 'image/');
        unlink($tempFile);
        return $extension;
    }

    public static function voidLabel($validateScannigData, $apiUserData = "")
    {
        // set variable
        $consignmentIdArr = [];
        $response = [];
        $notFound = [];
        $warning = false;
        // Get consignment id from hawb number
        if (count($validateScannigData['order_reference']) > 0) {
            foreach ($validateScannigData['order_reference'] as $validateScannig) {
                $consignmentFilter = new \ConsignmentFilter();
                $consignmentFilter->addFieldEqualFilter("hawb", "=", $validateScannig);
                $consignmentFilter->addFieldEqualFilter("shipment_status", "<>", Consignment::STATUS_RECYCLED);
                $consignmentObj = $consignmentFilter->getListNew("id");
                if (count($consignmentObj) > 0) {
                    $consignmentIdArr[] = $consignmentObj[0]->getId();
                } else {
                    $notFound[] = $validateScannig;
                }
            }
        }
        if (count($consignmentIdArr) > 0) {
            $consignment = new \Consignment();
            $resData = $consignment->RecycledShipment($consignmentIdArr, true,true);
            
    
            if ($resData['status'] == "ERROR") {
                $response['STATUS'] = "ERROR";
                $response['MESSAGE'] = $resData['message'];
                $response['ERROR'][] = $resData['error'];
            } else if ($resData['status'] == "SUCCESS") {
                $response['STATUS'] = "SUCCESS";
                $response['MESSAGE'] = $resData['message'];
                $warning = true;
            }
        }
        foreach ($notFound as $hawbNumber) {
            if (count($consignmentIdArr) > 0)
                $response['STATUS'] = "SUCCESS";
            else
                $response['STATUS'] = "ERROR";

            $response['ERROR'][] = "Invalid data, Please enter valid consignment order reference " . $hawbNumber;
        }
        if($warning)
            $response['STATUS'] = "SUCCESS";

        return $response;
    }

    public function voidBagParcel($params)
    {
        $output = [];
        $warning = false;
        $bagNumber = trim ($params['bag_number']);
        if(isset($params['bag_number']) && !empty($params['bag_number']) && count($params['tracking_numbers']) > 0){
            $bagId = 0;
            $baggingFilter = new BaggingFilter();
            $baggingFilter->addFieldFilter('    bagnumber',$bagNumber);
            $baggingData = $baggingFilter->getColumnList('id');
            if(count($baggingData) > 0){
                $bagId = $baggingData[0]->getId();
            }
            if($bagId > 0){
                foreach ($params['tracking_numbers'] as $trackingNumber) {
                    $parcelFilter = New ParcelFilter();
                    $parcelFilter->addFieldFilter('    tracking_number',$trackingNumber);
                    $parcelObj = $parcelFilter->getColumnList('id');
                    if(count($parcelObj) > 0){
                        $parcelId = $parcelObj[0]->getId();
                        if($parcelId > 0){
                            // Check if bag is closed
                            $bagging = new Bagging($bagId);
                            if($bagging->getIsClosed() == 1){
                                $output['STATUS'] = "ERROR";
                                $output['MESSAGE'][] = "Bag is closed. Cannot remove parcel";
                                $output['ERROR'][] = "Bag is closed. Cannot remove parcel";
                            }else{
                                $parcelBaggingMappingFilter = new ParcelBaggingMappingFilter();
                                $parcelBaggingMappingFilter->addFieldFilter("      parcel_id", $parcelId);
                                $parcelBaggingMappingFilter->addFieldFilter("      bag_id", $bagId);
                                $parcelBaggingMappingFilter->delete_parcel_from_mapping();
                                $output['STATUS'] = "SUCCESS";
                                $output['MESSAGE'][] = "Your parcel ".$trackingNumber." is deleted successfully";
                                $warning = true;
                            }
                        }else{
                            $output['STATUS'] = "ERROR";
                            $output['MESSAGE'][] = "Parcel ".$trackingNumber." not found in the system";
                            $output['ERROR'][] = "Parcel ".$trackingNumber." not found in the system";
                        }
                    }else{
                        $output['STATUS'] = "ERROR";
                        $output['MESSAGE'] = "Parcel ".$trackingNumber." not found in the system";
                        $output['ERROR'][] = "Parcel ".$trackingNumber." not found in the system";
                    }
                }
            }else{
                $output['STATUS'] = "ERROR";
                $output['MESSAGE'][] = "Parcel or bag not found";
                $output['ERROR'][] = "Parcel or bag not found";
            }
        }
        if($warning)
            $output['STATUS'] = "SUCCESS";

        return $output;
    }
    public static function CreateManifest($validateManifestData, $apiUserData)
    {
        // Set variable
        $response = [];
        $serviceId = [];
        // Check if parcel is exist
        $consignmentIdArr = [];
        $trackingNotFound = [];
        $parcelIdArr = [];
        foreach ($validateManifestData['tracking_number'] as $validateManifest) {
            $parcelFilter = new \ParcelFilter();
            $parcelFilter->addFieldFilter("tracking_number", $validateManifest);
            $parcelObj = $parcelFilter->getColumnList("tracking_number,consignment_id");
            if (count($parcelObj) > 0) {
                foreach ($parcelObj as $parcel) {
                    $consignmentIdArr[] = $parcel->getConsignmentId();
                }
                $parcelIdArr[] = $parcel->getId();
            } else {
                $trackingNotFound[] = "Parcel not found " . $validateManifest;
            }
        }
        if (count($trackingNotFound) > 0) {
            $response['STATUS'] = "ERROR";
            $response['MESSAGE'] = $trackingNotFound;
            $response['ERRORS'][] = $trackingNotFound;
        } else {
            $consignmentFilter = new \ConsignmentFilter();
            $consignmentFilter->addIdArrayFilter($consignmentIdArr);
            $consignmentRes = $consignmentFilter->getListNew("c.service_id");
            if (count($consignmentRes) > 0) {
                foreach ($consignmentRes as $consignment) {
                    $serviceId[] = $consignment->getServiceId();
                }
            }
            $serviceId = array_unique($serviceId);
            
            if (count($serviceId) > 1) {
                $response['STATUS'] = "ERROR";
                $response['MESSAGE'] = "Provided parcels are not belong to same service";
                $response['ERRORS'][] = "Invalid data, Provided parcels are not belong to same service";
            } else {
                $warehouseid = $apiUserData->getWarehouseId();
                $returnData = \Manifest::manifestCreate($parcelIdArr, $serviceId[0],"client", 0,$warehouseid,$apiUserData);
                
                if (isset($returnData['STATUS']) && $returnData['STATUS'] == "Error") {
                    $response['STATUS'] = "ERROR";
                    $response['MESSAGE'] = $returnData['MESSAGE'];
                    $response['ERRORS'][] = $returnData['MESSAGE'];
                } else {
                    $response['STATUS'] = "SUCCESS";
                    $response['MESSAGE'] = $returnData['MESSAGE'];
                    $response['DATA']['id'] = $returnData['ID'];
                    $response['DATA']['manifest_reference'] = $returnData['ID'];
                    $response['DATA']['pdf']['url'] = $returnData['PDF_FILE'];
                    $response['DATA']['pdf']['bin_str'] = base64_encode(file_get_contents($returnData['PDF_FILE']));                    
                    $response['DATA']['csv']['url'] = $returnData['CSV_FILE'];
                    $response['DATA']['csv']['bin_str'] = base64_encode(file_get_contents($returnData['CSV_FILE']));
                    
                        
                }
            }
        }
        return $response;
    }

    public static function validateServiceDimension($validatedServiceData, $userData)
    {
        $errosArray['STATUS'] = "SUCCESS";
        $errosArray['MESSAGE'] = "Validate successfully";
        $error_list = [];
        $count = 1;
        $parcelsTotalWeight = 0;
        $dimention = [];
        $parcels = $validatedServiceData['parcel'];
        foreach ($parcels as $parcelArr) {
            if ($parcelArr['weight'] < 0 || trim($parcelArr['weight']) == "" || !is_numeric($parcelArr['weight'])) {
                $error_list[] = 'Please enter parcel ' . $count . ' valid weight';
            }
            if ($parcelArr['length'] < 0 || trim($parcelArr['length']) == "" || !is_numeric($parcelArr['length'])) {
                $parcelArr['length'] = 0;
                $error_list[] = 'Please enter parcel ' . $count . ' valid length';
            }
            if ($parcelArr['width'] < 0 || trim($parcelArr['width']) == "" || !is_numeric($parcelArr['width'])) {
                $parcelArr['width'] = 0;
                $error_list[] = 'Please enter parcel ' . $count . ' valid width';
            }
            if ($parcelArr['height'] < 0 || trim($parcelArr['height']) == "" || !is_numeric($parcelArr['height'])) {
                $parcelArr['height'] = 0;
                $error_list[] = 'Please enter parcel ' . $count . ' valid height';
            }
            $parcelsTotalWeight += $parcelArr['weight'];
            $dimention[] = $parcelArr['length'];
            $dimention[] = $parcelArr['width'];
            $dimention[] = $parcelArr['height'];
            $count++;
        }
        // if we found any error then we will return from here
        if (count($error_list) > 0) {
            $errosArray['STATUS'] = "ERROR";
            $errosArray['MESSAGE'] = "Invalid parcel values";
            $errosArray['ERROR'] = $error_list;
            return $errosArray;
        }
        $serviceCode = $validatedServiceData['service_code'];
        $service = new \Services();
        $serviceObj = $service->getServiceByCode($serviceCode);
        $serviceId = "";
        if (!empty($serviceObj)) {
            $serviceId = $serviceObj->getId();
        } else {
            $errosArray['STATUS'] = "ERROR";
            $errosArray['MESSAGE'] = "Invalid Service Code";
            $errosArray['ERROR'] = "serviceCode $serviceCode is invalid.System cannot find any service for the specified service code";
            return $errosArray;
        }
        // Check if service is custommized
        $customizedServicesRouting = NULL;
        if ($serviceObj->getIsCustomized() == "1") {
            $countryFilter = new \CountryFilter();
            $countryFilter->addFieldFilter("iso", $validatedServiceData['receiver_country_iso']);
            $countryObj = $countryFilter->getList();
            $receiverCountryId = "";
            $receiverCountryName = "";
            if (count($countryObj) > 0) {
                $receiverCountryId = $countryObj[0]->getId();
                $receiverCountryName = $countryObj[0]->getName();
            } else {
                $errosArray['STATUS'] = "ERROR";
                $errosArray['MESSAGE'] = "Invalid receiver Country Iso";
                $errosArray['ERROR'] = "Receiver country iso is invalid. System cannot find any country for the specified receiver country iso";
                return $errosArray;
            }
            $customizedServicesRoutingFilter = new \CustomizedServicesRoutingFilter();
            $customizedServicesRoutingFilter->addJoin('services s', 's.id=csr.service_id');
            $customizedServicesRoutingFilter->addFieldFilter('country_id', $receiverCountryId);
            $customizedServicesRoutingFilter->addFieldFilter('customize_service_id', $serviceId);
            $customizedServicesRoutingFilter->addFieldFilter('status', '1');
            $customizedServicesRouting = $customizedServicesRoutingFilter->getColumnList("csr.service_id,csr.from_weight,csr.to_weight,s.volumetric_denominator");
            if (count($customizedServicesRouting) > 0) {
                $weightNotFound = true;
                foreach ($customizedServicesRouting as $customizedServicesRoutingArr) {
                    if ($customizedServicesRoutingArr->getFromWeight() < $parcelsTotalWeight && $customizedServicesRoutingArr->getToWeight() >= $parcelsTotalWeight) {
                        $weightNotFound = false;
                    }
                }
                if ($weightNotFound) {
                    $errosArray['STATUS'] = "ERROR";
                    $errosArray['MESSAGE'] = "Weight not allowed for user";
                    $errosArray['ERROR'] = "The service ( " . $serviceObj->getName() . " ), weight ( " . $parcelsTotalWeight . " Kg ) ,  country ( " . $receiverCountryName . " ) is not allowed for your account, Please select other service.";//$ErrorString;
                    return $errosArray;
                }
            }
        }
        $totalvolweight = 0;
        $maxvolweight = 0;
        $weightToSend = 0;
        $vol_weight = 0;
        $volweight = 0;
        $maxParcelWeight = 0;
        foreach ($parcels as $parcelArr) {
            $maximumAllowedDimension = $serviceObj->getMaximumAllowedDimension();
            $serviceDimFormula = $serviceObj->getMaximumDimFormula();
            $serviceGrithFormula = $serviceObj->getGirthFormula();
            $grithValue = $serviceObj->getGirth();
            $formulaReturn = 0;
            $grithFormulaReturn = 0;
            if ($serviceObj->getValidationType() == "mail") {
                if (trim($serviceDimFormula) != "" && trim($maximumAllowedDimension) != "" && $maximumAllowedDimension > 0) {
                    $findArr = ['L', 'W', 'H'];
                    $repArr = [$parcelArr['length'], $parcelArr['width'], $parcelArr['height']];
                    $serviceDimFormula = str_replace($findArr, $repArr, $serviceDimFormula);
                    eval('$formulaReturn = ' . $serviceDimFormula . ';');
                    if ($formulaReturn > $maximumAllowedDimension) {
                        $errosArray['STATUS'] = "ERROR";
                        $errosArray['MESSAGE'] = "Invalid dimension";
                        $errosArray['ERROR'] = "Max dimension ( " . $maximumAllowedDimension . " cm ) of service ( " . $serviceObj->getName() . " ) and your consignment is over size ( " . $formulaReturn . " ) cm";
                        return $errosArray;
                    }
                }
            } else {
                if (!empty($serviceObj->getGirth()) && !empty($serviceGrithFormula)) {
                    $findGrithArr = ['height', 'width', 'lenght'];
                    $repGrithArr = [$parcelArr['length'], $parcelArr['width'], $parcelArr['height']];
                    $serviceGrithFormula = str_replace($findGrithArr, $repGrithArr, $serviceGrithFormula);
                    eval('$grithFormulaReturn = ' . $serviceGrithFormula . ';');
                    if ($grithFormulaReturn > $grithValue) {
                        $errosArray['STATUS'] = "ERROR";
                        $errosArray['MESSAGE'] = "Invalid parcel dimension";
                        $errosArray['ERROR'] = "Your parcel dimension [ " . $grithFormulaReturn . " ] is out of gauge. Service ( " . $serviceObj->getName() . " ) allowed grith value is [ " . $grithValue . " ]";
                        return $errosArray;
                    }
                }
                if ($parcelArr['length'] > $serviceObj->getMaxLength() || $parcelArr['width'] > $serviceObj->getMaxWidth() || $parcelArr['height'] > $serviceObj->getMaxHeight()) {
                    $errosArray['STATUS'] = "ERROR";
                    $errosArray['MESSAGE'] = "Invalid dimension allowed";
                    $errosArray['ERROR'] = "Your parcel dimensions are exceeded from maximum value of allowed service dimension";
                    return $errosArray;
                }
            }
        }
        return $errosArray;
    }

    public static function getDropOffLocation($postcode)
    {
        $errosArray['STATUS'] = "SUCCESS";
        $errosArray['MESSAGE'] = "";
        $error_list = [];
        if (empty($postcode)) {
            $error_list[] = "Postcode cannot found";
        }
        // if we found any error then we will return from here
        if (count($error_list) > 0) {
            $errosArray['STATUS'] = "ERROR";
            $errosArray['MESSAGE'] = "Invalid postcode";
            $errosArray['ERROR'] = $error_list;
            return $errosArray;
        }
        $ups = new UPS();
        $response = $ups->getDropOffLocation($postcode);
        $errosArray['RESPONSE'] = $response;
        return $errosArray;
    }

    public static function getDriverParcel($userId)
    {
        $consignmentFilter = new \ConsignmentFilter();
        $returnData = $consignmentFilter->getDriverParcel($userId);
        return $returnData;
    }

    public static function getShipmentStatusStr($code)
    {
        return \Consignment::$database_status_array[$code];
    }

    public static function getTrackingStatusCode()
    {
        $data = [];
        $trackingCodes = \Tracking::$oneworld_status_code;
        $trackingDesc = \Tracking::$oneworld_status_desc;
        foreach ($trackingCodes as $key => $status) {
            $data[$key] = ['status_code' => $key, 'status' => $status, 'description' => $trackingDesc[$key]];
        }
        return $data;
    }

    public static function getShipmentInfo($hawbNumber)
    {
        return Consignment::getShipmentInfo($hawbNumber);
    }

    public function getAccountUsers($user_account_id)
    {
        $usersData = new \UserFilter();
        $usersData->addFilter('user_account_id = ' . $user_account_id);
        $usersData = $usersData->getColumnList('id');
        $usersIds = '';
        foreach ($usersData as $usersDatum) {
            $usersIds .= $usersDatum->getId() . ',';
        }
        return $usersIds = rtrim($usersIds, ',');
    }

    public static function ValidateShipnmentsDataTypes($shipnment)
    {
        $error = [];
        if (trim($shipnment['service_code']) == "") {
            $error[] = "Please enter valid service code";
        }
        if (trim($shipnment['sender_country_iso']) == "") {
            $error[] = "Please enter valid sender coutry iso";
        }
        if (trim($shipnment['sender_contact']) == "") {
            $error[] = "Please enter valid sender contact";
        }
        if (trim($shipnment['sender_address_line_1']) == "") {
            $error[] = "Please enter valid sender address line 1";
        }
        if (trim($shipnment['sender_city']) == "") {
            $error[] = "Please enter valid sender city";
        }
        if (trim($shipnment['sender_postcode']) == "") {
            $error[] = "Please enter valid sender postcode";
        }
        if (trim($shipnment['receiver_country_iso']) == "") {
            $error[] = "Please enter valid reciver country iso";
        }
        if (trim($shipnment['receiver_contact']) == "") {
            $error[] = "Please enter valid receiver contact";
        }
        if (trim($shipnment['receiver_address_line_1']) == "") {
            $error[] = "Please enter valid receiver address line 1";
        }
        if (trim($shipnment['receiver_city']) == "") {
            $error[] = "Please enter valid receiver city";
        }
        if (trim($shipnment['receiver_postcode']) == "") {
            $error[] = "Please enter valid receiver postcode";
        }
        if (trim($shipnment['description']) == "") {
            $error[] = "Please enter valid description";
        }
        $parcelData = $shipnment['parcel'];
        if (count($parcelData)) {
            foreach ($parcelData as $index => $parcel) {
                if (!preg_match("/^\d+(\.\d{1,3})?$/", $parcel['weight']) || $parcel['weight'] == "") {
                    $error[] = "Please enter valid [up to 3 decimal points] parcel " . ($index + 1) . " weight";
                }
                if (!preg_match('/^\d+(\.\d{1,3})?$/', $parcel['length']) || $parcel['length'] == "") {
                    $error[] = "Please enter valid [up to 3 decimal points] parcel " . ($index + 1) . " length";
                }
                if (!preg_match('/^\d+(\.\d{1,3})?$/', $parcel['width']) || $parcel['width'] == "") {
                    $error[] = "Please enter valid [up to 3 decimal points] parcel " . ($index + 1) . " width";
                }
                if (!preg_match('/^\d+(\.\d{1,3})?$/', $parcel['height']) || $parcel['height'] == "") {
                    $error[] = "Please enter valid [up to 3 decimal points] parcel  " . ($index + 1) . " height";
                }
                if (!empty($parcel['itemvalue'])) {
                    if (!preg_match('/^\d+(\.\d{1,3})?$/', $parcel['itemvalue'])) {
                        $error[] = "Please enter valid [up to 3 decimal points] parcel " . ($index + 1) . " value";
                    }
                }
            }
        } else {
            $error[] = "Please enter parcel details";
        }
        return $error;
    }

    public static function validateShipmentParams($params, $extraApiParams = [])
{
    $paramKeys = array_keys($params);
    $defaultParams = [
        'sender_country_iso',
        'service_code',
        'order_reference',
        'sender_contact',
        'sender_email',
        'sender_company',
        'sender_name',
        'sender_address_line_1',
        'sender_address_line_2',
        'sender_address_line_3',
        'sender_city',
        'sender_state',
        'sender_postcode',
        'sender_telephone',
        'receiver_country_iso',
        'receiver_contact',
        'receiver_email',
        'receiver_company',
        'receiver_address_line_1',
        'receiver_address_line_2',
        'receiver_address_line_3',
        'receiver_city',
        'receiver_state',
        'receiver_postcode',
        'receiver_telephone',
        'seller_name',
        'seller_first_name',
        'seller_last_name',
        'seller_email',
        'seller_phone',
        'seller_address_line_1',
        'seller_address_line_2',
        'seller_address_line_3',
        'seller_city',
        'seller_state',
        'seller_postcode',
        'seller_country_iso',
        'seller_gst_tax_id',
        'value',
        'currency',
        'item_type',
        'notes',
        'shipment_type',
        'collection_date',
        'collection_start_time',
        'collection_end_time',
        'description',
        'reference',
        'parcel',
        'tracking_number',
        'label_file',
        'custom_identifier',
        'commercial_invoice',
        'eori_number'            ,
        'vat_number',
        'ioss_number'
    ];
    $apiParamsLength = [
        'sender_country_iso' => 2,
        'service_code' => 10,
        'order_reference' => 40,
        'sender_contact' => 40,
        'sender_email' => 45,
        'sender_company' => 40,
        'sender_name' => 40,
        'sender_address_line_1' => 40,
        'sender_address_line_2' => 40,
        'sender_address_line_3' => 40,
        'sender_city' => 40,
        'sender_state' => 40,
        'sender_postcode' => 10,
        'sender_telephone' => 17,
        'receiver_country_iso' => 2,
        'receiver_contact' => 50,
        'receiver_email' => 45,
        'receiver_company' => 50,
        'receiver_address_line_1' => 30,
        'receiver_address_line_2' => 30,
        'receiver_address_line_3' => 30,
        'receiver_city' => 40,
        'receiver_state' => 40,
        'receiver_postcode' => 10,
        'receiver_telephone' => 17,

        'seller_name' => 255,
        'seller_first_name' => 255,
        'seller_last_name' => 255,
        'seller_email' => 255,
        'seller_phone' => 17,
        'seller_address_line_1' => 255,
        'seller_address_line_2' => 255,
        'seller_address_line_3' => 255,
        'seller_city' => 255,
        'seller_state' => 255,
        'seller_postcode' => 255,
        'seller_country_iso' => 2,
        'seller_gst_tax_id' => 255,

        'value' => 12,
        'currency' => 3,
        'item_type' => 60,
        'custom_identifier' => 255,
        'commercial_invoice' => 'string',
        'eori_number' => '20',
        'vat_number' => '20',
        'ioss_number' => '45'
    ];
    $defaultParams = array_merge($extraApiParams, $defaultParams);
    $invalidParams = [];
    foreach ($paramKeys as $param) {
        if ($param == 'parcel')
            continue;
        if (!in_array($param, $defaultParams)) {
            $invalidParams[] = $param;
        }
    }
    $parcelDefault = ['weight', 'height', 'width', 'length', 'itemvalue','items','tracking_number'];
    $parcelContentsDefault = [
            'item_description',
            'item_url',
            'item_sku', 
            'no_of_items', 
            'item_value', 
            'weight', 
            'tariff_no',    
            'hscode',
            'manufacture_country_iso'
        ];
    /*$parcelContentsDefaultLength = [
            'item_description'=>500,
            'item_url'=>200,
            'item_sku'=>50, 
            'no_of_items'=>10, 
            'item_value'=>10, 
            'weight'=>10, 
            'tariff_no'=>10,    
            'hscode'=>50,
            'manufacture_country_iso'=>2
        ];*/
    if (isset($params['parcel'])) {
        foreach ($params['parcel'] as $index => $parcel) {
            $arrayKeys = [];
            $arrayKeys = array_keys($parcel);
            foreach ($arrayKeys as $arrayKey) {
                if (!in_array($arrayKey, $parcelDefault)) {
                    $invalidParams['parcel'][$index][] = $arrayKey;
                }
                if($arrayKey == 'items'){
                    foreach ($parcel['items'] as $indexContents => $contents) {
                        $arrayKeysContents = array_keys($contents);
                        foreach ($arrayKeysContents as $arrayKeyC) {
                            if (!in_array($arrayKeyC, $parcelContentsDefault)) {
                                $invalidParams['parcel'][$index][] = "items ".$indexContents." - ". $arrayKeyC;
                            }
                            /*else if ($parcelContentsDefaultLength[$arrayKeyC] < strlen($contents[$arrayKeyC])) {
                                $invalidParams['parcel'][$index][] = "items ".$indexContents." - ". $arrayKeyC.", must be less than  ".$parcelContentsDefaultLength[$arrayKeyC]." characters, ";
                            }*/
                        }
                    }
                }
            }
        }
    }
//    foreach ($apiParamsLength as $key => $value) {
//        if(is_integer($value) && strlen($params[$key]) > $value) {
//            $invalidParams[] = $key . ' length is greater than maximum character limit. Maximum character limit is ' . $value;
//        }
//    }
    return $invalidParams;
}

    public static function validateSkuParams($params, $extraApiParams = [])
    {
        $paramKeys = array_keys($params);

        $defaultParams = [
            'sku',
            'name',
            'description',
            'currency',
            'length',
            'width',
            'height',
            'weight',
            'price',
            'currency',
            'notes'

        ];

        $required_params = [
            'sku',
            'name',
            'description',
            'length',
            'width',
            'height',
            'weight',
            
        ];

        $apiParamsLength = [
            45,
            45,
            500,
            3,
            1000,
            1000,
            1000,
            1000,
            3,
            1000

        ];

        $defaultParams = array_merge($extraApiParams, $defaultParams);
        $invalidParams = [];

        foreach ($paramKeys as $param) 
        {
            if ($param == 'sku')
                continue;
            if ($param == 'name')
                continue;
            if ($param == 'description')
                continue;
            if ($param == 'currency')
                continue;
            if ($param == 'length')
                continue;
            if ($param == 'width')
                continue;
            if ($param == 'height')
                continue;
            if ($param == 'weight')
                continue;
            if ($param == 'price')
                continue;
            if ($param == 'currency')
                continue;
            if ($param == 'notes')
                continue;

            if (!in_array($param, $defaultParams)) {
                $invalidParams[] = 'Invalid parameter ' . $param;
            }
        }

        foreach ($required_params as $required_param) {
            if(!in_array($required_param, $paramKeys)) {
                $invalidParams[] = 'Required parameter ' . $required_param . ' is missing';
            }
        }
        /*
        foreach ($apiParamsLength as $key => $value) {
            if(is_integer($value) && strlen($params[$key]) > $value) {
                $invalidParams[] = $key . ' length is greater than maximum character limit. Maximum character limit is ' . $value;
            }
        }
        */

        return $invalidParams;

    }

    public static function validateInboundSkuBagOrderParams($params)
    {
        $paramKeys = array_keys($params);
        $defaultParams = ['packageInfos', 'warehouse'];

        $required_params = [
            'packageInfos',
            'warehouse'
        ];

        $invalidParams = [];

        foreach ($paramKeys as $param) {
            if ($param == 'packageInfos')
                continue;
            if ($param == 'warehouse')
                continue;                
            if (!in_array($param, $defaultParams)) {
                $invalidParams[] = $param;
            }                       
        }

        foreach ($required_params as $required_param) {
            if(!in_array($required_param, $paramKeys)) {
                $invalidParams[] = 'Required parameter ' . $required_param . ' is missing';
            }
        }

        $packageInfoParams = ['bag_number', 'weight', 'length', 'width','height','skuList'];

        $parcelContentsDefault = [];
        if (isset($params['packageInfos'])) {
            foreach ($params['packageInfos'] as $index => $package) {
                $arrayKeys = [];
                $arrayKeys = array_keys($package);
                //print_r($arrayKeys);
                foreach ($arrayKeys as $arrayKey) {
                    if (!in_array($arrayKey, $packageInfoParams)) {
                        $invalidParams['packageInfos'][$index][] = $arrayKey;
                    }
                }
                
                foreach ($packageInfoParams as $packageParam) {
                    if (!in_array($packageParam, $arrayKeys)) {
                        $invalidParams['packageInfos'][$index][] = 'Required parameter ' . $packageParam . ' is missing in bag number ' . $package['bag_number'];
                    }
                }
                
                
                
            }
        }

        //print_r($invalidParams);
        //die;

        return $invalidParams;

    }

    public static function validateJDShipmentParams($params, $extraApiParams = [])
    {
        $paramKeys = array_keys($params);
        $defaultParams = [
            'logisticsProviderCode',
            'waybillCode',
            'orderId',
            'remark',
            'createSiteName',
            'country',
            'sellerCode',
            'packageCount',
            'currencyCode',
            'dimensionsUnits',
            'weightUnits',
            'account',
            'customerCode'
        ];
        $apiParamsLength = [
            'logisticsProviderCode' => 20,
            'waybillCode' => 64,
            'orderId' => 64,
            'remark' => 256,
            'createSiteName' => 100,
            'country' => 10,
            'packageCount' => 3,
            'currencyCode' => 3,
            'dimensionsUnits' => 10,
            'weightUnits' => 10,
            'customerCode' => 14
        ];
        $defaultParams = array_merge($extraApiParams, $defaultParams);
        $invalidParams = [];
        foreach ($paramKeys as $param) {
            if ($param == 'packageInfos')
                continue;
            if ($param == 'sender')
                continue;
            if ($param == 'receiver')
                continue;
            if ($param == 'orderDetail')
                continue;
            if ($param == 'extendFields')
                continue;
            if ($param == 'returnAddress')
                continue;
            if ($param == 'labelSpecification')
                continue;
            if (!in_array($param, $defaultParams)) {
                $invalidParams[] = $param;
            }
        }
        // Parcel info
        $parcelDefault = ['packageId', 'weight', 'volume', 'length', 'width','height','goods'];
        $goodsDefault = ['goodsCode','goodsName','goodsNameEn','goodsBrand','goodsBarcode','hsCode','snCode','sourceCountry','unitCode','goodsCount','worth','currency','chargedStatus','magneticStatus','liquidStatus','cosmeticStatus','dangerousStatus','powderStatus','url','goodsType','goodsWeight','goodsWeightUnits'];
        $parcelContentsDefault = [];
        if (isset($params['packageInfos'])) {
            foreach ($params['packageInfos'] as $index => $parcel) {
                $arrayKeys = [];
                $arrayKeys = array_keys($parcel);
                foreach ($arrayKeys as $arrayKey) {
                    if (!in_array($arrayKey, $parcelDefault)) {
                        $invalidParams['packageInfos'][$index][] = $arrayKey;
                    }
                    if($arrayKey == "goods"){
                        foreach ($parcel['goods'] as $indexContents => $contents) {
                        $arrayKeysContents = array_keys($contents);
                            foreach ($arrayKeysContents as $arrayKeyC) {
                                if (!in_array($arrayKeyC, $goodsDefault)) {
                                $invalidParams['packageInfos']['goods'][$index][] =  $arrayKeyC;
                                }
                            }
                        }
                    }
                }
            }
        }
        // Sender info
        $senderDefault = ['name', 'companyName', 'mobile', 'province', 'city','county','town','address','zipCode','country'];
        if (isset($params['sender'])) {
            foreach ($params['sender'] as $index => $sender) {
                $arrayKeys = [];
                $arrayKeys = array_keys($sender);
                foreach ($arrayKeys as $arrayKey) {
                    if (!in_array($arrayKey, $senderDefault)) {
                        $invalidParams['sender'][$index][] = $arrayKey;
                    }
                }
            }
        }
        // Receiver info
        $receiverDefault = ['name', 'companyName', 'mobile', 'province', 'city','county','town','address','zipCode','email','country'];
        if (isset($params['receiver'])) {
            foreach ($params['receiver'] as $index => $receiver) {
                $arrayKeys = [];
                $arrayKeys = array_keys($receiver);
                foreach ($arrayKeys as $arrayKey) {
                    if (!in_array($arrayKey, $receiverDefault)) {
                        $invalidParams['receiver'][$index][] = $arrayKey;
                    }
                }
            }
        }
        // orderDetail info
        $orderDetailDefault = ['orderCreateTime', 'orderType', 'gotStartTime', 'gotEndTime', 'goodsValue','totalFee','totalServiceFee','codSplitFee','scheduleType','currency','homeDeliveryService','signatureService','serviceType','serviceCode'];
        if (isset($params['orderDetail'])) {
            foreach ($params['orderDetail'] as $index => $orderDetail) {
                $arrayKeys = [];
                $arrayKeys = array_keys($orderDetail);
                foreach ($arrayKeys as $arrayKey) {
                    if (!in_array($arrayKey, $orderDetailDefault)) {
                        $invalidParams['orderDetail'][$index][] = $arrayKey;
                    }
                }
            }
        }
        // extendFields info
        $extendFieldsDefault = ['key', 'value'];
        if (isset($params['extendFields'])) {
            foreach ($params['extendFields'] as $index => $extendFields) {
                $arrayKeys = [];
                $arrayKeys = array_keys($extendFields);
                foreach ($arrayKeys as $arrayKey) {
                    if (!in_array($arrayKey, $extendFieldsDefault)) {
                        $invalidParams['extendFields'][$index][] = $arrayKey;
                    }
                }
            }
        }
        // returnAddress info
        $returnAddressDefault = ['name', 'mobile', 'province', 'city', 'county', 'town', 'address', 'zipCode', 'country'];
        if (isset($params['returnAddress'])) {
            foreach ($params['returnAddress'] as $index => $returnAddress) {
                $arrayKeys = [];
                $arrayKeys = array_keys($returnAddress);
                foreach ($arrayKeys as $arrayKey) {
                    if (!in_array($arrayKey, $returnAddressDefault)) {
                        $invalidParams['returnAddress'][$index][] = $arrayKey;
                    }
                }
            }
        }
        // labelSpecification info
        $labelSpecificationDefault = ['imageType', 'labelSize'];
        if (isset($params['labelSpecification'])) {
            foreach ($params['labelSpecification'] as $index => $labelSpecification) {
                $arrayKeys = [];
                $arrayKeys = array_keys($labelSpecification);
                foreach ($arrayKeys as $arrayKey) {
                    if (!in_array($arrayKey, $labelSpecificationDefault)) {
                        $invalidParams['labelSpecification'][$index][] = $arrayKey;
                    }
                }
            }
        }

        foreach ($apiParamsLength as $key => $value) {
            if(is_integer($value) && strlen($params[$key]) > $value) {
                $invalidParams[] = $key . ' length is greater than maximum character limit. Maximum character limit is ' . $value;
            }
        }
        return $invalidParams;
    }
    public static function validateGetShipmentParams($params)
    {
        $paramKeys = array_keys($params);
        $defaultParams = [
            'order_reference' => 40,
        ];
        $required_params = [
            'order_reference',
        ];
        $invalidParams = [];
        foreach ($required_params as $required_param) {
            if(!in_array($required_param, $paramKeys)) {
                $invalidParams[] = 'Required parameter ' . $required_param . ' is missing';
            }
        }
        foreach ($paramKeys as $param) {
            if(!in_array($param, array_keys($defaultParams))) {
                $invalidParams[] = $param. ' is invalid parameter ';
            }
        }
        foreach ($defaultParams as $key => $value) {
            if(is_integer($value) && strlen($params[$key]) > $value) {
                $invalidParams[] = $key . ' length is greater than maximum character limit. Maximum character limit is ' . $value;
            }
        }
        
        
        
        return $invalidParams;
    }
    public static function validateGetShipmentsParams($params)
    {

        $required_params = [
//            'order_reference'
        ];
        $paramKeys = array_keys($params);
        $defaultParams = [
            'order_reference',
            'tracking_number',
            'page',
            'date_from',
            'date_to',
            'city',
            'country',
            'consignment_status_code',
            'per_page'
        ];
        $invalidParams = [];
        foreach ($required_params as $required_param) {
            if (!in_array($required_param, $paramKeys)) {
                $invalidParams[] = 'Required parameter ' . $required_param . ' is missing.';
            }
        }
        foreach ($params as $key => $value) {
            if(!in_array($key, $defaultParams)) {
                $invalidParams[] = $value . ' is invalid parameter.';
            }
        }
        if(is_array($params['order_reference']) && count($params['order_reference']) > 40) {
            $invalidParams[] = 'order_reference allowed array length is 40';
        }

        if(!empty($params["order_reference"])){
            foreach ($params["order_reference"] as $key => $value) {
                if(is_array($value)) {
                    $invalidParams[] = $key . ' should be a valid order reference number string.';
                }
            }
        }
        return $invalidParams;
    }
    public static function validateGetMarketplaceOrdersParams($params)
    {
        $paramKeys = array_keys($params);
        $defaultParams = [
            'marketplace_key' => 70,
			'tracking_number' => 70,
			'receiver_country_iso' => 2,
			'receiver_city' => 200,
			'order_date_from' => 10,
			'order_date_to' => 10,
			'order_number' =>  200,
			'receiver_name' => 150,
			'shipped_date_from' => 10,
			'shipped_date_to' => 10,
			'order_status' => 10,
            'page' => 10,
            'per_page' => 3,

        ];
        $defaultParamsKeys = array_keys($defaultParams);
        $requiredParams = [
            //'marketplace_key'
        ];
        $invalidParams = [];
        foreach ($requiredParams as $requiredParam) {
            if(!in_array($requiredParam, $paramKeys)) {
                $invalidParams[] = 'Required parameter ' . $requiredParam . ' is missing';
            }
        }
        foreach ($paramKeys as $param) {
            if(!in_array($param, $defaultParamsKeys)) {
                $invalidParams[] = $param. ' is invalid parameter ';
            }
        }
        foreach ($defaultParams as $key => $value) {
            if(is_integer($value) && strlen($params[$key]) > $value) {
                $invalidParams[] = $key . ' length is greater than maximum character limit. Maximum character limit is ' . $value;
            }
        }
        return $invalidParams;
    }
    public static function validateScanParcelParams($params)
    {
        $paramKeys = array_keys($params);
        $defaultParams = [
            'tracking_numbers' => 45,
            'tracking_number' => 32,
            'longitude' => 255,
            'latitude' => 255,
            'scan_time' => 'time',
            'signature' => 255,
            'signature_image' => 'string',
            'parcel_image' => 255,
            'status_code' => 4,
            'track_point' => 255,
            'ip_address' => 'string',
        ];
        $required_params = [
            'ip_address',
            'tracking_number',
            'scan_time',
            'status_code'
        ];
        $invalidParams = [];
        foreach ($required_params as $required_param) {
            if(!in_array($required_param, $paramKeys)) {
                $invalidParams[] = 'Required parameter ' . $required_param . ' is missing';
            }
        }
        foreach ($paramKeys as $param) {
            if(!in_array($param, array_keys($defaultParams))) {
                $invalidParams[] = $param. '  is invalid parameter ';
            }
        }
        foreach ($defaultParams as $key => $value) {
            if(is_integer($value) && strlen($params[$key]) > $value) {
                $invalidParams[] = $key . ' length is greater than maximum character limit. Maximum character limit is ' . $value;
            }
        }
        if(isset($params['scan_time'])) {
            if (!strtotime($params['scan_time'])) {
                $invalidParams[] = 'Invalid scan_time format. Please enter valid format [YYYY-MM-DD HH:MM:SS].';
            }
        }
        return $invalidParams;
    }
    public static function validateBagParams($params)
    {
        $paramKeys = array_keys($params);
        $defaultParams = [
            'actual_weight' => 5,
            'bag_length' => 5,
            'bag_width' => 5,
            'bag_height' => 5,
            'origin_country_iso' => 2,
            'destination_country_iso' => 2,
            'service_code' => 10,
            'mawb_number' => 12,
            'extras' => 255,
            'label_file' => 255,
            'bag_number' => 255,
            'parcel' => 45
        ];
        $required_params = [
            'origin_country_iso',
            'destination_country_iso',
            'service_code',
            'parcel'
        ];
        $invalidParams = [];
        foreach ($required_params as $required_param) {
            if(!in_array($required_param, $paramKeys)) {
                $invalidParams[] = 'Required parameter ' . $required_param . ' is missing';
            }
        }
        foreach ($paramKeys as $param) {
            if(!in_array($param, array_keys($defaultParams))) {
                $invalidParams[] = $param. ' is invalid parameter ';
            }
        }
        foreach ($defaultParams as $key => $value) {
            if(is_integer($value) && strlen($params[$key]) > $value) {
                $invalidParams[] = $key . ' length is greater than maximum character limit. Maximum character limit is ' . $value;
            }
        }
        return $invalidParams;
    }
    public static function validateLabelParams($params)
    {
        $paramKeys = array_keys($params);
        $defaultParams = [
            'order_reference' => 40,
            'label_type' => 255,
            'label_size' => 255
        ];
        $defaultParamsKeys = array_keys($defaultParams);
        $requiredParams = [
            'order_reference'
        ];
        $invalidParams = [];
        foreach ($requiredParams as $requiredParam) {
            if(!in_array($requiredParam, $paramKeys)) {
                $invalidParams[] = 'Required parameter ' . $requiredParam . ' is missing';
            }
        }
        foreach ($paramKeys as $param) {
            if(!in_array($param, $defaultParamsKeys)) {
                $invalidParams[] = $param. ' is invalid parameter ';
            }
        }
        foreach ($defaultParams as $key => $value) {
            if(is_integer($value) && strlen($params[$key]) > $value) {
                $invalidParams[] = $key . ' length is greater than maximum character limit. Maximum character limit is ' . $value;
            }
        }
        return $invalidParams;
    }
    public static function validateVoidBagParcelParams($params)
    {
        $paramKeys = array_keys($params);
        $defaultParams = [
            'bag_number' => 45,
            'tracking_numbers' => 45
        ];
        $defaultParamsKeys = array_keys($defaultParams);
        $requiredParams = [
            'bag_number',
            'tracking_numbers'
        ];
        $invalidParams = [];
        foreach ($requiredParams as $requiredParam) {
            if(!in_array($requiredParam, $paramKeys)) {
                $invalidParams[] = 'Required parameter ' . $requiredParam . ' is missing';
            }
        }
        foreach ($paramKeys as $param) {
            if(!in_array($param, $defaultParamsKeys)) {
                $invalidParams[] = $param. ' is invalid parameter ';
            }
        }
        foreach ($defaultParams as $key => $value) {
            if(is_integer($value) && strlen($params[$key]) > $value) {
                $invalidParams[] = $key . ' length is greater than maximum character limit. Maximum character limit is ' . $value;
            }
        }
        return $invalidParams;
    }
    
    
    public static function validateRestoreLabelParams($params)
    {
        $paramKeys = array_keys($params);
        $invalidParams = [];
        $defaultParams = [
            'order_reference' => 32,
        ];
        $required_params = [
            'order_reference'
        ];
        foreach ($paramKeys as $param) {
            if(!in_array($param, array_keys($defaultParams))) {
                $invalidParams[] = $param. ' is invalid parameter ';
            }
        }
        foreach ($required_params as $required_param) {
            if(!in_array($required_param, $paramKeys)) {
                $invalidParams[] = 'Required parameter ' . $required_param . ' is missing';
            }
        }
        foreach ($defaultParams as $key => $value) {
            if(is_integer($value) && strlen($params[$key]) > $value) {
                $invalidParams[] = $key . ' length is greater than maximum character limit. Maximum character limit is ' . $value;
            }
        }
        if(!empty($params["order_reference"])){
            foreach ($params["order_reference"] as $key => $value) {
                if(is_array($value)) {
                    $invalidParams[] = $key . ' should be a order reference string.';
                }
            }
        }
        return $invalidParams;
    }
    public static function validateMultiTrackingParams($params)
    {
        $paramKeys = array_keys($params);
        $invalidParams = [];
        $defaultParams = [
            'tracking_numbers' => 32,
        ];
        $required_params = [
            'tracking_numbers'
        ];
        foreach ($paramKeys as $param) {
            if(!in_array($param, array_keys($defaultParams))) {
                $invalidParams[] = $param. ' is invalid parameter ';
            }
        }
        foreach ($required_params as $required_param) {
            if(!in_array($required_param, $paramKeys)) {
                $invalidParams[] = 'Required parameter ' . $required_param . ' is missing';
            }
        }
        foreach ($defaultParams as $key => $value) {
            if(is_integer($value) && strlen($params[$key]) > $value) {
                $invalidParams[] = $key . ' length is greater than maximum character limit. Maximum character limit is ' . $value;
            }
        }
        if(!empty($params["tracking_numbers"])){
            foreach ($params["tracking_numbers"] as $key => $value) {
                if(is_array($value)) {
                    $invalidParams[] = $key . ' should be a tracking number string.';
                }
            }
        }
        return $invalidParams;
    }
    public static function validateCreateUserParams($params)
    {
        $paramKeys = array_keys($params);
        $defaultParams = [
            'account_number' => 30,
            'account_email' => 500,
            'company_name' => 100,
            'contact_name' => 100,
            'contact_number' => 20,
            'country_iso' => 3,
            'return_address' => 255,
            'company_reg_number' => 20,
            'company_reg_address' => 255,
            'company_reg_postcode' => 10,
            'company_reg_country_iso' => 3,
            'bank_account_title' => 45,
            'bank_sort_code' => 10,
            'bank_account_number' => 20,
            'bank_branch_address' => 255,
            'trade_ref_name' => 50,
            'trade_ref_address' => 255,
            'trade_ref_email' => 255,
            'trade_ref_phone_no' => 50,
            'email_signature' => 255,
            'billing_currency' => 3,
            'invoice_period' => 50,
            'vat_number' => 40,
            'is_active' => 1,
            'credit_limit' => 14
        ];
        $defaultParamsKeys = array_keys($defaultParams);
        $requiredParams = [
            'account_number',
            'account_email',
            'company_name',
            'contact_name',
            'contact_number',
            'country_iso',
            'return_address',
            'company_reg_number',
            'company_reg_address',
            'company_reg_postcode',
            'company_reg_country_iso',
            'bank_account_title',
            'bank_sort_code',
            'bank_account_number',
            'bank_branch_address',
            'trade_ref_name',
            'trade_ref_address',
            'trade_ref_email',
            'trade_ref_phone_no',
            'email_signature',
            'billing_currency',
            'invoice_period',
            'vat_number',
            'is_active',
            'credit_limit'
        ];
        $invalidParams = [];
        foreach ($requiredParams as $requiredParam) {
            if(!in_array($requiredParam, $paramKeys)) {
                $invalidParams[] = 'Required parameter ' . $requiredParam . ' is missing';
            }
        }
        foreach ($paramKeys as $param) {
            if(!in_array($param, $defaultParamsKeys)) {
                $invalidParams[] = $param. ' is invalid parameter ';
            }
        }
        foreach ($defaultParams as $key => $value) {
            if(is_integer($value) && strlen($params[$key]) > $value) {
                $invalidParams[] = $key . ' length is greater than maximum character limit. Maximum character limit is ' . $value;
            }
        }
        return $invalidParams;
    }
    public static function validateCurrencyConverterParams($params)
    {
        $paramKeys = array_keys($params);
        $defaultParams = [
            'from_currency_code' => 3,
            'to_currency_code' => 3,
            'amount' => 14
        ];
        $defaultParamsKeys = array_keys($defaultParams);
        $requiredParams = [
            'from_currency_code',
            'to_currency_code',
            'amount'
        ];
        $invalidParams = [];
        foreach ($requiredParams as $requiredParam) {
            if(!in_array($requiredParam, $paramKeys)) {
                $invalidParams[] = 'Required parameter ' . $requiredParam . ' is missing';
            }
        }
        foreach ($paramKeys as $param) {
            if(!in_array($param, $defaultParamsKeys)) {
                $invalidParams[] = $param. ' is invalid parameter ';
            }
        }
        foreach ($defaultParams as $key => $value) {
            if(is_integer($value) && strlen($params[$key]) > $value) {
                $invalidParams[] = $key . ' length is greater than maximum character limit. Maximum character limit is ' . $value;
            }
        }
        return $invalidParams;
    }
    public static function validateQuotesParams($params)
    {
        $invalidParams = [];
        $apiDefaultParams = [
            'origin_country_iso' => 2,
            'delivery_country_iso' => 2,
            'origin_city' => 25,
            'delivery_city' => 25,
            'origin_postcode' => 10,
            'delivery_postcode' => 10,
        ];
        foreach ($apiDefaultParams as $key => $value) {
            if(is_integer($value) && strlen($params[$key]) > $value) {
                $invalidParams[] = $key . ' length is greater than maximum character limit. Maximum character limit is ' . $value;
            }
        }
        return $invalidParams;
    }

    public static function AddApiLog($type, $requestData, $responseData, $userId,$consignemtId=NULL)
    {
        if(!is_array($responseData))
            $responseData = json_decode($responseData,true);

        if(isset($responseData['data']) && isset($responseData['data']['label_bin_str'])){
            $responseData['data']['label_bin_str'] = substr($responseData['data']['label_bin_str'],0,50)."...";
        }
        $responseData = json_encode($responseData,JSON_PARTIAL_OUTPUT_ON_ERROR);
        $apiData = new \apiData();
        $apiData->setType($type);
        $apiData->setApiRequest(json_encode($requestData,JSON_PARTIAL_OUTPUT_ON_ERROR));
        $apiData->setApiResponse($responseData);
        $apiData->setAddedBy($userId);
        if(!empty($consignemtId) && is_numeric($consignemtId))
            $apiData->setConsignmentId($consignemtId);
        $apiData->setDateCreated(time());
        $apiData->save();
    }

    public static function checkDriverAssign($trackingNo, $userId)
    {
        $returnArray = [];
        $count = 0;
        if (empty($trackingNo)) {
            $returnArray['status'] = "error";
            $returnArray['message'] = "tracking number is missing";
            $returnArray['driver_assigned'] = 'no';
            $returnArray['driver_name'] = '';
            $returnArray['date_assign'] = '';
            return $returnArray;
            exit;
        }
        $parcelFilter = new \ParcelFilter();
        $parcelFilter->addFieldFilter('tracking_number', $trackingNo);
        $parcelObjTmp = $parcelFilter->getColumnList('id,owe_status_code');
        if (empty($parcelObjTmp)) {
            $returnArray['status'] = "error";
            $returnArray['message'] = "Invalid tracking number";
            $returnArray['driver_assigned'] = 'no';
            $returnArray['driver_name'] = '';
            $returnArray['date_assign'] = '';
            return $returnArray;
            exit;
        } else if ($parcelObjTmp[0]->getOweStatusCode() == 22) {
            $returnArray['status'] = "error";
            $returnArray['message'] = "Parcel is already dilivered";
            $returnArray['driver_assigned'] = 'no';
            $returnArray['driver_name'] = '';
            $returnArray['date_assign'] = '';
            return $returnArray;
            exit;
        }
        $parcelObj = $parcelObjTmp[0];
        $assignVehicleFilter = new \AssignVehicleFilter();
        //$assignVehicleFilter->join('`parcel`', "p.`id` = vpm.`parcel_id` AND p.`tracking_number` = '".\DbAccess3::escape($trackingNo)."'");
        $assignVehicleFilter->join('`user` u', "u.`id` = vpm.`driver_id`");
        $assignVehicleFilter->where(['vpm.`parcel_id`' => $parcelObj->getId()]);
        //$assignVehicleFilter->where(["p.`owe_status_code`" => '1'], '!=');
        $assignVehicleFilter->orderBy('vpm.id', "DESC");
        $assignVehicleObjs = $assignVehicleFilter->getList(['vpm.`driver_id`', "CONCAT(u.`first_name`,' ',u.`last_name`) AS added_by", 'vpm.`date_added`', 'vpm.`is_active`']);
        $isAssigned = 'no';
        $assignedTo = '';
        $assignedDate = '';
        $isAssignedToOther = 'no';
        if (!empty($assignVehicleObjs)) {
            foreach ($assignVehicleObjs as $assignVehicleObj) {
                if ($assignVehicleObj->getIsActive()) {
                    $isAssignedToOther = 'other';
                    $isAssigned = 'other';
                    if ($assignVehicleObj->getDriverId() == $userId) {
                        $isAssigned = 'yes';
                    }
                    $assignedTo = $assignVehicleObj->getAddedBy();
                    $assignedDate = $assignedDate = date('Y-m-d h:i:s', $assignVehicleObj->getDateAdded());
                }
            }
            $returnArray['status'] = "success";
            $returnArray['message'] = ($isAssigned == 'no' && $isAssignedToOther == 'no') ? "Parcel is not assigned to dirver" : "Parcel is assigned to dirver";
            $returnArray['driver_assigned'] = $isAssigned;
            $returnArray['driver_name'] = $assignedTo;
            $returnArray['date_assign'] = $assignedDate;
        } else {
            $returnArray['status'] = "success";
            $returnArray['message'] = "Parcel is not assigned to dirver";
            $returnArray['driver_assigned'] = 'no';
            $returnArray['driver_name'] = $assignedTo;
            $returnArray['date_assign'] = $assignedDate;
        }
        return $returnArray;
        /*$consignmentFilter = new \ConsignmentFilter();
        $consignmentFilter->addJoin("    parcel p", "p.consignment_id = c.id");
        $consignmentFilter->addFilter('       (c.hawb ='. $orderReference . ' OR c.awb = ' . $orderReference . ')');
        $returnData = $consignmentFilter->getListNew("p.id as parcel_id");
        $parcelIds = [];
        if(count($returnData)) {
            foreach($returnData as $obj) {
                $parcelIds[] = $obj->getParcelId();
            }
        } else {
            $returnArray['status'] = "error";
            $returnArray['message'] = "Consignment not found";
            return $returnArray;
        }
        if(count($parcelIds)) {
            $userFilter = new \UserFilter();
            $userFilter->addFieldFilter("       u.user_name", $username);
            $userObj = $userFilter->getList();
            if(count($userObj)) {
                $userObj = $userObj[0];
            }
            if(!empty($userObj)) {
                $userId = $userObj->getId();
                $vehicleParcelMappingFilter = new \AssignVehicleFilter();
                $vehicleParcelMappingFilter->where(['driver_id' => $userId]);
                $vehicleParcelMappingFilter->where(['is_active' => 1]);
                $vehicleParcelMappingFilter->whereIn('parcel_id',$parcelIds);
                $count = $vehicleParcelMappingFilter->getCount(false);
            } else {
                $returnArray['status'] = "error";
                $returnArray['message'] = "Driver Not found";
                return $returnArray;
            }
        } else {
            $returnArray['status'] = "error";
            $returnArray['message'] = "Parcel not found";
            return $returnArray;
        }
        if($count > 0) {
            $returnArray['status'] = "success";
            $returnArray['message'] = "Parcel assigned to dirver";
            $returnArray['driver_assigned'] = true;
            return $returnArray;
        } else {
            $returnArray['status'] = "success";
            $returnArray['message'] = "Parcel is not assigned to dirver";
            $returnArray['driver_assigned'] = false;
            return $returnArray;
        }*/
    }

    public static function restoreLabel($validateScannigData)
    {
        $response = [];
        if (count($validateScannigData['order_reference']) > 0) {
            $response = \Consignment::RestoreShipment($validateScannigData['order_reference']);
        } else {
            $response['STATUS'] = "ERROR";
            $response['MESSAGE'] = "Invalid order reference";
            $response['ERROR'][] = "Invalid order reference numbers";
        }
        return $response;
    }
    public static function validateImportBoxParams($params)
    {
        $paramKeys = array_keys($params);
		$defaultParams = [
			'bag_number' => 45,
			'origin_country_iso' => 2,
			'destination_country_iso' => 2,
			'origin_warehouse_code' => 3,
			'destination_warehouse_code' => 3,
			'parcel' => 45
		];
		$required_params = [
			'bag_number',
			'origin_country_iso',
			'destination_country_iso',
			'destination_warehouse_code',
			'parcel'
		];
        $invalidParams = [];
        foreach ($required_params as $required_param) {
            if(!in_array($required_param, $paramKeys)) {
                $invalidParams[] = 'Required parameter ' . $required_param . ' is missing';
            }
        }
        foreach ($paramKeys as $param) {
            if(!in_array($param, array_keys($defaultParams))) {
                $invalidParams[] = $param. ' is invalid parameter ';
            }
        }
        foreach ($defaultParams as $key => $value) {
            if(is_integer($value) && strlen($params[$key]) > $value) {
                $invalidParams[] = $key . ' length is greater than maximum character limit. Maximum character limit is ' . $value;
            }
        }
        return $invalidParams;
    }
    public static function validateConStatusUpdateParams($params)
    {
        $paramKeys = array_keys($params);
        $defaultParams = [
            'order_reference' => 40,
            'status' => 255,
            'description' => 255
        ];
        $required_params = [
            'order_reference',
            'status',
            'description'
        ];
        $invalidParams = [];
        foreach ($required_params as $required_param) {
            if(!in_array($required_param, $paramKeys)) {
                $invalidParams[] = 'Required parameter ' . $required_param . ' is missing';
            }
        }
        foreach ($paramKeys as $param) {
            if(!in_array($param, array_keys($defaultParams))) {
                $invalidParams[] = $param. ' is invalid parameter ';
            }
        }
        foreach ($defaultParams as $key => $value) {
            if(is_integer($value) && strlen($params[$key]) > $value) {
                $invalidParams[] = $key . ' length is greater than maximum character limit. Maximum character limit is ' . $value;
            }
        }
        return $invalidParams;
    }
    public static function importBox($param,$userObj){
        $bagtotalWeight = 0;
        $bagId =  0;
        $bagNumber =  $param['bag_number'];
        $originCountryIos =  $param['origin_country_iso'];
        $destinationCountryIos =  $param['destination_country_iso'];
        $originWarehouseCode =  $param['origin_warehouse_code'];
        $destinationWarehouseCode =  $param['destination_warehouse_code'];
        // Check if country iso code exist
        $originCountryId = self::checkCountryId($originCountryIos);
        if($originCountryId < 0){
            $response['STATUS'] = "ERROR";
            $response['MESSAGE'] = "Origin country Iso ".$originCountryIos." not found";
            $response['ERROR'][] = "Origin country Iso ".$originCountryIos." not found";
        }
        $destinationCountryId = self::checkCountryId($destinationCountryIos);
        if($destinationCountryId < 0){
            $response['STATUS'] = "ERROR";
            $response['MESSAGE'] = "Destination country Iso ".$destinationCountryIos." not found";
            $response['ERROR'][] = "Destination country Iso ".$destinationCountryIos." not found";
        }
        // Check if warehouse code exist
        $originWarehouseId = self::checkWarehouseId($originWarehouseCode);
        if($originWarehouseId < 0){
            $response['STATUS'] = "ERROR";
            $response['MESSAGE'] = "Origin warehouse code ".$originWarehouseCode." not found";
            $response['ERROR'][] = "Origin warehouse code ".$originWarehouseCode." not found";
        }
        $destinationWarehouse = self::checkWarehouseId($destinationWarehouseCode);
        if($destinationWarehouse < 0){
            $response['STATUS'] = "ERROR";
            $response['MESSAGE'] = "Destination warehouse code ".$destinationWarehouseCode." not found";
            $response['ERROR'][] = "Destination warehouse code ".$destinationWarehouseCode." not found";
        }
        if(isset($response['STATUS']) && $response['STATUS'] == "ERROR"){
            return $response;
        }
        $bagWeightLimit = \CountryFilter::getCountryBagWeightLimit($destinationCountryId);
        $parcel =  $param['parcel'];
        // Check if bag is already exists
        $baggingFilter = new \BaggingFilter();
        $baggingFilter->addFieldFilter("     bagnumber",$bagNumber);
        $baggingFilterObj = $baggingFilter->getColumnList("bagnumber,bag_source_country_id,bag_destination_country_id,is_closed");
        if(count($baggingFilterObj) > 0){
            /*
             * Check if bag is already opened
             * Check bag having same country ids
             * bag weight limit through countryfilter function getCountryBagWeightLimit
             */
            $bagId = $baggingFilterObj[0]->getId();
            if($baggingFilterObj[0]->getIsClosed()){
                $response['STATUS'] = "ERROR";
                $response['MESSAGE'] = "Please fix below error";
                $response['ERROR'][] = "Bag already exists and closed";
                return $response;
            }
            if (($originCountryId != $baggingFilterObj[0]->getBagSourceCountryId()) || ($destinationCountryId != $baggingFilterObj[0]->getBagDestinationCountryId())){
                $response['STATUS'] = "ERROR";
                $response['MESSAGE'] = "Please fix below error";
                $response['ERROR'][] = "Bag already exists and source/destination country is different";
                return $response;
            }
        }
        $serviceIds = [];
        $parcelWeight = [];
        $parcelTotalWeight = $bagtotalWeight;
        $serviceData = self::getParcelServices($parcel);
        if(count($serviceData) > 0){
            foreach ($serviceData as $serviceItem) {
                $serviceIds[] = $serviceItem->getId();
                $parcelWeight[$serviceItem->getTrackingNumber()] = $serviceItem->getWeight();
                $parcelTotalWeight += $serviceItem->getWeight();
            }
        }
        if($parcelTotalWeight > $bagWeightLimit){
            $response['STATUS'] = "ERROR";
            $response['MESSAGE'] = "Parcel total weight ".$parcelTotalWeight." is over limit ".$bagWeightLimit." kg ";
            $response['ERROR'][] = "Parcel total weight ".$parcelTotalWeight." is over limit ".$bagWeightLimit." kg ";
            return $response;
        }
        // Get parcel ids from tracking number
        $parcelFilter = new \ParcelFilter();
        $parcelFilter->addFilterIn("tracking_number",$parcel);
        $parcelFilter->addFieldNotFilter("parcel_status_code",Consignment::STATUS_RECYCLED);
        $parcelFilter->addConsignmentTableJoin("LEFT");
        $parcelData = $parcelFilter->getColumnList("p.id,p.tracking_number,join_con.service_id AS do_tracking_number");
        $parcelIds = [];
        $serviceIds = [];
        $parcelDataArr = [];
        $parcelTrackingIdsArr = [];
        foreach ($parcelData as $parcelArr) {
            $parcelIds[] = $parcelArr->getId();
            $serviceIds[] = $parcelArr->getDoTrackingNumber();
            $parcelDataArr[] = $parcelArr->getTrackingNumber();
            $parcelTrackingIdsArr[$parcelArr->getId()] = $parcelArr->getTrackingNumber();
        }
        $serviceIds = array_unique($serviceIds);
        $parcelArrDiff = array_diff($parcel,$parcelDataArr);
        if(count($parcelArrDiff) > 0){
            foreach ($parcelArrDiff as $parcelArrDiffItem) {
                $response['ERROR'][] = "Parcel tracking number ".$parcelArrDiffItem." not found ";
            }
            $response['STATUS'] = "ERROR";
            $response['MESSAGE'] = "Parcel tracking not found";
            return $response;
        }
        // Check if these parcels belong to any open bag at same wharehouse
        $parcelBaggingMappingFilter = new \ParcelBaggingMappingFilter();
        $parcelBaggingMappingFilter->addFilterIn("     parcel_id",$parcelIds);
        $parcelBaggingMappingData = $parcelBaggingMappingFilter->getColumnList("parcel_id");
        if(count($parcelBaggingMappingData)){
            $response['STATUS'] = "ERROR";
            $response['MESSAGE'] = "Parcel tracking not found";
            foreach ($parcelBaggingMappingData as $parcelBaggingMappingDatum) {
                $response['ERROR'][] = "Parcel tracking number ".$parcelTrackingIdsArr[$parcelBaggingMappingDatum->getParcelId()]." already exist in another bag";
            }
            return $response;
        }
        if($bagId == 0) {
            $bagging = new \Bagging();
            $bagging->setBagnumber($bagNumber);
            $bagging->setDateCreated(time());
            $bagging->setUserId($userObj->getId());
            $bagging->setBagSourceCountryId($originCountryId);
            $bagging->setBagSourceWarehouseId($originWarehouseId);
            $bagging->setBagDestinationCountryId($destinationCountryId);
            $bagging->setBagDestinationWarehouseId($destinationWarehouse);
            $bagging->save();
            $bagId = $bagging->getId();
        }
        // Save Data in Parcel Mapping Table
        foreach ($parcelIds as $parcelIdStr) {
            $parcelBaggingMapping = new \ParcelBaggingMapping();
            $parcelBaggingMapping->setParcelId($parcelIdStr);
            $parcelBaggingMapping->setBagId($bagId);
            $parcelBaggingMapping->setAddedBy($userObj->getId());
            $parcelBaggingMapping->setAddedDate(time());
            $parcelBaggingMapping->save();
        }
        // Save Data in Bagging Service Mapping Table
        foreach ($serviceIds as $serviceId) {
            $baggingServiceMapping = new \BaggingServicesMapping();
            $baggingServiceMapping->setBagId($bagId);
            $baggingServiceMapping->setServiceId($serviceId);
            $baggingServiceMapping->save();
        }
        $response['STATUS'] = "SUCCESS";
        $response['MESSAGE'] = "Bag created successfully";
        $response['BAG_NUMBER'] = $bagNumber;
        return $response;

    }
    function checkCountryId($iso){
        $return = "";
        if(!empty($iso)){
            $countryFilter = new \CountryFilter();
            $countryFilter->addFieldFilter("     iso",$iso);
            $countryObj = $countryFilter->getColumnList("id");
            if(count($countryObj) > 0)
                $return = $countryObj[0]->getId();
        }
        return $return;
    }
    public function getParcelServices($parcelArr){
        $return = "";
        if(!empty($parcelArr) && count($parcelArr) > 0){
            $parcelFilter = new \ParcelFilter();
            $return = $parcelFilter->getParcelSerivces($parcelArr);
        }
        return $return;
    }
 public static function consignmentsStatusUpdate($consignmentData) {
        $response = [];
        if (isset($consignmentData['order_reference']) && $consignmentData['order_reference'] != "") {
            if(isset($consignmentData['status']) && $consignmentData['status'] != "") {
                if(isset($consignmentData['description']) && $consignmentData['description'] != "") {
                    $orderReference = $consignmentData['order_reference'];
                    $consignmentFilter = new \ConsignmentFilter();
                    $consignmentFilter->addFieldEqualFilter("hawb", "=", $orderReference);
                    $consignmentObj = $consignmentFilter->getListNew("id,shipment_status");
                    $consignmentIds = [];
                    if(count($consignmentObj)) {
                        $consignmentIds[] = $consignmentObj[0]->getId();
                        $statusCont = "STATUS_".strtoupper(str_replace(" ","_",$consignmentData['status']));
                        $refl = new \ReflectionClass('Consignment');
                        $allConstant = $refl->getConstants();
                        if(isset($allConstant[$statusCont])) {
                            $status = $allConstant[$statusCont];
                            $description  = $consignmentData['description'];
                            \Consignment::consignmentStatusUpdate($consignmentIds,$status,$description);
                            $response['STATUS'] = "SUCCESS";
                            $response['MESSAGE'] = "Shipment status is update successfully";
                        } else {
                            $response['STATUS'] = "ERROR";
                            $response['MESSAGE'] = "Invalid status";
                            $response['ERROR'][] = "Shipment status is invalid";
                        }
                    } else {
                        $response['STATUS'] = "ERROR";
                        $response['MESSAGE'] = "Invalid shipment";
                        $response['ERROR'][] = "Shipment not found";
                    }
                } else {
                    $response['STATUS'] = "ERROR";
                    $response['MESSAGE'] = "Invalid description";
                    $response['ERROR'][] = "Please provide description of change status";
                }
            } else {
                $response['STATUS'] = "ERROR";
                $response['MESSAGE'] = "Invalid status";
                $response['ERROR'][] = "Please provide consignment status";
            }
        } else {
            $response['STATUS'] = "ERROR";
            $response['MESSAGE'] = "Invalid order reference";
            $response['ERROR'][] = "Please provide order reference numbers";
        }
        return $response;
    }
function checkWarehouseId($code){
        $return = "";
        if(!empty($code)){
            $warehouseFilter = new \WarehouseFilter();
            $warehouseFilter->addFieldFilter("     warehouse_code",$code);
            $warehouseObj = $warehouseFilter->getColumnList("id");
            if(count($warehouseObj) > 0)
                $return = $warehouseObj[0]->getId();
        }

        return $return;
    }
    public static function validateAssignMawbParams($params)
    {
        $paramKeys = array_keys($params);
		$defaultParams = [
			'origin_country_iso' => 2,
			'destination_country_iso' => 2,
			'origin_warehouse_code' => 4,
			'destination_warehouse_code' => 4,
			'mawb' => 20,
			'bags_numbers' => 20
		];
		$required_params = [
			'origin_country_iso',
			'destination_country_iso',
			'mawb',
			'bags_numbers'
		];
        $invalidParams = [];
        foreach ($required_params as $required_param) {
            if(!in_array($required_param, $paramKeys) || empty($params[$required_param])) {
                $invalidParams[] = 'Required parameter ' . $required_param . ' is missing';
            }
        }
        foreach ($paramKeys as $param) {
            if(!in_array($param, array_keys($defaultParams))) {
                $invalidParams[] = $param. ' is invalid parameter ';
            }
        }
        foreach ($defaultParams as $key => $value) {
            if(is_integer($value) && strlen($params[$key]) > $value) {
                $invalidParams[] = $key . ' length is greater than maximum character limit. Maximum character limit is ' . $value;
            }
        }
        $bags_numbers = [];
        if(isset($params['bags_numbers'])) {
            foreach ($params['bags_numbers'] as $bag_number) {
                if (!empty($bag_number)) {
                    $bags_numbers[] = $bag_number;
                }
            }
            if (empty($bags_numbers)) {
                $invalidParams[] = 'Required parameter bags_numbers is missing';
            }
        } else {
            $invalidParams[] = 'Required parameter bags_numbers is missing';
        }
        return $invalidParams;
    }

    public static function validateVoidMasterBagParams($params)
    {
        $paramKeys = array_keys($params);
        $defaultParams = [
            'mawb' => 20,
            'bags_numbers' => 20
        ];
        $required_params = [
            'mawb',
            'bags_numbers'
        ];
        $invalidParams = [];
        foreach ($required_params as $required_param) {
            if(!in_array($required_param, $paramKeys) || empty($params[$required_param])) {
                $invalidParams[] = 'Required parameter ' . $required_param . ' is missing';
            }
        }
        foreach ($paramKeys as $param) {
            if(!in_array($param, array_keys($defaultParams))) {
                $invalidParams[] = $param. ' is invalid parameter ';
            }
        }
        foreach ($defaultParams as $key => $value) {
            if(is_integer($value) && strlen($params[$key]) > $value) {
                $invalidParams[] = $key . ' length is greater than maximum character limit. Maximum character limit is ' . $value;
            }
        }
        $bags_numbers = [];
        if(isset($params['bags_numbers']) && !empty($params['bags_numbers'])) {
            foreach ($params['bags_numbers'] as $bag_number) {
                if (!empty(trim($bag_number))) {
                    $bags_numbers[] = $bag_number;
                }
            }
            if (empty($bags_numbers)) {
                $invalidParams[] = 'Required parameter bags_numbers is missing';
            } else {
                foreach ($params['bags_numbers'] as $bag_number) {
                    if(strlen($bag_number) > 45) {
                        $invalidParams[] = 'Bag ' . $bag_number . ' length is greater than maximum character limit. Maximum character limit is 45.';
                    }
                }
            }
        }
        return $invalidParams;
    }

    public static function validateCountryWareHouseParams($params)
    {
        $originCountryIos =  $params['origin_country_iso'];
        $destinationCountryIos =  $params['destination_country_iso'];
        $originWarehouseCode =  $params['origin_warehouse_code'];
        $destinationWarehouseCode =  $params['destination_warehouse_code'];
        $response = [];
        // Check if country iso code exist
        $originCountryId = self::checkCountryId($originCountryIos);
        if(empty($originCountryId)){
            $response['STATUS'] = "ERROR";
            $response['MESSAGE'] = "Origin country Iso ".$originCountryIos." not found";
            $response['ERROR'][] = "Origin country Iso ".$originCountryIos." not found";
        }
        $destinationCountryId = self::checkCountryId($destinationCountryIos);
        if(empty($destinationCountryId)){
            $response['STATUS'] = "ERROR";
            $response['MESSAGE'] = "Destination country Iso ".$destinationCountryIos." not found";
            $response['ERROR'][] = "Destination country Iso ".$destinationCountryIos." not found";
        }
        // Check if warehouse code exist
        $originWarehouseId = self::checkWarehouseId($originWarehouseCode);
        if(empty($originWarehouseId)){
            $response['STATUS'] = "ERROR";
            $response['MESSAGE'] = "Origin warehouse code ".$originWarehouseCode." not found";
            $response['ERROR'][] = "Origin warehouse code ".$originWarehouseCode." not found";
        }
        $destinationWarehouse = self::checkWarehouseId($destinationWarehouseCode);
        if(empty($destinationWarehouse)){
            $response['STATUS'] = "ERROR";
            $response['MESSAGE'] = "Destination warehouse code ".$destinationWarehouseCode." not found";
            $response['ERROR'][] = "Destination warehouse code ".$destinationWarehouseCode." not found";
        }
        return $response;
    }
    public static function checkBagNumbers($bagNumbers) {
        $errors = [];
        foreach ($bagNumbers as $key => $bagNumber) {
            if(!empty(trim($bagNumber))) {
                if(strlen($bagNumber) > 45) {
                    $errors[] = 'Bag ' . $bagNumber . ' length is greater than maximum character limit. Maximum character limit is 45.';
                } else {
                    $baggingObj = new \BaggingFilter();
                    $baggingObj->addFieldFilter('       bagnumber', \DbAccess3::escape($bagNumber));
                    $baggingObj = $baggingObj->getList();
                    if (empty($baggingObj)) {
                        $errors[] = 'Bag ' . $bagNumber . ' does not exist.';
                    }
                }
            } else {
                $errors[] = 'Value of bag_numbers[' . $key . '] is invalid.';
            }
        }
        return $errors;
    }

    public static function checkBagNumbersForVoidBag($data, $apiUserData) {
        $errors = [];
        $bagIds = [];
        foreach ($data['bags_numbers'] as $key => $bagNumber) {
            $baggingObj = new \BaggingFilter();
            $baggingObj->addFieldFilter('       bagnumber', \DbAccess3::escape($bagNumber));
            $baggingObj = $baggingObj->getList();
            if (empty($baggingObj)) {
                $errors[] = 'Bag ' . $bagNumber . ' does not exist.';
            } else {
                $userObj = new \User($baggingObj[0]->getUserId());
                if($userObj->getUserAccountId() != $apiUserData->getUserAccountId()) {
                    $errors[] = "Bag " . $bagNumber . " doesn't belong to your account.";
                    $bagIds[] = $baggingObj[0]->getId();
                } else {
                    $mawbObj = new \MawbFilter();
                    $mawbObj->addFieldFilter('       mawb_number', $data['mawb']);
                    $mawbObj = $mawbObj->getList();

                    $mawbMappingObj = new \MawbParcelMappingFilter();
                    $mawbMappingObj->addFieldFilter('       bag_id', $baggingObj[0]->getId());
                    $mawbMappingObj->addFieldFilter('       mawb_id', $mawbObj[0]->getId());
                    $mawbMappingObj = $mawbMappingObj->getList();
                    if(empty($mawbMappingObj)) {
                        $errors[] = "Bag " . $bagNumber . " is not assigned to mawb " . $data['mawb'] . ".";
                        $bagIds[] = $baggingObj[0]->getId();
                    }
                }
            }
        }
        return ['errors' => $errors, 'bag_ids' => $bagIds];
    }

    public static function checkMawb($apiData) {
        $errors = [];
        if(!empty(trim($apiData['mawb']))) {
            $mawbObj = new \MawbFilter();
            $mawbObj->addFieldFilter('       mawb_number', \DbAccess3::escape($apiData['mawb']));
            $mawbObj = $mawbObj->getList();
            if (!empty($mawbObj)) {
                $destinationCountry = new \Country($mawbObj[0]->getMawbDestinationCountryId());
                $sourceCountry = new \Country($mawbObj[0]->getMawbSourceCountryId());
                if($sourceCountry->getIso() != strtoupper($apiData['origin_country_iso']) || $destinationCountry->getIso() != $apiData['destination_country_iso']) {
                    $errors[] = 'Mawb source/destination country is different';
                } else {
                    $mawbFlightMappingFiler = new \FlightMappingFilter();
                    $mawbFlightMappingFiler->addFieldFilter('       mawb_id', $mawbObj[0]->getId());
                    $mawbFlightMappingFiler->join('flight_info', 'flight_info.id = m.flight_info_id', 'INNER');
                    $mawbFlightMappingFiler = $mawbFlightMappingFiler->getList();
                    if (!empty($mawbFlightMappingFiler)) {
                        if ($mawbFlightMappingFiler[0]->getIsClosed() == 1) {
                            $errors[] = 'Mawb is assigned to Flight Number ' . $mawbFlightMappingFiler[0]->getFlightNumber() . ' and this flight is closed.';
                        } else if ($mawbObj[0]->getMawbStatus() != 'o') {
                            $errors[] = 'Mawb ' . $apiData['mawb'] . ' is closed.';
                        }
                    } else {
                        if ($mawbObj[0]->getMawbStatus() != 'o') {
                            $errors[] = 'Mawb ' . $apiData['mawb'] . ' is closed.';
                        }
                    }
                }
            }
        }
        return $errors;
    }

    public static function checkMawbForVoidBag($apiData, $apiUserData) {
        $errors = [];
        if(!empty(trim($apiData['mawb']))) {
            $mawbObj = new \MawbFilter();
            $mawbObj->addFieldFilter('       mawb_number', \DbAccess3::escape($apiData['mawb']));
            $mawbObj = $mawbObj->getList();
            if (!empty($mawbObj)) {
                $userObj = new \User($mawbObj[0]->getAddedby());
                if($userObj->getUserAccountId() != $apiUserData->getUserAccountId()) {
                    $errors[] = "MAWB " . $apiData['mawb'] . " doesn't belong to your account.";
                } else {
                    $mawbFlightMappingFiler = new \FlightMappingFilter();
                    $mawbFlightMappingFiler->addFieldFilter('       mawb_id', $mawbObj[0]->getId());
                    $mawbFlightMappingFiler->join('flight_info', 'flight_info.id = m.flight_info_id', 'INNER');
                    $mawbFlightMappingFiler = $mawbFlightMappingFiler->getList();
                    if (!empty($mawbFlightMappingFiler)) {
                        if ($mawbFlightMappingFiler[0]->getIsClosed() == 1) {
                            $errors[] = 'Mawb is assigned to Flight Number ' . $mawbFlightMappingFiler[0]->getFlightNumber() . ' and this flight is closed.';
                        } else if ($mawbObj[0]->getMawbStatus() != 'o') {
                            $errors[] = 'Mawb ' . $apiData['mawb'] . ' is closed.';
                        }
                    } else {
                        if ($mawbObj[0]->getMawbStatus() != 'o') {
                            $errors[] = 'Mawb ' . $apiData['mawb'] . ' is closed.';
                        }
                    }
                }
            } else {
                $errors[] = 'Mawb ' .$apiData['mawb'] . 'does not exist.';
            }
        }
        return $errors;
    }

    public static function checkIsBagAssignedToMawb($bagNumbers) {
        $warnings = [];
        $bagIds = [];
        foreach ($bagNumbers as $key => $bagNumber) {
            $baggingObj = new \BaggingFilter();
            $baggingObj->addFieldFilter('       bagnumber', \DbAccess3::escape($bagNumber));
            $baggingObj = $baggingObj->getList();
            if (!empty($baggingObj)) {
                $mawbParcelMappingFilter = new \MawbParcelMappingFilter();
                $mawbParcelMappingFilter->addJoin('mawb', 'mawb.id', 'mpm.mawb_id', 'INNER JOIN');
                $mawbParcelMappingFilter->addFieldFilter('      mpm.bag_id', $baggingObj[0]->getId());
                $mawbParcelMappingFilter->addFieldFilter('      mawb.mawb_status', 'o');
                $mawbParcelMappingFilter = $mawbParcelMappingFilter->getList();
                if(!empty($mawbParcelMappingFilter)) {
                    $warnings[] = 'Bag ' . $bagNumber . ' is already assigned to another mawb.';
                } else {
                    $bagIds[] = $baggingObj[0]->getId();
                }
            }
        }
        return ['bag_ids' => $bagIds, 'warnings' => $warnings];
    }
    public static function checkIsParcelAssignedToMawb($bagNumbers) {
        $warnings = [];
        $oldBagNumber = '';
        $returnData = [];
        $existBagArray = [];
        foreach ($bagNumbers as $key => $bagId) {
            $baggingObj = new \Bagging($bagId);
            if (!empty($baggingObj)) {
                $parcelBaggingMapping = new \ParcelBaggingMappingFilter();
                $parcelBaggingMapping->addFieldFilter('      pbm.bag_id', $baggingObj->getId());
                $parcelBaggingMapping = $parcelBaggingMapping->getList();
                if(!empty($parcelBaggingMapping)) {
                    foreach ($parcelBaggingMapping as $parcelBaggingMappingObj) {
                        $mawbParcelMappingFilter = new \MawbParcelMappingFilter();
                        $mawbParcelMappingFilter->addJoin('mawb', 'mawb.id', 'mpm.mawb_id', 'INNER JOIN');
                        $mawbParcelMappingFilter->addFieldFilter('      mpm.parcel_id', $parcelBaggingMappingObj->getParcelId());
                        $mawbParcelMappingFilter->addFieldFilter('      mawb.mawb_status', 'o');
                        $mawbParcelMappingFilter = $mawbParcelMappingFilter->getList();
                        if(!empty($mawbParcelMappingFilter)) {
                            if(empty($oldBagNumber) || $baggingObj->getBagNumber() != $oldBagNumber) {
                                $existBagArray[] = $baggingObj->getId();
                                $warnings[] = 'Bag ' . $baggingObj->getBagnumber() . ' is already assigned to another mawb.';
                                $oldBagNumber = $baggingObj->getBagNumber();
                            }
                        }
                        if(empty($oldBagNumber) || $baggingObj->getBagNumber() != $oldBagNumber) {
                            if (!in_array($baggingObj->getId(), $existBagArray)) {
                                $returnData[$baggingObj->getId()]['bag_id'] = $baggingObj->getId();
                                $returnData[$baggingObj->getId()]['parcel_ids'][] = $parcelBaggingMappingObj->getParcelId();
                            }
                        } else {
                            if (!in_array($baggingObj->getId(), $existBagArray)) {
                                $returnData[$baggingObj->getId()]['parcel_ids'][] = $parcelBaggingMappingObj->getParcelId();
                            }
                        }
                    }
                } else {
                    $returnData[$baggingObj->getId()]['bag_id'] = $baggingObj->getId();
                }
            }
        }
        return ['bagging_data' => $returnData, 'warnings' => $warnings];
    }

    public static function assignBagging($params, $baggingData, $userObj) {
        $originCountryIos =  $params['origin_country_iso'];
        $destinationCountryIos =  $params['destination_country_iso'];
        $originWarehouseCode =  $params['origin_warehouse_code'];
        $destinationWarehouseCode =  $params['destination_warehouse_code'];
        $response = [];
        // Check if country iso code exist
        $originCountryId = self::checkCountryId($originCountryIos);
        if(empty($originCountryId)){
            $response['STATUS'] = "ERROR";
            $response['MESSAGE'] = "Origin country Iso ".$originCountryIos." not found";
            $response['ERROR'][] = "Origin country Iso ".$originCountryIos." not found";
        }
        $destinationCountryId = self::checkCountryId($destinationCountryIos);
        if(empty($destinationCountryId)){
            $response['STATUS'] = "ERROR";
            $response['MESSAGE'] = "Destination country Iso ".$destinationCountryIos." not found";
            $response['ERROR'][] = "Destination country Iso ".$destinationCountryIos." not found";
        }
        // Check if warehouse code exist
        if(!empty($originWarehouseCode)) {
            $originWarehouseId = self::checkWarehouseId($originWarehouseCode);
            if (empty($originWarehouseId)) {
                $response['STATUS'] = "ERROR";
                $response['MESSAGE'] = "Origin warehouse code " . $originWarehouseCode . " not found";
                $response['ERROR'][] = "Origin warehouse code " . $originWarehouseCode . " not found";
            }
        }
        if(!empty($destinationWarehouseCode)) {
            $destinationWarehouse = self::checkWarehouseId($destinationWarehouseCode);
            if (empty($destinationWarehouse)) {
                $response['STATUS'] = "ERROR";
                $response['MESSAGE'] = "Destination warehouse code " . $destinationWarehouseCode . " not found";
                $response['ERROR'][] = "Destination warehouse code " . $destinationWarehouseCode . " not found";
            }
        }
        if(!empty($response)) {
            return $response;
        }
        $mawbObj = new \MawbFilter();
        $mawbObj->addFieldFilter('mawb_number', $params['mawb']);
        $mawbObj = $mawbObj->getList();
        if(empty($mawbObj)) {
            $newMawbObj = new \Mawb();
            $newMawbObj->setMawbNumber($params['mawb']);
            $newMawbObj->setMawbSourceCountryId($originCountryId);
            $newMawbObj->setMawbDestinationCountryId($destinationCountryId);
            $newMawbObj->setMawbSourceWarehouseId($originWarehouseId);
            $newMawbObj->setMawbDestinationCountryId($destinationCountryId);
            $newMawbObj->setAddedBy($userObj->getId());
            $newMawbObj->setIsActive('y');
            $newMawbObj->setAddedDate(date("Y-m-d H:i:s",time()));
            $newMawbObj->setUpdatedDate(date("Y-m-d H:i:s",time()));
            $newMawbObj->save();
            $mawbId = $newMawbObj->getId();
        } else {
            $mawbId = $mawbObj[0]->getId();
        }

        if(empty($mawbId)) {
            $errors = ['Mawb can not be created. Please contact system administrator.'];
            $mawbErrors['STATUS'] = "ERROR";
            $mawbErrors['ERROR'] = $errors;
            $mawbErrors['MESSAGE'] = "Please fix below error(s)";
        }
        if(!empty($mawbErrors)) {
            return $mawbErrors;
        }
        foreach ($baggingData as $key => $bagData) {
            if(!empty($bagData['parcel_ids'] && is_array($bagData['parcel_ids']))) {
                foreach ($bagData['parcel_ids'] as $parcel_id) {
                    $mawbParcelMapping = new \MawbParcelMapping();
                    $mawbParcelMapping->setMawbId($mawbId);
                    $mawbParcelMapping->setBagId($key);
                    $mawbParcelMapping->setParcelId($parcel_id);
                    $mawbParcelMapping->setWharehouseId(0);
                    $mawbParcelMapping->setDateAdded(date("Y-m-d H:i:s", time()));
                    $mawbParcelMapping->setAddedBy($userObj->getId());
                    $mawbParcelMapping->save();
                }
            } else {
                $mawbParcelMapping = new \MawbParcelMapping();
                $mawbParcelMapping->setMawbId($mawbId);
                $mawbParcelMapping->setBagId($key);
//                $mawbParcelMapping->setParcelId(NULL);
                $mawbParcelMapping->setWharehouseId(0);
                $mawbParcelMapping->setDateAdded(date("Y-m-d H:i:s", time()));
                $mawbParcelMapping->setAddedBy($userObj->getId());
                $mawbParcelMapping->save();
            }
            $response['STATUS'] = "SUCCESS";
            $response['MESSAGE'] = "Mawb assigned to bag successfully";
            $response['data'] = "Mawb assigned to bag successfully.";
        }
        if(!empty($response)) {
            return $response;
        } else {
            $errors = ['Whoops, looks like something went wrong.'];
            $response['STATUS'] = "ERROR";
            $response['ERROR'] = $errors;
            $response['MESSAGE'] = "Please contact system administrator1.";
            return $response;
        }
    }

    public static function voidMawbBagging($params, $invalidBagIds = []) {
        $mawbObj = new \MawbFilter();
        $mawbObj->addFieldFilter('mawb_number', $params['mawb']);
        $mawbObj = $mawbObj->getList();
        $mawbId = $mawbObj[0]->getId();
        $responseData = [];
        foreach ($params['bags_numbers'] as $bagData) {
            $baggingObj = new BaggingFilter();
            $baggingObj->addFieldFilter('       bagnumber', $bagData);
            $baggingObj = $baggingObj->getList();
            if(!empty($baggingObj[0])) {
                if(!in_array($baggingObj[0]->getId(), $invalidBagIds)) {
                    $mawbParcelMapping = new \MawbParcelMappingFilter();
                    $mawbParcelMapping->addFieldFilter('        mawb_id', $mawbId);
                    $mawbParcelMapping->addFieldFilter('        bag_id', $baggingObj[0]->getId());
                    $mawbParcelMapping->delete_parcel_from_mapping();
                    $responseData[] = "Bag " . $bagData . " removed from master.";
                }
            }
        }
        if(!empty($responseData)) {
            $response['STATUS'] = "SUCCESS";
            $response['MESSAGE'] = "Bags removed from Mawb " . $params['mawb'];
            $response['data'] = "Bags removed from Mawb " . $params['mawb'];
        } else {
            $errors = ['Whoops, looks like something went wrong.'];
            $response['STATUS'] = "ERROR";
            $response['ERROR'] = $errors;
            $response['MESSAGE'] = "Please contact system administrator1.";
        }
        return $response;
    }
	
	public static function updateParcelWeight($data) {
        $response = [];
        $trackingNumber = $data['tracking_number'];
        $weight = $data['weight'];
        $parcelId = \ParcelFilter::getParcelIdFromTrackingNumber($trackingNumber);
        if($parcelId > 0) {
            $parcelObj = new Parcel($parcelId);
            $consignmentId = $parcelObj->getConsignmentId();
            if(\Consignment::checkConsignmentInvoiced($consignmentId)){
                $response['STATUS'] = "ERROR";
                $response['MESSAGE'] = "Consignment is invoiced";
                $response['ERROR'][] = "Weight not update. Consignment is invoiced";
            } else {
                saveConsignmentChargeableWeightApi($parcelId, $weight);
                $response['STATUS'] = "SUCCESS";
                $response['MESSAGE'] = "weight is update successfully";
            }
        } else {
            $response['STATUS'] = "ERROR";
            $response['MESSAGE'] = "Invalid tracking number";
            $response['ERROR'][] = "Please provide valid tracking number";
        }
        return $response;
    }
    function alreadyScannedParcelSameStatusCode($trackingNo,$warehouseId,$statusCode){
        $trackingNo = \ParseTrackingNumber::Parse($trackingNo);
        $return = false;
        $trackingDataFilter = new \TrackingDataFilter();
        $trackingDataFilter->addFieldFilter("    t.tracking_number", $trackingNo);
        $trackingDataFilter->addFieldFilter("    t.warehouse_id", $warehouseId);
        $trackingDataFilter->addFieldFilter("    t.entity_type", "parcel");
        $trackingDataFilter->addFieldFilter("    t.status_code_id", $statusCode);
        $trackingDataObj = $trackingDataFilter->getColumnList('id');
        if(count($trackingDataObj) > 0){
            $return = true;
        }
        return $return;
    }
    public static function getInsertMarketplaceOrders($user){
        $userMarketPlaceMappingFilter = new UserMarketPlacesMappingFilter();
        $userMarketPlaceMappingFilter->addFieldFilter("user_account_id", $user->getUserAccountId());
        $userMarketPlaceMappingFilter->addFieldFilter("market_places_id", $_POST["marketPlace_id"]);
        $userMarketPlaceMappingObj = $userMarketPlaceMappingFilter->getList();

        if (count($userMarketPlaceMappingObj) > 0) {
            $marketPlaces = new MarketPlaces($userMarketPlaceMappingObj[0]->getMarketPlacesId());
            include_classes([
                strtolower($marketPlaces->getClassName()) . '.class',
            ]);
            $className = ucwords($marketPlaces->getClassName());
            //$authdata = json_decode($userMarketPlaceMappingObj[0]->getAuthData());
            if (!empty($className)) {
                $marketPlaceClassObj = new $className($userMarketPlaceMappingObj[0]);
                $result = $marketPlaceClassObj->fetchOrders();
            } else {
                $result["STATUS"] = "ERROR";
                $result["MESSAGE"] = "Market place is not configured.";
            }
            echo json_encode($result, true);
            die;
        }
    }
    function checkserviceCode($serviceCode){
        $return = [];
        if(!empty($serviceCode)){
            foreach ($serviceCode as $serviceCodeStr) {
                $serviceData = "";
                $serviceFilter = NEW \ServiceFilter();
                $serviceFilter->addFieldFilter("code",$serviceCodeStr);
                $serviceData = $serviceFilter->getColumnList("carrier_id,label_class_name");
                if(count($serviceData) > 0){
                    if($serviceData[0]->getCarrierId() > 0){
                        $return['status'] = "success";
                        $return['carrier_id'][] = $serviceData[0]->getCarrierId();
                        $return['service_id'][] = $serviceData[0]->getId();
                        $return['label_class_name'] = $serviceData[0]->getLabelClassName();
                    }else{
                        $return['status'] = "error";
                        $return['service_code'] = $serviceCodeStr;
                        $return['error_message'] = "Service code ".$serviceCodeStr." not found in system. please check and resend the request";
                        return $return;
                    }
                }
            }
        }
        return $return;
    }

    public static function ValidateSkuDataTypes($param)
    {
        $error = [];
        if($sku == '')
        {
            $response['STATUS'] = "ERROR";
            $response['MESSAGE'] = "Sku length should be greater than 0.";
            $response['ERROR'][] = "Sku length should be greater than 0.";
        }

        if($skuName == '')
        {
            $response['STATUS'] = "ERROR";
            $response['MESSAGE'] = "Sku Name should be greater than zero.";
            $response['ERROR'][] = "Sku length should be greater than zero.";
        }

        if($length == 0)
        {
            $response['STATUS'] = "ERROR";
            $response['MESSAGE'] = "Length should be greater than zero.";
            $response['ERROR'][] = "Length should be greater than zero.";
        }

        if($width == 0)
        {
            $response['STATUS'] = "ERROR";
            $response['MESSAGE'] = "Width should be greater than zero.";
            $response['ERROR'][] = "Width should be greater than zero.";
        }

        if($height == 0)
        {
            $response['STATUS'] = "ERROR";
            $response['MESSAGE'] = "Height should be greater than zero.";
            $response['ERROR'][] = "Height should be greater than zero.";
        }

        if($weight == 0)
        {
            $response['STATUS'] = "ERROR";
            $response['MESSAGE'] = "Weight should be greater than zero.";
            $response['ERROR'][] = "Weight should be greater than zero.";
        }

    }    

    public static function createbag($param,$userObj){
        $bagtotalWeight = 0;
        $bagId =  0;
        $returnData = [];
        $mawbId = "";
        if(isset($param['bag_number']) && !empty($param['bag_number'])){
            $bagnumber =  $param['bag_number'];
        }else{
            $bagnumber = "SMRT".time();
        }
        $warehouse = new \Warehouse($userObj->getWarehouseId());
        $warehouseName = $warehouse->getWarehouseName();
        $originCountryIos =  $param['origin_country_iso'];
        $destinationCountryIos =  $param['destination_country_iso'];
        $serviceCode =  $param['service_code'];
        $mawbNumber =  $param['mawb_number'];
        $baglength =  $param['bag_length'];
        $bagWidth =  $param['bag_width'];
        $bagHeight =  $param['bag_height'];
        $parcel =  $param['parcel'];
        $actualWeight =  $param['actual_weight'];
        $labelFile = "";
        $labelFileGenerate = true;
        if(isset($param['label_file']) && !empty($param['label_file'])){
            $labelFile =  $param['label_file'];
            $labelFileGenerate = false;
        }
        // Check if country iso code exist
        $originCountryId = self::checkCountryId($originCountryIos);
        if($originCountryId < 0){
            $response['STATUS'] = "ERROR";
            $response['MESSAGE'] = "Origin country Iso ".$originCountryIos." not found";
            $response['ERROR'][] = "Origin country Iso ".$originCountryIos." not found";
        }
        $destinationCountryId = self::checkCountryId($destinationCountryIos);
        if($destinationCountryId < 0){
            $response['STATUS'] = "ERROR";
            $response['MESSAGE'] = "Destination country Iso ".$destinationCountryIos." not found";
            $response['ERROR'][] = "Destination country Iso ".$destinationCountryIos." not found";
        }
        // Check if service code exist
        $serviceCodeArr = self::checkserviceCode($serviceCode);
        $carrierIdArr =  array_unique($serviceCodeArr['carrier_id']);
        if(empty($serviceCodeArr)){
            $response['STATUS'] = "ERROR";
            $response['MESSAGE'] = "Service code not submitted";
            $response['ERROR'][] = "Service code not submitted";
        }
        if(count($serviceCodeArr) > 0 && isset($serviceCodeArr['status']) && $serviceCodeArr['status'] == "error"){
            $response['STATUS'] = "ERROR";
            $response['MESSAGE'] = $serviceCodeArr['error_message'];
            $response['ERROR'][] = $serviceCodeArr['error_message'];
        }
        if(isset($response['STATUS']) && $response['STATUS'] == "ERROR"){
            return $response;
        }
        $parcelIdArr = [];
        $parcelConIdArr = [];
        $parcelIdNotFoundArr = [];
        $parcelTotalWeight = 0;
        $parcelTrackingArr = [];
        // check if all parcels are in system
        if(count($parcel) > 0){
            foreach ($parcel as $parcelItem) {
                $parcelFilter = new \ParcelFilter();
                $parcelFilter->addFieldFilter("    tracking_number",$parcelItem);
                $parcelFilter->addFieldNotFilter("parcel_status_code",Consignment::STATUS_RECYCLED);
                $parcelObj = $parcelFilter->getColumnList("id,weight,tracking_number");
                if(count($parcelObj) > 0){
                    if($parcelObj[0]->getId() > 0){
                        $parcelIdArr[] = $parcelObj[0]->getId();
                        $parcelConIdArr[] = $parcelObj[0]->getConsignmentId();
                        $parcelTrackingArr[$parcelObj[0]->getId()] = $parcelObj[0]->getTrackingNumber();
                        $parcelTotalWeight += $parcelObj[0]->getWeight();
                    }else{
                        $parcelIdNotFoundArr[] = "Parcel [".$parcelItem."] not found in the system";
                    }
                }else{
                    $parcelIdNotFoundArr[] = "Parcel [".$parcelItem."] not found in the system";
                }
            }
        }
        if(count($parcelIdNotFoundArr) > 0){
            $response['STATUS'] = "ERROR";
            $response['MESSAGE'] = "Parcel not found or recycled in the system";
            $response['ERROR'][] = $parcelIdNotFoundArr;
            return $response;
        }
        // Check if mawb is created if not then create new
        $mawbfilter = new \MawbFilter();
        $mawbfilter->addFieldFilter("    mawb_number",$mawbNumber);
        $mawbObj = $mawbfilter->getList("id,mawb_status");
        if(count($mawbObj) > 0){
            $mawbId = $mawbObj[0]->getId();
            $mawbStatus = $mawbObj[0]->getMawbStatus();
            if($mawbStatus == "d"){
                $response['STATUS'] = "ERROR";
                $response['MESSAGE'] = "MAWB [".$mawbNumber."] already exists in the system and dispatched";
                $response['ERROR'][] = "MAWB [".$mawbNumber."] already exists in the system and dispatched";
                return $response;
            }
        }
        $bagWeightLimit = \CountryFilter::getCountryBagWeightLimit($destinationCountryId);

        if($parcelTotalWeight > $bagWeightLimit){
            $response['STATUS'] = "ERROR";
            $response['MESSAGE'] = "Parcel total weight ".$parcelTotalWeight." is over limit ".$bagWeightLimit." kg ";
            $response['ERROR'][] = "Parcel total weight ".$parcelTotalWeight." is over limit ".$bagWeightLimit." kg ";
            return $response;
        }
        if($actualWeight > $bagWeightLimit){
            $response['STATUS'] = "ERROR";
            $response['MESSAGE'] = "Bag actual weight ".$actualWeight." is over limit ".$bagWeightLimit." kg ";
            $response['ERROR'][] = "Bag actual weight ".$actualWeight." is over limit ".$bagWeightLimit." kg ";
            return $response;
        }
        // Check if these parcels belong to any open bag at same wharehouse
        $parcelBaggingMappingFilter = new \ParcelBaggingMappingFilter();
        $parcelBaggingMappingFilter->addFilterIn("     parcel_id",$parcelIdArr);
        $parcelBaggingMappingData = $parcelBaggingMappingFilter->getColumnList("parcel_id");
        if(count($parcelBaggingMappingData)){
            $response['STATUS'] = "ERROR";
            $response['MESSAGE'] = "Parcel tracking not found";
            foreach ($parcelBaggingMappingData as $parcelBaggingMappingDatum) {
                $response['ERROR'][] = "Parcel tracking number ".$parcelTrackingArr[$parcelBaggingMappingDatum->getParcelId()]." already exist in another bag";
            }
            return $response;
        }
        $bagging = new \Bagging();
        $bagging->setBagnumber($bagnumber);
        $bagging->setDateCreated(time());
        $bagging->setUserId($userObj->getId());
        $bagging->setBagSourceCountryId($originCountryId);
        $bagging->setBagDestinationCountryId($destinationCountryId);
        $bagging->setActualWeight($actualWeight);
        $bagging->setLength($baglength);
        $bagging->setWidth($bagWidth);
        $bagging->setHeight($bagHeight);
        $bagging->setBagLabel($labelFile);
        $bagging->save();
        $bagId = $bagging->getId();

        // Save data in manifest
        $manifest = new \Manifest();
        $manifest->setUserId($userObj->getId());
        $manifest->setDateCreated(time());
        $manifest->setPieces(count($parcelIdArr));
        $manifest->setWeight($actualWeight);
        $manifest->save();
        $manifestId = $manifest->getId();
        
        // Save Data in Manifest Entity Mapping Table
        $manifestEntityMapping = new \ManifestEntityMapping();
        $manifestEntityMapping->setEntityId($bagId);
        $manifestEntityMapping->setManifestId($manifestId);
        $manifestEntityMapping->setManifestEntityType("b");
        $manifestEntityMapping->save();
            
        // Save Data in Parcel Mapping Table
        foreach ($parcelIdArr as $parcelIdStr) {
            $parcelBaggingMapping = new \ParcelBaggingMapping();
            $parcelBaggingMapping->setParcelId($parcelIdStr);
            $parcelBaggingMapping->setBagId($bagId);
            $parcelBaggingMapping->setAddedBy($userObj->getId());
            $parcelBaggingMapping->setAddedDate(time());
            $parcelBaggingMapping->save();
        }
        // Save Data in Bagging Service Mapping Table
        foreach ($serviceCodeArr['service_id'] as $serviceId) {
            $baggingServiceMapping = new \BaggingServicesMapping();
            $baggingServiceMapping->setBagId($bagId);
            $baggingServiceMapping->setServiceId($serviceId);
            $baggingServiceMapping->save();
        }
        // If Mawb not exsit then crate New
        if(empty($mawbId)){
            $mawb = new \Mawb();
            $mawb->setMawbNumber($mawbNumber);
            $mawb->setMawbSourceCountryId($originCountryId);
            $mawb->setMawbDestinationCountryId($destinationCountryId);
            $mawb->setIsActive('y');
            $mawb->setAddedBy($userObj->getId());
            $mawb->setAddedDate(date("Y-m-d H:i:s", time()));
            $mawb->setUpdatedDate(date("Y-m-d H:i:s", time()));
            $mawb->setMawbStatus('o');
            $mawb->save();
            $mawbId = $mawb->getId();
        }
//        Save parcel in MAWB mapping table
        if($mawbId > 0){
            foreach ($parcelIdArr as $parcelIdStr) {
                $mawbParcelMapping = new \MawbParcelMapping();
                $mawbParcelMapping->setMawbId($mawbId);
                $mawbParcelMapping->setParcelId($parcelIdStr);
                $mawbParcelMapping->setBagId($bagId);
                $mawbParcelMapping->setDateAdded(time());
                $mawbParcelMapping->setAddedBy($userObj->getId());
                $mawbParcelMapping->setWharehouseid($userObj->getWarehouseId());
                $mawbParcelMapping->save();
            }
        }
        // Generate label
        $className = $serviceCodeArr['label_class_name'];
        $errorMessage = false;
        if(count($carrierIdArr) == 1 && $labelFileGenerate){
            $className = ucwords($className);
            $className = "\\".$className;
            $classObj = new $className();
            if(method_exists($classObj,'bagLabel')){
                $returnClassData = $classObj->bagLabel($parcel, $bagging, $serviceCode);
                if(isset($returnClassData['STATUS']) && $returnClassData['STATUS'] == "SUCCESS"){
                    $returnData['BAG_NUMBER'] = $returnClassData['BAG_NUMBER'];
                    $returnData['BAG_LABEL'] = $returnClassData['LABEL'];
                }
                else
                {
                    $message = $returnClassData['MESSAGE'];
                    $error = [];
                    if(empty($returnClassData['ERROR']) || !array($returnClassData['ERROR'])){
                        $error[] = $message;
                    }else{
                        $error = $returnClassData['ERROR'];
                    }
                    $errorMessage = true;
                    \ParcelBaggingMapping::deleteByBagId($bagId);
                    \Bagging::deleteByBagId($bagId);
                    \Manifest::DeleteManifestById($manifestId);
                    
                    $response['STATUS'] = "ERROR";
                    $response['MESSAGE'] = $message;
                    $response['ERROR'] = $error;
                    return $response;
                }
            }else{
                $boxLabel = new \BoxLabel();
                $returnBag = $boxLabel->buildPDFDocuments($bagnumber, count($parcelIdArr), "", $mawbNumber, $warehouseName);
                $returnData['BAG_NUMBER'] = $bagnumber;
                $returnData['BAG_LABEL'] = $returnBag;            }
        }else if($labelFileGenerate){
            $boxLabel = new \BoxLabel();
            $returnBag = $boxLabel->buildPDFDocuments($bagnumber, count($parcelIdArr), "", $mawbNumber, $warehouseName);
            $returnData['BAG_NUMBER'] = $bagnumber;
            $returnData['BAG_LABEL'] = $returnBag;
            
        }else{
            $returnData['BAG_NUMBER'] = $bagnumber;
            $returnData['BAG_LABEL'] = $labelFile;
        }
        if(!$errorMessage){
            $bagLabel = str_replace(SETTING_URL_ASSETS, "",$returnData['BAG_LABEL']);
            $bagObjNew = new \Bagging($bagId);
            $bagObjNew->setBagLabel($bagLabel);
            $bagObjNew->setBagnumber($returnData['BAG_NUMBER']);
            $bagObjNew->setPieces(count($parcelIdArr));
            $bagObjNew->setWeight($parcelTotalWeight);
            $bagObjNew->save();
            

        /* Assign bag number and Bag id to consignment_dropoff_mapping table for tracking purpose
        *
         *  First fetch the data having this consignment id as dropoff_consignment_id in table
         *  Then assign that record bag numbr and bag id
         */
            $parcelConIdArr = array_unique($parcelConIdArr);
            if(count($parcelConIdArr) > 0){
                foreach ($parcelConIdArr as $conId) {
                    $consignmentDropoffMappingFilter = New \ConsignmentDropoffMappingFilter();
                    $consignmentDropoffMappingFilter->addFieldFilter("    dropoff_consignment_id",$conId);
                    $consignmentDropoffMappingFilterData = $consignmentDropoffMappingFilter->getColumnList('id');
                    if(count($consignmentDropoffMappingFilterData) > 0){
                        $consignmentDropoffMapping = New \ConsignmentDropoffMapping($consignmentDropoffMappingFilterData[0]->getId());
                        $consignmentDropoffMapping->setBagId($bagId);
                        $consignmentDropoffMapping->setBagNumber($returnData['BAG_NUMBER']);
                        $consignmentDropoffMapping->save();
                    }
                }
            }

            $consignmentFilter = new \ConsignmentFilter();
            $consignmentFilter->addJoin("parcel p", "c.id = p.consignment_id");
            $consignmentFilter->addFilterNew("    p.tracking_number in ('".implode("','", $parcelTrackingArr)."')" );
            $consignmentList = $consignmentFilter->getListNew("c.id, p.tracking_number, c.awb, c.hawb, c.weight, c.number_pieces, c.user_id, c.service_id, c.description, c.company, c.contact,c.address_line_1, c.address_line_2, c.address_line_3, c.city, c.country_id, c.postcode, c.value, c.currency");
            
            $manifestSummaryReport = new \ManifestBrief();
            $manifestReport = $manifestSummaryReport->AddHTML($consignmentList, $manifestId);
            if($manifestReport != ''){
                $fileName = uniqid();
                $baglabelFilePath = "/bag_pdf/".$fileName.".pdf";
               // $bagLabel = $returnData['BAG_LABEL'];
                $PDFMerger = new \PDFMerger();
                $PDFMerger->addPDF(_ASSETS_PATH.$bagLabel);
                $PDFMerger->addPDF($manifestReport);
                try {
                    $PDFMerger->merge('file',_ASSETS_PATH.$baglabelFilePath);
                } catch (Exception $e) {
                    $response['STATUS'] = "ERROR";
                    $response['MESSAGE'] = "Unable to create bag.";
                    $response['ERROR'] = $e->getMessage();
                    return $response;
                }
            }
            $response['STATUS'] = "SUCCESS";
            $response['MESSAGE'] = "Bag created successfully";
            $response['BAG_NUMBER'] = $returnData['BAG_NUMBER'];
            $response['BAG_LABEL'] = _ASSETS_URL.$baglabelFilePath;
        }
        else
        {
            $response['STATUS'] = "ERROR";
            $response['MESSAGE'] = "Sorry, Unable to create Bag.";
            $response['ERROR'][] = "Sorry, Unable to create Bag.";
        }
        return $response;

    }

    public static function validateDispatchMarketplaceOrderParams($params)
    {
        $paramKeys = array_keys($params);
        $defaultParams = [
            'marketplace_order_number' => 'array'
        ];
        $defaultParamsKeys = array_keys($defaultParams);
        $requiredParams = [
            'marketplace_order_number'
        ];
        $invalidParams = [];
        foreach ($requiredParams as $requiredParam) {
            if(!in_array($requiredParam, $paramKeys)) {
                $invalidParams[] = 'Required parameter ' . $requiredParam . ' is missing';
            }
        }
        foreach ($paramKeys as $param) {
            if(!in_array($param, $defaultParamsKeys)) {
                $invalidParams[] = $param. ' is invalid parameter ';
            }
        }
        foreach ($defaultParams as $key => $value) {
            if(is_integer($value) && strlen($params[$key]) > $value) {
                $invalidParams[] = $key . ' length is greater than maximum character limit. Maximum character limit is ' . $value;
            }
        }
        return $invalidParams;
    }

    public static function dispatchMarketplaceOrder($marketplaceOrderNumber) {
        $consgmnentFilter = new \ConsignmentFilter();
        $consgmnentFilter->addJoin('     user u', '  u.id = c.user_id');
        $consgmnentFilter->addFilterIn('      c.hawb', $marketplaceOrderNumber,'filter');
        $consgmnentFilterObjs = $consgmnentFilter->getListNew('u.user_account_id,c.hawb');
        $response = [];
        $marketPlaceOrders = [];
        $userAccountId = '';
        $totalMarketplaceOrderNumber = 1;
        if(is_array($marketplaceOrderNumber)) {
            $totalMarketplaceOrderNumber = count($marketplaceOrderNumber);
        }
        if(count($consgmnentFilterObjs)) {
            foreach($consgmnentFilterObjs as $consgmnentFilterObj) {
                if($userAccountId != '' && ($userAccountId != $consgmnentFilterObj->getUserAccountId())) {
                    $userAccountId = '';
                    break;
                } else {
                    $hawb = $consgmnentFilterObj->getHawb();
                    $userAccountId = $consgmnentFilterObj->getUserAccountId();
                    $marketplaceOrderFilter = new \MarketPlaceOrderFilter();
                    $marketplaceOrderFilter->addFieldFilter('        marketplace_order_number',$hawb);
                    $marketplaceOrderFilterObjs = $marketplaceOrderFilter->getList();
                    if(count($marketplaceOrderFilterObjs)) {
                        foreach($marketplaceOrderFilterObjs as $marketplaceOrderFilterObj) {
                            $marketplaceId = $marketplaceOrderFilterObj->getMarketplaceId();
                            if(!isset($marketPlaceOrders[$marketplaceId])) {
                                $marketPlaceOrders[$marketplaceId] = [];
                            }
                            array_push($marketPlaceOrders[$marketplaceId],$hawb);
                        }
                    }
                }
            }
            $allexistHawb = array_keys(array_count_values(call_user_func_array('array_merge', $marketPlaceOrders)));
            $totalExistConsignment = count($allexistHawb);
            $notexist = array_merge(array_diff($allexistHawb, $marketplaceOrderNumber), array_diff($marketplaceOrderNumber, $allexistHawb));
            if($totalExistConsignment == $totalMarketplaceOrderNumber) {
                if ($userAccountId != '') {
                    if (count($marketPlaceOrders)) {
                        foreach ($marketPlaceOrders as $marketPlaceId => $maketHawb) {
                            $userMarketPlaceMappingFilter = new \UserMarketPlacesMappingFilter();
                            $userMarketPlaceMappingFilter->addFieldFilter("    user_account_id", $userAccountId);
                            $userMarketPlaceMappingFilter->addFieldFilter("    market_places_id", $marketPlaceId);
                            $userMarketPlaceMappingObj = $userMarketPlaceMappingFilter->getList('ump.*');

                            if (count($userMarketPlaceMappingObj)) {
                                $marketPlaceDetailObj = new \MarketPlaces($marketPlaceId);
                                include_classes([
                                    $marketPlaceDetailObj->getClassName() . '.class',
                                ]);
                                $className = ucwords($marketPlaceDetailObj->getClassName());
                                if (!empty($className)) {
                                    $marketPlaceClassObj = new $className($userMarketPlaceMappingObj[0]);
                                    $result = $marketPlaceClassObj->dispatchLabel($maketHawb, 'api');
                                    $response['STATUS'] = $result['STATUS'];
                                    $response['MESSAGE'][] = $result['MESSAGE'];
                                    $response['marketplace_order_number'][] = $maketHawb;
                                }
                            }
                        }
                    } else {
                        $response['STATUS'] = "ERROR";
                        $response['ERROR'] = "All marketplace order number not found";
                        $response['MESSAGE'] = "Marketplace order number not found";
                    }
                } else {
                    $response['STATUS'] = "ERROR";
                    $response['ERROR'] = "Sorry marketplace order number of multiple account not allowed";
                    $response['MESSAGE'] = "Sorry marketplace order number of multiple account not allowed";
                }
            } else {
                $response['STATUS'] = "ERROR";
                $response['ERROR'] = "Sorry marketplace order number not found " . implode(',', $notexist);
                $response['MESSAGE'] = "Sorry marketplace order number not found ";
            }
        } else {
            $response['STATUS'] = "ERROR";
            $response['ERROR'] = "Marketplace order number not found";
            $response['MESSAGE'] = "Marketplace order number not found";
        }
        return $response;
    }
     public static function JdAccessToken($token) {
        if(!empty($token)){
            $oauthaccesToken = OauthAccessTokens::getAccessToken($token);
           if(count($oauthaccesToken) > 0){
               $response['STATUS'] = "SUCCESS";
               $response['ERROR'] = "Access token is valid";
               $response['MESSAGE'] = "Access token is valid";
               $response['USER_ID'] = $oauthaccesToken['user_id'];
           }else{
               $response['STATUS'] = "ERROR";
               $response['ERROR'] = "Access token not found";
               $response['MESSAGE'] = "Access token not found";
           }
        }else{
            $response['STATUS'] = "ERROR";
            $response['ERROR'] = "Access token not found";
            $response['MESSAGE'] = "Access token not found";
        }
        return $response;
     }
}
?>
