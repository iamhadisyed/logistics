<?php

/**
 * ManifestServiceMapping Object
 *
 */
class ManifestServiceMapping extends DbAccess3 {

    /**
     * Construct
     *
     * @param id/array
     */
    public function __construct($mixedCreator = null) {
        $fieldList = array(
            'id' => 'number',
            'manifest_id' => 'number',
            'service_id' => 'number'
        );
        parent::__construct("manifest_service_mapping", 'id', $fieldList, $mixedCreator);
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
    public static function getManifestServiceMappingListFromSql($sql) {
        return DbAccess3::getListFromSql(__CLASS__, $sql);
    }

    public static function getTotalNumberOfManifestServiceMappingFromSql($sql) {
        $rs = DbAccess3::runQuery($sql);
        $data = mysqli_fetch_assoc($rs);
        return $data['total'];
    }
    public function deleteById($id) {
        if($id != "" && $id > 0)
            self::runQuery("DELETE FROM manifest_service_mapping WHERE id = '".DbAccess3::escape($id)."'");
    }
    static public function deleteByManifestIdNServiceId($manifestId,$serviceId) {
        if($serviceId != "" && $serviceId > 0 && $manifestId !="" && $manifestId > 0)
            self::runQuery("DELETE FROM manifest_service_mapping WHERE manifest_id = '".DbAccess3::escape($manifestId)."' AND service_id = '".DbAccess3::escape($serviceId)."'");
    }
}
