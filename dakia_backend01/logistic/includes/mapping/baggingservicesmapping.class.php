<?php

/**
 * BaggingServicesMapping Object
 *
 */
class BaggingServicesMapping extends DbAccess3 {

    /**
     * Construct
     *
     * @param id/array
     */
    public function __construct($mixedCreator = null) {
        $fieldList = array(
            'id' => 'number',
            'bag_id' => 'number',
            'service_id' => 'number'
        );
        parent::__construct("bagging_services_mapping", 'id', $fieldList, $mixedCreator);
    }

    /**
     * Get object Id (not provided as magic method) - read only.
     *
     */
    public function getId() {
        return $this->valArray["id"];
    }

    /**
     * Get list of bagging_services_mapping objects, using sql given
     *
     * @param string $sql
     */
    public static function getBaggingServicesMappingListFromSql($sql) {
        return DbAccess3::getListFromSql(__CLASS__, $sql);
    }

    public static function getTotalNumberOfBaggingServicesMappingFromSql($sql) {
        $rs = DbAccess3::runQuery($sql);
        $data = mysqli_fetch_assoc($rs);
        return $data['total'];
    }
    public function deleteById($id) {
        if($id != "" && $id > 0)
            self::runQuery("DELETE FROM bagging_services_mapping WHERE id = '".DbAccess3::escape($id)."'");
    }
}
