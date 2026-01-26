<?php
class ReamusSiteFilter
{
	private $filter = "";

	/**
	 * Gets SQL based on filter conditions that have been set.
	 *
	 * @return string
	 */
	public function getSQL()
	{
		$filter = "";
		if ($this->filter != "")
		{
			$filter = "WHERE " . substr($this->filter, 4);
		}

		$sql = "SELECT * FROM reamus_site $filter";
		t($sql);
		return $sql;
	}

	/***
	 * Filter on the reamus Id
	 */
	public function addReamusIdFilter ($reamus_id)
	{
		$this->filter .= "AND reamus_id='$reamus_id' ";
	}
}