<?php

/**
 * Parcelgroupconsignment - Parcel group consignment class
 * - deals with Parcel group consignments
 *
 */
class WarehouseToWarehouse extends DbAccess3 {

    // consignment service type
    // consignment status
    // - if add a new status, remember to update setStatus()
    //
    private $error_list = array();

    /**
     * Construct  
     *
     * @param id/array
     */
    public function __construct($mixedCreator = null) {
        $fieldList = array(
            'from_warehouse_id' => 'number',
            'to_warehouse_id' => 'number',
            'transit_time' => 'number',
        );
        //
        parent::__construct("warehouse_warehouse_ttime", 'id', $fieldList, $mixedCreator);
    }

    /**
     * Get object Id (not provided as magic method) - read only.
     *
     */
    public function getId() {
        return $this->valArray["id"];
    }

    public function getFromWarehouseId() {
        return $this->valArray["from_warehouse_id"];
    }

    public function getToWarehouseId() {
        return $this->valArray["to_warehouse_id"];
    }

    public function getTransitTime() {
        return $this->valArray["transit_time"];
    }

    public static function getWarehouseToWarehouseListFromSql($sql) {
        return DbAccess3::getListFromSql(__CLASS__, $sql);
    }

    public static function getTotalWarehouseToWarehouseListFromSql($sql) {
        $rs = DbAccess3::runQuery($sql);
        $data = mysqli_fetch_assoc($rs);
        return $data['total'];
    }
    
    public function SetFromWarehouseId($val) {
        $this->valArray["from_warehouse_id"] = $val;
        $this->modifyArray["from_warehouse_id"] = $val;
    }

    public function SetId($val) {
        $this->valArray["id"] = $val;
        $this->modifyArray["id"] = $val;
    }

    public function SetTransitTime($val) {
        $this->valArray["transit_time"] = $val;
        $this->modifyArray["transit_time"] = $val;
    }

    public function SetToWarehouseId($val) {
        $this->valArray["to_warehouse_id"] = $val;
        $this->modifyArray["to_warehouse_id"] = $val;
    }

    public static function getDataFromSql($sql) {
        $rs = DbAccess3::runQuery($sql);
        $data = mysqli_fetch_assoc($rs);
        return $data['total'];
    }

}

// class