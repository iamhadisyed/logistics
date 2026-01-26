<?php
/**
 * Standard Address Interface
 *
 */
interface iAddress
{
	public function getFullName();
	public function getTelephone();
	public function getCompany();
	public function getAddressLine1();
	public function getAddressLine2();
	public function getCity();
	public function getPostcode();
	//public function getCountry();
	//public function getCountryIsoCode();
	//
	public function setTelephone($value);
	public function setCompany($value);
	public function setAddressLine1($value);
	public function setAddressLine2($value);
	public function setCity($value);
	public function setPostcode($value);
	//public function setCountry($value);
	//public function setCountryIsoCode($value);
	//
	public function save();
}