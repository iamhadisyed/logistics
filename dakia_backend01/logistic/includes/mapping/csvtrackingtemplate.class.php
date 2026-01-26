<?php

/**
 * CsvTrackingTemplate Object
 *
 */
class CsvTrackingTemplate extends DbAccess3 {

    /**
     * Construct
     *
     * @param id/array
     */
    public function __construct($mixedCreator = null) {
        $fieldList = array(
            'id' => 'number',
            'user_id' => 'number',
            'user_account_id' => 'number',
            'template' => 'string',
            'template_name' => 'string',
            'service_id' => 'number',
            'added_by' => 'number',
            'added_date' => 'datetime',
            'update_by' => 'number',
            'update_date' => 'datetime'
        );
        parent::__construct("csv_tracking_template", 'id', $fieldList, $mixedCreator);
    }

    /**
     * Get object Id (not provided as magic method) - read only.
     *
     */
    public function getId() {
        return $this->valArray["id"];
    }

    /**
     * Get list of user objects, using sql given
     *
     * @param string $sql
     */
    public static function getCsvTrackingTemplateListFromSql($sql) {
        return DbAccess3::getListFromSql(__CLASS__, $sql);
    }

    public static function getTotalNumberOfCsvTrackingTemplateFromSql($sql) {
        $rs = DbAccess3::runQuery($sql);
        $data = mysqli_fetch_assoc($rs);
        return $data['total'];
    }
    public function deleteById($id) {
        if($id != "" && $id > 0)
            self::runQuery("DELETE FROM csv_tracking_template WHERE id = '".DbAccess3::escape($id)."'");
    }
}
