<?php
/**
 * A consignment contains a number of parcels.
 * Each parcel will need to be assigned a item number used on label.
 *
 */
class oweSouthAfricaRoutine extends DbAccess3
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
					'state' => 'string',
					'zone' => 'string',
                                        'route' => 'string',
                                        'delivery_time' => 'string'
                                       
					);
		//
		parent::__construct("owe_southafrica_routine", 'id', $fieldList, $mixedCreator);
	}
	
	public static function getOweSouthAfricaRoutineListFromSql($sql)
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
