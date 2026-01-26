<?php

/**
 * RemoteareaChargesCarrier Object
 *
 */
class RemoteareaChargesCarrier extends DbAccess3 {

    /**
     * Construct
     *
     * @param id/array
     */
    public function __construct($mixedCreator = null) {
        $fieldList = array(
            'id' => 'number',
            'remotearea_group_id' => 'number',
            'remotearea_charges' => 'number',
            'added_by' => 'number',
            'added_date' => 'datetime',
            'updated_by' => 'number',
            'updated_date' => 'datetime',
            'is_deleted' => 'string'
        );
        parent::__construct("remotearea_charges_carrier", 'id', $fieldList, $mixedCreator);
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
    public static function getRemoteareaChargesCarrierListFromSql($sql) {
        return DbAccess3::getListFromSql(__CLASS__, $sql);
    }

    public static function getTotalNumberOfRemoteareaChargesCarrierFromSql($sql) {
        $rs = DbAccess3::runQuery($sql);
        $data = mysqli_fetch_assoc($rs);
        return $data['total'];
    }
    public function deleteById($id) {
        if($id != "" && $id > 0)
            self::runQuery("DELETE FROM remotearea_charges_carrier WHERE id = '".DbAccess3::escape($id)."'");
    }
    public static function deleteByGroupId($GroupId) {
        if($GroupId != "" && $GroupId > 0){
            self::runQuery("DELETE FROM remotearea_charges_carrier WHERE remotearea_group_id = '".DbAccess3::escape($GroupId)."'");
        }
    }
    public static function getDataFromSql($sql) {
        return DbAccess3::getListFromSql(__CLASS__, $sql);
    }
}
