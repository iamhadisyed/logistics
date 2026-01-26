<?php
/*
 * Consignment Filter
 *
 */
class ConsignmentRelabelFilter
{
	private $filter = "";
	private $order_by = "";
	private $group_by = "";

	
	public function getList()
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
				FROM consignment_relabel c
				$where
				$sort limit 100
				";
				
//		echo $sql;die;

		t($sql, __METHOD__);
		

		

		return ConsignmentRelabel::getManifestListFromSql($sql);
	}
	
	
	
	public function addGroupBy ($field = 'account')
    {		
		$this->group_by =  " $field";		
    }
	
    public function getColumnList($fields, $debug = false) {
        // has filter been configured?
        $where = "";
        if ($this->filter != "") {
            $where = "WHERE " . substr($this->filter, 4);
        }
        $sort = "";
        if ($this->group_by != "") $sort  = "GROUP BY " . $this->group_by;
        if ($this->order_by != "") $sort .= "ORDER BY " . $this->order_by;
        
        $sql = "SELECT id, ".$fields."
                        FROM consignment_relabel c
                        $where
                        $sort
                        ";
        if($debug){
            echo $sql ;
            die;
        }
        return ConsignmentRelabel::getManifestListFromSql($sql);
    }	
    public function getPagingCount() {
        // has filter been configured?
        $where = "";
        if ($this->filter != "") {
            $where = "WHERE " . substr($this->filter, 4);
        }
        $sort = "";
        if ($this->order_by != "")
            $sort = "ORDER BY " . $this->order_by;

        $sql = "SELECT count(cr.id) as total FROM consignment_relabel cr $checkJoin $where $sort ";
        t($sql, __METHOD__);
        return ConsignmentRelabel::getTotalNumberOfConsignmentRelabelFromSql($sql);
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
         
       $sql = "SELECT " . $columns . " $joinColumnName FROM consignment_relabel cr   $where $sort LIMIT $this->pageOffset , $this->rowsPerPage";
        return ConsignmentRelabel::getManifestListFromSql($sql);
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
    public function addFilter($colm) {
        if ($this->filter != "")
            $this->filter .= " AND ";
        $this->filter .= "     " . $colm ;
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
    public function AddOrderBy($columnName = "id", $ascending = true) {
        if ($this->order_by != "")
            $this->order_by .= ", ";
        //
        $this->order_by .= $columnName . ($ascending ? "" : " DESC");
    }
    public function addOldParcelTrackingNoFindInSetFilter($trackingNumber) {
        if ($this->filter != "")
            $this->filter .= " AND   ";
        $this->filter .= "      (FIND_IN_SET('".DbAccess3::escape($trackingNumber)."',old_parcel_tracking_no) OR old_tracking_no='".DbAccess3::escape($trackingNumber)."' )";
    }
	
	
}