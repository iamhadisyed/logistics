<?php
// get settings
//require_once("includes/settings/common.inc.php");

class RemoteareaWeightCharge extends DbAccess3
{
	public function __construct($mixedCreator = null)
	{
		$fieldList = array
					(
						'id'     		=> 'number',
						'weight_from'   => 'string',
						'weight_to'     => 'string',
						'service_code'   	=> 'string',
						'country_iso'   => 'string',
						'postcode_name' => 'string',
						'formulla'   	=> 'string',
						'charges'   	=> 'string',
						 
					);
		//
		parent::__construct("remotearea_weight_charge", 'id', $fieldList, $mixedCreator);
	}



	/**
	 * Get list of user objects, using sql given
	 *
	 * @param string $sql
	 */
	 
	public static function getRemoteareaWeightChargeListFromSql($sql)
	{
//		echo $sql;
		return DbAccess3::getListFromSql(__CLASS__, $sql);
	}
	
	
}  // class
