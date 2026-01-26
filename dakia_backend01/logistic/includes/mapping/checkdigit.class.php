<?php
class ISO7064Decoder
{
    public static $iso7064 = array(
    '0' => 0,
    '1' => 1,
    '2' => 2,
    '3' => 3,
    '4' => 4,
    '5' => 5,
    '6' => 6,
    '7' => 7,
    '8' => 8,
    '9' => 9,
    'A' => 10,
    'B' => 11,
    'C' => 12,
    'D' => 13,
    'E' => 14,
    'F' => 15,
    'G' => 16,
    'H' => 17,
    'I' => 18,
    'J' => 19,
    'K' => 20,
    'L' => 21,
    'M' => 22,
    'N' => 23,
    'O' => 24,
    'P' => 25,
    'Q' => 26,
    'R' => 27,
    'S' => 28,
    'T' => 29,
    'U' => 30,
    'V' => 31,
    'W' => 32,
    'X' => 33,
    'Y' => 34,
    'Z' => 35
);

public static function calculateCheckDigit($value)
{
    $lngCheck = 36;
    $lngLen = strlen($value);

    for($lng = 0; $lng <= strlen($value)-1; $lng++)
    {
        $lngCheck = $lngCheck + self::charToNumber(substr($value, $lng, 1));

        if($lngCheck > 36)
        {
            $lngCheck = $lngCheck - 36;
        }
        $lngCheck = $lngCheck * 2;
        if($lngCheck >= 37)
        {
            $lngCheck = $lngCheck - 37;
        }
    }
    $lngCheck = 37 - $lngCheck;
    if($lngCheck == 36)
    {
        $lngCheck = 0;
    }

    return $lngCheck;
}

public static function charToNumber($value)
{
    $value = strtoupper($value);

    if(!array_key_exists($value, static::$iso7064))
    {
        return -1;
    }
    else
    {
        return static::$iso7064[$value];
    }
}

public static function NumberToChar($arraykey)
{

    $array = static::$iso7064 ;
   // print_r($array );


	foreach ($array as $key => $value) {
    if ($arraykey == $value)
    return $key;
    }
	return -1;

}










}
?>