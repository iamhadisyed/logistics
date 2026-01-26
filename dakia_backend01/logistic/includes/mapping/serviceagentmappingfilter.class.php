<?php

/*
 * Consignment Filter
 *
 */

class ServiceAgentMappingDataFilter {

    private $filter = "";
    private $order_by = "";
    private $group_by = "";
	private $join = "";

    public function getList($column = '*', $debug=false) {
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

        $sql = "SELECT ".$column."
				FROM service_agent_mapping m 
				$where
				$sort limit 5000
				";
        if($debug)
            echo $sql;
        t($sql, __METHOD__);
        return ServiceAgentMapping::getServiceAgentListFromSql($sql);
    }

    public function addServiceIDFilter($serviceid) {
        $this->filter .= " AND serviceid = '" . DbAccess3::escape($serviceid) . "'";
    }

    public function addAgentIDFilter($agentId) {
        $this->filter .= " AND agentid = '" . DbAccess3::escape($agentId) . "'";
    }

    public function addServiceIDArrayFilter($manifestidarray) {
        $manifest_array = "'" . implode("','", $manifestidarray) . "'";
        $this->filter .= " AND serviceid in (" . $manifest_array . ")";
    }

    public function getColumnList($fields) {
        
        $where = "";
        if ($this->filter != "") {
            $where = "WHERE " . substr($this->filter, 4);
        }
        $sort = "";
        if ($this->group_by != "")
            $sort = "GROUP BY " . $this->group_by;
        if ($this->order_by != "")
            $sort .= "ORDER BY " . $this->order_by;
        $serviceJoin = "";
        if ($this->join != "")
                $serviceJoin = $this->join;
                
        $sql = "SELECT " . $fields . "
                            FROM service_agent_mapping m
                            $serviceJoin
                            $where
                            $sort
                            ";
       
        return ServiceAgentMapping::getServiceAgentListFromSql($sql);
    }

    public function getPagingCount() {
        // has filter been configured?
        $where = "WHERE sam.agentid <> '' AND";
        $where .= $this->filter;
        $groupBy = $this->groupBy;
        $sort = $this->order_by;
	    $serviceJoin = "";
	    if ($this->join != "")
		    $serviceJoin = $this->join;
        $sql = "SELECT count(sam.id) AS total FROM  service_agent_mapping sam $serviceJoin $where " . $groupBy . " ";
//	    die($sql);
        t($sql, __METHOD__);
        return ServiceAgentMapping::getTotalNumberOfServiceAgentListFromSql($sql);
    }

    public function addFilter($filterVal) {
        if ($this->filter != "")
            $this->filter .= "    AND ";

        $this->filter .= "    " . $filterVal . " ";
    }
	public function setRowsPerPage($rowsPP) {
		$this->rowsPerPage = $rowsPP;
	}
	public function setOffset($offset) {
		$this->pageOffset = $offset;
	}
	public function getPagingList($fields="sam.*") {
		$where = " WHERE";
		$where .= $this->filter;
		$groupBy = $this->groupBy;
		if($this->order_by != '')
			$sort = " order by " . $this->order_by;
		$serviceJoin = "";
		if ($this->join != "")
			$serviceJoin = $this->join;
		$sql = "SELECT " . $fields . " FROM  service_agent_mapping sam $serviceJoin $where $sort LIMIT $this->pageOffset , $this->rowsPerPage";
		t($sql, __METHOD__);

		return ServiceAgentMapping::getServiceAgentListFromSql($sql);
	}
	public function addFieldFilter($colm, $value) {

		$this->filter .= " AND ";
		$this->filter .= " " . $colm . " = '" . DbAccess3::escape($value) . "'";
	}
	public function addFieldLikeFilter($colm, $value) {
		$this->filter .= " AND ";
		$this->filter .= " " . $colm . " LIKE '" . DbAccess3::escape($value) . "%'";
	}
	public function AddOrderBy($name, $ascending = true) {
		//if ($this->order_by != "") $this->order_by = " ";
		//
		if (trim($name) != '')
			$this->order_by = $name . " " . ($ascending ? "" : " DESC");
	}
	public function addServicesJoin() {
		$this->join .= " LEFT JOIN `services` ser  ON ser.id = sam.serviceid ";
	}
	public function addAgentJoin($joinWhere = "",$joinType = "") {
		$this->join .= $joinType."  JOIN `agent_data` ad  ON ad.`id` = m.`agentid` ".$joinWhere;
	}

}
