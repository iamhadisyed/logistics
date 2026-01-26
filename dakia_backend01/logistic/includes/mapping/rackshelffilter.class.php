<?php

// get settings
class RackShelfFilter {

    private $filter = "";

    /**
     * Get list of user items based on filter conditions
     *
     * @return array[User]
     */
    public function getList() {
        // has filter been configured?
        if ($this->filter != "")
            $where = "WHERE " . $this->filter;

        $sql = "SELECT rs.*
                    FROM rack_shelf rs                    
                    $where
                    Order by rs.shelf_no";
        return RackShelf::getRackShelfListFromSql($sql);
    }

    public function getColumnList($fields) {
        $where = "";
        if ($this->filter != "")
            $where = "WHERE " . $this->filter;
        $sql = "SELECT id, " . $fields . " FROM rack_shelf rs $where Order by rs.shelf_no";
        return RackShelf::getRackShelfListFromSql($sql);
    }

    public function addFilter($filter) {
        $this->filter .= $filter;
    }
    /**
     * Get count of user items based on filter conditions
     *
     * @return int
     */
    public function getCount() {
        $result = $this->getList();
        return sizeof($result);
    }
}

// class
?>