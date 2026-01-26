<?php
/**
 * A consignment contains a number of parcels.
 * Each parcel will need to be assigned a item number used on label.
 *
 */
class parcelforuDropoffPoint extends DbAccess3
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
					'special' => 'string',
                                        'place' => 'string',
                                        'address_line_1' => 'string',
                                        'city' => 'string',
                                        'postcode' => 'string',
                                        'country_iso' => 'string',
                                        'statuscode' => 'string',
                                        'status_description' => 'string',
                                        'latitude' => 'string',
                                        'longitude' => 'string',
                                        'mon' => 'string',
                                        'tue' => 'string',
                                        'wed' => 'string',
                                        'thu' => 'string',
                                        'fri' => 'string',
                                        'sat' => 'string',
                                        'sun' => 'string',
                                        'label_routing' => 'string',
                                        'branch_id' => 'string',
                                        'maxweight' => 'number',
                                        'label_name' => 'string',
                                        'branch_id' => 'string',
                                        'distance'  => 'undefined'
					);
		//
		parent::__construct("parcelforu_dropoff_point", 'id', $fieldList, $mixedCreator);
	}
	
	public static function getparcelForUDropoffPointRoutineListFromSql($sql)
	{
		return DbAccess3::getListFromSql(__CLASS__, $sql);
	}
	public static function getListFromSql($sql, $val_field, $key_field = "")
	{
		$list = array();
		$rs = DbAccess3::runQuery($sql);
		//
		while ($row = mysqli_fetch_assoc($rs))
		{
			if ($key_field == "")
			{
				$list[] = $row[$val_field];
			}
			else
			{
				$list[$row[$key_field]] = $row[$val_field];
			}
		}
		return $list;
	}
	
	


	 
 	
}
