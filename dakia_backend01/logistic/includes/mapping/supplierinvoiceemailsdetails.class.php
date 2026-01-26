<?php

/**
 * StatusReason Object
 *
 */
class SupplierInvoiceEmailsDetails extends DbAccess3 {

    /**
     * Construct
     *
     * @param id/array
     */
    public function __construct($mixedCreator = null) {
        $fieldList = array(
            'id' => 'number',
            'supplier_invoice_id' => 'number', 
            'supplier_bag_id' => 'string',
            'email_to' => 'string',
            'email_cc' => 'string',
            'email_subject' => 'string', 
            'email_body' => 'string',
            'added_by' => 'string',
            'added_date' => 'datetime',
            'supplier_invoice_entity_id' => 'string'
        );
        parent::__construct("supplier_invoice_emails_details", 'id', $fieldList, $mixedCreator);
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
    public static function getListFromSql($sql) {
        return DbAccess3::getListFromSql(__CLASS__, $sql);
    }

    public static function getTotalNumberFromSql($sql) {
        $rs = DbAccess3::runQuery($sql);
        $data = mysqli_fetch_assoc($rs);
        return $data['total'];
    }
    public function deleteById($id) {
        if($id != "" && $id > 0)
            self::runQuery("DELETE FROM supplier_invoice_emails_details WHERE id = '".DbAccess3::escape($id)."'");
    }
    
    public static function deleteDataFromSql($sql)
    {
        self::runQuery($sql);
    }

    public static function deleteDataById($Id)
    {
        if ($Id != "" && $Id > 0) {
            self::runQuery("DELETE FROM supplier_invoice_emails_details WHERE id = '" . DbAccess3::escape($Id) . "'");
        }
    }

    public static function updateDataFromSql($sql)
    {
        self::runQuery($sql);
    }
}
