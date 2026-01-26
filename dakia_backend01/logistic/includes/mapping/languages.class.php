<?php
/**
 * Parcelgroupconsignment - Parcel group consignment class
 * - deals with Parcel group consignments
 *
 */

class Languages extends DbAccess3 
{	/**
	 * Construct
	 *
	 * @param id/array
	 */
 	public function __construct($mixedCreator = null)
	{
		$fieldList = array(
					'id'	=> 'number',					
					'language' => 'string',
					'date_created' => 'string',
					'created_by' => 'number',
					'is_active' => 'string'

					);
		//
		parent::__construct("language", 'id', $fieldList, $mixedCreator);
	}
	
	public static function getLanguageListFromSql($sql)
	{
		//echo "SQL " . $sql;
		//die;
		return DbAccess3::getListFromSql(__CLASS__, $sql);
	}
	
	public static function getTotalNumberOfLanguageFromSql($sql)
    {
		$rs = self::runQuery($sql);
		$data=mysqli_fetch_assoc($rs);
		return $data['total'];
	}
	
	
	


   
   
}