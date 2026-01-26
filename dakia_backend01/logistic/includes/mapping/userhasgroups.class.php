<?php

/**
 * UserHasGroups Object
 *
 */
class UserHasGroups extends DbAccess3 {

    /**
     * Construct
     *
     * @param id/array
     */
    public function __construct($mixedCreator = null) {
        $fieldList = array(
            'id' => 'number',
            'admin_id' => 'number',
            'group_id' => 'number',
            'group_name' => 'undefined'
        );
        parent::__construct("userhasgroups", 'id', $fieldList, $mixedCreator);
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
    public static function getUserHasGroupsListFromSql($sql) {
        return DbAccess3::getListFromSql(__CLASS__, $sql);
    }
    public static function getTotalNumberOfUserHasGroupsFromSql($sql){
                    $rs = DbAccess3::runQuery($sql);
                    $data=mysqli_fetch_assoc($rs);
                    return $data['total'];
    }
    public function deleteByAdminId($user_id) {
        if($user_id != "" && $user_id > 0)
            self::runQuery("DELETE FROM userhasgroups WHERE admin_id = '".DbAccess3::escape($user_id)."'");
    }
}
