<?php
class ProductsAttributeMapping extends DbAccess3
{
    public function __construct($mixedCreator = null)
    {
        $fieldList = array(
            'id' => 'number',
            'product_id' => 'number',
            'attribute_id' => 'number',
            'value' => 'string'
        );
        parent::__construct("products_attribute_mapping", 'id', $fieldList, $mixedCreator);
    }

    public static function getProductsAttributeMappingListFromSql($sql)
    {
        return DbAccess3::getListFromSql(__CLASS__, $sql);
    }

    public static function getTotalNumberOfProductsAttributeMappingFromSql($sql)
    {
        $rs = DbAccess3::runQuery($sql);
        $data = mysqli_fetch_assoc($rs);
        return $data['total'];
    }

    public static function deleteProductsAttributeMappingFromSql($sql)
    {
        self::runQuery($sql);
    }

    public static function deleteProductsAttributeMappingByProductsId($productId)
    {
        if ($productId != "" && $productId > 0) {
            self::runQuery("DELETE FROM products_attribute_mapping WHERE product_id = '" . DbAccess3::escape($productId) . "'");
        }
    }

    public static function updateProductsAttributeMappingFromSql($sql)
    {
        self::runQuery($sql);
    }

}
