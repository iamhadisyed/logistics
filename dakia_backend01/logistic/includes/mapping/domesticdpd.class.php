<?php
// get settings
//require_once("includes/settings/common.inc.php");

class DomesticDPD extends DbAccess3
{
	
	public function __construct($mixedCreator = null)
	{
		$this->tablename = 'cs_log';
        $this->pkey      = 'id';
		$fieldList = array
					(
					'id'      => 'number',
					'postcode_sector'      => 'string',
					'dpd_depot'      => 'string',
					'dpd_services_group'   => 'string',
					'dpd_offshore_zone' 		=> 'string',
					'timeslots_code' => 'string',
					'cluster' => 'string',
					'ilk_depot'	=> 'string',
					'ilk_services_group' => 'string',
					'ilk_offshore_zone'	=> 'string',
					'ilk_alternate_service'	=> 'string',
					'new_postcode'	=> 'string',
					);
		//
		parent::__construct("domestic", 'id', $fieldList, $mixedCreator);
	}

	
	


	

	/**
	 * Get list of user objects, using sql given
	 *
	 * @param string $sql
	 */
	public static function getDomesticDPDListFromSql($sql)
	{
		return DbAccess3::getListFromSql(__CLASS__, $sql);
	}


	
	public static function getTotalNumberOfDomesticDPDFromSql($sql)
   {
			$rs = DbAccess3::runQuery($sql);
			$data=mysqli_fetch_assoc($rs);
			return $data['total'];
	}



}  // class
