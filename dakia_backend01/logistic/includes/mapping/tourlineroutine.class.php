<?php
/**
 * A consignment contains a number of parcels.
 * Each parcel will need to be assigned a item number used on label.
 *
 */
class TourlineRoutine extends DbAccess3
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
					'agency_name' => 'string',
					'postal_code' => 'string',
					'agency_code' => 'string',
					'zone' => 'string',
                                        'province' => 'string',
                                        'route_code' => 'string',
                                        'km' => 'string',
                                        'town_name' => 'string',
                                        'kilometer' => 'string',
					);
		//
		parent::__construct("tourline_routine", 'id', $fieldList, $mixedCreator);
	}
	
	public static function getTourlineRoutineListFromSql($sql)
	{
		return DbAccess3::getListFromSql(__CLASS__, $sql);
	}
	
	
	


	 
 	
}
