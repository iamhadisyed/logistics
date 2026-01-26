<?php
/**
 * Parcelgroupconsignment - Parcel group consignment class
 * - deals with Parcel group consignments
 *
 */

class ConsignmentBaggingMapping extends DbAccess3 
{	/**
	 * Construct
	 *
	 * @param id/array
	 */
 	public function __construct($mixedCreator = null)
	{
		$fieldList = array(
					'consignmentid'	=> 'number',
					'bagid'=> 'number'			

					);
		//
		parent::__construct("consignment_bagging_mapping", 'id', $fieldList, $mixedCreator);
	}
	
	public static function getConsignmentBaggingListFromSql($sql)
	{
		return DbAccess3::getListFromSql(__CLASS__, $sql);
	}
	
	
	public static function getBaggingListFromSql($sql)
	{
		return DbAccess3::getListFromSql(__CLASS__, $sql);
	}		
	
	private static function removecommas($data)
	{
	   return str_replace(",", " ", $data);

	}
	
	
	
}