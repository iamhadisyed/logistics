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
class InvoicesManualDetails extends DbAccess3 {

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
    public function __construct($mixedCreator = null) {

        $fieldList = array(
            'id' => 'number',
            'invoice_id' => 'string',
            'hawb' => 'string',
            'service_id' => 'number',
            'date_booked' => 'datetime',
            'reference' => 'string',
            'weight' => 'string',
            'amount' => 'decimal',
            'destination' => 'string',
            'description' => 'string',
            'vat_amount' => 'decimal',
            'is_vat' => ['enum' => ['YES', 'NO']],
            'created_by' => 'number',
            'date_created' => 'datetime',
            'updated_by' => 'number',
            'date_updated' => 'datetime',
        );

        parent::__construct("invoices_manual_details", 'id', $fieldList, $mixedCreator);
    }

    public static function getManualInvoiceDetailsListFromSql($sql) {
        return DbAccess3::getListFromSql(__CLASS__, $sql);
    }

    public static function getTotalNumberOfManualInvoiceDetailsFromSql($sql) {
        $rs = self::runQuery($sql);
        $data = mysqli_fetch_assoc($rs);
        return $data['total'];
    }

    public static function deleteManualInvoiceDetailsDetailsFromSql($sql) {
        self::runQuery($sql);
    }
    
    public static function deleteManualInvoiceDetailsDetailsByInvoiceId($invoiceId) {
        if($invoiceId != "" && $invoiceId > 0) {
             self::runQuery("DELETE FROM invoices_manual_details WHERE invoice_id = '".DbAccess3::escape($invoiceId)."'");
        }
    }

}

?>
