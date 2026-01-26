<?php

/**
 * Parcelgroupconsignment - Parcel group consignment class
 * - deals with Parcel group consignments
 *
 */
class ServiceCountryTime extends DbAccess3 {

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
            'id_country' => 'number',
            'id_service' => 'number',
            'transit_time' => 'number',
            'code'            => 'undefined',
            'iso'            => 'undefined',
            'country_name'            => 'undefined',
            'carrier_id'    => 'undefined'
        );
        //
        parent::__construct("service_country_ttime", 'id', $fieldList, $mixedCreator);
    }

    /**
     * Get object Id (not provided as magic method) - read only.
     *
     */
    public function getId() {
        return $this->valArray["id"];
    }

    public function getServiceId() {
        return $this->valArray["id_service"];
    }

    public function getCountryId() {
        return $this->valArray["id_country"];
    }

    public function getTransitTime() {
        return $this->valArray["transit_time"];
    }

    public static function getServiceCountryTimeListFromSql($sql) {
        return DbAccess3::getListFromSql(__CLASS__, $sql);
    }

    public static function getTotalServiceCountryTimeListFromSql($sql) {
        $rs = DbAccess3::runQuery($sql);
        $data = mysqli_fetch_assoc($rs);
        return $data['total'];
    }
    
    public function SetCountryId($val) {
        $this->valArray["id_country"] = $val;
        $this->modifyArray["id_country"] = $val;
    }

    public function SetId($val) {
        $this->valArray["id"] = $val;
        $this->modifyArray["id"] = $val;
    }

    public function SetTransitTime($val) {
        $this->valArray["transit_time"] = $val;
        $this->modifyArray["transit_time"] = $val;
    }

    public function SetServiceId($val) {
        $this->valArray["id_service"] = $val;
        $this->modifyArray["id_service"] = $val;
    }

    public static function getDataFromSql($sql) {
        $rs = DbAccess3::runQuery($sql);
        $data = mysqli_fetch_assoc($rs);
        return $data['total'];
    }
    public static function deleteByServiceIdAndCountryId($where)
        {
            $delQuery = " DELETE from service_country_ttime where $where";
            DbAccess3::runQuery($delQuery);
            return true;
        }
}

// class