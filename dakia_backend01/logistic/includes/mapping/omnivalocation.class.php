<?php
////////////////////////////////////////////////////
//
// Class for dealing Omniva Locations of Parcel Machine
//
////////////////////////////////////////////////////

/**
 * Omniva Location class
 * @package News Releases
 */

class OmnivaLocation extends DbAccess3
{
    /**
     * Constructor. Returns the object.
     * @param int $id
     * @return obj
     */
    public function __construct($mixedCreator = null)
    {
        $fieldList = array(
            'id'              => 'number',
            'zip'             => 'string',
            'postoffice_name' => 'string',
            'type'            => 'string',
            'country'         => 'string',
            'county'          => 'string',
            'town'            => 'string',
            'village'         => 'string',
            'small_place'     => 'string',
            'street'          => 'string',
            'area'            => 'string',
            'house_no'        => 'string',
            'apartment_no'        => 'string',
            'x_coordinate'    => 'string',
            'y_coordinate'    => 'string',
            'service_hours'    => 'string',
            'temp_service_hours'    => 'string',
            'temp_service_hours_until'    => 'string',
            'modified'        => 'datetime'
            
        );
        parent::__construct("omniva_location", 'id', $fieldList, $mixedCreator);
    }

    public static function getOmnivaLocationListFromSql($sql)
    {
        return DbAccess3::getListFromSql(__CLASS__, $sql);
    }

    public static function getTotalNumberOfOmnivaLocationFromSql($sql)
    {
        $rs = DbAccess3::runQuery($sql);
        $data = mysqli_fetch_assoc($rs);
        return $data['total'];
    }

    public static function deleteOmnivaLocationFromSql($sql)
    {
        self::runQuery($sql);
    }

    public static function getDataFromSql($sql) {
        return DbAccess3::getListFromSql(__CLASS__, $sql);
    }

}
