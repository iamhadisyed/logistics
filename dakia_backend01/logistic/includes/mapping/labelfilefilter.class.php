<?php
class LabelFileFilter
{
	private $limit = -1;
	private $filter = "";

	/**
	 * Get array of label files
	 *
	 * @return array
	 */
	public function getList ()
	{
		$limit = "";
		if ($this->limit > 0) $limit = "limit " . $this->limit;
		//
		$where = "";
		if ($this->filter != "") $where = "WHERE " . $this->filter;

		$sql = "SELECT * FROM label_file
				$where
				ORDER BY id desc
				$limit
				";

		return LabelFile::getFileListFromSql($sql);
	}
	
	/**
	 * Get array of label files
	 *
	 * @return array
	 */
	public function  getColumnList($fields)
	{
		$limit = "";
		if ($this->limit > 0) $limit = "limit " . $this->limit;
		//
		$where = "";
		if ($this->filter != "") $where = "WHERE " . $this->filter;

		$sql = "SELECT ".$fields." FROM label_file
				$where
				ORDER BY id desc
				$limit
				";

		return LabelFile::getFileListFromSql($sql);
	}

	/***
	 * Limit then number of rows returned
	 */
	public function setLimit ($val)
	{
		$this->limit = $val;
	}


	public function addIdFilter($id)
	{
		if ($this->filter == "") $this->filter .= " AND ";
		$this->filter = "id='" . DbAccess3::escape($id) . "'";
	}
	
	
}