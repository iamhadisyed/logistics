<?php
class ReconciliationData extends DbAccess3
{
    public function __construct($mixedCreator = null)
    {
        $fieldList = [
            'id' => 'number',
            'supplier_invoice_id' => 'string',
            'batch_number' => 'string',
            'account_number' => 'string',
            'invoice_number' => 'string',
            'agent_reference_number' => 'string',
            'collection_date' => 'string',
            'delivery_country' => 'string',
            'mawb' => 'string',
            'awb' => 'string',
            'hawb' => 'string',
            'service_name' => 'string',
            'service_code' => 'string',
            'surcharge_service_code' => 'string',
            'weight' => 'number',
            'vol_weight' => 'number',
            'length' => 'number',
            'width' => 'number',
            'height' => 'number',
            'number_of_pieces' => 'number',
            'basic_charges' => 'number',
            'fuel_charges' => 'number',
            'additional_charges' => 'number',
            'vat' => 'number',
            'total_amount' => 'number',
            'notes' => 'string',
            'currency' => 'string',
            'message' => 'string',
            'message_code' => 'string',
            'status' => ['enum' => ['success', 'error']],
            'action' => ['enum' => ['approved', 'manually_approved','query_with_supplier','credit_note_received']],
            'created_at' => 'datetime',
            'bag_number' => 'number',
            'carrier_type' => 'undefined',
            'carrier_id' => 'undefined'
        ];
        parent::__construct("reconciliation_data", 'id', $fieldList, $mixedCreator);
    }

    public static function getReconciliationDataListFromSql($sql)
    {
        return DbAccess3::getListFromSql(__CLASS__, $sql);
    }

    public static function getTotalNumberOfReconciliationDataFromSql($sql)
    {
        $rs = DbAccess3::runQuery($sql);
        $data = mysqli_fetch_assoc($rs);
        return $data['total'];
    }

    public static function deleteReconciliationDataFromSql($sql)
    {
        self::runQuery($sql);
    }

    public static function deleteReconciliationDataByReconciliationDataId($reconciliationDataId)
    {
        if ($reconciliationDataId != "" && $reconciliationDataId > 0) {
            self::runQuery("DELETE FROM reconciliation_data WHERE id = '" . DbAccess3::escape($reconciliationDataId) . "'");
        }
    }

    public static function updateReconciliationDataFromSql($sql)
    {
        self::runQuery($sql);
    }

}

?>