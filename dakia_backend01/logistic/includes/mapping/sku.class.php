<?php
class Sku extends DbAccess3
{
    public function __construct($mixedCreator = null)
    {
        $fieldList = array(
            'id' => 'number',
            'sku' => 'string',
            'customer_id' => 'string',
            'declared_name' => 'string',
            'description' => 'string',
            'length' => 'number',
            'width' => 'number',
            'height' => 'number',
            'gross_weight' => 'number',
            'net_weight' => 'number',
            'price' => 'number',
            'active' => 'bit',
            'notes' => 'string',
            'hscode' => 'string',
            'user_id' => 'number',
            'date_created' => 'datetime',
            'image' => 'string', 
            'send_wms' => 'bit',
            'currency' => 'string',
        );
        parent::__construct("sku", 'id', $fieldList, $mixedCreator);
    }

    public static function getSkuListFromSql($sql)
    {
        return DbAccess3::getListFromSql(__CLASS__, $sql);
    }

    public static function getTotalNumberOfSkuFromSql($sql)
    {
        $rs = DbAccess3::runQuery($sql);
        $data = mysqli_fetch_assoc($rs);
        return $data['total'];
    }

    public static function deleteSkuFromSql($sql)
    {
        self::runQuery($sql);
    }

    
    public static function updateSkuFromSql($sql)
    {
        self::runQuery($sql);
    }
    
    public static function getDataFromSql($sql) {
        return DbAccess3::getListFromSql(__CLASS__, $sql);
    }

}
