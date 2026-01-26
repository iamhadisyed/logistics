<?php
// get settings
//require_once("../includes/settings/config.inc.php");

class TrackingEstimatedTime extends DbAccess3
{
	public function __construct($mixedCreator = null)
	{
		$fieldList = array
					(
					'id'      => 'number',
					'handeling_code'      => 'string',
					'country_iso'      => 'string',
					'estimated_time'   => 'string',
					);
		//
		parent::__construct("tracking_estimated_time", 'id', $fieldList, $mixedCreator);
	}

	public static function getTrackingEstimatedTrackingListFromSql($sql)
	{
		return DbAccess3::getListFromSql(__CLASS__, $sql);
	}


}  // class
