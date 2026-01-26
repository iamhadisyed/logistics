<?php
class ReamusServiceProduct extends DbAccess3
{
	/**
	 * Construct
	 *
	 * @param id/array
	 */
 	public function __construct($mixedCreator = null)
	{
		$fieldList = array(
			'reamus_id' => 'string',
			'product_code' => 'string',
			'feature_code' => 'string',
			'exception' => 'string'
		);
		//
		parent::__construct("reamus_product_service", 'id', $fieldList, $mixedCreator);
	}

	/***
	 * Service Id
	 */
	public function getId()
	{
		return $this->valArray["id"];
	}

	/***
	 * Get list of service products, based on filter passed
	 */
	public static function getFilteredList(ReamusServiceProductFilter $filter)
	{
		return parent::getListFromSql(__CLASS__, $filter->getSQL());
	}

	/***
	 * Factory method to get a service from a feature code.
	 */
	public static function getProductService ($reamus_id, $product_code, $feature_code)
	{
		$sql = "SELECT * from reamus_product_service
				WHERE reamus_id='" . $reamus_id . "'
				AND product_code='$product_code'
				AND feature_code='$feature_code'
				";
		//t($sql);
		//
		$list = parent::getListFromSql(__CLASS__, $sql);
		if (sizeof($list) > 0) return $list[0];
		return null;
	}
}
