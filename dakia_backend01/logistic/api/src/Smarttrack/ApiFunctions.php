<?php
namespace Smarttrack;
use Consignment;
use Permissions;
use Tracking;
use User;
use UserAccountFilter;
use UserHasGroupsFilter;
use UserServicesRouting;
use Tariffs;
use WarehouseNewFilter;
use UPS;

class ApiFunctions
{
    public static function getUserById($userId){
        $userData = new User($userId);
        return $userData;
    }
    public static function getUserAccountDetails($apiUserData){
        $userAccounts = new UserAccountFilter();
        if ($apiUserData->getUserType() == User::USER_TYPE_CORPORATE) {
            $userAccounts->addFieldFilter('id', $apiUserData->getUserAccountId());
        }else{
            die('You are not allowed to perform this action');//will discuss later
        }
        $customerObjs = $userAccounts->getList();
        return $customerObjs;
    }
    public static function getUserServices($accountId,$serviceId = ''){
        $userServiceRouting = new UserServicesRouting();
        return $userServiceRouting->getAllUserServices($accountId,$serviceId);
    }
    public static function getServiceDeliveryCountries($serviceId){
        $userServiceRouting = new UserServicesRouting();
        return $userServiceRouting->getServiceDeliveryCountries($serviceId);
    }
    public static function getTracking($trackingNo){
        $trackingNumber = cleanTrackingNo($trackingNo);
        $trackingObj = new \Tracking(); //  Calling the constructor
        return  $trackingObj->GetTracking($trackingNumber);
    }
    public static function validateShipment($shipData,$user_id){
        $errorsArray= [];
        $userObj = new \User($user_id);
        $serviceObj = new \Services();
        $serviceCode = (isset($shipData['service_code']) ? $shipData['service_code'] : null);
        $serviceObj = $serviceObj->getServiceByCode(isset($shipData['service_code']) ? $shipData['service_code'] : null );
        if($serviceObj){
            $serviceId = $serviceObj->getId();
            $shipData['service_type']= $serviceId;
            $shipData['service_id'] = $serviceId;
            $shipData['is_product']= $serviceObj->getIsCustomized();
        }else{
            $errorsArray['MESSAGE'] = "Invalid Service Code";
            $errorsArray['ERROR']['shipmentErrors'] = ["serviceCode $serviceCode is invalid.System cannot find any service for the specified service code"];
        }
        $senderCountry = \Country::getCountryFromIso(isset($shipData['sender_country_iso']) ? $shipData['sender_country_iso'] : "XXXXXXX");
        if($senderCountry){
            $senderCountryId = $senderCountry->getId();
            $shipData['country']= $senderCountryId;
            $shipData['sender_country']= $senderCountryId;
        }else{
            $errorsArray['MESSAGE'] = "Invalid Sender Country Iso";
            $errorsArray['ERROR']['shipmentErrors'] = ["countryIso is invalid. Please enter a valid sender country iso."];
        }
        $receiverCountry = \Country::getCountryFromIso(isset($shipData['receiver_country_iso']) ? $shipData['receiver_country_iso'] : "XXXXXXX");
        if($receiverCountry){
            $receiverCountryId = $receiverCountry->getId();
            $shipData['country']= $receiverCountryId;
            $shipData['receiver_country']= $receiverCountryId;
        }else{
            $errorsArray['MESSAGE'] = "Invalid Receiver Country Iso";
            $errorsArray['ERROR']['shipmentErrors'] = ["countryIso is invalid. Please enter a valid receiver country iso."];
        }

        if(!empty($errorsArray)){
            $errorsArray['STATUS'] = "ERROR";
            return $errorsArray;
        }else{
            $shipData['user_id'] = $user_id;
            $consignment = \Consignment::getConsignmentObjectFromArray($shipData);
            $consignmentValidator = new \ConsignmentValidator($consignment, $shipData['parcel'],$userObj);
            $IsValidConsignment = $consignmentValidator->validateConsignment();
            if(!$IsValidConsignment){
                $errorsArray['STATUS'] = "ERROR";
                $errorsArray['ERROR']['shipmentErrors'] = $consignmentValidator->getErrorList();
                return $errorsArray;
            }
        }
        unset($shipData['serviceCode']);
        unset($shipData['countryIso']);
        return ["STATUS"=>"SUCCESS","DATA"=>$shipData];
    }
    public static function addShipment($shipData,$user_id){
        $shipData['save_invalid'] = 0;
        $errorsArray= [];
        $userObj = new \User($user_id);
        $serviceObj = new \Services();
        $serviceCode = (isset($shipData['service_code']) ? $shipData['service_code'] : null);
        $serviceObj = $serviceObj->getServiceByCode(isset($shipData['service_code']) ? $shipData['service_code'] : null );
        if($serviceObj){
            $serviceId = $serviceObj->getId();
            $shipData['service_type']= $serviceId;
            $shipData['service_id'] = $serviceId;
            $shipData['is_product']= $serviceObj->getIsCustomized();
        }else{
            $errorsArray['MESSAGE'] = "Invalid Service Code";
            $errorsArray['ERROR']['shipmentErrors'] = ["serviceCode $serviceCode is invalid.System cannot find any service for the specified service code"];
        }
        $senderCountry = \Country::getCountryFromIso(isset($shipData['sender_country_iso']) ? $shipData['sender_country_iso'] : "XXXXXXX");
        if($senderCountry){
            $senderCountryId = $senderCountry->getId();
            $shipData['country']= $senderCountryId;
            $shipData['sender_country']= $senderCountryId;
        }else{
            $errorsArray['MESSAGE'] = "Invalid Sender Country Iso";
            $errorsArray['ERROR']['shipmentErrors'] = ["countryIso is invalid. Please enter a valid sender country iso"];
        }
        $receiverCountry = \Country::getCountryFromIso(isset($shipData['receiver_country_iso']) ? $shipData['receiver_country_iso'] : "XXXXXXX");
        if($receiverCountry){
            $receiverCountryId = $receiverCountry->getId();
            $shipData['country']= $receiverCountryId;
            $shipData['receiver_country']= $receiverCountryId;
        }else{
            $errorsArray['MESSAGE'] = "Invalid Receiver Country Iso";
            $errorsArray['ERROR']['shipmentErrors'] = ["countryIso is invalid. Please enter a valid receiver country iso"];
        }

        if(!empty($errorsArray)){
            $errorsArray['STATUS'] = "ERROR";
            return $errorsArray;
        }else {
            $shipData['user_id'] = $user_id;
            $createShip = Consignment::saveShipment($shipData, $user_id, false, '', 'api');
            return $createShip;
        }
    }
    public static function getLabel($hawb, $labelType="pdf", $labelSize="100x150"){
        $error = [
            'STATUS' => 'ERROR',
            'LABEL'   => null,
            'MESSAGE' => "Invalid order_reference"
        ];
        if(!empty($hawb)) {
            $consignmentFilter = new \ConsignmentFilter();
            $consignmentFilter->addFieldFilter("    hawb", $hawb);
            $consignmentFilter->addFieldEqualFilter("shipment_status", "<>", Consignment::STATUS_RECYCLED);
            $consignmentObjs = $consignmentFilter->getListNew();
            $consignmentObj = [];
            if(!empty($consignmentObjs)) {
                $consignmentObj = $consignmentObjs[0];
            }
            if($consignmentObj && $consignmentObj->getId() > 0){
				$createShip = [];
				$labelNotFound = true;
//                if(!empty($consignmentObj->getAwb()) && !empty($consignmentObj->getLabelFile())){
//					if (file_exists(_ASSETS_PATH . "pdf/" . $consignmentObj->getLabelFile())) {
//						$createShip["STATUS"] = "SUCCESS";
//						$createShip["LABEL"] = SETTING_URL . "_assets/pdf/" . $consignmentObj->getLabelFile();
//						$createShip["LABEL_BIN_STR"] = base64_encode(file_get_contents(_ASSETS_PATH . "pdf/" . $consignmentObj->getLabelFile()));
//						$createShip["TRACKING_NUMBER"] = [$consignmentObj->getAwb()];
//						$createShip["CONSIGNMENT_ID"] = $consignmentObj->getId();
//						$createShip["ORDER_REFERENCE"] = $consignmentObj->getHawb();
//						$createShip["HAWB"] = $consignmentObj->getHawb();
//						$createShip["ID"] = $consignmentObj->getId();
//						$labelNotFound = false;
//					}
//				}
//                if($labelNotFound){
                    $createShip = Consignment::getInstantLabel($consignmentObj,$labelType,$labelSize,true);
//				}
                return $createShip;
            }else{
                return $error;
            }
        } else {
            return $error;
        }
    }
    public static function UserQuotes($quoteData,$userAccountId){
        $originCountry = \Country::getCountryFromIso(isset($quoteData['origin_country_iso']) ? $quoteData['origin_country_iso'] : "XXXXXXX");
        if($originCountry){
            $quoteData['origin_country_id']= $originCountry->getId();
        }else{
            $errorsArray['MESSAGE'] = "Invalid Country Iso";
            $errorsArray['ERROR'][] = "origin Country Iso is invalid. Please enter a valid country iso.";
        }

        $deliveryCountry = \Country::getCountryFromIso(isset($quoteData['delivery_country_iso']) ? $quoteData['delivery_country_iso'] : "XXXXXXX");
        if($deliveryCountry){
            $quoteData['delivery_country_id']= $deliveryCountry->getId();
        }else{
            $errorsArray['MESSAGE'] = "Invalid Country Iso";
            $errorsArray['ERROR'][] = "delivery Country Iso is invalid. Please enter a valid country iso.";
        }

        if(!empty($errorsArray)){
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
        return $userQuotations = Tariffs::getUserQuotationsByAssignedServices($userAccountId, $quoteData['origin_country_id'],$quoteData['delivery_country_id'],$originPostcode,$deliveryPostcode,$originCity,$deliveryCity,$quoteData['weight'],$quoteData['number_of_pieces'],0,0,0,0,"");
    }
    public static function createUserAccount($accountData,$apiUserData){
        $countryExt = \Country::getCountryFromIso(isset($accountData['countryIso']) ? $accountData['countryIso'] : "XXXXXXX");
        if($countryExt){
            $accountData['countryId']= $countryExt->getId();
        }else{
            $errorsArray['MESSAGE'] = "Invalid Country Iso";
            $errorsArray['ERRORS'][] = "CountryIso is invalid. Please enter a valid country iso.";
        }
        $companyRegCountry = \Country::getCountryFromIso(isset($accountData['companyRegCountryIso']) ? $accountData['companyRegCountryIso'] : "XXXXXXX");
        if($companyRegCountry){
            $accountData['companyRegCountryId']= $companyRegCountry->getId();
        }else{
            $errorsArray['MESSAGE'] = "Invalid Country Iso";
            $errorsArray['ERRORS'][] = "companyRegCountryIso is invalid. Please enter a valid country iso.";
        }
//		echo "<pre>"; print_r($errorsArray); echo "</pre>"; die();
        if(!empty($errorsArray)){
            $errorsArray['STATUS'] = "ERROR";
            return $errorsArray;
        }
        unset($accountData['countryIso']);
        unset($accountData['companyRegCountryIso']);
        return \CustomerAccount::createUserAccountApi($accountData,$apiUserData);

    }
    public static function getUserAccounts($apiUserData){
        $userAccounts = new UserAccountFilter();
        if ($apiUserData->getUserType() == User::USER_TYPE_CORPORATE) {
            $userAccounts->addFieldFilter('parentid', $apiUserData->getUserAccountId());
        }else{
            die('You are not allowed to perform this action');//will discuss later
        }
        $customerObjs = $userAccounts->getList();
        return $customerObjs;
    }
    public function getUserAccessabilities($apiUserData){
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
        $groupsList->addFilter("is_deleted = 0 AND is_active = 1 ". $groupsStrIn."");
        return $userGroupList = $groupsList->getList();

    }
    public function GetWarehouseList($apiUserData){
        $wareHouseFilter = new WarehouseNewFilter();
        $wareHouseFilter->addCountryJoin();
        return  $wareHouseList = $wareHouseFilter->getList();

    }
    public static function addUser($accountData,$apiUserData){
//		echo "<pre>"; print_r($accountData); echo "</pre>"; die();
        $countryExt = \Country::getCountryFromIso(isset($accountData['countryIso']) ? $accountData['countryIso'] : "XXXXXXX");
        if($countryExt){
            $accountData['countryId']= $countryExt->getId();
        }else{
            $errorsArray['MESSAGE'] = "invalid parameters";
            $errorsArray['ERRORS'][] = "CountryIso is invalid. Please enter a valid country iso.";
        }
        //check if warehousecode is valid
        if(isset($accountData['warehouseCode'])){
            $warehouseCodeCheck   =   new WarehouseNewFilter();
            $warehouseExist = $warehouseCodeCheck->getWarehouseByCode($accountData['warehouseCode']);
            if(count($warehouseExist) < 1){
                $errorsArray['MESSAGE'] = "invalid parameters";
                $errorsArray['ERRORS'][] = "Warehouse code not exist. Please enter valid code!";
            }else{
                $accountData['warehouseId'] = isset($warehouseExist[0]) ? $warehouseExist[0]->getId() : NULL;
            }
        }
        //check if accessabilites are valid
        $accountData['permission_group'] =[];
        if(isset($accountData['accessabilities'])){
            $accessabilitesCode = explode(",",$accountData['accessabilities']);
            $assignedAccessAbilies = [];
            $assignedAccessAbiliesIds = [];
            $userAccessAbilites = ApiFunctions::getUserAccessabilities($apiUserData);
            if(count($userAccessAbilites) > 0){
                foreach($userAccessAbilites as $accessability){
                    $assignedAccessAbilies[$accessability->getGroupId()] = $accessability->getGroupSlug();
                }
            }
            //check if request accessabilities are from user assigned accessabilites
            $diff = array_diff($accessabilitesCode,$assignedAccessAbilies);

            if(count($diff) > 0){
                $errorsArray['MESSAGE'] = "AccessError";
                $errorsArray['ERRORS'][] = "You can not assign these accessabilites (".implode(",",$diff).").Please contact your system administrator for more help!";
            }else{
                $selectedAccessabilites = array_intersect($assignedAccessAbilies,$accessabilitesCode);
                if(count($selectedAccessabilites) > 0){
                    $accountData['permission_group'] = array_keys($selectedAccessabilites);
                }
            }
        }
        //check duplication for username
        if(isset($accountData['userName'])){
            $userfilter = new \UserFilter();
            $userfilter->addUserNameFilter(trim($accountData['userName']));
            $userfilter->getList();
            $checkuser = $userfilter->getCount();
            if ($checkuser > 0) {
                $errorsArray['MESSAGE'] = "Duplicate Fields";
                $errorsArray['ERRORS'][] = "Duplicated Username, please use another name!";
            }
        }

        if(!empty($errorsArray)){
            $errorsArray['STATUS'] = "ERROR";
            return $errorsArray;
        }
        unset($accountData['countryIso']);
        return User::createUserApi($accountData,$apiUserData);

    }
    public static function addScanning($validateScannigData,$apiUserData){
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
        $trackpoint = $warehouseName." - ".$countryIso3;
        if($validateScannigData['status_code'] == 146){
            if(!empty($warehouseName))
                $carrierDesc = 'Arrived at Sort Facility '.$trackpoint;
        }else if($validateScannigData['status_code'] == 144){
            if(!empty($warehouseName))
                $carrierDesc = 'Departed Facility in '.$trackpoint;
        }else{
            $carrierDesc = \Tracking::$oneworld_status_desc[$validateScannigData['status_code']];
            $trackpoint = "";
        }

        // Check if parcel is exist
        $parcelFilter = new \ParcelFilter();
        $parcelFilter->addFieldFilter("tracking_number", \ParseTrackingNumber::Parse($validateScannigData['tracking_number']));
        $parcelObj = $parcelFilter->getColumnList("tracking_number");
        if(count($parcelObj) > 0){
            // Parcel Exist
            // Check if parcel is already scaned with same warehouse and status code
            if(self::alreadyScannedParcelSameStatusCode($validateScannigData['tracking_number'],$warehouseid,$validateScannigData['status_code'])){
                $errorsArray['STATUS'] = "ERROR";
                $errorsArray['MESSAGE'] = $validateScannigData['tracking_number']." Parcel is already scanned";
                $errorsArray['ERRORS'][] = $validateScannigData['tracking_number']." Parcel is already scanned";
                return $errorsArray;
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

            if(!empty($validateScannigData['parcel_image'])){
                $imageReturnData = self::saveImageFromApi($validateScannigData['parcel_image'], "parcel_img");
                $imageFilePath = $imageReturnData['file_name'];
                $trackingData->setParcelImage($imageFilePath);
                if(!empty($imageReturnData['return'])){
                    $errorsArray = $imageReturnData['return'];
                }
            }
            if(!empty($validateScannigData['signature']))
                $trackingData->setSignatory($validateScannigData['signature']);

            if(!empty($validateScannigData['signature_image'])){
                $imageReturnData = self::saveImageFromApi($validateScannigData['signature_image'], "pod_img");
                $imageFilePathPod = $imageReturnData['file_name'];
                $trackingData->setPodImage($imageFilePathPod);
                if(!empty($imageReturnData['return'])){
                    $errorsArray = $imageReturnData['return'];
                }
            }
            if(!empty($validateScannigData['latitude']))
                $trackingData->setLatitude($validateScannigData['latitude']);

            if(!empty($validateScannigData['longitude']))
                $trackingData->setLongitude($validateScannigData['longitude']);

            $trackingData->save();
            if($trackingData->getId() > 0){
                $response['STATUS'] = "SUCCESS";
                $response['MESSAGE'] = "Parcel scanned successfully";
            }else{
                $errorsArray['MESSAGE'] = "Parcel not scanned";
                $errorsArray['ERRORS'][] = "Parcel not scanned, please scan parcel again!";
            }
        }else{
            // Parcel Not Exist
            $errorsArray['MESSAGE'] = "Parcel not found";
            $errorsArray['ERRORS'][] = "Parcel not found in system, please scan other parcel!";
        }
        if(!empty($errorsArray)){
            $errorsArray['STATUS'] = "ERROR";
            return $errorsArray;
        }else{
            return $response;
        }
    }
    public static function saveImageFromApi($data,$fileName,$type="png") {
        $returnData = [];
        $errorsArray = [];
        if (!file_exists("../_assets/images/pod_images")) {
            mkdir("../_assets/images/pod_images", 0777, true);
        }
        $data = base64_decode($data);
        if ($data === false) {
            $errorsArray['STATUS'] = "ERROR";
            $errorsArray['MESSAGE'] = "Invalid image data, Please varify your encoded string";
            $errorsArray['ERRORS'][] = "Invalid image data, Please varify your encoded string";
            $returnData['return'] = $errorsArray;
            $returnData['type'] = "";
            $returnData['file_name'] = "";
            return $returnData;
        }
        $type = self::RetrieveExtension($data);
        if (!in_array($type, [ 'jpg', 'jpeg', 'gif', 'png' ])) {
            $errorsArray['STATUS'] = "ERROR";
            $errorsArray['MESSAGE'] = "Invalid image type, allow image types are 'jpg', 'jpeg','gif', 'png'";
            $errorsArray['ERRORS'][] = "Invalid image data, allow image types are 'jpg', 'jpeg','gif', 'png'";
            $returnData['return'] = $errorsArray;
            $returnData['type'] = "";
            $returnData['file_name'] = "";
            return $returnData;
        }
        $fileName = $fileName."_".time().".".$type;
        $filePath = "../_assets/images/pod_images/".$fileName;
        file_put_contents($filePath, $data);
        $returnData['return'] = $errorsArray;
        $returnData['type'] = $type;
        $returnData['file_name'] = $fileName;
        return $returnData;
    }
    public static function RetrieveExtension($data){
        if (!file_exists("../_assets/images/pod_images/temp")) {
            mkdir("../_assets/images/pod_images/temp", 0777, true);
        }
        $tempFile = "../_assets/images/pod_images/temp/".time();
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
    public static function voidLabel($validateScannigData,$apiUserData){
        // set variable
        $consignmentIdArr = [];
        $response = [];
        // Get consignment id from hawb number
        if(count($validateScannigData['hawb']) > 0){
            foreach ($validateScannigData['hawb'] as $validateScannig) {
                $consignmentFilter = new \ConsignmentFilter();
                $consignmentFilter->addFieldEqualFilter("hawb", "=", $validateScannig);
                $consignmentFilter->addFieldEqualFilter("shipment_status", "<>", Consignment::STATUS_RECYCLED);
                $consignmentObj = $consignmentFilter->getListNew("id");
                if(count($consignmentObj) > 0){
                    $consignmentIdArr[] = $consignmentObj[0]->getId();
                }
            }
        }
        if(count($consignmentIdArr) > 0){
            $consignment = new \Consignment();
            $resData = $consignment->RecycledShipment($consignmentIdArr,true);
            if($resData['status'] == "ERROR"){
                $response['STATUS'] = "ERROR";
                $response['MESSAGE'] = $resData['message'];
                $response['ERRORS'][] = $resData['message'];
            }else if ($resData['status'] == "SUCCESS"){
                $response['STATUS'] = "SUCCESS";
                $response['MESSAGE'] = $resData['message'];
            }
        }else{
            $response['STATUS'] = "ERROR";
            $response['MESSAGE'] = "Invalid data, Please enter valid consignment hawb";
            $response['ERRORS'][] = "Invalid data, Please enter valid consignment hawb";
        }
        return $response;
    }
    public static function CreateManifest($validateManifestData,$apiUserData) {
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
            if(count($parcelObj) > 0){
                foreach ($parcelObj as $parcel) {
                    $consignmentIdArr[] = $parcel->getConsignmentId();
                }
                $parcelIdArr[] = $parcel->getId();
            }else{
                $trackingNotFound[] = "Parcel not found ".$validateManifest;
            }
        }
        if(count($trackingNotFound) > 0){
            $response['STATUS'] = "ERROR";
            $response['MESSAGE'] = $trackingNotFound;
            $response['ERRORS'][] = $trackingNotFound;
        }else{
            $consignmentFilter = new \ConsignmentFilter();
            $consignmentFilter->addIdArrayFilter($consignmentIdArr);
            $consignmentRes = $consignmentFilter->getListNew("c.service_id");
            if(count($consignmentRes) > 0){
                foreach ($consignmentRes as $consignment) {
                    $serviceId[] = $consignment->getServiceId();
                }
            }
            $serviceId = array_unique($serviceId);
            if(count($serviceId) > 1){
                $response['STATUS'] = "ERROR";
                $response['MESSAGE'] = "Provided parcels are not belong to same service";
                $response['ERRORS'][] = "Invalid data, Provided parcels are not belong to same service";
            } else {
                $returnData = \Manifest::manifestCreate($parcelIdArr,$serviceId[0],'client',0,$apiUserData->getWarehouseId(),$apiUserData);
                if(isset($returnData['STATUS']) && $returnData['STATUS'] == "Error"){
                    $response['STATUS'] = "ERROR";
                    $response['MESSAGE'] = $returnData['MESSAGE'];
                    $response['ERRORS'][] = $returnData['MESSAGE'];
                }else{
                    $response['STATUS'] = "SUCCESS";
                    $response['MESSAGE'] = $returnData['MESSAGE'];
                    $response['ID'] = $returnData['ID'];
                    $response['PDF_FILE'] = $returnData['PDF_FILE'];
                    $response['CSV_FILE'] = $returnData['CSV_FILE'];
                }
            }
        }
        return $response;
    }
    public static function validateServiceDimension($validatedServiceData, $userData){
        $errosArray['STATUS'] = "SUCCESS";
        $errosArray['MESSAGE'] = "Validate successfully";
        $error_list = [];
        $count = 1;
        $parcelsTotalWeight = 0;
        $dimention = [];
        $parcels = $validatedServiceData['parcel'];
        foreach ($parcels as $parcelArr) {
            if($parcelArr['weight'] < 0 || trim($parcelArr['weight']) == "" || !is_numeric($parcelArr['weight'])) {
                $error_list[] = 'Please enter parcel '.$count.' valid weight';
            }
            if($parcelArr['length'] < 0 || trim($parcelArr['length']) == "" || !is_numeric($parcelArr['length'])) {
                $parcelArr['length'] = 0;
                $error_list[] = 'Please enter parcel '.$count.' valid length';
            }
            if($parcelArr['width'] < 0 || trim($parcelArr['width']) == "" || !is_numeric($parcelArr['width'])) {
                $parcelArr['width'] = 0;
                $error_list[] = 'Please enter parcel '.$count.' valid width';
            }
            if($parcelArr['height'] < 0 || trim($parcelArr['height']) == "" || !is_numeric($parcelArr['height'])) {
                $parcelArr['height'] = 0;
                $error_list[] = 'Please enter parcel '.$count.' valid height';
            }
            $parcelsTotalWeight += $parcelArr['weight'];
            $dimention[] = $parcelArr['length'];
            $dimention[] = $parcelArr['width'];
            $dimention[] = $parcelArr['height'];
            $count++;
        }
        // if we found any error then we will return from here
        if(count($error_list) > 0) {
            $errosArray['STATUS'] = "ERROR";
            $errosArray['MESSAGE'] = "Invalid parcel values";
            $errosArray['ERROR']= $error_list;
            return $errosArray;
        }
        $serviceCode = $validatedServiceData['service_code'];
        $service = new \Services();
        $serviceObj = $service->getServiceByCode($serviceCode);
        $serviceId = "";
        if(!empty($serviceObj)){
            $serviceId = $serviceObj->getId();
        } else {
            $errosArray['STATUS'] = "ERROR";
            $errosArray['MESSAGE'] = "Invalid Service Code";
            $errosArray['ERROR']= "serviceCode $serviceCode is invalid.System cannot find any service for the specified service code";
            return $errosArray;
        }
        // Check if service is custommized
        $customizedServicesRouting = NULL;
        if($serviceObj->getIsCustomized() == "1"){
            $countryFilter = new \CountryFilter();
            $countryFilter->addFieldFilter("iso", $validatedServiceData['receiver_country_iso']);
            $countryObj = $countryFilter->getList();
            $receiverCountryId = "";
            $receiverCountryName = "";
            if(count($countryObj) > 0) {
                $receiverCountryId = $countryObj[0]->getId();
                $receiverCountryName = $countryObj[0]->getName();
            } else {
                $errosArray['STATUS'] = "ERROR";
                $errosArray['MESSAGE'] = "Invalid receiver Country Iso";
                $errosArray['ERROR']= "Receiver country iso is invalid. System cannot find any country for the specified receiver country iso";
                return $errosArray;
            }
            $customizedServicesRoutingFilter = new \CustomizedServicesRoutingFilter();
            $customizedServicesRoutingFilter->addJoin('services s', 's.id=csr.service_id');
            $customizedServicesRoutingFilter->addFieldFilter('country_id', $receiverCountryId);
            $customizedServicesRoutingFilter->addFieldFilter('customize_service_id', $serviceId);
            $customizedServicesRoutingFilter->addFieldFilter('status', '1');
            $customizedServicesRouting = $customizedServicesRoutingFilter->getColumnList("csr.service_id,csr.from_weight,csr.to_weight,s.volumetric_denominator");
            if(count($customizedServicesRouting) > 0){
                $weightNotFound = true;
                foreach($customizedServicesRouting as $customizedServicesRoutingArr){
                    if($customizedServicesRoutingArr->getFromWeight() < $parcelsTotalWeight && $customizedServicesRoutingArr->getToWeight() >= $parcelsTotalWeight ){
                        $weightNotFound = false;
                    }
                }
                if($weightNotFound){
                    $errosArray['STATUS'] = "ERROR";
                    $errosArray['MESSAGE'] = "Weight not allowed for user";
                    $errosArray['ERROR']= "The service ( ".$serviceObj->getName()." ), weight ( ".$parcelsTotalWeight." Kg ) ,  country ( ".$receiverCountryName." ) is not allowed for your account, Please select other service.";//$ErrorString;
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
            if($serviceObj->getValidationType() == "mail"){
                if(trim($serviceDimFormula) != "" && trim($maximumAllowedDimension) != "" && $maximumAllowedDimension > 0) {
                    $findArr = ['L', 'W', 'H'];
                    $repArr = [$parcelArr['length'], $parcelArr['width'], $parcelArr['height']];
                    $serviceDimFormula = str_replace($findArr, $repArr, $serviceDimFormula);
                    eval('$formulaReturn = ' . $serviceDimFormula . ';');
                    if ($formulaReturn > $maximumAllowedDimension) {
                        $errosArray['STATUS'] = "ERROR";
                        $errosArray['MESSAGE'] = "Invalid dimension";
                        $errosArray['ERROR']= "Max dimension ( " . $maximumAllowedDimension . " cm ) of service ( " . $serviceObj->getName() . " ) and your consignment is over size ( " . $formulaReturn . " ) cm";
                        return $errosArray;
                    }
                }
            } else {
                if(!empty($serviceObj->getGirth()) && !empty($serviceGrithFormula)){
                    $findGrithArr = ['height', 'width', 'lenght'];
                    $repGrithArr = [$parcelArr['length'], $parcelArr['width'], $parcelArr['height']];
                    $serviceGrithFormula = str_replace($findGrithArr, $repGrithArr, $serviceGrithFormula);
                    eval('$grithFormulaReturn = ' . $serviceGrithFormula . ';');
                    if ($grithFormulaReturn > $grithValue) {
                        $errosArray['STATUS'] = "ERROR";
                        $errosArray['MESSAGE'] = "Invalid parcel dimension";
                        $errosArray['ERROR']= "Your parcel dimension [ ".$grithFormulaReturn." ] is out of gauge. Service ( " . $serviceObj->getName() . " ) allowed grith value is [ ".$grithValue." ]";
                        return $errosArray;
                    }
                }
                if($parcelArr['length'] > $serviceObj->getMaxLength() || $parcelArr['width'] > $serviceObj->getMaxWidth() || $parcelArr['height'] > $serviceObj->getMaxHeight()){
                    $errosArray['STATUS'] = "ERROR";
                    $errosArray['MESSAGE'] = "Invalid dimension allowed";
                    $errosArray['ERROR']= "Your parcel dimensions are exceeded from maximum value of allowed service dimension";
                    return $errosArray;
                }
            }
        }
        return $errosArray;
    }
    public static function getDropOffLocation($postcode){
        $errosArray['STATUS'] = "SUCCESS";
        $errosArray['MESSAGE'] = "";
        $error_list = [];
        if(empty($postcode)) {
            $error_list[] = "Postcode cannot found";
        }
        // if we found any error then we will return from here
        if(count($error_list) > 0) {
            $errosArray['STATUS'] = "ERROR";
            $errosArray['MESSAGE'] = "Invalid postcode";
            $errosArray['ERROR']= $error_list;
            return $errosArray;
        }
        $ups = new UPS();
        $response = $ups->getDropOffLocation($postcode);
        $errosArray['RESPONSE'] = $response;
        return $errosArray;
    }
    public static function getDriverParcel($userId) {
        $consignmentFilter = new \ConsignmentFilter();
        $returnData = $consignmentFilter->getDriverParcel($userId);
        return $returnData;
    }
    public static function getShipmentStatusStr($code) {
        return \Consignment::$database_status_array[$code];
    }
    public static function getTrackingStatusCode() {
        $data = [];
        $trackingCodes = \Tracking::$oneworld_status_code;
        $trackingDesc = \Tracking::$oneworld_status_desc;
        foreach($trackingCodes as $key => $status){
            $data[$key] = ['status_code' => $key, 'status'=> $status,'description' => $trackingDesc[$key]];
        }
        return $data;
    }
    public function getAccountUsers($user_account_id) {
        $usersData = new \UserFilter();
        $usersData->addFilter('user_account_id = ' . $user_account_id);
        $usersData = $usersData->getColumnList('id');
        $usersIds = '';
        foreach ($usersData as $usersDatum) {
            $usersIds .= $usersDatum->getId() . ',';
        }
        return $usersIds = rtrim($usersIds, ',');
    }
    public static function ValidateShipnmentsDataTypes($shipnment){
        $error = [];
        if(trim($shipnment['service_code']) == "") {
            $error[] = "Please enter valid service code";
        }
        if(trim($shipnment['sender_country_iso']) == "") {
            $error[] = "Please enter valid sender coutry iso";
        }
        if(trim($shipnment['sender_contact']) == "") {
            $error[] = "Please enter valid sender contact";
        }
        if(trim($shipnment['sender_address_line_1']) == "") {
            $error[] = "Please enter valid sender address line 1";
        }
        if(trim($shipnment['sender_city']) == "") {
            $error[] = "Please enter valid sender city";
        }
        if(trim($shipnment['sender_postcode']) == "") {
            $error[] = "Please enter valid sender postcode";
        }
        if(trim($shipnment['receiver_country_iso']) == "") {
            $error[] = "Please enter valid reciver country iso";
        }
        if(trim($shipnment['receiver_contact']) == "") {
            $error[] = "Please enter valid receiver contact";
        }
        if(trim($shipnment['receiver_address_line_1']) == "") {
            $error[] = "Please enter valid receiver address line 1";
        }
        if(trim($shipnment['receiver_city']) == "") {
            $error[] = "Please enter valid receiver city";
        }
        if(trim($shipnment['receiver_postcode']) == "") {
            $error[] = "Please enter valid receiver postcode";
        }
        if(trim($shipnment['description']) == "") {
            $error[] = "Please enter valid description";
        }
        $parcelData = $shipnment['parcel'];
        if(count($parcelData)) {
            foreach($parcelData as $index => $parcel) {
                if(!preg_match("/^\d+(\.\d{1,3})?$/", $parcel['weight']) || $parcel['weight'] == "") {
                    $error[] = "Please enter valid [up to 3 decimal points] parcel ".($index+1)." weight";
                }
                if(!preg_match('/^\d+(\.\d{1,3})?$/', $parcel['length']) || $parcel['length'] == "") {
                    $error[] = "Please enter valid [up to 3 decimal points] parcel ".($index+1)." length";
                }
                if(!preg_match('/^\d+(\.\d{1,3})?$/', $parcel['width']) || $parcel['width'] == "") {
                    $error[] = "Please enter valid [up to 3 decimal points] parcel ".($index+1)." width";
                }
                if(!preg_match('/^\d+(\.\d{1,3})?$/', $parcel['height']) || $parcel['height'] == "") {
                    $error[] = "Please enter valid [up to 3 decimal points] parcel  ".($index+1)." height";
                }
                if(!empty($parcel['itemvalue'])){
                    if(!preg_match('/^\d+(\.\d{1,3})?$/', $parcel['itemvalue'])) {
                        $error[] = "Please enter valid [up to 3 decimal points] parcel ".($index+1)." value";
                    }
                }
            }
        } else {
            $error[] = "Please enter parcel details";
        }
        return $error;
    }

    public static function AddApiLog($type,$requestData,$responseData, $userId){
        if(!is_object($apiData))
            $apiData = new \apiData();
        $apiData->setType($type);
        $apiData->setApiRequest(serialize($requestData));
        $apiData->setApiResponse(serialize($responseData));
        $apiData->setAddedBy($userId);
        $apiData->setDateCreated(date("Y-m-d H:i:s"));
        $apiData->save();
    }

    public static function checkDriverAssign($trackingNo,$userId){
        $returnArray = [];
        $count = 0;
        if(empty($trackingNo)){
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
        if(empty($parcelObjTmp)){
            $returnArray['status'] = "error";
            $returnArray['message'] = "Invalid tracking number";
            $returnArray['driver_assigned'] = 'no';
            $returnArray['driver_name'] = '';
            $returnArray['date_assign'] = '';
            return $returnArray;
            exit;
        }else if($parcelObjTmp[0]->getOweStatusCode() == 22){
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
        $assignVehicleFilter->orderBy('vpm.id',"DESC");
        $assignVehicleObjs = $assignVehicleFilter->getList(['vpm.`driver_id`',"CONCAT(u.`first_name`,' ',u.`last_name`) AS added_by", 'vpm.`date_added`','vpm.`is_active`']);
        $isAssigned = 'no';
        $assignedTo = '';
        $assignedDate = '';
        $isAssignedToOther = 'no';
        if(!empty($assignVehicleObjs)){
            foreach($assignVehicleObjs as $assignVehicleObj){
                if($assignVehicleObj->getIsActive()){
                    $isAssignedToOther = 'other';
                    $isAssigned = 'other';
                    if($assignVehicleObj->getDriverId() == $userId){
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
        }else{
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

}

?>
