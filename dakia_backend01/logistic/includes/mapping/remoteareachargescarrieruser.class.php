<?php

/**
 * RemoteareaChargesCarrierUser Object
 *
 */
class RemoteareaChargesCarrierUser extends DbAccess3 {

    /**
     * Construct
     *
     * @param id/array
     */
    public function __construct($mixedCreator = null) {
        $fieldList = array(
            'id' => 'number',
            'remotearea_group_id' => 'number',
            'user_account_id' => 'number',
            'remotearea_charges' => 'number',
            'added_by' => 'number',
            'added_date' => 'datetime',
            'updated_by' => 'number',
            'updated_date' => 'datetime',
            'is_deleted' => 'string'
        );
        parent::__construct("remotearea_charges_carrier_user", 'id', $fieldList, $mixedCreator);
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
    public static function getRemoteareaChargesCarrierUserListFromSql($sql) {
        return DbAccess3::getListFromSql(__CLASS__, $sql);
    }

    public static function getTotalNumberOfRemoteareaChargesCarrierUserFromSql($sql) {
        $rs = DbAccess3::runQuery($sql);
        $data = mysqli_fetch_assoc($rs);
        return $data['total'];
    }
    
   
    public static function deleteByUserIdNGroupId($userId,$groupId) {
        if($userId != "" && $userId > 0 && $groupId != "")
            self::runQuery("DELETE FROM remotearea_charges_carrier_user WHERE user_account_id = '".DbAccess3::escape($userId)."' AND remotearea_group_id IN(".$groupId.")");
    }
}
