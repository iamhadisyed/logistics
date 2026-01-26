<?php

/**
 * DocumentType Object
 *
 */
class DocumentType extends DbAccess3 {

    /**
     * Construct
     *
     * @param id/array
     */
    public function __construct($mixedCreator = null) {
        $fieldList = array(
            'id' => 'number',
            'document_name' => 'string',
            'discriptiion' => 'string',
            'document_type' => 'string',
            'is_active' => 'number',
            'added_by' => 'number',
            'added_date' => 'datetime',
            'updated_by' => 'number',
            'updated_date' => 'datetime',
            'is_delete' => 'number',
        );
        parent::__construct("document_type", 'id', $fieldList, $mixedCreator);
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
    public static function getDocumentTypeListFromSql($sql) {
        return DbAccess3::getListFromSql(__CLASS__, $sql);
    }

    public static function getTotalNumberOfDocumentTypeFromSql($sql) {
        $rs = DbAccess3::runQuery($sql);
        $data = mysqli_fetch_assoc($rs);
        return $data['total'];
    }
    public function deleteById($id) {
        if($id != "" && $id > 0)
            self::runQuery("DELETE FROM document_type WHERE id = '".DbAccess3::escape($id)."'");
    }
}
