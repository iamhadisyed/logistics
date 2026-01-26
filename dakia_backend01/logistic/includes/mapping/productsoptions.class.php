<?php
class ProductsOptions extends DbAccess3
{
    public function __construct($mixedCreator = null)
    {
        $fieldList = array(
            'id' => 'number',
            'option_name' => 'string',
            'is_active' => 'number',
            'date_added' => 'datetime',
            'added_by' => 'number',
            'date_updated' => 'datetime',
            'updated_by' => 'number'
        );
        parent::__construct("products_options", 'id', $fieldList, $mixedCreator);
    }

    public static function getProductsOptionsListFromSql($sql)
    {
        return DbAccess3::getListFromSql(__CLASS__, $sql);
    }

    public static function getTotalNumberOfProductsOptionsFromSql($sql)
    {
        $rs = DbAccess3::runQuery($sql);
        $data = mysqli_fetch_assoc($rs);
        return $data['total'];
    }

    public static function deleteProductsOptionsFromSql($sql)
    {
        self::runQuery($sql);
    }

    public static function deleteProductsOptionsByProductsOptionsId($productOptionId)
    {
        if ($productOptionId != "" && $productOptionId > 0) {
            self::runQuery("DELETE FROM  Table: products_options WHERE id = '" . DbAccess3::escape($productOptionId) . "'");
        }
    }

    public static function updateProductsOptionsFromSql($sql)
    {
        self::runQuery($sql);
    }

}
