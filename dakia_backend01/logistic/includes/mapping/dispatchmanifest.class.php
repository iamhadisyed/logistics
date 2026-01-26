<?php

/**
 * DispatchManifest Object
 *
 */
class DispatchManifest extends DbAccess3 {

    /**
     * Construct
     *
     * @param id/array
     */
    public function __construct($mixedCreator = null) {
        $fieldList = array(
            'id' => 'number',
            'manifest_id' => 'number',
            'service_id' => 'number',
            'agent_id' => 'number',
            'added_by' => 'number',
            'added_date' => 'datetime',
            'updated_by' => 'number',
            'updated_date' => 'numdatetimeber'
        );
        parent::__construct("dispatch_manifest", 'id', $fieldList, $mixedCreator);
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
    public static function getDispatchManifestListFromSql($sql) {
        return DbAccess3::getListFromSql(__CLASS__, $sql);
    }

    public static function getTotalNumberOfDispatchManifestFromSql($sql) {
        $rs = DbAccess3::runQuery($sql);
        $data = mysqli_fetch_assoc($rs);
        return $data['total'];
    }
    public function deleteById($id) {
        if($id != "" && $id > 0)
            self::runQuery("DELETE FROM dispatch_manifest WHERE id = '".DbAccess3::escape($id)."'");
    }
}
