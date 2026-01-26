<?php

// get settings
class PermissionsFilter {

    private $filter = "";
    private $order_by = "";
    private $limit = 100;
    private $rowsPerPage = 0;
    private $pageOffset = 0;

    /**
     * Get list of Permissions items based on filter conditions
     *
     * @return array[Permissions]
     */
    public function getPagingCount() {
        // has filter been configured?
        $where = "";
        if ($this->filter != "") {
            $where = "WHERE " . substr($this->filter, 4);
        }
        $sort = "";
        if ($this->order_by != "")
            $sort = "ORDER BY " . $this->order_by;

        $sql = "SELECT count(*) as total FROM permissions p $where $sort ";
        t($sql, __METHOD__);
        return Permissions::getTotalNumberOfPermissionsFromSql($sql);
    }

    public function setOffset($offset) {
        $this->pageOffset = $offset;
    }

    public function setRowsPerPage($rowsPP) {
        $this->rowsPerPage = $rowsPP;
    }

    public function getPagingList($columns = '*') {
        // has filter been configured?
        $where = "";
        if ($this->filter != "") {
            $where = "WHERE " . substr($this->filter, 4);
        }
        $sort = "";
        if ($this->order_by != "")
            $sort = "ORDER BY " . $this->order_by;
        $sql = "SELECT " . $columns . " FROM permissions p  $where $sort LIMIT $this->pageOffset , $this->rowsPerPage";
        t($sql, __METHOD__);
        return Permissions::getPermissionsListFromSql($sql);
    }

    /**
     * Get list of Permissions items based on filter conditions
     *
     * @return array[Permissions]
     */
    public function getList($debug = false) {
        // has filter been configured?
        $where = "";
        if ($this->filter != "") {
            $where = "WHERE " . substr($this->filter, 4);
        }
        $sort = "";
        if ($this->order_by != "")
            $sort = "ORDER BY " . $this->order_by;

        $userJoin = "";
        $columnName = "*";
        if ($this->join != "") {
            $userJoin = $this->join;
            $userColumn = "";
            if (strpos($userJoin, 'JOIN `user`') !== false) {
                $userColumn = ",u.first_name,u.user_type";
            }
            $columnName = " p.id,p.parent_id,p.file_name,p.description,p.added_by,p.added_date,p.query_string,p.icon,p.sort_order,p.is_menu_item,p.is_active,p.is_deleted,lk.caption AS lang_key".$userColumn;
        }
        $sql = "SELECT $columnName 
                FROM permissions p
                $userJoin 
                $where
                $sort
                ";
        if($debug)
        {
            echo $sql;
            die;
        }
        t($sql, __METHOD__);
        return Permissions::getPermissionsListFromSql($sql);
    }

    /**
     * Get count of Permissions items based on filter conditions
     *
     * @return int
     */
    public function getCount() {
        $result = $this->getList();
        return sizeof($result);
    }

    /*     * *
     * Oder by Column Name
     */

    public function AddOrderBy($columnName = "id", $ascending = true) {
        if ($this->order_by != "")
            $this->order_by .= ", ";
        //
        $this->order_by .= $columnName . ($ascending ? "" : " DESC");
    }

    public function addFieldLikeFilter($colm, $value) {
        if ($this->filter != "")
            $this->filter .= " AND ";

        $this->filter .= "    " . $colm . " LIKE '" . DbAccess3::escape($value) . "%'";
    }

    public function addFilter($filterVal) {
        if ($this->filter != "")
            $this->filter .= " AND ";

        $this->filter .= "    " . $filterVal . " ";
    }

    public function addUserTableJoin() {
        $this->join.= " JOIN `user` u ON p.added_by = u.id ";
    }
    public function addUserTableLeftJoin() {
        $this->join.= " LEFT JOIN `user` u ON p.added_by = u.id ";
    }

    public function addLanguageKeysTableJoin() {
        $this->join.= " JOIN language_keys lk ON p.lang_key = lk.keyword ";
    }
    public function addLanguageKeysTableLeftJoin($where) {
        $this->join.= " LEFT JOIN language_keys lk ON p.lang_key = lk.keyword  ".$where;
    }

}

// class
?>