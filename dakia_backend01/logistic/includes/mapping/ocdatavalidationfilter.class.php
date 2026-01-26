<?php
/*
 * Consignment Filter
 *
 */
class OcDataValidationFilter
{
	private $filter = "";
	private $order_by = "";
	private $group_by = "";
	private $join= "";

        
    /* Najam Code */    
    public function getPagingCount() {
        // has filter been configured?
        $where = "";
        if ($this->filter != "") {
            $where = "WHERE " . substr($this->filter, 4);
        }
        $sort = "";
        if ($this->order_by != "")
            $sort = "ORDER BY " . $this->order_by;
        $groupBy = "";
        if ($this->groupby != "")
            $groupBy = " GROUP BY " . $this->groupby;
        $checkJoin = "";
        if ($this->join != "")
            $checkJoin = $this->join;

        $sql = "SELECT COUNT(id) AS total FROM  `bagging` b $where $groupBy $sort ";
        t($sql, __METHOD__);
        return OcDataValidation::getTotalNumberOfBaggingFromSql($sql);
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
        else
            $sort = "ORDER BY b.id DESC";
        $groupBy = "";
        if ($this->groupby != "")
            $groupBy = " GROUP BY " . $this->groupby;
        $checkJoin = "";
        if ($this->join != "")
            $checkJoin = $this->join;
        
       $sql = "SELECT " . $columns . "  FROM `oc_data_validation` b $where $groupBy $sort LIMIT $this->pageOffset , $this->rowsPerPage";
       t($sql, __METHOD__);
        return OcDataValidation::getOcDataValidationListFromSql($sql);
    }
    
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
    
    public function addFieldFilter($colm, $value) {
        if ($this->filter != "")
            $this->filter .= " AND ";
        $this->filter .= " " . $colm . " = '" . DbAccess3::escape($value) . "'";
    }
    
    public function addJoin($table,$joinField,$fromJoinField,$type="LEFT JOIN") {
        $this->join .= " " . $type ." " . $table . " " . "  ON " . $joinField . "  =  " . $fromJoinField;
    }
    
    public function addGroupBy($colm) {
        if ($this->groupby != "")
            $this->groupby .= " , ";
        $this->groupby .= "  " . $colm . " ";
    }
	
	public function getList( $debug = false)
	{
		// has filter been configured?
		$where = "";
		if ($this->filter != "")
		{
			$where = "WHERE " . substr($this->filter, 4);
		}
		$sort = "";
		if ($this->group_by != "") $sort  = "GROUP BY " . $this->group_by;
		if ($this->order_by != "") $sort .= "ORDER BY " . $this->order_by;	

		$sql = "SELECT *
				FROM oc_data_validation b
				$where
				$sort limit 100
				";
                if($debug){
                    echo $sql;
                    die;    
                }
		return OcDataValidation::getOcDataValidationListFromSql($sql);
	}
    	
	public function addFilter ($type)
    {		
		$this->filter .= $type;		
    }
	
	 public function addFieldEqualFilter($columnName, $operator, $columnValue) {
        $this->filter .= " AND ";
        $this->filter .= "o." . $columnName . " " . $operator . " '" . DbAccess3::escape($columnValue) . "'";
    }
	

	
	public function addDateFilter ($date)
    {		
		$this->filter .= " AND date_created >= '$date' AND date_created <= ' " . date("Y-m-d 23:59:59") . "'";		
    }
    public function addOrderByField ($field, $desc = 'desc')
    {  
  		$this->order_by =  "$field $desc";  
    }
		
	public function getColumnList($fields)
	{
	    $selectColumn = 'id,'.$fields;
	    if(trim($fields) == "*"){
            $selectColumn = trim($fields);
        }
		// has filter been configured?
		$where = "";
		if ($this->filter != "")
		{
			$where = "WHERE " . substr($this->filter, 4);
		}
		$sort = "";
		if ($this->group_by != "") $sort  = "GROUP BY " . $this->group_by;
		if ($this->order_by != "") $sort .= "ORDER BY " . $this->order_by;	

		$sql = "SELECT ".$selectColumn."
				FROM oc_data_validation o
				$where
				$sort
				";
		return OcDataValidation::getOcDataValidationListFromSql($sql);
	}	
	

    public function addDateCreatedFilter($date_value1, $date_value2){
        if($date_value1 != ''){
            if(!empty($this->filter))
                $this->filter .= " AND ";
            $this->filter .= "DATE(o.date_created)>='" . date('Y-m-d', strtotime($date_value1)) . "'";
        }
        if($date_value2 != ''){
            $this->filter .= " AND ";
            $this->filter .= "DATE(o.date_created)<='" . date('Y-m-d',strtotime($date_value2)) . "'";
        }
    }
    
    public function addFilterIn($field, $values) {
        if (is_array($values)) { 
            if ($this->filter != "") {
                $this->filter .= " AND ";
            }
            $this->filter .= $field ." IN (" . implode(",", $values) . ")";
        } else {
            if ($this->filter != "") {
                $this->filter .= " AND ";
            }
            $this->filter .= $field . " IN ('" . $values . "')";
        }
    }
    
   
}