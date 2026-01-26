<?php 
/**
 * PaymentsHistory class
 * @package News Releases
 */
class PaymentsHistory extends DbAccess3 {

    /**
     * Constructor. Returns the object.
     * @param int $id
     * @return obj
     */
    public function __construct($mixedCreator = null) {
        $fieldList = array(
            'id' => 'number',
            'account_id' => 'number',
            'paypal_payment_id' => 'string',
            'txn_id' => 'string',
            'billing_id' => 'string',
            'sender_email' => 'string',
            'amount' => 'number',
            'amount_currency_id' => 'string',
            'payment_method' => ['enum' => ['cash','paypal','bank_transfer','cheque']],
            'payment_detail' => 'string',
            'user_currency_id' => 'string',
            'debit' => 'number',
            'credit' => 'number',
            'module_name' => 'string',
            'module_id' => 'string',
            'invoice_id' => 'number',
            'payment_status' => 'string',
            'is_completed' => ['enum' => ['yes','no']],
            'date_added' => 'datetime',
            'added_by' => 'number',
            'date_updated' => 'datetime',
            'updated_by' => 'number',
            'right_symbol' => 'undefined',
            'balance' => 'undefined'
        );
        parent::__construct("payments_history", 'id', $fieldList, $mixedCreator);
    }

    public static function getPaymentsHistoryListFromSql($sql) {
        return DbAccess3::getListFromSql(__CLASS__, $sql);
    }

    public static function getTotalNumberOfPaymentsHistoryFromSql($sql) {
        $rs = self::runQuery($sql);
        $data = mysqli_fetch_assoc($rs);
        return $data['total'];
    }

    public static function deletePaymentsHistoryFromSql($sql) {
        self::runQuery($sql);
    }
    
    public static function getAccountBalance($accountId) {
        $retunObj = "";
        if(!empty($accountId)){
            $accountIdIn = "";
            if(is_array($accountId)){
                $accountIdIn = " IN (" . implode(",", $accountId) . ")";
            }
            else{
                $accountIdIn = " IN ('".$accountId."')";
            }
            $sql = " 
                    SELECT 
                        p.`account_id`,
                          (SUM(p.`credit`) - SUM(p.`debit`)) AS credit
                        FROM
                          payments_history p 
                        WHERE p.`account_id` $accountIdIn
                          AND p.`is_completed` = 'yes' group by p.`account_id`
                     ";
            $retunObj = DbAccess3::getListFromSql(__CLASS__, $sql);
        }
        return $retunObj;
    }
    
    public static function getTransactionHistory($accountId,$where,$rowPerPage = 0, $offset = 0) {
        $limit = "";
        if($rowPerPage > 0){
            $limit =  " LIMIT $offset, $rowPerPage";
        }        
        $sql = "SELECT 
                    SQL_CALC_FOUND_ROWS
                    billing_id,
                    date_added,
                    payment_method,
                    payment_detail,
                    CONCAT(FORMAT(amount,2),' ',amount_currency) AS amount,
                    CONCAT(FORMAT(credit,2),' ',billing_currency) AS credit,
                    CONCAT(FORMAT(debit,2),' ',billing_currency) AS debit,
                    CONCAT(FORMAT(SUM(credit) over ( ORDER BY date_added) - SUM(debit) over ( ORDER BY date_added),2),' ',billing_currency) AS balance 
                    FROM  (
                      (SELECT 
                        '' AS billing_id,
                        `date_created` AS date_added,
                        '' AS payment_method,
                        'Credit Limit to this account' AS payment_detail,
                        `credit_limit` AS amount,
                        `credit_limit` AS credit,
                        '0' AS debit,
                        (CASE WHEN billing_currency = '' THEN 'GBP' ELSE billing_currency END) COLLATE utf8_general_ci AS amount_currency,
                        (CASE WHEN billing_currency = '' THEN 'GBP' ELSE billing_currency END) AS billing_currency
                      FROM
                        customer_account ua 
                      WHERE id = '".DbAccess3::escape($accountId)."' AND is_prepaid = 0) 
                      UNION
                      (SELECT 
                        ph.`billing_id`,
                        ph.`date_added`,
                        ph.`payment_method`,
                        ph.`payment_detail`,
                        ph.`amount` AS amount,
                        ph.`credit` AS credit,
                        ph.`debit` AS debit,
                        amount_c.rightsymbol  COLLATE utf8_general_ci AS amount_currency,
                        (CASE WHEN ua.billing_currency = '' THEN 'GBP' ELSE ua.billing_currency END) AS billing_currency
                      FROM
                        payments_history ph 
                        JOIN customer_account ua 
                          ON ph.account_id = ua.id 
                        JOIN currency amount_c 
                          ON amount_c.id = ph.`amount_currency_id` 
                      ".$where." 
                    ) ) AS tmp 
                ORDER BY date_added DESC".$limit;
//        echo $sql; die;
        return self::getPaymentsHistoryListFromSql($sql);
    }
    
}
