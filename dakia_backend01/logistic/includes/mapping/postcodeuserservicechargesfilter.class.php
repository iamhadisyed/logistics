<?php

// get settings
//require_once("includes/settings/common.inc.php");

class PostcodeUserServiceChargesFilter
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
				FROM postcode_user_service_charges tum
				$where
				 $sort ";

		return PostcodeUserServiceCharges::getPostcodeUserServiceChargesListFromSql($sql);
	}

	public function getColumnList($fields)
	{
		// has filter been configured?
		$where = "";
		if ($this->filter != "") $where = "WHERE " . $this->filter;
		$sort = "";
				if ($this->order_by != "") $sort = "ORDER BY " . $this->order_by;
		 $sql = "SELECT  ".$fields." , id FROM postcode_user_service_charges tum  $where  $sort "; 
		
		
		return PostcodeUserServiceCharges::getPostcodeUserServiceChargesListFromSql($sql);
	}
	
	
	public function getPagingList ()
			{
					// has filter been configured?
					$where = "";
					if ($this->filter != "")
					{
						$where = "WHERE " . $this->filter;
						//$where = "";
					}
					else
					{
						$where = "";
					}
					$sort = "";
					if ($this->order_by != "") $sort = "ORDER BY " . $this->order_by;

	//if($_SERVER['REMOTE_ADDR'] == '188.66.86.88')
		 	 		 $sql = "SELECT * FROM postcode_user_service_charges $where $sort LIMIT $this->pageOffset , $this->rowsPerPage";
					 
			//	else
			//	$sql = "SELECT * FROM consignment c $where $sort LIMIT $this->pageOffset , $this->rowsPerPage";
				/*if( $_SERVER['REMOTE_ADDR'] == '188.66.86.88')
					echo $sql;*/
				 //echo $sql;	
				t($sql, __METHOD__);

					return PostcodeUserServiceCharges::getPostcodeUserServiceChargesListFromSql($sql);
			}

	
	
	public function setRowsPerPage($rowsPP)
	{
		$this->rowsPerPage = $rowsPP;
	}
	
	public function setOffset($offset)
	{
		$this->pageOffset = $offset;
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
	
	public function getRemoteAreaPostCodeServicesFilter($postCode, $account, $countryIso='', $city='')
	{
		if(trim($postCode) == '0')
		{
			$postCode	=	'';
		}

		if(trim($city) != '' && trim($postCode) == '')
			$whereQuery	=	" AND UPPER(TRIM(city_name)) = '".strtoupper(trim(DbAccess3::escape($city)))."'";
		if(is_numeric($postCode))
		{
			$postCode	=	str_replace(' ','',$postCode);
			$sql	=	"SELECT 
						RUM.id ,  service_code 'city_name'
					FROM 
							postcode_user_service_charges PUSC 
					INNER JOIN 
							remotearea_user_mapping RUM 
						ON 	
							PUSC.postcode_name = RUM.postcode_name 
						WHERE 
								(CAST(from_postcode AS INT) <= '".DbAccess3::escape($postCode)."' AND CAST(to_postcode AS INT) >= '".DbAccess3::escape($postCode)."') 
							AND  RUM.user_account = 'ALL'   
							".$whereQuery."
							AND country_iso = '". DbAccess3::escape($countryIso)."'
						GROUP BY service_code ORDER BY  RUM.id DESC";
		
		}
		else
		{
				$postCodeLength	=	2;
				$postCode	=	trim(str_replace(' ', '',$postCode));
				if(strlen($postCode) == 5)
				{
					$postCodeA	=	substr($postCode, 0, 2);
					$postCodeLength	=	2;
				}
				elseif(strlen($postCode) == 6)
				{
					$postCodeA	=	substr($postCode, 0, 3);
					$postCodeLength	=	3;
				}
				elseif(strlen($postCode) == 7)
				{
					$postCodeA	=	substr($postCode, 0, 4);
					$postCodeLength	=	4;
				}
				
				$sql	=	"SELECT 
							RUM.id ,  service_code 'city_name'
						FROM 
								postcode_user_service_charges PUSC 
						INNER JOIN 
								remotearea_user_mapping RUM 
							ON 	
								PUSC.postcode_name = RUM.postcode_name 
							WHERE 
								(SUBSTRING(LOWER(from_postcode),1,".$postCodeLength.") = '".strtolower(DbAccess3::escape($postCodeA))."' OR SUBSTRING(LOWER(to_postcode),1,".$postCodeLength.") = '".strtolower(DbAccess3::escape($postCodeA))."')
							AND (char_length(REPLACE(from_postcode,' ','')) = ".DbAccess3::escape($postCodeLength)." OR char_length(REPLACE(from_postcode,' ','')) = ".strlen(DbAccess3::escape($postCode))." )
							AND RUM.user_account = 'ALL' 
							".@$whereQuery."
							AND country_iso = '". DbAccess3::escape($countryIso)."'
							GROUP BY service_code  ORDER BY  RUM.id DESC";
		}
		
		return PostcodeUserServiceCharges::getPostcodeUserServiceChargesListFromSql($sql);
	}
	
	
	
	public function addFilter($field)
	{
		if ($this->filter != "") $this->filter .= " AND ";
		$this->filter .= $field ;
	}
	
	/**
	 * Filter on given user/password combo
	 *
	 * @param string or array giving $service_type
	 */
	public function addFieldLikeFilter($field,$value)
	{
		if ($this->filter != "") $this->filter .= " AND ";
		$this->filter .= $field." LIKE '". DbAccess3::escape($value ). "'";
	}
	
	public function expunge()
	{
			$where = "";
		if ($this->filter != "") $where = "WHERE " . $this->filter;
		$sort = "";
				if ($this->order_by != "") $sort = "ORDER BY " . $this->order_by;

		$sql = "DELETE FROM postcode_user_service_charges
				$where
				";

		$result = DbAccess3::runQuery($sql);
		return;
	}

	
}  // class
?>