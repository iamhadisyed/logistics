<?php

////////////////////////////////////////////////////
//
// Class for dealing with input CSV files of format 1
//
////////////////////////////////////////////////////

class InputFile1
{
	private $if1_file_handle;
	private $if1_file_name;
	private $if1_row;

	// mapping of CSV file fields
	// => better as constant?
        
        //$headers = fgetcsv($handle, 256, ';');
        
        
	public $if1_field_list = array(
				"productname" => 0,
                                "countryiso"=> 1,
				"0" => 2,
				"0.25" => 3,
				"0.50" => 4,
				"0.75" => 5,
				"1.00" => 6,
				"1.25" => 7,
				"1.50" => 8,
				"1.75" => 9,
				"2.00" => 10,
				"2.50" => 11,
				"3.00" => 12,
				"3.50" => 13,
				"4.00" => 14,
				"4.50" => 15,
				"5.00" => 16,
				"5.50" => 17,
				"6.00" => 18,
				"6.50" => 19,
				"7.00" => 20,
				"7.50" => 21,
				"8.00" => 22,
				"8.50" => 23,
				"9.00" => 24,
				"9.50" => 25,
				"10.00" => 26,
				"10.50" => 27,
				"11.00" => 28,
				"11.50" => 29,
				"12.00" => 30,
				"12.50" => 31,
				"13.00" => 32,
				"13.50" => 33,
				"14.00" => 34,
				"14.50" => 35,
				"15.00" => 36,
				"15.50" => 37,
				"16.00" => 38,
				"16.50" => 39,
				"17.00" => 40,
				"17.50" => 41,
				"18.00" => 42,
				"18.50" => 43,
				"19.00" => 44,
				"19.50" => 45,
				"20.00" => 46,
				"20.50" => 47,
				"21.00" => 48,
				"21.50" => 49,
				"22.00" => 50,
				"22.50" => 51,
				"23.00" => 52,
				"23.50" => 53,
				"24.00" => 54,
				"24.50" => 55,
				"25.00" => 56,
				"25.50" => 57,
				"26.00" => 58,
				"26.50" => 59,
				"27.00" => 60,
				"27.50" => 61,
				"28.00" => 62,
				"28.50" => 63,
				"29.00" => 64,
				"29.50" => 65,
				"30.00" => 66
               );

	/**
	* Constructor
	* - open file by name
	* NB if file fails to open, object still constructed
	*/
	public function __construct($file_name)
	{
            $this->if1_field_list    =   array();
            $this->weightlimits     =   array();
            $this->if1_file_handle = fopen($file_name, "r");
                
		$this->if1_file_name   = null;
		if ($this->if1_file_handle != null)
		{
                    $header =   fgetcsv($this->if1_file_handle, 10000);
                    foreach($header as $key=>$value)
                    {
                        switch($key)
                        {
                            case 0:
                                $this->if1_field_list['productname']    =   $key;
                            break;
                            case 1:
                                $this->if1_field_list['countryiso']     =   $key;
                            break;
                            default:
                                $this->if1_field_list['weight'.($key-1)] =  $key ;
                                $weightLimitsArray      =   explode('-', $value);
                                $this->weightlimits[]   =   array(trim($weightLimitsArray[0]),trim($weightLimitsArray[1]));
                            break;
                        }
                        if(trim($value)=='')
                            break;
                       
                    }
                        //$this->if1_field_list = fgetcsv($this->if1_file_handle, 10000);
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
                
		$field_num = (int) (isset($this->if1_field_list["$field_name"])?$this->if1_field_list["$field_name"]:'-1');
                if($field_num != '-1')
                {
                    $field_value = $this->if1_row[$field_num];
                }
                else
                {
                    $field_value = '';
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