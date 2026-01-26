<?php

////////////////////////////////////////////////////
//
// Class for dealing with courier consignment_charges_types
//
////////////////////////////////////////////////////
/**
 * ConsignmentChargesTypes - ConsignmentChargesTypes class
 * @package Courier
 */
class ConsignmentChargesTypesFilter{
    private $filter = "";
    private $order_by = "";
    private $limit = 100;
    private $rowsPerPage = 0;
    private $pageOffset = 0;
    private $join = [];

    /**
     * Get list of CarrierZones items based on filter conditions
     *
     * @return array[CarrierZones]
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
        $sql = "SELECT count(t.id) as total FROM consignment_charges_types cct $checkJoin $where $sort ";
        t($sql, __METHOD__);
        return ConsignmentChargesTypes::getTotalNumberOfConsignmentChargesTypesFromSql($sql);
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

        $sql = "SELECT " . $columns . " FROM consignment_charges_types cct $checkJoin $where $sort LIMIT $this->pageOffset , $this->rowsPerPage";
        t($sql, __METHOD__);
        return ConsignmentChargesTypes::getConsignmentChargesTypesListFromSql($sql);
    }

    public function getList($columnName = "*", $debug = false) {
        // has filter been configured?
        $where = "";
        if ($this->filter != "") {
            $where = "WHERE " . $this->filter;
        }
        $sort = "";
        if ($this->order_by != "")
            $sort = "ORDER BY " . $this->order_by;

        $checkJoin = "";
        if (!empty($this->join)) {
            $checkJoin = implode(" ", $this->join);
        }

        $sql = "SELECT $columnName
                FROM consignment_charges_types cct
                $checkJoin
                $where
                $sort
                ";
        if($debug) {
            echo $sql;
            die;
        }
        t($sql, __METHOD__);
        return ConsignmentChargesTypes::getConsignmentChargesTypesListFromSql($sql);
    }
    public function getColumnList($fields, $recordLimit = 5000, $debug=false) {

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
        
        $checkJoin = "";

         if (!empty($this->join)) {
            $checkJoin = implode(" ", $this->join);
         }
         $sql = "SELECT " . $fields . ", id
				FROM consignment_charges_types cct
				$checkJoin
				$where
				$sort
				LIMIT " . $recordLimit . "
				";
         if($debug)
         	echo $sql;
         return ConsignmentChargesTypes::getConsignmentChargesTypesListFromSql($sql);
    }

  
    /**
     * Get count of CarrierZones items based on filter conditions
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
    public function addFieldFilter($colm, $value) {
        if ($this->filter != "")
            $this->filter .= " AND ";
        $this->filter .= " " . $colm . " = '" . DbAccess3::escape($value) . "'";
    }

	public function addFieldInFilter($colm, $values, $not=false) {
		if ($this->filter != "")
			$this->filter .= " AND ";

		if(is_array($values)){
			$this->filter .= " " . $colm . ($not === true ? ' NOT' : '') . " IN ('" . implode("','", $values) . "')";
		}else {
			$this->filter .= " " . $colm . ($not === true ? ' NOT' : '') . " IN ( '" . DbAccess3::escape($values) . "')";
		}
	}
	public function addFieldOrInFilter($colm, $values, $not=false) {
		if ($this->filter != "")
			$this->filter .= " OR ";

		if(is_array($values)){
			$this->filter .= " " . $colm . ($not === true ? ' NOT' : '') . " IN ('" . implode("','", $values) . "')";
		}else {
			$this->filter .= " " . $colm . ($not === true ? ' NOT' : '') . " IN ( '" . DbAccess3::escape($values) . "')";
		}
	}

    public function addFieldNotFilter($colm, $value) {
        if ($this->filter != "")
            $this->filter .= " AND ";
        $this->filter .= " " . $colm . " != '" . DbAccess3::escape($value) . "'";
    }
    public function addOrFieldFilter($colm, $value) {
        if ($this->filter != "")
            $this->filter .= " OR ";
        $this->filter .= " " . $colm . " = '" . DbAccess3::escape($value) . "'";
    }
    public function addIsDeletedFilter() {
        if ($this->filter != "")
            $this->filter .= " AND ";
        $this->filter .= "    t.status != '0'";
    }
    
    
    public function join($joinTable, $where, $type = "") {
        $_where = [];
        $operand = '=';
        if (is_array($where)) {
            foreach ($where as $col => $val) {
                $_where[] = $col . " " . $operand . " " . DbAccess3::escape($val);
            }
            $_where = implode(" AND ", $_where);
        } else if (is_string($where)) {
            $_where = $where;
        }
        $this->join[] = $type . " JOIN " . $joinTable . " ON " . $_where;
    }
    
    
     public function leftJoin($joinTable, $where) {
        $this->join($joinTable, $where, "LEFT");
    }

    public function rightJoin($joinTable, $where) {
        $this->join($joinTable, $where, "RIGHT");
    }

    public function innerJoin($joinTable, $where) {
        $this->join($joinTable, $where, "INNER");
    }
}
?>
