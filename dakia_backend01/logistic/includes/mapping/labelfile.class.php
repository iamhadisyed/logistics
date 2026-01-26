<?php
class LabelFile extends DbAccess3
{
	/**
	 * Construct
	 *
	 * @param id/array
	 */
 	public function __construct($mixedCreator = null)
	{
		$fieldList = array(
					'file_name' 		=> 'string',
					'account_number'	=> 'string',
					'hawb_list' 		=> 'string',
					'created_date' 		=> 'datetime',
					'error_list' 		=> 'string'
					);
		//
		parent::__construct("label_file", 'id', $fieldList, $mixedCreator);

		// Ensure there is always a file name available.
		if ($this->valArray["file_name"] == "")
		{
			$prefix = Date("Ymd_");
			$this->valArray["file_name"] = uniqid($prefix) . ".pdf";
		}
		// new item
		if ($this->valArray["id"] <= 0)
		{
			$this->valArray["created_date"] = date("Y-m-d H:i:s", time());
		}
	}

	/**
	 * Get object Id (not provided as magic method) - read only.
	 *
	 */
	public function getId()
	{
		return $this->valArray["id"];
	}

	/***
	 * Get a list of label file objects
	 * for given SQL
	 */
	public static function getFileListFromSql ($sql)
	{
		return self::getListFromSql(__CLASS__, $sql);
	}

	/***
	 * Gets the full page to the folder
	 */
	public function getFullPath()
	{
		$path = "../_assets/pdf/" . Date("Y_m_d", $this->getCreatedDate()) . "/";

		if (!file_exists($path)) @mkdir($path, 0777);

		return $path . $this->getFileName();
	}

}