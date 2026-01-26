<?php
class ReamusDestinationStation extends DbAccess3
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
			'station_id' => 'string',
			'hub_id' => 'string'
		);
		//
		parent::__construct("reamus_destination_station", 'id', $fieldList, $mixedCreator);
	}

	/***
	 * Gets list of destination stations based on filter passed
	 */
	public static function getFilteredList (ReamusDestinationStationFilter $filter)
	{
		return self::getListFromSql(__CLASS__, $filter->getSQL());
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
	public static function getForPostcodeFromAndProductCode ($postcode, $product_code)
	{
		$postcode= str_replace(" ", "", $postcode);
		$sql = "SELECT * from reamus_destination_station
				WHERE postcode_from='" . $postcode . "'
				AND product_code='$product_code'
				";
		t($sql);
		//
		$list = parent::getListFromSql(__CLASS__, $sql);
		if (sizeof($list) > 0) return $list[0];
		return null;
	}
}
