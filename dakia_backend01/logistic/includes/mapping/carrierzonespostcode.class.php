<?php
////////////////////////////////////////////////////
//
// Class for dealing with CarrierZonesPostcode
//
/////////////////////////////////////////////

/**
 * CarrierZonesPostcode class
 * @package News Releases
 */
class CarrierZonesPostcode extends DbAccess3
{

    private $filter;

    /**
     * Constructor. Returns the object.
     * @param int $id
     * @return obj
     */
    public function __construct($mixedCreator = null)
    {

        $fieldList = array(
            'id' => 'number',
            'carrier_zone_id' => 'number',
            'postcode' => 'string'
        );

        parent::__construct("carrier_zones_postcode", 'id', $fieldList, $mixedCreator);
    }

    public static function getCarrierZonesPostcodeListFromSql($sql)
    {
        return DbAccess3::getListFromSql(__CLASS__, $sql);
    }


    public static function getTotalNumberOfCarrierZonesPostcodeFromSql($sql)
    {
        $rs = self::runQuery($sql);
        $data = mysqli_fetch_assoc($rs);
        return $data['total'];
    }

    public static function deleteCarrierZonesPostcodeFromSql($sql)
    {
        self::runQuery($sql);
    }

    public static function getZonePostcodes($zoneId) {
        $sql = "SELECT czp.postcode, czp.carrier_zone_id FROM carrier_zones_postcode czp WHERE czp.carrier_zone_id ='".DbAccess3::escape($zoneId)."'";
        return DbAccess3::getListFromSql(__CLASS__, $sql);
    }

    public static function deletePostcodesByZoneId($zoneId) {
        if($zoneId != "" && $zoneId > 0)
            self::runQuery("DELETE FROM carrier_zones_postcode WHERE carrier_zone_id = '".DbAccess3::escape($zoneId)."'");
    }



}