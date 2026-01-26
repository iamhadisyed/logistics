<?php

// get settings
//require_once("../includes/settings/config.inc.php");

class TrackingEstimatedTimeFilter
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
				FROM tracking_estimated_time t
				$where
				 $sort ";
		return TrackingEstimatedTime::getTrackingEstimatedTrackingListFromSql($sql);
	}


	
	public function getColumnList($fields)
	{
		// has filter been configured?
		$where = "";
		if ($this->filter != "") $where = "WHERE " . $this->filter;
		$sort = "";
				if ($this->order_by != "") $sort = "ORDER BY " . $this->order_by;
		$sql = "SELECT id, ".$fields."
				FROM tracking_estimated_time t
				$where
				 $sort ";
		return TrackingEstimatedTime::getTrackingEstimatedTrackingListFromSql($sql);
	}
	

public function getPagingCount()
			{
				// has filter been configured?
				$where = "";
				if ($this->filter != "")
				{
					$where = "WHERE " . substr($this->filter, 4);
				}
				$sort = "";
				if ($this->order_by != "") $sort = "ORDER BY " . $this->order_by;

				$sql = "SELECT count(*) as total FROM tracking_estimated_time t $where $sort ";

				t($sql, __METHOD__);

				return TrackingEstimatedTime::getTrackingEstimatedTrackingListFromSql($sql);

			}

			public function getPagingList ()
			{
					// has filter been configured?
					$where = "";
					if ($this->filter != "")
					{
						$where = "WHERE " . substr($this->filter, 4);
					}
					$sort = "";
					if ($this->order_by != "") $sort = " ORDER BY " . $this->order_by;


					$sql = "SELECT * FROM tracking_estimated_time t $where $sort LIMIT $this->pageOffset , $this->rowsPerPage";
					
					

					t($sql, __METHOD__);

					return TrackingEstimatedTime::getTrackingEstimatedTrackingListFromSql($sql);
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
	public function addCountryIsoFilter($iso)
	{
		if ($this->filter != "") $this->filter .= " AND ";
		$this->filter .= "t.country_iso='" . DbAccess3::escape($iso) . "'";
	}

	


	
}  // class
?>