<?php

////////////////////////////////////////////////////
//
// Class for dealing with ReportCustomizeSettings
//
////////////////////////////////////////////////////

/**
 * ReportCustomizeSettings class
 * @package News Releases
 */
class ReportCustomizeSettings extends DbAccess3 {

    /**
     * Constructor. Returns the object.
     * @param int $id
     * @return obj
     */
    public function __construct($mixedCreator = null) {
        $fieldList = array(
            'id' => 'number',
            'account_id' => 'number',
            'report_key' => 'string',
            'report_title' => 'string',
            'fields_data' => 'string',
            'date_added' => 'datetime',
            'added_by' => 'number',
            'date_updated' => 'datetime',
            'updated_by' => 'number'
        );

        parent::__construct("report_customize_settings", 'id', $fieldList, $mixedCreator);
    }

    public static function getReportCustomizeSettingsListFromSql($sql) {
        return DbAccess3::getListFromSql(__CLASS__, $sql);
    }

    public static function getTotalNumberOfReportCustomizeSettingsFromSql($sql) {
        $rs = self::runQuery($sql);
        $data = mysqli_fetch_assoc($rs);
        return $data['total'];
    }

    public static function deleteReportCustomizeSettingsFromSql($sql) {
        self::runQuery($sql);
    }

}

?>
