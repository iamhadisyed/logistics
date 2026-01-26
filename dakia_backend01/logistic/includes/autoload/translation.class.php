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
class Translation
{
	//private static $user = null;
	//private static $label = null;
	/**
	* Constructor.
	*/
	
	public $LanguageKeywordsArray = array();
	
	private function __construct() // make private to force use of factory method.
	{
		
	}
	
	public static function PopulateKeywordsArray()
	{
		if(isset($_SESSION['lang']))
		{
			$language = $_SESSION['lang'];
		}
		else
		{
			$language = "en-GB";
		}
		
		$langFilter = new LanguageKeysFilter();
		$langFilter->addLanguageFilter($language);
		$list = $langFilter->getColumnList("keyword, caption");
		$count = 0;
		
		//print_r($list);
		
		//die;
		
		foreach($list as $language)
		{
			$LanguageKeywordsArray[$count]['keyword'] = $language->getKeyword();
			$LanguageKeywordsArray[$count++]['caption'] = $language->getCaption();
		}
		
		$_SESSION['Translation'] = $LanguageKeywordsArray;		
		
	}
	
	public static function GetKeywordsArray()
	{
		return @$_SESSION['Translation'];
	}
	
	
	public static function GetCaption($keyword)
	{	
            $keyword = trim($keyword);
            $result = $keyword;
		
		$array = self::GetKeywordsArray();
		if(!empty($array)&& count($array) > 0 ){
		
			foreach ($array as $key => $val) 
			{
				if ($val['keyword'] === $keyword) 
				{
					//$result = utf8_encode(($val['caption']));
					$result = html_entity_decode(($val['caption']));
				}
			}
		
		}
		
		return $result;		
	}
	
	private static function makeUTF8($str,$encoding = "") {
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