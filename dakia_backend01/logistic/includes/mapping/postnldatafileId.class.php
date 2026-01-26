<?php
/**
 * DHL day definite domestic bookings are sent to their links system.
 * Records name of files sent.
 *
 */
class PostnlDataFileId extends DbAccess3
{
	/**
	 * Construct
	 *
	 * @param id/array
	 */
 	public function __construct($mixedCreator = null)
	{
		$fieldList = array(
					'file_name' => 'string',
					'sent_date' => 'datetime',
					'service_country' => 'string',
					'file_id' => 'number'
					);
		//
		parent::__construct("postnl_datafile_id", 'id', $fieldList, $mixedCreator);
	}

	/**
	 * Get object Id (not provided as magic method) - read only.
	 *
	 */
	public function getId()
	{
		return $this->valArray["id"];
	}

	public static function getFileList(PostnlDatafileIdFilter $filter = null)
	{
		if ($filter == null) $filter = new PostnlDatafileIdFilter();
		$sql = $filter->getSql();
		return self::getListFromSql(__CLASS__, $sql);
	}
	 public static function getPostNlListFromSql($sql) {
        return DbAccess3::getListFromSql(__CLASS__, $sql);
    }
}
