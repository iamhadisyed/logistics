<?php
/**
 * Provides a mechanism for holding summary information about a location
 */
class LocationSummary
{
	private $postcode = "";
	private $country = "";
	private $country_iso = "";

	public function getPostcode()
	{
		return $this->postcode;
	}
	public function setPostcode($value)
	{
		$this->postcode = $value;
	}

	public function getCountry()
	{
		return $this->country;
	}
	public function setCountry($value)
	{
		$this->country = $value;
	}

	public function getCountryIso()
	{
		return $this->country_iso;
	}
	public function setCountryIso($value)
	{
		$this->country_iso = $value;
	}

}