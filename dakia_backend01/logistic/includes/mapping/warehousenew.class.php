<?php

/**
 * Warehouse - New Warehouse class
 * - deals with warehouse Details
 * 
 *
 */
class WarehouseNew extends DbAccess3 {

    
    private $error_list = array();

    /**
     * Construct
     *
     * @param id/array
     */
    public function __construct($mixedCreator = null) {
        $fieldList = array(
            'id' => 'number',
            'warehouse_name' => 'string',
            'addressline1' => 'string',
            'addressline2' => 'string',
            'stateregion' => 'string',
            'citytown' => 'string',
            'postzipcode' => 'string',
            'countryid' => 'number',
            'phone' => 'string',
            'description' => 'string',
            'is_active' => 'number',
            'is_deleted' => 'number',
            'added_date' => 'datetime',
            'added_by' => 'number',
            'updated_date' => 'datetime',
            'updated_by' => 'number',
            'hub' => 'string',
            'email' => 'string',
            'warehouse_code' => 'string',
            'country_name' => 'undefined',
            'country_iso' => 'undefined',
        );
        //
        parent::__construct("warehouse", 'id', $fieldList, $mixedCreator);
    }

    /**
     * Get object Id (not provided as magic method) - read only.
     *
     */
    public function getId() {
        return $this->valArray["id"];
    }

    /**
     * get Warehouse Name.
     *
     * @return string
     */
    public function getWarehouseName() {
        return $this->valArray["warehouse_name"];
    }

    /**
     * get Warehouse Address line 1.
     *
     * @return string
     */
    public function getAddressLine1() {
        return $this->valArray["addressline1"];
    }

    /**
     * get Warehouse Address line 2.
     *
     * @return string
     */
    public function getAddressLine2() {
        return $this->valArray["addressline2"];
    }

    /**
     * get Warehouse City.
     *
     * @return string
     */
    public function getState() {
        return $this->valArray["stateregion"];
    }

    /**
     * get Warehouse City.
     *
     * @return string
     */
    public function getCity() {
        return $this->valArray["citytown"];
    }

    /**
     * get Warehouse Post Code.
     *
     * @return string
     */
    public function getPostCode() {
        return $this->valArray["postzipcode"];
    }

    /**
     * get Warehouse Country ID.
     *
     * @return Interger
     */
    public function getCountry() {
        return $this->valArray["countryid"];
    }

    /**
     * get Warehouse Phone Number.
     *
     * @return Number
     */
    public function getPhone() {
        return $this->valArray["phone"];
    }

    /**
     * get Warehouse Description.
     *
     * @return string
     */
    public function getDescription() {
        return $this->valArray["description"];
    }

    /**
     * Indicates if this account is Active.
     *
     * @return bool
     */
    public function getActive() {
        return $this->valArray["is_active"];
    }

    

    /**
     * get hub.
     *
     * @return string
     */
    public function getHub() {
        return $this->valArray["hub"];
    }

    /**
     * This will gave the email Address.
     *
     * @return string
     */
    public function getEmail() {
        return $this->valArray["email"];
    }

    public static function getTotalNumberOfWarehouseFromSql($sql) {
        //echo $sql;
        //die;
        $rs = DbAccess3::runQuery($sql);
        $data = mysqli_fetch_assoc($rs);
        return $data['total'];
    }

    public static function getWarehouseListFromSql($sql) {
        return DbAccess3::getListFromSql(__CLASS__, $sql);
    }

    public function SetId($val) {
        $this->valArray["id"] = $val;
        $this->modifyArray["id"] = $val;
    }

    /**
     * Set WareHouse Name
     */
    public function SetWarehouseName($val) {
        $this->valArray["warehouse_name"] = $val;
        $this->modifyArray["warehouse_name"] = $val;
    }

    /**
     * Set WareHouse Address Line 1
     */
    public function Setaddressline1($val) {
        $this->valArray["addressline1"] = $val;
        $this->modifyArray["addressline1"] = $val;
    }

