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
class VehicleDriver extends DbAccess3
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
            'driver_end_time' => 'time',
            'driver_start_time' => 'time',
            'driver_id' => 'number',
            'is_active' => 'string',
            'vehicle_id' => 'number',
            'added_by' => 'number',
            'deleted_by' => 'number',
            'is_deleted' => 'number',
            'joining_date' => 'date',

            'first_name' => 'undefined',
            'last_name' => 'undefined',
        );

        parent::__construct("vehicle_driver", 'id', $fieldList, $mixedCreator);
    }

    public static function getDriversListFromSql($sql)
    {
        return DbAccess3::getListFromSql(__CLASS__, $sql);
    }


    public static function getTotalNumberOfDriversFromSql($sql)
    {
        $rs = self::runQuery($sql);
        $data = mysqli_fetch_assoc($rs);
        return $data['total'];
    }  
    
}
