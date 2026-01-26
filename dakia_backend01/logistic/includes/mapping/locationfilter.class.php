<?php

// get settings
//require_once("includes/settings/common.inc.php");

class LocationFilter
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
				FROM location l
				$where
				 $sort ";

		return Location::getLocationListFromSql($sql);
	}


	public function getColumnList($fields)
	{
		// has filter been configured?
		$where = "";
		if ($this->filter != "") $where = "WHERE " . $this->filter;
		$sort = "";
				if ($this->order_by != "") $sort = "ORDER BY " . $this->order_by;
		$sql = "SELECT  id, ".$fields."  FROM location l  $where  $sort "; 
		

		//mail("mkazim4u@gmail.com", "location", $sql);
		
		return Location::getLocationListFromSql($sql);
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

				$sql = "SELECT count(*) as total FROM location l $where $sort ";
				
				
				//echo $sql;
				

				t($sql, __METHOD__);

				return Location::getTotalNumberOfLocationsFromSql($sql);

			}

			public function getPagingList ()
			{
					// has filter been configured?
					$where = "";
					if ($this->filter != "")
					{
						$where = "WHERE " . $this->filter;//substr($this->filter, 4);
					}
					$sort = "";
					if ($this->order_by != "") $sort = " ORDER BY " . $this->order_by;


				 $sql = "SELECT * FROM location l $where $sort LIMIT $this->pageOffset , $this->rowsPerPage";
					
					

					t($sql, __METHOD__);

					return User::getLocationListFromSql($sql);
			}
			
			public function getPagingColumnList ( $fields)
			{
					// has filter been configured?
					$where = "";
					if ($this->filter != "")
					{
						$where = "WHERE " . substr($this->filter, 4);
					}
					$sort = "";
					if ($this->order_by != "") $sort = " ORDER BY " . $this->order_by;


					$sql = "SELECT ".$fields." FROM location l $where $sort LIMIT $this->pageOffset , $this->rowsPerPage";
					
					

					t($sql, __METHOD__);

					return User::getLocationListFromSql($sql);
			}


			public function setOffset($offset)
			{
				$this->pageOffset = $offset;
			}

			public function setRowsPerPage($rowsPP)
			{
				$this->rowsPerPage = $rowsPP;
			}
	/**
	 * Get count of user items based on filter conditions
	 *
	 * @return int
	 */
	public function getCount()
	{
		$result = $this->getList();
		return sizeof($result);
	}
	
	public function addWarehouseIdFilter($warehouseid)
	{
		if ($this->filter != "") $this->filter .= " AND ";
		$this->filter .= "l.warehouseid ='" .DbAccess3::escape( $warehouseid ). "'";
	}
	
	public function addLocationNameFilter($name)
	{
		if ($this->filter != "") $this->filter .= " AND ";
		$this->filter .= "l.name='" . DbAccess3::escape($name) . "'";
	}
	
	public function addLocationNameLikeFilter($name)
	{
		if ($this->filter != "") $this->filter .= " AND ";
		$this->filter .= "l.name like '%" . DbAccess3::escape($name ). "%'";
	}
	
	public function addReturnsLocationNameLikeFilter($not)
	{
		if ($this->filter != "") $this->filter .= " AND ";
		$this->filter .= "l.name $not like '%Returns" . DbAccess3::escape($name ). "%'";
	}
	
	public function AddTypeFilter($type = 'L')
	{
		if ($this->filter != "") $this->filter .= " AND ";
		$this->filter .= "l.type='" . DbAccess3::escape($type) . "'";	
	}
	
	public function AddActiveFilter($active = 'Y')
	{
		if ($this->filter != "") $this->filter .= " AND ";
		$this->filter .= "l.active='" . DbAccess3::escape($active) . "'";	}



	
}  // class
?>