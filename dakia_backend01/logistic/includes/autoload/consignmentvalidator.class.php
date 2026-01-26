<?php
/*
 * The following validation has to be applied to a consignment
 * 	1. Self validation, required fields set, etc.
 * 	2. Courier service validation - is choosen service able to deliver to given address.
 *
 *  The service validation is generic functionality.  This class provides
 *  coupling between a consignment (and it's self validation) and the generic
 *  service validation.
 *
 */
class ConsignmentValidator extends DbAccess3
{
    private $consignment = null;
    private $parcel_array = [];
    private $error_list = [];
    private $postcode="";
    private $serviceValues = null;
    private $sessionUser = null;
    private $countryDetail = null;
    private $userObj = null;
    private $userAccountObj = null;
    private $userAccountId = null;
    private $userId = null;
    private $failedBasicValidation = false;

    /*
     * Constant set
     */

    const SERVICE_CODE = 10;
    const ORDER_REFERENCE = 40;
    const REFERENCE = 40;
    const SENDER_CONTACT = 50;
    const SENDER_EMAIL = 45;
    const SENDER_COMPANY = 50;
    const SENDER_NAME = 50;
    const SENDER_ADDRESS_LINE_1 = 255;
    const SENDER_ADDRESS_LINE_2 = 255;
    const SENDER_ADDRESS_LINE_3 = 255;
    const SENDER_CITY = 25;
    const SENDER_STATE = 25;
    const SENDER_POSTCODE = 10;
    const SENDER_TELEPHONE = 17;
    const RECEIVER_EMAIL = 45;
    const RECEIVER_COMPANY = 50;
    const RECEIVER_CONTACT = 50;
    const RECEIVER_ADDRESS_LINE_1 = 255;
    const RECEIVER_ADDRESS_LINE_2 = 255;
    const RECEIVER_ADDRESS_LINE_3 = 255;
    const RECEIVER_CITY = 25;
    const RECEIVER_STATE = 25;
    const RECEIVER_POSTCODE = 10;
    const RECEIVER_TELEPHONE = 17;
    const VALUE = 12;
    const CURRENCY = 3;
    const ITEM_TYPE = 60;
    const DESCRIPTION = 50;
    const TRACKING_NUMBER = 40;
    const IOSSNUMBER = 12;
    const MAX_EORI = 15;
    const MIN_EORI = 12;