    /**
     * Set Ware House Address Line 2
     */
    public function Setaddressline2($val) {
        $this->valArray["addressline2"] = $val;
        $this->modifyArray["addressline2"] = $val;
    }

    /**
     * Set State or Region
     */
    public function SetState($val) {
        $this->valArray["stateregion"] = $val;
        $this->modifyArray["stateregion"] = $val;
    }

    /**
     * Set City
     */
    public function SetCity($val) {
        $this->valArray["citytown"] = $val;
        $this->modifyArray["citytown"] = $val;
    }

    /**
     * Set Country Post Code
     */
    public function SetPostCode($val) {
        $this->valArray["postzipcode"] = $val;
        $this->modifyArray["postzipcode"] = $val;
    }

    /**
     * Set Country id
     */
    public function SetCountry($val) {
        $this->valArray["countryid"] = $val;
        $this->modifyArray["countryid"] = $val;
    }

    /**
     * Set WareHouse Phone
     */
    public function SetPhone($val) {
        $this->valArray["phone"] = $val;
        $this->modifyArray["phone"] = $val;
    }

    /**
     * Set WareHouse Description
     */
    public function SetDescription($val) {
        $this->valArray["description"] = $val;
        $this->modifyArray["description"] = $val;
    }

    /**
     * Set WareHouse Active/InActive Statue
     */
    public function SetActive($val) {
        $this->valArray["is_active"] = $val;
        $this->modifyArray["is_active"] = $val;
    }

    /**
     * Set WareHouse Delete Statue
     */
    public function SetDelete($val) {
        $this->valArray["is_deleted"] = $val;
        $this->modifyArray["is_deleted"] = $val;
    }

    /**
     * Set WareHouse Added Date
     */
    public function setAdded_date($val) {
        $this->valArray["added_date"] = $val;
        $this->modifyArray["added_date"] = $val;
    }

    /**
     * Set Added By Id
     */
    public function setAddedBy($val) {
        $this->valArray["added_by"] = $val;
        $this->modifyArray["added_by"] = $val;
    }

    /**
     * Set WareHouse Updated Date
     */
    public function setUpdated_date($val) {
        $this->valArray["updated_date"] = $val;
        $this->modifyArray["updated_date"] = $val;
    }

    /**
     * Set Updated By user id
     */
    public function setUpdated_by($val) {
        $this->valArray["updated_by"] = $val;
        $this->modifyArray["updated_by"] = $val;
    }

    /**
     * Set WareHouse Hub
     */
    public function setHub($val) {
        $this->valArray["hub"] = $val;
        $this->modifyArray["hub"] = $val;
    }

    /**
     * Set WareHouse Email
     */
    public function setEmail($val) {
        $this->valArray["email"] = $val;
        $this->modifyArray["email"] = $val;
    }

    public static function getDataFromSql($sql) {
        $rs = DbAccess3::runQuery($sql);
        $data = mysqli_fetch_assoc($rs);
        return $data['total'];
    }

    public static function nameWarehouse($warehouse) {

        $sql = "SELECT id, warehouse_name  FROM warehouse WHERE id = '" . DbAccess3::escape($warehouse) . "'";
        $result = DbAccess3::getListFromSql(__CLASS__, $sql);
        $name = '';
        if (count($result) > 0) {
            $name = $result[0]->getWarehouseName();
        }
        return $name;
    }
    public static function getWarehouseDropDownList($warehouse_id="") {
            $join = "";
            $sql = "SELECT s.id, warehouse_name FROM warehouse s ".$join."  order by id asc ";
            $rs = DbAccess3::runQuery($sql);
            $option = "";
            $option .= "<option value=''>Select Warehouse</option>";
            while ($valArray = mysqli_fetch_assoc($rs)) {
            if ($valArray["warehouse_name"] == "")
                continue;
                $selected = ($warehouse_id == $valArray["id"]) ? " selected" : "";
                $option .= "<option ". $selected ." value='".$valArray["id"]."'>". $valArray["warehouse_name"] ."</option>" ;
            }
            return $option;
        }

}

// class