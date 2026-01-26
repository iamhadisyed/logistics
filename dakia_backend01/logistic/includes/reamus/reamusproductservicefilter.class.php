<?php
class ReamusServiceProductFilter
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

		$sql = "SELECT * FROM reamus_product_service $filter";
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

	/**
	 * Filter on product code
	 *
	 * @param string $product_code
	 * @return unknown_type
	 */
	public function addProductCodeFilter ($product_code)
	{
		$this->filter .= "AND product_code='$product_code' ";
	}

	/***
	 * Filter on feature code
	 */
	public function addFeatureCodeFilter ($feature_code)
	{
		$this->filter .= "AND feature_code='$feature_code' ";
	}
}