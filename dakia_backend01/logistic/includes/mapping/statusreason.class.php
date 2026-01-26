<?php

/**
 * StatusReason Object
 *
 */
class StatusReason extends DbAccess3 {

    /**
     * Construct
     *
     * @param id/array
     */
    public function __construct($mixedCreator = null) {
        $fieldList = array(
            'id' => 'number',
            'parcel_id' => 'number',
            'status_id' => 'number',
            'reason' => 'string',
            'status_label' => 'string',
            'is_hold' => ['enum' => ['0','1'],'default' => '0'],
            'added_by' => 'number',
            'date_added' => 'datetime'
        );
        parent::__construct("status_reason", 'id', $fieldList, $mixedCreator);
    }

    /**
     * Get object Id (not provided as magic method) - read only.
     *
     */
    public function getId() {
        return $this->valArray["id"];
    }

    /**
     * Get list of status_reason objects, using sql given
     *
     * @param string $sql
     */
    public static function getStatusReasonListFromSql($sql) {
        return DbAccess3::getListFromSql(__CLASS__, $sql);
    }

    public static function getTotalNumberOfStatusReasonFromSql($sql) {
        $rs = DbAccess3::runQuery($sql);
        $data = mysqli_fetch_assoc($rs);
        return $data['total'];
    }
    public function deleteById($id) {
        if($id != "" && $id > 0)
            self::runQuery("DELETE FROM status_reason WHERE id = '".DbAccess3::escape($id)."'");
    }
}
