<?php

////////////////////////////////////////////////////
//
// Class for dealing with input CSV files of format 1
//
////////////////////////////////////////////////////

class InputFileDutyTaxes
{
	private $if1_file_handle;
	private $if1_file_name;
	private $if1_row;

	// mapping of CSV file fields
	// => better as constant?
        
        //$headers = fgetcsv($handle, 256, ';');
        
        
	public $if1_field_list = array(
				"origin_country_iso" => 0,
                "destination_country_iso"=> 1,
				"weight" => 2,
				"weight_unit" => 3,
				"item_value" => 4,
				"value_currency" => 5,
				"quantity" => 6,
				"dims_unit" => 7,
				"parcel_length" => 8,
				"parcel_width" => 9,
				"parcel_height" => 10,
                                "categories" => 11,
                                "description" => 12,
				"status" => 13,
				"message" => 14,
				"commodity" => 15,
				"hscode" => 16,
				"duty_tax" => 17,
				"clearence_fee" => 18,
				"shipment_cost" => 19,
				"total" => 20
				
               );

	
	/**
	* Constructor
	* - open file by name
	* NB if file fails to open, object still constructed
	*/
	public function __construct($file_name=null)
	{
            //$this->if1_field_list    =   array();
            //$this->weightlimits     =   array();
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
                                $this->if1_field_list['origin_country_iso']    =   $key;
                            break;
                            case 1:
                                $this->if1_field_list['destination_country_iso']     =   $key;
                            break;
							case 2:
                                $this->if1_field_list['weight']     =   $key;
                            break;
							case 3:
                                $this->if1_field_list['weight_unit']     =   $key;
                            break;
							case 4:
                                $this->if1_field_list['item_value']     =   $key;
                            break;
							case 5:
                                $this->if1_field_list['value_currency']     =   $key;
                            break;
							case 6:
                                $this->if1_field_list['quantity']     =   $key;
                            break;
							case 7:
                                $this->if1_field_list['dims_unit']     =   $key;
                            break;
							case 8:
                                $this->if1_field_list['parcel_length']     =   $key;
                            break;
							case 9:
                                $this->if1_field_list['parcel_width']     =   $key;
                            break;
							case 10:
                                $this->if1_field_list['parcel_height']     =   $key;
                            break;
                            case 11:
                                $this->if1_field_list['categories']     =   $key;
                            break;
                        case 12:
                                $this->if1_field_list['description']     =   $key;
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

	public function csv_header(){
		return $this->if1_field_list;
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