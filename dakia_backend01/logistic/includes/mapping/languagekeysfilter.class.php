<?php

// get settings
//require_once("includes/settings/common.inc.php");
include_classes([
    'location.class'
]);
class LanguageKeysFilter
{
	private $filter = "";
	private $order_by = "";
	private $limit = 100;
	private $rowsPerPage = 0;
	private $pageOffset = 0;
	private $groupBy = "";
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
				FROM language_keys l
				$where
				$this->groupBy
				$sort ";
				
		//echo $sql;
		return LanguageKeys::getLanguageKeysListFromSql($sql);

		
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
		
		

		$sql = "SELECT count(distinct keyword) as total FROM language_keys l $where $sort ";
		
		
		//echo $sql;
		

		t($sql, __METHOD__);

		return Location::getTotalNumberOfLocationsFromSql($sql);

	}
	
	public function getPagingColumnList ($fields)
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
		$sql = "SELECT ".$fields." FROM language_keys l $where $this->groupBy $sort LIMIT $this->pageOffset , $this->rowsPerPage";
		
		
		//echo $sql;

		t($sql, __METHOD__);

		return LanguageKeys::getLanguageKeysListFromSql($sql);
	}


	public function getColumnList($fields)
	{
		// has filter been configured?
		$where = "";
		if ($this->filter != "") $where = "WHERE " . $this->filter;
		$sort = "";
				if ($this->order_by != "") $sort = "ORDER BY " . $this->order_by;
		 $sql = "SELECT  id, ".$fields."  FROM language_keys l  $where  $this->groupBy  $sort "; 
		
		//mail("mkazim4u@gmail.com", "sql",  $sql);
		
		return LanguageKeys::getLanguageKeysListFromSql($sql);
	}
	
	public function addLanguageFilter($language)
	{
		if ($this->filter != "") $this->filter .= " AND ";
		$this->filter .= "l.language ='" .DbAccess3::escape( $language) . "'";	
	}
	
	public function addKeywordFilter($keyword)
	{
		if ($this->filter != "") $this->filter .= " AND ";
		$this->filter .= "l.keyword ='" . DbAccess3::escape($keyword) . "'";	
	}

    public function addKeywordFilterL($keyword)
    {
        if ($this->filter != "") $this->filter .= " AND ";
        $this->filter .= "keyword ='" . DbAccess3::escape($keyword) . "'";
    }
	
	public function addCaptionFilter($caption)
	{
		if ($this->filter != "") $this->filter .= " AND ";
		$this->filter .= "l.caption = '" .DbAccess3::escape( $caption) . "'";	
	}
	
	
	public function addKeywordLikeFilter($keyword)
	{
		if ($this->filter != "") $this->filter .= " AND ";
		$this->filter .= "l.keyword like '%" . DbAccess3::escape($keyword ). "%'";	
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

	
	public function addGroupByFilter($field_name)
	{
		$this->groupBy = " group by " . $field_name;
	}

    public function addOrderBy($columnName = "id", $ascending = true) {
        if ($this->order_by != "")
            $this->order_by .= ", ";
        //
        $this->order_by .= $columnName . ($ascending ? "" : " DESC");
    }

    public function delete($debug = false)
    {
        // has filter been configured?
        $where = "";
        $sql = '';
        if ($this->filter != "")
        {
            $where = "WHERE " . $this->filter;//substr($this->filter, 4);
        }
        if(!empty($where)) {
            $sql = "DELETE FROM language_keys $where";
        }
        if ($debug) {
            echo $sql;
            die();
        }
        DbAccess3::runQuery($sql);
    }
	
}  // class
?>