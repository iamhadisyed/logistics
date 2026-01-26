<?php
class TariffsPricingDrafts extends DbAccess3
{
    public function __construct($mixedCreator = null)
    {
        $fieldList = [
            'id' => 'number',
            'user_id' => 'number',
            'tariff_id' => 'number',
            'zone_id' => 'number',
            'weight_from' => 'string',
            'weight_to' => 'string',
            'value' => 'string',
            'margin' => 'string',
            'margin_type' => ['enum' => ['percentage', 'price']],
            'date_added' => 'datetime',
            'added_by' => 'number',
            'date_updated' => 'datetime',
            'updated_by' => 'number'
        ];
        parent::__construct("tariffs_pricing_drafts", 'id', $fieldList, $mixedCreator);
    }

    public static function getTariffsPricingDraftsListFromSql($sql)
    {
        return DbAccess3::getListFromSql(__CLASS__, $sql);
    }

    public static function getTotalNumberOfTariffsPricingDraftsFromSql($sql)
    {
        $rs = DbAccess3::runQuery($sql);
        $data = mysqli_fetch_assoc($rs);
        return $data['total'];
    }

    public static function deleteTariffsPricingDraftsFromSql($sql)
    {
        self::runQuery($sql);
    }

    public static function deleteTariffsPricingDraftsByTariffsPricingDraftsId($tariffsPricingDraftsId)
    {
        if ($tariffsPricingDraftsId != "" && $tariffsPricingDraftsId > 0) {
            self::runQuery("DELETE FROM tariffs_pricing_drafts WHERE id = '" . DbAccess3::escape($tariffsPricingDraftsId) . "'");
        }
    }

    public static function updateTariffsPricingDraftsFromSql($sql)
    {
        self::runQuery($sql);
    }

    public static function deleteTariffsPricingDraftsByTariffId($tariffId,$userId) {
        if (($tariffId != "" && $tariffId > 0) && ($userId != "" && $userId > 0)) {
            self::runQuery("DELETE FROM tariffs_pricing_drafts WHERE tariff_id = '" . DbAccess3::escape($tariffId) . "' && user_id = '" . DbAccess3::escape($userId) . "'");
        }
    }

}

?>