<?php

// get settings
//require_once("includes/settings/common.inc.php");

class InvoiceBankDetails extends DbAccess3 {

    public function __construct($mixedCreator = null) {
        $this->tablename = 'carrier_hubs';
        $this->pkey = 'id';
        $fieldList = array
            (
            'id' => 'number',
            'user_account_id' => 'number',
            'account_title' => 'string',
            'account_sortcode' => 'string',
            'account_number' => 'string',
            'account_iban' => 'string',
            'account_swift_code' => 'string',
            'bank_name' => 'string',
            'bank_branch' => 'string',
            'bank_address' => 'string',
            'date_added' => 'string',
            'currency' => 'string',
            'added_by' => 'string',
            'status' => 'number',
            'user_account'=>'undefined',
            'user_name'=>'undefined'
        );
        //
        parent::__construct("invoice_bank_details", 'id', $fieldList, $mixedCreator);
    }

    public static function getInvoiceBankDetailsListFromSql($sql) {
        return DbAccess3::getListFromSql(__CLASS__, $sql);
    }
    public static function getTotalNumberOfInvoiceBankDetailsFromSql($sql)
   {
			$rs = DbAccess3::runQuery($sql);
			$data=mysqli_fetch_assoc($rs);
			return $data['total'];
	}

}

// class
