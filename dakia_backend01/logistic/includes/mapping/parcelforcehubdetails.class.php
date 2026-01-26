<?php
/**
 * Country Object
 *
 */
class ParcelForceHubDetails extends DbAccess3
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
					'depo_number' => 'string',
					'mon_hub_24'=>'string',
					'mon_chute_24'=>'string',
					'mon_hub_48'=>'string',
					'mon_chute_48'=>'string',
					'tue_hub_24'=>'string',
					'tue_chute_24' => 'string',
					'tue_hub_48' => 'string',
					'tue_chute_48'=>'string',
					'wed_hub_24'=>'string',
					'wed_chute_24'=>'string',
					'wed_hub_48'=>'string',
					'wed_chute_48'=>'string',
					'thu_hub_24' => 'string',
					'thu_chute_24' => 'string',
					'thu_hub_48'=>'string',
					'thu_chute_48'=>'string',
					'fri_hub_24'=>'string',
					'fri_chute_24'=>'string',
					'fri_hub_48'=>'string',
					'fri_chute_48' => 'string',
					'sat_hub_24' => 'string',
					'sat_chute_24'=>'string',
					'sat_hub_48'=>'string',
					'sat_chute_48'=>'string',
					'sun_hub_24'=>'string',
					'sun_chute_24'=>'string',
					'sun_hub_48'=>'string',
					'sun_chute_48'=>'string',
					'sat_delivery_hub'=>'string',
					'sat_delivery_chute'=>'string'
					);

		//
		parent::__construct("parcelforce_hub_details", 'id', $fieldList, $mixedCreator);
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
	public static function getParcelForceHubDetailsListFromSql($sql)
	{
		
		return DbAccess3::getListFromSql(__CLASS__, $sql);
	}
}
