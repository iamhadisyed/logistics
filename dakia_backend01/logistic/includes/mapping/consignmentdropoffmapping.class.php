<?php

/**
 * ConsignmentDropoffMapping Object
 *
 */
class ConsignmentDropoffMapping extends DbAccess3 {

    /**
     * Construct
     *
     * @param id/array
     */
    public function __construct($mixedCreator = null) {
        $fieldList = array(
            'id' => 'number',
            'dropoff_consignment_id' => 'number',
            'dispatch_consignment_id' => 'number',
            'dropoff_consignment_tracking' => 'string',
            'dispatch_consignment_tracking' => 'string',
            'parcel_tracking' => 'string',
            'bag_id' => 'number',
            'bag_number' => 'string',
            'added_by' => 'number',
            'date_created' => 'datetime'
        );
        parent::__construct("consignment_dropoff_mapping", 'id', $fieldList, $mixedCreator);
    }

    /**
     * Get object Id (not provided as magic method) - read only.
     *
     */
    public function getId() {
        return $this->valArray["id"];
    }

    /**
     * Get list of user objects, using sql given
     *
     * @param string $sql
     */
    public static function getConsignmentDropoffMappingListFromSql($sql) {
        return DbAccess3::getListFromSql(__CLASS__, $sql);
    }

    public static function getTotalNumberOfConsignmentDropoffMappingFromSql($sql) {
        $rs = DbAccess3::runQuery($sql);
        $data = mysqli_fetch_assoc($rs);
        return $data['total'];
    }
    public function deleteById($id) {
        if($id != "" && $id > 0)
            self::runQuery("DELETE FROM consignment_dropoff_mapping WHERE id = '".DbAccess3::escape($id)."'");
    }
}
