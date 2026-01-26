<?php
/**
 * Parcelgroupconsignment - Parcel group consignment class
 * - deals with Parcel group consignments
 *
 */
class LanguageKeys extends DbAccess3 
{	/**
	 * Construct
	 *
	 * @param id/array
	 */
 	public function __construct($mixedCreator = null)
	{
		$fieldList = array(
					'id'	=> 'number',
					'keyword' => 'string',
					'language' => 'string',
					'caption' => 'string',
					'date_created' => 'string',
					'createdby' => 'number',
					'date_updated' => 'string',
					'updatedby' => 'number',
					
                    'key_captions' => 'undefined',
                    'caption_language' => 'undefined'
					);
		//
		parent::__construct("language_keys", 'id', $fieldList, $mixedCreator);
	}
	
	public static function getLanguageKeysListFromSql($sql)
	{
		return DbAccess3::getListFromSql(__CLASS__, $sql);
	}
        public static function checkKeyExist($languageKey,$langType,$translationName) {
            $query = "SELECT COUNT(id) AS total FROM `language_keys` WHERE `language_keys`.`keyword` ='".DbAccess3::escape($languageKey)."' AND `language_keys`.`language` ='".DbAccess3::escape($langType)."' AND `language_keys`.`caption` ='".DbAccess3::escape($translationName)."'";
            $rs = DbAccess3::runQuery($query);
            $data = mysqli_fetch_assoc($rs);
            return $data['total'];
        }
	
	


   
   
}