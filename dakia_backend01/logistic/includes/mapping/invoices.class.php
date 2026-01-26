<?php

////////////////////////////////////////////////////
//
// Class for dealing with Invoices
//
////////////////////////////////////////////////////

/**
 * Invoices class
 * @package News Releases
 */
class Invoices extends DbAccess3
{
    public $vatPercent = 20;
    protected $InvoiceNo;
    protected $AgentCode;
    protected $InvoiceDate;
    protected $ReceivedDate;
    protected $RecordDateFrom;	
    protected $RecordDateTo;
    protected $Country;
    protected $County;
    protected $TotalAmount;
    protected $Weight;
    protected $Currency;    
    protected $ExchangeRate;
    protected $IStatus;
    protected $DateCreated;
    protected $DateUpdated;
    protected $AgentType;
    protected $Added_by;
    protected $ServiceType;
    protected $UserId;
    protected $CourierId;
    protected $CustomerId;
    protected $InvoiceAmount;
    protected $Vat;
    protected $TotalInvAmount;
    protected $FileDataType;
    protected $InvoiceFile;
    protected $IsActive;  
    protected $IsDeleted;
    protected $DateDeleted;
    protected $Account;
    private $filter;
	
    /**
     * Constructor. Returns the object.
     * @param int $id
     * @return obj
     */
    public function __construct($mixedCreator = null)
    {

        $fieldList = array( 
            'id'                    => 'number',
            'invoice_no'            => 'string',
            'invoice_type'          => ['enum' => ['INV','MNI']],
            'user_account_id'       => 'number',
            'net_amount'            => 'number',
            'vatable_amount'        => 'number',
            'vat'                   => 'number',
            'fuel_charges'          => 'number',
            'total_amount'          => 'number',
            'weight'                => 'number',
            'currency_id'           => 'number',
            'exchange_rate'         => 'number',
            'invoice_date'          => 'datetime',
            'invoice_by'            => 'number',
            'summary_pdf'           => 'string',
            'pdf'                   => 'string',
            'csv'                   => 'string',
            'date_created'          => 'datetime',
            'date_updated'          => 'datetime',
            'date_deleted'          => 'datetime',
            'added_by'              => 'number',
            'is_active'             => 'number',
            'is_deleted'            => 'number',
            'credit_amount'         => 'number',
            'credit_type'           => ['enum' => ['PARTIAL','FULL']],
            'is_email'              => 'bit',
            'is_read'              => 'bit',
            'is_paid'               => 'bit',
            'is_cancel'             => 'bit',
            'paid_date'             => 'datetime',
            'salepot_id'            => 'number',
            'invoice_heading'       => 'string',
            'attached_files'       =>  'string',
            'invoice_reference'    =>  'string',
            'user_account'          => 'undefined',
            'user_name'             => 'undefined',
            'last_insert_id'        => 'undefined',  
            'total'                 => 'undefined',
            'no_shipment'                 => 'undefined',
            'sale_date'                 => 'undefined',
            'sales_pot_time_period'                 => 'undefined',

            'sales_pot_is_paid'                 => 'undefined',
            'company_pot_percentage'                 => 'undefined',
            'company_pot_value'                 => 'undefined',
            'sale_pot_percentage'                 => 'undefined',
            'sale_pot_value'                 => 'undefined',
            'sale_user_agent'                 => 'undefined',
            'billing_currency'                 => 'undefined',
            );

        parent::__construct("invoices", 'id', $fieldList, $mixedCreator);
    }
  
    public static function getInvoiceListFromSql($sql)
    {
        return DbAccess3::getListFromSql(__CLASS__, $sql);
    }


    public static function getTotalNumberOfInvoiceFromSql($sql)
    {
        $rs = self::runQuery($sql);
        $data=mysqli_fetch_assoc($rs);
        return $data['total'];
    }
    
    public static function deleteInvoiceFromSql($sql) {
        self::runQuery($sql);
    }
    
    public static function generateInvoiceNumber($accountId,$invoiceType) {
        $invoice_number = "";
        if($invoiceType == "INV") {
            $type = "AUTO";
        } else if($invoiceType == "MNI") {
            $type = "MANUAL";
        } else if($invoiceType == "CRD") {
            $type = "CREDIT";
        }
        
        $host = SETTING_DB_SERVER;
        $user = SETTING_DB_USER;
        $password = SETTING_DB_PASSWORD;
        $db = SETTING_DB_DATABASE;
        
        $mysqli = new mysqli($host, $user, $password, $db);
        if ($mysqli->connect_errno) {
            $message['status'] = 'error';
            $message['message'] = "ERROR||Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
        }
        $sql = "CALL invoiceNumber(" .$accountId. ", '" .$type. "',@rtn)";
//        echo $sql; die;
        if ($result = $mysqli->query($sql)) {
            $arrObj = $result->fetch_assoc();
            $invoice_number .= $arrObj['prefix'];
            $invoice_number .= $arrObj['@invoice_number'];
            $invoice_number .= $arrObj['sufix'];
        } else {
            $message['status'] = 'error';
            $message['message'] = "ERROR||CALL failed: (" . $mysqli->errno . ") " . $mysqli->error;
        }
        $mysqli->close();
        if(trim($invoice_number) != "") {
            $message['status'] = 'success';
            $message['invoice_number'] = $invoice_number;   
        } else {
            $message['status'] = 'error';
            $message['message'] = "ERROR||Error in getting invoice number";
        }
        return $message;
    }
        public static function getTotalUnPaidAmountByAccountId($accountId) {
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
                        SUM(i.`total_amount`) AS `total_amount`
                      FROM
                        `invoices` i 
                      WHERE i.`is_paid` = 0 
                        AND i.`user_account_id`  $accountIdIn
                     ";
            $retunObj = DbAccess3::getListFromSql(__CLASS__, $sql);
        }
        return $retunObj;
    }
}