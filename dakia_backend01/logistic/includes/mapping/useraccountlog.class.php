<?php

class UserAccountLog extends DbAccess3 {

    /**
     * Construct
     *
     * @param id/array
     */
    public function __construct($mixedCreator = null) {

        $fieldList = array(
            'id' => 'number',
            'message' => 'string',
            'userid' => 'number',
            'logdate' => 'datetime',
            'ipaddress' => 'number',
            'userid' => 'number',
            'log_id' => 'number',
            'log_type' => 'string',
            'previous_data' => 'string',
            'current_data' => 'string'
        );
        
        
    
    
        //
        parent::__construct("user_account_log", 'id', $fieldList, $mixedCreator);
    }

    
    public static function logArray(){
        $displayArray = array
        (
            'id' => 'Id',
            'useraccount' => 'User Account',
            'company' => 'Company',
            'fullname' => 'Full Name',
            'activeflag' => 'Active Flag',
            'returnaddress' => 'Return Address',
            'email' => 'Email',
            'smsdpd' => 'Sms Dpd',
            'userservicetype' => 'User Service Type',
            'parentid' => 'Parentid',
            'phone' => 'Phone',
            'logo' => 'Logo',
            'instantlabel' => 'Instant Label',
            'country' => 'Country',
            'trackingapiaccess' => 'Tracking Api Access',
            'importdatacsv' => 'Import Data Csv',
            'proforma' => 'Proforma',
            'addtracking' => 'Add Tracking',
            'collection' => 'Collection',
            'defaultdescription' => 'Default Description',
            'defaultnotes' => 'Default Notes',
            'defaultweight' => 'Default Weight',
            'paymentterm' => 'Payment Term',
            'queryterm' => 'Query Term',
            'vatnumber' => 'Vat Number',
            'billingcurrency' => 'Billing Currency',
            'vatchargable' => 'Vat Chargable',
            'vatvalue' => 'Vat Value',
            'allowremotearea' => 'Allow Remote Area',
            'telephone' => 'Telephone',
            'alternativeemail' => 'Alternative Email',
            'billingaddress' => 'Billing Address',
            'datedispatch' => 'Date Dispatch',
            'isproduct' => 'Is Product',
            'sendcourierdata' => 'Send Courier Data',
            'archiveserver' => 'Archive Server',
            'creditcheck' => 'Credit Check',
            'tariffagreed' => 'Tariff Agreed',
            'salesperson' => 'Sales Person',
            'scandocument' => 'Scan Document',
            'dataentry' => 'Data Entry',
            'bankaccounttitle' => 'Bank Account Title',
            'banksortcode' => 'Bank Sortcode',
            'bankaccountnumber' => 'Bank Account Number',
            'bankbranchaddress' => 'Bank Branch Address',
            'tradenamei' => 'Trade Name I',
            'tradeaddressi' => 'Trade Address I',
            'tradenameii' => 'Trade Name Ii',
            'tradeaddressii' => 'Trade Address Ii',
            'tradeemaili' => 'Trade Email I',
            'tradeemailii' => 'Trade Email Ii',
            'tradephonei' => 'Trade Phone I',
            'tradephoneii' => 'Trade Phone Ii',
            'regnumber' => 'Reg Number',
            'regaddress' => 'Reg Address',
            'regpostcode' => 'Reg Postcode',
            'regcountry' => 'Reg Country',
            'saleagent' => 'Sale Agent',
            'saledate' => 'Sale Date',
            'fuelcharges' => 'Fuel Charges',
            'labelprice' => 'Label Price',
            'discount' => 'Discount',
            'warehouseid' => 'Warehouse Id',
            'usersignature' => 'User Signature',
            'billingemail' => 'Billing Email',
            'isfuelchargesinclude' => 'Is Fuelcharges Include',
            'isprepaid' => 'Is Prepaid',
            'returnlabel' => 'Return Label',
            'finalmileoverlabel' => 'Finalmile Over Label',
            'requestmanifestcollection' => 'Request Manifest Collection',
            'createprealert' => 'Create Pre Alert',
            'isemployee' => 'Is Employee',
            'invoicebankdetailsid' => 'Invoice Bank Details Id',
            'checklistaccountform' => 'Check List Account Form',
            'checklistcreditcheck' => 'Check List Credit Check',
            'checklisttcs' => 'Check List T Cs',
            'checklisttariffagreed' => 'Check List Tariff Agreed',
            'checklistsalespot' => 'Check List Sales Pot',
            'salespottimeperiod' => 'Sales Pot Time Period',
            'salespotpercentage' => 'Sales Pot Percentage',
            'lastlogindate' => 'Last Login Date',
            'invalidlogincount' => 'Invalid Login Count',
            'token' => 'Token',
            'tokenupdated' => 'Token Updated',
            'opearationmanifest' => 'Opearation Manifest',
            'owntariff' => 'Own Tariff',
            'userwarehouse' => 'User Warehouse',
            'apikey' => 'Api Key',
            'apisecert' => 'Api Secert',
            'apidate' => 'Api Date',
            'salesrate' => 'Sales Rate',
            'collectionaddline1' => 'Collection Add Line 1',
            'collectionaddline2' => 'Collection Add Line 2',
            'collectionaddline3' => 'Collection Add Line 3',
            'collectioncity' => 'Collection City',
            'collectioncountry' => 'Collection Country',
            'collectionpostcode' => 'Collection Postcode',
            'themeid' => 'Theme Id',
            'bagging' => 'Bagging',
            'showprice' => 'Show Price',
            'retailcustomer' => 'Retail Customer',
            'defaultlang' => 'Default Lang',
            'usercode' => 'User Code',
            'websitelink' => 'Website Link',
            'creditlimit' => 'Credit Limit',
            'invoiceperiod' => 'Invoice Period',
            'countryid' => 'Country Id',
            'accountcode' => 'Account Code',
            'paypalemail' => 'Paypal Email',
            'paypalcurrency' => 'Paypal Currency',
            'datecreated' => 'Date Created',
            'tariffvalues' => 'Tariff Values'
        );

        return $displayArray;
    }
    /**
     * Get list of Permissions Log objects, using sql given
     *
     * @param string $sql
     */
    public static function getUserAccountLogListFromSql($sql) {

        return DbAccess3::getListFromSql(__CLASS__, $sql);
    }

