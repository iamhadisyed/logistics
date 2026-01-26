<?php
/**
 * A consignment contains a number of parcels.
 * Each parcel will need to be assigned a item number used on label.
 *
 */
class UkmailAuthentication extends DbAccess3
{
	/**
	 * Construct
	 *
	 * @param id/array
	 */
	
					
					
	public function __construct($mixedCreator = null)
	{
		$fieldList = array(
					'id' => 'number',
					'authentication_token' => 'string',
					'date_created' => 'datetime',
					'user_account' => 'string',
					);
		//
		parent::__construct("ukmail_authentication", 'id', $fieldList, $mixedCreator);
	}
	
	public static function getUkmailAuthenticationListFromSql($sql)
	{
		return DbAccess3::getListFromSql(__CLASS__, $sql);
	}
	
 	
}
