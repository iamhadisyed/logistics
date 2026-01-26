<?php
class Attributes extends DbAccess3
{
    public function __construct($mixedCreator = null)
    {
        $fieldList = array(
            'id' => 'number',
            'attrbuite_name' => 'string',
            'attrbuite_group' => 'string',
            'is_active' => 'number',
            'date_added' => 'datetime',
            'added_by' => 'number',
            'date_updated' => 'datetime',
            'updated_by' => 'number',
        );
        parent::__construct("attributes", 'id', $fieldList, $mixedCreator);
    }

    public static function getAttributesListFromSql($sql)
    {
        return DbAccess3::getListFromSql(__CLASS__, $sql);
    }

    public static function getTotalNumberOfAttributesFromSql($sql)
    {
        $rs = DbAccess3::runQuery($sql);
        $data = mysqli_fetch_assoc($rs);
        return $data['total'];
    }

    public static function deleteAttributesFromSql($sql)
    {
        self::runQuery($sql);
    }

    public static function deleteAttributesByAttributesId($attributeId)
    {
        if ($attributeId != "" && $attributeId > 0) {
            self::runQuery("DELETE FROM attributes WHERE id = '" . DbAccess3::escape($attributeId) . "'");
        }
    }

    public static function updateAttributesFromSql($sql)
    {
        self::runQuery($sql);
    }

}
