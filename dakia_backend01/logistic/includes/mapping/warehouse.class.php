<?php

////////////////////////////////////////////////////
//
// Class for dealing with products
//
////////////////////////////////////////////////////

/**
 * Warehouse - warehouse class
 * @package Ecommerce
 */
class Warehouse extends DbAccess3 {

    /**
     * Constructor. Returns the object.
     * @param int $id     
     */
    public function __construct($mixedCreator = null) {
        $fieldList = array('warehouse_name' => 'string',
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
            'added_date' => 'date',
            'added_by' => 'number',
            'updated_date' => 'date',
            'updated_by' => 'number',
            'hub' => 'string',
            'email' => 'string',
            'warehouse_code' => 'string'
        );
        parent::__construct('warehouse', 'id', $fieldList, $mixedCreator);
    }
    
    /**
     * Get object Id (not provided as magic method) - read only.
     *
     */
    public function getId() {
        return $this->valArray["id"];
    }
    public function getWarehouseName() {
        return $this->valArray["warehouse_name"];
    }

    public function getAddressLine1() {
        return $this->valArray["addressline1"];
    }

    public function getAddressLine2() {
        return $this->valArray["addressline2"];
    }

    public function getStateRegion() {
        return $this->valArray["stateregion"];
    }

    public function getCityTown() {
        return $this->valArray["citytown"];
    }

    public function getPostZipcode() {
        return $this->valArray["postzipcode"];
    }

    public function getCountryId() {
        return $this->valArray["countryid"];
    }

    public function getPhone() {
        return $this->valArray["phone"];
    }

    public function getDescription() {
        return $this->valArray["description"];
    }

    public function getIsActive() {
        return $this->valArray["is_active"];
    }

    public function getIsDeleted() {
        return $this->valArray["is_deleted"];
    }

    public function getAddedDate() {
        return $this->valArray["added_date"];
    }

    public function getAddedBy() {
        return $this->valArray["added_by"];
    }

    public function getUpdatedDate() {
        return $this->valArray["updated_date"];
    }

    public function getUpdatedBy() {
        return $this->valArray["updated_by"];
    }
	
    public function getEmail() {
        return $this->valArray["email"];
    }
    /**
     * Get list of warehouse objects, using sql given     
     * @param string $sql
     */
    public static function getWarehouseListFromSql($sql) {        
        return DbAccess3::getListFromSql(__CLASS__, $sql);
    }
    public static function getTotalNumberOfWarehouseFromSql($sql) {
        $rs = DbAccess3::runQuery($sql);
        $data = mysqli_fetch_assoc($rs);
        return $data['total'];
    }
}

?>