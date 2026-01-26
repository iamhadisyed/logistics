<?php
// get settings
//require_once("/var/www/vhosts/oneworldexpress.co.uk/httpdocs/remote/includes/settings/common.inc.php");

class ManifestConsignment extends DbAccess3
{
	

	public function __construct($mixedCreator = null)
	{
		$fieldList = array
					(
						'manifestid'      => 'number',												
						'consignmentid'   => 'number',
											
					
					);
		//
		parent::__construct("manifest", 'manifestid', $fieldList, $mixedCreator);
	}
	
	
	public static function getManifestListFromSql($sql)
	{
		return DbAccess3::getListFromSql(__CLASS__, $sql);
	}
	
	
	

}  // class
