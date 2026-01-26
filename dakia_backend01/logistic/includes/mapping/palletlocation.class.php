<?php
/**
 * A consignment contains a number of parcels.
 * Each parcel will need to be assigned a item number used on label.
 *
 */
class PalletLocation extends DbAccess3
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
					'locationid' => 'number',	
					'palletid' => 'number',	
					'consignmentid' => 'number',	
					'date_created' => 'string',										
					'createdby' => 'number',
					'comments' => 'string'
					);

		//
		parent::__construct("pallet_location", 'id', $fieldList, $mixedCreator);
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
	
	public function bulkDataInsert($rows)
	{
		$sql = array(); 
		
		//print_r($rows);
		
		foreach($rows as $row) 
		{
    			$sql[] = "(".DbAccess3::escape($row['locationid']).",'"
							.DbAccess3::escape($row['palletid'])."','"
							.DbAccess3::escape($row['consignmentid'])."','"
							.DbAccess3::escape($row['date_created'])."','"
							.DbAccess3::escape($row['createdby'])."')";
		}
		$sqlQuery =     'INSERT INTO pallet_location (
						 locationid, 
						 palletid, 
						 consignmentid, 
						 date_created, 
						 createdby) 
						 VALUES '.implode(',', $sql);
						 
					 
		return DbAccess3::runQuery($sqlQuery);				 
		
	}

	
	
}
