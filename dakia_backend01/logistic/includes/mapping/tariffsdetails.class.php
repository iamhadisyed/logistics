<?php

class TariffsDetails extends DbAccess3 {

    /**
     * Construct
     *
     * @param id/array
     */
    public function __construct($mixedCreator = null) {

        $fieldList = array(
            'id' => 'number',
            'tariffs_id' => 'number',
            'from_zone_id' => 'number',
            'to_zone_id' => 'number',
            'agent_id' => 'number',
            'weight_from' => 'decimal',
            'weight_to' => 'decimal',
            'weight_cost' => 'decimal',
            'piece_cost' => 'decimal',
            'formula' => 'string',
            'zone_name' => 'undefined',
            'currency' => 'undefined',
            'service_id' => 'undefined',
            'transit_time' => 'undefined',
            'step' => 'undefined',
            'zone_id' => 'undefined',
            'name'=>'undefined'

        );
        //
        parent::__construct("tariffs_details", 'id', $fieldList, $mixedCreator);
    }

    /**
     * Get list of Service Log objects, using sql given
     *
     * @param string $sql
     */
    public static function getTariffsDetailsListFromSql($sql) {

        return DbAccess3::getListFromSql(__CLASS__, $sql);
    }
    public static function deleteTarifsDetailByTarifId($tarifId,$debug=false) {
        $sql = "DELETE FROM tariffs_details WHERE tariffs_id = '".DbAccess3::escape($tarifId)."'";
        if($debug)
            echo $sql ;
        if($tarifId != "" && $tarifId > 0)
             self::runQuery($sql );
    }
    public static function deleteById($id) {
        if($id != "" && $id > 0)
            $sql = "DELETE FROM tariffs_details WHERE id = '".DbAccess3::escape($id)."'";
        self::runQuery($sql);
    }

}

// class