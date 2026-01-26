<?php

class Department extends DbAccess3 {

    /**
     * Construct
     *
     * @param id/array
     */
    public function __construct($mixedCreator = null) {




        $fieldList = array(
            'id' => 'number',
            'title' => 'string',
            'department_code' => 'string',
            'description' => 'string',
            'isactive' => 'number',
            'isdeleted' => 'number',
            'addedby' => 'number',
            'added_date' => 'datetime',
            'updatedby' => 'number',
            'updated_on' => 'datetime',
            'department_head' => 'number',
            );
        //
        parent::__construct("department", 'id', $fieldList, $mixedCreator);
    }

    
    /**
     * Get list of HelpDeskMessage objects, using sql given
     *
     * @param string $sql
     */
    public static function getDepartmentListFromSql($sql) {

        return DbAccess3::getListFromSql(__CLASS__, $sql);
    }
    
    public static function getTotalNumberOfDepartmentFromSql($sql)
    {

        $rs = self::runQuery($sql);
        $data = mysqli_fetch_assoc($rs);
        return $data['total'];
    }

    /**
     * Get count of consignment objects, using sql given
     *
     * @param string $sql
     */
    public static function getDepartmentCountFromSql($sql) {
        return DbAccess3::getSql(__CLASS__, $sql);
    }

}

// class