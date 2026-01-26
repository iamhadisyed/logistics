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
class CreditNoteDetails extends DbAccess3 {

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

    /**
     * Constructor. Returns the object.
     * @param int $id
     * @return obj
     */
    public function __construct($mixedCreator = null) {

        $fieldList = array(
            'id' => 'string',
            'credit_note_id' => 'number',
            'hawb' => 'string',
            'date_booked' => 'datetime',
            'reference' => 'string',
            'invoice_amount' => 'string',
            'chargeable_amount' => 'string',
            'credit_amount' => 'string',
            'description' => 'string',
            'vat_amount' => 'string',
            'is_vatable' => ['enum' => ['YES','NO']],
            'added_by' => 'number',
            'date_created' => 'datetime',
            'updated_by' => 'number',
            'date_updated' => 'datetime'
        );

        parent::__construct("credit_note_details", 'id', $fieldList, $mixedCreator);
    }

    public static function getCreditNoteDetailsListFromSql($sql) {
        return DbAccess3::getListFromSql(__CLASS__, $sql);
    }

    public static function getTotalNumberOfCreditNoteDetailsFromSql($sql) {
        $rs = self::runQuery($sql);
        $data = mysqli_fetch_assoc($rs);
        return $data['total'];
    }

    public static function deleteCreditNoteDetailsFromSql($sql) {
        $rs = self::runQuery($sql);
    }

}

?>
