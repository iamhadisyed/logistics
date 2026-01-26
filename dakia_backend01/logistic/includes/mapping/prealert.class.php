<?php
/**
 * Country Object
 *
 */
//require_once("../includes/settings/config.inc.php");

class PreAlert extends DbAccess3
{
	/**
	 * Construct
	 *
	 * @param id/array
	 */
 	public function __construct($mixedCreator = null)
	{
		$fieldList = array(
					'mawb_id' => 'number',
					'flight_number' => 'string',
					'pieces'=>'string',
					'weight'=>'string',
					'etd'=>'string',
					'eta'=>'string',
					'current_status'=>'string',
					'date_time'=>'string',
					'cleared'=>'string',
					'status'=>'string',
					'comments'=>'string',
					'account'=>'string',
					'files'=>'string',
					'uploadby'=>'string',
					'shed'=>'string',
					'date_entry'=>'string',
					'created_by'=>'number',
					'date_updated'=>'string',
					'updated_by'=>'number',
					'type'=>'string'
					
					
					);

		//
		parent::__construct("pre_alert", 'id', $fieldList, $mixedCreator);
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
	public static function getPreAlertListFromSql($sql)
	{
		
		return DbAccess3::getListFromSql(__CLASS__, $sql);
	}
}
