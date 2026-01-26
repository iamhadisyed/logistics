<?php
/**
 * Country Object
 *
 */
class Country extends DbAccess3
{
	/**
	 * Construct
	 *
	 * @param id/array
	 */
 	public function __construct($mixedCreator = null)
	{
		$fieldList = array(
					'iso' => 'string',
					'iso3' => 'string',
					'name' => 'string',
					'region'=>'string',
					'postcode_required'=>'string',
					'type'=>'string',
					'region_collection'=>'string',
					'numcode' => 'number',
					'allow_express'=>'string',
					'allow_classic'=>'string',
					'eu_country'=>'string',
					'shipping_advice'=>'string',
					'is_vatable'=>'string',
					'vat_rate'=>'string',
					'printable_name'=>'string',
					'export_flag'=>'string',
					'timezone_difference'=>'string',
					'has_postcodeq'=>'string',
					'orderq'=>'string',
					'active'=>'string',
					'deletedq'=>'string','has_postcodeq'=>'string',
					'added_on'=>'string',
					'added_by'=>'string',
					'changed_on'=>'string',
					'changed_by'=>'string',
					'vat_charged_flag'=>'string',
					'customs_flag'=>'string',
					'description'=>'string',
					'country_image'=>'string',
					'metakeywords'=>'string',
					'metadescription'=>'string',
					'pagetitle'=>'string',
					'countrybanner'=>'string',
					'has_subzonesq'=>'string',
					'opcode' => 'string',
					'iso_three' => 'string',
					'german_name' => 'string',
					'manifest_template' => 'string',
					'bag_template' => 'string',
                                        'manifest_template' => 'string',
					'bag_template' => 'string',
					'bag_weight_limit' => 'number',
					'bag_low_value' => 'number',
					'currency_id' => 'number'
					);

		//
		parent::__construct("country", 'id', $fieldList, $mixedCreator);
	}

	/**
	 * Get object Id (not provided as magic method) - read only.
	 *
	 */
	public function getId()
	{
		return $this->valArray["id"];
	}
	
	public static function getCountryFromIso($iso)
	{
            $iso = strToUpper(trim($iso));
            $sql = "SELECT id,name, region from country Where iso = '" . DbAccess3::escape($iso) . "'";
            $list = DbAccess3::getListFromSql(__CLASS__, $sql);
            // return country or null
            if (sizeof($list) > 0) return $list[0];
            return null; 
	}
	
	

	/**
	 * Factory method for getting country from name.
	 * If found returns country object, else returns null.
	 *
	 * @param unknown_type $country_name
	 * @return Country
	 */
	public static function getCountryFromName($country_name)
	{
		$country_name = strtolower($country_name);
		// Provide translation for common usage
		if ($country_name == "england" ) $country_name = "united kingdom";

		$sql 		=	"SELECT * from country where lower(name)='" . DbAccess3::escape($country_name) . "'";
		$list 		=	DbAccess3::getListFromSql(__CLASS__, $sql);
		// return country or null
		if (sizeof($list) > 0) 
			return $list[0];
		return null;
	}
	 public static function getCountryByIso($country_iso) {
        // has filter been configured?
        $country_iso = strtolower($country_iso);
        $sql = "SELECT * FROM country where lower(iso)='" . DbAccess3::escape($country_iso) . "'";
        return Country::getCountryListFromSql($sql);
    }
	
	/**
	 * Factory method for getting country from name.
	 * If found returns country object, else returns null.
	 *
	 * @param unknown_type $country_name
	 * @return Country
	 */
	public static function getIdFromIso($country_iso)
	{
		$country_iso = strtolower($country_iso);
		// Provide translation for common usage
		if ($country_iso == "UK" ) $country_iso = "GB";

		$sql 		=	"SELECT * from country where lower(iso)='" . DbAccess3::escape($country_iso) . "'";
		$list 		=	DbAccess3::getListFromSql(__CLASS__, $sql);
		// return country or null
		if (sizeof($list) > 0) 
			return $list[0]->getId();
		return null;
	}
	
	
	public static function getIsoFromId($country_id)
	{
		$country_id = strtolower($country_id);
		$sql 		=	"SELECT * from country where id='" . DbAccess3::escape($country_id) . "'";
		$list 		=	DbAccess3::getListFromSql(__CLASS__, $sql);
		// return country or null
		if (sizeof($list) > 0) 
			return $list[0]->getIso();
		return null;
	}
	
