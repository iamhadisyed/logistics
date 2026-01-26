<?php

// get settings
//require_once("../includes/settings/config.inc.php");

class PreAlertFilter
{
	private $filter = "";
	
	private $pageOffset = "";

	/**
	 * Get list of user items based on filter conditions
	 *
	 * @return array[User]
	 */
	
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

		$sql = "SELECT count(id) 'id' FROM pre_alert p $where $sort ";
		
		//echo $sql;

		t($sql, __METHOD__);
		
		$result = PreAlert::getPreAlertListFromSql($sql);
		
		//print_r($result);
		
		return $result[0]->getId();

		//return PreAlert::getPreAlertListFromSql($sql);

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
				if ($this->order_by != "") $sort = "ORDER BY " . $this->order_by;

//if($_SERVER['REMOTE_ADDR'] == '188.66.86.88')
				 $sql = "select id, mawb, flight_number, pieces, weight, etd, eta, current_status, date_time, cleared, status, comments, account, files 					     					from pre_alert p $where $sort LIMIT $this->pageOffset , $this->rowsPerPage";		
			t($sql, __METHOD__);
			//echo $sql;

				return PreAlert::getPreAlertListFromSql($sql);
		}


			public function setOffset($offset)
			{
				$this->pageOffset = $offset;
			}

			public function setRowsPerPage($rowsPP)
			{
				$this->rowsPerPage = $rowsPP;
			} 
	 
	 
	
	public function getList()
	{
		// has filter been configured?
		$where = "";
		if ($this->filter != "")
		{
			$where = "WHERE " . substr($this->filter, 4);
		}
		

		$sql = "SELECT *
				FROM pre_alert p
				$where
				Order by id";
//		echo $sql;die;
		return PreAlert::getPreAlertListFromSql($sql);
	}
	
	public function getColumnList($fields)
	{		
		$where = "";
		if ($this->filter != "")
		{
			$where = "WHERE " . substr($this->filter, 4);
		}
		
		$sql = "SELECT ".$fields." FROM pre_alert p $where Order by id DESC"; 
		//echo $sql;
		return PreAlert::getPreAlertListFromSql($sql);
	}
	
	public function addIdFilter($id)
    {
		$sql = "SELECT *
				FROM pre_alert p
				where p.id = " .DbAccess3::escape($id) ."";
//		echo $sql;
		return PreAlert::getPreAlertListFromSql($sql);
	}
	

	public function addDateTimeFilter($dateFrom, $dateTo)
	{
		$this->filter .= " AND ";
		$this->filter .= " date_format(p.date_time, '%Y-%m-%d') >= '$dateFrom' and 
						   date_format(p.date_time, '%Y-%m-%d') <= '$dateTo'";
	}
	
	public function addMawbFilter($mawb)
	{
            if(!empty($this->filter))
		$this->filter .= " AND ";
            
            $this->filter .= "    p.mawb_id = '" .DbAccess3::escape($mawb) . "'";
		
	}
	
	public function addFlighNumberFilter($flightno)
	{
		$this->filter .= " AND ";
		$this->filter .= "p.flight_number = '" . DbAccess3::escape($flightno) . "'";
		
	}
	public function addEtdFilter($date)
	{
		
		$this->filter .= " AND ";
		$this->filter .= "date_format(p.etd, '%Y-%m-%d') = '" . $date . "'";
		
	}
	public function addEtaFilter($date)
	{
		$this->filter .= " AND ";
		$this->filter .= "date_format(p.eta, '%Y-%m-%d') = '" . $date . "'";
		
	}
	public function report($dat)
	{
		
		$sql = "SELECT mawb,flight_number,pieces, weight, etd, eta, current_status, date_time, cleared,status, comments, account, id,shed FROM pre_alert p WHERE (date_format(p.date_time,'%Y-%m-%d') = '".$dat."' and status = 'in warehouse') OR
		( DATE_SUB(p.eta,INTERVAL 7 DAY) and status in('Not Assigned','M') )
		
			Order by status DESC,current_status DESC, eta DESC";
			
		return PreAlert::getPreAlertListFromSql($sql);
	}
	
	public function updateFlightStatus($mawb)
	{
		$sql = "update pre_alert set current_status = 'SCANNING IN PROGRESS', status = 'IN WAREHOUSE', date_time = NOW(), cleared = 'YES'
				where replace(mawb,'-','')  = replace('$mawb', '-', '') and current_Status NOT IN ('IN WAREHOUSE', 'PROCESSED')";
				
		PreAlert::runQuery($sql);
	}

	public function getMawbReport($mawb)
	{
		$sql = " SELECT p.mawb, p.flight_number,p.pieces, p.weight, p.date_time, 
				 sum(agent_amount + agent_linehaul_cost + agent_handling_charges ) as current_status, c.agentid as status
				 from  ( consignment as c join pre_alert as p ON 
				 c.mawb =  p.mawb COLLATE utf8_unicode_ci ) LEFT JOIN invoice_detail as i  ON c.id = i.consignment_id where  p.type='E'
				 and p.mawb like '".DbAccess3::escape($mawb)."%' group by p.mawb";
		
//		echo $sql;
		return PreAlert::getPreAlertListFromSql($sql);
	}
	
	
}  // class
?>