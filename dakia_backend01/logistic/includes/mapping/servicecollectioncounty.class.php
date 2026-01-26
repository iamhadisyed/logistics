<?php

/**
 * ServiceCollectionCounty Object
 *
 */
class ServiceCollectionCounty extends DbAccess3 {

    /**
     * Construct
     *
     * @param id/array
     */
    public function __construct($mixedCreator = null) {
        $fieldList = array(
            'id' => 'number',
            'carrier_id' => 'number',
            'group_name' => 'string',
            'country_id' => 'number'
        );
        parent::__construct("service_collection_county", 'id', $fieldList, $mixedCreator);
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
    public static function getServiceCollectionCountyListFromSql($sql) {
        return DbAccess3::getListFromSql(__CLASS__, $sql);
    }

    public static function getTotalNumberOfServiceCollectionCountyFromSql($sql) {
        $rs = DbAccess3::runQuery($sql);
        $data = mysqli_fetch_assoc($rs);
        return $data['total'];
    }
    public function deleteByServiceId($serviceId) {
        if($serviceId != "" && $serviceId > 0)
            self::runQuery("DELETE FROM service_collection_county WHERE service_id = '".DbAccess3::escape($serviceId)."'");
    }
    public static function checkServiceCollectionExists($serviceId , $countryId) {
           $sql = "SELECT COUNT(id) AS id FROM `service_collection_county` WHERE  service_id = " . DbAccess3::escape($serviceId)." AND country_id = ". DbAccess3::escape($countryId);
            $result = DbAccess3::getListFromSql(__CLASS__, $sql);
                return $result[0]->getId();
    }
}
