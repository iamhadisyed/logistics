<?php

/*
 * MarketPlaces
 *
 */

class ProductMarketPlaceMappingFilter {

    private $filter = "";
    private $order_by = "";
    private $pageOffset = "";
    private $rowsPerPage = "";

    /**
     * Get list of MarketPlaces items based on filter conditions
     *
     * @return array[Sample]
     */
    public function getList($debug = false, $limit= 5000) {
        // has filter been configured?
        $where = "";
        if ($this->filter != "") {
            $where = "WHERE " . substr($this->filter, 4);
        }
        $sort = "";
        if ($this->order_by != "")
            $sort = "ORDER BY " . $this->order_by;

        $sql = "SELECT * FROM product_marketplace_mapping ap $where order by id desc LIMIT $limit";

        if($debug) {
            echo $sql;
            die;
        }

        t($sql, __METHOD__);
        return ProductMarketPlaceMapping::getProductMarketPlaceMappingListFromSql($sql);
    }
    
    public function AddOrderBy($name, $ascending = true) {
       	if (trim($name) != '')
            $this->order_by = $name . " " . ($ascending ? "" : " DESC");
    }
    
    public function AddGroupBy($name) {
        //echo $name;
       	if (trim($name) != '')
            $this->groupBy = $name;
        //echo $this->group_by;
    }
    public function getColumnList($fields, $recordLimit = "", $debug = false) {

        $fields = rtrim($fields, ",");
        // has filter been configured?
        $where = "";
        if ($this->filter != "") {
            $where = "WHERE " . substr($this->filter, 4);
        }
        $sort = "";
        if ($this->order_by != "")
            $sort = "ORDER BY " . $this->order_by;
        
        if ($this->join != "") {
            $Join = $this->join;
        }
        
        if ($recordLimit == ''){
            if($this->rowsPerPage > 0)
                $this->rowsPerPage = $this->rowsPerPage;
            else
                $this->rowsPerPage = 5000;
        }else if($recordLimit > 0){
            $this->rowsPerPage = $recordLimit;
        }


        if($this->pageOffset > 0)
            $this->pageOffset = $this->pageOffset;
        else
            $this->pageOffset = 0;



           $sql = "SELECT " . $fields . ", ord.id 
				FROM product_marketplace_mapping `ap`
                                $Join
				$where		
                                order by ap.id desc
                LIMIT $this->pageOffset , $this->rowsPerPage
				";
        if($debug){
            echo $sql; die;
        }
        return ProductMarketPlaceMapping::getProductMarketPlaceMappingListFromSql($sql);
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

    
    public function setRowsPerPage($rowsPP) {
        $this->rowsPerPage = $rowsPP;
    }
   
    public function setOffset($offset) {
        $this->pageOffset = $offset;
    }
  
    /*     * *
     * Oder by  id
     */

    public function AddOrderById($ascending = true) {
        $this->order_by = "id" . ($ascending ? "" : " DESC");
    }

   
	 public function addFieldFilter($colm, $value)
	{
             if($this->filter != '')
             {
		$this->filter .= " AND ";
             }
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
    
    public function addJoin($table,$where, $type = "INNER") {
        $this->join .= $type . " JOIN ".$table."  ON " . $where;
    }

    public function addFilterIn($field, $values) {
        if (trim($this->filter) != "") {
            $this->filter .= " AND ";
        }
        if (is_array($values)) {
            $this->filter .= $field ." IN ('" . implode("','", $values) . "')";
        } else {
            $this->filter .= $field . " IN (" . $values . ")";
        }
    }

}
