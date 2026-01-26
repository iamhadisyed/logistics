<?php
class GenericFunctions
{
	
	//RETURN DOOR NUMBER FROM ADDRESS LINE 1
	public static function getDoorNumber ($address_line_1)
	{
		
	 $aMatch         = array();
    //$pattern        = '#^([\w[:punct:] ]+) ([0-9]{1,5})([\w[:punct:]\-/]*)$#';
         $pattern = '~^(.*?)((?:unit )?(?:[0-9]+\s?-\s?[0-9]+|[0-9]+))(.*)$~is';
         //$pattern = '/([^\d]+)\s?(.+)/i';
    $matchResult    = preg_match($pattern, $address_line_1, $aMatch);
     
    $street         = (isset($aMatch[1])) ? $aMatch[1] : '';
    $number         = (isset($aMatch[2])) ? $aMatch[2] : '';
    $numberAddition = (isset($aMatch[3])) ? $aMatch[3] : '';
    
    if($street == "" && !is_numeric($numberAddition) && strlen($numberAddition) > 1){
        $street = $numberAddition;
    }
	return array('street' => $street, 'number' => $number, 'numberAddition' => $numberAddition);
		
	/*	$number = "";
		if(preg_match('/(?P<address>[^\d]+) (?P<number>\d+.?)/', $address_line_1, $matches))
		{
			$number = $matches['number'];
		}*/
	//	return $number;
	}


	
}
