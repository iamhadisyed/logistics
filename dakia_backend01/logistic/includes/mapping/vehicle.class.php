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
class Vehicle extends DbAccess3
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
            'id' => 'number',
            'vehicle_type' => 'string',
            'vehicle_make' => 'string',
            'model_year' => 'number',
            'vehicle_model' => 'string',
            'registration_number' => 'string',
            'vehicle_capacity' => 'string',
            'vehicle_color' => 'string',
            'updated_by' => 'number',
            'added_by' => 'number',
            'vehicle_number' => 'string',

            'first_name' => 'undefined',
            'last_name' => 'undefined',
        );

        parent::__construct("vehicle", 'id', $fieldList, $mixedCreator);
    }

    public static function getVehicleFromSql($sql)
    {
        return DbAccess3::getListFromSql(__CLASS__, $sql);
    }


    public static function getTotalNumberOfVehiclesFromSql($sql)
    {
        $rs = self::runQuery($sql);
        $data = mysqli_fetch_assoc($rs);
        return $data['total'];
    }
}
