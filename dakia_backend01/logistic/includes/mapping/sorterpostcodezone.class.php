<?php
/**
 * Country Object
 *
 */
class SorterPostcodeZone extends DbAccess3
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
					'area' => 'string',
					'postcode' => 'string',
					'zone'=>'string'
					);

		//
		parent::__construct("sorter_postcode_zone", 'id', $fieldList, $mixedCreator);
	}

	/**
	 * Get object Id (not provided as magic method) - read only.
	 *
	 */
	public function getId()
	{
		return $this->valArray["id"];
	}
	
	
	
	/**
	 * Get list of user objects, using sql given
	 *
	 * @param string $sql
	 */
	public static function getSorterPostcodeZoneListFromSql($sql)
	{
		
		return DbAccess3::getListFromSql(__CLASS__, $sql);
	}
}
