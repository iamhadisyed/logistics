<?php
class Categories extends DbAccess3
{
    public function __construct($mixedCreator = null)
    {
        $fieldList = array(
            'id' => 'number',
            'name' => 'string',
            'node_id' => 'number','node_id' => 'number',
            'parent_id' => 'number',
            'market_place_id' => 'number',
            'is_active' => 'number',
            'date_added' => 'datetime',
            'added_by' => 'number',
            'date_updated' => 'datetime',
            'updated_by' => 'number',
        );
        parent::__construct("categories", 'id', $fieldList, $mixedCreator);
    }

    public static function getCategoriesListFromSql($sql)
    {
        return DbAccess3::getListFromSql(__CLASS__, $sql);
    }

    public static function getTotalNumberOfCategoriesFromSql($sql)
    {
        $rs = DbAccess3::runQuery($sql);
        $data = mysqli_fetch_assoc($rs);
        return $data['total'];
    }

    public static function deleteCategoriesFromSql($sql)
    {
        self::runQuery($sql);
    }

    public static function deleteCategoriesByCategoryId($categoryId)
    {
        if ($categoryId != "" && $categoryId > 0) {
            self::runQuery("DELETE FROM categories WHERE id = '" . DbAccess3::escape($categoryId) . "'");
        }
    }

    public static function updateCategoryFromSql($sql)
    {
        self::runQuery($sql);
    }

}
