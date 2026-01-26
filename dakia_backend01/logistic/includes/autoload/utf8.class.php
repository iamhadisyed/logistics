<?php
// copied from smart system
////////////////////////////////////////////////////
//
//	Session Management
//
//
////////////////////////////////////////////////////

/**
 * Session Manager
 * @package Ecommerce
 */
class UTF8
{	
	
	public static function makeUTF8($str,$encoding = "") {
		  $str = preg_replace('/[^(\x20-\x7F)]*/','', $str);
		  $str = str_replace('&','and', $str);
		  $str = str_replace('<','&lt;', $str);
	      $str = str_replace('>','&gt;', $str);
	      $str = str_replace("'","", $str);

		  if ($str !== "") {
			if (empty($encoding) && self::isUTF8($str))
			  $encoding = "UTF-8";
			if (empty($encoding))
			  $encoding = mb_detect_encoding($str,'UTF-8, ISO-8859-1');
			if (empty($encoding))
			  $encoding = "ISO-8859-1"; //  if charset can't be detected, default to ISO-8859-1
			return $encoding == "UTF-8" ? $str : @mb_convert_encoding($str,"UTF-8",$encoding);
			}
		  }

   private static function isUTF8($str) {
   return preg_match('%^(?:
         [\x09\x0A\x0D\x20-\x7E]           # ASCII
       | [\xC2-\xDF][\x80-\xBF]            # non-overlong 2-byte
       | \xE0[\xA0-\xBF][\x80-\xBF]        # excluding overlongs
       | [\xE1-\xEC\xEE\xEF][\x80-\xBF]{2} # straight 3-byte
       | \xED[\x80-\x9F][\x80-\xBF]        # excluding surrogates
       | \xF0[\x90-\xBF][\x80-\xBF]{2}     # planes 1-3
       | [\xF1-\xF3][\x80-\xBF]{3}         # planes 4-15
       | \xF4[\x80-\x8F][\x80-\xBF]{2}     # plane 16
   )*$%xs', $str);
  }
	
	
	
	
	

	

}
?>