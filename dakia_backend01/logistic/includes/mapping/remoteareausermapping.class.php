<?php
// get settings
//require_once("includes/settings/common.inc.php");

class RemoteareaUserMapping extends DbAccess3
{
	public function __construct($mixedCreator = null)
	{
		$fieldList = array
					(
						'id'     					=> 'number',
						'user_account'      		=> 'string',
						'postcode_name'     		=> 'string',
						'service_code'   			=> 'string',
						'charges'   				=> 'string',
						'remotearea_added_date'   	=> 'string' 
					);
		//
		parent::__construct("remotearea_user_mapping", 'id', $fieldList, $mixedCreator);
	}



	/**
	 * Get list of user objects, using sql given
	 *
	 * @param string $sql
	 */
	 
	public static function getRemoteareaUserMappingListFromSql($sql)
	{
//		echo $sql;
		return DbAccess3::getListFromSql(__CLASS__, $sql);
	}
	
	
}  // class
