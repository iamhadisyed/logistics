<?php
/**
 * A consignment contains a number of parcels.
 * Each parcel will need to be assigned a item number used on label.
 *
 */
class Location extends DbAccess3
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
					'name' => 'string',	
					'date_created' => 'string',										
					'createdby' => 'number',
					'date_updated' => 'string',
					'updatedby' => 'number',
					'active' => 'string',
					'warehouseid' => 'string',
					'type' => 'string'
					
					);

		//
		parent::__construct("location", 'id', $fieldList, $mixedCreator);
	}
	
	public static function getLocationListFromSql($sql)
	{
		return DbAccess3::getListFromSql(__CLASS__, $sql);
	}
	
	public static function getTotalNumberOfLocationsFromSql($sql)
    {
		$rs = self::runQuery($sql);
		$data=mysqli_fetch_assoc($rs);
		return $data['total'];
	}

	
	
}
