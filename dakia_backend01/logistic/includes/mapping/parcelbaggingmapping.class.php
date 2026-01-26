<?php

/**
 * ParcelBaggingMapping Object
 *
 */
class ParcelBaggingMapping extends DbAccess3 {

    /**
     * Construct
     *
     * @param id/array
     */
    public function __construct($mixedCreator = null) {
        $fieldList = array(
            'id' => 'number',
            'parcel_id' => 'number',
            'bag_id' => 'number',
            'added_by' => 'number',
            'added_date' => 'datetime',
            
            'bag_number' => 'undefined',
            'total_parcel' => 'undefined',
            'tracking_number' => 'undefined',
            'length' => 'undefined',
            'width' => 'undefined',
            'height' => 'undefined',
            'weight' => 'undefined',
            'parcel_status_code' => 'undefined',
            'username' => 'undefined',
            'date_created' => 'undefined',
            'consignment_id' => 'undefined',
            'is_closed' => 'undefined',
            'sender_country_id' => 'undefined',
            'country_id' => 'undefined',
            'currency' => 'undefined',
            'value' => 'undefined',
            'contact' => 'undefined',
            'address_line_1' => 'undefined',
            'city' => 'undefined',
            'postcode' => 'undefined',
            'sender_name' => 'undefined',
            'sender_address_line_1' => 'undefined',
            'sender_city' => 'undefined',
            'sender_postcode' => 'undefined',
            'sender_telephone' => 'undefined',
            'telephone' => 'undefined',
        );
        parent::__construct("parcel_bagging_mapping", 'id', $fieldList, $mixedCreator);
    }

    /**
     * Get object Id (not provided as magic method) - read only.
     *
     */
    public function getId() {
        return $this->valArray["id"];
    }

    /**
     * Get list of parcel_bagging_mapping objects, using sql given
     *
     * @param string $sql
     */
    public static function getParcelBaggingMappingListFromSql($sql) {
        return DbAccess3::getListFromSql(__CLASS__, $sql);
    }

    public static function getTotalNumberOfParcelBaggingMappingFromSql($sql) {
        $rs = DbAccess3::runQuery($sql);
        $data = mysqli_fetch_assoc($rs);
        return $data['total'];
    }
    public function deleteById($id) {
        if($id != "" && $id > 0)
            self::runQuery("DELETE FROM parcel_bagging_mapping WHERE id = '".DbAccess3::escape($id)."'");
    }
     public function deleteBySql($sql) {
            self::runQuery($sql);
    }
    public static function deleteByBagId($id) {
        if($id != "" && $id > 0)
            self::runQuery("DELETE FROM parcel_bagging_mapping WHERE bag_id = '".DbAccess3::escape($id)."'");
    }
}
