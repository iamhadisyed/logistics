<?php
class ReamusException extends DbAccess3
{
	/**
	 * Construct
	 *
	 * @param id/array
	 */
 	public function __construct($mixedCreator = null)
	{
		$fieldList = array(
			'country_code' => 'string',
			'postcode_from' => 'string',
			'postcode_to' => 'string',
			'product_code' => 'string',
			'feature_code' => 'string'
		);
		//
		parent::__construct("reamus_exception", 'id', $fieldList, $mixedCreator);
	}

	/***
	 * Service Id
	 */
	public function getId()
	{
		return $this->valArray["id"];
	}

	public function setPostcodeTo($postcode)
	{
		$this->valArray["postcode_to"] = str_replace(" ", "", $postcode);
	}
	public function setPostcodeFrom($postcode)
	{
		$this->valArray["postcode_from"] = str_replace(" ", "", $postcode);
	}

	/***
	 * Factory method to get a service from a feature code.
	 */
	public static function getForPostcodeFromAndProductCode ($postcode, $product_code, $feature_code)
	{
		$postcode= str_replace(" ", "", $postcode);
		$sql = "SELECT * from reamus_exception
				WHERE postcode_from='" . $postcode . "'
				AND product_code='$product_code'
				AND feature_code='$feature_code'
				";
		//t($sql);
		//
		$list = parent::getListFromSql(__CLASS__, $sql);
		if (sizeof($list) > 0) return $list[0];
		return null;
	}

	public static function getFilteredList (ReamusExceptionFilter  $filter)
	{
		return parent::getListFromSql(__CLASS__, $filter->getSQL());
	}
}
