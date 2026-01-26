<?php

class GroupsLog extends DbAccess3 {

    /**
     * Construct
     *
     * @param id/array
     */
    public function __construct($mixedCreator = null) {

        $fieldList = array(
            'id' => 'number',
            'message' => 'string',
            'userid' => 'number',
            'logdate' => 'datetime',
            'ipaddress' => 'number',
            'userid' => 'number',
            'log_id' => 'number',
            'log_type' => 'string',
            'previous_data' => 'string',
            'current_data' => 'string'
        );

        //
        parent::__construct("groups_log", 'id', $fieldList, $mixedCreator);
    }

    
    public static function logArray(){
        $displayArray   =   array(
            'groupid'                => 'Group Id',
            'groupname'           => 'Group Name',
            'groupdesc'          => 'Group Description',
            'isactive'          => 'Is Active',
            'addedby'       => 'Added By',
            'addeddate'           => 'Added Date',
            'isdeleted'         => 'Is Deleted'
        );
        return $displayArray;
    }
    /**
     * Get list of Groups Log objects, using sql given
     *
     * @param string $sql
     */
    public static function getGroupsLogListFromSql($sql) {

        return DbAccess3::getListFromSql(__CLASS__, $sql);
    }

    /**
     * Get count of Groups Log objects, using sql given
     *
     * @param string $sql
     */
    public static function getGroupsLogCountFromSql($sql) {
        return DbAccess3::getSql(__CLASS__, $sql);
    }
    
     public function setUserid($val)
    {
        $this->valArray["userid"]=$val;
    }
     public function setIpaddress($val)
    {
        $this->valArray["ipaddress"]= ip2long($_SERVER['REMOTE_ADDR']);
    }
     public function setLogdate($val)
    {
        $this->valArray["logdate"]=$val;
    }
     public function setLogId($val)
    {
        $this->valArray["log_id"]=$val;
    }
     public function setLogType($val)
    {
        $this->valArray["log_type"]=$val;
    }
     public function setMessage($val)
    {
        $this->valArray["message"]=$val;
    }
     public function setPreviousData($val)
    {
        $this->valArray["previous_data"]=$val;
    }
     public function setCurrentData($val)
    {
        $this->valArray["current_data"]=$val;
    }
    
    public function createlog($user_id = '', $ipaddress, $Groupsid, $log_type, $message, $previous_data, $current_data) {
        if ($user_id == '') {
            $this->setUserId();
        } else {
            $this->setUserId($user_id);
        }
        $ipaddress = str_replace('.', '', $ipaddress);
        $this->SetLogdate(date('Y-m-d H:i:s'));
        $this->SetIpaddress($ipaddress);
        $this->SetLogId($Groupsid);
        $this->setLogType($log_type);
        $this->SetMessage($message);
        $this->setPreviousData($previous_data);
        $this->setCurrentData($current_data);        
        $this->Save();
    }

}

// class