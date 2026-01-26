<?php
class SkuOrder extends DbAccess3
{
    public function __construct($mixedCreator = null)
    {
        $fieldList = array(
            'id' => 'number',
            'shipment_reference' => 'string',
            'user_id' => 'number',
            'received_quantity' => 'number',
            'date_created' => 'datetime',
            'consignment_id' => 'number',
            'warehouse_id' => 'nummber',
            'total_sku_quantity' => 'number',
            'warehouse' => 'undefined',
            'shipped_quantity' => 'undefined',
            'sku' => 'undefined',
            'sku_order_id' => 'undefined',
            'sku_id' => 'undefined'
        );
        parent::__construct("sku_order", 'id', $fieldList, $mixedCreator);
    }

    public static function getSkuOrderListFromSql($sql)
    {
        return DbAccess3::getListFromSql(__CLASS__, $sql);
    }

    public static function getTotalNumberOfSkuOrderFromSql($sql)
    {
        $rs = DbAccess3::runQuery($sql);
        $data = mysqli_fetch_assoc($rs);
        return $data['total'];
    }

    public static function deleteSkuOrderFromSql($sql)
    {
        self::runQuery($sql);
    }

    
    public static function updateSkuOrdersFromSql($sql)
    {
        self::runQuery($sql);
    }

}