	public static function nameCountry($country)
	{

		$sql 		=	"SELECT id, name  FROM country WHERE id = '".DbAccess3::escape($country)."'";
		$result 	=	DbAccess3::getListFromSql(__CLASS__, $sql);
		$name	=	'';
		if(count($result)>0)
		{
					$name	=	$result[0]->getName();
		}
		return 	$name;
	}
	
	
	
	public static function vatCountry($country)
	{

		 $sql 		=	"SELECT is_vatable, vat_rate FROM country WHERE id = '".DbAccess3::escape($country)."'";
		$result 	=	DbAccess3::runQuery($sql);
		$vatRate	=	number_format(0.00,2);
		if(mysqli_num_rows($result)>0)
		{
			while($countryRow	=	mysqli_fetch_row($result))
			{
				//print_r($currencyRow);
				if($countryRow[0] == 'YES')
				{
				//	echo '<br>';
				 	$vatRate	=	number_format($countryRow[1],2);
				}
				
			}
			
		}
		return 	$vatRate;
	}
	
	
	/**
	 * Get list of user objects, using sql given
	 *
	 * @param string $sql
	 */
	public static function getCountryListFromSql($sql)
	{
		return DbAccess3::getListFromSql(__CLASS__, $sql);
	}

            
        public static function getCountryDropDown($selectName,$selectId, $country="",$value='iso') {
		
        $sql = "SELECT 
   				 id,iso, name, region, german_name
					FROM
						country";
        $rs = self::runQuery($sql);
        $option = "";
        $option .= '<select name="'.$selectName.'"  id="'.$selectId.'" class="bs-select form-control select2" required="" data-show-subtext="true">';
        $option .= "<option value=''>Select Country</option>" ;
        while ($valArray = mysqli_fetch_assoc($rs)) {
            $iso            =    (trim($value)=='iso')?$valArray["iso"]:$valArray["id"];
            $name           =    $valArray["name"];
            $id             = $valArray["id"];
            $german_name    = html_entity_decode($valArray["german_name"]);
				 
            if(isset($_SESSION['lang']) && $_SESSION['lang'] == "de-DE")
                $display_name = $german_name;
            else
                $display_name = $name;
                
            if ($valArray["name"] == "")
                continue;
		    $selected = ($iso == $country) ? " selected" : "";
            $option .=         '<option '. $selected .' value="'.$iso.'" data-content="<img src=\'../assets/global/img/flags/'.$iso.'.png\' /> '. $display_name .' ">'. $display_name .'</option>';
        }
        $option .= '</select>';
        return $option;
    }
    
            
            
            
            
	 public static function getCountryDropDownList_ValueById($country="") {
		
        $sql = "SELECT 
   				 id,iso, name, region, german_name
					FROM
						country";
        $rs = self::runQuery($sql);
        $option = "";
        $option .= "<option value=''>Select Country</option>" ;
        while ($valArray = mysqli_fetch_assoc($rs)) {
            $iso            =    $valArray["iso"];
            $name           =    $valArray["name"];
            $id             = $valArray["id"];
            $german_name    = html_entity_decode($valArray["german_name"]);
				 
            if(isset($_SESSION['lang']) && $_SESSION['lang'] == "de-DE")
                $display_name = $german_name;
            else
                $display_name = $name;
                
            if ($valArray["name"] == "")
                continue;
		    $selected = ($iso == $country) ? " selected" : "";
            $option .= "<option ". $selected ." value='".$id."'>". $display_name ."</option>" ;
        }

        return $option;
    }
    public function getIso() {
        return $this->valArray["iso"];
    }
    public static function getCountryIdIsoArr() {
        $sql = "SELECT id,iso, name, region, german_name FROM country";
        $rs = self::runQuery($sql);
        $arr = [];
        while ($valArray = mysqli_fetch_assoc($rs)) {
            $iso            =    $valArray["iso"];
            $id             = $valArray["id"];
            if ($valArray["name"] == "")
                continue;

            $arr[$id] = $iso;
        }
        return $arr;
    }
}
