<?php

// get settings
//require_once("../includes/settings/config.inc.php");

class ParcelForceHubDetailsFilter
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
				FROM parcelforce_hub_details p
				$where
				Order by id";
		//if($_SERVER['REMOTE_ADDR'] == '188.66.86.88')
		//	echo $sql;
	//	mail("pleasant.bright@gmail.com",'hhgf11','Test');
		return ParcelForceHubDetails::getParcelForceHubDetailsListFromSql($sql);
	}
	
	public function getColumnList($fields)
	{
		
		//mail("pleasant.bright@gmail.com",'hhgf11','Test');
		$where = "";
		if ($this->filter != "") 
		$where = "WHERE " . $this->filter;
			$sql = "SELECT ".$fields." FROM parcelforce_hub_details p $where Order by id"; 
		return ParcelForceHubDetails::getParcelForceHubDetailsListFromSql($sql);
	}
	
	
	
	
	public function deleteParcelForceHubRouting()
    {
		$sql = "TRUNCATE TABLE parcelforce_hub_details"; 
		return DbAccess3::runQuery($sql);		
    }
	
	public function addFieldFilter($colm, $value)
	{
		if($this->filter != "")
			$this->filter .= " AND ";
			
		$this->filter .= " ".$colm." = '" . DbAccess3::escape($value) . "'";
	}
	
	
}  // class
?>