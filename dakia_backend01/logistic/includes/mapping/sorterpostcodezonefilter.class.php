<?php

// get settings
//require_once("../includes/settings/config.inc.php");

class SorterPostcodeZoneFilter
{
	private $filter = "";

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

		 $sql = "SELECT *
				FROM sorter_postcode_zone s
				$where
				Order by id";
		//if($_SERVER['REMOTE_ADDR'] == '188.66.86.88')
		//	echo $sql;
	//	mail("pleasant.bright@gmail.com",'hhgf11','Test');
		return SorterPostcodeZone::getSorterPostcodeZoneListFromSql($sql);
	}
	
	public function getColumnList($fields)
	{
		
		//mail("pleasant.bright@gmail.com",'hhgf11','Test');
		$where = "";
		if ($this->filter != "") 
		$where = "WHERE " . $this->filter;
			$sql = "SELECT ".$fields." FROM sorter_postcode_zone s $where Order by id"; 
		return SorterPostcodeZone::getSorterPostcodeZoneListFromSql($sql);
	}
	
	
	public function addFieldFilter($colm, $value)
	{
		if($this->filter != "")
			$this->filter .= " AND ";
			
		$this->filter .= " ".$colm." = '" . DbAccess3::escape($value) . "'";
	}
	
	
}  // class
?>