<?php
class CollectionTimeGroup extends DbAccess3
{

	/*
	 * Create and define class
	 */
 	public function __construct($mixedCreator = null)
	{
		$fieldList =
			array( 
				'id' => 'number',	
				'name' => 'string',
					);
		//
		parent::__construct("collection_time_groups", 'id', $fieldList, $mixedCreator);
	}

	/*
	 * Get List of Address Lookup Objects
	 */
	 public static function getCollectionTimeGroupList($sql)
	{
		return DbAccess3::getListFromSql(__CLASS__, $sql);
	}
	

	
	
}