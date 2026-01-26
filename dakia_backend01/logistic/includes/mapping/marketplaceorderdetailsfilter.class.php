<?php

/*
 * MarketPlaces
 *
 */

class MarketPlaceOrderDetailsFilter {

    private $filter = "";
    private $order_by = "";

    /**
     * Get list of MarketPlaces items based on filter conditions
     *
     * @return array[Sample]
     */
    public function getList() {
        // has filter been configured?
        $where = "";
        if ($this->filter != "") {
            $where = "WHERE " . substr($this->filter, 4);
        }
        $sort = "";
        if ($this->order_by != "")
            $sort = "ORDER BY " . $this->order_by;

        $sql = "SELECT * FROM marketplace_order_details	$where order by title asc LIMIT 5000";

        t($sql, __METHOD__);
        return MarketPlaceOrderDetails::getMarketPlaceOrderDetailsPlatformListFromSql($sql);
    }

    public function GetOrderDetailByMarketPlaceId($marketplaceOrderId)
    {
        $sql = "select sku, title, item_price, currency, sum(quantity_purchased) 'quantity_purchased' from marketplace_order_details 
                where marketplace_order_id = " . $marketplaceOrderId . " group by sku";

        t($sql, __METHOD__);
        return MarketPlaceOrderDetails::getMarketPlaceOrderDetailsPlatformListFromSql($sql);
    }

    public function getColumnList($fields, $recordLimit = 5000,$debug = false) 
    {

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
				FROM marketplace_order_details
				$where
				order by title asc
				LIMIT " . $recordLimit . "
				";
        if($debug)
        {
            echo $sql; die;
        }
        return MarketPlaceOrderDetails::getMarketPlaceOrderDetailsPlatformListFromSql($sql);
    }
	
    /**
     * Get count of MarketPlaces items based on filter conditions
     *
     * @return int
     */
    public function getCount() {
        $result = $this->getList();
        return sizeof($result);
    }
   
 
	
	 public function addFieldFilter($colm, $value)
	{
		$this->filter .= " AND ";
		$this->filter .= " ".$colm." = '" . DbAccess3::escape($value) . "'";
	}	
	   public function addFilter($account_number) {
        //echo $account_number;
        if ($this->filter != "")
            $this->filter .= " AND ";
        $this->filter .= $account_number;
        //echo $this->filter;
        //exit;
    }

}
