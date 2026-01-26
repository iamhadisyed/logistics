<?php
/**
 * A consignment contains a number of parcels.
 * Each parcel will need to be assigned a item number used on label.
 *
 */
class ptwoRoutine extends DbAccess3
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
					'logistik' => 'string',
					'postcode' => 'string',
                                        'city' => 'string',
                                        'district' => 'string',
					'street' => 'string',
                                        'house_number_from' => 'string',
                                        'additional_from' => 'string',
					'house_number_to' => 'string',
                                        'addtional_to' => 'string',
                                        'house_number_filter' => 'string',
                                        'sortinfo' => 'string',
                                       
					);
		//
		parent::__construct("ptwo_routine", 'id', $fieldList, $mixedCreator);
	}
	
	public static function getPtwoRoutineListFromSql($sql)
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
