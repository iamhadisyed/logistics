<?php

class CountryRatebandFilter
{
	private $filter = "";
	private $order_by = "";
	private $limit = 100;
	private $rowsPerPage = 0;
	private $pageOffset = 0;
	/**
	 * Get list of country_rateband_id items based on filter conditions
	 *
	 * @return arrayCountry_rateband_id]
	 */
	public function getList()
	{
		// has filter been configured?
		$where = "";
		if ($this->filter != "") $where = "WHERE " . $this->filter;
		$sort = "";
				if ($this->order_by != "") $sort = "ORDER BY " . $this->order_by;
		$sql = "SELECT *
				FROM country_rateband_id cri
				$where
				 $sort ";

		return CountryRateband::getCountryRatebandListFromSql($sql);
	}


	public function getColumnList($fields)
	{
		// has filter been configured?
		$where = "";
		if ($this->filter != "") $where = "WHERE " . $this->filter;
		$sort = "";
				if ($this->order_by != "") $sort = "ORDER BY " . $this->order_by;
		$sql = "SELECT id, ".$fields." FROM country_rateband_id cri  $where  $sort "; 
		return CountryRateband::getCountryRatebandListFromSql($sql);
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

		$sql = "SELECT count(*) as total FROM country_rateband_id cri $where $sort ";

		t($sql, __METHOD__);

		return CountryRateband::getCountryRatebandListFromSql($sql);

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


				 $sql = "SELECT * FROM country_rateband_id cri $where $sort LIMIT $this->pageOffset , $this->rowsPerPage";
					
					

					t($sql, __METHOD__);

					return CountryRateband::getCountryRatebandListFromSql($sql);
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
	public function addcoulmnFilter($colm, $value)
	{
		$this->filter .= " AND ";
		$this->filter .= " ".$colm." = '" . $value . "'";
	}
	
	
}  // class
?>