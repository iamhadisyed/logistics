<?php

/*
 * Consignment Filter
 *
 */

class AgentRestrictedPostcodeFilter {

    private $filter = "";
    private $order_by = "";

    public function getList() {
        // has filter been configured?
        $where = "";
        if ($this->filter != "") {
            $where = "WHERE " . $this->filter;
        }
        $sort = "";
        if ($this->order_by != "")
            $sort = "ORDER BY " . $this->order_by;


     $sql = "SELECT *
				FROM agent_restricted_postcode
				$where
				$sort
				";

        return AgentRestrictedPostcode::getListSql($sql);
    }
	
	

    public function getColumnList($fields) {
        // has filter been configured?
        $where = "";
        if ($this->filter != "") {
            $where = "WHERE " . $this->filter; //substr($this->filter, 4);
        }
        $sort = "";
        if ($this->order_by != "")
            $sort = "ORDER BY " . $this->order_by;


        $sql = "SELECT id, " . $fields . "
				FROM agent_restricted_postcode
				$where
				$sort
				";

        return AgentRestrictedPostcode::getListSql($sql);

    }

    public function addFieldFilter($fieldName, $fieldValue) {
        if ($this->filter != "")
            $this->filter .= " AND ";
        $this->filter .= " " . $fieldName . "='" . DbAccess3::escape($fieldValue) . "' ";
    }

    public function addFieldIsNullFilter($fieldName) {
        if ($this->filter != "")
            $this->filter .= " AND ";
        $this->filter .= " ( " . $fieldName . " IS NULL OR " . $fieldName . " = '' )  ";
    }

    public function addQueryFilter($queryFilter) {
        if ($this->filter != "")
            $this->filter .= " AND ";
        $this->filter .= " " . $queryFilter . " ";
    }

    public function orderBySort($sorter) {
        $this->order_by .= $sorter;
    }


    public function setOffset($offset) {
        $this->pageOffset = $offset;
    }

    public function setRowsPerPage($rowsPP) {
        $this->rowsPerPage = $rowsPP;
    }

    public function setFilter($filter) {
        $this->filter = $filter;
    }
    

    public function insertData($query) {
        if (trim($query) != '') {
          
            return AgentRestrictedPostcode::runQuery($query);
        }
    }
    public function updateData($query) {
        if (trim($query) != '') {
             
            return AgentRestrictedPostcode::runQuery($query);
        }
    }
    
   
    public function delete($debug = false) {
        $where = "";
        if ($this->filter != "") {
            $where = "WHERE " . $this->filter;
        }
        $sql = "DELETE FROM agent_restricted_postcode $where";
        if ($debug) {
            echo $sql;
            die();
        }
        return AgentRestrictedPostcode::runQuery($sql);
    }

   

}
