<?php

/**
 * RemoteareaChargesServicesUser Object
 *
 */
class RemoteareaChargesServicesUser extends DbAccess3 {

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
            'service_id' => 'number',
            'from_weight' => 'number',
            'to_weight' => 'number',
            'formulla' => 'string',
            'user_account_id' => 'number',
            'added_by' => 'number',
            'added_date' => 'datetime',
            'updated_by' => 'number',
            'updated_date' => 'datetime',
            'is_deleted' => 'string'
        );
        parent::__construct("remotearea_charges_services_user", 'id', $fieldList, $mixedCreator);
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
    public static function getRemoteareaChargesServicesUserListFromSql($sql) {
        return DbAccess3::getListFromSql(__CLASS__, $sql);
    }

    public static function getTotalNumberOfRemoteareaChargesServicesUserFromSql($sql) {
        $rs = DbAccess3::runQuery($sql);
        $data = mysqli_fetch_assoc($rs);
        return $data['total'];
    }
  
    public static function deleteByServiceId($serviceId) {
        if($serviceId != "" && $serviceId > 0)
            self::runQuery("DELETE FROM remotearea_charges_services_user WHERE service_id = '".DbAccess3::escape($serviceId)."'");
    }
}
