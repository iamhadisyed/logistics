<?php

class CarrierZones extends DbAccess3
{
    /**
     * Construct
     *
     * @param id/array
     */

    public function __construct($mixedCreator = null) {
        $fieldList = array(
            'id' => 'number',
            'carrier_id' => 'number',
            'service_id' => 'number',
            'name' => 'string',
            'sort_order' => 'number',
            'status' => 'number',
            'deleted' => 'number',
            'date_added' => 'date',
            'added_by' => 'number',
            'date_updated' => 'date',
            'updated_by' => 'number',
            'carrier_name' => 'undefined',
            'carrier_logo' => 'undefined'
        );

        parent::__construct("carrier_zones", 'id', $fieldList, $mixedCreator);
    }

    ////////////////////////////////////////////////////
    // Access and update methods
    ////////////////////////////////////////////////////

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
    public static function getCarrierZonesListFromSql($sql) {
        return DbAccess3::getListFromSql(__CLASS__, $sql);
    }

    public static function getTotalNumberOfCarrierZonesFromSql($sql) {
        $rs = DbAccess3::runQuery($sql);
        $data = mysqli_fetch_assoc($rs);
        return $data['total'];
    }
    public function deleteById($id) {
        if($id != "" && $id > 0)
            $sql = "UPDATE carrier_zones SET status = '2' WHERE id=''".DbAccess3::escape($id)."'";
            self::runQuery($sql);
    }
    public static function getDataFromSql($sql) {
        return DbAccess3::getListFromSql(__CLASS__, $sql);
    }
    /**
     * delete. Updates row as deleted
     * @return void
     */
    public function delete()
    {
        if ($this->getId() > 0) {
        	$sql = "UPDATE carrier_zones SET status = '2' WHERE id=''".DbAccess3::escape($this->getId())."'";
        	$result = DbAccess3::runQuery($sql);
        }
        return;

    }
}
?>
