<?php
/*
 * Consignment Filter
 *
 */
class CustomizedUserServicesRoutingFilter
{
	private $filter = "";
	private $order_by = "";

	
	public function getList()
	{
		// has filter been configured?
		$where = "";
		if ($this->filter != "")
		{
			$where = "WHERE " . substr($this->filter, 4);
		}
		$sort = "";
		if ($this->order_by != "") $sort = "ORDER BY " . $this->order_by;


		$sql = "SELECT *
				FROM customized_user_services_routing t
				$where
				$sort
				";

		t($sql, __METHOD__);
		
		//echo $sql;
		return CustomizedUserServicesRouting::getCustomizedUserServicesRoutingListFromSql($sql);

	//	return highVolume::getHighVolumeListFromSql($sql);
	}
	
	public function AddOrderByField ($field, $ascending = true)
	{
		//if ($this->order_by != "") $this->order_by = "";
		//
		$this->order_by = $field . ($ascending ? " " : " DESC");
	}
	
	
	public function getColumnList($fields)
	{
		// has filter been configured?
		$where = "";
		if ($this->filter != "")
		{
			$where = "WHERE " . substr($this->filter, 4);
		}
		$sort = "";
		if ($this->order_by != "") $sort = "ORDER BY " . $this->order_by;


		$sql = "SELECT id, ".$fields."
				FROM customized_user_services_routing t
				$where
				$sort
				";

		t($sql, __METHOD__);
		
		//echo $sql;
		return CustomizedUserServicesRouting::getCustomizedUserServicesRoutingListFromSql($sql);

	//	return highVolume::getHighVolumeListFromSql($sql);
	}
	

	
	public function getCount()
	{
		$result = $this->getList();
		return sizeof($result);
	}	
	

	public function addUserAccountFilter($useraccount)
	{
		$this->filter .= " AND user_account_id = '" . DbAccess3::escape($useraccount) . "'";
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
			$where = "WHERE " . substr($this->filter, 4);
		}
	 	$sql = "DELETE FROM routing_user_mapping $where ";
		return DbAccess3::runQuery($sql);
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