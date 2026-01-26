<?php

class UserDepartment extends DbAccess3 {

    /**
     * Construct
     *
     * @param id/array
     */
    public function __construct($mixedCreator = null) {
        $fieldList = array(
            'user_id' => 'number',
            'department_id' => 'number'
        );
        //
        parent::__construct("user_department", 'id', $fieldList, $mixedCreator);
    }

    public function deleteByUser($user_id) {
        if($user_id != "" && $user_id > 0)
            self::runQuery("DELETE FROM user_department WHERE user_id = '".DbAccess3::escape($user_id)."'");
    }
    
    
    
    /**
     * Get list of User Department objects, using sql given
     *
     * @param string $sql
     */
    public static function getDepartmentListFromSql($sql) {

        return DbAccess3::getListFromSql(__CLASS__, $sql);
    }

    

}

// class