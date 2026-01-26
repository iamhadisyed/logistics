<?php

/**
 * RemoteareaChargesServices Object
 *
 */
class RemoteareaChargesServices extends DbAccess3 {

    /**
     * Construct
     *
     * @param id/array
     */
    public function __construct($mixedCreator = null) {
        $fieldList = array(
            'id' => 'number',
            'remotearea_group_id' => 'number',
            'service_id' => 'number',
            'remotearea_charges' => 'number',
            'from_weight' => 'number',
            'to_weight' => 'number',
            'formulla' => 'string',
            'added_by' => 'number',
            'added_date' => 'datetime',
            'updated_by' => 'number',
            'updated_date' => 'datetime',
            'is_deleted' => 'string'
        );
        parent::__construct("remotearea_charges_services", 'id', $fieldList, $mixedCreator);
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
    public static function getRemoteareaChargesServicesListFromSql($sql) {
        return DbAccess3::getListFromSql(__CLASS__, $sql);
    }

    public static function getTotalNumberOfRemoteareaChargesServicesFromSql($sql) {
        $rs = DbAccess3::runQuery($sql);
        $data = mysqli_fetch_assoc($rs);
        return $data['total'];
    }
   
    public static function deleteByServiceId($serviceId) {
        if($serviceId != "" && $serviceId > 0)
            self::runQuery("DELETE FROM remotearea_charges_services WHERE service_id = '".DbAccess3::escape($serviceId)."'");
    }
    public static function getDataFromSql($sql) {
        return DbAccess3::getListFromSql(__CLASS__, $sql);
    }
}
