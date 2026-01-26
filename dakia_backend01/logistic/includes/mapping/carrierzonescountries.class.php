<?php

/**
 * carrierServiceDefaultRules Object
 *
 */
class CarrierZonesCountries extends DbAccess3 {

    /**
     * Construct
     *
     * @param id/array
     */
    public function __construct($mixedCreator = null) {
        $fieldList = array(
            'id' => 'number',
            'country_id' => 'number',
            'country_name' => 'undefined',
            'country_iso' => 'undefined',
            'carrier_zone_id' => 'number'
        );
        parent::__construct("carrier_zones_countries", 'id', $fieldList, $mixedCreator);
    }

    /**
     * Get object Id (not provided as magic method) - read only.
     *
     */
    public function getId() {
        return $this->valArray["id"];
    }
    /**
     * Get list of user objects, using sql given
     *
     * @param string $sql
     */
    public static function getZoneCountries($zoneId) {
        $sql = "SELECT czc.country_id, czc.carrier_zone_id, c.name AS country_name, c.iso AS country_iso FROM carrier_zones_countries czc JOIN country c ON c.id = czc.country_id WHERE czc.carrier_zone_id ='".DbAccess3::escape($zoneId)."'";
        return DbAccess3::getListFromSql(__CLASS__, $sql);
    }
    public static function getZonesCountriesIds($zoneIds) {
        $country_ids = [];
        $ids = '';
        $sql = "SELECT czc.country_id, czc.carrier_zone_id, c.name AS country_name, c.iso AS country_iso FROM carrier_zones_countries czc JOIN country c ON c.id = czc.country_id WHERE czc.carrier_zone_id  IN(".$zoneIds.")";
        $country_zones = DbAccess3::getListFromSql(__CLASS__, $sql);
        if (!empty($country_zones)){
            foreach($country_zones as $zone){
                $country_ids[] = $zone->getCountryId();

            }
            $ids = implode(",", $country_ids);
        }
        return $country_ids;
    }
    public static function validateCarrierZonesCountries($zoneCountries,$carrier_id,$service_id='',$zoneId = '',$debug = false) {
        $output = [];
        $where = "";
        if(!empty($service_id))
            $where .= " AND cz.service_id = '". DbAccess3::escape ($service_id)."'";
        if(!empty($zoneId))
            $where .= " AND cz.id != '". DbAccess3::escape ($zoneId)."'";
        if(!empty($zoneCountries)){
            $where .= " AND czc.country_id IN (". implode(",",$zoneCountries).")";
        }

        
            
         $sql = "SELECT
                cz.`name` AS carrier_zone_id,
                czc.`country_id`,
                c.`name` AS country_name
              FROM
                carrier_zones cz 
                JOIN `carrier_zones_countries` czc 
                  ON czc.`carrier_zone_id` = cz.`id` 
                JOIN `country` c 
                  ON c.id = czc.`country_id` 
              WHERE cz.status != 2 AND cz.carrier_id = '".DbAccess3::escape($carrier_id)."'".$where;
         if($debug)
             echo $sql;
        $result = DbAccess3::getListFromSql(__CLASS__, $sql);
        if(count($result) > 0){
            foreach($result as $data){
                $output[$data->getCountryName()] = $data->getCarrierZoneId();
            }
        }
        return $output;
    }
    
    public static function deleteCountriesByZoneId($zoneId) {
        if($zoneId != "" && $zoneId > 0)
             self::runQuery("DELETE FROM carrier_zones_countries WHERE carrier_zone_id = '".DbAccess3::escape($zoneId)."'");
    }
}
