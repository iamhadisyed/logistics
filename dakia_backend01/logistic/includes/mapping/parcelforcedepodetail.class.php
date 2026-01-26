<?php
/**
 * Country Object
 *
 */
class ParcelForceDepoDetail extends DbAccess3
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
					'depo_name' => 'string',
					'depo_short_name' => 'string',
					'postcode'=>'string',
					'route_number'=>'string',
					'pfw_ect'=>'string',
					'pfw_lat'=>'string',
					'pfw_lct'=>'string'
					);

		//
		parent::__construct("parcelforce_depo_detail", 'id', $fieldList, $mixedCreator);
	}

	/**
	 * Get object Id (not provided as magic method) - read only.
	 *
	 */
	public function getId()
	{
		return $this->valArray["id"];
	}
	
	
	
	/**
	 * Get list of user objects, using sql given
	 *
	 * @param string $sql
	 */
	public static function getParcelForceDepoDetailListFromSql($sql)
	{
		
		return DbAccess3::getListFromSql(__CLASS__, $sql);
	}
}
