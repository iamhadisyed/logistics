<?php

/**
 * MawbParcelMapping Object
 *
 */
class MawbBagDetail extends DbAccess3 {

    /**
     * Construct
     *
     * @param id/array
     */
    public function __construct($mixedCreator = null) {
        $fieldList = array(
            'id' => 'number',
            'mawb_id' => 'number',
            'quantity' => 'number',
            'length' => 'number',
            'width' => 'number',
            'height' => 'number',
            'description' => 'string',
            'date_updated' => 'datetime'
            
        );
        parent::__construct("mawb_bag_detail", 'id', $fieldList, $mixedCreator);
    }

    /**
     * Get object Id (not provided as magic method) - read only.
     *
     */
    public function getId() {
        return $this->valArray["id"];
    }

    /**
     * Get list of mawb_parcel_mapping objects, using sql given
     *
     * @param string $sql
     */
    public static function getMawbBagDetailListFromSql($sql) {
        return DbAccess3::getListFromSql(__CLASS__, $sql);
    }

    public static function getTotalNumberOfMawbBagDetailFromSql($sql) {
        $rs = DbAccess3::runQuery($sql);
        $data = mysqli_fetch_assoc($rs);
        return $data['total'];
    }
    public function deleteById($id) {
        if($id != "" && $id > 0)
            self::runQuery("DELETE FROM mawb_bag_detail WHERE id = '".DbAccess3::escape($id)."'");
    }
    public function deleteBySql($sql) {
            self::runQuery($sql);
    }
    
}
