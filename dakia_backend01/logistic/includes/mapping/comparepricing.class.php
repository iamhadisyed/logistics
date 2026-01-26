<?php
class ComparePricing extends DbAccess3
{

    public function __construct($mixedCreator = null)
    {
        $fieldList = array(
            'id' => 'number',
            'zone_id' => 'number',
            'weight_from' => 'decimal',
            'weight_to' => 'decimal',
            'user_id'=>'number',
            'total_value'=>'number',
            'weight_cost'=>'number',
            'piece_cost'=>'number',
            'files'=>'string',
            'tariff_id'=>'number',
            'formula' => 'string'
        );
        parent::__construct("compare_pricings", 'id', $fieldList, $mixedCreator);
    }
    public static function getPricingListFromSql($sql)
    {
        return DbAccess3::getListFromSql(__CLASS__, $sql);
    }

    public static function getTotalNumberOfPricingsFromSql($sql)
    {
        $rs = DbAccess3::runQuery($sql);
        $data = mysqli_fetch_assoc($rs);
        return $data['total'];
    }

    public static function deletePricingsFromSql($sql)
    {
        self::runQuery($sql);
    }

    public static function deletePricingsById($Id)
    {
        if ($Id != "" && $Id > 0) {
            self::runQuery("DELETE FROM categories WHERE id = '" . DbAccess3::escape($Id) . "'");
        }
    }

    public static function updatePricingsFromSql($sql)
    {
        self::runQuery($sql);
    }

}
