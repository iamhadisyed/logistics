<?php
// get settings
//require_once("includes/settings/common.inc.php");

class PreSortCarrier extends DbAccess3
{	

	public function __construct($mixedCreator = null)
	{
		
		$fieldList = array
					(
					'id'      				=> 'number',					
					'carrier_name'      	=> 'string',
					
					);
		//
		parent::__construct("presort_carrier", 'id', $fieldList, $mixedCreator);
	}
	
	public static function getPreSortCarrierListFromSql($sql)
	{
		return DbAccess3::getListFromSql(__CLASS__, $sql);
	}
	
		
}  // class
