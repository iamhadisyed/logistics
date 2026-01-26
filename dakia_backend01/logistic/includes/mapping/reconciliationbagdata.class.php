<?php
class ReconciliationBagData extends DbAccess3
{
    public function __construct($mixedCreator = null)
    {
        $fieldList = array(
            'id' => 'number',
            'batch_number' => 'string',
            'bag_number' => 'number',
            'invoice_number' => 'string',
            'country' => 'string',
            'country_code' => 'string',
            'weight' => 'number',
            'number_of_piece' => 'number',
            'matched_piece' => 'number',
            'matched_weight' => 'number',
            'service' => 'string',
            'number_of_parcel_status' => ['enum' => ['less','more','match'],'default' => 'less'],
            'poster' => 'string',
            'poster_date' => 'string',
            'net_value' => 'number',
            'vat_code' => ['enum' => ['T','Z'],'default' => 'T'],
            'invoice_type' => 'string',
            'account_number' => 'string',
            'invoice_date' => 'string',
            'service_type' => 'string',
            'weight_status' => ['enum' => ['less','more','match'],'default' => 'less'],
            'parcel_status' => ['enum' => ['success','error'],'default' => 'error'],
            'action' => ['enum' => ['approved','manually_approved','query_with_supplier','credit_note_received']],
            'status' => ['enum' => ['success','error'],'default' => 'error'],
            'message' => 'string'
        );
        parent::__construct("reconciliation_bag_data", 'id', $fieldList, $mixedCreator);
    }

    public static function getReconciliationBagDataListFromSql($sql)
    {
        return DbAccess3::getListFromSql(__CLASS__, $sql);
    }

    public static function getTotalNumberOfReconciliationBagDataFromSql($sql)
    {
        $rs = DbAccess3::runQuery($sql);
        $data = mysqli_fetch_assoc($rs);
        return $data['total'];
    }

    public static function deleteReconciliationBagDataFromSql($sql)
    {
        self::runQuery($sql);
    }

    public static function deleteReconciliationBagDataByReconciliationBagDataId($reconciliationDataId)
    {
        if ($reconciliationDataId != "" && $reconciliationDataId > 0) {
            self::runQuery("DELETE FROM reconciliation_bag_data WHERE id = '" . DbAccess3::escape($reconciliationDataId) . "'");
        }
    }

    public static function updateReconciliationBagDataFromSql($sql)
    {
        self::runQuery($sql);
    }

}
