<?php

// get settings
class RackShelfItemFilter {

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

        $sql = "SELECT *
                    FROM rack_shelf_item rsi                    
                    $where
                    Order by rsi.id";
		
		//echo $sql;			
					
        return RackShelfItem::getRackShelfItemListFromSql($sql);
    }
	
	public function addTrackingNumberFilter($trackingnumber) 
	{
		//$this->filter .= " AND ";
        $this->filter .= "rsi.tracking_number = '" . DbAccess3::escape($trackingnumber) . "'";
    }
	
	public function getListOfRackItemsByNumberOfDays($days) 
	{
		$sql = "select * from rack_shelf_item where id in(
			    select rack_shelf_item_id from log_rack_shelf where in_date < now() - interval $days day and out_date is null
				) and tracking_number != ''";
				
		echo $sql;		
		
		return RackShelfItem::getRackShelfItemListFromSql($sql);		
					
		//$this->filter .= " AND ";
        //$this->filter .= " added_date < now() - interval $days day and tracking_number != '' ";
    }
	
	
    public function getColumnList($fields) {
        $where = "";
        if ($this->filter != "")
            $where = "WHERE " . $this->filter;
        $sql = "SELECT id, " . $fields . " FROM rack_shelf_item rsi $where Order by rsi.id";
        return LogRackShelf::getRackShelfItemListFromSql($sql);
    }

    
}

// class
?>