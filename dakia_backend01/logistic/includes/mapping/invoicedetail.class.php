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
class InvoiceDetail extends DbAccess3 {

    public $vatPercent = 20;
    protected $consignment_id;
    protected $invoice_no;
    protected $hawb;
    protected $amount;
    protected $reference;
    protected $date_created;
    protected $added_by;
    private $filter;

    /**
     * Constructor. Returns the object.
     * @param int $id
     * @return obj
     */
    public function __construct($mixedCreator = null) {

        $fieldList = array(
            'id' => 'string',
            'consignment_id' => 'number',
            'invoice_id' => 'number',
            'invoice_no' => 'string',
            'charges_detail' => 'string',
            'vat' => 'number',
            'total' => 'number',
            'date_created' => 'datetime',
            'added_by' => 'string'
        );
        parent::__construct("invoice_detail", 'id', $fieldList, $mixedCreator);
    }
    public static function getListSql($sql) {
        return DbAccess3::getListFromSql(__CLASS__, $sql);
    }
    
    public static function deleteInvoiceDetailsFromSql($sql) {
        self::runQuery($sql);
    }

    public function setAmount($total = 0) {
        if($total <= 0) {
            $total  = ((float) $this->getLabelPrice() +
                (float) $this->getBasicCharges() +
                (float) $this->getFuelCharges() +
                (float) $this->getAdditionalCharges() +
                (float) $this->getRemoteAreaCharge() +
                (float) $this->getOnFarwordCharges() +
                (float) $this->getNdx() +
                (float) $this->getDdp() +
                (float) $this->getHv() +
                (float) $this->getExtra() +
                (float) $this->getAncillaryCharges() )
                -
                (
                    (float) $this->getDiscount() +
                    (float) $this->getLabelDiscount()  );
        }

        $this->valArray["amount"] = $total;
        $this->modifyArray["amount"] = $total;

    }

    public static function getTotalNumberOfInvoiceFromSql($sql) {
        $rs = self::runQuery($sql);
        $data = mysqli_fetch_assoc($rs);
        return $data['total'];
    }

}

?>
