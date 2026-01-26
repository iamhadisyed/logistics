<?php
class UserServicesCharges extends DbAccess3 {

    public function __construct($mixedCreator = null) {
        $fieldList = array
            (
            'id' => 'number',
            'user_account_id' => 'number',
            'service_id' => 'string',
            'sur_charge' => 'number',
            'sur_charge_type' => 'string',
            'extra_charge' => 'number',
            'extra_charge_type' => 'string',
            'discount' => 'number',
            'discount_type' => 'string',
            'over_weight' => 'number',
            'over_weight_type' => 'string',
            'over_size' => 'number',
            'over_size_type' => 'string',
            'discount' => 'number',
            'discount_type' => 'string',
            'additional_charges_type' => 'string',
            'additional_charges' => 'number',
            'additional_charges_details' => 'string',
            'last_updated' => 'datetime'            
        );
        //
        parent::__construct("user_services_charges", 'id', $fieldList, $mixedCreator);
    }
	
	public static function extraDetailsCharges()
	{
		//清关/Clearance（GBP/KG)	Import Handing Charge（GBP/KG)	UK Local truck（GBP/KG)	UK OPT（GBP/KG)	Truck  To Destination（GBP/KG)
		$arrayFields							=	array();	
		$arrayFields['clearance']				=	'Clearance';
		$arrayFields['import_handing_charge']	=	'Import Handing Charge';
		$arrayFields['uk_local_truck']			=	'UK Local truck';
		$arrayFields['uk_opt']					=	'UK OPT';
		$arrayFields['truck_to_destination']	=	'Truck  To Destination';

		
		return $arrayFields;
	}

    public function getUserId() {
        return $this->valArray["user_id"];
    }

    public function getServiceId() {
        return $this->valArray["service_id"];
    }

    public function getSurCharge() {
        return $this->valArray["sur_charge"];
    }
    public function getSurChargeType() {
        return $this->valArray["sur_charge_type"];
    }

    public function getExtraCharge() {
        return $this->valArray["extra_charge"];
    }
    public function getExtraChargeType() {
        return $this->valArray["extra_charge_type"];
    }
    
    public function getDiscount() {
        return $this->valArray["discount"];
    }
    public function getDiscountType() {
        return $this->valArray["discount_type"];
    }

    public function getLastUpdated() {
        return $this->valArray["last_updated"];
    }

    public function getId() {
        return $this->valArray["id"];
    }

    /**
     * Get list of user objects, using sql given
     *
     * @param string $sql
     */
    public static function getUserServicesChargesListFromSql($sql) {
        return DbAccess3::getListFromSql(__CLASS__, $sql);
    }

    public function setUserId($flag) {
        $this->valArray["user_id"] = $flag; //($flag ? 1 : 0);
        $this->modifyArray["user_id"] = 'user_id';
    }

    public function setServiceId($flag) {
        $this->valArray["service_id"] = $flag;
        $this->modifyArray["service_id"] = 'service_id';
    }

    public function setSurCharge($flag) {
        $this->valArray["sur_charge"] = $flag;
        $this->modifyArray["sur_charge"] = 'sur_charge';
    }
    public function setSurChargeType($flag) {
        $this->valArray["sur_charge_type"] = $flag;
        $this->modifyArray["sur_charge_type"] = 'sur_charge_type';
    }

    public function setDiscount($flag) {
        $this->valArray["discount"] = $flag;
        $this->modifyArray["discount"] = 'discount';
    }
    public function setDiscountType($flag) {
        $this->valArray["discount_type"] = $flag;
        $this->modifyArray["discount_type"] = 'discount_type';
    }

    public function setExtraCharge($flag) {
        $this->valArray["extra_charge"] = $flag;
        $this->modifyArray["extra_charge"] = 'extra_charge';
    }
    public function setExtraChargeType($flag) {
        $this->valArray["extra_charge_type"] = $flag;
        $this->modifyArray["extra_charge_type"] = 'extra_charge_type';
    }

    public function setLastUpdated($flag) {
        $this->valArray["last_updated"] = $flag;
        $this->modifyArray["last_updated"] = 'last_updated';
    }
    public static function getTotalNumberOfUsersFromSql($sql){
        $rs = DbAccess3::runQuery($sql);
        $data=mysqli_fetch_assoc($rs);
        return $data['total'];
    }
}

// class
