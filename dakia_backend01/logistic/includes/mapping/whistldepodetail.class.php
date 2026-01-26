<?php
/**
 * A consignment contains a number of parcels.
 * Each parcel will need to be assigned a item number used on label.
 *
 */
class WhistlDepoDetail extends DbAccess3
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
					'depo_id' => 'string',
					'depo_detail' => 'string',
					'depo_address' => 'string',
					'from_postcode' => 'string'
					);
		//
		parent::__construct("whistl_depo_details", 'id', $fieldList, $mixedCreator);
	}
	
	public static function getWhistlDepoDetailListFromSql($sql)
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
