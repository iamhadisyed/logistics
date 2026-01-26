<?php

// get settings
//require_once("includes/settings/common.inc.php");

class RemoteareaUserMappingFilter
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
				FROM remotearea_user_mapping rum
				$where
				 $sort ";

		return RemoteareaUserMapping::getRemoteareaUserMappingListFromSql($sql);
	}


	public function getColumnList($fields)
	{
		// has filter been configured?
		$where = "";
		if ($this->filter != "") $where = "WHERE " . $this->filter;
		$sort = "";
				if ($this->order_by != "") $sort = "ORDER BY " . $this->order_by;
		$sql = "SELECT id, ".$fields." FROM remotearea_user_mapping rum  $where  $sort "; 
		
		
		return RemoteareaUserMapping::getRemoteareaUserMappingListFromSql($sql);
	}

public function getColumnDistinctList($fields)
	{
		// has filter been configured?
		$where = "";
		if ($this->filter != "") $where = "WHERE " . $this->filter;
		$sort = "";
				if ($this->order_by != "") $sort = "ORDER BY " . $this->order_by;
		$sql = "SELECT  ".$fields." FROM remotearea_user_mapping rum  $where  $sort "; 
		
		
		return RemoteareaUserMapping::getRemoteareaUserMappingListFromSql($sql);
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

		$sql = "SELECT count(*) as total FROM remotearea_user_mapping rum $where $sort ";

		t($sql, __METHOD__);

		return RemoteareaUserMapping::getRemoteareaUserMappingListFromSql($sql);

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


		 $sql = "SELECT * FROM remotearea_user_mapping rum $where $sort LIMIT $this->pageOffset , $this->rowsPerPage";
			
			

			t($sql, __METHOD__);

			return RemoteareaUserMapping::getRemoteareaUserMappingListFromSql($sql);
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
	/**
	 * Filter on given user/password combo
	 *
	 * @param string or array giving $service_type
	 */
	public function addFieldFilter($field,$value)
	{
		if ($this->filter != "") $this->filter .= " AND ";
		$this->filter .= $field."='". DbAccess3::escape($value) . "'";
	}
	public function addFieldInFilter($field,$value)
	{
		if ($this->filter != "") $this->filter .= " AND ";
		$this->filter .= $field." IN ('". implode("','",$value) . "')";
	}
	
	
	/**
	* expunge. Real delete
	* @return void
	*/
	public function expunge()
	{
		$where = "";
		if ($this->filter != "")
		{
			$where = "WHERE " . $this->filter;
		}
	 	$sql = "DELETE FROM remotearea_user_mapping $where ";
		return $sql;
		//return DbAccess3::runQuery($sql);
	}
}  // class
?>