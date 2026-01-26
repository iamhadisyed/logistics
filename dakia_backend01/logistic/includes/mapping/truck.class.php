<?php

/**
 * Truck Object
 *
 */
class Truck extends DbAccess3 {

    /**
     * Construct
     *
     * @param id/array
     */
    public function __construct($mixedCreator = null) {
        $fieldList = array(
            'id' => 'number',
            'truck_number' => 'string',
        );
        parent::__construct("truck", 'id', $fieldList, $mixedCreator);
    }

    /**
     * Get object Id (not provided as magic method) - read only.
     *
     */
    public function getId() {
        return $this->valArray["id"];
    }

    /**
     * Get list of truck objects, using sql given
     *
     * @param string $sql
     */
    public static function getTruckListFromSql($sql) {
        return DbAccess3::getListFromSql(__CLASS__, $sql);
    }

    public static function getTotalNumberOfTruckFromSql($sql) {
        $rs = DbAccess3::runQuery($sql);
        $data = mysqli_fetch_assoc($rs);
        return $data['total'];
    }
    public function deleteById($id) {
        if($id != "" && $id > 0)
            self::runQuery("DELETE FROM truck WHERE id = '".DbAccess3::escape($id)."'");
    }
     public function deleteBySql($sql) {
            self::runQuery($sql);
    }
}
