<?php
class ProductsImageMapping extends DbAccess3
{
    public function __construct($mixedCreator = null)
    {
        $fieldList = array(
            'id' => 'number',
            'product_id' => 'number',
            'image' => 'string'
        );
        parent::__construct("products_image_mapping", 'id', $fieldList, $mixedCreator);
    }

    public static function getProductsImageMappingListFromSql($sql)
    {
        return DbAccess3::getListFromSql(__CLASS__, $sql);
    }

    public static function getTotalNumberOfProductsImageMappingFromSql($sql)
    {
        $rs = DbAccess3::runQuery($sql);
        $data = mysqli_fetch_assoc($rs);
        return $data['total'];
    }

    public static function deleteProductsImageMappingFromSql($sql)
    {
        self::runQuery($sql);
    }

    public static function deleteProductsImageMappingByProductsId($productId)
    {
        if ($productId != "" && $productId > 0) {
            self::runQuery("DELETE FROM products_image_mapping WHERE product_id = '" . DbAccess3::escape($productId) . "'");
        }
    }

    public static function updateProductsImageMappingFromSql($sql)
    {
        self::runQuery($sql);
    }

}
