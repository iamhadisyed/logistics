<?php
class Linehaul extends DbAccess3
{
    public function __construct($mixedCreator = null)
    {
        $fieldList = array(
            'id' => 'number',
            'account_id' => 'number',
            'agent_id' => 'number',
            'country_id' => 'number',
            'delivery_type' => ['enum' => ['all', 'economy','priority']],
            'price' => 'string',
            'date_created' => 'datetime'
        );
        parent::__construct("linehaul", 'id', $fieldList, $mixedCreator);
    }

    public static function getLinehaulListFromSql($sql)
    {
        return DbAccess3::getListFromSql(__CLASS__, $sql);
    }

    public static function getTotalNumberOfLinehaulFromSql($sql)
    {
        $rs = DbAccess3::runQuery($sql);
        $data = mysqli_fetch_assoc($rs);
        return $data['total'];
    }

    public static function deleteLinehaulFromSql($sql)
    {
        self::runQuery($sql);
    }

    public static function deleteLinehaulByLinehaulId($reconciliationDataId)
    {
        if ($reconciliationDataId != "" && $reconciliationDataId > 0) {
            self::runQuery("DELETE FROM linehaul WHERE id = '" . DbAccess3::escape($reconciliationDataId) . "'");
        }
    }

    public static function updateLinehaulFromSql($sql)
    {
        self::runQuery($sql);
    }

}