    /**
     * Get count of Permissions Log objects, using sql given
     *
     * @param string $sql
     */
    public static function getUserAccountLogCountFromSql($sql) {
        return DbAccess3::getSql(__CLASS__, $sql);
    }
    
     public function setUserid($val)
    {
        $this->valArray["userid"]=$val;
    }
     public function setIpaddress($val)
    {
        $this->valArray["ipaddress"]= ip2long($_SERVER['REMOTE_ADDR']);
    }
     public function setLogdate($val)
    {
        $this->valArray["logdate"]=$val;
    }
     public function setLogId($val)
    {
        $this->valArray["log_id"]=$val;
    }
     public function setLogType($val)
    {
        $this->valArray["log_type"]=$val;
    }
     public function setMessage($val)
    {
        $this->valArray["message"]=$val;
    }
     public function setPreviousData($val)
    {
        $this->valArray["previous_data"]=$val;
    }
     public function setCurrentData($val)
    {
        $this->valArray["current_data"]=$val;
    }
    
    public function createlog($user_id = '', $ipaddress, $recordId, $log_type, $message, $previous_data, $current_data) {
        if ($user_id == '') {
            $this->setUserId();
        } else {
            $this->setUserId($user_id);
        }
        $ipaddress = str_replace('.', '', $ipaddress);
        $this->SetLogdate(date('Y-m-d H:i:s'));
        $this->SetIpaddress($ipaddress);
        $this->SetLogId($recordId);
        $this->setLogType($log_type);
        $this->SetMessage($message);
        $this->setPreviousData($previous_data);
        $this->setCurrentData($current_data);
        $this->save();
    }

}

// class