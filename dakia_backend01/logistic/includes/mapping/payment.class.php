<?php

////////////////////////////////////////////////////
//
// Class for dealing with products
//
////////////////////////////////////////////////////

/**
 * Rack - rack class
 * @package Ecommerce
 */
class Payment extends DbAccess3 {
    /**
     * Constructor. Returns the object.
     * @param int $id
     * @return obj
     */
    public function __construct($mixedCreator = null) {
        $fieldList = array('amount' => 'number',
            'amount_currency_id' => 'number',
            'dr' => 'number',
            'cr' => 'number',
            'currency_id' => 'number',
            'paymentmethod_id' => 'number',
            'paymentdate' => 'date',
            'iscompleted' => 'number',
            'customer_id' => 'number',
            'Paymentdetail' => 'string',
            'transaction_id' => 'string',
            'added_by' => 'number',
            'added_date' => 'date',
            'updated_by' => 'number',
            'updated_date' => 'date'
        );
        parent::__construct('payment', 'id', $fieldList, $mixedCreator);
    }    
    // specific getters
    
    ///setter
    
    public function setAmount($amount) {
        $this->valArray["amount"] = $amount;        
        $this->modifyArray["amount"] = $amount;
    }

    public function setAmountCurrencyId($amount_currency_id) {
        $this->valArray["amount_currency_id"] = $amount_currency_id;
        $this->modifyArray["amount_currency_id"] = $amount_currency_id;
    }
    public function setDr($dr) {
        $this->valArray["dr"] = $dr;
        $this->modifyArray["dr"] = $dr;        
    }

    public function setCr($cr) {
        $this->valArray["cr"] = $cr;
        $this->modifyArray["cr"] = $cr;
    }
    
    public function setCurrencyId($currency_id) {
        $this->valArray["currency_id"] = $currency_id;
        $this->modifyArray["currency_id"] = $currency_id;
    }

    public function setPaymentmethodId($paymentmethod_id) {
        return $this->valArray["paymentmethod_id"] = $paymentmethod_id;
        return $this->modifyArray["paymentmethod_id"] = $paymentmethod_id;
    }

    public function setPaymentDate($paymentdate) {
        $this->valArray["paymentdate"] = $paymentdate;
        $this->modifyArray["paymentdate"] = $paymentdate;
    }

    public function setIsCompleted($iscompleted) {
        $this->valArray["iscompleted"] = $iscompleted;
        $this->modifyArray["iscompleted"] = $iscompleted;
    }

    public function setPaymentDetail($Paymentdetail) {
        $this->valArray["Paymentdetail"] = $Paymentdetail;
        $this->modifyArray["Paymentdetail"] = $Paymentdetail;
    }
    public function setTransactionId($transaction_id) {
        $this->valArray["transaction_id"] = $transaction_id;
        $this->modifyArray["transaction_id"] = $transaction_id;
    }

    public function setAddedBy($added_by) {
        $this->valArray["added_by"] = $added_by;
        $this->modifyArray["added_by"] = $added_by;
    }

    public function setAddedDate($added_date) {
        $this->valArray["added_date"] = $added_date;
        $this->modifyArray["added_date"] = $added_date;
    }
    
    public function setUpdatedBy($updated_by) {
        $this->valArray["updated_by"] = $updated_by;
        $this->modifyArray["updated_by"] = $updated_by;
    }

    public function setUpdatedDate($updated_date) {
        $this->valArray["updated_date"] = $updated_date;
        $this->modifyArray["updated_date"] = $updated_date;
    }
    
    // getter
    public function getId() {
        return $this->valArray["id"];
    }    
    public function getAmount() {
        return $this->valArray["amount"];
    }

    public function getAmountCurrencyId() {
        return $this->valArray["amount_currency_id"];
    }
    
    public function getDr() {
        return $this->valArray["dr"];        
    }

    public function getCr() {
        return $this->valArray["cr"];
    }
    
    public function getCurrencyId() {
        return $this->valArray["currency_id"];
    }

    public function getPaymentmethodId() {
        return $this->valArray["paymentmethod_id"];
    }

    public function getPaymentDate() {
        return $this->valArray["paymentdate"];
    }

    public function getIsCompleted() {
        return $this->valArray["iscompleted"];
    }

    public function getPaymentDetail() {
        return $this->valArray["Paymentdetail"];
    }
    public function getTransactionId() {
        return $this->valArray["transaction_id"];
    }

    public function getAddedBy() {
        return $this->valArray["added_by"];
    }

    public function getAddedDate() {
        return $this->valArray["added_date"];
    }
    
    public function getUpdatedBy() {
        return $this->valArray["updated_by"];
    }

    public function getUpdatedDate() {
        return $this->valArray["updated_date"];
    }
    
    /**
     * Get list of warehouse objects, using sql given     
     * @param string $sql
     */
    public static function getPaymentListFromSql($sql) {        
        return DbAccess3::getListFromSql(__CLASS__, $sql);
    }
   	public static function getTotalNumberOfPaymentsFromSql($sql)
   {
			$rs = DbAccess3::runQuery($sql);
			$data=mysqli_fetch_assoc($rs);
			return $data['total'];
	}
}
?>