<?php
/**
 * Parcelgroupconsignment - Parcel group consignment class
 * - deals with Parcel group consignments
 *
 */
 
class CustomizedUserServicesRouting extends DbAccess3 
{	/**
	 * Construct
	 *
	 * @param id/array
	 */
 	public function __construct($mixedCreator = null)
	{
		$fieldList = array(
                                    'id' => 'number',
                                    'service_id' => 'number',
                                    'user_account_id' => 'number',
                                    'routing_added_date' => 'datetime',
                                    'routing_name'  => 'undefined'
                                    );
		//
		parent::__construct("customized_user_services_routing", 'id', $fieldList, $mixedCreator);
	}
	
	public static function getCustomizedUserServicesRoutingListFromSql($sql)
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

}  // class