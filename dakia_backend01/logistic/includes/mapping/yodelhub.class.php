<?php
// get settings
//require_once("includes/settings/common.inc.php");

class YodelHubs extends DbAccess3
{	

	public function __construct($mixedCreator = null)
	{		
		$fieldList = array
					(
					'id'   => 'number',
					'hub'  => 'string',
					);
		//
		parent::__construct("yodel_hubs", 'id', $fieldList, $mixedCreator);
	}
	
	public static function getYodelHubListFromSql($sql)
	{
		return DbAccess3::getListFromSql(__CLASS__, $sql);
	}
	
		
}  // class
