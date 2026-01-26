<?php

// get settings
class carrierServiceDefaultRulesFilter {

    private $filter = "";
    private $order_by = "";
    private $limit = 100;
    private $rowsPerPage = 0;
    private $pageOffset = 0;

    /**
     * Get list of carrierServiceDefaultRules items based on filter conditions
     *
     * @return array[carrierServiceDefaultRules]
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

        $sql = "SELECT count(*) as total FROM carrier_service_default_rules csdr $where $sort ";
        t($sql, __METHOD__);
        return carrierServiceDefaultRules::getTotalNumberOfcarrierServiceDefaultRulesFromSql($sql);
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
        $sql = "SELECT " . $columns . " FROM carrier_service_default_rules csdr  $where $sort LIMIT $this->pageOffset , $this->rowsPerPage";
        t($sql, __METHOD__);
        return carrierServiceDefaultRules::getcarrierServiceDefaultRulesListFromSql($sql);
    }

    /**
     * Get list of carrierServiceDefaultRules items based on filter conditions
     *
     * @return array[carrierServiceDefaultRules]
     */
    public function getList($columnName = "csdr.*",$agentColumn = '',$debug = false) {
        // has filter been configured?
        $where = "";
        if ($this->filter != "") {
            $where = "WHERE " . substr($this->filter, 4);
        }
        $sort = "";
        if ($this->order_by != "")
            $sort = "ORDER BY " . $this->order_by;
        
        $agentJoin = "";
        if ($this->join != "") {
            $agentJoin = $this->join;
        }
        
        if (strpos($agentJoin, 'JOIN `agent_data`') !== false) {
            if(trim($agentColumn )== '')
                $agentColumn = ",a.agent_name AS from_weight";
        }
        $sql = "SELECT $columnName  $agentColumn
                FROM carrier_service_default_rules csdr
                $agentJoin 
                $where
                $sort
                ";
        if($debug)
            echo $sql;
        return carrierServiceDefaultRules::getcarrierServiceDefaultRulesListFromSql($sql);
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
				FROM carrier_service_default_rules csdr
				$where
				$sort
				LIMIT " . $recordLimit . "
				";
        return carrierServiceDefaultRules::getcarrierServiceDefaultRulesListFromSql($sql);
    }

   
    /**
     * Get count of carrierServiceDefaultRules items based on filter conditions
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
     public function addAgentTableJoin() {
        $this->join.= " JOIN `agent_data` a ON csdr.agentid = a.id ";
    }
    public function addIdFilter($id_value) {
	if (trim($id_value) != "") {
	    $this->filter .= " AND ";
	    $this->filter .= "csdr.id ='" . DbAccess3::escape($id_value) . "'";
	}
    }
}

// class
?>