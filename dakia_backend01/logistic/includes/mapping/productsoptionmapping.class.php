<?php
class ProductsOptionMapping extends DbAccess3
{
    public function __construct($mixedCreator = null)
    {
        $fieldList = array(
            'id' => 'number',
            'product_id' => 'number',
            'option_id' => 'number',
            'value' => 'string'
        );
        parent::__construct("products_option_mapping", 'id', $fieldList, $mixedCreator);
    }

    public static function getProductsOptionMappingListFromSql($sql)
    {
        return DbAccess3::getListFromSql(__CLASS__, $sql);
    }

    public static function getTotalNumberOfProductsOptionMappingFromSql($sql)
    {
        $rs = DbAccess3::runQuery($sql);
        $data = mysqli_fetch_assoc($rs);
        return $data['total'];
    }

    public static function deleteProductsOptionMappingFromSql($sql)
    {
        self::runQuery($sql);
    }

    public static function deleteProductsOptionMappingByProductsId($productId)
    {
        if ($productId != "" && $productId > 0) {
            self::runQuery("DELETE FROM products_option_mapping WHERE product_id = '" . DbAccess3::escape($productId) . "'");
        }
    }

    public static function updateProductsOptionMappingFromSql($sql)
    {
        self::runQuery($sql);
    }

}
