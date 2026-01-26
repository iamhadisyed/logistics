<?php 
// get settings
class CreditNote extends DbAccess3 {

    public function __construct($mixedCreator = null) {
        $this->tablename = 'credit_note';
        $this->pkey = 'id';
        $fieldList = array
            (
            'id' => 'number',
            'credit_note_number' => 'string',
            'user_account_id' => 'number',
            'invoice_type' => ['enum' => ['INV','MNI']],
            'invoice_number' => 'string',
            'credit_note_type' => ['enum' => ['PARTIAL','FULL','OTHER']],
            'hawb' => 'string',
            'credit_note_heading' => 'string',
            'credit_date' => 'datetime',
            'net_amount' => 'string',
            'vat_amount' => 'string',
            'credit_total' => 'string',
            'credit_note_by' => 'number',
            'pdf' => 'string',
            'is_email' => 'bit',
            'is_read' => 'bit',
            'added_by' => 'number',
            'date_created' => 'datetime',
            'updated_by' => 'number',
            'date_updated' => 'datetime',
            'currency_id' => 'number',            
            'user_name' => 'undefined',
            'user_account' => 'undefined'
        );
        //
        parent::__construct("credit_note", 'id', $fieldList, $mixedCreator);
    }

    /**
     * Get list of user objects, using sql given
     *
     * @param string $sql
     */
    public static function getCreditNoteListFromSql($sql) {
        return DbAccess3::getListFromSql(__CLASS__, $sql);
    }

    public static function getTotalNumberOfCreditNoteFromSql($sql) {
        $rs = DbAccess3::runQuery($sql);
        $data = mysqli_fetch_assoc($rs);
        return $data['total'];
    }
    
    public static function deleteCreditNoteFromSql($sql) {
        self::runQuery($sql);
    }
    
    public static function deleteCreditNoteDetailsByCreditNoteId($creditNoteId) {
        if($creditNoteId != "" && $creditNoteId > 0) {
             self::runQuery("DELETE FROM credit_note_details WHERE credit_note_id = '".DbAccess3::escape($creditNoteId)."'");
        }
    }

}

