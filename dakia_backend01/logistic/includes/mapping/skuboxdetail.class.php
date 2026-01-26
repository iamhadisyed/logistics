<?php
class SkuBoxDetail extends DbAccess3
{
    public function __construct($mixedCreator = null)
    {
        $fieldList = array(
            'id' => 'number',
            'sku_order_id' => 'number',
            'bag_number' => 'string',
            'box_quantity' => 'number',
            'number_pieces' => 'number',
            'box_weight' => 'number',
            'box_length' => 'number',
            'box_width' => 'number',
            'box_height' => 'number',
            'user_id' => 'number',
            'date_created' => 'datetime',
            'send_wms' => 'bit'
        );
        parent::__construct("sku_box_detail", 'id', $fieldList, $mixedCreator);
    }

    public static function getSkuBoxDetailListFromSql($sql)
    {
        return DbAccess3::getListFromSql(__CLASS__, $sql);
    }

    public static function getTotalNumberOfSkuBoxDetailFromSql($sql)
    {
        $rs = DbAccess3::runQuery($sql);
        $data = mysqli_fetch_assoc($rs);
        return $data['total'];
    }

    public static function deleteSkuBoxDetailFromSql($sql)
    {
        self::runQuery($sql);
    }

    
    public static function updateSkuBoxDetailFromSql($sql)
    {
        self::runQuery($sql);
    }

}
