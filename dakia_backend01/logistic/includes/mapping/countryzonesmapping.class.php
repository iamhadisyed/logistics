<?php
class CountryZonesMapping extends DbAccess3
{
    public function __construct($mixedCreator = null)
    {
        $fieldList = [
            'id' => 'number',
            'country_id' => 'number',
            'zone' => ['enum' => ['asia', 'europe','africa','oceania','north_america','south_america','antarctica']],
            'created_at' => 'datetime'
        ];
        parent::__construct("country_zones_mapping", 'id', $fieldList, $mixedCreator);
    }

    public static function getCountryZonesMappingListFromSql($sql)
    {
        return DbAccess3::getListFromSql(__CLASS__, $sql);
    }

    public static function getTotalNumberOfCountryZonesMappingFromSql($sql)
    {
        $rs = DbAccess3::runQuery($sql);
        $data = mysqli_fetch_assoc($rs);
        return $data['total'];
    }

    public static function deleteCountryZonesMappingFromSql($sql)
    {
        self::runQuery($sql);
    }

    public static function deleteCountryZonesMappingByCountryZonesMappingId($countryZonesMappingId)
    {
        if ($countryZonesMappingId != "" && $countryZonesMappingId > 0) {
            self::runQuery("DELETE FROM country_zones_mapping WHERE id = '" . DbAccess3::escape($countryZonesMappingId) . "'");
        }
    }

    public static function updateCountryZonesMappingFromSql($sql)
    {
        self::runQuery($sql);
    }

}

?>