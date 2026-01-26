<?php

/**
 * Sea Object
 *
 */
class ItemDetail extends DbAccess3 {

    /**
     * Construct
     *
     * @param id/array
     */
    public function __construct($mixedCreator = null) {
        $fieldList = array(
            'id' => 'number',
            'consignment_id' => 'number',
            'session_id' => 'string',
            'parcel_count' => 'number',
            'item_detail' => 'string',
            'user_id' => 'number',
            'date_created' => 'undefined',
        );
        parent::__construct("item_details", 'id', $fieldList, $mixedCreator);
    }

    /**
     * Get object Id (not provided as magic method) - read only.
     *
     */
    public function getId() {
        return $this->valArray["id"];
    }

    /**
     * Get list of itemDetail objects, using sql given
     *
     * @param string $sql
     */
    public static function getItemDetailListFromSql($sql) {
        return DbAccess3::getListFromSql(__CLASS__, $sql);
    }

    public static function getTotalNumberOfItemDetailFromSql($sql) {
        $rs = DbAccess3::runQuery($sql);
        $data = mysqli_fetch_assoc($rs);
        return $data['total'];
    }
    public static function deleteItemDetailFromSql($sql)
    {
        self::runQuery($sql);
    }

    
    public static function updateItemDetailFromSql($sql)
    {
        self::runQuery($sql);
    }
}
