<?php

// get settings
class LogRackShelfFilter {

    private $filter = "";

    /**
     * Get list of user items based on filter conditions
     *
     * @return array[User]
     */
    public function getList() {
        // has filter been configured?
        if ($this->filter != "")
            $where = "WHERE " . $this->filter;

        $sql = "SELECT lrs.*
                    FROM log_rack_shelf lrs                    
                    $where
                    Order by lrs.id";
        return LogRackShelf::getLogRackShelfListFromSql($sql);
    }
	
	
    public function getColumnList($fields) {
        $where = "";
        if ($this->filter != "")
            $where = "WHERE " . $this->filter;
        $sql = "SELECT id, " . $fields . " FROM log_rack_shelf lrs $where Order by lrs.id";
        return LogRackShelf::getLogRackShelfListFromSql($sql);
    }

    public function getCurrentShelfItem($shelf_id){
        $sql = "SELECT 
                    lrs.id AS log_shelf_id,
                    lrs.customer_id,
                    lrs.`in_date`,
                    lrs.`in_by`,
                    lrs.`out_date`,
                    lrs.`out_by`,
                    rsi.*                    
                  FROM
                    log_rack_shelf lrs 
                    join rack_shelf_item rsi 
                      on lrs.`rack_shelf_item_id` = rsi.`id` 
                  WHERE lrs.`rack_shelf_id` = '".DbAccess3::escape($shelf_id)."'
                  ORDER BY lrs.id DESC 
                  LIMIT 1";
        t($sql, __METHOD__);
		
		//mail("mkazim4u@gmail.com", "query", $sql);
        return LogRackShelf::getRecordsFromSql($sql);
    }
	
	public function addRackShelfItemId($rack_shelf_item_id){
        $this->filter .= " rack_shelf_item_id = ". DbAccess3::escape($rack_shelf_item_id);
		
        //t($sql, __METHOD__);
		
		//mail("mkazim4u@gmail.com", "query", $sql);
        //return LogRackShelf::getRecordsFromSql($sql);
    }
	
	
    public function getShelfLabelInfo($shelf_id){
        $sql = "SELECT 
                    lrs.id AS log_shelf_id,
                    lrs.customer_id,
                    lrs.`in_date`,
                    lrs.`in_by`,
                    lrs.`out_date`,
                    lrs.`out_by`,
                    rsi.*,
                    rs.`rack_id`,
                    rs.`shelf_no`,
                    r.`title`,
                    r.`short_title` 
                  FROM
                    log_rack_shelf lrs 
                    JOIN rack_shelf_item rsi 
                      ON lrs.`rack_shelf_item_id` = rsi.`id` 
                    JOIN rack_shelf rs ON rs.`id` = lrs.`rack_shelf_id`  
                    JOIN rack r ON r.`id` = rs.`rack_id`
                  WHERE lrs.`rack_shelf_id` = '".DbAccess3::escape($shelf_id)."' 
                  ORDER BY lrs.id DESC 
                  LIMIT 1 ";
        t($sql, __METHOD__);
        return LogRackShelf::getRecordsFromSql($sql);
    }
    public function addFilter($filter) {
        $this->filter .= $filter;
    }
    /**
     * Get count of user items based on filter conditions
     *
     * @return int
     */
    public function getCount() {
        $result = $this->getList();
        return sizeof($result);
    }
	
	public function getTotalItemsInShelf($shelf_id)
	{
		$sql = "select count(*) 'total' from log_rack_shelf where rack_shelf_id = '".DbAccess3::escape($shelf_id)."'";
		$rs = DbAccess3::runQuery($sql);		
				
		while ($row = mysqli_fetch_assoc($rs)) 
		{
			$r = $row['total'];
		}		
		
		return $r;
	}
	
	
	public function getLogItemsInShelf($shelf_id)
	{
		$sql = "select id from log_rack_shelf where rack_shelf_id = '".DbAccess3::escape($shelf_id)."' and out_date is null";
		$rs = DbAccess3::runQuery($sql);		
		$r = array();
				
		while ($row = mysqli_fetch_assoc($rs)) 
		{
			$r[] = $row['id'];
		}		
		
		return $r;
	}
	
	
	
}

// class
?>