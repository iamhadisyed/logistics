<?php

// get settings
class ServiceCollectionCountyFilter {

    private $filter = "";
    private $order_by = "";
    private $limit = 100;
    private $rowsPerPage = 0;
    private $pageOffset = 0;
    private $join = "";
    private $groupBy = "";

    /**
     * Get list of ServiceCollectionCounty items based on filter conditions
     *
     * @return array[ServiceCollectionCounty]
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
        $checkJoin = "";
        if ($this->join != "")
            $checkJoin = $this->join;
        
        $sql = "SELECT count(scc.id) as total FROM service_collection_county scc $checkJoin $where $sort ";
        t($sql, __METHOD__);
        return ServiceCollectionCounty::getTotalNumberOfServiceCollectionCountyFromSql($sql);
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
        
        $checkJoin = "";
        if ($this->join != "")
            $checkJoin = $this->join;
        
        $joinColumnName = "";
        if (strpos($checkJoin, 'JOIN `carrier`') !== false) {
            $joinColumnName = ",c.carrier AS carrier_id";
        }
        
        
       $sql = "SELECT " . $columns . " $joinColumnName FROM service_collection_county scc  $checkJoin  $where $sort LIMIT $this->pageOffset , $this->rowsPerPage";
        t($sql, __METHOD__);
        return ServiceCollectionCounty::getServiceCollectionCountyListFromSql($sql);
    }

    /**
     * Get list of ServiceCollectionCounty items based on filter conditions
     *
     * @return array[ServiceCollectionCounty]
     */
    public function getList($columnName = "*") {
        // has filter been configured?
        $where = "";
        if ($this->filter != "") {
            $where = "WHERE " . substr($this->filter, 4);
        }
        $sort = "";
        if ($this->order_by != "")
            $sort = "ORDER BY " . $this->order_by;
        $groupBy = "";
        if ($this->groupBy != "")
            $groupBy = $this->groupBy;
        
        $checkJoin = "";
        if ($this->join != "")
            $checkJoin = $this->join;
        
        $joinColumnName = "";
        if (strpos($checkJoin, 'JOIN `carrier`') !== false) {
            $joinColumnName = ",c.carrier AS carrier_id";
        }
        
        
       $sql = "SELECT $columnName $joinColumnName
                FROM service_collection_county scc
                $checkJoin 
                $where
                $groupBy
                $sort
                ";
        t($sql, __METHOD__);
        return ServiceCollectionCounty::getServiceCollectionCountyListFromSql($sql);
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
				FROM service_collection_county scc
				$where
				$sort
				LIMIT " . $recordLimit . "
				";
        return ServiceCollectionCounty::getServiceCollectionCountyListFromSql($sql);
    }

    /**
     * Get count of ServiceCollectionCounty items based on filter conditions
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

        $this->filter .= "    " . DbAccess3::escape($filterVal) . " ";
    }
  
    public function addFieldFilter($colm, $value) {
        if ($this->filter != "")
            $this->filter .= " AND ";
        $this->filter .= " " . $colm . " = '" . DbAccess3::escape($value) . "'";
    }
    public function addFromFilter($colm, $value) {
        if ($this->filter != "")
            $this->filter .= " AND ";
        $this->filter .= " " . $colm . " >= '" .$value. "'";
    }
    public function addToFilter($colm, $value) {
        if ($this->filter != "")
            $this->filter .= " AND ";
        $this->filter .= " " . $colm . " <= '" .$value. "'";
    }
    public function addInFilter($colm, $value) {
        if ($this->filter != "")
            $this->filter .= " AND ";
        $this->filter .= " " . $colm . " IN( " .$value. ")";
    }
    public function addGroupBy($field) {
        $this->groupBy .= " GROUP BY $field";
    }
    public function addNotEqualEmptyFilter($colm, $value) {
        if ($this->filter != "")
            $this->filter .= " AND ";
        $this->filter .= " " . $colm . " != '' ";
    }
    public function saveBulkData($countryArr,$serviceId) {
        $sqlStr = "";
        if(count($countryArr) > 0){
            foreach ($countryArr as $countryId) {
                $sqlStr .= "('".$serviceId."','".$countryId."'),";
            }
        $sqlStr = rtrim($sqlStr,",");
        $sql = "INSERT INTO `service_collection_county`
                       (service_id,
                       country_id)
                VALUES ".$sqlStr."
                       ";
        }
        DbAccess3::runQuery($sql);
    }
}

// class
?>