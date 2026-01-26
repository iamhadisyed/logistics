<?php
class PostnlDatafileIdFilter
{
	private $filter_str = "";
	private $limit = 200;
	//
	public function getSql()
	{
		$filter = ($this->filter_str == "") ? "" : "WHERE " . substr($this->filter_str, 4);
		//
		$sql = "SELECT d.* FROM postnl_datafile_id d ";
		$sql .= $filter;
		$sql .= "ORDER BY id desc ";
		//
		if ($this->limit > 0) $sql .= "Limit " . $this->limit . " ";

		 return PostnlDataFileId::getPostNlListFromSql($sql);
	}

	/***
	 * Filter by sent date.
	 */
	public function addSentDateFilter ($sent_date)
	{
		$this->filter_str = "AND DateDiff(d.sent_date, '"  . Date("Y-m-d", $sent_date)   . "') = 0 "; 
	}
	public function addServiceCountryFilter ($country)
	{
		$this->filter_str = "AND service_country = '".$country."'"; 
	}
}