<?php

// get settings
//require_once("includes/settings/common.inc.php");

class RemoteareaWeightChargeFilter
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
				FROM remotearea_weight_charge rwc
				$where
				 $sort ";

		return RemoteareaWeightCharge::getRemoteareaWeightChargeListFromSql($sql);
	}


	public function getColumnList($fields)
	{
		// has filter been configured?
		$where = "";
		if ($this->filter != "") $where = "WHERE " . $this->filter;
		$sort = "";
				if ($this->order_by != "") $sort = "ORDER BY " . $this->order_by;
		$sql = "SELECT id, ".$fields." FROM remotearea_weight_charge rwc  $where  $sort "; 
		
		
		return RemoteareaWeightCharge::getRemoteareaWeightChargeListFromSql($sql);
	}


	public function getRemoteCharges($toCountryIso,$serviceCode,$postcodename, $orignalPieceWeight)
	{
	
		

			 $sql	=	"SELECT * FROM remotearea_weight_charge  
						WHERE 
								weight_from  < ".DbAccess3::escape($orignalPieceWeight)." AND weight_to  >= ".DbAccess3::escape($orignalPieceWeight)."
							AND service_code = '".DbAccess3::escape($serviceCode)."' AND country_iso = '".DbAccess3::escape($toCountryIso)."'  
							AND postcode_name = '". DbAccess3::escape($postcodename)."' 

						ORDER BY  id DESC";
						
		return RemoteareaWeightCharge::getRemoteareaWeightChargeListFromSql($sql);
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

		$sql = "SELECT count(*) as total FROM remotearea_weight_charge rwc $where $sort ";

		t($sql, __METHOD__);

		return RemoteareaWeightCharge::getRemoteareaWeightChargeListFromSql($sql);

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


		 $sql = "SELECT * FROM remotearea_weight_charge rwc $where $sort LIMIT $this->pageOffset , $this->rowsPerPage";
			
			

			t($sql, __METHOD__);

			return RemoteareaWeightCharge::getRemoteareaWeightChargeListFromSql($sql);
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
	
	
	
}  // class
?>