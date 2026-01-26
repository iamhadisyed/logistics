<?php
class SkuOrderMapping extends DbAccess3
{
    public function __construct($mixedCreator = null)
    {
        $fieldList = array(
            'id' => 'number',
            'sku_id' => 'number',
            'sku_order_id' => 'number',
            'shipped_quantity' => 'number',
            'consignment_id' => 'undefined',
            'sku' => 'undefined',
            'shipment_reference' => 'undefined',
            'received_quantity' => 'undefined',
            'warehouse' => 'undefined'
        );
        parent::__construct("sku_order_mapping", 'id', $fieldList, $mixedCreator);
    }

    public static function getSkuOrderMappingListFromSql($sql)
    {
        return DbAccess3::getListFromSql(__CLASS__, $sql);
    }

    public static function getTotalNumberOfSkuOrderMappingFromSql($sql)
    {
        $rs = DbAccess3::runQuery($sql);
        $data = mysqli_fetch_assoc($rs);
        return $data['total'];
    }
/*
    public static function deleteSkuOrderFromSql($sql)
    {
        self::runQuery($sql);
    }

    
    public static function updateSkuOrdersFromSql($sql)
    {
        self::runQuery($sql);
    }*/

}
