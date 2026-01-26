<?php 
////////////////////////////////////////////////////
//
// Class for dealing with administration users
//
////////////////////////////////////////////////////

/**
 * Adminuser - Adminuser class
 * @package Admin
 */
class Currency extends DbAccess3 {

    protected $currencyname;
    protected $leftsymbol;
    protected $rightsymbol;
    protected $isdefault;
    protected $currencyexchangerate;
    protected $isactive;
    protected $ClientDisplay;

    /**
     * Constructor. Returns the object.
     * @param int $id
     * @return obj
     */
    public function __construct($mixedCreator = null) {

        $fieldList = array
            (
            'id' => 'number',
			'country_id' => 'number',
            'currencyid' => 'number',
            'currencyname' => 'string',
            'leftsymbol' => 'string',
            'leftsymbolcode' => 'string',
            'isdefault' => 'string',
            'rightsymbol' => 'string',
            'currencyexchangerate' => 'string',
            'isactive' => 'string',
            'clientdisplay' => 'string',
			'country_name' => 'undefined'
        );

        parent::__construct("currency", 'id', $fieldList, $mixedCreator);
    }

    public static function getCurrencyListFromSql($sql) {
        return DbAccess3::getListFromSql(__CLASS__, $sql);
    }
     public static function getTotalNumberOfCurrencyFromSql($sql)
        {
            $rs = DbAccess3::runQuery($sql);
            $data=mysqli_fetch_assoc($rs);
            return $data['total'];
	}

    public function setCurrencyid($currencyId) {
        $this->valArray["currencyId"] = $this->getId();
        $this->modifyArray["currencyId"] = $this->getId();
    }

    public static function convertCurrency($fromCurrency, $toCurrency, $valueToConvert, $conversionRate = "") {
        $currencyConverted = $valueToConvert;
        if ($fromCurrency != $toCurrency) {
            $sql = "SELECT currencyexchangerate, rightsymbol FROM currency WHERE rightsymbol in ('" . DbAccess3::escape($fromCurrency) . "','" . DbAccess3::escape($toCurrency) . "')";
            $result = DbAccess3::runQuery($sql);
            if (mysqli_num_rows($result) > 0) {
                while ($currencyRow = mysqli_fetch_row($result)) {
                    if ($fromCurrency == $currencyRow[1]) {
                        $fromCurrencyRate = number_format($currencyRow[0], 2);
                    } else {
                        $toCurrencyRate = number_format($currencyRow[0], 2);
                    }
                }
                if($conversionRate != "") {
                    $toCurrencyRate = number_format($conversionRate,2);
                }
                $currencyInGBP = $valueToConvert / $fromCurrencyRate;
                $currencyConverted = $currencyInGBP * $toCurrencyRate;
            } else {
                $currencyConverted = $valueToConvert;
            }
        } else {
            $currencyConverted = $valueToConvert;
        }
        return $currencyConverted;
    }

}

?>
