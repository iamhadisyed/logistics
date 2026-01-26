<?php

class ParsePostcode
{
	
	public static function Parse($postcode, $countryIsoCode)
	{
		//echo "length " . strlen($trackingNumber);
		
	//	$postcode = str_replace(' ','', strtoupper($postcode)); 
	//	$postcode = str_replace('-','', strtoupper($postcode)); 
	$postcode = strtoupper($postcode); 
		
		$ZIPREG=array(
			"AD"=>"^AD\d{3}$", 
			"AL"=>"^\d{4}$", 
			"AT"=>"^[1-9]\d{3}$", 
			"AU"=>"^(0[289][0-9]{2})|([1345689][0-9]{3})|(2[0-8][0-9]{2})|(290[0-9])|(291[0-4])|(7[0-4][0-9]{2})|(7[8-9][0-9]{2})$", 
			"BA"=>"^\d{5}$", 
			"BE"=>"^[1-9]{1}[0-9]{3}$", 
			"BG"=>"^\d{4}$", 
			"BR"=>"^\d{5}([- ]?\d{3})?$", 
			"BY"=>"^\d{6}$", 
			"CA"=>"^([ABCEGHJKLMNPRSTVXY]\d[ABCEGHJKLMNPRSTVWXYZ])\ {0,1}(\d[ABCEGHJKLMNPRSTVWXYZ]\d)$", 
			"CH"=>"^\d{4}$", 
			"CN"=>"^\d{6}$", 
			"CY"=>"^\d{4}$", 
			"CZ"=>"^\d{3}[ ]?\d{2}$", 
			"DE"=>"\b((?:0[1-46-9]\d{3})|(?:[1-357-9]\d{4})|(?:[4][0-24-9]\d{3})|(?:[6][013-9]\d{3}))\b", 
			"DK"=>"(DK)?[-]?\d{4}$", 
			"EE"=>"^\d{5}$", 
			"ES"=>"^([1-9]{2}|[0-9][1-9]|[1-9][0-9])[0-9]{3}$", 
			"FI"=>"^\d{5}$", 
			"FR"=>"^(F-)?((2[A|B])|[0-9]{2})[0-9]{3}$", 
			"GB"=>"^(GIR|[A-Z]\d[A-Z\d]??|[A-Z]{2}\d[A-Z\d]??)[ ]??(\d[A-Z]{2})$", 
			"GL"=>"^\d{4}$", 
			"GR"=>"^\d{3}\s?\d{2}$", 
			"HR"=>"^\d{5}$", 
			"HU"=>"^\d{4}$", 
			"IL"=>"(^\d{7}$)|(^\d{5}$)", 
			"IN"=>"^\d{6}$", 
			"IQ"=>"^\d{5}$", 
			"IS"=>"^\d{3}$", 
			"IT"=>"^(V-|I-)?[0-9]{5}$", 
			"LT"=>"^(LT)?[-]?\d{5}$", 
			"LU"=>"^(L)?[-]?\d{4}$", 
			"LV"=>"^(LV)?[-]?\d{4}$", 
			"MA"=>"^\d{2}\s?\d{3}$", 
			"MD"=>"^(MD)?[-]?\d{4}$", 
			"ME"=>"^\d{5}$", 
			"MK"=>"^\d{4}$", 
			"NL"=>"^[1-9][0-9]{3}\s?([a-zA-Z]{2})?$", 
			"NO"=>"^\d{4}$", 
			"PL"=>"^\d{2}[- ]?\d{3}$", 
			"PT"=>"^\d{4}([-]?\d{3})?$", 
			"RO"=>"^\d{6}$", 
			"RS"=>"^\d{5}$", 
			"RU"=>"^\d{6}$", 
			"SE"=>"^(s-|S-){0,1}[0-9]{3}\s?[0-9]{2}$", 
			"SI"=>"^(SI)?[-]?\d{4}$", 
			"SK"=>"^\d{5}$", 
			"TR"=>"^\d{5}$", 
			"UA"=>"^\d{5}$", 
			"US"=>"^\d{5}([\-]?\d{4})?$"

		);
 $postcode_validation = $ZIPREG[$countryIsoCode];
		if (trim($postcode_validation) != '') {
		 preg_match("/".$postcode_validation."/i",$postcode, $matches);
			if (sizeof($matches) <= 0){
				//Validation failed, provided zip/postal code is not valid.
				return "Please enter correct postcode.";
			} 
		}
		 

	}

}