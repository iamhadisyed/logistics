<?php
// get settings
//require_once("includes/settings/common.inc.php");

class EstimateDeliveryTiming extends DbAccess3
{
	
	public function __construct($mixedCreator = null)
	{
		$this->tablename = 'estimate_delivery_timing';
        $this->pkey      = 'id';
		$fieldList = array
					(
					'id'     			=> 'number',
					'service_id'      	=> 'string',
					'from_rateband'     => 'string',
					'to_rateband'   	=> 'string',
					'date_created' 		=> 'string',
					'delivery_timing' 		=> 'string',
					'created_by' 		=> 'string',
					'status' 			=> 'string'
                   
					);
		//
		parent::__construct("estimate_delivery_timing", 'id', $fieldList, $mixedCreator);
	}
	/**
	 * Get list of user objects, using sql given
	 *
	 * @param string $sql
	 */
	public static function getEstimateDeliveryTimingListFromSql($sql)
	{
		return DbAccess3::getListFromSql(__CLASS__, $sql);

	}
}  // class
