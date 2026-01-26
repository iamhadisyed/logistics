<?php

////////////////////////////////////////////////////
//
// Class for dealing with consignment_charges
//
////////////////////////////////////////////////////
/**
 * ConsignmentCharges - ConsignmentCharges class
 * @package Courier
 */
class ConsignmentChargesFilter{
    private $filter = "";
    private $order_by = "";
    private $limit = 100;
    private $rowsPerPage = 0;
    private $pageOffset = 0;
    private $join = "";

    public function getPagingCount() {
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
        $sql = "SELECT count(t.id) as total FROM consignment_charges cc $checkJoin $where $sort ";
        t($sql, __METHOD__);
        return ConsignmentCharges::getTotalNumberOfConsignmentChargesFromSql($sql);
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

        $sql = "SELECT " . $columns . " FROM consignment_charges cc $checkJoin $where $sort LIMIT $this->pageOffset , $this->rowsPerPage";
        t($sql, __METHOD__);
        return ConsignmentCharges::getConsignmentChargesListFromSql($sql);
    }

    public function getList($columnName = "*",$debug = false) {
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

         $sql = "SELECT $columnName
                FROM consignment_charges cc
                $checkJoin
                $where
                $sort
                ";
        if($debug){
            echo $sql."<br>";
          //  die;
        }
        return ConsignmentCharges::getConsignmentChargesListFromSql($sql);
    }
    public function getColumnList($fields, $recordLimit = 5000, $debug = false) {

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
        if ($this->join != "")
            $checkJoin = $this->join;

        $sql = "SELECT " . $fields . ", cc.id
				FROM consignment_charges cc
				$checkJoin
				$where
				$sort
				LIMIT " . $recordLimit . "
				";
//        echo $sql; die();
        if($debug){
            echo $sql;
            die;
        }
        return ConsignmentCharges::getConsignmentChargesListFromSql($sql);
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
        $this->order_by .= $columnName . ($ascending ? " ASC" : " DESC");
    }
    
    public function groupBy($columnName) {
        if(is_array($columnName))
            $this->groupBy = " group by ".implode(", ",$columnName);
        else  if(trim($columnName)!= '')
            $this->groupBy = " group by ".$columnName;
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
    public function addIsDeletedFilter() {
        if ($this->filter != "")
            $this->filter .= " AND ";
        $this->filter .= "    t.status != '0'";
    }
    public function addFieldNotNullFilter($colm) {
        if ($this->filter != "")
            $this->filter .= " AND ";
        $this->filter .= "  " . $colm . " IS NOT NULL ";
    }
    public function addFieldNullFilter($colm) {
        if ($this->filter != "")
            $this->filter .= " AND ";
        $this->filter .= " " . $colm . " IS NULL";
    }
    public function addExtraChargesFilter($colm) {
        if ($this->filter != "")
            $this->filter .= " AND ";
        $this->filter .= "  " . $colm . " > 0";
    }
    
    
    public function whereIn($field, $values, $not = false) {
        if (is_array($values)) {
            $this->filter  = $field . ($not === true ? ' NOT' : '') . " IN ('" . implode("','", $values) . "')";
        } else {
            $this->filter = $field . ($not === true ? ' NOT' : '') . " IN (" . $values . ")";
        }
    }

    
    
    public function addConsignmentChargesTypeJoin() {
        $this->join .= " LEFT JOIN `consignment_charges_types` cct  ON cct.id = cc.charge_type_id ";
    }
    
    public static function checkAccountPostPaidCurrency($userAccountId = 0) {
        $sql = "SELECT 
                    SUM(c.`cost`) AS id 
                  FROM
                    `consignment_charges` c 
                  WHERE c.`invoice_id` > 0 
                    AND c.`account_id` IN ($userAccountId)";
        $return = ConsignmentCharges::getConsignmentChargesListFromSql($sql);
        $isBool = false;
        if(count($return) > 0){
            if(!empty($return[0]->getId())){
                $isBool = true;
            }
        }
        return $isBool;
    }
}
?>
