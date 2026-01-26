<?php

/**
 * Groups Object
 *
 */
class Groups extends DbAccess3 {

    /**
     * Construct
     *
     * @param id/array
     */
    public function __construct($mixedCreator = null) {
        $fieldList = array(
            'group_id' => 'number',
            'group_name' => 'string',
            'group_slug' => 'string',
            'group_desc' => 'string',           
            'group_type' => ['enum' => ['admin','corporate','client']],           
            'is_active' => 'number',
            'added_by' => 'number',
            'added_date' => 'datetime',            
            'is_deleted' => 'number'
        );
        parent::__construct("groups", 'group_id', $fieldList, $mixedCreator);
    }

    /**
     * Get object Id (not provided as magic method) - read only.
     *
     */
    public function getGroupId() {
        return $this->valArray["group_id"];
    }

    /**
     * Get list of user objects, using sql given
     *
     * @param string $sql
     */
    public static function getGroupsListFromSql($sql) {
        return DbAccess3::getListFromSql(__CLASS__, $sql);
    }
    public static function getTotalNumberOfGroupsFromSql($sql){
        $rs = DbAccess3::runQuery($sql);
        $data=mysqli_fetch_assoc($rs);
        return $data['total'];
    }
}
