<?php
class ProductsCategoryMapping extends DbAccess3
{
    public function __construct($mixedCreator = null)
    {
        $fieldList = array(
            'id' => 'number',
            'product_id' => 'number',
            'category_id' => 'number'
        );
        parent::__construct("products_category_mapping", 'id', $fieldList, $mixedCreator);
    }

    public static function getProductsCategoryMappingListFromSql($sql)
    {
        return DbAccess3::getListFromSql(__CLASS__, $sql);
    }

    public static function getTotalNumberOfProductsCategoryMappingFromSql($sql)
    {
        $rs = DbAccess3::runQuery($sql);
        $data = mysqli_fetch_assoc($rs);
        return $data['total'];
    }

    public static function deleteProductsCategoryMappingFromSql($sql)
    {
        self::runQuery($sql);
    }

    public static function deleteProductsCategoryMappingByProductsId($productId)
    {
        if ($productId != "" && $productId > 0) {
            self::runQuery("DELETE FROM  Table: products_category_mapping WHERE product_id = '" . DbAccess3::escape($productId) . "'");
        }
    }

    public static function updateProductsCategoryMappingFromSql($sql)
    {
        self::runQuery($sql);
    }

}
