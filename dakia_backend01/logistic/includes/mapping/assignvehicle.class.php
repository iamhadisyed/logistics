<?php 
////////////////////////////////////////////////////
//
// Class for dealing with Assign Driver
//
////////////////////////////////////////////////////

/**
 * Assign Vehicle class
 * @package News Releases
 */
class AssignVehicle extends DbAccess3 {

    /**
     * Constructor. Returns the object.
     * @param int $id
     * @return obj
     */
    public function __construct($mixedCreator = null) {

        $fieldList = array(
            'id' => 'number',
            'parcel_id' => 'number',
            'vehicle_id' => 'number',
            'driver_id' => 'number',
            'pickup_date' => 'date',
            'date_added' => 'datetime',
            'added_by' => 'number',
            'date_updated' => 'datetime',
            'updated_by' => 'number',
            'is_active' => 'bit',

            'first_name' => 'undefined',
            'last_name' => 'undefined',
            'vehicle_type' => 'undefined',
            'vehicle_make' => 'undefined',
            'registration_number' => 'undefined',
            'hawb' => 'undefined',
            'address_line_1' => 'undefined',
            'address_line_2' => 'undefined',
            'address_line_3' => 'undefined',
            'service_name' => 'undefined',
            'service_id' => 'undefined',
            'country_id' => 'undefined',
            'postcode' => 'undefined',
            'state' => 'undefined',
            'city' => 'undefined',
            'shipment_status' => 'undefined',
            'user_account_id' => 'undefined',
            'country_name' => 'undefined',
            'user_account' => 'undefined',
            'tracking_number' => 'undefined',
            'user_id' => 'undefined',
        );

        parent::__construct("vehicle_parcel_mapping", 'id', $fieldList, $mixedCreator);
    }

    public static function getAssignVehicleListFromSql($sql) {
        return DbAccess3::getListFromSql(__CLASS__, $sql);
    }

    public static function getTotalNumberOfAssignVehicleFromSql($sql) {
        $rs = self::runQuery($sql);
        $data = mysqli_fetch_assoc($rs);
        return $data['total'];
    }

    public static function deleteAssignVehicleFromSql($sql) {
        self::runQuery($sql);
    }

}

?>
