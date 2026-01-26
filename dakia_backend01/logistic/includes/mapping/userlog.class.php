<?php
/**
 * ConsignmentLog class
 * - deals with Logs of everything that happens with consignments.
 * Last modified Alex 22/05/2014
 */ 
class UserLog extends DbAccess3
{
	 /**
     * Construct
     *
     * @param id/array
     */
     public function __construct($mixedCreator = null)
    {
        $fieldList = array(
                    'id' 			=> 'number',
					'message' 			=> 'string',
					'userid' 			=> 'number',
                    'logdate' 			=> 'datetime',
					'ipaddress' 		=> 'number',
					'userid' 			=> 'number',
					'log_id'			=> 'number',
					'log_type'			=> 'string',
					'previous_data'		=> 'string',
					'current_data'		=> 'string'
                    );
        parent::__construct("user_log", 'id', $fieldList, $mixedCreator);
    }
    
    public static function logArray(){
        $displayArray   =   array(
                'id' => 'id',                       
                'usertype' => 'user type',                
                'username' => 'user name',                
                'userpass' => 'user pass',                
                'activeflag' => 'active flag',              
                'firstname' => 'first name',               
                'lastname' => 'last name',                
                'address' => 'address',                  
                'email' => 'email',                    
                'phone' => 'phone',                    
                'countryid' => 'country id',               
                'apikey' => 'api key',                  
                'apisecert' => 'api secert',               
                'apidate' => 'api date',                 
                'profileimage' => 'profile image',           
                'isemployee' => 'is employee',              
                'warehouseid' => 'warehouse id',             
                'dashboard' => 'dashboard',                
                'invalidlogincount' => 'invalid login count',      
                'useraccountid' => 'user account id',          
                'lastlogindate' => 'last login date',          
                'addedby' => 'added by',                 
                'addeddate' => 'added date',               
                'updatedby' => 'updated by',             
                'updateddate' => 'updated date',            
                'isdeleted' => 'is deleted',              
                'archiveserver' => 'archive server',           
                'carriersetupagreement' => 'carrier setup agreement',  
                'receiveemail' => 'receive email',            
                'tcagreeddate' => 'tc agreed date',          
                'istcagreed' => 'is tc agreed',
                'dashboard' => 'dashboard'
                );
        return $displayArray;
    }
	
	  public static function getUserLogListFromSql($sql)
    {
        return DbAccess3::getListFromSql(__CLASS__, $sql);
    }
	
	public static function getUserLogCountFromSql($sql)
	{
		return DbAccess3::getSql(__CLASS__, $sql);
	}
	//set functions of each column
	 public function setLogId($val)
    {
        $this->valArray["log_id"]=$val;
    }
	//get userid id number
	public function setUserId($val='')
    {
		if($val == '')
		{
			$user = SessionManager::getUser();
			if($user->getId() > 0 )
        		$this->valArray["userid"]= $user->getId();
			else
				$this->valArray["userid"]= $val;	
		}
		else
		{
			$this->valArray["userid"]= $val;	
		}
    }
	//set the action they performed.
	 public function setMessage($val)
    {
        $this->valArray["message"]=$val;
    }
	//ip address
	 public function setIpAddress()
    {
        $this->valArray["ipaddress"]= ip2long($_SERVER['REMOTE_ADDR']);

    }
	//set datetime
	 public function setDateTime()
    {
        $this->valArray["logdate"]=date('Y-m-d H:i:s');
    }
	//creates a log message @param $message,$consignmentid
	public function createlog($message,$logid, $logType, $user_id='',$oldData='', $newData='')
	{
		$this->setLogId($logid);
		$this->setLogType($logType);
		if($user_id == '')
		{
			$this->setUserId();
		}
		else
		{
			$this->setUserId($user_id);
		}
		$this->SetMessage($message);
		//$this->Setuserid();
		$this->SetDateTime();
		$this->SetIpAddress();
		$this->setPreviousData($oldData);
		$this->setCurrentData($newData);
		
		$this->Save();	
	}
	
}