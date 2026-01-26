<?php
class InvoiceTemplates extends DbAccess3
{
    public function __construct($mixedCreator = null)
    {
        $fieldList = [
            'id' => 'number',
            'name' => 'string',
            'image' => 'string',
            'invoice_function' => 'string',
            'summary_invoice_function' => 'string'
        ];
        parent::__construct("invoice_templates", 'id', $fieldList, $mixedCreator);
    }

    public static function getInvoiceTemplateListFromSql($sql)
    {
        return DbAccess3::getListFromSql(__CLASS__, $sql);
    }

    public static function getTotalNumberOfInvoiceTemplateFromSql($sql)
    {
        $rs = DbAccess3::runQuery($sql);
        $data = mysqli_fetch_assoc($rs);
        return $data['total'];
    }

    public static function deleteInvoiceTemplateFromSql($sql)
    {
        self::runQuery($sql);
    }

    public static function deleteInvoiceTemplateByInvoiceTemplateId($invoiceTemplateId)
    {
        if ($invoiceTemplateId != "" && $invoiceTemplateId > 0) {
            self::runQuery("DELETE FROM invoice_templates WHERE id = '" . DbAccess3::escape($invoiceTemplateId) . "'");
        }
    }

}

?>