    /***
     * Create with consignment to validate
     */
    public function __construct(Consignment $consignment, $parcel=array(),$user="",$userAccount="")
    {
        $this->consignment      =       $consignment;
        $this->parcel_array     =       $parcel;
        $this->serviceValues    =	    new Services($this->consignment->getServiceId());
        $this->sessionUser      =       Sessionmanager::getUser();
        $countryDel             =       new Country($this->consignment->getCountryId());
        $this->countryDetail    =       $countryDel;
        $this->userObj          =       $user;
        $this->userAccountObj   =       $userAccount;        
        // check if user account object is set
        if(!empty($this->userAccountObj) && $this->userAccountObj->getId() > 0){
            $this->userAccountId = $this->userAccountObj->getId();
        }else if(!empty($this->userObj) && $this->userObj->getUserAccountId() > 0){
            $this->userAccountId = $this->userObj->getUserAccountId();
            $this->userAccountObj = new CustomerAccount($this->userObj->getUserAccountId());
        }else if(!empty($this->consignment) && $this->consignment->getUserId() > 0) {
            $this->userObj = new User($this->consignment->getUserId());
            $this->userAccountObj = new CustomerAccount($this->userObj->getUserAccountId());
            $this->userAccountId = $this->userAccountObj->getId();
        }
    }
    public function validateConsignment (){
        $customizedServiceObj = null;

        // Length validations

        if(mb_strlen(trim($this->consignment->getHawb()),'utf8') > self::ORDER_REFERENCE)
            $this->error_list[] = 'Order reference must be less than '.self::ORDER_REFERENCE;

        if(trim($this->consignment->getReference()) != "") {
            if (mb_strlen(trim($this->consignment->getReference()), 'utf8') > self::REFERENCE)
                $this->error_list[] = 'Reference must be less than ' . self::REFERENCE;
        }

        if(mb_strlen(trim($this->consignment->getSenderName()),'utf8') > self::SENDER_CONTACT)
            $this->error_list[] = 'Sender contact must be less than '.self::SENDER_CONTACT;

        if(trim($this->consignment->getSenderEmail()) != "") {
            if (mb_strlen(trim($this->consignment->getSenderEmail()), 'utf8') > self::SENDER_EMAIL)
                $this->error_list[] = 'Sender email must be less than ' . self::SENDER_EMAIL;
        }
        if(trim($this->consignment->getSenderCompany()) != "") {
            if (mb_strlen(trim($this->consignment->getSenderCompany()), 'utf8') > self::SENDER_COMPANY)
                $this->error_list[] = 'Sender company must be less than ' . self::SENDER_COMPANY;
        }
        
        if(mb_strlen(trim($this->consignment->getSenderEmail()),'utf8') > self::SENDER_EMAIL)
            $this->error_list[] = 'Sender email must be less than '.self::SENDER_EMAIL;

        if(mb_strlen(trim($this->consignment->getSenderAddressLine1()),'utf8') > self::SENDER_ADDRESS_LINE_1)
            $this->error_list[] = 'Sender address Line 1 must be less than '.self::SENDER_ADDRESS_LINE_1;
        if(trim($this->consignment->getSenderAddressLine2()) != "") {
            if (mb_strlen(trim($this->consignment->getSenderAddressLine2()), 'utf8') > self::SENDER_ADDRESS_LINE_2)
                $this->error_list[] = 'Sender address Line 2 must be less than ' . self::SENDER_ADDRESS_LINE_2;
        }
        if(trim($this->consignment->getSenderAddressLine3()) != "") {
            if (mb_strlen(trim($this->consignment->getSenderAddressLine3()), 'utf8') > self::SENDER_ADDRESS_LINE_3)
                $this->error_list[] = 'Sender address Line 3 must be less than ' . self::SENDER_ADDRESS_LINE_3;
        }
        if(mb_strlen(trim($this->consignment->getSenderCity()),'utf8') > self::SENDER_CITY)
            $this->error_list[] = 'Sender city must be less than '.self::SENDER_CITY;
        if(trim($this->consignment->getSenderState()) != "") {
            if (mb_strlen(trim($this->consignment->getSenderState()), 'utf8') > self::SENDER_STATE)
                $this->error_list[] = 'Sender state must be less than ' . self::SENDER_STATE;
        }
        if(mb_strlen(trim($this->consignment->getSenderPostcode()),'utf8') > self::SENDER_POSTCODE)
            $this->error_list[] = 'Sender postcode must be less than '.self::SENDER_POSTCODE;
        if(trim($this->consignment->getSenderTelephone()) != "") {
            if (mb_strlen(trim($this->consignment->getSenderTelephone()), 'utf8') > self::SENDER_TELEPHONE)
                $this->error_list[] = 'Sender telephone must be less than ' . self::SENDER_TELEPHONE;
        }

        if(trim($this->consignment->getEmail()) != "") {
            if (mb_strlen(trim($this->consignment->getEmail()), 'utf8') > self::RECEIVER_EMAIL)
                $this->error_list[] = 'Receiver email must be less than ' . self::RECEIVER_EMAIL;
        }
        if(trim($this->consignment->getCompany()) != "") {
            if (mb_strlen(trim($this->consignment->getCompany()), 'utf8') > self::RECEIVER_COMPANY)
                $this->error_list[] = 'Receiver company must be less than ' . self::RECEIVER_COMPANY;
        }
        if(mb_strlen(trim($this->consignment->getContact()),'utf8') > self::RECEIVER_CONTACT)
            $this->error_list[] = 'Receiver contact must be less than '.self::RECEIVER_CONTACT;

        if(mb_strlen(trim($this->consignment->getAddressLine1()),'utf8') > self::RECEIVER_ADDRESS_LINE_1)
            $this->error_list[] = 'Receiver address line 1 must be less than '.self::RECEIVER_ADDRESS_LINE_1;
        if(trim($this->consignment->getAddressLine2()) != "") {
            if (mb_strlen(trim($this->consignment->getAddressLine2()), 'utf8') > self::RECEIVER_ADDRESS_LINE_2)
                $this->error_list[] = 'Receiver address line 2 must be less than ' . self::RECEIVER_ADDRESS_LINE_2;
        }
        if(trim($this->consignment->getAddressLine3()) != "") {
            if (mb_strlen(trim($this->consignment->getAddressLine3()), 'utf8') > self::RECEIVER_ADDRESS_LINE_3)
                $this->error_list[] = 'Receiver address line 3 must be less than ' . self::RECEIVER_ADDRESS_LINE_3;
        }
        if(mb_strlen(trim($this->consignment->getCity()),'utf8') > self::RECEIVER_CITY)
            $this->error_list[] = 'Receiver city must be less than '.self::RECEIVER_CITY;
        if(trim($this->consignment->getState()) != "") {
            if (mb_strlen(trim($this->consignment->getState()), 'utf8') > self::RECEIVER_STATE)
                $this->error_list[] = 'Receiver state must be less than ' . self::RECEIVER_STATE;
        }
        if(mb_strlen(trim($this->consignment->getPostcode()),'utf8') > self::RECEIVER_POSTCODE)
            $this->error_list[] = 'Receiver postcode must be less than '.self::RECEIVER_POSTCODE;
        if(trim($this->consignment->getTelephone()) != "") {
            if (mb_strlen(trim($this->consignment->getTelephone()), 'utf8') > self::RECEIVER_TELEPHONE)
                $this->error_list[] = 'Receiver telephone must be less than ' . self::RECEIVER_TELEPHONE;
        }
        if(trim($this->consignment->getValue()) != "") {
            if (mb_strlen(trim($this->consignment->getValue()), 'utf8') > self::VALUE)
                $this->error_list[] = 'Value must be less than ' . self::VALUE;
        }
        if(trim($this->consignment->getCurrency()) != "") {
            if (mb_strlen(trim($this->consignment->getCurrency()), 'utf8') > self::CURRENCY)
                $this->error_list[] = 'Currency must be less than ' . self::CURRENCY;
        }
        if(trim($this->consignment->getItemType()) != "") {
            if (mb_strlen(trim($this->consignment->getItemType()), 'utf8') > self::ITEM_TYPE)
                $this->error_list[] = 'Item type must be less than ' . self::ITEM_TYPE;
        }
        if(mb_strlen(trim($this->consignment->getDescription()),'utf8') > self::DESCRIPTION)
            $this->error_list[] = 'description must be less than '.self::DESCRIPTION;
        if(trim($this->consignment->getTrackingNumber()) != "") {
            if (mb_strlen(trim($this->consignment->getTrackingNumber()), 'utf8') > self::TRACKING_NUMBER)
                $this->error_list[] = 'Tracking number must be less than ' . self::TRACKING_NUMBER;
        }

        // check form validation
        if(trim($this->consignment->getSenderCountryId()) == "")
            $this->error_list[] = 'Please enter sender country';

        if(trim($this->consignment->getCountryId()) == "")
            $this->error_list[] = 'Please enter receiver country';

        if(trim($this->consignment->getServiceId()) == "" || $this->consignment->getServiceId() == 0){
            $this->error_list[] = 'Please select service';
        }
        
        if(trim($this->consignment->getSenderName()) == "")
            $this->error_list[] = 'Please enter sender contact name';
        
        if(trim($this->consignment->getSenderAddressLine1()) == "")
            $this->error_list[] = 'Please enter sender address 1';

        
        if(trim($this->consignment->getSenderCity()) == "")
            $this->error_list[] = 'Please enter sender city';

        if(trim($this->consignment->getSenderPostcode()) == "")
            $this->error_list[] = 'Please enter sender postcode';


        $senderPostcodeValid = validatePostCode(trim($this->consignment->getSenderCountryId()),trim($this->consignment->getSenderPostcode()));
        if($senderPostcodeValid !== true)
			$this->error_list[] = 'Invalid sender postcode ['.$this->consignment->getSenderPostcode().']. '.$senderPostcodeValid;
        
        if(trim($this->consignment->getContact()) == "")
            $this->error_list[] = 'Please enter receiver contact name';
        
        if(trim($this->serviceValues->getIsEoriRequired()) == "1"){
            if(trim($this->consignment->getEoriNumber()) == "" && ($this->consignment->getCountryId() > 0 && $this->consignment->getSenderCountryId() > 0  && trim($this->consignment->getSenderCountryId()) != trim($this->consignment->getCountryId()))){
                $this->error_list[] = 'Please enter valid EORI number';
            }else{
                // first 2 charter alphabet and minimum 12 digit and maximum 15
               $firstStrEori =  strtolower(substr($this->consignment->getEoriNumber(), 0,2));
                $secondStrEori =  strtolower(substr($this->consignment->getEoriNumber(), 2));
               if(!ctype_alpha($firstStrEori)){
                   $this->error_list[] = "Please enter valid EORI number, it should start with country ISO. i.e [GB111111111111000]";
               }
                if (!ctype_digit($secondStrEori)) {
                    $this->error_list[] = "Please enter valid EORI number, it should have numeric value i.e [GB111111111111000]";
                }
                if (mb_strlen(trim($secondStrEori),'utf8') < self::MIN_EORI || mb_strlen(trim($secondStrEori),'utf8') > self::MAX_EORI) {
                    $this->error_list[] = "Please enter valid EORI number, it should be at least 12 or maximum 15 digit numeric value i.e [GB111111111111000]";
                }
            }
        }
        if(trim($this->consignment->getIossNumber()) != ""){
            // check length minimum should be 12
            if(mb_strlen(trim($this->consignment->getIossNumber()), "utf8") < self::IOSSNUMBER){
                $this->error_list[] = 'Ioss number cannot be less then ' . self::IOSSNUMBER;
            }
        }
        if(trim($this->consignment->getAddressLine1()) == "")
            $this->error_list[] = 'Please enter receiver address 1';
        
        if(trim($this->consignment->getCity()) == "")
            $this->error_list[] = 'Please enter receiver city';
        
        if(trim($this->consignment->getPostcode()) == "")
            $this->error_list[] = 'Please enter receiver postcode';

		$receiverPostcodeValid = validatePostCode(trim($this->consignment->getCountryId()),trim($this->consignment->getPostcode()));
		if($receiverPostcodeValid !== true)
			$this->error_list[] = 'Invalid receiver postcode ['.$this->consignment->getPostcode().']. '.$receiverPostcodeValid;

        if(trim($this->consignment->getDescription()) == "")
            $this->error_list[] = 'Please enter description';
        
        if(trim($this->consignment->getEmail()) != ""){
           if (!filter_var($this->consignment->getEmail(), FILTER_VALIDATE_EMAIL))
                $this->error_list[] = 'Please enter valid receiver email address';
        }
        if(trim($this->consignment->getHawb()) == "")
            $this->error_list[] = 'Please enter order reference';
        if(trim($this->consignment->getSenderEmail()) != ""){
            if (!filter_var($this->consignment->getSenderEmail(), FILTER_VALIDATE_EMAIL))
                $this->error_list[] = 'Please enter valid sender email address';
        }
        /*
        *  Address limit  validation start
        */
        if(!empty($this->consignment->getServiceId()) && $this->consignment->getServiceId() > 0){
            $customizedServiceObj = $this->serviceValues;
            $limitForAddresss = $this->serviceValues->getCarrierAddressLimit();
            if (mb_strlen($this->consignment->getAddressLine1(), 'utf8') > $limitForAddresss)
                $this->error_list[] = "Address Line 1 must be less than " . $limitForAddresss . " characters";
            if (mb_strlen($this->consignment->getAddressLine2(), 'utf8') > $limitForAddresss)
               $this->error_list[]  = " Address Line 2 must be less than " . $limitForAddresss . " characters";
            if (mb_strlen($this->consignment->getAddressLine3(), 'utf8') > $limitForAddresss)
                $this->error_list[] = " Address Line 3 must be less than " . $limitForAddresss . " characters";
        }

        $count = 1;
        $parcelsTotalWeight = 0;
        $dimention = [];
        $parcelCount = count($this->parcel_array);
        foreach ($this->parcel_array as $parcelArr) {
            if($parcelCount > 1 && !empty($this->consignment->getAwb())) {
                if(trim($parcelArr['tracking_number']) == ""){
                    $this->error_list[] = 'Please enter parcel '.$count.' tracking number';
                }
            }else if($parcelCount == 1 && trim($parcelArr['tracking_number']) == "" && !empty($this->consignment->getAwb())){
                $parcelArr['tracking_number']  = $this->consignment->getAwb();
            }
            if($parcelArr['weight'] < 0 || trim($parcelArr['weight']) == "" || !is_numeric($parcelArr['weight']))
                $this->error_list[] = 'Please enter parcel '.$count.' valid weight';
            
            if($parcelArr['length'] < 0 || trim($parcelArr['length']) == "" || !is_numeric($parcelArr['length']))
                $parcelArr['length'] = 0;
                //$this->error_list[] = 'Please enter parcel '.$count.' valid length';
            
            if($parcelArr['width'] < 0 || trim($parcelArr['width']) == "" || !is_numeric($parcelArr['width']))
                $parcelArr['width'] = 0;
                //$this->error_list[] = 'Please enter parcel '.$count.' valid width';
            
            if($parcelArr['height'] < 0 || trim($parcelArr['height']) == "" || !is_numeric($parcelArr['height']))
                $parcelArr['height'] = 0;
                //$this->error_list[] = 'Please enter parcel '.$count.' valid height';
            
            $parcelsTotalWeight += $parcelArr['weight'];
            $dimention[] = $parcelArr['length'];
            $dimention[] = $parcelArr['width'];
            $dimention[] = $parcelArr['height'];
            $count++;

            $contentsData = $parcelArr['items'];
           /* if (count($contentsData)) {
                foreach ($contentsData as $indexc => $contents) {
                    if (!preg_match("/^\d+(\.\d{1,3})?$/", $contents['no_of_items']) || $contents['no_of_items'] == "") {
                        $this->error_list[] = "Please enter valid [up to 3 decimal points] parcel " . ($index + 1) . " contents ".($indexc+1)." no_of_items";
                    }
                    if (!preg_match("/^\d+(\.\d{1,3})?$/", $contents['item_value']) || $contents['item_value'] == "") {
                        $this->error_list[] = "Please enter valid [up to 3 decimal points] parcel " . ($index + 1) . " contents ".($indexc+1)." item_value";
                    }
                    if (!preg_match("/^\d+(\.\d{1,3})?$/", $contents['weight']) || $contents['weight'] == "") {
                        $this->error_list[] = "Please enter valid [up to 3 decimal points] parcel " . ($index + 1) . " contents ".($indexc+1)." weight";
                    }

                    if (strlen($contents['manufacture_country_iso'])!=2) {
                        $this->error_list[] = "Please enter valid 2 character iso code parcel " . ($index + 1) . " contents ".($indexc+1)." manufacture_country_iso";
                    }
                }
            }*/
        }
        $maxDim = max($dimention);
//      its just assignment        
        $this->consignment->setWeight($parcelsTotalWeight);
//      End its just assignment        
        if(trim($this->consignment->getWeight()) < 0 || trim($this->consignment->getWeight()) == "")
            $this->error_list[] = 'Please enter valid item weight';
        
        // Check if service is allowed to account
        $allowedServiceArr = [];
        $isDeadWeightArr = [];
        $isOverSizeArr = [];
//        $this->consignment->getSenderCountryId()
//        $this->consignment->getCountryId() reci
        $allowedServiceReturn = ServiceFilter::getUserAccountServices($this->userAccountId, $this->consignment->getSenderCountryId(), $this->consignment->getCountryId());
        if(count($allowedServiceReturn) > 0){
            foreach ($allowedServiceReturn as $allowedService) {
                $allowedServiceArr[]    = $allowedService->getId();
                $isDeadWeightArr[$allowedService->getId()]      = $allowedService->getIsdeadWeight();
                $isOverSizeArr[$allowedService->getId()]      = $allowedService->getIsOverSize();
            }
        }
        if(!in_array($this->consignment->getServiceId(), $allowedServiceArr)){
            $this->error_list[] = 'Service['.$this->serviceValues->getName().'] is not allowed to your account';
        }
        // if we found any error then we will return from here
        if(count($this->error_list) > 0) {
            $this->failedBasicValidation = true;
            return false;
        }
        
        $this->consignment->setisDeadWeightChargable($isDeadWeightArr[($this->consignment->getCustomizedServiceId()<= 0 ? $this->consignment->getServiceId() : $this->consignment->getCustomizedServiceId())]);
        $this->consignment->setIsOverSizeChargable($isOverSizeArr[($this->consignment->getCustomizedServiceId()<= 0 ? $this->consignment->getServiceId() : $this->consignment->getCustomizedServiceId())]);
     
        // Check if service is custommized
        $customizedServicesRouting = NULL;
        if($this->serviceValues->getIsCustomized() == "1"){
            $customizedServicesRoutingFilter = new CustomizedServicesRoutingFilter();
            $customizedServicesRoutingFilter->addJoin('services s', 's.id=csr.service_id');
            $customizedServicesRoutingFilter->addFieldFilter('country_id', $this->consignment->getCountryId());
            $customizedServicesRoutingFilter->addFieldFilter('customize_service_id', $this->consignment->getServiceId());
            $customizedServicesRoutingFilter->addFieldFilter('status', '1');
            $customizedServicesRouting = $customizedServicesRoutingFilter->getColumnList("csr.service_id,csr.from_weight,csr.to_weight,s.volumetric_denominator");
            if(count($customizedServicesRouting) > 0){
                $weightNotFound = true;
                foreach($customizedServicesRouting as $customizedServicesRoutingArr){
                    if($customizedServicesRoutingArr->getFromWeight() < $parcelsTotalWeight && $customizedServicesRoutingArr->getToWeight() >= $parcelsTotalWeight ){
                        $this->consignment->setCustomizedServiceId($this->consignment->getServiceId());
                        $this->consignment->setServiceId($customizedServicesRoutingArr->getServiceId());
                        
                        $this->consignment->setVolDemonimator($customizedServicesRoutingArr->getVolumetricDenominator());
                        $this->serviceValues = new Services($customizedServicesRoutingArr->getServiceId());
                        $this->consignment->setRoutingCode('PR');
                        $weightNotFound = false;
                        break;
                    }
                }
                if($weightNotFound){
                    $this->error_list[] = "The service ( ".$this->serviceValues->getName()." ), weight ( ".$parcelsTotalWeight." Kg ) ,  country ( ".$this->countryDetail->getName()." ) is not allowed for your account, Please select other service.";//$ErrorString;
                    $this->consignment->setMessage(implode("<br>", $this->error_list));
                    return false;
                }
            }else{
                $this->consignment->setVolDemonimator(5000);
            }
        }else{
            $this->consignment->setVolDemonimator($this->serviceValues->getVolumetricDenominator());
            $this->consignment->setRoutingCode('S');
            $this->consignment->setCustomizedServiceId('NULL');
        }
        // If you are here it means that form is valid
        // Now implement logical validation
        // Check if user or user account didn't found
        if(empty($this->userObj->getId()) || $this->userObj->getId() <= 0){
            $this->error_list[] = formatMessages(ERROR_NO_USER);
            return false;
        }
        if(empty($this->userAccountObj->getId()) || $this->userAccountObj->getId() <= 0){
            $this->error_list[] = formatMessages(ERROR_NO_ACCOUNT);
            return false;
        }
        // Check if hawb id empty or not unique
        $conWhere = '';
        if($this->consignment->getId() > 0){
            $conWhere = " AND id!='".DbAccess3::escape($this->consignment->getId())."'";
        }
        $queryData  =   " SELECT "
                . " count(hawb) as total "
                . " FROM "
                . " consignment "
                . " WHERE "
                . "     hawb = '".trim($this->consignment->getHawb())."' "
                . "     AND id <> 0 AND shipment_status !=".Consignment::STATUS_RECYCLED." ".$conWhere;
        $consignmenthawbCheck = Consignment::getTotalNumberOfConsignmentsFromSql($queryData);
        if($consignmenthawbCheck > 0) {
            // Check if this shipment is for same account and have invalid status then change status to recycle let
//            the process go on else show him error irshad sir 26-02-2021 (Hadi Syed)
//            $dataRtn = Consignment::checkHawbSameAccountExsit(trim($this->consignment->getHawb()),$this->userAccountObj->getId());
//            if(count($dataRtn) > 0){
//                // change status to recycle
//                Consignment::consignmentStatusUpdate($dataRtn['id'],11,'Same Hawb number exist in DB');
//            }else{
                $this->error_list[] = formatMessages(ERROR_HAWB_EXIST). "(".DbAccess3::escape(trim($this->consignment->getHawb())).")";
                return false;
//            }
        }
//        Its just assignment
        if(trim($this->consignment->getWeightType()) == '') {
            if($this->serviceValues->getWieghtType() == 'PARCEL' || $this->serviceValues->getWieghtType() == '1')
                $this->consignment->setWeightType('PP');
            else
                $this->consignment->setWeightType('PS');
        }
        $totalvolweight = 0;
//        $maxvolweight = $this->serviceValues->getMaxVolumetricWeight();
        $maxvolweight = 0;
        $weightToSend = 0;
        $vol_weight = 0;
        $volweight = 0;
//        $maximumAllowedDimension = [];
        $maxParcelWeight = 0;  
        foreach ($this->parcel_array as $parcelArr) {
            
            if(empty($parcelArr['length']) || strtolower($parcelArr['length']) == "null" || $parcelArr['length'] == NULL)
                $parcelArr['length'] = 0;
            if(empty($parcelArr['width']) || strtolower($parcelArr['width']) == "null" || $parcelArr['width'] == NULL)
                $parcelArr['width'] = 0;
            if(empty($parcelArr['height']) || strtolower($parcelArr['height']) == "null" || $parcelArr['height'] == NULL)
                $parcelArr['height'] = 0;
            
            $maximumAllowedDimension = $this->serviceValues->getMaximumAllowedDimension();
            $serviceDimFormula = $this->serviceValues->getMaximumDimFormula();
            $serviceGrithFormula = $this->serviceValues->getGirthFormula();
            $grithValue = $this->serviceValues->getGirth();
            $formulaReturn = 0;
            $grithFormulaReturn = 0;
            if($this->serviceValues->getValidationType() == "mail"){
                if(trim($serviceDimFormula) != "" && trim($maximumAllowedDimension) != "" && $maximumAllowedDimension > 0) {
                    $findArr = ['L', 'W', 'H'];
                    $repArr = [$parcelArr['length'], $parcelArr['width'], $parcelArr['height']];
                    $serviceDimFormula = str_replace($findArr, $repArr, $serviceDimFormula);
                    eval('$formulaReturn = ' . $serviceDimFormula . ';');
                    if($isOverSizeArr[($this->consignment->getCustomizedServiceId()<= 0 ? $this->consignment->getServiceId() : $this->consignment->getCustomizedServiceId())] <= 0){
                        if ($formulaReturn > $maximumAllowedDimension) {
                            $this->error_list[] = "Max dimension ( " . $maximumAllowedDimension . " cm ) of service ( " . $customizedServiceObj->getName() . " ) and your consignment is over size ( " . $formulaReturn . " ) cm";
                            $this->consignment->setMessage(implode("<br>", $this->error_list));
                            return false;
                        }
                    }
                }
            }else{
                 if($isOverSizeArr[($this->consignment->getCustomizedServiceId()<= 0 ? $this->consignment->getServiceId() : $this->consignment->getCustomizedServiceId())] <= 0){
                if(!empty($this->serviceValues->getGirth()) && !empty($serviceGrithFormula)){
                        $findGrithArr = ['height', 'width', 'lenght'];
                        $repGrithArr = [
                        (is_numeric($parcelArr['length'])?$parcelArr['length']:1 ), 
                        (is_numeric($parcelArr['width'])?$parcelArr['width']:1 ), 
                        (is_numeric($parcelArr['height'])?$parcelArr['height']:1 )];
                        $serviceGrithFormula = str_replace($findGrithArr, $repGrithArr, $serviceGrithFormula);
                        eval('$grithFormulaReturn = ' . $serviceGrithFormula . ';');
                        if ($grithFormulaReturn > $grithValue) {
                            $this->error_list[] = "Your parcel dimension [ ".$grithFormulaReturn." ] is out of gauge. Service ( " . $customizedServiceObj->getName() . " ) allowed grith value is [ ".$grithValue." ]";
                            $this->consignment->setMessage(implode("<br>", $this->error_list));
                            return false;
                        }
                    }
                   // echo "<pre>";
//                    print_r($isOverSizeArr);
                    //echo $this->consignment->getServiceId();
                    //echo $this->consignment->getCustomizedServiceId();
                    
                    //echo $isOverSizeArr[$this->consignment->getServiceId()];
                    //die;
                    
                        if($parcelArr['length'] > $this->serviceValues->getMaxLength() || $parcelArr['width'] > $this->serviceValues->getMaxWidth() || $parcelArr['height'] > $this->serviceValues->getMaxHeight()){
                            $this->error_list[] = "Your parcel dimensions are exceeded from maximum value of allowed service dimension";
                            $this->consignment->setMessage(implode("<br>", $this->error_list));
                            return false;
                        }
                    }
                    if(!empty($this->consignment->getVolDemonimator()) && $this->consignment->getVolDemonimator() > 0){
                        $volweight = (($parcelArr['width'] * $parcelArr['height'] * $parcelArr['length']) / $this->consignment->getVolDemonimator());
                        if($maxvolweight < $volweight)
                            $maxvolweight = $volweight;
                        $totalvolweight = $totalvolweight + $volweight;
                    }
            }
            // Calcualte max weight of parcel
            if ($maxParcelWeight < $parcelArr['weight']) {
                $maxParcelWeight = $parcelArr['weight'];
            }
        }
        if(trim($this->consignment->getWeightType()) == 'PP') {
            $weightToSend = $maxParcelWeight;
            $vol_weight = $maxvolweight;
        }else{
            $weightToSend = $this->consignment->getWeight();
            $vol_weight = $totalvolweight;
        }
        if(!empty($this->consignment->getVolDemonimator()) && $this->consignment->getVolDemonimator() > 0){
            if ($vol_weight > $weightToSend) {
                $weightToSend = $vol_weight;
            }
        }
        if($this->consignment->getisDeadWeightChargable() == 1)
        {
            $weightToSend = $this->consignment->getWeight();
        }
//        Its just assignment
            $this->consignment->setChargeWeight($weightToSend);
            $this->consignment->setVolWeight($vol_weight);
            $this->consignment->setRoutingCode('S');
//        End Its just assignment
        
        // Check if service is agreed
        $userServicesRoutingFilter = new UserServicesRoutingFilter();
        $userServicesRoutingFilter->addFieldFilter("user_account_id", $this->userAccountObj->getId());
        if($customizedServiceObj->getIsCustomized() == "0"){
            $userServicesRoutingFilter->addFieldFilter("country_id", $this->consignment->getCountryId());
        }
        if($this->consignment->getCustomizedServiceId() == 0 || $this->consignment->getCustomizedServiceId() == '')
            $userServicesRoutingFilter->addFieldFilter("service_id", $this->consignment->getServiceId());
        else
            $userServicesRoutingFilter->addFieldFilter("service_id", $this->consignment->getCustomizedServiceId());

        $userServicesRouting = $userServicesRoutingFilter->getColumnList("is_agreed,from_weight,to_weight,is_over_label");
        if(count($userServicesRouting) > 0){
            $this->consignment->setIsWhiteLabel($userServicesRouting[0]->getIsOverLabel());
            if($userServicesRouting[0]->getIsAgreed() == 0){
                $this->error_list[] = "Please accept term of use from carrier setup section before using this service.";
                $this->consignment->setMessage(implode("<br>", $this->error_list));
                return false;
            }
            if($this->consignment->getisDeadWeightChargable() != 1 && !empty($this->consignment->getVolDemonimator()) && $this->consignment->getVolDemonimator() > 0){
                // If weight to send is less then vol wieght of service
                if($weightToSend > $this->serviceValues->getMaxVolumetricWeight()){
                    $this->error_list[] = "Max vol weight ( ".$customizedServiceObj->getMaxVolumetricWeight()." Kg ) of service ( ".$customizedServiceObj->getName()." ) and your consignment is over weight ( ".$weightToSend." ) Kg";
                    $this->consignment->setMessage(implode("<br>", $this->error_list));
                    return false;
                }
            }
            // get all parent
            $parentAccount = CustomerAccount::accountParentAccount($this->userAccountObj->getId());
            foreach ($parentAccount as $parentAccountArr) {
                $isAgentSet = false;
                $isOwnContract = checkOwnContract($parentAccountArr->getId(),$this->consignment->getServiceId());
                if($isOwnContract){
                    // Own contract set it's own agent
                    $agentDataFilter = new AgentDataFilter();
                    $agentDataFilter->addFieldFilter("user_id", $this->userAccountObj->getId());
    //                $agentDataFilter->
                    $serviceAgentMappingDataFilter = new ServiceAgentMappingDataFilter();
                    $serviceAgentMappingDataFilter->addAgentJoin(" AND ad.`user_id` = '".$parentAccountArr->getId()."' and ad.active = 1 ");
                    $serviceAgentMappingDataFilter->addServiceIDFilter($this->consignment->getServiceId());
                    $serviceAgentMappingDataFilter->addFilter("m.from_weight < '".DbAccess3::escape($weightToSend)."'");
                    $serviceAgentMappingDataFilter->addFilter("m.to_weight >= '". DbAccess3::escape($weightToSend)."'");
                    $serviceAgentMappingDataObj = $serviceAgentMappingDataFilter->getColumnList("m.agentid");
                    if(count($serviceAgentMappingDataObj) > 0){
                        $ownAgentId = $serviceAgentMappingDataObj[0]->getAgentid();
                        $this->consignment->setAgentId($ownAgentId);
                        $isAgentSet = true;
                        break;
                    }
                }
            }
            
            if(($this->consignment->getCustomizedServiceId() == 0 || $this->consignment->getCustomizedServiceId() == '') && !$isAgentSet){
                if($userServicesRouting[0]->getFromWeight() < $weightToSend && $userServicesRouting[0]->getToWeight() >= $weightToSend ){
                    // Get service agent
                    $carrierServiceCustomizeRulesFilter = new carrierServiceCustomizeRulesFilter();
                    $carrierServiceCustomizeRulesFilter->addFilter("serviceid = ".$this->consignment->getServiceId());
                    $carrierServiceCustomizeRulesFilter->addFilter("user_account_id = ".$this->userAccountObj->getId());
                    /*$carrierServiceCustomizeRulesFilter->addFilter("from_weight <= ".$weightToSend);
                    $carrierServiceCustomizeRulesFilter->addFilter("to_weight >= ".$weightToSend);*/
                    $carrierServiceCustomizeRulesFilter->addFilter("status = 1");
                    $carrierServiceCustomizeRules = $carrierServiceCustomizeRulesFilter->getColumnList("agentid,from_weight,to_weight");
                    if(count($carrierServiceCustomizeRules) > 0){
                        $weightFound = false;
                        foreach($carrierServiceCustomizeRules as $carrierServiceCustomizeRule){
                            if($carrierServiceCustomizeRule->getFromWeight() < $weightToSend && $carrierServiceCustomizeRule->getToWeight() >= $weightToSend ){
                                $this->consignment->setAgentId($carrierServiceCustomizeRule->getAgentid());
                                $weightFound = true;
                                break;
                            }
                        }
                        if($weightFound === false){
                            $this->error_list[] = "Weight ( ".$weightToSend." Kg ) is not allowed for agent of Service ( ".$customizedServiceObj->getName()." )";
                            $this->consignment->setMessage(implode("<br>", $this->error_list));
                            return false;
                        }
                    }else{
                        $carrierServiceDefaultRulesFilter = new carrierServiceDefaultRulesFilter();
                        $carrierServiceDefaultRulesFilter->addFilter("serviceid = ".$this->consignment->getServiceId());
                        /*$carrierServiceDefaultRulesFilter->addFilter("from_weight <= ".$weightToSend);
                        $carrierServiceDefaultRulesFilter->addFilter("to_weight >= ".$weightToSend);*/
                        $carrierServiceDefaultRulesFilter->addFilter("agent_type = 'outbound'");
                        $carrierServiceDefaultRules = $carrierServiceDefaultRulesFilter->getColumnList("agentid,from_weight,to_weight");
                        if(count($carrierServiceDefaultRules) > 0){
                            $weightFound = false;
                            foreach($carrierServiceDefaultRules as $carrierServiceDefaultRule){
                                if($carrierServiceDefaultRule->getFromWeight() < $weightToSend && $carrierServiceDefaultRule->getToWeight() >= $weightToSend ){
                                    $this->consignment->setAgentId($carrierServiceDefaultRule->getAgentid());
                                    $weightFound = true;
                                    break;
                                }
                            }
                            if($weightFound === false){
                                $this->error_list[] = "Weight ( ".$weightToSend." Kg ) is not allowed for agent of Service ( ".$customizedServiceObj->getName()." )";
                                $this->consignment->setMessage(implode("<br>", $this->error_list));
                                return false;
                            }
                        }else{
                            $this->error_list[] = "Agent not assigned to Service ( ".$customizedServiceObj->getName()." )";
                            $this->consignment->setMessage(implode("<br>", $this->error_list));
                            return false;
                        }
                    }
                    $this->consignment->setCustomizedServiceId("");
                }else{
                    $this->error_list[] 	=	"The service ( ".$customizedServiceObj->getName()." ), weight ( ".$weightToSend." Kg ) ,  country ( ".$this->countryDetail->getName()." ) is not allowed for your account, Please select other service.";//$ErrorString;
                    $this->consignment->setMessage(implode("<br>", $this->error_list));
                    return false;
                }
            }else if(!$isAgentSet){
                    // If service is customized
                    if(count($customizedServicesRouting) > 0){
                        $weightFoundCustomized = false;
                        foreach($customizedServicesRouting as $customizedServicesRoutingArr){
                            if($customizedServicesRoutingArr->getFromWeight() < $weightToSend && $customizedServicesRoutingArr->getToWeight() >= $weightToSend ){
                                // Doublicate this code because we need customized service agent
                                $carrierServiceCustomizeRulesFilter = new carrierServiceCustomizeRulesFilter();
                                $carrierServiceCustomizeRulesFilter->addFilter("serviceid = ".$this->consignment->getServiceId());
                                $carrierServiceCustomizeRulesFilter->addFilter("user_account_id = ".$this->userAccountObj->getId());
                                $carrierServiceCustomizeRulesFilter->addFilter("from_weight < ".$weightToSend);
                                $carrierServiceCustomizeRulesFilter->addFilter("to_weight >= ".$weightToSend);
                                $carrierServiceCustomizeRulesFilter->addFilter("status = 1");
                                $carrierServiceCustomizeRules = $carrierServiceCustomizeRulesFilter->getColumnList("agentid");
                                if(count($carrierServiceCustomizeRules) > 0){
                                    $this->consignment->setAgentId($carrierServiceCustomizeRules[0]->getAgentid());
                                }else{
                                    $carrierServiceDefaultRulesFilter = new carrierServiceDefaultRulesFilter();
                                    $carrierServiceDefaultRulesFilter->addFilter("serviceid = ".$this->consignment->getServiceId());
                                    $carrierServiceDefaultRulesFilter->addFilter("from_weight < ".$weightToSend);
                                    $carrierServiceDefaultRulesFilter->addFilter("to_weight >= ".$weightToSend);
                                    $carrierServiceDefaultRulesFilter->addFilter("agent_type = 'outbound'");
                                    $carrierServiceDefaultRules = $carrierServiceDefaultRulesFilter->getColumnList("agentid");
                                    if(count($carrierServiceDefaultRules) > 0){
                                        $this->consignment->setAgentId($carrierServiceDefaultRules[0]->getAgentid());
                                    }else{
                                        $this->error_list[] = "Agent not assigned to Service ( ".$customizedServiceObj->getName()." ) for ".$weightToSend." kg";
                                        $this->consignment->setMessage(implode("<br>", $this->error_list));
                                        return false;
                                    }
                                }
                                $weightFoundCustomized = true;
                                break;
                            }
                        }
                        if($weightFoundCustomized === false){
                            $this->error_list[] 	=	"The service ( ".$customizedServiceObj->getName()." ), weight ( ".$weightToSend." Kg ) ,  country ( ".$this->countryDetail->getName()." ) is not allowed for your account, Please select other service.";//$ErrorString;
                            $this->consignment->setMessage(implode("<br>", $this->error_list));
                            return false;
                        }
                    }else{
                        $country = new Country($this->consignment->getCountryId());
                        $errorMsg = "Account (".$this->userAccountObj->getUserAccount().") do not have Service (".$customizedServiceObj->getName().") assigned to Country  (".$country->getName().")";
                        $this->error_list[] = $errorMsg;
                        $this->consignment->setMessage(implode("<br>", $this->error_list));
                        return false;
                    }

                }
        }else{
            $country = new Country($this->consignment->getCountryId());
            $errorMsg = "Account (".$this->userAccountObj->getUserAccount().") do not have Service (".$customizedServiceObj->getName().") assigned to Country (".$country->getName().")";
            $this->error_list[] = $errorMsg;
            $this->consignment->setMessage(implode("<br />", $this->error_list));
            return false;
        }
        $className    =   Consignment::IncludeCarrierClass($this->consignment);
        if($className !== false) {
            $carrierValidation = new $className(); //  Calling the constructor
            $resultsResponse = $carrierValidation->validation($this->consignment, $this->serviceValues, $this->countryDetail );
            if(method_exists($carrierValidation, 'getRoutingCode')){
                $routingCode = $carrierValidation->getRoutingCode();
                $this->consignment->setRoutingCode($routingCode);
            }
            if(method_exists($carrierValidation, 'isRemoteArea')){
                $isRemoteArea = $carrierValidation->isRemoteArea();
                $this->consignment->setRemoteCharges($isRemoteArea);
            }
            if(!empty($resultsResponse)) {
                $this->error_list = $resultsResponse;
                $this->consignment->setMessage(implode("<br />", $resultsResponse));
                return false;
            }
            
//            // Label Charges Variable
//            $tariffChargesArr = [];
//            $tariffCharges = [];
//            $consignemntLabelCharges = [];
//            // Check if account have balance if not then return from here with error message
//            $accountPaymentArr = checkBalance($this->userAccountId);
//            $userAccountArr = CustomerAccount::accountParentAccount($this->userAccountId);
//            /*
//            * Pass consignment id
//            * Return Maximum column value from that consignment's parcel (lenght, widht , height)
//            */
//            if(count($userAccountArr) > 0){
//                foreach ($userAccountArr as $userAccountIdArr) {
//                    $curUserAccountId = $userAccountIdArr->getId();
//                    $curUserAccountParrentId = $userAccountIdArr->getParentid();
//                    if($curUserAccountParrentId > 0){
//                        $tariffCharges = Tariffs::getUserQuotationsByAssignedServices($curUserAccountId, $this->consignment->getSenderCountryId(), $this->consignment->getCountryId(), $this->consignment->getSenderPostcode(), $this->consignment->getPostcode(), $this->consignment->getSenderCity(), $this->consignment->getCity(), $this->consignment->getWeight(), $this->consignment->getNumberPieces(), $this->consignment->getServiceId(),0,$maxDim);
//                        if($curUserAccountId == $this->userAccountId){
//                           $consignemntLabelCharges = $tariffCharges;
//                        }
//                        $tariffChargesArr[] = isset($tariffCharges['QUOTATIONS'][0]['TOTAL']) ? $tariffCharges['QUOTATIONS'][0]['TOTAL'] : 0;
//                    }
//                }
//            }
//            if(in_array(0, $tariffChargesArr)){
//                $this->error_list[] = "Label can not be generated. Tariff not found";
//                return false;
//            }
//            if (!is_array($accountPaymentArr) || empty($accountPaymentArr)) {
//                $accountPaymentArr[] = 0;
//            }
//            if (isset($consignemntLabelCharges['STATUS']) && $consignemntLabelCharges['STATUS'] == "SUCCESS") {
//                if (isset($consignemntLabelCharges['QUOTATIONS'][0]['TOTAL']) && min($accountPaymentArr) >= $consignemntLabelCharges['QUOTATIONS'][0]['TOTAL'] && min($accountPaymentArr) > 0) {
//                } else if (isset($consignemntLabelCharges['QUOTATIONS'][0]['TOTAL']) && $consignemntLabelCharges['QUOTATIONS'][0]['TOTAL'] == 0) {
//                    $this->error_list[] = "Label can not be generated. Tariff not found";
//                    return false;
//                } else {
//                    $this->error_list[] = "Credit is not available. Please top up your account or contact Admin";
//                    return false;
//                }
//            } else if (isset($consignemntLabelCharges['STATUS']) && $consignemntLabelCharges['STATUS'] == "ERROR") {
//                $this->error_list[] = $consignemntLabelCharges['MESSAGE'];
//                return false;
//            } else {
//                $this->error_list[] = "Label can not be generated. Please contact to administrator at info@smarttrack.com";
//                return false;
//            }
            return true;
        } else {
            $this->error_list[] = "No carrier class found";
            $this->consignment->setMessage("No carrier class found");
            return false;
        }
    }
        
    /***
     * List of errors
     */
    public function getErrorList() {
            return $this->error_list;
    }
    public function isBasicValidationFailed() {
            return $this->failedBasicValidation;
    }
    public function getConsignment() {
		return $this->consignment;
	}
}
