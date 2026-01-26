<?php

class UsaState extends DbAccess21
{
	/*
	 * Create and define class
	 */
 	public function __construct($mixedCreator = null)
	{
		$fieldList =
			array(	'code'  => 'string',
					'state' => 'string'
					);
		//
		parent::__construct("usa_states", 'id', $fieldList, $mixedCreator);
	}

	public static function getStateFromCode($code)
	{
		$filter = new UsaState();
		$filterArray = array ("code" => $code);

		$list = $filter->getList($filterArray);

		if (sizeof($list) > 0) return $list[0];
		return null;
	}

	/*
	 * Get List of USA States
	 */
	public static function getList($filterArray = null)
	{
		return parent::getObjectList(__CLASS__, $filterArray);
	}

	/**
	 * State Id
	 */
	public function getId() {return $this->valArray["id"]; }

	/**
	 * State Name
	 */
	public function getState() { return $this->valArray["state"]; }

}