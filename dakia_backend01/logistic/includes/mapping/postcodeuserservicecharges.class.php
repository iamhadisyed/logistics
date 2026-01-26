<?php
// get settings
//require_once("includes/settings/common.inc.php");

class PostcodeUserServiceCharges extends DbAccess3
{
	public function __construct($mixedCreator = null)
	{
		$fieldList = array
					(
						'id'      			=> 'number',
						'to_postcode'   	=> 'string',
						'from_postcode'   	=> 'string',
						'postcode_name'		=> 'string',
						'city_name'   		=> 'string',
						'country_iso'   	=> 'string'
					);
		//
		parent::__construct("postcode_user_service_charges", 'id', $fieldList, $mixedCreator);
	}



	/**
	 * Get list of user objects, using sql given
	 *
	 * @param string $sql
	 */
	 
	public static function getPostcodeUserServiceChargesListFromSql($sql)
	{
//		echo $sql;
		return DbAccess3::getListFromSql(__CLASS__, $sql);
	}
	
	
}  // class
