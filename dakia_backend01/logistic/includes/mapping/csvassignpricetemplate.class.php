<?php
// get settings
class CsvAssignPriceTemplate extends DbAccess3
{

    public function __construct($mixedCreator = null)
    {
        $fieldList = array(
            'id' => 'number',
            'user_id' => 'number',
            'user_account_id' => 'number',
            'template' => 'string',
            'template_name' => 'string',
            'added_by' => 'number',
            'added_date' => 'datetime',
            'update_by' => 'number',
            'update_date' => 'datetime'
        );
        parent::__construct("csv_assign_price_template", 'id', $fieldList, $mixedCreator);
    }

    /**
     * Get list of user objects, using sql given
     *
     * @param string $sql
     */
    public static function getCsvAssignPirceTemplateListFromSql($sql)
    {
        return DbAccess3::getListFromSql(__CLASS__, $sql);
    }

    public static function getTotalNumberOfCsvAssignPirceTemplateFromSql($sql)
    {
        $rs = DbAccess3::runQuery($sql);
        $data = mysqli_fetch_assoc($rs);
        return $data['total'];
    }

    public static function deleteCsvAssignPirceTemplateFromSql($sql)
    {
        self::runQuery($sql);
    }

    public static function deleteCsvAssignPirceTemplateById($CsvAssignPirceTemplateId)
    {
        if ($CsvAssignPirceTemplateId != "" && $CsvAssignPirceTemplateId > 0) {
            self::runQuery("DELETE FROM csv_assign_price_template WHERE id = '" . DbAccess3::escape($CsvAssignPirceTemplateId) . "'");
        }
    }

}

