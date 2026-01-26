<?php
class ReamusDestinationStationFilter
{
	private $filter = "";

	/***
	 * Get list of destionation stations based on
	 * filter conditions set.
	 */
	public function getSQL()
	{
		$filter = "";
		if ($this->filter != "")
		{
			$filter = "WHERE " . substr($this->filter, 4);
		}

		$sql = "SELECT * FROM reamus_destination_station ds " .
				$filter;
		//t($sql);
		return $sql;
	}

	/***
	 * Filter on postcode
	 */
	public function AddPostcodeFilter ($postcode)
	{
		$postcode = str_replace (" ", "", $postcode);
		$this->filter .= "AND postcode_from <= '" . $postcode . "' ";
		$this->filter .= "AND postcode_to >= '" . $postcode . "' ";
		$this->filter .= "AND length(postcode_to) = " . strlen($postcode) . " ";
		//
	}

	/***
	 * Filter on the product code
	 */
	public function AddProductCodeFilter($product_code)
	{
		$this->filter .= "AND product_code = '" . $product_code . "' ";
	}


}