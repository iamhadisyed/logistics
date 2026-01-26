<?php
class Products extends DbAccess3
{
    public function __construct($mixedCreator = null)
    {
        $fieldList = array(
            'id' => 'number',
            'product_name' => 'string',
            'brand_id' => 'number',
            'manufacturer' => 'string',
            'asin' => 'string',
            'upc' => 'string',
            'sku' => 'string',
            'isbn' => 'string',
            'ean' => 'string',
            'hs_code' => 'string',
            'gross_weight' => 'number',
            'net_weight' => 'number',
            'quantity' => 'number',
            'length' => 'number',
            'width' => 'number',
            'height' => 'number',
            'price' => 'number',
            'note' => 'string',
            'description' => 'string',
            'date_added' => 'datetime',
            'added_by' => 'number',
            'date_updated' => 'datetime',
            'updated_by' => 'number',
            'image' => 'undefined',
            'send_wms' => 'bit'
        );
        parent::__construct("products", 'id', $fieldList, $mixedCreator);
    }

    public static function getProductsListFromSql($sql)
    {
        return DbAccess3::getListFromSql(__CLASS__, $sql);
    }

    public static function getTotalNumberOfProductsFromSql($sql)
    {
        $rs = DbAccess3::runQuery($sql);
        $data = mysqli_fetch_assoc($rs);
        return $data['total'];
    }

    public static function deleteProductsFromSql($sql)
    {
        self::runQuery($sql);
    }

    public static function deleteProductsByProductsId($productId)
    {
        if ($productId != "" && $productId > 0) {
            self::runQuery("DELETE FROM products WHERE id = '" . DbAccess3::escape($productId) . "'");
        }
    }

    public static function updateProductsFromSql($sql)
    {
        self::runQuery($sql);
    }

}
