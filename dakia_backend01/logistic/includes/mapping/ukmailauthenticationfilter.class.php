<?php
class UkmailAuthenticationFilter
{
	private $filter_str = "";
	private $limit = 200;
	//
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
		
		$sql = "SELECT * FROM ukmail_authentication u	$where $sort LIMIT 5000";

		//echo $sql;
		t($sql, __METHOD__);
		

		return UkmailAuthentication::getUkmailAuthenticationListFromSql($sql);
	}

	/***
	 * Filter by sent date.
	 */
	
	public function addDateCreatedFilter()
	{
		$date = date("Y-m-d H:i:s",strtotime('-1 hours',  time()));
		$sql = "SELECT * FROM ukmail_authentication u where u.date_created > '" . DbAccess3::escape($date) . "' order by id desc limit 1";
                return UkmailAuthentication::getUkmailAuthenticationListFromSql($sql);
	}
	
	
}