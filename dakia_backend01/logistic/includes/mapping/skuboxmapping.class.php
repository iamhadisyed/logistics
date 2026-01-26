<?php
class SkuBoxMapping extends DbAccess3
{
    public function __construct($mixedCreator = null)
    {
        $fieldList = array(
            'id' => 'number',
            'sku_box_detail_id' => 'string',
            'sku_id' => 'number',
            'sku_quantity' => 'number',
            
        );
        parent::__construct("sku_box_mapping", 'id', $fieldList, $mixedCreator);
    }

    public static function getSkuBoxMappingListFromSql($sql)
    {
        return DbAccess3::getListFromSql(__CLASS__, $sql);
    }

    public static function getTotalNumberOfSkuBoxMappingFromSql($sql)
    {
        $rs = DbAccess3::runQuery($sql);
        $data = mysqli_fetch_assoc($rs);
        return $data['total'];
    }

    public static function deleteSkuBoxMappingFromSql($sql)
    {
        self::runQuery($sql);
    }

    
    public static function updateSkuBoxMappingFromSql($sql)
    {
        self::runQuery($sql);
    }

}
