<?php

/**
 * GroupHasPermissions Object
 *
 */
class GroupHasPermissions extends DbAccess3 {

    /**
     * Construct
     *
     * @param id/array
     */
    public function __construct($mixedCreator = null) {
        $fieldList = array(
            'id' => 'number',
            'group_id' => 'number',
            'perm_id' => 'number'
        );
        parent::__construct("grouphaspermissions", 'id', $fieldList, $mixedCreator);
    }

    /**
     * Get object Id (not provided as magic method) - read only.
     *
     */
    public function getGroupId() {
        return $this->valArray["id"];
    }

    /**
     * Get list of user objects, using sql given
     *
     * @param string $sql
     */
    public static function getGroupHasPermissionsListFromSql($sql) {
        return DbAccess3::getListFromSql(__CLASS__, $sql);
    }
    public static function getTotalNumberOfGroupHasPermissionsFromSql($sql){
        $rs = DbAccess3::runQuery($sql);
        $data=mysqli_fetch_assoc($rs);
        return $data['total'];
    }
}
