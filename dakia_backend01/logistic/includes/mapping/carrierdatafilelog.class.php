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
class CarrierDataFileLog extends DbAccess3
{
    public $vatPercent = 20;
    protected $Id;
    protected $CarrierId;
    protected $AgentId;
    protected $FileName;
    protected $DateCreated;

    private $filter;
	
    /**
     * Constructor. Returns the object.
     * @param int $id
     * @return obj
     */
    public function __construct($mixedCreator = null)
    {

        $fieldList = array( 
            'id'            => 'number',
            'carrier_id'    => 'number',
            'agent_id'      => 'number',
            'file_name'     => 'string',
            'date_created'  => 'datetime',
            'run_number'  => 'number'

            );

        parent::__construct("carrier_data_file_log", 'id', $fieldList, $mixedCreator);
    }
  
    public static function getCarrierDataFileLogListFromSql($sql)
    {
        return DbAccess3::getListFromSql(__CLASS__, $sql);
    }


    public static function getTotalNumberOfCarrierDataFileLogFromSql($sql)
    {
        $rs = self::runQuery($sql);
        $data=mysqli_fetch_assoc($rs);
        return $data['total'];
    }
    
    public static function deleteCarrierDataFileLogFromSql($sql) {
        self::runQuery($sql);
    }
    
    public static function generateRunNumber($carrierId,$agentId, $dateChecked = true) {


        $filter =   new CarrierDataFileLogFilter();
        $wehreArray =   ["carrier_id"=>$carrierId, "agent_id"=>$agentId];
        $filter->where($wehreArray);
        
        if($dateChecked)
        {   $wehreArrayGreated =   ["date_created"=>date("Y-m-d 00:00:00")];
            $filter->where($wehreArrayGreated, '>=');
        }
        $filter->orderBy("id", "DESC");
        $filter->setOffset(0);
        $filter->setRowsPerPage(1);
        $data   =   $filter->getList('*');
        if(count($data)>0)
        {
            foreach($data as $dataItems)
            {
                $runNumber  =   $dataItems->getRunNumber();
                break;
            }
        }
        else
            $runNumber = 500;
        $runNumber  =   $runNumber+1 ;
        
        
        return $runNumber ;
    }
	 
}
?>
