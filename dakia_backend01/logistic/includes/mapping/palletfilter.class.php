<?php
/*
 * Consignment Filter
 *
 */
class PalletFilter
{
	private $filter = "";
	private $order_by = "";
	private $group_by = "";
	private $join = "";

	
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
				FROM pallet p
				$where
				$sort limit 100
				";
		t($sql, __METHOD__);
		return Pallet::getPalletListFromSql($sql);
	}
	

	
	public function addDateRangeFilter ($date1, $date2)
    {		
		$this->filter .= " AND date_format(date_dispatch,'%Y-%m-%d') >= '$date1' AND date_format(date_dispatch,'%Y-%m-%d') <= '$date2'";		
    }
	
	public function addPalletNumberFilter ($palletno)
    {		
		$this->filter .= " AND palletno = '".DbAccess3::escape($palletno)."'";		
    }

	
    public function addDateDispatchIsNull() {
                    $this->filter .= " AND p.date_dispatch IS NULL";
    }	
	
    public function addDateDispatchIsNotNull() {
                    $this->filter .= " AND p.date_dispatch IS NOT NULL";
    }
    
    public function addDispatchUserIdIsNull() {
                    $this->filter .= " AND (p.dispatch_userid IS NULL OR p.dispatch_userid = 0 )";
    }	
	
	
	public function getColumnList($fields,$debug=false)
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

		$sql = "SELECT id, ".$fields."
				FROM pallet p
				$where
				$sort
				";
                if($debug){
                    echo '<pre>';
                    print_r($sql);
                    echo '</pre>';
                    die;
                }
		return Pallet::getPalletListFromSql($sql);
	}	
    
         public function getPagingCount() {
        // has filter been configured?
        $where = "";
        if ($this->filter != "") {
            $where = "WHERE  " . $this->filter; //substr($this->filter, 4);
        }
        $sort = "";
        if ($this->order_by != "")
            $sort = " ORDER BY " . $this->order_by;
        else
            $sort = " ORDER BY p.id DESC";
        $checkJoin = "";
        if ($this->join != "")
            $checkJoin = $this->join;
        
        $joinColumnName = "p.id";
        if (strpos($checkJoin, 'join_pem') !== false) {
            $joinColumnName = "p.id,p.palletno,p.date_dispatch,p.close,p.pallet_carrier_id,p.hub,p.label,GROUP_CONCAT(b.`bagnumber`SEPARATOR '<br />') AS comments";
        }

        $sql = "SELECT count(id) AS total FROM ( SELECT p.id FROM pallet p $checkJoin $where GROUP BY p.`id`) AS pallet";
        t($sql, __METHOD__);

        return Pallet::getTotalNumberOfPalletFromSql($sql);
    }

    public function getPagingList() {
        // has filter been configured?
        $where = "";
        if ($this->filter != "") {
            $where = "WHERE  " . $this->filter; //substr($this->filter, 4);
        }
        $sort = "";
        if ($this->order_by != "")
            $sort = " ORDER BY " . $this->order_by;
        else
            $sort = " ORDER BY p.id DESC";
        $checkJoin = "";
        if ($this->join != "")
            $checkJoin = $this->join;
        
        $joinColumnName = "p.*";
        if (strpos($checkJoin, 'join_pem') !== false) {
            $joinColumnName = "p.id,p.palletno,p.date_dispatch,p.close,p.pallet_carrier_id,p.hub,p.label,GROUP_CONCAT(b.`bagnumber`SEPARATOR '<br />') AS comments";
        }

        $sql = "SELECT $joinColumnName FROM pallet p $checkJoin $where GROUP BY p.`id` $sort  LIMIT $this->pageOffset , $this->rowsPerPage";
        return Pallet::getPalletListFromSql($sql);
    }
    
    public function setOffset($offset) {
        $this->pageOffset = $offset;
    }

    public function setRowsPerPage($rowsPP) {
        $this->rowsPerPage = $rowsPP;
    }
    
     public function getCount() {
        $result = $this->getList();
        return sizeof($result);
    }
    public function addFieldFilter($colm, $value) {
        if ($this->filter != "")
            $this->filter .= " AND ";
        $this->filter .= " " . $colm . " = '" . DbAccess3::escape($value) . "'";
    }
    public function addOrFieldFilter($colm, $value) {
        if ($this->filter != "")
            $this->filter .= " OR ";
        $this->filter .= " " . $colm . " = '" . DbAccess3::escape($value) . "'";
    }
    public function addFieldFindInSet($colm, $value) {
        if ($this->filter != "")
            $this->filter .= " AND ";
        $this->filter .= " FIND_IN_SET('" . DbAccess3::escape($value) . "'," . $colm . " )";
    }
    public function addBagJoin() {
        $this->join.= " LEFT JOIN `pallet_entity_mapping` join_pem  ON join_pem.pallet_id = p.id AND join_pem.`pallet_entity_type` = 'b' LEFT JOIN `bagging` b ON b.id = join_pem.entity_id";
    }
    public function addDateFilter($date_value1, $date_value2){
        if($date_value1 != ''){
            $this->filter .= " AND ";
            $this->filter .= "(p.date_dispatch>='" . date('Y-m-d 00:00:00', strtotime($date_value1)) . "'";
        }
        if($date_value2 != ''){
            $this->filter .= " AND ";
            $this->filter .= "p.date_dispatch<='" . date('Y-m-d 23:59:59',strtotime($date_value2)) . "')";
        }
    }
    public function addDateCreatedFilter($date_value1, $date_value2){
        if($date_value1 != ''){
            if(!empty($this->filter))
                $this->filter .= " AND ";
            $this->filter .= "DATE(p.date_created)>='" . date('Y-m-d', strtotime($date_value1)) . "'";
        }
        if($date_value2 != ''){
            $this->filter .= " AND ";
            $this->filter .= "DATE(p.date_created)<='" . date('Y-m-d',strtotime($date_value2)) . "'";
        }
    }
    public function AddOrderBy($columnName = "id", $ascending = true) {
        if ($this->order_by != "")
            $this->order_by .= ", ";
        $this->order_by .= $columnName . ($ascending ? "" : " DESC");
    }

    public function checkPalletDispatchClose($palletNo) {
        $sql = "SELECT 
                    id,
                    date_dispatch 
                  FROM
                    pallet p 
                  WHERE palletno ='". DbAccess3::escape($palletNo) ."' AND (
                                                                        p.date_dispatch != '' 
                                                                        OR `close` = '1'
                                                                      )";
        return Pallet::getPalletListFromSql($sql);
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