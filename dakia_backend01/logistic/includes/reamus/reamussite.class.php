<?php
class ReamusSite extends DbAccess3
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
			'site' => 'string',
			'reamus_id2' => 'string',
			'country_code' => 'string'
		);
		//
		parent::__construct("reamus_site", 'id', $fieldList, $mixedCreator);
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
	 */
	public static function getSiteFromReamusId ($reamus_id)
	{
		 $sql = "SELECT * from reamus_site WHERE reamus_id='" . $reamus_id . "'";
		//
		$list = parent::getListFromSql(__CLASS__, $sql);
		if (sizeof($list) > 0) return $list[0];
		return null;
	}
}



