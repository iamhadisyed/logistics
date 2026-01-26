<?php
class Brands extends DbAccess3
{
    public function __construct($mixedCreator = null)
    {
        $fieldList = array(
            'id' => 'number',
            'name' => 'string',
            'is_active' => 'number',
            'date_added' => 'datetime',
            'added_by' => 'number',
            'date_updated' => 'datetime',
            'updated_by' => 'number',
        );
        parent::__construct("brands", 'id', $fieldList, $mixedCreator);
    }

    public static function getBrandsListFromSql($sql)
    {
        return DbAccess3::getListFromSql(__CLASS__, $sql);
    }

    public static function getTotalNumberOfBrandsFromSql($sql)
    {
        $rs = DbAccess3::runQuery($sql);
        $data = mysqli_fetch_assoc($rs);
        return $data['total'];
    }

    public static function deleteBrandsFromSql($sql)
    {
        self::runQuery($sql);
    }

    public static function deleteBrandsByBrandId($brandId)
    {
        if ($brandId != "" && $brandId > 0) {
            self::runQuery("DELETE FROM brands WHERE id = '" . DbAccess3::escape($brandId) . "'");
        }
    }

    public static function updateBrandFromSql($sql)
    {
        self::runQuery($sql);
    }

}
