<?php
/**
 * QuotationPaymentsHistory class
 * @package News Releases
 */
class QuotationPaymentsHistory extends DbAccess3
{

    /**
     * Constructor. Returns the object.
     * @param int $id
     * @return obj
     */
    public function __construct($mixedCreator = null)
    {
        $fieldList = array(
            'id' => 'number',
            'quotation_id' => 'number',
            'consignment_id' => 'number',
            'paypal_payment_id' => 'string',
            'txn_id' => 'string',
            'billing_id' => 'string',
            'sender_email' => 'string',
            'amount' => 'number',
            'amount_currency_id' => 'number',
            'payment_method' => ['enum' => ['paypal', 'barclays']],
            'payment_detail' => 'string',
            'payment_status' => 'string',
            'is_completed' => ['enum' => ['yes', 'no']],
            'quote_data' => 'string',
            'date_added' => 'datetime',
            'date_updated' => 'datetime',
        );
        parent::__construct("quotation_payments_history", 'id', $fieldList, $mixedCreator);
    }

    public static function getQuotationPaymentsHistoryListFromSql($sql)
    {
        return DbAccess3::getListFromSql(__CLASS__, $sql);
    }

    public static function getTotalNumberOfQuotationPaymentsHistoryFromSql($sql)
    {
        $rs = self::runQuery($sql);
        $data = mysqli_fetch_assoc($rs);
        return $data['total'];
    }

    public static function deleteQuotationPaymentsHistoryFromSql($sql)
    {
        self::runQuery($sql);
    }

}
