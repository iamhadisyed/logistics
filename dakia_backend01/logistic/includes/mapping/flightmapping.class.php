<?php

/**
 * RemoteareasGroups Object
 *
 */
class FlightMapping extends DbAccess3 {

    /**
     * Construct
     *
     * @param id/array
     */
    public function __construct($mixedCreator = null) {
        $fieldList = array(
            'id' => 'number',
            'flight_info_id' => 'number',
            //'flight_number' => 'string',
            'mawb' => 'string',
            'is_delete' => 'tinyint',
            'mawb_id' => 'number',
            'is_closed' => 'undefined',
            'flight_number' => 'undefined'

        );
        parent::__construct("flight_mapping", 'id', $fieldList, $mixedCreator);
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
    public static function getFlightMappingListFromSql($sql) {
        return DbAccess3::getListFromSql(__CLASS__, $sql);
    }

    public static function getTotalNumberOfFlightMappingFromSql($sql) {
        $rs = DbAccess3::runQuery($sql);
        $data = mysqli_fetch_assoc($rs);
        return $data['total'];
    }

    public function getFlightMawbList($flightId) {
        $sql = "SELECT mawb_id FROM flight_mapping WHERE flight_info_id = $flightId";
        return DbAccess3::getListFromSql(__CLASS__, $sql);
    }
}
