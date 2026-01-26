<?php

/**
 * Parcelgroupconsignment - Parcel group consignment class
 * - deals with Parcel group consignments
 *
 */
class WarehouseProcessingTime extends DbAccess3 {

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
            'warehouse_id' => 'number',
            'service_id' => 'number',
            'parcel_processing_time' => 'number',
        );
        //
        parent::__construct("warehouse_processing_time", 'id', $fieldList, $mixedCreator);
    }

    /**
     * Get object Id (not provided as magic method) - read only.
     *
     */
    public function getId() {
        return $this->valArray["id"];
    }

    public function getServiceId() {
        return $this->valArray["service_id"];
    }

    public function getWarehouseId() {
        return $this->valArray["warehouse_id"];
    }

    public function getParcelProcessingTime() {
        return $this->valArray["parcel_processing_time"];
    }

    public static function getWarehouseProcessingTimeListFromSql($sql) {
        return DbAccess3::getListFromSql(__CLASS__, $sql);
    }

    public static function getTotalWarehouseProcessingTimeListFromSql($sql) {
        $rs = DbAccess3::runQuery($sql);
        $data = mysqli_fetch_assoc($rs);
        return $data['total'];
    }
    
    public function SetWarehouseId($val) {
        $this->valArray["warehouse_id"] = $val;
        $this->modifyArray["warehouse_id"] = $val;
    }

    public function SetId($val) {
        $this->valArray["id"] = $val;
        $this->modifyArray["id"] = $val;
    }

    public function SetParcelProcessingTime($val) {
        $this->valArray["parcel_processing_time"] = $val;
        $this->modifyArray["parcel_processing_time"] = $val;
    }

    public function setServiceId($val) {
        $this->valArray["service_id"] = $val;
        $this->modifyArray["service_id"] = $val;
    }

    public static function getDataFromSql($sql) {
        $rs = DbAccess3::runQuery($sql);
        $data = mysqli_fetch_assoc($rs);
        return $data['total'];
    }

}

// class