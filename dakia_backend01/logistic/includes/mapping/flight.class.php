<?php

/**
 * Flight Object
 *
 */
class Flight extends DbAccess3 {

    /**
     * Construct
     *
     * @param id/array
     */
    public function __construct($mixedCreator = null) {
        $fieldList = array(
            'id' => 'number',
            'flight_number' => 'string',
            'connecting_flight_number' => 'string'
        );
        parent::__construct("flight", 'id', $fieldList, $mixedCreator);
    }

    /**
     * Get object Id (not provided as magic method) - read only.
     *
     */
    public function getId() {
        return $this->valArray["id"];
    }

    /**
     * Get list of flight objects, using sql given
     *
     * @param string $sql
     */
    public static function getFlightListFromSql($sql) {
        return DbAccess3::getListFromSql(__CLASS__, $sql);
    }

    public static function getTotalNumberOfFlightFromSql($sql) {
        $rs = DbAccess3::runQuery($sql);
        $data = mysqli_fetch_assoc($rs);
        return $data['total'];
    }
    public function deleteById($id) {
        if($id != "" && $id > 0)
            self::runQuery("DELETE FROM flight fl WHERE id = '".DbAccess3::escape($id)."'");
    }
     public function deleteBySql($sql) {
            self::runQuery($sql);
    }
}
