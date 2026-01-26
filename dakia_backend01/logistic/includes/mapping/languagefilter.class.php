<?php

// get settings
//require_once("includes/settings/common.inc.php");
class LanguageFilter
{
	private $filter = "";
	private $order_by = "";
	private $limit = 100;
	private $rowsPerPage = 0;
	private $pageOffset = 0;
	/**
	 * Get list of user items based on filter conditions
	 *
	 * @return array[User]
	 */
	public function getList()
	{
		// has filter been configured?
		$where = "";
		if ($this->filter != "") $where = "WHERE " . $this->filter;
		$sort = "";
                if ($this->order_by != "") $sort = "ORDER BY " . $this->order_by;
                
		$sql = "SELECT *
				FROM language l
				$where
				$sort ";
				
//		echo $sql; die;
				
		return Languages::getLanguageListFromSql($sql);

		
	}
	
	public function getPagingCount()
	{
		// has filter been configured?
		$where = "";
		if ($this->filter != "")
		{
			$where = "WHERE " . $this->filter;//substr($this->filter, 4);
		}
		$sort = "";
		if ($this->order_by != "") $sort = "ORDER BY " . $this->order_by;

		$sql = "SELECT count(*) as total FROM language l $where $sort ";
		
		
		//echo $sql;
		

		t($sql, __METHOD__);

		return Languages::getTotalNumberOfLanguageFromSql($sql);

	}
	
	public function getPagingColumnList ( $fields)
	{
					// has filter been configured?
					
		//echo "rows per page " . $this->rowsPerPage;			
		$where = "";
		if ($this->filter != "")
		{
			$where = "WHERE " . $this->filter;//substr($this->filter, 4);
			//$where = "WHERE " . substr($this->filter, 4);
		}
		$sort = "";
	   if ($this->order_by != "") 
	       $sort = " ORDER BY " . $this->order_by;
		$sql = "SELECT ".$fields." FROM language l $where $sort LIMIT $this->pageOffset , $this->rowsPerPage";
		
		
		//echo $sql;			
					
		t($sql, __METHOD__);

		return Languages::getLanguageListFromSql($sql);
	}


	public function getColumnList($fields)
	{
		// has filter been configured?
		$where = "";
		if ($this->filter != "") $where = "WHERE " . $this->filter;
		$sort = "";
				if ($this->order_by != "") $sort = "ORDER BY " . $this->order_by;
		$sql = "SELECT  id, ".$fields."  FROM language l  $where  $sort "; 
		
		//echo $sql;
		
		return Languages::getLanguageListFromSql($sql);
	}
	
	public function addLanguageFilter($language)
	{
		if ($this->filter != "") $this->filter .= " AND ";
		$this->filter .= "l.language ='" . $language . "'";	
	}
	

	public function setOffset($offset)
	{
		$this->pageOffset = $offset;
	}

	public function setRowsPerPage($rowsPP)
	{
		$this->rowsPerPage = $rowsPP;
		//echo "settig " . $this->rowsPerPage;
	}	
	
	public function AddActiveFilter($active = 'Y')
	{
		if ($this->filter != "") $this->filter .= " AND ";
		$this->filter .= "l.is_active='" . $active . "'";	
	}
	
	public function AddOrderByLanguage($ascending = true)
	{
		if ($this->order_by != "") $this->order_by .= ", ";
		//
		$this->order_by .= "language" . ($ascending ? "" : " DESC");
	}
	
	public function AddOrderById($ascending = true)
	{
		if ($this->order_by != "") $this->order_by .= ", ";
		//
		$this->order_by .= "id" . ($ascending ? "" : " DESC");
	}

    public function addFilter($filterVal) {
        if ($this->filter != "")
            $this->filter .= "    AND ";

        if(!empty($filterVal)) {
            $this->filter .= "    " . $filterVal . " ";
        }
    }
	
}  // class
?>