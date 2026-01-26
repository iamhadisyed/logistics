<?php

////////////////////////////////////////////////////
//
// Class for dealing with input CSV files of format 1
//
////////////////////////////////////////////////////

class InputFileCon
{
	private $if1_file_handle;
	private $if1_file_name;
	private $if1_row;

	// mapping of CSV file fields
	// => better as constant?
	private $if1_field_list = array(
                "account"   		=> 0,
				"hawb"				=> 1,
				"service"			=> 2,
                "service_code"      => 3,
				"reference"			=> 4,
				"date_submitted"	=> 5,
				"company"			=> 6,
				"contact"			=> 7,
				"address_line_1"	=> 8,
				"address_line_2"	=> 9,
                "address_line_3"    => 10,
				"city"				=> 11,
				"country"			=> 12,
				"postcode"			=> 13,
				"telephone"			=> 14,
				"number_pieces"		=> 15,
				"weight"			=> 16,
				"description"		=> 17,
				"value"				=> 18,
				"currency"			=> 19,
				// fields 19-20 unused
				"notes"				=> 20,
				"both_checked"      => 21,
				"full_pallet"      	=> 22,
				"half_pallet"      	=> 23,
				"quarter_pallet"    => 24,
				"all_weight"      	=> 25,
				"all_width"      	=> 26,
				"all_height"      	=> 27,
				"all_length"      	=> 28,
				"itemtype"			=> 29,
				"awb"				=> 30,
				"email"				=> 31,
				"flight_number"		=> 32,
				"bag_number"		=> 33,		
				"mawb"				=> 34,	
				"TermsofPayment"	=> 35,
				"ReasonforExport"	=> 36,
				"Comments" 			=> 37,
				"TermsofDelivery"	=> 38,
				"PayerofVat"		=> 39,
				"HarmCode"			=> 40,
				"TypeofExport"		=> 41,
				"InvoiceType"		=> 42,
				"ThirdPartyCompany"	=> 43,
				"ThirdPartyContact"	=> 44,
				"ThirdPartyAddLine1"=> 45,
				"ThirdPartyAddLine2"=> 46,
				"ThirdPartyAddLine3"=> 47,
				"ThirdPartyCity" 	=> 48,
				"ThirdPartyCountry"	=> 49,
				"ThirdPartyPostcode"=> 50,
				"ThirdPartyTelephone"=> 51,
				"NumberofItem"		 => 52,
				"InvoiceDescription" => 53,
				"InvoiceWeight"		 => 54,
				"InvoiceValue"		 => 55,
				"InvoiceQuantity"	 => 56,
				"InvoiceTarrifNo"	 =>	57,
				"InvoiceManufactureCountry" => 58,
				
			);

	/**
	* Constructor
	* - open file by name
	* NB if file fails to open, object still constructed
	*/
	public function __construct($file_name)
	{
		$this->if1_file_handle = fopen($file_name, "r");
		$this->if1_file_name   = null;
		if ($this->if1_file_handle != null)
		{
			$this->if1_file_name = $file_name;
			t("Input file " . $this->if1_file_name . " opened");
			$this->reset();
		}
	}

	/**
	* Destructor
	* - close file by handle
	*/
	public function __destruct()
	{
		if ($this->if1_file_handle != null)
		{
			fclose ($this->if1_file_handle);
			t("Input file " . $this->if1_file_name . " closed");
		}
	}

	/**
	* Reset file
	* - extract header row to navigate to first normal row
	* - leaves current row content set to header in case this is useful
	* - expect calling function to select next [normal] row before using it
	* - NB assumes exactly one header row
	* @return bool
	*/
	public function reset()
	{
		$this->if1_row = null;
		if (($this->if1_file_handle == null) || feof($this->if1_file_handle)) return false;
		$this->if1_row = fgetcsv($this->if1_file_handle, 10000);
		t("Input file " . $this->if1_file_name . " reset");
		return true;
	}

	/**
	* File exists?
	* - object might exist but without associated file
	* @return bool
	*/
	public function exists()
	{
		return ($this->if1_file_handle != null);
	}

	/**
	* Set file position
	* @param $position
	* @return bool
	*/
	public function setPosition($position)
	{
		if ($this->if1_file_handle == null) return false;
		fseek($this->if1_file_handle, $position);
		return true;
	}

	/**
	* Get file position
	* @return position
	*/
	public function getPosition()
	{
		if ($this->if1_file_handle == null) return null;
		return ftell($this->if1_file_handle);
	}

	/**
	* Extract next row
	* @return bool
	*/
	public function nextRow()
	{
		// ensure file valid and not at end
		while (true)
		{
			if (($this->if1_file_handle == null) || (feof($this->if1_file_handle))) return false;
			$this->if1_row = fgetcsv($this->if1_file_handle, 10000);
			// ignore empty lines
			if (sizeof($this->if1_row) > 1) break;
		}
		return true;
	}

	/**
	* Get field from current row
	* @param $field_name
	* @return value [or null]
	*/
	public function getField($field_name)
	{
		// file handle must be valid
		if ($this->if1_file_handle == null) return null;

		// return field content
		// - unknown field maps to field number 0
		// !!! should check for empty row
		$field_num = (int) $this->if1_field_list["$field_name"];
		$field_value = $this->if1_row[$field_num];

		// special case for date/time
		// - change / to - for strtotime()
		// !!! more generic way of doing this?
		if ($field_name == "date_submitted")
		{
			$field_value = strtotime(strtr($field_value, "/", "-"));
		}
        		return $field_value;
	}

	/**
	* Get row
	* - mainly for debug purposes
	* @return row
	*/
	public function getRow()
	{
		return $this->if1_row;
	}
	
	public function arraySize()
	{
		return sizeof($this->if1_field_list);
	}

	/***
	 * Dimensions are stored in the following format
	 * l*w*h;l*w*h; etc for the number of parcels.
	 * Returns them in an n x 3 array (n being numer of parcels).
	 *
	 * @return array
	 */
	public function getDimensionArray()
	{
		$dimension_string = $this->getField("dimensions");
		$dimension_array = array();

		if ($dimension_string != "")
		{
			$parcels = explode(";", $dimension_string);
			foreach ($parcels as $parcel_str)
			{
t("parcel string is " . $parcel_str);
				$dimensions = array (0, 0, 0);
				$new_dimensions = explode("*", $parcel_str);
				if (sizeof($new_dimensions) > 0) $dimensions[0] = intval($new_dimensions[0]);
				if (sizeof($new_dimensions) > 1) $dimensions[1] = intval($new_dimensions[1]);
				if (sizeof($new_dimensions) > 2) $dimensions[2] = intval($new_dimensions[2]);
                if (sizeof($new_dimensions) > 3) $dimensions[3] = intval($new_dimensions[3]);
				//
				$dimension_array[] = $dimensions;
			}
		}
		return $dimension_array;
	}

}