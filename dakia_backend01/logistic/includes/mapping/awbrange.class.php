<?php
/**
 * DHL Airway Bills
 *
 */
class AwbRange extends DbAccess3
{
 	const  STATIC_PREFIX_CZECH = "98882";
	const  STATIC_PREFIX_IRELAND = "OC";
	
	public function __construct($mixedCreator = null)
	{
		$fieldList =
			array(  'range_start' => 'number',
					'range_end'   => 'number',
					'next_number'  => 'number',
					'increment_date' => 'date',
					'delivery_network' => 'string'
					);
		//
		parent::__construct("dhl_awb_numbers", 'id', $fieldList, $mixedCreator);
	}

	/*
	 * Get id
	 */
	public function getId() { return $this->valArray["id"]; }

	/**
	* Gets next available airways bill (and increments)
	*
	* @return AWB Number
	*/
	public static function getAwb ($deliveryNetwork)
	{
		if ($deliveryNetwork == "REGPOSTHUN")
		{
		    $prefix = "RR";
		    $suffix = "HU";
		}
		else if ($deliveryNetwork == "REGPOSTEST")
		{
		    $prefix = "RR";
		    $suffix = "EE";
		}
		else if ($deliveryNetwork == "REGPOSTIRE")
		{
		    $prefix = "CE";
		    $suffix = "IE";
		}
		else if ($deliveryNetwork == "REGPOSTPHL")
		{
		    $prefix = "RR";
		    $suffix = "PH";
		}
		else if ($deliveryNetwork == "REGPOSTUTR")
		{
            $prefix = "OWE";
		    $suffix = "LHR";
		  
		}
		else if ($deliveryNetwork == "REGPOSTTR")
		{
			$prefix = "RE";
		    $suffix = "SE";		
		}
		else if ($deliveryNetwork == "REGPOSTCZ")
		{
			$prefix = "RR";
		    $suffix = "CZ";		
		}
		
		
		 $sql = "SELECT * FROM dhl_awb_numbers
				WHERE (next_number <= range_end
				  OR (datediff(Now(), increment_date) > 366)) AND
                  (delivery_network='".DbAccess3::escape($deliveryNetwork).
				"' ) ORDER BY increment_date DESC";

		t($sql, __CLASS__);

		$list = DbAccess3::getListFromSql(__CLASS__, $sql);
		//
		if (sizeof($list) > 0)
		{
			$val = $list[0]->getNextNumber();
	
			// reusing a range.
			if ($val > $list[0]->getRangeEnd())
			{
				$val = $list[0]->getRangeStart();
			}
			//
			$list[0]->setNextNumber($val + 1);
			$list[0]->setIncrementDate(time());
			$list[0]->save();
			
			
			if ($deliveryNetwork == "REGPOSTTR" ||$deliveryNetwork == "REGPOSTUTR" || $deliveryNetwork == "REGPOSTPHL" || $deliveryNetwork == "REGPOSTIRE" || $deliveryNetwork == "REGPOSTEST" || $deliveryNetwork == "REGPOSTHUN" || $deliveryNetwork == "REGPOSTCZ")
			{
			$CD = Awbrange::mod11($val);		
			$val = $prefix  . $val . $CD . $suffix;
			}
			
			elseif($deliveryNetwork == "CZECH")
			{
			  $checkdigit =   Awbrange::modEuro($val);			  
			  $val = "DR" . self::STATIC_PREFIX_CZECH . $val .  $checkdigit . "M"  ; 
			}
			
			else  if($deliveryNetwork == "RIIRE")
			{			 
 		  	  $val =  self::STATIC_PREFIX_IRELAND  .  str_pad($val, 10, "0", STR_PAD_LEFT);  
			}
		}
		else
		{
			return -1;
		}


       $awb = $val;
	   return $awb;
	}
	
	 
	 public  static function modEuro($value)
	{
		  $awbno=  self::STATIC_PREFIX_CZECH. $value;
		  $arr = str_split($awbno);
		   $checkdigit = ($arr[0]*1)+($arr[1]*8)+($arr[2]*6)+($arr[3]*4)+($arr[4]*2)+($arr[5]*3)+($arr[6]*5)+($arr[7]*9)+($arr[8]*7);

		  $remainder = $checkdigit%11;

	      $checkdigit = 11 - $remainder ;

		   if ($checkdigit == 10)
		   {
			   $checkdigit = 0;
		   }
		   elseif ($checkdigit == 11)
		   {
			   $checkdigit = 5;
		   }

    	   return $checkdigit;
	}
	 
	 
	 public static function mod11($barcodeno)
	{



		  $arr = str_split($barcodeno);
		   $checkdigit =  ($arr[0]*8)+
		                  ($arr[1]*6)+
						  ($arr[2]*4)+
						  ($arr[3]*2)+
						  ($arr[4]*3)+
						  ($arr[5]*5)+
						  ($arr[6]*9)+
						  ($arr[7]*7);


		  $remainder = $checkdigit%11;

		  $checkdigit = 11 - $remainder ;

		  if ($checkdigit== 10)
		  {
			$checkdigit = 0;
		  }
		  else if ($checkdigit == 11)
		  {
			$checkdigit = 5;
		  }





		return $checkdigit;

	}

	/**
	 * Get last updated value for this AWB range
	 *
	 * @return date
	 */
	public function getLastUpdate()
	{
		return strtotime($this->valArray["last_updated"]);
	}

	/**
	* Set last updated for this AWB range
	*
	* @param $time
	*/
	public function setLastUpdate($time)
	{
		$this->valArray["last_updated"] = date ("Y-m-d H:i:s");
		$this->modifyArray["last_updated"]	= date ("Y-m-d H:i:s");
	}
	
	
	public function getRange()
	{
		$sql = "SELECT * FROM dhl_awb_numbers
				ORDER BY id";
		echo "m,rga";
		//t($sql, __METHOD__);
echo $sql;
		return DbAccess3::getListFromSql(__CLASS__, $sql);
		
	}







}