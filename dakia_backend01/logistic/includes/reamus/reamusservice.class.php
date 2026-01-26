<?php
class ReamusService extends DbAccess3
{
	/**
	 * Construct
	 *
	 * @param id/array
	 */
 	public function __construct($mixedCreator = null)
	{
		$fieldList = array(
			'service_id' => 'number',
			'service_description' => 'string',
			'product_line1' => 'string',
			'product_line2' => 'string',
			'product_code' => 'string',
			'date_code' => 'string',
			'day_text' => 'string',
			'time_code' => 'string',
			'time_text' => 'string',
			'handling' => 'string',
			'feature_id' => 'string',
			'feature_code' => 'string',
			'file_type' => 'string',
			'consignment_flag' => 'string',
			'ds_flag' => 'string'
		);
		//
		parent::__construct("reamus_service", 'id', $fieldList, $mixedCreator);
	}

	/***
	 * Service Id
	 */
	public function getId()
	{
		return $this->valArray["id"];
	}

	/***
	 * Factory method to get a service from a feature code.
	 *
	 * @return ReamusService
	 */
	public static function getServiceFromHandling ($handling)
	{
		$sql = "SELECT * from reamus_service WHERE handling='" . $handling . "'";
		//
		$list = parent::getListFromSql(__CLASS__, $sql);
		if (sizeof($list) > 0) return $list[0];
		return null;
	}
}



