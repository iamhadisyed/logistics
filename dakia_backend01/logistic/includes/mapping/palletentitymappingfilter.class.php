<?php

/*
 * PalletEntityMappingFilter Filter
 *
 */

class PalletEntityMappingFilter {

    private $filter = "";
    private $order_by = "";
    private $group_by = "";

    public function getList() {
        // has filter been configured?
        $where = "";
        if ($this->filter != "") {
            $where = "WHERE " . substr($this->filter, 4);
        }
        $sort = "";
        if ($this->group_by != "")
            $sort = "GROUP BY " . $this->group_by;
        if ($this->order_by != "")
            $sort .= "ORDER BY " . $this->order_by;

        $sql = "SELECT *
                        FROM pallet_entity_mapping pem
                        $where
                        $sort limit 5000
                ";
        t($sql, __METHOD__);
        return PalletEntityMapping::getPalletEntityMappingListFromSql($sql);
    }

    public function getColumnList($fields) {
        // has filter been configured?
        $where = "";
        if ($this->filter != "") {
            $where = "WHERE " . substr($this->filter, 4);
        }
        $sort = "";
        if ($this->group_by != "")
            $sort = "GROUP BY " . $this->group_by;
        if ($this->order_by != "")
            $sort .= "ORDER BY " . $this->order_by;

        $sql = "SELECT $fields
                            FROM pallet_entity_mapping pem
                            $where
                            $sort limit 5000
                            ";
        return PalletEntityMapping::getPalletEntityMappingListFromSql($sql);
    }

    public function addFilter($type) {
        $this->filter .= $type;
    }

    public function addFieldEqualFilter($columnName, $operator, $columnValue) {
        $this->filter .= " AND ";
        $this->filter .= "pem." . $columnName . " " . $operator . " '" . DbAccess3::escape($columnValue) . "'";
    }
}
