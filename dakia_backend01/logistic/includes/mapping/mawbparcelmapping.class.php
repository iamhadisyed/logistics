<?php

/**
 * MawbParcelMapping Object
 *
 */
class MawbParcelMapping extends DbAccess3 {

    /**
     * Construct
     *
     * @param id/array
     */
    public function __construct($mixedCreator = null) {
        $fieldList = array(
            'id' => 'number',
            'mawb_id' => 'number',
            'parcel_id' => 'number',
            'wharehouse_id' => 'number',
            'bag_id' => 'number',
            'date_added' => 'datetime',
            'added_by' => 'number',
            'date_updated' => 'datetime',
            'updated_by' => 'number',
            
            
            
            'bag_number' => 'undefined',
            'user_id' => 'undefined',
            'tracking_number' => 'undefined',
            'parcel_status_code' => 'undefined',
            'scanned_by' => 'undefined',
            'total_tracking' => 'undefined',
            'total_scan' => 'undefined',
            'total_not_scan' => 'undefined',
            'warehouse_name' => 'undefined',
            'pdf' => 'undefined',
            'bag_label' => 'undefined',
            'actual_weight' => 'undefined',
            'length' => 'undefined',
            'width' => 'undefined',
            'height' => 'undefined',
            'weight' => 'undefined',
            'mawb_number' => 'undefined'
        );
        parent::__construct("mawb_parcel_mapping", 'id', $fieldList, $mixedCreator);
    }

    /**
     * Get object Id (not provided as magic method) - read only.
     *
     */
    public function getId() {
        return $this->valArray["id"];
    }

    /**
     * Get list of mawb_parcel_mapping objects, using sql given
     *
     * @param string $sql
     */
    public static function getMawbParcelMappingListFromSql($sql) {
        return DbAccess3::getListFromSql(__CLASS__, $sql);
    }

    public static function getTotalNumberOfMawbParcelMappingFromSql($sql) {
        $rs = DbAccess3::runQuery($sql);
        $data = mysqli_fetch_assoc($rs);
        return $data['total'];
    }
    public function deleteById($id) {
        if($id != "" && $id > 0)
            self::runQuery("DELETE FROM mawb_parcel_mapping WHERE id = '".DbAccess3::escape($id)."'");
    }
    public function deleteBySql($sql) {
            self::runQuery($sql);
    }
    
}
