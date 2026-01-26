<?php

/*
 * User Department Filter
 *
 */

class UserDepartmentFilter {

    private $filter = "";
    private $order_by = "";

    /**
     * Get list of User Department items based on filter conditions
     *
     * @return array[User Department]
     */
    public function getList() {
        // has filter been configured?
        $where = "";
        if ($this->filter != "") {
            $where = "WHERE " . substr($this->filter, 4);
        }
        $sort = "";
        if ($this->order_by != "")
            $sort = "ORDER BY " . $this->order_by;

        $sql = "SELECT * FROM user_department $where $sort LIMIT 5000";

        t($sql, __METHOD__);
        return UserDepartment::getDepartmentListFromSql($sql);
    }

    public function getColumnList($fields, $recordLimit = 5000) {

        $fields = rtrim($fields, ",");
        // has filter been configured?
        $where = "";
        if ($this->filter != "") {
            $where = "WHERE " . substr($this->filter, 4);
        }
        $sort = "";
        if ($this->order_by != "")
            $sort = "ORDER BY " . $this->order_by;

        if ($recordLimit == '')
            $recordLimit = 5000;

        $sql = "SELECT " . $fields . ", id 
				FROM user_department
				$where
				$sort
				LIMIT " . $recordLimit . "
				";
        return UserDepartment::getDepartmentListFromSql($sql);
    }

    public function getCount() {
        $result = $this->getList();
        return sizeof($result);
    }
    
    public function addByUserId($date_value) {
        $this->filter .= " AND user_id  = '" . DbAccess3::escape($date_value) . "'";
    }

}
