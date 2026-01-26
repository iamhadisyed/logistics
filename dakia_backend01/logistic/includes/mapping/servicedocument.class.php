<?php

/**
 * serviceDocument Object
 *
 */
class serviceDocument extends DbAccess3 {

    /**
     * Construct
     *
     * @param id/array
     */
    public function __construct($mixedCreator = null) {
        $fieldList = array(
            'id' => 'number',
            'service_id' => 'number',
            'document_id' => 'number',
            'agent_id' => 'number',
            'document_name' => 'string',
            'added_by' => 'number',
            'added_date' => 'datetime',
            'updated_by' => 'number',
            'updated_date' => 'datetime'
        );
        parent::__construct("service_document", 'id', $fieldList, $mixedCreator);
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
    public static function getserviceDocumentListFromSql($sql) {
        return DbAccess3::getListFromSql(__CLASS__, $sql);
    }

    public static function getTotalNumberOfserviceDocumentFromSql($sql) {
        $rs = DbAccess3::runQuery($sql);
        $data = mysqli_fetch_assoc($rs);
        return $data['total'];
    }
    public function deleteById($id) {
        if($id != "" && $id > 0)
            self::runQuery("DELETE FROM service_document WHERE id = '".DbAccess3::escape($id)."'");
    }
}
